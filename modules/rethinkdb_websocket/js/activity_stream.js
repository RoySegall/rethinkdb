/**
 * @file
 * Defines Javascript behaviors for the rethinkdb websocket module.
 */

(function ($, Drupal, Pusher, drupalSettings) {

  'use strict';

  /**
   * Handling the activity stream.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Create a small activity stream.
   */
  Drupal.behaviors.activityStream = {
    attach: function (context) {
      var $context = $(context);

      var pusher = new Pusher(drupalSettings.rethinkdb_websocket.app_id, {encrypted: true});

      var channel = pusher.subscribe('activity_stream');

      channel.bind('example_create_comment', function(data) {
        $context.find('#activity-stream .content').prepend(data.message[0] + "<br />");
      });

      channel.bind('example_create_node', function(data) {
        $context.find('#activity-stream .content').prepend(data.message[0] + "<br />");
      });
    }
  };

})(jQuery, Drupal, Pusher, drupalSettings);
