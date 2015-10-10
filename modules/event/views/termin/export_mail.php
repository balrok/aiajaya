<?php
// because emails and css aren't so nice.. a small helper
$css = array(
	'a' => array(
		'text-decoration' => 'none',
		'color' => 'rgb(1,103,152)',
	),
	'td' => array(
		'border' => '1px solid black',
		'padding' => '5px',// 10px 0',
		'text-align' => 'center',
	),
	'tr'=>array(),
);
$css['th'] = $css['td'];
$css['th']['background-color'] = '#ffa200';
$css['th']['padding-bottom'] = '10px';

$docss = function($element, $update=array()) use($css)
{
	$style = $css[$element];

	$style = array_merge($style, $update);
	$string = array();
	foreach ($style as $k=>$v)
		if ($v)
			$string[] = $k.':'.$v;
	return implode(';',$string);
};
?>

<strong>Veranstaltungen</strong><br/>
<br/>
<u>Stand: <?= date('d.m.Y')?></u><br/>
<br/>



<table id="termintable" style="border-collapse:collapse">
<tr>
	<th colspan="2" style="<?= $docss('th')?>">Datum</th>
	<th style="<?= $docss('th')?>">Rubrik</th>
	<th style="<?= $docss('th')?>">Veranstaltung</th>
	<th style="<?= $docss('th')?>">Uhrzeit</th>
	<th style="<?= $docss('th')?>">Leitung</th>
	<th style="<?= $docss('th')?>">Anmeldung</th>
</tr>
<?php
$even = true;
$oldMonth = false;
foreach ($dataProvider->getData() as $data)
{
	$even = !$even;
	if (!$oldMonth)
		$oldMonth = date('m', $data->from);
	if ($oldMonth != date('m', $data->from))
	{
		$newMonth = true;
		$oldMonth = date('m', $data->from);
	}
	else
		$newMonth = false;
	$this->renderPartial('_exportview', array('data'=>$data, 'even'=>$even, 'newMonth'=>$newMonth, 'docss'=>$docss));
}
?>
</table>


<br/>
<br/>
<?php echo $textModel->text ?>
