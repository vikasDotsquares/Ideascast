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
    echo $this->Html->css('projects/organizations');
	echo $this->Html->script('projects/organizations', array('inline' => true));
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
      <div class="box-content community-sec-tab">
         <div class="row ">
            <div class="col-xs-12">

				<div class="competencies-tab">
					<div class="row">
					<div class="col-md-9">
						<ul class="nav nav-tabs" id="organization_tabs">
							<li class="active">
								<a data-toggle="tab" data-type="org" class="active competencies_tab org-tab" data-target="#tab_org" href="#tab_org" aria-expanded="true">ORGANIZATIONS</a>
							</li>

							<li>
								<a data-toggle="tab" data-type="loc" class="competencies_tab loc-tab"  data-target="#tab_loc" href="#tab_loc" aria-expanded="false">LOCATIONS</a>
							</li>

                            <li>
                                <a data-toggle="tab" data-type="dept" class="competencies_tab dept-tab" data-target="#tab_dept" href="#tab_dept" aria-expanded="false">DEPARTMENTS</a>
                            </li>
						</ul>
						</div>
						<div class="col-md-3 right text-right">
						<?php
							if( $user_is_admin ){ ?>
							<div class="skill-link-top-right">

                                <a class="tipText org-button common-btns" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'add', 'org', 'admin' => false)); ?>" data-area="" data-target="#modal_create" data-toggle="modal"  data-original-title="Add Organization"><i class="add-skill-icon"></i></a>

    							<a class="tipText dept-button hide common-btns" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'add', 'dept', 'admin' => false)); ?>" data-target="#modal_create" data-toggle="modal" data-original-title="Add Department"><i class="add-skill-icon"></i></a>

    							<a class="tipText loc-button hide common-btns" data-remote="<?php echo Router::url(array('controller' => 'communities', 'action' => 'add', 'loc', 'admin' => false)); ?>" data-area="" data-target="#modal_create" data-toggle="modal" data-original-title="Add Location"><i class="add-skill-icon"></i></a>

							</div>
						<?php } ?>
							<div class="input-group search-skills-box">
                                <input type="text" class="form-control search-box" data-type="org" placeholder="Search for Organizations...">
                                <input type="text" class="form-control search-box" data-type="dept" placeholder="Search for Departments...">
                                <input type="text" class="form-control search-box" data-type="loc" placeholder="Search for Locations...">
								<span class="input-group-btn" >
									<button class="btn search-btn disabled" type="button"><i class="search-skill"></i></button>
									<button class="btn clear-btn" type="button"><i class="clearblackicon search-clear"></i></button>
								</span>
							</div>


						</div>
						</div>
					</div>

               <div class="box noborder">

				   <div class="modal modal-success fade" id="com_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog ">
                                <div class="modal-content"></div>
                            </div>
                        </div>

			<div id="box_body">
				<div class="tab-content">
                    <div id="tab_org" class="tab-pane fade active in ssd-tabs">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
                        <div class="ssd-wrap">
                            <div class="ssd-col-header">

                                <div class="loc-col org-col-1">
                                    <span class="h-name-one sort_order active" data-coloumn="name" data-order="desc" data-type="org">Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>

								    <span class="h-name-two sort_order" data-coloumn="org_type"  data-order="" data-type="org">Type <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>

                                </div>
                                <div class="loc-col org-col-2 sort_order" data-coloumn="totalpeople"  data-order="" data-type="org">People <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
								 <div class="loc-col org-col-3 sort_order" data-coloumn="total_location"  data-order="" data-type="org">Locations <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
								 <div class="loc-col org-col-4  " >Competencies
                                    <span class="com-short sort_order tipText" title="Sort By Skills" data-coloumn="totalskills" data-order="" data-type="org"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
									 <span class="com-short sort_order tipText" title="Sort By Subjects" data-coloumn="totalsubjects" data-order="" data-type="org"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
									 <span class="com-short sort_order tipText" title="Sort By Domains" data-coloumn="totaldomains" data-order="" data-type="org"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                </div>
								<div class="loc-col org-col-10 sort_order">
                                    Stories <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
								<div class="loc-col org-col-5 sort_order" data-coloumn="linktotal"  data-order="" data-type="org">Links <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
								<div class="loc-col org-col-6 sort_order" data-coloumn="filetotal"  data-order="" data-type="org">Files <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="loc-col org-col-7 "   >Updated
                                    <span class="com-short sort_order tipText" title="Sort By Updated By" data-coloumn="updated_by" data-order="asc" data-type="org"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
									<span class="com-short sort_order tipText" title="Sort By Updated On" data-coloumn="modified" data-order="asc" data-type="org"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
                                </div>
                                <!--<div class="loc-col org-col-8 sort_order" data-coloumn="modified"  data-order="asc" data-type="org">Updated On <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>-->
                                <div class="loc-col org-col-9">
                                    Actions
                                </div>
                            </div>
                            <div class="ssd-data org-list-wrapper list-wrapper" data-type="org" data-target="#tab_organization" data-flag="true"></div>
                        </div>
                    </div>

					<div id="tab_dept" class="tab-pane fade ssd-tabs">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
						<div class="ssd-wrap">
                            <div class="ssd-col-header">
                                <div class="ssd-col dep-col-1 sort_order active" data-coloumn="name" data-order="desc" data-type="dept">Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="ssd-col dep-col-2 sort_order" data-coloumn="totalpeople"  data-order="" data-type="dept">People <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="ssd-col dep-col-3">Competencies
									<span class="com-short sort_order tipText" title="Sort By Skills" data-coloumn="totalskills" data-order="" data-type="dept">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                    </span>
									<span class="com-short sort_order tipText" title="Sort By Subjects" data-coloumn="totalsubjects" data-order="" data-type="dept">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                    </span>
									<span class="com-short sort_order tipText" title="Sort By Domains" data-coloumn="totaldomains" data-order="" data-type="dept">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                        <i class="fa fa-sort-asc" aria-hidden="true"></i>
                                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                    </span>
                                </div>
									<div class="ssd-col dep-col-5 sort_order">
                                    Stories <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
								<div class="ssd-col dep-col-4">Updated
                                    <span class="com-short sort_order tipText" title="Sort By Updated By" data-coloumn="updated_by" data-order="asc" data-type="dept"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
									<span class="com-short sort_order tipText" title="Sort By Updated On" data-coloumn="modified" data-order="asc" data-type="dept"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
                                </div>




                                <!--<div class="ssd-col dep-col-5 sort_order" data-coloumn="modified"  data-order="asc" data-type="dept">Updated On <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>-->
                                <div class="ssd-col dep-col-6">
                                    Actions
                                </div>
                            </div>
							<div class="ssd-data dept-list-wrapper list-wrapper" data-type="dept" data-target="#tab_dept" data-flag="true"></div>
						</div>
					</div>

					<div id="tab_loc" class="tab-pane fade ssd-tabs loc-tabs-wrap">
                        <input type="hidden" name="paging_offset" id="paging_offset" value="1">
                        <input type="hidden" name="paging_total" id="paging_total" value="0">
						<div class="ssd-wrap">
							<div class="ssd-col-header">
                                <div class="loc-col loc-col-1">
                                    <span class="h-name-one sort_order active" data-coloumn="name" data-order="desc" data-type="loc">Name <span class="total-data">(0)</span> <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
									<span class="h-name-two sort_order" data-coloumn="countryName" data-order="" data-type="loc">Country <i class="fa fa-sort" aria-hidden="true"></i> <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                </div>
                                <div class="loc-col loc-col-2 sort_order" data-coloumn="type"  data-order="" data-type="loc">Type <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="loc-col loc-col-3 sort_order" data-coloumn="totalpeople"  data-order="" data-type="loc">People <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="loc-col loc-col-4 sort_order"  data-coloumn="totalorg"  data-order="" data-type="loc">Organizations <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>

                                </div>
                                <div class="loc-col loc-col-5 ">Competencies
                                    <span class="com-short sort_order tipText" title="Sort By Skills" data-coloumn="totalskills" data-order="" data-type="loc"> <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
									<span class="com-short sort_order tipText" title="Sort By Subjects" data-coloumn="totalsubjects" data-order="" data-type="loc"> <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
									<span class="com-short sort_order tipText" title="Sort By Domains" data-coloumn="totaldomains" data-order="" data-type="loc"> <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                                </div>
								<div class="loc-col loc-col-9 sort_order"> Stories <i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </div>
                                <div class="loc-col loc-col-6 sort_order" data-coloumn="linktotal"  data-order="" data-type="loc">Links <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>
                                <div class="loc-col loc-col-7 sort_order" data-coloumn="filetotal"  data-order="" data-type="loc">Files <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>

								<div class="loc-col loc-col-8">Updated
                                    <span class="com-short sort_order tipText" title="Sort By Updated By" data-coloumn="updated_by" data-order="asc" data-type="loc"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
									<span class="com-short sort_order tipText" title="Sort By Updated On" data-coloumn="modified" data-order="asc" data-type="loc"><i class="fa fa-sort" aria-hidden="true"></i>  <i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i> </span>
                                </div>


                               <!-- <div class="loc-col loc-col-9 sort_order" data-coloumn="updated_on"  data-order="asc" data-type="loc">Updated On <i class="fa fa-sort" aria-hidden="true"></i><i class="fa fa-sort-asc" aria-hidden="true"></i> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                </div>-->
                                <div class="loc-col loc-col-10">
                                    Actions
                                </div>
                            </div>
							<div class="ssd-data loc-list-wrapper list-wrapper" data-type="loc" data-target="#tab_loc" data-flag="true"> </div>
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

<div class="modal modal-success fade " id="modal_bulk_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
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




