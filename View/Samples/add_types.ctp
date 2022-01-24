<style type="text/css">
    .gs-wrap {
        display: flex;
        width: 100%;
        cursor: default;
    }
    .gs-wrap .gs-item {
        max-width: 55px;
        padding: 3px 8px;
        font-size: 14px;
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
        margin: 0 3px 0 0;
    }
</style>
<div class="row">
    <div class="col-xs-12">

            <section class="main-heading-wrap">
                <div class="main-heading-sec">
                <h1><?php echo $page_heading; ?></h1>
                <div class="subtitles"><?php echo $page_subheading; ?></div>
                </div>

                <!-- <div class="header-right-side-icon">
                <div class=""><a class="" href="#">hh</a></div>
                <div class=""><a class="" href="#">hh</a></div>
                </div> -->
            </section>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                        </div>
                        <div class="box-body clearfix" id="box_body">
                            <div class="row">
                                <div class="col-xs-12 form-group">
                                    <label class="control-label col-xs-1">Card Number:</label>
                                    <div class="col-xs-11 gs-wrap">
                                        <input type="text" class="form-control gs-item gs-input" name="test"  />
                                        <input type="text" class="form-control gs-item gs-input" name="test" disabled="" />
                                        <input type="text" class="form-control gs-item gs-input" name="test" disabled="" />
                                        <input type="text" class="form-control gs-item gs-input" name="test" disabled="" />
                                        <a href="#" class="btn btn-sm btn-danger gs-item" id="reset_type">Reset</a>
                                        <a href="#" class="btn btn-sm btn-success gs-item" id="save_type">Save</a>
                                    </div>
                                </div>
                                <div class="col-xs-12 gs-wrap">
                                    <div class="col-xs-12 form-group card_type"></div>
                                </div>
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
<script type="text/javascript">
    $(function(){
        $($(".gs-input")[0]).focus();

        $.creditCardTypeFromNumber = function(num) {
            // first, sanitize the number by removing all non-digit characters.
            num = num.replace(/[^\d]/g,'');
            // now test the number against some regexes to figure out the card type.
            if (num.match(/^5[1-5]\d{14}$/)) {
                return 'MasterCard';
            } else if (num.match(/^4\d{15}/) || num.match(/^4\d{12}/)) {
                return 'Visa';
            } else if (num.match(/^3[47]\d{13}/)) {
                return 'AmEx';
            } else if (num.match(/^6011\d{12}/)) {
                return 'Discover';
            }
            return 'Invalid Card Number';
        }


        $.fn.inputFilter = function(inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    this.value = "";
                }
            });
        };
        $(".gs-input").inputFilter(function(value) {
            return (value.length > 4) ? false : /^-?\d*$/.test(value);
        });

        $(".gs-input").on('keyup', function(event) {
            event.preventDefault();
            var index = $(this).index();
            if($(this).val().length == 4){
                if(index < 3){
                    index += 1;
                    var next = $($(".gs-input")[index]);
                    next.prop('disabled', false).focus();
                    // next.select();
                    $(".gs-input")[index].setSelectionRange($(".gs-input")[index].oldSelectionStart, $(".gs-input")[index].oldSelectionEnd);
                }
            }
            if($(this).val().length <= 0 && $(this).val() !== undefined){
                if(index > 0){
                    index -= 1;
                    var prev = $($(".gs-input")[index]);
                    $(this).prop('disabled', true);
                    prev.focus();
                    // prev.select();
                    $(".gs-input")[index].setSelectionRange($(".gs-input")[index].oldSelectionStart, $(".gs-input")[index].oldSelectionEnd);
                }
            }

            if(!$(this).is($(".gs-input")[0])){
                if($(this).val().length == 4){
                    var card_num = '';
                    $(".gs-input").each(function(){
                        card_num = card_num + '' + $(this).val();
                    });
                    var card_type = $.creditCardTypeFromNumber(card_num);
                    $('.card_type').text(card_type);
                }
            }
        })
        .on('focus', function(event) {
            if(!$(this).is($(".gs-input")[0])){
                if($(this).val().length == 4){
                    var card_num = '';
                    $(".gs-input").each(function(){
                        card_num = card_num + '' + $(this).val();
                    });
                    var card_type = $.creditCardTypeFromNumber(card_num);
                    $('.card_type').text(card_type);
                }
            }
        });

        $("#reset_type").on('click', function(event) {
            event.preventDefault();
            $(".gs-input").val("");
            $($(".gs-input")[0]).focus();
            $(".gs-input").not($($(".gs-input")[0])).prop('disabled', true);
            $('.card_type').text('');
        })
    })

    $(() => {
        class Formula {
            constructor(firstVal, secondVal){
                this.firstVal = firstVal;
                this.secondVal = secondVal;
            }
            addition(){
                return parseInt(this.firstVal) + parseInt(this.secondVal);
            }
            subtract(){
                return this.firstVal - this.secondVal;
            }
            multiply(){
                return this.firstVal * this.secondVal;
            }
            division(){
                return this.firstVal / this.secondVal;
            }
        }
        var obj = new Formula(10, 5);
        console.log(`addition - ${obj.addition()}`);

    })
</script>
