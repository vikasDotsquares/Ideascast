<?php

if( isset($form_name) && !empty($form_name) ) {

		switch( $form_name ) {
			case 'open_project' :
			case 'project_reports' :
?>
<style>
ul.ui-autocomplete {
    list-style: outside none none;
    margin: 0;
    padding: 0;

}

ul.ui-autocomplete li{
    margin: 0 0 5px 0;
    padding: 0;
    position: relative;
    list-style: outside none none;
}
ul.ui-autocomplete > li > a {
	border-left: 3px solid rgba(0, 0, 0, 0);
	margin-right: 1px;
}
ul.ui-autocomplete > li > a {
	color: #333333;
	display: block;
	padding: 2px 0 2px 5px;
}
ul.ui-autocomplete > li > a:hover {
	background-color: #5F9323;
	color: #ffffff;
}
ul.ui-autocomplete li:first-child{
	border-top:none;
}
ul.ui-autocomplete li:last-child{
	border-bottom:none;
}

.input_error {
	border: 1px solid #DD4B39;
}
</style>


<?php
$this->Html->script('View/Projects/index', array('inline' => false));

	echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'index'), 'class' => 'form-bordered', 'id' => 'modelFormOpenProject')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Select Project</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<!-- <h5 class="project-name"> popup box heading </h5>	-->


		<div class="form-group">
			<label class=" " for="title">Project:</label>
			<?php
				// LOOP THROUGH ALL AREAS WITHIN THE A WORKSPACE OF THE SELECTED PROJECT
				if( isset($response['projects']) && !empty($response['projects']) ) {
					$projects = $response['projects'];
				?>
				
				<select class="form-control" name="project_id" id="project_id" >
					<option value="" selected>Select Project</option>
					<?php foreach( $projects as $k => $v ) { ?>
						<option value="<?php echo $k ?>"><?php echo $v ?></option>
					<?php }?>
				</select>
			<?php }?>
			
			
			<?php /*
				//echo $this->Form->input('title', array('class' => 'ui-autocomplete form-control', 'id' => 'autocomplete', "label" => false, "div" => false ));
			?>
				<!-- <ul class="ui-autocomplete" id="projects"> </ul>	-->
			<?php
				//echo $this->Form->input('project_id', array('type' => 'hidden', 'id' => 'project_id', "label" => false, "div" => false, 'value' => '' )); */
			?>
		</div> 
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<?php if( $form_name == 'open_project' ) { ?>
		 <button type="button" id="submit_choice" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', 'admin' => FALSE ), TRUE); ?>/index" class="btn btn-success">Open</button>
		<?php } else if( $form_name == 'project_reports' ) { ?>
		 <button type="button" id="submit_choice" data-remote="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'reports', 'admin' => FALSE ), TRUE); ?>" class="btn btn-warning">Show Report</button>
		<?php } ?>
		 
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function() {
	// console.clear();
	$('#smallModal').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});
	if( $("#autocomplete").length > 0 ) {
		$("#autocomplete").focus()
		$('#autocomplete').autocomplete({
			source:   function(request, response) {
				var sendData = []
				
				$.getJSON("<?php echo Router::Url(array('controller' => 'projects', 'action' => 'popup_filter.json', 'admin' => FALSE ), TRUE); ?>",
				{
					term: request.term
				},
				function(data) {
					if( data ) {

						$.each( data, function(i, v) {

							var ob = {
								label: i,
								value: v
							};
							sendData.push(v)
						})
						return response( $.map(data, function (item) {
							return {
								label: item.label,
								value: item.value
							}
						}))
					}
					else return false;
				});
			},
			response: function(event, ui) {
				var input = $(event.target)
				var div_proj = input.next("#projects:first")
				if( !$.isEmptyObject( ui.content ) ) {
					var $ul = $('ul.ui-autocomplete')
					$ul.empty()
					$.each(ui.content, function( i, v ) {
						var projID = v.value,
							projTitle = v.label,
							li = $("<li></li>"),
							a = $("<a>").html('<i class="fa fa-fw fa-check"></i>' + projTitle)
									.attr({
										href: "#",
										title: projTitle,
										id: projID
									})
									.addClass('clickable')
									.appendTo(li);
						li.appendTo($ul);
					})
					var li_len = $('li', $ul).length
					if( li_len > 0 ) {
						$ul.css({
								border: '1px solid #5F9323',
								'border-top': 'none',
						})
					}
					else {
						$ul.css({
							border: 'none'
						})
					}
				}
			}, 
			change: function( event, ui ) {
				console.log( $(event.target));
				console.log( ui);
				// if( .length > 0 ) console.log(request.term);
				// else  console.log("empty");
			}
		})

		$("#autocomplete").focus()

		// SET THE TITLE OF THE PROJECT INTO THE INPUT BOX
		// SET THE ID OF THE PROJECT INTO THE HIDDEN INPUT
		$('body').delegate("a.clickable", "click", function(e) {
			$("#autocomplete").val($(this).attr('title')).focus()
			$("#project_id").val($(this).attr('id'))
			$('ul.ui-autocomplete').empty()
		})
	}// end autocomplete element check
	
	// REDIRECT TO INDEX PAGE WITH THE SELECTED PROJECT ID
	$('body').delegate("#submit_choice", "click", function(e) {
		
		var $this = $(this)
		
		var loc = $this.data('remote')
		
		var $form = $this.closest('form')
		
		var $ele = $form.find('#project_id');
		
		var project_id = $ele.val();
		
		if( project_id != "" ) {
				
			$ele.removeClass('input_error')
			
			loc += '/' + project_id;
			
			window.location.replace(loc);
		}
		else { 
			$ele.addClass('input_error')
		}
	})


})
</script>
<?php break; // end select project form ?>

<?php case 'confirm_delete': ?>

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">Delete Confirm</h4>
		
	</div>
	
	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php echo $response['message']; ?>
	</div>
	
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		<a class="btn btn-danger btn-ok">Delete</a>
	</div>
	<script type="text/javascript" >
		$(function() {
			// console.clear();
			$('#confirm_delete').on('hidden.bs.modal', function () {
				$(this).removeData('bs.modal');
			});
			
			
		});
			
	</script>
<?php break; ?>
<?php
		default :
			echo "No form available for the choice.";
		break;
	}
} ?>
