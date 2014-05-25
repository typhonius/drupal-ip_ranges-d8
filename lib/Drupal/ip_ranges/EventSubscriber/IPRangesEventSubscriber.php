<?php

namespace Drupal\ip_ranges\EventSubscriber;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IPRangesEventSubscriber implements EventSubscriberInterface {

  public $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function onKernelRequest(GetResponseEvent $event) {
    drupal_set_message('event subscriber');
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequest', 100);
    return $events;
  }

}
