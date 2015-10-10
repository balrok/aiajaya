<?php
	// person and contact are merged inside untertitel
	$person = $data->getUntertitel(true);

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
	$contact = preg_replace('/(\d) +(\d)/', '\1&nbsp;\2', $contact);



	/* 				person 				*/
	$person = preg_replace('/([^"\'])http[^ <]+/', '\1', $person);
	$person = str_replace('#placeholderpersonstyle#', $docss('a'), $person);

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
	$title = CHtml::encode($data->titel);
	$title = str_replace('--', '<br/>', $title);
	$title = preg_replace('/(.)\/(.)/', '\1 / \2', $title);
	if ($data->page_id && $data->page)
	{
		$url = $data->page->getUrl();
		$partOne = $url[0];
		unset($url[0]);
		$title = CHtml::link($title, $this->createAbsoluteUrl($partOne, $url), array('style'=>$docss('a')));
	}
	else if (!is_array($data->getUrl()) && strpos($data->getUrl(), 'wachtraumarbeit.info'))
		$title = CHtml::link($title, $data->getUrl(), array('style'=>$docss('a')));
	else if (is_array($data->getUrl()))
	{
		$url = $data->getUrl();
		$partOne = $url[0];
		unset($url[0]);
		$title = CHtml::link($title, $this->createAbsoluteUrl($partOne, $url), array('style'=>$docss('a')));
	}




	$trstyle = array();
	if ($even)
		$trstyle['background-color'] = '#ffcc73';
	if ($newMonth)
		$trstyle['border-top'] = '3px solid black';
?>

<tr <?= $trstyle!=array()?'style="'.$docss('tr', $trstyle).'"':''?> itemscope itemtype="http://schema.org/Event">
	<td style="<?=$docss('td', array('border'=>'','border-top'=>'1px solid black', 'border-left'=>'1px solid black', 'border-bottom'=>'1px solid black')) ?>">
		<time itemprop="startDate" datetime="<?= date("c", $data->from)?>"> </time>
		<?= substr(strftime("%a", $data->from),0,2) ?></td>
	<td style="<?=$docss('td', array('border'=>'','border-top'=>'1px solid black', 'border-bottom'=>'1px solid black')) ?>">
		<?= $date ?></td>
	<td style="<?=$docss('td')?>"><?= $rubric ?></td>
	<td style="<?=$docss('td')?>" itemprop="name">
		<span itemprop="url" content="<?= $this->createAbsoluteUrl('/event/termin/index', array('#'=>'termin'.$data->id))?>"></span>
		<?= $title ?></td>
	<td style="<?=$docss('td')?>"><?= $time ?></td>
	<td style="<?=$docss('td')?>"><?= $person ?></td>
	<td style="<?=$docss('td')?>"> <?= $contact ?></td>
</tr>
