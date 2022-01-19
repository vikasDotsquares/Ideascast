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
					<li class="active">Plans</li>
				</ol>
				<h3> Plans (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('plan.per_page_show'); 
			$keyword = $this->Session->read('plan.keyword'); 
			$status = $this->Session->read('plan.status');
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('Plan', array( 'url' => array('controller'=>'plans', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
						<div class="pull-right padright">
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>							
							<!--<button  data-toggle="modal" data-whatever= "<?php echo SITEURL."sitepanel/plans/add/"; ?>" data-target="#RecordAdd" class="btn btn-primary">Add Plan</button> -->
							
							<a class="btn btn-primary" href="<?php echo SITEURL; ?>sitepanel/plans/add/"> Add Plan</a>
							
						</div>
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('Plan', array( 'url' => array('controller'=>'plans', 'action'=>'index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
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
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/plans/plan_resetfilter" >Close</a>
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
									<th><?php echo $this->Paginator->sort('Plan.title',__("Title"));?></th>
									<th><?php echo $this->Paginator->sort('Plan.description',__("Description"));?></th>
									<th><?php echo $this->Paginator->sort('Plan.plantype_monthly',__("Monthly"));?></th>
									<th><?php echo $this->Paginator->sort('Plan.plantype_yearly',__("Yearly"));?></th>
									<!--<th><?php echo $this->Paginator->sort('Plan.plantype_once',__("Once"));?></th>-->
									
									<th><?php echo $this->Paginator->sort('Plan.status',__("Status"));?></th>
									<th><?php echo $this->Paginator->sort('Plan.created',__("Created"));?></th>									
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($plans)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($plans as $plan) {
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php echo $plan['Plan']['title']; ?></td>
									<td class="col-sm-4"><?php echo $plan['Plan']['description']; ?></td>

									<td><?php echo $plan['Plan']['plantype_monthly']; ?></td>
									<td><?php echo $plan['Plan']['plantype_yearly']; ?></td>
								<!--	<td><?php echo $plan['Plan']['plantype_once']; ?></td>-->
									
									<td>
										<?php
											$clasificationId = $plan['Plan']['id'];
											if ($plan['Plan']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>

										<?php	}
										?>
									</td> 
									<td><?php echo dateFormat($plan['Plan']['created']); ?></td>
									<td>
										
										
										<?php 
										$editURL = SITEURL."sitepanel/plans/edit/".$plan['Plan']['id']; 
										?>
										
										<!--<a data-toggle="modal" class="edituser" data-target="#Recordedit"   title="Edit Plan" data-whatever= "<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-edit"></i></a>-->

										<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit Plan" ><i class="fa fa-fw fa-edit"></i></a> 
										
										<?php 
										$planAsso = SITEURL."sitepanel/users/planuser/".$plan['Plan']['id']; 
										?>
										
										<a  class="editusers" href= "<?php echo $planAsso; ?>"  title="Plan Users" ><i class="fa fa-fw fa-user"></i></a>
										
										<?php 
										$deleteURL = SITEURL."sitepanel/plans/delete/".$plan['Plan']['id']; 
										?>
										
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $plan['Plan']['id']; ?>"   title="Delete Plan" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a> 
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['Plan']['pageCount'] > 1) { ?> 
								<tr>
                                    <td colspan="5" align="right">
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
				
				<!------ Add New Classification ------>
				<div class="modal fade" id="RecordAdd" tabindex="-1" role="dialog" aria-hidden="true">
					<?php echo include('admin_add.ctp'); ?>					
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
	id = $(this).attr('rel');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>sitepanel/plans/delete');
});




$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/plans/plan_updatestatus');
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