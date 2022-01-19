<?php
echo $this->Html->css('projects/tooltip');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_sharing', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>



<?php
// Set it to true to collapse all tree levels on page load.
// Also assign this value to the global js variable
$collapse = true;

?>
<script type="text/javascript">
jQuery(function($) {

	$js_config.start_collapse = '<?php echo $collapse; ?>';

	    $(".btn").click(function(){

        $("#myCollapsible").collapse({

            toggle: false

        });

    });


	/* var dfd = $.Deferred();
	function doSomethingLater( fn, time ) {


		setTimeout(function() {
			dfd.resolve( fn() );
		}, time || 0);

		return dfd.promise();
	}
	var promise = doSomethingLater(function() {
		console.log( '1. This function will be called in 2000ms' );
	}, 2000);

	// $.fn.toggler = function() {

		// var deferred = $.Deferred();

		// $(this).animate({
			 // 'opacity': 1,
			 // 'width': '120px'
		// }, 3000, function () {
			// $(this).show()
			// console.log('in toggler animate')
			// console.log($(this))
			// $(this).find('label').css({opacity: 1})
			// deferred.resolve(1);
		// })

		// return deferred.promise();
	// }

	// call like
	// $pro_perm.toggler().promise().done(function(){
		// $this.toggleClass('activated')
		// console.log( "Both operations are done" );
	// })
 */
	$('input[type=checkbox]').prop('checked', false);

	$('body').delegate('label.permissions', 'click', function(event) {
		var e = $(this);

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

	})

	$('body').delegate('label.applied_permissions', 'click', function(event) {
		event.preventDefault()
		var e = $(this);
		console.log('applied_permissions')
	})

	$(".propogation").on('click', function(event){
		event.preventDefault();
		var $this = $(this);

		var $propogat_wrapper = $(this).parent('.propogation-wrapper'),
			$pro_perm = $propogat_wrapper.find('.propogat_permisions'),
			$pro_perm_inner = $pro_perm.find('.options-inner');


		if( $pro_perm.css('opacity') <= "0" ) {
			$pro_perm.animate({
				opacity: 1,
				width: '120px'
			}, 300,
			function () {
			})
				$pro_perm_inner.css({opacity: 1})
		}
		else {
			$pro_perm_inner.css({opacity: 0})

			setTimeout(function(){
				$pro_perm.animate({
					opacity: 0,
					width: 0
				}, 300,
				function () {
				})
			}, 600)
		}
		$(this).toggleClass('activated')

	})

	$('#selectUser').on('click', function(event){
		event.preventDefault();
		$('#frm_share_user').submit()
	})


	function resizeStuff() {
		$('.tree_links').ellipsis_word();
	}

	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 800); //800 is time in miliseconds
	});

	$('body').delegate('#rst_share_tree', 'click', function(event){
		event.preventDefault();
		var $that = $('#frm_share_tree');
			$that[0].reset()
	})

	$('body').delegate('#sbmt_share_tree', 'click', function(event){
		event.preventDefault();

		var $t = $(this),
			$that = $('#frm_share_tree'),
			$propogation_switch = $('input[name=enable_propogation]'),
			$propogation_enable = $that.find('input[name=propogation_enable]'),
			prop_chk = $propogation_switch.prop('checked'),
			prop_chk_value = (prop_chk) ? true : false;

		// PROPOPGATION ON/OFF SWITCH VALUE
		// STORE IN OTHER FIELD THAT IS UNDER THIS FORM
		// CREATE IT DYNAMICALLY AFTER REMOVE
		if( $propogation_enable.length )
			$propogation_enable.remove();

			var input = $("<input>")
						   .attr("type", "hidden")
						   .attr("name", "propogation_enable")
						   .val(prop_chk_value);
			$that.append($(input));

			// .prop('checked', prop_chk_value)
			// console.log($that.serializeArray())
			// console.log($that.serializeObjects())
		$that.submit()
		// return true;
	})

	setTimeout(function(){
		if($('.sharing_tree').length) {
			// $('.permissions.tipText').tooltip({ container: 'body', placement: 'bottom'})
			//
		}
	}, 600)
})
$(window).on('load', function(event){
		// setTimeout(function(){
		// }, 600)
})
</script>



<div class="row">
	<div class="col-xs-12">
  <?php echo $this->Session->flash(); ?>
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<?php  if( isset($project_detail )) {  ?>
					<p class="text-muted date-time">Project:
						<span>Created: <?php
						//echo date('d M Y h:i:s', $project_detail['Project']['created']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s');
						?></span>
						<span>Updated: <?php
						//echo date('d M Y h:i:s', $project_detail['Project']['modified']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['modified']),$format = 'd M Y h:i:s');
						?></span>
					</p>
					<?php }  ?>
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
							<div class="center">
								<?php
							 // pr($this->params['pass'][0], 1);
							$url = array('controller' => 'shares', 'action' => 'index' );
							if( isset($this->params['pass'][0]) && !empty($this->params['pass'][0])) {
								$url = array_merge($url, [$this->params['pass'][0]]);
							}


							echo $this->Form->create('ShareUser', array('url' => $url, 'class' => 'formAddElementNote', 'id' => 'frm_share_user', 'enctype' => 'multipart/form-data')); ?>

								<div class="select_dropdown">

									<?php if( isset($users_list) && !empty($users_list)) { ?>

									<select name="data[Share][user_id]" class="form-control">

										<option>Select User</option>

										<?php foreach($users_list as $key => $val ) {
											$selectedUser = ( isset($shareUser) && !empty($shareUser) && $key == $shareUser) ? ' selected="selected" ' : '';

										?>

											<option value="<?php echo $key; ?>"<?php echo $selectedUser; ?>><?php echo $val; ?>
											</option>
										<?php } ?>
									</select>
									<?php } ?>
								</div>
								<a type="submit" href="#" id="selectUser" class="btn btn-danger">Share</a>
								<?php echo $this->Form->end(); ?>
							</div>


				<?php if( isset($shareUser) && !empty($shareUser)) {
					$user_detail = $this->ViewModel->get_user($shareUser, ['hasOne' => array('UserInstitution'), 'hasMany' => array('UserProject', 'UserPlan', 'UserTransctionDetail')], 1);
					?>
                            <div class="panel panel-default" id="user_detail_section">
                                <div class="table-responsive">
                                    <table class="table table-bordered">

                                        <tr>
                                            <th width="25%" class="text-left">Name</th>
                                            <th width="30%" class="text-left">Email</th>
                                            <th width="15%" class="text-center">Active/Inactive</th>
                                            <th width="15%" class="text-center">Role Level</th>
                                            <th width="15%" class="text-center">Propogation</th>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo $user_detail['UserDetail']['first_name'].' '. $user_detail['UserDetail']['last_name']; ?></td>
                                            <td class="text-left"><?php echo $user_detail['User']['email']; ?></td>
                                            <td class="text-center"><?php echo ($user_detail['User']['status']) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?></td>
                                            <td class="text-center">
												<div class="btn-group toggle-group propogation-on-off">

													<a href="" class="btn btn-xs btn-toggle toggle-on"><i class="fa fa-arrow-up"></i></a>

													<a href="" class="btn btn-xs btn-toggle toggle-off"><i class="fa fa-arrow-down"></i></a>

												</div>
											</td>
											<td class="text-center">
	<div data-toggle="toggle" class="toggle" style="">
		<input type="checkbox" name="enable_propogation" value="0">
		<div class="toggle-handle propogation-handle off option-disabled">
			<label class="btn btn-success btn-xs">On</label>
			<label class="btn btn-danger btn-xs">Off</label>
		</div>
	</div>
											</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
				<?php } ?>

					<?php if( isset($shareUser) && !empty($shareUser)) { ?>
					<div class="panel panel-default sharing_tree " style="">

						<div class="panel-heading clearfix">
							<strong><?php //echo $page_heading; ?></strong>
							<div class="box-tools pull-left">
								<a style=" " href="#"  class="btn btn-danger btn-xs togg_search">
									<i class="fa fa-chevron-down"></i>
								</a>
							</div>

							<div class="pull-right search_box" style="display: none;">
								<div>
								<input placeholder="Search in List" class="search_list pull-left" id="search_list" />
									<span class="fa fa-search ifa-search-box"></span>
								</div>
								<div id="sidetreecontrol" class="pull-right">
									<a class="btn btn-primary action_buttons pull-right" data-action="collapse_all" href="#" style="margin: 0 0 0 3px;">Collapse All</a>
									<a class="btn btn-primary action_buttons" data-action="expand_all" href="#">Expand All</a>
								</div>
							</div>

						</div>
						<div class="panel-body">
							<?php

							echo $this->Form->create('ShareTree', array('url' => array('controller' => 'shares', 'action' => 'manage_sharing', $project_id, $shareUser ), 'class' => 'formAddElementNote', 'id' => 'frm_share_tree', 'enctype' => 'multipart/form-data')); ?>
                                <div class="table-responsive">
                                    <table class="table">
										<tr>
											<th>
												<div class="shares-list">
													<ul>
														<li class="shares-tree-heading">
															<label>Path</label>
															<div>Permissions</div>
														</li>
													</ul>
												</div>
											</th>
										</tr>

										<tr>
											<td style="padding-left: 0px;">

<ul class="list-group">
	<li class="list-group-item clearfix" style="background-color: rgba(0, 120, 120, 0.1);">
		<span style="display: inline-block; margin: 8px 10px 8px 0px;" class="clearfix pull-left"><?php echo $project_detail['Project']['title']; ?></span>
		<span class="pull-left" style="display: inline-block;">
			<?php echo project_permissions_html( ['id' => $project_id] ) ?>
			<!-- <button class="btn btn-xs btn-info">CCS</button>
			<button class="btn btn-xs btn-warning">
			  <span class="glyphicon glyphicon-trash"></span>
			</button>-->
      </span>
	</li>
</ul>


												<div id="multi_list" >
												<?php
												if( isset($project_id) && !empty($project_id) ) {

													echo $this->Form->input('Share.user_project_id', ['type' => 'hidden', 'value' => $project_id]);
													echo $this->Form->input('Share.user_id', ['type' => 'hidden', 'value' => $shareUser]);

													echo sharing_tree($project_id, false, $collapse);
												}
												?>
											<!-- <input type="hidden" name="enable_propogation_copy" value="">-->
												</div>
											</td>
										</tr>
									</table>

                                </div>
							</div>

                            <div class="panel-footer">
                                <div align="right">
									<a href="#" class="btn btn-success" id="sbmt_share_tree" >Save</a>
									<a href="" class="btn btn-danger" id="rst_share_tree">Cancel</a>
                                </div>
							</div>
							<?php echo $this->Form->end(); ?>


                    </div>
					<?php } ?>

                </div>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript" >
$(function() {

})
</script>