<div class="row">
	<div class="col-xs-12">

	<div class="row">

	<?php 
		// LOAD PARTIAL FILE FOR TOP DD-MENUS
		//echo $this->element('../Projects/partials/project_dd_menus', array('val' => 'testing')); 
		//$this->loadModel('UserProject');
		
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
				<!--	<li><a href="<?php echo SITEURL; ?>sitepanel/users">Users</a></li>-->
					<li class="active">Orders</li>
				</ol>
				<h3>Orders (<?php echo $count; ?>)</h3>
				<div class="pull-right">  <?php foreach($currencyTot as $tot){
						echo "<span style='margin:0 0 0 10px;'>Total Revenue <strong>".$this->Common->currencySignGet($tot['UserTransctionDetail']['mc_currency']).$tot[0]['total']."</strong></span>";
					}?> </div>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}	

			//pr($this->Session->read('coupon')); die;
			$per_page_show = $this->Session->read('usertransctiondetail.per_page_show'); 
			$keyword = $this->Session->read('coupon.search_id');  
			$user = $this->Session->read('coupon.user');
			$plan = $this->Session->read('coupon.plan');
			$author = $this->Session->read('coupon.author');			
			$inst = $this->Session->read('coupon.inst');
			$coupon = $this->Session->read('coupon.coupon');	
			$keyw = $this->Session->read('coupon.keywords');
			$inst = $this->Session->read('coupon.inst');
			$coupon = $this->Session->read('coupon.coupon');	
			$stt = $this->Session->read('usertransctiondetail.start');
			$endd = $this->Session->read('usertransctiondetail.end');	 //die;		
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('User', array( 'url' => array('controller'=>'users', 'action'=>'all_transaction'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false,'name'=>'data[UserTransctionDetail][per_page_show]', 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
					<div class="pull-right padright">
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>							
								<!--<a   href= "<?php echo SITEURL."sitepanel/coupons/add/"; ?>"  class="btn btn-primary">Add Coupon</a>-->
							<a   href= "javascript:window.history.back();"  class="btn btn-primary">Back</a>
						</div> 
						
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('Coupon', array( 'url' => array('controller'=>'users', 'action'=>'all_transaction'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
									<div class="modal-body">
										
										<div class="form-group">
												<div class="col-lg-1">
													<label for="focusedInput" class="control-label">Keyword:</label>
												</div>
												<div class="col-lg-3">
													<?php echo $this->Form->input('keywords', array('placeholder' => 'Enter Keyword here...','type' => 'text','label' => false, 'value'=>$keyw,'div' => false, 'class' => 'form-control')); ?>
												</div>
											
												  <label for="UserUser" class="col-lg-1 control-label">Start:</label>
												  <div class="col-lg-3">
													<?php echo $this->Form->input('UserTransctionDetail.start', array('type' => 'text','value'=>$stt ,'readonly','label' => false, 'div' => false, 'class' => 'form-control from_date')); ?>
												  </div>					  
												

												  <label for="UserUser" class="col-lg-1 control-label">End:</label>
												  <div class="col-lg-3">
													<?php echo $this->Form->input('UserTransctionDetail.end', array('type' => 'text', 'value'=>$endd ,'readonly','label' => false, 'div' => false, 'class' => 'form-control to_date')); ?>
												  </div>
												  
												  </div> 
												  
												<div class="form-group">  
												  <label for="UserUser" class="col-lg-1 control-label">Search by:</label>
												  <div class="col-lg-3" id="sby">
													<?php 
													 echo $this->Form->input('search_id', array('class' => 'form-control', 'options' => $searchOptions,'empty'=>'Please select','selected'=>$keyword, 'label' => false, 'div' => array('class' => 'formRight noSearch'))); ?>
												  </div>

												  <label for="UserUser" class="col-lg-1 control-label">&nbsp;</label>		
												<div class="col-lg-3" id="u" style="display:none">
													<?php 
													 echo $this->Form->input('user', array('options' => $userr,'empty'=>'All Users','class' => 'form-control','selected'=>$user,  'label' => false, 'div' => array('class' => 'formRight noSearch'))); 
													 ?>
												  </div>
												  
												
												<div class="col-lg-3" id="i" style="display:none">
													<?php 
													 echo $this->Form->input('inst', array('options' => $instt,'empty'=>'All Insitution','class' => 'form-control','selected'=>$inst,  'label' => false, 'div' => array('class' => 'formRight noSearch'))); 
													 ?>
												  </div> 

												  
												<div class="col-lg-3" id="c" style="display:none">
													<?php 
													 echo $this->Form->input('coupon', array('options' => $couponn,'empty'=>'All Coupons','selected'=>$coupon,  'class' => 'form-control', 'label' => false, 'div' => array('class' => 'formRight noSearch'))); 
													 ?>
												</div> 
												  
												<div class="col-lg-3" id="p" style="display:none">
													<?php 
													 echo $this->Form->input('plan', array('options' => $plann,'empty'=>'All Plans','selected'=>$plan, 'class' => 'form-control',  'label' => false, 'div' => array('class' => 'formRight noSearch'))); 
													 ?>
												</div> 

												<div class="col-lg-3" id="a" style="display:none">
													<?php 
													 echo $this->Form->input('author', array('options' => $authorr,'empty'=>'All Authors','selected'=>$author, 'class' => 'form-control',  'label' => false, 'div' => array('class' => 'formRight noSearch'))); 
													 ?>
												</div>		
												  
												  
											<div style="text-align:right;margin:20px 0 0 0" class="col-lg-12">
												<button type="submit" class="searchbtn btn btn-success">Go</button>
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/users/trans_all_resetfilter" >Close</a>
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
									<th><?php echo $this->Paginator->sort('UserTransctionDetail.txn_id',__("Transaction ID"));?></th>
									<th><?php echo $this->Paginator->sort('User.UserDetail.first_name',__("User Name"));?>
									</th>
									<th><?php echo $this->Paginator->sort('UserTransctionDetail.institution_id',__("Institution"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.name',__("Coupon"));?></th>						
									<th><?php echo $this->Paginator->sort('UserTransctionDetail.amount',__("Paid"));?></th>
									<th><?php echo $this->Paginator->sort('UserTransctionDetail.payment_date',__("Date"));?></th>
									
									
									<!-- <th><?php echo $this->Paginator->sort('Coupon.percentage',__("Percentage"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.on_amount',__("On Minimum Amount"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.flat',__("Flat Discount"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.start_time',__("Start"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.end_time',__("End"));?></th>
									<th><?php echo $this->Paginator->sort('Coupon.status',__("Status"));?></th> -->
									
									<!--<th>Actions</th> -->
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($coupons)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										
										
										foreach ($coupons as $coupon) {   //pr($coupon);
										// pr($coupon); die;
										//echo $coupon['UserDetail']['institution_id'];
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									
									<td>
									<a title="" href="javascript:void(0)" data-target="#Recordeditss" class="editusers" data-original-title="View Invoice"><?php echo $coupon['UserTransctionDetail']['txn_id']; ?></a>									
									</td>
									
									<td>
									<?php $viewURL = SITEURL."sitepanel/users/view/".$coupon['User']['id'];  ?>
									<a data-toggle="modal" class="viewuser"  data-target="#Recordview"   title="View User" data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-placement="top" ><?php 										
									//echo $coupon['UserDetail']['first_name']." ".$coupon['UserDetail']['last_name']; 
									$userD = $this->Common->userDetail($coupon['User']['id']);  
									echo $userD['UserDetail']['first_name']." ".$userD['UserDetail']['last_name'];
									?></a></td>
									
									<td><?php if(empty($coupon['UserDetail']['institution_id'])){ echo "N/A"; }else{ $det = $this->Common->userDetail($coupon['UserDetail']['institution_id']);  
									echo $det['UserDetail']['first_name'];
									} ?></td>
									<td><?php if(empty($coupon['Coupon']['name'])){ echo "N/A"; }else{ echo $coupon['Coupon']['name']; } ?></td>
									<td><?php echo $this->Common->currencySignGet($coupon['UserTransctionDetail']['mc_currency']).$coupon['UserTransctionDetail']['amount']; ?></td>
									<td><?php  echo $coupon['UserTransctionDetail']['payment_date'] = dateFormat(date("m/d/Y h:i:s", $coupon['UserTransctionDetail']['payment_date']));  ?></td>
									
									<!--<td><?php if(empty($coupon['Coupon']['percentage'])){ echo "N/A"; }else{ echo $coupon['Coupon']['percentage']; } ?></td>
									<td><?php if(empty($coupon['Coupon']['on_amount'])){ echo "N/A"; }else{ echo $coupon['Coupon']['on_amount']; } ?></td>
									<td><?php if(empty($coupon['Coupon']['flat'])){ echo "N/A"; }else{ echo $coupon['Coupon']['flat']; } ?></td>
									<td><?php echo $coupon['Coupon']['start_time'] = date("m/d/Y", strtotime($coupon['Coupon']['start_time'])); ?></td>
									<td><?php echo  $coupon['Coupon']['end_time'] = date("m/d/Y", strtotime($coupon['Coupon']['end_time'])); ?></td> -->
									
									

								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['UserTransctionDetail']['pageCount'] > 1) { ?> 
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

$(document).ready(function(){

$('#u,#i,#c,#p,#a').hide();

$('#CouponSearchId').change(function(){
var va = $(this).val(); 
if(va==1){
$('#u').show();	$('#i,#c,#p,#a').hide();
}else if(va==2){
$('#i').show();	$('#u,#c,#p,#a').hide();	
}else if(va==3){
$('#c').show();	$('#u,#i,#p,#a').hide();
}else if(va==4){
$('#p').show();	$('#u,#i,#c,#a').hide();
}else if(va==5){
$('#a').show();	$('#u,#i,#c,#p').hide();
}else if(va==''){
$('#u,#i,#c,#p,#a').hide();
}
})


var CouponSearchId = $('#CouponSearchId').val();
if(CouponSearchId==1){
$('#u').show();	$('#i,#c,#p,#a').hide();
}else if(CouponSearchId==2){
$('#i').show();	$('#u,#c,#p,#a').hide();	
}else if(CouponSearchId==3){
$('#c').show();	$('#u,#i,#p,#a').hide();
}else if(CouponSearchId==4){
$('#p').show();	$('#u,#i,#c,#a').hide();
}else if(CouponSearchId==5){
$('#a').show();	$('#u,#i,#c,#p').hide();
}else if(CouponSearchId==''){
$('#u,#i,#c,#p,#a').hide();
}
	
var startDate = new Date();
var FromEndDate = new Date();
var ToEndDate = new Date();

ToEndDate.setDate(ToEndDate.getDate()+365);

	$('.from_date').datepicker({
	weekStart: 1,
	endDate: FromEndDate, 
	autoclose: true
	})
	.on('changeDate', function(selected){
	startDate = new Date(selected.date.valueOf());
	startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
	$('.to_date').datepicker('setStartDate', startDate);
	}); 
	
	$('.to_date').datepicker({
        
        weekStart: 1,
        endDate: ToEndDate,
        autoclose: true
    })
    .on('changeDate', function(selected){
        FromEndDate = new Date(selected.date.valueOf());
        FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
        $('.from_date').datepicker('setEndDate', FromEndDate);
    });
})


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