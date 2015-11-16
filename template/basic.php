<?php

/*
 * This file is basic template for display articles
 * */

$temp_date = date( 'Y년 m월 d일', strtotime( $post->post_date ) );
$target    = '_blank';
$href      = 'http://facebook.com/' . $post->post_id;

if ( isset( $post->picture ) ) {
	$html .= <<<PIC
<img src="{$post->picture}" alt="" class="post_thumb">
PIC;

}
if(isset($post->story)){
	$html .= <<<STORY
	<p class='story'> {$post->story} </p>
STORY;
}


$html .= <<< ARTICLE
<a href="{$href}" target="{$target}">
<p class='message'> {$post->message} </p>
</a>
ARTICLE;

if(isset($post->link))

$html .= <<< ARTICLE
<p>
<a href='{$post->link}' target="{$target}">
{$post->link}
</a>
</p>
ARTICLE;

$html .= <<< ARTICLE
<p class='date'>{$temp_date}</p>
ARTICLE;

$html .= "<div class='tag_list'><p>";

$tags = array_map( function ( $t ) {
	return "<a class='tag_link' href='?t={$t}'>#{$t}</a>";
},
	$post->tags );;
$html .= implode( ", ", $tags );

$html .= "</p></div>";

$html .= "<hr class='clear-both' />";
