<?php
namespace ling;
class Model{
	public $vars=array();
	public $extraParams=array();
	public function __construct($properties=null){
        if($properties!==null){
            foreach($properties as $k=>$v){
                if (in_array($k,$this->_fields)) 
           		$this->vars[$k] = $v;
            }
        }
    }
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

   public function __set($nm, $val)
   {
       if (in_array($nm,$this->_fields)) $this->vars[$nm] = $val;
   }

   public function __isset($nm)
   {
       return isset($this->vars[$nm]);
   }

   public function __unset($nm)
   {
       unset($this->vars[$nm]);
   }
   /*find (line 172)
Find a record. (Prepares and execute the SELECT statements)

return: A model object or associateve array of the queried result
access: public
mixed find ([array $opt = null])
array $opt: Associative array of options to generate the SELECT statement. Supported: where, limit, select, param, groupby, asc, desc, custom, asArray*/
 /*validate ([string $checkMode = 'all'], [string $requireMode = 'null']) array
 	public function lastInsertId(){}
 	public function errorCode(){}
	public function errorInfo(){}*/
	/*
	 *@return this model's
	*/
	public function description(){
		return (object)get_object_vars($this);
	}
	public function find(){
		$pkn=$this->_primaryKey;
		if(isset($this->vars[$pkn])){
			//return $this->db->find($this->description())->fetch(\PDO::FETCH_OBJ);
			return $this->db->find($this->description())->fetchObject();
		}else{
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_OBJ);
		}
	}
	public function findObjects(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_OBJ);
	}
	public function findArrays(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_ASSOC);
	}
	public function findColumn(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN,0);
	}
	public function findColumns(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN);
	}
	/**
	 *Note, that you can use PDO::FETCH_COLUMN|PDO::FETCH_GROUP pair only while selecting two columns, not like DB_common::getAssoc(), when grouping is set to true.
	 *@return Group values by the first column
	 */
	public function findGroup(){
			return $this->db->find($this->description())->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);
	}
	public function findObject(){
		$this->db->find($this->description())->fetchObject();
	}
	/**
	 *
	 *@param $opt model object ||
	 *  
	 */
	public function update($opt=null){
		return	$this->db->update($this->description(),$opt);
	}
	/**
	 * adds a new record and fetch result
	 * @return inter || false last insert id.
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
	 * if you specified primarykey eg. $mode->id ,this will only delete one record.
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @return object PDOStatement
	 */
	public function delete($opt=null){
		return	$this->db->delete($this->description(),$opt);
	}
	public function leftJoin($model){

	}
	/*validate (line 111)
Validate the Model with the rules defined in getVRules()
return: Return array of errors if exists. Return null if data passes the validation rules.
access: public
array validate ([string $checkMode = 'all'], [string $requireMode = 'null'])
string $checkMode: Validation mode. all, all_one, skip
string $requireMode: Require Check Mode. null, nullempty*/
	public function validate(){}
	public function count($opt=null){
			return $this->db->count($this->description(),$opt)->fetchColumn();
	}
	//get_by_city
	public function getBy($k,$v){
		$this->vars[$k] = $v;
		return $this->find();
	}
	public function deleteBy($k,$v){
		$this->vars[$k] = $v;
		return $this->delete();
	}
	public function countBy($k,$v){
		$this->vars[$k] = $v;
		return $this->count();
	}
	public function updateBy($k,$v){
		$newk='c'.$k;
		$this->extraParams[$newk] = $v;
		$opt=array('kv'=>true,'where'=>"$k=:$newk");
		return	$this->db->update($this->description(),$opt);
	}
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