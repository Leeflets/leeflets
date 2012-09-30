<?php
class LF_Form extends LF_Form_Element {
    protected $id;
    protected $atts;
    
    function __construct( $id, $atts = array() ) {
        $this->id = $id;
        $this->atts = array_merge( array(
            'method' => 'POST',
            'action' => '',
            'novalidate' => 'true',
            'accept-charset' => 'UTF-8',
        ), $atts );
    }

    function values( $values = null ) {
        if ( is_null( $values ) ) {
            return $this->collection->values();
        }
        else {
            $this->collection->values( $values );
        }
    }
    
    function fields( $fields = null ) {
        if ( is_null( $fields ) ) {
            return $this->collection->fields();
        }
        else {
            $this->collection = new LF_Form_Collection( $fields, self::compact( $this, 'prefix', 'encoding' ) );
        }
    }
    
    function is_submitted() {
        return isset( $_POST[$this->id] );
    }

    function validate( $values = null ) {
        if ( !$values )
            $values = $_POST;
        
        $this->populate_values( $values );
        
        $this->errors = array();
        
        return $this->collection->validate();
    }
    
    static function compact() {
        $result = array();
        $args = func_get_args();
        $obj = array_shift( $args );
        foreach ( $args as $var ) {
            if ( isset( $obj->$var ) ) {
                $result[$var] = $this->$var;
            }
        }
        return $result;
    }
    
    static function extract( $obj, $vars ) {
        foreach ( $vars as $key => $val ) {
            $obj->$key = $val;
        }
    }
}
