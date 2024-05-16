### What is the util directory?
The util directory contains utility scripts which can only be ran via the CLI. These scripts are generally used as crons, one-off tasks or for general maintenance.

Run them via the CLI using the following command:
```bash
cd /var/www/default/htdocs/httpdocs/util
php script_name.php
```

It's important to note that these scripts are not intended to be ran via the browser. They are designed to be ran via the CLI only.

It's also probably useful to run "all_maintinance.php" script via a cron job. This script will run all the maintenance scripts in the util directory.