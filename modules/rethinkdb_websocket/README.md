RethinkDB websocket
====

Example on how to implement websocket with Drupal, Pusher and RethinkDB.

Dependencies
===

You need to have:

1. [NodeJS](http://www.nodejs.org) installed and npm as well.
2. [Pusher](http://www.pusher.com) account with a dedicated application.
3. [Bower](http://bower.io)
4. The Message module.

Set up
===
Go to `modules/rethinkdb_websocket` and install bower packages:

```js
bower install
```

Go to `modules/rethinkdb_websocket/nodejs` and:

1. install npm packages ```js npm install```
2. Copy `pusher.example.json` to `pusher.json` and insert your Pusher 
application setting.

Go to `admin/config/rethinkdb_websocket/rethinkdbwebsocket` and insert the your
Pusher app ID.

Enable the Message example module - `drush en message_example -y`

Go to `admin/configuration/rethinkdb/replica_manage` and `Create table replica`
for the Message entity.

Watch the magic beings
===
Go to `rethinkdb_websocket/activity_stream`. Open a secondary window and start 
to  create content or comments.
