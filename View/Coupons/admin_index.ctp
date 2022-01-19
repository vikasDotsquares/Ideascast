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
					<li class="active">Coupons</li>
				</ol>
				<h3> Coupons (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('coupon.per_page_show'); 
			$keyword = $this->Session->read('coupon.keyword'); 
			$status = $this->Session->read('coupon.status');
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('Coupon', array( 'url' => array('controller'=>'coupons', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
						<div class="pull-right padright">
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>							
							<a   href= "<?php echo SITEURL."sitepanel/coupons/add/"; ?>"  class="btn btn-primary">Add Coupon</a>
							
						</div>
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('Coupon', array( 'url' => array('controller'=>'coupons', 'action'=>'index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
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
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/coupons/coupon_resetfilter" >Close</a>
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
									<th><?php echo $this->Paginator->sort('Coupon.name',__("Name"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.percentage',__("Percent"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.on_amount',__("Min Quantity"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.flat',__("Discount"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.start_time',__("Start"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.end_time',__("End"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.status',__("Status"));?></th>
									
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($coupons)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($coupons as $coupon) { //pr($coupon);
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php echo $coupon['Coupon']['name']; ?></td>
									<td><?php if(empty($coupon['Coupon']['percentage'])){ echo "N/A"; }else{ echo $coupon['Coupon']['percentage']; } ?></td>
									<td><?php if(empty($coupon['Coupon']['on_amount'])){ echo "N/A"; }else{ echo $coupon['Coupon']['on_amount']; } ?></td>
									<td><?php if(empty($coupon['Coupon']['flat'])){ echo "N/A"; }else{ echo $coupon['Coupon']['flat']; } ?></td>
									<td><?php echo dateFormat($coupon['Coupon']['start_time'] ); ?></td>
									<td><?php echo  dateFormat($coupon['Coupon']['end_time']) ; ?></td>
									<td>
										<?php
											$clasificationId = $coupon['Coupon']['id'];
											if ($coupon['Coupon']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>

										<?php	}
										?>
									</td> 
									
									<td>
										
										
										<?php 
										$editURL = SITEURL."sitepanel/coupons/edit/".$coupon['Coupon']['id']; 
										?>
										
										<a  title="Edit Coupon" href= "<?php echo $editURL; ?>"  data-tooltip="tooltip" ><i class="fa fa-fw fa-edit"></i></a>

										<!--<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit User" ><i class="fa fa-fw fa-edit"></i></a> -->
																				<?php 
										$coupAsso = SITEURL."sitepanel/coupons/coupon_used/".$coupon['Coupon']['id']; 
										?>
										
										<a  class="editusers" href= "<?php echo $coupAsso; ?>"  title="View Users Transactions" ><i class="fa fa-fw fa-user"></i></a>
										
										
										<?php 
										$deleteURL = SITEURL."sitepanel/coupons/delete/".$coupon['Coupon']['id']; 
										?>
										
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $coupon['Coupon']['id']; ?>"   title="Delete Coupon" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a> 
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['Coupon']['pageCount'] > 1) { ?> 
								<tr>
                                    <td colspan="9" align="right">
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
			
					</div>
				</div>
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
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>sitepanel/coupons/delete');
});


$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/coupons/coupon_updatestatus');
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