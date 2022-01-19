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

		</div>
		<?php echo $this->Form->create('User', array('url' => array('controller' => 'Users', 'action' => 'login'), 'autocomplete' => false) ); ?>
			<input type="hidden" name="is_mobile" value="0" id="is_mobile" />
			<div class="form-wrap">
				<div class="form-group">
					<?php echo $this->Form->input('User.email', array('label' => false, 'div' => false, 'type' => 'email', 'placeholder' => 'Email', 'class' =>'form-control', 'autocomplete' => false,'required' => false, 'style' => 'background: none;'));?>
					<!--<div class="login-error-icon" id="email-error" data-toggle="popover" title="" data-content="Please enter email." ><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></div>-->
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('User.password', array('label' => false , 'div' => false, 'type' => 'password', 'placeholder' => 'Password', 'required' => false,'maxlength' =>50,  'class' =>'form-control pass', 'style' => 'background: none;'));?>
					<!--<div class="login-error-icon"><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></div>-->
				</div>



			  <div class="form-group btn-signin-wrap"><button class="btn-custom" type="submit"> Sign In</button></div>

				<div class="login-card-actions">
				<div class="remember-me">
					<div class="checkbox icheck" >
						<?php echo $this->Form->input('remember_me', array('label' => 'Remember me', 'div' => false, 'type' => 'checkbox', 'placeholder' => 'username', 'class' =>'checkbox' ));?>
					</div>
				</div>
				<div class="forgot-password">
					<?php echo $this->Html->link("Forgot password?",array("controller"=>"users","action"=>"forgetpwd")); ?>
				</div>
				</div>
			</div>
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



<?php /*?><?php  echo $this->Session->flash();?>
<div class="new-sign-in-box">

		<div class="sign-in-header">
        		<h3>login</h3>
				<?php //echo $this->Html->link('Create New Account', array('controller'=>'users','action'=>'register'), ['class' => 'pull-right']); ?>
        </div>

	<div class="sign-in-body">
		<?php echo $this->Form->create('User', array('url' => array('controller' => 'Users', 'action' => 'login'), 'autocomplete' => false) ); ?>
			<input type="hidden" name="is_mobile" value="0" id="is_mobile" />
			<div class="form-wrap">
				<div class="form-group">
					<label>Email</label>
					<?php echo $this->Form->input('User.email', array('label' => false, 'div' => false, 'type' => 'email', 'placeholder' => 'Email', 'class' =>'form-control', 'autocomplete' => false, 'style' => 'background: none;color:#fff;'));?>
				</div>

				<div class="form-group">
					<label>Password</label>
					<?php echo $this->Form->input('User.password', array('label' => false , 'div' => false, 'type' => 'password', 'placeholder' => 'Password','title'=>'Please provide password', 'class' =>'form-control pass', 'style' => 'background: none;color:#fff;'));?>
				</div>

				<div class="remember-me">
					<div class="checkbox icheck" style="color: #fff">
						<?php echo $this->Form->input('remember_me', array('label' => 'Remember me', 'div' => false, 'type' => 'checkbox', 'placeholder' => 'username', 'class' =>'checkbox' ));?>
					</div>
				<!-- <label>
				  <input type="checkbox" name="remember_me" id="remember_me">
				  Remember
				  </label> -->
				</div>

			  <button class="btn btn-primary pull-right black-btn submit-login" type="submit"> Login</button>
			</div>
		<?php echo $this->Form->end(); ?>

			<div class="form-group forgot-password">
				<?php echo $this->Html->link("Password ?",array("controller"=>"users","action"=>"forgetpwd")); ?>
			</div>
        </div>

</div>
<div class="all-reserved">&copy; <?php echo date('Y'); ?> IdeasCast Limited. All rights reserved. <a target="_blank" href="https://www.opusview.com/" style="text-decoration:none;">www.opusview.com</a>
<p><a href="https://www.opusview.com/privacy" target="_blank" >Privacy Policy</a>&nbsp;or&nbsp;<a href="https://www.opusview.com/terms" target="_blank" >Terms of use</a></p>
</div><?php */?>


<script type="text/javascript" >
var foo = document.getElementById('UserEmail');


//$("#email-error").html(foo.setCustomValidity(foo.validationMessage + ' An error occurred'));
$(function() {

    $.isDocumentClick = false;
    if(!$.isDocumentClick){
    	$.isDocumentClick = true;
	    var noAudioElement = document.createElement('audio');
	    var oneClick = 0;
	    $(document).on('click', function(event) {
	        if(oneClick == 0) {
	            noAudioElement.setAttribute('src', $js_config.subdomain_base_url + '/chat-audio/novoice.mp3');
	            noAudioElement.play();
	            // $.noAudio.play();
	            oneClick = 1;
	        }
	    });
	}

	$('#UserEmail').focus();


	$.isMobile = false; //initiate as false

	$('.pass').removeAttr('value');
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) $.isMobile = true;
	$("input#UserEmail").focus();
	if($.isMobile === true) {
		$('#is_mobile').val(1);
	}
	else{
		$('#is_mobile').val(0);
	}



		$(".submit-login").on('click', function( event ) {
			//event.preventDefault();
			//console.log('test');
			//$(this).addClass('dis abled');
		})

		$("#UserLoginForm").submit(function( event ) {
			//event.preventDefault();
			//$(".submit-login").addClass('disabled');
		})



	$.validator.addMethod(
        'email',
        function(value, element){
            return this.optional(element) || /(^[-!#$%&'*+/=?^_`{}|~0-9A-Z]+(\.[-!#$%&'*+/=?^_`{}|~0-9A-Z]+)*|^"([\001-\010\013\014\016-\037!#-\[\]-\177]|\\[\001-\011\013\014\016-\177])*")@((?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+(?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)$)|\[(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}\]$/i.test(value);
        },
        'Please enter email in valid format.'
    );


jQuery("#UserLoginForm").validate({
	rules: {
		'data[User][password]': "required",

		'data[User][email]': {
			required: true,
			email: true,


		}
	},
		messages: {
		'data[User][email]': {
			required: "Enter an email address",
			email: "Include a username, an @ symbol, and a domain",

		},
		'data[User][password]':  {
		required: "Enter a password",

		},

	}


});

/* 	$("#UserLoginForm").validate({
        normalizer: function( value ) {
            return $.trim( value );
        },
		rules: {
			UserEmail: {
				required: true,
                email: true
			},
			UserPassword: {
				required: true,
                //email: true
			},
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorClass: "error-message error",
		errorElement: "span",
		errorElement: "span",
		errorPlacement: function(error, element) {
			element.closest('.form-group').addClass('has-error');
			error.insertAfter(element);
		},
	}); */



})
</script>
<style>

#successFlashMsg,#flashMessage{
	background: transparent !important;
    padding: 0 !important;
    color: #444 !important;
    border: none;
    box-shadow: none;

}
#flashMessage p,#successFlashMsg p{
	margin: 0 0 0 0px !important;
	padding: 0 !important;
}
.error {
    color: #ff3e3e;
    font-size: 12px;
    display: block;
    clear: both;
}
</style>