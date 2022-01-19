<header class="header clearfix">
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header"> 
		<?php  
			$logo_image_class = '';
			// $logo_image = 'idea_cast_logo.png';
			$logo_image = 'icast-logo-wo-tm.png';
		/* if(AuthComponent::user('id')){ 
			$logo_image = 'logo.png';
			$logo_image_class = 'navbar-brand-login-img';
		} */
		?> 
		
		<a class="navbar-brand" href="<?php echo SITEURL; ?>">
			<img class="<?php echo $logo_image_class; ?>" src="<?php echo SITEURL?>images/<?php echo $logo_image; ?>" alt="logo" />
		</a>
		 
        <button data-target="#navbar-main" data-toggle="collapse" type="button" class="navbar-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      </div> 
	  <div class="nav-cont">
      <div id="navbar-main" class="navbar-collapse collapse navbar-right">
        <ul class="nav navbar-nav">
			<!--<li class="active"><a href="<?php //echo SITEURL?>"><i class="fa fa-home home-ic"></i></a></li>
			<li ><a href="<?php //echo $this->Html->url( SITEURL, true ); ?>">Home</a></li> 
			<li class=""><a href="<?php //echo $this->Html->url( SITEURL.'about', true ); ?>">About</a></li>-->
			<?php /*<li class=""><a href="<?php echo $this->Html->url( SITEURL.'why-jeera', true ); ?>">Why OpusView</a></li>
			<li class=""><a href="<?php echo $this->Html->url( SITEURL.'features', true ); ?>">FEATURES</a></li>
			 <li class=""><a href="<?php echo $this->Html->url( SITEURL.'price', true ); ?>">Pricing</a></li> 
			<li class=""><a href="<?php echo $this->Html->url( SITEURL.'downloads', true ); ?>">Downloads</a></li>
			<li class=""><a href="<?php echo $this->Html->url( SITEURL.'contactus', true ); ?>">Contact us</a></li>
			
			<li class=""><a href="<?php echo $this->Html->url( SITEURL.'blog', true ); ?>">Blog</a></li>*/ ?>
          <?php if(AuthComponent::user('id')) { ?>
		  <li class="">
		  
		 <!-- <a id="" data-original-title="Logoff" href="<?php echo SITEURL; ?>users/logout"  > <i class="fa fa-fw fa-sign-out"></i> Logout</a></p>-->
		  
			<?php
				 $userData = $this->session->read('Auth.User');

			 $profile = $this->Session->read('Auth.User.UserDetail.profile_pic');

			if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)) {
				$profiles = SITEURL.USER_PIC_PATH.$profile;
			}else{
				$profiles = SITEURL.'img/image_placeholders/profile_placeholder.png';
			}

			  SITEURL.'img/image_placeholders/logo_placeholder.gif';
			 
			?>				 
         </li>         
		 <?php }else { ?>
		 
		<?php }?>
		    
       
		 
		  <?php 
		  // IF USER LOGGED-IN, SET BUTTON LINK TO REDIRECT ON DASHBOARD PAGE,
		  // OTHERWISE SEND TO HOME PAGE
		  /* if(AuthComponent::user('id')): ?>  
		  <a class="btn btn-warning goto-ideascost" href="<?php echo SITEURL?>/projects/lists"><i class="fa fa-caret-right"></i> Go to Dashboard</a> 
		  
		  <?php else: ?>
		  <a class="btn btn-danger goto-ideascost" href="<?php echo SITEURL?>"><i class="fa fa-caret-right"></i> Go to Ideascast</a> 
		  <?php endif; */ ?>
		  
		  
        
       
			<?php if(AuthComponent::user('id')) { ?>
              <li class="dropdown user user-menu">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                  <img alt="User Image" class="user-image" src="<?php if(isset($profiles)) { echo $profiles; } ?>">
                 <!-- <span class="hidden-xs text-capitalize"><?php echo $userData['email']; ?></span>-->
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img alt="User Image" class="img-circle auto-mgd"  src="<?php echo $profiles; ?>">
                    <p class="text-black text-prjt" style="text-transform:none">
                      <?php echo $userData['email']; ?>                       
                    </p>
                  </li>
                 
                  <li class="user-footer">
					<?php if( $userData['role_id'] == 3 ){?>
					<div class="pull-left">                      
					<a  class="btn btn-info tipText btn-sm" href="<?php echo SITEURL; ?>organisations/domain_settings"><i class="fa fa-fw fa-desktop"></i> Back To Domains</a>
                    </div>
					<?php } else { ?>		
                    <div class="pull-left">                      
					<a  class="btn btn-info tipText btn-sm" href="<?php echo SITEURL; ?>dashboards/project_center"><i class="fa fa-fw fa-desktop"></i> Back To Projects</a>
                    </div>  
					<?php } ?>
                    <div class="pull-right">
                      <a class="btn btn-danger tipText btn-sm" href="<?php echo SITEURL; ?>users/logout"><i class="fa fa-fw fa-sign-out"></i> Sign Out</a>  
                      
                    </div>
                  </li>
                </ul>
              </li>
			  <?php }  else{ ?>
			    <li class="dropdown user user-menu">
				 <a href="<?php echo $this->Html->url( SITEURL.'users/login', true ); ?>">Account</a>
				</li>
			  <?php }   ?>
             
			 
            </ul>
		
		   </div>
		   </div>
    </div>
    
   <!-- <div class="search-box">
		<a href="#" id="searchtoggl"><i class="fa fa-search fa-lg"></i></a>    
		<div id="searchbar" class="clearfix">
		  <form id="searchform" method="get" action="<?php //echo SITEURL;?>contactus">		
			<input type="search" name="s" id="s" placeholder="Search..." autocomplete="off">
		  </form>
		</div>
	</div> -->
  </nav>
</header>


   
	  
<?php /* ?>

	<header class="header clearfix">
	<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
	<div class="navbar-header"> <a class="navbar-brand" href="<?php echo SITEURL?>"><img src="<?php echo SITEURL?>images/logo.png" alt="logo" /></a>
<?php   <button data-target="#navbar-main" data-toggle="collapse" type="button" class="navbar-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> ?>
      </div> 
      <div id="navbar-main" class="navbar-collapse collapse navbar-right">
        <ul class="nav navbar-nav">
          <li class="active"><a href="<?php echo SITEURL?>">Home</a></li>
          <li class=""><a href="<?php echo $this->Html->url( SITEURL.'about', true ); ?>">About us</a></li>
          <li class=""><a href="<?php echo $this->Html->url( SITEURL.'faq', true ); ?>">FAQ</a></li>
          <li class=""><a href="<?php echo $this->Html->url( SITEURL.'contact', true ); ?>">Contact us</a></li>
          <li class="dropdown">
              <?php 
                  $user = $this->Session->read('Auth.User');
                  if(!empty($user)){
                      //pr($user);
                  ?>
                  <a class=" dropdown-toggle" data-toggle="dropdown" href="#">
                      <i class="halflings-icon white user"></i> <?php echo $user['UserDetail']['first_name'];?>
                      <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                      
                      <li><a href="<?php echo SITEURL.'users/myaccount';?>"><i class="halflings-icon user"></i> Profile</a></li>
                      <li><a href="<?php echo SITEURL.'users/logout';?>"><i class="halflings-icon off"></i> Logout</a></li>
                  </ul>
              <?php }else{?>
                <a href="<?php echo $this->Html->url( SITEURL.'users/myaccount', true ); ?>">My Account</a></li>
              <?php }?>
        </ul>
        <a class="btn btn-warning goto-ideascost" href="<?php echo SITEURL?>projects"><i class="fa fa-caret-right"></i> Go to Ideascast</a> 

 
        </div>
    </div>
  </nav>
</header>
<?php */ ?>

<style>


</style>

<div id="scroll" >Scroll &#8595;</div>
<div id="scrolls"  style="display:none">Scroll &#8593;</div>
 <script type="text/javascript" >
 $(document).ready(function(){
	
	$('.whyjeera-left ul li a').on('click touchend', function(e) {
		var el = $(this);
		var link = el.attr('href');
		window.location = link;
	});
	
	$("body, html").animate({
       scrollTop: 0
    });
	
	var url = window.location.pathname;
  
        if(url !='/'){
         urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
         // now grab every link from the navigation
         $('#navbar-main ul.navbar-nav li a').each(function(){
             // and test its normalized href against the url pathname regexp
             if(urlRegExp.test(this.href.replace(/\/$/,''))){ 
          
 			    $(this).parent().addClass('active');
             }else{
			   $(this).parent().removeClass('active');
			 }
         });
		 
		 }
		 
	})	 
 </script>
  <script type="text/javascript" language="javascript">
         $(function () {
             var $win = $(window);

             $win.scroll(function () {
/*                  if ($win.scrollTop() == 0)
                     alert('Scrolled to Page Top');
					 
                 else if ($win.height() + $win.scrollTop()
                                == $(document).height()) {
                     alert('Scrolled to Page Bottom');
                 } */
				 
				 if ($win.height() + $win.scrollTop()
                                == $(document).height()) {
                    // alert('Scrolled to Page Bottom');
					 $('#scroll').hide();
					 $('#scrolls').show();
                 }else{
				     $('#scroll').show();
					 $('#scrolls').hide();
				 }
             });
			 
			 $('#scroll').click(function(){
				var y = $(window).scrollTop();  //your current y position on the page
				$("body, html").animate({
					  scrollTop: y + $(window).height()-120
				});
			 });
			 
			 $('#scrolls').click(function(){
				var x = $(window).scrollTop();  
				$("body, html").animate({
					  scrollTop: 0
				});
			 });
			 
			 
			 
			 
			 
			 
			 
         });
    </script>
	
   <script type="text/javascript" language="javascript">
   
			$(document).ready(function(){
				var submitIcon = $('.searchbox-icon');
				var inputBox = $('.searchbox-input');
				var searchBox = $('.searchbox');
				var isOpen = false;
				submitIcon.click(function(){
					if(isOpen == false){
						searchBox.addClass('searchbox-open');
						inputBox.focus();
						isOpen = true;
					} else {
						searchBox.removeClass('searchbox-open');
						inputBox.focusout();
						isOpen = false;
					}
				});  
				 submitIcon.mouseup(function(){
						return false;
					});
				searchBox.mouseup(function(){
						return false;
					});
				$(document).mouseup(function(){
						if(isOpen == true){
							$('.searchbox-icon').css('display','block');
							submitIcon.click();
						}
					});
			});
            function buttonUp(){
                var inputVal = $('.searchbox-input').val();
                inputVal = $.trim(inputVal).length;
                if( inputVal !== 0){
                    $('.searchbox-icon').css('display','none');
                } else {
                    $('.searchbox-input').val('');
                    $('.searchbox-icon').css('display','block');
                }
            }
			
   </script>