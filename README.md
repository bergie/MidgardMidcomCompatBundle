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

In addition, you need to register the `midcom` templating engine, so that templates used by components work. Do this in the `config.yml` by editing the `framework` section:

    framework:
        templating:      { engines: ['twig', 'midcom'] }

You also need to register all components you want to use as Symfony2 Bundles in your Kernel:

    new Midgard\MidcomCompatBundle\Bundle\ComponentBundle('net.nehmer.static')

As most components require the old `$_MIDCOM` superglobal to be available, you also need to enable superglobal compatibility in your `php.ini`:

    midgard.superglobals_compat=On

To enable MidCOM's internal routes, you should also include the following to your routing configuration:

    _midcom:
        resource: "@MidgardMidcomCompatBundle/Resources/config/routing.yml"

## Running components in your Symfony2 application

You can run individual components by adding them to your route configuration. For example:

    _mycontent:
        resource: "net.nehmer.static"
        prefix: /midgard
        type: midcom

## How does it work?

The MidCOM Compatibility Bundle operates as a Symfony2 bundle, which prprovides a set of mappings to make MidCOM APIs call their Symfony2 equivalents.

### Class loading

Loading of MidCOM classes is handled by the MidCOM Compatibility Bundle, which on boot registers a MidCOM-style autoloader. The autoloader loads files from paths matching the class name, in that underscores are converted to path separators. So for example `midcom_services_auth` is expected to be located in `midcom/services/auth.php`.

With most classes, the autoloader expects to find them from the directory given in the `midgard.midcomcompat.root` configuration parameter.

However, most MidCOM core APIs are instead loaded from the `Compat` directory of this bundle. This is controlled by the `compatPrefixes` array of the bundle class.

### Component loading

As most resource loading requires the MidCOM components to be also registered as Symfony2 bundles, this bundle provides the necessary bundle and extension classes for it.

Each component which has resources (templates, configurations, etc.) used in the application should be registered as a new bundle to the Symfony2 Application Kernel.

Normally this is done in the `registerBundles` method of the kernel itself:

    new Midgard\MidcomCompatBundle\Bundle\ComponentBundle('net.nehmer.blog'),

It is also possible to load bundles at runtime using MidCOM's API methods, though then the Symfony2 application kernel will not be aware of them:

    $_MIDCOM->load_library('midcom.helper.datamanager2');
    $_MIDCOM->componentloader->load('net.nemein.wiki');

The ComponentBundle class loads the component's MidCOM interface class, handles possible `_autoload_files` of the component, and runs the `_on_initialize` method of the component.

### Routing

The MidCOM compatibility bundle registers a new Symfony2 Route Loader, which handles routes of type `midcom`.

With these routes, the resource where routes are defined needs to conform to a loaded MidCOM component. For example:

    _blog:
        resource: "net.nehmer.blog"
        prefix: /blog
        type: midcom

MidCOM had its own way of defining routes via the classic `_request_switch` array of the viewer class. In latest versions this has been replaced by defining the routes in a `config/routes.inc` file. MidCOM compatibility bundle supports only the latter, so users should ensure their components have been properly upgraded.

Once you have registed some MidCOM components to your Symfony2 routing configuration, you can check that their routes are properly registered by running `php app/console router:debug`. Typical MidCOM routes should look like the following:

    feed_category_rss2        ANY    /blog/feeds/category/{midcom_arg_0}

When a Symfony2 request is matched to a MidCOM route, some additional request attributes will be set:

* `midcom_route_id`: Original `handler_id` of a MidCOM route, as these are cleaned up before registering to Symfony2
* `midcom_component`: MidCOM component handling the request
* `midcom_controller`: The actual MidCOM handler class assigned for the request
* `midcom_action`: Action name for the request

Of these, the `midcom_component` parameter is used to check that a request is a MidCOM one in event listeners the compatibility bundle registers. This way non-MidCOM requests can be safely ignored.

### Superglobals

Many MidCOM components still depend on the legacy `$_MIDCOM` and `$_MIDGARD` superglobals to be present. Because of this, the compatibility bundle checks for the presence of the `midgard.superglobals_compat` PHP ini setting on start-up.

When loading MidCOM components via the ComponentBundle class, this bundle ensures that both `$_MIDCOM` and `$_MIDGARD` superglobals, and the `$GLOBALS['midcom_config']` global are set with sensible values.

### Request handling

The MidCOM compatibility bundle provides its own ControllerResolver, which is run before the regular Symfony2 controller resolver. It only handles requests which have the `midcom_component` attribute set, and passes others onwards in the Controller Resolver chain.

In the `getController` phase, the Controller Resolver passes the dependency injection container and the current request to the MidCOM application class so that they will be available to MidCOM service mappings.

`getController` also loads component's configurations from the `config/config.inc` file. Then it initializes the component's viewer class and passes the request and configuration to that. The viewer instance is set to the `midcom_viewer_instance` attribute of the request.

The viewer's `handle` method is registered as the action callback.

`getArguments` grabs all request attributes prefixed by `midcom_arg_` and creates an array of them. The request class and this array are then passed to the `handle` method.

#### Viewer class

All MidCOM component viewer classes extend the `midcom_baseclasses_components_request` compatibility class.

This class is responsible for setting up the actual handler class instance defined in a route, and calling the `_can_handle` and `_handler` methods from it.

After the `_can_handle` and `_handler` methods of both the component viewer class and the handler class have been run, the viewer sets the component's request data as a `midcom_request_data` attribute to the request, and also sets the handler class instance to `midcom_controller_instance` attribute.

### MidCOM services

Most of the MidCOM service mapping classes are ContainerAware so that they can map their API calls to matching services accessible through the Symfony2 Dependency Injection container.

Current mappings include:

* `auth` maps all privilege checks to the `isGranted` method of the Symfony2 `security.context`. This way any Symfony2 authorization provider can be used
* `auth` maps user access to the Token API of the Symfony2 `security.context`
* `session` maps all API calls to Symfony2 sessioning service. If no session is running, one will be automatically started
