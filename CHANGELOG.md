# Changelog

All notable changes to this project will be documented in this file.  
This project adheres to [Semantic Versioning](https://semver.org/).

---

## [1.0.0] – 2025-06-11

### Added
- 🎉 Initial release of the CakeQueue plugin
- Job queue system with `queue_jobs` table and retry tracking
- CLI worker command: `bin/cake run_worker`
- CLI task generator: `bin/cake bake task MyTask`
- CLI retry command: `bin/cake retry`
- Web interface: `/queue-jobs`
- Plugin route protection via static `$accessPolicy` callback
- Error logging and retry logic with stack trace

### Fixed
- Proper handling of job status after worker termination (`Ctrl+C`)
- Off-by-one bug in retry limit logic

---

## [Unreleased]

### Planned
- Optional job delay and scheduling support
- Dashboard for failed jobs and retry UI
- Notification hooks (email/slack/webhook) on job failures
