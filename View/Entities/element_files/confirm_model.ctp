<div class="modal fade" id="confirm-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header">

            </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn_progress_wrapper" id="sign_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="sign_off_no" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>

<!--
<div class="modal fade" id="confirm-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header">

            </div>
            <div class="modal-body" id="modal_body">

                <div style="display: none" id="confirm_message"></div>

                <div class="" id="confirm_form">
                    <div class="margin-bottom" id="">Enter your password</div>
                    <div class="input-group">
                        <input type="password" placeholder="" id="inp_password" class="form-control" name="data[User][password]">
                        <div class="input-group-addon btn bg-green" style="border: 1px solid #649331" id="submit_pass">
                            <i class="fa fa-arrow-right "></i>
                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <div class="" style="display: none" id="btn_footer">
                    <a class="btn btn-success btn-ok btn_progress btn_progress_wrapper" id="submit_delete" style="min-width: 110px; width: 110px;">
                        <div class="btn_progressbar"></div>
                        <span class="text" >Confirm</span>
                    </a>
                    <button type="button" id="close_modal" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
 -->

<div id="myPopoverModal" class="popover popover-default">
    <div class="popover-content">
    </div>
    <div class="popover-footer">
        <button type="submit" class="btn btn-sm btn-primary">Submit</button><button type="reset" class="btn btn-sm btn-default">Reset</button>
    </div>
</div>


<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="working_in"></div>

        </div>
    </div>
</div>


<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_task_assignment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<!-- END MODAL BOX -->

<div id="modal-alert" class="modal fade">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <!--<i class="fa fa-exclamation-triangle"></i>-->Information
            </div>
            <!-- dialog body -->


            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                Please set project dates before setting workspace dates.
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>
