<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        Setting::set('site_name', $request->site_name);
        Setting::set('primary_color', $request->primary_color);
        Setting::set('secondary_color', $request->secondary_color);
        Setting::set('pdf_title', $request->pdf_title);
        Setting::set('pdf_footer', $request->pdf_footer);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');

            if ($oldLogo && !str_starts_with($oldLogo, 'data:image') && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        if ($request->hasFile('banner')) {
            $oldBanner = Setting::get('banner');

            if ($oldBanner && !str_starts_with($oldBanner, 'data:image') && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }

            $path = $request->file('banner')->store('settings', 'public');
            Setting::set('banner', $path);
        }

        if ($request->filled('firmas') && isset($request->firmas['nombre'], $request->firmas['cargo'])) {
            $firmas = [];

            foreach ($request->firmas['nombre'] as $i => $nombre) {
                $firmas[] = [
                    'nombre' => $nombre,
                    'cargo'  => $request->firmas['cargo'][$i] ?? null,
                ];
            }

            Setting::set('firmas', json_encode($firmas));
        }

        return back()->with('success', 'Configuración guardada correctamente');
    }
}