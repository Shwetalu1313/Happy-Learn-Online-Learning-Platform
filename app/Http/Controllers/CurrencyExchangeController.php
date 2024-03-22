<?php

namespace App\Http\Controllers;

use App\Models\CurrencyExchange;
use Illuminate\Http\Request;

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
        $exchange->save();
        return redirect()->back()->with('success','You updated United State (US) Dollar exchange rate.');
    }

    public function updatePts(Request $request){
        $data = $request->validate([
            'pts_ex' => 'required|integer'
        ]);
        $exchange = CurrencyExchange::first();
        $exchange->pts_ex = $data['pts_ex']; // Update pts_ex instead of us_ex
        $exchange->save();
        return redirect()->back()->with('success','You updated \'Point\' exchange rate.');
    }
}
