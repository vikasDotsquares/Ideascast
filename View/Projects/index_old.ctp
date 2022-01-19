<?php
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
echo $this->Html->script('projects/color_changer', array('inline' => true));
$perPageWspLimit = 1;
$currentWspPage = 0;
 ?>

<style>
	.list-unstyled span{cursor:default !important}
	table td{display:table-cell}

	td.action-buttons a.btn, td.action-buttons button.btn, td.action-buttons span.btn {
		padding: 4px 7px !important;
	}
	.ws_color_box .colors b {
		border: 1px solid #dddddd !important;
	}
	.btn-group.btn-actions {
		min-width: 150px;
	}
	.box-icon {
		font-size: 20px;
		position: absolute;
		right: 10px;
		transition: all 0.3s linear 0s;
		z-index: 0;
		top: 28%;
		color: #ffffff;
		cursor: pointer;
	}
	.wsp-title{ height:25px; display:block;overflow:hidden; }
	.error-page > .error-content > h3{ font-size: 17px;}

	.popover-template-detail {
		display: block;
		min-width: 131px;
	}
	.popover-template-detail .timg {
		display: block;
		padding: 0 0 7px;
		text-align: center;
	}
	.popover-template-detail .timg img {
		margin: 0 auto;
	}
	.popover-template-detail .tdetail {
		display: block;
		vertical-align: top;
		font-weight: 600;
		font-size: 13px;
		text-align: center;
	}
	.icon_status_board {
		background-attachment: scroll !important;
		background-image: url("../../images/icons/spinner-2.png") !important;
		background-position: center center;
		background-repeat: no-repeat !important;
		background-size: 100% auto !important;
		display: inline-block;
		height: 18px;
		vertical-align: middle;
		width: 20px;
	}
	.hide-multi-delete {
		display: none !important;
	}
	.multi-remove-trigger {
		display: none;
		cursor: pointer;
	}

	.first-sec-icons .el-icons ul li > span {
		/*padding: .2em .1em .3em;*/
		min-width: 28px;
	}
	.load_more_wsp:hover {
	    /*background-color: #f0f0f0;*/
	}

	.load_more_wsp {
	    text-align: center;
	    display: block;
	    margin: 0 5px 20px 5px;
	    font-weight: 600;
	}
	.load_more_wsp.working {
	    pointer-events: none;
	    opacity: 0.7;
	}
	.loader-icon.stop {
	    visibility: hidden;
	}
	.show-more, .show-less {
	    padding: 10px;
	    cursor: pointer;
	}
	.show-more:hover  {
	    color: #5F9323;
	    background-color: #f0f0f0;
	}
	.show-less:hover {
	    color: #c00;
	    background-color: #f0f0f0;
	}

	.popover {
		max-width: none;
	}

	.popover-content {
		word-break:break-word;

	}

	.popover .popover-content .template_create {
		font-size: 12px;
		font-weight: normal;
		word-break: break-word;
    	max-width: 276px;
	}

	#successFlashMsg{
		top:12px;
		padding:8px 5px 2px 5px;
	}

</style>

<script type="text/javascript">
    function explode(){

		$('.content-header').trigger('click')
		$('.tooltip-inner').text(' ');
		//$('.tooltip-inner').hide();
		$('.tooltip').remove();
    }
     jQuery(function ($) {

		   $('.prophover').popover({
				placement : 'top',
				trigger : 'hover',
				html : true,
				container: 'body',
				delay: {show: 50, hide: 400}
			})


           setTimeout(function(){
           $('#modal_medium').on('hidden.bs.modal', function () {
                 $('.modal-content',$(this)).html("")
				  $('.tooltip-inner').text(' ');
		    $('.tooltip').remove();
                    setTimeout(explode, 500);
            })
         }, 3000);

	  $("body").delegate('.open_ws', 'click', function (event) {
	       event.preventDefault();

	       window.open($(this).data('remote'), '_self');
	  })

	  $js_config.add_ws_url = '<?php echo Router::Url(array("controller" => "templates", "action" => "create_workspace", $projects['Project']['id']), true); ?>/';

	  $js_config.step_2_element_url = '<?php echo $this->Html->url(array("controller" => "projects", "action" => "manage_elements"), true); ?>/';


	  $('.has-create-referer').tooltip();
	  $('.has-create-referer')
		  .tooltip('show')
		  .click(function (event) {
		       event.preventDefault();
		       $(this).removeData('bs.tooltip')
		  })


	  var url = $js_config.base_url + 'projects/lists';
	  $('body').find("#btn_go_back").attr('href', url)
	  $('body').delegate("#btn_go_back", 'click', function (event) {
	       var newurl = url
	       $(this).attr("href", newurl);
	       window.location = url;
	  })

	  // SHOW TOOLTIP ON EACH COLORBOX ON HOVER
	  // SET BOX BACKGROUND COLOR CLASS WITH CONTENT WRAPPER DIV
	  $('body').delegate('.color_bucket', 'click', function (event) {
	       event.preventDefault();

	       var $that = $(this);

	       $that.next(".ws_color_box").find('.el_color_box').colored_tooltip();

	  })

		if( !$('.content-header .pull-right').find('a').length ) {
			var st = $('.content-header .text-muted.date-time.pull-left').attr('style');
			$('.content-header .text-muted.date-time.pull-left').attr('style', st+'; margin-top: 7px !important')

		}

     })

     jQuery(window).load(function () {

		var show_progress = false;
		setTimeout(function () {
			   if (show_progress) {
					$('.progress').each(function () {
					 var percent = parseFloat($(this).data('width')),
						 percent_val = (percent > 0) ? percent.toFixed(2) : 0,
						 $bar = $(this).find('div.progress-bar'),
						 $text = $(this).find('div.percent');

					 // $bar.animate({ width: percent+'%' }, 100,   function(){ $text.html( percent.toFixed(2) + "%&nbsp;")});
						$bar.animate(
						{width: percent + '%'},
						{
						  duration: 500,
						  step: function (now, fx) {
						   $text.html(now.toFixed(2));
						  },
						  complete: function () {

						   $text.html(percent_val + "%&nbsp;");
						  }
					 });
					});
			   }
		  }, 500);
	});

	function resizeStuff() {
		$('.ellipsis-word').ellipsis_word();
		$('.key_target').textdot();
		console.log("resize");
	}
	$(function(){


		$('body').delegate('.sidebar-toggle', 'click', function() {
			if( !$('body').hasClass('sidebar-collapse') ) {
				$.popover_hack();
			}
			setTimeout(function(){

				$('.ellipsis-word').ellipsis_word();
				$('.ellipsis-word').ellipsis_word();
			},1);


		})

		$('.template-pophover').popover({
			trigger: 'hover',
			placement: 'bottom',
			html: true,
			container: 'body',
			// delay: {show: 50, hide: 400}
		})

		$.popover_hack = function() {

			$('.template-pophover').on('shown.bs.popover', function () {
				var data = $(this).data('bs.popover'),
				$tip = data.$tip,
				$arrow = data.$arrow;

				if( !$('body').hasClass('sidebar-collapse') ) {
					$tip.animate({
						left: parseInt($tip.css('left')) + 45 + 'px'
						}, 200, function(){
					})
					$arrow.css('left', '22%')
				}

			})
		}

		if( !$('body').hasClass('sidebar-collapse') ) {
			$.popover_hack();
		}

	})
</script>

<?php
$summary = null;
?>

<div class="row">

     <div class="col-xs-12">

	  <div class="row">
	       <section class="content-header clearfix">

			<?php
			 if (isset($projects) && !empty($projects)) {

				$projectMinDetail = $project_detail = $projects;
			?>
 		<h1 class="pull-left">
			<?php
			      echo $this->ViewModel->_substr($projectMinDetail['Project']['title'], 60, array('html' => true, 'ending' => '...'));
 			?>
 		</h1>
  		<?php

		    // LOAD PARTIAL FILE FOR TOP DD-MENUS
		    ?>
     			 <p class="text-muted date-time pull-left " style="min-width:100%;padding:5px 0;">Project:
     			      <span>Created: <?php
					  //echo date('d M Y h:i:s', $projectMinDetail['Project']['created']);
					  echo $this->Wiki->_displayDate( date('Y-m-d H:i:s',$projectMinDetail['Project']['created']),$format = 'd M, Y H:i:s' );
					  ?>
					  </span>

     			      <span>Updated: <?php
						//echo date('d M Y h:i:s', strtotime($project_detail['UserProject']['modified']));
						echo $this->Wiki->_displayDate( date('Y-m-d H:i:s',strtotime($project_detail['UserProject']['modified'])),$format = 'd M, Y H:i:s' );
					?></span>



			 <?php $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
			 if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
			 ?>

     			      <br /><span>Shared by: <?php echo $this->Common->userFullname($p_permission['ProjectPermission']['share_by_id']); ?></span>
     			      <span>Date Shared: <?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s',strtotime($p_permission['ProjectPermission']['created'])),$format = 'd M, Y H:i:s' );
					  ?></span>

			 <?php
			 } ?>
			  </p>
				<?php
			 } else {
			      echo "Project Summary";
			 }
			 ?>



	       </section>
	  </div>

	<span id="project_header_image" class="">
		<?php
		$style = '';
		if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
			$style = 'top: -31px !important;';
		}
		echo $this->element('../Projects/partials/project_header_image', array( 'p_id' => $p_id, 'style' => $style ));
		?>
	</span>
	  <?php
	  if (isset($projects) && !empty($projects)) {
	  	$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

	       $project_detail = $projects;
	       ?>
     	  <script type="text/javascript">
     	       ajaxObject = {
     		    ajaxUrl: '<?php echo Router::url(array('controller' => 'projects', 'action' => 'sortOrderWorkspaces')); ?>',
     		    id: '<?php echo $projects['Project']['id'] ?>',
     	       }
     	  </script>
     	  <div class="box-content">
     	       <div class="row ">
     		    <div class="col-xs-12">
				<?php echo $this->Session->flash(); ?>

				<div style="padding :15px; margin: 0; border-top-left-radius: 3px; display:inline-block; width:100%;    background-color: #f5f5f5;  overflow:visible;  border: 1px solid #ddd; min-height:63px;  border-top-right-radius: 3px;border-top:none;border-left:none;border-right:none; border-bottom:2px solid #ddd" class="fliter margin-top">
				<?php  echo $this->element('../Projects/partials/project_settings', array('menu_project_id' => $project_id));
				?>
				</div>

     			 <div class="box noborder ">

     			      <div class="box-header nopadding">

	     				   <!-- Modal Boxes // PASSWORD DELETE-->
							<div class="modal modal-danger fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content"></div>
								</div>
							</div>

	     				   <!-- Modal Large -->
	     				   <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     					<div class="modal-dialog modal-lg">
	     					     <div class="modal-content"></div>
	     					</div>
	     				   </div>
	     				   <!-- /.modal -->

	     				   <!-- Modal Large -->
	     				   <div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     					<div class="modal-dialog modal-md">
	     					     <div class="modal-content">

								 </div>
	     					</div>
	     				   </div>
	     				   <!-- /.modal -->

	     				   <!-- Modal Large -->
	     				   <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     					<div class="modal-dialog modal-sm">
	     					     <div class="modal-content"></div>
	     					</div>
	     				   </div>
	     				   <!-- /.modal -->

	     				   <!-- Modal Confirm -->
	     				   <div class="modal modal-warning fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     					<div class="modal-dialog">ffff
	     					     <div class="modal-content">

	     					     </div>
	     					</div>
	     				   </div>

     				   <!-- /.modal -->
     			      </div>


     			      <div class="box-body nopadding data-sort-wrapper" style="min-height: 800px;">

					<?php
						$project_workspace_count = $this->ViewModel->project_workspace_count( $project_id );

						$project_workspace_detail = $this->ViewModel->projectWorkspaces( $project_id, $perPageWspLimit, $currentWspPage );

						if (isset($project_workspace_detail) && !empty($project_workspace_detail)) {

					     echo $this->Form->input('ProjectWorkspace.project_id', array('label' => false, 'id' => 'project_id', 'type' => 'hidden', 'value' => $project_id));
					     ?>
					     <?php echo $this->Form->create('ProjectWorkspace', array('url' => array('controller' => 'projects', 'action' => 'sortOrderWorkspaces'), 'class' => 'form-horizontal form-bordered table-responsive', 'id' => 'dd-form' )); ?>



  				   	<table class="table table-bordered" data-id="<?php echo $project_id; ?>" >
	  					<colgroup>
	  					     <col width="1"/>
	  					     <col width="1"/>
	  					     <col width="1"/>
	  					     <col width="1"/>
	  					     <col width="1"/>
	  					</colgroup>
	  					<thead class="sort-theader">
	  					     <tr>
	  						  <th width="32%" style="text-align:center">Workspace</th>
	  						  <th width="22%" style="text-align:center">Key Result Target</th>
	  						  <th width="8%" style="text-align:center">Tasks</th>
	  						  <th width="27%" style="text-align:center">Resources</th>
	  						  <th width="11%" style="text-align:center">Actions <i class="fa fa-trash text-red multi-remove-trigger hide-multi-delete" data-pid="<?php echo $project_id; ?>"></i></th>
	  					     </tr>
	  					</thead>

	  					<tbody class="connectedSortable" id="sortable-list">
					    <?php
							if(isset($gpid) && !empty($gpid)) {
								$wwsid = $this->Group->group_work_permission_details($project_id, $gpid);
							}

							if(isset($p_permission) && !empty($p_permission))
							{
								$wwsid = $this->Common->work_permission_details($project_id, $this->Session->read('Auth.User.id'));
							}

							$project_workspace_count = (isset($wwsid) && !empty($wwsid)) ? count($wwsid) : $project_workspace_count;

							if(((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && 	$p_permission['ProjectPermission']['project_level'] ==1 ) || (isset($project_level) && $project_level==1) || (isset($wwsid)))

						foreach ($project_workspace_detail as $key => $project_workspace) {

							    $project_workspace_data = ( isset($project_workspace['ProjectWorkspace']) && !empty($project_workspace['ProjectWorkspace'])) ? $project_workspace['ProjectWorkspace'] : null;

							    $workspaceArray = ( isset($project_workspace['Workspace']) && !empty($project_workspace['Workspace'])) ? $project_workspace['Workspace'] : null;

							    // Show only the workspaces that are selected to display into the list. This status field is also used to show workspace names in leftbar menus.
							    $leftbar_status = $project_workspace_data['leftbar_status'];

							if ($leftbar_status) {

								if(( ((isset($wwsid) && !empty($wwsid))) &&  (in_array($project_workspace_data['id'], $wwsid)))  || ((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   )

								if (isset($workspaceArray['id']) && !empty($workspaceArray['id'])) {
									$workspace_areas = $this->ViewModel->workspace_areas($workspaceArray['id']);

									$totalAreas = $totalActElements = $totalInActElements = $totalUsedArea = $percent = 0;

									if (isset($workspace_areas) && !empty($workspace_areas)) {
										$user_project_id = (isset($user_project['UserProject']['id']) && !empty($user_project['UserProject']['id'])) ? $user_project['UserProject']['id'] : null;
										$progress_data = $this->ViewModel->countAreaElements($workspaceArray['id'], null, null, $project_id, $user_project_id, $user_project, $p_permission);
										if (isset($progress_data) && !empty($progress_data)) {
											// pr($progress_data);
											$totalAreas = $progress_data['area_count'];
											$totalUsedArea = $progress_data['area_used'];
											$totalActElements = $progress_data['active_element_count'];
											$totalInActElements = 0;

											$percent = ($totalUsedArea > 0 && $totalAreas > 0) ? ($totalUsedArea * 100) / $totalAreas : 0;
										}
									}


								$class_name = (isset($workspaceArray['color_code']) && !empty($workspaceArray['color_code'])) ? $workspaceArray['color_code'] : 'bg-gray';

								$create_elements_link = Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_id, $workspaceArray['id']));

								if( isset($workspaceArray['studio_status']) && empty($workspaceArray['studio_status']) ) {


							?>

	<tr id="<?php echo $project_workspace_data['id']; ?>" data-value="<?php echo $workspaceArray['id']; ?>" data-id="<?php echo $workspaceArray['id']; ?>" data-pid="<?php echo $project_detail['Project']['id']; ?>">
	<td>

	<?php echo $this->Form->input('ProjectWorkspace.' . $key . '.id', array('label' => false, 'id' => 'pwk_id_' . $key, 'type' => 'hidden', 'value' => $project_workspace_data['id'])); ?>

	<?php echo $this->Form->input('ProjectWorkspace.' . $key . '.sort_order', array('label' => false, 'type' => 'hidden', 'value' => $project_workspace_data['sort_order'], 'id' => 'pwk_sort_order_' . $key)); ?>

	   <div class="small-box panel <?php echo $class_name ?>">

		<a class="inner" href="<?php echo $create_elements_link; ?>">

		 <strong class="wsp-title tipText ellipsis-word" style="text-transform:none !important" title="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>" data-text="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>">
		 <?php echo htmlentities($workspaceArray['title'], ENT_QUOTES);?>
		 </strong>
			<?php
				$templateDataCount = $this->ViewModel->getWorkspaceTemplateDetails($workspaceArray['template_id']);
				$total_areas = (isset($templateDataCount['TemplateDetail']) && !empty($templateDataCount['TemplateDetail'])) ? count($templateDataCount['TemplateDetail']) : 0;

			$content = '<div class="popover-template-detail">
				<small class="timg">'.workspace_template( $workspaceArray['template_id'], true ).'</small>
				<small class="tdetail">
					<small style="font-weight: 500; font-size: 13px; "><b class="num">'.$total_areas.' Area Workspace</small>
				</small>
			</div>';
			?>
            <div class="reminder-sharing-d-out">
	<span style="font-size: 14px; float:left;">
					<i class="fa fa-th template-pophover"  data-content='<?php echo $content; ?>' data-html="true"></i>
				</span>
<div class="reminder-sharing-d-in">
			<span class="text-muted date-time">
				<span>Created:
				<?php
					echo ( isset($workspaceArray['created']) && !empty($workspaceArray['created'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($workspaceArray['created'])),$format = 'd M Y') : 'N/A';

				?></span>
				<span>Updated:
				<?php
				echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';

				?></span>
			</span>

			<span class=" sten-date" style=" ">
				<span>Start:
				<?php
				 echo ( isset($workspaceArray['start_date']) && !empty($workspaceArray['start_date'])) ? date('d M, Y',strtotime($workspaceArray['start_date'])) : 'N/A';
				?></span>
				<span>End:
				<?php
				echo ( isset($workspaceArray['end_date']) && !empty($workspaceArray['end_date'])) ? date('d M, Y',strtotime($workspaceArray['end_date'])) : 'N/A';

				?></span>
			</span>
            </div>
            </div>
		</a>
	   </div>
	</td>
	<?php
	$workspacetip = $workspaceArray['description'];
	?>
	<td style="vertical-align: top ! important; font-size: 12px; line-height: 16px; " class="key-target-wrap">
		 <div style="max-height: 80px;  overflow: hidden; max-width:408px; text-overflow: ellipsis; word-break: break-word;" data-placement="top" data-content="<div class='template_create'><?php echo nl2br(htmlentities($workspacetip, ENT_QUOTES)) ; ?></div> " class="key_target key_target_wsp   " data-toggle="popover" data-trigger="hover" data-delay="{show: 300, hide: 400}">
			<?php echo nl2br(htmlentities($workspaceArray['description'], ENT_QUOTES)) ; ?>
		 </div>
	</td>

	<td class="first-sec-icons">
		<span class="text-center el-icons">
			<ul class="list-unstyled">
				<li>
					<span class="label bg-mix" title=""><?php echo (isset($totalActElements) && !empty($totalActElements) ) ? $totalActElements : 0; ?></span>
					<span class="icon_elm btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Tasks') ?>"  ></span>
				</li>
				<li>
					<span class="label bg-mix">

						<?php echo (isset($progress_data['overdue_element_count']) && !empty($progress_data['overdue_element_count']) ) ? $progress_data['overdue_element_count'] : 0; ?>
					</span>
					<span class="btn btn-xs bg-element tipText no-change" title="<?php echo tipText('Overdue Statuses') ?>"  href="#"><i class="fa fa-exclamation"></i></span>
				</li>
			</ul>
		</span>
	</td>
	<td class="first-sec-icons">
	   <span class="text-center el-icons">
		<ul class="list-unstyled">

		 <li>
			  <span class="label bg-mix">
				<?php
				echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
				?>
			  </span>
			  <span class="btn btn-xs bg-maroon tipText no-change" title="<?php echo tipText('Links') ?>"  href="#"><i class="fa fa-link"></i></span>
		 </li>
		 <li>
			  <span class="label bg-mix">
				<?php
				echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
				?>
			  </span>
			  <span class="btn btn-xs bg-purple tipText no-change" title="<?php echo tipText('Notes') ?>"  href="#"><i class="fa fa-file-text-o"></i></span>
		 </li>
		 <li>
			  <span class="label bg-mix">
				<?php //pr($progress_data['assets_count']);
				echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
				?>
			  </span>
			  <span class="btn btn-xs bg-blue tipText no-change" title="<?php echo tipText('Documents') ?>"  href="#"><i class="fa fa-folder-o"></i></span>
		 </li>

		 <li>
			  <span class="label bg-mix">
				<?php
				echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
				?>
			  </span>
			  <span class="btn btn-xs bg-green tipText no-change" title="<?php echo tipText('Mind Maps') ?>"  href="#"><i class="fa fa-sitemap"></i></span>
		 </li>


		 <li> <?php $varDecision =  show_counters($workspaceArray['id'], 'decision');

		 ?>
			  <span class="label bg-mix"><?php echo (isset($varDecision) && !empty($varDecision) && $varDecision > 0) ? $varDecision: 0; ?></span>
			  <span class="btn btn-xs bg-orange tipText no-change" title="<?php echo tipText('Incomplete Decisions') ?>"  href="#"><i class="far fa-arrow-alt-circle-right"></i></span>
		 </li>
		  <?php
		  if ($workspaceArray['id'] == 116) {
			   // pr($out, 1);
		  }
		  ?>


		 <li>
			  <span class="label bg-mix"><?php //echo show_counters($workspaceArray['id'], 'feedback');
			  echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
			  ?></span>
			  <span class="btn btn-xs bg-teal tipText no-change" title="<?php echo tipText('Live Feedbacks') ?>"  href="#"><i class="fa fa-bullhorn"></i></span>
		 </li>

		 <li>
			  <span class="label bg-mix">
				<?php
				echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
				?>
			  </span>
			  <span class="btn btn-xs bg-yellow tipText no-change" title="<?php echo tipText('Live Votes') ?>"  href="#"><i class="fa fa-inbox"></i></span>
		 </li>
		</ul>
		</span>
	</td>

	<td class="action-buttons" style="text-align: center ! important;">

		<div class="btn-group btn-actions">
		<?php  $wid = encr($workspaceArray['id']); ?>
			<!--<a class="btn btn-xs <?php echo $class_name ?> tipText" title="Show Key Result"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-remote="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'show_detail', $wid, 'admin' => FALSE), TRUE); ?>" data-target="#modal_medium"  data-modal-width="600" data-toggle="modal" >
				<i class="fa fa-fw fa-eye"></i>
			</a>-->

		<?php


		 if((isset($wwsid) && !empty($wwsid))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission)  && $p_permission['ProjectPermission']['project_level'] ==1 )    )  )

		if(isset($gpid) && (isset($wwsid) && !empty($wwsid))){
		$wsEDDDIT =  $this->Group->group_wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

		$wsDELETE =  $this->Group->group_wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

		}else if((isset($wwsid) && !empty($wwsid))){
		$wsEDDDIT =  $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));

		$wsDELETE =  $this->Common->wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));
		}

		   if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) ) { ?>
			<a class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php tipText('Update Workspace Details', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_detail['Project']['id'], $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
			 <i class="fa fa-fw fa-pencil"></i>
			</a>
			<?php  } ?>
			<?php
			if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )    ) ) { ?>
			<a class="btn btn-xs <?php echo $class_name ?> tipText color_bucket" title="Color Options"  href="#" style="margin-right: 0 !important;">
				<i class="fa fa-paint-brush"></i>
			</a>
				<small class="ws_color_box" style="display: none; width: 86px">
					<small class="colors btn-group">
						<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
						<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
						<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
						<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
						<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
						<!-- <b data-color="bg-orange" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></b>	-->
						<b data-color="bg-teal" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></b>
						<b  data-color="bg-purple" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></b>
						<b data-color="bg-navy" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></b>
						<b data-color="bg-gray" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Remove Color"><i class="fa fa-times"></i></b>
				   </small>
			  </small>

	  <?php  } ?>

			<a class="btn btn-xs <?php echo $class_name ?> tipText open_ws" title="<?php tipText('Open Workspace', false); ?>"  href="#" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'manage_elements', $project_detail['Project']['id'], $workspaceArray['id'])); ?>" >
				<i class="fa fa-fw fa-folder-open"></i>
			</a>
		 			<?php
					if(((isset($wwsid) && !empty($wwsid)) && ($wsDELETE==1))  || (isset($project_level) && $project_level==1) || (((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) )  { ?>

			<!-- <button class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php tipText('Move to Bin', false); ?>" type="button" data-remote="<?php echo Router::url(array('controller' => 'projects', 'action' => 'trashWorkspace', $project_detail['Project']['id'], $workspaceArray['id'])); ?>" id="confirm_delete" data-target="<?php echo $project_workspace_data['id']; ?>" >
			 <i class="fa  fa-trash-o"></i>
			</button> // PASSWORD DELETE-->
			<a data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "workspaces", "action" => "delete_an_item", $project_detail['Project']['id'], $workspaceArray['id'], $project_workspace_data['id'], 'admin' => FALSE ), true ); ?>" type="button" class="btn btn-xs tipText delete-an-item <?php echo $class_name ?>" title="Move to Bin">
				<i class="fa fa-trash"></i>
			</a>

				<a class="btn btn-xs <?php echo $class_name ?> tipText multi-remove hide-multi-delete" title="Select" href="#" data-wid="<?php echo $workspaceArray['id']; ?>" >
					<i class="fa fa-square-o"></i>
				</a>
			<?php  } ?>

	   </div>

	</td>

</tr>
								 <?php
									}
								 }
							    }
						       }

						       ?>

	  					</tbody>

	  				   </table>
	  				   <?php
	  				   	if($project_workspace_count > $perPageWspLimit) { ?>
		  				   	<div class="load_more_wsp">
		  				   		<span class="show-more" data-limit="<?php echo $perPageWspLimit; ?>" data-current="<?php echo $currentWspPage; ?>" data-project="<?php echo $project_id; ?>" data-total="<?php echo $project_workspace_count; ?>">More Workspaces</span>
	  				   			<i class="fa fa-spinner fa-pulse loader-icon stop"></i>
	  				   			<span class="show-less" data-limit="<?php echo $perPageWspLimit; ?>" data-current="<?php echo $currentWspPage; ?>" data-project="<?php echo $project_id; ?>" data-total="<?php echo $project_workspace_count; ?>" data-results="<?php echo $perPageWspLimit; ?>" style="display: none;">Less Workspaces</span></div>
	  					<?php } ?>

						<?php echo $this->Form->end();

						} else {

						     $message = $html = '';

							//$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
							//$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

							//pr($projectMinDetail[]);

							$messagen = null;
	                        $startdate = isset($projectMinDetail['Project']['start_date']) && !empty($projectMinDetail['Project']['start_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['start_date'])) : '';

							$enddate = isset($projectMinDetail['Project']['end_date']) && !empty($projectMinDetail['Project']['end_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['end_date'])) : '';

							/* if( isset($projectMinDetail['Project']['start_date']) && !empty($projectMinDetail['Project']['start_date']) ){
								$startdate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectMinDetail['Project']['start_date'])),$format = 'Y-m-d');
							}

							if( isset($projectMinDetail['Project']['end_date']) && !empty($projectMinDetail['Project']['end_date']) ){

								$enddate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($projectMinDetail['Project']['end_date'])),$format = 'Y-m-d');

							} */




							//$curdate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime(date("Y-m-d"))),$format = 'Y-m-d');
							$curdate = date("Y-m-d");

	                        $class = '';
	                        $url = SITEURL.'templates/create_workspace/'.$project_id;

							if(isset($projectMinDetail['Project']['sign_off']) && $projectMinDetail['Project']['sign_off'] == 1){
								$messagen = 'Cannot create Workspace, Project is Signed off.';
								$url ='';$class = 'workspace disable';
							}else if(!empty($startdate) && $startdate > $curdate ){

								if( FUTURE_DATE == 'off' ){
									$messagen = 'Cannot add Workspace because Project is not live (start date not reached)';
									$url ='';$class = 'workspace disable';
								}

							}else if(!empty($enddate) && $enddate < $curdate){
								$messagen = 'Project date has expired.';
								$url ='';$class = 'workspace disable';
							}else if(isset ($startdate) && empty($startdate)){
								$messagen = 'Please add a schedule to this Project first.';
								$url ='';$class = 'workspace disable';
							}else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
								$messagen = 'You are not allowed to add workspace because project hasn\'t started yet.';
								$url ='';$class = 'workspace disable';
							}


						    if (isset($create_referer) && !empty($create_referer)) {




								if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
									$class = '';
									$url = SITEURL.'templates/create_workspace/'.$project_id;
									//echo $startdate." ".$curdate;
									//echo $enddate." ".$curdate;

									if(isset($url) && !empty($url)){

										  $message = 'Add a Workspace to the Project.';
										  $link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
										  $html = '<a data-title="" href="'.$link.'" class="btn btn-sm btn-success tipText " title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';

										}

									if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
										$message = 'Cannot create Workspace, Project is Signed off.';
										$url ='';$class = 'workspace disable';
									}else if(!empty($startdate) && $startdate > $curdate ){
										if( FUTURE_DATE == 'off' ){
											$message = 'Cannot add Workspace because Project is not live (start date not reached).';
											$url ='';$class = 'workspace disable';
										}
									}else if(!empty($enddate) && $enddate < $curdate){
										$message = 'Project date has expired.';
										$url ='';$class = 'workspace disable';
									}else if(isset ($startdate) && empty($startdate)){
										$message = 'Please add a schedule to this Project first.';
										$url ='';$class = 'workspace disable';
									}else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
										$message = 'You are not allowed to add workspace because project hasn\'t started yet.';
										$url ='';$class = 'workspace disable';
									}





						$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" href="'.$url.'" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';




								} else {
									  $message = "Sorry you don't have permission to create a Workspace.";
									  //$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
									  $link = '';
									  $html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';

								}


						     } else {

								/* $link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);

								$html = '<a data-title="" href="'.$link.'" class="btn btn-sm btn-success tipText " title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>'; */
								// $html = "Click<a class='' href='" . $link . "'> here </a> to create a new Workspace now.";

								if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )  ) {
                        $class = '';
                        $url = SITEURL.'templates/create_workspace/'.$project_id;
						//echo $startdate." ".$curdate;
						//echo $enddate." ".$curdate;


						if(isset($url) && !empty($url)){

										  $message = 'Add a Workspace to the Project.';
										  $link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
										  $html = '<a data-title="" href="'.$link.'" class="btn btn-sm btn-success tipText " title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';

										}


                        if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
                            $message = 'Cannot create Workspace, Project is Signed off.';
                            $url ='';$class = 'workspace disable';
                        }else if(!empty($startdate) && $startdate > $curdate ){
							if( FUTURE_DATE == 'off' ){
								$message = 'Cannot add Workspace because Project is not live (start date not reached).';
								$url ='';$class = 'workspace disable';
							}
                        }else if(!empty($enddate) && $enddate < $curdate){
                            $message = 'Project date has expired.';
                            $url ='';$class = 'workspace disable';
                        }else if(isset ($startdate) && empty($startdate)){
                            $message = 'Please add a schedule to this Project first.';
                            $url ='';$class = 'workspace disable';
                        }else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
                            $message = 'You are not allowed to add workspace because project hasn\'t started yet.';
                            $url ='';$class = 'workspace disable';
                        }



						$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" href="'.$url.'" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';


								} else {
										$message = "Sorry you don't have permission to create a Workspace.";
										//$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
										$link = '';
										$class = 'workspace disable';
										$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';
								}

				     	}


						     echo $this->element('../Projects/partials/error_data', array(
							 'error_data' => [
							     'message' => $message,
							     'html' => $html
							 ]
						     ));
						}
					?>

     			      </div><!-- /.box-body -->
     			 </div><!-- /.box -->
     		    </div>
     	       </div>
     	  </div>


<style>
   .custom-ui-widget-header-warning {z-index: 9999 !important;}
</style>
<script  type="text/javascript">
$(document).ready(function () {
	$.page = 0;
	$('body').on('click', '.show-more', function(event) {
		event.preventDefault();
		var $this = $(this),
			data = $(this).data(),
			new_current = (parseInt(data.current)+parseInt(data.limit)),
			$pre = $('.show-less');

		var lastpage = Math.ceil(data.total / data.limit);
		if ($.page == 0) $.page = 1;
		$.page++;

		$(this).parent().addClass('working');
		$(this).parent().find('.loader-icon').removeClass('stop');
		$.ajax({
			url: $js_config.base_url + 'projects/load_more_wsp',
			type: 'POST',
			// dataType: 'json',
			data: {project_id: data.project, limit: data.limit, current_page: new_current, total: data.total },
			success: function(response){
				$('.connectedSortable#sortable-list').append(response);
				$this.data('current', new_current);
				$this.parent().removeClass('working');
				$this.parent().find('.loader-icon').addClass('stop');
				if(lastpage > 1) {
					if ($.page > 1) {
						$pre.show();
					}
					else{
						$pre.hide();
					}
					if ($.page < lastpage) {
			            $this.show();
					}
			        else{
			        	$this.hide();
			        }
				}
			}
		})
	});

	$('body').on('click', '.show-less', function(event) {
		event.preventDefault();
		var $this = $(this),
			data = $(this).data(),
			remove_count = data.results;
		var $more = $('.show-more'),
			more_data = $more.data(),
			current_page = more_data.current
			new_current = (parseInt(current_page)-parseInt(more_data.limit));

		var lastpage = Math.ceil(data.total / data.limit);
		$.page--;
		if(lastpage > 1) {
			if ($.page > 1) {
				$this.show();
			}
			else{
				$this.hide();
			}
			if ($.page < lastpage) {
	            $more.show();
			}
	        else{
	        	$more.hide();
	        }
		}
		$this.parent().find('.loader-icon').removeClass('stop');
		$this.parent().addClass('working');
		setTimeout(function(){
			if($(".connectedSortable").find('tr').length > data.limit) {
				var i = 1;
				$($(".connectedSortable").find('tr').get().reverse()).each(function() {
					if(i <= remove_count){
						$(this).remove();
					}
					i++;
				});
				$more.data('current', new_current);
				$this.data('results', data.limit);
			}
			$this.parent().find('.loader-icon').addClass('stop');
			$this.parent().removeClass('working');
		}, 1000)
	});

	// PASSWORD DELETE
	$.current_delete = {};
	$('body').delegate('.delete-an-item', 'click', function(event) {
		event.preventDefault();
		$.current_delete = $(this);
	});

	$('#modal_delete').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
        $.current_delete = {};
    });

	c = console.log.bind(console);
	$('body').delegate('.multi-remove', 'click', function(event) {
		event.preventDefault();

		var $this = $(this),
			$icon = $this.find('i.fa');

		$icon.toggleClass('fa-check-square fa-square-o');
		$this.toggleClass('active');
		if($('.multi-remove i.fa-check-square').length > 0) {
			$('.multi-remove-trigger').show();
		}
		else {
			$('.multi-remove-trigger').hide();
		}
	})

    $('body').delegate('.multi-remove-trigger', 'click', function(event){
        event.preventDefault();
        // console.log($('.multi-remove.active').length);
        // return;
        var $this = $(this),
        	pid = $this.data('pid');

        var workspaces = new Array();
        if($('.multi-remove.active').length > 0) {
        	$('.multi-remove.active').each(function(){
        		var wid = $(this).data('wid');
        		workspaces.push(wid);
        	})

	        BootstrapDialog.show({
	            title: 'Confirmation',
	            message: 'Are you sure you want to remove selected workspaces?',
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: true,
	            buttons: [{
	                    icon: 'fa fa-check',
	                    label: ' Yes',
	                    cssClass: 'btn-success',
	                    autospin: true,
	                    action: function(dialogRef) {
	                        var params = { 'wid': workspaces, 'pid': pid };
	                        $.when($.delete_workspaces(params))
	                            .then(function(data, textStatus, jqXHR) {
	                            	$this.hide();
	                            	for (var i = 0; i < workspaces.length; i++) {
						        		var dataid = workspaces[i];
						        		var $tr = $('[data-id='+dataid+']');
						        		$tr.children('td')
											.css('background-color', '#ef9b89')
											.animate({
												padding: 0
											})
											.wrapInner('<div />')
											.children()
											.slideUp(1000, function() {
												$tr.remove();
												// IF ALL ROWS WERE REMOVED, REFRESH THE PAGE TO SHOW CREATE WORKSPACE MESSAGE BOX
												if ($("#sortable-list").children('tr').length <= 0) {
													var loc = window.location.href;
													window.location.replace(loc);
											    }
											});
						        	}
	                                dialogRef.enableButtons(false);
	                                dialogRef.setClosable(false);
	                                dialogRef.getModalBody().html('<div class="loader"></div>');
	                                setTimeout(function() {
	                                    dialogRef.close();
	                                }, 500);
	                            })
	                    }
	                },
	                {
	                    label: ' No',
	                    icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });
	    }
    })

    $.delete_workspaces = function(params) {
    	c(params)
        var dfd = $.Deferred();

        $.ajax({
            url: $js_config.base_url + 'projects/delete_multiple_workspaces',
            type: "POST",
            data: $.param(params),
            dataType: "JSON",
            global: false,
            success: function(response) {
                dfd.resolve("done");
            }
        })
        return dfd.promise();
    }

	var tHeight = $('#dd-form > table').height();
	$('#dd-form').css({'min-height': tHeight +120});

	$('#modal_medium').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});

	$('#modal_medium').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	});


	$.create_bt_modal = function ($el) {

	 var modal = '<div class="modal modal-warning fade" id="dataConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">sdfsdf' +
		 '<div class="modal-dialog">' +
		 '<div class="modal-content"> ' +
		 '<div class="modal-header">' +
		 '	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
		 '			<h4 class="modal-title" id="myModalLabel">Delete Confirm</h4>' +
		 '</div>' +
		 '<div class="modal-body">' +
		 '</div>' +
		 '<div class="modal-footer">' +
		 '	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' +
		 '	<a class="btn btn-danger btn-ok" id="dataConfirmOK">Delete</a>' +
		 '</div>' +
		 '</div>' +
		 '</div>' +
		 '</div>';

	 $el.append(modal);
	}

$('button#confirm_delete11').click(function (event) {
	event.preventDefault()
		var data = $(this).data(),
		target = data.target,
		url = data.remote,
		$tr = $(this).parents('tr:first'),
		trData = $tr.data(),
		id = trData.id,
		trWSid = trData.id,
		trPJid = trData.pid;


	 $.when($.confirm({message: 'Are you sure you want to delete this Workspace?', title: 'Delete confirmation'}))
	 .then(
		function () {

		$.ajax({
		   url: url,
		   data: $.param({
				'action': 'delete',
				pwid: target,
				wsid: trWSid,
				prjid: trPJid
		   }),
		   type: 'post',
		   dataType: 'json',
		   success: function (response) {
				if (response.success) {

					if(response.content){
						// send web notification
						$.socket.emit('socket:notification', response.content.socket, function(userdata){});
					}
					 // Remove list item related to list after delete workspace
					 	var $list = $("#sideMenu.normal-list.sideMenu").find("ul#sidebar_menu.sidebar-menu"),
						$list_item = $list.find('#' + id);
						if ($list_item.length) {
							setTimeout(function () {
								$list_item.effect("shake", {
									times: 3
								   }, 600, function () {
									$list_item.remove()
							   });
						  	}, 200);
					 	}

					setTimeout(function () {

						$tr.children('td')
							.css('background-color', '#ef9b89')
							.animate({
								padding: 0
							})
							.wrapInner('<div />')
							.children()
							.slideUp(1000, function() {
								$tr.remove();
								// IF ALL ROWS WERE REMOVED, REFRESH THE PAGE TO SHOW CREATE WORKSPACE MESSAGE BOX
								if ($("#sortable-list").children('tr').length <= 0) {
									var loc = window.location.href;
									window.location.replace(loc);
							    }
							});
					 }, 300)
				}
			}
		});

	 },
	 function ( ) {
		  console.log('Error!!!')
	 });
});







	var TO = false;
	$(window).on('resize', function(){

		if(TO !== false) {
			clearTimeout(TO);
		}

		TO = setTimeout(resizeStuff, 1000); //200 is time in miliseconds
	});

});

$(window).load(function () {
	console.log("loaded");
	setTimeout(function(){
		$('.ellipsis-word').ellipsis_word();
		$('.key_target').textdot();
	}, 100)

})



     	  </script>

	  <?php
	  } else {
	       echo $this->element('../Projects/partials/error_data', array(
		   'error_data' => [
		       'message' => "Add a Workspace to the Project.",
		       'html' => "Click<a class='' href='" . Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE), TRUE) . "'> here </a>to add a Project now."
		   ]
	       ));
	       ?>



     	  <!--  <div class="box project_box" style="height:400px"><a href="#"> Create Project! </a> </div>	-->

     	  <script type="text/javascript" >
     	       $(function () {
     		    // var loc = '<?php echo SITEURL ?>projects';
     		    // window.location.replace(loc);
     	       })
     	  </script>
<?php } ?>

     </div>
</div>

