<?php

namespace Drupal\global_prayer_times\Service;

use GuzzleHttp\ClientInterface;

class LocationApi {

  protected ClientInterface $client;

  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  public function getCountries(): array {
    $response = $this->client->get('https://countriesnow.space/api/v0.1/countries/');
    $data = json_decode($response->getBody(), TRUE);

    $countries = [];
    foreach ($data['data'] as $item) {
      $countries[$item['country']] = $item['country'];
    }
    asort($countries);
    return $countries;
  }

  public function getCities(string $country): array {
    $response = $this->client->post('https://countriesnow.space/api/v0.1/countries/cities', [
      'json' => ['country' => $country]
    ]);
    $data = json_decode($response->getBody(), TRUE);

    $cities = [];
    foreach ($data['data'] as $city) {
      $cities[$city] = $city;
    }
    return $cities;
  }

  public function getLocationFromIp($ip = NULL) {
    try {
      $url = 'https://ipapi.co/json/';
      if ($ip) {
        $url = "https://ipapi.co/{$ip}/json/";
      }
      $response = $this->client->get($url);
      $data = json_decode($response->getBody(), TRUE);
      \Drupal::logger('global_prayer_times')->info('IP Location Data for @ip: <pre>@data</pre>', ['@ip' => $ip, '@data' => print_r($data, TRUE)]);
      return $data;
    }
    catch (\Exception $e) {
      \Drupal::logger('global_prayer_times')->error('IP Location Error: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
}