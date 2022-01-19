<div class="modal-header">
    <button type="button" class="close close-skill" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title annotationeleTitle" id="myModalLabel">Add Adjustment</h4>
</div>
<div class="modal-body popup-select-icon">
    <?php //pr($pe_data); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label class=" control-label col-adj-2" for="project_id">Project: <sup>*</sup></label>
                <div class="col-adj-10">
                    <?php echo $this->Form->input('project_id', array('type' => 'select', 'options' => $project_list, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'project_id', 'empty' => 'Select Project' )); ?>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label class=" control-label  col-adj-2" for="workspace_id">Workspace: <sup>*</sup></label>
                <div class="col-adj-10">
                    <select name="workspace_id" class="form-control" id="workspace_id" disabled>
                        <option value="">Select Workspace</option>
                    </select>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label class=" control-label col-adj-2" for="task_id">Task: <sup>*</sup></label>
                <div class="col-adj-10">
                    <select name="task_id" class="form-control" id="task_id" disabled>
                        <option value="">Select Task</option>
                    </select>
                    <span class="error-message error text-danger"></span>
                </div>
            </div>
        </div>
        <div class="unit-adjustment-tab">
            <ul class="nav nav-tabs tab-list" id="add_adj_tab">
                <li class="active">
                    <a data-toggle="tab" class="active" href="#teammembers" id="tab_member" aria-expanded="true">Team Members</a>
                </li>
                <li class="">
                    <a data-toggle="tab" href="#allpeople" id="tab_user" aria-expanded="false">All People</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="teammembers" class="tab-pane fade active in">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class=" control-label col-adj-2" for="member_id">Name: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <select name="member_id" class="form-control" id="member_id" disabled>
                                    <option value="">Select Name</option>
                                </select>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Completed:</label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="text" class="form-control" name="mcompleted" id="mcompleted" disabled> <span class="hour-text">Hours (Current)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Remaining:</label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="text" class="form-control" name="mremaining" id="mremaining" disabled> <span class="hour-text">Hours (Current)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label  col-adj-2" for="area_title">Comment:</label>
                            <div class="col-adj-10">
                                <input type="text" class="form-control" name="mcomment" id="mcomment" disabled>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Remaining: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="number" class="form-control" name="madj_remaining" id="madj_remaining" disabled> <span class="hour-text">Hours (Adjusted)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class=" control-label col-adj-2" for="area_title">Comment: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <input type="text" class="form-control" name="madj_comment" id="madj_comment" disabled placeholder="50 characters" autocomplete="off" maxlength="50">
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="allpeople" class="tab-pane fade">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class=" control-label col-adj-2" for="user_id">Name: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <?php echo $this->Form->input('user_id', array('type' => 'select', 'options' => $user_list, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'user_id', 'empty' => 'Select Name', 'disabled' => 'disabled' )); ?>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Completed:</label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="text" class="form-control" name="ucompleted" id="ucompleted" disabled> <span class="hour-text">Hours (Current)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Remaining:</label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="text" class="form-control" name="uremaining" id="uremaining" disabled> <span class="hour-text">Hours (Current)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label  col-adj-2" for="area_title">Comment:</label>
                            <div class="col-adj-10">
                                <input type="text" class="form-control" name="ucomment" id="ucomment" disabled>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group unti-mb7">
                            <label class=" control-label col-adj-2" for="area_title">Remaining: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <div class="hour-filed"> <input type="number" class="form-control" name="uadj_remaining" id="uadj_remaining" disabled> <span class="hour-text">Hours (Adjusted)</span></div>
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group ">
                            <label class=" control-label  col-adj-2" for="area_title">Comment: <sup>*</sup></label>
                            <div class="col-adj-10">
                                <input type="text" class="form-control" name="uadj_comment" id="uadj_comment" disabled placeholder="50 characters" autocomplete="off" maxlength="50">
                                <span class="error-message error text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" id="submit_adj" class="btn btn-primary">Add</button>
    <button type="button" class="btn btn-primary outline-btn-t" data-dismiss="modal">Close</button>
</div>


<script type="text/javascript">
    $(() => {
        var pe_data = <?php echo json_encode($pe_data); ?>;
        console.log(pe_data['project_id'])
        function sort_object (a, b){
            var aName = a.label.toLowerCase();
            var bName = b.label.toLowerCase();
            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
        }

        // GET ALL WORKSPACES OF THE SELECTED PROJECT
        $("#project_id").off('change').on('change', function(event) {
            event.preventDefault();
            var project_id = $(this).val();
            $("#workspace_id").empty().prop('disabled', true);
            $('#workspace_id').append('<option value="">Select Workspace</option>');
            $("#task_id").empty().prop('disabled', true);
            $('#task_id').append('<option value="">Select Task</option>');
            $("#member_id").empty().prop('disabled', true);
            $('#member_id').append('<option value="">Select Name</option>');
            $("#user_id").prop('disabled', true);
            $('#madj_remaining').prop('disabled', true).val('');
            $('#madj_comment').prop('disabled', true).val('');
            $('#mcompleted').val('');
            $('#mremaining').val('');
            $('#mcomment').val('');
            $(this).parent().find('.error').text('');
            if(project_id){
                $.ajax({
                    url: $js_config.base_url + 'searches/get_related_data',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: project_id, type: 'workspace'},
                    success: function(response){
                        if(response.success){
                            $("#workspace_id").empty().prop('disabled', false);
                            $('#workspace_id').append('<option value="">Select Workspace</option>');
                            if(response.content){
                                var content = response.content.sort(sort_object);
                                $('#workspace_id').append(function() {
                                    var output = '';
                                    $.each(content, function(key, value) {
                                        output += '<option value="' + value.value + '">' + value.label + '</option>';
                                    });
                                    return output;
                                });
                            }
                        }
                    }
                })
            }
        });

        // GET ALL TASKS OF THE SELECTED WORKSPACE
        $("#workspace_id").off('change').on('change', function(event) {
            event.preventDefault();
            var workspace_id = $(this).val();
            $("#task_id").empty().prop('disabled', true);
            $('#task_id').append('<option value="">Select Task</option>');
            $("#member_id").empty().prop('disabled', true);
            $('#member_id').append('<option value="">Select Name</option>');
            $("#user_id").prop('disabled', true);
            $('#madj_remaining').prop('disabled', true).val('');
            $('#madj_comment').prop('disabled', true).val('');
            $('#mcompleted').val('');
            $('#mremaining').val('');
            $('#mcomment').val('');
            $(this).parent().find('.error').text('');
            if(workspace_id){
                $.ajax({
                    url: $js_config.base_url + 'searches/get_related_data',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: workspace_id, type: 'task'},
                    success: function(response){
                        if(response.success){
                            $("#task_id").empty().prop('disabled', false);
                            $('#task_id').append('<option value="">Select Task</option>');
                            if(response.content){
                                var content = response.content.sort(sort_object);
                                $('#task_id').append(function() {
                                    var output = '';
                                    $.each(content, function(key, value) {
                                        output += '<option value="' + value.value + '">' + value.label + '</option>';
                                    });
                                    return output;
                                });
                            }
                        }
                    }
                })
            }
        });

        // GET ALL MEMBERS OF THE SELECTED TASK
        $("#task_id").off('change').on('change', function(event) {
            event.preventDefault();
            var task_id = $(this).val();
            $("#member_id").empty().prop('disabled', true);
            $('#member_id').append('<option value="">Select Name</option>');
            $("#user_id").prop('disabled', true);
            $('#madj_remaining').prop('disabled', true).val('');
            $('#madj_comment').prop('disabled', true).val('');
            $('#mcompleted').val('');
            $('#mremaining').val('');
            $('#mcomment').val('');
            $(this).parent().find('.error').text('');
            if(task_id){
                $("#user_id").prop('disabled', false);
                $.ajax({
                    url: $js_config.base_url + 'searches/get_related_data',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: task_id, type: 'user'},
                    success: function(response){
                        if(response.success){
                            if(response.content){
                                $("#member_id").empty().prop('disabled', false);
                                $('#member_id').append('<option value="">Select Name</option>');
                                var content = response.content.sort(sort_object);
                                $('#member_id').append(function() {
                                    var output = '';
                                    $.each(content, function(key, value) {
                                        output += '<option value="' + value.value + '">' + value.label + '</option>';
                                    });
                                    return output;
                                });
                            }
                        }
                    }
                })
            }
        });

        // GET ACTIVE EFFORT AND PLAN EFFORT OF THE SELECTED MEMBER
        $("#member_id").off('change').on('change', function(event) {
            event.preventDefault();
            var member_id = $(this).val();
            var data = {
                project_id: $('#project_id').val(),
                workspace_id: $('#workspace_id').val(),
                task_id: $('#task_id').val(),
                member_id: member_id
            }
            $('#madj_remaining').prop('disabled', true).val('');
            $('#madj_comment').prop('disabled', true).val('');
            $('#mcompleted').val('');
            $('#mremaining').val('');
            $('#mcomment').val('');
            $(this).parent().find('.error').text('');
            $('#madj_comment').parent().find('.error').text('');
            if(member_id){
                $('#madj_remaining').prop('disabled', false).val(0);
                $('#madj_comment').prop('disabled', false).val('');
                $.ajax({
                    url: $js_config.base_url + 'searches/get_effort_plan',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response){
                        if(response.success){
                            if(response.content.el_efforts){
                                var el_efforts = response.content.el_efforts;
                                $('#mcompleted').val(el_efforts.completed_hours);
                                $('#mremaining').val(el_efforts.remaining_hours);
                                $('#mcomment').val(el_efforts.comment);
                            }
                            if(response.content.pe_efforts){
                                var pe_efforts = response.content.pe_efforts;
                                $('#madj_remaining').prop('disabled', false).val(pe_efforts.remaining_hours);
                                $('#madj_comment').prop('disabled', false).val(pe_efforts.comment);
                            }
                        }
                    }
                })
            }
        });

        // GET ACTIVE EFFORT AND PLAN EFFORT OF THE SELECTED USER
        $("#user_id").off('change').on('change', function(event) {
            event.preventDefault();
            var user_id = $(this).val();
            var data = {
                project_id: $('#project_id').val(),
                workspace_id: $('#workspace_id').val(),
                task_id: $('#task_id').val(),
                member_id: user_id
            }
            $('#uadj_remaining').prop('disabled', true).val('');
            $('#uadj_comment').prop('disabled', true).val('');
            $('#ucompleted').val('');
            $('#uremaining').val('');
            $('#ucomment').val('');
            $(this).parent().find('.error').text('');
            $('#uadj_comment').parent().find('.error').text('');
            if(user_id){
                $('#uadj_remaining').prop('disabled', false).val(0);
                $('#uadj_comment').prop('disabled', false).val('');
                $.ajax({
                    url: $js_config.base_url + 'searches/get_effort_plan',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response){
                        if(response.success){
                            if(response.content.el_efforts){
                                var el_efforts = response.content.el_efforts;
                                $('#ucompleted').val(el_efforts.completed_hours);
                                $('#uremaining').val(el_efforts.remaining_hours);
                                $('#ucomment').val(el_efforts.comment);
                            }
                            if(response.content.pe_efforts){
                                var pe_efforts = response.content.pe_efforts;
                                $('#uadj_remaining').prop('disabled', false).val(pe_efforts.remaining_hours);
                                $('#uadj_comment').prop('disabled', false).val(pe_efforts.comment);
                            }
                        }
                    }
                })
            }
        });

        $('#madj_comment, #uadj_comment').off('keyup').on('keyup', function(event) {
            event.preventDefault();
            $(this).parent().find('.error').text('');
        });

        $('#madj_remaining, #uadj_remaining').off('change').on('change', function(event) {
            event.preventDefault();
            var val = $(this).val();
            if(val == '' || val === undefined || val < 0){
                $(this).val(0)
            }
        });

        $.sel_tab = 'tab_member';
        // SUBMIT ALL DATA
        $("#submit_adj").off('click').on('click', function(event) {
            event.preventDefault();
            var user_id = $('#user_id').val(),
                member_id = $('#member_id').val(),
                project_id = $('#project_id').val(),
                workspace_id = $('#workspace_id').val(),
                task_id = $('#task_id').val(),
                madj_comment = $('#madj_comment').val(),
                uadj_comment = $('#uadj_comment').val();

            $('.error').text('');
            var error = false;
            if(project_id == '' || project_id === undefined){
                $('#project_id').parent().find('.error').text('Project is required');
                error = true;
            }
            if(workspace_id == '' || workspace_id === undefined){
                $('#workspace_id').parent().find('.error').text('Workspace is required');
                error = true;
            }
            if(task_id == '' || task_id === undefined){
                $('#task_id').parent().find('.error').text('Task is required');
                error = true;
            }
            if($.sel_tab == 'tab_member') {
                if(member_id == '' || member_id === undefined){
                    $('#member_id').parent().find('.error').text('Name is required');
                    error = true;
                }
                if(madj_comment == '' || madj_comment === undefined){
                    $('#madj_comment').parent().find('.error').text('Comment is required');
                    error = true;
                }
            }
            if($.sel_tab == 'tab_user') {
                if(user_id == '' || user_id === undefined){
                    $('#user_id').parent().find('.error').text('Name is required');
                    error = true;
                }
                if(uadj_comment == '' || uadj_comment === undefined){
                    $('#uadj_comment').parent().find('.error').text('Comment is required');
                    error = true;
                }
            }
            if(!error){
                var post = {
                    project_id: project_id,
                    workspace_id: workspace_id,
                    task_id: task_id
                };
                if($.sel_tab == 'tab_member') {
                    post['user'] = member_id;
                    post['pe_remaining'] = $('#madj_remaining').val();
                    post['pe_comment'] = madj_comment;
                }
                if($.sel_tab == 'tab_user') {
                    post['user'] = user_id;
                    post['pe_remaining'] = $('#uadj_remaining').val();
                    post['pe_comment'] = uadj_comment;
                }
                $.ajax({
                    url: $js_config.base_url + 'searches/add_adjustment',
                    type: 'POST',
                    dataType: 'json',
                    data: post,
                    success: function(response){
                        if(response.success){
                            $.pe_added = true;
                            $('#modal_add_adj').modal('hide');
                        }
                    }
                })
            }
        });

        $('#add_adj_tab').on('show.bs.tab', function(event){
            $.sel_tab = $(event.target).attr('id');
            if($.sel_tab == 'tab_member'){
                $('.error', $('#allpeople')).text('');
            }
            else if($.sel_tab == 'tab_user'){
                $('.error', $('#teammembers')).text('');
            }
        })

        // EDIT PLAN EFFORT
        $.getWsp = function(project_id, workspace_id){
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'searches/get_related_data',
                type: 'POST',
                dataType: 'json',
                data: {id: project_id, type: 'workspace'},
                success: function(response){
                    if(response.success){
                        $("#workspace_id").empty().prop('disabled', false);
                        $('#workspace_id').append('<option value="">Select Workspace</option>');
                        if(response.content){
                            var content = response.content.sort(sort_object);
                            $('#workspace_id').append(function() {
                                var output = '';
                                $.each(content, function(key, value) {
                                    var sel = (workspace_id == value.value) ? 'selected' : '';
                                    output += '<option value="' + value.value + '" '+sel+'>' + value.label + '</option>';
                                });
                                return output;
                            });
                        }
                        dfd.resolve(response)
                    }
                }
            })
            return dfd.promise();
        }
        $.getTask = function(workspace_id, task_id) {
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'searches/get_related_data',
                type: 'POST',
                dataType: 'json',
                data: {id: workspace_id, type: 'task'},
                success: function(response){
                    if(response.success){
                        $("#task_id").empty().prop('disabled', false);
                        $('#task_id').append('<option value="">Select Task</option>');
                        if(response.content){
                            var content = response.content.sort(sort_object);
                            $('#task_id').append(function() {
                                var output = '';
                                $.each(content, function(key, value) {
                                    var sel = (task_id == value.value) ? 'selected' : '';
                                    output += '<option value="' + value.value + '" '+sel+'>' + value.label + '</option>';
                                });
                                return output;
                            });
                        }
                        dfd.resolve(response);
                    }
                }
            })
            return dfd.promise();
        }
        $.getUser = function(task_id, user_id) {
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'searches/get_related_data',
                type: 'POST',
                dataType: 'json',
                data: {id: task_id, type: 'user'},
                success: function(response){
                    if(response.success){
                        if(response.content){
                            $("#member_id").empty().prop('disabled', false);
                            $('#member_id').append('<option value="">Select Name</option>');
                            var content = response.content.sort(sort_object);
                            $('#member_id').append(function() {
                                var output = '';
                                $.each(content, function(key, value) {
                                    var sel = (user_id == value.value) ? '' : '';
                                    output += '<option value="' + value.value + '" '+sel+'>' + value.label + '</option>';
                                });
                                return output;
                            });
                        }
                        dfd.resolve(response);
                    }
                }
            })
            return dfd.promise();
        }
        if(pe_data['project_id'] != undefined){
            $("#project_id").val(pe_data['project_id']);
            $.getWsp(pe_data['project_id'], pe_data['workspace_id']).done(function(){
                $.getTask(pe_data['workspace_id'], pe_data['element_id']).done(function(){
                    $('a[href="#allpeople"]').tab('show');
                    $("#user_id").val(pe_data['user_id']).trigger('change').prop('disabled', false);
                    $.getUser(pe_data['element_id'], pe_data['user_id']);
                })
            })

        }
    })
</script>