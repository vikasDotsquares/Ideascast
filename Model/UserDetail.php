<?php

App::uses('AppModel', 'Model');

App::uses('Sanitize', 'Utility');

/**
 * UserDetail Model
 *
 */

class UserDetail extends AppModel {

	// public $belongsTo = array( 'State');

	public $virtualFields = array(
		'full_name' => 'CONCAT(UserDetail.first_name, " ", UserDetail.last_name)'
	);

    public $validate = array(
        'first_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'First Name is required'
            ),
			'maxLength' => array(
                'rule' => array('maxLength', 40),
				'message' => 'First Name must be no larger than 40 characters long.'
            )
        ),
		/*'org_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Organization name is required'
            ),
			'maxLength' => array(
               'rule' => array('maxLength', 150),
				'message' => 'Organization name must be no larger than 150 characters long.'
            )
        ),*/
        /*'address' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Address is required'
            ),
			'maxLength' => array(
               'rule' => array('maxLength', 250),
				'message' => 'Address must be no larger than 250 characters long.'
            )
        ),*/
		/*'department_id' => array(
			'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Department is required'
            )
		),*/
		/*'city' => array(
			'rule' => array('maxLength', 20),
			'message' => 'City name must be no larger than 20 characters long.'
		),		 */
        'last_name' => array(

            'maxLength' => array(
               'rule' => array('maxLength', 40),
                'message' => 'Last Name must be no larger than 40 characters long.'
            ),
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Last Name is required'
            )


        ),
		/* 'contact' => array(

			'maxLength' => array(
               'rule' => array('maxLength', 50),
				'message' => 'Contact Number must be no larger than 50 characters long.'
            ),
			'phone' => array(
				'rule' => array('isValidUSPhoneFormat'),
				'message' => 'Enter valid contact number.'
			),


        ), */
		/*'zip' => array(
			//'rule' => array('maxLength', 20),
			'rule' => array('between', 5, 20),
			'message' => 'Postcode minimum length should be 5 and must be no larger than 20 characters long.'
        ),*/
/* 		'job_title' => array(
            'rule' => array('maxLength', 50),
            'message' => 'Job title must be no larger than 50 characters long.'
        ), */
		/*'job_role' => array(
			'rule' => array('maxLength', 250),
            'message' => 'Job role must be no larger than 250 characters long.'
        ),*/
		'linkedin_url' => array(
			'rule' => array('url', true),
            'message' => 'Must be a valid url.',
			'allowEmpty' => true
        ),
		/* 'alphaNumeric' => array(
                'rule' => 'alphaNumeric',
                'required' => true,
                'message' => 'Special Characters are not allowed.',
				'on' => 'create'
            ) ,

			'country_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Country is required'
            )
        ),

		'state_id' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'State is required'
            )
        ),

		'city' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'city is required'
            )
        ),

		'zip' => array(
            'required' => array(
				 'required' => false,
                'allowEmpty' => true,
                'rule' => 'numeric',
                'message' => 'Please fill Valid Zip code '
            )
        ),
		'profile_pic' => array(
            'extension' => array(
                'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'jpg, jpeg, png, gif files',
            ),
            'uploadProfileImage' => array(
                'rule' => array('uploadProfileImage'),
                'message' => 'Error uploading file'
            )
        ), */

		'profile_pic' => array(
            'extension' => array(
                'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'jpg, jpeg, png, gif files',
            ),
/* 			'profileimagesize' => array(
				 'rule' => array('profileimagesize'),
				 'message' => 'Please upload image with dimension (150px * 150px)'
			), */
            'uploadProfileImage' => array(
                'rule' => array('uploadProfileImage'),
                'message' => 'Error uploading file',
				'last' => true,
            )

        ),

		'menu_pic' => array(
            'extension' => array(
                'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'allowEmpty' => true,
                'required' => false,
                'message' => 'jpg, jpeg, png, gif files',
            ),
            'uploadProfileImagemenu' => array(
                'rule' => array('uploadProfileImagemenu'),
                'message' => 'Sign In Logo size exceeds the maximum allowed.',
				'last' => true,
            )
        ),


		'document_pic' => array(
            'extension' => array(
                'rule' => array('extension', array('jpeg', 'jpg','png','gif')),
                'required' => false,
                'allowEmpty' => true,
                'message' => 'jpg, jpeg, png, gif files',
            ),
            'uploadProfileImagedoc' => array(
                'rule' => array('uploadProfileImagedoc'),
                'message' => 'Error uploading file',
				'last' => true,
            )
        ),

		/* 'question' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Secret question is required'
            ),

        ),

		'answer' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Secret answer is required'
            ),

        ), */
    );
/*     public function afterFind($results, $primary = false) {
        App::uses('CakeTime', 'Utility');
        foreach ($results as $key => $val) {
            if (isset($val[$this->alias]['created'])) {
                $results[$key][$this->alias]['created'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created'], null, TIME_ZONE);
            }
        }
        return $results;
    } */

		public function afterFind($results, $primary = false) {
		App::uses('CakeTime', 'Utility');

			foreach ($results as $key => $val) {

				if (isset($val[$this->alias]['first_name'])) {
					$results[$key][$this->alias]['first_name'] = html_entity_decode(html_entity_decode($val[$this->alias]['first_name'] ,ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['last_name'])) {
					$results[$key][$this->alias]['last_name'] = html_entity_decode(html_entity_decode($val[$this->alias]['last_name'], ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['job_title'])) {
					$results[$key][$this->alias]['job_title'] = html_entity_decode(html_entity_decode($val[$this->alias]['job_title'], ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['address'])) {
					$results[$key][$this->alias]['address'] = html_entity_decode(html_entity_decode($val[$this->alias]['address'], ENT_QUOTES, "UTF-8"));
				}
				if (isset($val[$this->alias]['org_name'])) {
					$results[$key][$this->alias]['org_name'] = html_entity_decode(html_entity_decode($val[$this->alias]['org_name'], ENT_QUOTES, "UTF-8"));
				}

				if (isset($val[$this->alias]['bio'])) {
					$results[$key][$this->alias]['bio'] = html_entity_decode(html_entity_decode($val[$this->alias]['bio'], ENT_QUOTES, "UTF-8"));
				}



			}

			 //$results = Sanitize::clean($results, array('encode' => true));

/* 			foreach ($results as $key => $val) {
				if (isset($val[$this->alias]['created_at'])) {
					$results[$key][$this->alias]['created_at'] = CakeTime::format(ADMIN_DATE_FORMAT, $val[$this->alias]['created_at'], null, TIME_ZONE);
				}
			}
 */
			return $results;


	}


	/*isValidUSPhoneFormat() - Custom method to validate US Phone Number
	 * @params Int $phone
	 */
	 function isValidUSPhoneFormat($phone){

	 $phone_no=$phone['contact'];
	 $errors = array();

		/*if(empty($phone_no)) {
			$errors [] = "Contact number is required";
		}
		else */if (!preg_match('/^(\(?\+?[0-9]*\)?)?[0-9_\-. \(\)]*$/', $phone_no)) {
			$errors [] = "Please enter valid Contact Number";
		}

		if (!empty($errors))
		return implode("\n", $errors);

		return true;
	}

	public function profileimagesize($check) {

        $key = key($check);
        $uploadData = array_shift($check);
        $ext = pathinfo($uploadData['name']);

		list($width, $height) = getimagesize($uploadData['tmp_name']);

		if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

		if( $width > 150 && $height > 150 ){
			return false;
		} else if( $width > 150  ){
			return false;
		} else if( $height > 150 ){
			return false;
		} else {
			return true;
		}
		return false;

	}

	public function uploadProfileImage($check) {

        $key = key($check);



        $uploadData = array_shift($check);

		if(is_array($uploadData)){
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);

		//list($width, $height) = getimagesize($uploadData['tmp_name']);


        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
		//Code starts for check file MIME type
		$file_mime_type = mime_content_type($uploadData['tmp_name']);

		$allowFileExts = ['.jpg', '.jpeg', '.png', '.gif'];
		$disAllowFileExts = ['.exe', '.msi', '.js', '.cgi', '.htaccess'];

		$allowFileTypes = ['image/jpeg', 'image/pjpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif'];
		$disAllowFileTypes = ['application/octet-stream', 'application/x-msdownload', 'application/exe', 'application/x-exe', 'application/dos-exe',
                                  'vms/exe', 'application/x-winexe', 'application/msdos-windows', 'application/x-msdos-program', 'application/x-javascript', 'application/x-dosexec',
                                  'application/javascript'];

		if( in_array( '.' . strtolower($ext['extension']), $disAllowFileExts) || (in_array($file_mime_type, $disAllowFileTypes)) ){
			return false;
		} else if(!(in_array( '.' . strtolower($ext['extension']), $allowFileExts) === true && in_array( $file_mime_type, $allowFileTypes) === true )){
			return false;
		}
		//Code ends for check file MIME type

		/* if( $width > 150 && $height > 150 ){
			return false;
		} else if( $width > 150  ){
			return false;
		} else if( $height > 150 ){
			return false;
		}	 */


        $fileName = time() . '.' . $ext['extension'];



        $uploadPath = USER_PIC_PATH . DS . $fileName;
        if (!file_exists(USER_PIC_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
			App::import('Component','Image');
			$image = new ImageComponent(new ComponentCollection);
			$image->resize_image($uploadPath, '150', '150', $uploadPath);
            if (isset($this->data[$this->alias]['id'])) {

				$this->unlinkProfileImage($key);

            }
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }
		}else{
			return true;
		}

        return false;
    }

	// public function afterValidate($options = null) {
		// pr($options , 1);
	// }

		public function uploadProfileImagedoc($check) {


        $key = key($check);
        $uploadData = array_shift($check);
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

		//Code starts for check file MIME type
		$file_mime_type = mime_content_type($uploadData['tmp_name']);

		$allowFileExts = ['.jpg', '.jpeg', '.png', '.gif'];
		$disAllowFileExts = ['.exe', '.msi', '.js', '.cgi', '.htaccess'];

		$allowFileTypes = ['image/jpeg', 'image/pjpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif'];
		$disAllowFileTypes = ['application/octet-stream', 'application/x-msdownload', 'application/exe', 'application/x-exe', 'application/dos-exe',
                                  'vms/exe', 'application/x-winexe', 'application/msdos-windows', 'application/x-msdos-program', 'application/x-javascript', 'application/x-dosexec',
                                  'application/javascript'];

		if( in_array( '.' . strtolower($ext['extension']), $disAllowFileExts) || (in_array($file_mime_type, $disAllowFileTypes)) ){
			return false;
		} else if(!(in_array( '.' . strtolower($ext['extension']), $allowFileExts) === true && in_array( $file_mime_type, $allowFileTypes) === true )){
			return false;
		}
		//Code ends for check file MIME type

        $fileName2 = time() . '_doc.' . $ext['extension'];



        $uploadPath = USER_PIC_PATH . DS . $fileName2;
        if (!file_exists(USER_PIC_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
			App::import('Component','Image');
			$image = new ImageComponent(new ComponentCollection);
			$image->resize_image($uploadPath, '230', '74', $uploadPath);
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkProfileImage($key);
            }
			//pr($key); die;
            $this->data[$this->alias][$key] = $fileName2;
            return true;
        }

        return false;
    }

	public function uploadProfileImagemenu($check) {


        $key = key($check);
        $uploadData = array_shift($check);
		//$this->data[$this->alias][$key] = '';
        $ext = pathinfo($uploadData['name']);
        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }
        $fileName3 = time() . '_menu.'  . $ext['extension'];

		$image_info = getimagesize($uploadData["tmp_name"]);
		$image_width = $image_info[0];
		$image_height = $image_info[1];
        $uploadPath = USER_PIC_PATH . DS . $fileName3;


	    if($image_width > 640 || $image_height >150 ){

			 return false;
		}

        if (!file_exists(USER_PIC_PATH)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
			App::import('Component','Image');
			$image = new ImageComponent(new ComponentCollection);
			//$image->resize_image($uploadPath,'320', '75', $uploadPath);
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkProfileImage($key);
            }
			//pr($key); die;
            $this->data[$this->alias][$key] = $fileName3;
            return true;
        }

        return false;
    }

    public function unlinkProfileImage($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
                @unlink(WWW_ROOT .USER_PIC_PATH . DS . $files[$this->alias][$key]);
        }
    }


	    function beforeValidate($options = array()) {

        if (isset($this->data[$this->alias]["profile_pic"]["name"]) && $this->data[$this->alias]["profile_pic"]["name"] == '') {
            unset($this->data[$this->alias]["profile_pic"]);
            //unset($this->data[$this->alias]["is_profile_pic"]);
        }

		if (isset($this->data[$this->alias]["menu_pic"]["name"]) && $this->data[$this->alias]["menu_pic"]["name"] == '') {
            unset($this->data[$this->alias]["menu_pic"]);
            //unset($this->data[$this->alias]["is_profile_pic"]);
        }


		if (isset($this->data[$this->alias]["document_pic"]["name"]) && $this->data[$this->alias]["document_pic"]["name"] == '') {
            unset($this->data[$this->alias]["document_pic"]);
            //unset($this->data[$this->alias]["is_profile_pic"]);
        }



        return true; //this is required, otherwise validation will always fail
    }

	//public function beforeSave($options = array()){
	//pr($this->data); die("dfgfgfg");
	//}


	public function beforeSave($options = array()) {


 		foreach($this->data[$this->alias] as $k => $v) {
          // $c = preg_replace('@<script[^>]*?.*?</script>@siu', '', $v);
		  // $c = preg_replace('@<script>.*?@siu', '', $c);
		   $c = htmlspecialchars( $v);
           $this->data[$this->alias][$k] = $c;

        }
        // $this->data = Sanitize::clean($this->data, array('encode' => true));

        return true;
    }

    /* function beforeValidate($options = array()) {

        if (isset($this->data[$this->alias]["profile_pic"]["name"]) && $this->data[$this->alias]["profile_pic"]["name"] == '') {
            unset($this->data[$this->alias]["profile_pic"]);
        }
        return true; //this is required, otherwise validation will always fail
    } */




}
