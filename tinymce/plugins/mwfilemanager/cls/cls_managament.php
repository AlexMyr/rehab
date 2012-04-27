<?
class managament
{
	var $filetypes = array(
                        '.ai' => 1,
                        '.bin' => 1,
                        '.bmp' => 1,
                        '.css' => 1,
                        '.csv' => 1,
                        '.doc' => 1,
                        '.dot' => 1,
                        '.eps' => 1,
                        '.gif' => 1,
                        '.gz' => 1,
                        '.htm' => 1,
                        '.html' => 1,
                        '.ico' => 1,
                        '.jpg' => 1,
                        '.jpe' => 1,
                        '.jpeg' => 1,
                        '.js' => 1,
                        '.mov' => 1,
                        '.mp3' => 1,
                        '.mp4' => 1,                        
                        '.mpeg' => 1,
                        '.mpg' => 1,
                        '.pdf' => 1,
                        '.png' => 1,
                        '.pot' => 1,
                        '.pps' => 1,
                        '.ppt' => 1,
                        '.qt' => 1,
                        '.ra' => 1,
                        '.ram' => 1,
                        '.rtf' => 1,
                        '.swf' => 1,
                        '.tar' => 1,
                        '.tgz' => 1,
                        '.tif' => 1,
                        '.tiff' => 1,
                        '.txt' => 1,
                        '.xls' => 1,
                        '.zip' => 1
                    );
	
	function upload(&$ld)
	{
		if(!$this->_validate_upload($ld))
		{
			return false;
		}
		
		global $_FILES;
		
		$base_dir='';
		$ld['path']=$ld['location'];
		if($ld['location']=='/')
		{
			$ld['location']=$_FILES['filename']['name'];
		}
		else 
		{
				$ld['location'].=$_FILES['filename']['name'];
		}
		switch ($ld['type'])
		{
			case 'image': 
					$base_dir='../../../../img_gallery/'.$ld['location'];
					
					break;
			case 'file':  
					$base_dir='../../../../upload/'.$ld['location'];					
					break;
		}
	    if(FALSE === move_uploaded_file($_FILES['filename']['tmp_name'],$base_dir))
	    {
	               $ld['error_upload'].="Unable to upload the file.  Move operation failed."."<!-- Check file permissions -->";
	                return false;
	    }
	    $ld['error_upload'] ='File uploaded succesfuly';
	    return true;
	}

	function create(&$ld)
	{
		if(!$this->_validate_create($ld))
		{
			return false;
		}
		$base_dir='';
		switch ($ld['type'])
		{
			case 'image': 
					$base_dir='../../../../img_gallery/';
					break;
			case 'file':  
					$base_dir='../../../../upload/';
					break;
		}
		if(is_dir($base_dir.$ld['location']))
		{
			$newfolder=$base_dir.$ld['location'].$ld['folder'];
			if(@mkdir($newfolder,0777))
			{
				$ld['error_create'] = 'Folder has been created';
				$ld['path']=$ld['location'].$ld['folder'].'/';
			}
			else 
			{
				$ld['error_create'] = 'An error occurred while trying to create folder:'.$ld['folder'];				
			}
		}
		return true;
	}
	
	function delete(&$ld)
	{
		if(!$this->_validate_delete($ld))
		{
			return false;
		}
		$base_dir='';
		$ld['path'] = $ld['location'];
		if($ld['location']=='/')
		{
			$ld['location']='';
		}
		
		switch ($ld['type'])
		{
			case 'image': 
					$base_dir='../../../../img_gallery/'.$ld['location'];
					
					break;
			case 'file':  
					$base_dir='../../../../upload/'.$ld['location'];					
					break;
		}
		if(file_exists($base_dir.$ld['file']))
		{
			unlink($base_dir.$ld['file']);
		}
		return true;
	}
	
	function _validate_delete(&$ld)
	{
		$is_ok=true;
		
		if(!$ld['type'])
		{
			$ld['error_upload'].='No filemanager found';
			$is_ok=false;
		}
		if(!$ld['location'])
		{
			$ld['error_upload'].='Location does not exist';
			$is_ok=false;
		}
		if(!$ld['file'])
		{
			$is_ok=false;
			$ld['error_upload'].='No file detected';			
		}
		$f_ext=substr($ld['file'],strrpos($ld['file'],"."));	    
	    if(!$this->filetypes[strtolower($f_ext)])
       	{
       		$ld['error_upload'].='Invalid filetype<br>';
       		return false;
       	}
		return $is_ok;
	}
	
	function _validate_upload(&$ld)
	{
		$is_ok=true;
		global $_FILES;

		$f_ext=substr($_FILES['filename']['name'],strrpos($_FILES['filename']['name'],"."));	    
	    if(!$this->filetypes[strtolower($f_ext)])
       	{
       		$ld['error_upload'].='Invalid filetype<br>';
       		return false;
       	}
       	if(!$ld['type'])
		{
			$is_ok=false;
			$ld['error_upload'].='No filemanager found';
			$ld['type']='image';
		}
		if(!$ld['location'])
		{
			$is_ok=false;
			$ld['error_upload'].='Location does not exist';			
		}		
       	return $is_ok;		   
	}
	
	function _validate_create(&$ld)
	{
		$is_ok=true;
		if(!$ld['type'])
		{
			$is_ok=false;
			$ld['error_create'].='No filemanager found';
			$ld['type']='image';
		}
		if(!$ld['location'])
		{
			$is_ok=false;
			$ld['error_create'].='Location does not exist';			
		}
		if(!$ld['folder'])
		{
			$is_ok=false;
			$ld['error_create'].='No folder name';			
		}
		return $is_ok;
	}
}//end class


        
 
?>