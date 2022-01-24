<!-- EDIT Industry Classification -->
<?php
 
$params = (isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] > 0) ? $this->request->params['pass'][0] : 0;

 
//$qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView-'.$_SERVER['HTTP_HOST']);
  
?>

<style>
    .enable-two-factor{
        display: inline-block;
        width: 100%;
    }
     .enable-two-factor-left {
        width: 26.6%;
        float: left;
        padding-top: 9px;
    }
    .enable-two-factor-right {
        width: 73.4%;
        float: right;
        padding-left: 15px;
    }
	
	.coder{ font-weight : 600; padding : 10px 0;}
	
	.authentication-popup .modal-header{background:#d9534f!important; border-color: #d9534f;}
	.authentication-popup .modal-title{font-size: 24px;}
	.authentication-popup .modal-body #data{font-size: 13px; font-weight: 600; margin-top:10px;}
	.authentication-popup .modal-body #data p{margin:0 0 2px 0;}
	.authentication-popup .modal-body .form-group label{margin-top:7px;}
	.authentication-popup .error { color: #ff3e3e;  font-size: 12px;  display: block;    clear: both; position: absolute;}
	
    @media (max-width:767px) {
    .enable-two-factor-left {
        width: 100%;
        padding-top: 0;
    }
    .enable-two-factor-right {
        width: 100%;
        padding-left: 0;
    }
    }
    
    
    
</style>
<div class="modal-dialog authentication-popup">
<form method="post" id = "RecordFormedit" action="<?php echo SITEURL; ?>organisations/backup_code">

<?php echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>

		<div class="modal-content ">
			<div class="modal-header" >
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" > Backup Code</h4>
			</div>
			<?php //echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>
			
				<div class="modal-body">
					 
				<?php 
				$membership_code = $this->Session->read('Auth.User.UserDetail.membership_code');
				//if(!isset($membership_code) || empty($membership_code)){
							

				?>		

 				<div class="enable-two-factor">
					 
					 
					 <?php if(isset($flag) && $flag > 1) { ?>
					 <p>Two-factor authentication is now enabled.</p>
					 <?php } else { ?>					 
					 <p>A new backup code has been generated for you.</p>
					 <?php } ?>
					 
					 <p>Store this backup code safely and securely:</p>
					 
					 
					 <p class="coder"><?php echo $backup_code; ?></p>
					 
					 
					 <p>If your device becomes unavailable you can use this one time code to sign in.</p>
					 <p>A new backup code can be generated from the security settings page.</p>

				</div>
					
					
 
				</div>
				<div class="modal-footer clearfix">
 

					<button type="button" id="Discard" class="btn btn-success" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Close</button>
				</div>
</div>
			</form>
			
</div>			
 
