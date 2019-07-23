<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
* 
*/
class Performance_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();;
	}

	function lab_performance_stat($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		// echo round(3.6451895227869, 2, PHP_ROUND_HALF_UP);die();
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_lab_performance_stats`('".$year."','".$month."','".$to_year."','".$to_month."');";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);echo "</pre>";die();
		$ul = '';
		foreach ($result as $key => $value) {
			$name = $value['name'];
			if(!$name) $name = "POC Sites";
			$ul .= "<tr>
						<td>".($key+1)."</td>
						<td>" . $name . "</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round(@(($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) $value['redraw'])."</td>
						<td>".number_format((int) $value['eqa'])."</td>
						<td>".number_format((int) $value['controls'])."</td>
						<td>".number_format((int) ($value['pos']+$value['neg']))."</td>
						<td>".number_format((int) $value['pos'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatspospos'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedpos'])."</td>
						<td>".number_format((int) $value['fake_confirmatory'])."</td>

						<td>".number_format((int) $value['tiebreaker'])."</td>
						<td>".number_format((int) $value['tiebreakerPOS'])."</td>
						
						<td>".number_format((int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']))."</td>
						<td>".number_format((int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']))."</td>						
					</tr>";
					// <td>".number_format((int) $value['alltests'] + (int) $value['eqa'] + (int) $value['confirmdna'] + (int) $value['repeatspos'])."</td>
					// 	<td>".number_format((int) $value['pos'])."</td>
					// 	<td>".round(@(($value['pos']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					// 	<td>".number_format((int) $value['neg'])."</td>
					// 	<td>".round(@(($value['neg']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					// 	<td>".number_format((int) $value['redraw'])."</td>
					// 	<td>".round(@(($value['redraw']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					
		}

		return $ul;
	}

	function poc_performance_stat($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		// echo round(3.6451895227869, 2, PHP_ROUND_HALF_UP);die();
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_poc_performance_stats`('".$year."','".$month."','".$to_year."','".$to_month."');";
		// echo "<pre>";print_r($sql);die();

		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);echo "</pre>";die();
		$ul = '';
		foreach ($result as $key => $value) {
			$name = $value['name'];
			if(!$name) $name = "POC Sites";
			$ul .= "<tr>
						<td>".($key+1)."</td>
						<td>" . $name . "</td>
						<td>" . $value['facilitycode'] . "</td>
						<td>" . $value['countyname'] . "</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round(@(($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) ($value['pos']+$value['neg']))."</td>
						<td>".number_format((int) $value['pos'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatspospos'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedpos'])."</td>
						
						<td>".number_format((int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']))."</td>
						<td>".number_format((int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']))."</td>
						<td> <button class='btn btn-primary'  onclick='expand_poc(" . $value['id'] . ");' style='background-color: #1BA39C;color: white; margin-top: 1em;margin-bottom: 1em;'>View</button> </td>						
					</tr>";

					// <td>".number_format((int) $value['alltests'] + (int) $value['eqa'] + (int) $value['confirmdna'] + (int) $value['repeatspos'])."</td>
					// 	<td>".number_format((int) $value['pos'])."</td>
					// 	<td>".round(@(($value['pos']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					// 	<td>".number_format((int) $value['neg'])."</td>
					// 	<td>".round(@(($value['neg']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					// 	<td>".number_format((int) $value['redraw'])."</td>
					// 	<td>".round(@(($value['redraw']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."</td>
					
		}

		return $ul;
	}


	function poc_performance_details($lab_id=NULL,$year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		// echo round(3.6451895227869, 2, PHP_ROUND_HALF_UP);die();
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_poc_site_details`('".$lab_id."','".$year."','".$month."','".$to_year."','".$to_month."');";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);echo "</pre>";die();
						// <td>".round((($routinesup*100)/$routine), 1, PHP_ROUND_HALF_UP)."</td>
		$ul = '';
		foreach ($result as $key => $value) {
			$name = "POC Sites";
			if($value['name']) $name = $value['name'];
			$ul .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['name']."</td>
						<td>".$value['facilitycode']."</td>
						<td>" . $value['countyname'] . "</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round(@(($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) ($value['pos']+$value['neg']))."</td>
						<td>".number_format((int) $value['pos'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatspospos'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedpos'])."</td>
						
						<td>".number_format((int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']))."</td>
						<td>".number_format((int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']))."</td>	

					</tr>";

						/*<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) ($value['positive'] + $value['repeatsposPOS'] + $value['confirmedPOS']) )."</td>
						<td>".number_format((int) ($value['positive'] + $value['negative']))."</td>
						<td>".number_format((int) $value['positive'])."</td>				
						<td>".number_format((int) $value['infantsless2m'])."</td>				
						<td>".number_format((int) $value['infantsless2mpos'])."</td>*/
		}

		return $ul;
	}

	function download_lab_performance_stat($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_lab_performance_stats`('".$year."','".$month."','".$to_year."','".$to_month."');";
		// echo "<pre>";print_r($sql);die();		

		$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "lab_performance.csv";

        $result = $this->db->query($sql)->result_array();

		$data;

		foreach ($result as $key => $value) {

			$data[$key]['name'] = $value['name']; 
			$data[$key]['sitesending'] = (int) $value['sitesending']; 
			$data[$key]['received'] = $value['received'];
			$data[$key]['rejected'] = $value['rejected']. " (" . 
							round(@(($value['rejected']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP)."%)"; 
			$data[$key]['redrawal'] =  $value['redraw'];
			$data[$key]['alltests'] = $value['alltests'];
			$data[$key]['tests'] = $value['tests'];  
			$data[$key]['confirms'] = (int) $value['confirmdna'] + (int) $value['repeatspos'];  
			$data[$key]['eqa'] = $value['eqa']; 
			$data[$key]['total_tests'] = (int) $value['alltests'] + (int) $value['eqa'] + (int) $value['confirmdna'] + (int) $value['repeatspos']; 
			$data[$key]['pos'] = $value['pos']; 
			$data[$key]['pos_percentage'] = round(@(($value['pos']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP); 
			$data[$key]['neg'] = $value['neg']; 
			$data[$key]['neg_percentage'] = round(@(($value['neg']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP); 
			$data[$key]['redraw'] = $value['redraw']; 
			$data[$key]['redraw_percentage'] = round(@(($value['redraw']*100)/$value['tests']), 1, PHP_ROUND_HALF_UP); 
		}

		// $this->load->helper('download');
        // $this->load->library('PHPReport/PHPReport');

        // ini_set('memory_limit','-1');
	    // ini_set('max_execution_time', 900);

        // $template = 'lab_performance_template.xlsx';

	    //set absolute path to directory with template files
	    // $templateDir = __DIR__ . "/";
	    
	    // //set config for report
	    // $config = array(
	    //     'template' => $template,
	    //     'templateDir' => $templateDir
	    // );


	    //   //load template
	    // $R = new PHPReport($config);
	    
	    // $R->load(array(
	    //         'id' => 'data',
	    //         'repeat' => TRUE,
	    //         'data' => $data   
	    //     )
	    // );
	      
	    //   // define output directoy 
	    // $output_file_dir = __DIR__ ."/tmp/";
	    //  // echo "<pre>";print_r("Still working");die();

	    // $output_file_excel = $output_file_dir  . "lab_performance.xlsx";
	    // //download excel sheet with data in /tmp folder
	    // $result = $R->render('excel', $output_file_excel);
	    // force_download($output_file_excel, null);

		

        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Testing Lab', 'Facilities Served', 'Total Samples Received', 'Rejected Samples (on receipt at lab)', 'Redraws (after testing)', 'All Samples Run', 'Valid Test Results', 'Confirmatory Repeat Tests', 'EQA QA/IQC Tests', 'Total Tests Performed', 'Positives', 'Positives(%)', 'Negatives', 'Negatives(%)', 'Redraws', 'Redraws(%)');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="lab_performance.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);

	}

	function lab_testing_trends($year=NULL)
	{
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		$data['year'] = $year;

		$sql = "CALL `proc_get_eid_lab_performance`('".$year."')";
		// echo "<pre>";print_r($sql);die();

		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$categories = array();
		foreach ($result as $key => $value) {

			$month = (int) $value['month'];
			$month--;

			$lab = (int) $value['lab'];
			$lab--;
			$tests = (int) $value['new_tests'];
			$received = (int) $value['received'];

			$data['test_trends'][$lab]['name'] = $value['name'];
			if(!$data['test_trends'][$lab]['name']) $data['test_trends'][$lab]['name'] = "POC Sites";
			$data['test_trends'][$lab]['data'][$month] = (int) $tests;

			$data['rejected_trends'][$lab]['name'] = $value['name'];
			if(!$data['rejected_trends'][$lab]['name']) $data['rejected_trends'][$lab]['name'] = "POC Sites";
			if($tests == 0){
				$data['rejected_trends'][$lab]['data'][$month] = 0;
			}else{
				$data['rejected_trends'][$lab]['data'][$month] =  
				 round(($value['rejected'] / $received * 100), 1);
			}

			$data['positivity_trends'][$lab]['name'] = $value['name'];
			if(!$data['positivity_trends'][$lab]['name']) $data['positivity_trends'][$lab]['name'] = "POC Sites";
			if($value['pos'] == 0){
				$data['positivity_trends'][$lab]['data'][$month] = 0;
			}else{
				$data['positivity_trends'][$lab]['data'][$month] = (int) 
				(($value['pos']/ ($value['pos'] + $value['neg'])) * 100);
			}

		}

		$this->db->close();

		$sql2 = "CALL `proc_get_eid_average_rejection`('".$year."')";
		// echo "<pre>";print_r($sql);die();
		// echo "<pre>";print_r($sql2);die();
		$result2 = $this->db->query($sql2)->result_array();
			$i = count($data['rejected_trends']);
		$count = 0;
		foreach ($result2 as $key => $value) {
					
			$data['rejected_trends'][$i]['name'] = 'National Rejection Rate';
			$data['rejected_trends'][$i]['data'][$count] = round(@((int) $value['rejected'] * 100 / (int) $value['received']), 1);
			$count++;
		}

		//echo "<pre>";print_r($data);die();

		return $data;
	}

	function lab_outcomes($year=NULL, $month=NULL,$to_year=NULL,$to_month=NULL){
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$data;
		if($month == 0){
			$data['title'] = "Outcomes (" . $year . ")";
		}
		else{
			$data['title'] = "Outcomes (" . $year . ", " . $this->resolve_month($month) . ")";
		}

		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}else {
			$data['title'] = "Outcomes (" . $year . ", " . $this->resolve_month($month) . " - ". $to_year . ", " . $this->resolve_month($to_month) .")";
		} 
		
		$sql = "CALL `proc_get_eid_lab_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		

		
		foreach ($result as $key => $value) {

			$lab = (int) $value['lab'];
			$lab--;

			$data['categories'][$lab] = $value['name'];
			if(!$data['categories'][$lab]) $data['categories'][$lab] = "POC Sites";

			$data['outcomes'][0]['name'] = "Positive";
			$data['outcomes'][0]['data'][$lab] = (int) $value['pos'];


			$data['outcomes'][1]['name'] = "Negative";
			$data['outcomes'][1]['data'][$lab] = (int) $value['neg'];

			// $data['outcomes'][2]['name'] = "Redraws";
			// $data['outcomes'][2]['data'][$lab] = (int) $value['redraw'];

		}
		 // echo "<pre>";print_r($data);die();
		return $data;
	}

	function lab_turnaround($year=NULL,$month=NULL,$to_year=null,$to_month=null){
		$title = null;
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$title = " (" . $year . ")";
			}else {
				$month = $this->session->userdata('filter_month');
				//$title += " (" . $year . ", " . $this->resolve_month($month) . ")";
			}

		}

		if(!$title){
			$title = " (" . $year . ", " . $this->resolve_month($month) . ")";
		}
		 
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}else {
			$title = " (" . $year . ", " . $this->resolve_month($month) . " - ". $to_year . ", " . $this->resolve_month($to_month) .")";
		}

		

		$sql = "CALL `proc_get_eid_lab_tat`('".$year."','".$month."','".$to_year."','".$to_month."')";
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		$data;
		foreach ($result as $key => $value) {
			$lab = (int) $value['lab'];
			$lab--;

			$data[$lab]['div'] = "#container" . ($lab+1);	
			$data[$lab]['div_name'] = "container" . ($lab+1);	
			$name = $value['name'];
			if(!$name) $name = "POC Sites";
			$data[$lab]['name'] = $name . $title;	
			$data[$lab]['tat1'] = round($value['tat1']);	
			$data[$lab]['tat2'] = round($value['tat2']) + $data[$lab]['tat1'];	
			$data[$lab]['tat3'] = round($value['tat3']) + $data[$lab]['tat2'];	
			$data[$lab]['tat4'] = round($value['tat4']);	
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;		
	}

	function yearly_trends($lab=NULL){


		$sql = "CALL `proc_get_eid_yearly_lab_tests`(" . $lab . ");";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);echo "</pre>";die();
		
		$year;
		$i = 0;
		$b = true;

		$data;

		$cur_year = date('Y');

		foreach ($result as $key => $value) {

			if((int) $value['year'] > $cur_year || (int) $value['year'] < 2008){

			}
			else{
				if($b){
					$b = false;
					$year = (int) $value['year'];
				}

				$y = (int) $value['year'];
				if($value['year'] != $year){
					$i++;
					$year--;
				}

				$month = (int) $value['month'];
				$month--;


				$data['test_trends'][$i]['name'] = $value['year'];
				$data['test_trends'][$i]['data'][$month] = (int) $value['tests'];

				$data['rejected_trends'][$i]['name'] = $value['year'];

				if($value['tests'] == 0){
					$data['rejected_trends'][$i]['data'][$month] = 0;
				}else{
					$data['rejected_trends'][$i]['data'][$month] = (int)
					($value['rejected'] / $value['tests'] * 100);
				}

				$data['positivity_trends'][$i]['name'] = $value['year'];

				if ($value['positive'] == 0){
					$data['positivity_trends'][$i]['data'][$month] = 0;
				}else{
					$data['positivity_trends'][$i]['data'][$month] = (int) 
					($value['positive'] / ($value['positive'] + $value['negative']) * 100 );
				}

				

				$data['tat4_trends'][$i]['name'] = $value['year'];
				$data['tat4_trends'][$i]['data'][$month] = (int) $value['tat4'];
			}

		}
		

		return $data;
	}

	function yearly_summary($lab=NULL, $year=NULL){

		if($lab == NULL || $lab == 'null'){
			$lab = 0;
		}

		if($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		$from = $year-1;
		
		$sql = "CALL `proc_get_eid_yearly_lab_summary`(" . $lab . ",'" . $from . "','" . $year . "');";
		// echo "<pre>";print_r($sql);die();
		
		$result = $this->db->query($sql)->result_array();

		$data['outcomes'][0]['name'] = "Redraws";
		$data['outcomes'][1]['name'] = "Positive";
		$data['outcomes'][2]['name'] = "Negative";
		$data['outcomes'][3]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		foreach ($result as $key => $value) {

			$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];
		
			$data['outcomes'][0]['data'][$key] = (int) $value['redraw'];
			$data['outcomes'][1]['data'][$key] = (int) $value['pos'];
			$data['outcomes'][2]['data'][$key] = (int) $value['neg'];
			$data['outcomes'][3]['data'][$key] = round(@( ((int) $value['pos']*100) /((int) $value['neg']+(int) $value['pos']+(int) $value['redraw'])),1);
			
		}
		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "Outcomes";

		return $data;
	}

	function poc_outcomes($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		// echo round(3.6451895227869, 2, PHP_ROUND_HALF_UP);die();
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_poc_performance_stats`('".$year."','".$month."','".$to_year."','".$to_month."');";
		// echo "<pre>";print_r($sql);die();

		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);echo "</pre>";die();

		$data['outcomes'][0]['name'] = "Redraws";
		$data['outcomes'][1]['name'] = "Positive";
		$data['outcomes'][2]['name'] = "Negative";
		$data['outcomes'][3]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' %');


		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['title'] = "";

		foreach ($result as $key => $value) {
			$data['categories'][$key] = $value['name'];

			$pos = (int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']);
			$tests = (int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']);
			$neg = $tests - $pos;
			$redraw = (int) $value['redraw'];
		
			$data['outcomes'][0]['data'][$key] = $redraw;
			$data['outcomes'][1]['data'][$key] = $pos;
			$data['outcomes'][2]['data'][$key] = $neg;
			$data['outcomes'][3]['data'][$key] = round(@( ($pos*100)/$tests), 1);					
		}
		return $data;
	}

	function rejections($lab=NULL, $year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL){	

		if($lab == NULL || $lab == 'null'){
			$lab = 0;
		}

		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);
		
		$sql = "CALL `proc_get_eid_lab_rejections`({$lab}, '{$year}', '{$month}', '{$to_year}', '{$to_month}' );";
		
		// echo "<pre>";print_r($sql);die();
		
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['outcomes'][0]['name'] = "Rejected Samples";
		$data['outcomes'][1]['name'] = "% Rejected";

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;

		$total = 0;
		foreach ($result as $key => $value) {
			$total += $value['total'];
		}
		$data['categories'][0] = 'No Data';
		$data['outcomes'][0]['data'][0] = 0;
		$data['outcomes'][1]['data'][0] = 0;
		foreach ($result as $key => $value) {
			$data['categories'][$key] = $value['name'];
		
			$data['outcomes'][0]['data'][$key] = (int) $value['total'];
			$data['outcomes'][1]['data'][$key] = round(($value['total']/$total)*100,1);
		}
		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' %');

		if($lab == 0){
			$data['title'] = "National Rejections";
		}
		else{
			$data['title'] = "Lab Rejections";
		}


		return $data;
	}


	function lab_mapping($lab=NULL, $year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL){	

		if($lab == NULL || $lab == 'null'){
			$lab = 0;
		}

		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);
		
		$sql = "CALL `proc_get_eid_lab_site_mapping`({$lab}, '{$year}', '{$month}', '{$to_year}', '{$to_month}' );";
		
		// echo "<pre>";print_r($sql);die();
		
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['title'] = "Tests";
		foreach ($result as $key => $value) {
			
				$data['outcomes'][$key]['id'] = (int) $value['county'];
				
				$data['outcomes'][$key]['value'] = (int) $value['value'];
				
			
		}


		return $data;
	}


}