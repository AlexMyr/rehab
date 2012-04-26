// JavaScript Document
var fileManager=window.opener.mwwFileManager;
var selected=null;

function $(id){
	if(typeof(id)=='string'){
		return document.getElementById(id);
	}	
}

function selectFile(id,url){	
	if(selected==null){
		selected={
						'id' : id,
						'url' : url
			};
	$(id).style.backgroundColor='#CCCCCC';
	}else{
		$(selected.id).style.backgroundColor='#ffffff';		
		selected={
						'id' : id,
						'url' : url
			};
	$(id).style.backgroundColor='#CCCCCC';
	}
}

function delFile(){
	return confirm('Are you sure you want to delete this file?');	
}



function selectImg(id,url){	
	if(selected==null){
		selected={
						'id' : id,
						'url' : url
			};
	$(id).style.borderColor='#FF0000';
	}else{
		$(selected.id).style.borderColor='#000000';		
		selected={
						'id' : id,
						'url' : url
			};
	$(id).style.borderColor='#FF0000';
	}
}

function cancelAction(){
	fileManager.openWindow.tinyMCE.windowArgs.editor_id=fileManager.prevIinstance;
	tinyMCEPopup.close();
}

function insertAction(){
	if(selected==null){
		cancelAction();
		return false;
	}
	fileManager.updateField(selected.url);
	tinyMCEPopup.close();	
}
function show(path){
	w=window.open(path,'se','scrollbars=yes,width=400,height=400,resizable=yes');
    w.focus();
}
