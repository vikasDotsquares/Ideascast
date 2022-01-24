<?php 
	// $notes = $element_notes;
	pr($model );
	pr($rows, 1);
	foreach( $rows as $detail ) { 
	 
		$data = $detail[$model];
	?>
	<div class="table-rows">
		<div class="row">
			<div class="col-sm-3">
				<?php echo  htmlentities($data['title']) ; ?>
			</div>
			
			<div class="col-sm-3">
				<?php echo dateFormat($data['created'] ); ?>
			</div>
			
			<div class="col-sm-3">
				<?php echo dateFormat($data['modified'] ); ?>
			</div>
			
			<div class="col-sm-3 text-center">
				<div class="btn-group ">
					<a href="#" class="btn btn-sm btn-info update_note" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_note', $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update">
						<i class="fa fa-arrow-down"></i>
					</a>
					
					<a href="#" class="btn btn-sm btn-danger remove_note" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_note', $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="remove">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>
			
			<div class="list-form col-sm-12">
				<?php 
				echo $this->Form->create('Notes', array('url' => array('controller' => 'entities', 'action' => 'add_note', $data['element_id']), 'class' => 'padding-top formAddElementNote', 'id' => '', 'enctype' => 'multipart/form-data')); ?>
				<input type="hidden" name="data[Notes][id]" class="form-control" value="<?php echo $data['id']; ?>" /> 
				<input type="hidden" name="data[Notes][element_id]" class="form-control" value="<?php echo $data['element_id']; ?>" /> 
				
				<div class="col-sm-5">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-font"></i>
						</div>
						<input type="text" name="data[Notes][title]" class="form-control" value="<?php echo htmlentities($data['title']); ?>" /> 
					</div>
					<span class="error-message text-danger" style=""></span>
				</div>
				
				<div class="col-sm-5">  
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-file-text"></i>
						</div>
						<textarea rows="3" class="form-control" name="data[Notes][description]"><?php echo htmlentities($data['description']); ?></textarea> 
					</div>
					<span class="error-message text-danger" style=""> </span>
				</div>
				
				
				<div class="col-sm-2"> 
					<a id="" href="#" class="btn btn-sm btn-success save_note submit disabled">
						<i class="fa fa-fw fa-save"></i> Save
					</a>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
<?php } ?>
<div class="ajax-pagination">
	<?php  echo $this->element('jeera_paging');  ?>
</div>

