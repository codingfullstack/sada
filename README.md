# SADA WordPress project

WordPress/LocalWP project files needed to run the site locally.

## Contents

- `app/public/` - WordPress files, theme, plugins, and uploaded media.
- `app/sql/local.sql` - local database dump.
- `conf/` - LocalWP service configuration templates.
- `logs/` - Local runtime logs from this copy.

## Local setup

1. Create/import a WordPress site in LocalWP.
2. Copy or keep these project files in the LocalWP site folder.
3. Import `app/sql/local.sql` into the local database.
4. Use the included `app/public/wp-config.php` or regenerate it locally if your database settings differ.
