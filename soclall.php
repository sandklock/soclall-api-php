<?php

	class SoclAll{
	
		private $_app_id;
		private $_app_secret;
		private $_network;
		private $_sk_token;
		private $_login_url = 'http://localhost/plurk1/login';
		private $_service_url = 'http://localhost/plurk1/service';
	
		public function __construct($app_id,$app_secret,$network){
			
			$this->_app_id = $app_id;
			$this->_app_secret = $app_secret;
			$this->_network = strtolower($network);
			
		}
		
		public function setSkToken($token){
			$this->_sk_token = $token;
		}
		
		public function getLoginUrl(){
		
			$url = $this->_login_url.'/'.$this->_network.'?app_id='.$this->_app_id.'&gurl=true';
		
			$response = $this->makeRequest($url);
			
			return !empty($response['login_url']) ? $response['login_url'] : $response ;
		
		}
		
		public function getInfo(){
		
			//$data = $this->getDataToSign('getinfo');
			//$sig = $this->signRequest($data,$this->_app_secret);
			
			//$url = $this->_service_url.'/'.$this->_network.'/getinfo?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$this->_network.'/getinfo?sk_token='.$this->_sk_token;
			
			//return $url;
			
			$response = $this->makeRequest($url);
			
			return $response;
		
		}
		
		public function getFriends(){
		
			//$data = $this->getDataToSign('getfriend');
			//$sig = $this->signRequest($data,$this->_app_secret);
		
			//$url = $this->_service_url.'/'.$this->_network.'/getfriend?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$this->_network.'/getfriend?sk_token='.$this->_sk_token;
			
			$response = $this->makeRequest($url);
			
			return $response;
			
		}
		
		public function postStream($message){
		
			$params = array(
				'message' => $message,
			);
			
			if($this->_network == 'plurk')
				$params['qualifier'] = 'shares';
			if($this->_network == 'tumblr')
				$params['type'] = 'text';
			if($this->_network == 'linkedin')
				$params['type'] = 'comment';
	
			//$data = $this->getDataToSign('poststream',$params);
			//$sig = $this->signRequest($data,$this->_app_secret);
		
			//$url = $this->_service_url.'/'.$this->_network.'/poststream?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$this->_network.'/poststream?sk_token='.$this->_sk_token;
			
			$response = $this->makeRequest($url,$params,true);
			
			return $response;
		}
		
		public function sendMessage($message,$friends,$title = ''){
		
			if(!is_array($friends))
				exit('wrong type . type of second parameter must be an array');
		
			$params = array(
				'friend_id' => implode(',',$friends),
				'message' => $message,
			);
		
			if($this->_network == 'linkedin' || $this->_network == 'tumblr'){
				if(empty($title))
					exit('sending message on '.$this->_network.' is required title parameter');
				$params['title'] = $title;
			}
			
			//$data = $this->getDataToSign('sendmessage',$params);
			//$sig = $this->signRequest($data,$this->_app_secret);
			
			//$url = $this->_service_url.'/'.$this->_network.'/sendmessage?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$this->_network.'/sendmessage?sk_token='.$this->_sk_token;
			
			$response = $this->makeRequest($url,$params,true);
			
			return $response;
		}
		
		private function getDataToSign($method,$params=''){
			$data = array(
				'network' => $this->_network,
				'sk_token' => $this->_sk_token,
				'method' => $method,
			);
			
			if(!empty($params))
				$data = array_merge($data,$params);
			
			return $data;
		}
		
		private function makeRequest($url,$params='',$post = false){
		
			if(!$post){
				$context = stream_context_create(array(
					'http' => array(
						'method' => 'GET',
						'ignore_errors' => true,
					),
				));
			}else{
				$context = stream_context_create(array(
					'http' => array(
						'method' => 'POST',
						'header' => 'Content-type: application/x-www-form-urlencoded',
						'content' => http_build_query($params),
						'ignore_errors' => true,
					),
				));
			}
			
			$response = file_get_contents($url,false,$context);
			
			return json_decode($response,true);
		
		}
		
		private function signRequest($data,$app_secret){
		
			ksort($data);
		
			$str_data = '';
			
			foreach($data as $key=>$value)
				$str_data .= "$key=$value";
		
			return md5($app_secret.$str_data);
		}
		
	}

?>