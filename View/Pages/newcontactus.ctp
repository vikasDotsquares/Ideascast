<?php 
	$email = $this->requestAction('/settings/sett/'.'email');
	$cphone = $this->requestAction('/settings/sett/'.'cphone');
	$phone = $this->requestAction('/settings/sett/'.'phone');								
	$address = $this->requestAction('/settings/sett/'.'address');
?>
<div class="smaill-banner-inner faq-banner">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>Contact us</h2>
      </div>
    </div>
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <div class="contact-left">
		<div class="left-top">
			<div class="map"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2434.203116674771!2d-1.5042256999999999!3d52.402994899999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48774bb719caacff%3A0x86abe71bee56ac25!2sThe+Technocentre%2C+Coventry+University%2C+Puma+Way%2C+Coventry%2C+West+Midlands+CV1+2TT%2C+UK!5e0!3m2!1sen!2sin!4v1434616994693" width="100%" height="385" frameborder="0" style="border:0"></iframe></div>
		</div>		
      </div>
    </div>
    <div class="col-sm-6">
      <div class="contact-right">
		<?php echo $this->Form->create('Page',array('class'=>'addEdit','id'=>'PageContactusForm')); ?>
        <div class="contact-content">
			<h4>IdeasCast Limited</h4>
			<div class="contact-address"><?php echo nl2br($address); ?></div>	
			<div class="contact-details">
				<p><span>e:</span> <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><br />
				<span>t:</span> <?php echo $phone; ?></p>
			</div>
			<h3>Online enquiry</h3>    						
							
				<div class="form-group has-feedback">
				<?php  echo $this->Form->input('full_name',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your name', ));   ?>
				</div> 
										
			
				<div class="form-group has-feedback">
				  <?php  echo $this->Form->input('phone',array('type'=>'text','label'=>false,"maxlength"=>'12', "required" ,'class' =>'form-control', 'placeholder' => 'Your contact number', ));   ?>
				</div> 
								
			
				<div class="form-group has-feedback">
				 <?php  echo $this->Form->input('email',array('type'=>'text','label'=>false, "required",'class' =>'form-control', 'placeholder' => 'Your email',  ));   ?>
				</div> 
		   
		  
				<div class="form-group has-feedback">		
				  <?php  echo $this->Form->input('subject',array('type'=>'text','label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'Subject', ));   ?>			
				</div>
		   
		  
			
				<div class="form-group has-feedback">
				 <?php  echo $this->Form->textarea('message',array('type'=>'text','rows'=>4,'label'=>false, "required",'class' =>'form-control' , 'placeholder' => 'How can we help ?', ));   ?>
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
<div class="why-jeera">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4">
        <div class="why-jeera-hading">
          <h2>WHY JEERA? </h2>
          <h3> <span>THE</span> <br />
            BENEFITS </h3>
          <a class="read-m" href="#"> Read More </a> </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <div class="row">
          <div class="jeera-right-iocn">
            <div class="col-sm-4 text-right"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/business.png" /></span> </div>
            <div class="col-sm-4 text-center"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/framework.png" /></span> </div>
            <div class="col-sm-4 text-left"> <span class="iocn-bf"><img src="<?php echo SITEURL; ?>images/2017/messaging.png" /></span> </div>
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
</div>