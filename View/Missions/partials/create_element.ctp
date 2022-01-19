<?php 
echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>

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
.date_constraints_wrappers{ overflow:hidden;}
</style>


<?php if( isset($response) && !empty($response) )  {  ?>


		<?php
			echo $this->Form->create('Element', array('url' => array('controller' => 'missions', 'action' => 'save_element', $response['area_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddElement', 'data-async' => "")); 
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">
			<?php if(isset($response['element_id']) && !empty($response['element_id']) ){
				echo 'Edit Task';
			}else {
				echo 'Quick Task Create';
			} ?>
		</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">

		
		<?php 
		
		if(isset($response['element_id']) && !empty($response['element_id']) ){
			echo $this->Form->input('Element.id', [ 'type' => 'hidden', 'value' => $response['element_id'] ] );
		}
		?>

		<?php  echo $this->Form->input('Element.area_id', [ 'type' => 'hidden',  'value' => $response['area_id'] ] ); ?>
		<?php echo $this->Form->input('Element.template_detail_id', [ 'type' => 'hidden', 'value' => 0 ] ); ?>
		
			
		<div class="form-group">
			<label class=" " for="title">Title:</label>
			<?php echo $this->Form->input('Element.title', [ 'type' => 'text', 'class' => 'form-control', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 50', 'label' => false , 'autocomplete' => 'off' ] );   ?>
			<span style="" class="error-message text-danger"> </span> 
			<span class="error chars_left" ></span>
		</div>
		
		<div class="form-group">
			<label class=" " for="description">Description:</label>
			<?php echo $this->Form->textarea('Element.description', [ 'class'	=> 'form-control', 'required'=>false, 'id' => 'description', 'escape' => true, 'rows' => 6, 'placeholder' => 'max chars allowed 750' ] ); ?>
			<span style="" class="error-message text-danger"> </span> 
			<span class="error chars_left" ></span>
		</div>
		
		
		<div class="clearfix form-group">
			<label class="pull-left col-sm-2" style="margin: 0px; padding: 0px;" for="">Color Theme:</label>
			<?php echo $this->Form->input('Element.color_code', [ 'type' => 'hidden', 'value' => (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code'])) ? $this->data['Element']['color_code'] : 'bg-gray', 'id' => 'color_code' ] ); ?>
			
			<div class="col-sm-6 pull-left" >
				<a href="#" data-color="panel-red" data-preview-color="bg-red" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-red') ? 'fa-check' : 'fa-square' ?> text-red"></i></a>
				<a href="#" data-color="panel-blue" data-preview-color="bg-blue" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-blue') ? 'fa-check' : 'fa-square' ?> text-blue"></i></a>
				<a href="#" data-color="panel-maroon" data-preview-color="bg-maroon" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-maroon') ? 'fa-check' : 'fa-square' ?> text-maroon"></i></a>
				<a href="#" data-color="panel-aqua" data-preview-color="bg-aqua" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-aqua') ? 'fa-check' : 'fa-square' ?> text-aqua"></i></a>
				<a href="#" data-color="panel-yellow" data-preview-color="bg-yellow" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-yellow') ? 'fa-check' : 'fa-square' ?> text-yellow"></i></a>
				<a href="#" data-color="panel-green" data-preview-color="bg-green" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-green') ? 'fa-check' : 'fa-square' ?> text-green"></i></a>
				<a href="#" data-color="panel-teal" data-preview-color="bg-teal" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-teal') ? 'fa-check' : 'fa-square' ?> text-teal"></i></a>
				<a href="#" data-color="panel-purple" data-preview-color="bg-purple" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-purple') ? 'fa-check' : 'fa-square' ?> text-purple"></i></a>
				<a href="#" data-color="panel-navy" data-preview-color="bg-navy" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Element']['color_code']) && !empty($this->data['Element']['color_code']) && $this->data['Element']['color_code'] == 'panel-navy') ? 'fa-check' : 'fa-square' ?> text-navy"></i></a>
			</div> 
			<div class="col-sm-4 pull-left preview" style="text-align: center; display: none;">
				<span style="width: 100%; display: inline-block; font-size: 12px;">Click color box to see preview here.</span> 
			</div>
		</div> 
			
			<div class="date_constraints_wrappers" style="">
				
				<div class="form-group col-sm-6 clearfix">
					<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
					<div class="input-group">
						<?php echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
						
						<div class="input-group-addon  open-start-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<span id="start_date_err" class="error-message text-danger"> </span> 
				</div>
				
				<div class="form-group col-sm-6 clearfix">
					<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
					<div class="input-group">
						<?php echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
						
						<div class="input-group-addon  open-end-date-picker calendar-trigger">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
					<span id="end_date_err" class="error-message text-danger"> </span> 
				</div>
			</div> 
			
			
		
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button"  class="btn btn-success submit_element submitted">Save</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>
	
	
	<?php if( isset($response['area_id']) && !empty($response['area_id']) )  {
		
		$workspace_id = area_workspace($response['area_id']);
		$project_id = workspace_pid($workspace_id);
		
		$project_detail = $this->ViewModel->getProjectDetail($project_id, -1);
		
	} ?>
	
	<?php
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
		
		if (isset($mindate_elm) && empty($mindate_elm)) {
			if (isset($mindate_workspace) && !empty($mindate_workspace)) {
				//$mindate_elm = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;
				$mindate_elm = $mindate_workspace;
				
				$messageVar = 'Workspace';
			} else if (isset($mindate_workspace) && empty($mindate_workspace)) {
				// $mindate_elm = ($mindate_project < $cur_date) ? $cur_date : $mindate_project; 
				$mindate_elm = $mindate_project;
				$messageVar = 'Project';
			} else {
				$mindate_elm = '';
			}
		} else if (isset($mindate_elm) && !empty($mindate_elm)) {
			//$mindate_elm = ($mindate_workspace < $cur_date) ? $cur_date : $mindate_workspace;
		}
		if (isset($maxdate_elm) && empty($maxdate_elm)) {
			if (isset($maxdate_workspace) && !empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_workspace;
				$messageVar = 'Workspace';
			} else if (isset($maxdate_workspace) && empty($maxdate_workspace)) {
				$maxdate_elm = $maxdate_project;
				$messageVar = 'Project';
			} else {
				$maxdate_elm = '';
			}
		}
		
		//$mindate_elm = ($mindate_elm < $cur_date) ? $cur_date : $mindate_elm;
		
		$mindate_elm_cal = $cur_date;
		
		// echo $maxdate_elm .'  ' .$cur_date;
	?>
	
	
	<script type="text/javascript" >
	$(function() {
		
		
		var start = '<?php echo $mindate_elm_cal; ?>';
        var end = '<?php echo $maxdate_workspace; ?>';
		
		$(".open-start-date-picker").click(function () {
            $("#start_date").datepicker('show').focus();
        })
        $(".open-end-date-picker").click(function () {
            $("#end_date").datepicker('show').focus();
        })
		
		
		var characters = 750;
		$("#description").keyup(function(){
			var $ts = $(this)
			if($(this).val().length > characters){
				$(this).val($(this).val().substr(0, characters));
			}
			var remaining = characters -  $(this).val().length; 
			$(this).next().html("Char 750 , <strong>" +$(this).val().length+ "</strong> characters entered.");
			if(remaining <= 10)
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
			else
			{
				$(this).next().css("color","#dd4b39");
				$(this).next().css("font-size","11px");
			}
		});	
		
        $("#start_date").datepicker({
            minDate: start,
            maxDate: end,
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

                    $("#end_date").datepicker("setDate", selectedDate);
                    $("#end_date").datepicker("option", "minDate", selectedDate);
                }
            }
        });
		
        $("#end_date").datepicker({
            minDate: start,
            maxDate: end,
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
                    $("#start_date").datepicker("option", "maxDate", start);
                }


            },
            onSelect: function (selectedDate) {
                if (selectedDate != '') {
                    start = selectedDate;
                    $("#end_date").datepicker("setDate", selectedDate);
                    $("#end_date").datepicker("option", "minDate", start);
                    $("#end_date").datepicker("option", "maxDate", end);
                    $("#start_date").datepicker("option", "maxDate", start);
                }
            }			
			
			
        });
        // end
		
		$('#modal_box').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});
		
		// SET WORKSPACE COLOR THEME
		$(document).on('click', ".el_color_box", function( event ) {
			event.preventDefault();
			var runAjax = true;
			$.each( $('.el_color_box'), function(i, el){
				$(el).find('i').addClass('fa-square').removeClass('fa-check')
			} )
			
			var $cb = $(this)
				$cb.find('i').addClass('fa-check').removeClass('fa-square')
			
			var $frm = $cb.closest('form#modelFormAddElement')
			var $hd = $frm.find('input#color_code[type=hidden]')
			var cls = ''
			
			cls = $hd.val()
			
			var foundClass = ''
			setTimeout(function(){
				if( cls != '' && cls != undefined ) {
					foundClass = (cls.match (/(^|\s)panel-\S+/g) || []).join('')
				}
				if( foundClass != '' ) {
					$hd.val('')
				}
				
				var applyClass = $cb.data('color');
				
				var splited = applyClass.split('-');
				
				$hd.val(applyClass);
			}, 1000)
			
		})
		
		/* var elm = [ $('#title'), $('#description') ];
		wysihtml5_editor.set_elements(elm) */
		$.wysihtml5_config = $.get_wysihtml5_config()
		console.log($.wysihtml5_config )
		
		setTimeout( function() {
			
			var title_config = $.wysihtml5_config;
			$.extend( title_config, {"remove_underline": true, 'limit': 50, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }   ,'blockquote': { 'remove': 0 },'ol': { 'remove': 0 } ,'u': { 'remove': 0 }   } } }) 
			
			$("#title").wysihtml5( title_config );
			
			/* $("#description").wysihtml5( $.extend( $.wysihtml5_config, {"remove_underline": false, 'lists': true, 'limit': 750, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }  ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }    } }}, $.wysihtml5_config)  ); */
		}, 500);
		
		
		$('.submit_element').on( "click", function(e) {

			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormAddElement'),
				add_ws_url = $form.attr('action'),
				runAjax = true,
				$area_input = $form.find('#ElementAreaId'),
				area_id = $area_input.val();
				
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
									$('#modal_box').modal('hide')
									setTimeout(function() { 
										$.ajax({
											type: 'POST',
											dataType: 'JSON',
											data: $.param({}),
											url: $js_config.base_url + 'missions/area_element_manage/' + area_id,
											global: false,
											success: function (response) {
												$("td#"+area_id+".area_box .box-body").html(response)
												$.bind_dragDrop()
											},
										});
									
										// var $selectedList = $('.idea-workspace-carousel li.selectable.selected');
										// $selectedList.removeClass('selected');
										// $selectedList.trigger('click'); 
									}, 200); 
								}
							}
						}
						else {
							$this.html('Save')
							if( ! $.isEmptyObject( response.content ) ) { 
								$.each( response.content, function( ele, msg) { 
										
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