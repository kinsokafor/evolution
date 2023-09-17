import axios from "axios";
import { Base64 } from "./base64";

export const randomId = (length) => {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
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
      console.log("Pos 3: "+pos3, "Pos 4: "+pos4)
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
    var ret = 0;
    await axios.get("https://api.exchangerate.host/latest").then(response => {
        if(response.status == 200) {
            if(response.data.rates.hasOwnProperty(base) && response.data.rates.hasOwnProperty(currency)) {
                var fetchedBase = response.data.base;
                var rateToBase = response.data.rates[base];
                var rateToCurrency = response.data.rates[currency];
                ret = (rateToCurrency/rateToBase) * amount;
                ret = Math.round((ret + Number.EPSILON) * 100) / 100
            }
        }
    })
    return ret;
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
                fullname += data.surname+" " ?? ""
                break;
            case "M":
                fullname += data.middle_name+" " ?? ""
                break;
            case "T":
                fullname += data.title+" " ?? ""
                break;
            case "O":
                fullname += data.other_names+" " ?? ""
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

export const findByDottedIndex = (i, obj) => {
	const arr = i.split('.')
    return arr.reduce((a, b) => a[b], obj)
}