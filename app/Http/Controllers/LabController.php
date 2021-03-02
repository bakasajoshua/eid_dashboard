<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lookup;
use DB;
use Str;

class LabController extends Controller
{

	public function lab_performance_stat()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_lab_performance_stats`(".$year_month_query.");";
		$result = DB::select($sql);

		$table = '';
		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			$name = $value['name'];
			if(!$name) $name = "POC Sites";
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>" . $name . "</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . Lookup::get_percentage($value['rejected'], $value['received'], 1)
							."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) $value['redraw'])."</td>
						<td>".number_format((int) $value['eqa'])."</td>
						<td>".number_format((int) $value['controls'])."</td>
						<td>".number_format((int) ($value['pos']+$value['neg']))."</td>
						<td>".number_format((int) $value['pos'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatspospos'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedpos'])."</td>
						<td>".number_format((int) $value['fake_confirmatory'])."</td>

						<td>".number_format((int) $value['tiebreaker'])."</td>
						<td>".number_format((int) $value['tiebreakerPOS'])."</td>
						
						<td>".number_format((int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']))."</td>
						<td>".number_format((int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']))."</td>						
					</tr>";
					
		}
		$t_head = "			
			<tr>
				<th> # </th>
				<th> Lab </th>
				<th> Facilities Sending Samples </th>
				<th> Received Samples at Lab </th>
				<th> Rejected Samples (on receipt at lab) </th>
				<th> All Tests (plus reruns) Done at Lab </th>
				<th> Redraws (after testing) </th>
				<th> EQA Tests </th>
				<th> Controls Run </th>
				<th> Initial PCR Tests </th>
				<th> Initial PCR Positives </th>
				<th> 2nd/3rd PCR Tests </th>
				<th> 2nd/3rd PCR Positives </th>
				<th> Confirmatory PCR Tests </th>
				<th> Confirmatory PCR Positives </th>
				<th> Confirmatory Without Previous Positive </th>
				<th> Tiebreaker PCR Tests </th>
				<th> Tiebreaker PCR Positives </th>
				<th> Tests with Valid Outcomes </th>
				<th> Tests with Valid Outcomes - Positives </th>
			</tr>
		";
		$data['outcomes'] = $table;
		$data['th'] = $t_head;
		$data['div'] = Str::random(15);

		return view('tables.datatable', $data);
	}



	public function lab_testing_trends($trend='testing')
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_lab_performance`('".$year."')";

		$result = DB::select($sql);

		$data['div'] = Str::random(15);
		$data['categories'] = Lookup::$months;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

			$month = (int) $value['month'];
			$month--;

			$lab = (int) $value['lab'];
			$lab--;

			$data['outcomes'][$lab]['name'] = $value['name'];
			if(!$data['outcomes'][$lab]['name']) $data['outcomes'][$lab]['name'] = "POC Sites";

			$tests = (int) $value['new_tests'];
			$received = (int) $value['received'];

			if($trend == 'testing') $data['outcomes'][$lab]['data'][$month] = (int) $tests;
			else if($trend == 'positivity') $data['outcomes'][$lab]['data'][$month] = Lookup::get_percentage($value['pos'], ($value['pos'] + $value['neg']), 1);
			else if($trend == 'rejection') $data['outcomes'][$lab]['data'][$month] = Lookup::get_percentage($value['rejected'], $received, 1);
		}

		if($trend != 'rejection') return view('charts.line_graph', $data);

		$sql2 = "CALL `proc_get_eid_average_rejection`('".$year."')";
		$result2 = DB::select($sql2);

		$i = count($data['outcomes']);

		foreach ($result2 as $key => $value) {
			$value = get_object_vars($value);
					
			$data['outcomes'][$i]['name'] = 'National Rejection Rate';
			$data['outcomes'][$i]['data'][$key] =  Lookup::get_percentage($value['rejected'], $value['received'], 1); 
		}

		return view('charts.line_graph', $data);
	}

	public function yearly_trends()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_yearly_lab_tests`(" . $lab . ");";

		$result = DB::select($sql);
		
		$year;
		$i = 0;
		$b = true;

		$data;

		$cur_year = date('Y');

		$data_tests['div'] = Str::random(15);
		$data_rejected['div'] = Str::random(15);
		$data_positivity['div'] = Str::random(15);
		$data_tat4['div'] = Str::random(15);

		$data_tests['categories'] = $data_rejected['categories'] = $data_positivity['categories'] = $data_tat4['categories'] = Lookup::$months;

		foreach ($result as $key => $value) {
			$value = get_object_vars($value);

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

				$data_tests['outcomes'][$i]['name'] = $value['year'];
				$data_rejected['outcomes'][$i]['name'] = $value['year'];
				$data_positivity['outcomes'][$i]['name'] = $value['year'];
				$data_tat4['outcomes'][$i]['name'] = $value['year'];


				$data_tests['outcomes'][$i]['data'][$month] = (int) $value['tests'];
				$data_rejected['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['rejected'], $value['tests'], 0);
				$data_positivity['outcomes'][$i]['data'][$month] = Lookup::get_percentage($value['positive'], ($value['positive'] + $value['negative']), 0);
				$data_tat4['outcomes'][$i]['data'][$month] = (int) $value['tat4'];
			}

		}
		$view_data = view('charts.line_graph', $data_tests)->render() . view('charts.line_graph', $data_rejected)->render() . view('charts.line_graph', $data_positivity)->render() . view('charts.line_graph', $data_tat4)->render();
		

		return $view_data;
	}


	public function labs_turnaround()
	{
		extract($this->get_filters());

		$title = " (" . $year . ")";
		if($month) $title = " (" . $year . ", " . $this->resolve_month($month) . ")";
		if($to_year) $title = " (" . $year . ", " . $this->resolve_month($month) . " - ". $to_year . ", " . $this->resolve_month($to_month) .")";

		$sql = "CALL `proc_get_eid_lab_tat`(".$year_month_query.")";
		
		$rows = DB::select($sql);
		
		$view_data = '';
		$for_labs = true;

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			// $title = strtolower(str_replace(" ", "_", $value['labname']));
			$title = $value['labname'] ?? $value['name'];
			if(!$title) $title = "POC Sites";
			$div = Str::random(15);
			$tat1 = round($value['tat1']);
			$tat2 = round($value['tat2']+$tat1);
			$tat3 = round($value['tat3']+$tat2);
			$tat4 = round($value['tat4']);

			$view_data .= view('charts.tat', compact('tat1', 'tat2', 'tat3', 'tat4', 'div', 'title', 'for_labs'))->render();
		}
		$view_data .= view('charts.tat_key');
		
		return $view_data;
	}


	public function rejections()
	{	
		extract($this->get_filters());
		if(!$lab) $lab = 0;
		
		$sql = "CALL `proc_get_eid_lab_rejections`({$lab}, {$year_month_query});";
		$rows = DB::select($sql);

		$data = $this->bars(['Rejected Samples', '% Rejected'], 'column', [], ['', ' %']);
		$this->columns($data, 1, 1, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;

		$total = 0;
		foreach ($rows as $key => $value) {
			$total += $value->total;
		}

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['name'];
		
			$data['outcomes'][0]['data'][$key] = (int) $value['total'];
			$data['outcomes'][1]['data'][$key] = Lookup::get_percentage($value['total'], $total, 1);
		}

		if($lab == 0) $data['title'] = "National Rejections";
		else{
			$data['title'] = "Lab Rejections";
		}

		return view('charts.dual_axis', $data);
	}


	public function mapping()
	{	
		extract($this->get_filters());
		if(!$lab) $lab = 0;
		
		$sql = "CALL `proc_get_eid_lab_site_mapping`({$lab}, {$year_month_query});";
		$rows = DB::select($sql);

		$data['title'] = "Tests";
		$data['div'] = Str::random(15);

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			
			$data['outcomes'][$key]['id'] = (int) $value['county'];			
			$data['outcomes'][$key]['value'] = (int) $value['value'];
		}

		return view('charts.kenya_map', $data);
	}


	public function poc_performance_stat()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_poc_performance_stats`({$year_month_query});";
		$rows = DB::select($sql);

		$data['div'] = Str::random(15);

		$table = '';
		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$name = $value['name'];
			if(!$name) $name = "POC Sites";
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>" . $name . "</td>
						<td>" . $value['facilitycode'] . "</td>
						<td>" . $value['countyname'] . "</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round(@(($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) ($value['pos']+$value['neg']))."</td>
						<td>".number_format((int) $value['pos'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatspospos'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedpos'])."</td>
						
						<td>".number_format((int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']))."</td>
						<td>".number_format((int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']))."</td>
						<td> <button class='btn btn-primary'  onclick='expand_poc(" . $value['id'] . ");' style='background-color: #1BA39C;color: white; margin-top: 1em;margin-bottom: 1em;'>View</button> </td>						
					</tr>";
					
		}
		$data['outcomes'] = $table;
		$data['th'] = "
			<th> # </th>
			<th> Facility </th>
			<th> MFL </th>
			<th> County </th>
			<th> Facilities Sending Samples </th>
			<th> Received Samples at Hub </th>
			<th> Rejected Samples (on receipt at Hub) </th>
			<th> Tests with Valid Outcomes + Redraws (after testing) </th>
			<th> Initial PCR Tests </th>
			<th> Initial PCR Positives </th>
			<th> 2nd/3rd PCR Tests </th>
			<th> 2nd/3rd PCR Positives </th>
			<th> Confirmatory PCR Tests </th>
			<th> Confirmatory PCR Positives </th>
			<th> Tests with Valid Outcomes </th>
			<th> Tests with Valid Outcomes - Positives </th>
			<th> View Spokes </th>
		";
		return view('tables.datatable', $data);
	}

	public function poc_performance_details($lab_id=NULL)
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_poc_site_details`('".$lab_id."',{$year_month_query});";
		$rows = DB::select($sql);

		$data['div'] = Str::random(15);
		$data['modal_div'] = Str::random(15);
		$data['modal_title'] = 'POC Site Details';

		$table = '';
		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$name = "POC Sites";
			if($value['name']) $name = $value['name'];
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['name']."</td>
						<td>".$value['facilitycode']."</td>
						<td>" . $value['countyname'] . "</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round(@(($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) ($value['positive']+$value['negative']))."</td>
						<td>".number_format((int) $value['positive'])."</td>
						<td>".number_format((int) $value['repeatspos'])."</td>
						<td>".number_format((int) $value['repeatsposPOS'])."</td>
						<td>".number_format((int) $value['confirmdna'])."</td>
						<td>".number_format((int) $value['confirmedPOS'])."</td>
						
						<td>".number_format((int) ($value['positive']+$value['negative']+$value['confirmdna'] + $value['repeatspos'] ))."</td>
						<td>".number_format((int) ($value['positive']+$value['confirmedPOS'] + $value['repeatsposPOS'] ))."</td>	

					</tr>";
		}
		$data['outcomes'] = $table;
		$data['th'] = "
			<th> # </th>
			<th> Facility </th>
			<th> MFL </th>
			<th> County </th>
			<th> Received Samples at Hub </th>
			<th> Rejected Samples (on receipt at Hub) </th>
			<th> Tests with Valid Outcomes + Redraws (after testing) </th>
			<th> Initial PCR Tests </th>
			<th> Initial PCR Positives </th>
			<th> 2nd/3rd PCR Tests </th>
			<th> 2nd/3rd PCR Positives </th>
			<th> Confirmatory PCR Tests </th>
			<th> Confirmatory PCR Positives </th>
			<th> Tests with Valid Outcomes </th>
			<th> Tests with Valid Outcomes - Positives </th>
		";
		return view('tables.modal-table', $data);
	}

	public function poc_outcomes()
	{
		extract($this->get_filters());

		$sql = "CALL `proc_get_eid_poc_performance_stats`({$year_month_query});";
		$rows = DB::select($sql);

		$data = $this->bars(['Redraws', 'Positive', 'Negative', 'Positivity'], 'column', ['#52B3D9', '#E26A6A', '#257766', '#913D88'], ['', '', '', ' %']);
		$this->columns($data, 3, 3, 'spline');

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;

		$data['title'] = "";

		foreach ($rows as $key => $value) {
			$value = get_object_vars($value);
			$data['categories'][$key] = $value['name'];

			$pos = (int) ($value['pos']+$value['confirmedpos'] + $value['repeatspospos'] + $value['tiebreakerPOS']);
			$tests = (int) ($value['pos']+$value['neg']+$value['confirmdna'] + $value['repeatspos'] + $value['tiebreaker']);
			$neg = $tests - $pos;
			$redraw = (int) $value['redraw'];
		
			$data['outcomes'][0]['data'][$key] = $redraw;
			$data['outcomes'][1]['data'][$key] = $pos;
			$data['outcomes'][2]['data'][$key] = $neg;
			$data['outcomes'][3]['data'][$key] = Lookup::get_percentage($pos, $tests, 1);
		}
		return view('charts.dual_axis', $data);
	}
}

