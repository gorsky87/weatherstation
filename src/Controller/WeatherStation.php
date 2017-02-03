<?php

/**
 * @file
 * Contains \Drupal\weatherstation\Controller\WeatherStation.
 */

namespace Drupal\weatherstation\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class WeatherStation
 * @package Drupal\weatherstation\Controller
 */
class WeatherStation extends ControllerBase {


  public $config;

  /**
   * WeatherStation constructor.
   */
  public function __construct() {
    $this->config = $this->config('weatherstation.settings');
  }


  /**
   * Get weather in json.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Weather data in json format.
   */
  public function getWeather() {
    $response = new JsonResponse();
    return $response->setData($this->getData());
  }


  /**
   * Select last information about weather from db.
   *
   * @return mixed
   *   Date from database.
   */
  private function getData() {
    if ($this->needRefresh()) {
      $this->refreshData();
    }
    $query = \Drupal::database()->select('weatherstation', 'ow');
    $query->addField('ow', 'data', 'created');
    $query->orderBy('created');
    $query->range(0, 1);
    return ($query->execute()->fetchField());
  }

  /**
   * Check we need call api to refresh data.
   *
   * @return bool
   *   True when we data is old.
   */
  private function needRefresh() {
    if ($this->config->get('last_time_refresh') + 10 * 60 > REQUEST_TIME) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Refresh data from openweather.
   *
   * @return bool
   *   True when refresh data was done without error.
   */
  private function refreshData() {
    $api_key = $this->config->get('openweather_api_key');

    $latitude = $this->config->get('lat');
    $longitude = $this->config->get('lon');
    if (empty($latitude) || empty($longitude)) {
      return FALSE;
    }
    $endpoint_url = "http://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&APPID={$api_key}";

    $client = \Drupal::httpClient();
    $response = $client->get($endpoint_url);
    if ($response->getStatusCode() != 200) {
      return FALSE;
    }
    $weather_json = $response->getBody()->getContents();
    $weather = json_decode($response->getBody());
    $query = \Drupal::database()->insert('weatherstation');
    $query->fields(['name', 'lon', 'lat', 'created', 'time', 'data']);
    $query->values([
      $weather->name,
      $weather->coord->lon,
      $weather->coord->lat,
      REQUEST_TIME,
      $weather->dt,
      $weather_json
    ]);
    if (!$query->execute()) {
      return FALSE;
    }
    $editable_config = \Drupal::service('config.factory')->getEditable('weatherstation.settings');
    $editable_config->set('last_time_refresh', REQUEST_TIME)->save();
    return TRUE;
  }
}
