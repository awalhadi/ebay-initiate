<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\SystemSettings;
use App\Http\Controllers\Controller;

class SystemSettingsController extends Controller
{
    public function editGeneral()
    {
        $settings = [];
        $raw_settings = SystemSettings::all();

        foreach ($raw_settings as $s){
            $settings[$s->settings_key] = $s->settings_value;
        }

        return view('system_settings.edit_general', compact('settings'));
    }

    public function editApi()
    {
        $settings = [];
        $raw_settings = SystemSettings::all();

        foreach ($raw_settings as $s){
            $settings[$s->settings_key] = $s->settings_value;
        }

        return view('admin.system_settings.edit_api', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        $keys = array_keys($data);

        foreach ($keys as $key){
            $settings = SystemSettings::where('settings_key', $key)->first();
            if(!$settings) $settings = new SystemSettings();

            $settings->settings_key = $key;
            $settings->settings_value = $data[$key];
            $settings->save();
        }

       return redirect()->back();
    }
}
