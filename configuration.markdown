<div class="page-header">
    <h3>Configuration</h3>
</div>
<div>
    <pre class="prettyprint lang-yaml">
        avro_generator:
            style: 'avro' # build onto several built in styles or roll your own
            overwrite: false # overwrite current code if true, write to Temp folder if false
    </pre>

    <p>You can override or build onto the default styles by means of yml configuration. Checkout 
    the <a href="https://github.com/jdewit/GeneratorBundle/blob/master/Resources/config/avro.yml">avro.yml</a> config file 
    for a good example on how you specify your templates and even call services. 

    You can tell the generator to run your own templates by specifying them in your config_dev.yml like so:
    </p>
    <pre class="prettyprint lang-yaml">
        avro_generator:
            style: false
            overwrite: true
            files:
                list_view: 
                    filename: 'Resources/views/{{ entity }}/list.html.twig' # the target location for the generated file relative to the bundle path
                    template: 'AvroGeneratorBundle:Skeleton/Resources/views/Avro/list.html.twig' # the path to the template file, 
                        # use shortcut notation to have it relative to a bundle (ex. AvroDemoBundle:Skeleton/myfile.html.twig) 
                        # otherwise, it can be relative to your system path "/"
                        # or your applications 'app' folder
                    tags: ['view', 'crud'] # tags allow you to specify which files you want to generate
    </pre>

    <p>You can also generate standalone files that are not based off an entity</p>

    <pre class="prettyprint lang-yaml">
        avro_generator:
            #...
            standalone_files:
                README: 
                    filename: 'README' 
                    template: 'Resources/README' 
                    tags: ['readme'] 
    </pre>

    <p>The generator also allows you to create a bundle skeleton.
    Specify bundle folders and files in the same way.</p>

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
    <p>
    Notice that the parameters are available in the filename as well. 

    If you have the style option set to a built-in style, it will generate your files along with the others. If you have it set to false, it will
    only generate the files you specify in your configuration.

    The generator also allows you to pass your own parameters to the template and even call services to manipulate code.

    Take the following configuration for generating a controller. The controllers actions are added to the parameters node
    which are now available in the template. The manipulator node is also set to manipulate the bundles routing
    file so that the new controller is added.
    </p>

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

