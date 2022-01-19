<?php
$com_hours = $rem_hours = $ef_comm = '';
if(isset($ef_data) && !empty($ef_data)){
	$ef_data = $ef_data[0]['element_efforts'];
	$com_hours = $ef_data['completed_hours'];
	$rem_hours = $ef_data['remaining_hours'];
	$ef_comm = $ef_data['comment'];


}
$esignoff = (isset($el_signoff) && !empty($el_signoff)) ? true : false;?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Effort</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body clearfix confidence-popup">
    <div class="common-tab-sec view-skills-tab">
        <ul class="nav nav-tabs tab-list" id="effort_tab">
        	<?php if(!$esignoff){ ?>
            <li class="<?php if(!$esignoff){ ?>active<?php } ?>"> <a data-toggle="tab" class="slevels <?php if($esignoff){ ?>disabled<?php } ?>" href="#set_effort" aria-expanded="true">Set Effort</a> </li>
            <?php } ?>
            <li class="shistoryMain <?php if($esignoff){ ?>active<?php } ?>"> <a data-toggle="tab" class="shistory" href="#history" aria-expanded="true">History</a> </li>
        </ul>
        <div class="tab-content">
            <div id="set_effort" class="tab-pane fade <?php if(!$esignoff){ ?>active in<?php } ?>">
                <div class="row">
                    <div class="form-groups"><div class="col-sm-12"><span class="same-error"></span></div></div>
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-2 control-label">Completed: </label>
                        <div class="col-lg-10 effortlevelsec">
							<input class="form-control <?php if(!isset($hcount) || empty($hcount)){ ?> disabled <?php } ?>" value="<?php echo $com_hours; ?>" placeholder="0" type="number" name="chours" id="chours" max="10000" min="0"> Hours
							<span class="error text-red"></span>
                        </div>
                    </div>
					<div class="form-group">
                        <label for="UserUser" class="col-lg-2 control-label">Remaining: </label>
                        <div class="col-lg-10 effortlevelsec">
							<input class="form-control" placeholder="0" type="number" name="rhours" id="rhours" max="10000" min="0" value="<?php echo $rem_hours; ?>" > Hours
							<span class="error text-red"></span>
                        </div>
                    </div>
					<div class="form-group">
                        <label for="UserUser" class="col-lg-2 control-label">Comment: </label>
                        <div class="col-lg-10">
							<input class="form-control" maxlength="50" placeholder="50 characters" type="text" name="comment" id="comment" value="<?php //echo $ef_comm; ?>" autocomplete="off">
							<span class="error text-red"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="history" class="tab-pane fade <?php if($esignoff){ ?>active in<?php } ?>">
				<div class="effort-history-wrap"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer confidencefooter">
	<?php if(!$esignoff){ ?>
    <a class="btn btn-success save_effort" >Set</a>
    <?php } ?>
    <a class="btn btn-danger " data-dismiss="modal">Close</a>
</div>
<style type="text/css">
    .error, .same-error {
        color: #dd4b39;
        font-size: 11px;
    }
</style>

<script type="text/javascript">
	$(()=>{
		$("#effort_tab").on('shown.bs.tab', function (e) {
            if($(this).find('li.active a').is('.shistory')){
            	$(".save_effort").hide();
            }
            else{
            	$(".save_effort").show();
            }
		})

		var task_id = '<?php echo $element_id ?>';
		var user_id = $.effort_user = '<?php echo $user_id ?>';
		$.cval_disabled = '<?php if(!isset($hcount) || empty($hcount)){  echo true; } ?>';

		$.data_changed = false;
		$('#chours, #rhours').off('keyup keydown change').on('keyup keydown change', function(event) {
			$.data_changed = true;
		});

		$('#chours, #rhours').off('blur').on('blur', function(event) {
			var val = $(this).val();
			if(val == '' || val === undefined){
				$(this).val(0)
			}
			var $this = $(this);
		    var minlength = $this.attr('min');
		    if (val && parseInt(val, 10) < parseInt(minlength, 10) ) {
		      	$this.val(parseInt(minlength, 10));
		    }
		    var maxlength = $this.attr('max');
		    if (val && parseInt(val, 10) > parseInt(maxlength, 10)) {
		      	$this.val(parseInt(maxlength, 10));
		    }
		});

		($.effort_history = function(){
			var dfd = new $.Deferred();
			$.ajax({
				url: $js_config.base_url + 'entities/effort_history',
				type: 'POST',
				data: {task_id: task_id, user_id: user_id},
				success:function(response){
					$('.effort-history-wrap').html(response);
					dfd.resolve();
				}
			})
			return dfd.promise();
		})();

		$('.save_effort').off('click').on('click', function(event) {
			var $this = $(this),
				cval = $('#chours').val(),
				rval = $('#rhours').val(),
				comment = $('#comment').val(),
				error = false;

			$('.error, .same-error').html('');

			if($.cval_disabled && (rval == '' || rval <= 0 || rval === undefined)){
				$('#rhours').parent().find('.error').html('Remaining Hours are required');
				error = true;
			}
			else if(!$.cval_disabled && (rval == '' || rval <= 0 || rval === undefined) && (cval == '' || cval <= 0 || cval === undefined)){
				if(rval == '' || rval <= 0 || rval === undefined){
					$('.same-error').html('At least one value must be entered.');
					error = true;
				}
				else{
					$('.same-error').html('At least one value must be entered');
					error = true;
					cval = 0;
				}
			}

			if(comment == '' || comment === undefined){
				$('#comment').parent().find('.error').html('Comment is required');
				error = true;
			}

			if(error){
				error = false;
				return;
			}
			$.data_changed = false;

			$(this).addClass('disabled');

			var data = {task_id: task_id, user_id: user_id, completed_hours: cval, remaining_hours: rval, comment: comment};

			$.ajax({
				url: $js_config.base_url + 'entities/save_effort',
				type: 'POST',
				data: data,
				dataType: 'json',
				success:function(response){
					$this.removeClass('disabled');
					if(response.success){
						$.effort_history().done(function(){
							$('#effort_tab a.shistory').tab('show');
						});
						$.save_effort = true;
						$('#chours').removeClass('disabled');
						$.cval_disabled = false;
						$('#rhours,#chours,#comment').val('');

					}
					else{
						$('.same-error').html(response.msg);
					}
				}
			})
		});

	})
</script>


