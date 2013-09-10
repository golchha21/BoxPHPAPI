<?php
	include('library/BoxAPI.class.php');

	$client_id		= 'CLIENT ID';
	$client_secret 	= 'CLIENT SECRET';
	$redirect_uri 	= 'REDIRECT URL';
	
	$box = new Box_API($client_id, $client_secret, $redirect_uri);
	
	if(!$box->load_token()){
		if(isset($_GET['code'])){
			$token = $box->get_token($_GET['code'], true);
			if($box->write_token($token, 'file')){
				$box->load_token();
			}
		} else {
			$box->get_code();
		}
	}
	
	// User details
	$box->get_user());
	
	// Get folder details
	$box->get_folder_details('FOLDER ID'));

	// Get folder items list
	$box->get_folder_items('FOLDER ID'));
	
	// All folders in particular folder
	$box->get_folders('FOLDER ID'));
	
	// All Files in a particular folder
	$box->get_files('FOLDER ID'));
	
	// Get folder collaborators list
	$box->get_folder_collaborators('FOLDER ID'));
	
	// Update folder details
	$details['name'] = 'NEW FOLDER NAME';
	$box->update_folder('FOLDER ID', $details));
	
	// Get file details
	$box->get_file_details('FILE ID'));
	
	// Upload file
	$box->put_file('RELATIVE FILE URL', '0'));
	
	// Update file details
	$details['name'] = 'NEW FILE NAME';
	$details['description'] = 'NEW DESCRIPTION FOR THE FILE';
	$box->update_file('FILE ID', $details));
?>