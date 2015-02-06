<?php

	class SoclAll{
	
		private $_app_id;
		private $_app_secret;
		private $_login_url = 'http://test.soclall.com/login';
		private $_service_url = 'http://test.soclall.com/';
	
		public function __construct($app_id,$app_secret){
			
			$this->_app_id = $app_id;
			$this->_app_secret = $app_secret;			
		}
		
		public function getLoginUrl($network,$callback){

			$param = array(
				'app_id' => $this->_app_id,
				'callback' => $callback,
			);
		
			return $this->_login_url.'/'.$network.'?'.http_build_query($param);
		}
		
		public function getUser($token){
		
			$bodyParams = $this->getParams('user',$token);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		
		}
		
		public function getFriends($token){
		
			$bodyParams = $this->getParams('friends',$token);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
			
		}
		
		public function postStream($token,$message){
		
			$params = array(
				'message' => $message,
			);
	
			$bodyParams = $this->getParams('publish',$token,$params);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		}
		
		public function sendMessage($token,$message,$friends,$title = ''){
		
			if(!is_array($friends))
				exit('wrong type . type of second parameter must be an array');
		
			$params = array(
				'friend_id' => implode(',',$friends),
				'message' => $message,
			);
			
			if(!empty($title))
				$params['title'] = $title;
			
			$bodyParams = $this->getParams('message',$token,$params);
			
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