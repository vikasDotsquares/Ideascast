
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Project Links</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body  popup-select-icon project-document-popup clearfix">
		<div class="common-tab-sec view-skills-tab">
	        <ul class="nav nav-tabs tab-list" id="doc_tabs">
                <li class="active"> <a data-toggle="tab" class="active slevels" href="#projlinks" aria-expanded="true">Links</a> </li>
	            <li class="shistoryMain"> <a data-toggle="tab" class="shistory" href="#projaddlink" aria-expanded="true">Add Link</a> </li>
	        </ul>
	        <div class="tab-content">
	            <div id="projlinks" class="tab-pane fade active in">
	                <div class="projdocuments-list"></div>
	            </div>
	            <div id="projaddlink" class="tab-pane fade">
	            	<?php echo $this->Form->create('ProjectLink', array('class' => 'form-bordered', 'id' => 'link_add', 'url'=>'add_link' )); ?>
	            	<?php echo $this->Form->input('ProjectLink.project_id', array('type' => 'hidden', 'value' => $project_id)); ?>
					<div class="row">
						<div class="form-group">
						  	<label for="" class="col-lg-2 control-label">Link:</label>
						  	<div class="col-lg-10">
							  	<input class="form-control" type="text" id="link_url" name="data[ProjectLink][url]" autocomplete="off" >
	                            <span class="error text-red"></span>
						  	</div>
						</div>
						<div class="form-group">
	                        <label for="" class="col-lg-2 control-label">Title: </label>
	                        <div class="col-lg-10">
	                            <input class="form-control" placeholder="50 characters" type="text" id="link_title" name="data[ProjectLink][title]" autocomplete="off" maxlength="50">
	                            <span class="error text-red"></span>
	                        </div>
	                    </div>
						<div class="form-group">
	                        <label for="" class="col-lg-2 control-label">Summary: </label>
	                        <div class="col-lg-10">
	                            <input class="form-control" placeholder="50 characters" type="text" id="link_summary" name="data[ProjectLink][summary]" autocomplete="off" maxlength="50">
	                            <span class="error text-red"></span>
	                        </div>
	                    </div>

						<div class="form-group visiblesharers-field">
	                        <label for="" class="col-lg-2 control-label"> </label>
	                        <div class="col-lg-4">
	                            <input type="checkbox" value="1" id="visible_sharers"> <label for="visible_sharers">Visible to Sharers</label>
	                        </div>
	                        <div class="col-lg-6">
	                            <input type="checkbox" value="1" id="open_in_tab"> <label for="open_in_tab">Open in new tab</label>
	                        </div>
	                    </div>
					</div>
					<?php echo $this->Form->end(); ?>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-link" style="display: none;">Add</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

<script type="text/javascript">
	$(function(){
		$(".projdocuments-list").slimScroll({height: 244, alwaysVisible: true, width: '100%'});

		$("#doc_tabs").on('shown.bs.tab', function (e) {
            if($(this).find('li.active a').is('.shistory')){
            	$(".submit-link").show();
            }
            else{
            	$(".submit-link").hide();
            }
		})

		var project_id = '<?php echo $project_id; ?>';

		($.link_list = function(){
			$.ajax({
				url: $js_config.base_url + 'projects/project_link_list/' + project_id,
				type: 'POST',
				data: {},
				success:function(response){
					$('.projdocuments-list').html(response);
					$('.projdocuments-list-right').removeClass('stopped');
				}
			})
		})();

		$('#link_url, #link_title, #link_summary').off('keyup').on('keyup', function(event) {
			$(this).parent().find('.error').html('');
		})

		$.isValidUrl = function(url){

			var myVariable = url;

			if( /^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/.test(myVariable) ) {
			  return 1;
			} else {
			  return -1;
			}
		}

		$('.submit-link').off('click').on('click', function(event) {
			event.preventDefault();
			$('.error').html('');

			var error = false;
			if($("#link_url").val() == '' || $("#link_url").val() === undefined){
				$("#link_url").parent().find('.error').text('Link is required');
				error = true;
			}

			if( $.trim($("#link_url").val()) != '' && $.isValidUrl($.trim($("#link_url").val())) == -1 ){
				$("#link_url").parent().find('.error').text('Missing a protocol, hostname or filename')
				error = true;
			}

			if($('#link_title').val() == '' || $('#link_title').val() === undefined){
				$('#link_title').parent().find('.error').html('Title is required');
				error = true;
			}
			if($('#link_summary').val() == '' || $('#link_summary').val() === undefined){
				$('#link_summary').parent().find('.error').html('Summary is required');
				error = true;
			}

			if(error) return;

			var wlink = $("#link_url").val();
			var input_link = wlink;
			if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
			}

			$(this).prop('disabled', true);
			var data = {
				project_id: $('#ProjectLinkProjectId').val(),
				url: input_link,
				title: $('#link_title').val(),
				summary: $('#link_summary').val(),
				is_sharers: ($('#visible_sharers').prop('checked')) ? 1 : 0 ,
				open_in_tab: ($('#open_in_tab').prop('checked')) ? 1 : 0 ,
			}

			$.ajax({
				url: $js_config.base_url + 'projects/add_project_link',
				type:'POST',
				dataType:'json',
				data: data,
				success:function(response){
					if(response.success){
						$.link_added = true;
						$.link_list();
						$(".submit-link").prop('disabled', false);
						$('#link_url, #link_title, #link_summary').val('');
						$('#visible_sharers').prop('checked', false);
						$('#open_in_tab').prop('checked', false);
						$('#doc_tabs a.slevels').tab('show');
					}
				}
			})
		});

	})
</script>