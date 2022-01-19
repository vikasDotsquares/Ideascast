<?php
echo $this->Html->script('projects/plugins/wysi-b3-editor/lib/js/wysihtml5-0.3.0', array('inline' => true));
echo $this->Html->script('projects/plugins/wysi-b3-editor/bootstrap3-wysihtml5', array('inline' => true));
echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>

<style>
.date_constraints_wrappers { overflow : hidden;}
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control{ cursor : default;}
	
</style>

<?php if( isset($response) && !empty($response) )  { ?>

		<?php
			echo $this->Form->create('Project', array('url' => array('controller' => 'studios', 'action' => 'create_project' ), 'class' => 'form-bordered', 'id' => 'modelFormAddProject', 'data-async' => ""));
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header comm-head">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="createModelLabel">
			<?php if(isset($response['project_id']) && !empty($response['project_id']) ){
				echo 'Edit Project';
			}else {
				echo 'Create Project';
			} ?>
		</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">


		<?php
		echo $this->Form->input('Project.studio_status', [ 'type' => 'hidden' ] );
		if(isset($response['project_id']) && !empty($response['project_id']) ) {
			echo $this->Form->input('Project.id', [ 'type' => 'hidden', 'value' => $response['project_id'] ] );
		}

		/* ?>

		<div class="form-group">

			<label for="category_id" >Category:</label>

			<?php
				echo $this->Form->input('Project.category_id', array(
				'options' => $categories,
				'type'=>'select',
				'default'=> (isset($this->data['Project']['category_id'])) ? $this->data['Project']['category_id'] : 0,'required'=>false,
				'label' => false,
				'div' => false,
				'id' => 'category_id',
				'class' => 'form-control'
			)); ?>
			<span class="error-message text-danger" ></span>
		</div>
		<?php */ ?>

		<div class="form-group">

			<label for="aligned_id" class="control-label" >Project Type:</label>

			<?php
			echo $this->Form->input('Project.aligned_id', array(
				'options' => $aligneds,
				'empty'=> 'Please select',
				'type'=>'select',
				'required'=>false,
				'label' => false,
				'div' => false,
				'id' => 'aligned_id',
				'class' => 'form-control'
			)); ?>
			<span class="error-message text-danger"></span>


		</div>

		<div class="form-group">
			<label  class="control-label"  for="title">Title:</label>
			<?php
			echo $this->Form->input('Project.title', [ 'type' => 'text', 'class' => 'form-control proj_title', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => 'max chars allowed 100', 'label' => false ] );
			?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>

		<div class="form-group">
			<label  class="control-label"  for="objective">Outcome:</label>
			<?php echo $this->Form->textarea('Project.objective', [ 'class'	=> 'form-control proj_objective', 'required'=>false, 'id' => 'objective', 'escape' => true, 'rows' => 6, 'placeholder' => 'max chars allowed 500' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>


		<div class="form-group">
			<label  class="control-label"  for="description">Description:</label>
			<?php echo $this->Form->textarea('Project.description', [ 'class'	=> 'form-control proj_description', 'required'=>false, 'id' => 'description', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>


		<div class="clearfix form-group">
			<label class="pull-left control-label col-sm-2" style="margin: 0px; padding: 0px;" for="">Color Theme:</label>
			<?php echo $this->Form->input('Project.color_code', [ 'type' => 'hidden', 'value' => (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code'])) ? $this->data['Project']['color_code'] : 'panel-gray', 'id' => 'color_code' ] ); ?>

			<div class="col-sm-6 pull-left" >
				<a href="#" data-color="panel-red" data-preview-color="bg-red" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-red') ? 'fa-check' : 'fa-square' ?> text-red"></i></a>
				<a href="#" data-color="panel-blue" data-preview-color="bg-blue" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-blue') ? 'fa-check' : 'fa-square' ?> text-blue"></i></a>
				<a href="#" data-color="panel-maroon" data-preview-color="bg-maroon" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-maroon') ? 'fa-check' : 'fa-square' ?> text-maroon"></i></a>
				<a href="#" data-color="panel-aqua" data-preview-color="bg-aqua" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-aqua') ? 'fa-check' : 'fa-square' ?> text-aqua"></i></a>
				<a href="#" data-color="panel-yellow" data-preview-color="bg-yellow" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-yellow') ? 'fa-check' : 'fa-square' ?> text-yellow"></i></a>
				<a href="#" data-color="panel-green" data-preview-color="bg-green" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-green') ? 'fa-check' : 'fa-square' ?> text-green"></i></a>
				<a href="#" data-color="panel-teal" data-preview-color="bg-teal" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-teal') ? 'fa-check' : 'fa-square' ?> text-teal"></i></a>
				<a href="#" data-color="panel-purple" data-preview-color="bg-purple" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-purple') ? 'fa-check' : 'fa-square' ?> text-purple"></i></a>
				<a href="#" data-color="panel-navy" data-preview-color="bg-navy" class="btn btn-default btn-xs el_color_box"><i class="fa <?php echo (isset($this->data['Project']['color_code']) && !empty($this->data['Project']['color_code']) && $this->data['Project']['color_code'] == 'panel-navy') ? 'fa-check' : 'fa-square' ?> text-navy"></i></a>
			</div>
			<div class="col-sm-4 pull-left preview" style="text-align: center; display: none;">
				<span style="width: 100%; display: inline-block; font-size: 12px;">Click color box to see preview here.</span>
			</div>

		</div>

		<div class="date_constraints_wrappers" style="">

			<div class="form-group clearfix col-sm-6">
				<label class="control-label" for="start_date" style="padding-top: 6px; padding-left: 0px;">Start Date:</label>
				<div class="input-group">
					<?php
					if( isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date']) ){
						$startdate = date('d M Y',strtotime($this->request->data['Project']['start_date']));
					} else {
						$startdate = '';
					}
					echo $this->Form->input('Project.start_date', [ 'type' => 'text', 'label' => false, 'div' => false,'value'=>$startdate, 'id' => 'start_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']);

					?>

					<div class="input-group-addon  open-start-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
				<span id="start_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>

			<div class="form-group clearfix col-sm-6">
				<label class="control-label" for="end_date" style="padding-top: 6px; padding-left: 0px;">End Date:</label>
				<div class="input-group">
					<?php
					if( isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']) ){
						$enddate = date('d M Y',strtotime($this->request->data['Project']['end_date']));
					} else {
						$enddate = '';
					}

					echo $this->Form->input('Project.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'value'=>$enddate, 'id' => 'end_date', 'required' => false,  'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>

					<div class="input-group-addon  open-end-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
                <span id="end_date_err" class="error-message text-danger"> </span>
				<span class="error chars_left" ></span>
			</div>
		</div>

	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button"  class="btn btn-primary submit_project submitted">Save</button>
		 <button type="button" class="btn outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>

	<?php
		$date = $this->Common->getDateStartOrEnd($response['project_id']);

		//$mindate =  date("d M Y");
		$mindate = isset($date['start_date']) && !empty($date['start_date']) ? date("d M Y",strtotime($date['start_date'])) : '';
		$maxdate = '';

	?>


	<script type="text/javascript" >
	$(function() {

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
			//dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
			changeMonth: true,
			changeYear: true,
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

				//$("#end_date").datepicker("setDate", selectedDate);
				$( "#end_date" ).datepicker( "option", "minDate", selectedDate );

			}
		});
		$( "#end_date" ).datepicker({
			minDate: '<?php echo $mindate;?>',
			maxDate: '<?php echo $maxdate;?>',
			// defaultDate: "+1w",
			// dateFormat: 'dd-mm-yy',
			dateFormat: 'dd M yy',
			changeMonth: true,
			changeYear: true,
			beforeShow: function( input, inst ) {
				setTimeout(function(){
					inst.dpDiv.zIndex(9999999)
				}, 2)
			},
			onClose: function( selectedDate ) {

				if(selectedDate != ''){
					$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
				}
			},
			onSelect: function(selectedDate) {

					this.value = selectedDate;
					$(this).val(selectedDate);

			}
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

			var $frm = $cb.closest('form#modelFormAddProject')
			var $hd = $frm.find('input#color_code[type=hidden]')
			var cls = $hd.val()

			setTimeout(function(){
				var foundClass = (cls.match (/(^|\s)panel-\S+/g) || []).join('')
				if( foundClass != '' ) {
					$hd.val('')
				}

				var applyClass = $cb.data('color');

				var splited = applyClass.split('-');

				$hd.val(applyClass);
			}, 100)

		})


		// var elm = [ $('#title'), $('#objective'), $('#description') ];
		/*var elm = [  $('#objective'), $('#description') ];
		wysihtml5_editor.set_elements(elm)
		$.wysihtml5_config = $.get_wysihtml5_config()
		// console.log($.wysihtml5_config )

		setTimeout( function() {

			// var title_config = $.wysihtml5_config;
			// $.extend( title_config, {"remove_underline": true,  'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }   ,'blockquote': { 'remove': 0 },'ol': { 'remove': 0 } ,'u': { 'remove': 0 }   } } })

			// $("#title").wysihtml5( title_config );

			$("#objective").wysihtml5( $.extend( $.wysihtml5_config, {"lists": false, "remove_underline": true, 'limit': 250, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }  ,'blockquote': { 'remove': 0 },'ol': { 'remove': 0 } ,'u': { 'remove': 0 }     } }}, $.wysihtml5_config) );

			$("#description").wysihtml5( $.extend( $.wysihtml5_config, {"remove_underline": false, 'lists': true, 'limit': 500, 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 }  ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }    } }}, $.wysihtml5_config)  );
		}, 500);*/


		$('.submit_project').on( "click", function(e){
			$.save_triggered = true;
			e.preventDefault();

			var $this = $(this),
			$form = $('form#modelFormAddProject'),
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
								var insert_id = response.content.id;
								if( insert_id ) {
									if( response.content.insert == true ) {
										$('#ajax_overlay').show()
										$('#create_model').modal('hide')
										history.pushState(null, null, $js_config.base_url + 'studios/index/' + insert_id);
										location.reload();
									}
									else {
										$('#create_model').modal('hide')
									}
								}
							}
						}
						else {

							$this.html('Save')

							if( ! $.isEmptyObject( response.content ) ) {

								$.each( response.content, function( ele, msg) {

									var $element = $form.find('[name="data[Project]['+ele+']"]')
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