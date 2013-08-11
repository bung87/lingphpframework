<?php
/**
 * Class ling\Application
 *
 * @author     bung <zh.bung@gmail.com>
 * @copyright  Copyright © 2013 bung.
 * @license    New BSD License
 */

namespace ling;

define('FRAMEWORK_ROOT', str_replace('\\', '/', __DIR__));

/**
 *
 * class Application
 *
 *
 */
class Application{
	/**
	 * DB instance
	 * @var ling\DB
	 */
	protected static $db;
	/**
	 * DSN config
	 * @var array
	 */
	protected static $dbconf;
	/**
	 * default controller name.
	 * @var string
	 */
	protected static $defaultController;
	/**
	 * table prefix e.g wp_
	 * @var string
	 */
	public static $tablePrefix;
	/**
	 * this application all models name,use ling\ModelGenerater for generating.
	 * location in application root /models.php
	 * @var array
	 */
	public static $modelsName=array();
	/**
	 * 
	 * initialize application.apply models name to $modelName,registe autoload function.
	 *
	 */
	function __construct(){
		self::$modelsName=require_once(APPLICATION_ROOT."/models.php");
		spl_autoload_extensions(".php");
		spl_autoload_register(__CLASS__."::load");
	}
	/**
	 * 
	 * run application.
	 *
	 * @return void
	 */
	public function run(){
		$router = new Router();
		$router->to();
	}
	/**
	 * 
	 * set the database info before application run.
	 *
	 * @param array database settings.
	 * @return void
	 */
	public static function setDB($conf){
		self::$dbconf=$conf;
		self::$tablePrefix=$conf['tablePrefix'];
	}
	/**
	 *
	 * set the default controller name
	 *
	 * @param string 
	 * @return void
	 */
	public static function setDefaultController($dfc){
		self::$defaultController=$dfc;
	}
	/**
	 * @return string the name of default controller 
	 */
	public static function getDefaultController(){
		return self::$defaultController;
	}
	/**
	 * @return object an instance of DB
	 */
	public static function getDB(){
		if(self::$db==null){
			self::$db=new DB(self::$dbconf);
		}
		return self::$db;
	}
	/**
	 * @return string default tableprefix value.
	 */
	public static function tablePrefix(){
		return self::$tablePrefix;
	}
	/**
	 *
	 * for spl_autoload_register use.
	 *
	 * if the class name contains this framework namespace will load from framework root diretory,
	 * if the class name can be found in APPLICATION_ROOT.'/'.models.php will load from models,
	 * otherwise do nothing.
	 *
	 * @param string $classname 
	 * @return void
	 */
	public static function load($classname){
		if(strpos($classname, __namespace__)===0){
			$classname=str_replace("\\", "/", $classname);
			$classname= substr($classname, strlen(__namespace__));  
			require_once (FRAMEWORK_ROOT.$classname . ".php");	
		}
		if(in_array($classname, self::$modelsName)){
			require_once (APPLICATION_ROOT.'/models/'.$classname . ".php");	
		}
	}

}

?>