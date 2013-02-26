<?php
class LF_Form_Control extends LF_Form_Element {
    public $required, $required_msg, $validation, $name, $label,
        $tip, $errors, $value, $class, $pattern, $pattern_msg, 
        $column_width;
    public $has_multiple_values = false;
    protected $form;
    
    function __construct( $parent, $id, $args = array() ) {
        $this->id = $id;
        $this->parent = $parent;
        $this->errors = array();
        $this->form = $this->get_form();

        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $this->class = trim( $id . ' ' . $args['class'] );

        if ( !isset( $args['name'] ) ) {
            $args['name'] = $id;
        }

        $args['name'] = $this->parent->id . '[' . $args['name'] . ']';

        if ( $this->has_multiple_values ) {
            $args['name'] .= '[]';
        }

        if ( isset( $args['required'] ) && is_string( $args['required'] ) ) {
            $this->required_msg = $args['required'];
            $args['required'] = true;
        }

        if ( isset( $args['pattern'][1] ) ) {
            $this->pattern_msg = $args['pattern'][1];
            $args['pattern'] = $args['pattern'][0];
        }

        // Set class variables and remove from $args array
        $this->special_args( 'validation, label, tip, value, column-width, repeatable', $args );

        // Set class variables, but keep them in $args array
        $this->special_args( 'name, required, pattern', $args, false );

        // Set control attributes to variables leftover in $args
        $this->atts = $args;

        $this->load_post_value();
    }

    function load_post_value() {
        $this->value = $this->get_value_from_array( $_POST );

        if ( is_null( $this->value ) ) {
            $this->value = '';
        }
    }

    function get_value_from_array( $array ) {
        return parent::get_value_from_array( $this->name, $array );
    }

    function set_value_from_array( $array ) {
        $value = $this->get_value_from_array( $array );

        if ( is_null( $value ) ) {
            return false;
        }
        else {
            $this->value = $value;
            return true;
        }
    }

    function pre_validate() {}
    function post_validate() {}

    function validate() {
        $this->errors = array();

        $this->pre_validate();
        
        if ( $this->required && $this->value == '' ) {
            if ( !is_null( $this->required_msg ) ) {
                $this->errors[] = $this->required_msg;
            }
            else {
                $this->errors[] = 'Please fill in this field.';
            }
            return $this->errors;
        }

        if ( !is_null( $this->pattern ) && !preg_match( $this->pattern, $this->value ) ) {
            if ( !is_null( $this->pattern_msg ) ) {
                $this->errors[] = $this->pattern_msg;
            }
            else {
                $this->errors[] = 'Invalid entry.';
            }
        }

        if ( $this->value != '' && !empty( $this->validation ) ) {
            foreach ( $this->validation as $validation ) {
                $this->call_validation_func( $validation );
            }
        }

        $this->post_validate();
        
        return $this->errors;
    }
    
    function call_validation_func( $validation ) {
        extract( $validation );
        
        if ( !isset( $callback ) ) return;

        if ( isset( $args ) ) {
            array_unshift( $args, $this->value );
        }
        else {
            $args = array( $this->value );
        }

        if ( is_string( $callback ) && is_callable( array( $this, 'validate_' . $callback ) ) ) {
            $callback = array( $this, 'validate_' . $callback );
        }

        if ( !call_user_func_array( $callback, $args ) ) {
            if ( isset( $msg ) ) {
                $this->errors[] = $msg;
            }
            else {
                $this->errors[] = 'Invalid entry.';
            }
        }
    }

    function style_att() {
        $styles = array();
        if ( !is_null( $this->column_width ) ) {
            $styles[] = 'width: ' . $this->column_width . ';';
        }

        if ( $styles ) {
            return 'style="' . implode( ' ', $styles ) . '"';
        }

        return '';
    }

    function html_start() {
        ?>

        <div class="control-group <?php echo $this->class; if ( !empty( $this->errors ) ) echo ' error'; ?>" <?php echo $this->style_att(); ?>>
            <?php if ( $this->label != '' ) : ?>
            <label><?php echo $this->label; echo ($this->required) ? '<span class="req">*</span>' : '' ?></label>
            <?php endif; ?>
        
        <?php
    }

    function html() {
        $this->html_start();
        $this->html_middle();
        $this->html_end();
    }

    function html_end() {
        ?>

            <?php $this->errors_html(); $this->tip_html(); ?>
        </div>

        <?php
    }
    
    function tip_html() {
        if ( !$this->tip ) return;
        echo '<p class="help-block">', $this->tip, '</p>';
    }
    
    function errors_html() {
        if ( empty( $this->errors ) )
            return '';
        
        foreach ( $this->errors as $error ) {
            ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?php echo $error; ?>
            </div>
            <?php
        }
    }

    function value_att() {
        return $this->esc_att( $this->value );
    }

    function value_html() {
        return $this->esc_html( $this->value );
    }

    function slug($str) {
       return preg_replace('@[^a-z0-9-]+@', '-', strtolower($str));
    }

    function validate_min_length( $value, $length ) {
        return ( strlen( $value ) >= $length );
    }

    function validate_max_length( $value, $length ) {
        return ( strlen( $value ) <= $length );
    }
}
