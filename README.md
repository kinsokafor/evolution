# EvoPHP

EvoPHP is a command-line tool designed to simplify the creation, installation, and management of plugins, themes, and projects using Evolution by Kins Okafor. Whether you are starting a new project or extending functionality with plugins and themes, EvoPHP provides the tools you need to get up and running quickly.

---

## Table of Contents
1. [Installation](#installation)
2. [Getting Started](#getting-started)
    - [Initialize a New Project](#initialize-a-new-project)
    - [Install a Plugin or Theme](#install-a-plugin-or-theme)
    - [Create a New Plugin or Theme](#create-a-new-plugin-or-theme)
3. [Plugin Development](#plugin-development)
4. [Troubleshooting](#troubleshooting)
5. [License](#license)

---

## Installation

### Prerequisites
Ensure the following are installed on your system:
- **Node.js**: Verify by running `node -v` and `npm -v`.
- **Composer** (optional): For managing PHP dependencies.

### Install EvoPHP
To install EvoPHP globally, run:
```bash
npm install -g evophp
```

This command makes the `evophp` CLI available system-wide.

---

## Getting Started

### Initialize a New Project
To create and set up a new project, run:
```bash
evophp init <project-name>
```
Example:
```bash
evophp init my-project
```

This will:
1. Clone the EvoPHP repository into a folder named `<project-name>`.
2. Run `node evolution` with the project name as an argument.
3. Install Composer dependencies (if a `composer.json` file exists).
4. Install Node.js dependencies using `npm install`.

After setup, navigate to your project folder:
```bash
cd my-project
```

To start your project:
```bash
npm run start
```

---

### Install a Plugin or Theme
To install a plugin or theme, use the following command:
```bash
evophp install <plugin|theme> <author/repository-name>
```
Example:
```bash
evophp install plugin kinsokafor/eEdu
```

The command will fetch the specified plugin or theme and set it up for use.

---

### Create a New Plugin or Theme
To create a new plugin or theme, use:
```bash
evophp new <plugin|theme> <author/repository-name>
```
Example:
```bash
evophp new plugin kinsokafor/MyAwesomePlugin
```

You will be prompted to provide additional details like:
- The entry URI.
- A unique 3-character plugin prefix.

---

## Plugin Development

### Creating a Plugin
To create your own plugin:
1. Use the [EvoPlugin GitHub template](https://github.com/kinsokafor/EvoPlugin) and click "Use this template."
2. Name your repository (e.g., `ExamplePlugin`).
3. Use the [EvoPlugin.js GitHub template](https://github.com/kinsokafor/EvoPlugin.js) for the JavaScript counterpart. Name it similarly, adding `.js` to the end (e.g., `ExamplePlugin.js`).

### Installing Your Plugin
To install your new plugin:
```bash
npm run --i plugin
```
You will be prompted for:
- Plugin name (e.g., `kinsokafor/ExamplePlugin`).
- Entry URI (e.g., `example-plugin`).
- Unique 3-character plugin prefix (e.g., `ex1`).

---

## Troubleshooting

- **Permission Issues**: On Linux/macOS, use `sudo` for commands requiring elevated permissions.
- **Node.js Installation**: Verify installation using `node -v` and `npm -v`.
- **Versioning Conflicts**: If updating, increment the version in `package.json`.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

### Key Features of This Version:
1. **Clear Commands**: All commands are listed with syntax, examples, and expected results.
2. **Plugin Development**: Detailed instructions for creating and managing plugins.
3. **Troubleshooting Section**: Practical solutions to common issues.
4. **User-Friendly Layout**: A clean structure with headings and examples for easy navigation.
