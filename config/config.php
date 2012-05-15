<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/
$conf_data=array(


'MYSQL_DB_HOST'                =>                        "openserver",       //The host name of your database. But usualy is simply localhost
'MYSQL_DB_USER'                =>                        "mysql",            //The User for your database
'MYSQL_DB_PASS'                =>                        "mysql",                //Password of database's User
'MYSQL_DB_NAME'                =>                        "rehab"  //Database Name

);

//$site_url="http://70.85.226.185/~rehabmyp/"; //The URL to the public script. (www.your_domain.com/script_folder/)
$site_url="http://www.rehabmypatient.com"; //The URL to the public script. (www.your_domain.com/script_folder/)
$site_name="Rehab My Patient"; //The URL to the public script. (www.your_domain.com/script_folder/)

//Meta Settings - changable from admin panel
$meta_title="Rehab My Patient";
$meta_keywords="Rehab My Patient";
$meta_description="Rehab My Patient";

// Is mod_rewrite available for search engine friendly URL?
// 1-yes, 0-no
$rewrite_url=0;

// Witch HTML editor to use?
// 1-TinyMCE, 2-KTML
$htmlEditor=1;
	

//debug mode 1 enable,0 disabled
$debug=0;

//----------------------------------------------
foreach($conf_data as $c_key => $c_val)
        {
                define($c_key,$c_val);
        }
        
$is_live=1; 
        
//define('API_USERNAME', 'tibi_1305030086_biz_api1.medeeaweb.com');
//define('API_PASSWORD', '1305030103');
//define('API_SIGNATURE', 'AEvJTLkAETZrJl7.q.Vxym.DXhMGAXGqrN0MIWrjSgjm-qrAN1LdctDt');
//define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');
//define('VERSION', '51.0');

define('AFFILIATES_API_M_USERNAME', 'dbeach@daxic.com');
define('AFFILIATES_API_M_PASSWORD', '123456');
define('AFFILIATES_API_M_URL', 'http://rehabmypatient.com/affiliate/scripts/server.php'); // live url

?>
