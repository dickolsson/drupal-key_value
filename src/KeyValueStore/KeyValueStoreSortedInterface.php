<?php

namespace Drupal\key_value\KeyValueStore;

interface KeyValueStoreSortedInterface {

  /**
   * @return integer
   */
  public function getCount();

  /**
   * @param float $start
   * @param float $stop
   *
   * @return array
   */
  public function getRange($start, $stop = NULL);

}
