<?php
$src = $_GET['src'];
$parts = explode('/', $src);
$filename = array_pop($parts);
$img_src = implode('/', $parts).'/thumbs/'.$filename;
if(is_file($img_src)){
    header('Content-Disposition: inline; filename="'.$img_src.'"');
    header("Content-Type: image/jpeg");
    $sour = ImageCreateFromjpeg($img_src);
    ImageJPEG($sour, "", 80);
    imagedestroy($sour);
}
else{
    ob_start();
    require_once('./phpThumb.php');
    $img = ob_get_contents();
    if(strlen($img) > 500){
        $fp = fopen($img_src, 'w');
        fwrite($fp, $img);
        fclose($fp);
    }
    ob_end_flush();
}
exit;
?>