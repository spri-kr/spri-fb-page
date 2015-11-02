<?php

class fb_connector{

	static function get_fb_obj(){
		$options = get_option( 'spri_fb_page_option_name' );

		$fb_config = array(
			'appId'              => $options['app_id'],
			'secret'             => $options['app_secret'],
			'fileUpload'         => false, // optional
			'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
		);

		$fb = new Facebook( $fb_config );

		$access_token = $options['client_token'];
		$app_secret   = $options['app_secret'];

		$appsecret_proof = hash_hmac( 'sha256', $access_token, $app_secret );

		return $fb;
	}
}