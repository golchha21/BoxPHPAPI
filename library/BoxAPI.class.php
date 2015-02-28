<?php 
	define( '_CODENAME', 'BoxPHPAPI'); 
	define( '_VERSION', '1.0.5'); 
	define( '_URL', 'https://github.com/golchha21/BoxPHPAPI');
	error_reporting(E_ERROR);
	
	class Box_API {
		
		public $client_id 		= '';
		public $client_secret 	= '';
		public $redirect_uri	= '';
		public $access_token	= '';
		public $refresh_token	= '';
		public $authorize_url 	= 'https://www.box.com/api/oauth2/authorize';
		public $token_url	 	= 'https://www.box.com/api/oauth2/token';
		public $api_url 		= 'https://api.box.com/2.0';
		public $upload_url 		= 'https://upload.box.com/api/2.0';

		public function __construct($client_id = '', $client_secret = '', $redirect_uri = '') {
			if(empty($client_id) || empty($client_secret)) {
				throw ('Invalid CLIENT_ID or CLIENT_SECRET or REDIRECT_URL. Please provide CLIENT_ID, CLIENT_SECRET and REDIRECT_URL when creating an instance of the class.');
			} else {
				$this->client_id 		= $client_id;
				$this->client_secret	= $client_secret;
				$this->redirect_uri		= $redirect_uri;
			}
		}
		
		/* First step for authentication [Gets the code] */
		public function get_code() {
			if(array_key_exists('refresh_token', $_REQUEST)) {
				$this->refresh_token = $_REQUEST['refresh_token'];
			} else {
				// echo $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
				$url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
				header('location: ' . $url);
				exit();
			}
		}
		
		/* Second step for authentication [Gets the access_token and the refresh_token] */
		public function get_token($code = '', $json = false) {
			$url = $this->token_url;
			if(!empty($this->refresh_token)){
				$params = array('grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
			} else {
				$params = array('grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
			}
			if($json){
				return $this->post($url, $params);
			} else {
				return json_decode($this->post($url, $params), true);
			}
		}
		
		/* Gets the current user details */
		public function get_user() {
			$url = $this->build_url('/users/me');
			return json_decode($this->get($url),true);
		}
		
		/* Get the details of the mentioned folder */
		public function get_folder_details($folder, $json = false) {
			$url = $this->build_url("/folders/$folder");
			if($json){	
				return $this->get($url);
			} else {
				return json_decode($this->get($url),true);
			}
		}
		
		/* Get the list of items in the mentioned folder */
		public function get_folder_items($folder, $json = false) {
			$url = $this->build_url("/folders/$folder/items");
			if($json){	
				return $this->get($url);
			} else {
				return json_decode($this->get($url),true);
			}
		}
		
		/* Get the list of collaborators in the mentioned folder */
		public function get_folder_collaborators($folder, $json = false) {
			$url = $this->build_url("/folders/$folder/collaborations");
			if($json){	
				return $this->get($url);
			} else {
				return json_decode($this->get($url),true);
			}
		}
		
		/* Lists the folders in the mentioned folder */
		public function get_folders($folder) {
			$data = $this->get_folder_items($folder);
			foreach($data['entries'] as $item){
				$array = '';
				if($item['type'] == 'folder'){
					$array = $item;
				}
				$return[] = $array;
			}
			return array_filter($return);
		}
		
		/* Lists the files in the mentioned folder */
		public function get_files($folder) {
			$data = $this->get_folder_items($folder);
			foreach($data['entries'] as $item){
				$array = '';
				if($item['type'] == 'file'){
					$array = $item;
				}
				$return[] = $array;
			}
			return array_filter($return);
		}
		
		/* Lists the files in the mentioned folder */
		public function get_links($folder) {
			$data = $this->get_folder_items($folder);
			foreach($data['entries'] as $item){
				$array = '';
				if($item['type'] == 'web_link'){
					$array = $item;
				}
				$return[] = $array;
			}
			return array_filter($return);
		}
		
		public function create_folder($name, $parent_id) {
			$url = $this->build_url("/folders");
			$params = array('name' => $name, 'parent' => array('id' => $parent_id));
			return json_decode($this->post($url, json_encode($params)), true);
		}
		
		/* Modifies the folder details as per the api */
		public function update_folder($folder, array $params) {
			$url = $this->build_url("/folders/$folder");
			return json_decode($this->put($url, $params), true);
		}
		
		/* Deletes a folder */
		public function delete_folder($folder, array $opts) {
			echo $url = $this->build_url("/folders/$folder", $opts);
			$return = json_decode($this->delete($url), true);
			if(empty($return)){
				return 'The folder has been deleted.';
			} else {
				return $return;
			}
		}
		
		/* Shares a folder */
		public function share_folder($folder, array $params) {
			$url = $this->build_url("/folders/$folder");
			return json_decode($this->put($url, $params), true);
		}
		
		/* Shares a file */
		public function share_file($file, array $params) {
			$url = $this->build_url("/files/$file");
			return json_decode($this->put($url, $params), true);
		}

		/* Get the details of the mentioned file */
		public function get_file_details($file, $json = false) {
			$url = $this->build_url("/files/$file");
			if($json){	
				return $this->get($url);
			} else {
				return json_decode($this->get($url),true);
			}
		}
		
		/* Uploads a file */
		public function put_file($filename, $parent_id) {
			$url = $this->upload_url . '/files/content';
			$params = array('filename' => "@" . realpath($filename), 'parent_id' => $parent_id, 'access_token' => $this->access_token);
			return json_decode($this->post($url, $params), true);
		}
		
		/* Modifies the file details as per the api */
		public function update_file($file, array $params) {
			$url = $this->build_url("/files/$file");
			return json_decode($this->put($url, $params), true);
		}

		/* Deletes a file */
		public function delete_file($file) {
			$url = $this->build_url("/files/$file");
			$return = json_decode($this->delete($url),true);
			if(empty($return)){
				return 'The file has been deleted.';
			} else {
				return $return;
			}
		}
		
		/* Saves the token */
		public function write_token($token, $type = 'file') {
			$array = json_decode($token, true);
			if(isset($array['error'])){
				$this->error = $array['error_description'];
				return false;
			} else {
				$array['timestamp'] = time();
				if($type == 'file'){
					$fp = fopen('token.box', 'w');
					fwrite($fp, json_encode($array));
					fclose($fp);
				}
				return true;
			}
		}
		
		/* Reads the token */
		public function read_token($type = 'file', $json = false) {
			if($type == 'file' && file_exists('token.box')){
				$fp = fopen('token.box', 'r');
				$content = fread($fp, filesize('token.box'));
				fclose($fp);
			} else {
				return false;
			}
			if($json){
				return $content;
			} else {
				return json_decode($content, true);
			}
		}
		
		/* Loads the token */
		public function load_token() {
			$array = $this->read_token('file');
			if(!$array){
				return false;
			} else {
				if(isset($array['error'])){
					$this->error = $array['error_description'];
					return false;
				} elseif($this->expired($array['expires_in'], $array['timestamp'])){
					$this->refresh_token = $array['refresh_token'];
					$token = $this->get_token(NULL, true);
					if($this->write_token($token, 'file')){
						$array = json_decode($token, true);
						$this->refresh_token = $array['refresh_token'];
						$this->access_token = $array['access_token'];
						return true;
					}
				} else {
					$this->refresh_token = $array['refresh_token'];
					$this->access_token = $array['access_token'];
					return true;
				}
			}
		}
		
		/* Builds the URL for the call */
		private function build_url($api_func, array $opts = array()) {
			$opts = $this->set_opts($opts);
			$base = $this->api_url . $api_func . '?';
			$query_string = http_build_query($opts);
			$base = $base . $query_string;
			return $base;
		}
		
		/* Sets the required before biulding the query */
		private function set_opts(array $opts) {
			if(!array_key_exists('access_token', $opts)) {
				$opts['access_token'] = $this->access_token;
			}
			return $opts;
		}
		
		private function parse_result($res) {
			$xml = simplexml_load_string($res);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			return $array;
		}
		
		private static function expired($expires_in, $timestamp) {
			$ctimestamp = time();
			if(($ctimestamp - $timestamp) >= $expires_in){
				return true;
			} else {
				return false;
			}
		}
		
		private static function get($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function post($url, $params) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function put($url, array $params = array()) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		private static function delete($url, $params = '') {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
	}