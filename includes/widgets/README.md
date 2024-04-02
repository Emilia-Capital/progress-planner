# Widgets

This directory contains the widgets that are used in the plugin's admin page.

Individual widgets should extend the `\ProgressPlanner\Widgets\Widget` class, and implement a `render` method that outputs the widget's content.
Widgets should also have an `$id` defined, which will be used to generate the CSS class for the widget.
