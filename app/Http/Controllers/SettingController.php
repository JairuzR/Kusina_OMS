<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings'          => 'required|array',
            'settings.*.key'    => 'required|string',
            'settings.*.value'  => 'nullable',
        ]);

        foreach ($request->input('settings') as $item) {
            Setting::where('key', $item['key'])->update(['value' => $item['value'] ?? '']);
        }

        AuditLog::record('updated', 'settings', description: 'Site settings updated');

        return back()->with('success', 'Settings saved successfully.');
    }
}