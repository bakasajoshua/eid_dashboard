<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
	public function get_filters()
	{
		$year = session('filter_year', date('Y'));
		$month = session('filter_month', 0);
		$to_year = session('filter_to_year', 0);
		$to_month = session('filter_to_month', 0);
		// $type = session('filter_type', 1);

		$from = $year -1;
		$to = $year;

		$agency_id = session('funding_agency_filter');
		$partner = session('partner_filter');
		$county = session('county_filter');
		$subcounty = session('sub_county_filter');
		$site = session('site_filter');
		$lab = session('lab_filter');
		$age = session('age_filter');
		$regimen = session('regimen_filter');

		if($partner === '0') $partner = 0;

		$year_month_query = "'".$year."','".$month."','".$to_year."','".$to_month."'";

		return compact('year_month_query', 'year', 'month', 'to_year', 'to_month', 'from', 'to', 'agency_id', 'partner', 'county', 'subcounty', 'site', 'lab', 'age', 'regimen');
	}

	public static function bars($categories=[], $type='column', $colours=[], $suffixes=[])
	{
		$data['div'] = Str::random(15);
		foreach ($categories as $key => $value) {
			$data['outcomes'][$key]['name'] = $value;
			$data['outcomes'][$key]['type'] = $type;
			$data['outcomes'][$key]['tooltip'] = ["valueSuffix" => ($suffixes[$key] ?? ' ')];
			if(isset($colours[$key]) && $colours[$key]) $data['outcomes'][$key]['color'] = $colours[$key];
		}
		return $data;
	}

	public static function columns(&$data, $start, $finish, $type='column')
	{
		for ($i=$start; $i <= $finish; $i++) { 
			$data['outcomes'][$i]['type'] = $type;
		}
	}
	public static function yAxis(&$data, $start, $finish, $axis=1)
	{
		for ($i=$start; $i <= $finish; $i++) { 
			$data['outcomes'][$i]['yAxis'] = $axis;
		}
	}		

	public function get_months()
	{
		return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	}
	
	public function resolve_month($month)
	{
		$months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		return $months[$month] ?? '';
	}
	
	
}
