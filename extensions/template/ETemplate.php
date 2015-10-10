<?php


/*
 * This class will help injecting template variables into a text
 * and at display replacing the template variables with real data
 * following a description what needs to be setup:
 *
 *
	Yii::import('aiajaya.extensions.ETemplate');
	class Example extends ETemplate {
		public function init()
		{
			$this->path[] = 'example'; // foldername inside runtime folder where to save the tmp-data
			$this->eTemplate = 'attr'; // attributename which should get replaced
			return parent::init();
		}

		// here the variables getting defined
		public function getReplaceArray()
		{
			// syntax is following:
			// key is the to-replace template variable it can either be a string or a regex.. regexes are indicated by
			// a starting /
			// the value is an array first element is the attribute or method and second argument is an array of variable-names
			// this variable will represent the class which should get asked for the attribute or value.. if its an array longer than
			// one it will search up the array until it finds something
			return array(
				'{name}'=>array('name', array('model')),
				'/\{attribut_([a-z0-9A-Z]+)\}/'=>array('getTemplateAttribute("attributes_\1")', array('model')),
				// it is also possible that a templatevar is forced to have a closing parameter this is for example for opening and closing
				// php-code brackets important
				'/\{open([0-9]+)\}/'=>array('dummy(\1); if($this->isTrue){', array('this'),
     				array( // 3rd parameter opens the options
     					'parity'=>array( // option is parity
							// parameter for parity is an array with a single row.. the key is a non-regex for what can be searched in the
							// document - also this supports \1 from the regex of the original search
     						'{close\1}'=>array('dummy(\1); }', array('this'))
						),
					),
				),

			);
		}

		public function _saveToFile()
		{
			// extend the folder where to save that file
			$this->path[] = $this->id;
			parent::_saveToFile();
		}
	}
 *
 */




/**
 * to gain performance all templates will be written to the filesystem on each save
 * but other way round isn't possible currently
 */


// TODO the path-thing could be more performant


function append($a, $b)
{
	foreach ($b as $v)
		$a[] = $v;
	return $a;
}

function escapeForRegex($str)
{
	$patterns = array('/\//', '/\^/', '/\./', '/\$/', '/\|/',
			'/\(/', '/\)/', '/\[/', '/\]/', '/\*/', '/\+/',
			'/\?/', '/\{/', '/\}/', '/\,/');
	$replace = array('\/', '\^', '\.', '\$', '\|', '\(', '\)',
			'\[', '\]', '\*', '\+', '\?', '\{', '\}', '\,');
    return preg_replace($patterns,$replace, $str);
}


abstract class ETemplate extends CActiveRecord
{
	public $savedAsFile = false;

	public $basepath = array('application', 'runtime');
	// specify here your custom path for your model
	public $path = array();

	// the attribute which should become a template
	public $eTemplate = '';

	// must be called after we have an id
	// currently only depth=0 and depth=-1 are supported
	public function getSavePath($depth=0)
	{
		// error:
		if (!$this->id)
			throw new CException("getSavePath must be called only after this object has an id");
		if ($depth != 0 && $depth != -1)
			throw new CException("getSavePath depth is currently only 0 or -1");

		$path = append($this->basepath, $this->path);
		if ($depth == -1)
			array_pop($path);

		return implode('.', $path);
	}

	// this function specify which stuff should get replaced
	// it works like this:
	// key specifies the displayed string
	// value specifies that, how the attribute is named in the model
	public abstract function getReplaceArray();
		// example:
		// {mail} will be $object->mail
		// '{mail}'=>array('mail', array('object')),
		// {name} will be either $model1->name or $model2->name whatever exists
		// '{name}'=>array('name', array('model1', 'model2')),
		// {picture} will be $object->getPicture()
		// '{picture}'=>array('getPicture()', array('object')),

	public function _removeFile()
	{
		// TODO implement a remove file for folders
		@unlink(YiiBase::getPathOfAlias($this->getSavePath()).'.php');
	}

	private function _saveToFile()
	{
		if ($this->savedAsFile)
			return true;
		// write to filesystem
		@mkdir(YiiBase::getPathOfAlias($this->getSavePath(-1)),  0777,  true);
		$html = $this->getReplacedContent($this->{$this->eTemplate}, $this->getReplaceArray());
		$ret = (@file_put_contents(YiiBase::getPathOfAlias($this->getSavePath()).'.php', $html) === false) ? false:true;
		$this->savedAsFile = $ret;
		return $ret;
	}

	protected function getReplace($objects, $replace)
	{
		$getone = $this->getOneHelper($objects, $replace);
		$newReplace = <<<EOP
<?php echo $getone;?>
EOP;
		return $newReplace;
	}

	protected function getReplacedContent($content, $replArray)
	{
		foreach ($replArray as $search => $value)
		{
			$search = (strpos($search, '/') === 0) ? $search : '/'.escapeForRegex($search).'/';
			preg_match_all($search, $content, $matches);
			if (empty($matches[0]))
				continue;

			$newReplace = $this->getReplace($value[1], $value[0]);

			if (isset($value[2]))
			{
				$options = $value[2];
				if (isset($options['parity']))
				{
					$parityList = array();
					$parity = $options['parity'];
					$pSearch = array_keys($parity);
					$pSearch = $pSearch[0];
					$pReplace = $parity[$pSearch][0];
					foreach ($matches as $k=>$list)
					{
						if ($k == 0)
							foreach ($list as $v)
								$parityList[] = array($pSearch, $pReplace);
						else
						{
							foreach ($list as $pos=>$v)
							{
								$parityList[$pos] = array(str_replace('\\'.$k, $v, $parityList[$pos][0]),
									str_replace('\\'.$k, $v, $parityList[$pos][1]));
							}
						}
					}
					$parityOne = array();
					foreach ($matches[0] as $v)
						$parityOne[] = $v;
					$parityTwo = array();
					$parityTwoReplace = array();
					foreach($parityList as $v)
					{
						$parityTwo[] = $v[0];
						$parityTwoReplace[] = $this->getReplace($parity[$pSearch][1], $v[1]);
					}
					$content = $this->correctParity($parityOne, $parityTwo, $parityTwoReplace, $content);
				}
			}
			$content = preg_replace($search, $newReplace, $content);
		}
		return $content;
	}

	/*
	* This function will correct elements which should occur in pairs.. it also wouldn't allow to cross them
	so <start1><start2></end1></end2> would be corrected to <start1></end1><start2></end1></end2>
	as you see in the example it is not very friendly ;)
	*/
	protected static function correctParity($parityOne, $parityTwo, $parityTwoReplace, $content)
	{
		// we always have to look at position of the current element and of the next element to correct it
		$pos = 0;
		$nextPos = 0;
		$oldPos = 0;
		$pOneCount = count($parityOne);
		for($i=0; $i<$pOneCount;$i++)
		{
			$pos = strpos($content, $parityOne[$i], $oldPos)+strlen($parityOne[$i]);
			$nextPos = ($i==$pOneCount-1)?strlen($content):strpos($content,$parityOne[$i+1], $pos);
			$parityPos = stripos($content, $parityTwo[$i], $pos);
			if ($parityPos === false || $parityPos > $nextPos)
			{
				// we have to insert this parity element before nextPos
				$content = substr_replace($content, $parityTwoReplace[$i], $nextPos, 0);
			}
			else
			{
				// we have to replace the parity element with the content
				$content = substr_replace($content, $parityTwoReplace[$i], $parityPos, strlen($parityTwo[$i]));
			}
			$oldPos = $pos;
		}
		return $content;
	}

	public function getOneHelper($models, $replace)
	{
		if (count($models) == 0)
			return '';

		// we can directly interpret this here
		// but have to watch out that noone can inject code
		if ($models[0] == 'this' && strpos($replace,';')===false)
		{
			if (($pos = strpos($replace, '(')) !== false)
				$val = call_user_func_array(array($this, substr($replace, 0, $pos)), explode(',', substr($replace,$pos+1,-1)));
			else
				$val = $this->$replace;
			$first = "'".str_replace("'", "\\'", $val)."'";
		}
		else
			$first = '$'.$models[0].'->'.$replace;

		if (count($models) > 1)
		{
			array_shift($models);
			$second = $this->getOneHelper($models, $replace);
			$ret = '$val='.$first.';((!empty($val))?$val:'.$second.')';
		}
		else
			$ret = $first;
		return $first;
	}

	// must be called after we have an id
	protected function afterSave()
	{
		$this->_removeFile();
		$ret = $this->_saveToFile();
		if ($ret)
		{
    		parent::afterSave();
			return true;
		}
		else
		{
			$this->addError('file', 'Fehler beim Speichern der Datei');
			return false;
		}
	}

	// we could process our templates with renderpartial.. but perhaps this will be faster
	// variables are set in like renderpartial
	// IDEA: in future we could declare which variables are static, so that repetive calls don't have to set the same vars again
	// and we can cache the results a bit
	public function getReplacedTemplate($variables = array())
	{
		// TODO should only be called at saving template
		// but also doesn't take much time ;)
		$this->_saveToFile();

		foreach ($variables as $k => $v)
			$$k = $v;
		ob_start();
		include YiiBase::getPathOfAlias($this->getSavePath()).'.php';
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
