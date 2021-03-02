<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class PocController extends Controller
{

	public function testing_trends()
	{
		extract($this->get_filters());
		if(!$county) $county=0;

		$sql = "CALL `proc_get_eid_poc_trends`('".$county."',{$year_month_query})";
		$result = DB::select($sql);

		$data = $this->bars(['Confirmatory PCR', '2nd/3rd PCR', 'Initial PCR', '&lt; 2m % Contribution'], 'column', ['#52B3D9', '#E26A6A', '#257766', '#913D88'], ['', '', '', ' %', ' %']);

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);
			
			$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['confirmdna'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['repeatspos'];
			$data["outcomes"][2]["data"][$key]	= (int) $value['firstdna'];
			$data["outcomes"][3]["data"][$key]	= Lookup::get_percentage($value['positive'], ($value['positive']+$value['negative']), 1);
			$data["outcomes"][4]["data"][$key]	= Lookup::get_percentage($value['infantsless2m'], $value['tests'], 1);
			
		}
		return view('charts.dual_axis', $data);
	}



	public function eid_outcomes()
	{
		extract($this->get_filters());
		if(!$county) $county=0;

		$sql = "CALL `proc_get_eid_poc_summary_outcomes`('".$county."',{$year_month_query})";
		$rows = DB::select($sql);
		$value = $rows[0];
		$value = get_object_vars($value);

		$data['div'] = Str::random(15);
		$data['paragraph'] = '';
		$data['eid_outcomes']['name'] = 'Tests';
		$data['eid_outcomes']['colorByPoint'] = true;

		$count = 0;

		$data['eid_outcomes']['data'][0]['name'] = 'No Data';
		$data['eid_outcomes']['data'][0]['y'] = $count;

		$data['paragraph'] .= '<table class=\'table\'>
			<tr>
				<td>Total EID Tests</td>
				<td>'.number_format((int) ($value['firstdna']+$value['confirmdna']+$value['repeatspos'])).'</td>
				<td>Positive (+)</td>
				<td>'.number_format((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])).'('.round((((int) ($value['confirmpos']+$value['repeatsposPOS']+$value['pos'])/(int) ($value['firstdna']+$value['confirmdna']+$value['repeatspos']))*100),1).'%)</td>
			</tr>
			<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Initial PCR:</td>
	    		<td>'.number_format((int) $value['firstdna']).'</td>
	    		<td>Positive (+):</td>
	    		<td>'.number_format((int) $value['pos']).'('.Lookup::get_percentage($value['pos'], $value['firstdna'], 1).'%)</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2nd/3rd PCR:</td>
	    		<td>'.number_format((int) $value['repeatspos']).'</td>
	    		<td>Positive (+):</td>
	    		<td>'.number_format((int) $value['repeatsposPOS']).'('.Lookup::get_percentage($value['repeatspos'], $value['firstdna'], 1).'%)</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Confirmatory PCR:</td>
	    		<td>'.number_format((int) $value['confirmdna']).'</td>
	    		<td>Positive (+):</td>
	    		<td>'.number_format((int) $value['confirmpos']).'('.Lookup::get_percentage($value['confirmpos'], $value['confirmdna'], 1).'%)</td>
	    	</tr>
			<tr style="height:14px;background-color:#ABB7B7;">
	    		<td></td>
	    		<td></td>
	    		<td></td>
	    		<td></td>
	    	</tr>

	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Infants &lt;= 2M:</td>
	    		<td>'.number_format((int) $value['infantsless2m']).'</td>
	    		<td>Infants &lt;= 2M Positive:</td>
	    		<td>'.number_format((int) $value['infantless2mpos']).'('.Lookup::get_percentage($value['infantless2mpos'], $value['infantsless2m'], 1).'%)</td>
	    	</tr>

	    	<tr>
	    		<td>Above 2years Tested:</td>
	    		<td>'.number_format((int) $value['adults']).'</td>
	    		<td>Positive (+):</td>
	    		<td>'.number_format((int) $value['adultsPOS']).'('.Lookup::get_percentage($value['adultsPOS'], $value['adults'], 1).'%)</td>
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
	    		<td>'.Lookup::get_percentage($value['rejected'], $value['alltests'], 1).'%</td>
	    	</tr>
	    </table>';


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


	public function entrypoints()
	{
		extract($this->get_filters());
		if(!$county) $county=0;

		$sql = "CALL `proc_get_eid_county_poc_entry_points`('".$county."',{$year_month_query})";

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative']);

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] 		= $value['name'];

			$data["outcomes"][0]["data"][$key]	=  (int) $value['positive'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['negative'];
			
		}
		return view('charts.line_graph', $data);
	}	


	public function ages()
	{
		extract($this->get_filters());
		if(!$county) $county=0;

		$sql = "CALL `proc_get_eid_county_poc_age_range`(0, '".$county."',{$year_month_query})";
		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative']);

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] 		= $value['agename'];

			$data["outcomes"][0]["data"][$key]	=  (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['neg'];
			
		}
		return view('charts.line_graph', $data);
	}		


	public function county_outcomes()
	{
		extract($this->get_filters());
		if(!$county) $county=0;

		$sql = "CALL `proc_get_eid_county_poc_outcomes`({$year_month_query})";
		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'] );
		
		$data['outcomes'][2]['color'] = '#913D88';
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] 		= $value['countyname'];

			$data["outcomes"][0]["data"][$key]	=  (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['neg'];
			$data["outcomes"][2]["data"][$key]	= Lookup::get_percentage($value['pos'], ($value['pos']+$value['neg']), 1);
			
		}
		return view('charts.dual_axis', $data);
	}	



}
