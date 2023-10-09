const shell = require('shelljs')
const path = require('path');
const {mergeConfig} = require('./evo.config');

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
const [author, pluginName] = plugin;
//clone the repos
shell.cd('./Public/Modules')
shell.exec(`git clone https://github.com/${author}/${pluginName} ${pluginName}`)

shell.cd('../../EvoJsDev/Modules')
shell.exec(`git clone https://github.com/${author}/${pluginName}.js ${pluginName}`)

//merge config file
const pluginConfig = path.resolve(__dirname, `./EvoJsDev/Modules/${pluginName}/config.json`)
mergeConfig(pluginConfig)

//run the install.php