<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 24/5/14
 * Time: 18:08
 */

namespace Drupal\ip_ranges;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;

class IPRangesListBuilder extends EntityListBuilder {

  /**
   * Constructs a new UserRestrictionsListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Banned IP addresses');
    $header['type'] = t('List type');

    return $header + parent::buildHeader();
  }

  public function emptyText() {
    return $this->t('There are no @label yet.', array('@label' => $this->entityType->getLabel()));
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    $row['type'] = $entity->getType() ? t('whitelist') : t('blacklist');
    return $row + parent::buildRow($entity);
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormID() {
    return 'ip_ranges_admin_form';
  }

}