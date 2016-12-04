<?php

namespace Leeflets\Core\Library;

/**
 * Class Leeflets
 * @package Leeflets\Core\Library
 */
class Leeflets {

    private $config;

    /**
     * Leeflets constructor.
     *
     * @param string $basePath
     */
    public function __construct($basePath) {
        $this->check_magic_quotes();

        $config = $this->config = new Config($basePath);

        $is_config_loaded = $this->config->load();

        $this->setup_error_reporting();

        if (!$is_config_loaded) {
            $router = new Router($config, null, '/setup/install/');
        } else {
            $router = new Router($config);
        }
    }

    private function check_magic_quotes() {
        if (!get_magic_quotes_gpc()) {
            return;
        }

        $process = [&$_GET, &$_POST, &$_COOKIE, &$_REQUEST];
        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = &$process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
        unset($process);
    }

    private function setup_error_reporting() {
        if ($this->config->debug) {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

            if ($this->config->debug_display) {
                ini_set('display_errors', 1);
            } else {
                ini_set('display_errors', 0);
            }

            if ($this->config->debug_log) {
                ini_set('log_errors', 1);
                ini_set('error_log', LF_DEBUG_LOG);
            }
        } else {
            error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
        }

    }

    public function run() {

    }
}
