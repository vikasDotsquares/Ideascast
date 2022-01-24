<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class Language extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    public $displayField = 'name';
    public $validate = array(
            'Language' => array(
                'multiple' => array(
                        'rule' => array('multiple', array('min' => 1)),
                        'message' => 'You need to select at least one language',
                        'required' => true,
                ),
            ),
        );

    

}
