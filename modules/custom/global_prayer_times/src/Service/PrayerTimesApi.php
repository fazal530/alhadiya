<?php

namespace Drupal\global_prayer_times\Service;

use GuzzleHttp\ClientInterface;

class PrayerTimesApi {

  protected ClientInterface $client;

  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  public function getTimes(string $city, string $country): array {
    $response = $this->client->get('https://api.aladhan.com/v1/timingsByCity', [
      'query' => [
        'city' => $city,
        'country' => $country,
        'method' => 2,
      ]
    ]);
    return json_decode($response->getBody(), TRUE);
  }
}