// We don't want to load uikit using requirejs or similar so implement a workaround here!
var gslWorkaround = false, keepModule = false, keepExports = false, keepDefine = false;
if (typeof exports === 'object' && typeof module !== 'undefined')
{
    keepModule = module;
    keepExports = exports;
    module = exports = false;
    gslWorkaround = true;
}
if (typeof define === 'function' && define.amd)
{
    keepDefine = define;
    define = false;
    gslWorkaround = true;
}
