<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Rht_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}



	function get_trends($county=null,$year=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		$from = $year-1;

		$sql = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$from."','1')";
		$sql2 = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$from."','2')";

		$sql3 = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$year."','1')";
		$sql4 = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$year."','2')";

		// Negative for previous year
		$result = $this->db->query($sql)->result_array();
		$this->db->close();

		// Positive for previous year
		$result2 = $this->db->query($sql2)->result_array();
		$this->db->close();

		// Negative for current year
		$result3 = $this->db->query($sql3)->result_array();
		$this->db->close();

		// Positive for current year
		$result4 = $this->db->query($sql4)->result_array();


		$data["categories"] = array_fill(0, 24, '');
		$data["outcomes"][0]["data"] = array_fill(0, 24, 0);
		$data["outcomes"][1]["data"] = array_fill(0, 24, 0);
		$data["outcomes"][2]["data"] = array_fill(0, 24, 0);


		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";


		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";


		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;


		$data['outcomes'][0]['color'] = '#F2784B';
		$data['outcomes'][1]['color'] = '#1BA39C';
		$data['outcomes'][2]['color'] = '#913D88';

		$y = $from;
		$i = 1;

		while($y < ($year+1)){
			
			for ($p=0; $p < 12; $p++) { 
				$o = $i -1;
				$data['categories'][$o] = $this->resolve_month($p+1).'-'.$y;
				$i++;
			}
			$y++;

		}

		foreach ($result as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$data["outcomes"][1]["data"][$month] = (int) $value['tests'];
		}

		foreach ($result2 as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$data["outcomes"][0]["data"][$month] = (int) $value['tests'];
		}

		foreach ($result3 as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$month = $month + 12;
			$data["outcomes"][1]["data"][$month] = (int) $value['tests'];
		}

		foreach ($result4 as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$month = $month + 12;
			$data["outcomes"][0]["data"][$month] = (int) $value['tests'];
		}

		for ($i=0; $i < 24; $i++) { 
			$divisor = ($data["outcomes"][0]["data"][$i]+$data["outcomes"][1]["data"][$i]);
			if($divisor == 0){
				$data["outcomes"][2]["data"][$i] = 0;
			}
			else{
				$data["outcomes"][2]["data"][$i] = round(( $data["outcomes"][0]["data"][$i] /$divisor), 1);
			}
			
		}

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = '';

		return $data;
		
	}

	function get_outcomes($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','1','')";
		$sql2 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','2','')";
		$sql3 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','0','')";
		$sql4 = "CALL `proc_get_eid_rht_facility_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','0')";

		$result = $this->db->query($sql)->row();
		$this->db->close();

		$result2 = $this->db->query($sql2)->row();
		$this->db->close();

		$result3 = $this->db->query($sql3)->row();
		$this->db->close();

		$result4 = $this->db->query($sql4)->result_array();
		$facilities = count($result4);

		// return $result->tests;

		$data['eid_outcomes']['data'][0]['name'] = 'Positive';
		$data['eid_outcomes']['data'][1]['name'] = 'Negative';

		$data['eid_outcomes']['data'][0]['y'] = (int) $result2->tests;
		$data['eid_outcomes']['data'][1]['y'] = (int) $result->tests;


		$data['ul'] = '';
		$data['eid_outcomes']['name'] = 'Tests';
		$data['eid_outcomes']['colorByPoint'] = true;

		$pos = 0;
		$neg = 0;

		if($result3->tests != 0){
			$pos = round((( (int) $result2->tests / (int) $result3->tests) * 100),1);
			$neg = round((( (int) $result->tests / (int) $result3->tests) * 100),1);
		}

		$data['ul'] .= '
				<tr>
		    		<td>Total Tests:</td>
		    		<td>'.number_format((int) $result3->tests).'</td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Positives:</td>
		    		<td>'.number_format((int) $result2->tests). '(' . $pos .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Negatives:</td>
		    		<td>'.number_format((int) $result->tests). '(' . $neg .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>Facilities Sending:</td>
		    		<td>'.number_format($facilities).'</td>
		    	</tr>

		    	';

		$data['eid_outcomes']['data'][0]['sliced'] = true;
		$data['eid_outcomes']['data'][0]['selected'] = true;
		$data['eid_outcomes']['data'][0]['color'] = '#F2784B';
		$data['eid_outcomes']['data'][1]['color'] = '#1BA39C';
		// echo "<pre>";print_r($data);die();
		return $data;

		

	}

	function get_gender($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);


		// Queries to get negatives
		$sql = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','1','M')";
		$sql2 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','1','F')";
		$sql3 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','1','U')";

		// Queries to get positives
		$sql4 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','2','M')";
		$sql5 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','2','F')";
		$sql6 = "CALL `proc_get_eid_rht_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','2','U')";

		$result = $this->db->query($sql)->row();
		$this->db->close();

		$result2 = $this->db->query($sql2)->row();
		$this->db->close();

		$result3 = $this->db->query($sql3)->row();
		$this->db->close();

		$result4 = $this->db->query($sql4)->row();
		$this->db->close();

		$result5 = $this->db->query($sql5)->row();
		$this->db->close();

		$result6 = $this->db->query($sql6)->row();
		$this->db->close();


		$data['categories'][0] 			= 'No Data';
		$data['categories'][1] 			= 'Male';
		$data['categories'][2] 			= 'Female';

		$data['ageGnd'][0]['name'] = 'Positive';
		$data['ageGnd'][1]['name'] = 'Negative';

		$data["ageGnd"][0]["data"][0]	=  (int) $result6->tests;
		$data["ageGnd"][1]["data"][0]	=  (int) $result3->tests;

		$data["ageGnd"][0]["data"][1]	=  (int) $result4->tests;
		$data["ageGnd"][1]["data"][1]	=  (int) $result->tests;

		$data["ageGnd"][0]["data"][2]	=  (int) $result5->tests;
		$data["ageGnd"][1]["data"][2]	=  (int) $result2->tests;

		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][1]['drilldown']['color'] = '#96281B';

		// echo "<pre>";print_r($data);die();
		return $data;


	}

	function get_yearly_trends($county=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}

		$sql = "CALL `proc_get_eid_rht_yearly_trend`('".$county."','0')";

		$result = $this->db->query($sql)->result_array();
		$data;

		$cur_year = date('Y');

		$i = 0;
		$b = true;
		$year;

		foreach ($result as $key => $value) {
			if($b){
				$b = false;
				$year = (int) $value['year'];
				$data['test_trends'][$i]['data'] = array_fill(0, 12, 0);
			}

			$y = (int) $value['year'];
			if($value['year'] != $year){
				$i++;
				$year--;
				$data['test_trends'][$i]['data'] = array_fill(0, 12, 0);
			}

			$month = (int) $value['month'];
			$month--;

			$data['test_trends'][$i]['name'] = $value['year'];
			$data['test_trends'][$i]['data'][$month] = (int) $value['tests'];

		}
		return $data;




	}

	function get_positivity($county=null,$year=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		

		$sql = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$year."','2')";

		$result = $this->db->query($sql)->result_array();
		$data;

		$year;
		$i = 0;
		$b = true;


		$data["test_trends"][0]["data"] = array_fill(0, 12, 0);
		$data["test_trends"][0]["name"] = "Positives";

		foreach ($result as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$data["test_trends"][0]["data"][$month] = (int) $value['tests'];

		}

		return $data;

	}

	function get_negativity($county=null,$year=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		

		$sql = "CALL `proc_get_eid_rht_pos_trend`('".$county."','".$year."','1')";

		$result = $this->db->query($sql)->result_array();
		$data;

		$year;
		$i = 0;
		$b = true;


		$data["test_trends"][0]["data"] = array_fill(0, 12, 0);
		$data["test_trends"][0]["name"] = "Negatives";

		foreach ($result as $key => $value) {
			$month = (int) $value['month'];
			$month--;
			$data["test_trends"][0]["data"][$month] = (int) $value['tests'];

		}

		return $data;




	}

	function get_facilities($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);
		
		$sql = "CALL `proc_get_eid_rht_facility_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','1')";
		$sql2 = "CALL `proc_get_eid_rht_facility_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."','2')";

		$result = $this->db->query($sql)->result_array();
		$this->db->close();

		$result2 = $this->db->query($sql2)->result_array();
		$data;
		$data['county_outcomes'][0]['name'] = 'Positive';
		$data['county_outcomes'][1]['name'] = 'Negative';

		$i=0;

		$data["categories"] = array_fill(0, 12, '');

		foreach ($result as $key => $value) {
			$data['categories'][$key] = $value['name'];
			$data["county_outcomes"][0]["data"][$key]	=  0;
			$data["county_outcomes"][1]["data"][$key]	=  (int) $value['tests'];
			if($i == 50){
				break;
			}
			$i++;
		}

		foreach ($result2 as $key => $value) {

			foreach ($data['categories'] as $key2 => $value2) {
				if ($value2 == $value['name']) {
					$data["county_outcomes"][0]["data"][$key2]	=  (int) $value['tests'];
					break;
				}
			}
			
		}

		return $data;





	}
}
?>