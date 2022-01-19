<?php
App::uses('AppModel', 'Model');

class ElementDocument extends AppModel {
	
    var $name = 'ElementDocument';
	
    var $belongsTo = array (
			'Element'  => [ 'dependent' => true ]
		);
		
	var $validate = array(
		'element_id' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Reference with an element is required.'
			) 
		), 
		'title' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Title is required.'
			), 
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum length is 100 chars.',
			)
		), 
		
		'file_name' => array(
			'required' => array(
				'rule' => array(EMPTY_MSG),
				'message' => 'Document is required.'
			) 
		), 
		
		// 'file_name' => array(
			// 'required' => false, 
			// 'extension' => array(
				// 'rule' => array('extension', array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'xls','xlsx', 'ppt', 'pptx', 'txt', 'rtf' )),
				// 'required' => false,
				// 'allowEmpty' => true,
				// 'message' => 'jpg, jpeg, png, gif, doc, docx, xls, xlsx, ppt, pptx, txt, rtf files',
			// ),
			// 'uploadfile' => array(
				// 'rule' => array('uploadfile'),
				// 'message' => 'Error uploading file'
			// )
		// ) 
	); 
	
	
	public function uploadfile($check) {
        $key = key($check);
        $uploadData = array_shift($check);
        //$ext = pathinfo($uploadData['name']);
		
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
        
        //$fileName = time() . '.' . $ext['extension'];
        $fileName = $uploadData['name'];
        $uploadPath = DATA_FILEPATH . DS . $fileName;
		
        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkfile($key);
            }
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }
		
        return false;
    }
	
    public function unlinkfile($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
			@unlink(WWW_ROOT .DATA_FILEPATH . DS . $files[$this->alias][$key]);
        }
    }
}