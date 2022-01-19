<?php

App::uses( 'AppModel', 'Model' );
class VoteQuestionOption extends AppModel {

	var $name = 'VoteQuestionOption';
	
	var $validate = array(
		'option_count' => array(
				'required' => array(
						'rule' => array(EMPTY_MSG),
						'message' => 'Vote Option Count is required.'
				),
		),
		'option' => array(
				'required' => array(
						'rule' => array('opitons'),
						'message' => 'Vote Option is required.'
				),
				'checkRange' => array(
						'rule' => array('checkRange'),
						'message'  => 'Please enter valid range.'
				),
				/* 'Alphanumeric' => array(
						'rule' => array('custom', '/^[a-zA-Z ]*$/i'),
						'message'  => 'characters with spaces only'
				), */
		)
	);
	
	function opitons(){
		Configure::write('debug', 0);
		if(isset($this->data['VoteQuestionOption']['option']) && !empty($this->data['VoteQuestionOption']['option'])){
			foreach($this->data['VoteQuestionOption']['option'] as $key => $val){ 
				foreach($val as $key1 => $value){
					if(empty($value) && $value != '0'){ return false; }
				}					
			}
		}
		return true;
	}
	function checkRange() {
		if(isset($this->data['VoteQuestionOption']['option']) && !empty($this->data['VoteQuestionOption']['option'])) {
			if(isset($this->data['VoteQuestionOption']['option'][2]) && !empty($this->data['VoteQuestionOption']['option'][2])){
				$minval = $this->data['VoteQuestionOption']['option'][2][0];
				$maxval = $this->data['VoteQuestionOption']['option'][2][1];
				
				if($minval != '' && $minval >= 0 && $maxval != '' && $maxval >=0) {
					if($minval < $maxval) {
						return true;
					}
				}
				return false;
			} else {
				return true;
			}
		}
	}	
}
