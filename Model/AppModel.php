<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	var $inserted_ids = array();
	
	// paginate and paginateCount implemented on a behavior.
	/* public function paginate( $model, $conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = 1, $extra = array()) {
		// method content
		echo 'paginate';
		pr($model, 1);
	} */
	/* 
	public function paginateCount( $model = null, $conditions = null, $recursive = 0, $extra = array()) {
		// method body
		// pr($conditions, 1);
		$conditions = (!is_null($conditions)) ? ['conditions' => $conditions] : null;
		$result = $this->find('count', [
					$conditions,
					$recursive,
					
				]);
		return $result;
		// 
	} */
		
	/**
	 * This method generates a slug from a title
		*
	 * @param  string $title The title or name
	 * @param  string $id The ID of the model
	 * @return string Slug
	 */
	public function generateSlug($title = null, $id = null, $field = 'slug') {
		if (!$title) {
			throw new NotFoundException(__('Invalid Title'));
		}
		
		$title = strtolower($title);
		$slug  = Inflector::slug($title, '-');
		
		$conditions = array();
		$conditions[$this->alias . '.'.$field] = $slug;
		
		if ($id) {
			$conditions[$this->primaryKey. ' NOT'] = $id;
		}
		
		$total = $this->find('count', array('conditions' => $conditions, 'recursive' => -1));
		if ($total > 0) {
			for ($number = 2; $number > 0; $number ++) {
				$conditions[$this->alias . '.'.$field] = $slug . '-' . $number;
				
				$total = $this->find('count', array('conditions' => $conditions, 'recursive' => -1));
				if ($total == 0) {
					return $slug . '-' . $number;
				}
			}
		}
		
		return $slug;
	}
	 
	function afterSave( $created, $options = array() ) {
        // pr($this->validationErrors); die;
		if( $created ) {
            $this->inserted_ids[] = $this->getInsertID();
        }
        return true;
    }
	
	
	public function _query( $last = true ) {
		// if( is_null($model) ) return;
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		
		$log = ( $last ) ? end($logs['log']) : $logs;
		
		debug($log);
		
		// return $lastLog['query'];
		// $log = $this->Project->getDataSource()->getLog(false, false);
		// 
	}
	
	
	
    public function uploadFileSlider($check) {

        $key = key($check);

        $uploadData = array_shift($check);

        $ext = pathinfo($uploadData['name']);

        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

        $uploadFolder = 'uploads' . DS . 'slider_images';
        $fileName = time() . '.' . $ext['extension'];
        $uploadPath = $uploadFolder . DS . $fileName;

        if (!file_exists($uploadFolder)) {
            $oldmask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkFileSlider($key);
            }

            $this->set('pdf_path', $fileName);
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }

        return false;
    }

    public function unlinkFileSlider($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
            @unlink(WWW_ROOT . 'uploads/slider_images/' . $files[$this->alias][$key]);
        }
    }
  
    public function uploadNewsImage($check) {

        $key = key($check);

        $uploadData = array_shift($check);

        $ext = pathinfo($uploadData['name']);

        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

        $fileName = time() . '.' . $ext['extension'];
        $uploadPath = NEWS_FILE_PATH . DS . $fileName;

        if (!file_exists(NEWS_FILE_PATH)) {
            $oldmask = umask(0);
            mkdir(NEWS_FILE_PATH, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkNewsImage($key);
            }

            $this->set('pdf_path', $fileName);
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }

        return false;
    }

    public function unlinkNewsImage($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
            @unlink(WWW_ROOT . NEWS_FILE_PATH . DS . $files[$this->alias][$key]);
        }
    }
 
        
    public function uploadAgencyLicense($check) {

        $key = key($check);

        $uploadData = array_shift($check);

        $ext = pathinfo($uploadData['name']);

        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

        $fileName = time() . '.' . $ext['extension'];
        $uploadPath = AGENCY_FILE_PATH .DS . $fileName;

        if (!file_exists(AGENCY_FILE_PATH)) {
            $oldmask = umask(0);
            mkdir(AGENCY_FILE_PATH, 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkAgencyLicense($key);
            }

            $this->set('pdf_path', $fileName);
            $this->data[$this->alias][$key] = $fileName;
            //pr($this->data);die;
            return true;
        }

        return false;
    }

    public function unlinkAgencyLicense($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
                @unlink(WWW_ROOT . AGENCY_FILE_PATH .DS. $files[$this->alias][$key]);
        }
    }
    
    public function uploadPassportScanCopy($check) {

        $key = key($check);

        $uploadData = array_shift($check);

        $ext = pathinfo($uploadData['name']);

        if ($uploadData['size'] == 0 || $uploadData['error'] !== 0) {
            return false;
        }

        
        $fileName = time() . '.' . $ext['extension'];
        $uploadPath = USER_PASSPORT_PATH . DS . $fileName;

        if (!file_exists(USER_PASSPORT_PATH )) {
            $oldmask = umask(0);
            mkdir(USER_PASSPORT_PATH , 0777);
            umask($oldmask);
        }

        if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
            if (isset($this->data[$this->alias]['id'])) {
                $this->unlinkPassportScanCopy($key);
            }
            
            $this->set('pdf_path', $fileName);
            $this->data[$this->alias][$key] = $fileName;
            return true;
        }

        return false;
    }

    public function unlinkPassportScanCopy($key) {
        if (isset($this->data[$this->alias]['id'])) {
            $files = $this->find('first', array('conditions' => array($this->alias . '.id' => $this->data[$this->alias]['id']), 'fields' => array($this->alias . "." . $key)));
            @unlink(WWW_ROOT . USER_PASSPORT_PATH . DS . $files[$this->alias][$key]);
        }
    }
	
	function unbindAll($params = array())
	{
		
		foreach($this->_associations as $ass)
		{
			if(!empty($this->{$ass}))
			{
				$this->__backAssociation[$ass] = $this->{$ass};
				if(isset($params[$ass]))
				{
					foreach($this->{$ass} as $model => $detail)
					{
						if(!in_array($model,$params[$ass]))
						{
							$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
							unset($this->{$ass}[$model]);
						}
					}
				}else
				{
					$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
					$this->{$ass} = array();
				}

			}
		}
		return true;
	}
 
    /* 
    protected function _findAll($state, $query, $results = array())
    {
        if ($state === 'before') {
            return $query;
        }
		
        if (isset($query['reformat']) && $query['reformat'] === true) {
            foreach ($results as &$_result) {
                reset($_result);
                $modelName = key($_result);
                $modelPart = array_shift($_result);
				
                if (!empty($query['filter']) && is_array($query['filter'])) {
                    $this->recursive_unset($_result, $query['filter']);
                }
				
                $_result = array(
				$modelName => array_merge($modelPart, $_result)
                );
            }
        }
		
        return $results;
    }
	
    public function getHabtmKeys() {
        $habtmKeys = array();
        // 1. level inspection
        foreach ($this->hasAndBelongsToMany as $name) {
            $habtmKeys[] = $name['with'];
        }
        // 2. level inspection
        $allAsssoc = array_merge(
		$this->belongsTo,
		$this->hasMany,
		$this->hasOne
        );
        foreach ($allAsssoc as $assocName => $assocVal) {
            foreach ($this->$assocName->hasAndBelongsToMany as $name) {
                $habtmKeys[] = $name['with'];
            }
        }
		
        return $habtmKeys;
    }
	
    private function recursive_unset(&$array, $keys_to_remove) {
        foreach ($keys_to_remove as $_key) {
            unset($array[$_key]);
        }
		
        foreach ($array as $key => &$val) {
            if (is_array($val)) {
                if (empty($val) || (!isset($val[0]) && empty($val['id']))) {
                    unset($array[$key]);
                } else {
                    $this->recursive_unset($val, $keys_to_remove);
                }
            }
        }
    } */

}
