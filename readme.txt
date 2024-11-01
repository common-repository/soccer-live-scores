=== Soccer Live Scores ===
Contributors: DAEXT
Tags: soccer, live score, soccer live score, live football scores, soccer match, football match, football, results, soccer plugin, real time results, sport live score, sport
Donate link: https://daext.com
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 5.3
Stable tag: 1.05
License: GPLv3

This plugin allows you to displays the live scores of a soccer match in your WordPress posts, pages, or custom post types.

== Description ==
This plugin allows you to displays the live scores of a soccer match in your WordPress posts, pages, or custom post types.

It can be used, for example, in sport news websites to cover the results of a competition, in the match reviews of a soccer-related blog, in the official website of soccer teams, and more.

Please note that the live scores data should be added from the plugin back-end menus by the website administrator. This plugin doesn't support the use of soccer data from external APIs.

## Upgrade to Soccer Engine

Create soccer competitions, generate standings tables, display soccer statistics, show the profiles of the players, and more with [Soccer Engine](https://daext.com/product/soccer-engine/), the ultimate soccer plugin.

## Matches

Create soccer matches from the **Matches** administrative menu simply by adding the names and optionally the logos of the teams.

This menu also includes the following configuration options:

* The **Live** option to enable real-time updates.
* The **Additional Score Mode** option to include the aggregate score in the front-end layout.

## Events

In the **Events** menu, you can add all the relevant events of a soccer match. Each event comes with the following fields:

* Match
* Team
* Minute
* Description
* Additional information
* Event type

## Event types

The event types determine the icons displayed in the front-end layout and are also used to calculate the result of the match.

* By default, the plugin comes with the following six event types:
* Generic
* Yellow Card
* Red Card
* Double Yellow Card
* Substitution
* Goal

There is also the possibility to create custom events. For example, you can create a "Missed Penalty" event type with a custom icon, create an "Own Goal" event type that substracts a goal for the match result, or add other events based on your specific needs.

## Update the live scores in real-time

The plugin updates the result and the events of the match in real-time with AJAX requests. Consequently, the visitors will be able to see the updated live scores layout without refreshing the page.

From the plugin options, you can also set the time interval between the AJAX requests. Note that a short update time increases the number of HTTP requests sent to the server. Therefore, consider the website traffic and the specs of your server before changing this value.

## Customize the style

Use the options provided by the plugin to customize the colors and typography of all the elements displayed in the live score.

## Support multiple matches

The plugin can handle an unlimited number of soccer matches updated in real-time on the same page.

You can, for instance, display all the matches of a competition round on the same page without issues.

## Responsive

Set a responsive breakpoint to switch the layout of the live score from desktop mode to mobile mode at the correct position.

== Installation ==
= Installation (Single Site) =

With this procedure you will be able to install the Soccer Live Scores plugin on your WordPress website:

1. Visit the **Plugins -> Add New** menu
2. Click on the **Upload Plugin** button and select the zip file you just downloaded
3. Click on **Install Now**
4. Click on **Activate Plugin**

= Installation (Multisite) =

This plugin supports both a **Network Activation** (the plugin will be activated on all the sites of your WordPress Network) and a **Single Site Activation** in a **WordPress Network** environment (your plugin will be activated on a single site of the network).

With this procedure you will be able to perform a **Network Activation**:

1. Visit the **Plugins -> Add New** menu
2. Click on the **Upload Plugin** button and select the zip file you just downloaded
3. Click on **Install Now**
4. Click on **Network Activate**

With this procedure you will be able to perform a **Single Site Activation** in a **WordPress Network** environment:

1. Visit the specific site of the **WordPress Network** where you want to install the plugin
2. Visit the **Plugins** menu
3. Click on the **Activate** button (just below the name of the plugin)

== Changelog ==

= 1.05 =

*February 9, 2024*

* Fixed a bug (started with WordPress version 6.5) that prevented the creation of the plugin database tables and the initialization of the plugin options during the plugin activation.

= 1.04 =

*February 7, 2024*

* Removed call to non-exising method during the plugin deactivation.

= 1.03 =

*November 8, 2023*

* Removed PHP deprecation notices.

= 1.02 =

*November 15, 2022*

* Minor back-end improvements.
* Changelog added.

= 1.01 =

*July 20, 2021*

* Initial release.

== Screenshots ==
1. Matches menu
2. Events menu
3. Event Types menu
4. Options menu