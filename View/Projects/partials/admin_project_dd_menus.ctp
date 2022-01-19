<script type="text/javascript" >
$(function() {
	$('#smallModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
})
</script>
<style>
.dropdown-menu1 {
    background-clip: padding-box;
    background-color: #ECF0F5;
    border: 0px solid #cccccc;
    border-radius: 0;
    float: left;
    font-size: 14px;
    left: auto;
    list-style: outside none none;
    margin: 3px 0 0;
    max-height: 30px;
    max-width: 550px;
    padding:  0;
    position: absolute;
    right: 232px;
    text-align: left;
    top: -2%;
    z-index: 1000;
	overflow: hidden;
	/* transition: all 0.5s ease;  */
	-webkit-transition: all 500ms ease-out 1s;
    -moz-transition: all 500ms ease-out 1s;
    -o-transition: all 500ms ease-out 1s;
    transition: all ease-out 1s;
	opacity: 1;
	width: 148px;
}
.opened	{
	opacity: 1;
	visibility: visible;
	width: 148px;
}
.closed	{
	opacity: 0;
	visibility: hidden;
	width: 0;
}
.dropdown-menu2 {
    background-clip: padding-box;
    background-color: #ecf0f5;
    border: 0 solid #cccccc;
    border-radius: 0;
    float: left;
    font-size: 14px;
    left: auto;
    list-style: outside none none;
    margin: 4px 0 0;
    max-height: 41px;
    max-width: 550px;
    opacity: 1;
    padding: 0;
    position: absolute;
    right: 81px;
    text-align: left;
    top: -1px;
    transition: all 1s ease-out 0s;
    width: 148px;
    z-index: 1000;
}
.dropdown-menu1 > li, .dropdown-menu2 > li {
    float: left;
    padding: 0;
}
.dropdown-menu2 li .btn-group {
    margin-right: 3px;
}
#slide_out_trigger {
	padding-top: 4px;
	padding-bottom: 4px;
	border-radius: 5px 0px 0px 5px;
	margin-right: 0px;
	display: none;
}
#slide_out_trigger i {
	padding-left: 2px;
}
#back_btn {
	margin-top: 3px;
    padding-bottom: 4px;
    padding-top: 4px;
}

.dropdown-menu2 ul.dropdown-menu {
	border: 1px solid #b9b9b9 !important;
}
</style>
<section class="content-header clearfix">

		<div class=" pull-right">
			<!-- Project Options -->
			<div class="btn-group action">
				<?php
					if( isset($projects) && !empty($projects) ){
						$project_detail = $projects;

					?>

					<a data-toggle="modal" data-modal-width="600" class="btn btn-sm btn-success tipText" title="<?php tipText('projects-index-config-ws' ); ?>" href="<?php echo SITEURL?>sitepanel/projects/workspaceConfigPopup/<?php echo $project_detail['Project']['id'];?>" data-target="#modal_medium"><i class="fa fa-fw fa-wrench"></i></a>

					<?php
						// SHOW PROJECT EDIT LINK IF PROJECT ID VALUES ARE EXISTS
						if( isset($project_id) && !empty($project_id) ) { ?>
						<a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_project', $project_id, 'admin' => TRUE ), TRUE); ?>" class="btn btn-sm btn-success tipText" title="<?php echo tipText('Edit Project') ?>"><i class="fa fa-fw fa-pencil"></i> </a>
					<?php } ?>

					<a  class="btn btn-sm btn-success tipText" href="<?php echo SITEURL?>sitepanel/templates/create_workspace/<?php echo $project_detail['Project']['id'];?>" title="<?php tipText('create-ws'); ?>" ><i class="fa fa-fw fa-columns"></i> </a>
				<?php } ?>


			</div>
			<div class="btn-group action ">
				<a data-toggle="dropdown" class="btn btn-sm btn-success dropdown-toggle tipText" title="Export Options" type="button" href="javascript:void(0);">
					Export <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="#"><i class="halflings-icon user"></i> Word</a></li>
					<li><a href="#"><i class="halflings-icon user"></i> Power Point</a></li>
					<li><a href="#"><i class="halflings-icon user"></i> PDF</a></li>
				</ul>
			</div>

			<div class="btn-group action ">
				<a data-toggle="dropdown" class="btn btn-sm btn-success dropdown-toggle tipText" title="More Project Options" type="button" href="javascript:void(0);">
					Actions <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<!-- <li><a href="<?php //echo Router::Url(array('controller' => 'projects', 'action' => 'popups', 'project_reports', 'admin' => TRUE ), TRUE); ?>" data-toggle="modal" data-title="Project Report" id="project_reports"  data-target="#smallModal"><span class="more-align"><i class="fa fa-fw fa-bar-chart-o"></i></span> Project Report</a></li> -->

				</ul>
			</div>
			<div class="btn-group action ">
				<a id="btn_go_back" data-original-title="Go Back" href="<?php echo $this->request->referer(); ?>" class="btn btn-warning tipText pull-right btn-sm" > <i class="fa fa-fw fa-chevron-left"></i> Back</a>
			</div>
		</div>

</section>


<!-- Modal Small -->
<div class="modal modal-success fade " id="smallModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div>
	</div>
</div>
<!-- /.modal -->
