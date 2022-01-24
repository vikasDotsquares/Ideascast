<?php 
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
$current_user_id = $this->Session->read('Auth.User.id');

echo $this->Html->script(array(        
	'star-rating'
	));

echo $this->Html->css(
		array(
			 
			'star-rating'	
		)
);

?>

<div class="col-sm-8 col-md-8 col-lg-9 wiki-right-section">
    <div id="comments_list" class="tast-list-left-main">
        
        <?php
        $wikipagecomments = $this->requestAction(array("action" => "get_wiki_page_comment", $project_id, $user_id, $wiki_id));
        $wikipagedocuments = $this->Wiki->get_wiki_page_public_document($project_id, $user_id, $wiki_id);
        $commcount =  ( isset($wikipagecomments) && !empty($wikipagecomments) ) ? count($wikipagecomments) : 0;
        $docucount =  ( isset($wikipagedocuments) && !empty($wikipagedocuments) ) ? count($wikipagedocuments) : 0;
        ?>
        

        <div class="task-list-left-tabs">
            <ul class="nav nav-tabs comments">
                <li class="active">
                    <a aria-expanded="true" id="tab_comm" href="#comments" class="active" data-toggle="tab">All Comments <!--(<span id="commcount"><?php echo $commcount;?></span>) --></a>
                </li>
                <li class="">
                    <a aria-expanded="false" id="tab_docu" href="#pages" data-toggle="tab">All Pages <!--(<span id="docucount"><?php echo $docucount;?></span>)  --></a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="comments">
                    <ul class="people-list comment_list" id="wiki-update-comment-list">
                        <?php
                        if (isset($wikipagecomments) && !empty($wikipagecomments)) {
                            foreach ($wikipagecomments as $wikipagecomment) {
                                $documents = $wikipagecomment['WikiPageCommentDocument'];
                                ?>
                                <li id="page-comment-<?php echo $wikipagecomment['WikiPageComment']['id'];?>">
                                    <div class="comment-people-pic">
                                        <?php
                                        $user_data = $this->ViewModel->get_user_data($wikipagecomment['WikiPageComment']['user_id']);
                                        $pic = $user_data['UserDetail']['profile_pic'];
                                        $profiles = SITEURL . USER_PIC_PATH . $pic;
                                        $job_title = htmlentities($user_data['UserDetail']['job_title']);
                                        
                                        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                                        } else {
                                            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                                        }
                                        $html = '';
                                        if( $wikipagecomment['WikiPageComment']['user_id'] != $current_user_id ) {
                                                $html = CHATHTML($wikipagecomment['WikiPageComment']['user_id'],$project_id);
                                        }
                                        ?>
                                        <img src="<?php echo $profiles ?>" class="img-circledd pophover" align="left" data-content="<div><p><?php echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name']; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"  />

                                    </div>
                                    <div class="comment-people-info people-info">
                                        <h2><?php echo nl2br($wikipagecomment['WikiPageComment']['description']); ?></h2>
                                        <p class="doc-type">
                                            <?php
                                            if (isset($documents) && !empty($documents)) {
                                                foreach ($documents as $doc_key => $doc_val) {
                                                    $urlofdoc = SITEURL . WIKI_PAGE_DOCUMENT . $doc_val['document_name'];
                                                    //if(file_exists($urlofdoc)){
                                                    $ext = pathinfo($doc_val['document_name']);
                                                    ?>
                                                    <span class="dolist-document">
                                                        <span class="download_asset icon_btn icon_btn_sm icon_btn_teal">

                                                            <span class="icon_text"><?php echo $ext['extension']; ?></span>
                                                        </span>
                                                        <a class="tipText " href="<?php echo $urlofdoc; ?>" download="download" title="<?php echo$doc_val['document_name']; ?>">
                <?php echo $ext['filename']; ?>
                                                        </a>
                                                    </span>
                <?php
                //}
            }
        }
        ?>
                                        </p>
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
										
									<div class="rate_blog pull-right"> <input data-type="wiki" data-id="<?php echo $wikipagecomment['WikiPageComment']['id'];?>"  id="input-<?php echo $wikipagecomment['WikiPageComment']['id'];?>" value="<?php echo $wikipagecomment['WikiPageComment']['rating'];?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="xs" > </div>
										</div> 
                                    </div>
                                </li>
        <?php
    }
} else {
    ?>
                            <li class="text-center">No comments found!</li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="tab-pane fade" id="pages">
                    <ul class="people-list document_list" id="wiki-update-comment-list">
                        <div class="panel wiki-block panel-default page-collapse-<?php echo $project_wiki['Wiki']['id'] ?>" style="margin:0 0 5px 0" ">
                            <div class="panel-heading bg-gray noborder">
                                <h4 class="panel-title wiki-common-h4">
                                    <a class="accordion-toggle wiki-accordion collapsed" data-toggle="collapse" data-parent="#read-page-accordion" href="#wiki-collapse-<?php echo $project_wiki['Wiki']['id'] ?>">
                                        <i class="wikiicon fab fa-wikipedia-w"></i>
                                        <?php echo $project_wiki['Wiki']['title']; ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="wiki-collapse-<?php echo $project_wiki['Wiki']['id'] ?>" class="panel-collapse wiki-accordion collapse">
                                <div class="panel-body">
                                    <div class="idea-wiki-top-sec">
                                        <div class="description">
                                           <?php 
                                           echo $project_wiki['Wiki']['description'];
                                           ?> 
                                        </div>
                                    </div>

                                    <?php  
                                       if (( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) || (isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id")) ) {
                                    ?>    
                                        <a data-toggle="modal" data-target="#modal_create_wiki"  href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'update_wiki', $project_id,$this->Session->read('Auth.User.id'),$wiki_id, 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $wiki_id; ?>"  title="Edit Wiki" class="tipText btn btn-xs btn-success full_permission"><i class="fa fa-pencil"></i></a>
                                    <?php }else{?>
                                        <a href="" title="Edit Wiki" class="tipText disabled btn btn-xs btn-success not_full_permission"><i class="fa fa-pencil"></i></a>
                                    <?php } ?>
                                    <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true ) { ?>
                                        <a href="" data-original-title="Delete Wiki" class="btn btn-xs btn-danger tipText delete_wiki full_permission" data-remote="<?php echo SITEURL; ?>wikies/delete_wiki/<?php echo $project_id . '/' . $project_wiki['Wiki']['user_id'] . '/' . $wiki_id ; ?>" data-id="<?php echo $project_wiki['Wiki']['id']; ?>" data-user-id="<?php echo $project_wiki['Wiki']['user_id']; ?>"><i class="fa fa-trash"></i></a>
                                    <?php } else { ?>
                                        <a href="" data-original-title="Delete Wiki" class="btn btn-xs btn-danger disabled tipText not_full_permission" ><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                        <a class="btn btn-default btn-xs tipText gotowiki"  title="Wiki Details"><i class="fab fa-wikipedia-w"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <?php
                        $allWikiPages = $this->Wiki->getWikiPageLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id);
                        ?>

                        <?php
                        $params = array("allWikiPages" => $allWikiPages, "project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null, "user_project" => $user_project, "p_permission" => $p_permission, 'type' => 'comment');

                        echo $this->element('../Wikies/partials/wiki_admin/get_wiki_page', $params);
                        ?>
                    </ul>
                </div>
            </div>
        </div>




    </div>

</div>
<div class="col-sm-4 col-md-4 col-lg-3 wiki-left-section" >
    <div class="panel wiki-block panel-default page-collapse-<?php echo $project_wiki['Wiki']['id'] ?>" style="margin:0 0 5px 0">
        <div class="panel-heading bg-curious-Blue noborder">
            <h4 class="panel-title wiki-common-h4">
                <a class="accordion-toggle wiki-accordion collapsed" data-toggle="collapse" data-parent="#read-page-accordion" href="#admin-wiki-collapse-<?php echo $project_wiki['Wiki']['id'] ?>">
                    <i class="wikiicon fab fa-wikipedia-w"></i>
                    <?php echo $project_wiki['Wiki']['title']; ?>
                </a>
            </h4>
        </div>
        <div id="admin-wiki-collapse-<?php echo $project_wiki['Wiki']['id'] ?>" class="panel-collapse wiki-accordion collapse">
            <div class="panel-body">
                <div class="idea-wiki-top-sec">
                    <div class="description">
                       <?php 
                       echo $project_wiki['Wiki']['description'];
                       ?> 
                    </div>
                </div>

                <?php  
                //if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true){
                    if (( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) || (isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id")) ) {
                ?>    
                    <a data-toggle="modal" data-target="#modal_create_wiki"  href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'update_wiki', $project_id,$this->Session->read('Auth.User.id'),$wiki_id, 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $wiki_id; ?>"  title="Edit Wiki" class="tipText btn btn-xs btn-success full_permission"><i class="fa fa-pencil"></i></a>
                <?php }else{?>
                    <a href="" title="Edit Wiki" class="tipText disabled btn btn-xs btn-success not_full_permission"><i class="fa fa-pencil"></i></a>
                <?php } ?>
                    <a class="btn btn-default btn-xs tipText gotowiki"  title="Main Wiki Page"><i class="fab fa-wikipedia-w"></i></a>
            </div>
        </div>
    </div>
    <?php
    $allWikiPages = $this->Wiki->getWikiPageLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id);
    ?>

    <?php
    $params = array("allWikiPages" => $allWikiPages, "project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null, "user_project" => $user_project, "p_permission" => $p_permission, 'type' => 'comment');

    echo $this->element('../Wikies/partials/wiki_admin/get_wiki_page_by_user', $params);
    ?>


</div>
<div class="modal modal-success fade " id="wiki_page_comment_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<script type="text/javascript" >
    $(function(){
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
            $('.tooltip').hide()
        });

        $.wiki_sorting()
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
<style>
  .set-margn{ margin-top: 10px !important; width: 40%;float: left ; } 
</style>