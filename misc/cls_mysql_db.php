<?php
/**
 * @author MedeeaWeb Works
 * @copyright MedeeaWeb Works
 * @package mysql_db
 * @version 2.0
 */

class mysql_db{

	/**
	 * hold the host where the MySql server is located
	 * @var string
	 * @access private
	 */
	var $host = MYSQL_DB_HOST;
	/**
	 * store de user name required for connection
	 * @access private
	 * @var string
	 */
	var $user = MYSQL_DB_USER;
	/**
	 * password required by the username
	 * @access private
	 * @var string
	 */
	var $pass = MYSQL_DB_PASS;
	/**
	 * database to connect
	 * @access private
	 * @var unknown_type
	 */
	var $database = MYSQL_DB_NAME;
	/**
	 * store the conection handle
	 * @access private
	 * @var resource
	 */
	var $conHwnd = 0;
	/**
	 * store the last query handle
	 * @access private
	 * @var resource
	 */
	var $qHwnd = 0;
	/**
	 * store a record readed from database
	 * @access private
	 * @var array
	 */
	var $record = null;
	/**
	 * store the last row from record
	 * @access private
	 * @var integer
	 */
	var $row = 0;
	/**
	 * we know if we are in a tranzaction
	 * @access private
	 * @var bool
	 */
    var $isTrans = false;
	/**
	 * Link to debugging class
	 *
	 * @var unknown_type
	 */
    var $debug = null;

    /**
	   * constructor of the class
	   *
	   * @example $dbu = new mysql_db(host=sheep, port=5432, dbname=mary, user=lamb, password=baaaa);
	   * @param string $strHost
	   * @param string $strUser
	   * @param string $strPass
	   * @param string $strDatabase
	   * @access public
	   */
	function mysql_db($strHost = "",$strUser = "",$strPass = "",$strDatabase = "") {
	      if(($strHost != '') && ($strUser != '') && ($strDatabase != '')){
	            $this->host=$strHost;
	            $this->user=$strUser;
	            $this->pass=$strPass;
	            $this->database=$strDatabase;
	      }
	      if(!function_exists('get_debug_instance')){
	      		include_once('cls_mysql_debug.php');
	      }
	      $this->debug =& get_debug_instance();
	     $this->_con_open();
	  }

	/**
	 * Open a connection to Sql server.
	 *
	 * @access private
	 * @return bool
	 */
	function _con_open(){
		if(is_resource($this->conHwnd)){
			if(!mysql_select_db($this->database,$this->conHwnd))
			{
				$this->raise_error("db::conOpen()", "Failed to select given database:".$this->database." ! ".mysql_error());
			}
			return true;
		}

		$this->conHwnd = mysql_connect($this->host,$this->user,$this->pass) ;
		if(!is_resource($this->conHwnd))
		{
			$this->raise_error("db::conOpen()", "Failed to connect to MySql server ! ".mysql_error());
		}

		if(!mysql_select_db($this->database,$this->conHwnd))
		{
			$this->raise_error("db::conOpen()", "Failed to select given database:".$this->database." ! ".mysql_error());
		}
		return true;
	}

	/**
	 * Used to close the active Connection
	 * Must return true if the connection is openned.Else will return false.
	 * This will occur only when the connection with server is lost from some
	 * reason.
	 *
	 * @access public
	 * @return bool
	 */
	function _con_close(){
		if(is_resource($this->conHwnd)){
			return mysql_close($this->conHwnd);
		}
	}

	/**
	 * The query() function returns a database result object when "read" type queries are run,
	 * which you can use to show your results. When "write" type queries are run it simply returns
	 * TRUE or FALSE depending on success or failure.
	 *
	 * @param string $strQuery
	 * @param mixed $binds string or array of values to bind to this query
	 * @param bool $return_object
	 * @return mixed
	 */
	function query($strQuery = '',$binds = false, $return_object = true){
		if($strQuery == ''){
			$this->raise_error("db::query()","Query can't be an empty string !");
		}

		if(!is_resource($this->conHwnd)){
			$this->con_open();
		}
		if($binds !== false){
			if (!is_array($binds)){
				$binds = array($binds);
			}

			foreach ($binds as $val){
				$val = "'".$val."'";

				$val = str_replace('?', '{%bind_marker%}', $val);
				$strQuery = preg_replace("#".preg_quote('?', '#')."#", str_replace('$', '\$', $val), $strQuery, 1);
			}
		}
		$this->qHwnd = mysql_db_query($this->database, $strQuery, $this->conHwnd);

		if(!$this->qHwnd){
			$this->raise_error("db::query()","Failed to run Query:$strQuery ".mysql_error());
		}
		$this->debug->save($strQuery);
		$this->row = 0;
		if($this->is_write_type($strQuery) === true){
			return $this->qHwnd;
		}
		if($return_object !== true){
			return $this->qHwnd;
		}
		if(!class_exists('mysql_record')){
			require_once('cls_mysql_record.php');
		}
		
		$RES 			= new mysql_record();
		$RES->conn_id	= $this->conHwnd;
		$RES->result_id	= $this->qHwnd;
		$this->record = $RES;
		return $RES;
	}

	/**
	 * Returns the insert ID number when performing database inserts.
	 * @access public
	 * @param string $strQuery
	 * @return mixed
	 */
	function query_get_id($strQuery = ''){
		$this->query($strQuery,false,false);
		return @mysql_insert_id();
	}

	/**
	 * Returns a single result row from recordset
	 * Alias for mysql_record->row();
	 *
	 * @access public
	 * @param string $strQuery
	 * @return array
	 */
	function row($strQuery = ''){
		$result = $this->query($strQuery,false,true);
		if(is_object($result)){
			return $result->row();
		}
		return array();
	}

	/**
	 * Returns the first field from the recordset or an empty string
	 * Alias for mysql_record->first();
	 *
	 * @access public
	 * @param string $strQuery
	 * @return mixed
	 */
	function field($strQuery = ''){
		$result = $this->query($strQuery);
		if(is_object($result)){
				return current($result->row());
		}
		return '';
	}

	/**
	 * Creates and executes a correctly formated SQL delete string
	 *
	 * @param string $table
	 * @param string $where
	 * @return bool
	 */
	function deletefrom($table = '' ,$where = ''){
		if ($where == '')
			return false;
		return $this->query("DELETE FROM ".$table." WHERE ".$where);
	}

	/**
	 * move the pointer to the next row in the query result
	 * @access public
	 * @return bool false if the end is reached ,else return the current position
	 * @deprecated  use mysql_record->next() or mysql_next->next_array() instead
	 */
	function move_next(){
		return $this->record->next();
	}

	/**
	 * offsets record pointer
	 *
	 * @param integer $row_number
	 * @return bool
	 * @deprecated use mysql_record->move_to() instead
	 */
	function move_to($row_number=0){
		return $this->record->move_to($row_number);
	}

	/**
	 * Get field from results
	 *
	 * @param string $mixFld
	 * @return mixed
	 * @access public
	 * @deprecated use mysq_record->get_field() instead
	 */
	function get_field_raw($mixFld){
		return ($this->record->get_field($mixFld));
	}

 	/**
 	 * get value of the specified field
 	 * @access public
 	 * @param string $mixFld
 	 * @return mixed
 	 * @deprecated use mysq_record->get_field() instead
 	 */
	function get_field($mixFld){
		return ($this->record->get_field($mixFld));
	}

	/**
	 * Alias for get_field
	 *
	 * @param string $mixFld
	 * @return mixed
	 * @access public
	 * @deprecated  use mysql_record->f() instead;
	 */
	function f($mixFld){
		if(is_object($this->record))
		{
			return $this->record->get_field($mixFld);
		}
		return '';
	}

	/**
	 * Print in Browser the value of the field name from the $glob variable
     * if it is set, otherwise returns the value of the current
     * record in the RecordSet.
	 *
	 * @param string $mixFld
	 * @see gf();
	 */
	function pf($mixFld){
		echo $this->record->gf($mixFld);
	}

	/**
	 * Return the value of the field name from the $glob variable
	 * if it is set, otherwise returns the value of the current
	 * record in the RecordSet.
	 *
	 * @param mixed $mixFld
	 * @return mixed
	 * @access public
	 * @deprecated use mysql_record->gf() instead;
	 */
	function gf($mixFld){
		if(is_object($this->record))
		{
			return $this->record->gf($mixFld);
		}
		return '';
	}

	/**
	 * the number of records from the last query
	 *
	 * @access public
	 * @return int
	 * @deprecated use mysql_record->records_count() instead;
	 */
	function records_count(){
		return $this->record->records_count();
	}

    /**
     * the number of fields from the last query
     *
     * @access public
     * @return int
     * @deprecated use mysql_record->fields_count instead;
     */
	function fields_count(){
		return $this->record->fields_count();
	}

  	/**
  	 * Close and free last query result
  	 * @access public
  	 */
	function query_close(){
		@mysql_free_result($this->qHwnd);
	}

   	/**
	 * Determines if a query is a "write" type.
	 *
	 * @access	private
	 * @param	string	An SQL query string
	 * @return	boolean
	 */
	function is_write_type($sql)
	{
		if ( ! preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql))
		{
			return false;
		}
		return true;
	}


  //Warning!!
  //Use Transaction only with tables of type DBD or InnoDB.
  /*
    Public function beginTrans()
     Start a new tranzaction
  */
  function begin_trans()
    {
      mysql_db_query($this->database,"set autocommit=0");
      if(!mysql_db_query($this->database,'BEGIN WORK;'))
         {
           $this->raise_error('db::beginTrans()','Failed to start transaction.!'.mysql_error());

         }
      $this->isTrans=true;
    }

  /*
    Public function commitTrans()
    Commit all changes in the current transaction
  */
  function commit_trans()
    {
     if($this->isTrans)
       {
         if(!mysql_db_query($this->database,'COMMIT'))
           {
            $this->raise_error('db::commitTrans()','Failed to commit transaction. !--- CHANGES WAS NOT SAVED ---!'.mysql_error());
           }

         $this->isTrans=false;
        mysql_db_query($this->database,"SET AUTOCOMMIT=1");
       }
    }

  /*
    Public function roolBackTrans()
    RoolBack all changes in the current transaction
  */
  function rooll_back_trans()
    {
      if($this->isTrans)
         {
          //no error check here because the changes in this
          //transaction are lost anyway if the connection or roolback has filed

          mysql_db_query($this->database,'ROLLBACK');
          $this->isTrans=false;
          mysql_db_query($this->database,"SET AUTOCOMMIT=1");
         }
    }

  /* Private Function
     Called wen need to display an error message inside of class
  */
  function raise_error($f,$errMsg)
    {
      if ($this->isTrans)
        {
         mysql_db_query($this->database,"ROLLBACK");
         mysql_db_query($this->database,"set AUTOCOMMIT=1");
        }
	  $this->log_error(debug_backtrace());
      //echo "<center><table border=0 cellpadding=2 cellspacing=2>";
      //echo "<tr><td><font color=\"red\" face=\"Times New Roman\"> ";
      //echo "Error:".$f." failed.ERROR MESSAGE IS: ".$errMsg;
      //echo "</font></td></tr></center>";
      //
      //die("Process Halted!");
    }
	
	function log_error($debug)
	{
	  $root_path = dirname(dirname(__FILE__));
	  //require_once ($root_path.'/config/config.php');
	  require_once ($root_path.'/classes/class.phpmailer.php');        
	  include_once ($root_path."/classes/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
	  
	  $file_name = dirname(dirname(__FILE__))."\error_log_mysql";
	  $error_report = "";
	  $error_report_old = file_get_contents($file_name);
	  foreach($debug as $debug_element)
	  {
			$cur_date = date("m/d/Y H:i:s");
			if($debug_element['args'])
				  $error_report .= "(time: $cur_date)=> file: '".$debug_element['file']."', line: '".$debug_element['line']."', function: '".$debug_element['function']."', args: '".str_replace(array('\r', '\t', '\n'), '', json_encode($debug_element['args']))."';<br/>";
	  }
	  
	  $mail = new PHPMailer();
	  $mail->Mailer = 'sendmail';
	  $mail->IsHTML(true);
	  $mail->IsSMTP(); // telling the class to use SMTP
	  $mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
	  $mail->SMTPAuth = true; // enable SMTP authentication
	  $mail->Host = SMTP_HOST; // sets the SMTP server
	  $mail->Port = SMTP_PORT; // set the SMTP port for the GMAIL server
	  $mail->Username = SMTP_USERNAME; // SMTP account username
	  $mail->Password = SMTP_PASSWORD; // SMTP account password
	  $mail->SetFrom('support@rehabmypatient.com', 'support@rehabmypatient.com');
	  $mail->Subject = 'Mysql Error!';
	  $mail->Body = $error_report;
	  $mail->AddAddress('ole_gi@rehabmypatient.com', 'ole_gi@rehabmypatient.com');
	  //$mail->AddAddress('support@rehabmypatient.com', 'support@rehabmypatient.com');
	  $mail->Send();	
	  
	  $fp = fopen($file_name, "r+");
	  $fp = fopen(dirname(dirname(__FILE__))."\error_log_mysql", "r+");
	  fwrite($fp, $error_report.$error_report_old);
	  fclose($fp);
	  
	}
	
 }//end class
 ?>