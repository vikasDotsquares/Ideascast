<style type="text/css">


.select-hidden {
  display: none;
  visibility: hidden;
  padding-right: 10px;
}
.select {
  cursor: pointer;
  display: inline-block;
  position: relative;
  font-size: 16px;
  color: #fff;
  width: 220px;
  height: 40px;
}
.select-styled {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  transition: all 0.2s ease-in;
background-color: #ffffff;
    padding: 6px 12px;
    border: 1px solid #ccc;
    color: #333;
}

.select-styled:after {
    content:"";
    width: 0;
    height: 0;
    border: 7px solid transparent;
    border-color: #fff transparent transparent transparent;
    position: absolute;
    top: 16px;
    right: 10px;
  }

.select-styled:hover {
    background-color: darken(#c0392b, 2);
  }
.select-styled:active, .select-styled.active {
    background-color: darken(#c0392b, 5);
  }

.select-styled:active:after, .select-styled.active:after {
      top: 9px;
      border-color: transparent transparent #fff transparent;
    }
.select-options {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  left: 0;
  z-index: 999;
  margin: 0;
  padding: 0;
  list-style: none;
  background-color: darken(#c0392b, 5);
  max-height: 100px;
  overflow: auto;
  border: 1px solid #d8d8d8;
}
.select-options li {
    margin: 0;
    padding: 2px 0;
    text-indent: 15px;
    /*transition: all 0.15s ease-in;*/
    background: #ffffff;
    color: #333;
  }

.select-options li:hover {
    color: #fff;
    background: #0073b7;
}
.select-options li[rel="hide"] {
      display: none;
    }
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
                        <div class="box-body clearfix" >

<select id="mounth">
    <option value="hide">-- Month --</option>
    <option value="january" rel="icon-temperature">January</option>
    <option value="february" selected="">February</option>
    <option value="march">March</option>
    <option value="april">April</option>
    <option value="may">May</option>
    <option value="june">June</option>
    <option value="july">July</option>
    <option value="august">August</option>
    <option value="september">September</option>
    <option value="october">October</option>
    <option value="november">November</option>
    <option value="december">December</option>
</select>

<select id="year">
    <option value="hide">-- Year --</option>
    <option value="2010">2010</option>
    <option value="2011">2011</option>
    <option value="2012">2012</option>
    <option value="2013">2013</option>
    <option value="2014">2014</option>
    <option value="2015">2015</option>
</select>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
1px solid #337ab7
<script type="text/javascript">
    $(function(){
        /*
        Reference: http://jsfiddle.net/BB3JK/47/
        */

        $('select').each(function(){
            var $this = $(this), numberOfOptions = $(this).children('option').length;
            console.log($('option:selected',$this).val())

            $this.addClass('select-hidden');
            $this.wrap('<div class="select"></div>');
            $this.after('<div class="select-styled"></div>');

            var $styledSelect = $this.next('div.select-styled');
            $styledSelect.text($this.children('option').eq(0).text());

            var $list = $('<ul />', {
                'class': 'select-options'
            }).insertAfter($styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: $this.children('option').eq(i).text(),
                    rel: $this.children('option').eq(i).val()
                }).appendTo($list);
            }

            var $listItems = $list.children('li');

            $styledSelect.click(function(e) {
                e.stopPropagation();
                $('div.select-styled.active').not(this).each(function(){
                    $(this).removeClass('active').next('ul.select-options').hide();
                });
                $(this).toggleClass('active').next('ul.select-options').toggle();
            });

            $listItems.click(function(e) {
                e.stopPropagation();
                $styledSelect.text($(this).text()).removeClass('active');
                $this.val($(this).attr('rel'));
                $list.hide();
                console.log($this.val());
            });

            $(document).click(function() {
                $styledSelect.removeClass('active');
                $list.hide();
            });

        });
    })
</script>
