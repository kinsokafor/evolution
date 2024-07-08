import axios from "axios";
import { Base64 } from "./base64";
import { Request } from "./request";
import { Records } from "./records";
import _ from "lodash";
import male from '../components/images/male_avatar.svg'
import female from '../components/images/female_avatar.svg'

export const randomId = (length, characters = "") => {
    var result = '';
    if(characters == "")
        characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

export const dynamicSort = (property) => {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

export const dynamicSortMultiple = () => {
    /*
     * save the arguments object as it will be overwritten
     * note that arguments object is an array-like object
     * consisting of the names of the properties to sort by
     */
    var props = arguments;
    return function (obj1, obj2) {
        var i = 0, result = 0, numberOfProperties = props.length;
        /* try getting a different result from 0 (equal)
         * as long as we have extra properties to compare
         */
        while(result === 0 && i < numberOfProperties) {
            result = dynamicSort(props[i])(obj1, obj2);
            i++;
        }
        return result;
    }
}

export const arrayIntersect = (A, B) => {
    return A.filter(x => B.includes(x));
}

export const arrayDiff = (A, B) => {
    return A.filter(x => !B.includes(x));
}

export const nonce = () => {
    return appData().nonce
}

export const appData = () => {
    return JSON.parse(Base64.decode(document.getElementById("app-data").getAttribute("content"))) ?? [];
}

export const getCookie = (name) => {
    function escape(s) { return s.replace(/([.*+?\^$(){}|\[\]\/\\])/g, '\\$1'); }
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}

export const dragElement = (elmnt, handle = false) => {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (handle) {
      // if present, the header is where you move the DIV from:
      handle.onmousedown = dragMouseDown;
    } else {
      // otherwise, move the DIV from anywhere inside the DIV:
      elmnt.onmousedown = dragMouseDown;
    }
  
    function dragMouseDown(e) {
      e = e || window.event;
      e.preventDefault();
      // get the mouse cursor position at startup:
      pos3 = e.clientX;
      pos4 = e.clientY;
      document.onmouseup = closeDragElement;
      // call a function whenever the cursor moves:
      document.onmousemove = elementDrag;
    }
  
    function elementDrag(e) {
      e = e || window.event;
      e.preventDefault();
      var x = e.clientX //+ pos1;
      var y = e.clientY //+ pos2;
      // calculate the new cursor position:
      pos1 = (pos3 - x) * 25;
      pos2 = (pos4 - y) * 5;
      pos3 = x;
      pos4 = y;

      // set the element's new position:
      elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
      elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }
  
    function closeDragElement() {
      // stop moving when mouse button is released:
      document.onmouseup = null;
      document.onmousemove = null;
    }
}

export const currencyConverter = async (currency, base = "USD", amount = 1) => {

    const convert = (data, currency, base) => {
        if(base in data && currency in data) {
            var rateToBase = data[base];
            var rateToCurrency = data[currency];
            const ret = (rateToCurrency/rateToBase) * amount;
            // return (((ret + Number.EPSILON) * 100).toFixed(6) / 100).toFixed(2)
            return ret;
        } else return "NA"
    }

    const getFromServer = async (currency, base, endPoint, rateKey, refreshes) => {
        return await axios.get(endPoint).then(response => {
            if(response.status == 200) {
                if(rateKey in response.data) {
                    var a = new Date(); 
                    a.setHours(a.getHours()+parseInt(refreshes));
                    (new Records).update("exchange_rates", {rates: response.data[rateKey], expiry: a.getTime()});
                    return convert(response.data[rateKey], currency, base)
                } else return "NA"
            } else return "failed"
        })
    }
    
    return await (new Request).post(process.env.EVO_API_URL + "/api/config/all").then(async (r) => {
        let endPoint = r.data.data.currency.endPoint ?? "https://api.exchangerate.host/latest"
        const apiKey = r.data.data.currency.apiKey ?? ""
        const rateKey = r.data.data.currency.rateKey ?? "rates"
        const refreshes = r.data.data.currency.refreshes ?? 1
        endPoint = endPoint.replace("{key}", apiKey).replace("{base}", base)
        return await (new Records).get("exchange_rates").then(r => {
            var a = new Date();
            if(a.getTime() >= r.data.expiry) {
                return getFromServer(currency, base, endPoint, rateKey, refreshes);
            }
            return convert(r.data.rates, currency, base)
        }).catch(async (e) => {
            return getFromServer(currency, base, endPoint, rateKey, refreshes);
        })
        
    })  
}

export const isEmpty = (obj) => {
    return Object.keys(obj).length === 0;
}

export const shaEncode = (input) => {
    const sha1 = require('js-sha1')
    return sha1(input)
} 

export const objectMap = (object, mapFn) => {
    return Object.keys(object).reduce(function(result, key) {
      result[key] = mapFn(object[key])
      return result
    }, {})
}

export const objectFilter = (obj, callback) => {
    return Object.fromEntries(Object.entries(obj).filter(([key, val]) => callback(key, val)));
}

export const ordinalSuffixOf = (i) => {
    var j = i % 10,
        k = i % 100;
    if (j == 1 && k != 11) {
        return i + "st";
    }
    if (j == 2 && k != 12) {
        return i + "nd";
    }
    if (j == 3 && k != 13) {
        return i + "rd";
    }
    return i + "th";
}

export const getFullname = (data, format = 'SMO') => {
    format = format.split("")
    let fullname = ""
    format.forEach(i => {
        switch(i) {
            case "S":
                fullname += (data.surname ?? "") + " "
                break;
            case "M":
                fullname += (data.middle_name ?? "") + " "
                break;
            case "T":
                fullname += (data.title ?? "") + " "
                break;
            case "O":
                fullname += (data.other_names ?? "") + " "
                break;
        }
    })
    return fullname.trim().replace("  ", " ")
}

export const imageExists = (imageSrc, good, bad) => {
    var img = new Image();
    img.src = imageSrc;
    img.onload = good; 
    img.onerror = bad; 
}

export const getProfilePicture = (data) => {
    const getTemp = () => {
        switch(data?.gender) {
            case "female":
            case "Female":
            return female
                break;
            default:
            return male
                break;
        }
    }
    if(data?.profile_picture == undefined) {
        return new Promise((resolve, reject) => {
            resolve(getTemp())
        })
    }
    const profilePicture = data?.profile_picture.charAt(0) == "/" ?  data?.profile_picture : (String(data?.profile_picture).search("http") == -1 ? "/"+data?.profile_picture : data?.profile_picture);
    return new Promise((resolve, reject) => {
        const img = new Image()
        img.src = profilePicture
        img.onload = () => {
            resolve(profilePicture)
        }
        img.onerror = () => {
            resolve(getTemp())
        }
    })
}

export const findByDottedIndex = (i, obj) => {
	const arr = i.split('.')
    if(!(arr[0] in obj)) return null
    return arr.reduce((a, b) =>  {
        return a[b]
    }, obj)
}

export const clickToCopyText = (textElementId) => {
    return new Promise((resolve, reject) => {
        var text = document.getElementById(textElementId).innerHTML;
        navigator.clipboard.writeText(text);
        resolve("Copied")
    })
}

export const numberWithCommas = (x) => {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}

export const storeGetter = (state, data, loader, params = {}, exclude = []) => {
    let tempParams = {...params};
    exclude.forEach(i => {
        delete tempParams[i]
    })
    if (!state.fetching || !_.isEqual(tempParams, state.lastParams)) {
        if(state.lastTimeOut != null) {
            clearTimeout(state.lastTimeOut);
        }
        state.lastParams = tempParams;
        loader(tempParams)
    }
    const r = data.filter(i => {
        let test = true;
        for (var k in params) {
            if(typeof params[k] == "string") {
                test = new RegExp('^' + params[k].replace(/\%/g, '.*') + '$').test(i[k])
            }
            else if (k in i && params[k] != i[k]) test = false
        }
        return test
    })
    return r
}

export const titleCase = (s) =>
  s.replace(/^_*(.)|_+(.)/g, (s, c, d) => c ? c.toUpperCase() : ' ' + d.toUpperCase())

export const timeStampToDate = (timestamp) => {
    const d = new Date(parseInt(timestamp) * 1000);
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return `${d.getDate()} ${months[d.getMonth()]}, ${d.getFullYear()}`
}

export const linkParams = (href = null) => {
    if(href == null) href = window.location.href
    const paramsStrings = href.split("?")[1] ?? ""
    const paramsArr = paramsStrings.split("&");
    let result = {};
    paramsArr.forEach(i => {
        let temp = i.split("=")
        if(temp[0] != undefined && temp[1] != undefined) {
            result[temp[0]] = decodeURIComponent(temp[1])
        }
    })
    return result;
}