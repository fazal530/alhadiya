<?php

namespace Drupal\global_prayer_times\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *   id = "global_prayer_times_block",
 *   admin_label = @Translation("Global Prayer Times")
 * )
 */
class PrayerTimesBlock extends BlockBase {

  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\global_prayer_times\Form\PrayerTimesForm');
  }
}