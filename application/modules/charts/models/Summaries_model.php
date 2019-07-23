<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Summaries_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}
	

	function turnaroundtime($year=null,$month=null,$county=null,$to_year=null,$to_month=null)
	{
		$toMonth = 0;
		$type = 0;
		$id = 0;
		
		if ($county != null) {$type = 1; $id = $county;}
		
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_national_tat`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$id."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
		$tat1 = 0;
		$tat2 = 0;
		$tat3 = 0;
		$tat4 = 0;
		$tat = array();
		
		foreach ($result as $key => $value) {
			if (($value['tat1']!=0) || ($value['tat2']!=0) || ($value['tat3']!=0) || ($value['tat4']!=0)) {
				$count++;

				$tat1 = $tat1+$value['tat1'];
				$tat2 = $tat2+$value['tat2'];
				$tat3 = $tat3+$value['tat3'];
				$tat4 = $tat4+$value['tat4'];
			}
		}
		$tat[] = array(
					'tat1' => $tat1,
					'tat2' => $tat2,
					'tat3' => $tat3,
					'tat4' => $tat4,
					'count' => $count
					);
		// echo "<pre>";print_r($tat);die();
		foreach ($tat as $key => $value) {
			$data['tat1'] = round(@$value['tat1']/@$value['count']);
			$data['tat2'] = round((@$value['tat2']/@$value['count']) + @$data['tat1']);
			$data['tat3'] = round((@$value['tat3']/@$value['count']) + @$data['tat2']);
			$data['tat4'] = round(@$value['tat4']/@$value['count']);
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function get_testing_trends($year=null,$county=null,$partner=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
		}

		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;
		
		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_testing_trends`('".$partner."','".$from."','".$to."')";
			// $sql2 = "CALL `proc_get_partner_sitessending`('".$partner."','".$year."','".$month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_testing_trends`('".$from."','".$to."')";
				// $sql2 = "CALL `proc_get_national_sitessending`('".$year."','".$month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_testing_trends`('".$county."','".$from."','".$to."')";
				// $sql2 = "CALL `proc_get_regional_sitessending`('".$county."','".$year."','".$month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		return $this->db->query($sql)->result_array();
	}

	function test_trends($year=null, $type=1, $county=null,$partner=null)
	{
		$result = $this->get_testing_trends($year,$county,$partner);
		// echo "<pre>";print_r($result);die();

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
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function download_testing_trends($year=null,$county=null,$partner=null)
	{
		$data = $this->get_testing_trends($year,$county,$partner);
		// echo "<pre>";print_r($result);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Year', 'Month', 'Positive', 'Negative', '2nd/3rd Positives', '2nd/3rd Negatives', 'All Positives', 'All Negatives');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('YmdH:i:s').' EID Testing Trends.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function eid_outcomes($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_eid_outcomes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
			// $sql2 = "CALL `proc_get_partner_sitessending`('".$partner."','".$year."','".$month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_eid_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
				// $sql2 = "CALL `proc_get_national_sitessending`('".$year."','".$month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_eid_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				// $sql2 = "CALL `proc_get_regional_sitessending`('".$county."','".$year."','".$month."')";
			}
		}

		// echo "<pre>";print_r($sql);die();
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
		// <td>Cumulative Tests (All Samples Run):</td>
		//     		<td>'.number_format((int) $value['alltests']).'</td>
		//     		<td></td>
		//     		<td></td>
		//     	</tr>
		//<tr>
		// <tr>
		//     	<td>Actual Tests With Valid Results:</td>
		//     		<td>'.number_format((int) $value['tests']).'</td>
		//     		<td>Positive Outcomes:</td>
		//     		<td>'.number_format((int) $value['pos']).'('.round((((int) $value['pos']/(int) $value['tests'])*100),1).'%)</td>
		//     	</tr>

		//     	<tr>
		//     		<td>First DNA PCR With Valid Results:</td>
		//     		<td>'. number_format((int) $value['firstdna']).'</td>
		//     		<td></td>
		//     		<td></td>
		//     	</tr>

		//     	<tr>
		//     		<td>Repeat Confirmatory Tests:</td>
		//     		<td>'. number_format((int) $value['confirmdna'] + (int) $value['repeatspos']) .'</td>
		//     		<td>Repeat Confirmatory Tests POS</td>
		//     		<td>'. number_format((int) $value['confirmpos']) .'('. round(((int) $value['confirmpos'])/((int) $value['confirmdna'] + (int) $value['repeatspos'])*100,1) .'%)</td>
		//     	</tr>
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

	function hei_validation($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($month == 0) {
			if ($partner) {
				$sql = "CALL `proc_get_eid_partner_yearly_hei_validation`('".$partner."','".$year."')";
			} else {
				if ($county==null || $county=='null') {
					$sql = "CALL `proc_get_eid_national_yearly_hei_validation`('".$year."')";
				} else {
					$sql = "CALL `proc_get_eid_county_yearly_hei_validation`('".$county."','".$year."')";
				}
			}
		} else {
			if ($partner) {
				$sql = "CALL `proc_get_eid_partner_hei_validation`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				if ($county==null || $county=='null') {
					$sql = "CALL `proc_get_eid_national_hei_validation`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					$sql = "CALL `proc_get_eid_county_hei_validation`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			}
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
			$followup_hei = (int) $value['Confirmed Positive']+(int) $value['Repeat Test']+(int) $value['Viral Load']+(int) $value['Adult']+(int) $value['Unknown Facility'];
				$data['ul'] .= '<tr>
                 <td><center>Actual Infants Tested Positive:</center></td>
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
                   <td><center>&nbsp;&nbsp;&nbsp;Actual Confirmed Positives at Site:</center></td>
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

	function hei_follow($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($month == 0) {
			if ($partner) {
				$sql = "CALL `proc_get_eid_partner_yearly_hei`('".$partner."','".$year."')";
			} else {
				if ($county==null || $county=='null') {
					$sql = "CALL `proc_get_eid_national_yearly_hei`('".$year."')";
				} else {
					$sql = "CALL `proc_get_eid_county_yearly_hei`('".$county."','".$year."')";
				}
			}
		} else {
			if ($partner) {
				$sql = "CALL `proc_get_eid_partner_hei`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				if ($county==null || $county=='null') {
					$sql = "CALL `proc_get_eid_national_hei`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					$sql = "CALL `proc_get_eid_county_hei`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			}
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['hei']['name'] = 'Followed-Up';
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

	function age($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_age`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_age`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
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

	function age2($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_age_range`(0, '".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_age_range`(0, '".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_age_range`(0, '".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		
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

	function entry_points($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_entry_points`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_entry_points`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_entry_points`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
				
		$data['entry'][0]['name'] = 'Positive';
		$data['entry'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["entry"][0]["data"][0]	= $count;
		$data["entry"][1]["data"][0]	= $count;
		$data['categories'][0]			= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value['name'];

			$data["entry"][0]["data"][$key]	=  (int) $value['positive'];
			$data["entry"][1]["data"][$key]	=  (int) $value['negative'];
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function mprophylaxis($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_mprophylaxis`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_mprophylaxis`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_mprophylaxis`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
				
		$data['mprophilaxis'][0]['name'] = 'Positive';
		$data['mprophilaxis'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["mprophilaxis"][0]["data"][0]	= $count;
		$data["mprophilaxis"][1]["data"][0]	= $count;
		$data['categories'][0]			= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value['name'];

			$data["mprophilaxis"][0]["data"][$key]	=  (int) $value['positive'];
			$data["mprophilaxis"][1]["data"][$key]	=  (int) $value['negative'];
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function iprophylaxis($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		// Assigning the value of the county
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_iprophylaxis`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_eid_national_iprophylaxis`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_eid_county_iprophylaxis`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
				
		$data['iprophilaxis'][0]['name'] = 'Positive';
		$data['iprophilaxis'][1]['name'] = 'Negative';

		$count = 0;
		
		$data["iprophilaxis"][0]["data"][0]	= $count;
		$data["iprophilaxis"][1]["data"][0]	= $count;
		$data['categories'][0]			= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value['name'];

			$data["iprophilaxis"][0]["data"][$key]	=  (int) $value['positive'];
			$data["iprophilaxis"][1]["data"][$key]	=  (int) $value['negative'];
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county_outcomes($year=null,$month=null,$pfil=null,$partner=null,$county=null,$to_year=null,$to_month=null)
	{
		//Initializing the value of the Year to the selected year or the default year which is current year
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($pfil==null || $pfil=='null') {
			$pfil = NULL;
		}
				
		// echo "PFil: ".$pfil." --Partner: ".$partner." -- County: ".$county;
		if ($county) {
			$sql = "CALL `proc_get_eid_county_sites_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($pfil||$pfil==1) {
				if ($partner) {
					$sql = "CALL `proc_get_eid_partner_sites_outcomes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					$sql = "CALL `proc_get_eid_partner_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_eid_county_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// $sql = "CALL `proc_get_county_outcomes`('".$year."','".$month."')";
		// echo "<pre>";print_r($sql);echo "</pre>";die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

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
				$data["outcomes"][1]["data"][$key]	= (int) $value['negative'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['positive']+(int) $value['negative'])),1);
			
		}

		// echo "<pre>";print_r($data);die();
		return $data;
	} 


	function get_patients($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$type = 0;
		$params;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);
		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}	

		if ($partner) {
			$params = "patient/partner/{$partner}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
		} else {
			if ($county==null || $county=='null') {
				$params = "patient/national/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			} else {
				$query = $this->db->get_where('CountyMFLCode', array('id' => $county), 1)->row();
				$c = $query->CountyMFLCode;

				$params = "patient/county/{$c}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			}
		}

		$result = $this->req($params);

		// echo "<pre>";print_r($result);die();

		$data['stats'] = "<tr><td>" . $result->total_tests . "</td><td>" . $result->one . "</td><td>" . $result->two . "</td><td>" . $result->three . "</td><td>" . $result->three_g . "</td></tr>";

		$data['tests'] = $result->total_tests;
		$data['patients'] = $result->total_patients;

		return $data;
	}

	function get_patients_outcomes($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$type = 0;
		$params;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		if ($partner) {
			$params = "patient/partner/{$partner}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
		} else {
			if ($county==null || $county=='null') {
				$params = "patient/national/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			} else {
				$query = $this->db->get_where('CountyMFLCode', array('id' => $county), 1)->row();
				$c = $query->CountyMFLCode;

				$params = "patient/county/{$c}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			}
		}

		$result = $this->req($params);

		$data['categories'] = array('Total Patients', "Tests Done");
		$data['outcomes']['name'] = 'Tests';
		$data['outcomes']['data'][0] = (int) $result->total_patients;
		$data['outcomes']['data'][1] = (int) $result->total_tests;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}

	function get_patients_graph($year=null,$month=null,$county=null,$partner=null,$to_year=null,$to_month=null)
	{
		$type = 0;
		$params;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		if ($partner) {
			$params = "patient/partner/{$partner}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
		} else {
			if ($county==null || $county=='null') {
				$params = "patient/national/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			} else {
				$query = $this->db->get_where('CountyMFLCode', array('id' => $county), 1)->row();
				$c = $query->CountyMFLCode;

				$params = "patient/county/{$c}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
			}
		}

		$result = $this->req($params);

		$data['categories'] = array('1 Test', '2 Test', '3 Test', '> 3 Test');
		$data['outcomes']['name'] = 'Tests';
		$data['outcomes']['data'][0] = (int) $result->one;
		$data['outcomes']['data'][1] = (int) $result->two;
		$data['outcomes']['data'][2] = (int) $result->three;
		$data['outcomes']['data'][3] = (int) $result->three_g;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}
}
?>