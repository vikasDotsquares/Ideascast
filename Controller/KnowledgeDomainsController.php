<?php
/**
 * KnowledgeDomains Controller
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

class KnowledgeDomainsController  extends AppController {

	public $name = 'KnowledgeDomains';
	public $uses = array('KnowledgeDomain','UserDomain','ProjectDomain');

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
        $data['title_for_layout'] = __('Domains', true);
        $data['page_heading'] = __('Domains', true);
        //$data['page_subheading'] = __('Skills list, available to all Domains', true);
        $data['page_subheading'] = __('Domains list', true);

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
           // $per_page_show = ADMIN_PAGING;
            $per_page_show = 50;
        }

		if (($this->Session->read('Auth.User.role_id') == 2 && $this->Session->read('Auth.User.UserDetail.administrator') != 1)  && LOCALIP != $_SERVER['SERVER_ADDR'] ) {

			$this->redirect(array('controller' => 'projects', 'action' => 'lists'));
		}

		if( ($this->request->is('post') || $this->request->is('put')) ) {
			if (isset($this->data['KnowledgeDomain']['keyword'])) {
				$keyword = trim($this->data['KnowledgeDomain']['keyword']);
			} else {
				$keyword = $this->Session->read('KnowledgeDomain.keyword');
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

			//$this->redirect(['action' => 'index','page'=>$pages,'sortorder'=>$ascorder,'search' => $keyword]);
			$this->redirect(['action' => 'index','page'=>$pages,'sort'=>'KnowledgeDomain.title','search' => $keyword,'direction'=>$ascorder]);

		}

		if (isset($this->params['named']['search']) && !empty($this->params['named']['search'])) {
			$keyword = trim($this->params['named']['search']);
		} else {
			$keyword = $this->Session->read('KnowledgeDomain.keyword');
		}

        if (isset($keyword)) {
            $this->Session->write('domain.keyword', $keyword);
            $keywords = explode(" ", $keyword);

            if ( !empty($keyword) ) {

                $andConditions = array('AND' => array(
                    'KnowledgeDomain.title LIKE' => '%' . $keyword . '%',
                ));
				$in = 1;
            }
        }

        if (isset($this->data['KnowledgeDomain']['per_page_show']) && !empty($this->data['KnowledgeDomain']['per_page_show'])) {
            $per_page_show = $this->data['KnowledgeDomain']['per_page_show'];
        }

        if (!empty($orConditions)) {
            $finalConditions = array_merge($finalConditions, $orConditions);
        }
        if (!empty($andConditions)) {
            $finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
        }



		if( isset($this->params['named']['sortorder']) && !empty($this->params['named']['sortorder']) && ( $this->params['named']['sortorder'] == 'desc' || $this->params['named']['sortorder'] == 'DESC' ) ){
			//$skillOrder = "Skill.title ".$this->params['named']['sortorder'];
			$skillOrder = "KnowledgeDomain.sorder ".$this->params['named']['sortorder'];
		} else {
			$skillOrder = 'KnowledgeDomain.title ASC';
		}

		if( isset($this->params['named']['direction']) && !empty($this->params['named']['direction'])  ){
			//$skillOrder = "Skill.title ".$this->params['named']['sortorder'];
			$skillOrder = "KnowledgeDomain.title ".$this->params['named']['direction'];
		}

		$count = $this->KnowledgeDomain->find('count', array('conditions' => $finalConditions));
		//$countF = $this->Skill->find('all', array('conditions' => $finalConditions,"order" => $skillOrder));
        $this->set('count', $count);

        $this->set('title_for_layout', __('All Domains', true));
        $this->Session->write('skill.per_page_show', $per_page_show);
        $this->KnowledgeDomain->recursive = 0;

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
		$this->set('currencies', $this->paginate('KnowledgeDomain'));


		// $this->Paginator = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => $skillOrder);

       // $this->set('currencies', $this->paginate('Skill'));

		$viewData['crumb'] = [
			'last' => [
				'data' => [
					'title' => "Domains",
					'data-original-title' => "Domains",
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
    public function domain_add() {
        $this->set('title_for_layout', __('Add Domain', true));
        if ($this->request->is('post') || $this->request->is('put')) { //pr($this->request->data); die;

			if ($this->KnowledgeDomain->save($this->request->data,true)) {
                $this->Session->setFlash(__('Domain has been saved successfully.'), 'success');
                die('success');
            }

        }
    }


    /**
     * Open Popup Modal Boxes method
		*
     * @return void
     */
    public function manage_domain( $id = null ) {

        if ($this->request->isAjax()) {

            $this->layout = 'ajax';
			$response = ['success'=> false, 'content'=>null];

			if( ($this->request->is('post') || $this->request->is('put')) ) {

				$checkskill = $this->KnowledgeDomain->find('count', array('conditions'=>array('KnowledgeDomain.title'=>trim($this->request->data['KnowledgeDomain']['title'])) ) );

				if( !isset($this->request->data['KnowledgeDomain']['title']) || empty(trim($this->request->data['KnowledgeDomain']['title'])) )
				{
					$response['content']['title'] = 'Domain title is required';

				} else if( $checkskill > 0 ) {

					$response['content']['title'] = 'Domain title has already been taken';

				} else {
				//if( !empty(trim($this->request->data['Skill']['title'])) ){

					$frmactionType = $this->request->data['KnowledgeDomain']['actionType'];
					unset($this->request->data['KnowledgeDomain']['actionType']);

					$this->request->data['KnowledgeDomain']['title'] = strip_tags(trim($this->request->data['KnowledgeDomain']['title']));
					$this->KnowledgeDomain->set($this->request->data);

					if ($this->KnowledgeDomain->save($this->request->data, false)) {
						$lastid = $this->KnowledgeDomain->getLastInsertId();
						if( $frmactionType == 'editAction' ){
							$this->Session->setFlash(__('Domain has been updated successfully.'), 'success');
							$response = ['success'=> true ];
						} else {
							$this->request->data['KnowledgeDomain']['sorder'] = $lastid;
							$this->request->data['KnowledgeDomain']['id'] = $lastid;
							$this->KnowledgeDomain->save($this->request->data, false);
							$this->Session->setFlash(__('Domain has been added successfully.'), 'success');
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
				$this->request->data = $this->KnowledgeDomain->read(null, $id);
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
    public function domain_edit($id = null) {

        $this->set('title_for_layout', __('Edit Domain', true));

        if( ($this->request->is('post') || $this->request->is('put')) ) {

			if( !empty($this->request->data['KnowledgeDomain']['title'])  ){

				$frmactionType = $this->request->data['KnowledgeDomain']['actionType'];
				unset($this->request->data['KnowledgeDomain']['actionType']);

				if ($this->KnowledgeDomain->save($this->request->data, false)) {
					$lastid = $this->KnowledgeDomain->getLastInsertId();
					if( $frmactionType == 'editAction' ){
						$this->Session->setFlash(__('Domain has been updated successfully.'), 'success');
					} else {
						$this->request->data['KnowledgeDomain']['sorder'] = $lastid;
						$this->request->data['KnowledgeDomain']['id'] = $lastid;
						$this->KnowledgeDomain->save($this->request->data, false);
						$this->Session->setFlash(__('Domain has been added successfully.'), 'success');
					}
					die('success');
				}

			} else {
				$this->Session->setFlash(__('Please try again.'), 'error');
			}
        } else {
			$this->request->data = $this->KnowledgeDomain->read(null, $id);
        }

    }

	function domain_resetfilter(){

		$this->Session->write('domain.keyword', '');
		$this->redirect(array('action' => 'index'));
	}

	function domain_updatestatus(){


		if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->loadModel('KnowledgeDomain');

            $this->request->data['KnowledgeDomain'] = $this->request->data;
			//pr($this->request->data); die;
            if ($this->KnowledgeDomain->save($this->request->data, false )) {
				$this->KnowledgeDomain->updateAll( array('KnowledgeDomain.status' => $this->request->data['KnowledgeDomain']['status']),array('KnowledgeDomain.id' => $this->request->data['KnowledgeDomain']['id']));
                $this->Session->setFlash(__('Domain status has been updated successfully.'), 'success');
                die('success');
            } else {
                $this->Session->setFlash(__('Domain status could not updated successfully.'), 'error');
            }
        }

	}

	function domain_delete(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;
            $this->loadModel('KnowledgeDomain');
            $response = [
				'success' => false,
				'page' => 0
			];

			if(isset($this->request->data['domain_id'])){
				$domain_id = $this->request->data['domain_id'];
				$condition = array('KnowledgeDomain.id' => $this->request->data['domain_id']);
				$userSkillCondition = array('UserDomain.domain_id' => $this->request->data['domain_id']);

				$userPDCondition = array('ProjectDomain.domain_id' => $this->request->data['domain_id']);

				if ($this->KnowledgeDomain->deleteAll($condition,false)) {
					$this->UserDomain->deleteAll($userSkillCondition,false);
					$this->ProjectDomain->deleteAll($userPDCondition,false);
					if( $this->request->data['deltype'] == 'single' ){
						$this->Session->setFlash(__('Domain has been deleted successfully.'), 'success');
						$total = $this->KnowledgeDomain->find('count');
						$page = $total/10;
						$response = [
							'success' => true,
							'page' => ceil($page)
						];
						return json_encode($response);
					} else {
						$this->Session->setFlash(__('Domains have been deleted successfully.'), 'success');
						$total = $this->KnowledgeDomain->find('count');
						$page = $total/10;
						$response = [
							'success' => true,
							'page' => ceil($page)
						];
						return json_encode($response);
					}

					//die('success');
				} else {
					$this->Session->setFlash(__('Domain could not deleted successfully.'), 'error');
					die('error');
				}
			}
			return json_encode($response);

        }
	}

	function domain_swap(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;
            $this->loadModel('KnowledgeDomain');


			if(isset($this->request->data['domain_id'])){

				$getMoveid = $this->KnowledgeDomain->find('first', array('conditions'=>array('id'=>$this->request->data['movetoid'])));

				//pr($this->request->data['movetoid']);
				//pr($getMoveid);

				$topMsg = 'top';
				$downMsg = 'down';


				 $getMoveid['KnowledgeDomain']['sorder'] = ($getMoveid['KnowledgeDomain']['sorder'])  ? $getMoveid['KnowledgeDomain']['sorder'] : 0 ;

				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "DOWN" ){

					if(isset($getMoveid['KnowledgeDomain']['sorder']) && ($getMoveid['KnowledgeDomain']['sorder'] >=0)){

					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){

					$neighborid = $this->KnowledgeDomain->query('SELECT id,sorder FROM `knowledge_domains` WHERE `sorder` > '.$getMoveid['KnowledgeDomain']['sorder'].' ORDER BY sorder ASC LIMIT 1');

				 	//echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1';

					}else{
					$neighborid = $this->Skill->query('SELECT id,sorder FROM `knowledge_domains` WHERE `sorder` < '.$getMoveid['KnowledgeDomain']['sorder'].' ORDER BY sorder DESC LIMIT 1');

					}


					//  echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1'.'<br>';

					//pr($neighborid);  die;
			/* 						echo $this->request->data['movetoid'];
						pr($neighborid[0]['skills']['sorder']);

						echo "<br>";
						pr($neighborid[0]['skills']['id']);
						echo	$getMoveid['Skill']['sorder']; */
					// die;

					$this->KnowledgeDomain->id = $this->request->data['movetoid'];
					$this->KnowledgeDomain->saveField('sorder',$neighborid[0]['knowledge_domains']['sorder']);

					$this->KnowledgeDomain->id = $neighborid[0]['knowledge_domains']['id'];
					$this->KnowledgeDomain->saveField('sorder',$getMoveid['KnowledgeDomain']['sorder']);
					$this->Session->setFlash(__('Domain has been moved down the list successfully.'), 'success');
					return "SUCCESS";
					}
					return "SUCCESS";
				}

				if( isset($this->request->data['moveto']) && $this->request->data['moveto'] == "TOP" ){

					//$neighborid = $this->Skill->query('select * from skills where sorder = (select min(sorder) from skills where sorder > '.$getMoveid['Skill']['sorder'].')');
					//echo $this->request->data['sortorder'];
					if( isset($this->request->data['sortorder']) && ( $this->request->data['sortorder'] == 'asc' || $this->request->data['sortorder'] == 'ASC' ) ){

					$neighborid = $this->KnowledgeDomain->query('SELECT id,sorder FROM `knowledge_domains` WHERE `sorder` < '.$getMoveid['KnowledgeDomain']['sorder'].' ORDER BY sorder DESC LIMIT 1');

				 	//echo 'SELECT id,sorder FROM `skills` WHERE `sorder` < '.$getMoveid['Skill']['sorder'].' ORDER BY sorder DESC LIMIT 1';

					}else{

					$neighborid = $this->KnowledgeDomain->query('SELECT id,sorder FROM `knowledge_domains` WHERE `sorder` > '.$getMoveid['KnowledgeDomain']['sorder'].' ORDER BY sorder ASC LIMIT 1');
					}


				/* 						echo $this->request->data['movetoid'];
						pr($neighborid[0]['skills']['sorder']);

						echo "<br>";
						pr($neighborid[0]['skills']['id']);
						echo	$getMoveid['Skill']['sorder'];	 */
					// pr($neighborid);
					// pr($getMoveid);
 					//die;

					$this->KnowledgeDomain->id = null;
					$this->KnowledgeDomain->id = $this->request->data['movetoid'];
					$this->KnowledgeDomain->saveField('sorder',$neighborid[0]['knowledge_domains']['sorder']);
					$this->KnowledgeDomain->id = null;
					$this->KnowledgeDomain->id = $neighborid[0]['knowledge_domains']['id'];
					$this->KnowledgeDomain->saveField('sorder',$getMoveid['KnowledgeDomain']['sorder']);
					$this->Session->setFlash(__('Domain has been moved up in the list successfully.'), 'success');
					return "SUCCESS";

				}

			}

        }

	}


	function domain_DragDrop(){

		if ($this->request->is('ajax')) {

            $this->autoRender = false;
            $this->loadModel('KnowledgeDomain');

			if( $this->request->is( 'post' ) || $this->request->is( 'put' ) ) {

				$sortOrderss = $this->request->data['skill_order'];
				$domain_id = $this->request->data['domain_id'];


				/* if( isset($this->request->data['sorder'])  && !empty($this->request->data['sorder'])){
					sort($sortOrderss);
				} else {
				//	rsort($sortOrderss);
				}	 */

				foreach($sortOrderss as $key => $value){
					e( $domain_id[$key].'<>'.$value);

					$this->KnowledgeDomain->id = null;
					$this->KnowledgeDomain->id = $domain_id[$key];
					$this->KnowledgeDomain->saveField('sorder',$value);

				}
			}
        }

	}



	/************** account edit **********************/

	public function get_domains() {

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			// pr($this->request->query['term'] , 1);
			$response = [
				'success' => false,
				'msg' => null,
				'content' => null,
			];
			$knowledge_domains = [];
			if ($this->request->isJson()) {
				if (isset($this->request->query['term']) && !empty($this->request->query['term'])) {
					$term = $this->request->query['term'];
					
					$seperator = '^';
					$search_str = Sanitize::escape(like($term, $seperator ));
					 
					$query = "SELECT id, title FROM knowledge_domains WHERE status = 1 AND (`title` like '%$search_str%' ESCAPE '$seperator' ) order by title asc";
					//$query = "SELECT id, title FROM knowledge_domains WHERE status = 1 AND (`title` like '% $term%' OR  `title` like  '%$term %'  OR  `title` like  '% $term %' OR `title` like '$term%')  order by title asc";

					$knowledge_domains = $this->KnowledgeDomain->query($query);
					if (isset($knowledge_domains) && !empty($knowledge_domains)) {
						$knowledge_domains = Set::combine($knowledge_domains, '{n}.knowledge_domains.id', '{n}.knowledge_domains.title');
					}
					if (isset($knowledge_domains) && !empty($knowledge_domains)) {
						$response['success'] = true;
						$response['content'] = $knowledge_domains;
					} else {
						$response['success'] = false;
						$response['content'] = null;
						$response['msg'] = 'No matching Domains found';
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
			//DomainPdf
			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'msg' => '',
				'content' => null,
				'skillcount' => 0,
			];

			$this->loadModel('DomainPdf');

			if (isset($this->params['form']['pdf_file']) && !empty(($this->params['form']['pdf_file']))) {
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$this->request->data['pdf_name'] = strip_tags($this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace("'", "", $this->request->data['pdf_name']);
				$this->request->data['pdf_name'] = str_replace('"', "", $this->request->data['pdf_name']);

				$this->request->data['DomainPdf']['pdf_name'] = $newname = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? str_replace(".", "", pathinfo($this->request->data['pdf_name'], PATHINFO_FILENAME)) . ".pdf" : $this->params['form']['pdf_file']['name'];

				$this->request->data['DomainPdf']['tooltip_name'] = (isset($this->request->data['pdf_name']) && !empty($this->request->data['pdf_name'])) ? $this->request->data['pdf_name'] : basename($this->params['form']['pdf_file']['name'], ".pdf");

				$this->request->data['DomainPdf']['user_id'] = $user_id;
				$this->request->data['DomainPdf']['domain_id'] = $this->request->data['skill_id'];

				if (file_exists(DOMAIN_PDF_PATH . $user_id . DS . $newname)) {

					$filepath = DOMAIN_PDF_PATH . $user_id;
					$fileoldname = $newname;
					$this->request->data['DomainPdf']['pdf_name'] = $newname = $this->file_newname($filepath, $fileoldname);

				}

				$allowed = array('pdf');
				$filename = $this->params['form']['pdf_file']['name'];
				$tmpname = $this->params['form']['pdf_file']['tmp_name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$filsize = filesize($tmpname);

				if (in_array($ext, $allowed) && $filsize <= 5242880) {

					if (!file_exists(DOMAIN_PDF_PATH . $user_id)) {
						mkdir(DOMAIN_PDF_PATH . $user_id, 0777, true);
					}
					if (move_uploaded_file($tmpname, DOMAIN_PDF_PATH . $user_id . '/' . $newname)) {
						$this->DomainPdf->save($this->request->data);

						$getSkill = $this->DomainPdf->find('all', array('conditions' => array('DomainPdf.user_id' => $user_id, 'DomainPdf.domain_id' => $this->request->data['skill_id']), 'order' => 'DomainPdf.pdf_name ASC'));

						$content = [];
						if (isset($getSkill) && !empty($getSkill)) {
							$skillCnt = 0;
							foreach($getSkill as $listskill){

								if( file_exists(DOMAIN_PDF_PATH . $listskill['DomainPdf']['user_id'] . DS . $listskill['DomainPdf']['pdf_name']) ){
									$content[$skillCnt] = ['pdfname' => $listskill['DomainPdf']['pdf_name'], 'pdf_id' => $listskill['DomainPdf']['id']];
									if( isset($listskill['DomainPdf']['tooltip_name']) && !empty($listskill['DomainPdf']['tooltip_name']) ){
										$content[$skillCnt]['tooltip_name'] = $listskill['DomainPdf']['tooltip_name'];
									} else {
										$pdftooltip = explode(".pdf",$listskill['DomainPdf']['pdf_name']);
										$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
									}
									$content[$skillCnt]['user_id'] = $listskill['DomainPdf']['user_id'];
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
				'content' => ['pdfname'=>'','pdf_id'=>'','tooltip_name'=>'','user_id'=>'','skillcount'=>'','DomainDetail'=>''],
			];
			if ($this->request->is('post') || $this->request->is('put')) {

				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');
				$response['success'] = true;

				$this->loadModel('DomainPdf');
				$this->loadModel('DomainDetail');
				if( !empty($post['skill_id']) ){
					$getSkill = $this->DomainPdf->find('all', array('conditions' => array('DomainPdf.user_id' => $user_id, 'DomainPdf.domain_id' => $post['skill_id'] ), 'order' => 'DomainPdf.pdf_name ASC'));

					$userDomainDetails = $this->DomainDetail->find('first',array('conditions'=>array('DomainDetail.domain_id'=>$post['skill_id'],'DomainDetail.user_id'=>$user_id)));

					$content = [];
					if (isset($getSkill) && !empty($getSkill)) {
						$skillCnt = 0;
						foreach($getSkill as $listskill){

							if( file_exists(DOMAIN_PDF_PATH . $listskill['DomainPdf']['user_id'] . DS . $listskill['DomainPdf']['pdf_name']) ){
								$content[$skillCnt] = ['pdfname' => $listskill['DomainPdf']['pdf_name'], 'pdf_id' => $listskill['DomainPdf']['id']];
								if( isset($listskill['DomainPdf']['tooltip_name']) && !empty($listskill['DomainPdf']['tooltip_name']) ){
									$content[$skillCnt]['tooltip_name'] = $listskill['DomainPdf']['tooltip_name'];
								} else {
									$pdftooltip = explode(".pdf",$listskill['DomainPdf']['pdf_name']);
									$content[$skillCnt]['tooltip_name'] = $pdftooltip[0];
								}
								$content[$skillCnt]['user_id'] = $listskill['DomainPdf']['user_id'];
								$skillCnt++;
							}

						}
						$response['content']['skillcount'] = $skillCnt;
					}


					if( isset($userDomainDetails) && !empty($userDomainDetails['DomainDetail']) ){

						$content['details']['user_level'] = $userDomainDetails['DomainDetail']['user_level'];
						$content['details']['user_experience'] = $userDomainDetails['DomainDetail']['user_experience'];
						$content['details']['domain_id'] = $userDomainDetails['DomainDetail']['domain_id'];
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

			$this->loadModel('DomainPdf');
			if ($this->request->is('post') || $this->request->is('put')) {
				$post = $this->request->data;

				$getSkill = $this->DomainPdf->find('first', array('conditions' => array('DomainPdf.id' => $post['id'])));

				if (isset($getSkill) && !empty($getSkill)) {
					$this->DomainPdf->delete(array('DomainPdf.id' => $post['id']));
					$response['success'] = true;
					if (file_exists(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['DomainPdf']['pdf_name'])) {
						unlink(DOMAIN_PDF_PATH . $this->Session->read("Auth.User.id") . DS . $getSkill['DomainPdf']['pdf_name']);
					}
				}

			}
		}
		echo json_encode($response);
		exit();
	}

	public function save_user_domain_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');

			if( isset($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$this->loadModel('UserDomain');
				$this->loadModel('DomainDetail');
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$user_skill_count = $this->UserDomain->find('count',
                                   [
	                                   	'conditions' =>
		                                   	[
		                               			'UserDomain.user_id' => $user_id,
		                               			'UserDomain.domain_id' => $this->request->data['skill_id']
											]
									]
								);
				if(!isset($user_skill_count) || empty($user_skill_count)){
					$skilldata['UserDomain'] = ['user_id' => $user_id, 'domain_id' => $this->request->data['skill_id']];
					$this->UserDomain->save($skilldata);
				}

				$this->request->data['DomainDetail']['user_level'] = $this->request->data['user_level'];
				$this->request->data['DomainDetail']['user_experience'] = $this->request->data['user_experience'];
				$this->request->data['DomainDetail']['domain_id'] = $this->request->data['skill_id'];
				$this->request->data['DomainDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->DomainDetail->find('all',
						array('conditions'=>array('DomainDetail.domain_id' => $this->request->data['skill_id'], 'DomainDetail.user_id' => $user_id))
				);

				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/DomainDetail/id');
					if( $this->DomainDetail->delete($skillDetailIds) ){
						$this->DomainDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}

				} else {
					$this->DomainDetail->save($this->request->data);
					$response['success'] = true;
					$response['content'] = $this->request->data;
				}
			}

		echo json_encode($response);
		exit();
		}

	}
	public function delete_user_domain_detail(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');
			$this->loadModel('DomainDetail');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){

				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');


				$this->request->data['DomainDetail']['domain_id'] = $this->request->data['skill_id'];
				$this->request->data['DomainDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->DomainDetail->find('all',
						array('conditions'=>array('DomainDetail.domain_id'=>$this->request->data['skill_id'],'DomainDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/DomainDetail/id');
					if( $this->DomainDetail->delete($skillDetailIds) ){
						$this->DomainDetail->save($this->request->data);
						$response['success'] = true;
						$response['content'] = $this->request->data;
					}
				}
			}

		echo json_encode($response);
		exit();
		}

	}
	public function delete_user_domain(){

		if ($this->request->isAjax()) {

			$this->layout = 'ajax';
			$response = [
				'success' => false,
				'content' => null,
			];

			$user_id = $this->Auth->user('id');
			$this->loadModel('DomainDetail');

			if( !empty($this->request->data['skill_id']) && !empty($this->request->data['skill_id']) ){
				$post = $this->request->data;
				$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? $post['user_id'] : $this->Auth->user('id');

				$this->request->data['DomainDetail']['domain_id'] = $this->request->data['skill_id'];
				$this->request->data['DomainDetail']['user_id'] = $user_id;

				$checkSkillDetails = $this->DomainDetail->find('all',
						array('conditions'=>array('DomainDetail.domain_id'=>$this->request->data['skill_id'],'DomainDetail.user_id'=>$user_id))
				);
				if( isset($checkSkillDetails) && count($checkSkillDetails) > 0 ){

					$skillDetailIds = Set::extract($checkSkillDetails, '/DomainDetail/id');
					$this->DomainDetail->delete($skillDetailIds);
				}


				$countSkillDetail = $this->UserDomain->find('all',
						array('conditions'=> ['UserDomain.domain_id'=>$this->request->data['skill_id'], 'UserDomain.user_id'=>$user_id], 'recursive'=>-1, 'fields'=> ['id']));
				if(isset($countSkillDetail) && !empty($countSkillDetail)){
					$user_skill_ids = Set::extract($countSkillDetail, '/UserDomain/id');
					$this->UserDomain->delete($user_skill_ids);
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
			$this->loadModel('DomainPdf');

			// Retrieve the file ready for download
			$data = $this->DomainPdf->find('first', ['conditions' => ['DomainPdf.id' => $id]]);

			if (empty($data)) {
				throw new NotFoundException();
			}

			$response = [
				'success' => false,
				'content' => null,
			];

			if (file_exists(DOMAIN_PDF_PATH . $user_id . DS . $data['DomainPdf']['pdf_name'])) {
				if (isset($data) && !empty($data)) {
					// Send file as response
					$response['content'] = DOMAIN_PDF_PATH . $user_id . DS . $data['DomainPdf']['pdf_name'];
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