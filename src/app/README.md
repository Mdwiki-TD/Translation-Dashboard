```
app/
├── ApiClients/
│   └── WikiApi.php
├── Controllers/
│   ├── AppRouter.php
│   ├── LeaderboardController.php
│   ├── LeaderboardJsController.php
│   ├── MissingController.php
│   └── SiteLinksController.php
├── Langs/
│   ├── lang_names.json
│   └── LangsTables.php
├── Leaderboard/
│   ├── helpers/
│   │   ├── Camps/
│   │   │   ├── Camps.php
│   │   │   └── CampsText.php
│   │   ├── Filters/
│   │   │   ├── FilterForm.php
│   │   │   ├── LeaderFilter.php
│   │   │   └── LeadHelp.php
│   │   ├── Graph/
│   │   │   ├── Graph.php
│   │   │   ├── GraphApi.php
│   │   │   └── LangUserGraph.php
│   │   ├── Langs/
│   │   │   ├── LangsSub.php
│   │   │   └── LeaderTablesLangs.php
│   │   ├── Users/
│   │   │   ├── LeaderTablesUsers.php
│   │   │   └── UsersSub.php
│   │   └── bootstrap.php
│   ├── LangsLeaderboard.php
│   ├── MainLeaderboard.php
│   └── UsersLeaderboard.php
├── MdwikiSql/
│   └── Database.php
├── Results27/
│   ├── Data/
│   │   └── ResultsFetcher.php
│   ├── Helpers/
│   │   ├── CardRenderer.php
│   │   └── TranslateTypeLoader.php
│   ├── Rows/
│   │   ├── ExistsRowBuilder.php
│   │   ├── InProcessRowBuilder.php
│   │   └── MissingRowBuilder.php
│   ├── Tables/
│   │   ├── AbstractResultsTable.php
│   │   ├── ExistsTable.php
│   │   ├── InProcessTable.php
│   │   └── MissingTable.php
│   └── ResultsLoader.php
├── SQLorAPI/
│   ├── ApiOrSqlService.php
│   ├── BaseTable.php
│   ├── CategoriesTable.php
│   ├── InProcessTable.php
│   ├── LeaderboardTable.php
│   ├── PagesTable.php
│   ├── QidsTable.php
│   ├── RecentTable.php
│   ├── SettingsTable.php
│   ├── TitlesTable.php
│   ├── UsersTable.php
│   └── ViewsTable.php
├── Templates/
│   ├── Missing/
│   │   └── missing.php
│   ├── SiteLinks/
│   │   └── sitelinks.php
│   └── TemplateRenderer.php
├── User/
│   ├── AccessKeyRepository.php
│   ├── CoordinatorRepository.php
│   ├── CurrentUser.php
│   ├── SessionManager.php
│   └── UserCookieService.php
├── Utils/
│   ├── Helps.php
│   ├── Html.php
│   ├── HtmlUrls.php
│   └── TrLink.php
├── autoload.php
├── bootstrap.php
├── Logger.php
└── Settings.php

```