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
class Router {
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
		if(false!==strpos($this->request_uri,'?')){
			$pureUrl=substr($this->request_uri,0,strpos($this->request_uri, '?'));
		}else{
			$pureUrl=$this->request_uri;
		}
		
		$query="";
		$pieces=explode("/", APPLICATION_ROOT);
		foreach ($pieces as $k => $v) {
				if($v!=""){
					$pos=strpos($pureUrl, $v );
					if($pos!==false ){
						$query = substr($pureUrl, $pos+strlen($v));
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
			$methodName='index';
		}
		
		$cf=APPLICATION_ROOT.'/controllers/'.$controllerName.".php";
		if(file_exists($cf)){
			include $cf;
		}else{
			  header('HTTP/1.1 404 Not Found');
    		  header("status: 404 Not Found");
    		   exit;
		}
		// $methodName = !empty($methodName) ? $methodName : $methodName="index";
		$controller = new $controllerName();

		/*if $methodName not empty follow these rule below
		if $methodName method exists call it directly
		if index method exists call index method and use the $methodName as first param.
		otherwise return 404*/
		if(!empty($methodName)){
			if( method_exists($controller, $methodName)){
				$class_methods = get_class_methods('ling\Controller');
				if(in_array($methodName, $class_methods)){
					 exit ('can not execute '.$controllerName.':'.$methodName.' directly.');
				}
			
			}else{
				if(method_exists($controller, 'index')){
					$arg=$methodName;
					$methodName='index';
					$vars[0]=$arg;
					
				}else{
				  header('HTTP/1.1 404 Not Found');
	    		  header("status: 404 Not Found");
	    		  exit;
				}
			  
			}
		}else{
			header('HTTP/1.1 404 Not Found');
	    	header("status: 404 Not Found");
	    	exit;
		}
	
		$controller->params=array_merge($vars,$_GET);
		$_GET=&$controller->params;//retrieve $_GET variables
		$controller->before();
		$controller->$methodName();
		$controller->after();
		
		
	}

}

?>