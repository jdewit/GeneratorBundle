AvroGeneratorBundle
-------------------
Generate Symfony2 related code from the command line!
With this bundle you can generate or update 
all classes related to an entity with just a few commands!
You can even build onto an existing entity.

This bundle allows you to easily create your own templates, place and render them 
in the path of your choice, and generate code from a mapped
entity. 

Status
------
The bundle is a work in progress but is working...most of the time :) 
Currently it only provides support for Doctrine ORM. 

The code still needs to get cleaned up a fair bit but the code that 
it does generate is pretty solid. 

Any help would be much appreciated!


Configuration
-------------
``` yml
avro_generator:
    style: 'none' # build onto several built in styles or roll your own
    overwrite: false # overwrite current code if true, write to Temp folder if false
```

You can override or build onto the default styles by means of yml configuration. Checkout 
the <a href="https://github.com/jdewit/GeneratorBundle/blob/master/Resources/config/avro.yml">avro.yml</a> config file 
for a good example on how you specify your templates and even call services. 

You can tell the generator to run your own templates by specifying them in your config_dev.yml like so:

``` yml
parameters:
    avro_generator.my.files:
        list_view: 
            filename: 'Resources/views/{{ entity }}/list.html.twig' # the target location for the generated file relative to the bundle path
            template: 'AvroGeneratorBundle:Skeleton/Resources/views/Avro/list.html.twig' # the path to the template file, 
                # use shortcut notation to have it relative to a bundle (ex. AvroDemoBundle:Skeleton/myfile.html.twig) 
                # otherwise, it can be relative to your system path "/"
                # or your applications 'app' folder
            tags: ['view', 'crud'] # tags allow you to specify which files you want to generate
```

You can also generate standalone files that are not based off an entity

``` yml
parameters:
    avro_generator.my.standalone_files:
        README: 
            filename: 'README' 
            template: 'Resources/README' 
            tags: ['readme'] 
```

The generator also allows you to create a bundle skeleton.
Specify bundle folders and files in the same way.

``` yml
parameters:
    avro_generator.my.bundle_folders:
        controller:
            path: 'Controller'

    avro_generator.my.bundle_files:
        bundle:
            filename: '{{ bundle_name }}.php'
            template: 'AvroGeneratorBundle:Skeleton/Bundle.php'
```

If you have the style option set to a built-in style, it will generate your files along with the others. If you have it set to 'none', it will
only generate the files you specify in your configuration.

The generator also allows you to pass your own parameters to the template and even call services to manipulate code.

Take the following configuration for generating a controller. The controllers actions are added to the parameters node
which are now available in the template. The manipulator node is also set to manipulate the bundles routing
file so that the new controller is added.

``` yml
parameters:
    avro_generator.my.files:
        controller: 
            filename: 'Controller/{{ entity }}Controller.php'
            template: 'AvroGeneratorBundle:Skeleton/Controller/Avro/Controller.php'
            parameters: 
                actions: ['list', 'new', 'edit', 'delete', 'restore', 'import']
            tags: ['controller', 'crud']
            manipulator: 
                service: 'avro_generator.routing.manipulator' # the manipulators service name
                method: 'updateBundleRouting' # the method you want the generator to execute
                filename: 'Resources/config/routing.yml' # the file you want to manipulate
                setters: # specify any setters you want the generate to set
                    format: 'yml' # variable passed to the setter
```

USAGE
-----

One command does it all!

``` bash
$ php app/console avro:generate
```

The generator will prompt you to specify an entity you wish to 
generate code from. Specify an entity using shortcut notation.
(Ex. AvroDemoBundle:Post)

Omit the entity, and you can generate code for all of the entities
mapped in a bundle!
(ex. AvroDemoBundle)

If the bundle does not exist, it will prompt you whether or not you would like to
create it.

The generator also prompts you for a tag. This allows 
you to only generate files you have marked with a specific 
tag in your configuration. Just press "enter" if you 
want to generate all of the files in your config.

Templates
----------

Check the <a href="https://github.com/jdewit/GeneratorBundle/tree/master/Skeleton">Skeleton</a> directory to see the included templates. 

Templates are parsed with twig so all the normal twig filters are available to you.

Since you are basing your templates off of an entity, there 
are a number of variables available to you in your twig templates:

- {{ entity }} // The entity name 
- {{ entity_cc }} // The entity name in camel-case format
- {{ entity_us }} // The entity name in underscore format
- {{ fields }} // array of the entities fields
 - {{ field.type }} // field type (string, integter, manyToOne, etc)
 - {{ field.fieldName }} // field name
 - {{ field.fieldTitle }} // field name in title format
 - {{ field.targetEntity }} // field target entity
 - {{ field.length }} // field length
 - {{ field.cascade }} // array
 - {{ field.targetVendor }} *
 - {{ field.targetBundle }} *
 - {{ field.targetBundleAlias }} *
 - {{ targetEntityName }} *
- {{ uniqueRelations }} // array of unique manyToOne relations
- {{ bundle_vendor }} // bundles vendor name (ex. Avro)
- {{ bundle_basename }} // bundles base name (ex. GeneratorBundle)
- {{ bundle_name }} // bundles name (ex. AvroGeneratorBundle)
- {{ bundle_corename }} // bundle core name (ex. Generator)
- {{ bundle_path }} // bundle path
- {{ bundle_namespace }} // bundle namespace 
- {{ bundle_alias }} // bundle alias (ex. Avro_generator)
- {{ db_driver }} // bundle db 
- {{ style }} // style specified in your config

(* only if field is of manyToOne type)

Built-in Styles
---------------

The 'Avro' style is a work in progress. It provides basic crud
functionality using some similar techniques as the FOSUserBundle.

It is designed to be used along with: 

- <a href="http://twitter.github.com/bootstrap">Twitter Bootstrap</a>
- <a href="http://github.com/jdewit/AvroQueueBundle">AvroQueueBundle</a>
- <a href="http://github.com/jdewit/AvroCsvBundle">AvroCsvBundle</a>

Sharing is caring. 
Submit some of your own templates!

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
- TESTS!
- travis 
- xml 
- MongoDB support
- CouchDB support
