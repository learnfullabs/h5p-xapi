<?php

namespace Drupal\h5p_xapi\Services;

/**
 * Parse the Event Object and save the parsed data in DB tables.
 *
 */
interface EventObjectParserInterface {

  /**
   * Save Event Data in the h5p_xapi_rawdata table for initial review
   *
   * @param object $event_data
   *   The event data.
   * @return bool
   *   TRUE on success or FALSE on error
   */
  public function saveEventRawData($user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Author Data (the user interacting with the h5p object) in the
   * h5p_xapi_event_actor table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventAuthorData($user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Object Data (information about the H5P Object) in the
   * h5p_xapi_event_object table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventObjectData($user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Context Data (Context information about the H5P interaction) in the
   * h5p_xapi_event_context table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventContextData($user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Result Data (Result information about the H5P interaction) in the
   * h5p_xapi_event_result table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventResultData($user_id, $node_id, $event_data = NULL);

}
