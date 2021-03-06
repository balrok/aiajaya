<?php
/**
 * example:
include "debug.php";
$configVarDumper['highlight'] = true;
$configVarDumper['printConstants'] = false;
$configVarDumper['printMethods'] = false;
$configVarDumper['depth'] = 5;
diedump($myVar);
 *
 */

/** configures enableDebug */
$debug_address = ''; // ip
$debug_useragent = ''; // http user-agent

$configVarDumper = array(
    'printMethods' => true,
    'printConstants' => true,
    'highlight' => true,
    'depth' => 3,
    'filterArray'=>false,
    'filterObject'=>false,
);

/**
 * checks by ip/useragent if debugging is enabled
 */
function enableDebug()
{
    global $debug_useragent;
    global $debug_address;

    if ($debug_useragent && isset($_SERVER['HTTP_USER_AGENT']))
    {
        if ($_SERVER['HTTP_USER_AGENT'] != $debug_useragent)
            return false;
        return true;
    }
    if ($debug_address && isset($_SERVER['REMOTE_ADDR']))
    {
        if ($_SERVER['REMOTE_ADDR'] != $debug_address)
            return false;
        return true;
    }
    if ($debug_address && !isset($_SERVER['HTTP_USER_AGENT']))
        return false;
    return true;
}

/**
 * an easy to read backtrace (uses debug_backtrace())
 * @todo longest common string should only go until / or \
 */
function callHistory()
{
    $arr = debug_backtrace();
    // find out which is the basic path each files are sharing
    // this is done to keep readability
    $base = false;
    foreach($arr as $v)
    {
        if (!isset($v['file']) || !$v['file'])
            continue;
        if ($base === false)
            $base = $v['file'];
        while (!startswith($base, $v['file']))
            $base = substr($base, 0, -1);
    }

    $ret = array(array('basepath'=>$base));
    foreach($arr as $v)
        if (isset($v['file']) && $v['file'])
            $ret[] = array('file'=>substr($v['file'], strlen($base)), 'line'=>$v['line']);
    return $ret;
}

/**
 * returns a short line where this function gets called
 * level determines how many levels it should go up (should be 1 if used directly)
 */
function whereCalled($level = 1)
{
    $trace = debug_backtrace();
    $file   = (isset($trace[$level]['file']))?$trace[$level]['file']:'';
    $line   = (isset($trace[$level]['line']))?$trace[$level]['line']:'';
    $object = (isset($trace[$level]['object']))?$trace[$level]['object']:'no Object';
    if (is_object($object)) { $object = get_class($object); }
    return "<b>Called in: line $line of $object \n(in $file):</b><br/>\n";
}

/**
 * stops the program and dumps the variable content, also tries to make it visible by ending output-buffer and some html-tags
 * second parameter is optional and can be used to increase depth
 */
function diedump($data, $depth=null)
{
    if (!enableDebug())
        return;
    while (@ob_end_clean ())
        continue;
    // if diedump is called inside a hidden div or script tag it won't be visible
    echo '<!-- --></div></div></script></script></script></div>';
    die(dump($data, $depth));
}
function diedumpc($data, $depth=null)
{
    if (!enableDebug())
        return;
    while (@ob_end_clean ())
        continue;
    die(dumpc($data, $depth));
}

/**
 * dumps the data in a nice and readable format
 * @param $depth how deep arrays/objects should be printed
 * @param $return if true it would only return a string, else output
 */
function dump($data, $depth=null, $return = false)
{
    global $configVarDumper;
    $d = new DebugVarDumper();
    foreach ($configVarDumper as $k=>$v)
        $d->$k = $v;
    if ($depth !==null)
        $d->depth = $depth;
    if (!enableDebug())
        return;
    $ret = whereCalled(2);
    $ret .= $d->dumpAsString($data);
    if ($return)
        return $ret;
    echo $ret;
}

function dumpc($data, $depth, $return=false)
{
    global $configVarDumper;
    $d = new DebugVarDumper();
    foreach ($configVarDumper as $k=>$v)
        $d->$k = $v;
    $d->highlight = false;
    if ($depth !==null)
        $d->depth = $depth;
    if (!enableDebug())
        return;
    $ret = whereCalled(2)."\n\n";
    $ret .= $d->dumpAsString($data);
    if ($return)
        return $ret;
    echo $ret;
}

/**
 * helper function which will look if a string starts with another string
 * similar to the python version
 * example: if (startswith('Apple', 'Appletree')) ...
 */
function startswith($needle, $haystack)
{
    if (!$haystack) return false;
    if (!$needle) return true;
    return strpos($haystack, $needle) === 0;
}

/**
 * A Helper class from yii (CVarDumper) for nicely displaying variables
 * extended to be a bit more helpful (arrays are easily copyable)
 */
class DebugVarDumper
{
    /** what to print for objects */
    public $printMethods = true;
    /** prints doc-comment for methods*/
    public $printMethodComment = true;
    public $printConstants = true;
    /** prints doc-comment for properties */
    public $printPropertyComment = true;
    /** highlight the output */
    public $highlight = true;
    /** how deep the nesting of arrays/objects should be printed */
    public $depth;
    // arrays after first level are only displayed shortly
    public $filterArray = false;
    // objects after first level are only displayed shortly
    public $filterObject = false;
    public $console = false;


    private $_objects;

    /**
     * Displays a variable.
     * This method achieves the similar functionality as var_dump and print_r
     * @param mixed $var variable to be dumped
     */
    public function dump($var)
    {
        echo $this->dumpAsString($var);
    }

    /**
     * Dumps a variable in terms of a string.
     * This method achieves the similar functionality as var_dump and print_r
     * @param mixed $var variable to be dumped
     * @return string the string representation of the variable
     */
    public function dumpAsString($var)
    {
        $this->_objects=array();
        $output = $this->dumpInternal($var,0);
        if($this->highlight)
        {
            $result = highlight_string("<?php\n".$output,true);
            $output=preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
        }
        else
        {
            $output .= "\n";
        }
        return $output;
    }

    /*
     * @param mixed $var variable to be dumped
     * @param integer $level depth level
     */
    private function dumpInternal($var, $level)
    {
        $output = '';
        switch(gettype($var))
        {
            case 'boolean':
                $output.=$var?'true':'false';
                break;
            case 'integer':
                $output.="$var";
                break;
            case 'double':
                $output.="$var";
                break;
            case 'string':
                $output.="'".addslashes($var)."'";
                break;
            case 'resource':
                $output.='{resource}';
                break;
            case 'NULL':
                $output.="null";
                break;
            case 'unknown type':
                $output.='{unknown}';
                break;
            case 'array':
                if (($this->filterArray && $level > 1) || $this->depth <= $level)
                    $output.='array(...)';
                else if(empty($var))
                    $output.='array()';
                else
                {
                    $keys=array_keys($var);
                    $spaces=str_repeat(' ',$level*4);
                    $output .="array(";
                    foreach($keys as $key)
                    {
                        $key2=str_replace("'","\\'",$key);
                        $output .="\n".$spaces."    '$key2' => ";
                        $output .=$this->dumpInternal($var[$key],$level+1);
                        $output .=',';
                    }
                    $output.="\n".$spaces.')';
                }
                break;
            case 'object':
                if(($id=array_search($var,$this->_objects,true))!==false)
                    $output .= get_class($var).'#'.($id+1).'(...)';
                else if (($this->filterObject && $level > 1) || $this->depth <= $level)
                    $output .= get_class($var).'(...)';
                else
                {
                    $id=array_push($this->_objects,$var);
                    $spaces=str_repeat(' ',$level*4);

                    if (class_exists('ReflectionClass', false))
                    {
                        $reflClass = new ReflectionClass($var);
                        $output.='class '.$reflClass->getName().'#'.$id;
                        $output.= ' - '.$reflClass->getFileName();
                        if ($reflClass->getParentClass())
                            $output .= "\n".$spaces.'    extends '.$reflClass->getParentClass()->getName().' // - '.$reflClass->getParentClass()->getFileName();
                        foreach ($reflClass->getInterfaces() as $int)
                            $output .= "\n".$spaces.'    implements '.$int->getName().' // - '.$int->getFileName();
                        $output .= "\n".$spaces.'{';
                        $level += 1;
                        $spaces=str_repeat(' ',$level*4);
                        // output all constants
                        if ($this->printConstants)
                            foreach ($reflClass->getConstants() as $key=>$value)
                                $output .= "\n".$spaces.$key.' = '.  $this->dumpInternal($value,$level+1) . ';';

                        // output all properties:
                        foreach ($reflClass->getProperties() as $prop)
                        {
                            $type = array();
                            if ($prop->getModifiers() & ReflectionProperty::IS_STATIC) $type[] = 'static';
                            if ($prop->getModifiers() & ReflectionProperty::IS_PUBLIC) $type[] = 'public';
                            if ($prop->getModifiers() & ReflectionProperty::IS_PROTECTED) $type[] = 'protected';
                            if ($prop->getModifiers() & ReflectionProperty::IS_PRIVATE) $type[] = 'private';
                            $type = implode(' ', $type);
                            // prop->setAccessible() exists only since 5.3 - as fallback just don't display private stuff
                            if (version_compare(PHP_VERSION, '5.3.0') >= 0 || $prop->isPublic())
                            {
                                if (version_compare(PHP_VERSION, '5.3.0') >= 0)
                                    $prop->setAccessible(true); // else we can't access private/protected
                                if ($this->printPropertyComment && $prop->getDocComment())
                                    $output .= "\n".$spaces.$prop->getDocComment();
                                $output .= "\n".$spaces.$type.' $'.$prop->getName().' = '. $this->dumpInternal($prop->getValue($var),$level+1) . ';';
                                if ($prop->getDeclaringClass() != $reflClass)
                                    $output .= '#'.$prop->getDeclaringClass()->getName();
                            }
                        }

                        // output all methods
                        if ($this->printMethods)
                        {
                            foreach ($reflClass->getMethods() as $meth)
                            {
                                $type = array();
                                if ($meth->getModifiers() & ReflectionMethod::IS_STATIC) $type[] = 'static';
                                if ($meth->getModifiers() & ReflectionMethod::IS_PUBLIC) $type[] = 'public';
                                if ($meth->getModifiers() & ReflectionMethod::IS_PROTECTED) $type[] = 'protected';
                                if ($meth->getModifiers() & ReflectionMethod::IS_PRIVATE) $type[] = 'private';
                                if ($meth->getModifiers() & ReflectionMethod::IS_ABSTRACT) $type[] = 'abstract';
                                if ($meth->getModifiers() & ReflectionMethod::IS_FINAL) $type[] = 'final';
                                if ($meth->isInternal()) $type[] = '/*internal*/';
                                $type = implode(' ', $type);

                                $parameters = array();
                                foreach ($meth->getParameters() as $param)
                                {
                                    $paramStr = '';
                                    if ($param->getClass())
                                        $paramStr .= $param->getClass()->name;
                                    if ($param->isPassedByReference())
                                        $paramStr .= '&';
                                    $paramStr .= '$'.$param->getName();
                                    if ($param->isOptional())
                                    {
                                        if ($meth->isInternal()) // you can't get the default for them :/
                                            $paramStr .= ' = "???"';
                                        else
                                            $paramStr .= ' = '.$this->dumpInternal($param->getDefaultValue(), $level+1);
                                    }
                                    $parameters[] = $paramStr;
                                }

                                if ($this->printMethodComment && $meth->getDocComment())
                                    $output .= "\n".$spaces.$meth->getDocComment();
                                $output .= "\n".$spaces.$type.' function '.$meth->getName().'('. implode(', ', $parameters).');';
                                if ($meth->getDeclaringClass() != $reflClass)
                                    $output .= '#'.$meth->getDeclaringClass()->getName();
                            }
                        }

                        $level -= 1;
                        $spaces=str_repeat(' ',$level*4);
                        $output.="\n".$spaces.'}';
                    }
                    else
                    {
                        $className=get_class($var);
                        $members=(array)$var;
                        $output.="class $className#$id\n".$spaces.'(';
                        foreach($members as $key=>$value)
                        {
                            $tmp = explode("\0", trim($key));
                            $type = 'public';
                            $varName = $tmp[0];
                            if (isset($tmp[1]))
                            {
                                if ($tmp[0] == '*')
                                    $type = 'protected';
                                else
                                {
                                    $output.="\n".$spaces."    // in class ".$tmp[0];
                                    $type = 'private';
                                }
                                $varName = $tmp[1];
                            }

                            $output.="\n".$spaces."    ".$type." $".$varName;
                            if (!is_null($value))
                            {
                                $output.=" = ".$this->dumpInternal($value,$level+1). ";";
                            }
                            else
                                $output.=";";

                        }
                        $output.="\n".$spaces.')';
                    }
                }
                break;
        }
        return $output;
    }
}


// disable caches for debugging
if (enableDebug())
{
    @ini_set('apc.cache_by_default', 0);
    @ini_set('opcache.validate_timestamps', 1);
}


function do_php_debug() {
	error_reporting(-1);

	ini_set('display_startup_errors',1);  
	ini_set('display_errors',1);
	ini_set('short_open_tag',1);
	ini_set('html_errors', 1);


	function ShutdownHandler()
	{
		if(@is_array($error = @error_get_last()))
		{
			return(@call_user_func_array('ErrorHandler', $error));
		};

		return(TRUE);
	};

	register_shutdown_function('ShutdownHandler');

	function ErrorHandler($type, $message, $file, $line)
	{
		$_ERRORS = Array(
			0x0001 => 'E_ERROR',
			0x0002 => 'E_WARNING',
			0x0004 => 'E_PARSE',
			0x0008 => 'E_NOTICE',
			0x0010 => 'E_CORE_ERROR',
			0x0020 => 'E_CORE_WARNING',
			0x0040 => 'E_COMPILE_ERROR',
			0x0080 => 'E_COMPILE_WARNING',
			0x0100 => 'E_USER_ERROR',
			0x0200 => 'E_USER_WARNING',
			0x0400 => 'E_USER_NOTICE',
			0x0800 => 'E_STRICT',
			0x1000 => 'E_RECOVERABLE_ERROR',
			0x2000 => 'E_DEPRECATED',
			0x4000 => 'E_USER_DEPRECATED'
		);

		if(!@is_string($name = @array_search($type, @array_flip($_ERRORS))))
		{
			$name = 'E_UNKNOWN';
		};

		return(print(@sprintf("%s Error in file %s at line %d: %s\n", $name, @basename($file), $line, $message)));
	};

	$old_error_handler = set_error_handler("ErrorHandler");

}
