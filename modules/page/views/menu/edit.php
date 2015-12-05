<?php


Yii::app()->clientScript->registerScript('autocomplete_img_renderer','
	var default_render=$.ui.autocomplete.prototype._renderItem;             
	jQuery.ui.autocomplete.prototype._renderItem = function(ul, item ) {              
		if(this.element[0].id.substr(0, 7) != "img") {
			return default_render(ul,item);
		} else  {
			return renderPlainhtml(ul,item)
		};
	}

	function renderPlainhtml(ul,item){
		return $("<li></li>").data("item.autocomplete", item).append("<a><span class=\'button create\'>"+item.label+"</span></a>").appendTo( ul );
	}
');

$model = end($models);
if ($model)
{
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'categorypage-form',
	'enableAjaxValidation'=>false,
));
echo $form->errorSummary($model);


foreach ($models as $model)
	echo $this->renderPartial('_edit', array('model'=>$model, 'allpages'=>$allpages, 'allimages'=>$allimages, 'form'=>$form));
?>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php } ?>
