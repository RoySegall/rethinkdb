/**
 * @file
 * Defines Javascript behaviors for the node module.
 */

(function ($, Drupal, Pusher) {

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

      var pusher = new Pusher('967f75fd40222f07fae5', {
        encrypted: true
      });

      var channel = pusher.subscribe('activity_stream');

      channel.bind('example_create_comment', function(data) {
        $context.find('#activity-stream .content').prepend(Drupal.t('A new comment was created. cid: ' + data.field_comment_reference) + "<br />");
      });

      channel.bind('example_create_node', function(data) {
        $context.find('#activity-stream .content').prepend(Drupal.t('A new node was created. nid: ' + data.field_node_reference) + "<br />");
      });
    }
  };

})(jQuery, Drupal, Pusher);
