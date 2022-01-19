<?php 
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"),1);
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
echo $this->Html->script(array(        
	'star-rating'
	));

echo $this->Html->css(
		array(
			 
			'star-rating'	
		)
);
?>
<?php
$wikipagedetail = $this->Wiki->get_wiki_page_detail($wiki_page_id);
if(isset($wikipagedetail['WikiPage']['sign_off']) && $wikipagedetail['WikiPage']['sign_off'] == 1){
    $is_full_permission_to_current_login = false;
}
$current_user_id = $this->Session->read('Auth.User.id');
?>
<?php
$commentparam = "wikies/add_wiki_page_comments/" . $project_id . "/" . $this->Session->read('Auth.User.id') . "/" . $wiki_id."/".$wiki_page_id;
?>

<div class=" left-main-header clear-box">
     
    <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) { ?>        
        <a class="btn btn-sm btn-default pull-right addcomments" data-remote="<?php echo SITEURL . $commentparam; ?>" data-target="#wiki_page_comment_model" data-toggle="modal" data-id="1">Add Comment or Update a file</a>
    <?php } else { ?>
        <a class="btn btn-sm btn-default pull-right disabled_comment_list"  >Add Comment or Update a file</a>
    <?php } ?>
    
    
</div>

<div class="task-list-left-tabs">
    <ul class="nav nav-tabs comments">
        <li class="active">
            <a aria-expanded="true" href="#all" class="active" data-toggle="tab">All</a>
        </li>
        <li class="">
            <a aria-expanded="false" href="#people" data-toggle="tab">Team Members</a>
        </li>
		<li class="">
			<a aria-expanded="false" href="#relevance" data-toggle="tab">Popular</a>
		</li>
    </ul>
    <div id="myTabContent" class="tab-content">
		<div class="tab-pane fade" id="relevance">
            <ul class="people-list" id="wiki-update-comment-list">
                <?php 
                if(isset($wikipagecomments_rating) && !empty($wikipagecomments_rating)){
                    foreach($wikipagecomments_rating as $wikipagecomment){
                        $documents =  $wikipagecomment['WikiPageCommentDocument'];
                ?>
                        <li id="page-comment-<?php echo $wikipagecomment['WikiPageComment']['id'];?>">
                                <div class="comment-people-pic">
                                <?php
                                    $user_data = $this->ViewModel->get_user_data($wikipagecomment['WikiPageComment']['user_id']);
                                    $pic = $user_data['UserDetail']['profile_pic'];
                                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                                    $job_title = htmlentities($user_data['UserDetail']['job_title']);
                                    if(!empty($pic) && file_exists(USER_PIC_PATH.$pic)){
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                    }else{
                                            $profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
                                    }
                                    $html = '';
                                    if( $wikipagecomment['WikiPageComment']['user_id'] != $current_user_id ) {
                                            $html = CHATHTML($wikipagecomment['WikiPageComment']['user_id'], $project_id);
                                    }
                                ?>
                                <img src="<?php echo $profiles ?>" class="img-circledd pophover" align="left" data-content="<div><p><?php echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name']; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" />

                                </div>
                                <div class="comment-people-info people-info">
                                    <h2><?php echo nl2br($wikipagecomment['WikiPageComment']['description']); ?></h2>
                                    <p class="doc-type">
                                        <?php 
                                        if(isset($documents) && !empty($documents)){
                                            foreach($documents as $doc_key =>$doc_val){
                                                $urlofdoc = SITEURL.WIKI_PAGE_DOCUMENT.$doc_val['document_name'];
                                                //if(file_exists($urlofdoc)){
                                                    $ext = pathinfo($doc_val['document_name']);
                                                
                                        ?>
                                            <span class="dolist-document">
                                                <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                    <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                </span>
                                                <a class="tipText " href="<?php echo $urlofdoc;?>" download="download" title="<?php echo $doc_val['document_name'];?>">
                                                    <?php  echo $ext['filename'];?>
                                                </a>
                                            </span>
                                        <?php
                                                //}
                                            }
                                        }
                                        ?>
                                    </p>
                                    <?php 
                                    if(isset($wikipagecomment['WikiPageComment']['user_id']) && $wikipagecomment['WikiPageComment']['user_id'] == $this->Session->read('Auth.User.id')){
                                        $is_full_permission_to_current_login = true;
                                    }
                                    ?>
									<div class="clearfix">
                                    <p class="created-date">
                                        <?php  
                                        echo $this->Wiki->_displayDate($wikipagecomment['WikiPageComment']['updated']);
                                        $wiki_page_id = $wikipagecomment['WikiPageComment']['wiki_page_id'];
                                        $comment_id = $wikipagecomment['WikiPageComment']['id'];
                                        ?> 							
                                        
                                        <?php
                                        $logedin_user = $this->Session->read("Auth.User.id");

                                        $likes = $this->Wiki->wiki_page_comment_likes($wikipagecomment['WikiPageComment']['id']);

                                        $like_posted = $this->Wiki->wiki_page_comment_like_posted($logedin_user, $wikipagecomment['WikiPageComment']['id']);

                                        if ($logedin_user == $wikipagecomment['WikiPageComment']['user_id']) {
                                            ?>

                                            <a class="btn btn-xs btn-default tipText like_no_comment disabled" data-remote="" data-original-title="Likes">
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
                                       // if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login == true){
                                        
                                        if ($logedin_user == $wikipagecomment['WikiPageComment']['user_id']) {
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
										
									<div class="rate_blog pull-right"> <input disabled data-id="<?php echo $wikipagecomment['WikiPageComment']['id'];?>"  id="input-<?php echo $wikipagecomment['WikiPageComment']['id'];?>" value="<?php echo $wikipagecomment['WikiPageComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>
										</div> 
                                </div>
                            </li>
                <?php
                    }
                }else{
                ?>
                    <li class="text-center">No comments found!</li>
                <?php
                }
                ?>
            </ul>
        </div>
	
	
        <div class="tab-pane fade active in" id="all">
            <ul class="people-list" id="wiki-update-comment-list">
                <?php 
                if(isset($wikipagecomments) && !empty($wikipagecomments)){
                    foreach($wikipagecomments as $wikipagecomment){
                        $documents =  $wikipagecomment['WikiPageCommentDocument'];
                ?>
                        <li id="page-comment-<?php echo $wikipagecomment['WikiPageComment']['id'];?>">
                                <div class="comment-people-pic">
                                <?php
                                    $user_data = $this->ViewModel->get_user_data($wikipagecomment['WikiPageComment']['user_id']);
                                    $pic = $user_data['UserDetail']['profile_pic'];
                                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                                    $job_title = htmlentities($user_data['UserDetail']['job_title']);
                                    if(!empty($pic) && file_exists(USER_PIC_PATH.$pic)){
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                    }else{
                                            $profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
                                    }
                                    $html = '';
                                    if( $wikipagecomment['WikiPageComment']['user_id'] != $current_user_id ) {
                                            $html = CHATHTML($wikipagecomment['WikiPageComment']['user_id']);
                                    }
                                ?>
                                <img src="<?php echo $profiles ?>" class="img-circledd pophover" align="left" data-content="<div><p><?php echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name']; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" />

                                </div>
                                <div class="comment-people-info people-info">
                                    <h2><?php echo nl2br($wikipagecomment['WikiPageComment']['description']); ?></h2>
                                    <p class="doc-type">
                                        <?php 
                                        if(isset($documents) && !empty($documents)){
                                            foreach($documents as $doc_key =>$doc_val){
                                                $urlofdoc = SITEURL.WIKI_PAGE_DOCUMENT.$doc_val['document_name'];
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
                                    <?php 
                                    if(isset($wikipagecomment['WikiPageComment']['user_id']) && $wikipagecomment['WikiPageComment']['user_id'] == $this->Session->read('Auth.User.id')){
                                        $is_full_permission_to_current_login = true;
                                    }
                                    ?>
									<div class="clearfix">
                                    <p class="created-date set-margn">
                                        <?php 
                                        echo $this->Wiki->_displayDate($wikipagecomment['WikiPageComment']['updated']);
                                        $wiki_page_id = $wikipagecomment['WikiPageComment']['wiki_page_id'];
                                        $comment_id = $wikipagecomment['WikiPageComment']['id'];
                                        ?> 							
                                        
                                        <?php
                                        $logedin_user = $this->Session->read("Auth.User.id");

                                        $likes = $this->Wiki->wiki_page_comment_likes($wikipagecomment['WikiPageComment']['id']);

                                        $like_posted = $this->Wiki->wiki_page_comment_like_posted($logedin_user, $wikipagecomment['WikiPageComment']['id']);

                                        if ($logedin_user == $wikipagecomment['WikiPageComment']['user_id']) {
                                            ?>

                                            <a class="btn btn-xs btn-default tipText like_no_comment disabled" data-remote="" data-original-title="Likes">
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
                                       // if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login == true){
                                        
                                        if ($logedin_user == $wikipagecomment['WikiPageComment']['user_id']) {
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
										
									<div class="rate_blog pull-right"> <input disabled data-id="<?php echo $wikipagecomment['WikiPageComment']['id'];?>"  id="input-<?php echo $wikipagecomment['WikiPageComment']['id'];?>" value="<?php echo $wikipagecomment['WikiPageComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>
										</div> 
                                </div>
                            </li>
                <?php
                    }
                }else{
                ?>
                    <li class="text-center">No comments found!</li>
                <?php
                }
                ?>
            </ul>
        </div>
		
        <div class="tab-pane fade" id="people">
		<div class="col-sm-6">
            <?php
            $users = $this->Wiki->get_all_wiki_users($wiki_id);
            $remote = SITEURL."wikies/get_wiki_page_comment_by_user/".$project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id."/".$wiki_page_id;
            echo $this->Form->input("user_id",array("id"=>"people_change","multiple"=>true,"type"=>"select","options"=>$users,"div"=>false,"label"=>false,"class"=>"form-control aqua","data-wiki-page-id"=>$wiki_page_id,"data-remote"=>$remote))
            ?>

		</div>	
            <ul class="people-list" id="wiki-update-comment-list">

            </ul>
			

        </div>
    </div>

    <?php 
    if(isset($wikipagedetail['WikiPage']['sign_off']) && $wikipagedetail['WikiPage']['sign_off'] == 1){
        $is_full_permission_to_current_login = false;
    }
    if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) { ?>        
        <a class="btn btn-sm btn-default  addcomments" data-remote="<?php echo SITEURL . $commentparam; ?>" data-target="#wiki_page_comment_model" data-toggle="modal" data-id="1">Add Comment or Update a file</a>
    <?php } else { ?>
        <a class="btn btn-sm btn-default disabled_comment_list"  >Add Comment or Update a file</a>
    <?php } ?>
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
<style>.set-margn{ margin-top: 10px !important; width: 40%;float: left ; }</style>