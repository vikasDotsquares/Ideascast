<ul class="cd-tabs-navigation "  id="element_tabs">

    <li class=""><a class="act_links tipText" title="Link"	data-content="links"  >
            <i class="asset-all-icon re-LinkBlack"></i> <span class="hidden-lg hidden-md hidden-sm">Links</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'ElementLink'); ?>)
        </a>
    </li>

    <li class=""><a class="act_notes tipText" title="Note" data-content="notes" >
            <i class="asset-all-icon re-NoteBlack"></i><span class="hidden-lg hidden-md hidden-sm"> Notes</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'ElementNote'); ?>)
        </a>
    </li>

    <li class="">
        <a class="act_document tipText" title="Document" data-content="documents"   >
            <i class="asset-all-icon re-DocumentBlack"></i><span class=" hidden-lg hidden-md hidden-sm"> Documents</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'ElementDocument'); ?>)
        </a>
    </li>

    <li class="">
        <a class="act_mind_map tipText" title="Mind Map" data-content="mind_maps"  >
            <i class="asset-all-icon re-MindMapBlack"></i><span class=" hidden-lg hidden-md hidden-sm"> Mind Maps</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'ElementMindmap'); ?>)
        </a>
    </li>

    <?php
    $is_owner = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
    $original_owner = $this->Common->userprojectOwner($project_id, $this->Session->read('Auth.User.id'));

    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

    //$is_editaa = $this->Common->element_manage_editable($this->data['Element']['id'], $project_id, $this->Session->read('Auth.User.id'));
		$elementPermission = $this->Common->element_manage_permission($this->data['Element']['id'], $project_id, $this->Session->read('Auth.User.id'));

		$is_editaa = 0;
		$is_add_shares = 0;
		$is_edit_shares = 0;
		$is_read_shares = 0;
		$is_delete_shares = 0;
		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_edit']) ){
			$is_editaa = $elementPermission[0]['user_permissions']['permit_edit'];
		}
		if( isset($elementPermission) && !empty($elementPermission[0]['user_permissions']['permit_edit']) ){
			$is_edit_shares = $elementPermission[0]['user_permissions']['permit_edit'];
			$is_edit_share = $elementPermission[0]['user_permissions']['permit_edit'];
		}

	//pr($is_editaa);
    //if ((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($is_editaa) && $is_editaa > 0)) {
    if ((isset($is_editaa) && $is_editaa > 0)) {
        ?>

        <?php
        $disabled_links = '';
    } else {
        $disabled_links = 'disabled_links';
    }

    $element_decisions = _element_decisions($this->data['Element']['id'], 'decision');
    //pr($element_decisions['decision_short_term']);
	//$element_decisions = $this->ViewModel->getElementDecisionSts($this->data['Element']['id']);
    ?>
    <li class=""><a class="act_decision <?php echo $disabled_links; ?> tipText" title="Decision" data-content="decisions" >
            <i class="asset-all-icon re-DecisionBlack"></i> <span class="hidden-lg bhidden-md hidden-sm"> Decision</span> (<?php echo $element_decisions['decision_short_term']; ?>)
        </a>
    </li>

    <li class=""><a class="act_feedback <?php echo $disabled_links; ?> tipText" title="Feedback" data-content="feedbacks"  >
            <i class="asset-all-icon re-FeedbackBlack"></i><span class="hidden-lg hidden-md hidden-sm"> Feedback</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'Feedback'); ?>)
        </a>
    </li>

    <li class=""><a class="act_vote <?php echo $disabled_links; ?> tipText" title="Vote" data-content="votes"  >
            <i class="asset-all-icon re-VoteBlack"></i><span class="hidden-lg hidden-md hidden-sm"> Votes</span> (<?php echo $this->Common->countTotalElementParts($this->data['Element']['id'], 'Vote'); ?>)
        </a>
    </li>
</ul>