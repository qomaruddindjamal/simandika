<?php
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * @version: 33.0 (Jan 05, 2026)
 * @notes:
 *
 * Copyright (C) 2024 JIBAS (http://www.jibas.net)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 **[N]**/ ?>
<?php
class Logger
{
    private $logFile = "";
    private $file;

    public function __construct()
    {
        $logPath = realpath(dirname(__FILE__)) . "/../log";
        if (!file_exists($logPath))
            mkdir($logPath);

        $this->logFile = realpath(dirname(__FILE__)) . "/../log/debugger.log";

        $this->file = fopen($this->logFile, "a");
        fwrite($this->file, "-------------------------------------------\r\n");
    }

    public function ClearLog()
    {
        ftruncate($this->file, 0);
    }

    public function Log($message)
    {
        fwrite($this->file, date('H:i:s') . " " . $message . "\r\n");
    }

    public function LogError($message)
    {
        fwrite($this->file, date('H:i:s') . " ERROR: " . $message . "\r\n");
    }

    public function LogNoTime($message)
    {
        fwrite($this->file, $message . "\r\n");
    }

    public function Close()
    {
        fclose($this->file);
    }

    public static function LogOnce($message)
    {
        $log = new Logger();
        $log->Log($message);
        $log->Close();
    }

    public static function LogPageRequestOnce()
    {
        $log = new Logger();

        $message = "";
        foreach($_REQUEST as $key => $value)
        {
            $message .= "$key = $value\n";
        }

        foreach($_FILES as $key => $file)
        {
            $message .= "$key = " . $file["name"] . "\n";
        }

        $log->Log($message);
        $log->Close();
    }

    public static function LogErrorOnce($exception, $code)
    {
        $log = new Logger();
        $log->Log($exception->getMessage() . " /$code");

        $stackTrace = $exception->getTrace();
        foreach ($stackTrace as $trace)

        {
            if (isset($trace['file']) && isset($trace['line']) && isset($trace['function']))
            {
                $st = "In " . $trace['file'] . " on line " . $trace['line'] . ", calling function: " . $trace['function'];
                $log->LogNoTime($st);
            }
        }

        $log->Close();
    }

    public static function LogDataOnce($message, $data, $exception)
    {
        $log = new Logger();
        $log->Log("---------------------");
        $log->LogNoTime($message);
        $log->LogNoTime($data);
        $log->LogNoTime($exception->getMessage());
        $log->Close();
    }
}
?>