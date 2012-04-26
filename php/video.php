<?php
$ft=new ft(ADMIN_PATH.MODULE."templates/");
$ft->define(array('main' => "video.html"));

$site_meta_title=$meta_title.get_meta($glob['pag'], $glob['lang'], 'title');
$site_meta_keywords=$meta_keywords.get_meta($glob['pag'], $glob['lang'], 'keywords');
$site_meta_description=$meta_description.get_meta($glob['pag'], $glob['lang'], 'description');

$youtube = ytchannel();
	
$ft->assign('YOUTUBE',$youtube);

$ft->assign('MESSAGE', get_error($glob['error'],$glob['success']));
$ft->parse('CONTENT','main');
//$ft->fastprint('CONTENT');
return $ft->fetch('CONTENT');


function ytchannel($atts = '') {
	
    $channel = 'RehabMyPatient';
    $limit = '10';
    $thumbnail_width = '150';
    $show_titles = '1';
    $showcase = 'AfboydF_Zww';
    $showcase_width = "640"; 
    $showcase_height = "390";
    
    $str = 'http://gdata.youtube.com/feeds/users/'.$channel.'/uploads?max-results='.$limit.'';
    $arr = xml2array($str);
    
	if (empty($arr)) { 
		echo('Youtube Channel "' . $channel . '" not found');
	}
	 
    $feed = $arr['feed'];
    $entries = $feed['entry'];
    
    $entries_output = ''; //string to contain entries
    $showcase_output = ''; //string to contain showcase content
    $output = '';  //create empty string to store content
   	
   	//create output: all videos in channel as thumbnails and showcase if requested
    foreach ($entries as $entry):
      
      $thumbnail = ($entry['media:group']['media:thumbnail']['0_attr']['url']);
      $title = ($entry['media:group']['media:title']);
      
      if ($showcase != ""):
        
        if (isset($_GET['entry'])) { 
        	$iframe_src = 'http://www.youtube.com/v/' . $_GET['entry'] . '?version=3&f=videos&app=youtube_gdata&autoplay=0';
        }
        else {
        	$iframe_src = 'http://www.youtube.com/v/' . $showcase . '?version=3&f=videos&app=youtube_gdata&autoplay=0';
        }
      	
      	$showcase_output = "<iframe id='currentVideo' height='" . $showcase_height . "' width='". $showcase_width . "' src='" . $iframe_src . "'></iframe>";
        
      endif;
      
      $entries_output .= "<div class='youtubechannelEntry'>";
      if ($show_titles == '1'):
        $entries_output .= "<h4>" . $title . "</h4>";
      endif;
      
      if ($showcase != '') { 
      	
				$full_path = $entry['link']['0_attr']['href'];
				$short_yt_code = preg_replace('%.*(v=|/v/)(.+?)(/|&|\\?).*%', '$2', $full_path);


      	$current_url = preg_replace(('/\?.*/'), '', $_SERVER['REQUEST_URI']);
        $new_vid_url = $current_url . "?entry=" . $short_yt_code;
        //$entries_output .= '<a href="'. $new_vid_url . '">';
        $entries_output .= '<a href="javascript: void(0);" class="changeVideoUrl" url="'.$short_yt_code.'">'; 
      }
      
      $entries_output .= "<img src='" . $thumbnail . "' width=" . $thumbnail_width . "/>";
      if ($showcase != '') { $entries_output .= "</a>"; }
      $entries_output .= "</div>";

    endforeach;
	
	$output = $showcase_output . $entries_output;
	return $output;

}


//getting php array from xml feed
function xml2array($url, $get_attributes = 1, $priority = 'tag')
{
    $contents = "";
    if (!function_exists('xml_parser_create'))
    {
        return array ();
    }
    $parser = xml_parser_create('');
    if (!($fp = @ fopen($url, 'rb')))
    {
        return array ();
    }
    while (!feof($fp))
    {
        $contents .= fread($fp, 8192);
    }
    fclose($fp);
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return; //Hmm...
    $xml_array = array ();
    $parents = array ();
    $opened_tags = array ();
    $arr = array ();
    $current = & $xml_array;
    $repeated_tag_index = array (); 
    foreach ($xml_values as $data)
    {
        unset ($attributes, $value);
        extract($data);
        $result = array ();
        $attributes_data = array ();
        if (isset ($value))
        {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value;
        }
        if (isset ($attributes) and $get_attributes)
        {
            foreach ($attributes as $attr => $val)
            {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }
        if ($type == "open")
        { 
            $parent[$level -1] = & $current;
            if (!is_array($current) or (!in_array($tag, array_keys($current))))
            {
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current = & $current[$tag];
            }
            else
            {
                if (isset ($current[$tag][0]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                { 
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    ); 
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if (isset ($current[$tag . '_attr']))
                    {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset ($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = & $current[$tag][$last_item_index];
            }
        }
        elseif ($type == "complete")
        {
            if (!isset ($current[$tag]))
            {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            }
            else
            {
                if (isset ($current[$tag][0]) and is_array($current[$tag]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    if ($priority == 'tag' and $get_attributes and $attributes_data)
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    ); 
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes)
                    {
                        if (isset ($current[$tag . '_attr']))
                        { 
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                        if ($attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                }
            }
        }
        elseif ($type == 'close')
        {
            $current = & $parent[$level -1];
        }
    }
    return ($xml_array);
}

?>