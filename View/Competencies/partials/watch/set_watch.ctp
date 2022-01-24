<style>
	@media (min-width:768px) {
		.modal-dialog {
			width: 685px;
		}
		#add_watch_wrapper {
			display: none;
		}
	}
</style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 class="modal-title">Watch</h3>
</div>
<div class="modal-body">
	<div class="popup-watch-top-sec" id="add_watch_wrapper">
		<p>Add a watch to get notified when people gain new competencies based on your current filter.</p>
		<div class="add-watch-name">
			<label>Name</label>
			<div class="add-watch-filed">
				<div class="add-watch-input">
					<input type="text" name="watch_name" id="watch_name" class="form-control" placeholder="60 chars" maxlength="60">
				</div>
				<div class="add-btn-block">
					<input type="button" name="add_tag_btn" id="add_watch_btn" class="btn btn-success" disabled="disabled" value="Add">
				</div>
			</div>
		</div>
	</div>
	<div class="popup-watch-list-wrap">
		<div class="popup-watch-list-header">
			<div class="popup-watch-col popup-watch-col1">Name</div>
			<div class="popup-watch-col popup-watch-col2">Actions</div>
		</div>
		<div class="popup-watch-list-data"></div>
	</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" id="" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
	$(()=>{

		// IF A VALID SELECTION THAN ONLY SHOW THE ADD SECTION
		if(!$('#watch_showbtn').prop('disabled')){
			$("#add_watch_wrapper").show();
		}

		$("#watch_name").off('keyup').on('keyup', function(event) {
			$('#add_watch_btn').prop('disabled', true);
			if($(this).val().length > 0){
				$('#add_watch_btn').prop('disabled', false);
				if($(this).val().length > 60){
					$(this).val($(this).val().substring(0, 60));
				}
			}
		});

		;($.watch_list = function(){
			$.ajax({
	            url: $js_config.base_url + 'competencies/watch_list',
	            type: 'POST',
	            data: {},
	            success: function(response){
	                $(".popup-watch-list-data").html(response);
	            }
	        })
		})();

		$("#add_watch_btn").off('click').on('click', function(event) {
			event.preventDefault();
			var post_data = {};

      		$.watch_post().done(function(data){
      			post_data.selection = data;
      			post_data.watch_name = $("#watch_name").val();
      			$.ajax({
		            url: $js_config.base_url + 'competencies/set_watch',
		            type: 'POST',
		            dataType: 'json',
		            data: post_data,
		            success: function(response){
		                if(response){
		                	$("#watch_name").val("");
		                	$('#add_watch_btn').prop('disabled', true);
		                	$("#add_watch_wrapper").slideUp(200);
		                	$.watch_list();
		                }
		            }
		        })
      		});
		})
	})
</script>