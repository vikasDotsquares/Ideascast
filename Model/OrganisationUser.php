<?php

App::uses('AppModel', 'Model');

/**
 * OrganisationUser Model
 *
 */
class OrganisationUser extends AppModel {

    var $name = 'OrganisationUser';
   /*  var $belongsTo = array(
        'User'
    ); */
	var $validate = array(
        'domain_name' => array(
            'required' => array(
                'rule' => array(EMPTY_MSG),
                'message' => 'Domain name is required'
            ),
			'notempty' => array(
                'rule' => EMPTY_MSG,
                'required' => false,
                'message' => 'Please enter Domain',
            ),
            'unique' => array(
                'rule' => array('isUnique', 'domain_name'),
                'message' => 'This domain has already been taken',
            //'on' => 'create'
            )
        )
	);	
}
