<style>
	.lbl {
		margin-bottom: 10px; padding: 0px; text-align: left; font-weight: 600;
	}
	.lbl_prj {
		margin: 5px; padding: 0px; text-align: right; font-weight: 600;
	}

	.modal.fade .modal-dialog {
		-webkit-transform: scale(0);
		-moz-transform: scale(0);
		-ms-transform: scale(0);
		transform: scale(0);
		top: 300px;
		opacity: 0;
		-webkit-transition: all 0s;
		-moz-transition: all 0s;
		transition: all 0s;
	}

	.modal.fade.in .modal-dialog {
		-webkit-transform: scale(0);
		-moz-transform: scale(0);
		-ms-transform: scale(0);
		transform: scale(0);
		-webkit-transform: translate3d(0, -300px, 0);
		transform: translate3d(0, -300px, 0);
		opacity: 1;
	}
	.tokenfield.form-control {
	/* 	width: 96%; float: left; */
	}
	#submit_group, #users_list {
		/* 	display: none; */
	}

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
	.multiselect-container.dropdown-menu > li:not(.multiselect-group):not(.multiselect-item.filter) {
		margin-top: -5px;
	}

	/* .custom_span span:first-child{
		width:84% !important;
	} */

	#user_text_search span:first-child, #user_tag_search span:first-child, #user_skill_search span:first-child, #user_subject_search span:first-child, #user_domain_search span:first-child {
		width:84% !important;
	}

	@media screen and (max-width:1199px) {

	.control-label.col-sm-2.text-normal { padding:7px 0; }
	}

	@media screen and (max-width:992px) {

	.form-horizontal .form-group.mrg-btm { margin: 0; padding: 0; margin-bottom:10px; }
	.button_full.last-dropdown {
		width: 323px;
	}
	}

	@media screen and (max-width:767px) {

	.control-label.col-sm-2.text-normal { padding: 5px 16px;}

	}

	.p-select-box {
		display:inline-block;
		width:300px;
		padding-bottom:5px;
	}


	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
	    padding: 5px 20px 5px 40px !important;
	}
    .send-group-tabs-nav {
        padding-bottom: 15px;
    }
    .multiselect-container.dropdown-menu li .multiselect-clear-filter{
		background: #fff;
	}
    .multiselect-container.dropdown-menu li .multiselect-clear-filter:hover{
		background: #f5f5f5;
        border-color: #ddd;
	}
	.send-group-tabs-nav .nav-tabs {
	    border-bottom: none;
	    vertical-align: top;
	}
	 .send-group-tabs-nav .nav-tabs>li {
	    margin-bottom: 0;
	     margin-right: 10px;
	     border-right: 2px solid #67a028;
	     padding-right: 10px;
	}
	 .send-group-tabs-nav .nav-tabs>li:last-child {
	     margin-right: 0;
	     border-right: none;
	     padding-right: 0;
	}
	.send-group-tabs-nav .nav-tabs>li a{
	    padding:4px 0;
	    margin-right: 0;
	    border: none;
	    font-weight: 600;
	}
	 .send-group-tabs-nav .nav-tabs>li.active>a, .send-group-tabs-nav .nav-tabs>li.active>a:focus, .send-group-tabs-nav .nav-tabs>li.active>a:hover {
	    color: #444;
	    border: none;
	     background: #fff;
	}
	.send-group-tabs-nav .nav-tabs>li>a:hover {
	    background: #fff;
	    color: #444;
	}

   .send-group-pop-tabs {
    /*padding-bottom: 15px;*/
    min-height: 50px;
	}
	.skills-tags-info {
	    min-height: 34px;
	    margin-bottom: 25px;
	    margin-top: 0;
	    padding-top: 10px;
	    color: #444;
	        font-weight: 400;
	}
    .group-pop-group{
        position: relative;
    }
	 .checkbox-group {
	    position: absolute;
	    right: 15px;
	    bottom: -20px;
	     margin: 0;
	}
	.checkbox-group label.fancy_label {
	    background-image: none;
	    padding-left: 0 !important;
	    font-size: 13px;
	}
	.checkbox-group label.fancy_label {
	    height: 14px;
	    line-height: 14px;
        margin: 0;
	}
	.checkbox-group .fancy_label {
	    position: relative;
	    top: -2px;
	}

</style>
<?php
echo $this->Html->css('projects/bs-selectbox/bs.selectbox');
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->css('projects/tokenfield/tokenfield-typeahead');
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->css('projects/multi-select');
// echo $this->Html->script('projects/plugins/select-multiple', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-selectbox', array('inline' => true));
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));
echo $this->Html->script('projects/multi-select', array('inline' => true));
?>

<script type="text/javascript">
$(function() {

	function cl( term ) {
		console.log(term);
    }
	function extractLast( term ) {
		return split( term ).pop();
    }
	var newtest = checkEnabled = 0;
	$("input#skills").tokenfield({
		autocomplete: {
			// source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
			source: function( request, response ) {
				var selectedSkills = $('#selectedSkills').val();
				var existingTokens = $('input#skills').tokenfield('getTokens');
				if( request.term == '') {
					$('#users').multiselect('enable');
					$("input#skills").parents('.input-group').next('span.error-message:first').html('');
					//if(checkEnabled == 1) {
						$('#create_group').attr('disabled', false);
					//}
				} else {
					$('#users').multiselect('disable');
					$('#create_group').attr('disabled', true);
				}

				if( request.term != '' && request.term.length > 2 ) {
					$.getJSON( $js_config.base_url + 'groups/get_skills', { term: request.term, selectedSkills: selectedSkills  }, function(response_data) {
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
							$('#users').multiselect('disable');
							$('ul.ui-autocomplete').css("display", "none");
							$('#create_group').attr('disabled', true);
							//$('#get_users').trigger('click')
							$("input#skills").parents('.input-group').next('span.error-message:first').html('Does not exists');
						}
						// cl(items)
					} );
				}
			},
			focus: function( event, ui ) {
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
		$('#users').multiselect('enable');
		$('#create_group').attr('disabled', false);
	})
	.on('tokenfield:createdtoken tokenfield:removedtoken', function (event) {
		event.preventDefault();
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
			$('#users').multiselect('enable');
			$('#create_group').attr('disabled', false);
		}
		if(existingTokens.length == 0 && tokenInput != '') {
			$('#users').multiselect('disable');
			$('#create_group').attr('disabled', true);
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
		$('#create_group').attr('disabled', true);

		var tokens = $('#skills').tokenfield('getTokens');
		$('#progress_bar').css({'display': 'table-cell'})
		var titems = [],
			params = {}
			url = '';
		var project_id = $('[name="data[Group][project_id]"]').val(),
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

		$('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html('');

		$.ajax({
			url: url,
			type: "POST",
			data: $.param(params),
			dataType: "JSON",
			global: false,
			success: function (response) {

				if (response.success) {

					var selectValues = response.content;
					newtest = 1;checkEnabled = 1;

					$('#users').empty();
					var check = 0;

					$('#create_group').attr('disabled', false);

					if( selectValues != null ) {
						$('#users').append(function() {
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

						$('#users').multiselect({
							maxHeight: '400',
							buttonWidth: '100%',
							buttonClass: 'btn btn-info',
							checkboxName: 'data[Group][users][]',
							enableFiltering: true,
							filterBehavior: 'text',
							includeFilterClearBtn: true,
							enableCaseInsensitiveFiltering: true,
							// numberDisplayed: 3,
							includeSelectAllOption: true,
							includeSelectAllIfMoreThan: 5,
							selectAllText: ' Select all',
							nonSelectedText: 'Select Users',
							// disableIfEmpty: true
							onInitialized: function() {
								var o = $.map(selectValues, function(e,i) {return i;});
								if( $("#userIds").length ) {
									$("#userIds").val(o)
								}
								else {
									$("#userIds").val('')
								}
							},
						});

						$('#users').multiselect('rebuild') ;

						// $("#users_list").slideUp().slideDown()

						var existingTokens = $('input#skills').tokenfield('getTokens');
						var tokenInput = $('.token-input').val();
						if(existingTokens.length == 0 && tokenInput == '') {
							$('#users').multiselect('enable');
							$('#create_group').attr('disabled', false);
						}
						if(existingTokens.length == 0 && tokenInput != '') {
							$('#users').multiselect('disable');
							$('#create_group').attr('disabled', true);
						}
						if(existingTokens.length > 0 && tokenInput != '') {
							$('#users').multiselect('disable');
							$('#create_group').attr('disabled', true);
						}
						if(existingTokens.length > 0 && tokenInput == '') {
							$('#users').multiselect('enable');
							$('#create_group').attr('disabled', false);
						}
						if(check == 1) {
							$('#create_group').attr('disabled', false);
						}
					}, 100)
				}
				else {
					newtest = 0;checkEnabled = 0;
					$('#users').empty();
					$('#users').multiselect('rebuild') ;
					$('#users').multiselect('disable');
					$('#create_group').attr('disabled', true);
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


	$.fn.propDisable = function () {
		$(this).prop('disabled', true)
	}

	$.fn.classDisable = function () {
		$(this).addClass('disabled')
	}

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

	function SortByName(a, b){
		var aName = a.val.toLowerCase();
		var bName = b.val.toLowerCase();
		return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	}



	$.mselect_size = 15;
	$.mselect_height = "300px";
	//$('body').delegate('.btn-select-list li', 'click', function(event) {
	$('body').delegate('.btn-select-list', 'change', function(event) {
		// var project_id = $(this).data('value');
		var project_id = $(this).val();
		$('#skills').tokenfield('setTokens', []);
		$('#selectedSkills').val('');
		$('#subjects').tokenfield('setTokens', []);
		$('#selectedSubjects').val('');
		$('#domains').tokenfield('setTokens', []);
		$('#selectedDomains').val('');
		$('#projectId').val( (project_id != '') ? project_id : "")
		$('span.error-message').html('');
		// $(".user_selection").slideUp().slideDown()

		$('.nav-tabs.comments a[href="#text_search"]').tab('show')
		$('#sel_skills_group').empty();
		$('#sel_subjects_group').empty();
		$('#sel_domains_group').empty();
		$('#sel_tags_group').multiselect('deselectAll', false);
		$("#match_all_skill_checkbox").prop("checked", false);
		$("#match_all_subject_checkbox").prop("checked", false);
		$("#match_all_domain_checkbox").prop("checked", false);
		$("#match_all_tag_checkbox").prop("checked", false);
		$('.group-user-err').html('');
		var uoutput = [];
		$.ajax({
			url: $js_config.base_url + "groups/get_users_with_skills/" + project_id,
			type: "POST",
			data: $.param({'project_id': project_id}),
			dataType: "JSON",
			global: false,
			success: function (response) {
				if (response.success) {
					var selectValues = response.content.user_list;
					var selectSkills = response.content.skill_list;
					var selectSubjects = response.content.subject_list;
					var selectDomains = response.content.domain_list;

					$('#user_text_search').empty();
					$('#user_skill_search').empty();
					$('#user_subject_search').empty();
					$('#user_domain_search').empty();
					$('#user_tag_search').empty();

					if( selectValues != null ) {
						var output = '';
						var uoutput = [];
						var userBunch = [];
						var userAll = [];
						$.each(selectValues, function(key, value) {
							output += '<option value="' + key + '">' + value + '</option>';
							uoutput.push(value);
							userBunch.push(key);
							var u = {key: key, val: value}
							userAll.push(u)
						});
						userAll.sort(SortByName);

						if( $("#userIds").length ) {
							$("#userIds").val(userBunch)
						}
						$.availableAllUsers = userBunch.join();
						$('#user_skill_search').append(output);
						$('#user_subject_search').append(output);
						$('#user_domain_search').append(output);
						$('#user_tag_search').append(output);
					}else {
						$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
						$(".user_selection").slideUp()
					}
					if( selectSkills != null ) {
						var outputSk = '';
						$.each(selectSkills, function(key, value) {
							outputSk += '<option value="' + value.value + '">' + value.label + '</option>';
						});

						$('#sel_skills_group').append(outputSk);
					}
					if( selectSubjects != null ) {
						var outputSk = '';
						$.each(selectSubjects, function(key, value) {
							outputSk += '<option value="' + value.value + '">' + value.label + '</option>';
						});

						$('#sel_subjects_group').append(outputSk);
					}
					if( selectDomains != null ) {
						var outputSk = '';
						$.each(selectDomains, function(key, value) {
							outputSk += '<option value="' + value.value + '">' + value.label + '</option>';
						});

						$('#sel_domains_group').append(outputSk);
					}
					$('#sel_tags_group').multiselect('rebuild') ;

					setTimeout(function(){
						$.user_text_search_multi = $.user_tag_search_multi = $.user_skill_search_multi = $.user_subject_search_multi = $.user_domain_search_multi = undefined;
						$.user_text_search_multi = $('#user_text_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '60%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default custom_span',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.text_search_users = $.user_text_search_multi.data('value');
						});

						$.user_tag_search_multi = $('#user_tag_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '60%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.tag_search_users = $.user_tag_search_multi.data('value');
						});


						$.user_skill_search_multi = $('#user_skill_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '60%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.skill_search_users = $.user_skill_search_multi.data('value');;
						});

						$.user_subject_search_multi = $('#user_subject_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '60%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.subject_search_users = $.user_subject_search_multi.data('value');;
						});

						$.user_domain_search_multi = $('#user_domain_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '60%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.domain_search_users = $.user_domain_search_multi.data('value');;
						});
						$.sel_skills_group = $('#sel_skills_group').multiselect({
							enableUserIcon: false,
							buttonClass: 'btn btn-default aqua',
							buttonWidth: '100%',
							buttonContainerWidth: '100%',
							numberDisplayed: 2,
							maxHeight: '318',
							checkboxName: 'tags_skills_sel[]',
							includeSelectAllOption: true,
							enableFiltering: true,
							filterPlaceholder: 'Search text...',
							enableCaseInsensitiveFiltering: true,
							nonSelectedText: 'Select Skills',
							templates: {
								filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
							},
							onSelectAll: function() {
								var sel_users = $('#sel_skills_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('skill', selStr, selected.length);
							},
							onDeselectAll: function() {
								apply_user_filter('skill', '', 0);
							},
							onChange: function(element, checked) {
								var sel_users = $('#sel_skills_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('skill', selStr, selected.length);
							}
						});
						$('#sel_skills_group').multiselect('rebuild') ;

						$.sel_subjects_group = $('#sel_subjects_group').multiselect({
							enableUserIcon: false,
							buttonClass: 'btn btn-default aqua',
							buttonWidth: '100%',
							buttonContainerWidth: '100%',
							numberDisplayed: 2,
							maxHeight: '318',
							checkboxName: 'tags_skills_sel[]',
							includeSelectAllOption: true,
							enableFiltering: true,
							filterPlaceholder: 'Search text...',
							enableCaseInsensitiveFiltering: true,
							nonSelectedText: 'Select Subjects',
							templates: {
								filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
							},
							onSelectAll: function() {
								var sel_users = $('#sel_subjects_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('subject', selStr, selected.length);
							},
							onDeselectAll: function() {
								apply_user_filter('subject', '', 0);
							},
							onChange: function(element, checked) {
								var sel_users = $('#sel_subjects_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('subject', selStr, selected.length);
							}
						});
						$('#sel_subjects_group').multiselect('rebuild') ;

						$.sel_domains_group = $('#sel_domains_group').multiselect({
							enableUserIcon: false,
							buttonClass: 'btn btn-default aqua',
							buttonWidth: '100%',
							buttonContainerWidth: '100%',
							numberDisplayed: 2,
							maxHeight: '318',
							checkboxName: 'tags_skills_sel[]',
							includeSelectAllOption: true,
							enableFiltering: true,
							filterPlaceholder: 'Search text...',
							enableCaseInsensitiveFiltering: true,
							nonSelectedText: 'Select Domains',
							templates: {
								filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
							},
							onSelectAll: function() {
								var sel_users = $('#sel_domains_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('domain', selStr, selected.length);
							},
							onDeselectAll: function() {
								apply_user_filter('domain', '', 0);
							},
							onChange: function(element, checked) {
								var sel_users = $('#sel_domains_group option:selected');
								var selected = [];
								$(sel_users).each(function(index, brand){
									selected.push([$(this).val()]);
								});
								var selStr = selected.join();
								apply_user_filter('domain', selStr, selected.length);
							}
						});
						$('#sel_domains_group').multiselect('rebuild') ;

						$(".user_selection").slideUp().slideDown()
					}, 1)
				} else {
					// $('.btn-select.btn-select-light').parent().find('span.error-message').html(response.msg);
					$('.custom-dropdown').parent().find('span.error-message').html(response.msg);
					$(".user_selection").slideUp()
				}
			}// end success
		})// end ajax
	})

	/* multiselect group */
	setTimeout(function(){
		$('#multiselect_groups').multiselect({
			enableCollapsibleOptGroups: true,
			maxHeight: '400',
			buttonWidth: '100%',
			buttonClass: 'btn btn-info',
			checkboxName: 'data[Group][id][]',
			enableFiltering: true,
			filterBehavior: 'remote',
			includeFilterClearBtn: true,
			enableCaseInsensitiveFiltering: true,
			includeSelectAllOption: true,
			includeSelectAllIfMoreThan: 5,
			selectAllText: ' Select all',
			// disableIfEmpty: true
			onInitialized: function() {

			}
		});

		$('#multiselect_groups').multiselect('rebuild') ;
	},1000)
	/* multiselect group */


	$('body').delegate('a#create_group', 'click', function(event){
		event.preventDefault();
		var $btn = $(this);
		$('span.error-message').text('');

		$merged_user = [];
		if($.target_tab == '#text_search') {
			$merged_user = $.text_search_users;
		} else if($.target_tab == '#skills_search') {
			$merged_user = $.skill_search_users;
		} else if($.target_tab == '#subjects_search') {
			$merged_user = $.subject_search_users;
		} else if($.target_tab == '#domains_search') {
			$merged_user = $.domain_search_users;
		} else if($.target_tab == '#tag_search') {
			$merged_user = $.tag_search_users;
		}
		if($merged_user){
			var $groupUserUnique = $merged_user.filter(function(item, pos){
				return $merged_user.indexOf(item)=== pos;
			});
		}
		$('#userSelectedIds').val($merged_user.join(','));

		$.ajax({
			url: $js_config.base_url + "groups/create_group/",
			type: "POST",
			data: $("#frm_create_group").serialize(),
			dataType: "JSON",
			global: false,
			success: function (response) {
				if(response.success) {
					 if(response.socket_content){
						// send web notification
						$.socket.emit('socket:notification', response.socket_content, function(userdata){ });

					}
					var ids = response.content;
					window.location = $js_config.base_url + 'groups/create_permissions/'+ ids.project_id + '/' + ids.group_id
				}
				else {
					if(response.content) {
						$.each(response.content, function(k, v) {
							var $inpEle = $('#'+k);
							if(k == 'users') {
								$('.group-user-err').html(v);
							} else {
								$inpEle.parent().find('span.error-message').html(v);
							}
						})
					}
				}
			}
		})

	})

	$('body').delegate('#title', 'keyup', function(event){
		var val = $(this).val()

			if(val != '' ) {
				$(this).parent().find('span.error-message').text('')
			}
	})

	$('#popup_model_box').on('hidden.bs.modal', function(){
			$(this).removeData()
	})

	$('body').delegate('input[name="project_type"]', 'change', function(event) {
		var val = $(this).val()

		$('.btn-select-value').text('Select a Project')
		$(".user_selection").slideUp()

		if(val != '' ) {
			$.ajax({
				url: $js_config.base_url + "groups/get_projects/",
				type: "POST",
				data: $.param({'project_type': val}),
				dataType: "JSON",
				global: false,
				success: function (response) {

					if (response.success) {

						$('.btn-select-value').text('Select a Project')
						$('.btn-select.btn-select-light').parents('.form-group:first').find('span.error-message').text('')

						var selectValues = response.content;

						$('.btn-select-value').text('Select a Project')
						$('.btn-select-list').empty()


						if( selectValues != null ) {
							$('.btn-select-list').append(function() {
								var output = '';

								$.each(selectValues, function(key, value) {
									output += '<li data-value="' + key + '">' + value + '</li>';
								});
								return output;
							});
							$(".user_selection").slideUp()
						}
						else {
							$('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
							$(".user_selection").slideUp()
						}
					}
					else {
						$('.btn-select.btn-select-light').parent().find('span.error-message').html('No project found.');
						$('.btn-select-list').empty()
					}

				}// end success

			})// end ajax
		}
		else {

		}
	})

	$('body').delegate(".group_title", 'keyup focus', function(event){
		var characters = 50;
		event.preventDefault();
		var $error_el = $(this).parent().find('.error-message');
		if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
			$.input_char_count(this, characters, $error_el);
		}
	})

	$(window).on('click', function(event) {
		if(!$(event.target).is("textarea") && !$(event.target).is("input") ){
			if ($('.error-message.text-danger').length) {
				$('.error-message.text-danger').text("")
			}
		}
    })

	$('input[name="project_type"][value=1]').trigger('change')


	$('body').delegate(".skillboxes", 'keydown', function(event){
		//$('.skillboxes').keydown(function(event) {
		if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
			event.preventDefault();
		 }
	});

	$('body').delegate(".skillboxes", 'contextmenu', function(event){
	//$('.skillboxes').on("contextmenu",function(){
	   return false;
	});

})


/*SJ Code*/
function arrayDiff(array1, array2) {
	var newItems = [];
	$.grep(array2, function(i) {
		if ($.inArray(i, array1) == -1)
		{
			newItems.push(i);
		}
	});
	return newItems;
}
$.availableAllUsers = [];
$.text_search_users = [];
$.skill_search_users = [];
$.subject_search_users = [];
$.domain_search_users = [];
$.tag_search_users = [];
$.target_tab = '#text_search';
$.relatedTarget_tab = '';
$(function(){
	$.sort_data = function(data){
		var temp = [];
		$.each(data, function(index, el) {
			temp.push(el);
		});
		temp.sort(function (a, b) {
			return a.label.localeCompare(b.label);
		});

		return temp;
	}

	/* SJ code for skill and tags*/
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		$('.group-user-err').html('');

		if($(e.relatedTarget).attr("href") === undefined) {
			$.relatedTarget_tab = $.target_tab;
		} else {
			$.relatedTarget_tab = $(e.relatedTarget).attr("href");
		}
		$.target_tab = $(e.target).attr("href");
	})
	$('#match_all_tag_checkbox').on('change', function(){
		var sel_users = $('#sel_tags_group option:selected');
		var selected = [];
		$(sel_users).each(function(index, brand){
			selected.push([$(this).val()]);
		});
		var selStr = selected.join();
		if(selStr.length > 0) {
			apply_user_filter('tag', selStr, selected.length);
		}
	});
	$('#match_all_skill_checkbox').on('change', function(){
		var sel_users = $('#sel_skills_group option:selected');
		var selected = [];
		$(sel_users).each(function(index, brand){
			selected.push([$(this).val()]);
		});
		var selStr = selected.join();
		if(selStr.length > 0) {
			apply_user_filter('skill', selStr, selected.length);
		}
	})
	/* SJ code for skill and tags*/

	$('.submit-nudge').click(function(event) {
		event.preventDefault();
		var formData = {};
		//var $merged_user = $.text_search_users.concat($.skill_search_users, $.tag_search_users);
		if($.target_tab == '#text_search') {
			$merged_user = $.text_search_users;
		} else if($.target_tab == '#skills_search') {
			$merged_user = $.skill_search_users;
		} else if($.target_tab == '#tag_search') {
			$merged_user = $.tag_search_users;
		} else if($.target_tab == '#subjects_search') {
			$merged_user = $.subject_search_users;
		} else if($.target_tab == '#domains_search') {
			$merged_user = $.domain_search_users;
		}

		var $nudgeUserUnique = $merged_user.filter(function(item, pos){
			return $merged_user.indexOf(item)=== pos;
		});

		$('.error').html('');
		if($nudgeUserUnique.length <= 0) {
			$('.group-user-err').html('One or more People are required');
			return;
		} else {
			formData['user'] = $nudgeUserUnique;
		}
	});
})
function apply_user_filter(type, selected, selLength) {
	var is_match_all = 0;
	if(type == 'tag') {
		is_match_all = ($('#match_all_tag_checkbox').is(":checked")) ? 1 : 0;
	}
	if(type == 'skill') {
		is_match_all = ($('#match_all_skill_checkbox').is(":checked")) ? 1 : 0;
	}
	if(type == 'subject') {
		is_match_all = ($('#match_all_subject_checkbox').is(":checked")) ? 1 : 0;
	}
	if(type == 'domain') {
		is_match_all = ($('#match_all_domain_checkbox').is(":checked")) ? 1 : 0;
	}
	var project_id = $('[name="data[Group][project_id]"]').val();

	if($.availableAllUsers.length > 0) {
		$.ajax({
			url: $js_config.base_url + 'groups/apply_user_filter',
			type: 'POST',
			dataType: 'json',
			data: { type: type, selected: selected, is_match_all: is_match_all, project_id: project_id, available_users: $.availableAllUsers },
			success: function(response) {
				if(response.success){
					var userAll = [];
					if(response.content.length > 0) {
						$.each(response.content, function(key, val) {
							var u = {key: val.value, val: val.label}
							userAll.push(u);
						})
					}
					if(type == 'tag') {
						/*$.user_tag_search.multiselect('deselectAll', false);
						var queryArr = [];
						if(response.content.length > 0) {
							$.each(response.content, function(index, el) {
								var pieces = {
									"label" :el.label, "value" :el.value
								};
									queryArr.push( pieces );
							});
							queryArr = $.sort_data(queryArr);
						}
						$.user_tag_search.multiselect('dataprovider', queryArr);*/
						$.tag_search_users = [];
						$.user_tag_search_multi = $('#user_tag_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '100%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.tag_search_users = $.user_tag_search_multi.data('value');
						});
					}
					if(type == 'skill') {

						$.skill_search_users = [];
						$.user_skill_search_multi = $('#user_skill_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '100%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.skill_search_users = $.user_skill_search_multi.data('value');;
						});

					}
					if(type == 'subject') {
						/*$.user_subject_search.multiselect('deselectAll', false);
						var queryArr = [];
						if(response.content.length > 0) {
							$.each(response.content, function(index, el) {
								var pieces = {
									"label" :el.label, "value" :el.value
								};
								//if($.inArray(parseInt(el.value), $.availableAllUsers) != -1) {
									queryArr.push( pieces );
								//}
							});
							queryArr = $.sort_data(queryArr);
						}
						$.user_subject_search.multiselect('dataprovider', queryArr);*/
						$.subject_search_users = [];


						$.user_subject_search_multi = $('#user_subject_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '100%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.subject_search_users = $.user_subject_search_multi.data('value');;
						});

					}
					if(type == 'domain') {
						/*$.user_domain_search.multiselect('deselectAll', false);
						var queryArr = [];
						if(response.content.length > 0) {
							$.each(response.content, function(index, el) {
								var pieces = {
									"label" :el.label, "value" :el.value
								};
								//if($.inArray(parseInt(el.value), $.availableAllUsers) != -1) {
									queryArr.push( pieces );
								//}
							});
							queryArr = $.sort_data(queryArr);
						}
						$.user_domain_search.multiselect('dataprovider', queryArr);*/
						$.domain_search_users = [];

						$.user_domain_search_multi = $('#user_domain_search_multi').selectmultiple({
							text: 'Select Users',
							data: userAll,
							size: $.mselect_size,
							maxWidth: '100%',
							placeholder: 'Search Users',
							selectSize: $.mselect_height,
							btnClass: 'nofill form-control btn-default',
							checkboxName: 'group_users[]',
						 })
						.on('change', function(e){
							$.domain_search_users = $.user_domain_search_multi.data('value');;
						});

					}
				}

			}
		});
	}
}
</script>

<?php echo $this->Session->flash(); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none">Create a Project sharing Group</span>
					</p>
				</h1>
			</section>
		</div>
		<div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-header no-padding" style="">
							<!-- MODAL BOX WINDOW -->
                            <!-- <div class="modal modal-success fade" id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div> -->
							<!-- END MODAL BOX -->
                        </div>
						<div class="box-body  popup-select-icon clearfix list-shares" style="min-height: 800px">
							<?php echo $this->Form->create('ShareTree', array('url' => array('controller' => 'groups', 'action' => 'manage_sharing'), 'style'=>"padding-top:15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;     border: 1px solid #ddd;  border-top-right-radius: 3px;" ,'class' => 'formAddSharing form-horizontal', 'id' => 'frm_create_group', 'enctype' => 'multipart/form-data')); ?>
							<div class="row">
								<div class="col-md-12">
									<?php /* <div class="col-md-8">
										Select Project From:
										<div class="radio-family create-group-radio">
											<div class="radio radio-warning">
												<input type="radio" name="project_type" id="my_projects" value="1" checked />
												<label for="my_projects"> My Projects </label>
											</div>
											<div class="radio radio-warning">
												<input type="radio" name="project_type" id="rec_projects" value="2" />
												<label for="rec_projects"> Received Projects </label>
											</div>
										</div>
									</div> */?>

									<div class="col-md-12">
										<span class="p-select-box">
											<label class="custom-dropdown" style="width:100%;">
												<select name="data[Group][project_id]" class="form-control aqua btn-select-list">
													<option value="">Select Project</option>
													<?php if( isset($projects_list) && !empty($projects_list) ) { ?>
													<?php foreach($projects_list as $key => $value )  { ?>
														<option data-value="<?php echo $key; ?>" value="<?php echo $key; ?>"><?php echo $value; ?></option>
														<?php } ?>
													<?php } ?>
												</select>
											</label>
											<span class="error-message text-danger" style="display: block;" ></span>
										</span>
									</div>
								</div>
							</div>
							<div class="panel panel-default user_selection clearfix" style="display: none;margin-bottom:0px; border:none;">
								<!--<div class="panel-heading" >
										<h4 class="panel-title">Add Group Detail</h4>
								</div>-->
								<div class="panel-body sharing-group-crt" >
									<div class="add-user-body clearfix">
										<input type="hidden" name="selectedSkills" id="selectedSkills">
										<input type="hidden" name="selectedSubjects" id="selectedSubjects">
										<input type="hidden" name="selectedDomains" id="selectedDomains">
										<div class="create-group-col-wrap">
                                            <div class="create-group-col-left">
                                            <div class="create-group-rows">
                                            <div class="create-group-title">
											<label style="" class="input-label">Group Title: </label>
                                                </div>
                                            <div class="create-group-input-col">

												<div class="input-group group-title-input" style="width: 100%;">
													<input type="text" name="data[Group][title]" id="title" class="form-control group_title" autocomplete="off" />
													<span class="error-message text-danger"></span>
												</div>

                                                </div>
                                                </div>
                                            </div>
										</div>
										<?php /*
										<div style="" class="col-sm-12 col-md-12 col-lg-12 mgbottom skillboxes creategroupfield">
											<label style="" class="input-label create-group-title">Required Skills: </label>
											<div class="input-colm input-hidden">
												<div class="input-group" style="">
													<input type="text" name="skills" id="skills" class="form-control" />
													<!--<span class="input-group-addon btn-progress" id="progress_bar"><span class="fa fa-spinner fa-pulse"></span></span> -->
													<span class="input-group-addon btn-filter" style="display:none" id="get_users"><span class="fa fa-user"></span></span>
													<span class="input-group-addon btn-times tipText" data-title="Clear Skills" id="clear_skills" ><span class="fa fa-times"></span></span>
												</div>
												<span class="error-message text-danger"></span>
											</div>
										</div>
										*/?>
										<div class="col-sm-12 send-group-tabs-nav">
											<ul class="nav nav-tabs comments" style="cursor: move; display: inline-block;">
												<li class="active">
													<a data-toggle="tab" class="active" data-target="#text_search" href="#text_search" aria-expanded="true">Search</a>
												</li>
												<li class="">
													<a data-toggle="tab" data-target="#tag_search" href="#tag_search" aria-expanded="false">Tags</a>
												</li>
												<li class="">
													<a data-toggle="tab" data-target="#skills_search" href="#skills_search" aria-expanded="false">Skills</a>
												</li>
												<li class="">
													<a data-toggle="tab" data-target="#subjects_search" href="#subjects_search" aria-expanded="false">Subjects</a>
												</li>
												<li class="">
													<a data-toggle="tab" data-target="#domains_search" href="#domains_search" aria-expanded="false">Domains</a>
												</li>
											</ul>
										</div>
                                        <div class="create-group-col-wrap">
											<div class="create-group-col-left send-group-pop-tabs">
												<div class="tab-content" id="myTabContent">
													<div id="text_search" class="tab-pane fade active in">
														<h5 class="skills-tags-info">Search for People or filter on Tags and Competencies.</h5>
														<div class="create-group-rows">
															<div class="create-group-title">
																<label class="input-label" for="RiskType">Group Members: </label>
                                                            </div>
															<div class="create-group-input-col">
																<div id="user_text_search_multi" > </div>
																	<input type="hidden" name="user_text_search" id="user_text_search" value="">

																<span class="error-message group-user-err error text-danger"></span>
                                                            </div>
														</div>
													</div>
													<div id="tag_search" class="tab-pane fade">
														<div class="row">
															<div class="col-sm-12 group-pop-group marginb25">
																<select class="form-control sel_tags_group" id="sel_tags_group" style="height:30px;display:none;" multiple="multiple" name="tag_select[]">
																	<?php
																	if( isset($tags) && !empty($tags) ){
																		foreach($tags as $k => $v){
																			?>
																			<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
																			<?php
																		}
																	}
																	?>
																</select>
																<label class="checkbox-group">
																	<input type="checkbox" id="match_all_tag_checkbox" name="match_all_tag_checkbox" class="" value="0">
																	<label class="fancy_label text-black" for="match_all_tag_checkbox">Require All</label>
																</label>
															</div>
															<div class="create-group-rows col-sm-12">
																<div class="create-group-title"><label class="input-label" for="RiskType">Group Members: </label></div>
																<div class="create-group-input-col">
																	<div id="user_tag_search_multi"> </div>
																	<input type="hidden" name="user_tag_search" id="user_tag_search" value="">
																	<span class="error-message group-user-err error text-danger"></span>
																</div>
															</div>
														</div>
													</div>
													<div id="skills_search" class="tab-pane fade">
														<div class="row">
															<div class="col-sm-12 group-pop-group marginb25">
																<select class="form-control sel_skills_group" id="sel_skills_group" style="height:30px;display:none;" multiple="multiple" name="skill_select[]">
																	<?php
																	if( isset($skills) && !empty($skills) ){
																		foreach($skills as $k => $v){
																			?>
																			<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
																			<?php
																		}
																	}
																	?>
																</select>
																<label class="checkbox-group">
																	<input type="checkbox" id="match_all_skill_checkbox" name="match_all_skill_checkbox" class="" value="0">
																	<label class="fancy_label text-black" for="match_all_skill_checkbox">Require All</label>
																</label>
															</div>
															<div class="create-group-rows col-sm-12">
																<div class="create-group-title"><label class="input-label" for="RiskType">Group Members: </label></div>
																<div class="create-group-input-col">
																	<div id="user_skill_search_multi" > </div>
																	<input type="hidden" name="user_skill_search" id="user_skill_search" value="">
																	<span class="error-message group-user-err error text-danger"></span>
																</div>
															</div>
														</div>
													</div>
													<div id="subjects_search" class="tab-pane fade">
														<div class="row">
															<div class="col-sm-12 group-pop-group marginb25">
																<select class="form-control sel_subjects_group" id="sel_subjects_group" style="height:30px;display:none;" multiple="multiple" name="subject_select[]">
																	<?php
																	if( isset($subjects) && !empty($subjects) ){
																		foreach($subjects as $k => $v){
																			?>
																			<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
																			<?php
																		}
																	}
																	?>
																</select>
																<label class="checkbox-group">
																	<input type="checkbox" id="match_all_subject_checkbox" name="match_all_subject_checkbox" class="" value="0">
																	<label class="fancy_label text-black" for="match_all_subject_checkbox">Require All</label>
																</label>
															</div>
															<div class="create-group-rows col-sm-12">
																<div class="create-group-title"><label class="input-label" for="RiskType">Group Members: </label></div>
																<div class="create-group-input-col">
																	<div id="user_subject_search_multi"  > </div>
																	<input type="hidden" name="user_subject_search" id="user_subject_search" value="">
																	<span class="error-message group-user-err error text-danger"></span>
																</div>
															</div>
														</div>
													</div>
													<div id="domains_search" class="tab-pane fade">
														<div class="row">
															<div class="col-sm-12 group-pop-group marginb25">
																<select class="form-control sel_domains_group" id="sel_domains_group" style="height:30px;display:none;" multiple="multiple" name="domain_select[]">
																	<?php
																	if( isset($domains) && !empty($domains) ){
																		foreach($domains as $k => $v){
																			?>
																			<option value="<?php echo $v['value']; ?>"><?php echo htmlentities($v['label']); ?></option>
																			<?php
																		}
																	}
																	?>
																</select>
																<label class="checkbox-group">
																	<input type="checkbox" id="match_all_domain_checkbox" name="match_all_domain_checkbox" class="" value="0">
																	<label class="fancy_label text-black" for="match_all_domain_checkbox">Require All</label>
																</label>
															</div>
															<div class="create-group-rows col-sm-12">
																<div class="create-group-title"><label class="input-label" for="RiskType">Group Members: </label></div>
																<div class="create-group-input-col">
																	<div id="user_domain_search_multi" > </div>
																	<input type="hidden" name="user_domain_search" id="user_domain_search" value="">
																	<span class="error-message group-user-err error text-danger"></span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										<!--<div class="col-sm-12 col-md-9 col-lg-9 creategroupfield">
											<label style="" class="input-label create-group-title">Add Members to Group: </label>
											<div class="input-colm">
												<div class="button_full last-dropdown">
													<select id="users" multiple="multiple"> </select>
													<span class="error-message text-danger"></span>
												</div>
											</div>
										</div>-->
										<div class="create-group-col-right">
											<div class="text-right margin-right">
												<a href="#" class="btn btn-success btn-sm" style="margin-top: 2px;" id="create_group" >Create</a>
												<a href="<?php echo Router::url(array('controller' => 'shares', 'action' => 'my_groups')); ?>" class="btn btn-danger btn-sm" style="margin-top: 2px;" id="cancel_group" >Cancel</a>
											</div>
										</div>
                                        </div>

									</div>
								</div>
								<!--<div class="panel-footer clearfix"></div>-->
							</div>
							<input type="hidden" name="userIds" id="userIds" value="" />
							<input type="hidden" name="userSelectedIds" id="userSelectedIds" value="" />
							<input type="hidden" name="projectId" id="projectId" value="" />
							<?php  echo $this->Form->end(); ?>
						<!-- end stage 1 -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function(){
		$.sel_tags_group = $('#sel_tags_group').multiselect({
			enableUserIcon: false,
			buttonClass: 'btn btn-default aqua',
			buttonWidth: '100%',
			buttonContainerWidth: '100%',
			numberDisplayed: 2,
			maxHeight: '318',
			checkboxName: 'tags_skills_sel[]',
			includeSelectAllOption: true,
			enableFiltering: true,
			filterPlaceholder: 'Search text...',
			enableCaseInsensitiveFiltering: true,
			nonSelectedText: 'Select Tags',
			templates: {
				filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter tipText" type="button" title="Clear Search"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
			},
			onSelectAll: function() {
				var sel_users = $('#sel_tags_group option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('tag', selStr, selected.length);
			},
			onDeselectAll: function() {
				apply_user_filter('tag', '', 0);
			},
			onChange: function(element, checked) {
				var sel_users = $('#sel_tags_group option:selected');
				var selected = [];
				$(sel_users).each(function(index, brand){
					selected.push([$(this).val()]);
				});
				var selStr = selected.join();
				apply_user_filter('tag', selStr, selected.length);
			}
		});
		$('#match_all_tag_checkbox').on('change', function(){
			var sel_users = $('#sel_tags_group option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('tag', selStr, selected.length);
			}
		});
		$('#match_all_skill_checkbox').on('change', function(){
			var sel_users = $('#sel_skills_group option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('skill', selStr, selected.length);
			}
		})
		$('#match_all_subject_checkbox').on('change', function(){
			var sel_users = $('#sel_subjects_group option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('subject', selStr, selected.length);
			}
		})
		$('#match_all_domain_checkbox').on('change', function(){
			var sel_users = $('#sel_domains_group option:selected');
			var selected = [];
			$(sel_users).each(function(index, brand){
				selected.push([$(this).val()]);
			});
			var selStr = selected.join();
			if(selStr.length > 0) {
				apply_user_filter('domain', selStr, selected.length);
			}
		})
	})

</script>