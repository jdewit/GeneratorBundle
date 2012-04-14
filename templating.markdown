<div class="page-header">
    <h3>Templating</h3>
</div>
<div>
    <p>
        Check the <a href="https://github.com/jdewit/GeneratorBundle/tree/master/Skeleton">Skeleton</a> directory to see the included templates. 

        Templates are parsed with twig so all the normal twig filters are available to you.

        Since you are basing your templates off of an entity, there 
        are a number of variables available to you in your twig templates:
    </p>
    <table class="table-bordered table-striped table-condensed span11">
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
                <td><a href="#fields-modal" data-toggle="modal">array</a></td>
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
    <br />
    <div id="fields-modal" class="modal hide fade" style="width: 50%; margin-left: -25%;">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Field Variables</h3>
        </div>
        <div class="modal-body">
            <table class="table-condensed table-bordered table-striped" style="width: 100%;">
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
            </table>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Close</a>
        </div>
    </div>
</div>
<div class="page-header">
    <h3>Included Templates</h3>
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

