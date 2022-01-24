<?php
App::uses('AppModel', 'Model');

class Post extends AppModel {

	public $validate = array(
		'title' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Blog title is required'
            ), 
			'length' => array(
				'rule' => array('maxLength', 150),
				'message' => 'Title must be no larger than 150 characters.'
			)
        ),
		'description' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Blog description is required'
            ) 
        ),
		'slug' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'This is required'
            ),             
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'Please use different title ,it has already been taken',
            //'on' => 'create'
            )
        ),
		'blog_img' => array(
            'extension' => array(
               'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'jpg, jpeg, png, gif files',
            ),
			  'size' => array(
				'rule'    => array('validateImageSize'),
				'required' => false,
                'allowEmpty' => true,
				'message' => 'Please supply a valid image size.',

			),
            'uploadBlogImage' => array(
                'rule' => array('uploadBlogImage'),
				'required' => false,
                'allowEmpty' => true,
                'message' => 'Error uploading file',
				'last' => true,
            )  
        )
		
	);
	
	public function validateImageSize($uploadimage){
		
		list($width, $height) = getimagesize($uploadimage['blog_img']['tmp_name']);	

		if($width < 570 && $height < 150 ){			
			return false;
		} else {
		 
			return true;
		}
		
	}
	
	public function uploadBlogImage($check) {
	
        $key = key($check);
		
        $uploadData = array_shift($check);
		
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
        $fileName = time() . '.' . $ext['extension'];		
		
        $uploadPath = POST_PIC_PATH . $fileName;
        $resizeuploadPath = POST_RESIZE_PIC_PATH . $fileName;		
		//echo $uploadPath; 		
        if (!file_exists(POST_PIC_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }
        
        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {			
			App::import('Component','Image');			
			$image = new ImageComponent(new ComponentCollection);
			$image->resize_image($uploadPath, '100', '100', $resizeuploadPath);			
            if (isset($this->data[$this->alias]['id'])) {
				 
				$this->unlinkUploadBlogImage($key);
			  
            }
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }		
        return false;
    }
	
	public function unlinkUploadBlogImage($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
                @unlink(WWW_ROOT .POST_PIC_PATH . $files[$this->alias][$key]);
                @unlink(WWW_ROOT .POST_RESIZE_PIC_PATH . $files[$this->alias][$key]);
        }
    }
	
	
}