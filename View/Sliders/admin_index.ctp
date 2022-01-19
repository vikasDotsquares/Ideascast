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
					<li class="active">Sliders</li>
				</ol>
				<h3>Sliders (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('homeslider.per_page_show'); 
			$keyword = $this->Session->read('homeslider.keyword'); 
			$status = $this->Session->read('homeslider.status');
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Slider Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('HomeSlider', array( 'url' => array('controller'=>'sliders', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>
						
						
						<div class="pull-right padright">
							<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								Search
							</a>							
							<!--<button  data-toggle="modal" data-target="#RecordAdd" class="btn btn-primary">Add Page</button> -->
							<a  href="<?php echo SITEURL."sitepanel/sliders/add/"?>" class="btn btn-primary">Add Slider</a>
							
						</div>
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('HomeSlider', array( 'url' => array('controller'=>'sliders', 'action'=>'index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
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
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/sliders/slider_resetfilter" >Close</a>
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
									<th><?php echo $this->Paginator->sort('HomeSlider.slider_title',__("Name"));?></th>
									<th><?php echo $this->Paginator->sort('HomeSlider.slider_image',__("Thumb"));?></th>
									<th><?php echo $this->Paginator->sort('HomeSlider.status',__("Status"));?></th>
									<th><?php echo $this->Paginator->sort('HomeSlider.created',__("Created"));?></th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($sliders)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($sliders as $slider) {
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php echo $slider['HomeSlider']['slider_title']; ?></td>
									<td><?php $folder_url = SITEURL . HOME_SLIDER_PATH.$slider['HomeSlider']['slider_image']; ?>
										<img src="<?php echo $folder_url;?>" width="70" >
									</td>
									<td>
										<?php
											$clasificationId = $slider['HomeSlider']['id'];
											if ($slider['HomeSlider']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php } ?>
									</td>
									<td><?php echo dateFormat($slider['HomeSlider']['created']); ?></td>
									<td>
										<?php /*
										$viewURL = SITEURL."sitepanel/sliders/view/".$slider['HomeSlider']['id']; 
										?>
										<a  title="View Slider" href="<?php echo $viewURL; ?>"  data-tooltip="tooltip"><i class="fa fa-fw fa-eye"></i></a> */ ?>
										
										<?php 
										$editURL = SITEURL."sitepanel/sliders/edit/".$slider['HomeSlider']['id']; 
										?>
										<a class="editslider"  title="Edit Slider" href= "<?php echo $editURL; ?>"  ><i class="fa fa-fw fa-edit"></i></a>
										<?php 
											$deleteURL = SITEURL."sitepanel/sliders/delete/".$slider['HomeSlider']['id']; 
										?>
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $slider['HomeSlider']['id']; ?>"   title="Delete slider" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['HomeSlider']['pageCount'] > 1) { ?> 
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
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>sitepanel/sliders/delete');
});


// Status click Update
$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/sliders/slider_updatestatus');
	$('#recordID').val(id);
	if(rel == 'activate'){
		$('#recordStatus').val(1);
	}else{
		$('#recordStatus').val(0);
	}
	$('#statusname').text(rel);
});






// Open Edit Form

/* $(document).on('click','.editpage', function (e) {
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
}) */

// Open View Form

/* $(document).on('click','.viewpage', function (e) {
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
}) */



</script>