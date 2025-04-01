<?php

namespace App\Services;

class WebSettings
{
    protected $settings = null;

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}