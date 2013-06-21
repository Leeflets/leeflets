<?php
namespace Leeflets;

class String {
    // Why is this email validation so simplistic? Why not use a regex?
    // Because you can't actually validate email with a regex (http://stackoverflow.com/a/201378/112832)
    // Good email validation is extremely complex and even then is often erroneous
    static function valid_email( $string ) {
        return false !== strpos( $string, '@' );
    }

    static function camelize( $str ) {
        $str[0] = strtoupper( $str[0] );
        $func = create_function( '$c', 'return "_" . strtoupper($c[1]);' );
        return preg_replace_callback( '/[_|-]([a-z])/', $func, $str );
    }

    static function decamelize( $str ) {
        $str[0] = strtolower( $str[0] );
        $func = create_function( '$c', 'return "-" . strtolower($c[1]);' );
        return preg_replace_callback( '/_([A-Z])/', $func, $str );
    }

    /**
     * Parses a string representing an array (typically used in form field names) 
     * and returns an array containing the keys.
     *
     * e.g. page-meta[site][title] becomes array( 'page-meta', 'site', 'title' )
     *
     * @since 0.1
     *
     * @param string $rep String representing an array
     * @return array PHP array of keys
     */
    static function parse_array_representation( $rep ) {
        // No square brackets, not an array representation
        if ( !preg_match( '@^([^\[]+)@', $rep, $matches ) ) {
            return false;
        }

        $keys = array();

        $keys[] = $matches[1];

        if ( !preg_match_all( '@\[([^\]]+)\]@', $rep, $matches ) ) {
            return false;
        }

        return array_merge( $keys, $matches[1] );
    }

    /**
     * Converts a string representing an array (typically used in form field names)
     * to a PHP array with the same hierchichal structure. Optionally set a value.
     *
     * e.g. page-meta[title] becomes array( 'page-meta' => array( 'title' => '' ) ) 
     *
     * @since 0.1
     *
     * @param string $rep String representing an array
     * @param string $value (optional) Value to set the array variable
     * @return array PHP array of the string representation
     */
    static function convert_representation_to_array( $rep, $value = '' ) {
        $keys = self::parse_array_representation( $rep );
        $keys = array_reverse( $keys );

        foreach ( $keys as $key ) {
            $result = array( $key => $value );
            $value = $result;
        }

        return $value;
    }

    static function json_prettify( $json ) {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen( $json );
        $indentStr   = '    ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ( $i=0; $i<=$strLen; $i++ ) {

            // Grab the next character in the string.
            $char = substr( $json, $i, 1 );

            // Are we inside a quoted string?
            if ( $char == '"' && $prevChar != '\\' ) {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if ( ( $char == '}' || $char == ']' ) && $outOfQuotes ) {
                    $result .= $newLine;
                    $pos --;
                    for ( $j=0; $j<$pos; $j++ ) {
                        $result .= $indentStr;
                    }
                }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if ( ( $char == ',' || $char == '{' || $char == '[' ) && $outOfQuotes ) {
                $result .= $newLine;
                if ( $char == '{' || $char == '[' ) {
                    $pos ++;
                }

                for ( $j = 0; $j < $pos; $j++ ) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    /**
     * Retrieve a modified URL query string.
     *
     * You can rebuild the URL and append a new query variable to the URL query by
     * using this function. You can also retrieve the full URL with query data.
     *
     * Adding a single key & value or an associative array. Setting a key value to
     * an empty string removes the key. Omitting oldquery_or_uri uses the $_SERVER
     * value. Additional values provided are expected to be encoded appropriately
     * with urlencode() or rawurlencode().
     *
     * @since 0.1
     *
     * @param mixed   $param1 Either newkey or an associative_array
     * @param mixed   $param2 Either newvalue or oldquery or uri
     * @param mixed   $param3 Optional. Old query or uri
     * @return string New URL query string.
     */
    function add_query_arg() {
        $ret = '';
        $args = func_get_args();
        if ( is_array( $args[0] ) ) {
            if ( count( $args ) < 2 || false === $args[1] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[1];
        } else {
            if ( count( $args ) < 3 || false === $args[2] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[2];
        }

        if ( $frag = strstr( $uri, '#' ) )
            $uri = substr( $uri, 0, -strlen( $frag ) );
        else
            $frag = '';

        if ( 0 === stripos( 'http://', $uri ) ) {
            $protocol = 'http://';
            $uri = substr( $uri, 7 );
        } elseif ( 0 === stripos( 'https://', $uri ) ) {
            $protocol = 'https://';
            $uri = substr( $uri, 8 );
        } else {
            $protocol = '';
        }

        if ( strpos( $uri, '?' ) !== false ) {
            $parts = explode( '?', $uri, 2 );
            if ( 1 == count( $parts ) ) {
                $base = '?';
                $query = $parts[0];
            } else {
                $base = $parts[0] . '?';
                $query = $parts[1];
            }
        } elseif ( $protocol || strpos( $uri, '=' ) === false ) {
            $base = $uri . '?';
            $query = '';
        } else {
            $base = '';
            $query = $uri;
        }

        parse_str( $query, $qs );
        $qs = self::urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
        if ( is_array( $args[0] ) ) {
            $kayvees = $args[0];
            $qs = array_merge( $qs, $kayvees );
        } else {
            $qs[ $args[0] ] = $args[1];
        }

        foreach ( $qs as $k => $v ) {
            if ( $v === false )
                unset( $qs[$k] );
        }

        $ret = http_build_query( $qs );
        $ret = trim( $ret, '?' );
        $ret = preg_replace( '#=(&|$)#', '$1', $ret );
        $ret = $protocol . $base . $ret . $frag;
        $ret = rtrim( $ret, '?' );
        return $ret;
    }

    /**
     * Removes an item or list from the query string.
     *
     * @since 0.1
     *
     * @param string|array $key   Query key or keys to remove.
     * @param bool    $query When false uses the $_SERVER value.
     * @return string New URL query string.
     */
    function remove_query_arg( $key, $query=false ) {
        if ( is_array( $key ) ) { // removing multiple keys
            foreach ( $key as $k )
                $query = add_query_arg( $k, false, $query );
            return $query;
        }
        return add_query_arg( $key, false, $query );
    }

    /**
     * Navigates through an array and encodes the values to be used in a URL.
     *
     *
     * @since 0.1
     *
     * @param array|string $value The array or string to be encoded.
     * @return array|string $value The encoded array (or string from the callback).
     */
    function urlencode_deep( $value ) {
        $value = is_array( $value ) ? array_map( '\Leeflets\String::urlencode_deep', $value ) : urlencode( $value );
        return $value;
    }
}
