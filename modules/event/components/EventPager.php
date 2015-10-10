<?php
class EventPager extends CLinkPager
{
	public $cssFile = false;

	public $header = '';
	public $footer = '';

    public function run()
    {
        $this->registerClientScript();
        $buttons=$this->createPageButtons();

        echo Yii::app()->getController()->renderPartial('aiajaya.modules.event.views.terminnav', array('disable'=>'foot'), true);

		if ($buttons)
		{
			echo '<div style="float:right">
					Seite:
					<div style="float:right">';

			echo CHtml::tag('ul', $this->htmlOptions,implode("\n",$buttons));

			echo '</div>
				</div>';
		}

        echo Yii::app()->getController()->renderPartial('aiajaya.modules.event.views.terminnav', array('disable'=>'head'), true);
    }

    /** 
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons()
    {   
        if(($pageCount=$this->getPageCount())<=1)
            return array();

        list($beginPage,$endPage)=$this->getPageRange();
        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

		// first page
		if ($beginPage > 0)
		{
			$buttons[]=$this->createPageButton(1,0,self::CSS_INTERNAL_PAGE,$currentPage<=0,false);
		}

        // internal pages
        for($i=$beginPage;$i<=$endPage;++$i)
            $buttons[]=$this->createPageButton($i+1,$i,self::CSS_INTERNAL_PAGE,false,$i==$currentPage);

		// last page
		if ($endPage < $pageCount-1)
		{
			$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,self::CSS_INTERNAL_PAGE,$currentPage>=$pageCount-1,false);
		}

        return $buttons;
    }   

}
