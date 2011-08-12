Symfony2 MidCOM Compatibility Bundle
====================================

This bundle has been created to allow running [MidCOM]() components and applications under Symfony2.

It is still in very early stages, but eventually the aim will be that Symfony2 can be used as a drop-in replacement for MidCOM core.

## Installation

Install this bundle by adding the following to the `deps` file and running `php bin/vendors install`:

    [MidcomCompatBundle]
        git=git://github.com/bergie/MidgardMidcomCompatBundle.git
        target=Midgard/MidcomCompatBundle

Then add the `Midgard` namespace to the `app/autoload.php`:

    'Midgard' => __DIR__.'/../vendor'

And enable this bundle in your Kernel:

    new Midgard\MidcomCompatBundle\MidgardMidcomCompatBundle()

You also need a Midgard2 repository connection, so ensure that you also have the [MidgardConnectionBundle](https://github.com/bergie/MidgardConnectionBundle) installed and configured.

## Configuration

You need to tell the MidcomCompat autoloader where your MidCOM components are installed.

Do this by editing your `config.yml`. If your components are installed in the `midcom` directory under Symfony2 root, then:

    midgard_midcom_compat:
        root: "%kernel.root_dir%/../midcom"

You also need to register all components you want to use as Symfony2 Bundles in your Kernel:

    new Midgard\MidcomCompatBundle\Bundle\ComponentBundle('net.nehmer.static')

## Running components in your Symfony2 application

You can run individual components by adding them to your route configuration. For example:

    _projectsite:
        resource: "net.nehmer.static"
        prefix: /midgard
        type: midcom
