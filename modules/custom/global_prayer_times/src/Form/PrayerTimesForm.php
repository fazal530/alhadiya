<?php

namespace Drupal\global_prayer_times\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PrayerTimesForm extends FormBase {

  protected $locationApi;
  protected $prayerApi;

  public function __construct($locationApi, $prayerApi) {
    $this->locationApi = $locationApi;
    $this->prayerApi = $prayerApi;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('global_prayer_times.location_api'),
      $container->get('global_prayer_times.prayer_api')
    );
  }

  public function getFormId() {
    return 'global_prayer_times_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $countries = $this->locationApi->getCountries();
    $country = $form_state->getValue('country');
    $city = $form_state->getValue('city');

    $cities = [];

    // Auto-detect location if not set
    if (!$country && !$city) {
      $ip = \Drupal::request()->getClientIp();
      
      // Check if IP is private/reserved or force test IP for dev
      if (strpos($ip, '127.') === 0 || strpos($ip, '172.') === 0 || strpos($ip, '192.') === 0 || $ip == '::1') {
         $ip = '103.151.42.130'; // Example IP (Lahore, Pakistan)
      }
      
      $location = $this->locationApi->getLocationFromIp($ip);
      if ($location && isset($location['country_name']) && isset($location['city'])) {
        $detected_country = $location['country_name'];
        $detected_city = $location['city'];
        
        \Drupal::logger('global_prayer_times')->info('Detected: Country=@country, City=@city', [
            '@country' => $detected_country,
            '@city' => $detected_city
        ]);

        // Check if country exists in the list
        if (array_key_exists($detected_country, $countries)) {
             $country = $detected_country;
             $cities = $this->locationApi->getCities($country);
             
             // Case-insensitive search for city
             $found_city = NULL;
             foreach ($cities as $c_key => $c_val) {
                 if (strcasecmp(trim($c_val), trim($detected_city)) === 0) {
                     $found_city = $c_key;
                     break;
                 }
             }

             if ($found_city) {
                 $city = $found_city;
                 \Drupal::logger('global_prayer_times')->info('City matched: @city', ['@city' => $city]);
             } else {
                 // If not found, add it to the list anyway so it can be selected.
                 $city = $detected_city;
                 $cities[$city] = $city;
                 asort($cities); // Keep it sorted
                 
                 \Drupal::logger('global_prayer_times')->warning('City "@city" added to options (exact match not found).', [
                    '@city' => $detected_city
                 ]);
             }
        } else {
            \Drupal::logger('global_prayer_times')->warning('Country "@country" not found in list.', ['@country' => $detected_country]);
        }
      }
    }

    /**
     * ===============================
     * LOCATION WRAPPER (FLEX)
     * ===============================
     */
    $form['location_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['location-wrapper'],
      ],
    ];

    // COUNTRY FIELD
    $form['location_wrapper']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $countries,
      '#empty_option' => $this->t('- Select country -'),
      '#default_value' => $country,
      '#ajax' => [
        'callback' => '::updateCity',
        'wrapper' => 'city-wrapper',
      ],
    ];

    // CITY AJAX CONTAINER
    $form['location_wrapper']['city_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'city-wrapper',
      ],
    ];

    if ($country) {
      // If cities were not fetched during auto-detection, fetch them now
      if (empty($cities)) {
        $cities = $this->locationApi->getCities($country);
      }

      $form['location_wrapper']['city_wrapper']['city'] = [
        '#type' => 'select',
        '#title' => $this->t('City'),
        '#options' => $cities,
        '#empty_option' => $this->t('- Select city -'),
        '#default_value' => $city,
        '#ajax' => [
          'callback' => '::updatePrayer',
          'wrapper' => 'prayer-wrapper',
        ],
      ];
    }

    /**
     * ===============================
     * PRAYER TIMES WRAPPER
     * ===============================
     */
    $form['prayer_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'prayer-wrapper'],
    ];

    if ($country && $city) {
      $data = $this->prayerApi->getTimes($city, $country);

      if (!empty($data['data']['timings'])) {
        $times = $data['data']['timings'];
        $date = $data['data']['date'];
        $hijri = $date['hijri'];

        $hijri_date_string = "{$hijri['weekday']['en']}, {$hijri['day']} {$hijri['month']['en']} {$hijri['year']}";

        $form['prayer_wrapper']['date'] = [
          '#type' => 'markup',
          '#markup' => '<div class="prayer-date"><strong>Islamic Date:</strong> ' . $hijri_date_string . '</div>',
          '#weight' => -10,
        ];

        $form['prayer_wrapper']['times'] = [
          '#theme' => 'item_list',
          '#items' => [
            'Fajr: ' . $times['Fajr'],
            'Dhuhr: ' . $times['Dhuhr'],
            'Asr: ' . $times['Asr'],
            'Maghrib: ' . $times['Maghrib'],
            'Isha: ' . $times['Isha'],
          ],
        ];
      }
    }

    return $form;
  }

  /**
   * AJAX callback: Update City
   */
  public function updateCity(array &$form, FormStateInterface $form_state) {
    return $form['location_wrapper']['city_wrapper'];
  }

  /**
   * AJAX callback: Update Prayer Times
   */
  public function updatePrayer(array &$form, FormStateInterface $form_state) {
    return $form['prayer_wrapper'];
  }

  /**
   * Required submit handler
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submit action (AJAX-only form)
  }
}
