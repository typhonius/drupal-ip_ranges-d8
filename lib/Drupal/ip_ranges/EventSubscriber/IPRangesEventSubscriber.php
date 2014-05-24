<?php

namespace Drupal\ip_ranges\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IPRangesEventSubscriber implements EventSubscriberInterface {

  public function onKernelRequest(GetResponseEvent $event) {
    $request = \Drupal::service('request');

    drupal_set_message('event subscriber');

  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequest', 100);
    return $events;
  }

}