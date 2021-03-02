<?php

namespace App\Http\Middleware;

use Closure;

class ClearSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        session([
            'funding_agency_filter' => null,
            'partner_filter' => null,
            'county_filter' => null,
            'sub_county_filter' => null,
            'site_filter' => null,
            'age_filter' => null,
            'regimen_filter' => null,

            'filter_year' => date('Y'),
            'filter_month' => 0,
            'filter_to_year' => 0,
            'filter_to_month' => 0,
            // 'filter_type' => 1,
        ]);
        return $next($request);
    }
}
