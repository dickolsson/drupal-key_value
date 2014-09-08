<?php

namespace Drupal\key_value\Tests;

use Drupal\Component\Utility\String;
use Drupal\simpletest\KernelTestBase;

abstract class DatabaseStorageSortedTestBase extends KernelTestBase {

  static public $modules = array('serialization', 'key_value');

  /**
   * @var string
   */
  protected $collection;

  /**
   * @var \Drupal\Component\Serialization\SerializationInterface
   */
  protected $serializer;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  public function setUp() {
    parent::setUp();
    $this->installSchema('key_value', array('key_value_sorted'));

    $this->collection = $this->randomMachineName();
    $this->serializer = \Drupal::service('serialization.phpserialize');
    $this->connection = \Drupal::service('database');
  }

  public function assertPairs($expected_pairs) {
    $result = $this->connection->select('key_value_sorted', 't')
      ->fields('t', array('name', 'value'))
      ->condition('collection', $this->collection)
      ->condition('name', array_keys($expected_pairs), 'IN')
      ->execute()
      ->fetchAllAssoc('name');

    $expected_count = count($expected_pairs);
    $this->assertIdentical(count($result), $expected_count, String::format('Query affected !count records.', array('!count' => $expected_count)));
    foreach ($expected_pairs as $key => $value) {
      $this->assertIdentical($this->serializer->decode($result[$key]->value), $value, String::format('Key !key have value !value', array('!key' => $key, '!value' => $value)));
    }
  }

  public function assertCount($expected, $message = NULL) {
    $count = $this->connection->select('key_value_sorted', 't')
      ->fields('t')
      ->condition('collection', $this->collection)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEqual($count, $expected, $message ? $message : String::format('There are !count records.', array('!count' => $expected)));
  }
}
