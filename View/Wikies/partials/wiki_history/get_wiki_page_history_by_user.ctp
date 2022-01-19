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
<style>
    .internalpagescroll{
        max-height: 424px;
        min-height: 424px;
        overflow: auto;
    }
</style>
<div class="panel-group internalpagescroll" id="right-history-history-accordion">
    <?php
    if (isset($allWikiPages) && !empty($allWikiPages)) {
        foreach ($allWikiPages as $wikipage) {
            ?>
            <div class="panel panel-default right-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>" >
                <?php 
                if(isset($wikipage['WikiPage']['is_archived']) && $wikipage['WikiPage']['is_archived'] == 1 && $wikipage['WikiPage']['is_deleted'] == 0){
                    $bgclass = 'bg-green';
                }else if(isset($wikipage['WikiPage']['is_deleted']) && $wikipage['WikiPage']['is_deleted'] == 1 ){
                    $bgclass = 'bg-red';
                }else{
                    $bgclass = 'bg-gray';
                }?>
                
                <div class="panel-heading history-sub-page <?php echo $bgclass; ?>">
                    <h4 class="panel-title wiki-common-h4">
                        <a class="accordion-toggle page-accordion collapsed" data-toggle="collapse" data-parent="#right-history-history-accordion" href="#right-history-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>">
<!--                            <i class="indicator fa "></i>-->
                            
                            
                            
                            
                            
                            
                            
                            
                            <?php 
                           // pr($wikipage['WikiPage']);
                           // echo $wikipage['WikiPage']['is_archived'].'----';
                            echo $wikipage['WikiPage']['title']; ?>
                        </a>
                        <div class="historyupdate pull-right">
                            <?php 
                            
                            //pr($wikipage['WikiPage']);
                            if(isset($wikipage['WikiPage']['is_archived']) && $wikipage['WikiPage']['is_archived'] == 1 && $wikipage['WikiPage']['is_deleted'] == 0){?>
                            Approved: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$wikipage['WikiPage']['updated'])); ?>, by: <?php echo $this->Common->userFullname($wikipage['WikiPage']['updated_user_id']); ?>
                            <?php }else if(isset($wikipage['WikiPage']['is_deleted']) && $wikipage['WikiPage']['is_deleted'] == 1 ){ ?>
                            Deleted: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$wikipage['WikiPage']['updated'])); ?>, by: <?php echo $this->Common->userFullname($wikipage['WikiPage']['updated_user_id']);?>
                            <?php }else{?>
                            Updated: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$wikipage['WikiPage']['updated'])); ?>, by: <?php echo $this->Common->userFullname($wikipage['WikiPage']['updated_user_id']);?>
                            <?php }?>
                            
                        </div>
                    </h4>
                </div>
                <div id="right-history-page-collapse-<?php echo $wikipage['WikiPage']['id'] ?>" class="panel-collapse wikipage collapse">
                    <div class="panel-body">
                        <div class="description">
                           <?php echo $wikipage['WikiPage']['description']; ?> 
                        </div>
                        
                       
                         <?php 
                        $historyparam = "wikies/wiki_history_page_approved/" . $project_id . "/" . $this->Session->read('Auth.User.id') . "/" . $wiki_id . "/" . $wikipage['WikiPage']['id'];
						if((isset($wikipage['WikiPage']['archieved_on']) && !empty($wikipage['WikiPage']['archieved_on'])) && $wikipage['WikiPage']['archieved_on'] != '0000-00-00 00:00:00'){
						?>
						<span><label class="text-blue">Approved On:</label> <strong><?php echo $this->Wiki->_displayDate($wikipage['WikiPage']['archieved_on']); ?></strong></span>
						<?php
						}else{
                        ?>
                        <div class="btn-group">
                            <a href="" data-remote="<?php echo SITEURL . $historyparam; ?>"  class="tipText btn btn-xs btn-warning historypageapproved" title="Approve" >Approve</a>
                        </div>
						<?php  } ?>
                         
                        
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="text-center "> No Updates <!--<a href="" class="backtoread" >  Back</a>--></div>
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
