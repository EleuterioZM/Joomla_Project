(function(){"use strict";/**
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */if(!window.Joomla)throw new Error("Joomla API was not properly initialised");var e=Joomla.getOptions("system.keepalive"),i=e&&e.interval?parseInt(e.interval,10):45*1e3,o=e&&e.uri?e.uri.replace(/&amp;/g,"&"):"";if(o===""){var t=Joomla.getOptions("system.paths");o=(t?t.root+"/index.php":window.location.pathname)+"?option=com_ajax&format=json"}setInterval(function(){return fetch(o,{method:"POST"})},i)})();
