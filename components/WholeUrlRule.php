<?php
// this class allows urlrules like index.php?a=123 old urlrules just parsed foldernames
// it is just made to fit my usecase

class WholeUrlRule extends CBaseUrlRule
{
	public $pattern = '';
	public $route = '';

	/**
	 * @var boolean whether this rule will also parse the host info part. Defaults to false.
	 */
	public $hasHostInfo=false;
	/**
	 * Creates a URL based on this rule.
	 * @param CUrlManager $manager the manager
	 * @param string $route the route
	 * @param array $params list of parameters (name=>value) associated with the route
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return mixed the constructed URL. False if this rule does not apply.
	 */
	public function createUrl($manager,$route,$params,$ampersand)
	{
		return false;
	}
	/**
	 * Parses a URL based on this rule.
	 * @param CUrlManager $manager the URL manager
	 * @param CHttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL (URL suffix is already removed based on {@link CUrlManager::urlSuffix})
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID. False if this rule does not apply.
	 */
	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
	{
		$len = strlen($request->getBaseUrl());
		$page = substr($request->getRequestUri(), $len); // /index.php?p=123

		$tr = array();
        if(preg_match_all('/<(\w+):?(.*?)?>/',$this->pattern,$matches))
        {
            $tokens=array_combine($matches[1],$matches[2]);
            foreach($tokens as $name=>$value)
            {
                if($value==='')
                    $value='[^\/]+';
                $tr["<$name>"]="(?P<$name>$value)";
            }
        }

		$this->pattern= str_replace('?', '\?', $this->pattern);
		$p=trim(rtrim($this->pattern,'*'), '/');
		$template=preg_replace('/<(\w+):?.*?>/','<$1>',$p);
		$this->pattern='/^\/'.strtr($template,$tr).'/';
		if (preg_match($this->pattern, $page, $matches))
		{
			foreach($_GET as $k=>$v)
				unset($_GET[$k]);
			foreach ($tr as $k=>$v)
			{
				$key = substr($k,1,-1);
				if (isset($matches[$key]))
					$_GET[$key] = $matches[$key];
			}
			return $this->route;
		}
		return false;
	}
}
