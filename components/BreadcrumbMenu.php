<?php

Yii::import('zii.widgets.CMenu');
class BreadcrumbMenu extends CMenu
{
	public $rootLabel = '';
	public $rootLink = array();
	public function init()
	{
		$this->activateParents = true;
		// from parent
		$this->htmlOptions['id']=$this->getId();
		$route=$this->getController()->getRoute();
		// start change
		$route = str_replace('page/categorypage/get', 'page/page/get', $route);
		$this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
		// I already process the parent method beforeparent::init();
	}

	protected function renderMenu($items)
	{
		echo CHtml::openTag('ul', array('class'=>'breadcrumb', 'itemprop'=>'breadcrumb'));
		echo CHtml::openTag('li'). 'Sie befinden sich hier:' . CHtml::closeTag('li');

		if (!count($items) || (count($items)>0 && $items[0]['url'] != $this->rootLink))
		{
			echo $this->renderMenuItem(array('label'=>$this->rootLabel, 'url'=>$this->rootLink), count($items) < 1);
		}

		$endItem = count($items)-1;
		foreach ($items as $k=>$item)
		{
			echo $this->renderMenuItem($item, $k==$endItem);
		}
		echo CHtml::closeTag('ul')."\n";
	}

	protected function renderMenuItem($item, $isEnd=true)
	{
		$label = $item['label'];
		if (isset($item['glyph']))
		{
			$label = '<span class="glyphicon glyphicon-'.$item['glyph'].'"></span> '.$label;
		}

		$label = '<span>' . $label . '</span>';
		// $options = array('itemscope'=>'itemscope', 'itemtype'=>"http://data-vocabulary.org/Breadcrumb");
		$options = array();
		if ($isEnd)
			$options['class'] = 'active';
        $return = CHtml::openTag('li', $options);
        if(!isset($item['url']) || $isEnd)
			$return .= $label;
		else
		{
			// $options = array('itemprop'=>'url');
			$options = array();
			if ($item['url'] == $this->rootLink)
				$options['rel'] = 'home';
			$return .= CHtml::link($label, $item['url'], $options);
		}
        $return .= CHtml::closeTag('li')."\n";
		return $return;

	}

	protected function isItemActive($item,$route)
	{
		return false;
	}
}
