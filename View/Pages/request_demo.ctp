<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="smaill-banner-inner exclusive-Packs"> <img class="img-responsive" src="<?php echo SITEURL?>images/2017/demo_request_bg.jpg" alt="offer-banner" />
  <div class="smaill-banner-contant">
    <h2>REQUEST DEMO</h2>
  </div>
</div>



<div class="container jeerademo">
	
	
  <div class="row">
	  <?php if( isset($this->request['pass'][0]) && $this->request['pass'][0] == 'thanks' ){ ?>
	  <div class="col-sm-12">
		  <div class="thankyoupage">
			  <h5>Thank you!</h5>
			  <p>Thanks for requesting a demo of OpusView! Your details are now being read by a member of our team who will contact you to set up a suitable date and time to show our platform to you and your colleagues. In the meantime, if you have any specific questions please <a href="#">Contact Us</a>, we are here to help.</p>
		  </div>
	  </div>
	<?php } else { ?>
		<div class="col-sm-6">
		  <div class="offering-free"><h3>High Performance Team Working</h3></div>
		  <div class="request-demo-contant">
			
			<p>We hear from Customers the different frustrations they face when managing their project portfolios - from assessing resource availability to managing risk, finding useful communication tools that work for multiple teams, handling project creep as well as how to quickly template projects and repeat success to initiate new programs effectively.</p>

			<p>Schedule a 30-minute demo with our friendly team to see how OpusView helps you overcome the challenges of coordinating the right people and aligning everyone’s work purpose to deliver on key business initiatives.</p>
			
			</div>
		</div>
	
	  
    <div class="col-sm-6 requestdemo">
      	<div class="offering-contant-right  contact-select">
        <div class="offer-form-tital">REQUEST YOUR DEMO</div>
		<?php echo $this->Form->create('Page',array('id'=>'PageJeeraRequestDemo')); ?>
        	 
			<div class="form-group has-feedback">
					<div class="row">
						<div class="col-sm-6 first-name">							
							<?php  echo $this->Form->input('first_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'First name' ));   ?>
						</div>
						<div class="col-sm-6">
							<?php  echo $this->Form->input('last_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Last name', ));   ?>
						</div>					
					</div>					
				</div>
				<div class="form-group has-feedback">
				 <?php  echo $this->Form->input('email',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your email',  ));   ?>
				</div>
				<div class="form-group has-feedback">
				  <?php  echo $this->Form->input('company_name',array('type'=>'text','label'=>false, "required" ,'class' =>'form-control', 'placeholder' => 'Company name', ));   ?>
				</div>
				<div class="form-group has-feedback">
				  <?php  echo $this->Form->input('job_title',array('type'=>'text','label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'Your job title', ));   ?>
				</div>

				<div class="form-group has-feedback">
					<div class="row">
				  	<div class="col-sm-6">
						 <?php echo $this->Form->input('industries', array('options' => $industryList,  'empty' => 'Industry', 'label' => false, 'class' => 'form-control', 'style'=>'color: rgb(187,187,193) ')); ?>
				 	</div>
				 	<div class="col-sm-6 country-sec">
						 <?php echo $this->Form->input('countries', array('options' => $this->Common->getCountryList(),  'empty' => 'Country', 'label' => false, 'class' => 'form-control','style'=>'color: rgb(187,187,193) ')); ?>
				 	</div>
					</div>
				</div>
				<div class="form-group">
				 <?php  echo $this->Form->textarea('message',array('type'=>'text','rows'=>4,'label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'Tell us about the challenges you are trying to solve?', ));   ?>
				</div>
				<div class="form-group has-feedback">
					<div class="g-recaptcha" data-sitekey="6Ldd4E4UAAAAAJmdZEZBAXuKw0VssDwS7ZE2YNQV"></div>
				</div>
				<div class="pull-left">
					<?php
						echo $this->Form->submit(
						'Submit',
						array('class' => 'btn btn-warning pull-left btn-flat', 'title' => 'Submit','div' => false)
						);
					?>
				</div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
	<?php } ?>  
  </div>
</div>
</div>
<script>
$(function() {
		// validate signup form on keyup and submit
		$("#PageJeeraRequestDemo").validate({
			rules: {
				'data[Page][first_name]': "required",
				'data[Page][last_name]': "required",
				'data[Page][company_name]': "required",
				'data[Page][job_title]': "required",
				'data[Page][industries]': "required",
				'data[Page][countries]': "required",
				'data[Page][message]': "required",
				'data[Page][email]': {
					required: true,
					email: true
				}
			},
			messages: {
				'data[Page][first_name]': "Please enter your first name",
				'data[Page][last_name]': "Please enter your last name",
				'data[Page][company_name]': "Please enter your company name",
				'data[Page][job_title]': "Please enter your job title",
				'data[Page][industries]': "Please select your industry",
				'data[Page][countries]': "Please select your country",
				'data[Page][message]': "This is required",
				'data[Page][email]': {
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