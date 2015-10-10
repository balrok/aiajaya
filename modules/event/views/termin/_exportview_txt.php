<?php
	// person and contact are merged inside untertitel
	$person = $data->getUntertitel(false, false);

	/* 				contact 				*/
	$contact = '';
	// u. is for 0123 u. 456 # so two numbers
	if (preg_match_all('/\d[\d- \/€u.]+/', $person, $matches))
	{
		foreach ($matches[0] as $match)
		{
			if (strpos($match, '€') !== false)
				continue;
			if (strpos($match, '127.0.0.1') !== false)
				continue;
			$person = str_replace($match, '', $person);


			if (!strpos($match, '-') && !strpos($match, '/'))
			{
				$match = explode(' ', $match);
				if (strlen($match[0]) > 3)
					$match[0] .= '/';
				$match = implode(' ', $match);
			}
			$contact .= $match;
		}
		$contact = trim($contact);
	}
	// make contact more uniform:
	$contact = str_replace('/', '-', $contact);
	$contact = preg_replace('/ *- */', '-', $contact);
	$contact = str_replace('-', ' - ', $contact);
	$contact = preg_replace('/(\d) +(\d)/', '\1 \2', $contact);



	/* 				person 				*/
	$person = preg_replace('/([^"\'])http[^ <]+/', '\1', $person);

	$repl_array = array(
		'Tel',
		'mit',
		'Anm',
		'Anm: bei',
		'Anmeldung bei',
		'mit Anmeldung',
		'mit Anmeldung bei',
	);
	foreach ($repl_array as $v)
		$repl_array[] = $v.':';
	$repl_array = array_reverse($repl_array);
	$person = str_replace($repl_array, '', $person);
	$person = preg_replace('/  +/', ' ', $person);
	$person = preg_replace('/( *<br\/> *)+$/', '', $person);
	$person = trim($person);


	/* 				date 				*/
	$date = date('d.m.', $data->from);
	if (date('m', $data->from) != date('m', $data->to) || date('y', $data->from) != date('y', $data->to))
	{
		$date .= '- '.date('d.m.', $data->to);
	}
	else if (date('d', $data->from) != date('d', $data->to))
	{
		$date = date('d.', $data->from).'- '.date('d.m.', $data->to);
	}

	$time = date("H:i", $data->from);
	if ($time != date("H:i", $data->to))
	{
		$time.='-'.date("H:i", $data->to);
	}

	/* 				rubric				*/
	$rubric = $data->rubric;
	if (!$rubric)
	{
		if ($data->page_id && $data->page)
			$rubric = ucfirst(str_replace('oe', 'ö', $data->page->getPreKey()));
	}
	$rubric = preg_replace('/(.)\/(.)/', '\1 / \2', $rubric);
	$rubric = CHtml::encode($rubric);


	/* 				title				*/
	$url = '';
	$title = $data->titel;
	$title = str_replace('--', '', $title);
	$title = preg_replace('/(.)\/(.)/', '\1 / \2', $title); // lol looks like boobs
	if (($data->page_id && $data->page) || is_array($data->getUrl()))
	{
		if ($data->page_id && $data->page)
			$url = $data->page->getUrl();
		else
			$url = $data->getUrl();
		$partOne = $url[0];
		unset($url[0]);
		$url = $this->createAbsoluteUrl($partOne, $url);
	}
	else if (!is_array($data->getUrl()) && strpos($data->getUrl(), 'wachtraumarbeit.info'))
		$url = $data->getUrl();

?>

**<?= $title ?>**
  <?= substr(strftime("%a", $data->from),0,2) ?> <?= $date ?> <?= $time ?>

  Rubrik: <?= $rubric ?>

  <?= $person ?>

  <?php if ($contact) echo 'Anmeldung: '?><?= $contact ?>

  <?= $url ?>

-------------------------------
