<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 24/5/14
 * Time: 17:56
 */

namespace Drupal\ip_ranges\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;

/**
 * Defines a Block configuration entity class.
 *
 * @ContentEntityType(
 *   id = "ip_ranges",
 *   label = @Translation("IP Ranges"),
 *   controllers = {
 *     "list_builder" = "Drupal\ip_ranges\IPRangesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ip_ranges\IPRangesFormController",
 *       "edit" = "Drupal\ip_ranges\IPRangesFormController",
 *       "delete" = "Drupal\ip_ranges\Form\IPRangesDeleteForm",
 *     },
 *   },
 *   base_table = "ip_ranges",
 *   admin_permission = "ban IP address ranges",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "bid",
 *     "label" = "ip",
 *     "status" = "status",
 *   },
 *   links = {
 *     "edit-form" = "ip_ranges.edit_form",
 *     "delete-form" = "ip_ranges.delete_form",
 *   }
 * )
 */
class IpRanges extends ContentEntityBase implements ContentEntityInterface {

  public function getType() {
    return $this->get('type')->value;
  }

  public function getIpStart() {
    $ip = $this->get('ip')->value;
    if (strstr($ip, '-')) {
      $range = explode('-', $ip);
      return $range[0];
    }
    return $ip;
  }

  public function getIpEnd() {
    $ip = $this->get('ip')->value;
    if (strstr($ip, '-')) {
      $range = explode('-', $ip);
      return $range[1];
    }
    return NULL;
  }

  public function getIp() {
    return $this->get('ip')->value;
  }

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['bid'] = FieldDefinition::create('integer')
      ->setLabel(t('User Restrictions ID'))
      ->setDescription(t('The User Restrictions ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The User Restrictions UUID.'))
      ->setReadOnly(TRUE);

    $fields['ip'] = FieldDefinition::create('string')
      ->setLabel(t('IP'))
      ->setDescription(t('Text mask used for filtering restrictions.
      %: Matches any number of characters, even zero characters.
      _: Matches exactly one character.'))
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 31,
      ));

    $fields['status'] = FieldDefinition::create('boolean')
      ->setLabel(t('Restriction status'))
      ->setDescription(t('A boolean indicating whether the ip range is whitelisted or blacklisted.'))
      ->setSetting('default_value', 1)
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }
}