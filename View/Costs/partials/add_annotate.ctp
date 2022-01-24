<?php
	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
	echo $this->Form->create('ElementCostComment', array('url' => array('controller' => 'costs', 'action' => 'save_annotate'), 'class' => 'form-bordered', 'id' => 'modelFormCostsComment')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title annotationeleTitle" id="myModalLabel">Annotation: <?php echo $tskpboxtitle;?> </h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-group" style="border-bottom: 1px solid #ccc; padding-bottom: 15px;">
			<label class="">Annotate:</label>
			<label class="pull-right btn btn-danger btn-xs tipText" title="Clear Annotate" id="clear_annotate" style="display: none;">
				<i class="fa fa-times"></i>
			</label>
			<textarea rows="3" class="form-control" name="data[ElementCostComment][comments]" id="CostElemnetComments" style="resize: vertical;"></textarea>
			<span class="error-message text-danger" ></span>
		</div>
			<input type="hidden" name="data[ElementCostComment][id]" id="ElementCostCommentId" value="" />
			<input type="hidden" name="data[ElementCostComment][element_id]" id="CostCommentElementId" value="<?php echo $element_id; ?>" />
			<input type="hidden" name="data[ElementCostComment][user_id]" id="CostCommentUserId" value="<?php echo $current_user_id; ?>" />
			<input type="hidden" name="data[ElementCostComment][cost_type]" id="CostCommentUserId" value="<?php echo $cost_type; ?>" />

		<div class="" id="annotate-list">
			<?php if( isset($data) && !empty($data) ) { ?>
				<?php $i=1;
				foreach($data as $key => $row) { ?>
					<?php

						$userDetail = $this->ViewModel->get_user( $row['ElementCostComment']['user_id'], null, 1 );
						// pr($userDetail);
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							$html = '';
							if( $row['ElementCostComment']['user_id'] != $current_user_id ) {
								$html = CHATHTML($row['ElementCostComment']['user_id']);
							}

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
					$project_id = element_project($row['ElementCostComment']['element_id']);
					$project_currency_symbol = project_currency_symbol($project_id);

					?>
				<div class="annotate-item" data-id="<?php echo $row['ElementCostComment']['id']; ?>">
					<div class="annotate">
						<div class="annotate-text-image">
							<img src="<?php echo $user_image; ?>" class="annotate-user-image pophover tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
							<?php if( ($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']) &&  ($userDetail['UserDetail']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>	
									<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
							<?php } ?>
						</div>
						<div class="annotate-text">
							<div class="annotate-cost"><?php echo isset($row['ElementCostComment']['cost'])? "<span class='cost-symbol'>".$project_currency_symbol."</span>".$row['ElementCostComment']['cost'] : "<span class='cost-symbol'>".$project_currency_symbol."</span>"."0"; ?></div>
							<span class="editannotatetext">
							<?php
								$string = $row['ElementCostComment']['comments'] ;
								echo stripslashes(str_replace('\r\n','<br/>',$string));
							 ?></span>
						</div>

					</div>
					<div class="date-options">
						<span class="date-text"><?php echo _displayDate($row['ElementCostComment']['created']); ?></span>
						<span class="controls">
						<?php if( $row['ElementCostComment']['user_id'] == $current_user_id ) { ?>
							<a type="button" id="" class="btn btn-default btn-xs edit_annotate">
								<i class="fa fa-pencil"></i>
							</a>
							<a type="button" id="" class="btn btn-danger btn-xs delete_annotate">
								<i class="fa fa-trash"></i>
							</a>
						<?php } ?>
						</span>
					</div>
				</div>
				<?php $i++; } ?>
			<?php }
			else { ?>
			<div id="no-annotate-list" >No Annotations</div>
			<?php } ?>

		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" id="submit_annotate" class="btn btn-success">Save</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function() {
	// console.clear();
	$('#model_bx').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});


	$('body').delegate('.edit_annotate', 'click', function(event){
		event.preventDefault();
		var $parent = $(this).parents('.annotate-item:first'),
			id = $parent.data('id'),
			//text = $parent.find('.annotate-text').text();
			text = $.trim($parent.find('.editannotatetext').html());
		text = text.replace(/<br\s*[\/]?>/gi, "\n");
		$('#ElementCostCommentId').val(id);
		$('#CostElemnetComments').val(text);

		$('#clear_annotate').show();
	})

	$('body').delegate('#clear_annotate', 'click', function(event){
		event.preventDefault();
		$("textarea#CostElemnetComments").val('');
		$('#ElementCostCommentId').val('');
		$(this).hide();
	})


        $('body').delegate('#CostElemnetComments', 'keyup focus', function(event){
            var characters = 250;
            event.preventDefault();
            var $error_el = $(this).parent().find('.error-message');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
            if( $(this).val().length > 0 ) {
				$('#clear_annotate').show();
			}
			else {
				$('#clear_annotate').hide();
			}
        })



/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    })  */
	$('body').on('click', function (e) {
		$('.pophover').each(function () {
			//the 'is' for buttons that trigger popups
			//the 'has' for icons within a button that triggers a popup
			if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				var $that = $(this);
				$that.popover('hide');
			}
		});
	});
})
</script>
