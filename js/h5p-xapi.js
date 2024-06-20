/**
 * @file
 * Default JavaScript file for Block Class.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.blockClass = {
    attach: function (context, settings) {
      console.log("Test Load");
      console.log(drupalSettings.h5pxapi.userId);
      H5P.externalDispatcher.on('xAPI', function (event) {
        console.log(event.data.statement);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);