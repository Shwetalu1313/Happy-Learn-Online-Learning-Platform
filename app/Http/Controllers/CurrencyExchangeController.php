<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\CurrencyExchange;
use App\Models\SystemActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyExchangeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     *
     * show update form with data
     */
    public function edit() {
        $titlePage = __('nav.currency_ex');
        $exchange = CurrencyExchange::first();
        return view('currency.exchangeUpdate', compact('titlePage', 'exchange'));
    }

    public function updateUSDollar(Request $request){
        $data = $request->validate([
            'us_ex' => 'required|integer'
        ]);
        $exchange = CurrencyExchange::first();
        $exchange->us_ex = $data['us_ex'];
        if($exchange->save()) {
            $systemActivity = [
                'table_name' => CurrencyExchange::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'US Dollar  ' . $data['us_ex'],
                'about' => 'Currency was changed. US>>'. $data['us_ex'] .' by '. Auth::user()->name,
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);
            return redirect()->back()->with('success', 'You updated United State (US) Dollar exchange rate.');
        }
        else return redirect()->back()->with('success', 'Fail to change.');
    }

    public function updatePts(Request $request){
        $data = $request->validate([
            'pts_ex' => 'required|integer'
        ]);
        $exchange = CurrencyExchange::first();
        $exchange->pts_ex = $data['pts_ex']; // Update pts_ex instead of us_ex
        if($exchange->save()) {
            $systemActivity = [
                'table_name' => CurrencyExchange::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'Points  ' . $data['pts_ex'],
                'about' => 'Currency was changed. Points>>'. $data['pts_ex'] .' by '. Auth::user()->name,
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);
            return redirect()->back()->with('success','You updated \'Point\' exchange rate.');
        }
        else return redirect()->back()->with('success', 'Fail to change.');
    }
}
