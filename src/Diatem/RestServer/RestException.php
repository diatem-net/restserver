<?php

namespace Diatem\RestServer;

use Exception;

/**
 * Classe utilisée pour retourner une exception dans un service
 */
class RestException extends Exception{
    /**
     * Code d'erreur interne
     *
     * @var string
     */
    private $internalCode;

    private $stackTrace;

    /**
     * Constructeur
     *
     * @param integer $httpCode Code HTTP à retourner
     * @param string $message Message de l'erreur
     * @param string $internalCode Code interne de l'erreur
     */
    public function __construct($httpCode = 500, $message = null, $internalCode = null){
        $this->internalCode = $internalCode;

        ob_start(); 
        debug_print_backtrace(); 
        $this->stackTrace = ob_get_contents(); 
        ob_end_clean(); 

        parent::__construct($message, $httpCode);
    }

    /**
     * Retourne le code d'erreur interne
     *
     * @return string
     */
    public function getInternalCode(){
        return $this->internalCode;
    }

    public function getStackTrace(){
        return $this->stackTrace;
    }
}