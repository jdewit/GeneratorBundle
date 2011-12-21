{{ bundle_name }}
====================

Installation
============

Add the `{{ bundle_vendor }}` namespace to your autoloader:

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    '{{ bundle_vendor }}' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

    new {{ bundle_namespace }}\{{ bundle_name }}
```

```
[{{ bundle_name }}]
    git=git://github.com/yourGitHubAccount.git
    target=bundles/{{ bundle_vendor }}/{{ bundle_basename }}
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

