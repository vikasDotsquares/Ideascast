<?php
App::uses('AppModel', 'Model');
/**
 * SearchListUser Model
 *
 */
class SearchListUser extends AppModel {
	
    var $name = 'SearchListUser';
	
	var $belongsTo = [ 'SearchList' ]; 
			
			
}