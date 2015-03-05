<?php

function array2string($data, $isformdata = 1) {
  if($data == '') return '';
  if($isformdata) $data = new_stripslashes($data);
  return addslashes(var_export($data, TRUE));
}

function string2array($data) {
  if($data == '') return array();
  @eval("\$array = $data;");
  return $array;
}

function new_stripslashes($string) {
  if(!is_array($string)) return stripslashes($string);
  foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
  return $string;
}