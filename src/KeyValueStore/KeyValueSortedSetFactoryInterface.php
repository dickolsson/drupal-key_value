<?php

namespace Drupal\key_value\KeyValueStore;

interface KeyValueSortedSetFactoryInterface {

  /**
   * @param string $collection
   *
   * @return \Drupal\key_value_list\KeyValueStore\KeyValueStoreSortedSetInterface
   */
  public function get($collection);

}
