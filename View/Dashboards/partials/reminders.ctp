<style type="text/css">
	.rem_popover {
	    display: block;
	    font-size: 13px;
        margin: 5px 0 0 0;
	}
	.rem_popover .el-title {
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    width: 100%;
	    display: block;
	    overflow: hidden;
	    background-color: #d6e9c6;
	    padding: 7px 15px;
    	font-weight: 600;
	}
	.rem_popover span.text-data {
	    display: block;
        margin: 3px 0;
	}
	.rem_popover span.comment-label {
	    display: block;
	}
	.rem_popover span.comment-data {
	    border: 1px solid #ccc;
	    display: block;
	    padding: 3px 5px;
	    min-height: 70px;
	}
	.rem_popover .data-content {
        padding: 7px 15px;
        font-size: 11px;
	}
	.el-icon {
		/*position: absolute;
	    right: 6px;
	    top: 2px;
	    padding: 2px 0px 0px 5px;*/
	    padding: 0;
	    float: right;
	    margin-top: -5px;
	}
	.icon_element_add_black {
	    background-attachment: scroll;
	    background-repeat: no-repeat;
	    background-image: url(../images/icons/black_md.png);
        background-position: 6px 5px;
	    background-size: 50% auto;
	    margin: 0;
	    padding: 1px 0 !important;
	    display: inline-block;
	    min-height: 17px;
	    min-width: 24px;
	}
 
	section.content {
		padding-top: 0;
	}
	.no-scroll {
		overflow: hidden;
	}
	
	.reminder-data{
	overflow-x: auto;
    overflow-y: overlay;
	}
	
</style>
<div class="buttons-container">
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-1">
		<div class="panel panel-default">
			<div class="panel-heading people-section">From
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control alphabetical tipText" title="Alphabetical Sort" data-sorted="asc" data-type="user">AZ</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-2">
		<div class="panel panel-default">
			<div class="panel-heading task-section">Project
				<input type="text" class="select_dates" style="opacity: 0; width: 0; height: 0;">
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control alphabetical tipText" title="Alphabetical Sort" data-sorted="asc" data-type="project">AZ</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-3">
		<div class="panel panel-default">
			<div class="panel-heading wsp-section">Tasks with Reminders
				<div class="btn-group pull-right">
					<a class="btn btn-xs btn-control alphabetical tipText" title="Alphabetical Sort" data-sorted="asc" data-type="element">AZ</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-4">
		<div class="panel panel-default">
			<div class="panel-heading project-section">Schedule
				<div class="btn-group pull-right">
					<span href="#" class="btn btn-xs btn-control dropdown" id="el-status-dd">
					    <span href="#" class="dropdown-toggle" id="status-drop" data-toggle="dropdown" aria-controls="status-dropdown" aria-expanded="false">Ending <span class="fa fa-times bg-red clear_status_filter"></span></span>
					    <ul class="dropdown-menu" aria-labelledby="status-drop" id="status-dropdown">
					    	<li><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Overdue" data-status="overdue">Overdue <i class="fa fa-check"></i></a></li>
					    	<li><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Today" data-status="today">Today <i class="fa fa-check"></i></a></li>
					    	<li><a id="dropdown1-tab" aria-controls="dropdown1" data-text="Upcoming" data-status="pending">Upcoming <i class="fa fa-check"></i></a></li>
					    </ul>
				    </span>
					<a class="btn btn-xs btn-control reminder-sort tipText" title="Reminder Date Ascending" data-parent=".filter_selected_project" data-type="project" data-direction="ASC" data-field="reminder_date"><i class="fa fa-chevron-circle-up"></i></a>
					<a class="btn btn-xs btn-control reminder-sort tipText" title="Reminder Date Descending" data-parent=".filter_selected_project" data-type="project" data-direction="DESC" data-field="reminder_date"><i class="fa fa-chevron-circle-down"></i></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-3 col-md-3 col-lg-3 col-data col-data-5" style="width: 12%;">
		<div class="panel panel-default">
			<div class="panel-heading project-section">Action</div>
		</div>
	</div>
</div>

<?php
// $user_element_reminder = user_element_reminder();
// pr($reminder_filter);
$current_user_id = $this->Session->read('Auth.User.id');
?>
<div class="reminder-data">
	<div class="no-row-wrapper" style="display: none;">NO REMINDERS</div>
<?php
$reminder_elements = element_reminder($current_user_id, $params);

if(isset($reminder_elements) && !empty($reminder_elements)){
	$current_org = $this->Permission->current_org();
	
	if(isset($project_id) && !empty($project_id)){
		$reminder_elements = arraySearch( $reminder_elements, 'project_id', $project_id);
	}

	//usort($reminder_elements, 'reminder_date_compare');
	/*if(isset($reminder_filter) && !empty($reminder_filter)) {
		$buffer = array();
		foreach ($reminder_elements as $element) {
			$todayDate = date('Y-m-d 24:00:00');
			$todayDate1 = date('Y-m-d 00:00:00');
		    if (strtotime($element['reminder_date']) > strtotime($todayDate1) && strtotime($element['reminder_date']) < strtotime($todayDate)) {
		        $buffer[] = $element;
		    }
		}
		$reminder_elements = $buffer;
	}*/
	//pr($reminder_elements);
	foreach ($reminder_elements as $key => $value) {
			if(isset($value['reminder_date']) && !empty($value['reminder_date'])){
			if( !reminder_is_deleted($value['id'], $current_user_id) ){
			$project_detail = getByDbId('Project', $value['project_id'], ['title']);
			$element_detail = getByDbId('Element', $value['element_id'], ['title', 'id', 'start_date', 'end_date']);

			$userDetail = $this->ViewModel->get_user( $value['user_id'], null, 1 );
			$organization_id = '';
			if( isset($userDetail['UserDetail']['organization_id']) && !empty($userDetail['UserDetail']['organization_id']) ){
				$organization_id = $userDetail['UserDetail']['organization_id']; 
			}
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			$html = '';
			if(isset($userDetail) && !empty($userDetail)) {
				
				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

				if( $value['user_id'] != $current_user_id ) {
					$html = CHATHTML($value['user_id'], $value['project_id']);
				}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}
			$status_class_name = '';
			$str = '';
			$remaining_left = remaining_left($value['reminder_date']);

			if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='today'){
				$str .= "Today ";
				$status_class_name = 'today';

			}
			$str .= $remaining_left['text'];

			if( $remaining_left['due']=='OVD'){
				$status_class_name = 'overdue';
			}

			if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='OVD' ){
				$status_class_name = 'overdue';
			}else if(isset($remaining_left['ending']) && !empty($remaining_left['ending'])  && $remaining_left['ending']=='to go'){
				$status_class_name = 'pending';
			}

			$reminder_dates = (isset($value['reminder_date']) && !empty($value['reminder_date'])) ? $this->TaskCenter->_displayDate_new($value['reminder_date'],'Y-m-d') : false;

			?>
<?php
	$p_title = str_replace("'", "", $project_detail['Project']['title']);
	$p_title = str_replace('"', "", $p_title);
 ?>
<?php
	$e_title = str_replace("'", "", $element_detail['Element']['title']);
	$e_title = str_replace('"', "", $e_title);
?>
		<div class="data-row" data-filter="<?php if( $value['user_id'] != $current_user_id ) { echo('received'); }else{ echo('sent'); } ?>" data-user="<?php echo $user_name; ?>" data-project="<?php echo strip_tags($p_title); ?>" data-element="<?php echo strip_tags($e_title); ?>" data-status="<?php echo($status_class_name); ?>" data-reminder-date="<?php echo($reminder_dates); ?>" data-reminder="<?php echo($value['id']) ?>">
			<div class="col-xs-3 col-md-3 col-lg-3 data-wrap data-wrap-1">								
				<div class="style-people-com ">
								<span class="style-popple-icon-out">
									<div class="style-people-com">
											<span class="style-popple-icon-out">
										<a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $value['user_id'], 'admin' => FALSE ), TRUE ); ?>" class="style-popple-icons pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>" >
												<span class="style-popple-icon"  >
							<img  src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40"  />
													</span>
											</a>
								<?php if( $current_org['organization_id'] != $organization_id ){ ?>					
												<i class="communitygray18 community-g tipText" title="" data-original-title="Not In Your Organization" data-toggle="modal"></i>
								<?php } ?>				
												</span>
						
									</div>
								</span>
								<div class="style-people-info">
									<a href="#">
										<span class="style-people-name"><?php
						echo htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
					?> </span>
										<span class="style-people-title" style="cursor: default;">
											<?php 
												echo htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);
											?>
										</span>
									</a>
								</div>
							</div>
		
			</div>
			<div class="col-xs-3 col-md-3 col-lg-3 data-wrap data-wrap-2">
				<span class="title">
					<a href="<?php echo Router::Url(array("controller" => "projects", "action" => "index", $value['project_id']), true); ?>" class="tipText" title="Open Project"><?php echo strip_tags($p_title); ?></a>
				</span>
			</div>
			<div class="col-xs-3 col-md-3 col-lg-3 data-wrap data-wrap-3">
				<?php
				$rtitle = htmlentities($e_title);
				$rcomments = htmlentities($value['comments']);
				$popoverHtml = '';
				$popoverHtml .= "<div class='rem_popover'>";
					$popoverHtml .= "<div class='el-title'>".$rtitle."</div>";
					$popoverHtml .= "<div class='data-content'>";
						$popoverHtml .= "<span class='text-data'>Reminder By: ".$user_name."</span>";
						$popoverHtml .= "<span class='text-data'>Reminder Ends: ".date( 'd M Y, g A',strtotime($value['reminder_date']))."</span>";
						if(isset($value['comments']) && !empty($value['comments'])) {
							$popoverHtml .= "<span class='text-data'>";
								$popoverHtml .= "<span class='comment-label'>Comment:</span>";
								$popoverHtml .= "<span class='comment-data'>".$rcomments."</span>";
							$popoverHtml .= "</span>";
						}
					$popoverHtml .= "</div>";
				$popoverHtml .= "</div>";
				 ?>
				<span class="title">
					<a href="<?php echo Router::Url(array("controller" => "entities", "action" => "update_element", $value['element_id']), true); ?>#tasks" class="popovers" title="Reminder Details <a href='<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $element_detail['Element']['id'], 'admin' => FALSE ), TRUE ); ?>' class='el-icon btn btn-default btn-xs'><span class='icon_element_add_black'></span></a>" data-content="<?php echo $popoverHtml; ?>">

					<?php
						echo $rtitle;
				 ?></a>
				</span>
			</div>
			<div class="col-xs-3 col-md-3 col-lg-3 data-wrap data-wrap-4">

				<span class="date-section <?php echo($status_class_name) ?>"><?php echo date( 'd M, Y g:iA',strtotime($value['reminder_date']) ); ?></span><span class="date-section <?php echo($status_class_name) ?>"><?php echo($str); ?></span>
			</div>
			<div class="col-xs-3 col-md-3 col-lg-3 data-wrap data-wrap-5">
				<a href="" class="note_icon remove_reminder tipText" title="Got It" data-id="<?php echo($value['id']) ?>"></a>
			</div>
		</div>
	<?php } ?>
	<?php } ?>
	<?php } ?>
<?php }else{ ?>
<div class="no-row-wrapper">No Reminders</div>
<?php } ?>
</div>
<style type="text/css">
	.bgcolor {
	    background-color: #ccc;
	}
</style>
<script type="text/javascript">
	$(function(){
		$('.popovers').popover({
			placement : 'bottom',
	        trigger : 'hover',
	        html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		}).
		on('show.bs.popover', function(){
			var $popover = $(this).data('bs.popover');
			var $tip = $popover.$tip;
			$tip.find('.popover-content').css('padding', 0)
			$tip.find('.popover-title').attr('style', 'font-size: 13px;')
		})


		if ($('.data-row').length <= 0) {
			$('.btn-control').addClass('disabled');
			$('.filter_reminder').prop('disabled', true);
		}

		// $('.title').ellipsis_word();
    	$("#status-dropdown li a").click(function(e){
    		e.preventDefault();
    		var status = $(this).data('status');
    		$("#status-dropdown li a i.fa-check").hide();
    	  	$('#status-drop').html($(this).data('text') + ' <span class="fa fa-times bg-red clear_status_filter"></span>');
    		$("i.fa-check", $(this)).show();

    		$('.reminder-data .no-row-wrapper').hide();
    		$('.data-row').show()
    		$('.data-row').each(function(){
    			if($(this).data('status') == status){
    				$(this).show();
    			}
    			else{
    				$(this).hide();
    			}
    		})

    		if ( $('.data-row:visible').length <= 0 ) {
				$('.reminder-data .no-row-wrapper').show();
    		}
    	});

    	// JAI Functionality
    	$('#status-dropdown li a[data-status="'+$js_config.reminder_filter+'"]').trigger('click');

    	$('body').delegate(".clear_status_filter", 'click', function(e) {

    		$('#status-drop').html('Ending <span class="fa fa-times bg-red clear_status_filter"></span>');
    		$("#status-dropdown li a i.fa-check").hide();
    		$('.data-row').show();
    		$('.reminder-data .no-row-wrapper').hide();
    		return false;
    	});
		
		
	$('html').addClass('no-scroll');
	// $('.nav.nav-tabs').removeAttr('style');

	// RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        $('.reminder-data').animate({
            minHeight: (($(window).height() - $('.reminder-data').offset().top) ) - 17,
            maxHeight: (($(window).height() - $('.reminder-data').offset().top) ) - 17
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })
		
		
		
	})
</script>

<script type="text/javascript">
	$(function(){
		$('html').scrollTop(0);
	})
</script>