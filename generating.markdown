---
layout: default
title: Avro Generator Bundle - Generating
---

<div class="page-header">
    <h3>Generating</h3>
</div>
<div>
    <p>One command does it all!</p>

    <pre class="prettyprint lang-bsh">
        $ php app/console avro:generate
    </pre>
    <h3>Prompt 1: <small>Provide the bundle/entity name</small></h3>
    <p>Enter an entity you wish to generate code from. Use shortcut notation (Ex. AvroDemoBundle:Post).</p>
    <p>You can omit the entity to generate code for all of the entities mapped in the bundle! (ex. AvroDemoBundle)</p>
    <p>If the bundle does not exist, it will prompt you whether or not you would like to create it.</p>
    <br />
    <h3>Prompt 2: <small>Provide a tag</small></h3>
    <p>Enter a tag. This allows you to only generate files you have marked with a specific tag in your configuration.</p>
    <p>Press "enter" to bypass the tag filter and generate all of the files in your config.</p>
</div>

