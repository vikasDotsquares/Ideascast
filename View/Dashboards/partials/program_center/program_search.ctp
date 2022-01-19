
<?php
$current_user_id = $this->Session->read('Auth.User.id');
$counter = 0;
if(isset($program_data) && !empty($program_data)) {
    // pr($program_data);
    foreach ($program_data as $prm_key => $prm_value) {
        if(isset($prm_value['Projects']) && !empty($prm_value['Projects'])) {
            $counter++;
        }
    }
}
?>
<!--     <div class="panel-heading program-search-heading" style="position: relative;">
        <h3 class="panel-title">PROGRAMS (<?php echo $counter; ?>)</h3>
        <div class="program-search">
            <div class="input-group">
                <input id="programsearch" name="programsearch" type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                <button class="btn btn-danger search_clear" style="display:none;" type="button"><i class="fa fa-times"></i></button>
                <button class="btn btn-success search_submit" type="button"><i class="fa fa-search"></i></button>
            </span>
            </div>
        </div>
        <?php if (isset($program_data) && !empty($program_data)){ ?>
        <a href="#" id="" class="btn btn-primary btn-xs toggle-accordion tipText" title="Expand All" accordion-id="#accordion">
            <i class="fa"></i>
        </a>
        <?php } ?>
    </div> -->
    <!-- <div class="panel-body filter-projects"> -->
        <div class="panel-group panel-custom SearchT" id="accordion">
            <?php if(isset($program_data) && !empty($program_data)) {?>
            <?php foreach ($program_data as $prm_key => $prm_value) {
        $program = $prm_value;
        // find all projects under this program
        $program_projects = (isset($program['ProjectProgram']) && !empty($program['ProjectProgram'])) ? $program['ProjectProgram'] : null;
        if( isset($program_projects) && !empty($program_projects) ) {
            $prg_prj_id = Set::extract($program_projects, '{n}.project_id');
            $prg_prj_data = getByDbIds('Project', $prg_prj_id, ['id', 'title']);
            $prg_prj_detail = Set::combine($prg_prj_data, '/Project/id', '/Project/title');
            $prg_prjs = array_map(function($v){
                return trim(strip_tags($v));
            }, $prg_prj_detail);

        ?>
            <div class="panel panel-default SearchTerm" data-prmid="<?php echo $program['Program']['id']; ?>">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-anchor">
                            <span class="program-title"><?php echo $program['Program']['program_name']; ?> (<span><?php echo (isset($program_projects) && !empty($program_projects)) ? count($program_projects) : 0; ?></span>)</span>
                            <div class="right-options">
                                <span class="ico-nudge ico-program-center tipText" title="Send Nudge"  data-toggle="modal" data-target="#modal_nudge" data-remote="<?php echo Router::url(array('controller' => 'boards', 'action' => 'send_nudge_board', 'program' => $program['Program']['id'], 'type' => 'program', 'admin' => false)); ?>"></span>
                                <div class="project-multi-select" id="project_multi_select_<?php echo $program['Program']['id']; ?>" style="display: none; min-width: 200px; max-width: 200px;">
                                    <div class="dropdown select-drop-wrap">
                                        <span class="select-drop dropdown-toggle" id="dropdownMenu<?php echo $program['Program']['id']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span class="select-span">All Projects</span><span class="arrow"></span></span>
                                        <ul class="dropdown-menu select-drop-list" aria-labelledby="dropdownMenu<?php echo $program['Program']['id']; ?>">
                                            <?php foreach ($prg_prjs as $pid => $pname) { ?>
                                            <li data-pid="<?php echo $pid; ?>"><span href="#"><span class="pname" data-title="<?php echo $pname; ?>"><?php echo $pname; ?></span> <span class="check-mark">✔</span></span></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>

                                <span class="btn btn-xs btn-white icon-chart" style="display: none;">
                                    <i class="color-chart"></i>
                                </span>
                                <span class="show-hide-program btn btn-xs btn-white tipText" title="Show Project Cards" data-toggle="collapse"  data-parent="#accordion" href="#collapse<?php echo $program['Program']['id']; ?>" aria-expanded="false">
                                    <i class="fa"></i>
                                </span>
                            </div>
                        </a>
                    </h4>
                </div>
                <div id="collapse<?php echo $program['Program']['id']; ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="scroll-horizontal">
                            <!-- <div class="ph-box">
                                <div class="ph-box-item"></div>
                                <div class="ph-box-item"></div>
                                <div class="ph-box-item"></div>
                                <div class="ph-box-item"></div>
                                <div class="ph-box-item"></div>
                            </div> -->
                        </div>
                        <div class="cost-risk-map">
                            <div class="cost_center "></div>
                            <div class="chart_center"></div>
                        </div>
                    </div>
                </div>

            </div>
            <?php } else {
					?>
					<div class="panel panel-default SearchTerm" data-prmid="<?php echo $program['Program']['id']; ?>">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-anchor">
									<span class="program-title"><?php echo $program['Program']['program_name']; ?> (<span>0</span>)</span>
									<div class="right-options">
										<span class="btn btn-xs btn-white tipText no-project-cards" title="No Project Cards"   data-parent="#accordion" aria-expanded="false">
											<i class="fa"></i>
										</span>
									</div>
								</a>
							</h4>
						</div>
					</div>
					<?php
				}
            }
        }
        if($program_count <= 0){ ?>
            <div style="" class="no-data">No Programs</div>
            <?php } ?>
        </div>
    <!-- </div> -->
<script type="text/javascript">
    $(function() {

        $.reset_project_list = function($ul) {
            $('li', $ul).removeClass('selected');
            $('.select-span', $ul).text('All Projects');
        }

        $('.select-drop-list li').on('click', function(e) {
            $(this).toggleClass('selected');
            $('.select-drop-list li').not(this).removeClass('selected');
            var $dd = $(this).parents('.select-drop-wrap:first');
            var $ul = $(this).parent();
            var selected = $( "li.selected", $ul )
                                .map(function() {
                                    return this ;
                                });

            if(selected.length <= 0) {
                $('.select-span', $dd).text('All Projects');
            }
            else{
                $('.select-span', $dd).text($(this).find('.pname').data('title'));
            }
            var $panel = $(this).parents('.panel:first');
            $.cost_chart($panel, false);
        });

        $.load_cost = function(data, $panel) {
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'Dashboards/program_center_costs',
                type: 'POST',
                global: false,
                data: data,
                success: function(response) {
                    $('.cost_center', $panel).html(response);
                    dfd.resolve('risk deleted');
                }
            })
            return dfd.promise();
        }

        $.load_chart = function(data, $panel) {
            var dfd = new $.Deferred();
            $.ajax({
                url: $js_config.base_url + 'Dashboards/program_center_charts',
                type: 'POST',
                global: false,
                data: data,
                success: function(response) {
                    $('.chart_center', $panel).html(response);
                    $('.chat-data.tasks .task-chart', $panel).html($.tasks_chart())
                    $('.chat-data.risk .risk-chart', $panel).html($.exposure_chart())
                    dfd.resolve('risk deleted');
                }
            })
            return dfd.promise();
        }

        $.cost_chart = function($panel, loader){
            // return;
            var loader = loader || false;
            var selected = $('.select-drop-list li.selected', $panel);
            var program_id = $panel.data('prmid');
            var selectedProjects = [];
            var selected_project_id = 0;
            if(selected.length <= 0) {
                $( "li", $panel ).each(function(index, el) {
                    selectedProjects.push($(this).data('pid'));
                });
            }
            else{
                $( "li.selected", $panel ).each(function(index, el) {
                    selectedProjects.push($(this).data('pid'));
                    selected_project_id = $(this).data('pid');
                });
            }
                $('.cost_center', $panel).html('<p class="loading-bar"></p>');
                $('.chart_center', $panel).html('');
            var cost_data = { projects: selectedProjects };
            $.load_cost(cost_data, $panel)
                .done(function() {
                    $('.chart_center', $panel).html('<p class="loading-bar"></p>');
                    var chart_data = { projects: selectedProjects, program_id: program_id, selected_project_id: selected_project_id }
                    $.load_chart(chart_data, $panel)
                        .done(function(){
                            // console.log('cost chart loaded successfully.')
                        })
                })
        }
/*
        $('.show-hide-program').on('click', function(event) {
            var $this = $(this),
                $panel = $(this).parents('.panel:first'),
                $rightOption = $(this).parents('.right-options:first'),
                program_id = $panel.data('prmid'),
                rag_status = $('#rag_status').val();
            var selectedProjects = $.multi_select.multi_select('getSelectedValues');
            if(!$(this).hasClass('loaded')) {
                $(this).addClass('loaded');
                $('.scroll-horizontal', $panel).html('<p class="loading-bar"></p>');
                $.ajax({
                    url: $js_config.base_url + 'dashboards/project_cards',
                    type: 'POST',
                    data: {program_id: program_id, rag: rag_status, projects: selectedProjects},
                    success: function(response){
                        $('.scroll-horizontal', $panel).html(response);
                    }
                })
            }
            $panel.toggleClass('opened');
            if(!$('.toggle-accordion').hasClass('toggle-active')){
                $('.panel.SearchTerm.opened').not($panel).removeClass('opened');
                $('.panel.SearchTerm').not($panel).each(function(index, el) {
                    $('.icon-chart,.project-multi-select', $(this)).hide();
                    if(!$(this).hasClass('opened') && $(this).hasClass('map-view')){
                        $('.icon-chart', $(this)).trigger('click');
                        $.reset_project_list($(this));
                    }

                });
                if(!$panel.hasClass('opened')) {
                    $('.icon-chart,.project-multi-select', $panel).hide();
                    if($panel.hasClass('map-view')) {
                        $('.icon-chart', $panel).trigger('click');
                        $.reset_project_list($(this));
                    }
                }
                else{
                    $('.icon-chart', $panel).show();
                }
            }

            if($('.toggle-accordion').hasClass('toggle-active')){
                $('.panel.SearchTerm:not(.opened)').each(function(index, el) {
                    if($(this).hasClass('map-view')){
                        $('.icon-chart', $(this)).trigger('click');
                        $.reset_project_list($(this));
                    }
                    $('.icon-chart,.project-multi-select', $(this)).hide();
                });
            }
        })

        $('.icon-chart').on('click', function(event) {
            event.preventDefault();
            var $parent = $(this).parents('.panel:first');
            $parent.toggleClass('map-view');
            $.reset_project_list($parent);

            if($parent.hasClass('map-view')) {
                $parent.find('.project-multi-select').show();
                $parent.find('.scroll-horizontal').fadeOut('slow', function(){
                    $parent.find('.cost-risk-map').fadeIn('slow');
                })
            }
            else{
                $parent.find('.project-multi-select').hide();
                $parent.find('.cost-risk-map').fadeOut('slow', function(){
                    $parent.find('.scroll-horizontal').fadeIn('slow');
                })
            }

            $(this).toggleClass('gray');
            setTimeout(function(){
                $.cost_chart($parent);
            }, 500)

        })
*/
        $('.panel-collapse').on('hidden.bs.collapse', function(e) {
            $('.show-hide-program').each(function(index, el) {
                if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
                    $(this).attr('title', '').attr('data-original-title', 'Show Project Cards');
                } else {
                    $(this).attr('title', '').attr('data-original-title', 'Close Project Cards');
                }
            });
            $('.icon-chart,.project-multi-select', $(this).parents('.panel.SearchTerm')).hide();
        });
        $('.panel-collapse').on('shown.bs.collapse', function(e) {
            var $parent = $(this);
            $('.show-hide-program').each(function(index, el) {
                if ($(this).attr('aria-expanded') == "false" || $(this).attr('aria-expanded') == false) {
                    $(this).attr('title', '').attr('data-original-title', 'Show Project Cards');
                } else {
                    $(this).attr('title', '').attr('data-original-title', 'Close Project Cards');
                }
            });
            $('.icon-chart', $(this).parents('.panel.SearchTerm')).show();
        });

    })
</script>
<style type="text/css">
	.no-project-cards{
		cursor: default;
		filter: alpha(opacity=65);
		-webkit-box-shadow: none;
		box-shadow: none;
		opacity: .65;
	}
	.panel-custom .panel-heading a.accordion-anchor .no-project-cards[aria-expanded="true"] i.fa:before {
		content: "\f068";
		-webkit-transform: rotate(180deg);
		transform: rotate(180deg);
	}

	.panel-custom .panel-heading a.accordion-anchor .no-project-cards[aria-expanded="false"] i.fa:before {
		content: "\f067";
		-webkit-transform: rotate(90deg);
		transform: rotate(90deg);
	}
    .ph-box {
        display: block;
        position: relative;
    }
    .ph-box-item {
        width: 248px;
        height: 311px;
        display: inline-block;
        background-color: #e3e3e3;
        margin-right: 10px;
    }
    .ph-box::before {
        content: " ";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 50%;
        z-index: 1;
        width: 500%;
        margin-left: -250%;
        -webkit-animation: phAnimation 0.8s linear infinite;
                animation: phAnimation 0.8s linear infinite;
        background: -webkit-gradient(linear, left top, right top, color-stop(46%, rgba(255, 255, 255, 0)), color-stop(50%, rgba(255, 255, 255, 0.35)), color-stop(54%, rgba(255, 255, 255, 0))) 50% 50%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0) 46%, rgba(255, 255, 255, 0.35) 50%, rgba(255, 255, 255, 0) 54%) 50% 50%;
    }
    @-webkit-keyframes phAnimation {
      0% {
        -webkit-transform: translate3d(-30%, 0, 0);
                transform: translate3d(-30%, 0, 0); }
      100% {
        -webkit-transform: translate3d(30%, 0, 0);
                transform: translate3d(30%, 0, 0); } }

    @keyframes phAnimation {
      0% {
        -webkit-transform: translate3d(-30%, 0, 0);
                transform: translate3d(-30%, 0, 0); }
      100% {
        -webkit-transform: translate3d(30%, 0, 0);
                transform: translate3d(30%, 0, 0); } }
</style>