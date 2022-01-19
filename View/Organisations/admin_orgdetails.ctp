<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?php echo "Account Details: ".$userfullname['UserDetail']['org_name']; ?></h4>
		</div>
		<div class="modal-body">	
			<div class="row">	
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Account Owner:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $orgsettings['User']['email']; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Account Creation:</label>
				<p class="control-label col-md-7 col-xs-6"><?php echo date('d M, Y',$orgsettings['OrgSetting']['created']); ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of Licences:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $orgsettings['OrgSetting']['license']; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of API Licences:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $orgsettings['OrgSetting']['apilicense']; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of Registered Users:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $licencestotal; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Number of API Users:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  echo $apilincentotals; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Licences Renewal Date:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  
				echo date('d M, Y',strtotime($orgsettings['OrgSetting']['end_date'])); ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Allowed Space:</label>
				<p class="control-label col-md-7 col-xs-6"><?php  
				echo $orgsettings['OrgSetting']['allowed_space']." GB"; ?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">Used Space:</label>
				<p class="control-label col-md-7 col-xs-6">
				<?php 
					if( isset($consumedbsizegb) && !empty($consumedbsizegb) ){
						echo $consumedbsizegb." GB / ".$consumedbsizemb." MB"; 
					} else {
						echo "0 GB / 0 MB";
					}
				
				
				?></p>
			</div>
			
			<div class="form-group">
				<label  class="control-label col-md-5 col-xs-6">OpusView Version:</label>
				<p class="control-label col-md-7 col-xs-6">
				<?php  //echo $orgsettings['OrgSetting']['jeera_version']; 
				echo $this->Common->jeeraversion_main(); ?></p>
			</div>
			
		  </div>							
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->