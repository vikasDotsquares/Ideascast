<div class="competencies-buttons-container">
   <div class="competencies-col-data competencies-col-data-1">
      <div class="panel-heading">
           <span class="peoplecount-info">People (<span class="people-count">0</span>)</span>

         <span class="short-arrow-wrap">
          <span class="short-arrow sorting sort-btns sort-fname tipText" title="Sort by First Name" data-sorted="asc" data-type="">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
              <span class="short-arrow sorting sort-btns sort-lname tipText" title="Sort by Last Name" data-sorted="asc" data-type="">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
            <!--<a class="btn btn-xs btn-control sort-btns sort-fname tipText" title="Sort by First Name" data-sorted="asc" data-type="">AZ</a>
            <a class="btn btn-xs btn-control sort-btns sort-lname tipText" title="Sort by Last Name" data-sorted="asc" data-type="">AZ</a>-->
         </span>
      </div>
   </div>
   <div class="competencies-col-data competencies-col-data-2">
      <div class="panel-heading">
         Competencies
         <span class="short-arrow-wrap">
           <span class="short-arrow sort-type tipText sorting" title="Sort by Type" data-sorted="asc" data-type="asc">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
           <span class="short-arrow sort-level tipText sorting" title="Sort by Level"  data-sorted="asc" data-type="asc">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
           <span class="short-arrow  sort-exp tipText sorting" title="Sort by Experience" data-sorted="asc" data-type="asc">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
           <span class="short-arrow sort-name tipText sorting" title="Sort by Name"  data-sorted="asc" data-type="asc">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>
          <span class="short-arrow sort-match  tipText sorting"  title="Sort by Matches"  data-sorted="asc" data-type="asc">
            <i class="fa fa-sort" aria-hidden="true"></i>
            <i class="fa fa-sort-asc" aria-hidden="true"></i>
            <i class="fa fa-sort-desc" aria-hidden="true"></i>
          </span>

           <!-- <a class="btn btn-xs btn-control sort-type tipText" title="Sort by Type" data-sorted="asc" data-type="asc">AZ</a>
            <a class="btn btn-xs btn-control sort-level tipText" title="Sort by Level" data-sorted="asc" data-type="asc">AZ</a>
            <a class="btn btn-xs btn-control sort-exp tipText" title="Sort by Experience" data-sorted="asc" data-type="asc">AZ</a>
            <a class="btn btn-xs btn-control sort-name tipText" title="Sort by Name" data-sorted="asc" data-type="asc">AZ</a>
            <a class="btn btn-xs btn-control sort-match tipText" title="Sort by Matches" data-sorted="asc" data-type="asc">AZ</a>-->
         </span>
      </div>
   </div>
   <div class="competencies-col-data competencies-col-data-3">
      <div class="panel-heading">
         Action
      </div>
   </div>
</div>
<div class="competencies-data data-wrapper">

    <?php $all_users = [];
	$current_org = $this->Permission->current_org();
    if (isset($users) && !empty($users)) {
        foreach ($users as $key => $value) {
            $all_users[] = $value['users']['id'];
        }
        $all_users = implode('~', $all_users);

        // $input = array_map("unserialize", array_unique(array_map("serialize", $users)));
        // pr($users);
        foreach ($users as $key => $value) {
            $user_id = $value['users']['id'];
            $user_details = $value['user_details'];
            $user_name = $value[0]['fullname'];

            $member_skills = $value[0]['member_skills'];
            $member_subjects = $value[0]['member_subjects'];
            $member_domains = $value[0]['member_domains'];
            $chat_html = CHATHTML($user_id);

            $profile_pic = (!empty($user_details['profile_pic'])) ? $user_details['profile_pic'] : SITEURL . 'images/placeholders/user/user_1.png';
            $user_image = SITEURL . 'images/placeholders/user/user_1.png';
            $job_title = (!empty($user_details['job_title'])) ? $user_details['job_title'] : 'Not Available';
            if (!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
                $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
            }
			$user_org = $this->Permission->current_org($user_id);

    ?>
    <div class="competencies-data-row" data-user="<?php echo $user_id; ?>" data-fname="<?php echo $user_details['first_name']; ?>" data-lname="<?php echo $user_details['last_name']; ?>">
        <div class="competencies-col-data competencies-col-data-1">

				<div class="style-people-com ">
					<span class="style-popple-icon-out">
						<a  class="style-popple-icon" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true ); ?>" class="pophover" data-content="<div class='com-user'><p><?php echo $user_name; ?></p><p><?php echo htmlentities($job_title,ENT_QUOTES); ?></p><?php echo $chat_html; ?></div>" data-original-title="" title="">
							<img src="<?php echo $user_image; ?>" class="user-image" align="left" width="40" height="40">
						</a>
					<?php if($current_org['organization_id'] != $user_org['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"></i>
					<?php } ?>
					</span>

					<div class="style-people-info"  >
						<a data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( 'controller' => 'shares', 'action' => 'show_profile', $user_id, 'admin' => FALSE ), TRUE ); ?>"   >
						<span class="style-people-name"><?php echo $user_details['first_name'].' '.$user_details['last_name']; ?></span>
						<span class="style-people-title"> <?php echo  $job_title; ?></span>
						</a>
					</div>
			 	</div>

        </div>
        <div class="competencies-col-data competencies-col-data-2">
            <ul class="competencies-list" data-matches="0">
                <?php // SKILL TAGS
                if(isset($member_skills) && !empty($member_skills)){
                    $member_skills_arr = explode("/;/", $member_skills);
                    foreach ($member_skills_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        // pr($all);
                        $sk_title = (isset($all[0])) ? $all[0] : "";
                        $sk_level = (isset($all[1])) ? $all[1] : "";
                        $sk_exp = (isset($all[2])) ? $all[2] : "";
                        $sk_id = (isset($all[3])) ? $all[3] : "";
                        $sk_date = (isset($all[4])) ? $all[4] : "";
                        // pr($sk_date);
                        $ctip = (isset($sk_date) && !empty($sk_date)) ? 'Added: '.date('d M, Y', strtotime($sk_date)) : '';

                        $selected = "";
                        if(in_array($sk_id, $selected_skills, true)) {
                            $selected = "selected";
                        }
                        // $sk_detail = $this->Permission->skill_detail($user_id, $sk_id);
                        // pr($sk_level);
                        $level_icon = $this->Permission->level_exp_icon($sk_level);
                        $exp_icon = $this->Permission->level_exp_icon($sk_exp, false);
                        $exp_num = $this->Permission->exp_number($sk_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="skills" data-level="<?php echo $sk_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($sk_title, ENT_QUOTES, "UTF-8"); ?>">
                        <i class="skills-icon tipText" title="Skill"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $sk_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$sk_exp.' Years':$sk_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_skills/<?php echo $sk_id;?>" data-target="#modal_view_skill" data-toggle="modal" class="tipText" title="<?php echo $ctip; ?>" ><?php echo htmlentities($sk_title, ENT_QUOTES, "UTF-8"); ?></span>
                    </li>
                <?php
                    }
                }
                ?>
                <?php // SUBJECT TAGS
                if(isset($member_subjects) && !empty($member_subjects)){
                    $member_subjects_arr = explode("/;/", $member_subjects);
                    foreach ($member_subjects_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        $sb_title = (isset($all[0])) ? $all[0] : "";
                        $sb_level = (isset($all[1])) ? $all[1] : "";
                        $sb_exp = (isset($all[2])) ? $all[2] : "";
                        $sb_id = (isset($all[3])) ? $all[3] : "";
                        $sb_date = (isset($all[4])) ? $all[4] : "";

                        $ctip = (isset($sb_date) && !empty($sb_date)) ? 'Added: '.date('d M, Y', strtotime($sb_date)) : '';
                        $selected = "";
                        if(in_array($sb_id, $selected_subjects, true)) {
                            $selected = "selected";
                        }
                        // $sb_detail = $this->Permission->subject_detail($user_id, $sb_id);
                        // pr($sk_detail);
                        $level_icon = $this->Permission->level_exp_icon($sb_level);
                        $exp_icon = $this->Permission->level_exp_icon($sb_exp, false);
                        $exp_num = $this->Permission->exp_number($sb_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="subjects" data-level="<?php echo $sb_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($sb_title, ENT_QUOTES, "UTF-8"); ?>">
                        <i class="subjects-icon tipText" title="Subject"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $sb_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$sb_exp.' Years':$sb_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_subjects/<?php echo $sb_id;?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" data-type="Subject" class="tipText" title="<?php echo $ctip; ?>" ><?php echo htmlentities($sb_title, ENT_QUOTES, "UTF-8");; ?></span>
                    </li>
                <?php
                    }
                }
                ?>
                <?php // DOMAIN TAGS
                if(isset($member_domains) && !empty($member_domains)){
                    $member_domains_arr = explode("/;/", $member_domains);
                    foreach ($member_domains_arr as $key => $value) {
                        $all = explode("/,/", $value);
                        $dm_title = (isset($all[0])) ? $all[0] : "";
                        $dm_level =  (isset($all[1])) ? $all[1] : "";
                        $dm_exp = (isset($all[2])) ? $all[2] : "";
                        $dm_id = (isset($all[3])) ? $all[3] : "";
                        $dm_date = (isset($all[4])) ? $all[4] : "";

                        $ctip = (isset($dm_date) && !empty($dm_date)) ? 'Added: '.date('d M, Y', strtotime($dm_date)) : '';
                        $selected = "";
                        if(in_array($dm_id, $selected_domains, true)) {
                            $selected = "selected";
                        }
                        // $dm_detail = $this->Permission->domain_detail($user_id, $dm_id);
                        // pr($sk_detail);
                        $level_icon = $this->Permission->level_exp_icon($dm_level);
                        $exp_icon = $this->Permission->level_exp_icon($dm_exp, false);
                        $exp_num = $this->Permission->exp_number($dm_exp);
                ?>
                    <li class="<?php echo $selected; ?> data-tags" data-type="domains" data-level="<?php echo $dm_level; ?>" data-exp="<?php echo $exp_num; ?>" data-name="<?php echo htmlentities($dm_title, ENT_QUOTES, "UTF-8");   ?>">
                        <i class="domain-icon tipText" title="Domain"></i>
                        <i class="<?php echo $level_icon ?> tipText" title="Level: <?php echo $dm_level; ?>"></i>
                        <i class="<?php echo $exp_icon; ?> tipText" title="Experience: <?php echo ($exp_num>1)?$dm_exp.' Years':$dm_exp.' Year'; ?>"></i>
                        <span data-remote="<?php echo SITEURL;?>competencies/view_domains/<?php echo $dm_id;?>" data-area="" data-target="#modal_view_skill" data-toggle="modal" class="tipText" title="<?php echo $ctip; ?>" ><?php echo htmlentities($dm_title, ENT_QUOTES, "UTF-8"); ; ?></span>
                    </li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div class="competencies-col-data competencies-col-data-3 actionlink">
            <a href="#" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "tags", "action" => "add_tags_team_members", "type" => "competency", "cusers" => $all_users, "selected" => $user_id, 'admin' => FALSE ), true ); ?>" data-target="#com_modal" class="action-tag tipText" title="Tag"> <i class="comptag"></i> </a>
            <a href="#" class="action-col-exp tipText" title="Expand">
                <i class="more-icon"></i>
                <i class="less-icon"></i>
            </a>
			<a href="<?php echo Router::Url( array( "controller" => "searches", "action" => "people", "user" => $user_id, 'admin' => FALSE ), true ); ?>" class="tipText" title="Go To People"><i class="peopleblack18"></i></a>

        </div>
    </div>
    <?php }
    }else{ ?>
    <div class="no-res-found">NO PEOPLE</div>
    <?php } ?>
</div>



<script type="text/javascript">
    $(function(){

        $parent_tab = $('#tab_search');

        $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });

        ;($.showHideCollapseBtn = function(){
            $('.competencies-col-data-2', $('.competencies-data.data-wrapper')).each(function(index, el) {
                var $parent = $(this).parents('.competencies-data-row:first');
                var $btn = $(this).parents('.competencies-data-row:first').find('.action-col-exp');
                if ($(this)[0].scrollHeight >  $(this).innerHeight()) {
                    $btn.show();
                }
                else{
                    $btn.hide();
                }
            });
        })();

        $(window).resize(function(event) {
            $.showHideCollapseBtn();
        });

        $('.competencies-list').each(function(index, el) {
            var matches = $(this).find('li.data-tags.selected').length;
            $(this).data('matches', matches);
        });

        var $list_wrapper = $('.competencies-data.data-wrapper')
        $.alphabate_sort = function(params) {
            var $this = $(params.this);
            var $arrow = $('.short-arrow').not(params.this)

            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');
            var data = $(params.this).data();
            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == '') $(params.this).data('sorted', 'asc');
            var data = $(params.this).data();

            if (data && data.sorted == 'asc') {
                var ascending = $('.competencies-data-row').sort(function(a, b) {
                    var contentA = $(a).attr(params.attr);
                    var contentB = $(b).attr(params.attr);
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                });
                ascending.each(function(index, el) {
                    $(this).appendTo($list_wrapper)
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                var descending = $('.competencies-data-row').sort(function(a, b) {
                    var contentA = $(a).attr(params.attr);
                    var contentB = $(b).attr(params.attr);
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                })
                descending.each(function(index, el) {
                    $(this).appendTo($list_wrapper)
                });
                sorted = 'asc';
            }
           // $('.sorting').removeClass('active')
           // $(params.this).data('sorted', sorted).attr('data-sorted', sorted).addClass('active')//.text(text);
			$(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }
        /*$('.sort-fname').off('click').on( 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-fname'
            }
            $.alphabate_sort(params)
        })*/
        $('body').delegate('.sort-lname', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-lname'
            }
            $.alphabate_sort(params)
        })

// 501443879
// 1040
        $.type_sort = function(params) {

            var $arrow = $('.short-arrow').not(params.this)
            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');
            var data = $(params.this).data();
            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == 'asc') {
                var ascending1 = ascending2 = ascending3 = $({});
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending1 = $('.data-tags[data-type="skills"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending2 = $('.data-tags[data-type="subjects"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending2.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending3 = $('.data-tags[data-type="domains"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending3.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                var descending1 = descending2 = descending3 = $({});
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending1 = $('.data-tags[data-type="domains"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending2 = $('.data-tags[data-type="subjects"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending2.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending3 = $('.data-tags[data-type="skills"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending3.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                })
                sorted = 'asc';
            }
            $(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }

        $.level_sort = function(params) {

            var $arrow = $('.short-arrow').not(params.this)
            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');
            var data = $(params.this).data();
            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == 'asc') {
                var ascending1 = ascending2 = ascending3 =  ascending4 = $({});
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending1 = $('.data-tags[data-level="Beginner"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending2 = $('.data-tags[data-level="Intermediate"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending2.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending3 = $('.data-tags[data-level="Advanced"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending3.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var ascending4 = $('.data-tags[data-level="Expert"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending4.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                var descending1 = descending2 = descending3 =  descending4 = $({});
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending1 = $('.data-tags[data-level="Expert"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending2 = $('.data-tags[data-level="Advanced"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending2.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending3 = $('.data-tags[data-level="Intermediate"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending3.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                })
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending4 = $('.data-tags[data-level="Beginner"]', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending4.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                })
                sorted = 'asc';
            }
            $(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }

        $.numeric_sort = function(params) {

            var $arrow = $('.short-arrow').not(params.this)
            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');
            var data = $(params.this).data();
            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == 'asc') {

                $('.competencies-list').each(function(ind, els) {
                    var $ul = $(this)
                    var ascending = $('.data-tags', $ul).sort(function(a, b) {
                        var contentA = parseInt($(a).attr(params.attr));
                        var contentB = parseInt($(b).attr(params.attr));
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                $('.competencies-list').each(function(index, el) {
                    var $ul = $(this)
                    var descending = $('.data-tags', $ul).sort(function(a, b) {
                        var contentA = parseInt($(a).attr(params.attr));
                        var contentB = parseInt($(b).attr(params.attr));
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                })
                sorted = 'asc';
            }
            $(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }

        $.matches_sort = function(params) {

            var $arrow = $('.short-arrow').not(params.this)
            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');
            var data = $(params.this).data();
            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == 'asc') {
                var ascending = $('.competencies-data-row').sort(function(a, b) {
                    var contentA = $(a).find('.competencies-list').data('matches');
                    var contentB = $(b).find('.competencies-list').data('matches');
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                });
                ascending.each(function(index, el) {
                    $(this).appendTo($list_wrapper)
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                var descending = $('.competencies-data-row').sort(function(a, b) {
                    var contentA = $(a).find('.competencies-list').data('matches');
                    var contentB = $(b).find('.competencies-list').data('matches');
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                })
                descending.each(function(index, el) {
                    $(this).appendTo($list_wrapper)
                });
                sorted = 'asc';
            }
            $(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }

        $.other_sort = function(params) {
            var data = $(params.this).data();

            var $arrow = $('.short-arrow').not(params.this)
            $('.fa-sort, .fa-sort-asc, .fa-sort-desc', $arrow).removeAttr('style');

            var sorted = 'asc';
            var text = 'fa-sort-desc';

            if (data && data.sorted == 'asc') {
                $('.competencies-list', $parent_tab).each(function(index, el) {
                    var $ul = $(this)
                    var ascending1 = $('.data-tags', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                    });
                    ascending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                sorted = 'desc';
                text = 'fa-sort-asc';
            } else if (data && data.sorted == 'desc') {
                var descending1 = $({});
                $('.competencies-list', $parent_tab).each(function(index, el) {
                    var $ul = $(this)
                    var descending1 = $('.data-tags', $ul).sort(function(a, b) {
                        var contentA = $(a).attr(params.attr);
                        var contentB = $(b).attr(params.attr);
                        return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                    })
                    descending1.each(function(index, el) {
                        $(this).appendTo($ul)
                    });
                });
                sorted = 'asc';
            }
            $(params.this).data('sorted', sorted).find('i').hide()
			$(params.this).find('i.'+text).show();
        }

        $('body').delegate('.sort-type', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-type'
            }
            $.type_sort(params)
        })
        $('body').delegate('.sort-level', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-level'
            }
            $.level_sort(params);
        })
        $('body').delegate('.sort-exp', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-exp'
            }
            $.numeric_sort(params);
        })
        $('body').delegate('.sort-name', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-name'
            }
            $.other_sort(params);
        })

        $('body').delegate('.sort-match', 'click', function(event) {
            event.preventDefault();
            var params = {
                this: $(this)[0],
                attr: 'data-matches'
            }
            $.matches_sort(params);
        })

    })
    $(function(){

        var outerPane = $('.competencies-data.data-wrapper'),
            didScroll = false;


        outerPane.scroll(function() { //watches scroll of the div
            didScroll = true;
        });

        //Sets an interval so your div.scroll event doesn't fire constantly. This waits for the user to stop scrolling for not even a second and then fires the pageCountUpdate function (and then the getPost function)
        setInterval(function() {
            if (didScroll){
               didScroll = false;
               // if(($(document).height()-$(window).height()) - $(window).scrollTop() < 10){
                if(outerPane.scrollTop() + outerPane.innerHeight() >= outerPane[0].scrollHeight - 20)
                {
                    $.pageCountUpdate();
                }
           }
        }, 250);

    })
</script>