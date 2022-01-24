  
<script type="text/javascript">
jQuery(function($) {
	$('.nav-tabs').on('shown.bs.tab', function (e) {
		var now_tab = e.target // activated tab

		// get the div's id
		var divid = $(now_tab).attr('href').substr(1);
		$("#"+divid).text('current tabs: ' + divid);
		// $.getJSON('xxx.php').success(function(data){
			// $("#"+divid).text(data.msg);
		// });
	})
	
})
</script> 

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">
					
					<p class="text-muted date-time">
						<span> </span>
						<span> </span>
					</p>
					
				</h1>

			</section>
		</div>
		
	<div class="box-content">

		<div class="row ">
			 <div class="col-xs-12">
				  <div class="box border-top margin-top">
						<div class="box-header no-padding" style="">
					<!-- MODAL BOX WINDOW -->
							 <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
										<div class="modal-content"></div>
								  </div>
							 </div>
					<!-- END MODAL BOX -->
						</div>
						
					<div class="box-body clearfix list-shares" >
						<!-- FIRST TAB -->
						<ul class="nav nav-tabs" id="ajax_tabs">
						  <li class="active"><a data-toggle="tab" href="#ajax_login">Login</a></li>
						  <li><a data-toggle="tab" href="#ajax_registration">Registration</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="ajax_login" data-target="">
								first tab content
							</div>
							<div class="tab-pane" id="ajax_registration" data-target="">
								second tab content
							</div>
						</div>
						
						<!-- SECOND TAB -->
						<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
							  <li class="active"><a href="#red" data-toggle="tab">Red</a></li>
							  <li><a href="#orange" data-toggle="tab">Orange</a></li>
							  <li><a href="#yellow" data-toggle="tab">Yellow</a></li>
							  <li><a href="#green" data-toggle="tab">Green</a></li>
							  <li><a href="#blue" data-toggle="tab">Blue</a></li>
						 </ul>
						 <div id="my-tab-content" class="tab-content">
							  <div class="tab-pane active" id="red">
									<h1>Red</h1>
									<p>red red red red red red</p>
							  </div>
							  <div class="tab-pane" id="orange">
									<h1>Orange</h1>
									<p>orange orange orange orange orange</p>
							  </div>
							  <div class="tab-pane" id="yellow">
									<h1>Yellow</h1>
									<p>yellow yellow yellow yellow yellow</p>
							  </div>
							  <div class="tab-pane" id="green">
									<h1>Green</h1>
									<p>green green green green green</p>
							  </div>
							  <div class="tab-pane" id="blue">
									<h1>Blue</h1>
									<p>blue blue blue blue blue</p>
							  </div>
						 </div>
					</div>
						
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>	
