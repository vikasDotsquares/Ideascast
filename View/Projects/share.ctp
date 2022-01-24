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
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<?php if( isset($project_detail )) { //pr($this->data); ?>
					<p class="text-muted date-time">Project: 
						<span>Created: <?php 
						//echo date('d M Y h:i:s', $project_detail['Project']['created']); 
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s'); 
						?></span>
						<span>Updated: <?php 
						//echo date('d M Y h:i:s', $project_detail['Project']['modified']); 
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['modified']),$format = 'd M Y h:i:s'); 
						?></span>
					</p>
					<?php } ?>
				</h1>
				 
			</section>
		</div>

 
    <div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12"> 
                    <div class="box border-top margin-top">
                        <div class="box-header" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX --> 
							 
                        </div>
						<div class="box-body clearfix list-acknowledge" style="">
							
							
<div class="container">

<?php  
echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'manage_project'),  'role' => 'form', 'id' => 'frm_manage_project', 'class' => 'clearfix')); 
?> 
		<div class="form-group col-md-12">
		
			<label for="ProjectCategoryId" class="col-md-2 text-right">User:</label>
			<div class="col-md-7">  
				<?php 
				// pr($this->data, 1);
				echo $this->Form->input('User.id', array(
					'options' => $users_list,
					'empty' => 'Select User',
					'type'=>'select',
					// 'default'=> (isset($this->data['Project']['category_id'])) ? $this->data['Project']['category_id'] : 0,
					'label' => false,
					'div' => false,
					'class' => 'form-control'
				)); ?>
				<span class="error" ></span>
				<?php echo $this->Form->error('user.id'); ?>
			</div>
			
		</div>
<?php echo $this->Form->end(); ?>

	<div class="row">
		<div class="col-lg-12">
			
		</div>
		
		<div class="container" id="user_container" style="display: block;">
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
											
										<!-- <div class="checkbox-family">
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
										</div>-->
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
<script type="text/javascript" >
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
