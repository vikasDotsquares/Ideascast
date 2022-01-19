<div class="col-xs-12">	
    <section class="content-header clearfix">
        <div class="container" style="max-width: 100%">
            <div class="page-header" style=" margin:  0px 0">
                <h3 style="font-size : 17px; margin: 10px 0"></h3>	
            </div>
            <div class="col-md-3" style="display:none">
                <div class="row">
                    <label class="checkbox" style="display:none">
                        <input type="checkbox" checked=true value="#events-modal" id="events-in-modal"> Open events in modal window
                    </label>
                </div>

                <h4>Events</h4>
                <small>This list is populated with events dynamically</small>
                <ul id="eventlist" class="nav nav-list"></ul>
            </div>
        </div>
    </section>
</div>

<div class="col-xs-12">
    <section class="content-header clearfix">
        <div id="calendars">  </div>
    </section>
</div>

<div class="col-xs-12">	
    <section class="content-header clearfix">
        <div id="calendaraa"></div>
        <div id="events-modal" class="modal fade in modal-success">
            <div class="modal-dialog  modal-md modal-sm">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </section>
</div>	

<?php
include 'js_css.ctp';
?>
