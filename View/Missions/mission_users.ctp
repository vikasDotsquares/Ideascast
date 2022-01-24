<?php 
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true)); 
?>

<style>
.multiselect.dropdown-toggle.btn {
	background: #fff none repeat scroll 0 0 !important;
	color: #333;
	padding: 0;
}
.multiselect-all .username.checkbox {
	padding: 5px 20px 7px 40px !important;
}
.multiselect-container > li {
	margin-bottom: -5px;
	padding: 0;
}
</style>

<script type="text/javascript">
$(function(){
	
	$('#mission_user').multiselect({
		maxHeight: '400',
		buttonWidth: '100%',
		buttonClass: 'btn btn-info',
		checkboxName: 'data[MissionUser][user_id][]',
		enableFiltering: true,
		filterBehavior: 'text',
		includeFilterClearBtn: true,
		enableCaseInsensitiveFiltering: true,
		includeSelectAllOption: true,
		includeSelectAllIfMoreThan: 2,
		selectAllText: ' Select all',
		onInitialized: function() {
				
		}, 
	});
	 
})
</script>

<div class="row">
	<!-- Modal Large -->
	<div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>

	<!-- /.modal -->
	
	<div class="col-xs-12">

		<div class="row">
		   <section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>

		<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box">
						<!-- <div class="box-header" style=" cursor: move;"> </div> -->
						<div class="box-body clearfix" style="min-height: 800px;">
							<div class="share_wrapper col-sm-12" style="background-color: #F5F5F5; padding: 30px 10px;">
								<?php
									echo $this->Form->create('MissionUser', array('url' => array('controller' => 'missions', 'action' => 'mission_users', $project_id ), 'class' => 'form-bordered', 'id' => 'modelFormMissionUser', 'data-async' => ""));
									echo $this->Form->input('MissionUser.project_id', ['type' => 'hidden', 'value' => $project_id]);
								?>
								<label class="input-label col-sm-1" style="">Share With: </label>
								<div class="col-sm-7">
									<select id="mission_user" name="mission_user" multiple="multiple">
									<?php if(isset($all_users) && !empty($all_users)) { ?>
										<?php foreach($all_users as $key => $val) {
												$selected = '';
												if(isset($project_users) && !empty($project_users)) {
													$selected = ( in_array($val, $project_users) ) ? 'selected="selected"' : '';
												}
											$userDetail = $this->ViewModel->get_user( $val, null, 1 );
											$user_name = '';
											if(isset($userDetail) && !empty($userDetail)) {
												$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
											}
										?>
											<option <?php echo $selected; ?> value="<?php echo $val; ?>"><?php echo $user_name; ?></option>
										<?php } ?>
									<?php } ?>
									</select>
								</div>
								<div class="col-sm-4">
									<button class="btn btn-sm btn-success pull-right" type="submit" id="submit_form">Submit</button>
								</div>
							</div>
							
							<?php echo $this->Form->end(); ?>
							
						</div>
					</div>
				</div>
			</div>
    	</div>
	</div>
</div>
