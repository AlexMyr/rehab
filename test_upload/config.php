<?php
set_time_limit(0);
//define("DB_SERVER", "localhost");
//define("DB_NAME", "rehabmyp_site");
//define("DB_USER", "rehabmyp");
//define("DB_PASSWORD", "34trfewy");
define("DB_SERVER", "openserver");
define("DB_NAME", "rehab");
define("DB_USER", "mysql");
define("DB_PASSWORD", "mysql");
define("DIRNAME", dirname(dirname(dirname(__FILE__)))."/uploads_new");
//define("UPLOADS_DIR", dirname(dirname(__FILE__))."/upload/test_upload");
define("UPLOADS_DIR", dirname(dirname(__FILE__))."/upload");
define("PROGRAMS_TABLE", "programs");
define("CATEGORIES_TABLE", "programs_in_category");
?>