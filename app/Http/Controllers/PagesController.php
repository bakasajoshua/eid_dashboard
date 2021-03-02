<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lookup;
use Str;

class PagesController extends Controller
{

	public function summary()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.summary', $data);
	}

	public function heivalidation()
	{
		$url = url()->current();
		if(Str::contains($url, 'partner')){
			$data = Lookup::get_dropdown('Partner');
			$data['type'] = 1;
		}
		else{
			$data = Lookup::get_dropdown('County');
			$data['type'] = 0;
		}		
		return view('base.hei_validation', $data);
	}

	public function county()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.county', $data);
	}

	public function county_tat()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.county_tat', $data);
	}

	public function subCounty_tat()
	{
		$data = Lookup::get_dropdown('Sub County');
		return view('base.subcounty_tat', $data);
	}

	public function subCounty()
	{
		$data = Lookup::get_dropdown('Sub County');
		return view('base.subcounty', $data);
	}

	public function facility()
	{
		$data = Lookup::get_dropdown('Facility');
		return view('base.facility', $data);
	}
	
	public function lab()
	{
		$data = Lookup::get_dropdown('Lab');
		return view('base.lab', $data);
	}
	
	public function poc()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.poc', $data);
	}
	
	public function trends()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.trends', $data);
	}
	
	public function partner_trends()
	{
		$data = Lookup::get_dropdown('Partner');
		return view('base.trends', $data);
	}


	public function agency()
	{
		$data = Lookup::get_dropdown('Funding Agency');
		return view('base.funding_agency', $data);
	}

	public function partner()
	{
		$data = Lookup::get_dropdown('Partner');
		return view('base.partner', $data);
	}

	public function partner_sites()
	{
		$data = Lookup::get_dropdown('Partner');
		return view('base.partner-sites', $data);
	}

	public function partner_counties()
	{
		$data = Lookup::get_dropdown('Partner');
		return view('base.partner-counties', $data);
	}

	public function partner_tat()
	{
		$data = Lookup::get_dropdown('Partner');
		return view('base.partner_tat', $data);
	}
	
	public function positivity()
	{
		$data = Lookup::get_dropdown('County');
		return view('base.positivity', $data);
	}
	
	public function age()
	{
		$data = Lookup::get_dropdown('Age');
		return view('base.age', $data);
	}
	
	public function regimen()
	{
		$data = Lookup::get_dropdown('Regimen');
		return view('base.regimen', $data);
	}
}