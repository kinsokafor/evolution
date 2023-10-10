const { exec } = require('child_process');
import { createInterface } from "readline";
const fs = require('fs');

let projectName = "Evolution"

let port = 3000

const readline = createInterface({
    input: process.stdin,
    output: process.stdout
});

const readLineAsync = msg => {
    return new Promise(resolve => {
        readline.question(msg, userRes => {
            resolve(userRes);
        });
    });    
}

const writePackageJSON = () => {
    const packageJSON = require("sample.package.json");

    let packageStr = JSON.stringify(packageJSON, null, 2);

    packageStr = packageStr.replaceAll("<port>",port).replaceAll("<projectName>",projectName);

    fs.writeFile("package.json", packageStr, 'utf8', (err) => {
        if (err) {
        console.error('Error writing package.json file:', err);
        } else {
        console.log('package.json file created successfully.');
        }
    });
}

const writeConfigJSON = () => {
    const configJSON = require("sample.config.json");

    let configStr = JSON.stringify(configJSON, null, 2);

    configStr = configStr.replaceAll("<port>",port).replaceAll("<projectName>",projectName);

    fs.writeFile("config.json", configStr, 'utf8', (err) => {
        if (err) {
        console.error('Error writing config.json file:', err);
        } else {
        console.log('config.json file created successfully.');
        }
    });
}

const startApp = async() => {
    console.log("Welcome to Evolution!")
    projectName = await readLineAsync("What is the name of the project you want to build?");
    port = await readLineAsync("What is the local environment port? Enter 3000 or any other port that you wish");

    writePackageJSON();

    writeConfigJSON()

    exec(`composer install`, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error executing command: ${error}`);
            return;
        }

        console.log(`Command output: ${stdout}`);
        console.error(`Command errors: ${stderr}`);
    });

    exec(`npm install`, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error executing command: ${error}`);
            return;
        }

        console.log(`Command output: ${stdout}`);
        console.error(`Command errors: ${stderr}`);
    });
    // readline.close();
    // console.log("Your response was: " + userRes + " - Thanks!");
}

startApp();