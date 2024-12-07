const { exec } = require('child_process');
const readline = require('readline');
const fs = require('fs');

const projectName = process.argv[2];  // Receives the project name from the argument passed

let port = 3000

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

const salt = () => {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < 15; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

const readLineAsync = msg => {
    return new Promise(resolve => {
        rl.question(msg, userRes => {
            resolve(userRes);
        });
    });    
}

const writePackageJSON = () => {
    const packageJSON = require("./sample.package.json");

    let packageStr = JSON.stringify(packageJSON, null, 2);

    packageStr = packageStr.replaceAll("<port>",port).replaceAll("<projectName>",projectName);

    fs.writeFile("./package.json", packageStr, 'utf8', (err) => {
        if (err) {
            console.error('Error writing package.json file:', err);
        } else {
            console.log('package.json file created successfully.');
        }
    });
}

const writeComposerJSON = () => {
    const composerJSON = require("./sample.composer.json");

    let composerStr = JSON.stringify(composerJSON, null, 2);

    // composerStr = composerStr.replaceAll("<port>",port).replaceAll("<projectName>",projectName);

    fs.writeFile("./composer.json", composerStr, 'utf8', (err) => {
        if (err) {
            console.error('Error writing composer.json file:', err);
        } else {
            console.log('composer.json file created successfully.');
        }
    });
}

const writeConfigJSON = () => {
    const configJSON = require("./sample.config.json");

    const mySalt = salt();

    const publicKey = salt();

    let configStr = JSON.stringify(configJSON, null, 2);

    configStr = configStr.replaceAll("<port>",port).replaceAll("<projectName>",projectName).replaceAll("<salt>",mySalt).replaceAll("<publickey>",publicKey);

    fs.writeFile("./config.json", configStr, 'utf8', (err) => {
        if (err) {
            console.error('Error writing config.json file:', err);
        } else {
            console.log('config.json file created successfully.');
        }
    });
}

const writeColorScheme = () => {
    fs.readFile("./sample-color-scheme.css", 'utf8', (err, data) => {
        if (err) {
          console.error('Error reading color scheme file:', err);
          return;
        }
      
        // Write the content to the destination file
        fs.writeFile("./color-scheme.css", data, 'utf8', (err) => {
          if (err) {
            console.error('Error writing color scheme file:', err);
            return;
          }
          console.log('Color scheme file created successfully.');
        });
      });     
}

const writeDotEvnFiles = () => {
    const devData = `VUE_OPTIONS_API=true\nVUE_PROD_DEVTOOLS=true\nVUE_PROD_HYDRATION_MISMATCH_DETAILS=false\nDEBUG=true\nEVO_API_URL=http://localhost:${port}`
    const liveData = "VUE_OPTIONS_API=true\nVUE_PROD_DEVTOOLS=false\nVUE_PROD_HYDRATION_MISMATCH_DETAILS=false\nDEBUG=false\nEVO_API_URL=https://example.com"

    // Write the content to the destination file
    fs.writeFile("./.env.development", devData, 'utf8', (err) => {
        if (err) {
          console.error('Error writing development environment file:', err);
          return;
        }
        console.log('Development environment file created successfully.');
    });

    fs.writeFile("./.env.production", liveData, 'utf8', (err) => {
        if (err) {
          console.error('Error writing production environment file:', err);
          return;
        }
        console.log('Production environment file created successfully.');
    });
}

const startApp = async() => {
    console.log("Welcome to Evolution!")
    if (!projectName) {
        projectName = await readLineAsync("What is the name of the project you want to build?");
    }
    port = await readLineAsync("What is the local environment port? Enter 3000 or any other port that you wish?");

    writePackageJSON();

    writeComposerJSON()

    writeConfigJSON()

    writeColorScheme()

    writeDotEvnFiles()

    rl.close();

    exec(`git clone https://github.com/kinsokafor/EvoSamples EvoSamples`)

    console.log("Installation complete");

    console.log("Run composer install and npm install to continue");
}

startApp();