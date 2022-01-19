<style>
.has-error span.error{color: #ff3e3e;font-size: 12px;display: block; clear: both;}
.forget-password{
	float: right;
	width: 210px;
	font-weight: 600;
}
.forgot-password .submit-btn{width: 50%; padding: 0;}
.back-login{float: right;}
#flashMessage{
	background:transparent;
	padding: 0;
	color :#333;
	border:none;
	
}
</style>

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


<div class="clearfix">

<div class="login-card-outer">
	<?php 
  //pr($this->Session->read();
	 $errs = $this->Session->read('Message.flash');
	 
	if(isset($errs) && !empty($errs)){?>
	<div class="login-card-error">
		<span class="icon">
		<?php if(isset($errs[0]['element']) && $errs[0]['element'] =='success'){ ?>
		<img src="<?php echo SITEURL; ?>images/info-icon.png" alt="">
		<?php } else{ ?>
		<img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt="">
		<?php } ?>
		</span>
		<span class="text"><?php  echo $this->Session->flash();?></span>
	</div>
	<?php } ?>
	<div  class="login-card">
		<div class="login-card-logo">
			<!--<a href="https://www.opusview.com" target="_blank">-->
			<img src="<?php echo $menuprofiles; ?>" alt="">
			<!--</a>-->
			
		</div>
		
		<?php echo $this->Form->create('User', array('url' => 'forgetpwd', 'autocomplete' => false )); ?>

		<?php
		if(isset($this->request->data['User']['Method'])){
			$default = $this->request->data['User']['Method'];
		}else{
			$default = '5';
		}
		?>
		<div class="form-wrap">
			<div class="form-group we-will-text">
				<p>We will email you a link to change your password:</p>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('User.email',array('label' =>false, 'div' => false, 'placeholder' => 'Email', 'class' =>'form-control ', 'type' => 'email', 'autocomplete' => false));?>
				<!--<div class="login-error-icon"><img src="<?php echo SITEURL; ?>images/SignInErrorIcon.png" alt=""></div>-->
			</div>

			<div class="secrete-checkboxes">
				<div class="form-group has-feedback" style="display:none;">
					<input type="radio" checked="checked" class="testClass minimal" value="5" id="flat5" name="data[User][Method]">
					<label for="flat5">Using Email</label>
					<input type="radio" class="testClass minimal" value="6" id="flat6" name="data[User][Method]">
					<label for="flat6">Using Secret</label>&nbsp;

				</div>
			</div>

			<div  id="seceret" style="display:none;">
				<?php //pr($questionArray);
				$question = array_rand($questionArrayAns,1);
				?>
				<div class="form-group" >
					<label>Secret</label>
					<?php
						echo $this->Form->input('UserDetail.question', array('type'=>'select', 'div' => false, 'options'=>$questionArrayAns, 'label'=>false, 'value'=>$question, 'empty'=>'Select Secret Questions', 'class' =>'form-control text-white'));
					?>
				</div>

				<div class="form-group">
				<label>Answer</label>
					<?php echo $this->Form->input('UserDetail.answer',array('label' => false, 'div' => false, 'placeholder' =>'Answer' , 'class' =>'form-control text-white', 'type' => 'text'));?>
				</div>

			</div>
		</div>
		<div class="form-group btn-signin-wrap">
			<button class="btn-custom" type="submit"> Send Email </button>
		</div>
		<div><?php echo $this->Html->link("Return to Sign In",array("controller"=>"users","action"=>"login"), array('class' => 'text-button', 'escape' => false)); ?></div>
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
	
	
	 





</div>
<script type="text/javascript" >
 $(document).ready(function(){
	 
	$('#UserEmail').focus();
	
	
	$.validator.addMethod(
        'email',
        function(value, element){
            return this.optional(element) || /(^[-!#$%&'*+/=?^_`{}|~0-9A-Z]+(\.[-!#$%&'*+/=?^_`{}|~0-9A-Z]+)*|^"([\001-\010\013\014\016-\037!#-\[\]-\177]|\\[\001-\011\013\014\016-\177])*")@((?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+(?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)$)|\[(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}\]$/i.test(value);
        },
        'Please enter email in valid format.'
    );
	$("#UserForgetpwdForm").validate({
        normalizer: function( value ) {
            return $.trim( value );
        },
		rules: {
			UserEmail: {
				required: true,
                email: true
			},
		},
		messages: {
		'data[User][email]': {
			//required: "Enter an email address",
			required: "Include a username, an @ symbol, and a domain",
			email: "Include a username, an @ symbol, and a domain",
			 
		},		 	
	 
	}
		/* success: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		//errorClass: "error-message error",
		errorElement: "span",
		errorPlacement: function(error, element) {
			element.closest('.form-group').addClass('has-error');
			error.insertAfter(element);
		}, */
	});
$('#seceret').hide();
$('#seceret input').attr('disabled','disabled');
$('#seceret select').attr('disabled','disabled');

//$('#flat').hide();
//$('#flat input').attr('disabled','disabled');

});
$(window).load(function(){

		if($('#flat6').is(':checked')){
			$('#seceret').show();
			$('#seceret input').removeAttr('disabled','disabled');
			$('#seceret select').removeAttr('disabled','disabled');
		}
		if($('#flat5').is(':checked')){
					//$('#seceret').hide();
					$('#seceret input').attr('disabled','disabled');
					$('#seceret select').attr('disabled','disabled');
		}

		$('#flat6').click(function() { //alert(0);
			$('#seceret').show();
			$('#seceret input').removeAttr('disabled','disabled');
			$('#seceret select').removeAttr('disabled','disabled');
		});

	    $('#flat5').click(function() { //alert(1);
					$('#seceret').hide();
					$('#seceret input').attr('disabled','disabled');
					$('#seceret select').attr('disabled','disabled');
		});

})
</script>
<style>.text-white{color:#fff !important;}</style>
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
.error {
    color: #ff3e3e;
    font-size: 12px;
    display: block;
    clear: both;
}
</style>