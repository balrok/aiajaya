<?php

class FormRenderer extends CWidget
{
	public $form;
	public $htmlOptions = array();

    public function run()
	{
		$htmlOptions = array_merge(array('class'=>'form wide'), $this->htmlOptions);
		$form = new CForm($this->form, $this->form['model']);
		echo CHtml::openTag('div', $htmlOptions);
		echo $form->renderBegin();
		foreach($form->getElements() as $element)
		{
			echo CHtml::openTag('div', array('class'=>'row'));
			echo $element->render();
			echo '</div>';
		}
		echo '<div style="display:block; height:30px; margin-top:10px;">';
		echo $form->renderButtons();
		echo '</div>';
		echo $form->getActiveFormWidget()->errorSummary($this->form['model'])."\n";
		echo $form->renderEnd();
		echo '</div>';
    }
}
