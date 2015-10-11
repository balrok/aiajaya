<?php

    class Element
    {
        public $gallery;
        public $descr = '';
        public $item;                                       // path to the element
        public $name;
        public $big;                                        // path to the big picture
        public $thumb;
        public $dir;
        /// $item=path to file, $gallery = gallery object, $big path to big file, $descr= description
        public function __construct($item, Gallery $gallery, $dir, $descr='')
        {
			$tmp = explode('/', $item);
            $this->name = end($tmp);
            if (file_exists($dir.'/thumb/'.$this->name))
                $this->thumb = $dir.'/thumb/'.$this->name;
            else
                $this->thumb= $item;
            $this->item = $item;
            $this->gallery = $gallery;

            if (file_exists($dir.'/big/'.$this->name))
			{
                $this->big = $dir.'/big/'.$this->name;
			}
            else
            {
                $flv = substr($this->name, 0, strrpos($this->name, '.')).'.flv';
                if (file_exists($dir.'/big/'.$flv))
                {
                    $this->big = $dir.'/big/'.$flv;
                }
                else
                    $this->big = $item;
            }
            $this->descr = $descr;
        }

        public function print_small()
        {
            echo '<div class="gal_box">';
            //if ($this->big != $this->item)

			$baseUrl = Yii::app()->controller->baseUrl;
            echo CHtml::link(
				CHtml::image($baseUrl.$this->item, '', array('border'=>'0'))
				. '<br/>'
            	. $this->descr
				, $baseUrl.$this->big, array('class'=>'boxpopup'));

            //if ($this->big != $this->item)
            echo '</div>';
        }
    }
