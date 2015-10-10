<?php

	class PageSizeWidget extends CWidget
	{
		public $current = array();
		public $available = array();
		public $used = int;
		public $dataProvider = null;

		public function run()
		{
			if ($this->dataProvider && !$this->dataProvider->ItemCount)
				return;
			$this->render('pageSize');
		}
	}
?>
