<?php

/**

 * AnnouncementS controller.

 *

 * This file will render views from views/announcementS/

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

 * @link          http://cakephp.org CakePHP(tm) Project

 * @package       app.Controller

 * @since         CakePHP(tm) v 0.2.9

 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)

 */

App::uses('AppController', 'Controller');

App::uses('CakeEmail', 'Network/Email');

/**

 * Static content controller

 *

 * Override this controller by placing a copy in controllers directory of an application

 *

 * @package       app.Controller

 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html

 */

class AnnouncementsController extends AppController {

/**

 * Controller name

 *

 * @var string

 */

	public $name = 'Announcements';

/**

 * Default helper

 *

 * @var array

 */

	public $helpers = array('Html', 'Session', 'Js', 'Wiki', 'Common');

	public $components = array('RequestHandler', 'Wiki');

	public function beforeFilter() {

		parent::beforeFilter();

		$this->Auth->allow('setnull', 'display', 'showblog', 'blogdetails', 'deletepostimage', 'unlinkBlogImage');

	}

	public function admin_index() {

		$orConditions = array();
		$andConditions = array();
		$finalConditions = array();

		$in = 0;
		$per_page_show = $this->Session->read('announcement.per_page_show');
		if (empty($per_page_show)) {
			$per_page_show = ADMIN_PAGING;
		}

		if (isset($this->data['Announcement']['keyword']) && !empty($this->data['Announcement']['keyword']) && strlen($this->data['Announcement']['keyword']) > 0) {
			$keyword = trim($this->data['Announcement']['keyword']);
		} else {
			$keyword = $this->Session->read('announcement.keyword');
		}

		if (isset($keyword) && strlen($keyword) > 0) {
			$this->Session->write('announcement.keyword', $keyword);
			$in = 1;
			$orConditions = array('OR' => array('Announcement.title LIKE' => '%' . $keyword . '%'));
		}

		if (isset($this->data['Announcement']['status'])) {
			$status = $this->data['Announcement']['status'];
		} else {
			$status = $this->Session->read('announcement.status');
		}

		if (isset($status)) {
			$this->Session->write('announcement.status', $status);
			if ($status != '') {
				$in = 1;
				$andConditions = array_merge($andConditions, array('Announcement.status' => $status));
			}
		}

		if (isset($this->data['Announcement']['per_page_show']) && !empty($this->data['Announcement']['per_page_show'])) {
			$per_page_show = $this->data['Announcement']['per_page_show'];
		}

		if (!empty($orConditions)) {
			$finalConditions = array_merge($finalConditions, $orConditions);
		}
		if (!empty($andConditions)) {
			$finalConditions = array_merge($finalConditions, array('AND' => $andConditions));
		}

		$count = $this->Announcement->find('count', array('conditions' => $finalConditions));
		$this->set('count', $count);

		$this->set('title_for_layout', __('All Announcement', true));
		$this->Session->write('announcement.per_page_show', $per_page_show);
		$this->Announcement->recursive = 0;
		$this->paginate = array('conditions' => $finalConditions, "limit" => $per_page_show, "order" => "Announcement.created DESC");
		$this->set('allannouncement', $this->paginate('Announcement'));
		$this->set('in', $in);
	}

	public function admin_add() {

		if ($this->request->is('post') || $this->request->is('put')) {

			//pr($this->request->data); die;

			$this->Announcement->create();
			if ($this->Announcement->save($this->request->data)) {

				$this->Session->setFlash(__('Announcement has been saved'), 'success');
				$this->redirect(array('action' => 'index'));

			} else {
				$this->Session->setFlash(__('Announcement could not be saved. Please, try again.'), 'error');
			}

		}
		$breadcrumb = array(
			array(
				'title' => 'Announcement Manager',
				'url' => Router::url(array('controller' => 'announcements', 'action' => 'index', 'admin' => true)),
			),
			array(
				'title' => 'Add Announcement',
			),
		);
		$this->set(compact('breadcrumb'));
	}

	public function admin_edit($id = null, $pageName = null) {

		$this->Announcement->id = $id;

		//check category exist

		if (!$this->Announcement->exists()) {

			$this->Session->setFlash(__('Invalid Announcement'), 'error');

			$this->redirect(array('action' => 'index'));

		}

		if ($this->request->is('post') || $this->request->is('put')) {

			//Set page as inactive if it is unchecked

			if (!array_key_exists('status', $this->request->data['Announcement'])) {

				$this->request->data['Announcement']['status'] = 0;

			}

			if (isset($this->request->data['Announcement']['announce_file']['name']) && empty($this->request->data['Announcement']['announce_file']['name'])) {
				unset($this->request->data['Announcement']['announce_file']);
			} else {

				//pr($this->request->data); die;

				$filesdetail = $this->Announcement->findById($this->request->data['Announcement']['id']);
				$oldImage = $filesdetail['Announcement']['announce_file'];

				if (file_exists(WWW_ROOT . ANNOUNCEMENT_FILE_PATH . $oldImage)) {
					@unlink(WWW_ROOT . ANNOUNCEMENT_FILE_PATH . $oldImage);
				}
			}

			if ($this->Announcement->save($this->request->data)) {

				$this->Session->setFlash(__('Announcement has been updated'), 'success');
				$this->redirect(array('action' => 'index'));

			} else {

				$this->Session->setFlash(__('Announcement could not be saved. Please, try again.'), 'error');
			}

		} else {

			$this->request->data = $this->Announcement->read(null, $id);
		}

		$breadcrumb = array(
			array(
				'title' => 'Announcement Manager',
				'url' => Router::url(array('controller' => 'announcements', 'action' => 'index', 'admin' => true)),
			),
			array(
				'title' => 'Edit Announcement',
			),
		);

		$this->set(compact('breadcrumb'));

	}

	public function admin_announcement_resetfilter() {
		$this->Session->write('announcement.keyword', '');
		$this->Session->write('announcement.status', '');

		$this->redirect(array('action' => 'index'));
	}

	public function admin_announcement_updatestatus() {
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->request->data['Announcement'] = $this->request->data;

			if ($this->Announcement->save($this->request->data)) {
				$this->Session->setFlash(__('Announcement status has been updated successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Announcement status could not updated successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_delete($id = null) {
		if (isset($this->data['id']) && !empty($this->data['id'])) {
			$id = $this->data['id'];
			$this->Announcement->id = $id;
			if (!$this->Announcement->exists()) {
				throw new NotFoundException(__('Invalid Announcement'), 'error');
			}
			if ($this->Announcement->delete()) {
				$this->Session->setFlash(__('Announcement has been deleted successfully.'), 'success');
				die('success');
			} else {
				$this->Session->setFlash(__('Announcement could not deleted successfully.'), 'error');
			}
		}
		die('error');
	}

	public function admin_update_status() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;

			$this->request->data['Announcement']['id'] = $this->data['id'];

			$this->request->data['Announcement']['status'] = $this->params['data']['status'];

			if ($this->Announcement->save($this->request->data)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function deleteAnnouncementfile() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$this->loadModel('Announcement');

			if (isset($this->request->data['announcementid']) && !empty($this->request->data['blogImg'])) {

				$this->request->data['Announcement']['id'] = $this->request->data['announcementid'];
				$this->request->data['Announcement']['blog_img_old'] = $this->request->data['blogImg'];
				$this->request->data['Announcement']['announce_file'] = null;

				unset($this->request->data['announcementid']);
				unset($this->request->data['blogImg']);

				//pr($this->request->data,1);
				if ($this->Announcement->save($this->request->data['Announcement'])) {

					unlink(WWW_ROOT . ANNOUNCEMENT_FILE_PATH . $this->request->data['Announcement']['blog_img_old']);

					$this->Session->setFlash(__('Announcement file has been deleted successfully.'), 'success');
					die('success');
				} else {
					$this->Session->setFlash(__('Announcement file could not deleted.'), 'error');
				}
			} else {
				$this->Session->setFlash(__('Announcement file could not deleted.'), 'error');
			}
		}
		die('error');
	}

	public function admin_downloads_doc($filename = null) {
		$this->autoRender = false;

		if (!empty($filename)) {

			$path = WWW_ROOT . ANNOUNCEMENT_FILE_PATH;
			$download_file = $path . $filename;

			if (file_exists($download_file)) {
				// Getting file extension.
				$extension = explode('.', $filename);
				$extension_tot = ( isset($extension) && !empty($extension) ) ? count($extension) - 1 :0;
				$extension = $extension[$extension_tot];
				// For Gecko browsers
				header('Content-Transfer-Encoding: binary');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
				// Supports for download resume
				header('Accept-Ranges: bytes');
				// Calculate File size
				header('Content-Length: ' . filesize($download_file));
				header('Content-Encoding: none');
				// Change the mime type if the file is not PDF
				header('Content-Type: application/' . $extension);
				// Make the browser display the Save As dialog
				header('Content-Disposition: attachment; filename=' . $filename);
				readfile($download_file);
				exit;
			} else {
				echo 'File does not exists on given path';
			}
		}
	}

}