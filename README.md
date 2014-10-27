# Family Roots
Family Roots is a WordPress plugin designed to seamlessly integrate WordPress and [The Next Generation of Genealogy Sitebuilding](http://www.tngsitebuilding.com). Family Roots uses native WordPress database functions to connect to and pull data from the TNG database. Family Roots offers simple and clean default templates or create your own to display your genealogy research. The plugin is a fast and safe way to show off your family tree. 

### Features
* Use any WordPress theme.
* Search people from your TNG database using the native WordPress search.
* Create templates to display your TNG data how you want.

#### Access the TNG Data
There are several classes that allow you to pull out the data you want from TNG.
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
* /family-roots/tng-lastnames-page.php
* /family-roots/tng-lastname-page.php
* /family-roots/tng-person-page.php
* /family-roots/tng-family-page.php
* /family-roots/tng-places-page.php
* /family-roots/tng-place-page.php

### Requirements
* PHP 5.4 or greater
* WordPress 4.0 or greater
* TNG on same server as WordPress
* Only one tree in TNG
* No branches in TNG

### Roadmap
* TNG_Notes class
* TNG_Media class
* TNG_Events class
* TNG_Sources class
* TNG_Citations class
* TNG_Cemetery class