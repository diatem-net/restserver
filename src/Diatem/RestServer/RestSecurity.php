<?php

namespace Diatem\RestServer;

use \Firebase\JWT\JWT;
use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestConfig;
use \Diatem\RestServer\RestUser;
use Jin2\Utils\ListTools;
use Jin2\Utils\ArrayTools;

/**
 * Classe permettant de gérer la politique de sécurisation des services
 */
class RestSecurity{
    /**
     * Utilisateur authentifié (authentification Basic)
     *
     * @var Diatem\RestServer\RestUser
     */
    private static $authentificatedUser;

    /**
     * Token Bearer authentifié (authentification Bearer)
     *
     * @var string 
     */
    private static $bearerAuthentificatedToken;

    /**
     * Vérification d'un jeton JWT
     *
     * @param string $jwtToken
     * @return void
     */
    public static function checkJWT($jwtToken){
        $secretKey = base64_decode(RestConfig::getSecretKey());
        try{
           $token = JWT::decode($jwtToken, $secretKey, array(RestConfig::getEncryptAlg()));
           self::$authentificatedUser = new RestUser($token->data->userID, null, $token->data->userSecurityPolicies);
        }catch(\Exception $e){
            throw new RestException(401, 'Unauthorized : '.$e->getMessage());
        }
    }

    /**
     * Vérifie une authentification via un header Authenfication Basic
     */
    public static function checkBasicCredentials($authentification){
        $datas = ListTools::toArray($authentification, ' ');
        if(strtolower($datas[0]) != 'basic'){
            throw new RestException(401, 'Unauthorized : header \'authorization\' Basic requis');
        }

        $decoded = base64_decode($datas[1]);
        if(ListTools::len($decoded, ':') != 2){
            throw new RestException(401, 'Unauthorized : header \'authorization\' Basic : mauvais format');
        }
        $credentials = ListTools::toArray($decoded, ':');

        self::login($credentials[0], $credentials[1]);
        $user = RestConfig::getUsers()[$credentials[0]];
        self::$authentificatedUser = new RestUser($credentials[0], null, ArrayTools::toList(RestConfig::getUsers()[$credentials[0]]->getUserSecurityPolicies(), ' '));
    }



    public static function checkBearerCrendentials($authentification){
        if(!RestConfig::getBearerTokenCheckerClassPath() || !RestConfig::getBearerTokenCheckerStaticMethod()){
            throw new RestException(500, 'Configuration incorrecte du paramétrage de vérification des tokens d\'authentification de type Bearer');
        }

		$datas = ListTools::toArray($authentification, ' ');
		if(count($datas) != 2){
            throw new RestException(401, 'Unauthorized : header \'authorization\' format non conforme');
        }

       
        if(strtolower($datas[0]) != 'bearer'){
            throw new RestException(401, 'Unauthorized : header \'authorization\' bearer requis');
        }

        $return = call_user_func(RestConfig::getBearerTokenCheckerClassPath().'::'.RestConfig::getBearerTokenCheckerStaticMethod(), $datas[1]);

        if($return){
            self::$bearerAuthentificatedToken = $datas[1];
        }else{
            throw new RestException(401, 'Unauthorized : Bearer token invalide');
        }
    }

    /**
     * Authentification à partir d'un userID et un userKey
     *
     * @param string $userID
     * @param string $userKey
     * @return boolean
     */
    public static function login($userID, $userKey){
        if(isset(RestConfig::getUsers()[$userID])){
            $user = RestConfig::getUsers()[$userID];
            if($user->getUserKey() != $userKey){
                throw new RestException(401, 'Unauthorized : userKey invalide');
            }
            return true;
        }else{
            throw new RestException(401, 'Unauthorized : userID et userKey invalides');
        }
    }

    /**
     * Génére un JWT pour un userID donné
     *
     * @param [type] $userID
     * @return void
     */
    public static function generateJWT($userID){

        $user = RestConfig::getUsers()[$userID];

        $tokenId    = base64_encode(mcrypt_create_iv(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + RestConfig::getSessionNotUseBeforeSeconds();
        $expire     = $notBefore + RestConfig::getSessionValiditySeconds();         
        $serverName = RestConfig::getAppzName();
            
        /*
        * Create the token as an array
        */
        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userID'   => $userID,      // userid from the users table
                'userSecurityPolicies' => implode(' ', $user->getUserSecurityPolicies())
            ]
        ];

        $secretKey = base64_decode(RestConfig::getSecretKey());
        $jwt = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            RestConfig::getEncryptAlg()     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
        
        return $jwt;
    }

    /**
     * Vérifie si l'utilisateur authentifié peut accéder à la ressource
     *
     * @param string $allowTo groupes de droits autorisés (UNIQUEMENT ...) séparé par des espaces
     * @param [type] $disallowTo groupes de droits refusés (TOUS SAUF ...) séparé par des espaces
     * @return void
     */
    public static function checkAccess($allowTo = null, $disallowTo = null){
        if(self::$bearerAuthentificatedToken){
            return;
        }
        if(!$allowTo && !$disallowTo){
            return;
        }

        if($allowTo){
            $intersect = array_intersect(explode(' ', $allowTo), self::$authentificatedUser->getUserSecurityPolicies());
            if(count($intersect) > 0){
                return;
            }
            throw new RestException(403, 'Forbidden : ressource non autorisée');
        }
        if($disallowTo){
            $intersect = array_intersect(explode(' ', $disallowTo), self::$authentificatedUser->getUserSecurityPolicies());
            if(count($intersect) > 0){
                throw new RestException(403, 'Forbidden : ressource non autorisée');
            }
            return;
        }
    }
}