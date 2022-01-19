<?php

App::uses('AppModel', 'Model');

/**
 * ThirdParty Model
 *
 */
  
class ThirdParty extends AppModel {    

    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Username is required'
            ),           
            'unique' => array(
                'rule' => array('isUnique', 'username'),
                'message' => 'This username has already been taken'
            )
        ), 
		'email' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Email is required'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Valid email is required',
            ),
            'unique' => array(
                'rule' => array('isUnique', 'email'),
                'message' => 'This email has already been taken'
            )
        ),
		'website' => array(
			'rule' => 'url',
			'required' => false,
            'allowEmpty' => true,
			'message' => 'Something went wrong with the website'
		),
		
		/* 'phone' => array(
			'loginRule-1' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'message' => 'Phone number should be numeric',
			),
			'loginRule-2' => array(
				'rule' => array('minLength', 10),
				'message' => 'Minimum length of 10 number'
			)	
		), */
		'profile_img' => array(
            'extension' => array(
               'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'jpg, jpeg, png, gif files',
            ),			
            'uploadProfileImage' => array(
                'rule' => array('uploadProfileImage'),
				'required' => false,
                'allowEmpty' => true,
                'message' => 'Error uploading file',
				'last' => true,
            )  
        )	
        
    );
	
	public function uploadProfileImage($check) {
	
        $key = key($check);
		
        $uploadData = array_shift($check);
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
        $fileName = time() . '.' . $ext['extension'];
		
	
		
        $uploadPath = THIRD_PARTY_USER_PATH . DS . $fileName;
        if (!file_exists(THIRD_PARTY_USER_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }
        
        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
			App::import('Component','Image');
			$image = new ImageComponent(new ComponentCollection);
			//$image->resize_image($uploadPath, '150', '150', $uploadPath);
            if (isset($this->data[$this->alias]['id'])) {
				 
				$this->unlinkProfileImage($key);
			  
            }
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }

        return false;
    }
	
	public function unlinkProfileImage($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
                @unlink(WWW_ROOT .THIRD_PARTY_USER_PATH . DS . $files[$this->alias][$key]);
        }
    }

}
