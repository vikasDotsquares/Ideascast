<div class="idea-blog-list" id="blog_user_lists">
    <?php

    $allwikipageusers = $this->Wiki->getWikiDocumentAllUserLists($project_id, $user_id,$wiki_id,$wiki_page_id);
    if (isset($allwikipageusers) && !empty($allwikipageusers)) {
        ?>
        <ul class="list-inline page_users list-group">
            <?php
            foreach ($allwikipageusers as $userList) {
                $user_data = $this->ViewModel->get_user_data($userList);
                $user_id = $userList;
                $pic = $user_data['UserDetail']['profile_pic'];
                $profiles = SITEURL . USER_PIC_PATH . $pic;

                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }
                // echo $profiles;
                ?>
                <li>

                    <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $userList; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                        <img src="<?php echo $profiles; ?>" alt=""/>
                    </a>
                    <h5><?php echo $this->Common->userFullname($userList); ?></h5>
                    <p>
                        <?php $pageCount = $this->Wiki->userTotalWikiDocument($project_id,$user_id,$wiki_id); ?>
                        <i class="fab fa-wikipedia-w gotowiki tipText" style="cursor: pointer;" title="" data-original-title="Wiki Details"></i>
                        <a title="Uploaded <?php echo $pageCount; ?> document by <?php echo $this->Common->userFullname($userList); ?>" data-remote="<?php echo SITEURL; ?>wikies/get_wiki_document_by_user/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" data-user-id="<?php echo $user_id; ?>" class="tipText btn btn-xs btn-default get_wiki_document_by_user"  >
                            <i class="fa fa-folder-o"></i>&nbsp;(<?php echo $pageCount; ?>)
                        </a>
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
    <div class="modal modal-success fade " id="popup_modal_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
    <!-- END MODAL BOX -->
</div>
<script type="text/javascript" >
    $(function ($) {
        //$("body").delegate(".page-list-by-user", 'click', function (e) {
        $(".page-list-by-user").click( function (e) {
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
                        $(".wiki-inner ul li").removeClass("active");
                        $(".wiki-inner ul li:first-child").addClass("active");
                        $(".tabContentLeft").html(response);
                        is_send = false;
                    }
                });
            }
            return false;
        });
    });
</script>