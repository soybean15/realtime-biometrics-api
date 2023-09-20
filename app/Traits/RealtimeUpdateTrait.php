<?php

namespace App\Traits;

use App\Models\Setting;

trait RealtimeUpdateTrait
{
    protected $settings;

    protected function getSettings()
    {
        if (!$this->settings) {
            $this->settings = Setting::find(1);
        }

        return $this->settings;
    }

    public function liveUpdate()
    {
        $settings = $this->getSettings();
        return $settings->data['live_update'];
    }

    public function enableRealtimeUpdate()
    {
        $settings = $this->getSettings();
        $data= $settings->data;
        $data['live_update'] = true;
        $settings->data = $data;
        $settings->save();
    }

    public function disableRealtimeUpdate()
    {
        $settings = $this->getSettings();
        $data= $settings->data;
        $data['live_update'] = false;
        $settings->data = $data;
        $settings->save();;
    }
}
