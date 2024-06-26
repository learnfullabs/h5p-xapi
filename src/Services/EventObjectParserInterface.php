<?php

namespace Drupal\h5p_xapi\Services;

/**
 * Parse the Event Object and save the parsed data in DB tables.
 *
 */
interface EventObjectInterface {

  const RESULT_ID = 'result_id';

  const CURRENT_QUESTION = 'current_question';

  const TEMP_ID = 'temp';

  /**
   * Save Event Data in the h5p_xapi_rawdata table for initial review
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveRawData($event_data = NULL);

  /**
   * Save Event Author Data (the user interacting with the h5p object) in the
   * h5p_xapi_event_actor table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventAuthorData($event_data = NULL);

  /**
   * Save Event Object Data (information about the H5P Object) in the
   * h5p_xapi_event_object table
   *
   * @param object $event_data
   *   The event data.
   */
  public function saveEventObjectData(Quiz $quiz);

  /**
   * Get the current user's result for a Quiz in the session
   *
   * @param Quiz $quiz
   *   The quiz.
   */
  public function getResult(Quiz $quiz = NULL);

  /**
   * Get the current user's temporary result ID (for feedback/review).
   *
   */
  public function getTemporaryResult();

  /**
   * Set a quiz result for the current user.
   *
   * @param QuizResult $quiz_result
   *   The quiz result.
   */
  public function setResult(QuizResult $quiz_result);

  /**
   * Set the user's temporary result ID (for feedback/review).
   *
   * @param QuizResult $quiz_result
   *   The quiz result.
   */
  public function setTemporaryResult(QuizResult $quiz_result);

  /**
   * Get the user's current question index for a quiz in the session.
   *
   * @param Quiz $quiz
   *   The quiz.
   *
   * @return int
   *   Question index starting at 1.
   */
  public function getCurrentQuestion(Quiz $quiz);

  /**
   * Set the user's current question.
   *
   * @param Quiz $quiz
   *   The quiz ID.
   *
   * @param int $current_question
   *   The current question, starting at 1.
   */
  public function setCurrentQuestion(Quiz $quiz, int $current_question);

}
