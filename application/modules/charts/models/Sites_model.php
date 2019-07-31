<?php
defined('BASEPATH') or exit();

/**
* 
*/
class Sites_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}


	function sites_outcomes($year=null,$month=null,$to_year=null,$to_month=null){
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_all_sites_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo $sql;die();
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
				$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
				$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['pos']+(int) $value['neg'])),1);
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;

	}

	function unsupported_sites(){
		$sql = "CALL `proc_get_eid_unsupported_facilities`()";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$ul = '';
		foreach ($result as $key => $value) {
			$ul .= "<tr>
				<td>" . ($key+1) . "</td>
				<td>" . $value['facilitycode'] . "</td>
				<td>" . $value['DHIScode'] . "</td>
				<td>" . $value['name'] . "</td>
				<td>" . $value['county'] . "</td>
				<td>" . $value['subcounty'] . "</td>
			</tr>";
		}

		return $ul;

	}

	function download_unsupported_sites(){
		// $sql = "CALL `proc_get_eid_unsupported_facilities`()";

		// $data = $this->db->query($sql)->result_array();

		// $this->load->helper('download');
        // $this->load->library('PHPReport/PHPReport');


        // $template = 'unsupported_sites_template.xlsx';

	 //    //set absolute path to directory with template files
	 //    $templateDir = __DIR__ . "/";
	    
	 //    //set config for report
	 //    $config = array(
	 //        'template' => $template,
	 //        'templateDir' => $templateDir
	 //    );

	 //    ini_set('memory_limit','-1');
	 //    ini_set('max_execution_time', 900);


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
	 //     // echo "<pre>";print_r("Still working");die();

	 //    $output_file_excel = $output_file_dir  . "unsupported_sites.xlsx";
	 //    //download excel sheet with data in /tmp folder
	 //    $result = $R->render('excel', $output_file_excel);
	 //    force_download($output_file_excel, null);

		$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "eid_unsupported_sites.csv";
        $sql = "CALL `proc_get_eid_unsupported_facilities`()";
        $result = $this->db->query($sql);
        // echo "<pre>";print_r($result);die();
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);


	}

	function partner_sites_outcomes($year=NULL,$month=NULL,$site=NULL,$partner=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site, 'partner' => $partner]);
		extract($d);

		$sql = "CALL `proc_get_eid_partner_sites_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$initial = $value['positive']+$value['negative'];
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value['MFLCode'].'</td>';
			$table .= '<td>'.$value['name'].'</td>';
			$table .= '<td>'.$value['county'].'</td>';
			$table .= '<td>'.$value['subcounty'].'</td>';
			$table .= '<td>'.number_format($value['alltests']).'</td>';
			$table .= '<td>'.number_format($value['actualinfants']).'</td>';
			$table .= '<td>'.number_format($initial).'</td>';
			$table .= '<td>'.number_format($value['positive']).'</td>';
			$table .= '<td>'.number_format($value['repeatspos']).'</td>';
			$table .= '<td>'.number_format($value['repeatsposPOS']).'</td>';
			$table .= '<td>'.number_format($value['confirmdna']).'</td>';
			$table .= '<td>'.number_format($value['confirmedPOS']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2w']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2wpos']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsless2mpos']).'</td>';
			$table .= '<td>'.round(($value['infantsless2m']/$initial)*100, 2).'%</td>';
			$table .= '<td>'.number_format($value['infantsabove2m']).'</td>';
			$table .= '<td>'.number_format($value['infantsabove2mpos']).'</td>';
			$table .= '<td>'.number_format($value['noage']).'</td>';
			$table .= '<td>'.number_format($value['adult']).'</td>';
			$table .= '<td>'.number_format($value['medage']).'</td>';
			$table .= '<td>'.number_format($value['rejected']).'</td>';
			$table .= '</tr>';
			$count++;
		}
		

		return $table;
	}

	function partner_sites_outcomes_download($year=NULL,$month=NULL,$partner=NULL,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['partner' => $partner]);
		extract($d);

		$sql = "CALL `proc_get_eid_partner_sites_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$data = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($data);die();

		// $this->load->helper('download');
        // $this->load->library('PHPReport/PHPReport');

        // ini_set('memory_limit','-1');
	 //    ini_set('max_execution_time', 900);


        // $template = 'partner_sites.xlsx';

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
	 //     // echo "<pre>";print_r("Still working");die();

	 //    $output_file_excel = $output_file_dir  . "partner_sites.xlsx";
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

	    $b = array('MFL Code', 'Name', 'County', 'All Tests', 'Actual Infants Tested', 'Repeat Confirmatory Tests', 'Positives', 'Negatives', 'Redraws', 'Infants < 2weeks Tests', 'Infants < 2weeks Positives', 'Infants <= 2M Tests', 'Infants <= 2M Positives', 'Infants >= 2M Tests', 'Infants >= 2M Positives', 'Median Age', 'Rejected');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="eid_partner_sites.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}

	function partner_supported_sites_download($partner=NULL)
	{
		$this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "eid_partner_supported_sites.csv";
        $sql = "CALL `proc_get_eid_partner_supported_sites`('".$partner."')";
        $result = $this->db->query($sql);
        // echo "<pre>";print_r($result);die();
        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);
	}

	function get_trends($site=null, $year=null){

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		
		$sql = "CALL `proc_get_eid_sites_trends`('".$site."', '".$year."')";

		$result = $this->db->query($sql)->result_array();

		$data;

		$data['year'] = $year;

		
		$data['trends'][0]['name'] = "Tests";
		$data['trends'][1]['name'] = "Initial PCR";
		$data['trends'][2]['name'] = "Rejected";
		$data['trends'][3]['name'] = "Positives";
		$data['trends'][4]['name'] = "Negatives";

		foreach ($result as $key => $value) {

			$month = (int) $value['month'];
			$month--;


			$data['trends'][0]['data'][$month] = (int) $value['tests'];
			$data['trends'][1]['data'][$month] = (int) $value['initial_pcr'];
			$data['trends'][2]['data'][$month] = (int) $value['rejected'];
			$data['trends'][3]['data'][$month] = (int) $value['pos'];
			$data['trends'][4]['data'][$month] = (int) $value['neg'];
		}
		
		$data['title'] = "Test Trends (" . $year . ")";
		$data['div'] = "#tests";
		$data['div_name'] = "tests";
		$data['suffix'] = " ";
		$data['yAxis'] = "Number of Tests";

		return $data;
	}


	function get_positivity($site=null, $year=null){

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		
		$sql = "CALL `proc_get_eid_sites_trends`('".$site."','".$year."')";

		$result = $this->db->query($sql)->result_array();

		$data;
		$data['year'] = $year;
		$data['trends'][0][0] = 0;
		$data['trends'][1][0] = 0;

		foreach ($result as $key => $value) {

			$month = (int) $value['month'];
			$month--;


			$data['trends'][0][$month] = (int) $value['pos'];

			if ($value['pos'] == 0 && $value['neg'] == 0){
				$data['trends'][1][$month] = 0;
			}else{
				$data['trends'][1][$month] = (int) ($value['pos'] / 
					($value['pos'] + $value['neg']) * 100);
			}
		}
		$data['title'] = "Positivity (" . $year . ")";
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function get_eid($site=null, $year=null, $month=null,$to_year=null, $to_month=null){

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		$data['title'] = "EID Outcome (" . $year . ", " . $this->resolve_month($month) . ")";
	
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}else {
			$data['title'] = "EID Outcome (" . $year . ", " . $this->resolve_month($month) . " - ".$this->resolve_month($to_month).")";
		}
		
		$sql = "CALL `proc_get_eid_sites_eid`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";

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
					<td>Positive Outcomes</td>
					<td>'.number_format((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])).'('.round((((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])/(int) ($value['firstdna']+$value['confirmdna']+$value['repeatspos']))*100),1).'%)</td>
				</tr>
				<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initial PCR:</td>
		    		<td>'.number_format((int) $value['firstdna']).'</td>
		    		<td>Positive Outcomes:</td>
		    		<td>'.number_format((int) $value['pos']).'('.round((((int) $value['pos']/(int) $value['firstdna'])*100),1).'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2nd/3rd PCR:</td>
		    		<td>'.number_format((int) $value['repeatspos']).'</td>
		    		<td>Positive Outcomes:</td>
		    		<td>'.number_format((int) $value['repeatsposPOS']).'('.round((((int) $value['repeatsposPOS']/(int) $value['repeatspos'])*100),1).'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Confirmatory PCR:</td>
		    		<td>'.number_format((int) $value['confirmdna']).'</td>
		    		<td>Positive Outcomes:</td>
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
		    		<td>Positive Outcomes:</td>
		    		<td>'.number_format((int) $value['actualinfantspos']).'('. round((((int) $value['actualinfantspos']/(int) $value['actualinfants'])*100),1)  .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infants &lt;= 2M:</td>
		    		<td>'.number_format((int) $value['infantsless2m']).'</td>
		    		<td>Infants &lt;= 2M Positive:</td>
		    		<td>'.number_format((int) $value['infantless2mpos']).'('.round((((int) $value['infantless2mpos']/(int) $value['infantsless2m'])*100),1).'%)</td>
		    	</tr>

		    	<tr>
		    		<td>Adults Tested:</td>
		    		<td>'.number_format((int) $value['adults']).'</td>
		    		<td>Positive Outcomes:</td>
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
			// $data['ul'] .= '<li>Cumulative No. of Tests: <strong>'.(int) $value['alltests'].'</strong></li>';
			// $data['ul'] .= '<li>Cumulative No. of EQA Tests: <strong>'.(int) $value['eqatests'].'</strong></li>';
			// $data['ul'] .= '<li>No. of All Infants Tested: <strong>'.(int) $value['tests'].'</strong></li>';
			// $data['ul'] .= '<li>No. of All Infants Tested ( < 2 months): <strong>'.(int) $value['infantsless2m'].'</strong></li>';
			// $data['ul'] .= '<li>No. of first DNA PCR Test: <strong>'.(int) $value['firstdna'].'</strong></li>';
			// $data['ul'] .= '<li>No of Confirmatory PCR Test @9M: <strong>'.(int) $value['confirmdna'].'</strong></li>';
			// $data['ul'] .= '<li>Median Age of Testing (Months): <strong>'.$value['medage'].'</strong></li>';
			// $data['ul'] .= '<li>Rejected Samples: <strong>'.$value['rejected'].'</strong></li>';
			// $data['ul'] .= '<li>Sites Sending: <strong>'.(int) $value['sitessending'].'</strong></li>';
			// if($value['name'] == ''){
			// 	$data['hei']['data'][$key]['color'] = '#5C97BF';
			// }
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

	function get_hei_validation($site=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		$data['title'] = "Actual Infants Tested Positive Validation at Site Outcomes (" . $year . ", " . $this->resolve_month($month) . ")";

		if ($month==null || $month=='null') {
			$data['title'] = "Actual Infants Tested Positive Validation at Site Outcomes (" . $year . ")";
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}else {
			$data['title'] = "Actual Infants Tested Positive Validation at Site Outcomes (" . $year . ", " . $this->resolve_month($month) . " - ".$this->resolve_month($to_month).")";
		}

		if ($month == 0) {
			$sql = "CALL `proc_get_eid_site_yearly_hei_validation`('".$site."', '".$year."')";
		} else {
			$sql = "CALL `proc_get_eid_site_hei_validation`('".$site."', '".$year."', '".$month."','".$to_year."', '".$to_month."')";
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
                // <td>Validated Positives:</td>
                //     <td>'.number_format((int) $value['followup_positives']).'<b>('.round((((int) $value['followup_positives']/(int) $value['positives'])*100),1).'%)</b></td>
                //     <td></td>
                //     <td></td>
                // </tr>
 
                // <tr>
                //     <td>Confirmed Actual positive Infants:</td>
                //     <td>'.number_format((int) $value['Confirmed Positive']).'<b>('.round((((int) $value['Confirmed Positive']/(int) $value['true_tests'])*100),1).'%)</b></td>
                //     <td></td>
                //     <td></td>
                // </tr>';
				$data['ul'] .= '<tr>
                 <td>Actual Infants Tested Positive:</td>
                     <td>'.number_format((int) $value['positives']).'</td>
                     <td></td>
                     <td></td>
                </tr><tr>
                 <td>Actual Infants Validated at Site:</td>
                     <td>'.number_format((int) $value['followup_hei']).'<b>('.round((((int) $value['followup_hei']/(int) $value['positives'])*100),1).'%)</b></td>
                     <td></td>
                     <td></td>
                </tr>
               	<tr>
                   <td>Actual Confirmed Positives at Site:</td>
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

	function get_hei($site=null, $year=null, $month=null,$to_year=null,$to_month=null){


		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		$data['title'] = "Status of Actual Confirmed Positives at Site (" . $year . ", " . $this->resolve_month($month) . ")";


		if ($month==null || $month=='null') {
			$data['title'] = "Status of Actual Confirmed Positives at Site (" . $year . ")";
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		
		// proc_get_eid_sites_hei_follow_up
		
		if ($month == 0) {
			$sql = "CALL `proc_get_eid_sites_yearly_hei_follow_up`('".$site."', '".$year."')";
		} else {
			$sql = "CALL `proc_get_eid_sites_hei_follow_up`('".$site."', '".$year."', '".$month."','".$to_year."','".$to_month."')";
		}
		
		$result = $this->db->query($sql)->row();
		// echo "<pre>";print_r($result);die();
		$data['trend'][0]['name'] = "Initiated On Treatment";
		$data['trend'][1]['name'] = "Dead";
		$data['trend'][2]['name'] = "Lost to Follow Up";
		$data['trend'][3]['name'] = "Transferred Out";
		$data['trend'][4]['name'] = "Other Reasons";

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

		$str = "Initiated On Treatment: " . number_format($data['trend'][0]['y']) . " <b>(" . $data['per'][0] . "%)</b>";
		$str .= "<br />Lost to Follow Up: " . number_format($data['trend'][2]['y']) . " <b>(" . $data['per'][2] . "%)</b>";
		$str .= "<br />Dead: " . number_format($data['trend'][1]['y']) . " <b>(" . $data['per'][1] . "%)</b>";
		$str .= "<br />Transferred out: " . number_format($data['trend'][3]['y']) . " <b>(" . $data['per'][3] . "%)</b>";
		$str .= "<br />Other Reasons(e.g denial): " . number_format($data['other'][1]) . " <b>(" . $data['per'][5] . "%)</b>";

		$str = '<li>Initiated On Treatment: '.number_format((int) $data['trend'][0]['y']).' <strong>('.(int) $data['per'][0].'%)</strong></li>';
		$str .= '<li>Lost to Follow Up: '.number_format($data['trend'][2]['y']).' <strong>('.(int) $data['per'][2].'%)</strong></li>';
		$str .= '<li>Dead: '.number_format((int) $data['trend'][1]['y']).' <strong>('.(int) $data['per'][1].'%)</strong></li>';
		$str .= '<li>Transferred Out: '.number_format($data['trend'][3]['y']).' <strong>('.(int) $data['per'][3].'%)</strong></li>';
		$str .= '<li>Other Reasons(e.g denial): '.number_format($data['other'][1]).' <strong>('.(int) $data['per'][5].'%)</strong></li>';

		$data['stats'] = $str;


		$data['div'] = "hei_pie";
		$data['content'] = "hei_content";
		
		

		return $data;
	}

	function get_patients($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		$data['stats'] = "<tr><td>" . $result->total_tests . "</td><td>" . $result->one . "</td><td>" . $result->two . "</td><td>" . $result->three . "</td><td>" . $result->three_g . "</td></tr>";

		$data['tests'] = $result->total_tests;
		$data['patients'] = $result->total_patients;

		return $data;
	}

	function get_patients_outcomes($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		$data['categories'] = array('Total Patients', "Tests Done");
		$data['outcomes']['name'] = 'Tests';
		$data['outcomes']['data'][0] = (int) $result->total_patients;
		$data['outcomes']['data'][1] = (int) $result->total_tests;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}

	function get_patients_graph($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		$data['categories'] = array('1 Test', '2 Tests', '3 Tests', '> 3 Tests');
		$data['outcomes']['name'] = 'Tests';
		$data['outcomes']['data'][0] = (int) $result->one;
		$data['outcomes']['data'][1] = (int) $result->two;
		$data['outcomes']['data'][2] = (int) $result->three;
		$data['outcomes']['data'][3] = (int) $result->three_g;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}

	public function age_breakdown($site=null,$year=null,$month=null,$to_year=null,$to_month=null) {
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);

		$sql = "CALL `proc_get_eid_site_age_range`(0, '".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
				
		$data['ageGnd'][0]['name'] = 'Positive';
		$data['ageGnd'][1]['name'] = 'Negative';
		$data['categories'][0] 			= 'No Data';
		$data["ageGnd"][0]["data"][0]	=  0;
		$data["ageGnd"][1]["data"][0]	=  0;
		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['age_range'];

			$data["ageGnd"][0]["data"][$key]	=  (int) $value['pos'];
			$data["ageGnd"][1]["data"][$key]	=  (int) $value['neg'];
		}
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][1]['drilldown']['color'] = '#96281B';

		return $data;
	}

	function entry_points($site=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);

		$sql = "CALL `proc_get_eid_site_entry_points`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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

	function mprophylaxis($site=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		$sql = "CALL `proc_get_eid_site_mprophylaxis`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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

	function iprophylaxis($site=null,$year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['site' => $site]);
		extract($d);
		
		$sql = "CALL `proc_get_eid_site_iprophylaxis`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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

	
}
?>