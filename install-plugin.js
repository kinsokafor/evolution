const shell = require('shelljs')
const path = require('path')
const {mergeConfig} = require('./evo.config');
const fs = require('fs');
const { dependencies } = require('webpack');
const axios = require('axios');
const config = require('./config.json');
let pass1 = false, pass2 = false

const plugin = process.env.npm_config_plugin
if(plugin == undefined) {
    throw new Error('Please specify the plugin to install npm run install-plugin --plugin=exampleplugin');
}
const arr = plugin
            .replace('https://github.com/', '')
            .replace('http://github.com/', '')
            .replace('//github.com/', '')
            .split('/')
if(arr.length < 2) {
    throw new Error('plugin name must include the author\'s name like johndoe/exampleplugin');
}
const [author, pluginName] = arr;

// clone the repos
shell.cd('./Public/Modules')
if (fs.existsSync(pluginName)) {
    pass1 = true
} else {
    try {
        shell.exec(`git clone https://github.com/${author}/${pluginName} ${pluginName}`)
        pass1 = true
    }
    catch(error) {
        console.error(error)
        pass1 = false
        throw new Error(error);
    }
}

shell.cd('../../EvoJsDev/Modules')
if (fs.existsSync(pluginName)) {
    pass2 = true
} else {
    try {
        shell.exec(`git clone https://github.com/${author}/${pluginName}.js ${pluginName}`)
        pass2 = true
    }
    catch(error) {
        console.error(error)
        pass2 = false
        throw new Error(error);
    }
}
shell.cd('../../')

//merge config file
if(pass1 && pass2) {
    const configFile = path.resolve(__dirname, `./EvoJsDev/Modules/${pluginName}/config.json`)
    if (fs.existsSync(configFile)) {
        const pluginConfig = require(configFile)
        mergeConfig(pluginConfig)
    }
    const dependenciesFile = path.resolve(__dirname, `./EvoJsDev/Modules/${pluginName}/dependencies.json`)
    if (fs.existsSync(dependenciesFile)) {
        const dependencies = require(dependenciesFile)
        try {
            if(dependencies.dev !== undefined) {
                shell.exec(`npm install -D ${dependencies.dev}`)
            }
            if(dependencies.prod !== undefined) {
                shell.exec(`npm install ${dependencies.prod}`)
            }
        }
        catch(error) {
            console.error(error)
            throw new Error(error);
        }
    }

    const sDependenciesFile = path.resolve(__dirname, `./Public/Modules/${pluginName}/dependencies.json`)
    if (fs.existsSync(sDependenciesFile)) {
        const dependencies = require(sDependenciesFile)
        try {
            if(dependencies.all !== undefined) {
                shell.exec(`composer require ${dependencies.all}`)
            }
        }
        catch(error) {
            console.error(error)
            throw new Error(error);
        }
    }

    console.log(`${plugin} plugin was installed successfully.`)
} else {
    console.error(`${plugin} plugin was not installed successfully. Try again.`)
}
