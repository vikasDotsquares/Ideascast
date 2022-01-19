
<?php
$program_projects = (isset($program_data[$program_id]['Projects']) && !empty($program_data[$program_id]['Projects'])) ? $program_data[$program_id]['Projects'] : null;


?>
<div class="inner-horizontal" style="display: inline-block;">
    <?php
    $overdues = [];
    if( isset($program_projects) && !empty($program_projects) ) {
        $project_details = null;
        foreach( $program_projects as $pkey => $value ) {
            $pvalue = $value;
            $key = $pvalue['project_id'];
            $upid = project_upid($key);
            $project_details[$key] = project_primary_id($upid, true);
            $project_details[$key]['rag_percent'] = $this->Common->getRAG($key, true)['rag_color'];
        }
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
            <div style="" class="no-data">No Project Found</div>
        <?php }
     ?>
</div>

<script type="text/javascript">
    $(function(){
        // set scrolling
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

    })
</script>
