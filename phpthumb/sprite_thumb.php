<?php

if(isset($_GET['img']))
{
  get_sprite_thumb($_GET['img']);
  exit;
}
elseif(isset($_GET['bimg']))
{
  get_sprite($_GET['bimg']);
  exit;
}

define(LOCAL_PATH_TO_IMAGES, dirname(dirname(__FILE__)));

function get_sprite($sprite_name)
{
  if($sprite_name)
  {
    $sprite_name = str_replace('_sprite', '', $sprite_name);
    $images_array = explode('_', $sprite_name);
    
    $count_per_sprite = 9;
    $sprite_width = 3;
    $sprite_height = 3;
    $image_side_size_x = 132;
    $image_side_size_y = 138;
    
    $image_side_thumb_size_x = 64;
    $image_side_thumb_size_y = 64;
    
    $count_of_exercises = count($images_array);
    $count_of_sprites = ceil($count_of_exercises/$count_per_sprite);
    
    //fill array for sprites
    $exercises_images_sprites = array();
    $sprite_names = array();
    
    $k = 0;
    for($i=0; $i<$count_of_sprites; $i++)
    {
      $programs_images_sprites[$i] = array();
      for($j=0; $j<$count_per_sprite; $j++)
      {
        if(isset($images_array[$k]))
          $exercises_images_sprites[$i][] = $images_array[$k++];
        else
          break 2;
      }
    }
    
    foreach($exercises_images_sprites as $exercises_images_sprite)
    {
      $current_sprite_name = generate_sprite_name($exercises_images_sprite, false);
      $sprite_names[] = $current_sprite_name;
      
      $dest = imagecreatetruecolor($image_side_size_x*$sprite_width, $image_side_size_y*$sprite_height+$sprite_width*$image_side_thumb_size_y);
      $tmp_bimg = imagecreatetruecolor($image_side_size_x, $image_side_size_y);
      $tmp_img = imagecreatetruecolor($image_side_thumb_size_x, $image_side_thumb_size_y);
      
      $counter = 0;
      for($i=0;$i<$sprite_height;$i++)
      {
        for($j=0;$j<$sprite_width;$j++)
        {
          if(!isset($exercises_images_sprite[$counter]))
            break 2;

          $src = imagecreatefromjpeg(dirname(dirname(__FILE__)).'/upload/'.$exercises_images_sprite[$counter].'.jpg');
          imagecopyresampled($tmp_bimg, $src, 0, 0, 0, 0, $image_side_size_x, $image_side_size_y, imagesx($src), imagesy($src));
          imagecopymerge($dest, $tmp_bimg, $j*$image_side_size_x, $i*$image_side_size_y, 0, 0, $image_side_size_x, $image_side_size_y, 100);
          
          imagecopyresampled($tmp_img, $src, 0, 0, 0, 0, $image_side_thumb_size_x, $image_side_thumb_size_y, imagesx($src), imagesy($src));
          imagecopymerge($dest, $tmp_img, $j*$image_side_thumb_size_x, $i*$image_side_thumb_size_y+$image_side_size_y*$sprite_width, 0, 0, $image_side_thumb_size_x, $image_side_thumb_size_y, 100);
          
          imagedestroy($src);
          $counter++;
        }
      }
      
      imagejpeg($dest);
    }
  }
}

function get_sprite_thumb($sprite_name)
{
  if($sprite_name)
  {
    $sprite_name = str_replace('_thumb_sprite', '', $sprite_name);
    $images_array = explode('_', $sprite_name);
    
    $count_per_sprite = 6;
    $sprite_width = 1;
    $sprite_height = 6;
    $image_side_size_x = 64;
    $image_side_size_y = 64;
    
    $count_of_exercises = count($images_array);
    $count_of_sprites = ceil($count_of_exercises/$count_per_sprite);
    
    //fill array for sprites
    $exercises_images_sprites = array();
    $sprite_names = array();
    
    $k = 0;
    for($i=0; $i<$count_of_sprites; $i++)
    {
      $programs_images_sprites[$i] = array();
      for($j=0; $j<$count_per_sprite; $j++)
      {
        if(isset($images_array[$k]))
          $exercises_images_sprites[$i][] = $images_array[$k++];
        else
          break 2;
      }
    }
    
    foreach($exercises_images_sprites as $exercises_images_sprite)
    {
      $current_sprite_name = generate_sprite_name($exercises_images_sprite, true);
      $sprite_names[] = $current_sprite_name;
      
      $dest = imagecreatetruecolor($image_side_size_x*$sprite_width, $image_side_size_y*$sprite_height);
      $tmp_img = imagecreatetruecolor($image_side_size_x, $image_side_size_y);
      
      $counter = 0;
      for($i=0;$i<$sprite_height;$i++)
      {
        for($j=0;$j<$sprite_width;$j++)
        {
          if(!isset($exercises_images_sprite[$counter]))
            break 2;
          $src = imagecreatefromjpeg(dirname(dirname(__FILE__)).'/upload/'.$exercises_images_sprite[$counter].'.jpg');
          imagecopyresampled($tmp_img, $src, 0, 0, 0, 0, $image_side_size_x, $image_side_size_y, imagesx($src), imagesy($src));
          imagecopymerge($dest, $tmp_img, $j*$image_side_size_x, $i*$image_side_size_y, 0, 0, $image_side_size_x, $image_side_size_y, 100);
          imagedestroy($src);
          $counter++;
        }
      }
      
      imagejpeg($dest);
    }
  }
}

function get_exercises_sprite_names($images_array, $is_thumb = false)
{
  if($is_thumb)
    $count_per_sprite = 6;
  else
    $count_per_sprite = 9;
  
  $count_of_exercises = count($images_array);
   $count_of_sprites = ceil($count_of_exercises/$count_per_sprite);
  $sprite_names = array();
    
  $k = 0;
  for($i=0; $i<$count_of_sprites; $i++)
  {
    $programs_images_sprites[$i] = array();
    for($j=0; $j<$count_per_sprite; $j++)
    {
      if(isset($images_array[$k]))
        $exercises_images_sprites[$i][] = $images_array[$k++];
      else
        break 2;
    }
  }
  
  foreach($exercises_images_sprites as $exercises_images_sprite)
  {
    $cur_sprite_name = generate_sprite_name($exercises_images_sprite, $is_thumb);
    $sprite_names[] = str_replace('.jpg', '', $cur_sprite_name);
  }
  
  return $sprite_names;
}

function get_exercises_sprite_thumb($images_array)
{
  if(!empty($images_array))
  {
    $count_per_sprite = 6;
    $sprite_width = 1;
    $sprite_height = 6;
    $image_side_size_x = 64;
    $image_side_size_y = 64;
    
    $count_of_exercises = count($images_array);
    $count_of_sprites = ceil($count_of_exercises/$count_per_sprite);
    
    //fill array for sprites
    $exercises_images_sprites = array();
    $sprite_names = array();
    
    $k = 0;
    for($i=0; $i<$count_of_sprites; $i++)
    {
      $programs_images_sprites[$i] = array();
      for($j=0; $j<$count_per_sprite; $j++)
      {
        if(isset($images_array[$k]))
          $exercises_images_sprites[$i][] = $images_array[$k++];
        else
          break 2;
      }
    }
    
    foreach($exercises_images_sprites as $exercises_images_sprite)
    {
      $current_sprite_name = generate_sprite_name($exercises_images_sprite, true);
      $sprite_names[] = $current_sprite_name;
      //if(check_sprite_exists($current_sprite_name))
      //  continue;
      
      $dest = imagecreatetruecolor($image_side_size_x*$sprite_width, $image_side_size_y*$sprite_height);
      $tmp_img = imagecreatetruecolor($image_side_size_x, $image_side_size_y);
      
      $counter = 0;
      for($i=0;$i<$sprite_height;$i++)
      {
        for($j=0;$j<$sprite_width;$j++)
        {
          if(!isset($exercises_images_sprite[$counter]))
            break 2;
          $src = imagecreatefromjpeg(LOCAL_PATH_TO_IMAGES.'/upload/'.$exercises_images_sprite[$counter]);
          imagecopyresampled($tmp_img, $src, 0, 0, 0, 0, $image_side_size_x, $image_side_size_y, imagesx($src), imagesy($src));
          imagecopymerge($dest, $tmp_img, $j*$image_side_size_x, $i*$image_side_size_y, 0, 0, $image_side_size_x, $image_side_size_y, 100);
          imagedestroy($src);
          $counter++;
        }
      }
      
      // Output and free from memory
      imagejpeg($dest);
      //imagejpeg($dest, LOCAL_PATH_TO_IMAGES.'/upload/thumbs/'.$current_sprite_name);
      imagedestroy($dest);
    }
    
    return $sprite_names;
  }
  else
    return false;
}

function get_exercises_sprite($programs_images)
{
  $count_per_sprite = 9;
  $sprite_width = 3;
  $sprite_height = 3;
  $image_side_size_x = 132;
  $image_side_size_y = 138;
  
  $image_side_thumb_size_x = 64;
  $image_side_thumb_size_y = 64;
  
  $count_of_programs = count($programs_images);
  $count_of_sprites = ceil($count_of_programs/$count_per_sprite);
  
  //fill array for sprites
  $programs_images_sprites = array();
  $sprite_names = array();
  
  $k = 0;
  for($i=0; $i<$count_of_sprites; $i++)
  {
    $programs_images_sprites[$i] = array();
    for($j=0; $j<$count_per_sprite; $j++)
    {
      if(isset($programs_images[$k]))
        $programs_images_sprites[$i][] = $programs_images[$k++];
      else
        break 2;
    }
  }
  
  foreach($programs_images_sprites as $programs_images_sprite)
  {
    $current_sprite_name = generate_sprite_name($programs_images_sprite);
    $sprite_names[] = $current_sprite_name;
    if(check_sprite_exists($current_sprite_name))
      continue;
    
    $dest = imagecreatetruecolor($image_side_size_x*$sprite_width, $image_side_size_y*$sprite_height+$sprite_width*$image_side_thumb_size_y);
    $tmp_img = imagecreatetruecolor($image_side_thumb_size_x, $image_side_thumb_size_y);
    
    $counter = 0;
    for($i=0;$i<$sprite_height;$i++)
    {
      for($j=0;$j<$sprite_width;$j++)
      {
        if(!isset($programs_images_sprite[$counter]))
          break 2;
        $src = imagecreatefromjpeg(LOCAL_PATH_TO_IMAGES.'/upload/'.$programs_images_sprite[$counter]);
        
        imagecopyresampled($tmp_img, $src, 0, 0, 0, 0, $image_side_thumb_size_x, $image_side_thumb_size_y, imagesx($src), imagesy($src));
        imagecopymerge($dest, $tmp_img, $j*$image_side_thumb_size_x, $i*$image_side_thumb_size_y+$image_side_size_y*$sprite_width, 0, 0, $image_side_thumb_size_x, $image_side_thumb_size_y, 100);
        
        imagecopymerge($dest, $src, $j*$image_side_size_x, $i*$image_side_size_y, 0, 0, $image_side_size_x, $image_side_size_y, 100);
        imagedestroy($src);
        $counter++;
      }
    }
    
    // Output and free from memory
    imagejpeg($dest, LOCAL_PATH_TO_IMAGES.'/upload/thumbs/'.$current_sprite_name);
    imagedestroy($dest);
  }
  return $sprite_names;
}

function generate_sprite_name($sprite, $is_thumb = false)
{
  $new_sprite = array();
  foreach($sprite as $image)
  {
    $new_sprite[] = str_replace(array('.jpeg', '.png', '.jpg'), '', $image);
  }

  $sprite_name = implode('_', $new_sprite);
  
  if($is_thumb)
    $sprite_name .= '_thumb';
  
  $sprite_name .= '_sprite.jpg';

  return $sprite_name;
}

function check_sprite_exists($sprite_name)
{

  if(file_exists(LOCAL_PATH_TO_IMAGES.'/upload/thumbs/'.$sprite_name))
    return true;
  return false;
}

function get_sprite_name_by_image($cur_sprite_names, $image_name, $is_thumb = false)
{
  $image_name = str_replace(array('.jpeg', '.png', '.jpg'), '', $image_name);

  foreach($cur_sprite_names as $sprite_name)
  {
    if(strpos($sprite_name, $image_name) > -1)
    {
      if($is_thumb)
      {
        if(strpos($sprite_name, '_thumb_') > -1)
          return $sprite_name;
      }
      else
      {
        if(!strpos($sprite_name, '_thumb_'))
          return $sprite_name;
      }
    }
  }
  return '';
}


?>