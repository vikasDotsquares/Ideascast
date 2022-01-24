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
    .internalwikicroll{
        max-height: 424px;
        min-height: 424px;
        overflow: auto;
    }
</style>
<div class="panel-group internalwikicroll" id="history-accordion">
    <?php
    if (isset($allWikis) && !empty($allWikis)) {
        foreach ($allWikis as $wiki) {
            ?>
            <div class="panel panel-default page-collapse-<?php echo $wiki['Wiki']['id'] ?>" >
                <div class="panel-heading  <?php echo isset($wiki['Wiki']['status']) && $wiki['Wiki']['status'] == 1 ? 'bg-green' : 'bg-gray';?>">
                    <h4 class="panel-title wiki-common-h4">
                        <a class="accordion-toggle page-accordion collapsed" data-toggle="collapse" data-parent="#history-accordion" href="#<?php echo $type;?>-page-collapse-<?php echo $wiki['Wiki']['id'] ?>">
                            <?php echo $wiki['Wiki']['title']; ?>
                        </a>
                        <div class="historyupdate pull-right">
                            
                            Updated: <?php echo $this->Wiki->_displayDate(date("Y-m-d h:i:s A",$wiki['Wiki']['updated'])); ?>, by: <?php echo $this->Common->userFullname($wiki['Wiki']['updated_user_id']);?>
                            
                        </div>
                    </h4>
                </div>
                <div id="<?php echo $type;?>-page-collapse-<?php echo $wiki['Wiki']['id'] ?>" class="panel-collapse wikipage collapse">
                    <div class="panel-body">
                        <div class="description">
                           <?php echo $wiki['Wiki']['description']; ?> 
                        </div>
                        
                        
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