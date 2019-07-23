<?php
(defined('BASEPATH') or exit('No direct script access allowed!'));

/**
* 
*/
class Tat_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function outcomes($year=null, $month=null, $to_year=null, $to_month=null, $type=null, $id=null)
	{
		$data['outcomes'][0]['name'] = "Processing-Dispatch (P-D)";
		$data['outcomes'][1]['name'] = "Receipt to-Processing (R-P)";
		$data['outcomes'][2]['name'] = "Collection-Receipt (C-R)";
		$data['outcomes'][3]['name'] = "Collection-Dispatch (C-D)";

		$data['outcomes'][0]['color'] = 'rgba(0, 255, 0, 0.498039)';
		$data['outcomes'][1]['color'] = 'rgba(255, 255, 0, 0.498039)';
		$data['outcomes'][2]['color'] = 'rgba(255, 0, 0, 0.498039)';
		// $data['outcomes'][0]['color'] = '#26C281';
		// $data['outcomes'][1]['color'] = '#FABE58';
		// $data['outcomes'][2]['color'] = '#EF4836';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' Days');

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';
		$data["outcomes"][0]["data"][0]	= 0;
		$data["outcomes"][1]["data"][0]	= 0;
		$data["outcomes"][2]["data"][0]	= 0;
		$data["outcomes"][3]["data"][0]	= 0;

		$result = $this->_getData($year, $month, $to_year, $to_month, $type, $id);
		
		foreach ($result as $key => $value) {
			if ($key < 70) {
				$data['categories'][$key] = $value->name;
				$data["outcomes"][0]["data"][$key]	= round($value->tat3,1);
				$data["outcomes"][1]["data"][$key]	= round($value->tat2,1);
				$data["outcomes"][2]["data"][$key]	= round($value->tat1,1);
				$data["outcomes"][3]["data"][$key]	= round($value->tat4,1);
			}
		}

		return $data;
	}

	function details($year=null, $month=null, $to_year=null, $to_month=null, $type=null, $id=null)
	{
		$result = $this->_getData($year, $month, $to_year, $to_month, $type, $id);
		// echo "<pre>";print_r($result);die();
		$count = 1;
		$table = '';
		foreach ($result as $key => $value) {
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value->name.'</td>';
			$table .= '<td>'.number_format($value->tat1).'</td>';
			$table .= '<td>'.number_format($value->tat2).'</td>';
			$table .= '<td>'.number_format($value->tat3).'</td>';
			$table .= '<td>'.number_format($value->tat4).'</td>';
			$table .= '</tr>';
			$count++;
		}
		return $table;
	}

	public function _getData($year=null, $month=null, $to_year=null, $to_month=null, $type=null, $id=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county, 'partner' => $partner]);
		extract($d);
		if ($type==null || $type=='null') $type = 0;
		if ($type == 0 || $type == '0') {
			if ($id==null || $id=='null') $id = $this->session->userdata('county_filter');
		} else if ($type == 1 || $type == '1') {
			if ($id==null || $id=='null') $id = $this->session->userdata('partner_filter');
		} else if ($type == 2 || $type == '2') {
			if ($id==null || $id=='null') $id = $this->session->userdata('sub_county_filter');
		} else if ($type == 3 || $type == '3') {
			if ($id==null || $id=='null') $id = $this->session->userdata('site_filter');
		}
	
		if ($id==null || $id=='null') $id = 0;
		
		$sql = "CALL `proc_get_eid_tat_ranking`('".$year."','".$month."','".$to_year."','".$to_month."','".$type."','".$id."')";
		
		return $this->db->query($sql)->result();
	}
}

?>