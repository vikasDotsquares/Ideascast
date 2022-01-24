
<div class="el_ops">
	<?php echo $this->element('../Entities/element_files/element_options_partial', array('project_id' => $prj[0]['Project']['id'])); ?>
</div>







<div class="header-progressbar">
	<div class="progressbar-sec task-progress-bar">
		<?php echo $this->element('../Entities/element_files/task_progress_bar'); ?>
	</div>


</div>

<style>
	.popover .col-contant {
	    padding: 0;
	    min-height: 15px;
	}
	.popover .col-contant p {
		font-size:14px;
	}

	.taskcounters li {
	    cursor: pointer;
	}

	.popover .col-contant p {
	    font-weight: normal !important;
	    width: auto !important;
	    font-size: 12px !important;
	}

	.popover .col-contant p:first-child {
	    font-weight: normal !important;
	    width: auto !important;
	    font-size: 12px !important;
	}
	.task-schedule, .task-schedule .arrow-down {
		cursor: default !important;
	}
	.default-tooltip {
	    text-transform: none;
	}
	.el_ops .element-sign-off:hover {
	    background-color: unset;
	}

	.disableS {

	    opacity: .5;
	}
	.progress-assets li .assets-count.blue{ background:#3c8dbc; }
	.progress-assets li .assets-count.light-gray{ background:#a6a6a6; }
	.progress-assets li .assets-count.green-bg{ background:#5f9322; }
	.progress-assets li .assets-count.yellow { background:#e3a809; }
	.progress-assets li .assets-count.dark-gray{ background:#666666; }
	.progress-assets li .assets-count.red { background:#e5030d; }

	.content-wrapper{
	    background-color: #f1f3f4;
	}
	.box-header.filter{
	    background-color: #f1f3f4;
	    border-top: 1px solid #dcdcdc;
	}



	.el_ops .not-react:before {
	    content: "\f00c";
	    color: blue;
	}

	.el_ops .not-avail:before {
	    content: "\f00d";
	}

	.el_ops .accepted:before {
	    content: "\f00c";
	    color: #67a028;
	}

	.el_ops .not-accept-start:before {
	    content: "\f00c";
	    color: #DF0707;
	}

	.el_ops .disengage:before {
	    content: "\f04d";
	}


	.eassignment .not-avail{ color:#333; }
	.eassignment .disengage { color:#333; }


</style>

<script type="text/javascript">
$(function(){

	$('.ico-nudge.ico-task').on('click', function(event) {
		event.preventDefault();
		var hash = window.location.hash
		hash = hash.substring(1, hash.length)
		$('#modal_nudge').modal({
			remote: $(this).data('url')+'/hash:'+hash
		})
		.modal('show');
	});

	$('.pophover-popup').popover({
		placement : 'top',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	});


	$('.save_element_new').click(function(){

		$('.save_element').trigger('click');
	})


	$('body').delegate('.fav-current-task', 'click', function(event) {
			event.preventDefault();
			var $that = $(this);
			var project_id = $that.data('projectid');
			var task_id = $that.data('taskid');
			var tasktitle = $that.data('tasktitle');
			var tasktitlefull = $that.data('tasktitlefull');

			var pinCount = $(this).parents('#list_grid_container').find('li.li-listing .fav-current-project.active').length;

			var cpliHtml = '';
			if( !$(this).hasClass('remove_pin') ){

				if( project_id > 0 && project_id !== "" && task_id > 0 && task_id !== ""  ){
					var cpliHtml = '';
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id, 'task_id': task_id ,'status': 'add'}),
						url: $js_config.base_url + 'entities/current_task',
						global: false,
						success: function (response) {
							if( response.success ){

								//$that.find('i').removeClass('fa').removeClass('pin-thumbtack').addClass('current_task_icon_logo');
								$that.find('i').removeClass('headerbookmark').addClass('headerbookmarkclear');
								$that.addClass('remove_pin');
								$that.attr('title','');
								$that.attr('data-original-title','Clear Bookmark');

								var newUrl = $js_config.base_url+"entities/update_element/"+task_id+"#tasks";
								cpliHtml = '<li class="current_task" data-taskproject="'+project_id+'" id="currenttaskid_'+task_id+'"><a class="" href="'+newUrl+'"><span class="left-icon-all"><i class="left-nav-icon task-nav"></i></span> '+tasktitle+'</a></li>';

								$.add_task_sidebar(cpliHtml);

							}
						},
					});
				}
			}

			if( $(this).hasClass('remove_pin') ){
				if( project_id > 0 && project_id !== "" && task_id > 0 && task_id !== ""  ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id, 'task_id': task_id, 'status': 'remove' }),
						url: $js_config.base_url + 'entities/current_task',
						global: false,
						success: function (response) {
							if( response.success ){

								$that.removeClass('remove_pin');
								$that.attr('title','');
								$that.attr('data-original-title','Set Bookmark');
								//$that.find('i').removeClass('fa-bookmark').addClass('fa').addClass('fa-bookmark-o');
								$that.find('i').removeClass('headerbookmarkclear').addClass('headerbookmark');


								setTimeout(function(){
									$("#currenttaskid_"+task_id).remove();
								},100)

								//console.log($("#currenttaskid_"+task_id));

							}
						},
					});
				}
			}

		})

})

		function open_cost(cparams){
			location.href = $js_config.base_url+"costs/index/"+cparams;
		}

</script>

