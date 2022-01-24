<?php 
$controller = $this->params['controller'];
$action = $this->params['action'];

$dashboard = ''; $industry = ''; $structure = ''; $expense = ''; $source = ''; $category = '';

if ($controller == 'dashboards'){
    $dashboard = ' active';
}
 

if ($controller == 'projects' && $action == 'admin_source') {
    $source = ' active';
}

if ($controller == 'projects' && $action == 'admin_expense') {
    $expense = ' active';
}

if ($controller == 'projects' && $action == 'admin_category') {
    $category = ' active';
}
 
 
$name = ucfirst($this->Session->read('Auth.Admin.UserDetail.first_name')) . ' ' . ucfirst($this->Session->read('Auth.Admin.UserDetail.last_name'));
$name = trim($name);
if(empty($name)){ $name = 'Admin'; }
/*$profile = $this->Session->read('Auth.Admin.UserDetail.profile_pic');
 if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)){
	$profile = SITEURL.USER_PIC_PATH.$profile;
}else{
	$profile = SITEURL.'img/avatar3.png';
} */
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">
			<?php /* <div class="pull-left image">
				<img src="<?php echo $profile; ?>" class="img-circle" alt="User Image" />
			</div>*/?>
			<div class="pull-left info">
				<p><?php echo $name; ?></p>

				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>
		<div id="users" >
		<!-- search form -->
		<form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control searching" autocomplete="off" placeholder="Search..."/>
				<span class="input-group-btn">
					<button type='submit' name='seach' id='search-btn' class="btn btn-flat"><!--<i class="fa fa-search"></i>--></button>
				</span>
			</div>
		</form>
		<!-- /.search form -->
		
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu list">
			<li>
				<a href="<?php echo SITEURL; ?>admin/dashboards">
					<i class="fa fa-dashboard"></i> <span class="name">Dashboards</span>
				</a>
			</li>
			<?php /*
			<li class="treeview">
				<a href="#">
					<i class="fa fa-filter"></i>
					<span class="name"> Projects</span>
					<i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li><a href="<?php echo SITEURL; ?>admin/projects"><i class="fa fa-angle-double-right"></i> All Projects</a></li>
				<?php //<li><a href="#"><i class="fa fa-angle-double-right"></i> Form</a></li>
					//<li><a href="#"><i class="fa fa-angle-double-right"></i> Active Projects</a></li>
					//<li><a href="#"><i class="fa fa-angle-double-right"></i> Tasks</a></li>
					//<li><a href="#"><i class="fa fa-angle-double-right"></i> Events and Milestones</a></li>   ?>
				</ul>
			</li> */ ?>
			<li>
				<a href="<?php echo SITEURL; ?>admin/users">
					<i class="fa fa-user"></i> <span class="name">People</span>
				</a>
			</li>
			<li>
				<a href="#">
					<i class="fa fa-gear"></i> <span class="name">Settings</span>
				</a>
			</li>
			<?php 
			/*	$active = '';
				if(!empty($industry) || !empty($structure) || !empty($source) || !empty($expense) || !empty($category)){
					$active = 'active'; 
				}
			?>
			<li class="treeview <?php echo $active; ?>">
				<a href="#">
					<i class="fa fa-list-alt"></i> 
					<span class="name">Property Manager</span>
					<i class="fa <?php if(!empty($active)){ ?> pull-right fa-angle-down <?php }else{ ?> fa pull-right fa-angle-left <?php } ?>"></i>
				</a>
				<ul class="treeview-menu" <?php if(!empty($active)){ ?> style="display:block;" <?php } ?>>
					<li class="<?php echo $source; ?>">
						<a href="<?php echo SITEURL; ?>admin/projects/source">
							<i class="fa fa-angle-double-right"></i><span>Project Source</span>
						</a>
					</li>
					
					<li class="<?php echo $expense; ?>">
						<a href="<?php echo SITEURL; ?>admin/projects/expense">
							<i class="fa fa-angle-double-right"></i> <span>Subject Expense Type</span>
						</a>
					</li>
					
					<li class="<?php echo $category; ?>">
						<a href="<?php echo SITEURL; ?>admin/projects/category">
							<i class="fa fa-angle-double-right"></i> <span>Project Category</span>
						</a>
					</li>
				</ul>
			</li>
			<?php */?>
			
		</ul>
	</section>
	<!-- /.sidebar -->
</aside>
<script type="text/javascript" >
$(document).ready(function(){
var options = {
  valueNames: [ 'name' ]
};

var userList = new List('users', options);
});
</script>
<?php
echo $this->Html->script(array('list'));
?>