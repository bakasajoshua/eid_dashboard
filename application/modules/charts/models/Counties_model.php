<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Counties_model extends MY_Model
{
	function __construct()
	{
		parent:: __construct();;
	}

	function counties_details($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);
		$sql = "CALL `proc_get_eid_countys_details`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		foreach ($result as $key => $value) {
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['county'].'</td>';
			$table .= '<td>'.number_format(round($value['sitessending'])).'</td>';
			$table .= '<td>'.number_format($value['alltests']).'</td>';
			if ($year > '2015') {
				$table .= '<td>'.number_format($value['pmtctneed']).'</td>';
			} else {
				$table .= '<td>0</td>';
			}
			$table .= '<td>'.number_format($value['actualinfants']).'</td>';
			$table .= '<td>'.number_format($value['positive']+$value['negative']).'</td>';
			$table .= '<td>'.number_format($value['positive']).'</td>';
			$table .= '<td>'.number_format($value['repeatspos']).'</td>';
			$table .= '<td>'.number_format($value['repeatsposPOS']).'</td>';
			$table .= '<td>'.number_format($value['confirmdna']).'</td>';
			$table .= '<td>'.number_format($value['confirmedPOS']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2w']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2wpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2mpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2mpos']).'</td>';
			$table .= '<td>'.number_format($value['medage']).'</td>';
			$table .= '<td>'.number_format($value['rejected']).'</td>';

			$table .= '</tr>';
			$count++;
		}
		
		// echo "<pre>";print_r($table);die();
		return $table;
	}

	function download_counties_details($year=NULL,$month=NULL,$to_year=null,$to_month=null){
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_countys_details`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('County', 'Average sites sending', 'All Tests', 'PMTCT Need', 'Actual Infants Tested', 'Initial PCR Tests', 'Initial PCR Positives', '2nd/3rd PCR Tests', '2nd/3rd PCR Positives', 'Confirmatory PCR Tests', 'Confirmatory PCR Positives', 'Infants < 2Weeks', 'Infants < 2Weeks  Positives', 'Infants <= 2M', 'Infants <= 2m Positives', 'Infants >= 2M', 'Infants >= 2m Positives', 'Median Age', 'Rejected');

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').' county_details.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function sub_county_outcomes($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$data['sub_county_outcomes'][0]['name'] = 'Positive';
		$data['sub_county_outcomes'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["sub_county_outcomes"][0]["data"][0]	= $count;
		$data["sub_county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sub_county_outcomes"][0]["data"][$key]	=  (int) $value['positive'];
			$data["sub_county_outcomes"][1]["data"][$key]	=  (int) $value['negative'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function sub_county_positivity($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['sub_county_outcomes'][0]['name'] = 'Positive';
		$data['sub_county_outcomes'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["sub_county_outcomes"][0]["data"][0]	= $count;
		$data["sub_county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sub_county_outcomes"][0]["data"][$key]	=  (int) $value['positive'];
			$data["sub_county_outcomes"][1]["data"][$key]	=  (int) $value['negative'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county_subcounties_details($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_county_subcounties_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['subcounty'].'</td>';
			$table .= '<td>'.number_format(round($value['sitessending'])).'</td>';
			$table .= '<td>'.number_format($value['alltests']).'</td>';
			$table .= '<td>'.number_format($value['actualinfants']).'</td>';
			$table .= '<td>'.number_format($value['positive']+$value['negative']).'</td>';
			$table .= '<td>'.number_format($value['positive']).'</td>';
			$table .= '<td>'.number_format($value['repeatspos']).'</td>';
			$table .= '<td>'.number_format($value['repeatsposPOS']).'</td>';
			$table .= '<td>'.number_format($value['confirmdna']).'</td>';
			$table .= '<td>'.number_format($value['confirmedPOS']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2w']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2wpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2mpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2mpos']).'</td>';
			$table .= '<td>'.number_format($value['medage']).'</td>';
			$table .= '<td>'.number_format($value['rejected']).'</td>';
			$table .= '</tr>';
			$count++;
		}
		

		return $table;
	}

	function download_county_subcounty_outcomes($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_county_subcounties_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Subcounty', 'County',  'All Tests', 'Average Sites Sending', 'Actual Infants Tested', 'Initial PCR Tests', 'Initial PCR Positives', '2nd/3rd PCR Tests', '2nd/3rd PCR Positives', 'Confirmatory PCR Tests', 'Confirmatory PCR Positives', 'Infants < 2Weeks', 'Infants < 2Weeks  Positives', 'Infants <= 2M', 'Infants <= 2m Positives', 'Infants >= 2M', 'Infants >= 2m Positives', 'Median Age', 'Rejected');

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'county_subcounty_details.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}

	function county_partners_details($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_county_partners_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		foreach ($result as $key => $value) {
			if ($value['partner'] == NULL || $value['partner'] == 'NULL') {
				$value['partner'] = 'No Partner';
			}
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['partner'].'</td>';
			$table .= '<td>'.number_format($value['alltests']).'</td>';
			$table .= '<td>'.number_format($value['actualinfants']).'</td>';
			$table .= '<td>'.number_format($value['positive']+$value['negative']).'</td>';
			$table .= '<td>'.number_format($value['positive']).'</td>';
			$table .= '<td>'.number_format($value['repeatspos']).'</td>';
			$table .= '<td>'.number_format($value['repeatsposPOS']).'</td>';
			$table .= '<td>'.number_format($value['confirmdna']).'</td>';
			$table .= '<td>'.number_format($value['confirmedPOS']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2w']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2wpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2mpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2mpos']).'</td>';
			$table .= '<td>'.number_format($value['medage']).'</td>';
			$table .= '<td>'.number_format($value['rejected']).'</td>';
			$table .= '</tr>';
			$count++;
		}
		

		return $table;
	}

	function county_facilities_details($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$type = 1;

		$sql = "CALL `proc_get_eid_sites_details`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$county."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		foreach ($result as $key => $value) {
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['facility'].'</td>';
			$table .= '<td>'.$value['facilitycode'].'</td>';
			$table .= '<td>'.$value['subcounty'].'</td>';
			$table .= '<td>'.number_format($value['alltests']).'</td>';
			$table .= '<td>'.number_format($value['actualinfants']).'</td>';
			$table .= '<td>'.number_format($value['positive']+$value['negative']).'</td>';
			$table .= '<td>'.number_format($value['positive']).'</td>';
			$table .= '<td>'.number_format($value['repeatspos']).'</td>';
			$table .= '<td>'.number_format($value['repeatsposPOS']).'</td>';
			$table .= '<td>'.number_format($value['confirmdna']).'</td>';
			$table .= '<td>'.number_format($value['confirmedPOS']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2w']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2wpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2mpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2mpos']).'</td>';
			$table .= '<td>'.number_format($value['medage']).'</td>';
			$table .= '<td>'.number_format($value['rejected']).'</td>';
			$table .= '</tr>';
			$count++;
		}
		

		return $table;
	}

	function download_county_partners_outcomes($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_county_partners_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Partner', 'County', 'All Tests', 'Actual Infants Tested', 'Initial PCR Tests', 'Initial PCR Positives', '2nd/3rd PCR Tests', '2nd/3rd PCR Positives', 'Confirmatory PCR Tests', 'Confirmatory PCR Positives', 'Infants < 2Weeks', 'Infants < 2Weeks  Positives', 'Infants <= 2M', 'Infants <= 2m Positives', 'Infants >= 2M', 'Infants >= 2m Positives', 'Median Age', 'Rejected');

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'county_partners_details.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}


	// function country_tests($year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_suppression`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
				
	// 			$data[$value['id']]['value'] = $value['tests'];
				
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }

	// function country_suppression($year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_suppression`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
	// 			if($value['tests'] == 0){
	// 				$data[$value['id']]['value'] = 0;
	// 			}
	// 			else{
	// 				$data[$value['id']]['value'] = round((int) $value['suppressed'] / $value['tests'] * 100);
	// 			}
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }

	// function country_non_suppression($year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_non_suppression`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
	// 			if($value['tests'] == 0){
	// 				$data[$value['id']]['value'] = 0;
	// 			}
	// 			else{
	// 				$data[$value['id']]['value'] = 
	// 				round((int) $value['non_suppressed'] / $value['tests'] * 100);
	// 			}
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }

	// function country_rejects($year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_rejected`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
	// 			if($value['tests'] == 0){
	// 				$data[$value['id']]['value'] = 0;
	// 			}
	// 			else{
	// 				$data[$value['id']]['value'] = round((int) $value['rejected'] / $value['tests'] * 100);
	// 			}
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }

	// function country_pregnant($year=NULL,$month=NULL)
	// {

	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = $this->session->userdata('filter_month');
	// 		}else {
	// 			$month = 0;
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_pregnant_women`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
				
	// 			$data[$value['id']]['value'] = $value['tests'];
				
				
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }

	// function country_lactating($year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_lactating_women`('".$year."','".$month."')";
		
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data;
	// 	foreach ($result as $key => $value) {
			
	// 			$data[$value['id']]['id'] = $value['id'];
				
	// 			$data[$value['id']]['value'] = $value['tests'];
				
				
			
	// 	}
	// 	 // echo "<pre>";print_r($data);die();
	// 	return $data;

	// }		

	// function county_details($county=NULL,$year=NULL,$month=NULL)
	// {
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
		
	// 	$sql = "CALL `proc_get_county_partner_details`('".$county."','".$year."','".$month."')";

	// 	$result = $this->db->query($sql)->result_array();
		
	// 	$data;
	// 	$i = 0;

	// 	foreach ($result as $key => $value) {
			
	// 		$data[$i]['partner'] = $value['partner'];
	// 		$data[$i]['facility'] = $value['facility'];
	// 		$data[$i]['tests'] = $value['tests'];

	// 		if($value['tests'] == 0){
	// 				$data[$i]['suppressed'] = 0;
	// 				$data[$i]['non_suppressed'] = 0;
	// 				$data[$i]['rejected'] = $value['rejected'];
	// 			}
	// 		else{
	// 			$data[$i]['suppressed'] = $value['suppressed'] . " (" . round((int) $value['suppressed'] / $value['tests'] * 100) . "%)";
	// 			$data[$i]['non_suppressed'] = $value['non_suppressed'] . " (" . round((int) $value['non_suppressed'] / $value['tests'] * 100) . "%)";
	// 			$data[$i]['rejected'] = $value['rejected'] . " (" . round((int) $value['rejected'] / $value['tests'] * 100) . "%)";

	// 		}
			
			
	// 		$data[$i]['adults'] = $value['adults'];
	// 		$data[$i]['children'] = $value['children'];
			
	// 		$i++;
	// 	}		
	// 	$table = '';
	// 	foreach ($data as $key => $value) {
	// 		$table .= '<tr>';
	// 		$table .= '<td>'.$value['partner'].'</td>';
	// 		$table .= '<td>'.$value['facility'].'</td>';
	// 		$table .= '<td>'.$value['tests'].'</td>';
	// 		$table .= '<td>'.$value['suppressed'].'</td>';
	// 		$table .= '<td>'.$value['non_suppressed'].'</td>';
	// 		$table .= '<td>'.$value['rejected'].'</td>';
	// 		$table .= '<td>'.$value['adults'].'</td>';
	// 		$table .= '<td>'.$value['children'].'</td>';
	// 		$table .= '</tr>';
	// 	}

	// 	return $table;
	// }

	
	
	
}
?>
