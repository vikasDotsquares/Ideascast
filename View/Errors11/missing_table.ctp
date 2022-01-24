<?php  
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
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
					 <p>Status: Database missing.</p>
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