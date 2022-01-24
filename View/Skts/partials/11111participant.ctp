<?php 
if( isset($sketchdata['ProjectSketch']) && !empty($sketchdata['ProjectSketch']['is_edit_mode']) ){
	$editmode = $sketchdata['ProjectSketch']['is_edit_mode'];
} else {
	$editmode = 0;
}
if( isset($sketchdata['ProjectSketch']) && !empty($sketchdata['ProjectSketch']['edit_user_id']) ){
	$edituser = $sketchdata['ProjectSketch']['edit_user_id'];
} else {
	$edituser = 0;
}
 
$currentuser = $this->Session->read("Auth.User.id");
 

$interest_users = (isset($interest) && !empty($interest)) ? Hash::extract($interest, '{n}.ProjectSketchInterest.user_id') : array();
$editing_user = '';$users = array();
if(isset($participant_users) && !empty($participant_users)){
    foreach($participant_users as $k => $value){
        $user = $value['ProjectSketchParticipant']['user_id'];
        if(($editmode == 1 && $edituser == $user)){
            $editing_user[0] = $user;
        }else{
            $k = $k+1;
            $users[$k] = $user;
        }
        
        
    }
}  
//pr($editing_user);
if(isset($editing_user) && !empty($editing_user)){
	if( isset($interest_users) && !empty($interest_users) ){
		$interest_users = array_unique(array_merge($editing_user, $interest_users));	
	} else {
		$interest_users = array($editing_user);	
	}	
}
if(isset($interest_users) && !empty($interest_users)){	
	
	if( isset($users) && !empty($users) ){
		$finalArr = array_unique(array_merge($interest_users, $users));
	} else {
		$finalArr = $interest_users;
	}	
}else{
    $finalArr = $users;
} 
//pr($finalArr);
if (isset($finalArr) && !empty($finalArr)) {
    foreach ($finalArr as $user) {
        $checkedself =  $tip =  $checkedother = $checkedthumb = $interest_user = $self_edit = $other_user = '';
        if(($editmode == 1 && $edituser == $user)){
            $checkedself =   ' checked="checked"  disabled="disabled" ';
            $tip = 'data-original-title="Currently Editing"';
            $self_edit = 'self_edit';
        }        
        if(($editmode == 1 && $user != $currentuser)){
            $checkedother =   ' disabled="disabled" ';
            $checkedthumb =   '  ';
            $other_user =   ' other_user ';
        } 
        if($editmode == 1 && $edituser != $user && $user == $currentuser){
            $checkedthumb =   ' q-thumb ';
        }
        if(isset($interest_users) && !empty($interest_users) && ($editmode == 1 && $edituser != $user) &&   in_array($user,$interest_users) ){
            $checkedthumb =   ' q-thumb ';
            $interest_user = ' checked="checked" ';
            $tip = 'data-original-title="Request to Edit"';
        }
        
        $pic = $profiles = $job_title = $pic = $btn_html = '';
        $user_data = $this->ViewModel->get_user_data($user);
        if(isset($user_data) && !empty($user_data)){
            $pic = $user_data['UserDetail']['profile_pic'];
            $profiles = SITEURL . USER_PIC_PATH . $pic;
            $job_title = $user_data['UserDetail']['job_title'];
            if( $user != $currentuser ) {
                    $btn_html = CHATHTML($user, $sketchdata['ProjectSketch']['project_id']);
            }
        }

        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
            $profiles = SITEURL . USER_PIC_PATH . $pic;
        } else {
            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
        }
        //echo $checkedself.' - ' , $checkedother.' - ',$checkedthumb;
        //$checkedself =   ''; $checkedother = $checkedthumb = ''; 
        
       
        ?>




<div class="repeate">
    <a href="#" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $user; ?>" data-target="#popup_model_box" data-toggle="modal" class="pophover" data-content="<div class='user-detail'><p><?php echo isset($user_data['UserDetail']) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></p><p><?php echo $job_title; ?></p><?php echo $btn_html; ?>" data-original-title="" title="">

        <img src="<?php echo $profiles; ?>" class="user-image" style="border: 2px solid #333">
    </a>
    <!-- q-thumb-->
    <div <?php echo $tip;?>  class="tipText chk-wrapper <?php echo $checkedthumb;?>">
        <input <?php echo $checkedother; ?> <?php echo $interest_user;?> <?php echo $checkedself; ?> id="user-<?php echo $user; ?>" class="user-checkbox checkbox-custom  <?php echo $self_edit; ?> <?php echo $other_user;?>" name="data[ProjectSketch][user_id][]" value="<?php echo $user; ?>" type="checkbox">
        <label for="user-<?php echo $user; ?>" class="checkbox-custom-label"></label>

    </div> 
</div>
        <?php
        $editTip = $tip = '';
    }
}
?>
 
<script type="text/javascript" >
    $(document).ready(function () {
        $('.pophover').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
    });
</script>                                                    
  
