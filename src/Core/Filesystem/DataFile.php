<?php

namespace Leeflets\Core\Library;

class DataFile {

    public $filepath, $config;

    public function __construct($filepath, Config $config) {
        $this->config = $config;
        $this->filepath = $filepath;
    }

    /**
     * @param array $data
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    function write($data, $filesystem) {
        $filesystem->connect();
        $path = $filesystem->translate_path($this->filepath);
        $out = "<?php exit; // No public access. ?>\n";
        $out .= String::json_prettify(json_encode($data));
        return $filesystem->put_contents($path, $out);
    }

    function read() {
        if (!file_exists($this->filepath)) {
            return false;
        }

        $json = file_get_contents($this->filepath);
        $after_first_line = strpos($json, "\n") + 1;
        $json = substr($json, $after_first_line);

        $data = json_decode($json, true);

        if (is_null($data)) {
            return false;
        }

        return $data;
    }

}