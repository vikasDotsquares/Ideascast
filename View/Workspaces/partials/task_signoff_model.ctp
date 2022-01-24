
		<style type="text/css">
    #list_percentages, #list_impacts {
        font-size: 12px;
    }
    .dropdown-menu.inner {
        box-shadow: none !important;
    }
    .dropdown-menu.inner > li a {
        padding: 4px 10px !important;
    }
    .btn.dropdown-toggle.btn-default {
        background-color: #fff ;
    }

	.response-description{
		margin-bottom:10px;
	}

	.response-description label>strong{
		font-weight:550 !important;
	}

	.response-description input[type=file] {
		width: 100%;
		border: 1px #ccc solid;
	}
	textarea{
		margin-bottom:0 !important;
	}

	.evidencefile{
		color: #dd4b39;
		font-size: 11px;
	}
</style>
<form name="signofffrm" id="signofffrm" method="POST" enctype="multipart/form-data" >
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h3 class="modal-title " id="createModelLabel">Workspace Sign Off</h3>
</div>
<div class="modal-body">
    <div class="elements-list">
        <p>Provide a comment with any evidence you wish to upload.</p>
    </div>
    <div class="" id="impact_assessment">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
						<div class="response-description">
                            <label><strong>Comment:</strong></label>
							<input type="hidden" name="workspace_id" id="workspace_id" value="<?php echo $workspace_id;?>" >
							<input type="hidden" name="sign_off" id="sign_off" value="<?php echo $sign_off;?>" >

                            <textarea class="form-control" rows="4" placeholder="Max chars allowed 250" id="signoff_comment" name="signoff_comment" style="resize: vertical; min-height:90px;"></textarea>
                            <span class="error-message error text-danger"></span>
                        </div>
					</div>
					<div class="col-md-12">
						<div class="response-description">
							<label><strong>Evidence:</strong></label>
							<input type="file" name="task_evidence" id="task_evidence" >
							<span class="text-danger evidencefile">File upload limit 10Mb.</span>
						</div>
					</div>
                </div>
            </div>
        </div>

    <div class="loader_bar" id="loader_bar" style="display: none;"></div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="submit" class="btn btn-success submit_signoff" ><i class="bootstrap-dialog-button-icon glyphicon glyphicon-asterisk icon-spin fa-spin" style="display: none !important;float: left;margin: 3px 5px 0 0px !important;" ></i> Sign Off</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
</form>
<script type="text/javascript">
	$(function() {
		$('body').delegate('#signoff_comment', 'keyup focus', function(event){
			var characters = 250;

			event.preventDefault();
			var $error_el = $(this).next('.error');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate('#task_evidence', 'change', function(e){
			$(".evidencefile").text("File upload limit 10Mb.");
		})

		$('body').delegate('#signofffrm', 'submit', function(e){

			e.preventDefault();

			var form_data = new FormData();
			var signoff_comment = $.trim($("#signoff_comment").val());
			var workspace_id = $("#workspace_id").val();
			var sign_off = $("#sign_off").val();
			var file = $("#task_evidence")[0].files[0];
			if( file ){
				form_data.append('file', file, file.name);
				$(".submit_signoff").find('i').show();
			}
			form_data.append('signoff_comment', signoff_comment);
			form_data.append('workspace_id', workspace_id);

			form_data.append('data[Workspace][id]', workspace_id);
			form_data.append('data[Workspace][sign_off]', sign_off);


			if( signoff_comment.length <= 0 ){
				$(".error-message", $('#signofffrm')).show().text('A comment is required');
				$(".submit_signoff").find('i').hide();
			}

			if( signoff_comment.length > 0 ){

				$.ajax({
					url: $js_config.base_url+'workspaces/save_signoff_comment',
					type: "POST",
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,
					dataType: 'JSON',
					beforeSend:function(){
						$(".submit_signoff").prop("disabled", true);
					},
					success: function(response){
							if (response.success) {
                                if (response.content) {
                                    $.socket.emit('socket:notification', response.content.socket, function(userdata) {});
                                }
                                // location.reload();
                                $.signoff_btn = true;
                                $('#signoff_comment_box').modal('hide');
                                $.reload_wsp_progress().done(function(res){
			                    	$.adjust_resize();
			                    });
                            } else {
								$(".evidencefile").text(response.msg);
                                $(".submit_signoff").prop("disabled", false);
								$(".submit_signoff").find('i').hide();
                            }

					}
				})

			}

		})
	})
</script>
