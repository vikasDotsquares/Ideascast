
<style>
#Recordlisting .tipText, .tipText > a:hover{
	text-transform:lowercase !important;
}
#Recordlisting .box-header p > a{
	text-transform:lowercase !important;
}
</style>
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
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>
                    <p class="text-muted date-time" style="padding: 4px 0px; text-transform: none;">
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
				<div class="box noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding">
			<?php $class = 'collapse';
					if(isset($in) && !empty($in)){
						$class = 'in';
					}
			?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box no-box-shadow box-success">
					<div class="box-header">
						<div class="col-xs-7 col-sm-7 domain-setting">
							<p class="domain-setting-text">Account:
							<?php
								$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
								$whatINeed = $whatINeed[0];

								echo "<a class='customtipText ' style='text-transform:lowercase' title='https://".$whatINeed.WEBDOMAIN."' target='_blank' href='https://".$whatINeed.WEBDOMAIN."'>".$whatINeed.WEBDOMAIN."</a>";

							?></p>
						</div>
						<div class="col-xs-5 col-sm-5 domain-setting">
							<div class="pull-right padright">
								<a class="btn btn-primary edituser tipText" data-toggle="modal" data-target="#modal_box" title="Add Email Domain" data-remote="<?php echo SITEURL."organisations/manage_domain" ; ?>"  data-tooltip="tooltip" data-placement="top" style="text-transform: capitalize !important" >
									Add
								</a>&nbsp;<?php if( !empty($listdomain) && count($listdomain) > 0 ){?><a class="btn btn-primary deletemultiple" >
									Delete
								</a><?php } ?>
							</div>
						</div>
					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th><input type="checkbox" name="checkAlltop" id="checkDomains" /></th>
									<th><?php echo __("Email Domain");?></th>
									<th><?php echo __("Created by");?></th>
									<th><?php echo __("Created on");?></th>
									<th><?php echo __("Enabled");?></th>
									<th><?php echo __("Action");?></th>
								</tr>
							</thead>
							<tbody id="tbody_skills">
								<?php
									if (!empty($listdomain)) {
										$icount = 0;
										foreach ($listdomain as $listdomains){

										$domainUser = $this->Common->checkEmailDomainUser($listdomains['ManageDomain']['domain_name']);

										$userDetail = $this->ViewModel->get_user_data( $listdomains['ManageDomain']['user_id'] );
										$user_name = '';
										if(isset($userDetail) && !empty($userDetail)) {
											$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
										}



                                ?>
								<tr data-id="<?php echo $listdomains['ManageDomain']['id']; ?>">

									<td>
									<?php if( isset($domainUser) && $domainUser == true ){ ?>
										<input type="checkbox" class="checkDomainList" name="checkAll" disabled="disabled"  />
									<?php } else { ?>
										<input type="checkbox" class="checkDomainList" name="checkAll" value="<?php echo $listdomains['ManageDomain']['id']; ?>" />
									<?php } ?>
									</td>
									<td><span class="tipText text-limit-field" title="<?php echo "Total User(s): ".$this->Common->getEmailDomainUser($listdomains['ManageDomain']['id']);?>" style="text-transform: none !important;"><a href="<?php echo SITEURL?>organisations/manage_users/<?php echo $listdomains['ManageDomain']['id'];?>"><?php echo $listdomains['ManageDomain']['domain_name'];?></a></span></td>

									<td><a href="#" data-remote="<?php echo Router::url(array('controller' => 'shares', 'action' => 'show_profile', $listdomains['ManageDomain']['user_id']));?>" data-target="#popup_modal" data-toggle="modal" ><?php echo $user_name;?></a></td>
									<td><?php echo _displayDate($listdomains['ManageDomain']['created'], 'd M Y'); ?></td>
									<td><?php
											$clasificationId = $listdomains['ManageDomain']['id'];
											if($listdomains['ManageDomain']['create_account'] == 1){ ?>
												<button rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordCreateClass"><i class="fa fa-fw fa-check alert-success"></i></button>
										<?php } else { ?>
												<button  rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordCreateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php } ?>
									</td>
									<?php /* <td>
										<?php
											$clasificationId = $listdomains['ManageDomain']['id'];
											if($listdomains['ManageDomain']['status'] == 1){ ?>
												<button rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button>
										<?php } else { ?>
												<button  rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php }	?>

									</td> */ ?>
									<td>
										<?php


										if( isset($domainUser) && $domainUser == true ){

										?>
										<a class="tipText" title="Editing not allowed"   data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
										<a class="tipText" title="Deletion not allowed" data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>

										<?php } else {

											$editURL = SITEURL."organisations/manage_domain/".$listdomains['ManageDomain']['id'];
											$deleteURL = SITEURL."organisations/domain_delete/";

										?>
										<a data-toggle="modal" class="edituser tipText" data-target="#modal_box" title="Edit Email Domain" data-remote="<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" style="cursor:pointer;" ><i class="fa fa-fw fa-edit"></i></a>
										<a class="RecordDeleteClass tipText disabled" rel="<?php echo $listdomains['ManageDomain']['id']; ?>" title="Delete Email Domain" data-whatever="<?php echo $deleteURL; ?>" data-tooltip="tooltip" data-placement="top" style="cursor:pointer;"><i class="fa fa-fw fa-trash-o"></i></a>

									<?php } ?>
									</td>
								</tr>
							<?php
									$icount++;
							} //end foreach
							if($this->params['paging']['ManageDomain']['pageCount'] > 1) {
							?>
								<tr>
                                    <td colspan="7" align="right">
									<ul class="pagination">
										<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
										<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
										<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
									</ul>
									</td>
								</tr>
							<?php }
							} else { ?>
								<tr>
                                    <td colspan="7" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
				<!------ Add New Classification ------>
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
<link rel="stylesheet" type="text/css" href="/css/front-paging.css" />
<script type="text/javascript" >

$(function(){

	$('.customtipText').tooltip({
		template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		, 'container': 'body', 'placement': 'top',
	})

	setTimeout(function(){

		$('#successFlashMsg').hide('slow', function(){ $('#successFlashMsg').remove() })

	},4000);


	$('body').delegate('#ManageDomainDomainName', 'keypress', function(event){

		var englishAlphabetAndWhiteSpace = new RegExp('^[a-zA-Z0-9\-]$');
		var key = String.fromCharCode(event.which);

		if(event.shiftKey){
			if(event.keyCode == 37){
				$("#add_edit_msg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
				return false;
			}

		}
		if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetAndWhiteSpace.test(key)) {
			$("#add_edit_msg").html("");
			return true;
		}else {
			$("#add_edit_msg").removeClass('text-green').addClass('text-red').html("Special Characters and white spaces are not allowed");
		}
		return false;

	});


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

			var deleteURL = '<?php echo SITEURL; ?>organisations/domain_updatestatus';

			$('#recordID').val(id);
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want to change the status?',
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
								/* $that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								}); */

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							/* dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								location.reload();
							}, 500); */
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

			var deleteURL = '<?php echo SITEURL; ?>organisations/domain_createaccount';

			$('#recordID').val(id);
			if(rel == 'activate'){
				$('#recordStatus').val(1);
			}else{
				$('#recordStatus').val(0);
			}
			$('#statusname').text(rel);

			BootstrapDialog.show({
				title: 'Confirmation',
				//message: 'Are you sure you want to change the create account?',
				message: 'Are you sure you want to change status?',
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

		$(".RecordDeleteClass").click(function(event){
			event.preventDefault();

			$that = $(this);
			var row = $that.parents('tr:first');

			var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
			var deleteid = $(this).attr('rel');

			BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want to remove this domain?',
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
								data: $.param({domain_id:deleteid}),
								global: true,
								async:false,
								success:function(response){
									console.log(response);
								}
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else{

								$that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								});

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								location.reload();
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
				message: 'Are you sure you want to remove this domain?',
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
									url : deleteURL = '<?php echo SITEURL."organisations/domain_delete/";?>',
									type: "POST",
									data: $.param({domain_id:allChecked}),
									global: true,
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
											}
										}
									})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){

								$('#Recordedit').html(data);

							}else{

								$that.closest('tr').css('background-color','#FFBFBF');
								row.children('td, th').animate({
									padding: 0
									}).wrapInner('<div />').children().slideUp(1000,function () {
									$that.closest('tr').remove();
								});

							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();
								location.reload();
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



			 }
		});

		$('#Recordedit,#modal_box').on('hidden.bs.modal', function(){
			$(this).removeData('bs.modal')
			$(this).find('.modal-content').html('')
		})
});

</script>
<style>
.tooltip{ text-transform: none !important;}
</style>