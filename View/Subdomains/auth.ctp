<?php

App::import('Vendor', 'googleAuth/GoogleAuthenticator');

$ga = new GoogleAuthenticator();

$email = $this->Session->read('Auth.User.email');
//$secret = $ga->createSecret();


 
 
if( !isset($_SESSION['secrets'] ) || empty($_SESSION['secrets'] )){
				
	 $secret = $ga->createSecret();
	 $_SESSION['secrets'] = $secret;

}else{
	
	    $secret= $_SESSION['secrets'];
}

  
//$_SESSION['secret'] = $secret;
//echo $secret; 

$qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView');

//$qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView-'.$_SERVER['HTTP_HOST']);

//$qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $secret,'OpusView');

?>

<?php
$ud =  get_theme_data();
$menuprofile =  $ud['UserDetail']['menu_pic'];
$menuprofile_color =  $ud['UserDetail']['theme_color'];


if(isset($menuprofile_color) && !empty($menuprofile_color)){ ?>

<style>
.login-card-outer .login-card{
	    border-bottom: solid 3px <?php echo $menuprofile_color; ?>;
}
.btn-custom{
	
    background-color: <?php echo $menuprofile_color; ?> !important;
}
</style>
	
<?php
}

$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)){
	$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
	}else{
	$menuprofiles = SITEURL.'images/SignInOpusViewLogo.png';
}
?>


<?php echo $this->Session->flash(); ?>


<div class="login-card-outer">

<?php if(isset($error) && !empty($error)){ ?>
	<div class="login-card-error">
		<span class="icon"><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></span>
		<span class="text">Invalid two-factor code.</span>
	</div>
<?php } ?>	
	
<div id="container" class="login-card">
 
<div id='device' >
 
<?php 
$membership_code = $this->Session->read('Auth.User.UserDetail.membership_code');
 if(!isset($membership_code) || empty($membership_code) ){
			
//$auther = two_factor_check();
//if(isset($auther ) && !empty($auther )){
?>		
 

<div class="scan-code">
	<form method="post" id="scan-register" action="<?php echo SITEURL; ?>subdomains/auth">
        <h3>Two-Factor Authentication</h3>
	<div class="form-group">	 
		 <div class="scan-code-wrap">	 
			 <div class="scan-code-image">
				 <div id="img">
				<img src='<?php echo $qrCodeUrl; ?>' />
				</div>
			</div>

			<?php //} ?>
			<div class="scan-code-text">
					<p class="we-will-text">Scan this code using your authenticator application, then enter code to register.</p>	
					<div class="we-will-text form-group">Enter verification code:</div>
					<input type="text" name="code" id="codeGen"  maxlength="20"   class="form-control" placeholder="Code" />

			</div>	
	</div> 
</div>		 
	 
	<div class="form-group">
		 <div class="row">
					<div class="col-md-12">
						 <input type="hidden" name="secret"  value="<?php echo $secret; ?>" />
						 <input type="hidden" name="type"  value="scan" />
						<input type="submit" value="Register" class="btn-custom"/>
					 </div>
		 </div>
	</div> 	 
	 
	 <div class="authentication-bottom-link">
         <a href="#" class="text-button manual">Enter manually</a>
        <a href="<?php echo SITEURL."users/logout" ?>" class="text-button return-signin">Return to Sign In</a>
        </div>
	</form>		
</div>
			
<div class="manually-code"  style="display:none;">
	<form method="post" id="manual-register" action="<?php echo SITEURL; ?>subdomains/auth">
        <h3>Two-Factor Authentication</h3>
	<div class="form-group we-will-text">	 
		 <div class="row">
			 <div class="col-md-12 verification-code-text">
				 <p>Account: <?php echo $email; ?> <br>
				 Key: <?php echo $secret; ?><br>
				 <!--<p>Issuer Name: OpusView-<?php echo $_SERVER['HTTP_HOST']; ?></p>-->
				 Type: Time Based</p>
			</div>

	 </div>
</div>		 

 <div class="form-group we-will-text" style="margin-bottom: 10.5px;">
			Enter verification code:
			
	</div> 
	<div class="form-group">			
			<input type="text" name="code"  maxlength="20"  id="codes" class="form-control code-input" placeholder="Code" />
	</div> 
		<div class="form-group">
		 		<input type="hidden" name="secret"  value="<?php echo $secret; ?>" />
				<input type="hidden" name="type"  value="manual" />
				
		</div>
	 <div class="form-group btn-signin-wrap">
		 		
				<input type="submit" value="Register" class="btn-custom"/>
		</div>
	 <div class="authentication-bottom-link">
         <a href="#" class="text-button scan">Scan code</a>
         <a href="<?php echo SITEURL."users/logout" ?>" class="text-button return-signin">Return to Sign In</a>
     </div>
	</form>		
</div>

<script>
$(function(){
	
	setTimeout(function(){
		$('#codeGen').focus();	
	},150)	
})
</script>	

<?php }
 
 ?>				
	
<?php if(isset($membership_code) && !empty($membership_code)){ ?>	
	<div class="authentication-code">
	<div class="login-card-logo">
	<!--<a href="https://www.opusview.com" target="_blank">-->
			<img src="<?php echo $menuprofiles; ?>" alt="">
			<!--</a>-->
	</div>
	<form method="post" id="scan-code" action="<?php echo SITEURL; ?>subdomains/auth">
        <div class="form-group we-will-text">
				<p>Enter your two-factor authentication code:</p>
			</div>
        
		<div class="form-group">			
			<!--<input type="text" name="code" id="codee" maxlength="20" class="form-control" placeholder="Code" />-->
			<input type="text" name="code" id="codee" maxlength="50" class="form-control" placeholder="Code" />
			<input type="hidden" name="secret"  value="<?php echo $secret; ?>" />
		</div>
		<div class="form-group  btn-signin-wrap">
			<input type="submit" class="btn-custom" value="Verify">
		</div>
		 <div class="authentication-bottom-link">
			  
			 <a href="<?php echo SITEURL."users/logout" ?>" class="text-button return-signin pull-right">Return to Sign In</a>
		 </div>
	</form>
	</div>	
	
<script>
$(function(){
	
	setTimeout(function(){
		$('#codee').focus();	
	},200)	
})
</script>	
<?php } ?>	
	
</div>

<?php 

//pr($_REQUEST);
 $secrets  = "";
//$secret = $_REQUEST['secret'];

/* if(isset($_REQUEST) && !empty($_REQUEST)){
if( !isset($_SESSION['secrets'] ) || empty($_SESSION['secrets'] )){
	
$_SESSION['secrets'] = $_REQUEST['secret'];


}

if( isset($_SESSION['secrets'] ) && !empty($_SESSION['secrets'] )){
$secrets = $_SESSION['secrets'];
}
pr($secrets);
pr($_SESSION['secrets']);

$code = $_REQUEST['code'];

$checkResult = $ga->verifyCode($secrets, $code,2);    // 2 = 2*30sec clock tolerance
 
if ($checkResult) 
{
echo 'success';
echo $userData['landing_url'];

} 
else 
{
echo 'FAILED';
}

} */
 
?>
 
</div>
	
	<div class="login-card-footer">		
			<ul class="login-card-footer-menu">
				<li><a target="_blank" href="https://www.opusview.com/privacy">Privacy Policy</a></li>
				<li><a target="_blank" href="https://www.opusview.com/terms">Terms of Service</a></li>
			</ul>
		
		<div class="login-card-footer-powered">
			<span>Powered by</span>
			<a href="https://www.opusview.com" target="_blank"><img src="<?php echo SITEURL; ?>images/SignInPoweredbyLogo.png" alt=""></a>
		</div>
	</div>
	</div>	
<script>
$(function(){
	
	
	jQuery("#scan-code").validate({
		rules: {
			'code': "required",			  
			
		},
			messages: {
			'code': {
				required: "Code is required",
				//email: "Include a username, an @ symbol, and a domain",
				 
			},				
		 
		} 
		 
	});
	
	jQuery("#manual-register").validate({
		rules: {
			'code': "required",			  
			
		},
			messages: {
			'code': {
				required: "Code is required",
				//email: "Include a username, an @ symbol, and a domain",
				 
			},				
		 
		} 
		 
	});

	jQuery("#scan-register").validate({
		rules: {
			'code': "required",			  
			
		},
			messages: {
			'code': {
				required: "Code is required",
				//email: "Include a username, an @ symbol, and a domain",
				 
			},				
		 
		} 
		 
	});	
	
	$('.scan').click(function(){
		$('.manually-code').hide();
		$('.scan-code').show();
			setTimeout(function(){
				$('#codeGen').focus();	
			},150)
	})
	$('.manual').click(function(){
		$('.scan-code').hide();
		$('.manually-code').show();
			setTimeout(function(){
				$('#codes').focus();	
			},150)	
	})	
	
	<?php if(isset($type) && !empty($type)){
	 if($type=='scan'){
	?>
		$('.scan').trigger('click');
	<?php }else{?>
		$('.manual').trigger('click');
	<?php }}?>
	
})
</script>

<style>
#codeGen-error,#codes-error,#codee-error {
    color: #ff3e3e;
    font-size: 12px;
    display: block;
    clear: both;
    position: absolute;
}
</style>	