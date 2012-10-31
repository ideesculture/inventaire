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

class ServiceResult {
	# ----------------------------------------------
	private $opa_data;
	private $opb_ok;
	private $opa_errors;
	# ----------------------------------------------
	public function __construct($ps_data){
		$this->opa_data = json_decode($ps_data,true);
		$this->opb_ok = (isset($this->opa_data["ok"]) && $this->opa_data["ok"]);
		unset($this->opa_data["ok"]);

		if(is_array($this->opa_data["errors"]) && sizeof($this->opa_data["errors"])>0){
			$this->opa_errors = $this->opa_data["errors"];
		} else {
			$this->opa_errors = array();
		}

	}
	# ----------------------------------------------
	public function getRawData(){
		return $this->opa_data;
	}
	# ----------------------------------------------
	public function isOk(){
		return $this->opb_ok;
	}
	# ----------------------------------------------
	public function getErrors(){
		return $this->opa_errors;
	}
	# ----------------------------------------------
}