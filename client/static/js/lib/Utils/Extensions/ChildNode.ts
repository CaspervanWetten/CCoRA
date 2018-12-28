interface HTMLElement {
    replace(node : HTMLElement) : void;
}

HTMLElement.prototype.replace = function(element : HTMLElement) {
    console.log(this.outerHTML);
    console.log(element.outerHTML);
    this.outerHTML = element.outerHTML;
    // 'use-strict'; // For safari, and IE > 10
    // var parent = this.parentNode,
    //     i = arguments.length,
    //     firstIsNode = +(parent && typeof Ele === 'object');
    // if (!parent) return;
    
    // while (i-- > firstIsNode){
    //   if (parent && typeof arguments[i] !== 'object'){
    //     arguments[i] = document.createTextNode(arguments[i]);
    //   } if (!parent && arguments[i].parentNode){
    //     arguments[i].parentNode.removeChild(arguments[i]);
    //     continue;
    //   }
    //   parent.insertBefore(this.previousSibling, arguments[i]);
    // }
    // if (firstIsNode) parent.replaceChild(Ele, this);
}
