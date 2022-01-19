<?php echo $this->Html->css(array(
	'projects/org_user_settings.css',
)); ?>

<style type="text/css">
	section.content{
		padding-top: 0;
	}

	.not-shown {
	    display: none !important;
	}
	.error {
	    margin-bottom: 0;
	    font-size: 11px;
	    font-weight: 400;
	    vertical-align: top;
	    display: block;
	}
	label.restricted {
		color: #aeaeae;
		pointer-events: none;
	}
</style>
<?php

if(isset($ProfileSetting) && !empty($ProfileSetting)){
	// pr($ProfileSetting);
	$admin_checked = (isset($ProfileSetting['ProfileSetting']['allow_admin_from_org']) && !empty($ProfileSetting['ProfileSetting']['allow_admin_from_org'])) ? 'checked' : '';

	$readonly = (isset($ProfileSetting['ProfileSetting']['allow_admin_from_org']) && !empty($ProfileSetting['ProfileSetting']['allow_admin_from_org'])) ? 'disabled' : '';

	$profile_checked = (isset($ProfileSetting['ProfileSetting']['own_profile']) && !empty($ProfileSetting['ProfileSetting']['own_profile'])) ? 'checked' : '';
	$fields_disabled = (isset($ProfileSetting['ProfileSetting']['own_profile']) && !empty($ProfileSetting['ProfileSetting']['own_profile'])) ? '' : 'disabled';
}
?>
<div class="row">
	<div class="col-xs-12">
		<section class="main-heading-wrap pb6">
			<div class="main-heading-sec">
				<h1><?php echo $page_heading; ?></h1>
				<div class="subtitles">
					<?php echo $page_subheading; ?>
				</div>
			</div>
			<div class="header-right-side-icon"></div>
		</section>
		<div class="box-content">
			<div class="box noborder">
				<div class="box-body clearfix user-setting-wrap" style="" id="box_body">
					 <p>These settings determine the policy for adding, updating and deleting User profiles in the system. </p>
				<div class="user-check-option">
				<input type="checkbox" <?php echo $admin_checked; ?>  id="application_administrators" name= "data[ProfileSetting][allow_admin_from_org]">	<label for="application_administrators">Application administrators can only add, update and delete User profiles in their Organization </label>
					</div>
					<div class="user-check-option">
				<input type="checkbox"  <?php echo $profile_checked; ?> id="allow_users" name= "data[ProfileSetting][own_profile]">	<label for="allow_users">Allow Users to update their own User profile:</label>
					</div>
					<div class="user-check-list-option">

						<ul>
						<?php
							foreach($ProfileField as $fld){

							if($fld['ProfileField']['column_no'] == 1){

							if($fld['ProfileField']['slug'] == 'email'){
								$disabled = 'disabled';
								$class = 'restricted';
							}else{
								$disabled = '';
								$class = '';
							}

							if($fld['ProfileField']['status'] == 1){
								$checked = 'checked';
							}else{
								$checked = '';
							}
						?>
						<li><input type="checkbox" <?php echo $checked; ?> <?php echo $fields_disabled; ?> <?php echo $disabled; ?> class="profile_field"  id="<?php echo $fld['ProfileField']['slug'] ?>" name="<?php echo $fld['ProfileField']['slug'] ?>">	<label for="<?php echo $fld['ProfileField']['slug'] ?>" class="<?php echo $class; ?>"><?php echo $fld['ProfileField']['title'] ?></label></li>
							<?php } } ?>
						</ul>
				     <ul>
						<?php
							foreach($ProfileField as $fld){

							if($fld['ProfileField']['column_no'] == 2){

							if($fld['ProfileField']['status'] == 1){
								$checked = 'checked';
							}else{
								$checked = '';
							}
						?>
						<li><input type="checkbox" <?php echo $checked; ?> <?php echo $fields_disabled; ?> class="profile_field" id="<?php echo $fld['ProfileField']['slug'] ?>" name="<?php echo $fld['ProfileField']['slug'] ?>">	<label for="<?php echo $fld['ProfileField']['slug'] ?>"><?php echo $fld['ProfileField']['title'] ?></label></li>
						<?php } } ?>
						</ul>


					</div>

					<div class="user-setting-footer">
					<button class="btn btn-success save_user_settings" type="button" >Save</button>
					</div>




				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
			<?php  //echo $this->Form->end(); ?>
		</div>
	</div>
</div>

<div class="modal modal-success fade" id="model_reassign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog ">
        <div class="modal-content"></div>
    </div>
</div>

<script type="text/javascript">
	$(() => {
		$('.user-check-list-option input:not(#email)').on('change', function(event) {
			event.preventDefault();
			var total_checked = 0;
			$('.user-check-list-option input').each(function(index, el) {
				if($(this).prop('checked')){
					total_checked += 1;
				}
			});
			if(total_checked == 0 || total_checked == undefined ) {
				$('#allow_users').prop('checked', false)
				$('.user-check-list-option input').prop('disabled', true);
			}
		});

		;($.restrict_related = (state, element) => {
			if(!$('#allow_users').prop('checked')) return;
			if(state){
                element.prop('disabled', true).prop('checked', true)
                element.parent().find('label').addClass('restricted');
			}
			else{
				element.prop('disabled', false);
				element.parent().find('label').removeClass('restricted');
			}
		})($('#organization').prop('checked'), $('#location'));
		$.restrict_related($('#reports_to').prop('checked'), $('#dotted_lines_to'));

		$('#organization').on('change', function(event) {
			event.preventDefault();
			var state = $('#organization').prop('checked');
			$.restrict_related(state, $('#location'));
		});
		$('#reports_to').on('change', function(event) {
			event.preventDefault();
			var state = $('#reports_to').prop('checked');
			$.restrict_related(state, $('#dotted_lines_to'));
		});

		$('#allow_users').on('change', function(event) {
			event.preventDefault();
			var state = $(this).prop('checked');
			if(state){
                $('.user-check-list-option input:not(#email)').prop('disabled', false);
			}
			else{
				$('.user-check-list-option input:not(#email)').prop('disabled', true);
			}
		});

		$('.save_user_settings').on('click', function(event) {
			event.preventDefault();
			var data = {
				admin: 0,
				user: 0,
				fields: {}
			};
			if($('#application_administrators').prop('checked')) {
				data.admin = 1;
			}
			if($('#allow_users').prop('checked')) {
				data.user = 1;
				$('.user-check-list-option input').each(function(index, el) {
					data["fields"][$(this).attr('id')] = ($(this).prop('checked')) ? 1 : 0;
				});
			}
			$(this).prop('disabled', true);
			$.ajax({
				url: $js_config.base_url + 'organisations/save_user_settings',
				type: 'POST',
				dataType: 'json',
				data: data,
				context: this,
				success: (response) => {
					$(this).prop('disabled', false);
				}
			})

		});
	})
</script>

