<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{
    public string $site_name;
    public bool $maintenance_mode;
    public string $site_logo;
    public string $footer_text;
    public int $page_limit;

    public static function group(): string
    {
        return 'generalSetting';
    }
}