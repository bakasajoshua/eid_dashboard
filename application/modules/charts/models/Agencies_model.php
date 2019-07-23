<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 */
class Agencies_model extends MY_Model
{
	
	function __construct() {
		parent:: __construct();
	}

	function test_trends($year=null, $type=1, $agency_id=null)
	{
		if ($agency_id==null || $agency_id == 'null')
			$agency_id = $this->session->userdata('funding_agency_filter');

		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;
		
		$sql = "CALL `proc_get_eid_fundingagency_testing_trends`('".$agency_id."','".$from."','".$to."')";
		$result = $this->db->query($sql)->result_array();
		
		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		//$data['outcomes'][0]['color'] = '#52B3D9';
		// $data['outcomes'][0]['color'] = '#E26A6A';
		// $data['outcomes'][1]['color'] = '#257766';
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
			if (!((int) $value['pos'] == 0 && (int) $value['neg'] == 0)){
				$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];

				if($type==1){
					$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
					$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
					$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['neg']+(int) $value['pos'])),1);
				}

				else if($type==2){
					$data["outcomes"][0]["data"][$key]	= (int) $value['rpos'];
					$data["outcomes"][1]["data"][$key]	= (int) $value['rneg'];
					$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['rpos']*100) /((int) $value['rneg']+(int) $value['rpos'])),1);
				}

				else{
					$data["outcomes"][0]["data"][$key]	= (int) $value['allpos'];
					$data["outcomes"][1]["data"][$key]	= (int) $value['allneg'];
					$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['allpos']*100) /((int) $value['allneg']+(int) $value['allpos'])),1);
				}
			}
		}
		return $data;
	}

	function test_analysis_trends($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		$agency_id = 0;
		
		if ($type==null || $type=='null') $type = 4;
		if ($type == 4 || $type == '4') {
			if (null !== $this->session->userdata('funding_agency_filter')) $agency_id = $this->session->userdata('funding_agency_filter');
		}

		$sql = "CALL `proc_get_eid_tests_analysis`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";

		$result = $this->db->query($sql)->result();
		
		$data['outcomes'][0]['name'] = "<= 2M Tests (of Initial PCR)";
		$data['outcomes'][1]['name'] = "> 2M Tests (of Initial PCR)";
		$data['outcomes'][2]['name'] = "<= 2M Tests (% of Initial PCR)";

		$data['outcomes'][0]['color'] = '#1BA39C';
		$data['outcomes'][1]['color'] = '#F2784B';
		// $data['outcomes'][1]['color'] = '#257766';
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
			if (!((int) $value->infantsless2m == 0 && (int) $value->firstdna == 0)){
				$above2m = $value->firstdna - $value->infantsless2m;
				$data['categories'][$key] = $value->name;
				$data["outcomes"][0]["data"][$key]	= (int) $value->infantsless2m;
				$data["outcomes"][1]["data"][$key]	= (int) $above2m;
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value->infantsless2m*100) /((int) $value->firstdna)),1);
			}
		}
		return $data;
	}

	function tests_analysis($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		$agency_id = 0;
		
		if ($type==null || $type=='null') $type = 4;
		if ($type == 4 || $type == '4') {
			if (null !== $this->session->userdata('funding_agency_filter')) $agency_id = $this->session->userdata('funding_agency_filter');
		}

		$sql = "CALL `proc_get_eid_tests_analysis`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";
		
		$result = $this->db->query($sql)->result();
		$count = 1;
		$table = '';
		foreach ($result as $key => $value) {
			if (!((int) $value->infantsless2m == 0 && (int) $value->firstdna == 0)){
				$tests = (int) ($value->firstdna+$value->confirmdna+$value->repeatspos);
				$table .= '<tr>';
				$table .= '<td>'.$count.'</td>';
				$table .= '<td>'.$value->name.'</td>';
				$table .= '<td>'.number_format($tests).'</td>';
				$table .= '<td>'.number_format($value->firstdna).'</td>';
				$table .= '<td>'.number_format($value->infantsless2m).'</td>';
				$table .= '<td><center>'.round(@($value->infantsless2m/$value->firstdna)*100, 1).'%</center></td>';
				$table .= '<td>'.number_format($value->repeatspos).'</td>';
				$table .= '<td><center>'.round(@($value->repeatspos/$tests)*100, 1).'%</center></td>';
				$table .= '<td>'.number_format($value->confirmdna).'</td>';
				$table .= '<td><center>'.round(@($value->confirmdna/$tests)*100, 1).'%</center></td>';
				$table .= '</tr>';
				$count++;
			}
		}
		return $table;
	}


	function positivity($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		if ($type==null || $type == 'null'){
			$type = 0;
			$agency_id = 0;
		}

		$sql = "CALL `proc_get_eid_agencies_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";

		$result = $this->db->query($sql)->result_array();

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
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
		$data["outcomes"][0]["data"][0]	= 0;
		$data["outcomes"][1]["data"][0]	= 0;
		$data["outcomes"][2]["data"][0]	= 0;

		foreach ($result as $key => $value) {
			if (!((int) $value['positive'] == 0 && (int) $value['negative'] == 0)){
				$data['categories'][$key] = $value['agency'];
				$data["outcomes"][0]["data"][$key]	= (int) $value['positive'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['negative'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['positive']+(int) $value['negative'])),1);
			}
		}
		return $data;
	}

	public function outcomes($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		if ($type==null || $type == 'null')
			$type = 0;
		
		$sql = "CALL `proc_get_eid_fundingagencies_eid_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";
		
		$result = $this->db->query($sql)->result_array();
		
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
		    		<td>Actual Infants Tested <br />(Based on Unique IDs):</td>
		    		<td>'.number_format((int) $value['actualinfants']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['actualinfantspos']).'('. round((((int) $value['actualinfantspos']/(int) $value['actualinfants'])*100),1)  .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infants &lt;= 2M:</td>
		    		<td>'.number_format((int) $value['infantsless2m']).'</td>
		    		<td>Infants &lt;= 2M Positive:</td>
		    		<td>'.number_format((int) $value['infantless2mpos']).'('.round((((int) $value['infantless2mpos']/(int) $value['infantsless2m'])*100),1).'%)</td>
		    	</tr>

		    	<tr>
		    		<td>Above 2 yrs Tested:</td>
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
		    	</tr>


		    	<tr>
		    		<td>Median Age of Testing at Initial PCR:</td>
		    		<td>'.round($value['medage']).'</td>
		    		<td>Average Sites sending:</td>
		    		<td>'.number_format((int) $value['sitessending']).'</td>
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

	function hei_validation($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		if ($type==null || $type == 'null')
			$type = 0;

		$sql = "CALL `proc_get_eid_fundingagencies_hei_validation`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";
		
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['hei']['name'] = 'Validation';
		$data['hei']['colorByPoint'] = true;

		$count = 0;
		$data['ul'] = '';

		$data['hei']['data'][0]['name'] = 'No Data';
		$data['hei']['data'][0]['y'] = $count;

		foreach ($result as $key => $value) {
			$followup_hei = (int) $value['Confirmed Positive']+(int) $value['Repeat Test']+(int) $value['Viral Load']+(int) $value['Adult']+(int) $value['Unknown Facility'];
			$data['ul'] .= '<tr>
                 <td><center>Actual Infants Tested Positive :</center></td>
                     <td>'.number_format((int) $value['positives']).'</td>
                     <td></td>
                     <td></td>
                </tr><tr>
                 <td><center>&nbsp;&nbsp;Actual Infants Validated at Site:</center></td>
                     <td>'.number_format((int) $followup_hei).'<b>('.round((((int) $followup_hei/(int) $value['positives'])*100),1).'%)</b></td>
                     <td></td>
                     <td></td>
                </tr>
               	<tr>
                   <td><center>&nbsp;&nbsp;&nbsp;&nbsp;Actual Confirmed Positives at Site :</center></td>
                     <td>'.number_format((int) $value['Confirmed Positive']).'<b>('.round((((int) $value['Confirmed Positive']/(int) $value['true_tests'])*100),1).'%)</b></td>
                     <td></td>
                     <td></td>
                 </tr>';
			$data['hei']['data'][0]['name'] = 'Confirmed Positive';
			$data['hei']['data'][1]['name'] = '2nd/3rd Test';
			$data['hei']['data'][2]['name'] = 'Viral Load';
			$data['hei']['data'][3]['name'] = 'Adult';
			$data['hei']['data'][4]['name'] = 'Unknown Facility';

			$data['hei']['data'][0]['y'] = (int) $value['Confirmed Positive'];
			$data['hei']['data'][1]['y'] = (int) $value['Repeat Test'];
			$data['hei']['data'][2]['y'] = (int) $value['Viral Load'];
			$data['hei']['data'][3]['y'] = (int) $value['Adult'];
			$data['hei']['data'][4]['y'] = (int) $value['Unknown Facility'];

			$count++;
		}
		$data['hei']['data'][0]['sliced'] = true;
		$data['hei']['data'][0]['selected'] = true;
		$data['hei']['data'][0]['color'] = '#1BA39C';
		$data['hei']['data'][1]['color'] = '#F2784B';
		$data['hei']['data'][2]['color'] = '#5C97BF';
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function hei_follow($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		if ($type==null || $type == 'null')
			$type = 0;

		$sql = "CALL `proc_get_eid_fundingagency_hei`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";
		
		$result = $this->db->query($sql)->result_array();
		
		$data['hei']['name'] = 'Tests';
		$data['hei']['colorByPoint'] = true;

		$count = 0;
		$data['ul'] = '';

		$data['hei']['data'][0]['name'] = 'No Data';
		$data['hei']['data'][0]['y'] = $count;

		foreach ($result as $key => $value) {
			$total = (int) ($value['enrolled']+$value['dead']+$value['ltfu']+$value['adult']+$value['transout']+$value['other']);
			$data['ul'] .= '<li>Initiated On Treatment: '.(int) $value['enrolled'].' <strong>('.(int) (($value['enrolled']/$total)*100).'%)</strong></li>';
			$data['ul'] .= '<li>Lost to Follow Up: '.$value['ltfu'].' <strong>('.(int) (($value['ltfu']/$total)*100).'%)</strong></li>';
			$data['ul'] .= '<li>Dead: '.(int) $value['dead'].' <strong>('.(int) (($value['dead']/$total)*100).'%)</strong></li>';
			$data['ul'] .= '<li>Transferred Out: '.$value['transout'].' <strong>('.(int) (($value['transout']/$total)*100).'%)</strong></li>';
			$data['ul'] .= '<li>Other Reasons(e.g denial): '.$value['other'].' <strong>('.(int) (($value['other']/$total)*100).'%)</strong></li>';
			// if($value['name'] == ''){
			// 	$data['hei']['data'][$key]['color'] = '#5C97BF';
			// }
			$data['hei']['data'][$key]['y'] = $count;

			$data['hei']['data'][0]['name'] = 'Initiated on Treatment';
			$data['hei']['data'][1]['name'] = 'Dead';
			$data['hei']['data'][2]['name'] = 'Lost to Follow up';
			$data['hei']['data'][3]['name'] = 'Transferred out';
			$data['hei']['data'][4]['name'] = 'Other Reasons';

			$data['hei']['data'][0]['y'] = (int) $value['enrolled'];
			$data['hei']['data'][1]['y'] = (int) $value['dead'];
			$data['hei']['data'][2]['y'] = (int) $value['ltfu'];
			$data['hei']['data'][3]['y'] = (int) $value['transout'];
			$data['hei']['data'][4]['y'] = (int) $value['other'];
		}

		$data['hei']['data'][0]['sliced'] = true;
		$data['hei']['data'][0]['selected'] = true;
		$data['hei']['data'][0]['color'] = '#1BA39C';
		$data['hei']['data'][1]['color'] = '#F2784B';
		$data['hei']['data'][2]['color'] = '#5C97BF';
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function age2($year=null,$month=null,$to_year=null,$to_month=null,$type=null,$agency_id=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['agency_id' => $agency_id]);
		extract($d);

		if ($type==null || $type == 'null')
			$type = 0;

		$sql = "CALL `proc_get_eid_fundingegency_age_range`(0, '".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$agency_id."')";
		
		$result = $this->db->query($sql)->result_array();
		$count = 0;
				
		$data['ageGnd'][0]['name'] = 'Positive';
		$data['ageGnd'][1]['name'] = 'Negative';

		$count = 0;

		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['age_range'];

			$data["ageGnd"][0]["data"][$key]	=  (int) $value['pos'];
			$data["ageGnd"][1]["data"][$key]	=  (int) $value['neg'];
		}
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][1]['drilldown']['color'] = '#96281B';

		return $data;
	}
}
?>