<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 24/5/14
 * Time: 17:56
 */

namespace Drupal\ip_ranges\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a Block configuration entity class.
 *
 * @ConfigEntityType(
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
 *   admin_permission = "ban IP address ranges",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *     "plugin" = "plugin"
 *   },
 *   links = {
 *     "edit-form" = "ip_ranges.edit_form",
 *     "delete-form" = "ip_ranges.delete_form",
 *   }
 * )
 */
class IpRanges extends ConfigEntityBase implements ConfigEntityInterface {

}