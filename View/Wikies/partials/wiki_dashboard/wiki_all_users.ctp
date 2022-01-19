<div class="idea-blog-list" id="blog_user_lists">
    <?php
    $current_user_id = $this->Session->read('Auth.User.id');


    $allwikipageusers = $this->Wiki->getWikiDashboardAllUserLists($project_id, $user_id,$wiki_id,$wiki_page_id);
    if (isset($allwikipageusers) && !empty($allwikipageusers)) {
        ?>
        <ul class="list-inline page_users list-group">
            <?php
            foreach ($allwikipageusers as $userList) {
                $user_data = $this->ViewModel->get_user_data($userList);
                $user_id = $userList;
                $pic = $user_data['UserDetail']['profile_pic'];
                $profiles = SITEURL . USER_PIC_PATH . $pic;
                $job_title = htmlentities($user_data['UserDetail']['job_title']);

                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }
                $html = '';
                if( $userList != $current_user_id ) {
                        $html = CHATHTML($userList,$project_id);
                }
                // echo $profiles;
                ?>
                <li>

                    <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $userList; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                        <img src="<?php echo $profiles; ?>" class="pophover" align="left" data-content="<div><p><?php echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name']; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" />
                    </a>
                    <h5><?php echo $this->Common->userFullname($userList); ?></h5>
                    <p>
                        <?php
						$pageCount = $this->Wiki->userTotalWikiPage($project_id,$user_id,$wiki_id);

						?>
                        <?php if(isset($pageCount) && !empty($pageCount) && $pageCount >= 1){?>
                        <a title="Wiki Page" data-remote="<?php echo SITEURL; ?>wikies/get_wiki_page_by_user_dashboard/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>/null/1" data-user-id="<?php echo $user_id; ?>" class="tipText btn btn-xs btn-default get_wiki_page_by_user_dashboard"  >
                            <i class="fab fa-wikipedia-w tipText" style="cursor: pointer;"></i>&nbsp;<?php echo $pageCount; ?>
                        </a>
                        <?php }else{?>
                        <a title="Wiki Page" class="tipText btn btn-xs btn-default "  >
                            <i class="fab fa-wikipedia-w tipText" style="cursor: pointer;"></i>&nbsp;<?php echo $pageCount; ?>
                        </a>
                        <?php } ?>

                    </p>
                </li>
            <?php } ?>
        </ul>
    <?php }else{ ?>
    <li class="text-center">No wiki user found!</li>
    <?php
    }
    ?>
    <!-- MODAL BOX WINDOW -->
    <!-- <div class="modal modal-success fade " id="popup_modal_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div> -->
    <!-- END MODAL BOX -->
</div>
<script type="text/javascript" >
    $(function ($) {
        $(".get_wiki_page_by_user_dashboard").click( function (e) {
            e.preventDefault();
            var $current = $(this), project_id = '<?php echo $project_id; ?>', wiki_id = '<?php echo $wiki_id; ?>', user_id = $current.data("user-id"), actionURL = $current.data("remote");
            var is_send = false;
            if(is_send == false){
                is_send = true;
                $.ajax({
                    url: actionURL,
                    type: "POST",
                    global:false,
                    async: false, //blocks window close
                    data: {project_id: project_id , user_id: user_id , wiki_id: wiki_id},
                    success: function (response) {
                        $("#tabContent5 .tabContentLeft").html(response);
                        is_send = false;
                    }
                });
            }
            return false;
        });
    });
</script>

<script type="text/javascript" >
	$(function(){

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