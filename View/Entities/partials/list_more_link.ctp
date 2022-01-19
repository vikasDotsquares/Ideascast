<?php /* list_more_link.ctp */ ?>
 
	<?php 
		if( isset($rows) && !empty($rows) ) { 
			
			foreach( $rows as $detail ) {
				$data = $detail['ElementLink']; 
			?>
		<div class="row">
			<div class="col-sm-4">
				<?php echo  $data['title'] ; ?>
			</div>
			
			<div class="col-sm-5">
				<?php echo $data['references']; ?>
			</div> 

			<div class="col-sm-3 text-center">
				<div class="btn-group ">
					<a href="#" class="btn btn-sm btn-info update_link" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_link', $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update">
						<i class="fa fa-pencil"></i>
					</a>
					
					<a href="#" class="btn btn-sm btn-danger remove_link" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_link', $data['id'], 'admin' => FALSE ), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="remove">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<div class="ajax-pagination clearfix"> 
		<?php echo $this->element('pagination', array( 'model'=>'ElementLink', 'limit' => 1  ));  ?>
	</div>
	
<?php } ?>

