<?php
namespace ling;
class Router{
	public $request_uri;
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
		include APPLICATION_ROOT.'/controllers/'.$controllerName.".php";
		$controller = new $controllerName();
		$controller->params=$vars;
		$controller->before();
		$controller->$methodName();
		$controller->after();
	}
}
?>