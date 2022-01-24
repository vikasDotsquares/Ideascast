
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
		<div class="fix-area">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo SITEURL?>img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>Alexander Pierce</p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
		  
		  
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
              </ul>
		  
		  </div>
		  
		   <div id="sideMenu" class="normal-list ">
		  <ul class="sidebar-menu">
			  
              <li><a href="#"><i class="fa fa-dashboard"></i> Summary</a></li>
              <li><a href="#"><i class="fa fa-calendar"></i> Calendar</a></li>
              <li><a href="#"><i class="fa fa-folder"></i> Documents</a></li>
			  <?php
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
      
 

 