<?php
/**
 * Class ling\Router
 *
 * @author     bung <zh.bung@gmail.com>
 * @copyright  Copyright © 2013 bung.
 * @license    New BSD License
 */

namespace ling;

/**
 *
 * Router class
 *
 */
class Router{
	/**
     * $_SERVER['REQUEST_URI']
     * @var string
     */
	public $request_uri;
	/**
	 * assign $_SERVER['REQUEST_URI'] value to $this->request_uri
	 */
	function __construct(){

		$this->request_uri=$_SERVER['REQUEST_URI'];
	}
	/**
	 *
	 * retrieve request uri,return uri that directory name part removed
	 *
	 * @return string $query
	 */
	function getRoute(){
		$query="";
		$pieces=explode("/", APPLICATION_ROOT);
		foreach ($pieces as $k => $v) {
				if($v!=""){
					$pos=strpos($this->request_uri, $v );
					if($pos!==false ){
						$query = substr($this->request_uri, $pos+strlen($v));
					}
				}
			}
		return $query;
	}
	/**
	 *
	 * route to controller.
	 *
	 * @return void
	 */
	function to(){
		$route=$this->getRoute();
		if(strpos($route, '/')!==false){
			$pieces=explode("/",substr($route, 1) );
		}
		$first=array_shift($pieces);
		if($first=='index.php')
			$controllerName=array_shift($pieces);
		else
			$controllerName=$first;
		$vars = array();
		if(!empty($controllerName)){
		$controllerName=mb_convert_case($controllerName, MB_CASE_TITLE, "UTF-8");
		$methodName=array_shift($pieces);
		while ($keys=array_shift($pieces)) {
					if(current($pieces)) $vars[$keys]=current($pieces);		
				}
		}else{
			$controllerName=Application::getDefaultController();	
		}
		$methodName = !empty($methodName) ? $methodName : $methodName="index";
		$cf=APPLICATION_ROOT.'/controllers/'.$controllerName.".php";
		if(file_exists($cf)){
			include $cf;
		}else{
			  header('HTTP/1.1 404 Not Found');
    		  header("status: 404 Not Found");
    		   exit;
		}
		
		$controller = new $controllerName();
		if(method_exists($controller, $methodName)){
			$controller->params=$vars;
			$controller->before();
			$controller->$methodName();
			$controller->after();
		}else{
			  header('HTTP/1.1 404 Not Found');
    		  header("status: 404 Not Found");
    		  exit;
		}
		
		
	}
}
?>