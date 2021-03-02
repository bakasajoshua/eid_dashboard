<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::post('facility/search', 'FilterController@facility')->name('facility.search');

Route::prefix('filter')->name('filter.')->group(function(){
	Route::post('date', 'FilterController@filter_date')->name('date');
	Route::post('any', 'FilterController@filter_any')->name('any');
});

Route::middleware(['clear_session', ])->group(function(){
	Route::get('/', 'PagesController@summary')->name('summary');
	Route::get('summary/heivalidation', 'PagesController@heivalidation')->name('heivalidation');
	Route::get('county', 'PagesController@county')->name('county');
	Route::get('county/tat', 'PagesController@county_tat')->name('county_tat');
	Route::get('subcounty', 'PagesController@subCounty')->name('subCounty');
	Route::get('subcounty/tat', 'PagesController@subCounty_tat')->name('subCounty_tat');
	Route::get('facility', 'PagesController@facility')->name('facility');
	Route::get('labPerformance', 'PagesController@lab')->name('lab');
	Route::get('labPerformance/poc', 'PagesController@poc')->name('poc');

	Route::get('partner/agencies', 'PagesController@agency')->name('agency');
	Route::get('partner', 'PagesController@partner')->name('partner');
	Route::get('partner/sites', 'PagesController@partner_sites')->name('partner_sites');
	Route::get('partner/counties', 'PagesController@partner_counties')->name('partner_counties');
	Route::get('partner/heivalidation', 'PagesController@heivalidation')->name('heivalidation');
	Route::get('partner/tat', 'PagesController@partner_tat')->name('partner_tat');

	Route::get('positivity', 'PagesController@positivity')->name('positivity');
	Route::get('age', 'PagesController@age')->name('age');
	Route::get('regimen', 'PagesController@regimen')->name('regimen');

	Route::get('trends', 'PagesController@trends')->name('trends');
	Route::get('partner/trends', 'PagesController@partner_trends')->name('partner_trends');

});


Route::prefix('summary')->name('summary.')->group(function(){
	Route::get('turnaroundtime', 'SummaryController@turnaroundtime')->name('turnaroundtime');
	Route::get('test_trends/{type?}', 'SummaryController@test_trends')->name('test_trends');
	Route::get('eid_outcomes', 'SummaryController@eid_outcomes')->name('eid_outcomes');
	Route::get('hei_validation', 'SummaryController@hei_validation')->name('hei_validation');
	Route::get('hei_follow', 'SummaryController@hei_follow')->name('hei_follow');
	Route::get('age', 'SummaryController@age')->name('age');
	Route::get('age2/{stacking_percent?}', 'SummaryController@age2')->name('age2');
	Route::get('dynamic_detailed/{field}/{stacking_percent?}', 'SummaryController@dynamic_detailed')->name('dynamic_detailed');
	Route::get('dynamic_outcomes/{level}/{level2?}/{stacking_percent?}', 'SummaryController@dynamic_outcomes')->name('dynamic_outcomes');
	Route::get('hei/validation/{type?}', 'HeiController@validation')->name('hei');
});

Route::prefix('county')->name('county.')->group(function(){
	Route::get('county_outcomes/{var?}', 'CountyController@county_outcomes')->name('county_outcomes');
	Route::get('counties_details/{var?}', 'CountyController@counties_details')->name('counties_details');

	Route::get('subcounty/{var}', 'CountyController@county_subcounty')->name('county_subcounty');
	Route::get('details/{var?}/{var2?}', 'CountyController@countyDetails')->name('county_details');
	Route::get('test_analysis/{type?}', 'CountyController@test_analysis')->name('test_analysis');
	Route::get('test_analysis_trends', 'CountyController@test_analysis_trends')->name('test_analysis_trends');
});

Route::prefix('subcounty')->name('subcounty.')->group(function(){
	Route::get('tat_outcomes/{type}', 'TatController@tat_outcomes')->name('tat_outcomes');
	Route::get('tat_details/{type}', 'TatController@tat_details')->name('tat_details');

	Route::get('subcounties_positivity/{var}', 'CountyController@county_outcomes')->name('subcounties_positivity');
	Route::get('subcounties_outcomes/{var}', 'CountyController@counties_details')->name('subcounties_outcomes');

	Route::get('subcounties_eid', 'SummaryController@eid_outcomes')->name('subcounties_eid');
});

Route::prefix('facility')->name('facility.')->group(function(){
	Route::get('unsupported_sites', 'FacilityController@unsupported_sites')->name('unsupported_sites');
	Route::get('get_trends', 'FacilityController@get_trends')->name('get_trends');
	Route::get('get_positivity', 'FacilityController@get_positivity')->name('get_positivity');
});

Route::prefix('lab')->name('lab.')->group(function(){
	Route::get('lab_performance_stat', 'LabController@lab_performance_stat')->name('lab_performance_stat');
	Route::get('lab_testing_trends/{trend}', 'LabController@lab_testing_trends')->name('lab_testing_trends');
	Route::get('labs_turnaround', 'LabController@labs_turnaround')->name('labs_turnaround');
	Route::get('rejections', 'LabController@rejections')->name('rejections');
	Route::get('mapping', 'LabController@mapping')->name('mapping');

	// POC Routes
	Route::get('poc_performance_stat', 'LabController@poc_performance_stat')->name('poc_performance_stat');
	Route::get('poc_performance_details/{lab_id}', 'LabController@poc_performance_details')->name('poc_performance_details');
	Route::get('poc_outcomes', 'LabController@poc_outcomes')->name('poc_outcomes');

});

Route::prefix('poc')->name('poc.')->group(function(){
	Route::get('testing_trends', 'PocController@testing_trends')->name('testing_trends');
	Route::get('eid_outcomes', 'PocController@eid_outcomes')->name('eid_outcomes');
	Route::get('entrypoints', 'PocController@entrypoints')->name('entrypoints');
	Route::get('ages', 'PocController@ages')->name('ages');
	Route::get('county_outcomes', 'PocController@county_outcomes')->name('county_outcomes');
});

Route::prefix('trends')->name('trends.')->group(function(){
	Route::get('positive_trends', 'TrendsController@yearly_trends')->name('yearly_trends');
	Route::get('summary', 'TrendsController@yearly_summary')->name('yearly_summary');
	Route::get('quarterly', 'TrendsController@quarterly_trends')->name('quarterly_trends');
	Route::get('alltests_q', 'TrendsController@alltests')->name('alltests');
	Route::get('repeat_q', 'TrendsController@rtests')->name('rtests');
	Route::get('infants_q', 'TrendsController@infant_tests')->name('infant_tests');
	Route::get('less2m_q', 'TrendsController@ages_2m_quarterly')->name('ages_2m_quarterly');
	Route::get('quarterly_outcomes', 'TrendsController@quarterly_outcomes')->name('quarterly_outcomes');
});

Route::prefix('tat')->name('tat.')->group(function(){
	Route::get('tat_outcomes/{type?}', 'TatController@tat_outcomes')->name('tat_outcomes');
	Route::get('tat_details/{type?}', 'TatController@tat_details')->name('tat_details');
});

Route::prefix('positivity')->name('positivity.')->group(function(){
	Route::get('notification_bar', 'PositivityController@notification_bar')->name('notification_bar');
	Route::get('listings/{level}', 'PositivityController@listings')->name('listings');
	Route::get('age_listings/{level}', 'PositivityController@age_listings')->name('age_listings');
	Route::get('regimen_listings/{level}', 'PositivityController@regimen_listings')->name('regimen_listings');
});

Route::prefix('age')->name('age.')->group(function(){
	Route::get('ages_outcomes', 'AgeController@ages_outcomes')->name('ages_outcomes');
	Route::get('testing_trends', 'AgeController@testing_trends')->name('testing_trends');
	Route::get('get_counties_agebreakdown', 'AgeController@get_counties_agebreakdown')->name('get_counties_agebreakdown');
});

Route::prefix('regimen')->name('regimen.')->group(function(){
	Route::get('regimen_outcomes', 'RegimenController@regimen_outcomes')->name('regimen_outcomes');
	Route::get('testing_trends', 'RegimenController@testing_trends')->name('testing_trends');
	Route::get('get_counties_breakdown', 'RegimenController@get_counties_breakdown')->name('get_counties_breakdown');
});
