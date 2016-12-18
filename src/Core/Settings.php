<?php

namespace Leeflets\Core\Library;

class Settings extends DataFile {

    private $data;

    public function __construct(Config $config) {
        parent::__construct($config->data_path . '/settings.json.php', $config);
        $this->load();
    }

    public function load() {
        $this->data = $this->read();
        if (!$this->data) {
            $this->data = [];
        }

        // Defaults
        if (!isset($this->data['template']['active'])) {
            $this->data['template']['active'] = 'koala';
        }

        if (!isset($this->data['analytics']['placement'])) {
            $this->data['analytics']['placement'] = 'head';
        }
    }

    public function get_data() {
        return $this->data;
    }

    public function out() {
        echo $this->vget(func_get_args());
    }

    public function get() {
        return $this->vget(func_get_args());
    }

    public function vget($keys) {
        $settings = $this->data;

        foreach ($keys as $key) {
            if (!isset($settings[$key])) {
                return '';
            }

            $settings = $settings[$key];
        }

        return $settings;
    }

    public function save_connection_info($data, $filesystem) {
        $settings = $this->read();

        $fields = ['type', 'hostname', 'username', 'password'];
        foreach ($fields as $field) {
            $settings['connection'][$field] = $data['connection'][$field];
        }

        $this->write($settings, $filesystem);
    }

    public function get_template_about($template = '') {
        if (!$template) {
            $template = $this->get('template', 'active');
        }

        $path = $this->config->templates_path . '/' . $template;
        return $this->get_product_about($path);
    }

    public function get_addon_about($addon) {
        $path = $this->config->addons_path . '/' . $addon;
        return $this->get_product_about($path);
    }

    public function get_product_about($path = '') {

        $path .= '/meta-about.php';

        if (!file_exists($path)) {
            return false;
        }

        $variables = Inc::variables($path, ['about']);
        if (is_array($variables)) {
            extract($variables);
        }

        if (!isset($about['name']) || !isset($about['version'])) {
            return false;
        }

        $default_about = [
            'name' => '',
            'version' => '',
            'description' => '',
            'screenshot' => '',
            'author' => [
                'name' => '',
                'url' => ''
            ],
            'changelog' => []
        ];

        // Add default array keys to avoid having to check if
        // indexes exist and array index errors
        $about = array_merge($default_about, $about);

        return $about;
    }
}
