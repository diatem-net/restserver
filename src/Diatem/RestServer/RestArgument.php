<?php

namespace Diatem\RestServer;

use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestServer;
use Jin2\Utils\StringTools;
use Jin2\Utils\ListTools;
use Jin2\DataFormat\Json;

/**
 * Décrit un argument d'une méthode
 */
class RestArgument
{

    //Définition de l'argument

    /**
     * Nom de l'argument
     *
     * @var string
     */
    private $name;

    /**
     * Type d'argument
     *
     * @var string
     */
    private $type;

    /**
     * Argument obligatoire ou non
     *
     * @var boolean
     */
    private $required;

    /**
     * Valeur par défaut
     *
     * @var mixed
     */
    private $defaultValue;

    /**
     * Taille maximale
     *
     * @var mixed
     */
    private $maxSize;

    //Constantes typage arguments
    /**
     * Constante définissant un type STRING
     */
    const TYPE_STRING = 'string';

    /**
     * Constante définissant un type BOOLEAN
     */
    const TYPE_BOOL = 'bool';

    /**
     * Constante définissant un type NUMERIC
     */
    const TYPE_NUMERIC = 'numeric';

    /**
     * Constante définissant un type DATE
     */
    const TYPE_DATE = 'date';

    /**
     * Constante définissant un type DATETIME
     */
    const TYPE_DATETIME = 'datetime';

    /**
     * Constante définissant un type ARRAY
     */
    const TYPE_ARRAY = 'array';

    /**
     * Constante définissant un type FILE (json comprenant deux attributs : fileName (nom du fichier) et fileContent (contenu base64 du fichier))
     */
    const TYPE_FILE = 'file';

    /**
     * Constante définissant un type MIXED (types multiples acceptés)
     */
    const TYPE_MIXED = 'mixed';

    /**
     * UConstructeur
     *
     * @param string $name Nom de l'argument
     * @param string $type Type d'argument
     * @param boolean $required Obligatoire ou pas
     * @param mixed $defaultValue Valeur par défaut
     * @param integer $maxSize Longueur maximale
     */
    public function __construct($name, $type, $required = false, $defaultValue = null, $maxSize = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->defaultValue = $defaultValue;
        $this->maxSize = $maxSize;
    }

    /**
     * Retourne le nom de l'argument
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retourne le type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retourne si l'argument est requis
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Retourne la valeur par défaut de l'argument
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Retourne la taille maximale de la valeur
     *
     * @return integer
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * Vérifie que la valeur de l'argument est compatible
     *
     * @return mixed|void
     */
    public function check()
    {
        if ($this->required
            && !isset(RestServer::$request[$this->name])) {
            if ($this->defaultValue != null) {
                return $this->defaultValue;
            }
            throw new RestException(400, 'Argument ' . $this->name . ' : requis');
        }

        if (!$this->required && !isset(RestServer::$request[$this->name])) {
            if ($this->defaultValue !== null) {
                return $this->defaultValue;
            }
            return null;
        }

        if ($this->type == self::TYPE_STRING) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null && !is_string(RestServer::$request[$this->name])) {
                throw new RestException(400, 'Argument ' . $this->name . ' : type string attendu');
            }
            if(isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null && $this->getMaxSize() && strlen(RestServer::$request[$this->name]) > $this->getMaxSize()){
                throw new RestException(400, 'Argument ' . $this->name . ' : '.strlen(RestServer::$request[$this->name]).' caractères : '. $this->getMaxSize().' caractères acceptés');
            }
        } else if ($this->type == self::TYPE_BOOL) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null && $this->testIfBool(RestServer::$request[$this->name]) === null) {
                throw new RestException(400, 'Argument ' . $this->name . ' : type boolean attendu');
            }
        } else if ($this->type == self::TYPE_FILE) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null) {
                $dt = array();
                if(is_array(RestServer::$request[$this->name])){
                    $dt = RestServer::$request[$this->name];
                }else{
                    $dt = Json::decode(RestServer::$request[$this->name]);
                }
               
                if (!$dt) {
                    throw new RestException(400, 'Argument ' . $this->name . ' : type File attendu (json comprenant deux attributs : fileName et fileContent)');
                } else if (!isset($dt['fileName'])) {
                    throw new RestException(400, 'Argument ' . $this->name . ' : type File attendu (json comprenant deux attributs : fileName et fileContent)');
                } else if (!isset($dt['fileContent'])) {
                    throw new RestException(400, 'Argument ' . $this->name . ' : type File attendu (json comprenant deux attributs : fileName et fileContent)');
                }
                return $dt;
            }
        } else if ($this->type == self::TYPE_NUMERIC) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null && !is_numeric(RestServer::$request[$this->name])) {
                throw new RestException(400, 'Argument ' . $this->name . ' : type numeric attendu');
            }
        } else if ($this->type == self::TYPE_DATE) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null) {
                $val = $this->testIfDate(RestServer::$request[$this->name]);
                if ($val === false) {
                    throw new RestException(400, 'Argument ' . $this->name . ' : type date attendu');
                }
                RestServer::$request[$this->name] = $val;
            }
        } else if ($this->type == self::TYPE_DATETIME) {
            if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null) {
                $val = $this->testIfDateTime(RestServer::$request[$this->name]);
                if ($val === false) {
                    throw new RestException(400, 'Argument ' . $this->name . ' : type datetime attendu');
                }
                RestServer::$request[$this->name] = $val;
            }
        } else if ($this->type == self::TYPE_ARRAY) {

            $val = RestServer::$request[$this->name];
            if (!is_array($val)) {
                $val = Json::decode($val);
            }
            RestServer::$request[$this->name] = $val;

            if (!is_array($val)) {
                throw new RestException(400, 'Argument ' . $this->name . ' : type array attendu');
            }
        } else if ($this->type == self::TYPE_MIXED) {
        } else {
            if (ListTools::len($this->type, '|') > 1) {
                if (isset(RestServer::$request[$this->name]) && RestServer::$request[$this->name] != null) {
                    if (!ListTools::contains($this->type, RestServer::$request[$this->name], '|')) {
                        throw new RestException(400, 'Argument ' . $this->name . ' : valeur ' . RestServer::$request[$this->name] . ' non supportée (' . $this->type . ')');
                    }
                }
            } else {
                throw new RestException(400, 'Argument ' . $this->name . ' : type ' . $this->type . ' non supporté');
            }
        }


        if (!isset(RestServer::$request[$this->name]) && isset($this->defaultValue)) {
            return $this->defaultValue;
        }


        return RestServer::$request[$this->name];
    }

    /**
     * teste si une valeur est booléenne
     *
     * @param mixed $var Valeur à tester
     * @return boolean
     */
    private function testIfBool($var)
    {
        $var = (String)$var . '';
        if ($var === true ||
            $var === '1' ||
            $var === 'true' ||
            $var === 'on' ||
            $var === 'yes' ||
            $var === 'y') {
            return true;
        } else if ($var === false ||
            $var === '0' ||
            $var === 'false' ||
            $var === 'off' ||
            $var === 'no' ||
            $var === 'n' ||
            $var === '') {
            return false;
        } else {
            return null;
        }
    }

    /**
     * Teste si une valeur est une date
     *
     * @param mixed $var Valeur à tester
     * @return string|boolean
     */
    private function testIfDate($var)
    {
        try {
            if (StringTools::contains($var, '-')) {

                $testdate = \DateTime::createFromFormat('Y-m-d', $var);
                if (!$testdate || $testdate->format('Y-m-d') != $var) {
                    return false;
                }
                return $testdate->format('Y-m-d');
            } else if (StringTools::contains($var, '/')) {
                if (StringTools::len(ListTools::ListGetAt($var, 0, '/')) == 1) {
                    $var = '0' . $var;
                }
                $testdate = \DateTime::createFromFormat('d/m/Y', $var);
                if (!$testdate || $testdate->format('d/m/Y') != $var) {
                    return false;
                }
                return $testdate->format('Y-m-d');
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Teste si une valeur est un datetime
     *
     * @param mixed $var Valeur à tester
     * @return string|boolean
     */
    private function testIfDateTime($var)
    {
        try {
            if (StringTools::contains($var, '-')) {
                $testdate = \DateTime::createFromFormat('Y-m-d H:i:s', $var);
                if (!$testdate || $testdate->format('Y-m-d H:i:s') != $var) {
                    return false;
                }
                return $testdate->format('Y-m-d H:i:s');
            } else if (StringTools::contains($var, '/')) {
                if (StringTools::len(ListTools::ListGetAt($var, 0, '/')) == 1) {
                    $var = '0' . $var;
                }
                $testdate = \DateTime::createFromFormat('d/m/Y H:i:s', $var);
                if (!$testdate || $testdate->format('d/m/Y H:i:s') != $var) {
                    return false;
                }
                return $testdate->format('Y-m-d H:i:s');
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }
}