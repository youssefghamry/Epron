/**
 * Copyright Marc J. Schmidt. See the LICENSE file at the top-level
 * directory of this distribution and at
 * https://github.com/marcj/css-element-queries/blob/master/LICENSE.
 */
(function(){(function(a,b){if(typeof define==="function"&&define.amd){define(b);}else{if(typeof exports==="object"){module.exports=b();}else{a.ResizeSensor=b();
}}}(typeof window!=="undefined"?window:this,function(){if(typeof window==="undefined"){return null;}var d=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||function(e){return window.setTimeout(e,20);
};function c(h,l){var g=Object.prototype.toString.call(h);var k=("[object Array]"===g||("[object NodeList]"===g)||("[object HTMLCollection]"===g)||("[object Object]"===g)||("undefined"!==typeof jQuery&&h instanceof jQuery)||("undefined"!==typeof Elements&&h instanceof Elements));
var f=0,e=h.length;if(k){for(;f<e;f++){l(h[f]);}}else{l(h);}}function a(e){if(!e.getBoundingClientRect){return{width:e.offsetWidth,height:e.offsetHeight};
}var f=e.getBoundingClientRect();return{width:Math.round(f.width),height:Math.round(f.height)};}var b=function(e,h){function g(){var m=[];this.add=function(i){m.push(i);
};var l,k;this.call=function(){for(l=0,k=m.length;l<k;l++){m[l].call();}};this.remove=function(j){var i=[];for(l=0,k=m.length;l<k;l++){if(m[l]!==j){i.push(m[l]);
}}m=i;};this.length=function(){return m.length;};}function f(j,x){if(!j){return;}if(j.resizedAttached){j.resizedAttached.add(x);return;}j.resizedAttached=new g();
j.resizedAttached.add(x);j.resizeSensor=document.createElement("div");j.resizeSensor.dir="ltr";j.resizeSensor.className="resize-sensor";var v="position: absolute; left: -10px; top: -10px; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;";
var l="position: absolute; left: 0; top: 0; transition: 0s;";j.resizeSensor.style.cssText=v;j.resizeSensor.innerHTML='<div class="resize-sensor-expand" style="'+v+'"><div style="'+l+'"></div></div><div class="resize-sensor-shrink" style="'+v+'"><div style="'+l+' width: 200%; height: 200%"></div></div>';
j.appendChild(j.resizeSensor);var A=window.getComputedStyle(j).getPropertyValue("position");if("absolute"!==A&&"relative"!==A&&"fixed"!==A){j.style.position="relative";
}var i=j.resizeSensor.childNodes[0];var r=i.childNodes[0];var q=j.resizeSensor.childNodes[1];var n,z,p,u;var s=a(j);var k=s.width;var w=s.height;var y=function(){var B=j.offsetWidth===0&&j.offsetHeight===0;
if(B){var C=j.style.display;j.style.display="block";}r.style.width="100000px";r.style.height="100000px";i.scrollLeft=100000;i.scrollTop=100000;q.scrollLeft=100000;
q.scrollTop=100000;if(B){j.style.display=C;}};j.resizeSensor.resetSensor=y;var m=function(){z=0;if(!n){return;}k=p;w=u;if(j.resizedAttached){j.resizedAttached.call();
}};var o=function(){var C=a(j);var D=C.width;var B=C.height;n=D!=k||B!=w;if(n&&!z){z=d(m);}y();};var t=function(D,C,B){if(D.attachEvent){D.attachEvent("on"+C,B);
}else{D.addEventListener(C,B);}};t(i,"scroll",o);t(q,"scroll",o);d(y);}c(e,function(i){f(i,h);});this.detach=function(i){b.detach(e,i);};this.reset=function(){e.resizeSensor.resetSensor();
};};b.reset=function(e,f){c(e,function(g){g.resizeSensor.resetSensor();});};b.detach=function(e,f){c(e,function(g){if(!g){return;}if(g.resizedAttached&&typeof f==="function"){g.resizedAttached.remove(f);
if(g.resizedAttached.length()){return;}}if(g.resizeSensor){if(g.contains(g.resizeSensor)){g.removeChild(g.resizeSensor);}delete g.resizeSensor;delete g.resizedAttached;
}});};return b;}));})();