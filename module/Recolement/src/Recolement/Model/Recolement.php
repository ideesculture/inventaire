<?php
namespace Recolement\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Recolement
{
	
	private function curlWs($username, $password, $url, $request)
	{
		// initialisation de la session
		$ch = curl_init();
		$baseurl = str_replace("http://", "http://".$username.":".$password."@", $url);
		// configuration des options
		curl_setopt($ch, CURLOPT_URL, $baseurl.$request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
		
		$res = curl_exec($ch);
		curl_close($ch);
		
		// exécution de la session
		if ($res !== false) {
			return json_decode($res);
		} else {
			return false;
		}
		
	}
	public function caWsListeCampagnes(array $caWsConfig)
	{
		$json = $this->curlWs(
				$caWsConfig["username"],
				$caWsConfig["password"], 
				$caWsConfig["ca_service_url"], 
				"/find/ca_occurrences?q=ca_occurrences.type_id:108&pretty=1"); 
		return $json->results;
	}	
	
	public function caWsInfoCampagne(array $caWsConfig, $id)
	{
		$request = "/item/ca_occurrences/id/".$id;
		$json = $this->curlWs(
				$caWsConfig["username"],
				$caWsConfig["password"],
				$caWsConfig["ca_service_url"],
				$request
				);
		return $json;
	}
}