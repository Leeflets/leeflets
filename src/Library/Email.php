<?php

namespace Leeflets\Core\Library;

class Email_Template {
    private $to;
    private $subject;
    private $message;
    private $headers;
    private $template_dir;
    
    function __construct($file, $to, $subject, $vars = array()) {
        $this->template_dir = ROOT_DIR . '/inc/email/';

        if (!file_exists($this->template_dir . $file . '.php')) return false;

        extract($vars);
        
        $this->to = $to;
        $this->subject = $subject;

        $this->headers = "MIME-Version: 1.0\n";
        $this->headers .= 'From: ' . get_siteinfo('name') . ' <' . get_siteinfo('admin_email') . ">\n";
        $this->headers .= "Content-Type: text/plain; charset=\"" . get_siteinfo('charset') . "\"\n";
        
        if (isset($headers)) {
            $this->headers .= $headers;
        }
        
        ob_start();
        include($this->template_dir . $file);
        $this->message = ob_get_clean();
        $this->message = wordwrap($this->message, 80, "\n");
    }
    
    function send() {
        return mail($this->to, $this->subject, $this->message, $this->headers);
    }
}
