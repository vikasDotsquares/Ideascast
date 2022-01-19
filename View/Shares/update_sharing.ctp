<?php

// Set it to true to collapse all tree levels on page load.
// Also assign this value to the global js variable
$collapse = false;
?>

<script type="text/javascript">

jQuery(function($) {

	$js_config.start_collapse = '<?php echo $collapse; ?>';

	$js_config.share_action = '<?php echo (isset($share_action)) ? $share_action : 0; ?>';

})
</script>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
 ?>
<?php
echo $this->Html->css('projects/tooltip');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>

<script type="text/javascript">
jQuery(function($) {
	$('input[type=checkbox]').prop('checked', false);

	$('body').delegate('label.permissions', 'click', function(event) {

		var e = $(this);
		if( e.hasClass('unchangable') ) return;

		var $input = $(this).find('input[type=checkbox]'),
		iName = $input.attr('name'),
		$options = $('.propogate-options');

		$input.prop("checked", !$input.prop("checked"));

		if($input.prop("checked")) {
			$(this).addClass('active')
		}
		else {
			$(this).removeClass('active')
		}

		var $parent = $(this).parent();

		var active_length = ($parent.find('label.active').length) ? $parent.find('label.active').length : 0;

		// if edit permission is to be deactivated then deactivate copy and move permissions also
		if( $(this).is($('.permit_edit')) ) {

			if( !$(this).hasClass( 'active' ) ) {

				// deactivate copy permission
				var $copy = $parent.find('.permit_copy')
				if( $copy.hasClass('active') ){
					$copy.removeClass('active');
					$copy.find('input[type=checkbox]').prop("checked", false);
				}

				// deactivate move permission
				var $move = $parent.find('.permit_move');
				if( $move.hasClass('active') ) {
					$move.removeClass('active');
					$move.find('input[type=checkbox]').prop("checked", false);
				}

			}

		}

		// if edit permission is not activated then restrict move and copy permissions
		if( ( $(this).hasClass('permit_move') ||  $(this).hasClass('permit_copy') ) &&  !$parent.find('.permit_edit').hasClass('active') ) {

			$(this).removeClass('active')
			$input.prop("checked", false);

			return;

		}

		// if clicked other than read permission and read button has not an active class
		// add it manually
		if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent.find('.permit_read').hasClass('active') ) {

			$parent.find('.permit_read').addClass('active');
			$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

		}
		// if only one permission is given but its not the read permission
		else if( $(this).is($('.permit_read')) && active_length > 0 ) {

			$parent.find('.permit_read').addClass('active');
			$parent.find('.permit_read').find('input[type=checkbox]').prop('checked', true);

		}


		if($(this).parents('.area-list:first').find('.permissions.active').length > 0) {
			$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').addClass('active');
			$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').find('input').prop('checked', true);
		}

		if($(this).hasClass('workspace') && $(this).hasClass('permit_read')) {
			if($(this).parents('.wsp-list:first').find('.permissions.active.element').length > 0) {
				$(this).addClass('active');
				$(this).find('input').prop('checked', true);
			}
		}
		// $.check_area_icons(this);
	})

	$.check_area_icons = function(label) {
		var $label = $(label),
			$parent_ul = $label.parents('.nav.nav-list.tree:first'),
			$parent_li = $parent_ul.parents('.has-sub-cat:first'),
			$area_icons = $parent_li.find('.area-icons'),
			read_checked = $parent_ul.find('.permit_read.active').length,
			read_all = $parent_ul.find('.permit_read').length,
			edit_checked = $parent_ul.find('.permit_edit.active').length,
			edit_all = $parent_ul.find('.permit_edit').length,
			dele_checked = $parent_ul.find('.permit_delete.active').length,
			dele_all = $parent_ul.find('.permit_delete').length,
			copy_checked = $parent_ul.find('.permit_copy.active').length,
			copy_all = $parent_ul.find('.permit_copy').length,
			move_checked = $parent_ul.find('.permit_move.active').length,
			move_all = $parent_ul.find('.permit_move').length;

		if($parent_ul.find('.permit_read.active').length == $parent_ul.find('.permit_read').length) {
			$area_icons.find('.permit_read').addClass('active')
		}
		else {
			$area_icons.find('.permit_read').removeClass('active')
		}

		if($parent_ul.find('.permit_edit.active').length == $parent_ul.find('.permit_edit').length) {
			$area_icons.find('.permit_edit').addClass('active')
		}
		else {
			$area_icons.find('.permit_edit').removeClass('active')
		}

		if($parent_ul.find('.permit_delete.active').length == $parent_ul.find('.permit_delete').length) {
			$area_icons.find('.permit_delete').addClass('active')
		}
		else {
			$area_icons.find('.permit_delete').removeClass('active')
		}

		if($parent_ul.find('.permit_copy.active').length == $parent_ul.find('.permit_copy').length) {
			$area_icons.find('.permit_copy').addClass('active')
		}
		else {
			$area_icons.find('.permit_copy').removeClass('active')
		}

		if($parent_ul.find('.permit_move.active').length == $parent_ul.find('.permit_move').length) {
			$area_icons.find('.permit_move').addClass('active')
		}
		else {
			$area_icons.find('.permit_move').removeClass('active')
		}

		// console.log(read_all, read_checked)
	}

	$('body').delegate('.label-text', 'click', function(event) {
		event.preventDefault();
		var $t = $(this),
			$btn = $(this);

		$t.prev('.goto.btn-goto').trigger('click')
	})

	$('body').delegate('.goto.btn-goto:not(.block)', 'click', function(event) {
		event.preventDefault();
		var $btn = $(this),
			$chk_input = $btn.find('input[type=checkbox]'),
			share_action = 0;

		$(this).toggleClass('checked')

		if( $chk_input.length > 0 ) {
			$chk_input.prop('checked', !$chk_input.prop('checked'))
		}

		if($chk_input.prop('checked')) {

		}

	})

	function resizeStuff() {
		// $('.tree_links').ellipsis_word();
	}
	resizeStuff()
	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 800); //800 is time in miliseconds
	});

	$.fn.clickToggle = function(f1, f2) {
        var fn = [f1, f2];
        this.data('toggleclicked', 0);
        this.click(function(e) {
            e.preventDefault()
			var data = $(this).data();
            var tc = data.toggleclicked;
            $.proxy(fn[tc], this)();
            data.toggleclicked = (tc + 1) % 2;
        });
        return this;
    };



	$('body').delegate('.btn-label', 'click', function(event){
		event.preventDefault();
		var parent = $(this).parent();

		if( parent.is('.btn-label-group') ) {

			var child = parent.find('.btn-label'),
				child_count = child.length,
				active_child = parent.find('.btn-label.active');
				active_child_count = active_child.length;
			if( child_count > 1 ) {

				if( active_child_count <= 0 ) {
					$(this).addClass('active')
				}
				if( active_child_count == 1 ) {
					parent.find('.btn-label').each(function(){
						$(this).toggleClass('active')
					})
				}
				if( active_child_count > 1 ) {
					if( $(this).hasClass('active') ) {
						parent.find('.btn-label').not(this).addClass('active')
						$(this).removeClass('active');
					}
				}
			}
		}

		parent.find('input[type=radio]').each( function( i, j ){
			var $p = $(this).parent();
			$('.share_propagation_permission').addClass('block')
			$('#rst_share_tree').addClass('disabled')
			if($p.hasClass('active')) {
				$(this).prop('checked', true);
				if( i == 1 ) {
						$("#share_detail_section").slideFade(2000, 'easeOutBounce')
						$('.share_propagation_permission').removeClass('block')
						$('#rst_share_tree').removeClass('disabled')
						console.log('1111111111')
				}
				else if( i == 0 ) {
					$("#share_detail_section").slideFade(500)
					console.log('2222222')
				}
			}
		})
		$('.share_propagation_permission.block input[type=checkbox]').prop('checked', false)
	})

	$('body').delegate('#user_detail_section .panel-heading', 'click', function(event){
		event.preventDefault();
		$(this).parent().find('.currentpermission-sec').slideToggle(200)
		$(this).parent().toggleClass('panel-hidden');
	})

	$('body').delegate('#sbmt_share_tree', 'click', function(event){
		event.preventDefault();

		var $t = $(this),
			$form = $('#frm_share_tree'),
			$toggle_group = $(".toggle-group.propogation-on-off"),
			$firstBtn = $toggle_group.find(".btn-toggle:first"),
			$lastBtn = $toggle_group.find(".btn-toggle:last"),
			$propogation_switch = $('input[name="data[Share][share_permission]"]');


			$t.addClass('disabled');
			$t.css('pointer-events','none');

		if( $firstBtn.hasClass('toggle-on') ) {
			// full permission is ON

			$("<input>")
				.attr("type", "hidden")
				.attr("id", "project_level")
				.attr("name", "data[Share][project_level]")
				.val('1')
				.appendTo($form);

		}
		else if( $lastBtn.hasClass('toggle-on') ) {
			// specific permission is ON

			$("<input>")
				.attr("type", "hidden")
				.attr("id", "project_level")
				.attr("name", "data[Share][project_level]")
				.val('0')
				.appendTo($form);

		}
		// console.log($form.serializeArray())

		// All Done!!!
		// Submit Form
		$form.submit()
		// return true;
	})

	setTimeout(function(){

	// CHECK ALL SELECTED PERMISSIONS AND SET CHECKED TO ASSOCIATED CHECKBOX TO TRUE
		$('.permissions.active').each( function(i, j) {
			var $t = $(this),
				$checkbox = $t.find('input[type=checkbox]');
			if( $checkbox.length )
				$checkbox.prop('checked', true);
		})

		$('.dis_permits.active').each( function(i, j) {
			var $t = $(this),
				$checkbox = $t.find('input[type=checkbox]');
			if( $checkbox.length )
				$checkbox.prop('checked', true);
		})

	}, 600)

	// 1 = same level, 2 = down level
	// 1 = propagation on, 2 = propagation off
	if( $js_config.project_level == 1  ) {
		$( "input[name='data[ProjectPermission][project_level]'][value=1]" ).prop('checked', true)
		$( "input[name='data[Share][share_permission]']" ).parent().addClass('block');
		$('.toggle-handle').addClass('on').removeClass('off');
		$('[name="data[Share][share_permission]"]').prop('checked', true);
	}
	else if( parseInt($js_config.project_level) == 2  ) {

		$(".toggle-group.propogation-on-off").trigger("click")
		$('[name="data[Share][share_permission]"]').prop('checked', false);
		if( $js_config.share_permission == 1 ) {
			$('[name="data[Share][share_permission]"]').prop('checked', true);
			/*$(".toggle-handle.propogation-handle").trigger("click")

			if( $(".toggle-handle.propogation-handle").hasClass('off') ) {
				$(".toggle-handle.propogation-handle").removeClass('off').addClass('on')
			}*/
			$(".toggle-handle.propogation-handle").removeClass('off').addClass('on')

		}
	}


	$('body').delegate('.propogation-on-off', 'click', function(event){
		setTimeout(function(){
			if( !$('#share_detail_section').is(':visible') ) {
				$('.option-buttons label.goto').hide();
				$('.option-buttons span.label-text').hide();
				$('.toggle-handle').addClass('on').removeClass('off');
				$('[name="data[Share][share_permission]"]').prop('checked', true);
				$('.all-permit').show();
			}
			else {
				$('.option-buttons label.goto').css({'display': 'inline-block'});
				$('.option-buttons span.label-text').show();
				$('.all-permit').hide();
				/*$('.toggle-handle').addClass('off').removeClass('on');
				$('.propogat_permisions').hide(500);
				$("a.propogation").addClass('not-editable');
				$("a.propogation").removeClass('activated');*/
				/*if($('.toggle-handle').hasClass('on')) {
					$("a.propogation").removeClass('not-editable').addClass('activated');
					$('.propogat_permisions').show("slide", { direction: "left" }, 500);
				}*/
				// $('.propogat_permisions').slideFadeToggle(500, 'linear' );
			}
		},1000)
	})


	$.all_sharing_options = $('#all_sharing_options').multiselect({
	        buttonClass: 'btn btn-white aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 0,
	        maxHeight: '318',
	        checkboxName: 'select_opts',
	        includeSelectAllOption: true,
	        enableFiltering: false,
	        enableCaseInsensitiveFiltering: false,
	        enableUserIcon: false,
	        nonSelectedText: 'Group Permissions',
	        allSelectedText: false,
	        onChange: function(element, checked) {

	        	$.toggle_one_permission(element, checked);
	        	if($(element).val() == 'edit' && checked) {
	        		$('[value="copy"]').removeAttr('disabled');
	        		$('[value="move"]').removeAttr('disabled');
	        		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('disabled');
	        		$('.multiselect-container input[value="move"]').parents('li:first').removeClass('disabled');
	        	}
	        	else if($(element).val() == 'edit' && !checked) {
	        		$('[value="copy"]').attr('disabled','disabled').prop('checked', false);
	        		$('[value="move"]').attr('disabled','disabled').prop('checked', false);
	        		$('#all_sharing_options [value="copy"]').prop("selected", false)
	        		$('#all_sharing_options [value="move"]').prop("selected", false)
	        		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('active').addClass('disabled');
	        		$('.multiselect-container input[value="move"]').parents('li:first').addClass('disabled').removeClass('active');
	        	}
	        },
	        onSelectAll: function(checked) {
	            if(checked) {
		            $('[value="copy"]').removeAttr('disabled').prop('checked', true);
		    		$('[value="move"]').removeAttr('disabled').prop('checked', true);
		    		$('.multiselect-container input[value="copy"]').parents('li:first').removeClass('disabled').addClass('active');
	    			$('.multiselect-container input[value="move"]').parents('li:first').removeClass('disabled').addClass('active');
	    			$('#all_sharing_options option').removeAttr('disabled').prop("selected", true);
	    			$('#all_sharing_options').multiselect('selectAll', true).multiselect('updateButtonText');
		    	}
		    	else {
		            $('[value="copy"]').attr('disabled', 'disabled').prop('checked', false);
		    		$('[value="move"]').attr('disabled', 'disabled').prop('checked', false);
		    		$('#all_sharing_options option').prop("selected", false)
		    		$('.multiselect-container input[value="copy"]').parents('li:first').addClass('disabled').removeClass('active');
	    			$('.multiselect-container input[value="move"]').parents('li:first').addClass('disabled').removeClass('active');
		    	}

		        if(checked) {
		        	var selected = ['read', 'edit', 'delete', 'copy', 'move', 'add'];
			        $.update_all_permissions(selected);
			    }
			    else{
			    	$.remove_all_permissions();
			    }
	        }
	    });

	$.toggle_one_permission = function(option, checked) {
		// On/Off clicked permission.
		// $('#all_sharing_options').multiselect('select', ['read', 'copy'] );
		var $option = $(option),
			permit_text = $option.val();
		if(!checked) {
			$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
				if( !$(this).hasClass('unchangable') ) {
					var $input = $(this).find('input[type=checkbox]');
					if(permit_text == 'edit') {
						var $parent = $(this).parents('.sharing-icon:first'),
							$copy_icon = $parent.find('label.permissions.permit_copy'),
							$move_icon = $parent.find('label.permissions.permit_move');

						if( $copy_icon.hasClass('active') ) {
							$copy_icon.removeClass('active');
							$copy_icon.find('input').prop("checked", false);
						}
						if( $move_icon.hasClass('active') ) {
							$move_icon.removeClass('active');
							$move_icon.find('input').prop("checked", false);
						}
						$input.prop("checked", false);
						$(this).removeClass('active');
					}
					else{
						$input.prop("checked", false);
						$(this).removeClass('active');
					}
				}
			});
		}
		else{
			$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
				if( !$(this).hasClass('unchangable') ) {
					var $input = $(this).find('input[type=checkbox]');
					if(permit_text == 'copy') {
						var $parent = $(this).parents('.sharing-icon:first'),
							$edit_icon = $parent.find('label.permissions.permit_edit');

						if( $edit_icon.hasClass('active') ) {
							$(this).addClass('active');
							$(this).find('input').prop("checked", true);
						}
					}
					else if(permit_text == 'move') {
						var $parent = $(this).parents('.sharing-icon:first'),
							$edit_icon = $parent.find('label.permissions.permit_edit');

						if( $edit_icon.hasClass('active') ) {
							$(this).addClass('active');
							$(this).find('input').prop("checked", true);
						}
					}
					else{
						$input.prop("checked", true);
						$(this).addClass('active');
					}
				}
			});
		}

	}

	$.remove_all_permissions = function() {
		// Remove all selected permission.
		$('label.permissions').each(function(index, el) {
			if( !$(this).hasClass('unchangable') ) {
				var $input = $(this).find('input[type=checkbox]');

				$input.prop("checked", false);
				$(this).removeClass('active');
			}
		});
	}

	$.update_all_permissions = function(selected) {
		if(selected.length <= 0) return;
		// return;
		for(var i = 0; i < selected.length; i++) {
			// console.log(selected[i])
			var permit_text = selected[i];
			$('label.permissions.permit_'+permit_text).not('.disabled').each(function(index, el) {
				if( !$(this).hasClass('unchangable') ) {
					var $input = $(this).find('input[type=checkbox]'),
						iName = $input.attr('name');
					if(permit_text == 'copy') {
						var $edit_icon = $('.sharing-icon').find('.permit_edit')
						if( $edit_icon.hasClass('active') ){
							$input.prop("checked", true);
							$(this).addClass('active');
						}
					}
					else {
						$input.prop("checked", true);
						$(this).addClass('active');
					}
				}
			});
		}
	}


})
$(window).on('load', function(event){
	setTimeout(function(){
		$('input[type=checkbox][name=sharing_level]').prop('checked', true)
		$('.tipText.tree_text').tooltip({ container: 'body', placement: 'top'})

		$('#sharing_list li.has-sub-cat:first .sharing-icon:first .permissions.permit_read').addClass('unchangable active');
		$('#sharing_list li.has-sub-cat:first .sharing-icon:first .permissions.permit_read.unchangable').find('input[type=checkbox]').prop('checked', true);

	}, 600)
})

</script>
<style type="text/css">

	.multiselect.dropdown-toggle.btn.btn-white {
	    background-color: #fff !important;
	    color: #444 !important;
	    border-color: #ddd !important;
	}
	.multiselect.dropdown-toggle.btn .multiselect-selected-text {
	    font-size: 12px !important;
	}

	.currentpermission-sec{
		display: block;
		width: 100%;
	}
	.currentpermission-bg{
		background: #eee;
		display: flex;
		flex-wrap: wrap;
	}
	.currentpermission-bg .currentpermissio-col{
		font-weight: bold;
	}
	.currentpermissio-col{
		border-right: 1px solid #f4f4f4;
		padding: 8px;
	}
	.currentpermissio-col:last-child{
		border-right:none;
	}
	.currentpermissionone{
		width: 28%;
	}
	.currentpermissiontwo{
		width: 30%;
	}
	.currentpermissionthree{
		width: 10%;
	}
	.currentpermissionfour{
		width: 17%;
	}
	.currentpermissionfive{
		width: 15%;
	}
	.currentpermission-cont{
		display: inline-block;
		width: 100%;
	}
	.currentpermission-cont-row {
		display: flex;
		flex-wrap: wrap;
	}

	.currentpermissionthree , .currentpermissionfour, .currentpermissionfive{
		text-align: center;
	}

	/*.nav.nav-list.tree a.tree_links {
	    cursor: pointer;
	    display: inline-block;
	    font-size: 13px;
	    line-height: 24px;
	    margin: 0;
	    padding: 5px 5px 5px 6px;
	    color: #333333;
	    width: 100px;
	    margin-right: 20px;
	    overflow: hidden;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
	.sharing_list li a.tree-toggler.tree_links .tree_text {
	     width: 85%;
	    height: 25px;
	    max-height: 25px;
	    vertical-align: top;
	    line-height: 20px;
	}*/
</style>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<p class="text-muted date-time">
						<span><?php echo $page_heading; ?></span>
					</p>
				</h1>

			</section>
		</div>

	<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
                        </div>

		<div class="box-body clearfix list-shares" style="min-height: 800px">


				<?php if( isset($shareUser) && !empty($shareUser)) {

					$user_detail = $this->ViewModel->get_user($shareUser, ['hasOne' => array('UserInstitution'), 'hasMany' => array('UserProject', 'UserPlan', 'UserTransctionDetail')], 1);

				?>
					<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'shares', 'action' => 'update_sharing', $project_id, $shareUser, $share_action, $ppermit_update_id ), 'class' => 'formAddElementNote', 'id' => 'frm_share_tree', 'enctype' => 'multipart/form-data')); ?>

						<?php if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
						echo $this->Form->input('ShareRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>

 							<?php $current_level = 'Current Permission Level: ';
 							if(isset($exist_permissions) && !empty($exist_permissions)) {
 								if(isset($exist_permissions['pp_data']['ProjectPermission']) && !empty($exist_permissions['pp_data']['ProjectPermission'])) {
 									$current_level .= ($exist_permissions['pp_data']['ProjectPermission']['project_level'] > 0) ? 'Owner' : 'Sharer';
 								}
 							}
 							else{
 								$current_level .= 'No Sharing';
 							} ?>

                            <div class="panel panel-default" id="user_detail_section">
								<div class="panel-heading clearfix" id="" >
									<h5 class="pull-left"> <?php echo $current_level; ?> </h5>

								</div>
								<span class="collapse-indicator">
									<i class="fa"></i>
								</span>
								<div class="currentpermission-sec ">
									<div class="currentpermission-bg">
											<div class="currentpermissionone currentpermissio-col">Team Member</div>
                                            <div class="currentpermissiontwo currentpermissio-col">Organization</div>
                                            <div class="currentpermissionthree currentpermissio-col">Active/Inactive</div>
                                            <div class="currentpermissionfour currentpermissio-col">Role Level</div>
                                            <div class="currentpermissionfive currentpermissio-col">Propagation</div>
									</div>
									<div class="currentpermission-cont">
										<div class="currentpermission-cont-row">
										<div class="currentpermissionone currentpermissio-col"><?php echo $user_detail['UserDetail']['first_name'].' '. $user_detail['UserDetail']['last_name']; ?></div>
                                            <div class="currentpermissiontwo currentpermissio-col"><?php echo  !empty($user_detail['UserDetail']['org_name']) ? $user_detail['UserDetail']['org_name'] : "N/A"; ?></div>
                                            <div class="currentpermissionthree currentpermissio-col"><label class="goto btn-goto block" title="">
													<?php echo ($user_detail['User']['status']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?>
												</label></div>
                                            <div class="currentpermissionfour currentpermissio-col"><div class="btn-group toggle-group propogation-on-off" <?php if( isset($swayam_ki_permissions) && !$swayam_ki_permissions ) { ?> style="display: none !important;" <?php } ?> >

													<a href=""  title="All Permissions Granted" data-container="body" class="tipText btn btn-xs btn-toggle toggle-on">Owner</a>

													<a href="" title="Set Sharing Permissions" data-container="body" class="tipText btn btn-xs btn-toggle toggle-off">Sharer</a>

												</div>

												<?php
												if( isset($swayam_ki_permissions) && !$swayam_ki_permissions ) { ?>
													<label class="goto btn-goto block unchange" title="">
														<i class="fa fa-arrow-down"></i>
													</label> Specific
												<?php }  ?></div>
                                            <div class="currentpermissionfive currentpermissio-col"><div data-toggle="toggle" class="toggle" style="">
														<input type="checkbox" name="data[Share][share_permission]" value="1">
														<div class="toggle-handle propogation-handle off option-disabled">
															<label class="btn btn-success btn-xs tipText" title="Further sharing is allowed">On</label>
															<label class="btn btn-danger btn-xs tipText" title="Further sharing not allowed">Off</label>
														</div>
													</div></div>
									</div>
									</div>

								</div>
                            </div>


						<div class="panel panel-default" id="share_section_submit">
							<div class="panel-heading clearfix" style="padding: 10px 10px;">
								<h5 class="">Project Sharing</h5>

								<span class="pull-right option-buttons">
<!--
									<label class="goto btn-goto tipText" title="Update & Goto Propagation">
										<input type="checkbox" name="goto_propogation" value="<?php echo '0'; ?>" id="goto_propogation"/>
										<i class="fa fa-check"></i>
									</label>
									<span class="label-text">Update & Goto Propagation</span>
 -->
<?php
$project_detail = getByDbId('Project', $project_id);

$prjWrks = $this->ViewModel->project_workspaces($project_id); ?>

										<a href="" class="btn btn-warning btn-sm <?php echo (isset($exist_project_level) && $exist_project_level == 1) ? 'disabled' : ''; ?>" id="rst_share_tree">Toggle Tree</a>
										<a href="#" class="btn btn-success btn-sm " id="sbmt_share_tree" >Save</a>
										<a href="<?php echo Router::url(['controller' => 'shares', 'action' => 'sharing_map', $project_id]); ?>" class="btn btn-danger btn-sm " id="caneel_share" >Cancel</a>
								</span>

							</div>
						</div>

						<div class="panel panel-default" id="share_detail_section" style="<?php echo (isset($exist_project_level) && $exist_project_level == 1) ? 'display: none;' : ''; ?>">

							<div class="panel-body no-padding" id="">
								<div class="table-responsive" style="min-height: 300px">
									<table class="table">
										<tr>
											<th>
												<div class="shares-list header">
													<ul>
														<li class="shares-tree-heading">
															<label>Path</label>
															<div>Permissions</div>
														</li>
														<li class="tree-options">
															<div class="col_exp">
																<div class="btn-group action-group" style="margin-left: 5px;">
																	<a class="btn btn-sm action_buttons btn-primary" data-action="collapse_all" href="#" >Collapse All</a>
																	<a class="btn btn-sm action_buttons btn-primary" data-action="expand_all" href="#" >Expand All</a>
																</div>
															</div>
															<div class="sel-opt pull-right" style="display: inline-block; min-width: 200px;">
																<!-- <label>Group Permissions: </label> -->
																<select class="form-control" id="all_sharing_options" multiple="">
																	<option value="read">Read</option>
																	<option value="edit">Update</option>
																	<option value="delete">Delete</option>
																	<option value="copy" disabled="">Copy</option>
																	<option value="move" disabled="">Cut/Move</option>
																	<option value="add">Add Task</option>
																</select>
														    </div>
														</li>
													</ul>
												</div>
											</th>
										</tr>

										<tr>
											<td style="padding-left: 0px;">

												<div id="sharing_list" class="sharing_list" >
												<?php
												if( isset($project_id) && !empty($project_id) ) {

													echo $this->Form->input('Share.project_id', ['type' => 'hidden', 'value' => $project_id]);
													echo $this->Form->input('Share.user_id', ['type' => 'hidden', 'value' => $shareUser]);
													echo $this->Form->input('Share.share_action', ['type' => 'hidden', 'value' => $share_action]);


$area_icons = '<label class="ap-permissions permit_read btn-circle btn-xs tipText" title="Read" data-class=".permit_read">' .
			        '<input type="checkbox" name="area_permit_read" value="" >' .
			        '<i class="fa fa-eye lbl-icn"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_edit btn-circle btn-xs tipText" title="" data-original-title="Update" data-class=".permit_edit">' .
			        '<input type="checkbox" name="area_permit_edit" value="">' .
			        '<i class="fa fa-pencil"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_delete btn-circle btn-xs tipText" title="" data-original-title="Delete" data-class=".permit_delete">' .
			        '<input type="checkbox" name="area_permit_delete" value="">' .
			        '<i class="fa fa-trash"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_copy btn-circle btn-xs tipText not-allowed" title="" data-original-title="Copy" data-class=".permit_copy">' .
			        '<input type="checkbox" name="area_permit_copy" value="">' .
			        '<i class="fa fa-copy"></i>' .
			    '</label>' .
			    '<label class="ap-permissions permit_move btn-circle btn-xs tipText not-allowed" title="Cut &amp; Move" data-class=".permit_move">' .
			        '<input type="checkbox" name="area_permit_move" value="">' .
			        '<i class="fa fa-cut"></i>' .
			    '</label>' .
			    '<label class="set_unset_all">Set All</label>' ;
 ?>
<ul class="nav nav-list tree" >
<?php

	$share_file = 'share_icons_edit';

	$display = '';
	$icon_class = 'tree_icons opened fa fa-minus';
	if( $collapse ) {
		$display = ' style="display: none;" ';
		$icon_class = 'tree_icons closed fa fa-plus';
	}
		$prj_status_text = '';
		$prj_status = $this->Permission->project_status($project_id);
		if(isset($prj_status) && !empty($prj_status)) {
			$prj_status = $prj_status[0][0]['prj_status'];
			if($prj_status == 'not_spacified'){
				$prj_status_text = ': Not Set';
			}
			else if($prj_status == 'progress'){
				$prj_status_text = ': In Progress';
			}
			else if($prj_status == 'overdue'){
				$prj_status_text = ': Overdue';
			}
			else if($prj_status == 'completed'){
				$prj_status_text = ': Completed';
			}
			else if($prj_status == 'not_started'){
				$prj_status_text = ': Not Started';
			}
		}

	$prjData = $prjWrks[$project_id]['project'];
	$wrkData = $prjWrks[$project_id]['workspace'];

	echo  '<li class="has-sub-cat prj-list">';
		echo  '<a ' .
				' data-id="' . $prjData['id'] . '"' .
				' class="tipText tree-toggler nav-header tree_links">' .
					'<i class="'.$icon_class.'"></i> ' .
					'<span class="tipText tree_text" title="Project Status'.$prj_status_text.'" style="">'.strip_tags($project_detail['Project']['title']).'</span>' .
			'</a>' ;

		// show project icons
		echo $this->element('../Shares/partials/'.$share_file, ['type' => 'project', 'model' => 'ProjectPermission']);

	// start ws list
	echo '<ul class="nav nav-list tree" '.$display.' >';

if( isset($prjWrks) && !empty($prjWrks) ) {
	foreach($wrkData as $key => $val) {
		// pr($val);
		$ws_sign_off = false;
		$adis = '';
		if(isset($val['sign_off']) && !empty($val['sign_off'])) {
			$ws_sign_off = true;
			$adis = 'disabled';
		}

		$ws_status_text = '';
		$ws_status = $this->Permission->wsp_status($val['id']);
		if(isset($ws_status) && !empty($ws_status)) {
			$ws_status = $ws_status[0][0]['ws_status'];
			if($ws_status == 'not_spacified'){
				$ws_status_text = ': Not Set';
			}
			else if($ws_status == 'progress'){
				$ws_status_text = ': In Progress';
			}
			else if($ws_status == 'overdue'){
				$ws_status_text = ': Overdue';
			}
			else if($ws_status == 'completed'){
				$ws_status_text = ': Completed';
			}
			else if($ws_status == 'not_started'){
				$ws_status_text = ': Not Started';
			}
		}
		// create ws and el if area exists
		if( isset($val['area']) && !empty($val['area']) ) {

			$areas = array_keys($val['area']);

			echo  '<li class="has-sub-cat wsp-list">';
				echo  '<a ' .
						' data-id="' . $val['id'] . '"' .
						' class="tree-toggler nav-header tree_links">'.
						'<i class="'.$icon_class.'"></i>' .
						'<span class="tipText tree_text" title="Workspace Status'.$ws_status_text.'" style="">'.strip_tags($val['title']).'</span>' .
					'</a>' ;

					echo $this->element('../Shares/partials/'.$share_file, ['type' => 'workspace', 'model' => 'WorkspacePermission', 'workspace_id' => $val['id'], 'ws_sign_off' => $ws_sign_off] );

					$wsEls = $this->ViewModel->area_elements($areas);

					if( isset($wsEls) && !empty($wsEls) ) {

						// if elements exists, print area
						echo '<ul class="nav nav-list tree" '.$display.' >';

						foreach($val['area'] as $akey => $aval) {

							// get each area elements
							$arEls = $this->ViewModel->area_elements($akey);

							if( isset($arEls) && !empty($arEls) ) {

								echo  '<li class="has-sub-cat area-list">';
									echo  '<a ' .
											' data-id="' . $akey . '"' .
											' class="tree-toggler nav-header tree_links">'.
											'<i class="'.$icon_class.'"></i>' .
											'<span class="tipText tree_text area-title" title="Area" style="">'.$aval.'</span>' .
										'</a>' ;
									if(!$ws_sign_off){
										echo '<div class="sharing-icon area-icons '.$adis.'">';
										echo($area_icons);
										echo '</div>';
									}
									// start el list
									echo '<ul class="nav nav-list tree" '.$display.' >';
								// pr($arEls, 1);
								foreach($arEls as $ekey => $evals) {
									$eval = $evals['Element'];

									$task_sign_off = false;
									if(isset($eval['sign_off']) && !empty($eval['sign_off'])) {
										$task_sign_off = true;
									}

									$ele_status_text = '';
									$ele_status = $this->Permission->task_status($eval['id']);
									if(isset($ele_status) && !empty($ele_status)) {
										$ele_status = $ele_status[0][0]['ele_status'];
										if($ele_status == 'not_spacified'){
											$ele_status_text = ': Not Set';
										}
										else if($ele_status == 'progress'){
											$ele_status_text = ': In Progress';
										}
										else if($ele_status == 'overdue'){
											$ele_status_text = ': Overdue';
										}
										else if($ele_status == 'completed'){
											$ele_status_text = ': Completed';
										}
										else if($ele_status == 'not_started'){
											$ele_status_text = ': Not Started';
										}
									}

									echo  '<li class="has-sub-cat elm-list">';
									echo  '<a ' .
											' data-id="' . $eval['id'] . '"' .
											' title=""' .
											' class="tree-toggler nav-header tree_links">'.
											'<span class="ico_element pull-left"></span>'.
											'<span class="tipText tree_text" title="Task Status'.$ele_status_text.'" >'.strip_tags($eval['title']).'</span>'.
										'</a>' ;

										echo $this->element('../Shares/partials/'.$share_file, ['type' => 'element', 'model' => 'ElementPermission', 'workspace_id' => $val['id'], 'area_id' => $akey, 'element_id' => $eval['id'], 'ws_sign_off' => $ws_sign_off, 'task_sign_off' => $task_sign_off]);

									echo  '</li>';// end el
								}
									echo '</ul>';// end el list
							}
							echo  '</li>';// end ar
						}

						echo '</ul>';// end ar list

					}

			echo  '</li>';// end ws
		}
	}
}
		echo '</ul>';
		// end ws list

	echo '</li>';// end pr


													// pr($exist_permissions, 1);
													// echo sharing_tree($project_id, false, $collapse, $exist_permissions);
													// die;
												}
												?>
</ul>
											<!-- -->
												</div>
											</td>
										</tr>
									</table>
                                </div>
							</div>


                  </div>
                  <div class="all-permit"  style="<?php echo (isset($exist_project_level) && $exist_project_level == 2) ? 'display: none;' : ''; ?>">Owner: All Permissions Granted</div>


					<?php echo $this->Form->end(); ?>
				<?php } ?>

                </div>
            </div>
        </div>
	</div>
</div>

<style>
.goto.block.unchange {
    border-radius: 0 4px 4px 0;
    padding: 1px 3px 0 4px;
	pointer-events: none;
}
#user_detail_section .panel-heading {
	cursor: pointer;
	padding: 10px 15px 10px 5px;
}
</style>
<script type="text/javascript">
	$(function(){


	/*
	--------------------------------------------------------------------------
	PROPOGATION ON/OFF INPUT+ICON EVENTS
	--------------------------------------------------------------------------
	*/
	$('body').delegate('.propogation-handle', 'click', function(event){
		event.preventDefault();

		var $this = $(this);

		if($(this).hasClass('option-disabled')){
			return;
		}
		var $chk = $(this).parent().find('input[type=checkbox]')
		$chk.prop('checked', false)

		if($(this).hasClass('off')){
			BootstrapDialog.show({
                title: 'Propagation',
                type: BootstrapDialog.TYPE_DANGER,
                message: 'Are you sure you want to allow propagation?<br /><br />This Team Member will be allowed to share onwards.',
                draggable: true,
                buttons: [{
                    label: 'Allow',
                    // icon: 'fa fa-times',
                    cssClass: 'btn-success',
                    action: function(dialogRef) {
                        $this.addClass('on').removeClass('off');
                        $chk.prop('checked', !$chk.prop('checked'))
                        $("a.propogation").removeClass('activated');
						$('.propogat_permisions').slideFadeToggle(500, 'linear' );
                        dialogRef.close();
                    }
                },{
                    label: 'Cancel',
                    // icon: 'fa fa-times',
                    cssClass: 'btn-danger',
                    action: function(dialogRef) {
                        dialogRef.close();
                    }
                }]
            });
		}
		else {
			$this.addClass('off').removeClass('on');
			$("a.propogation").addClass('activated');
			$('.propogat_permisions').slideFadeToggle(500, 'linear' );
		}


	})


		$.fn.toggleText = function(text1, text2) {
			($(this).text() === text1) ? $(this).text(text2) : $(this).text(text1);
			return this;
		}

		$('body').delegate('.set_unset_all', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent_ul = $(this).parents('.area-icons:first'),
				$parent_li = $parent_ul.parents('li.has-sub-cat:first'),
				$child_ul = $parent_li.find('ul.tree'),
				$area_labels = $parent_ul.find('label'),
				$area_input = $area_labels.find('input');
				$all_el_labels = $child_ul.find('label.permissions').not('.disabled'),
				$input = $all_el_labels.find('input');

			$input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			$area_input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			$(this).toggleText('Set All', 'Unselect All');
			$(this).toggleClass('active');

			if($this.hasClass('active')){
				$parent_ul.find('.permit_copy,.permit_move').removeClass('not-allowed');
			}
			else{
				$parent_ul.find('.permit_copy,.permit_move').addClass('not-allowed');
			}


		})


		$('body').delegate('.area-icons label.ap-permissions', 'click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$parent_ul = $(this).parents('.area-icons:first'),
				$parent_li = $parent_ul.parents('li.has-sub-cat:first'),
				$child_ul = $parent_li.find('ul.tree'),
				set_class = $(this).data('class'),
				$this_input = $(this).find('input');

			$this_input.prop("checked", !$this_input.prop("checked"));

			var $all_el_labels = $child_ul.find(set_class).not('.disabled'),
				$input = $all_el_labels.find('input');

			$input.each(function(){
				$(this).prop("checked", ($this.hasClass('active')?false:true));

				if($(this).prop("checked")) {
					$(this).parent('label:first').addClass('active');
				}
				else {
					$(this).parent('label:first').removeClass('active');
				}
			})

			if($(this).hasClass('active')){
				$(this).removeClass('active');
			}
			else {
				$(this).addClass('active');
			}

			if( $(this).hasClass('permit_edit') ) {
				if( !$(this).hasClass( 'active' ) ) {
					$parent_li.find('.permit_copy,.permit_move').addClass('not-allowed').removeClass('active');
				}
				else {
					$parent_li.find('.permit_copy,.permit_move').removeClass('not-allowed');
				}
			}

			var active_length = ($parent_li.find('label.active').length) ? $parent_li.find('label.active').length : 0;
			if( active_length > 0 && !$(this).hasClass('permit_read') && !$parent_li.find('.permit_read').hasClass('active') ) {

				$parent_li.find('.permit_read').not('.disabled').addClass('active');
				$parent_li.find('.permit_read').not('.disabled').find('input[type=checkbox]').prop('checked', true);

			}
			// if only one permission is given but its not the read permission
			else if( $(this).is($('.permit_read')) && active_length > 0 ) {

				$parent_li.find('.permit_read').not('.disabled').addClass('active');
				$parent_li.find('.permit_read').not('.disabled').find('input[type=checkbox]').prop('checked', true);

			}

			if($(this).parents('.area-list:first').find('.ap-permissions.active').length > 0) {
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').not('.disabled').addClass('active');
				$(this).parents('.wsp-list:first').find('.sharing-icon:first').find('.permissions.permit_read').not('.disabled').find('input').prop('checked', true);
			}

		})

	})
</script>