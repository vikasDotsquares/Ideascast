<?php
$allProjectUsers = [];
if(isset($project_id) && !empty($project_id)) {
	$projects = [$project_id];
	foreach ($projects as $key => $p_id) {

		$projectUsers = $this->TaskCenter->userByProject($p_id);
		if (isset($projectUsers) && !empty($projectUsers)) {
			$allProjectUsers = array_merge($allProjectUsers, $projectUsers['all_project_user']);
		}
	}
	if(isset($allProjectUsers) && !empty($allProjectUsers)) {
		$allUsers = $this->TaskCenter->user_exists($allProjectUsers);

		$allUsersList = $this->Common->usersFullname($allUsers);
		$allProjectUsers = Set::combine($allUsersList, '/UserDetail/user_id', '/UserDetail/full_name');
	}
}
asort($allProjectUsers);
$usersList = [];
if(isset($allProjectUsers) && !empty($allProjectUsers)) {
	foreach ($allProjectUsers as $key => $value) {
		if(user_table_opt_status($key)) {
			$usersList[$key] = $value;
		}
	}
}
asort($usersList);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Project OV Achieved</h3>
</div>
<div class="modal-body clearfix">
    <input type="hidden" name="project_ov_user_id" id="project_ov_user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="project_ov_project_id" id="project_ov_project_id" value="<?php echo $project_id; ?>" />
	<div class="col-sm-6 nopadding-left" style="min-height: 34px;">
	   	<div class="project-ov-input-wrap">
	  		<div class="input-group1 project-ov-input-group">
	  			<?php
		            echo $this->Form->input('sel_project_ov_types', array(
		                'options' => $usersList,
		                'empty' => 'Select Member',
		                'class' => 'form-control sel-project-ov-types',
		                'id' => 'sel_project_ov_types',
		                'label' => false,
		                'div' => false,
		                'onfocus="this.size=8" onblur="this.size=1" onchange="this.size=1; this.blur();" style="position: absolute;"'
		            ));
	            ?>
			</div>
		</div>
	</div>


	<div class="ov-project-list project-ov-data">
		<div class="loading-bar"></div>
	</div>

</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
    $(function(){
    	var pdata = {
    		user_id: $('#project_ov_user_id').val(),
    		project_id: $('#project_ov_project_id').val(),
    	};

    	($.get_project_ov_data = function(data) {
	        var dfd = new $.Deferred();
	        $('.project-ov-data').html('<div class="loading-bar"></div>')
	        $.ajax({
	            url: $.url + 'rewards/get_project_ov_data',
	            type: 'POST',
	            dataType: 'json',
	            data: data,
	            success: function(response) {
	                $('.project-ov-data').html(response);
	                dfd.resolve('done');
	            }
	        });
	        return dfd.promise();
	    })(pdata);

/*	    $('#sel_project_ov_types').change(function(event) {
	    	var type = $(this).val();

    		var post_data = {
    			user_id: $('#project_ov_user_id').val(),
				project_id: $('#project_ov_project_id').val(),
				type: type,
    		}
    		$.get_project_ov_data(post_data);
	    });*/

	    $('#sel_project_ov_types').change(function(event) {
	        event.preventDefault();
	        $('.ov-list').hide();
	        $('.ov-project-list .panel').show();
	        var target = $(this).val();
	        var delay = 0;
	        var show_time = 200;

        	if(target != '' && target != undefined) {
	            $('.ov-list[data-user="' + target + '"]').each(function(index, el) {
	                $(el).fadeIn(delay);
	                delay += show_time;
	            });
	        }
	        else{
	        	$('.ov-list').each(function(index, el) {
	                $(el).fadeIn(delay);
	                delay += show_time;
	            });
	        }

	        $('.ov-project-list .panel').each(function(index, el) {
	        	if($(this).find('.ov-list:visible').length <= 0) {
	        		$(this).hide();
	        	}
	        });
	    })

	    $('.project-ov-clear-type').click(function(event) {
	    	if($('#sel_project_ov_types').val() != ''){
		    	$('#sel_project_ov_types option[value=""]').prop('selected', true);
		    	$.get_project_ov_data(pdata);
		    }
	    });

    })// END document ready
</script>



<style>

    .info-msg {
        color: #bbbbbb;
        font-size: 20px;
        text-align: center;
        text-transform: uppercase;
        width: 100%;
        display: block;
    }
    .project-ov-clear-type {
    	cursor: pointer;
    }
	.charity-info {
		display: inline-block;
		width: 100%;
		padding-bottom: 5px;
	}
	.project-ov-input-wrap {
		max-width: 100%;
	}

	.project-ov-input-wrap .input-group-addon {
    padding: 2px 10px;
	}
	.project-ov-input-wrap .form-control{
		height: auto;
		min-height: 30px;
		padding-top: 0px;
		padding-bottom: 0px;
	}

	.ov-project-list {
		display: inline-block;
		width: 100%;
		max-height: 465px;
		overflow: auto;
	}
	.ov-project-list .panel{
		margin-top: 10px;
	}
	.ov-project-list .panel .panel-heading {
	    padding: 10px;
	}
	.ov-project-list .panel	.panel-body{
		padding: 10px;
	}
	.ov-list-member {
		display: inline-block;
		width: 100%;
		white-space: nowrap;
overflow: auto;
	}
	.ov-list-member ul{
		padding: 0px;margin: 0px;
	}
	.ov-list-member ul li{
	display: inline-block;
	width: 50px;
		margin-right: 3px;
	}

.ov-list-member ul li .ov-list-img {
    border: 2px solid #ccc;
    display: inline-block;
    border-radius: 50%;
    overflow: hidden;
	width: 40px;
	height: 40px;
}

	.ov-list-member ul li .count{
		color: #95c043;
		font-weight: bold;
		font-size: 13px;
		line-height: normal;
		display: block;

	}


</style>