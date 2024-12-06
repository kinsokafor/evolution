const fs = require("fs");
const archiver = require("archiver");
const readline = require("readline");

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
});

const readLineAsync = (msg) =>
  new Promise((resolve) => rl.question(msg, (userRes) => resolve(userRes.trim())));

const validateYesNoInput = async (msg) => {
  let input;
  do {
    input = (await readLineAsync(msg)).toLowerCase();
  } while (!["y", "n"].includes(input));
  return input === "y";
};

const updateConfigJSON = async (mode = "production") => {
  try {
    const configPath = "./config.json";
    if (!fs.existsSync(configPath)) {
      throw new Error("config.json file not found.");
    }

    const configJSON = JSON.parse(fs.readFileSync(configPath, "utf8"));
    configJSON.mode = mode;

    await fs.promises.writeFile(configPath, JSON.stringify(configJSON, null, 2), "utf8");
    console.log(`config.json file updated to ${mode} successfully.`);
  } catch (error) {
    console.error("Error updating config.json file:", error.message);
    throw error;
  }
};

const bundleFiles = async () => {
  try {
    const timestamp = Date.now();
    const outputPath = `./bundle-${timestamp}.zip`;
    const output = fs.createWriteStream(outputPath);
    const archive = archiver("zip", { zlib: { level: 9 } });
    const bundledFiles = [];

    // Setup event listeners for the archive process
    output.on("close", () => {
      console.log(`${archive.pointer()} total bytes`);
      console.log(`Archive finalized: ${outputPath}`);
      console.log("Bundled files/directories:", bundledFiles.join(", "));
    });

    archive.on("warning", (err) => {
      if (err.code !== "ENOENT") throw err;
      console.warn("Warning during archiving:", err.message);
    });

    archive.on("error", (err) => {
      console.error("Error during archiving:", err.message);
      throw err;
    });

    archive.pipe(output);

    // Add default files
    const defaultFiles = ["cron.php", "index.php", "Install.php"];
    defaultFiles.forEach((file) => {
      if (fs.existsSync(file)) {
        archive.file(file, { name: file });
        bundledFiles.push(file);
      } else {
        console.warn(`File not found: ${file}`);
      }
    });

    // Ask user about additional files
    const includeAll = await validateYesNoInput("Do you want to bundle all files? (Y/N): ");
    if (includeAll) {
      await updateConfigJSON("production");
      const extraFiles = ["config.json", ".htaccess", "color-scheme.css"];
      extraFiles.forEach((file) => {
        if (fs.existsSync(file)) {
          archive.file(file, { name: file });
          bundledFiles.push(file);
        } else {
          console.warn(`File not found: ${file}`);
        }
      });
    } else {
      const optionalFiles = [
        { name: "config.json", prompt: "Do you want to bundle config.json? (Y/N): " },
        { name: ".htaccess", prompt: "Do you want to bundle .htaccess? (Y/N): " },
        { name: "color-scheme.css", prompt: "Do you want to bundle color-scheme.css? (Y/N): " },
      ];

      for (const file of optionalFiles) {
        if (await validateYesNoInput(file.prompt)) {
          if (file.name === "config.json") await updateConfigJSON("production");
          if (fs.existsSync(file.name)) {
            archive.file(file.name, { name: file.name });
            bundledFiles.push(file.name);
          } else {
            console.warn(`File not found: ${file.name}`);
          }
        }
      }
    }

    // Include directories
    const directories = [
      { path: "vendor/", name: "vendor" },
      { path: "./Public", name: "Public/", ignore: ["Themes/*/Views/cache/*", "*/.git"] },
      { path: "./EvoPhp", name: "EvoPhp/", ignore: ["Database/Config.php"] },
    ];

    directories.forEach(({ path, name, ignore }) => {
      if (fs.existsSync(path)) {
        archive.glob("**", { cwd: path, ignore: ignore || [] }, { prefix: name });
        bundledFiles.push(name);
      } else {
        console.warn(`Directory not found: ${path}`);
      }
    });

    await archive.finalize();
    await updateConfigJSON("development"); // Revert changes after bundling
    console.log("Bundle creation completed successfully.");
  } catch (error) {
    console.error("Error during bundling process:", error.message);
  } finally {
    rl.close();
  }
};

// Start the bundling process
bundleFiles();
