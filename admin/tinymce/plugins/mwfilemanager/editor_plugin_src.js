// Setup file_browser_callback option
function TinyMCE_mwwfilemanager_initInstance(inst) {
	inst.settings['file_browser_callback'] = 'mwFileBrowser.browserCallBack';
};

function TinyMCE_mwwfilemanager_getInfo() {
	return {
		longname : 'File Browser',
		author : 'Bodi Zsolt @ Medeeaweb',
		infourl : '',
		version : "1.0b"
	};
};
function TinyMCE_mwwfilemanager_getTinyMCEBaseURL() {
	var nl, i, src;

	if (!tinyMCE.baseURL) {
		nl = document.getElementsByTagName('script');
		for (i=0; i<nl.length; i++) {
			src = "" + nl[i].src;

			if (/(tiny_mce\.js|tiny_mce_dev\.js|tiny_mce_gzip)/.test(src))
				return src = src.substring(0, src.lastIndexOf('/'));
		}
	}

	return tinyMCE.baseURL;
};

// Load mwwfilemanager.js script
if (typeof(mwwFileManager) == "undefined")
	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + TinyMCE_mwwfilemanager_getTinyMCEBaseURL() + '/plugins/mwfilemanager/jscripts/mwfilemanager.js"></script>');