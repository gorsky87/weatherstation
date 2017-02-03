<?php

/**
 * @file
 * providing the service return all weather icon.
 */

namespace Drupal\weatherstation\Services;

/**
 * Class WeatherStationServices
 * @package Drupal\weatherstation\Services
 */
class WeatherStationServices {

  /**
   * Combination weather and icon code.
   *
   * @return array
   *   All combination icon with weather.
   */
  public function getWeatherTypeIcon() {
    return array(
      '01d' => 'Clear sky - day',
      '01n' => 'Clear sky - night',
      '02d' => 'Few clouds - day',
      '02n' => 'Few clouds - night',
      '03d' => 'Scattered clouds - day',
      '03n' => 'Scattered clouds - night',
      '04d' => 'Broken clouds - day',
      '04n' => 'Broken clouds - night',
      '09d' => 'Shower rain - day',
      '09n' => 'Shower rain - night',
      '10d' => 'Rain - day',
      '10n' => 'Rain - night',
      '11d' => 'Thunderstorm - day',
      '11n' => 'Thunderstorm - night',
      '13d' => 'Snow - day',
      '13n' => 'Snow - night',
      '50d' => 'Mist - day',
      '50n' => 'Mist - night',
    );
  }
}
