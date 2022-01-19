
<style>
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
    background: rgba(0, 0, 0, 0) url("../images/ajax-loader-1.gif") no-repeat scroll left center;
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

.action-db .drop-db {
    position: relative;
    bottom: -3px;
    right: 3px;
    background: #c00;
    color: #fff;
    border-radius: 50%;
    padding: 0px 1px 1px 2px;
    font-size: 10px;
}

.action-db .text-db {
	background: #ccc;
    position: relative;
    bottom: -3px;
    right: 3px;
    color: #fff;
    border-radius: 50%;
    padding: 0px 1px 1px 2px;
    font-size: 10px;
}

.activerows{
	background:peachpuff;
}

.RecordUpdateSdkClass, .RecordUpdateSdkClass:hover{
    padding:0 4px;
    margin: 0;
    background-color: #6eb243;
    position: absolute;
	border:none;
	border-radius:0;
}
.RecordUpdateSdkClass img{
	width:10px;
}

.RecordUpdateSdkClass .sdk-drop-db {
    position: absolute;
    bottom: -3px;
    right: -5px;
    background: #c00;
    color: #fff;
    border-radius: 50%;
    padding: 0px 1px 1px 2px;
    font-size: 10px;
}

.tooltip .tooltip-inner {
    max-width: 100%;
}

</style>
<div id="ajax_overlay" class="ajax_overlay_preloader">
	<div id="" class="gif_preloader" style="">
		<div id="" class="loading_text" style="">Loading..</div>
	</div>
</div>
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
					<li><a href="<?php echo SITEURL; ?>sitepanel/organisations"><i class="fa fa-building"></i> Organization</a></li>
					<li><a href="<?php echo SITEURL; ?>sitepanel/organisations"> Linked Domains</a></li>
					<li class="active"><?php echo $organisationname;?></li>
				</ol>
				<h3>Domains (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}
			$per_page_show = $this->Session->read('orgsetting.per_page_show');
			$keyword = $this->Session->read('orgsetting.keyword');
			$status = $this->Session->read('orgsetting.status');

		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title page-recode-title">
							<small>Per Page Records</small> </h3>
								<div class="page-recode">
										<?php
										echo $this->Form->create('OrgSetting', array( 'url' => array('controller'=>'organisations', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' ));

										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>$per_page_show, 'div' => false, 'class' =>'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();'));
										?>
										</form>
								</div>
								<div class="pull-right padright">
									<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
										Search
									</a>
									<?php $add = SITEURL."sitepanel/organisations/add_domain/".$this->params['pass'][0]; ?>
									<a class="btn btn-primary tipText" title="Add Linked Domain" href="<?php echo $add; ?>">
										Add Linked Domain
									</a>
								</div>
								<div class="<?php echo $class; ?> search" id="collapseExample">
									<div class="well">
										<?php echo $this->Form->create('OrgSetting', array( 'url' => array('controller'=>'organisations', 'action'=>'admin_list_domain', $this->params['pass'][0]), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
											<div class="modal-body">
												<div class="form-group">
													<div class="col-lg-1">
														<label for="focusedInput" class="control-label">Keyword:</label>
													</div>
													<div class="col-lg-3">
														<?php echo $this->Form->input('OrgSetting.keyword', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
													</div>
													<div class="col-lg-1">
														<label for="focusedInput" class="control-label">Status:</label>
													</div>
													<div class="col-lg-3">
														<?php $options = array('1' => 'Active', '0'=>'Deactive');
														 echo $this->Form->input('OrgSetting.status', array('options'=>$options, 'empty' => '- Select Status -','label' => false, 'selected'=>$status, 'div' => false, 'class' => 'form-control')); ?>
													</div>

													<div class="col-lg-4" style="text-align:right;">
														<button type="submit" class="searchbtn btn btn-success">Go</button>
														<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/organisations/list_resetfilter/<?php echo $this->params['pass'][0]; ?>" >Close</a>
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
									<th><?php echo __("Domains");?></th>
									<th><?php echo __("License");?></th>
									<th><?php echo __("Allowed Space");?></th>
									<th><?php echo __("Start Date");?></th>
									<th><?php echo __("Primary");?></th>
									<th><?php echo __("Status");?></th>
									<th><?php echo __("Actions");?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($listdomain)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										//pr($listdomain);
										foreach ($listdomain as $domainlist) { //pr($user);
                                ?>
								<tr <?php if($num == 1 && $domainlist['OrgSetting']['prmry_sts']==1){ ?> class="activerows"<?php } ?>>
									<td><?php echo $num ?></td>
									<td><a class="tipText" title="https://<?php echo $domainlist['OrgSetting']['subdomain'].WEBDOMAIN;?>" target="_blank" href="https://<?php echo $domainlist['OrgSetting']['subdomain'].WEBDOMAIN;?>"><?php echo $domainlist['OrgSetting']['subdomain']; ?></a></td>
									<td><?php echo $domainlist['OrgSetting']['license']; ?></td>
									<td><?php echo $domainlist['OrgSetting']['allowed_space']." GB"; ?></td>
									<td><?php echo date('j<\s\u\p\>S<\/\s\u\p\> M Y',strtotime($domainlist['OrgSetting']['start_date']));?></td>
									<?php /*<td><?php //echo dateFormat(date('Y-m-d',$domainlist['OrgSetting']['created'])); ?></td>*/ ?>

									<td>
										<?php
											$clasificationId = $domainlist['OrgSetting']['id'];
											if ($domainlist['OrgSetting']['prmry_sts'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBoxPrimary" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordPrimaryStatus"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBoxPrimary" rel="activate" id="<?php echo $clasificationId; ?>" class="btn btn-default RecordPrimaryStatus"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>

									<td>
										<?php
											$clasificationId = $domainlist['OrgSetting']['id'];
											if ($domainlist['OrgSetting']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>

									<td class="action-db">
										<?php
										$editURL = SITEURL."sitepanel/organisations/edit_domain/".$domainlist['OrgSetting']['id'].'/'.$domainlist['User']['id'];
										?>
										<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit" ><i class="fa fa-fw fa-edit"></i></a>

										<?php
											$deleteURL = SITEURL."sitepanel/organisations/deleteAuth_domain/".$domainlist['OrgSetting']['id'];
										?>
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $domainlist['OrgSetting']['org_id']; ?>" title="Delete" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" data-orgid="<?php echo $domainlist['OrgSetting']['id'];?>" data-userid="<?php echo $domainlist['OrgSetting']['user_id'];?>" ><i class="fa fa-fw fa-trash-o"></i></a>

										<?php
										$orgUsers = SITEURL."sitepanel/organisations/orgdetails/".$domainlist['User']['id']."/".$domainlist['OrgSetting']['id'];
										?>
										<a data-toggle="modal" class="viewusers"  data-target="#popup_model_box_profile" title="Account Details" data-remote="<?php echo $orgUsers; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-globe"></i></a>

										<?php
											if( isset($domainlist['OrgSetting']['db_setup']) && $domainlist['OrgSetting']['db_setup'] == 0 ){
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

											$ndbname = preg_replace('/[.,]/', '', $domainlist['OrgSetting']['subdomain']);
											$latestdb = str_replace('-','',$ndbname);
										?>
										<a data-toggle="modal" class="<?php echo $setupclass;?>" data-target="#setupdb" data-orguid="<?php echo $domainlist['OrgSetting']['id']; ?>" rel="<?php echo $latestdb; ?>"   title="<?php echo $setupTooltip; ?>" data-where="<?php echo $dbimport; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-database <?php echo $setupdisclass;?>" aria-hidden="true"></i></a>

										<?php
										if( isset($domainlist['OrgSetting']['db_setup']) && $domainlist['OrgSetting']['db_setup'] == 0 ){
											$setupclass = 'disabled';
											$dbdrop = '';
											$setupTooltip = 'Database is already empty';
											$setupdisclass = 'text-gray';
											$extracls = " text-db";

										} else {
											$setupclass = 'databasedropclass';
											$dbdrop = SITEURL."sitepanel/organisations/drop_tables";
											$setupTooltip = 'Drop Database';
											$setupdisclass = '';
											$extracls = " ";
										}
										?>
										<a data-toggle="modal" class="<?php echo $setupclass; ?>" data-target="#dropdb" rel="<?php echo $domainlist['OrgSetting']['org_id']; ?>" data-orguid="<?php echo $domainlist['OrgSetting']['id']; ?>" title="<?php echo $setupTooltip; ?>" data-where="<?php echo $dbdrop; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-database <?php echo $setupdisclass;?>" aria-hidden="true"></i><i class="fa fa-times drop-db <?php echo $extracls;?>"></i></a>

										<?php
										$licenseusers = $this->Common->domain_users($domainlist['User']['id'], $domainlist['OrgSetting']['id']);

										if( $setupclass == "databasedropclass" && $licenseusers > 0 ){

													if( $domainlist['OrgSetting']['apisdk_status'] == 1 ){

														$setupclass = 'enableapiclass';
														$dbdrop = SITEURL."sitepanel/organisations/domain_sdkstatus";
														$setupTooltip = 'Enable API SDK';
														$setupdisclass = '';
														$extracls = " ";

													} else {

														$setupclass = 'disableapiclass';
														$dbdrop = SITEURL."sitepanel/organisations/domain_sdkstatus";
														$setupTooltip = 'Disable API SDK';
														$setupdisclass = '';
														$extracls = " ";

													}

											$clasificationsdkId = $domainlist['OrgSetting']['id'];
											if ($domainlist['OrgSetting']['apisdk_status'] == 1) { ?>
												<a data-toggle="modal" data-target="#SdkStatusBox" rel="disable" id="<?php echo $clasificationsdkId; ?>" data-tooltip="tooltip" data-placement="top" data-original-title="Disable API SDK" class="btn btn-default RecordUpdateSdkClass" aria-hidden="true"><img src="<?php echo SITEURL;?>images/switch-icon.png" ><i class="fa fa-times sdk-drop-db"></i></a>
											<?php	} else { ?>
												<a data-toggle="modal" data-target="#SdkStatusBox" rel="enable" id="<?php echo $clasificationsdkId; ?>" data-tooltip="tooltip" data-placement="top" data-original-title="Enable API SDK" class="btn btn-default RecordUpdateSdkClass" aria-hidden="true"><img src="<?php echo SITEURL;?>images/switch-icon.png" ></a>
										<?php	}
											}
										?>
									</td>
								</tr>
								<?php
										$num++;
										}//end foreach
								?>
								<?php
								if( $this->params['paging']['OrgSetting']['pageCount'] > 1) { ?>
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
	var user_id = $(this).attr('data-userid');
	var org_id = $(this).attr('data-orgid');
	var deleteAction = $(this).attr('url');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').prepend('<input type="hidden" id="recordDeleteUserid" name="data[user_id]"  value="'+user_id+'">');
	$('#RecordDeleteForm').prepend('<input type="hidden" id="recordDeleteOrgid" name="data[org_id]" value="'+org_id+'">');
	$('#RecordDeleteForm').attr('action', deleteAction);
});


// Status click Update
$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/organisations/domain_statusupdate');
	$('#recordID').val(id);
	if(rel == 'activate'){
		$('#recordStatus').val(1);
	}else{
		$('#recordStatus').val(0);
	}
	$('#statusname').text(rel);
});

// SDK Status click Update
$(document).on('click', '.RecordUpdateSdkClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');

	console.log(id);
	console.log(rel);

	$('#RecordSdkStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/organisations/domain_sdkstatus');
	$('#sdkrecordID').val(id);
	if(rel == 'enable'){
		$('#sdkrecordStatus').val(1);
	}else{
		$('#sdkrecordStatus').val(0);
	}
	$('#sdkstatusname').text(rel);

});

// Primary Status click Update
$(document).on('click', '.RecordPrimaryStatus', function(){
	var id = $(this).attr('id');
	var rel = $(this).attr('rel');
	$('#RecordStatusPrimaryFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/organisations/domain_primaryupdate');
	$('#RecordStatusPrimaryFormId #recordID').val(id);
	if(rel == 'activate'){
		$('#RecordStatusPrimaryFormId #recordStatus').val(1);
	}else{
		$('#RecordStatusPrimaryFormId #recordStatus').val(0);
	}
	$('#RecordStatusPrimaryFormId #statusname').text(rel);
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
            message: 'Are you sure you want to setup database?',
            type: BootstrapDialog.TYPE_DANGER,
			 closable: false,
            closeByBackdrop: false,
            closeByKeyboard: false,
            draggable: true,
            buttons: [
                {
                    icon: 'fa fa-check',
                    label: ' Yes',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function (dialogRef) {
					     $('body').css('pointer-events','none');
                         var formURL = $that.data('where');
						 var dnames = $that.attr('rel');
						 var orguserid = $that.data('orguid');
						 //console.log(orguserid);
						 $(this).find('i').removeAttr('class').addClass('fa fa-spinner fa-spin');
                        $.when(
							$.ajax({
								url : formURL,
								data: { dname: dnames, orguser: orguserid },
								//async:false,
								global:true,
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


// Open View Form
$(document).on('click','.databasedropclass', function (e) {
		var $that = $(this);
		BootstrapDialog.show({
            title: 'Confirmation',
            message: 'Are you sure you want to delete database?',
            type: BootstrapDialog.TYPE_DANGER,
			 closable: false,
            closeByBackdrop: false,
            closeByKeyboard: false,
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
								/* async:false, */
								global:true,
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
		$('#popup_model_box_profile').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')
		});


			$(document).ajaxSuccess(function (event, jqXHR, ajaxSettings, data) {
						   // $('.tooltip').hide()

							if ($(".ajax_overlay_preloader").length > 0) {
								$(".ajax_overlay_preloader").fadeOut(150);
								$("body").removeClass('noscroll');
							}
			});


			$(document).ajaxSend(function (e, xhr) {

                window.theAJAXInterval = 1;
                // $("#ajax_overlay_text").textAnimate("..........");
                $(".ajax_overlay_preloader")
                        .fadeIn(300)
                        .bind('click', function (e) {
                            $(this).fadeOut(300);
                        });

                $("body").addClass('noscroll');
            })
            .ajaxComplete(function () {
                setTimeout(function () {
                    $(".ajax_overlay_preloader").fadeOut(300);
                    $("body").removeClass('noscroll');
                    clearInterval(window.theAJAXInterval);
                }, 2000)

			 });

			$.ajaxSetup({
                global: false,
                headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                }
            });


})
</script>

  <!-- MODAL BOX WINDOW -->
    <div class="modal modal-success fade " id="popup_model_box_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

