<?php

class NavigationMenu
{
	public function getMenu()
	{
		$menu = array();
		$menu[] = array( 'label'=>'Praxis für Wurzeln und Flügel', 'url'=>array('/page/page/get', 'key'=>'startseite'),);
		$menu[] = array(
				'label'=>'Lebensberatung',
				'url'=>array('/page/page/get', 'key'=>'lebensberatung'),
			);
		$menu[] = array(
				'label'=>'Selbstentfaltungsseminare',
				'url'=>array('/page/page/get', 'key'=>'selbstentfaltungsseminare'),
			);
		$menu[] = array(
				'label'=>'Bibliodrama und Märchen',
				'url'=>array('/page/page/get', 'key'=>'bibliodrama_maerchen'),
			);
		$menu[] = array(
				'label'=>'Stimmwachstum',
				'url'=>array('/page/page/get', 'key'=>'stimmwachstum'),
			);
		$menu[] = array(
				'label'=>'Sologesang',
				'url'=>array('/page/page/get', 'key'=>'sologesang'),
			);
		$menu[] = array(
				'label'=>'Referenzen/Kooperation',
				'url'=>array('/page/page/get', 'key'=>'partner'),
			);
		$menu[] = array(
				'label'=>'zur Person',
				'url'=>array('/page/page/get', 'key'=>'person'),
			);
		$menu[] = array(
				'label'=>'Kontakt/Impressum',
				'url'=>array('/page/page/get', 'key'=>'impressum'),
			);
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
}
