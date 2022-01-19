<?php 
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
?>

<?php
$commentparam = "wikies/add_wiki_page_comments/" . $project_id . "/" . $this->Session->read('Auth.User.id') . "/" . $wiki_id."/".$wiki_page_id;
?>

<div class=" left-main-header clear-box">
    <h5 class="pull-left">Comments</h5>
    <a class="btn btn-sm btn-warning pull-right addcomments" data-remote="<?php echo SITEURL . $commentparam; ?>" data-target="#wiki_page_comment_model" data-toggle="modal" data-id="1"><i class="fa fa-plus"></i>&nbsp;Add Comment or Update a file</a>
</div>

<div class="task-list-left-tabs">
    <ul class="nav nav-tabs comments">
        <li class="active">
            <a aria-expanded="true" href="#all" class="active" data-toggle="tab">All</a>
        </li>
        <li class="">
            <a aria-expanded="false" href="#people" data-toggle="tab">People</a>
        </li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade active in" id="all">
            <ul class="people-list" id="wiki-update-comment-list">
                <?php 
                if(isset($wikipagecomments) && !empty($wikipagecomments)){
                    foreach($wikipagecomments as $wikipagecomment){
                        $documents =  $wikipagecomment['WikiPageCommentDocument'];
						$current_org = $this->Permission->current_org();
						$current_org_other = $this->Permission->current_org($wikipagecomment['WikiPageComment']['user_id']);
                ?>
                        <li id="page-comment-<?php echo $wikipagecomment['WikiPageComment']['id'];?>">
                                <div class="comment-people-pic">
                                <?php
                                    $user_data = $this->ViewModel->get_user_data($wikipagecomment['WikiPageComment']['user_id']);
                                    $pic = $user_data['UserDetail']['profile_pic'];
                                    $profiles = SITEURL . USER_PIC_PATH . $pic;

                                    if(!empty($pic) && file_exists(USER_PIC_PATH.$pic)){
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                    }else{
                                            $profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
                                    }
                                ?>
                                <img src="<?php echo $profiles ?>" class="img-circledd tipText" title="<?php echo htmlentities($user_data['UserDetail']['first_name']) . ' ' .htmlentities($user_data['UserDetail']['last_name']); ?>" alt="Personal Image" />
								<?php  if($current_org !=$current_org_other){ ?>
										<i class="communitygray18 team-meb-com tipText" title="" data-original-title="Not In Your Organization"></i>
								<?php } ?>

                                </div>
                                <div class="comment-people-info people-info">
                                    <h2><?php echo nl2br($wikipagecomment['WikiPageComment']['description']); ?></h2>
                                    <p class="doc-type">
                                        <?php 
                                        if(isset($documents) && !empty($documents)){
                                            foreach($documents as $doc_key =>$doc_val){
                                                $urlofdoc = SITEURL.DO_LIST_BLOG_DOCUMENTS.$doc_val['document_name'];
                                                //if(file_exists($urlofdoc)){
                                                    $ext = pathinfo($doc_val['document_name']);
                                                
                                        ?>
                                            <span class="dolist-document">
                                                <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                    <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                </span>
                                                <a class="tipText " href="<?php echo $urlofdoc;?>" download="download" title="<?php echo$doc_val['document_name'];?>">
                                                    <?php  echo $ext['filename'];?>
                                                </a>
                                            </span>
                                        <?php
                                                //}
                                            }
                                        }
                                        ?>
                                    </p>
                                    <p class="created-date">
                                        <?php 
                                        echo _displayDate($wikipagecomment['WikiPageComment']['created']);
                                        $wiki_page_id = $wikipagecomment['WikiPageComment']['wiki_page_id'];
                                        $comment_id = $wikipagecomment['WikiPageComment']['id'];
                                        ?> 							
                                        
                                        <?php
                                        $logedin_user = $this->Session->read("Auth.User.id");

                                        $likes = $this->Wiki->wiki_page_comment_likes($wikipagecomment['WikiPageComment']['id']);

                                        $like_posted = $this->Wiki->wiki_page_comment_like_posted($logedin_user, $wikipagecomment['WikiPageComment']['id']);

                                        if ($logedin_user == $wikipagecomment['WikiPageComment']['user_id']) {
                                            ?>

                                            <a class="btn btn-xs btn-default tipText like_no_comment" data-remote="" data-original-title="Likes">
                                                <i class="fa fa-thumbs-o-up"></i>&nbsp;
                                                <span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span>
                                            </a>
                                            <?php
                                        } else {
                                            ?>
                                            <a class="btn btn-xs btn-default tipText <?php if ($like_posted) { ?>disabled<?php } else { ?>like_comment<?php } ?>" data-remote="<?php echo Router::Url(array("controller" => "wikies", "action" => "wiki_page_comment_like", $wikipagecomment['WikiPageComment']['id']), true); ?>" data-original-title="Like comment"><i class="fa fa-thumbs-o-up"></i>&nbsp;<span class="label bg-purple"><?php echo ($likes) ? $likes : 0; ?></span></a>
                                            <?php
                                        }
                                        ?>
                                        
                                        
                                        <?php 
                                        if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login == true){
                                        ?>

                                        <a data-target="#wiki_page_comment_model" data-original-title="Edit comment" data-toggle="modal" class="btn btn-xs btn-default tipText" data-remote="<?php echo SITEURL . "wikies/edit_wiki_page_comments/" . $project_id . "/" . $this->Session->read("Auth.User.id") . "/" . $wiki_id . "/" . $wiki_page_id . "/" . $comment_id; ?>" href="#">
                                                <i class="fa fa-pencil"></i>
                                        </a>
                                        <a data-original-title="Delete comment" class="btn btn-xs btn-danger tipText delete_comment" data-remote="<?php echo SITEURL . "wikies/wiki_page_comment_delete/" . $project_id . "/" . $this->Session->read("Auth.User.id") . "/" . $wiki_id . "/" . $wiki_page_id . "/" . $comment_id; ?>"><i class="fa fa-trash"></i>
                                        </a>
                                        <?php
                                        }
                                        ?>
                                    </p>
                                </div>
                            </li>
                <?php
                    }
                }else{
                ?>
                    <li>Select <b>Wiki page </b>to view comments here</li>
                <?php
                }
                ?>
            </ul>
        </div>
        <div class="tab-pane fade" id="people">
            <?php
            $users = $this->Wiki->get_all_wiki_users($wiki_id);
            $remote = SITEURL."wikies/get_wiki_page_comment_by_user/".$project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id."/".$wiki_page_id;
            echo $this->Form->input("user_id",array("id"=>"people_change","multiple"=>true,"type"=>"select","options"=>$users,"div"=>false,"label"=>false,"class"=>"form-control aqua","data-wiki-page-id"=>$wiki_page_id,"data-remote"=>$remote))
            ?>

            <ul class="people-list" id="wiki-update-comment-list">

            </ul>

        </div>
    </div>

    <a class="btn btn-sm btn-default pull-right addcomments" data-remote="<?php echo SITEURL . $commentparam; ?>" data-target="#wiki_page_comment_model" data-toggle="modal" data-id="1">Add Comment or Update a file</a>
</div>
<script type="text/javascript" >
    $(function(){
		
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
            $('.tooltip').hide()
        });

        $('#people_change').multiselect({
            maxHeight: '400',
            buttonWidth: '100%',
            buttonClass: 'btn btn-info',
            // checkboxName: 'data[DoListUser][user_id][]',
            enableFiltering: true,
            filterBehavior: 'text',
            includeFilterClearBtn: true,
            enableCaseInsensitiveFiltering: true,
            // numberDisplayed: 3,
            includeSelectAllOption: true,
            includeSelectAllIfMoreThan: 5,
            selectAllText: ' Select all',
            // disableIfEmpty: true
            onInitialized: function() {

            },
            onChange: function(element, checked) {

            }
        });
    })

</script>