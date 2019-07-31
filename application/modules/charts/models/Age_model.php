<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Age_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function ages_outcomes($year=null,$month=null,$toYear=null,$toMonth=null,$county=null,$subCounty=null)
	{
		$result = $this->get_breakdown_data($year,$month,$toYear,$toMonth,$county,$subCounty);

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		//$data['outcomes'][0]['color'] = '#52B3D9';
		// $data['outcomes'][0]['color'] = '#E26A6A';
		// $data['outcomes'][1]['color'] = '#257766';

		$data['outcomes'][0]['color'] = '#E26A6A';
		$data['outcomes'][1]['color'] = '#257766';
		$data['outcomes'][2]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $value['name'];
				$data["outcomes"][0]["data"][$key]	= (int) $value['positive'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['negative'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['positive']+(int) $value['negative'])),1);
			
		}
		
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function get_breakdown_data($year=null,$month=null,$to_year=null,$to_month=null,$county=null,$subcounty=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'subcounty' => $subcounty]);
		extract($d);
		// echo "County: ".$county." and sub-county:".$subcounty;die();
		if ($county) {
			$sql = "CALL `proc_get_eid_age_data`(1, '".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else if ($subcounty) {
			$sql = "CALL `proc_get_eid_age_data`(2, '".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_age_data`(0, 0, '".$year."','".$month."','".$to_year."','".$to_month."')";
		}

		// echo "<pre>";print_r($sql);die();
		$data = $this->db->query($sql)->result_array();
		return $data;
	}


	function age_testing_trends($year=null,$age=null)
	{
		$result = $this->get_testing_trends($year, $age);

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";


		$data['outcomes'][0]['color'] = '#E26A6A';
		$data['outcomes'][1]['color'] = '#257766';
		$data['outcomes'][2]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];

				$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['neg']+(int) $value['pos'])),1);
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function download_testing_trends($year=null,$age=null)
	{
		$data = $this->get_testing_trends($year,$age);
		// echo "<pre>";print_r($result);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Year', 'Month', 'Positive', 'Negative');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('YmdH:i:s').'EID Age Group Trends.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function get_testing_trends($year=null,$age=null)
	{
		if ($age==null || $age=='null') {
			$age = $this->session->userdata('age_filter');
		}

		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;

		$sql = "CALL `proc_get_eid_age_breakdown_trends`('".$age."', '".$from."', '".$to."')";

		$result = $this->db->query($sql)->result_array();
		return $result;
	}


	function get_agebreakdown($year=null,$month=null,$to_year=null,$to_month=null,$age=null,$county=null,$subcounty=null,$partner=null)
	{
		if ($county == null || $county == 'null') {
			$county = 0;
		}else {
			$modal_name = 'countyModal';
			$div_name = 'countyDiv';
			$type = 1;
		}
		if ($subcounty == null || $subcounty == 'null') {
			$subcounty = 0;
		} else {
			$modal_name = 'subCountyModal';
			$div_name			 = 'subCountyDiv';
			$type = 2;
		}
		if ($partner == null || $partner == 'null') {
			$partner = 0;
		} else {
			$modal_name = 'partnerModal';
			$div_name = 'partnerDiv';
			$type = 3;
		}
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['age' => $age, 'county' => $county, 'subcounty' => $subcounty, 'partner' => $partner]);
		extract($d);

		$sql = "CALL `proc_get_eid_age_data_listing`('{$type}', '{$age}', '".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				foreach ($result as $key => $value) {
					$percentage = @round((@$value['pos']/@(@$value['pos']+@$value['neg']))*100,1);
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.$percentage.'%&nbsp;('.number_format($value['pos']).')</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.$percentage.'%</td>';
					$table .= '<td>'.number_format((int) $value['pos']).'</td>';
					$table .= '<td>'.number_format((int) $value['neg']).'</td>';
					$table .= '</tr>';
				$count++;
				}
			}else{
				$li = 'No Data';
			}

		$data = array(
					'ul' => $li,
					'table' => $table,
					'modal_name' => $modal_name,
					'div_name' => $div_name);
		return $data;
		// echo "<pre>";print_r($data);die();
	}

	function get_counties_agebreakdown($year=null,$month=null,$to_year=null,$to_month=null,$age=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['age' => $age]);
		extract($d);

		$sql = "CALL `proc_get_eid_age_data_listing`(1, '{$age}', '".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";


		$data['outcomes'][0]['color'] = '#E26A6A';
		$data['outcomes'][1]['color'] = '#257766';
		$data['outcomes'][2]['color'] = '#913D88';


		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $value['name'];
				$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['pos']+(int) $value['neg'])),1);
			
		}
		
		// echo "<pre>";print_r($data);die();
		return $data;


	}






}
?>