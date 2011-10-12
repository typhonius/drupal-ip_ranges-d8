<?php

/**
 * @file
 * Page callback file for the ip ranges module.
 */

/**
 * Menu callback. Displays banned IP ranges.
 */
function ip_ranges_page() {
  $rows = array();
  $header = array(t('banned IP addresses'), t('list type'), t('Operations'));
  $ip_list = ip_ranges_get_ip_list();
  foreach ($ip_list as $ip) {
    $rows[] = array(
      str_replace('-', ' - ',$ip->ip),
      $ip->type,
      l(t('delete'), "admin/config/people/ip-ranges/delete/$ip->bid/$ip->ip"),
    );
  }

  $build['ip_ranges_form'] = drupal_get_form('ip_ranges_form');

  $build['ip_ranges_banning_table'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
  );

  return $build;
}

/**
 * Defines the form for banning IP addresses.
 *
 * @ingroup forms
 * @see ip_ranges_form_submit()
 */
function ip_ranges_form($form, $form_state) {
  $form['ip-start'] = array(
    '#title' => t('IP range start / Single IP-address'),
    '#type' => 'textfield',
    '#size' => 48,
    '#required' => TRUE,
    '#maxlength' => 15,
    '#description' => t('Enter IP-address (100.100.100.100). If range end is specified, it will be used as start of the range, otherwise as a single IP-address.'),
  );
  $form['ip-end'] = array(
    '#title' => t('IP range end (optional)'),
    '#type' => 'textfield',
    '#size' => 48,
    '#required' => FALSE,
    '#maxlength' => 15,
    '#description' => t('If entered, the banned ip will be treated as a range.'),
  );
  $form['type'] = array(
    '#title' => t('List type'),
    '#type' => 'select',
    '#multiple' => FALSE,
    '#options' => array('blacklist' => 'blacklist', 'whitelist' => 'whitelist'),
    '#default_value' => 'blacklist',
    '#required' => TRUE,
    '#description' => t('Choose list type.'),
  );
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Add'),
  );
  $form['#submit'][] = 'ip_ranges_form_submit';
  // No working validation yet. Probably the best solution is to modify the form to have separete fields for the ranges and validate those like normal ip-addresses.
  // However, since the new Database API handles injection checks and the range will probably get bitmask-support later, this is not that important right now.
  //$form['#validate'][] = 'ban_ip_form_validate';
  return $form;
}

/*
  // This is the original form validation from core. Could be used as a starting point.
 function ip_ranges_form_validate($form, &$form_state) {
  $ip = trim($form_state['values']['ip']);
  if (db_query("SELECT * FROM {banned_ips} WHERE ip = :ip", array(':ip' => $ip))->fetchField()) {
    form_set_error('ip', t('This IP address is already banned.'));
  }
  elseif ($ip == ip_address()) {
    form_set_error('ip', t('You may not block your own IP address.'));
  }
  elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) == FALSE) {
    form_set_error('ip', t('Enter a valid IP address.'));
  }
}

 */

/**
 * Form submission handler for ip_ranges_form().
 */
function ip_ranges_form_submit($form, &$form_state) {
  $ip_start = trim($form_state['values']['ip-start']);
  $ip_start = preg_replace('/\s+/', '', $ip_start);
  $ip = $ip_start;

  if ($form_state['values']['ip-end']) {
    $ip_end = trim($form_state['values']['ip-end']);
    $ip_end = preg_replace('/\s+/', '', $ip_end);
    $ip.='-'.$ip_end;
  }

  $type = $form_state['values']['type'];
  db_insert('ip_ranges')
    ->fields(array('ip' => $ip, 'type' => $type))
    ->execute();

  $list = ($type == 'blacklist') ? 'blacklisted' : 'whitelisted';
  drupal_set_message(t('The IP address %ip has been %list.', array('%ip' => $ip, '%list' => $list)));
  $form_state['redirect'] = 'admin/config/people/ip-ranges';
  return;
}

/**
 * IP deletion confirm page.
 *
 * @see ip_ranges_delete_submit()
 */
function ip_ranges_delete($form, &$form_state, $bid, $ip) {
  $form['ip_range'] = array(
    '#type' => 'value',
    '#value' => $bid,
  );
  return confirm_form($form, t('Are you sure you want to delete ip/range %ip?', array('%ip' => str_replace('-',' - ',$ip))), 'admin/config/people/ip-ranges', t('This action cannot be undone.'), t('Delete'), t('Cancel'));
}

/**
 * Processes ip_ranges_delete form submissions.
 */
function ip_ranges_delete_submit($form, &$form_state) {
  $ip = $form_state['build_info']['args']['1'];
  $bid = $form_state['values']['ip_range'];
  db_delete('ip_ranges')
    ->condition('bid', $bid)
    ->execute();
  watchdog('user', 'Deleted %ip from banned ip-ranges', array('%ip' => $ip));
  drupal_set_message(t('The IP address/range %ip was deleted.', array('%ip' => str_replace('-',' - ',$ip))));
  $form_state['redirect'] = 'admin/config/people/ip-ranges';
}
