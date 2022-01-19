<?php
/**
 * Component for working Group.

 */
class GroupComponent extends Component {

	var $components = array('Html', 'Session', 'Thumbnail');

	public function group_wsp_permission_edit($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		//echo $id."<br>".$common->get_up_id($pid,$uid)."<br>".$uid;

		//echo  $common->get_up_id($pid,$uid)."<br>".$pid ;

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.user_project_id' => project_upid($pid))));

		return isset($data['WorkspacePermission']['permit_edit']) ? $data['WorkspacePermission']['permit_edit'] : 0;
	}

	public function group_wsp_permission_delete($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid))));
		return isset($data['WorkspacePermission']['permit_delete']) ? $data['WorkspacePermission']['permit_delete'] : 0;
	}

	public function group_wsp_permission_details($id, $pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('WorkspacePermission')->find('first', array('conditions' => array('WorkspacePermission.project_workspace_id' => $id, 'WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid))));

		return isset($data) ? $data : array();
	}

	public function group_permission_details($pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');

		$data = ClassRegistry::init('ProjectPermission')->find('first', array('conditions' => array('ProjectPermission.project_group_id' => $gid, 'ProjectPermission.user_project_id' => project_upid($pid)), 'recursive' => -1));

		return isset($data) ? $data : array();
	}

	public function ProjectGroupDetail($gid = null, $pid = null, $uid = null) {
		$data = null;
		if (isset($gid) && !empty($gid)) {

			$data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions' => array('ProjectGroup.id' => $gid)));
		} else {
			$data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions' => array('ProjectGroup.group_owner_id' => $uid, 'ProjectGroup.user_project_id' => $pid)));
		}
		return isset($data) ? $data : array();
	}

/* 	public function groupprojectOwner( $gid ,$pid){
$data = ClassRegistry::init('ProjectGroup')->find('first', array('conditions'=>array('ProjectGroup.user_project_id'=>$pid )));

return isset($data['ProjectGroup']['group_owner_id']) ? $data['ProjectGroup']['group_owner_id']  :  "N/A";
} */

	public function group_element_share_permission($element_id, $pid, $gid) {

		$data = ClassRegistry::init('ElementPermission')->find('first', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'ElementPermission.element_id' => $element_id)));

		return isset($data) ? $data : 0;
	}

	public function group_work_permission_details($pid, $gid) {

		$view = new View();
		$common = $view->loadHelper('Common');
		ClassRegistry::init('WorkspacePermission')->recursive = 2;
		$datas = ClassRegistry::init('WorkspacePermission')->find('all', array('conditions' => array('WorkspacePermission.project_group_id' => $gid, 'WorkspacePermission.user_project_id' => project_upid($pid), 'ProjectWorkspace.id !=' => '')));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['WorkspacePermission']['project_workspace_id'];
			}

		}

		return isset($data) ? $data : array();
	}

	public function group_element_permission_details($wid, $pid, $gid) {

		$datas = ClassRegistry::init('ElementPermission')->find('all', array('conditions' => array('ElementPermission.project_group_id' => $gid, 'ElementPermission.project_id' => $pid, 'ElementPermission.workspace_id' => $wid)));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['ElementPermission']['element_id'];
			}

		}

		return isset($data) ? $data : array();
	}

	public function group_users($gid) {

		$datas = ClassRegistry::init('GrpUser')->find('all', array('conditions' => array('GrpUser.project_group_id' => $gid)));

		if (isset($datas) && !empty($datas)) {

			foreach ($datas as $dat) {
				$data[] = $dat['GrpUser']['user_id'];
			}

		}

		return isset($data) ? $data : array();
	}

	public function GroupIDbyUserID($pid, $uid) {

		$datas = ClassRegistry::init('ProjectGroupUser')->find('first', array('conditions' => array('ProjectGroupUser.user_id' => $uid, 'ProjectGroupUser.user_project_id' => project_upid($pid), 'ProjectGroupUser.approved' => 1)));

		if (isset($datas) && !empty($datas)) {

			$data = $datas['ProjectGroupUser']['project_group_id'];

		}

		return isset($data) ? $data : array();
	}

	public function ProjectDateValidEnd($pid) {

		$datas = ClassRegistry::init('ProjectWorkspace')->find('list', array('conditions' => array('ProjectWorkspace.project_id' => $pid), 'fields' => array('ProjectWorkspace.workspace_id'), 'recursive' => -1));

		if (isset($datas) && !empty($datas)) {

			$datasEND = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $datas, 'Workspace.end_date !=' => ''), 'fields' => array('Workspace.end_date', 'Workspace.id'), 'order' => 'end_date desc', 'recursive' => -1));

			$datasStart = ClassRegistry::init('Workspace')->find('first', array('conditions' => array('Workspace.id' => $datas, 'Workspace.start_date !=' => ''), 'fields' => array('Workspace.start_date', 'Workspace.id'), 'order' => 'start_date asc', 'recursive' => -1));
			$dm = null;
			if ((isset($datasEND) && !empty($datasEND)) && (isset($datasStart) && !empty($datasStart))) {
				$dm['start_date'] = $datasStart['Workspace']['start_date'];
				$dm['end_date'] = $datasEND['Workspace']['end_date'];
			}

			return isset($dm) ? $dm : array();
		}
		return array();
	}

}