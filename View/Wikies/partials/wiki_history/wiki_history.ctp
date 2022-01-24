<?php
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id, $this->Session->read("Auth.User.id"));
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}

$project_wiki = $this->Wiki->getMainWikiHistory($project_id, $this->Session->read("Auth.User.id"));
$s = 'Created';
$c = 'bg-gray';
$t = 'text-black';
$current_wiki = $this->Wiki->getCurrentWiki($project_id,$project_wiki['Wiki']['id']);
if(isset($current_wiki['Wiki']) && !empty($current_wiki['Wiki'])){
    $project_wiki['Wiki']['title'] = $current_wiki['Wiki']['title'];
    $project_wiki['Wiki']['user_id'] = $current_wiki['Wiki']['user_id'];
    $project_wiki['Wiki']['updated'] = $current_wiki['Wiki']['updated'];
    $bgclass = 'bg-green';
    $s = 'Current';
    $c = 'bg-green';
    $t = 'text-white';
}
?>


<div class="col-sm-8 col-md-8 col-lg-9 wiki-right-section">
    <div class="tabContentLeft">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading <?php echo $c;?>">
                    <h4 class="panel-title wiki-accordion-icon">
                        <a class="accordion-toggle collapsed <?php echo $t;?>" id="changeaccordion-<?php echo $project_wiki['Wiki']['id']; ?>" data-toggle="collapse" data-parent="#accordion" href="#wikidata-<?php echo $project_wiki['Wiki']['id']; ?>">
                            <i class="fab fa-wikipedia-w"></i>
                            <?php
                            
                            //pr($current_wiki);
                            
                            
                            
                            echo $project_wiki['Wiki']['title'];
                            ?>
                        </a>
                        <div class="historyupdate pull-right"><?php echo $s;?>: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$project_wiki['Wiki']['updated'])); ?>, <span class="wikihistoryby">by: <?php echo $this->Common->userFullname($project_wiki['Wiki']['user_id']);?></span></div>

                    </h4>
                </div>
                <div id="wikidata-<?php echo $project_wiki['Wiki']['id']; ?>" class="panel-collapse collapse wikihistorytab">
                    <div class="panel-body">
                        <div class="idea-wiki-top-sec">
                            <div class="description">
                                <?php echo $project_wiki['Wiki']['description']; ?>
                            </div>

                        </div>
                        <div>&nbsp;</div>
                        <?php
                        $allWikis = $this->Wiki->getWikiHistoryLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id, $project_wiki['Wiki']['id']);
                        ?>

                        <?php
                        $params = array("allWikis" => $allWikis, "project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null, "user_project" => $user_project, "p_permission" => $p_permission, 'type' => 'history');
                        echo $this->element('../Wikies/partials/wiki_history/get_wiki_history_by_user', $params);
                        ?>

                    </div>
                </div>
            </div>
            
            
            
            <?php
            $allMains = $this->Wiki->getWikiPageListsParent($project_id, $this->Session->read('Auth.User.id'), $wiki_id);
            if (isset($allMains) && !empty($allMains)) {
                foreach ($allMains as $allMainPage) {
                    $wikiPageUnapproved = $this->Wiki->getWikiPageUnapproved($project_id, $this->Session->read('Auth.User.id'), $wiki_id, $allMainPage['WikiPage']['id']);
                    if(isset($allMainPage['WikiPage']['is_deleted']) && $allMainPage['WikiPage']['is_deleted'] == 1 ){
                        $bgclass = 'bg-red';
                        $s = 'Deleted';
                    }else{
                        $bgclass = 'bg-gray';
                        $s = 'Created';
                    }
                    if(isset($wikiPageUnapproved['approved']) && !empty($wikiPageUnapproved['approved'])){
                        $allMainPage['WikiPage']['title'] = $wikiPageUnapproved['approved']['WikiPage']['title'];
                        $allMainPage['WikiPage']['user_id'] = $wikiPageUnapproved['approved']['WikiPage']['user_id'];
                        $allMainPage['WikiPage']['updated'] = $wikiPageUnapproved['approved']['WikiPage']['updated'];
                        $allMainPage['WikiPage']['is_deleted'] = $wikiPageUnapproved['approved']['WikiPage']['is_deleted'];
                        $bgclass = 'bg-green';
                        $s = 'Current';
                    }
                    ?>


                    <div class="panel panel-default">
                        
                        
                        
                        <div class="panel-heading <?php echo $bgclass;?>">
                            <h4 class="panel-title wiki-accordion-icon">
                                <a class="accordion-toggle wiki-toggle-history collapsed" id="changeaccordion-<?php echo $allMainPage['WikiPage']['id']; ?>" data-toggle="collapse" data-parent="#accordion" href="#allMain_wikidata-<?php echo $allMainPage['WikiPage']['id']; ?>">
<!--                                    <i class="fa"></i>-->
                                    
                                    
                                    <?php 
                                    
                                    if(isset($wikiPageUnapproved['unapproved']) && $wikiPageUnapproved['unapproved'] > 0){
                                    ?>
                                     
                                    <img class="" alt="" style="width:15px; float: left;margin-right: 5px;" src="<?php echo SITEURL;?>/img/unapproved.png" />
                                    
                                    <?php
                                    }
                                    ?>
                                    
                                    
                                    
                                    
                                    <?php
                                    echo $allMainPage['WikiPage']['title'];
                                  //  pr($wikiPageUnapproved['info']);
                                    ?>
                                </a>
                                <?php //pr($allMainPage['WikiPage']);?>
                                <div class="historyupdate pull-right">
                                    <?php echo $s;?>: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$allMainPage['WikiPage']['updated'])); ?>, <span class="wikihistoryby">by: <?php echo $this->Common->userFullname($allMainPage['WikiPage']['user_id']);?></span><?php
                                    $info = null;
                                    //pr($wikiPageUnapproved['info']['active']);
                                    if(isset($wikiPageUnapproved['info'])){
                                        $info = '<div class=wiki-info-detail>'
                                                . 'Active:'.$wikiPageUnapproved['info']['active'].'<br>'
                                                . 'Approved:'.$wikiPageUnapproved['info']['approved'].'<br>'
                                                . 'Deleted:'.$wikiPageUnapproved['info']['deleted'].'<br>'
                                                . 'Unapproved:'.$wikiPageUnapproved['info']['unapproved']
                                                . '</div>';
                                    }
                                    ?>
                                    <span style="float:right;" class="info-wiki clickables panel-collapsed  pophover-wiki btn btn-xs btn-defaults" data-placement="top"  data-toggle="popover" data-trigger="hover"  data-content="<?php echo $info;?>" data-project="2">
                                            <i class="fa fa-info fa-3 martop text-white"></i> 
                                        </span>    
                                </div>
                                
                            </h4>
                        </div>
                        <div id="allMain_wikidata-<?php echo $allMainPage['WikiPage']['id']; ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="idea-wiki-top-sec">
                                    <div class="description">
                                        <?php echo $allMainPage['WikiPage']['description']; ?>
                                    </div>
                                   
                                </div>
                                <div>&nbsp;</div>
                                <?php
                                $allWikiPages = $this->Wiki->getWikiPageHistoryLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id, $allMainPage['WikiPage']['id']);
                                ?>

                                <?php
                                $params = array("allWikiPages" => $allWikiPages, "project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null, "user_project" => $user_project, "p_permission" => $p_permission, 'type' => 'history');
                                echo $this->element('../Wikies/partials/wiki_history/get_wiki_page_history_by_user', $params);
                                ?>

                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                ?>
                <!--<div class="text-center margin">No wiki page found! <a href="" class="backtoread" >  Back</a>--></div>
                <?php
            }
            ?>

        </div>
    </div>
</div>
<div class="col-sm-4 col-md-4 col-lg-3 wiki-left-section" >
    <ul class="pageusertab nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" class="active" href="#pages_tab" aria-expanded="true">Pages</a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#users_tab" aria-expanded="false">Participants</a>
        </li>
    </ul>
    <div class="tab-content">
        <?php
        $allWikiPages = $this->Wiki->getWikiPageLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id);
        ?>

        <?php
        $params_reed = array("allWikiPages" => $allWikiPages, "project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null, "user_project" => $user_project, "p_permission" => $p_permission, 'type' => 'read');
        ?>
        <div id="pages_tab" class="tab-pane fade in active" >
            <?php echo $this->element('../Wikies/partials/wiki_history/get_wiki_page_by_user', $params_reed); ?>
        </div>
        <div id="users_tab" class="tab-pane fade">
<?php echo $this->element('../Wikies/partials/wiki_history/wiki_all_users', array("project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'), "wiki_id" => $wiki_id, "wiki_page_id" => null)); ?>
        </div>
    </div>

</div>
<script type="text/javascript">
$(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;'); 	
    $('.pophover-wiki').popover({
            placement: 'top',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: {show: 50, hide: 400}
    })
})
</script>
<style type="text/css">
    .info-wiki{
        float: right !important;
        margin: 0 0 0 10px !important;
        padding: 0 !important;
        position: relative;
        top: -3px !important;
    }
  .fa-info {
    background: #00aff0 none repeat scroll 0 0;
    border-radius: 50%;
    color: #fff;
    font-size: 10px;
    height: 18px;
    line-height: 20px;
    padding: 0 0 0 1px;
    width: 20px;
}  
.panel-title.wiki-accordion-icon {
 font-size: 13px;
}

@media (min-width:992px) and (max-width:1199px) {
.info-wiki {
  right: -14px;
  top: -22px !important;
}
}
</style>

