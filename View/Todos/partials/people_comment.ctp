<?php
$current_user_id = $this->Session->read('Auth.User.id');
if( isset($data) && !empty($data) ) {
	// pr($data);
?>

	<?php
	foreach( $data as $key => $val ) {
		$comments = $val['DoListComment'];
		$doListData = $this->Todocomon->get_do_list_detail($comments['do_list_id']);

	?>
	<li>
		<div class="comment-people-pic">
			<?php
				$user_data = $this->ViewModel->get_user_data($comments['user_id']);

				$pic = $user_data['UserDetail']['profile_pic'];
				$profiles = SITEURL . USER_PIC_PATH . $pic;

				if(!empty($pic) && file_exists(USER_PIC_PATH.$pic)){
					$profiles = SITEURL . USER_PIC_PATH . $pic;
				}else{
					$profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
				}
                $job_title = htmlentities($user_data['UserDetail']['job_title'],ENT_QUOTES);
                $html = '';
				if( $comments['user_id'] != $current_user_id ) {
					$project_id = null;
					if(isset($doListData['DoList']['project_id']) && !empty($doListData['DoList']['project_id']))
						$project_id = $doListData['DoList']['project_id'];
					$html = CHATHTML($comments['user_id'], $project_id);
				}
				// echo $profiles;
			?>
		<a href="#" style="float: none;"  data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $comments['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <img src="<?php echo $profiles ?>" class="img-circledd pophover" align="left" data-content="<div><p><?php echo htmlentities($user_data['UserDetail']['first_name'],ENT_QUOTES) . ' '. htmlentities($user_data['UserDetail']['last_name'],ENT_QUOTES); ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"  />
                </a>
                </div>

		<div class="comment-people-info people-info">
			<h2><?php echo nl2br($comments['comments']); ?></h2>
			<p class="doc-type">
				<?php
				$uploads = $this->Group->do_comment_uploads($comments['id']);
				if( isset($uploads) && !empty($uploads) ) {
					foreach($uploads as $upkey => $up) {
						$upval = $up['DoListCommentUpload'];
						$ext = pathinfo($upval['file_name']);
				?>
						<span class="dolist-document">
							<span class="download_asset icon_btn icon_btn_sm icon_btn_teal"><span class="icon_text"><?php echo $ext['extension']; ?></span></span>
							<a href="#" class="tipText "><?php echo $upval['file_name'] ?></a>
						</span>
				<?php
					}
				}
				else { ?>
					<!--<span class="dolist-document">
						No Attachment
					</span>-->
				<?php }
				?>
			</p>
			<p class="created-date">
				<?php echo _displayDate($comments['modified']);

				$logedin_user = $this->Session->read("Auth.User.id");

				$likes = $this->Group->do_comment_likes($comments['id']);

				$like_posted = like_posted($logedin_user, $comments['id']);

				$disabledCls = '';
				$disablests = 0;
				if(isset($doListData['DoList']['sign_off']) && !empty($doListData['DoList']['sign_off']) && $doListData['DoList']['sign_off'] == 1 ){
						$disabledCls = "disabled";
						$disablests = 1;
				}


				if($logedin_user == $comments['user_id']) {
				?>

					<a class="btn btn-xs btn-default tipText like_no_comment disabled" data-remote="" data-original-title="Likes">
						<i class="fa fa-thumbs-o-up"></i>
						<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
					</a>

					<a <?php if( $disablests == 0){?> href="#" data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "manage_comment", $do_list_id, $comments['id']), true); ?>" data-toggle="modal" data-target="#todo_model" <?php } ?> class="btn btn-xs btn-default tipText <?php if( $disablests == 1){ echo "disabled"; }?>" data-original-title="Edit comment" ><i class="fa fa-pencil"></i></a>
					<a <?php if( $disablests == 0){?> data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "delete_comment", $comments['id']), true); ?>" <?php } ?> class="btn btn-xs btn-danger tipText <?php if( $disablests == 0){?>delete_comment<?php } else { echo "disabled"; }?>" data-original-title="Delete comment"><i class="fa fa-trash"></i>
					</a>
					<?php
					}
					else
					{


					?>
					<a class="btn btn-xs btn-default tipText <?php if( $like_posted ){ ?>disabled<?php }else{ ?>like_comment<?php } ?> <?php if( $disablests == 1){ echo "disabled"; }?>" <?php if( $disablests == 0){?> data-remote="<?php echo Router::Url(array("controller" => "todos", "action" => "like_comment", $comments['id']), true); ?>"  <?php } ?> data-original-title="Like comment">
						<i class="fa fa-thumbs-o-up"></i>
						<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
					</a>

					<?php
					}
				?>
			</p>
		</div>
	</li>
	<?php } ?>

<?php }
else {
?>
<h4 class="no-comments">No Comments posted.</h4>
<?php } ?>

<script type="text/javascript" >
	$(function(){

		$('a[href="#"]').attr('href', 'javascript:;');

		$('.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
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

	})
</script>
