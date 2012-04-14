---
layout: default
title: Avro Generator Bundle
---

<div class="page-header">
    <h3>Generating</h3>
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

