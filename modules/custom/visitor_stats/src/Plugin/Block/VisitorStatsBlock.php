<?php

namespace Drupal\visitor_stats\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Visitor Stats block.
 *
 * @Block(
 *   id = "visitor_stats_block",
 *   admin_label = @Translation("Visitor Stats")
 * )
 */
class VisitorStatsBlock extends BlockBase {

  public function build() {
    $db = \Drupal::database();
    $today = strtotime('today');

    $visitors_today = $db->query(
      "SELECT COUNT(DISTINCT ip) FROM {visitor_stats} WHERE created >= :today",
      [':today' => $today]
    )->fetchField();

    $total_visits = $db->query(
      "SELECT COUNT(*) FROM {visitor_stats}"
    )->fetchField();

    $online = $db->query(
      "SELECT COUNT(DISTINCT ip) FROM {visitor_stats} WHERE created >= :time",
      [':time' => time() - 300]
    )->fetchField();

    return [
      '#markup' => "
        <div class='visitor-stats'>
          <div>Visitors Today: <strong>$visitors_today</strong></div>
          <div>Total Visits: <strong>$total_visits</strong></div>
          <div>Online: <strong>$online</strong></div>
        </div>
      ",
      '#cache' => ['max-age' => 0],
    ];
  }
}
