<?php

namespace Drupal\h5p_xapi\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a Save Events User Endpoint
 * 
 * Returns a Group object (the course) identified by the id {orgUnitId}
 * this Group object contains a set of student objects who belong to that 
 * course, or returns an error message/empty array otherwise.
 * 
 * @RestResource(
 *   id = "h5p_xapi_save_events_user",
 *   label = @Translation("H5P XAPI Save Events User"),
 *   serialization_class = "",
 *   uri_paths = {
 *     "canonical" = "/h5p-xapi/save-events-user"
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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
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
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {
    $response_status = TRUE;

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

    $response = new ResourceResponse($response_status);

    return $response;
  }
}