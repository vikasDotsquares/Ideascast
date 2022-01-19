<style>
.tab_content {
	padding: 10px 15px;
}
.nav-tabs > li > a:hover {
	border-color: rgb(0, 0, 0);
}
.nav-tabs > li.active > a:hover {
	background: #d81b60 none repeat scroll 0 0;
	color: #fff;
	border-color: #d81b60 #d81b60 transparent;
}
.cnt-code {
	max-width: 70px;
}
.btn.active.focus, .btn.active:focus, .btn.focus, .btn.focus:active, .btn:active:focus, .btn:focus {
	outline: none;
}

.btn-on.active {
	background-color: #449d44;
	border-color: #398439;
	color: #fff;
	box-shadow: 0 3px 5px rgba(0, 0, 0, 0.125) inset;
}
.btn-off.active {
	background-color: #c9302c;
	border-color: #ac2925;
	color: #fff;
	box-shadow: 0 3px 5px rgba(0, 0, 0, 0.125) inset;
}








</style>

<?php echo $this->Html->css('projects/dropdown');  ?>
<?php echo $this->Html->css('projects/notifications');  ?>
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>

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

	/*
	$('body').delegate('.btn-on-off', 'click', function(event){

			event.preventDefault();
			$(this).find('.btn').toggleClass('active');
			$(this).find('.btn').toggleClass('active');
	})
	 */
})
</script>
<div class="row">

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

		<div class="box-content ">
			<div class="row ">
                <div class="col-xs-12">

                    <div class="box border-top margin-top" style="border-radius: 0px 0px 3px 3px;">

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
                        <div class="box-body clearfix list-shares" style="min-height: 550px;">
							<?php
								echo $this->Form->create('setStartPage', array('url' => ['controller' => 'settings', 'action' => 'start_page'], 'class' => '', 'id' => 'frmStartPage'));
							?>
								<div class="clearfix col-sm-12 col-md-12 col-lg=12 ">

									<div class="form-group clearfix">
										<label class="" for="notify_toggle">Notifications Setting:</label>
										<input type="checkbox" value="1" class="notify_on_off tipText" name="notify_toggle" id="notify_toggle" checked="checked">
									</div>
								</div>
								<!--<div class="clearfix col-sm-6 col-md-12 col-lg-6 ">

									<div class="form-group clearfix">
											<label style="vertical-align: top;" for="">Mob/Cell:</label>
											<div class="form-inline" style="display: inline-block;">
												<div class="input-group">
													<div class="input-group-addon">+</div>
													<input type="text" class="form-control cnt-code" id="" placeholder="">
												</div>
												<div class="form-group">
													<input type="tel" class="form-control" id="" placeholder="Enter Number">
												</div>
											</div>

									</div>
								</div> -->
							<?php  echo $this->Form->end(); ?>



							<ul id = "notifyTab" class = "nav nav-tabs">
								<li class = "active"><a href = "#project" data-toggle="tab" title="Project"><i class="fa fa-briefcase"></i> <span>Project</span></a></li>
								<li><a href = "#dashboard" data-toggle="tab" title="Dashboard"><i class="fa fa-briefcase"></i> <span>Dashboard</span></a></li>
								<li><a href = "#group" data-toggle="tab" data-original-title="Group"><i class="fa fa-users"></i> <span>Group</span></a></li>
								<li><a href = "#workspaces" data-toggle="tab" data-original-title="Workspace"><i class="fa fa-th"></i> <span>Workspace</span></a></li>
								<li><a href = "#element" data-toggle="tab" data-original-title="Element"><i class="fa fa-briefcase"></i> <span>Element</span></a></li>
								<li><a href = "#team_talk" data-toggle="tab" data-original-title="Team Talk"><i class="fa fa-microphone"></i> <span>Team Talk</span></a></li>
								<li><a href = "#to_dos" data-toggle="tab" data-original-title="To-dos"><i class="fa fa-list todo_icon"></i> <span>To-dos</span></a></li>
								<li><a href = "#chat" data-toggle="tab" data-original-title="Chat"><i class="fa fa-comment"></i> <span>Chat</span></a></li>
							</ul>
							<div id="notifyTabContent" class="tab-content tab_content">

								<div class="tab-pane fade in active" id = "project">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Project interest from Project Board</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_interest_board">
														<label for="email_project_interest_board"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_interest_board" checked>
														<label for="mob_project_interest_board"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Project sharing</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_sharing">
														<label for="email_project_sharing"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_sharing">
														<label for="mob_project_sharing"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">RAG status change</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_rag">
														<label for="email_project_rag"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_rag">
														<label for="mob_project_rag"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Project deleted</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_deleted">
														<label for="email_project_deleted"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_deleted">
														<label for="mob_project_deleted"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Project schedule change</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_schedule_change">
														<label for="email_project_schedule_change"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_schedule_change">
														<label for="mob_project_schedule_change"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Project schedule overdue</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_schedule_overdue">
														<label for="email_project_schedule_overdue"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_schedule_overdue">
														<label for="mob_project_schedule_overdue"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">New project member</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_new_member">
														<label for="email_project_new_member"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_new_member">
														<label for="mob_project_new_member"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">New project received</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_project_new_received">
														<label for="email_project_new_received"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_project_new_received">
														<label for="mob_project_new_received"></label>
													</div>
												</div>
											</div>
										</div>
									</div>




								</div>

								<div class="tab-pane fade" id="group">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Group request</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_group_request">
														<label for="email_group_request"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_group_request" checked>
														<label for="mob_group_request"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="tab-pane fade" id="workspaces">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Workspace schedule change</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_workspace_schedule_change">
														<label for="email_workspace_schedule_change"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_workspace_schedule_change" checked>
														<label for="mob_workspace_schedule_change"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Workspace deleted</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_workspace_deleted">
														<label for="email_workspace_deleted"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_workspace_deleted" checked>
														<label for="mob_workspace_deleted"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Workspace schedule overdue</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_workspace_schedule_overdue">
														<label for="email_workspace_schedule_overdue"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_workspace_schedule_overdue" checked>
														<label for="mob_workspace_schedule_overdue"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="tab-pane fade" id="element">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Element schedule change</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_element_schedule_change">
														<label for="email_element_schedule_change"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_element_schedule_change" >
														<label for="mob_element_schedule_change"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Element deleted</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_element_deleted">
														<label for="email_element_deleted"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_element_deleted" checked>
														<label for="mob_element_deleted"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Element schedule overdue</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_element_schedule_overdue">
														<label for="email_element_schedule_overdue"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_element_schedule_overdue" checked>
														<label for="mob_element_schedule_overdue"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Element sign-off</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_element_sign_off">
														<label for="email_element_sign_off"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_element_sign_off" checked>
														<label for="mob_element_sign_off"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Vote invitation request</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_vote_invitation_request">
														<label for="email_vote_invitation_request"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_vote_invitation_request" checked>
														<label for="mob_vote_invitation_request"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Vote reminder</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_vote_reminder">
														<label for="email_vote_reminder"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_vote_reminder" checked>
														<label for="mob_vote_reminder"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Vote removed</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_vote_removed">
														<label for="email_vote_removed"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_vote_removed" checked>
														<label for="mob_vote_removed"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Feedback invitation request</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_feedback_invitation_request">
														<label for="email_feedback_invitation_request"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_feedback_invitation_request" checked>
														<label for="mob_feedback_invitation_request"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Feedback reminder</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_feedback_reminder">
														<label for="email_feedback_reminder"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_feedback_reminder" checked>
														<label for="mob_feedback_reminder"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Feedback received</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_feedback_received">
														<label for="email_feedback_received"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_feedback_received" checked>
														<label for="mob_feedback_received"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="tab-pane fade" id="team_talk">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Wiki created</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_wiki_created">
														<label for="email_wiki_created"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_wiki_created" >
														<label for="mob_wiki_created"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Wiki update page request</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_wiki_page_request">
														<label for="email_wiki_page_request"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_wiki_page_request" >
														<label for="mob_wiki_page_request"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Blog created</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_blog_created">
														<label for="email_blog_created"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_blog_created" >
														<label for="mob_blog_created"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">Blog updated</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_blog_updated">
														<label for="email_blog_updated"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_blog_updated" >
														<label for="mob_blog_updated"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="tab-pane fade" id="to_dos">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">To-do request</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_todo_request">
														<label for="email_todo_request"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_todo_request" >
														<label for="mob_todo_request"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">To-Do sign-off</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_todo_signoff">
														<label for="email_todo_signoff"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_todo_signoff" >
														<label for="mob_todo_signoff"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">To-Do assigned</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_todo_assigned">
														<label for="email_todo_assigned"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_todo_assigned" >
														<label for="mob_todo_assigned"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">To-Do overdue</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_todo_overdue">
														<label for="email_todo_overdue"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_todo_overdue" >
														<label for="mob_todo_overdue"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div class="tab-pane fade" id="chat">

									<div class="row notify_header">
										<div class="col-sm-6"></div>
										<div class="col-sm-6">
											<div class="col-sm-6">Email</div>
											<div class="col-sm-6">Mobile/Cell</div>
										</div>
									</div>

									<div class="setting_row">
										<div class="col-sm-12 col-md-6 col-lg-6 col-head">New chat message</div>
										<div class="col-sm-12 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Email</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="email_new_chat_message">
														<label for="email_new_chat_message"></label>
													</div>
												</div>
												<div class="col-sm-12 col-md-6 col-section">
													<span class="sr_only text-bold">Mobile</span>
													<div class="switch">
														<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="mob_new_chat_message" >
														<label for="mob_new_chat_message"></label>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

							</div>



						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
