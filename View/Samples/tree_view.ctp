<style type="text/css">
    /*Now the CSS*/ 
.tree {
    overflow: auto;
}
.tree ul {
    padding-top: 20px; 
    position: relative;
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
}

.tree li {
    float: left; text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;
    
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
}

/*We will use ::before and ::after to draw the connectors*/

.tree li::before, .tree li::after{
    content: '';
    position: absolute; top: 0; right: 50%;
    border-top: 1px solid #ccc;
    width: 50%; height: 20px;
}
.tree li::after{
    right: auto; left: 50%;
    border-left: 1px solid #ccc;
}

/*We need to remove left-right connectors from elements without 
any siblings*/
.tree li:only-child::after, .tree li:only-child::before {
    display: none;
}

/*Remove space from the top of single children*/
.tree li:only-child{ padding-top: 0;}

/*Remove left connector from first child and 
right connector from last child*/
.tree li:first-child::before, .tree li:last-child::after{
    border: 0 none;
}
/*Adding back the vertical connector to the last nodes*/
.tree li:last-child::before{
    border-right: 1px solid #ccc;
    border-radius: 0 5px 0 0;
    -webkit-border-radius: 0 5px 0 0;
    -moz-border-radius: 0 5px 0 0;
}
.tree li:first-child::after{
    border-radius: 5px 0 0 0;
    -webkit-border-radius: 5px 0 0 0;
    -moz-border-radius: 5px 0 0 0;
}

/*Time to add downward connectors from parents*/
.tree ul ul::before{
    content: '';
    position: absolute; top: 0; left: 50%;
    border-left: 1px solid #ccc;
    width: 0; height: 20px;
}

.tree li a{
    border: 1px solid #ccc;
    padding: 5px 10px;
    text-decoration: none;
    color: #666;
    font-family: arial, verdana, tahoma;
    font-size: 11px;
    display: inline-block;
    
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    
    transition: all 0.5s;
    -webkit-transition: all 0.5s;
    -moz-transition: all 0.5s;
}

/*Time for some hover effects*/
/*We will apply the hover effect the the lineage of the element also*/
.tree li a:hover, .tree li a:hover+ul li a {
    background: #e9453f;
    color: #fff;
    border: 1px solid #c52923;
}
/*Connector styles on hover*/
.tree li a:hover+ul li::after, 
.tree li a:hover+ul li::before, 
.tree li a:hover+ul::before, 
.tree li a:hover+ul ul::before{
    border-color:  #c52923;
}

/*Thats all. I hope you enjoyed it.
Thanks :)*/
</style>


<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    Samples
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;">Create & Check your sample pages here</span>
                    </p>
                </h1>
            </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xs">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                        </div>
                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">
                            <!--
We will create a family tree using just CSS(3)
The markup will be simple nested lists
-->
<div class="tree">
    <ul>
        <li>
            <a href="#">Parent</a>
            <ul>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                    <ul>
                        <li>
                            <a href="#">Child 1</a>
                            <!-- <ul>
                                <li>
                                    <a href="#">Child 1</a>
                                </li>
                                <li>
                                    <a href="#">Child 1</a>
                                </li>
                                <li>
                                    <a href="#">Child 1</a>
                                </li>
                            </ul> -->
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 1</a>
                </li>
                <li>
                    <a href="#">Child 2</a>
                    <ul>
                        <li>
                            <a href="#">Grand Child</a>
                            <ul>
                                <li>
                                    <a href="#">Grand Child</a>
                                </li>
                                <li>
                                    <a href="#">Grand Child</a>
                                </li>
                                <li>
                                    <a href="#">Grand Child</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">Child 3</a>
                    <ul>
                        <li><a href="#">Grand Child</a></li>
                        <li>
                            <a href="#">Grand Child</a>
                            <ul>
                                <li>
                                    <a href="#">Great Grand Child</a>
                                </li>
                                <li>
                                    <a href="#">Great Grand Child</a>
                                    <ul>
                                        <li>
                                            <a href="#">Great Great Grand Child</a>
                                        </li>
                                        <li>
                                            <a href="#">Great+ Grand Child</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">Great Grand Child</a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="#">Grand Child</a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div> 