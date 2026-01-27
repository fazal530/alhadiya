<?php

namespace Drupal\visitor_stats\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class VisitorSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['trackVisitor'],
    ];
  }

  public function trackVisitor(RequestEvent $event) {
    if (!$event->isMainRequest()) {
      return;
    }

    \Drupal::database()->insert('visitor_stats')
      ->fields([
        'ip' => \Drupal::request()->getClientIp(),
        'created' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();
  }
}
