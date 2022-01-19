<div class="smaill-banner-inner exclusive-Packs"> <img class="img-responsive" src="<?php echo SITEURL?>images/2017/offer-banner.jpg" alt="offer-banner" />
  <div class="smaill-banner-contant">
    <h2>Exclusive<br /> Team Packs</h2>
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <div class="offering-contant-left">
        <div class="offering-free"><b>IdeasCast</b>  is offering FREE OpusView Team packs  for the first 20 organizations to sign up. </div>
        <p>Each OpusView Team Pack comes with the full version of OpusView and life-time usage. So here you go:</p>
        <ul class="offer-item">
          <li>FREE 10 USER LICENSES </li>
          <li>FREE SUPPORT FOR 60 DAYS</li>
          <li>FREE SOFTWARE UPDATES</li>
          <li>FREE 15GB CLOUD STORAGE</li>
           <li>WE WILL SET YOU UP</li>
          <li>CANCEL ANYTIME</li>
          <li>NO CREDIT CARD OR PAYMENT NEEDED</li>       
        </ul>
        <p class="additional-licenses">Additional user licenses and cloud storage can be purchased.</p>
        <!--<span class="Offer-not">Offer is not available to educational and charity organizations.</span>--> </div>
    </div>
    <div class="col-sm-6">
      <div class="offering-contant-right">
        <div class="offer-form-tital">JUST COMPLETE THIS VERY SHORT FORM TO SIGN UP AND WE WILL GET BACK TO YOU</div>
        
		<?php echo $this->Form->create('Page',array('id'=>'PageJeeraOffer')); ?>
          <div class="form-group">
			<?php  echo $this->Form->input('your_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your name', 'id'=>'FullName', 'aria-required'=>'true' ));   ?>
          </div>
          <div class="form-group">            
			<?php  echo $this->Form->input('your_email',array('type'=>'email','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your email', 'id'=>'email', 'aria-required'=>'true' ));   ?>
          </div>
          <div class="form-group">            
			<?php  echo $this->Form->input('company_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Company name', 'id'=>'companyname', 'aria-required'=>'true' ));   ?>
          </div>
          <div class="form-group">            
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
		$("#PageJeeraOffer").validate({
			rules: {
				'data[Page][your_name]': "required",
				'data[Page][company_name]': "required",				
				'data[Page][your_email]': {
					required: true,
					email: true
				}
			},
			messages: {
				'data[Page][your_name]': "Please enter your name",
				'data[Page][company_name]': "Please enter your company name",				
				'data[Page][your_email]': {
					 required: "Please enter your email",					 
					 email: "Please enter a valid email address",					 
				}
			}
		});
});
	
</script>
