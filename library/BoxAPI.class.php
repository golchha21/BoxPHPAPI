<?php
define('_CODENAME', 'BoxPHPAPI');
define('_VERSION', '1.1.0');
define('_URL', 'https://github.com/golchha21/BoxPHPAPI');
error_reporting(E_ERROR);

class Box_API
{

    public $client_id = '';
    public $client_secret = '';
    public $redirect_uri = '';
    public $access_token = '';
    public $refresh_token = '';
    public $authorize_url = 'https://www.box.com/api/oauth2/authorize';
    public $token_url = 'https://www.box.com/api/oauth2/token';
    public $api_url = 'https://api.box.com/2.0';
    public $upload_url = 'https://upload.box.com/api/2.0';
    public $error_message = '';
    public $response_status = '';
    public $asUser = '';

    public function __construct($client_id = '', $client_secret = '', $redirect_uri = '')
    {
        if (empty($client_id) || empty($client_secret)) {
            echo 'Invalid CLIENT_ID or CLIENT_SECRET or REDIRECT_URL. Please provide CLIENT_ID, CLIENT_SECRET and REDIRECT_URL when creating an instance of the class.';
        } else {
            $this->client_id = $client_id;
            $this->client_secret = $client_secret;
            $this->redirect_uri = $redirect_uri;
        }
    }

    public function setAsUser($userID)
    {
        $this->asUser = $userID;
    }

    /* First step for authentication [Gets the code] */
    public function get_code()
    {
        if (array_key_exists('refresh_token', $_REQUEST)) {
            $this->refresh_token = $_REQUEST['refresh_token'];
        } else {
            // echo $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
            $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
            header('location: ' . $url);
            exit();
        }
    }

    /* Second step for authentication [Gets the access_token and the refresh_token] */
    public function get_token($code = '', $json = false)
    {
        $url = $this->token_url;
        if (!empty($this->refresh_token)) {
            $params = array('grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
        } else {
            $params = array('grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
        }
        if ($json) {
            return $this->post($url, $params);
        } else {
            return json_decode($this->post($url, $params), true);
        }
    }

    /* Get comments */
    public function get_comments($file)
    {
        $url = $this->build_url("/files/$file/comments");
        return json_decode($this->get($url), true);
    }

    /* Get tasks */
    public function get_tasks($file)
    {
        $url = $this->build_url("/files/$file/tasks");
        return json_decode($this->get($url), true);
    }

    public function create_user($login, $name)
    {
        $url = $this->build_url("/users");
        $params = array('login' => $login, 'name' => $name);
        return json_decode($this->post($url, json_encode($params)), true);
    }

    public function get_user_by_login($login, $complete = false)
    {
        $fields = '';
        if ($complete)
            $fields = '&fields=id,name,login,created_at,modified_at,language,space_amount,max_upload_size,status,avatar_url,space_used,can_see_managed_users,is_sync_enabled,is_external_collab_restricted,is_exempt_from_device_limits,is_exempt_from_login_verification';
        $url = $this->build_url("/users") . "&filter_term=$login" . $fields;
        return json_decode($this->get($url));
    }

    public function get_users($limit = 100, $offset = 0)
    {
        $url = $this->build_url("/users") . "&limit=$limit&offset=$offset";
        return json_decode($this->get($url));
    }

    /* Gets user details  by ID*/
    public function get_user_by_id($id)
    {
        $url = $this->build_url("/users/$id");
        return json_decode($this->get($url), true);
    }

    public function get_userID_by_login($user)
    {
        $result = $this->get_user_by_login($user->email);
        if (isset($result->entries)) {
            if (count($result->entries) == 1) {
                $user->id = $result->entries[0]->id;
            }
        }
        return $user;
    }

    public function get_enterprise_events($limit = 0, $after = "2015-06-10T00:00:00-08:00", $before = "2015-12-12T10:53:43-08:00", $event = '', $stream_position = 0)
    {
        $url = $this->build_url("/events") . "&stream_type=admin_logs&limit=$limit&created_after=$after&created_before=$before&event_type=$event&stream_position=$stream_position";
        return json_decode($this->get($url));
    }

    public function invite_user($login, $name)
    {
        $url = $this->build_url("/invites");
        $params = array('login' => $login, 'name' => $name);
        return json_decode($this->post($url, json_encode($params)), true);
    }

    private function get_groups()
    {
        $url = $this->build_url("/groups");
        return json_decode($this->get($url));
    }

    public function get_group_id($name)
    {
        $group_id = 0;
        $groups = $this->get_groups();
        foreach ($groups->entries as $group) {
            if ($group->name == $name) {
                $group_id = $group->id;
            }
        }
        return $group_id;
    }

    public function create_group($name)
    {
        $url = $this->build_url("/groups");
        $params = array('name' => $name);
        return json_decode($this->post($url, json_encode($params)), true);
    }

    public function add_user_to_group($userId, $groupId)
    {
        $url = $this->build_url("/group_memberships");
        $params = array('user' => array('id' => $userId), 'group' => array('id' => $groupId));
        return json_decode($this->post($url, json_encode($params)), true);
    }

    public function share_folder_with_user($folderId, $userId)
    {
        $url = $this->build_url("/collaborations");
        $items = array('id' => $folderId, "type" => "folder");
        $accessible_by = array("id" => $userId, "type" => "user");
        $params = array("item" => $items, "accessible_by" => $accessible_by, "role" => "viewer");
        return json_decode($this->post($url, json_encode($params)), true);
    }

    /* Gets the current user details */
    public function get_user()
    {
        $url = $this->build_url('/users/me');
        return json_decode($this->get($url), true);
    }

    /* Get the details of the mentioned folder */
    public function get_folder_details($folder, $json = false)
    {
        $url = $this->build_url("/folders/$folder");
        if ($json) {
            return $this->get($url);
        } else {
            return json_decode($this->get($url), true);
        }
    }

    /* Get the list of items in the mentioned folder */
    public function get_folder_items($folder, $json = false)
    {
        $url = $this->build_url("/folders/$folder/items");
        if ($json) {
            return $this->get($url);
        } else {
            return json_decode($this->get($url), true);
        }
    }

    /* Get the list of collaborators in the mentioned folder */
    public function get_folder_collaborators($folder, $json = false)
    {
        $url = $this->build_url("/folders/$folder/collaborations");
        if ($json) {
            return $this->get($url);
        } else {
            return json_decode($this->get($url), true);
        }
    }

    /* Lists the folders in the mentioned folder */
    public function get_folders($folder)
    {
        $return = array();
        $data = $this->get_folder_items($folder);
        foreach ($data['entries'] as $item) {
            $array = '';
            if ($item['type'] == 'folder') {
                $array = $item;
            }
            $return[] = $array;
        }
        return array_filter($return);
    }

    /* Lists the files in the mentioned folder */
    public function get_files($folder)
    {
        $return = array();
        $data = $this->get_folder_items($folder);
        foreach ($data['entries'] as $item) {
            $array = '';
            if ($item['type'] == 'file') {
                $array = $item;
            }
            $return[] = $array;
        }
        return array_filter($return);
    }

    /* Lists the files in the mentioned folder */
    public function get_links($folder)
    {
        $return = array();
        $data = $this->get_folder_items($folder);
        foreach ($data['entries'] as $item) {
            $array = '';
            if ($item['type'] == 'web_link') {
                $array = $item;
            }
            $return[] = $array;
        }
        return array_filter($return);
    }

    public function create_folder($name, $parent_id)
    {
        $url = $this->build_url("/folders");
        $params = array('name' => $name, 'parent' => array('id' => $parent_id));
        return json_decode($this->post($url, json_encode($params)), true);
    }

    /* Modifies the folder details as per the api */
    public function update_folder($folder, array $params)
    {
        $url = $this->build_url("/folders/$folder");
        return json_decode($this->put($url, $params), true);
    }

    /* Deletes a folder */
    public function delete_folder($folder, array $opts)
    {
        echo $url = $this->build_url("/folders/$folder", $opts);
        $return = json_decode($this->delete($url), true);
        if (empty($return)) {
            return 'The folder has been deleted.';
        } else {
            return $return;
        }
    }

    /* Shares a folder */
    public function share_folder($folder, array $params)
    {
        $url = $this->build_url("/folders/$folder");
        return json_decode($this->put($url, $params), true);
    }

    /* Shares a file */
    public function share_file($file, array $params)
    {
        $url = $this->build_url("/files/$file");
        return json_decode($this->put($url, $params), true);
    }

    /* Get the details of the mentioned file */
    public function get_file_details($file, $json = false)
    {
        $url = $this->build_url("/files/$file");
        if ($json) {
            return $this->get($url);
        } else {
            return json_decode($this->get($url), true);
        }
    }

    /* Uploads a file */
    public function put_file($filename, $name, $parent_id)
    {
        $url = $this->build_url('/files/content', array(), $this->upload_url);
        if (isset($name)) {
            $name = basename($filename);
        }
        $file = new CURLFile($filename);
        $params = array('file' => $file, 'name' => $name, 'parent_id' => $parent_id, 'access_token' => $this->access_token);
        return json_decode($this->post($url, $params), true);
    }

    /* Uploads a file version */
    public function put_file_version($filename, $file_id, $name = NULL)
    {
        $url = $this->build_url("/files/$file_id/content", array(), $this->upload_url);
        $file = new CURLFile($filename);
        $params = array('file' => $file, 'name' => $name, 'access_token' => $this->access_token);
        return json_decode($this->post($url, $params), true);
    }

    /* Modifies the file details as per the api */
    public function update_file($file, array $params)
    {
        $url = $this->build_url("/files/$file");
        return json_decode($this->put($url, $params), true);
    }

    /* Deletes a file */
    public function delete_file($file)
    {
        $url = $this->build_url("/files/$file");
        $return = json_decode($this->delete($url), true);
        if (empty($return)) {
            return 'The file has been deleted.';
        } else {
            return $return;
        }
    }

    /* Saves the token */
    public function write_token($token, $type = 'file')
    {
        $array = json_decode($token, true);
        if (isset($array['error'])) {
            $this->error_message = $array['error_description'];
            return false;
        } else {
            $array['timestamp'] = time();
            if ($type == 'file') {
                $fp = fopen('token.box', 'w');
                fwrite($fp, json_encode($array));
                fclose($fp);
            }
            return true;
        }
    }

    /* Reads the token */
    public function read_token($type = 'file', $json = false)
    {
        if ($type == 'file' && file_exists('token.box')) {
            $fp = fopen('token.box', 'r');
            $content = fread($fp, filesize('token.box'));
            fclose($fp);
        } else {
            return false;
        }
        if ($json) {
            return $content;
        } else {
            return json_decode($content, true);
        }
    }

    /* Loads the token */
    public function load_token()
    {
        $array = $this->read_token('file');
        if (!$array) {
            return false;
        } else {
            if (isset($array['error'])) {
                $this->error_message = $array['error_description'];
                return false;
            } elseif ($this->expired($array['expires_in'], $array['timestamp'])) {
                $this->refresh_token = $array['refresh_token'];
                $token = $this->get_token(NULL, true);
                if ($this->write_token($token, 'file')) {
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
            return true;
        }
    }

    /* Builds the URL for the call */
    private function build_url($api_func, array $opts = array(), $url = NULL)
    {
        $opts = $this->set_opts($opts);
        if (isset($url)) {
            $base = $url . $api_func . '?';
        } else {
            $base = $this->api_url . $api_func . '?';
        }
        $query_string = http_build_query($opts);
        $base = $base . $query_string;
        return $base;
    }

    /* Sets the required before building the query */
    private function set_opts(array $opts)
    {
        if (!array_key_exists('access_token', $opts)) {
            $opts['access_token'] = $this->access_token;
        }
        return $opts;
    }

    private function parse_result($res)
    {
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }

    private static function expired($expires_in, $timestamp)
    {
        $cTimestamp = time();
        if (($cTimestamp - $timestamp) >= $expires_in) {
            return true;
        } else {
            return false;
        }
    }

    /* Download a file */
    public function download_file($file_id, $destination)
    {
        $file = $this->get_file_details($file_id);
        $destination = $destination . DIRECTORY_SEPARATOR . $file['name'];
        $url = $this->build_url("/files/$file_id/content");
        return json_decode($this->download($url, $destination));
    }

    private static function download($url, $destination)
    {
        $ch = curl_init();
        $file = fopen($destination, "w+");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $file); // write curl response to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($file);
        return $data;
    }

    private function getStatus($code)
    {
        $returnedCode = array(
            100 => "Continue", 101 => "Switching Protocols", 200 => "OK", 201 => "Created", 202 => "Accepted", 203 => "Non-Authoritative Information",
            204 => "No Content", 205 => "Reset Content", 206 => "Partial Content", 300 => "Multiple Choices", 301 => "Moved Permanently", 302 => "Found", 303 => "See Other",
            304 => "Not Modified", 305 => "Use Proxy", 306 => "(Unused)", 307 => "Temporary Redirect", 400 => "Bad Request", 401 => "Unauthorized", 402 => "Payment Required",
            403 => "Forbidden", 404 => "Not Found", 405 => "Method Not Allowed", 406 => "Not Acceptable", 407 => "Proxy Authentication Required", 408 => "Request Timeout",
            409 => "Conflict", 410 => "Gone", 411 => "Length Required", 412 => "Precondition Failed", 413 => "Request Entity Too Large", 414 => "Request-URI Too Long",
            415 => "Unsupported Media Type", 416 => "Requested Range Not Satisfiable", 417 => "Expectation Failed", 500 => "Internal Server Error", 501 => "Not Implemented",
            502 => "Bad Gateway", 503 => "Service Unavailable", 504 => "Gateway Timeout", 505 => "HTTP Version Not Supported");

        $this->response_status = $code;
        $this->error_message = $returnedCode[$code];
    }

    private function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //curl_setopt($ch, CURLOPT_HEADER, 1);

        if (!empty($this->asUser)) {
            $headers = array();
            $headers[] = "as-user:$this->asUser";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $data = curl_exec($ch);
        $this->getStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        curl_close($ch);

        return $data;
    }

    private static function post($url, $params)
    {
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

    private static function put($url, array $params = array())
    {
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

    private static function delete($url, $params = '')
    {
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
