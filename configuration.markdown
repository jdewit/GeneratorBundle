---
layout: default
title: Avro Generator Bundle - Configuration
---
<div class="page-header">
    <h3>Configuration</h3>
</div>
<div>
    <p>Add your templates to the generator by creating a new node under the files node.</p>

    <pre class="prettyprint lang-yaml">
        avro_generator:
            extend: 'avro' # extend on several built in templates or roll your own
            overwrite: false # overwrite current code if true, write to Temp folder if false
            add_fields: true # allows you to add fields to your entity
            files:
                list_view: 
                    filename: 'Resources/views/{{ entity }}/list.html.twig' # the target location for the generated file relative to the bundle path
                    template: 'AvroGeneratorBundle:Skeleton/Resources/views/Avro/list.html.twig' # the path to the template file, 
                        # use shortcut notation to have it relative to a bundle (ex. AvroDemoBundle:Skeleton/myfile.html.twig) 
                        # otherwise, it can be relative to your system path "/"
                        # or your applications 'app' folder
                    tags: ['view', 'crud'] # tags allow you to specify which files you want to generate
    </pre>

    <p>Checkout the <a href="https://github.com/jdewit/GeneratorBundle/blob/master/Resources/config/avro.yml">avro.yml</a> config file 
    for a good example on how you can customize which files you want to generate.</p> 

    <p>Generate standalone files that are not based off an entity</p>

    <pre class="prettyprint lang-yaml">
        avro_generator:
            #...
            standalone_files:
                README: 
                    filename: 'README' 
                    template: 'Resources/README' 
                    tags: ['readme'] 
    </pre>

    <p>Create a bundle skeleton.</p>

    <pre class="prettyprint lang-yaml">
        avro_generator:
            #...
            bundle_folders:
                controller:
                    path: 'Controller' #relative to bundle root path

            bundle_files:
                bundle:
                    filename: '{{ bundleName }}.php'
                    template: 'AvroGeneratorBundle:Skeleton/Bundle.php'
    </pre>
    <p>Notice that the parameters are available in the filename as well.</p> 

    <p>If you have the extend option set to a built-in template, it will generate your files along with the others. If you have it set to false, it will
    only generate the files you specify in your configuration.</p>

    <p>The generator also allows you to pass your own parameters to the template and even call services to manipulate code.</p>

    <p>Take the following configuration for generating a controller. The controllers actions are added to the parameters node
    which are now available in the template. The manipulator node is also set to manipulate the bundles routing
    file so that the new controller is added.</p>

    <pre class="prettyprint lang-yaml">
        avro_generator:
            #...
            files:
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
    </pre>
</div>
<br />
<a class="btn pull-left" href="installation.html">&larr; Installation</a>
<a class="btn pull-right" href="templating.html">Templating &rarr;</a>
