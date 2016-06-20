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

interface Basket_Controller_Interface{

}

class Basket_Controller extends Base_Controller implements Basket_Controller_Interface{

    /**
     * VIEWS METHOD -------------------------------------------------------
     */

    /**
     * Default route /basket -> /basket/index
     */
    public function index(){

        $this->view();

    }

    /**
     * Controller action
     * Displays basket data
     */
    public function view(){
        Auth_Core::init()->isAuth(true);
        Basket_Model::basket()->create();

        $oUser = new User_Model();
        $oUser->attr(['email' => $_SESSION['user']]);

        $this->template->oUser = $oUser->aData;

        $this->template->basketItems = $aBasketData = Basket_Model::basket()->view();
        $this->view = 'basket_view_new';
    }

    /**
     * PRIVATE METHODS ----------------------------------------------------
     * Used for ajax
     */

    /**
     * Used for handling ajax calls
     * Method returns baskets data
     * @return JSON -> basket data
     */
    public function basketData(){
        Auth_Core::init()->isAuth(true);
        $this->isAjaxCall = true;
        $aBasketData = Basket_Model::basket()->view();
        echo json_encode(array('basket' => $aBasketData));
    }

    /**
     * Used for handling ajax calls
     * Method handles removing item from the basket
     * @param in $_POST - Item id
     * @return JSON -> success
     */
    public function removeItem(){
        Auth_Core::init()->isAuth(true);
        $this->isAjaxCall = true;

        $aItemId = $_POST['item_id'];

        Basket_Model::basket()->removeItem($aItemId);

        echo json_encode(array('Success' => $aItemId));
        exit();
    }

    /**
     * Used for handling ajax calls
     * Method handles updating quantity of the items in the basket
     * @param in $_POST - Item id
     * @param in $_POST - Quantity
     * @return JSON -> success
     */
    public function updateQuantity(){
        Auth_Core::init()->isAuth(true);
        $this->isAjaxCall = true;

        $aItemId = $_POST['item_id'];
        $iQuantity = $_POST['quantity'];

        Basket_Model::basket()->updateQuantity($aItemId, $iQuantity);

        echo json_encode(array('Success' => $aItemId));
        exit();
    }


    /**
     * Used for handling ajax calls
     * Method handles adding item to the basket
     * @param in $_POST - Item id
     * @return JSON -> success
     */
    public function addToBasket(){
        Auth_Core::init()->isAuth(true);
        $this->isAjaxCall = true;


        $aItemId = $_POST['item_id'];

        Basket_Model::basket()->addItem($aItemId);


        echo json_encode(array('Success' => $aItemId));
        exit();

    }



} 