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
        
        /* TODO: Check if userId or nodeId are empty, if so, stop execution (return FALSE) */
        if (userId == 0) {
          return;
        }

        H5P.externalDispatcher.on('xAPI', function (event) {
          /* TODO
           *
           * - OPTIONAL: to parse the event.data.statement object before sending it */
          let data = {
            user_id: userId,
            node_id: nodeId,
            h5p_event: event.data.statement
          };
      
          fetch("/session/token", {
            method: "GET",
          })
          /* Request was success the data is the server response */
          .then(response => {
            const csrfToken = response.text();
            console.log(csrfToken);
            return csrfToken;
          }).then((csrfToken) => {
            fetch("/h5p-xapi/save-events-user?_format=json", {
              body: JSON.stringify(data),
              headers: {
                // We don't need this header if user is logged in => "Authorization": "Basic ####",
                "Content-Type": "application/json",
                "X-Csrf-Token": csrfToken,
              },
              method: "POST"
            })
            .then(response => {
              if (!response.ok) {
                  throw new Error('HTTP error ' + response.status);
              }
  
              return response.json();
          })
          .then(data => console.log(data)) /* Request was success the data is the server response */
          .catch(error => console.error(error));
          
          });

        });

      });
    }
  };
})(jQuery, Drupal, drupalSettings);