<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
{
    Setting::set('site_name', 'Sistema de Votación');
    Setting::set('primary_color', '#0d47a1');
    Setting::set('secondary_color', '#1565c0');

    Setting::set('banner', null);
    Setting::set('logo', null);

    Setting::set('pdf_title', 'Resultados Oficiales');
    Setting::set('pdf_footer', 'Sistema Institucional de Votación');
}
}
