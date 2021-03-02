<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class RegimenController extends Controller
{
	public function regimen_outcomes()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_iprophylaxis`({$year_month_query})";

		$rows = DB::select($sql);
		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', ['#E26A6A', '#257766', '#913D88'], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = "";

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			
			$data['categories'][$key] = $value['name'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['neg'];
			$data["outcomes"][2]["data"][$key]	=  Lookup::get_percentage($value['pos'], ($value['pos']+$value['neg']), 1);
		}
		return view('charts.dual_axis', $data);
	}


	public function testing_trends()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_iproph_testing_trends`('".$regimen."', '".$from."', '".$to."')";

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


	public function get_counties_breakdown()
	{
		extract($this->get_filters());


		$sql = "CALL `proc_get_eid_iproph_breakdown`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."',1,0,0)";

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
