<style>
.error-content{
	padding-top: 35px;
	padding-left: 11px;
	font-size: 16px;
	color: #444;
}
.error-page-cont .left {
    float: left;
}
.error-page-cont .right {
    overflow: hidden;
	padding-top: 20px;
	font-size: 16px;
	line-height: normal;
	padding-left:15px;
}
.error-page-cont .right p{
	margin-bottom:5px;
	color: #444;
	text-align: left;
}

.error-page-cont p{
	margin-bottom:5px;
	color: #444;
	text-align: left;
}
.error-page-cont h3{
	    font-size: 50px;
    font-weight: 500;
}

@media (max-width:991px){
.error-page-cont .left {   
    width: 200px;
}
.error-page-cont h3{
	font-size: 40px;
}
.error-page-cont .right {
	    font-size: 22px;
		
}
}
@media (max-width:767px){
.error-page-cont .left {   
    width: 170px;
}
.error-page-cont h3{
	font-size: 30px;
}
.error-page-cont .right {
	    font-size: 17px;
		
}
}
@media (max-width:540px){
.error-page-cont .left {   
   width: 170px;
	float:none;	
}
.error-page-cont .right{
	padding-top:5px;
	padding-left:0px;
}
@media (max-width:479px){
.error-page-cont h3 {
    font-size: 21px;
}
.error-page-cont .right {
    font-size: 16px;
}
}


</style>
<?php
if(isset($_SESSION['LoginDetails'])){
	$this->request->data = $_SESSION['LoginDetails'];
}

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
<?php /* ?>
<section class="content"> 
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="error-page-cont">            
					<div class="error-content">
							<div class="left">
								<img src="<?php echo SITEURL?>images/exclaim.png">
							</div>
							<div class="right">
								<?php 
								$message = 'Page not found.';
								if($this->params->controller == 'error2'){
									//$message = "Oops! This domain will not be accessible. Domain's start date has not reached";
									$message = "Awaiting service start date.";
								}else if($this->params->controller == 'error3') {
									$message = "Service end date passed.";
								}else if($this->params->controller == 'error4') {
									$message = "Domain disabled.";
								}else if($this->params->controller == 'error1') {
									//$message = "Oops! domain doesn't exist";
									$message = "Domain being prepared.";
								}
								else if($this->params->controller == 'error5') {
									//$message = "Oops! domain's disk space quota limit is exceeded";
									$message = "Storage quota exceeded.";
								}
								?>
								<h3><?php echo 'Oops - There is a problem.'; ?></h3>
								<p>Please contact to your administrator</p>
								<p>REF: <?php echo $message;?></p>
								<p>We could not find the page you were looking for.</p>
						  </div>
						  <!--<p>
							We could not find the page you were looking for.
							Meanwhile, you may <a href="<?php echo !empty(SUBSITEURL)? SUBSITEURL : 'http://www.ideascast.com/'; ?>">return to website</a>.
						  </p>-->              
					</div><!-- /.error-content -->
				</div><!-- /.error-page -->
			</div>
		</div> 
	</div>
</section> <?php */ ?>
       

<?php 
	$message = 'Page not found.';
	if($this->params->controller == 'error2'){
		//$message = "Oops! This domain will not be accessible. Domain's start date has not reached";
		$message = "Awaiting service start date.";
	}else if($this->params->controller == 'error3') {
		$message = "Service end date passed.";
	}else if($this->params->controller == 'error4') {
		$message = "Domain disabled.";
	}else if($this->params->controller == 'error1') {
		//$message = "Oops! domain doesn't exist";
		$message = "Domain being prepared.";
	}
	else if($this->params->controller == 'error5') {
		//$message = "Oops! domain's disk space quota limit is exceeded";
		$message = "Storage quota exceeded.";
	}
	?>
<div class=" clearfix">

<div class="login-card-outer">
	<?php
	 $err = $this->Session->read('Message.flash');

	if(isset($err) && !empty($err)){?>
	<div class="login-card-error">
		<span class="icon">
			<?php if(isset($err[0]['element']) && $err[0]['element'] =='success'){ ?>
			<img src="<?php echo SITEURL; ?>images/info-icon.png" alt="">
			<?php } else{ ?>
			<img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt="">
			<?php } ?>
		</span>
		<span class="text"><?php //echo $loginerror; ?><?php  echo $this->Session->flash();?></span>
	</div>
	<?php } ?>



	<div  class="login-card">
		<div class="login-card-logo">
			<!--<a href="https://www.opusview.com" target="_blank">-->
			<img src="<?php echo $menuprofiles; ?>" alt="">
			<!--</a>-->
				<div class="error-page-cont">            
					<div class="error-content">
					 <p>This domain is currently unavailable.</p>
					 <p>Status: <?php echo $message;?></p>
				</div>
				</div>
		</div>
	



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
</div>
	   