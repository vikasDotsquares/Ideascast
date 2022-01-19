<?php
// $a = '2020-05-25 06:39:23';
// $today = date('Y-m-d H:i:s');
// $activation_time = date('Y-m-d H:i:s', strtotime($a));
// $hour_diff = round((strtotime($today) - strtotime($activation_time))/3600, 1);
// e($hour_diff, 1);
pr($lists);
echo $this->Html->css('projects/manage_users');
?>
<style>
#Recordlisting .box-header p > a {
	text-transform:lowercase !important;
}
.filter-email .edituser{ margin-right :0 ;}
a.btn.btn-primary {
    margin-left: 4px;
}
.well {
    margin-bottom: 0px;
    min-height: auto;
    padding: 0;
}
#example2 th a{ color : #333; }
.no-scroll {
    overflow: hidden;
}
</style>
<?php $whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
	  $whatINeed = $whatINeed[0];

	  unset($_SESSION['data']);

	  $admin_true = (isset($this->request->params['named']['flag']) && !empty($this->request->params['named']['flag'])) ? $this->request->params['named']['flag'] : 0;

?>
<div class="modal modal-success fade " id="Recordedit" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div>
	</div>
</div><!-- /.modal -->
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="modal_box" tabindex="-1" role="dialog" aria-labelledby="modalBoxModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<!-- END MODAL BOX -->
<div class="row">
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>  (<?php
				if(isset($admin_true) && !empty($admin_true)){
					echo $this->Common->totalDataU('User',1);
				}else{
					echo $this->Common->totalDataU('User');
				}
				?>)
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;"><?php echo $viewData['page_subheading']; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box1 noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding">
			<?php $class = 'collapse';
					if(isset($in) && !empty($in)){
						$class = 'in';
					}
			?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box create-manage-users-filter manage_users-template">
					<div class="box-header">
						<div class="col-sm-12 col-md-5">
							<p style="margin:0; padding-top:11px;">Account:
							<?php

								echo "<a class='customtipText ' style='text-transform:lowercase' title='https://".$whatINeed.WEBDOMAIN."' target='_blank' href='https://".$whatINeed.WEBDOMAIN."'>".$whatINeed.WEBDOMAIN."</a>";
							?></p>
						</div>
						<div class="col-sm-12 col-md-7 filter-email">
							<div class="pull-right padright">
								<div class="filter-email-inside">

								<span class="filter-email-select">
								<?php

								echo $this->Form->input('User.types', array('options' => array("All Users","Admin Users"), 'label' => false, 'div' => false,'selected'=>$admin_true,  'onchange' => 'searchAdmin(this.value)','class' => 'form-control')); ?></span>


								<span class="filter-email-select"><?php $edomains = $this->Common->getOrgEmailDomain($this->Session->read('Auth.User.id'));
								$selected = isset($this->request->params['pass'][0]) ? $this->request->params['pass'][0] : '';
								echo $this->Form->input('User.managedomain_id ', array('options' => $edomains,  'empty' => 'All Email Domains', 'label' => false, 'div' => false, 'selected'=>$selected, 'onchange' => 'searchUsers(this.value)','class' => 'form-control')); ?></span>

								<?php
								if($this->Session->read('Auth.User.role_id')==2 && $this->Session->read('Auth.User.UserDetail.administrator')==1){
									  $permitID = $this->Session->read('Auth.User.UserDetail.org_id');
								}else{
									  $permitID = $this->Session->read('Auth.User.id');
								}


								$editDisable = '';
								if(  ($this->Session->read('Auth.User.role_id') != 1 ))

								{
									//$editDisable = 'disable';
									$editDisable = ' ';
								}

								  $validDomain = $this->Common->checkOrgEmailDomain($permitID);


									if( $validDomain < 1 ){ ?>


									<a class="edituser tipText"  title="Cannot Create User - Create email domain" href="javascript:void(0);" data-placement="top" >
									<span class="btn btn-primary" style="text-transform:none;">Add</span>

								<?php 	}else if( check_license() && $validDomain > 0 ){
								?>
								<a class="btn btn-primary edituser"  title="Add User" href="<?php echo SITEURL."organisations/user_add" ; ?>"  data-tooltip="tooltip" data-placement="top" >
									Add
								</a> <?php } else { ?>
									<a class="edituser tipText"  title="All Licenses used" href="javascript:void(0);" data-placement="top" >
									<span class="btn btn-default" style="text-transform:none;">Add</span>
								</a>
								<?php } ?><?php if( isset($listDomainUsers) && !empty($listDomainUsers) && count($listDomainUsers) > 0 ){?> <a class="btn btn-primary deletemultiple <?php echo $editDisable; ?>" >
									Delete
								</a><?php }  ?>
								   <a class="btn btn-primary searchbtn " data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>
								</div>

							</div>
						</div>

						<div class="<?php echo $class; ?> search pull-right" style="width: 100%; margin-top: 10px;" id="collapseExample">
							<div class="wells">
								<?php

								if(isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder'])){
									$formAction = SITEURL."organisations/manage_users/page:".$this->params['paging']['User']['page']."/sortorder:".$this->params['named']['sortorder'];
								} else {
								$parms =  (isset($this->params['pass']['0']) && !empty($this->params['pass']['0'])) ? $this->params['pass']['0'] : '';
									$formAction = SITEURL."organisations/manage_users/".$parms;
								}
								if(isset($keyword) && !empty($keyword)){
									$parms =  (isset($this->params['pass']['0']) && !empty($this->params['pass']['0'])) ? $this->params['pass']['0'] : '';
									$formAction = SITEURL."organisations/manage_users/".$parms."/page:".$this->params['paging']['User']['page']."/search:".$keyword;
								}
								echo $this->Form->create('User', array( 'url' => $formAction, 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body">
										<div class="form-group" style="margin-bottom:0;">

											<div class="col-xs-6 col-sm-6 col-md-5 col-lg-4" style="padding-right: 0;">
												<?php echo $this->Form->input('keyword', array('placeholder' => 'Search for User...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
											</div>
											<div style="text-align:right;float:left;">
												<button type="submit" class="searchbtn btn" style="border-radius: 0 5px 5px 0"><i class="search-skill"></i></button>
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>organisations/manage_users" >Close</a>
											</div>
										</div>
									</div>
								<?php echo $this->Form->end(); ?>
							</div>
						</div>
					</div>


					<?php
					$facol = " ";
					$auther = two_factor_check();
					if(isset($auther ) && !empty($auther )){
						$facol = "fa-col-add";
					}
					?>

					<div class="box-body">
						<div id="example2" class="manage_users-wrap <?php echo $facol; ?>">
							<div class="manage_users_header">
									<div class="manage-users-col mu-col1"><input type="checkbox" name="checkAlltop" id="checkDomains" /></div>
									<div class="manage-users-col mu-col2"><?php echo __("Name");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By First Name">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Last Name">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<!-- <th><?php echo __("Last Name");?></th>-->
									<div class="manage-users-col mu-col3"><?php echo __("Email Address");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Email">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<div class="manage-users-col mu-col4"><?php echo __("Created on");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Created">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<div  class="manage-users-col mu-col5"><?php echo __("Status");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Status">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<div  class="manage-users-col mu-col6"><?php echo __("+ Project");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Project">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<div  class="manage-users-col mu-col7"><?php echo __("+ Template");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Template">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
								<div  class="manage-users-col mu-col12"><?php echo __("Analytics");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Analytics">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>
									<div  class="manage-users-col mu-col8"><?php echo __("Resourcer ");?><span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Resourcer">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span></div>


									<?php
									if(isset($auther ) && !empty($auther )){
									?>
									<div  class="manage-users-col mu-col9">
									<?php echo __("2FA");?>
									</div>
									<?php } ?>
									<div class="manage-users-col mu-col10">
									<?php
									if(isset($admin_true) && !empty($admin_true)){
										 echo __("Administrator");
									}else{
										echo $this->Paginator->sort('UserDetail.administrator',__("Admin"));
									}
									?>
									<span class="com-short sort_order tipText" title="" data-type="projects" data-by="sharer_count" data-order="" data-original-title="Sort By Admin">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<?php if($_SERVER['REMOTE_ADDR'] == '111.93.41.194'){?>
									<?php } ?>
									<div  class="manage-users-col mu-col11"><?php echo __("Actions");?>	</div>
							</div>
							<div id="tbody_skills" class="manage-users-listing">
								<?php
									if (!empty($listDomainUsers)) {
										$icount = 0;
										foreach ($listDomainUsers as $listdomainusers){
									$CheckProfileEdit = CheckProfileEdit($listdomainusers['User']['id']);
                                ?>
								<div class="manage-data-row" data-id="<?php echo $listdomainusers['User']['id']; ?>">

									<div class="manage-users-col mu-col1">
									<?php
										$listcheckbx = "disable";
										$checkAll = "empties";
										$property = "disabled";
										if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
											$listcheckbx = "checkDomainList";
											$checkAll = "checkAll";
											$property = "";
										}
										if(   ($CheckProfileEdit != 1 )) {
											$listcheckbx = "disable";
											$checkAll = "empties";
											$property = "disabled";
										}
									?>
									<input type="checkbox" class="<?php echo $listcheckbx;?>" name="<?php echo $checkAll;?>" value="<?php echo $listdomainusers['User']['id']; ?>" <?php echo $property;?> /></div>
									<div class="manage-users-col mu-col2"><?php echo ucfirst($listdomainusers['UserDetail']['first_name'].' '.$listdomainusers['UserDetail']['last_name']); ?></div>

									<div class="manage-users-col mu-col3"><span class="mu-email"><?php echo $listdomainusers['User']['email'];?></span></div>
									<div class="manage-users-col mu-col4"><?php echo _displayDate($listdomainusers['User']['created'], 'd M, Y'); ?></div>
								<div class="manage-users-col mu-col5">
										<?php

											$disabledadminstratorsts = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstratorsts = "btn btn-default RecordUpdateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstratorsts = "btn btn-default disable";
											}
											$disabledadminstratorstsAct = "";

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstratorstsAct = "disable";
											}


								if(empty($listdomainusers['User']['is_activated']) && !empty($listdomainusers['User']['activation_time'])){
									 ?>
									 <span class="btn btn-default" title="Awaiting User Activation" data-toggle="tooltip" data-placement="top" style="cursor: default;">
									 	<i class="waitingblack <?php echo $disabledadminstratorstsAct; ?>"></i>
									 </span>
									<?php
								}
								else{

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['User']['status'] == 1){ ?>
												<button  title="Active" data-toggle="tooltip" data-placement="top" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstratorsts;?>"><i class="activegreen"></i></button>
										<?php } else { ?>
												<button  rel="activate"  title="Inactive" data-toggle="tooltip" data-placement="top" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstratorsts;?>"><i class="inactivered"></i></button>
										<?php }
								}	?>
									</div>

									<div class="manage-users-col mu-col6"><?php
											$disabledadminstrator = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default ProjectUpdateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstrator = "btn btn-default disable";
											}

											if( $this->Session->read('Auth.User.id') == $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default disable";
											}

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['UserDetail']['create_project'] == 1){ ?>
												<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"> <i class="activegreen"></i></button>
										<?php } else { ?>
												<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="inactivered"></i> </button>
										<?php } ?>
									</div>

									<div class="manage-users-col mu-col7"><?php
											$disabledadminstrator = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default TemplateUpdateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstrator = "btn btn-default disable";
											}

											if( $this->Session->read('Auth.User.id') == $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default disable";
											}

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['UserDetail']['create_template'] == 1){ ?>
												<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"> <i class="activegreen"></i></button>
										<?php } else { ?>
												<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="inactivered"></i> </button>
										<?php } ?>
									</div>

									<div class="manage-users-col mu-col12"><?php
											$disabledadminstrator = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default AnalyticsUpdateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstrator = "btn btn-default disable";
											}

											if( $this->Session->read('Auth.User.id') == $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default disable";
											}

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['UserDetail']['analytics'] == 1){ ?>
												<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"> <i class="activegreen"></i></button>
										<?php } else { ?>
												<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="inactivered"></i> </button>
										<?php } ?>
									</div>

									<div class="manage-users-col mu-col8"><?php
											$disabledadminstrator = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default ResourcerUpdateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstrator = "btn btn-default disable";
											}

											if( $this->Session->read('Auth.User.id') == $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default disable";
											}

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['UserDetail']['resourcer'] == 1){ ?>
												<button data-toggle="tooltip" data-placement="top" title="Granted" rel="deactivate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"> <i class="activegreen"></i></button>
										<?php } else { ?>
												<button data-toggle="tooltip" data-placement="top" title="Denied" rel="activate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="inactivered"></i> </button>
										<?php } ?>
									</div>



									<?php

									$auther = two_factor_check();
									if(isset($auther ) && !empty($auther )){
									?>
									<div class="manage-users-col mu-col9">
									<?php
									if(isset($listdomainusers['UserDetail']['membership_code']) && !empty($listdomainusers['UserDetail']['membership_code'])){?>
									<button title="" data-toggle="tooltip" style="cursor: default;" data-placement="top"    class="btn btn-default  " data-original-title="Registered"><i class="activegreen"></i></button>
									<?php }else{ ?>
									<span class="btn btn-default" title="" data-toggle="tooltip" data-placement="top" style="cursor: default;" data-original-title="Not Registered">
									 	<i class="waitingblack"></i>
									</span>
									<?php }  ?>
									</div>
									<?php  }  ?>

									<div class="manage-users-col mu-col10"><?php
											$disabledadminstrator = "btn btn-default disable";
											if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default RecordCreateClass";
											}

											if(   ($CheckProfileEdit != 1 )) {
												$disabledadminstrator = "btn btn-default disable";
											}

											if( $this->Session->read('Auth.User.id') == $listdomainusers['User']['id'] ){
												$disabledadminstrator = "btn btn-default disable";
											}

											$clasificationId = $listdomainusers['User']['id'];
											if($listdomainusers['UserDetail']['administrator'] == 1){ ?>
												<button data-toggle="tooltip" data-placement="top" title="Admin Permissions" rel="deactivate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="activegreen"></i></button>
										<?php } else { ?>
												<button data-toggle="tooltip" data-placement="top" title="No Admin Permissions" rel="activate" data-userdetailid="<?php echo $listdomainusers['UserDetail']['id'];?>" id="<?php echo $clasificationId; ?>"  class="<?php echo $disabledadminstrator;?> btn btn-default"><i class="inactivered"></i></button>
										<?php } ?>
									</div>
									<div class="manage-users-col mu-col11 mu-actions">
										<?php

										 if( ($CheckProfileEdit == 1 )) {

										$editURL = "javascript:void(0);";
										$deleteURL = "javascript:void(0);";
										$disablededitcls = "tipText disable";
										$disableddelcls = "tipText disable";
										if( $this->Session->read('Auth.User.id') != $listdomainusers['User']['id'] ){

											$editURL = SITEURL."users/myaccountedit/".$listdomainusers['User']['id']."?refer=".SITEURL.'organisations/manage_users';
											$disablededitcls = "edituser tipText";
											$disableddelcls = "RecordDeleteClass tipText";
											$deleteURL = SITEURL."organisations/organisation_user_delete";
										}
										?>
										<a class="<?php echo $disablededitcls;?>" title="Edit User" href="<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="edit-icon"></i></a>
										<a class="<?php echo $disableddelcls;?>" rel="<?php echo $listdomainusers['User']['id']; ?>" title="Delete User" data-whatever="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="deleteblack"></i></a>


										<?php // activation code
										if(empty($listdomainusers['User']['is_activated']) && !empty($listdomainusers['User']['activation_time'])){
										$today = date('Y-m-d h:i:s');
										$activation_time = date('Y-m-d h:i:s', strtotime($listdomainusers['User']['activation_time']));
										$hour_diff = round((strtotime($today) - strtotime($activation_time))/3600, 1);
										?>
											<a class="resend-activation" data-user="<?php echo $listdomainusers['User']['id']; ?>" title="Resend Activation Email"   data-toggle="tooltip" data-placement="top" style="cursor:pointer;"><i class="emailblack"></i></a>
										<?php
										}


										if(isset($auther ) && !empty($auther )){

										$restURL = SITEURL."organisations/organisation_user_reset";
										if(isset($listdomainusers['UserDetail']['membership_code']) && !empty($listdomainusers['UserDetail']['membership_code'])){ ?>
											<a class="reset-fa" data-user="<?php echo $listdomainusers['User']['id']; ?>" data-whatever="<?php echo $restURL; ?>" title="Reset 2FA"   data-toggle="tooltip" data-placement="top" style="cursor:pointer;"><i class="reset-2fa"></i></a>

										<?php
										}
										}
										 }
										?>
									</div>
								</div>
								<?php
									$icount++;
								} //end foreach

								if($this->params['paging']['User']['pageCount'] > 1) { ?>
								<tr>
                                    <td colspan="8" align="right">
									<ul class="pagination">
										<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
										<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
										<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
									</ul>
									</td>
								</tr>
								<?php } ?>

								<?php } else { ?>
								<tr>
                                    <td colspan="8" style="color:#444;text-align: center;">NO USERS</td>
								</tr>
                                    <?php
										}
									?>
							</div>
						</div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true"></div><!-- /.modal -->
			</div></div>
		</section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
<?php echo $this->Html->css(array(
	'front-paging',
)); ?>
<!-- <link rel="stylesheet" type="text/css" href="/css/front-paging.css" /> -->
<script type="text/javascript" >
	$('html').addClass('no-scroll');
	$(function(){
		$('.resend-activation').on('click', function(event){
			event.preventDefault();
			var user_id = $(this).data('user');
			$.ajax({
				url: $js_config.base_url + 'organisations/resend_activation_email',
				type: 'POST',
				dataType: 'json',
				data: {user: user_id},
				success: function(response){
					if(response.success){
						location.reload()
					}
				}
			})

		})
	})

function searchUsers(domainname){

	window.location.href='<?php echo SITEURL?>organisations/manage_users/'+domainname;
	//location.reload();

}

function searchAdmin(params){

    if(params > 0){
		window.location.href='<?php echo SITEURL?>organisations/manage_users/flag:'+params;
	}else{
		window.location.href='<?php echo SITEURL?>organisations/manage_users';
	}
	//location.reload();

}



	$('#UserManagedomainId option').each(function() {
	  var optionText = this.text;

	  if( optionText.length > 40  ){
		var extension = optionText.split('.');
		var newOption = extension[0].substring(0, 30);
		$(this).text(newOption + '...'+extension[1]);
	  }

	});

$(function(){

	$('.customtipText').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		, 'container': 'body', 'placement': 'top',
	})


	setTimeout(function(){

		$('#successFlashMsg').hide('slow', function(){ $('#successFlashMsg').remove() })

	},4000);


	$('input[name="checkAll"]').removeAttr('checked');

	// Sorting with drag and drop
	var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index)
        {
          $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

});

	// Used for Sorting icons on listing pages
	$('th a').append(' <i class="fa fa-sort"></i>');
	$('th a.asc i').attr('class', 'fa fa-sort-down');
	$('th a.desc i').attr('class', 'fa fa-sort-up');

	$(document).on('click', '.RecordUpdateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');

			var deleteURL = '<?php echo SITEURL; ?>organisations/org_updatestatus';

			$('#recordID').val(id);
			var  activeInMsg = 'Are you sure you want to make this user active?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
				activeInMsg = '<p>Warning: Inactive users are unable to sign in and cannot have new work shared with them. However, their existing data continues to be visible in the system to other users.</p><p>Are you sure you want to make this user inactive?</p>';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: activeInMsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : deleteURL,
								type: "POST",
								data: $.param({id:id,status:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){ }
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});

	$(document).on('click', '.RecordCreateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');
			var userdetailid = $that.attr('data-userdetailid');

			var statusUrl = '<?php echo SITEURL; ?>organisations/org_admin_status';

			$('#recordID').val(id);
			var defaultmsg = 'Are you sure you want to set the user as an Administrator?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
				defaultmsg = 'Are you sure you want to set the user as an Administrator?';
			}else{
				$('#recordStatus').val(0);
				defaultmsg = 'Are you sure you want to remove Administrator rights?';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: defaultmsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : statusUrl,
								type: "POST",
								data: $.param({id:userdetailid,user_id:id,administrator:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){
									if($.trim(response) == 'error'){
										location.reload();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								location.reload();
							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});

	// ============ Change Project Status ===================================================================
	$(document).on('click', '.ProjectUpdateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');
			var userdetailid = $that.attr('data-userdetailid');

			var statusUrl = '<?php echo SITEURL; ?>organisations/org_project_status';

			$('#recordID').val(id);
			var defaultmsg = 'Are you sure you want to set the project status?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
				defaultmsg = 'Are you sure you want to set the project status?';
			}else{
				$('#recordStatus').val(0);
				defaultmsg = 'Are you sure you want to set the project status?';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: defaultmsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : statusUrl,
								type: "POST",
								data: $.param({id:userdetailid,user_id:id,create_project:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){
									if($.trim(response) == 'error'){
										location.reload();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								location.reload();
							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});

	$(document).on('click', '.ResourcerUpdateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');
			var userdetailid = $that.attr('data-userdetailid');

			var statusUrl = '<?php echo SITEURL; ?>organisations/org_project_status';

			$('#recordID').val(id);
			var defaultmsg = 'Are you sure you want to set the resourcer status?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
				defaultmsg = 'Are you sure you want to set the resourcer status?';
			}else{
				$('#recordStatus').val(0);
				defaultmsg = 'Are you sure you want to set the resourcer status?';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: defaultmsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : statusUrl,
								type: "POST",
								data: $.param({id:userdetailid,user_id:id,resourcer:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){
									if($.trim(response) == 'error'){
										location.reload();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								location.reload();
							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});


	$(document).on('click', '.TemplateUpdateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');
			var userdetailid = $that.attr('data-userdetailid');

			var statusUrl = '<?php echo SITEURL; ?>organisations/org_project_status';

			$('#recordID').val(id);
			var defaultmsg = 'Are you sure you want to set the template status?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
				defaultmsg = 'Are you sure you want to set the template status?';
			}else{
				$('#recordStatus').val(0);
				defaultmsg = 'Are you sure you want to set the template status?';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: defaultmsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : statusUrl,
								type: "POST",
								data: $.param({id:userdetailid,user_id:id,create_template:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){
									if($.trim(response) == 'error'){
										location.reload();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								location.reload();
							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});


	$(document).on('click', '.AnalyticsUpdateClass', function(event){
		event.preventDefault();

			$that = $(this);
			var id = $that.attr('id');
			var rel = $that.attr('rel');
			var userdetailid = $that.attr('data-userdetailid');

			var statusUrl = '<?php echo SITEURL; ?>organisations/org_project_status';

			$('#recordID').val(id);
			var defaultmsg = 'Are you sure you want to set the anaytics status?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
				defaultmsg = 'Are you sure you want to set the anaytics status?';
			}else{
				$('#recordStatus').val(0);
				defaultmsg = 'Are you sure you want to set the anaytics status?';
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: defaultmsg,
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : statusUrl,
								type: "POST",
								data: $.param({id:userdetailid,user_id:id,analytics:$('#recordStatus').val()}),
								global: true,
								async:false,
								success:function(response){
									if($.trim(response) == 'error'){
										location.reload();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								location.reload();
							}else{
								location.reload();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


	});

	$(function(){

		$(".checkDomainList").each(function(){

			if( $(this).prop('checked') == true ) {
				$(this).parents('tr:first').css('background-color', '#EFFFFE')
			}
			else {
				$(this).parents('tr:first').css('background-color', '#ffffff')
			}

		})

		$("#checkDomains").click(function(){

			var status = this.checked;
			$('.checkDomainList').each(function(){
				this.checked = status;
				$(this).parents('tr:first').css('background-color', '#ffffff')
			});
		})

		$(".checkDomainList").click(function(){

			//uncheck "select all", if one of the listed checkbox item is unchecked
			if(false == $(this).prop("checked")){
				$("#checkDomains").prop('checked', false);
			}

			//check "select all" if all checkbox items are checked
			if ($('.checkDomainList:checked').length == $('.checkDomainList').length ){
				$("#checkDomains").prop('checked', true);
			}

			// change parent row background-color on checked
			if( $(this).prop('checked') == true ) {
				$(this).parents('tr:first').css('background-color', '#EFFFFE')
			}
			else {
				$(this).parents('tr:first').css('background-color', '#ffffff')
			}

		});


		$(".reset-fa").click(function(event){
			event.preventDefault();

			$that = $(this);
			var row = $that.parents('tr:first');

			var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
			var deleteid = $(this).attr('data-user');

			BootstrapDialog.show({
				title: 'Reset 2FA',
				message: "<p>Are you sure you want to set the user's 2FA status to Unregistered?</p>",
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Reset',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : deleteURL,
								type: "POST",
								data: $.param({user_id: deleteid}),
								global: false,
								// async:false,
								success:function(response){
									 location.reload();
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							} else {

/* 								$that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								}); */

								window.location.href=$js_config.base_url+'organisations/manage_users';

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							//dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								// location.reload();
							}, 500);
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});
		})


		$(".RecordDeleteClass").click(function(event){
			event.preventDefault();

			$that = $(this);
			var row = $that.parents('tr:first');

			var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
			var deleteid = $(this).attr('rel');

			BootstrapDialog.show({
				title: 'Confirmation',
				message: '<p>Warning: All data for this user will be deleted from the system and is not recoverable.</p><p>Are you sure you want to delete this user?</p>',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					//icon: '',
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : deleteURL,
								type: "POST",
								data: $.param({user_id: deleteid}),
								global: false,
								// async:false,
								success:function(response){
									 location.reload();
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							} else {

/* 								$that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								}); */

								window.location.href=$js_config.base_url+'organisations/manage_users';

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							//dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								// location.reload();
							}, 500);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});
		})



		$(".deletemultiple").click(function(event){
			event.preventDefault();

			var row = $(".checkDomainList").parents('tr:first');

			var allChecked = [];
			$('input[name="checkAll"]:checked').each(function(i) {
				allChecked[i] = this.value;
				$('tr[data-id='+this.value+']').css('background-color','#FFBFBF');

			});
			if( allChecked.length > 0 ){

				BootstrapDialog.show({
				title: 'Confirmation',
				message: '<p>Warning: All data for these users will be deleted from the system and is not recoverable.</p><p>Are you sure you want to delete these users?</p>',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
					{
						//icon: '',
						label: ' Yes',
						cssClass: 'btn-success',
						autospin: true,
						action: function (dialogRef) {
							$.when(

									$.ajax({
									url : deleteURL = '<?php echo SITEURL."organisations/delete_allusers";?>',
									type: "POST",
									dataType:"json",
									data: $.param({user_id:allChecked}),
									global: false,
									async:false,
									success:function(response){

											if($.trim(response) != 'success'){

												$('#Recordedit').html(response);


											}else{

												$('input[name="checkAll"]:checked').each(function(i) {
													rowN = $('tr[data-id='+this.value+']');
													rowN.children('td, th').animate({
													padding: 0
													}).wrapInner('<div />').children().slideUp(1000,function () {
														$(this).closest('tr').remove();
													});
													location.reload();
												});

												window.location.href=$js_config.base_url+'organisations/manage_users';

											}
										}
									})

							).then(function( data, textStatus, jqXHR ) {
								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);

								setTimeout(function () {
									dialogRef.close();
									location.reload();
								}, 500);
								dialogRef.getModalBody().html('<div class="loader"></div>');
							})
						}
					},
					{
						label: ' No',
						//icon: '',
						cssClass: 'btn-danger',
						action: function (dialogRef) {
							dialogRef.close();
						}
					}
					]
				});

			}
		});

		$('#Recordedit,#modal_box').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal')
			$(this).find('.modal-content').html('')
		})


	    // RESIZE MAIN FRAME
	    ;($.adjust_resize = function(){
	        var $ptypes_tab_wrap = $('.manage-users-listing');
	        $ptypes_tab_wrap.animate({
	            minHeight: (($(window).height() - $ptypes_tab_wrap.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $ptypes_tab_wrap.offset().top) ) - 17
	        }, 1);

	    })();

	    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
	    var interval = setInterval(function() {
	        if (document.readyState === 'complete') {
	            $.adjust_resize();
	            clearInterval(interval);
	        }
	    }, 1);

	    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
	    $(".sidebar-toggle").on('click', function() {
	        $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })
});


</script>