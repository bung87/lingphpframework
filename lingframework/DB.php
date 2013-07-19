<?php
namespace ling;
class DB {
	public $pdo;
	public static $conf;
	function __construct($conf){
		self::$conf=$conf;
		$attr=array();
		if(isset($conf['persistent']) && !empty($conf['persistent']))
			$attr=array(\PDO::ATTR_PERSISTENT => true);
		$this->pdo = new \PDO($conf['driver'].
			':host='.$conf['host'].
			';dbname='.$conf['database'],
			 $conf['user'],
			  $conf['password'],
			  $attr
		//a bug of PHP5.3.see here http://stackoverflow.com/questions/2424343/undefined-class-constant-mysql-attr-init-command-with-pdo
		//array(\PDO::MYSQL_ATTR_INIT_COMMAND , "SET NAMES '".$conf['collate']."';")
		);
		if(isset($conf['collate']) && !empty($conf['collate'])) $this->pdo->query("SET NAMES ".$conf['collate']);
	}
	/**
	 *	
	 * builds sql,prepares,bind each params to PDO statement and execute.
	 *
	 * builds sql statement based on $model,$opt,$struct and $filter,prepares the sql statement,
	 * bind each model's params to PDO statment and execute.
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @param array $struct sql statement struct
	 * @return object PDOStatement
	 */
	public  function pbe($model,$opt,$struct,$filter)
	{
		$builder= new QueryBuilder($model,$opt,$struct,$filter);
		$smt = $this->prepare($builder->getQuery());
		array_walk($model->vars, array($this,'bindParams'),$smt);
		if(!empty($model->extraParams)){
			array_walk($model->extraParams, array($this,'bindParams'),$smt);
		}
		$smt->execute();
		return $smt;
	}
	/**
	 *
	 * Update an existing record or records that satisfy the conditions.
	 *
	 * if you specified primarykey eg. $mode->id ,this will only update one record.
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @return object PDOStatement
	 */
	public function update($model,$opt=array()){
		$struct = array('update'=>true, 
			'low_priority'=>false,
			'ignore'=>false,
			'tbl_name'=>'',
			'kv'=>'',
			'where'=>'',
			'order by'=>'',
			'limit'=>''
			);
		$filter=array('update','tbl_name');
		return $this->pbe($model,$opt,$struct,$filter);
	}

	/**
	 *
	 * Detecte data type for the parameter using the PDO::PARAM_* constants.
	 *
	 * @param string $value 
	 * @return integer $type
	 */
	public function dataType($value){
		if(is_int($value))
                    $type = \PDO::PARAM_INT;
                elseif(is_bool($value))
                    $type = \PDO::PARAM_BOOL;
                elseif(is_null($value))
                    $type = \PDO::PARAM_NULL;
                elseif(is_string($value))
                    $type = \PDO::PARAM_STR;
                else
                    $type = FALSE;              
               return $type;
	}
	/**
	 *
	 * Binds a PHP variable to a corresponding named omark placeholder in the SQL statement.
	 *
	 * @param string $value 
	 * @return integer $type
	 */
	public function bindParams($v,$k,&$smt){
		$smt->bindParam(":$k",$v,$this->dataType($v));
	}
	/**
	 *
	 * Find an existing record or records that satisfy the conditions.
	 *
	 * if you specified primarykey eg. $mode->id ,this will only return one record as object
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @return object PDOStatement
	 */
	public function find($model,$opt=array()){
		$struct = array('select'=>'*',
			'from'=>true,
			'tbl_name'=>'',
			'where'=>'',
			'order by'=>'',
			'limit'=>''
			);
		$filter=array('select','tbl_name');
		return $this->pbe($model,$opt,$struct,$filter);
	}
	/**
	 *
	 * Count number of records that satisfy the conditions.
	 *
	 * @param object $model 
	 * @param array $opt 
	 * @return object PDOStatement
	 */
	public function count($model,$opt=array()){
		$struct = array('select'=>'COUNT(*)',
			'from'=>true,
			'tbl_name'=>'',
			'where'=>'',
			'order by'=>'',
			'limit'=>''
		);
		$filter=array('select','tbl_name');
		return $this->pbe($model,$opt,$struct,$filter);
	}
	/**
	 *
	 * Adds a new record.
	 *
	 * @param object $model 
	 * @return object PDOStatement
	 */
	public function insert($model){
		$struct = array('insert into'=>true, 
			'tbl_name'=>'',
			'kv'=>'',
			'where'=>'',
			'order by'=>'',
			'limit'=>''
			);
		$filter=array('insert into','tbl_name');
	return $this->pbe($model,null,$struct,$filter);
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
	public function delete($model,$opt=array()){
		$struct = array('delete'=>true, 
			'from'=>true,
			'tbl_name'=>'',
			'where'=>'',
			'order by'=>'',
			'limit'=>''
			);
		$filter=array('delete','tbl_name');
	return $this->pbe($model,$opt,$struct,$filter);
	}

	/**
	 *
	 * Interacting with methods that have not been declared or are not visible in the current scope
	 *
	 * @param string $name method name of PDO
	 * @param array $arguments arguments apply to $this->pdo .
	 */
	function __call($name,$arguments) {
    	return call_user_func_array(array(&$this->pdo,$name),$arguments);
	}

}

?>