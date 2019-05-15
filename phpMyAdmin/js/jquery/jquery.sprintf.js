/*
 http://www.gnu.org/licenses/gpl.html
 @project jquery.sprintf
*/
(function(i){var d={b:function(a){return parseInt(a,10).toString(2)},c:function(a){return String.fromCharCode(parseInt(a,10))},d:function(a){return parseInt(a,10)},u:function(a){return Math.abs(a)},f:function(a,b){b=parseInt(b,10);a=parseFloat(a);if(isNaN(b&&a))return NaN;return b&&a.toFixed(b)||a},o:function(a){return parseInt(a,10).toString(8)},s:function(a){return a},x:function(a){return(""+parseInt(a,10).toString(16)).toLowerCase()},X:function(a){return(""+parseInt(a,10).toString(16)).toUpperCase()}},
e=/%(?:(\d+)?(?:\.(\d+))?|\(([^)]+)\))([%bcdufosxX])/g,h=function(a){if(a.length==1&&typeof a[0]=="object"){a=a[0];return function(j,k,f,g,c){return d[c](a[g])}}else{var b=0;return function(j,k,f,g,c){if(c=="%")return"%";return d[c](a[b++],f)}}};i.extend({sprintf:function(a){var b=Array.apply(null,arguments).slice(1);return a.replace(e,h(b))},vsprintf:function(a,b){return a.replace(e,h(b))}})})(jQuery);
