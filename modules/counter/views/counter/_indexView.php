<?php
$sum = 0;
$max = 0;
foreach ($ips as $stat) {
	$sum += $stat[2];
	$max = max($stat[2], $max);
}
?>
<h1><?= $name ?></h1>
<b>Besucher Insgesamt:</b> <?= $sum ?>
<br/>
<br/>
<table style="border:1px">
<?php

foreach($ips as $stat)
{
	list($m, $y, $c) = $stat;
	$month2lang = array('Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	// to have those statistic-bars not wider than xx pics i'll calculate a bit around
	$ratio = 560 / $max;
	$width = max(20, round($c * $ratio)-10);
	echo '<tr><td>'.$m.' ('.$y.')</td><td><div
	style="background:blue;color:white;padding-left:10px;width:'.$width.'px">'.$c.'</div></td></tr>'."\n";
}
?>
</table>
