<?php

namespace Drupal\ip_ranges\EventSubscriber;

use Drupal\ip_ranges\IPRangeManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IPRangesEventSubscriber implements EventSubscriberInterface {

  protected  $request;

  protected $manager;

  public function __construct(Request $request, IPRangeManager $manager) {
    $this->request = $request;
    $this->manager = $manager;
  }

  public function onKernelRequest(GetResponseEvent $event) {
    drupal_set_message('event subscriber');
    $this->manager->ipIsBanned($this->request->getClientIp());
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequest', 100);
    return $events;
  }

}
