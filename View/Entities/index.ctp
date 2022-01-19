<!-- R118/G181/B50 

rgb(118,181,50) 
#76b532
	-->
<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
			
				<h1 class="pull-left"> <?php echo $page_heading ?> </h1>
				
				<div class="btn-group pull-right">
					<div class="btn-group action">
						 
					</div> 
				</div>
				
			</section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box ">
						<!-- CONTENT HEADING -->
                        <div class="box-header">
						
                            <h3 class="box-title">Update Element</h3>
							<p class="text-muted date-time"> </p>
							
							<!-- PAGE TOOLS BUTTONS -->
                            <div class="box-tools">
								<div class="btn-group"> 
									<a data-toggle="modal" class="btn btn-success btn-sm" href="#" data-target="#myModal"><i class="fa fa-fw fa-wrench"></i></a>
								</div>
							</div> 
                            
								
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							
                        </div>
						<!-- END CONTENT HEADING -->

						
                        <div class="box-body no-padding">
							<div class="col-lg-12">
							<?php //pr($elements); ?> 
									<div class="" id="dynamic_content">
										<?php 
											// LOAD PARTIAL WORKSPACE LAYOUT FILE FOR LOADING DYNAMIC WORKSPACE AREAS
											echo $this->element('../Entities/partials/get_partial', 
													[ 
														'data' =>   $elements 
													]
												);
										?> 
							
							
										
											
										</div>
										
									</div> 
						
						
					   
                    </div>
                </div>
            </div>
        </div>
		<!-- END MAIN CONTENT -->
		
	</div>
</div>
<!-- END OUTER WRAPPER -->



<?php echo $this->Html->css('projects/modal.popover'); ?>
<script type="text/javascript" >
    $( function() {
		
		/*
			--------------------------------------------------------------------------
			Getter and Setter for element attributes. it can:
			1. Get attributes as an Array
			2. Set attributes of newly created element
			
			@param 		#element_as_object
			@return 	Array/None
			@uses		1. $('#element').attrs({
			'name' : 'newName',
			'id' : 'newId',
			'readonly': true
			});
			2. var attrs = $('#element').attrs();
			
			--------------------------------------------------------------------------
		 */
		$.fn.attrs = function( attrs ) {
			var t = $(this);
			if (attrs) {
				// Set attributes
				t.each(function(i, e) {
					var j = $(e);
					for (var attr in attrs) {
						j.attr(attr, attrs[attr]);
					};
				});
				return t;
			} else {
				// Get attributes
				var a = {},
				r = t.get(0);
				if (r) {
					r = r.attributes;
					for (var i in r) {
						var p = r[i];
						if (typeof p.nodeValue !== 'undefined') a[p.nodeName] = p.nodeValue;
					}
				}
				return a;
			}
		};
		
		
		$('.popover-markup > .trigger').popover({
			html: true,
			title: function () {
				return $(this).parent().find('.head').html();
			},
			content: function () {
				return $(this).parent().find('.content').html();
			},
			container: '#dynamic_content',
			viewport: '#dynamic_content',
			placement: 'auto',
			template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
		})
		.on('show.bs.popover', function(event) {
				
				var bs = $(this).data('bs.popover') // Triggered element's data
				var bsContent = bs.getContent() // Get content html
				var bsTitle = bs.getTitle() // Get Title html
				var $arrow = bs.arrow(); // Header arrow object
				var $currentTarget = event.currentTarget; // Clicked object
				var $delegateTarget = event.delegateTarget; // Clicked object
				var defaults = bs.getDefaults(); // get all default options from library JS 
				 
				var $tip = bs.tip(); // Outer div.popover object 
				var position = bs.getPosition($(this)); // get clicked elements position object
				
				if( bs.hasContent() ) { // check is content is set or not
					// //console.log(bsContent)
					//console.log($(bsContent))
				}
				
			var $newInput = $("<input />")
								.attrs( {
									name: 'title_box',	
									type: 'text',	
									class: 'form-control',	
								})
								.data({
									'toggle': "popover" ,
									'trigger': "hover" ,
									'placement': "right" ,
									'content': "Must be at least 3 characters long, and must only contain letters."
								});
			
			var $hasFeedback = $(bs)
								.find("div.has-feedback")
								
			var title_text = $hasFeedback.attr('id')
				$newInput.attr('value', title_text)
						.appendTo($hasFeedback)
						.bind('popover')
				
				$hasFeedback.append('<a href="" class="glyphicon glyphicon-question-sign form-control-feedback"></a>')
				
				// $('body').delegate( '.popover-markup input[data-toggle="popover"]', 'focus', [], 'popover' )
				
				//console.log( $hasFeedback ) 
				//console.log( $newInput ) 
				
		})
		
		$('body').on('click', function (e) {
			$('.popover-markup > .trigger').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					$(this).popover('hide');
				} 
			});
		});
		/* 
		
		$('input[data-toggle="popover"]').each( function() {
			$(this).bind('click', 'show.bs.popover')
			
			// $(this).popover({ title: '', content: "Popover Content", placement: 'left|top|etc', trigger: 'hover', selector: 'form-control' });
			//console.log( $(this) )	
		});
		 */
		
		
		
		$('.trigger').on('click', function(e) {
				e.preventDefault()
		})
		
		
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
	
	})
</script>
