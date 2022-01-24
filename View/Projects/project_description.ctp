
<?php

if( isset($data) && !empty($data) ) {
?>
	<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 id="modalTitle" class="modal-title" ><?php echo $data['Project']['title'] ?></h3>
	</div>
<div class="modal-body element-form clearfix "  >

	<div class="clearfix" style=" ">

		<div class="rows clearfix" >
			<div class="col-sm-12 text-bold ">Project Objective  </div>
			<div class="col-sm-12 text-left text-break"> <?php echo $data['Project']['objective'] ?></div>
		</div>

		<div class="rows clearfix" style="padding:15px 0 0 0 ">
			<div class="col-sm-12 text-bold ">Project Type</div>
			<div class="col-sm-12 text-left ">
				<?php
				$title = get_alignment($data['Project']['aligned_id']);

				echo isset($title['title']) ? $title['title'] : "N/A";
				?>
			</div>
		</div>

		<div class="rows clearfix" style="padding:15px 0 0 0 ">
			<div class="col-sm-12 text-bold ">Description  </div>
			<div class="col-sm-12 text-left text-break"> <?php echo $data['Project']['description'] ?></div>
		</div>
	</div>

</div>

	<div class="modal-footer">
		<button class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

<?php
}
 ?>



<script type="text/javascript" >
$(function(){
    $('#modal_medium').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

});


</script>