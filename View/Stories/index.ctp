<style>

.content{
	padding-top: 0;
}
.ssd-data-row  .ssd-col-10 > a {
	display:none;
}
.ssd-data-row:hover .ssd-col-10 > a {
	display:block !important;
}
.no-scroll {
  overflow: hidden;
}
</style>
<script type="text/javascript">
    $(function(){
        $('html').addClass('no-scroll');
   })
</script>
<?php
    echo $this->Html->css('projects/stories');
	echo $this->Html->script('projects/stories', array('inline' => true));
?>

<div class="row">
   <div class="col-xs-12">
      <section class="main-heading-wrap">
         <div class="main-heading-sec">
            <h1><?php   echo $page_heading; ?></h1>
            <div class="subtitles"><?php echo $page_subheading; ?></div>
         </div>
         <div class="header-right-side-icon">
            <!-- <div class=""><a class="" href="#">icon 1</a></div>
            <div class=""><a class="" href="#">icon 2</a></div> -->
         </div>
      </section>
      <div class="box-content story-sec-tab">
         <div class="row ">
            <div class="col-xs-12">

				<div class="competencies-tab">
					<div class="row">
					<div class="col-md-9">
						<ul class="nav nav-tabs" id="stories_tabs">
							<li class="active">
								<a data-toggle="tab" data-type="story" class="active competencies_tab story-tab" data-target="#tab_story" href="#tab_story" aria-expanded="true">STORIES</a>
							</li>
						</ul>
						</div>
						<div class="col-md-3 right text-right">
							<div class="skill-link-top-right">

							 <a class="tipText filter-button common-btns" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'search_story', 'admin' => false)); ?>" data-area="" data-target="#story_search" data-toggle="modal"  data-original-title="Filter List"><i class="filter-black-icon"></i></a>

							<a class="tipText story-button common-btns" data-remote="<?php echo Router::url(array('controller' => 'stories', 'action' => 'add', 'story', 'admin' => false)); ?>" data-area="" data-target="#modal_create" data-toggle="modal"  data-original-title="Add Story"><i class="add-skill-icon"></i></a>

							</div>
							<div class="input-group search-skills-box">
                                <input type="text" class="form-control search-box" data-type="story" placeholder="Search for Stories...">
								<span class="input-group-btn" >
									<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
									<button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
								</span>
							</div>


						</div>
						</div>
					</div>

               <div class="box noborder">

        			<div id="box_body">
        				<div class="tab-content">
                            <div id="tab_story" class="tab-pane fade active in ssd-tabs">
                                <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                                <input type="hidden" name="paging_total" id="paging_total" value="0">
                                <div class="ssd-wrap">
                                    <div class="ssd-col-header">

                                        <div class="loc-col storie-col-1">
                                            <div class="storie-heading sort_order active" data-coloumn="name" data-order="desc" data-type="story">Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></div>

        								    <div class="storie-heading sort_order" data-coloumn="story_type"  data-order="" data-type="story">Type <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></div>

 										<div class="storie-heading sort_order" data-coloumn="total_people"  data-order="" data-type="story">People <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
										<div class="storie-heading sort_order" data-coloumn="total_organization"  data-order="" data-type="story">Organizations <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>

										<div class="storie-heading sort_order" data-coloumn="total_location"  data-order="" data-type="story">Locations <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
										<div class="storie-heading sort_order" data-coloumn="total_department"  data-order="" data-type="story">Departments <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
										<div class="storie-heading">Competencies
                                            <span class="h-name-one sort_order tipText" title="Sort By Skills" data-coloumn="total_skills"  data-order="" data-type="story"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
											<span class="h-name-one sort_order tipText" title="Sort By Subjects" data-coloumn="total_subjects"  data-order="" data-type="story"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
											<span class="h-name-one sort_order tipText" title="Sort By Domains" data-coloumn="total_domains"  data-order="" data-type="story"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                        </div>
										<div class="storie-heading sort_order" data-coloumn="total_story"  data-order="" data-type="story">Stories <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
											<div class="storie-heading sort_order" data-coloumn="total_link"  data-order="" data-type="story">Links <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
											<div class="storie-heading sort_order" data-coloumn="total_file"  data-order="" data-type="story">Files <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>

										<div class="storie-heading sort_order" data-coloumn="created_by"  data-order="" data-type="story">Created By <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
										<div class="storie-heading updated-head">Updated
                                            <span class="h-name-one sort_order  tipText" title="Sort By Updated By" data-coloumn="updated_by"  data-order="" data-type="story"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                            <span class="h-name-one sort_order tipText" title="Sort By Updated On" data-coloumn="modified"  data-order="" data-type="story"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                        </div>

                                        </div>
                                       <div class="loc-col storie-col-2">
                                            Actions
                                        </div>
                                    </div>
                                    <div class="ssd-data story-list-wrapper list-wrapper" data-type="story" data-target="#tab_organization" data-flag="true"></div>
                                </div>
                            </div>
        				</div>
        			</div>

               </div>
               <!-- /.box -->
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal modal-danger fade " id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<div class="modal modal-primary fade " id="modal_create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content add-skill-t add-skill-popup-cont"></div>
  </div>
</div>

<div class="modal modal-success fade filter-popup" id="story_search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog ">
		<div class="modal-content "></div>
	</div>
</div>


<div class="modal modal-danger fade " id="modal_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Delete Selected <span class="selected-type-text"></span></h3>
            </div>
            <div class="modal-body">Are you sure you want to delete all selected <span class="selected-type-text"></span>?
                <input type="hidden" name="selected_ids" id="selected_ids">
                <input type="hidden" name="selected_type" id="selected_type">
            </div>
            <div class="modal-footer clearfix">
                <button type="button" class="btn btn-success bulk-delete-confirm">Delete</button>
                <button type="button" id="discard" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function(){

   })
</script>

