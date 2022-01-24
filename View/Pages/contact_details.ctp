


<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix"> </section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
					
                    <section class="about_ic clearfix">
                    		<div class="container">
                            			<div class="section-title">
                                                <h2>About IdeaCast</h2>
                                        </div>
                                        
                                        <div class=" about_head row">
                                        		<div class="col-sm-7 nopadding-right">
                                                      <div class="about_head_left ">
                                                                <h2>New generation software to amplify creativity and productivity</h2>
                                                      </div>
                                                </div>
                                      			 <div class="col-sm-5  nopadding-left">         
                                                 <div class="about_head_right">
                                                                <img src="<?php echo SITEURL?>images/graphic.jpg" alt="project" class="img-responsive">
                                                 </div>
                                        </div>
                                       </div> 
                                        
                                        <div class="content_section row  clearfix  ">
                                        	
                                        		<div class="col-sm-8">
                                                	<div class="content_left">
                                                    		<h3>Mission statement</h3>	
                                                            <p>To develop and market OpusView, a new generation social software that boosts innovation and effectiveness at enterprise, team and individual levels.</p>
                                                            
                                                            <h3>Leadership</h3>	
                                                            <p>Bal has over twenty-five year’s software industry experience. The last fifteen have been as an entrepreneur during which time Bal has had 2 of his start-ups acquired. Bal has a BSc (Hons) degree in Computing and Physics and a PGDip in Information Systems. </p>
                                                            
                                                            <h3>Other stuff (by Bal)</h3>	
                                                            <p>Recently, I started to play Squash again, it’s the sport I have always loved. At home, I’m constantly dodging the cat (Haldi) and the dog (Tikka), our three-legged Rhodesian Ridgeback. If I can find room on the sofa, I’ll sit down to watch the latest thriller or Sci-fi movie. My favourite food is curry (guess  why…) and best loved beers are Peroni and Budweiser Budvar.</p>
                                                            
                                                            
                                                            
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                		<aside class="content_right">
                                                        		<h3>Location</h3>	
                                                                <P>Coventry,<br />
                                                                United Kingdom</P>
                                                                
                                                                <br /><br />
                                                                
                                                                <h3>Founded</h3>	
                                                                <P>2015</P>
                                                                
                                                                <div class="fonuder text-center">
                                                               			 <img src="<?php echo SITEURL?>images/founder.jpg" alt="project" class="img-responsive">
                                                                         <p>Bal Mattu, Founder</p>
                                                                </div>
                                                                
                                                        </aside>
                                        		</div>
                                          
                                        </div>
                                        		
                                        
                                        </div>
                            
                            </div>
                    </section>
            
        </div>
		<!-- END MAIN CONTENT -->
		
	</div>
</div>
<!-- END OUTER WRAPPER -->





<!-- ---------------- MODEL BOX INNER HTML LOADED BY JS ------------------------ -->

<div class="hide" >
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">POPUP MODAL HEADING</h3>
		
	</div>
	
	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<h5 class="project-name"> popup box heading </h5>
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit" class="btn btn-warning">Save changes</button>
		 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
	</div>
</div>

<!-- ---------------- JS TO OPEN MODEL BOX ------------------------ -->
<script type="text/javascript" >
    $('#myModal').on('hidden.bs.modal', function () {  
        $(this).removeData('bs.modal');
    });
	
// Submit Add Form 
      jQuery("#formID").submit(function (e) {
        var postData = jQuery(this).serializeArray();
		
        jQuery.ajax({
            url: jQuery(this).attr("action"),
            type: "POST",
            data: postData,
            success: function (response) {
                if (jQuery.trim(response) != 'success') {
                    
                } else {
                    
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action 
    });  
</script>


		



 
