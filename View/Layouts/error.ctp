<!DOCTYPE html>

<html lang="en" style="overflow-x: hidden">
    <head>
		<style>
			.fixed-position {
				position: fixed;
				right: 130px;
				bottom: 10px;
				z-index: 2147483647;
			}
			.chat-icon-box {
				width: 54px;
				height: 54px;
				background-color: #58c6ff;
				border-radius: 50%;
				cursor: pointer;
			}
			.chat-icon-box:hover {
				background-color: #67a028;
			}
			.circle {
				background-color: rgba(255, 255, 255, 0.2);
				border: 2px solid #fff;
				border-radius: 50%;
				color: #fff;
				font-size: 28px;
				font-weight: bold;
				height: 100%;
				left: 50%;
				line-height: 45px;
				position: absolute;
				text-align: center;
				top: 50%;
				transform: translate(-50%, -50%);
				width: 100%;
			}
			.notify {
				animation: animSpin 4s ease-in-out forwards infinite, animFade 0s ease forwards infinite;
			}

			@keyframes animSpin {
				0% { -webkit-transform: perspective(120px) rotateY(180deg) rotateX(0deg); transform: perspective(120px) rotateY(181deg) rotateX(0deg);
			}
				50% { -webkit-transform: perspective(120px) rotateY(0deg) rotateX(0deg); transform: perspective(120px) rotateY(0deg) rotateX(0deg);
			}
				100% { -webkit-transform: perspective(120px) rotateY(180deg); transform: perspective(120px) rotateY(181deg); }
			}
			@keyframes animFade {
				0% { opacity: .95; }
				100% { opacity: 1; }
			}
			.ico_cht {
				background-image: url("<?php echo SITEURL;?>images/icons/tollfree.png");
				background-repeat: no-repeat;
				background-size: 100% auto;
				display: block;
				/* filter: invert(100%); */
				height: 35px;
				margin: 6px 0 0 7px;
				width: 35px;
				border-radius: 50%;
			}
			.chat-icon-box:hover .ico_cht {
				background-color: #fbc760;
			}
			
		</style>
        <script>

            // This will turn-off the common methods in the console if it exists, and they can be called without error. In the case of a browser like IE6 with no console, the dummy methods will be created to prevent errors.

            // TO TURN-OFF CONSOLE.LOG FUNCTION COMPLETLY; SET BELOW VARIABLE TO FALSE
            var debug = true; // set to false to disable debugging

            if (debug === false) {
                if (typeof (window.console) === 'undefined') {
                    window.console = {};
                }

                var methods = ["log", "debug", "warn", "info"];
                for (var i = 0; i < methods.length; i++) {
                    console[methods[i]] = function () {
                    };
                }
            }

        </script>
        <!-- start: Meta -->
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">

        <meta charset="utf-8">

        <meta name="_token" content="<?php echo (isset($this->params['_Token']['key']) && !empty($this->params['_Token']['key'])) ? $this->params['_Token']['key'] : ''; ?>" />
        <title><?php echo $title_for_layout; ?></title>
        <!-- end: Meta -->
		
	 
    <!-- end: Meta -->	
    <?php echo $this->element('front/head');
		echo $this->element('front/header');
	?>	
	
	
    <?php //echo $this->Html->css('styles'); ?>	
</head>
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
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?> 
 
<body class="<?php echo isset($is_home)?$is_home:'inner_page_view';?>">
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
 
	$menuprofiles = SITEURL.'images/SignInOpusViewLogo.png';
 
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
					 <p>Status: Database being prepared.</p>
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
 
<footer class="footer-fix">
	<div class="footer-our-view" style="text-transform: uppercase; font-size: 12px;">
		 Company Registered in England and Wales (number 9384490)
	</div>
</footer>
 
  
    </body>
</html>
