=== Adapter Responsive Video ===
Contributors: ryankienstra
Donate link: https://www.jdrf.org/donate/
Tags: video, embed, responsive, mobile, post, Bootstrap
Requires at least: 3.8
Tested up to: 4.9
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A widget for videos and embeds from [YouTube](https://youtube.com), [SlideShare](https://www.slideshare.net), [Spotify](https://spotify.com), and more. Resizes to its container.

== Description ==

* Requires [Bootstrap](https://getbootstrap.com/) 3.2 or later
* This responsive widget resizes with its container
* With the [Video widget](https://make.wordpress.org/core/2017/05/26/media-widgets-for-images-video-and-audio/) in WordPress 4.8 and later, this widget is less necessary for videos
* Though this widget still works with videos, the [Video widget](https://make.wordpress.org/core/2017/05/26/media-widgets-for-images-video-and-audio/) displays well in many [Bootstrap](https://getbootstrap.com/) themes
* But this widget now supports embeds like [SlideShare](https://www.slideshare.net), [Speaker Deck](https://speakerdeck.com), [Spotify](https://spotify.com), and [SoundCloud](https://soundcloud.com)

[youtube http://www.youtube.com/watch?v=6FfXmebV1sI]

== Installation ==

1. Upload the adapter-responsive-video directory to your wp-content/plugins/ directory.
1. In the "Plugins" menu, find "Adapter Responsive Video," and click "Activate."
1. Add an "Adapter Video" widget by going to the admin menu and clicking "Appearance" > "Widgets."
1. Type the url of the video or embed, like YouTube or Spotify.

== Frequently Asked Questions ==

= What does this require? =

[Bootstrap](https://getbootstrap.com/) 3.2 or later

== Screenshots ==

1. An "Adapter Video" widget is only as wide as the sidebar.
2. The full page.

== Changelog ==

= 1.1 =
- Support non-video embeds in the widget, after refactoring and addding tests. See [#9](https://github.com/kienstra/adapter-responsive-video/pull/9).
- Refactor the main plugin file and add PHPUnit tests. See [#7](https://github.com/kienstra/adapter-responsive-video/pull/7).
- Set up wp-dev-lib with configuration files. See [#3](https://github.com/kienstra/adapter-responsive-video/pull/3).
- Add PHPUnit tests, for 100% coverage of methods and functions. See [#2](https://github.com/kienstra/adapter-post-preview/issues/2).

= 1.0.1 =
- Security improvements, including securing widget output.

= 1.0.0 =
- First version

== Upgrade Notice ==

= 1.0.1 =
Please upgrade for the security improvements, especially the safer widget output.
