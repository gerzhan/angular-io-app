<?php

/**
 * Account - Handle core Account level operations
 *
 * PHP version 5.4
 *
 * @category  PHP
 * @package   Angular.io
 * @author    will Farrell <iam@willfarrell.ca>
 * @copyright 2000-2013 Farrell Labs
 * @license   http://angulario.com
 * @version   0.0.1
 * @link      http://angulario.com
 *
 * @access protected
 */

class Company extends Core {
	private $table = 'companies';
	
	/**
	 * Constructs a Company object.
	 */
	function __construct() {
		global $session;
		parent::__construct();
		
		$this->session = $session;
	}
	
	/**
	 * Destructs a Company object.
	 *
	 * @return void
	 */
	function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Search for companies
	 *
	 * @param string $keyword Query string
	 * @param int $limit      Max number of results
	 * @return array
	 *
	 * @url GET search
	 * @url GET search/{keyword}
	 * @url GET search/{keyword}/{limit}
	 * @access protected
	 */
	function search($keyword=NULL, $limit=NULL) {
		if ($limit && !is_int($limit)) return;
		if (!$limit) $limit = 10;
		$return = array();
		
		$this->console->log("test");
		
		$query = "SELECT company_ID, company_name, company_url, company_phone" //
				." FROM companies C"
				." WHERE"
				." (company_name LIKE '%{{keyword}}%' OR company_details LIKE '%{{keyword}}%' OR company_url LIKE '%{{keyword}}%')"
				." LIMIT 0,{{limit}}";
		$companies = $this->db->query($query, array('keyword' => $keyword, 'limit' => $limit));
		while ($companies && $company = $this->db->fetch_assoc($companies)) {
			$return[] = $company;
		}

		return $return;
	}
	
	/**
	 * get a list of users or user for a company
	 *
	 * @param int $user_ID
	 * @return array
	 *
	 * @url GET user
	 * @url GET user/{user_ID}	
	 * @access protected
	 */
	function get_user($user_ID=NULL) {
		$return = array();
		
		// Check permissions
		/*if(!$this->permission->check(array("user_ID" => $user_ID))) {
			return $this->permission->errorMessage();
		};*/
		
		$db_where = array('company_ID' => COMPANY_ID);
		if (!is_null($user_ID)) {
			$db_where['user_ID'] = $user_ID;
		}/* else {
			$db_where['user_ID'] =$this->session->cookie["user_ID"];
		}*/

		$results = $this->db->select('users',
			$db_where,
			array("user_ID", "user_level", "user_username", "user_name_first", "user_name_last", "user_email", "user_phone", "user_function", "user_details", "timestamp_create")
		);
		if ($results) {
			while($user = $this->db->fetch_assoc($results, array("user_phone"))) {
				$user['user_ID'] = $user['user_ID'];
				$return[$user['user_ID']] = $user;
			}
			if (!is_null($user_ID)) {
				$return = $return[0];
			}
		}

		return $return;
	}

	/**
	 * Add new user to ones company
	 *
	 * @param array $request_data POST data
	 * @return array
	 *
	 * @url POST user
	 * @access protected
	 */
	function post_user($request_data=NULL) {
		$return = array();
		$params = array(
			"user_ID",
			"user_username",
			"user_email",
			"user_level",
			//"user_cell",
			"user_phone",
			//"user_fax",
			"user_function",
		);

		foreach ($params as $key) {
			$request_data[$key] = isset($request_data[$key]) ? $request_data[$key] : NULL;
		}
		
		// Check permissions
		/*if(!$this->permission->check($request_data)) {
			return $this->permission->errorMessage();
		};*/
		
		/*$this->filter->set_request_data($request_data);
		$this->filter->set_group_rules('users');
		if(!$this->filter->run()) {
			$return["errors"] = $this->filter->get_errors();
			return $return;
		}
		$request_data = $this->filter->get_request_data();*/
		$request_data = $this->filter->run($request_data);
		if ($this->filter->hasErrors()) { return $this->filter->getErrorsReturn(); }
		
		$user = array(
			'company_ID' => COMPANY_ID,
			//'user_username' => $request_data['user_username'],
			'user_name_first' => $request_data['user_name_first'],
			'user_name_last' => $request_data['user_name_last'],
			'user_email' => $request_data['user_email'],
			'user_level' => $request_data['user_level'],
			'user_phone' => $request_data['user_phone'],
			//'timestamp_create' => $_SERVER['REQUEST_TIME'],
			//'timestamp_update' => $_SERVER['REQUEST_TIME'],
		);

		//print_r($user);
		$user_ID = $this->db->insert('users', $user);
		
		// add user reset
		$expire_timestamp = $_SERVER['REQUEST_TIME']+360*24*60; // 60 day life
		$hash = substr(hash("sha512", $request_data['user_email']+$_SERVER['REQUEST_TIME']), 0, 16);
		
		$insert = array('user_ID' => $user_ID, 'hash' => $hash, 'expire_timestamp' => $expire_timestamp);
		$this->db->insert_update('user_reset', $insert, $insert);
		
		$this->notify->send($user_ID, 'password_reset_request_new', array("hash" => $hash), "email");
		
		return $user_ID;
	}
	
	/**
	 * user user of ones company
	 *
	 * @param array $request_data PUT data
	 * @return array
	 *
	 * @url PUT user
	 * @access protected
	 */
	function put_user($request_data=NULL) {
		$return = array();
		$params = array(
			"user_ID",
			"user_name_first",
			"user_name_last",
			//"user_email",
			"user_level",
			//"user_cell",
			"user_phone",
			//"user_fax",
			"user_function",
		);

		foreach ($params as $key) {
			$request_data[$key] = isset($request_data[$key]) ? $request_data[$key] : NULL;
		}
		
		/*$this->filter->set_request_data($request_data);
		$this->filter->set_group_rules('users');
		if(!$this->filter->run()) {
			$return["errors"] = $this->filter->get_errors();
			return $return;
		}
		$request_data = $this->filter->get_request_data();*/
		$request_data = $this->filter->run($request_data);
		if ($this->filter->hasErrors()) { return $this->filter->getErrorsReturn(); }
		
		
		// update
		$user = array(
			'user_ID' => $request_data['user_ID'],
			'company_ID' => COMPANY_ID,
			//'user_username' => $request_data['user_username'],
			'user_name_first' => $request_data['user_name_first'],
			'user_name_last' => $request_data['user_name_last'],
			//'user_email' => $request_data['user_email'],
			'user_level' => $request_data['user_level'],
			'user_phone' => $request_data['user_phone'],
			//'timestamp_create' => $_SERVER['REQUEST_TIME'],
			'timestamp_update' => $_SERVER['REQUEST_TIME'],
		);

		
		$this->db->insert_update('users', $user);
		
	}
	
	/**
	 * Get company info by username
	 *
	 * @param string $username Username of user
	 * @return array
	 *
	 * @url GET name/{username}
	 * @access protected
	 */
	function get_name($username=NULL) {
		$return = array();
		
		$request_data = $this->filter->run(array("company_username" => $username));
		if ($this->filter->hasErrors()) { return $this->filter->getErrorsReturn(); }
		
		// add in user_username check
		
		
		
		// check user_ID
		$db_where = array();
		if ($username) {
			$db_where['company_username'] = $username;
		} else {
			return $return;
		}
		$db_select = array('company_ID','company_username', 'company_name','company_url','company_phone','company_details','user_default_ID','location_default_ID');

		$results_companies = $this->db->select('companies', $db_where, $db_select);
		while($results_companies && $company = $this->db->fetch_assoc($results_companies, array("company_phone"))) {
			/*if (!is_null($user_ID) && $user['user_username'] == '') {
				$user['user_username'] = $user["user_name_first"]." ".$user["user_name_last"];
			}*/
			$return = $company;
			
			// primary user
			$results_users = $this->db->select('users',
				array('company_ID' => $company['company_ID'], 'user_ID' => $company['user_default_ID']),
				array("user_ID", "user_username", "user_name_first", "user_name_last", "user_phone", "user_function", "user_details")
			);
			while ($results_users && $user = $this->db->fetch_assoc($results_users, array("user_phone"))) {
				$user['user_ID'] = $user['user_ID'];
				$return['user'] = $user;
			}
			// get users
			/*$results = $this->db->select('users',
				array('company_ID' => COMPANY_ID),
				array("user_ID", "user_username", "user_name_first", "user_name_last", "user_email", "user_details")
			);
			while ($results && $user = $this->db->fetch_assoc($results)) {
				$return['users'][$user['user_ID']] = $user;
			}*/
			
			// primary location
			$results_locations = $this->db->select('locations',
				array('company_ID' => $company['company_ID'], 'location_ID' => $company['location_default_ID']),
				array('location_ID', 'company_ID', 'location_name', 'address_1', 'address_2', 'city', 'region_code', 'country_code', 'mail_code', 'latitude', 'longitude', 'location_phone')
			);
			while ($results_locations && $location = $this->db->fetch_assoc($results_locations, array("location_phone"))) {
				//$location['primary'] = true;
				//$location['company_ID'] =  $location['company_ID'];
				//$location['location_ID'] =  $location['location_ID'];
				//$location['latitude'] = (double) $location['latitude'];
				//$location['longitude'] = (double) $location['longitude'];
				$return['location'] = $location;
			}
			// get locations
			/*$results = $this->db->select('locations', array('company_ID' => COMPANY_ID));
			while ($results && $location = $this->db->fetch_assoc($results)) {
				$return['locations'][$location['location_ID']] = $location;
			}*/

		}
			/*if (!is_null($user_ID)) {
				$return = $return[0];
			}*/
		
		
		
		return $return;
	}
	
	/**
	 * Get company details by Company ID
	 * 
	 * @param int $company_ID Company ID
	 * @return array
	 *
	 * @url GET
	 * @url GET {company_ID}
	 * @access protected
	 */
	function get($company_ID=NULL) {
		$return = array();
		$company_ID = !$company_ID ? COMPANY_ID: $company_ID;
		
		$results = $this->db->select('companies',
			array('company_ID' => $company_ID),
			array('company_ID','company_username', 'company_name','company_url','company_phone','company_details','user_default_ID','location_default_ID')
		);
		if ($results) {
			$company = $this->db->fetch_assoc($results, array("company_phone"));
			
			$return = $company;
			
			// primary user
			$results = $this->db->select('users',
				array('company_ID' => $company['company_ID'], 'user_ID' => $company['user_default_ID']),
				array("user_ID", "user_username", "user_name_first", "user_name_last", "user_phone", "user_function", "user_details")
			);
			//echo $this->db->last_query;
			while ($results && $user = $this->db->fetch_assoc($results, array("user_phone"))) {
				$user['user_ID'] = $user['user_ID'];
				$return['user'] = $user;
			}
			// get users
			/*$results = $this->db->select('users',
				array('company_ID' => COMPANY_ID),
				array("user_ID", "user_username", "user_name_first", "user_name_last", "user_email", "user_details")
			);
			while ($results && $user = $this->db->fetch_assoc($results)) {
				$return['users'][$user['user_ID']] = $user;
			}*/
			
			// primary location
			$results = $this->db->select('locations',
				array('company_ID' => $company['company_ID'], 'location_ID' => $company['location_default_ID']),
				array('location_ID', 'company_ID', 'location_name', 'address_1', 'address_2', 'city', 'region_code', 'country_code', 'mail_code', 'latitude', 'longitude', 'location_phone')
			);
			while ($results && $location = $this->db->fetch_assoc($results, array("location_phone"))) {
				//$location['primary'] = true;
				//$location['company_ID'] =  $location['company_ID'];
				//$location['location_ID'] =  $location['location_ID'];
				//$location['latitude'] = (double) $location['latitude'];
				//$location['longitude'] = (double) $location['longitude'];
				$return['location'] = $location;
			}
			// get locations
			/*$results = $this->db->select('locations', array('company_ID' => COMPANY_ID));
			while ($results && $location = $this->db->fetch_assoc($results)) {
				$return['locations'][$location['location_ID']] = $location;
			}*/

		}
		//print_r($return);
		return $return;
	}

	/**
	 * Create a new company during onboard
	 * 
	 * @param array $request_data POST data
	 * @return NULL
	 *
	 * @url POST
	 * @access protected
	 */
	function post($request_data=NULL) {
		$params = array(
			"company_username",
			"company_name",
			"company_url",
			"company_phone",
			"company_details",
		);

		foreach ($params as $key) {
			$request_data[$key] = isset($request_data[$key]) ? $request_data[$key] : NULL;
		}
		
		// validate and sanitize
		/*$this->filter->set_request_data($request_data);
		$this->filter->set_group_rules('companies,locations,users');
		$this->filter->set_key_rules(array('company_name', 'company_type', 'address_1', 'city', 'region_code', 'country_code', 'mail_code', 'user_username', 'user_email', 'password'), 'required');
		$this->filter->set_all_rules('trim|sanitize_string', true);
		if(!$this->filter->run()) {
			$return["errors"] = $this->filter->get_errors();
			return $return;
		}
		$request_data = $this->filter->get_request_data();*/
		$request_data = $this->filter->run($request_data);
		if ($this->filter->hasErrors()) { return $this->filter->getErrorsReturn(); }
		
		// company //
		$company = array(
			"company_username"		=> $request_data["company_username"],
			"company_name"			=> $request_data["company_name"],
			"company_url"			=> $request_data["company_url"],
			"company_phone"			=> $request_data["company_phone"],
			"company_details"		=> $request_data["company_details"],
			"user_default_ID"		=> USER_ID,
			'timestamp_create' 		=> $_SERVER['REQUEST_TIME'],
			'timestamp_update' 		=> $_SERVER['REQUEST_TIME'],
		);
		$company_ID = $this->db->insert('companies', $company);

		// add to user
		$this->db->update('users', array('company_ID' => $company_ID), array('user_ID' => USER_ID));
		
		$this->session->update(array("company_ID" => $company_ID));	// add company_ID into session
		
		return $company_ID;
	}
	
	/**
	 * update company details
	 * 
	 * @param array $request_data PUT data
	 * @return NULL
	 *
	 * @url PUT
	 * @access protected
	 */
	function put($request_data=NULL) {
		$alerts = array();
		$params = array(
			// company
			"company_username",
			"company_name",
			"company_url",
			"company_details",
			"company_phone",
			"user_default_ID",
			"location_default_ID",
		);

		foreach ($params as $key) {
			$request_data[$key] = isset($request_data[$key]) ? $request_data[$key] : NULL;
		}
		
		$request_data = $this->filter->run($request_data);
		if ($this->filter->hasErrors()) { return $this->filter->getErrorsReturn(); }
		
		// company //
		$company = array(
			"company_ID"			=> COMPANY_ID,
			"company_username"		=> $request_data["company_username"],
			"company_name"			=> $request_data["company_name"],
			"company_url"			=> $request_data["company_url"],
			"company_phone"			=> $request_data["company_phone"],
			"company_details"		=> $request_data["company_details"],
			"user_default_ID"		=> $request_data["user_default_ID"],
			"location_default_ID"	=> $request_data["location_default_ID"],
			//'timestamp_create' 		=> $_SERVER['REQUEST_TIME'],
			'timestamp_update' 		=> $_SERVER['REQUEST_TIME'],
		);

		$this->db->insert_update('companies', $company, $company);

		return TRUE;
	}

}

?>