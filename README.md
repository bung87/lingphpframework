lingphpframework
================
一个仍构思中的php framewrok。
##特性

*  基于MVC设计模式
*  支持ORM（部分，模型关联没想好，也不怎么想弄）
*  简洁的URL和极少的命名规则
*  数据模型自动生成
*  极少的配置  

##TODO
*  数据验证
*  增强异常处理机制
*  Request/Respone Content-Type 映射
*  抽象数据库操作

##Code
一个通常的入口文件：
    <?php
namespace ling;
$dbconf = include "./db.conf.php";
include "./lingframework/Application.php";
define('APPLICATION_ROOT', str_replace('\\', '/', __DIR__));
$app=new Application();
$app::setDB($dbconf);
$app::setDefaultController("Aa");
$app->run();
//echo  $_SERVER['REQUEST_URI'];
?>

一个通常的Controller:
"""http://localhost/helloworld/getkey/getvalue/

    <?php
class Helloworld extends ling\Controller{
	public $params=array();
    public function index(){
    	var_dump($this->controllerName);
    	var_dump($this->methodName);
    	 var_dump($this->params);
    	$this->redirect('myframework/aa/cc');
    }
    public function before(){
    // UAC
    }
    public function after(){
   // log
    }
    ?>
