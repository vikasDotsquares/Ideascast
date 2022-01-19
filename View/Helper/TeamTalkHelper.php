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
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class TeamTalkHelper extends Helper {

    var $helpers = array('Html', 'Session', 'Thumbnail');
	
	public function getProjectWiki($project_id = null, $user_id = null) {

        App::import("Model", "Wiki");
        $ws = new Wiki();

        //$data = $ws->find('first', ['conditions' => ['Wiki.project_id' => $project_id, 'Wiki.user_id' => $user_id]]);        
        $data = $ws->find('first', ['conditions' => ['Wiki.project_id' => $project_id],'recursive'=>-1]);        
        return $data;
    }
	
	public function getWikiBlog($project_id = null, $user_id = null) {

        App::import("Model", "Blog");
        $ws = new Blog();
		//,'Blog.user_id'=>$user_id
        $data = $ws->find('count', ['conditions' => ['Blog.project_id'=>$project_id]]);        
        return $data;
    }
	
	public function getWikiBlogList($project_id = null, $user_id = null) {

        App::import("Model", "Blog");
        $ws = new Blog();
		//,'Blog.user_id'=>$user_id	
        $data = $ws->find('all', ['conditions' => ['Blog.project_id'=>$project_id],'order'=>'Blog.id DESC']);        
        return $data;
    }

	public function getLatestBlog($project_id = null,$user_id = null){
		App::import("Model", "Blog");
        $ws = new Blog();
		
        $data = $ws->find('first', ['conditions' => ['Blog.project_id'=>$project_id],'order'=>array('Blog.updated'=>'DESC'),'limit'=> 5]  );        
        return $data;
	}
	// Total counter of Blog
	public function getBlogCounter($project_id = null,$blog_id = null){
		//App::import("Model", "BlogLike");
        $ws = ClassRegistry::init('BlogLike');		
        $countLike = $ws->find('count', 
						array('conditions'=>
								array('BlogLike.project_id'=>$project_id,'BlogLike.blog_id'=>$blog_id)	
							) 
						);        
        return $countLike;
	}
	// How many count of every user for bolg
	public function userBlogLike($project_id = null,$blog_id = null,$user_id = null){
		
        $ws = ClassRegistry::init('BlogLike');		
        $countLike = $ws->find('count', 
						array('conditions'=>
								array('BlogLike.project_id'=>$project_id,'BlogLike.blog_id'=>$blog_id, 'BlogLike.user_id'=>$user_id)	
							) 
						);        
        return $countLike;
	}
	
	// Number of blogs added by Users
	public function getBlogUsers($project_id = null,$user_id = null){
		App::import("Model", "Blog");
         $ws = new Blog();
        $countBlog = $ws->find('all', 
						array('conditions'=>
								array('Blog.project_id'=>$project_id),
								'group'=>'Blog.user_id', 'order'=> 'Blog.id DESC'								
							) 
						);        
        return $countBlog;
	}
	
	// Number of blogs added by Users
	public function getBlogUsersWithName($project_id = null,$user_id = null){
		App::import("Model", "Blog");
		App::import("Model", "BlogComment");		
		
        $ws = new Blog();		
        $blc = new BlogComment();		
        		
		
		$data = $ws->find("all",array("conditions"=>array("Blog.project_id"=>$project_id), 'order'=>'Blog.id DESC'));	
		
		if(!empty($data)){
			
			$blog_ids = Set::extract('/Blog/id', $data);
			$blc->unBindModel(array('belongsTo' => array('Blog')));
			
			$bloguser = $blc->find("all",array("conditions"=>array("BlogComment.blog_id"=>$blog_ids),'group'=>'BlogComment.user_id', 'order'=>'BlogComment.id DESC'));
		}
		
			/* $bloguser = $ws->find('all',						
				array('conditions'=>
					array('Blog.project_id'=>$project_id),
					'group'=>'Blog.user_id', 'order'=> 'Blog.id DESC'								
				) 
			); */  
						
        return $bloguser;
	}
	
	
	// User Total Blog for specific project
	public function userTotalBlog($project_id = null,$user_id = null){
		App::import("Model", "Blog");
         $ws = new Blog();
        $countBlog = $ws->find('count', 
						array('conditions'=>
								array('Blog.project_id'=>$project_id,'Blog.user_id'=>$user_id)
							) 
						);        
        return $countBlog;
	}
	
	public function getWiki($project_id = null, $user_id = null) {

        App::import("Model", "Wiki");
        $ws = new Wiki();
		//,'Wiki.user_id'=>$user_id
        $data = $ws->find('count', ['conditions' => ['Wiki.project_id'=>$project_id]]);        
        return $data;
    }
	
	public function getWikiList($project_id = null, $user_id = null) {

        App::import("Model", "Wiki");
        $ws = new Wiki();
		//,'Wiki.user_id'=>$user_id
        $data = $ws->find('all', ['conditions' => ['Wiki.project_id'=>$project_id]]);        
        return $data;
    }

	public function getLatestWiki($project_id = null,$user_id = null){
		App::import("Model", "Wiki");
        $ws = new Wiki();
		//,'Wiki.user_id'=>$user_id
        $data = $ws->find('first', ['conditions' => ['Wiki.project_id'=>$project_id],'order'=>array('Wiki.updated'=>'DESC'),'limit'=> 5]  );        
        return $data;
	}
	
	// Total counter of Blog
	public function blog_comment_likes($comment_id = null,$project_id = null){
	
        $ws = ClassRegistry::init('CommentLike');		
        $countLike = $ws->find('count', 
						array('conditions'=>
								array('CommentLike.comment_id'=>$comment_id)	
							) 
						);        
        return $countLike;
	}
	
	public function comment_like_posted($user_id = null, $comment_id = null){
		
        $ws = ClassRegistry::init('CommentLike');		
        $countLike = $ws->find('count', 
						array('conditions'=>
								array('CommentLike.comment_id'=>$comment_id, 'CommentLike.user_id'=>$user_id)	
							) 
						);        
        return $countLike;
	}
	
	function user_blog_lists($blog_id = null, $project_id = null){		
		$ws = ClassRegistry::init('Blog');				
		$blog = $ws->find("all",array("conditions"=>array("Blog.project_id"=>$project_id), 'order'=>'Blog.id DESC'));		
		return $blog;
	}
	
	public function getCommentDocuments($comment_id = null){
		$ws = ClassRegistry::init('BlogDocument');				
		$comentdoc = $ws->find("all",array("conditions"=>array("BlogDocument.blog_comment_id"=>$comment_id), 'order'=>'BlogDocument.id DESC'));		
		return $comentdoc;
	}
	
	
	public function documentCounter($project_id = null, $blog_id = null, $user_id = null){
		$ws = ClassRegistry::init('BlogDocument');			
		$bs = ClassRegistry::init('Blog');				
		
		$data =0;
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$data = $ws->find("count",array(
						"conditions"=>array(								
								"BlogDocument.blog_id"=>$blog_id
							)
						));
			
		} else {
			
			$blogdata = $bs->find("all",array("conditions"=>array("Blog.project_id"=>$project_id)));		
			
			$blog_ids = Set::extract('/Blog/id', $blogdata);
			
			$data = $ws->find("count",array("conditions"=>array("BlogDocument.blog_id"=>$blog_ids)) );
		}	
		
		return $data;
	}
	
	public function userBlogDocumentCntPeople($blog_id = null){
		
		$ws = ClassRegistry::init('BlogDocument');
		$data = 0;
		$data = $ws->find("count",array("conditions"=>array(
		
						"BlogDocument.blog_id"=>$blog_id
					), "group" => "BlogDocument.user_id"
				)
			);
		return $data;
		
	}
	
	
	public function commentCounter($project_id = null, $blog_id = null, $user_id = null){
		$ws = ClassRegistry::init('BlogComment');
		$bs = ClassRegistry::init('Blog');			
		
		$data = 0;		
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$data = $ws->find("count",array("conditions"=>array("BlogComment.blog_id"=>$blog_id), 'order'=>'BlogComment.id DESC'));		
			
		} else {		
		
			$blogdata = $bs->find("all",array("conditions"=>array("Blog.project_id"=>$project_id), 'order'=>'Blog.id DESC'));
			
			if(!empty($blogdata)){
				$blog_ids = Set::extract('/Blog/id', $blogdata);
				$data = $ws->find("count",array("conditions"=>array("BlogComment.blog_id"=>$blog_ids), 'order'=>'BlogComment.id DESC'));
			}			
		}
		return $data;
	}
	
	public function commentCounterPeople($project_id = null, $blog_id = null){
		$ws = ClassRegistry::init('BlogComment');
		$bs = ClassRegistry::init('Blog');			
		
		$data = 0;		
		if( !empty($blog_id) ){
			
			$data = $ws->find("count",array("conditions"=>array("BlogComment.blog_id"=>$blog_id), 'group'=>"BlogComment.user_id", 'order'=>'BlogComment.id DESC'));		
			
		} 
		return $data;
	}
	
	public function totalCommentLike($project_id = null, $blog_id = null){
		
		$ws = ClassRegistry::init('CommentLike');
		$bs = ClassRegistry::init('BlogComment');
		$data = 0;
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$commentdata = $bs->find("all",array("conditions"=>array("BlogComment.blog_id"=>$blog_id)));
			
			if(!empty($commentdata)){
				$comment_ids = Set::extract('/BlogComment/id', $commentdata);
				$data = $ws->find("count",array("conditions"=>array("CommentLike.comment_id"=>$comment_ids)));
			}
			
		} 
		
		return $data;		
		
	}
	
	
	
	public function totalBlogCnt($project_id = null, $blog_id = null, $user_id = null){
		
		$ws = ClassRegistry::init('Blog');
		$data = 0;
		$data = $ws->find("count",array("conditions"=>array("Blog.project_id"=>$project_id)));
		return $data;
		
	}

	public function totalBlogViews($project_id = null, $blog_id = null, $user_id = null){
		
		$ws = ClassRegistry::init('BlogView');
		
		$data = 0;		
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$data = $ws->find("all",array("conditions"=>array("BlogView.project_id"=>$project_id,"BlogView.blog_id"=>$blog_id)));
			
			$cntViewsBlog = 0;
			foreach($data as $listCntBlogViews){
				$cntViewsBlog += $listCntBlogViews['BlogView']['bview'];
			}
			$data = $cntViewsBlog;
		} else {		
		
			$data = $ws->find("all",array("conditions"=>array("BlogView.project_id"=>$project_id)));			
		}
		return $data;		
		
	}
	
	public function totalBlogViewsPeople($project_id = null, $blog_id = null, $user_id = null){
		
		$ws = ClassRegistry::init('BlogView');
		
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$data = $ws->find("count",array("conditions"=>array("BlogView.project_id"=>$project_id,"BlogView.blog_id"=>$blog_id)));
			
		} 
		
		return $data;		
		
	}
	
	public function totalBlogLikePeople($project_id = null, $blog_id = null){
		
		$ws = ClassRegistry::init('BlogLike');
		
		if( !empty($project_id) &&  !empty($blog_id) ){
			
			$data = $ws->find("count",array("conditions"=>array("BlogLike.project_id"=>$project_id,"BlogLike.blog_id"=>$blog_id)));
			
		} 
		
		return $data;		
		
	}	
	
	public function getCommentTitle($comment_id = null){
		
		$ws = ClassRegistry::init('BlogComment');
		
		$data = $ws->find("first",array("conditions"=>array("BlogComment.id"=>$comment_id)));
		return $data;
		
	}
	
	public function userBlogCommentCnt($blog_id = null,$user_id = null){
		
		$ws = ClassRegistry::init('BlogComment');
		$data = 0;
		$data = $ws->find("count",array("conditions"=>array("BlogComment.blog_id"=>$blog_id, "BlogComment.user_id"=>$user_id)));
		return $data;
		
	}
	
	
	public function userBlogDocumentCnt($blog_id = null,$project_id=null,$user_id = null){
		
		$ws = ClassRegistry::init('BlogDocument');
		$data = 0;
		$data = $ws->find("count",array("conditions"=>array(
		
						"BlogDocument.blog_id"=>$blog_id, 
						"BlogDocument.user_id"=>$user_id
					)
				)
			);
		return $data;
		
	}
	
	
	public function blogUsers($blog_id = null){
		
		$data = $dataC = array(); 
		$bd = ClassRegistry::init('BlogDocument');
		$data = $bd->find("list",array('fields'=>array('BlogDocument.user_id'),"conditions"=>array("BlogDocument.blog_id"=>$blog_id,"BlogDocument.blog_comment_id"=>0 ),
		'group'=>array('BlogDocument.user_id')));
		
		$ws = ClassRegistry::init('BlogComment');		 
		$dataC = $ws->find("list",array('fields'=>array('BlogComment.user_id'),"conditions"=>array("BlogComment.blog_id"=>$blog_id  ),'group'=>array('BlogComment.user_id')  ));
		
		$all = array_unique(array_merge($data,$dataC));
		return $all;
		
	}
	
	public function blogCommentCount($blog_id = null, $user_id = null){
		$ws = ClassRegistry::init('BlogComment');
		$data = $ws->find("count",array("conditions"=>array("BlogComment.blog_id"=>$blog_id )));
		return $data;
	}
	
	public function blogDocumentCount($blog_id = null, $user_id = null){
		$bd = ClassRegistry::init('BlogDocument');		
		$data = $bd->find("count",array("conditions"=>array("BlogDocument.blog_id"=>$blog_id )));
		return $data;
	}
	
	
}
