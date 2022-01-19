<?php
// $a = '2020-05-25 06:39:23';
// $today = date('Y-m-d H:i:s');
// $activation_time = date('Y-m-d H:i:s', strtotime($a));
// $hour_diff = round((strtotime($today) - strtotime($activation_time))/3600, 1);
// e($hour_diff, 1);
// pr($lists);
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
.disable i {
    filter: grayscale(1);
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
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>  (<span class="total-users">0<?php
				/*if(isset($admin_true) && !empty($admin_true)){
					echo $this->Common->totalDataU('User',1);
				}else{
					echo $this->Common->totalDataU('User');
				}*/
				?></span>)
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

								echo $this->Form->input('User.types', array('options' => array("All Users","Admin Users"), 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'admin_search')); // 'onchange' => 'searchAdmin(this.value)', ?></span>


								<span class="filter-email-select">
									<?php $edomains = $this->Common->getOrgEmailDomain($this->Session->read('Auth.User.id'));

								echo $this->Form->input('User.managedomain_id ', array('options' => $edomains,  'empty' => 'All Email Domains', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'edomain_search')); //'onchange' => 'searchUsers(this.value)', ?></span>

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
									$editDisable = '';
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
								<?php } ?><?php if( isset($lists) && !empty($lists) && count($lists) > 0 ){?> <a class="btn btn-primary deletemultiple <?php echo $editDisable; ?> disabled" >
									Delete
								</a><?php }  ?>
								   <a class="btn btn-primary searchbtn show-search" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
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
								//echo $this->Form->create('User', array( 'url' => $formAction, 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body" style="padding-left: 0;">
										<div class="form-group" style="margin-bottom:0;">

											<div class="col-xs-6 col-sm-6 col-md-5 col-lg-4 nopadding-left" style="padding-right: 0;">
												<?php echo $this->Form->input('keyword', array('placeholder' => 'Search for User...','type' => 'text','label' => false,  'div' => false, 'class' => 'form-control', 'id' => 'user_search')); ?>
											</div>
											<div style="text-align:right;float:left;">
												<button  class="searchbtn btn search-btn" style="border-radius: 0 5px 5px 0"><i class="search-skill"></i></button>
												<button class="btn clear-btn" type="button" style="display: none;"><i class="clearblackicon search-clear"></i></button>
												<!-- <a class="btn btn-primary searchbtn" href="#" >Close</a> -->
											</div>
										</div>
									</div>
								<?php //echo $this->Form->end(); ?>
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
							<input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        	<input type="hidden" name="paging_total" id="paging_total" value="0">
							<div class="manage_users_header">
									<div class="manage-users-col mu-col1"><input type="checkbox" name="checkAlltop" id="checkDomains" /></div>
									<div class="manage-users-col mu-col2"><?php echo __("Name");?><span class="com-short sort_order tipText active" title="" data-type="user_list" data-by="first_name" data-order="desc" data-original-title="Sort By First Name">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="last_name" data-order="" data-original-title="Sort By Last Name">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="manage-users-col mu-col3"><?php echo __("Email Address");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="email" data-order="" data-original-title="Sort By Email">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div class="manage-users-col mu-col4"><?php echo __("Created on");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="created" data-order="" data-original-title="Sort By Created">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col5"><?php echo __("Status");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="status" data-order="" data-original-title="Sort By Status">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col6"><?php echo __("+ Project");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="create_project" data-order="" data-original-title="Sort By Project">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col7"><?php echo __("+ Template");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="create_template" data-order="" data-original-title="Sort By Template">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col12"><?php echo __("Analytics");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="analytics" data-order="" data-original-title="Sort By Analytics">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col8"><?php echo __("Resourcer ");?><span class="com-short sort_order tipText" title="" data-type="user_list" data-by="resourcer" data-order="" data-original-title="Sort By Resourcer">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>


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
									<span class="com-short sort_order tipText" title="" data-type="user_list" data-by="administrator" data-order="" data-original-title="Sort By Admin">
											<i class="fa fa-sort" aria-hidden="true"></i>
											<i class="fa fa-sort-asc" aria-hidden="true"></i>
											<i class="fa fa-sort-desc" aria-hidden="true"></i>
										</span>
									</div>
									<div  class="manage-users-col mu-col11"><?php echo __("Actions");?>	</div>
							</div>
							<div id="tbody_skills" class="manage-users-listing" data-flag="true">
								<?php echo $this->element('../Organisations/partial/user_lists'); ?>
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


<script type="text/javascript" >
	$('html').addClass('no-scroll');
	$(function(){
		$('body').on('click', '.resend-activation', function(event){
			event.preventDefault();
			var user_id = $(this).data('user');
			$.ajax({
				url: $js_config.base_url + 'organisations/resend_activation_email',
				type: 'POST',
				dataType: 'json',
				data: {user: user_id},
				success: function(response){
					if(response.success){
						$.refreshTeam();
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
								// location.reload();
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
			var defaultmsg = 'Are you sure you want to change the Application Administrator permission?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
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
										$.refreshTeam();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								$.refreshTeam();
							}else{
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
			var defaultmsg = 'Are you sure you want to change the Add Project permission?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
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
										$.refreshTeam();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								$.refreshTeam();
							}else{
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
			var defaultmsg = 'Are you sure you want to change the Resourcer permission?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
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
										$.refreshTeam();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								$.refreshTeam();
							}else{
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
			var defaultmsg = 'Are you sure you want to change the Add Knowledge Template permission?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
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
										$.refreshTeam();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								$.refreshTeam();
							}else{
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
			var defaultmsg = 'Are you sure you want to change the View Analytics permission?';
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
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
										$.refreshTeam();
									}
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else if($.trim(data) == 'error'){
								$.refreshTeam();
							}else{
								$.refreshTeam();
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.close();
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
//
		$('body').on('click', "#checkDomains", function(event){

			var status = this.checked;
			$('.checkDomainList').each(function(){
				this.checked = status;
				if(status) {
					$(this).parents('.manage-data-row:first').css('background-color', '#ebf1fb');
				}
				else {
					$(this).parents('.manage-data-row:first').css('background-color', '#ffffff');
				}
			});

			$('.deletemultiple').addClass('disabled');
			if($('.checkDomainList:checked').length > 0){
				$('.deletemultiple').removeClass('disabled');
			}
		})

		$('body').on('click', ".checkDomainList", function(){

			//uncheck "select all", if one of the listed checkbox item is unchecked
			if(false == $(this).prop("checked")) {
				$("#checkDomains").prop('checked', false);
			}

			//check "select all" if all checkbox items are checked
			if ($('.checkDomainList:checked').length == $('.checkDomainList').length ){
				$("#checkDomains").prop('checked', true);
			}

			// change parent row background-color on checked
			if( $(this).prop('checked') == true ) {
				$(this).parents('.manage-data-row:first').css('background-color', '#ebf1fb')
			}
			else {
				$(this).parents('.manage-data-row:first').css('background-color', '#ffffff')
			}

			$('.deletemultiple').addClass('disabled');
			if($('.checkDomainList:checked').length > 0){
				$('.deletemultiple').removeClass('disabled');
			}

		});


		$('body').on('click', ".reset-fa", function(event){
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


		$('body').on('click', ".RecordDeleteClass", function(event){
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
									$.refreshTeam();
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							} else {
								$.refreshTeam();

								// window.location.href=$js_config.base_url+'organisations/manage_users';

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

												$.refreshTeam();
												dialogRef.close();
												$(".deletemultiple").addClass('disabled');

												// window.location.href=$js_config.base_url+'organisations/manage_users';

											}
										}
									})

							).then(function( data, textStatus, jqXHR ) {
								dialogRef.enableButtons(false);
								dialogRef.setClosable(false);

								setTimeout(function () {
									dialogRef.close();
									$.refreshTeam();
									$(".deletemultiple").addClass('disabled');
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



		/******* PROJECT TEAM ******/
		$.list_wrap = $('.manage_users-wrap');
		$.list_scroller = $('.manage-users-listing');
		// SORTING
		$('body').on('click', '.manage_users-wrap .sort_order', function(event) {
			var $that = $(this),
				$parent = $.list_wrap,
				order = $that.data('order') || 'asc',
				coloumn = $that.data('by');

			if( order == 'desc' ){
				$(this).attr('data-order', 'asc');
			}
			else{
				$(this).attr('data-order', 'desc');
			}

			$parent.find('.sort_order.active').not(this).removeClass('active');

			$that.addClass('active');
			$('.tooltip').remove();

			var search = $('#user_search').val();
			var admin_search = $('#admin_search').val();
			var edomain_search = $('#edomain_search').val();

			var data = {order: order, coloumn: coloumn, search: search, admin_search: admin_search, edomain_search: edomain_search}

			$.ajax({
				url: $js_config.base_url + 'organisations/user_list',
				type: 'POST',
				// dataType: 'json',
				data: data,
				success: function(response){

					$parent.find("#paging_offset").val(0);

					if( order == 'asc' ){
						$that.data('order', 'desc');
					} else {
						$that.data('order', 'asc');
					}
					$.list_scroller.html(response);
					$('.tooltip').remove();

				}
			})
		})

		// TABS PAGINATION
	    $('.manage-users-listing').scroll(function() {
	        $('.tooltip').hide()
	        var $this = $(this);
	        var $parent = $.list_wrap;
	        clearTimeout($.data(this, 'scrollTimer'));

	        $.data(this, 'scrollTimer', setTimeout(function() {
	            if($this.scrollTop() + $this.innerHeight()+15 >= $this[0].scrollHeight)  {
					$.updateTeamOffset($this, $parent);
	            }
	        }, 250));
	    });

	    $.countTeamSize = function(parent) {
	        var dfd = $.Deferred();

	        var order = 'asc',
	        	coloumn = 'first_name';
	        if( $('.sort_order.active', parent).length > 0 ) {
	            order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
	            coloumn = $('.sort_order.active', parent).data('by');

	            if( order == 'asc' ){
	                order = 'desc';
	            } else {
	                order = 'asc';
	            }
	        }

	        var search = $('#user_search').val();
	        var admin_search = $('#admin_search').val();
	        var edomain_search = $('#edomain_search').val();

			var data = {order: order, coloumn: coloumn, search: search, admin_search: admin_search, edomain_search: edomain_search}

	        $.ajax({
	            url: $js_config.base_url + 'organisations/user_count',
	            data: data,
	            type: 'post',
	            dataType: 'JSON',
	            success: function(response) {
	                $('#paging_offset', parent).val(0);
	                $('#paging_total', parent).val(response);
	                $('.total-users').html( response );
					if(response <= 0){
	            		$('.ps-col-header', parent).addClass('none-selection');
					}
					else{
						$('.ps-col-header', parent).removeClass('none-selection');
					}
	                dfd.resolve('paging count');
	            }
	        })
	        return dfd.promise();
	    }
	    $.countTeamSize($.list_wrap);

	    $.user_offset = $js_config.user_offset;
	    $.updateTeamOffset = function(wrapper, parent){
	        var page = parseInt($('#paging_offset', parent).val());
	        var max_page = parseInt($('#paging_total', parent).val());
	        var last_page = Math.ceil(max_page/$.user_offset);

	        if(page < last_page - 1 && wrapper.data('flag')){
	            $('#paging_offset', parent).val(page + 1);
	            offset = ( parseInt($('#paging_offset', parent).val()) * $.user_offset);
	            $.getTeamPages(offset, wrapper, parent);
	        }
	    }

	    $.getTeamPages = function(page, wrapper, parent){
	        wrapper.data('flag', false);
	        var $wrapper = wrapper;
			//added by me ******************
			var order = 'asc',
				coloumn = 'first_name';
			if( $('.sort_order.active', parent).length > 0 ) {
				order = ($('.sort_order.active', parent).data('order') == 'asc') ? 'desc' : 'asc',
				coloumn = $('.sort_order.active', parent).data('by');
			}
	        var data = {page: page, order: order, coloumn: coloumn }

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'organisations/user_list',
	            data: data,
	            success: function(html) {
	                $wrapper.append(html);
	                wrapper.data('flag', true);
	            }
	         });
	    }

	    $.refreshTeam = function(){
	        var $parent = $.list_wrap;
	        var $wrapper = $('.manage-users-listing');
	        $wrapper.data('flag', false);

			var order = 'asc',
				coloumn = 'first_name';
			if( $('.sort_order.active', $parent).length > 0 ) {
				order = ($('.sort_order.active', $parent).data('order') == 'asc') ? 'desc' : 'asc',
				coloumn = $('.sort_order.active', $parent).data('by');
			}

	        var search = $('#user_search').val();
	        var admin_search = $('#admin_search').val();
	        var edomain_search = $('#edomain_search').val();

			var data = {order: order, coloumn: coloumn, search: search, admin_search: admin_search, edomain_search: edomain_search}

	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + 'organisations/user_list',
	            data: data,
	            success: function(html) {
	                $wrapper.html(html);
	                $wrapper.data('flag', true);
	                $.countTeamSize($.list_wrap);
	                $('.tooltip').remove();
	            }
	         });
	    }

	    $('.sort_order').tooltip({
	        placement: 'top',
	        container: 'body'
	    })

	    var typingTimer;                //timer identifier
		var doneTypingInterval = 300;  //time in ms
		var $input = $('#user_search');

		$('body').on('keyup', '#user_search', function(event) {
			event.preventDefault();
			var search_text = $(this).val(),
				type = $(this).data('type');

			var $thisParent = $(this).parents('.modal-body');

			$.list_wrap.find("#paging_offset").val(0);

			if( search_text.length == 0 ){
				$(".clear-btn", $thisParent).hide();
				$(".search-btn", $thisParent).show();
			}
			clearTimeout(typingTimer);

	  		//user is "finished typing," do something
	  		typingTimer = setTimeout($.proxy(function(){
				$.refreshTeam()
	  		},this), doneTypingInterval);

		});
		//on keydown, clear the countdown
		$('body').on('keydown', '#user_search', function(event) {
		  	clearTimeout(typingTimer);
		  	var $thisParent = $(this).parents('.modal-body');
			$(".clear-btn", $thisParent).show();
			$(".search-btn", $thisParent).hide();
		});

		$('body').on('click', '.clear-btn',function(event){
			event.preventDefault();

			var $thisParent = $(this).parents('.modal-body');

			$(this).hide();
			$(".search-btn", $thisParent).show();

			$('#user_search').val('').trigger('keyup');

		});


		$('body').on('change', '#admin_search, #edomain_search', function(event) {
		  	$.refreshTeam();
		});

		$('#collapseExample').on('shown.bs.collapse', function () {
			$.adjust_resize();
		})
		$('#collapseExample').on('hidden.bs.collapse', function () {
			$.adjust_resize();
		})
});
</script>