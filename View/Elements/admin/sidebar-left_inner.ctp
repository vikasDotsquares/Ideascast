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

</style>
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
		
		<?php 
		//$user_setting = '<ul class=""><li><a href="'.SITEURL.'sitepanel/settings/edit/1"><i class="fa fa-fw  fa-cog"></i>Settings</a></li><li><a href="'.SITEURL.'sitepanel/users/profile" class="editcompany" data-target=".myprofile"  data-toggle="modal"><i class="fa fa-fw fa-user"></i> Profile</a></li><li><a href="'.SITEURL.'sitepanel/users/logout"><i class="fa fa-fw fa-power-off"></i> Sign out</a></li></ul>';
		
		$user_setting = '<div>	
				<p class="pop_para"> 
					<a href="'.SITEURL.'sitepanel/settings/edit/1"><i class="fa fa-fw  fa-cog"></i>Settings</a>			
				</p>
				<p class="pop_para user ">
					<a href="'.SITEURL.'sitepanel/users/profile" class="editcompany" data-target=".myprofile"  data-toggle="modal"><i class="fa fa-fw fa-user"></i> Profile</a>
				</p>
				<p class="pop_para bordrTop user-signout"> <a href="'.SITEURL.'sitepanel/users/logout"><i class="fa fa-fw fa-power-off"></i> Sign out</a>
				</p>	
			</div>';
		
		?>
		
		<div class="fix-area user-settings" data-popover="true" data-html="true" data-content='<?php echo $user_setting; ?>'>
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">

			<?php 
			
			if(empty($name)){ $name = 'Admin'; }
			
			
			 $profile = $this->Session->read('Auth.Admin.User.UserDetail.profile_pic');
			
			if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)){
				$profiles = SITEURL.USER_PIC_PATH.$profile;
			}else{
				$profiles = SITEURL.'img/user2-160x160.jpg';
			}
			
			
			?>
			
			
			
              <img src="<?php echo $profiles ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->Session->read('Auth.Admin.User.UserDetail.first_name')." ".$this->Session->read('Auth.Admin.User.UserDetail.last_name'); ?></p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
			
			
          </div>

		 
          <!-- sidebar menu: : style can be found in sidebar.less -->
         <!-- <ul class="sidebar-menu">
		  
            <li class="header">MAIN NAVIGATION</li>
			
          </ul> -->
		  
		  </div>

		   <div id="sideMenu" class="normal-list sideMenu">
		  <ul class="sidebar-menu">
			<?php if($this->Session->read('Auth.Admin.User.role_id')==1){ ?>
             <!-- <li><a href="#"><i class="fa fa-dashboard"></i> Summary</a></li>
              <li><a href="#"><i class="fa fa-calendar"></i> Calendar</a></li>
              <li><a href="#"><i class="fa fa-folder"></i> Documents</a></li> -->
			  <li>
				<a class="active_bg" href="<?php echo SITEURL; ?>dashboard">
					<i class="fa fa-dashboard"></i> <span class="name">Dashboard</span>
				</a>
			 </li>
			 <li>
				<a href="<?php echo SITEURL; ?>sitepanel/organisations">
					<i class="fa fa-building"></i> <span class="name">Organizations</span>
				</a>
			 </li>
			 <li>
				<a href="<?php echo SITEURL; ?>sitepanel/users">
					<i class="fa fa-group"></i> <span class="name">Internal IdeasCast Users</span>
				</a>
			 </li>
			 <li>
				<a href="<?php echo SITEURL; ?>sitepanel/third_parties">
					<i class="fa fa-users"></i> <span class="name">Third Party Users</span>
				</a>
			 </li>
			 <li>
				<a href="<?php echo SITEURL; ?>templates/create_workspace/0/">
					<i class="app-center-menu-all-icon app-center-work-center"></i> <span class="name">Knowledge Center</span>
				</a>
			 </li>
			 <?php  			
			/* if( $_SERVER['REMOTE_ADDR'] == '111.93.41.194' ) { ?>
			 <li>
				<a  href="<?php echo SITEURL; ?>sitepanel/app_users/">
					<i class="ion-stats-bars"></i> &nbsp;&nbsp;  <span class="name">Api Users</span>
				</a>
			 </li>
			 <?php } 
			 <li>
				<a  href="<?php echo SITEURL; ?>sitepanel/users/all_transaction">
					<i class="ion-stats-bars"></i> &nbsp;&nbsp;  <span class="name">Orders</span>
				</a>
			 </li>*/ ?>
			 <!--<li>
				<a  href="<?php echo SITEURL; ?>/projects/lists">
				
				<a  href="javascript:void(0)">
					<i class="fa fa-folder-open"></i> <span class="name">Projects</span>
				</a>
			 </li> -->
			 
			 <?php /*<li class="treeview">
				<a href="<?php echo SITEURL; ?>sitepanel/users/thirdparty_users">
					<i class="fa fa-user"></i> <span class="name">Organisations</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-down pull-right"></i>
					</span>
				</a>
				   <ul class="treeview-menu" style="display: none;">
					   <li><a href="<?php echo SITEURL; ?>sitepanel/users/thirdparty_users"><i class="fa fa-circle-o"></i> Manage Users</a></li>
					    <li class=""><a href="<?php echo SITEURL; ?>sitepanel/organisations/domain_settings"><i class="fa fa-database"></i> Manage Domains</a></li>
					   <li><a href="<?php echo SITEURL; ?>sitepanel/organisations/domain_settings"><i class="fa fa-circle-o"></i> Password Policy</a></li> 
					</ul>
			 </li>*/?>
			 
			 
			 <!-- <li>
				<a href="<?php echo SITEURL; ?>sitepanel/users/institution_users">
					<i class="fa fa fa-building-o"></i> <span class="name">Institutions</span>
				</a>
			 </li> -->
			 
			
			 
			 <!--<li>
				<a href="<?php echo SITEURL; ?>sitepanel/currencies">
					<i class="fa  fa-dollar"></i> <span class="name">Currencies</span>
				</a>
			 </li>-->
			 <?php /*<li>
				<a href="<?php echo SITEURL; ?>sitepanel/pages">
					<i class="fa  fa-file-text"></i> <span class="name">Pages</span>
				</a>
				 <ul>
					<li><a href="<?php echo SITEURL; ?>sitepanel/sliders"><span class="name">Home Slider</span></a></li>
				</ul> 
			 </li>
			 <li>
				<a href="<?php echo SITEURL; ?>sitepanel/posts">
					<i class="fa fa-rss"></i> <span class="name">Blogs</span>
				</a>
			 </li>		*/ ?>	  
			 <!-- <li>
				<a href="<?php //echo SITEURL; ?>sitepanel/announcements">
					<i class="fa fa-flag"></i> <span class="name">Announcements</span>
				</a>
			 </li>-->
			 <!--<li>
				<a href="<?php echo SITEURL; ?>sitepanel/countries">
					<i class="fa fa-flag"></i> <span class="name">Countries</span>
				</a>
			 </li>
			 
			 <li>
				<a href="<?php echo SITEURL; ?>sitepanel/plans">
					<i class="fa fa-user-secret"></i> <span class="name">Plans</span>
				</a>
			 </li>
			  <li>
				<a href="<?php echo SITEURL; ?>sitepanel/coupons">
					<i class="fa fa-ticket"></i> <span class="name">Coupons</span>
				</a>
			 </li>-->	
							
			 
			 <?php } else { ?>
			 <!--<li>
				<a  href="<?php echo SITEURL; ?>/projects/lists">
					<i class="fa fa-folder-open"></i> <span class="name">Projects</span>
				</a>
			 </li>-->
			  <?php }
                  if (isset($projects['Project']['id'])) {
                      $workspacesList = $this->requestAction(array('controller' => 'Workspaces', 'action' => 'workspaceListForProject'), array('project_id' => $projects['Project']['id']));
                      foreach ($workspacesList as $key => $val) {
                      ?>
                      <li><a href="#"><i class="fa fa-book"></i> <?php echo $val; ?></a></li>
                      <?php
                      }
                  }
              ?>  
		  </ul>
		  
		  
		  
		   </div>
        </section>
        <!-- /.sidebar -->
      </aside>
      
 <script type="text/javascript" >
 $(document).ready(function(){
  var url = window.location.pathname;
   
         urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
         // now grab every link from the navigation
         $('ul.sidebar-menu li a').each(function(){
             // and test its normalized href against the url pathname regexp
             if(urlRegExp.test(this.href.replace(/\/$/,''))){ 
          
 			    $(this).addClass('active_bg');
             }else{
			   $(this).removeClass('active_bg');
			 }
         });
		 
    /* 
     * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
     * */
    var originalLeave = $.fn.popover.Constructor.prototype.leave;
    $.fn.popover.Constructor.prototype.leave = function(obj) {
        var self = obj instanceof this.constructor ?
            obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
        var container, timeout;

        originalLeave.call(this, obj);
        if (obj.currentTarget) {

            // container = $(obj.currentTarget).siblings('.popover')
            container = $(obj.currentTarget).data('bs.popover').tip()
            timeout = self.timeout;
            container.one('mouseenter', function() {
                //We entered the actual popover – call off the dogs
                clearTimeout(timeout);
                //Let's monitor popover content instead
                container.one('mouseleave', function() {
                    $.fn.popover.Constructor.prototype.leave.call(self, self);
                });
            })
        }
    };
    /* 
     * End Popover haack
     * */
		 $(".user-settings").popover({
			 trigger: 'hover',
			 placement: 'bottom',
			 container: 'body',
			 delay: {show: 50, hide: 400}
		 })
	})	 
 </script>

 