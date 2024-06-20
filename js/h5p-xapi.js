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
        let nodeId = drupalSettings.h5pxapi.nodeId;

        console.log(userId);
        console.log(nodeId);

        H5P.externalDispatcher.on('xAPI', function (event) {
          console.log(event.data.statement);
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);