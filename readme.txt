=== Plugin Name ===
Contributors: sveinhansen
Donate link: none
Tags: usermeta
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 0.3

This plugin allows an admin to create simple table reports from the usermeta database table.

== Description ==
Very simple reporting on usermeta data.

Will appear in the Settings menu in the Dashboard. Will list all unique keys in the usermeta
database table and allow you to select what fields to include and the order to include them.

Pressing "Generate report" will produce a simple table with the following rows:

ID		: id of the user (not user configurable)
nice_name	: readable username (not user configurable)
1:n usermeta    . what ever fields you select from the usermeta table
	
No fancy stuff here. If you want more advanced handling of the report, copy and past to Openoffice Calc
or MS Excel or whatever spreadsheet application you normally use.

== Installation ==

1. Unpack the .zip-file in the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Access the application from the 'Setting' menu in the Dashboard.

== Frequently Asked Questions ==

= Will there be a more advanced version of the plugin? =

Maybe, depends on the time I have available. I already have a full-time job.

= What about performance? =

The code is not optimised for performance. To illustrate, when generating the report, the plugin first
selects all the users in in your Wordpress intallation. For each user, it makes a separate query for each
meta-key you have choosen for the report to find the value. This could potentially generate lots of queries
towards the database.

Example: you have 1200 users in your database, and want to produce a report using 7 report fields:

1 query to find all users: 1 query
7 queries for each user: 1200 x 7 = 8400 queries
In total: 8401 queries
	
I have tested it with about 200 users and genereated reports with 10+ fields. It still takes less than
5 seconds on my test equipment. The usermeta queries are all small and hits the default indexes.


== Screenshots ==

1. Specifying the fields to include in the report
2. The produced report

== Changelog ==

= 0.1 =
* First release


= 0.2 =
* Fixed a few minor bugs

= 0.3 =
* Fixed styling
* Fixed some bugs
