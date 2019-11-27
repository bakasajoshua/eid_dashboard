<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* 
*/
class Template extends MY_Controller
{
	
	public function index($data)
	{
		$this->load_template($data);
	}

	public function all_sess()
	{print_r($this->session->all_userdata());}

	public function load_template($data)
	{
		$this->load->model('template_model');

		$data['filter'] = $this->template_model->get_counties_dropdown();
		$data['partner'] = $this->template_model->get_partners_dropdown();
		$data['sites'] = $this->template_model->get_site_dropdown();
		$data['subCounty'] = $this->template_model->get_sub_county_dropdown();
		$data['laborotories'] = $this->template_model->get_lab_dropdown();
		$data['regimen'] = $this->template_model->get_regimen_dropdown();
		$data['ages'] = $this->template_model->get_ages_dropdown();
		$data['agencies'] = $this->template_model->funding_agencies_dropdown();
		// $data['breadcrum'] = $this->breadcrum();
		$this->load->view('template_view',$data);
	}

	function filter_county_data()
	{
		
		$data = array(
				'county' => $this->input->post('county')
			);

		$this->filter_regions($data);

		echo $this->session->userdata('county_filter');
		
	}

	function filter_sub_county_data()
	{
		
		$data = array(
				'sub_county' => $this->input->post('subCounty')
			);
		// echo "<pre>";print_r($data);die();
		$this->filter_sub_county($data);

		// echo $this->input->post('subCounty');
		
		echo $this->session->userdata('sub_county_filter');
		
	}

	function filter_partner_data()
	{
		
		$data = array(
				'partner' => $this->input->post('partner')
			);
		
		$this->filter_partners($data);

		echo $this->session->userdata('partner_filter');
		
	}
	
	function filter_site_data()
	{
		$data = array(
				'site' => $this->input->post('site')
			);
		
		$this->filter_site($data);

		echo $this->session->userdata('site_filter');
	}

	function filter_date_data()
	{
		$data = array(
				'year' => $this->input->post('year'),
				'month' => $this->input->post('month')
			);
		
		echo $this->set_filter_date($data);
	}

	function filter_age_data()
	{
		$data = array(
				'age' => $this->input->post('age')
			);
		
		$this->filter_age($data);

		echo $this->session->userdata('age_filter');
	}

	function filter_regimen_data()
	{
		$data = array(
				'regimen' => $this->input->post('regimen')
			);
		
		$this->filter_regimen($data);

		echo $this->session->userdata('regimen_filter');
	}

	function filter_funding_agency_data() {
		$data = array(
				'funding_agency' => $this->input->post('agency')
		);

		$response = $this->filter_funding_agency($data);
		if ($response)
			echo json_encode($this->session->userdata('funding_agency_filter'));
	}

	function breadcrum($data=null,$partner=NULL,$site=NULL,$sub_county=NULL,$age=NULL,$regimen=NULL,$type=null) {
		/*$type ==> 
			1: county
			2: Partner
			3: Sub-County
			4: Site
			5: Funding Agency*/
		if ($partner=='null'||$partner==null) {
			$partner = NULL;
		}
		if ($site=='null'||$site==null) {
			$site = NULL;
		}
		if ($data=='null'||$data==null) {
			$data = NULL;
		}
		if ($sub_county=='null'||$sub_county==null) {
			$sub_county = NULL;
		}
		if ($age=='null'||$age==null) {
			$age = NULL;
		}
		if ($regimen=='null'||$regimen==null) {
			$regimen = NULL;
		}
		if ($type=='null'||$type==null) 
			$type = NULL;
		
		$this->load->model('template_model');
		// echo "<pre>";print_r($data."<_Part__>".$partner."<_Site__>".$site."<_Sub__>".$sub_county."<__Age_>".$age."<__Regimen_>".$regimen);die();
		if ($partner) {
			if (!$data) {
				if (!$this->session->userdata('partner_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Partners</strong></a>";
				} else {
					$partner = $this->template_model->get_partner_name($this->session->userdata('partner_filter'));
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$partner."</strong></a>";
				}
			} else {
				$partner = $this->template_model->get_partner_name($data);
				echo "<a href='javascript:void(0)' class='alert-link'><strong>".$partner."</strong></a>";
			}
		} else if ($site) {
			if (!$data) {
				if (!$this->session->userdata('site_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Sites</strong></a>";
				} else {
					$site = $this->template_model->get_site_name($this->session->userdata('site_filter'));
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$site."</strong></a>";
				}
			} else {
				$site = $this->template_model->get_site_name($data);
				echo "<a href='javascript:void(0)' class='alert-link'><strong>".$site."</strong></a>";
			}
			
		} else if ($sub_county) {
			if (!$data) {
				if (!$this->session->userdata('site_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Sub-Counties</strong></a>";
				} else {
					$sub_county = $this->template_model->get_sub_county_name($this->session->userdata('site_filter'));
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$sub_county."</strong></a>";
				}
			} else {
				$sub_county = $this->template_model->get_sub_county_name($data);
				echo "<a href='javascript:void(0)' class='alert-link'><strong>".$sub_county."</strong></a>";
			}
			
		} else if ($age) {
			if (!$data) {
				if (!$this->session->userdata('age_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Ages</strong></a>";
				} else {
					$age_name = $this->template_model->get_age_name($this->session->userdata('age_filter'));
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$age_name."</strong></a>";
				}
			} else {
				$age_name = $this->template_model->get_age_name($data);
				echo "<a href='javascript:void(0)' class='alert-link'><strong>".$age_name."</strong></a>";
			}

			/*if ($data == 1 || $data == '1') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>No Data</strong></a>";
			} elseif($data == 2 || $data == '2') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>Less than 2 Weeks</strong></a>";
			} elseif($data == 3 || $data == '3') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>2 - 6 Weeks</strong></a>";
			} elseif($data == 4 || $data == '4') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>6 - 8 Weeks</strong></a>";
			} elseif($data == 5 || $data == '5') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>6 Months</strong></a>";
			} elseif($data == 6 || $data == '6') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>9 Months</strong></a>";
			} elseif($data == 7 || $data == '7') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>12 Months</strong></a>";
			} elseif($data == 8 || $data == '8') {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>All Age Groups</strong></a>";
			} else {
				echo "<a href='javascript:void(0)' class='alert-link'><strong>All Age Groups</strong></a>";
			}*/
			
		} else if ($regimen) {
			// echo "Regimen Found";
			if (!$data) {
				if (!$this->session->userdata('regimen_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Regimen</strong></a>";
				} else {
					$regimen = $this->template_model->get_regimen_name($this->session->userdata('regimen_filter'));
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$regimen."</strong></a>";
				}
				
			} else {
				$regimen = $this->template_model->get_regimen_name($data);
				echo "<a href='javascript:void(0)' class='alert-link'><strong>".$regimen."</strong></a>";
			}
			
		} else if($type) {
			if ($type == 5 || $type == '5') {
				if ($data == null || $data == 'null' || $data == 'NA') {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>All Funding Agencies</strong></a>";
				} else {
					$agency = $this->template_model->get_funding_agency($data);
					echo "<a href='javascript:void(0)' class='alert-link'><strong>".$agency."</strong></a>";
				}
			}
		}  else {
			if (!$data) {
				if (!$this->session->userdata('county_filter')) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>Kenya</strong></a>";
				} else {
					$county = $this->template_model->get_county_name($this->session->userdata('county_filter'));
					echo "Kenya / <a href='javascript:void(0)' class='alert-link'><strong>".$county."</strong></a>";
				}
			} else {
				if ($data == '48' || $data == 48) {
					echo "<a href='javascript:void(0)' class='alert-link'><strong>Kenya</strong></a>";
				} else {
					$county = $this->template_model->get_county_name($data);
					echo "Kenya / <a href='javascript:void(0)' class='alert-link'><strong>".$county."</strong></a>";
				}
			}
		}
	}

	function dates()
	{
		$data = array(
					'prev_year' => ($this->session->userdata('filter_year')-1),
					'year' => $this->session->userdata('filter_year'),
					'month' => $this->session->userdata('filter_month'));
		echo json_encode($data);
	}


	function get_site_details($id)
	{
		$this->load->model('template_model');
		echo json_encode($this->template_model->get_site_details($id));
	}
}
?>