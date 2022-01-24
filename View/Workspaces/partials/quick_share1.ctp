<?php
echo $this->Html->css('projects/bs.checkbox');
?>
<style>
.perm_users {
    max-height: 253px;
    min-height: 253px;
	overflow-x: hidden;
	overflow-y: auto;
	margin-bottom: 0;
}
.perm_users .filters {
	margin-bottom: 5px;
}
.perm_users_wrapper {
	border-radius: 3px;
	padding: 3px;
	border: 1px solid #00c0ef;
}
.list-group.filter {
	margin-bottom: 3px;
}
.list-group-item.users {
	cursor: pointer;
}
.list-group-item.filters {
	padding: 0;
	border: medium none;
}
.list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
	border-color: #ffffff;
}
.list-group-item.users > input {
	display: none;
}
.users .user-image {
	margin-right: 5px;
}
.user-pophover p {
	margin-bottom: 2px ;
}
.user-pophover p:first-child {
	font-weight: 600 !important;
	margin-bottom: 2px !important;
	width: 170px !important;
	font-size: 14px;
}
.user-pophover p:nth-child(2) {
	font-size: 11px;
	margin-bottom: 4px !important;
}
.project-name {
	border-bottom: 1px solid #cccccc;
	font-size: 14px;
	font-weight: 600;
	margin-bottom: 10px;
	padding: 0 0 5px;
}

.sharing-icon {
	display: inline-block;
}


.clt {
	margin: 10px 0;
}
.clt, .clt ul, .clt li {
	position: relative;
}

.clt ul {
    list-style: none;
    padding-left: 32px;
	color: #4c4c4c;
}
.clt li.child::before, .clt li.child::after {
    content: "";
    position: absolute;
    left: 2px;
}
.clt li.child::before {
    border-top: 1px solid #00C0EF;
    top: 16px;
    width: 10px;
    height: 0;
}
.clt li.child::after {
    border-left: 1px solid #00C0EF;
    height: 100%;
    width: 0px;
    top: 9px;
}
.clt ul > li.child:last-child::after {
    height: 8px;
}
.clt input[type="checkbox"] {
    display: none;
}

.users-select, .area-elements {
	border: 1px solid #00c0ef;
	border-radius: 3px;
	min-height: 300px;
	max-height: 300px;
	overflow-x: hidden;
	overflow-y: auto;
}
.containers {
	width: 100%;
	float: left;
	margin-bottom: 20px;
}
.col-half {
	width: 49%;
	float: left;
	margin: 2px 0 5px 2px;
}
.tree_icons {
	border: 1px solid #00c0ef;
	color: #333333;
	font-size: 10px;
	padding: 2px 2px 0;
}
.child {
	padding: 5px 0 0;
}
.tree-toggle, .tree-toggle:hover {
	color: #333333;
	cursor: pointer;
}
.el-title {
	color: #333333;
	cursor: pointer;
}
.children {
	margin-bottom: 10px;
}
.all-elements, .ico-element {
	cursor: default !important;
}
</style>
<?php $current_user_id = $this->Session->read('Auth.User.id'); ?>
<?php if( isset($project_id) && !empty($project_id) ) {


	$p_permission = $this->Common->project_permission_details($project_id, $current_user_id);

	$user_project = $this->Common->userproject($project_id, $current_user_id);

?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Share Workspace</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body clearfix">
	<?php $workspace_detail = getByDbId('Workspace', $workspace_id, ['title']); ?>
		<div class="project-name">
			<span class="fa fa-th" title="" data-original-title="Elements"></span> <?php echo strip_tags($workspace_detail['Workspace']['title']); ?>
		</div>

		<?php
			echo $this->Form->create('Project', array('url' => array('controller' => 'workspaces', 'action' => 'save_quick_share', $project_id), 'class' => 'form-bordered', 'id' => 'modelFormAddSharing', 'data-async' => ""));
			echo $this->Form->input('Share.share_by_id', [ 'type' => 'hidden',  'value' => $current_user_id ] );
			echo $this->Form->input('Share.project_id', [ 'type' => 'hidden',  'value' => $project_id ] );
			echo $this->Form->input('Share.workspace_id', [ 'type' => 'hidden',  'value' => $workspace_id ] );
		?>

		<div class="form-group">
			<?php if( isset($perm_users) && !empty($perm_users) ) { ?>
				<div class="perm_users_wrapper">
					<ul class="list-group filter">
						<li class="list-group-item filters" value="0">
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
								<input class="form-control filter-search" placeholder="Search" type="text">
								<span class="input-group-btn">
									<button class="btn btn-default clear-filter" style=" " type="button"><i class="glyphicon glyphicon-remove-circle"></i></button>
								</span>
							</div>
						</li>
					</ul>
					<ul class="list-group perm_users">
						<?php foreach($perm_users as $key => $value ) {
							$userDetail = $value['UserDetail'];
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id']);
							}
							if(isset($userDetail) && !empty($userDetail)) {
								$user_name = $userDetail['first_name'] . ' ' . $userDetail['last_name'];
								$profile_pic = $userDetail['profile_pic'];
								$job_title = $userDetail['job_title'];

								if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
									$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}
							}
							?>
							<li class="list-group-item users" data-value="">
								<img  src="<?php echo $user_image; ?>" class="user-image pophover" align="left" width="20" height="20" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
								<?php echo $user_name; ?>
								<input type="checkbox" value="<?php echo $userDetail['user_id']; ?>" class="user-check" name="data[Share][user_id]">
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>

		<div class="containers">
			<div class="col-half">
				<label class=" " for="">Task Selection:</label>
			</div>
			<div class="col-half">
				<label class=" " for="">Share With:</label>
			</div>
			<div class="form-group col-half area-elements">

				<div class="clt">
					<ul>
					<?php $areas = get_workspace_areas($workspace_id);
					if( isset($areas) && !empty($areas) ) {
						foreach($areas as $akey => $aval) {
							 $elements = $this->ViewModel->area_elements($akey);
					?>
						<li class="parent">
							<?php if( isset($elements) && !empty($elements) ) { ?>
							<i class="tree_icons fa fa-minus opened"></i>
							<?php } ?>
							<a class="tree-toggle" >
								<i class="fa fa fa-list-alt " style="margin-left: 10px;"></i>
								<?php echo strip_tags($aval); ?>
							</a>
							<?php if( isset($elements) && !empty($elements) ) { ?>
								<i class="fa fa-times text-red all-elements" style="margin-left: 10px;"></i>
							<?php } ?>
							<?php
								if( isset($elements) && !empty($elements) ) {

							?>
							<ul class="children">
								<?php foreach($elements as $ekey => $eval) {
								$el = $eval['Element'];
								?>
								<li class="child">
									<i class="icon_element_add_black" style="margin-left: 20px; min-height: 17px;"></i>
									<span class="el-title">
										<?php echo strip_tags($el['title']); ?>
									</span>
									<i class="fa fa-times text-red ico-element" style="margin-left: 10px; cursor: default !important;"></i>
									<input type="checkbox" name="data[ElementPermission][element][]" value="<?php echo $el['id']; ?>"     />
								</li>
								<?php } ?>
							</ul>
								<?php } ?>
						</li>
					<?php }
					} ?>
					</ul>
				</div>
			</div>
			<div class="form-group col-half users-select">
				<?php if( isset($perm_users) && !empty($perm_users) ) { ?>
				<div class="perm_users_wrapper">
					<ul class="list-group filter">
						<li class="list-group-item filters" value="0">
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
								<input class="form-control filter-search" placeholder="Search" type="text">
								<span class="input-group-btn">
								<button class="btn btn-default clear-filter" style=" " type="button"><i class="glyphicon glyphicon-remove-circle"></i></button>
								</span>
							</div>
						</li>
					</ul>
					<ul class="list-group perm_users">
						<?php foreach($perm_users as $key => $value ) {
							$userDetail = $value['UserDetail'];
							$user_image = SITEURL . 'images/placeholders/user/user_1.png';
							$user_name = 'Not Available';
							$job_title = 'Not Available';
							$html = '';
							if( $userDetail['user_id'] != $current_user_id ) {
								$html = CHATHTML($userDetail['user_id']);
							}
							if(isset($userDetail) && !empty($userDetail)) {
								$user_name = $userDetail['first_name'] . ' ' . $userDetail['last_name'];
								$profile_pic = $userDetail['profile_pic'];
								$job_title = $userDetail['job_title'];

								if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
									$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
								}
							}
						?>
						<li class="list-group-item users" data-value="">
							<img  src="<?php echo $user_image; ?>" class="user-image pophover" align="left" width="20" height="20" data-content="<div class='user-pophover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
							<?php echo $user_name; ?>
							<input type="checkbox" value="<?php echo $userDetail['user_id']; ?>" class="user-check" name="data[Share][user_id]">
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</div>
		</div>

		<div class="form-group" style="min-height: 30px;">
			<label class=" " for="description">Permission level: </label>
			<div class="bs-checkbox">
				<label>
					<input type="checkbox" value="1" id="wsp_selection" checked  name="data[Share][select]">
					<span class="cr" style="border-radius: 50%;"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					Selection in Workspace
				</label>
			</div>
			<div class="bs-checkbox">
				<label>
					<input type="checkbox" value="2" id="wsp_all"  name="data[Share][select]">
					<span class="cr" style="border-radius: 50%;"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					Workspace
				</label>
			</div>
		</div>
		<div class="form-group" style="min-height: 30px;">
			<label class=" " for="description">Permissions:</label>
			<div class="sharing-icon">Select a user</div>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="submit" class="btn btn-primary pull-left disabled" id="advance">Advance</button>

		<button type="submit"  class="btn btn-success submit_sharing">Submit</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>

<script type="text/javascript" >
$(function() {

	$.sharing_icons = function( option ) {
		var opt = option || {};
		$('.sharing-icon').html('<i class="fa fa-spinner fa-pulse" style="font-size: 23px;"></i>');
		$.ajax({
			url: $js_config.base_url + 'workspaces/select_permissions',
			type:'POST',
			data: $.param(opt),
			success: function( response, status, jxhr ) {
				setTimeout( function(){
					$('.sharing-icon').html(response);
				}, 200 )
			}
		});
		$('.list-group-item.users.active').removeClass('active')
	}

	$('#wsp_selection').on('change', function(event){
		event.preventDefault();
		if( $(this).prop('checked') ) {
			$('#wsp_all').prop('checked', false);
			$.sharing_icons({'type': 'select'});
			$('.tree-toggle').removeClass('showing');
		}
		else {
			$('#wsp_all').prop('checked', true);
			$('.clt').find('.all-elements').removeClass('fa-times text-red').addClass('fa-check text-green');
			$('.clt').find('.ico-element').removeClass('fa-times text-red').addClass('fa-check text-green');
			$('.tree-toggle').addClass('showing');
			$.sharing_icons({'type': 'all'});
		}
	})

	$('#wsp_all').on('change', function(event){
		event.preventDefault();
		if( $(this).prop('checked') ) {
			$('#wsp_selection').prop('checked', false);
			$('.clt').find('.all-elements').removeClass('fa-times text-red').addClass('fa-check text-green');
			$('.clt').find('.ico-element').removeClass('fa-times text-red').addClass('fa-check text-green');
			$('.tree-toggle').addClass('showing');
			$.sharing_icons({'type': 'all'});
		}
		else {
			$('#wsp_selection').prop('checked', true);
			$.sharing_icons({'type': 'select'});
			$('.tree-toggle').removeClass('showing');
		}
	})

	$('.tree-toggle').on( 'click', function (event) {

		event.preventDefault();

		if( !$('#wsp_selection').prop('checked') ) {
			$('#wsp_selection').prop('checked', true);
			$('#wsp_all').prop('checked', false);
			$.sharing_icons({'type': 'select'});
		}

		if( $(this).hasClass('showing') ) {
			$(this).parents('li.parent:first').find('.all-elements').removeClass('fa-check text-green').addClass('fa-times text-red');
			$(this).removeClass('showing');
			$(this).parents('li.parent:first').find('.children').find('.ico-element').removeClass('fa-check text-green').addClass('fa-times text-red')//.fadeOut(200);
			$(this).parents('li.parent:first').find('.children').find('input[type=checkbox]').prop('checked', false);
		}
		else {
			$(this).parents('li.parent:first').find('.all-elements').removeClass('fa-times text-red').addClass('fa-check text-green');
			$(this).addClass('showing');
			$(this).parents('li.parent:first').find('.children').find('.ico-element').removeClass('fa-times text-red').addClass('fa-check text-green');
			$(this).parents('li.parent:first').find('.children').find('input[type=checkbox]').prop('checked', true);
		}

	});

	$('.el-title').on( 'click', function (event) {

		event.preventDefault();
		var $that = $(this),
			visible = 0;

		if( !$('#wsp_selection').prop('checked') ) {
			$('#wsp_selection').prop('checked', true);
			$('#wsp_all').prop('checked', false);
			$.sharing_icons({'type': 'select'});
		}

		$(this).parents('li.child:first').find('.ico-element').toggleClass('fa-times fa-check').toggleClass('text-red text-green');

		setTimeout(function(){
			if( $that.parents('li.child:first').find('.ico-element').hasClass('fa-check') ) {
				$that.parents('li.child:first').find('input[type=checkbox]').prop('checked', true);
			}
			else {
				$that.parents('li.child:first').find('input[type=checkbox]').prop('checked', false);
			}
		}, 500 )

		setTimeout(function(){
			var all = $that.parents('ul.children:first').find('.ico-element');
			$.each( all, function(i, v){
				if($(v).hasClass('fa-check')) {
					visible++;
				}
			} )
		}, 500 )
		setTimeout(function(){
			if( visible == $that.parents('ul.children:first').find('.ico-element').length ) {
				$that.parents('.parent:first').find('.all-elements').removeClass('fa-times text-red').addClass('fa-check text-green')//;
				$that.parents('.parent:first').find('.tree-toggle').addClass('showing');
			}
			else {
				$that.parents('.parent:first').find('.all-elements').removeClass('fa-check text-green').addClass('fa-times text-red')//.fadeOut();
				$that.parents('.parent:first').find('.tree-toggle').removeClass('showing');
			}
		}, 800 )

	});

	$('i.tree_icons').on( 'click', function (event) {

		event.preventDefault();

		if( $(this).hasClass('opened') ) {

			$(this).removeClass('opened fa-minus').addClass('closed fa-plus');

			$(this).parents('li.parent:first').find('ul.children:first').slideUp(400);

		}
		else {
			$(this).removeClass('closed fa-plus').addClass('opened fa-minus');

			$(this).parents('li.parent:first').find('ul.children:first').slideDown(400);

		}

	});

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

	/* select/deselect list items */
	$('.perm_users li.list-group-item').on('click', function(e){
			e.preventDefault();
		if( !$(this).hasClass('active') ) {

			$('.perm_users li.list-group-item').removeClass('active');
			$('.perm_users li.list-group-item').find('input.user-check').prop('checked', false);
			$(this).toggleClass('active');

			if( $(this).hasClass('active') ) {
				$(this).find('input.user-check').prop('checked', true);
			}
			else {
				$(this).find('input.user-check').prop('checked', false);
			}

			$('#advance').addClass('disabled');

			if( $('.perm_users li.list-group-item.active').length > 0 ) {
				$('.submit_sharing').removeClass('disabled');
			}

			var prms = {'type': ''};
			if( $('#wsp_all').prop('checked') ) {
				prms = {'type': 'all'}
			}

			if( $(this).hasClass('active') ) {
				$('.sharing-icon').html('<i class="fa fa-spinner fa-pulse" style="font-size: 23px;"></i>');
				$.ajax({
					url: $js_config.base_url + 'workspaces/quick_share_permissions/' + $js_config.project_id + '/' + $(this).find('input.user-check').val() + '/' + $js_config.workspace_id,
					type:'POST',
					data: $.param(prms),
					success: function( response, status, jxhr ) {
						setTimeout( function(){
						$('.sharing-icon').html(response);
						}, 200 )
					}
				});
			}
		}
	} )

	/* search within users list */
	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
		return function( elem ) {
			return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
		};
	});

	$('body').delegate('.clear-filter', 'click', function(event){
		event.preventDefault();
		$('.filter-search').val('').trigger('keyup');
		return false;
	})

	$('.filter-search').keyup(function(e) {
		e.preventDefault();
		var searchTerms = $(this).val();

		$( '.perm_users li.list-group-item').each(function() {

			var that = $(this);

			var hasMatch = searchTerms.length == 0 || that.is(':contains(' + searchTerms  + ')');
			if( hasMatch ) {
				that.show();
			}
			else {
				if( that.hasClass('active') ) {
					that.removeClass('active');
					$(this).find('input.user-check').prop('checked', false);
				}
				that.hide();
			}
		});
	});

	$('.submit_sharing').on( "click", function(e){

		e.preventDefault();

		var $this = $(this),
			$form = $('form#modelFormAddSharing'),
			add_share_url = $form.attr('action'),
			runAjax = true;

		console.log($form.serializeArray())
		return;

		// if( runAjax ) {
			runAjax = false;
			$.ajax({
				url: add_share_url,
				type:'POST',
				data: $form.serialize(),
				dataType: 'json',
				beforeSend: function( response, status, jxhr ) {
					// Add a spinner in button html just after ajax starts
					$this.html('<i class="fa fa-spinner fa-pulse"></i>');
				},
				success: function( response, status, jxhr ) {
					$this.html('Submit');
					if(response.success) {
						// location.reload();
					}
				}
			});
			// end ajax

		// }
	})

	$('#advance').on('click', function(e){
		e.preventDefault();

		if( $('input.user-check:checked').length > 0 )
			window.location = $js_config.base_url + 'shares/index/' + $('input#ShareProjectId').val() +'/' + $('input.user-check:checked').val() +'/1';
	})


	/*
	--------------------------------------------------------------------------
	Toggle permission icons
	--------------------------------------------------------------------------
	*/
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
	})


})
</script>
<?php } ?>