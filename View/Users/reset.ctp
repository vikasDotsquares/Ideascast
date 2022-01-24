
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

<div class="login-card-outer">
	<?php   
	 $class ="";	
	 $errs = $this->Session->read('Message.flash');
	if(isset($errs) && !empty($errs)){?>
	<div class="login-card-error">
			<span class="icon">
			<?php if(isset($errs[0]['element']) && $errs[0]['element'] =='success'){ 
			$class ="";
			?>
			<img src="<?php echo SITEURL; ?>images/info-icon.png" alt="">
			<?php } else{
				$class ="disabled disable";
				 
				?>
			<img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt="">
			<?php } ?>
			</span>
		
		<span class="text"><?php echo $this->Session->flash(); ?></span>
	</div>
	<?php } ?>
 
	<div class="login-card">
		<div class="login-card-logo">
			<!--<a href="https://www.opusview.com" target="_blank">-->
			<img src="<?php echo $menuprofiles; ?>" alt="">
			<!--</a>-->
			
		</div>
		
			<?php echo $this->Form->create('User',array('url'=>'/users/reset/'.$token, "id" => "editcoupon","class"=>""));  ?> 
			<?php echo $this->Form->input('User.id', array('type' => 'hidden')); ?>
			<?php echo $this->Form->input('User.tokenhash', array('type' => 'hidden', 'value' => '')); ?>
			
			<div class="form-wrap" >
				<div class="form-group">								
					<?php echo $this->Form->input('User.password', array('type' => 'Password', 'placeholder' => 'New Password', 'div' => false, 'label' => false, 'class' => 'form-control '.$class, 'size' => 20,'maxlength' =>50, 'style' => 'max-width: 100%;')); ?>
					<!-- <div class="show_policy-wrap"> <a href="javascrip:;" class="show_policy">SHOW POLICY</a></div> -->
					
					<!--<div class="login-error-icon"><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></div>-->
				</div>
				<div class="form-group">					
					<?php echo $this->Form->input('User.cpassword', array('type' => 'password', 'placeholder' => 'Confirm Password', 'div' => false, 'required' => true, 'label' => false, 'class' => 'form-control '.$class, 'size' => 20,'maxlength' =>50, 'style' => 'max-width: 100%;')); ?>  
					<!--<div class="login-error-icon"><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></div>-->
				</div>
			</div>
			<div class="form-group btn-signin-wrap">
		<button class="btn-custom <?php echo $class; ?>" type="submit"> Change Password </button>
		</div>
		<a href="<?php echo SITEURL; ?>" class="text-button">Return to Sign In</a>
			<?php echo $this->Form->end(); ?>
		
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
jQuery.fn.putCursorAtEnd = function() {

  return this.each(function() {
    
    // Cache references
    var $el = $(this),
        el = this;

    // Only focus if input isn't already
    if (!$el.is(":focus")) {
     $el.focus();
    }

    // If this function exists... (IE 9+)
    if (el.setSelectionRange) {

      // Double the length because Opera is inconsistent about whether a carriage return is one character or two.
      var len = $el.val().length * 2;
      
      // Timeout seems to be required for Blink
      setTimeout(function() {
        el.setSelectionRange(len, len);
      }, 1);
    
    } else {
      
      // As a fallback, replace the contents with itself
      // Doesn't work in Chrome, but Chrome supports setSelectionRange
      $el.val($el.val());
      
    }

    // Scroll to the bottom, in case we're in a tall textarea
    // (Necessary for Firefox and Chrome)
    this.scrollTop = 999999;

  });

};
$( document ).ready(function() {
	
	
	$('#UserPassword').focus();
	
	$('body').on('focus', '#UserCpassword', function(event){
		
		 $('.popover').remove();
		
	});
	$('body').on('mouseenter focus', '#UserPassword', function(event){
		event.preventDefault();
		
		var href = $js_config.base_url + 'users/list_program_policy';;		
		$t = $(this);		 
		  $.ajax({
				url: href,
				type: "POST",				
				data: $.param({listpolicy:'resetpssword'}),
				global: false,
				crossDomain:true,
				success: function (response) {  console.log(response); 
                    $t.popover({
					placement : 'right',
					trigger : 'hover',
					crossDomain:true,
					html : true,
					template: '<div class="popover " role="tooltip"><div class="arrow"></div><h3 class="popover-title" style="display:none;"></h3><div class="popover-content reset-popover pop-content"></div></div>',
					container: 'body',
					delay: { hide: 400}
					});
					$t.attr('data-content',response);
					$t.popover('show');
                }
				
			})  
		})	
	 
	 
});
</script>
<style>
#flashMessage,#successFlashMsg{
	background: transparent !important;
    padding: 0 !important;
    color: #444444 !important;
    border: none;
    box-shadow: none;
	
}
#flashMessage p,#successFlashMsg p{
	margin: 0 0 0 0px !important;
	padding: 0 !important;
}
.error ,.error-message{
    color: #ff3e3e;
    font-size: 12px;
    display: block;
    clear: both;
}	
.disabled{
	pointer-events : none;
	opacity:0.8;
}
</style>