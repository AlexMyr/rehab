<?php
/**********************************************************************
Copyright (C) 2003 Cornel Alexa

This program is free software; you can redistribute it and/or modify it 
under the terms of the GNU General Public License as published by the 
Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty 
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software Foundation, 
Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
************************************************************************/

/////////////////////////////////////////////////////////////////////////
//Class ft version 1.0 (born)
//Template engine class created by Cornel Alexa <cornelalexa@yahoo.com>
//Creation Date: 2003-05-17
//////////////////////////////////////////////////////////////////////////
// $Id: cls_ft.php,v 1.1.1.1 2003/10/22 12:00:46 tynu Exp $
//////////////////////////////////////////////////////////////////////////
//Some capabilities:
//                  -multiple template files nested or not
//                  -multiple dynamic blocks in one file nested or not
//                   (you can have more blocks and some of them in others)
//////////////////////////////////////////////////////////////////////////
//See the function comments for more info.
//The use style is like FastTemplate, except the clear_dynamic() which work 
//in other way.He's job is to remove all unprocessed dynamic blocks, so must be
//used after parse() as required.
/////////////////////////////////////////////////////////////////////////

class ft
{

var $root;     //hold the prefix path where the templates are located
var $templates;//hold the defined file(s) template and their content
var $tags;     //hold defined tags->value pairs  
var $blocks;   //hold dynamic blocks defined as block => parent pairs 
var $strip;    //if true (default) remove all remaining unassigned tags when parsing
/**
 * @return object ft
 * @param string root_dir = NULL 
 * @desc Class constructor.Use set_root() if you not specify the $root_dir
 */
function ft($root_dir=NULL)
{
    if($root_dir)
    {
        $this->set_root($root_dir);
    }
    $this->templates=array();
    $this->tags=array();
    $this->blocks=array();
    $this->strip=true;
}

/**
 * @return boolean
 * @param root_dir string
 * @desc Set the folder where template files are located.
 */
function set_root($root_dir)
{
    if('/' != substr($root_dir,strlen($root_dir)-1,1))
    {
        $root_dir.='/';
    }
    if(!is_dir($root_dir))
    {
        user_error("ft::set_root()-Unable to set root folder: $root_dir");
        return false;
    }
    $this->root=$root_dir;
    return true;
    
}

/**
 * @return void
 * @desc Specify if all unprocessed/unassigned tags are silent removed.
 */
function strict()
{
    $this->strip=false;
}

/**
 * @return void
 * @desc Specify if all unprocessed/unassigned tags are NOT silent removed.
 */
function no_strict()
{
    $this->strip=true;
}

/**
 * @return unknown
 * @param mixed_template array()
 * @desc Specify and load all required template files. 
 */
function define($mixed_template,$file_name=NULL)
{
    if('array' == gettype($mixed_template))
    {
        foreach($mixed_template as $t_id => $t_file)
        {
            $this->templates[$t_id]=$this->_get_file($t_file);
        }
        return true;
    }
    
    user_error("ft::define()-Expected array of templates(array('name' => 'file')).");
    return false;
}
/**
* @return boolean
* @param block_name string
* @param parent string
* @desc Set/create a dynamic block, and he's parent template.The 
        parent can be an already defined dynamic block. 
*/
function define_dynamic($block_name,$parent)
{
    if(!$block_name)
    {
        user_error("ft::define_dynamic()-Expected value for the 'block_name' parameter.");
        return false;
    }
    if(!$parent)
    {
        user_error("ft::define_dynamic()-Expected value for the parent template/block parameter.");
        return false;
    }
    if(!isset($this->templates[$parent]))
    {
        user_error("ft::define_dynamic()-Invalid (not defined) parent template/block name: $parent.");
        return false;
    }
    $this->blocks[$block_name]=$parent;
    $m_parent=$this->templates[$parent];
    $block_start="<!-- BEGIN DYNAMIC BLOCK: $block_name -->";
    $block_end="<!-- END DYNAMIC BLOCK: $block_name -->";
   
    $rez=preg_match_all("/$block_start(.*)$block_end/s",$m_parent,$new_parent);
    if(!$rez)
    {
        user_error("ft::define_dynamic()-Expected block '$block_name' not found.Please define the block in your template file.");
        return false;
    }
    if($rez > 1)
    {
        user_error("ft::define_dynamic()-Expected block '$block_name' found twice.A block name can be only once declared in your template file.");
        return false;
    }
    $this->templates[$block_name]=$new_parent[1][0];
    return true;
}

/**
 * @return boolean
 * @param mixed_tags_values mixed_var
 * @param string tag_value = NULL
 * @desc Create a tag=>value assignation.
         All tags are replaced in the parse() process.
         If the $mixed_tags_values is an array the second 
         parameter is omited.
 */
function assign($mixed_tags_values,$tag_value=NULL)
{
	if(empty($mixed_tags_values))
    {
        user_error("ft::assign()-Expected tag_name,tag_value or array of tag_name=>tag_value pairs for \$mixed_tags_values.");
        return false;
    }
    if('array' == gettype($mixed_tags_values))
    {
        foreach($mixed_tags_values as $tag_name => $tag_value)
        {
            $this->tags['{'.$tag_name.'}']=$tag_value;
        }
        return true;
    }
    $this->tags['{'.$mixed_tags_values.'}']=$tag_value;
    return true;
}

/**
 * @return boolean
 * @param out_section string
 * @param template_name string
 * @desc The function where the "magic" happend.
         The specified $template_name is parsed and a new tag with the 
         $out_section name is created in the collection.The parsed output is assigned here,
         so the original content remain untouched.You can retrieve he's content via fetch() function.
         If any future parse() call is made and the $out_section is in that template as a tag, then the content
         will replace that tag, so keep tracking the order of your nested files/blocks to be the same as calling 
         parse(), else you can get strange results.
 */
function parse($out_section,$template_name)
{
    if("." == substr($template_name,0,1))
    {
        $append=true;
        $template_name=substr($template_name,1);
    }
    
    if(!isset($this->templates[$template_name]))
    {
        user_error("ft::parse()-Invalid (not defined) template name: $template_name.Use ft::define() first.");
        return false;
    }
    if(isset($this->blocks[$template_name]))
    {
        if(!$this->_parse_dynamic($out_section,$template_name))
        {
            return false;
        }
    }
    $t_content=$this->templates[$template_name];
    
    $t=array_keys($this->tags);
    $v=array_values($this->tags);
    $t_content=str_replace($t,$v,$t_content);
    
    if($this->strip)
    {
        $t_content=ereg_replace("{([A-Z0-9_]+)}",'',$t_content);
    }
   	$t_content = preg_replace('/<!-- BEGIN DYNAMIC BLOCK: (.*?) -->(.*?)<!-- END DYNAMIC BLOCK: \1 -->/is','',$t_content);
    
    if($append)
    {
        $this->tags['{'.$out_section.'}'].=$t_content;
    }
    else
    {
        $this->tags['{'.$out_section.'}']=$t_content;
    }
    unset($t_content);
    return true;
}

/**
 * @return boolean
 * @param out_tag string
 * @param template_name string
 * @desc Private function called by parse() if the specified template
         name is a dynamic block name.Strip aou the block content and create
         a new $out_tag in the collection with it.
 */
function _parse_dynamic($out_tag,$template_name)
{
    if(!$out_tag)
    {
        user_error("ft::_parse_dynamic()-Expected value for the 'out_tag' parameter.");
        return false;
    }
    if(!isset($this->templates[$template_name]))
    {
        user_error("ft::_parse_dynamic()-Invalid (not defined) template/block name: $template_name.Use ft::define[_dynamic]() first.");
        return false;
    }
    
    $m_template=$this->templates[$this->blocks[$template_name]];
    
    $block_start="<!-- BEGIN DYNAMIC BLOCK: $template_name -->";
    $block_end="<!-- END DYNAMIC BLOCK: $template_name -->";
    
    $m_template=preg_replace("/$block_start(.*)$block_end/s",'{'.$out_tag.'}',$m_template); 
    $this->templates[$this->blocks[$template_name]]=$m_template;
    unset($m_template);
    return true;
}

/**
 * @return string
 * @param tag_name string
 * @desc return the content of the specified $tag_name(parse or not)
         You must keep track of what is parsed and what is not....
 */
function fetch($tag_name)
{
    return $this->tags['{'.$tag_name.'}'];
}

/**
 * @return boolean
 * @param out_section string
 * @param block_name = NULL string
 * @desc Clear a marked dynamic block from an $out_section or a assigned tag if the
         $block_name is specified.Else will strip ALL defined blocks in your template!!!.
         Use with care,especialy when you have two blocks inside an already defined one,
         and one of the childs remain unprocesed after parse().
         This can strip out all your content!
 */
function clear_dynamic($out_section,$block_name=NULL)
{
    if(!$block_name)
    {
        $block_name="([a-zA-Z0-9_]+)";
    }
    $m_template=$this->tags['{'.$out_section.'}'];
    
    $block_start="<!-- BEGIN DYNAMIC BLOCK: $block_name -->";
    $block_end="<!-- END DYNAMIC BLOCK: $block_name -->";
    
    $m_template=preg_replace("/$block_start(.*)$block_end/s"," ",$m_template);
    $this->tags['{'.$out_section.'}']=$m_template;
    return true;
}

/**
 * @return void
 * @param tag_name string
 * @desc Print out to browser a defined tag/out section (one created by parse()).
 */
function ft_print($tag_name)
{
    echo $this->fetch($tag_name);
}


/**
 * @return boolean
 * @param tag_name string
 * @desc clear a defined tag, or an out section created by parse().
 */
function clear($tag_name)
{
    unset($this->tags['{'.$tag_name.'}']);
    return true;
}
/**
 * @return string
 * @param file_path string Full path to a text file.
 * @desc Open a given file and return he's content as string.
 * @scope Private
 */
function _get_file($file_path)
{
    if(FALSE === ($f_hwnd=@fopen($this->root.$file_path,'r')))
    {
        user_error("ft::_get_file()-Failed to open template file: ".$this->root.$file_path);
        return false;
    }
    if(-1 === ($f_content=@fread($f_hwnd,@filesize($this->root.$file_path))))
    {
        user_error("ft::_get_file()-Failed to read template file: ".$this->root.$file_path);
        return false;
    }
    fclose($f_hwnd);
    return $f_content;
}
}//end class ft

//created for easy replacement of FastTemplate
class FastTemplate extends ft 
{
function FastTemplate($root_dir=NULL)
{
    if($root_dir)
    {
        $this->set_root($root_dir);
    }
    $this->templates=array();
    $this->tags=array();
    $this->blocks=array();
    $this->strip=true;
}

/**
 * @return void
 * @param tag_name string
 * @desc See ft::ft_print()
 */
function FastPrint($tag_name)
{
    $this->ft_print($tag_name);
}
}//end class fast template
?>