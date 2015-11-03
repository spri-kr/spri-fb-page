# SPRI Facebook Page Plugin

## 소개

페이스북 페이지의 포스트 피드를 가져와서 위젯과 숏코드로 표시할 수 있게 하는 플러그인이다.

## 설치방법

이 저장소를 다운로드하여 워드프레스 설치경로 밑의 `wp-contents/plugins/`에 압축을 풉니다.

## 사용법

이 플러그인을 사용하기 위해서는 페이스북에서 앱을 생성하는 과정이 필요합니다. 그리고 다음의 3가지가 필요합니다.

0. 페이스북 앱 ID
0. 페이스북 앱 시크릿
0. 페이스북 클라이언트 토큰

![Imgur](http://i.imgur.com/fywha3l.jpg)

페이스북 앱 생성후, 개별 앱 관리 화면에서 App ID와, App Secret을 확인합니다.

![Imgur](http://i.imgur.com/jIOqhyK.jpg)

앱의 Settings, Advanced에서 Client Token을 확인합니다.

![menu](http://i.imgur.com/3vQtfAg.jpg)

대시보드의 'SPRI Facebook page feed Settings'메뉴에 들어갑니다.

![key setting](http://i.imgur.com/MbbuTjH.jpg)

위에서 확인한 ID, 시크릿, 클라이언트 토큰을, 플러그인의 설정페이지에 입력하고 저장합니다. 이 이후로는 위젯과 숏코드를 사용할 수 있습니다.

### 숏코드

![Imgur](http://i.imgur.com/cnl6rtS.jpg)

`spri-fb-page-feed` 숏코드를 사용할 수 있습니다. 지원되는 파라미터는 다음과 같습니다

+ `page_id`: 필수입니다. 페이스북 페이지의 ID를 넣습니다. 기본값은 `spribook`입니다.
+ `number`: 한번에 몇개의 포스트를 표시할지 정합니다. 기본값은 6입니다.
+ `template`: 개별 포스트를 표시하는데 사용할 html템플릿을 결정합니다. 템플릿은 `spri-fb-page/template/` 하위에 위치합니다. 기본값은 `basic`입니다.
    + 템플릿 커스터마이징에 쓰이는 post의 변수는 다음과 같습니다:
    + `post->post_id`
    + `post->post_date`
    + `post->tags`
    + `post->picture`
    + `post->story`
    + `post->message`
    
![Imgur](http://i.imgur.com/4m05wIF.jpg)

숏코드를 사용하면 위와같은 화면을 페이지나 포스트에 표시할 수 있습니다. 

![Imgur](http://i.imgur.com/DZDzdbO.jpg)

우상단의 검색창에서 포스트에 쓰인 태그를 검색할 수 있습니다. 표시되는 태그에서만 검색 결과가 나옵니다. 

![Imgur](http://i.imgur.com/YkcfN8I.jpg)

태그를 선택후 검색을 클릭하면 위와같은 화면이 나옵니다. 이 경우는 '소프트웨어정책연구소'를 선택했을 경우입니다.

![Imgur](http://i.imgur.com/Y62u3M0.jpg)

숏코드가 삽입된 페이지 하단에는 페이지 네비게이션 링크가 있습니다.

### 위젯

![Imgur](http://i.imgur.com/mFi5Zua.jpg)

위젯 설정 화면입니다. 페이지 ID와 몇개의 Post를 보여줄것인지 입력하고, 저장하면 위젯 설정은 끝이납니다.

![Imgur](http://i.imgur.com/kksbfZP.jpg)

위젯은 위와같이 표시됩니다.

## 프로젝트 구조


```
├─css                       숏코드와 위젯에서 쓰이는 스타일 시트
├─js                        숏코드와 위젯에서 쓰이는 스크립트
├─lib
│  ├─jQuery-autoComplete    숏코드 자동완성
│  └─owl-carousel           위젯 슬라이드
├─template                  숏코드를 이용해서 출력할때 쓰이는 템플릿을 포함하는 폴더
└─vendor                    composer가 자동 생성한 폴더
```