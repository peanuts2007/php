<?php

function get_rss_feed_as_html($nuus_url, $max_nuus = 500, $show_description = true, $max_words = 0, $cache_timeout = 7200, $cache_prefix = "/tmp/pullNews-")
{
    $result = "";
  
    $rss = new DOMDocument();
    $cache_file = $cache_prefix . md5($nuus_url);
    
    if ($cache_timeout > 0 && is_file($cache_file) && (filemtime($cache_file) + $cache_timeout > time())) {
            $rss->load($cache_file);
    } else {
        $rss->load($nuus_url);
       
    }
    $feed = array();
    foreach ($rss->getElementsByTagName('item') as $node) {
        $item = array (
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
            'content' => $node->getElementsByTagName('description')->item(0)->nodeValue,
            'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
            
        );
        $content = $node->getElementsByTagName('encoded');
        if ($content->length > 0) {
            $item['content'] = $content->item(0)->nodeValue;
        }
        array_push($feed, $item);
    }
    
    if ($max_nuus > count($feed)) {
        $max_nuus = count($feed);
    }
    $result .= '<ul class="feed-lists">';
    for ($x = 0;$x < $max_nuus; $x++) {
        $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
        $link = $feed[$x]['link'];
        $result .= '<li class="feed-item">';
        $result .= '<div class="feed-title"><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong></div>';
       
        if ($show_description) {
            $description = $feed[$x]['desc'];
            $content = $feed[$x]['content'];
           
            $has_image = preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
            $description = strip_tags(preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/s', "$1$3", $description), '');
            if ($max_words > 0) {
                $arr = explode(' ', $description);
                if ($max_words < count($arr)) {
                    $description = '';
                    $w_cnt = 0;
                    foreach($arr as $w) {
                        $description .= $w . ' ';
                        $w_cnt = $w_cnt + 1;
                        if ($w_cnt == $max_words) {
                            break;
                        }
                    }
                    $description .= " ...";
                }
            }
            
            if ($has_image == 1) {
                $description = '<img class="feed-item-image" src="' . $image['src'] . '" />' . $description;
            }
            $result .= '<div class="feed-description">' . $description;
            $result .= ' <a href="'. $link .'" title="'. $title .'" target="_blank">Continue Reading &raquo;</a>'.'</div>';
        }
        $result .= '</li>';
    }
    $result .= '</ul>';
    return $result;
}

function output_rss_feed($nuus_url, $max_nuus = 10, $show_description = true, $max_words = 0)
{
    echo get_rss_feed_as_html($nuus_url, $max_nuus, $show_description, $max_words);
}

?>