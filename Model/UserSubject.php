<?php

App::uses('AppModel', 'Model');

/**
 * User Model
 *
 */
class UserSubject extends AppModel {

    /**
     * Validation rules
     *
     * @var array
     */
    public $actsAs = array('Containable');
    public $useTable  = 'user_subjects';
    /**
    * @see Model::$actsAs
    */


    /**
     * @see Model::$belongsTo
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
      /*   'Skill' => array(
            'className' => 'Language',
            'foreignKey' => 'skill_id',
        ), */
    );

}
