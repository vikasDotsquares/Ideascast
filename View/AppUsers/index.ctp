
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
                <h1 class="pull-left">API Users
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;">Manage API Users</span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box  noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding">
			<?php
				$class = 'collapse';
				if(isset($in) && !empty($in)){
					$class = 'in';
				}
				$per_page_show = $this->Session->read('api_user.per_page_show');
				$keyword = $this->Session->read('api_user.keyword');
				$status = $this->Session->read('api_user.status');
				//style="width: 141px;top: 7px;font-size: 16px;"
			?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box no-box-shadow box-success">
					<div class="box-header">
							<div class="margintop">
								<div class="box-title page-recode-title" style="float:left;" >
									<small>Per Page Records</small>
								</div>
								<div class="page-recode">
									<?php
									echo $this->Form->create('AppUser', array( 'url' => array('controller'=>'app_users', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' ));

									echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();'  ));
									?>
									</form>
								</div>
							</div>

						<div class="pull-right padright">
						<?php

							$url = array('controller' => 'app_users','action' => 'index');
							if( $this->params['paging']['AppUser']['page'] > 1 ){
								$my_params = array(
									'sort'=>'AppUser.api_email',
									'direction' => 'desc'
								);
							} else {
								$my_params = array(
									'sort'=>'AppUser.api_email',
									'direction' => 'desc'
								);
							}
							$sortUrl = $this->Html->url(array_merge($url, $my_params));
							$sortText = 'Sort Desc';

							if(isset($this->params['named']['sort']) && !empty($this->params['named']['sort']) && ($this->params['named']['direction'] == 'desc' || $this->params['named']['direction'] == 'DESC' )){

								if( $this->params['paging']['AppUser']['page'] > 1 ){
									$my_params = array(
										'sort'=>'AppUser.api_email',
										'direction' => 'asc'
									);
								} else {
									$my_params = array(
										'sort'=>'AppUser.api_email',
										'direction' => 'asc'
									);
								}
								$sortUrl = $this->Html->url(array_merge($url, $my_params));
								$sortText = 'Sort ASC';

							}

						?>
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>
							<?php
							if( isset($organisationDetails) && !empty($organisationDetails) && isset($organisationDetails['OrgSetting']['apilicense']) && $apiusercount < $organisationDetails['OrgSetting']['apilicense']  ){ ?>
							<a class="btn btn-primary" href="<?php echo SITEURL; ?>app_users/add"> Add API User</a>
							<?php } ?>

						</div>

						<div class="<?php echo $class; ?> search pull-right" style="width: 100%; margin-top: 10px;" id="collapseExample">
							<div class="well">
								<?php

								if(isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder'])){
									$formAction = SITEURL."app_users/index/page:".$this->params['paging']['AppUser']['page']."/sortorder:".$this->params['named']['sortorder'];
								} else {
									$formAction = SITEURL."app_users/";
								}

								echo $this->Form->create('AppUser', array( 'url' => $formAction, 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body">
										<div class="form-group">
											<div class="col-lg-1">
												<label for="focusedInput" class="control-label">Keyword:</label>
											</div>
											<div class="col-lg-4">
												<?php echo $this->Form->input('keyword', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyword,'div' => false, 'class' => 'form-control')); ?>
											</div>
											<div class="col-lg-1">
												<label for="focusedInput" class="control-label">Status:</label>
											</div>
											<div class="col-lg-3">
												<?php $options = array('1' => 'Active', '0'=>'Deactive');
												 echo $this->Form->input('status', array('options'=>$options, 'empty' => '- Select Status -','label' => false, 'selected'=>$status, 'div' => false, 'class' => 'form-control')); ?>
											</div>
											<div class="col-lg-3" style="text-align:right">
												<button type="submit" class="searchbtn btn btn-success">Go</button>
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>app_users/user_resetfilter" >Close</a>
											</div>
										</div>
									</div>
								<?php echo $this->Form->end(); ?>
							</div>
						</div>
					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
						<table id="example2" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>

									<th><?php echo $this->Paginator->sort('AppUser.api_email',__("API User Email"));?></th>
									<th><?php echo $this->Paginator->sort('AppUser.api_username',__("API User Username"));?></th>
									<th><?php echo $this->Paginator->sort('AppUser.created',__("API User Created"));?></th>
									<th><?php echo $this->Paginator->sort('AppUser.api_key',__("API User Key"));?></th>
									<th><?php echo $this->Paginator->sort('AppUser.status',__("API User Status"));?></th>
									<th><?php echo $this->Paginator->sort('AppUser.web_execution_permission',__("API User Web Permission"));?></th>

									<th>Actions</th>
								</tr>
							</thead>
							<tbody id="tbody_skills">
								<?php

									if (!empty($users)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($users as $user) { //pr($user);
                                ?>
								<tr>
									<td><?php echo $num; ?></td>

									<td><?php echo $user['AppUser']['api_email']; ?></td>

									<td><?php echo $user['AppUser']['api_username']; ?></td>
									<td><?php echo dateFormat($user['AppUser']['created']); ?></td>
									<td><?php echo $user['AppUser']['api_key']; ?></td>
									<td>
										<?php
											$clasificationId = $user['AppUser']['id'];
											if ($user['AppUser']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>

									<td>
										<?php

											if ($user['AppUser']['web_execution_permission'] == 1) { ?>
												<button data-toggle="modal" data-target="#WebExecutionStatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default WebExecutionRecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#WebExecutionStatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default WebExecutionRecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>
									<td>

										<?php
											$editURL = SITEURL."app_users/edit/".$user['AppUser']['id'];
										?>

										<!--<a data-toggle="modal" class="edituser" data-target="#Recordedit"   title="Edit User" data-whatever= "<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-edit"></i></a>-->

										<!--<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit User" ><i class="fa fa-fw fa-edit"></i></a>-->



										<?php
										$deleteURL = SITEURL."app_users/delete/".$user['AppUser']['id'];
										?>

										<a  class="RecordDeleteClass tipText"   rel="<?php echo $user['AppUser']['id']; ?>"   title="Delete User" url="<?php echo $deleteURL; ?>" style="cursor: pointer;" ><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>
								<?php
										$num++;
										}//end foreach
								}else{
                                ?>
								<tr>
                                    <td colspan="8" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
                                    <?php
										}
									?>
							</tbody>
							<tfoot>
								<?php
								if($this->params['paging']['AppUser']['pageCount'] > 1) { ?>
								<tr>
                                    <td colspan="8" align="right">

										<div class="pagination-summary pull-left" >
											<?php
												echo $this->Paginator->counter(array(
												'format' => __('{:current} of {:count} &nbsp;/&nbsp; Page {:page} of {:pages}')
												));
											?>
										</div>
										<ul class="pagination">
											<?php
											echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span')); ?>
											<?php //echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li', 'disabledTag' => 'span', 'first' => 1));?>
											<?php echo $this->Paginator->numbers(array('first'=>1,'last'=>1,'ellipsis'=>'<li><a>...</a></li>','modulus'=>3,'tag' => 'li','separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li'), null, array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'span'));?>
											<?php echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'span')); ?>
										</ul>

									</td>
								</tr>
								<?php } ?>
							</tfoot>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<!------ Add New Classification ------>


				         </div></div>
    </section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>

 <!------ Record Status Update Confirmation Message ------>
<div class="modal fade" id="WebExecutionStatusBox" tabindex="-1" role="dialog" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
		    <?php echo $this->Form->create('', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'WebExecutionFormId')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Are you sure, you would like to <span id="statusname"></span> this record?</h4>
			</div>
			<input type="hidden" id="recordID" name='data[id]' />
			<input type="hidden" id="recordWebExecutionPermission" name='data[web_execution_permission]' />
			<div class="modal-footer clearfix bordertopnone">
				<button type="submit" class="btn btn-success"><i class="fa fa-fw fa-check"></i> Yes</button>
				<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
			</div>
</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->

<script type="text/javascript" >
// Used for Sorting icons on listing pages
$('th a').append(' <i class="fa fa-sort"></i>');
$('th a.asc i').attr('class', 'fa fa-sort-down');
$('th a.desc i').attr('class', 'fa fa-sort-up');

// Delete click Update
$(document).on('click', '.RecordDeleteClass123', function(){
	id = $(this).attr('rel');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>app_users/delete');
});


$(".RecordDeleteClass").click(function(event){
	event.preventDefault();

	$that = $(this);
	var row = $that.parents('tr:first');

	//var deleteURL = $(this).attr('data-whatever'); // Extract info from data-* attributes
	var deleteURL = $js_config.base_url+'app_users/delete'; // Extract info from data-* attributes
	var deleteid = $(this).attr('rel');

	BootstrapDialog.show({
		title: 'Confirmation',
		message: 'Are you sure you want to remove this record?',
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
						data: $.param({id: deleteid}),
						global: true,
						// async:false,
						success:function(response){
							location.reload();
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


// Status click Update
$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>app_users/user_updatestatus');
	$('#recordID').val(id);
	if(rel == 'activate'){
		$('#recordStatus').val(1);
	}else{
		$('#recordStatus').val(0);
	}
	$('#statusname').text(rel);
});

$(document).on('click', '.WebExecutionRecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#WebExecutionFormId').attr('action', '<?php echo SITEURL; ?>app_users/update_web_permission');
	$('form#WebExecutionFormId').find('input#recordID').val(id);
	if(rel == 'activate'){
		$('form#WebExecutionFormId').find('input#recordWebExecutionPermission').val(1);
	}else{
		$('form#WebExecutionFormId').find('input#recordWebExecutionPermission').val(0);
	}
	$('form#WebExecutionFormId').find('input#statusname').text(rel);
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

// Open View Form
$(document).on('click','.viewuser', function (e) {
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



</script>

<style>
.well{margin-bottom: 0px; min-height: auto; padding: 0;}
#search_page_show_form .modal-body .form-group{ margin-bottom: 0px;}
</style>