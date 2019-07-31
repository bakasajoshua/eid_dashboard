<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	function __construct()
	{
		parent:: __construct();
	}
	function resolve_month($month)
	{
		switch ($month) {
			case 1:
				$value = 'Jan';
				break;
			case 2:
				$value = 'Feb';
				break;
			case 3:
				$value = 'Mar';
				break;
			case 4:
				$value = 'Apr';
				break;
			case 5:
				$value = 'May';
				break;
			case 6:
				$value = 'Jun';
				break;
			case 7:
				$value = 'Jul';
				break;
			case 8:
				$value = 'Aug';
				break;
			case 9:
				$value = 'Sep';
				break;
			case 10:
				$value = 'Oct';
				break;
			case 11:
				$value = 'Nov';
				break;
			case 12:
				$value = 'Dec';
				break;
			default:
				$value = NULL;
				break;
		}

		return $value;

	}

	function req($url)
	{
		$this->load->library('requests/library/requests');
		$this->requests->register_autoloader();
		// $headers = array('X-Auth-Token' => 'jhWXc65gZUI=yG5ndWkpAGNsaW50b85oZWFsdGhhY2Nlc3Mub3Jn');
		$headers = array();
		$options = array('verify' => false);

		// $my_url = "http://eidapi.nascop.org/eid/ver2.0/" . $url;
		$my_url = "https://api.nascop.org/eid/ver2.0/" . $url;
		$request = $this->requests->get($my_url, $headers, $options);
		// $request = $this->requests->get($my_url);


		// return json_decode(json_encode(json_decode($request->body)), true);
		return json_decode($request->body);
	}

	public function extract_variables($year=null,$month=null,$to_year=null,$to_month=null,$others=null,$to_api=false)
	{
		$type = 0;
 
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$type = 1;
			}else {
				$month = $this->session->userdata('filter_month');
				$type = 3;
			}
		}

		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}		

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}

		$data = ['year' => $year, 'month' => $month, 'to_year' => $to_year, 'to_month' => $to_month];

		if($to_api) $data['type'] = $type;

		if(!is_array($others)) return $data;

		if(isset($others['partner'])){
			$partner = $others['partner'];
			if ($partner==null || $partner=='null') {
				$partner = $this->session->userdata('partner_filter');
			}
			$data['partner'] = $partner;
		}

		if(isset($others['county'])){
			$county = $others['county'];
			if ($county==null || $county=='null') {
				$county = $this->session->userdata('county_filter');
			}
			$data['county'] = $county;
		}

		if(isset($others['subcounty'])){
			$subcounty = $others['subcounty'];
			if ($subcounty==null || $subcounty=='null') {
				$subcounty = $this->session->userdata('sub_county_filter');
			}
			$data['subcounty'] = $subcounty;
		}

		if(isset($others['site'])){
			$site = $others['site'];
			if ($site==null || $site=='null') {
				$site = $this->session->userdata('site_filter');
			}
			$data['site'] = $site;
		}

		if(isset($others['agency_id'])){
			$agency_id = $others['agency_id'];
			if ($agency_id==null || $agency_id=='null') {
				$agency_id = $this->session->userdata('funding_agency_filter');
			}
			$data['agency_id'] = $agency_id;
		}

		if(isset($others['age_cat'])){
			$age_cat = $others['age_cat'];
			if ($age_cat==null || $age_cat=='null') {
				$age_cat = $this->session->userdata('age_category_filter');
			}
			$data['age_cat'] = $age_cat;
		}

		if(isset($others['regimen'])){
			$regimen = $others['regimen'];
			if ($regimen==null || $regimen=='null') {
				$regimen = $this->session->userdata('regimen_filter');
			}
			$data['regimen'] = $regimen;
		}

		if(isset($others['sample'])){
			$sample = $others['sample'];
			if ($sample==null || $sample=='null') {
				$sample = $this->session->userdata('sample_filter');
			}
			$data['sample'] = $sample;
		}

		if(isset($others['age'])){
			$age = $others['age'];
			if ($age==null || $age=='null') {
				$age = $this->session->userdata('age_filter');
			}
			$data['age'] = $age;
		}

		return $data;
		// call extract() to the data that returns
	}
}
?>