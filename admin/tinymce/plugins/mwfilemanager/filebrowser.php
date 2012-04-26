<?php 
$ft = new ft('templates');
$ft->define(array('main'=>'filebrowser.html'));
$ft->define_dynamic('folders_row','main');
$ft->define_dynamic('files_row','main');
if($glob['path']=='../')//stop us from going up into www
{
	$glob['path']='';
}
$dir = '../../../../upload/'.$glob['path'];
$dirinfo=pathinfo($dir);
if($dirinfo['basename']=='.')
{
	$dir=$dirinfo['dirname'].'/';
	$glob['path']='';
}
else 
{
	$glob['path'].'/';
}
$base=$site_url.'upload/'.$glob['path'];
$folder_count=0;
$file_count=0;
if(is_dir($dir))
{
	if ($dh = opendir($dir))
	{
	   while (($file = readdir($dh)) !== false)
	   {
	  	 switch (filetype($dir . $file))
	  	 {
	  	 	case 'dir':
	  	 				if($file == '..') continue;
	  	 				if($file=='.')
	  	 				{
	  	 					if($glob['path'])	
	  	 					{
	  	 						$p=explode('/',$glob['path']);
	  	 						if(is_array($p))
	  	 						{
	  	 							array_pop($p);
		  							array_pop($p);		  	 							
	  								$p=implode('/',$p);
	  	 						}
	  	 						$ft->assign('FOLDER',$file.'.');
	  	 						$ft->assign('PATH',$p.'/');
	  	 					}
	  	 					else 
	  	 					{	  	 					
								continue;  	 						
	  	 					}
	  	 				}
	  	 				else
	  	 				{
	  	 					$ft->assign('FOLDER',$file);	  	 					  	 			
	  	 					$ft->assign('PATH',$glob['path'].$file.'/');	
	  	 				}
		  	 			
		  	 			
	  	 			$ft->parse('folders_row_OUT','.folders_row');
	  	 			$folder_count++;
	  	 			break;
	  	 	case 'file':
	  	 			$ft->assign(array(
	  	 								'IMG' => $base.$file,
	  	 								'IMG_ID' => $file_count,
	  	 								'DIR' => $glob['path']=='' ? '/' : $glob['path'],
	  	 								'FILE' => $file
	  	 			));
	  	 				$ft->assign('CAPTION',$file);	  	 			
	  	 			$file_count++;
	  	 			$ft->parse('files_row_OUT','.files_row');
	  	 			break;			
	  	 }
	   }
	   closedir($dh);
	}
}
$ft->assign('ERROR_UPLOAD',$glob['error_upload']);
$ft->assign('ERROR_CREATE',$glob['error_create']);

$ft->assign('LOC',$glob['path']=='' ? '/' : $glob['path']);
$ft->parse('content','main');
$ft->clear_dynamic('content','folders_row');
$ft->clear_dynamic('content','files_row');
$ft->ft_print('content');
?>