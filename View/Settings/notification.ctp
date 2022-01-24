<?php echo $this->Html->css('projects/dropdown');  ?>
<?php echo $this->Html->css('projects/notification');  ?>
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ;
echo $this->Html->css('projects/people');
?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>


<?php
$uds =  $this->ViewModel->get_user($this->Session->read('Auth.User.id'));
$email_setting =  $uds['User']['email_notification'];
$web_setting =  $uds['User']['web_notification'];

?>
<style type="text/css" media="screen">
	.no-scroll {
	    overflow: hidden;
	}
	/*.tab-wrapper {
		overflow: overlay;
	}*/
	/*.bhoechie-tab-menu .list-group, .bhoechie-tab .bhoechie-tab-content {
		overflow-y: overlay;
		overflow-x: hidden;
	}*/
	/*.tab-content-wrapper {
		overflow: overlay;
		display: inline-block;
		width: 100%;
	}*/
	.bhoechie-tab {
		overflow: overlay;
	}
	section.content {
	    padding-top: 0;
	}
	.fa-exclamation {
		height: 14px;
		width: 14px;
		text-align: center;
		border: 1px solid;
		border-radius: 50%;
		line-height: 13px;
		margin-left: 0px;
		font-size: 11px;
	}
	a.list-group-item span, a.list-group-item i {
		cursor: pointer;
	}
	.multiselect-container>li>a>label {
	    padding: 3px 20px 3px 10px;
	}
	.available-selectbox2 .dropdown-menu {
		min-width: 74px;
	}
	.available-selectbox2 .multiselect.dropdown-toggle.btn .multiselect-selected-text {
	    float: left;
	    padding: 6px;
	}
	.available-selectbox2 .arrow.fa.fa-arrow-down::before {
	    content: "\f107";
	}
	.available-selectbox2 .multiselect.dropdown-toggle.btn .arrow {
	    background: transparent !important;
	    color: #000 !important;
	    border-left: none !important;
	}
	#start_date {
	    pointer-events: none;
	    background-color: #fff !important;
	}
	.calendar-trigger {
	    cursor: pointer;
	}
</style>

<script type="text/javascript" >


$(function(){
	$(".notify_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})

	$(".notify_on_off2").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		defaultId: 'tick_on_off_new',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})

	//$('.cmn-toggle').removeAttr('checked');
	//$('.cmn-toggle').parent().next().text('On');
	//$('.cmn-toggle').prop('checked', true);

	$('.cmn-toggle').click(function(){
		if($(this).is(":checked")){
			$(this).parent().next().text('On');
		}else{
			$(this).parent().next().text('Off');
		}
	})


	$(window).on('resize', function(){
		var $notifyTab = $("#notifyTab");
		if( $(this).width() <= 768 ) {
			$("[data-toggle=tab]", $notifyTab).tooltip({
				placement: 'bottom',
				container: 'body'
			});
		}
		else {
			$("[data-toggle=tab]", $notifyTab).tooltip('destroy')
		}
	})

	$(document).ready(function() {
		$("div.bhoechie-tab-menu > div.list-group>a").click(function(e) {
			e.preventDefault();

			var action_type = $(this).find('span').text().toLowerCase().replace(/ /g,"_");
			$("#email_notification_type").val(action_type.replace(/-/g,"_"));

			$(this).siblings('a.active').removeClass("active");
			$(this).addClass("active");
			var index = $(this).index();
			$("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
			$("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");

		});
	});


	$("#tick_on_off").click(function(e, data){

		if( data && data !== undefined ) {
			 if(data == 1) {
				$('#notify_toggle').prop('checked', true);
				$(".bhoechie-list-wrap .bhoechie-list").each(function(){
					$(this).find('.col-xs-4:first input').prop('disabled',false);
					$("#notfactionbtn").prop('disabled',false);
					$("#discard").prop('disabled',false);
				});


				if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);

				} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

					$("#notfactionbtn").prop('disabled',false);
					$("#discard").prop('disabled',false);

				} else {
					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);
				}

			}
			else {
				$('#notify_toggle').prop('checked', false);
				$(".bhoechie-list-wrap .bhoechie-list").each(function(){
					$(this).find('.col-xs-4:first input').prop('disabled',true);
					$('.col-xs-4:first-child .cmn-toggle').parent().next().text('Off');
						$("#notfactionbtn").prop('disabled',true);
						$("#discard").prop('disabled',true);
				});

				if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);

				} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

					$("#notfactionbtn").prop('disabled',false);
					$("#discard").prop('disabled',false);

				} else {
					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);
				}

			}
		} else {
			if( $("#tick_on_off .active").text() ){

				if( $("#tick_on_off .active").text() == 'Off' ){
					//console.log("11111111111");
					var runCode = true;
					BootstrapDialog.show({
						title: 'Email Notifications',
						message: 'Are you sure you want to turn off all email notifications?',
						type: BootstrapDialog.TYPE_DANGER,
						draggable: true,
							buttons: [
								{
									//icon: '',
									label: ' Turn Off',
									cssClass: 'btn-success',
									autospin: true,
									action: function (dialogRef) {
											$.when(

												$.ajax({
												url: $js_config.base_url + 'settings/notification_setting',
												data: $.param({email_notification:$("#tick_on_off .active").text(), user_id: '<?php echo $this->Session->read('Auth.User.id');?>' }),
												type: 'post',
												global: true,
												dataType: 'json',
												success: function(response){
														runCode = false;
														if( response.notType == 0 ){
															$('#notify_toggle').removeAttr('checked');
															$(".bhoechie-list-wrap .bhoechie-list").each(function(){
																$(this).find('.col-xs-4:first input').prop('disabled',true).prop('checked',false);
																$(this).find('.switch').next().text('Off');
																$(this).find('.col-xs-4:first input').prop('disabled',true);
																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);
															});


															if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);

															} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

																$("#notfactionbtn").prop('disabled',false);
																$("#discard").prop('disabled',false);

															} else {
																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);
															}

														} else {

															$('#notify_toggle').attr('checked','checked');
															$(".bhoechie-list-wrap .bhoechie-list").each(function(){
																$(this).find('.col-xs-4:first input').prop('disabled',false);
																$("#notfactionbtn").prop('disabled',false);
																$("#discard").prop('disabled',false);
															})
														}
													}
												})

											).then(function( data, textStatus, jqXHR ) {
												dialogRef.enableButtons(false);
												dialogRef.setClosable(false);
												dialogRef.getModalBody().html('<div class="loader"></div>');
												setTimeout(function () {
													dialogRef.close();
												}, 500);

											})
										}
								},
								{
									label: ' Cancel',
									//icon: '',
									cssClass: 'btn-danger',
									action: function (dialogRef) {
										$("#notify_toggle").prop('checked',false);

										$("#tick_on_off").find('.btn:eq(0)').removeClass('active tick_off').addClass('tick_default');
										$("#tick_on_off").find('.btn:eq(1)').addClass('active tick_on').removeClass("tick_default");
										dialogRef.close();
									}
								}
							],

						onhide: function(dialogRef){
							if(runCode) {
				                $("#notify_toggle").prop('checked',false);
				                $("#tick_on_off").find('.btn:eq(0)').removeClass('active tick_off').addClass('tick_default');
				                $("#tick_on_off").find('.btn:eq(1)').addClass('active tick_on').removeClass("tick_default");
			                }
			            }
					})


				} else {

					$.ajax({
						url: $js_config.base_url + 'settings/notification_setting',
						data: $.param({email_notification:$("#tick_on_off .active").text(), user_id: '<?php echo $this->Session->read('Auth.User.id');?>' }),
						type: 'post',
						global: true,
						dataType: 'json',
						success: function(response){

							if( response.notType == 0 ){
								$('#notify_toggle').removeAttr('checked');
								$(".bhoechie-list-wrap .bhoechie-list").each(function(){
									$(this).find('.col-xs-4:first input').prop('disabled',true);
									$("#notfactionbtn").prop('disabled',true);
									$("#discard").prop('disabled',true);
								})

							} else {
								window.location.href='<?php echo SITEURL; ?>'+'settings/notification/tab:tnotify';
							}
						}
					})

				}

			}
		}
	}).trigger('click', ['<?php echo $email_setting; ?>']);


	$("#tick_on_off_new").click(function(e, data){

		if( data && data !== undefined ) {

			  if(data == 1) {

				$('#notify_toggle_web').prop('checked', true);

				$(".switch_wrap_web").each(function(){

					//$(this).find('input').prop('disabled',false).prop('checked','true');
					//$(this).find('.switch').next().text('On');

						if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

							$("#notfactionbtn").prop('disabled',true);
							$("#discard").prop('disabled',true);

						} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

							$("#notfactionbtn").prop('disabled',false);
							$("#discard").prop('disabled',false);

						} else {
							$("#notfactionbtn").prop('disabled',true);
							$("#discard").prop('disabled',true);
						}

					})

			}
			else {

				$('#notify_toggle_web').prop('checked', false);

				$(".switch_wrap_web").each(function(){

					$(this).find('input').prop('disabled',true).prop('checked',false);
					$(this).find('.switch').next('label').text('Off');

				});

				if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);

				} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

					$("#notfactionbtn").prop('disabled',false);
					$("#discard").prop('disabled',false);

				} else {
					$("#notfactionbtn").prop('disabled',true);
					$("#discard").prop('disabled',true);
				}

			}



		} else {

			if( $("#tick_on_off_new .active").text() ){

				if( $("#tick_on_off_new .active").text() == 'Off' ){
					var runCode = true;
					BootstrapDialog.show({
						title: 'Web Notifications',
						message: 'Are you sure you want to turn off all web notifications?',
						type: BootstrapDialog.TYPE_DANGER,
						draggable: true,
							buttons: [
								{
									//icon: '',
									label: ' Turn Off',
									cssClass: 'btn-success',
									autospin: true,
									action: function (dialogRef) {
											$.when(

												$.ajax({
												url: $js_config.base_url + 'settings/notification_setting_web',
												data: $.param({web_notification:$("#tick_on_off_new .active").text(), user_id: '<?php echo $this->Session->read('Auth.User.id');?>' }),
												type: 'post',
												global: true,
												dataType: 'json',
												success: function(response){
														runCode = false;

														if( response.notType == 0 ){
															$('#notify_toggle_web').removeAttr('checked');
															$(".switch_wrap_web").each(function(){

																$(this).find('input').prop('disabled',true).prop('checked',false);
																$(this).find('.switch').next().text('Off');

																/* $("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true); */

															});


															if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);

															} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

																$("#notfactionbtn").prop('disabled',false);
																$("#discard").prop('disabled',false);

															} else {
																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);
															}

														} else {

															$('#notify_toggle_web').attr('checked','checked');
															$(".switch_wrap_web").each(function(){

																$(this).find('input').prop('disabled',false);

																/* $("#notfactionbtn").prop('disabled',false);
																$("#discard").prop('disabled',false); */
															});

															if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);

															} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

																$("#notfactionbtn").prop('disabled',false);
																$("#discard").prop('disabled',false);

															} else {
																$("#notfactionbtn").prop('disabled',true);
																$("#discard").prop('disabled',true);
															}


														}
													}
												})

											).then(function( data, textStatus, jqXHR ) {
												dialogRef.enableButtons(false);
												dialogRef.setClosable(false);
												dialogRef.getModalBody().html('<div class="loader"></div>');
												setTimeout(function () {
													dialogRef.close();
												}, 500);

											})
										}
								},
								{
									label: ' Cancel',
									//icon: '',
									cssClass: 'btn-danger',
									action: function (dialogRef) {
										$("#notify_toggle_web").prop('checked',false);

										$("#tick_on_off_new").find('.btn:eq(0)').removeClass('active tick_off').addClass('tick_default');
										$("#tick_on_off_new").find('.btn:eq(1)').addClass('active tick_on').removeClass("tick_default");
										dialogRef.close();
									}
								}
							],

						onhide: function(dialogRef){
							if(runCode) {
				                $("#notify_toggle_web").prop('checked',false);
				                $("#tick_on_off_new").find('.btn:eq(0)').removeClass('active tick_off').addClass('tick_default');
				                $("#tick_on_off_new").find('.btn:eq(1)').addClass('active tick_on').removeClass("tick_default");
			                }
			            }
					})
				} else {

					$.ajax({
						url: $js_config.base_url + 'settings/notification_setting_web',
						data: $.param({web_notification:$("#tick_on_off_new .active").text(), user_id: '<?php echo $this->Session->read('Auth.User.id');?>' }),
						type: 'post',
						global: true,
						dataType: 'json',
						success: function(response){

							if( response.notType == 0 ){
								$('#notify_toggle_web').removeAttr('checked');
								$(".switch_wrap_web").each(function(){

									$(this).find('input').prop('disabled',true);

									$("#notfactionbtn").prop('disabled',true);
									$("#discard").prop('disabled',true);
								});

								if( $("#tick_on_off_new .active").text() == 'Off' && $("#tick_on_off .active").text() == 'Off' ){

									$("#notfactionbtn").prop('disabled',true);
									$("#discard").prop('disabled',true);

								} else if( $("#tick_on_off_new .active").text() == 'On' || $("#tick_on_off .active").text() == 'On' ){

									$("#notfactionbtn").prop('disabled',false);
									$("#discard").prop('disabled',false);

								} else {
									$("#notfactionbtn").prop('disabled',true);
									$("#discard").prop('disabled',true);
								}


							} else {

								$(this).find('input').prop('disabled',false);
								$(this).find('.switch').next().text('On');
								window.location.href='<?php echo SITEURL; ?>'+'settings/notification/tab:tnotify';
							}
						}
					})

				}

			}
		}
	}).trigger('click', ['<?php echo $web_setting; ?>']);

})
</script>
<div class="row">

	<div class="col-xs-12">
		<section class="main-heading-wrap">
            <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><span><?php echo $page_subheading; ?> </span></div>
            </div>


         </section>

		<div class="box-content ">
			<div class="competencies-tab">
				<div class="row">
					<div class="col-md-9">
						<ul class="nav nav-tabs" id="setting_tabs">
							<li <?php if(isset($current_tab) && $current_tab != 'tnotify'){ ?> class="active" <?php } ?>>
								<a data-toggle="tab" data-target="#tab_general" href="#tab_general" aria-expanded="true">General</a>
							</li>
							<li>
								<a data-toggle="tab" data-target="#availability" href="#availability" aria-expanded="false">Availability</a>
							</li>
							<li <?php if(isset($current_tab) && $current_tab == 'tnotify'){ ?> class="active" <?php } ?>>
								<a data-toggle="tab" data-target="#tnotify" href="#tnotify" aria-expanded="true">Notifications</a>
							</li>

						</ul>
					</div>

					<div class="col-md-3 right text-right">
						<div class="setting-link-top-right">
								<a href="" class="tipText common-btns" title="Work Blocks" data-toggle="modal" data-target="#modal_work" data-remote="<?php echo Router::Url( array( "controller" => "searches", "action" => "work_block", 'admin' => FALSE ), true ); ?>"> <i class="blockblack18"></i></a>
                                <a href="" class="tipText common-btns" title="Absences" data-toggle="modal" data-target="#availability_modal" data-remote="<?php echo Router::Url( array( "controller" => "settings", "action" => "availability", 'admin' => FALSE ), true ); ?>"> <i class="absenceblack18"></i></a>
						</div>
					</div>
				</div>
			</div>



			<div class="row ">
                <div class="col-xs-12">
				<?php
					//$this->Session->read("Auth.User.email_notification");
					echo $this->Form->create('setStartPage', array('url' => ['controller' => 'settings', 'action' => 'email_notification'], 'class' => '', 'id' => 'frmStartPage'));
				?>
				<input type="hidden" name="email_type" id="email_notification_type" value="project" >

                    <div class="box noborder-top" style=" ">

                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
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
                        <div class="box-body clearfix list-shares" >
							<div class="tab-content">

								<div id="tab_general" class="tab-pane fade <?php if(isset($current_tab) && $current_tab != 'tnotify'){ ?> active in <?php } ?>">
								<div class="fliter fliter-notification">

									<div class="col-sm-8 text-left notpadding generalchoose" >
											<label>Choose from these options to customize your experience:	</label>

									</div>

									<div class="text-right col-sm-4" >
										<button class="btn btn-success btn-md" id="save_settings"> Save</button>

									</div>
								</div>

						   <div class="setting-general">

									<div class="select-signin-text">Select the page you want to display when you sign in:</div>
							   <div class="select-signin-box">
							   <select class="form-control" name="landing_url" >
									<?php /* ?><option value="users/projects">Assets</option><?php */ ?>
									<option value="analytics/knowledge">Capability Analytics</option>
									<option value="communities/index">Community</option>
									<option value="competencies/index">Competencies</option>
									<?php /* ?><option value="costs/index" >Cost Center</option><?php */ ?>
									<option value="studios/index">Design Board</option>
									<option value="templates/create_workspace/0">Knowledge Library</option>
									<?php /* <option value="team_talks/index">My Blogs</option> */?>
									<option value="shares/my_groups">My Groups</option>
									<option  value="boards/nudge_list">My Nudges</option>
									<option  value="projects/lists">My Programs</option>
									<option  value="projects/lists/tab:tab_projects">My Projects</option>
									<option  value="risks/index/0/0/1">My Risks</option>
									<option value="shares/my_sharing#user_view">My Sharing</option>
									<option value="tags/my_tags">My Tags</option>
									<option value="dashboards/task_centers/status:8/assigned:<?php echo $this->Session->read('Auth.User.id'); ?>">My Tasks</option>
									<?php /* ?><option value="todos/index" >My To-dos</option><?php */ ?>
									<option value="boards/opportunity" >Opportunities</option>
									<option value="resources/people">People</option>
									<option value="resources/planning">Planning</option>
									<?php /* ?><option value="dashboards/project_center">Project Center</option><?php */ ?>
									<option value="rewards/index">Reward Center</option>
									<option value="risks/index">Risk Center</option>
									<option value="analytics/social">Social Analytics</option>
									<option value="stories/index">Stories</option>
									<option value="dashboards/task_center">Task Center</option>
								</select>
								</div>

							   <div class="select-signin-text">Select the color theme you would like to apply:</div>

							   <div class="theme-option-sec">
							   <div class="row">
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">

							<div class="col-sm-12 color-preview indigo" data-theme="theme_indigo">
								Indigo
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">

							<div class="col-sm-12 color-preview dark_orchid" data-theme="theme_dark_orchid">
								Dark Orchid
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 color-preview plum_velvet" data-theme="theme_plum_velvet">
								Plum Velvet <!--(Initiative)<!-- -->
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 color-preview chilli_pepper" data-theme="theme_chilli_pepper">
								Chilli Pepper <!--(Action)-->
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 color-preview seaweed_green" data-theme="theme_seaweed_green">
								Seaweed Green <!--(Energize)-->
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12 color-preview cinnamon" data-theme="theme_cinnamon">
								Cinnamon <!--(Innovate)-->
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 theme_column">
					<div class="form-inner">
						<div class="form-group">
							<div class="col-sm-12 color-preview navy_blue" data-theme="theme_navy_blue">
								Navy Blue <!--(Focus)-->
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12 color-preview slate_blue" data-theme="theme_slate_blue">
								Slate Blue
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12 color-preview iridium" data-theme="theme_iridium">
								Iridium
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">

							<div class="col-sm-12 color-preview battleship_gray" data-theme="theme_battleship_gray">
								Battleship Gray
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
						<div class="form-group">

							<div class="col-sm-12 color-preview black " data-theme="theme_black">
								Black <!--(Strength)-->
								<i class="fa fa-check pull-right" ></i>
							</div>
						</div>

						<div class="form-group">

							<div class="col-sm-12 color-preview default" data-theme="theme_default">
								Default
								<i class="fa fa-check pull-right"></i>
							</div>
						</div>
					</div>
				</div>
		  </div>

							   </div>



								</div>


								</div>

								<?php

								$defaultTime = array(0,0.5,1,1.5,2,2.5,3,3.5,4,4.5,5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12,12.5,13,13.5,14,14.5,15,15.5,16,16.5,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5,22,22.5,23,23.5,24);
								$weekDefault = 8;
								$weekendDefault = 0;
								?>

								<div id="availability" class="tab-pane fade <?php if(isset($current_tab) && $current_tab == 'availability'){ ?> active in <?php } ?>">

									<div class="setting-available-top"><label>Specify the number of hours per day that you are available to work during each period:</label></div>
									<div class="setting-available">
									<div class="setting-available-scroll">
									<div class="available-list-width">
									<?php  //echo $this->Form->create('UserAvailability', array('url' => array('controller' => 'settings', 'action' => 'save_availability'), 'id' => 'modelFormProjectComment')); ?>
									<div class="setting-available-select">
										<div class="available-selectbox1">
										<label>Effective From:</label>
											<input id="id" name="id" type="hidden" />
										<div class="input-group">
											<input name="data[UserAvailability][start_date]" id="start_date" value="<?php echo date('d M, Y'); ?>" class="form-control dates input-small" type="text" readonly />

											<div class="input-group-addon data-new open-start-date-picker calendar-trigger">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
										</div>

										<div class="available-selectbox2">
										<label>Monday:</label>
										<select id="monday" name="monday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>
										<div class="available-selectbox2">
										<label>Tuesday:</label>
										<select id="tuesday" name="tuesday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>
										<div class="available-selectbox2">
										<label>Wednesday:</label>
										<select id="wednesday" name="wednesday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>
										<div class="available-selectbox2">
										<label>Thursday:</label>
										<select id="thursday" name="thursday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>
										<div class="available-selectbox2">
										<label>Friday:</label>
										<select id="friday" name="friday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>
										<div class="available-selectbox2">
										<label>Saturday:</label>
										<select id="saturday" name="saturday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekendDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>
										</div>

										<div class="available-selectbox2">
										<label>Sunday:</label>

										<select name="sunday"  id="sunday" class="form-control">
										<?php
											foreach($defaultTime as $d){
												if($weekendDefault == $d){
													echo "<option selected value=".$d.">".$d."</option>";
												}else{
													echo "<option value=".$d.">".$d."</option>";
												}
											}
										?>
										</select>

										</div>

										<div class="available-actionbox3">
											<button type="button" id="add_avail" class="btn btn-success">Add</button>
											<button type="button" id="reset_avail" class="btn btn-danger">Reset</button>
										</div>

										</div>
										<span class="error error-message"></span>
										<?php //echo $this->Form->end(); ?>

										<div class="available-list-wrap">

										<div class="available-list-header">
										 <div class="avbl-col avbl-col-1">
											Effective From
										 </div>
										  	<div class="avbl-col avbl-col-2">
											Monday
										 </div>
											<div class="avbl-col avbl-col-3">
											Tuesday
										 </div>
											<div class="avbl-col avbl-col-4">
											Wednesday
										 </div>
											<div class="avbl-col avbl-col-5">
											Thursday
										 </div>
											<div class="avbl-col avbl-col-6">
											Friday
										 </div>
											<div class="avbl-col avbl-col-7">
											Saturday
										 </div>
											<div class="avbl-col avbl-col-8">
											Sunday
										 </div>
											<div class="avbl-col avbl-col-9">
											Total
										 </div>
											<div class="avbl-col avbl-col-10">
											Actions
										 </div>

										</div>

										<div class="available-list-data">
											<?php
												//$current_user_id = $this->Session->read('Auth.User.id');
											$data = getAvailability();
											if( isset($data) && !empty($data) ) { ?>
												<?php foreach($data as  $row) { ?>
													<?php

														//$current_org_other = $this->Permission->current_org($row['UserAvailability']['user_id']);
														// pr($current_org_other);
													?>

															<div class="available-data-row">
																 <div class="avbl-col avbl-col-1">
																	<?php echo date('d M, Y',strtotime($row['UserAvailability']['effective'])); ?>
																 </div>
																 <div class="avbl-col avbl-col-2">
																	<?php echo $row['UserAvailability']['monday']; ?>
																 </div>
																<div class="avbl-col avbl-col-3">
																	<?php echo $row['UserAvailability']['tuesday']; ?>
																</div>
																<div class="avbl-col avbl-col-4">
																	<?php echo $row['UserAvailability']['wednesday']; ?>
																</div>
																<div class="avbl-col avbl-col-5">
																	<?php echo $row['UserAvailability']['thursday']; ?>
																</div>
																<div class="avbl-col avbl-col-6">
																	<?php echo $row['UserAvailability']['friday']; ?>
																</div>
																<div class="avbl-col avbl-col-7">
																	<?php echo $row['UserAvailability']['saturday']; ?>
																</div>
																<div class="avbl-col avbl-col-8">
																	<?php echo $row['UserAvailability']['sunday']; ?>
																</div>
																<div class="avbl-col avbl-col-9">
																	<?php echo $row['UserAvailability']['monday']+$row['UserAvailability']['tuesday']+$row['UserAvailability']['wednesday']+$row['UserAvailability']['thursday']+$row['UserAvailability']['friday']+$row['UserAvailability']['saturday']+$row['UserAvailability']['sunday']; ?>
																</div>

																<div class="avbl-col avbl-col-10 avbl-actions">
																	<a href="#" class="tipText" title="Edit" id="edit-data" data-id="<?php echo $row['UserAvailability']['id']; ?>"><i class="edit-icon"></i></a>
																	<a href="#" class="tipText" title="Delete" id="delete-data" data-id="<?php echo $row['UserAvailability']['id']; ?>"><i class="clearblackicon"></i></a>
																</div>
															</div>


												<?php } ?>
											<?php }
											else { ?>
											<div class="availability-data-found" >No Availability</div>
											<?php } ?>


										</div>

										</div>


										</div>


									</div>

									</div>
								</div>

						<div id="tnotify" class="tab-pane fade <?php if(isset($current_tab) && $current_tab == 'tnotify'){ ?> active in <?php } ?>">
									<div class="tab-wrapper">
						<div class="fliter fliter-notification">

						<div class="col-sm-8 text-left notpadding not-email-web" >
						<div class="alloptions" >
							<div class="alloptionssec">
								<label class="" for="notify_toggle">All Email Options: </label>
								<input type="checkbox" value="1" class="notify_on_off tipText" name="notify_toggle" id="notify_toggle">
							</div>
							<div class="alloptionssec allweboptions">
								<label class="" for="notify_toggle_web">All Web Options: </label>
								<input type="checkbox" value="1" class="notify_on_off2 web_notification tipText" name="notify_toggle_web" id="notify_toggle_web" >
							</div>
						</div>
						</div>

						<div class="text-right col-sm-4" >
							<button class="btn btn-success btn-md" type="submit" id="notfactionbtn"> Save</button>
							<!-- <button data-dismiss="modal" class="btn btn-danger btn-sm" id="discard" type="button"> Cancel</button>-->
							<?php /*?><a class="btn btn-sm btn-danger" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'lists', 'admin' => FALSE), TRUE); ?>" type="button">Cancel</a><?php */?>
						</div>
					</div>
							<div id="notify" class="tab-content-wrapper">

										<div class="col-lg-5 col-md-5 col-sm-8 col-xs-9 bhoechie-tab-container">
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bhoechie-tab-menu">
											  <div class="list-group">


												<?php /* ?><a href="#" class="list-group-item  active text-center">
												  <h4><i class="notification-menu-icon not-my-dashboards"></i> <span>Dashboard</span></h4>
												</a><?php */ ?>
												<a href="#" class="list-group-item  text-center active">
												  <h4><i class="notification-menu-icon not-program"></i> <span>Program</span></h4>
												</a>
												<a href="#" class="list-group-item  text-center ">
												  <h4><i class="notification-menu-icon not-project"></i> <span>Project</span></h4>
												</a>
												<a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon not-workspace"></i> <span>Workspace</span></h4>
												</a>
												<a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon not-task"></i> <span>Task</span></h4>
												</a>
												<a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon not-my-risks"></i> <span>Risk</span></h4>
												</a>
												<a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon not-my-groups"></i> <span>Group</span></h4>
												</a>
												<?php /* <a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon blog-info-center"></i> <span>Blogs</span></h4>
												</a>
												<a href="#" class="list-group-item text-center">
												   <h4><i class="notification-menu-icon not-to-dos"></i> <span>To-dos</span></h4>
												</a>*/?>

												<!--<a href="#" class="list-group-item text-center">
												   <h4 ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span>Sketches</span></h4>
												</a>-->


											  </div>
											</div>
											<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bhoechie-tab">
												<!-- flight section -->
													<?php /* ?>
												<div class="bhoechie-tab-content active ">
														<div class="bhoechie-tab-content-heading text-left">
															<h4><i class="fa fa-bell-o" aria-hidden="true"></i>&nbsp;Personalization</h4>
														</div>

														<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>

												<?php


												if(isset($project_data) && count($project_data) > 0 && $project_data[0]['EmailNotification']['notification_type'] == 'project'){

												?>
												<script>
													$(function(){
														setTimeout(function(){
														if('<?php echo $email_setting; ?>' == 0){
															$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');
															$('.col-xs-4:first-child .cmn-toggle').parent().next().text('Off');
														}
														},500)
													})
													</script>
												<?php
													echo $this->element('email_notification/dashboard_notification', $project_data);
												} else {
													echo $this->element('email_notification/html/dashboard_notification');
													?>
													<script>
													$(function(){
														setTimeout(function(){
														$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');

														if('<?php echo $email_setting; ?>' == 0){
															$('.col-xs-4:first-child .cmn-toggle').parent().next().text('Off');
															$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');
														}else{
														$('.col-xs-4:first-child .cmn-toggle').parent().next().text('On');

														$('.col-xs-4:first-child .cmn-toggle').prop('checked', true);

														}

														if('<?php echo $web_setting; ?>' == 0){

															$('.switch_wrap_web .cmn-toggle').parent().next().text('Off');
															$('.switch_wrap_web .cmn-toggle').removeAttr('checked' );


														}else{
															$('.switch_wrap_web .cmn-toggle').parent().next().text('On');
															$('.switch_wrap_web .cmn-toggle').prop('checked', true);
														}

														},500)
													})
													</script>
													<?php
												}
												?>

												</div>
												<?php */ ?>
												<!-- train section -->

												<div class="bhoechie-tab-content  active ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>


														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																</div>
															</div>





													<?php

													if(isset($program_data) && count($program_data) > 0 && $program_data[0]['EmailNotification']['notification_type'] == 'program'){
														?>
														<script>
															$(function(){
																setTimeout(function(){
																if('<?php echo $email_setting; ?>' == 0){
																	$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');
																	$('.col-xs-4:first-child .cmn-toggle').parent().next().text('Off');
																}
																},500)
															})
														</script>
														<?php
														echo $this->element('email_notification/program_notification', $program_data);
													} else {
														echo $this->element('email_notification/html/program_notification');
														?>
														<script>
															$(function(){
																setTimeout(function(){
																$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');

																if('<?php echo $email_setting; ?>' == 0){
																	$('.col-xs-4:first-child .cmn-toggle').parent().next().text('Off');
																	$('.col-xs-4:first-child .cmn-toggle').removeAttr('checked');
																}else{
																$('.col-xs-4:first-child .cmn-toggle').parent().next().text('On');

																$('.col-xs-4:first-child .cmn-toggle').prop('checked', true);

																}

																if('<?php echo $web_setting; ?>' == 0){

																	$('.switch_wrap_web .cmn-toggle').parent().next().text('Off');
																	$('.switch_wrap_web .cmn-toggle').removeAttr('checked' );


																}else{
																	$('.switch_wrap_web .cmn-toggle').parent().next().text('On');
																	$('.switch_wrap_web .cmn-toggle').prop('checked', true);
																}

																},500)
															})
														</script>
														<?php
													}
													?>
												</div>
												</div>



													<div class="bhoechie-tab-content">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>


														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																</div>
															</div>





													<?php



													if(isset($project_data) && count($project_data) > 0 && $project_data[0]['EmailNotification']['notification_type'] == 'project'){
														?>

														<?php
														echo $this->element('email_notification/project_notification', $project_data);
													} else {
														echo $this->element('email_notification/html/project_notification');
														?>

														<?php
													}
													?>
												</div>
												</div>

													<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
														<?php if(isset($workspace_data) && count($workspace_data) > 0 && $workspace_data[0]['EmailNotification']['notification_type'] == 'workspace'){
															echo $this->element('email_notification/workspace_notification', $workspace_data);
														} else {
															echo $this->element('email_notification/html/workspace_notification');
														}
														?>
													</div>
												</div>


												<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
															<?php if(isset($element_data) && count($element_data) > 0 && $element_data[0]['EmailNotification']['notification_type'] == 'element'){
																echo $this->element('email_notification/element_notification', $element_data);
															} else {
																echo $this->element('email_notification/html/element_notification');
															}
															?>
													</div>
												</div>

												<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">


																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
															<?php //if(isset($sketch_data) && count($sketch_data) > 0 && $sketch_data[0]['EmailNotification']['notification_type'] == 'riskcenter'){
																	echo $this->element('email_notification/risks_notification', $riskcenter);
															//}
															?>
													</div>
												</div>


													<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>


														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																<!--	<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>

															<?php if(isset($group_data) && count($group_data) > 0 && $group_data[0]['EmailNotification']['notification_type'] == 'group'){
																echo $this->element('email_notification/group_notification', $group_data);
															} else {
																echo $this->element('email_notification/html/group_notification');
															}
															?>
														</div>
													</div>



												<!-- hotel search -->




													<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">
																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
															<?php if(isset($todo_data) && count($todo_data) > 0 && $todo_data[0]['EmailNotification']['notification_type'] == 'to_dos'){
																echo $this->element('email_notification/todo_notification', $todo_data);
															} else {
																echo $this->element('email_notification/html/todo_notification');
															}
														?>

													</div>
												</div>

												<?php   /* ?><div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4>Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">


																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
														<?php if(isset($teamtalk_data) && count($teamtalk_data) > 0 && $teamtalk_data[0]['EmailNotification']['notification_type'] == 'team_talk'){
																echo $this->element('email_notification/teamtalk_notification', $teamtalk_data);
															} else {
																echo $this->element('email_notification/html/teamtalk_notification');
															}
															?>
													</div>
												</div><?php */ ?>


											<?php   /* ?>	<div class="bhoechie-tab-content  ">

														<div class="bhoechie-tab-content-heading text-left">
															<h4><i class="fa fa-bell-o" aria-hidden="true"></i>&nbsp;Personalization</h4>
														</div>

														<div class="bhoechie-list-wrap">

															<div class="bhoechie-header">

																<div class="row">


																	<div class="col-xs-4">

																	<label class="lable-l">Email</label>

																	</div>
																	<div class="col-xs-4">

																	<label class="lable-l">Web</label>

																	</div>
																	<!--<div class="col-xs-4">

																	<label class="lable-l">Mobile</label>

																	</div>-->
																</div>
															</div>
															<?php   if(isset($sketch_data) && count($sketch_data) > 0 && $sketch_data[0]['EmailNotification']['notification_type'] == 'sketches'){
																	echo $this->element('email_notification/sketch_notification', $sketch_data);
																} else {
																	echo $this->element('email_notification/html/sketch_notification');
																}
															?>
													</div>
												</div><?php   */ ?>




											</div>
										</div>
								</div>
							<div class="clearfix"></div>

						</div>
						</div>
							</div>
						</div>
					</div>
				<?php  echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal modal-success fade" id="modal_work" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog people-workblock-modal">
        <div class="modal-content"></div>
    </div>
</div>


<script type="text/javascript">
	$('html').addClass('no-scroll');
	$(() => {
		$('.nav.nav-tabs').removeAttr('style');

	    // RESIZE MAIN FRAME
	    ($.adjust_resize = function(){
	        var $ntab_menus = $('.bhoechie-tab');
	        // var $ntab_menus = $('.tab-content-wrapper');
	        $ntab_menus.animate({
	            minHeight: (($(window).height() - $ntab_menus.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $ntab_menus.offset().top) ) - 17
	        }, 1 );
	        var $ttab_menus = $('.setting-general');
	        // var $ntab_menus = $('.tab-content-wrapper');
	        $ttab_menus.animate({
	            minHeight: (($(window).height() - $ttab_menus.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $ttab_menus.offset().top) ) - 17
	        }, 1 );
	        /*var $stab_menus = $('.setting-available-scroll');
	        $stab_menus.animate({
	            minHeight: (($(window).height() - $stab_menus.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $stab_menus.offset().top) ) - 17
	        }, 1 );*/

	        var $setting_wrap = $('.setting-available');
	        var $list_wrap = $('.available-list-data');
	        $setting_wrap.animate({
	            minHeight: (($(window).height() - $setting_wrap.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $setting_wrap.offset().top) ) - 17
	        }, 1, () => {
	            // RESIZE LIST
	            $list_wrap.animate({
	                maxHeight: (($(window).height() - $list_wrap.offset().top) ) - 37
	            }, 1)
	        });

	    })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
	    $(".sidebar-toggle").on('click', function() {
	        $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })

	    $("#setting_tabs").on('show.bs.tab', function(e){
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1000);
	        history.pushState(null, null, $js_config.base_url + 'settings/notification');
	    })

	    <?php if(isset($userData) && !empty($userData)) { ?>
			<?php if(isset($userData['page_setting_toggle']) && empty($userData['page_setting_toggle'])) { ?>
				$('.page_on_off').prop('checked', false);
			<?php } ?>


			<?php if(isset($userData['landing_url']) && !empty($userData['landing_url'])) { ?>
				$('input[value="<?php echo $userData['landing_url']; ?>"]').prop('checked', true)
				$('option[value="<?php echo $userData['landing_url']; ?>"]').prop('selected', true)
			<?php }else{ ?>
			$('option[value="projets/lists"]').prop('selected', true)
			<?php }  ?>
		<?php } ?>

		/*$('body').delegate('.nav-link', 'click', function(event) {
			$that = $(this);
			$("#theme_name").val( $that.data('tval') );
		});*/


		$(".color-preview[data-theme=<?php echo $user_theme ?>]").find('i').show();

		$(".color-preview[data-theme=<?php echo $user_theme ?>]").addClass('theme-selected');

		$.set_current_theme = function( ) {
			var dfd = new $.Deferred();
			var theme_type = 'primary';
			if( $("#theme_name").val() != null ){
				theme_type = $("#theme_name").val();
			}

			var $selected = $('.theme-selected'),
				selected_theme = $.current_theme = ($selected.length) ? $selected.data('theme') : 'theme_default';

			var $img = $('header.main-header a.logo img'),
				logo = 'logo_white.png';
			if( $img.length ) {
				$img[0].src = $js_config.base_url + 'images/' + logo;
			}

			$.ajax({
				type:'POST',
				url: $js_config.base_url + 'settings/themes/',
				data: $.param({'selected_theme': selected_theme }),
				global: false,
				dataType: 'JSON',
				success: function(response) {
					if( response.success ) {
					    $.socket.emit("theme:setting:change", {userID: $js_config.USER.id, theme: selected_theme});

						dfd.resolve();
					}
					else {

					}
				}

			});
			return dfd.promise();
		}

		$('body').delegate('.color-preview', 'click', function(event) {
			event.preventDefault();

			var $others = $('.color-preview').not(this)
			$others.removeClass('theme-selected');
			$(this).addClass('theme-selected');
			$others.find('i').hide();
			$(this).find('i').show();

			var data = $(this).data(),
				theme = data.theme,
				ms_theme = $('.main-sidebar').data('theme'),
				mh_theme = $('.main-header').data('theme'),
				ch_theme = $('.open-chat-win').data('theme');

				$('.main-header').removeClass(mh_theme).addClass(theme)
				$('.main-sidebar').removeClass(ms_theme).addClass(theme)
        		$('.open-chat-win').removeClass(ch_theme).addClass(theme)

				$('.main-header').data({'theme': theme})
				$('.main-sidebar').data({'theme': theme})
        		$('.open-chat-win').data({'theme': theme})

			var $img = $('header.main-header a.logo img'),
				logo = 'logo_white.png'
			/* if( theme == 'theme_default' ) {
				logo = 'logo.png';
			} */
			if( $img.length ) {
				$img[0].src = $js_config.base_url + 'images/' + logo;
			}

		})



		$("#save_settings").on("click", function(a) {
			var $this = $(this);
	        a.preventDefault();
	        $this.prop('disabled', true);
	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + "settings/start_page",
	            data: {page_setting_toggle: 1, landing_url: $("select[name='landing_url']").val()},
	            dataType: "JSON",
	            success: function(a) {
	            	$.set_current_theme().done(function(){
	            		$this.prop('disabled', false);
	            	});
	            }
	        })
	    });

		setTimeout(function(){
			var active_tab = '<?php echo $current_tab; ?>';
			$('#setting_tabs a[href="#'+active_tab+'"]').tab('show');
		}, 200)

		$.unavailableDates;
		$.datesToDisable = function(date){
			if($.unavailableDates && $.unavailableDates != undefined && $.unavailableDates != null) {
				dmy = ('0' + date.getDate()).slice(-2) + "-" + (('0' + (date.getMonth()+1)).slice(-2)) + "-" + date.getFullYear();
				if ($.inArray(dmy, $.unavailableDates) < 0) {
				    return [true,"","Book Now"];
			  	} else {
				    return [false,"","Booked Out"];
			  	}
		  	}
		  	else{
		  		return [true,"","Book Now"];
		  	}
		}
		$( "#start_date" ).datepicker({
            dateFormat: 'dd M, yy',
            changeMonth: true,
            changeYear: true,
            beforeShowDay: $.datesToDisable,
			onSelect: function (dateString, txtDate) {
                //DisplayDate("Selected Date: " + dateString + "\nTextBox ID: " + txtDate.id);
				$('.error').text('');
            }
        });

		$('body').delegate('#add_avail', 'click', function(event) {
			event.preventDefault();
			$that = $(this);
			var jsDate = $('#start_date').datepicker('getDate');
			if (jsDate !== null) { // if any date selected in datepicker
			    jsDate instanceof Date; // -> true
			    jsDate.getDate();
			    jsDate.getMonth();
			    jsDate.getFullYear();
				passdate = jsDate.getDate()+'-'+(jsDate.getMonth()+1)+'-'+jsDate.getFullYear();
			}
			// $("#start_date").val()
			var $form = $('#modelFormProjectComment'),
				data = {effective: passdate, id: $("#id").val(), monday: $("select[name='monday']").val(), tuesday: $("select[name='tuesday']").val() , wednesday: $("select[name='wednesday']").val(), thursday: $("select[name='thursday']").val(), friday: $("select[name='friday']").val(), saturday: $("select[name='saturday']").val(), sunday: $("select[name='sunday']").val(), user_id : '<?php echo $this->Session->read('Auth.User.id');?>'};

			$.when(
				$.ajax({
					url: $js_config.base_url + 'settings/save_availability/',
					type: "POST",
					data: data,
					dataType: "JSON",
					global: false,
					success: function (response) {
						if(response.success) {

							$that.removeClass('disabled');
							$('.error').text('');
							$("#reset_avail").trigger('click');
							$.unavail_dates();
							$.user_hr_day();
						}
						else {
							if( ! $.isEmptyObject( response.msg ) ) {
								$('.error').text( response.msg[0]);
								$that.removeClass('disabled');
							}
						}

					}
				})
			).then(function( data, textStatus, jqXHR ) {
				if(data.success) {
					$.ajax({
						url: $js_config.base_url + 'settings/get_availability',
						type: "POST",
						data: $.param({}),
						global: false,
						success: function (responses) {
							$('.available-list-data', $('body')).html(responses)
							$that.removeClass('disabled');
							$('#add_avail').text('Add');
						}
					})
				}
			})
		})

	$("#reset_avail").click(function(){
		var d = "<?php echo date('d M, Y') ?>";
		$("#start_date").val(d);
		$("select[name='monday']").val(8);
		$("select[name='tuesday']").val(8);
		$("select[name='wednesday']").val(8);
		$("select[name='thursday']").val(8);
		$("select[name='friday']").val(8);
		$("select[name='saturday']").val(0);
		$("select[name='sunday']").val(0);
		$("#id").removeAttr('value');
		$('#add_avail').text('Add');
		$('.error').text('');
		$.unavailableDates = [];
		$.unavail_dates();
	});



	$('body').delegate('#delete-data', 'click', function(event) {
		event.preventDefault()
		var id = $(this).data('id');
		BootstrapDialog.show({
			title: 'Delete Availability',
			message: 'Are you sure you want to delete this Availability?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Delete',
					cssClass: 'btn-success',
					// autospin: true,
					action: function (dialogRef) {
						$.when(

							$.ajax({
								url: $js_config.base_url + 'settings/delete_data',
								data: $.param({ id: id }),
								type: 'post',
								global: true,
								dataType: 'json',
								success: function(response){
									$("#reset_avail").trigger('click');
									$.unavail_dates();
									$.user_hr_day();
								}
							})

							).then(function( data, textStatus, jqXHR ) {
								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);
								// dialogRef.getModalBody().html('<div class="loader"></div>');
								setTimeout(function () {
									$.ajax({
										url: $js_config.base_url + 'settings/get_availability/' ,
										type: "POST",
										data: $.param({}),
										//dataType: "html",
										global: false,
										success: function (responses) {
											$('.available-list-data', $('body')).html(responses)
											// $that.removeClass('disabled');
										}
									})
									dialogRef.close();
								}, 500);

							})
						}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {

						dialogRef.close();
					}
				}
			],
		})

	})


	/* $('body').delegate('.calendar-trigger', 'click', function(event) {
		$('#start_date').trigger('click');
	}) */

		$('body').delegate('#edit-data', 'click', function(event) {
			event.preventDefault()
			var id = $(this).data('id');
			$('#id').attr('value',id);
			$('#add_avail').text('Save');

			$.ajax({
				url: $js_config.base_url + 'settings/find_availability/'+id ,
				type: "POST",
				data: $.param({}),
				dataType: "json",
				global: false,
				success: function (responses) {

					$("#start_date").val(responses.effective);
					$("select[name='monday']").val(responses.monday);
					$("select[name='tuesday']").val(responses.tuesday);
					$("select[name='wednesday']").val(responses.wednesday);
					$("select[name='thursday']").val(responses.thursday);
					$("select[name='friday']").val(responses.friday);
					$("select[name='saturday']").val(responses.saturday);
					$("select[name='sunday']").val(responses.sunday);
				}
			})

			$.ajax({
				url: $js_config.base_url + 'settings/availability_dates',
				type: "POST",
				data: {id: id},
				dataType: "json",
				global: false,
				success: function (response) {
					if(response.success){
						$.unavailableDates = response.content;
					}
				}
			})

		});

		;($.unavail_dates = function(){
			$.ajax({
				url: $js_config.base_url + 'settings/availability_dates',
				type: "POST",
				data: {},
				dataType: "json",
				global: false,
				success: function (response) {
					if(response.success){
						$.unavailableDates = response.content;
					}
				}
			})
		})();

		$.user_hr_day = function(){
			$.ajax({
				url: $js_config.base_url + 'settings/user_hr_day',
				type: "POST",
				data: {},
				dataType: "json",
				global: false,
				success: function (response) {
					$('.work-availability').html(response.content + ' hr day');
				}
			})
		}

	})
</script>

<script type="text/javascript">
	$(() => {
		/*$monday = $('#monday').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            maxHeight: '402',
            nonSelectedText: 'Select Roles'
        });*/
        $('body').on('click', '.calendar-trigger', function(event) {
        	$(this).parent('.input-group:first').find('input#start_date').datepicker('show');
        	event.preventDefault();
        });
	})
</script>