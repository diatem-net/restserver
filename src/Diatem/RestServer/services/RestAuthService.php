<?php

namespace Diatem\RestServer\services;

use \Diatem\RestServer\RestController;
use \Diatem\RestServer\RestArgument;
use \Diatem\RestServer\RestSecurity;
use \Diatem\RestServer\RestConfig;
use \Firebase\JWT\JWT;

/**
 * Service d'authentification
 */
class RestAuthService extends RestController{
    /**
     * @api {post} login
     * @apiDescription Identification aux services et création du JWT
     * @apiGroup 
     * @apiVersion 1.0.0
     * @apiPermission none
     * 
     * @apiParam {string} userID    Nom d'identification de l'utilisateur
     * @apiParam {string} userKey   Clé d'authentification
     * 
     * @url POST /login
     */
    public function login(){
        parent::_exec(
            array(
                new RestArgument('userID', RestArgument::TYPE_STRING, true, null),
                new RestArgument('userKey', RestArgument::TYPE_STRING, true, null)
            ),
            false,
            null,
            null
        );
        
        //Authentification
        if(RestSecurity::login($this->args['userID'], $this->args['userKey'])){
            $jwt = RestSecurity::generateJWT($this->args['userID']);
        }
        
        return parent::_response(array('jwt' => $jwt));
    }
}