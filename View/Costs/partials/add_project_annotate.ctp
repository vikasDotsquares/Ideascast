<?php
	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
	echo $this->Form->create('ProjectCostComment', array('url' => array('controller' => 'costs', 'action' => 'save_project_annotate'), 'class' => 'form-bordered', 'id' => 'modelFormCostsProjectComment')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title annotationwspTitle" id="myModalLabel">Annotation: <?php echo $wspboxtitle;?></h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-group" style="border-bottom: 1px solid #ccc; padding-bottom: 15px;">
			<label class="">Annotate:</label>
			<label class="pull-right btn btn-danger btn-xs tipText" title="Clear Annotate" id="clear_project_annotate" style="display: none;">
				<i class="fa fa-times"></i>
			</label>
			<textarea rows="3" class="form-control" name="data[ProjectCostComment][comments]" id="CostProjectComments" style="resize: vertical;"></textarea>
			<span class="error-message text-danger" ></span>
		</div>
			<input type="hidden" name="data[ProjectCostComment][id]" id="ProjectCostCommentId" value="" />
			<input type="hidden" name="data[ProjectCostComment][project_id]" id="CostCommentProjectId" value="<?php echo $project_id; ?>" />
			<input type="hidden" name="data[ProjectCostComment][user_id]" id="CostCommentUserId" value="<?php echo $current_user_id; ?>" />
			<input type="hidden" name="data[ProjectCostComment][cost_type]" id="CostCommentType" value="<?php echo $cost_type; ?>" />
			<input type="hidden" name="data[ProjectCostComment][cost]" id="project_cost" value="" />

		<div class="" id="annotate-list">
			<?php if( isset($data) && !empty($data) ) { ?>
				<?php $i=1;
				foreach($data as $key => $row) { ?>
					<?php

						$userDetail = $this->ViewModel->get_user( $row['ProjectCostComment']['user_id'], null, 1 );
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							$html = '';
							if( $row['ProjectCostComment']['user_id'] != $current_user_id ) {
								$html = CHATHTML($row['ProjectCostComment']['user_id']);
							}

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}

					$crysymbol = '<i class="fa fa-gbp"></i>';
					if( !empty($row['ProjectCostComment']['currency_id']) ){

						// $projectCurrencyName = $this->Common->getCurrencySymbolName($row['ProjectCostComment']['currency_id']);
						$projectCurrencyName = $this->Common->getCurrencySymbolNameByID($row['ProjectCostComment']['currency_id']);

						/*if($projectCurrencyName == 'USD') {
							$projectCurrencysymbol = '<i class="fa fa-dollar"></i>';
						}
						else if($projectCurrencyName == 'GBP') {
							$projectCurrencysymbol = '<i class="fa fa-gbp"></i>';
						}
						else if($projectCurrencyName == 'EUR') {
							$projectCurrencysymbol = '<i class="fa fa-eur"></i>';
						}
						else if($projectCurrencyName == 'DKK' || $projectCurrencyName == 'ISK') {
							$projectCurrencysymbol = '<span style="font-weight: 600">Kr</span>';
						}

						if( isset($projectCurrencysymbol) && !empty($projectCurrencysymbol) ){
							$crysymbol = $projectCurrencysymbol;
						}*/
						$crysymbol = $projectCurrencyName;

					}

					?>
				<div class="annotate-item" data-id="<?php echo $row['ProjectCostComment']['id']; ?>">

					<div class="annotate">

						<div class="annotate-text-image">
							<img src="<?php echo $user_image; ?>" class="annotate-user-image pophover tipText" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
							<?php if( ($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']) &&  ($userDetail['UserDetail']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>	
								<i class="communitygray18 team-meb-com tipText" title="Not In Your Organization"></i>
							<?php } ?>
						</div>

						<div class="annotate-text">
                        <div class="annotate-cost"><?php echo isset($row['ProjectCostComment']['cost'])? "<span class='cost-symbol'>".$crysymbol."</span>"." ".$row['ProjectCostComment']['cost'] : "<span class='cost-symbol'>".$crysymbol."</span>".''.'0'; ?></div>
							<span class="editannotatetext">
							<?php //echo trim(stripslashes($row['ProjectCostComment']['comments'])); ?>
							<?php
							$string =   $row['ProjectCostComment']['comments'] ;

							echo stripslashes(str_replace('\r\n','<br/>',$string));

							?>
							</span>

						</div>



					</div>

					<div class="date-options">
						<span class="date-text"><?php echo _displayDate($row['ProjectCostComment']['created']); ?></span>
						<span class="controls">
						<?php if( $row['ProjectCostComment']['user_id'] == $current_user_id ) { ?>
							<a type="button" id="" class="btn btn-default btn-xs edit_project_annotate">
								<i class="fa fa-pencil"></i>
							</a>
							<a type="button" id="" class="btn btn-danger btn-xs delete_project_annotate">
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
		<button type="button" id="submit_project_annotate" class="btn btn-success">Save</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function() {
	$('#project_cost').val($.prjcost);

	$('#model_bx_wsp').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});

	$('body').delegate('.edit_project_annotate', 'click', function(event){
		event.preventDefault();
		var $parent = $(this).parents('.annotate-item:first'),
			id = $parent.data('id'),
			text = $.trim($parent.find('.editannotatetext').html());
		text = text.replace(/<br\s*[\/]?>/gi, "\n");
		$('#ProjectCostCommentId').val(id);
		$('#CostProjectComments').val(text);

		$('#clear_project_annotate').show();
		$('#CostProjectComments').trigger('keyup')
	})


	$('body').delegate('#clear_project_annotate', 'click', function(event){
		event.preventDefault();
		$("textarea#CostProjectComments").val('');
		$('#ProjectCostCommentId').val('');
		$(this).hide();
		$('.error-message').text('')
	})

    $('body').delegate('#CostProjectComments', 'keyup focus', function(event){
        var characters = 250;

        event.preventDefault();
        var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
		if( $(this).val().length > 0 ) {
			$('#clear_project_annotate').show();
		}
		else {
			$('#clear_project_annotate').hide();
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
