<?php /* list_more_mindmap.ctp */ ?>
<?php 
if( isset($rows) && !empty($rows) ) {
	
	foreach( $rows as $detail ) { 
	 
		$data = $detail[$model];
	?>
	<div class="row">
		<div class="col-sm-5" > <?php echo  $data['title'] ; ?></div> 						
		<div class="col-sm-5 text-justify">
			<?php echo _substr_text($data['description'], 150 ); ?>
		</div>							
		<div class="col-sm-2 text-center">							
				<div class="btn-group" >
				<a href="#" class="btn btn-sm btn-info view_mindmap tipText" data-remote="	<?php echo Router::Url(array('controller' => 'entities', 'action' => 'view_mindmap', $data['element_id'], $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>"  data-eid="<?php echo $data['element_id']; ?>" title="Open MindMap" >
				<i class="fa fa-eye"></i>
				</a>				
				
				
				<a href="#" class="btn btn-sm btn-success update_mindmap tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_note', $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update" title="Edit MindMap">
					<i class="fa fa-arrow-down"></i></a>
					
					<a href="#" class="btn btn-sm btn-danger remove_mindmap tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_mindmap', $data['element_id'], $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="remove" title="Move to Bin">
						<i class="fa fa-trash"></i>
					</a>
					
				</div>
		</div>
		<div class="list-form col-sm-12" style="display: none;">
			<?php
			echo $this->Form->create('Mindmaps', array('url' => array('controller' => 'entities', 'action' => 'add_mindmap', $data['element_id']), 'class' => 'padding-top formAddElementMindmap', 'id' => '', 'enctype' => 'multipart/form-data')); ?>
			<input type="hidden" name="data[ElementMindmap][id]" class="form-control" value="<?php echo $data['id']; ?>" />
			<input type="hidden" name="data[ElementMindmap][element_id]" class="form-control" value="<?php echo $data['element_id']; ?>" />
			<div class="form-group">
				<label class=" " for=" ">Title:</label>
				<input type="text" name="data[ElementMindmap][title]" class="form-control" placeholder="MindMap title" value="<?php echo $data['title']; ?>" /> 
				<span class="error-message text-danger" style=""></span>
			</div>
			<div class="form-group">
				<label class=" " for="mindmap_desc_<?php echo $data['id'] ?>">Description:</label>
				<textarea rows="3" class="form-control mindmap_desc" placeholder="MindMap description" name="data[ElementMindmap][description]" id="mindmap_desc_<?php echo $data['id'] ?>"><?php echo $data['description'] ?></textarea> 
				<script type="text/javascript" >
					$(function(){
						$('#mindmap_desc_<?php echo $data['id'] ?>').wysihtml5({
							"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
							"emphasis": true, //Italics, bold, etc. Default true
							"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
							"html": false, //Button which allows you to edit the generated HTML. Default false
							"link": true, //Button to insert a link. Default true
							"image": true, //Button to insert an image. Default true,
							"image_url": $js_config.base_url + "entities/upload_note_image/" + $js_config.currentElementId, //Button to insert an image. Default true,
							"color": false, //Button to change color of font
							"size": 'sm', //Button size like sm, xs etc.
							"tags": {
								"img": {
									"check_attributes": {
										"width": 200,
										"alt": "alt-text",
									}
								}
							} //Button size like sm, xs etc.
						});
					})
				</script>
				<span class="error-message text-danger" style=""> </span>
			</div>
			
			<div class="form-group text-center">
				<a id="" href="#" class="btn btn-lg btn-success save_mindmap submit">
					<i class="fa fa-fw fa-save"></i> Save
				</a>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
<?php } ?>
	 
			<?php if( isset($rows) && !empty($rows) ) { ?>
				<div class="ajax-pagination clearfix">
					<?php  echo $this->element('jeera_paging');  ?>
				</div>
			<?php } ?>
		 
<?php } ?>

