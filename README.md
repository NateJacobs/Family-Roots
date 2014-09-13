# Family Roots

This WordPress plugin integrates [The Next Generation of Genealogy Sitebuilding](http://www.tngsitebuilding.com) with Wordpress. This plugin simply uses the data stored in the TNG database to display the genealogy data.

### What it does today
The plugin expects WordPress and TNG to be set-up a specific way to ensure a seamless and clean experience for all installations: WordPress and TNG should be installed in the same directory (but different subfolders). Upon activation, the plugin will search for the TNG file path. On the options page you have the option of manually overriding the path or adding one if the plugin can’t find it automatically. The plugin options page is a submenu located under the WordPress Settings menu.

#### Access the TNG Data
There are several classes that allow you to pull out the data you want from the plugin.
* Person Class - provide the person ID or name and a TNG_Person object will be returned.
* Family Class - provide the family ID and a TNG_Family object will be returned.
* Relationship Class - provide two person IDs and the blood relationship will be returned.
* Person Query Class - search the person table using different variables.
* Family Query Class - search the family table using different variables.
* Places Class - provide the place ID or name and a TNG_Places object will be returned.

#### Display the TNG Data
The plugin adds rewrite rules to display the TNG data using custom templates in the theme directory. The following rewrite rules are supported.
* home_url()/genealogy/lastnames/
* home_url()/genealogy/lastname/LASTNAME/(page/PAGE#)
* home_url()/genealogy/person/ID
* home_url()/genealogy/family/ID
* home_url()/genealogy/places
* home_url()/genealogy/place/ID/


##### Templates Supported
The following template names are supported in the theme folder. If the plugin does not find any there, the basic templates will be loaded from the plugin folder. The following templates are supported.
* tng-lastnames-page.php
* tng-lastname-page.php
* tng-person-page.php
* tng-family-page.php
* tng-places-page.php
* tng-place-page.php

### Roadmap
**Version 1.0**

* WordPress Toolbar access to common features
* Widgetized sidebars
* Add plugin hooks and filters to allow for other developers to extend and add-on
* TNG_Notes class
* TNG_Media class
* TNG_Events class
* TNG_Sources class
* TNG_Citations class
