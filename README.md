# Listen Again (Canstream)
A simple set of PHP functions, mainly intended for Wordpress, to create a list of most recent "Listen Again" for each show

Canstream provide a "Listen Again" service for the radio stations that they stream. This set of functions makes it possible to embed links to the most recent listen again audio file directly against each show presence (page, element) in the station's own website - which lets you keep your own look, feel, navigation.

The code is self-documented.

**Please note** the warnings that acquiring the catalogue of shows from Canstream is "expensive" (and probably slow), so use it sparingly. Ideally, you'd code one instance of $shows=listenagain_catalogue(rss_usl) into a page, and then render each show using listenagain_render($shows,$title) passing the title of the show you want.

This is very much an alpha-prototype. Don't rely on it being robust or perfect.
