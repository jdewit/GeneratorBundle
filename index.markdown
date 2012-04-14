---
layout: default
title: Avro Generator Bundle
---

<div class="page-header">
    <h3>AvroGeneratorBundle</h3>
</div>
<div>
    <p>
        Generate Symfony2 related code from the command line!
        With this bundle you can generate or update 
        all classes related to an entity with just a few commands!
        You can even build onto an existing entity.

        This bundle allows you to easily create your own templates, place and render them 
        in the path of your choice, and generate code from a mapped
        entity. 
    </p>
</div>
<div class="page-header">
    <h3>Status</h3>
</div>
<div>
    <p>
        The bundle is a work in progress but is working...most of the time :) 
        Currently it only provides support for Doctrine ORM. 

        The code still needs to get cleaned up a fair bit but the code that 
        it does generate is pretty solid. 

        Any help would be much appreciated!
    </p>
</div>
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
<div class="page-header">
    <h3>USAGE</h3>
</div>
<div>
    <p>
        One command does it all!
    </p>

    <pre class="prettyprint lang-bsh">
        $ php app/console avro:generate
    </pre>
    <p>
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
    </p>
</div>
<div class="page-header">
    <h3>Template Variables</h3>
</div>
<div>
    <p>
        Check the <a href="https://github.com/jdewit/GeneratorBundle/tree/master/Skeleton">Skeleton</a> directory to see the included templates. 

        Templates are parsed with twig so all the normal twig filters are available to you.

        Since you are basing your templates off of an entity, there 
        are a number of variables available to you in your twig templates:
    </p>
    <table class="table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>entity</td>
                <td>string</td>
                <td>The entity name</td>
            </tr>
            <tr>
                <td>entityCC</td>
                <td>string</td>
                <td>The entity name in camel-case format</td>
            </tr>
            <tr>
                <td>entityUS</td>
                <td>string</td>
                <td>The entity name in underscore format</td>
            </tr>
            <tr>
                <td>entityTitle</td>
                <td>string</td>
                <td>The entity name in title format</td>
            </tr>
            <tr>
                <td>entityTitleLC</td>
                <td>string</td>
                <td>The entity name in lowercase title format</td>
            </tr>
            <tr>
                <td>fields</td>
                <td><a data-original-title="Field Variables" href="#" rel="popover" data-content="
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>fieldName</td>
                                <td>string</td>
                                <td>The fields name.</td>
                            </tr>
                            <tr>
                                <td>Field type</td>
                                <td>string</td>
                                <td>The field type.</td>
                            </tr>
                            <tr>
                                <td>columnName</td>
                                <td>string</td>
                                <td>The field column name</td>
                            </tr>
                            <tr>
                                <td>fieldTitle</td>
                                <td>string</td>
                                <td>The field name in title format</td>
                            </tr>
                            <tr>
                                <td>length</td>
                                <td>integer</td>
                                <td>The field length</td>
                            </tr>
                            <tr>
                                <td>unique</td>
                                <td>boolean</td>
                                <td>Is field unique</td>
                            </tr>
                            <tr>
                                <td>nullable</td>
                                <td>boolean</td>
                                <td>Is field nullable</td>
                            </tr>
                            <tr>
                                <td>precision</td>
                                <td>integer</td>
                                <td>The field precision (if type is number)</td>
                            </tr>
                            <tr>
                                <td>scale</td>
                                <td>integer</td>
                                <td>Field scale (if type is number)</td>
                            </tr>
                            <tr>
                                <td>targetVendor</td>
                                <td>string</td>
                                <td>The vendor name of the target entity. (If type is manyToOne)</td>
                            </tr>
                            <tr>
                                <td>targetBundle</td>
                                <td>string</td>
                                <td>The bundle name of the target entity. (If type is manyToOne)</td>
                            </tr>
                            <tr>
                                <td>targetBundleAlias</td>
                                <td>string</td>
                                <td>The bundle alias of the target entity. (If type is manyToOne)</td>
                            </tr>
                            <tr>
                                <td>targetEntityName</td>
                                <td>string</td>
                                <td>The target entity name (If type is manyToOne)</td>
                            </tr>
                            <tr>
                                <td>orphanRemoval</td>
                                <td>boolean</td>
                                <td>Is orphan removal set? (If field is assocation)</td>
                            </tr>
                            <tr>
                                <td>joinColumns</td>
                                <td>array</td>
                                <td>The join columns. [name, referencedColumnName] (If field is association)</td>
                            </tr>
                            <tr>
                                <td>cascade</td>
                                <td>array</td>
                                <td>The fields cascade properties</td>
                            </tr>
                            <tr>
                                <td>inversedBy</td>
                                <td>string</td>
                                <td>The inverse field. (If field is association)</td>
                            </tr>
                            <tr>
                                <td>targetEntity</td>
                                <td>string</td>
                                <td>The target entity class name. (If field is association)</td> 
                            </tr>
                            <tr>
                                <td>fetch</td>
                                <td>string</td>
                                <td>The mappedBy class name. (If field is assocation)</td>
                            </tr>
                            <tr>
                                <td>isOwningSide</td>
                                <td>boolean</td>
                                <td>Is this field the owning side of the relation. (If field is association)</td>
                            </tr>
                            <tr>
                                <td>isCascadeRemove</td>
                                <td>boolean</td>
                                <td>Is cascade remove property set? (If field is association)</td>
                            </tr>
                            <tr>
                                <td>isCascadeRefresh</td>
                                <td>boolean</td>
                                <td>Is cascade refresh property set? (If field is association)</td>
                            </tr>
                            <tr>
                                <td>isCascadePersist</td>
                                <td>boolean</td>
                                <td>Is cascade persist property set? (If field is association)</td>
                            </tr>
                            <tr>
                                <td>isCascadeMerge</td>
                                <td>boolean</td>
                                <td>Is cascade merge property set? (If field is association)</td>
                            </tr>
                            <tr>
                                <td>isCascadeDetach</td>
                                <td>boolean</td>
                                <td>Is cascade detach property set? (If field is association)</td>
                            </tr>
                            <tr>
                                <td>joinColumnFieldNames</td>
                                <td>array</td>
                                <td>Join column field names. (If field is association)</td>
                            </tr>
                        </tbody>
                    </table>">array</a></td>
                    <td>The entities fields</td>
                </tr>
                <tr>
                    <td>uniqueManyToOneRelations</td>
                    <td>array</td>
                    <td>An array of unique manyToOne relations.</td>
                </tr>
                <tr>
                    <td>bundleVendor</td>
                    <td>string</td>
                    <td>The bundles vendor name (ex. Avro).</td>
                </tr>
                <tr>
                    <td>bundleBaseName</td>
                    <td>string</td>
                    <td>The bundles base name (ex. GeneratorBundle).</td>
                </tr>
                <tr>
                    <td>bundleName</td>
                    <td>string</td>
                    <td>The bundles name (ex. AvroGeneratorBundle).</td>
                </tr>
                <tr>
                    <td>bundleCorename</td>
                    <td>string</td>
                    <td>The bundles core name (ex. generator).</td>
                </tr>
                <tr>
                    <td>bundlePath</td>
                    <td>string</td>
                    <td>The bundle path relative to system path.</td>
                </tr>
                <tr>
                    <td>bundleNamespace</td>
                    <td>string</td>
                    <td>The bundles namespace (ex. Avro\GeneratorBundle).</td>
                </tr>
                <tr>
                    <td>bundleAlias</td>
                    <td>string</td>
                    <td>The bundles alias (ex. avro_generator)</td>
                </tr>
                <tr>
                    <td>dbDriver</td>
                    <td>string</td>
                    <td>The bundles db driver</td>
                </tr>
                <tr>
                    <td>style</td>
                    <td>string</td>
                    <td>The style parameter specified in your config</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="page-header">
        <h3>Built-in Styles</h3>
    </div>
    <div>
        <h5>Avro</h5>
        <p>
            A work in progress. It provides basic crud
            functionality using some similar techniques as the FOSUserBundle.

            It is designed to be used along with: 
            <ul>
                <li><a href="http://twitter.github.com/bootstrap">Twitter Bootstrap</a></li>
                <li><a href="http://github.com/jdewit/AvroQueueBundle">AvroQueueBundle</a></li>
                <li><a href="http://github.com/jdewit/AvroCsvBundle">AvroCsvBundle</a></li>
            </ul>
            Sharing is caring. 
            Submit some of your own templates!
        </p>
    </div>
    <div class="page-header">
        <h3>Installation</h3>
    </div>
    <div>
        <p>
            Add the `Avro` namespace to your autoloader:
        </p>

        <pre class="prettyprint lang-php">
            <?php
            // app/autoload.php

            $loader->registerNamespaces(array(
                // ...
                'Avro' => __DIR__.'/../vendor/bundles',
            ));
        </pre>
        <p>Enable the bundle in the kernel:</p>

        <pre class="prettyprint lang-php">
            <?php
            // app/AppKernel.php

                if (in_array($this->getEnvironment(), array('dev', 'test'))) {
                    ...
                    $bundles[] = new Avro\GeneratorBundle\AvroGeneratorBundle();
                }
        </pre>
        
        <p>Add to deps file</p>

        <pre class="prettyprint lang-php">
            [AvroGeneratorBundle]
                git=git://github.com/jdewit/AvroGeneratorBundle.git
                target=bundles/Avro/GeneratorBundle
        </pre>

        <p>Now, run the vendors script to download the bundle:</p>

        <pre class="prettyprint lang-bsh">
            $ bin/vendors update
        </pre>
    </div>
    <div class="page-header">
        <h3>TODO</h3>
    </div>
    <div>
        <ul>
            <li>TESTS!</li>
            <li>travis</li> 
            <li>xml support</li>
            <li>MongoDB support</li>
            <li>CouchDB support</li>
        </ul>
    </div>
</div>
<hr>

<footer>
<p>© Company 2012</p>
</footer>

