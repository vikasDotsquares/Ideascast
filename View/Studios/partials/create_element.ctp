

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

<style>
.date_box {
	display: block;
	border: 1px solid #ccc;
	padding: 20px 5px 10px 5px;
	display: none;
}
</style>


<?php if( isset($response) && !empty($response) )  {


  ?>


		<?php
			echo $this->Form->create('Element', array('url' => array('controller' => 'studios', 'action' => 'save_element', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddElement', 'data-async' => ""));
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header comm-head">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="createModelLabel">
			<?php if(isset($response['element_id']) && !empty($response['element_id']) ){
				echo 'Edit Task';
			}else {
				echo 'Add Task';
			} ?>
		</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">


		<?php
		echo $this->Form->input('Element.studio_status', [ 'type' => 'hidden' ] );
		if(isset($response['element_id']) && !empty($response['element_id']) ){
			echo $this->Form->input('Element.id', [ 'type' => 'hidden', 'value' => $response['element_id'] ] );
		}
		?>

		<?php  echo $this->Form->input('Element.area_id', [ 'type' => 'hidden',  'value' => $response['area_id'] ] ); ?>
		<?php echo $this->Form->input('Element.template_detail_id', [ 'type' => 'hidden', 'value' => 0 ] ); ?>


		<div class="form-group">
			<label class="control-label " for="title">Task Title:</label>
			<?php echo $this->Form->input('Element.title', [ 'type' => 'text', 'class' => 'form-control elem_title', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => '100 chars', 'label' => false , 'autocomplete' => 'off' ] );   ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>

		<div class="form-group">
			<label class="control-label " for="description">Task Description:</label>
			<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control elem_description', 'required'=>false, 'id' => 'description', 'escape' => true, 'rows' => 6,  'placeholder' => '500 chars','style'=>'resize:none;' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>


		<div class="clearfix form-group">
			<label class="pull-left control-label col-sm-2" style="margin: 0px; padding: 0px;" for="">Color Theme:</label>
			<?php echo $this->Form->input('Element.color_code', [ 'type' => 'hidden', 'value' => (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code'])) ? $this->data['Element']['color_code'] : 'panel-color-gray', 'id' => 'color_code' ] ); ?>

			<div class="col-sm-9 pull-left" >

				<ul class="color-ul">
                	<li class="color-items">
						<a href="#" data-color="panel-color-lightred" data-preview-color="bg-color-lightred" class="squares squares-default squares-xs task_color_box tipText" title="Light Red"><i class="square-color panel-text-lightred"></i></a>
						<a href="#" data-color="panel-color-red" data-preview-color="bg-color-red" class="squares squares-default squares-xs task_color_box tipText" title="Red"><i class="square-color panel-text-red"></i></a>
						<a href="#" data-color="panel-color-maroon" data-preview-color="bg-color-maroon" class="squares squares-default squares-xs task_color_box tipText" title="Maroon"><i class="square-color panel-text-maroon"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightorange" data-preview-color="bg-color-lightorange" class="squares squares-default squares-xs task_color_box tipText" title="Light Orange"><i class="square-color panel-text-lightorange"></i></a>
						<a href="#" data-color="panel-color-orange" data-preview-color="bg-color-orange" class="squares squares-default squares-xs task_color_box tipText" title="Orange"><i class="square-color panel-text-orange"></i></a>
						<a href="#" data-color="panel-color-darkorange" data-preview-color="bg-color-darkorange" class="squares squares-default squares-xs task_color_box tipText" title="Dark Orange"><i class="square-color panel-text-darkorange"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightyellow" data-preview-color="bg-color-lightyellow" class="squares squares-default squares-xs task_color_box tipText" title="Light Yellow"><i class="square-color panel-text-lightyellow"></i></a>
						<a href="#" data-color="panel-color-yellow" data-preview-color="bg-color-yellow" class="squares squares-default squares-xs task_color_box tipText" title="Yellow"><i class="square-color panel-text-yellow"></i></a>
						<a href="#" data-color="panel-color-darkyellow" data-preview-color="bg-color-darkyellow" class="squares squares-default squares-xs task_color_box tipText" title="Dark Yellow"><i class="square-color panel-text-darkyellow"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightgreen" data-preview-color="bg-color-lightgreen" class="squares squares-default squares-xs task_color_box tipText" title="Light Green"><i class="square-color panel-text-lightgreen"></i></a>
						<a href="#" data-color="panel-color-green" data-preview-color="bg-color-green" class="squares squares-default squares-xs task_color_box tipText" title="Green"><i class="square-color panel-text-green"></i></a>
						<a href="#" data-color="panel-color-darkgreen" data-preview-color="bg-color-darkgreen" class="squares squares-default squares-xs task_color_box tipText" title="Dark Green"><i class="square-color panel-text-darkgreen"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightteal" data-preview-color="bg-color-lightteal" class="squares squares-default squares-xs task_color_box tipText" title="Light Teal"><i class="square-color panel-text-lightteal"></i></a>
						<a href="#" data-color="panel-color-teal" data-preview-color="bg-color-teal" class="squares squares-default squares-xs task_color_box tipText" title="Teal"><i class="square-color panel-text-teal"></i></a>
						<a href="#" data-color="panel-color-darkteal" data-preview-color="bg-color-darkteal" class="squares squares-default squares-xs task_color_box tipText" title="Dark Teal"><i class="square-color panel-text-darkteal"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightaqua" data-preview-color="bg-color-lightaqua" class="squares squares-default squares-xs task_color_box tipText" title="Light Aqua"><i class="square-color panel-text-lightaqua"></i></a>
						<a href="#" data-color="panel-color-aqua" data-preview-color="bg-color-aqua" class="squares squares-default squares-xs task_color_box tipText" title="Aqua"><i class="square-color panel-text-aqua"></i></a>
						<a href="#" data-color="panel-color-darkaqua" data-preview-color="bg-color-darkaqua" class="squares squares-default squares-xs task_color_box tipText" title="Dark Aqua"><i class="square-color panel-text-darkaqua"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightblue" data-preview-color="bg-color-lightblue" class="squares squares-default squares-xs task_color_box tipText" title="Light Blue"><i class="square-color panel-text-lightblue"></i></a>
						<a href="#" data-color="panel-color-blue" data-preview-color="bg-color-blue" class="squares squares-default squares-xs task_color_box tipText" title="Blue"><i class="square-color panel-text-blue"></i></a>
						 <a href="#" data-color="panel-color-navy" data-preview-color="bg-color-navy" class="squares squares-default squares-xs task_color_box tipText" title="Navy"><i class="square-color panel-text-navy"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightpurple" data-preview-color="bg-color-lightpurple" class="squares squares-default squares-xs task_color_box tipText" title="Light Purple"><i class="square-color panel-text-lightpurple"></i></a>
						<a href="#" data-color="panel-color-purple" data-preview-color="bg-color-purple" class="squares squares-default squares-xs task_color_box tipText" title="Purple"><i class="square-color panel-text-purple"></i></a>
						<a href="#" data-color="panel-color-darkpurple" data-preview-color="bg-color-darkpurple" class="squares squares-default squares-xs task_color_box tipText" title="Dark Purple"><i class="square-color panel-text-darkpurple"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightmagenta" data-preview-color="bg-color-lightmagenta" class="squares squares-default squares-xs task_color_box tipText" title="Light Magenta"><i class="square-color panel-text-lightmagenta"></i></a>
						<a href="#" data-color="panel-color-magenta" data-preview-color="bg-color-magenta" class="squares squares-default squares-xs task_color_box tipText" title="Magenta"><i class="square-color panel-text-magenta"></i></a>
						<a href="#" data-color="panel-color-darkmagenta" data-preview-color="bg-color-darkmagenta" class="squares squares-default squares-xs task_color_box tipText" title="Dark Magenta"><i class="square-color panel-text-darkmagenta"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightgray" data-preview-color="bg-color-lightgray" class="squares squares-default squares-xs task_color_box tipText" title="Light Gray"><i class="square-color panel-text-lightgray"></i></a>
						<a href="#" data-color="panel-color-gray" data-preview-color="bg-color-gray" class="squares squares-default squares-xs task_color_box tipText" title="Gray"><i class="square-color panel-text-gray"></i></a>
						<a href="#" data-color="panel-color-darkgray" data-preview-color="bg-color-darkgray" class="squares squares-default squares-xs task_color_box tipText" title="Dark Gray"><i class="square-color panel-text-darkgray"></i></a>
					</li>
				</ul>
			</div>
			<div class="col-sm-4 pull-left preview" style="text-align: center; display: none;">
				<span style="width: 100%; display: inline-block; font-size: 12px;">Click color box to see preview here.</span>
			</div>
		</div>

			<div class="date_constraints_wrappers" style="">
				<div class="row">
				<div class="col-sm-6">
				<div class="form-group ">
					<label class="control-label" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
					<div class="input-group">
						<?php
						if( isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ){
							$eleStartDate = date('d M Y',strtotime($this->request->data['Element']['start_date']) );
						} else {
							$eleStartDate = '';
						}
						echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small','value'=>$eleStartDate]); ?>

						<div class="input-group-addon  open-start-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<span id="start_date_err" class="error-message text-danger"> </span>
				</div></div>
				<div class="col-sm-6">

				<div class="form-group">
					<label class="control-label" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
					<div class="input-group">
						<?php
						if( isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ){
							$eleEndDate = date('d M Y',strtotime($this->request->data['Element']['end_date']) );
						} else {
							$eleEndDate = '';
						}
						echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small','value'=>$eleEndDate]); ?>

						<div class="input-group-addon  open-end-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<span id="end_date_err" class="error-message text-danger"> </span>
				</div>
					</div></div>
			</div>



	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button"  class="btn btn-primary submit_element submitted">Save</button>
		 <button type="button" class="btn outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>


	<?php
		if( isset($response['area_id']) && !empty($response['area_id']) )  {

		 $workspace_id = area_workspace_id($response['area_id'],0);

		$project_id = workspace_pid($workspace_id);

		$project_detail = $this->ViewModel->getProjectDetail($project_id, -1);

		}

		$date_workspace = $this->Common->getDateStartOrEnd_elm($workspace_id);

		$cur_date = date("d M Y");
		$mindate_project = isset($project_detail['Project']['start_date']) && !empty($project_detail['Project']['start_date']) ? $project_detail['Project']['start_date'] : '';
		$maxdate_project = isset($project_detail['Project']['end_date']) && !empty($project_detail['Project']['end_date']) ? $project_detail['Project']['end_date'] : '';


		$mindate_workspace = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d M Y", strtotime($date_workspace['start_date'])) : '';
		$maxdate_workspace = isset($date_workspace['end_date']) && !empty($date_workspace['end_date']) ? date("d M Y", strtotime($date_workspace['end_date'])) : '';
		//$mindate_workspace = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;

		$mindate_elm = isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['start_date'])) : '';
		$maxdate_elm = isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ? date("d-m-Y", strtotime($this->request->data['Element']['end_date'])) : '';

		$messageVar = 'Element';
		if (!isset($mindate_elm) || empty($mindate_elm)) {
			if (isset($mindate_workspace) && !empty($mindate_workspace)) {
				$mindate_elm = $mindate_workspace;

				$messageVar = 'Workspace';
			} else if (isset($mindate_workspace) && empty($mindate_workspace)) {
				$mindate_elm = $mindate_project;
				$messageVar = 'Project';
			} else {
				$mindate_elm = '';
			}

			/* else if (isset($mindate_workspace) && empty($mindate_workspace)) {
				$mindate_elm = $mindate_project;
				$messageVar = 'Project';
			} */

		}
		else if (isset($mindate_elm) && !empty($mindate_elm)) {

		}

		$checkWspDate = false;
		if (isset($maxdate_elm) && empty($maxdate_elm)) {
			if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_workspace;
				$messageVar = 'Workspace';
				$checkWspDate = true;
			} else if (!isset($maxdate_workspace) || empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
				$messageVar = 'Project';
			} else {
				$maxdate_elm = '';
			}
			/* else if (!isset($maxdate_workspace) || empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
				$messageVar = 'Project';
			} */
		} else {

			if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_workspace;
				$messageVar = 'Workspace';
				$checkWspDate = true;
			} else if (!isset($maxdate_workspace) || empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
				$messageVar = 'Project';
			} else {
				$maxdate_elm = '';
			}

		}

		if( FUTURE_DATE == 'on' ){

			$mindate_wsp = isset($date_workspace['start_date']) && !empty($date_workspace['start_date']) ? date("d M Y", strtotime($date_workspace['start_date'])) : '';
			$mindate_elm_cal = $mindate_wsp;

		} else {
			$mindate_elm_cal = $cur_date;
		}

		//echo $mindate_elm_cal .'  ' .$maxdate_workspace;
	?>


<script type="text/javascript" >
	$(function() {
		$('.elem_title').focus();
		var res = $.parseJSON('<?php echo json_encode($response); ?>');

		// run only if task is in edit mode
		if(res && res.element_id) {
		    ;($.setSelectedColor = function() {
		        var previewClass = ($.trim($('input#color_code').val()) != '') ? $('input#color_code').val() : 'bg-jeera'

		        var splited = previewClass.split('-'),
		            previewText = 'Color preview';

		        if (splited[1] != '') {
		            previewClass = 'panel-' + splited[1] + '-' + splited[2];

		            previewText = splited[1];
		        }

		        $(".preview span").removeAttr('class')
		            .attr('class', previewClass)
		            .text(previewText)

		        $("a[data-color=" + previewClass + "]").find('i').removeClass('fa-square').addClass('fa-check')
		    })();
	    }

		$('body').delegate(".elem_title", 'keyup focus', function(event){
			var characters = 100
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
		$('body').delegate(".elem_description", 'keyup focus', function(event){
			var characters = 500
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})


		var start = '<?php echo $mindate_elm_cal; ?>';
        var end = '<?php echo $maxdate_elm; ?>';

		$(".open-start-date-picker").click(function () {
            $("#start_date").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $("#end_date").datepicker('show').focus();
        })

        $("#start_date").datepicker({
            minDate: start,
            maxDate: end,
            // dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
            changeMonth: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
            onClose: function (selectedDate) {
                if (selectedDate == '') {
                    $("#end_date").datepicker("option", "minDate", start);
                } else {
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
            },
            onSelect: function (selectedDate) {
                if (start == '') {
                    this.value = '';
                    $("#dateAlertBox").modal("show");
                } else {
                    //$("#end_date").datepicker("setDate", selectedDate);
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
            },
			beforeShowDay:function(date){
				 return ['<?php echo $checkWspDate;?>', ''];
			}
        });

        $("#end_date").datepicker({
            minDate: start,
            maxDate: end,
            // dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
            changeMonth: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                    $("#end_date").datepicker("setDate", selectedDate);
                    $("#end_date").datepicker("option", "minDate", start);
                    $("#end_date").datepicker("option", "maxDate", end);
                   // $("#start_date").datepicker("option", "maxDate", start);
				   //$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                    $("#end_date").datepicker("setDate", selectedDate);
                    $("#end_date").datepicker("option", "minDate", start);
                    $("#end_date").datepicker("option", "maxDate", end);
                  //  $("#start_date").datepicker("option", "maxDate", start);
                }
            },
			beforeShowDay:function(date){
				 return ['<?php echo $checkWspDate;?>', ''];
			}


        });
        // end


		$('#create_model').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});



		 /* var elm = [  $('#description') ];
		wysihtml5_editor.set_elements(elm)   */
		/*$.wysihtml5_config = $.get_wysihtml5_config()
		console.log($.wysihtml5_config )

		setTimeout( function() {

			var title_config = $.wysihtml5_config;
			$.extend( title_config, {"remove_underline": true, 'limit': 50, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }   ,'blockquote': { 'remove': 0 },'ol': { 'remove': 0 } ,'u': { 'remove': 0 }   } } })

			// $("#title").wysihtml5( title_config );

			//$("#description").wysihtml5( $.extend( $.wysihtml5_config, {"remove_underline": false, 'lists': true, 'limit': 750, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }  ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }    } }}, $.wysihtml5_config)  );
		}, 500);*/


		$('.submit_element').on( "click", function(e){
			$.save_triggered = true;
			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormAddElement'),
				add_ws_url = $form.attr('action'),
				runAjax = true;

			if( runAjax ) {
				runAjax = false;
				$.ajax({
					url: add_ws_url,
					type:'POST',
					data: $form.serialize(),
					dataType: 'json',
					beforeSend: function( response, status, jxhr ) {
						// Add a spinner in button html just after ajax starts
						$this.html('<i class="fa fa-spinner fa-pulse"></i>')
					},
					success: function( response, status, jxhr ) {

						$this.html('Save')
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {

							if( !$.isEmptyObject(response.content) ) {
								var insert_ws_id = response.content.id;
								if( insert_ws_id ) {
									$('#create_model').modal('hide')
								}
							}
						}
						else {
							$this.html('Save')
							if( ! $.isEmptyObject( response.content ) ) {
								console.log(response.content)
								$.each( response.content, function( ele, msg) {
									console.log($form.find('[name="data[Element]['+ele+']"]'))

										var $element = $form.find('[name="data[Element]['+ele+']"]')
										var $parent = $element.parent();

										if( $parent.find('span.error-message.text-danger').length  ) {
											$parent.find('span.error-message.text-danger').text(msg)
										}
										if(ele == 'start_date'){
											$("#start_date_err").text(msg);
										}
										if(ele == 'end_date'){
											$("#end_date_err").text(msg);
										}

									}
								)

							}
							if( ! $.isEmptyObject(response.date_error ) ) {
								$("#date-error-message").html('<div id="successFlashMsg" class="box box-solid bg-red" style="overflow: hidden;  "><div class="box-body"><p>'+response.date_error+'</p></div></div>')
							   setTimeout(function(){
									$("#date-error-message").fadeOut("500");
								},2000)
							}
						}
					}
				});
				// end ajax

			}
		})



	})
	</script>
<?php } ?>