<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Subcounties_model extends MY_Model
{
	function __construct()
	{
		parent:: __construct();;
	}

	function subcounties_positivity($year=null,$month=null,$to_year=null,$to_month=null)
	{

		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_top_subcounty_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
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
			
				$data['categories'][$key] = $value['name'];
				$data["outcomes"][0]["data"][$key]	= (int) $value['positive'];
				$data["outcomes"][1]["data"][$key]	= (int) ($value['actual']-$value['positive']);
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['actual'])),1);
			
		}
		
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function subcounties_outcomes($year=null,$month=null,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);
		
		$sql = "CALL `proc_get_eid_subcountys_details`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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
		
		// echo "<pre>";print_r($table);die();
		return $table;
	}

	function get_eid($subcounty=null, $year=null, $month=null,$to_year=null,$to_month=null){

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		$data['title'] = "EID Outcome (" . $year . ", " . $this->resolve_month($month) . ")";

		
		
		// if ($site==null || $site=='null') {
		// 	$site = $this->session->userdata('site_filter');
		// }

		if ($month==null || $month=='null') {
			$data['title'] = "EID Outcome (" . $year . ")";
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}
		

		
		$sql = "CALL `proc_get_eid_subcounty_eid`('".$subcounty."', '".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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

	function hei_validation($subcounty=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		if ($month == 0) {
			$sql = "CALL `proc_get_eid_subcounty_yearly_hei_validation`('".$subcounty."','".$year."')";
		} else {
			$sql = "CALL `proc_get_eid_subcounty_hei_validation`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['hei']['name'] = 'Validation';
		$data['hei']['colorByPoint'] = true;

		$count = 0;
		$data['ul'] = '';

		$data['hei']['data'][0]['name'] = 'No Data';
		$data['hei']['data'][0]['y'] = $count;

		foreach ($result as $key => $value) {
			// echo "<pre>";print_r($value);die();
			// $data['ul'] .= '<tr>
   //              <td>Validated Positives:</td>
   //                  <td>'.number_format((int) $value['followup_positives']).'<b>('.round((((int) $value['followup_positives']/(int) $value['positives'])*100),1).'%)</b></td>
   //                  <td></td>
   //                  <td></td>
   //              </tr>
 
   //              <tr>
   //                  <td>Confirmed Actual positive Infants:</td>
   //                  <td>'.number_format((int) $value['Confirmed Positive']).'<b>('.round((((int) $value['Confirmed Positive']/(int) $value['true_tests'])*100),1).'%)</b></td>
   //                  <td></td>
   //                  <td></td>
   //              </tr>';
				$data['ul'] .= '<tr>
                 <td>Actual Infants Tested Positive:</td>
                     <td>'.number_format((int) $value['positives']).'</td>
                     <td></td>
                     <td></td>
                </tr><tr>
                 <td><center>&nbsp;&nbsp;Actual Positives Validated at Site:</center></td>
                     <td>'.number_format((int) $value['followup_hei']).'<b>('.round((((int) $value['followup_hei']/(int) $value['positives'])*100),1).'%)</b></td>
                     <td></td>
                     <td></td>
                </tr>
               	<tr>
                   <td><center>&nbsp;&nbsp;&nbsp;&nbsp;Actual Confirmed Positives at Site:</center></td>
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

	function get_hei($subcounty=null, $year=null, $month=null,$to_year=null,$to_month=null){

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);
		
		$data['title'] = "HEI Follow Up (" . $year . ", " . $this->resolve_month($month) . ")";

		// if ($site==null || $site=='null') {
		// 	$site = $this->session->userdata('site_filter');
		// }

		if ($month==null || $month=='null') {
			$data['title'] = "HEI Follow Up (" . $year . ")";
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($month == 0) {
			$sql = "CALL `proc_get_eid_subcounty_yearly_hei_follow_up`('".$subcounty."','".$year."')";
		} else {
			$sql = "CALL `proc_get_eid_subcounty_hei_follow_up`('".$subcounty."','".$year."', '".$month."','".$to_year."', '".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->row();
		// echo "<pre>";print_r($result);die();
		// $data['trend']['name'] = 'Followed-Up';
		$data['trend'][0]['name'] = "Initiated On Treatment";
		$data['trend'][1]['name'] = "Dead";
		$data['trend'][2]['name'] = "Lost to Follow Up";
		$data['trend'][3]['name'] = "Transferred Out";
		$data['trend'][4]['name'] = "Adult Samples";
		$data['trend'][5]['name'] = "Other Reasons";

		$per = (int) ($result->enrolled + $result->dead + $result->ltfu + $result->transout + $result->adult + $result->other);

		$data['trend'][0]['y'] = (int) $result->enrolled;
		$data['trend'][1]['y'] = (int) $result->dead;
		$data['trend'][2]['y'] = (int) $result->ltfu;
		$data['trend'][3]['y'] = (int) $result->transout;
		$data['trend'][4]['y'] = (int) $result->other;

		$data['trend'][0]['sliced'] = true;
		$data['trend'][0]['selected'] = true;

		$data['trend'][0]['color'] = '#1BA39C';
		$data['trend'][1]['color'] = '#F2784B';
		$data['trend'][2]['color'] = '#5C97BF';

		$data['other'][0] = (int) $result->adult;
		$data['other'][1] = (int) $result->other;

		$data['per'][0] = (int) ($result->enrolled / $per * 100);
		$data['per'][1] = (int) ($result->dead / $per * 100);
		$data['per'][2] = (int) ($result->ltfu / $per * 100);
		$data['per'][3] = (int) ($result->transout / $per * 100);
		$data['per'][4] = (int) ($result->other / $per * 100);

		$str = '<li>Initiated On Treatment: '.number_format((int) $data['trend'][0]['y']).' <strong>('.(int) $data['per'][0].'%)</strong></li>';
		$str .= '<li>Lost to Follow Up: '.number_format((int) $data['trend'][2]['y']).' <strong>('.(int) $data['per'][2].'%)</strong></li>';
		$str .= '<li>Dead: '.number_format((int) $data['trend'][1]['y']).' <strong>('.(int) $data['per'][1].'%)</strong></li>';
		$str .= '<li>Transferred Out: '.number_format((int) $data['trend'][3]['y']).' <strong>('.(int) $data['per'][3].'%)</strong></li>';
		$str .= '<li>Other Reasons(e.g denial): '.number_format((int) $data['other'][1]).' <strong>('.(int) $data['per'][5].'%)</strong></li>';

		$data['stats'] = $str;


		$data['div'] = "hei_pie";
		$data['content'] = "hei_content";
		
		

		return $data;
	}

	function age($subcounty=null, $year=null, $month=null,$to_year=null,$to_month=null)
	{
		// echo "<pre>";print_r($subcounty."<__>");die();
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_age`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
				
		// echo "<pre>";print_r($result);die();
		$data['ageGnd'][0]['name'] = 'Positive';
		$data['ageGnd'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["ageGnd"][0]["data"][0]	= $count;
		$data["ageGnd"][1]["data"][0]	= $count;
		$data['categories'][0]			= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][0] 			= 'No Data';
			$data['categories'][1] 			= '2M';
			$data['categories'][2] 			= '3-8M';
			$data['categories'][3] 			= '9-12M';
			$data['categories'][4] 			= 'Above 12M';
			// $data['categories'][4] 			= 'above18M';


			$data["ageGnd"][0]["data"][0]	=  (int) $value['nodatapos'];
			$data["ageGnd"][1]["data"][0]	=  (int) $value['nodataneg'];

			$data["ageGnd"][0]["data"][1]	=  (int) $value['sixweekspos'];
			$data["ageGnd"][1]["data"][1]	=  (int) $value['sixweeksneg'];
			$data["ageGnd"][0]["data"][2]	=  (int) $value['sevento3mpos'];
			$data["ageGnd"][1]["data"][2]	=  (int) $value['sevento3mneg'];
			$data["ageGnd"][0]["data"][3]	=  (int) $value['threemto9mpos'];
			$data["ageGnd"][1]["data"][3]	=  (int) $value['threemto9mneg'];
			$data["ageGnd"][0]["data"][4]	=  (int) $value['ninemto18mpos'];
			$data["ageGnd"][1]["data"][4]	=  (int) $value['ninemto18mneg'];
			// $data["ageGnd"][0]["data"][4]	=  (int) $value['above18mpos'];
			// $data["ageGnd"][1]["data"][4]	=  (int) $value['above18mneg'];
		}
		// die();
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][1]['drilldown']['color'] = '#96281B';

		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function age2($subcounty=null, $year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_age_range`(0, '".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
				
		// echo "<pre>";print_r($result);die();
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

		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function subcounty_sites_outcomes($subcounty=NULL,$year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		// echo "<pre>";print_r($subcounty."<__>".$year."<___>".$month);die();
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_sites_details`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		foreach ($result as $key => $value) {
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['MFLCode'].'</td>';
			$table .= '<td>'.$value['name'].'</td>';
			$table .= '<td>'.$value['county'].'</td>';
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

	function subcounty_sites_outcomes_download($subcounty=NULL,$year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['subcounty' => $subcounty]);
		extract($d);

		$sql = "CALL `proc_get_eid_subcounty_sites_details`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$data = $this->db->query($sql)->result_array();

		// $this->load->helper('download');
  //       $this->load->library('PHPReport/PHPReport');

  //       ini_set('memory_limit','-1');
	 //    ini_set('max_execution_time', 900);


  //       $template = 'partner_sites.xlsx';

	 //    //set absolute path to directory with template files
	 //    $templateDir = __DIR__ . "/";
	    
	 //    //set config for report
	 //    $config = array(
	 //        'template' => $template,
	 //        'templateDir' => $templateDir
	 //    );


	 //      //load template
	 //    $R = new PHPReport($config);
	    
	 //    $R->load(array(
	 //            'id' => 'data',
	 //            'repeat' => TRUE,
	 //            'data' => $data   
	 //        )
	 //    );
	      
	 //      // define output directoy 
	 //    $output_file_dir = __DIR__ ."/tmp/";

	 //    $output_file_excel = $output_file_dir  . "subcounty_sites.xlsx";
	 //    //download excel sheet with data in /tmp folder
	 //    $result = $R->render('excel', $output_file_excel);
	 //    force_download($output_file_excel, null);

	    $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('MFL Code', 'Name', 'County', 'Subcounty', 'All Tests', 'Actual Infants Tested', 'Repeat Confirmatory Tests', 'Positives', 'Negatives', 'Redraws', 'Infants < 2weeks Tests', 'Infants < 2weeks Positives', 'Infants <= 2M Tests', 'Infants <= 2M Positives', 'Infants >= 2M Tests', 'Infants >= 2M Positives', 'Median Age', 'Rejected');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="subcounty_sites.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);	
		
	}
}
?>