
<div class="row">
	<div class="col-xs-12">
	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box ">
				<div class="box-header">
					<?php echo $this->Session->flash(); ?>
					<ol class="breadcrumb">
						<li><a href="<?php echo SITEURL; ?>dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
						<li class="active">Query</li>
					</ol>
					<h3><?php echo $viewData['page_heading']?></h3>
					<p class="text-muted date-time lib" id="">
					  <?php echo $viewData['page_subheading']?>
					 </p>
				</div>
				<section class="box-body no-padding">
					<div class="row" id="Recordlisting">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body table-responsive">
									<?php echo $this->Form->create('OrgSetting', array( 'url' => array('controller'=>'organisations', 'action'=>'query_run'),  'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'User')); ?>
										<div class="form-group padding no-margin">
										  <label for="sqldbquery">Query</label>
										  <textarea class="form-control" id="sqldbquery" placeholder="Enter Sql Query" name="dbquery" style="width:60%; min-height:40%;" ></textarea>
										</div>
										<div class="form-group padding no-margin">
											<div class="col-md-1" style="margin:0; padding:0;">
											<label for="multiquery" style="padding-top: 5px;">MultiQuery</label></div>
											<div class="col-md-11">
											<input type="checkbox" checked="checked" id="multiquery" name="dbmultiquery" ></div>
										</div>
										<div class="form-group padding no-margin">
											<div class="col-md-1" style="margin:0; padding:0;">
											<label for="fileimports" style="padding-top: 5px;">File Import</label></div>
											<div class="col-md-11">
											<input type="checkbox" id="fileimports" name="filebrowse" ></div>
										</div>

										<div class="form-group padding no-margin"  >
										  <label for="sqlfile">File</label>
										  <input type="file" class="form-control" id="sqlfile" placeholder="Enter Sql file" name="dbsqlfile" style="width:60%;" disabled >
										</div>

										<div class="form-group padding no-margin">
											<button type="submit" class="btn btn-success">Submit</button>
										</div>
									</form>
								</div><!-- /.box-body -->
							</div><!-- /.box -->
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
$(function () {
	$('#fileimports').on('ifChecked', function(event){
		$("#sqldbquery").attr('disabled', true);
		$("#multiquery").attr('disabled', true);
		$("#sqlfile").attr('disabled', false);
	});
	$('#fileimports').on('ifUnchecked', function(event){
		$("#sqldbquery").attr('disabled', false);
		$("#multiquery").attr('disabled', false);
		$("#sqlfile").attr('disabled', true);
	});

})
</script>