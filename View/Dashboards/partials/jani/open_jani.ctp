



<?php echo $this->Html->css('projects/assistant_jani'); ?>
<?php

 ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<a onclick='responsiveVoice.speak("  ");' class="btn btn-xs btn-default" id="voice_btn" style="display: none;"><i class="fa fa-play"></i></a>
	<h3 class="modal-title" id="createModelLabel" style="line-height: 25px;"><i class="outline-assistant-icon"></i> Assistant</h3>
</div>

<!-- POPUP MODAL BODY -->
<div class="modal-body jani-wrapper">
	<a class="get-info collecting">Collecting Information</a>
	<div class="assistance" style="display: none;">
		<!-- <a class="get-info">Collecting Information</a> -->
	</div>
</div>

<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
	<div class="pull-left voice-btn-wrap">
		Voice: <a href="" class="btn btn-xs btn-default voice-btn"><i class="icon_voice audio-mute" ></i></a>
	</div>
	 <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
</div>
<!--  -->
<script type="text/javascript">
	$(function(){

		$.ajax({
			url: $js_config.base_url + 'dashboards/jani_partial',
			type: 'POST',
			// dataType: 'json',
			data: {},
			success: function(response){
				$('.collecting').remove()
				$('.assistance').html(response)
				$('.assistance').slideDown(100)
			}
		});

	})
</script>