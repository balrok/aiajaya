<?php 

class EGoogleAnalytics extends CWidget 
{ 
 	// type will switch inside the configs and also enable them
	// so your config goes to params['googleAnalytics'][$type]=array('code')
	public $type = '';

	public $code = ''; // code is your tracking code
	public $enabled = true; // wether this is enabled or not (for example running this code locally would slow down the pageload without
		// need or will blur the real traffic

	private $clientScript;
	private $pos;

    public function init() 
    { 
		$this->clientScript = Yii::app()->getClientScript();
		if ($this->type && isset(Yii::app()->params['googleAnalytics']) && isset(Yii::app()->params['googleAnalytics'][$this->type]))
		{
			$conf = Yii::app()->params['googleAnalytics'][$this->type];
			$data = array('code', 'enabled');
			foreach ($data as $d)
			{
				if (isset($conf[$d]))
					$this->$d = $conf[$d];
			}
		}

		$this->pos = CClientScript::POS_END;
    }

    public function run() 
    { 
		if (!$this->enabled)
			return;
		$code = $this->code;
		$js = <<<EOP
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '$code']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
EOP;
        $this->clientScript->registerScript(__CLASS__, $js, $this->pos);
    }
}
?> 
