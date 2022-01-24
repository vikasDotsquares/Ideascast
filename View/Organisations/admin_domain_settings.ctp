<div class="row">
	<div class="col-xs-12"> 
	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box ">
				<div class="box-header">
				
				<ol class="breadcrumb">
					<li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
					<li class="active">Domains</li>
				</ol>
				<h3>Manage Domains (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('user.per_page_show'); 
			$domain_name = $this->Session->read('OrganisationUser.domain_name');
			$email = $this->Session->read('user.email'); 
			
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('User', array( 'url' => array('controller'=>'users', 'action'=>'admin_domain_settings'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
						<div class="pull-right padright">
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>
						</div>
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('User', array( 'url' => array('controller'=>'users', 'action'=>'admin_domain_settings'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body">
										<div class="form-group">
											<div class="col-lg-1">
												<label for="OrganisationUserDomainName" class="control-label">Domain name:</label>
											</div>
											<div class="col-lg-3">
												<?php echo $this->Form->input('OrganisationUser.domain_name', array('placeholder' => 'Enter domain name here...','type' => 'text','label' => false, 'value'=>$domain_name,'div' => false, 'class' => 'form-control')); ?>
											</div>
											
											<div class="col-lg-1">
												<label for="UserStatus" class="control-label">Status:</label>
											</div>
											<div class="col-lg-3">
												<?php echo $this->Form->input('User.email', array('placeholder' => 'Enter email here...','type' => 'text','label' => false, 'value'=>$email,'div' => false, 'class' => 'form-control')); ?>
											</div>
											
											<div class="col-lg-12" style="text-align:right; margin:20px 0 0 0">
												<button type="submit" class="searchbtn btn btn-success">Go</button>
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/users/user_third_resetfilter" >Close</a>
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
									<th>Domain Name</th>
									<th>Create Account</th>
									<th>Status</th> 
									<th>Actions</th>
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
									<td><?php echo $user['UserDetail']['first_name'].' '.$user['UserDetail']['last_name']; ?></td>
									<td><?php echo $user['User']['email']; ?></td>
									<td>
										<?php
											$clasificationId = $user['User']['id'];
											if ($user['User']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td> 
									<td>
										<?php 
										$viewURL = SITEURL."sitepanel/users/view/".$user['User']['id']; 
										?>
										
										<a data-toggle="modal" class="viewuser"  data-target="#Recordview"   title="View Author" data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-eye"></i></a>
										
										
										<?php 
										$editURL = SITEURL."sitepanel/users/thirdparty_edit/".$user['User']['id']; 
										?>
										
										<!--<a data-toggle="modal" class="edituser" data-target="#Recordedit"   title="Edit User" data-whatever= "<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-edit"></i></a> -->

										<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit Author" ><i class="fa fa-fw fa-edit"></i></a>
										
										<a  class="editusers" data-target="#Recordeditss" href= "javascript:void(0)"  title="View Projects" ><i class="fa fa-folder-open"></i></a>
										
										<?php 
										$deleteURL = SITEURL."sitepanel/users/deleteAuth/".$user['User']['id']; 
										?>
										
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $user['User']['id']; ?>"   title="Delete Author" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['User']['pageCount'] > 1) { ?> 
								<tr>
                                    <td colspan="6" align="right">
									<ul class="pagination">
										<?php echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
										<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
										<?php echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li')); ?>
									</ul>
									</td>
								</tr>
								<?php } ?>
								<?php	}else{
                                ?>
								<tr>
                                    <td colspan="6" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
                                    <?php
										}
									?>
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
				 
			
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
	id = $(this).attr('rel');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>sitepanel/users/deleteAuth');
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