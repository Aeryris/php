<?php

/**
    Copyright (c) 2015 Bartlomiej Kliszczyk

    This work is licensed under a
    Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.

 */

/**
 * @author Bartlomiej Kliszczyk
 * @date 12-02-2015
 * @version 1.0
 * @license The MIT License (MIT)
 */

/**
 * @todo Move parser to separate class
 */

class Template_Core implements Template_Interface{

    public $templateName = array();

    public $variables = array();

    public $templatesArray = array();

    public $refresh = false;

    public static $sRealPath;

    public function __construct($sName, $sPath = ''){
        if($sPath == ''){
            self::$sRealPath = \System\System_Core::$sTemplatePath;
        }else{
            self::$sRealPath = $sPath;
        }
        if(!$sName == null || !$sName == '')
            $this->templateName[] = $sName;
    }

    /**
     * Assing template file to variable
     * @param string $TemplateName
     */
    public function assignTemplate($TemplateName){
        $this->templateName[] = $TemplateName;
    }

    public function assignTemplates($aTemplates){
        foreach($aTemplates as $key => $value){
            $this->templateName[] = $value;
        }
    }

    /**
     * This method allows to set parameters and variables that will be passed to template file for easy access
     * Usage: to assign any variables to the template use: $oTemplate->name = value
     * @param string $sIndex
     * @param any $sValue
     */
    public function __set($sIndex, $sValue){
        $this->variables[$sIndex] = $sValue;
    }

    /**
     * This method allows to retrieve assigned parameters and variables to template file
     * @param unknown_type $sIndex
     * @return multitype:
     */
    public function __get($sIndex){
        return $this->variables[$sIndex];
    }

    public function _read_file($filename)
    {
        $res = false;

        if (file_exists($filename)) {
            if (function_exists('ioncube_read_file')) {
                $res = ioncube_read_file($filename);
                if (is_int($res)) $res = false;
            }
            else if ( ($fd = @fopen($filename, 'rb')) ) {
                $res = ($size = filesize($filename)) ? fread($fd, $size) : '';
                fclose($fd);
            }
        }

        return $res;
    }

    /**
     * This method is an interpreter and it generates new temporary template file which is displayed to the user.
     * Method replaces "pseudo interpreted" code into actual php code which can be executed
     * Usage:
     * echo:
     * {! 'text' } or {! $variable }
     *
     * ----------
     *
     * if statement:
     * {if($var == 0)}
     * 	//code
     * {/if}
     *
     * ----------
     *
     * while loop:
     * {while(true)}
     * 	//code
     * {/while}
     *
     * ----------
     *
     * foreach loop:
     * {foreach($var as $k => $v)}
     * 	{! $k }
     * {/foreach}
     *
     * or
     *
     * alternative foreach loop:
     * <foreach($var as $k => $v)>
     * 	//code
     * </foreach>
     *
     * ----------
     *
     * php tags:
     * {php}
     * 	//code
     * {/php}
     *
     * ----------
     *
     * language:
     * <lang="language" />
     *
     * or
     *
     * {lang="language"}
     *
     * ----------
     *
     * file include(only template files available for include):
     * {include file="file.php"}
     *
     *
     * @todo repair switch statement
     * @todo work on require and require_once
     * @todo check if template exisits
     *
     * @param string $sTemporaryTemplate
     */
    public function replace($sTemporaryTemplate, $temporaryTemplate){

        //foreach($this->templateName as $key => $value){


        /**
         * Get OS for preg_replace cross platform incompatilility
         */
        //var_dump(PHP_OS);

        $includeString = '$1';

        if(PHP_OS == 'Linux'){
            $includeString = '$1';
        }elseif(PHP_OS == 'WINNT'){
            $includeString = '$1';
        }

        $TemplatePathsSystem = \System\System_Core::$sTemplatePath;

        $contents = $this->_read_file($sTemporaryTemplate);
        $contentsP = preg_replace(
            array("~\{!\s*(.+?)\s*\}~",
                "~\{%\s*(.+?)\s*\}~",
                "~<lang=(.*?)\s*\/>~",
                "~<foreach(.*)>~",
                "~</foreach>~",
                "~{foreach(.*)}~",
                "~{/foreach}~",
                "~{if(\(.*?)\}~",
                "~{elseif(.*)}~",
                "/{else}/",
                "~{/if}~",
                "~{switch(.*)}~",
                "~\{case\s(.*)\}~",
                "~{break}~",
                "~{default}~",
                "~{/switch}~",
                "~{for(.*)}~",
                "~{/for}~",
                "~{include file=(.*)}~",
                "~{php}~",
                "~{/php}~",
                "~{while(.*)}~",
                "~{/while}~",
                "~{lang=(.*)}~",
                "~{require file=(.*)~",
                "~{require_once file=(.*)~",
                "/{!isset\s*(.*)\s*\}/",
                "~{{(.*)}}~",
                "~\{fcase\s(.*)\}~",
                //"/{(.*)\\.?(.*)}/",
                "/{!\\s+(.*)\\.?(.*)}/",
                "/{input::get(.*)}/",
                "/{input::post(.*)}/",
                "~{slang=(.*)}~",
                "/{if::isset=(.*)}/",
                "/{if::post(.*)}/",
                "/{if::get(.*)}/"


            ),

            array("<?php echo $1 ?>",
                "<?php $1 ?>",
                "<?php echo Language::get($1); ?>",
                "<?php foreach$1: ?>",
                "<?php endforeach; ?>",
                "<?php foreach$1: ?>",
                "<?php endforeach; ?>",
                "<?php if$1: ?>",
                "<?php elseif$1: ?>",
                "<?php else: ?>",
                "<?php endif; ?>",
                "<?php switch$1: ",
                "<?php case $1: ?>",
                "<?php break; ?>",
                "<?php default: ?>",
                "<?php endswitch; ?>",
                "<?php for$1: ?>",
                "<?php endfor; ?>",
                "<?php include(str_replace(' ','','$TemplatePathsSystem $includeString')); ?>",
                "<?php ",
                "?>",
                "<?php while$1 ?>",
                "<?php endwhile; ?>",
                "<?php echo Language::get($1); ?>",
                "<?php require(''); ?>",
                "<?php require_once(''); ?>",
                "<?php if(isset( $1 )) echo $1; ?>",
                "echo $1",
                "case $1: ?>",
                //"$$1['$2'] ",
                "<?php echo $$1['$2'] ?> ",
                "<?php Input::get$1; ?>",
                "<?php Input::post$1 ?>",
                "Language::get($1)",
                "<?php if(isset($1)) echo $1; ?>",
                "<?php if(Input::post$1): ?>",
                "<?php if(Input::get$1): ?>"



            ),
            $contents);
       // var_dump($contentsP);
        file_put_contents($temporaryTemplate, $contentsP);

        //}
    }
    // "'".System::$sPath."' $1"
    /**
     * This method allows to set parameters and variables that will be passed to template file for easy access
     * Usage: to assign any variables to the template use: $oTemplate->set(name, value)
     * @param string $sIndex
     * @param any $sValue
     */
    public function set($sIndex, $sValue){
        $this->variables[$sIndex] = $sValue;
    }

    /**
     * This function renderes the template, it means that it is putting everything together, it creates new file
     * with temporary template and displays it to the user
     * Usage: to display the template simply use: echo $oTemplate->render();
     */
    public function render(){

        foreach($this->templateName as $key => $value){

            if($value == '.php') return;

            $orginalTemplate = self::$sRealPath.  $value. '.php';
            //var_dump($orginalTemplate);
            $temporaryTemplate = self::$sRealPath.'cache'.DIRECTORY_SEPARATOR.$value.'.php';
            //var_dump($temporaryTemplate);
            //var_dump($temporaryTemplate);

            /**
             * @todo Check for time
             * @todo Check for size
             * @todo Check for checksum
             */
            //if(!file_exists($temporaryTemplate) || $this->refresh == true){
                $this->replace($orginalTemplate, $temporaryTemplate);
            //}


            extract($this->variables, EXTR_SKIP);

            //include $temporaryTemplate;

        }

        extract($this->variables, EXTR_SKIP);

        try{
            include(self::$sRealPath.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$this->templateName[0].".php");
        }catch(Exception $e){
            throw new Exception('Failed to include template');
        }
        //var_dump(self::$sRealPath.'application'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$this->templateName[0].".php");


    }

    public function includeTemplate($sName){
        include(self::$sRealPath.'cache'.DIRECTORY_SEPARATOR.$sName.".php");
    }

    /**
     * An alias to the $this->render() function but user does not have to echo the $this->render() function
     * Usage: to display the template simply use: $oTemplate->display();
     */
    public function display(){
        echo $this->render();
    }


    public function __toString(){
        var_dump($this->templateName);
    }


}
