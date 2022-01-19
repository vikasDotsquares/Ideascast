<!--<ul class="list-group list-group-root  ">
<?php
$wikicreateuser = null;
if (isset($allWikiUsers) && !empty($allWikiUsers)) {
    ?>
    <?php
    foreach ($allWikiUsers as $user) {
        $user = $user['Wiki'];
        $wikicreateuser = $user['user_id'];
        $udetail = $this->Common->UserDetail($user['user_id']);
        $ud = $this->ViewModel->get_user($user['user_id']);
        ?>
                    <div class="row row-pep  ">
                        <div class="col-sm-2">
        <?php
        echo $this->Html->image($this->Common->get_profile_pic($user['user_id']), array("", "class" => "tipText", "title" => $this->Common->userFullname($user['user_id'])));
        ?>
                        </div>
                        <div class="col-sm-8 user-detail">
                            <p class="user_name"><?php echo 'Wiki Creator'; ?> : <?php echo $this->Common->userFullname($user['user_id']); ?></p>
                            <p><?php echo $ud['User']['email']; ?></p><p><?php echo $udetail['UserDetail']['job_role']; ?></p>
                        </div>
                        
                    </div><?php
    }
}
?>
<?php
$allWikiPageUsers = null;
if (isset($allWikiPageUsers) && !empty($allWikiPageUsers)) {
    ?>
    <?php
    foreach ($allWikiPageUsers as $user) {
        $user = $user['WikiPage'];
        $udetail = $this->Common->UserDetail($user['user_id']);
        $ud = $this->ViewModel->get_user($user['user_id']);
        ?>
                    <div class="row row-pep  ">
                        <div class="col-sm-2">
        <?php
        echo $this->Html->image($this->Common->get_profile_pic($user['user_id']), array("", "class" => "tipText", "title" => $this->Common->userFullname($user['user_id'])));
        ?>
                        </div>
                        <div class="col-sm-8 user-detail">
                            <p class="user_name"><?php echo 'Page Creator'; ?> : <?php echo $this->Common->userFullname($user['user_id']); ?></p>
                            <p><?php echo $ud['User']['email']; ?></p><p><?php echo $udetail['UserDetail']['job_role']; ?></p>
                        </div>
                        
                    </div><?php
    }
}
?>
<?php
$allWikiPageCommentDocumentUsers = null;
if (isset($allWikiPageCommentDocumentUsers) && !empty($allWikiPageCommentDocumentUsers)) {
    ?>
    <?php
    foreach ($allWikiPageCommentDocumentUsers as $user) {
        $user = $user['WikiPageCommentDocument'];
        $udetail = $this->Common->UserDetail($user['user_id']);
        $ud = $this->ViewModel->get_user($user['user_id']);
        ?>
                    <div class="row row-pep  ">
                        <div class="col-sm-2">
        <?php
        echo $this->Html->image($this->Common->get_profile_pic($user['user_id']), array("", "class" => "tipText", "title" => $this->Common->userFullname($user['user_id'])));
        ?>
                        </div>
                        <div class="col-sm-8 user-detail">
                            <p class="user_name"><?php echo 'Document Uploader'; ?> : <?php echo $this->Common->userFullname($user['user_id']); ?></p>
                            <p><?php echo $ud['User']['email']; ?></p><p><?php echo $udetail['UserDetail']['job_role']; ?></p>
                        </div>
                        
                    </div><?php
    }
}
?>
<?php
if (isset($users) && !empty($users)) {
    ?>
    <?php
    foreach ($users as $user) {
        if ($user != $wikicreateuser) {
            $udetail = $this->Common->UserDetail($user);
            $ud = $this->ViewModel->get_user($user);
            ?>
                        <div class="row row-pep  ">
                            <div class="col-sm-2">
            <?php
            echo $this->Html->image($this->Common->get_profile_pic($user), array("", "class" => "tipText", "title" => $this->Common->userFullname($user)));
            ?>
                            </div>
                            <div class="col-sm-8 user-detail">
                                <p class="user_name"><?php echo 'Assigned'; ?> : <?php echo $this->Common->userFullname($user); ?></p>
                                <p><?php echo $ud['User']['email']; ?></p><p><?php echo $udetail['UserDetail']['job_role']; ?></p>
                            </div>
                            
                        </div>
            <?php
        }
    }
}
?>
</ul>

<style> 
    .row-pep {
        border-top: 1px solid #ccc;
        margin-top: 5px;
        padding-top: 5px;
    }
    .row-pep:first-child {
        border-top: none;
    }

</style>-->

<?php
$project_people = $this->requestAction(array("action" => "get_all_user_on_this_wiki_count", $project_id, $this->Session->read("Auth.User.id"), $wiki_id));
$allwikipageusers = $this->Wiki->getWikiAllUserLists($project_id, $this->Session->read("Auth.User.id"), $wiki_id);
$allwikipageusers = array_merge($allwikipageusers, $project_people);
$allwikipageusers = array_unique($allwikipageusers);
?>

  
    <?php if (isset($allwikipageusers) && !empty($allwikipageusers)) { ?>
        <?php foreach ($allwikipageusers as $key => $val) { ?>

            <?php
            $userDetail = $this->ViewModel->get_user_data($val);
            $user = $this->ViewModel->get_user($val);

            if (isset($userDetail) && !empty($userDetail)) {
                $user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];

                $profile_pic = $userDetail['UserDetail']['profile_pic'];

                if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                    $profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
                } else {
                    $profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
                }
                ?>
                <div class="row">
                    <div class="col-sm-2">
                        <img class="myaccountPic" alt="Logo Image" style="width: 80%"  src="<?php echo $profilesPic ?>" alt="Profile Image" />
                    </div>
                    <div class="col-sm-8 user-detail">
                        <p class="user_name">Owner: <?php echo $user_name; ?></p>
                        <p><?php echo $user['User']['email']; ?></p> 
						<?php if( !empty(trim($userDetail['UserDetail']['org_name'])) ){?>
						<p><span class="ucompany">Organization: </span><?php echo $userDetail['UserDetail']['org_name']; ?></p><?php } ?><p><span class="jobrole">Role: </span><?php 
							echo ( isset($userDetail['UserDetail']['job_role']) && !empty($userDetail['UserDetail']['job_role']) && strlen(trim($userDetail['UserDetail']['job_role'])) > 0 )? trim($userDetail['UserDetail']['job_role']) : 'Not Given'; 
							?></p>
						
						
                    </div>
                    <div class="col-sm-2">
                            <!-- <i class="fa fa-comment"></i> -->
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    <?php } else { ?>	
        <div class="text-center bold padding">No User found!</div>
    <?php } ?>
        <style>
            .bg-green .modal-body .rempvelastbrdr:last-child{
                -moz-border-bottom-colors: none !important;
                -moz-border-left-colors: none !important;
                -moz-border-right-colors: none !important;
                -moz-border-top-colors: none !important;
                border-color: #dddddd !important;
                border-image: none !important;
                border-style: none !important;
                border-width: 0 0 0px !important;
            }
        </style>
<script type="text/javascript" >
    $(function () {
        $('#modal_medium').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        });

    });


</script>