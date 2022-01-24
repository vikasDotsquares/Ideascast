<script type="text/javascript">
    $(function () {
        $('#task-header-tabs').removeAttr('style');
        $('#popup_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal')//.find(".modal-content").html('<img src="../images/ajax-loader-1.gif" style="margin: auto;">');
        });
        $('.selectpicker').selectpicker();

        $("body").delegate(".history", "click", function (event) {
            event.preventDefault()
            var itemid = $(this).attr("itemid");
            var itemtype = $(this).attr("itemtype");
            var dataid = $(this).attr("data-id");
            var $row = $(this).parents('.row:first'),
                $formdiv = $row.find('.list-form');

            var urlV = $js_config.base_url + "entities/get_history/" + dataid + "/" + itemtype;


            if ($formdiv.is(':visible')) {
                $formdiv.slideUp('slow')
            }

			 if ($("#" + itemid).length <= 0) {
			   $row.after('<div id="'+itemid+'" class="history_update" style="display:none"></div>');
			 }

            if (!$("#" + itemid).is(":visible")) {
                $row.removeClass('bg-warning')
                $.ajax({
                    url: urlV,
                    async: false,
                    success: function (response) {
						$('.View-result-form').hide();
						$('#feedbacks_table .row').removeClass('bg-warning2');
						$('#votes_table .row').removeClass('bg-warning2');
                        $(".history_update").stop().slideUp("slow");
                        $("#" + itemid).html(response);
                        $("#" + itemid).stop().slideToggle("slow");
                    }
                });
            }
            else {
                $("#" + itemid).stop().slideToggle("slow");

                $row.removeClass('bg-warning')

                if ($row.find('.update_link').length > 0) {
                    $row.parents('#links_table').next('.link_form:first').find('#cancel_update_link').trigger('click')
                }

                if ($row.find('.update_doc').length > 0) {
                    $row.parents('#documents_table').next('.doc_form:first').find('#cancel_update_doc').trigger('click')
                }
            }
            var $this = $(this);
            setTimeout(()=>{
                $this.attr('data-original-title', $this.attr('data-original-title'))
                    .tooltip('fixTitle')
                    .tooltip('show');
            },400)

        })

        var hash = document.location.hash;
        // location.hash = '';
        var hash_array = ['#tasks', '#links', '#notes', '#documents', '#mind_maps', '#decisions', '#feedbacks', '#votes'];
        if (hash == '') {
            // $('.anchor-update-task').trigger('click');
        }
        if (hash) {
            if (hash == '#tasks') {
                // hash = 'links';
                // $('.anchor-update-task').trigger('click');
            }
            if (jQuery.inArray(hash, hash_array) == -1){
                hash = '#links';
            }
            hash = '#links';
            hash = hash.substring(1, hash.length);
            var hashTag = $('.cd-tabs-navigation li a[data-content="' + hash + '"]');

            if (hashTag.length > 0)
                hashTag.trigger('click')
        }
        else {
            var hashTag = $('.cd-tabs-navigation li:first a');
            if (hashTag.length > 0)
                hashTag.trigger('click')
        }
        var url = location.href.split('#');
        window.history.pushState(null, '', url[0]);



        $("body").on('click', '#element_tabs a', function (event) {
            event.preventDefault();
            // location.hash = '';
            var url = location.href.split('#');
            window.history.pushState(null, '', url[0]);
        })

        $('#off').click(function () {
            $.ajax({
                url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new',
                type: "POST",
                data: $.param({project: $js_config.currentProjectId}),
                dataType: "JSON",
                global: false,
                success: function (response) {

                    if (response.success) {

                        var selectUsers = response.users;
                        var selectGroups = response.group_data;

                        $('#multiselect_groups').empty();

                        if (selectGroups != null) {

                            var output = '';
                            $.each(selectGroups, function (key, value) {

                                if (!$.isEmptyObject(value['users'])) {

                                    output += '<optgroup label="' + value[0].title + '">';

                                    var userlist = value['users'];
                                    userlist.sort($.SortByName);

                                    $.each(userlist, function (key1, value1) {

                                        output += '<option value="' + value1.id + '">' + value1.name + '</option>';

                                    });

                                    output += '</optgroup>';
                                }

                            });

                            $('#multiselect_groups').html(output)
                        }

                        setTimeout(function () {

                            $('#multiselect_groups').multiselect({
                                maxHeight: '400',
                                buttonWidth: '100%',
                                buttonContainerWidth: '60%',
                                buttonClass: 'btn btn-info',
                                checkboxName: 'data[FeedbackUser][list][]',
                                enableFiltering: true,
                                showRating: true,
                                filterBehavior: 'text',
                                includeFilterClearBtn: true,
                                enableCaseInsensitiveFiltering: true,
                                // numberDisplayed: 3,
                                includeSelectAllOption: true,
                                includeSelectAllIfMoreThan: 5,
                                selectAllText: ' Select all',
                                // disableIfEmpty: true
                                onInitialized: function () {

                                },
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,
								enableHTML:true,
                            });

                            $('#multiselect_groups').multiselect('rebuild');

                            // $("#users_list").slideUp().slideDown()
                            $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                                // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                            })
                        }, 100)
                    }
                    else {
                        $('#multiselect_groups').multiselect('disable')
                        $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                        // $("#users_list").slideUp()
                    }

                }, // end success
                complete: function () {
                    // $('#progress_bar').hide()
                }

            })

        })

        $.SortByName = function(a, b){
            var aName = a.name.toLowerCase();
            var bName = b.name.toLowerCase();
            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
        }

        $('#onn').click(function () {
            $.ajax({
                url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new',
                type: "POST",
                data: $.param({project: $js_config.currentProjectId}),
                dataType: "JSON",
                global: false,
                success: function (response) {

                    if (response.success) {

                        var selectUsers = response.users;
                        var selectGroups = response.group_data;

                        $('#multiselect_groups').empty();


                        if (selectUsers != null) {
                            selectUsers.sort($.SortByName);

                            $('#multiselect_groups').append(function () {

                                var output = '';

                                //output += '<optgroup label="Individual">';

                                $.each(selectUsers, function (key, value) {

                                    output += '<option value="' + value.id + '">' + value.name + '</option>';

                                });

                                //output += '</optgroup>';

                                return output;
                            });
                        }
                        else {
                            $('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
                            $(".user_selection").slideUp()
                        }
                        setTimeout(function () {

                            $('#multiselect_groups').multiselect({
                                maxHeight: '400',
                                buttonWidth: '100%',
                                buttonContainerWidth: '60%',
                                buttonClass: 'btn btn-info',
                                checkboxName: 'data[FeedbackUser][list][]',
                                enableFiltering: true,
                                showRating: true,
                                filterBehavior: 'text',
                                includeFilterClearBtn: true,
                                enableCaseInsensitiveFiltering: true,
                                // numberDisplayed: 3,
                                includeSelectAllOption: true,
                                includeSelectAllIfMoreThan: 5,
                                selectAllText: ' Select all',
								enableHTML : true,
                                // disableIfEmpty: true
                                onInitialized: function () {

                                },
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,

                            });

                            $('#multiselect_groups').multiselect('rebuild');

                            // $("#users_list").slideUp().slideDown()
                            $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                                // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                            })
                        }, 100)
                    }
                    else {
                        $('#multiselect_groups').multiselect('disable')
                        $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                        // $("#users_list").slideUp()
                    }

                }, // end success
                complete: function () {
                    // $('#progress_bar').hide()
                }

            })

        });

        $.ajax({
            url: '<?php echo SITEURL; ?>entities/feedback_users_listing_new',
            type: "POST",
            data: $.param({project: $js_config.currentProjectId}),
            dataType: "JSON",
            global: false,
            success: function (response) {

                if (response.success) {

                    var selectUsers = response.users;
                    var selectGroups = response.group_data;

                    $('#multiselect_groups').empty();

                    if (selectUsers != null) {
                        selectUsers.sort($.SortByName);

                        $('#multiselect_groups').append(function () {

                            var output = '';

                            //	output += '<optgroup label="Individual">';

                            $.each(selectUsers, function (key, value) {

                                output += '<option value="' + value.id + '">' + value.name + '</option>';

                            });

                            //	output += '</optgroup>';

                            return output;
                        });
                    }
                    else {
                        $('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
                        $(".user_selection").slideUp()
                    }
                    setTimeout(function () {

                        $('#multiselect_groups').multiselect({
                            maxHeight: '400',
                            buttonWidth: '100%',
                            buttonContainerWidth: '60%',
                            buttonClass: 'btn btn-info',
                            checkboxName: 'data[FeedbackUser][list][]',
                            enableFiltering: true,
                            showRating: true,
							enableHTML: true,
                            filterBehavior: 'text',
                            includeFilterClearBtn: true,
                            enableCaseInsensitiveFiltering: true,
                            // numberDisplayed: 3,
                            includeSelectAllOption: true,
                            includeSelectAllIfMoreThan: 5,
                            selectAllText: ' Select all',
                            // disableIfEmpty: true
                            onInitialized: function () {

                            },
                            enableClickableOptGroups: true,
                            enableCollapsibleOptGroups: true,
                        });

                        $('#multiselect_groups').multiselect('rebuild');

                        // $("#users_list").slideUp().slideDown()
                        $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                            // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                        })
                    }, 100)
                }
                else {
                    $('#multiselect_groups').multiselect('disable')
                    $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                    // $("#users_list").slideUp()
                }

            }, // end success
            complete: function () {
                // $('#progress_bar').hide()
            }

        })// end ajax


        $('#off1').click(function () {

            $.ajax({
                url: '<?php echo SITEURL; ?>entities/vote_users_listing',
                type: "POST",
                data: $.param({}),
                dataType: "JSON",
                global: false,
                success: function (response) {

                    if (response.success) {

                        var selectUsers = response.users;
                        var selectGroups = response.group_data;

                        $('#multi_participant_vote_users').empty();

                        if (selectGroups != null) {

                            var output = '';
                            $.each(selectGroups, function (key, value) {

                                if (!$.isEmptyObject(value['users'])) {

                                    output += '<optgroup label="' + value[0].title + '">';

                                    var userlist = value['users'];
                                    userlist.sort($.SortByName);

                                    $.each(userlist, function (key1, value1) {

                                        output += '<option value="' + value1.id + '">' + value1.name + '</option>';

                                    });

                                    output += '</optgroup>';
                                }

                            });

                            $('#multi_participant_vote_users').html(output)
                        }

                        setTimeout(function () {

                            $('#multi_participant_vote_users').multiselect({
                                maxHeight: '400',
                                buttonWidth: '100%',
                                buttonClass: 'btn btn-info',
                                checkboxName: 'data[VoteUser][list][]',
                                enableFiltering: true,
                                showRating: true,
                                filterBehavior: 'text',
                                includeFilterClearBtn: true,
                                enableCaseInsensitiveFiltering: true,
                                // numberDisplayed: 3,
                                includeSelectAllOption: true,
                                includeSelectAllIfMoreThan: 5,
                                selectAllText: ' Select all',
                                // disableIfEmpty: true
                                onInitialized: function () {

                                },
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,
                            });

                            $('#multi_participant_vote_users').multiselect('rebuild');
                            $('#multi_participant_vote_users').multiselect('enable')

                            // $("#users_list").slideUp().slideDown()
                            $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                                // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                            })
                        }, 100)
                    }
                    else {
                        $('#multi_participant_vote_users').multiselect('disable')
                        $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                        // $("#users_list").slideUp()
                    }

                }, // end success
                complete: function () {
                    // $('#progress_bar').hide()
                }

            })// end ajax


        })


        $('#onn1').click(function () {

            $.ajax({
                url: '<?php echo SITEURL; ?>entities/vote_users_listing',
                type: "POST",
                data: $.param({}),
                dataType: "JSON",
                global: false,
                success: function (response) {

                    if (response.success) {

                        var selectUsers = response.users;
                        var selectGroups = response.group_data;

                        $('#multi_participant_vote_users').empty();
                        if (selectUsers != null) {
                            selectUsers.sort($.SortByName);

                            $('#multi_participant_vote_users').append(function () {

                                var output = '';

                                //	output += '<optgroup label="Individual">';

                                $.each(selectUsers, function (key, value) {

                                    output += '<option value="' + value.id + '">' + value.name + '</option>';

                                });

                                //	output += '</optgroup>';

                                return output;
                            });
                        }
                        else {
                            $('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
                            $(".user_selection").slideUp()
                        }
                        setTimeout(function () {

                            $('#multi_participant_vote_users').multiselect({
                                maxHeight: '400',
                                buttonWidth: '100%',
                                buttonContainerWidth: '60%',
                                buttonClass: 'btn btn-info',
                                checkboxName: 'data[VoteUser][list][]',
                                enableFiltering: true,
                                showRating: true,
                                filterBehavior: 'text',
                                includeFilterClearBtn: true,
                                enableCaseInsensitiveFiltering: true,
                                // numberDisplayed: 3,
                                includeSelectAllOption: true,
                                includeSelectAllIfMoreThan: 5,
                                selectAllText: ' Select all',
                                // disableIfEmpty: true
                                onInitialized: function () {

                                },
                                enableClickableOptGroups: true,
                                enableCollapsibleOptGroups: true,
                            });

                            $('#multi_participant_vote_users').multiselect('rebuild');
                            $('#multi_participant_vote_users').multiselect('enable')

                            // $("#users_list").slideUp().slideDown()
                            $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                                // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                            })
                        }, 100)
                    }
                    else {
                        $('#multi_participant_vote_users').multiselect('disable')
                        $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                        // $("#users_list").slideUp()
                    }

                }, // end success
                complete: function () {
                    // $('#progress_bar').hide()
                }

            })// end ajax

        })

        $.ajax({
            url: '<?php echo SITEURL; ?>entities/vote_users_listing',
            type: "POST",
            data: $.param({}),
            dataType: "JSON",
            global: false,
            success: function (response) {

                if (response.success) {

                    var selectUsers = response.users;
                    var selectGroups = response.group_data;

                    $('#multi_participant_vote_users').empty();
                    if (selectUsers != null) {
                        selectUsers.sort($.SortByName);

                        $('#multi_participant_vote_users').append(function () {

                            var output = '';

                            //	output += '<optgroup label="Individual">';

                            $.each(selectUsers, function (key, value) {

                                output += '<option value="' + value.id + '">' + value.name + '</option>';

                            });

                            //	output += '</optgroup>';

                            return output;
                        });
                    }
                    else {
                        $('.btn-select.btn-select-light').parent().find('span.error-message').html('No user found. Please select different project.');
                        $(".user_selection").slideUp()
                    }
                    setTimeout(function () {

                        $('#multi_participant_vote_users').multiselect({
                            maxHeight: '400',
                            buttonWidth: '100%',
                            buttonContainerWidth: '60%',
                            buttonClass: 'btn btn-info',
                            checkboxName: 'data[VoteUser][list][]',
                            enableFiltering: true,
                            showRating: true,
                            filterBehavior: 'text',
                            includeFilterClearBtn: true,
                            enableCaseInsensitiveFiltering: true,
                            // numberDisplayed: 3,
                            includeSelectAllOption: true,
                            includeSelectAllIfMoreThan: 5,
                            selectAllText: ' Select all',
                            // disableIfEmpty: true
                            onInitialized: function () {

                            },
                            enableClickableOptGroups: true,
                            enableCollapsibleOptGroups: true,
                        });

                        $('#multi_participant_vote_users').multiselect('rebuild');
                        $('#multi_participant_vote_users').multiselect('enable')

                        // $("#users_list").slideUp().slideDown()
                        $('.multiselect-container li:not(.multiselect-item.multiselect-group.multiselect-group-clickable)').each(function (i, v) {
                            // $(this).css({"padding-left": "20px", "margin-top": "-5px"})
                        })
                    }, 100)
                }
                else {
                    $('#multi_participant_vote_users').multiselect('disable')
                    $('.multiselect.dropdown-toggle').parent().next('span.error-message:first').html(response.msg);
                    // $("#users_list").slideUp()
                }

            }, // end success
            complete: function () {
                // $('#progress_bar').hide()
            }

        })// end ajax


        $('body').on('click', function (event) {

            setTimeout(function () {
                if ($('.multiselect.dropdown-toggle').parent('.btn-group').hasClass('open')) {
                    $('.main-header').parent('.wrapper').css('overflow', 'visible')
                }
                else {
                    $('.main-header').parent('.wrapper').css('overflow', 'hidden')
                }
            }, 200)
        })




    });
</script>

