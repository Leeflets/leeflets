<?php

namespace Leeflets\Core;

interface ResponseInterface {

    public function output();

    public function headers();
}