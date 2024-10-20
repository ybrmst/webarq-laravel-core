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

if (! function_exists('array_pull')) {
  /**
   * Remove an item from an array using "dot" notation.
   *
   * @param  array  $array
   * @param  string  $key
   * @return mixed
   */
  function array_pull(&$array, $key)
  {
      return Illuminate\Support\Arr::pull($array, $key);
  }
}

if (! function_exists('studly_case')) {
  /**
   * Convert a value to studly caps case.
   *
   * @param  string  $value
   * @return string
   */
  function studly_case($value)
  {
      return Illuminate\Support\Str::studly($value);
  }
}