<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 24/5/14
 * Time: 19:10
 */

namespace Drupal\ip_ranges;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\ContentEntityFormInterface;
use Drupal\Core\Entity\EntityManagerInterface;

class IPRangesFormController extends ContentEntityForm implements ContentEntityFormInterface {

  protected $own_ip;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->own_ip = \Drupal::request()->getClientIp();
    parent::__construct($entity_manager);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {

    $entity = $this->entity;

    $form['own_ip_warning'] = array(
      '#type' => 'value',
      '#markup' => t('<strong>Note that your own IP Address is currently @ip. Be careful not to lock yourself out!<br />'.l('Click here to whitelist your own IP-address.', 'admin/config/people/ip-ranges/whitelist_own').'</strong>', array('@ip' => $this->own_ip)),
    );

    $form['ip'] = array(
      '#title' => t('IP range start / Single IP-address'),
      '#type' => 'textfield',
      '#size' => 48,
      '#required' => TRUE,
      '#maxlength' => 15,
      '#description' => t('Enter IP-address (100.100.100.100). If range end is specified, it will be used as start of the range, otherwise as a single IP-address.'),
    );
    $form['ip_end'] = array(
      '#title' => t('IP range end (optional)'),
      '#type' => 'textfield',
      '#size' => 48,
      '#required' => FALSE,
      '#maxlength' => 15,
      '#description' => t('If entered, the banned ip will be treated as a range.'),
    );

    $form['type'] = array(
      '#title' => t('List type'),
      '#description' => t('Add the IP to a whitelist to ensure access or the blacklist to deny access'),
      '#type' => 'select',
      '#options' => array(0 => t('Blacklist'), 1 => t('Whitelist')),
      '#multiple' => FALSE,
      '#default_value' => 0,
      '#required' => TRUE,
    );

    if ($this->operation == 'edit') {
      $form['type']['#default_value'] = $entity->getType();
      $form['ip']['#default_value'] = $entity->getIpStart();
      $form['ip_end']['#default_value'] = $entity->getIpEnd();
    }

    return parent::form($form, $form_state, $entity);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state){
    $ip_start = $form_state['values']['ip'] = trim($form_state['values']['ip']);
    $ip_end = $form_state['values']['ip_end'] = isset($form_state['values']['ip_end']) ? trim($form_state['values']['ip_end']) : FALSE;
    if (filter_var($ip_start, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) == FALSE) {
      \Drupal::formBuilder()->setErrorByName('ip', $form_state, t("IP(/range start) is not a valid IP address."));
    }
    elseif ($ip_end && filter_var($ip_end, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) == FALSE) {
      \Drupal::formBuilder()->setErrorByName('ip_end', $form_state, t("IP range end is not a valid IP address."));
    }
    elseif ($ip_start == $this->own_ip || ($ip_end && ip_ranges_check_range($ip_start . '-' . $ip_end, $this->own_ip))) {
      $this->setFormError('You may not block your own IP address', $form_state);
    }
    parent::validate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    if ($form_state['values']['ip_end']) {
      $form_state['values']['ip'] .= '-' . $form_state['values']['ip_end'];
    }
    parent::submit($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $entity = $this->entity;
    $entity->save();
    $form_state['redirect_route']['route_name'] = 'ip_ranges.admin_list';
  }
}