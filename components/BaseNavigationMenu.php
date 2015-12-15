<?php

class BaseNavigationMenu
{
	public function getMenu()
	{
		$menu = array();
		return $menu;
	}

	public function getAngebote() {
		return array();
	}

	public function getFlatMenu($menu = null)
	{
		if (!$menu)
			$menu = $this->getMenu();
		return $this->_constructFlatMenu($menu);
	}

	protected function _constructFlatMenu($menu)
	{
		$retMenu = array();
		foreach ($menu as $item)
			if (isset($item['items']))
			{
				$items = $item['items'];
				unset($item['items']);
				$retMenu[] = $item;
				foreach ($this->_constructFlatMenu($items) as $subitem)
					$retMenu[] = $subitem;
			}
			else
				$retMenu[] = $item;
		return $retMenu;
	}

	public function getEntryByPageKey($menu, $key)
	{
		foreach ($menu as $item)
		{
			$found = false;
			if (isset($item['url']) && isset($item['url']['key']) && $item['url']['key'] == $key)
				$found = $item;
			else if (isset($item['items']))
				$found = $this->getEntryByPageKey($item['items'], $key);
			if ($found)
				return $found;
		}
		return null;
	}

	public function getMenuWithActive($url, $onlyActive = true)
	{
		$menu = $this->getMenu();

		foreach ($menu as &$m)
			if ($this->_activateMenu($m, $url))
			{
				$m['active'] = true;
				break;
			}

		if ($onlyActive)
		{
			$activeMenu = array();
			foreach ($menu as &$m)
			{
				if (isset($m['active']) && $m['active'])
				{
					if (isset($m['items']))
						$m['items'] = $this->_deleteNonActive($m['items']);
					$activeMenu[] = $m;
				}
			}
			return $activeMenu;
		}
		else
			return $menu;
	}

	protected function _deleteNonActive(&$menu)
	{
		$activeMenu = array();
		foreach ($menu as &$m)
		{
			if (isset($m['active']) && $m['active'])
			{
				if (isset($m['items']))
					$m['items'] = $this->_deleteNonActive($m['items']);
				$activeMenu[] = $m;
			}
		}
		return $activeMenu;

	}

	protected function _activateMenu(&$menu, $url)
	{
		if (isset($menu['url']))
		{
			$matches = true;
			foreach ($menu['url'] as $k=>$v)
			{
				if ($k == 'preKey' && !isset($url[$k]))
					continue;
				if (!isset($url[$k]) || $v != $url[$k])
				{
					$matches = false;
					break;
				}
			}
			if ($matches)
			{
				$menu['active'] = true;
				return true;
			}
		}
		if (isset($menu['items']))
		{
			foreach ($menu['items'] as &$m)
			{
				if ($this->_activateMenu($m, $url))
				{
					$m['active'] = true;
					return true;
				}
			}
		}
		return false;
	}


	/* copied from CMenu **/
	function isItemActive($item)
    {  
		$route=Yii::app()->getController()->getRoute();
        if(isset($item['url']) && is_array($item['url']) && !strcasecmp(trim($item['url'][0],'/'),$route))
        {
            unset($item['url']['#']);
            if(count($item['url'])>1)
            {
                foreach(array_splice($item['url'],1) as $name=>$value)
                {
                    if(!isset($_GET[$name]) || $_GET[$name]!=$value)
                        return false;
                }
            }
            return true;
        }
        return false;
    }  
}
