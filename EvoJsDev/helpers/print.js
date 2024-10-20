export class Print {
    constructor(option) {
      this.standards = {
        strict: 'strict',
        loose: 'loose',
        html5: 'html5'
      };
      this.counter = 0;
      this.settings = {
        standard: this.standards.html5,
        extraHead: '', 
        extraCss: '', 
        popTitle: '', 
        endCallback: () => {}, 
        el: '' 
      };
      Object.assign(this.settings, option);
      this.init();
    };
    init() {
      this.counter++;
      this.settings.id = `printArea_${this.counter}`;
      let box = document.getElementById(this.settings.id);
      if (box) {
        box.parentNode.removeChild(box);
      }
      let PrintAreaWindow = this.getPrintWindow(); 
      this.write(PrintAreaWindow.doc); 
      this.print(PrintAreaWindow);
      this.settings.endCallback();
    };
    print(PAWindow) {
      let paWindow = PAWindow.win;
      paWindow.onload = () => {
        paWindow.focus();
        paWindow.print();
      };
    };
    write(PADocument, $ele) {
      PADocument.open();
      PADocument.write(`${this.docType()}<html>${this.getHead()}${this.getBody()}</html>`);
      PADocument.close();
    };
    docType() {
      if (this.settings.standard === this.standards.html5) {
        return '<!DOCTYPE html>';
      }
      var transitional = this.settings.standard === this.standards.loose ? ' Transitional' : '';
      var dtd = this.settings.standard === this.standards.loose ? 'loose' : 'strict';
      
      return `<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01${transitional}//EN" "http://www.w3.org/TR/html4/${dtd}.dtd">`;
    };
    getHead() {
      let extraHead = '';
      let links = '';
      let style = '';
      if (this.settings.extraHead) {
        this.settings.extraHead.replace(/([^,]+)/g, function(m) {
          extraHead += m;
        });
      }
      document.querySelectorAll('link').forEach((item, i) => {
        if (item.href.indexOf('.css') >= 0) {
          links += `<link type="text/css" rel="stylesheet" href="${item.href}" >`;
        }
      });
      for (let i = 0 ; i < document.styleSheets.length; i++) {
        try {
          if (document.styleSheets[i].cssRules || document.styleSheets[i].rules) {
            let rules = document.styleSheets[i].cssRules || document.styleSheets[i].rules;
            for (let b = 0 ; b < rules.length; b++) {
              style += rules[b].cssText;
            }
          }
        }
        catch(err) {
          console.warn("CORS CSS Omitted: ", document.styleSheets[i])
        }
        
      }
      
      if (this.settings.extraCss) {
        this.settings.extraCss.replace(/([^,\s]+)/g, function(m) {
          links += `<link type="text/css" rel="stylesheet" href="${m}">`;
        });
      }
      
      return `<head><title>${this.settings.popTitle}</title>${extraHead}${links}<style type="text/css">${style}</style></head>`;
    };
    getBody() {
      let ele = this.getFormData(document.querySelector(this.settings.el));
      let htm = ele.outerHTML;
      return '<body>' + htm + '</body>';
    };
    getFormData(ele) {
      let copy = ele.cloneNode(true);
      let copiedElements = copy.querySelectorAll('input,select,textarea,canvas');
      let originalElements = document.querySelectorAll(`${this.settings.el} input,${this.settings.el} select,${this.settings.el} textarea,${this.settings.el} canvas`);
    
      copiedElements.forEach((copiedElement, i) => {
        let originalElement = originalElements[i];
        let tagName = copiedElement.tagName.toLowerCase();
    
        if (tagName === 'canvas') {
          // Handle canvas elements (e.g., multiple QR codes)
          let img = document.createElement('img');
          img.src = originalElement.toDataURL(); // Convert canvas to image
          copiedElement.parentNode.replaceChild(img, copiedElement); // Replace canvas with image
        } else if (tagName === 'input') {
          let typeInput = originalElement.getAttribute('type');
          
          if (typeInput === 'radio' || typeInput === 'checkbox') {
            copiedElement.setAttribute('checked', originalElement.checked);
          } else if (typeInput === 'text' || typeInput === '') {
            copiedElement.value = originalElement.value;
            copiedElement.setAttribute('value', originalElement.value);
          }
        } else if (tagName === 'select') {
          copiedElement.querySelectorAll('option').forEach((option, b) => {
            if (originalElement.options[b].selected) {
              option.setAttribute('selected', true);
            } else {
              option.removeAttribute('selected');
            }
          });
        } else if (tagName === 'textarea') {
          copiedElement.value = originalElement.value;
          copiedElement.setAttribute('value', originalElement.value);
        }
      });
    
      return copy;
    };
    
    getPrintWindow() {
      var f = this.Iframe();
      return {
        win: f.contentWindow || f,
        doc: f.doc
      };
    };
    Iframe() {
      let frameId = this.settings.id;
      let iframe;
      
      try {
        iframe = document.createElement('iframe');
        document.body.appendChild(iframe);
        iframe.style.border = '0px';
        iframe.style.position = 'absolute';
        iframe.style.width = '0px';
        iframe.style.height = '0px';
        iframe.style.right = '0px';
        iframe.style.top = '0px';
        iframe.setAttribute('id', frameId);
        iframe.setAttribute('src', new Date().getTime());
        iframe.doc = null;
        iframe.doc = iframe.contentDocument ? iframe.contentDocument : (iframe.contentWindow ? iframe.contentWindow.document : iframe.document);
      } catch (e) {
        throw new Error(e + '. iframes may not be supported in this browser.');
      }
      
      if (iframe.doc == null) {
        throw new Error('Cannot find document.');
      }
      
      return iframe;
    };
}