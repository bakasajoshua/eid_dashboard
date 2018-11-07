<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
* 
*/
class Partner_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();;
	}

	function yearly_trends($partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}


		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$i = 0;

		$b = true;
		$year;

		$data;

		$cur_year = date('Y');

		foreach ($result as $key => $value) {
			if((int) $value['year'] > $cur_year || (int) $value['year'] < 2008){

			}
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


				$data['test_trends'][$i]['name'] = $value['year'];
				$data['test_trends'][$i]['data'][$month] = (int) $value['tests'];

				$data['rejected_trends'][$i]['name'] = $value['year'];

				if($value['tests'] == 0){
					$data['rejected_trends'][$i]['data'][$month] = 0;
				}else{
					$data['rejected_trends'][$i]['data'][$month] = (int)
					($value['rejected'] / $value['tests'] * 100);
				}

				$data['positivity_trends'][$i]['name'] = $value['year'];

				if ($value['positive'] == 0){
					$data['positivity_trends'][$i]['data'][$month] = 0;
				}else{
					$data['positivity_trends'][$i]['data'][$month] = (int) 
					($value['positive'] / ($value['positive'] + $value['negative']) * 100 );
				}

				$data['infant_trends'][$i]['name'] = $value['year'];
				$data['infant_trends'][$i]['data'][$month] = (int) $value['infants'];
				
			}

		}		

		return $data;
	}

	function yearly_summary($partner=NULL){

		if($partner == NULL){
			$partner = 0;
		}

		$sql = "CALL `proc_get_eid_partner_year_summary`(" . $partner . ");";

		$result = $this->db->query($sql)->result_array();

		
		$i = 0;

		$data;

		$data['outcomes'][0]['name'] = "Redraws";
		$data['outcomes'][1]['name'] = "Positive";
		$data['outcomes'][2]['name'] = "Negative";
		$data['outcomes'][3]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		foreach ($result as $key => $value) {
			if($value['year'] != 2007){
				$data['categories'][$i] = $value['year'];

				$total = (int) $value['negative']+(int) $value['positive']+(int) $value['redraws'];

				$data['outcomes'][0]['data'][$i] = (int) $value['redraws'];
				$data['outcomes'][1]['data'][$i] = (int) $value['positive'];
				$data['outcomes'][2]['data'][$i] = (int) $value['negative'];

				if($total == 0){
					$data['outcomes'][3]['data'][$i] = 0;
				}
				else{
					$data['outcomes'][3]['data'][$i] = round(( (int) $value['positive']*100)/$total,1);
				}
				
				$i++;
			}
			
		}
		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' %');
		
		$data['title'] = "Outcomes";

		return $data;
	}

	function quarterly_trends($partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = $this->db->query($sql)->result_array();
		
		$year;
		$i = 0;
		$b = true;
		$limit = 0;
		$quarter = 1;
		$month;

		$data;

		foreach ($result as $key => $value) {

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

			$data['test_trends'][$i]['name'] = $name;
			$data['test_trends'][$i]['data'][$month] = (int) $value['tests'];

			$data['rejected_trends'][$i]['name'] = $name;

			if($value['tests'] == 0){
				$data['rejected_trends'][$i]['data'][$month] = 0;
			}else{
				$data['rejected_trends'][$i]['data'][$month] = (int)
				($value['rejected'] / $value['tests'] * 100);
			}

			$data['positivity_trends'][$i]['name'] = $name;

			if ($value['positive'] == 0){
				$data['positivity_trends'][$i]['data'][$month] = 0;
			}else{
				$data['positivity_trends'][$i]['data'][$month] = (int) 
				($value['positive'] / ($value['positive'] + $value['negative']) * 100 );
			}

			$data['infant_trends'][$i]['name'] = $name;
			$data['infant_trends'][$i]['data'][$month] = (int) $value['infants'];

			$data['tat4_trends'][$i]['name'] = $name;
			$data['tat4_trends'][$i]['data'][$month] = (int) $value['tat4'];

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
		

		return $data;
	}

	function quarterly_outcomes($partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
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

		$data['outcomes'][0]['name'] = "Redraws";
		$data['outcomes'][1]['name'] = "Positive";
		$data['outcomes'][2]['name'] = "Negative";
		$data['outcomes'][3]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['title'] = "Outcomes (Initial PCR)";

		$data['categories'] = array_fill(0, $columns, "No Data");
		$data['outcomes'][0]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][1]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][2]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][3]['data'] = array_fill(0, $columns, 0);


		foreach ($result as $key => $value) {

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

		return $data;

	}

	

	function alltests($partner=NULL){
		return $this->any_quarterly('allpositive', 'allnegative', 'Outcomes (All Tests)', $partner);
	}

	function rtests($partner=NULL){
		return $this->any_quarterly('rpos', 'rneg', 'Outcomes (2nd/3rd Tests)', $partner);
	}

	function infant_tests($partner=NULL){
		return $this->any_quarterly('infantspos', 'infants', 'Outcomes (Infants <2m)', $partner);
	}



	function any_quarterly($pos_c, $neg_c, $title, $partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		
		$result = $this->db->query($sql)->result_array();
		
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

		$data['outcomes'][0]['name'] = "Positive";
		$data['outcomes'][1]['name'] = "Negative";
		$data['outcomes'][2]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#E26A6A';
		$data['outcomes'][1]['color'] = '#257766';
		$data['outcomes'][2]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['title'] = $title;

		$data['categories'] = array_fill(0, $columns, "Null");
		$data['outcomes'][0]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][1]['data'] = array_fill(0, $columns, 0);
		$data['outcomes'][2]['data'] = array_fill(0, $columns, 0);


		foreach ($result as $key => $value) {

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
		return $data;
	}



	function ages_2m_quarterly($partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_yearly_tests_age`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests_age`();";
		}
		
		
		$result = $this->db->query($sql)->result_array();
		
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
		return $data;
	}

	function some_quarters($partner=NULL){

		if($partner == NULL || $partner == 'NA'){
			$partner = NULL;
		}

		if ($partner) {
			$sql = "CALL `proc_get_eid_partner_performance`(" . $partner . ");";
		} else {
			$sql = "CALL `proc_get_eid_national_yearly_tests`();";
		}
		$year = null;
		$quarter = null;
		$count = 0;
		$newdata[] = ['year'=>null, 
					'quarter'=>null, 'tests'=>0, 'positive'=>0, 'negative'=>0, 'allpositive'=>0, 
					'allnegative'=>0, 'rpos'=>0, 'rneg'=>0, 'rejected'=>0, 'infants'=>0, 'infantspos'=>0, 
					'redraw'=>0, 'tat4' => 0];
		$result = $this->db->query($sql)->result();

		foreach ($result as $key => $value) {
			if ($year == null || $year != $value->year) {
				$year = $value->year;
			}

			if ($quarter != null && $quarter != $this->getQuarter($value->month))
				$count++;

			$quarter = $this->getQuarter($value->month);
			$newdata[$count]['year'] = $year;
			$newdata[$count]['quarter'] = $quarter;
			$newdata[$count]['tests'] += $value->tests;
			$newdata[$count]['positive'] += $value->positive;
			$newdata[$count]['negative'] += $value->negative;
			$newdata[$count]['allpositive'] += $value->allpositive;
			$newdata[$count]['allnegative'] += $value->allnegative;
			$newdata[$count]['rpos'] += $value->rpos;
			$newdata[$count]['rneg'] += $value->rneg;
			$newdata[$count]['rejected'] += $value->rejected;
			$newdata[$count]['infants'] += $value->infants;
			$newdata[$count]['infantspos'] += $value->infantspos;
			$newdata[$count]['redraw'] += $value->redraw;
			$newdata[$count]['tat4'] = $value->tat4;
  			
		}
				
		$data['outcomes'][0]['name'] = "Redraws";
		$data['outcomes'][1]['name'] = "Positive";
		$data['outcomes'][2]['name'] = "Negative";
		$data['outcomes'][3]['name'] = "Positivity";

		$data['outcomes'][0]['color'] = '#52B3D9';
		$data['outcomes'][1]['color'] = '#E26A6A';
		$data['outcomes'][2]['color'] = '#257766';
		$data['outcomes'][3]['color'] = '#913D88';

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "spline";

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['title'] = "Outcomes (Initial PCR)";

		$data['categories'] = "No Data";
		$data['outcomes'][0]['data'][0] = 0;
		$data['outcomes'][1]['data'][0] = 0;
		$data['outcomes'][2]['data'][0] = 0;
		$data['outcomes'][3]['data'][0] = 0;


		foreach ($newdata as $key => $value) {
			

		// 	if($b){
		// 		$b = false;
		// 		$year = (int) $value['year'];
		// 	}

		// 	$y = (int) $value['year'];
		// 	$name = $y . ' Q' . $quarter;
		// 	if($value['year'] != $year){
		// 		$year--;

		// 		if($year == $prev_year){

		// 			if($modulo != 0){	
		// 				$data['outcomes'][3]['data'][$i] += round(@(( $data['outcomes'][1]['data'][$i]*100)/
		// 				($data['outcomes'][0]['data'][$i]+$data['outcomes'][1]['data'][$i]+$data['outcomes'][2]['data'][$i])),1);
		// 			}
		// 			$i = 4;
		// 			$quarter=1;
		// 			$limit++;

		// 		}
		// 	}

		// 	$month = (int) $value['month'];
		// 	$modulo = ($month % 3);

			$data['categories'][$key] = $name;

			$data['outcomes'][0]['data'][$key] = (int) $value['redraw'];
			$data['outcomes'][1]['data'][$key] = (int) $value['positive'];
			$data['outcomes'][2]['data'][$key] = (int) $value['negative'];
			$data['outcomes'][3]['data'][$key] = round(((int) $value['positive']/(int)$value['tests']), 1);

		// 	if($modulo == 0){
		// 		$data['outcomes'][3]['data'][$i] += round(@(( $data['outcomes'][1]['data'][$i]*100)/
		// 			($data['outcomes'][0]['data'][$i]+$data['outcomes'][1]['data'][$i]+$data['outcomes'][2]['data'][$i])),1);

		// 		$i++;
		// 		$quarter++;
		// 		$limit++;

		// 	}
		// 	if($quarter == 5){
		// 		$quarter = 1;
		// 		$i = 0;
		// 	}	

		// 	if ($limit == ($columns+1)) {
		// 		break;
		// 	}


		}

		return $data;

	}

	function getQuarter($month){
		$quarters = ['1' => [1,2,3], '2' => [4,5,6], '3' => [7,8,9], '4' => [10,11,12]];
		foreach ($quarters as $key => $value) {
			if (in_array($month, $value)) {
				$quarter = $key;
				break;
			}
		}
		return $quarter;
	}


}