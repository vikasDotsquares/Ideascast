<style>
.box-body {
	padding: 10px 0 4px;
}
</style>
<div class="row">
	<div class="col-xs-12">
	<div class="row"></div>
	<!-- Content Header (Post header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box ">
				<div class="box-header">
				
				<ol class="breadcrumb">
					<li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
					<li class="active">Blogs</li>
				</ol>
				<h3>Blogs (<?php echo $count; ?>)</h3>
				</div>
				<?php echo $this->Session->flash(); ?>
				<section class="box-body no-padding">
		<?php 
			$class = 'collapse';
			if(isset($in) && !empty($in)){
				$class = 'in';
			}						
			$per_page_show = $this->Session->read('post.per_page_show'); 
			$keyword = $this->Session->read('post.keyword'); 
			$status = $this->Session->read('post.status');
		?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">
							<small>Per Page Records</small> </h3>
								<div class="margintop">
									<div class="col-lg-1 ">
										<?php echo $this->Form->create('Post', array( 'url' => array('controller'=>'posts', 'action'=>'index'), 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'per_page_show_form' )); ?>
										<?php 
										 
										echo $this->Form->input('per_page_show', array('options'=>unserialize(SHOW_PER_PAGE), 'label' => false, 'selected'=>	$per_page_show, 'div' => false, 'class' => 'form-control perpageshow', 'onchange' => '$("#per_page_show_form").submit();' )); ?>
										</form>
									</div>
								</div>						
								<div class="pull-right padright">
									<a class="btn btn-primary searchbtn" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
										Search
									</a>
									<a href="<?php echo SITEURL."sitepanel/posts/add/"?>" class="btn btn-primary">Add Blog</a>
								</div>
						
						<div class="<?php echo $class; ?> search" id="collapseExample">
							<div class="well">
								<?php echo $this->Form->create('Post', array( 'url' => array('controller'=>'posts', 'action'=>'index'), 'type' => 'POST', 'class' => 'form-horizontal form-bordered', 'id' => 'search_page_show_form')); ?>
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
												<a class="btn btn-primary searchbtn" href="<?php echo SITEURL; ?>sitepanel/posts/post_resetfilter" >Close</a>
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
									<th><?php echo $this->Paginator->sort('Post.title',__("Title"));?></th>
									<th><a href="javascript:"><?php echo __("Image");?></a></th>
									<th><?php echo $this->Paginator->sort('Post.status',__("Status"));?></th>
									<th><?php echo $this->Paginator->sort('Post.created',__("Created"));?></th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($allblog)) {
										$num = $this->Paginator->counter(array('format' => ' %start%'));
										foreach ($allblog as $blog) {
                                ?>
								<tr>
									<td><?php echo $num ?></td>
									<td><?php echo $blog['Post']['title']; ?></td>
									<td>
									<?php if( file_exists(WWW_ROOT.POST_RESIZE_PIC_PATH.$blog['Post']['blog_img']) ) {?>
									<img class="img-responsive" src="<?php echo (isset($blog['Post']['blog_img']) && !empty($blog['Post']['blog_img']))? SITEURL.POST_PIC_PATH.$blog['Post']['blog_img']: POST_RESIZE_SHOW_PATH.'no_image.jpg';?>" style="height:50px;" >
									<?php } else { ?>
									<img height="50" class="img-responsive" src="<?php echo POST_RESIZE_SHOW_PATH.'no_image.jpg';?>" >
									<?php } ?>
									</td>
									<td>
										<?php
											$clasificationId = $blog['Post']['id'];
											if ($blog['Post']['status'] == 1) { ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="deactivate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-check alert-success"></i></button	>
										<?php	} else {
											 ?>
												<button data-toggle="modal" data-target="#StatusBox" rel="activate" id="<?php echo $clasificationId; ?>"  class="btn btn-default RecordUpdateClass"><i class="fa fa-fw fa-times alert-danger"></i></button>
										<?php	}
										?>
									</td>
									<td><?php echo dateFormat($blog['Post']['created']); ?></td>
									<td>
										<?php 
										$viewURL = SITEURL."sitepanel/posts/view/".$blog['Post']['id']; 
										?>
										<a data-toggle="modal" class="viewBlog"  data-target="#blogRecordView" title="View Blog" href="<?php echo $viewURL; ?>" data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip"><i class="fa fa-fw fa-eye"></i></a>
										
										<?php 
										$editURL = SITEURL."sitepanel/posts/edit/".$blog['Post']['id']; 
										?>
										<a class="editpage"  title="Edit Blog" href= "<?php echo $editURL; ?>"  ><i class="fa fa-fw fa-edit"></i></a>
										<?php 
										$deleteURL = SITEURL."sitepanel/posts/delete/".$blog['Post']['id']; 
										?>
										
										<a data-toggle="modal" class="RecordDeleteClass" data-target="#deleteBox" rel="<?php echo $blog['Post']['id']; ?>"   title="Delete Blog" url="<?php echo $deleteURL; ?>"  data-tooltip="tooltip" data-placement="top" ><i class="fa fa-fw fa-trash-o"></i></a>
									</td>
								</tr>								
								<?php
										$num++;
										}//end foreach 
								?>
								<?php 
								
								if($this->params['paging']['Post']['pageCount'] > 1) { ?> 
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

<div class="modal modal-success fade" id="blogRecordView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" ></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal --> 

<script type="text/javascript" >
// Used for Sorting icons on listing pages
$('th a').append(' <i class="fa fa-sort"></i>');
$('th a.asc i').attr('class', 'fa fa-sort-down');
$('th a.desc i').attr('class', 'fa fa-sort-up');	
	
// Delete click Update	
$(document).on('click', '.RecordDeleteClass', function(){
	id = $(this).attr('rel');
	$('#recordDeleteID').val(id);
	$('#RecordDeleteForm').attr('action', '<?php echo SITEURL; ?>sitepanel/posts/delete');
});


// Status click Update
$(document).on('click', '.RecordUpdateClass', function(){
	id = $(this).attr('id');
	rel = $(this).attr('rel');
	$('#RecordStatusFormId').attr('action', '<?php echo SITEURL; ?>sitepanel/posts/post_updatestatus');
	$('#recordID').val(id);
	if(rel == 'activate'){
		$('#recordStatus').val(1);
	}else{
		$('#recordStatus').val(0);
	}
	$('#statusname').text(rel);
});

// Open View Form
$(document).on('click','.viewBlog', function (e) {
  var formURL = $(this).attr('data-whatever') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){	
			if($.trim(response) != 'success'){
				$('#blogRecordView .modal-content').html(response);
			}else{					
				location.reload(); // Saved successfully
			}
		}
	});
})

</script>