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
}