<!-- EDIT Industry Classification -->
<?php
App::import('Vendor', 'googleAuth/GoogleAuthenticator');

$ga = new GoogleAuthenticator();

$params = (isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] > 0) ? $this->request->params['pass'][0] : 0;

$email = $this->Session->read('Auth.User.email');
//$secret = $ga->createSecret();

unset($_SESSION['secrets']);
 
if( (!isset($_SESSION['secrets'] ) || empty($_SESSION['secrets'] ))  || $params > 0 ){
	
	
				
	 $secret = $ga->createSecret();
	 $_SESSION['secrets'] = $secret;
	 
	   $qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView');
	 


}else{
	
	    $secret= $_SESSION['secrets'];
		$qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView');
}

//pr($_SESSION); 


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
<form method="post" id = "RecordFormedit" action="<?php echo SITEURL; ?>organisations/authentication/<?php echo $params; ?>">

<?php echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>

		<div class="modal-content ">
			<div class="modal-header" >
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" > Two-Factor Authentication</h4>
			</div>
			<?php //echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>
			
				<div class="modal-body">
					 
				<?php 
				$membership_code = $this->Session->read('Auth.User.UserDetail.membership_code');
				//if(!isset($membership_code) || empty($membership_code)){
							

				?>		

 				<div class="enable-two-factor">
					<div class="enable-two-factor-left">
						<div id="img">
						<img src='<?php echo $qrCodeUrl; ?>' />
						</div>
					</div>
					
					<div class="enable-two-factor-right">
					<div id="data">
					<p>Account: <?php echo $email; ?></p>
					<p>Key: <?php echo $secret; ?></p>
					<p>Issuer: OpusView <?php //echo $_SERVER['HTTP_HOST']; ?></p>
					<p>Type: Time Based</p>
					</div>


					<?php //} ?>

					<div id="codeinput">
						<div class="form-group">
							<label>Enter Verification Code:</label>
							<input type="text" maxlength="20" name="code" id="code" class="form-control" placeholder="Code" />
							<div id="code_error" class="error "></div>
							<input type="hidden" name="secret"  value="<?php echo $secret; ?>" />
						</div>	
					</div>
					
					</div>

					</div>
					
					
 
				</div>
				<div class="modal-footer clearfix">

					<button type="submit" class="btn btn-success">
					<?php if($params > 0){
						echo "Verify";
					}else{
						echo "Enable";
					} ?>
					 
					</button>

					<button type="button" id="Discard" class="btn btn-danger" data-dismiss="modal"><!--<i class="fa fa-times"></i>--> Cancel</button>
				</div>
</div>
			</form>
			
</div>			
<script type="text/javascript" >

$(function(){
	
	setTimeout(function(){
		$('#code').focus();	
	},150)	
 
$("#RecordFormedit").submit(function(e){
	//var postData = $(this).serializeArray();
	var postData = new FormData($(this)[0]);
 
	var formURL = $(this).attr("action");
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		global : false,
		dataType: 'JSON',
		success:function(response){
			console.log(response);
			console.log(response.success);
			console.log(response.error);
			setTimeout(function(){
				$('#code_error').html(response.error);
			},200)
			
			if(response.success != true){
				$('#code_error').html(response.error);
			}else{
				 $.cookie('backup_code', 1, {path: '/'});

				 location.reload();
				 //window.location.href = window.location.href + "/1";
			}
		},
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){

		}
	});
	e.preventDefault();

});

})

</script>
 
