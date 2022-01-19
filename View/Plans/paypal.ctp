<?php echo $this->Html->script(array('/plugins/jQuery/jQuery-2.1.3.min',)) ?>
<script type="text/javascript">
  $(document).ready(function() {
  $("#loader").load(function(e) {
       var timer = setTimeout(function(){
    	 jQuery('#selPaymentForm').submit();
    },1000);
    });
 });
</script>

<div id="body">
  <div class="wrapper">
    <div class="content-sidebar">
      <div class="primary">
        <div class="indent">
          <section class="post">
            <div style="display:none;">
              <?php $this->Paypal->paypal($gateway_options); ?>
            </div>
            <div style="text-align:center;">
              <p>Please do not refresh the page or press back button.</p>
              <img id="loader" width="200" alt="" src="<?php echo SITEURL; ?>/images/ajax-loader-1.gif">
              <p>You are being redirected to the PayPal site.</p>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>