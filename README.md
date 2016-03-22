# TODO #
-attach a filter to all cron jobs so i can see when they run
  - find all hooks that we want to monitor
  - add an action to run before the hook (1) to tell cronalytics it has started
  - add an action after the hook (9999999) to tell cronalytics it finished
  - TODO: find some way to record a result for each cron. 
- crate a management page that 
  - lists all crons
  - has a way to manage (add/ link to view) triggers
- add composer into lib dir (commit vendors dir)


# Cronalytics Wordpress Plugin #




# WordPress Plugin Boilerplate #

Use this as a template for creating your own custom WordPress plugins.

### How do I get set up? ###

* [Download](https://bitbucket.org/4mationtechnologies/wordpress-plugin-boilerplate/downloads) a copy of this repository.
* Extract the zip to your project's `wp-content/plugins` directory.
* Rename the folder to your plugin's slug, e.g. `your-plugin`.

Perform the following **case-sensitive** string replacements across all files in the plugin.

* Plugin_Boilerplate -> Your_Plugin
* Plugin Boilerplate -> Your Plugin
* plugin-boilerplate -> your-plugin
* plugin_boilerplate -> your_plugin

Additionally

* Rename the root directory file `plugin-boilerplate.php` to your plugin's slug, e.g. `your-plugin.php`.
* Delete or edit the `inc/classes/class-plugin-boilerplate-example.php` file as you see fit.

### Contribution guidelines ###

* Clone this repository locally.
* Create a branch for your changes.
* Make the desired changes.
* Push your branch back up to this repository .
* Open a pull request into master and add Chris or Kieran as a reviewer.

### Who do I talk to? ###

* Chris and/or Kieran.