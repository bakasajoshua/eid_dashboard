<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class SummaryController extends Controller
{

	public function turnaroundtime()
	{
		extract($this->get_filters());

		$type = 0;
		$id = 0;
		if ($county) {
			$type = 1; 
			$id = $county;
		}

		$sql = "CALL `proc_get_eid_national_tat`(".$year_month_query.",'".$type."','".$id."')";

		$rows = DB::select($sql);

		$count =  $tat1 = $tat2 = $tat3 = $tat4 = 0;
		$tat = [];

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			if (($value['tat1']!=0) || ($value['tat2']!=0) || ($value['tat3']!=0) || ($value['tat4']!=0)) {
				$count++;

				$tat1 += $value['tat1'];
				$tat2 += $value['tat2'];
				$tat3 += $value['tat3'];
				$tat4 += $value['tat4'];
			}
		}
		if(!$count) $count = 1;

		$tat1 = round($tat1 / $count);
		$tat2 = round($tat2 / $count) + $tat1;
		$tat3 = round($tat3 / $count) + $tat2;
		$tat4 = round($tat4 / $count);

		$div = Str::random(15);
		// return null;

		return view('charts.tat', compact('tat1', 'tat2', 'tat3', 'tat4', 'div'));
	}

	public function test_trends($type=1)
	{
		extract($this->get_filters());

		if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_testing_trends`('".$partner."','".$from."','".$to."')";
		else if($county) $sql = "CALL `proc_get_eid_county_testing_trends`('".$county."','".$from."','".$to."')";
		else if($agency_id) $sql = "CALL `proc_get_eid_fundingagency_testing_trends`('".$agency_id."','".$from."','".$to."')";
		else{
			$sql = "CALL `proc_get_eid_national_testing_trends`('".$from."','".$to."')";
		}

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', [], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		if($type == 1){
			$pos_column = 'pos';
			$neg_column = 'neg';
		}else if($type == 2){
			$pos_column = 'rpos';
			$neg_column = 'rneg';
		}else{
			$pos_column = 'allpos';
			$neg_column = 'allneg';
		}

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = Lookup::resolve_month($value['month']).'-'.$value['year'];
			$data["outcomes"][0]["data"][$key]	= (int) $value[$pos_column];
			$data["outcomes"][1]["data"][$key]	= (int) $value[$neg_column];
			$total = $value[$pos_column] + $value[$neg_column];
			$data["outcomes"][2]["data"][$key]	= Lookup::get_percentage($value[$pos_column], $total, 1);	

			// $data["outcomes"][2]["data"][$key]	= round(@( ((int) $value[$pos_column]*100) /((int) $value[$neg_column]+(int) $value[$pos_column])),1);
		}
		return view('charts.dual_axis', $data);
	}

	public function eid_outcomes()
	{
		extract($this->get_filters());

		if ($partner || $partner === 0) {
			$sql = "CALL `proc_get_eid_partner_eid_outcomes`('".$partner."',".$year_month_query.")";
		} else if($county) {
			$sql = "CALL `proc_get_eid_county_eid_outcomes`('".$county."',".$year_month_query.")";
		} else if($subcounty) {
			$sql = "CALL `proc_get_eid_subcounty_eid`('".$subcounty."',".$year_month_query.")";
		}else{
			$sql = "CALL `proc_get_eid_national_eid_outcomes`(".$year_month_query.")";			
		}

		$rows = DB::select($sql);
		$value = $rows[0];
		$value = get_object_vars($value);
		$pcr2 = $value['repeatspos'] - $value['pcr3'];
		$pcr2pos = $value['repeatsposPOS'] - $value['pcr3pos'];
		$data['div'] = Str::random(15);
		$num = ($value['confirmpos']+$value['repeatsposPOS']+$value['pos']);
		$den = ($value['firstdna']+$value['confirmdna']+$value['repeatspos']);
		$data['paragraph'] = '<table class=\'table\'>
				<tr>
					<td>Total EID Tests</td>
					<td>'.number_format((int) $den).'</td>
					<td>Positive (+)</td>
					<td>'.number_format((int) $num).'('. Lookup::get_percentage($num, $den, 1) .'%)</td>
				</tr>
				<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initial PCR:</td>
		    		<td>'.number_format((int) $value['firstdna']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['pos']).'('. Lookup::get_percentage($value['pos'], $value['firstdna'], 1) .'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2nd PCR:</td>
		    		<td>'.number_format((int) $pcr2).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $pcr2pos).'('. Lookup::get_percentage($pcr2pos, $pcr2, 1) .'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3rd PCR:</td>
		    		<td>'.number_format((int) $value['pcr3']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['pcr3pos']).'('. Lookup::get_percentage($value['pcr3pos'], $value['pcr3'], 1) .'%)</td>
		    	</tr>
		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Confirmatory PCR:</td>
		    		<td>'.number_format((int) $value['confirmdna']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['confirmpos']).'('. Lookup::get_percentage($value['confirmpos'], $value['confirmdna'], 1) .'%)</td>
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
		    		<td>'.number_format((int) $value['actualinfantspos']).'('. Lookup::get_percentage($value['actualinfantspos'], $value['actualinfants'], 1) .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infants &lt;= 2M:</td>
		    		<td>'.number_format((int) $value['infantsless2m']).'</td>
		    		<td>Infants &lt;= 2M Positive:</td>
		    		<td>'.number_format((int) $value['infantless2mpos']).'('. Lookup::get_percentage($value['infantless2mpos'], $value['infantsless2m'], 1) .'%)</td>
		    	</tr>

		    	<tr>
		    		<td>Above 2years Tested:</td>
		    		<td>'.number_format((int) $value['adults']).'</td>
		    		<td>Positive (+):</td>
		    		<td>'.number_format((int) $value['adultsPOS']).'('. Lookup::get_percentage($value['adultsPOS'], $value['adults'], 1) .'%)</td>
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
		    		<td>'. Lookup::get_percentage($value['rejected'], $value['alltests'], 1) .'%</td>
		    	</tr>

		    	<tr>
		    		<td>Median Age of Testing at Initial PCR:</td>
		    		<td>'.round($value['medage']).'</td>
		    		<td>Average Sites sending:</td>
		    		<td>'.number_format((int) $value['sitessending']).'</td>
		    	</tr>
		</table>
		';

		$data['outcomes']['name'] = 'Tests';
		$data['outcomes']['colorByPoint'] = true;

		$data['outcomes']['data'][0]['name'] = 'Positive';
		$data['outcomes']['data'][1]['name'] = 'Negative';

		$data['outcomes']['data'][0]['y'] = (int) $value['pos'];
		$data['outcomes']['data'][1]['y'] = (int) $value['neg'];

		$data['outcomes']['data'][0]['sliced'] = true;
		$data['outcomes']['data'][0]['selected'] = true;
		$data['outcomes']['data'][0]['color'] = '#F2784B';
		$data['outcomes']['data'][1]['color'] = '#1BA39C';

		return view('charts.pie_chart', $data);
	}


	public function hei_validation()
	{
		extract($this->get_filters());

		if ($partner || $partner === 0) {
			$sql = "CALL `proc_get_eid_partner_hei_validation`('".$partner."',".$year_month_query.")";
		} else if($county) {
			$sql = "CALL `proc_get_eid_county_hei_validation`('".$county."',".$year_month_query.")";
		} else if($subcounty) {
			$sql = "CALL `proc_get_eid_subcounty_hei_validation`('".$subcounty."',".$year_month_query.")";
		}else{
			$sql = "CALL `proc_get_eid_national_hei_validation`(".$year_month_query.")";			
		}

		if(!$month){
			if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_yearly_hei_validation`('".$partner."','".$year."')";
			else if($county) $sql = "CALL `proc_get_eid_county_yearly_hei_validation`('".$county."','".$year."')";
			else if($subcounty) $sql = "CALL `proc_get_eid_subcounty_yearly_hei_validation`('".$subcounty."','".$year."')";
			else{
				$sql = "CALL `proc_get_eid_national_yearly_hei_validation`('".$year."')";
			}			
		}

		$rows = DB::select($sql);
		$value = $rows[0];	
		$value = get_object_vars($value);	

		$followup_hei = (int) $value['Confirmed Positive']+(int) $value['Repeat Test']+(int) $value['Viral Load']+(int) $value['Adult']+(int) $value['Unknown Facility'];

		$data['div'] = Str::random(15);
		$data['paragraph'] = '<table>
			<tr>
				<td><center>Actual Infants Tested Positive:</center></td>
				<td>'.number_format((int) $value['positives']).'</td>
				<td></td>
				<td></td>
	        </tr>
	        <tr>
	        	<td><center>&nbsp;&nbsp;Actual Infants Validated at Site:</center></td>
	            <td>'.number_format((int) $followup_hei).'<b>('. Lookup::get_percentage($followup_hei, $value['positives'], 1) .'%)</b></td>
	            <td></td>
	            <td></td>
	        </tr>
	       	<tr>
	            <td><center>&nbsp;&nbsp;&nbsp;Actual Confirmed Positives at Site:</center></td>
	            <td>'.number_format((int) $value['Confirmed Positive']).'<b>('. Lookup::get_percentage($value['Confirmed Positive'], $value['true_tests'], 1) .'%)</b></td>
	            <td></td>
	            <td></td>
	        </tr>
	        </table>
        ';
        $data['colours'] = [];
        $data['outcomes']['name'] = 'Validation';

		$data['outcomes']['data'][0]['name'] = 'Confirmed Positive';
		$data['outcomes']['data'][1]['name'] = '2nd/3rd Test';
		$data['outcomes']['data'][2]['name'] = 'Viral Load';
		$data['outcomes']['data'][3]['name'] = 'Adult';
		$data['outcomes']['data'][4]['name'] = 'Unknown Facility';

		$data['outcomes']['data'][0]['y'] = (int) $value['Confirmed Positive'];
		$data['outcomes']['data'][1]['y'] = (int) $value['Repeat Test'];
		$data['outcomes']['data'][2]['y'] = (int) $value['Viral Load'];
		$data['outcomes']['data'][3]['y'] = (int) $value['Adult'];
		$data['outcomes']['data'][4]['y'] = (int) $value['Unknown Facility'];

		$data['outcomes']['data'][0]['sliced'] = true;
		$data['outcomes']['data'][0]['selected'] = true;

		return view('charts.pie_chart', $data);
	}

	public function hei_follow()
	{
		extract($this->get_filters());

		if ($partner || $partner === 0) {
			$sql = "CALL `proc_get_eid_partner_hei`('".$partner."',".$year_month_query.")";
		} else if($county) {
			$sql = "CALL `proc_get_eid_county_hei`('".$county."',".$year_month_query.")";
		} else if($subcounty) {
			$sql = "CALL `proc_get_eid_subcounty_hei_follow_up`('".$subcounty."','".$year."', '".$month."','".$to_year."', '".$to_month."')";
		}else{
			$sql = "CALL `proc_get_eid_national_hei`(".$year_month_query.")";			
		}

		if(!$month){
			if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_yearly_hei`('".$partner."','".$year."')";
			else if($county) $sql = "CALL `proc_get_eid_county_yearly_hei`('".$county."','".$year."')";
			else if($subcounty) $sql = "CALL `proc_get_eid_subcounty_yearly_hei_follow_up`('".$subcounty."','".$year."')";
			else{
				$sql = "CALL `proc_get_eid_national_yearly_hei`('".$year."')";
			}			
		}

		$rows = DB::select($sql);
		$value = $rows[0];	
		$value = get_object_vars($value);
		$data['div'] = Str::random(15);	
        $data['colours'] = [];


		$total = (int) ($value['enrolled']+$value['dead']+$value['ltfu']+$value['adult']+$value['transout']+$value['other']);
		$data['paragraph'] = '<li>Initiated On Treatment: '.(int) $value['enrolled'].' <strong>('. Lookup::get_percentage($value['enrolled'], $total, 1) .'%)</strong></li>';
		$data['paragraph'] .= '<li>Lost to Follow Up: '.$value['ltfu'].' <strong>('. Lookup::get_percentage($value['ltfu'], $total, 1) .'%)</strong></li>';
		$data['paragraph'] .= '<li>Dead: '.(int) $value['dead'].' <strong>('. Lookup::get_percentage($value['dead'], $total, 1) .'%)</strong></li>';
		$data['paragraph'] .= '<li>Transferred Out: '.$value['transout'].' <strong>('. Lookup::get_percentage($value['transout'], $total, 1) .'%)</strong></li>';
		$data['paragraph'] .= '<li>Other Reasons(e.g denial): '.$value['other'].' <strong>('. Lookup::get_percentage($value['other'], $total, 1) .'%)</strong></li>';

        $data['outcomes']['name'] = 'Percentage';
		$data['outcomes']['data'][0]['name'] = 'Initiated on Treatment';
		$data['outcomes']['data'][1]['name'] = 'Dead';
		$data['outcomes']['data'][2]['name'] = 'Lost to Follow up';
		$data['outcomes']['data'][3]['name'] = 'Transferred out';
		$data['outcomes']['data'][4]['name'] = 'Other Reasons';

		$data['outcomes']['data'][0]['y'] = (int) $value['enrolled'];
		$data['outcomes']['data'][1]['y'] = (int) $value['dead'];
		$data['outcomes']['data'][2]['y'] = (int) $value['ltfu'];
		$data['outcomes']['data'][3]['y'] = (int) $value['transout'];
		$data['outcomes']['data'][4]['y'] = (int) $value['other'];

		$data['outcomes']['data'][0]['sliced'] = true;
		$data['outcomes']['data'][0]['selected'] = true;
		
		return view('charts.pie_chart', $data);
	}


	public function age()
	{
		extract($this->get_filters());

		if ($partner || $partner === 0) {
			$sql = "CALL `proc_get_eid_partner_age`('".$partner."',".$year_month_query.")";
		} else if($county) {
			$sql = "CALL `proc_get_eid_county_age`('".$county."',".$year_month_query.")";
		} else if($subcounty) {
			$sql = "CALL `proc_get_eid_subcounty_age`('".$subcounty."',".$year_month_query.")";
		}else{
			$sql = "CALL `proc_get_eid_national_age`(".$year_month_query.")";			
		}

		$rows = DB::select($sql);
		$value = $rows[0];
		$value = get_object_vars($value);

		$data = $this->bars(['Positive', 'Negative']);

		$data['outcomes'][0]['drilldown']['color'] = '#913D88';
		$data['outcomes'][1]['drilldown']['color'] = '#96281B';


		$data['categories'][0] = 'No Data';
		$data['categories'][1] = '2M';
		$data['categories'][2] = '3-8M';
		$data['categories'][3] = '9-12M';
		$data['categories'][4] = 'Above 12M';
		// $data['categories'][4] = 'above18M';

		$data['outcomes'][0]["data"][0]	=  (int) $value['nodatapos'];
		$data['outcomes'][1]["data"][0]	=  (int) $value['nodataneg'];
		$data['outcomes'][0]["data"][1]	=  (int) $value['sixweekspos'];
		$data['outcomes'][1]["data"][1]	=  (int) $value['sixweeksneg'];
		$data['outcomes'][0]["data"][2]	=  (int) $value['sevento3mpos'];
		$data['outcomes'][1]["data"][2]	=  (int) $value['sevento3mneg'];
		$data['outcomes'][0]["data"][3]	=  (int) $value['threemto9mpos'];
		$data['outcomes'][1]["data"][3]	=  (int) $value['threemto9mneg'];
		$data['outcomes'][0]["data"][4]	=  (int) $value['ninemto18mpos'];
		$data['outcomes'][1]["data"][4]	=  (int) $value['ninemto18mneg'];

		return view('charts.line_graph', $data);
	}

	public function age2($stacking_percent=false)
	{
		extract($this->get_filters());

		if ($partner || $partner === 0) {
			$sql = "CALL `proc_get_eid_partner_age_range`(0,'".$partner."',".$year_month_query.")";
		} else if($county) {
			$sql = "CALL `proc_get_eid_county_age_range`(0,'".$county."',".$year_month_query.")";
		} else if($subcounty) {
			$sql = "CALL `proc_get_eid_subcounty_age_range`(0, '".$subcounty."',".$year_month_query.")";
		}else{
			$sql = "CALL `proc_get_eid_national_age_range`(0,".$year_month_query.")";			
		}

		$rows = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative']);
		if($stacking_percent) $data['stacking_percent'] = 1;

		$data['outcomes'][0]['drilldown']['color'] = '#913D88';
		$data['outcomes'][1]['drilldown']['color'] = '#96281B';

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['age_range'];

			$data["outcomes"][0]["data"][$key] = (int) $value['pos'];
			$data["outcomes"][1]["data"][$key] = (int) $value['neg'];
		}

		return view('charts.line_graph', $data);
	}

	//  Can be Used for entry points, mprophylaxis or iprophylaxis 
	public function dynamic_detailed($field='entry_points', $stacking_percent=false)
	{
		extract($this->get_filters());

		if($field == 'entry_points'){
			if ($partner || $partner === 0) {
				$sql = "CALL `proc_get_eid_partner_entry_points`('".$partner."',".$year_month_query.")";
			} else if($county) {
				$sql = "CALL `proc_get_eid_county_entry_points`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_national_entry_points`(".$year_month_query.")";			
			}
		}
		else if($field == 'mprophylaxis'){
			if ($partner || $partner === 0) {
				$sql = "CALL `proc_get_eid_partner_mprophylaxis`('".$partner."',".$year_month_query.")";
			} else if($county) {
				$sql = "CALL `proc_get_eid_county_mprophylaxis`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_national_mprophylaxis`(".$year_month_query.")";			
			}			
		}
		else if($field == 'iprophylaxis'){
			if ($partner || $partner === 0) {
				$sql = "CALL `proc_get_eid_partner_iprophylaxis`('".$partner."',".$year_month_query.")";
			} else if($county) {
				$sql = "CALL `proc_get_eid_county_iprophylaxis`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_national_iprophylaxis`(".$year_month_query.")";			
			}			
		}

		$rows = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative']);
		if($stacking_percent) $data['stacking_percent'] = 1;

		if($field == 'iprophylaxis' && !$stacking_percent) $data['inverted'] = true;

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] 		= $value['name'];

			$data['outcomes'][0]["data"][$key]	=  (int) $value['positive'];
			$data['outcomes'][1]["data"][$key]	=  (int) $value['negative'];			
		}

		return view('charts.line_graph', $data);
	}

	public function dynamic_outcomes($level='partner', $level2='facility', $stacking_percent=false)
	{
		extract($this->get_filters());

		if($level == 'partner'){
			if(($partner || $partner === 0)){
				if($level2 == 'facility'){
					$sql = "CALL `proc_get_eid_partner_sites_outcomes`('".$partner."',".$year_month_query.")";
				}
				else if($level2 == 'county'){
					$sql = "CALL `proc_get_eid_partner_county_details`('".$partner."',".$year_month_query.")";
				}
			}
			else{
				$sql = "CALL `proc_get_eid_partner_outcomes`(".$year_month_query.")";
			}
		}
		else if($level == 'county'){
			if($county){
				$sql = "CALL `proc_get_eid_county_sites_outcomes`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_county_outcomes`(".$year_month_query.")";
			}
		}
		else if($level == 'subcounty'){
			if($subcounty){
				$sql = "CALL `proc_get_eid_top_subcounty_outcomes`(".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_top_subcounty_outcomes`(".$year_month_query.")";
			}
		}
		else if($level == 'facility'){
			$sql = "CALL `proc_get_eid_all_sites_outcomes`(".$year_month_query.")";
		}
		else if($level == 'agency'){
			if($agency_id) $type=1;
			else{
				$type = 0;
				$agency_id = 0;
			}

			$sql = "CALL `proc_get_eid_agencies_outcomes`({$year_month_query},'".$type."','".$agency_id."')";
		}

		$rows = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', [], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		if($stacking_percent){
			$data = $this->bars(['Positive', 'Negative']);
			$data['stacking_percent'] = true;
		}

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['agency'] ?? $value['county'] ?? $value['name'] ?? '';

			$total = $value['positive']+$value['negative'];

			$data['outcomes'][0]["data"][$key]	=  (int) $value['positive'];
			$data['outcomes'][1]["data"][$key]	=  (int) $value['negative'];
			if(!$stacking_percent) $data["outcomes"][2]["data"][$key]	= Lookup::get_percentage($value['positive'], $total, 1);			
		}
		if($stacking_percent) return view('charts.line_graph', $data);
		return view('charts.dual_axis', $data);
	}


}
