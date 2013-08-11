<?php

class Helper extends ling\Controller{
	public function index(){

		echo 'this is helper index';
	}
	public function modelGen(){
    	$tablePrefix=ling\Application::$tablePrefix;
    	$smt=$this->db->prepare("SHOW TABLES");
		$smt->execute();
		$tables = $smt->fetchAll(\PDO::FETCH_ASSOC);
		$clsExtendedNum = 0;
		$models=array();
		foreach( $tables as $tbl ) {
			$partten="/^".$tablePrefix."/";
			$tableName=current($tbl);
			if(!preg_match($partten,$tableName))
			continue;
			$smt2 = $this->db->query("DESC `$tableName`");
			$fields = $smt2->fetchAll(\PDO::FETCH_OBJ);
			$m=new ling\ModelGenerator($tableName,$fields);
			if(!is_dir(APPLICATION_ROOT."/models")){
				mkdir(APPLICATION_ROOT."/models",0755);
			}
			$modelName=$m->modelName();
			array_push($models, $modelName);
			file_put_contents(APPLICATION_ROOT."/models/".$modelName.".php",$m->export());
   		}
   		$ct="<?php\n return ".var_export($models,true).";\n ?>";
   		file_put_contents(APPLICATION_ROOT."/models.php",$ct);
	}
}

?>