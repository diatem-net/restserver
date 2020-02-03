<?php
namespace Diatem\RestServer;

use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestUser;
use \Diatem\RestServer\templates\RestCallTemplate;
use \Diatem\RestServer\templates\RestResponseTemplate;

/**
 * Permet de configurer ou de récupérer la configuration du serveur
 */
class RestConfig{
    /**
     * Nom identificatif du serveur
     *
     * @var string
     */
    private static $appzName = null;

    /**
     * SecretKey pour la génération du JWT
     *
     * @var string
     */
    private static $secretKey = null;

    /**
     * Type d'encodage pour la génération du JWT
     *
     * @var string
     */
    private static $encryptAlg = 'HS512';

    /**
     * Le JWT sera valide N secondes après sa génération
     *
     * @var integer
     */
    private static $sessionNotUseBeforSeconds = 0;

    /**
     * Le JWT sera valide pendant N secondes
     *
     * @var integer
     */
    private static $sessionValiditySeconds = 3600;

    /**
     * Utilisateurs du serveur
     *
     * @var array
     */
    private static $users = array();

    /**
     * Template utilisée pour les arguments-type d'une requête
     *
     * @var \Diatem\RestServer\templates\RestCallTemplate
     */
    private static $restCallTemplate;

    /**
     * Template utilisée pour la réponse-type d'une requête
     *
     * @var \Diatem\RestServer\templates\RestResponseTemplate
     */
    private static $restResponseTemplate;

    /**
     * Namespace complet de la classe utilisée pour les vérifications de tokens de type Bearer
     * 
     */
    private static $bearerTokenCheckerClassPath = null;

    /**
     * Methode de $bearerTokenCheckerClass appelée pour vérifier un token Bearer
     */
    private static $bearerTokenCheckerStaticMethod = null;


    public static function getBearerTokenCheckerClassPath(){
        return self::$bearerTokenCheckerClassPath;
    }

    public static function getBearerTokenCheckerStaticMethod(){
        return self::$bearerTokenCheckerStaticMethod;
    }

    /**
     * Définir la classe / méthode utilisée pour vérifier les tokens de type Bearer (token envoyé en paramètre dans la méthode)
     * @param object    $classPath      Namespace complet de la classe
     * @param string    $staticMethod    Nom de la méthode statique qui sera appelée pour vérifier le token (avec token envoyé en paramètre)
     * 
     */
    public static function setBearerTokenChecker($classPath, $staticMethod){
        self::$bearerTokenCheckerClassPath = $classPath;
        self::$bearerTokenCheckerStaticMethod = $staticMethod;
    }

    /**
     * Retourne un utilisateur par son userID
     *
     * @param string $userID UserID de l'utilisateur à récupérer
     * @return \Diatem\RestServer\RestUser;
     */
    public static function getUser($userID){
        return self::$users[$userID];
    }

    /**
     * Retourne tous les utilisateurs
     *
     * @return array
     */
    public static function getUsers(){
        return self::$users;
    }

    /**
     * Ajoute un utilisateur
     *
     * @param string $userID UserID de l'utilisateur
     * @param string $userKey Clé d'authentification
     * @param string $securityPolicies groupes de droits dans lesquels l'utilisateur est inscrits. Séparés par des espaces.
     * @return void
     */
    public static function addUser($userID, $userKey, $securityPolicies = ''){
        self::$users[$userID] = new RestUser($userID, $userKey, $securityPolicies);
    }

    /**
     * Définit le nom identificatif du serveur
     *
     * @param string $appzName Nom identificatif du serveur
     * @return void
     */
    public static function setAppzName($appzName){
        self::$appzName = $appzName;
    }

    /**
     * Retourne le nom identificatif du serveur
     *
     * @return string
     */
    public static function getAppzName(){
        return self::$appzName;
    }

    /**
     * Définit la secretKey pour la génération du JWT
     *
     * @param string $secretKey
     * @return void
     */
    public static function setSecretKey($secretKey){
        self::$secretKey = $secretKey;
    }

    /**
     * Retourne la secretKey utilisée pour la génération du JWT
     *
     * @return string
     */
    public static function getSecretKey(){
        return self::$secretKey;
    }

    /**
     * Définit la méthode d'encryptage du JWT
     *
     * @param string $alg Nom de l'algorithme à utiliser
     * @return void
     */
    public static function setEncryptAlg($alg){
        self::$encryptAlg = $alg;
    }

    /**
     * Retourne la méthode d'encryptage du JWT
     *
     * @return string
     */
    public static function getEncryptAlg(){
        return self::$encryptAlg;
    }

    /**
     * Définit le temps minimum (en secondes) qui doit s'écouler entre la génération du JWT et son premier usage
     *
     * @param integer $seconds Nombre de secondes
     * @return void
     */
    public static function setSessionNotUseBeforeSeconds($seconds){
        self::$sessionNotUseBeforSeconds = $seconds;
    }

    /**
     * Retourne le temps minimum (en secondes) qui doit s'écouler entre la génération du JWT et son premier usage
     *
     * @return integer
     */
    public static function getSessionNotUseBeforeSeconds(){
        return self::$sessionNotUseBeforSeconds;
    }

    /**
     * Définit le temps durant lequel le JWT sera valide (en secondes)
     *
     * @param integer $seconds Nombre de secondes
     * @return void
     */
    public static function setSessionValiditySeconds($seconds){
        self::$sessionValiditySeconds = $seconds;
    }

    /**
     * Retourne le temps durant lequel le JWT sera valide (en seconde)
     *
     * @return integer
     */
    public static function getSessionValiditySeconds(){
        return self::$sessionValiditySeconds;
    }

    /**
     * Définit la template utilisée pour les arguments-type d'une requête
     *
     * @param \Diatem\RestServer\templates\RestCallTemplate $restCallTemplate
     * @return void
     */
    public static function setRestCallTemplate($restCallTemplate){
        self::$restCallTemplate = $restCallTemplate;
    }

    /**
     * Retourne la template utilisée pour les arguments-type d'une requête
     *
     * @return \Diatem\RestServer\templates\RestCallTemplate
     */
    public static function getRestCallTemplate(){
        return self::$restCallTemplate;
    }

    /**
     * Définit la template utilisée pour la réponse-type d'une requête
     *
     * @param \Diatem\RestServer\templates\RestResponseTemplate $restResponseTemplate
     * @return void
     */
    public static function setRestResponseTemplate($restResponseTemplate){
        self::$restResponseTemplate = $restReponseTemplate;
    }

    /**
     * Retourne la template utilisaée pour la réponse-type d'une requête
     *
     * @return \Diatem\RestServer\templates\RestResponseTemplate
     */
    public static function getRestResponseTemplate(){
        return self::$restResponseTemplate;
    }

    /**
     * Vérifie la conformité de la configuration du serveur
     *
     * @return void
     */
    public static function checkConfiguration(){
        if(!self::$secretKey){
            throw new RestException(401, 'Unauthorized : paramètre secretKey du serveur REST non configuré');
        }
        if(!self::$appzName){
            throw new RestException(401, 'Unauthorized : paramètre appzName du serveur REST non configuré');
        }
        if(!self::$restCallTemplate){
            self::$restCallTemplate = new RestCallTemplate();
        }
        if(!self::$restResponseTemplate){
            self::$restResponseTemplate = new RestResponseTemplate();
        }
    }


}