<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="smaill-banner-inner exclusive-Packs"> <img class="img-responsive" src="<?php echo SITEURL?>images/2017/offer-banner.jpg" alt="offer-banner" />
  <div class="smaill-banner-contant">
    <h2>TRY IT</h2>
  </div>
</div>
<div class="container jeerademo">
  <div class="row">
    <div class="col-sm-6">
      <div class="offering-contant-left">
        <div class="offering-free"><h3>OpusView - Business Social Software</h3></div>
        <p>OpusView is a powerful  mix of social working, project management<br>and real-time communications that maximizes the return on<br>your teams and business projects.</p>

		<p>OpusView provides all the productivity,  management, collaborative<br>and social tools in one seamless real-time system.</p>		
		<p>Complete this short form to get started with high performance team working with OpusView.</p>

		<div class="offer-form-tital"><!--JUST COMPLETE THE SHORT FORM AND WE WILL GET BACK TO YOU--></div>

        </div>
    </div>
    <div class="col-sm-6">
      	<div class="offering-contant-right  contact-select">
        <div class="offer-form-tital">REQUEST YOUR TRIAL</div>
		<?php echo $this->Form->create('Page',array('id'=>'PageJeeraDemo')); ?>
        	 
			<div class="form-group">
				<div class="row">
					<div class="col-sm-6 first-name">							
						<?php  echo $this->Form->input('first_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'First name', 'id'=>'FullNames', 'aria-required'=>'true' ));   ?>
					</div>
					<div class="col-sm-6">
						<?php  echo $this->Form->input('last_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Last name', 'id'=>'FullName', 'aria-required'=>'true' ));   ?>
					</div>					
				</div>					
			</div>
          	<div class="form-group">
				<?php  echo $this->Form->input('your_email',array('type'=>'email','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your email', 'id'=>'email', 'aria-required'=>'true' ));   ?>
          	</div>
          	<div class="form-group">
				<?php  echo $this->Form->input('company_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Company name', 'id'=>'companyname', 'aria-required'=>'true' ));   ?>
          	</div>
		  	<div class="form-group">
				<?php  echo $this->Form->input('your_title',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your job title', 'id'=>'yourtitle', 'aria-required'=>'true' ));   ?>
          	</div>
          	<div class="form-group">
          		<div class="row">
				  	<div class="col-sm-6" >
						 <?php echo $this->Form->input('industries', array('options' => $industryList,  'empty' => 'Industry', 'label' => false, 'class' => 'form-control','style'=>'color: rgb(187,187,193) ')); ?>
				 	</div>
				 	<div class="col-sm-6 country-sec">
						 <?php echo $this->Form->input('countries', array('options' => $this->Common->getCountryList(),  'empty' => 'Country', 'label' => false, 'class' => 'form-control','style'=>'color: rgb(187,187,193) ')); ?>
				 	</div>
          		</div>
			</div>

			<div class="form-group has-feedback">
					<div class="g-recaptcha" data-sitekey="6Ldd4E4UAAAAAJmdZEZBAXuKw0VssDwS7ZE2YNQV"></div>
			</div>

			<div class="form-group"  >
				<?php
					echo $this->Form->submit('Submit', array('class' => 'btn pull-left', 'title' => 'Submit','div' => false) );
				?>
			</div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>
</div>
</div>
<script>
$(function() {
		// validate signup form on keyup and submit
		$("#PageJeeraDemo").validate({
			rules: {
				'data[Page][your_name]': "required",
				'data[Page][company_name]': "required",
				'data[Page][your_title]': "required",
				'data[Page][industries]': "required",
				'data[Page][countries]': "required",
				'data[Page][your_email]': {
					required: true,
					email: true
				}
			},
			messages: {
				'data[Page][your_name]': "Please enter your name",
				'data[Page][company_name]': "Please enter your company name",
				'data[Page][your_title]': "Please enter your title",
				'data[Page][industries]': "Please select your industry",
				'data[Page][countries]': "Please select your country",
				'data[Page][your_email]': {
					 required: "Please enter your email",
					 email: "Please enter a valid email address",
				}
			}
		});
});

</script>
<script>
$(document).ready(function() {
    $('select').css('color','rgb(187,187,193)');
    $('select').change(function() {
       var current = $(this).val();
       if (current != 'null' && current != '') {   
           $(this).css('color','#555');
       } else {
           $(this).css('color','rgb(187,187,193)');
       }
    }); 
});
</script>