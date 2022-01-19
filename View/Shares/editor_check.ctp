 
<?php echo $this->Html->script('projects/plugins/paste.image.browser', array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/plugins/paste.image.at.position' , array('inline' => true)); ?>
<?php echo $this->Html->script('projects/plugins/advanced_editor_options', array('inline' => true)); ?>

<?php echo $this->Html->css('projects/bootstrap-input'); ?>
<?php echo $this->Html->css('projects/advanced_editor_options'); ?>

<script type="text/javascript">
jQuery(function($) {
  $.fn.strip_tags = function() {
		
		var text = $(this).val();
		var cleanText = text.replace(/(<([^>]+)>)/ig,"");
		//alert(cleanText);
		$('#tout').val(cleanText);
	}
 
	$('textarea#tin').off('keyup').on( 'keyup', function(event){
			
			$(this).strip_tags()
		
	})
})
$(window).on('load', function(event){
	setTimeout(function(){
		 
	}, 600)
})
</script>



<div class="row">
	<div class="col-xs-12">
		 
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left">Heading </h1> 
			</section>
		</div>


	<div class="box-content">

		<div class="row ">
			<div class="col-xs-12">
				<div class="box border-top margin-top">
					<div class="box-header no-padding" style="">
						<!-- MODAL BOX WINDOW -->
						<div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content"></div>
							</div>
						</div>
						<!-- END MODAL BOX -->
					</div>
					
					<div class="box-body clearfix list-shares" style="min-height: 800px">
						<!-- <textarea name="editor" id="editor" rows="5" cols="100">test</textarea> -->
						<form name="myform" METHOD=POST>
							<textarea name="tin" id="tin" rows="2" cols="30">sdf sadf dsaf dsf dsf<p>asdf d</p><p>asdf d<br /></p><p>asdf d</p><p>asdf d</p><p>asdf d</p><p>asdf d</p><p>asdf d</p></textarea>
							<textarea name="tout" id="tout" rows="2" cols="30"></textarea>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript" >
$(function() {
	$("#editor").Editor($.note_editor_config)
	$("#editor").Editor('setText', $(this).val())
})
</script>

<style>
#contentarea { 
	border: 1px solid #ccc; 
	height: 250px;
}
</style>