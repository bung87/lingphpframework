<?php
namespace ling;
class Controller{
	public $params=array();
	public function __get($nm)
   	{
   		if ($nm=='db'){return Application::getDB();}
   	}
    /**
     * Check if the request is a Ajax request
     * @return bool determined if it is a Ajax request
     */
   	public function isAjax(){
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
    /**
     * Check if the request is a pjax request
     * @return bool determined if it is a pjax request
     */
    public function isPjax(){
    return array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] === 'true';
	}

    /**
     * Check if the connection is a SSL connection
     * @return bool determined if it is a SSL connection
     */
    public function isSSL(){
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
     * @param string $cn class name eg.  'smarty/Smarty'
     * @return void
     */
    public function loadClass($cn){
       require_once APPLICATION_ROOT."/class/".$cn.'.class.php';
    }
    public function before(){}
    public function after(){}
}
?>