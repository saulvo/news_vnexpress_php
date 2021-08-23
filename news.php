<?php
  require 'lib/simple_html_dom.php';

  $cat = $_GET["category"];
  $sub_cat = $_GET["sub"];
  $page = $_GET["page"]; settype($page, 'int');

  $base_url = 'https://vnexpress.net/';

  function getQueryUrl($url, $page, $cat, $sub_cat) {
    $result = $url.$cat;

    if($sub_cat !== '') {
      $result = $result.'/'.$sub_cat;
    }
    if($page > 1) {
      $result = $result.'-p'.$page;
    }


    return $result;
  }
  $query_url = getQueryUrl($base_url, $page, $cat, $sub_cat);

  $HTML = file_get_html($query_url);
  $news_list = $HTML->find(".list-news-subfolder .item-news-common");

  function getIdFromLink ($link) {
    $arr = explode("-", $link);
    return str_replace(".html", "", $arr[count($arr) - 1]);
  }

  $data_news = [];

  foreach($news_list as $news_item) {
    $b_thumb = $news_item->find(".thumb img", 0);
    $b_desc = $news_item->find(".description a", 0);
    $b_link = $news_item->find(".title-news a", 0);

    if(isset($b_thumb->innertext) && isset($b_desc->innertext) && isset($b_link->innertext)) {
      $thumb = $b_thumb->getAttribute('data-src') ? $b_thumb->getAttribute('data-src') : $b_thumb->src;
      $title = $b_thumb->alt;
      $link = $b_link->href;
      $desc = $b_desc->innertext;
      $id = getIdFromLink($link);

      array_push($data_news, new News(
        $id, $title, $desc, $thumb, $link
      ));
    }
  }

  echo json_encode($data_news);

  class News {
    public function __construct(
      public string $id,
      public string $title,
      public string $desc,
      public string $thumb,
      public string $link,
    ) {}
  }
?>