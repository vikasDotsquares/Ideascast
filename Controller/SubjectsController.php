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

class SubjectsController  extends AppController {

	public $name = 'Subjects';
	public $uses = array('Subject', 'UserSubject', 'ProjectSubject');

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
        $data['title_for_layout'] = __('Subjects', true);
        $data['page_heading'] = __('Subjects', true);
        $data['page_subheading'] = __('Subjects list', true);


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
        $per_page_show = $this->Session->read('subjects.per_page_show');
        if (empty($per_page_show)) {
            //$per_page_show = ADMIN_PAGING;
			$per_page_show = 50;
        }

		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		if( ($this->request->is('post') || $this->request->is('put')) ) {
			if (isset($this->data['Subject']['keyword'])) {
				$keyword = trim($this->data['Subject']['keyword']);
			} else {
				$keyword = $this->Session->read('Subject.keyword');
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

			//$this->redirect(['action' => 'index','page'=>$pages,'sortorder'=>$ascorder,'search' => $keyword]);
			$this->redirect(['action' => 'index','page'=>$pages,'sort'=>'Subject.title','search' => $keyword,'direction'=>$ascorder]);

		}

		if (isset($this->params['named']['search']) && !empty($this->params['named']['search'])) {
			$keyword = trim($this->params['named']['search']);
			// pr($keyword);
		} else {
			$keyword = $this->Session->read('Subject.keyword');
		}

        if (isset($keyword)) {
            $this->Session->write('subjects.keyword', $keyword);
            $keywords = explode(" ", $keyword);

            if ( !empty($keyword) ) {

                $andConditions = array('AND' => array(
                    'Subject.title LIKE' => '%' . $keyword . '%',
                ));
				$in = 1;
            }
        }

        if (isset($this->data['Subject']['per_page_show']) && !empty($this->data['Subject']['per_page_show'])) {
            $per_page_show = $this->data['Subject']['per_page_show'];
        }

        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }



		if( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) && ( $this->params['named']['sortorder'] == 'desc' || $this->params['named']['sortorder'] == 'DESC' ) ){
			$subjectOrder = "Subject.sorder ".$this->params['named']['sortorder'];
		} else {
			$subjectOrder = 'Subject.title ASC';
		}

		if( isset($this->params['named']['direction']) && !empty($this->params['named']['direction'])  ){
			$subjectOrder = "Subject.title ".$this->params['named']['direction'];
		}

		$count = $this->Subject->find('count', array('conditions' => $finalConditions));
        $this->set('count', $count);

        $this->set('title_for_layout', __('All Subjects', true));
        $this->Session->write('subject.per_page_show', $per_page_show);
        $this->Subject->recursive = 0;



        $paginator = array(
			'conditions' => $finalConditions,
			'limit' =>$per_page_show,
			"order" => $subjectOrder,
		);

		$this->paginate = $paginator;
		$this->set('subjects', $this->paginate('Subject'));



		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Subjects",
					'data-original-title' => "Subjects",
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
    public function subject_add() {
        $this->set('title_for_layout', __('Add Subject', true));
        if ($this->request->is('post') || $this->request->is('put')) { //pr($this->request->data); die;

			if ($this->Subject->save($this->request->data,true)) {
                $this->Session->setFlash(__('Subject has been saved successfully.'), 'success');
                die('success');
            }

        }
    }


    /**
     * Open Popup Modal Boxes method
		*
     * @return void
     */
    public function manage_subject( $id = null ) {

        if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$response = ['success'=> false, 'content'=>null];

			if( ($this->request->is('post') || $this->request->is('put')) ) {

				$checkSubject = $this->Subject->find('count', array('conditions'=>array('Subject.title'=>trim($this->request->data['Subject']['title'])) ) );

				if( !isset($this->request->data['Subject']['title']) || empty(trim($this->request->data['Subject']['title'])) )
				{
					$response['content']['title'] = 'Subject title is required';

				} else if( $checkSubject > 0 ) {

					$response['content']['title'] = 'Subject title has already been taken';

				} else {
				//if( !empty(trim($this->request->data['Skill']['title'])) ){

					$frmactionType = $this->request->data['Subject']['actionType'];
					unset($this->request->data['Subject']['actionType']);

					$this->request->data['Subject']['title'] = strip_tags(trim($this->request->data['Subject']['title']));
					$this->Subject->set($this->request->data);

					if ($this->Subject->save($this->request->data, false)) {
						$lastid = $this->Subject->getLastInsertId();
						if( $frmactionType == 'editAction' ){
							$this->Session->setFlash(__('The Subject has been updated successfully.'), 'success');
							$response = ['success'=> true ];
						} else {
							$this->request->data['Subject']['sorder'] = $lastid;
							$this->request->data['Subject']['id'] = $lastid;
							$this->Subject->save($this->request->data, false);
							$this->Session->setFlash(__('The Subject has been added successfully.'), 'success');
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
				$this->request->data = $this->Subject->read(null, $id);
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
    public function subject_edit($id = null) {

        $this->set('title_for_layout', __('Edit Subject', true));

        if( ($this->request->is('post') || $this->request->is('put')) ) {

			if( !empty($this->request->data['Subject']['title'])  ){

				$frmactionType = $this->request->data['Subject']['actionType'];
				unset($this->request->data['Subject']['actionType']);

				if ($this->Subject->save($this->request->data, false)) {
					$lastid = $this->Subject->getLastInsertId();
					if( $frmactionType == 'editAction' ){
						$this->Session->setFlash(__('The Skill has been updated successfully.'), 'success');
					} else {
						$this->request->data['Subject']['sorder'] = $lastid;
						$this->request->data['Subject']['id'] = $lastid;
						$this->Subject->save($this->request->data, false);
						$this->Session->setFlash(__('The Subject has been added successfully.'), 'success');
					}
					die('success');
				}

			} else {
				$this->Session->setFlash(__('Please try again.'), 'error');
			}
        } else {
			$this->request->data = $this->Subject->read(null, $id);
        }

    }

	function subject_resetfilter(){

		$this->Session->write('subject.keyword', '');
		$this->redirect(array('action' => 'index'));
	}

	function subject_updatestatus(){


		if ($this->request->is('ajax')) {
            $this->autoRender = false;

            $this->request->data['Subject'] = $this->request->data;
			//pr($this->request->data); die;
            if ($this->Subject->save($this->request->data, false )) {
				$this->Subject->updateAll( array('Subject.status' => $this->request->data['Subject']['status']),array('Subject.id' => $this->request->data['Subject']['id']));
                $this->Session->setFlash(__('Subject status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Subject status could not updated successfully.'), 'error');
            }
        }

	}

	function subject_delete(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;
            $response = [
				'success' => false,
				'page' => 0
			];
			if(isset($this->request->data['subject_id'])){

				$subject_id = $this->request->data['subject_id'];
				$condition = array('Subject.id' => $this->request->data['subject_id']);
				$UserSubjectCondition = array('UserSubject.subject_id' => $this->request->data['subject_id']);

				$userPSCondition = array('ProjectSubject.subject_id' => $this->request->data['subject_id']);

				if ($this->Subject->deleteAll($condition,false)) {
					$this->UserSubject->deleteAll($UserSubjectCondition,false);
					$this->ProjectSubject->deleteAll($userPSCondition,false);
					if( $this->request->data['deltype'] == 'single' ){
						$this->Session->setFlash(__('Subject has been deleted successfully.'), 'success');
						$total = $this->Subject->find('count');
						$page = $total/ADMIN_PAGING;
						$response = [
							'success' => true,
							'page' => ceil($page)
						];
						return json_encode($response);
					} else {
						$this->Session->setFlash(__('Subjects have been deleted successfully.'), 'success');
						$total = $this->Subject->find('count');
						$page = $total/ADMIN_PAGING;
						$response = [
							'success' => true,
							'page' => ceil($page)
						];
						return json_encode($response);
					}
					// die('success');
				} else {
					$this->Session->setFlash(__('Subject could not deleted successfully.'), 'error');
					die('error');
				}
			}
			return json_encode($response);
        }
	}

	function subject_swap(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;


			if(isset($this->request->data['subject_id'])){

				$getMoveid = $this->Subject->find('first', array('conditions'=>array('id'=>$this->request->data['movetoid'])));

				$topMsg = 'top';
				$downMsg = 'down';


				 $getMoveid['Subject']['sorder'] = ($getMoveid['Subject']['sorder'])  ? $getMoveid['Subject']['sorder'] : 0 ;

				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "DOWN" ){

					if(isset($getMoveid['Subject']['sorder']) && ($getMoveid['Subject']['sorder'] >=0)){

					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){

						$neighborid = $this->Subject->query('SELECT id,sorder FROM `subjects` WHERE `sorder` > '.$getMoveid['Subject']['sorder'].' ORDER BY sorder ASC LIMIT 1');

					}else{
						$neighborid = $this->Subject->query('SELECT id,sorder FROM `subjects` WHERE `sorder` < '.$getMoveid['Subject']['sorder'].' ORDER BY sorder DESC LIMIT 1');

					}

					$this->Subject->id = $this->request->data['movetoid'];
					$this->Subject->saveField('sorder',$neighborid[0]['subjects']['sorder']);

					$this->Subject->id = $neighborid[0]['subjects']['id'];
					$this->Subject->saveField('sorder',$getMoveid['Subject']['sorder']);
					$this->Session->setFlash(__('Subject has been moved down the list successfully.'), 'success');
					return "SUCCESS";
					}
					return "SUCCESS";
				}

				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "TOP" ){

					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){

					$neighborid = $this->Subject->query('SELECT id,sorder FROM `subjects` WHERE `sorder` < '.$getMoveid['Subject']['sorder'].' ORDER BY sorder DESC LIMIT 1');
					}else{

					$neighborid = $this->Subject->query('SELECT id,sorder FROM `subjects` WHERE `sorder` > '.$getMoveid['Subject']['sorder'].' ORDER BY sorder ASC LIMIT 1');
					}


					$this->Subject->id = null;
					$this->Subject->id = $this->request->data['movetoid'];
					$this->Subject->saveField('sorder',$neighborid[0]['subjects']['sorder']);
					$this->Subject->id = null;
					$this->Subject->id = $neighborid[0]['subjects']['id'];
					$this->Subject->saveField('sorder',$getMoveid['Subject']['sorder']);
					$this->Session->setFlash(__('Subject has been moved up in the list successfully.'), 'success');
					return "SUCCESS";

				}

			}

        }

	}


	function subject_DragDrop(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$sortOrderss = $this->request->data['subject_order'];
				$subject_id = $this->request->data['subject_id'];


				/* if( isset($this->request->data['sorder'])  && !empty($this->request->data['sorder'])){
					sort($sortOrderss);
				} else {
				//	rsort($sortOrderss);
				}	 */

				foreach($sortOrderss as $key => $value){
					// e( $subject_id[$key].'<>'.$value);

					$this->Subject->id = null;
					$this->Subject->id = $subject_id[$key];
					$this->Subject->saveField('sorder',$value);

				}
			}
        }

	}

	/************** account edit **********************/

	public function get_subjects() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$subjects = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					 
					$seperator = '^';
					$search_str = Sanitize::escape(like($term, $seperator ));
					//$query = "SELECT id, title FROM skills WHERE status = 1 AND (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%')  order by title asc";
					$query = "SELECT id, title FROM subjects WHERE status = 1 AND (`title` like '%$search_str%' ESCAPE '$seperator' ) order by title asc";
					
					//$query = "SELECT id, title FROM subjects WHERE status = 1 AND (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%')  order by title asc";

					$subjects = $this->Subject->query($query);
					if (isset($subjects) && !empty($subjects)) {
						$subjects = Set::combine($subjects, '{n}.subjects.id', '{n}.subjects.title');
					}
					if (isset($subjects) && !empty($subjects)) {
						$response['success'] = true;
						$response['content'] = $subjects;
					} else {
						$response['success'] = false;
						$response['content'] = null;
						$response['msg'] = 'No matching Subjects found';
					}

				}
			}
			echo json_encode($response);
			exit;
		}

	}


	public function pdf_upload() {
		$this->autoRender = false;
		if ($this->request->isAjax()) {
			$user_id = $this->Auth->user('id');
			//SubjectPdf
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'skillcount' => 0,
			];

			$this->loadModel('SubjectPdf');

			if (isset($this->params['form']['pdf_file']) && !empty(($this->params['form']['pdf_file']))) {

				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$this->request->data['pdf_name'] = strip_tags($this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace("'", "", $this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace('"', "", $this->request->data['pdf_name']);

				$this->request->data['SubjectPdf']['pdf_name'] = $newname = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? str_replace(".", "", pathinfo($this->request->data['pdf_name'], PATHINFO_FILENAME)) . ".pdf" : $this->params['form']['pdf_file']['name'];

				$this->request->data['SubjectPdf']['tooltip_name'] = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? $this->request->data['pdf_name'] : basename($this->params['form']['pdf_file']['name'], ".pdf");

				$this->request->data['SubjectPdf']['user_id'] = $user_id;
				$this->request->data['SubjectPdf']['subject_id'] = $this->request->data['skill_id'];

				if (file_exists(SUBJECT_PDF_PATH . $user_id . DS . $newname)) {

					$filepath = SUBJECT_PDF_PATH . $user_id;
					$fileoldname = $newname;
					$this->request->data['SubjectPdf']['pdf_name'] = $newname = $this->file_newname($filepath, $fileoldname);

				}

				$allowed = array('pdf');
				$filename = $this->params['form']['pdf_file']['name'];
				$tmpname = $this->params['form']['pdf_file']['tmp_name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$filsize = filesize($tmpname);

				if (in_array($ext, $allowed) && $filsize <= 5242880) {

					if (!file_exists(SUBJECT_PDF_PATH . $user_id)) {
						mkdir(SUBJECT_PDF_PATH . $user_id, 0777, true);
					}
					if (move_uploaded_file($tmpname, SUBJECT_PDF_PATH . $user_id . '/' . $newname)) {
						$this->SubjectPdf->save($this->request->data);

						$getSkill = $this->SubjectPdf->find('all', array('conditions' => array('SubjectPdf.user_id' => $user_id, 'SubjectPdf.subject_id' => $this->request->data['skill_id']), 'order' => 'SubjectPdf.pdf_name ASC'));

						$content = [];
						if (isset($getSkill) && !empty($getSkill)) {
							$skillCnt = 0;
							foreach($getSkill as $listskill){

								if( file_exists(SUBJECT_PDF_PATH . $listskill['SubjectPdf']['user_id'] . DS . $listskill['SubjectPdf']['pdf_name']) ){
									$content[$skillCnt] = ['pdfname' => $listskill['SubjectPdf']['pdf_name'], 'pdf_id' => $listskill['SubjectPdf']['id']];
									if( isset($listskill['SubjectPdf']['tooltip_name']) && !empty($listskill['SubjectPdf']['tooltip_name']) ){
										$content[$skillCnt]['tooltip_name'] = $listskill['SubjectPdf']['tooltip_name'];
									} else {
										$pdftooltip = explode(".pdf",$listskill['SubjectPdf']['pdf_name']);
										$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
									}
									$content[$skillCnt]['user_id'] = $listskill['SubjectPdf']['user_id'];
									$skillCnt++;
								}
							}

						}

						$response = [
							'success' => true,
							'msg' => 'Pdf file uploaded successfully',
							'content' => $content,
							'skillcount' => $skillCnt,

						];

					}

				} else {
					$response = [
						'success' => false,
						'msg' => 'File type or file type is not valid',
						'content' => '',
						'skillcount' => 0,
					];
				}

			}
			echo (json_encode($response));
			exit;

		}

	}

	public function get_pdf() {
		if ($this->request->isAjax()) {
			$user_id = $this->Auth->user('id');
			$response = [
				'success' => false,
				'content' => ['pdfname'=>'','pdf_id'=>'','tooltip_name'=>'','user_id'=>'','skillcount'=>'','SubjectDetail'=>''],
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				$response['success'] = true;
				// $user_id = $this->Auth->user('id');
				// pr($post, 1);

				$this->loadModel('SubjectPdf');
				$this->loadModel('SubjectDetail');
				if( !empty($post['skill_id']) ){
					$getSkill = $this->SubjectPdf->find('all', array('conditions' => array('SubjectPdf.user_id' => $user_id, 'SubjectPdf.subject_id' => $post['skill_id'] ), 'order' => 'SubjectPdf.pdf_name ASC'));

					$userSubjectDetails = $this->SubjectDetail->find('first',array('conditions'=>array('SubjectDetail.subject_id'=>$post['skill_id'],'SubjectDetail.user_id'=>$user_id)));

					// pr($getSkill);
					$content = [];
					if (isset($getSkill) && !empty($getSkill)) {
						$skillCnt = 0;
						foreach($getSkill as $listskill){

							if( file_exists(SUBJECT_PDF_PATH . $listskill['SubjectPdf']['user_id'] . DS . $listskill['SubjectPdf']['pdf_name']) ){
								$content[$skillCnt] = ['pdfname' => $listskill['SubjectPdf']['pdf_name'], 'pdf_id' => $listskill['SubjectPdf']['id']];
								if( isset($listskill['SubjectPdf']['tooltip_name']) && !empty($listskill['SubjectPdf']['tooltip_name']) ){
									$content[$skillCnt]['tooltip_name'] = $listskill['SubjectPdf']['tooltip_name'];
								} else {
									$pdftooltip = explode(".pdf",$listskill['SubjectPdf']['pdf_name']);
									$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
								}
								$content[$skillCnt]['user_id'] = $listskill['SubjectPdf']['user_id'];
								$skillCnt++;
							}

						}
						$response['content']['skillcount'] = $skillCnt;
					}


					if( isset($userSubjectDetails) && !empty($userSubjectDetails['SubjectDetail']) ){

						$content['details']['user_level'] = $userSubjectDetails['SubjectDetail']['user_level'];
						$content['details']['user_experience'] = $userSubjectDetails['SubjectDetail']['user_experience'];
						$content['details']['subject_id'] = $userSubjectDetails['SubjectDetail']['subject_id'];
					} else {
						$content['details']['user_level'] = null;
						$content['details']['user_experience'] = null;
					}

				}

				$response['content'] = $content;
			}
		}
		echo json_encode($response);
		exit();
	}

	public function pdf_delete() {
		if ($this->request->isAjax()) {
			$response = [
				'success' => false,
				'content' => [],
			];

			$this->loadModel('SubjectPdf');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$getSkill = $this->SubjectPdf->find('first', array('conditions' => array('SubjectPdf.id' => $post['id'])));

				if (isset($getSkill) && !empty($getSkill)) {
					$this->SubjectPdf->delete(array('SubjectPdf.id' => $post['id']));
					$response['success'] = true;
					if (file_exists(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['SubjectPdf']['pdf_name'])) {
						unlink(SUBJECT_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['SubjectPdf']['pdf_name']);
					}
				}

			}
		}
		echo json_encode($response);
		exit();
	}

	public function save_user_subject_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');
			if( isset($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$this->loadModel('UserSubject');
				$this->loadModel('SubjectDetail');
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$user_skill_count = $this->UserSubject->find('count',
                                   [
	                                   	'conditions' =>
		                                   	[
		                               			'UserSubject.user_id' => $user_id,
		                               			'UserSubject.subject_id' => $this->request->data['skill_id']
											]
									]
								);
				if(!isset($user_skill_count) || empty($user_skill_count)){
					$skilldata['UserSubject'] = ['user_id' => $user_id, 'subject_id' => $this->request->data['skill_id']];
					$this->UserSubject->save($skilldata);
				}

				$this->request->data['SubjectDetail']['user_level'] = $this->request->data['user_level'];
				$this->request->data['SubjectDetail']['user_experience'] = $this->request->data['user_experience'];
				$this->request->data['SubjectDetail']['subject_id'] = $this->request->data['skill_id'];
				$this->request->data['SubjectDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SubjectDetail->find('all',
						array('conditions'=>array('SubjectDetail.subject_id' => $this->request->data['skill_id'],'SubjectDetail.user_id' => $user_id))
				);

				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SubjectDetail/id');
					if( $this->SubjectDetail->delete($skillDetailIds) ){
						$this->SubjectDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}

				} else {
					$this->SubjectDetail->save($this->request->data);
					$response['success'] = true;
					$response['content'] = $this->request->data;
				}
			}

		echo json_encode($response);
		exit();
		}

	}
	public function delete_user_subject_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');
			$this->loadModel('SubjectDetail');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){

				$this->request->data['SubjectDetail']['subject_id'] = $this->request->data['skill_id'];
				$this->request->data['SubjectDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SubjectDetail->find('all',
						array('conditions'=>array('SubjectDetail.subject_id'=>$this->request->data['skill_id'], 'SubjectDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SubjectDetail/id');
					if( $this->SubjectDetail->delete($skillDetailIds) ){
						$this->SubjectDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}
				}
			}

		echo json_encode($response);
		exit();
		}

	}

	public function delete_user_subject(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');
			$this->loadModel('SubjectDetail');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				// pr($post, 1);
				$this->request->data['SubjectDetail']['subject_id'] = $this->request->data['skill_id'];
				$this->request->data['SubjectDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->SubjectDetail->find('all',
						array('conditions'=>array('SubjectDetail.subject_id'=>$this->request->data['skill_id'],'SubjectDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/SubjectDetail/id');

					$this->SubjectDetail->delete($skillDetailIds);
				}


				$countSkillDetail = $this->UserSubject->find('all',
						array('conditions'=> ['UserSubject.subject_id'=>$this->request->data['skill_id'], 'UserSubject.user_id'=>$user_id], 'recursive'=>-1, 'fields'=> ['id']));
				if(isset($countSkillDetail) && !empty($countSkillDetail)){
					$user_skill_ids = Set::extract($countSkillDetail, '/UserSubject/id');
					// pr($user_skill_ids, 1);
					// pr($user_skill_ids, 1);
					$this->UserSubject->delete($user_skill_ids);
				}
				$response['success'] = true;
				$response['content'] = $this->request->data;
			}

		echo json_encode($response);
		exit();
		}

	}

	// download pdf
	public function download_pdf($id = null, $user_id = null) {
		$this->autoRender = false;
		if (isset($id) && !empty($id)) {
			$this->loadModel('SubjectPdf');

			// Retrieve the file ready for download
			$data = $this->SubjectPdf->find('first', ['conditions' => ['SubjectPdf.id' => $id]]);

			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (file_exists(SUBJECT_PDF_PATH . $user_id . DS . $data['SubjectPdf']['pdf_name'])) {
				if (isset($data) && !empty($data)) {
					// Send file as response
					$response['content'] = SUBJECT_PDF_PATH . $user_id . DS . $data['SubjectPdf']['pdf_name'];
					$response['success'] = true;
					return $this->response->file($response['content'], array('download' => true));
				}
			} else {
				return false;
			}
		}
	}


	function file_newname($path, $filename) {
		if ($pos = strrpos($filename, '.')) {
			$name = substr($filename, 0, $pos);
			$ext = substr($filename, $pos);
		} else {
			$name = $filename;
		}

		$newpath = $path . '/' . $filename;
		$newname = $filename;
		$counter = 0;
		while (file_exists($newpath)) {
			$newname = $name . '_' . $counter . $ext;
			$newpath = $path . '/' . $newname;
			$counter++;
		}

		return $newname;
	}

}