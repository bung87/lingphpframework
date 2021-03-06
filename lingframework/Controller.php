<?php
/**
 * Class ling\Controller
 *
 * Base class of all controller.
 *
 * @author     bung <zh.bung@gmail.com>
 * @copyright  Copyright © 2013 bung.
 * @license    New BSD License
 */
 
namespace ling;

/**
 *
 * base controller.
 *
 * Parameter lists can be accessed through $this->params.
 * GET  variables can still be accessed via php $_GET 
 *
 */
class Controller{
    public $controllerName='';
    public $methodName='';
    /**
     * Associative array of the parameter list found matched in a URI route.
     * @var array
     */
	public $params=array();
    /**
     * quik access current DB instance. e.g $this->db
     * you can use parent::__get($name) access this method,if you'd like to override.
     * @param string $nm 
     * @return mixed
     */
	public function __get($nm)
   	{
   		if ($nm=='db'){return Application::getDB();}
   	}
    /**
     * Check if the request is a Ajax request
     * @return bool determined if it is a Ajax request
     */
   	protected function isAjax(){
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
    /**
     * Check if the request is a pjax request
     * @return bool determined if it is a pjax request
     */
    protected function isPjax(){
    return array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] === 'true';
	}

    /**
     * Check if the connection is a SSL connection
     * @return bool determined if it is a SSL connection
     */
    protected function isSSL(){
        if(!isset($_SERVER['HTTPS']))
            return FALSE;
        //Apache
        if($_SERVER['HTTPS'] === 1) {
            return TRUE;
        }
        //IIS
        elseif ($_SERVER['HTTPS'] === 'on') {
            return TRUE;
        }
        //other servers
        elseif ($_SERVER['SERVER_PORT'] == 443){
            return TRUE;
        }
        return FALSE;
    }
    /**
     *
     * load class file form class directory
     * file name will be retrieved as "$cn.class.php".
     *
     * @param string $cn class name e.g.  'smarty/Smarty'
     * @return void
     */
    protected function loadClass($cn){
       require_once APPLICATION_ROOT."/class/".$cn.'.class.php';
    }
    /**
     * This will be called before the actual action is executed
     */
    public function before(){}
    /**
     * This will be called after the actual action is executed
     */
    public function after(){}
    protected function render(){}
    protected function redirect($path){
        if(strpos($path, '/')===0) $path=substr($path, 1);
        header("Location: ".($this->isSSL() ? 'https' : 'http')."://".$_SERVER['SERVER_NAME']."/$path");
    }
}
?>