<?php
                    if(empty($name)){ $name = 'Admin'; }

                    $ud =  $this->ViewModel->get_user_data($this->Session->read('Auth.User.id'));
                    $profile = '';
                    if( isset($ud['UserDetail']['profile_pic']) && !empty($ud['UserDetail']['profile_pic']) ){
                        $profile =  $ud['UserDetail']['profile_pic'];
                    }
                   // $profile = $this->Session->read('Auth.User.UserDetail.profile_pic');

                    if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)) {
                        $profiles = SITEURL.USER_PIC_PATH.$profile;
                    }else{
                        $profiles = SITEURL.'img/image_placeholders/profile_placeholder.png';
                    }

                     $menuprofile = $this->Session->read('Auth.User.UserDetail.menu_pic');
                     $menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

                    if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)) {
                        $menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
                    }
                    else{
                        $menuprofiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
                    }
                    // $currentTimeinSeconds = time();

                    if( $this->Session->read('Auth.User.role_id') == 3 ){
                        $editURL = SITEURL . 'shares/show_org_profile/'.$this->Session->read('Auth.User.id');
                    } else {
                        $editURL = SITEURL . 'shares/show_profile/'.$this->Session->read('Auth.User.id');
                    }

                    if( isset($ud['UserDetail']['first_name']) && !empty($ud['UserDetail']['first_name']) ){
                        $userFirstName = $ud['UserDetail']['first_name'];
                    } else {
                        $userFirstName = $this->Session->read('Auth.User.UserDetail.first_name');
                    }
                    if( isset($ud['UserDetail']['last_name']) && !empty($ud['UserDetail']['last_name']) ){
                        $userLastName = $ud['UserDetail']['last_name'];
                    } else {
                        $userLastName = $this->Session->read('Auth.User.UserDetail.last_name');
                    }


                    $current_user_menus = "<div class=''><p class='pop_para user text-ellipsis'>".htmlspecialchars($userFirstName)."</p><p class='pop_para user userlastname text-ellipsis'>". htmlspecialchars($userLastName)."</p>";

					if( $this->Session->read('Auth.User.role_id') == 3 ){
						$classB = 'bordrTop';
					}else{
						//$classB = '';
						$classB = 'bordrTop';
					}

                    //$current_user_menus .="<p class='pop_para ".$classB." close-user-popover'><a id='trigger_uploads' data-toggle='modal' data-target='#popup_modal'  href='".SITEURL . "users/profile'><i class='user-menu-all-icon user-menu-images'></i> Image </a></p>";

                    /*$current_user_menus .= "<p class='pop_para user ".$classB." close-user-popover'><a href='' data-remote='".SITEURL ."settings/themes' data-toggle='modal' data-backdrop='true' data-target='#modal_medium'><i class='user-menu-all-icon user-menu-themes'></i> Themes </a></p>";
                    $current_user_menus .= "<p class='pop_para user close-user-popover'><a href='' data-remote='".SITEURL ."settings/start_page'  data-toggle='modal' data-backdrop='true' data-target='#popup_modal_start'><i class='user-menu-all-icon start-page-icon'></i> Start Page </a></p>";*/
                    if( $this->Session->read('Auth.User.role_id') == 2 ){
                    $current_user_menus .= "<p class='pop_para user close-user-popover ".$classB."'><a href='".SITEURL ."settings/notification' ><i class='user-menu-all-icon user-menu-settings'></i> Settings </a></p>";
                    //$current_user_menus .= "<p class='pop_para user close-user-popover'><a href='#' class='anc_show_unavailability'><i class='user-menu-all-icon user-menu-unavailable'></i> Unavailable</a></p>";

                    //$current_user_menus .= "<p class='pop_para user brdrBtm'><a href='".SITEURL ."boards' ><i class='fa fa-newspaper-o' style='padding-right: 5px; font-size: 14px;  margin-left: 1px;'></i> Social Board  </a></p>";

                    }

                    if( $this->Session->read('Auth.User.role_id') != 1 && $this->Session->read('Auth.User.role_id') != 3 ){

                        $userOrgID = $this->Session->read('Auth.User.UserDetail.org_id');
                        if( $userOrgID == 0 || empty($userOrgID) ){
                            $current_user_menus .= "<p class='pop_para'><a href='".SITEURL ."users/changepassword' class=''><i class='user-menu-all-icon user-change-password'></i> Change Password</a></p>";
                        } else {
                            $current_user_menus .= "<p class='pop_para  '><a href='".SITEURL ."users/changepassword' class=''><i class='user-menu-all-icon user-change-password'></i> Change Password</a></p>";
                        }
                    }
        //if( $this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') == 1 ){

            /* $li_styles = " ";
            $trigger_class1 = 'fa-long-arrow-down';
            if( $this->request->params['controller'] == 'skills' || $this->request->params['controller'] == 'konwledge_domains' || $this->request->params['controller'] == 'subjects'  ||  $this->request->params['action'] == 'domain_settings' || $this->request->params['action'] == 'manage_users' || $this->request->params['action'] == 'domain_list' || $this->request->params['action'] == 'client_email_domain' || $this->request->params['action'] == 'client_manage_users' ){
                $li_styles = '';
                $trigger_class1 = 'fa-long-arrow-up';
            } */

            /* $current_user_menus .= "<p class='pop_para user bordrTop'><a href='#' class='showadmin'><i class='user-menu-all-icon user-menu-admin'></i> Admin <i class='fas fa-chevron-down admin-ex-col'></i></a></p>";

            $current_user_menus .= "<ul class='thirduser'>"; */
            /* $current_user_menus .= "<li ".$li_styles." class='pop_para ";
            if( $this->request->params['controller'] == 'skills' ){
                $current_user_menus .= "active";
            }
            $current_user_menus .= " ' >";
            $current_user_menus .= "<a href='".SITEURL."skills'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-skills'></i></span> Skills</a></li>";

			$current_user_menus .= "<li ".$li_styles." class='pop_para ";
            if( $this->request->params['controller'] == 'subjects' ){
                $current_user_menus .= "active";
            }
			$current_user_menus .= " ' >";
			$current_user_menus .= "<a href='".SITEURL."subjects'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-subjects'></i></span> Subjects</a></li>";

			$current_user_menus .= "<li ".$li_styles." class='pop_para ";
            if( $this->request->params['controller'] == 'knowledge_domains' ){
                $current_user_menus .= "active";
            }
			$current_user_menus .= " ' >";
			$current_user_menus .= "<a href='".SITEURL."knowledge_domains'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-domains'></i></span> Domains</a></li>"; */


            /* $current_user_menus .= "<li ".$li_styles." class='pop_para ";
            if( $this->request->params['action'] == 'domain_settings' ){
                $current_user_menus .= "active";
            } */
           // $current_user_menus .= " '><a href='".SITEURL."organisations/domain_settings'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-email-domains'></i></span> Email Domains</a></li>";

            /* $current_user_menus .= "<li ".$li_styles." class='pop_para ";
            if( $this->request->params['action'] == 'manage_users' ){
                $current_user_menus .= "active";
            }
            $current_user_menus .= " '><a href='".SITEURL."organisations/manage_users'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-manage-users'></i></span> Manage Users</a></li>"; */

                /* $flagORg = $this->Common->domain_list();

                if($flagORg == true){
                $current_user_menus .= "<li ".$li_styles." class='pop_para  ";

                if( $this->request->params['action'] == 'domain_list' ){
                    $current_user_menus .= " active";
                }
                $current_user_menus .= "' >"; */

                /* $current_user_menus .= "<a href='".SITEURL."organisations/domain_list'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-linked-domains'></i></span> <span class='name'>Linked Domains</span></a></li>"; */

				//$current_user_menus .= "<a href='".SITEURL."organisations/domain_list'><span class='left-icon-all'><i class='user-menu-all-icon user-menu-linked-domains'></i></span> Linked Domains</a></li>";

                 //}
                    //$current_user_menus .= "</ul>";
               //}

				 if( $this->Session->read('Auth.User.role_id') == 3 ){
					$current_user_menus .= "<p class='pop_para bordrTop user-signout'> <a class='user-signoffs' href='".SITEURL."logout' class=''><i class='user-menu-all-icon user-menu-sign-out'></i> Sign Out</a></p></div>";
				} else {
					$current_user_menus .= "<p class='pop_para bordrTop user-signout'> <a class='user-signoff' href='' class=''><i class='user-menu-all-icon user-menu-sign-out'></i> Sign Out</a></p></div>";
				}


                    /*if(($_SERVER['REMOTE_ADDR'] == '192.168.4.218' || $_SERVER['REMOTE_ADDR'] == '192.168.4.175' || $_SERVER['REMOTE_ADDR'] == '192.168.4.176')){
                        $current_user_menus .= "<p class='pop_para user-log-off' > <a href='' class=''><i class='fa fa-lock' style='font-size: 16px; margin-right: 10px;'></i> Log-Off</a></p></div>";
                    }*/
                ?>
                <li class="fix-width-menu" >
                    <a  href="javascript:;" class=" tipText user-image-pophovers" title="User Menu" data-content="<?php  echo $current_user_menus;  ?>">
                        <img style="width: 32px; height: 32px; border-radius: 50%;" src="<?php echo $profiles ?>" class="user-own-pic">
                    </a>
                </li>
                <?php if(chat_enabled()){
                 ?>
                <li class="status-menu mongo-user-status">
                    <!-- <i class="fas fa-circle user-oc-status status-<?php //echo $mongo_user_status; ?>" data-toggle="dropdown" data-original-title="Requests" aria-expanded="true" style="display: none;"></i>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a href="#" class="update-user-status" data-status="online"><span class="status-nav-icon"><i class="status-all-icon status-online-icon"></i></span> Online</a>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="update-user-status" data-status="away"><span class="status-nav-icon"><i class="status-all-icon status-away-icon"></i></span> Away</a>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="update-user-status" data-status="dnd"><span class="status-nav-icon"><i class="status-all-icon status-disturb-icon"></i></span> Do Not Disturb</a>
                        </li>
                        <li class="dropdown-submenu">
                            <a href="#" class="update-user-status" data-status="invisible"><span class="status-nav-icon"><i class="status-all-icon status-invisible-icon"></i></span> Invisible</a>
                        </li>
                    </ul> -->
                </li>
                <?php } ?>


<script type="text/javascript">
/*    $(function(){
        $("body").delegate(".showadmin","click",function(event){
           event.preventDefault();
           event.stopPropagation();
           $(".thirduser").slideToggle(200);
           $(this).find('.admin-ex-col').toggleClass('fa-chevron-up fa-chevron-down');
        });

    })*/
</script>
<style type="text/css">


    .status-menu {
        position: relative;
		width: 0;
    }

   .navbar-custom-menu .navbar-nav li.status-menu > a {
        padding: 0 !important;
		position: absolute;
		right: 13px;
		bottom: 16px;
        z-index: 333;
        line-height: 0;
    }
	.main-header .navbar-custom-menu .navbar-nav li.status-menu > a:hover {
		background-color: transparent !important;
	}
    .user-oc-status {
        font-size: 8px;
        border: 2px solid #eee;
        border-radius: 50%;
		vertical-align: bottom;
    }
    .user-oc-status.status-online {
        color: #5f9323;
    }
    .user-oc-status.status-away {
        color: #ffba00;
    }
    .user-oc-status.status-dnd {
        color: #ff0000;
    }
    .user-oc-status.status-invisible {
        color: #ccc;
    }




</style>