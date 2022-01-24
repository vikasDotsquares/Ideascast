  
<?php
if (isset($wikipage['WikiPage']['user_id']) && $wikipage['WikiPage']['user_id'] == $this->Session->read('Auth.User.id')) {
    $is_full_permission_to_current_login = true;
}
?>
<div class="btn-group">
    <?php
    $signoffparam = "wikies/wiki_page_sign_off/" . $project_id . "/" . $this->Session->read('Auth.User.id') . "/" . $wiki_id . "/" . $wikipage['WikiPage']['id'];
    $signoffclass = 'wiki_page_signoff';
    $signofftitle = "Sign Off";
    if (isset($wikipage['WikiPage']['sign_off']) && $wikipage['WikiPage']['sign_off'] == 1) {
        $signoffclass = 'wiki_page_signedoff';
        $signofftitle = "Signed Off";
    }
    ?>
    <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true && $wikipage['WikiPage']['sign_off'] != 1) { ?>    
        <a href="" data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="btn btn-xs btn-success tipText full_permission" data-remote="<?php echo SITEURL; ?>wikies/update_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $wikipage['WikiPage']['id']; ?>" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" ><i class="fa fa-pencil"></i></a>
    <?php } else { ?>
        <a data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="disabled btn btn-xs btn-success tipText not_full_permission"><i class="fa fa-pencil"></i></a>
<?php } ?>



    <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) { ?>        
        <a href="" data-original-title="Delete Wiki Page" class="btn btn-xs btn-danger tipText delete_wiki_page full_permission" data-remote="<?php echo SITEURL; ?>wikies/delete_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $wikipage['WikiPage']['id']; ?>" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" data-user-id="<?php echo $wikipage['WikiPage']['user_id']; ?>"><i class="fa fa-trash"></i></a>
    <?php } else { ?>
        <a href="" data-original-title="Delete Wiki Page" class="btn btn-xs btn-danger disabled tipText not_full_permission" ><i class="fa fa-trash"></i></a>
<?php } ?>
    <a class="btn btn-warning btn-xs tipText <?php echo $signoffclass; ?>" data-remote="<?php echo SITEURL . $signoffparam; ?>" title="<?php echo $signofftitle; ?>"><i class="fa fa-sign-out"></i></a>

</div>
<div class="btn-group">


    <a href="#left_wiki_page_all_detail_<?php echo $wikipage['WikiPage']['id']; ?>" data-toggle="collapse"  title="Wiki Page Details" class="tipText btn btn-xs btn-default wikipage collapsed">
        <i class="page-details-show fa "></i>
    </a>

    <a href="#wiki_page_all_users_<?php echo $wikipage['WikiPage']['id']; ?>" data-toggle="collapse"  title="Wiki Page All User" class="tipText btn btn-xs btn-default">
        <i class="fa fa-user-victor"></i>
    </a>

    
    <a class="btn btn-default btn-xs tipText gotowiki"  title="Main Wiki Page"><i class="fab fa-wikipedia-w"></i></a>
    
</div>
<div class="btn-group">  

    <?php
    $logedin_user = $this->Session->read("Auth.User.id");

    $likes = $this->Wiki->wiki_page_likes($wikipage['WikiPage']['id']);

    $like_posted = $this->Wiki->wiki_page_like_posted($logedin_user, $wikipage['WikiPage']['id']);

    if ($logedin_user == $wikipage['WikiPage']['user_id']) {
        ?>

        <a class="btn btn-xs btn-default tipText like_no_comment disabled" data-remote="" data-original-title="Likes">
            <i class="fa fa-thumbs-o-up"></i>&nbsp;
            <span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
        </a>
        <?php
    } else {
        ?>
        <a class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_page<?php } ?>" data-remote="<?php echo Router::Url(array("controller" => "wikies", "action" => "like_comment", $wikipage['WikiPage']['id']), true); ?>" data-original-title="Like Page"><i class="fa fa-thumbs-o-up"></i>&nbsp;<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span></a>
            <?php
        }
        ?>
<a title="Page Details" style="visibility: hidden;" data-remote="<?php echo $readmoreurl; ?>" id="openwikipage_open_<?php echo $wikipage['WikiPage']['id']; ?>" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" class="readmorepage btn btn-default btn-xs tipText" ><i class="fa fa-folder-open-o"></i></a>

</div>
