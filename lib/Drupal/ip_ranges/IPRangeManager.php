<?php

namespace Drupal\ip_ranges;

use Drupal\Core\Database\Connection;

class IPRangeManager {

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function ipIsBanned($ip) {
    $args = array(
      ':ip' => ip2long($ip),
    );

    $sql = "SELECT 1 FROM {ip_ranges} WHERE type = :type AND ip_lower <= :ip AND ip_higher >= :ip";
    $banned = (
      $this->connection->queryRange($sql, 0, 1, $args + array(':type' => 0))->fetchField() &&
      !$this->connection->queryRange($sql, 0, 1, $args + array(':type' => 1))->fetchField()
    );

    return $banned;
  }

}