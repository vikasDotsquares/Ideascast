<style type="text/css">
/* The Modal (background) */
.offer-popup {
    display: none; /* Hidden by default */   
}

/* The Close Button */

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
<img  class="blank-home" src="<?php echo SITEURL?>images/2017/home-1.jpg" alt="jeera"> 				 
<div class="flexslider">

          <ul class="slides">
		  
		  
            <li class="slide1">
				<div class="caption">
					<img  class="jrm" src="<?php echo SITEURL?>images/2017/first-slide.png" alt="jeera"> 				
				<!-- <div class="first-slide-text">Powering The <span>Digital Enterprise</span></div> -->
				<div class="first-slide-text">
					<span>Optimized<br>Project Working</span>
					
					<a href="<?php echo $this->Html->url( SITEURL.'jeera-demo', true ); ?>" class="read-more" >GET STARTED</a> 
					</div>
					
			   </div>
			   <img  class="jrm" src="<?php echo SITEURL?>images/2017/home-1.jpg" alt="jeera"> 
            </li>
			<!--<li class="slide2">
				<div class="caption">
					
					<h2 class="greish"> <span>Business</br>Social Software</span> </br>
for High Performance
Collaborative Working</h2>
					
					<a href="<?php echo $this->Html->url( SITEURL.'empoweringteamwork', true ); ?>" class="read-more" >TAKE A LOOK</a>  
			   </div>
              <img  class="jrm" src="<?php echo SITEURL?>images/2017/home-2.jpg" alt="jeera"> 
            </li>
			<li class="slide3">
				<div class="caption">					
					<h2 class="greish"> Work Smarter </br>
Drive Results</h2>
					
					<a href="<?php echo $this->Html->url( SITEURL.'empoweringteamwork', true ); ?>" class="read-more" >TAKE A LOOK</a> 
				</div>	
				<img  class="jrm" src="<?php echo SITEURL?>images/2017/home-3.jpg" alt="jeera"> 
            </li>
			<li class="slide2 slide4">
				<div class="caption">					
					<h2 class="greish"> <span>Increasing<br/> Project Success</span> </br>
 
Business Collaboration,<br/> Real-Time Communication <br/>and Information Management
					</h2>
					
					<a href="<?php echo $this->Html->url( SITEURL.'empoweringteamwork', true ); ?>" class="read-more" >TAKE A LOOK</a> 
			   </div>
				<img  class="jrm" src="<?php echo SITEURL?>images/2017/home-4.jpg" alt="jeera"> 
            </li>	-->

			
          </ul>
        </div>
        
    <div class="youroffer">    
	 
		<div class="offer-popup" id="myModal" >
			<span class="close">CLOSE OFFER </span>
			<div class="popuplogo">
				<img class="pull-left" src="<?php echo SITEURL?>images/2017/popup-logo.png" alt="popup-logo"> 
				<img class="pull-right" src="<?php echo SITEURL?>images/2017/popup-iocn.png" alt="popup-iocn"> 
			</div>
		
			<h3>Intelligent Teamworking</h3>
			<p>Business social software that combines real-time communication, project management and automated assistant technology.</p>
			<a class="but" href="<?php echo $this->Html->url( SITEURL.'jeera-offer', true ); ?>">FREE Jeera Team Pack </a>
		</div>
    </div>    
        
        
        
 <style>
 .text-white{ color:#fff;}
 .bg-whiten{ background : #fff; opacity: 0.8;}
 .greish{ color: #5b5b5b; font-size: 38px;  font-weight: 400;}
 .greish-para{ color: #000 !important;padding-top:0 !important;}
 .slider .caption .work-btn:hover {
    background: rgba(103, 160, 40, 1) none repeat scroll 0 0 !important;
}




 </style>
 
<script>
var date = new Date();
//var minutes = 1;
var minutes = 2;
//date.setTime(date.getTime() + (minutes * 60 * 1000)); 
date.setDate(date.getDate() + 1);
if ( $.cookie('offer_modal_jeera') != 'yes' ) { 
	setTimeout(function(){
	//	$('#jeeraoffer').trigger('click');
	},6000)
} 
// Get the modal
var modal = document.getElementById('myModal');
// Get the button that opens the modal
var btn = document.getElementById("jeeraoffer");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

if($(btn).length > 0) {
// When the user clicks the button, open the modal 

// Below code will be uncomment when slide will be moving
	/* btn.onclick = function() {
		modal.style.display = "block";
		btn.style.visibility = "hidden";	
	} */
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {    
	modal.style.display = "none";	
	btn.style.visibility = "visible";
	$.cookie('offer_modal_jeera', 'yes', { path: '/', expires: date});
	$("html,body").animate({scrollTop: 0}, 700);
}
</script>