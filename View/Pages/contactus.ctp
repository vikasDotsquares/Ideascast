<?php
	$email = $this->requestAction('/settings/sett/'.'email');
	$cphone = $this->requestAction('/settings/sett/'.'cphone');
	$phone = $this->requestAction('/settings/sett/'.'phone');
	$address = $this->requestAction('/settings/sett/'.'address');
?>
<script src='https://www.google.com/recaptcha/api.js'></script> 
<!-- <div class="smaill-banner-inner faq-banner">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>Contact us</h2>
      </div>
    </div>
  </div>
</div> -->
<div class="smaill-banner-inner why-jeera-banner">
	<img class="img-responsive" src="<?php echo SITEURL?>images/2017/why-jeerr-banner.jpg" alt="" />
	<div class="smaill-banner-contant">
        <h2>Contact us</h2>
    </div>
</div>
<div class="container">
  <div class="row">
    <!-- <div class="col-sm-6">
      <div class="contact-left">
		<div class="left-top">
			<h4>IdeasCast Limited</h4>
			<div class="contact-address"><?php echo nl2br($address); ?></div>
			<div class="contact-details">
				<p><span>e:</span> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br />
				<span>t:</span> <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></p>
			</div>	 
		</div>
      </div>
    </div> -->
	
	<div class="col-sm-6">
      <div class="contact-left">
		<div class="left-top">
			<h4>IdeasCast Limited</h4>
			<div class="contact-address">
			15 Stoney Road<br />
			Coventry<br />
			CV1 2NP<br />
			United Kingdom<br />
 
			<?php //echo nl2br($address); ?></div>
			<div class="contact-details" style="border-top:none; border-bottom: 3px solid #939598; margin:0 0 40px 0;">
				<p><span>e:</span> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br />
				<span>m:</span> <a href="+44 (0)7768 392705<?php //echo $phone; ?>">+44 (0)7768 392705<?php //echo $phone; ?></a></p>
			</div>
			
			<h4>Sales Offices:</h4>
			<div class="contact-address">
				<strong>European Sales Enquiries:</strong><br />
				XEAUSOFT<br />
				Pure Offices<br />
				Plato Close<br />
				Leamington Spa<br />
				Warwickshire<br />
				CV34 6WE<br />
				United Kingdom
			</div>
			<div class="contact-details" style="border-top:none; border-bottom: none; margin:0;">
				<p><span>e:</span> <a href="mailto:opusview@xeau.com">opusview@xeau.com</a><br />
				<span>t:</span> <a href="tel:+44 (0)1926 354000">+44 (0)1926 354000</a></p>
			</div>
			 
			 
			<div class="contact-address">
				<strong>USA and Canada Sales Enquiries:</strong><br />
				BARBECANA INC<br />
				1001 S Dairy Ashford Ste 100<br />
				Houston TX 77077<br />
				USA 
				 
			</div>
			<div class="contact-details" style="border-top:none; border-bottom: none; margin:0;">
				<p><span>e:</span> <a href="mailto:info@barbecana.com">info@barbecana.com</a><br />
				<span>t:</span> <a href="tel:+1 281-971-9825">+1 281-971-9825</a></p>
			</div> 
			
			<!-- <div class="map"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2434.203116674771!2d-1.5042256999999999!3d52.402994899999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48774bb719caacff%3A0x86abe71bee56ac25!2sThe+Technocentre%2C+Coventry+University%2C+Puma+Way%2C+Coventry%2C+West+Midlands+CV1+2TT%2C+UK!5e0!3m2!1sen!2sin!4v1434616994693" width="100%" height="385" frameborder="0" style="border:0"></iframe></div> -->
		</div>
      </div>
    </div>
	
    <div class="col-sm-6">
      <div class="contact-right">
		<?php echo $this->Form->create('Page',array('class'=>'addEdit','id'=>'PageContactusForm')); ?>
        <div class="contact-content">

			<h3 style="margin-top: 10px;" >Contact Us</h3>

				 
				<div class="form-group has-feedback">
					<div class="row">
						<div class="col-sm-6 first-name">							
							<?php  echo $this->Form->input('first_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'First name', ));   ?>
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
				  <?php  echo $this->Form->input('phone',array('type'=>'text','label'=>false, "required" ,'class' =>'form-control', 'placeholder' => 'Company name', ));   ?>
				</div>
				<div class="form-group has-feedback">
				  <?php  echo $this->Form->input('subject',array('type'=>'text','label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'Your job title', ));   ?>
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
				 <?php  echo $this->Form->textarea('message',array('type'=>'text','rows'=>4,'label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'How can we help ?', ));   ?>
				</div>
				<div class="form-group has-feedback">
					<div class="g-recaptcha" data-sitekey="6LekbnMUAAAAAJfv0-hkn4IMQ9YfwHm6wvIhsFI1"></div>
				</div>
				<div class="pull-left">
					<?php
						echo $this->Form->submit(
						'Submit',
						array('class' => 'btn btn-warning pull-left btn-flat', 'title' => 'Submit','div' => false)
						);
					?>
				</div>
        </div>
		<?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>
</div>
<?php /* <div class="why-jeera">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4">
        <div class="why-jeera-hading">
          <h2>WHY OpusView? </h2>
          <h3> <span>THE</span> <br />
            BENEFITS </h3>
          <a class="read-m" href="<?php echo SITEURL.'empoweringteamwork'; ?>"> TAKE A LOOK </a> </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <div class="row">
          <div class="jeera-right-iocn">
            <div class="col-xs-4 col-sm-4 text-right"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/business.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-center"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span> </div>
            <div class="col-xs-4 col-sm-4 text-left"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
 <div class="container">
  <div class="row">
    <div class="col-sm-12">
		<?php echo $this->element('front/contactfordemo');?>
    </div>
  </div>
</div> */ ?>
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