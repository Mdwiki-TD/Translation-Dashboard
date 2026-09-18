<?php

# app env one of development/production/testing
putenv('APP_ENV=development');

# database informations
putenv('DB_HOST_TOOLS=localhost:3306');
putenv('DB_NAME=s54732__mdwikiz');

putenv('TOOL_TOOLSDB_USER=root');
putenv('TOOL_TOOLSDB_PASSWORD=root11');

# OAuth keys
putenv('CONSUMER_SECRET=CONSUMER_SECRET');
putenv('CONSUMER_KEY=CONSUMER_KEY');

# Security keys
putenv('COOKIE_KEY=def0000094b5407082bdbe864563db3dbe3c5e2d067ac8aadb4bd88ec354d39caf3f3c62e3aed3d232af62909ec630aef9d681c2db26f8e8c1037333b6ca36a47651552d');
putenv('DECRYPT_KEY=def000001358577eb292b944a354cfe446413d532d4c18c963597a88ec1daeba34080234b36ad1c54269ff04c443b5155c0c122a2c4e95137b12507b924f799bf13d8571');
putenv('JWT_KEY=c963597a884269ff04c443b5155c071');

# paths keys
putenv('TABLES_PATH=I:/MD_TOOLS/MDWIKI_MAIN_REPO/src/public_html/td/Tables');
