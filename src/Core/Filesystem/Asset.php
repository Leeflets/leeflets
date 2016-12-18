<?php
/**
 * Created by PhpStorm.
 * User: squad
 * Date: 11/11/2016
 * Time: 22:47
 */
namespace Leeflets\Core\Library;

class Asset
{
    var $handle;
    var $src;
    var $deps = array();
    var $ver = false;
    var $args = null;

    var $extra = array();

    function __construct()
    {
        @list($this->handle, $this->src, $this->deps, $this->ver, $this->args) = func_get_args();
        if (!is_array($this->deps))
            $this->deps = array();
    }

    function add_data($name, $data)
    {
        if (!is_scalar($name))
            return false;
        $this->extra[$name] = $data;
        return true;
    }
}