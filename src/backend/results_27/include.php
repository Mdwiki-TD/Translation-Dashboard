<?php

// Data layer
include_once __DIR__ . '/Data/ResultsFetcher.php';

// Helpers
include_once __DIR__ . '/Helpers/CardRenderer.php';
include_once __DIR__ . '/Helpers/TranslateTypeLoader.php';

// Rows
include_once __DIR__ . '/Rows/MissingRowBuilder.php';
include_once __DIR__ . '/Rows/ExistsRowBuilder.php';
include_once __DIR__ . '/Rows/InProcessRowBuilder.php';

// Tables
include_once __DIR__ . '/Tables/AbstractResultsTable.php';
include_once __DIR__ . '/Tables/MissingTable.php';
include_once __DIR__ . '/Tables/ExistsTable.php';
include_once __DIR__ . '/Tables/InProcessTable.php';

// Main loader
include_once __DIR__ . '/ResultsLoader.php';
include_once __DIR__ . '/get_results_2026.php';
include_once __DIR__ . '/index.php';
