<?php
$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read("Auth.User.id"));
$user_project = $this->Common->userproject($project_id, $this->Session->read("Auth.User.id"));
$gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read("Auth.User.id"));

if (isset($gp_exists) && !empty($gp_exists)) {
    $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
}
$type = (isset($type) && !empty($type)) ? $type : "comment";
?>
<div class="panel-group" id="<?php echo $type;?>-page-accordion">
    <?php
    if (isset($bloglist) && !empty($bloglist)) {
        foreach ($bloglist as $blog) {
            $blog_id  = $blog['Blog']['id'];

			$blogconter = $this->TeamTalk->getBlogCounter($project_id,$blog_id);
			$userBlog = $this->TeamTalk->userBlogLike($blog['Blog']['project_id'],$blog_id,$this->Session->read('Auth.User.id'));


            ?>
            <div class="panel pan-uls panel-default page-collapse-<?php echo $blog['Blog']['id'] ?>">
                <div class="panel-heading bg-curious-Blue">
                    <h4 class="panel-title">
                        <a class="accordion-toggle page-accordion collapsed trigerblogcomments" data-parent="#<?php echo $type;?>-page-accordion" href="javascript:void(0)">
                            <?php
							/*echo html_entity_decode($this->Text->truncate(
								$blog['Blog']['title']),
								45,
								array(
									'ellipsis' => '...',
									'exact' => false
								)
							);*/
							echo html_entity_decode($blog['Blog']['title']); ?>
                        </a>
                    </h4>
                </div>
                <div id="<?php echo $type;?>-page-collapse-<?php echo $blog['Blog']['id'] ?>" class="panel-collapse collapse">
                    <div class="panel-body nopadding">

					<div class="idea-right-up-blog padding">
					   <div class="can-img"></div>


                        <div class="btn-group">
                        <?php /* if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) { ?>
                            <a href="" data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="btn btn-xs btn-success tipText full_permission" data-remote="<?php echo SITEURL; ?>wikies/update_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $blog['Blog']['id']; ?>" data-id="<?php echo $blog['Blog']['id']; ?>" ><i class="fa fa-pencil"></i></a>
                        <?php } else { ?>
                            <a data-target="#modal_create_wiki_page" data-original-title="Edit Wiki Page" data-toggle="modal" class="disabled btn btn-xs btn-success tipText not_full_permission"><i class="fa fa-pencil"></i></a>
                        <?php } ?>
                        <?php if (isset($is_full_permission_to_current_login) && $is_full_permission_to_current_login === true) { ?>
                            <a href="" data-original-title="Delete Wiki Page" class="btn btn-xs btn-danger tipText delete_wiki_page full_permission" data-remote="<?php echo SITEURL; ?>wikies/delete_wiki_page/<?php echo $project_id . '/' . $this->Session->read('Auth.User.id') . '/' . $wiki_id . '/' . $blog['Blog']['id']; ?>" data-id="<?php echo $blog['Blog']['id']; ?>" data-user-id="<?php echo $blog['Blog']['user_id']; ?>"><i class="fa fa-trash"></i></a>
                        <?php } else { ?>
                            <a href="" data-original-title="Delete Blog" class="btn btn-xs btn-danger disabled tipText not_full_permission" ><i class="fa fa-trash"></i></a>
                        <?php } */ ?>


						<a href="#blog_all_detail_<?php echo $blog['Blog']['id']; ?>" data-toggle="collapse"  title="Blog Details" class="tipText btn btn-xs btn-default wikipage collapsed"><i class="fa page-details-show"></i></a>


						<a href="<?php echo SITEURL.'team_talks/index/project:'.$blog['Blog']['project_id'].'/blog:'.$blog['Blog']['id']; ?>" title="Open Blog Post" class="tipText btn btn-xs btn-default wikippost"><i class="fa fa-folder-open"></i></a>



                        <a title="Blog Comments" data-value="<?php echo $blog['Blog']['id']; ?>" data-project="<?php echo $blog['Blog']['project_id']; ?>"  class="tipText btn btn-xs btn-default blogcomments"><i class="fa fa-comments"></i></a>

                        </div>


						<?php if( $userBlog <= 0 && $blog['Blog']['user_id'] != $this->Session->read('Auth.User.id') ){?>
							<a id="blog_like" data-value="<?php echo $blog['Blog']['id'];?>" style="cursor:pointer;" class="btn btn-xs btn-default ">
								<i class="fa fa-thumbs-o-up"></i>
								<span class="label bg-purple" id="blogcounter<?php echo $blog['Blog']['id'];?>"><?php echo $blogconter; ?></span>
							</a>

						<?php } else { ?>

							<a style="cursor:pointer;" class="btn btn-xs btn-default disabled">
								<i class="fa fa-thumbs-o-up"></i>
								<span class="label bg-purple" ><?php echo $blogconter; ?></span>
							</a>
						<?php

							$project_id = $blog['Blog']['project_id'];
							$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
							$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
							$gp_exists = $this->Group->GroupIDbyUserID($project_id,$this->Session->read('Auth.User.id'));

							if( isset($gp_exists) && !empty($gp_exists) ){
							  $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
							}

							if ( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) { }
							     ?>

					<?php } ?>


						<div class="collapse description margin-top" id="blog_details_<?php echo $blog['Blog']['id']; ?>" >
                           <?php echo html_entity_decode($blog['Blog']['description']); ?>
                        </div>
                        </div>
                        <div class="idea-right-down-blog">
                        <div class="collapse" id="blog_all_detail_<?php echo $blog['Blog']['id']; ?>">
                            <ul class="updateDetail">
                                <li>Created by:
                                    <?php
                                    echo (isset($blog['Blog']['user_id']) && !empty($blog['Blog']['user_id'])) ? $this->Common->userFullname($blog['Blog']['user_id']) : 'N/A';
                                    ?>
                                </li>
                                <li>Created On:
                                    <?php
                                echo (isset($blog['Blog']['created']) && !empty($blog['Blog']['created'])) ? _displayDate($blog['Blog']['created']) : 'N/A';
                                    ?>
                                </li>

                                <li>Created On:
                                    <?php
                                echo (isset($blog['Blog']['updated']) && !empty($blog['Blog']['updated'])) ? _displayDate($blog['Blog']['updated']) : 'N/A';
                                    ?>
                                </li>
								<li>Last Updated by:
                                    <?php
                                    echo (isset($blog['Blog']['user_id']) && !empty($blog['Blog']['user_id'])) ? $this->Common->userFullname($blog['Blog']['user_id']) : 'N/A';
                                    ?>
                                </li>

                                <?php /* <li id="updatedtext-<?php echo $blog['Blog']['id'];?>">Updated:
                                    <?php
                                    $updated = (isset($blog['Blog']['updated']) && isset($blog['Blog']['updated_user_id']) && $blog['Blog']['updated_user_id'] != null) ? $blog['Blog']['updated'] : '';
                                    echo (isset($updated) && !empty($updated)) ? _displayDate(date("Y-m-d h:i:s",$updated)) : 'N/A';
                                    ?>
                                </li>
                                <li id="updatedbytext-<?php echo $blog['Blog']['id'];?>">Updated by:
                                <?php
                                $wikiupdatedusername = (isset($blog['Blog']['updated_user_id']) && $blog['Blog']['updated_user_id'] != null) ? $this->Common->userFullname($blog['Blog']['updated_user_id']) : '';
                                echo (isset($wikiupdatedusername) && $wikiupdatedusername != '') ? $wikiupdatedusername : 'N/A';
                                ?>
                                </li>
                                <li id="signofftext-<?php echo $blog['Blog']['id'];?>">Sign Off:
                                <?php
                                echo (isset($blog['Blog']['sign_off']) && $blog['Blog']['sign_off'] == 0) ? 'NO' : 'Yes';
                                ?>
                                </li> */ ?>
                            </ul>

                        </div>
						</div>



                    </div>


                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="text-center "> No blog found! <!--<a href="" class="backtoread" >  Back</a>--></div>
        <?php
    }
    ?>
</div>
<script type="text/javascript" >
    $(function ($) {
        //$('body').delegate('.backtoread', 'click', function(event) {
//        $(".backtoread").click(function (e) {
//            e.preventDefault();
//            $('.nav .active a').trigger("click")
//        })
    })
</script>

<script type="text/javascript" >
    $(function ($) {
		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
       //$("body").delegate(".wikipagecomments", 'click', function (e) {
        $(".wikipagecomments").click(function (e) {
            e.preventDefault();
            var $current = $(this), project_id = '<?php echo isset($project_id)? $project_id : 0; ?>', wiki_id = '<?php echo isset($wiki_id)? $wiki_id : 0; ?>', user_id = $current.data("user-id"), actionURL = $current.data("remote");

            $.ajax({
                url: actionURL,
                type: "POST",
                async: false, //blocks window close
                global:true,
                data: {project_id: project_id, wiki_id: wiki_id, user_id: user_id},
                beforeSend: function () {
                    //$(".wiki-left-section").html('<div class="loader"></div>');
                },
                complete: function () {
                    $(".wikipagecomments").removeClass("active");
                    $current.addClass("active");
                    $('.tooltip').hide()
                    //$(".wiki-left-section").html("");
                },
                success: function (response) {
                    $('.tooltip').hide()
                    $("#comments_list").html(response);
                }
            });
            return;
        });
    })
</script>