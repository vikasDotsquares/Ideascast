<div class="col-sm-8 col-md-8 col-lg-9 wiki-right-section">
    <div class="tabContentLeft">
        <?php 
        $allWikiPages = $this->Wiki->getWikiPageLists($project_id, $this->Session->read('Auth.User.id'), $wiki_id);


        include 'wiki_page_list.ctp';
        ?>
    </div>
    
</div>
<div class="col-sm-4 col-md-4 col-lg-3 wiki-left-section">
    <?php
    echo $this->element('../Wikies/partials/wiki_dashboard/wiki_all_users', array("project_id" => $project_id, "user_id" => $this->Session->read('Auth.User.id'),"wiki_id" => $wiki_id, "wiki_page_id" => null ));
    ?>
</div>
<style>

    .table-fixed thead {
        width: 100%;
    }
    .table-fixed tbody {
        height: 352px;
        overflow-y: auto;
        width: 100%;
    }
    .table-fixed thead, .table-fixed tbody, .table-fixed tr, .table-fixed td, .table-fixed th {
        display: block;
    }
    .table-fixed tbody td, .table-fixed thead > tr> th {
        float: left;
        border-bottom-width: 0;
    }
</style>

<style>
#comment_doc_list{
	margin-top:14px;
}
.text-label {
  background: #367fa9 none repeat scroll 0 0;
  color: #ffffff;
  cursor: pointer;
  display: block;
  margin: 0 0 4px;
  padding: 5px;
  width: 100%;
}

.blog-comment-lists li .comment-people-info > p > span {
  display: inline-block;
  width: 100%;
  margin: 3px 0;
}

.blog-comment-lists li .comment-people-info{
	  margin: 3px 0;
}
.idea-blog-list li p {
    color: #333;
    font-size: 13px;
}

.comment-people-info .created-date{ clear:both; margin: 2px 0 6px 0; }

#comment_doc_list .dolist-document{clear:both; float:left; margin:0 0 10px;}

.idea-blog-list li {
  border-bottom: 1px solid #ccc;
  display: inline-block;
  width: 100%;
  padding: 9px 0;
}

.idea-blog-list{ max-height:600px; overflow-y:auto;}
</style>
<script type="text/javascript" >
 $(function(){
	$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
})	
</script>