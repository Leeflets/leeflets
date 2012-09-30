<?php
class LF_Form_Fieldset extends LF_Form_Element {
    private $legend = '';
    private $elements = array();
    private $errors = array();
    
    function __construct( $legend, $fields, $args = array() ) {
        $args = wp_parse_args( $args, array(
            'encoding' => 'UTF-8',
            'prefix' => ''
        ));
        
        extract( $args );
        
        $this->encoding = $encoding;
        $this->prefix = $prefix;

        $this->fields( $fields );
    }
    
    function values( $values = null ) {
        if ( is_null( $values ) ) {
            foreach ( $this->fields() as $field ) {
                
            }
            $values = array();
            return $this->collection->values();
        }
        else {
            foreach ( $values as $id => $value ) {
                if ( isset( $this->fields[$id] ) ) {
                    $field = $this->fields[$id];
                    if ( $field instanceof LF_Form_Collection ) {
                        $field->values( $values );
                    }
                    else {
                        $field->value( $value );
                    }
                }
            }
        }
    }
    
    function field( $id ) {
        foreach ( $this->fields as $field ) {
            
        }
        
        if ( isset( $this->fields[$id] ) ) {
            $field = $this->fields[$id];
            if ( $field instanceof LF_Form_Collection ) {
                $field->values( $values );
            }
            else {
                $field->value( $value );
            }
        }
    }
    
    function fields( $fields = null ) {
        if ( is_null( $fields ) ) {
            return $this->fields;
        }
        else {
            foreach ( $fields as $id => $field ) {
                if ( !isset( $field['type'] ) ) {
                    $field['type'] = 'text';
                }
                
                if ( class_exists( 'Base_Ext_Field_' . ucfirst( $field['type'] ) ) ) {
                    $class_name = 'Base_Ext_Field_' . ucfirst( $field['type'] );
                }
                elseif ( class_exists( 'LF_Form_' . ucfirst( $field['type'] ) ) ) {
                    $class_name = 'LF_Form_' . ucfirst( $field['type'] );
                }
                else {
                    throw new Exception( 'Could not find class for field type ' . $field['type'] . '.' );
                }
                
                $this->fields[$id] = new $class_name( Base_Form::compact( $this, 'prefix', 'encoding' ) );
            }
        }
    }
    
    function validate() {
        $this->errors = array();
        
        foreach ($this->fields as $field) {
            $this->errors = array_merge($this->errors, $field->validate());
        }
        
        return $this->errors;
    }

    function table($print = false) {
        if (!$print) ob_start();
        ?>

        <?php if ($this->title) : ?>
        <tr id="<?php echo $this->id ?>"<?php echo ($this->css) ? ' class="' . $this->css . '"' : ''; ?>>
            <th colspan="2" style="text-align: left;"><?php echo $this->title ?></th>
        </tr>
        <?php endif; ?>

        <?php
        foreach ($this->fields as $field) {
            $field->table(true);
        }
        ?>
        
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function display_table() {
        $this->html(true);
    }    
        
    function html($print = false) {
        if (!$print) ob_start();
        ?>

        <fieldset id="<?php echo $this->id ?>"<?php echo ($this->css) ? ' class="' . $this->css . '"' : ''; ?>>
            <?php if ($this->title) : ?>
            <legend><?php echo $this->title ?></legend>
            <?php endif; ?>
            
            <?php
            foreach ($this->fields as $field) {
                $field->display();
            }
            ?>
            
        </fieldset>
        
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function display() {
        $this->html(true);
    }
}
