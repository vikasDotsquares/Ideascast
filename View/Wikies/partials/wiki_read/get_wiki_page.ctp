<?php
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
$type = (isset($type) && !empty($type)) ? $type : "read";
?>
<div class="panel-group" id="">
    <?php
    if (isset($allWikiPages) && !empty($allWikiPages)) {
        foreach ($allWikiPages as $wikipage) {
            ?>
            <div class="panel panel-default page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>">
                <div class="panel-heading bg-gray">
                    <h4 class="panel-title">
                        <a class="accordion-toggle page-accordion"  href="javascript:void(0);">
<!--                            <i class="indicator fa "></i>-->
                            <?php echo $wikipage['WikiPage']['title']; ?>
                        </a>
                       <div class="pull-right"><i  class="fa fa-long-arrow-up main-wiki tipText" title="Top"></i>&nbsp;<i class="fa  fa-long-arrow-left  onestepback tipText" title="Previous"  ></i></div>
                    </h4>
                </div>
                <div  class="wikipage">
                    <div class="panel-body">
                        <div class="idea-wiki-top-sec">
                            <?php
                            $selectionurl = SITEURL."wikies/create_wiki_page_linked/".$project_id."/".$this->Session->read("Auth.User.id")."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                            $contant_selection = null;
                            if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) {
                                $contant_selection = "contant_selection";
                            }
                            ?>
                            <div class="description <?php echo $contant_selection;?>" data-remote="<?php echo $selectionurl;?>" data-user-id="<?php echo $this->Session->read("Auth.User.id");?>" data-project-id="<?php echo $project_id;?>" data-wiki-id="<?php echo $wiki_id;?>" data-page-id="<?php echo $wikipage['WikiPage']['id'] ?>">
                           <?php
                           echo $wikipage['WikiPage']['description']
                           ?>
                        </div>
                        <?php
                        if(isset($wikipage['WikiPage']['user_id']) && $wikipage['WikiPage']['user_id'] == $this->Session->read('Auth.User.id')){
                            $is_full_permission_to_current_login = true;
                        }
                        ?>

                        <?php
                        $signoffparam = "wikies/wiki_page_sign_off/".$project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                        $signoffclass = 'wiki_page_signoff'; $signofftitle = "Sign Off";
                        if(isset($wikipage['WikiPage']['sign_off']) && $wikipage['WikiPage']['sign_off'] == 1){
                            $signoffclass = 'wiki_page_signedoff';$signofftitle = "Signed Off";
                        }
                        ?>
                        <div class="btn-group">




                        <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true && $wikipage['WikiPage']['sign_off'] != 1) { ?>
                            <a href="" data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="btn btn-xs btn-success tipText full_permission" data-remote="<?php echo SITEURL; ?>wikies/update_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $wikipage['WikiPage']['id']; ?>" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" ><i class="fa fa-pencil"></i></a>
                        <?php } else { ?>
                            <a data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="disabled btn btn-xs btn-success tipText not_full_permission"><i class="fa fa-pencil"></i></a>
                        <?php } ?>



                        <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true ) { ?>
                            <a href="" data-original-title="Delete Wiki Page" class="btn btn-xs btn-danger tipText delete_wiki_page full_permission" data-remote="<?php echo SITEURL; ?>wikies/delete_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $wikipage['WikiPage']['id']; ?>" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" data-user-id="<?php echo $wikipage['WikiPage']['user_id']; ?>"><i class="fa fa-trash"></i></a>
                        <?php } else { ?>
                            <a href="" data-original-title="Delete Wiki Page" class="btn btn-xs btn-danger disabled tipText not_full_permission" ><i class="fa fa-trash"></i></a>
                        <?php } ?>
                         <a class="btn btn-warning btn-xs tipText <?php echo $signoffclass;?>" data-remote="<?php echo SITEURL.$signoffparam;?>" title="<?php echo $signofftitle;?>"><i class="fa fa-sign-out"></i></a>

                        </div>
                        <div class="btn-group">

                            <a href="#wiki_page_all_detail_<?php echo $wikipage['WikiPage']['id']; ?>" data-toggle="collapse"  title="Wiki Page Details" class="tipText btn btn-xs btn-default wikipage collapsed">
                                <i class="page-details-show fa "></i>
                            </a>
<a href="#left_wiki_page_all_users_<?php echo $wikipage['WikiPage']['id']; ?>" data-toggle="collapse"  title="Wiki Page All User" class="tipText btn btn-xs btn-default">
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
                            </div>

                        </div>
                        <div>&nbsp; </div>
                        <div class="idea-wiki-bottom-sec">
                            <div class="collapse" id="left_wiki_page_all_users_<?php echo $wikipage['WikiPage']['id']; ?>">
                                <ul class="updateDetail">
                                <?php
                                $project_people = $this->requestAction(array("controller"=>"wikies","action"=>"project_people",$project_id));
                                $allwikipageusers = $this->Wiki->getWikiAllUserLists($project_id, $user_id,$wiki_id);

                                $allwikipageusers = array_merge($allwikipageusers,$project_people);
                                $allwikipageusers = array_unique($allwikipageusers);
                                if(isset($allwikipageusers) && !empty($allwikipageusers)){
                                    foreach($allwikipageusers as $userList){
                                        if(!empty($userList)){
                                        ?>
                                    <li>
                                        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $userList; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                            <i class="fa fa-user"></i>
                                        </a>
                                        <?php
                                        echo $this->Common->userFullname($userList).'</li>';
                                        }
                                    }
                                }else{
                                    echo "No User";
                                }
                                ?>
                                </ul>
                            </div>

                            <div class="collapse" id="wiki_page_all_detail_<?php echo $wikipage['WikiPage']['id']; ?>">
                                <ul class="updateDetail">
                                    <li>Created:
                                        <?php
                                    echo (isset($wikipage['WikiPage']['created']) && !empty($wikipage['WikiPage']['created'])) ? $this->Wiki->_displayDate(date("Y-m-d h:i:s",$wikipage['WikiPage']['created'])) : 'N/A';
                                        ?>
                                    </li>
                                    <li>Created by:
                                        <?php
                                        echo (isset($wikipage['WikiPage']['user_id']) && !empty($wikipage['WikiPage']['user_id'])) ? $this->Common->userFullname($wikipage['WikiPage']['user_id']) : 'N/A';
                                        ?>
                                    </li>
                                    <li id="updatedtext-<?php echo $wikipage['WikiPage']['id'];?>">Updated:
                                        <?php
                                        $updated = (isset($wikipage['WikiPage']['updated']) && isset($wikipage['WikiPage']['updated_user_id']) && $wikipage['WikiPage']['updated_user_id'] != null) ? $wikipage['WikiPage']['updated'] : '';
                                        echo (isset($updated) && !empty($updated)) ? $this->Wiki->_displayDate(date("Y-m-d h:i:s",$updated)) : 'N/A';
                                        ?>
                                    </li>
                                    <li id="updatedbytext-<?php echo $wikipage['WikiPage']['id'];?>">Updated by:
                                    <?php
                                    $wikiupdatedusername = (isset($wikipage['WikiPage']['updated_user_id']) && $wikipage['WikiPage']['updated_user_id'] != null) ? $this->Common->userFullname($wikipage['WikiPage']['updated_user_id']) : '';
                                    echo (isset($wikiupdatedusername) && $wikiupdatedusername != '') ? $wikiupdatedusername : 'N/A';
                                    ?>
                                    </li>
                                    <li id="signofftext-<?php echo $wikipage['WikiPage']['id'];?>">Sign Off:
                                    <?php
                                    echo (isset($wikipage['WikiPage']['sign_off']) && $wikipage['WikiPage']['sign_off'] == 0) ? 'NO' : 'Yes';
                                    ?>
                                    </li>
                                </ul>

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
.accordion-toggle.wiki-toggle {
  font-size: 13px;
}

.tabContentLeft .panel-title {
    font-size: 13px;
    padding-bottom: 0;
}
</style>