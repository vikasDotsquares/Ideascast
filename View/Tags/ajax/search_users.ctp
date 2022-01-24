<script>
//var q_param =  '<?php echo $q; ?>';
//var q_param_arr = q_param.split(',');
</script>
<style>
a.clear_tag.disabled{
	pointer-events: none;
	opacity: .65;
}
</style>
<?php
$current_org = $this->Permission->current_org();
$query_tags = explode('$$$', $q);

//pr($query_tags);

$totCount = 0;
if(isset($result) && !empty($result) && count($result['tagElemsCnt']) > 0 ){
	$totCount = count($result['tagElemsCnt']);
	foreach ($result['tagElements'] as $key => $tag) {
		$user_id = $tag['tags']['tagged_user_id'];
		$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		$user_name = 'Not Available';
		$first_name = 'Not';
		$last_name = ' Available';
		$job_title = 'Not Available';
		$html = '';
		if(isset($userDetail) && !empty($userDetail)) {
			$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
			$first_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES);
			$last_name = htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
			$profile_pic = $userDetail['UserDetail']['profile_pic'];
			$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

			if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
				$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
			}
		}
		$tagCnt = $tag[0]['tagcnt'];
		if($match_all == 0) {
			$tagCnt = $tag[0]['totMatch'];
		}
		$user_org = $this->Permission->current_org($user_id);
		?>
		<div class="tag-data-row" data-user="<?php echo $user_id; ?>" data-filter="" data-user_first_name="<?php echo $first_name; ?>" data-user_last_name="<?php echo $last_name; ?>" data-tag="<?php echo $tagCnt ?>">
			<div class="tag-col-data tag-col-data-1">
				<div class="style-people-com">
					<span class="style-popple-icon-out">
						<a class="style-popple-icon" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p></div>" >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  />
						</a>
						<?php if($current_org['organization_id'] != $user_org['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"></i>
						<?php } ?>
						</span>

					<div class="style-people-info">
						<a   data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"   >
						<span class="style-people-name"><?php echo $user_name; ?></span>
						<span class="style-people-title"><?php echo $job_title; ?></span>
						</a>
					</div>
			 	</div>
			</div>

			<div class="tag-col-data tag-col-data-2">
				<div class="tag_container tags_<?php echo $user_id?>">
				<?php //echo $tag[0]['taglist']?>
				<?php
				$tags = explode('$$$',$tag[0]['taglist']);
				foreach($tags as $k => $v) {
					if(in_array($v, $query_tags, true)) {
						echo '<div class="token" data-title="'.htmlentities($v,ENT_QUOTES,'UTF-8').'"><span class="token-label" style="background-color:#e7d685">'.htmlentities($v,ENT_QUOTES,'UTF-8').'</span></div>';
					}else {
						echo '<div class="token" data-title="'.htmlentities($v,ENT_QUOTES,'UTF-8').'"><span class="token-label">'.htmlentities($v,ENT_QUOTES,'UTF-8').'</span></div>';
					}
				}
				?>
				</div>
				<script>
				/*$(".tags_<?php echo $user_id;?>").html($(".tags_<?php echo $user_id;?>").html().split(',').map(function(el) {
					return '<div class="token"><span class="token-label">' + el + '</span></div>';
				}))*/
				</script>
			</div>
			<div class="tag-col-data tag-col-data-3 actionlink action_<?php echo $user_id?>">
				<a href="javascript:void(0)" class="tipText" title="Edit Tags" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $tag['tags']['tagged_user_id'], 'admin' => FALSE ), TRUE ); ?>" >
					<i class="tagicon edittags"></i>
				</a>
				<a href="javascript:void(0)" class="clear_tag RecordDeleteClass tipText" title="Clear Tags" data-id="<?php echo($tag['tags']['tagged_user_id']) ?>"><i class="tagicon alltags"></i></a>
				   <a href="<?php echo SITEURL.'/resources/people/user:'.$tag['tags']['tagged_user_id']; ?>" class="tipText  " title="Go To People" data-id="pp"><i class="peopleblack18"></i></a> 
			</div>
		</div>
		<?php
	}
	?>
	<script>
		$(function(){
			var total = parseInt(<?php echo $totCount; ?>);
			$('.peoplecount-info .people-count').html(<?php echo $totCount; ?>);
			//$('#menu1').attr('disabled', false);
			//$('#menu1').removeClass('disabled');
			$('.tag-dd-menu a.clear_all_tags, .tag-dd-menu a.add_remove_tags').removeClass('disabled');
			$('#paging_max_page').val(<?php echo $totCount; ?>);
			$('#paging_type').val('tag');
			$('.pophover').popover({
				placement : 'bottom',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			}).
			on('show.bs.popover', function(){
				var $popover = $(this).data('bs.popover');
				var $tip = $popover.$tip;
			})

			$('.tag-buttons-container .people-section a').removeClass('disabled');
			$('.tag-buttons-container .task-section a').removeClass('disabled');
			$(".RecordDeleteClass").click(function(event){
				$that = $(this);
				var row = $that.parents('tag-data-row:first');
				var deleteURL = $js_config.base_url+'tags/clear_user_tag'; // Extract info from data-* attributes
				var deleteid = $(this).data('id');
				BootstrapDialog.show({
					title: 'Clear Tags',
					message: 'Are you sure you want to clear all Tags for this User?',
					type: BootstrapDialog.TYPE_DANGER,
					draggable: false,
					buttons: [
					{
						label: ' Clear',
						cssClass: 'btn-success',
						autospin: false,
						action: function (dialogRef) {
							$.when(
								$.ajax({
									url : deleteURL,
									type: "POST",
									data: $.param({id: deleteid}),
									dataType: 'JSON',
									global: false,
									// async:false,
									success:function(response){
										//location.reload();
									}
								})
							).then(function( data, textStatus, jqXHR ) {
								if(data.status == true){
									$('.tags_'+deleteid).html('');
									$that.addClass('disabled');

									$.getJSON(
										$js_config.base_url + 'tags/get_saved_tags',
										{ },
										function(result){
											$.availableTags = result;
											//$("input#my_tags").tokenfield('destroy');
											//$.add_tokenfield();
											if(result.length > 0) {
												$('#my_tags').data('bs.tokenfield').$input.autocomplete({source: result});
											} else {
												$("input#my_tags").tokenfield('destroy');
												$.add_tokenfield();
												$("#show_user_btn").attr('disabled', true);
											}
										}
									);
								}else{

								}
								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);
								//dialogRef.getModalBody().html('<div class="loader"></div>');
								setTimeout(function () {
									dialogRef.close();
									// location.reload();
								}, 500);
							})
						}
					},
					{
						label: ' Cancel',
						//icon: '',
						cssClass: 'btn-danger',
						action: function (dialogRef) {
							dialogRef.close();
						}
					}],
					onshow: function(dialogRef){
						var modaltitle = dialogRef.getTitle();
						dialogRef.getModalHeader().find('.bootstrap-dialog-title').remove();
						dialogRef.getModalHeader().append('<h3 class="modal-title">'+modaltitle+'</h3>');

						dialogRef.setType('modal-danger');
					},
				});
			})
		});
	</script>
	<?php
} else {
	?>
	<div class="no-row-wrapper" style="position:unset">NO PEOPLE</div>
	<script>
		$(function(){
			$('.buttons-container .people-section a').addClass('disabled');
			$('.buttons-container .task-section a').addClass('disabled');
			$('.tag-dd-menu a.clear_all_tags, .tag-dd-menu a.add_remove_tags').addClass('disabled');
			//$('#menu1').attr('disabled', true);
			//$('#menu1').addClass('disabled');
		});
	</script>
	<?php
}
?>