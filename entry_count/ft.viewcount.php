<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Viewcount_ft extends EE_Fieldtype
{
    public $info = array(
        'name'      => 'Viewcount',
        'version'   => '1.0.0',
    );

    public function install()
    {
        return [];
    }

    public function display_global_settings()
    {
        $val = array_merge($this->settings, $_POST);

        $form = '';

        return $form;
    }

    public function save_global_settings()
    {
        return array_merge($this->settings, $_POST);
    }

    public function display_settings($data)
    {
    }

    public function save_settings($data)
    {
        return [];
    }

    public function display_field($data)
    {
        return $this->getEntryCount();
    }

    public function replace_tag($data, $params = array(), $tagdata = false)
    {
        return $this->getEntryCount();
    }

    private function getEntryCount()
    {
        if (!$this->content_id) {
            return 0;
        }

        $entry = ee('Model')->get('ChannelEntry')->filter('entry_id', $this->content_id)->first();

        if (!$entry) {
            return 0;
        }

        return $entry->view_count_one ?? 0;
    }
}
