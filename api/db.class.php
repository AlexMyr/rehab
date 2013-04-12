<?php
class DB {
    /**
     * DB object handler
     */
    private static $initialized=NULL;

    private $con=false;

    function __construct() {
        $this->con = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_errno()." : ".mysql_error());
        mysql_select_db(DB_NAME, $this->con) or die(mysql_errno()." : ".mysql_error());
        mysql_query('SET @@collation_connection = @@collation_database');
    }

    function insertID() {
        return mysql_insert_id($this->con);
    }

    function con() {
        if (is_null(self::$initialized)) {
            self::$initialized = new DB;
        }
        return self::$initialized;
    }

    function query($sql) {
        $result = mysql_query($sql, $this->con) or die(mysql_errno()." : ".mysql_error());

        return $result;
    }

    function count($sql) {
        $res = $this->query($sql);
        return mysql_num_rows($res);
    }

    function getResults($sql) {
        $res = $this->query($sql);
        $result = array();

        if ($res)
            while($row = mysql_fetch_assoc($res))
                $result[] = $row;

        return $result;
    }

    function getRow($sql) {
        $res = $this->query($sql);
        $result = array();

        if($res)
            $result = mysql_fetch_assoc($res);

        return $result;
    }

    function getVar($sql) {
        $row = $this->getRow($sql);
        $result = is_array($row) ? array_shift($row) : null;

        return $result;
    }

    function getCol($sql) {
        $res = $this->getResults($sql);
        $result = array();

        if($res)
            foreach($res as $row)
                $result[] = array_shift($row);

        return $result;
    }
}
?>