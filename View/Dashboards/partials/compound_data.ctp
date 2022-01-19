<?php
$projects_list = $align_id = null;


if( isset($align) && !empty($align) ) {
	$align_id = $align;
}

if( isset($slug) && !empty($slug) ) {

	if( $slug == 'projects' ) {
		$projects_list = $this->ViewModel->my_projects_list(['Project.id', 'Project.title' ], $align_id);
	}
	else if( $slug == 'shared_projects' ) {
		$projects_list = $this->ViewModel->shared_projects_list(['Project.id', 'Project.title' ], $align_id);
	}
	else if( $slug == 'received_projects' ) {
		$projects_list = $this->ViewModel->received_projects_list(['Project.id', 'Project.title' ], $align_id);
	}
	else if( $slug == 'group_received_projects' ) {
		$projects_list = $this->ViewModel->group_received_projects_list(['Project.id', 'Project.title' ], $align_id);
	}
	else if( $slug == 'propagated_projects' ) {
		$projects_list = $this->ViewModel->propagated_projects_list(['Project.id', 'Project.title' ], $align_id);
	}
}

 ?>


<div class="overview-box <?php echo $slug; ?>" id="<?php echo $slug; ?>" data-slug="<?php echo $slug; ?>">
	<!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 plisting">

		<ul class="list-group">
			<?php if($projects_list) { ?>
				<?php
					foreach($projects_list as $key => $val ) {
						$prj = $val['Project'];
						$prj_permit = (isset($val['ProjectPermission']) && !empty($val['ProjectPermission']) ) ? $val['ProjectPermission'] : null;

					?>
					<li class="list-group-item">
						<a href="<?php echo Router::url(array('controller' => 'projects', 'action' => 'index', $prj['id'])); ?>" class="project-title">
							<?php
								$prj_title = strip_tags($prj['title']);
								echo $prj_title; ?>
						</a>
						<span class="label label-default label-pill pull-right view_data tipText" title="Shows Information" data-placement="left" data-id="<?php echo $prj['id']; ?>" data-permitid="<?php echo $prj_permit['id']; ?>" data-share="<?php echo $this->ViewModel->getProjectPermit( $prj['id'], 'projects' ); ?>">
							<i class="fa fa-chevron-right "></i>
						</span>
					</li>
					<?php } ?>
				<?php }else{ ?>
				<li class="list-group-item text-center no-data">No Project</li>
			<?php } ?>
		</ul>

	</div> -->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 project_data">
		<div class="no-data"> Select a project </div>
	</div>
</div>

<div class="col-sm-12 comments_section">
	<div class="bottom-data clearfix">

		<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-zero"></div>
		<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-one"></div>
		<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-two"></div>
		<div class="col-xs-12 col-sm-12 col-md-3 comment-tabs tab-three"></div>

	</div>
</div>