# SPRI Facebook Page Plugin

## Introduction

This plugin displays facebook page's feed on wordpress widget and post content by shortcode

## Installation

Download this repository, unzip at wordpress plugins directory

## How to use

This plugin needs facebook app. you will want obtain these things. 

0. App's ID
0. App's Secret
0. Client Token

![Imgur](http://i.imgur.com/fywha3l.jpg)

You can find App Id and App secret from app dashboard page.

![Imgur](http://i.imgur.com/jIOqhyK.jpg)

You can find Client Token from `setting > advanced` page.

![menu](http://i.imgur.com/3vQtfAg.jpg)

Go to wordpress `Dashboard > SPRI Facebook page feed Settings`.  

![key setting](http://i.imgur.com/MbbuTjH.jpg)

Put your App ID, App Secret, Client Token onto option page. Save them. You can now use shortcode and widget

### Shortcode

![Imgur](http://i.imgur.com/cnl6rtS.jpg)

You can use `spri-fb-page-feed` shortcode. Available parameters are here.

+ `page_id`: Require. put page id you want to display. default value is `spribook`.
+ `number`: Set how many posts will be displayed. Default is 6.
+ `template`: Set what template be used to display post. template is stored at `spri-fb-page/template/`. default values is `basic`. variables uesed to customizing template are here 
    + `post->post_id`
    + `post->post_date`
    + `post->tags`
    + `post->picture`
    + `post->story`
    + `post->message`
    
![Imgur](http://i.imgur.com/4m05wIF.jpg)

You can show facebook posts as a post or page content 

![Imgur](http://i.imgur.com/DZDzdbO.jpg)

you can search tags used in posts at upper right search box. search result only available with listed tags.

![Imgur](http://i.imgur.com/YkcfN8I.jpg)

As you select tag and click the search button, you can see this screen. In this case, '소프트웨어정책연구소' was selected and searched tag.

![Imgur](http://i.imgur.com/Y62u3M0.jpg)

There are pagination links at bottom of posts content 

### Widget

![Imgur](http://i.imgur.com/mFi5Zua.jpg)

Widget configuration page. You can set page id and number of posts will be displayed.

![Imgur](http://i.imgur.com/kksbfZP.jpg)


## 프로젝트 구조


```
├─css                       css files uesed at slide and shortcode
├─js                        js files uesed at slide and shortcode
├─lib
│  ├─jQuery-autoComplete    auto complete lib uesed at shortcode
│  └─owl-carousel           widget slide lib
├─template                  shortcode template directory
└─vendor                    auto generated directory by composer
```