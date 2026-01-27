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

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $countries,
      '#empty_option' => $this->t('- Select country -'),
      '#ajax' => [
        'callback' => '::updateCity',
        'wrapper' => 'city-wrapper',
      ],
    ];

    $form['city_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'city-wrapper'],
    ];

    if ($country) {
      $cities = $this->locationApi->getCities($country);

      $form['city_wrapper']['city'] = [
        '#type' => 'select',
        '#title' => $this->t('City'),
        '#options' => $cities,
        '#empty_option' => $this->t('- Select city -'),
        '#ajax' => [
          'callback' => '::updatePrayer',
          'wrapper' => 'prayer-wrapper',
        ],
      ];
    }

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

  public function updateCity(array &$form, FormStateInterface $form_state) {
    return $form['city_wrapper'];
  }

  public function updatePrayer(array &$form, FormStateInterface $form_state) {
    return $form['prayer_wrapper'];
  }

  // REQUIRED by FormInterface
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submit action needed (AJAX-only form)
  }
}