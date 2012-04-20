{{ bundleName }}
-----------------

Installation
------------

Add the `{{ bundleVendor }}` namespace to your autoloader:

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    '{{ bundleVendor }}' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

    new {{ bundleNamespace }}\{{ bundleName }}
```

```
[{{ bundleName }}]
    git=git://github.com/{{ bundleVendor }}/{{ bundleBaseName }}.git
    target=bundles/{{ bundleVendor }}/{{ bundleBaseName }}
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

