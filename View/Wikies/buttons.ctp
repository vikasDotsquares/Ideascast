<?php 
$is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"),1);
$project_wiki = $this->Wiki->getProjectWiki($project_id, $this->Session->read("Auth.User.id"));
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));
if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
//pr($project_wiki);
 
?>
<div class="pull-left">
    <a class="bg-blue sb_blog" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>projects/project_people/<?php echo $data['Project']['id']; ?>"><button class="btn  btn-sm btn-primary">Project Team: <?php echo $total; ?></button></a>
    
	<?php if ( ( isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id']) )  &&  (((count($project_wiki) > 0) && (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status']==1)) || ($project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id"))) ) { ?>
       <!-- <a href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'index', 'project_id' => $data['Project']['id'], 'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success">Wiki</button></a>-->
    <?php
    }
	$bloglist_tot = ( isset($bloglist) && !empty($bloglist) ) ? count($bloglist) : 0;
    if ( $bloglist_tot > 0 ) {
        ?>	
        <a href="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'index', 'project' => $data['Project']['id'], 'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success">Blog &nbsp;<i class="fa fa-folder-open tipText" title="Open Blog"></i></button></a>
<?php } ?>	
</div>


<div class="pull-right">
    <?php
 
    if (( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || ($is_full_permission_to_current_login == 1)) {
  
		if ((!isset($project_wiki['Wiki']['id'])) ) {
		
            ?>
            <button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_wiki', $data['Project']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id']; ?>" data-area="<?php echo $data['Project']['id']; ?>" id="create_wiki" data-target="#modal_create_wiki" data-toggle="modal" data-original-title="Create Wiki" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Wiki</button>
        <?php }
	
$flag = true;	
if ((isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id']) )  &&  (((count($project_wiki) > 0) && (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status']==1)) || ($project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id"))) ) {  	
 
 
  $is_requested_user = $this->Wiki->is_requested_user_is_approved($this->Session->read("Auth.User.id"), $project_wiki['Wiki']['id']);
    
		
    if(( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {

    }else if((isset($is_requested_user) && $is_requested_user == 1 ) &&  ($project_wiki['Wiki']['wtype'] !=1)){

    $flag =  false;

    }
						
if( $flag == true){		
		
		?>
            
        <button data-remote="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'create_wiki_page', $data['Project']['id'], $this->Session->read('Auth.User.id'), $wiki_id, 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id']; ?>" data-area="<?php echo $wiki_id; ?>" id="create_wiki_page" data-target="#modal_create_wiki_page" data-toggle="modal" data-original-title="Create Wiki Page" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Wiki Page</button> 
<?php } }

    if (( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {

 ?>		
       
      
   <!-- <button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_blog', $data['Project']['id'], $this->Session->read('Auth.User.id'), 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id']; ?>" data-area="<?php //echo $project_wiki['Wiki']['id']; ?>" id="create_wiki" data-target="#modal_create_blogpost" data-toggle="modal" data-original-title="Create Blog" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Blog Post</button>-->
	  
        
    <?php  }
    } else if ((empty($p_permission['ProjectPermission']['project_level']) || $p_permission['ProjectPermission']['project_level'] != 1)) {

 
		if ((isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id']) )  &&  (((count($project_wiki) > 0) && (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status']==1)))  && ($project_wiki['Wiki']['wtype']==1)  && ($is_full_permission_to_current_login != 1) ) {  
		
            $checkStatus = $this->Wiki->getWikiRequestStatus($project_id,$this->Session->read('Auth.User.id'),$wiki_id);
            
            $class = "wiki_request_send";
            $text = 'Request Wiki';
            if($checkStatus == 0 && $checkStatus != null){
                $class = "disabled";$text ="Requested Wiki";
            }
            
            ?>
            <button data-original-title="Request Wiki" data-id="<?php echo $wiki_id;?>" data-remote="<?php echo SITEURL;?>wikies/wiki_request/<?php echo $project_id."/".$this->Session->read('Auth.User.id')."/".$wiki_id;?>" class="btn btn-sm btn-warning tipText <?php echo $class;?>"><i class="fa fa-plus"></i>&nbsp;<?php echo $text;?></button>
    <?php
        }
    }
    ?>
</div>	