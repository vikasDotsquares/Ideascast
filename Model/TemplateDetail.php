<?php
App::uses('AppModel', 'Model');

class TemplateDetail extends AppModel {
	
    var $name = 'TemplateDetail'; 
	
	var $belongsTo = array ( 'Template' );
}