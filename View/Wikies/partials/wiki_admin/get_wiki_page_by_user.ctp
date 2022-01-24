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
$type = (isset($type) && !empty($type)) ? $type : "admin";
?>
<div class="panel-group"  id="<?php echo $type;?>-page-accordion-wiki">
    <?php
    if (isset($allWikiPages) && !empty($allWikiPages)) {
        foreach ($allWikiPages as $k => $wikipage) {
            $wiki_page_id  = $wikipage['WikiPage']['id'];
            ?>
            <div class="panel panel-default wiki-block page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>" data-order="<?php echo ($k+1); ?>" data-wid="<?php echo $wiki_page_id; ?>">
                <div class="panel-heading bg-curious-Blue">
                    <h4 class="panel-title wiki-common-h4">
                        <a class="accordion-toggle page-accordion collapsed"  id="comment-id-<?php echo $wikipage['WikiPage']['id'] ?>" data-toggle="collapse" data-parent="#<?php echo $type;?>-page-accordion-wiki" href="#<?php echo $type;?>-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>">
                           <!-- <i class="indicator fa "></i> -->
                            <?php echo $wikipage['WikiPage']['title']; //echo '  =>'.$wikipage['WikiPage']['sort_order']; ?>
                        </a>
                    </h4>
                </div>
                <div id="<?php echo $type;?>-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>" class="panel-collapse collapse">
                    <div class="panel-body nopadding">
                        <div class="idea-wiki-top-sec" style="position: relative;">
                            
                        <?php 
                        $commentremote = "wikies/get_wiki_page_comment_admin/".$project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                        $documentremote = "wikies/get_wiki_page_document_admin/".$project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                        
                        
                        $commentcount = $this->requestAction(array("action" => "get_wiki_page_comment_admin_count", $project_id, $user_id, $wiki_id,$wikipage['WikiPage']['id']));
                        $documentcount = $this->requestAction(array("action" => "get_wiki_page_document_admin_count", $project_id, $user_id, $wiki_id,$wikipage['WikiPage']['id']));
                        
                        
                        
                        
                        ?>
                        <?php
                        $users = $this->Wiki->get_all_wiki_user_admin($wikipage['WikiPage']['wiki_id']);
                        if(isset($users) && !empty($users)){
                            foreach($users as $key => $user){
                                $commentremote_user = SITEURL . "wikies/get_wiki_page_comment_by_user_admin/".$project_id."/".$key."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                                $documentremote_user = SITEURL . "wikies/get_wiki_page_document_by_user_admin/".$project_id."/".$key."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                                
                                $commentcount_user = $this->requestAction(array("action" => "get_wiki_page_comment_by_user_admin_count", $project_id, $key, $wiki_id,$wikipage['WikiPage']['id']));
                                $documentcount_user = $this->requestAction(array("action" => "get_wiki_page_document_by_user_admin_count", $project_id, $key, $wiki_id,$wikipage['WikiPage']['id']));
                        
                        
                        ?>
                            <?php
                            $user_data = $this->ViewModel->get_user_data($key);
                            $pic = $user_data['UserDetail']['profile_pic'];
                            $profiles = SITEURL . USER_PIC_PATH . $pic;
                            $job_title = htmlentities($user_data['UserDetail']['job_title']);
                            if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                                $profiles = SITEURL . USER_PIC_PATH . $pic;
                            } else {
                                $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                            }
                            $html = '';
                                        if( $key != $current_user_id ) {
                                                $html = CHATHTML($key,$project_id);
                                        }
                            ?>

                            <div class="panel-body  border-bottom">
                                <div class="comment-people-pic">
                                    <img  align="left" data-content="<div><p><?php echo $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name']; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"   class="img-circledd pophover" src="<?php echo $profiles ?>" >
                                </div>
                                
                                <ul class="list-inline">
                                    <?php
                                    $page_admin_comment = '';
                                    if(is_array($commentcount_user) && isset($commentcount_user) && !empty($commentcount_user) && count($commentcount_user) >= 1 ){
                                        $page_admin_comment = 'page_admin_comment';
                                        
                                    }
                                    ?>
                                    <li>
                                        <a style="cursor: pointer;" title="User Comment"  data-remote="<?php echo $commentremote_user;?>" data-count="<?php echo $commentcount_user;?>"  data-user-id="<?php echo $key; ?>" class="<?php echo $page_admin_comment;?> tipText btn btn-xs btn-default"><i class="fa fa-comments"></i>&nbsp;<?php echo $commentcount_user;?></a>
                                    </li>
<!--                                    <li><a style="cursor: pointer;" data-remote="<?php echo $documentremote_user; ?>" data-count="<?php echo $documentcount_user;?>"  data-user-id="<?php echo $key; ?>" class=" btn btn-xs btn-default page_admin_document"><i class="fa fa-folder-o"></i>&nbsp;<?php echo $documentcount_user;?></a></li>-->

                                </ul>
                                <h5 class="bh_head"><?php echo $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name']; ?></h5>
                            </div>
                        <?php
                            }
                        }
                        ?>
                            <div class="btn-group pull-right adminbtn" style="width:auto;position: absolute; top: 7px; right: 7px;">
                          
                        <?php
                        if(is_array($commentcount) && isset($commentcount) && !empty($commentcount) && count($commentcount) >= 1 && count($users) > 1){
                        ?>
                        
                        <a  id="get-comment-id-<?php echo $wikipage['WikiPage']['id'] ?>" title="Page Comments" data-count="<?php echo $commentcount;?>" data-remote="<?php echo SITEURL.$commentremote;?>" class="tipText btn btn-xs btn-default admin_wikipagecomments"><i class="fa fa-comments"></i><!-- &nbsp;(<?php //echo $commentcount;?>) -->
                        </a>
                        <?php } ?>
                      <!--  <a title="Page Documents" data-count="<?php echo $documentcount;?>" data-remote="<?php echo SITEURL.$documentremote;?>" class="tipText btn btn-xs btn-default admin_wikipagedocuments"><i class="fa fa-folder-o"></i>&nbsp;(<?php //echo $documentcount;?>)</a>
                        
                        <a class="btn btn-default btn-xs tipText gotowiki"  title="Main Wiki Page"><i class="fa fa-wikipedia-w"></i></a>-->
                        
                        
                        </div>
                        
                        </div>
                        
                       
                        
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="text-center "> No wiki page found! <!--<a href="" class="backtoread" >  Back</a>--></div>
        <?php
    }
    ?>
</div>

<script type="text/javascript" >
    $(function ($) {
        //$('body').delegate('.backtoread', 'click', function(event) {
//        $(".backtoread").click(function (e) {
//            e.preventDefault();
//            $('.nav .active a').trigger("click")
//        })
    })
</script>
<style>
    .list-inline > li{
        padding-left:0px;
    }
</style>
<script type="text/javascript" >
    $(function ($) {
       //$("body").delegate(".wikipagecomments", 'click', function (e) {
        $(".admin_wikipagecomments").click(function (e) {
            e.preventDefault();
            var $current = $(this), actionURL = $current.data("remote"), count = $current.data("count");
            
            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                global:false,
                data: {},
                beforeSend: function () {
                    //$(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    $(".admin_wikipagecomments").removeClass("active");
                    $current.addClass("active");
                    $('.tooltip').hide()
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $("#tab_comm").trigger("click");
                    $(".comment_list").html(response);
                    $("#commcount").html(count);
                }
            });
            return;
        });
        $(".page_admin_comment").click(function (e) {
            e.preventDefault();
            var $current = $(this), actionURL = $current.data("remote"), count = $current.data("count");
            
            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                global:false,
                data: {},
                beforeSend: function () {
                    //$(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    //$(".admin_wikipagecomments").removeClass("active");
                    //$current.addClass("active");
                    $('.tooltip').hide()
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $("#tab_comm").trigger("click");
                    $(".comment_list").html(response);
                    $("#commcount").html(count);
                }
            });
            return;
        });
        $(".admin_wikipagedocuments").click(function (e) {
            e.preventDefault();
            var $current = $(this), actionURL = $current.data("remote"), count = $current.data("count");
            
            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                global:false,
                data: {},
                beforeSend: function () {
                    //$(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    $(".admin_wikipagedocuments").removeClass("active");
                    $current.addClass("active");
                    $('.tooltip').hide()
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $("#tab_docu").trigger("click");
                    $(".document_list").html(response);
                    $("#docucount").html(count);
                }
            });
            return;
        });
        $(".page_admin_document").click(function (e) {
            e.preventDefault();
            var $current = $(this), actionURL = $current.data("remote"), count = $current.data("count");
            
            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                global:false,
                data: {},
                beforeSend: function () {
                    //$(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    //$(".admin_wikipagedocuments").removeClass("active");
                    //$current.addClass("active");
                    $('.tooltip').hide()
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $("#tab_docu").trigger("click");
                    $(".document_list").html(response);
                    $("#docucount").html(count);
                }
            });
            return;
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