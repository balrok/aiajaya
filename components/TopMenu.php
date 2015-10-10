<?php

Yii::import('zii.widgets.CMenu');
class TopMenu extends CMenu
{
	public $customText = 'Wachtraumarbeit.info';
	public $customLink = array('/page/page/get', 'key' => 'start');
	public $enableSearch = false;

	protected function renderMenu($items)
	{
		static $id = 0;
		$id++;
		$this->submenuHtmlOptions = array('class'=>'dropdown-menu');
		if(count($items))
		{
			?>
			<nav class="navbar navbar-default" role="navigation">
				<div class="navbar-header">
					  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-<?php echo $id?>">
						<span class="sr-only">Navigation ausblenden</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					<?php echo CHtml::link($this->customText, $this->customLink, array('class'=>'navbar-brand')) ?>
				</div>
				<div class="container-fluid">
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-<?php echo $id?>">
			<?php if ($this->enableSearch) { ?>
				<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<!--<input name="keywords" class="searchfield" type="text" class="form-control" placeholder="Durchsuche die
						Seite...">-->
						<div id="searchbox">
							<div id="inputbox"><input class="form-control searchfield" type="text" name="keywords" placeholder="Durchsuche die Seite..." value="" /></div>
							<div class="results" style="display:none"></div>
							<div class="overlay" style="display::none"></div>
						</div>
					</div>
					<!--<button type="submit" class="btn btn-default">Submit</button>-->
				</form>
			<?php } ?>
				<ul class="nav navbar-nav">
			<?php
			$this->renderMenuRecursive($items);
			echo '</ul>';
			echo '</div>';
			echo '</div>';
			echo '</nav>';
		}
	}

    /**
	 * copied a big function for a small change
	 * search for "MY CHANGE"
     * Recursively renders the menu items.
     * @param array $items the menu items to be rendered recursively
     */
    protected function renderMenuRecursive($items)
    {  
        $count=0;
        $n=count($items);
        foreach($items as $item)
        {  
            $count++;
            $options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
            $class=array();
            if($item['active'] && $this->activeCssClass!='')
                $class[]=$this->activeCssClass;
            if($count===1 && $this->firstItemCssClass!='')
                $class[]=$this->firstItemCssClass;
            if($count===$n && $this->lastItemCssClass!='')
                $class[]=$this->lastItemCssClass;
            if($class!==array())
            {  
                if(empty($options['class']))
                    $options['class']=implode(' ',$class);
                else
                    $options['class'].=' '.implode(' ',$class);
            }  

            echo CHtml::openTag('li', $options);

            $menu=$this->renderMenuItem($item);
            if(isset($this->itemTemplate) || isset($item['template']))
            {  
                $template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
                echo strtr($template,array('{menu}'=>$menu));
            }  
            else
                echo $menu;

            if(isset($item['items']) && count($item['items']))
            {  
				// MY CHANGE removed <b></b> around submenu
                echo CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag('ul')."\n";
            }  

            echo CHtml::closeTag('li')."\n";
        }
    }

	protected function renderMenuItem($item)
	{
		$label = $item['label'];
		if (isset($item['items']))
		{
			if (!isset($item['linkOptions']))
				$item['linkOptions'] = array();
			if (!isset($items['linkOptions']['class']))
				$item['linkOptions']['class'] = '';
			$item['linkOptions']['class'] .= ' dropdown-toggle';
			$item['linkOptions']['data-toggle'] = 'dropdown';

			$label .= '<b class="caret"></b>';
		}
		if (isset($item['glyph']))
		{
			$label = '<span class="glyphicon glyphicon-'.$item['glyph'].'"></span> '.$label;
		}

		if (isset($item['addToEnd']))
			$label .= $item['addToEnd'];
        if(!isset($item['url']))
			$item['url'] = '#';
		$return = CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
		return $return;

	}
}
