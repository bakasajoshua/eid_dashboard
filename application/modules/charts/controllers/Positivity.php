<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Positivity extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('positivity_model');
	}

	function positive_trends(){
		$obj['trends'] = $this->positivity_model->yearly_trends();

		$data['trends'] = $obj['trends']['test_trends'];
		$data['title'] = "Test Trends";
		$data['div'] = "#tests";
		$this->load->view('lab_performance_view', $data);

		$data['trends'] = $obj['trends']['rejected_trends'];
		$data['title'] = "Rejected Trends";
		$data['div'] = "#rejects";
		$this->load->view('lab_performance_view', $data);

		$data['trends'] = $obj['trends']['positivity_trends'];
		$data['title'] = "Positivity Trends";
		$data['div'] = "#positivity";
		$this->load->view('lab_performance_view', $data);

		//echo json_encode($obj);
		//echo "<pr>";print_r($obj);die;

	}

	function summary(){
		$data['trends'] = $this->positivity_model->yearly_summary();
		//echo json_encode($data);
		$this->load->view('lab_outcomes_view', $data);
	}


}