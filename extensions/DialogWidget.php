<?php

class DialogWidget extends CWidget
{
	public $id = '';
	public $link = array(
		'name'=>'Click me',
		'options'=>array(),
		);
	public $dialog = array('options'=>array(
		'title'=>'',
		'autoOpen'=>false,
		'modal'=>true,
		'width'=>'800px',
		));
	public $render = array('view'=>'somewhere',
		'vars'=>array('model'=>null));

	public function run()
	{
		$this->id = ($this->id)?$this->id:$this->getId();
		$linkOptions = CMap::mergeArray(array('onclick'=>'$("#'.$this->id.'").dialog("open"); return false'), $this->link['options']);
		echo CHtml::link($this->link['name'], '#', $linkOptions);
		ob_start();
			$this->beginWidget('zii.widgets.jui.CJuiDialog',
				CMap::mergeArray(array('id'=>$this->id), $this->dialog));
			echo Yii::app()->getController()->renderPartial($this->render['view'], $this->render['vars']);
			$this->endWidget('zii.widgets.jui.CJuiDialog');
		$html = ob_get_contents();
     	ob_end_clean();
		echo CHtml::tag('div', array('style'=>'display:none'), $html);
	}
}
	
