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

interface Base_Controller_Interface{

}

/**
 * Class Base_Controller
 * Base controller, responsible for connecting all required system
 * functionality to all of its subclasses
 * Class automatically allocates features file:
 *      Database
 *      Input -> Get -> Post
 *      Auth
 *      Template
 *      Benchmark
 *      Session
 *
 * Class automatically setups the templating system including:
 *      header
 *      main view (provided by subclass)
 *      footer
 */
class Base_Controller {

    /**
     * Database connection
     * @var PDO
     */
    public $db;

    /**
     * Request methods wrapper
     * @var Input_Core
     */
    public $input;

    /**
     * Post method wrapper
     * @var Input_Core
     */
    public $post;

    /**
     * Get method wrapper
     * @var Input_Core
     */
    public $get;

    /**
     * Authentication system
     * @var Auth_Core
     */
    public $auth;

    /**
     * Template engine
     * @var Template_Core
     */
    public $template;

    /**
     * Used by subclasses to define main view for templates
     * @var String
     */
    public $view;

    /**
     * Default header template
     * @var string
     */
    public $header = 'header';

    /**
     * Default footer template
     * @var string
     */
    public $footer = 'footer';

    /**
     * Benchmark instance
     * Only available when DEVELOPMENT_MODE = true
     * @var Benchmark_Core
     */
    public $benchmark;

    /**
     * $_SESSION wrapper
     * @var Session_Core
     */
    public $session;

    /**
     * Used by subclasses to define whether method is used for responding to ajax calls
     * @var bool
     */
    public $isAjaxCall = false;

    public function __construct(){
        /**
         * Start benchmark class
         */
        if(DEVELOPMENT_MODE){
            $this->benchmark = new Benchmark_Core();
            $this->benchmark->start();
        }

        /**
         * Load session wrapper and enable default language
         * @todo move ->language to config.php
         */
        $this->session = new Session_Core();
        $this->session->language = 'en';

        /**
         * Set language according to session
         * The language system is used throughout the templates and controllers
         */
        Language_Core::setLocale($this->session->language);

        /**
         * Authentication system
         */
        $this->auth = new Auth_Core();

        /**
         * Only start template engine when method is not used for ajax call
         */
        if(!$this->isAjaxCall)
            $this->template = new Template_Core('');

        /**
         * When refresh on template is set to true, cached templates are always re-processed
         * Otherwise only processed and cached templates are used
         */
        if(DEVELOPMENT_MODE) $this->template->refresh = true;

        /**
         * Get current database connection
         */
        $this->db = Database_Core::get();


    }


    /**
     * Assign remaining templates, stop benchmark system and display the page
     * @todo Uncomment code below and test
     */
    public function __destruct()
    {
        /**
         * if(!$this->isAjaxCall){
         *
         * $inc = array();
         * if(isset($this->view)){
         * $inc = array($this->view, $this->footer);
         * }else{
         * $inc = array($this->footer);
         * }
         *
         * //var_dump($this->view);
         *
         * $this->template->assignTemplates($inc);
         * //var_dump($this->template);
         * //$this->benchmark->end();
         * $this->template->display();
         * }
         * }
         *
         */
        $inc = array($this->view);
        $this->template->assignTemplates($inc);
        $this->template->display();
    }
} 