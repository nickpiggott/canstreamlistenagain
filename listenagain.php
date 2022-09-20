<?php
/** Code Fragment to implement embeddable Listen Again links from Canstream

You should make sure this file is included in Wordpress theme plugins

a "show" array is a key-value array of:
title (type: string)
pubdate (type: datetime)
url (type: string)

listenagain_catalogue(<rss url>)
takes: a valid http/https URL to a Canstream RSS feed
returns: an array of shows

** NOTE **
This call is "expensive" as it calls the Canstream server to get the RSS feed - there is no caching.
Please use it ONCE per page if possible

listenagain_render(<shows>,<title>)
takes: an array of show arrays (the output of listenagain_catalogue)
takes: a title of a show to find the listen again information for
returns: a string of HTML to render into the page

listenagain_shortcode(<atts>)
takes: a "title" attribute on a Wordpress shortcode "listenagain"
returns: a string of HTML to render into the page for ONE show with the given title

** NOTE **
This call is "expensive" as it calls uses listenagain_catalogue, which calls the Canstream server to get the RSS feed - there is no caching.
Please use it ONCE per page if possible


**/

// retrieves the most revent listen again for every show in the RSS feed
function listenagain_catalogue($rss_url) {
	$date_regex_pattern = "/ (Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday) [0-9]{1,2} (January|February|March|April|May|June|July|August|September|October|November|December)/i";
	$xml = simplexml_load_file($rss_url);
        $shows = array();

	foreach($xml->channel[0]->item as $item) {
	 	$title = preg_replace($date_regex_pattern, "", $item->title);
		$pub_date = DateTime::createFromFormat('D, d M Y H:i:s O', $item->pubDate);
		$listenagain_url = $item->enclosure->attributes()->{'url'};
       		$shows[] = array("title" => $title,"pub_date" => $pub_date, "url" => $listenagain_url);
	}

	usort($shows, function ($a, $b) {
		return $a['pub_date'] <= $b['pub_date'];
	});

	$unique_shows = array_reverse(array_values(array_column(
		array_reverse($shows),
		null,
		'title'
	)));

        return $unique_shows;
}

// renders the HTML for a show, given the array of shows and a show title to look for
function listenagain_render($shows,$title) {

        $key = array_search($title,array_column($shows,"title"));
        $show = $shows[$key];

	return '<div id="listen-again-'.str_replace(' ', '', $show['title']).'" class="listen-again-embed">'.$show['title'].'</div><div class="listen-again-date">'.date_format($show['pub_date'],'l dS F, H:i').'</div><div class="listen-again-player"><a href="'.$show['url'].'">PLAYER</a></div>';

}

// implements the [listenagain title="title"] shortcode
function listenagain_shortcode($atts) {

	$shows = listenagain_catalogue('https://podcast.canstream.co.uk/{your station name here}/audio/rss.xml');	
	return listenagain_render($shows,$atts['title']);

}
add_shortcode( 'listenagain', 'listenagain_shortcode' );


?>
