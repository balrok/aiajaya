<?php
	echo '<p class="pagesizer">';
	echo 'Zeige ';
	$list = array();
	$checked_used = false;

	foreach ($this->available as $k => $v)
	{
		$page = $this->current;
		$html_opt = array();
		if ($k == $this->used)
		{
			$html_opt['class'] = 'used';
			$checked_used = true;
		}
		if ($this->used < $k && !$checked_used)
		{
			$html_opt1['class'] = 'used';
			$page1 = $page;
			$page1['pagesize'] = $this->used;
			$list[] = CHtml::link($this->used, $page1, $html_opt1);
			$checked_used = true;
		}

		$page['pagesize'] = $k;
		$list[] = CHtml::link($v, $page, $html_opt);
	}

	echo implode(' | ', $list);
	echo ' pro Seite';
	echo '</p>
	<p class="afterpagesizer"></p>
	';
?>
