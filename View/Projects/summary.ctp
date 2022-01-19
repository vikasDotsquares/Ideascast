<?php
//$.socket.emit("project:add");
$referer = $this->request->referer('/', true);
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/ellipsis-word', array('inline' => true));
echo $this->Html->script('projects/color_changer.min', array('inline' => true));
// $perPageWspLimit = 1;
$currentWspPage = 0;
?>

<style>
	.no-wsp {
	    color: #bbbbbb;
	    display: block;
	    font-size: 30px;
	    height: 50px;
	    margin: 47px 0;
	    text-align: center;
	    vertical-align: middle;
	    width: 100%;
	    text-transform: uppercase;
	}
	.list-unstyled span{cursor:default !important}
	table td{display:table-cell}

	td.action-buttons a.btn, td.action-buttons button.btn, td.action-buttons span.btn {
		padding: 4px 7px !important;
	}
	.ws_color_box .colors b {
		border: 1px solid #dddddd !important;
	}
	.btn-group.btn-actions {
		/*min-width: 150px;*/
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
	    margin: 15px 5px 20px 5px;
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
	$( function() {
		$('.text-ellipsis').tooltip({
			placement: 'top-left'
		})
	    $.reload_project_progress = function() {
	        $.ajax({
	            type: 'POST',
	            data: $.param({ project_id: $js_config.project_id }),
	            url: $js_config.base_url + 'projects/project_progress_bar',
	            global: false,
	            success: function(response) {
	                $(".project_progress_bar").html(response);
	            }
	        })
	    }

		var referer_url = '<?php echo $referer; ?>';
		if (referer_url.indexOf('manage_project') > -1){
			var connect_url = $js_config.SOCKETURL;
        	$.socketIO = io.connect(connect_url);
			$.socketIO.emit("project:add", $js_config.USER.id);
			console.log('socket emit project:add');
		}
		var sort_able = ".sort-able";
	    $( ".sort-able" ).sortable({
	    	placeholder: "sort-highlight",
        	connectWith: sort_able,
	    	axis: 'y',
		    update: function (event, ui) {
		        var ids = new Array();
	            $(sort_able).children('.workspace-tasks-sec-top').each(function(index, el) {
	                ids.push(this.id);
	            });
	            var request = $.ajax({
	                url: ajaxObject.ajaxUrl,
	                method: "POST",
	                data: { ids: ids, id: ajaxObject.id },
	                dataType: "JSON",
	                global: true
	            });

	            request.done(function(msg) {

	            });

	            request.fail(function(jqXHR, textStatus) {
	                alert("Request failed: " + textStatus);
	            });
		    }
	    });
	    $( ".sort-able" ).disableSelection();
	  } );
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
	  /*$('body').delegate('.color_bucket', 'click', function (event) {
	       event.preventDefault();
	       console.log('-------------------------------------------')

	       var $that = $(this);

	       $that.next(".ws_color_box").find('.el_color_box').colored_tooltip();

	  })*/

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



		$('.project-sign-off').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    data = $this.data(),
                    id = data.id,
                    title = data.header,
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');
            $span_text.css({ 'padding': '0', 'font-size': '14px'})
            // set message
            var body_text = $this.attr('data-msg');
            console.log('data', $yes)

            $('#confirm-box').find('#modal_body').text(body_text)
            $('#confirm-box').find('#modal_header').css('background-color','#d9534f');
            $('#confirm-box').find('#modal_header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><span style="font-size:16px;color:#fff;" id="myModalLabel" class="modal-title">'+title+'</span>');


			BootstrapDialog.show({
	            title: title,
	            type: BootstrapDialog.TYPE_DANGER,
	            message: body_text,
	            draggable: true,
	            buttons: [{
	                label: 'Reopen',
	                cssClass: 'btn-success',
	                autospin: true,
	                action: function(dialogRef) {
	                    var post = { 'data[Element][id]': id, 'data[Element][sign_off]': data.value },
	                    data_string = $.param(post);
	                     // Ajax request to sign-off/reopen
	                        var post = {'data[Project][id]': id, 'data[Project][sign_off]': data.value},
	                        data_string = $.param(post);

	                        $.ajax({
	                            type: 'POST',
	                            data: data_string,
	                            url: $js_config.base_url + 'projects/project_signoff',
	                            global: false,
	                            dataType: 'JSON',
	                            beforeSend: function () {
	                                $span_text.css({'opacity': 0.5, 'color': '#222222'})
	                                $div_progress.css({'width': '100%'})
	                            },
	                            complete: function () {
	                                setTimeout(function () {
	                                    $('#confirm-box').modal('hide')
	                                    $span_text.css({'opacity': 1, 'color': '#ffffff'})
	                                    $div_progress.css({'width': '0%'})
	                                }, 3000)
	                            },
	                            success: function (response, statusText, jxhr) {

	                                console.log(response)

	                                //return
	                                if (response.success) {
	                                    if(response.content){
	                                        // send web notification
	                                        $.socket.emit('socket:notification', response.content.socket, function(userdata){});
	                                    }
	                                    location.reload();
	                                    setTimeout(function () {
	                                        // location.reload(true)

	                                    }, 2500)
	                                    location.reload();
	                                }
	                                else {
	                                    console.log('fail')
	                                }

	                            }
	                        })
	                }
	            },
	            {
	                label: ' Cancel',
	                //icon: '',
	                cssClass: 'btn-danger',
	                action: function(dialogRef) {
	                    dialogRef.close();
	                }
	            }]
	        });
        });

        $('.element-sign-off-restrict').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                    $cbox = $('#confirm-box'),
                    $yes = $cbox.find('#sign_off_yes'),
                    $no = $cbox.find('#sign_off_no');

            var $span_text = $yes.find('span.text'),
                    $div_progress = $yes.find('div.btn_progressbar');

            // set message
            var body_text = $this.attr("data-msg");

             BootstrapDialog.show({
	            title: '<h3>Sign Off</h3>',
	            message: body_text,
	            type: BootstrapDialog.TYPE_DANGER,
	            draggable: true,
	            buttons: [
	                {
	                    label: ' Close',
	                   // icon: 'fa fa-times',
	                    cssClass: 'btn-danger',
	                    action: function(dialogRef) {
	                        dialogRef.close();
	                    }
	                }
	            ]
	        });
        });

	})
</script>

<?php
$summary = null;
?>
<style type="text/css">

	.content{
		padding-top: 0;
	}

	.theader {
		border-right: 1px solid #ccc;
	}
	.wsp-link {
		overflow: hidden;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    width: 100%;
	    display: inline-block;
	    color: #fff;
	}

    .workspace-tasks-sec-top{
        display: flex;
        width: 100%;
        background-color: #fff;
    }
    .padd8{
        padding: 8px;
    }

    .workspace-col-5 {
        width: 30%;
    }
    .workspace-col-4 {
        width: 24%;
        display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
    .workspace-col-3 {
        width: 24.5%;
    }
     .workspace-col-2 {
        width: 13%;
         display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
    .workspace-col-1 {
        width: 8.5%;
        display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
	.header-progressbar{
		padding-right: 0;
	}
    .workspace-tasks-sec-top .theader {
        background-color: #f5f5f5;
        border-top: 1px solid #dcdcdc;
        font-weight: bold;
        border-right: none;
    }
    .workspace-tasks-sec-top .tcont {
            border: 1px solid #f4f4f4;
    }
	.workspace-tasks-sec-top .text-ellipsis {
	    white-space: nowrap;
	    overflow: hidden;
	    display: block;
	    padding-bottom: 5px ;
	}
    /*.workspace-tasks-sec-top .reminder-sharing-d-in{
        padding-left: 0;
    }*/
    .workspace-tasks-sec-top .description-wroks{
         font-size: 12px; line-height: 16px;
    }
    .workspace-tasks-sec-top .inner{
        color: #fff;
    }
    .workspace-tasks-sec-top .reminder-sharing-d-out span.date-time {
    display: flex;
    flex-wrap: wrap;
    padding-bottom: 6px;
            padding-top: 1px;
	}
    .workspace-tasks-sec-top .reminder-sharing-d-in span.date-time span {
        font-size: 11px;
    }
    .workspace-tasks-sec-top .reminder-sharing-d-out span.sten-date {
    display: flex;
        flex-wrap: wrap;
	}
    .workspace-tasks-sec-top .small-box {
    	margin-bottom: 0;
    	transition: none !important;
    }
    .workspace-tasks-sec-top .btn-actions a.btn, .workspace-tasks-sec-top .btn-actions button.btn, .workspace-tasks-sec-top .btn-actions span.btn {
	    padding: 4px 7px !important;
	}
	.small-box>.inner {
	    display: block;
	    padding: 10px;
	    text-align: left;
	}
     @media (max-width:1365px){
    .workspace-tasks-sec-top .reminder-sharing-d-out span.date-time {
		padding-bottom: 0;
	}
   	.workspace-tasks-sec-top .reminder-sharing-d-out span.date-time span{
		width: 100% !important;
	}
  	.workspace-tasks-sec-top  .reminder-sharing-d-in span.sten-date span {
		width:  100% !important;
	}

    }
    @media(min-width:1140px) and (max-width:1340px){
        .workspace-col-3 {
            width: 21%;
        }
        .workspace-col-1 {
            width: 10%;
        }
        .workspace-col-2 {
            width: 15%;
        }
    }
     @media(min-width:992px) and (max-width:1139px){
        .workspace-col-3 {
            width: 21%;
        }
        .workspace-col-1 {
            width: 10%;
        }
        .workspace-col-2 {
            width: 19%;
        }
        .workspace-col-4 {
            width: 20%;
         }

    }

    @media(max-width:991px){
        .workspace-tasks-sec-wrap{
            overflow: auto;
        }
        .workspace-tasks-sec-wrap .workspace-tasks-sec-top{
        min-width: 990px;
        }
        .workspace-col-3 {
            width: 23%;
        }
         .workspace-col-2 {
            width: 14.5%;
        }
    }

		@media(max-width:479px){
		.pull-left.project-detail span {
			margin: 0 2px 5px 2px;
		}
		.pull-left.project-detail .sb_blog {
			margin-bottom: 2px !important;
		}

		 .projec-els-progressbar {
			padding-top: 0px;
			width: 86%;
		}

    }


</style>
<div class="row">

     <div class="col-xs-12">


	       <section class="main-heading-wrap pb6">
<div class="main-heading-sec">
			<?php
			 if (isset($projects) && !empty($projects)) {

				$projectMinDetail = $project_detail = $projects;
			?>
 		<h1>
			<?php
			      echo htmlentities($this->ViewModel->_substr($projectMinDetail['Project']['title'], 60, array('html' => true, 'ending' => '...')),ENT_QUOTES, "UTF-8");
 			?>
 		</h1>

  		<?php

		    // LOAD PARTIAL FILE FOR TOP DD-MENUS
		    ?>
     			 <div class="subtitles">
     			      <span> <?php
					  echo $this->Wiki->_displayDate( date('Y-m-d',strtotime($projectMinDetail['Project']['start_date'])),$format = 'd M, Y' );
					  ?>
					  </span> → <span> <?php
						echo $this->Wiki->_displayDate( date('Y-m-d',strtotime($projectMinDetail['Project']['end_date'])),$format = 'd M, Y' );
					?></span>



			 <?php /*

			 $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
			 if(isset($p_permission['ProjectPermission']['share_by_id']) && !empty($p_permission['ProjectPermission']['share_by_id'])){
			 ?>

     			      <br /><span>Shared by: <?php echo $this->Common->userFullname($p_permission['ProjectPermission']['share_by_id']); ?></span>
     			      <span>Date Shared: <?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s',strtotime($p_permission['ProjectPermission']['created'])),$format = 'd M, Y H:i:s' );
					  ?></span>

			 <?php
			 } */ ?>
			 </div>
				<?php
			 } else {
			      echo "Project Summary";
			 }
			 ?>

</div>
<div class="header-right-side-icon">

    <span class="headertag ico-project-summary tipText" title="Tag Team Members" data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'tags', 'action' => 'add_tags_team_members', 'project' => $project_id, 'type' => 'project', 'admin' => false)); ?>"></span>
	<span class="ico-nudge ico-project-summary tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'project' => $project_id, 'type' => 'project', 'admin' => false)); ?>"></span>
		<?php
		$currentProject = $this->ViewModel->checkCurrentProjectid($project_id);

		$showTip = 'Set Bookmark';
		$pinClass = '';
		$pinitag = '<i class="headerbookmark"></i>';
		if( $currentProject > 0 ){
			$showTip = 'Clear Bookmark';
			$pinClass = 'remove_pin';
			//$pinitag = '<i class="current_task_icon_logo"></i>';
			$pinitag = '<i class="headerbookmarkclear"></i>';
		}

	 ?>
          <a class="tipText fav-current-task bookmark-project <?php echo $pinClass;?>" data-projectid="<?php echo $project_id; ?>" href="#" data-original-title="<?php echo $showTip;?>"><?php echo $pinitag; ?></a>
</div>
	       </section>


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


$prj_disabled = '';
$prj_disabled_tip = '';
$prj_disabled_cursor = '';
if(isset($project_detail['Project']['sign_off']) && !empty($project_detail['Project']['sign_off']) && $project_detail['Project']['sign_off'] == 1 ){
	$prj_disabled = 'disable';
	$prj_disabled_tip = "Project Is Signed Off";
	$prj_disabled_cursor =" cursor:default !important; ";
}

	       ?>
     	  <script type="text/javascript">
     	       ajaxObject = {
     		    ajaxUrl: '<?php echo Router::url(array('controller' => 'projects', 'action' => 'sortOrderWorkspaces')); ?>',
     		    id: '<?php echo $projects['Project']['id'] ?>',
     	       }
     	  </script>
     	  <div class="box-content postion">
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
<?php $toc = 'bg-green';


if (isset($project_id) && !empty($project_id)) {

$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
}

$project_status = $this->Permission->project_status($project_id)[0][0]['prj_status'];
$projectSignOffWsp = $this->Permission->WspSOCount($project_id)[0][0]['PRG'];
// pr($projectSignOffWsp, 1);
$risk_count = project_risk_status($project_id);
$projectSignoffComments = $this->Permission->projectSignoffComments($project_id);
?>

<div class="header-link-top-right">

<?php
if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {

?>

<?php

if($project_status != 'not_spacified' && $project_status != 'not_started' && $project_status != 'completed'){ ?>
        <?php
/* 			if( !empty($projectSignOffWsp) && $risk_count > 0  ){
				$signoffmsg = "This Project cannot be signed off because it has Workspaces and Risks in progress.";
			} else if( $risk_count > 0 ){
				$signoffmsg = "This Project cannot be signed off because it has active project Risks.";
			} else {
				$signoffmsg = "This Project cannot be signed off because it has Workspaces in progress.";
			} */
			
			if( !empty($projectSignOffWsp)  ){
				$signoffmsg = "This Project cannot be signed off because it has Workspaces in progress.";
			} 

			if( !empty($projectSignOffWsp)   ){ ?>


            	<a href="#" class="tipText signoff-btn h-common-btn disable element-sign-off-restrict" title="Sign Off" data-msg="<?php echo $signoffmsg;?>" data-type="Share"><i class="signoffblack"></i></a>

            <?php
        	}  else { ?>

            	<a href="#" class="tipText signoff-btn h-common-btn" data-toggle="modal" data-target="#signoff_comment_box" data-remote="<?php echo SITEURL;?>projects/tasks_signoff/<?php echo $project_id; ?>" title="Sign Off"  data-type="Share"><i class="signoffblack"></i></a>
        <?php } ?>

<?php } else if ($project_status == 'completed') {
		$flipclass = '';
		if( isset($projectSignoffComments) && $projectSignoffComments != 0 ){
			$flipclass ='fa-rotate-180';
		?>
			<a href="#" class="tipText signoff-btn h-common-btn disable" title="Click To See Comment and Evidence"  data-toggle="modal" data-target="#signoff_comment_show" data-remote="<?php echo SITEURL;?>projects/show_signoff/<?php echo $project_id; ?>"><i class="signoffblack"></i></a>

		<?php } ?>

			<a href="#" class="tipText reopen-btn h-common-btn project-sign-off"  title="Reopen" data-msg="Are you sure you want to reopen this Project?" data-toggle="confirmation" data-header="Reopen Project"  data-id="<?php echo $project_id; ?>"><i class="reopenblack"></i></a>

    <?php } ?>



<?php } ?>

				<?php if ( (isset($project_id) && !empty($project_id)))
                    $dataOwner = $this->ViewModel->projectPermitType($project_id  , $this->Session->read('Auth.User.id') );
                    if ( (isset($project_id) && !empty($project_id))  && $dataOwner == 1 ) {

						$project_workspace_details = $this->ViewModel->getProjectWorkspaces( $project_id, 1 );
						if (isset($project_workspace_details) && !empty($project_workspace_details)) {
						?>
						<span class="hlt-sep">
						<a href="<?php echo SITEURL ?>export_datas/index/project_id:<?php echo $project_id; ?>" class="tipText report-button h-common-btn" title="Generate Report" data-target="#modal_medium" data-toggle="modal"><i class="reportblack"></i></a>
						<a href="<?php echo SITEURL ?>missions/index/project:<?php echo $project_id; ?>" class="tipText report-buttons h-common-btn" title="Mission Room"  ><i class="missionblack-icon"></i></a>
                         </span>
					<?php } ?>

				<?php }	?>



						<?php
							if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {

								if ($ws_exists) {
							?>
							<?php /*?><a class="btn btn-sm btn-success tipText" title="Work Board" href="<?php echo Router::Url(array( "controller" => "boards", "action" => "status_board", $project_id, 'admin' => FALSE ), true); ?>" style="padding: 5px 8px 5px 8px; line-height: 14px;"><i class="icon-work-board-white"></i></a><?php */?>

							<?php }
							// $grp_id = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));

								if( isset($quick_share_disable) && !empty($quick_share_disable) ){
							?>    <span class="hlt-sep">
								<a class="share-button h-common-btn tipText <?php echo $quick_share_disable;?>" title="<?php echo $quick_share_disable_tip;?>" rel="tooltip" style="<?php echo $quick_share_disable_cursor;?>" ><i class="share-icon"></i></a>
							<?php } else { ?>
							    <span class="hlt-sep">
								<a data-toggle="modal" class="share-button h-common-btn tipText" title="Share Project" href="<?php echo SITEURL ?>projects/quick_share/<?php echo $project_id; ?>" data-target="#modal_medium" rel="tooltip" ><i class="share-icon"></i></a>
							<?php	}
							}

if (isset($project_id) && !empty($project_id)) {
 if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($p_permission['ProjectPermission']['permit_edit']) && $p_permission['ProjectPermission']['permit_edit'] == 1 )) {

        ?>


                <?php
                if ($ws_exists === true) {
                    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
                        ?>
            <?php }
        }
        ?>



                    <?php if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {


                        $message = null;

                        $startdate = isset($user_project['Project']['start_date']) && !empty($user_project['Project']['start_date']) ? date("Y-m-d",strtotime($user_project['Project']['start_date'])) : '';
                        $enddate = isset($user_project['Project']['end_date']) && !empty($user_project['Project']['end_date']) ? date("Y-m-d",strtotime($user_project['Project']['end_date'])) : '';

						$startdate = isset($projectMinDetail['Project']['start_date']) && !empty($projectMinDetail['Project']['start_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['start_date'])) : '';

						$enddate = isset($projectMinDetail['Project']['end_date']) && !empty($projectMinDetail['Project']['end_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['end_date'])) : '';


						$curdate = date("Y-m-d");

						$curdate =  $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime(date("Y-m-d"))),$format = 'Y-m-d');

                        $class = '';
                        $url = SITEURL.'templates/create_workspace/'.$project_id;
						$prj_tooltip = 'Add workspace';
						if( FUTURE_DATE == 'on' ){

							if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
								$message = 'Cannot create Workspace, Project is Signed off.';
								$url ='';
								$class = 'workspace disable';
								$prj_tooltip = "Project Is Signed Off";

							}else if(!empty($enddate) && $enddate < $curdate){
								$message = 'Cannot add a Workspace because the Project end date is overdue.';
								$url ='';$class = 'workspace disable';
							}else if(isset ($startdate) && empty($startdate)){
								$message = 'Please add a schedule to this Project first.';
								$url ='';$class = 'workspace disable';
							}

						} else {

							if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
								$message = 'Cannot create Workspace, Project is Signed off.';
								$url ='';$class = 'workspace disable';
								$prj_tooltip = "Project Is Signed Off";
							}else if(!empty($startdate) && $startdate > $curdate ){

								$message = 'Cannot add Workspace because Project is not live (start date not reached)';
								$url ='';$class = 'workspace disable';
							}else if(!empty($enddate) && $enddate < $curdate){
								$message = 'Cannot add a Workspace because the Project end date is overdue.';
								$url ='';$class = 'workspace disable';
							}else if(isset ($startdate) && empty($startdate)){
								$message = 'Please add a schedule to this Project first.';
								$url ='';$class = 'workspace disable';
							}else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
								$message = 'You are not allowed to add workspace because project hasn\'t started yet.';
								$url ='';$class = 'workspace disable';
							}

						}

						if( isset($prj_disabled) && !empty($prj_disabled) ){
                        ?>
							<a class="workspace-button h-common-btn tipText <?php echo $prj_disabled;?>" title="<?php echo $prj_disabled_tip;?>" rel="tooltip" style="<?php echo $prj_disabled_cursor;?>" ><i class="workspace-icon"></i> </a>

						<?php } else { ?>

							<a  data-title="<?php echo $message;?>" data-headertitle="Add Workspace" class="workspace-button h-common-btn tipText <?php echo $class;?>" href="<?php echo $url; ?>" title="<?php echo $prj_tooltip;?>" rel="tooltip"  ><i class="workspace-icon"></i> </a>

				   <?php }
					}

                     ?>

<?php
                    if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($p_permission['ProjectPermission']['permit_edit']) && isset($p_permission['ProjectPermission']['permit_edit']) == 1)) {
                        if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )){
                        $user_project = $this->Common->get_project($project_id);

                        }

  ?>
                        <?php if( isset($edit_button_disable) && !empty($edit_button_disable) ){


						?><span class="hlt-sep">

							<a class="edit-button h-common-btn tipText <?php echo $edit_button_disable; ?>" rel="tooltip"  title="<?php echo tipText($edit_button_disable_tip) ?>" style="<?php echo $edit_button_disable_cursor; ?>" ><i class="edit-icon"></i> </a>
						</span>
						<?php


						} else {?>
						<span class="hlt-sep">
							<a href="<?php echo Router::Url(array('controller' => 'projects', 'action' => 'manage_project', $project_id, 'admin' => FALSE), TRUE); ?>" class="edit-button h-common-btn tipText " rel="tooltip"  title="Edit Project"><i class="edit-icon"></i> </a>
						</span>
						<?php


						}
					   //}
					} ?>




	  <?php  } } ?>

	  		<?php if (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) || (isset($p_permission['ProjectPermission']['permit_delete']) && $p_permission['ProjectPermission']['permit_delete'] == 1 )) {


			?>
				<a  data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "projects", "action" => "delete_an_item", $project_id, 'admin' => FALSE ), true ); ?>" class="workspace-button h-common-btn tipText delete-an-item" title="Delete Project"><i class="deleteblack"></i></a>

			<?php } ?>

				</div>


     	       <div class="row ">
     		    <div class="col-xs-12">
				<?php echo $this->Session->flash(); ?>

				<div class=" sep-header-fliter">
				<?php  echo $this->element('../Projects/partials/project_settings', array('menu_project_id' => $project_id));
				?>
				</div>

     			 <div class="box noborder ">

     			      <div class="box-header nopadding">
     			      	<div class="modal modal-danger fade" id="signoff_comment_box" tabindex="-1" >
						    <div class="modal-dialog">
						        <div class="modal-content border-radius">

						        </div><!-- /.modal-content -->
						    </div><!-- /.modal-dialog -->
						</div>
     			      		<div class="modal modal-danger fade" id="signoff_comment_show" tabindex="-1" >
							    <div class="modal-dialog">
							        <div class="modal-content border-radius">

							        </div><!-- /.modal-content -->
							    </div><!-- /.modal-dialog -->
							</div>

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
						// $project_workspace_count = $this->ViewModel->project_workspace_count( $project_id );

						$project_workspace_detail = $this->ViewModel->projectWorkspaces( $project_id, $perPageWspLimit, $currentWspPage );

						// pr($project_workspace_detail);
						if (isset($project_workspace_detail) && !empty($project_workspace_detail)) {

					     echo $this->Form->input('ProjectWorkspace.project_id', array('label' => false, 'id' => 'project_id', 'type' => 'hidden', 'value' => $project_id));
					     ?>
					     <?php echo $this->Form->create('ProjectWorkspace', array('url' => array('controller' => 'projects', 'action' => 'sortOrderWorkspaces'), 'class' => 'form-horizontal form-bordered table-responsive', 'id' => 'dd-form' )); ?>






<div class="workspace-tasks-sec-wrap">
    <div class="workspace-tasks-sec-top">
        <div class="workspace-col-5 padd8 text-center theader first">Workspace</div>
        <div class="workspace-col-3 padd8 text-center theader">Key Result Target</div>
        <div class="workspace-col-1 padd8 text-center theader">Tasks</div>
        <div class="workspace-col-4 padd8 text-center theader">Assets</div>
        <div class="workspace-col-2 padd8 text-center theader">Actions</div>
    </div>
    <div class="sort-able">
    <?php
		if(isset($gpid) && !empty($gpid)) {
			$wwsid = $this->Group->group_work_permission_details($project_id, $gpid);
		}

		if(isset($p_permission) && !empty($p_permission))
		{
			$wwsid = $this->Common->work_permission_details($project_id, $this->Session->read('Auth.User.id'));
		}

		// $project_workspace_count = (isset($wwsid) && !empty($wwsid)) ? count($wwsid) : $project_workspace_count;

		if(((isset($user_project)) && (!empty($user_project))) ||  (isset($p_permission['ProjectPermission']['project_level'])  && 	$p_permission['ProjectPermission']['project_level'] ==1 ) || (isset($project_level) && $project_level==1) || (isset($wwsid)))

		$i=0;

		foreach ($project_workspace_detail as $key => $project_workspace) {

		    $project_workspace_data = ( isset($project_workspace['ProjectWorkspace']) && !empty($project_workspace['ProjectWorkspace'])) ? $project_workspace['ProjectWorkspace'] : null;

		    $workspaceArray = ( isset($project_workspace['Workspace']) && !empty($project_workspace['Workspace'])) ? $project_workspace['Workspace'] : null;

		    // Show only the workspaces that are selected to display into the list. This status field is also used to show workspace names in leftbar menus.
		    $leftbar_status = $project_workspace_data['leftbar_status'];

		if ($leftbar_status) {


			if(( ((isset($wwsid) && !empty($wwsid))) &&  (in_array($project_workspace_data['id'], $wwsid)))  || ((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   )
// pr($wwsid);
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

			$i++;
		?>
		<?php echo $this->Form->input('ProjectWorkspace.' . $key . '.id', array('label' => false, 'id' => 'pwk_id_' . $key, 'type' => 'hidden', 'value' => $project_workspace_data['id'])); ?>

		<?php echo $this->Form->input('ProjectWorkspace.' . $key . '.sort_order', array('label' => false, 'type' => 'hidden', 'value' => $project_workspace_data['sort_order'], 'id' => 'pwk_sort_order_' . $key)); ?>

    <div class="workspace-tasks-sec-top" id="<?php echo $project_workspace_data['id']; ?>" data-value="<?php echo $workspaceArray['id']; ?>" data-id="<?php echo $workspaceArray['id']; ?>" data-pid="<?php echo $project_detail['Project']['id']; ?>">
        <div class="workspace-col-5 padd8 text-center tcont colm-1">

		   <div class="small-box task-inworks panel <?php echo $class_name ?>">
				<a class="inner" href="<?php echo $create_elements_link; ?>">
					<strong class="text-ellipsis" style="text-transform:none !important" title="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>" data-text="<?php echo htmlentities($workspaceArray['title'], ENT_QUOTES); ?>">
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
									?>
								</span>
								<span>Updated:
									<?php
									echo ( isset($workspaceArray['modified']) && !empty($workspaceArray['modified'])) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s',strtotime($workspaceArray['modified'])),$format = 'd M Y') : 'N/A';
									?>
								</span>
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
        </div>
        <div class="workspace-col-3 padd8 tcont description-wroks colm-2">
        	<?php
				$workspacetip = $workspaceArray['description'];
			?>
			<div style="max-height: 80px;  overflow: hidden; max-width:408px; text-overflow: ellipsis; word-break: break-word; display: inline-block;" data-placement="top" data-content="<div class='template_create'><?php echo nl2br(htmlentities($workspacetip, ENT_QUOTES)) ; ?></div> " class="key_target key_target_wsp   " data-toggle="popover" data-trigger="hover" data-delay="{show: 300, hide: 400}">
				<?php echo nl2br(htmlentities($workspaceArray['description'], ENT_QUOTES)) ; ?>
			</div>
        </div>
        <div class="workspace-col-1 padd8 text-center tcont colm-3">
			<span class="text-center el-icons">
				<ul class="list-unstyled">
					<li>
						<span class="label bg-mix" title=""><?php echo (isset($totalActElements) && !empty($totalActElements) ) ? $totalActElements : 0; ?></span>
						<span class="btn btn-xs <?php echo $class_name ?> tipText" title="<?php echo tipText('Tasks') ?>"><i class="asset-all-icon taskwhite"></i></span>
					</li>
					<li>
						<span class="label bg-mix">

							<?php echo (isset($progress_data['overdue_element_count']) && !empty($progress_data['overdue_element_count']) ) ? $progress_data['overdue_element_count'] : 0; ?>
						</span>
						<span class="btn btn-xs bg-element tipText no-change" title="Tasks Overdue"  href="#"><i class="asset-all-icon overduewhite"></i></span>
					</li>
				</ul>
			</span>
    	</div>
    	<div class="workspace-col-4 padd8 text-center tcont colm-4">
			<span class="text-center el-icons">
				<ul class="list-unstyled">
		 			<li>
					  	<span class="label bg-mix">
							<?php
							echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['links']) && !empty($progress_data['assets_count']['links'])) ? $progress_data['assets_count']['links'] : 0 ) : 0;
							?>
					  	</span>
					  	<span class="btn btn-xs bg-maroon tipText no-change" title="<?php echo tipText('Links') ?>"  href="#"><i class="asset-all-icon linkwhite"></i></span>
				 	</li>
		 			<li>
			  			<span class="label bg-mix">
							<?php
							echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['notes']) && !empty($progress_data['assets_count']['notes'])) ? $progress_data['assets_count']['notes'] : 0 ) : 0;
							?>
			  			</span>
			  			<span class="btn btn-xs bg-purple tipText no-change" title="<?php echo tipText('Notes') ?>"  href="#"><i class="asset-all-icon notewhite"></i></span>
		 			</li>
		 			<li>
				  		<span class="label bg-mix">
							<?php //pr($progress_data['assets_count']);
							echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['docs']) && !empty($progress_data['assets_count']['docs'])) ? $progress_data['assets_count']['docs'] : 0 ) : 0;
							?>
				  		</span>
				  		<span class="btn btn-xs bg-blue tipText no-change" title="<?php echo tipText('Documents') ?>"  href="#"><i class="asset-all-icon documentwhite"></i></span>
			 		</li>

			 		<li>
				  		<span class="label bg-mix">
						<?php
						echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['mindmaps']) && !empty($progress_data['assets_count']['mindmaps'])) ? $progress_data['assets_count']['mindmaps'] : 0 ) : 0;
						?>
				  		</span>
				  		<span class="btn btn-xs bg-green tipText no-change" title="<?php echo tipText('Mind Maps') ?>"  href="#"><i class="asset-all-icon mindmapwhite"></i></span>
			 		</li>


			 		<li>
			 			<?php $varDecision =  show_counters($workspaceArray['id'], 'decision'); ?>
				  		<span class="label bg-mix"><?php echo (isset($varDecision) && !empty($varDecision) && $varDecision > 0) ? $varDecision: 0; ?></span>
				  		<span class="btn btn-xs bg-orange tipText no-change" title="<?php echo tipText('Live Decisions') ?>"  href="#"><i class="asset-all-icon decisionwhite"></i></span>
			 		</li>
			 		<li>
				  		<span class="label bg-mix">
			  			<?php
				  		echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['feedbacks']) && !empty($progress_data['assets_count']['feedbacks'])) ? $progress_data['assets_count']['feedbacks'] : 0 ) : 0;
				  		?></span>
				  		<span class="btn btn-xs bg-teal tipText no-change" title="<?php echo tipText('Live Feedbacks') ?>"  href="#"><i class="asset-all-icon feedbackwhite"></i></span>
			 		</li>
			 		<li>
				  		<span class="label bg-mix">
						<?php
						echo ( isset($progress_data['assets_count']) && !empty($progress_data['assets_count'])) ? ( ( isset($progress_data['assets_count']['votes']) && !empty($progress_data['assets_count']['votes'])) ? $progress_data['assets_count']['votes'] : 0 ) : 0;
						?>
				  		</span>
				  		<span class="btn btn-xs bg-yellow tipText no-change" title="<?php echo tipText('Live Votes') ?>"  href="#"><i class="asset-all-icon votewhite"></i></span>
			 		</li>
				</ul>
			</span>
    	</div>
    	<div class="workspace-col-2 padd8 text-center tcont colm-5">
			<div class="btn-group btn-actions">
				<?php  $wid = encr($workspaceArray['id']);

			 	if((isset($wwsid) && !empty($wwsid))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission)  && $p_permission['ProjectPermission']['project_level'] ==1 )    )  )

				if(isset($gpid) && (isset($wwsid) && !empty($wwsid))){
				$wsEDDDIT =  $this->Group->group_wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

				$wsDELETE =  $this->Group->group_wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$gpid);

				}else if((isset($wwsid) && !empty($wwsid))){
				$wsEDDDIT =  $this->Common->wsp_permission_edit($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));

				$wsDELETE =  $this->Common->wsp_permission_delete($this->ViewModel->workspace_pwid($workspaceArray['id']),$project_id,$this->Session->read('Auth.User.id'));
				}

$wsp_disabled = '';
$wsp_tip = '';
$cursor = '';
if(isset($workspaceArray['sign_off']) && !empty($workspaceArray['sign_off']) && $workspaceArray['sign_off'] == 1 ){
	$wsp_disabled = 'disable';
	$wsp_tip = "Workspace Is Signed Off";
	$cursor =" cursor:default; ";
}




		   		if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) || (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )   ) ) {

					?>
					<a class="btn btn-xs <?php echo $class_name ?> tipText <?php //echo $wsp_disabled; ?>" title="<?php tipText('Update Workspace Details', false); ?>"  href="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_workspace', $project_detail['Project']['id'], $workspaceArray['id'], 'admin' => FALSE), TRUE); ?>" id="btn_select_workspace" >
			 			<i class="fa fa-fw fa-pencil"></i>
					</a>
				<?php  } ?>
				<?php
				if(((isset($wwsid) && !empty($wwsid)) && ($wsEDDDIT==1))  || (((isset($user_project)) && (!empty($user_project))) || (isset($project_level) && $project_level==1) ||  (isset($p_permission['ProjectPermission']['project_level'])  && $p_permission['ProjectPermission']['project_level'] ==1 )    ) ) { ?>

				<?php if( isset($wsp_disabled) && !empty($wsp_disabled) ){ ?>
					<a class="btn btn-xs <?php echo $class_name ?> tipText disable" title="<?php echo $wsp_tip;?>"  style="margin-right: 0 !important; <?php echo $cursor;?>">
						<i class="fa fa-paint-brush"></i>
					</a>
				<?php } else { ?>
					<a class="btn btn-xs <?php echo $class_name ?> tipText color_bucket" title="Color Options"  href="#" style="margin-right: 0 !important;">
						<i class="fa fa-paint-brush"></i>
					</a>
				<?php } ?>
					<small class="ws_color_box" style="display: none; width: 86px">
						<small class="colors btn-group">
							<b data-color="bg-red" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></b>
							<b data-color="bg-blue" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></b>
							<b data-color="bg-maroon" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
							<b data-color="bg-aqua" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
							<b data-color="bg-yellow" data-remote="<?php echo SITEURL . 'workspaces/update_color/' . $workspaceArray['id'] ?>" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
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


					<?php if( isset($wsp_disabled) && !empty($wsp_disabled) ){ ?>
					<a type="button" class="btn btn-xs tipText <?php echo $class_name ?> <?php echo $wsp_disabled; ?>" title="<?php echo ( isset($wsp_tip) && !empty($wsp_tip) ) ? $wsp_tip : "Delete"; ?>" style="<?php echo $cursor;?>">
						<i class="fa fa-trash"></i>
					</a>

					<?php } else { ?>

					<a data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "workspaces", "action" => "delete_an_item", $project_detail['Project']['id'], $workspaceArray['id'], $project_workspace_data['id'], 'admin' => FALSE ), true ); ?>" type="button" class="btn btn-xs tipText delete-an-item <?php echo $class_name ?> " title="Delete">
						<i class="fa fa-trash"></i>
					</a>
					<?php } ?>
					<a class="btn btn-xs <?php echo $class_name ?> tipText multi-remove hide-multi-delete" title="Select" href="#" data-wid="<?php echo $workspaceArray['id']; ?>" >
						<i class="fa fa-square-o"></i>
					</a>
				<?php  } ?>

	   		</div>
    	</div>
    </div>
<?php }// studio-status ?>
<?php }// workspace-id ?>
<?php }// leftbar-status ?>
<?php }// foreach

if($i < 1){

	echo "<div class='no-wsp'>No Workspaces</div>";
}

?>
</div>





	  				   <?php
	  				   	/*if($project_workspace_count > $perPageWspLimit) { ?>
		  				   	<div class="load_more_wsp">
		  				   		<span class="show-more" data-limit="<?php echo $perPageWspLimit; ?>" data-current="<?php echo $currentWspPage; ?>" data-project="<?php echo $project_id; ?>" data-total="<?php echo $project_workspace_count; ?>">More Workspaces</span>
	  				   			<i class="fa fa-spinner fa-pulse loader-icon stop"></i>
	  				   			<span class="show-less" data-limit="<?php echo $perPageWspLimit; ?>" data-current="<?php echo $currentWspPage; ?>" data-project="<?php echo $project_id; ?>" data-total="<?php echo $project_workspace_count; ?>" data-results="<?php echo $perPageWspLimit; ?>" style="display: none;">Less Workspaces</span></div>
	  					<?php }*/ ?>

						<?php echo $this->Form->end();

						} else {

						    $message = $html = '';

							$messagen = null;
	                        $startdate = isset($projectMinDetail['Project']['start_date']) && !empty($projectMinDetail['Project']['start_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['start_date'])) : '';

							$enddate = isset($projectMinDetail['Project']['end_date']) && !empty($projectMinDetail['Project']['end_date']) ? date("Y-m-d",strtotime($projectMinDetail['Project']['end_date'])) : '';


							$curdate = date("Y-m-d");
							$prj_tooltip = 'Add workspace';
	                        $class = '';
	                        $url = SITEURL.'templates/create_workspace/'.$project_id;

							if(isset($projectMinDetail['Project']['sign_off']) && $projectMinDetail['Project']['sign_off'] == 1){
								$messagen = 'Cannot create Workspace, Project is Signed off.';
								$url ='';
								$class = 'workspace disable';
								$prj_tooltip = "Project Is Signed Off";
							}else if(!empty($startdate) && $startdate > $curdate ){

								if( FUTURE_DATE == 'off' ){
									$messagen = 'Cannot add Workspace because Project is not live (start date not reached)';
									$url ='';$class = 'workspace disable';
								}

							}else if(!empty($enddate) && $enddate < $curdate){
								$messagen = 'Cannot add a Workspace because the Project end date is overdue.';
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
										$message = 'Cannot add a Workspace because the Project end date is overdue.';
										$url ='';$class = 'workspace disable';
									}else if(isset ($startdate) && empty($startdate)){
										$message = 'Please add a schedule to this Project first.';
										$url ='';$class = 'workspace disable';
									}else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
										$message = 'You are not allowed to add workspace because project hasn\'t started yet.';
										$url ='';$class = 'workspace disable';
									}


						if( isset($prj_disabled) && !empty($prj_disabled) ){
							$html = '<a data-title="'.$prj_disabled_tip.'" class="btn btn-sm btn-success tipText '.$class.' '.$prj_disabled.' " title="" rel="tooltip" data-original-title="'.$prj_disabled_tip.'" style="'.$prj_disabled_cursor.'" ><i class="fa fa-fw fa-plus"></i> </a>';
						} else {
							$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" href="'.$url.'" rel="tooltip" data-original-title="'.$prj_tooltip.'"><i class="fa fa-fw fa-plus"></i> </a>';
						}




								} else {
									  $message = "Sorry you don't have permission to create a Workspace.";
									  //$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
									  $link = '';
									  if( isset($prj_disabled) && !empty($prj_disabled) ){
										  $html = '<a data-title="'.$prj_disabled_tip.'" class="btn btn-sm btn-success tipText '.$class.' '.$prj_disabled_tip.' " title="" rel="tooltip" data-original-title="'.$prj_disabled_tip.'" style="'.$prj_disabled_cursor.'"><i class="fa fa-fw fa-plus"></i> </a>';
									  } else {
										$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';
									  }

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


										if( isset($prj_disabled) && !empty($prj_disabled) ){
											$html = '<a data-title="" class="btn btn-sm btn-success tipText '.$prj_disabled.'" title="" rel="tooltip" data-original-title="'.$prj_disabled_tip.'" style="'.$prj_disabled_cursor.'"><i class="fa fa-fw fa-plus"></i> </a>';
										} else {
										  $html = '<a data-title="" href="'.$link.'" class="btn btn-sm btn-success tipText " title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';
										}

										}


                        if(isset($user_project['Project']['sign_off']) && $user_project['Project']['sign_off'] == 1){
                            $message = 'Cannot create Workspace, Project is Signed off.';
                            $url ='';
							$class = 'workspace disable';

                        }else if(!empty($startdate) && $startdate > $curdate ){
							if( FUTURE_DATE == 'off' ){
								$message = 'Cannot add Workspace because Project is not live (start date not reached).';
								$url ='';$class = 'workspace disable';
							}
                        }else if(!empty($enddate) && $enddate < $curdate){
                            $message = 'Cannot add a Workspace because the Project end date is overdue.';
                            $url ='';$class = 'workspace disable';
                        }else if(isset ($startdate) && empty($startdate)){
                            $message = 'Please add a schedule to this Project first.';
                            $url ='';$class = 'workspace disable';
                        }else if(empty($startdate) && $startdate > $curdate && $enddate >= $curdate){
                            $message = 'You are not allowed to add workspace because project hasn\'t started yet.';
                            $url ='';$class = 'workspace disable';
                        }

							if( isset($prj_disabled) && !empty($prj_disabled) ){
								$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$prj_disabled.'" title="" rel="tooltip" data-original-title="'.$prj_disabled_tip.'" style="'.$prj_disabled_cursor.'"><i class="fa fa-fw fa-plus"></i> </a>';
							} else {

										$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" href="'.$url.'" rel="tooltip" data-original-title="'.$prj_tooltip.'"><i class="fa fa-fw fa-plus"></i> </a>';
							}


								} else {

										$message = "Sorry you don't have permission to create a Workspace.";
										//$link = Router::Url(array('controller' => 'templates', 'action' => 'create_workspace', $project_detail['Project']['id'], 'admin' => FALSE), TRUE);
										$link = '';
										$class = 'workspace disable';

										if( isset($prj_disabled) && !empty($prj_disabled) ){
											$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.'" title="" rel="tooltip" data-original-title="Add workspace"><i class="fa fa-fw fa-plus"></i> </a>';
										} else {
											$html = '<a data-title="'.$message.'" class="btn btn-sm btn-success tipText '.$class.' '.$prj_disabled.'" title="" rel="tooltip" data-original-title="'.$prj_disabled_tip.'" style="'.$prj_disabled.'"><i class="fa fa-fw fa-plus"></i> </a>';
										}
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

<input type="hidden" id="paging_page" value="0" />
<input type="hidden" id="paging_max_page" value="" />

<script  type="text/javascript">

	$(function(){



		$('body').delegate('#submit_annotate', 'click', function(event) {
			event.preventDefault();
			$that = $(this);
			$that.addClass('disabled');

			var selected_value = $('input.project_name:checked').map(function() {
				return this.value;
			}).get();

			var $form = $('#modelFormProjectComment'),
				data = $form.serialize(),
				data_array = $form.serializeArray();

			$.when(
				$.ajax({
					url: $js_config.base_url + 'projects/save_annotate/77',
					type: "POST",
					data: data,
					dataType: "JSON",
					global: false,
					success: function (response) {
						if(response.success) {
							if(response.content){
                                // send web notification
                                $.socket.emit('socket:notification', response.content.socket, function(userdata){});
                            }
							$form.find('#ProjectCommentId').val('');
							$form.find('#ProjectCommentComments').val('');
							$form.find('#ProjectCommentComments').next().html('');
							$form.find('#clear_annotate').hide();
							$that.removeClass('disabled');

								$.ajax({
								type: 'POST',
								data: $.param({ project_id: $js_config.project_id}),
								url: $js_config.base_url + 'projects/el_rag/',
								global: false,
								success: function(response) {
									$('.asset_counter').html(response);

									//dfd.resolve('');
								}
								})
						}
						else {
							if( ! $.isEmptyObject( response.content ) ) {

								$.each( response.content, function( ele, msg) {

									var $element = $form.find('[name="data[ProjectComment]['+ele+']"]')
									var $parent = $element.parent();

									if( $parent.find('span.error-message.text-danger').length  ) {
										$parent.find('span.error-message.text-danger').text(msg)

									}
								})
								$form.find('#ProjectCommentComments').val('');
								$that.removeClass('disabled');
							}
						}

					}
				})
			).then(function( data, textStatus, jqXHR ) {
				if(data.success) {
					$.ajax({
						url: $js_config.base_url + 'projects/get_annotations/' + data.content[0],
						type: "POST",
						data: $.param({}),
						dataType: "JSON",
						global: false,
						success: function (responses) {
							$('#annotate-list', $('body')).html(responses)
							$that.removeClass('disabled');
						}
					})
				}
			})
		})


		$('body').delegate('.delete_annotate', 'click', function(event) {
			event.preventDefault();

			var $parent = $(this).parents('.annotate-item:first'),
				id = $parent.data('id'),
				project_id = $js_config.project_id,
				$annotate_list = $parent.parents('#annotate-list:first');

			$.ajax({
				url: $js_config.base_url + 'projects/delete_annotate/' + id +'/'+ project_id,
				type: "POST",
				data: $.param({}),
				dataType: "JSON",
				global: true,
				success: function (response) {

					$.ajax({
								type: 'POST',
								data: $.param({ project_id: $js_config.project_id}),
								url: $js_config.base_url + 'projects/el_rag/',
								global: false,
								success: function(response) {
									$('.asset_counter').html(response);

									//dfd.resolve('');
								}
					})

					$parent.fadeOut(1000, function(){
						$(this).remove();
						if( $annotate_list.find('.annotate-item').length <= 0 ) {
							$annotate_list.html('<div id="no-annotate-list" >No Annotations</div>')
						}
					})
				}
			})

		})





		var outerPane = $(window),
        didScroll = false;

	    outerPane.scroll(function(e) { //watches scroll of the div
	        didScroll = true;
	    });

	    //Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
	    setInterval(function() {
	        if (didScroll){
	           didScroll = false;
	            // if(outerPane.scrollTop() + outerPane.innerHeight() >= outerPane[0].scrollHeight) {
            	if($(window).scrollTop() + $(window).height() == $(document).height()) {
	                $.pageCountUpdate();
	            }
	       }
	    }, 250);

		;($.getWspCount = function() {
	        var dfd = $.Deferred();
	        $.ajax({
	            url: $js_config.base_url + 'projects/get_wsp_count',
	            data: {project_id: $js_config.project_id},
	            type: 'post',
	            dataType: 'JSON',
	            success: function(response) {
	                $('#paging_page').val(0);
	                $('#paging_max_page').val(response.content);
	                dfd.resolve('task count');
	            }
	        })
	        return dfd.promise();
	    })();


	    $.loading_data = true;
	    $.pageCountUpdate = function(){
	        var page = parseInt($('#paging_page').val());
	        var max_page = parseInt($('#paging_max_page').val());
	        var last_page = Math.ceil(max_page/$js_config.wsp_limit);
	        if(page < last_page - 1 && $.loading_data){
	            $('#paging_page').val(page + 1);
	            offset = ( parseInt($('#paging_page').val()) * $js_config.wsp_limit);
	            $.getPosts(offset);
	        }
	    }

	    $.getPosts = function(page){
	    	$.loading_data = false;
	        $('#loading').remove();

	        var data = { project_id: $js_config.project_id, current_page: page, limit: $js_config.wsp_limit }
	        $.ajax({
	            type: "POST",
	            url: $js_config.base_url + "projects/load_more_wsp",
	            data: data,
	            // dataType: 'JSON',
	            beforeSend: function(){
	                // outerPane.append('<div class="loader_bar" id="loading"></div>');
	            },
	            complete: function(){
	                // $('#loading').remove();
	            },
	            success: function(html) {
	                $('.sort-able').append(html);
	                $.loading_data = true;
	            }
	         });

	    }

		$('body').delegate('.bookmark-project', 'click', function(event) {
				event.preventDefault();
				var $that = $(this);
				var project_id = $that.data('projectid');
				var workspace_id = $that.data('wspid');

				if( !$(this).hasClass('remove_pin') ){

					if( project_id > 0 && project_id !== "" ){
						$.ajax({
							type: 'POST',
							dataType: 'JSON',
							data: $.param({ 'project_id': project_id, 'status': 'add'}),
							url: $js_config.base_url + 'projects/current_project',
							global: false,
							success: function (response) {
								if( response.success ){
									$that.tooltip('hide')
									          .attr('data-original-title', 'Clear Bookmark')
									          .tooltip('fixTitle')
									          .tooltip('show');
									$that.find('i').removeClass('headerbookmark').addClass('headerbookmarkclear');
									$that.addClass('remove_pin');

								}
							},
						});
					}
				}

				if( $(this).hasClass('remove_pin') ){
					if( project_id > 0 && project_id !== "" ){
						$.ajax({
							type: 'POST',
							dataType: 'JSON',
							data: $.param({ 'project_id': project_id, 'status': 'remove' }),
							url: $js_config.base_url + 'projects/current_project',
							global: false,
							success: function (response) {
								if( response.success ){

									$that.removeClass('remove_pin');
									$that.find('i').removeClass('headerbookmarkclear').addClass('headerbookmark');
									$that.tooltip('hide')
									          .attr('data-original-title', 'Set Bookmark')
									          .tooltip('fixTitle')
									          .tooltip('show');
								}
							},
						});
					}
				}

			})
		/*$.page = 0;
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
					$('.sort-able').append(response);
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

			$this.parent().find('.loader-icon').removeClass('stop');
			$this.parent().addClass('working');
			setTimeout(function(){
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
				if($(".sort-able").find('.workspace-tasks-sec-top').length > data.limit) {
					var i = 1;
					$($(".sort-able").find('.workspace-tasks-sec-top').get().reverse()).each(function() {
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
		});*/

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
		// $('#dd-form').css({'min-height': tHeight +120});

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


		 $.when($.confirm({message: 'Are you sure you want to delete this Workspace?', title: 'Delete Workspace'}))
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

<div class="modal modal-success fade" id="model_bx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>