/**
 * @file
 * Default JavaScript file for H5P XAPI module.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.h5pXapi = {
    attach: function (context, settings) {
      const elements = once('h5pxapiLibrary', '.h5p-iframe-wrapper', context);

      elements.forEach(function(index){
        let userId = drupalSettings.h5pxapi.userId;
        let nodeId = drupalSettings.h5pxapi.nodeId;

        H5P.externalDispatcher.on('xAPI', function (event) {
          /* TODO
           *
           * - make a fetch call to send a POST request to /h5p-xapi/save-events-user
           * - check for errors and other exceptions 
           * - OPTIONAL: to parse the event.data.statement object before sending it */
          let data = {
            user_id: userId,
            node_id: nodeId,
            h5p_event: event.data.statement
          };

          /* TODO
           * 
           * - Check for errors, success values, and return values also
           * - Make a fetch() call to /session/token' save the return value somehow 
           * - use that value in the X-Csrf-Token header value
           * - Get session cookie (somehow)
           * - Change "Authorization" to "Cookie" */
          fetch("/h5p-xapi/save-events-user?_format=json", {
            body: JSON.stringify(data),
            headers: {
              "Authorization": "Basic ZGFucm9kdGVzdDp0ZXN0MTE=",
              "Content-Type": "application/json",
              "X-Csrf-Token": "QqwBBvo2rvacoaJjSGOxrfjklStTw2wqLMsYgHkaetI"
            },
            method: "POST"
          });

          console.log(event.data.statement);
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);