<?php
App::uses('AppModel', 'Model');

class Template extends AppModel {
	
    var $name = 'Template';
    // var $hasOne = array ('Workspace');
    var $hasMany = array ('TemplateDetail' );
	
	
    /**
     * Get template_detail to create workspace template
	 * @param  int $template_id
	 *
     * @return mixed/empty $response
     */
	public function getWorkspace( $template_id ) {
		
		ClassRegistry::init('TemplateDetail');
		$response = null;
		
		if ( isset($template_id) && !empty($template_id)) { 
			 
			// GET ALL DISTINCT ROW NUMBERS
			$template_groups = $this->TemplateDetail->find('all', array(
									'fields' => 'DISTINCT row_no', 
									'conditions' => array(
										'TemplateDetail.template_id' => $template_id
									)
								));
			// echo $this->getLastQuery();die;
			// EXTRACT ALL ROW NUMBERS
			$grouped_ids = Set::extract($template_groups, '/TemplateDetail/row_no');
			
			// NOW GET GROUPED ROWS DATA INTO DIFFERENT ARRAY KEYS
			$template_detail = $this->TemplateDetail->find('all', array(
						'fields' => ['TemplateDetail.id', 'TemplateDetail.row_no', 'TemplateDetail.col_no', 'TemplateDetail.size_w', 'TemplateDetail.size_h'],
						'conditions' => array(
							'TemplateDetail.row_no' => $grouped_ids,
							'TemplateDetail.size_w >=' => 1,
							'TemplateDetail.size_h >=' => 1,
							'TemplateDetail.template_id' => $template_id
						)
					)
				);
			
			$templateRows = null;
			foreach( $template_detail as $k => $v ) {
				
				$row = $v['TemplateDetail'];
				if( $row['size_w'] > 0 && $row['size_h'] > 0 ) {
					$row_no = $row['row_no'];
					$templateRows[$row_no][] = $row;
				}
			}
			
			if( ! is_null($templateRows) ) { 
				$response = $templateRows;
			} 	
		}
		
		return $response;
	}
}