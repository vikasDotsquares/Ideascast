<?php
echo $this->Html->css('projects/smart_search', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->css('projects/tokenfield/bootstrap-tokenfield');
echo $this->Html->css('projects/dropdown');
echo $this->Html->css('projects/dd-menus');
echo $this->Html->css('projects/bs-selectbox/bootstrap-select');
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->script('projects/smart_search', array('inline' => true));
echo $this->Html->script('projects/plugins/dd-menus/dd-menus', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/tokenfield/bootstrap-tokenfield', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); 
 ?> 
<style>
.search_wrapper .left-container, .search_wrapper .right-container {
	min-height: 700px;
}
.searching-ac{
	min-height:662px;
}
.search_wrapper .right-container {
	border: 1px solid #ccc;
}

.search_wrapper .left-container .panel-green, .search_wrapper .left-container .panel-heading h5 {
	margin: 0;
}
.search_wrapper .left-container .panel-green.items {
	margin-bottom: 5px;
}
.toggle-search-items {
    color: #fff;
}
.toggle-search-items:hover, .toggle-search-items:focus {
    color: #fff;
}
.toggle-search-items[aria-expanded="true"] i.fa::before  {
	content: "";
}
.toggle-search-items[aria-expanded="false"] i.fa::before  {
	content: "";
}
.left-container ul.search-items {
    margin: 0;
    padding: 0;
}
.left-container ul.search-items li {
    color: #333;
    list-style-type: none;
    padding: 3px;
}
#project_accordion ul.search-items.first {
    border-bottom: 1px solid #ccc;
    margin: 0 0 10px;
    padding: 0 0 10px;
}
.ico_badge {
	background-repeat: no-repeat;
	background-size: 80% auto;
	cursor: pointer;
	display: inline-block;
	height: 20px;
	width: 23px;
	margin-bottom: -4px;
}
.result-item {
	padding: 15px 0 5px;
	border-bottom: 1px solid #ccc;
}
.ico_badge_blank {
	background-image: url("../images/icons/badge.png");
}
.ico_badge_user {
	background-image: url("../images/icons/badge-user.png");
}
.details-title-wrapper {
	display: block;
}
.details-title {
	font-size: 15px;
	font-weight: 600;
	color: #1A79CE;
}
.details-title .rows {
	padding-bottom: 4px;
}
.details-title .participate-link {
	font-size: 15px;
	font-weight: 600;
	color: #1A79CE;
}
.details-title .participate-link:hover {
	color: #67a028;
}
.find-in {
	font-size: 14px;
	font-weight: normal;
	color: #696969;
}
.find-in .type {
	font-weight: 600;
}
.highlighted {
	font-weight: 600;
	color: #cc0000;
}
@media (min-width:767px) and (max-width:991px){
.search_wrapper .left-container, .search_wrapper .right-container{
  min-height: inherit;
  margin-bottom:20px;
  padding:0px;
}
.searching-ac{min-height: inherit;}
}
@media (max-width:767px){
.search_wrapper .left-container, .search_wrapper .right-container{
  min-height: inherit;
  margin-bottom:20px;
  padding:0px;
}
.searching-ac{min-height: inherit;}
.sm-search{
	padding:10px 0px;
}
}



</style>
<script type="text/javascript">
$(function(){
	
})
</script>

<div class="row smart_search">
	<!-- Modal Large -->
	<div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<!-- /.modal -->
	
	<div class="col-xs-12">

		<div class="row">
		   <section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $page_heading; ?>
					<p class="text-muted date-time">
						<span style="text-transform: none;"><?php echo $page_subheading; ?></span>
					</p>
				</h1>
			</section>
		</div>

		<div class="box-content">
			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header" style="background: #f5f5f5 none repeat scroll 0 0; border-color: #d2d6de #d2d6de transparent; border-style: solid solid none; border-width: 1px 1px medium; cursor: move; padding: 10px 0 8px 5px;">
							<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
								<div class="radio-left">
									<div class="radio radio-warning">
										<input type="radio" data-target="#people_search" checked="checked" value="1" class="fancy_input change_checkbox" name="search_type" id="radio_people">
										<label for="radio_people" class="fancy_labels">People</label>
									</div>
									<div class="radio radio-warning">
										<input type="radio" data-target="#content_search" value="2" class="fancy_input change_checkbox" name="search_type" id="radio_content">
										<label for="radio_content" class="fancy_labels">Content</label>
									</div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-7 col-md-8 col-lg-9 sm-search">
								<div class="clearfix">
									<div class="col-sm-12 col-md-12 col-lg-6"> 
										<input type="text" class="form-control" id="inp_keyword" placeholder="Keyword">
									</div>
									<div class="col-sm-12 col-md-12 col-lg-6">
										<div class="radio-left">
											<div class="radio radio-warning">
												<input type="radio" data-target="#people_search" checked="checked" value="1" class="fancy_input" name="keyword_type" id="radio_exw_any">
												<label for="radio_exw_any" class="fancy_labels">Exact words, any order</label>
											</div>
											<div class="radio radio-warning">
												<input type="radio" value="2" class="fancy_input" name="keyword_type" id="radio_exw_exo">
												<label for="radio_exw_exo" class="fancy_labels">Exact words, exact order</label>
											</div>
											<div class="radio radio-warning">
												<input type="radio" value="2" class="fancy_input" name="keyword_type" id="radio_any">
												<label for="radio_any" class="fancy_labels">Any words</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-3 col-md-2 col-lg-1 text-right">
								<a class="btn btn-sm btn-success" href="#" id="search_type"><i class="fa fa-search"></i></a>
								<a class="btn btn-sm btn-danger" href="#" id="reset_search_type">Reset</a>
							</div>
						</div>
						<div class="box-body clearfix" >
							<div class="search_wrapper">
								<div class="col-sm-12 col-md-4 col-lg-3 left-container">
									<div class="panel panel-green ">
										<div class="panel-heading">
											<h5>Search Results</h5>
										</div>
										<div class="panel-body searching-ac" id="search_accordion">
										
											<div class="panel panel-green items">
												<div class="panel-heading">
													<h5>
														<a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#project_accordion">
															<i class="fa fa-minus"></i> Projects (4)
														</a>
													</h5>
												</div>
												<div class="panel-body collapse in" id="project_accordion">
													<ul class="search-items first">
														<li><a href="#" class="search-link">Tasks (2)</a></li>
														<li><a href="#" class="search-link">Workspaces (0)</a></li>
														<li><a href="#" class="search-link">Projects (1)</a></li>
													</ul>
													<ul class="search-items">
														<li><a href="#" class="search-link">Links (2)</a></li>
														<li><a href="#" class="search-link">Notes (0)</a></li>
														<li><a href="#" class="search-link">Documents (1)</a></li>
														<li><a href="#" class="search-link">Mind Maps (1)</a></li>
														<li><a href="#" class="search-link">Decisions (1)</a></li>
														<li><a href="#" class="search-link">Feedbacks (1)</a></li>
														<li><a href="#" class="search-link">Votes (1)</a></li>
														<li><a href="#" class="search-link">To-dos (1)</a></li>
														<li><a href="#" class="search-link">Sketches (1)</a></li>
													</ul>
												</div>
											</div>
											
											<div class="panel panel-green items" >
												<div class="panel-heading">
													<h5>
														<a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#teamtalk_accordion">
															<i class="fa fa-plus"></i> Team Talk (2)
														</a>
													</h5>
												</div>
												<div class="panel-body collapse" id="teamtalk_accordion">
													<ul class="search-items">
														<li><a href="#" class="search-link">Blogs (2)</a></li>
														<li><a href="#" class="search-link">Wikis (0)</a></li> 
													</ul>
												</div>
											</div>
											
											<div class="panel panel-green items" >
												<div class="panel-heading">
													<h5>
														<a class="toggle-search-items" data-toggle="collapse" data-parent="#search_accordion" href="#chat_accordion">
															<i class="fa fa-plus"></i> Chat (2)
														</a>
													</h5>
												</div>
												<div class="panel-body collapse" id="chat_accordion">
													<ul class="search-items">
														<li><a href="#" class="search-link">Messages (2)</a></li>
														<li><a href="#" class="search-link">Broadcasts (0)</a></li>
														<li><a href="#" class="search-link">Conversations (0)</a></li>
													</ul>
												</div>
											</div>
											
										</div>
									</div>
								</div>
								<div class="col-sm-12 col-md-8 col-lg-9 right-container panel panel-default no-padding">
									<div class="panel-heading">
										<h5 style="margin: 0px;">Search Details</h5>
									</div>
									
									<div class="panel-body" >
										<div class="result-item">
                                                                                    <img src="<?php echo SITEURL;?>uploads/user_images/1470912360.png" style="margin: 0px 10px 10px 0px;" width="40" align="left">
											<div class="details-title-wrapper">
												<div class="details-title">
													<div class="rows row-1">
														Project: <span class="ico_badge ico_badge_user"></span>
													</div>
													<div class="rows row-2">
														<a href="#" class="participate-link">New Market Evaluation</a>
													</div>
												</div>
											</div>
											<div class="details"> 
												<div class="find-in">
													Workspace <span class="type">Description</span>
												</div>
												<p>
													.....dolor sit amet, <b class="highlighted">consectetur</b> adipiscing elit.....<br />
													.....dolor sit amet, dolor sit <b class="highlighted">consectetur</b> amet, adipiscing elit.....
												</p> 
											</div>
										</div>
										<div class="result-item">
                                                                                    <img src="<?php echo SITEURL;?>uploads/user_images/1470912360.png" style="margin: 0px 10px 10px 0px;" width="40" align="left">
											<div class="details-title-wrapper">
												<div class="details-title">
													<div class="rows row-1">
														Project: <span class="ico_badge ico_badge_blank"></span>
													</div>
													<div class="rows row-2">
														New Market Evaluation
													</div>
												</div>
											</div>
											<div class="details"> 
												<div class="find-in">
													Workspace <span class="type">Key Result Target</span>
												</div>
												<p>
													.....'lorem ipsum' will uncover many web <b class="highlighted">consectetur</b> still in their infancy......<br />
													.....There are many variations of <b class="highlighted">consectetur</b> of Lorem Ipsum available.....
												</p> 
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

    </div>
</div>
