

	<div class="panel-heading program-search-heading" style="position: relative;">
		  <h3 class="panel-title">PORTFOLIO: PROGRAMS (<?php echo (isset($program_data) && !empty($program_data)) ? count($program_data) : 0; ?>)</h3>
		  <?php if (isset($program_data) && !empty($program_data)){ ?>
		  <div class="program-search">
				<div class="input-group">
					<input id="programsearch" name="programsearch" type="text" class="form-control" placeholder="Search for...">
					<span class="input-group-btn">
						<button class="btn btn-danger search_clear" style="display:none;" type="button"><i class="fa fa-times"></i></button>
						<button class="btn btn-success search_submit" type="button"><i class="fa fa-search"></i></button>
					</span>
				</div>
			</div>
			<a href="#" id="" class="btn btn-primary btn-xs toggle-accordion tipText" title="Expand All" accordion-id="#accordion">
			<i class="fa"></i>
		  </a>
	</div>


      <?php } ?>
    </div>
    <div class="panel-body">
    <?php if(isset($program_data) && !empty($program_data)) { ?>
    <div class="panel-group panel-custom SearchT" id="accordion">
      <?php foreach ($program_data as $prm_key => $prm_value) {
        $program = $prm_value['Program'];
        // find all projects under this program
        $program_projects = program_projects($program['id']);
        // pr($program_projects);
        ?>
      <div class="panel panel-default SearchTerm">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a class="accordion-anchor">
                <span class="program-title"><?php echo $program['program_name']; ?> (<span><?php echo (isset($program_projects) && !empty($program_projects)) ? count($program_projects) : 0; ?></span>)</span>
                <div class="right-options">
                  <div class="project-multi-select">
                    <select class="project-select"></select>
                  </div><!--
                  <select class="project-select">
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option value="1">1</option>
                  </select> -->
                  <span class="btn btn-xs btn-white" >
                      <i class="bar-chart"></i>
                  </span>
                  <span class="show-hide-program btn btn-xs btn-white tipText" title="Show Project Cards" data-toggle="collapse"  data-parent="#accordion" href="#collapse<?php echo $program['id']; ?>" aria-expanded="false">
                      <i class="fa"></i>
                  </span>
                </div>
            </a>
          </h4>
        </div>
        <div id="collapse<?php echo $program['id']; ?>" class="panel-collapse collapse">
          <div class="panel-body scroll-horizontal">
            <div class="inner-horizontal" style="display: inline-block;">
              <?php
              $overdues = [];
              if( isset($program_projects) && !empty($program_projects) ) {
                $project_details = null;
                  foreach( $program_projects as $pkey => $pvalue ) {
                //
                    $key = $pvalue['ProjectProgram']['project_id'];
                    $upid = project_upid($key);
                    $project_details[$key] = project_primary_id($upid, true);
                    $project_details[$key]['rag_percent'] = $this->Common->getRAG($key, true)['rag_color'];
                  }
                  // pr($project_details);
                  $project_details = array_sort($project_details, 'rag_percent');

                  $rag_status_1 = $rag_status_2 = $rag_status_3 = null;

                  $rag_status_1 = arraySearch($project_details, 'rag_percent', 1);
                  $rag_status_2 = arraySearch($project_details, 'rag_percent', 2);
                  $rag_status_3 = arraySearch($project_details, 'rag_percent', 3);

                  if( isset($rag_status_1) && !empty($rag_status_1) ) {
                    $rag_status_1 = Set::extract($rag_status_1, '{n}.id');
                    echo $this->element('../Dashboards/partials/program_center/project_cards_sorting', array('projects' => $rag_status_1 ));
                  }

                  if( isset($rag_status_2) && !empty($rag_status_2) ) {
                    $rag_status_2 = Set::extract($rag_status_2, '{n}.id');
                    echo $this->element('../Dashboards/partials/program_center/project_cards_sorting', array('projects' => $rag_status_2 ));
                  }

                  if( isset($rag_status_3) && !empty($rag_status_3) ) {
                    $rag_status_3 = Set::extract($rag_status_3, '{n}.id');
                    echo $this->element('../Dashboards/partials/program_center/project_cards_sorting', array('projects' => $rag_status_3 ));
                  } ?>
              <?php }else { ?>
                <div style="" class="no-project">No Project Found</div>
              <?php }
               ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <?php }
  else{ ?>
  <div class="no-project" >No Program Found</div>
  <?php } ?>
</div>
<script type="text/javascript">
  $(function(){
    $.multi_select = $('.project-multi-select').multi_select({
        selectColor: 'aqua',
        selectSize: 'small',
        selectText: 'Select Projects',
        data: $js_config.projects,
        duration: 300,
        easing: 'slide',
        listMaxHeight: 300,
        selectedCount: 1,
        countText: 'Selected Projects',
        selectedIndexes: [parseInt($js_config.project_id)],
    });
    // set scrolling
    setTimeout(function(){
        $(".inner-horizontal").each(function() {
            var all = 0;
            var w = 0;
            var $inner = $(this);
            $(this).find('div.project-block').each(function(index, el) {
                var w = $(this).outerWidth(true);
                all = all + w;
            });
            $inner.css('min-width', all);
        })

    }, 1000)
    $('.panel-collapse').on('hidden.bs.collapse', function (e) {
        // $(this).parents('.panel:first').find('.show-hide-program').attr('title', '').attr('data-original-title', 'Show Project Cards');
        $('.accordion-anchor').each(function(index, el) {
            if ($(this).attr('aria-expanded') == "false") {
                $(this).find('.show-hide-program').attr('title', '').attr('data-original-title', 'Show Project Cards');
            }
            else{
                $(this).find('.show-hide-program').attr('title', '').attr('data-original-title', 'Close Project Cards');
            }
        });
    });
    $('.panel-collapse').on('shown.bs.collapse', function (e) {
        // $(this).parents('.panel:first').find('.show-hide-program').attr('title', '').attr('data-original-title', 'Close Project Cards');
        $('.accordion-anchor').each(function(index, el) {
            if ($(this).attr('aria-expanded') == "false") {
                $(this).find('.show-hide-program').attr('title', '').attr('data-original-title', 'Show Project Cards');
            }
            else{
                $(this).find('.show-hide-program').attr('title', '').attr('data-original-title', 'Close Project Cards');
            }
        });
    });
  })
</script> 