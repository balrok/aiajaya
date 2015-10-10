<?php

class CounterController extends Controller
{
	protected static $month2lang = array('Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');

	protected function getIpsData($pre)
	{
        $sql = 'SELECT ip, time FROM '.$pre.'ips ORDER BY time DESC';
		$ips = Yii::app()->db->createCommand($sql)->queryAll();

		$tmp = explode(' ',date("m y", $ips[0]["time"]));
		$y = $tmp[1];
		$m = $tmp[0];
		$c = 1;
		$stat_array = array();
		$max = 0;
		foreach ($ips as $ip)
		{
			$tmp = explode(' ',date("m y", $ip["time"]));
			if ($y != $tmp[1] || $m != $tmp[0])
			{
				$stat_array[] = array(self::$month2lang[$m-1], $y, $c);
				$m = $tmp[0];
				$y = $tmp[1];
				if ($c > $max)
					$max = $c;
				$c = 0;
			}
			$c++;
		}
		if ($c > $max)
			$max = $c;
		$stat_array[] = array(self::$month2lang[$m-1], $y, $c);
		return $stat_array;
	}

	public function actionIndex()
	{
		$balanceIps = $this->getIpsData("balance_");
		$wachtraumIps = $this->getIpsData("wachtraum_");
		$this->render('index', array(
			'balanceIps' => $balanceIps,
			'wachtraumIps' => $wachtraumIps,
			));
	}
}
