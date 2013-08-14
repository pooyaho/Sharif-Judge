# Sharif Judge

[Sharif Judge](http://sharifjudge.ir) is a free and open source online judge system for judging C, C++ and Java programming assignments. It can also be used for programming contests, but it has features specially for programming assignments.

Installation doesn't need root privileges.

The web interface is written in PHP (CodeIgniter framework) and the main backend is written in BASH.

## Features
  * Multiple user roles (admin, head instructor, instructor, student)
  * Sandboxing
  * Cheat detection (similar codes detection) using [Moss](http://theory.stanford.edu/~aiken/moss/)
  * Custom rule for grading late submissions
  * Download results in excel file
  * Download submitted codes in zip file
  * _"input/output compare"_ and _"special script"_ methods for checking output correctness
  * Add multiple users
  * Rejudge _(Will be added soon)_
  * Scoreboard

## Installation

Sharif Judge doesn't need root privileges to install. It has a simple installation.

### Requirements

For running Sharif Judge, a Linux server with following requirements is needed:

  * Webserver running PHP version 5 or later
  * PHP CLI (PHP command line interface, i.e. `php` shell command)
  * MySql database
  * PHP must have permission to run shell commands using [`shell_exec()`](http://www.php.net/manual/en/function.shell-exec.php) php function (specially `shell_exec("php");`)
  * Tools for compiling and running submitted codes (`gcc`, `g++`, `javac`, `java`)

### Installation

  1. Download the latest release from download page and unpack downloaded file in your public html directory.
  2. Create a MySql database for Sharif Judge.
  3. Set database connection settings in files `application/config/database.php` and `tester/queue_process.php`. Make sure that you have provided correct information in both files.
  4. Open Sharif Judge main page in a web browser and follow the installation process.
  5. Log in with your admin account.
  6. **[IMPORTANT]** Move folders `tester` and `assignments` somewhere outside your public directory. Then save their path in `Settings` page. **These two folders must be writable by PHP.**

### After Installation

  * Read the [documentation](http://sharifjudge.ir/docs).