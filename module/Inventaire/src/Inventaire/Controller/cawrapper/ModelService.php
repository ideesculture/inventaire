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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'BaseServiceClient.php');

class ModelService extends BaseServiceClient {
	# ----------------------------------------------
	public function __construct($ps_base_url,$ps_table){
		parent::__construct($ps_base_url,"model");

		$this->setRequestMethod("GET");
		$this->setTable($ps_table);
	}
	# ----------------------------------------------
}