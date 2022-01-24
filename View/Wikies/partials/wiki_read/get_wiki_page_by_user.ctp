<?php
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}

$type = (isset($type) && !empty($type)) ? $type : "";
?>
<div class="panel-group" id="<?php echo $type;?>-page-accordion">
    <?php //echo count($allWikiPages);?>
    <?php
    if (isset($allWikiPages) && !empty($allWikiPages)) {
        foreach ($allWikiPages as $wikipage) {
            ?>
            <div class="panel panel-default wiki-block page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>">
                <div class="panel-heading bg-gray">
                    <h4 class="panel-title wiki-common-h4">
                        <a id="page_select_default_<?php echo $wikipage['WikiPage']['id'];?>" aria-expanded=false class="accordion-toggle page-accordion collapsed openwikipage_anchor" data-id="<?php echo $wikipage['WikiPage']['id']; ?>" data-toggle="collapse" data-parent="#<?php echo $type;?>-page-accordion" href="#<?php echo $type;?>-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>">
                            <i class="indicator fa "></i>
                            <?php echo $wikipage['WikiPage']['title']; ?>
                        </a>
                    </h4>
                </div>
                <div id="<?php echo $type;?>-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>" class="panel-collapse page-accordion collapse">
                    <div class="panel-body">
                        <div class="idea-wiki-top-sec">
                        <div class="can-img"></div>
                        <div class="description collapse ">
                           <?php
                           $readmoreurl = SITEURL."wikies/get_wiki_page/".$project_id."/".$this->Session->read("Auth.User.id")."/".$wiki_id."/".$wikipage['WikiPage']['id'];
                           echo $wikipage['WikiPage']['description'];
                           ?>
                        </div>
                            <?php include 'page_button.ctp';?>
                        </div>
                        <div>&nbsp; </div>
                        <div class="idea-wiki-bottom-sec">
                            <div class="collapse" id="wiki_page_all_users_<?php echo $wikipage['WikiPage']['id']; ?>">
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

                            <div class="collapse" id="left_wiki_page_all_detail_<?php echo $wikipage['WikiPage']['id']; ?>">
                                <ul class="updateDetail">
                                    <li>Created:
                                        <?php
                                            echo $this->Wiki->_displayDate(date("Y-m-d h:i:s",$wikipage['WikiPage']['created'])) ;
                                        ?>
                                    </li>
                                    <li>Created by:
                                        <?php
                                        echo (isset($wikipage['WikiPage']['user_id']) && !empty($wikipage['WikiPage']['user_id'])) ? $this->Common->userFullname($wikipage['WikiPage']['user_id']) : 'N/A';
                                        ?>
                                    </li>
                                    <li id="updatedtext-<?php echo $wikipage['WikiPage']['id'];?>">Updated:
                                        <?php

                                        echo  $this->Wiki->_displayDate(date("Y-m-d h:i:s",$wikipage['WikiPage']['updated']));;
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
       <!-- <div class="text-center "> No wiki page found! </div>-->
        <?php
    }
    ?>
</div>
<script type="text/javascript" >




    $(function ($) {


          $("body").delegate(".openwikipage_anchor", 'click', function (e) {

			  var $that = $(this);
			  $that.toggleClass('canvas');


			  if( $that.hasClass('canvas') ){
				$that.parents('.wiki-block').find('.description').addClass('in');
				html2canvas($that.parents('.wiki-block').find('.description').css('font-size','45%'), {

					onrendered: function(canvas) {
					theCanvas = canvas;
					document.body.appendChild(canvas);

					$that.parents('.wiki-block').find(".can-img").html(canvas);

					}
				});
					 setTimeout(function(){
						//$that.parents('.wiki-block').find('.description').css('display:none')
						//$that.parents('.wiki-block').find(".can-img").hide();
						$that.parents('.wiki-block').find('.description').css('font-size','14px');
						$that.parents('.wiki-block').find('.description').removeClass('in');
					 },300);

				}

			});

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