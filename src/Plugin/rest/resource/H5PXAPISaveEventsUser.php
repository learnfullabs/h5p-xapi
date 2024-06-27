<?php

namespace Drupal\h5p_xapi\Plugin\rest\resource;

use Psr\Log\LoggerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\Core\Database\Connection;

/**
 * Provides a Save Events User Endpoint
 * 
 * @RestResource(
 *   id = "h5p_xapi_save_events_user",
 *   label = @Translation("H5P XAPI Save Events User API"),
 *   serialization_class = "",
 *   uri_paths = {
 *     "create" = "/h5p-xapi/save-events-user"
 *   }
 * )
 */
class H5PXAPISaveEventsUser extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
 
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $database
   *  The database connection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user, EntityTypeManagerInterface $entity_type_manager, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('custom_rest'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }

  /**
   * Responds to POST requests.
   *
   * Saves H5P XAPI data when user interacts with the H5P Resource
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {

    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->isAuthenticated()) {
      // Display the default access denied page.
      throw new AccessDeniedHttpException('Access Denied.');
    }

    // Check if we have enough data to work with.
    if (empty($data['user_id']) || empty($data['node_id']) || empty($data["h5p_event"])) {
      // If an exception was thrown at this stage, there was a problem
      // decoding the data. Throw a 400 http exception.
      throw new BadRequestHttpException('Bad data format. Please make sure you have all data set in the request.');
    }

    /* Check if node_id corresponds to an existing H5P Resource */
    $h5p_resource = $this->entityTypeManager->getStorage('node')->load($data["node_id"]);

    if (empty($h5p_resource)) {
      throw new BadRequestHttpException('H5P Resource does not exist, check your node_id.');
    } else {
      if ($h5p_resource->bundle() != "h5p") {
        throw new BadRequestHttpException('The node_id supplier does not correspond to an H5P Resource, check your node_id.');
      }
    }

    /* Check if user_id corresponds to an existing User */
    $user_storage = $this->entityTypeManager->getStorage('user');

    $user_ids = $user_storage->getQuery()
          ->accessCheck(FALSE)
          ->condition('uid', $data['user_id'])
          ->execute();

    if (count($user_ids) < 1) {
      throw new BadRequestHttpException('User does not exist, check your user_id.');
    }

    /** @var \Drupal\h5p_xapi\Services\EventObjectParserInterface $event_object_parser */
    $event_object_parser = \Drupal::service('h5p_xapi.event_object_parser');

    $user_id = $data["user_id"];
    $node_id = $data["node_id"];

    if (!($event_id = $event_object_parser->saveEventRawData($user_id, $node_id, $data["h5p_event"]))){
      throw new BadRequestHttpException('Error when saving the Raw Data, check database logger table for more information."');
    }

    if (!$event_object_parser->saveEventAuthorData($event_id, $user_id, $node_id, $data["h5p_event"])){
      throw new BadRequestHttpException('Error when saving the Author Data, check database logger table for more information."');
    }

    if (!$event_object_parser->saveEventObjectData($event_id, $user_id, $node_id, $data["h5p_event"])){
      throw new BadRequestHttpException('Error when saving the Event Object Data, check database logger table for more information."');
    }

    if (!$event_object_parser->saveEventContextData($event_id, $user_id, $node_id, $data["h5p_event"])){
      throw new BadRequestHttpException('Error when saving the Event Context Data, check database logger table for more information."');
    }

    if (!$event_object_parser->saveEventResultData($event_id, $user_id, $node_id, $data["h5p_event"])){
      throw new BadRequestHttpException('Error when saving the Event Result Data, check database logger table for more information."');
    }

    $response = new ResourceResponse("Event Data saved with success !");

    return $response;
  }
}