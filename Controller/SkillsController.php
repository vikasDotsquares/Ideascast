<?php
/**
 * Skills Controller
 *
 * This file will render views from views/Currencies/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Task
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller/Component','Auth','Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
/**
 * Skill Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */

class SkillsController  extends AppController {

	public $name = 'Skills';	
	public $uses = array('Skill','UserSkill');
	
	/**
	* check login for admin and frontend user
	* allow and deny user
	*/
	//public $components = array('Email');
	
	/**
	* Helpers
	*
	* @var array
	*/
	public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Common');	
	
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
        
    /**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @return void
	 */	
	public function index($search = null){
		
		$this->layout = 'inner';
        $data['title_for_layout'] = __('Skill', true);
        $data['page_heading'] = __('Skills', true);
        //$data['page_subheading'] = __('Skills list, available to all Domains', true);
        $data['page_subheading'] = __('Skills list', true);
		
		if ( $this->Session->read('Auth.User.role_id') == 3  ) {
							
			$this->redirect(array('controller' => 'organisations', 'action' => 'dashboard'));
		}
		
		if ( $this->Session->read('Auth.User.role_id') == 2  ) {
							
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		
		
        $orConditions = array();
        $andConditions = array();
        $finalConditions = array();

        $in = 0;
        $per_page_show = $this->Session->read('skill.per_page_show');
        if (empty($per_page_show)) {
            //$per_page_show = ADMIN_PAGING;
            $per_page_show = 50;
        }
		
		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {
							
			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}
		
		if( ($this->request->is('post') || $this->request->is('put')) ) {
			if (isset($this->data['Skill']['keyword'])) {
				$keyword = trim($this->data['Skill']['keyword']);
			} else {
				$keyword = $this->Session->read('Skill.keyword');
			}
			$ascorder = 'asc';
			$pages = '';
			
			if( isset($this->params['named']['page']) && !empty($this->params['named']['page']) ){
				$pages = $this->params['named']['page'];
			} else if ( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) ){
				$ascorder = $this->params['named']['sortorder'];
			} else {
				$pages = '';
				$ascorder = 'asc';
			}
			
			//pr($pages); die;
			/* $this->redirect(['action' => 'index','page'=>$this->params['named']['page'],'sortorder'=>$this->params['named']['sortorder'],'search' => $keyword]); */
			
			$this->redirect(['action' => 'index','page'=>$pages,'sort'=>'Skill.title','search' => $keyword,'direction'=>$ascorder]);
			
		}
		
		if (isset($this->params['named']['search']) && !empty($this->params['named']['search'])) {
			$keyword = trim($this->params['named']['search']);
		} else {
			$keyword = $this->Session->read('Skill.keyword');
		}
		
        if (isset($keyword)) {
            $this->Session->write('skill.keyword', $keyword);
            $keywords = explode(" ", $keyword);
			
            if ( !empty($keyword) ) {
				
                $andConditions = array('AND' => array(
                    'Skill.title LIKE' => '%' . $keyword . '%',
                ));
				$in = 1;
            }
        }

        if (isset($this->data['Skill']['per_page_show']) && !empty($this->data['Skill']['per_page_show'])) {
            $per_page_show = $this->data['Skill']['per_page_show'];
        }

        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }
		
 
		
		if( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) && ( $this->params['named']['sortorder'] == 'desc' || $this->params['named']['sortorder'] == 'DESC' ) ){
			//$skillOrder = "Skill.title ".$this->params['named']['sortorder'];					
			$skillOrder = "Skill.sorder ".$this->params['named']['sortorder'];					
		} else {
			$skillOrder = 'Skill.title ASC';
		}
		
		if( isset($this->params['named']['direction']) && !empty($this->params['named']['direction'])  ){
			//$skillOrder = "Skill.title ".$this->params['named']['sortorder'];					
			$skillOrder = "Skill.title ".$this->params['named']['direction'];					
		}  
		 
		$count = $this->Skill->find('count', array('conditions' => $finalConditions));
		//$countF = $this->Skill->find('all', array('conditions' => $finalConditions,"order" => $skillOrder));
        $this->set('count', $count);
		
        $this->set('title_for_layout', __('All Skill', true));
        $this->Session->write('skill.per_page_show', $per_page_show);
        $this->Skill->recursive = 0;
		
		//pr($finalConditions); 
		// pr($skillOrder); 
		//pr($countF); 
		 
		 
        $paginator = array(
			// 'fields' => array(
			// 'Template.id', 'Template.title', 'Template.description', 'Template.layout_preview'
			// ),
			'conditions' => $finalConditions,
			'limit' =>$per_page_show,
			"order" => $skillOrder,
		);

		$this->paginate = $paginator;
		$this->set('currencies', $this->paginate('Skill'));

	 
		// $this->Paginator = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => $skillOrder);
	 
       // $this->set('currencies', $this->paginate('Skill'));
	   
		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Skills",
					'data-original-title' => "Skills",
				],
			],
		];		
		$this->set('crumb',$viewData['crumb']);
        $this->set('in', $in);
		$this->set('page_heading', $data['page_heading']);
		$this->set('page_subheading', $data['page_subheading']);
	}

	/**
     * admin_add method
     *
     * @return void
     */
    public function skill_add() {
        $this->set('title_for_layout', __('Add Skill', true));
        if ($this->request->is('post') || $this->request->is('put')) { //pr($this->request->data); die;
            
			if ($this->Skill->save($this->request->data,true)) {				
                $this->Session->setFlash(__('Skill has been saved successfully.'), 'success');
                die('success');
            }

        }
    }
	
	
    /**
     * Open Popup Modal Boxes method
		*
     * @return void
     */
    public function manage_skill( $id = null ) {
		
        if ($this->request->isAjax()) {
			
            $this->layout = 'ajax'; 
			$response = ['success'=> false, 'content'=>null];
			
			if( ($this->request->is('post') || $this->request->is('put')) ) {				
				
				$checkskill = $this->Skill->find('count', array('conditions'=>array('Skill.title'=>trim($this->request->data['Skill']['title'])) ) );	
				 
				if( !isset($this->request->data['Skill']['title']) || empty(trim($this->request->data['Skill']['title'])) ) 
				{	
					$response['content']['title'] = 'Skill title is required'; 					
					
				} else if( $checkskill > 0 ) {
					
					$response['content']['title'] = 'Skill title has already been taken'; 					
					
				} else {
				//if( !empty(trim($this->request->data['Skill']['title'])) ){
					
					$frmactionType = $this->request->data['Skill']['actionType']; 
					unset($this->request->data['Skill']['actionType']);
					
					$this->request->data['Skill']['title'] = strip_tags(trim($this->request->data['Skill']['title']));
					$this->Skill->set($this->request->data);
						 
					if ($this->Skill->save($this->request->data, false)) {
						$lastid = $this->Skill->getLastInsertId();
						if( $frmactionType == 'editAction' ){					
							$this->Session->setFlash(__('The Skill has been updated successfully.'), 'success');
							$response = ['success'=> true ];
						} else {						
							$this->request->data['Skill']['sorder'] = $lastid;
							$this->request->data['Skill']['id'] = $lastid;
							$this->Skill->save($this->request->data, false);
							$this->Session->setFlash(__('The Skill has been added successfully.'), 'success');
							$response = ['success'=> true];
						}	 
					}
					
				}
				echo json_encode($response);
				exit();
			
			
				/* $response = null;				
				if (isset($id) && !empty($id)) {
					$response['id'] = $id;					
					$this->request->data = $this->Skill->read(null, $id);
				}			
				$this->set('response', $response);  */
				//$this->render('/Skills/manage_skill');
			}
			else {			
				$this->request->data = $this->Skill->read(null, $id);           
			}
		}
	}

	
    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function skill_edit($id = null) {
		
        $this->set('title_for_layout', __('Edit Skill', true));
		
        if( ($this->request->is('post') || $this->request->is('put')) ) {
			
			if( !empty($this->request->data['Skill']['title'])  ){
			
				$frmactionType = $this->request->data['Skill']['actionType']; 
				unset($this->request->data['Skill']['actionType']);
				
				if ($this->Skill->save($this->request->data, false)) {
					$lastid = $this->Skill->getLastInsertId();
					if( $frmactionType == 'editAction' ){					
						$this->Session->setFlash(__('The Skill has been updated successfully.'), 'success');
					} else {						
						$this->request->data['Skill']['sorder'] = $lastid;
						$this->request->data['Skill']['id'] = $lastid;
						$this->Skill->save($this->request->data, false);
						$this->Session->setFlash(__('The Skill has been added successfully.'), 'success');
					}	
					die('success');
				}
				
			} else {
				$this->Session->setFlash(__('Please try again.'), 'error');					
			}			
        } else {			
			$this->request->data = $this->Skill->read(null, $id);           
        }
		
    }
	
	function skill_resetfilter(){
		
		$this->Session->write('skill.keyword', '');
		$this->redirect(array('action' => 'index'));
	}
	
	function skill_updatestatus(){
		
		
		if ($this->request->is('ajax')) {
            $this->autoRender = false;			
            $this->loadModel('Skill');
			
            $this->request->data['Skill'] = $this->request->data;
			//pr($this->request->data); die;
            if ($this->Skill->save($this->request->data, false )) {
				$this->Skill->updateAll( array('Skill.status' => $this->request->data['Skill']['status']),array('Skill.id' => $this->request->data['Skill']['id']));
                $this->Session->setFlash(__('Skill status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Skill status could not updated successfully.'), 'error');
            }			
        }		
		
	}
	
	function skill_delete(){
		
		if ($this->request->is('ajax')) {
			
            $this->autoRender = false;			
            $this->loadModel('Skill');
				
			if(isset($this->request->data['skill_id'])){
				$skill_id = $this->request->data['skill_id'];
				$condition = array('Skill.id' => $this->request->data['skill_id']);		
				$userSkillCondition = array('UserSkill.skill_id' => $this->request->data['skill_id']);		
				if ($this->Skill->deleteAll($condition,false)) {
					$this->UserSkill->deleteAll($userSkillCondition,false);
					if( $this->request->data['deltype'] == 'single' ){
						$this->Session->setFlash(__('Skill has been deleted successfully.'), 'success');
					} else {
						$this->Session->setFlash(__('Skills have been deleted successfully.'), 'success');
					}	
					die('success');
				} else {
					$this->Session->setFlash(__('Skill could not deleted successfully.'), 'error');
					die('error');
				}
			}	
			
        }		
	}
	
	function skill_swap(){
		
		if ($this->request->is('ajax')) {
			
            $this->autoRender = false;			
            $this->loadModel('Skill');
			
			
			if(isset($this->request->data['skill_id'])){
				
				$getMoveid = $this->Skill->find('first', array('conditions'=>array('id'=>$this->request->data['movetoid'])));
				
				//pr($this->request->data['movetoid']);
				//pr($getMoveid);
				
				$topMsg = 'top';
				$downMsg = 'down';
 
				
				 $getMoveid['Skill']['sorder'] = ($getMoveid['Skill']['sorder'])  ? $getMoveid['Skill']['sorder'] : 0 ;
				
				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "DOWN" ){
					
					if(isset($getMoveid['Skill']['sorder']) && ($getMoveid['Skill']['sorder'] >=0)){
					 
					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){
					
					$neighborid = $this->Skill->query('SELECT id,sorder FROM `skills` WHERE `sorder` > '.$getMoveid['Skill']['sorder'].' ORDER BY sorder ASC LIMIT 1');	
					
				 	//echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1';
					
					}else{
					$neighborid = $this->Skill->query('SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1');	
					
					}
					 
					
					//  echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1'.'<br>';
				 
					//pr($neighborid);  die;
/* 						echo $this->request->data['movetoid'];				 
						pr($neighborid[0]['skills']['sorder']);
					 
						echo "<br>";
						pr($neighborid[0]['skills']['id']); 
						echo	$getMoveid['Skill']['sorder']; */
					// die;
					
					$this->Skill->id = $this->request->data['movetoid'];					
					$this->Skill->saveField('sorder',$neighborid[0]['skills']['sorder']);
					
					$this->Skill->id = $neighborid[0]['skills']['id'];					
					$this->Skill->saveField('sorder',$getMoveid['Skill']['sorder']);				
					$this->Session->setFlash(__('Skill has been moved down the list successfully.'), 'success');					
					return "SUCCESS";
					}
					return "SUCCESS";
				}
 
				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "TOP" ){
					
					//$neighborid = $this->Skill->query('select * from skills where sorder = (select min(sorder) from skills where sorder > '.$getMoveid['Skill']['sorder'].')');
					//echo $this->request->data['sortorder'];
					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){
					
					$neighborid = $this->Skill->query('SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1');
					
				 	//echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1';
					
					}else{
						
					$neighborid = $this->Skill->query('SELECT id,sorder FROM `skills` WHERE `sorder` > '.$getMoveid['Skill']['sorder'].' ORDER BY sorder ASC LIMIT 1');	
					}
					
					 
/* 						echo $this->request->data['movetoid'];				 
						pr($neighborid[0]['skills']['sorder']);
					 
						echo "<br>";
						pr($neighborid[0]['skills']['id']); 
						echo	$getMoveid['Skill']['sorder'];	 */				
					// pr($neighborid); 
					// pr($getMoveid); 
 //die;
					
					$this->Skill->id = null;					
					$this->Skill->id = $this->request->data['movetoid'];					
					$this->Skill->saveField('sorder',$neighborid[0]['skills']['sorder']);
					$this->Skill->id = null;	
					$this->Skill->id = $neighborid[0]['skills']['id'];					
					$this->Skill->saveField('sorder',$getMoveid['Skill']['sorder']);				
					$this->Session->setFlash(__('Skill has been moved up in the list successfully.'), 'success');					
					return "SUCCESS";
					
				}					
				
			}	
			
        }
		
	}
	
	
	function skill_DragDrop(){
		
		if ($this->request->is('ajax')) {
			
            $this->autoRender = false;			
            $this->loadModel('Skill');
			
			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {
			
				$sortOrderss = $this->request->data['skill_order'];
				$skill_id = $this->request->data['skill_id'];
				 
				 
				/* if( isset($this->request->data['sorder'])  && !empty($this->request->data['sorder'])){
					sort($sortOrderss);				
				} else { 
				//	rsort($sortOrderss);	
				}	 */	
				  
				foreach($sortOrderss as $key => $value){
					e( $skill_id[$key].'<>'.$value);
					 
					$this->Skill->id = null;					
					$this->Skill->id = $skill_id[$key];					
					$this->Skill->saveField('sorder',$value);		
					
				}
			}
        }
		
	} 
	
}