# Translation Dashboard

## Table of Contents
* [Overview](#overview)
* [Features](#features)
* [Architecture](#architecture)
* [Installation](#installation)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

## Overview
This project is a web-based translation dashboard tool designed to identify Wikidata items that have a page on mdwiki.org but are missing from another Wikipedia language. It is deployed at [mdwiki.toolforge.org](mdwiki.toolforge.org).

## Features

* Identifies Wikidata items missing translations.
* Provides a user interface to manage translations.
* Aggregates and displays ranking-related information (leaderboard).
* Supports user authentication and coordination.
* Includes testing and continuous integration.

## Architecture

This full-stack web application is built using PHP, HTML, CSS, and JavaScript.

**Frontend:**

* **Main Pages:**
    * `results` module (files in the `results` directory, e.g., `SPARQLDispatcher.php`, `fetch_cat_data.php`).
    * `missing` page (`missing.php`).
    * `leaderboard` module (contents of the `leaderboard` directory).
* **Shared UI Components:** (`head.php`, `header.php`, `footer.php`).
* **CSS:** (`css/Responsive_Table.css`, `css/dashboard_new1.css`, etc.)
* **JavaScript:** (`js/login.js`, `js/sorttable.js`, `js/theme.js`, etc.)

**Backend:**

* **Actions/API Integration:** (`actions/mdwiki_api.php`, `actions/td_api.php`, `actions/wiki_api.php`, etc.)
* **API/SQL Endpoints:** (`api_or_sql/data_tab.php`, `api_or_sql/get_lead.php`, `api_or_sql/index.php`).
* **Table Generation & Management:** (`Tables/include.php`, `Tables/sql_tables.php`, `Tables/tables.php`, `Tables/langcode.php`).
* **Controllers/Coordination & Authentication:** (`coordinator.php`, `auth.php`, `index.php`).
* **Translation Modules:** (`translate.php`, `translate/medwiki.php`, `translate_med/index.php`, `translate_med/db_insert.php`, `translate_med/medwiki.php`).

**External Integrations:**

* Wikidata/MDwiki APIs.
* SPARQL endpoints (in the `results` folder).

**Testing & Deployment:**

* Tests directory (`tests/index.php`, `tests/test_fetch_cat_data.php`, `tests/test_fetch_cat_data_sparql.php`).
* Continuous Integration / Deployment Configurations: `.coderabbit.yaml`, `.github/workflows` directory.

## Installation
## Usage
## Contributing
## License
## Contact
