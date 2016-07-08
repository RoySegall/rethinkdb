[![Build Status](https://travis-ci.org/RoySegall/rethinkdb.svg?branch=8.x-1.x)](https://travis-ci.org/RoySegall/rethinkdb)

# RethinkDB for Drupal

This is an ORM for RethinkDB. Which mean the module does not intend to replace
the default DB drivers but to create entity representation of your RethinkDB in
your Drupal installation.

## Setting up
1. install [RethinkDB](http://rethinkdb.com/docs/install)
2. Enable the module.

The connection settings to the DB can be changed at `admin/config/rethinkdb/rethinkdbconfig` 

## Writing you custom entity
In order to define an entity based on RethinkDB storage you need to apply two
things:
1. Add the ```rethink = TRUE``` settings in the annotatoin.
2. The entity class need to extends from `AbstractRethinkDbEntity`

You can have a look in the next example or in [RethinkDB example module](https://github.com/RoySegall/rethinkdb/blob/8.x-1.x/modules/rethinkdb_example/src/Entity/RethinkMessages.php):

```php
/**
 * @ContentEntityType(
 *   id = "rethinkdb_message",
 *   label = @Translation("RethinkDB messages"),
 *   base_table = "rethinkdb_messages",
 *   translatable = FALSE,
 *   rethink = TRUE,
 *   entity_keys = {}
 * )
 */
class RethinkMessages extends AbstractRethinkDbEntity {

}
```

# CRUD operations
The CRUD operations are not different from the Drupal's entity API:

### Creating
```php
$message = RethinkMessages::create(['title' => 'Foo', 'body' => 'Bar']);
$results = $message->save();
```

The returned value is an array with some information from RethinkDB on the
creation operation. You are probably interested with the ID of the entity you
just created. Unlike schematic DBs, RethinkDB is a NoSQL server which mean the
ID's of the rows, or documents, is a simple hash.

### Loading

Loading the entity is very easy:

```php
$document = RethinkMessages::load(reset($results['generated_keys']));
```

Or
```php
$document = RethinkMessages::load('404bef53-4b2c-433f-9184-bc3f7bda4a15');
```

You can get the values of the document:

```php
$document->get('title');
$document->get('body');
$document->getValues();
```

### Updating
Don't worry. It's very easy:

```php
$document->set('title', 'new title')->save();
```

### Deleting

As before, it's EASY:

```php
$document->delete();
```

# Query

### Basic query

Let's warm up with some nice query:

```php
    $messages = \Drupal::entityQuery('rethinkdb_message')
      ->execute();
```

### condition

You can apply all the operations you know: ``` =, !=, >, >=, <, <=, CONTAINS ```:

```php
    $messages = \Drupal::entityQuery('rethinkdb_message')
      ->condition('title', 'fo', 'CONTAINS')
      ->execute();
```

__When executing the query, the query will return the objects and not the IDs of
 the matching documents.__

## Contribution

Any PR is more than welcome. When creating the PR please ping me so I could know
a contribution has been done. 10x.
