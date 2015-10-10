<h1>Termine überprüfen</h1>

<p class="note">Hauptsächlich zum Fehler finden gedacht</p>

<table>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider'=>$dataProvider,
			'columns'=>array(
				'datum',
				array(
					'name'=>'Von',
					'value'=>'date("d.m.y", $data->from)',
				),
				array(
					'name'=>'Bis',
					'value'=>'date("d.m.y", $data->to)',
				),
				'titel',
				'untertitel',
				array(
					'name'=>'Fehler',
					'value'=>'$data->checkError(true)',
				),
			),

			'ajaxUpdate'=>true,
			'enableSorting'=>true,
			'enablePagination'=>'false',
			)
		); ?>
</table>



<?php
$this->widget('aiajaya.extensions.pagesize.PageSizeWidget', array(
			'available' => array('25'=>'25', '50'=>'50', '100'=>'100', '250'=>'250', '9999'=>'Alle'),
			'used' => $pagesize,
			'current'=>array('/termin/check'),
			'dataProvider'=>$dataProvider,
			));
?>
