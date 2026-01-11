# pfSense Syslog Migration Tool (InfluxDB → MySQL)

This project migrates parsed **pfSense syslog data** stored in **MySQL** into **InfluxDB** for analytics, dashboards, and long-term metrics storage.

It is designed to run **headless** on a Linux server and can be executed manually or scheduled via **cron**.

---

## 📁 Project Structure

```

src/
├── Config/
│   └── AppConfig.php               # Central configuration (DB, Influx, limits)
├── Logger/
│   └── DebugLogger.php             # Console / file logger
├── Influx/
│   ├── InfluxClient.php            # InfluxDB HTTP client
│   ├── InfluxServiceManager.php    # Write / delete / batch logic
│   └── InfluxResponseParser.php    # InfluxDB response handling
├── Mysql/
│   ├── MysqlConnection.php         # PDO connection factory
│   ├── PfsenseLogRepository.php    # Syslog record access
│   └── PortCountRepository.php     # Aggregated port statistics
├── Parser/
│   └── PfsenseMessageParser.php    # pfSense syslog message parser
├── Migration/
│   └── PfsenseSyslogMigrator.php   # Migration orchestration logic
├── Runner/
│   └── MigrationRunner.php         # Safe execution wrapper
├── InfluxRun.php                   # Entry point (CLI runner)
└── README.md

```

---

## ⚙️ Requirements

- **PHP 8.1+**
- **MySQL / MariaDB**
- **InfluxDB 2.x**
- Linux (Ubuntu recommended)
- CLI access (cron support)

---

## 🔧 Configuration

All configuration values are defined in:

```

src/Config/AppConfig.php

````

This includes:
- MySQL host, database, user, password
- InfluxDB URL, bucket, token, org
- Batch size and processing limits

⚠️ **Important:**  
Ensure credentials are correct before running the migration.

---

## ▶️ Running the Migration (Manual)

From the project root directory:

```bash
php InfluxRun.php
````

If successful, you should see log output indicating:

* Connection success
* Records processed
* InfluxDB write results

---

## ⏱️ Running via Cron (Ubuntu)

### 1️⃣ Edit crontab

```bash
crontab -e
```

### 2️⃣ Example Cron Job (Every 5 Minutes)

```cron
*/5 * * * * /usr/bin/php /full/path/to/InfluxRun.php >> /var/log/pfsense_migration.log 2>&1
```

⚠️ Notes:

* Always use **absolute paths**
* Ensure `/usr/bin/php` is correct (`which php`)
* Log file must be writable

---

## 📝 Logs

Logs are written to:

* STDOUT when run manually
* Log file when run via cron

Example:

```
/var/log/pfsense_migration.log
```

---

## 🧠 Execution Flow

1. Load configuration
2. Connect to MySQL
3. Fetch unprocessed pfSense syslog entries
4. Parse syslog messages
5. Write metrics to InfluxDB
6. Update MySQL migration state
7. Exit safely

---

## 🚀 Best Practices

* Run once manually before enabling cron
* Monitor logs after first cron execution
* Keep batch size reasonable to avoid memory spikes
* Backup MySQL before first migration

---

## 🛠️ Troubleshooting

### ❌ Class not found error

Ensure either:

* Composer autoload is configured **OR**
* All required PHP files are included manually

### ❌ Cron not running

* Check `/var/log/syslog`
* Confirm PHP path
* Confirm file permissions

---

## 📌 Future Improvements (Optional)

* `.env` support
* Systemd service instead of cron
* Docker deployment
* Retry & backoff logic
* Metrics health check

---
