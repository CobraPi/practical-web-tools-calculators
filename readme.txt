=== Practical Web Tools — Free Calculator Widgets ===
Contributors: practicalwebtools
Tags: calculator, widgets, mortgage calculator, embed, tools
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed free calculator widgets — mortgage, loan, BMI, and more — with one shortcode, or embed any Practical Web Tools calculator as an auto-sizing iframe.

== Description ==

Practical Web Tools publishes free online calculators. This plugin makes them trivial to embed in any WordPress post or page with the `[pwt_calculator]` shortcode.

Two embedding modes:

**1. Bundled widget scripts** — nine self-contained calculators that ship with the plugin and run entirely in your visitor's browser. The JavaScript is loaded from your own site (no CDN, no external calls), so these widgets work even if practicalwebtools.com is unreachable:

* Mortgage Calculator (`mortgage`)
* Loan Calculator (`loan`)
* Compound Interest Calculator (`compound`)
* BMI Calculator (`bmi`)
* Calorie Calculator (`calorie`)
* Pregnancy Due Date Calculator (`duedate`)
* Percentage Calculator (`percentage`)
* Countdown Timer (`countdown`)
* Word Counter (`wordcount`)

**2. Any-calculator iframe** — every calculator on practicalwebtools.com (including ones not in the list above) can be embedded by path. The iframe loads from practicalwebtools.com and is automatically resized to its content by a small bundled script, so there is no inner scrollbar and no wasted space.

The plugin is free, there is no API key, no account, and nothing to configure. The widget scripts do not upload anything: all math happens client-side in the browser. For honest disclosure: the iframe mode loads calculator pages from practicalwebtools.com, and every widget shows a small "Powered by" attribution link to the tool it embeds.

**Shortcode examples**

`[pwt_calculator widget="mortgage"]`

`[pwt_calculator widget="bmi" width="480"]`

`[pwt_calculator tool="/finance/mortgage-calculator"]`

`[pwt_calculator tool="/other/time/countdown-timer" theme="dark" width="640"]`

Attributes:

* `widget` — one of the nine widget ids listed above: `mortgage`, `loan`, `compound`, `bmi`, `calorie`, `duedate`, `percentage`, `countdown`, `wordcount`.
* `tool` — any calculator path from practicalwebtools.com, for example `/finance/mortgage-calculator` or `/text/word-counter`.
* `width` — maximum width in pixels (default `880`).
* `theme` — `light` (default) or `dark`. Applies to iframe embeds; bundled widget scripts always use their own clean styling.

If both `widget` and `tool` are given, `widget` wins. Shortcode mistakes (an unknown widget or a malformed path) show a discreet inline notice to logged-in editors only — visitors never see errors.

== Installation ==

1. In your WordPress dashboard, go to Plugins → Add New → Upload Plugin.
2. Upload practical-web-tools-calculators.zip and click Activate.
3. Add the shortcode to any post or page, for example `[pwt_calculator widget="mortgage"]`.

That's it — there is no settings screen. In the block editor, paste the shortcode into a Shortcode block, or use the Custom HTML block with the embed snippet style of your choice.

== Frequently Asked Questions ==

= Is it free? =

Yes. The plugin and all calculators are free, with no API key, account, or subscription.

= Does it upload my data? =

No. The nine bundled widgets are plain JavaScript that runs in the browser; nothing is sent anywhere. The iframe mode loads the calculator page from practicalwebtools.com like any embedded YouTube video would, but no plugin settings or user data are transmitted.

= Does it work in the block editor (Gutenberg)? =

Yes. Add a Shortcode block and paste `[pwt_calculator widget="mortgage"]`, or use a Custom HTML block. Both work in the classic editor and page builders too.

= Can I embed a calculator that is not one of the nine widgets? =

Yes. Find the calculator on https://practicalwebtools.com, copy its path from the address bar, and use it with the `tool` attribute: `[pwt_calculator tool="/finance/compound-interest-calculator"]`.

= Why does the widget show a "Powered by" link? =

The widgets are provided free of charge; the small attribution link to practicalwebtools.com is the fair-trade condition of using them.

== Changelog ==

= 1.0.0 =

* Initial release.
* Nine bundled client-side widgets: mortgage, loan, compound interest, BMI, calorie, due date, percentage, countdown timer, word counter.
* `[pwt_calculator]` shortcode with `widget`, `tool`, `width`, and `theme` attributes.
* Any-calculator iframe mode with auto-sizing and attribution link.
* Editor-only inline error notices for invalid shortcode attributes.

== Upgrade Notice ==

= 1.0.0 =

Initial release.

== A note on licenses ==

This plugin is licensed under the GNU General Public License v2.0 or later. The JavaScript widget files bundled in `assets/embed/` originate from https://practicalwebtools.com/embed and are released under the MIT License; redistributing MIT-licensed code inside GPL-licensed software is permitted, and the MIT notice is retained in each bundled file.
