<?php

Yii::import('aiajaya.modules.counter.models.Ips');
// a simple helper class for counting users
class Counter
{
    public function count()
    {
		if (!isset(Yii::app()->session['counted_ip']))
		{
			$userip = Yii::app()->request->userHostAddress;

			$ip = Ips::model()->find('ip=:ip AND time>:time', array(':ip'=>$userip, ':time'=>time() - (1*60*60*60)));

			if (!$ip)
			{
				$ip = new Ips();
				$ip->referer = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'';
				$ip->agent = (isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:'';
			}
			$ip->time = time();
			$ip->ip = $userip;
			$ip->save();
		}
		Yii::app()->session['counted_ip'] = true;
    }
}
