<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View', 'Auth', 'Session');
App::uses('Sanitize', 'Utility');
App::uses('CakeText', 'Utility');

App::import("Model", "ProjectSketch");
App::import("Model", "ProjectSketchParticipant");
App::import("Model", "ProjectSketchInterest");

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class SketchHelper extends Helper {

    protected $_sketch;
    protected $_participant;
	protected $_interest;

    public function __construct(View $View, $settings = array()) {

        parent::__construct($View, $settings);

        
		
        $this->ProjectSketch = ClassRegistry::init('ProjectSketch');
        $this->ProjectSketchParticipant = ClassRegistry::init('ProjectSketchParticipant');
        $this->ProjectSketchInterest = ClassRegistry::init('ProjectSketchInterest');
		
    }

    var $helpers = array('Html', 'Session', 'Text', "Common");

    public function getUnviewed() {
        $user_id = $this->Session->read("Auth.User.id");
        $joins = array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Project.id = ProjectSketchParticipant.project_id'
                    ),
                ),
		);		
        $count = $this->ProjectSketchParticipant->find("count",array(
            "conditions"=>array(
                "ProjectSketchParticipant.created_user_id  IS NULL",
                "ProjectSketchParticipant.user_id"=>$user_id,
                "ProjectSketchParticipant.is_read"=>0,
                 "Project.id !="=>'',
				
                ),
			 'joins' => $joins,'fields' => array('*')	
            )
        );
	//	pr($count,1);
        return $count;
    }
    public function getListUnviewed() {
        $user_id = $this->Session->read("Auth.User.id");
		
		$joins = array(
                array(
                    'table' => 'projects',
                    'alias' => 'Project',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Project.id = ProjectSketchParticipant.project_id'
                    ),
                ),
		);		
        
        $data = $this->ProjectSketchParticipant->find("all",array("conditions"=>array("ProjectSketchParticipant.created_user_id IS NULL","ProjectSketchParticipant.user_id"=>$user_id,"ProjectSketchParticipant.is_read"=>0,"Project.id !="=>'' ), 'joins' => $joins,'fields' => array('*')	));
        
		$sketch_id = isset($data) && !empty($data) ? $ids = Hash::extract($data, '{n}.ProjectSketchParticipant.project_sketch_id') : null;
        //pr($data);
	$sketchdata = $this->ProjectSketch->find("all",array("conditions"=>array("ProjectSketch.id"=>$sketch_id)));
        return $sketchdata;
    }
    public function getSaveasSketch($sketch_id = null){
        $sketchdata = $this->ProjectSketch->find("all",array("conditions"=>array("ProjectSketch.parent_id"=>$sketch_id)));
        return $sketchdata;
    }
    public function saveasCount($sketch_id = null){
        $sketchdata = $this->ProjectSketch->find("count",array("conditions"=>array("ProjectSketch.parent_id"=>$sketch_id)));
        return isset($sketchdata) && !empty($sketchdata) ? $sketchdata : 0;
    } 
	
	public function sktCounts($project_id = null){
        $sketchdata = $this->ProjectSketch->find("count",array("conditions"=>array("ProjectSketch.project_id"=>$project_id,"ProjectSketch.parent_id"=>0,"ProjectSketch.user_id"=>$this->Session->read("Auth.User.id"))));
		
		  $sketchdataN = $this->ProjectSketchParticipant->find("count",array(
		  	'joins' => array(
				array(
					'table' => 'project_sketches',
					'alias' => 'ProjectSketch',
					'type' => 'INNER',
					'conditions' => array(
						'ProjectSketch.id = ProjectSketchParticipant.project_sketch_id','ProjectSketch.project_id = ProjectSketchParticipant.project_id','ProjectSketch.parent_id = 0'
					),
				),
			),
		  "conditions"=>array("ProjectSketchParticipant.project_id"=>$project_id,"ProjectSketchParticipant.user_id"=> $this->Session->read("Auth.User.id"),"ProjectSketchParticipant.created_user_id"=>''
		  ),
		  'fields' => array('ProjectSketchParticipant.*', 'ProjectSketch.*'),
		  ));
		
		 
		 $sketchdata = $sketchdata + $sketchdataN;
		
        return isset($sketchdata) && !empty($sketchdata) ? $sketchdata : 0;
    }

    
}
