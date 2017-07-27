<?php

namespace Diatem\RestServer;

use \Jacwright\RestServer\RestServer AS JRestServer;
use \Jacwright\RestServer\RestException AS JRestException;
use \Diatem\RestServer\RestConfig;
use \Diatem\RestServer\RestLog;

/**
 * Classe permettant d'initialiser un serveur REST
 */
class RestServer extends JRestServer{
	/**
	 * Variables "brutes" de la requête
	 *
	 * @var array
	 */
	public static $request = array();

	/**
	 * Analyse les arguments bruts de la requête
	 *
	 * @return void
	 */
	private function setRequestArgs(){
		if($this->method == 'PUT' || $this->method == 'PATCH'){
			parse_str(file_get_contents("php://input"),$post_vars);
			foreach($post_vars AS $key => $value){
				self::$request[$key] = $value;
			}
		}else{
			self::$request = $_REQUEST;
		}
	}

	/**
	 * Analyse des erreurs relevées par le gestionnaire d'erreurs
	 *
	 * @param string $error_level Niveau d'erreur
	 * @param string $error_message Message
	 * @param string $error_file Fichier
	 * @param integer $error_line Ligne
	 * @param string $error_context Contexte
	 * @return void
	 */
	function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context){
		$error = 'lvl:' . $error_level . ' | msg:' . $error_message . ' | file:' . $error_file . ' | ln:' . $error_line.' | context:'.$error_context;
		$this->handleError(500, $error);
	}

	/**
	 * Analyse des erreurs fatales relevées par le gestionnaire d'erreurs
	 *
	 * @return void
	 */
	function shutdownHandler(){
		
		$lasterror = error_get_last();
		switch ($lasterror['type'])
		{
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_PARSE:
				$error = "[SHUTDOWN] lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
				$this->handleError(500, $error);
		}
	}

	//--------------------------------------------------------------------------------------
	//Methodes surchargées

	/**
	 * Méthode surchargée pour gestion de l'erreur 405
	 *
	 * @return void
	 */
	protected function findUrl() {
		//Adjonction pour gestion de l'erreur 405
		if (!isset($this->map[$this->method])){
			$this->handleError(405, 'Method not allowed');
			exit;
		} 

		return parent::findUrl();
	}

	/**
	 * Méthode surchargée pour gestion ajout des gestionnaires d'erreur, et catch des RestException avec codes internes
	 *
	 * @return void
	 */
	public function handle()
	{
		error_reporting(0);
		set_error_handler(array($this, 'errorHandler'));
		register_shutdown_function(array($this, 'shutdownHandler'));

		$this->url = $this->getPath();
		$this->method = $this->getMethod();
		$this->format = $this->getFormat();

		if ($this->method == 'PUT' || $this->method == 'POST' || $this->method == 'PATCH') {
			$this->data = $this->getData();
		}

		$this->setRequestArgs();

		//preflight requests response 
		if($this->method == 'OPTIONS' && getallheaders()->Access-Control-Request-Headers){
			$this->sendData($this->options());
		}

		list($obj, $method, $params, $this->params, $noAuth) = $this->findUrl();


		if ($obj) {
			if (is_string($obj) && !($newObj = $this->instantiateClass($obj))) {
				throw new Exception("Class $obj does not exist");
			}

			$obj = $newObj;
			$obj->server = $this;


			try {
				$this->initClass($obj);

				if (!$noAuth && !$this->isAuthorizedByClass($obj)) {
					$this->sendData($this->unauthorized(true)); //@todo unauthorized returns void
					exit;
				}

				$result = call_user_func_array(array($obj, $method), $params);

				if ($result !== null) {
					$this->sendData($result);
				}
			} catch (JRestException $e) {
				$this->handleError($e->getCode(), $e->getMessage());
			} catch(RestException $e){
                $this->handleError($e->getCode(), $e->getMessage(), $e->getInternalCode(), $obj);
            }

		} else {
			$this->handleError(404);
		}
	}

	/**
	 * Methode surchargée pour gestion d'un retour spécifique en cas d'erreur
	 *
	 * @param string $statusCode Code HTTP
	 * @param string $errorMessage Message
	 * @param string $internalCode Code interne
	 * @param Object $obj Service
	 * @return void
	 */
   public function handleError($statusCode, $errorMessage = null, $internalCode = null, $obj = null)
	{
		
		$method = "handle$statusCode";
		foreach ($this->errorClasses as $class) {
			if (is_object($class)) {
				$reflection = new ReflectionObject($class);
			} elseif (class_exists($class)) {
				$reflection = new ReflectionClass($class);
			}

			if (isset($reflection))
			{
				if ($reflection->hasMethod($method))
				{
					$obj = is_string($class) ? new $class() : $class;
					$obj->$method();
					return;
				}
			}
		}

		if (!$errorMessage)
		{
			$errorMessage = $this->codes[$statusCode];
		}

		$this->setStatus($statusCode);

		$array = RestConfig::getRestResponseTemplate()->getTemplate();
        if(isset(self::$request['debug']) && self::$request['debug'] && $obj){
            $obj->perfAnalyser->addPoint();
            $array['executionTime'] =  $obj->perfAnalyser->getTotalTimeInMS();

            $array['logs'] = RestLog::getLogs();
        }
		$array['errorCode'] = $internalCode;
		$array['errorMessage'] = $errorMessage;
		$array['httpStatus'] = $statusCode;

		$this->sendData($array);
		exit;
	}
	

	//--------------------------------------------------------------------------------------
	//Attribuys surchargés

	private $codes = array(
		'100' => 'Continue',
		'200' => 'OK',
		'201' => 'Created',
		'202' => 'Accepted',
		'203' => 'Non-Authoritative Information',
		'204' => 'No Content',
		'205' => 'Reset Content',
		'206' => 'Partial Content',
		'300' => 'Multiple Choices',
		'301' => 'Moved Permanently',
		'302' => 'Found',
		'303' => 'See Other',
		'304' => 'Not Modified',
		'305' => 'Use Proxy',
		'307' => 'Temporary Redirect',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'402' => 'Payment Required',
		'403' => 'Forbidden',
		'404' => 'Not Found',
		'405' => 'Method Not Allowed',
		'406' => 'Not Acceptable',
		'409' => 'Conflict',
		'410' => 'Gone',
		'411' => 'Length Required',
		'412' => 'Precondition Failed',
		'413' => 'Request Entity Too Large',
		'414' => 'Request-URI Too Long',
		'415' => 'Unsupported Media Type',
		'416' => 'Requested Range Not Satisfiable',
		'417' => 'Expectation Failed',
		'500' => 'Internal Server Error',
		'501' => 'Not Implemented',
		'503' => 'Service Unavailable'
	);

	

	

	

    
   
}