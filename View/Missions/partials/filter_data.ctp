<?php 
$user_id = $this->Session->read('Auth.User.id');
$user_setting = mission_settings($user_id);
$bucket_setting = mission_settings($user_id, null, ['links','notes','documents','mindmaps','feedbacks','decisions','votes']);

if(isset($data) && !empty($data)) {

	$workspace_id = $data['workspace_id'];
	$elements = [];
	if( ( !isset($data['area_id']) || empty($data['area_id']) ) && ( !isset($data['element_id']) || empty($data['element_id']) ) ) {
		$elements = workspace_elements($workspace_id);
		if(isset($elements) && !empty($elements)) {
			$elements = Set::extract($elements, '/Element/id');
		}
	}
	if( isset($data['area_id']) && !empty($data['area_id'])  && ( !isset($data['element_id']) || empty($data['element_id']) ) ) {
		$elements = area_element($data['area_id']);

		if(isset($elements) && !empty($elements)) {
			$elements = Set::extract($elements, '/Element/id');
		}
	}
	else if(isset($data['element_id']) && !empty($data['element_id'])) {
		$elements = $data['element_id'];
	}

	$people = (isset($data['people']) && !empty($data['people'])) ? $data['people'] : null;
	$elements = (isset($elements) && !empty($elements)) ? $elements : null;

	$links = $documents = $notes = $mindmaps = [];
	$area_ids = $decisions = $feedbacks = $votes = null;
	$decision_counts = $feedback_counts = $vote_counts = 0;

	$assets = workspace_element_assets($workspace_id, $elements, $people);

	$links = Set::extract($assets, '/ElementLink');
	$documents = Set::extract($assets, '/ElementDocument');
	$notes = Set::extract($assets, '/ElementNote');
	$mindmaps = Set::extract($assets, '/ElementMindmap');

	$decisions = _filter_decision_and_detail($workspace_id, $elements, $people);
	
	$feedback_data = _filter_feedbacks($workspace_id, $elements, $people);

	if( isset($feedback_data) && !empty($feedback_data) ) {
		$f = 0;
		foreach( $feedback_data as $k => $fb_data ) {
			if( isset($fb_data['feedback']) && !empty($fb_data['feedback']) ) {
				$feedback_counts++;
				$feedbacks[$f]['feedback'] = $fb_data['feedback']['Feedback'];
				if( isset($fb_data['feedback']['FeedbackResults']) && !empty($fb_data['feedback']['FeedbackResults']) ) {
					$feedbacks[$f]['feedback_detail'] = $fb_data['feedback']['FeedbackResults'];
				}
			}
			$f++;
		}
	}

	$vote_data = _filter_votes($workspace_id, $elements, $people);

	if( isset($vote_data) && !empty($vote_data) ) {
		$v = 0;
		foreach( $vote_data as $k => $data ) {



			if( isset($data['vote']) && !empty($data['vote']) ) {

				$vote_counts++;
				$votes[$v]['vote'] = $data['vote']['Vote'];

				if( isset($data['vote']['VoteResults']) && !empty($data['vote']['VoteResults']) ) {
					$votes[$v]['vote_detail'] = $data['vote']['VoteResults'];
				}
			}
			$v++;
		}

	}



	if( !isset($data['area_id']) || empty($data['area_id']) ) {
		$areas = $this->ViewModel->workspace_areas($workspace_id, false, true);
		if( isset($areas) && !empty($areas) ) {
			if(is_array($area_ids))
				$area_ids = array_merge($area_ids, array_values($areas));
			else
				$area_ids = array_values($areas);
		}
	}
	else {
		$area_ids = $data['area_id'];
	}

	$no_result = 'No data found';
	
	if( isset($bucket_setting) && !empty($bucket_setting) ) {

		foreach($bucket_setting as $key => $val) {
			// e($key);
			if( $key == 'links' ) {
				echo $this->element('../Missions/partials/buckets/link_bucket', ['sort_order' => $val['sort_order'], 'data' => $links, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'notes' ) {
				echo $this->element('../Missions/partials/buckets/note_bucket', ['sort_order' => $val['sort_order'], 'data' => $notes, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'documents' ) {
				echo $this->element('../Missions/partials/buckets/document_bucket', ['sort_order' => $val['sort_order'], 'data' => $documents, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'decisions' ) {
				echo $this->element('../Missions/partials/buckets/decision_bucket', ['sort_order' => $val['sort_order'], 'data' => $decisions, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'feedbacks' ) {
				echo $this->element('../Missions/partials/buckets/feedback_bucket', ['sort_order' => $val['sort_order'], 'data' => $feedbacks, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'votes' ) {
				echo $this->element('../Missions/partials/buckets/vote_bucket', ['sort_order' => $val['sort_order'], 'data' => $votes, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}
			else if( $key == 'mindmaps' ) {
				echo $this->element('../Missions/partials/buckets/mindmap_bucket', ['sort_order' => $val['sort_order'], 'data' => $mindmaps, 'workspace_id' => $workspace_id, 'el_count' => ( (isset($elements) && !empty($elements))? count($elements) : 0)]);
			}

		}
	}
}
?>
<script type="text/javascript" >
$(function(){
	/* $.bucket_sorting();

	setTimeout(function(){
		var numericallyOrderedDivs = $('.idea-bucket-inner').sort(function (a, b) {
			return $(a).data('order') > $(b).data('order');
		});
		$("#wsp_buckets").html(numericallyOrderedDivs);
	}, 100) */
	// $.animateSort("#wsp_buckets", ".idea-bucket-inner", "data-order");

	// $("#ajax_overlay").show()
	setTimeout(function(){
		$.bucket_sorting();
		// $( ".idea-bucket-inner" ).removeAttr('style');
		// $("#ajax_overlay").hide()

		// var numericallyOrderedDivs = $('.idea-bucket-inner').sort(function (a, b) {
			// return $(a).data('order') > $(b).data('order');
		// });
		// $("#wsp_buckets").html(numericallyOrderedDivs);
	}, 1200)

	var $active_element = $('.el').filter(function () {
		return $(this).data("highlight") == true;
	});

	if( $active_element.length > 0 ) {
		$('.add_asset').removeClass('not_selected')
	}

})

</script>

