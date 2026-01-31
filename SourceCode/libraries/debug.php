<?php

declare(strict_types=1);

/////////////////////////////////////////////////////////////////////////////
// Defines
/////////////////////////////////////////////////////////////////////////////
defined('E_NONE') OR define('E_NONE', 0);
defined('E_DEBUG') OR define('E_DEBUG', 4);
defined('E_WARNING') OR define('E_WARNING', 8);

/**
 * Dumps a variable for debugging purposes
 *
 * @param string $name The name/label for the variable being dumped
 * @param mixed $variable The variable to dump
 * @param bool $htmlcode Whether to wrap output in HTML pre/code/xmp tags
 * @return void
 */
function DebugDump($name, $variable, $htmlcode = false)
{
	global $debug;

	if ($debug == true)
	{
		echo "$name: ";

		if ($htmlcode == true)
		{
			echo "<pre><code><xmp>";
		}
		htmlentities(var_dump($variable));

		if ($htmlcode == true)
		{
			echo "</xmp></code></pre>";
		}
		else
		{
			echo '<br>' . PHP_EOL;
		}
	}
}

/**
 * Echoes a variable/value for debugging purposes
 *
 * @param string $variable The variable name or message to echo
 * @param mixed|null $value Optional value to display alongside the variable name
 * @return void
 */
function DebugEcho($variable, $value = null)
{
	global $debug;

	if ($debug == true)
	{
		if (!is_null($value))
		{
			echo "$variable: $value<br />\r\n";
		}
		else
		{
			echo "$variable<br />\r\n";
		}
	}
}

/**
 * Prints a statement for debugging purposes
 *
 * @param mixed $Statement The statement to print
 * @return void
 */
function DebugPrint( $Statement )
{
	global $g_Debug;

	if (TRUE == $g_Debug)
	{
		print_r($Statement);
		echo "<br />";
	}
}

/**
 * Displays a variable for debugging purposes
 *
 * @param string $variable The variable name to display
 * @param mixed|null $value Optional value to display alongside the variable name
 * @return void
 */
function DebugVar($variable, $value = null)
{
	global $debug;

	if ($debug == true)
	{
		if (!is_null($value))
		{
			echo "$variable: $value<br />\r\n";
		}
		else
		{
			echo "<span style=\"color: red; \">$variable</span><br />\r\n";
		}
	}
}

/**
 * Enables error reporting and display
 *
 * @return void
 */
function SetErrorReportingOn()
{
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

/**
 * Recursively prints data structure with level control
 *
 * @param mixed $data The data to print
 * @param int $level Maximum recursion level (default: 5)
 * @return string The formatted output string
 */
function print_r_level($data, $level = 5)
{
	static $innerLevel = 1;

	static $tabLevel = 1;

	static $cache = array();

	$self = __FUNCTION__;

	$type       = gettype($data);
	$tabs       = str_repeat('    ', $tabLevel);
	$quoteTabes = str_repeat('    ', $tabLevel - 1);

	$recrusiveType = array('object', 'array');

	// Recrusive
	if (in_array($type, $recrusiveType))
	{
		// If type is object, try to get properties by Reflection.
		if ($type == 'object')
		{
			if (in_array($data, $cache))
			{
				return "\n{$quoteTabes}*RECURSION*\n";
			}

			// Cache the data
			$cache[] = $data;
			$output     = get_class($data) . ' ' . ucfirst($type);
			$ref        = new \ReflectionObject($data);
			$properties = $ref->getProperties();

			$elements = array();

			foreach ($properties as $property)
			{
				$property->setAccessible(true);

				$pType = $property->getName();

				if ($property->isProtected())
				{
					$pType .= ":protected";
				}
				elseif ($property->isPrivate())
				{
					$pType .= ":" . $property->class . ":private";
				}

				if ($property->isStatic())
				{
					$pType .= ":static";
				}

				$elements[$pType] = $property->getValue($data);
			}
		}

		// If type is array, just retun it's value.
		elseif ($type == 'array')
		{
			$output = ucfirst($type);
			$elements = $data;
		}

		// Start dumping datas
		if ($level == 0 || $innerLevel < $level)
		{
			// Start recrusive print
			$output .= "\n{$quoteTabes}(";

			foreach ($elements as $key => $element)
			{
				$output .= "\n{$tabs}[{$key}] => ";

				// Increment level
				$tabLevel = $tabLevel + 2;
				$innerLevel++;

				$output  .= in_array(gettype($element), $recrusiveType) ? $self($element, $level) : $element;

				// Decrement level
				$tabLevel = $tabLevel - 2;
				$innerLevel--;
			}

			$output .= "\n{$quoteTabes})\n";
		}
		else
		{
			$output .= "\n{$quoteTabes}*MAX LEVEL*\n";
		}
	}

	// Clean cache
	if($innerLevel == 1)
	{
		$cache = array();
	}

	return $output;
}

/**
 * Debugs a variable with detailed formatting and recursion detection.
 *
 * @param mixed $variable The variable to debug.
 * @param int $strlen Maximum string length to display (default: 100).
 * @param int $width Maximum width for array/object display (default: 50).
 * @param int $depth Maximum recursion depth (default: 10).
 * @param int $i Current recursion level (default: 0).
 * @param array $objects Array to track object references for recursion
 *                       detection.
 *
 * @return string|void Returns string when called recursively ($i > 0), void when at top level (echoes output)
 */
function var_debug($variable, $strlen=100, $width=50, $depth=10, $i=0,
	&$objects = array())
{
	$search = array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
	$replace = array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v');

	$string = '';

	switch(gettype($variable))
	{
		case 'boolean':      $string.= $variable?'true':'false'; break;
		case 'integer':      $string.= $variable;                break;
		case 'double':       $string.= $variable;                break;
		case 'resource':     $string.= '[resource]';             break;
		case 'NULL':         $string.= "null";                   break;
		case 'unknown type': $string.= '???';                    break;
		case 'string':
			$len = strlen($variable);
			$variable = str_replace($search,$replace,substr($variable,0,$strlen),$count);
			$variable = substr($variable,0,$strlen);
			if ($len<$strlen) $string.= '"'.$variable.'"';
			else $string.= 'string('.$len.'): "'.$variable.'"...';
			break;
		case 'array':
			$len = count($variable);
			if ($i==$depth) $string.= 'array('.$len.') {...}';
			elseif(!$len) $string.= 'array(0) {}';
			else
			{
				$keys = array_keys($variable);
				$spaces = str_repeat(' ',$i*2);
				$string.= "array($len)\n".$spaces.'{';
				$count=0;
				foreach($keys as $key)
				{
					if ($count==$width)
					{
						$string.= "\n".$spaces."  ...";
						break;
					}

					$string.= "\n".$spaces."  [$key] => ";
					$string.= var_debug($variable[$key],$strlen,$width,$depth,$i+1,$objects);
					$count++;
				}

				$string.="\n".$spaces.'}';
			}
		break;
		case 'object':
			$id = array_search($variable,$objects,true);
			if ($id !== false)
			{
				$string.=get_class($variable).'#'.($id+1).' {...}';
			}
			else if($i == $depth)
			{
				$string.=get_class($variable).' {...}';
			}
			else
			{
				$id = array_push($objects,$variable);
				$array = (array)$variable;
				$spaces = str_repeat(' ',$i*2);
				$string.= get_class($variable)."#$id\n".$spaces.'{';
				$properties = array_keys($array);
				foreach($properties as $property)
				{
					$name = str_replace("\0",':',trim($property));
					$string.= "\n".$spaces."  [$name] => ";
					$string.= var_debug($array[$property],$strlen,$width,$depth,$i+1,$objects);
				}
				$string.= "\n".$spaces.'}';
			}
			break;
	}

	if ($i>0) return $string;

	$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	do
	{
		$caller = array_shift($backtrace);
	}
	while ($caller && !isset($caller['file']));

	 if ($caller)
	{
		$string = $caller['file'] . ':' . $caller['line'] . "\n" . $string;
	}

	echo $string;
}

class Debug
{
	const ERROR = 1;
	const WARNING = 2;
	const DEBUG = 4;
	const INFO = 8;

	private $level = self::ERROR;
	private $logFile = null;

	/**
	 * Constructor for Debug class.
	 *
	 * @param int $level Debug level (default: self::ERROR).
	 * @param string|null $logFile Path to log file (default: null).
	 */
	public function __construct($level = self::ERROR, $logFile = null)
	{
		$this->level = $level;
		$this->logFile = $logFile;
	}

	/**
	 * Dumps an object if the specified level is within the debug level.
	 *
	 * @param int $level The debug level threshold.
	 * @param mixed $object The object to dump.
	 *
	 * @return void
	 */
	public function Dump($level, $object)
	{
		if ($level <= $this->level)
		{
			self::DumpStatic($object);
		}
	}

	/**
	 * Statically dumps an object.
	 *
	 * @param mixed $object The object to dump.
	 *
	 * @return void
	 */
	public static function DumpStatic($object)
	{
		var_dump($object);
		echo "<br />" . PHP_EOL;
		Common::FlushBuffers();
	}

	/**
	 * Exits with a message if the specified level is within the debug level.
	 *
	 * @param int $level The debug level threshold.
	 * @param string $message The exit message.
	 *
	 * @return void
	 */
	public function DebugExit($level, $message)
	{
		if ($level <= $this->level)
		{
			exit($message);
		}
	}

	/**
	 * Logs a message to the configured log file.
	 *
	 * @param string $message The message to log.
	 *
	 * @return void
	 */
	public function Log($message)
	{
		if (null != $this->logFile)
		{
			self::LogStatic($message, $this->logFile);
		}
	}

	/**
	 * Statically logs a message to a specified log file.
	 *
	 * @param string $message      The message to log
	 * @param string|null $logFile Path to the log file
	 *
	 * @return void
	 */
	public static function LogStatic($message, $logFile)
	{
		if (null != $logFile)
		{
			$time = date('Y-m-d H:i:s');

			$directory = dirname($logFile);
			if (!is_dir($directory))
			{
				mkdir($directory);
			}

			file_put_contents($logFile, $time . ' ' . $message . PHP_EOL,
				FILE_APPEND | LOCK_EX);
		}
	}

	/**
	 * Turns on debugging at the specified level.
	 *
	 * @param integer $level The debug level to set (default: E_NOTICE).
	 *
	 * @return void
	 */
	public function On($level = E_NOTICE)
	{
		$this->debug = $level;
	}

	/**
	 * Turns off debugging (sets level to E_NONE).
	 *
	 * @param integer $level The debug level to set (default: E_NONE).
	 *
	 * @return void
	 */
	public function Off(int $level = E_NONE)
	{
		$this->debug = $level;
	}

	/**
	 * Prints a statement if the specified level is within the debug level.
	 *
	 * @param mixed $level     The debug level threshold.
	 * @param mixed $statement The statement to print.
	 * @param mixed $label     Optional label to prefix the statement.
	 *
	 * @return void
	 */
	public function DebugPrint(
		mixed $level,
		mixed $statement,
		mixed $label = '')
	{
		if ($level <= $this->level)
		{
			if (!empty($label))
			{
				echo $label . ': ';
			}

			print_r($statement);
			echo '<br>' . PHP_EOL;
			Common::FlushBuffers();
		}
	}

	/**
	 * Shows a message if the specified level is within the debug level.
	 *
	 * @param mixed $level   The debug level threshold.
	 * @param mixed $message The message to show.
	 *
	 * @return void
	 */
	public function Show(mixed $level, mixed $message)
	{
		if ($level <= $this->level)
		{
			$this->Log($message);

			echo $message . PHP_EOL;
			Common::FlushBuffers();
		}
	}

	/**
	 * Statically shows a message and optionally logs it.
	 *
	 * @param string      $message The message to show.
	 * @param string|null $logFile Optional path to log file.
	 *
	 * @return void
	 */
	public static function ShowStatic(string $message, ?string $logFile = null)
	{
		self::LogStatic($message, $logFile);

		echo $message . PHP_EOL;
		Common::FlushBuffers();
	}
}
