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


<div class="col-sm-8 col-md-8 col-lg-9 wiki-right-section">
    <div class="tabContentLeft">
        <div class="panel-group" id="wiki-accordion">
            <div class="panel panel-default wiki-accordion" >
                <div class="panel-heading bg-gray">
                    <h4 class="panel-title wiki-accordion-icon">
                        <a class="accordion-toggle wiki-toggle" id="changeaccordion" data-toggle="collapse" data-parent="#wiki-accordion" href="#wikidata">
                            <i class="fab fa-wikipedia-w"></i>&nbsp;<?php echo $project_wiki['Wiki']['title']; ?>
                        </a>
                        <div class="pull-right"><i  class="fa fa-long-arrow-up main-wiki tipText" title="Top"></i>&nbsp;<i class="fa  fa-long-arrow-left  onestepback tipText" title="Previous" ></i></div>
                    </h4>
                </div>
                <div id="wikidata" class="panel-collapse wikidata in wiki-collapse" style="">
                    <div class="panel-body">
                        <?php 
                            $selectionurl = SITEURL."wikies/create_wiki_page_linked/".$project_id."/".$this->Session->read("Auth.User.id")."/".$wiki_id;
                        ?>
                            
                        <div class="description contant_selection" data-remote="<?php echo $selectionurl;?>" data-user-id="<?php echo $this->Session->read("Auth.User.id");?>" data-project-id="<?php echo $project_id;?>" data-wiki-id="<?php echo $wiki_id;?>" data-page-id="null">
                            
                           
                            
                               <?php  echo $project_wiki['Wiki']['description']; ?>
                        </div>
                        
                        <div class="btn" style="padding:  5px 0; ">
                            
                            
                        <?php if(isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id") ){?>
                            <?php if (isset($project_wiki['Wiki']['wtype']) && $project_wiki['Wiki']['wtype'] == 0) { ?>
                                <label title=" Limited user Permission" class="fancy_labels tipText btn btn-xs btn-info" for="wiki_type_open" style="width:72px;">Limited </label>
                            <?php } else { ?>
                                <label title="All user Permission"  class="fancy_labels tipText btn btn-xs btn-info" for="wiki_type_limited" style="width:72px;"> Open</label> 
                            <?php } ?>
                            <?php if (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status'] == 0) { ?>
                                <label title="Draft" class="fancy_labels tipText btn btn-xs btn-info" for="wiki_status_draft" style="width:72px;">Draft</label>
                            <?php } else if (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status'] == 1) { ?>
                                <label title="Published"  class="fancy_labels tipText btn btn-xs btn-info" for="wiki_status_published" style="width:72px;">Published</label> 
                            <?php } ?>

                        <?php } ?>
                            <?php  
                         
                            //if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true){
                            //if(isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id")){   
                                
                             if (( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) || (isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id")) ) {    
                            ?>    
                            <a data-toggle="modal" data-target="#modal_create_wiki"  href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'update_wiki', $project_id,$this->Session->read('Auth.User.id'),$project_wiki['Wiki']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $project_wiki['Wiki']['id']; ?>"  title="Edit Wiki" class="tipText btn btn-xs btn-success full_permission"><i class="fa fa-pencil"></i></a>
                            <?php }else{?>
                                <a href="" title="Edit Wiki" class="tipText disabled btn btn-xs btn-success not_full_permission"><i class="fa fa-pencil"></i></a>
                            <?php } ?>

<!--                            <a href="" title="Wiki All User" data-remote="<?php echo SITEURL; ?>wikies/wiki_all_users/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id?>" class="tipText btn btn-xs btn-default wiki_all_users" href="" ><i class="fa fa-user-plus"></i></a>-->

                        </div>
                        <?php
                        $allWikiPages = $this->Wiki->getWikiPageLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id);
                        ?>

                        <?php
                        $params_reed = array( "allWikiPages" => $allWikiPages,"project_id" => $project_id,"user_id" => $this->Session->read('Auth.User.id'),"wiki_id" => $wiki_id,"wiki_page_id"=>null,"user_project"=>$user_project,"p_permission"=>$p_permission,'type' => 'read');
                        //echo $this->element('../Wikies/partials/wiki_read/get_wiki_page_by_user', $params_reed );
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-4 col-md-4 col-lg-3 wiki-left-section" >
    <ul class="pageusertab nav nav-tabs noborder" >
         
        <li class="active">
            <a data-toggle="tab" data-tab="page" data-remote="<?php echo SITEURL;?>wikies/get_wiki_page_by_user/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id; ?>" class="active" href="#pages_tab" aria-expanded="true">Pages</a>
        </li>
        <li class="righttab">
            <a data-toggle="tab" data-tab="user" data-remote="<?php echo SITEURL;?>wikies/wiki_all_users/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id; ?>"  href="#users_tab" aria-expanded="false">Participants</a>
        </li>
    </ul>
    <div class="tab-content">
        
        <div id="pages_tab" class="tab-pane fade in active" >
            <div class="panel wiki-block panel-default page-collapse-<?php echo $project_wiki['Wiki']['id'] ?>" style="margin:0 0 5px 0">
                <div class="panel-heading bg-gray noborder">
                    <h4 class="panel-title wiki-common-h4">
                        <a class="accordion-toggle gotowiki wiki-accordion collapsed" data-toggle="collapse" data-parent="#read-page-accordion" href="#wiki-collapse-<?php echo $project_wiki['Wiki']['id'] ?>">
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
                        //if(isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true){
                        if(isset($project_wiki['Wiki']['user_id']) && $project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id")){   
                        
                            

                            
                        ?>    
                            <a data-toggle="modal" data-target="#modal_create_wiki"  href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'update_wiki', $project_id,$this->Session->read('Auth.User.id'),$wiki_id, 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $wiki_id; ?>"  title="Edit Wiki" class="tipText btn btn-xs btn-success full_permission"><i class="fa fa-pencil"></i></a>
                        <?php }else{?>
                            <a href="" title="Edit Wiki" class="tipText disabled btn btn-xs btn-success not_full_permission"><i class="fa fa-pencil"></i></a>
                        <?php } ?>
                            <a class="btn btn-default btn-xs tipText gotowiki"  title="Wiki Details"><i class="fab fa-wikipedia-w"></i></a>
                    </div>
                </div>
            </div>
            <div class="keywordchange-page">
                
                <?php 
                 
                echo $this->element('../Wikies/partials/wiki_read/get_wiki_page_by_user', $params_reed );?>
            </div>
            
        </div>
        <div id="users_tab" class="tab-pane fade">
            <div class="keywordchange-user">
          <?php 
          
          echo $this->element('../Wikies/partials/wiki_read/wiki_all_users', array("project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'),"wiki_id" => $wiki_id,"wiki_page_id"=>null ));
          ?>
            </div>
        </div>
    </div>
    
</div>

<script type="text/javascript" >
    
    
    function getTimeZone() {
        var offset = new Date().getTimezoneOffset(),
            o = Math.abs(offset);
        return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
    }

    //console.log(getTimeZone());
    //console.log(new Date());
    
    
    $(function() {
		
		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
		
        var startData = new Date();
        var day = startData.getDate();
        var month = startData.getMonth();
        var year = startData.getFullYear();
        var hour = startData.getHours();
        var min = startData.getMinutes();
        var sec = startData.getSeconds();
        var startTime = startData.getTime();
        var currentTimeZoneOffsetInHours = startData.getTimezoneOffset() / 60;
       
        
        //alert(currentTimeZoneOffsetInHours);
        //alert(day+" - "+ month +" - " + year +"   " + hour +" - " + min +" - "+ sec);
        
        
        
        $('body').delegate(".description.contant_selection a", 'dblclick', function(){ 
                return false;
        })
        $('#modal_create_wiki').on('hide.bs.modal', function () { 
            $(".selected-now").each(function(key,val){
                var text = $(this).html();
                //$(this).removeAttr("class");
                //$(this).removeAttr("href");
                //$(this).removeAttr("data-id");
                $(this).replaceWith(text);
               
                //$(".tabtype").find("li:first-child a").trigger("click");
            })
        });
    });
</script>


<style>
.accordion-toggle.wiki-toggle {
  font-size: 13px;
}

#tabContentLeft .panel-title {
    font-size: 13px;
    padding-bottom: 0;
}
</style>