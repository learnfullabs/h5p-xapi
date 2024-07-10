<?php

namespace Drupal\h5p_xapi\Services;

use Drupal\Core\Database\Connection;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
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

      $number = $this->database->select('h5p_xapi_rawdata', 'f')
      ->countQuery()
      ->execute()
      ->fetchField();

      if ($number) {
        return $number;
      }

    } catch (\Exception $e) {
      watchdog_exception('h5p_xapi', $e);
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function saveEventAuthorData($event_id, $user_id, $node_id, $event_data = NULL) {
    if ($event_id === NULL) {
      return FALSE;
    }
    
    if (empty($event_data['actor']['name']) || empty($event_data['actor']['mbox'])) {
      return FALSE;
    } else {
      $actor_name = $event_data['actor']['name'];
      $actor_mbox = $event_data['actor']['mbox'];
    }

    try {
      $result = $this->database->insert('h5p_xapi_event_actor')
      ->fields([
        'event_id' => $event_id,
        'nid' =>  $node_id,
        'uid' => $user_id,
        'name' => $actor_name,
        'mailbox' => $actor_mbox,
        'timestamp' => \Drupal::time()->getRequestTime(),
      ])
      ->execute();
    } catch (\Exception $e) {
      watchdog_exception('h5p_xapi', $e);
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function saveEventObjectData($event_id, $user_id, $node_id, $event_data = NULL) {
    if ($event_id === NULL) {
      return FALSE;
    }

    /* Let's get the 'verb' information first and save it */
    if (isset($event_data["verb"]) && !empty($event_data["verb"])) {
      $verb_object = (object) $event_data["verb"];
      $verb_object_id = $verb_object->id ?? "";
      $verb_object_name = $verb_object->display["en-US"] ?? "";
    }

    if (isset($event_data["object"]) && !empty($event_data["object"])) {
      $event_object = (object) $event_data["object"];
      $event_object_id = $event_object->id;
      $event_object_type = $event_object->objectType;
      $event_object_definition = (object) $event_object->definition;
      $event_object_name = $event_object_definition->name["en-US"] ?? "";
      $event_object_description = $event_object_definition->description["en-US"] ?? "";
      $event_object_type = $event_object_definition->type ?? "";
      $event_object_interaction_type = $event_object_definition->interactionType ?? "";
      $event_object_definition->extensions["http://h5p.org/x-api/h5p-local-content-id"];
      
      try {
        $result = $this->database->insert('h5p_xapi_event_object')
        ->fields([
          'event_id' => $event_id,
          'nid' =>  $node_id,
          'uid' => $user_id,
          'object_id' => $event_object_type,
          'verb_id' => $verb_object_id,
          'verb_name' => $verb_object_name,
          'name' => $event_object_name,
          'description' => $event_object_description,
          'type' => $event_object_type,
          'interaction_type' => $event_object_interaction_type,
          'correct_responses_pattern' => "",
          'timestamp' => \Drupal::time()->getRequestTime(),
        ])
        ->execute();
      } catch (\Exception $e) {
        watchdog_exception('h5p_xapi', $e);
        return FALSE;
      }
    } else {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function saveEventContextData($event_id, $user_id, $node_id, $event_data = NULL) {
    if ($event_id === NULL) {
      return FALSE;
    }

    if (isset($event_data["context"]) && !empty($event_data["context"])) {
      $event_context = (object) $event_data["context"];
      $event_context_activities = (object) $event_context->contextActivities;
      $event_context_parent_id = $event_context_activities->parent["0"]["id"] ?? "";
      $event_context_category_id = $event_context_activities->category["0"]["id"] ?? "";
      $event_content_extensions = $event_context->extensions["http://id.tincanapi.com/extension/ending-point"] ?? "";
    
      try {
        $result = $this->database->insert('h5p_xapi_event_context')
        ->fields([
          'event_id' => $event_id,
          'nid' =>  $node_id,
          'uid' => $user_id,
          'parent_id' => $event_context_parent_id,
          'category_id' => $event_context_category_id,
          'extensions' => $event_content_extensions,
          'timestamp' => \Drupal::time()->getRequestTime(),
        ])
        ->execute();
      } catch (\Exception $e) {
        watchdog_exception('h5p_xapi', $e);
        return FALSE;
      }
    } else {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function saveEventResultData($event_id, $user_id, $node_id, $event_data = NULL) {
    if ($event_id === NULL) {
      return FALSE;
    }

    /* if $event_data["result"] is not set, it doesn't mean there
     * was an issue with the call, this array is only set when the
     * user clicks to validate the result */
    if (isset($event_data["result"]) && !empty($event_data["result"])) {
      $event_result = (object) $event_data["result"];
      $event_result_completion = $event_result->completion ?? 0;
      
      if (isset($event_result->success) && !empty($event_result->success)) {
        if ($event_result->success === TRUE) {
          $event_result_success = 1;
        } else {
          $event_result_success = 0;
        }
      } else {
        $event_result_success = 0;
      }

      if (isset($event_result->score) && !empty($event_result->score)) {
        $result_min = $event_result->score["min"] ?? 0;
        $result_max = $event_result->score["max"] ?? 0;
        $result_scored = $event_result->score["raw"] ?? 0;
        $result_scaled = $event_result->score["scaled"] ?? 0.0;
      }

      $event_result_duration = $event_result->duration ?? "";
      $event_result_response = $event_result->response ?? "";

      try {
        $result = $this->database->insert('h5p_xapi_event_result')
        ->fields([
          'event_id' => $event_id,
          'nid' =>  $node_id,
          'uid' => $user_id,
          'completion' => $event_result_completion,
          'success' => $event_result_success,
          'duration' => $event_result_duration,
          'result_min' => $result_min,
          'result_max' => $result_max,
          'result_scored' => $result_scored,
          'result_scaled' => $result_scaled,
          'response' => $event_result_response,
          'timestamp' => \Drupal::time()->getRequestTime(),
        ])
        ->execute();
      } catch (\Exception $e) {
        watchdog_exception('h5p_xapi', $e);
        return FALSE;
      }
    } else {
      if (isset($event_data["object"]) && !empty($event_data["object"])) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    return TRUE;
  }
}