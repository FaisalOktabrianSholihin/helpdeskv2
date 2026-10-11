<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const SECTIONS = ['company', 'email', 'categories', 'statuses', 'backup', 'general'];

    public function show(Request $request)
    {
        abort_unless($request->user()->hasPermission('setting', 'view'), 403);

        $s = Setting::current();

        return response()->json(['ok' => true, 'settings' => [
            'company' => $s->company,
            'email' => $s->email,
            'categories' => $s->categories,
            'statuses' => $s->statuses,
            'backup' => $s->backup,
            'general' => $s->general,
        ]]);
    }

    /** Update satu section, mis. PUT /api/settings/company. */
    public function update(Request $request, string $section)
    {
        abort_unless($request->user()->hasPermission('setting', 'edit'), 403);

        if (! in_array($section, self::SECTIONS, true)) {
            return response()->json(['ok' => false, 'message' => 'Section setting tidak dikenal.'], 422);
        }

        $data = $request->validate(['value' => ['required']]);

        $s = Setting::current();
        $s->{$section} = $data['value'];
        $s->save();

        return response()->json(['ok' => true, 'settings' => [
            'company' => $s->company, 'email' => $s->email, 'categories' => $s->categories,
            'statuses' => $s->statuses, 'backup' => $s->backup, 'general' => $s->general,
        ]]);
    }
}
