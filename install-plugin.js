const shell = require('shelljs')
if(process.env.npm_config_plugin == undefined) {
    throw new Error('Please specify the plugin to install npm run install-plugin --plugin=<plugin name>');
}
//clone the repos
shell.cd('./Public/Modules')
shell.exec(`git clone https://github.com/kinsokafor/${process.env.npm_config_plugin} ${process.env.npm_config_plugin}`)

shell.cd('../../EvoJsDev/Modules')
shell.exec(`git clone https://github.com/kinsokafor/${process.env.npm_config_plugin}.js ${process.env.npm_config_plugin}`)

//merge config file

//run the install.php