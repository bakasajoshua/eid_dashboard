<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class AgeController extends Controller
{

	public function ages_outcomes()
	{
		extract($this->get_filters());

		if ($county) {
			$sql = "CALL `proc_get_eid_age_data`(1, '".$county."',{$year_month_query})";
		} else if ($subcounty) {
			$sql = "CALL `proc_get_eid_age_data`(2, '".$subcounty."',{$year_month_query})";
		} else {
			$sql = "CALL `proc_get_eid_age_data`(0, 0, {$year_month_query})";
		}

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', ['#E26A6A', '#257766', '#913D88'], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			
			$data['categories'][$key] = $value['name'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['positive'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['negative'];
			$data["outcomes"][2]["data"][$key]	=  Lookup::get_percentage($value['positive'], ($value['positive']+$value['negative']), 1);
		}
		return view('charts.dual_axis', $data);
	}

	public function testing_trends()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_age_breakdown_trends`('".$age."', '".$from."', '".$to."')";

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', ['#E26A6A', '#257766', '#913D88'], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			
			$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
			$data["outcomes"][2]["data"][$key]	=  Lookup::get_percentage($value['pos'], ($value['pos']+$value['neg']), 1);
		}
		return view('charts.dual_axis', $data);
	}


	public function get_counties_agebreakdown()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_age_data_listing`(1, '{$age}', {$year_month_query})";

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', ['#E26A6A', '#257766', '#913D88'], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = "";
		
		$data['categories'][0] = 'No Data';

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			
			$data['categories'][$key] = $value['name'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
			$data["outcomes"][2]["data"][$key]	=  Lookup::get_percentage($value['pos'], ($value['pos']+$value['neg']), 1);
		}
		return view('charts.dual_axis', $data);
	}

}
