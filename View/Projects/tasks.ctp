
<?php echo $this->Html->css('projects/list-grid'); ?>
<?php //echo $this->Html->css('projects/smart_menu/sm-core-css'); ?>
<?php //echo $this->Html->css('projects/smart_menu/sm-mint/sm-mint'); ?>
<?php echo $this->Html->css('projects/manage_elements'); ?>


<?php echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));  ?>
<?php echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));  ?>
<?php //echo $this->Html->script('projects/plugins/context-menu', array('inline' => true)) ?>
<?php //echo $this->Html->script('projects/plugins/smart-menu', array('inline' => true)) ?>

<?php //echo $this->Html->script('projects/elements_library_redisign', array('inline' => true)) ?>
<?php echo $this->Html->script('projects/manage_elements', array('inline' => true)); ?>

<?php echo $this->Html->script('projects/color_changer', array('inline' => true)); ?>


<style type="text/css">
	/* 	.popover-content {
	    padding: 9px 5px 9px 9px !important;
		text-align:center !important;
	} */
	#workspace td .box-body {
	    max-height: 5000px;
	}
	.drop_start {
	    background-color: #fbfbfb;
	    /*border: 1px solid #eee;*/
	}
	.ws_color_box {
	  background: rgb(255, 255, 255) none repeat scroll 0 0;
	  border: 1px solid #dddddd;
	  border-radius: 5px;
	  left: auto;
	  margin: 0;
	  padding: 5px;
	  position: absolute;
	  right: 36px;
	  top: -18px;
	  width: 83px;
	  z-index: 9999;
	}
	.option-panel {
		text-align: center;
		margin-bottom: 3px;
	}
	.element-icons-list li {
		position: relative;
		z-index: 0;
	}
	.option-panel .btn-default {
	    background-color: #b5bbc8;
	    border-color: #919398;
	    color: #ffffff;
	}
	.option-panel .btn-default:hover {
	    opacity: 0.9;
	}
	.color_box_wrapper:hover {
	     margin-left: -1px !important;
	}

	.el-filters {
		display: inline-block;
		margin: 0 5px;
	}
	.el-filters .selected-filters {
		min-width: 80px;
		display: inline-block;
    	text-align: left;
	}
	.el-filters .filter-control {
		background: #fff;
    	border: 1px solid #ccc;
    	font-size: 13px;
	}
	.filter_elements {
		font-size: 13px;
	}
	.filter_elements label {
		margin: 0;
		font-weight: normal;
		display: flex;
		align-items: center;
	}
	.filter_elements label.all-statuses {
		cursor: pointer;
	}
	.filter_elements label input {
	    margin-right: 5px;
	    margin-top: 0;
	}
	.filter_elements.disabled li {
	    pointer-events: none;
	}

	.filter_elements label span {
	    display: inline-block;
	    line-height: 13px;
	}
	.open ul.dropdown-menu.filter_elements>li a {
	    padding: 10px 20px;
	}

	/* cog-setting css */
	.ul-menus {
		display: block;
	    list-style: none;
	    margin: 0;
	    padding: 0;
	    line-height: normal;
	    direction: ltr;
	    -webkit-tap-highlight-color: rgba(0,0,0,0);
	    font-size: 13px !important;
	}
	.ul-menus li {
	    display: block;
	    list-style: none;
	    margin: 0;
	    padding: 0;
	    line-height: normal;
	    direction: ltr;
	    -webkit-tap-highlight-color: rgba(0,0,0,0);
        border-bottom: 1px solid #d9d9d9;
	}
	.ul-menus li:last-child {
	    border-bottom: none;
	}
	.ul-menus li a {
    	background-color: #eeeeee;
    	display: block;
	    padding: 5px 10px;
	    color: #333;
	}
	.ul-menus li a:hover {
	    background: #DDDDDD;
    	color: #222222;
	}
	.ul-menus li a span {
	    display: inline-block;
	    margin-left: 5px;
	}

	.ul-menus .open ul.dropdown-menu>li a {
	    padding: 8px 10px;
	    font-size: 13px;
	}
	.ul-menus .open ul.dropdown-menu>li a i {
	    margin-right: 5px;
	}

	.setting-dropdown { /* dropdown-toggle */
		position: relative;
	}
	.setting-dropdown-menu { /* dropdown-menu */
	    position: absolute;
	    top: 100%;
	    left: 100%;
	    z-index: 1000;
	    display: none;
	    float: left;
	    min-width: 160px;
	    padding: 5px 0;
	    margin: 2px 0 0;
	    font-size: 14px;
	    text-align: left;
	    list-style: none;
	    background-color: #fff;
	    -webkit-background-clip: padding-box;
	    background-clip: padding-box;
	    border: 1px solid #ccc;
	    border: 1px solid rgba(0,0,0,.15);
	    border-radius: 0;
	    -webkit-box-shadow: none;
	    box-shadow: none;
	}
	.setting-dropdown-submenu { /* dropdown-submenu */
	    position: relative;
	}
	.dropdown-menu > li > a { /* .dropdown-menu > li > a */
	    display: block;
	    clear: both;
	    font-weight: 400;
	    line-height: 1.42857143;
	    white-space: nowrap;
	}

	.ul-menus a.dropdown-toggle:hover:after {
	    border-left: 7px solid #000;
	}
	.ul-menus .open a.dropdown-toggle:after {
	    border-left: 7px solid #000;
	}

	.ul-menus a.dropdown-toggle::after {
	    content: '';
	    position: absolute;
	    left: 90%;
	    top: 35%;
	    width: 0;
	    height: 0;
	    border-top: 4px solid transparent;
	    border-bottom: 4px solid transparent;
	    border-left: 7px solid #c00;
	    clear: both;
	}
	.ul-menus a.delete-an-item:hover {
	    color: #c00;
	}
	li.dropdown-header {
	    padding: 5px 20px;
	    background-color: #585858;
	    color: #fff;
	}
	.custom-drop {
		top: -30px ;
		left: 100% ;
		box-shadow: none !important;
    	border-radius: 0 !important;
    	font-size: 13px;
	}
	.custom-drop li.dropdown-submenu > .dropdown-menu {
	    left: 100%;
	    top: -29px;
		box-shadow: none !important;
    	border-radius: 0 !important;
	}
	.paste-menus {
		box-shadow: none !important;
	    border-radius: 0;
	    border-color: #999 !important;
	    min-width: 100px;
	}
	.paste-menus a {
	    background-color: #eeeeee !important;
	    padding: 5px 10px !important;
	    color: #333 !important;
	}
	.paste-menus a:hover {
	    background: #DDDDDD !important;
	    color: #222222 !important;
	}
	#copy_to_list,#move_to_list {
		display: none;
	}
	.btn-paste {
		transition: all 0.5s;
	}
	.fade-out {
		display: none;
		/*transform: scale(1.2);*/
	}
	.fade-in {
		display: inline-block;
		/*transform: scale(1.2);*/
	}
	/* cog-setting css */
</style>

<script type="text/javascript" >

$(function(){
	$(window).on('resize', function(){
		$('.key_target').textdot();
	})
	$("#lg_model_box").on('hidden.bs.modal', function() {
		$(this).removeData()
		$(this).find('.modal-content').html('');
	})

	$("#risk_model_box").on('hidden.bs.modal', function() {
		$(this).removeData()
		$(this).find('.modal-content').html('');
	})


	$("#popup_modal").on('hidden.bs.modal', function() {
		$(this).removeData()
		$(this).find('.modal-content').html('');
	})



	$('#modal_medium').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});

	$('#modal_medium').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});


		if($( window ).width() > 1280 ){
		if( $('#tbl tr').length == 1){
			var extraH = $('.fliter.margin-top').height() + $('#table-responsive').height() +  $('.content-wrapper .content-header').height() + $('.row .content-header').height() +$('.navbar').height() ;
			 var TotH = $('.content-wrapper').innerHeight();
			availH = TotH - extraH;
			}
		}

		function resizeStuff() {
			$('.ellipsis-words').ellipsis_word();
			$('.key_target').textdot();
		}

	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		// TO = setTimeout(resizeStuff, 1000); //200 is time in miliseconds
	});

	// ============ Start Risk Popup =================================
		$('body').delegate('.popover .el_users', 'click', function(e){
			// e.preventDefault()
			setTimeout($.proxy(function() {
				$(this).parents('.popover:first').data('bs.popover').$element.popover('hide');
			}, this), 300);
		})

		$('.modal').on('show.bs.modal', function (event) {
			$('html').addClass('modal-open');
		})
		$('.modal').on('shown.bs.modal', function (event) {
			$(event.relatedTarget).tooltip('destroy')
		})
		$('.modal').on('hidden.bs.modal', function (event) {
			$('.tooltip').hide();
			$('html').removeClass('modal-open');
		})
	// ============ End Risk Popup ===================================

	//====================== Element Filters ==========================//
	$.set_filter_text = function(){
		var sel_text = '';
		var selected_value = $('.dropdown-menu#filter_elements input[type="checkbox"]:checked').map(function() {
	        return this.value;
	    }).get();
		if(selected_value.length) {
			if(selected_value.length == 1){
				$('.dropdown-menu#filter_elements input[type="checkbox"]:checked').each(function(index, el) {
					sel_text = $(this).parent().find('span').text();
				});
			}
			else if(selected_value.length == $('.dropdown-menu#filter_elements input[type="checkbox"]').length){
				sel_text = 'All Selected';
			}
			else{
				sel_text = 'Selected ('+selected_value.length+')';
			}
	    }
	    else{
	    	sel_text = 'All Statuses';
	    }
	    $('.filter-control .selected-filters').text(sel_text);
	}
	$('.dropdown-menu#filter_elements').on('click', function(e) {
	  	e.stopPropagation();
	  	if($(e.target).is('.all-statuses')) {
	  		$('.dropdown-menu#filter_elements input[type="checkbox"]').prop('checked', false);
	  		$('.el').show();
	  		$.get_filtered_tasks();
	  		$.cookie("selected_value", "" );
	  	}
	});

	$.show_filtered_tasks = function() {
		var selected_value = $('.dropdown-menu#filter_elements input[type="checkbox"]:checked').map(function() {
	        return this.value;
	    }).get();
		if(selected_value.length) {
	    	$.each(selected_value, function(index, el) {
	    		$('.el[data-status="'+el+'"]').show();
	    	});
	    }
	    else{
	    	$('.el').show();
	    }
	}
	$.get_filtered_tasks = function(selected_value){
		//$(".fix-progress-wrapper").data_loader();
		$('.dropdown-menu#filter_elements').addClass('disabled');
		$.set_filter_text();
		$.ajax({
			url: $js_config.base_url + 'entities/get_workspace_template',
			type: 'POST',
			data: {
				project_id: $js_config.project_id,
				workspace_id: $js_config.workspace_id,
				status: selected_value,
			},
			success: function(response){
				// console.log(response);
				$('#workspace').html(response);
				$('.dropdown-menu#filter_elements').removeClass('disabled');
				$('.tooltip').remove();
	            // $.bind_context_menu('clear');
	            $.bind_dragDrop();
	            $('.color_bucket').each(function() {
	                var $color_box = $(this).parent().find('.ws_color_box');
	                $(this).data('ws_color_box', $color_box);
	                $color_box.data('color_bucket', $(this));
	            })
	            $.show_filtered_tasks();
			}
		});
	}

	$('.dropdown-menu#filter_elements input[type="checkbox"]').on('change', function(e) {

	  	var selected_value = $('.dropdown-menu#filter_elements input[type="checkbox"]:checked').map(function() {
	        return this.value;
	    }).get();
	    if(selected_value.length) {
	    	$.get_filtered_tasks(selected_value);
	    }
	    else{
	    	$.get_filtered_tasks();
	    }
	});


	($.check_options = function(){

		if (typeof $.cookie('selected_value') !== 'undefined'){
			var cookieValue = $.cookie("selected_value");

			if(cookieValue.length){
				var all = JSON.parse(cookieValue);
				if(all.hasOwnProperty('checked') && all.wsp_id == $js_config.workspace_id) {
					if($.isArray(all.checked)){
						$.each(all.checked, function(index, val) {
							$('.dropdown-menu#filter_elements input[type="checkbox"][value="'+val+'"]').prop('checked', true)
						});
					}
				}
			}

			var selected_value = $('.dropdown-menu#filter_elements input[type="checkbox"]:checked').map(function() {
		        return this.value;
		    }).get();
		    if(selected_value.length) {
		    	$.get_filtered_tasks(selected_value);
		    }
		    else{
		    	$('.el').show();
		    }
		    $.cookie("selected_value", "" );
		}
		else{
			$('.el').show();
		}

	})();

	$.save_filter_cookie = function(){
		var selected_value = $('.dropdown-menu#filter_elements input[type="checkbox"]:checked').map(function() {
	        return this.value;
	    }).get();
	    if(selected_value.length) {
	    	$('.el').hide();
	    	$.each(selected_value, function(index, el) {
	    		$('.el[data-status="'+el+'"]').show();
	    	});
	        var json_str = {'checked':selected_value, 'wsp_id': $js_config.workspace_id};
	        $.cookie("selected_value", json_str );
	    }
	    else{
	    	$('.el').show();
	    	$.cookie("selected_value", "" );
	    }
	}
	$('body').delegate('.btn-open', 'click', function(event) {
		event.preventDefault();
		$.save_filter_cookie();
	    location.href = $(this).attr('href');
	});
	//====================== Element Filters ==========================//


})

$(window).load(function() {

	setTimeout(function(){
		$('.key_target').textdot();
		$('.ellipsis-words').ellipsis_word();
			}, 1500)

		$('.area_info').mouseover(function(){
			setTimeout(function(){
				$('.tooltip').css('text-transform','none');
			}, 200);
		})

		$('.small-box .inner').on('click', function(event) {
			event.preventDefault()
		});

		// PASSWORD DELETE
		$.current_delete = {};
		$('body').delegate('.delete-an-item', 'click', function(event) {
			event.preventDefault();
			$.current_delete = $(this);
		});

		$('#modal_delete').on('hidden.bs.modal', function () {
	        $(this).removeData('bs.modal');
	        $(this).find('.modal-content').html('');
	        $.current_delete = {};
	    });
})
</script>
<?php
	$class = 'collapse';
	if(isset($in) && !empty($in)){
		$class = 'in';
	}
	//pr($this->Session->read('user'));

	$per_page_show = $this->Session->read('project.per_page_show');
	$keyword = $this->Session->read('user.keyword');
	$status = $this->Session->read('project.status');
	$country = $this->Session->read('project.country');
	$stt = $this->Session->read('element.start');
	$endd = $this->Session->read('element.end');
?>
<div class="pull-right padright" style="display:none">
	<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
		Search
	</a>
	<button  class="btn btn-primary" id="resize_window">Resize</button>
</div>


 <div class="<?php echo $class; ?> search" id="collapseExample" style="display:none;">
	<div class="well">
		<?php echo $this->Form->create('User', array('type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form'));
		?>
			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-2">
						<label for="focusedInput" class="control-label">Keyword:</label>
					</div>
					<div class="col-lg-4">
						<?php echo $this->Form->input('keyword', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
					</div>


				  <label for="UserUser" class="col-lg-2 control-label">Start:</label>
				  <div class="col-lg-4">
					<div class="input-group">

						<?php echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false, 'readonly' => 'readonly', 'class'	=> 'form-control dates input-small' ] ); ?>
						<div class="input-group-addon open-start-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				  </div>
				  <label for="UserUser" class="col-lg-2 control-label">End:</label>
				  <div class="col-lg-4">
				  <div class="input-group">
						<?php echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small' ] ); ?>
						<div class="input-group-addon  open-end-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				  </div>

					<div class="col-lg-2">
						<label for="focusedInput" class="control-label">Status:</label>
					</div>
					<div class="col-lg-4">
						<?php $options = array( '0'=>'No Status Given','1'=>'Progressing','2' => 'Not Started', '3'=>'Completed','4'=>'Overdue');
						 echo $this->Form->input('status', array('options'=>$options, 'empty' => '- Select Status -','label' => false, 'selected'=>$status, 'div' => false, 'class' => 'form-control')); ?>
					</div>

					<div class="col-lg-12" style="text-align:right;margin:20px 0 0 0">
						<button type="submit" class="searchbtn btn btn-success">Go</button>
						<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>projects/element_resetfilter/<?php echo $this->params['pass'][0]."/".$this->params['pass'][1]; ?>" >Close</a>
					</div>
				</div>
			</div>
			</form>
	</div>
</div>



<div class="row">

	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $data['page_heading'] ?></h1>
					<?php
					$menu_project_id = null;
					if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
						$menu_project_id = $this->params['pass'][0];
					}
					// LOAD PARTIAL FILE FOR TOP DD-MENUS

				?>
				<p class="text-muted date-time pull-left" style="min-width: 100%; padding: 5px 0px;">
					<span><?php echo $data['page_subheading'] ?>
					<?php

					if(isset($data['workspace']['Workspace']['template_relation_id']) && $data['workspace']['Workspace']['template_relation_id'] > 0){
					$ruid = $this->Common->tempRelationUser($data['workspace']['Workspace']['template_relation_id']);

						if($ruid != 'IdeasCast'){
							//$ruDetail = $this->Common->userFullname($ruid);
							// echo ", Template Created by : ".$ruid;
							echo '(Template)';
						}else{
							// echo ", Template Created by : ".$ruid;
							echo '(Template)';
						}
					}
					?></span>
				</p>
			</section>
		</div>

	<?php echo $this->element('../Projects/partials/project_header_image', array('p_id' => $this->params['pass'][0])); ?>

		<div class="box-content">

			<div class="row ">


				<div class="col-xs-12">

				<div style="padding :15px 0; margin:  0;  border-top-left-radius: 3px; background-color: #f5f5f5; overflow:visible; border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd" class="fliter margin-top">


				 <?php echo $this->element('../Projects/partials/element_settings', array('menu_project_id' => $menu_project_id)); ?>

				</div>

					<div class="box noborder ">
						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding noborder" style="background: none repeat scroll 0 0 #ecf0f5; height: auto">

							<div class="btn-group pull-right" style="opacity: 0; display: none;" id="element_options" >

								<button  title="Remove Task" data-remote="<?php echo SITEURL.'entities/remove_element'; ?>" id="btn_remove_element" class="btn bg-black btn-sm remove_element tipText"><i class="fa fa-trash"></i></button>

								<input type="hidden" name="element_id" id="element_id" value="" />

								<button  title="<?php echo tipText('Cut') ?>" id="btn_cut" class="btn bg-black btn-sm btn_cut tipText" style="border-right: 2px solid #fff;"><i class="fa fa-cut"></i></button>

								<button  title="<?php echo tipText('Copy') ?>" id="btn_copy" class="btn bg-black btn-sm tipText btn_copy"><i class="fa fa-copy"></i></button>

								<span class="btn bg-black btn-sm color_box_wrapper" style="border-radius: 0px 3px 3px 0px;" >
									<span class="color_bucket tipText" title="<?php echo tipText('Edit Colors') ?>" ><i class="fa fa-paint-brush"></i></span>
									<div class="el_colors" style="display: none;">
										<div class="colors btn-group" style="width:100%;">
											<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
											<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
											<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
											<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
											<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
											<a href="#" data-color="panel-green" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Green"><i class="fa fa-square text-green"></i></a>
											<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
											<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>

											<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
										</div>
									</div>
								</span>
								<button  title="<?php echo tipText('Close Options') ?>" class="btn btn-danger btn-sm tipText" id="close_options"><i class="fa fa-times"></i></button>
							</div>

							<div id="myPopoverModal" class="popover popover-default">
								<div class="popover-content">
								</div>
								<div class="popover-footer">
									<button type="submit" class="btn btn-sm btn-primary">Submit</button><button type="reset" class="btn btn-sm btn-default">Reset</button>
								</div>
							</div>

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
								</div>
                            </div>



							<!-- END MODAL BOX -->


							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="lg_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
								</div>
                            </div>

                        <!-- // PASSWORD DELETE -->
						<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content"></div>
							</div>
						</div>



							<!-- END MODAL BOX -->


                        </div>
						<!-- END CONTENT HEADING -->


					<div class="box-body border-top " style="padding: 0">
						<?php
						// pr($project_id, 1);
							$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

							$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

							if(isset($gpid) && !empty($gpid)) {
								$wwsid = $this->Group->group_work_permission_details($project_id, $gpid);
								//pr($wwsid); die;
							}

							if(isset($p_permission) && !empty($p_permission))
							{
								$wwsid = $this->Common->work_permission_details($project_id, $this->Session->read('Auth.User.id'));
							}


						$workspaceArray = $data['workspace']['Workspace'];
						$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';
							$workspace_areas = $this->ViewModel->workspace_areas($workspaceArray['id']);

							$w_a_total = $this->ViewModel->workspace_areas($workspaceArray['id'], true);

							$totalAreas = $totalActElements = $totalInActElements = $totalUsedArea = $percent = 0;

							if ($w_a_total > 0) {

								$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id']);
								if (isset($progress_data) && !empty($progress_data)) {
									// pr($progress_data);
									$totalAreas = $progress_data['area_count'];
									$totalUsedArea = $progress_data['area_used'];
									$totalActElements = $progress_data['active_element_count'];
									$totalInActElements = 0;

									$percent = ($totalUsedArea > 0 && $totalAreas > 0) ? ($totalUsedArea * 100) / $totalAreas : 0;
								}
							}

						// pr($data['workspace'], 1);
						?>


						<div id="table-responsive" class="table-responsive wsp_keyresult_icon">
							<table class="table table-bordered" id="">
								<thead class="sort-theader">
									<tr>
										<th width="30%" style="text-align:center">Workspace</th>
										<th width="25%" style="text-align:center">Key Result Target</th>
										<th width="8%" style="text-align:center">Tasks</th>
										<th width="24%" style="text-align:center">Resources</th>
										<th width="13%" style="text-align:center">Actions</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="small-box task-inworks panel <?php echo $class_name ?>">

												<a class="inner"  style="cursor:default;" href="#">

													<strong class="ellipsis-word ellipsis-words tipText" title='<?php echo html_entity_decode(strip_tags($workspaceArray['title'])); ?>'  style="text-transform:none !important"><?php // workspace_title truncate
													echo html_entity_decode(strip_tags($workspaceArray['title'])) ; //echo _substr_text($workspaceArray['title'], 29); ?></strong>
													<div class="reminder-sharing-d-in">
													<span class="text-muted date-time">
														<span>Created:
														<?php
														//echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? date('d M Y', strtotime($workspaceArray['created'])) : 'N/A';

														echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A';


														?></span>
														<span>Updated:
														<?php
														//echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? date('d M Y', strtotime($workspaceArray['modified'])) : 'N/A';
														echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';

														?></span>
													</span>

													<span class="text-muted date-time" style="padding: 0px; margin: 0px ! important;">
														<span>Start:
														<?php  echo ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? date('d M Y', strtotime($workspaceArray['start_date'])) : 'N/A';

														//echo ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['start_date'])),$format = 'd M Y') : 'N/A';


														?></span>
														<span>End:
														<?php  echo ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? date('d M Y', strtotime($workspaceArray['end_date'])) : 'N/A';

														//echo ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($workspaceArray['end_date'])),$format = 'd M Y') : 'N/A';
														?></span>
													</span>

												</a>

											</div>
										</td>
										<td style="vertical-align: top ! important; font-size: 12px; line-height: 16px; max-width:408px;" class=" ">
											<div style="max-height: 65px;  overflow: hidden;" class="key_target">
												<?php echo  nl2br($workspaceArray['description']) ; ?>
											</div>
										</td>
										<td style="vertical-align: middle ! important;">
											<span class="text-center el-icons" >
												<ul class="list-unstyled">
													<li  style="cursor:default;">
														<span class="label bg-mix" title=""><?php echo ($totalActElements ); ?></span>
														<span style="cursor:default;"  class="icon_elm btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Tasks ') ?>"  ></span>
													</li>
													<li  >
														<span class="label bg-mix">
															<?php
																// get areas
																$element_detail = null;
																$sum_value = 0;
																$area_id = $this->ViewModel->workspace_areas($workspaceArray['id'], false, true);


																$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

																$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

																$grp_id = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));

																$el_permission = $this->Common->element_permission_data($project_id,$this->Session->read('Auth.User.id'));

																if(isset($grp_id) && !empty($grp_id)){

																$p_permission = $this->Group->group_permission_details($project_id,$grp_id);
																$el_permission = $this->Group->group_element_permission_data($project_id,$grp_id);


																}

																//pr($el_permission );

																if((isset($el_permission) && !empty($el_permission)))
																{
																	$el = $this->ViewModel->area_elements_permissions($area_id, false,$el_permission);
																}

																if(((isset($user_project) && !empty($user_project)) || (isset($project_level) && $project_level==1)   ||  (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) )){
																	$el = $this->ViewModel->area_elements($area_id);
																}



																if (!empty($el)) {
																	$element_detail = _element_detail(null, $el);

																	if (!empty($element_detail)) {
																		$filter = arraySearch($element_detail, 'date_constraint_flag');
																		if (!empty($filter)) {
																			$sum_value = array_sum(array_columns($element_detail, 'date_constraint_flag'));
																			if (!empty($sum_value)) {
																			}
																		}
																	}
																}
																echo $sum_value;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-element no-change tipText" title="<?php echo tipText('Overdue Statuses ') ; ?>"  href="#"><i class="fa fa-exclamation"></i></span>
													</li>
												</ul>
											</span>

										</td>
										<td style="vertical-align: middle ! important;">
											<span class="text-center el-icons" >
												<ul class="list-unstyled">
													<li  style="cursor:default;">
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-maroon no-change tipText" title="<?php echo tipText('Links ') ?>"  href="#"><i class="fa fa-link"></i></span>
													</li>
													<li  style="cursor:default;">
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-purple no-purple tipText" title="<?php echo tipText('Notes ') ?>"  href="#"><i class="fa fa-file-text-o"></i></span>
													</li>
													<li  style="cursor:default;">
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-blue no-change tipText" title="<?php echo tipText('Documents ') ?>"  href="#"><i class="fa fa-folder-o"></i></span>
													</li>

													<li  style="cursor:default;">
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-green no-change tipText" title="<?php echo tipText('Mind Maps ') ?>"  href="#"><i class="fa fa-sitemap"></i></span>
													</li>


													<li  style="cursor:default;">
														<span class="label bg-mix"><?php echo show_counters($workspaceArray['id'], 'decision'); ?></span>
														<span style="cursor:default;" class="btn btn-xs bg-orange no-change tipText" title="<?php echo tipText('Live Decisions ') ?>"  href="#"><i class="far fa-arrow-alt-circle-right"></i></span>
													</li>
													<li  style="cursor:default;">
														<span class="label bg-mix"><?php
															echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
														?></span>
														<span style="cursor:default;" class="btn btn-xs bg-teal no-change tipText" title="<?php echo tipText('Live Feedbacks ') ?>"  href="#"><i class="fa fa-bullhorn"></i></span>
													</li>

													<li  style="cursor:default;">
														<span class="label bg-mix">
															<?php
																echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
															?>
														</span>
														<span style="cursor:default;" class="btn btn-xs bg-yellow no-change tipText" title="<?php echo tipText('Live Votes ') ?>"  href="#"><i class="fa fa-inbox"></i></span>
													</li>
												</ul>
											</span>
										</td>
										<td  style="vertical-align: middle ! important; text-align: center ! important; ">
											<div class="btn-group btn-actions">
												<?php  $wid = encr($workspaceArray['id']); ?>
												<!--<a class="btn btn-sm <?php echo $class_name ?> tipText" title="Show Key Result"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-remote="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-target="#popup_modal"  data-modal-width="600" data-toggle="modal" >
													<i class="fa fa-fw fa-eye"></i>
												</a>-->

												<?php
													if( (isset($wwsid) && !empty($wwsid))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission) && !empty($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] ==1 )    ))

													if(isset($gpid) && (isset($wwsid) && !empty($wwsid))){
														$wsEDDDIT =  $this->Group->group_wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

														$wsDELETE =  $this->Group->group_wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

														}else if((isset($wwsid) && !empty($wwsid))){
														$wsEDDDIT =  $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));

														$wsDELETE =  $this->Common->wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));
													}

													if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) ) { ?>
													<a class="btn btn-sm <?php echo $class_name ?> tipText" title="<?php tipText('Update Workspace Details', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_id, $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
														<i class="fa fa-fw fa-pencil"></i>
													</a>
												<?php  } ?>
												<?php
													if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )    ) ) { ?>
													<a class="btn btn-sm <?php echo $class_name ?> tipText color_bucket" title="Color Options"  href="#" style="margin-right: 0 !important;">
														<i class="fa fa-paint-brush"></i>

													<small class="ws_color_box" style="display: none; ">
														<small class="colors btn-group" style="width:100%;">
															<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
															<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
															<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
															<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
															<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
															<!-- <b data-color="bg-orange" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></b>	-->
															<b data-color="bg-teal" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
															<b  data-color="bg-purple" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
															<b data-color="bg-navy" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
															<b data-color="bg-gray" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
														</small>
													</small>
													</a>
												<?php  } ?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="workspace">

							<?php
								// LOAD PARTIAL WORKSPACE LAYOUT FILE FOR LOADING DYNAMIC WORKSPACE AREAS
								echo $this->element('../Projects/partials/workspace_layout', ['load' => true]);
							?>

						</div>


					</div><!-- /.box-body -->
					</div><!-- /.box -->
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
	<div class="modal modal-success fade" id="popup_modal" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content border-radius"><div style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;"><img src="<?php echo SITEURL;?>images/ajax-loader-1.gif" style="margin: auto;"></div></div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<style>
		/* ul.list-inline li { vertical-align:top; height: 180px !important; }
		ul.list-inline li .list-hed { border-bottom:2px solid #ddd; }
		#workspace{ height:280px; overflow:auto; } */

		.popover span:first-child {
			width: auto !important;
		}
	</style>

<style type="text/css">
	.quickee-container {
	    position: absolute;
	    background-color: #fff;
	    padding: 2px;
	    border-radius: 5px;
	    z-index: 1050;
	    transition: all .5s ease-in-out;
	    box-shadow: 0 5px 10px rgba(0,0,0,.2);
	    /*left: 0;
	    top: 0;*/
	}

	.quickee-container .arrow {
	    width: 0;
	    height: 0;
		position: absolute;
	}
	.quickee-container .arrow.bottom {
		border-left: 7px solid transparent;
	    border-right: 7px solid transparent;
	    border-bottom: 7px solid #fff;
	    left: 40%;
	    top: -7px;
	}
	.quickee-container .arrow.top {
		border-left: 7px solid transparent;
	    border-right: 7px solid transparent;
	    border-top: 7px solid #fff;
	    left: 40%;
	    top: 100%;
	}
	.quickee-container .arrow.left {
	    border-top: 7px solid transparent;
	    border-bottom: 7px solid transparent;
	    border-left: 7px solid #fff;
	    right: -7px;
	    top: 25%;
	}
	.quickee-container .arrow.right {
		border-top: 7px solid transparent;
	    border-bottom: 7px solid transparent;
	    border-right: 7px solid #fff;
	    left: -7px;
	    top: 25%;
	}
	.quickee-container .quickee-items {
	    position: relative;
	    display: inline-flex;
	    vertical-align: middle;
	}
	.quickee-items .item {
	    display: inline-block;
	    font-weight: 400;
	    color: #212529;
	    text-align: center;
	    vertical-align: middle;
	    -webkit-user-select: none;
	    -moz-user-select: none;
	    -ms-user-select: none;
	    user-select: none;
	    background-color: transparent;
	    border: 1px solid transparent;
	    padding: .375rem .75rem;
	    font-size: 1rem;
	    line-height: 1.5;
	    border-radius: .25rem;
	    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;

        padding: 5px 10px;
	    font-size: 12px;
	    line-height: 1.5;
	}
	.quickee-items .item.item-default {
	    color: #fff;
	    background-color: #343a40;
	    border-color: #323335;
	}
	.quickee-items .item.item-blue {
	    color: #fff;
	    background-color: #007bff;
	    border-color: #3390f2;
	}
	.quickee-items .item.item-red {
	    color: #fff;
	    background-color: #dc3545;
	    border-color: #cd1f30;
	}
	.quickee-items .item.item-yellow {
	    color: #fff;
	    background-color: #f39c12;
	    border-color: #f8c52a;
	}
	.quickee-items .item.item-cyan {
	    color: #fff;
	    background-color: #17a2b8;
	    border-color: #127e90;
	}
	.quickee-items .item.item-green {
	    color: #fff;
	    background-color: #28a745;
	    border-color: #1f9c3c;
	}
	.quickee-items > .item {
	    position: relative;
	    -ms-flex: 1 1 auto;
	    flex: 1 1 auto;
	}
	.quickee-items > .item {
	    border-radius: 0;
	}
	.quickee-items > .item:first-child {
	    border-top-right-radius: 0;
	    border-bottom-right-radius: 0;
	    border-bottom-left-radius: .25rem;
	    border-top-left-radius: .25rem;
	}
	.quickee-items .item:last-child {
	    border-top-right-radius: .25rem;
	    border-bottom-right-radius: .25rem;
	    border-bottom-left-radius: 0;
	    border-top-left-radius: 0;
	}
	.quickee-items .item+.item, .quickee-items .item+.quickee-items, .quickee-items .quickee-items+.item, .quickee-items .quickee-items+.quickee-items {
	    margin-left: -1px;
	}
</style>
<?php if($_SERVER['REMOTE_ADDR'] == '192.168.4.218'){ ?>
	<?php echo $this->Html->script('plugins/gs/quickee', array('inline' => true)); ?>
	<script type="text/javascript">
		$(function(){
			$('.btn-quickee').quickee();
		})
	</script>
<?php } ?>


