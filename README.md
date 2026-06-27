[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/Mdwiki-TD/Translation-Dashboard)

# WikiProjectMed Translation Dashboard

The **WikiProjectMed Translation Dashboard** is a web-based platform designed to facilitate the translation of medical articles from [mdwiki.org](https://mdwiki.org) into various Wikipedia languages. It streamlines the identification of untranslated articles, integrates with MediaWiki's Content Translation tool, and tracks translation progress through a leaderboard system.

---

## 📋 Table of Contents

-   [Features](#features)
-   [System Architecture](#system-architecture)
-   [Installation and Configuration](#installation-and-configuration)
-   [Core Components](#core-components)
-   [Translation Workflow](#translation-workflow)
-   [Data Layer](#data-layer)
-   [Leaderboard System](#leaderboard-system)
-   [Coordinator Tools](#coordinator-tools)
-   [Diagram](#diagram)

---

## Features

-   **Article Identification**: Detects medical articles present on mdwiki.org but missing in target languages.
-   **Translation Facilitation**: Integrates with MediaWiki's Content Translation tool for seamless translation.
-   **Progress Tracking**: Monitors translation progress and contributions via a dynamic leaderboard.
-   **Coordinator Tools**: Provides tools for translation coordinators to manage and oversee translation activities.

---

## System Architecture

The dashboard employs a modular architecture, ensuring a clear separation between presentation, business logic, and data access layers. Built primarily using PHP, it interacts with MediaWiki APIs, SPARQL queries, and a custom database to perform its functions.

**Key Components**:

-   **Header System**: Manages navigation, authentication, and styling across all pages.
-   **Translation System**: Handles translation requests and redirects to the MediaWiki Content Translation tool.
-   **Results System**: Displays articles needing translation based on user-selected parameters.
-   **Leaderboard System**: Tracks and displays translation statistics by user, language, and campaign.

---

## Installation and Configuration

### Prerequisites

-   PHP 7.4 or higher
-   MySQL/MariaDB 10.3 or higher
-   Apache/Nginx web server
-   Composer for dependency management
-   Git for version control

### Installation Steps

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/WikiProjectMed/Translation-Dashboard.git
    cd Translation-Dashboard
    ```

2. **Install Dependencies**:

    ```bash
    composer install
    ```

3. **Database Setup**:

    - Create a new MySQL database.
    - Import the schema from `td.sql`.
    - Configure database connection parameters in the configuration files.

4. **Configure Settings**:

    - Adjust settings in the database tables, such as `settings`, `translate_type`, and `categories`.

5. **Authentication Setup**:

    - Ensure the [`/auth/`](https://github.com/Mdwiki-TD/auth-repo) directory exists with the necessary files.
    - Set up user credentials in the authentication database.

---

## Core Components

### Entry Points

-   **Main Interface (`index.php`)**: Allows users to select languages and categories for translation.
-   **Missing Articles (`missing.php`)**: Displays articles missing in different languages.
-   **Translation Redirect (`translate.php`)**: Redirects translation requests to the translation system.
-   **Leaderboard (`leaderboard.php`)**: Shows statistics about translations and translators.

### Header System

Implemented in `header.php`, this component:

-   Manages user authentication status.
-   Generates navigation menus.
-   Controls access to coordinator tools.
-   Handles theme selection and session initialization.

### Styling

The dashboard uses Bootstrap 5 combined with custom CSS for responsive and accessible design.

---

## Translation Workflow

1. **Initiation**:

    - Users select an article to translate from the Results System.
    - A request is sent to `translate.php` with parameters like article title, target language, category, campaign, and translation type.

2. **Processing**:

    - `translate.php` redirects the request to `translate_med/index.php`.
    - The system checks user authentication, validates parameters, and records the translation attempt in the database.

3. **Redirection**:

    - Users are redirected to the MediaWiki Content Translation interface to begin translating.

---

## Data Layer

The dashboard's data layer supports retrieving data from both the database and MediaWiki APIs, ensuring flexibility and continued operation even if one source is unavailable.

**Key Tables**:

-   **`settings`**: Stores core system settings.
-   **`translate_type`**: Defines translation types.
-   **`categories`**: Maps categories and campaigns.
-   **`views`**: Tracks view statistics by language.

---

## Leaderboard System

The leaderboard tracks and displays translation statistics, offering insights into:

-   **User Contributions**: Number of translations completed by each user.
-   **Language Statistics**: Translations per language.
-   **Campaign Progress**: Translations completed within specific campaigns.

Visual representations help in monitoring translation progress and recognizing active contributors.

---

## Coordinator Tools

The [**Coordinator Tools**](https://github.com/Mdwiki-TD/tdc) module is a specialized component within the WikiProjectMed Translation Dashboard, designed to empower translation coordinators with administrative capabilities for managing translation projects, monitoring activities, and performing maintenance tasks.

# End points

### Page Routes

| Endpoint                                     | Method | Description                                                               |
| -------------------------------------------- | ------ | ------------------------------------------------------------------------- |
| `/`                                          | GET    | Main dashboard — search for missing translations by campaign and language |
| `/leaderboard.php`                           | GET    | Leaderboard — translation stats per user, language, and campaign          |
| `/leaderboard.php?get=users&user={user}`     | GET    | User-specific translation statistics                                      |
| `/leaderboard.php?get=langs&langcode={code}` | GET    | Language-specific translation statistics                                  |
| `/leaderboard.php?camps=1`                   | GET    | Campaign and article statistics tables                                    |
| `/leaderboard.php?graph=1`                   | GET    | Translation timeline graph (server-rendered)                              |
| `/leaderboard.php?graph_api=1`               | GET    | Translation timeline graph (API-driven JS)                                |
| `/missing.php`                               | GET    | Missing articles — top languages by missing article count                 |
| `/sitelinks.php`                             | GET    | Wikidata sitelinks report                                                 |
| `/translate.php`                             | GET    | Redirect to Content Translation tool                                      |
| `/x.php`                                     | GET    | AJAX-based leaderboard using DataTables                                   |
| `/auth.php`                                  | GET    | Redirect to authentication                                                |
| `/coordinator.php`                           | GET    | Redirect to coordinator tools                                             |
| `/404.php`                                   | GET    | 404 error page                                                            |

### API Endpoints

All API calls use `/api.php` with a `get=` query parameter to indicate the operation.

| Endpoint                                    | Method   | Description                                |
| ------------------------------------------- | -------- | ------------------------------------------ |
| `/api.php?get=settings`                     | GET/POST | Platform settings                          |
| `/api.php?get=categories`                   | GET/POST | Campaign categories                        |
| `/api.php?get=pages`                        | GET/POST | Pages by language and category             |
| `/api.php?get=pages_by_user_or_lang`        | GET/POST | Pages filtered by user or language         |
| `/api.php?get=graph_data`                   | GET/POST | Monthly translation counts                 |
| `/api.php?get=coordinators`                 | GET/POST | Coordinator users                          |
| `/api.php?get=lang_views2`                  | GET/POST | Views per language                         |
| `/api.php?get=user_views2`                  | GET/POST | Views per user                             |
| `/api.php?get=user_status`                  | GET/POST | User year and language data                |
| `/api.php?get=user_lang_status`             | GET/POST | Language year data                         |
| `/api.php?get=top_users`                    | GET/POST | Top users leaderboard data                 |
| `/api.php?get=top_langs`                    | GET/POST | Top languages leaderboard data             |
| `/api.php?get=top_lang_of_users`            | GET/POST | Top language per user                      |
| `/api.php?get=status`                       | GET/POST | Translation timeline status                |
| `/api.php?get=missing_by_lang_and_category` | GET/POST | Missing articles by language and category  |
| `/api.php?get=exists_by_lang_and_category`  | GET/POST | Existing articles by language and category |
| `/api.php?get=statics_by_category`          | GET/POST | Statistics by category                     |
| `/api.php?get=titles`                       | GET/POST | Article metadata                           |
| `/api.php?get=views_new`                    | GET/POST | Page views                                 |
| `/api.php?get=projects`                     | GET/POST | User groups and projects                   |
| `/api.php?get=qids`                         | GET/POST | Wikidata QID mapping                       |
| `/api.php?get=users`                        | GET/POST | Username autocomplete                      |
| `/api.php?get=lang_names`                   | GET/POST | Language code autocomplete                 |
| `/api.php?get=users_no_inprocess`           | GET/POST | Users excluded from process tracking       |
| `/api.php?get=full_translators`             | GET/POST | Full-translate-enabled users               |
| `/api.php?get=translate_type`               | GET/POST | Translation type configuration             |
| `/api.php?get=count_pages`                  | GET/POST | Page counts per user                       |
| `/api.php?get=langs`                        | GET/POST | Language list                              |
| `/api.php?get=in_process`                   | GET/POST | In-process translations                    |
| `/api.php?get=category_members`             | GET/POST | Category member IDs                        |
| `/api.php?get=leaderboard_table`            | GET/POST | Leaderboard table data (deprecated)        |
