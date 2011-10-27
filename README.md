AvroGeneratorBundle
====================
Generate code similar in structure to the 
FOSUserBundle. 

Status
======

The bundle is a work in progress. The code still needs to get cleaned up a 
fair bit and tests still need to be made. Any help would be much appreciated!

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
$ php bin/vendors install
```

USAGE
=====

Generate a bundle with:

``` bash
$ php app/console generate:avro:bundle
```

Generate an entity with:

``` bash
$ php app/console generate:avro:entity
```

Generate crud with:

``` bash
$ php app/console generate:avro:crud
```

