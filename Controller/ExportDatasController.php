<?php
App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'MPDF56/PhpWord');
header('Content-type: text/html; charset=utf-8');

class ExportDatasController extends AppController {

    public $name = 'ExportDatas';
    public $uses = [
        'User',
        'UserDetail',
        'ProjectPermission',
        'UserSetting',
        'Category',
        'Aligned',
        'UserProject',
        'Project',
        'Workspace',
        'Area',
        'ProjectWorkspace',
        'Element',
        'ProjectGroup',
        'ProjectGroupUser',
        'ProjectSketch'
    ];
    public $user_id = null;
    public $pagination = null;
    public $components = array('Mpdf', 'Common', 'CommonEmail', 'Group');

    /**
     * check login for admin and frontend user
     * allow and deny user
     */
    //public $components = array('Email');
    // $this->loadModel('Project');
    /**
     * Helpers
     *
     * @var array
     */
    public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text','Phpword', 'Common', 'ViewModel', 'Mpdf', 'Scratch', 'Js' => array('Jquery'));

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('controller', 'sketches');
        $this->user_id = $this->Auth->user('id');
    }

    public function index() {
        $viewVars = $data = null;
        $this->layout = false;

        $project_id = (isset($this->params['named']['project_id']) && !empty($this->params['named']['project_id'])) ? $this->params['named']['project_id'] : null;
        $viewVars['project_id'] = $project_id;

        if ((empty($project_id) || !is_numeric($project_id) || $project_id == null)) {
            $this->Session->setFlash(__('Invalid Project Id.'));
            //$this->redirect($this->referer());
            $this->redirect(array("controller" => "projects", "action" => "lists"));
        }

        $this->Project->id = $project_id;
        if (!$this->Project->exists()) {
            $this->Session->setFlash(__('Invalid Project.'));
            $this->redirect(array("controller" => "projects", "action" => "lists"));
        }
		
		//================================================================================
		
			App::import('Controller', 'Users');
			$Users = new UsersController;
			$projects = null;
			
			$project = $this->Project->findById($project_id);
			
			$myprojectlist = $myreceivedprojectlist = $mygroupprojectlist = array();
			// Find All current user's projects
			$myprojectlist = $Users->__myproject_selectbox($this->user_id);
			// Find All current user's received projects
			$myreceivedprojectlist = $Users->__receivedproject_selectbox($this->user_id, 1);
			// Find All current user's group projects
			$mygroupprojectlist = $Users->__groupproject_selectbox($this->user_id, 1);

			if (is_array($myprojectlist)) {
				$projects1 = $myprojectlist;
			}

			if (is_array($mygroupprojectlist)) {
				$projects1 = array_replace($mygroupprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			if (is_array($myreceivedprojectlist) && is_array($projects1)) {
				$projects1 = array_replace($myreceivedprojectlist, $projects1);
			} else {
				$projects1 = $projects1;
			}

			$projects = array_map("strip_tags", $projects1);
			$projects = array_map("html_entity_decode", $projects1);
			$projects = array_map("html_entity_decode", $projects1);
			$projects = array_map(function($projects1) { return str_replace("'","",$projects1); }, $projects);
			$projects = array_map(function($projects1) { return str_replace('"',"",$projects1); }, $projects);
			//$projects = array_map(function($projects1) { return preg_replace('/[^A-Za-z0-9\-]/',"",$projects1); }, $projects);
			
			$projects = array_map("trim", $projects);
			
			natcasesort($projects);
			
			$this->set(compact('projects','project_id','project'));		
		//================================================================================
		
    }

    public function project_reports() {
        //$this->layout = false;
        $this->layout = 'doc';
        //$this->autoRender = false;
        $response = [
            'success' => false,
            'msg' => '',
            'content' => null
        ];

        $projects = $this->Common->get_user_project_list($this->user_id);
        $project_ids = array_keys($projects);
        if ($this->request->isAjax()) {


            if ($this->request->is('post') || $this->request->is('put')) {

                if (isset($this->data['ExportData']['project']) && !empty($this->data['ExportData']['project'])) {
                    $project_list = $this->data['ExportData']['project'];
                    $data = $this->Common->get_project_chain($project_list);

                    $response['success'] = true;
                } else {
                    $response['msg'] = 'Please select atleast one project.';
                }
            }
        }

        echo json_encode($response);
        exit();




        //pr($data);
    }

    public function save_doc() {
        $this->loadModel('Project');
        $user_id = $this->Auth->user('id');
 
		
        if ($this->request->is('post') || $this->request->is('put')) { 
			
			$title = 'Project Report'; 
			if( isset($this->data['ExportData']['title']) && !empty($this->data['ExportData']['title']) ){
				
				$title = html_entity_decode(strip_tags($this->data['ExportData']['title'])); 
				$title = str_replace("'", "", $title);
				$title = str_replace('"', "", $title);
				$title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
				 
			}      
            
            $document_title = $title;
            $is_show_doc_img = $this->data['ExportData']['DocumentImageOnFrontPage'];

            if(is_array($this->data['ExportData']['project'])){
                $project_list = $this->data['ExportData']['project'];
            }else{
                $project_list = array($this->data['ExportData']['project']);
            }
            
            $data = $this->Common->get_project_chain($project_list);
            
            $this->set(compact("data","document_title",'is_show_doc_img'));
        }

        $this->layout = 'generating_doc_file';
    }

    public function test() {
        $this->layout = false;
        $detail = $this->request->data;
		
        //$data = $this->Common->get_project_chain(array(2));

        if(!isset($this->data['ExportData']['project']) && empty($this->data['ExportData']['project']) ){
                die("Invalid Project Id test");
        }         
        $document_title = (isset($detail['ExportData']['title']) && !empty($detail['ExportData']['title'])) ? $detail['ExportData']['title'] : 'Project Report';            

        $is_show_doc_img = (isset($detail['ExportData']['DocumentImageOnFrontPage'])) ? $detail['ExportData']['DocumentImageOnFrontPage'] : 'N';
        $is_show_project_img = (isset($detail['ExportData']['ProjectImageOnFrontPage'])) ? $detail['ExportData']['ProjectImageOnFrontPage'] : 'N';

        if( isset($detail['ExportData']['project']) && is_array($detail['ExportData']['project'])){
            $project_list = $detail['ExportData']['project'];
        }else{
            $project_list = (isset($detail['ExportData']['project'])) ? array($detail['ExportData']['project']) : [];
        }
        $project_id = $detail['ExportData']['project'];

        $data = $this->Common->get_project_chain($project_list);

        $this->set(compact("data", "document_title",'is_show_doc_img','is_show_project_img','project_id'));

			 
    }
    public function word_doc() {
        $this->layout = false;
        $detail = $this->request->data;
		
        //$data = $this->Common->get_project_chain(array(2));
		
        if(!isset($this->data['ExportData']['project']) && empty($this->data['ExportData']['project']) ){
                die("Invalid Project Id test");
        }         
		$document_title = 'Project Report';
		if( (isset($detail['ExportData']['title']) && !empty($detail['ExportData']['title'])) ){
        
			$document_title =  htmlentities(strip_tags($detail['ExportData']['title']),ENT_QUOTES);
			//$document_title = str_replace("'", "", $document_title);
			//$document_title = str_replace('"', "", $document_title);
			//$document_title = preg_replace('/[^A-Za-z0-9\-]/', '', $document_title);
		
		}

        $is_show_doc_img = (isset($detail['ExportData']['DocumentImageOnFrontPage'])) ? $detail['ExportData']['DocumentImageOnFrontPage'] : 'N';
        $is_show_project_img = (isset($detail['ExportData']['ProjectImageOnFrontPage'])) ? $detail['ExportData']['ProjectImageOnFrontPage'] : 'N';

        if( isset($detail['ExportData']['project']) && is_array($detail['ExportData']['project'])){
            $project_list = $detail['ExportData']['project'];
        }else{
            $project_list = (isset($detail['ExportData']['project'])) ? array($detail['ExportData']['project']) : [];
        }
        $project_id = $detail['ExportData']['project'];

        $data = $this->Common->get_project_chain($project_list);
        $this->set(compact("data", "document_title",'is_show_doc_img','is_show_project_img','project_id'));

			 
    }
    public function word_doc1() {
        $this->layout = false;
        $detail = $this->request->data;
		
        //$data = $this->Common->get_project_chain(array(2));

        if(!isset($this->data['ExportData']['project']) && empty($this->data['ExportData']['project']) ){
                die("Invalid Project Id test");
        }         
        $document_title = (isset($detail['ExportData']['title']) && !empty($detail['ExportData']['title'])) ? $detail['ExportData']['title'] : 'Project Report';            

        $is_show_doc_img = (isset($detail['ExportData']['DocumentImageOnFrontPage'])) ? $detail['ExportData']['DocumentImageOnFrontPage'] : 'N';
        $is_show_project_img = (isset($detail['ExportData']['ProjectImageOnFrontPage'])) ? $detail['ExportData']['ProjectImageOnFrontPage'] : 'N';

        if( isset($detail['ExportData']['project']) && is_array($detail['ExportData']['project'])){
            $project_list = $detail['ExportData']['project'];
        }else{
            $project_list = (isset($detail['ExportData']['project'])) ? array($detail['ExportData']['project']) : [];
        }
        $project_id = $detail['ExportData']['project'];

        $data = $this->Common->get_project_chain($project_list);

        $this->set(compact("data", "document_title",'is_show_doc_img','is_show_project_img','project_id'));

			 
    }
    public function word_doc2() {
        $this->layout = false;
        $detail = $this->request->data;
		
        //$data = $this->Common->get_project_chain(array(2));

        if(!isset($this->data['ExportData']['project']) && empty($this->data['ExportData']['project']) ){
                die("Invalid Project Id test");
        }         
        $document_title = (isset($detail['ExportData']['title']) && !empty($detail['ExportData']['title'])) ? $detail['ExportData']['title'] : 'Project Report';            

        $is_show_doc_img = (isset($detail['ExportData']['DocumentImageOnFrontPage'])) ? $detail['ExportData']['DocumentImageOnFrontPage'] : 'N';
        $is_show_project_img = (isset($detail['ExportData']['ProjectImageOnFrontPage'])) ? $detail['ExportData']['ProjectImageOnFrontPage'] : 'N';

        if( isset($detail['ExportData']['project']) && is_array($detail['ExportData']['project'])){
            $project_list = $detail['ExportData']['project'];
        }else{
            $project_list = (isset($detail['ExportData']['project'])) ? array($detail['ExportData']['project']) : [];
        }
        $project_id = $detail['ExportData']['project'];

        $data = $this->Common->get_project_chain($project_list);

        $this->set(compact("data", "document_title",'is_show_doc_img','is_show_project_img','project_id'));

			 
    }

}
