<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Facility;
use \App\Lookup;

class FilterController extends Controller
{
	public function filter_date(Request $request)
	{
		$default_year = session('filter_year');

		$year = $request->input('year');
		$month = $request->input('month', 0);

		if($month && !$year) $year = $default_year;

		$to_year = $request->input('to_year');
		$to_month = $request->input('to_month');
		$prev_year = ($year - 1);

		$range = ['filter_year' => $year, 'filter_month' => $month, 'to_year' => $to_year, 'to_month' => $to_month];

		session($range);

		if($to_year){
			if($year == $to_year) 
				$display_date = '(' . Lookup::resolve_month($month) . ' - ' . Lookup::resolve_month($to_month) . " {$year})";
			else{
				$display_date = "(" . Lookup::resolve_month($month) . ", {$year} - " . Lookup::resolve_month($to_month) . ", {$to_year})";
			}
		}
		else if($month){
			$display_date = '(' . $year . ', ' . Lookup::resolve_month($month) . ')';
		}
		else{
			$display_date = '(' . $year . ')';
		}

		return ['year' => $year, 'prev_year' => $prev_year, 'range' => $range, 'display_date' => $display_date];
	}


	public function filter_any(Request $request)
	{
		// if(!session('filter_groupby')) abort(400);
		$var = $request->input('session_var');
		$val = $request->input('value');

		if($val == null || (!is_array($val) && in_array($val, ['null', ''])) || (is_array($val) && in_array('null', $val)) ) $val = null;
		session([$var => $val]);

		return [$var => $val];
	}

    public function facility(Request $request)
    {
        $search = $request->input('search');
        $facilities = Facility::select('id', 'name', 'facilitycode')
            ->whereRaw("(name like '%" . $search . "%' OR  facilitycode like '" . $search . "%')")
            // ->whereNotIn('id', Lookup::get_unshowable())
            ->where('flag', 1)
            ->paginate(10);
        return $facilities;
    }
}
