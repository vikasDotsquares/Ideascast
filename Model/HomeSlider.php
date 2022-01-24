<?php
App::uses('AppModel', 'Model');
/**
 * HomeSlider Model
 *
 */
class HomeSlider extends AppModel {
	     
    public $validate = array(
        'slider_title' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Slider title is required'
				),
			),
            'slider_image' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Slider image is required',
					'on' => 'create',
				),	
            ),
			'slider_text' => array(
				'required' => array(
					'rule' => array(EMPTY_MSG),
					'message' => 'Slider text is required',
				),	
            ),
          
        
    );    
    
	
}