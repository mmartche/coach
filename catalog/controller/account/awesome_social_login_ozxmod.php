<?php
/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
* It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/
require_once DIR_SYSTEM . 'library/social-login/facebook/src/Facebook/autoload.php';
require_once DIR_SYSTEM . 'library/social-login/google/src/Google/autoload.php';

class ControllerAccountAwesomeSocialLoginOzxmod extends Controller {
	private $error;
	public function template() {
		
		$oc_version = (int)VERSION.'.'.str_replace('.',"",substr(VERSION,2));
		 
		$this->language->load('account/awesome_social_login_ozxmod');
		
		// Template 1 Language
		$data['text_new_welcome'] = $this->language->get('text_new_welcome');
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$data['tab_login'] = $this->language->get('tab_login');
		$data['tab_signup'] = $this->language->get('tab_signup');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_forgot'] = $this->language->get('text_forgot');
		$data['text_forgot_desc'] = $this->language->get('text_forgot_desc');
		$data['text_repeat'] = $this->language->get('text_repeat');
		$data['text_close_window'] = $this->language->get('text_close_window');
		$data['text_sign_with'] = $this->language->get('text_sign_with');
		$data['text_fb_title'] = $this->language->get('text_fb_title');
		$data['text_google_title'] = $this->language->get('text_google_title');
		$data['text_twitter_title'] = $this->language->get('text_twitter_title');
		$data['text_linkedin_title'] = $this->language->get('text_linkedin_title');
		$data['button_social_login'] = $this->language->get('button_social_login');
		$data['button_send'] = $this->language->get('button_send');
		$data['button_signup'] = $this->language->get('button_signup');
		$data['tab_twitter'] = $this->language->get('tab_twitter');
		$data['text_twit_email'] = $this->language->get('text_twit_email');
		$data['forgot_password'] = $this->url->link('account/forgotten', '', 'SSL');
		
		// Template 2 language
		$data['text_2_login_head'] = $this->language->get('text_2_login_head');
		$data['text_2_login_summary'] = $this->language->get('text_2_login_summary');
		$data['text_2_login_already'] = $this->language->get('text_2_login_already');
		$data['text_2_login_with'] = $this->language->get('text_2_login_with');
		$data['text_2_signup_head'] = $this->language->get('text_2_signup_head');
		$data['text_2_signup_summary'] = $this->language->get('text_2_signup_summary');
		$data['text_2_signup_already'] = $this->language->get('text_2_signup_already');
		$data['text_2_twitter_summary'] = $this->language->get('text_2_twitter_summary');
		
		// Google login
		$client = new Google_Client();
		$client->setClientId($this->config->get('awesome_social_login_ozxmod_googleapikey'));
		$client->setClientSecret($this->config->get('awesome_social_login_ozxmod_googleapisecret'));
		$client->setRedirectUri($this->url->link('account/awesome_social_login_ozxmod/glogin', '', 'SSL'));
		$client->addScope("email");
		$client->addScope("profile");
		
		$service = new Google_Service_Oauth2($client);
		$data['g_login'] = $client->createAuthUrl();
		// End Google Login
		
		// Facebook Login
		$fb = new Facebook\Facebook(array(
		  'app_id' => $this->config->get('awesome_social_login_ozxmod_apikey'), // Replace {app-id} with your app id
		  'app_secret' => $this->config->get('awesome_social_login_ozxmod_apisecret'),
		  'default_graph_version' => 'v2.8',
		  ));
		
		$helper = $fb->getRedirectLoginHelper();
		
		$permissions = array('email'); // Optional permissions
		$data['fb_login'] = $helper->getLoginUrl($this->url->link('account/awesome_social_login_ozxmod/fblogin'), $permissions);
		
		// End Facebook Login
		
		// Twitter Error
		$data['ozxmod_twit_error'] = '';
		if(isset($this->session->data['ozxmod_twit_error'])) {
			$data['ozxmod_twit_error'] = $this->session->data['ozxmod_twit_error'];
			unset($this->session->data['ozxmod_twit_error']);
		}
		// End Twitter Error
		
		// Linkedin Login
		$data['linkedin_login'] = $this->url->link('account/awesome_social_login_ozxmod/linkedinlogin', '', 'SSL');
		// End Linkedin Login	
		
		// Loading template
		$template_id = $this->config->get('awesome_social_login_ozxmod_template_id');
		
		$default_theme = "";
		$tpl = "";
		if($oc_version < 2.302){
			$default_theme = 'default/template/';
			$tpl = ".tpl";
		}
	
		$data['config'] = $this->config;
		
		return $this->load->view($default_theme.'account/ozxmod/template_'.$template_id.$tpl, $data);
	}

	public function button_template() {
		$oc_version = (int)VERSION.'.'.str_replace('.',"",substr(VERSION,2));
		 
		$this->language->load('account/awesome_social_login_ozxmod');
		
		$data['text_sign_with'] = $this->language->get('text_sign_with');
		$data['text_fb_title'] = $this->language->get('text_fb_title');
		$data['text_google_title'] = $this->language->get('text_google_title');
		$data['text_twitter_title'] = $this->language->get('text_twitter_title');
		$data['text_linkedin_title'] = $this->language->get('text_linkedin_title');
		$data['button_social_login'] = $this->language->get('button_social_login');
		$data['tab_twitter'] = $this->language->get('tab_twitter');
		$data['text_twit_email'] = $this->language->get('text_twit_email');
		$data['forgot_password'] = $this->url->link('account/forgotten', '', 'SSL');
		
		// Template 2 language
		$data['text_2_login_with_checkout'] = $this->language->get('text_2_login_with');
	
		// Google login
		$client = new Google_Client();
		$client->setClientId($this->config->get('awesome_social_login_ozxmod_googleapikey'));
		$client->setClientSecret($this->config->get('awesome_social_login_ozxmod_googleapisecret'));
		$client->setRedirectUri($this->url->link('account/awesome_social_login_ozxmod/glogin', '', 'SSL'));
		$client->addScope("email");
		$client->addScope("profile");
		
		$service = new Google_Service_Oauth2($client);
		$data['g_login'] = $client->createAuthUrl();
		// End Google Login
		
		// Facebook Login
		$fb = new Facebook\Facebook(array(
		  'app_id' => $this->config->get('awesome_social_login_ozxmod_apikey'), // Replace {app-id} with your app id
		  'app_secret' => $this->config->get('awesome_social_login_ozxmod_apisecret'),
		  'default_graph_version' => 'v2.8',
		  ));
		
		$helper = $fb->getRedirectLoginHelper();
		
		$permissions = array('email'); // Optional permissions
		$data['fb_login'] = $helper->getLoginUrl($this->url->link('account/awesome_social_login_ozxmod/fblogin'), $permissions);
		
		// End Facebook Login
		
		// Linkedin Login
		$data['linkedin_login'] = $this->url->link('account/awesome_social_login_ozxmod/linkedinlogin', '', 'SSL');
		// End Linkedin Login	
		
		$default_theme = "";
		$tpl = "";
		if($oc_version < 2.302){
			$default_theme = 'default/template/';
			$tpl = ".tpl";
		}
		
		$data['config'] = $this->config;
		
		return $this->load->view($default_theme.'account/ozxmod/button_template_1'.$tpl, $data);
	}

	public function login(){
	
		$this->language->load('account/awesome_social_login_ozxmod');
		$json = array();
		
		if(!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['ajax_email'])){
			$json["error"] = $this->language->get('error_email');
		}else if (!$this->customer->login($this->request->post['ajax_email'], $this->request->post['ajax_password'])) {
			$json['error'] = $this->language->get('error_login');
		}else{
			$json['success'] = $this->language->get('text_success');
		}
		if ($this->customer->isLogged()) {
			if(isset($_SERVER['HTTP_REFERER'])){
				$json['redirect']= $_SERVER['HTTP_REFERER'];
			}else{
				$json['redirect']=$this->url->link('account/account', '', 'SSL');	
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function signup(){
		
		$this->language->load('account/awesome_social_login_ozxmod');
		
		$this->load->model('account/customer');
		
		$json = array();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSignup()) {
			$name = $telephone = "";
			$email = $this->request->post["ajax_register_email"];
			$password = $this->request->post["ajax_register_password"];
			$re_password = $this->request->post["re_ajax_register_password"];
			if(isset($this->request->post['ajax_register_name']))
				$name = $this->request->post['ajax_register_name'];
			if(isset($this->request->post['ajax_register_telephone']))
				$telephone = $this->request->post['ajax_register_telephone'];
			$firstname = $lastname = "";	
			if($name != "") {
				$name_arr = explode(' ', $name);
				$firstname = $name_arr[0];
				if(isset($name_arr[1]))
					$lastname = $name_arr[1];
			}else {
				$name_arr = explode('@', $email);
				$firstname = $name_arr[0];
			}
			$config_customer_approval = $this->config->get('config_customer_approval');
			$this->config->set('config_customer_approval',0);
			
			$add_data=array();
			$add_data['email'] = $email;
			$add_data['password'] = $password;
			$add_data['firstname'] = $firstname;
			$add_data['lastname'] = $lastname;
			$add_data['fax'] = '';
			$add_data['telephone'] = $telephone;
			$add_data['company'] = '';
			$add_data['company_id'] = '';
			$add_data['tax_id'] = '';
			$add_data['address_1'] = '';
			$add_data['address_2'] = '';
			$add_data['city'] = '';
			$add_data['city_id'] = '';
			$add_data['postcode'] = '';
			$add_data['country_id'] = 0;
			$add_data['zone_id'] = 0;
			
			$this->model_account_customer->addCustomer($add_data);
			$this->config->set('config_customer_approval',$config_customer_approval);
			
			if($this->customer->login($email, $password)){
				// Delete address
				$this->deleteAddress();
					
				unset($this->session->data['guest']);
				$json['success'] = "Success";
				if(isset($_SERVER['HTTP_REFERER'])){
					$json['redirect']= $_SERVER['HTTP_REFERER'];
				}else{
					$json['redirect'] = $this->url->link('account/success', '', 'SSL');	
				}
				
			}
				
			
		} else{
			$json["error"] = $this->language->get('error_hack');
		}

		if($this->error) {
			$json['error'] = $this->error;
		}

		$this->response->setOutput(json_encode($json));
	}

	public function validateSignup() {
		 
		if(isset($this->request->post['ajax_register_name']) && $this->request->post['ajax_register_name'] == "") {
			$this->error['error_name'] = $this->language->get('error_name_required');
		}
		
		if($this->request->post['ajax_register_email'] == "") {
			$this->error['error_email'] = $this->language->get('error_email_required');
		}else if(!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['ajax_register_email'])){
			$this->error['error_email'] = $this->language->get('error_email');
		}else if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['ajax_register_email'])) {
			$this->error['error_email'] = $this->language->get('error_exists');
		}
		
		if(isset($this->request->post['ajax_register_telephone']) && $this->request->post['ajax_register_telephone'] == "") {
			$this->error['error_telephone'] = $this->language->get('error_telephone_required');
		}
		
		if($this->request->post['ajax_register_password'] == "") {
			$this->error['error_password'] = $this->language->get('error_password_required');
		}
		
		if($this->request->post['re_ajax_register_password'] == "") {
			$this->error['error_repassword'] = $this->language->get('error_repassword_required');
		}
		
		if($this->request->post['ajax_register_password'] !="" && $this->request->post['re_ajax_register_password'] != "" && $this->request->post['ajax_register_password'] != $this->request->post['re_ajax_register_password']) {
			$this->error['error_repassword'] = $this->language->get("error_password_match");
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
		
	}
	
	public function sendForgotPassword(){
		$json = array();
		
		$this->language->load('account/awesome_social_login_ozxmod');
		
		if($this->validateForgotPassword()) {
		$this->language->load('mail/forgotten');
		
		$password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);
		
		$this->model_account_customer->editPassword($this->request->post['ajax_forgot_email'], $password);
		
		$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));
		
		$message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
		$message .= $this->language->get('text_password') . "\n\n";
		$message .= $password;
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');
		$mail->setTo($this->request->post['ajax_forgot_email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
		$mail->send();
		
		$json['success'] = sprintf($this->language->get('text_email_sent'), $this->request->post['ajax_forgot_email']);
		
		} else {
			$json["error"] = $this->language->get('error_no_accounts');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	protected function validateForgotPassword() {
		$this->load->model("account/customer");
		
		if (!isset($this->request->post['ajax_forgot_email']) || empty($this->request->post['ajax_forgot_email']) ) {
			$this->error['warning'] = $this->language->get('error_ajax_forgot_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['ajax_forgot_email'])) {
			$this->error['warning'] = $this->language->get('error_ajax_forgot_email');
		}
	
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function fblogin() {
		
		 
		$fb = new Facebook\Facebook(array(
		  'app_id' => $this->config->get('awesome_social_login_ozxmod_apikey'),
		  'app_secret' => $this->config->get('awesome_social_login_ozxmod_apisecret'),
		  'default_graph_version' => 'v2.8',
		  ));
		
		$helper = $fb->getRedirectLoginHelper();
		
		try {
		  $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		
		if (! isset($accessToken)) {
		  if ($helper->getError()) {
		    header('HTTP/1.0 401 Unauthorized');
		    echo "Error: " . $helper->getError() . "\n";
		    echo "Error Code: " . $helper->getErrorCode() . "\n";
		    echo "Error Reason: " . $helper->getErrorReason() . "\n";
		    echo "Error Description: " . $helper->getErrorDescription() . "\n";
		  } else {
		    header('HTTP/1.0 400 Bad Request');
		    echo 'Bad request';
		  }
		  exit;
		}
		
		try {
		  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
		  // If you provided a 'default_access_token', the '{access-token}' is optional.
		  $response = $fb->get('/me?fields=id,first_name,last_name,email', $accessToken);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		
		$me = $response->getGraphUser();
		$fbuser_profile = array(); 
		$fbuser_profile['id'] = $me->getId();
		$fbuser_profile['first_name'] = $me->getFirstName();
		$fbuser_profile['last_name'] = $me->getLastName();
		$fbuser_profile['email'] = $me->getEmail();
		
		$loc = $this->url->link("account/account", "", 'SSL');
		
		if(isset($_SERVER['HTTP_REFERER']))
			$loc = $_SERVER['HTTP_REFERER'];
		
		if ($this->customer->isLogged())	
			$this->response->redirect($loc);
		
		$_SERVER_CLEANED = $_SERVER;
		$_SERVER = $this->clean_decode($_SERVER);
	
		$_SERVER = $_SERVER_CLEANED;
	
		if($me->getEmail()){
			$this->load->model('account/customer');
	
			$email = $fbuser_profile['email'];
			$password = $this->get_password($fbuser_profile['id']);
			if($this->customer->login($email, $password, true)){
				$this->response->redirect($loc);
			}
	
			$email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "'");
			if($email_query->num_rows){
				//$this->model_account_customer->editPassword($email, $password);
				if($this->customer->login($email, $password, true)){
					$this->response->redirect($loc);
				}
			}
			else{
	
				$config_customer_approval = $this->config->get('config_customer_approval');
				$this->config->set('config_customer_approval',0);
	
				$this->request->post['email'] = $email;
					
				$add_data=array();
				$add_data['email'] = $fbuser_profile['email'];
				$add_data['password'] = $password;
				$add_data['firstname'] = isset($fbuser_profile['first_name']) ? $fbuser_profile['first_name'] : '';
				$add_data['lastname'] = isset($fbuser_profile['last_name']) ? $fbuser_profile['last_name'] : '';
				$add_data['fax'] = '';
				$add_data['telephone'] = '';
				$add_data['company'] = '';
				$add_data['company_id'] = '';
				$add_data['tax_id'] = '';
				$add_data['address_1'] = '';
				$add_data['address_2'] = '';
				$add_data['city'] = '';
				$add_data['city_id'] = '';
				$add_data['postcode'] = '';
				$add_data['country_id'] = 0;
				$add_data['zone_id'] = 0;
	
				$this->model_account_customer->addCustomer($add_data);
				$this->config->set('config_customer_approval',$config_customer_approval);
	
				if($this->customer->login($email, $password, true)){
					
					// Delete address
					$this->deleteAddress();
					
					unset($this->session->data['guest']);
					$this->response->redirect($loc);
					
				}
			}
	
		}
		$this->response->redirect($loc);
		
	}
	
	// Google Login Code
	
	public function glogin() {
		
		
		$client = new Google_Client();
		$client->setClientId($this->config->get('awesome_social_login_ozxmod_googleapikey'));
		$client->setClientSecret($this->config->get('awesome_social_login_ozxmod_googleapisecret'));
		$client->setRedirectUri($this->url->link('account/awesome_social_login_ozxmod/glogin', '', 'SSL'));
		$client->addScope("email");
		$client->addScope("profile");
		
		$service = new Google_Service_Oauth2($client);
		
		if (isset($_REQUEST['logout'])) {
			unset($_SESSION['access_token']);
		}
		
		if (isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$access_token = $client->getAccessToken();
			$client->setAccessToken($access_token);
		}
		
		if ($client->getAccessToken()) {
			
			$data = $service->userinfo->get();
			
			$loc = $this->url->link("account/account", "", 'SSL');
			
			if(isset($_SERVER['HTTP_REFERER']))
				$loc = $_SERVER['HTTP_REFERER'];
			
			$this->load->model('account/customer');

			// Checking email id if already registered
			$email = $data["email"];
			$password = $this->get_password($email);

			if($this->customer->login($email, $password, true)){
				$this->response->redirect($loc);
			}

			$email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "'");

			if($email_query->num_rows){
				//$this->model_account_customer->editPassword($email, $password);
				if($this->customer->login($email, $password, true)){
					$this->response->redirect($loc);
				}
			} else {
				$name = $data['name'];
				$name_split = explode(" ", $name);
				
				$f_name = $name_split[0];
				$l_name = '';
				if(isset($name_split[1]))
					$l_name = $name_split[1];
				
				if(isset($name_split[2]))
					$l_name .= $name_split[2];
					
				$config_customer_approval = $this->config->get('config_customer_approval');
				$this->config->set('config_customer_approval',0);
					
				$this->request->post['email'] = $email;

				$add_data=array();
				$add_data['email'] = $email;
				$add_data['password'] = $password;
				$add_data['firstname'] = $f_name;
				$add_data['lastname'] = $l_name;
				$add_data['fax'] = '';
				$add_data['telephone'] = '';
				$add_data['company'] = '';
				$add_data['company_id'] = '';
				$add_data['tax_id'] = '';
				$add_data['address_1'] = '';
				$add_data['address_2'] = '';
				$add_data['city'] = '';
				$add_data['city_id'] = '';
				$add_data['postcode'] = '';
				$add_data['country_id'] = 0;
				$add_data['zone_id'] = 0;
					
				$this->model_account_customer->addCustomer($add_data);
				$this->config->set('config_customer_approval',$config_customer_approval);
				
				if($this->customer->login($email, $password, true)){
					
					// Delete address
					$this->deleteAddress();
					
					unset($this->session->data['guest']);
					
					$this->response->redirect($loc);
				}
			}
		}else{
			$this->response->redirect($this->url->link('common/home', '', 'SSL'));		
		}
	}
	
	// End Google Login Code
	
	// Twitter Login Code
	public function login_twit(){
	
		$this->language->load('account/awesome_social_login_ozxmod');
		$json = array();
	
		if(!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['ajax_twit_email'])){
			$json["error"] = $this->language->get('error_email');
		}else{
			
			$json['success'] = $this->language->get('text_success');
			
			$this->session->data['ozxmod_twit_email'] = $this->request->post['ajax_twit_email'];
			$json['redirect']= $this->url->link('account/awesome_social_login_ozxmod/twit', '', 'SSL');
			
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function twit() {
		require_once DIR_SYSTEM.'library/social-login/twitter/twitteroauth.php';
		
		
		$twitteroauth = new TwitterOAuth($this->config->get('awesome_social_login_ozxmod_twitterapikey'), $this->config->get('awesome_social_login_ozxmod_twitterapisecret'));
		
		// Requesting authentication tokens, the parameter is the URL we will be redirected to
		
		//die("0");
		$request_token = $twitteroauth->getRequestToken($this->url->link('account/awesome_social_login_ozxmod/twitter', '', 'SSL'));
		
		//print_r($request_token); die("Hey");
		// Saving them into the session
		
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		if(isset($_SERVER['HTTP_REFERER']))
			$this->session->data['ajaxfblogin_from'] = $_SERVER['HTTP_REFERER']; 
		
		// If everything goes well..
		if ($twitteroauth->http_code == 200) {
			// Let's generate the URL and redirect
			$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
			header('Location: ' . $url);
		} else {
			// It's a bad idea to kill the script, but we've got to know when there's an error.
			die('Something wrong happened.');
		}
	}
	
	public function twitter() {
		require_once DIR_SYSTEM.'library/social-login/twitter/twitteroauth.php';
		
		$this->language->load('account/awesome_social_login_ozxmod');
		
		
		if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
		
			
			
			// We've got everything we need
			//$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		 
			$twitteroauth = new TwitterOAuth($this->config->get('awesome_social_login_ozxmod_twitterapikey'), $this->config->get('awesome_social_login_ozxmod_twitterapisecret'), $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			
			// Let's request the access token
		
			$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
			// Save it in a session var
			$_SESSION['access_token'] = $access_token;
			// Let's get the user's info
			$user_info = $twitteroauth->get('account/verify_credentials');
			
			if (isset($user_info->error)) {
				// Something's wrong, go back to square 1
				//header('Location: login-twitter.php');
			} else {
				$twit_id = $user_info->id;
				$name = $user_info->name;
				
				$name_arr = explode(" ", $name);
				$f_name = array_shift($name_arr);
				$l_name = implode(" ", $name_arr);
				
				$loc = $this->url->link("account/account", "", 'SSL');
				
				if(isset($this->session->data['ajaxfblogin_from'])) {
					$loc = $this->session->data['ajaxfblogin_from'];
					unset($this->session->data['ajaxfblogin_from']);
				}
				
				$this->load->model('account/customer');
				
				// Checking email id if already registered
				$email = $this->session->data['ozxmod_twit_email'];
				unset($this->session->data['ozxmod_twit_email']);
				
				$password = $this->get_twitpassword($twit_id);
				
				if($this->customer->login($email, $password)){
					$this->response->redirect($loc);
				}
				$email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "'");
				//print_r($email_query->rows); die("hey");
				if($email_query->num_rows){
					//$this->model_account_customer->editPassword($email, $password);
					
					if($this->customer->login($email, $password)){
						$this->response->redirect($loc);
					} else {
					
						$this->session->data['ozxmod_twit_error'] = $this->language->get('error_twitter');
						//echo $this->session->data['ozxmod_twit_error']; die;
						$this->response->redirect($loc);
					}
					
				} else {
						
					$config_customer_approval = $this->config->get('config_customer_approval');
					$this->config->set('config_customer_approval',0);
						
					$this->request->post['email'] = $email;
				
					$add_data=array();
					$add_data['email'] = $email;
					$add_data['password'] = $password;
					$add_data['firstname'] = $f_name;
					$add_data['lastname'] = $l_name;
					$add_data['fax'] = '';
					$add_data['telephone'] = '';
					$add_data['company'] = '';
					$add_data['company_id'] = '';
					$add_data['tax_id'] = '';
					$add_data['address_1'] = '';
					$add_data['address_2'] = '';
					$add_data['city'] = '';
					$add_data['city_id'] = '';
					$add_data['postcode'] = '';
					$add_data['country_id'] = 0;
					$add_data['zone_id'] = 0;
						
					$this->model_account_customer->addCustomer($add_data);
					$this->config->set('config_customer_approval',$config_customer_approval);
				
					if($this->customer->login($email, $password)){
						// Delete address
						$this->deleteAddress();
						unset($this->session->data['guest']);
						$this->response->redirect($loc);
					}
				}
				
			}
		} else {
			// Something's missing, go back to square 1
			$this->response->redirect($this->url->link('common/home', '', 'SSL'));
			//header('Location: login-twitter.php');
		}
	}
	 
	// End Twitter Login Code
	
	
	// LinkedIn Login
	
	public function linkedinlogin() {
	
		require_once DIR_SYSTEM.'library/social-login/linkedin/http.php';
		require_once DIR_SYSTEM.'library/social-login/linkedin/oauth_client.php';
		
		
		$client = new oauth_client_class;
		
		$client->debug = false;
		$client->debug_http = true;
		$client->redirect_uri = $this->url->link('account/awesome_social_login_ozxmod/linkedinlogin', '', 'SSL');
	
		$client->client_id = $this->config->get('awesome_social_login_ozxmod_linkedinapikey'); 
		$application_line = __LINE__;
		$client->client_secret = $this->config->get('awesome_social_login_ozxmod_linkedinapisecret');
		$client->scope = 'r_basicprofile r_emailaddress';
		
		if(($success = $client->Initialize()))
		{	
			if(($success = $client->Process()))
			{
				if (strlen($client->authorization_error)) {
			      $client->error = $client->authorization_error;
			      $success = false;
			    } elseif (strlen($client->access_token)) {
			      $success = $client->CallAPI(
								'http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 
								'GET', array(
									'format'=>'json'
								), array('FailOnAccessError'=>true), $user);
			    }
			}
			
			$success = $client->Finalize($success);

		}
		
		if($client->exit)
			exit;
			
		if(strlen($client->authorization_error))
		{
			$client->error = $client->authorization_error;
			$success = false;
		}
		
		$loc = $this->url->link("account/account", "", 'SSL');
		if(isset($_SERVER['HTTP_REFERER']))
			$loc = $_SERVER['HTTP_REFERER'];

		$this->load->model('account/customer');
		// Checking email id if already registered
		$email = $user->emailAddress;
		$password = $this->get_password($email);

		if($this->customer->login($email, $password, true)){
			$this->response->redirect($loc);
		}

		$email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($email)) . "'");

		if($email_query->num_rows){
			//$this->model_account_customer->editPassword($email, $password);
			if($this->customer->login($email, $password, true)){
				$this->response->redirect($loc);
			}
		} else {

			$f_name = $user->firstName;
			$l_name = $user->lastName;
				
			$config_customer_approval = $this->config->get('config_customer_approval');
			$this->config->set('config_customer_approval',0);
				
			$this->request->post['email'] = $email;

			$add_data=array();
			$add_data['email'] = $email;
			$add_data['password'] = $password;
			$add_data['firstname'] = $f_name;
			$add_data['lastname'] = $l_name;
			$add_data['fax'] = '';
			$add_data['telephone'] = '';
			$add_data['company'] = '';
			$add_data['company_id'] = '';
			$add_data['tax_id'] = '';
			$add_data['address_1'] = '';
			$add_data['address_2'] = '';
			$add_data['city'] = '';
			$add_data['city_id'] = '';
			$add_data['postcode'] = '';
			$add_data['country_id'] = 0;
			$add_data['zone_id'] = 0;
				
			$this->model_account_customer->addCustomer($add_data);
			$this->config->set('config_customer_approval',$config_customer_approval);

			if($this->customer->login($email, $password, true)){
				// Delete address
				$this->deleteAddress();
				unset($this->session->data['guest']);
				$this->response->redirect($loc);
			}
		}
		
	}
	
	// End LinkedIn Login
	
	private function get_password($str) {
		$password = 'newpassword';
		$password.=substr('74993889fa88dc03c2e6b83bab88e845',0,3).substr($str,0,3).substr('74993889fa88dc03c2e6b83bab88e845',-3).substr($str,-3);
		return strtolower($password);
	}
	
	private function get_twitpassword($twit_id) {
		$password = 'newpassword';
		$password.=substr('74993889fa88dc03c2e6b83bab88e845',0,3).substr($twit_id,0,3).substr('74993889fa88dc03c2e6b83bab88e845',-3).substr($twit_id,-3);
		return strtolower($password);
	}
	
	private function clean_decode($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);
				$data[$this->clean_decode($key)] = $this->clean_decode($value);
			}
		} else {
			$data = htmlspecialchars_decode($data, ENT_COMPAT);
		}
	
		return $data;
	}
	
	private function deleteAddress(){
		$customer_id = $this->session->data['customer_id'];
		$this->db->query("DELETE FROM ".DB_PREFIX."address WHERE customer_id = '".(int)$customer_id."' AND country_id=0 AND zone_id = 0 ");	
	}

}

/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
* It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/
