<?php

namespace Leeflets\Core;

class Response implements ResponseInterface {

    /**
     * @var string
     */
    private $content;

    /**
     * Response constructor.
     *
     * @param string $content
     */
    public function __construct($content) {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function output() {
        return $this->content;
    }

    public function headers() {
        return [];
    }
}