<?php

################################################################################################################
$ws_exists = true;
$ws_count = $prj_count = 0;
if (isset($project_id) && !empty($project_id)) {
    $ws_count = $this->ViewModel->project_workspace_count($project_id);

    if (empty($ws_count)) {
        $ws_exists = false;
    }
}


$projectStartDate = date("d M, Y",strtotime($projects['Project']['start_date']));
$projectEndDate = date("d M, Y",strtotime($projects['Project']['end_date']));



$prj_disabled = '';
$prj_disabled_tip = '';
$prj_disabled_cursor = '';
if(isset($projects['Project']['sign_off']) && !empty($projects['Project']['sign_off']) && $projects['Project']['sign_off'] == 1 ){
	$prj_disabled = 'disable';
	$prj_disabled_tip = 'Project Is Signed Off';
	$prj_disabled_cursor = 'cursor:default !important;';
}

$quick_share_disable = '';
$quick_share_disable_tip = '';
$quick_share_disable_cursor = '';

$edit_button_disable = '';
$edit_button_disable_tip = '';
$edit_button_disable_cursor = '';
if(isset($projects['Project']['sign_off']) && !empty($projects['Project']['sign_off']) && $projects['Project']['sign_off'] == 1 && $ws_exists){

	$quick_share_disable = 'disable';
	$quick_share_disable_tip = 'Project Is Signed Off';
	$quick_share_disable_cursor = 'cursor:default !important;';

}
?>

<style>
.content-wrapper{
    background-color: #f1f3f4;
}
.sep-header-fliter{
    background-color: #f1f3f4;
    border-top: 1px solid #dcdcdc;
}
</style>


<section class="content-header clearfix nopadding ">
	<div class="project-detail header-progressbar">
		<div class="progressbar-sec project_progress_bar">
			<?php echo $this->element('../Projects/partials/project_progress_bar', ['project_id' => $project_id]); ?>
		</div>


	<?php $toc = 'bg-green';

	if (isset($project_id) && !empty($project_id)) {

    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

    $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
 	} ?>



    <div class="right-side-progress-bar">





        <!-- Project Options -->




<?php
if (isset($project_id) && !empty($project_id)) {
   ?>
            <?php   if( $ws_exists === true ) { ?>
              <?php  if(((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1)  ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )	) { ?>
              <div class="btn-group action" style="display:none;">
              <a data-toggle="dropdown" class="btn btn-sm btn-success dropdown-toggle tipText" title="Export Options" type="button" href="javascript:void(0);">
              DocBuilder <span class="caret"></span>
              </a>

              <ul class="dropdown-menu">
              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>/doc" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> Word</a></li>

              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>/ppt" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> Power Point</a></li>

              <li><a data-toggle="modal" data-modal-width="600" class="tipText" title="Select work space" href="<?php echo SITEURL?>projects/exportwsp/<?php echo $project_id;?>" data-target="#modal_medium" rel="tooltip" ><i class="halflings-icon user"></i> PDF</a></li>
              </ul>

              </div>
              <?php } }  ?>

<div class="btn-group action">
        <?php
            if (isset($ws_exists) && $ws_exists != false) {
				//********************* More Button ************************
				echo $this->element('more_button', array('project_id' => $project_id, 'user_id'=>$this->Session->read('Auth.User.id'),'controllerName'=>'costs'));
			}
		?>
</div>
<?php } ?>

    </div>

</section>
<style>
.icon-file-export {
   height: 17px;
	width: 17px;
	vertical-align: top;
	/* margin-top: 3px */;
}
.tooltip.default-tooltip {
    text-transform: none;
}
.taskcounters li {
	cursor: pointer;
}
</style>
<script type="text/javascript" >
	$(function(){
		$('.task_count').on('click', function(event) {
			event.preventDefault();
			var url = $(this).data('url');
			location.href = url;
			/* Act on the event */
		});
		$('.reward-distributed, .reward-distributed-from, .schedule-percent, .schedule-bar, .barTip').tooltip({
			'template': '<div class="tooltip default-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
			'placement': 'top',
			'container': 'body'
		})
		$('.cost-tooltip').tooltip({
			'placement': 'top',
			'container': 'body',
			'html': true
		})
	})
        $(document).on("click", ".workspace", function(e) {
            e.preventDefault();
            var message = $(this).attr("data-title");
            var headertitle = $(this).attr("data-headertitle");
           /*  $(".modal-body").html('<p>'+message+'</p>');
            $.model = $("#modal-alert");
            $.model.modal('show'); */

			BootstrapDialog.show({
				//title: 'Message',
				title: headertitle,
				type: BootstrapDialog.TYPE_DANGER,
				message: message,
				draggable: true,
				buttons: [{
					label: 'Close',
					// icon: 'fa fa-times',
					cssClass: 'btn-danger',
					action: function(dialogRef) {
						dialogRef.close();
					}
				}]
			});

        });
</script>
<div id="modal-alert" class="modal fade">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-radius-top">
        <div class="modal-header border-radius-top bg-red">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <i class="fa fa-exclamation-triangle"></i>&nbsp;Warning
        </div>
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        Please set project dates before setting workspace dates.
      </div>
      <!-- dialog buttons -->
      <div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">OK</button></div>
    </div>
  </div>
</div>
<style>
.icon-file-export {
    height: 17px;
    width: 17px;
    vertical-align: top;
    /* margin-top: 3px; */
}
</style>