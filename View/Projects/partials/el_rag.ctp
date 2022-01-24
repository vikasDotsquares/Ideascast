<?php
       $RAG = $this->Common->getRAG($project_id);
        $parent_color = 'green-bg';
        $parent_Tip = 'Green';
        $parent_color =  ($RAG['rag_color'] == 'bg-red') ? 'red' : ( ( $RAG['rag_color'] == 'bg-yellow') ? 'yellow' : 'green-bg' );
        $parent_Tip =  ($RAG['rag_color'] == 'bg-red') ? 'Red' : ( ( $RAG['rag_color'] == 'bg-yellow') ? 'Amber' : 'Green' );


        $elements_overdue = $this->Permission->projectTaskCounters($project_id);
		$CMP = $NON = $PRG = $PND = $OVD  = $total_t =  0;
		if(isset($elements_overdue) && !empty($elements_overdue)){

			$CMP = (isset($elements_overdue['0']['0']['CMP']) && !empty($elements_overdue['0']['0']['CMP']) ) ? $elements_overdue['0']['0']['CMP'] : 0;
			$NON = (isset($elements_overdue['0']['0']['NON']) && !empty($elements_overdue['0']['0']['NON']) ) ? $elements_overdue['0']['0']['NON'] : 0;
			$PRG = (isset($elements_overdue['0']['0']['PRG']) && !empty($elements_overdue['0']['0']['PRG']) ) ? $elements_overdue['0']['0']['PRG'] : 0;
			$PND = (isset($elements_overdue['0']['0']['PND']) && !empty($elements_overdue['0']['0']['PND']) ) ? $elements_overdue['0']['0']['PND'] : 0;
			$OVD = (isset($elements_overdue['0']['0']['OVD']) && !empty($elements_overdue['0']['0']['OVD']) ) ? $elements_overdue['0']['0']['OVD'] : 0;
			$total_t = $CMP + $NON + $PRG + $PND + $OVD;


			$ovd_per = (!empty($total_t)) ? round(($OVD / $total_t) * 100) : 0;

		}

		$rag_exist = rag_exist($project_id);

		if( !empty($rag_exist)) {

		if($parent_color == 'green-bg'){
			$parent_Tip  = 'Below Overdue Tasks Thresholds';
		}else if($parent_color == 'yellow'){
			$parent_Tip  = $ovd_per.'% Overdue Tasks';
		}else if($parent_color == 'red'){
			$parent_Tip  = $ovd_per.'% Overdue Tasks';
		}

		}
?>

<div class="progress-col rag-col">
	<div class="progress-col-heading">
		<span class="prog-h"><a href="#" data-toggle="modal" data-target="#model_bx" data-remote="<?php echo SITEURL; ?>projects/add_annotate/<?php echo $project_id; ?>">Rag <i class="arrow-down"></i></a></span>
	</div>
	<div class="progress-col-cont">
		<div class="schedule-bar">
			<span class="annotation pull-right tipText annotation-black <?php echo $parent_color ;?> barTip" title="<?php echo $parent_Tip; ?>" data-original-title="Annotations"  data-toggle="modal" data-target="#model_bx" data-remote="<?php echo SITEURL; ?>projects/add_annotate/<?php echo $project_id; ?>" style="width:100%"><?php echo project_annotation_count($project_id); ?></span>
		</div>
		<div class="proginfotext">
			<?php
			if( !empty($rag_exist)) { ?>
				  Rules
			<?php }else { ?>
				 Manual
			<?php } ?>
		</div>

	</div>
</div>