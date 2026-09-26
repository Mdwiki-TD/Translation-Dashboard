<?php

namespace App\MdwikiSql;

use App\MdwikiSql\Database;

function execute_query(string $sqlQuery, $params = null)
{
    // Create a new database object
    $db = new Database('DB_NAME');

    // Execute a SQL query
    if ($params) {
        $results = $db->executequery($sqlQuery, $params);
    } else {
        $results = $db->executequery($sqlQuery);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    return $results;
};
function fetch_query(string $sqlQuery, $params = null, $noprint = false)
{
    // Create a new database object
    $db = new Database('DB_NAME');

    if ($noprint == false) {
        $db->test_print($sqlQuery);
    }

    // Execute a SQL query
    if ($params) {
        $results = $db->fetchquery($sqlQuery, $params);
    } else {
        $results = $db->fetchquery($sqlQuery, null);
    }

    // Print the results
    // foreach ($results as $row) echo $row['column1'] . " " . $row['column2'] . "<br>";

    // Destroy the database object
    $db = null;

    return $results;
};
