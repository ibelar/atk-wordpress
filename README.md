# Read Me

## What is it?

Atk-wordpress help build plugins for Wordpress. With this package, plugins UI elements are created using the [Agile Toolkit Framework ](http://www.agiletoolkit.org) for php.

This package make it easy to implement each of Wordpress components like: 

 - Admin panels and sub-panels;
 - Widgets;
 - Dashboard widgets;
 - Meta boxes;
 - Shortcode;

Then each component UI element, like **Form, Lister, CRUD, Icon, Button, Table and more** are easily add to the component, thanks to the Agile Toolkit framework for php.

Creating a Wordpress component is as simple as:

 - define it's parameter as required by Wordpress;
 - define the proper View class to use for the component,  
 - define the component view by adding UI elements using Agile Toolkit framework;

This package will take care of properly wiring the necessary Wordpress hook and action for the component to run.

Furthermore, components define will work under ajax, whether in admin or front section of Wordpress, right out-of-the-box.

## Require

This package require [composer](https://getcomposer.org/) in order to install all it's dependencies.

## Getting started

### Using the starter project

Although you can start from scratch, the easiest way would be to start using the [atk-wordpress-starter](http://github.com/ibelar/atk-wordpress-starter).
 
 - download or clone the starter project inside you Wordpress installation plugins folder, 
 - rename it to match your plugin name and namespace by replacing text mark as TODO,
 - update the composer.json file and run composer.
  

The starter project comes with empty configuration files for each components, except one, an admin panel that display a simple message.

_Note: if the starter project is use as is, it will create a new plugin name atk-wordpress-starter. Once activated, a new admin panel will be accessible via "Hello Atkwp" menu item in Wordpress admin section._ 

### From scratch

Minimal files and folders structure are required for creating a pluging using this package. These files and folders should be located directly under the plugins folder being built.

 - composer.json;
 - plugin.php file;
 - src folders with a Plugin.php class;
    - Plugin.php must extends AtkWp class and implement the atkwp\interfaces\PluginInterface.
 - configurations folder;
    - where default and components configurations are located.
 - assets folder;
   - where custom css, js or images files are located.    

#### Composer.json

The composer configuration file must require this package and autoload should match the plugin namespace.

      "require": {
        "ibelar/atk-wordpress": "^1.0"
      },
      "autoload": {
          "psr-4": {
            "atkstarter\\" : "src/"
          }
      }

### plugin.php file

This is the plugin entry point in Wordpress as any other Wordpress plugin is required to have. It should contains your plugin name and description.
The plugin.php file is also responsible of creating and instantiating the Plugin.php class. It will also call the Plugin::booth() method.
The booth() method will load all components define in configuration file and properly hook up their Wordpress actions.

This is how it will normally look:

    <?php
    /*
    Plugin Name: My Plugin name
    Description: My plugin description.
    Version: 1.0
    */
    
    namespace my_plugin_namespace;
    
    use atkwp\controllers\ComponentController;
    use atkwp\helpers\Pathfinder;
    
    require 'vendor/autoload.php';
    
    if (array_search(ABSPATH . 'wp-admin/includes/plugin.php', get_included_files()) === false) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $atk_plugin_name = "myPluginName";
    $atk_plugin = __NAMESPACE__."\\Plugin";
    
    $$atk_plugin_name = new  $atk_plugin($atk_plugin_name, new Pathfinder(plugin_dir_path(__FILE__)), new ComponentController());
    
    if (!is_null( $$atk_plugin_name)) {
        $$atk_plugin_name->boot(__FILE__);
    }

### Plugin.php class

The main plugin file. The Plugin class must extends the AtkWp class and implement the PluginInterface.

Minimally, it should look like this:

    <?php
    /**
     * The Plugin implementation.
     */
    
    namespace my_plugin_namespace;
    
    use atkwp\interfaces\PluginInterface;
    use atkwp\AtkWp;
    
    class Plugin extends AtkWp implements PluginInterface
    {
        public function init()
        {
            // Uncommented this for database connectivity.
            //$this->setDbConnection();
        }
    
        public function activatePlugin()
        {
            // TODO: Implement activatePlugin() method.
        }
    
        public function deactivatePlugin()
        {
            // TODO: Implement deactivatePlugin() method.
        }
    }
    
### configuratons folder

This is the place where configuration files should be located. There is one configuration file for each possible component in Wordpress and one for default configuration value a plugin might needed.

Generally speaking, a component configuration required the uses of a php class implementing the component detail. The component class must extends the proper component's type class, for example a panel 
component class would extends the PanelComponent type class. Each component's type class, except for WidgetComponent, is an Agile Toolkit View, that will automatically output itself when need by Wordpress. 
Therefore, you can use a component class as you would use a regular Agile Toolkit View class. (atk4\ui)

 - [config-default.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-default.php) 
   - The default configuration a plugin might need. Developper can define their own configuration value and use them troughout their plugin.
 - [config-panel.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-panel.php)
   - The configuration need to create admin section panel in Wordpress. You will define as many panels or sub-panels needed for your plugin.
 - [config-metabox.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-metabox.php)
   - The configuration need to create meta box section in a Wordpress post. 
 - [config-widget.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-widget.php)
   - The configuration need to create widget in Wordpress.
 - [config-dashboard.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-dashboard.php)
   - The configuration need to create dashboard widget in Wordpress.
 - [config-shortcode.php](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-shortcode.php)
   - The configuration need to create shortcode in Wordpress.
 - [config-enqueue](https://github.com/ibelar/atk-wordpress-starter/blob/dev-master/configurations/config-enqueue.php)
   - The configuration need to load custom js or css file within Wordpress.  

_Note on WidgetComponent: The WidgetComponent type class is the only one not deriving from a regular atk4\ui\View class. The reason is that a Widget class in Wordpress must extends \Wp_Widget.
However, when running \Wp_Widget::widget() or \Wp_Widget::form() method, an atk4\ui/View object is passed as an argument in order to be able to add atk UI element to the widget._

### assets folder

The assest's a plugin need. It usually contains:

 - a js folder;
 - a css folder;
 - an images folder;

## Sample plugin

Beside the [plugin starter project](http://github.com/ibelar/atk-wordress-starter), there is a more complete plugin sample ([atk-wordpress-sample](http://github.com/ibelar/atk-wordress-sample)) available that use most of Wordpress components.

# License

Copyright (c) 2017 Alain Belair. MIT Licensed,

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

# Agile Toolkit

To know more about the [Agile Toolkit Framework ](http://www.agiletoolkit.org)
