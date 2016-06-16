[![Build Status](https://travis-ci.org/RoySegall/rethinkdb.svg?branch=8.x-1.x)](https://travis-ci.org/RoySegall/rethinkdb)

# RethinkDB for Drupal

This is an ORM for RethinkDB. Which mean the module does not intend to replace
the default DB drivers but to create entity representation of your RethinkDB in
your Drupal installation.

## Setting up
1. install [RethinkDB](http://rethinkdb.com/docs/install)
2. In the `settings.php` file add the RethinkDB connection credentials as listed
below:
```php
<?php

$settings['rethinkdb'] = array(
 'database' => 'drupal8',
 'host' => 'localhost',
);

```
# CRUD operations:
## Creating
## Loading
## Updating
## Deleting

# Query

## Writing you custom entity
You can look in the `rethinkdb_example` module on how to set up an entity,
using the CRUD API or query against the RethinkDB database.

## Contribution
Any PR is more than welcome. When creating the PR please ping me so I could know
a contribution has been done. 10x.
