<?php
(defined('BASEPATH') or exit('No direct script access allowed!'));

/**
* 
*/
class Regimen_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function regimen_outcomes($year=null,$month=null,$to_year=null,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);
		$sql = "CALL `proc_get_eid_iprophylaxis`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		
		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
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
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['neg']+(int) $value['pos'])),1);

			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function get_testing_trends($year=null,$regimen=null)
	{
		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
		}

		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;

		$sql = "CALL `proc_get_eid_iproph_testing_trends`('".$regimen."','".$from."','".$to."')";

		return $this->db->query($sql)->result();
	}

	function regimen_testing_trends($year=null,$regimen=null)
	{
		
		$result = $this->get_testing_trends($year,$regimen);

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
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
			
				$data['categories'][$key] = $this->resolve_month($value->month).'-'.$value->year;

				$data["outcomes"][0]["data"][$key]	= (int) $value->pos;
				$data["outcomes"][1]["data"][$key]	= (int) $value->neg;
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value->pos*100) /(int) $value->tests),1);

			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function download_testing_trends($year=null,$regimen=null)
	{
		$data = $this->get_testing_trends($year,$regimen);
		// echo "<pre>";print_r((array) $data);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array('Year', 'Month', 'Tests', 'Positive', 'Negative', 'Redraw');

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, (array)$line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('YmdH:i:s').'EID Regimen Trends.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function get_regimen_breakdown($year=null,$month=null,$to_year=null,$to_month=null,$regimen=null,$county=null,$subcounty=null,$partner=null)
	{
		if ($county == null || $county == 'null') {
			$county = 0;
		}else {
			$modal_name = 'countyModal';
			$div_name = 'countyDiv';
		}
		if ($subcounty == null || $subcounty == 'null') {
			$subcounty = 0;
		} else {
			$modal_name = 'subCountyModal';
			$div_name			 = 'subCountyDiv';
		}
		if ($partner == null || $partner == 'null') {
			$partner = 0;
		} else {
			$modal_name = 'partnerModal';
			$div_name = 'partnerDiv';
		}
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['regimen' => $regimen, 'county' => $county, 'subcounty' => $subcounty, 'partner' => $partner]);
		extract($d);

		$sql = "CALL `proc_get_eid_iproph_breakdown`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$county."','".$subcounty."','".$partner."')";

		$data = $this->db->query($sql)->result();
		// echo "<pre>";print_r($data);die();
		$li = '';
		$table = '';
		$count = 1;
		if($data)
			{
				foreach ($data as $key => $value) {
					$percentage = @round((@$value->pos/@$value->tests)*100,1);
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value->name.'.&nbsp;'.$percentage.'%&nbsp;('.number_format($value->pos).')</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value->name.'</td>';
					$table .= '<td>'.$percentage.'%</td>';
					$table .= '<td>'.number_format((int) $value->pos).'</td>';
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
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function get_regimen_counties($year=null,$month=null,$to_year=null,$to_month=null,$regimen=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['regimen' => $regimen]);
		extract($d);
		$county = 1;
		$zeros = 0;
		$sql = "CALL `proc_get_eid_iproph_breakdown`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$county."','','')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		
		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
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
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['pos']*100) /((int) $value['neg']+(int) $value['pos'])),1);

			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}
}
?>