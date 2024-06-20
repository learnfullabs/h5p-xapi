/**
 * @file
 * Default JavaScript file for Block Class.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.blockClass = {
    attach: function (context, settings) {
      console.log("Test Load");
    }
  };
})(jQuery, Drupal, drupalSettings);