<?php

	class SoclAll{
	
		private $_app_id;
		private $_app_secret;
		private $_login_url = 'http://api.soclall.com/login';
		private $_service_url = 'http://api.soclall.com/service';
	
		public function __construct($app_id,$app_secret){
			
			$this->_app_id = $app_id;
			$this->_app_secret = $app_secret;			
		}
		
		public function getLoginUrl($network,$callback){

			$param = array(
				'app_id' => $network,
				'callback' => urlencode($callback),
			);
		
			return $this->_login_url.'/'.$network.'?'.http_build_query($param);
		}
		
		public function getUser($token){
		
			$bodyParams = $this->getParams('getuser',$token);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		
		}
		
		public function getFriends($token){
		
			$bodyParams = $this->getParams('getfriends',$token);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
			
		}
		
		public function postStream($network,$token,$message){
		
			$params = array(
				'message' => $message,
			);
			
			if($network == 'plurk')
				$params['qualifier'] = 'shares';
			if($network == 'tumblr')
				$params['type'] = 'text';
			if($network == 'linkedin')
				$params['type'] = 'comment';
	
			$bodyParams = $this->getParams('poststream',$token,$params);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		}
		
		public function sendMessage($network,$token,$message,$friends,$title = ''){
		
			if(!is_array($friends))
				exit('wrong type . type of second parameter must be an array');
		
			$params = array(
				'friend_id' => implode(',',$friends),
				'message' => $message,
			);
		
			if($network == 'linkedin' || $network == 'tumblr'){
				if(empty($title))
					exit('sending message on '.$network.' is required title parameter');
				$params['title'] = $title;
			}
			
			$bodyParams = $this->getParams('sendmessage',$token,$params);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		}
		
		private function getParams($method,$token,$params=''){
			$data = array(
				'token' => $token,
				'method' => $method,
			);
			
			if(!empty($params))
				$data = array_merge($data,$params);
			
			return $data;
		}
		
		private function makeRequest($params){
	
			//TODO: sign request here
			//$this->signRequest($params)
	
			$queryParams = http_build_query($params);
		
			$context = stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => $queryParams,
				),
			));
			
			$response = file_get_contents($this->_service_url,false,$context);
			
			return json_decode($response,true);
		
		}
		
		private function signRequest($data){
		
			ksort($data);
		
			$str_data = '';
			
			foreach($data as $key=>$value)
				$str_data .= "$key=$value";
		
			return md5($this->_app_secret.$str_data);
		}
		
	}

?>