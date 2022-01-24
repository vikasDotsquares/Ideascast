<!-- <div class="modal-dialog orgownerdetail">
	<div class="modal-content">-->
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?php echo $userfullname['UserDetail']['org_name']; ?></h4>
		</div>
		<div class="modal-body">	
			<div class="row">	
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Admin Email:</label>
				<p class="control-label col-md-7 col-xs-6"><?php echo $this->Session->read('Auth.User.email'); ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Account Creation:</label>
				<p class="control-label col-md-7 col-xs-6"><?php echo date('d M, Y',$organisationDetails['OrgSetting']['created']); ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of Licences:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $organisationDetails['OrgSetting']['license']; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of API Licences:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $organisationDetails['OrgSetting']['apilicense']; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of Registered Users:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $licencestotal; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of API Users:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $apilicencestotal; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Licences Renewal Date:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  
				echo date('d M, Y',strtotime($organisationDetails['OrgSetting']['end_date'])); ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Allowed Space:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  
				echo $organisationDetails['OrgSetting']['allowed_space']." GB"; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Used Space:</label>
				<p class="control-label col-md-7 col-xs-6">
				<?php  
				if( isset($consumedbsizegb) && !empty($consumedbsizegb) ){
						echo $consumedbsizegb." GB / ".$consumedbsizemb." MB"; 
					} else {
						echo "0 GB";
					}
				?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">OpusView Version:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  //echo $organisationDetails['OrgSetting']['jeera_version']; 
				echo $this->Common->jeeraversion_main(); 
				?></p>
			</div>
			
		  </div>							
		</div>
	<!-- </div>--><!-- /.modal-content -->
<!-- </div>--><!-- /.modal-dialog -->