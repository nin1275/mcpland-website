<?php
  /**
  * ExtraResources For Layouts
  * @version     1.0
  * @author      Firat Kaya
  **/
  defined('BUILD_NUMBER') or define('BUILD_NUMBER', 500);
  class ExtraResources {
    private $resources = array();
    private $type;

    public function __construct($type = null) {
      $this->type = $type;
    }

    public function addResource($resource = null, $async = false, $defer = false) {
      array_push($this->resources, array(
        'url'   => $resource,
        'async' => $async,
        'defer' => $defer
      ));
    }

    public function getResources() {
      if (!empty(array_filter($this->resources))) {
        foreach ($this->resources as $resource) {
          if ($this->type === 'css') {
            echo '<link rel="stylesheet" href="'.$resource['url'].'?v='.BUILD_NUMBER.'">';
          }
          else if ($this->type === 'js') {
            echo '<script src="'.$resource['url'].'?v='.BUILD_NUMBER.'" '.(($resource['async']) ? 'async' : null).' '.(($resource['defer']) ? 'defer' : null).'></script>';
          }
          else {
            return false;
          }
        }
      }
      else {
        return false;
      }
    }
  }
?>
