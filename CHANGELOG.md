= 0.9.6 =

Fixed:

* Accessibility of the to-do list.

= 0.9.5 =

Fixed:

* Post-type filters intruduced in v0.9.4 now also affect the graph results.

= 0.9.4 =

Enhancements:

* Added a setting to include post types, we default to `post` and `page` and you can add others as you wish.

Fixed:

* Completing the last badge wouldn't ever work, fixed.
* Fixed some bugs around detecting badges being "had".
* Replaced links to the site with shortlinks, so we can change them as needed without doing a release.

= 0.9.3 =

Security:

* Stricter sanitization & escaping of data in to-do items. Props to [justakazh](https://github.com/justakazh) for reporting through our [PatchStack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/progress-planner).
* Restrict access to the plugin's dashboard widgets to users with the `publish_posts` capability.

= 0.9.2 =

Security:

* Fixes a vulnerability in our REST API endpoint access validation to retrieve stats. Props to [Djennez](https://github.com/Djennez) for reporting through our [PatchStack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/progress-planner).

= 0.9.1 =

Enhancements:

* Added an action link to the Dashboard to the plugin's action links on the plugins page.
* No longer show Elementor templates as a post type in the plugin's reports.
* Improved translatability (is that a word?) of some of our strings with singulars and plurals.

Bugfixes:

* Fixed the responsive styles of the dashboard widget. Thanks to [Aaron Jorbin](https://aaron.jorb.in/) for reporting.
* Fix the accessibility of the to-do list. Thanks to Steve Jones of [Accessibility checker](https://equalizedigital.com/accessibility-checker/) for the report and fix.
* The plugin would throw a fatal error on uninstall. Thanks to [Jose Varghese](https://github.com/josevarghese) for reporting.
* Deleting the last to do item on the to do list would not work. Thanks to [Jose Varghese](https://github.com/josevarghese) for reporting.
* Don't show the password reset link during onboarding of users as it leads to confusion. Thanks to [Jose Varghese](https://github.com/josevarghese) for reporting.

= 0.9 =

Initial release on GitHub and WordPress.org.
