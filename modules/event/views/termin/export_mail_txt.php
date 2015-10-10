VERANSTALTUNGEN:
Stand: <?= date('d.m.Y')?>

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
	$this->renderPartial('_exportview_txt', array('data'=>$data, 'even'=>$even, 'newMonth'=>$newMonth));
}
// TODO this contains html
echo $textModel->text;
