<?php

// returns the basepath of that application
// like /var/htdocs/projects/yii/myProject
function getBasePath()
{
	$request=Yii::app()->getRequest();
	return dirname($request->getScriptFile());
}

// call it anywhere in the code and it will log out the current memory usage
// TODO perhaps use one of yiis logging function
function memoryTrack()
{
	static $start_time = NULL;
	static $start_code_line = 0;

	$call_info = array_shift( debug_backtrace() );
	$code_line = $call_info['line'];
	$file = array_pop(explode('/', $call_info['file']));


	if($start_time === NULL)
	{
		print "debug ".$file."> initialize\n";
		$start_time = time() + microtime();
		$start_code_line = $code_line;
		return 0;
	}

	printf("debug %s> code-lines: %d-%d time: %.4f mem: %d KB\n", $file, $start_code_line, $code_line, (time() + microtime() - $start_time), ceil(memory_get_usage()/1024));
	$start_time = time() + microtime();
	$start_code_line = $code_line;
}



function getModule($name)
{
	return Yii::app()->getModule($name);
}

function getUser()
{
	return Yii::app()->user;
}

function formatCurrency($number, $cur='EUR')
{
	return locale()->NumberFormatter->format("#,##0.00 ¤", $number, $cur);
}

// 'full', 'long', 'medium' (default) and 'short' and null
function formatDate($timestamp, $dateWidth='medium', $timeWidth='medium')
{
	return locale()->DateFormatter->formatDateTime($timestamp, $dateWidth, $timeWidth);
}

function locale()
{
	return Yii::app()->locale->get(Yii::app()->params['locale']);
}

// will ping a url - is intended to be used for multithreaded code
// won't wait for any return value
function pingUrl($url)
{
	// call the action asynchronous
	$parts=parse_url($url);

	$fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

	if (!$fp)
		return;
	$out = "GET ".$parts['path']." HTTP/1.1\r\n";
	$out.= "Host: ".$parts['host']."\r\n";
	$out.= "Connection: Close\r\n\r\n";
	fwrite($fp, $out);
	fclose($fp); // immediately close after request
}


// this function gets called when a translation was missing
// there are 2 different configurable actions
// * Yii::app()->params['translation']['onMissingTranslation']['addToBottomDebug']
// 		will add the missing csv-translation line to bottomdebug
// * Yii::app()->params['translation']['onMissingTranslation']['echo']
// 		will output in place the missing csv line
function onMissingTranslation($event)
{
	if (!$event->message)
		return;
	$t = 'Message '.$event->category;
	if (Yii::app()->params['translation']['onMissingTranslation']['addToBottomDebug'] ||
		Yii::app()->params['translation']['onMissingTranslation']['echo'])
	{
		$csvLine = '"'.htmlentities(str_replace('"', '""', $event->message), ENT_QUOTES, 'UTF-8').'"';
		$csvLine .= ','.$csvLine;
		if (Yii::app()->params['translation']['onMissingTranslation']['addToBottomDebug'] && !addToBottomDebug($t, $csvLine, true))
			addToBottomDebug($t, $csvLine);
		if (Yii::app()->params['translation']['onMissingTranslation']['echo'])
			echo '<b>'.$event->category.'</b>: '.$csvLine.'<br/>';
	}
}

function sputcsv($row, $delimiter = ',', $enclosure = '"', $eol = "\n")
{
    static $fp = false;
    if ($fp === false)
        $fp = fopen('php://temp', 'r+'); // see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
        // NB: anything you read/write to/from 'php://temp' is specific to this filehandle

    else
        rewind($fp);
   
    if (fputcsv($fp, $row, $delimiter, $enclosure) === false)
        return false;
   
    rewind($fp);
    $csv = fgets($fp);
   
    if ($eol != PHP_EOL)
        $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
   
    return $csv;
}


// from http://www.webtoolkit.info/php-random-password-generator.html
// will generate random "speakable" strings
// the number of strengt is flag based
function generateRandomString($length=9, $strength=0)
{
	$vowels = 'aeuyi';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1)
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	if ($strength & 2)
		$vowels .= "AEUYI";
	if ($strength & 4)
		$consonants .= '23456789';
	if ($strength & 8)
		$consonants .= '@#$%?';

	$string = '';
	$alt = time() % 2; // will give a random alternation start
	for ($i = $alt; $i < $length+$alt; $i++)
	{
		if ($i%2)
			$string .= $consonants[(rand() % strlen($consonants))];
		else
			$string .= $vowels[(rand() % strlen($vowels))];
	}
	return $string;
}


function getSalutation($name, $surname, $title, $isMale)
{
	if ($isMale)
		$str = Yii::t('global', 'Sehr geehrter {title} {vorname} {name},', array('{vorname}'=>$surname, '{name}'=>$name, '{title}'=>$title));
	else
		$str = Yii::t('global', 'Sehr geehrte {title} {vorname} {name},', array('{vorname}'=>$surname, '{name}'=>$name, '{title}'=>$title));
	return $str;
}
