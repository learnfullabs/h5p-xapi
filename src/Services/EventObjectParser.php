<?php

namespace Drupal\h5p_xapi\Services;

use Drupal\Core\Database\Connection;
use Psr\Log\LoggerInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Default implementation of the Event Object Parser.
 */
class EventObjectParser implements EventObjectParserInterface {

  /**
   * The logger channel factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

    /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new Event Object Parser object.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(LoggerChannelFactoryInterface $logger, MessengerInterface $messenger, Connection $database) {
    $this->logger = $logger->get('h5p_xapi');
    $this->database = $database;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function saveEventRawData($user_id, $node_id, $event_data = NULL) {
    /* At this point we can process the data */
    /* Initial saving of the Event Object in the MySQL Drupal DB */
    /* TODO: Save this in a MongoDB DB */
    try {
      $result = $this->database->insert('h5p_xapi_rawdata')
      ->fields([
        'nid' =>  $node_id,
        'uid' => $user_id,
        'event_data' => json_encode($event_data),
        'timestamp' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();
    } catch (\Exception $e) {
      watchdog_exception('h5p_xapi', $e);
      return FALSE;
    }

    return TRUE;
  }

}