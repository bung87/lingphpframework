<?php
namespace ling;

class Debuger{
	function __construct(){
		ini_set('html_errors', 0);
		set_error_handler(array($this,'errorHandler'));
		set_exception_handler(array($this,'exceptionHandler'));
		register_shutdown_function(array($this,'shutdown'));
	}

function exceptionHandler($e){
	if($e instanceof \PDOException){
		$this->errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
	}
	if($e instanceof \SmartyCompilerException){
		$this->errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
	}
	
	if(isset($e->trace)){
		$trace=$e->trace;
		if(is_array($trace)){
				foreach ($trace as $k => $v) {
			$this->errorHandler($v['type'], $v['message'], $v['file'], $v['line']);
		}
	}
	
		
	}

	
}

public static function getTrimedFilePath($errfile){
	$orignalerrfile=str_replace('\\', '/', $errfile);
	$errfile=str_replace(APPLICATION_ROOT,"",$orignalerrfile);
	
	return $errfile;
}
public static function getFakeFrameWorkNamespace($func){
	$ns=__namespace__;
	$asterisk="";
	$asterisks=str_pad($asterisk, strlen($ns),'*');
	$fakeFrameWorkNamespace= preg_replace("|^/?$ns|", $asterisks, $func,1);
	return $fakeFrameWorkNamespace;
}
function errorHandler($errno, $errstr, $errfile, $errline, $errcontext=null){
	ob_clean();
	$orignalerrfile=str_replace('\\', '/', $errfile);

	$trimedFilePath= $this->getTrimedFilePath($orignalerrfile);
	$code=file($orignalerrfile);
	$line=$errline;
	$startLine=$line-5;
	$endLine=$line+5;

	echo '<html><head><link href="http://localhost/lingphpframework/google-code-prettify/prettify.css" type="text/css" rel="stylesheet" /><script src="http://localhost/lingphpframework/google-code-prettify/prettify.js"></script></head>';
	echo "<body style=\"color:#fff;background:#000\">";
	// echo getcwd();
	 switch ($errno) {
    case E_ERROR:
    	 // echo "<b>My E_ERROR</b> [$errno] $errstr<br />\n";
    	
        break;

    case E_WARNING:
        // echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_NOTICE:
        // echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        // echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }
  
     echo "<b>Fatal error</b> [$errno] $errstr\n";
     echo "   on line $errline in file $trimedFilePath";
     $st=$startLine;
	 echo "<?prettify linenums=$st?><pre class=\"prettyprint linenums\">";
	    	for ($i=$startLine; $i < $endLine; $i++) { 
		$index=$i-1;
		if (isset($code[$index]))
		echo htmlspecialchars($code[$index]);
	}
	 // echo "</pre><br /> PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
     echo "</pre>";
     $this->errorBacktrace();
     echo "</body><script>prettyPrint();</script></html>";
	

	}
	function shutdown(){
    $isError = false;
    if ($error = error_get_last()){
        switch($error['type']){
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $isError = true;
                break;
        }
    }
    if($isError){
		//print_r($error);exit;
		$this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}
function errorBacktrace() {
    $trace = array_reverse(debug_backtrace());
    echo '<ol>';
    $debugerMethod=get_class_methods($this);
    foreach($trace as $item){
    	if(in_array($item['function'], $debugerMethod)) continue;
        echo '<li><span style="color:#3A66CC">' . (isset($item['file']) ? self::getTrimedFilePath($item['file']) : '<unknown file>') . '</span><strong style="color:#DD0000">(' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ')</strong> calling <span style="color:#0000BB">' .self::getFakeFrameWorkNamespace($item['function'])  . '()</span></li>';
     }
    echo '</ol>';    
}
}
?>