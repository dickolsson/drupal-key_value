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
    $key0 = $this->newKey();
    $value0 = $this->randomMachineName();
    $this->store->add($key0, $value0);
    $this->assertPairs(array($key0 => $value0));

    $key1 = $this->newKey();
    $value1 = $this->randomMachineName();
    $this->store->add($key1, $value1);
    $this->assertPairs(array($key1 => $value1));

    // Ensure it works to add sets with the same score.
    $key2 = $this->newKey();
    $value2 = $this->randomMachineName();
    $value3 = $this->randomMachineName();
    $value4 = $this->randomMachineName();
    $this->store->addMultiple(array(
      array($key2 => $value2),
      array($key2 => $value3),
      array($key2 => $value4),
    ));

    $count = $this->store->getCount();
    $this->assertEqual($count, 5, 'The count method returned correct count.');

    $value = $this->store->getRange($key1, $key2);
    $this->assertIdentical($value, array($value1, $value2, $value3, $value4));

    $new1 = $this->newKey();
    $this->store->add($new1, $value1);

    $value = $this->store->getRange($new1, $new1);
    $this->assertIdentical($value, array($value1), 'Member was successfully updated.');
    $this->assertRecords(5, 'Correct number of record in the collection after member update.');

    $value = $this->store->getRange($key1, $key1);
    $this->assertIdentical($value, array(), 'Non-existing range returned empty array.');

    $max_score = $this->store->getMaxScore();
    $this->assertEqual($max_score, $new1, 'The getMaxScore method returned correct score.');

    $min_score = $this->store->getMinScore();
    $this->assertEqual($min_score, $key0, 'The getMinScore method returned correct score.');
  }
}
