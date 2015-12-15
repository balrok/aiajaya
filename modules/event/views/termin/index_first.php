<?php

if (Yii::app()->getModule('event')->termine_first) {
    if($logged_in)
    {
        echo "Zeilenumbr&uuml;che mit einer neuen Zeile im Text<br/>\n";

		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'aktuelles_form',
			'enableAjaxValidation'=>false,
		));
		echo CHtml::textField('infodate', $date->info, array('class'=>'form-control')).'<br/>';
		echo CHtml::textField('infotitel', $titel->info, array('class'=>'form-control')).'<br/>';
		echo CHtml::checkbox('infoactive', $active->info).'<br/>';
		$textModel = new Page();
		$textModel->text = $text->info;
		ShortWidgets::ckEditor($textModel, 'text');
        //echo $info->inputfield('<textarea rows="25" cols="96" name="%s">%s</textarea>', 2);
        echo '<input type="submit" name="stop" class="btn btn-primary" value="eintragen"/>';
		$this->endWidget();
    }
    if (!$text->info)
        echo '<!-- leerer text -->';
    else if (!$active->info)
        echo '<!-- nicht aktiv -->';
    else
    {
?>
        <div class="termin" id="specialtermin">
            <p class="terminHead">
                <span class="terminTitle2">
                    <?php echo $date->info;?>
                    <div style="float:right"><?php echo $titel->info;?></div>
                </span>
            </p>
            <div class="terminDescr2"><?php echo $text->info;?></div>
        </div>
        <div class="orangeGreenBox trenn">
         </div>
         <br/>
         <br/>
		<div style=" clear:both"> </div>
<?php
    }
}
