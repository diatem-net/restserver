<?php

namespace Diatem\RestServer;

use \Diatem\RestServer\RestArgument;
use \Diatem\RestServer\RestSecurity;
use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestConfig;
use \Diatem\RestServer\RestServer;
use Jin2\Log\PerfAnalyser;

/**
 * Classe permettant d'enregistrer des logs qui seront remontés dans la réponse au service (en mode debug)
 */
class RestLog{
    /**
     * Logs enregistrés
     *
     * @var array
     */
    private static $logs = array();

    /**
     * Ajoute un log
     *
     * @param string $texte
     * @return void
     */
    public static function addLog($texte){
        self::$logs[] = $texte;
    }

    /**
     * Retourne les logs enregistrés
     *
     * @return array
     */
    public static function getLogs(){
        return self::$logs;
    }
}