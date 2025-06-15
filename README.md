# QueueWorker Plugin for CakePHP

[![Latest Version](https://img.shields.io/github/v/tag/mahankals/CakePHP-QueueWorker?label=Latest)](https://github.com/mahankals/CakePHP-QueueWorker/releases)
[![Stable Version](https://img.shields.io/github/v/release/mahankals/CakePHP-QueueWorker?label=Stable&sort=semver)](https://github.com/mahankals/CakePHP-QueueWorker/releases)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/github/downloads/mahankals/CakePHP-QueueWorker/total.svg)](https://github.com/mahankals/CakePHP-QueueWorker/releases)

[![GitHub Stars](https://img.shields.io/github/stars/mahankals/CakePHP-QueueWorker?style=social)](https://github.com/mahankals/CakePHP-QueueWorker/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/mahankals/CakePHP-QueueWorker?style=social)](https://github.com/mahankals/CakePHP-QueueWorker/network/members)
[![GitHub Watchers](https://img.shields.io/github/watchers/mahankals/CakePHP-QueueWorker?style=social)](https://github.com/mahankals/CakePHP-QueueWorker/watchers)


<!-- [![Latest Stable Version](https://poser.pugx.org/mahankals/CakePHP-QueueWorker/v/stable)](https://packagist.org/packages/mahankals/CakePHP-QueueWorker)
[![Total Downloads](https://poser.pugx.org/mahankals/CakePHP-QueueWorker/downloads)](https://packagist.org/packages/mahankals/CakePHP-QueueWorker)
 -->

---

A lightweight, database-driven job queue plugin for [CakePHP 5](https://cakephp.org/).  
Supports asynchronous task execution, retries, error tracking, admin view, and CLI commands.

## Features

- Simple job enqueueing: `$queue->createJob('MyTask', [...]);`
- CLI workers: `bin/cake run_worker`
- Task creation: `bin/cake bake task MyTask`
- Error/retry support
- Pluggable access control via static callback

## Installation

You can install this plugin directly from GitHub using Composer:

### 1. Add the GitHub repository to your app's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/mahankals/CakePHP-QueueWorker"
    }
]
```

### 2. Require the plugin via Composer:

```bash
composer require mahankals/cakephp-queueworker:dev-main
```

## Load the plugin

### Method 1: from terminal

```bash
bin/cake plugin load QueueWorker
```

### Method 2: load in `Application.php`, bootstrap method

```bash
$this->addPlugin('QueueWorker');
```

## Create tables with migration

```php
  bin/cake migrations migrate --plugin QueueWorker
```

## Permission (optional)

In `src/Application.php`, bootstrap method

```php
  \QueueWorker\Controller\QueueJobsController::$accessPolicy = fn($req) =>
    $req->getAttribute('identity')?->get('role') === 'admin';
```

now queue jobs are available on **[http://localhost:8765/queue-worker/jobs](http://localhost:8765/queue-worker/jobs)**

## 🛠 Creating a Task and Adding Jobs to the Queue

This plugin uses task classes located in `src/Queue/Task/` to execute background jobs.

---

### 1. 🧱 Create a Task Class

Create a file:  
`src/Queue/Task/MyTaskTask.php`

```php
<?php
namespace App\Queue\Task;

class MyTaskTask
{
    public function run(array $data): void
    {
        // Example job logic
        echo "Running MyTask with data: " . json_encode($data) . PHP_EOL;
    }
}
```

You can use CLI to generate this:

```bash
bin/cake bake task MyTask
```

### 2. 📨 Add a Job to the Queue

In any controller, command, or service:

```php
use QueueWorker\QueueWorker;

$queue = new QueueWorker();
$queue->createJob('MyTask', ['user_id' => 42, 'message' => 'Welcome']);
```

This stores the job in the database table (queue_jobs) and marks it as queued.

### 3. 🏃 Run the Queue Worker on local

Use the CLI to start processing jobs:

```bash
bin/cake queue start
```

The worker:
  - Fetches one job at a time
  - Executes the corresponding Task class
  - Tracks attempts, errors, and status

### 3. 🏃 Run the Queue Worker on Production

#### 1. with cronetab

`export EDITOR=nano && crontab -e`
```bach
* * * * * cd /var/www && bin/cake queue start --once >> ./logs/queue_start.log 2>&1
```

#### 2. with Supervisor

Create `/etc/supervisor/conf.d/cake_queue_worker.conf`

```conf
[program:cake_queue_worker]
directory=/var/www/your-app
command=/usr/bin/php bin/cake queue start
autostart=true
autorestart=true
stderr_logfile=/var/www/your-app/logs/worker_error.log
stdout_logfile=/var/www/your-app/logs/worker_output.log
user=www-data
numprocs=1
```

**Enable & Start Supervisor**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cake_queue_worker
```

Confirm Worker is Running

```bash
ps aux | grep 'queue start'
```

or

```bash
sudo supervisorctl status
```


## 💡 Tips

### You can retry failed jobs manually:

Retry all failed jobs:

```bash
bin/cake queue retry
```

Retry a single job:

```bash
bin/cake queue retry 42
```

You can safely terminate queue worker manually:

```bash
bin/cake queue stop
```

## Customization

You can publish templates for customization:

```bash
bin/cake queueworker publish
```
