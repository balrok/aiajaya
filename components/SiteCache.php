<?php

class SiteCache
{
	public static function begin(CEvent $ev)
	{
		if (!self::isCacheable())
			Yii::app()->cache->flush();

		$html = Yii::app()->cache->get(self::cacheKey(), array('duration'=>60*60*24));
		$html = str_replace('  Not found in cache', 'The Cached one took:'."\n".self::getPerformanceData(), $html);
		if ($html)
			die($html);
		ob_start();
	}

	public static function end(CEvent $ev)
	{
		$html = ob_get_clean();
		if (self::isCacheable())
			Yii::app()->cache->set(self::cacheKey(), $html);
		echo $html;
	}

	public static function getPerformanceData()
	{
		list($count, $time) = Yii::app()->db->getStats();
		return "Execution Time: " . sprintf("%dms", Yii::getLogger()->getExecutionTime()*1000)."\n".
			"Memory Usage: " . sprintf("%.2f", Yii::getLogger()->getMemoryUsage()/1024)."kB\n".
			"DB Query count: " . sprintf("%d", $count)."\n".
			"DB Query time: " . sprintf("%dms", $time*1000);
	}

	public static function isCacheable()
	{
		return (Yii::app()->user->isGuest && empty($_POST) && http_response_code() == 200);
	}

	public static function cacheKey()
	{
		return base64_encode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
}
