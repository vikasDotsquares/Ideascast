<div class="workspace-sec not-specified">
  <div class="hading-box-four-sec"><strong class="tab-titel-ws">NON</strong><small class="hidden-md">Not Specified</small><label class="con"></label>
		<span class="workspace-arrow-right"><span class="btn btn-default sorted tipText" title="Sort">AZ</span>
			<span class="workspace-arrow-but">
            <a href="javascript:;" class="sortOrderStartFirst " data-order="startfirst"  ><i data-original-title="Start first" class="fa fa-chevron-circle-up tipText" aria-hidden="true"></i></a>
			<a href="javascript:;" class="sortOrderEndFirst " data-order="endfirst">

			<i class="fa fa-chevron-circle-down tipText" aria-hidden="true"  data-original-title="End first"></i>
			</a>
            </span>
		</span>
	</div>
  <div class="workspace-contant-sec">
	<ul>
	<?php $b=false;
		if( isset($workspace_area) && !empty($workspace_area) ) {
			$a=0;
			foreach($workspace_area as $index => $element_data ) {
			$b=true;


				//pr($element_data); die;

				$element = $element_data['element'];
				$element_id = $element['id'];

				$area_id = $element['area_id'];
				$area_detail = $this->ViewModel->getAreaDetail( $area_id );

				if( isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0 ) {
					$b=false;
				} else {
					$a++;
					$workspaceDetails = getByDbId('Workspace', $area_detail['workspace_id'], $fields = null );

					echo $this->element('../Boards/partial/status_element_lists', array('element' => $element,'project_id'=>$project_id,'workspaceDetails'=>$workspaceDetails,'element_id'=>$element_id, 'areaDeatail'=>$area_detail));

				}
			}

				if($a == 0   ){
					echo 	'<li>
								<div class="user-contant-area" style="border-right-width:1px;">None</div>
							</li>';
				}

		} else { ?>
	<li>
		<div class="user-contant-area" style="border-right-width:1px;">None</div>
	</li>
	<?php } ?>
	</ul>
  </div>
</div>
<div class="workspace-sec not-started">
<div class="hading-box-four-sec"><strong class="tab-titel-ws">PND</strong><small class="hidden-md">Not Started</small><label class="con"></label>

	<span class="workspace-arrow-right"><span class="btn btn-default sorted tipText" title="Sort">AZ</span>
		<span class="workspace-arrow-but">	<a href="javascript:;" class="sortOrderStartFirst " data-order="startfirst"  ><i class="fa fa-chevron-circle-up tipText" data-original-title="Start first" aria-hidden="true"></i></a>


			<a href="javascript:;" class="sortOrderEndFirst " data-order="endfirst">

			<i class="fa fa-chevron-circle-down tipText"  data-original-title="End first" aria-hidden="true"></i></a>
            </span>
		</span>
</div>
  <div class="workspace-contant-sec">
	<ul>

		  <?php
			if( isset($workspace_area) && !empty($workspace_area) ) {

				$b=0;
				foreach($workspace_area as $index => $element_data ) {

					$element = $element_data['element'];
					$element_id = $element['id'];
					$area_id = $element['area_id'];
					$area_detail = $this->ViewModel->getAreaDetail( $area_id );

					if( ((isset( $element['start_date'] ) && !empty( $element['start_date'] )) && date( 'Y-m-d', strtotime( $element['start_date'] ) ) > date( 'Y-m-d' )  )  && ($element['sign_off'] != 1) && (isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0) ) {
					$b++;

					$workspaceDetails = getByDbId('Workspace', $area_detail['workspace_id'], $fields = null );

						echo $this->element('../Boards/partial/status_element_lists', array('element' => $element,'project_id'=>$project_id,'workspaceDetails'=>$workspaceDetails,'element_id'=>$element_id,'areaDeatail'=>$area_detail));

					}
				}

				if($b == 0 ){
					echo 	'<li>
								<div class="user-contant-area" style="border-right-width:1px;">None</div>
							</li>';
				}

			} else { ?>
		<li>
			<div class="user-contant-area" style="border-right-width:1px;">None</div>
		</li>
		<?php } ?>

	</ul>
  </div>
</div>
<div class="workspace-sec progressing">
    <div class="hading-box-four-sec"><strong class="tab-titel-ws">PRG</strong><small class="hidden-md">Progressing</small><label class="con"></label>
		<span class="workspace-arrow-right"><span class="btn btn-default sorted tipText" title="Sort">AZ</span>
			<span class="workspace-arrow-but"><a href="javascript:;" class="sortOrderStartFirst " data-order="startfirst"  ><i class="fa fa-chevron-circle-up tipText" data-original-title="Start first" aria-hidden="true"></i></a>
			<a href="javascript:;" class="sortOrderEndFirst" data-order="endfirst" data-original-title="End first"><i class="fa fa-chevron-circle-down tipText" data-original-title="End first" aria-hidden="true"></i></a>
            </span>
		</span>
    </div>
  <div class="workspace-contant-sec">
	<ul>
	  <?php
			if( isset($workspace_area) && !empty($workspace_area) ) {
				$c=0;
				foreach($workspace_area as $index => $element_data ) {

					$element = $element_data['element'];
					$element_id = $element['id'];
					$area_id = $element['area_id'];
					$area_detail = $this->ViewModel->getAreaDetail( $area_id );

					if( (((isset( $element['end_date'] ) && !empty( $element['end_date'] )) && (isset( $element['start_date'] ) && !empty( $element['start_date'] ))) && (date( 'Y-m-d', strtotime( $element['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) >= date( 'Y-m-d' ) )  && $element['sign_off'] != 1 && (isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0) ) {

						$c++;

						$workspaceDetails = getByDbId('Workspace', $area_detail['workspace_id'], $fields = null );

						echo $this->element('../Boards/partial/status_element_lists', array('element' => $element,'project_id'=>$project_id,'workspaceDetails'=>$workspaceDetails,'element_id'=>$element_id,'areaDeatail'=>$area_detail));

					}
				}

				if($c == 0 ){
					echo 	'<li>
								<div class="user-contant-area" style="border-right-width:1px;">None</div>
							</li>';
				}

		} else { ?>
		<li>
			<div class="user-contant-area" style="border-right-width:1px;">None </div>
		</li>
		<?php } ?>
	</ul>
  </div>
</div>

<div class="workspace-sec overdue">
	<div class="hading-box-four-sec"><strong class="tab-titel-ws">OVD</strong><small class="hidden-md">Overdue</small><label class="con"></label>
		<span class="workspace-arrow-right"><span class="btn btn-default sorted tipText" title="Sort">AZ</span>
		<span class="workspace-arrow-but">	<a href="javascript:;" class="sortOrderStartFirst " data-order="startfirst" ><i class="fa fa-chevron-circle-up tipText" data-original-title="Start first"  aria-hidden="true"></i></a>
			<a href="javascript:;" class="sortOrderEndFirst " data-order="endfirst"><i class="fa fa-chevron-circle-down tipText"  aria-hidden="true"  data-original-title="End first"></i></a></span>
		</span>
	</div>
  <div class="workspace-contant-sec">
	<ul>
	  <?php
			if( isset($workspace_area) && !empty($workspace_area) ) {
				$e=0;
				foreach($workspace_area as $index => $element_data ) {

					$element = $element_data['element'];
					$element_id = $element['id'];
					$area_id = $element['area_id'];
					$area_detail = $this->ViewModel->getAreaDetail( $area_id );

					if( ( (isset( $element['end_date'] ) && !empty( $element['end_date'] )) && date( 'Y-m-d', strtotime( $element['end_date'] ) ) < date( 'Y-m-d' ) )  && $element['sign_off'] != 1 && (isset( $element['date_constraints'] ) && !empty( $element['date_constraints'] ) && $element['date_constraints'] > 0) ) {

					$e++;

						$workspaceDetails = getByDbId('Workspace', $area_detail['workspace_id'], $fields = null );

						echo $this->element('../Boards/partial/status_element_lists', array('element' => $element,'project_id'=>$project_id,'workspaceDetails'=>$workspaceDetails,'element_id'=>$element_id,'areaDeatail'=>$area_detail));
					}
				}

				if($e == 0 ){
					echo 	'<li>
								<div class="user-contant-area" style="border-right-width:1px;">None</div>
							</li>';
				}
		} else { ?>
		<li>
			<div class="user-contant-area" style="border-right-width:1px;">None</div>
		</li>
		<?php } ?>
	</ul>
  </div>
</div>
<div class="workspace-sec completed">
	<div class="hading-box-four-sec"><strong class="tab-titel-ws">CMP</strong><small class="hidden-md">Completed</small><label class="con"></label>
		<span class="workspace-arrow-right"><span class="btn btn-default sorted tipText" title="Sort">AZ</span>
		<span class="workspace-arrow-but">	<a href="javascript:;" class="sortOrderStartFirst " data-order="startfirst" ><i class="fa fa-chevron-circle-up tipText" data-original-title="Start first"  aria-hidden="true"></i></a>
			<a href="javascript:;" class="sortOrderEndFirst " data-order="endfirst"><i class="fa fa-chevron-circle-down tipText" aria-hidden="true"  data-original-title="End first"></i></a></span>
		</span>
	</div>
  <div class="workspace-contant-sec">
	<ul>
	  <?php
			if( isset($workspace_area) && !empty($workspace_area) ) {
				$d=0;
				foreach($workspace_area as $index => $element_data ) {

					$element = $element_data['element'];
					$element_id = $element['id'];
					$area_id = $element['area_id'];
					$area_detail = $this->ViewModel->getAreaDetail( $area_id );

					if( isset( $element['sign_off'] ) && !empty( $element['sign_off'] ) ) {
						$d++;
						$workspaceDetails = getByDbId('Workspace', $area_detail['workspace_id'], $fields = null );

						echo $this->element('../Boards/partial/status_element_lists', array('element' => $element,'project_id'=>$project_id,'workspaceDetails'=>$workspaceDetails,'element_id'=>$element_id,'areaDeatail'=>$area_detail));

					}
				}

				if($d == 0 ){
					echo 	'<li>
								<div class="user-contant-area" style="border-right-width:1px;">None</div>
							</li>';
				}

			} else { ?>
		<li>
			<div class="user-contant-area" style="border-right-width:1px;">None</div>
		</li>
		<?php } ?>
	</ul>
  </div>
</div>


<script type="text/javascript">

$(function(){

	/* $('.users_popovers,.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });  */
})
</script>