<?php
/**
 * Class ling\Model
 *
 * @author     bung <zh.bung@gmail.com>
 * @copyright  Copyright © 2013 bung.
 * @license    New BSD License
 */
 
namespace ling;

/**
 *
 * base Model.
 *
 */
class Model{
	/**
     * Associative array of the model properies.
     * @var array
     */
	public $vars=array();
	/**
     * Associative array of the model properies.
     * @var array
     */
	public $extraParams=array();
	/**
     * initialze the model properies.
     * @param array Associative array
     */
	public function __construct($properties=null){
        if($properties!==null){
            foreach($properties as $k=>$v){
                if (in_array($k,$this->_fields)) 
           		$this->vars[$k] = $v;
            }
        }
    }
    /**
     * quick access DB instance,$this->vars 
     * @param string model property
     */
	public function __get($nm)
   {
		if ($nm=='db'){return Application::getDB();}
    	if (isset($this->vars[$nm])) {
           $r = $this->vars[$nm];
           return $r;
    	} else {
       	return null;
    	}
   }
    /**
     * Check if the model related table has that field,and set to $this->vars[$nm]
     * @param string $nm field name
     * @param mixed $val field value
     */
   public function __set($nm, $val)
   {
       if (in_array($nm,$this->_fields)) $this->vars[$nm] = $val;
   }
    /**
     * Check if $this->vars[$field] has been set
     * @param string $nm field name
     */
   public function __isset($nm)
   {
       return isset($this->vars[$nm]);
   }
    /**
     * Unset $this->vars[$field]
     * @param string $nm field name
     */
   public function __unset($nm)
   {
       unset($this->vars[$nm]);
   }

	/**
	 *@return array model properties
	 */
	public function description(){
		return (object)get_object_vars($this);
	}
	/**
	 *
	 * find an existing record or records that satisfy the conditions.
	 *
	 * if you specified primarykey e.g. $mode->id ,this will only find one record as object.
	 *
	 * @return object || array
	 */
	public function find(){
		$pkn=$this->_primaryKey;
		if(isset($this->vars[$pkn])){
			//return $this->db->find($this->description())->fetch(\PDO::FETCH_OBJ);
			return $this->db->find($this->description())->fetchObject();
		}else{
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_OBJ);
		}
	}

	/**
	 *
	 * find an existing record or records that satisfy the conditions.
	 *
	 * if you specified primarykey e.g. $mode->id ,this will only find one record as object.
	 *
	 * @return object || array
	 */
	public function findObjects(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_OBJ);
	}
	/**
	 *
	 * find records as array that satisfy the conditions.
	 *
	 * @return array
	 */
	public function findArrays(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_ASSOC);
	}
	/**
	 *
	 * find first column that satisfy the conditions.
	 *
	 * @return array
	 */
	public function findColumn(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN,0);
	}
	/**
	 *
	 * find all column that satisfy the conditions.
	 *
	 * @return array
	 */
	public function findColumns(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN);
	}
	/**
	 *Note, that you can use PDO::FETCH_COLUMN|PDO::FETCH_GROUP pair only while selecting two columns, not like DB_common::getAssoc(), when grouping is set to true.
	 *@return array Group values by the first column
	 */
	public function findGroup(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);
	}
	/**
	 *
	 * find an existing record as object
	 *
	 * @return object
	 */
	public function findObject(){
		$this->db->find($this->description())->fetchObject();
	}
	/**
	 *
	 * @param array $opt
	 *
	 */
	public function update($opt=null){
		return	$this->db->update($this->description(),$opt);
	}
	/**
	 * adds a new record and fetch result
	 * @return int || false last insert id.
	 */
	public function insert(){
		$result = $this->db->insert($this->description())->fetch(PDO::FETCH_ASSOC);
		$pk=$this->_primaryKey;
		return ( $result ) ? $result[$pk] : false;
	}
	/**
	 *
	 * Delete an existing record or records that satisfy the conditions.
	 *
	 * if you specified primarykey e.g. $mode->id ,this will only delete one record.
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @return object PDOStatement
	 */
	public function delete($opt=null){
		return	$this->db->delete($this->description(),$opt);
	}
	/**
	 * @todo 
	 *
	 */
	public function validate(){}
	public function count($opt=null){
			return $this->db->count($this->description(),$opt)->fetchColumn();
	}
	/**
	 * 
	 * @param string $k ucwords(strtolower($fieldName));
	 * @param mixed $v field value
	 * @return see find().
	 */
	public function getBy($k,$v){
		$this->vars[$k] = $v;
		return $this->find();
	}
	/**
	 * 
	 * @param string $k ucwords(strtolower($fieldName));
	 * @param mixed $v field value
	 * @return see delete().
	 */
	public function deleteBy($k,$v){
		$this->vars[$k] = $v;
		return $this->delete();
	}
	/**
	 * 
	 * @param string $k ucwords(strtolower($fieldName));
	 * @param mixed $v field value
	 * @return see count().
	 */
	public function countBy($k,$v){
		$this->vars[$k] = $v;
		return $this->count();
	}
	/**
	 * 
	 * @param string $k ucwords(strtolower($fieldName));
	 * @param mixed $v field value
	 * @return see update().
	 */
	public function updateBy($k,$v){
		$newk='c'.$k;
		$this->extraParams[$newk] = $v;
		$opt=array('kv'=>true,'where'=>"$k=:$newk");
		return	$this->db->update($this->description(),$opt);
	}
	/**
	 * 
	 * @param string $name ucwords(strtolower($fieldName));
	 * @param array $arguments field value
	 * @return see getBy(),updateBy(),countBy()
	 */
	function __call($name,$arguments) {
		$methodPre='';
		if(strpos($name,'getBy') !==false){
			$methodPre='getBy';
		}elseif (strpos($name,'updateBy') !==false) {
			$methodPre='updateBy';
		}elseif (strpos($name,'deleteBy') !==false) {
			$methodPre='deleteBy';
		}elseif (strpos($name,'countBy') !==false) {
			$methodPre='countBy';
		}
		if(!empty($methodPre)){
			$upperKey=preg_replace("/^$methodPre/", "", $name);
			$k=$this->_fields[$upperKey];
			return call_user_func_array(array($this,$methodPre),array($k,current($arguments)));		
		}
		return false;
	}
	/*
	public static function __callStatic($name, $args){}*/
}

?>