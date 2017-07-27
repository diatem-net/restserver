<?php

namespace Diatem\RestServer\templates;

use \Diatem\RestServer\RestArgument;

/**
 * Template des arguments-type d'une requête aux service
 */
class RestCallTemplate{
    /**
     * Retourne un tableau des arguments-type
     *
     * @return array
     */
    public function getTemplate(){
        return array(
            new RestArgument('debug', RestArgument::TYPE_STRING, false, false)
        );
    }
}