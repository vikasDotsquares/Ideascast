<?php $current_user_id = $this->Session->read('Auth.User.id'); ?> 
<div class="users"> 
 
    <?php
    
    if (isset($user_arrays) && !empty($user_arrays)) {
        foreach ($user_arrays as $ke => $user) {
            $class_current = '';
            // pr($project_id);
            $checked = (isset($sketchdata['ProjectSketch']['participant_all']) && $sketchdata['ProjectSketch']['participant_all'] == 1 ) ? 'checked' : '';
            if (isset($selecteduser) && in_array($user, $selecteduser)) {
                $checked = 'checked';
            }
            if ($user == $this->Session->read("Auth.User.id")) {
                $checked = 'checked';
                $class_current = " currentuser";
            }

                $pic = $profiles = $job_title = $pic = $btn_html = '';
                $user_data = $this->ViewModel->get_user_data($user);
                if (isset($user_data) && !empty($user_data)) {
                    $pic = $user_data['UserDetail']['profile_pic'];
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                    $job_title = $user_data['UserDetail']['job_title'];
                    if ($user != $current_user_id) {
                        $btn_html = CHATHTML($user, $project_id);
                    }
                }


                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }
                ?>
                <div class="repeate">                                                                                                                                     <a href="#" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $user; ?>" data-target="#popup_model_box" data-toggle="modal" class="pophover" data-content="<div class='user-detail'><p><?php echo isset($user_data['UserDetail']) ? $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'] : 'N/A'; ?></p><p><?php echo $job_title; ?></p><?php echo $btn_html; ?>" data-original-title="" title="">

                        <img src="<?php echo $profiles; ?>" class="user-image" style="border: 2px solid #333">
                    </a>
                    <div class="chk-wrapper">
                        <?php
//                        echo $this->Form->input("ProjectSketchParticipant." . $ke . ".user_id", array(
//                            $checked,
//                            "id" => "user-$user",
//                            "class" => "user-checkbox checkbox-custom $class_current",
//                            "value" => $user,
//                            "type" => "checkbox",
//                            "label" => false,
//                            "div" => false
//                                )
//                        );
                        ?>
                        <input type="checkbox" <?php echo $checked;?> value="<?php echo $user;?>" name="data[ProjectSketchParticipant][<?php echo $ke;?>][user_id]" class="user-checkbox checkbox-custom <?php echo $class_current;?>" id="user-<?php echo $user;?>"/>
                        <label for="user-<?php echo $user; ?>" class="checkbox-custom-label"></label>
                    </div> 
                </div>
                <?php
            }
        }
    ?>
</div>
<script type="text/javascript" >
    $(document).ready(function(){
        var $_current_user = "<?php echo $this->Session->read("Auth.User.id");?>";
        
        $(".user-checkbox").click(function(event){
            var $that = $(this);
            if($_current_user === $that.val()){
                $that.prop("checked",true);
            }
        })
        
    });
</script>
