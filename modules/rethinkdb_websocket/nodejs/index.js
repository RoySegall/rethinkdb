var connection = null;
var r = require('rethinkdb');
var fs = require('fs');
var Pusher = require('pusher');
var obj = JSON.parse(fs.readFileSync('pusher.json', 'utf8'));

var pusher = new Pusher(obj);

r.connect( {host: 'localhost', port: 28015}, function(err, conn) {
  if (err) {
    throw err;
  }

  connection = conn;

  var row = r.row('template').match("example_create_node|example_create_comment");

  r.db('drupal').table('message_replica').filter(row).changes().run(conn, function(err, cursor) {
    cursor.each(function(connection, value) {
      pusher.trigger('activity_stream', value['new_val'].template, value['new_val']);
    });
  })
});
