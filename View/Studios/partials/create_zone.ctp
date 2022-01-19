
<?php if( isset($response) && !empty($response) )  { ?>

		<?php
			echo $this->Form->create('Area', array('url' => array('controller' => 'studios', 'action' => 'save_zone', $response['workspace_id']), 'class' => 'form-bordered', 'id' => 'modelFormAddZone', 'data-async' => ""));
		?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header comm-head">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel">
			<?php if(isset($response['area_id']) && !empty($response['area_id']) ){
				echo 'Edit Area';
			}else {
				echo 'Add Area';
			} ?>
		</h4>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">


		<?php
		echo $this->Form->input('Area.studio_status', [ 'type' => 'hidden' ] );
		if(isset($response['area_id']) && !empty($response['area_id']) ) {
			echo $this->Form->input('Area.id', [ 'type' => 'hidden', 'value' => $response['area_id'] ] );
		}
		?>

		<?php  echo $this->Form->input('Area.workspace_id', [ 'type' => 'hidden',  'value' => $response['workspace_id'] ] ); ?>
		<?php //echo $this->Form->input('Area.template_detail_id', [ 'type' => 'hidden' ] ); ?>


		<div class="form-group">
			<label class="control-label " for="title">Title:</label>
			<?php
			echo $this->Form->input('Area.title', [ 'type' => 'text', 'class' => 'form-control zone_title', 'required'=>false, 'div' => false, 'id' => 'title', 'escape' => true, 'placeholder' => '100 chars', 'label' => false ] );   ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>

		<div class="form-group">
			<label class=" control-label" for="description">Purpose:</label>
			<?php echo $this->Form->textarea('Area.tooltip_text', [ 'class'	=> 'form-control zone_description', 'required'=>false, 'id' => 'description', 'escape' => true, 'rows' => 6, 'placeholder' => '200 chars','style'=>'resize:none;' ] ); ?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>

	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-primary submit_wsp submitted">Save</button>
		 <button type="button" class="btn outline-btn-t" data-dismiss="modal">Cancel</button>
	</div>
	<?php echo $this->Form->end(); ?>
	<script type="text/javascript" >
	$(function() {

		/*$("#title,#description").on('keyup', function(){
			var characters = ($(this).is('#title')) ? 100 : 200;
			if($(this).val().length > characters){
				$(this).val($(this).val().substr(0, characters));
			}
			$(this).parent().find('.chars_left:first').text('Chars: '+characters +", "+$(this).val().length + ' characters entered.')
		})*/
		$('body').delegate(".zone_title,.zone_description", 'keyup focus', function(event){
			var characters = ($(this).is('.zone_title')) ? 100 : 200;
			event.preventDefault();
			var $error_el = $(this).parent().find('.error-message');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('#create_model').on('hidden.bs.modal', function () {
			$(this).removeData('bs.modal');
		});

		$('.submit_wsp').on( "click", function(e){
			$.save_triggered = true;
			e.preventDefault();

			var $this = $(this),
				$form = $('form#modelFormAddZone'),
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
									console.log($form.find('[name="data[Area]['+ele+']"]'))

										var $element = $form.find('[name="data[Area]['+ele+']"]')
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