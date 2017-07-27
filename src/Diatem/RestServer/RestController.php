<?php

namespace Diatem\RestServer;

use \Diatem\RestServer\RestArgument;
use \Diatem\RestServer\RestSecurity;
use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestConfig;
use \Diatem\RestServer\RestServer;
use \Diatem\RestServer\RestLog;
use Jin2\Log\PerfAnalyser;

/**
 * Classe dont doivent hériter les controllers écrits pour la définition des services
 */
class RestController{
   
    /**
     * Arguments de la requete
     *
     * @var array
     */
    protected $args = array();

    /**
     * Headers de la requête
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Si appelé en mode debug, objet pour l'analyse des performances
     *
     * @var Jin2\Log\PerfAnalyser
     */
    public $perfAnalyser;


    /**
     * Méthode à appeler pour initialiser un service
     *
     * @param array $arguments Tableau d'arguments (\Diatem\RestServer\RestArgument)
     * @param boolean $secured Définit si la méthode est soumise à authentification
     * @param string $allowTo Liste des groupes de droits (séparés par des espaces) pour lesquels l'appel du service est authorisé (UNIQUEMENT ...) - non compatible avec une valeur dans $disallowTo
     * @param string $disallowTo Liste des groupes de droits (séparés par des espaces) pour lesquels l'appel du service est défendu (TOUS SAUF...) - non compatible avec une valeur dans $allowTo
     * @return void
     */
    public function _exec($arguments = array(), $secured = true, $allowTo = null, $disallowTo = null){
        //Si debug, initialisation de l'analyseur de perfs
        if(isset(RestServer::$request['debug']) && RestServer::$request['debug']){
            $this->perfAnalyser = new PerfAnalyser();
        }

        //Vérification pré-requis serveur
        RestConfig::checkConfiguration();

        //On génère le tableau d'arguments attendus pour la méthode
        $arguments = array_merge($arguments, RestConfig::getRestCallTemplate()->getTemplate());

        //On récupère les données envoyées au service
        $this->getInputs($arguments);

        //On récupère les headers de la requête
        $this->getHeaders();    

        //Vérification authentification
        if($secured){
            //Vérification de la présence du header
            if(!isset($this->headers['Authorization'])){
                throw new RestException(401, 'Unauthorized : header \'Authorization\' non transmis');
            }else{
                //vérification de la conformité du JWT
                RestSecurity::checkJWT($this->headers['Authorization']);
            }

            //Vérification de l'accès à la méthode
            if($allowTo || $disallowTo){
                RestSecurity::checkAccess($allowTo, $disallowTo);
            }
        }
    }


    /**
     * Méthode à appeler pour renvoyer la réponse
     *
     * @param array $array Données à sérialiser dans la réponse
     * @return void
     */
    public function _response($array){
        //Préparation de la réponse
        $array = array_merge($array, RestConfig::getRestResponseTemplate()->getTemplate());

        //Si mode debug, on ajoute les informations nécessaires
        if(isset(RestServer::$request['debug']) && RestServer::$request['debug']){
            $this->perfAnalyser->addPoint();
            $array['executionTime'] = $this->perfAnalyser->getTotalTimeInMS();

            $array['logs'] = RestLog::getLogs();
        }

        //Statut HTTP
        $array['httpStatus'] = 200;
        if($this->server->method == 'POST'){
            //201 pour l'ajout d'une ressource en POST
            $array['httpStatus'] = 201;
        }

        return $array;
    }

    private function getInputs($arguments){
        foreach($arguments AS $arg){
            $this->args[$arg->getName()] = $arg->check();
        }
    }

    private function getHeaders(){
        $this->headers = getallheaders();
    }

   
}