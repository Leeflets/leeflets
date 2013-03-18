<?php
namespace Leeflets;

/*
 * Modified version of the jQuery File Upload Plugin PHP Class 6.1.1
 * https://github.com/blueimp/jQuery-File-Upload
 */

class Upload {
    protected $options;
    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'Exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'File type not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height'
    );

    private $config, $router, $settings;

    function __construct( LF_Config $config, LF_Router $router, LF_Settings $settings, $options = null ) {
        $this->config = $config;
        $this->router = $router;
        $this->settings = $settings;

        $this->options = array(
            'script_url' => $this->router->admin_url( '/content/upload/' ),
            'upload_dir' => $this->config->uploads_path . '/',
            'upload_url' => $this->router->admin_url( '/uploads/' ),
            'user_dirs' => false,
            'mkdir_mode' => 0755,
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            'access_control_allow_origin' => '*',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => array(
                'OPTIONS',
                'HEAD',
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE'
            ),
            'access_control_allow_headers' => array(
                'Content-Type',
                'Content-Range',
                'Content-Disposition'
            ),
            // Enable to provide file downloads via GET requests to the PHP script:
            'download_via_php' => false,
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => true,
            'image_versions' => array()
        );
        if ( $options ) {
            $this->options = array_merge( $this->options, $options );
        }
    }

    protected function get_file_path( $file_name = null, $version = null ) {
        $file_name = $file_name ? $file_name : '';
        if ( $file_name && $version ) {
            $file_name = preg_replace( '/(\.[^.]+)$/', '-' . $version . '$1', $file_name );
        }
        $template = $this->settings->get( 'template', 'active' ) . '/';
        return $template . $file_name;
    }

    protected function get_upload_path( $file_name = null, $version = null ) {
        $file_path = $this->get_file_path( $file_name, $version );
        return $this->options['upload_dir'] . $file_path;
    }

    // Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    protected function fix_integer_overflow( $size ) {
        if ( $size < 0 ) {
            $size += 2.0 * ( PHP_INT_MAX + 1 );
        }
        return $size;
    }

    protected function get_file_size( $file_path, $clear_stat_cache = false ) {
        if ( $clear_stat_cache ) {
            clearstatcache( true, $file_path );
        }
        return $this->fix_integer_overflow( filesize( $file_path ) );

    }

    /**
     * Creates a scaled image and saves to the filesystem
     *
     * @since 1.0
     *
     * In addition to true/false, the $crop parameter takes an array of the format 
     * array( $x_crop_position, $y_crop_position )
     * $x_crop_position can be 'left', 'center', 'right'
     * $y_crop_position can be 'top', 'center', 'bottom'
     *
     * @param string $file_name Name of the file
     * @param string $version Version label
     * @param int $width Resized image width
     * @param int $height Resized image height
     * @param bool $crop Optional, default is false. Whether to crop image or resize.
     * @param int $quality Optional, default is 80 for jpeg and 9 for png. Valid values are 0-100 for jpeg, 0-9 for png, doesn't apply for gif
     * @return bool True on success, false on failure
     */
    protected function create_scaled_image( $file_name, $version, $width, $height, $crop = false, $quality = null ) {
        $file_path = $this->get_upload_path( $file_name );
        
        if ( !empty( $version ) ) {
            $new_file_path = $this->get_upload_path( $file_name, $version );
        } else {
            $new_file_path = $file_path;
        }
        
        list( $img_width, $img_height ) = @getimagesize( $file_path );
        if ( !$img_width || !$img_height ) {
            return false;
        }
        
        $sizes = $this->image_resize_dimensions( $img_width, $img_height, $width, $height, $crop );
        list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $sizes;

        $new_img = @imagecreatetruecolor( $dst_w, $dst_h );
        switch ( strtolower( substr( strrchr( $file_name, '.' ), 1 ) ) ) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg( $file_path );
                $write_image = 'imagejpeg';
                $image_quality = ( $quality > 0 && $quality <= 100 ) ? $quality : 80;
                break;
            case 'gif':
                    @imagecolortransparent( $new_img, @imagecolorallocate( $new_img, 0, 0, 0 ) );
                $src_img = @imagecreatefromgif( $file_path );
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent( $new_img, @imagecolorallocate( $new_img, 0, 0, 0 ) );
                @imagealphablending( $new_img, false );
                @imagesavealpha( $new_img, true );
                $src_img = @imagecreatefrompng( $file_path );
                $write_image = 'imagepng';
                $image_quality = ( $quality > 0 && $quality <= 9 ) ? $quality : 9;
                break;
            default:
                $src_img = null;
        }

        $success = $src_img
            && @imagecopyresampled( $new_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h )
            && $write_image( $new_img, $new_file_path, $image_quality );

        // Free up memory (imagedestroy does not delete files):
        @imagedestroy( $src_img );
        @imagedestroy( $new_img );
        return $success;
    }

    /**
     * Retrieve calculated resized dimensions for use in WP_Image_Editor.
     * (Borrowed from WordPress)
     *
     * Calculate dimensions and coordinates for a resized image that fits within a
     * specified width and height. If $crop is true, the largest matching central
     * portion of the image will be cropped out and resized to the required size.
     *
     * @since 1.0
     *
     * @param int $orig_w Original width.
     * @param int $orig_h Original height.
     * @param int $dest_w New width.
     * @param int $dest_h New height.
     * @param bool $crop Optional, default is false. Whether to crop image or resize.
     * @return bool|array False on failure. Returned array matches parameters for imagecopyresampled() PHP function.
     */
    function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false) {

        if ($orig_w <= 0 || $orig_h <= 0)
            return false;
        // at least one of dest_w or dest_h must be specific
        if ($dest_w <= 0 && $dest_h <= 0)
            return false;

        if ( $crop ) {
            // crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
            $aspect_ratio = $orig_w / $orig_h;
            $new_w = min($dest_w, $orig_w);
            $new_h = min($dest_h, $orig_h);

            if ( !$new_w ) {
                $new_w = intval($new_h * $aspect_ratio);
            }

            if ( !$new_h ) {
                $new_h = intval($new_w / $aspect_ratio);
            }

            $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

            $crop_w = round($new_w / $size_ratio);
            $crop_h = round($new_h / $size_ratio);

            if ( !is_array( $crop ) || count( $crop ) != 2 ) {
                $crop = array( 'center', 'center' );
            }

            $x_index = 0;
            $y_index = 1;

            if ( in_array( $crop[1], array( 'left', 'right' ) ) || in_array( $crop[0], array( 'top', 'bottom' ) ) ) {
                $x_index = 1;
                $y_index = 0;
            }

            switch ( $crop[$x_index] ) {
                case 'left': $s_x = 0; break;
                case 'right': $s_x = $orig_w - $crop_w; break;
                default: $s_x = floor( ( $orig_w - $crop_w ) / 2 );
            }

            switch ( $crop[$y_index] ) {
                case 'top': $s_y = 0; break;
                case 'bottom': $s_y = $orig_h - $crop_h; break;
                default: $s_y = floor( ( $orig_h - $crop_h ) / 2 );
            }

        } else {
            // don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
            $crop_w = $orig_w;
            $crop_h = $orig_h;

            $s_x = 0;
            $s_y = 0;

            list( $new_w, $new_h ) = $this->constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
        }

        // if the resulting image would be the same size or larger we don't want to resize it
        if ( $new_w >= $orig_w && $new_h >= $orig_h )
            return false;

        // the return array matches the parameters to imagecopyresampled()
        // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

    }

    /**
     * Calculates the new dimensions for a downsampled image.
     * (Borrowed from WordPress)
     *
     * If either width or height are empty, no constraint is applied on
     * that dimension.
     *
     * @since 1.0
     *
     * @param int $current_width Current width of the image.
     * @param int $current_height Current height of the image.
     * @param int $max_width Optional. Maximum wanted width.
     * @param int $max_height Optional. Maximum wanted height.
     * @return array First item is the width, the second item is the height.
     */
    function constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
        if ( !$max_width and !$max_height )
            return array( $current_width, $current_height );

        $width_ratio = $height_ratio = 1.0;
        $did_width = $did_height = false;

        if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width ) {
            $width_ratio = $max_width / $current_width;
            $did_width = true;
        }

        if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height ) {
            $height_ratio = $max_height / $current_height;
            $did_height = true;
        }

        // Calculate the larger/smaller ratios
        $smaller_ratio = min( $width_ratio, $height_ratio );
        $larger_ratio  = max( $width_ratio, $height_ratio );

        if ( intval( $current_width * $larger_ratio ) > $max_width || intval( $current_height * $larger_ratio ) > $max_height )
            // The larger ratio is too big. It would result in an overflow.
            $ratio = $smaller_ratio;
        else
            // The larger ratio fits, and is likely to be a more "snug" fit.
            $ratio = $larger_ratio;

        $w = intval( $current_width  * $ratio );
        $h = intval( $current_height * $ratio );

        // Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
        // We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
        // Thus we look for dimensions that are one pixel shy of the max value and bump them up
        if ( $did_width && $w == $max_width - 1 )
            $w = $max_width; // Round it up
        if ( $did_height && $h == $max_height - 1 )
            $h = $max_height; // Round it up

        return array( $w, $h );
    }

    protected function get_error_message( $error, $file ) {
        if ( array_key_exists( $error, $this->error_messages ) ) {
            $error = $this->error_messages[$error];
        }

        if ( isset( $file->name ) && $file->name ) {
            $error = $file->name . ': ' . $error;
        }

        return $error;
    }

    function get_config_bytes( $val ) {
        $val = trim( $val );
        $last = strtolower( $val[strlen( $val )-1] );
        switch ( $last ) {
        case 'g':
                $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
        }
        return $this->fix_integer_overflow( $val );
    }

    protected function validate( $uploaded_file, $file, $error, $index ) {
        if ( $error ) {
            $file->error = $this->get_error_message( $error, $file );
            return false;
        }
        $content_length = $this->fix_integer_overflow( intval( $_SERVER['CONTENT_LENGTH'] ) );
        if ( $content_length > $this->get_config_bytes( ini_get( 'post_max_size' ) ) ) {
            $file->error = $this->get_error_message( 'post_max_size', $file );
            return false;
        }
        if ( !preg_match( $this->options['accept_file_types'], $file->name ) ) {
            $file->error = $this->get_error_message( 'accept_file_types', $file );
            return false;
        }
        if ( $uploaded_file && is_uploaded_file( $uploaded_file ) ) {
            $file_size = $this->get_file_size( $uploaded_file );
        } else {
            $file_size = $content_length;
        }
        if ( $this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'] )
        ) {
            $file->error = $this->get_error_message( 'max_file_size', $file );
            return false;
        }
        if ( $this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size'] ) {
            $file->error = $this->get_error_message( 'min_file_size', $file );
            return false;
        }
        if ( is_int( $this->options['max_number_of_files'] ) && (
                $this->count_file_objects() >= $this->options['max_number_of_files'] )
        ) {
            $file->error = $this->get_error_message( 'max_number_of_files', $file );
            return false;
        }
        list( $img_width, $img_height ) = @getimagesize( $uploaded_file );
        if ( is_int( $img_width ) ) {
            if ( $this->options['max_width'] && $img_width > $this->options['max_width'] ) {
                $file->error = $this->get_error_message( 'max_width', $file );
                return false;
            }
            if ( $this->options['max_height'] && $img_height > $this->options['max_height'] ) {
                $file->error = $this->get_error_message( 'max_height', $file );
                return false;
            }
            if ( $this->options['min_width'] && $img_width < $this->options['min_width'] ) {
                $file->error = $this->get_error_message( 'min_width', $file );
                return false;
            }
            if ( $this->options['min_height'] && $img_height < $this->options['min_height'] ) {
                $file->error = $this->get_error_message( 'min_height', $file );
                return false;
            }
        }
        return true;
    }

    protected function upcount_name_callback( $matches ) {
        $index = isset( $matches[1] ) ? intval( $matches[1] ) + 1 : 1;
        $ext = isset( $matches[2] ) ? $matches[2] : '';
        return ' ('.$index.')'.$ext;
    }

    protected function upcount_name( $name ) {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array( $this, 'upcount_name_callback' ),
            $name,
            1
        );
    }

    protected function get_unique_filename( $name, $type, $index, $content_range ) {
        while ( is_dir( $this->get_upload_path( $name ) ) ) {
            $name = $this->upcount_name( $name );
        }
        // Keep an existing filename if this is part of a chunked upload:
        $uploaded_bytes = $this->fix_integer_overflow( intval( $content_range[1] ) );
        while ( is_file( $this->get_upload_path( $name ) ) ) {
            if ( $uploaded_bytes === $this->get_file_size(
                    $this->get_upload_path( $name ) ) ) {
                break;
            }
            $name = $this->upcount_name( $name );
        }
        return $name;
    }

    protected function trim_file_name( $name, $type, $index, $content_range ) {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $name = trim( basename( stripslashes( $name ) ), ".\x00..\x20" );
        // Use a timestamp for empty filenames:
        if ( !$name ) {
            $name = str_replace( '.', '-', microtime( true ) );
        }
        // Add missing file extension for known image types:
        if ( strpos( $name, '.' ) === false &&
            preg_match( '/^image\/(gif|jpe?g|png)/', $type, $matches ) ) {
            $name .= '.'.$matches[1];
        }
        return $name;
    }

    protected function get_file_name( $name, $type, $index, $content_range ) {
        return $this->get_unique_filename(
            $this->trim_file_name( $name, $type, $index, $content_range ),
            $type,
            $index,
            $content_range
        );
    }

    protected function handle_form_data( $file, $index ) {
        // Handle form data, e.g. $_REQUEST['description'][$index]
    }

    protected function orient_image( $file_path ) {
        if ( !function_exists( 'exif_read_data' ) ) {
            return false;
        }
        $exif = @exif_read_data( $file_path );
        if ( $exif === false ) {
            return false;
        }
        $orientation = intval( @$exif['Orientation'] );
        if ( !in_array( $orientation, array( 3, 6, 8 ) ) ) {
            return false;
        }
        $image = @imagecreatefromjpeg( $file_path );
        switch ( $orientation ) {
        case 3:
            $image = @imagerotate( $image, 180, 0 );
            break;
        case 6:
            $image = @imagerotate( $image, 270, 0 );
            break;
        case 8:
            $image = @imagerotate( $image, 90, 0 );
            break;
        default:
            return false;
        }
        $success = imagejpeg( $image, $file_path );
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy( $image );
        return $success;
    }

    protected function handle_file_upload( $uploaded_file, $name, $size, $type, $error,
        $index = null, $content_range = null ) {
        $file = new stdClass();
        $file->name = $this->get_file_name( $name, $type, $index, $content_range );
        $file->size = $this->fix_integer_overflow( intval( $size ) );
        $file->type = $type;
        
        list( $img_width, $img_height ) = @getimagesize( $uploaded_file );
        
        if ( is_int( $img_width ) ) {
            $file->width = $img_width;
            $file->height = $img_height;
        }

        if ( !$this->validate( $uploaded_file, $file, $error, $index ) ) {
            return (array)$file;
        }

        $this->handle_form_data( $file, $index );
        $upload_dir = $this->get_upload_path();
        if ( !is_dir( $upload_dir ) ) {
            mkdir( $upload_dir, $this->options['mkdir_mode'], true );
        }
        $file_path = $this->get_upload_path( $file->name );
        $append_file = $content_range && is_file( $file_path ) &&
            $file->size > $this->get_file_size( $file_path );
        if ( $uploaded_file && is_uploaded_file( $uploaded_file ) ) {
            // multipart/formdata uploads (POST method uploads)
            if ( $append_file ) {
                file_put_contents(
                    $file_path,
                    fopen( $uploaded_file, 'r' ),
                    FILE_APPEND
                );
            } else {
                move_uploaded_file( $uploaded_file, $file_path );
            }
        } else {
            // Non-multipart uploads (PUT method support)
            file_put_contents(
                $file_path,
                fopen( 'php://input', 'r' ),
                $append_file ? FILE_APPEND : 0
            );
        }
        $file_size = $this->get_file_size( $file_path, $append_file );
        if ( $file_size === $file->size ) {
            if ( $this->options['orient_image'] ) {
                $this->orient_image( $file_path );
            }
            $file->path = $this->get_file_path( $file->name );
            foreach ( $this->options['image_versions'] as $version => $options ) {
                // Width and height are required
                if ( !isset( $options['width'] ) || !isset( $options['height'] ) ) {
                    continue;
                }

                $options = array_merge( array(
                    'crop' => false,
                    'quality' => null
                ), $options );

                if ( $this->create_scaled_image( $file->name, $version, $options['width'], $options['height'], $options['crop'], $options['quality'] ) ) {
                    if ( !empty( $version ) ) {
                        $file->versions[$version] = array(
                            'path' => $this->get_file_path( $file->name, $version ),
                            'width' => $options['width'],
                            'height' => $options['height']
                        );
                    } else {
                        $file_size = $this->get_file_size( $file_path, true );
                    }
                }
            }
        } else if ( !$content_range && $this->options['discard_aborted_uploads'] ) {
                unlink( $file_path );
                $file->error = 'abort';
            }
        $file->size = $file_size;

        return (array)$file;
    }

    protected function body( $str ) {
        echo $str;
    }

    protected function header( $str ) {
        header( $str );
    }

    protected function generate_response( $content, $print_response = true ) {
        if ( $print_response ) {
            $json = json_encode( $content );
            $redirect = isset( $_REQUEST['redirect'] ) ?
                stripslashes( $_REQUEST['redirect'] ) : null;
            if ( $redirect ) {
                $this->header( 'Location: '.sprintf( $redirect, rawurlencode( $json ) ) );
                return;
            }
            $this->head();
            if ( isset( $_SERVER['HTTP_CONTENT_RANGE'] ) ) {
                $files = isset( $content[$this->options['param_name']] ) ?
                    $content[$this->options['param_name']] : null;
                if ( $files && is_array( $files ) && is_object( $files[0] ) && $files[0]->size ) {
                    $this->header( 'Range: 0-'.( $this->fix_integer_overflow( intval( $files[0]->size ) ) - 1 ) );
                }
            }
            $this->body( $json );
        }
        return $content;
    }

    protected function send_content_type_header() {
        $this->header( 'Vary: Accept' );
        if ( isset( $_SERVER['HTTP_ACCEPT'] ) &&
            ( strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false ) ) {
            $this->header( 'Content-type: application/json' );
        } else {
            $this->header( 'Content-type: text/plain' );
        }
    }

    protected function send_access_control_headers() {
        $this->header( 'Access-Control-Allow-Origin: '.$this->options['access_control_allow_origin'] );
        $this->header( 'Access-Control-Allow-Credentials: '
            .( $this->options['access_control_allow_credentials'] ? 'true' : 'false' ) );
        $this->header( 'Access-Control-Allow-Methods: '
            .implode( ', ', $this->options['access_control_allow_methods'] ) );
        $this->header( 'Access-Control-Allow-Headers: '
            .implode( ', ', $this->options['access_control_allow_headers'] ) );
    }

    public function head() {
        $this->header( 'Pragma: no-cache' );
        $this->header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        $this->header( 'Content-Disposition: inline; filename="files.json"' );
        // Prevent Internet Explorer from MIME-sniffing the content-type:
        $this->header( 'X-Content-Type-Options: nosniff' );
        if ( $this->options['access_control_allow_origin'] ) {
            $this->send_access_control_headers();
        }
        $this->send_content_type_header();
    }

    public function post( $print_response = true ) {
        $upload = isset( $_FILES[$this->options['param_name']] ) ?
            $_FILES[$this->options['param_name']] : null;
        // Parse the Content-Disposition header, if available:
        $file_name = isset( $_SERVER['HTTP_CONTENT_DISPOSITION'] ) ?
            rawurldecode( preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $_SERVER['HTTP_CONTENT_DISPOSITION']
            ) ) : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = isset( $_SERVER['HTTP_CONTENT_RANGE'] ) ?
            preg_split( '/[^0-9]+/', $_SERVER['HTTP_CONTENT_RANGE'] ) : null;
        $size =  $content_range ? $content_range[3] : null;
        $files = array();
        if ( $upload && is_array( $upload['tmp_name'] ) ) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ( $upload['tmp_name'] as $index => $value ) {
                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    $file_name ? $file_name : $upload['name'][$index],
                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            $files[] = $this->handle_file_upload(
                isset( $upload['tmp_name'] ) ? $upload['tmp_name'] : null,
                $file_name ? $file_name : ( isset( $upload['name'] ) ?
                    $upload['name'] : null ),
                $size ? $size : ( isset( $upload['size'] ) ?
                    $upload['size'] : $_SERVER['CONTENT_LENGTH'] ),
                isset( $upload['type'] ) ?
                $upload['type'] : $_SERVER['CONTENT_TYPE'],
                isset( $upload['error'] ) ? $upload['error'] : null,
                null,
                $content_range
            );
        }

        return $this->generate_response( $files, $print_response );
    }
}
