
<?php $named = (isset($this->params['named']['project']) && !empty($this->params['named']['project'])) ? $this->params['named']['project'] : ''; ?>

<script>
$(function(){

	        $(document).ajaxSend(function (e, xhr) {
				e.stopImmediatePropagation();
                window.theAJAXInterval = 1;
                // $("#ajax_overlay_text").textAnimate("..........");
                $(".ajax_overlay_preloader").hide()
				e.preventDefault();


            })
            .ajaxComplete(function (e) {
					e.stopImmediatePropagation();
                    $(".ajax_overlay_preloader").hide();
					e.preventDefault();
				e.stopPropagation();

            });
			$.ajaxSetup({
                global: false,
                headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                }
            })


	var $c_status;
	$.fn.modal.Constructor.prototype.enforceFocus = function() {
		modal_this = this
		$(document).on('focusin.modal', function (e) {
			if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
			&& !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
			&& !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
				modal_this.$element.focus()
			}
		})
	};
})
$js_config.base_url = '<?php echo SITEURL; ?>';
</script>
<style>
.radio-left > .form-group{ margin-bottom : 0;}
</style>
<?php

echo $this->Html->css('projects/team_talk', array('inline' => true));
echo $this->Html->css('projects/dropdown', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->css('projects/task_lists', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/marks/jquery.mark.min', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->script('canvas', array('inline' => true));
echo $this->Html->script('canvas2image', array('inline' => true));

?>
<script>
$(function(){

    $('body').delegate('#people_change', 'change', function(event){
		event.preventDefault();

		var $that = $(this),
				data = $that.data(),
				href = data.remote,
				blog_id = $that.attr("data-blogid"),
				project_id = $that.attr("data-projectid")

			if( href != '' ) {
				$.ajax({
						url: href,
						type: "POST",
						data: $.param({user_id:$that.val(),project_id:project_id,blog_id:blog_id}),
						global: true,
						success: function (response) {
							$("#people #blog-update-comment-list").html(response)
						}
				})
			}

	})

    $c_status = null;
	$(".checkbox_on_off").checkboxpicker({
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


	$.element_status = $('[name=element_status]').multiselect({
		buttonClass					: 'btn aqua',
		buttonWidth					: '100%',
		checkboxName				: 'element_status_val',
		includeSelectAllOption		: true,
		nonSelectedText				: 'No Status Selected',
		onChange : function(option, checked, select) {
			// console.log('onChange: ' )
		}
	});

	$.strip_tags = function(value) {
		var body = value || '';
		var regex = /(<([^>]+)>)/ig
		return body.replace(regex, "");
	}

	/* $.project_id = $('[name=project_id]').multiselect({
		buttonClass					: 'btn aqua',
		buttonWidth					: '100%',
		checkboxName				: 'project_id',
		includeSelectAllOption		: true,
        selected					: '',
		nonSelectedText				: 'No Projects',
		onChange : function(option, checked, select) {
			var project_id = option.val();
			//$.get_wsp_area(project_id)
		},
	}); */

	$('body').delegate('[name=project_type]', 'change', function(e) {
		e.preventDefault();
		console.log($(this))

		var $that 			= $(this),
			value 			= $that.val(),
			params 			= {type: value},
			$prj_spiner 	= $('#ProjectId').parent().find('.loader-icon');
			//$wsp_spiner 	= $('#WorkspaceId').parent().find('.loader-icon'),
			//$area_spiner 	= $('#AreaId').parent().find('.loader-icon');

		$prj_spiner.show()

		$.when(

					$.ajax({
						url: $js_config.base_url + 'team_talks/list_projects',
						type: "POST",
						data: $.param(params),
						dataType: "JSON",
						global: false,
						success: function (response) {

							/* var pathname = window.location.pathname.split("/");
							var filename = pathname[pathname.length-1];

							var named_project = filename.split(':');
							if(named_project.length > 0){
								filename = named_project[1];
							}

							if(pathname[pathname.length-1] == ''){
								var filename = pathname[pathname.length-2];
							}else if(pathname[pathname.length-1] != '' && $.isNumeric(pathname[pathname.length-1])){
								var filename = pathname[pathname.length-1];
							} */

							var filename = '<?php echo $named; ?>';

							var selectValues = response.content;
							$('#ProjectId').empty();

							if( selectValues != null ) {
								$('#ProjectId').append(function() {
									var output = '';

									$.each(selectValues, function(key, value) {
										var sel = '';
										if(key == filename){
											sel = 'selected="selected"';
										}
										if($.strip_tags(value) != '')
											output += '<option '+sel+' value="' + key + '">' + $.strip_tags(value) + '</option>';
									});
									return output;
								});
							}
							else {
								$('#ProjectId').append('<option value="">No Project</option>')
							}

						//	$('#ProjectId').multiselect('rebuild');

						}
					})

				).then(function( data, textStatus, jqXHR ) {
					$prj_spiner.hide()

					var project_id = $("#ProjectId option:first").val();
					$( "#ProjectId" ).trigger( "change" );

				});
		})
		  $('body').delegate('[id=ProjectId]', 'change', function(e) {
			e.preventDefault();
			//console.log($(this).val())
			var $that 			= $(this),
			value 			= $that.val(),
			params 			= {project_id: value},
			$prj_spiner 	= $('#ProjectId').parents('.project_selection').find('.loader-icon');

			$prj_spiner.show();
			// For Project Image =====================================================
			$.ajax({
				url: $js_config.base_url + 'projects/project_header_image/' + value,
				type: "POST",
				data: $.param({}),
				dataType: "JSON",
				global: false,
				success: function (response) {
				 $("#project_header_image").html(response)
				}
			});
			// ========================================================================

		$.when(

			$.ajax({
				url: $js_config.base_url + 'team_talks/projects_list',
				type: "POST",
				data: $.param(params),
				dataType: "HTML",
				global: false,
				success: function (response) {
					$('#box_body').html(response);
				}
			})

		).then(function( data, textStatus, jqXHR ) {
			$prj_spiner.hide()

			var project_id = $("#ProjectId option:first").val();

		});


		})



	$('#modal_edit_blogComments').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	 //console.log("reoved");
	});

	$('#modal_add_blogComments').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});

	 $('.modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html("");
    });

	 $('#popup_modal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html("");
    });

	/* ******************************** BOOTSTRAP HACK *************************************
	 * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
	 * */
	var originalLeave = $.fn.popover.Constructor.prototype.leave;
	$.fn.popover.Constructor.prototype.leave = function(obj){
		var self = obj instanceof this.constructor ?
		obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
		var container, timeout;

		originalLeave.call(this, obj);
		if(obj.currentTarget) {

			// container = $(obj.currentTarget).siblings('.popover')
			container = $(obj.currentTarget).data('bs.popover').tip()
			timeout = self.timeout;
			container.one('mouseenter', function(){
				//We entered the actual popover – call off the dogs
				clearTimeout(timeout);
				//Let's monitor popover content instead
				container.one('mouseleave', function(){
					$.fn.popover.Constructor.prototype.leave.call(self, self);
				});
			})
		}
	};
	/*
	 * End Popover
	 * ******************************** BOOTSTRAP HACK *************************************
	 * */


})

</script>
<style>

</style>
<?php
//pr($this->request);
?>
<!-- OUTER WRAPPER	-->
<div class="row">

	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php   echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 6px 0px;">
                        <span><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

		<span id="project_header_image">
		   <?php
			/* if( isset( $project_id ) && !empty( $project_id ) ) {
			 echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id));
			} */
		   ?>
		</span>

		<!-- MAIN CONTENT -->
		<div class="box-content idea-team-talk">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder">
						<?php echo $this->Session->flash(); ?>
						<!-- CONTENT HEADING -->
                        <div class="box-header task-list-header" style="background: #efefef none repeat scroll 0 0; border-top:none; border-radius: 0px 0px 0 0 !important; border:1px solid #ddd !important;">

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>

							<div class="modal modal-success fade" id="modal_edit_blogpost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>
							<div class="modal modal-success fade" id="modal_edit_blogpost_md" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-md">
									<div class="modal-content"></div>
								</div>
							</div>
							 <!-- MODAL BOX WINDOW -->

							<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="popup_modal" class="modal modal-success fade ">
								<div class="modal-dialog">
									<div class="modal-content modal-lg"></div>
								</div>
							</div>

							<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_edit_blogComments" class="modal modal-success fade ">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

							<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_edit_blogComments_list" class="modal modal-success fade ">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade " id="popup_modal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>
							<!-- Modal For Create Wiki -->

							<div class="modal modal-success fade " id="modal_create_wiki" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>

							<div class="modal modal-success fade " id="modal_create_blogpost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content"></div>
								</div>
							</div>


							<div id="confirm_box_img_del" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header bg-red">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title"> Delete confirmation</h4>
										</div>
										<div class="modal-body">
											<p>Do you want to delete To-do attachment you sure to before click delete?</p>
											<p class="text-warning"><small>If you click on delete, your To-do attachment will be lost.</small></p>
										</div>
										<div class="modal-footer">
											<button type="button" id="delete-yes" class="btn btn-success">Delete</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal modal-success fade " id="modal_add_blogComments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

							<!-- FILTER BOX -->

							<?php
							if(isset($project_id) && !empty($project_id)) {
								$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id'));
							//echo $project_id." ".$cky;
							?>
							<script>
							$(function(){
								$c_status ='<?php echo $cky; ?>';

								if($c_status == 'm_project'){
									$('[for=project_type_my]').trigger('click');
									 $('#project_type_my').trigger('change');
								}else if($c_status == 'r_project'){
									$('[for=project_type_rec]').trigger('click');
									 $('#project_type_rec').trigger('change');
								}else if($c_status == 'g_project'){
									$('[for=project_type_group]').trigger('click');
									 $('#project_type_group').trigger('change');
								}
							   //console.log($c_status);
								$(".fancy_input").click(function(e) {
									$thisdata = $(this);

									setTimeout(function(){

										$("#project_report_link").attr("href", $js_config.base_url +"projects/reports/"+$("#ProjectId").val())
										$("#dashboard_link").attr("href", $js_config.base_url +"projects/objectives/"+$("#ProjectId").val())


										if($thisdata.attr("id") == 'project_type_my'){
											$c_status = 'm_project';
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/m_project:"+$("#ProjectId").val())
										}
										else if($thisdata.attr("id") == 'project_type_rec'){
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/r_project:"+$("#ProjectId").val())
											 $c_status = 'r_project';
										}
										else if($thisdata.attr("id") == 'project_type_group'){
											$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/g_project:"+$("#ProjectId").val())
											$c_status = 'g_project';
										}

									},2000)
								})
							})

							</script>
							<?php }else{ ?>

							<script>
							 $(function(){
									$('#project_type_my').trigger('change');
									$('[for=project_type_my]').trigger('click');
							})
							</script>
							<?php } ?>

								<!--<div class="radio-left row-first">
									<div class="form-group clearfix">
										<div class="radio radio-warning">
											<input type="radio" id="project_type_my" name="project_type" class="fancy_input" value="1"   />
											<label class="fancy_labels" for="project_type_my">My Projects</label>
										</div>
										<div class="radio radio-warning">
											<input type="radio"   id="project_type_rec" name="project_type" class="fancy_input"  value="2" checked />
											<label class="fancy_labels" for="project_type_rec">Received Projects</label>
										</div>
										<div class="radio radio-warning">
											<input type="radio"   id="project_type_group" name="project_type" class="fancy_input"  value="3" checked />
											<label class="fancy_labels" for="project_type_group">Group Received Projects</label>
										</div>
									</div>
								</div>-->
								<div class="radio-left  project_selection select-box-wrap-top" style="position:relative">
									<label style="font-weight:normal;">Projects</label>
									<label class="custom-dropdown">
									<select id="ProjectId"  name="project_id" class="project_select aqua"   placeholder="Select Project">
									<option value="">Select Project</option>
									<?php if( isset($projectlists) && !empty($projectlists) ){
										foreach( $projectlists as $key => $pval ){
										?>
										<option value="<?php echo $key;?>" <?php if( !empty($project_id) && $project_id == $key ){?> selected="selected"<?php } ?> ><?php echo $pval;?></option>
									<?php }
									}?>
									</select>
									</label>
									<!-- <span class="loader-icon fa fa-spinner fa-pulse" style="right:-20px;"></span> -->
								</div>

						</div>
						<div class="box noborder margin-top" id="box_body"></div>

					</div>

				</div>
			</div>

		</div>

	</div>
</div>

<!-- Modal Large -->
     				   <div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     					<div class="modal-dialog modal-md">
     					     <div class="modal-content"></div>
     					</div>
     				   </div>

<!-- /.modal -->


<script>
$(function(){
	$('[id=ProjectId]').trigger('change');

	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
	$("#upload_user_blog_document").attr('data-bid','');
	$("#upload_user_blog_document").attr('data-target','');
	$("#upload_user_blog_document").attr('data-remote','');
	$("#upload_user_blog_document").addClass('disabled_comment_list');

	$("body").delegate("#blog_read", 'click', function (e) {
		e.preventDefault();

		$("#upload_user_blog_document").attr('data-bid','');
		$("#upload_user_blog_document").attr('data-target','');
		$("#upload_user_blog_document").attr('data-remote','');
		$("#upload_user_blog_document").addClass('disabled_comment_list');

		$('.ttsearch > input').show();
		$('.ttsearch > input').val('');
		$("#blog_user_lists").show();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").hide();



		var project_id = $(this).data('project');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_list";

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent1").show();
					$('#accordion2').html(response);
				}
			}
		});
		return;
	});

	$("body").delegate("#blog_dashboard", 'click', function (e) {
		e.preventDefault();

		$("#upload_user_blog_document").attr('data-bid','');
		$("#upload_user_blog_document").attr('data-target','');
		$("#upload_user_blog_document").attr('data-remote','');
		$("#upload_user_blog_document").addClass('disabled_comment_list');

		$('.ttsearch > input').hide();
		$('.ttsearch > input').val('');
		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").show();

		var project_id = $(this).data('project');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_dashboard";

		dashboard_Blog_user_list(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent6").show();
					$('#tabContent6').html(response);
				}
			}
		});
		return;
	});

	$("body").delegate(".userDashboardBlogcount", 'click', function (e) {
		e.preventDefault();

		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").show();

		var project_id = $(this).data('project');
		var user_id = $(this).data('value');
		var ulistby = $(this).data('listby');
		var actionURL =  $js_config.base_url+"team_talks/blog_dashboard";

		//dashboard_Blog_user_list(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id, 'listby':ulistby},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent6").show();
					$('#tabContent6').html(response);
				}
			}
		});
		return;
	});

	$("body").delegate(".blog_filterby_days", 'change', function (e) {
		e.preventDefault();

		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").show();

		var project_id = $(this).data('project');
		var user_id = $(this).data('value');
		var bfilterby = $(this).val();
		var actionURL =  $js_config.base_url+"team_talks/blog_dashboard";

		dashboard_Blog_user_list(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id, 'filterby':bfilterby},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent6").show();
					$('#tabContent6').html(response);
				}

				$("#BlogCreated").val(bfilterby);

			}
		});
		return;
	});


	$("body").delegate("#blog_admin", 'click', function (e) {
		e.preventDefault();

		$("#upload_user_blog_document").attr('data-bid','');
		$("#upload_user_blog_document").attr('data-target','');
		$("#upload_user_blog_document").attr('data-remote','');
		$("#upload_user_blog_document").addClass('disabled_comment_list');

		$('.ttsearch > input').hide();
		$('.ttsearch > input').val('');
		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").show();
		$("#dashboardblog_lists").hide();

		var project_id = $(this).data('project');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_admin";

		getAdminBlogslist(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent7").show();
					$('#tabContent7').html(response);
				}
			}
		});
		return;
	});

	$("body").delegate(".blog_admin_comment", 'click', function (e) {
		e.preventDefault();

		var project_id = $(this).data('project');
		var blog_id = $(this).data('blogid');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_admin";
		var blog_type = $(this).data('ctype');
		var blog_user_id = $(this).data('bloguser');

		//getAdminBlogslist(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id, 'blog_id':blog_id,'blog_type':blog_type,'blog_userid':blog_user_id},
			global:false,
			success:function(response){

				//console.log(response.blog_id);

				if( response ){

					$('#tabContent7').html(response);
					$("#tabContent7").show();

					if(blog_type == 'show_document'){

						$(".page-collapse-"+blog_id).trigger('click');
						$('.all_docT').trigger('click');

					}else if(blog_type == 'show_comment'){

						$(".page-collapse-"+blog_id).trigger('click');
						$('.all_comT').trigger('click');

					}else if(blog_type == 'show_all_comment'){

						$(".page-collapse-"+blog_id).trigger('click');
						$('.all_comT').trigger('click');

					}else if(blog_type == 'show_all_document'){

						$(".page-collapse-"+blog_id).trigger('click');
						$('.all_docT').trigger('click');

					} else {

						$(".page-collapse-"+blog_id).trigger('click');
						$('.all_blgT').trigger('click');

					}



				}

			}
		});
		return;
	});

	$("body").delegate("#confirm_admin_coment_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");


		var params = {"comment_id" : $(this).data('value')};
		var project_id = $(this).data('projectid');
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this comment?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/blog_comment_delete',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: true,
								success: function (response) {
									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();
										var com_counter	= $( "#blog_comment_lists .comments ul.blog-comment-lists li" ).size();
										/* if( com_counter == 0 ){
											$("#admin_cmt_list").html('<li class="padding-left borderBT_none">No Comments</li>');
										} */
										getAdminBlogslist(project_id);

									})
								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();

						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});

	$("body").delegate("#confirm_admin_doc_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var params = {"document_id" : $(this).data('value')};
		var projectid = $(this).data('projectid');
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this document?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/document_delete',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: true,
								success: function (response) {

									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();

										/* var document_li_counter_dca	= $( "#tabContent7 .idea-doc-list ul#accordion li div.list-group-item" ).size();

										if( document_li_counter_dca == 0 ){
											$("#admin_doc_list").html('<li class="padding-left borderBT_none">No Documents</li>');
										} */

										getAdminBlogslist(projectid);
									})

								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();
						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});


	$("body").delegate("#blog_comments", 'click', function (e) {
		e.preventDefault();

		$("#upload_user_blog_document").attr('data-bid','');
		$("#upload_user_blog_document").attr('data-target','');
		$("#upload_user_blog_document").attr('data-remote','');
		$("#upload_user_blog_document").addClass('disabled_comment_list');

		$('.ttsearch > input').show();
		$('.ttsearch > input').val('');
		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").show();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").hide();

		var project_id = $(this).data('project');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_comments_list/"+project_id;

		getProjectBlogs(project_id);

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id},
			global:false,
			success:function(response){

				if( response ){
					$("#tabContent3").show();
					$('.idea-team-talks-commentlist').html(response);
				}
			}
		});
		return;
	});


	setTimeout(function(){
		var type = window.location.hash.substr(1);
		if( type != '' && type == 'comments' ) {
			$("#blog_comments").trigger('click');
			setTimeout(function(){
				if($('#comments_list .tab-content').length > 0) {

					$('#comments_list .tab-content').animate(
					{
						scrollTop: (  $('#page-comment-<?php echo isset($this->request->params['named']['comment'])? $this->request->params['named']['comment'] : '';?>', $('#all.tab-pane.active')).offset().top - $('#comments_list .tab-content').offset().top + $('#comments_list .tab-content').scrollTop() )
					}, "slow");
				}
			},2000 )
		}
	},1500 )

	$('body').delegate('.submit_blog_comment', 'click', function(event) {
		event.preventDefault();

		var $that = $(this),
		$form = $('#BlogComment'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();

		$that.addClass('disabled');

		var spinnerbefore = '<span style="display: block;float: left;margin-right: 6px;margin-top: 3px;" class="fa fa-spinner fa-pulse"></span>Save';
		var spinnerafter = 'Save';
		$that.html(spinnerbefore);

		var project_id = $("#BlogCommentProjectId").val();
		var blog_id = $("#BlogCommentBlogId").val();
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_comments_list/"+project_id;

		var addBlogCommentUrl = $js_config.base_url+'team_talks/add_comment_blog/blog_id:'+blog_id+'/project_id:'+project_id;

		$.ajax({
			type:'POST',
			data: $.param(form_data),
			dataType: 'json',
			url: url,
			global: true,
			async: false,
			cache: false,
			success: function( response ) {
				//console.log(response);
				if( response.success == true ){

						if(response.refer_id.length > 0 && response.refer_id != null ){

						   actionURL = actionURL+'/refer_id:'+response.refer_id;
					   }else if(response.refer_id == null ){
						    actionURL = actionURL;
					   } else {
						   actionURL = actionURL;
					   }

						$.when(

							$.ajax({
								url : actionURL,
								type: "POST",
								data : {'project_id':project_id,'blog_id':blog_id, 'user_id':user_id},
								global:false,
								success:function(response){

									if( response ){
										$("#tabContent3").show();
										$('.idea-team-talks-commentlist').html(response);
									}
								}
							})

						).then(function( data, textStatus, jqXHR ) {

							$("#blog_user_lists").hide();
							$("#blog_comment_lists").hide();
							$("#blog_document_lists").hide();
							$("#adminblog_lists").hide();
							$("#commentblog_lists").show();
							$("#dashboardblog_lists").hide();

							$(".add_blogcomments").attr('data-blogid', blog_id);
							$(".add_blogcomments").attr('data-projectid', project_id);
							//$(".add_blogcomments").attr('data-remote', addBlogCommentUrl);
							$(".add_blogcomments").attr('data-target', "#modal_add_blogComments");
							$(".add_blogcomments").attr('data-toggle', "modal");
							$(".add_blogcomments").removeClass('disabled_comment_list');

							$(".edit_blog_comment").each(function(){
								var oldUrl = $(this).attr('data-remote');
								$(this).attr('data-remote',oldUrl+'/refer_id:'+blog_id);
							})

							$(".add_blogcomments").attr('data-remote',addBlogCommentUrl+'/refer_id:'+blog_id);

							$('#modal_add_blogComments').modal('hide');

						})


				}else{
					$(".submit_blog_comment").removeClass('disabled');
					$that.html(spinnerafter);
					$('#title').next("span").text("Description is required.");
				}
			}
		})

	});


		$('body').delegate('.submit_blog_addedit_comments_list', 'click', function(event) {
		event.preventDefault();

		var $that = $(this),
		$form = $('#BlogComment'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();
		var comment_id = $("#BlogCommentId").val();

		var project_id = $("#BlogProjectId").val();
		var blog_id = $("#BlogCommentBlogId").val();

		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_comments_list/"+project_id;
		var addBlogCommentUrl = $js_config.base_url+'team_talks/add_comment_blog/blog_id:'+blog_id+'/project_id:'+project_id;

			$.ajax({
				type:'POST',
				data: $.param(form_data),
				dataType: 'json',
				url: url,
				global: true,
				async: false,
				cache: false,
				success: function( response ) {

					if(response.success == true){

						if(response.refer_id ==''){

							response.refer_id =0;
						}

						if( response.refer_id != null && response.refer_id !='' && response.refer_id > 0){

							actionURL = actionURL+'/refer_id:'+response.refer_id;

						} else if(response.refer_id == null){
							actionURL = actionURL;
						} else { actionURL = actionURL; }

						$.when(

							$.ajax({
							url : actionURL,
							type: "POST",
							data : {'project_id':project_id,'blog_id':blog_id, 'user_id':user_id},
							global:false,
							success:function(response){
									//console.log(response);
									if( response ){
										$("#tabContent3").show();
										$('.idea-team-talks-commentlist').html(response);
									}
								}
							})

						).then(function( data, textStatus, jqXHR ) {

							$("#blog_user_lists").hide();
							$("#blog_comment_lists").hide();
							$("#blog_document_lists").hide();
							$("#adminblog_lists").hide();
							$("#commentblog_lists").show();
							$("#dashboardblog_lists").hide();

							$(".add_blogcomments").attr('data-blogid', blog_id);
							$(".add_blogcomments").attr('data-projectid', project_id);
							//$(".add_blogcomments").attr('data-remote', addBlogCommentUrl);
							$(".add_blogcomments").attr('data-target', "#modal_add_blogComments");
							$(".add_blogcomments").attr('data-toggle', "modal");
							$(".add_blogcomments").removeClass('disabled_comment_list');

							$(".edit_blog_comment").each(function(){
								var oldUrl = $(this).attr('data-remote');
								$(this).attr('data-remote',oldUrl+'/refer_id:'+blog_id);
							})

							$(".add_blogcomments").attr('data-remote',addBlogCommentUrl+'/refer_id:'+blog_id);

							$('#modal_edit_blogComments_list').modal('hide');

						})
					}
				}
			})
		});


	$("body").delegate(".trigerblogcomments", 'click', function (e) {
		//$(this,$(".blogcomments")).trigger("click");

		$(this).parents('.pan-uls').find('.blogcomments').trigger('click');
	});


	$("body").delegate(".blogcomments", 'click', function (e) {
		e.preventDefault();

		 $('<style>.task-list-left-wrap::before{height:'+$('#tabContent1').height()+'px;}</style>').appendTo('head');


		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#adminblog_lists").hide();
		$("#commentblog_lists").show();
		$("#dashboardblog_lists").hide();


		var project_id = $(this).data('project');
		var blog_id = $(this).data('value');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/blog_comments_list/"+project_id;

		var addBlogCommentUrl = $js_config.base_url+"team_talks/add_comment_blog/blog_id:"+blog_id+"/project_id:"+project_id;

 	    $.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id,'blog_id':blog_id,'user_id':user_id},
			global:false,
			async:false,
			success:function(response){

				if( response ){
					$("#tabContent3").show();
					$('.idea-team-talks-commentlist').html(response);

					$(".add_blogcomments").attr('data-refered', blog_id);

					$("#edit_blog_comment").attr('data-refered', blog_id);
					$("#confirm_coment_delete").attr('data-refered', blog_id);

					$(".add_blogcomments").attr('data-blogid', blog_id);
					$(".add_blogcomments").attr('data-projectid', project_id);

					$(".add_blogcomments").attr('data-target', "#modal_add_blogComments");

					$(".edit_blog_comment").each(function(){
						var oldUrl = $(this).attr('data-remote');
						$(this).attr('data-remote',oldUrl+'/refer_id:'+blog_id);
					})

					$(".add_blogcomments").attr('data-remote',addBlogCommentUrl+'/refer_id:'+blog_id);


					$(".add_blogcomments").attr('data-toggle', "modal");
					$(".add_blogcomments").removeClass('disabled_comment_list');
				}
			}
		});




		return;
	});


	$("body").delegate("#blog_documents", 'click', function (e) {
		e.preventDefault();
		$('.ttsearch > input').show();
		$('.ttsearch > input').val('');
		$("#blog_user_lists").hide();
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").show();
		$("#commentblog_lists").hide();
		$("#adminblog_lists").hide();
		$("#dashboardblog_lists").hide();
		$(".tabContent").not("#tabContent4").hide();


		var project_id = $(this).data('project');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		var actionURL =  $js_config.base_url+"team_talks/document_list";
		getDocumentBlogslist(project_id);
		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':project_id, 'user_id':user_id},
			global:false,
			success:function(response){
				if( response ){
					$("#tabContent4").show();
					$('.idea-team-talks-document-list').html(response);
					var document_li_counter	= $( "#tabContent4 .idea-doc-list ul#accordion li div.list-group-item" ).size();
					//console.log(document_li_counter);
					//$("#blog_documents").html('<i class="fa fa-folder-o"></i>&nbsp;Documents ('+document_li_counter+')');
				}
			}
		});
		return;
	});



	$("body").delegate(".users_blog_document", 'click', function (e) {
		e.preventDefault();

		if( !$(this).hasClass('collapsed') ){

			$("#upload_user_blog_document").attr('data-bid','');
			$("#upload_user_blog_document").attr('data-target','');
			$("#upload_user_blog_document").attr('data-remote','');
			$("#upload_user_blog_document").addClass('disabled_comment_list');

		} else {

			var blog_id = $(this).data('blogid');
			var project_id = $("#ProjectId").val();
			console.log(project_id);
			var daction = $js_config.base_url+"team_talks/public_blog_documents/project_id:"+project_id+"/blog_id:"+blog_id;
			var dtraget = "#modal_edit_blogpost_md";

			$("#upload_user_blog_document").attr('data-bid',blog_id);
			$("#upload_user_blog_document").attr('data-target',dtraget);
			$("#upload_user_blog_document").attr('data-remote',daction);
			$("#upload_user_blog_document").removeClass('disabled_comment_list');
		}

	});



	$("body").delegate("#upload_user_blog_document", 'click', function (e) {
		e.preventDefault();

		   $('#modal_edit_blogpost_md').modal({
				remote: $(this).attr('data-remote')
		   })
		   .show()
		   .on('hidden.bs.modal', function(event) {
			// Get workspace comments

			 $(this).removeData('bs.modal');
			 $(this).find('.modal-content').html('');
		   })

		});


	$('#modal_edit_blogpost').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html("");
    });
	$('#modal_edit_blogpost_md').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html("");
    });


	$("body").delegate(".blog_details #blog_like", 'click', function (e) {
		e.preventDefault();

		var blog_project_id = $('#ProjectId').val();
		var blog_id = $(this).data('value');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';

		var actionURL =  $js_config.base_url+"team_talks/save_blog_like/project_id/";

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':blog_project_id, 'blog_id':blog_id, 'user_id':user_id},
			 dataType : 'JSON',
			success:function(response){
				if( response.success == true ){
					$("#blogcounter"+blog_id).parent().addClass('disable');
					$("#blogcounter"+blog_id).parent().removeAttr('id');
					$("#blogcounter"+blog_id).parent().removeAttr('data-value');
					$("#blogcounter"+blog_id).text(response.content);
					//console.log(response.content);
				}
			}
		});
		return;
	});

	$("body").delegate("#commentblog_lists #blog_like", 'click', function (e) {
		e.preventDefault();

		var blog_project_id = $('#ProjectId').val();
		var blog_id = $(this).data('value');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';

		var actionURL =  $js_config.base_url+"team_talks/save_blog_like/project_id/";

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':blog_project_id, 'blog_id':blog_id, 'user_id':user_id},
			 dataType : 'JSON',
			success:function(response){
				if( response.success == true ){
					$("#commentblog_lists #blogcounter"+blog_id).parent().addClass('disable');
					$("#commentblog_lists #blogcounter"+blog_id).parent().removeAttr('id');
					$("#commentblog_lists #blogcounter"+blog_id).parent().removeAttr('data-value');
					$("#commentblog_lists #blogcounter"+blog_id).text(response.content);
					//console.log(response.content);
				}
			}
		});
		return;
	});

	$("body").delegate("#admin_blg_list #admin_blog_like", 'click', function (e) {
		e.preventDefault();

		var blog_project_id = $('#ProjectId').val();
		var blog_id = $(this).data('value');
		var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';

		var actionURL =  $js_config.base_url+"team_talks/save_blog_like/";

		$.ajax({
			url : actionURL,
			type: "POST",
			data : {'project_id':blog_project_id, 'blog_id':blog_id, 'user_id':user_id},
			 dataType : 'JSON',
			success:function(response){
				if( response.success == true ){
					$("#admin_blg_list #blogcounter"+blog_id).parent().addClass('disable');
					$("#admin_blg_list #blogcounter"+blog_id).parent().removeAttr('id');
					$("#admin_blg_list #blogcounter"+blog_id).parent().removeAttr('data-value');
					$("#admin_blg_list #blogcounter"+blog_id).text(response.content);
					//console.log(response.content);
				}
			}
		});
		return;
	});

	$("body").delegate(".blog-comment-lists #blog_comment_like", 'click', function (e) {
		e.preventDefault();

			var blog_project_id = $('#ProjectId').val();
		 	var comment_id = $(this).data('value');
		 	var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
		 	var actionURL =  $js_config.base_url+"team_talks/comments_like/";
			var comval = parseInt($("#commentcounter"+comment_id).text());

			  $.ajax({
				url : actionURL,
				type: "POST",
				data : {'project_id':blog_project_id, 'comment_id':comment_id, 'user_id':user_id},
				 dataType : 'JSON',
				success:function(response){
					if( response.success == true ){
						$("#commentcounter"+comment_id).parent().addClass('disable');
						$("#commentcounter"+comment_id).parent().removeAttr('id');
						$("#commentcounter"+comment_id).parent().removeAttr('data-value');
						$("#commentcounter"+comment_id).text(response.content);
						//$("#commentcounter"+comment_id).text(comval+1)
						 //console.log(response.content);
					}
				}
			});
			return;
		});

		$("body").delegate(".idea-team-talks-commentlist .like_comment", 'click', function (e) {
			e.preventDefault();

				var $this = $(this);
				var blog_project_id = $('#ProjectId').val();
				var comment_id = $(this).data('value');
				var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
				var actionURL =  $js_config.base_url+"team_talks/comments_like/";
				var comval = parseInt($("#commentcounters"+comment_id).text());
				  $.ajax({
					url : actionURL,
					type: "POST",
					data : {'project_id':blog_project_id, 'comment_id':comment_id, 'user_id':user_id},
					 dataType : 'JSON',
					success:function(response){
						if( response.success == true ){
							$("#commentcounters"+comment_id).parent().addClass('disable');
							$("#commentcounters"+comment_id).parent().removeAttr('id');
							$("#commentcounters"+comment_id).parent().removeAttr('data-value');
							$(".label.bg-purple", $this).text(response.content);
							$this.addClass('disable');
							// $("#commentcounters"+comment_id).text(response.content);
							//$("#commentcounter"+comment_id).text(comval+1)
							 //console.log(response.content);
						}
					}
				});

				return;

		});

		$("body").delegate(".idea-team-talks-admin_commentlist .like_comment", 'click', function (e) {
			e.preventDefault();

				$that = $(this);

				var blog_project_id = $('#ProjectId').val();
				var comment_id = $(this).data('value');
				var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
				var actionURL =  $js_config.base_url+"team_talks/comments_like/";
				var comval = parseInt($("#commentcounters"+comment_id).text());

				  $.ajax({
					url : actionURL,
					type: "POST",
					data : {'project_id':blog_project_id, 'comment_id':comment_id, 'user_id':user_id},
					 dataType : 'JSON',
					success:function(response){
						if( response.success == true ){

							$that.parent().find('.like_comment').addClass('disable');
							$that.parent().find('.like_comment').removeAttr('id');
							$that.parent().find('.like_comment').removeAttr('data-value');
							$("#commentcounters"+comment_id,$that).text(response.content);

						}
					}
				});

				return;

		});


		$("body").delegate("#blog-update-comment-list .like_user_comment", 'click', function (e) {
		e.preventDefault();

		    $that = $(this);
			var blog_project_id = $('#ProjectId').val();
			var comment_id = $(this).data('value');
			var user_id = '<?php echo $this->Session->read('Auth.User.id')?>';
			var actionURL =  $js_config.base_url+"team_talks/comments_like/";
			var comval = parseInt($("#commentcounters"+comment_id,$that).text());



/* 			  $that.parent().find('.like_user_comment').addClass('disable');
						 $that.parent().find('.like_user_comment').removeAttr('id');
						 $that.parent().find('.like_user_comment').removeAttr('data-value');
						 $("#commentcounters"+comment_id,$that).text("10");   */


			    $.ajax({
				url : actionURL,
				type: "POST",
				data : {'project_id':blog_project_id, 'comment_id':comment_id, 'user_id':user_id},
				 dataType : 'JSON',
				success:function(response){
					if( response.success == true ){


						 $that.parent().find('.like_user_comment').addClass('disable');
						 $that.parent().find('.like_user_comment').removeAttr('id');
						 $that.parent().find('.like_user_comment').removeAttr('data-value');
						 $("#commentcounters"+comment_id,$that).text(response.content);


					}
				}
			 });

			return;

	});



	$("body").delegate(".blog-uploads", 'change', function (event) {

			event.preventDefault();
			$(".upload-multiple-docs").show();
			var $t = $(this),
				file_path = "uploads/blogdocuments/",
				$form = $("#blogDocuments"),
				formData = new FormData($form[0]),
				$fileInput = $t,
				file = $fileInput[0].files[0],
				valid_flag = false,
				sizeMB = 0;

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name,
					size = file.size,
					type = file.type;

				formData.append('file_name', $fileInput[0].files[0]);

			}
			//async: "false",
			if ( $fileInput.val() !== "" ) {

				$.ajax({
					type: 'POST',
					dataType: "JSON",
					url: $js_config.base_url + "team_talks/blog_uploads",
					data: formData,
					global: false,
					cache: false,
					contentType: false,
					processData: false,
					xhr: function () {
						// 3-9-15 updates
						var xhr = new window.XMLHttpRequest();
						//Upload progress
						xhr.upload.addEventListener("progress", function (event) {
							if (event.lengthComputable) {
								var percentComplete = Math.round(event.loaded / event.total * 100);
								$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%")
							}
						}, false);
						return xhr;
					},
					success: function (response) {
						//console.log(response);
						if( response.success ) {
						    $('.doc-error').text('');
							$(".upload-multiple-docs").hide();
							var $output = '';
							if( !$.isEmptyObject(response.content) ){
								var uploaded_files = response.content;

								$.each(uploaded_files, function(key, value) {
									if(value != ''){
										$output += '<li class="todoimg list-group-item">' +
											'<input type="hidden" name="data[BlogDocument][file_name][]" value="'+ value +'"/>' +
												'<a href="#" class="todoimglink tipText" title="'+ value +'" download="download" >' + value + '</a>' +
												'<span class="del-img-todo pull-right">' +
													'<a id=""  data-file="'+value+'" title="Click here to delete" class="text-red tipText confirm_blogdoc_delete" href="javascript:void(0);">' +
														'<i class="fa fa-times"></i>'+
													'</a>' +
												'</span>' +
										'</li>';

									}
								});
							}
						}

						$("#comment_uploads_list").append($output);
					}
				});
			}
	});


	$("body").delegate(".confirm_doc_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var params = {"id" : id,"file_name" : $t.attr("data-file")};
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this file?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/blog_comment_document_delete',
								type: "POST",
								data: $.param(params),
								dataType: "JSON",
								global: false,
								success: function (response) {
									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();
									})

								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();

						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});


	$('body').delegate('.submit_blog_documents', 'click', function(event) {
			event.preventDefault();

			var $that = $(this),
			$form = $('#blogDocuments'),
			url = $form.attr('action'),
			form_data = $form.serializeArray();
			var blog_id = $("#BlogDocumentBlogId").val();

			$.ajax({
				type:'POST',
				data: $.param(form_data),
				dataType: 'json',
				url: url,
				global: true,
				async: false,
				cache: false,
				success: function( response ) {
					//console.log(response);
					if( response ){
						//$("#blog_document_lists").html(response);
						getBlogDocumentList(response.blog_id, response.project_id);
						$("#modal_edit_blogpost").modal('hide');
					}else{
						$.each(response.content, function(i, val){
							$form.find("#"+i).parents('.form-group:first').find('.error-message').text(val)
						})
					}
				}
			})

		});

	$('body').delegate('.submit_public_documents', 'click', function(event) {
		event.preventDefault();

		var $that = $(this),
		$form = $('#blogDocuments'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();

		$.ajax({
			type:'POST',
			data: $.param(form_data),
			dataType: 'json',
			url: url,
			global: true,
			async: false,
			cache: false,
			success: function( response ) {
				if( response.success == true ){
					$('.doc-error').text('');
					$("#modal_edit_blogpost").modal('hide');
					$("#blog_documents").trigger('click');
				}else{
				    $('.doc-error').text(response.content);
				}
			}
		})

	});


	$('body').delegate('.submit_doc_comments', 'click', function(event) {
		event.preventDefault();

		var $that = $(this),
		$form = $('#BlogComment'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();

		$.ajax({
			type:'POST',
			data: $.param(form_data),
			dataType: 'json',
			url: url,
			global: true,
			async: false,
			cache: false,
			success: function( response ) {
				//console.log(response);
				if( response.success == true ){
					$("#modal_add_blogComments").modal('hide');
					var $output = '';
					var blog_id = response.content.BlogComment.blog_id,project_id = response.content.BlogComment.project_id;
					$('.blog-comment-lists').load($js_config.base_url + "team_talks/user_comment_list/"+blog_id+'/'+project_id);

					if( !$.isEmptyObject(response.content.BlogDocument) ){
						var uploaded_files = response.content.BlogDocument;
						$.each(uploaded_files, function(key, value) {
							if(value != ''){
								//console.log(value);
							}
						})
					}
					//console.log(response.content.BlogComment);
				}else{
					$('#title').next("span").text("Description is required.")
				}
			}
		})

	});




	$('body').delegate('.submit_blog_edit_comments', 'click', function(event) {
		event.preventDefault();
		/*
		<?php
			//if(isset($this->params['named']['com_class']) && !empty($this->params['named']['com_class'])){
		?>
		console.log("<?php //echo $this->params['named']['com_class'] ?>");

		<?php		 //}
		?>return; */

		$(".upload-bmultiple-docs").show();
		var $that = $(this),
		$form = $('#BlogComment'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();

		$.ajax({
			type:'POST',
			data: $.param(form_data),
			dataType: 'json',
			url: url,
			global: true,
			async: false,
			cache: false,
			success: function( response ) {
				//console.log(response);
				if( response.success == true ){
					$(".upload-bmultiple-docs").hide();
					$("#modal_edit_blogComments").modal('hide');
					var $output = '';
					var blog_id = response.content.BlogComment.blog_id,project_id = response.content.BlogComment.project_id;
					$('.blog-comment-lists').load($js_config.base_url + "team_talks/user_comment_list/"+blog_id+'/'+project_id);

					if( !$.isEmptyObject(response.content.BlogDocument) ){
						var uploaded_files = response.content.BlogDocument;
						$.each(uploaded_files, function(key, value) {
							if(value != ''){
								//console.log(value);
							}
						})
					}
					//console.log(response.content.BlogComment);
				}else{
					$('#title').next("span").text("Description is required.")
				}
			}
		})

	});


	$('body').delegate('.submit_blog_edit_comments_list', 'click', function(event) {
		event.preventDefault();

		var $that = $(this),
		$form = $('#BlogComment'),
		url = $form.attr('action'),
		form_data = $form.serializeArray();
		var comment_id = $("#BlogCommentId").val();

		$.ajax({
			type:'POST',
			data: $.param(form_data),
			//dataType: 'json',
			url: url,
			global: true,
			async: false,
			cache: false,
			success: function( response ) {
				$("#modal_edit_blogComments_list").modal('hide');
				setTimeout(function(){

				    $("#bcommentlists"+comment_id).html(response);


				},500);

			}
		})

	});


	$("body").delegate(".comments_doc_uploads", 'change', function (event) {
			event.preventDefault();

			$(".upload-bmultiple-docs").show();
			var $t = $(this),
				file_path = "uploads/blogdocuments/",
				$form = $(this).closest('form'),
				formData = new FormData($form[0]),
				$fileInput = $t,
				file = $fileInput[0].files[0],
				valid_flag = false,
				sizeMB = 0;

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name,
					size = file.size,
					type = file.type;

				formData.append('file_name', $fileInput[0].files[0]);

			}
			// return;


			if ( $fileInput.val() !== "" ) {

				$.ajax({
					type: 'POST',
					dataType: "JSON",
					url: $js_config.base_url + "team_talks/comments_doc_uploads",
					data: formData,
					global: false,
					cache: false,
					contentType: false,
					processData: false,
					xhr: function () {
						// 3-9-15 updates
						var xhr = new window.XMLHttpRequest();
						//Upload progress
						xhr.upload.addEventListener("progress", function (event) {
							if (event.lengthComputable) {
								var percentComplete = Math.round(event.loaded / event.total * 100);
								$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%")
							}
						}, false);
						return xhr;
					},
					success: function (response) {

						if( response.success ) {
							$(".upload-bmultiple-docs").hide();
							var $output = '';
							if( !$.isEmptyObject(response.content) ){
								var uploaded_files = response.content;

								$.each(uploaded_files, function(key, value) {
									if(value != ''){
										$output += '<li class="todoimg list-group-item">' +
											'<input type="hidden" name="data[BlogDocument][file_name][]" value="'+ value +'"/>' +
												'<a href="#" class="todoimglink tipText" title="'+ value +'" download="download" >' + value + '</a>' +
												'<span class="del-img-todo pull-right">' +
													'<a id=""  data-file="'+value+'" title="Click here to delete" class="text-red tipText confirm_blogdoc_delete" href="javascript:void(0);">' +
														'<i class="fa fa-times"></i>'+
													'</a>' +
												'</span>' +
										'</li>';

									}
								});
							}
						}

						$("#comment_uploads_list", $('.modal.modal-success.fade.in')).append($output);

					}
				});
			}
	});


	$("body").delegate(".confirm_blogdoc_delete", 'click', function (event) {
		event.preventDefault()
		var $t = $(this),id = $t.attr("id");
		var params = {"id" : id,"file_name" : $t.attr("data-file")};
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this file?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/blog_comment_document_delete',
								type: "POST",
								data: $.param(params),
								dataType: "JSON",
								global: false,
								success: function (response) {
									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove()
									})
								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();
						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});


	$('body').delegate('.accordion-group a.blog_data','click',function(){

		$(this).find('em').toggleClass('fa-plus-square-o fa-minus-square-o');
		$(this).parent().next().find('.accordion-inner').toggleClass('heightmin heightmax');

				if($(this).find('em').hasClass('fa-minus-square-o')){

					//console.log('run ajax...');

					$('#blog_comment_lists').show();
					$('#blog_user_lists').hide();
					$('#blog_document_lists').hide();
					$('#commentblog_lists').hide();
					$('#adminblog_lists').hide();
					$("#dashboardblog_lists").hide();

					//console.log($(this).data('rel'));

					var projectID;
					projectID = $(this).data('project');
					var BlogId;
					BlogId = $(this).data('rel');

					var async = false;
					if(async == false){
						async = true;
						$.ajax({
						url : $js_config.base_url+'team_talks/blog_comments',
						type: "POST",
						global: true,
						async: false,
						data : {'project_id':projectID, 'blog_id':BlogId},
						//dataType : 'JSON',
						success:function(response){
							async = false;
								if( response){
									// console.log(response);
									$('#blog_comment_lists').html(response);
									$('.blog-comment-lists').load($js_config.base_url + "team_talks/user_comment_list/"+BlogId+'/'+projectID);
								}
						}
						});

					}


				} else if($(this).find('em').hasClass('fa-plus-square-o')){

					$('#blog_comment_lists').hide();
					$('#blog_user_lists').show();
					$('#blog_document_lists').hide();
					$('#commentblog_lists').hide();
					$('#adminblog_lists').hide();
					$("#dashboardblog_lists").hide();
				}

		});

	$("body").delegate("#confirm_blog_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var project_id = $("#ProjectId").val();
		var blog_id = $(this).data('value');
		var blgUser = $(this).data('user');
		var params = {"blog_id" : $(this).data('value'),"project_id":project_id};

		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this Blog?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/blog_delete',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: false,
								success: function (response) {


									 $("#allAdminBlogs #admin_blg_list #bloglistview"+blog_id).slideUp(800, function(){

										var delblg = $(".delete_blog").length;

										$("#blog_user_lists .list-inline .userBlogcount[data-value*="+blgUser+"]").text(delblg) ;

										$( "#bloglistview"+blog_id ).remove();
									})



								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();

										$( "#bloglistview"+blog_id ).remove();


						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});


	$("body").delegate("#confirm_coment_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var params = {"comment_id" : $(this).data('value')};
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this comment?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/blog_comment_delete',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: true,
								success: function (response) {
									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();
										var com_counter	= $( "#blog_comment_lists .comments ul.blog-comment-lists li" ).size();
										if( com_counter == 0 ){
											$(".blog-comment-lists").html('<li class="list-group-item-nf padding-left">No Comments</li>');
										}
									})
								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.close();
						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});

	$("body").delegate("#confirm_doc_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var params = {"document_id" : $(this).data('value')};
		var projectid = $(this).data('projectid');
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this document?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/document_delete',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: true,
								success: function (response) {

									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();

										var document_li_counter_dc	= $( "#tabContent4 .idea-doc-list ul#accordion li div.list-group-item" ).size();
										//console.log(document_li_counter);
										//$("#blog_documents").html('<i class="fa fa-folder-o"></i>&nbsp;Documents ('+document_li_counter_dc+')');

									})

								}
							})
						).then(function( data, textStatus, jqXHR ) {
							getDocumentBlogslist(projectid);
							dialogRef.close();
						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});

	$("body").delegate("#confirm_document_blog_delete", 'click', function (event) {
		event.preventDefault()

		var $t = $(this),id = $t.attr("id");
		var params = {"id" : $(this).data('value')};
		var project_id = $(this).data('projectid');
		BootstrapDialog.show({
			title: 'Confirmation',
			message: 'Are you sure you want to delete this document?',
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: false,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'team_talks/delete_blog_document',
								type: "POST",
								data: params,
								dataType: "JSON",
								global: true,
								success: function (response) {
									$t.parents('li:first').slideUp(300, function(){
										$t.parents('li:first').remove();

										var docc_counter = $( "#blog_document_lists .comments ul#bdoc-list li" ).size();
										if( docc_counter == 0 ){
											$("#bdoc-list").html('<li class="list-group-item padding-left">No Documents</li>');
										}

									})
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							$.ajax({
								url : $js_config.base_url+'team_talks/project_document_count',
								type: "POST",
								data: {"project_id" : project_id},
								success:function(response){
									async = false;
										if( response){
											//$("#blog_documents").html('<i class="fa fa-folder-o"></i>&nbsp;Documents ('+response+')');
										}
								}
							});

							dialogRef.close();

						})

					}
				},
				{
					label: ' No',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});
	});

	$("body").delegate('#list_blog_comments', 'click', function(){

		var blog_id = $(this).data('blog_id');
		$('.accordion-group a.blog_data[data-rel='+blog_id+']').trigger('click')
		$("#blog_user_lists").hide();
		$("#blog_comment_lists").show();

	})

	$('.modal').on('show.bs.modal', function(event) {
		$('body').css('padding-right', 0);
		//console.log('on')
	})

	$('.modal').on('hide.bs.modal', function(event) {
		$(event.relatedTarget).tooltip('destroy')
		$('body').css('padding-right', 0);
		//console.log('off')
	})

	$('body').delegate('.accordion-group a.blog_data_admin','click',function(){

		$(this).find('em').toggleClass('fa-plus-square-o fa-minus-square-o');
		$(this).parent().next().find('.accordion-inner').toggleClass('heightmin heightmax');

	});



});

	function getProjectBlogs(projectID){

			var async = false;
			if(async == false){
				async = true;

				$.ajax({
				url : $js_config.base_url+'team_talks/project_blog_list',
				type: "POST",
				async: false,
				data : {'project_id':projectID},

				success:function(response){
					async = false;
						if( response){
							$('#commentblog_lists').html(response);
						}
				}
				});

			}
	}

	function getDocumentBlogslist(projectID){

			var async = false;
			if(async == false){
				async = true;

				$.ajax({
				url : $js_config.base_url+'team_talks/document_blog_list',
				type: "POST",
				async: false,
				data : {'project_id':projectID},

				success:function(response){
					async = false;
						if( response){
							$('#blog_document_lists').html(response);
						}
				}
				});

			}
	}

	function getAdminBlogslist(projectID){
			console.log(projectID);
			var async = false;
			if(async == false){
				async = true;

				$.ajax({
				url : $js_config.base_url+'team_talks/blog_admin_list',
				type: "POST",
				async: false,
				data : {'project_id':projectID},

				success:function(response){
					async = false;
						if( response){
							$('#adminblog_lists').html(response);
						}
				}
				});

			}
	}


	function getBlogDocumentList(BlogId, projectID ){

		$that = $("#open_blog_documents")
		$that.find('em').toggleClass('fa-plus-square-o fa-minus-square-o');
		$that.parent().next().find('.accordion-inner').toggleClass('heightmin heightmax');


			//console.log('run ajax...');

			$('#blog_comment_lists').hide();
			$('#blog_user_lists').hide();
			$('#blog_document_lists').show();

			//console.log($(this).data('rel'));

			/* var projectID;
			projectID = $that.data('project');
			var BlogId;
			BlogId = $that.data('blog_id'); */

			var async = false;
			if(async == false){
				async = true;

				/* console.log(BlogId);
				console.log(projectID); */

				$.ajax({
				url : $js_config.base_url+'team_talks/blog_documents_list',
				type: "POST",
				async: false,
				data : {'project_id':projectID, 'blog_id':BlogId},
				//dataType : 'JSON',
				success:function(response){
					async = false;
						if( response){
							$('#blog_document_lists').html(response);
							allBlogProjectDocumentCount(projectID);
						}
				}
				});

			}
	}




	function getPublicDocumentList(UserID, projectID ){

			$that = $("#open_blog_documents")
			//console.log($(this).data('rel'));

			var projectID;
			projectID = $that.data('project');
			var BlogId;
			BlogId = $that.data('blog_id');

			var async = false;
			if(async == false){
				async = true;

				//console.log(BlogId);
				//console.log(projectID);

				$.ajax({
				url : $js_config.base_url+'team_talks/blog_documents_list',
				type: "POST",
				async: false,
				data : {'project_id':projectID, 'blog_id':BlogId},
				//dataType : 'JSON',
				success:function(response){
					async = false;
						if( response){
							$('#blog_document_lists').html(response);
							allBlogProjectDocumentCount(projectID);
						}
				}
				});

			}
	}

	function allBlogProjectDocumentCount(project_id){

			if(project_id.length > 0){

				$.ajax({
					url : $js_config.base_url+'team_talks/project_document_count',
					type: "POST",
					async: false,
					data : {'project_id':project_id},
					//dataType : 'JSON',
					success:function(response){
						async = false;
							if( response){
								//var document_li_counter_dc	= $( "#tabContent4 .idea-doc-list ul#accordion li div.list-group-item" ).size();
								//console.log(document_li_counter);
								//$("#blog_documents").html('<i class="fa fa-folder-o"></i>&nbsp;Documents ('+response+')');
							}
					}
				});
			}


	}

	function dashboard_Blog_user_list(projectID){

		//var $that = $(this);
		//var projectID = $that.data('project');
		$.ajax({
		url : $js_config.base_url+'team_talks/blog_list_user',
		type: "POST",
		async: false,
		data : {'project_id':projectID},

			success:function(response){
				if( response){
					$('#dashboardblog_lists').html(response);
				}
			}
		});
	}








	$(function(){

	//	if('[id^=comment-page-collapse-]').hasClass('in')){

			//can-img

			  //$('[class^=page-collapse-]').click(function() {




			  $("body").delegate("#comment-page-accordion .page-accordion", 'click', function (e) {

			  var $that = $(this);
			  $that.toggleClass('canvas');

			  $(this).parents('.pan-uls').find('.panel-collapse').toggleClass('in');
			  if( $that.hasClass('canvas') ){
			     $(this).parents('.pan-uls').find('.description img').hide();
				//console.log($that);
				$(this).parents('.pan-uls').find('.description').css('max-height','250px');
				$(this).parents('.pan-uls').find('.description').css('overflow','hidden');
				html2canvas($(this).parents('.pan-uls').find('.description').css('font-size','45%').addClass('in'), {

					onrendered: function(canvas) {
					theCanvas = canvas;
					document.body.appendChild(canvas);

					$that.parents('.pan-uls').find(".can-img").html(canvas);

					}
				});
					 setTimeout(function(){
					     $(this).parents('.pan-uls').find('.description img').show();
						//$that.parents('.pan-uls').find('.description').css('display:none')
						//$that.parents('.pan-uls').find(".can-img").hide();
						$that.parents('.pan-uls').find('.description').css('font-size','14px');
						$that.parents('.pan-uls').find('.description').removeClass('in');
					 },300);

				}

			});

	//	}




		var re_projectId = '';
		var re_blogId = '';
		re_projectId = '<?php echo isset($this->request->params['named']['project'])? $this->request->params['named']['project'] : '';?>';
		re_blogId =	'<?php echo isset($this->request->params['named']['blog'])? $this->request->params['named']['blog'] : '';?>';

		if( re_projectId.length > 0 && re_blogId.length > 0 ){
		   //console.log("noooo");
			setTimeout(function(){

				 if($('a#target'+re_blogId).length > 0){
					$("#bloglistview"+re_blogId+" a#target"+re_blogId).trigger('click');

					$( '#tabContent1').scrollTop($('a#target'+re_blogId).offset().top);

					$('html, body').animate({
					 scrollTop: $('a#target'+re_blogId).offset().top - 100
				  }, 'slow');
				  }


			}, 2000);
		}

		$("#people > .btn-group").css('width','40px');

	})





$(function () {

    var mark = function () {
        var keyword = $(".ttsearch > input").val();
		//console.log(keyword);
        $(".accordion-inner").unmark();
        $(".accordion-inner").mark(keyword);

			$allListElements = $('#tabContent1 .accordion-group');
            var $matchingListElements = $allListElements.filter(function (i, li) {
                var listItemText = $(li).text().toUpperCase(), searchText = keyword.toUpperCase();
                return ~listItemText.indexOf(searchText);
            });

            $allListElements.hide();
            $matchingListElements.show();
            var searchText = keyword.toUpperCase();
            $("#tabContent1 .accordion-group").unmark();
            $("#tabContent1 .accordion-group").mark(searchText);


    };
    $("body").delegate(".ttsearch  > input", 'keyup', mark);



    $('body').delegate('.ttsearch  > input', 'keyup', function (event) {
        event.preventDefault();
            var keyword = $(".ttsearch > input").val();

            $allListElements = $('ul#blog-comment-list > li');
            var $matchingListElements = $allListElements.filter(function (i, li) {
                var listItemText = $(li).text().toUpperCase(), searchText = keyword.toUpperCase();
                return ~listItemText.indexOf(searchText);
            });

            $allListElements.hide();
            $matchingListElements.show();
            var searchText = keyword.toUpperCase();
            $("#blog-comment-list").unmark();
            $("#blog-comment-list").mark(searchText);


			if($(".wiki-inner ul.nav-tabs li:nth-child(3)").hasClass('active')){

			$allListElements = $('ul.idea-team-talks-document-list > li');
            var $matchingListElements = $allListElements.filter(function (i, li) {
                var listItemText = $(li).text().toUpperCase(), searchText = keyword.toUpperCase();
                return ~listItemText.indexOf(searchText);
            });

            $allListElements.hide();
            $matchingListElements.show();
            var searchText = keyword.toUpperCase();
            $("ul.idea-team-talks-document-list").unmark();
            $("ul.idea-team-talks-document-list").mark(searchText);


			}


    });

	$('body').delegate('#gotoblog', 'click', function (event) {

		var blog_id = $(this).data('blogid');
		var projectid = $(this).data('projectid');
		//var div_class = ".page-collapse-"+blog_id;

		//$("#blog_read").trigger("click");

		var url = $js_config.base_url+'team_talks/index/project:'+projectid+'/blog:'+blog_id;
		window.location = url
		//$(div_class+" h4 > a").trigger("click");

	});


	$('body').delegate('#gotoblogcomment', 'click', function (event) {

		var blog_id = $(this).data('blogid');
		var projectid = $(this).data('projectid');
		var div_class = ".page-collapse-"+blog_id;

		$("#blog_admin").trigger("click");
		$(div_class+" h4 > a").trigger("click");

		$("#"+blog_id+"-page-accordion .admin-corner-icons li:first .blog_admin_comment").trigger("click");



	});

	$('body').delegate('#gotoblogdocument', 'click', function (event) {

		var blog_id = $(this).data('blogid');
		var projectid = $(this).data('projectid');
		var div_class = ".page-collapse-"+blog_id;

		$("#blog_admin").trigger("click");
		$(div_class+" h4 > a").trigger("click");

		$("#"+blog_id+"-page-accordion .admin-corner-icons li:last .blog_admin_comment").trigger("click");



	});

	$('body').delegate('.accordion-group .accordion-body .accordion-inner.vik a', 'click', function (e) {
		 e.preventDefault();
		 //console.log("inout")
			window.open($(this).attr('href'));
			return false;
	});


	/* $.ajax({
		url: $js_config.base_url + 'projects/project_header_image/' + project_id,
		type: "POST",
		data: $.param({}),
		dataType: "JSON",
		global: false,
		success: function (response) {
		  $("#project_header_image").html(response)
		}
    }) */

});


</script>



