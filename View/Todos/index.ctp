<?php
    echo $this->Html->css('projects/todo');
    echo $this->Html->script('projects/todo', array('inline' => true));

    echo $this->Html->css('projects/dropdown');

    echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
    echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
    echo $this->Html->script('projects/plugins/calendar/jquery.daterange', array('inline' => true));
?>
<script type="text/javascript">

    $(function () {
		//$('#ajax_overlay').show();
        /* ******************************** BOOTSTRAP HACK *************************************
         * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
         * */
        var originalLeave = $.fn.popover.Constructor.prototype.leave;
        $.fn.popover.Constructor.prototype.leave = function (obj) {
            var self = obj instanceof this.constructor ?
                    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
            var container, timeout;

            originalLeave.call(this, obj);
            if (obj.currentTarget) {
                container = $(obj.currentTarget).data('bs.popover').tip()
                timeout = self.timeout;
                container.one('mouseenter', function () {
                    //We entered the actual popover – call off the dogs
                    clearTimeout(timeout);
                    //Let's monitor popover content instead
                    container.one('mouseleave', function () {
                        $.fn.popover.Constructor.prototype.leave.call(self, self);
                    });
                })
            }
        };
        /* End Popover
         * ******************************** BOOTSTRAP HACK *************************************
         * */

        $('#popup_model_box_profile').on('hidden.bs.modal', function (event) {
            $(this).find('.modal-content').html("")
            $(this).removeData('bs.modal')
        })

        $.modal_triggered = $({});
        $('#todo_model').on('shown.bs.modal', function (event) {
            $.modal_triggered = $(event.relatedTarget);
        })

        $('#todo_model').on('hidden.bs.modal', function (event) {
            $(this).find('.modal-content').html("")
            $(this).removeData('bs.modal');
            if($.modal_triggered.length > 0) {
                var $el = $.modal_triggered,
                    data = $el.data(),
                    do_list_id = data.id;
                $.update_comment_icon(do_list_id, false).done(function(){
                    $.modal_triggered = $({});
                })
            }
        })
        $.update_comment_icon = function(do_list_id, removed){
            var dfd = $.Deferred();
            $.ajax({
                type: 'POST',
                data: $.param({ 'do_list_id': do_list_id }),
                dataType: 'json',
                url: $js_config.base_url + 'todos/get_comments_count',
                global: false,
                success: function(response) {
                    if(response.success) {
                        var comment_icon = $('.list-group-item[data-id="'+do_list_id+'"]').find('.todo_comments');
                        if(response.content > 0){
                            comment_icon.removeClass('text-dark-gray').addClass('text-green');
                            comment_icon.attr('data-original-title', 'Comments').tooltip('fixTitle');
                        }
                        else {
                            comment_icon.removeClass('text-green').addClass('text-dark-gray');
                            comment_icon.attr('data-original-title', 'No Comments').tooltip('fixTitle');
                        }
                        dfd.resolve();
                    }
                }
            });
            return dfd.promise();
        }

        /*$('body').delegate('#project_change', 'click', function(e){
            e.preventDefault();
            if($(this).val() != ''){
                $('#manage_todo').attr('href', $js_config.base_url + 'todos/manage/project:'+$(this).val())
            }
            else{
                $('#manage_todo').attr('href', $js_config.base_url + 'todos/manage')
            }
        })*/

		$("body").on("mouseover", ".subtodolist_old", function(){
			$(this).popover('destroy');
			$that = $(this);
			var do_list_id = $that.data('subtodoid');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $.param({'do_list_id':do_list_id }),
				url: $js_config.base_url + 'todos/getdouploadlist',
				global: false,
				success: function(response) {
					if(response){

						var datacontentli = '<ul>';
						$.each(response, function(key, data) {
							// datacontentli +='<li>'+data.DoListUpload.file_name+'</li>';
							datacontentli +='<li><a href="<?php echo SITEURL . TODO; ?>'+data.DoListUpload.file_name+'" class="todoimglink" download >'+data.DoListUpload.file_name+'</a></li>';
						})
						datacontentli += '</ul>';
						$that.data('content',datacontentli);
						$('#subtodolist_'+do_list_id).popover('destroy');
						setTimeout(function(){
							$('#subtodolist_'+do_list_id).popover({
								trigger: 'hover',
								placement: 'bottom',
								html: true,
								container: 'body',
								template: '<div class="popover todouploadlistpop" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
								delay: {show: 50, hide: 400}
							}).popover('show');

						},100)
						$('#subtodolist_'+do_list_id).tooltip('hide');
					}
					//$that.attr('data-content',datacontentli);
				}
			})
		})


		/* $("body").on("mouseout", ".poptodolist, .subtodolist", function(){
			$(this).popover('destroy');
		}) */


		$("body").on("mouseover", ".poptodolist111", function(){


			$(this).popover('destroy');

			$that = $(this);
			var do_list_id = $that.data('todoid');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: $.param({'do_list_id':do_list_id }),
				url: $js_config.base_url + 'todos/getdouploadlist',
				global: false,
				success: function(response) {
					if(response){

						var datacontentli = '<ul>';
						$.each(response, function(key, data) {
							datacontentli +='<li><a href="<?php echo SITEURL . TODO; ?>'+data.DoListUpload.file_name+'" class="todoimglink" download >'+data.DoListUpload.file_name+'</a></li>';
						})
						datacontentli += '</ul>';
						$that.data('content',datacontentli);

						$('#todolist_'+do_list_id).popover('destroy');

						setTimeout(function(){
							$('#todolist_'+do_list_id).popover({
								trigger: 'hover',
								placement: 'bottom',
								html: true,
								container: 'body',
								template: '<div class="popover todouploadlistpop" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
								delay: {show: 50, hide: 400}
							}).popover('show');

							$('#todolist_'+do_list_id).tooltip('hide');

						},100)

					}
					//$that.attr('data-content',datacontentli);
				}
			})
		})


    })
</script>
<style>
    /* loading */

    .loader_bar {
        height: 2px;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

	.todo-radio{
		margin-top: 11px;
	}

    .loader_bar:before {
        display: block;
        position: absolute;
        content: "";
        left: -200px;
        width: 200px;
        height: 4px;
        background-color: #2980b9;
        animation: loading 2s linear infinite;
    }


    @keyframes loading {
        from {
            left: -200px;
            width: 30%;
        }

        50% {
            width: 30%;
        }

        70% {
            width: 70%;
        }

        80% {
            left: 50%;
        }

        95% {
            left: 120%;
        }

        to {
            left: 100%;
        }
    }


    .ui-datepicker-range > .ui-state-default {
        background: #f39c12 none repeat scroll 0 0;
        border-color: #c36c10;
        color: #fff;
        font-size: 12px;
    }
    .ui-datepicker .ui-datepicker-title {
        font-size: 12px;
        line-height: 1.8em;
        margin: 0 2.3em;
        text-align: center;
    }
    .ui-datepicker th {
        border: 0 none;
        font-size: 12px;
        font-weight: bold;
        padding: 0.7em 0.3em;
        text-align: center;
    }
    .ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next {
        height: 1.4em;
    }
    .ui-datepicker td {
        border: 0 none;
        padding: 0.5px;
    }
    .ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
        font-size: 12px;
    }
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
        font-size: 12px;
    }
    .ui-datepicker-group {
        width: 25.3%;
    }
    .bootstrap-select.form-control:not([class*="span"]){
        width: 450px;max-width: 100%;
    }

    .doc-type  {
        margin: 0 !important;
    }
    .created-date  {
        margin: 0px 0 0 !important;
        font-size: 12px;
    }
    .tab-content {
        overflow: visible !important;
    }
    #update-comment-list-by-users, #update-comment-list {
        border: 1px solid #ccc;
        max-height: 480px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 10px;
    }
    #update-comment-list-by-users > li:last-child, #update-comment-list > li:last-child {
        margin-bottom: 15px;
    }
    #update-comment-list-by-users {
        margin-top: 15px;
    }
    .radio-left{
        float:left;
    }

    .updatecommenttodoclass { margin-left: 5px;}

    .raio-left-select{
        float:left;
        margin-top:5px;
    }
    .my-rec-tab-wrapper {
        clear: both;
        display: block;
        float: left;
        width: 100%;
    }
    .my-rec-tab {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        border-color: #ccc #ccc -moz-use-text-color;
        border-image: none;
        border-radius: 3px;
        border-style: solid solid none;
        border-width: 1px 1px medium;
        float: left;
        margin-left: -1px;
        padding: 5px 15px;
        width: auto;
        cursor: pointer;
    }
    .selected-list {
        background: #f0f0f0;
    }
    #received_todos_wrapper {
        display: none;
    }
    .todocombutton_up{
        margin-bottom: 15px;
    }

	#people-comment-list {
		max-height: 430px;
		overflow-x: hidden;
		overflow-y: auto;
	}

	.todouploadlistpop {
		white-space: nowrap;
		text-overflow: ellipsis;
		width: 100%;
		display: block;
		/*overflow: hidden;*/
	}
	.todouploadlistpop ul {
		padding: 0 2px 0 2px;
		margin: 0;
		white-space: nowrap;
		text-overflow: ellipsis;
		width: 100%;
		display: block;
		overflow: hidden;
	}
	.todouploadlistpop ul li {
		padding: 2px 0;
		margin: 0;
		list-style:none;
		white-space: nowrap;
		text-overflow: ellipsis;
		line-height: normal;
		overflow: hidden;
        font-size: 12px;
	}
	.todouploadlistpop ul li a{
        color:#333;
        list-style: none;
        text-align: left !important;
        padding-left: 0 !important;
    }

	.to-dos-project-list {
		margin-right: 25px;
	}
	.to-dos-project-list {
		margin-right: 25px;
	}
	.to-dos-project-list .custom-dropdown select{
		width: 400px
	}
	@media (max-width:567px) {
		.to-dos-project-list {
		margin-right: 0px;
	}

	}
	@media (max-width:480px) {
	.to-dos-project-list {
		margin-right: 0px;
	}
	.to-dos-project-list .custom-dropdown select{
		width: 100%
	}


	}

	</style>
<?php
$summary = null;
// $status = $this->Todocomon->get_status(7,  'main');
?>

<?php
if (isset($prj_id) && !empty($prj_id)) {
    $cky = $this->requestAction('/projects/CheckProjectType/' . $prj_id . '/' . $this->Session->read('Auth.User.id'));
    ?>
    <script type="text/javascript" >
        $(function () {
            $('#project_change > option[value="' +<?php echo $prj_id ?> + '"]').attr('selected', 'selected');
            $('#project_change').trigger('change');
        })
    </script>
<?php } ?>

<div class="row" id="to-do-list">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"> <?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 6px 0"> <span style="text-transform: none;"><?php echo $page_subheading; ?></span> </p>
                </h1>
            </section>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php echo $this->Session->flash(); ?>
            </div>
        </div>

		<span id="project_header_image">
			<?php
				if( isset( $prj_id ) && !empty( $prj_id ) ) {
					echo $this->element('../Projects/partials/project_header_image', array('p_id' => $prj_id));
				}
			?>
		</span>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                    <!-- Header here-->
                    <div style="background: #f1f3f4 none repeat scroll 0px 0px; cursor: move; border-top: 1px solid #ddd; border-radius: 0; padding: 10px 10px 5px 10px;" class="box-header task-list-header">
                        <div class="form-groups clearfix">

                            <div class="raio-left-select to-dos-project-list">
                                <label class="control-label" style="margin-right: 10px; font-weight: normal;">Project: </label>
                                <label style=" " class="custom-dropdown">
                                    <?php
                                    echo $this->Form->input('project_change', array(
                                        'options' => $projects,
                                        'empty' => 'Select Project',
                                        'class' => 'form-control aqua',
                                        'id' => 'project_change',
                                        'label' => false,
                                        'div' => false,
                                        'style' => 'max-width: 100%; background: #E6E6E6;'
                                    ));
                                     ?>
                                </label>
                                <span class="loader-icon fa fa-spinner fa-pulse" style="display: none;"></span>
                            </div>

                            <div class="radio-left">
                                <div class="radio radio-warning todo-radio">
                                    <input type="radio" value="4" class="fancy_input change_checkbox" name="project_type" <?php if(!isset($prj_id) || empty($prj_id)){ ?> checked="checked" <?php } ?> id="project_type_unspecified">
                                    <label for="project_type_unspecified" class="fancy_labels">No Project</label>
                                </div>
                            </div>

		<div class="tast-list-right-head" id="buttons_panel" >

			<div class="tast-list-right-head-left pull-left">

			</div>

			<div class="tast-list-right-head-left pull-right">

				<span class="pull-right">
					<div class="todocombutton_up">
						<div class="todo_cald text-right">
							<?php
								if (isset($prj_id) && !empty($prj_id)) {
									$class = ""; //$class = "disabled";
									$p_permission = $this->Common->project_permission_details($prj_id, $this->Session->read('Auth.User.id'));
									$user_project = $this->Common->userproject($prj_id, $this->Session->read('Auth.User.id'));
									$gp_exists = $this->Group->GroupIDbyUserID($prj_id, $this->Session->read('Auth.User.id'));

									if (isset($gp_exists) && !empty($gp_exists)) {
										$p_permission = $this->Group->group_permission_details($prj_id, $gp_exists);
									}
									if (( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
										$class = '';
									}
								} else {
									$class = '';
								}
							?>



							<a href="<?php echo Router::Url(array("controller" => "todos", "action" => "manage"), true); ?><?php if(isset($prj_id) && !empty($prj_id)) {echo '/project:'.$prj_id;} ?>"  id="manage_todo" class="<?php echo $class; ?> btn btn-sm btn-warning"><i class="fa fa-plus"></i> Create To-do</a>
						</div>
					</div>
				</span>

			</div>

		</div>

                            <div class="clearfix"></div>


                        </div>
                        <div class="loader_bar" style="display: none;"></div>
                    </div>
                        <!-- MODAL BOX WINDOW -->
                        <div class="modal modal-success fade" id="todo_model" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content "></div>
                            </div>
                        </div>
                        <!-- END MODAL BOX -->
                        <!-- MODAL BOX WINDOW -->
                        <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content "></div>
                            </div>
                        </div>
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_small" class="modal modal-success fade">
                            <div class="modal-dialog">
                                <div class="modal-content"></div>
                            </div>
                        </div>

                        <!-- END MODAL BOX -->

                        <div class="box-body no-padding">
                            <!-- task list main  here-->
                            <div class="task-list-wrapper" id="page_body">
                                <?php echo $this->element('../Todos/partials/page_body'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>