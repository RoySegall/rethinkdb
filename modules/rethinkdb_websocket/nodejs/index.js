r = require('rethinkdb');
var connection = null;

r.connect( {host: 'localhost', port: 28015}, function(err, conn) {
  if (err) throw err;
  connection = conn;

  r.db('drupal8').table('node_replica').changes().run(conn, function(err, cursor) {
    cursor.each(function(connection, value) {
      console.log(value['new_val'].title);
    });
  })
});
