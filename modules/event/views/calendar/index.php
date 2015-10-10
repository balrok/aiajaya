<?php
if ($logged_in && $model){

	foreach (get_class_methods($this) as $method_name)
	{
   		if (!preg_match("/^action\w+$/", $method_name))
        	continue;
        $action_id = preg_replace('/action/', '', $method_name, 1);
		$action_id = lcfirst($action_id);
		if ($action_id == 's')
			continue;
		echo CHtml::link($action_id, array($action_id)).'<br/>';
	}
}

$startDate = '';
if ($model)
{
	$startDate = '&dates='.date('Ymd', $model->from).'%2F'.date('Ymd', $model->to);
}



if (Yii::app()->params['calendarEnabled'] || Yii::app()->user->id) {
	$this->renderPartial('_calendar', array('startDate'=>$startDate));
}else {?>
	Der Kalender ist momentan deaktiviert.
<?php } ?>

