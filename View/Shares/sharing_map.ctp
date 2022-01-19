<?php
// echo $this->Html->css('projects/tree_view');
echo $this->Html->css('projects/manage_categories');
echo $this->Html->script('projects/manage_categories', array('inline' => true));
// echo $this->Html->script('projects/tree_view', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
?>
<style>
	.btn-rounded-xs {
	    border-radius: 30px;
	    font-size: 12px;
	    height: 20px;
	    line-height: 1.65;
	    padding: 0;
	    text-align: center;
	    width: 20px;
		margin: -3px 0 0 10px;
	}
	.overlay_loader {
	    background: rgba(0, 0, 0, 0.6) url("../../images/ajax-loader-1.gif") no-repeat scroll 380px center;
	    height: 100%;
	    margin: 0;
	    padding: 310px 0 0;
	    position: absolute;
	    width: 100%;
	    z-index: 999;
		display: none;
	}
	.no-child-icon {
	    background: #d4d4d4 none repeat scroll 0 0;
	    border: 1px solid #ccc;
	    border-radius: 50%;
	    font-size: 10px;
	    margin: 0 4px 0 0;
	    padding: 3px 4px 3px 5px;
		color: #929292;
	}
	.nav.nav-list.tree a i.tree_icons.opened {
	    background-color: #fcb248;
	    border: 1px solid #fda53b;
	    padding: 4px 5px;
	}
	.btn-owner {
		margin: 0 7px 0 0px;
	}
	#tree_view > li {
	    border-bottom: 1px solid #ccc;
	    display: inline-block;
	    width: auto;
	}

	.unix_profile {
	    display: inline-flex !important;
	     padding: 0 !important;
	     margin: 0 10px 0 0 !important;
        width: 28px;
	}
    .unix_profile .text-maroon {
        width: 28px;
    }
	.unix_profile:hover {
	    display: inline-flex !important;
	    padding: 0 !important;
	     margin: 0 10px 0 0 !important;
	}
	.tree-owner {
		border-bottom: 1px solid #ccc;
	}
</style>
<script type="text/javascript">
jQuery(function($) {
	// $('#tree_view').tree_view({openedClass:'fa-chevron-circle-right', closedClass:'fa-chevron-circle-down'});


	$('body').on('click', function () {
		$('button.tipText').each(function(){
			if( $(this).data('bs.tooltip') ) {
					var data = $(this).data('bs.tooltip'),
						tip = data.$tip,
						tip.hide()

			}

		})
    })
	$('#popup_modal').on('hidden.bs.modal', function () {
		$('button.tipText').tooltip('hide');
		$('html').removeClass('modal-open');
        $(this).removeData('bs.modal');
		$(this).find('.modal-content').html('<div class="overlay_loader" style=""></div>')
		$('body').trigger('click')
    })
	.on('show.bs.modal', function () {
		$('button.tipText').tooltip('hide');
    });


	$('body').delegate('.tree-toggler', 'click', function(event){
		event.preventDefault();
	})

	$('body').delegate('button.tipText', 'mouseenter', function(){
		var that = $(this)
		that.tooltip('show');
		setTimeout(function(){

			that.tooltip('hide');
		}, 1000);
	})

	$('body').delegate('button.tipText', 'click', function(event){
		event.preventDefault();
		$(this).tooltip('hide');
	});


})

</script>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_heading; ?></span>
					</p>
				</h1>

			</section>
		</div>
	</div>

	<div class="box-content">
		<div class="row ">
			<div class="col-xs-12">
				<div class="box border-top margin-top">
					<div class="box-header no-padding" style="">
						<!-- MODAL BOX WINDOW -->
						<div class="modal modal-success fade " id="popup_modal123" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content modal-lg"></div>
							</div>
						</div>
						<!-- END MODAL BOX -->
					</div>

					<div class="box-body clearfix list-shares" style="min-height: 800px">
						<div id="multi_list" class=" " >


<?php

	$pg_detail = user_groups($user_id, project_upid($this->params['pass'][0]));

	if(empty($pg_detail)){
	$pg_detail = user_groupsbyUser($user_id, project_upid($this->params['pass'][0]));
	}

	$pg_data = Set::combine($pg_detail, '{n}.ProjectGroup.id', '{n}.ProjectGroup.title');


	//$view = new View();
	//$groupR = $view->loadHelper('Group');
	//$commonR = $view->loadHelper('Common');





	$ghtml = '';
	if( isset($pg_data) && !empty($pg_data) ) {
		$ghtml .= '<ul class="nav nav-list tree">';
		$project_level = 0;
		foreach($pg_data as $pk => $pv ) {
			$pg_deleted = $pv;
			$pg_delete_flag = false;

			 $pg = group_detail($pk);

			 if(isset($pg) && !empty($pg)){
			 $uusde = $this->Common->userproject(project_primary_id($pg['ProjectGroup']['user_project_id']),$user_id);
			 $pp_perm = $this->Common->project_permission_details(project_primary_id($pg['ProjectGroup']['user_project_id'] ),$user_id);
			 //$grp_id = $this->Group->GroupIDbyUserID(project_primary_id($pg['ProjectGroup']['user_project_id']), $user_id);
			 $grp_id = $pg['ProjectGroup']['id'];



			if (isset($grp_id) && !empty($grp_id)) {

			$group_permission = $this->Group->group_permission_details(project_primary_id($pg['ProjectGroup']['user_project_id']), $grp_id);
			if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
				$project_level = $group_permission['ProjectPermission']['project_level'];
			}else{
				$project_level = 0;
			}

			}

			//  pr($uusde);
			 // pr($pp_perm);


			if( $project_level == 1)  {
					$sts = "<span class='text-blue'>[Group Owner]</span>";
			}else{
				$sts = "<span class='text-green'>[Group Sharer]</span>";
			}
			}


			if( isset($pg) && !empty($pg) ) {
				$pgdata = $pg['ProjectGroup'];
				if( isset($pgdata['is_deleted']) && !empty($pgdata['is_deleted']) ) {
					$pg_deleted = "<span class='tipText' title='Group Deleted' style='color:red;text-decoration:line-through'>
					<span style='color:black'>".$pv."</span>
					</span>";
					$pg_delete_flag = true;
				}
			}
			$ghtmls = '';
			if( !$pg_delete_flag ) {
				$button_shares = '<button class="btn btn-rounded-xs bg-blue  fa fa-share tipText" title="View Permissions"  role="button"  data-target="#popup_modal" data-toggle="modal"';
			 	 $button_closes = '</button>';
						$ghtmls .= $button_shares . ' data-remote="'.Router::Url( array( 'controller' => 'shares', 'action' => 'gmap_permissions',  $pgdata['id'], $this->params['pass'][0], 'admin' => FALSE ), TRUE ).'" >';
						$ghtmls .= $button_closes;


			$ghtml .= '<li><a href="#" class="tree_links"><i class="no-child-icon fa fa-chevron-right"></i><a href="'.SITEURL.'/groups/group_users/'.$pgdata['id'].'/0/'.$this->params['pass'][0].'" data-toggle="modal" data-target="#modal_people" class=" unix_profile"> <i class="fa text-maroon fa-users btn btn-default btn-xs type" ></i></a> ' . htmlentities($pg_deleted,ENT_QUOTES, "UTF-8") . '</a>'.$ghtmls." " .$sts.'</li>';
			}else{

			$ghtml .= '<li><a href="#" class="tree_links "  ><i class="no-child-icon fa fa-chevron-right"></i><a    class=" unix_profile"> <i class="fa text-maroon fa-users btn btn-default btn-xs type" ></i></a> ' . htmlentities($pg_deleted,ENT_QUOTES, "UTF-8") . '</a>'.$ghtmls." " . $sts.'</li>';

			}
		}
		$ghtml .= '</ul>';
	}


$html = '<ul id="tree_view" class="nav nav-list tree">';
	$html .= '<li class="has-sub-cat">';
		$html .= '<a href="'.Router::Url( array( 'controller' => 'subdomains', 'action' => 'index',  $this->params['pass'][0], 'admin' => FALSE ), TRUE ).'" class="tree-owner nav-header tree_links tipText" title="Open Social Analytics"><span class="btn-rounded-xs1 icon-owner "></span>';

		$ownnn_id = $this->Common->userprojectOwner($project_detail['Project']['id']);
		//$owner_data = user_name( $user_id );
		$owner_data = user_name( $ownnn_id  );
		$html .= $owner_data['first_name'] . ' ' . $owner_data['last_name'];
		$html .= ' <span class="text-green">[Creator]</span></a>';
		$html .= $ghtml;
		$html .= '<ul class="nav nav-list tree">';
			$html .= $list_html;
		$html .= '</ul>';
	$html .= '</li>';
$html .= '</ul>';

echo $html;
?>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

<script type="text/javascript" >
$(function() {
	// $('.tree_links').ellipsis_word()
})
</script>


<div class="modal modal-success fade" id="RecordviewB" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div> <!-- /.modal-content -->
	</div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content"></div> <!-- /.modal-content -->
	</div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->


