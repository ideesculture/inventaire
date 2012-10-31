<?php
/** 
 *  Copyright 2012 Stefan Keidel <stefan@whirl-i-gig.com>
 *   
 *  This file is part of ca_service_api_wrapper_php.
 *
 *  ca_service_api_wrapper_php is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  ca_service_api_wrapper_php is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with ca_service_api_wrapper_php.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'ServiceResult.php');

abstract class BaseServiceClient {
	# ----------------------------------------------
	private $opa_get_parameters;
	private $opa_request_body;
	private $ops_request_method;
	private $ops_service_url;
	private $ops_table;
	# ----------------------------------------------
	public function __construct($ps_base_url,$ps_service){
		$this->ops_service_url = $ps_base_url."/service.php/".$ps_service;

		$this->opa_get_parameters = array();
		$this->opa_request_body = array();
		$this->ops_request_method = "";
		$this->ops_table = "";
	}
	# ----------------------------------------------
	public function setRequestMethod($ps_method){
		if(!in_array($ps_method,array("GET","PUT","DELETE"))){
			return false;
		}
		$this->ops_request_method = $ps_method;
	}
	# ----------------------------------------------
	public function getRequestMethod(){
		return $this->ops_request_method;
	}
	# ----------------------------------------------
	public function setRequestBody($pa_request_body){
		$this->opa_request_body = $pa_request_body;
	}
	# ----------------------------------------------
	public function getRequestBody(){
		return $this->opa_request_body;
	}
	# ----------------------------------------------
	public function setTable($ps_table){
		$this->ops_table = $ps_table;
	}
	# ----------------------------------------------
	public function getTable(){
		return $this->ops_table;
	}
	# ----------------------------------------------
	public function addGetParameter($ps_param_name,$ps_value){
		$this->opa_get_parameters[$ps_param_name] = $ps_value;
	}
	# ----------------------------------------------
	public function getAllGetParameters(){
		return $this->opa_get_parameters;
	}
	# ----------------------------------------------
	public function getGetParameter($ps_param_name){
		return $this->opa_get_parameters[$ps_param_name];
	}
	# ----------------------------------------------
	public function request(){
		if(!($vs_method = $this->getRequestMethod())){
			return false;
		}

		$va_get = array();
		foreach($this->getAllGetParameters() as $vs_name => $vs_val){
			$va_get[] = $vs_name."=".$vs_val;
		}

		$vs_get = sizeof($va_get)>0 ? "?".join("&",$va_get) : "";

		$vo_handle = curl_init($this->ops_service_url."/".$this->getTable()."/".$vs_get);

		curl_setopt($vo_handle, CURLOPT_CUSTOMREQUEST, $vs_method);
		curl_setopt($vo_handle, CURLOPT_RETURNTRANSFER, true);

		$va_body = $this->getRequestBody();
		if(is_array($va_body) && sizeof($va_body)>0){
			curl_setopt($vo_handle, CURLOPT_POSTFIELDS, json_encode($va_body));
		}

		$vs_exec = curl_exec($vo_handle);
		curl_close($vo_handle);
		
		return new ServiceResult($vs_exec);
	}
	# ----------------------------------------------
}

