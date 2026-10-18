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
function IsColumnExist($db, $dbName, $tableName, $columnName)
{
    try
    {
        $sql = "SELECT COUNT(*) 
                  FROM information_schema.COLUMNS 
                 WHERE TABLE_SCHEMA = '$dbName' 
                   AND TABLE_NAME = '$tableName'
                   AND COLUMN_NAME = '$columnName'";
        $nData = $db->FetchSingle($sql, 0);
        //Peek::Show($sql, $nData);
        return $nData > 0;
    }
    catch (Exception $ex)
    {
        //Peek::Show($ex->getMessage());
        return false;
    }
}

function IsTableExist($db, $dbName, $tableName)
{
    try
    {
        $sql = "SELECT COUNT(*) 
                  FROM information_schema.TABLES 
                 WHERE TABLE_SCHEMA = '$dbName' 
                   AND TABLE_NAME = '$tableName'";
        $nData = $db->FetchSingle($sql, 0);
        //Peek::Show($sql, $nData);
        return $nData > 0;
    }
    catch (Exception $ex)
    {
        //Peek::Show($ex->getMessage());
        return false;
    }
}

function IsTriggerExist($db, $dbName, $tableName, $triggerName)
{
    try
    {
        $sql = "SELECT COUNT(trigger_name)
                  FROM information_schema.triggers
                 WHERE trigger_schema = '$dbName'
                   AND trigger_name = '$triggerName'
                   AND event_object_table = '$tableName'";
        $nData = $db->FetchSingle($sql, 0);
        //Peek::Show($sql, $nData);
        return $nData > 0;
    }
    catch (Exception $ex)
    {
        //Peek::Show($ex->getMessage());
        return false;
    }
}

function IsIndexExist($db, $dbName, $tableName, $indexName)
{
    try
    {
        $sql = "SELECT COUNT(index_name)
                  FROM information_schema.statistics
                 WHERE table_schema = '$dbName'
                   AND table_name = '$tableName'
                   AND index_name = '$indexName'";
        $nData = $db->FetchSingle($sql, 0);
        //Peek::Show($sql, $nData);
        return $nData > 0;
    }
    catch (Exception $ex)
    {
        //Peek::Show($ex->getMessage());
        return false;
    }
}

function ExecIgnore($db, $sql)
{
    try
    {
        $res = $db->QueryDb($sql);
        //Peek::Show("DONE", $sql);
        return $res;
    }
    catch (Exception $ex)
    {
        //Peek::Show("ERROR", $sql);
        //Peek::Show($ex->getMessage());
        return null;
    }
}
?>
