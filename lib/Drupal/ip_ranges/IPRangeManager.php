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

    $sql = "SELECT * FROM {ip_ranges} WHERE type = :type AND ip_lower <= :ip AND ip_higher >= :ip";
    $banned = (
      $this->connection->queryRange($sql, 0, 1, $args + array(':type' => 0))->fetchField() &&
      !$this->connection->queryRange($sql, 0, 1, $args + array(':type' => 1))->fetchField()
    );

    return $banned;
  }

  /**
   * Checks if the given ip-address matches the given range.
   *
   * @param $ip
   *   Black- or whitelisted ip-address range.
   * @param $current_ip
   *   Ip to be checked against the list, usually users current ip-address.
   *
   * @return
   *   TRUE if the ip is on the list, FALSE if it is not.
   */
  public function ip_ranges_check_range($ip, $current_ip) {
    $ip = explode('-', $ip);
    list($lower, $upper) = $ip;
    $lower_dec = (float) sprintf("%u", ip2long($lower));
    $upper_dec = (float) sprintf("%u", ip2long($upper));
    $ip_dec = (float) sprintf("%u", ip2long($current_ip));
    return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
  }

}