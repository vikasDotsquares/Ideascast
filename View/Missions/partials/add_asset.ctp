<style>
#users-table thead tr {
	background-color: #000000;
	border-top: 2px solid #2c7dac;
}
.table-fixed {
	max-height: 400px;
	overflow-y: auto;
}
#link_embeded_holder {
	display: none;
}
</style>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Add <?php echo ucwords($type); ?></h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php 
		$users = $parent = null;
		if( isset($type) && !empty($type) ) { ?>
		
		
		<?php if( $type == 'link' ) { ?>
			 
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-font"></i></div>
						<input type="text" placeholder="Link title" id="link_title" class="form-control" name="data[Links][title]">
					</div>
				</div>
				
				<div id="link_options" class="form-group"> 
					<div class="radio-family plain">
						<div class="radio radio-danger">
							<input type="radio" class="rinput" data-collapse="link_url_holder" checked="checked" value="1" id="refer_external" name="data[Links][link_type]">
							<label for="refer_external"> External URL </label>
						</div>
					</div>
					
					<div class="radio-family plain">
						<div class="radio radio-danger">
							<input type="radio" class="rinput" data-collapse="link_embeded_holder" value="2" id="refer_embeded" name="data[Links][link_type]">
							<label for="refer_embeded"> Embeded Code </label>
						</div>
					</div>
				</div>
				
				<div class="form-group" id="link_url_holder">
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-link"></i></div>
						<input type="text" placeholder="Link URL" id="link_url" class="form-control" name="data[Links][references]">
					</div>
				</div>
				<div class="form-group" id="link_embeded_holder">
					<textarea style="width: 100% !important; resize: vertical;" id="link_embeded" name="data[Links][embed_code]" placeholder="Embeded Code" class="form-control" rows="5"></textarea>
				</div>
			 
		<?php }
		else if( $type == 'note' ) { ?>
			 
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-font"></i></div>
						<input type="text" placeholder="Link title" id="link_title" class="form-control" name="data[Links][title]">
					</div>
				</div>
				
				<div id="link_options" class="form-group"> 
					<div class="radio-family plain">
						<div class="radio radio-danger">
							<input type="radio" class="rinput" data-collapse="link_url_holder" checked="checked" value="1" id="refer_external" name="data[Links][link_type]">
							<label for="refer_external"> External URL </label>
						</div>
					</div>
					
					<div class="radio-family plain">
						<div class="radio radio-danger">
							<input type="radio" class="rinput" data-collapse="link_embeded_holder" value="2" id="refer_embeded" name="data[Links][link_type]">
							<label for="refer_embeded"> Embeded Code </label>
						</div>
					</div>
				</div>
				
				<div class="form-group" id="link_url_holder">
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-link"></i></div>
						<input type="text" placeholder="Link URL" id="link_url" class="form-control" name="data[Links][references]">
					</div>
				</div>
				<div class="form-group" id="link_embeded_holder">
					<textarea style="width: 100% !important; resize: vertical;" id="link_embeded" name="data[Links][embed_code]" placeholder="Embeded Code" class="form-control" rows="5"></textarea>
				</div>
			 
		<?php } ?>
		
		
		<?php } ?>
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" class="btn btn-success" >Save</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	
<script type="text/javascript" >
$(function(){
	$('body').delegate('input.rinput', 'change', function(){
		var val = $(this).val();
		if( val == 1 ) {
			$('#link_embeded_holder').slideUp(500, function(){
				$('#link_url_holder').slideDown();
			})
		}
		else if( val == 2 ) {
			$('#link_url_holder').slideUp(500, function(){
				$('#link_embeded_holder').slideDown();
			})
		}
	})
})
</script>