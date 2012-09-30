<?php
class LF_Hook {
    
    static $hooks = array();
        
    static function add($tag, $func, $priority = 10, $num_args = 1) {
        self::$hooks[$tag][$priority][$func] = array('name' => $func, 'num_args' => $num_args);
        return true;
    }
    
    static function apply($tag, $string = '') {
        if (!isset(self::$hooks[$tag])) {
            return $string;
        }
        
        $args = func_get_args();
        
        foreach (self::$hooks[$tag] as $priority => $funcs) {
            foreach ($funcs as $func) {
                if (!is_null($func['name'])) {
                    $args[1] = $string;
                    $string = call_user_func_array($func['name'], array_slice($args, 1, (int) $func['num_args']));
                }
            }
        }
        
        return $string;
    }
    
    function remove($tag, $func, $priority = 10, $num_args = 1) {
        $return = isset(self::$hooks[$tag][$priority][$func]);
        
        unset(self::$hooks[$tag][$priority][$func]);
        
        return $return;
    }    
    
}
