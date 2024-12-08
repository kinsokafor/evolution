
# Project Installation Guide

This guide will help you install and set up the project with ease using the `evophp` package.

---

## **Installation**

### **1. Install the Package Globally**

To install the package globally, run the following command:

```bash
npm install -g evophp
```

This will install the `evophp` command, which you can use to initialize your project.

---

### **2. Clone the Repository and Install Dependencies**

Once installed, use the `evophp` command to clone the repository and set up the project.

Run the following command, replacing `your-project-name` with the desired name of your project:

```bash
evophp your-project-name
```

Example:
```bash
evophp jonikins-apartments
```

This will:
- Clone the `evolution` repository into a folder named `your-project-name`.
- Run `node evolution` with the project name passed as an argument.
- Install any Composer dependencies (if a `composer.json` file exists).
- Install Node.js dependencies via `npm install`.

---

### **3. Start the Project**

After the installation completes, navigate to your project directory:

```bash
cd your-project-name
```

To start the project, you can run:

```bash
npm run start
```

This will launch your project and you can begin working on it.

---

## **Plugin Installation**

You can install a plugin by running the following command:

```bash
npm run plugin
```

You will be prompted to provide the plugin name. The plugin name should be the full GitHub repository name, including the author. For example, if you want to install the `kinsokafor/eEdu` plugin, you will enter:

```
kinsokafor/eEdu
```

---

## **Creating a New Plugin**

If you want to create your own plugin, follow these steps:

1. **Create the Plugin Repository**:
   - Go to [EvoPlugin GitHub template](https://github.com/kinsokafor/EvoPlugin) and click on "Use this template".
   - Name your repository (e.g., `ExamplePlugin`).

2. **Create the JavaScript Counterpart**:
   - Go to [EvoPlugin.js GitHub template](https://github.com/kinsokafor/EvoPlugin.js) and repeat the same process.
   - Make sure to name the repository the same as your plugin name but add `.js` to the end (e.g., `ExamplePlugin.js`).

3. **Install Your New Plugin**:
   - Run the following command to install your new plugin:
     ```bash
     npm run --i plugin
     ```
   - You will be asked to provide:
     - The plugin name (e.g., `kinsokafor/ExamplePlugin`).
     - The entry URI (e.g., `example-plugin`).
     - A unique 3-character plugin prefix (e.g., `ex1`).

---

## **Troubleshooting**

If you encounter any issues during the installation process, check the following:
- **Ensure Node.js is installed**: Run `node -v` and `npm -v` to verify that Node.js is correctly installed.
- **Check for permission issues**: If you encounter permission errors, try running the command with `sudo` (on Linux/macOS) or as an administrator (on Windows).
- **Check the version**: If you're updating an existing package, make sure to increment the version number in the `package.json` before republishing.

---

## **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

Let me know if you'd like to make any further changes or need additional clarification!
