/*
 * Copyright (c) 2009 Simo Kinnunen.
 * Licensed under the MIT license.
 *
 * @version 1.09
 */
var Cufon=(function(){var m=function(){return m.replace.apply(null,arguments)};var x=m.DOM={ready:(function(){var C=false,E={loaded:1,complete:1};var B=[],D=function(){if(C){return}C=true;for(var F;F=B.shift();F()){}};if(document.addEventListener){document.addEventListener("DOMContentLoaded",D,false);window.addEventListener("pageshow",D,false)}if(!window.opera&&document.readyState){(function(){E[document.readyState]?D():setTimeout(arguments.callee,10)})()}if(document.readyState&&document.createStyleSheet){(function(){try{document.body.doScroll("left");D()}catch(F){setTimeout(arguments.callee,1)}})()}q(window,"load",D);return function(F){if(!arguments.length){D()}else{C?F():B.push(F)}}})(),root:function(){return document.documentElement||document.body}};var n=m.CSS={Size:function(C,B){this.value=parseFloat(C);this.unit=String(C).match(/[a-z%]*$/)[0]||"px";this.convert=function(D){return D/B*this.value};this.convertFrom=function(D){return D/this.value*B};this.toString=function(){return this.value+this.unit}},addClass:function(C,B){var D=C.className;C.className=D+(D&&" ")+B;return C},color:j(function(C){var B={};B.color=C.replace(/^rgba\((.*?),\s*([\d.]+)\)/,function(E,D,F){B.opacity=parseFloat(F);return"rgb("+D+")"});return B}),fontStretch:j(function(B){if(typeof B=="number"){return B}if(/%$/.test(B)){return parseFloat(B)/100}return{"ultra-condensed":0.5,"extra-condensed":0.625,condensed:0.75,"semi-condensed":0.875,"semi-expanded":1.125,expanded:1.25,"extra-expanded":1.5,"ultra-expanded":2}[B]||1}),getStyle:function(C){var B=document.defaultView;if(B&&B.getComputedStyle){return new a(B.getComputedStyle(C,null))}if(C.currentStyle){return new a(C.currentStyle)}return new a(C.style)},gradient:j(function(F){var G={id:F,type:F.match(/^-([a-z]+)-gradient\(/)[1],stops:[]},C=F.substr(F.indexOf("(")).match(/([\d.]+=)?(#[a-f0-9]+|[a-z]+\(.*?\)|[a-z]+)/ig);for(var E=0,B=C.length,D;E<B;++E){D=C[E].split("=",2).reverse();G.stops.push([D[1]||E/(B-1),D[0]])}return G}),quotedList:j(function(E){var D=[],C=/\s*((["'])([\s\S]*?[^\\])\2|[^,]+)\s*/g,B;while(B=C.exec(E)){D.push(B[3]||B[1])}return D}),recognizesMedia:j(function(G){var E=document.createElement("style"),D,C,B;E.type="text/css";E.media=G;try{E.appendChild(document.createTextNode("/**/"))}catch(F){}C=g("head")[0];C.insertBefore(E,C.firstChild);D=(E.sheet||E.styleSheet);B=D&&!D.disabled;C.removeChild(E);return B}),removeClass:function(D,C){var B=RegExp("(?:^|\\s+)"+C+"(?=\\s|$)","g");D.className=D.className.replace(B,"");return D},supports:function(D,C){var B=document.createElement("span").style;if(B[D]===undefined){return false}B[D]=C;return B[D]===C},textAlign:function(E,D,B,C){if(D.get("textAlign")=="right"){if(B>0){E=" "+E}}else{if(B<C-1){E+=" "}}return E},textShadow:j(function(F){if(F=="none"){return null}var E=[],G={},B,C=0;var D=/(#[a-f0-9]+|[a-z]+\(.*?\)|[a-z]+)|(-?[\d.]+[a-z%]*)|,/ig;while(B=D.exec(F)){if(B[0]==","){E.push(G);G={};C=0}else{if(B[1]){G.color=B[1]}else{G[["offX","offY","blur"][C++]]=B[2]}}}E.push(G);return E}),textTransform:(function(){var B={uppercase:function(C){return C.toUpperCase()},lowercase:function(C){return C.toLowerCase()},capitalize:function(C){return C.replace(/\b./g,function(D){return D.toUpperCase()})}};return function(E,D){var C=B[D.get("textTransform")];return C?C(E):E}})(),whiteSpace:(function(){var D={inline:1,"inline-block":1,"run-in":1};var C=/^\s+/,B=/\s+$/;return function(H,F,G,E){if(E){if(E.nodeName.toLowerCase()=="br"){H=H.replace(C,"")}}if(D[F.get("display")]){return H}if(!G.previousSibling){H=H.replace(C,"")}if(!G.nextSibling){H=H.replace(B,"")}return H}})()};n.ready=(function(){var B=!n.recognizesMedia("all"),E=false;var D=[],H=function(){B=true;for(var K;K=D.shift();K()){}};var I=g("link"),J=g("style");function C(K){return K.disabled||G(K.sheet,K.media||"screen")}function G(M,P){if(!n.recognizesMedia(P||"all")){return true}if(!M||M.disabled){return false}try{var Q=M.cssRules,O;if(Q){search:for(var L=0,K=Q.length;O=Q[L],L<K;++L){switch(O.type){case 2:break;case 3:if(!G(O.styleSheet,O.media.mediaText)){return false}break;default:break search}}}}catch(N){}return true}function F(){if(document.createStyleSheet){return true}var L,K;for(K=0;L=I[K];++K){if(L.rel.toLowerCase()=="stylesheet"&&!C(L)){return false}}for(K=0;L=J[K];++K){if(!C(L)){return false}}return true}x.ready(function(){if(!E){E=n.getStyle(document.body).isUsable()}if(B||(E&&F())){H()}else{setTimeout(arguments.callee,10)}});return function(K){if(B){K()}else{D.push(K)}}})();function s(D){var C=this.face=D.face,B={"\u0020":1,"\u00a0":1,"\u3000":1};this.glyphs=D.glyphs;this.w=D.w;this.baseSize=parseInt(C["units-per-em"],10);this.family=C["font-family"].toLowerCase();this.weight=C["font-weight"];this.style=C["font-style"]||"normal";this.viewBox=(function(){var F=C.bbox.split(/\s+/);var E={minX:parseInt(F[0],10),minY:parseInt(F[1],10),maxX:parseInt(F[2],10),maxY:parseInt(F[3],10)};E.width=E.maxX-E.minX;E.height=E.maxY-E.minY;E.toString=function(){return[this.minX,this.minY,this.width,this.height].join(" ")};return E})();this.ascent=-parseInt(C.ascent,10);this.descent=-parseInt(C.descent,10);this.height=-this.ascent+this.descent;this.spacing=function(L,N,E){var O=this.glyphs,M,K,G,P=[],F=0,J=-1,I=-1,H;while(H=L[++J]){M=O[H]||this.missingGlyph;if(!M){continue}if(K){F-=G=K[H]||0;P[I]-=G}F+=P[++I]=~~(M.w||this.w)+N+(B[H]?E:0);K=M.k}P.total=F;return P}}function f(){var C={},B={oblique:"italic",italic:"oblique"};this.add=function(D){(C[D.style]||(C[D.style]={}))[D.weight]=D};this.get=function(H,I){var G=C[H]||C[B[H]]||C.normal||C.italic||C.oblique;if(!G){return null}I={normal:400,bold:700}[I]||parseInt(I,10);if(G[I]){return G[I]}var E={1:1,99:0}[I%100],K=[],F,D;if(E===undefined){E=I>400}if(I==500){I=400}for(var J in G){if(!k(G,J)){continue}J=parseInt(J,10);if(!F||J<F){F=J}if(!D||J>D){D=J}K.push(J)}if(I<F){I=F}if(I>D){I=D}K.sort(function(M,L){return(E?(M>=I&&L>=I)?M<L:M>L:(M<=I&&L<=I)?M>L:M<L)?-1:1});return G[K[0]]}}function r(){function D(F,G){if(F.contains){return F.contains(G)}return F.compareDocumentPosition(G)&16}function B(G){var F=G.relatedTarget;if(!F||D(this,F)){return}C(this,G.type=="mouseover")}function E(F){C(this,F.type=="mouseenter")}function C(F,G){setTimeout(function(){var H=d.get(F).options;m.replace(F,G?h(H,H.hover):H,true)},10)}this.attach=function(F){if(F.onmouseenter===undefined){q(F,"mouseover",B);q(F,"mouseout",B)}else{q(F,"mouseenter",E);q(F,"mouseleave",E)}}}function u(){var C=[],D={};function B(H){var E=[],G;for(var F=0;G=H[F];++F){E[F]=C[D[G]]}return E}this.add=function(F,E){D[F]=C.push(E)-1};this.repeat=function(){var E=arguments.length?B(arguments):C,F;for(var G=0;F=E[G++];){m.replace(F[0],F[1],true)}}}function A(){var D={},B=0;function C(E){return E.cufid||(E.cufid=++B)}this.get=function(E){var F=C(E);return D[F]||(D[F]={})}}function a(B){var D={},C={};this.extend=function(E){for(var F in E){if(k(E,F)){D[F]=E[F]}}return this};this.get=function(E){return D[E]!=undefined?D[E]:B[E]};this.getSize=function(F,E){return C[F]||(C[F]=new n.Size(this.get(F),E))};this.isUsable=function(){return !!B}}function q(C,B,D){if(C.addEventListener){C.addEventListener(B,D,false)}else{if(C.attachEvent){C.attachEvent("on"+B,function(){return D.call(C,window.event)})}}}function v(C,B){var D=d.get(C);if(D.options){return C}if(B.hover&&B.hoverables[C.nodeName.toLowerCase()]){b.attach(C)}D.options=B;return C}function j(B){var C={};return function(D){if(!k(C,D)){C[D]=B.apply(null,arguments)}return C[D]}}function c(F,E){var B=n.quotedList(E.get("fontFamily").toLowerCase()),D;for(var C=0;D=B[C];++C){if(i[D]){return i[D].get(E.get("fontStyle"),E.get("fontWeight"))}}return null}function g(B){return document.getElementsByTagName(B)}function k(C,B){return C.hasOwnProperty(B)}function h(){var C={},B,F;for(var E=0,D=arguments.length;B=arguments[E],E<D;++E){for(F in B){if(k(B,F)){C[F]=B[F]}}}return C}function o(E,M,C,N,F,D){var K=document.createDocumentFragment(),H;if(M===""){return K}var L=N.separate;var I=M.split(p[L]),B=(L=="words");if(B&&t){if(/^\s/.test(M)){I.unshift("")}if(/\s$/.test(M)){I.push("")}}for(var J=0,G=I.length;J<G;++J){H=z[N.engine](E,B?n.textAlign(I[J],C,J,G):I[J],C,N,F,D,J<G-1);if(H){K.appendChild(H)}}return K}function l(D,M){var C=D.nodeName.toLowerCase();if(M.ignore[C]){return}var E=!M.textless[C];var B=n.getStyle(v(D,M)).extend(M);var F=c(D,B),G,K,I,H,L,J;if(!F){return}for(G=D.firstChild;G;G=I){K=G.nodeType;I=G.nextSibling;if(E&&K==3){if(H){H.appendData(G.data);D.removeChild(G)}else{H=G}if(I){continue}}if(H){D.replaceChild(o(F,n.whiteSpace(H.data,B,H,J),B,M,G,D),H);H=null}if(K==1){if(G.firstChild){if(G.nodeName.toLowerCase()=="cufon"){z[M.engine](F,null,B,M,G,D)}else{arguments.callee(G,M)}}J=G}}}var t=" ".split(/\s+/).length==0;var d=new A();var b=new r();var y=new u();var e=false;var z={},i={},w={autoDetect:false,engine:null,forceHitArea:false,hover:false,hoverables:{a:true},ignore:{applet:1,canvas:1,col:1,colgroup:1,head:1,iframe:1,map:1,optgroup:1,option:1,script:1,select:1,style:1,textarea:1,title:1,pre:1},printable:true,selector:(window.Sizzle||(window.jQuery&&function(B){return jQuery(B)})||(window.dojo&&dojo.query)||(window.Ext&&Ext.query)||(window.YAHOO&&YAHOO.util&&YAHOO.util.Selector&&YAHOO.util.Selector.query)||(window.$$&&function(B){return $$(B)})||(window.$&&function(B){return $(B)})||(document.querySelectorAll&&function(B){return document.querySelectorAll(B)})||g),separate:"words",textless:{dl:1,html:1,ol:1,table:1,tbody:1,thead:1,tfoot:1,tr:1,ul:1},textShadow:"none"};var p={words:/\s/.test("\u00a0")?/[^\S\u00a0]+/:/\s+/,characters:"",none:/^/};m.now=function(){x.ready();return m};m.refresh=function(){y.repeat.apply(y,arguments);return m};m.registerEngine=function(C,B){if(!B){return m}z[C]=B;return m.set("engine",C)};m.registerFont=function(D){if(!D){return m}var B=new s(D),C=B.family;if(!i[C]){i[C]=new f()}i[C].add(B);return m.set("fontFamily",'"'+C+'"')};m.replace=function(D,C,B){C=h(w,C);if(!C.engine){return m}if(!e){n.addClass(x.root(),"cufon-active cufon-loading");n.ready(function(){n.addClass(n.removeClass(x.root(),"cufon-loading"),"cufon-ready")});e=true}if(C.hover){C.forceHitArea=true}if(C.autoDetect){delete C.fontFamily}if(typeof C.textShadow=="string"){C.textShadow=n.textShadow(C.textShadow)}if(typeof C.color=="string"&&/^-/.test(C.color)){C.textGradient=n.gradient(C.color)}else{delete C.textGradient}if(!B){y.add(D,arguments)}if(D.nodeType||typeof D=="string"){D=[D]}n.ready(function(){for(var F=0,E=D.length;F<E;++F){var G=D[F];if(typeof G=="string"){m.replace(C.selector(G),C,true)}else{l(G,C)}}});return m};m.set=function(B,C){w[B]=C;return m};return m})();Cufon.registerEngine("canvas",(function(){var b=document.createElement("canvas");if(!b||!b.getContext||!b.getContext.apply){return}b=null;var a=Cufon.CSS.supports("display","inline-block");var e=!a&&(document.compatMode=="BackCompat"||/frameset|transitional/i.test(document.doctype.publicId));var f=document.createElement("style");f.type="text/css";f.appendChild(document.createTextNode(("cufon{text-indent:0;}@media screen,projection{cufon{display:inline;display:inline-block;position:relative;vertical-align:middle;"+(e?"":"font-size:1px;line-height:1px;")+"}cufon cufontext{display:-moz-inline-box;display:inline-block;width:0;height:0;overflow:hidden;text-indent:-10000in;}"+(a?"cufon canvas{position:relative;}":"cufon canvas{position:absolute;}")+"}@media print{cufon{padding:0;}cufon canvas{display:none;}}").replace(/;/g,"!important;")));document.getElementsByTagName("head")[0].appendChild(f);function d(p,h){var n=0,m=0;var g=[],o=/([mrvxe])([^a-z]*)/g,k;generate:for(var j=0;k=o.exec(p);++j){var l=k[2].split(",");switch(k[1]){case"v":g[j]={m:"bezierCurveTo",a:[n+~~l[0],m+~~l[1],n+~~l[2],m+~~l[3],n+=~~l[4],m+=~~l[5]]};break;case"r":g[j]={m:"lineTo",a:[n+=~~l[0],m+=~~l[1]]};break;case"m":g[j]={m:"moveTo",a:[n=~~l[0],m=~~l[1]]};break;case"x":g[j]={m:"closePath"};break;case"e":break generate}h[g[j].m].apply(h,g[j].a)}return g}function c(m,k){for(var j=0,h=m.length;j<h;++j){var g=m[j];k[g.m].apply(k,g.a)}}return function(V,w,P,t,C,W){var k=(w===null);if(k){w=C.getAttribute("alt")}var A=V.viewBox;var m=P.getSize("fontSize",V.baseSize);var B=0,O=0,N=0,u=0;var z=t.textShadow,L=[];if(z){for(var U=z.length;U--;){var F=z[U];var K=m.convertFrom(parseFloat(F.offX));var I=m.convertFrom(parseFloat(F.offY));L[U]=[K,I];if(I<B){B=I}if(K>O){O=K}if(I>N){N=I}if(K<u){u=K}}}var Z=Cufon.CSS.textTransform(w,P).split("");var E=V.spacing(Z,~~m.convertFrom(parseFloat(P.get("letterSpacing"))||0),~~m.convertFrom(parseFloat(P.get("wordSpacing"))||0));if(!E.length){return null}var h=E.total;O+=A.width-E[E.length-1];u+=A.minX;var s,n;if(k){s=C;n=C.firstChild}else{s=document.createElement("cufon");s.className="cufon cufon-canvas";s.setAttribute("alt",w);n=document.createElement("canvas");s.appendChild(n);if(t.printable){var S=document.createElement("cufontext");S.appendChild(document.createTextNode(w));s.appendChild(S)}}var aa=s.style;var H=n.style;var j=m.convert(A.height);var Y=Math.ceil(j);var M=Y/j;var G=M*Cufon.CSS.fontStretch(P.get("fontStretch"));var J=h*G;var Q=Math.ceil(m.convert(J+O-u));var o=Math.ceil(m.convert(A.height-B+N));n.width=Q;n.height=o;H.width=Q+"px";H.height=o+"px";B+=A.minY;H.top=Math.round(m.convert(B-V.ascent))+"px";H.left=Math.round(m.convert(u))+"px";var r=Math.max(Math.ceil(m.convert(J)),0)+"px";if(a){aa.width=r;aa.height=m.convert(V.height)+"px"}else{aa.paddingLeft=r;aa.paddingBottom=(m.convert(V.height)-1)+"px"}var X=n.getContext("2d"),D=j/A.height;X.scale(D,D*M);X.translate(-u,-B);X.save();function T(){var x=V.glyphs,ab,l=-1,g=-1,y;X.scale(G,1);while(y=Z[++l]){var ab=x[Z[l]]||V.missingGlyph;if(!ab){continue}if(ab.d){X.beginPath();if(ab.code){c(ab.code,X)}else{ab.code=d("m"+ab.d,X)}X.fill()}X.translate(E[++g],0)}X.restore()}if(z){for(var U=z.length;U--;){var F=z[U];X.save();X.fillStyle=F.color;X.translate.apply(X,L[U]);T()}}var q=t.textGradient;if(q){var v=q.stops,p=X.createLinearGradient(0,A.minY,0,A.maxY);for(var U=0,R=v.length;U<R;++U){p.addColorStop.apply(p,v[U])}X.fillStyle=p}else{X.fillStyle=P.get("color")}T();return s}})());Cufon.registerEngine("vml",(function(){var e=document.namespaces;if(!e){return}e.add("cvml","urn:schemas-microsoft-com:vml");e=null;var b=document.createElement("cvml:shape");b.style.behavior="url(#default#VML)";if(!b.coordsize){return}b=null;var h=(document.documentMode||0)<8;document.write(('<style type="text/css">cufoncanvas{text-indent:0;}@media screen{cvml\\:shape,cvml\\:rect,cvml\\:fill,cvml\\:shadow{behavior:url(#default#VML);display:block;antialias:true;position:absolute;}cufoncanvas{position:absolute;text-align:left;}cufon{display:inline-block;position:relative;vertical-align:'+(h?"middle":"text-bottom")+";}cufon cufontext{position:absolute;left:-10000in;font-size:1px;}a cufon{cursor:pointer}}@media print{cufon cufoncanvas{display:none;}}</style>").replace(/;/g,"!important;"));function c(i,j){return a(i,/(?:em|ex|%)$|^[a-z-]+$/i.test(j)?"1em":j)}function a(l,m){if(m==="0"){return 0}if(/px$/i.test(m)){return parseFloat(m)}var k=l.style.left,j=l.runtimeStyle.left;l.runtimeStyle.left=l.currentStyle.left;l.style.left=m.replace("%","em");var i=l.style.pixelLeft;l.style.left=k;l.runtimeStyle.left=j;return i}function f(l,k,j,n){var i="computed"+n,m=k[i];if(isNaN(m)){m=k.get(n);k[i]=m=(m=="normal")?0:~~j.convertFrom(a(l,m))}return m}var g={};function d(p){var q=p.id;if(!g[q]){var n=p.stops,o=document.createElement("cvml:fill"),i=[];o.type="gradient";o.angle=180;o.focus="0";o.method="sigma";o.color=n[0][1];for(var m=1,l=n.length-1;m<l;++m){i.push(n[m][0]*100+"% "+n[m][1])}o.colors=i.join(",");o.color2=n[l][1];g[q]=o}return g[q]}return function(ac,G,Y,C,K,ad,W){var n=(G===null);if(n){G=K.alt}var I=ac.viewBox;var p=Y.computedFontSize||(Y.computedFontSize=new Cufon.CSS.Size(c(ad,Y.get("fontSize"))+"px",ac.baseSize));var y,q;if(n){y=K;q=K.firstChild}else{y=document.createElement("cufon");y.className="cufon cufon-vml";y.alt=G;q=document.createElement("cufoncanvas");y.appendChild(q);if(C.printable){var Z=document.createElement("cufontext");Z.appendChild(document.createTextNode(G));y.appendChild(Z)}if(!W){y.appendChild(document.createElement("cvml:shape"))}}var ai=y.style;var R=q.style;var l=p.convert(I.height),af=Math.ceil(l);var V=af/l;var P=V*Cufon.CSS.fontStretch(Y.get("fontStretch"));var U=I.minX,T=I.minY;R.height=af;R.top=Math.round(p.convert(T-ac.ascent));R.left=Math.round(p.convert(U));ai.height=p.convert(ac.height)+"px";var F=Y.get("color");var ag=Cufon.CSS.textTransform(G,Y).split("");var L=ac.spacing(ag,f(ad,Y,p,"letterSpacing"),f(ad,Y,p,"wordSpacing"));if(!L.length){return null}var k=L.total;var x=-U+k+(I.width-L[L.length-1]);var ah=p.convert(x*P),X=Math.round(ah);var O=x+","+I.height,m;var J="r"+O+"ns";var u=C.textGradient&&d(C.textGradient);var o=ac.glyphs,S=0;var H=C.textShadow;var ab=-1,aa=0,w;while(w=ag[++ab]){var D=o[ag[ab]]||ac.missingGlyph,v;if(!D){continue}if(n){v=q.childNodes[aa];while(v.firstChild){v.removeChild(v.firstChild)}}else{v=document.createElement("cvml:shape");q.appendChild(v)}v.stroked="f";v.coordsize=O;v.coordorigin=m=(U-S)+","+T;v.path=(D.d?"m"+D.d+"xe":"")+"m"+m+J;v.fillcolor=F;if(u){v.appendChild(u.cloneNode(false))}var ae=v.style;ae.width=X;ae.height=af;if(H){var s=H[0],r=H[1];var B=Cufon.CSS.color(s.color),z;var N=document.createElement("cvml:shadow");N.on="t";N.color=B.color;N.offset=s.offX+","+s.offY;if(r){z=Cufon.CSS.color(r.color);N.type="double";N.color2=z.color;N.offset2=r.offX+","+r.offY}N.opacity=B.opacity||(z&&z.opacity)||1;v.appendChild(N)}S+=L[aa++]}var M=v.nextSibling,t,A;if(C.forceHitArea){if(!M){M=document.createElement("cvml:rect");M.stroked="f";M.className="cufon-vml-cover";t=document.createElement("cvml:fill");t.opacity=0;M.appendChild(t);q.appendChild(M)}A=M.style;A.width=X;A.height=af}else{if(M){q.removeChild(M)}}ai.width=Math.max(Math.ceil(p.convert(k*P)),0);if(h){var Q=Y.computedYAdjust;if(Q===undefined){var E=Y.get("lineHeight");if(E=="normal"){E="1em"}else{if(!isNaN(E)){E+="em"}}Y.computedYAdjust=Q=0.5*(a(ad,E)-parseFloat(ai.height))}if(Q){ai.marginTop=Math.ceil(Q)+"px";ai.marginBottom=Q+"px"}}return y}})());
/* This file is part of JonDesign's SmoothGallery v2.0. JonDesign's SmoothGallery is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. */
var gallery = {initialize: function(element, options) {this.setOptions({showArrows: true,showCarousel: true,showInfopane: true,embedLinks: true,fadeDuration: 500,timed: false,delay: 9000,preloader: true,preloaderImage: true,preloaderErrorImage: true,manualData: [],populateFrom: false,populateData: true,destroyAfterPopulate: true,elementSelector: "div.imageElement",titleSelector: "h3",subtitleSelector: "p",linkSelector: "a.open",imageSelector: "img.full",thumbnailSelector: "img.thumbnail",defaultTransition: "fade",slideInfoZoneOpacity: 1.0,slideInfoZoneSlide: true,carouselMinimizedOpacity: 0.4,carouselMinimizedHeight: 20,carouselMaximizedOpacity: 0.9,thumbHeight: 75,thumbWidth: 100,thumbSpacing: 13,thumbIdleOpacity: 0.2,showCarouselLabel: true,thumbCloseCarousel: true,useThumbGenerator: false,thumbGenerator: 'resizer.php',useExternalCarousel: false,carouselElement: false,carouselHorizontal: true,activateCarouselScroller: true,carouselPreloader: true,textPreloadingCarousel: 'Loading...',baseClass: 'jdGallery',withArrowsClass: 'withArrows',useHistoryManager: false,customHistoryKey: false}, options);this.fireEvent('onInit');this.currentIter = 0;this.lastIter = 0;this.maxIter = 0;this.galleryElement = element;this.galleryData = this.options.manualData;this.galleryInit = 1;this.galleryElements = Array();this.thumbnailElements = Array();this.galleryElement.addClass(this.options.baseClass);this.populateFrom = element;if (this.options.populateFrom) {this.populateFrom = this.options.populateFrom;}if (this.options.populateData){this.populateData();}element.style.display="block";if (this.options.useHistoryManager){this.initHistory();}if (this.options.embedLinks){this.currentLink = new Element('a').addClass('open').setProperties({href: '#',title: ''}).injectInside(element);if ((!this.options.showArrows) && (!this.options.showCarousel)){this.galleryElement = element = this.currentLink;}else{this.currentLink.setStyle('display', 'none');}}

		

		this.constructElements();

		if ((this.galleryData.length>1)&&(this.options.showArrows))

		{

			var leftArrow = new Element('a').addClass('left').addEvent(

				'click',

				this.prevItem.bind(this)

			).injectInside(element);

			var rightArrow = new Element('a').addClass('right').addEvent(

				'click',

				this.nextItem.bind(this)

			).injectInside(element);

			this.galleryElement.addClass(this.options.withArrowsClass);

		}

		this.loadingElement = new Element('div').addClass('loadingElement').injectInside(element);

		if (this.options.showInfopane) this.initInfoSlideshow();

		if (this.options.showCarousel) this.initCarousel();

		this.doSlideShow(1);

	},

	populateData: function() {

		currentArrayPlace = this.galleryData.length;

		options = this.options;

		var data = $A(this.galleryData);

		data.extend(this.populateGallery(this.populateFrom, currentArrayPlace));

		this.galleryData = data;

		this.fireEvent('onPopulated');

	},

	populateGallery: function(element, startNumber) {

		var data = [];

		options = this.options;

		currentArrayPlace = startNumber;

		element.getElements(options.elementSelector).each(function(el) {

			elementDict = {

				image: el.getElement(options.imageSelector).getProperty('src'),

				number: currentArrayPlace,

				transition: this.options.defaultTransition

			};

			elementDict.extend = $extend;

			if ((options.showInfopane) | (options.showCarousel))

				elementDict.extend({

					title: el.getElement(options.titleSelector).innerHTML,

					description: el.getElement(options.subtitleSelector).innerHTML

				});

			if (options.embedLinks)

				elementDict.extend({

					link: el.getElement(options.linkSelector).href||false,

					linkTitle: el.getElement(options.linkSelector).title||false,

					linkTarget: el.getElement(options.linkSelector).getProperty('target')||false

				});

			if ((!options.useThumbGenerator) && (options.showCarousel))

				elementDict.extend({

					thumbnail: el.getElement(options.thumbnailSelector).getProperty('src')

				});

			else if (options.useThumbGenerator)

				elementDict.extend({

					thumbnail: options.thumbGenerator + '?imgfile=' + elementDict.image + '&max_width=' + options.thumbWidth + '&max_height=' + options.thumbHeight

				});

			

			data.extend([elementDict]);

			currentArrayPlace++;

			if (this.options.destroyAfterPopulate)

				el.remove();

		});

		return data;

	},

	constructElements: function() {

		el = this.galleryElement;

		this.maxIter = this.galleryData.length;

		var currentImg;

		for(i=0;i<this.galleryData.length;i++)

		{

			var currentImg = new Fx.Styles(

				new Element('div').addClass('slideElement').setStyles({

					'position':'absolute',

					'left':'0px',

					'right':'0px',

					'margin':'0px',

					'padding':'0px',

					'backgroundPosition':"center center",

					'opacity':'0'

				}).injectInside(el),

				'opacity',

				{duration: this.options.fadeDuration}

			);

			if (this.options.preloader)

			{

				currentImg.source = this.galleryData[i].image;

				currentImg.loaded = false;

				currentImg.load = function(imageStyle) {

					if (!imageStyle.loaded)	{

						new Asset.image(imageStyle.source, {

		                            'onload'  : function(img){

													img.element.setStyle(

													'backgroundImage',

													"url('" + img.source + "')")

													img.loaded = true;

												}.bind(this, imageStyle)

						});

					}

				}.pass(currentImg, this);

			} else {

				currentImg.element.setStyle('backgroundImage',

									"url('" + this.galleryData[i].image + "')");

			}

			this.galleryElements[parseInt(i)] = currentImg;

		}

	},

	destroySlideShow: function(element) {

		var myClassName = element.className;

		var newElement = new Element('div').addClass('myClassName');

		element.parentNode.replaceChild(newElement, element);

	},

	startSlideShow: function() {

		this.fireEvent('onStart');

		this.loadingElement.style.display = "none";

		this.lastIter = this.maxIter - 1;

		this.currentIter = 0;

		this.galleryInit = 0;

		this.galleryElements[parseInt(this.currentIter)].set({opacity: 1});

		if (this.options.showInfopane)

			this.showInfoSlideShow.delay(1000, this);

		var textShowCarousel = formatString(this.options.textShowCarousel, this.currentIter+1, this.maxIter);

		if (this.options.showCarousel&&(!this.options.carouselPreloader))

			this.carouselBtn.setHTML(textShowCarousel).setProperty('title', textShowCarousel);

		this.prepareTimer();

		if (this.options.embedLinks)

			this.makeLink(this.currentIter);

	},

	nextItem: function() {

		this.fireEvent('onNextCalled');

		this.nextIter = this.currentIter+1;

		if (this.nextIter >= this.maxIter)

			this.nextIter = 0;

		this.galleryInit = 0;

		this.goTo(this.nextIter);

	},

	prevItem: function() {

		this.fireEvent('onPreviousCalled');

		this.nextIter = this.currentIter-1;

		if (this.nextIter <= -1)

			this.nextIter = this.maxIter - 1;

		this.galleryInit = 0;

		this.goTo(this.nextIter);

	},

	goTo: function(num) {

		this.clearTimer();

		if(this.options.preloader)

		{

			this.galleryElements[num].load();

			if (num==0)

				this.galleryElements[this.maxIter - 1].load();

			else

				this.galleryElements[num - 1].load();

			if (num==(this.maxIter - 1))

				this.galleryElements[0].load();

			else

				this.galleryElements[num + 1].load();

				

		}

		if (this.options.embedLinks)

			this.clearLink();

		if (this.options.showInfopane)

		{

			this.slideInfoZone.clearChain();

			this.hideInfoSlideShow().chain(this.changeItem.pass(num, this));

		} else

			this.currentChangeDelay = this.changeItem.delay(500, this, num);

		if (this.options.embedLinks)

			this.makeLink(num);

		this.prepareTimer();

		/*if (this.options.showCarousel)

			this.clearThumbnailsHighlights();*/

	},

	changeItem: function(num) {

		this.fireEvent('onStartChanging');

		this.galleryInit = 0;

		if (this.currentIter != num)

		{

			for(i=0;i<this.maxIter;i++)

			{

				if ((i != this.currentIter)) this.galleryElements[i].set({opacity: 0});

			}

			gallery.Transitions[this.galleryData[num].transition].pass([

				this.galleryElements[this.currentIter],

				this.galleryElements[num],

				this.currentIter,

				num], this)();

			this.currentIter = num;

		}

		var textShowCarousel = formatString(this.options.textShowCarousel, num+1, this.maxIter);

		if (this.options.showCarousel)

			this.carouselBtn.setHTML(textShowCarousel).setProperty('title', textShowCarousel);

		this.doSlideShow.bind(this)();

		this.fireEvent('onChanged');

	},

	clearTimer: function() {

		if (this.options.timed)

			$clear(this.timer);

	},

	prepareTimer: function() {

		if (this.options.timed)

			this.timer = this.nextItem.delay(this.options.delay, this);

	},

	doSlideShow: function(position) {

		if (this.galleryInit == 1)

		{

			imgPreloader = new Image();

			imgPreloader.onload=function(){

				this.startSlideShow.delay(10, this);

			}.bind(this);

			imgPreloader.src = this.galleryData[0].image;

			if(this.options.preloader)

				this.galleryElements[0].load();

		} else {

			if (this.options.showInfopane)

			{

				if (this.options.showInfopane)

				{

					this.showInfoSlideShow.delay((500 + this.options.fadeDuration), this);

				} else

					if ((this.options.showCarousel)&&(this.options.activateCarouselScroller))

						this.centerCarouselOn(position);

			}

		}

	},

	createCarousel: function() {

		var carouselElement;

		if (!this.options.useExternalCarousel)

		{

			var carouselContainerElement = new Element('div').addClass('carouselContainer').injectInside(this.galleryElement);

			this.carouselContainer = new Fx.Styles(carouselContainerElement, {transition: Fx.Transitions.expoOut});

			this.carouselContainer.normalHeight = carouselContainerElement.offsetHeight;

			this.carouselContainer.set({'opacity': this.options.carouselMinimizedOpacity, 'top': (this.options.carouselMinimizedHeight - this.carouselContainer.normalHeight)});

			this.carouselBtn = new Element('a').addClass('carouselBtn').setProperties({

				title: this.options.textShowCarousel

			}).injectInside(carouselContainerElement);

			if(this.options.carouselPreloader)

				this.carouselBtn.setHTML(this.options.textPreloadingCarousel);

			else

				this.carouselBtn.setHTML(this.options.textShowCarousel);

			this.carouselBtn.addEvent(

				'click',

				function () {

					this.carouselContainer.clearTimer();

					this.toggleCarousel();

				}.bind(this)

			);

			this.carouselActive = false;

	

			carouselElement = new Element('div').addClass('carousel').injectInside(carouselContainerElement);

			this.carousel = new Fx.Styles(carouselElement);

		} else {

			carouselElement = $(this.options.carouselElement).addClass('jdExtCarousel');

		}

		this.carouselElement = new Fx.Styles(carouselElement, {transition: Fx.Transitions.expoOut});

		this.carouselElement.normalHeight = carouselElement.offsetHeight;

		if (this.options.showCarouselLabel)

			this.carouselLabel = new Element('p').addClass('label').injectInside(carouselElement);

		carouselWrapper = new Element('div').addClass('carouselWrapper').injectInside(carouselElement);

		this.carouselWrapper = new Fx.Styles(carouselWrapper, {transition: Fx.Transitions.expoOut});

		this.carouselWrapper.normalHeight = carouselWrapper.offsetHeight;

		this.carouselInner = new Element('div').addClass('carouselInner').injectInside(carouselWrapper);

		if (this.options.activateCarouselScroller)

		{

			this.carouselWrapper.scroller = new Scroller(carouselWrapper, {

				area: 100,

				velocity: 0.2

			})

			

			this.carouselWrapper.elementScroller = new Fx.Scroll(carouselWrapper, {

				duration: 400,

				onStart: this.carouselWrapper.scroller.stop.bind(this.carouselWrapper.scroller),

				onComplete: this.carouselWrapper.scroller.start.bind(this.carouselWrapper.scroller)

			});

		}

	},

	fillCarousel: function() {

		this.constructThumbnails();

		this.carouselInner.normalWidth = ((this.maxIter * (this.options.thumbWidth + this.options.thumbSpacing + 2))+this.options.thumbSpacing) + "px";

		this.carouselInner.style.width = this.carouselInner.normalWidth;

	},

	initCarousel: function () {

		this.createCarousel();

		this.fillCarousel();

		if (this.options.carouselPreloader)

			this.preloadThumbnails();

	},

	flushCarousel: function() {

		this.thumbnailElements.each(function(myFx) {

			myFx.element.remove();

			myFx = myFx.element = null;

		});

		this.thumbnailElements = [];

	},

	toggleCarousel: function() {

		if (this.carouselActive)

			this.hideCarousel();

		else

			this.showCarousel();

	},

	showCarousel: function () {

		this.fireEvent('onShowCarousel');

		this.carouselContainer.start({

			'opacity': this.options.carouselMaximizedOpacity,

			'top': 0

		}).chain(function() {

			this.carouselActive = true;

			this.carouselWrapper.scroller.start();

			this.fireEvent('onCarouselShown');

			this.carouselContainer.options.onComplete = null;

		}.bind(this));

	},

	hideCarousel: function () {

		this.fireEvent('onHideCarousel');

		var targetTop = this.options.carouselMinimizedHeight - this.carouselContainer.normalHeight;

		this.carouselContainer.start({

			'opacity': this.options.carouselMinimizedOpacity,

			'top': targetTop

		}).chain(function() {

			this.carouselActive = false;

			this.carouselWrapper.scroller.stop();

			this.fireEvent('onCarouselHidden');

			this.carouselContainer.options.onComplete = null;

		}.bind(this));

	},

	constructThumbnails: function () {

		element = this.carouselInner;

		for(i=0;i<this.galleryData.length;i++)

		{

			var currentImg = new Fx.Style(new Element ('div').addClass("thumbnail").setStyles({

					backgroundImage: "url('" + this.galleryData[i].thumbnail + "')",

					backgroundPosition: "center center",

					backgroundRepeat: 'no-repeat',

					marginLeft: this.options.thumbSpacing + "px",

					width: this.options.thumbWidth + "px",

					height: this.options.thumbHeight + "px"

				}).injectInside(element), "opacity", {duration: 200}).set(this.options.thumbIdleOpacity);

			currentImg.element.addEvents({

				'mouseover': function (myself) {

					myself.clearTimer();

					myself.start(0.99);

					if (this.options.showCarouselLabel)

						$(this.carouselLabel).setHTML('<span class="number">' + (myself.relatedImage.number + 1) + "/" + this.maxIter + ":</span> " + myself.relatedImage.title);

				}.pass(currentImg, this),

				'mouseout': function (myself) {

					myself.clearTimer();

					myself.start(this.options.thumbIdleOpacity);

				}.pass(currentImg, this),

				'click': function (myself) {

					this.goTo(myself.relatedImage.number);

					if (this.options.thumbCloseCarousel)

						this.hideCarousel();

				}.pass(currentImg, this)

			});

			

			currentImg.relatedImage = this.galleryData[i];

			this.thumbnailElements[parseInt(i)] = currentImg;

		}

	},

	log: function(value) {

		if(console.log)

			console.log(value);

	},

	preloadThumbnails: function() {

		var thumbnails = [];

		for(i=0;i<this.galleryData.length;i++)

		{

			thumbnails[parseInt(i)] = this.galleryData[i].thumbnail;

		}

		this.thumbnailPreloader = new Preloader();

		this.thumbnailPreloader.addEvent('onComplete', function() {

			var textShowCarousel = formatString(this.options.textShowCarousel, this.currentIter+1, this.maxIter);

			this.carouselBtn.setHTML(textShowCarousel).setProperty('title', textShowCarousel);

		}.bind(this));

		this.thumbnailPreloader.load(thumbnails);

	},

	clearThumbnailsHighlights: function()

	{

		for(i=0;i<this.galleryData.length;i++)

		{

			this.thumbnailElements[i].clearTimer();

			this.thumbnailElements[i].start(0.2);

		}

	},

	changeThumbnailsSize: function(width, height)

	{

		for(i=0;i<this.galleryData.length;i++)

		{

			this.thumbnailElements[i].clearTimer();

			this.thumbnailElements[i].element.setStyles({

				'width': width + "px",

				'height': height + "px"

			});

		}

	},

	centerCarouselOn: function(num) {

		if (!this.carouselWallMode)

		{

			var carouselElement = this.thumbnailElements[num];

			var position = carouselElement.element.offsetLeft + (carouselElement.element.offsetWidth / 2);

			var carouselWidth = this.carouselWrapper.element.offsetWidth;

			var carouselInnerWidth = this.carouselInner.offsetWidth;

			var diffWidth = carouselWidth / 2;

			var scrollPos = position-diffWidth;

			this.carouselWrapper.elementScroller.scrollTo(scrollPos,0);

		}

	},

	initInfoSlideshow: function() {

		/*if (this.slideInfoZone.element)

			this.slideInfoZone.element.remove();*/

		this.slideInfoZone = new Fx.Styles(new Element('div').addClass('slideInfoZone').injectInside($(this.galleryElement))).set({'opacity':0});

		var slideInfoZoneTitle = new Element('h2').injectInside(this.slideInfoZone.element);

		var slideInfoZoneDescription = new Element('p').injectInside(this.slideInfoZone.element);

		this.slideInfoZone.normalHeight = this.slideInfoZone.element.offsetHeight;

		this.slideInfoZone.element.setStyle('opacity',0);

	},

	changeInfoSlideShow: function()

	{

		this.hideInfoSlideShow.delay(10, this);

		this.showInfoSlideShow.delay(500, this);

	},

	showInfoSlideShow: function() {

		this.fireEvent('onShowInfopane');

		this.slideInfoZone.clearTimer();

		element = this.slideInfoZone.element;

		element.getElement('h2').setHTML(this.galleryData[this.currentIter].title);

		element.getElement('p').setHTML(this.galleryData[this.currentIter].description);

		if(this.options.slideInfoZoneSlide)

			this.slideInfoZone.start({'opacity': [0, this.options.slideInfoZoneOpacity], 'height': [0, this.slideInfoZone.normalHeight]});

		else

			this.slideInfoZone.start({'opacity': [0, this.options.slideInfoZoneOpacity]});

		if (this.options.showCarousel)

			this.slideInfoZone.chain(this.centerCarouselOn.pass(this.currentIter, this));

		return this.slideInfoZone;

	},

	hideInfoSlideShow: function() {

		this.fireEvent('onHideInfopane');

		this.slideInfoZone.clearTimer();

		if(this.options.slideInfoZoneSlide)

			this.slideInfoZone.start({'opacity': 0, 'height': 0});

		else

			this.slideInfoZone.start({'opacity': 0});

		return this.slideInfoZone;

	},

	makeLink: function(num) {

		this.currentLink.setProperties({

			href: this.galleryData[num].link,

			title: this.galleryData[num].linkTitle

		})

		if (!((this.options.embedLinks) && (!this.options.showArrows) && (!this.options.showCarousel)))

			this.currentLink.setStyle('display', 'block');

	},

	clearLink: function() {

		this.currentLink.setProperties({href: '', title: ''});

		if (!((this.options.embedLinks) && (!this.options.showArrows) && (!this.options.showCarousel)))

			this.currentLink.setStyle('display', 'none');

	},

	/* To change the gallery data, those two functions : */

	flushGallery: function() {

		this.galleryElements.each(function(myFx) {

			myFx.element.remove();

			myFx = myFx.element = null;

		});

		this.galleryElements = [];

	},

	changeData: function(data) {

		this.galleryData = data;

		this.clearTimer();

		this.flushGallery();

		if (this.options.showCarousel) this.flushCarousel();

		this.constructElements();

		if (this.options.showCarousel) this.fillCarousel();

		if (this.options.showInfopane) this.hideInfoSlideShow();

		this.galleryInit=1;

		this.lastIter=0;

		this.currentIter=0;

		this.doSlideShow(1);

	},

	/* Plugins: HistoryManager */

	initHistory: function() {

		this.fireEvent('onHistoryInit');

		this.historyKey = this.galleryElement.id + '-picture';

		if (this.options.customHistoryKey)

			this.historyKey = this.options.customHistoryKey();

		this.history = HistoryManager.register(

			this.historyKey,

			[1],

			function(values) {

				if (parseInt(values[0])-1 < this.maxIter)

					this.goTo(parseInt(values[0])-1);

			}.bind(this),

			function(values) {

				return [this.historyKey, '(', values[0], ')'].join('');

			}.bind(this),

			this.historyKey + '\\((\\d+)\\)');

		this.addEvent('onChanged', function(){

			this.history.setValue(0, this.currentIter+1);

		}.bind(this));

		this.fireEvent('onHistoryInited');

	}

};

gallery = new Class(gallery);

gallery.implement(new Events);

gallery.implement(new Options);



gallery.Transitions = new Abstract ({

	fade: function(oldFx, newFx, oldPos, newPos){

		oldFx.options.transition = newFx.options.transition = Fx.Transitions.linear;

		oldFx.options.duration = newFx.options.duration = this.options.fadeDuration;

		if (newPos > oldPos) newFx.start({opacity: 1});

		else

		{

			newFx.set({opacity: 1});

			oldFx.start({opacity: 0});

		}

	},

	crossfade: function(oldFx, newFx, oldPos, newPos){

		oldFx.options.transition = newFx.options.transition = Fx.Transitions.linear;

		oldFx.options.duration = newFx.options.duration = this.options.fadeDuration;

		newFx.start({opacity: 1});

		oldFx.start({opacity: 0});

	},

	fadebg: function(oldFx, newFx, oldPos, newPos){

		oldFx.options.transition = newFx.options.transition = Fx.Transitions.linear;

		oldFx.options.duration = newFx.options.duration = this.options.fadeDuration / 2;

		oldFx.start({opacity: 0}).chain(newFx.start.pass([{opacity: 1}], newFx));

	}

});/* All code copyright 2007 Jonathan Schemoul */var Preloader = new Class({Implements: [Events, Options],options: {root : '',period : 100},initialize: function(options){this.setOptions(options);},load: function(sources) {this.index = 0;this.images = [];this.sources = this.temps = sources;this.total = this. sources.length;this.fireEvent('onStart', [this.index, this.total]);this.timer = this.progress.periodical(this.options.period, this);this.sources.each(function(source, index){this.images[index] = new Asset.image(this.options.root + source, {'onload'  : function(){ this.index++; if(this.images[index]) this.fireEvent('onLoad', [this.images[index], index, source]); }.bind(this),'onerror' : function(){ this.index++; this.fireEvent('onError', [this.images.splice(index, 1), index, source]); }.bind(this),'onabort' : function(){ this.index++; this.fireEvent('onError', [this.images.splice(index, 1), index, source]); }.bind(this)});}, this);},progress: function() {this.fireEvent('onProgress', [Math.min(this.index, this.total), this.total]);if(this.index >= this.total) this.complete();},complete: function(){$clear(this.timer);this.fireEvent('onComplete', [this.images]);},cancel: function(){$clear(this.timer);}});Preloader.implement(new Events, new Options);function formatString() {var num = arguments.length;var oStr = arguments[0];for (var i = 1; i < num; i++) {var pattern = "\\{" + (i-1) + "\\}"; var re = new RegExp(pattern, "g");oStr = oStr.replace(re, arguments[i]);}return oStr;}