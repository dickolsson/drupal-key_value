<?php

namespace Drupal\key_value\Tests;

/**
 * Tests the sorted set key-value database storage.
 *
 * @group key_value
 */
class DatabaseStorageSortedSetTest extends DatabaseStorageSortedTestBase {

  /**
   * @var \Drupal\key_value\KeyValueStore\KeyValueStoreListInterface
   */
  protected $store;

  public function setUp() {
    parent::setUp();
    $this->store = \Drupal::service('keyvalue.sorted_set')->get($this->collection);
  }

  public function testCalls() {
    $key0 = (string) microtime(TRUE);
    $value0 = $this->randomName();
    $this->store->add($key0, $value0);
    $this->assertPairs(array($key0 => $value0));

    $key1 = (string) microtime(TRUE);
    $value1 = $this->randomName();
    $this->store->add($key1, $value1);
    $this->assertPairs(array($key1 => $value1));

    // Ensure it works to add sets with the same score.
    $key2 = (string) microtime(TRUE);
    $value2 = $this->randomName();
    $value3 = $this->randomName();
    $value4 = $this->randomName();
    $this->store->addMultiple(array(
      array($key2 => $value2),
      array($key2 => $value3),
      array($key2 => $value4),
    ));

    $count = $this->store->getCount();
    $this->assertEqual($count, 5, 'The count method returned correct count.');

    $value = $this->store->getRange($key1, $key2);
    $this->assertIdentical($value, array($value1, $value2, $value3, $value4));

    $new1 = (string) microtime(TRUE);
    $this->store->add($new1, $value1);

    $value = $this->store->getRange($new1, $new1);
    $this->assertIdentical($value, array($value1), 'Member was successfully updated.');
    $this->assertCount(5, 'Correct number of record in the collection after member update.');

    $value = $this->store->getRange($key1, $key1);
    $this->assertIdentical($value, array(), 'Non-existing range returned empty array.');
  }
}
