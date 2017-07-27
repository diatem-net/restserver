<?php
use \Diatem\RestServer\RestException;
use \Diatem\RestServer\RestController;
use \Diatem\RestServer\RestArgument;

/**
 * Classe définissant des services
 */
class Cornichons extends RestController{
    /**
     * @api {post} cornichons/
     * @url POST /cornichons/
     * @apiDescription Créé un cornichon
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission admin
     * 
     * @apiParam {string} nom Nom du cornichon
     * 
     */
    public function post_cornichons(){
         parent::_exec(
            array(
                new RestArgument('nom', 'A|B', true, null)
            ),
            true,
            'admin',
            null
        );
        
        return parent::_response(array('task' => "CREATION D'UN CORNICHON : ".$this->args['nom']));
    }

    /**
     * @api {get} cornichons/:id
     * @apiDescription Retourne un cornichon
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     * 
     * @apiParam {string} fields Champs à récupérer
     * 
     * @url GET /cornichons/$id
     */
    public function get_cornichons_id($id = null){
        parent::_exec(
            array(
                new RestArgument('fields', RestArgument::TYPE_STRING, false, '')
            ),
            true,
            null,
            null
        );

        return parent::_response(array('task' => 'CORNICHON (fields : '.$this->args['fields'].')');
    }

    /**
     * @api {put} cornichons/:id
     * @apiDescription Modifie un cornichon
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     * 
     * @apiParam {string} nom Nom du cornichon
     * 
     * @url PUT /cornichons/$id
     */
    public function put_cornichons_id($id = null){
        parent::_exec(
            array(
                new RestArgument('nom', RestArgument::TYPE_STRING, true, null)
            ),
            true,
            null,
            null
        );

        return parent::_response(array('task' => "MODIFICATION D'UN CORNICHON (COMPLETE) : ".$this->args['nom']);
    }

    /**
     * @api {patch} cornichons/:id
     * @apiDescription Modifie un cornichon (partiel)
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     * 
     * @apiParam {string} nom Nom du cornichon
     *
     * @url PATCH /cornichons/$id
     */
    public function patch_cornichons_id($id = null){
        parent::_exec(
            array(
                new RestArgument('nom', RestArgument::TYPE_STRING, true, null)
            ),
            true,
            null,
            null
        );

        return parent::_response(array('task' => "MODIFICATION D'UN CORNICHON (PARTIEL) : ".$this->args['nom']);
    }

    /**
     * @api {delete} cornichons/:id
     * @apiDescription Supprime un cornichon
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     * 
     * @url DELETE /cornichons/$id
     */
    public function delete_cornichons_id($id = null){
         parent::_exec(
            array(
                new RestArgument('nom', RestArgument::TYPE_STRING, true, null)
            ),
            true,
            null,
            null
        );

        return parent::_response(array('task' => "SUPPRESSION D'UN CORNICHON : ".$this->args['nom']));
    }



    /**
     * @api {get} cornichons/
     * @apiDescription Retourne la liste des cornichons
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     *
     * @url GET /cornichons/
     */
    public function get_cornichons(){
        parent::_exec(array(), true, null, null);

        return parent::_response(array('task' => "CORNICHON LIST");
    }

    /**
     * @api {get} cornichons/:id/vendeurs/
     * @apiDescription Retourne la liste des vendeurs de cornichons
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     *
     * @url GET /cornichons/$id/vendeurs/
     */
    public function get_cornichons_id_vendeurs(){
         parent::_exec(array(), true, null, null);

        return parent::_response(array('task' => "CORNICHON VENDEUR LIST");
    }

    /**
     * @api {get} cornichons/:id/vendeurs/:id
     * @apiDescription Retourne un vendeur de cornichon
     * @apiGroup cornichons
     * @apiVersion 1.0.0
     * @apiPermission none
     *
     * @url GET /cornichons/$id/vendeurs/$j
     */
    public function get_cornichons_id_vendeurs_id($i = null, $j = null){
         parent::_exec(array(), true, null, null);

        return parent::_response(array('task' => "CORNICHON VENDEUR");
    }

}