---
layout: default
title: Avro Generator Bundle
---

<div class="page-header">
    <h3>Installation</h3>
</div>
<div>
    <p>
        The bundle is listed on packagist under 'avro/generator-bundle'. Just add it to your composer.json file.
    </p>

    <pre class="prettyprint lang-json">
        "require": {
            "avro/generator-bundle": "dev-master"
        }
    </pre>

    <p>Enable the bundle in the kernel:</p>

    <pre class="prettyprint lang-php">
        // app/AppKernel.php

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            ...
            $bundles[] = new Avro\GeneratorBundle\AvroGeneratorBundle();
        }
    </pre>
    
    <p>Now, update composer to download the bundle:</p>

    <pre class="prettyprint lang-bsh">
        $ php composer.phar update
    </pre>
</div>

