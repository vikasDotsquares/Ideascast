<?php
// USED TO SHOW POPOVER BOX WITH A FORM WHEN CLICKING ON THE SHARE ICON ON SHARED PROJECT LIST PAGE
// Used in Projects/share_projects.ctp as Element View ?>

<?php //echo $this->Html->css('projects/scroller') ?>
<?php //echo $this->Html->script('projects/plugins/bs.typehead.js', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/scroller', array('inline' => true)); ?>

<div class="modal modal-success fade " id="show_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<style>
.popover {
    width: 530px;
    max-width: 530px;
}
.table {
    max-width: 530px;
}
.input-propagate {
    margin: 0 auto;
}
.table tr:first-child td {
    color: #686868;
    font-size: 12px;
    font-weight: 600 !important;
}
.table {
    margin-bottom: 0 !important;
}
.table td {
    padding-top: 0 !important;
    border: none !important;
}
#tick_on_off, #view_permissions {
    margin: 5px 0 0 !important;
}
.search-select li a i{
visibility:visible !important;
padding-right:0px !important;
}
</style>
<div class="form-input-body">

		<table class="table">
			<tr>
				<td width="35%"  class="text-left"> Share With </td>
				<?php if( can_propagate($project_id) ) { ?><td width="20%" class="text-center" > Propagation </td><?php } ?>
				<td width="35%"  class="text-center"> </td>
				<td width="10%"  class="text-right"> </td>
			</tr>
			<tr>
				<td class="text-left">

					<div class="center" id="helper_div">
						<input type="hidden" id="inpUserName" name="data[Share][name]" value="" />

						<div class="select-list bg-gray" title="Select User" id="users_list">

							<a data-selected="" class="btn btn-default pull-left" id="open_list" data-parent="#users_list" href="#" style="min-width: 220px !important">
								Select User <span class="caret"></span>
							</a>
							<ul class="search-select" id=" ">
								<li class="search-handler">
									<div class="controls" style="">
										<input type="text" class="form-control input-xs us_input">
										<span class="error"></span>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</td>
				<td width="" class="text-center">
					<?php if( can_propagate($project_id) ) { ?>
						<div class="input-group input-propagate" title="User can further propagate this project or not">
							<input type="checkbox" name="data[Share][propagating]" value="1" class="checkbox_on_off" id="propagating">
						</div>
					<?php } ?>
				</td>
				<td width="" class="text-center">
					<input type="hidden" id="inpUserId" name="data[Share][user_id]" value="" />
					<input type="hidden" id="projectId" name="data[Share][project_id]" value="<?php echo (isset($project_id) && !empty($project_id)) ? $project_id : 0; ?>" />
					<input type="hidden" id="share_action" name="data[Share][share_action]" value="3" />

						<button type="submit" style="margin: 5px 2px;" class="btn btn-xs btn-success " title="Save Sharing" id="set_sharing">Submit</button>
						<button type="reset" style="margin: 5px 2px;" class="btn btn-xs btn-danger " title="Close Sharing Box" id="reset_sharing">Close</button>

				</td>
				<td width="" class="text-right">
					<a href="#" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'my_permissions', $project_id, 'admin' => FALSE ), TRUE ); ?>" class="btn btn-xs btn-warning tipText" title="View your permissions and propagations" id="view_permissions" role="button"  data-target="#permissionModal" data-toggle="modal"> <i class="fa fa-user" id=""></i> </a>
				</td>
			</tr>
		</table>
		<span class="trigger_error"></span>
</div>

<script type="text/javascript">
// Waiting for the DOM ready...
$(function(){
	// console.clear();




	$('.search-select').on('click', function(event){
		// $(this).scroller()
	})

	$('#view_permissions').tooltip({ container: 'body', placement: 'right'})

	$("#inpUserId").val();
	$(".trigger_error").text('');

	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offLabel: " Off ",
		onLabel: " On ",
		offTitle: "Further sharing not allowed",
		onTitle: "Further sharing is allowed",
	})
	setTimeout(function(){
		$('#tick_on_off').find('a:first').addClass('tipText').tooltip({ container: 'body', placement: 'top'})
		$('#tick_on_off').find('a:last').addClass('tipText').tooltip({ container: 'body', placement: 'top'})
		$('.tipText')
	},1000)

	$('body').delegate('.search-select li:not(.search-handler)', 'click', function(event) {

		var $el = $(this),
			$a = $el.find('a[data-value]'),
			$inp = $('input#inpUserId'),
			data = $a.data(),
			value = data.value;

			$inp.val( (value != '') ? value : '')
	})

	var userNames = new Array();
	var user_ids = new Array();
	var user_id_str = '';
	var userIds = new Object();

	$.getJSON( '<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'propagate_users', $project_id, 'admin' => FALSE ), TRUE ); ?>', null, function ( jsonData ) {
		var $users_list = $('.search-select')
		$.each( jsonData, function ( index, user ) {

			var profile = '	<a style="color:#D81B60 !important" class="show_profile text-red pull-right btn btn-sm" data-remote="'+$js_config.base_url+'/shares/show_profile/'+user.id+'"  data-target="#popup_modal" data-toggle="modal" data-backdrop="false" href="#"><i class="fa fa-user"></i></a>';
			//<i class="fa fa-user"></i>
			$users_list.append('<li class="" ><a class="user_name" data-value="'+user.id+'"  href="#">'+user.label+'</a>'+profile+'</li>')
			userNames.push( user.label );
			user_ids.push( user.id );

			userIds[user.label] = user.id;
		});

		if(user_ids.length) {
			user_id_str = user_ids.join(',');
			$('#helper_div').append('<input type="hidden" id="userIds" name="userIds" value="'+user_id_str+'" />')
			console.log()
		}
	})
	.done(function(d){
		/* setTimeout(function(){
		$( '#inpUserName' )
			.typeahead({
					local: userNames,
					highlight: true,
				}
			)
			.on('typeahead:opened', onOpened)
			.on('typeahead:selected', onSelected);
		}, 500) */
	})

	$('#inpUserName').on('keyup', function(event) {
		if( $(this).val().trim() == '' )
			$("#inpUserId").val('')
	})

	function onOpened($e, data) {
		console.log("opened");
		$("#inpUserId").val('')
	}

	function onSelected($e, data) {
		$("#inpUserId").val(userIds[data.value])
	}

	$('body').delegate(".show_profile", 'click', function (event) {
		$(this).modal('show')
	})


	$(this).find('.popover').find('.popover-title').append('<span onclick="$(this).parent().parent().hide();" class="close" id="close_form">&times;</span>')
	$('body').delegate("#set_sharing", 'click', function (event) {
		event.preventDefault();
		var $el = $(this),
			$popover = $(this).parents(".popover:first"),
			$content = $(this).parents(".popover:first").find('.popover-content'),
			pop_data = $popover.data('bs.popover'),
			$propagate_button = pop_data.$element,
			pb_data = $propagate_button.data(),
			$propagating = $content.find('input[type=checkbox][name="data[Share][propagating]"]'),
			url = pb_data.remote;

		if ( $el.data('requestRunning') ) {
			return;
		}

		$el.data('requestRunning', true);

var form_data = {
	'data[Share][user_id]': $content.find('input[type=hidden][name="data[Share][user_id]"]').val(),
	'data[Share][project_id]': $content.find('input[type=hidden][name="data[Share][project_id]"]').val(),
	'data[Share][share_action]': $content.find('input[type=hidden][name="data[Share][share_action]"]').val(),
	'data[Share][propagating]': ($propagating.prop('checked')) ? 1 : 0
};

		var id = $content.find('input[type=hidden][name="data[Share][user_id]"]').val();

		if( id != '' ) {
			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				data: form_data,
				url: url,
				global: false,
				beforeSend: function (res) {

				},
				complete: function (data, textStatus, jqXHR) {
					$el.data('requestRunning', false);
				},
				success: function (response, status, jxhr) {
					$content.find('.trigger_error').text('')
					var type = '';

					if (response.success) {
						$popover.fadeOut(500)
						setTimeout(function() {
							$propagate_button.popover('hide');
						}, 1500)
					}
					else {
						$('trigger_error').text(response.msg)
					}

					type = response.type;
					$('.flash_message').load($js_config.base_url + 'shares/show_flash/'+type).show();
				},
			});
		}

	});

	$('#search').on('keyup', function(evt) {
		// do what you want with the item here
		console.log(event.which)
	})


	/* $('#inpUserName').on('typeahead:selected', function(evt, item) {
		// do what you want with the item here
		console.log(item)
	}) */

	$('.share_propagation').on('hidden.bs.popover', function(){

		$(this).removeData('bs.popover')

	});


})
</script>