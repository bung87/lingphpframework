<?php
class API extends ling\Controller{
    public function index(){
    	echo "this is api page";
    }
    public function find(){
    	var_dump($this->params);
    }
    public function convertCase($str){
		return ucwords(strtolower($str)); 
	}
    function __call($name,$arguments) {
    	$params=$this->params;
    	$keys=array_keys($params);
    	$method=array_shift($keys);
    	$model=$this->convertCase($name);
    	$m = new $model($params);
    	$res=$m->$method();
    	if($res)
    	echo json_encode($res);
	}
}
?>