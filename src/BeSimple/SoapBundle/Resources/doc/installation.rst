Installation with Composer
==========================

Add `natlibfi/besimple-soap <https://packagist.org/packages/natlibfi/besimple-soap>`_ (with vendors) in your composer.json:

.. code-block:: json

    {
        "require": {
            "natlibfi/besimple-soap":   "^3.0.0"
        }
    }

Update vendors:

.. code-block:: bash

    $ php composer.phar self-update
    $ php composer.phar update

Enable the BeSimpleSoapBundle your application Kernel class:

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new BeSimple\SoapBundle\BeSimpleSoapBundle(),
            // ...
        );
    }
