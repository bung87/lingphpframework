<?php
/**
 * Class ling\QueryBuilder
 *
 * @author     bung <zh.bung@gmail.com>
 * @copyright  Copyright © 2013 bung.
 * @license    New BSD License
 */
 
namespace ling;

/**
 *
 * generate a sql query string.
 *
 */
class QueryBuilder{
	/**
     * the query string
     * @var string
     */
	public $query;
	/**
	 *
	 * @param array &$model 
	 * @param array $opt 
	 * @param array $struct 
	 * @param array $filter keys not allowed in $opt 
	 */
	public function __construct(&$model,$opt=array(),$struct,$filter){
		$filteredOpt=$this->optionFilter($opt,$filter,$struct);
		$fullstruct=$this->buildStruct($model,$struct,$filteredOpt);
		$this->query=$this->build($fullstruct);
	}
	/**
	 *
	 * @return string $this->query
	 */
	public function getQuery(){
		return $this->query;
	}
	/**
	 * @param array &$model 
	 * @param array $struct 
	 * @param array $filteredOpt filtered $opt 
	 * @return array $fullstruct full struct of query
	 */
	public function buildStruct(&$model,$struct,$filteredOpt){
		$pkn=$model->_primaryKey;
		$pk= isset($model->vars[$pkn]) ? $model->vars[$pkn] : '';
		if(!empty($pk)){ 
			$filteredOpt['where']=$this->kvmapper($pkn);
			if (isset($struct['select'])) {
				//如果select语句主键有值则将$model->vars设置只有主键一个元素
				$model->vars = array($pkn => $pk );
			}
		}else{
			if (isset($filteredOpt['kv'])){
				if( isset($struct['insert into'])){
					$filteredOpt['kv']=$this->generateKV($model->vars,$pkn);
				}elseif (isset($struct['update'])) {
					$map = array_map(array($this,'kvmapper'), array_keys($model->vars));
					$vals=implode(',',$map);
					$filteredOpt['kv']="SET ".$vals;
				}
			}else{
				$map = array_map(array($this,'kvmapper'), array_keys($model->vars));
				$where=implode(' AND ',$map);
				$filteredOpt['where']=$where;
			}
		}
		$filteredOpt['tbl_name']=$model->_tableName;
		$fullstruct=array_merge($struct,$filteredOpt);
		return $fullstruct;
	}
	/**
	 * @param array $fullstruct full struct of query
	 * @return string final query string
	 */
	public function build($fullstruct){
			foreach ($fullstruct as $k => $v) {
			if(is_bool($v)) {
				if($v===false)
					unset($fullstruct[$k]);
				else
					$fullstruct[$k]=strtoupper($k);		
			}else{
				if(!empty($v) && in_array($k, array('select','where','order by','limit')))
				$fullstruct[$k]=strtoupper($k)." ".$v;
				
			}
				//删除多余的元素
			if(empty($v)) unset($fullstruct[$k]);
				
		}
		return (implode(" ", $fullstruct));
	}
	/**
	 *
	 * @param array $opt 
	 * @param array $filter keys not allowed in $opt 
	 * @param array $struct
	 * @return array filtered $opt || $struct
	 */
	public function optionFilter($opt,$filters,$struct){
		if(!empty($opt)){	
			foreach ($opt as $k => $v) {
				if(!in_array($k, $struct))
					unset($opt[$k]);
				if(in_array($k, $filters))
					unset($opt[$k]);
			return $opt;
			}
		}else{
			return $struct;
		}
	}
	/**
	 *
	 * @param string $k field name
	 * @return string "$fieldName=:$fieldName"
	 */
	public function kvmapper($k){
		return ("$k=:$k");
	}
	/**
	 *
	 * @param string $k field value
	 * @return string ":$fieldName"
	 */
	public function vmapper($k){
		return (":$k");
	}
	/**
	 * generate key-value pairs for adding new recored use.
	 * @param array $vars
	 * @param string $pkn primarykey name
	 * @return string "($fieldName1,$fieldName2,...) VALUES($fieldValue1,$fieldValue2,...)"
	 */
	public function generateKV($vars,$pkn=null){
		if (!empty($pkn)) unset($vars[$pkn]);
			$keys=implode(',',array_keys($vars));
			$keys="($keys)";
			$map = array_map(array($this,'vmapper'), array_keys($vars));
			$values="(".implode(',',$map).")";
			return $keys." VALUES".$values;
	}
}
?>