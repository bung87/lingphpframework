<?php
namespace ling;
class QueryBuilder{
	public $query;
	public function __construct(&$model,$opt=array(),$struct,$filter){
		$filteredOpt=$this->optionFilter($opt,$filter,$struct);
		$fullstruct=$this->buildStruct($model,$struct,$filteredOpt);
		$this->query=$this->build($fullstruct);
	}
	public function getQuery(){
		return $this->query;
	}
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
	public function kvmapper($k){
		return ("$k=:$k");
	}
	public function vmapper($k){
		return (":$k");
	}
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