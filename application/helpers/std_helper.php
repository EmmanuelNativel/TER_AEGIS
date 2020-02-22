<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('std_to_array'))
{
  function std_to_array($std) {
    return json_decode(json_encode($std), true);
  }

}
