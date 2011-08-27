<?php

/**
 * @file
 * Page callback file for the ip ranges module.
 */

/**
 * Menu callback. Display banned IP ranges.
 *
 */
function ip_ranges_page() {
  $rows = array();
  $header = array(t('banned IP addresses'),t('list type'), t('Operations'));
  $ip_list = ip_ranges_get_ip_list();
  foreach ($ip_list as $ip) { 
   $rows[] = array(
      $ip->ip,
      $ip->type,
      l(t('delete'), "admin/config/people/ip-ranges/delete/$ip->bid"),
    );
  }

  $build['ip_ranges_form'] = drupal_get_form('ip_ranges_form');

  $build['ip_ranges_banning_table'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
  );

  return $build;}

/**
 * Define the form for banning IP addresses.
 *
 * @ingroup forms
 * @see ban_ip_form_validate()
 * @see ban_ip_form_submit()
 */
function ip_ranges_form($form, $form_state) {
  $form['ip'] = array(
    '#title' => t('IP address'),
    '#type' => 'textfield',
    '#size' => 48,
	'#required' => 'true',
    '#maxlength' => 40,
    '#description' => t('Enter IP-address (100.100.100.100) or address range (100.100.100.100-100.100.100.150).'),
  );
  $form['type'] = array(
    '#title' => t('List type'),
    '#type' => 'select',
    '#multiple' => FALSE,
    '#options' => array('blacklist' => 'blacklist', 'whitelist' => 'whitelist'),
    '#default_value' => 'blacklist',
    '#required' => 'true',
    '#description' => t('Enter list type.'),
  );
 
 $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Add'),
  );
  $form['#submit'][] = 'ip_ranges_form_submit';
  // No working validation yet. Probably the best solution is to modify the form to have separete fields for the ranges and validate those like normal ip-addresses.
  //$form['#validate'][] = 'ban_ip_form_validate';
  return $form;
}

/*
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

function ip_ranges_form_submit($form, &$form_state) {
  $ip = trim($form_state['values']['ip']);
  $ip = preg_replace('/\s+/', '', $ip);
  $type = $form_state['values']['type'];
  db_insert('ip_ranges')
    ->fields(array('ip' => $ip, 'type' => $type))
    ->execute();
  drupal_set_message(t('The IP address %ip has been banned.', array('%ip' => $ip)));
  $form_state['redirect'] = 'admin/config/people/ip-ranges';
  return;
}
/**
 * IP deletion confirm page.
 *
 * @see ban_ip_delete_submit()
 */
function ip_ranges_delete($form, &$form_state, $bid) {
 $form['ip_range'] = array(
    '#type' => 'value',
    '#value' => $bid,
  );
  return confirm_form($form, t('Are you sure you want to delete %ip?', array('%ip' => $bid['ip'])), 'admin/config/people/ip-ranges', t('This action cannot be undone.'), t('Delete'), t('Cancel'));
}

/**
 * Process ban_ip_delete form submissions.
 */
function ip_ranges_delete_submit($form, &$form_state) {
 $banned_ip = $form_state['values']['ip_range'];
  db_delete('ip_ranges')
    ->condition('bid', $banned_ip['bid'])
    ->execute();
  watchdog('user', 'Deleted %ip', array('%ip' => $banned_ip['ip']));
  drupal_set_message(t('The IP address/range %ip was deleted.', array('%ip' => $banned_ip['ip'])));
  $form_state['redirect'] = 'admin/config/people/ip-ranges';
}