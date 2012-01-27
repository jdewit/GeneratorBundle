AvroGeneratorBundle
====================
Generate code similar in structure to the 
FOSUserBundle. With this bundle you can generate or update all crud code for a specific entity or specific classes.

FYI: Updating overwrites the original file.

Status
======

The bundle is a work in progress but is working. 
Currently it only provides support for Doctrine ORM.

The code still needs to get cleaned up a 
fair bit and tests still need to be made. Any help would be much appreciated!

Optional Dependencies
=====================

The view generator creates views with classes for grid960 and twitter bootstrap.

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

Generate a bundle skeleton with:

``` bash
$ php app/console generate:avro:bundle
```

Generate an entity and entityManager with:

``` bash
$ php app/console generate:avro:entity
```

Generate crud with:

``` bash
$ php app/console generate:avro:crud
```

Generate a formType and formHandler with:

``` bash
$ php app/console generate:avro:form
```

Generate views with:

``` bash
$ php app/console generate:avro:view
```
Generate a services.yml file with:

``` bash
$ php app/console generate:avro:service
```

Generate a formType and formHandler with:

``` bash
$ php app/console generate:avro:form
```


