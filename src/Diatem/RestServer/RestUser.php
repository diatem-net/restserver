<?php

namespace Diatem\RestServer;

use \Firebase\JWT\JWT;
use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestConfig;

/**
 * Classe gérant un utilisateur des services
 */
class RestUser{
    /**
     * userID
     *
     * @var string
     */
    private $userID;

    /**
     * UserKey
     *
     * @var string
     */
    private $userKey;

    /**
     * Groupe de droits
     *
     * @var array
     */
    private $userSecurityPolicies;

    /**
     * Constructeur
     *
     * @param string $userID UserID
     * @param string $userKey Clé d'authentification
     * @param string $userSecurityPolicies Groupes de droits (séparés par des espaces)
     */
    public function __construct($userID, $userKey, $userSecurityPolicies){
        $this->userSecurityPolicies = explode(' ', $userSecurityPolicies);
        $this->userID = $userID;
        $this->userKey = $userKey;
    }

    /**
     * Retourne la clé d'authentification
     *
     * @return string
     */
    public function getUserKey(){
        return $this->userKey;
    }

    /**
     * Retourne les groupes de droits
     *
     * @return array
     */
    public function getUserSecurityPolicies(){
        return $this->userSecurityPolicies;
    }
}