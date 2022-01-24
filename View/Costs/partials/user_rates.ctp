<?php
echo $this->Html->script('projects/plugins/validations/decimal', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<style type="text/css">
    .prj_com_pnone {
        pointer-events: none;
    }
</style>
<div class="modal-data">
<?php
    $project_status = $this->Permission->project_status($project_id);
    if(isset($project_status) && !empty($project_status)){
        $project_status = $project_status[0][0]['prj_status'];
    }

    $project_detail = getByDbId("Project", $project_id, ['id', 'title', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $currency_symbol = 'GBP';
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'name',  'sign']);
        $currency_detail = $currency_detail['Currency'];
        $currency_symbol = $currency_detail['sign'];
    }
    /*if($currency_symbol == 'USD') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#x24;</span>';
    }
    else if($currency_symbol == 'GBP') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#xa3;</span>';
    }
    else if($currency_symbol == 'EUR') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#x20AC;</span>';
    }
    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
        $currency_symbol = '<span  style="font-size: 12px;">Kr</span>';
    }*/

    /******************************************************************************************************************************/
    $users_list = [];
	$all_users = $this->Permission->project_user_list($project_id);
	if(isset($all_users) && !empty($all_users)){
		foreach ($all_users as $key => $value) {
			 $users_list[$value['user_details']['user_id']] = $value[0]['fullname'];
		}
	}
	function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b);
	    return strcasecmp($t1, $t2);
	}
	uasort($users_list, 'asrt');
	// pr($users_list);
	// $project_user_rates = $this->Permission->project_user_rates($project_id);
	// $current_org = $this->Permission->current_org();
 ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title annotationeleTitle" id="myModalLabel">Rate Cards</h4>
</div>
<div class="modal-body popup-select-icon ">
    <div class="rate-card-manager ">

		<div class="rate-card-sec  <?php if($project_status == 'completed'){ ?>prj_com_pnone<?php } ?>">
			<div class="rate-card-col1">
				<label>Set Rates For:</label>
				<?php echo $this->Form->input('users_list', array('type' => 'select', 'options' => $users_list, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'users_list', 'multiple' => 'multiple' )); ?>
				<span class="error"></span>
			</div>
			<div class="rate-card-col2">
				<label>Day Rate:</label>
				<input type="text" class="form-control" id="user_day_rate">
				<span class="error"></span>
			</div>

			<div class="rate-card-col3">
				<label>Hour Rate:</label>
				<input type="text" class="form-control" id="user_hour_rate">
				<span class="error"></span>
			</div>
			<div class="rate-card-col4">
				<button type="button" id="set_costs" class="btn btn-success">Set</button>

			</div>
		</div>

		<div class="col-rate-project" style="min-height: 322px;">

        </div>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <!-- <button type="button" id="submit_rates" class="btn btn-success disabled">Save</button> -->
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
</div>
<style type="text/css">
    .rate-card-manager .rate-data-entry{
        display: none;
    }
    .project-rates .user-name {
        padding: 5px 14px;
        background-color: #d6e9c6;
        display: block;
    }
    .project-rates .rates {
        padding: 0px 14px 4px 14px;
        display: block;
        font-size: 12px;
    }
    .rateday .clear-num-day {
        position: absolute;
        top: 4px;
        right: 20px;
        padding: 2px 3px;
        border-radius: 50%;
    }
    .rateday .clear-num-hour {
        position: absolute;
        top: 4px;
        right: 11px;
        padding: 2px 3px;
        border-radius: 50%;
    }
    .error {
	    color: #dd4b39;
	    font-size: 11px;
	}
    .e-border {
	    border-color: #dd4b39;
	}
</style>

<script type="text/javascript">
    $(function(){


        var project_id = '<?php echo $project_id; ?>';

        // $(".numeric").numeric({ decimal : ".",  negative : false, scale: 2 });
        function validateNumberInput(value){
            // return inputNumber.search(/^[0-9]{0,3}.?[0-9]{0,3}$/) == 0 ? true : false;
            return /^\d{1,10}(\.\d{1,2})?$/.test(value);
        }
        $('body').on('keyup focus blur', '.urates', function(e){
            // if($(this).val() == "" || $(this).val() === undefined) return;
            // if($('.urates').not($(this)).val() == "" || $('.urates').not($(this)).val() === undefined) return;
            var $this = $(this);
            $('.rate-card-manager .error').text('');
            $('#submit_rates').removeClass('disabled');

            $('.urates').each(function(index, el) {
                if($(this).val() != "" && $(this).val() !== undefined) {
                    val = $.comma_2_numeric($(this).val())
                    if(!validateNumberInput(val)) {
                        $('.rate-card-manager .error').text('Correct format is 10,2');
                        $('#submit_rates').addClass('disabled');
                    }
                }
            });
        })

        $('body').on('click', '.user-rates', function(event){
            event.preventDefault();
            var $this = $(this),
                data = $this.data(),
                userid = data.userid,
                user_name = data.name,
                day_rate = data.day,
                hour_rate = data.hour,
                $dayrate = $('#dayrate'),
                $hourrate = $('#hourrate');

            $('.rate-card-manager .rate-data-entry').slideDown(100);
            $('.user-rates i.text-red').hide();
            $('i.text-red', $this).show();
            $('#submit_rates').removeClass('disabled');
            $('.error-message').html('')

            $('#userid').val(userid);
            $('#rate_card_name').text(user_name);
            $dayrate.val(day_rate);
            $hourrate.val(hour_rate);
        })
        $('.user-rates').popover({
            placement: "bottom",
            container: 'body',
            trigger: 'hover',
            html: true,
        })
        .on('show.bs.popover', function(){
            var data = $(this).data('bs.popover'),
                $tip = data.$tip,
                $content = $tip.find('.popover-content');

            $tip.css('min-width','180px');
            $content.css('padding','0');
        })

        $('body').on('click', '#submit_rates', function(event){
            if($('#dayrate').val() == '' && $('#hourrate').val() == ''){
                $('.error-message').html('Please enter either day or hour or both rates.');
                return;
            }
            if($('#userid').val() == ''){
                $('.error-message').html('Please select a user first.');
                return;
            }
            var dayrate = $.comma_2_numeric($('#dayrate').val()),
                hourrate = $.comma_2_numeric($('#hourrate').val())
            $.ajax({
                global: false,
                url: $js_config.base_url + 'costs/save_user_rates',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    project_id: project_id,
                    user_id: $('#userid').val(),
                    day_rate: dayrate,
                    hour_rate: hourrate
                },
                success: function(response) {
                    if(response.success){
                        // $('#model_bx').modal('hide');
                        $( ".modal-data" ).load( $js_config.base_url + "costs/get_user_rates/" + project_id, function(data) {
                            console.log( "Load was performed.", data );

                        });
                    }
                }
            })
        })

        $('body').on('click', '.clear-num', function(event){
            var $parent = $(this).parent('.rateday'),
                $input = $parent.find('input'),
                data = {
                    project_id: project_id,
                    user_id: $('#userid').val(),
                };

            if($('#userid').val() == ''){
                return;
            }
            $input.val('');

            if($(this).hasClass('clear-num-day')){
                data['clear_rate'] = 'day';
            }
            else if($(this).hasClass('clear-num-hour')){
                data['clear_rate'] = 'hour';
            }

            $.ajax({
                global: false,
                url: $js_config.base_url + 'costs/clear_user_rates',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: function(response) {
                    if(response.success){

                    }
                }
            })
        })
    })

	$(()=>{
		var project_id = '<?php echo $project_id; ?>';

		$users_list = $('#users_list').multiselect({
	        enableUserIcon: false,
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'users[]',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search Members',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Team Members',
            onSelectAll:function(){
            	$('#users_list').parent().find('.error').html('');
			},
			onDeselectAll:function(){
                $('#users_list').parent().find('.error').html('');
			},
	        onChange: function(element, checked) {
            	$('#users_list').parent().find('.error').html('');
            }
	    });

		$('#user_day_rate,#user_hour_rate').off('keyup').on('keyup', function(event) {
			$('#user_day_rate,#user_hour_rate').removeClass('e-border');
		})

		;($.user_rate_list = ()=>{
			var dfd = new $.Deferred();
			$( ".col-rate-project" ).load( $js_config.base_url + "costs/user_rate_list/" + project_id, function(data) {
                $(".cost-data-list").slimScroll({width: '100%', height: 286, alwaysVisible: true});
                dfd.resolve()
            });
            return dfd.promise();
		})();

		$('#set_costs').off('click').on('click', function(event) {
			event.preventDefault();
			var $this = $(this),
				$users_lists = $('#users_list'),
				$user_day_rate = $('#user_day_rate'),
				$user_hour_rate = $('#user_hour_rate'),
				users = $users_lists.val() || [],
				day_rate = $.comma_2_numeric($user_day_rate.val()),
				hour_rate = $.comma_2_numeric($user_hour_rate.val());
			var error = false;
			$('.error').html('');
			$('#user_day_rate,#user_hour_rate').removeClass('e-border');

			if(users.length <= 0){
				error = true;
				$users_lists.parent().find('.error').html('Team Members, Day Rate and/or Hour Rate are required');
			}
			if((day_rate == 0 || day_rate === undefined || day_rate == '') && (hour_rate == 0 || hour_rate === undefined || hour_rate == '')){
				error = true;
				$('#user_day_rate,#user_hour_rate').addClass('e-border');
			}
			if(error){
				return;
			}
			$this.addClass('disabled');
			$.ajax({
				url: $js_config.base_url + 'costs/save_user_rates',
				type: 'POST',
				dataType: 'json',
				data: {project_id: project_id, users: users, day_rate: day_rate, hour_rate: hour_rate},
				success: function(response) {
					if(response.success){
						$.user_rate_list().done(() => {
							$users_lists.val([]).multiselect('refresh');
							$user_day_rate.val('');
							$user_hour_rate.val('');
							$this.removeClass('disabled');
						});
					}
				}
			});

		});

	    $(".cost-data-list").slimScroll({width: '100%', height: 286, alwaysVisible: true});

	})
</script>