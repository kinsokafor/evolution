# EvoPHP CLI - Comprehensive Guide

## Overview
EvoPHP CLI is a command-line interface designed to simplify the development and management of projects, plugins, and themes within the EvoPHP ecosystem. With intuitive commands and shorthands, developers can quickly set up projects, manage dependencies, and handle plugin/theme creation and installation with ease.

---

## Table of Contents
- [Installation](#installation)
- [All Commands](#all-commands)
  - [Initialization](#initialization)
  - [Plugin/Theme Creation](#plugintheme-creation)
  - [Plugin/Theme Installation](#plugintheme-installation)
  - [Plugin/Theme Uninstallation](#plugintheme-uninstallation)
  - [Dependency Management](#dependency-management)
- [Notes](#notes)
- [Further Documentation](#further-documentation)

---

## Installation

### Prerequisites
Ensure the following are installed on your system:
- **Node.js**: Verify by running `node -v` and `npm -v`.
- **Composer**: For managing PHP dependencies.

### Install EvoPHP
To install EvoPHP globally, run:
```bash
npm install -g evophp
```

This command makes the `evophp` CLI available system-wide.

---

## All Commands

### Initialization
```bash
evophp init ?<project-name>
```
- Initialize a new project.
- Examples:
  ```bash
  evophp init
  evophp init example-project
  ```

---

### Plugin/Theme Creation
```bash
evophp n | new plugin | theme <author>/<name>
```
- Create a new plugin or theme.
- Examples:
  - Plugin:
    ```bash
    evophp new plugin example-author/example-plugin
    evophp n plugin example-author/example-plugin
    ```
  - Theme:
    ```bash
    evophp new theme example-author/example-theme
    evophp n theme example-author/example-theme
    ```

---

### Plugin/Theme Installation
```bash
evophp i | install plugin | theme <author>/<name>
```
- Install a plugin or theme.
- Examples:
  - Plugin:
    ```bash
    evophp install plugin example-author/example-plugin
    evophp i plugin example-author/example-plugin
    ```
  - Theme:
    ```bash
    evophp install theme example-author/example-theme
    evophp i theme example-author/example-theme
    ```

---

### Plugin/Theme Uninstallation
```bash
evophp u | uninstall plugin | theme <plugin-name>
```
- Uninstall a plugin or theme.
- Examples:
  - Plugin:
    ```bash
    evophp uninstall plugin example-plugin
    evophp u plugin example-plugin
    ```
  - Theme:
    ```bash
    evophp uninstall theme example-theme
    evophp u theme example-theme
    ```

---

### Dependency Management
```bash
evophp d | install-dependencies <plugin-name>
```
- Install npm production dependencies.
- Examples:
  ```bash
  evophp install-dependencies example-plugin
  evophp d example-plugin
  ```

#### Development Dependencies
```bash
evophp d | install-dependencies <plugin-name> --dev | --save-dev
```
- Install npm development dependencies.
- Examples:
  ```bash
  evophp install-dependencies example-plugin --dev
  evophp d example-plugin --dev
  ```

#### Composer Dependencies
```bash
evophp d | install-dependencies <plugin-name> --composer
```
- Install composer dependencies.
- Examples:
  ```bash
  evophp install-dependencies example-plugin --composer
  evophp d example-plugin --composer
  ```

---

## Notes
- `<author>` must be a valid GitHub username.
- `<name>` must be a unique repository name (not already existing on GitHub).

---

## Further Documentation
For additional details, best practices, and advanced usage, please refer to the [official EvoPHP documentation](#).
