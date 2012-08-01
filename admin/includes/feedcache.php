<?php
/**
 * Cache remote feeds to improve speed and reliability
 * Author: Erik Runyon
 * Updated: 2012-06-08
 */
 

class FeedCache {
  private $local;
  private $remote;
  private $valid_for;
  private $is_expired;
  private $is_local;
  private $data = false;

  public function __construct($local, $remote, $valid_for=3600) {
    $this->local = ADMIN_DIR . 'cache/'.$local;
    $this->remote = $remote;
    $this->valid_for = $valid_for;
    $this->is_local = $this->check_local();
    $this->is_expired = $this->check_expired();
    $this->data = $this->populate_data();
  }

  public function get_data() {
    return $this->data;
  }

  /**
   * 1. If local file is valid, use it
   * 2. If it's not, try to cache it
   * 3. If that fails, use the local even if its expired so we at least have something
   */
  private function populate_data() {
    if( $this->is_local && !$this->is_expired ) {
      return file_get_contents($this->local);
    } else if( $this->cache_feed() || $this->is_local ) {
      return file_get_contents($this->local);
    }
  }

  private function determine_feed() {
    $file = '';
    if($this->is_local && !$this->expired) {
      $file = $this->local;
    } else {
      $file = $this->cache_feed() ? $this->local : $this->remote;
    }
    return $file;
  }

  /**
   * If remote file exists, get the data and write it to the local cache folder
   */
  private function cache_feed() {
    if($this->remote_file_exists($this->remote)) {
      $compressed_content = '';
      $remote_content = file_get_contents($this->remote);
      $compressed_content = preg_replace('/\s*?\n\s*/', "\n", $remote_content);
      $compressed_content = preg_replace('/( |\t)( |\t)*/', " ", $compressed_content);
      file_put_contents($this->local, $compressed_content);
      return true;
    } else {
      return false;
    }
  }

  private function check_local() {
    return ( (is_file($this->local)) && (filesize($this->local) > 500) ) ? true : false;
  }

  private function check_expired() {
    if($this->is_local === true) {
      $valid_until = filemtime($this->local) + $this->valid_for;
      return $valid_until < mktime();
    }
    return true;
  }

  /**
   * Check to see if remote feed exists and responding in a timely manner
   */
  private function remote_file_exists($url) {
    $ret = false;
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true); // check the connection; return no content
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // timeout after 1 second
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // The maximum number of seconds to allow cURL functions to execute.
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; da; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11');

    // do request
    $result = curl_exec($ch);

    // if request is successful
    if ($result === true) {
      $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($statusCode === 200) {
        $ret = true;
      }
    }
    curl_close($ch);

    return $ret;
  }

}
?>