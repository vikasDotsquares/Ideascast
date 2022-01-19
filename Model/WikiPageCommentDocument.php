<?php

App::uses('AppModel', 'Model');

class WikiPageCommentDocument extends AppModel {
    
   public $useTable = "wiki_page_comment_documents";
    
   
    public $hasOne = array(
    'WikiPage' => array(
        'className'     => 'WikiPage',
        'foreignKey'    => false,
        'conditions' => array('WikiPageCommentDocument.wiki_page_id = WikiPage.id')  
    ));
   
     

}
