<?php

namespace Diatem\RestServer\templates;

use \Diatem\RestServer\RestArgument;
use \Diatem\RestServer\RestServer;

/**
 *  Template d'une réponse-type d'une requête aux services
 */
class RestResponseTemplate{
    /**
     * Retourne un tableau d'une réponse type
     *
     * @return array
     */
    public function getTemplate(){
        $retour = array(
            'errorCode'     =>      null,   //Code interne d'erreur
            'errorMessage'  =>      null,   //Message de l'erreur
            'httpStatus'    =>      0       //Code HTTP
        );
        if(isset(RestServer::$request['debug']) && RestServer::$request['debug']){
            $retour['logs'] = array();      //Tableau des logs
            $retour['executionTime'] = 0;   //Temps d'execution
        }

        return $retour;
    }
}