
<div class="row">
	<div class="col-xs-12">

	<div class="row">

	<?php
		// LOAD PARTIAL FILE FOR TOP DD-MENUS
		//echo $this->element('../Projects/partials/project_dd_menus', array('val' => 'testing'));
	?>

    </div>
	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box ">
				<div class="box-header">

				<ol class="breadcrumb">
					<li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
					<li class="active">Organizations</li>
				</ol>
				<h3>Organizations (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}
			$per_page_show = $this->Session->read('user.per_page_show');
			$keyword = $this->Session->read('user.keyword');
			$domain_name = $this->Session->read('OrganisationUser.domain_name');
			$status = $this->Session->read('user.status');
			$country = $this->Session->read('user.country');

		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php
										echo $this->Form->create('User', array( 'url' => array('controller'=>'organisations', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' ));

										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>$per_page_show, 'div' => false, 'class' =>'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();'));
										?>
										</form>
									</div>
								</div>
								<div class="pull-right padright">
									<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
										Search
									</a>
									<!--<button  data-toggle="modal" data-target="#RecordAdd" class="btn btn-primary">Add User</button> -->
									<?php $add = SITEURL."sitepanel/organisations/add/"; ?>
									<a class="btn btn-primary "  href="<?php echo $add; ?>">
										Add Organization
									</a>
								</div>
								<div class="<?php echo $class; ?> search" id="collapseExample">
									<div class="well">
										<?php echo $this->Form->create('User', array( 'url' => array('controller'=>'organisations', 'action'=>'admin_index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
											<div class="modal-body">
												<div class="form-group">
													<div class="col-lg-1">
														<label for="focusedInput" class="control-label">Keyword:</label>
													</div>
													<div class="col-lg-3">
														<?php echo $this->Form->input('keyword', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
													</div>

													<?php /* <div class="col-lg-1">
														<label for="focusedInput" class="control-label">Domain:</label>
													</div>
													<div class="col-lg-3">
														<?php echo $this->Form->input('OrganisationUser.domain_names', array('placeholder' => 'Enter Domain name here...','type' => 'text','label' => false, 'value'=>$domain_name,'div' => false, 'class' => 'form-control')); ?>
													</div> */ ?>

													<div class="col-lg-1">
														<label for="focusedInput" class="control-label">Status:</label>
													</div>
													<div class="col-lg-3">
														<?php $options = array('1' => 'Active', '0'=>'Deactive');
														 echo $this->Form->input('User.status', array('options'=>$options, 'empty' => '- Select Status -','label' => false, 'selected'=>$status, 'div' => false, 'class' => 'form-control')); ?>
													</div>

													<?php /* <<div class="col-lg-1">
														<label for="focusedInput" class="control-label">Country:</label>
													</div>
													<div class="col-lg-3">


															 <?php echo $this->Form->input('UserDetail.country_id', array('options' => $this->Common->getCountryList(),  'empty' => 'Select Country','selected'=>$country,  'label' => false, 'div' => false,  'onchange' => 'selectCity(this.options[this.selectedIndex].value)','class' => 'form-control')); ?>

													</div>*/ ?>


													<div class="col-lg-4" style="text-align:right;">
														<button type="submit" class="searchbtn btn btn-success">Go</button>
														<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/organisations/resetfilter" >Close</a>
													</div>
												</div>
											</div>
											</form>
									</div>
								</div>
					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo $this->Paginator->sort('UserDetail.org_name',__("Organizations"));?></th>
									<th><?php echo $this->Paginator->sort('UserDetail.first_name',__("Contact Person"));?></th>
									<?php /* <th><?php echo $this->Paginator->sort('OrganisationUser.domain_name',__("Domain"));?></th>*/ ?>
									<th><?php echo $this->Paginator->sort('User.email',__("Email"));?></th>
									<th><?php echo __("Domains");?></th>
									<th><?php echo $this->Paginator->sort('User.status',__("Status"));?></th>
									<th><?php echo $this->Paginator->sort('User.created',__("Created"));?></th>
									<th><?php echo __("Actions");?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($users)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($users as $user) { //pr($user);
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php
										if( !empty($user['UserDetail']['org_name']) && strlen($user['UserDetail']['org_name']) > 50 ){
											echo mb_strimwidth($user['UserDetail']['org_name'], 0, 50, "...");
										} else {
											echo $user['UserDetail']['org_name'];
										}
									?></td>
									<td><?php
										$fullName = $user['UserDetail']['first_name'].' '.$user['UserDetail']['last_name'];
										if( !empty($fullName) && strlen($fullName) > 50 ){
											echo mb_strimwidth($fullName, 0, 50, "...");
										} else {
											echo $user['UserDetail']['first_name'].' '.$user['UserDetail']['last_name'];
										}
										?>
									</td>
									<?php /* <td><?php echo $user['OrganisationUser']['domain_name']; ?></td> */ ?>
									<td><?php echo $user['User']['email']; ?></td>
									<td><?php echo $this->Common->orgdomainslist($user['User']['id']); ?></td>
									<td>
										<?php
											$clasificationId = $user['User']['id'];
											if ($user['User']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>
									<td><?php echo dateFormat($user['User']['created']);
										//echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($user['User']['created'])),$format = 'd M, Y');
									?></td>
									<td>
										<?php
										$viewURL = SITEURL."sitepanel/organisations/view/".$user['User']['id'];
										?>

										<a data-toggle="modal" class="viewusers"  data-target="#popup_model_box_profile"   title="Organization Details" data-remote="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-eye"></i></a>

										<?php
										$editURL = SITEURL."sitepanel/organisations/edit/".$user['User']['id'];
										?>
										<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit Organization" ><i class="fa fa-fw fa-edit"></i></a>
										<?php
											$deleteURL = SITEURL."sitepanel/organisations/deleteAuth/".$user['User']['id'];
										?>
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $user['User']['id']; ?>" title="Delete Organization" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a>
										<?php /*
											if( isset($user['UserDetail']['db_setup']) && $user['UserDetail']['db_setup'] == 0 ){
												$dbimport = SITEURL."sitepanel/organisations/import_database";
												$setupclass = 'databasesetupclass';
												$setupdisclass = '';
												$setupTooltip = 'Setup Database';
											} else {
												$dbimport = '';
												$setupclass = 'disabled';
												$setupdisclass = 'text-gray';
												$setupTooltip = 'Database has already setup';
											}

											$ndbname = preg_replace('/[.,]/', '', $user['OrganisationUser']['domain_name']);
											$latestdb = str_replace('-','',$ndbname);
										?>
										<a data-toggle="modal" class="<?php echo $setupclass;?>" data-target="#setupdb" data-orguid="<?php echo $user['User']['id']; ?>" rel="<?php echo $latestdb; ?>"   title="<?php echo $setupTooltip; ?>" data-where="<?php echo $dbimport; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-database <?php echo $setupdisclass;?>" aria-hidden="true"></i></a>

										<?php
										if( isset($user['UserDetail']['db_setup']) && $user['UserDetail']['db_setup'] == 0 ){
											$setupclass = 'disabled';
											$dbdrop = '';
											$setupTooltip = 'Database is already empty';
											$setupdisclass = 'text-gray';
										} else {
											$setupclass = 'databasedropclass';
											$dbdrop = SITEURL."sitepanel/organisations/drop_tables";
											$setupTooltip = 'Drop Database';
											$setupdisclass = '';
										}
										?>
										<a data-toggle="modal" class="<?php echo $setupclass; ?>" data-target="#dropdb" rel="<?php echo $user['OrganisationUser']['id']; ?>" data-orguid="<?php echo $user['User']['id']; ?>" title="<?php echo $setupTooltip; ?>" data-where="<?php echo $dbdrop; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-trash-o <?php echo $setupdisclass;?>" ></i></a>
										<?php */ /* <a href="<?php echo SITEURL."sitepanel/organisations/add_domain/".$user['User']['id'];?>"><i class="fa fa-plus"></i></a> */

										$domainlist = $this->Common->orgdomainslist($user['User']['id'],'list');
										$html = '<div>';
										$i = 1;
										//echo count($domainlist);
										if( isset($domainlist) && !empty($domainlist) && count($domainlist) > 0 ){
											foreach($domainlist as $dlist){
												if( $i <= 5 ){
													$html .= '<p>'.$i.'. '.strip_tags($dlist).'</p>';
												} else {
													if( $i == 6 ){
														$html .= "<p><a target='_blank' href='".SITEURL."sitepanel/organisations/list_domain/".$user['User']['id']."'>more...</a></p>";
													}
												}
											$i++;
											}

										} else {
											$html .='<p>No Domain</p>';
										}
										$html .="</div>";
										?>
										<a title="Domains" data-tooltip="false" href="<?php echo SITEURL."sitepanel/organisations/list_domain/".$user['User']['id'];?>" class="pophover destroyP" data-content="<?php echo $html; ?>" ><i class="fa fa-list"></i></a>

									</td>
								</tr>
								<?php
										$num++;
										}//end foreach
								?>
								<?php

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
								<?php	} else {
                                ?>
								<tr>
                                    <td colspan="8" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
                                    <?php
										}
									?>
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<!------ Add New Classification ------>
				<div class="modal fade" id="RecordAdd" tabindex="-1" role="dialog" aria-hidden="true">
					<?php echo include('admin_thirdparty_add.ctp'); ?>
				</div><!-- /.modal -->

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
// Used for Sorting icons on listing pages
$('th a').append(' <i class="fa fa-sort"></i>');
$('th a.asc i').attr('class', 'fa fa-sort-down');
$('th a.desc i').attr('class', 'fa fa-sort-up');

// Delete click Update
$(document).on('click', '.RecordDeleteClass', function(){
	var id = $(this).attr('rel');
	var deleteAction = $(this).attr('url');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').attr('action', deleteAction);
});


// Status click Update
$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/users/user_updatestatus');
	$('#recordID').val(id);
	if(rel == 'activate'){
		$('#recordStatus').val(1);
	}else{
		$('#recordStatus').val(0);
	}
	$('#statusname').text(rel);
});

$(document).on('click', '#RecordStatusFormId .btn-success', function(){
		$('#ajax_overlay').show();
});

// Open Edit Form
$(document).on('click','.edituser', function (e) {
  var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){
			if($.trim(response) != 'success'){
				$('#Recordedit').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		}
	});
})

// Open organisationusers Form
$(document).on('click','.organisationusers', function (e) {
  var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){
			if($.trim(response) != 'success'){
				$('#Recordview').html(response);
			}else{
				location.reload(); // Saved successfully
			}
		}
	});
})

// Open View Form
$(document).on('click','.databasesetupclass', function (e) {
	var $that = $(this);

	BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Are you sure you want to setup your database?',
            type: BootstrapDialog.TYPE_DANGER,
            draggable: true,
            buttons: [
                {
                    icon: 'fa fa-check',
                    label: ' Yes',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function (dialogRef) {
                         var formURL = $that.data('where');
						 var dnames = $that.attr('rel');
						 var orguserid = $that.data('orguid');
						 //console.log(orguserid);
						 $(this).find('i').removeAttr('class').addClass('fa fa-spinner fa-spin');
                        $.when(
							$.ajax({
								url : formURL,
								data: { dname: dnames, orguser: orguserid },
								async:false,
								type: "post",
								dataType: 'json',
								success:function(response){
									$(this).find('i').removeAttr('class').addClass('fa fa-database');
									if( response.success == false ){
										//console.log(response);
										location.reload();
									}else{
										location.reload();
									}
								}
							})
						)
                            .then(function( data, textStatus, jqXHR ) {

                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                dialogRef.getModalBody().html('<div class="loader"></div>');
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 500);
                        })
                    }
                },
                {
                    label: ' No',
                    icon: 'fa fa-times',
                    cssClass: 'btn-danger',
                    action: function (dialogRef) {
                        dialogRef.close();
                    }
                }
            ]
      })
})


// Open View Form
$(document).on('click','.databasedropclass', function (e) {
		var $that = $(this);
		BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Are you sure you want to delete database?',
            type: BootstrapDialog.TYPE_DANGER,
            draggable: true,
            buttons: [
                {
                    icon: 'fa fa-check',
                    label: ' Yes',
                    cssClass: 'btn-danger',
                    autospin: true,
                    action: function (dialogRef) {

					    $('body').css('pointer-events','none');
                        var formURL = $that.data('where');
						console.log(formURL);
						var dbid = $that.attr('rel');
						var orguserid = $that.data('orguid');
                        $.when(
							$.ajax({
								url : formURL,
								data: { dbid: dbid,orguid:orguserid },
								async:false,
								type: "post",
								dataType: 'json',
								success:function(response){
								    $('body').css('pointer-events','auto');
									$(this).find('i').removeAttr('class').addClass('fa fa-database');
									if( response.success == false ){
										//console.log(response);
										location.reload();
									}else{
										location.reload();
									}
								}
							})
						)
                            .then(function( data, textStatus, jqXHR ) {

                                dialogRef.enableButtons(false);
                                dialogRef.setClosable(false);
                                dialogRef.getModalBody().html('<div class="loader"></div>');
                                setTimeout(function () {
                                    dialogRef.close();
                                }, 500);
                        })
                    }
                },
                {
                    label: ' No',
                    icon: 'fa fa-times',
                    cssClass: 'btn-danger',
                    action: function (dialogRef) {
                        dialogRef.close();
                    }
                }
            ]
        })

})
$(function () {

	$('.destroyP').tooltip('destroy');

	$('#popup_model_box_profile').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
		$(this).find('.modal-content').html('')
	});

	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });
})
</script>

  <!-- MODAL BOX WINDOW -->
    <div class="modal modal-success fade " id="popup_model_box_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

<style>
.popover p:first-child {
    font-weight: normal !important;
    width: 170px;
}


#ajax_overlay, .ajax_overlay, #tl_overlay {
    background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0;
    display: none;
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 10000;
}
.ajax_overlay_loader {
    background: rgba(0, 0, 0, 0) url("<?php echo SITEURL; ?>/images/ajax-loader-1.gif") no-repeat scroll left center;
    height: 30%;
    margin: 0 auto;
    padding: 490px 0 0;
    position: relative;
    width: 10%;
    z-index: 999;
}

body.noscroll {
    overflow: scroll;
}
.ajax_flash {
    background: #679f27 none repeat scroll 0 0;
    border: medium none;
    color: #ffffff;
    display: none;
    font-size: 16px;
    font-weight: normal;
    left: 0;
    overflow: hidden;
    padding: 10px;
    position: fixed;
    text-align: center;
    top: 0;
    width: 100%;
    z-index: 2000;
}
.ajax_flash > #message {
    background: rgba(0, 0, 0, 0.7) none repeat scroll 0 0;
    border: 3px solid #ffffff;
    border-radius: 4px;
    color: #ffffff;
    display: none;
    font-size: 13px;
    font-weight: normal;
    left: 4%;
    overflow: hidden;
    padding: 10px;
    position: absolute;
    text-align: center;
    top: 40%;
    width: 90%;
    z-index: 2000;
}
#circularG {
    height: 128px;
    position: relative;
    width: 128px;
}
.circularG {
    animation-direction: normal;
    animation-duration: 0.96s;
    animation-iteration-count: infinite;
    animation-name: bounce_circularG;
    background-color: #ffffff;
    border-radius: 19px;
    height: 29px;
    position: absolute;
    width: 29px;
}
#circularG_1 {
    animation-delay: 0.36s;
    left: 0;
    top: 50px;
}
#circularG_2 {
    animation-delay: 0.48s;
    left: 14px;
    top: 14px;
}
#circularG_3 {
    animation-delay: 0.6s;
    left: 50px;
    top: 0;
}
#circularG_4 {
    animation-delay: 0.72s;
    right: 14px;
    top: 14px;
}
#circularG_5 {
    animation-delay: 0.84s;
    right: 0;
    top: 50px;
}
#circularG_6 {
    animation-delay: 0.96s;
    bottom: 14px;
    right: 14px;
}
#circularG_7 {
    animation-delay: 1.08s;
    bottom: 0;
    left: 50px;
}
#circularG_8 {
    animation-delay: 1.2s;
    bottom: 14px;
    left: 14px;
}
@keyframes bounce_circularG {
0% {
    transform: scale(1);
}
100% {
    transform: scale(0.3);
}
}
@keyframes bounce_circularG {
0% {
    transform: scale(1);
}
100% {
    transform: scale(0.3);
}
}
@keyframes bounce_circularG {
0% {
    transform: scale(1);
}
100% {
    transform: scale(0.3);
}
}
.gif_preloader11 {
    height: 30%;
    margin: 20px 0 0 60px;
    position: relative;
    width: 10%;
    z-index: 999;
}
.ajax_overlay_preloader, #tl_overlay {
    display: none;
}
.gif_preloader {
    background: #67a028 none repeat scroll 0 0;
    border: 10px solid #19bee1;
    border-radius: 100%;
    height: 150px;
    left: 50%;
    margin: -60px 0 0 -60px;
    position: absolute;
    top: 50%;
    width: 150px;
}
.gif_preloader::after {
    animation: 2s linear 0s normal none infinite running rotate;
    border-radius: 100%;
    box-shadow: -4px -5px 3px -3px rgba(255, 255, 255, 0.6);
    content: "";
    height: 140%;
    left: -20%;
    opacity: 0.7;
    position: absolute;
    top: -20%;
    width: 140%;
}
@keyframes rotate {
0% {
    transform: rotateZ(0deg);
}
100% {
    transform: rotateZ(360deg);
}
}
.gif_preloader .loading_text {
    color: #ffffff;
    display: block;
    font-weight: bold;
    margin: 40% auto;
    text-align: center;
    width: 100px;
}
.box-body .el.panel .panel-body.collapse.in {
    border-top: 1px solid #dddddd;
}
</style>

<div id="ajax_overlay" class="ajax_overlay_preloader">
	<div id="" class="gif_preloader" style="">
		<div id="" class="loading_text" style="">Loading..</div>
	</div>
</div>