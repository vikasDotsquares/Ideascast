<?php

App::uses('Controller/Component', 'Auth', 'Session', 'RequestHandler');
App::uses('CakeEmail', 'Network/Email');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

App::import('Lib', 'XmlApi');

use Cake\Network\Request;

class CategoriesController extends AppController {

     public $name = 'Categories';
     public $uses = array('Category', 'Project', 'Workspace', 'Area', 'Element');
     public $helpers = array('Html', 'Form', 'Session', 'Time', 'Text', 'Common');
     public $user_id = null;
     public $pagination = null;
     public $mongoDB = null;

    public function beforeFilter() {
	  parent::beforeFilter();

	  $this->set('controller', 'categories');

	  if ($this->request->is('ajax')) {
	       $this->response->disableCache();
	  }

	  $this->user_id = $this->Auth->user('id');

	  // Pagination
	  $this->pagination['limit'] = 4;
	  $this->pagination['show_summary'] = true;
	  $this->pagination['options'] = array(
	      'url' => array_merge(
		      array(
		  'controller' => $this->request->params['controller'],
		  'action' => 'get_more',
		      ), $this->request->params['pass'], $this->request->params['named']
	      )
	  );

	  $this->set('JeeraPaging', $this->pagination);
	  // Pagination
     }

    public function index($id = null) {

	  $this->layout = 'inner';
	  $this->set('title_for_layout', __("Category Organizer", true));
	  $this->set('page_heading', __("Category Organizer", true));

	  $this->redirect(array('controller' => 'categories', 'action' => 'manage_categories'));
     }

     public function mongo_create() {

     	echo 'sagar boss';

	  $this->layout = false;
	  $this->autoRender = false;
	  	$mongo = new MongoClient(MONGO_CONNECT);
	  	echo "Connection to database successfully";

		$this->mongoDB = $mongo->iphone;
		echo "Database mydb selected";

		$mongo_collection = new MongoCollection($this->mongoDB, 'projects');

		$collection = $mongo_collection;
   		echo "Collection selected succsessfully";
	
		   $document = array( 
		      "title" => "MongoDB", 
		      "description" => "database", 
		      "likes" => 100,
		      "url" => "http://www.tutorialspoint.com/mongodb/",
		      "by", "tutorials point"
		   );
	
   		$collection->insert($document);
   		echo "Document inserted successfully";

     }

	public function create_subdomain() {
				
			$cpanelusr = 'ideascast';
			$cpanelpass = '23WT0GgCq2*#b2';
			$xmlapi = new XmlApi('127.0.0.1', $cpanelusr, $cpanelpass);
			$xmlapi->set_port( 2083 );
			$xmlapi->password_auth($cpanelusr,$cpanelpass);
			$xmlapi->set_debug(1); 

			//output actions in the error log 1 for true and 0 false 
			$result = $xmlapi->api1_query($cpanelusr, 'SubDomain', 'addsubdomain', array('test_subdomain','domain.com',0,0, '/test_subdomain'));
			
			die;
			$domains = $xmlapi->listaccts('domain'); 
			pr($domains, 1);
		}

	public function create_db() {
				
			$cpanelusr = 'ideascast';
			$cpanelpass = '23WT0GgCq2*#b2';
			$xmlapi2 = new XmlApi('127.0.0.1', $cpanelusr, $cpanelpass);
			
			$xmlapi2->set_port( 2083 );
			$xmlapi2->password_auth($cpanelusr,$cpanelpass);
			$xmlapi2->set_debug(1); //output actions in the error log 1 for true and 0 false 
			
			//the actual $databasename and $databaseuser will contain the cpanel prefix for a particular account. Ex: prefix_dbname and prefix_dbuser
			$databasename = 'subdomain';
			$databaseuser = 'subdomain'; 
			//be careful this can only have a maximum of 7 characters
			$databasepass = '23WT0GgCq2*#b2';
			
			$createdb = $xmlapi2->api1_query($cpanelusr, "Mysql", "adddb", array($databasename)); //creates the database
			$usr = $xmlapi2->api1_query($cpanelusr, "Mysql", "adduser", array($databaseuser, $databasepass)); //creates the user
			$addusr = $xmlapi2->api1_query($cpanelusr, "Mysql", "adduserdb", array("".$cpanelusr."_".$databasename."", "".$cpanelusr."_".$databaseuser."", 'all')); 
			//gives all privileges to the newly created user on the new db
			
			die;
		}
	
	function create_subdomains() {
		
		$cPanelUser = 'ideascast';
		$cPanelPass = '23WT0GgCq2*#b2';
		$rootDomain = 'ideascast.com';
		$subDomain = 'mysub';
		
		$buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain . "&dir=" . $subDomain;
		
		$openSocket = fsockopen('localhost',2082);
		if(!$openSocket) {
			return "Socket error";
			exit();
		}
		
		$authString = $cPanelUser . ":" . $cPanelPass;
		$authPass = base64_encode($authString);
		$buildHeaders  = "GET " . $buildRequest ."\r\n";
		$buildHeaders .= "HTTP/1.0\r\n";
		$buildHeaders .= "Host:localhost\r\n";
		$buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
		$buildHeaders .= "\r\n";
		
		fputs($openSocket, $buildHeaders);
		while(!feof($openSocket)) {
			fgets($openSocket,128);
		}
		fclose($openSocket);
		
		echo $newDomain = "http://" . $subDomain . "." . $rootDomain . "/";
		
		//  return "Created subdomain $newDomain";
		die;
	} 
	
    public function manage_categories($id = null) {

	  $this->layout = 'inner';
	  $this->set('title_for_layout', __("Category Organizer", true));
	  $this->set('page_heading', __("Category Organizer", true));

	  $param = ( isset($id) && !empty($id)) ? $id : 0;
	  $this->set('param_id', $param);
	  $this->setJsVar('param_id', $param);
	  
	  if ($this->request->isAjax()) {
	       $this->layout = 'ajax';
	       $response = [
		   'success' => false,
		   'msg' => '',
		   'content' => null,
	       ];

	       $row = null;

	       if ($this->request->is('post') || $this->request->is('put')) {
		    $post = $this->request->data;
			
		    $this->Category->set($this->request->data['Category']);

		    if ($this->Category->validates()) {

			 $id = null;

			 if (isset($post['perform']) && !empty($post['perform'])) {
			      $perform = $post['perform'];
			      if ($perform == 'add_category' || $perform == 'add_sub_category') {

				   $this->request->data['Category']['user_id'] = $this->user_id;
				   if ($this->Category->save($this->request->data['Category'])) {

					if (isset($post['Category']['id']) && !empty($post['Category']['id'])) {
					     $id = $post['Category']['id'];
					} else {
					     $id = $insert_id = $this->Category->getLastInsertId();
					}

					$content = $this->Category->find('first', ['conditions' => ['Category.id' => $id]]);
					// pr($content, 1);
					$response['msg'] = "Category has been saved successfully.";
					$response['success'] = true;
					$response['content'] = $content;
				   } else {
					$response['msg'] = "Category could not be saved.";
				   }
			      } else if ($perform == 'remove_category') {
				   // Cannot delete because there are Projects in this Category path.
				   if (isset($post['Category']['id']) && !empty($post['Category']['id'])) {
					$this->Category->id = $post['Category']['id'];
					if ($this->Category->delete(['Category.deletable >' => 0])) {

					     if ($this->Category->deleteAll(['Category.parent_id' => $post['Category']['id']])) {
						  $response['msg'] = "Category children removed.";
						  $response['success'] = true;
					     }

					     $response['msg'] = "Category has been removed successfully.";
					     $response['success'] = true;
					     $response['content'] = null;
					} else {
					     echo 'not deletable';
					}
				   }
			      }
			 }
		    } else {
			 $errors = $this->validateErrors($this->Category);
			 $response['content'] = $errors;
		    }
	       }
	       echo json_encode($response);
	       exit();
	  }

	  $categories = $this->Category->find('threaded', array('recursive' => -1));

	  $this->set('categories', $categories);

	  $crumb = [ 'last' => [ 'Categories']];
	  $this->set('crumb', $crumb);
     }

     /*
      * @name  		update_category
      * @todo  		Update title of a category
      * @access		public
      * @package  	App/Controller/CategoriesController 
      * */

    public function update_category() {

		$this->layout = 'inner';


		if ($this->request->isAjax()) {
	       $this->layout = 'ajax';
			   $response = [
				   'success' => false,
				   'msg' => '',
				   'content' => null,
			   ];

	       $row = null;

	       if ($this->request->is('post') || $this->request->is('put')) {
		    $post = $this->request->data;
		    $this->Category->set($this->request->data['Category']);
		    if ($this->Category->validates()) {

			 $id = null;
			 if (isset($post['Category']['id']) && !empty($post['Category']['id'])) {
			      $id = $post['Category']['id'];

			      if ($this->Category->save($this->request->data['Category'])) {

				   $content = $this->Category->find('first', ['conditions' => ['Category.id' => $id]]);
				   // pr($content, 1);
				   $response['msg'] = "Category has been saved successfully.";
				   $response['success'] = true;
				   $response['content'] = $content;
			      } else {
				   $response['msg'] = "Category could not be saved.";
			      }
			 }
		    } else {
			 $errors = $this->validateErrors($this->Category);
			 $response['content'] = $errors;
		    }
	       }
	       echo json_encode($response);
	       exit();
	  }

	  $categories = $this->Category->find('threaded', array(
	      'fields' => array('id', 'title', 'parent_id')
	  ));
	  // $this->set(compact('categories'));

	  $this->set('categories', $categories);
     }

     /*
      * @name  		remove_category
      * @todo  		Remove a category
      * @access		public
      * @package  	App/Controller/CategoriesController 
      * */

    public function remove_category($id = null) {

	  // $this->layout = 'inner';


	  if ($this->request->isAjax()) {
	       $this->layout = 'ajax';
	       $response = [
		   'success' => false,
		   'msg' => '',
		   'content' => null,
	       ];

	       $row = null;

	       if ($this->request->is('post') || $this->request->is('put')) {

		    if (!empty($this->user_id)) {
			 $post = $this->request->data;
			 // pr($post, 1);
			 $category_post = $post['Category'];

			 $this->Category->set($this->request->data['Category']);

			 if ($this->Category->validates()) {

			      // $id = null;
			      if (isset($post['Category']['id']) && !empty($post['Category']['id'])) {
				   $id = ( $id === $post['Category']['id']) ? $id : 0;

				   /*
				     _findAllChildren(2, 0);
				     $category_id_str = '';

				     $category_session_ids = CakeSession::read("Category.ids");
				     if( isset($category_session_ids) && !empty($category_session_ids)) {
				     $category_ids = array_unique($category_session_ids);

				     // pr($category_ids, 1);

				     if( isset($category_ids) && !empty($category_ids)) {
				     $category_id_str = implode(',', $category_ids);
				     $total = category_projects($category_id_str);
				     // e($total);
				     }

				     CakeSession::write("Category.ids", "");


				     } */

				   // die;
				   if (!empty($id)) {
					$cdata = $this->Category->find('first', ['conditions' => ['Category.id' => $id], 'fields' => ['Category.id', 'Category.parent_id', 'Category.deletable'], 'recursive' => -1]);
					$parent_li = $cdata['Category']['parent_id'];


					$deletable = ( isset($cdata) && !empty($cdata) ) ? (
						(!empty($cdata['Category']['deletable']) && $cdata['Category']['deletable'] > 0 ) ? $cdata['Category']['deletable'] : 0
						) : 0;
					// e($deletable, 1);			
					if ($deletable > 0) {


					     $deleteAll = $this->Category->query("delete from categories where (id = '" . $id . "' OR parent_id = '" . $id . "') and user_id = '" . $this->user_id . "'");

					     if (is_array($deleteAll)) {

						  $remain_cat_count = $this->Category->find('count', ['conditions' => ['Category.parent_id' => $parent_li], 'recursive' => -1]);

						  // e($remain_cat_count, 1);
						  $response['msg'] = "Category has been deleted successfully.";
						  $response['success'] = true;
						  $response['content']['remain_cat_count'] = $remain_cat_count;
					     } else {
						  $response['msg'] = "Category could not be deleted.";
					     }
					} else {
					     $response['msg'] = "You are not authorized to delete this Category.";
					}
				   } else {
					$response['msg'] = "Invalid data.";
				   }
			      }
			 } else {
			      $errors = $this->validateErrors($this->Category);
			      $response['content'] = $errors;
			 }
		    }
	       }
	       echo json_encode($response);
	       exit();
	  }

	  $categories = $this->Category->find('threaded', array(
	      'fields' => array('id', 'title', 'parent_id')
	  ));
	  // $this->set(compact('categories'));
	  // pr($categories, 1);
	  $this->set('categories', $categories);
     }

     /*
      * @name  		get_projects
      * @todo  		Get all projects of a category
      * @access		public
      * @package  	App/Controller/CategoriesController 
      * */

    public function get_projects($category_id = null) {


	  if ($this->request->isAjax()) {

	       $this->layout = 'ajax';

	       $response = $row = null;

	       $rows = category_projects($category_id, false);
	       

	       $this->set('category_id', $category_id);
	       $this->set('rows', $rows);

	       $this->render(DS . 'Categories' . DS . 'partials' . DS . 'popover');
	  }
     }

}
