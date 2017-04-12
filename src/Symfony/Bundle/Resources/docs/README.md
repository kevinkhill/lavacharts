Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require khill/lavacharts
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Khill\Lavacharts\Symfony\Bundle\LavachartsBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Import the Service
-------------------------

Lastly, import the service by adding the `services.yml` to the imports
in the `app/config/config.yml` file of your project:

```php
imports:
    // ...

    - { resource: "@LavachartsBundle/Resources/config/services.yml" }

```

Step 4: Pull from the Container
-------------------------

Now you can use Lavacharts from within your controller:

```php
    $lava = $this->get('lavacharts');

    $datatable = $lava->DataTable();

    // ...
```
