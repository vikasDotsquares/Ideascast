<?php  
/* pr($filter_users);
pr($filter_projects);
pr($named_params) */
?>
<style type="text/css">
	.small-caps.tipText + .tooltip > .tooltip-inner  { text-transform: initial !important; }
</style> 
<div class="col-sm-12 projects-line-wrapper">
	
	<div class="line-data line-1 prj-people">
		<div class="img-box data-block">
			Remove <?php echo $this->Common->userFullname($filter_users);?> From Selected Project.
			<br />
			<button class="btn btn-danger">Remove</button>
		</div>
	</div>
</div>
<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>