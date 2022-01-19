<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class ElementType extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */

	 public function beforeSave($options = array()) {

		$user_id = CakeSession::read("Auth.User.id");

		if (isset($this->data[$this->alias]["id"]) && !empty($this->data[$this->alias]["id"])) {

			// Save UserProject each time when a project is updated
			// This will help to set last updated project on top while getting summary page
			// $user_project = new UserProject();
			$project_id = $this->data[$this->alias]["id"];

/* 			$user_projects = $user_project->find("all", [
				'conditions' => [
					'UserProject.project_id' => $project_id,
					'UserProject.user_id' => $user_id,
				],
				'fields' => [
					'UserProject.id',
					'UserProject.project_id',
				],
			]);

			if (isset($user_projects) && !empty($user_projects)) {
				$data = null;
				$user_project_ids = Set::extract($user_projects, '/UserProject/id');
				foreach ($user_project_ids as $key => $val) {
					$data[] = array('UserProject' => array('id' => $val, 'modified' => date('Y-m-d h:i:s')));
				}
				if ($user_project->saveMany($data, array('deep' => true))) {

				}
			} */
		}
		foreach ($this->data[$this->alias] as $k => $v) {
			//$c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
			//$c = preg_replace('@<script>.*?@siu', '', $c);
			if(is_string($v)){
			$c = htmlspecialchars( $v);
			$this->data[$this->alias][$k] = $c;
			}

		}

		//$this->data = Sanitize::clean($this->data, array('encode' => true));
		//pr($this->data); die;
		return true;
	}

}
