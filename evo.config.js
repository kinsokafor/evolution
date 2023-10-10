const config = require('./config.json');
const path = require('path');
const editJsonFile = require("edit-json-file");

const resolveEntryPointVar = (moduleName, moduleDetail) => {
    if( typeof(moduleDetail) == "string" ) {
        return {entryVar: moduleName, active: true}
    }
    if( typeof(moduleDetail) == "object" ) {
        if (Object.hasOwnProperty.call(moduleDetail, "entry")) {
            return {entryVar: moduleDetail.entry, active: moduleDetail.active}
        }
        return {entryVar: moduleName, active: true}
    }
}

const isObject = (item) => {
    return (item && typeof item === 'object' && !Array.isArray(item));
}

/**
 * Deep merge two objects.
 * @param target
 * @param ...sources
 */
const mergeDeep = (target, ...sources) => {
    if (!sources.length) return target;
    const source = sources.shift();

    if (isObject(target) && isObject(source)) {
        for (const key in source) {
            if (isObject(source[key])) {
                if (!target[key]) Object.assign(target, { [key]: {} });
                mergeDeep(target[key], source[key]);
            } else {
                Object.assign(target, { [key]: source[key] });
            }
        }
    }

    return mergeDeep(target, ...sources);
}

const mergeConfig = (config) => {
    let file = editJsonFile("./config.json");
    for(var data in config) {
        const oldValue = file.get(data);
        if(oldValue == undefined) {
            file.set(data, config[data]);
        } else {
            if(isObject(oldValue)) {
                file.set(data, mergeDeep(oldValue, config[data]));
            } else {
                file.set(data, config[data]);
            }
        }  
    }
    file.save();
}

const buildTempConfig  = (newConfigLocation, currentConfig = {}) => {
    const config = require(newConfigLocation);
    for(var data in config) {
        if(typeof(config[data]) == 'object' && 
            Object.hasOwnProperty.call(currentConfig, data)) {
            if(Array.isArray(config[data])) {
                currentConfig[data] = [...currentConfig[data], ...config[data]];
            } else {
                currentConfig[data] = {...currentConfig[data], ...config[data]};
            }
        } else {
            currentConfig[data] = config[data]
        }
    }
    return currentConfig;
}

exports.entryPoints = () => {
    var entry = {};
    var currentConfig = {};
    for (const moduleName in config.modules) {
        if (Object.hasOwnProperty.call(config.modules, moduleName)) {
            const {entryVar, active} = resolveEntryPointVar(moduleName, config.modules[moduleName]);
            if(active) {
                if(typeof(entryVar) == "object") {
                    entryVar.forEach(name => {
                        entry[name] = path.resolve(__dirname, './EvoJsDev/Modules/'+moduleName+'/'+name+'.js')
                    });
                }
                else if(typeof(entryVar) == "string") {
                    entry[entryVar] = path.resolve(__dirname, './EvoJsDev/Modules/'+moduleName+'/'+entryVar+'.js')
                }
                currentConfig = buildTempConfig(path.resolve(__dirname, './EvoJsDev/Modules/'+moduleName+'/config.json'), currentConfig);
            }
        }
    }
    mergeConfig(currentConfig);
    return entry;
}

exports.mergeConfig = mergeConfig;