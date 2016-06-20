<?php

/**
    The MIT License (MIT)

    Copyright (c) 2015 Bartlomiej Kliszczyk

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.
 */

/**
 * @author Bartlomiej Kliszczyk
 * @date 12-02-2015
 * @version 1.0
 * @license The MIT License (MIT)
 */


interface Basket_Interface{

    /**
     * Singleton initializer
     *
     * If iId (basket_id) parameter is provided and basket is assigned to current user
     * all basket data is fetched
     *
     * @param int $iId - Takes optional basket_id parameter
     * @return self
     */
    public static function basket($iId = null);

    /**
     * Control method
     * Informs the class that basket might have to be created
     * @return $this
     * @throws Basket_Exception
     */
    public function create();

    /**
     * Control method
     * Informs the class that basket might have to be fetched
     * @return $this
     * @throws Basket_Exception
     */
    public function get();

    /**
     * Interface method for adding item to the basket
     * @param $iItemId
     * @return $this
     * @throws Basket_Exception
     */
    public function addItem($iItemId);

    /**
     * Interface method for removing item to the basket
     * @param $iItemId
     * @return $this
     * @throws Basket_Exception
     */
    public function removeItem($iItemId);

    /**
     * Interface method for updating item from the basket
     * @param $iItemId
     * @param $iQuantity
     * @return $this
     * @throws Basket_Exception
     */
    public function updateQuantity($iItemId, $iQuantity);

    /**
     * Find basket information (no items) by providing user id
     * If there is ACTIVE basket connected to the user data is returned otherwise false
     * @param $iUserId
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function findBasketByUserId($iUserId);

    /**
     * Find all basket with items information by providing user id
     * If there is ACTIVE basket connected to the user data is returned otherwise false
     * @param $iUserId
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function findBasketWithItems($iUserId);

    /**
     * Interface for returning basket with items that belongs to currently logged in user
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function view();

}

class Basket_Exception extends Exception{}

class Basket_Model extends Foundation_Model implements Basket_Interface{

    /**
     * Singleton object reference
     * @var Object
     */
    public static $oInstance;

    /*
     * Private database connection reference
     */
    public $db;

    /**
     * Private
     * Control variable to determine whether basket should be created
     * @var bool
     */
    private $bCreateBasket = false;

    /**
     * Basket ownser id
     * @var int
     */
    public $iOwnerId;

    /**
     * Fetched basket data
     * @var null | array
     */
    public $aBasketData = null;


    /**
     * Singleton initializer
     *
     * If iId (basket_id) parameter is provided and basket is assigned to current user
     * all basket data is fetched
     *
     * @param int $iId - Takes optional basket_id parameter
     * @return self (Basket_Model)
     */
    public static function basket($iId = null){

        if(!self::$oInstance instanceof self){
            self::$oInstance = new self($iId);
        }

        return self::$oInstance;
    }

    /*
     * Default initializer
     */
    public function __construct($iId = null){

        //if(self::$oInstance == null) throw new Basket_Exception('Default initializer disabled use: Basket_Model::basket(<id>)');

        /*
         * Store provided basket ID or default null
         */
        $this->iOwnerId = $iId;

        /**
         * Get current database connection
         */
        $this->db = Database_Core::get();

        /**
         * Check if user has any basket assigned to him
         */
        $oUser = new User_Model();
        $oUser->attr(['email' => $_SESSION['user']]);
        $bBasketExists = $this->findBasketByUserId($oUser->aData['user_id']);

        if($iId != null) $this->bCreateBasket = true;

        /**
         * Create or fetch basket
         */
        $this->prepareBasket();

        /**
         * Check if basket will be assigned to the user
         */
        //if($sId == null)
          //  throw new Basket_Exception('Basket owner ID cannot be null');

         /**
          * @todo Check if user exists
          */
        return $this;
    }

    /**
     * Control method
     * Informs the class that basket might have to be created
     * @return $this
     * @throws Basket_Exception
     */
    public function create(){

        $this->bCreateBasket = true;

        $this->prepareBasket();

        return $this;
    }

    private function prepareBasket(){

        $oUser = new User_Model();
        $oUser->attr(['email' => $_SESSION['user']]);


        /**
         * Find basket assigned to the user
         */
        $bBasketExists = $this->findBasketByUserId($oUser->aData['user_id']);

        try{

            /**
             * If basket does not exist crease one and get the data, otherwise get data of existing and active basket
             */
            if(!$bBasketExists && $oUser->aData['user_id'] != null){
                $sQuery = 'INSERT INTO basket(basket_owner_id, basket_active) VALUES(:owner_id, :active)';

                $oStmt = $this->db->prepare($sQuery);

                $oStmt->bindValue(':owner_id', $oUser->aData['user_id'], PDO::PARAM_INT);
                $oStmt->bindValue(':active', "true", PDO::PARAM_STR);

                $bExecute = $oStmt->execute();

                /**
                 * Get newly created basket if sql insert was successful
                 */
                $this->aBasketData = $this->findBasketByUserId($oUser->aData['user_id']);
            }else{
                /**
                 * Get old basket if one exists
                 */
                $this->aBasketData = $this->findBasketByUserId($oUser->aData['user_id']);
            }

        }catch(Basket_Exception $e){
            throw new Basket_Exception($e);
        }

        return $this;
    }

    /**
     * Control method
     * Informs the class that basket might have to be fetched
     * @return $this
     * @throws Basket_Exception
     */
    public function get(){


        $this->bCreateBasket = false;

        $this->prepareBasket();
        return $this;
    }

    /**
     * Interface method for adding item to the basket
     * @triggers p_addItem($Id)
     * @param $iItemId
     * @return $this
     * @throws Basket_Exception
     */
    public function addItem($iItemId){

        $this->p_addItem($iItemId);

        return $this;
    }

    /**
     * Logic for addItem method
     * Method checks whether requested basket exists, if it does:
     * method checks whether there is already requested item in the basket: if yes, quantity is updated
     *                                                                      if not, new item is inserted into the basket
     * @param $iItemId
     * @return $this
     * @throws Basket_Exception
     */
    private function p_addItem($iItemId){

        try{

            $sQuery = 'SELECT * FROM basket_items WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

            $oStmt = $this->db->prepare($sQuery);

            $oStmt->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
            $oStmt->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

            $bExecute = $oStmt->execute();

            $aBasketItemData = $oStmt->fetch(PDO::FETCH_ASSOC);

            /**
             * If item already exists in the basket increase quantity otherwise add as new item
             */
            if($aBasketItemData){
                $iIncrease = $aBasketItemData['basket_items_quantity'];
                $iIncrease++;
                $sQueryUpdate = 'UPDATE basket_items SET basket_items_quantity = :item_quantity WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

                $oStmtUpdate = $this->db->prepare($sQueryUpdate);

                $oStmtUpdate->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
                $oStmtUpdate->bindValue(':item_quantity', $iIncrease, PDO::PARAM_INT);
                $oStmtUpdate->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

                $bExecute = $oStmtUpdate->execute();
            }else{


                $sQueryInsert = 'INSERT INTO basket_items(basket_items_id, basket_items_item_id, basket_items_quantity) VALUES(:basket_id, :item_id, :quantity)';
                $oStmtInsert = $this->db->prepare($sQueryInsert);

                $oStmtInsert->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
                $oStmtInsert->bindValue(':item_id', $iItemId, PDO::PARAM_INT);
                $oStmtInsert->bindValue(':quantity', 1, PDO::PARAM_INT);

                $bExecute = $oStmtInsert->execute();

            }


        }catch (Basket_Exception $e){
            throw new Basket_Exception($e);
        }

        return $this;
    }




    /**
     * Interface method for removing item from the basket
     * @triggers p_removeItem($Id)
     * @param $iItemId
     * @return $this
     * @throws Basket_Exception
     */
    public function removeItem($iItemId){
        $this->p_removeItem($iItemId);
        return $this;
    }

    /**
     * Logic method for removeItem interface
     * Method checks whether basket exists and contains requested item: if yes item is removed
     * @param $iItemId
     * @throws Basket_Exception
     */
    private function p_removeItem($iItemId){
        try{

            $sQuery = 'SELECT * FROM basket_items WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

            $oStmt = $this->db->prepare($sQuery);

            $oStmt->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
            $oStmt->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

            $bExecute = $oStmt->execute();

            $aBasketItemData = $oStmt->fetch(PDO::FETCH_ASSOC);

            /*
             * If requested item is in the db and basket, it is removed
             */
            if($aBasketItemData){
                $sQueryUpdate = 'DELETE FROM basket_items WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

                $oStmtUpdate = $this->db->prepare($sQueryUpdate);

                $oStmtUpdate->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
                $oStmtUpdate->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

                $bExecute = $oStmtUpdate->execute();
            }


        }catch (Basket_Exception $e){
            throw new Basket_Exception($e);
        }
    }

    /**
     * Interface method for updating item from the basket
     * @triggers p_updateQuantity($Id)
     * @param $iItemId
     * @param $iQuantity
     * @return $this
     * @throws Basket_Exception
     */
    public function updateQuantity($iItemId, $iQuantity){
        $this->p_updateQuantity($iItemId, $iQuantity);
        return $this;
    }

    /**
     * Logic method for updateQuantity interface
     * Method checks whether basket exists and contains requested item: if yes item quantity is changed
     * @param $iItemId
     * @param $iQuantity
     * @return $this
     * @throws Basket_Exception
     * @todo check if requested quantity is in stock
     */
    private function p_updateQuantity($iItemId, $iQuantity){
        try{

            $sQuery = 'SELECT * FROM basket_items WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

            $oStmt = $this->db->prepare($sQuery);

            $oStmt->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
            $oStmt->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

            $bExecute = $oStmt->execute();

            $aBasketItemData = $oStmt->fetch(PDO::FETCH_ASSOC);

            /**
             * If item already exists in the basket increase quantity otherwise add as new item
             */
            if($aBasketItemData){
                $iIncrease = $iQuantity;
                $sQueryUpdate = 'UPDATE basket_items SET basket_items_quantity = :item_quantity WHERE basket_items_id = :basket_id AND basket_items_item_id = :item_id';

                $oStmtUpdate = $this->db->prepare($sQueryUpdate);

                $oStmtUpdate->bindValue(':basket_id', $this->aBasketData['basket_id'], PDO::PARAM_INT);
                $oStmtUpdate->bindValue(':item_quantity', $iIncrease, PDO::PARAM_INT);
                $oStmtUpdate->bindValue(':item_id', $iItemId, PDO::PARAM_INT);

                $bExecute = $oStmtUpdate->execute();
            }


        }catch (Basket_Exception $e){
            throw new Basket_Exception($e);
        }

        return $this;
    }


    /**
     * Find basket information (no items) by providing user id
     * If there is ACTIVE basket connected to the user data is returned otherwise false
     * @param $iUserId
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function findBasketByUserId($iUserId){

        $aBasketData = false;

        try{

            $sQuery = 'SELECT * FROM basket WHERE basket_owner_id = :id';

            $oStmt = $this->db->prepare($sQuery);

            $oStmt->bindValue(':id', $iUserId, PDO::PARAM_INT);

            $bExecute = $oStmt->execute();
            $aBasketData = $oStmt->fetch(PDO::FETCH_ASSOC);

        }catch(Basket_Exception $e){
            throw new Basket_Exception($e);
        }

        return $aBasketData;
    }

    /**
     * Find all basket with items information by providing user id
     * If there is ACTIVE basket connected to the user data is returned otherwise false
     * @param $iUserId
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function findBasketWithItems($iUserId){

        $aBasketData = false;

        try{

            $sQuery = 'SELECT * FROM basket JOIN basket_items ON basket.basket_id = basket_items.basket_items_id JOIN item ON basket_items.basket_items_item_id = item.item_id  WHERE basket_owner_id = :id';

            $oStmt = $this->db->prepare($sQuery);

            $oStmt->bindValue(':id', $iUserId, PDO::PARAM_INT);

            $bExecute = $oStmt->execute();

            $aBasketData = $oStmt->fetchAll(PDO::FETCH_ASSOC);


        }catch(Basket_Exception $e){
            throw new Basket_Exception($e);
        }

        return $aBasketData;

    }


    /**
     * Interface for returning basket with items that belongs to currently logged in user
     * @return bool|mixed
     * @throws Basket_Exception
     */
    public function view(){

        $oUser = new User_Model();
        $oUser->attr(['email' => $_SESSION['user']]);


        return $this->findBasketWithItems($oUser->aData['user_id']);
    }

    public function clear(){

        try{
            $this->db->beginTransaction();
            $aBasketData = Basket_Model::basket()->view();

            $sQuery = 'DELETE FROM basket_items WHERE basket_items_id = :basket_id';

            $oBasketItems = $this->db->prepare($sQuery);
            $oBasketItems->bindValue(':basket_id',$aBasketData[0]['basket_id']);
            $oBasketItems->execute();

            $sBasket = 'DELETE FROM basket WHERE basket_id = :basket_id';
            $oBasket = $this->db->prepare($sBasket);
            $oBasket->bindValue(':basket_id',$aBasketData[0]['basket_id']);
            $oBasketItems->execute();

            $this->db->commit();
        }catch(Exception $e){
            $this->db->rollBack();
        }

    }

} 