/*!
 * Theia Sticky Sidebar v1.7.0
 * https://github.com/WeCodePixels/theia-sticky-sidebar
 *
 * Glues your website's sidebars, making them permanently visible while scrolling.
 *
 * Copyright 2013-2016 WeCodePixels and other contributors
 * Released under the MIT license
 */
(function(a){a.fn.theiaStickySidebar=function(d){var f={containerSelector:"",additionalMarginTop:0,additionalMarginBottom:0,updateSidebarHeight:true,minWidth:0,disableOnResponsiveLayouts:true,sidebarBehavior:"modern",defaultPosition:"relative",namespace:"TSS"};
d=a.extend(f,d);d.additionalMarginTop=parseInt(d.additionalMarginTop)||0;d.additionalMarginBottom=parseInt(d.additionalMarginBottom)||0;e(d,this);function e(i,h){var j=b(i,h);
if(!j){console.log("TSS: Body width smaller than options.minWidth. Init is delayed.");a(document).on("scroll."+i.namespace,function(l,k){return function(m){var n=b(l,k);
if(n){a(this).unbind(m);}};}(i,h));a(window).on("resize."+i.namespace,function(l,k){return function(m){var n=b(l,k);if(n){a(this).unbind(m);}};}(i,h));
}}function b(i,h){if(i.initialized===true){return true;}if(a("body").width()<i.minWidth){return false;}g(i,h);return true;}function g(i,h){i.initialized=true;
var j=a("#theia-sticky-sidebar-stylesheet-"+i.namespace);if(j.length===0){a("head").append(a('<style id="theia-sticky-sidebar-stylesheet-'+i.namespace+'">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'));
}h.each(function(){var q={};q.sidebar=a(this);q.options=i||{};q.container=a(q.options.containerSelector);if(q.container.length==0){q.container=q.sidebar.parent();
}q.sidebar.css({position:q.options.defaultPosition,overflow:"visible","-webkit-box-sizing":"border-box","-moz-box-sizing":"border-box","box-sizing":"border-box"});
q.stickySidebar=q.sidebar.find(".theiaStickySidebar");if(q.stickySidebar.length==0){var m=/(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;q.sidebar.find("script").filter(function(r,o){return o.type.length===0||o.type.match(m);
}).remove();q.stickySidebar=a("<div>").addClass("theiaStickySidebar").append(q.sidebar.children());q.sidebar.append(q.stickySidebar);}q.marginBottom=parseInt(q.sidebar.css("margin-bottom"));
q.paddingTop=parseInt(q.sidebar.css("padding-top"));q.paddingBottom=parseInt(q.sidebar.css("padding-bottom"));var l=q.stickySidebar.offset().top;var p=q.stickySidebar.outerHeight();
q.stickySidebar.css("padding-top",1);q.stickySidebar.css("padding-bottom",1);l-=q.stickySidebar.offset().top;p=q.stickySidebar.outerHeight()-p-l;if(l==0){q.stickySidebar.css("padding-top",0);
q.stickySidebarPaddingTop=0;}else{q.stickySidebarPaddingTop=1;}if(p==0){q.stickySidebar.css("padding-bottom",0);q.stickySidebarPaddingBottom=0;}else{q.stickySidebarPaddingBottom=1;
}q.previousScrollTop=null;q.fixedScrollTop=0;k();q.onScroll=function(z){if(!z.stickySidebar.is(":visible")){return;}if(a("body").width()<z.options.minWidth){k();
return;}if(z.options.disableOnResponsiveLayouts){var H=z.sidebar.outerWidth(z.sidebar.css("float")=="none");if(H+50>z.container.width()){k();return;}}var r=a(document).scrollTop();
var I="static";if(r>=z.sidebar.offset().top+(z.paddingTop-z.options.additionalMarginTop)){var E=z.paddingTop+i.additionalMarginTop;var G=z.paddingBottom+z.marginBottom+i.additionalMarginBottom;
var w=z.sidebar.offset().top;var F=z.sidebar.offset().top+n(z.container);var C=0+i.additionalMarginTop;var A;var D=(z.stickySidebar.outerHeight()+E+G)<a(window).height();
if(D){A=C+z.stickySidebar.outerHeight();}else{A=a(window).height()-z.marginBottom-z.paddingBottom-i.additionalMarginBottom;}var t=w-r+z.paddingTop;var y=F-r-z.paddingBottom-z.marginBottom;
var x=z.stickySidebar.offset().top-r;var s=z.previousScrollTop-r;if(z.stickySidebar.css("position")=="fixed"){if(z.options.sidebarBehavior=="modern"){x+=s;
}}if(z.options.sidebarBehavior=="stick-to-top"){x=i.additionalMarginTop;}if(z.options.sidebarBehavior=="stick-to-bottom"){x=A-z.stickySidebar.outerHeight();
}if(s>0){x=Math.min(x,C);}else{x=Math.max(x,A-z.stickySidebar.outerHeight());}x=Math.max(x,t);x=Math.min(x,y-z.stickySidebar.outerHeight());var u=z.container.height()==z.stickySidebar.outerHeight();
if(!u&&x==C){I="fixed";}else{if(!u&&x==A-z.stickySidebar.outerHeight()){I="fixed";}else{if(r+x-z.sidebar.offset().top-z.paddingTop<=i.additionalMarginTop){I="static";
}else{I="absolute";}}}}if(I=="fixed"){var B=a(document).scrollLeft();z.stickySidebar.css({position:"fixed",width:c(z.stickySidebar)+"px",transform:"translateY("+x+"px)",left:(z.sidebar.offset().left+parseInt(z.sidebar.css("padding-left"))-B)+"px",top:"0px"});
}else{if(I=="absolute"){var v={};if(z.stickySidebar.css("position")!="absolute"){v.position="absolute";v.transform="translateY("+(r+x-z.sidebar.offset().top-z.stickySidebarPaddingTop-z.stickySidebarPaddingBottom)+"px)";
v.top="0px";}v.width=c(z.stickySidebar)+"px";v.left="";z.stickySidebar.css(v);}else{if(I=="static"){k();}}}if(I!="static"){if(z.options.updateSidebarHeight==true){z.sidebar.css({"min-height":z.stickySidebar.outerHeight()+z.stickySidebar.offset().top-z.sidebar.offset().top+z.paddingBottom});
}}z.previousScrollTop=r;};q.onScroll(q);a(document).on("scroll."+q.options.namespace,function(r){return function(){r.onScroll(r);};}(q));a(window).on("resize."+q.options.namespace,function(r){return function(){r.stickySidebar.css({position:"static"});
r.onScroll(r);};}(q));if(typeof ResizeSensor!=="undefined"){new ResizeSensor(q.stickySidebar[0],function(r){return function(){r.onScroll(r);};}(q));}function k(){q.fixedScrollTop=0;
q.sidebar.css({"min-height":"1px"});q.stickySidebar.css({position:"static",width:"",transform:"none"});}function n(r){var o=r.height();r.children().each(function(){o=Math.max(o,a(this).height());
});return o;}});}function c(h){var i;try{i=h[0].getBoundingClientRect().width;}catch(j){}if(typeof i==="undefined"){i=h.width();}return i;}return this;
};})(jQuery);