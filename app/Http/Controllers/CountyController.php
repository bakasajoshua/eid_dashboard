<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class CountyController extends Controller
{

	public function county_outcomes($var = 1)
	{
		extract($this->get_filters());
		if ($var == 1) $sql = "CALL `proc_get_eid_county_outcomes`(".$year_month_query.")";
		else if ($var == 2) $sql = "CALL `proc_get_eid_top_subcounty_outcomes`(".$year_month_query.")";
		else if ($var == 4) $sql = "CALL `proc_get_eid_all_sites_outcomes`(".$year_month_query.")";
		else if ($var == 5) $sql = "CALL `proc_get_eid_lab_outcomes`(".$year_month_query.")";
		else {
			$sql = "CALL `proc_get_eid_county_outcomes`(".$year_month_query.")";
		}
		
		$result = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', [], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;


		foreach ($result as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['name'];
			if(!$data['categories'][$key]) $data['categories'][$key] = "POC Sites";

			$data["outcomes"][0]["data"][$key]	= (int) ($value['positive'] ?? $value['pos'] ?? 0);
			if ($var == 2) {
				$data["outcomes"][1]["data"][$key]	= (int) ($value['actual']-$value['positive']);
				$total = $value['positive'] + ($value['actual']-$value['positive']);
			} else {
				$data["outcomes"][1]["data"][$key]	= (int) ($value['negative'] ?? $value['neg'] ?? 0);
				$total = ($value['positive'] ?? $value['pos'] ?? 0) + ($value['negative'] ?? $value['neg'] ?? 0);
			}
			
			$data["outcomes"][2]["data"][$key]	= Lookup::get_percentage(($value['positive'] ?? $value['pos'] ?? 0), $total, 1);
			
		}
		return view('charts.dual_axis', $data);
	}

	public function counties_details($var = 1)
	{
		extract($this->get_filters());
		if ($var == 2){
			$sql = "CALL `proc_get_eid_subcountys_details`(".$year_month_query.")";
			$data['subcounty'] = $var;
		} else {
			$sql = "CALL `proc_get_eid_countys_details`(".$year_month_query.")";
		}
		$result = DB::select($sql);
		$table = '';
		$data['div'] = Str::random(15);
		// $data['columns'] = [];

		foreach ($result as $key => $value) {
			if ($var === 2 || $var === '2'){
				$title = $value->subcounty;
			} else {
				$title = $value->county;
			} 
			$table .= '<tr>';
			$table .= '<td>'.($key+1).'</td>';
			$table .= '<td>'.$title.'</td>';
			$table .= '<td>'.number_format(round($value->sitessending)).'</td>';
			$table .= '<td>'.number_format($value->alltests).'</td>';
			if ($year > '2015' ) {
				if ($var != 2) {
					$table .= '<td>'.number_format($value->pmtctneed).'</td>';
				}
				
			} else {
				$table .= '<td>0</td>';
			}
			$table .= '<td>'.number_format($value->actualinfants).'</td>';
			$table .= '<td>'.number_format($value->positive+$value->negative).'</td>';
			$table .= '<td>'.number_format($value->positive).'</td>';
			$table .= '<td>'.number_format($value->repeatspos).'</td>';
			$table .= '<td>'.number_format($value->repeatsposPOS).'</td>';
			$table .= '<td>'.number_format($value->confirmdna).'</td>';
			$table .= '<td>'.number_format($value->confirmedPOS).'</td>';
			$table .= '<td>'.number_format($value->infantsless2w).'</td>';
			$table .= '<td>'.number_format($value->infantsless2wpos).'</td>';
			$table .= '<td>'.number_format($value->infantsless2m).'</td>';
			$table .= '<td>'.number_format($value->infantsless2mpos).'</td>';
			$table .= '<td>'.number_format($value->infantsabove2m).'</td>';
			$table .= '<td>'.number_format($value->infantsabove2mpos).'</td>';
			$table .= '<td>'.number_format($value->medage).'</td>';
			$table .= '<td>'.number_format($value->rejected).'</td>';

			$table .= '</tr>';
		}
		$data['outcomes'] = $table;
		return view('tables.datatable', $data);
	}

	public function county_sub_details($level, $originator='county')
	{
		extract($this->get_filters());

		if($level == 'subcounty'){
			$sql = "CALL `proc_get_eid_county_subcounties_details`('".$county."',".$year_month_query.")";
		}else if($level == 'partner'){
			$sql = "CALL `proc_get_eid_county_partners_details`('".$county."',{$year_month_query})";
		}else if($level == 'facility'){
			$type = 1;
			$sql = "CALL `proc_get_eid_sites_details`(".$year_month_query.",'".$type."','".$county."')";
			if($originator='subcounty'){
				$sql = "CALL `proc_get_eid_subcounty_sites_details`('".$subcounty."',".$year_month_query.")";
			}
		}
		$result = DB::select($sql);
		$table = '';
		$data['div'] = Str::random(15);
		$data['level'] = $level;

		foreach ($result as $key => $value) {
			if ($level == 'partner' && ($value['partner'] == NULL || $value['partner'] == 'NULL')) {
				$value['partner'] = 'No Partner';
			}
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			if(in_array($level, ['subcounty', 'partner'])){
				$table .= '<td>'.($value['subcounty'] ?? $value['partner']).'</td>';
			}else{
				$table .= '<td>'.$value['facility'].'</td>';
				$table .= '<td>'.$value['facilitycode'].'</td>';
				$table .= '<td>'.$value['subcounty'].'</td>';
			}
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
		$data['outcomes'] = $table;
		$data['div'] = Str::random(15);
		return view('tables.datatable', $data);
	}

	public function county_subcounty($var = 'outcomes')
	{
		extract($this->get_filters());
		$sql = "CALL `proc_get_eid_subcounty_outcomes`('".$county."',".$year_month_query.")";
		$result = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative']);
		$data['yAxis'] = 'Tests';

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['name'];
			$data["outcomes"][0]["data"][$key] = (int) $value['positive'];
			$data["outcomes"][1]["data"][$key] = (int) $value['negative'];
		}
		
		if ($var === 'positivity') {
			$data['stacking_percent'] = 1;
			$data['yAxis'] = 'Positivity';
		}
		
		return view('charts.bar_graph', $data);
	}

	public function countyDetails($var = 'sub_county', $var2 = 'facilities')
	{
		extract($this->get_filters());
		$table = '';
		$t_head = '';
		
		
		if ($var == 'county') {
			$type = 1;
			if($var2 == 'sub_county'){
				$sql = "CALL `proc_get_eid_county_subcounties_details`('".$county."',{$year_month_query})";
			}else if($var2 == 'partner'){
				$sql = "CALL `proc_get_eid_county_partners_details`('".$county."',{$year_month_query})";
			}else if($var2 == 'facilities'){
				$sql = "CALL `proc_get_eid_sites_details`({$year_month_query},'".$type."','".$county."')";
			}else{
				$sql = "CALL `proc_get_eid_countys_details`(".$year_month_query.")";
			}
		}
		else if ($var == 'sub_county') {
			$sql = "CALL `proc_get_eid_subcounty_sites_details`('".$subcounty."',{$year_month_query})";
		} 
		else if ($var == 'partner') {
			if($var2 == 'county'){
				$sql = "CALL `proc_get_eid_partner_county_details`('".$partner."',{$year_month_query})";
			}
			else{
				$sql = "CALL `proc_get_eid_partner_sites_details`('".$partner."',{$year_month_query})";
			}
		} 
		
		$result = DB::select($sql);
		
		foreach ($result as $key => $value) {
			$value = get_object_vars($value);
			$table .= '<tr>';
			$table .= '<td>'.($key+1).'</td>';

			if ((in_array($var, ['county', 'sub_county']) || $var2 == 'county') && $var2 != 'facilities') {
				$table .= '<td>'.($value['subcounty'] ?? $value['county'] ?? '').'</td>';
				$table .= '<td>'.number_format(round($value['facilities'] ?? $value['sitessending'] ?? 0)).'</td>';

			} elseif ($var2 == 'partner' || ($var == 'partner' && $var2 == 'county')) {
				$table .= '<td>'.($value['county'] ?? $value['partner'] ?? 'No Partner').'</td>';

			} elseif ($var == 'facilities' || $var2 == 'facilities') {
				$table .= '<td>'.($value['facility'] ?? $value['name'] ?? '').'</td>';
				$table .= '<td>'.($value['facilitycode'] ?? $value['MFLCode']).'</td>';
				$table .= '<td>'.$value['subcounty'].'</td>';
				if($var2 == 'facilities') $table .= '<td>'.$value['county'].'</td>';
			}
			
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
		}
		
		$t_head .= ' <tr class="colhead">
					 <th rowspan="2">#</th>';
		if ($var == 'facilities' || $var2 == 'facilities') {
			$t_head .= '<th rowspan="2">Facility Name</th>
						<th rowspan="2">MFL Code</th>
						<th rowspan="2">Sub-County</th>';
			if($var2 == 'facilities') $t_head .= '<th rowspan="2">County</th>';
		} elseif ($var2 == 'partner') {
			$t_head .= '<th rowspan="2">Partner</th>';
		} elseif ($var == 'county' || $var2 == 'county') {
			$t_head .= '<th rowspan="2">County</th>
						<th rowspan="2">Sites</th>';
		} else {
			$t_head .= '<th rowspan="2">Sub-County</th>
						<th rowspan="2">Sites</th>';
		}
		$t_head .= '<th rowspan="2">All Tests</th>
					<th rowspan="2">Actual Infants Tested</th>
					<th colspan="2">Initial PCR</th>
					<th colspan="2">2nd/3rd PCR</th>
					<th colspan="2">Confirmatory PCR</th>
					<th colspan="2">Infants &lt;2Weeks</th>
					<th colspan="2">Infants &lt;=2M</th>
					<th colspan="2">Infants &gt;=2M</th>
					<th rowspan="2">Median Age</th>
					<th rowspan="2">Rejected</th>
				</tr>
				<tr>
					<th>Tests</th>
					<th>Pos</th>
					<th>Tests</th>
					<th>Pos</th>
					<th>Tests</th>
					<th>Pos</th>
					<th>Tests</th>
					<th>Pos</th>
					<th>Tests</th>
					<th>Pos</th>
					<th>Tests</th>
					<th>Pos</th>
				</tr>';	 
		$data['outcomes'] = $table;
		$data['th'] = $t_head;
		$data['div'] = Str::random(15);

		return view('tables.datatable', $data);
	}

	public function test_analysis($type=1)
	{
		extract($this->get_filters());
		$id = 0;

		if($type == 0) $title = 'Countys';
		else if($type == 1) $title = 'Partners';
		else if($type == 2) $title = 'Sub-countys';
		else if($type == 3) $title = 'Facilitys';
		else if($type == 4){
			$title = 'Agency';
			if($agency_id){
				$id = $agency_id;
				$title = 'Partner';
			}
		}

		$sql = "CALL `proc_get_eid_tests_analysis`({$year_month_query},'".$type."','{$id}')";
		$result = DB::select($sql);

		$table = '';
		foreach ($result as $key => $value) {
			$tests = (int) ($value->firstdna+$value->confirmdna+$value->repeatspos);
			$table .= '<tr>';
			$table .= '<td>'.($key+1).'</td>';
			$table .= '<td>'.$value->name.'</td>';
			$table .= '<td>'.number_format($tests).'</td>';
			$table .= '<td>'.number_format($value->firstdna).'</td>';
			$table .= '<td>'.number_format($value->infantsless2m).'</td>';
			$table .= '<td><center>'.Lookup::get_percentage($value->infantsless2m, $value->firstdna, 1).'%</center></td>';
			$table .= '<td>'.number_format($value->repeatspos).'</td>';
			$table .= '<td><center>'.Lookup::get_percentage($value->repeatspos, $tests, 1).'%</center></td>';
			$table .= '<td>'.number_format($value->confirmdna).'</td>';
			$table .= '<td><center>'.Lookup::get_percentage($value->confirmdna, $tests, 1).'%</center></td>';
			$table .= '</tr>';
		}
		$data['th']	= '<tr class="colhead">
							<th>#</th>
							<th>'.$title.'</th>
							<th>Total Tests</th>
							<th>Initial PCR</th>
							<th>&lt;=2M</th>
							<th>&lt;=2M(% of Initial PCR)</th>
							<th>2nd/3rd PCR</th>
							<th>% 2nd/3rd PCR</th>
							<th>Confirmatory PCR</th>
							<th>% Confirmatory PCR</th>
						</tr>';
		$data['outcomes'] = $table;
		$data['div'] = Str::random(15);

		return view('tables.datatable', $data);
	}

	public function test_analysis_trends()
	{
		extract($this->get_filters());

		$type=4;

		$sql = "CALL `proc_get_eid_tests_analysis`({$year_month_query},'".$type."','".$agency_id."')";

		$result = DB::select($sql);

		$data = $this->bars(['&lt;= 2M Tests (of Initial PCR)', '&gt; 2M Tests (of Initial PCR)', '&lt;= 2M Tests (% of Initial PCR)'], 'column', ['#1BA39C', '#F2784B', '#913D88'], ['', '', ' %']);
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($result as $key => $value) {
			if (!((int) $value->infantsless2m == 0 && (int) $value->firstdna == 0)){
				$above2m = $value->firstdna - $value->infantsless2m;
				$data['categories'][$key] = $value->name;
				$data["outcomes"][0]["data"][$key]	= (int) $value->infantsless2m;
				$data["outcomes"][1]["data"][$key]	= (int) $above2m;
				$data["outcomes"][2]["data"][$key]	= Lookup::get_percentage($value->infantsless2m, $value->firstdna, 1);
			}
		}
		return view('charts.dual_axis', $data);

	}

	
}
