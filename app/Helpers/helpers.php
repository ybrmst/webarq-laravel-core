<?php

if (! function_exists('array_get')) {
  /**
   * Get an item from an array using "dot" notation.
   *
   * @param  array  $array
   * @param  string  $key
   * @param  mixed   $default
   * @return mixed
   */
  function array_get($array, $key, $default = null)
  {
      return Illuminate\Support\Arr::get($array, $key, $default);
  }
}