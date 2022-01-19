
<?php
/*	echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
	echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true));*/ ?>
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

<?php if( isset($response) && !empty($response) )  { ?>


<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">
			<?php if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
				echo 'Edit Workspace';
			}else {
				echo 'Add Workspace';
			} ?>
		</h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php
			echo $this->Form->create('Workspace', array('url' => array('controller' => 'missions', 'action' => 'save_workspace', $response['project_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddWorkspace', 'data-async' => ""));
		?>
		<span id="date-error-message" style="" class="error-message text-danger"> </span>


			<?php
			echo $this->Form->input('Workspace.studio_status', [ 'type' => 'hidden', 'value' => 0 ] );

			if(isset($response['workspace_id']) && !empty($response['workspace_id']) ){
					echo $this->Form->input('ProjectWorkspace.workspace_id', [ 'type' => 'hidden', 'value' => $response['workspace_id'] ] );
					echo $this->Form->input('Workspace.id', [ 'type' => 'hidden', 'value' => $response['workspace_id'] ] );
			}
			?>

			<?php  echo $this->Form->input('ProjectWorkspace.project_id', [ 'type' => 'hidden',  'value' => $response['project_id'] ] ); ?>
			<?php echo $this->Form->input('Workspace.template_id', [ 'type' => 'hidden', 'value' => 0 ] ); ?>


		<div class="form-group">
			<label class=" " for="title">Workspace Title:</label>
			<?php echo $this->Form->input('Workspace.title', [ 'type' => 'text', 'class' => 'form-control', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 50', 'label' => false ,  ] );   ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left text-danger" ></span>
		</div>

        <?php /* ?>
		<div class="form-group">
			<label class=" " for="description">Key Result Target:</label>
			<?php echo $this->Form->textarea('Workspace.description', [ 'class'	=> 'form-control',  'style' => 'resize: vertical', 'required'=>false, 'id' => 'description', 'escape' => true, 'rows' => 3, 'placeholder' => 'max chars allowed 250' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left text-danger" ></span>
		</div>
		 <?php */ ?>

		<div class="clearfix form-group">

			<label class="pull-left col-sm-2" style="margin: 0px; padding: 0px;" for="">Color Theme:</label>
			<?php echo $this->Form->input('Workspace.color_code', [ 'type' => 'hidden', 'value' => (isset($this->data['Workspace']['color_code']) && !empty($this->data['Workspace']['color_code'])) ? $this->data['Workspace']['color_code'] : 'bg-gray', 'id' => 'color_code' ] ); ?>

			<div class="col-sm-6 pull-left" >
				<a href="#" data-color="bg-red" data-preview-color="bg-red" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-red"></i></a>
				<a href="#" data-color="bg-blue" data-preview-color="bg-blue" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-blue"></i></a>
				<a href="#" data-color="bg-maroon" data-preview-color="bg-maroon" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-maroon"></i></a>
				<a href="#" data-color="bg-aqua" data-preview-color="bg-aqua" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-aqua"></i></a>
				<a href="#" data-color="bg-yellow" data-preview-color="bg-yellow" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-yellow"></i></a>
				<a href="#" data-color="bg-green" data-preview-color="bg-green" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-green"></i></a>
				<a href="#" data-color="bg-teal" data-preview-color="bg-teal" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-teal"></i></a>
				<a href="#" data-color="bg-purple" data-preview-color="bg-purple" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-purple"></i></a>
				<a href="#" data-color="bg-navy" data-preview-color="bg-navy" class="btn btn-default btn-xs el_color_box"><i class="fa fa-square text-navy"></i></a>
			</div>
			<div class="col-sm-4 pull-left preview" style="text-align: center; display: none;">
				<span style="width: 100%; display: inline-block; font-size: 12px;">Click color box to see preview here.</span>
			</div>

		</div>

		<div class="date_constraints_wrappers" style="">

			<div class="form-group col-sm-6 clearfix">
				<label class="" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
				<div class="input-group">
					<?php echo $this->Form->input('Workspace.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>

					<div class="input-group-addon  open-start-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
				<span id="start_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>

			<div class="form-group col-sm-6 clearfix">
				<label class="" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
				<div class="input-group">
					<?php echo $this->Form->input('Workspace.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>

					<div class="input-group-addon  open-end-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
                <span id="end_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-success submit_wsp submitted">Select Workspace Type</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	<?php
	$date = $this->Common->getDateStartOrEnd($project_id);

	//$mindate =  date("d M Y");
	//$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d M Y",strtotime($date['end_date'])) : '';

	if( FUTURE_DATE == 'on' ){
		$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y",strtotime($date['start_date'])) : '';
	} else {
		$mindate =  date("d M Y");
	}
	$maxdate = isset($date['end_date']) && !empty($date['end_date']) ? date("d M Y",strtotime($date['end_date'])) : '';
	?>
	<script type="text/javascript">
	$(function() {
		$('#title').focus();
        $('body').delegate('#title', 'keyup focus', function(event){
            var characters = 50;
            event.preventDefault();
            var $error_el = $(this).parent().find('.chars_left:first');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })


        $('body').delegate('#description', 'keyup focus', function(event){
            var characters = 250;
            event.preventDefault();
            var $error_el = $(this).parent().find('.chars_left:first');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

		$('#create_model').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});

		var start = '<?php echo date("d M Y");?>';
		var end = '<?php echo $maxdate;?>';
		$(".open-start-date-picker").click(function(){
			$("#start_date").datepicker('show').focus();
		})

		$(".open-end-date-picker").click(function(){
			$("#end_date").datepicker('show').focus();
		})

		$( "#start_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
			//defaultDate: "+1w",
			dateFormat: 'dd M yy',
			changeMonth: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {

				if(selectedDate != ''){
					$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
				}

			},
			onSelect: function(selectedDate) {
			   if(start == ''){
					this.value='';
					$("#dateAlertBox").modal("show");
				} else{
				$("#end_date").datepicker("setDate", selectedDate);
				  $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
			  }
			}
		});
		$( "#end_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
		   // defaultDate: "+1w",
			dateFormat: 'dd M yy',
			changeMonth: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
						inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {

				if(selectedDate != ''){
					//$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
				}
			},
			onSelect: function(selectedDate) {
				if(end == ''){
					this.value='';
					$("#dateAlertBox").modal("show");
				}
			}
		});

		// SET WORKSPACE COLOR THEME
		$(".el_color_box").on('click', function( event ) {
			event.preventDefault();

			$.each( $('.el_color_box'), function(i, el){
				$(el).find('i').addClass('fa-square').removeClass('fa-check')
			} )

			var $cb = $(this)
			$cb.find('i').addClass('fa-check').removeClass('fa-square')

			var $frm = $cb.closest('form#modelFormAddWorkspace')
			var $hd = $frm.find('input#color_code')
			var cls = $hd.val()

			var foundClass = ''
			if( cls != '' && cls != undefined ) {
				foundClass = (cls.match (/(^|\s)bg-\S+/g) || []).join('')
			}
			if( foundClass != '' ) {
				$hd.val('')
			}

			var applyClass = $cb.data('color')

			var splited = applyClass.split('-'),
			previewClass = 'bg-jeera',
			previewText = 'Color preview';


			if(splited[1] != '') {
				previewClass = 'bg-' + splited[1];
				previewText = $.ucwords(splited[1]);
			}

			$(".preview span").removeAttr('class')
				.attr('class', previewClass)
				.text(previewText)

			$hd.val(applyClass);
		})

/*
		var elm = [ $('#title') ];
		wysihtml5_editor.set_elements(elm)
		$.wysihtml5_config = $.get_wysihtml5_config()

		var title_config = $.extend( {}, {'remove_underline': true}, $.wysihtml5_config)

		// var title_config = $.wysihtml5_config;
		$.extend( title_config, { 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 } ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }     } } })


		$("#title").wysihtml5( title_config );

		var wysihtml5_data = $("#title").data('wysihtml5'),
			$editor = wysihtml5_data.editor;
		console.log($editor)

		// var $clipboard = $('<textarea />').insertAfter($("#title"));
		// $clipboard.css('display', 'none');
		// $clipboard.attr('name', 'data[Workspace][titles]');
		if(!document.execCommand('StyleWithCSS', false, false)) {
			document.execCommand('UseCSS', false, true);
        }

		function onPaste() {
			var $self = $(this.currentView.element);
			console.log($self.text())
			setTimeout(function(){
                var $content = $self.text();
				// $clipboard.html($self.html())
					console.log($content)
                $("[name='data[Workspace][title]']").val($content);
            },1000);
		};
		$editor.on("paste", onPaste);*/


		$('.submit_wsp').on( "click", function(e){

			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormAddWorkspace'),
				add_ws_url = $form.attr('action'),
				runAjax = true,
				project_id = $form.find('#ProjectWorkspaceProjectId').val();


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

						$this.html('Select Template')
						// REMOVE ALL ERROR SPAN
						$form.find('span.error-message.text-danger').text("")

						if( response.success ) {

							if( !$.isEmptyObject(response.content) ) {
								var insert_ws_id = response.content.id;
								if( insert_ws_id ) {
									$('#modal_box').modal('hide');
									// $('#modal_box').hide();

									setTimeout(function(){
										$('#modal_box2').modal({
											remote: $js_config.base_url + 'missions/get_templates/' + project_id + '/' + insert_ws_id
										})
										.show()
										.on('hidden.bs.modal', function(event) {
											$(this).removeData('bs.modal');
											$(this).find(".modal-content").html("");

											if( $.template_selected ) {
												location.href = $js_config.base_url + 'missions/index/project:' + project_id + '/workspace:' + insert_ws_id
											}
											else {
												$.ajax({
													url: $js_config.base_url + 'missions/delete_workspace',
													type: "POST",
													data: $.param({'project_id': project_id, 'workspace_id': insert_ws_id}),
													dataType: "JSON",
													global: false,
													success: function (response) {
														if(response.success) {
															var $selected_list = $('body').find('.idea-workspace-carousel li.selectable.selected');
															if( $selected_list.length > 0) {
																var selected_workspace_id = $selected_list.data('id');
																location.href = $js_config.base_url + 'missions/index/project:' + project_id + '/workspace:' + selected_workspace_id
															}
															else {
																location.href = $js_config.base_url + 'missions/index/project:' + project_id
															}
														}
													}
												})
											}
										})
									}, 800)
								}
							}
						}
						else {
							$this.html('Select Template')
							if( ! $.isEmptyObject( response.content ) ) {
								$.each( response.content, function( ele, msg) {

										var $element = $form.find('[name="data[Workspace]['+ele+']"]')
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