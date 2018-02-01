<?php

namespace frontend\components;

abstract class Debug {

	public function init(){
		 Yii::app()->controller->render('/frontend/gestor1/index', $data);
	}

	static function fmaker($fp, $args, $debug) {
		fwrite($fp, PHP_EOL);
		fwrite($fp, date("H:i:s") . " >> " . basename($debug[0]['file']) . " L: " . $debug[0]['line'] . PHP_EOL); 

		//recorremos args
		foreach ($args as $k => $something) {
			$type = gettype($something);

			if (is_array($something) OR is_object($something)) {
				$log = print_r($something, true);
			} else {
				$log = $something;
			}

			//escribimos el log
			fwrite($fp, "Param $k ($type) > " . PHP_EOL);
			fwrite($fp, $log . PHP_EOL);
			fwrite($fp, PHP_EOL);
		}

		//cerramos
		fwrite($fp, PHP_EOL);
		fclose($fp);
	}

	static function fc() {
		//creamos archivo o abrimos
		$log = "debug_" . date("Y_m_d");
		$fp = fopen('protected/runtime/' . $log . ".log", 'w');

		//debug backtrace para sabe donde se hace
		$debug = debug_backtrace();

		//pillamos parametros
		$args = func_get_args();

		Debug::fmaker($fp, $args, $debug);
	}

	static function f() {
		//creamos archivo o abrimos
		$log = "debug_" . date("Y_m_d");
		$fp = fopen('protected/runtime/' . $log . ".log", 'a');

		//debug backtrace para sabe donde se hace
		$debug = debug_backtrace();

		//pillamos parametros
		$args = func_get_args();

		Debug::fmaker($fp, $args, $debug);
	}

	static function tracex($arg_list) {
		if (!YII_DEBUG) { Debug::errorPage(); }

		$debug = debug_backtrace();
		print "Realizado en: " . basename($debug[0]['file']) . " | Linea: " . $debug[0]['line'] . PHP_EOL; 

		$x = 0;
		foreach ($debug as $key=>$line) { 
			$x++;
			print "#" . $key . " > " . $line['file'] . " " . $line['line'] . PHP_EOL;
			if ($x > 2) {
				break;
			}
		}

		foreach ($arg_list as $k=>$v) {
			print "Param: " . $k . PHP_EOL . "[";
			//print "<pre>";
			print_r ($v);
			//print "</pre>";
			print "]" . PHP_EOL . PHP_EOL;
		}

		die();
	}

	//debug, todavia mejor :)
	static function trace() {
		if (!YII_DEBUG) {Debug::errorPage(); }

		$arg_list = func_get_args();


		//si es ajax request, debug normal
		if (Yii::app()->request->isAjaxRequest) {
			Debug::tracex($arg_list);
		}

		print "<pre style='margin-left: 0;
				border-left: 8px solid #4F5B93;
				border-right: 1px solid #4F5B93;
				border-bottom: 1px solid #4F5B93;
				font-family: Verdana !important;
				font-size: 12px;
				line-height: 23px;
				margin-bottom: 15px;
				background-color: #F0F0F0;
				color: #1E5EBC;
				'>";

		foreach ($arg_list as $x=>$me) {

			if (Debug::isJson($me)) {
				$type = "JSON";
			} else {
				$type = gettype($me);
			}



			print "<div style='border-top: 3px solid #4F5B93; padding:4px; padding-left: 12px; padding-right: 14px; background-color: #8892BF; color: white;'><b>Param</b>: $x <div style='float:right; font-weight:bold;'> (<i>$type</i>)</div> </div>";
			print "<div style='color: #303343; padding: 14px;'>";
			$txt = print_r ($me, true);
			print Debug::varProcess($txt);
			print "</div>";


		}

		$debug = debug_backtrace();
		$me = "Realizado en: <b>" . basename($debug[0]['file']) . "</b> | Linea: <b>" . $debug[0]['line'] . "</b>"; 

		print "<div style='border-top: 3px solid #4F5B93;font-size: 150%; font-weight: bold; padding:4px; padding-left: 12px; background-color: #8892BF; color: white;'>Trace <div style='font-size: 12px; float:right;font-weight:normal;'> $me</div></div>";

		print "<div style='color: #303343; padding: 14px;'>";
		
		foreach ($debug as $key=>$line) {

			echo "<div style='padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #303343;'>";
			echo "<div style='width: 30px; float: left;'>#" . $key . "</div>";
			if (isset($line['file'])) {
				echo "<div style='width: 100px; float: left;'><b>File</b>: " . $line['file'] . " </div>";
			}
			if (isset($line['line'])) {
				echo "<div style='float: right;'><b>Line</b>: " . $line['line'] . " </div><br/>";
			}
			if (isset($line['class'])) {
				echo "<div style='width: 320px; float: left; margin-left: 30px;'><b>Calling Class</b>: " . $line['class'] . " </div>";
			}
			echo "<b>Function</b>: " . $line['function'];
			echo "</div>";

			//echo "<hr style='border: 0; height: 1px; color: gray; background-color: #8892BF;'/>";
		}
		print "</div>";

		print "</pre>";

		die();
	}

	static function s($x, $finish = false, $trace = FALSE) {

		if (!YII_DEBUG) {Debug::errorPage(); }

		print "<pre style='margin-left: 0;
			padding: 18px;
			border: 1px solid #74b73f;
			border-left: 6px solid #74b73f;
			font-family: Arial !important;
			font-size: 14px;
			line-height: 23px;
			margin-bottom: 15px;
			background-color: #F0F0F0;
			color: green;
			'>";
		print_r ($x);
		

		if ($trace) {
			$debug = debug_backtrace();

			echo "Este debug viene de<br/>";

			foreach ($debug as $key=>$line) {
				echo "# " . $key . "<br/>";
				echo "Archivo: " . $line['file'] . "<br/>";
				echo "Linea: " . $line['line'] . "<hr/>";
			}
		}

		print "</pre>";

		if ($finish) {
			die();
		}

		
	}

	static function ml($m,$l,$finish = false)
	{
		if (!YII_DEBUG) { Debug::errorPage(); }

		print "<pre style='margin-left: 0;
			padding: 18px;
			border: 1px solid #74b73f;
			border-left: 6px solid #74b73f;
			font-family: Arial !important;
			font-size: 14px;
			line-height: 23px;
			margin-bottom: 15px;
			background-color: #F0F0F0;
			color: green;
			'>";
		print_r ($m." - ".$l);
		print "</pre>";
		if ($finish) 
		{
			die();
		}
	}

	static function c($x) {
		 Yii::log(CVarDumper::dumpAsString($x,10), 'info', 'application');
	}

	static function c_elastic($x) {

		Yii::log(json_encode($x), 'info','application.components.Elastix');
		
		//Yii::log(json_encode($x), 'info','application');
		

	}

	static function isJson($string) {
		if (is_string($string)) {
			return is_array(json_decode($string,true));
		}
	}


	static function varProcess($txt) {
		//comprobamos si es un json valido
		if (Debug::isJson($txt)) {
			$txt = Debug::jsonIndent($txt);
			$txt = str_replace(chr(34), "<span style='font-weight: bold;'>".chr(34)."</span>", $txt);
		}
		//
		$txt = str_replace("Array", "<b>Array</b>", $txt);
		$txt = str_replace("Object", "<b>Object</b>", $txt);
		$txt = str_replace("(", "<span style='color:gray;'>(</span>", $txt);
		$txt = str_replace(")", "<span style='color:gray;'>)</span>", $txt);
		$txt = str_replace("[", "<span style='color:gray;'>[</span>", $txt);
		$txt = str_replace("]", "<span style='color:gray;'>]</span>", $txt);
		$txt = str_replace("{", "<span style='color:gray;'>{</span>", $txt);
		$txt = str_replace("}", "<span style='color:gray;'>}</span>", $txt);

		preg_match_all('/\[([A-Za-z0-9: _-]+?)\]/', $txt, $out);

		if ($out) {
			foreach ($out[0] as $one) {
				$txt = str_replace($one, "<i style='color:#1E5EBC;'>" . $one . "</i>", $txt);
			}
		}

		$txt = str_replace("=>", "<span style='color:gray;'>=></span>", $txt);

		return ($txt);
	}

	static function q()
	{
		self::s("-- STOP --",1);
	}

	static function ss($x) {
		if (!YII_DEBUG) {Debug::errorPage(); }

		$r = "<pre style='color: gray; '>";
		$r .= print_r($x, true);
		$r .= "</pre>";

		return ($r);
	}

	static function w($x, $finish = false) {
		if (!YII_DEBUG) {Debug::errorPage(); }
		
		$r = Debug::draw_array($x);
		
		print $r;

		if ($finish) {
			die();
		}		
	}


	static function draw_array($array) {
		$r = "<table width='100%' style='font-family: Arial; font-size: 12px; border: 1px solid gray'>";

		$maintype = ucfirst(gettype($array));

		foreach ($array as $key=>$value) {
			
			if (is_array($value)) {
				$r .= "<tr style='border: 1px solid black;'>";
				$r .= "<td style='width:150px;font-weight: bold; background-color: #D8D8D8; padding: 3px;' valign='top'>$key ($maintype)</td>";
				$r .= "<td style='background-color: #f1f1f1; padding: 3px;'>";
				$r .= Debug::draw_array($value);
				$r .= "</td>";
				$r .= "</tr>";
			} else if (is_object($value)) {
				$r .= "<tr style='border: 1px solid black;'>";
				$r .= "<td style='width:150px;font-weight: bold; background-color: #D8D8D8; padding: 3px;' valign='top'>$key ($maintype)</td>";
				$r .= "<td style='background-color: #f1f1f1; padding: 3px;'>";
				$r .= Debug::draw_array((array) $value);//Debug::ss($value);
				$r .= "</td>";
				$r .= "</tr>";

			} else {

				$r .= "<tr >";
				$r .= "<td  style='width:150px;font-weight: bold; background-color: #D8D8D8; padding: 3px;' valign='top'>$key</td>";

				$type = ucfirst(gettype($value));
		        if($type == "String") $type_color = "<span style='color:green'>";
		        elseif($type == "Integer") $type_color = "<span style='color:red'>";
		        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
		        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
		        elseif($type == "NULL") $type_color = "<span style='color:gray'>";

				$r .= "<td style='background-color: #f1f1f1; padding: 3px;'>$type_color";
		        
		        if (is_array($value) || is_object($value)) {
		        	Debug::draw_array($value);
		        } else {
		        	$r .= $value;
		        }

				$r .= "</span> ($type)</td>";
				$r .= "</tr>";
			}
		}
		$r .= "</table>";
		return ($r);
	}
	
	/**
	 * Indents a flat JSON string to make it more human-readable.
	 *
	 * @param string $json The original JSON string to process.
	 *
	 * @return string Indented version of the original JSON string.
	 */
	static function jsonIndent($json) {

	    $result      = '';
	    $pos         = 0;
	    $strLen      = strlen($json);
	    $indentStr   = '  ';
	    $newLine     = "\n";
	    $prevChar    = '';
	    $outOfQuotes = true;

	    for ($i=0; $i<=$strLen; $i++) {

	        // Grab the next character in the string.
	        $char = substr($json, $i, 1);

	        // Are we inside a quoted string?
	        if ($char == '"' && $prevChar != '\\') {
	            $outOfQuotes = !$outOfQuotes;

	        // If this character is the end of an element,
	        // output a new line and indent the next line.
	        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
	            $result .= $newLine;
	            $pos --;
	            for ($j=0; $j<$pos; $j++) {
	                $result .= $indentStr;
	            }
	        }

	        // Add the character to the result string.
	        $result .= $char;

	        // If the last character was the beginning of an element,
	        // output a new line and indent the next line.
	        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
	            $result .= $newLine;
	            if ($char == '{' || $char == '[') {
	                $pos ++;
	            }

	            for ($j = 0; $j < $pos; $j++) {
	                $result .= $indentStr;
	            }
	        }

	        $prevChar = $char;
	    }

	    return $result;
	}	

	static function errorPage() {

		Yii::app()->layout='error';
		    Yii::app()->controller->render('/frontend/errors/error404');
	}

}