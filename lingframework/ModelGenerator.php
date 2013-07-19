<?php
namespace ling;
class ModelGenerator{
	public $_fields=array();
	public $_tableName;
	public $_tablePrefix;
	public $_primaryKey;
	public $_rules=array();
	function __construct($tbn,$fields){
		$this->_tableName=$tbn;
		$this->setTablePrefix($this->getTablePrefix());
		$mRules=array();
		foreach ($fields as $k => $v) {
			extract(get_object_vars($v));
			/*
			[Field] => id
            [Type] => bigint(20) unsigned
            [Null] => NO
            [Key] => PRI
            [Default] => 
            [Extra] => auto_increment*/
            if($Key=="PRI")	$this->_primaryKey=$Field;
			//array_push($this->_fields, $Field);
			$field_key=$this->convertCase($Field);
            $this->_fields[$field_key]=$Field;
			$rule=array(array($Type),array($Null),array($Key),array($Default),array($Extra));
			array_push($mRules, $rule);
		}
		//$this->_rules=array_combine($this->_fields,$mRules);
	}
	/**
	 *
	 * Uppercase the first character of each word in a string
	 *
	 * @param string $str 
	 * @return string 
	 */
	public function convertCase($str){
		//mb_convert_case($mn,MB_CASE_TITLE,'UTF-8');
		return ucwords(strtolower($str)); 
	}
	/**
	 *
	 * generate a model as php script content.
	 *
	 * @param string $str 
	 * @return string 
	 */
	public function export(){
		$start="<?php\r\nclass ".$this->modelName()." extends ling\Model{\r\n";
		$end="\r\n}\r\n?>";
		$properties=array();
		$vars = get_object_vars ($this);
		foreach ($vars as $k => $v) {
			$pro='public $'.$k.' = '.var_export($v,true).";\r\n";
		 	array_push($properties, $pro);
		}
		$propertiesStr=implode("", $properties);
		return implode("", array($start,$propertiesStr,$end));
	}
	/**
	 *
	 * set the tableprefix.
	 *
	 * @param string $tbp tableprefix
	 * @return void 
	 */
	public function setTablePrefix($tbp){
		$this->_tablePrefix=$tbp;
	}
	/**
	 *
	 * get the tableprefix.
	 *
	 * @return string  
	 */
	public function getTablePrefix(){
		return empty($this->_tablePrefix) ? Application::$tablePrefix : $this->_tablePrefix;
	}
	/**
	 *
	 * generate the name of model.
	 *
	 * @return string converted name
	 */
	public function modelName(){
		$tbp=$this->getTablePrefix();
		if(!empty($tbp)){
			$partten="/^".$tbp."/";
			$mn=preg_replace($partten,"",$this->_tableName);	
		}else{
			$mn=$this->_tableName;
		}
		return $this->convertCase($mn);
	}

}
?>