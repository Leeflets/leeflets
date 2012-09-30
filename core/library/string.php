<?php
class LF_String {
    static function camelize($str) {
        $str[0] = strtoupper($str[0]);
        $func = create_function('$c', 'return "_" . strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
    
    static function decamelize($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "-" . strtolower($c[1]);');
        return preg_replace_callback('/_([A-Z])/', $func, $str);
    }

    static function json_prettify( $json ) {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '    ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            
            // If this character is the end of an element, 
            // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }
            
            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element, 
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }
                
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            
            $prevChar = $char;
        }

        return $result;
    }
}
