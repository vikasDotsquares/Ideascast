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
					<li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Summary</a></li>
					<li class="active">Settings</li>
				</ol>
				<h3> Settings (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('setting.per_page_show'); 
			$keyword = $this->Session->read('setting.keyword'); 
			$status = $this->Session->read('setting.status');
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header" style="display:none">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('Plan', array( 'url' => array('controller'=>'settings', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
						<!--<div class="pull-right padright" >
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>							
							
							
							<a class="btn btn-primary" href="<?php echo SITEURL; ?>sitepanel/plans/add/"> Add Plan</a>
							
						</div>-->
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('Setting', array( 'url' => array('controller'=>'plans', 'action'=>'index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
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
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/setting/setting_resetfilter" >Close</a>
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
									<th >#</th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.fb',__("Facebook"));?></th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.twiter',__("Twitter"));?></th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.linkedin',__("LinkedIn"));?></th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.youtube',__("Youtube"));?></th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.phone',__("Phone"));?></th>
									
									<th class="col-sm-2"><?php echo $this->Paginator->sort('Setting.cphone',__("Compnay phone"));?></th>
									<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.email',__("Email"));?></th>
									<!--<th class="col-sm-1"><?php echo $this->Paginator->sort('Setting.address',__("Company Address"));?></th>	-->								
									<th >Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($settings)) { //pr($settings);
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($settings as $plan) {
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php echo $plan['Setting']['fb']; ?></td>
									<td class=""><?php echo $plan['Setting']['twitter']; ?></td>

									<td><?php echo $plan['Setting']['linkedin']; ?></td>
									<td><?php echo $plan['Setting']['youtube']; ?></td>
									<td><?php echo $plan['Setting']['phone']; ?></td>
									

									<td><?php echo $plan['Setting']['cphone']; ?></td>									
									<td><?php echo $plan['Setting']['email']; ?></td>						
									<!--<td><?php echo $plan['Setting']['address']; ?></td>-->
									<td>
										
										
										<?php 
										$editURL = SITEURL."sitepanel/settings/edit/".$plan['Setting']['id']; 
										?>
										
										<!--<a data-toggle="modal" class="edituser" data-target="#Recordedit"   title="Edit Plan" data-whatever= "<?php echo $editURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-edit"></i></a>-->

										<a  class="editusers" data-target="#Recordeditss" href= "<?php echo $editURL; ?>"  title="Edit Settings" ><i class="fa fa-fw fa-edit"></i></a> 
										
										
										
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['Setting']['pageCount'] > 1) { ?> 
								<tr>
                                    <td colspan="5" align="right">
										<ul class="pagination">
											<?php 
												echo $this->Paginator->prev('« Previous', array('class' => 'prev'), null, array('class' => 'disabled', 'tag'=>'li')); 
											?>
											<?php echo $this->Paginator->numbers(array('currentClass' => 'avtive', 'Class' => '', 'tag'=>'li', 'separator'=>'')); ?>
											<?php 
												echo $this->Paginator->next('Next »',  array('class' => 'next'), null, array('class' => 'disabled', 'tag'=>'li'));
											?>
										</ul>
									</td>
								</tr>
								<?php } ?>
								<?php	}else{
                                ?>
								<tr>
                                    <td colspan="6" style="color:RED;text-align: center;">No Records Found!</td>
								</tr>
                                <?php } ?>
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