<?php echo $this->Html->css('projects/bootstrap-input'); ?>
<!-- /.modal -->
<script type="text/javascript">
jQuery(function($) {
 
	
})
</script>

<div class="row">
	<div class="col-xs-12">
  <?php echo $this->Session->flash(); ?>
		<div class="row">
			<section class="content-header clearfix"> 
					<h1 class="box-title pull-left"><?php echo $page_heading; ?>
						<p class="text-muted date-time">
							<span>Project Sharing</span>
						</p>
					</h1>
				 
			</section>
		</div>

 
    <div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12"> 
                    <div class="box border-top margin-top">
                        <div class="box-header  " style=""> 
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX --> 
							
<!--
<div class="radio-family">
	<div class="radio radio-danger">
		<input type="radio" name="radio2" id="radio3" value="option1">
		<label for="radio3">
		Prev
		</label>
	</div>
	<div class="radio radio-danger">
		<input type="radio" name="radio2" id="radio4" value="option2">
		<label for="radio4">
		Next
		</label>
	</div>
</div>	 

<div class="radio-family">
	<div class="checkbox checkbox-danger">
		<input type="checkbox" name="checkbox1" id="checkbox1" value="option1">
		<label for="checkbox1">
		Prev
		</label>
	</div>
	<div class="checkbox checkbox-danger">
		<input type="checkbox" name="checkbox2" id="checkbox2" value="option2">
		<label for="checkbox2">
		Next
		</label>
	</div>
</div>
-->
                        </div>
						<div class="box-body clearfix list-acknowledge" style="min-height: 600px;">
							<div class="row">
								<h4>
									<?php echo $project_detail['Project']['title']; ?>
								</h4>
							</div>
							
							
<div class="container">
	<div class="row">
       <div class="col-lg-12">
			<div class="button-group">
				<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
					<span class="fa fa-key"></span> Users <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li>
						<a href="#" class="small" data-value="option1" tabIndex="-1">
							<input type="checkbox"/> Option 1
						</a> 
					</li>
					<li>
						<a href="#" class="small" data-value="option2" tabIndex="-1">
							<input type="checkbox"/> Option 2
						</a> 
					</li>
					<li>
						<a href="#" class="small" data-value="option3" tabIndex="-1">
							<input type="checkbox"/> Option 3
						</a> 
					</li>
				</ul>
				
				<button type="button" class="btn btn-success btn-sm select_users">
					<span class="fa fa-check"></span> Select
				</button>
			</div>
		</div>




		<div class="container" id="user_container" style="display: none;">
			<div class="row">
				<div class="col-md-12">
					<h4>Users List</h4>
					<div class="table-responsive">
						<table id="mytable" class="table table-bordred table-striped">
						   
							<thead>
								<th class="text-left" width="">
									<div class="checkbox-family">
										<div class="checkbox checkbox-danger">
											<input type="checkbox" name="checkall" id="checkall" value="checkall">
											<label for="checkall"></label>
										</div>
									</div> 
								</th>
								<th class="text-left">First Name</th>
								<th class="text-center">Permissions</th>
							</thead>
			
							<tbody>
								<tr>
									<td>
										<div class="checkbox-family">
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="checkthis" class="checkthis" value="checkthis">
												<label for="checkthis"></label>
											</div>
										</div> 
									</td>
									<td>User 1</td> 
									<td>
										
										<div class="checkbox-family">
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="permission_all" id="permission_all" value="all">
												<label for="permission_all">All</label>
											</div>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="permission_add" id="permission_add" value="add">
												<label for="permission_add">Add</label>
											</div>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="permission_update" id="permission_update" value="update">
												<label for="permission_update">Update</label>
											</div>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="permission_remove" id="permission_remove" value="remove">
												<label for="permission_remove">remove</label>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					 
				</div>
			</div>
		</div>
	 
	</div> 
</div>
				 
                </div>
            </div>
        </div>
	</div>
</div>

<style>
.open ul.dropdown-menu > li a.btn {
    color: #fff !important;
    padding: 3px 20px !important;
}
</style>
<script>
$(function() { 

	$("#mytable #checkall").click(function () {
        if ($("#mytable #checkall").is(':checked')) {
            $("#mytable tr td  input.checkthis").each(function () {
                $(this).prop("checked", true); 
            });

        } else {
            $("#mytable tr td  input.checkthis").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
    
    $("[data-toggle=tooltip]").tooltip();
	var options = [];
 
	$( '.dropdown-menu a' ).on( 'click', function( event ) {

	   var $target = $( this ),
		   val = $target.attr( 'data-value' ),
		   $inp = $target.find( 'input' ),
		   idx;

	   if ( ( idx = options.indexOf( val ) ) > -1 ) {
		  options.splice( idx, 1 );
		  setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
	   } else {
		  options.push( val );
		  setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
	   }

	   // $( event.target ).blur();
		  
	   console.log( options );
	   return false;
	});
	
	//
	$('.select_users').on('click', function(event) {
		event.preventDefault();
		if( options.length ) {
			var $table = $('#mytable tbody');
			 
			$.each(options, function(i, v) {
				$('#mytable tbody tr:last').clone().appendTo($table) 
			})
			$('#user_container').slideDown(1000)
		}
		
	})
	
	$( '.dropdown-menu a' ).trigger('click')
	$( '.select_users' ).trigger('click')
	
	$("#user_container .table-responsive").slimscroll({
		height: "400px",
		alwaysVisible: true,
		color: '#0F0F1E',
		size: "6px",
		borderRadius: "4px"
	}).css("width", "100%");
	 
}) 
</script>
