<div class="signout-overlay"></div>
<?php
echo $this->element('../Elements/front/chat_7');

$sidebar_time_start = microtime_float();

?>
<!--<script src="https://code.responsivevoice.org/responsivevoice.js?key=CzvQSbu3"></script>-->
<?php //echo $this->Html->script('projects/responsivevoice11', array('inline' => true)); ?>
<script type="text/javascript" >
$(function () {
	$.checkDispalypage = $.reloadProfile = false;
	<?php
		if( $this->request->params['controller'] == 'skills' ||  $this->request->params['action'] == 'domain_settings' || $this->request->params['action'] == 'manage_users' || $this->request->params['action'] == 'domain_list' || $this->request->params['action'] == 'client_email_domain' || $this->request->params['action'] == 'client_manage_users' ) {
	?>
		$.checkDispalypage = true;
	<?php } ?>

	$('#modal_small').on('hidden.bs.modal', function(e){
		$(this).find('.modal-content').html('')
		$(this).removeData('bs.modal')
	})

})






</script>



<style>
	.popover .pop_para {
		font-size: 12px;
		font-weight: 500;
		margin-bottom: 2px;
		display: block;
	}
	.popover .pop_para a {
		display: block;
		font-weight: 600;
		font-size: 13px;
		color: #333333;
		text-decoration: none;
		margin-bottom: 5px;
	}
	.popover .pop_para a:hover {
		color: #333333;
	}
	.popover .pop_para.user {
		font-size: 14px;
		margin-bottom: 2px;
	}
	.popover .pop_para.user.brdrBtm {
		padding-bottom: 5px;
		border-bottom: 1px solid #ccc;
	}
	.popover .pop_para.brdrTop {
		padding: 5px 0;

	}
	.popover .pop_para.bordrTop {
		border-top: solid 1px #ccc;
		padding: 5px 0 0 0 ;

	}
	.popover .arrow {
		z-index: 99999999 !important;
	}
	.popover p:first-child {
		font-weight: 600 !important;
		width: 170px !important;
	}

	.white-tooltip + .tooltip > .tooltip-inner { background-color: #fff; color: #333; }
	.white-tooltip + .tooltip > .tooltip-arrow { border-bottom-color:#fff; color: #333; }

	.reminder_popup_trigger { display: none;  }


	.ellipsisorgname {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		width:106px;
	}
	.pull-right.lhs-counters {
	    margin-right: 10px;
	}
	.currentproject{
		display:none;
	}

	.current_task a {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		width: 100%;
	}
</style>
<script type="text/javascript">
	$(function(){
		$('#reminder_modal').on('hidden.bs.modal', function(e){
	        $(this).removeData('bs.modal');
	        $(this).find('.modal-content').html("");
	    })
	})
</script>
<!-- <a href="" id="user_profile_click" data-toggle="modal" data-target="#user_profile_modal" data-remote="<?php //echo Router::Url( array( 'controller' => 'settings', 'action' => 'user_profile', 'admin' => FALSE ), TRUE ); ?>"></a> -->
<div class="modal modal-success fade" id="user_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content modal-lg"></div>
	</div>
</div>

<div class="modal modal-success fade" id="user_images_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content modal-lg"></div>
	</div>
</div>

<div class="modal modal-success fade" id="reminder_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="todays_reminder_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-default fade" id="jai_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal modal-success fade" id="availability_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<a href="#" class="reminder_popup_trigger" data-target="#reminder_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'settings', 'action' => 'show_reminder', 'admin' => FALSE ), TRUE ); ?>" ></a>
<a href="#" class="todays_reminder_popup_trigger" data-target="#todays_reminder_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'dashboards', 'action' => 'show_todays_reminder', 'admin' => FALSE ), TRUE ); ?>" ></a>

<?php
	$current_user_id = $this->Session->read('Auth.User.id');

	$currentUserTasks = $this->Permission->currentUserTasks();

	$projid = 0;
	if (isset($project_id) && !empty($project_id)) {
		$projid = $project_id;
	}
	else if(isset($_sidebarProjectId) && !empty($_sidebarProjectId)) {
		$projid = $_sidebarProjectId;
	}
	else if(isset($this->params['named']['project']) && !empty($this->params['named']['project'])){
		$projid = $this->params['named']['project'];
	}
?>
<script type="text/javascript">
	$(function(){
		$.current_project_id = '<?php echo $projid; ?>';
		$.load_talk_now = function(){
	        var dfd = new $.Deferred();
			$.ajax({
	            type: 'POST',
	            data: {project_id: $.current_project_id},
	            url: $js_config.base_url + 'projects/talk_now',
	            success: function(response) {
	            	$('.talk-now-menu').html(response);
	                dfd.resolve();
	            }
	        });
	        return dfd.promise();
		}
	})
</script>

        <div class="calling-overlay" style="display: none;">
            <div class="calling-ui">
                <div class="call-header-sec">
                <span class="call-profile"><img src=""></span>
                    <h4 class="caller-name"></h4>
                    <h6>Calling...</h6>
                </div>
                <div class="call-btn-group">
                    <button class="btn call-video tipText" title="Accept Call with Video" style="display: none;"><i class="video-camera"></i></button>
                    <button class="btn call-on tipText" title="Accept Call with Audio"><i  class="icon-phone-call"></i></button>
                    <button class="btn call-end tipText" title="Decline Call"><i  class="icon-phone-call"></i></button>
                </div>
            </div>
        </div>


  	<aside class="main-sidebar <?php echo $user_theme; ?>" data-theme="<?php echo $user_theme; ?>">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebars">
	<?php //if( $this->Session->read('Auth.User.role_id') != 3 ){
	 if( $this->Session->read('Auth.User.role_id') == 2 ){
	 	$sidebar_menu_status = sidebar_menu_status($this->Session->read('Auth.User.id'));
	  ?>
		<div id="sideMenu" class="normal-lists sideMenu">
			<ul class="sidebar-menu project_workspaces" id="sidebar_menu">
				<!-- <div class="new-projectd"> -->
                <li><span class="sep1 sp-mt-0"></span></li>
                <li class="menu-section-heading"><a href="#" class="section-submenus  <?php if($sidebar_menu_status['navigation']){echo 'up';}else{echo 'down';} ?>"  data-target=".navigation-submenu" data-type="navigation_collapse">NAVIGATION <i class="section-arrow-down"></i></a></li>
                 <div class="menutoggles-section navigation-submenu" <?php if(!$sidebar_menu_status['navigation']){echo 'style="display: none;"';} ?>>


                <li class="dropdown browse-nav-left">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-original-title="Browse">
                        <span class="left-icon-all"><i class="left-nav-icon browse-icon"></i> </span> Browse  <span class="menu-arrow"></span>
                    </a>
                </li>

                <li class="dropdown recent-nav-left">
                	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-original-title="Recent"> <span class="left-icon-all"><i class="left-nav-icon recent-icon"></i> </span> Recent  <span class="menu-arrow"></span></a>
                </li>

                <li class="dropdown bookmarks-nav-left">
                	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-original-title="Rewards"> <span class="left-icon-all"><i class="left-nav-icon bookmarks-nav"></i> </span> Bookmarks  <span class="menu-arrow"></span></a>
                </li>


				</div>

				<li><span class="sep1"></span></li>
				<!-- </div>
				<div class="social"> -->
            	<li class="menu-section-heading"><a href="#" class="section-submenus <?php if($sidebar_menu_status['social']){echo 'up';}else{echo 'down';} ?>" data-target=".social-submenu" data-type="wsp_collapse">Social <i class="section-arrow-down"></i></a></li>
					<div class="menutoggles-section social-submenu " <?php if(!$sidebar_menu_status['social']){echo 'style="display: none;"';} ?>>
						<li><a  href="<?php echo SITEURL?>analytics/social" > <span class="left-icon-all"><i class="left-nav-icon social-analytics-nav"></i> </span> Social Analytics </a>


						</li>



						<li>
							<a data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $this->Session->read('Auth.User.id'), 'admin' => FALSE ), TRUE ); ?>"  data-target="#popup_modal" data-toggle="modal" href="#"><span class="left-icon-all"><i class="left-nav-icon left-profile-icon"></i></span> My Profile </a>
						</li>

                        <li><a href="<?php echo Router::url(['controller' => 'shares', 'action' => 'my_sharing#user_view', 'admin' => FALSE ], true); ?>"> <span class="left-icon-all"><i class="left-nav-icon mysharing-nav"></i> </span> My Sharing</a></li>

						<li class="dropdown mynudge-nav-left"><a href="<?php echo Router::url(['controller' => 'boards', 'action' => 'nudge_list', 'admin' => FALSE ], true); ?>"  > <span class="left-icon-all"><i class="left-nav-icon mynudge-icon"></i> </span> My Nudges</a></li>

                        <li class="dropdown mynudge-nav-left"><a href="<?php echo Router::url(['controller' => 'tags', 'action' => 'my_tags', 'admin' => FALSE ], true); ?>"> <span class="left-icon-all"><i class="left-nav-icon mytag-icon"></i> </span> My Tags</a></li>

						<li class="dropdown myreward-nav-left"><a href="#" class="dropdown-toggle" data-toggle="dropdown" data-original-title="Rewards"> <span class="left-icon-all"><i class="left-nav-icon myreward-icon"></i> </span>   Rewards  <span class="menu-arrow"></span></a></li>

						<?php if(chat_enabled()){ ?>
						<li class="dropdown talknow-nav-left talk-now-menu" >
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-original-title="Talk Now">
			    				<span class="left-icon-all"><i class="left-nav-icon talknow-nav"></i> </span> Talk Now
								<span class="menu-arrow"></span>
			    			</a>
						</li>
						<?php } ?>


					</div>

				<li><span class="sep1"></span></li>

				<li class="menu-section-heading "><a href="#" class="section-submenus <?php if($sidebar_menu_status['delivery']){echo 'up';}else{echo 'down';} ?>" data-target=".delivery-submenu" data-type="shr_collapse">Delivery <i class="section-arrow-down"></i></a></li>
					<div class="menutoggles-section delivery-submenu" <?php if(!$sidebar_menu_status['delivery']){echo 'style="display: none;"';} ?>>


				<li><a href="<?php echo Router::url(['controller' => 'boards', 'action' => 'opportunity',  'admin' => FALSE ], true); ?>"> <span class="left-icon-all"><i class="left-nav-icon social-board-icon"></i> </span> Opportunities</a></li>

						<?php
						$total_programs = get_programs_count($current_user_id);
						?>
						<li class="menu-my-programs"><a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'lists', 'admin' => FALSE ], true); ?>"><span class="left-icon-all"> <i class="left-nav-icon my-programs-icon"></i> </span> My Programs <span class="pull-right lhs-counters"><?php echo $total_programs; ?></span></a></li>

						<li ><a href="<?php echo Router::url(['controller' => 'projects', 'action' => 'lists', 'tab' => 'tab_projects', 'admin' => FALSE ], true); ?>"><span class="left-icon-all"> <i class="left-nav-icon project-left-icon"></i> </span> My Projects <span class="user_projects_total pull-right lhs-counters"><?php echo ( isset($project_lists) && !empty($project_lists) ) ? count($project_lists) : 0; ?></span></a></li>

						<?php
						if( isset($currentUserTasks) && !empty($currentUserTasks) ){
						$my_task =  Router::url(['controller' => 'dashboards', 'action' => 'task_centers','status'=>8, 'assigned'=>$this->Session->read("Auth.User.id"),'admin' => FALSE ], true);
							$my_task =  TASK_CENTERS.'status:8/assigned:'.$this->Session->read("Auth.User.id");

						}else{
							$my_task =  TASK_CENTERS;
						}

						?>

						<li class="mytasksCount"><a href="<?php echo $my_task; ?>"><span class="left-icon-all"> <i class="left-nav-icon task-nav"></i> </span> My Tasks <span class="user_projects_total pull-right lhs-counters"><?php echo ( isset($currentUserTasks) && !empty($currentUserTasks) ) ? $currentUserTasks : 0; ?></span></a></li>

						<?php
						$user_risks = my_risks($this->Session->read("Auth.User.id"), 'my');
						$user_risks = (isset($user_risks) && !empty($user_risks)) ? count($user_risks) : 0;

						?>

						<li><a href="<?php echo Router::url(['controller' => 'risks', 'action' => 'index', 'project' => 'my', 'admin' => FALSE ], true); ?>"><span class="left-icon-all"> <i class="left-nav-icon my-risks"></i> </span> My Risks <span class="pull-right lhs-counters my-risk-counter"><?php echo $user_risks; ?></span></a></li>

						<?php $total_my_groups = get_my_groups($this->Session->read('Auth.User.id'), true); ?>
						<li><a href="<?php echo SITEURL?>shares/my_groups"><span class="left-icon-all"><i class="left-nav-icon my-groups"></i></span> My Groups <span class="pull-right lhs-counters"><?php echo $total_my_groups; ?></span></a></li>

                        </li>

	                </div>

				<li><span class="sep1"></span></li>
             	<li class="menu-section-heading"><a href="#" class="section-submenus <?php if($sidebar_menu_status['assets']){echo 'up';}else{echo 'down';} ?>" data-target=".assets-submenu" data-type="request_collapse">RESOURCES <i class="section-arrow-down"></i></a></li>
					<div class="menutoggles-section assets-submenu" <?php if(!$sidebar_menu_status['assets']){echo 'style="display: none;"';} ?>>
             			<li><a href="<?php echo Router::Url( array( 'controller' => 'subdomains', 'action' => 'knowledge_analytics', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon icon-knowledge-analitics"></i></span> Capability Analytics </a></li>
						 <li><a href="<?php echo Router::Url( array( 'controller' => 'resources', 'action' => 'planning ', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon icon-planning"></i></span> Planning  </a></li>
						<li><a href="<?php echo Router::Url( array( 'controller' => 'communities', 'action' => 'index', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon icon-community-white"></i></span> Community </a></li>
						<li><a href="<?php echo Router::Url( array( 'controller' => 'searches', 'action' => 'people', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon people-icon"></i></span> People </a></li>
             			<li><a href="<?php echo Router::Url( array( 'controller' => 'competencies', 'action' => 'index', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon competency-m"></i></span> Competencies </a></li>

						<li><a href="<?php echo Router::Url( array( 'controller' => 'stories', 'action' => 'index', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon stories-m"></i></span> Stories </a></li>




             			<li><a href="<?php echo Router::Url( array( 'controller' => 'templates', 'action' => 'create_workspace', 0, 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon knowledge-library-m"></i></span> Knowledge Library </a></li>
                	</div>

                <li><span class="sep1"></span></li>
				<?php
				$li_styles = " ";
				$trigger_class1 = 'down';
				if( $this->request->params['controller'] == 'skills' || $this->request->params['controller'] == 'konwledge_domains' || $this->request->params['controller'] == 'subjects'  ||  $this->request->params['action'] == 'domain_settings' || $this->request->params['action'] == 'manage_users' || $this->request->params['action'] == 'domain_list' || $this->request->params['action'] == 'client_email_domain' || $this->request->params['action'] == 'client_manage_users' || ($this->request->params['controller'] == 'organisations' && $this->request->params['action'] == 'listings') ){
					$li_styles = '';
					$trigger_class1 = 'up';
				}

				if( $this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') == 1 ){ ?>
				<li class="menu-section-heading"><a href="#" class="section-submenus <?php echo $trigger_class1;?>" data-target=".admin-submenu" >ADMINISTRATION <i class="section-arrow-down"></i></a></li>

					<div class="menutoggles-section admin-submenu" <?php if( $trigger_class1 == 'down' ){?> style="display: none;"<?php } ?> >
             			<li><a href="<?php echo Router::Url( array( 'controller' => 'organisations', 'action' => 'manage_users', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon icon-manage-users"></i></span> Manage Users </a></li>
         			<li><a href="<?php echo Router::Url( array( 'controller' => 'organisations', 'action' => 'listings', 'admin' => FALSE ), TRUE ); ?>"><span class="left-icon-all"><i class="left-nav-icon icon-admin-listing"></i></span> Lists </a></li>


					</div>
				<li><span class="sep1"></span></li>
				<?php } ?>
			</ul>


		<?php }

		$displaysh = 'display: none';
		$menucls ='';
		$activeClass ='';
		if( $this->request->params['action'] == 'domain_settings' || $this->request->params['action'] == 'manage_users' || $this->request->params['action'] == 'skills' || $this->request->params['action'] == 'domain_list'   ){
			$displaysh = 'display: block';
			$menucls = 'menu-open';
			$activeClass = 'active';
		}

		if( ($this->Session->read('Auth.User.role_id')==3) ) { ?>

			<!-- <div id="sideMenu" class="normal-lists sideMenu"> -->
			<ul class="sidebar-menu" id="">
				<li <?php if( $this->request->params['action'] == 'dashboard' ){?>class="active"<?php } ?>>
					<a href="<?php echo SITEURL; ?>organisations/dashboard" data-original-title="" title="">
						<span class="left-icon-all"><i class="sac-icon dashboard-white"></i></span> <span class="name">Dashboard</span>
					</a>
				</li>
			<?php
			$li_style = 'style="display:none"';
			$trigger_class = 'fa-long-arrow-down';
			if( $this->request->params['action'] == 'domain_settings' ||  $this->request->params['action'] == 'manage_users' ||  $this->request->params['action'] == 'password_policy'  ||  $this->request->params['action'] == 'general_settings' || $this->request->params['action'] == 'appearance'  || $this->request->params['action'] == 'user_settings' ){
				$li_style = '';
				$trigger_class = 'fa-long-arrow-up';
			?>
			<?php }

			$li_style2 = 'style="display:none"';
			$trigger_class2 = 'fa-long-arrow-down';
			if( $this->request->params['controller'] == 'app_assets' ||  $this->request->params['controller'] == 'app_users'   ){
				$li_style2 = '';
				$trigger_class2 = 'fa-long-arrow-up';
			}
			?>
				<?php
						if( $this->Session->read('Auth.User.role_id')==3 ){
						$orgUsers = SITEURL."organisations/orgownerdetail/".$this->Session->read('Auth.User.id');
					?>
					<li <?php //echo($li_style); ?> class="adm_submenus"><a href="javascript:;" id="trigger_edit_profile" data-target='#popup_modal_start' data-remote="<?php echo $orgUsers; ?>" data-toggle="modal"><span class="left-icon-all"><i class='sac-icon accountwhite'></i></span> Account</a></li><?php } ?>


			</ul>

			<ul class="sidebar-menu " id="">
				<li id="adm_menu_role_3">
					<a style="font-weight: bold" href="javascript:void(0);">
						<span class="left-icon-all"><i class="sac-icon <?php echo $trigger_class; ?> arrow-ico"></i></span> <span class="name">Administration</span>
					</a>
				</li>
					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'appearance' ){?>active<?php } ?> adm_submenu">
					<a href="<?php echo SITEURL?>organisations/appearance">
						<span class="left-icon-all"><i class="user-menu-all-icon appearance-brush"></i></span> Appearance
					</a>
				</li>

					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'domain_settings' ){?>active<?php } ?> adm_submenu"><a href="<?php echo SITEURL; ?>organisations/domain_settings"><span class="left-icon-all"><i class="sac-icon emaildomainwhite"></i></span> Email Domains</a></li>
					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'manage_users' ){?>active<?php }  ?> adm_submenu"><a href="<?php echo SITEURL; ?>organisations/manage_users"><span class="left-icon-all"><i class="user-menu-all-icon user-menu-manage-users-white"></i></span> Manage Users</a></li>

					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'user_settings' ){?>active<?php }  ?> adm_submenu"><a href="<?php echo SITEURL; ?>organisations/user_settings"><span class="left-icon-all"><i class="user-menu-all-icon user-menu-settings-white"></i></span> User Settings</a></li>



					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'password_policy' ){?>active<?php }  ?> adm_submenu"><a href="<?php echo SITEURL; ?>organisations/password_policy"><span class="left-icon-all"><i class="user-menu-all-icon security-white"></i></span> Security</a></li>

					<li <?php echo($li_style); ?> class="<?php if( $this->request->params['action'] == 'general_settings' ){?>active<?php }  ?> adm_submenu"><a href="<?php echo SITEURL; ?>organisations/general_settings"><span class="left-icon-all"><i class="sac-icon cogwhite"></i></span> General Settings</a></li>

					<!-- <li class=""><a href="<?php echo SITEURL; ?>organisations/password_policy"><i class="fa fa-unlock-alt"></i> Password Policy</a></li> -->
			</ul>
			<?php /*
			$flagORg = $this->Common->domain_list();
			if($flagORg == true){
			?>
			<ul class="sidebar-menu " id="sidebar_menu2">
				<li class="treeview <?php if( $this->request->params['action'] == 'domain_list' ){?> active<?php } ?>" >
					<a href="<?php echo SITEURL; ?>organisations/domain_list">
						<span class="left-icon-all">  <i class="user-menu-all-icon user-menu-linked-domains-white"></i></span> <span class="name">Linked Domains</span>
					</a>
				</li>
			</ul>
			<?php } */ ?>
			<ul class="sidebar-menu" id="">
				<li id="adm_menu_role_3" class="api-menu">
					<a style="font-weight: bold" href="javascript:void(0);">
						<span class="left-icon-all"><i class="sac-icon arrow-ico <?php echo $trigger_class2; ?>"></i></span> <span class="name">API</span>
					</a>
				</li>
				<li class="<?php if( $this->request->params['controller'] == 'app_users' ){?>active<?php }  ?> adm_submenu" <?php echo($li_style2); ?>><a href="<?php echo SITEURL; ?>app_users"><span class="left-icon-all"><i class="sac-icon plugwhite"></i></span> Users</a></li>
				<li class="<?php if( $this->request->params['controller'] == 'app_assets' ){?>active<?php }  ?> adm_submenu" <?php echo($li_style2); ?>><a href="<?php echo SITEURL; ?>app_assets"><span class="left-icon-all"><i class="sac-icon cablewhite"></i></span> Assets</a></li>
			</ul>
			<!--<ul class="sidebar-menu" id="">

			</ul>-->
		<!-- </div> -->
		<?php }
		 if(($this->Session->read('Auth.User.role_id')==1) ){ ?>
				<div id="sideMenu" class="normal-lists sideMenu">
					<ul class="sidebar-menu" id="sidebar_menu1">
						<li>
							<a href="<?php echo SITEURL?>dashboard">
								<span class="left-icon-all"><i class="fa fa-long-arrow-left"></i></span> Back to Admin
							</a>
						</li>
					</ul>
				</div>
		<?php } ?>
		</div>
        </section>
        <!-- /.sidebar -->
      </aside>
<?php
$sidebar_time_end = microtime_float();
$sidebar_time = $sidebar_time_end - $sidebar_time_start;
?>

<input type="hidden" id="leftsidebar_time" value="<?php echo $sidebar_time;?>" >


<ul class="browse-nav-dropdown browse-main"> </ul>

<ul class="talk-nav-dropdown talk-main"> asfsdfdfdff</ul>


<ul class="recent-nav-dropdown recent-main">
	<li data-type="recent-project" class="recent-list">
	    <a href="#" class="recent-title">
			<span class="left-icon-all"><i class="left-nav-icon projectblack"></i></span>
	        <span class="recentmenutext recent-to-link" >Projects</span>
	        <span class="recentmenuarrow"></span>
	    </a>
	</li>
	<li data-type="recent-wsp" class="recent-list">
	    <a href="#" class="recent-title">
			<span class="left-icon-all"><i class="left-nav-icon wspblack"></i></span>
	        <span class="recentmenutext recent-to-link" >Workspaces</span>
	        <span class="recentmenuarrow"></span>
	    </a>
	</li>
	<li data-type="recent-tasks" class="recent-list">
	    <a href="#" class="recent-title">
			<span class="left-icon-all"><i class="left-nav-icon taskblack"></i></span>
	        <span class="recentmenutext recent-to-link" >Tasks</span>
	        <span class="recentmenuarrow"></span>
	    </a>
	</li>
	<li data-type="recent-assets" class="recent-list">
	    <a href="#" class="recent-title">
			<span class="left-icon-all"><i class="left-nav-icon assetsblack"></i></span>
	        <span class="recentmenutext recent-to-link" >Assets</span>
	        <span class="recentmenuarrow "></span>
	    </a>
	</li>
</ul>

<ul class="dropdown-menu reward-abs-dd">
	<li class="">
        <div class="total-reward-h">
            <div class="icon-ov"></div> My Rewards Summary
        </div>
    </li>
    <li class="reward-graphs"></li>
</ul>

<ul class="bookmarksdropdown bookmarksdropdown-main">
	<!-- <li><a href="#" style="padding-left: 31px;"><span class="left-icon-all">Loading...</span></a></li> -->

	<li data-type="projects"><a href="#" class="book-title"><span class="left-icon-all"><i class="left-nav-icon bm-ProjectBlack"></i></span> <span class="bookmarksmenutext"> Projects </span>  <span class="bookmarksmenuarrow"></span> </a></li>
	<li data-type="workspaces"><a href="#" class="book-title"><span class="left-icon-all"><i class="left-nav-icon bm-wspBlack"></i></span> <span class="bookmarksmenutext"> Workspaces </span>  <span class="bookmarksmenuarrow"></span> </a></li>
	<li data-type="tasks"><a href="#" class="book-title"><span class="left-icon-all"><i class="left-nav-icon bm-Task-Black"></i></span> <span class="bookmarksmenutext"> Tasks </span>  <span class="bookmarksmenuarrow"></span> </a></li>

</ul>

<?php /* ?>
<ul class="to-dos-nav-dropdown">
	<?php echo $this->element('front/todo_right_menus'); ?>
</ul><?php */ ?>

<style>
	.adm_menu_role_2 a, .adm_menu_role_3 a {
		cursor: pointer;
	}
	.ws_togbn a, .request_list a, .adm_submenu a {
		padding: 12px 5px 12px 38px !important;
	}

	.img-circle {
	    display: inline-block;
		margin: 0 10px 0 0;
		border-radius:0%;
	}

	.browse-list:hover .browsemenuarrow {
		/*animation: arrow_move 1s ease-in-out infinite;*/
	}


	@keyframes arrow_move {
	  0%,
	  100% {
	    transform: translate(0, 0);
	  }

	  50% {
	    transform: translate(5px, 0);
	  }
	}

</style>
<script type="text/javascript" >
	$(function(){

		;($.sideBarScroll = function(height){
			$('#sidebar_menu.sidebar-menu.project_workspaces').slimScroll({
		        height: height,
		        size: '5px',
		        color: '#FFFFFF',
		        alwaysVisible: false,
		        opacity: 0.3,
		        position: 'right',
		        distance: '0px',
		    });
		})($(window).height() - 70);

	    $(window).resize(function(event) {
	    	var h = $(window).height() - 70;
    		$.sideBarScroll(h)
    		$('.normal-lists.sideMenu #scrollDiv').height(h);
      		$('.normal-lists.sideMenu .slimScrollDiv').height(h);
      		$('#sidebar_menu.sidebar-menu.project_workspaces').height(h);

	    });

		$('body').delegate('.anc_show_unavailability', 'click', function (event) {
			event.preventDefault();
			$.ajax({
				url: $js_config.base_url + 'settings/availability',
				type: 'get',
				dataType: 'html',
				global: false,
				success: function(response) {
					$('#availability_modal').find('.modal-content').html(response);
					$('#availability_modal').modal('show');
				}
			})
		})
		/*$('.current-status').tooltip({
            placement: 'bottom',
            container: 'body'
        })*/
	})

</script>

<?php echo $this->Html->script('projects/sidebar.min', array('inline' => true)); ?>