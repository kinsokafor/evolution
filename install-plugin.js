const shell = require('shelljs')
const path = require('path')
const {mergeConfig} = require('./evo.config');
const fs = require('fs');
const readline = require('readline');
let pass1 = false, pass2 = false

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

const readLineAsync = msg => {
    return new Promise(resolve => {
        rl.question(msg, userRes => {
            resolve(userRes);
        });
    });    
}

const getPluginName = async() => {
    var check = false;
    let arr = [];

    do {
        const plugin = await readLineAsync("Enter the name of the plugin?");

        arr = plugin.replace('https://github.com/', '')
                .replace('http://github.com/', '')
                .replace('//github.com/', '')
                .split('/')
        if(arr.length < 2) {
            console.error('plugin name must include the author\'s name like johndoe/exampleplugin');
        } else {
            check = true;
        }
    } while(!check)

    return arr;
}

const getThemeName = async() => {
    var check = false;
    let arr = [];

    do {
        const theme = await readLineAsync("Enter the name of the theme?");

        arr = theme.replace('https://github.com/', '')
                .replace('http://github.com/', '')
                .replace('//github.com/', '')
                .split('/')
        if(arr.length < 2) {
            console.error('theme name must include the author\'s name like johndoe/exampletheme');
        } else {
            check = true;
        }
    } while(!check)

    return arr;
}

const test = (str, maxLength) => {
    str = str.trim().replace(" ", "")
    if(str.length > maxLength) {
        console.error(`Your input cannot be longer than ${maxLength} characters`);
        return false
    }
    if(str.length < 1) {
        console.error(`You have not made any entry`);
        return false
    }
    return str;
}

const clone = async(author, pluginName) => {
    // clone the repos
    shell.cd('./Public/Modules')
    if (fs.existsSync(pluginName)) {
        pass1 = true
    } else {
        try {
            var c = shell.exec(`git clone https://github.com/${author}/${pluginName} ${pluginName}`)
            if(c.code == 0) {
                pass1 = true
            }
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
            var c = shell.exec(`git clone https://github.com/${author}/${pluginName}.js ${pluginName}`)
            if(c.code == 0) {
                pass2 = true
            }
        }
        catch(error) {
            console.error(error)
            pass2 = false
            throw new Error(error);
        }
    }
    shell.cd('../../')
    return (pass1 && pass2);
}

const cloneTheme = async(author, themeName) => {

        shell.cd('./Public/Themes')
        if (fs.existsSync(themeName)) {
            pass1 = true
        } else {
            try {
                var c = shell.exec(`git clone https://github.com/${author}/${themeName} ${themeName}`)
                if(c.code == 0) {
                    pass1 = true
                }
            }
            catch(error) {
                console.error(error)
                pass1 = false
                throw new Error(error);
            }
        }
        shell.cd('../../')
        return pass1

}

const editFile = async (file, data) => {
    return new Promise((resolve, reject) => {
        fs.readFile(file, 'utf8', (err, fileData) => {
            if (err) {
              console.error('Error reading color scheme file:', err);
              reject(err)
              return;
            }
            for(var i in data) {
                fileData = fileData.replace(`<${i}>`, data[i])
            }
            // Write the content to the destination file
            fs.writeFile(file, fileData, 'utf8', (err) => {
              if (err) {
                console.error('Error writing color scheme file:', err);
                reject(err)
                return;
              }
              console.log(`${file} file created successfully.`);
              resolve(file);
            });
        });
    })
         
}

const install = async() => {

    const [author, pluginName] = await getPluginName();

    const done = await clone(author, pluginName);

    //merge config file
    if(done) {
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

        console.log(`${pluginName} plugin was installed successfully.`)
    } else {
        console.error(`${pluginName} plugin was not installed successfully. Try again.`)
    }
}

const newPlugin = async() => {

    const [author, pluginName] = await getPluginName();

    const done = await clone(author, pluginName);

    if(done) {
        var pluginPrefix = await readLineAsync("Enter Plugin Prefix?");
        while(!test(pluginPrefix, 3)) {
            pluginPrefix = await readLineAsync("Enter Plugin Prefix?");
        }
        pluginPrefix = pluginPrefix.trim().replace(" ", "").toUpperCase()

        var entryURI = await readLineAsync("Entry URI?");
        while(!test(entryURI, 20)) {
            entryURI = await readLineAsync("Entry URI?");
        }
        entryURI = entryURI.trim().replace(" ", "").toLowerCase()

        const entry = pluginPrefix + "Main"

        await editFile(`./EvoJsDev/Modules/${pluginName}/config.json`, {
            pluginName: pluginName,
            entry: entry
        }).then(file => {
            try {
                fs.rename(`./EvoJsDev/Modules/${pluginName}/Entry`, 
                `./EvoJsDev/Modules/${pluginName}/${entry}`, function(err) {
                    if ( err ) console.error('ERROR: ' + err);
                });
            } catch(e) {}
            
            const configFile = path.resolve(__dirname, `./EvoJsDev/Modules/${pluginName}/config.json`)
            if (fs.existsSync(configFile)) {
                const pluginConfig = require(configFile)
                mergeConfig(pluginConfig)
            }
        })

        await editFile(`./Public/Modules/${pluginName}/Routes.php`, {
            pluginName: pluginName,
            entry: entry,
            entryURI: entryURI,
            pluginPrefix: pluginPrefix
        })
        
        if (fs.existsSync(path.resolve(__dirname, `./Public/Modules/${pluginName}/Controller.php`))) {
            await editFile(`./Public/Modules/${pluginName}/Controller.php`, {
                pluginName: pluginName,
                pluginPrefix: pluginPrefix
            }).then(file => {
                
                fs.rename(`./Public/Modules/${pluginName}/Controller.php`, 
                `./Public/Modules/${pluginName}/${pluginPrefix}Controller.php`, function(err) {
                    if ( err ) console.error('ERROR: ' + err);
                });
                
                fs.rename(`./Public/Modules/${pluginName}/Views/Entry`, 
                `./Public/Modules/${pluginName}/Views/${entry}`, function(err) {
                    if ( err ) console.error('ERROR: ' + err);
                });
            })
        }

        console.log(`${pluginName} plugin was installed successfully.`)
    } else {
        console.error(`${pluginName} plugin was not installed successfully. Try again.`)
    }
}

const newTheme = async() => {

    const [author, themeName] = await getThemeName();

    const done = await cloneTheme(author, themeName);

    if(done) {
        await editFile(`./Public/Themes/${themeName}/Data/index.data.php`, {
            themeName: themeName
        })

        await editFile(`./Public/Themes/${themeName}/Data/login.data.php`, {
            themeName: themeName
        })

        await editFile(`./Public/Themes/${themeName}/Views/index.blade.php`, {
            themeName: themeName
        })

        await editFile(`./Public/Themes/${themeName}/Views/login.blade.php`, {
            themeName: themeName
        })

        console.log(`${themeName} theme was installed successfully.`)
    } else {
        console.error(`${themeName} theme was not installed successfully. Try again.`)
    }
}

const installTheme = async() => {

    const [author, themeName] = await getThemeName();

    const done = await cloneTheme(author, themeName);

    if(done) {
        console.log(`${themeName} theme was installed successfully.`)
    } else {
        console.error(`${themeName} theme was not installed successfully. Try again.`)
    }
}

async function start() {
    const pluginortheme = await readLineAsync("Press 't' to install theme or 'p' to install plugin...");

    if(pluginortheme == 't') {
        const neworexisting = await readLineAsync("Press 'n' to install a new theme or 'i' for existing...");
        if(neworexisting == 'n') {
            newTheme()
        } else {
            installTheme()
        }
    } else {
        const neworexisting = await readLineAsync("Press 'n' to install a new plugin or 'i' for existing...");
        if(neworexisting == 'n') {
            newPlugin()
        } else {
            install()
        }
    }
}

start()