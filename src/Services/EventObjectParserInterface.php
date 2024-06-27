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
   * @param int $user_id
   *   UID of the user triggering the event
   * @param int $node_id
   *   NID of the node the user is interacting with
   * @param object $event_data
   *   The event data.
   * @return number
   *   ID of the event_data row, will be used for the subsequent calls to other methods, 
   *   or -1 on error.
   */
  public function saveEventRawData($user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Author Data (the user interacting with the h5p object) in the
   * h5p_xapi_event_actor table
   *
   * @param int $user_id
   *   UID of the user triggering the event
   * @param int $node_id
   *   NID of the node the user is interacting with
   * @param object $event_data
   *   The event data.
   * @return bool
   *   TRUE on success or FALSE on error.
   */
  public function saveEventAuthorData($event_id, $user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Object Data (information about the H5P Object) in the
   * h5p_xapi_event_object table
   *
   * @param int $event_id
   *   Event ID returned from the method saveEventRawData(), this value is a primary
   *   key that should be passed to the other methods to createa relationships from other tables
   *   to the h5p_xapi_rawdata table
   * @param int $user_id
   *   UID of the user triggering the event
   * @param int $node_id
   *   NID of the node the user is interacting with
   * @param object $event_data
   *   The event data.
   * @return bool
   *   TRUE on success or FALSE on error.
   */
  public function saveEventObjectData($event_id, $user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Context Data (Context information about the H5P interaction) in the
   * h5p_xapi_event_context table
   *
   * @param int $event_id
   *   Event ID returned from the method saveEventRawData(), this value is a primary
   *   key that should be passed to the other methods to createa relationships from other tables
   *   to the h5p_xapi_rawdata table
   * @param int $user_id
   *   UID of the user triggering the event
   * @param int $node_id
   *   NID of the node the user is interacting with
   * @param object $event_data
   *   The event data.
   * @return bool
   *   TRUE on success or FALSE on error.
   */
  public function saveEventContextData($event_id, $user_id, $node_id, $event_data = NULL);

  /**
   * Save Event Result Data (Result information about the H5P interaction) in the
   * h5p_xapi_event_result table
   *
   * @param int $event_id
   *   Event ID returned from the method saveEventRawData(), this value is a primary
   *   key that should be passed to the other methods to createa relationships from other tables
   *   to the h5p_xapi_rawdata table
   * @param int $user_id
   *   UID of the user triggering the event
   * @param int $node_id
   *   NID of the node the user is interacting with
   * @param object $event_data
   *   The event data.
   * @return bool
   *   TRUE on success or FALSE on error.
   */
  public function saveEventResultData($event_id, $user_id, $node_id, $event_data = NULL);

}
