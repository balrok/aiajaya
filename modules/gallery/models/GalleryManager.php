<?php



    class GalleryManager
    {
        public function __construct(CController $controller, $categories = array())
        {
			$this->controller = $controller;
            if (count($categories) > 0)
            {
                $this->mode = 'cat';
                $this->categories = $categories;

				foreach ($categories as $cat)
				{
                    $g = new Gallery('bilder/gallery/'.$cat, $this);
                    if (!$g)
                        return;
                    $this->galleries[] = $g;
                }
            }
        }

		public function print_all()
		{
			foreach ($this->galleries as $gal)
			{
				$gal->print_all();
			}
		}
    }
