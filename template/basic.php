<?php

/*
 * This file is basic template for display articles
 * */

$temp_date        = date( 'Y년 m월 d일', strtotime( $post->post_date ) );
$target           = '_blank';
$facebook_address = 'http://facebook.com/' . $post->post_id;


# Picture Block
if ( isset( $post->picture ) ) {
	$picture = <<<PIC
<img src="{$post->picture}" alt="" class="post_thumb">
PIC;
} else {
	$picture = "";
}


# Message Block
$message = <<< ARTICLE
<p class='message'> {$post->message} </p>
ARTICLE;


# Link Block
if ( isset( $post->link ) ) {
	$link = <<< ARTICLE
<p class='url_link pull-right'>
자세한 정보는 링크에서 확인할 수 있습니다: <a href='{$post->link}' target="{$target}">보러 가기</a>
</p>
ARTICLE;
} else {
	$link = "";
}


# Date Block
$date = <<< ARTICLE
<p class='date pull-left'>{$temp_date}</p>
ARTICLE;


# template treat
$template = "";
if(isset($_REQUEST['template'])){
	$template = "&template=".$_REQUEST['template'];
}

# Tag Block
$tag_html = "<div class='tag_list clear-both pull-right'><p>";
$tags     = array_map(
	function ( $t ) {
		return "<a class='tag_link' href='?t={$t}__TEMPLATE__'>#{$t}</a>";
	},
	$post->tags );;

$tag_html .= implode( ", ", $tags );

// template injection
$tag_html = str_replace( "__TEMPLATE__", $template, $tag_html );

$tag_html .= "</p></div>";


# End Block
$end .= "<hr class='clear-both' />";



# Assemble
$html .= <<< HTML
<div class='date-link-head'>
{$date}
{$link}
</div>

<div class="clear-both">
{$picture}
{$message}
</div>

{$tag_html}
{$end}
HTML;
