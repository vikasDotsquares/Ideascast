<?php echo $this->Html->css('projects/dropdown') ?>

<?php echo $this->Form->create('ExportData', array('url' => array('controller' => 'export_datas', 'action' => 'word_doc',"project_id"=>$project_id), 'class' => 'form-bordered','id'=>'ExportDataIndexForm')); ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Generate Report</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body">
    <div class="form-group">
        <label class="" for="txa_title">Select Project<!--<span class="text-red">&nbsp;*&nbsp;</span>-->:</label>

        <?php
			echo $this->Form->input('ExportData.project', array('label' => false, 'type' => 'select','options'=>$projects,"multiple"=>false, 'selected'=>$project_id,'class' => 'form-control title_limit title aqua',  'id' => 'project','style'=>"width:100%;" ));
        ?>

        <span class="error-message text-danger roject_list" ></span>
    </div>

    <div class="form-group mar-adjust-more">
        <label class="" for="txa_title">Report Title:</label>
        <?php
		$projectTitle = html_entity_decode(strip_tags($project['Project']['title']));
		//$projectTitle = str_replace("'", "", $projectTitle);
		//$projectTitle = str_replace('"', "", $projectTitle);
		//$projectTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $projectTitle);
        echo $this->Form->input('ExportData.title', ['label' => false,'div'=>false,'value'=>$projectTitle, 'class' => 'form-control tittle_limit title', 'id' => 'export_report_title', 'escape' => true, 'placeholder' => 'max chars allowed 50']); ?>
        <span class="error-message text-danger descriptions" ></span>
    </div>
    <?php /* ?><div class="form-group mar-adjust">
        <label class="" for="txa_title">Logo Image On Front Page:</label>
        <div class="radio radio-warning">
            <input type="radio" checked="checked" id="DocumentImageOnFrontPageNo" name="data[ExportData][DocumentImageOnFrontPage]" class="fancy_input"  value="N"  />
            <label class="fancy_labels" for="DocumentImageOnFrontPageNo">No</label>
        </div>
        <div class="radio radio-warning">
            <input type="radio"  id="DocumentImageOnFrontPageYes" name="data[ExportData][DocumentImageOnFrontPage]" class="fancy_input"  value="Y" />
            <label class="fancy_labels" for="DocumentImageOnFrontPageYes">Yes</label>
        </div>
        <span class="error-message text-danger descriptions" ></span>
    </div> <?php */ ?>
    <div class="form-group nomargin">
        <label class="" for="txa_title">Project Image On Front Page:</label>
        <div class="radio radio-warning">
            <input type="radio" checked="checked" id="ProjectImageOnFrontPageNo" name="data[ExportData][ProjectImageOnFrontPage]" class="fancy_input"  value="N"  />
            <label class="fancy_labels" for="ProjectImageOnFrontPageNo">No</label>
        </div>
        <div class="radio radio-warning">
            <input type="radio"  id="ProjectImageOnFrontPageYes" name="data[ExportData][ProjectImageOnFrontPage]" class="fancy_input"  value="Y" />
            <label class="fancy_labels" for="ProjectImageOnFrontPageYes">Yes</label>
        </div>
        <span class="error-message text-danger descriptions" ></span>
    </div>


</div>
<div class="modal-footer">
    <button type="button" id="ExportsubmitForm" class="btn btn-success">Generate</button>
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

        if($("#project").val() == '' || $("#project").val() == null){
            $(".roject_list").text("Please select project.")
        }else{
            $("#ExportsubmitForm").html("Generating...");
            setTimeout(function(){
                $("#ExportsubmitForm").html("Confirm");
		console.log($("#ExportDataIndexForm").serializeArray())
                $("#ExportDataIndexForm").submit()
                $("#modal_medium").modal('hide')
            },2000);
        }
    })


    $("#ExportsubmitForm__").click( function (e) {
        e.preventDefault(); //STOP default action
        var formURL = $("#ExportDataIndexForm").attr("action");
        var postData = $("#ExportDataIndexForm").serialize()
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

	$('body').delegate('#export_report_title', 'keyup focus', function(event){
            var characters = 50;
            event.preventDefault();
            // var $error_el = $(this).parent().find('.error');
            var $error_el = $(this).next();
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
   })

	$('body').delegate('#ExportDataIndexForm #project', 'change', function(event){

		if( $(this).find("option[value='"+$(this).val()+"']").text() && $(this).val() != '' ){

			var projectTitle = $(this).find("option[value='"+$(this).val()+"']").text();
			$("#export_report_title").val(projectTitle);

		} else {
			$("#export_report_title").val('');
		}

	});

});
</script>

<style>
.radio.radio-warning{ margin: 0 0 0 10px;}
.mar-adjust{ margin : 0 0 10px;}
.mar-adjust-more{ margin : 0 0 25px;}
</style>
