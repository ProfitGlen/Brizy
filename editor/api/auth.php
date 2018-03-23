<?php

class Brizy_Editor_API_Auth extends Brizy_Editor_Http_Client {

	/**
	 * @var string
	 */
	private $gateway_url;

	/**
	 * @var string
	 */
	private $client_id;

	/**
	 * @var string
	 */
	private $secret;

	public function __construct( $gateway_url, $client_id, $secret ) {
		parent::__construct();
		$this->gateway_url = $gateway_url;
		$this->client_id   = $client_id;
		$this->secret      = $secret;
	}

	public function auth_url() {
		return $this->gateway_url . '/oauth/token';
	}

	public function refresh_url() {
		return $this->gateway_url . '/oauth/refresh';
	}

	/**
	 * @param $email
	 *
	 * @return Brizy_Editor_API_AccessToken
	 * @throws Brizy_Editor_API_Exceptions_Exception
	 * @throws Brizy_Editor_Http_Exceptions_BadRequest 
	 * @throws Brizy_Editor_Http_Exceptions_ResponseException
	 * @throws Brizy_Editor_Http_Exceptions_ResponseNotFound
	 * @throws Brizy_Editor_Http_Exceptions_ResponseUnauthorized
	 */
	public function getToken( $email ) {

		if(isset($_SESSION[md5($this->client_id.$this->secret)]))
		{
			$token = $_SESSION[md5($this->client_id.$this->secret)];

			if(!$token->expired())
				return $_SESSION[md5($this->client_id.$this->secret)];
			else
				$this->clearTokenCache();
		}

		$response = $this->post( $this->auth_url(),
			array(
				'body'      => array(
					'client_id'     => $this->client_id,
					'client_secret' => $this->secret,
					'email'         => $email,
					'grant_type'      => 'https://visual.dev/api/extended_client_credentials'
				),
				'sslverify' => false
			)
		)->get_response_body();


		$brizy_editor_API_access_token = new Brizy_Editor_API_AccessToken( $response['access_token'], $response['expires_in'] + time() );

		if(isset($response['refresh_token']))
		{
			$brizy_editor_API_access_token->set_refresh_token( $response['refresh_token'] );
		}

		return $_SESSION[md5($this->client_id.$this->secret)] = $brizy_editor_API_access_token;
	}

	public function clearTokenCache() {

		unset($_SESSION[md5($this->client_id.$this->secret)]);
	}


}