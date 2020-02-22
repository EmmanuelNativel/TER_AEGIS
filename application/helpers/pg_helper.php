<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('to_pg_array'))
{
  function to_pg_array($set) {
      settype($set, 'array'); // can be called with a scalar or array
      $result = array();
      foreach ($set as $t) {
          if (is_array($t)) {
              $result[] = to_pg_array($t);
          } else {
              $t = str_replace('"', '\\"', $t); // escape double quote
              if (! is_numeric($t)) // quote only non-numeric values
                  $t = '"' . $t . '"';
              $result[] = $t;
          }
      }
      return '{' . implode(",", $result) . '}'; // format
  }
}

if ( ! function_exists('pg_to_php_array'))
{
  function pg_to_php_array($postgresArray)
  {
    if ($postgresArray == "{}") {
      return Array();
    }

    $postgresStr = trim($postgresArray,"{}");
    $elmts = explode(",",$postgresStr);
    return $elmts;
  }
}

if (! function_exists('nullify_array')) {
  /**
  * Remplace les valeurs vide d'un tableau par la valeur NULL
  */
  function nullify_array($array)
  {
    foreach ($array as $key => $value) {
      if (empty($value)) {
        $array[$key] = NULL;
      }
    }
    return $array;
  }
}
