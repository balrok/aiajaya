<ul id="results">
	<?php
		if (!$models)
		{
			echo '<li>Keine Suchergebnisse</li>'."\n";
		}
		else
		{
			$i = 0;
			$max = 8;
			foreach ($models as $model)
			{
				$i++;
				if (!$model->meta_title)
					echo '<li>'.CHtml::link($model->key, array('/page/page/get', 'key'=>$model->key)).'</li>'."\n";
				else
					echo '<li>'.CHtml::link($model->meta_title, array('/page/page/get', 'key'=>$model->key)).'</li>'."\n";
				if ($i == $max)
					break;
			}
			if (count($models) > $i)
				echo '<li>... noch '.(count($models)-$i).' weitere</li>';
		}
	?>
</ul>
