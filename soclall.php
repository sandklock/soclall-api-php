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
		
		//	$url = $this->_login_url.'/'.$network.'?app_id='.$this->_app_id.'&gurl=true';
		
		//	$response = $this->makeRequest($url);
			
		//	return !empty($response['login_url']) ? $response['login_url'] : $response ;
		
			return $this->_login_url.'/'.$network.'?app_id='.$this->_app_id;
		}
		
		public function getInfo($network,$token){
		
			//$data = $this->getDataToSign($network,'getinfo',$token);
			//$sig = $this->signRequest($data,$this->_app_secret);
			
			//$url = $this->_service_url.'/'.$this->_network.'/getinfo?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$network.'/getinfo?sk_token='.$token;
			
			$response = $this->makeRequest($url);
			
			return $response;
		
		}
		
		public function getFriends($network,$token){
		
			//$data = $this->getDataToSign($network,'getfriend',$token);
			//$sig = $this->signRequest($data,$this->_app_secret);
		
			//$url = $this->_service_url.'/'.$this->_network.'/getfriend?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$network.'/getfriend?sk_token='.$token;
			
			$response = $this->makeRequest($url);
			
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
	
			//$data = $this->getDataToSign($network,'poststream',$token,$params);
			//$sig = $this->signRequest($data,$this->_app_secret);
		
			//$url = $this->_service_url.'/'.$this->_network.'/poststream?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$network.'/poststream?sk_token='.$token;
			
			$response = $this->makeRequest($url,$params,true);
			
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
			
			//$data = $this->getDataToSign($network,'sendmessage',$token,$params);
			//$sig = $this->signRequest($data,$this->_app_secret);
			
			//$url = $this->_service_url.'/'.$this->_network.'/sendmessage?sk_token='.$this->_sk_token.'&sig='.$sig;
			$url = $this->_service_url.'/'.$network.'/sendmessage?sk_token='.$token;
			
			$response = $this->makeRequest($url,$params,true);
			
			return $response;
		}
		
		private function getDataToSign($network,$method,$token,$params=''){
			$data = array(
				'network' => $network,
				'sk_token' => $token,
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
		
		private function signRequest($data){
		
			ksort($data);
		
			$str_data = '';
			
			foreach($data as $key=>$value)
				$str_data .= "$key=$value";
		
			return md5($this->_app_secret.$str_data);
		}
		
	}

?>