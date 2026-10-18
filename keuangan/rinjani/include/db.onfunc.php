<?php

class Db
{
    private $conn = null;
    private $stmt = null;
    private $closed = false;
    private $autoClose = false;
    private $lastSql = "";

    function __destruct()
    {
        //print "Destroying " . __CLASS__ . "\n";

        if ($this->autoClose)
        {
            $this->Close();
        }
    }

    public function Open($autoClose = true)
    {
        global $db_host, $db_user, $db_pass, $db_name;

        $this->autoClose = $autoClose;

        $this->conn = mysqli_connect($db_host, $db_user, $db_pass);

        mysqli_select_db($this->conn, $db_name);

        return $this->conn;
    }

    public function TryOpenExit($autoClose = true)
    {
        try
        {
            global $db_host, $db_user, $db_pass, $db_name;

            $this->autoClose = $autoClose;

            $this->conn = mysqli_connect($db_host, $db_user, $db_pass);

            mysqli_select_db($this->conn, $db_name);

            return $this->conn;
        }
        catch (Exception $ex)
        {
            $msg = $ex->getMessage();

            echo "DATABASE CONNECTION ERROR: $msg /kwv37";

            exit();
        }
    }

    public function Close()
    {
        if ($this->conn == null)
            return;

        if ($this->closed)
            return;

        try
        {
            //print "Closing db connection";

            @mysqli_close($this->conn);
        }
        catch (Exception $ex)
        {
            // ignored
        }
        finally
        {
            $this->closed = true;
            $this->conn = null;
        }
    }

    public function InsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    public function ExecuteNonQuery($sql)
    {
        if ($this->conn == null)
            return;

        $this->lastSql = $sql;
        mysqli_query($this->conn, $sql);
    }

    public function QueryDb($sql)
    {
        $this->lastSql = $sql;
        return $this->ExecuteReader($sql);
    }

    public function QueryDbEx($sql)
    {
        $this->lastSql = $sql;
        return $this->ExecuteReader($sql);
    }

    public function FetchSingleRow($sql)
    {
        if ($this->conn == null)
            return null;

        $res = $this->ExecuteReader($sql);
        if (mysqli_num_rows($res) == 0)
            return null;

        return mysqli_fetch_row($res);
    }

    public function FetchSingleArray($sql)
    {
        if ($this->conn == null)
            return null;

        $res = $this->ExecuteReader($sql);
        if (mysqli_num_rows($res) == 0)
            return null;

        return mysqli_fetch_array($res);
    }


    public function ExecuteReader($sql)
    {
        if ($this->conn == null)
            return null;

        $this->lastSql = $sql;
        return mysqli_query($this->conn, $sql);
    }

    public function ExecuteScalar($sql, $defaultValue)
    {
        if ($this->conn == null)
            return null;

        $this->lastSql = $sql;
        $result = mysqli_query($this->conn, $sql);
        if (mysqli_num_rows($result) == 0)
            return $defaultValue;

        $row = mysqli_fetch_row($result);
        return $row[0];
    }

    public function FetchSingle($sql, $defaultValue)
    {
        return $this->ExecuteScalar($sql, $defaultValue);
    }

    public function PrepareStatement($sql)
    {
        $this->lastSql = $sql;
        return mysqli_prepare($this->conn, $sql);
    }

    public function ExecuteStatement()
    {
        if ($this->stmt == null)
            return;

        mysqli_stmt_execute($this->stmt);
    }

    public function BeginTrans()
    {
        if ($this->conn == null)
            return;

        mysqli_query($this->conn, "SET AUTOCOMMIT=0");
        mysqli_query($this->conn, "BEGIN");
    }

    public function CommitTrans()
    {
        if ($this->conn == null)
            return;

        mysqli_query($this->conn,"COMMIT");
        mysqli_query($this->conn,"SET AUTOCOMMIT=1");
    }

    public function RollbackTrans()
    {
        if ($this->conn == null)
            return;

        mysqli_query($this->conn,"ROLLBACK");
        mysqli_query($this->conn,"SET AUTOCOMMIT=1");
    }

    public function LastError()
    {
        if ($this->conn == null)
            return [0, ""];

        if (mysqli_errno($this->conn) == 0)
            return [0, ""];

        return [ mysqli_errno($this->conn), mysqli_error($this->conn) ];
    }

    public function LogLastErrorIfExist()
    {
        if ($this->conn == null)
            return;

        if (mysqli_errno($this->conn) == 0)
            return;

        $sql = $this->lastSql;
        $errno = mysqli_errno($this->conn);
        $error = mysqli_error($this->conn);

        $logPath = @realpath(@dirname(__FILE__)) . "/../../../log";
        $logExists = @file_exists($logPath) && @is_dir($logPath);
        if (!$logExists)
            @mkdir($logPath, 0755);

        $logFile = @realpath(@dirname(__FILE__)) . "/../../../log/keuangan-error.log";
        $modeFile = (@file_exists($logFile) && @filesize($logFile) > 1024 * 1024) ? "w" : "a";

        $fp = @fopen($logFile, $modeFile);
        @fwrite($fp, "-- Query Error on " . date('d-M-Y H:i:s') . " --------\r\n");
        @fwrite($fp, " SCRIPT > " . $_SERVER['SCRIPT_NAME'] . "\r\n");
        @fwrite($fp, " QUERY  > $sql\r\n");
        @fwrite($fp, " ERRNO  > $errno\r\n");
        @fwrite($fp, " ERROR  > $error\r\n");
        @fwrite($fp, "\r\n");
        @fclose($fp);
    }
}
?>
