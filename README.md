AvroGeneratorBundle
-------------------
Generate Symfony2 code from the command line!
With this bundle you can generate or update 
all classes related to an entity with just a few commands!

This bundle is highly configurable. Create your own templates and render them 
in the path of your choice. 

Status
------
The bundle is a work in progress but is working...most of the time :) 
Currently it only provides support for Doctrine ORM. 

The code still needs to get cleaned up a fair bit but the code that 
it does generate is pretty solid. 

Any help would be much appreciated!

Dependencies
------------
- none 

Configuration
-------------
``` php
avro_generator:
    style: Avro // choose from several built in styles
    overwrite: false // overwrite current code if true, write to Temp folder if false
```

You can override and build onto the default styles through paramaters in a yml configuration file. Checkout 
the <a href="http://www.github.com/jdewit/GeneratorBundle/Resources/config/avro.yml">avro.yml</a> config file 
for a good example on how to specify your templates. 

The generator bundle parses all nodes specified under the parameter avro_generator.files
``` php
//config.yml
parameters:
    avro_generator.files
        list_view: 
            filename: 'Resources/views/%s/list.html.twig' // the target location for the generated file relative to the bundle path
            template: 'AvroGeneratorBundle:Skeleton/Resources/views/Avro/list.html.twig' //the path to the template file 
            tags: ['view', 'crud'] // tags allow you to specify which files you want to generate
```

The generator also allows you pass your own parameters to the template and call manipulator services to manipulate code

``` php
parameters:
    avro_generator.files:
        controller: 
            filename: 'Controller/%sController.php'
            template: 'AvroGeneratorBundle:Skeleton/Controller/Avro/Controller.php'
            parameters: // specify custom parameters you want available in your template
                actions: ['list', 'new', 'edit', 'delete', 'restore', 'import']
            tags: ['controller', 'crud']
            manipulator: 
                service: 'avro_generator.routing.manipulator' // the manipulators service name
                method: 'updateBundleRouting' // the method you want the generator to execute
                filename: 'Resources/config/routing.yml' // the file you want to manipulate
                setters: // specify any setters you want the generate to set
                    format: 'yml' // variable passed to the setter
```

USAGE
-----

Enter the following commands in the console and follow the prompts!

Generate a bundle skeleton with:

``` bash
$ php app/console generate:avro:bundle
```

Generate code for one entity or all mapped entities in the entire application with:

``` bash
$ php app/console generate:avro:build
```

Parameters
----------
Variables available in twig templates
    - entity // entity name 
    - entity_cc // entity name in camel-case
    - entity_us // entity name in underscore
    - fields // array of the entities fields
        - type // field type (string, integter, manyToOne, etc)
        - fieldName // field name
        - targetEntity // field target entity
        - length // field length
        - cascade // array
    - uniqueRelations // unique many to one relations
    - bundle_vendor // bundles vendor name
    - bundle_basename // bundles base name
    - bundle_name // bundles name
    - bundle_corename // bundle core name
    - bundle_path // bundle path
    - bundle_namespace // bundle namespace
    - bundle_alias // bundle alias
    - db_driver // bundle db 
    - style // style

Twig Filters
------------

Several handy twig filters are included
- camelCaseToTitle
- camelCaseToUnderscore
- ucFirst


Installation
------------
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

SOMEDAY FEATURES
----------------
- MongoDB support
- CouchDB support
