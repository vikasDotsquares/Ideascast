<?php

class PaypalHelper extends AppHelper {
	var $helpers = array(
        'Form',
        'Html'
    );
	function paypal($settings = array()) 
	{ 
		$__default_settings = array(
		// Common fixed settings
		'action_url' => array(
			'livemode' => 'https://www.paypal.com/cgi-bin/webscr',
			'testmode' => 'https://www.sandbox.paypal.com/cgi-bin/webscr'
			) , // Paypal URL to which the form to be posted
		    'cmd' => '_xclick',
		// Overridable setting
		'is_testmode' => Configure::read('paypal.is_testmode') ,
		'notify_url' => '', // Our site URL to which the paypal will post the payment status details in background
			'cancel_return' => '', // Our site URL to which paypal transaction cancel click will return
			'return' => '', // Our site URL to which paypal transaction success click will return
			'item_name' => '', // Item/product name
			'business' => Configure::read('paypal.account') ,
			'currency_code' => Configure::read('paypal.currency_code') ,
			'amount' => '',
			'on0' => 'Transkey',
			'os0' => '',
		);
		$settings = array_merge($__default_settings, $settings);
		if (!empty($settings['user_defined'])) {
			$ecnoded_params = base64_encode(gzdeflate(serialize($settings['user_defined']) , 9));
			$user_defined_hash = substr(md5(Configure::read('Security.salt') . $ecnoded_params) , 5, 5);
			$settings['os0'] = $ecnoded_params . '~' . $user_defined_hash;
		}
		$settings['action_url'] = (!empty($settings['is_testmode'])) ? $settings['action_url']['testmode'] : $settings['action_url']['livemode'];
		
		echo $this->Form->create('Paypal', array(
			'class' => 'normal js-auto-submit',
			'id' => 'selPaymentForm',
			'url' => $settings['action_url'],
			
		));
		
		if(isset($settings['recurring']) && $settings['recurring']==1)
		{
		
		echo $this->Form->input('cmd', array(
			'type' => 'hidden',
			'name' => 'cmd',
			//'value' => $settings['cmd']
			'value' => '_xclick-subscriptions'
		));
		
		}else {
		
		echo $this->Form->input('cmd', array(
			'type' => 'hidden',
			'name' => 'cmd',			
			'value' => $settings['cmd']
		));
		
		}
		echo $this->Form->input('rm', array(
			'type' => 'hidden',
			'name' => 'rm',
			'value' => '2'
		));
		
		echo $this->Form->input('custom', array(
			'type' => 'hidden',
			'name' => 'custom',
			'value' => $settings['custom']
		));
		echo $this->Form->input('notify_url', array(
			'type' => 'hidden',
			'name' => 'notify_url',
			'value' => $this->Html->url($settings['notify_url'], true)
		));
		echo $this->Form->input('cancel_return', array(
			'type' => 'hidden',
			'name' => 'cancel_return',
			'value' => $this->Html->url($settings['cancel_return'], true)
		));
		echo $this->Form->input('return', array(
			'type' => 'hidden',
			'name' => 'return',
			'value' => $this->Html->url($settings['return'], true)
		));
		echo $this->Form->input('business', array(
			'type' => 'hidden',
			'name' => 'business',
			'value' => $settings['business']
		));
		echo $this->Form->input('item_name', array(
			'type' => 'hidden',
			'name' => 'item_name',
			'value' => $settings['item_name']
		));
		 echo $this->Form->input('item_number', array(
			'type' => 'hidden',
			'name' => 'item_number',
			'value' => $settings['item_number']
		));
		echo $this->Form->input('currency_code', array(
			'type' => 'hidden',
			'name' => 'currency_code',
			'value' => $settings['currency_code']
		));
		
		
		if(!isset($settings['recurring']))
		{
		 echo $this->Form->input('amount', array(
			'type' => 'hidden',
			'name' => 'amount',
			'value' => $settings['amount']
		));
		}
		
		
		
		echo $this->Form->input('no_shipping', array(
			'type' => 'hidden',
			'name' => 'no_shipping',
			'value' => 1
		));
		echo $this->Form->input('no_note', array(
			'type' => 'hidden',
			'name' => 'no_note',
			'value' => 1
		));
		
		
		if(isset($settings['recurring']) && $settings['recurring']==1)
		{
		echo $this->Form->input('a3', array(
			'type' => 'hidden',
			'name' => 'a3',
			'value' => $settings['a1']
		));
		
		echo $this->Form->input('p3', array(
			'type' => 'hidden',
			'name' => 'p3',
			'value' => $settings['p1']
		));
		
		echo $this->Form->input('t3', array(
			'type' => 'hidden',
			'name' => 't3',
			'value' => $settings['t1']
		));
		
		echo $this->Form->input('src', array(
			'type' => 'hidden',
			'name' => 'src',
			'value' => "1"
		));
		
		
		echo $this->Form->input('sra', array(
			'type' => 'hidden',
			'name' => 'sra',
			'value' => "1"
		));
		
		}
		
		
		echo $this->Form->submit(__('Pay via Paypal'));
		echo $this->Form->end();
	}
}