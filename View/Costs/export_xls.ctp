<?php echo $this->Html->css('projects/dropdown') ?>

<?php echo $this->Form->create('Cost', array('url' => array('controller' => 'costs', 'action' => 'export_datas',"project_id"=>$project_id), 'class' => 'form-bordered','id'=>"CostExportXlsForm")); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Generate Spreadsheet</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body">
    <div class="form-group">
        <label class="" for="txa_title">Select Project<span class="text-red">&nbsp;*&nbsp;</span>:</label>

        <?php
        $projects = array_map("html_entity_decode", $projects);
        $projects = array_map("html_entity_decode", $projects);
		echo $this->Form->input('Cost.project', array('label' => false, 'type' => 'select','options'=>$projects,"multiple"=>false, 'empty'=>'Select a Project', 'selected'=>$project_id,'class' => 'form-control title_limit title aqua custom-dropdown',  'id' => 'project','style'=>"width: 100%"));

        ?>

        <span class="error-message text-danger roject_list" ></span>
    </div>

    <div class="form-group mar-adjust-more">
        <label class="" for="txa_title">Give Spreadsheet a Title (50 char):</label>
        <?php

        echo $this->Form->input('Cost.title', ['label' => false,'div'=>false,'value'=>$project['Project']['title'], 'class' => 'form-control tittle_limit title', 'id' => 'export_report_title', 'escape' => true, 'placeholder' => 'max chars allowed 50', 'autocomplete'=>'off' ] ); ?>
        <span class="error-message text-danger descriptions" ></span>
    </div>

</div>
<div class="modal-footer">
    <button type="button" id="ExportsubmitForm" class="btn btn-success">Confirm</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>

<?php echo $this->Form->end(); ?>
<style>
    .radio label{width: 100%;}
</style>
<script type="text/javascript" >
    $(document).ready(function()  {
        var characters = 50;
        $("#export_report_title_old").keyup(function(){
            var $ts = $(this)
            if($(this).val().length > characters){
                $(this).val($(this).val().substr(0, characters));
            }
            var remaining = characters -  $(this).val().length;
            //$(this).next().html("You have <strong>"+  remaining+"</strong> characters remaining");
            $(this).next().html("Char 50 , <strong>" +$(this).val().length+ "</strong> characters entered.");
            if(remaining <= 10)
            {
                $(this).next().css("color","red");
            }
            else
            {
                $(this).next().css("color","red");
            }
        });
    });


$(document).ready(function(){
    $("#ExportsubmitForm").click( function (e) {
        e.preventDefault();
        $(".roject_list").text('')
        var pid = $("#project").val();
        var extitle = $("#export_report_title").val();

        if($("#project").val() == '' || $("#project").val() == null){
            $(".roject_list").text("Please select project.")
        } else if( extitle == '' || extitle == null){
            $(".descriptions").text("Please enter Spreadsheet title.")
        }else{
            $("#ExportsubmitForm").html("Generating...");
            setTimeout(function(){
                $("#ExportsubmitForm").html("Confirm");
                $("#CostExportXlsForm").submit()
                $("#modal_medium").modal('hide')
            },2000);
        }
    })


    $("#ExportsubmitForm__").click( function (e) {
        e.preventDefault(); //STOP default action
        var formURL = $("#CostExportXlsForm").attr("action");
        var postData = $("#CostExportXlsForm").serialize()
        $.ajax({
            url: formURL,
            type: "POST",
            dataType: "json",
            data: postData,
            async: false,
            beforeSend: function (response) {
                //$("#ExportsubmitForm").html("<img src='<?php echo SITEURL;?>/images/ajax-loader-new3.gif' class='' />");
                $("#ExportsubmitForm").html("Generating...");
            },
            complete: function (response) {
               $("#ExportsubmitForm").html("Confirm");
            },
            success: function (response) {
                console.log(response);
                $(".roject_list").text('')
                if(response.success){

                }else{
                    $(".roject_list").text(response.msg)
                }


            }
        });

    })


	$('body').delegate('#project', 'change', function(event){

		if( $(this).find("option[value='"+$(this).val()+"']").text() && $(this).val() != '' ){

			var projectTitle = $(this).find("option[value='"+$(this).val()+"']").text();
			$("#export_report_title").val(projectTitle);

		} else {
			$("#export_report_title").val('');
		}

	});

	$('body').delegate('#export_report_title', 'keyup focus', function(event){
            var characters = 50;
            event.preventDefault();
            // var $error_el = $(this).parent().find('.error');
            var $error_el = $(this).next();
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
   })



});
</script>

<style>
.radio.radio-warning{ margin: 0 0 0 10px;}
.mar-adjust{ margin : 0 0 10px;}
.mar-adjust-more{ margin : 0 0 25px;}
</style>
