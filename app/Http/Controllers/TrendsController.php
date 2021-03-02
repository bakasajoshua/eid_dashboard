<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class TrendsController extends Controller
{

	public function yearly_trends()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_tests`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$i = 0;
		$b = true;

		$data;

		$cur_year = date('Y');

		$data_tests['div'] = Str::random(15);
		$data_rejected['div'] = Str::random(15);
		$data_positivity['div'] = Str::random(15);
		$data_infants['div'] = Str::random(15);
		$data_tat4['div'] = Str::random(15);

		$data_tests['title'] = "Testing Trends (Initial PCR)";
		$data_rejected['title'] = "Rejection Rate Trends";
		$data_tat4['title'] = "Turn Around Time ( Collection - Dispatch )";
		$data_infants['title'] = "Infant tests (less than 2m)";
		$data_positivity['title'] = "Positivity Trends";

		$data_tests['div_class'] = $data_rejected['div_class'] = $data_positivity['div_class'] = $data_infants['div_class'] = $data_tat4['div_class'] = 'col-md-12';
		
		$data_tests['categories'] = $data_rejected['categories'] = $data_positivity['categories'] = $data_infants['categories'] = $data_tat4['categories'] = Lookup::$months;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if((int) $value['year'] > $cur_year || (int) $value['year'] < 2008){}
			else{
				if($b){
					$b = false;
					$year = (int) $value['year'];
				}

				$y = (int) $value['year'];
				if($value['year'] != $year){
					$i++;
					$year--;
				}

				$month = (int) $value['month'];
				$month--;

				$data_tests['outcomes'][$i]['name'] = $value['year'];
				$data_tests['outcomes'][$i]['data'][$month] = (int) $value['tests'];

				$data_rejected['outcomes'][$i]['name'] = $value['year'];
				$data_rejected['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['rejected'], $value['tests'], 0);

				$data_positivity['outcomes'][$i]['name'] = $value['year'];
				$data_positivity['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['positive'], ($value['positive'] + $value['negative']), 0);

				$data_infants['outcomes'][$i]['name'] = $value['year'];
				$data_infants['outcomes'][$i]['data'][$month] = (int) $value['infants'];

				$data_tat4['outcomes'][$i]['name'] = $value['year'];
				$data_tat4['outcomes'][$i]['data'][$month] = (int) $value['tat4'];
			}
		}
		$view_data = view('charts.line_graph', $data_tests)->render() . view('charts.line_graph', $data_rejected)->render() . view('charts.line_graph', $data_positivity)->render() . view('charts.line_graph', $data_infants)->render() . view('charts.line_graph', $data_tat4)->render();

		return $view_data;
	}

	public function yearly_summary()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_summary`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_year_summary`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_summary`();";
		}
		
		$result = DB::select($sql);
		$year = date("Y");
		$i = 0;

		$data = $this->bars(['Redraws', 'Positive', 'Negative', 'Positivity'], 'column', ['#52B3D9', '#E26A6A', '#257766', '#913D88'], ['', '', '', ' %']);
		$this->columns($data, 3, 3, 'spline');
		$this->yAxis($data, 0, 2);

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($value['year'] != 2007){
				$data['categories'][$i] = $value['year'];
			
				$data['outcomes'][0]['data'][$i] = (int) $value['redraws'];
				$data['outcomes'][1]['data'][$i] = (int) $value['positive'];
				$data['outcomes'][2]['data'][$i] = (int) $value['negative'];
				$data['outcomes'][3]['data'][$i] = Lookup::get_percentage($value['positive'], ($value['negative']+$value['positive']+$value['redraws']), 1);
				$i++;
			}			
		}

		$data['title'] = "Outcomes (Initial PCR)";

		return view('charts.dual_axis', $data);
	}

	public function quarterly_trends()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_tests`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$i = 0;
		$b = true;
		$limit = 0;
		$quarter = 1;
		$month;


		$data_tests['div'] = Str::random(15);
		$data_rejected['div'] = Str::random(15);
		$data_positivity['div'] = Str::random(15);
		$data_infants['div'] = Str::random(15);
		$data_tat4['div'] = Str::random(15);

		$data_tests['title'] = "Testing Trends (Initial PCR)";
		$data_rejected['title'] = "Rejection Rate Trends";
		$data_tat4['title'] = "Turn Around Time ( Collection - Dispatch )";
		$data_infants['title'] = "Infant tests (less than 2m)";
		$data_positivity['title'] = "Positivity Trends";

		$data_tests['div_class'] = $data_rejected['div_class'] = $data_positivity['div_class'] = $data_infants['div_class'] = $data_tat4['div_class'] = 'col-md-12';
		
		$data_tests['categories'] = $data_rejected['categories'] = $data_positivity['categories'] = $data_infants['categories'] = $data_tat4['categories'] = ['Month 1', 'Month 2', 'Month 3'];

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($b){
				$b = false;
				$year = (int) $value['year'];
			}

			$y = (int) $value['year'];
			$name = $y . ' Q' . $quarter;
			if($value['year'] != $year){
				$year--;
				if($month != 2){
					$i++;
				}
			}

			$m = (int) $value['month'];
			$modulo = ($m % 3);

			$month = $modulo-1;

			if($modulo == 0){
				$month = 2;
			}			

			$data_tests['outcomes'][$i]['name'] = $value['year'];
			$data_tests['outcomes'][$i]['data'][$month] = (int) $value['tests'];

			$data_rejected['outcomes'][$i]['name'] = $value['year'];
			$data_rejected['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['rejected'], $value['tests'], 0);

			$data_positivity['outcomes'][$i]['name'] = $value['year'];
			$data_positivity['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['positive'], ($value['positive'] + $value['negative']), 0);

			$data_infants['outcomes'][$i]['name'] = $value['year'];
			$data_infants['outcomes'][$i]['data'][$month] = (int) $value['infants'];

			$data_tat4['outcomes'][$i]['name'] = $value['year'];
			$data_tat4['outcomes'][$i]['data'][$month] = (int) $value['tat4'];


			if($modulo == 0){
				$i++;
				$quarter++;
				$limit++;
			}
			if($quarter == 5){
				$quarter = 1;
			}
			if ($limit == 8) {
				break;
			}
		}
		
		$view_data = view('charts.line_graph', $data_tests)->render() . view('charts.line_graph', $data_rejected)->render() . view('charts.line_graph', $data_positivity)->render() . view('charts.line_graph', $data_infants)->render() . view('charts.line_graph', $data_tat4)->render();

		return $view_data;
	}

	public function quarterly_outcomes()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_tests`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$prev_year = date('Y') - 1;
		$cur_month = date('m');

		$b = true;
		$limit = 0;
		$quarter = 1;

		$extra = ceil($cur_month / 3);
		$i = 8;

		if($extra == 4){
			$i = 9;
		}
		$columns = 8 + $extra;

		$data = $this->bars(['Redraws', 'Positive', 'Negative', 'Positivity'], 'column', ['#52B3D9', '#E26A6A', '#257766', '#913D88'], ['', '', '', ' %']);
		$this->columns($data, 3, 3, 'spline');
		$this->yAxis($data, 0, 2);

		$data['title'] = "Outcomes (Initial PCR)";

		$data['categories'] = array_fill(0, 8, "Null");
		$data['outcomes'][0]['data'] = array_fill(0, 8, 0);
		$data['outcomes'][1]['data'] = array_fill(0, 8, 0);
		$data['outcomes'][2]['data'] = array_fill(0, 8, 0);
		$data['outcomes'][3]['data'] = array_fill(0, 8, 0);


		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($b){
				$b = false;
				$year = (int) $value['year'];
			}

			$y = (int) $value['year'];
			$name = $y . ' Q' . $quarter;
			if($value['year'] != $year){
				$year--;

				if($year == $prev_year){
					if($modulo != 0){						
						$data['outcomes'][3]['data'][$i] += round(@(( $data['outcomes'][1]['data'][$i]*100)/
						($data['outcomes'][0]['data'][$i]+$data['outcomes'][1]['data'][$i]+$data['outcomes'][2]['data'][$i])),1);
					}
					$i = 4;
					$quarter=1;
					$limit++;

				}
			}

			$month = (int) $value['month'];
			$modulo = ($month % 3);

			$data['categories'][$i] = $name;

			$data['outcomes'][0]['data'][$i] += (int) $value['redraw'];
			$data['outcomes'][1]['data'][$i] += (int) $value['positive'];
			$data['outcomes'][2]['data'][$i] += (int) $value['negative'];			

			if($modulo == 0){
				$data['outcomes'][3]['data'][$i] += round(@(( $data['outcomes'][1]['data'][$i]*100)/
					($data['outcomes'][0]['data'][$i]+$data['outcomes'][1]['data'][$i]+$data['outcomes'][2]['data'][$i])),1);

				$i++;
				$quarter++;
				$limit++;

			}
			if($quarter == 5){
				$quarter = 1;
				$i = 0;
			}	

			if ($limit == ($columns+1)) {
				break;
			}


		}
		return view('charts.dual_axis', $data);
	}

	

	public function alltests(){
		return $this->any_quarterly('allpositive', 'allnegative', 'Outcomes (All Tests)');
	}

	public function rtests(){
		return $this->any_quarterly('rpos', 'rneg', 'Outcomes (2nd/3rd Tests)');
	}

	public function infant_tests(){
		return $this->any_quarterly('infantspos', 'infants', 'Outcomes (Infants <2m)');
	}



	public function any_quarterly($pos_c, $neg_c, $title)
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_tests`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$prev_year = date('Y') - 1;
		$cur_month = date('m');

		$b = true;
		$limit = 0;
		$quarter = 1;

		$extra = ceil($cur_month / 3);
		$i = 8;

		if($extra == 4){
			$i = 9;
		}
		$columns = 8 + $extra;


		$data = $this->bars(['Positive', 'Negative', 'Positivity'], 'column', ['#E26A6A', '#257766', '#913D88'], ['', '', ' %']);
		$this->columns($data, 2, 2, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = $title;

		$data['categories'] = array_fill(0, $columns, "Null");
		$data['outcomes'][0]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][1]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][2]['data'] = array_fill(0, $columns, 0);


		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($b){
				$b = false;
				$year = (int) $value['year'];
			}

			$y = (int) $value['year'];
			$name = $y . ' Q' . $quarter;

			if($value['year'] != $year){
				$year--;

				if($year == $prev_year){
					if($modulo != 0){	
						$data['outcomes'][2]['data'][$i] = round(@(( $data['outcomes'][0]['data'][$i]*100)/
						( $data['outcomes'][0]['data'][$i] + $data['outcomes'][1]['data'][$i] )),1);
					}
					$i = 4;
					$quarter=1;
					$limit++;

				}

			}

			$month = (int) $value['month'];
			$modulo = ($month % 3);

			$data['categories'][$i] = $name;

			$data['outcomes'][0]['data'][$i] += (int) $value[$pos_c];
			$data['outcomes'][1]['data'][$i] += (int) $value[$neg_c];

			if($neg_c == "infants"){
				$data['outcomes'][1]['data'][$i] -= (int) $value[$pos_c];
			}
			

			if($modulo == 0){
				$data['outcomes'][2]['data'][$i] = round(@(( $data['outcomes'][0]['data'][$i]*100)/
				( $data['outcomes'][0]['data'][$i]+$data['outcomes'][1]['data'][$i] )),1);

				$i++;
				$quarter++;
				$limit++;
			}

			if($quarter == 5){
				$quarter = 1;
				$i = 0;
			}

			if ($limit == ($columns+1)) {
				break;
			}

		}
		return view('charts.dual_axis', $data);
	}



	public function ages_2m_quarterly()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_county_yearly_tests_age`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_yearly_tests_age`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests_age`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$prev_year = date('Y') - 1;
		$cur_month = date('m');

		$b = true;
		$limit = 0;
		$quarter = 1;

		$extra = ceil($cur_month / 3);
		$i = 8;

		if($extra == 4){
			$i = 9;
		}
		$columns = 8 + $extra;

		$data['div'] = Str::random(15);
		$data['outcomes'][0]['name'] = "No Data";
		$data['outcomes'][1]['name'] = ">24m";
		$data['outcomes'][2]['name'] = "12-24m";
		$data['outcomes'][3]['name'] = "9-12m";
		$data['outcomes'][4]['name'] = "2-9m";
		$data['outcomes'][5]['name'] = "<2m";
		$data['outcomes'][6]['name'] = "<2m contribution";

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "column";
		$data['outcomes'][4]['type'] = "column";
		$data['outcomes'][5]['type'] = "column";
		$data['outcomes'][6]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;
		$data['outcomes'][3]['yAxis'] = 1;
		$data['outcomes'][4]['yAxis'] = 1;
		$data['outcomes'][5]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][4]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][5]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][6]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "Less 2m Contribution (Initial PCR)";

		$data['categories'] = array_fill(0, $columns, "Null");
		$data['outcomes'][0]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][1]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][2]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][3]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][4]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][5]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][6]['data'] = array_fill(0, $columns, 0);

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($b){
				$b = false;
				$year = (int) $value['year'];
			}

			$y = (int) $value['year'];
			$name = $y . ' Q' . $quarter;

			if($value['year'] != $year){
				$year--;

				if($year == $prev_year){

					if($modulo != 0){	
						$total = $data['outcomes'][0]['data'][$i] + $data['outcomes'][1]['data'][$i] + $data['outcomes'][2]['data'][$i] + $data['outcomes'][3]['data'][$i] + $data['outcomes'][4]['data'][$i] + $data['outcomes'][5]['data'][$i];

						$data['outcomes'][6]['data'][$i] = round(@( $data['outcomes'][5]['data'][$i]*100 / $total ),1);
					}

					$i = 4;
					$quarter=1;
					$limit++;
				}
			}

			$age_range = (int) $value['age_range_id'];
			$month = (int) $value['month'];
			$modulo = ($month % 3);

			$data['categories'][$i] = $name;

			// $data['outcomes'][$age_range]['data'][$i] += ((int) $value['pos'] + (int) $value['neg']);

			switch ($age_range) {
				case 0:
					$data['outcomes'][0]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				case 1:
					$data['outcomes'][5]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				case 2:
					$data['outcomes'][4]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				case 3:
					$data['outcomes'][3]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				case 4:
					$data['outcomes'][2]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				case 5:
					$data['outcomes'][1]['data'][$i] += (int) $value['pos'] + (int) $value['neg'];
					break;
				default:
					break;
			}
			

			if($modulo == 0 && $age_range == 5){
				$total = $data['outcomes'][0]['data'][$i] + $data['outcomes'][1]['data'][$i] + $data['outcomes'][2]['data'][$i] + $data['outcomes'][3]['data'][$i] + $data['outcomes'][4]['data'][$i] + $data['outcomes'][5]['data'][$i];

				$data['outcomes'][6]['data'][$i] = round(@( $data['outcomes'][5]['data'][$i]*100 / $total ),1);

				$i++;
				$quarter++;
				$limit++;
			}

			if($quarter == 5){
				$quarter = 1;
				$i = 0;
			}

			if ($limit == ($columns+1)) {
				break;
			}
		}
		return view('charts.dual_axis', $data);
	}



	public function ages_quarterly()
	{
		extract($this->get_filters());

		if($county) $sql = "CALL `proc_get_eid_yearly_tests`(" . $county . ");";
		else if($partner || $partner === 0) $sql = "CALL `proc_get_eid_partner_yearly_tests_age`(" . $partner . ");";
		else{
			$sql = "CALL `proc_get_eid_national_yearly_tests_age`();";
		}
		
		$result = DB::select($sql);
		
		$year;
		$i = 8;
		$b = true;
		$limit = 0;
		$quarter = 1;

		$data['div'] = Str::random(15);

		$data['outcomes'][0]['name'] = "No Data POS";
		$data['outcomes'][1]['name'] = "No Data NEG";
		$data['outcomes'][2]['name'] = "<2m POS";
		$data['outcomes'][3]['name'] = "<2m NEG";
		$data['outcomes'][4]['name'] = "2-9m POS";
		$data['outcomes'][5]['name'] = "2-9m NEG";
		$data['outcomes'][6]['name'] = "9-12m POS";
		$data['outcomes'][7]['name'] = "9-12m NEG";
		$data['outcomes'][8]['name'] = "12-24m POS";
		$data['outcomes'][9]['name'] = "12-24m NEG";
		$data['outcomes'][10]['name'] = ">24m POS";
		$data['outcomes'][11]['name'] = ">24m NEG";

		$data['title'] = 'Ages Quarterly';

		$data['categories'] = array_fill(0, 9, "Null");
		$data['outcomes'][0]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][1]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][2]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][3]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][4]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][5]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][6]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][7]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][8]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][9]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][10]['data'] = array_fill(0, 9, 0);
		$data['outcomes'][11]['data'] = array_fill(0, 9, 0);


		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			if($b){
				$b = false;
				$year = (int) $value['year'];
			}

			$y = (int) $value['year'];
			$name = $y . ' Q' . $quarter;

			if($value['year'] != $year){
				$year--;

				if($year == 2017){
					$i = 4;
					$quarter=1;
					$limit++;

				}

			}

			$age_range = (int) $value['age_range_id'];
			$month = (int) $value['month'];
			$modulo = ($month % 3);

			$data['categories'][$i] = $name;

			switch ($age_range) {
				case 0:
					$data['outcomes'][0]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][1]['data'][$i] += (int) $value[$neg];
					break;
				case 1:
					$data['outcomes'][2]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][3]['data'][$i] += (int) $value[$neg];
					break;
				case 2:
					$data['outcomes'][4]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][5]['data'][$i] += (int) $value[$neg];
					break;
				case 3:
					$data['outcomes'][6]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][7]['data'][$i] += (int) $value[$neg];
					break;
				case 4:
					$data['outcomes'][8]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][9]['data'][$i] += (int) $value[$neg];
					break;
				case 5:
					$data['outcomes'][10]['data'][$i] += (int) $value[$pos];
					$data['outcomes'][11]['data'][$i] += (int) $value[$neg];
					break;
				default:
					break;
			}
			

			if($modulo == 0){

				$i++;
				$quarter++;
				$limit++;

				if($i == 8){
					$i == 0;
				}

			}

			if($quarter == 5){
				$quarter = 1;
				$i = 0;
			}


			if ($limit == 9) {
				break;
			}


		}

		return view('charts.line_graph', $data);
	}

}
