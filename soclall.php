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
		
		public function getLoginUrl($network){	
			return $this->_login_url.'/'.$network.'?app_id='.$this->_app_id;
		}
		
		public function getInfo($network,$token){
		
			$bodyParams = $this->getParams($network,'getinfo',$token);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		
		}
		
		public function getFriends($network,$token){
		
			$bodyParams = $this->getParams($network,'getfriend',$token);
			
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
	
			$bodyParams = $this->getParams($network,'poststream',$token,$params);
			
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
			
			$bodyParams = $this->getParams($network,'sendmessage',$token,$params);
			
			$response = $this->makeRequest($bodyParams);
			
			return $response;
		}
		
		private function getParams($network,$method,$token,$params=''){
			$data = array(
				'network' => $network,
				'sk_token' => $token,
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