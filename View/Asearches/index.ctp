<?php
echo $this->Html->css('projects/smart_search', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->css('projects/dropdown');
echo $this->Html->css('projects/dd-menus');
echo $this->Html->css('projects/bs-selectbox/bootstrap-select');
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->script('projects/smart_search', array('inline' => true));
echo $this->Html->script('projects/plugins/dd-menus/dd-menus', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); 
 ?> 
<style>

</style>
<script type="text/javascript">
$(function(){
	$('body').delegate('.submit_list', 'click', function(event){
		event.preventDefault();
		
		var $this = $(this),
			$form = $this.closest('form#modelFormAddSearchList'),
			$title = $("#title", $form),
			title_text = $title.val();
			
		if( title_text != '' ) {
			$.ajax({
				url: $js_config.base_url + 'searches/save_people_list',
				type: "POST",
				data: $form.serialize(),
				dataType: "JSON",
				global: false,
				success: function (response) {
					if( response.success ) {
						$('#modal_box').modal('hide')
					}
					
				}, 
				
			})// end ajax
		}
		else {
			$title.parent().find('.error-message.text-danger').text('List title is required.')
		}
	})
})
</script>

<div class="row smart_search">
	<!-- Modal Large -->
	<div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
						<div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move;">
							<div class="col-xs-9">
								<div class="radio-left">
									<div class="radio radio-warning">
										<input type="radio" data-target="#people_search" checked="checked" value="1" class="fancy_input change_checkbox" name="search_type" id="radio_people">
										<label for="radio_people" class="fancy_labels">People</label>
									</div>
									<div class="radio radio-warning">
										<input type="radio" data-target="#content_search" value="2" class="fancy_input change_checkbox" name="search_type" id="radio_content">
										<label for="radio_content" class="fancy_labels">Content</label>
									</div>
								</div>
							</div>
							<div class="col-xs-3">
								<a class="btn btn-sm btn-danger pull-right" href="#" id="reset_search_type">Reset</a>
							</div>
						</div>
						<div class="box-body clearfix" >
						
							<div class="people_search search-section" id="people_search">
							
								<div class="clearfix" style="display: block; text-right">
									<span  class="btn btn-sm btn-warning pull-right create_people_list" data-selection="2" data-remote="<?php echo Router::Url(array("controller" => "searches", "action" => "create_people_list", "selection" => 2), true); ?>" style="margin-left: 5px"><i class="fa fa-plus"></i> Create People List</span>
									<span  class="btn btn-sm btn-success pull-right" id="open_list">Open List</span>
								</div>

								<ul class="nav nav-tabs">
									<li class="active"><a data-toggle="tab" href="#people_user"><i class="fa fa-user"></i> User</a></li>
									<li><a data-toggle="tab" href="#people_skills"><i class="fa fa-cogs"></i> Skills</a></li>
									<li><a data-toggle="tab" href="#people_group"><i class="fa fa-users"></i> Group</a></li>
									<li><a data-toggle="tab" href="#people_recommend"><i class="fa fa-thumbs-o-up"></i> Recommend</a></li> 
								</ul>

								<div class="tab-content">
									<div id="people_user" class="tab-pane fade in active">
										<div class="col-sm-12 margin-top">
											<div class="col-sm-12 col-md-6 col-lg-5">
												<label class="input-label col-sm-2" style="margin-bootom:0;" >
													<span>People: </span>
													<span>
														<i class="fa fa-plus btn btn-sm btn-success create_people_list" data-selection="1" data-target="#people_user_select" data-remote="<?php echo Router::Url(array("controller" => "searches", "action" => "create_people_list", "selection" => 1), true); ?>"></i>
													</span>
												</label>
												<div class="col-sm-10">
													<?php
														$users = $this->Search->users_list();
														// pr($users);
														$userArr = $selected = null;
														if ( $users ) {
															foreach($users as $k => $val){
																$userArr[$val["User"]["id"]] = $val[0]["User__name"];
															}
														}
													echo $this->Form->select("people_user_id", $userArr, array("title"=>"Select User", "multiple"=>"multiple", "default" => $selected, "id"=>"people_user_select", "class"=>"form-control aqua", "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));
													?>
												</div>
											</div>
											<div class="col-sm-12 col-md-6 col-lg-7">

											</div>
										</div>
									</div>
									<div id="people_skills" class="tab-pane fade">
										<div class="col-sm-12 margin-top">
											<div class="col-sm-12 col-md-6 col-lg-6">
												<div class="row">
													<label class="input-label col-sm-2" style=" margin-bootom:0;" >
														<span>Skills: </span>
														<span>
															<i class="fa fa-search btn btn-sm btn-default tipText" title="Search for People" id="btn_user_by_skills"></i>
															<i class="fa fa-times btn btn-sm btn-danger tipText" title="Clear List" id="clear_skills"></i>
														</span>
													</label>
													<div class="col-sm-9">
														<textarea rows="3" cols="50" name="txa_people_skills" id="txa_people_skills" class="form-control" placeholder=""></textarea>
													</div>
												</div>
											</div>
											<div class="col-sm-12 col-md-6 col-lg-6">
												<div class="row">
													<label class="input-label col-sm-2" style=" margin-bootom:0;" >
														<span>Names: </span>
														<span>
															<i class="fa fa-plus btn btn-sm btn-success create_people_list" data-selection="1" data-target="#skill_user_select" data-remote="<?php echo Router::Url(array("controller" => "searches", "action" => "create_people_list", "selection" => 1), true); ?>"></i>
														</span>
													</label>
													<div class="col-sm-9">
													<?php
														$users = $this->Search->users_list();
														// pr($users);
														$userArr = $selected = null;
														if ( $users ) {
															foreach($users as $k => $val){
																$userArr[$val["User"]["id"]] = $val[0]["User__name"];
															}
														}
														echo $this->Form->select("skill_user_id", $userArr, array("title"=>"Select User", "multiple"=>"multiple", "default" => $selected, "id"=>"skill_user_select", "class"=>"form-control aqua", "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));
													?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="people_group" class="tab-pane fade">
										<div class="col-sm-12 margin-top">
											<div class="col-sm-12 col-md-6 col-lg-6">
												<label class="input-label col-sm-2" style=" margin-bootom:0;" >Group: </label>
												<div class="col-sm-10">
													<label class="custom-dropdown"  style="width: 100%; margin-top: 3px;">
														<select class="aqua" name="group_select" id="group_select">
															<option value="">Select</option>
															<?php if(isset($all_groups) && !empty($all_groups)){  ?>
																<?php foreach($all_groups as $key => $val ) { ?>
																	<?php if( !empty($key)){
																		$v = ( strlen($val) > 65 ) ? substr($val, 0, 65).'...' : $val;
																		?>
																		<option value="<?php echo $key; ?>"><?php echo $v; ?></option>
																	<?php } ?>
																<?php } ?>
															<?php } ?>
														</select>
													</label>
												</div>
											</div>
											<div class="col-sm-12 col-md-6 col-lg-6">
												<label class="input-label col-sm-2" style=" margin-bootom:0;" >
													<span>People: </span>
													<span>
														<i class="fa fa-plus btn btn-sm btn-success create_people_list" data-selection="1" data-target="#group_user_select" data-remote="<?php echo Router::Url(array("controller" => "searches", "action" => "create_people_list", "selection" => 1), true); ?>"></i>
													</span>
												</label>
												<div class="col-sm-10">
													<?php
														$users = $this->Search->users_list();
														// pr($users);
														$userArr = $selected = null;
														if ( $users ) {
															foreach($users as $k => $val){
																$userArr[$val["User"]["id"]] = $val[0]["User__name"];
															}
														}
														echo $this->Form->select("group_user_id", $userArr, array("title"=>"Select User", "multiple"=>"multiple", "default" => $selected, "id"=>"group_user_select", "class"=>"form-control aqua", "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));
													?>
												</div>
											</div>
										</div>
									</div>
									<div id="people_recommend" class="tab-pane fade">
										<div class="col-sm-12 margin-top">
											<div class="col-sm-12 col-md-6 col-lg-6">
												<div class="row">
													<label class="input-label col-sm-2" style="margin-bootom:0;">
														<span>Skills: </span>
														<span>
															<i class="fa fa-search btn btn-sm btn-default tipText" title="Search People" id="btn_user_by_keyword"></i>
															<i class="fa fa-times btn btn-sm btn-danger tipText" title="Clear List" id="clear_keyword"></i>
														</span>
													</label>
													<div class="col-sm-9">
														<textarea rows="5" cols="50" name="txa_keyword" id="txa_keyword" class="form-control" placeholder="" style="resize: vertical;"></textarea>
													</div>
												</div>
											</div>
											<div class="col-sm-12 col-md-6 col-lg-6">
												<div class="row">
													<label class="input-label col-sm-2" style=" margin-bootom:0;" >
														<span>Names: </span>
														<span>
															<i class="fa fa-plus btn btn-sm btn-success create_people_list" data-selection="1" data-target="#recommend_user_select" data-remote="<?php echo Router::Url(array("controller" => "searches", "action" => "create_people_list", "selection" => 1), true); ?>"></i>
														</span>
													</label>
													<div class="col-sm-9">
													<?php
														$users = $this->Search->users_list();
														// pr($users);
														$userArr = $selected = null;
														if ( $users ) {
															foreach($users as $k => $val){
																$userArr[$val["User"]["id"]] = $val[0]["User__name"];
															}
														}
														echo $this->Form->select("recommend_user_id", $userArr, array("title"=>"Select User", "multiple"=>"multiple", "default" => $selected, "id"=>"recommend_user_select", "class"=>"form-control aqua", "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));
													?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								
							<div class="col-xs-12 padding-top margin-top border-top" id="create_list_box">
								<div class="col-xs-4" >
									<label style="width: 100%;">Lists:</label>
									
									<div href="#" data-dd="#my_saved_people" class="btn btn-sm dd-trigger">My Saved List</div>
									<ul class="dd-menu" id="my_saved_people">
									<?php if(isset($my_search_list) && !empty($my_search_list)) { ?>
										<?php foreach($my_search_list as $key => $val) { ?>
											<li class="">
												<i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
												<a href="#" data-id="<?php echo $key; ?>"><?php echo $val; ?></a>
											</li>
										<?php } ?>
									<?php }else{ ?>
										<li class=""> 
											<a href="#">No saved list</a>
										</li> 
									<?php } ?>
									</ul>
									
								</div>
								<div class="col-xs-3"> </div>
								<div class="col-xs-5">
									<label style="width: 100%;">People:</label>
									
									<div href="#" data-dd="#my_people_list" class="btn btn-sm dd-trigger" style="width: 95%;">People in List</div>
									<ul class="dd-menu" id="my_people_list">
										<li class="">
											<i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
											<i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i>
											<a href="#">quae ab</a>
										</li>
										<li class="">
											<i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
											<i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i>
											<a href="#">Eaque ipsa</a>
										</li> 
										<li class="">
											<i class="fa fa-trash i-btn i-btn-sm i-btn-red"></i>
											<i class="fa fa-user i-btn i-btn-sm i-btn-maroon"></i>
											<a href="#">illo inventore</a>
										</li> 
									</ul>
									<span class="fa fa-spinner fa-pulse" id="my_list_spinner"></span>
									
								</div>
							</div>
							
							</div>

							<div class="content_search search-section" id="content_search">

								<div class="keyword_form_wrapper clearfix">
									<div class="col-sm-12 col-md-12 col-lg-6"> 
										<div class="form-group row">
											<label class="col-sm-2 form-control-label " for="inp_keyword">Keyword: </label>
											<div class="col-sm-10 ">
												<input type="text" class="form-control" id="inp_keyword" placeholder="Keyword">
											</div>
										</div>
									</div>
									<div class="col-sm-12 col-md-12 col-lg-6">
										<div class="radio-left">
											<div class="radio radio-warning">
												<input type="radio" data-target="#people_search" checked="checked" value="1" class="fancy_input" name="keyword_type" id="radio_exw_any">
												<label for="radio_exw_any" class="fancy_labels">Exact words, any order </label>
											</div>
											 <div class="radio radio-warning">
												<input type="radio" value="2" class="fancy_input" name="keyword_type" id="radio_exw_exo">
												<label for="radio_exw_exo" class="fancy_labels">Exact words, exact order</label>
											</div> 
											<div class="radio radio-warning">
												<input type="radio" value="0" class="fancy_input" name="keyword_type" id="radio_any">
												<label for="radio_any" class="fancy_labels">Any words</label>
											</div>
										</div>
									</div>
								</div>
								
								 
									<div class="btn-group option_wrapper">
										<button type="button" class="btn btn-danger show_tabs disabled" data-target="#project_search_block">Projects</button>
										<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="sr-only">Toggle Dropdown</span>
											<span class="fa fa-arrow-down"></span>
										</button>
										<ul class="dropdown-menu button-menus project_dropdown">
											<li>
												<a href="#" class="" data-value="1" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="1" class="fancy_input change_checkbox" name="project_search_option" id="project_search_option_1">
														<label for="project_search_option_1" class="fancy_labels"> Yes Participating</label>
													</div>
												</a>
											</li>
											<li>
												<a href="#" class="" data-value="2" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="2" class="fancy_input change_checkbox" name="project_search_option" id="project_search_option_2">
														<label for="project_search_option_2" class="fancy_labels"> Not Participating</label>
													</div>
												</a>
											</li>
											<li>
												<a href="#" class="" data-value="3" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="3" class="fancy_input change_checkbox" name="project_search_option" id="project_search_option_3">
														<label for="project_search_option_3" class="fancy_labels"> Yes and No</label>
													</div>
												</a>
											</li>
										</ul>
									</div>

									<div class="btn-group option_wrapper">
										<button type="button" class="btn btn-danger show_tabs disabled" data-target="#team_talk_search_block">Team Talk</button>
										<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="sr-only">Toggle Dropdown</span>
											<span class="fa fa-arrow-down"></span>
										</button>
										<ul class="dropdown-menu button-menus team_talk_dropdown">
											<li>
												<a href="#" class="" data-value="1" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="1" class="fancy_input" name="team_talk_search_option" id="team_talk_search_option_1">
														<label for="team_talk_search_option_1" class="fancy_labels"> Yes Participating</label>
													</div>
												</a>
											</li>
											<li>
												<a href="#" class="" data-value="2" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="2" class="fancy_input" name="team_talk_search_option" id="team_talk_search_option_2">
														<label for="team_talk_search_option_2" class="fancy_labels"> Not Participating</label>
													</div>
												</a>
											</li>
											<li>
												<a href="#" class="" data-value="3" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="3" class="fancy_input" name="team_talk_search_option" id="team_talk_search_option_3">
														<label for="team_talk_search_option_3" class="fancy_labels"> Yes and No</label>
													</div>
												</a>
											</li>
										</ul>
									</div>

									<div class="btn-group option_wrapper">
										<button type="button" class="btn btn-danger show_tabs disabled" data-target="#conversation_search_block">Conversations</button>
										<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="sr-only">Toggle Dropdown</span>
											<span class="fa fa-arrow-down"></span>
										</button>
										<ul class="dropdown-menu button-menus conversation_dropdown">
											<li>
												<a href="#" class="" data-value="1" tabIndex="-1">
													<div class="radio radio-warning">
														<input type="radio" value="1" class="fancy_input" name="conversation_search_option" id="conversation_search_option_1">
														<label for="conversation_search_option_1" class="fancy_labels"> Include in Search</label>
													</div>
												</a>
											</li>
										</ul>
									</div>
									
									<button type="button" class="btn btn-success disabled" id="btn_search_content">Search</button>
								
								<div class="content_search_sections" id="project_search_block">
									<ul class="nav nav-tabs">
										<li class="active"><a data-toggle="tab" href="#content_projects"><i class="fa fa-user"></i> Project <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_workspaces">Workspaces <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_tasks">Tasks <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_links">Links <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_notes">Notes <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_documents">Documents <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_mindmaps">Mind Maps <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_decisions">Decisions <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_feedback">Feedback <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_votes">Votes <span>(0)</span></a></li>
										<li><a data-toggle="tab" href="#content_todos">To-dos <span>(0)</span></a></li>
									</ul>

									<div class="tab-content">
										<div id="content_projects" class="tab-pane fade in active">
											<p>Project</p>
										</div>
										<div id="content_workspaces" class="tab-pane fade">
											<p>Workspace</p>
										</div>
										<div id="content_tasks" class="tab-pane fade">
											<p>Tasks</p>
										</div>
										<div id="content_links" class="tab-pane fade">
											<p>Links</p>
										</div>
										<div id="content_notes" class="tab-pane fade">
											<p>Notes</p>
										</div>
										<div id="content_documents" class="tab-pane fade">
											<p>Documents</p>
										</div>
										<div id="content_mindmaps" class="tab-pane fade">
											<p>Mind maps</p>
										</div>
										<div id="content_decisions" class="tab-pane fade">
											<p>Decisions</p>
										</div>
										<div id="content_feedback" class="tab-pane fade">
											<p>Feedback</p>
										</div>
										<div id="content_votes" class="tab-pane fade">
											<p>Votes</p>
										</div>
										<div id="content_todos" class="tab-pane fade">
											<p>To-dos</p>
										</div>
									</div>
								</div>

								<div class="content_search_sections" id="team_talk_search_block">
									<ul class="nav nav-tabs">
										<li class="active"><a data-toggle="tab" href="#content_wiki">Wiki <span>(0)</span></a></li> 
										<li><a data-toggle="tab" href="#content_blog">Blog Posts <span>(0)</span></a></li>
									</ul>

									<div class="tab-content">
										<div id="content_wiki" class="tab-pane fade in active">
											<p>Wiki</p>
										</div>
										<div id="content_blog" class="tab-pane fade">
											<p>Blog</p>
										</div>
									</div>
								</div>

								<div class="content_search_sections" id="conversation_search_block">
									<ul class="nav nav-tabs">
										<li class="active"><a data-toggle="tab" href="#content_mail">Mail <span>(0)</span></a></li> 
										<li><a data-toggle="tab" href="#content_chat">Live Chat <span>(0)</span></a></li>
									</ul>

									<div class="tab-content">
										<div id="content_mail" class="tab-pane fade in active">
											<p>Mail</p>
										</div>
										<div id="content_chat" class="tab-pane fade">
											<p>Chat</p>
										</div>
									</div>
								</div>
							</div>

							<div class="col-xs-12 padding-top margin-top border-top" id="keyword_search_result">
								
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>

    </div>
</div>
