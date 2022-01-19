<?php
// $this->set(compact("table","table_id","table_field","table_field_text","table_field_data"));
$middlepart = $this->Search->getAjaxProjects ( $table, $table_id, $table_field, $table_field_text );
$table_value = $this->Search->table_array ();
$field_html = $middlepart ['field_html'];

$permission_check = $middlepart ['permission_check'];

$icon = isset ( $permission_check ) && $permission_check == true ? 'ico_badge_user' : 'ico_badge_blank';

$icon_tooltip = isset ( $permission_check ) && $permission_check == true ? 'Access' : 'No Access';





if (isset ( $middlepart ['project_id'] ) && ! empty ( $middlepart ['project_id'] )) {
	$project_title = $middlepart ['project_title'];
	$users = $this->Search->getProjectCreatorHtml ( $middlepart ['project_id'] );
	$id = $middlepart ['project_id'];
	$PP = $this->Search->getProjectPermission ( $id );
	$target = '';
	if ($PP == true) {
		$url = SITEURL . 'projects/index/' . $middlepart ['project_id'];
	} else {
		$target = '';
		$url = 'javascript:void(0);';
	}
} else {


$users = '<img src="' . SITEURL . 'img/image_placeholders/logo_placeholder.gif" style="margin: 0px 10px 10px 0px;" width="40" align="left" class="search-user-image">';

if($table =='template_relations'){

$dd =  getByDbId('TemplateRelation',$table_id) ;
$icon =  'ico_badge_user' ;
$icon_tooltip =  'Access';
$userDetail = $this->ViewModel->get_user($dd['TemplateRelation']['user_id'] , null, 1 );

if(isset($userDetail) && !empty($userDetail)) {
 $profile_pic = $userDetail['UserDetail']['profile_pic'];
if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
$users = '<img src="'.$user_image.'" style="margin: 0px 10px 10px 0px;" width="40" align="left" class="search-user-image">';

}

}
}


	$target = '';
	$url = 'javascript:void(0);';
	$id = 0;
	$project_title = 'N/A';
}
$html = $users;

$html .= '<div class="details-title-wrapper">
			<div class="details-title">
				<div class="rows row-1">Project:
					<a ' . $target . ' id="search-project-' . $id . '"  href="' . $url . '" class="participate-link">' . $project_title . '</a>
				</div>
				<div class="rows row-2">
					<span title="' . $icon_tooltip . '" class="ico_badge tipText ' . $icon . '"></span>
				</div>
			</div>
		</div>';
$html .= "  <div class='find-in'><b>$table_value[$table]" . ' ' . preg_replace('/s$/', '', $table_field)  . ":</b></div>";
$html .= '  <div class="details">';
$html .= "<div class='type'> <spam >" . $field_html . "</spam></div>";
$html .= '  </div>';
echo $html;
?>

<div class="modal modal-success fade" id="popup_model_box_profile"
	style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content border-radius">
			<div
				style="background: #303030 none repeat scroll 0 0; display: block; padding: 100px; width: 100%;">
				<img src="<?php echo SITEURL; ?>images/ajax-loader-1.gif"
					style="margin: auto;">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    $(function () {
        $("#popup_model_box_profile").on('hidden.bs.modal', function () {
            $(this).data('bs.modal', null);
        });


		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
		$('a[href="#"][data-toggle="modals"]').attr('href', 'javascript:;');
		$('a[href=""][data-toggle="modal"]').attr('href', 'javascript:void(0);');

        $('.search-items a[href*=#]').click(function (event) {
            var target = $(this.getAttribute('href'));
            var $this = $(this);
            //$(".search-div-main").hide();




            $(".search-div-main").fadeOut(1000, "linear", function () {});
            $("#ajax_overlay").show();
            setTimeout(function () {
			//	$this.addClass('chat-shad');
                $("#ajax_overlay").hide();
                $(".search-div-main" + $this.attr("href")).fadeIn(1600, "linear");
            }, 1100);
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    //scrollTop: (target.offset().top)-'125'
                }, 1000);
            }
        });
        $('.pophover').popover({
            placement: 'bottom',
            trigger: 'hover',
            html: true,
            container: 'body',
            delay: {show: 50, hide: 400}
        })
    })

</script>

<style type="text/css">
.mark, mark {
	padding: .2em;
	color: #f00;
	background: none !important;
}

.search-div-main .result-item:last-child {
	border-bottom: 0px !important;
}
</style>
