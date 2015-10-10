<form action="" method="post">
	Folgende Termine aufnehmen:
	<ul>
		<?php echo CHtml::checkboxList('inc_events', $inc_events, $all_events) ?>
	</ul>

	Folgender Text ganz unten:
	<?php echo ShortWidgets::ckEditor($textModel, 'text'); ?>


	Aktiviere was du tun möchtest:
	<ul>
		<?php echo CHtml::radioButtonList('dowhat', isset($_POST['dowhat'])?$_POST['dowhat']:0, array(
			0=>'Vorschau hier',
			1=>'Vorschau als Email',
			2=>'Test-Newsletter',
			3=>'Newsletter WIRKLICH senden',
			)) ?>
	</ul>

	<input type="submit" name="xx" value="Testen / Senden" />
</form>

<div id="exportdata">
<br/>
<br/>

<?= $this->renderPartial('export_mail', array('dataProvider'=>$dataProvider, 'textModel'=>$textModel), true) ?>


</div>

<iframe id="myframe" src="about:blank" style="width:878px; height: 1035px;"></iframe>
<script type="text/javascript">
var doc = document.getElementById('myframe').contentWindow.document;
doc.open();
doc.write( document.getElementById('exportdata').innerHTML);
var size = Math.round(document.getElementById('exportdata').innerHTML.length/1024*100)/100 + "kByte";
document.getElementById('exportdata').innerHTML = size;
doc.close();
// var width = document.getElementById("main").clientWidth;
// var height = document.getElementById("mainbox").clientHeight;
// document.getElementById('myframe').style = "width:"+width+"px;height:"+height+"px;";
</script>
