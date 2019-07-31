<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Poc_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function testing_trends($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		if($county == NULL || $county == 48 || $county=='null') $county=0;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);


		$sql = "CALL `proc_get_eid_poc_trends`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
		$result = $this->db->query($sql)->result_array();

		$data['outcomes'][0]['name'] = "Confirmatory PCR";
		$data['outcomes'][1]['name'] = "2nd/3rd PCR";
		$data['outcomes'][2]['name'] = "Initial PCR";
		$data['outcomes'][3]['name'] = "Initial PCR Positivity";
		$data['outcomes'][4]['name'] = "&lt; 2m % Contribution";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";
		$data['outcomes'][4]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' %');
		$data['outcomes'][4]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];
				$data["outcomes"][0]["data"][$key]	= (int) $value['confirmdna'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['repeatspos'];
				$data["outcomes"][2]["data"][$key]	= (int) $value['firstdna'];
				$data["outcomes"][3]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['positive']+(int) $value['negative'])),1);
				$data["outcomes"][4]["data"][$key]	= round(@( ((int) $value['infantsless2m']*100) /((int) $value['tests'])),1);
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}



	function eid_outcomes($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		if($county == NULL || $county == 48 || $county=='null') $county=0;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_poc_summary_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
		$result = $this->db->query($sql)->result_array();


		// echo "<pre>";print_r($result);die();
		// $this->db->close();
		// $sitessending = $this->db->query($sql2)->result_array();
		$data['ul'] = '';
		$data['eid_outcomes']['name'] = 'Tests';
		$data['eid_outcomes']['colorByPoint'] = true;

		$count = 0;

		$data['eid_outcomes']['data'][0]['name'] = 'No Data';
		$data['eid_outcomes']['data'][0]['y'] = $count;

		foreach ($result as $key => $value) {
			$data['ul'] .= '<tr>
					<td>Total EID Tests</td>
					<td>'.number_format((int) ($value['firstdna']+$value['confirmdna']+$value['repeatspos'])).'</td>
					<td>Positive (+)</td>
					<td>'.number_format((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])).'('.round((((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])/(int) ($value['firstdna']+$value['confirmdna']+$value['repeatspos']))*100),1).'%)</td>
				</tr>
				<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initial PCR:</td>
		    		<td>'.number_format((int) $value['firstdna']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['pos']).'('.round((((int) $value['pos']/(int) $value['firstdna'])*100),1).'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2nd/3rd PCR:</td>
		    		<td>'.number_format((int) $value['repeatspos']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['repeatsposPOS']).'('.round((((int) $value['repeatsposPOS']/(int) $value['repeatspos'])*100),1).'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Confirmatory PCR:</td>
		    		<td>'.number_format((int) $value['confirmdna']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['confirmpos']).'('.round((((int) $value['confirmpos']/(int) $value['confirmdna'])*100),1).'%)</td>
		    	</tr>
				<tr style="height:14px;background-color:#ABB7B7;">
		    		<td></td>
		    		<td></td>
		    		<td></td>
		    		<td></td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infants &lt;= 2M:</td>
		    		<td>'.number_format((int) $value['infantsless2m']).'</td>
		    		<td>Infants &lt;= 2M Positive:</td>
		    		<td>'.number_format((int) $value['infantless2mpos']).'('.round((((int) $value['infantless2mpos']/(int) $value['infantsless2m'])*100),1).'%)</td>
		    	</tr>

		    	<tr>
		    		<td>Above 2years Tested:</td>
		    		<td>'.number_format((int) $value['adults']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['adultsPOS']).'('.round((((int) $value['adultsPOS']/(int) $value['adults'])*100),1).'%)</td>
		    	</tr>
		    	<tr>
		    		<td></td>
		    		<td></td>
		    		<td></td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>Rejected Samples:</td>
		    		<td>'.number_format((int) $value['rejected']).'</td>
		    		<td>% Rejection:</td>
		    		<td>'.round((((int) $value['rejected']/(int) $value['alltests'])*100),1).'%</td>
		    	</tr>';

			$data['eid_outcomes']['data'][$key]['y'] = $count;

			$data['eid_outcomes']['data'][0]['name'] = 'Positive';
			$data['eid_outcomes']['data'][1]['name'] = 'Negative';

			$data['eid_outcomes']['data'][0]['y'] = (int) $value['pos'];
			$data['eid_outcomes']['data'][1]['y'] = (int) $value['neg'];
		}

		$data['eid_outcomes']['data'][0]['sliced'] = true;
		$data['eid_outcomes']['data'][0]['selected'] = true;
		$data['eid_outcomes']['data'][0]['color'] = '#F2784B';
		$data['eid_outcomes']['data'][1]['color'] = '#1BA39C';
		// echo "<pre>";print_r($data);die();
		return $data;
	}


	function entrypoints($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		if($county == NULL || $county == 48 || $county=='null') $county=0;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);


		$sql = "CALL `proc_get_eid_county_poc_entry_points`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
		$result = $this->db->query($sql)->result_array();
				
		$data['entry'][0]['name'] = 'Positive';
		$data['entry'][1]['name'] = 'Negative';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value['name'];

			$data["entry"][0]["data"][$key]	=  (int) $value['positive'];
			$data["entry"][1]["data"][$key]	=  (int) $value['negative'];
			
		}
		return $data;
	}	


	function ages($county=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		if($county == NULL || $county == 48 || $county=='null') $county=0;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_county_poc_age_range`(0, '".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
		$result = $this->db->query($sql)->result_array();
				
		$data['ageGnd'][0]['name'] = 'Positive';
		$data['ageGnd'][1]['name'] = 'Negative';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value['agename'];

			$data["ageGnd"][0]["data"][$key]	=  (int) $value['pos'];
			$data["ageGnd"][1]["data"][$key]	=  (int) $value['neg'];
			
		}
		// $data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		// $data['ageGnd'][1]['drilldown']['color'] = '#96281B';

		return $data;
	}		


	function county_outcomes($year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_county_poc_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
		$result = $this->db->query($sql)->result_array();
				
		$data['outcomes'][0]['name'] = 'Positive';
		$data['outcomes'][1]['name'] = 'Negative';
		$data['outcomes'][2]['name'] = "Positivity";
		
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
			$data['categories'][$key] 		= $value['countyname'];

			$data["outcomes"][0]["data"][$key]	=  (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['neg'];
			$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['pos']+(int) $value['neg'])),1);
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}	




}