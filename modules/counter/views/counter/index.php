<?php

$this->renderPartial('_indexView', array(
	'ips' => $balanceIps,
	'name' => 'Balance-Dresden.info',
));

$this->renderPartial('_indexView', array(
	'ips' => $wachtraumIps,
	'name' => 'WachtraumArbeit.info',
));
?>
<br/>
<a href="https://google.de/analytics">google analytics</a>
