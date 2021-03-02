<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class FacilityController extends Controller
{

	public function unsupported_sites()
	{
		$sql = "CALL `proc_get_eid_unsupported_facilities`()";
		$result = DB::select($sql);

		$table = '';
		
		foreach ($result as $key => $value) {
			$value = get_object_vars($value);
			$table .= "<tr>
				<td>" . ($key+1) . "</td>
				<td>" . $value['facilitycode'] . "</td>
				<td>" . $value['DHIScode'] . "</td>
				<td>" . $value['name'] . "</td>
				<td>" . $value['county'] . "</td>
				<td>" . $value['subcounty'] . "</td>
			</tr>";

		}
		$data['outcomes'] = $table;
		$data['th'] = "
			<tr class=\"colhead\">
				<th> # </th>
				<th> MFL Code </th>
				<th> DHIS Code </th>
				<th> Name </th>
				<th> County </th>
				<th> Subcounty </th>
			</tr>
		";
		$data['div'] = Str::random(15);

		return view('tables.datatable', $data);
	}

	public function get_trends()
	{
		extract($this->get_filters());
		
		$sql = "CALL `proc_get_eid_sites_trends`('".$site."', '".$year."')";

		$result = DB::select($sql);

		$data = $this->bars(['Tests', 'Initial PCR', 'Rejected', 'Positives', 'Negatives'], 'spline');
		$data['categories'] = Lookup::$months;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			$month = (int) $value['month'];
			$month--;

			$data['outcomes'][0]['data'][$month] = (int) $value['tests'];
			$data['outcomes'][1]['data'][$month] = (int) $value['initial_pcr'];
			$data['outcomes'][2]['data'][$month] = (int) $value['rejected'];
			$data['outcomes'][3]['data'][$month] = (int) $value['pos'];
			$data['outcomes'][4]['data'][$month] = (int) $value['neg'];
		}
		
		$data['chart_title'] = "Test Trends (" . $year . ")";
		$data['yAxis'] = "Number of Tests";

		return view('charts.line_graph', $data);
	}


	public function get_positivity()
	{
		extract($this->get_filters());
		
		$sql = "CALL `proc_get_eid_sites_trends`('".$site."','".$year."')";

		$result = DB::select($sql);

		$data = $this->bars(['Positives', 'Positivity (%)'], 'spline');
		$data['outcomes'][0]['yAxis'] = 1;
		$data['categories'] = Lookup::$months;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			$month = (int) $value['month'];
			$month--;

			$data['outcomes'][0]['data'][$month] = (int) $value['pos'];
			$data['outcomes'][1]['data'][$month] = Lookup::get_percentage($value['pos'], ($value['pos'] + $value['neg']), 1);
		}

		$data['chart_title'] = "Positivity (" . $year . ")";
		$data['yAxis'] = "Positive Tests";
		return view('charts.dual_axis', $data);
	}
}
