<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class HeiController extends Controller
{
    public function validation($type=0)
	{
        extract($this->get_filters());

		$id = 0;
		$sql = "CALL `proc_get_eid_hei_validation`(".$year_month_query.",'".$type."','".$id."')";
		// echo "<pre>";print_r($sql);die();
		$result = DB::select($sql);
		// echo "<pre>";print_r($result);die();
		$count = 1;
		$table = '';
		foreach ($result as $key => $value) {
			$followup_hei = (int) $value->Confirmed_Positive+(int) $value->Repeat_Test+(int) $value->Viral_Load+(int) $value->Adult+(int) $value->Unknown_Facility;
			$followup_percentage = Lookup::get_percentage($followup_hei, $value->positives, 1);
			$confirmed_percentage = Lookup::get_percentage($value->Confirmed_Positive, $followup_hei, 1);
			$table .= '<tr>';
			$table .= '<td>'.$count.'</td>';
			$table .= '<td>'.$value->name.'</td>';
			$table .= '<td>'.number_format($value->positives).'</td>';
			$table .= '<td>'.number_format($followup_hei).'</td>';
			$table .= '<td>'.$followup_percentage.'%</td>';
			$table .= '<td>'.number_format($value->Confirmed_Positive).'</td>';
			if ($confirmed_percentage > 69) {
				$table .= '<td><span class="alert alert-success" style="color:black;">'.$confirmed_percentage.'%</span></td>';
			} else if ($confirmed_percentage < 70 && $confirmed_percentage > 39) {
				$table .= '<td><span class="alert alert-warning" style="color:black;">'.$confirmed_percentage.'%</span></td>';
			} else {
				$table .= '<td><span class="alert alert-danger" style="color:black;">'.$confirmed_percentage.'%</span></td>';
			}
			$table .= '<td>'.number_format($value->enrolled).'</td>';
			$table .= '<td>'.number_format($value->ltfu).'</td>';
			$table .= '<td>'.number_format($value->adult).'</td>';
			$table .= '<td>'.number_format($value->transout).'</td>';
			$table .= '<td>'.number_format($value->dead).'</td>';
			$table .= '<td>'.number_format($value->other).'</td>';
			$table .= '</tr>';
			$count++;
		}

		if ($type == 0) $title = 'County';
		if ($type == 1) $title = 'Partner';
		if ($type == 2) $title = 'Sub-county';
		if ($type == 3) $title = 'Facility';
		$data['th'] = '<tr class="colhead">
							<th>#</th>
							<th>'.$title.'</th>
							<th>Actual Infants Tested Positive</th>
							<th>Actual Infants Validated at Site</th>
							<th>% Infants Validated at Site</th>
							<th>Actual Infants Confirmed Positive</th>
							<th>% Infants Confirmed Positive</th>
							<th>Enrolled</th>
							<th>Lost to follow up</th>
							<th>Adults</th>
							<th>Transfer Out</th>
							<th>Dead</th>
							<th>Others</th>
						</tr>';
		$data['outcomes'] = $table;
		$data['div'] = Str::random(15);
		// echo "<pre>";print_r($data);die();
		return view('tables.datatable', $data);
	}
}
