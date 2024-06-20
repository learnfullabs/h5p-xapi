/**
 * @file
 * Default JavaScript file for Block Class.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.blockClass = {
    attach: function (context, settings) {
      const elements = once('wrapped', '.h5p-iframe-wrapper', context);

      elements.forEach(function(index){
        console.log("Test Load");
        let userId = drupalSettings.h5pxapi.userId;

        H5P.externalDispatcher.on('xAPI', function (event) {
          console.log(event.data.statement);
          console.log(userId);
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);