[user]: mahankals
[repo]: Package-Template
[title]: [repo]
[name]: package-teplate

# Package-Template

[![Latest Version](https://img.shields.io/github/v/tag/mahankals/Package-Template?label=Latest)](https://github.com/mahankals/Package-Template)
[![Stable Version](https://img.shields.io/github/v/release/mahankals/Package-Template?label=Stable&sort=semver)](https://github.com/mahankals/Package-Template/releases)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/github/downloads/mahankals/Package-Template/total.svg)](https://github.com/mahankals/Package-Template/releases)

[![GitHub Stars](https://img.shields.io/github/stars/mahankals/Package-Template?style=social)](https://github.com/mahankals/Package-Template/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/mahankals/Package-Template?style=social)](https://github.com/mahankals/Package-Template/network/members)
[![GitHub Watchers](https://img.shields.io/github/watchers/mahankals/Package-Template?style=social)](https://github.com/mahankals/Package-Template/watchers)


<!-- [![Latest Stable Version](https://poser.pugx.org/mahankals/Package-Template/v/stable)](https://packagist.org/packages/mahankals/Package-Template)
[![Total Downloads](https://poser.pugx.org/mahankals/Package-Template/downloads)](https://packagist.org/packages/mahankals/Package-Template)
 -->

Package description

---

## Features

- 

## Installation

You can install this plugin directly from GitHub using Composer:

### 1. Add the GitHub repository to your app's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/mahankals/Package-Template"
    }
]
```

### 2. Require the plugin via Composer:

```bash
composer require mahankals/Package-Template:dev-main
bin/cake plugin load Package-Template
```

## Load the plugin

### Method 1: from terminal

```bash
bin/cake plugin load Package-Template
```

### Method 2: load in `Application.php`, bootstrap method

```bash
$this->addPlugin('Package-Template');
```

