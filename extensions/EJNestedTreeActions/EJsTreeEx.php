<?php

Yii::import('ext.jsTree.CJsTree');

class EJsTreeEx extends CJsTree
{
	public $options = array(
		'bindDoubleClick'=>true,
	);

	function init()
	{
		if (!is_array($this->options['plugins']))
			$this->options['plugins'] = array();
		$this->options['plugins'] = CMap::mergeArray($this->options['plugins'], array('ui', 'crrm', 'contextmenu', 'dnd', 'json_data'));
		if (!isset($this->options['json_data']) || !is_array($this->options['json_data']))
			$this->options['json_data'] = array();
		$this->options['json_data'] = CMap::mergeArray(
			array(
				'ajax'=>
					array(
						'url' => $this->getController()->createUrl('render'),
						'data' => 'js:function (n) {return {"id" : 	n.attr ? n.attr("id").replace("node_","") : 0}; }',
					),
			),
			$this->options['json_data']);

		$this->bind = array(
        	'move_node.jstree'=>'function (e, data) {
				data.rslt.o.each(function (i) {
				$.ajax({
					async : false,
					type: "POST",
                	url: "'.$this->getController()->createUrl('movenode').'" ,
					data : { 
						"operation" : "move_node", 
						"id" : $(this).attr("id").replace("node_",""), 
						"ref" : data.rslt.np.attr("id").replace("node_",""), 
						"isroot" : data.rslt.np.attr("id") == "'.$this->getId().'",
						"position" : data.rslt.cp + i,
						"title" : data.rslt.name,
						"copy" : data.rslt.cy ? 1 : 0
					},
					success : function (r) {
						if(!r.status) {
							$("#errordialog").html(html);
							if (r.status == 0)
							{
								var html = "";
								for (i=0;i<r.error.length;i++)
									html += r.error[i]+"<hr/><br/>";
							}
							else
								html = r;
							$("#errordialog").html(html);
							$("#errordialog").dialog("open");
							$.jstree.rollback(data.rlbk);
						}
						else {
							$(data.rslt.oc).attr("id", "node_" + r.id);
							if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
								data.inst.refresh(data.inst._get_parent(data.rslt.oc));
							}
						}
						$("#analyze").click();
					}
				});});}',
			'rename.jstree' => 'function (e, data) {
				$.post(
                	"'.$this->getController()->createUrl('renamenode').'",
					{ 
						"operation" : "rename_node", 
						"id" : data.rslt.obj.attr("id").replace("node_",""),
						"title" : data.rslt.new_name
					}, 
					function (r) {
						if(!r.status) {
							$.jstree.rollback(data.rlbk);
						}
					}
				);
			}',
			'remove.jstree' =>'function (e, data) {
				data.rslt.obj.each(function () {
					$.ajax({
						async : false,
						type: "POST",
						url: "'.$this->getController()->createUrl('deletenode').'",
						data : { 
							"operation" : "remove_node", 
							"id" : this.id.replace("node_","")
						}, 
						success : function (r) {
							if(!r.status) {
								data.inst.refresh();
							}
						}
					});
				});
			}',
			"create.jstree" =>'function (e, data) {
				$.post(
					"'.$this->getController()->createUrl('createnode').'",
					{ 
						"operation" : "create_node", 
						"id" : (data.rslt.parent == -1)? -1 : data.rslt.parent.attr("id").replace("node_",""), 
						"position" : data.rslt.position,
						"title" : data.rslt.name,
						"type" : data.rslt.obj.attr("rel"),
						"real_id" : data.rslt.obj.attr("real_id")
					}, 
					function (r) {
						if(r.status) {
							$(data.rslt.obj).attr("id", "node_" + r.id);
						}
						else {
							$.jstree.rollback(data.rlbk);
						}
					}
				);
			}',
		);
		parent::init();
	}

	function run()
	{
        $cs = Yii::app()->getClientScript();

		// button to create root nodes
		/*
		$id = 'button1';
		echo CHtml::button('Create Root', array('id'=>$id));

		$js = '$("#'.$id.'").click(function () {
				$("#'.$this->getId().'").jstree("create", -1);
		});';
        $cs->registerScript('Yii.CJsTreeButton#1',$js);
		*/


		// bind doubleclick	
		if (isset($this->options['bindDoubleClick']))
		{
			$js = '
			$("#'.$this->getId().' ul li").live("dblclick",function(){ 
				$.get(
					"'.$this->getController()->createUrl('enternode').'",
					{
						"id" : $(this).attr("id").replace("node_",""),
					},
					function (r)
					{
						if (r.status)
							window.open(r.url, "_self");
					}
				);

			});';
			$cs->registerScript('Yii.CJsTreeClick#1', $js);
		}

		// error dialog
		$js = 'function errorDialog(r) {
				var html = "";
				if (r.status == 0)
				{
					for (i=0;i<r.error.length;i++)
						html += r.error[i]+"<hr/><br/>";
				}
				else
					html = r;
				$("#errordialog'.$this->getId().'").html(html);
				$("#errordialog'.$this->getId().'").dialog("open");
		}';
        $cs->registerScript('Yii.CJsTreeError#1', $js);

		echo '<div style="display:none;">';
		$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'errordialog',
			// additional javascript options for the dialog plugin
			'options'=>array(
				'title'=>'Error',
				'autoOpen'=>false,
				'modal'=>true,
				'width'=>'324px',
			),
		));

		$this->endWidget('zii.widgets.jui.CJuiDialog');
		echo '</div>';

		parent::run();
	}
}
