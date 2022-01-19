<style type="text/css" media="screen">
	.mtitle {
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    display: block;
	    overflow: hidden;
	}
	.annotate-text {
		word-break: break-all;
	}
	.style-popple-icons{
		cursor:pointer;
	}
	
	.popover p {
    margin-bottom: 2px !important;
	}
	.popover p:nth-child(2) {
		font-size: 11px;
	}
	.style-people-name { white-space : inherit;}
</style>
<?php
	$current_user_id = $this->Session->read('Auth.User.id');

	echo $this->Form->create('ProjectComment', array('url' => array('controller' => 'projects', 'action' => 'save_annotate'), 'class' => 'form-bordered', 'id' => 'modelFormProjectComment')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" class=""><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">
			<span class="mtitle">Annotations</span>
		</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body project-annotations-popup">
		<div class="form-group" style="">
			<label class="">Annotate:</label>
			<label class="pull-right btn btn-danger btn-xs tipText" title="Clear Annotate" id="clear_annotate" style="display: none;">
				<i class="fa fa-times"></i>
			</label>
			<textarea rows="3" class="form-control" name="data[ProjectComment][comments]" id="ProjectCommentComments"   placeholder="max 250 chars"></textarea>
			<span class="error-message text-danger" ></span>
		</div>
			<input type="hidden" name="data[ProjectComment][id]" id="ProjectCommentId" value="" />
			<input type="hidden" name="data[ProjectComment][project_id]" id="ProjectCommentProjectId" value="<?php echo $project_id; ?>" />
			<input type="hidden" name="data[ProjectComment][user_id]" id="ProjectCommentUserId" value="<?php echo $current_user_id; ?>" />

		<div class="" id="annotate-list">
			<?php $current_org = $this->Permission->current_org();
			if( isset($data) && !empty($data) ) { ?>
				<?php foreach($data as $key => $row) { ?>
					<?php
						$userDetail = $this->ViewModel->get_user( $row['ProjectComment']['user_id'], null, 1 );
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

							$html = '';
							
							
							if( $row['ProjectComment']['user_id'] != $current_user_id ) {
								$html = CHATHTML($row['ProjectComment']['user_id'],$row['ProjectComment']['project_id']);
							}

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}


						$current_org_other = $this->Permission->current_org($row['ProjectComment']['user_id']);

					?>
				<div class="annotate-item" data-id="<?php echo $row['ProjectComment']['id']; ?>">
					<div class="style-people-com">
					
					<a class="style-popple-icons "      data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo  $row['ProjectComment']['user_id']; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
            							 
						<span class="style-popple-icon-out">
							<span class="style-popple-icon" style="cursor: default;">
							<img src="<?php echo $user_image; ?>" style="cursor:pointer;" class=" tipText pophover" title="<?php echo $user_name; ?>" align="left" width="36" height="36" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">

							<?php   if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
								<i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
							<?php }  ?>
							</span>
						</span>
					</a>
						<div class="style-people-info">
						<span class="style-people-name " style="cursor: default;"><?php echo nl2br($row['ProjectComment']['comments']); ?></span>
						<span class="date-text"><?php echo _displayDate($row['ProjectComment']['created']); ?></span>
						<div class="date-options">
						<span class="controls">
						<?php if( $row['ProjectComment']['user_id'] == $current_user_id ) { ?>
							<a type="button" id="" class="edit_annotate tipText" title="Edit Annotation">
								<i class="edit-icon"></i>
							</a>
							<a type="button" id="" class="delete_annotate tipText" title="Delete Annotation">
								<i class="deleteblack"></i>
							</a>
						<?php } ?>
						</span>
					    </div>
					  </div>
					</div>


				</div>
				<?php } ?>
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
			text = $parent.find('.style-people-name').text();

		$('#ProjectCommentId').val(id);
		$('#ProjectCommentComments').val(text);

		$('#clear_annotate').show();
	})

	$('body').delegate('#clear_annotate', 'click', function(event){
		event.preventDefault();
		$("textarea#ProjectCommentComments").val('');
		$('#ProjectCommentId').val('');
		$(this).hide();
	})

    $('body').delegate('#ProjectCommentComments', 'keyup focus', function(event){
        var characters = 250;

        event.preventDefault();
        var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })


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
	
	$('.style-popple-icons').off('click').on('click', function(event) {
			
		$('#model_bx').modal('hide');
	})	
})
</script>
 <script>
$(function(){
	
 $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })
		
})
</script>	