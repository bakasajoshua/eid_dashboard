<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Str;

class SubcountyController extends Controller
{

	public function sub_county_outcomes($percent=false)
	{
		extract($this->get_filters());

		if($partner) $sql = "CALL `proc_get_eid_partner_testing_trends`('".$partner."','".$from."','".$to."')";
		else if($county) $sql = "CALL `proc_get_eid_subcounty_outcomes`('".$county."',".$year_month_query.")";
		else{
			$sql = "CALL `proc_get_eid_top_subcounty_outcomes`(".$year_month_query.")";
		}

		$rows = DB::select($sql);

		if($percent){
			$data = $this->bars(['Positive', 'Negative']);
			$data['stacking_percent'] = true;
		}else{
			$data = $this->bars(['Positive', 'Negative', 'Positivity'], [], ['', '', ' %']);
			$data['outcomes'][2]['type'] = 'spline';

			$data['outcomes'][0]['yAxis'] = 1;
			$data['outcomes'][1]['yAxis'] = 1;

		}

		foreach ($result as $key => $value) {
			$data['categories'][$key] = $value['name'];
			$data["outcomes"][0]["data"][$key]	= (int) $value['positive'];
			$data["outcomes"][1]["data"][$key]	= (int) $value['negative'];
			if(!$percent){
				$data["outcomes"][2]["data"][$key]	= round(@( ((int) $value['positive']*100) /((int) $value['positive']+(int) $value['negative'])),1);	
			}
		}
		return view('charts.line_graph', $data);
	}





}
