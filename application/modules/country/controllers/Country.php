<?php
/**
* 
*/
class Country extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data, $this->load_libraries(array('material','highstock','highmaps','highcharts','custom', 'Kenya', 'tablecloth', 'select2')));
		$this->session->set_userdata('partner_filter', NULL);
		$this->load->module('charts/counties');
	}

	public function index()
	{
		$this->data['content_view'] = 'country/county_map';
		$this -> template($this->data);
	}
}
?>