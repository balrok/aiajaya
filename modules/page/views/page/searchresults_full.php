Ihre Suche nach: <strong><?= implode(', ', $keywords)?></strong><br/>
<br/>

<?php
if (!$models)
{
?>
	Ergab leider keine Suchergebnisse
<?php
}
else
{
?>
	Liefert folgende Ergebnisse:
		<?php
			foreach ($models as $page)
			{
				$category = $page->getCategory();
			?>
				<div class="row">
					<div class="col-xs-2">
						<?php
							if ($category)
								echo CHtml::link(CHtml::image($category->getPageImg(), '', array('class'=>"thumbnail img-responsive")), $page->getUrl());
						?>
					</div>
					<div class="col-xs-10">
						<?= CHtml::link(!$page->meta_title?$page->key:$page->meta_title, array('/page/page/get', 'key'=>$page->key)); ?>
					</div>
				</div>
		<?php
			}
		?>
	</ul>
<?php
}
?>
