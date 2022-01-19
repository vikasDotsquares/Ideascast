
<?php
	echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
	echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));

?>
<style>
	.lbl {
		margin-bottom: 10px; padding: 0px; text-align: left; font-weight: 600;
	}
	.center {
	    margin: 5px auto;
	    max-width: 100%;
	    width: 650px;
	}

	.add-user-heading .add-user-title{
	  font-size: 15px;
	}

	.btn-info { border-color: #d2d6de; }

	.input-group-addon.btn-filter, .input-group-addon.btn-times, .input-group-addon.btn-progress {
		color: #fff;
		cursor: pointer;
	}
	.input-group-addon.btn-progress {
		border-color: #00acd6 !important;
		background-color: #00c0ef !important;
		display: none;
	}
	.input-group-addon.btn-filter {
		border-color: #478008 !important;
		background-color: #67a028 !important;
	}
	.input-group-addon.btn-times {
		border-color: #c12f1d !important;
		background-color: #dd4b39  !important;
	}

</style>
<div class="add-user-heading" >
	<h4 class="add-user-title">Edit Group: <span class="group-name"><?php echo (isset($groupData['title']) && !empty($groupData['title'])) ? htmlentities($groupData['title'],ENT_QUOTES, "UTF-8") : 'Group'; ?></span></h4>
</div>
<div class="add-user-body clearfix">
		<?php if( isset($perm_users) && !empty($perm_users) ) {  ?>
		<input type="hidden" value="<?php echo implode(",", array_keys($perm_users)) ?>" name='userIds' id="userIds" />
		<?php } ?>

		<?php
		echo $this->Form->create('addUsersToGroup', array('url' => ['controller' => 'groups', 'action' => 'attach_users_with_group'], 'class' => 'frmAddUsersToGroup', 'id' => 'frmAddUsersToGroup'));
		?>
		<input type="hidden" value="<?php echo $group_id; ?>" name='group_id' id="group_id" />
		<input type="hidden" value="<?php echo $project_id; ?>" name='project_id' id="project_id" />
		<input type="hidden" name="selectedSkills" id="selectedSkills">

		<div class="col-sm-12 col-md-12 col-lg-12 mgbottom" style="">

			<label class="input-label create-group-title" style="">Group Title: </label>
			<div class="input-colm input-hidden col-sm-4 title-wrapper" style="padding-left: 0;">
				<div class="input-group">
                    <input type="text" class="form-control grp_title" name="grp_title" value="<?php echo htmlentities($groupData['title'],ENT_QUOTES, "UTF-8"); ?>" />
                    <span class="input-group-addon  clear_group_title tipText " title="Clear Group Title" style="cursor: pointer;    border-left: none;"><i class="inactivered"></i></span>
                    <span class="input-group-addon update_group_title tipText" title="Save Group Title" style=" cursor: pointer;"><i class="activegreen"></i></span>
                </div>
				<span class="error-message text-danger error"></span>
			</div>
		</div>

		<div class="col-sm-12 col-md-12 col-lg-12 mgbottom" style="display: none;">

				<label class="input-label create-group-title" style="">Skills: </label>
				<div class="input-colm input-hidden">
				<div class="input-group">
					<input type="text" name="skills" id="skills" class="form-control" />
					<span style="display:none" class="input-group-addon btn-filter" id="get_users"><span class="fa fa-user"></span></span>
					<span class="input-group-addon btn-times tipText" title="Clear Skills" id="clear_skills" ><span class="fa fa-times"></span></span>
				</div>

				<span class="error-message text-danger"></span>
			</div>
		</div>

		<div class="col-sm-12 col-md-9 col-lg-9" >
			<div class="form-group ">

				<label class="input-label create-group-title" style="">Add Users: </label>
				<div class="input-colm">
				<div class="button_full" style="display: none;">

				<?php if( isset($perm_users) && !empty($perm_users) ) { ?>
					<select id="user_list" class="user_list" multiple="multiple" style="width:100%;">
						<?php foreach($perm_users as $key => $value )  { ?>
							<option value="<?php echo $key; ?>"  > <?php echo $value; ?> </option>
						<?php } ?>
					</select>
				<?php }else{ ?>
					<select id="user_list" class="user_list" multiple="multiple" style="width:100%" disabled=""></select>
				<?php } ?>
				</div>
				<span class="error-message text-danger" style="display: block;"></span>
				</div>

			</div>
		</div>
		<div class="col-sm-12 col-md-3 col-lg-3" >
			<div class="form-group text-right">

				<a href="#" class="btn btn-success btn-sm btn_add_users <?php if( !isset($perm_users) || empty($perm_users) ) { ?> disabled <?php } ?> "   id="btn_add_users" >Save Users</a>
				<a class="btn btn-sm btn-danger close_add_user_panel">Cancel</a>
			</div>
		</div>
		<?php  echo $this->Form->end(); ?>

</div>

<script type="text/javascript" >
$(function(){
	$('.close_add_user_panel').on('click', function(event) {
		event.preventDefault();
		/* Act on the event */
		$(this).parents('.panel_add_users:first').slideUp()
	});


	/************** skill search **********************/
	var newtest = checkEnabled = 0;
	$("input#skills").tokenfield({
		autocomplete: {
			// source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
			source: function( request, response ) {
				var selectedSkills = $('#selectedSkills').val();
				var existingTokens = $('input#skills').tokenfield('getTokens');
				if( request.term == '') {
					$('.user_list').multiselect('enable');
					$("input#skills").parents('.input-group').next('span.error-message:first').html('');
					if(checkEnabled == 1) {
						$('#btn_add_users').attr('disabled', false);
					}
				} else {
					$('.user_list').multiselect('disable');
					$('#btn_add_users').attr('disabled', true);
				}

				if( request.term != '' && request.term.length > 2 ) {
					$.getJSON( $js_config.base_url + 'groups/get_skills', { term: request.term,selectedSkills: selectedSkills  }, function(response_data) {
						var items = [];
						if( response_data.success ) {
							if( response_data.content != null ) {
								checkEnabled = 1;
								$.each( response_data.content, function( key, val ) {
									var item ;
									item = {'label': val, 'value': key}
									items.push(item);
								});
								response(items)
							}
						} else if(response_data.success == false) {
							checkEnabled = 0;
							$('.user_list').multiselect('disable');
							$('ul.ui-autocomplete').css("display", "none");
							$('#btn_add_users').attr('disabled', true);
							//$('#get_users').trigger('click')
							$("input#skills").parents('.input-group').next('span.error-message:first').html('Does not exists');
						}
						// cl(items)
					} );
				}
			},
			focus: function( event, ui ) {
				// $( ".project" ).val( ui.item.label );
				// console.log(ui.item.label);
				return false;
			},
			delay: 100
		},
		// limit: 1,
		showAutocompleteOnFocus: true,
		allowEditing: false,
		delimiter: ''

	})
	.on('tokenfield:createtoken', function (event) {
		var existingTokens = $(this).tokenfield('getTokens');
		$.each(existingTokens, function(index, token) {
			if (token.value === event.attrs.value)
			event.preventDefault();
		});
		$('.user_list').multiselect('enable');
		$('#btn_add_users').attr('disabled', false);
	})
	.on('tokenfield:createdtoken tokenfield:removedtoken', function (event) {
		event.preventDefault()
		$('#get_users').trigger('click')
	})
	.on('tokenfield:createdtoken', function (event) {
		var selectedSkills = $('#selectedSkills').val();
		if(selectedSkills != '') {
			var selectedSkills = selectedSkills.split(',');
		} else {
			var selectedSkills = [];
		}
		selectedSkills.push(event.attrs.value);

		selectedSkills = selectedSkills.join(',');
		$('#selectedSkills').val(selectedSkills);
	})
	.on('tokenfield:removedtoken', function (event) {
		event.preventDefault();

		var selectedSkills = $('#selectedSkills').val();
		if(selectedSkills != '') {
			var selectedSkills = selectedSkills.split(',');
		} else {
			var selectedSkills = [];
		}
		selectedSkills.splice($.inArray(event.attrs.value, selectedSkills),1);

		selectedSkills = selectedSkills.join(',');
		$('#selectedSkills').val(selectedSkills);

		var existingTokens = $(this).tokenfield('getTokens');
		var tokenInput = $('.token-input').val();
		if(existingTokens.length == 0 && tokenInput == '') {
			$('.user_list').multiselect('enable');
			$('#btn_add_users').attr('disabled', false);
		}
		if(existingTokens.length == 0 && tokenInput != '') {
			$('.user_list').multiselect('disable');
			$('#btn_add_users').attr('disabled', true);
		}
	})

	// clear skills field on page load
	$('#skills').tokenfield('setTokens', []);

	// clear skills field on click of clear skills button
	$('body').delegate('#clear_skills', 'click', function(event){
		event.preventDefault()
		$('#skills').tokenfield('setTokens', []);
		$('.token-input').val('');
		$('#selectedSkills').val('');
		$('#get_users').trigger('click')
	})

	// Get users according to the skills entered
	$('body').delegate('#get_users', 'click', function(event){

		event.preventDefault();

		var tokens = $('#skills').tokenfield('getTokens');
		$('#progress_bar').css({'display': 'table-cell'})
		var titems = [],
		params = {}
		url = '';
		var project_id = '<?php echo $project_id; ?>',
		userIds = $('#userIds').val()
		if( tokens.length ) {
			$.each(tokens, function(key, data){
				titems.push(data.value)
			} )
			params = {'project_id': project_id, 'skills': titems, 'userIds': userIds};
			url = $js_config.base_url + "groups/get_users_by_skills/" + project_id;
		}
		else {
			url = $js_config.base_url + "groups/get_users/" + project_id
			params = { 'project_id': project_id }
		}

		var $parent_panel = $('.add-user-body')

		$('.multiselect.dropdown-toggle').parents('.button_full:first').next('span.error-message:first').html('');

		$.ajax({
			url: url,
			type: "POST",
			data: $.param(params),
			dataType: "JSON",
			global: false,
			success: function (response) {
				var multiselect_opt = {
					maxHeight: '400',
					buttonWidth: '100%',
					buttonClass: 'btn btn-info',
					checkboxName: 'data[ProjectGroupUsers][]',
					enableFiltering: true,
					filterBehavior: 'text',
					includeFilterClearBtn: true,
					enableCaseInsensitiveFiltering: true,
					// numberDisplayed: 3,
					includeSelectAllOption: true,
					includeSelectAllIfMoreThan: 5,
					selectAllText: ' Select all',
					nonSelectedText: 'Select Users',
					disabledText: 'Select Users',
					disableIfEmpty: true
				}
					$('.user_list').empty();
				if (response.success) {


					var selectValues = response.content;
					newtest = 1;checkEnabled = 1;
					var check = 0;

					if( selectValues != null ) {
						$('.user_list').append(function() {
							var output = '';

							$.each(selectValues, function(key, value) {
								output += '<option value="' + key + '">' + value + '</option>';
							});
							return output;
						});
						check = 1;
					}
					else {
						$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
						$(".user_selection").slideUp()
					}
					setTimeout(function(){

						$('.user_list').multiselect(multiselect_opt);

						$('.user_list').multiselect('rebuild') ;

						setTimeout(()=>{
							$('.button_full').show();
						},1200)

						var existingTokens = $('input#skills').tokenfield('getTokens');
						var tokenInput = $('.token-input').val();
						if(existingTokens.length == 0 && tokenInput == '') {
							$('.user_list').multiselect('enable');
							$('#btn_add_users').attr('disabled', false);
						}
						if(existingTokens.length == 0 && tokenInput != '') {
							$('.user_list').multiselect('disable');
							$('#btn_add_users').attr('disabled', true);
						}
						if(existingTokens.length > 0 && tokenInput != '') {
							$('.user_list').multiselect('disable');
							$('#btn_add_users').attr('disabled', true);
						}
						if(existingTokens.length > 0 && tokenInput == '') {
							$('.user_list').multiselect('enable');
							$('#btn_add_users').attr('disabled', false);
						}
						if(check == 1) {
							$('#btn_add_users').attr('disabled', false);
						}

					}, 100)
				}
				else {
					newtest = 0;checkEnabled = 0;
					setTimeout(function(){
						console.log($('.user_list',$parent_panel))
						$('.user_list',$parent_panel).multiselect(multiselect_opt);
						$('.user_list',$parent_panel).multiselect('disable')
					}, 100)

					$('.user_list').empty();
					$('.user_list').multiselect('rebuild') ;
					$('.user_list').multiselect('disable');
					$('#btn_add_users').attr('disabled', true);
					$('.multiselect-selected-text').html('No Users');

					//$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
					// $("#users_list").slideUp()
				}

			},// end success
			complete: function() {
				$('#progress_bar').hide()
			}

		})// end ajax

	})

	$('#get_users').trigger('click')

	$.fn.serializeObjects = function () {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (this.name.substr(-2) == "[]") {
				this.name = this.name.substr(0, this.name.length - 2);
				o[this.name] = [];
			}

			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};


	$('#popup_model_box').on('hidden.bs.modal', function(){
		$(this).removeData()
	})

})
</script>
