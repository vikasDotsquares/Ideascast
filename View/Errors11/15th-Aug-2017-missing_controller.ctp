<style>
.error-page-cont .left {
    float: left;
}
.error-page-cont .right {
    overflow: hidden;
	padding-top: 20px;
	    font-size: 29px;
		    line-height: normal;
			padding-left:15px;
}
.error-page-cont .right p{
	margin-bottom:5px;
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

<section class="content"> 
<div class="container">
<div class="row">
<div class="col-sm-12">
          <div class="error-page-cont">            
            <div class="error-content">
				<div class="left">
					<img src="<?php echo SITEURL?>images/exclaim.png">
				</div>
				<div class="right">
					<?php 
					$message = 'Oops! Page not found.';
					if($this->params->controller == 'error2'){
						$message = "Oops! This domain will not be accessible. Domain's start date has not reached";
					}else if($this->params->controller == 'error3') {
						$message = "Oops!! This Domain end date is Expired";
					}else if($this->params->controller == 'error4') {
						$message = "Oops! This domain is disabled";
					}else if($this->params->controller == 'error1') {
						$message = "Oops! domain doesn't exist";
					}
					else if($this->params->controller == 'error5') {
						$message = "Oops! domain's disk space quota limit is exceeded";
					}
					?>
					  <h3><?php echo $message; ?>,</h3>
		<p>Please contact to your administrator</p>
		<p>REF: Domain start date.</p>
		<p>We could not find the page you were looking for.</p>
			</div>
              <!--<p>
                We could not find the page you were looking for.
                Meanwhile, you may <a href="<?php echo !empty(SUBSITEURL)? SUBSITEURL : 'http://www.ideascast.com/'; ?>">return to website</a>.
              </p>-->
              
            </div><!-- /.error-content -->
          </div><!-- /.error-page -->
		   </div>
		    </div> </div>
        </section>
        </section>