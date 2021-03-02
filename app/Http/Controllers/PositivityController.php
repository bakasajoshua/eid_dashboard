<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Str;

class PositivityController extends Controller
{

	public function notification_bar()
	{
		extract($this->get_filters());

		if (!$month) {
			if (!$county) {
				$sql = "CALL `proc_get_national_positivity_yearly_notification`('".$year."')";
			} else {
				$sql = "CALL `proc_get_county_positivity_yearly_notification`('".$county."','".$year."')";
			}
		} else {
			if (!$county) {
				$sql = "CALL `proc_get_national_positivity_notification`(".$year_month_query.")";
			} else {
				$sql = "CALL `proc_get_county_positivity_notification`('".$county."',".$year_month_query.")";
				// $data['county'] = $county;
			}
		}
		$result = DB::select($sql);
		
		foreach ($result as $key => $value) { 
			$value = get_object_vars($value);
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
		$str = "Positivity {$year}";
		if($month) $str .= ' as of '. $this->resolve_month($month);
		if($to_month) $str .= ' to '. $this->resolve_month($to_month) . ' of ' . $to_year;
		$str .= ': ' . $data['sustxfail'] . '&nbsp(<strong>' . $data['rate'] . '%</strong>)';
		return $str;
	}




	public function age()
	{
		extract($this->get_filters());

		if (!$county) {
			$sql = "CALL `proc_get_eid_national_age_range`(0, ".$year_month_query.")";
		} else {
			$sql = "CALL `proc_get_eid_county_age_range`(0, '".$county."',".$year_month_query.")";
		}
		$result = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative']);

		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['age_range'];

			$data["outcomes"][0]["data"][$key]	=  (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['neg'];
		}
		$data['outcomes'][0]['drilldown']['color'] = '#913D88';
		$data['outcomes'][1]['drilldown']['color'] = '#96281B';
		$data['stacking_percent'] = true;		

		return $data;
	}

	public function stacked_bars($chart='age')
	{
		extract($this->get_filters());

		if($chart == 'age'){
			if (!$county) {
				$sql = "CALL `proc_get_eid_national_age_range`(0, ".$year_month_query.")";
			} else {
				$sql = "CALL `proc_get_eid_county_age_range`(0, '".$county."',".$year_month_query.")";
			}
		}else{
			if($chart == 'iprophylaxis'){
				if(!$county){
					$sql = "CALL `proc_get_eid_national_iproph_positivity`(".$year_month_query.")";
				}else{
					$sql = "CALL `proc_get_eid_county_iproph_positivity`('".$county."',".$year_month_query.")";
				}
			}else if($chart == 'mprophylaxis'){
				if(!$county){
					$sql = "CALL `proc_get_eid_national_mproph_positivity`(".$year_month_query.")";
				}else{
					$sql = "CALL `proc_get_eid_county_mproph_positivity`('".$county."',".$year_month_query.")";
				}
			}else if($chart == 'entryPoint'){
				if(!$county){
					$sql = "CALL `proc_get_eid_national_entryP_positivity`(".$year_month_query.")";
				}else{
					$sql = "CALL `proc_get_eid_county_entryP_positivity`('".$county."',".$year_month_query.")";
				}
			}

		}
		$result = DB::select($sql);

		$data = $this->bars(['Positive', 'Negative']);

		foreach ($result as $key => $value) {
			if($chart == 'age') $data['categories'][$key] = $value['age_range'];
			else{
				$data['categories'][$key] = $value['name'];
			}

			$data["outcomes"][0]["data"][$key]	=  (int) $value['pos'];
			$data["outcomes"][1]["data"][$key]	=  (int) $value['neg'];
		}
		$data['outcomes'][0]['drilldown']['color'] = '#913D88';
		$data['outcomes'][1]['drilldown']['color'] = '#96281B';
		$data['stacking_percent'] = true;		

		return view('charts.line_graph', $data);
	}

	public function listings($level='Partner')
	{
		extract($this->get_filters());

		if($level == 'Partner'){
			if($county){
				$sql = "CALL `proc_get_eid_county_partner_positivity`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_nat_partner_positivity`(".$year_month_query.")";
			}
		}
		else if($level == 'County'){			
			$sql = "CALL `proc_get_eid_counties_positivity_stats`(".$year_month_query.")";
		}
		else if($level == 'Subcounty'){
			if($county){
				$sql = "CALL `proc_get_eid_county_subcounties_positivity`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_nat_subcounties_positivity`(".$year_month_query.")";
			}
		}
		else if($level == 'Facility'){
			if($county){
				$sql = "CALL `proc_get_eid_county_sites_positivity`('".$county."',".$year_month_query.")";
			}else{
				$sql = "CALL `proc_get_eid_sites_positivity`(".$year_month_query.")";
			}
		}
		$data['div'] = Str::random(15);
		$data['rows'] = DB::select($sql);
		$data['level'] = $level;

		return view('tables.listings', $data);
	}

	public function age_listings($level='Partner')
	{
		extract($this->get_filters());

		if($level == 'Partner') $type = 3;
		else if($level == 'County') $type = 1;
		else if($level == 'Subcounty') $type = 2;
		else if($level == 'Facility') $type = 4;

		$sql = "CALL `proc_get_eid_age_data_listing`('{$type}', '{$age}', ".$year_month_query.")";

		$data['div'] = Str::random(15);
		$data['rows'] = DB::select($sql);
		$data['level'] = $level;

		return view('tables.listings', $data);
	}

	public function regimen_listings($level='Partner')
	{
		extract($this->get_filters());

		if($level == 'Partner') $partner = 1;
		else if($level == 'County') $county = 1;
		else if($level == 'Subcounty') $subcounty = 1;

		$sql = "CALL `proc_get_eid_iproph_breakdown`('".$regimen."',{$year_month_query}, '".$county."','".$subcounty."','".$partner."')";

		$data['div'] = Str::random(15);
		$data['rows'] = DB::select($sql);
		$data['level'] = $level;

		return view('tables.listings', $data);
	}



	/*public function listings($level='County')
	{
		extract($this->get_filters());

		if($level == 'County'){
			$sql = "CALL `proc_get_eid_counties_positivity_stats`(".$year_month_query.")";
		}else if($level == 'Subcounty'){
			if(!$county) $sql = "CALL `proc_get_eid_nat_subcounties_positivity`(".$year_month_query.")";
			else{
				$sql = "CALL `proc_get_eid_county_subcounties_positivity`('".$county."',".$year_month_query.")";
			}
		}else if($level == 'Partner'){
			if(!$county) $sql = "CALL `proc_get_eid_nat_partner_positivity`(".$year_month_query.")";
			else{
				$sql = "CALL `proc_get_eid_county_partner_positivity`('".$county."',".$year_month_query.")";
			}
		}else if($level == 'Facility'){
			if(!$county) $sql = "CALL `proc_get_eid_sites_positivity`(".$year_month_query.")";
			else{
				$sql = "CALL `proc_get_eid_county_sites_positivity`('".$county."',".$year_month_query.")";
			}
		}

		$data['rows'] = DB::select($sql);

		return view('tables.listings', $data);
	}*/
}
