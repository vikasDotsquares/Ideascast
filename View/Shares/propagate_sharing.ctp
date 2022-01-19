<style>
	#tick_on_off .tick_default, #tick_on_off .tick_on, #tick_on_off .tick_off {

		padding: 6px 7px !important;
		line-height:18px;
	}
	.form-group{ margin-bottom:8px; }

	#save_propagation{  font-size:12px; padding: 5px 10px !important; }

	.btn-select ul li {
	border-bottom: 1px solid #b5bbc8;
	padding: 5px 6px;
	text-align: left;
	}
	.btn-select-list span.text-value {
	display: inline-block;
	float:right;
	padding : 6px 0px;
	}
	.btn-select-list span:first-child {
	width: 90%;
	}


	.input-group-addon.btn-filter, .input-group-addon.btn-times, .input-group-addon.btn-progress {
	color: #fff;
	cursor: pointer;
	}
	.input-group-addon.btn-progress {
	border-color: #00acd6 !important;
	background-color: #00c0ef !important;
	display: none;
	}
	.input-group-addon.btn-filter {
	border-color: #478008 !important;
	background-color: #67a028 !important;
	}
	.input-group-addon.btn-times {
	border-color: #c12f1d !important;
	background-color: #dd4b39  !important;
	}


	.input-group.controls {
	border: 1px solid #cccccc;
	border-collapse: separate;
	display: table;
	float: right;
	position: relative;
	transition: all 0.6s ease-in-out 0s;
	width: 80px;
	}

	.form-group {
			display: table;
			width: 100%;
	}
	.form-group .form-part {
			display: table-cell;
			width: 30%;
	}
	@media screen and (max-width: 1023px) {
		.input-group.input-propagate {
				margin: 10px auto !important;
		}
	}
	.show_profile {
	display: inline-block;
	padding: 5px 10px;

	}
</style>


<script type="text/javascript">
$(function(){

	$("input#skills").tokenfield({
		autocomplete: {
			// source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
			source: function( request, response ) {

				if( request.term != '' && request.term.length > 2 ) {
					$.getJSON( $js_config.base_url + 'groups/get_skills', { term: request.term  }, function(response_data) {
						var items = [];
						if( response_data.success ) {
							if( response_data.content != null ) {
								$.each( response_data.content, function( key, val ) {
									var item ;
									item = {'label': val, 'value': key}
									items.push(item);
								});
								response(items)
							}
						}
						// cl(items)
					} );
				}
			},
			focus: function( event, ui ) {
                // $( ".project" ).val( ui.item.label );
                // console.log(ui.item.label);
				return false;
			},
			delay: 100
		},
		// limit: 1,
		showAutocompleteOnFocus: true,
		allowEditing: false

	})
	.on('tokenfield:createtoken', function (event) {
		var existingTokens = $(this).tokenfield('getTokens');
		$.each(existingTokens, function(index, token) {
			if (token.value === event.attrs.value)
			event.preventDefault();
		});
	})
	.on('tokenfield:createdtoken tokenfield:removedtoken', function (event) {
		event.preventDefault()
		$('#get_users').trigger('click')
	})

	// clear skills field on page load
	$('#skills').tokenfield('setTokens', []);

	// clear skills field on click of clear skills button
	$('body').delegate('#clear_skills', 'click', function(event){
		event.preventDefault()
		$(this).find('span').removeClass('fa-search').addClass('fa-times');
		$('#skills').tokenfield('setTokens', []);
		$('#get_users').trigger('click')
		console.log($(this).find('span'))
	})

	// Get users according to the skills entered
	$('body').delegate('#get_users', 'click', function(event){

		event.preventDefault();

		// set blank to select box label
		$('.btn-select .btn-select-value').text('Select User')

		var tokens = $('#skills').tokenfield('getTokens');

		$('#progress_bar').css({'display': 'table-cell'})
		var titems = [],
		params = {}
		url = '';
		var project_id = $js_config.project_id,
		userIds = $('#userIds').val()
		if( tokens.length ) {
			$.each(tokens, function(key, data){
				titems.push(data.value)
			} )
			params = {'project_id': project_id, 'skills': titems, 'userIds': userIds};
			url = $js_config.base_url + "groups/get_users_by_skills/" + project_id;
		}
		else {
			url = $js_config.base_url + "groups/get_users/" + project_id
			params = { 'project_id': project_id }
		}

		$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html('');

		$.ajax({
			url: url,
			type: "POST",
			data: $.param(params),
			dataType: "JSON",
			global: false,
			success: function (response) {

					var search_handler = '<li class="search-handler">' +
													'<div class="input-group">' +
													'<input type="text" class="form-control input-search">' +
													'<span class="input-group-addon" style="opacity: 0">' +
												'</span>' +
											'</div>' +
										'</li>';
				if (response.success) {

					var selectValues = response.content;

					$('#user_select').empty();

					$('#user_select').append(search_handler)
					if( selectValues != null ) {

						$('#user_select').append(function() {
							var output = '';

							$.each(selectValues, function(key, value) {

								output += '<li data-value="'+key+'" >' +
											'<span class="text-value">'+value+'</span>' +
											'<span style="" class="show_profile text-maroon" data-remote="'+$js_config.base_url+'shares/show_profile/'+key+'"  data-target="#popup_modal" data-toggle="modal" href="#">' +
												'<i class="fa fa-user "></i>' +
											'</span>' +
										'</li>';

								// output += '<option value="' + key + '">' + value + '</option>';
							});
							return output;
						});
					}
					else {
						$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
						$('#user_select').empty().append(search_handler);
					}

				}
				else {
					$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
					$('#user_select').empty().append(search_handler);
				}

			},// end success
			complete: function() {
				$('#progress_bar').hide()
			}

		})// end ajax

	})

	$('#show_profile_modal').on('hidden.bs.modal', function(event){
			$(this).removeData();
	})

	$(window).on('resize', function(){
		$("#skills-tokenfield").css('width', '100%')
	})

})
</script>


<script type="text/javascript">
jQuery(function($) {

	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offLabel: " Propagation Off ",
		onLabel: " Propagation On ",
		offTitle: "Further sharing not allowed",
		onTitle: "Further sharing is allowed",
	})
	setTimeout(function(){
		$('#tick_on_off').find('a:first').addClass('tipText').tooltip({ container: 'body', placement: 'top'})
		$('#tick_on_off').find('a:last').addClass('tipText').tooltip({ container: 'body', placement: 'top'})
		$('.tipText')
	},1000)

	$('#popup_model_box').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

	$('body').delegate('#save_propagation', 'click', function(event) {
		event.preventDefault()

		$('#user_select').next('.error-message.text-danger').text('')
		if( $("#share_user_id").val() == '' ) {
			$('#user_select').next('.error-message.text-danger').text('Please select a user.')
		}
		else{
			$('#frm_propagate_sharing').submit()
		}
	})
})
</script>

<?php
echo $this->Html->css('projects/tooltip');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/propagate_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));


echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));

echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));

?>

<?php echo $this->Session->flash(); ?>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_heading; ?></span>
					</p>
				</h1>

			</section>
		</div>

		<div class="row" >
		<section class="content-header clearfix" style="margin:8px 15px 0 15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;" >

				<div class="box-tools pull-right" style="padding: 0 0px 10px 10px ">
				<div class="box-tools pull-right">
					<a href="#" data-remote="<?php echo SITEURL.'shares/my_permissions/'.$project_id; ?>"  title="View your permissions and sharing" id="view_permissions" role="button" data-target="#popup_modal" data-toggle="modal" data-original-title="View your permissions and propagations" class="btn btn-sm btn-danger tipText pull-right" style="margin: 6px 0 0 0;
					" >
						<i class="fa fa-user"></i> My Permissions
					</a>
				</div>
				</div>

		</div>

		<div class="box-content">
			<div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top ">
                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

                            <div class="modal modal-success fade " id="show_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

		<div class="box-body clearfix list-shares" style="min-height: 800px">

			<div class="" style="width: 100%;  padding-bottom:15px ">

				<?php if( isset($users_list) && !empty($users_list)) {
					$userIds = array_keys($users_list);
					?>
					<input type="hidden" id="userIds" name="userIds" value="<?php echo implode(",", $userIds); ?>" />
					<input type="hidden" id="projectId" name="projectId" value="<?php echo $project_id; ?>" />
					<?php
					}
				?>

				<?php
				echo $this->Form->create('Propagation', array('url' => array('controller' => 'shares', 'action' => 'propagate_sharing', $project_id, 3, $share_by_id), 'class' => 'formAddSharing form-horizontal  ', 'style' =>'padding-top:15px;  border-top-left-radius: 3px;    background-color: #f5f5f5; border: 1px solid #ddd;  border-top-right-radius: 3px;', 'id' => 'frm_propagate_sharing' ));
				?>

				<input type="hidden" id="share_by_id" name="data[Share][share_by_id]" value="<?php echo $share_by_id; ?>">
				<input type="hidden" id="share_action" name="data[Share][share_action]" value="3">
				<input type="hidden" id="project_id" name="data[Share][project_id]" value="<?php echo $project_id; ?>">

				<div class="container-fluid  clearfix">

					 <div class="mgbottom">

							<label   class="input-label" style=" margin-bootom:0;">Skills: </label>
							<div class="input-colm input-hidden">
								<div class="input-group  text-left" style="">
									<input type="text" name="skills" id="skills" class="form-control" placeholder="" />

									<span class="input-group-addon btn-progress" id="progress_bar"><span class="fa fa-spinner fa-pulse"></span></span>
									<span class="input-group-addon btn-filter" style="display:none" id="get_users"><span class="fa fa-user"></span></span>
									<span class="input-group-addon btn-times" id="clear_skills" ><span class="fa fa-times"></span></span>
								</div>
								<span class="error-message text-danger"></span>

							</div>

					 </div>


					<div class="mgbottom">

							<label  class="input-label" style=" margin-bootom:0;">Share With: </label>
							<div id="select_user"  class="input-colm" style="margin-bottom:10px;">

								<a class="btn btn-default btn-select btn-select-light col-md-5 ">
									<input type="hidden" class="btn-select-input" id="share_user_id" name="data[Share][user_id]" value="<?php if( isset($shareUser) && !empty($shareUser)) { echo $shareUser; } ?>" />
									<span class="btn-select-value" style="height: 100%;">Select User</span>
									<span class='btn-select-arrow fa fa-arrow-down'></span>

									<ul class="btn-select-list" id="user_select">
										<li class="search-handler">
											<div class="input-group">
												<input type="text" class="form-control input-search">
												<span class="input-group-addon" style="opacity: 0">
													<!-- <i class="fa fa-times"></i> -->
												</span>
											</div>
										</li>
										<?php if( isset($users_list) && !empty($users_list) ) { ?>
											<?php foreach($users_list as $key => $value ) {
												$selectedUser = ( isset($shareUser) && !empty($shareUser) && $key == $shareUser) ? 'selected ' : '';
												?>
												<li data-value="<?php echo $key; ?>"  class="<?php echo $selectedUser; ?>">
													<span class="text-value"><?php echo $value; ?></span>
													<span style="" class="show_profile text-maroon" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $key)); ?>"  data-target="#popup_modal" data-toggle="modal" href="#"><i class="fa fa-user "></i></span>
												</li>
											<?php } ?>
										<?php } ?>
									</ul>
								</a>

							</div>




					<?php if( can_propagate($project_id) ) { ?>

							<!--<label  class=" col-sm-3 col-md-2 col-lg-2 " style="line-height:34px; margin-bootom:0;">Propagation: </label>-->
							<div class="input-colm"  title="User can further propagate this project or not" style="margin-bottom:10px; text-align:left" >
								<input type="checkbox" name="data[Share][propagating]" value="1" class="checkbox_on_off" id="propagating">
							</div>

					<?php } ?>



					<div class="input-colm   pull-right">

							<a   type="submit" href="#" id="save_propagation" class="btn btn-success pull-right">Submit</a>

					</div>


					</div>

				</div>
			</div>

			<?php  echo $this->Form->end(); ?>


<!-- end stage 1 -->



                </div>
            </div>
        </div>
	</div>
</div>
 <style>.mgbottom .btn.btn-success {
  font-size: 12px;
  padding: 5px 10px;
}</style>