<?php 
echo $this->Html->css('bootstrap-dialog/bootstrap-dialog.min');
echo $this->Html->script('bootstrap-dialog/bootstrap3.3.5.min', array('inline' => true));
echo $this->Html->script('bootstrap-dialog/bootstrap-dialog.min', array('inline' => true));
?>
<header class="main-header">
           <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
        <!-- Logo -->
        <a href="<?php echo SITEURL?>dashboard" class="logo">
			<span class="logo-text">
				OpusView<sup>TM</sup>
			</span>
		<?php /*<img width="" height="45px" src="<?php echo SITEURL?>images/logo_white.png"    alt=" logo" />*/ ?></a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->

          <div class="navbar-custom-menu" style="display: none;">
            <ul class="nav navbar-nav">
			
				<!-- User Account: style can be found in dropdown.less -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-gear"></i></a>
					<ul class="dropdown-menu">
						<!-- Menu Footer-->
					    <li>
						  <a href="<?php echo SITEURL; ?>sitepanel/settings/edit/1">
							<i class="fa fa-fw  fa-cog"></i>Settings
						</a>
					    </li>	
						<li>
						<?php 
						$editURL = SITEURL.'sitepanel/users/profile'; 
						?>			
							<a href="<?php echo $editURL; ?>" class="editcompany" data-target=".myprofile"  data-toggle="modal"><i class="fa fa-fw fa-user"></i> Profile</a>		

						</li>						
						<li><a href="<?php echo SITEURL.'sitepanel/users/logout';?>"><i class="fa fa-fw fa-power-off"></i> Sign out</a>				
						</li>
					</ul>
				</li> 
				<!--<li><a class="btn btn-sm btn-warning goto-ideascost" href="<?php echo SITEURL?>dashboard"><i class="fa fa-caret-right"></i> Go to Dashboard</a> </li>-->
			</ul>
          </div>
        </nav>
      </header>
	  
	  

<!------ Record Edit Box ------>
<div class="modal fade" id="Recordedit" tabindex="-1" role="dialog"  aria-hidden="true"></div>

<!-- Modal -->
<div class="modal modal-success fade myprofile" id="myprofile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div> <!-- /.modal-content -->
	</div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->


<!-- Modal -->
<div class="modal modal-success fade myprofilen" id="myprofilen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div> <!-- /.modal-content -->
	</div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<!-- Modal -->
<div class="modal modal-success fade" id="modal_notifications" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal --> 



<div class="modal fade" id="Recordview" tabindex="-1" role="dialog" aria-hidden="true"></div>

<!------ Record Delete Alert Message ------>
<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true">
	
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Are you sure, you would like to delete this record?</h4>
			</div>
			<?php echo $this->Form->create('',array('type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordDeleteForm')); ?>
			<input type="hidden" id="recordDeleteID" name='data[id]' />
			<div class="modal-footer clearfix bordertopnone">
				<button type="submit" class="btn btn-success"><!--<i class="fa fa-fw fa-check"></i>--> Delete</button>
				<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
			</div>	
			</form>			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
</div><!-- /.modal -->
<!------ Record Delete Alert Message ------>


<!------ Record Status Update Confirmation Message ------>
<div class="modal fade" id="StatusBox" tabindex="-1" role="dialog" aria-hidden="true">
	
	<div class="modal-dialog">
		<div class="modal-content">
		    <?php echo $this->Form->create('', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordStatusFormId')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Are you sure, you would like to <span id="statusname"></span> this record?</h4>
			</div>
			<input type="hidden" id="recordID" name='data[id]' />
			<input type="hidden" id="recordStatus" name='data[status]' />
			<div class="modal-footer clearfix bordertopnone">
				<button type="submit" class="btn btn-success"><i class="fa fa-fw fa-check"></i> Yes</button>
				<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
			</div>
</form>			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
</div><!-- /.modal -->


<!------ Record Api SDK Status Update Confirmation Message ------>
<div class="modal modal-danger fade" id="SdkStatusBox" tabindex="-1" role="dialog" aria-hidden="true">	
	<div class="modal-dialog">
		<div class="modal-content">
		    <?php echo $this->Form->create('', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' => 'RecordSdkStatusFormId')); ?>
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to <span id="sdkstatusname"></span> the API SDK?</p>
				</div>
				<input type="hidden" id="sdkrecordID" name='data[id]' />
				<input type="hidden" id="sdkrecordStatus" name='data[apisdk_status]' />
				<div class="modal-footer clearfix bordertopnone" style="border-top: 1px solid #666 !important; background-color: #eee !important;">
					<button type="submit" class="btn btn-success">Yes</button>
					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal">No</button>
				</div>
			</form>			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->	
</div><!-- /.modal -->

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
<!------ Record Status Update Confirmation Message ------>



<!------ Record Primary Status Update Confirmation Message ------>
<div class="modal fade" id="StatusBoxPrimary" tabindex="-1" role="dialog" aria-hidden="true">
	
	<div class="modal-dialog">
		<div class="modal-content">
		    <?php echo $this->Form->create('', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'id' =>'RecordStatusPrimaryFormId')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Are you sure you want to change Primary domain?</h4>
			</div>
			<input type="hidden" id="recordID" name='data[id]' />
			<input type="hidden" id="recordStatus" name='data[prmry_sts]' />
			<input type="hidden" id="org_idPPP" value="<?php echo $this->params['params']['0']; ?>" name='data[org_id]' />
			<div class="modal-footer clearfix bordertopnone">
				<button type="submit" class="btn btn-success"><i class="fa fa-fw fa-check"></i> Yes</button>
				<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> No</button>
			</div>
</form>			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
	
</div><!-- /.modal -->
<!------ Record Primary Status Update Confirmation Message ------>

<script type="text/javascript" >

// Submit form to delete records
$(document).on('submit','#RecordDeleteForm',function(e){
	var postData = $(this).serializeArray();
	var formURL = $(this).attr("action");	
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		async:false,
		success:function(response){
			$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
			location.reload();
		}
	});
	e.preventDefault(); //STOP default action
});

// Submit form to update status  
$(document).on('submit','#RecordStatusFormId',function(e){
	var postData = $(this).serializeArray();
	var formURL = $(this).attr("action");
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		async:false,
		success:function(response){	
			$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
			location.reload();
		}
	});
	e.preventDefault(); //STOP default action
});


// Api SDK Submit form to update status  
$(document).on('submit','#RecordSdkStatusFormId',function(e){
	var postData = $(this).serializeArray();
	var formURL = $(this).attr("action");
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		async:false,
		success:function(response){	
			console.log(response);
			$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
			location.reload();
		}
	});
	e.preventDefault(); //STOP default action
});

// Submit form to update status  
$(document).on('submit','#RecordStatusPrimaryFormId',function(e){
console.log("test");
	var postData = $(this).serializeArray();
	var formURL = $(this).attr("action");
	var cid = postData[1].value;
	//console.log(formURL);
	 
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		async:false,
		success:function(response){	
			$( '.modal' ).modal( 'hide' ).data( 'bs.modal', null );
			//$('#example2 .RecordPrimaryStatus').attr('rel','active').find('i').removeClass('alert-success').removeClass('fa-check').addClass('alert-danger').addClass('fa-times');
			//$('#example2 .RecordPrimaryStatus#'+cid).attr('rel','deactive').find('i').removeClass('alert-danger').removeClass('fa-times').addClass('alert-success').addClass('fa-check');
			location.reload();
		}
	});
	e.preventDefault(); //STOP default action
});


// Open Admin Profile Form
$(document).on('click','.editcompany', function (e) {
  var formURL = $(this).attr('href') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){	
			if($.trim(response) != 'success'){
				$('.myprofile').html(response);
			}else{					
				location.reload(); // Saved successfully
			}
		}
	});
	e.preventDefault();
})


$(document).on('click','.editcompanyn', function (e) {
    $('.myprofile').modal('hide');
  var formURL = $(this).attr('href') // Extract info from data-* attributes
  $.ajax({
	url : formURL,
	async:false,
	success:function(response){	
			if($.trim(response) != 'success'){
				$('.myprofilen').html(response);
			}else{					
				location.reload(); // Saved successfully
			}
		}
	});
	e.preventDefault();
})



</script>
<style>.icheckbox_flat-blue{top :6px;}</style>