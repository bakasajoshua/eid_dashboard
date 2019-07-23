<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Positivity_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function notification_bar($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$data['year'] = $year;
		$data['month'] = '';

		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}else {
			$data['month'] = ' as of '.$this->resolve_month($month);
		}

		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}else {
			$data['month'] .= ' to '.$this->resolve_month($to_month).' of '.$to_year;
		}

		if ($month == 0) {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_national_positivity_yearly_notification`('".$year."')";
			} else {
				$sql = "CALL `proc_get_county_positivity_yearly_notification`('".$county."','".$year."')";
			}
		} else {
			if ($county==null || $county=='null') {
				$sql = "CALL `proc_get_national_positivity_notification`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_county_positivity_notification`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				// $data['county'] = $county;
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		
		foreach ($result as $key => $value) {
			$data['rate'] = round($value['positivity_rate'], 1);
			$data['sustxfail'] = number_format((int) $value['positive']);
			if ((int) $value['positivity_rate']=0) {
				$data['color'] = '#E4F1FE';
			} else if ($value['positivity_rate']>0 && $value['positivity_rate']<10) {
				$data['color'] = '#E4F1FE';
			} else if($value['positivity_rate']>=10 && $value['positivity_rate']<50) {
				$data['color'] = '#E4F1FE';
			} else if($value['positivity_rate']>=50 && $value['positivity_rate']<90) {
				$data['color'] = '#E4F1FE';
			} else if($value['positivity_rate']>=90 && $value['positivity_rate']<100) {
				$data['color'] = '#E4F1FE';
			}
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function age($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		// if ($county==null || $county=='null') {
		// 	$sql = "CALL `proc_get_eid_national_age_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// } else {
		// 	$sql = "CALL `proc_get_eid_county_age_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// }
		
		// echo "<pre>";print_r($sql);die();
		// $result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		// $data['positivity'][0]['name'] = 'Positives';
		// $data['positivity'][1]['name'] = 'Negatives';

		// $count = 0;
		
		// $data["positivity"][0]["data"][0]	= $count;
		// $data["positivity"][1]["data"][0]	= $count;
		// $data['categories'][0]				= 'No Data';
		// $data['categories'][1]				= '< 2 Weeks';
		// $data['categories'][2]				= '2 - 6 Weeks';
		// $data['categories'][3]				= '6 - 8 Weeks';
		// $data['categories'][4]				= '6 Months';
		// $data['categories'][5]				= '9 Months';
		// $data['categories'][6]				= '12 Months';

		// foreach ($result as $key => $value) {
		// 	$data["positivity"][0]["data"][0]	=  (int) $value['nodatapos'];
		// 	$data["positivity"][1]["data"][0]	=  (int) $value['nodataneg'];
		// 	$data["positivity"][0]["data"][1]	=  (int) $value['less2wpos'];
		// 	$data["positivity"][1]["data"][1]	=  (int) $value['less2wneg'];
		// 	$data["positivity"][0]["data"][2]	=  (int) $value['twoto6wpos'];
		// 	$data["positivity"][1]["data"][2]	=  (int) $value['twoto6wneg'];
		// 	$data["positivity"][0]["data"][3]	=  (int) $value['sixto8wpos'];
		// 	$data["positivity"][1]["data"][3]	=  (int) $value['sixto8wneg'];
		// 	$data["positivity"][0]["data"][4]	=  (int) $value['sixmonthpos'];
		// 	$data["positivity"][1]["data"][4]	=  (int) $value['sixmonthneg'];
		// 	$data["positivity"][0]["data"][5]	=  (int) $value['ninemonthpos'];
		// 	$data["positivity"][1]["data"][5]	=  (int) $value['ninemonthneg'];
		// 	$data["positivity"][0]["data"][6]	=  (int) $value['twelvemonthpos'];
		// 	$data["positivity"][1]["data"][6]	=  (int) $value['twelvemonthneg'];
		// }
		// echo "<pre>";print_r($data);die();

		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_national_age_range`(0, '".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_age_range`(0, '".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}

		$result = $this->db->query($sql)->result_array();
				
		// echo "<pre>";print_r($result);die();
		$data['positivity'][0]['name'] = 'Positive';
		$data['positivity'][1]['name'] = 'Negative';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['age_range'];

			$data["positivity"][0]["data"][$key]	=  (int) $value['pos'];
			$data["positivity"][1]["data"][$key]	=  (int) $value['neg'];
		}
		$data['positivity'][0]['drilldown']['color'] = '#913D88';
		$data['positivity'][1]['drilldown']['color'] = '#96281B';
		

		return $data;
	}


	function iprophylaxis($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_national_iproph_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_iproph_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['positivity'][0]['name'] = 'Positives';
		$data['positivity'][1]['name'] = 'Negatives';

		$count = 0;
		
		$data["positivity"][0]["data"][0]	= $count;
		$data["positivity"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["positivity"][0]["data"][$key]	=  (int) $value['pos'];
			$data["positivity"][1]["data"][$key]	=  (int) $value['neg'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function mprophylaxis($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_national_mproph_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_mproph_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['positivity'][0]['name'] = 'Positives';
		$data['positivity'][1]['name'] = 'Negatives';

		$count = 0;
		
		$data["positivity"][0]["data"][0]	= $count;
		$data["positivity"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["positivity"][0]["data"][$key]	=  (int) $value['pos'];
			$data["positivity"][1]["data"][$key]	=  (int) $value['neg'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function entryPoint($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_national_entryP_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_entryP_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['positivity'][0]['name'] = 'Positives';
		$data['positivity'][1]['name'] = 'Negatives';

		$count = 0;
		
		$data["positivity"][0]["data"][0]	= $count;
		$data["positivity"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["positivity"][0]["data"][$key]	=  (int) $value['pos'];
			$data["positivity"][1]["data"][$key]	=  (int) $value['neg'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county_listings($year=NULL,$month=NULL,$to_year=NULL,$to_month=null)
	{
		$li = '';
		$table = '';
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_counties_positivity_stats`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($result);die();
		$count = 1;
		$listed = FALSE;

		if($result)
		{
			foreach ($result as $key => $value)
			{
				if ($count<16) {
					$li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.round($value['pecentage'],1).'%&nbsp;('.number_format($value['pos']).')</a>';
				}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.round($value['pecentage'],1).'%</td>';
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
						'table' => $table);
		return $data;
	}

	function subcounty_listings($year=null,$month=null,$county=null,$to_year=NULL,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);
		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_nat_subcounties_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_subcounties_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.round($value['pecentage'],1).'%&nbsp;('.number_format($value['pos']).')</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.round($value['pecentage'],1).'%</td>';
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
					'table' => $table);
		return $data;
	}

	function partners($year=null,$month=null,$county=null,$to_year=NULL,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);
		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_nat_partner_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_partner_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.round($value['pecentage'],1).'%&nbsp;('.number_format($value['pos']).')</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.round($value['pecentage'],1).'%</td>';
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
					'table' => $table);
		return $data;
	}

	function facility_listing($year=null,$month=null,$county=NULL,$to_year=NULL,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);
		
		if ($county==null || $county=='null') {
			$sql = "CALL `proc_get_eid_sites_positivity`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_eid_county_sites_positivity`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.round($value['positivity'],1).'%&nbsp;('.number_format($value['pos']).')</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.round($value['positivity'],1).'%</td>';
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
						'table' => $table);
		return $data;
	}

	function county_positivities($year=null,$month=null,$county=NULL,$to_year=NULL,$to_month=null)
	{
		$d = $this->extract_variables($year, $month, $to_year, $to_month, ['county' => $county]);
		extract($d);

		$sql = "CALL `proc_get_eid_counties_positivity_stats`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['positivity'][0]['name'] = 'Positives';
		$data['positivity'][1]['name'] = 'Negatives';

		$count = 0;
		
		$data["positivity"][0]["data"][0]	= $count;
		$data["positivity"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["positivity"][0]["data"][$key]	=  (int) $value['pos'];
			$data["positivity"][1]["data"][$key]	=  (int) $value['neg'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}
	

	function county_mixed($year=null,$month=null,$to_year=NULL,$to_month=null)
	{
		
		$d = $this->extract_variables($year, $month, $to_year, $to_month);
		extract($d);

		$sql = "CALL `proc_get_eid_counties_positivity_mixed`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

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

		$data['title'] = "Outcomes";



		$data['positivity'][0]['name'] = 'Positives';
		$data['positivity'][1]['name'] = 'Negatives';

		

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];

			$data['outcomes'][0]['data'][$key] = (int) $value['pos'];
			$data['outcomes'][1]['data'][$key] = (int) $value['neg'];
			$data['outcomes'][2]['data'][$key] = round($value['pecentage'], 1);
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}


}
?>