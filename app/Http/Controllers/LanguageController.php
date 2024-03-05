<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function languageSwitch(Request $request){
            // get the language from the form
            $language = $request->input('language');

            // store in the session
            session(['language' => $language]);
            return redirect()->back()->with(['language_switched' => $language]);
    }
}
