<?php

if ($disable != 'head')
{
?>

<div class="terminnav">

<?php
if (Yii::app()->params['calendarEnabled'] || Yii::app()->user->id)
{
?>
	<div style="float:left;">
		Zeige als:
	<?php
	$ajax = array(
		'type'=>'GET',
		'success'=>'js:function(html){
			jQuery("#eventlisting").html(html);
		}',
	);

	echo CHtml::ajaxLink('Liste', array('/event/termin/ajaxListing'), $ajax, array('id'=>'ajaxList',
		'href'=>$this->createUrl('/event/termin/index'),
		'class'=>get_class($this)=='TerminController'?'active':''));
	echo ', ';
	echo CHtml::ajaxLink('Kalender', array('/event/calendar/ajaxCalendar'), $ajax, array('id'=>'ajaxCalendar',
		'href'=>$this->createUrl('/event/calendar/index'),
		'class'=>get_class($this)=='CalendarController'?'active':''));
	echo '</div>';
}
?>


<?php
}

if ($disable != 'foot')
{
?>
	<div style="clear:both"></div>
</div>
<?php } ?>
