<style type="text/css">
    textarea {
        resize: vertical;
    }
	section.content{
		min-height:500px !important;
	}
</style>
<?php //echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true)); ?>

<?php

if(isset($this->request->data['Workspace']) && !empty($this->request->data['Workspace']['title'])){

	$this->request->data['Workspace']['title'] = html_entity_decode($this->request->data['Workspace']['title']);
}

if(isset($this->request->data['Workspace']) && !empty($this->request->data['Workspace']['description'])){

	$this->request->data['Workspace']['description'] = html_entity_decode($this->request->data['Workspace']['description']);
}

$user_id = $this->Session->read('Auth.User.id');
$timeZone = getTimezoneDetail('Timezone',$user_id,'name');

$sign_off_dis = "";
if( isset($this->data['Workspace']['sign_off']) && $this->data['Workspace']['sign_off'] == 1){
	$sign_off_dis = "disabled";
}


?>
<script type="text/javascript" >
    $(function(){
		var lastX = 0;
		var currentX = 0;
		var page = 1;
		$('.scrolling-history').scroll(function () {
			currentX = $(this).scrollTop();

			if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 100)) {
				if ((currentX - lastX) > (20 * page)) {
					lastX = currentX;
					page++;
					$.post( $js_config.base_url+'workspaces/more_history/'+$js_config.workspace_id+'/page:' + page, {workspace_id: $js_config.workspace_id}, function(data) {
						$('.hmgo').append(data);
					});
				}
			}
		});
	})
    $(function () {

/*
        // var elm = [ $('#txa_title'), $('#txa_description') ];
        var elm = [$('#txa_title')];
        wysihtml5_editor.set_elements(elm)
        $.wysihtml5_config = $.get_wysihtml5_config()

        var title_config = $.extend({}, {'remove_underline': true}, $.wysihtml5_config)

        // var title_config = $.wysihtml5_config;
        $.extend(title_config, {'parserRules': {'tags': {'br': {'remove': 0}, 'ul': {'remove': 0}, 'li': {'remove': 0}, 'b': {'remove': 0}, 'i': {'remove': 0}, 'blockquote': {'remove': 0}, 'ol': {'remove': 0}, 'u': {'remove': 0}}}})


        $("#txa_title").wysihtml5(title_config);

        // $("#txa_description").wysihtml5( $.extend( $.wysihtml5_config, {'remove_underline': false, 'lists': true, 'limit': 250 }, $.wysihtml5_config) );
        // $.extend( $.wysihtml5_config, {'remove_underline': false, 'lists': true, 'limit': 250,  'parserRules': {'br': { 'remove': 1 }, 'tags': { 'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 } ,'blockquote': { 'remove': 0 }  ,'ol': { 'remove': 0 }  ,'u': { 'remove': 0 }   } }}, $.wysihtml5_config)

// Get title field iframe from its data and stop scrolling and seamless to disable scrollbars on different browsers.
// This also remove br tags from the text
        setTimeout(function () {
            // for Firefox
            if ($.check_browser() == 3) {

                $("iframe").each(function () {

                    var title_wysi = $('#txa_title').data("wysihtml5");

                    var iframe = title_wysi.editor.composer.iframe;

                    // $(this).load(function (event) {

                    if ($(this).is(iframe)) {

                        var $body = $(this).contents().find('body')

                        $body.bind('keyup', function (events) {

                            if (events.keyCode == 13) {
                                events.preventDefault();

                                $('br', $(this)).replaceWith('');

                                return;
                            }
                        })

                    }
                    // });
                });

            }
            else if ($.check_browser() == 1 || $.check_browser() == 2 || $.check_browser() == 4) {
                // For Google Chrome. Opera, Safari and IE
                var title_wysi = $('#txa_title').data("wysihtml5");

                if (title_wysi) {
                    var edtor = $('#txa_title').data("wysihtml5").editor;

                    if (edtor) {
                        var iram = edtor.composer.iframe;

                        $(iram).attr('scrolling', 'no')
                        $(iram).attr('seamless', 'seamless')
                        var $body = $(iram).contents().find('body')
                        $body.on('keyup', function (event) {

                            if (event.keyCode == 13) {
                                event.preventDefault();

                                $('br', $(this)).replaceWith('');
                                return;
                            }
                        })
                    }
                }
            }
        }, 3000)
*/

    })
</script>


<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">

        <!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
        <div class="row">
            <section class="content-header clearfix">

                <h1 class="pull-left"> <?php echo $data['page_heading']; ?>
                   <p class="text-muted date-time">Workspace:
                        <span>Created: <?php
							//echo date('d M Y h:i:s', strtotime($data['workspace']['created']));
							echo $this->Wiki->_displayDate($date = date('Y-m-d H:i:s',strtotime($data['workspace']['created'])),$format = 'd M Y H:i:s');
						?></span>
                        <span>Updated: <?php
							//echo date('d M Y h:i:s', strtotime($data['workspace']['modified']));
							echo $this->Wiki->_displayDate($date = date('Y-m-d H:i:s',strtotime($data['workspace']['modified'])),$format = 'd M Y H:i:s');
						?></span></p>
                </h1>

                <div class="btn-group action pull-right">
                    <a id="btn_go_back" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'index', $data['project_id'], 'admin' => FALSE), TRUE); ?>" class="btn btn-warning tipText btn-sm btn_go_back" > <i class="fa fa-fw fa-chevron-left"></i> Back</a>
                </div>

            </section>

        </div>
        <!-- END HEADING AND MENUS -->

            <span id="project_header_image" class="">
                <?php
                if (isset($project_id)) {
                    $is_owner = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
                    $original_owner = $this->Common->userprojectOwner($project_id, $this->Session->read('Auth.User.id'));
                    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
                }
                $style = '';
                if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
                    $style = 'top: -31px !important;';
                }
                echo $this->element('../Projects/partials/project_header_image', array( 'p_id' => $project_id, 'style' => $style ));
                ?>
            </span>
        <!-- MAIN CONTENT -->
        <div class="box-content">

            <div class="row ">

                <div class="col-xs-12">
                    <?php
                    echo $this->Form->create('Workspace', array('url' => array('controller' => 'workspaces', 'action' => 'update_workspace', $data['project_id'], $data['id']), 'class' => 'form-bordered', 'id' => 'modelFormUpdateWorkspace'));

                    ?>
                    <div class="fliter margin-top" style="padding :15px; margin:  0;  border-top-left-radius: 3px;    background-color: #f5f5f5;  text-align:right;   border: 1px solid #ddd;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd">
                        <button class="btn btn-sm btn-success <?php echo $sign_off_dis; ?>" type="submit">Save</button>

                        <a class="btn btn-sm btn-danger btn_go_back"  id="btn_go_back tipText" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_elements', $data['project_id'], $data['id'], 'admin' => FALSE), TRUE); ?>" class="btn btn-warning tipText btn-sm" > Cancel</a>
                    </div>


                    <div class="box noborder ">
                        <!-- CONTENT HEADING -->

                        <div class="box-header nopadding">

                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- END MODAL BOX -->

                        </div>
                        <!-- END CONTENT HEADING -->


                        <div class="box-body border-top clearfix">
                            <?php echo $this->Session->flash(); ?>



                            <?php echo $this->Form->input('Workspace.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => '')); ?>
 


                            <div class="form-group col-md-12">
                                <label class="" for="title">Workspace Title:</label>

                                <?php echo $this->Form->text('Workspace.title', [ 'class' => 'form-control', 'id' => 'txa_title', 'required' => false, 'escape' => true,  'placeholder' => 'max chars allowed 50']); ?>
                                <span style="" class="error-message text-danger"> <?php echo (isset($errors) && isset($errors['title']) && isset($errors['title'][0])) ? strip_tags($errors['title'][0]) : ''; ?></span>
                                <span class="error text-red chars_left" ></span>
                            </div>
                            <?php /* ?>
							<div class="form-group col-md-12">
                                <label class="" for="description">Key Result Target:</label>

								<a class="pull-right"><i class="fa fa-info template_info prophover" data-placement="top" data-content="<div class='template_create'>State a target and the measure. For example, Complete the evaluation by end of this month, or Investigate three options for the executive board, or Budget process for quarter.</div>" data-original-title="" title=""></i></a>

                                <?php echo $this->Form->textarea('Workspace.description', [ 'class' => 'form-control', 'id' => 'txa_description', 'required' => false, 'escape' => true, 'rows' => 3, 'placeholder' => 'max chars allowed 250']); ?>
                                <span style="" class="error-message text-danger"> <?php echo (isset($errors) && isset($errors['description']) && isset($errors['description'][0])) ? strip_tags($errors['description'][0]) : ''; ?></span>
                                <span class="error text-red chars_left" ></span>

                            </div> 
							<?php */ ?>

                            <div class="form-group col-md-12">
                                <label class="" style="margin:5px 0 0 0 " for=" ">Color Theme:</label>

                                <div class="col-md-12 nopadding-left">
                                    <?php echo $this->Form->input('Workspace.color_code', [ 'type' => 'hidden', 'id' => 'color_code']); ?>

                                    <div style="margin:0 0 0 -14px;" class="form-control noborder" >
                                        <a href="#" data-color="bg-red" data-preview-color="bg-red" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-red"></i></a>
                                        <a href="#" data-color="bg-blue" data-preview-color="bg-blue" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-blue"></i></a>
                                        <a href="#" data-color="bg-maroon" data-preview-color="bg-maroon" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-maroon"></i></a>
                                        <a href="#" data-color="bg-aqua" data-preview-color="bg-aqua" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-aqua"></i></a>
                                        <a href="#" data-color="bg-yellow" data-preview-color="bg-yellow" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-yellow"></i></a>
                                        <a href="#" data-color="bg-green" data-preview-color="bg-green" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-green"></i></a>
                                        <a href="#" data-color="bg-teal" data-preview-color="bg-teal" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-teal"></i></a>
                                        <a href="#" data-color="bg-purple" data-preview-color="bg-purple" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-purple"></i></a>
                                        <a href="#" data-color="bg-navy" data-preview-color="bg-navy" class="btn btn-default btn-xs el_color_box <?php echo $sign_off_dis;?>"><i class="fa fa-square text-navy"></i></a>
                                    </div>
                                </div>
                                <div class="col-md-2 preview" style="text-align: center; display: none;">
                                    <span style="margin-top: 8px; display: block;">Color preview</span>
                                </div>

                            </div>
                            <div class=" col-md-12">

                                <div id="date_constraints_dates" class="form-group clearfix">
                                    <div class=" input-daterange ">
										<div class="date-row row">
											<div class="col-sm-6 create-edit-date-f">
                                                <label class="control-label" for="start_date">Start date:</label>
                                                    <div class="input-group">
                                                        <?php
														if(!empty($this->request->data['Workspace']['start_date']))
														{
															$startDate = date('d M Y',strtotime($this->request->data['Workspace']['start_date']));

															//$startDate = $this->Wiki->_displayDate(date($startDate.' h:i:s A'),$format = 'Y/m/d');

															//$endDate = $this->Wiki->_displayDate(date($endDate.' h:i:s A'),$format = 'Y/m/d');



														} else {
															$startDate = date('d M Y');
														}



														echo $this->Form->input('Workspace.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date',  'required' => false, 'readonly' => 'readonly', $sign_off_dis, 'class' => 'form-control dates input-small']);
														?>


														<?php if( !empty($sign_off_dis) ){ ?>
														<div class="input-group-addon ">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
														<?php } else { ?>
														 <div class="input-group-addon open-start-date-picker calendar-trigger">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
														<?php } ?>


                                                    </div>
                                                </div>
                                                <div class="col-sm-6 create-edit-date-f">
        								            <label class="control-label " for="end_date">End date:</label>
                                                    <div class="input-group ">
                                                        <?php

													  if(!empty($this->request->data['Workspace']['end_date']))
														{
															$endDate = date('d M Y',strtotime($this->request->data['Workspace']['end_date']));

															//$endDate = $this->Wiki->_displayDate(date($endDate.' h:i:s A'),$format = 'd M Y');

														} else {
															$endDate = date('d M Y');
														}

														echo $this->Form->input('Workspace.end_date', [ 'type' => 'text',   'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false, 'readonly' => 'readonly', $sign_off_dis, 'class' => 'form-control dates input-small']); ?>

														<?php if( !empty($sign_off_dis) ){ ?>
														<div class="input-group-addon ">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
														<?php } else { ?>
														<div class="input-group-addon  open-end-date-picker calendar-trigger ">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
														<?php } ?>

                                                    </div>
											     </div>
                                        </div>
                                    </div>
                                </div>
<script>
														            sdate = new Date("<?php echo  $startDate; ?>").toLocaleString("en-US", {timeZone: "<?php echo $timeZone; ?>"});

																	sdate = new Date(sdate);												edate = new Date("<?php echo $endDate; ?>").toLocaleString("en-US", {timeZone: "<?php echo $timeZone; ?>"});

																	edate = new Date(edate);

														$(function(){
														 console.log(sdate);
														 console.log(edate);
														 setTimeout(function(){
															 $("#start_date").datepicker("setDate", sdate);
															$("#end_date").datepicker("setDate", edate);

														 })

														})
														</script>
                            </div>
                            <div class="form-group col-sm-12">
                                <button class="btn btn-sm btn-success  <?php echo $sign_off_dis; ?>" type="submit">Save</button>

								<a class="btn btn-sm btn-danger btn_go_back"  id="btn_go_back tipText" data-original-title="Go Back" href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_elements', $data['project_id'], $data['id'], 'admin' => FALSE), TRUE); ?>" class="btn btn-warning tipText btn-sm" > Cancel</a>
                            </div>

                        </div>



                    </div>
                    <?php echo $this->Form->end(); ?>
                    <?php
                    include 'activity/task_activity.ctp';
                    ?>
                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->

    </div>
</div>


<!-- set up the modal to start hidden and fade in and out -->
<div id="dateAlertBox" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                Please set project dates before setting workspace dates.
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button></div>
        </div>
    </div>
</div>


<!-- END OUTER WRAPPER -->
<?php
$date = $this->Common->getDateStartOrEnd($project_id);

//$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y", strtotime($date['start_date'])) : '';

if( !empty($date['start_date']) && !empty($date['start_date']) )
{
	$mindate = date('d M Y',strtotime($date['start_date']));
	//$mindate = $this->Wiki->_displayDate(date($mindate.' h:i:s A'),$format = 'd M Y');

} else {
	$mindate = '';
}

//$mindate =  date("d-m-Y");
//$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d M Y", strtotime($date['end_date'])) : '';

if( !empty($date['end_date']) && !empty($date['end_date']) )
{
	$maxdate = date('d M Y',strtotime($date['end_date']));
	//$maxdate = $this->Wiki->_displayDate(date($maxdate.' h:i:s A'),$format = 'd M Y');

} else {
	$maxdate = '';
}

?>
<script type="text/javascript" >
    $(function () {

		$('.prophover').popover({
			placement : 'left',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		})

        $('body').delegate('#txa_title', 'keyup focus', function(event){
            var characters = 50;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })
        /*$('#txa_title').on('keydown',function(e){
            flag = $(this).val().length;
            if(flag > 50){
               e.preventDefault();
            }
        });*/


        $('body').delegate('#txa_description', 'keyup focus', function(event){
            var characters = 250;
            event.preventDefault();
            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })



     //   var start = '<?php echo date("d-m-Y"); ?>';
        var start = '<?php echo $mindate; ?>';
        var end = '<?php echo $maxdate; ?>';
        $(".open-start-date-picker").click(function () {
            $("#start_date").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $("#end_date").datepicker('show').focus();
        })
		 
        $("#start_date").datepicker({
            minDate: start,
            maxDate: end,
            //defaultDate: "+1w",
            dateFormat: 'dd M yy',
            changeMonth: true,
			changeYear: true,
            //numberOfMonths: 3,
            onClose: function (selectedDate) {
                //if(selectedDate == ''){
                    //$("#end_date").datepicker("option", "minDate", start);
                //}else{
                    //$("#end_date").datepicker("option", "minDate", selectedDate);
                //}

            },
            onSelect: function (selectedDate) {
                if (start == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                } else {


					var startDate = $(this).datepicker('getDate');
					var endDate1 = $("#end_date").datepicker('getDate');
					
					 
					if ( $("#end_date").datepicker('getDate') == null || (startDate > endDate1 && startDate != null) ) {

					  if(new Date() > startDate){
						  $('#end_date').datepicker('option', 'minDate', 0);
						  
					  }else{
						  if( startDate > endDate1 ){
							$('#end_date').datepicker('option', 'minDate', startDate);
							 
						  }
					  }
					} else {
						$('#end_date').datepicker('option', 'minDate', startDate);
						 
					}
					if (( new Date() > startDate) && startDate != null) {
						 // $('#end_date').datepicker('option', 'minDate', 0);
						 
					}


                }
            }
        });
	 
        $("#end_date").datepicker({
            minDate: sdate,
            maxDate: end,
            //defaultDate: "+1w",
            dateFormat: 'dd M yy',
            changeMonth: true,
			changeYear: true,
            //numberOfMonths: 3,
            onClose: function (selectedDate) {
                //$("#start_date").datepicker("option", "maxDate", selectedDate);
				//$('#end_date').datepicker('option', 'minDate', 0);
            },
            onSelect: function (selectedDate) {
                if (end == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                }

            }
        });












        /* $('.btn_go_back').on('click', function(event) {
         event.preventDefault()
         var back_url = ''; //$js_config.base_url;

         if( $.global_var.go_back ) {
         back_url = $.global_var.go_back
         $.global_var.go_back = localStorage['go_back'] = '';
         }
         console.log(back_url)
         return false;
         })
         */

        $('#modal_medium').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal');
        });

        var previewClass = ($('input#color_code').val() != '') ? $('input#color_code').val() : 'bg-jeera'

        var splited = previewClass.split('-'),
                previewText = 'Color preview';


        if (splited[1] != '') {
            previewClass = 'bg-' + splited[1];

            previewText = splited[1];
        }
        $(".preview span").removeAttr('class')
                .attr('class', previewClass)
                .text(previewText)

        $("a[data-color=" + previewClass + "]").find('i').removeClass('fa-square').addClass('fa-check')
        // console.log($("a[data-color="+previewClass+"]"))
        // SET WORKSPACE COLOR THEME
        $(".el_color_box").on('click', function (event) {
            event.preventDefault();

            $.each($('.el_color_box'), function (i, el) {
                $(el).find('i').addClass('fa-square').removeClass('fa-check')
            })

            var $cb = $(this)
            $cb.find('i').addClass('fa-check').removeClass('fa-square')

            var $frm = $cb.closest('form#modelFormUpdateWorkspace')
            var $hd = $frm.find('input#color_code')
            var cls = $hd.val()
            // console.log($frm)
            // console.log(cls)
            var foundClass = (cls.match(/(^|\s)bg-\S+/g) || []).join('')
            if (foundClass != '') {
                $hd.val('')
            }

            var applyClass = $cb.data('color')

            var splited = applyClass.split('-'),
                    previewClass = 'bg-jeera',
                    previewText = 'Color preview';


            if (splited[1] != '') {
                previewClass = 'bg-' + splited[1];
                previewText = $.ucwords(splited[1]);
            }

            $(".preview span").removeAttr('class')
                    .attr('class', previewClass)
                    .text(previewText)

            $hd.val(applyClass);
        })



        $('.element-sign-off-restrict').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    $no = $cbox.find('#sign_off_no');

            var $span_text = $yes.find('span.text'),
            $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr("data-msg");
			BootstrapDialog.show({
	            title: '<h3 class="h3_style">Sign Off</h3>',
	            message: body_text,
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: false,
	            buttons: [
	                {
	                    label: ' Close',
	                   // icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });

        });

		$('#sign_off_no').click(function () {
		location.reload();
		})

        $('.element-sign-off').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
                    title = data.header,
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr('data-msg');

var post = {'data[Workspace][id]': id, 'data[Workspace][sign_off]': data.value},
                        data_string = $.param(post);
          //  $('#confirm-box').find('#modal_body').text(body_text)
          //  $('#confirm-box').find('#modal_header').text(title)


			BootstrapDialog.show({
			title: title,
			message: body_text,
			type: BootstrapDialog.TYPE_DANGER,
			draggable: true,
			buttons: [
				{
					//icon: '',
					label: ' Reopen',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url: $js_config.base_url + 'workspaces/workspace_signoff',
								type: "POST",
								data: data_string,
								dataType: "JSON",
								global: false,
								success: function (response) {
                                    if(response.success) {
                                        if(response.content){
                                            // send web notification
                                            $.socket.emit('socket:notification', response.content.socket, function(userdata){});
                                        }
                                    }
									 location.reload();
								}
							})
						).then(function( data, textStatus, jqXHR ) {
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							//dialogRef.getModalBody().html('<div class="loader"></div>');
							setTimeout(function () {
								dialogRef.close();

							}, 1500);
						})
					}
				},
				{
					label: ' Cancel',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
			]
		});


  /*           $('#confirm-box').modal({keyboard: true})
                    .on('click', '#sign_off_yes', function () {

                        // Ajax request to sign-off/reopen
                        var post = {'data[Workspace][id]': id, 'data[Workspace][sign_off]': data.value},
                        data_string = $.param(post);

                        $.ajax({
                            type: 'POST',
                            data: data_string,
                            url: $js_config.base_url + 'workspaces/workspace_signoff',
                            global: false,
                            dataType: 'JSON',
                            beforeSend: function () {
                                $span_text.css({'opacity': 0.5, 'color': '#222222'})
                                $div_progress.css({'width': '100%'})
                            },
                            complete: function () {
                                setTimeout(function () {
                                    $('#confirm-box').modal('hide')
                                    $span_text.css({'opacity': 1, 'color': '#ffffff'})
                                    $div_progress.css({'width': '0%'})
                                }, 3000)
                            },
                            success: function (response, statusText, jxhr) {

                                //return
                                if (response.success) {
                                    location.reload();
                                    setTimeout(function () {
                                       // location.reload(true)

                                    }, 2500)
                                    location.reload();
                                }
                            }
                        })
                    }); */
        });


		$('#confirm-box').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
			//$(this).find('.modal-content').html('')
		});

		$('#signoff_comment_box').on('hide.bs.modal', function(event) {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('')


        })

		$('.reopen-signoff').tooltip({
				 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
		})

		$('#signoff_comment_show').on('hidden.bs.modal', function(event) {

			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('');
			//$(".reopen-signoff").tooltip("")
		})

    })
</script>

<div class="modal fade" id="confirm-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header">

            </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn-sm btn_progress_wrapper" id="sign_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="sign_off_no" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>

<div class="modal modal-danger fade" id="signoff_comment_box" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content border-radius">

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-warning fade" id="confirm_signoff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<style>
	.btn_progress .btn_progressbar {
		background-color: rgba(0, 0, 0, 0.5);
		display: block;
		height: 36px;
		top: 0px;
		left: 0px;
		max-width: 100%;
		position: absolute;
		transition: width 3s ease 0s;
		width: 0;
	}

	.template_info {
		background: #00aff0 none repeat scroll 0 0;
		border-radius: 50%;
		color: #ffffff;
		font-size: 10px;
		height: 20px;
		line-height: 18px;
		padding: 0 8px;
		width: 20px;
	}

	.popover .popover-content .template_create  {
		font-size: 12px;
		font-weight: normal;
	}
</style>
