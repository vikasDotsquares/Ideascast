<?php


$current_user_id = $this->Session->read('Auth.User.id');
$interest_users = (isset($interest) && !empty($interest)) ? Hash::extract($interest, '{n}.ProjectSketchInterest.user_id') : null; 
if(isset($participant_users) && !empty($participant_users)){
    foreach($participant_users as $k => $value){
        $user = $value['ProjectSketchParticipant']['user_id'];
        $users[$k] = $user;
    }
}  
if(isset($interest_users) && !empty($interest_users)){
    $finalArr = array_unique(array_merge($interest_users, $users));
}else{
    $finalArr = $users;
} 
$editmode = $sketchdata['ProjectSketch']['is_edit_mode'];
$edituser = $sketchdata['ProjectSketch']['edit_user_id'];
pr($sketchdata);
$currentuser = $this->Session->read("Auth.User.id");
if (isset($finalArr) && !empty($finalArr)) {
    foreach ($finalArr as $user) {
        $checkedself =   ''; $checkedother = $checkedthumb = $interest_user = $html = '';
        if(($editmode == 1 && $edituser == $user)){
            $checkedself =   ' checked="checked" disabled="disabled" ';
        }        
        if(($editmode == 1 && $user != $currentuser)){
            $checkedother =   ' disabled="disabled" ';
            $checkedthumb =   '  ';
        } 
        if($editmode == 1 && $edituser != $user && $user == $currentuser){
            $checkedthumb =   ' q-thumb ';
        }
        if(isset($interest_users) && !empty($interest_users) &&   in_array($user,$interest_users) ){
            $checkedthumb =   ' q-thumb ';
            $interest_user = ' checked="checked" ';
        }
        
        $pic = $profiles = $job_title = $pic = $btn_html = '';
        $user_data = $this->ViewModel->get_user_data($user);
        if(isset($user_data) && !empty($user_data)){
            $pic = $user_data['UserDetail']['profile_pic'];
            $profiles = SITEURL . USER_PIC_PATH . $pic;
            $job_title = $user_data['UserDetail']['job_title'];
            if( $user != $current_user_id ) {
                    $btn_html = CHATHTML($user);
            }
        }

        if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
            $profiles = SITEURL . USER_PIC_PATH . $pic;
        } else {
            $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
        }
        //echo $checkedself.' - ' , $checkedother.' - ',$checkedthumb;
        //$checkedself =   ''; $checkedother = $checkedthumb = '';
        $html .= '<div class="repeate">';
        $html .= '<a href="#" data-remote="'.SITEURL.'shares/show_profile/'.$user.'data-target="#popup_model_box" data-toggle="modal" class="pophover" '.'data-content="<div class='.'"user-detail" ><p>'.isset($user_data['UserDetail']) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'.'</p><p>'.$job_title.'</p>'.$btn_html.'"'.' data-original-title="" title=""><img src="'.$profiles.'" class="user-image" style="border: 2px solid #333"></a>';
        $html .= '<div class="chk-wrapper '.$checkedthumb.'">';
        $html .= '<input '.$checkedother. ' ' . $interest_user.' '.$checkedself. ' id="user-'.$user.'" class="user-checkbox checkbox-custom" name="data[ProjectSketch][user_id][]" value="'.$user.'" type="checkbox">';
        $html .= '<label for="user-'.$user.'" class="checkbox-custom-label"></label>';
        $html .= '</div> </div> ';
        
    }
}


?>
 <div class="repeate">
    <a href="#" data-remote="http://192.168.4.29/ideascomposer/shares/show_profile/194" data-target="#popup_model_box" data-toggle="modal" class="pophover" data-content="&lt;div class='user-detail'&gt;&lt;p&gt;Harry zop&lt;/p&gt;&lt;p&gt;Tester&lt;/p&gt;&lt;p&gt;&lt;a class='btn btn-default btn-xs disabled'&gt;Send Email&lt;/a&gt; &lt;a class='btn btn-default btn-xs disabled'&gt;start chat&lt;/a&gt;&lt;/p&gt;" data-original-title="" title="">

        <img src="http://192.168.4.29/ideascomposer/uploads/user_images/1462774254.png" class="user-image" style="border: 2px solid #333">
    </a>
    <!-- q-thumb-->
    <div class="chk-wrapper   ">
        <input disabled="disabled" id="user-194" class="user-checkbox checkbox-custom" name="data[ProjectSketch][user_id][]" value="194" type="checkbox">
        <label for="user-194" class="checkbox-custom-label"></label>

    </div> 
</div>
<div class="repeate">
    <a href="#" data-remote="http://192.168.4.29/ideascomposer/shares/show_profile/15" data-target="#popup_model_box" data-toggle="modal" class="pophover" data-content="&lt;div class='user-detail'&gt;&lt;p&gt;Pukhraj Panwar&lt;/p&gt;&lt;p&gt;Head of Operations/Management/Business&lt;/p&gt;" data-original-title="" title="">
        
        <img src="http://192.168.4.29/ideascomposer/uploads/user_images/1470912360.png" class="user-image" style="border: 2px solid #333">
    </a>
    <div class="chk-wrapper  q-thumb ">
        <input checked="checked" id="user-15" class="user-checkbox checkbox-custom" name="data[ProjectSketch][user_id][]" value="15" type="checkbox">
        <label for="user-15" class="checkbox-custom-label"></label>
    </div> 
</div>
