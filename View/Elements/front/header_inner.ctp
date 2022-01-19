<?php $header_time_start = microtime_float();
if(checkPasswordExp($this->params['action']) == false && $this->params['action'] != "changepassword" && $this->Session->read('Auth.User.role_id') == 2 && $_SERVER['SERVER_ADDR'] != '192.168.7.20' ){
	header('location:'.SITEURL.'users/changepassword');
	exit;
}

// $match_token = $this->User->check_user_token($this->Session->read('Auth.User.id'), $_SESSION['stoken']);
// if(!$match_token){
    // header('location:'.SITEURL.'users/logout');
    // exit;
// }

$force_logout = force_logout($this->Session->read('Auth.User.id'));
if($force_logout){
    header('location:'.SITEURL.'users/logout');
    exit;
}

$auther = two_factor_check();
if(isset($auther ) && !empty($auther )){


if(!isset($_SESSION['check_secrets']) || $_SESSION['check_secrets'] < 1) {

	if(LOCALIP != $_SERVER['SERVER_ADDR']){

	header('location:'.SITEURL.'subdomains/auth');
	exit;
	}
}

}


?>

<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/v4-shims.css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.0/css/v4-shims.min.css">

<!-- Modal Confirm -->
<div class="modal modal-success fade" id="modal_people" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-people">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade" id="modal_nudge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<header class="main-header <?php echo $user_theme; ?>" data-theme="<?php echo $user_theme; ?>">
    <a href="#" class="sidebar-toggle"  title="Navigation Menu" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Logo -->

    <a  href="<?php echo current_landing_page(); ?>"  class="logo">
		<span class="logo-text">
		<?php $application_name = application_name();
			if(isset($application_name['UserDetail']['theme_name']) && !empty($application_name['UserDetail']['theme_name'])){
				echo $application_name['UserDetail']['theme_name'];
			}else{
				echo "OpusView";
			}
		?>

		</span>
            <?php
            if ($user_theme == 'theme_default') {
                $logo_file = 'logo_white.svg';
            } else {
                $logo_file = 'logo_white.svg';
            }
        ?>

    </a>

    <a class="btn btn-default toggle_header_menus" href="#">
        <span class="fa fa-bars"></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
				<?php
					 if( $this->Session->read('Auth.User.role_id') == 1 ){
				?>
					<li class="dropdown"><a href="javascript:void(0)" class="dropdown-toggle   drop-icon" data-toggle="dropdown" >
						<span class="nav-icon-all"><i class="icon-size-nav ico-work"></i></span>
						</a>
						<ul class="dropdown-menu admin-drop-menu">
							<li  ><a  href="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', 0 ), TRUE); ?>" class="" ><i class="app-center-menu-all-icon app-center-work-center"></i> Knowledge Center </a></li>
						</ul>
					</li>
				<?php } else {

					if( $this->Session->read('Auth.User.role_id') != 3 ) {
                ?>

                <!-- WORK MENU -->
                <?php echo $this->element('front/work_menus'); ?>

                <!-- Reward Notifications -->
                <?php echo $this->element('front/reward_notifications'); ?>

                <!-- TO-DO MENU -->
                <?php //echo $this->element('front/todo_menus'); ?>

                <!-- NUDGE NOTIFICATION MENU -->
                <?php echo $this->element('front/nudge_notifications'); ?>

                <!-- REQUESTS MENU -->
				<?php echo $this->element('front/request_pages'); ?>

                <!-- REMINDER -->
                <li class="">
                    <a href="<?php echo Router::Url( array( "controller" => "dashboards", "action" => "task_reminder", 'admin' => FALSE ), true ); ?>" id="reminder_menu" class="tipText" title="Reminders">
                        <span class="nav-icon-all">
							<i class="icon-size-nav alarm"></i>
                            <?php
                            $reminder_elements = element_reminder($this->Session->read('Auth.User.id'));
                            if(isset($reminder_elements) && !empty($reminder_elements)){  ?>
                                <i class="bg-gray counter header-counter hr-count"><?php /* variable $reminder_elements set in AppController.php */ echo count($reminder_elements); ?></i>
                            <?php } ?>
                        </span>
                    </a>
                </li>
                <!-- REMINDER -->


                <?php if(chat_enabled()){ ?>
				    <li class=""><a href="javascript:void(0)" id="chat_icon" class="tipText  dropdown-toggle dropdown-submenu" title="Talk Now" data-toggle="dropdown" ><span class="nav-icon-all"><i class="icon-size-nav chat-bubble"></i><i class="bg-gray counter header-counter" style="display: none;">0</i></span></a></li>
                <?php } ?>

                <!-- NOTIFICATION MENU -->
                <?php echo $this->element('front/general_notifications'); ?>
                <!-- NOTIFICATION MENU END -->


                <!-- JANI -->
                <?php /* ?><li class="">
                    <a href="#" class="tipText open-jai" title="Assistant" data-toggle="modal" data-target="#jai_modal" data-remote="<?php echo Router::Url( array( 'controller' => 'dashboards', 'action' => 'open_jani', 'admin' => FALSE ), TRUE ); ?>" onclick='responsiveVoice.speak("  ");'>
                        <span class="nav-icon-all">
                           <i class="icon-size-nav assistant-nav"></i>
                        </span>
                    </a>
                </li><?php */ ?>
                <!-- JANI -->


                <?php /* ?><li><a href="<?php echo SITEURL."searches"; ?>" class="tipText" title="Search"><span class="nav-icon-all"><i class="icon-size-nav search-nav"></i> </a></li><?php */ ?>

                <?php
                    if ( (isset($project_id) && !empty($project_id)))
                    $dataOwner = $this->ViewModel->projectPermitType($project_id , $this->Session->read('Auth.User.id') );
                    if ( (isset($project_id) && !empty($project_id) && $this->params['controller'] != 'costs')  && $dataOwner == 1 ) {
                ?>
                <?php
                    } else if ( (isset($project_id) && !empty($project_id))){
                    if($dataOwner == 1){ ?>
					<li class="dropdown" id="exportdatafiles" style="display:none;">
                       <a href="#" class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" title="Export"><i class="icon-file-export" style="    font-size: 16px;"></i></a>
                        <ul class="dropdown-menu">
							<?php if( isset($project_id) && !empty($project_id) ){ ?>
							<li>
                                <a data-toggle="modal" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_doc" href="<?php echo SITEURL;?>export_datas/index/project_id:<?php echo isset($project_id) ? $project_id : '' ;?>" rel="tooltip" >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>
                            </li>
                            <?php }
							if( isset($this->params['controller']) && $this->params['controller'] == 'costs'){ ?>
                            <li>
								<a data-toggle="modal" id="exportdatasw" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_xls" href="<?php echo SITEURL;?>costs/export_xls/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-excel-o text-black"></i>MS Excel
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php }
					}else{ ?>
                    <li class="dropdown" id="exportdatafiles" style="display:none;">
                       <a href="#" class="dropdown-toggle tipText drop-icon" data-toggle="dropdown" title="Export"><i class="icon-file-export" style="font-size: 16px;"></i></a>
                        <ul class="dropdown-menu">
							<?php if( isset($project_id) && !empty($project_id) ){ ?>
							<li>
                                <a data-toggle="modal" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_doc" href="<?php echo SITEURL;?>export_datas/index/project_id:<?php echo isset($project_id) ? $project_id : '' ;?>" rel="tooltip" >
                                    <i class="fa fa-file-word-o text-black"></i>MS Word
                                </a>
                            </li>
                            <?php }
							if( isset($this->params['controller']) && $this->params['controller'] == 'costs'){ ?>
                            <li>
								<a data-toggle="modal" id="exportdatasw" data-target="#modal_medium" data-modal-width="600" class="tipTexts msword_xls" href="<?php echo SITEURL;?>costs/export_xls/project_id:<?php echo $project_id;?>" rel="tooltip" >
                                    <i class="fa fa-file-excel-o text-black"></i>MS Excel
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php }
                }
                ?>

                <li class="dropdown "><a  data-toggle='dropdown' class="dropdown-toggle drop-icon tipText" title="Help" data-placement="bottom"  href="javascript:void(0)" >
                    <span class="nav-icon-all"><i class="icon-size-nav help-nav"></i></span> </a>
                    <ul class="dropdown-menu" style="min-width: 235px;">
                       <!-- <li><a href="<?php echo SITEURL.'docs/user/index.htm';?>" target="_blank" class="tipText" >Online Help</a></li>-->
                       <?php /* <li class=""><a target="_blank" href="https://help.opusview.com/">Online Help</a></li> */ ?>
                        <li class=""><a target="_blank" href="https://ideascast.freshdesk.com/support/login">Support</a></li>
                        <li class=""><a href="https://www.opusview.com/contactus" target="_blank">Contact Us</a></li>
                        <li class="mega-dropdown show_hide_about"><a href="javascript:void(0);" class="dropdown-toggle">About OpusView</a></li>
                        <li style="" id="about_ideascost">
                            <div class="">
                                <div class="i-about-top">
                                <img src="<?php echo SITEURL; ?>images/help_about_logo.png" alt="about-green-logo">

                                </div>
                                    <div class="i-about-mid">Version: <?php
									if( !empty(DOMAIN_JEERA_VERSION) ){
										echo DOMAIN_JEERA_VERSION;
									} else {
										echo "1.0";
									}
									?></div>
                                    <div class="i-about-bottom"> &copy; <?php echo date('Y')?> IdeasCast Limited. All rights reserved.</div>
                            </div>
                        </li>

                    </ul>
                </li>

                <!-- SIGNOUT -->
                <li class=" nav-more-link">
                    <a href="#" class="tipText user-signoff" title="Sign Out">
                        <span class="nav-icon-all">
                            <i class="icon-size-nav hsignoff"></i>
                        </span>
                    </a>
                </li>
                <!-- SIGNOUT -->



                <!--  USER PROFILE MENUS -->
                <?php echo $this->element('front/location_menus'); ?>

                <!--  USER PROFILE MENUS -->
                <?php echo $this->element('front/user_menus'); ?>

                <?php
                }
                ?>
            </ul>
        </div>
    </nav>
</header>

<!--   Record Edit Box   -->
<div class="modal fade" id="Recordedit" tabindex="-1" role="dialog"  aria-hidden="true"></div>

<!-- Modal -->
<div class="modal modal-success fade" id="popup_modal_start" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<!-- Modal -->
<div class="modal modal-success fade" id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog profile-view-header modal-lg">
    <!-- <div class="modal-dialog modal-lg profile-hack"> -->
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<!-- Modal -->
<div class="modal modal-success fade" id="modal_notifications" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<div class="modal fade" id="Recordview" tabindex="-1" role="dialog" aria-hidden="true"></div>

<!-- Record Delete Alert Message -->
<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true">
<?php echo $this->Form->create('', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordDeleteForm')); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure, you would like to delete this record?</h4>
            </div>
            <input type="hidden" id="recordDeleteID" name='data[id]' />
            <div class="modal-footer clearfix bordertopnone">
                    <button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Delete</button>
                    <button type="button"  class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->


<div class="modal fade" id="StatusBox" tabindex="-1" role="dialog" aria-hidden="true">
<?php echo $this->Form->create('', array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordStatusFormId')); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Are you sure, you would like to <span id="statusname"></span> this record?</h4>
            </div>
            <input type="hidden" id="recordID" name='data[id]' />
            <input type="hidden" id="recordStatus" name='data[status]' />
            <div class="modal-footer clearfix bordertopnone">
                <button type="submit" class="btn btn-success"><i class="fa fa-fw fa-check"></i> Yes</button>
                <button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->

<!-- Modal Small -->
<div class="modal modal-success fade " id="smallModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content"></div>
    </div>
</div>
<!-- /.modal -->


<div class="modal modal-success fade" id="modal_info" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-radius">
            <div class="modal-header"  >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"> Information </h3>
            </div>
            <div class="modal-body"> </div>
            <div class="modal-footer"  >
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-success fade" id="popup_model_box"  tabindex="-1">
    <div class="modal-dialog modal-lg1 add-update-modal">
        <div class="modal-content">
        </div>
    </div>
</div><!-- /.modal -->


<script type="text/javascript" > var SITEURL = '<?php echo SITEURL; ?>'</script>
<?php
$header_time_end = microtime_float();
$header_load_time = $header_time_end - $header_time_start;
 ?>
<input type="hidden" name="sdfas" id="header_load_time" value="<?php echo $header_load_time; ?>" >
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="modal_medium" class="modal modal-success fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content"></div>
    </div>
</div>

<?php echo $this->Html->script(['projects/header_inner.min']); ?>

<?php

if( $this->request->params['controller'] != 'users' &&  $this->request->params['action'] != 'event_gantt'){
echo $this->Html->css('bootstrap-dialog/bootstrap-dialog.min');
echo $this->Html->script('bootstrap-dialog/bootstrap3.3.5.min', array('inline' => true));
echo $this->Html->script('bootstrap-dialog/bootstrap-dialog.min', array('inline' => true));
}

echo $this->Html->script(['projects/push.notification.js']); ?>

