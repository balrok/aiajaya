<?php
    function is_pic($file)
    {
		$ending = strtolower(substr($file, -3));
		return in_array($ending, array('jpg', 'png', 'gif'));
    }

    class Gallery
    {
		public $cat;
        public $title;
        public $descr = '';
        public $dir;
        public $elements = array();

        public function __construct($cat, $dir='')
        {
			if (!$dir)
				$dir = 'bilder/gallery/'.$cat;
			$this->cat = $cat;
            if (!file_exists($dir)
                || !is_dir($dir)
                || strpos($dir, '..') == -1)
            {
                echo 'strange directory '.$dir;
                return NULL;
            }

            $this->dir = $dir;
            $info = @fopen($dir.'/info', "r");
            if ($info)
            {
                $content = '';
                while (!feof($info))
                    $content .= fgets($info, 4096);
                $rows = explode("\n", $content);
            }
            $this->title = $rows[0];
            $this->descr = $rows[1];
            $dir_handle = opendir($dir);

            $big = (file_exists($dir.'/big'));

            $files = array();

            while ($file = readdir($dir_handle)) 
            {
                if (is_pic($dir.'/'.$file))
                    $files[] = $file;
            }

            sort($files);

            for ($i = 0; $i < count($files); ++$i)
            {
				if (isset($rows[$i+2]))
				{
					if (substr($rows[$i+2], 0, 4) == 'NOT ' || $rows[$i+2] == 'NOT')
						continue;
				}
				else
				{
					$rows[$i+2] = '';
				}
				if ($rows[$i+2] == $files[$i])
					$rows[$i+2] = '';
                $big_path = ($big)? $dir.'/big/'.$files[$i] : '';
                $this->elements[] = new Element($dir.'/'.$files[$i], $this, $dir, $rows[$i+2]);
            }
        }

        public function print_all()
        {
            echo '<b>'.$this->title.'</b><br/>';
            echo $this->descr.'<br/>';
            for ($i = 0; $i < count($this->elements); ++$i)
                $this->elements[$i]->print_small();
            echo '<div style="clear:both"></div><br/><br/>';
        }

    }
