<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses ( 'Helper', 'View' );

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package app.View.Helper
 */
class PhpwordHelper extends AppHelper {
	var $helpers = array (
			'Html',
			'Session',
			'Thumbnail',
			'Common',
			"Group" ,
                        "ViewModel"
	);


    public function skill_required( $project_id = null ) {
        $data = array();
        $user_ids = $this->project_people_ids($project_id);

        $userTopSkill = get_userSkills($user_ids,true);
        $UserSkillList = array();
        foreach ($userTopSkill as $skillID) {
            $skillsname = get_SkillName($skillID['UserSkill']['skill_id']);
            $UserSkillList[] = trim($skillsname['Skill']['title']);
        }
        sort($UserSkillList);
		// pr($UserSkillList);
		$UserSkillList = array_filter($UserSkillList, create_function('$value', 'return $value !== "";'));

        if(isset($UserSkillList) && !empty($UserSkillList)){
            foreach ($UserSkillList as $skillIDList) {
                $data[] = $skillIDList;
            }

        }else{
            $data[] = "N/A";
        }


        return $data;
    }

    public function skill_missing( $project_id = null ) {
        $data = array();
        $userSkillArr = $projectSkillArr = array();
        $userAllSkill = get_userSkills($this->project_people_ids($project_id),true);
        if(isset($pid) && !empty($pid)){
            $projectSkillArr =  $this->Common->get_skill_of_project($project_id);
            $projectSkillArr = Set::extract('/ProjectSkill/skill_id', $projectSkillArr);
        }
        if(isset($userAllSkill) && !empty($userAllSkill)){
            $userSkillArr = Set::extract('/UserSkill/skill_id', $userAllSkill);
        }

        $array_intersect = array_diff($projectSkillArr, $userSkillArr);

        if(empty($array_intersect))
        {
           $data[] = "N/A";
        }else
        {
            foreach ($array_intersect as $result) {
                $title = get_SkillName($result);
                $data[] = trim(strip_tags($title['Skill']['title']));
            }
        }
        return $data;
    }

    public function project_people( $project_id = null ) {

        //if ($this->request->is('get')) {

            $this->layout = 'ajax';

			$view = new View();
			$common = $view->loadHelper('Common');

			$data = null;

            if (isset($project_id) && !empty($project_id)) {

                $owner = $common->ProjectOwner( $project_id, $this->Session->read('Auth.User.id') );

                $data['participants_creator'] = [$owner['UserProject']['user_id']];
				$users['creator'] = array();
                if(isset($data['participants_creator']) && !empty($data['participants_creator'])){
                    foreach($data['participants_creator'] as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $users['creator'][] = $user_name;
                    }
                }


                $b = participants_owners( $project_id, $owner['UserProject']['user_id'], 1 );
                $data['participants_owners'] = (isset($b) && !empty($b)) ? array_filter($b) : null;
                $users['owners'] = array();
                if(isset($data['participants_owners']) && !empty($data['participants_owners'])){
                    foreach($data['participants_owners'] as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $users['owners'][] = $user_name;
                    }
                }


                $a = participants( $project_id, $owner['UserProject']['user_id'], 1 );
                $data['participants_sharer'] = (isset($a) && !empty($a)) ? array_filter($a) : null;
                $users['sharer'] = array();

                if(isset($data['participants_sharer']) && !empty($data['participants_sharer'])){

                    $data['participants_sharer'] = array_unique($data['participants_sharer']);

					foreach($data['participants_sharer'] as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $users['sharer'][] = $user_name;
                    }
                }


                $c = participants_group_owner( $project_id );
                $data['participantsGpOwner'] = (isset($c) && !empty($c)) ? array_filter($c) : null;
                $users['GpOwner'] = array();

                if(isset($data['participantsGpOwner']) && !empty($data['participantsGpOwner'])){
                    foreach($data['participantsGpOwner'] as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $users['GpOwner'][] = $user_name;
                    }
                }



                $d = participants_group_sharer( $project_id );
                $data['participantsGpSharer'] = (isset($d) && !empty($d)) ? array_filter($d) : null;
                $users['GpSharer'] = array();
                if(isset($data['participantsGpSharer']) && !empty($data['participantsGpSharer'])){
                    foreach($data['participantsGpSharer'] as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $users['GpSharer'][] = $user_name;
                    }
                }

            }
            return array_filter($users);
        //}

    }
    public function project_people_ids( $project_id = null ) {

        //if ($this->request->is('get')) {

            $this->layout = 'ajax';

			$view = new View();
			$common = $view->loadHelper('Common');

			$data = array();

            if (isset($project_id) && !empty($project_id)) {

                $owner = $common->ProjectOwner( $project_id, $this->Session->read('Auth.User.id') );

                $a = [$owner['UserProject']['user_id']];
                if(isset($a) && !empty($a)){
                    $data = array_merge($data,$a);
                }


                $b = participants_owners( $project_id, $owner['UserProject']['user_id'], 1 );
                if(isset($b) && !empty($b)){
                    $data = array_merge($data,$b);
                }


                $c = participants( $project_id, $owner['UserProject']['user_id'], 1 );
                if(isset($c) && !empty($c)){
                    $data = array_merge($data,$c);
                }


                $d = participants_group_owner( $project_id );
                if(isset($d) && !empty($d)){
                    $data = array_merge($data,$d);
                }


                $e = participants_group_sharer( $project_id );
                if(isset($e) && !empty($e)){
                    $data = array_merge($data,$e);
                }



            }
            return array_filter($data);

        //}

    }
    public function wsp_people( $projectwsp_id = null,$project_id = null) {

       // if ($this->request->is('get')) {

            $this->layout = 'ajax';

            $view = new View();
            $group = $view->loadHelper('Group');
            $common = $view->loadHelper('Common');
            $users = $user_names = array();
            $data = null;

            if (isset($projectwsp_id) && !empty($projectwsp_id)) {

                $owner = $common->ProjectOwner( $project_id, $this->Session->read('Auth.User.id') );



                $data['participants'] = wsp_participants( $project_id,$projectwsp_id, $owner['UserProject']['user_id'] );

                $data['participants_owners'] = participants_owners( $project_id, $owner['UserProject']['user_id'], 1);

                $data['participantsGpOwner'] = participants_group_owner( $project_id );

                $data['participantsGpSharer'] = wsp_grps_sharer( $project_id ,$projectwsp_id);

                $a = $data['participants'] ;
                if(isset($a) && !empty($a)){
                    $users = array_merge($users,$a);
                }
                $b = $data['participants_owners'] ;
                if(isset($b) && !empty($b)){
                    $users = array_merge($users,$b);
                }
                $c = $data['participantsGpOwner'] ;
                if(isset($c) && !empty($c)){
                    $users = array_merge($users,$c);
                }
                $d = $data['participantsGpSharer'] ;
				//echo $project_id."  ".$projectwsp_id;

                if(isset($d) && !empty($d)){
                    $users = array_merge($users,$d);
                }
				if(isset($owner['UserProject']['user_id']) && !empty($owner['UserProject']['user_id'])){
					$e['creator'] = $owner['UserProject']['user_id'];
					$users = array_merge($users,$e);
				}

                if(isset($users) && !empty($users)){
                    foreach(array_filter($users) as $user_id){
                        $user_data = $this->ViewModel->get_user_data($user_id);
                        $user_name = $user_data['UserDetail']['first_name'] . ' ' . $user_data['UserDetail']['last_name'];
                        $user_names[] = $user_name;
                    }
                }


            }

            return array_unique($user_names);
        //}

    }


    public function get_color_code( $color_class = null ) {

        $color_class = trim($color_class);

        //if ($this->request->is('get')) {
            $color_code = 'b0b4bc';
            if(isset($color_class) && $color_class == 'bg-red'){
                $color_code = 'dd4b39';
            }else if(isset($color_class) && $color_class == 'bg-blue'){
                $color_code = '0073b7';
            }else if(isset($color_class) && $color_class == 'bg-maroon'){
                $color_code = 'd81b60';
            }else if(isset($color_class) && $color_class == 'bg-aqua'){
                $color_code = '00c0ef';
            }else if(isset($color_class) && $color_class == 'bg-yellow'){
                $color_code = 'f39c12';
            }else if(isset($color_class) && $color_class == 'bg-green'){
                $color_code = '67a028';
            }else if(isset($color_class) && $color_class == 'bg-teal'){
                $color_code = '39cccc';
            }else if(isset($color_class) && $color_class == 'bg-purple'){
                $color_code = '605ca8';
            }else if(isset($color_class) && $color_class == 'bg-navy'){
                $color_code = '001f3f';
            }else if(isset($color_class) && $color_class == 'panel-blue'){
                $color_code = '0073b7';
            }else if(isset($color_class) && $color_class == 'panel-maroon'){
                $color_code = 'd81b60';
            }else if(isset($color_class) && $color_class == 'panel-aqua'){
                $color_code = '00c0ef';
            }else if(isset($color_class) && $color_class == 'panel-yellow'){
                $color_code = 'f39c12';
            }else if(isset($color_class) && $color_class == 'panel-green'){
                $color_code = '67a028';
            }else if(isset($color_class) && $color_class == 'panel-teal'){
                $color_code = '39cccc';
            }else if(isset($color_class) && $color_class == 'panel-purple'){
                $color_code = '605ca8';
            }else if(isset($color_class) && $color_class == 'panel-navy'){
                $color_code = '001f3f';
            }else if(isset($color_class) && $color_class == 'panel-red'){
                $color_code = 'ba3223';
            }

            else if(isset($color_class) && $color_class == 'panel-color-maroon'){
                $color_code = '8a0000';
            }
            else if(isset($color_class) && $color_class == 'panel-color-red'){
                $color_code = 'ca0000';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightred'){
                $color_code = 'fa0000';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkorange'){
                $color_code = '843c0c';
            }
            else if(isset($color_class) && $color_class == 'panel-color-orange'){
                $color_code = 'c55a11';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightorange'){
                $color_code = 'ee8640';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkyellow'){
                $color_code = '7f6000';
            }
            else if(isset($color_class) && $color_class == 'panel-color-yellow'){
                $color_code = 'c89800';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightyellow'){
                $color_code = 'ffc000';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkgreen'){
                $color_code = '385723';
            }
            else if(isset($color_class) && $color_class == 'panel-color-green'){
                $color_code = '548235';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightgreen'){
                $color_code = '77b64c';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkteal'){
                $color_code = '1b6d6b';
            }
            else if(isset($color_class) && $color_class == 'panel-color-teal'){
                $color_code = '29a3a0';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightteal'){
                $color_code = '3cd0cc';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkaqua'){
                $color_code = '1f4e79';
            }
            else if(isset($color_class) && $color_class == 'panel-color-aqua'){
                $color_code = '2e75b6';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightaqua'){
                $color_code = '74a9da';
            }
            else if(isset($color_class) && $color_class == 'panel-color-navy'){
                $color_code = '000080';
            }
            else if(isset($color_class) && $color_class == 'panel-color-blue'){
                $color_code = '0000f0';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightblue'){
                $color_code = '6363ff';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkpurple'){
                $color_code = '522375';
            }
            else if(isset($color_class) && $color_class == 'panel-color-purple'){
                $color_code = '7b35af';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightpurple'){
                $color_code = 'af7ad6';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkmagenta'){
                $color_code = '7d0552';
            }
            else if(isset($color_class) && $color_class == 'panel-color-magenta'){
                $color_code = 'bc087c';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightmagenta'){
                $color_code = 'f72bae';
            }
            else if(isset($color_class) && $color_class == 'panel-color-darkgray'){
                $color_code = '3b3838';
            }
            else if(isset($color_class) && $color_class == 'panel-color-gray'){
                $color_code = '7f7f7f';
            }
            else if(isset($color_class) && $color_class == 'panel-color-lightgray'){
                $color_code = 'b5b5b5';
            }
             return $color_code;

        //}

    }


}
