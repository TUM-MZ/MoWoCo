<?php
defined('MOODLE_INTERNAL') || die();

class filter_wowza extends moodle_text_filter {
    public function filter($text, array $options = array()) {
        global $CFG, $PAGE;

        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }
        if (stripos($text, '</a>') === false) {
            // performance shortcut - all regexes bellow end with the </a> tag,
            // if not present nothing can match
            return $text;
        }
        if (stripos($text, 'rtmp') === false) {
            return $text;   // If it lacks rtmp, it can't be the Wowza sever.
        }

        $newtext = $text; // we need to return the original value if regex fails!

        $search = '/<a\s[^>]*href="([^"#\?]+\.(mp4|m4v)([#\?][^"]*)?)"[^>]*>([^>]*)<\/a>/is';
        $newtext = preg_replace_callback($search, 'filter_wowza_callback', $newtext);
            
        if (empty($newtext) or $newtext === $text) {
            // error or not filtered
            unset($newtext);
            return $text;
        }
        return $newtext;
     }   
      
}        
        function filter_wowza_callback($link) {
            global $CFG, $PAGE;
            static $count = 0;

            $count++;
            $id = 'filter_wowza_'.time().'_'.$count; //we need something unique because it might be stored in text cache

	//list($urls, $width, $height) = filter_mediaplugin_parse_alternatives($link[1], 0, 0);
    //TODO: check if we have more than one link
    
            $width  = null;
            $height = null;
            $matches = null;

            if (preg_match('/#d=([\d]{1,4})x([\d]{1,4})$/i', $link[1], $matches)) { // #d=640x480
                $width  = $matches[1];  
                $height = $matches[2];  
            }
            if (preg_match('/\?d=([\d]{1,4})x([\d]{1,4})$/i', $link[1], $matches)) { // old style file.ext?d=640x480
                $width  = $matches[1];
                $height = $matches[2];
                $url = str_replace($matches[0], '', $link[1]); 
            }

            $link[1] = str_replace('&amp;', '&', $link[1]);
            /*   $link[1] = clean_param($link[1], PARAM_URL);
            if (empty($link[1])) {
                break;
            }
        */
            $autosize = false;
            if (!$width and !$height) { // There isn't an automatic video size detection yet
                $width    = 640;
                $height   = 360;
                $autosize = true;
            }

            // part the url in its elements
            $completeURL = parse_url($link[1]); 
            $streamerprotokoll = $completeURL["scheme"] . "://";
            $port = ( $completeURL["port"] ) ? ":" . $completeURL["port"] : "";
            $streamer= $completeURL["host"] . $port .substr($completeURL["path"],0,strpos($completeURL["path"],"/",1)+1);
            $mediapath = substr($completeURL["path"],strpos($completeURL["path"],"/",1)+1);
            $mediatype = (stripos($mediapath,"mp4:")===false) ? "mp4:" : ""; 
            $playerpath = $CFG->wwwroot . '/filter/wowza'; 

            $client = $_SERVER["HTTP_USER_AGENT"]; 
            //if(!(stripos($client,"iPod")===false)||!(stripos($client,"iPad")===false)||!(stripos($client,"iPhone")===false)){
            //    $ios = true;
           // $client ='Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11';
if(!(stripos($client,"iPod")===false)||!(stripos($client,"iPad")===false)||!(stripos($client,"iPhone")===false)){
$output = <<<EOT
    <video controls width=$width height=$height src="http://$streamer$mediapath/playlist.m3u8" />
EOT;
} else {
$output = <<<EOT
<object  id="player" data="$playerpath/flowplayer-3.2.14.swf" type="application/x-shockwave-flash" width=$width height=$height>
<param name="allowfullscreen" value="true">
<param name="allowscriptaccess" value="always">
<param name="quality" value="high">
<param name="cachebusting" value="false">
<param name="bgcolor" value="#000000">
<param name="flashvars" value="config={
				'clip':{
					'url':'$mediatype$mediapath',
					'provider':'lrzwowza'
				},
				'plugins': {
					'lrzwowza': {
						'url':'flowplayer.rtmp-3.2.11.swf',
						'netConnectionUrl':'$streamerprotokoll$streamer'
					}
				}}
				">
</object>
EOT;
}
            return $output;
}
?>
