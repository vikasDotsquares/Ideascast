<?php
App::uses('AppModel', 'Model');

class Announcement extends AppModel {

	public $validate = array(
		'first_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Announcement first name is required'
            ) 
        ),
		'last_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Announcement last name is required'
            ) 
        ),
		'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Announcement title is required'
            ), 
			'length' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Title must be no larger than 50 characters.'
			)
        ),				
		'announce_file' => array(
            'extension' => array(
               'rule' => array('extension', array('doc', 'docx', 'pdf', 'png','jpg','jpeg')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'File Type Should Be .docx or .pdf or .doc or png or jpg Or jpeg',
            ),
			 'size' => array(
				'rule'    => array('validateFileSize'),
				'required' => false,
                'allowEmpty' => true,
				'message' => 'Please supply a valid file size.',

			),
            'uploadAnnouncementFile' => array(
                'rule' => array('uploadAnnouncementFile'),
				'required' => false,
                'allowEmpty' => true,
                'message' => 'Error uploading file',
				'last' => true,
            )  
        )
		
	);
	
	public function validateFileSize($uploadimage){
		
		$size = $uploadimage['announce_file']['size'];
		
		if( !isset($size) && empty($size) && $size > 204800 ){			
			return false;
		} else { 
			return true;
		}
		
	}
	
	public function uploadAnnouncementFile($check) {
	
        $key = key($check);
		
        $uploadData = array_shift($check);
		
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
        //$fileName = time() . '.' . $ext['extension'];		
        $fileName = $uploadData['name'];		
		
        $uploadPath = ANNOUNCEMENT_FILE_PATH . $fileName;
        if (!file_exists(ANNOUNCEMENT_FILE_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }
        
        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {			
			App::import('Component','Image');			
			//$image = new ImageComponent(new ComponentCollection);					
            if (isset($this->data[$this->alias]['id'])) {
				 
				$this->unlinkUploadAnnouncementFile($key);
			  
            }
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }		
        return false;
    }
	
	public function unlinkUploadAnnouncementFile($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
                @unlink(WWW_ROOT .ANNOUNCEMENT_FILE_PATH . $files[$this->alias][$key]);
		}
    }
	
	
}