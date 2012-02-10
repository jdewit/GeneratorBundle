AvroGeneratorBundle
====================
Generate code similar in structure to the 
FOSUserBundle. With this bundle you can generate or update 
all classes related to an entity with just a few commands 
in the console.

This bundle generates code that is customised to my personal 
preferences so it won't be for everyone. However, I am open 
to collaborating with others in improving this bundle and 
making it more suitable for more people. 

FYI: Updating any classes overwrites the original file.

Status
======
The bundle is a work in progress but is working...most of the time :) 
Currently it only provides support for Doctrine ORM.

The code still needs to get cleaned up a 
fair bit and tests still need to be made. Any help would be much appreciated!

Dependencies
============
The view generator creates views with classes for <a href="http://twitter.github.com/bootstrap/index.html">twitter bootstrap 2.0</a>.

Installation
============
Add the `Avro` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Avro' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        ...
        $bundles[] = new Avro\GeneratorBundle\AvroGeneratorBundle();
    }
```

Add to deps file
    
```
[AvroGeneratorBundle]
    git=git://github.com/jdewit/AvroGeneratorBundle.git
    target=bundles/Avro/GeneratorBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ bin/vendors update
```

USAGE
=====
Enter the following in the console and follow the directions!

Generate a bundle skeleton with:

``` bash
$ php app/console generate:avro:bundle
```

Generate an entity and entityManager with:

``` bash
$ php app/console generate:avro:entity
```

Generate a controller and views with:

``` bash
$ php app/console generate:avro:crud
```

Generate a controller with:

``` bash
$ php app/console generate:avro:controller
```

Generate views with:

``` bash
$ php app/console generate:avro:view
```

Generate a formType and formHandler with:

``` bash
$ php app/console generate:avro:form
```

Generate a services.yml file for formType, formHandler, and entityManager:

``` bash
$ php app/console generate:avro:service
```

Generate behat features with:

``` bash
$ php app/console generate:avro:feature
```

SOMEDAY FEATURES
================

- MongoDB support
- CouchDB support
- speech activation and/or a pet monkey
