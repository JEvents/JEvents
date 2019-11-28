/*! gslUIkit 3.1.5 | http://www.getuikit.com | (c) 2014 - 2018 YOOtheme | MIT License */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define('uikit', factory) :
    (global = global || self, global.gslUIkit = factory());
}(this, function () { 'use strict';

    function bind(fn, context) {
        return function (a) {
            var l = arguments.length;
            return l ? l > 1 ? fn.apply(context, arguments) : fn.call(context, a) : fn.call(context);
        };
    }

    var objPrototype = Object.prototype;
    var hasOwnProperty = objPrototype.hasOwnProperty;

    function hasOwn(obj, key) {
        return hasOwnProperty.call(obj, key);
    }

    var hyphenateCache = {};
    var hyphenateRe = /([a-z\d])([A-Z])/g;

    function hyphenate(str) {

        if (!(str in hyphenateCache)) {
            hyphenateCache[str] = str
                .replace(hyphenateRe, '$1-$2')
                .toLowerCase();
        }

        return hyphenateCache[str];
    }

    var camelizeRe = /-(\w)/g;

    function camelize(str) {
        return str.replace(camelizeRe, toUpper);
    }

    function toUpper(_, c) {
        return c ? c.toUpperCase() : '';
    }

    function ucfirst(str) {
        return str.length ? toUpper(null, str.charAt(0)) + str.slice(1) : '';
    }

    var strPrototype = String.prototype;
    var startsWithFn = strPrototype.startsWith || function (search) { return this.lastIndexOf(search, 0) === 0; };

    function startsWith(str, search) {
        return startsWithFn.call(str, search);
    }

    var endsWithFn = strPrototype.endsWith || function (search) { return this.substr(-search.length) === search; };

    function endsWith(str, search) {
        return endsWithFn.call(str, search);
    }

    var arrPrototype = Array.prototype;

    var includesFn = function (search, i) { return ~this.indexOf(search, i); };
    var includesStr = strPrototype.includes || includesFn;
    var includesArray = arrPrototype.includes || includesFn;

    function includes(obj, search) {
        return obj && (isString(obj) ? includesStr : includesArray).call(obj, search);
    }

    var findIndexFn = arrPrototype.findIndex || function (predicate) {
        var arguments$1 = arguments;

        for (var i = 0; i < this.length; i++) {
            if (predicate.call(arguments$1[1], this[i], i, this)) {
                return i;
            }
        }
        return -1;
    };

    function findIndex(array, predicate) {
        return findIndexFn.call(array, predicate);
    }

    var isArray = Array.isArray;

    function isFunction(obj) {
        return typeof obj === 'function';
    }

    function isObject(obj) {
        return obj !== null && typeof obj === 'object';
    }

    function isPlainObject(obj) {
        return isObject(obj) && Object.getPrototypeOf(obj) === objPrototype;
    }

    function isWindow(obj) {
        return isObject(obj) && obj === obj.window;
    }

    function isDocument(obj) {
        return isObject(obj) && obj.nodeType === 9;
    }

    function isJQuery(obj) {
        return isObject(obj) && !!obj.jquery;
    }

    function isNode(obj) {
        return obj instanceof Node || isObject(obj) && obj.nodeType >= 1;
    }

    var toString = objPrototype.toString;
    function isNodeCollection(obj) {
        return toString.call(obj).match(/^\[object (NodeList|HTMLCollection)\]$/);
    }

    function isBoolean(value) {
        return typeof value === 'boolean';
    }

    function isString(value) {
        return typeof value === 'string';
    }

    function isNumber(value) {
        return typeof value === 'number';
    }

    function isNumeric(value) {
        return isNumber(value) || isString(value) && !isNaN(value - parseFloat(value));
    }

    function isEmpty(obj) {
        return !(isArray(obj)
            ? obj.length
            : isObject(obj)
                ? Object.keys(obj).length
                : false
        );
    }

    function isUndefined(value) {
        return value === void 0;
    }

    function toBoolean(value) {
        return isBoolean(value)
            ? value
            : value === 'true' || value === '1' || value === ''
                ? true
                : value === 'false' || value === '0'
                    ? false
                    : value;
    }

    function toNumber(value) {
        var number = Number(value);
        return !isNaN(number) ? number : false;
    }

    function toFloat(value) {
        return parseFloat(value) || 0;
    }

    function toNode(element) {
        return isNode(element) || isWindow(element) || isDocument(element)
            ? element
            : isNodeCollection(element) || isJQuery(element)
                ? element[0]
                : isArray(element)
                    ? toNode(element[0])
                    : null;
    }

    function toNodes(element) {
        return isNode(element)
            ? [element]
            : isNodeCollection(element)
                ? arrPrototype.slice.call(element)
                : isArray(element)
                    ? element.map(toNode).filter(Boolean)
                    : isJQuery(element)
                        ? element.toArray()
                        : [];
    }

    function toList(value) {
        return isArray(value)
            ? value
            : isString(value)
                ? value.split(/,(?![^(]*\))/).map(function (value) { return isNumeric(value)
                    ? toNumber(value)
                    : toBoolean(value.trim()); })
                : [value];
    }

    function toMs(time) {
        return !time
            ? 0
            : endsWith(time, 'ms')
                ? toFloat(time)
                : toFloat(time) * 1000;
    }

    function isEqual(value, other) {
        return value === other
            || isObject(value)
            && isObject(other)
            && Object.keys(value).length === Object.keys(other).length
            && each(value, function (val, key) { return val === other[key]; });
    }

    function swap(value, a, b) {
        return value.replace(new RegExp((a + "|" + b), 'mg'), function (match) {
            return match === a ? b : a;
        });
    }

    var assign = Object.assign || function (target) {
        var args = [], len = arguments.length - 1;
        while ( len-- > 0 ) args[ len ] = arguments[ len + 1 ];

        target = Object(target);
        for (var i = 0; i < args.length; i++) {
            var source = args[i];
            if (source !== null) {
                for (var key in source) {
                    if (hasOwn(source, key)) {
                        target[key] = source[key];
                    }
                }
            }
        }
        return target;
    };

    function each(obj, cb) {
        for (var key in obj) {
            if (false === cb(obj[key], key)) {
                return false;
            }
        }
        return true;
    }

    function sortBy(array, prop) {
        return array.sort(function (ref, ref$1) {
                var propA = ref[prop]; if ( propA === void 0 ) propA = 0;
                var propB = ref$1[prop]; if ( propB === void 0 ) propB = 0;

                return propA > propB
                ? 1
                : propB > propA
                    ? -1
                    : 0;
        }
        );
    }

    function uniqueBy(array, prop) {
        var seen = new Set();
        return array.filter(function (ref) {
            var check = ref[prop];

            return seen.has(check)
            ? false
            : seen.add(check) || true;
        } // IE 11 does not return the Set object
        );
    }

    function clamp(number, min, max) {
        if ( min === void 0 ) min = 0;
        if ( max === void 0 ) max = 1;

        return Math.min(Math.max(toNumber(number) || 0, min), max);
    }

    function noop() {}

    function intersectRect(r1, r2) {
        return r1.left < r2.right &&
            r1.right > r2.left &&
            r1.top < r2.bottom &&
            r1.bottom > r2.top;
    }

    function pointInRect(point, rect) {
        return point.x <= rect.right &&
            point.x >= rect.left &&
            point.y <= rect.bottom &&
            point.y >= rect.top;
    }

    var Dimensions = {

        ratio: function(dimensions, prop, value) {
            var obj;


            var aProp = prop === 'width' ? 'height' : 'width';

            return ( obj = {}, obj[aProp] = dimensions[prop] ? Math.round(value * dimensions[aProp] / dimensions[prop]) : dimensions[aProp], obj[prop] = value, obj );
        },

        contain: function(dimensions, maxDimensions) {
            var this$1 = this;

            dimensions = assign({}, dimensions);

            each(dimensions, function (_, prop) { return dimensions = dimensions[prop] > maxDimensions[prop]
                ? this$1.ratio(dimensions, prop, maxDimensions[prop])
                : dimensions; }
            );

            return dimensions;
        },

        cover: function(dimensions, maxDimensions) {
            var this$1 = this;

            dimensions = this.contain(dimensions, maxDimensions);

            each(dimensions, function (_, prop) { return dimensions = dimensions[prop] < maxDimensions[prop]
                ? this$1.ratio(dimensions, prop, maxDimensions[prop])
                : dimensions; }
            );

            return dimensions;
        }

    };

    function attr(element, name, value) {

        if (isObject(name)) {
            for (var key in name) {
                attr(element, key, name[key]);
            }
            return;
        }

        if (isUndefined(value)) {
            element = toNode(element);
            return element && element.getAttribute(name);
        } else {
            toNodes(element).forEach(function (element) {

                if (isFunction(value)) {
                    value = value.call(element, attr(element, name));
                }

                if (value === null) {
                    removeAttr(element, name);
                } else {
                    element.setAttribute(name, value);
                }
            });
        }

    }

    function hasAttr(element, name) {
        return toNodes(element).some(function (element) { return element.hasAttribute(name); });
    }

    function removeAttr(element, name) {
        element = toNodes(element);
        name.split(' ').forEach(function (name) { return element.forEach(function (element) { return element.hasAttribute(name) && element.removeAttribute(name); }
            ); }
        );
    }

    function data(element, attribute) {
        for (var i = 0, attrs = [attribute, ("data-" + attribute)]; i < attrs.length; i++) {
            if (hasAttr(element, attrs[i])) {
                return attr(element, attrs[i]);
            }
        }
    }

    function query(selector, context) {
        return toNode(selector) || find(selector, getContext(selector, context));
    }

    function queryAll(selector, context) {
        var nodes = toNodes(selector);
        return nodes.length && nodes || findAll(selector, getContext(selector, context));
    }

    function getContext(selector, context) {
        if ( context === void 0 ) context = document;

        return isContextSelector(selector) || isDocument(context)
            ? context
            : context.ownerDocument;
    }

    function find(selector, context) {
        return toNode(_query(selector, context, 'querySelector'));
    }

    function findAll(selector, context) {
        return toNodes(_query(selector, context, 'querySelectorAll'));
    }

    function _query(selector, context, queryFn) {
        if ( context === void 0 ) context = document;


        if (!selector || !isString(selector)) {
            return null;
        }

        selector = selector.replace(contextSanitizeRe, '$1 *');

        var removes;

        if (isContextSelector(selector)) {

            removes = [];

            selector = splitSelector(selector).map(function (selector, i) {

                var ctx = context;

                if (selector[0] === '!') {

                    var selectors = selector.substr(1).trim().split(' ');
                    ctx = closest(context.parentNode, selectors[0]);
                    selector = selectors.slice(1).join(' ').trim();

                }

                if (selector[0] === '-') {

                    var selectors$1 = selector.substr(1).trim().split(' ');
                    var prev = (ctx || context).previousElementSibling;
                    ctx = matches(prev, selector.substr(1)) ? prev : null;
                    selector = selectors$1.slice(1).join(' ');

                }

                if (!ctx) {
                    return null;
                }

                if (!ctx.id) {
                    ctx.id = "gsl-" + (Date.now()) + i;
                    removes.push(function () { return removeAttr(ctx, 'id'); });
                }

                return ("#" + (escape(ctx.id)) + " " + selector);

            }).filter(Boolean).join(',');

            context = document;

        }

        try {

            return context[queryFn](selector);

        } catch (e) {

            return null;

        } finally {

            removes && removes.forEach(function (remove) { return remove(); });

        }

    }

    var contextSelectorRe = /(^|[^\\],)\s*[!>+~-]/;
    var contextSanitizeRe = /([!>+~-])(?=\s+[!>+~-]|\s*$)/g;

    function isContextSelector(selector) {
        return isString(selector) && selector.match(contextSelectorRe);
    }

    var selectorRe = /.*?[^\\](?:,|$)/g;

    function splitSelector(selector) {
        return selector.match(selectorRe).map(function (selector) { return selector.replace(/,$/, '').trim(); });
    }

    var elProto = Element.prototype;
    var matchesFn = elProto.matches || elProto.webkitMatchesSelector || elProto.msMatchesSelector;

    function matches(element, selector) {
        return toNodes(element).some(function (element) { return matchesFn.call(element, selector); });
    }

    var closestFn = elProto.closest || function (selector) {
        var ancestor = this;

        do {

            if (matches(ancestor, selector)) {
                return ancestor;
            }

            ancestor = ancestor.parentNode;

        } while (ancestor && ancestor.nodeType === 1);
    };

    function closest(element, selector) {

        if (startsWith(selector, '>')) {
            selector = selector.slice(1);
        }

        return isNode(element)
            ? element.parentNode && closestFn.call(element, selector)
            : toNodes(element).map(function (element) { return closest(element, selector); }).filter(Boolean);
    }

    function parents(element, selector) {
        var elements = [];
        var parent = toNode(element).parentNode;

        while (parent && parent.nodeType === 1) {

            if (matches(parent, selector)) {
                elements.push(parent);
            }

            parent = parent.parentNode;
        }

        return elements;
    }

    var escapeFn = window.CSS && CSS.escape || function (css) { return css.replace(/([^\x7f-\uFFFF\w-])/g, function (match) { return ("\\" + match); }); };
    function escape(css) {
        return isString(css) ? escapeFn.call(null, css) : '';
    }

    var voidElements = {
        area: true,
        base: true,
        br: true,
        col: true,
        embed: true,
        hr: true,
        img: true,
        input: true,
        keygen: true,
        link: true,
        menuitem: true,
        meta: true,
        param: true,
        source: true,
        track: true,
        wbr: true
    };
    function isVoidElement(element) {
        return toNodes(element).some(function (element) { return voidElements[element.tagName.toLowerCase()]; });
    }

    function isVisible(element) {
        return toNodes(element).some(function (element) { return element.offsetWidth || element.offsetHeight || element.getClientRects().length; });
    }

    var selInput = 'input,select,textarea,button';
    function isInput(element) {
        return toNodes(element).some(function (element) { return matches(element, selInput); });
    }

    function filter(element, selector) {
        return toNodes(element).filter(function (element) { return matches(element, selector); });
    }

    function within(element, selector) {
        return !isString(selector)
            ? element === selector || (isDocument(selector)
                ? selector.documentElement
                : toNode(selector)).contains(toNode(element)) // IE 11 document does not implement contains
            : matches(element, selector) || closest(element, selector);
    }

    function on() {
        var args = [], len = arguments.length;
        while ( len-- ) args[ len ] = arguments[ len ];


        var ref = getArgs(args);
        var targets = ref[0];
        var type = ref[1];
        var selector = ref[2];
        var listener = ref[3];
        var useCapture = ref[4];

        targets = toEventTargets(targets);

        if (selector) {
            listener = delegate(targets, selector, listener);
        }

        if (listener.length > 1) {
            listener = detail(listener);
        }

        type.split(' ').forEach(function (type) { return targets.forEach(function (target) { return target.addEventListener(type, listener, useCapture); }
            ); }
        );
        return function () { return off(targets, type, listener, useCapture); };
    }

    function off(targets, type, listener, useCapture) {
        if ( useCapture === void 0 ) useCapture = false;

        targets = toEventTargets(targets);
        type.split(' ').forEach(function (type) { return targets.forEach(function (target) { return target.removeEventListener(type, listener, useCapture); }
            ); }
        );
    }

    function once() {
        var args = [], len = arguments.length;
        while ( len-- ) args[ len ] = arguments[ len ];


        var ref = getArgs(args);
        var element = ref[0];
        var type = ref[1];
        var selector = ref[2];
        var listener = ref[3];
        var useCapture = ref[4];
        var condition = ref[5];
        var off = on(element, type, selector, function (e) {
            var result = !condition || condition(e);
            if (result) {
                off();
                listener(e, result);
            }
        }, useCapture);

        return off;
    }

    function trigger(targets, event, detail) {
        return toEventTargets(targets).reduce(function (notCanceled, target) { return notCanceled && target.dispatchEvent(createEvent(event, true, true, detail)); }
            , true);
    }

    function createEvent(e, bubbles, cancelable, detail) {
        if ( bubbles === void 0 ) bubbles = true;
        if ( cancelable === void 0 ) cancelable = false;

        if (isString(e)) {
            var event = document.createEvent('CustomEvent'); // IE 11
            event.initCustomEvent(e, bubbles, cancelable, detail);
            e = event;
        }

        return e;
    }

    function getArgs(args) {
        if (isFunction(args[2])) {
            args.splice(2, 0, false);
        }
        return args;
    }

    function delegate(delegates, selector, listener) {
        var this$1 = this;

        return function (e) {

            delegates.forEach(function (delegate) {

                var current = selector[0] === '>'
                    ? findAll(selector, delegate).reverse().filter(function (element) { return within(e.target, element); })[0]
                    : closest(e.target, selector);

                if (current) {
                    e.delegate = delegate;
                    e.current = current;

                    listener.call(this$1, e);
                }

            });

        };
    }

    function detail(listener) {
        return function (e) { return isArray(e.detail) ? listener.apply(void 0, [e].concat(e.detail)) : listener(e); };
    }

    function isEventTarget(target) {
        return target && 'addEventListener' in target;
    }

    function toEventTarget(target) {
        return isEventTarget(target) ? target : toNode(target);
    }

    function toEventTargets(target) {
        return isArray(target)
                ? target.map(toEventTarget).filter(Boolean)
                : isString(target)
                    ? findAll(target)
                    : isEventTarget(target)
                        ? [target]
                        : toNodes(target);
    }

    function isTouch(e) {
        return e.pointerType === 'touch' || e.touches;
    }

    function getEventPos(e, prop) {
        if ( prop === void 0 ) prop = 'client';

        var touches = e.touches;
        var changedTouches = e.changedTouches;
        var ref = touches && touches[0] || changedTouches && changedTouches[0] || e;
        var x = ref[(prop + "X")];
        var y = ref[(prop + "Y")];

        return {x: x, y: y};
    }

    /* global setImmediate */

    var Promise = 'Promise' in window ? window.Promise : PromiseFn;

    var Deferred = function() {
        var this$1 = this;

        this.promise = new Promise(function (resolve, reject) {
            this$1.reject = reject;
            this$1.resolve = resolve;
        });
    };

    /**
     * Promises/A+ polyfill v1.1.4 (https://github.com/bramstein/promis)
     */

    var RESOLVED = 0;
    var REJECTED = 1;
    var PENDING = 2;

    var async = 'setImmediate' in window ? setImmediate : setTimeout;

    function PromiseFn(executor) {

        this.state = PENDING;
        this.value = undefined;
        this.deferred = [];

        var promise = this;

        try {
            executor(
                function (x) {
                    promise.resolve(x);
                },
                function (r) {
                    promise.reject(r);
                }
            );
        } catch (e) {
            promise.reject(e);
        }
    }

    PromiseFn.reject = function (r) {
        return new PromiseFn(function (resolve, reject) {
            reject(r);
        });
    };

    PromiseFn.resolve = function (x) {
        return new PromiseFn(function (resolve, reject) {
            resolve(x);
        });
    };

    PromiseFn.all = function all(iterable) {
        return new PromiseFn(function (resolve, reject) {
            var result = [];
            var count = 0;

            if (iterable.length === 0) {
                resolve(result);
            }

            function resolver(i) {
                return function (x) {
                    result[i] = x;
                    count += 1;

                    if (count === iterable.length) {
                        resolve(result);
                    }
                };
            }

            for (var i = 0; i < iterable.length; i += 1) {
                PromiseFn.resolve(iterable[i]).then(resolver(i), reject);
            }
        });
    };

    PromiseFn.race = function race(iterable) {
        return new PromiseFn(function (resolve, reject) {
            for (var i = 0; i < iterable.length; i += 1) {
                PromiseFn.resolve(iterable[i]).then(resolve, reject);
            }
        });
    };

    var p = PromiseFn.prototype;

    p.resolve = function resolve(x) {
        var promise = this;

        if (promise.state === PENDING) {
            if (x === promise) {
                throw new TypeError('Promise settled with itself.');
            }

            var called = false;

            try {
                var then = x && x.then;

                if (x !== null && isObject(x) && isFunction(then)) {
                    then.call(
                        x,
                        function (x) {
                            if (!called) {
                                promise.resolve(x);
                            }
                            called = true;
                        },
                        function (r) {
                            if (!called) {
                                promise.reject(r);
                            }
                            called = true;
                        }
                    );
                    return;
                }
            } catch (e) {
                if (!called) {
                    promise.reject(e);
                }
                return;
            }

            promise.state = RESOLVED;
            promise.value = x;
            promise.notify();
        }
    };

    p.reject = function reject(reason) {
        var promise = this;

        if (promise.state === PENDING) {
            if (reason === promise) {
                throw new TypeError('Promise settled with itself.');
            }

            promise.state = REJECTED;
            promise.value = reason;
            promise.notify();
        }
    };

    p.notify = function notify() {
        var this$1 = this;

        async(function () {
            if (this$1.state !== PENDING) {
                while (this$1.deferred.length) {
                    var ref = this$1.deferred.shift();
                    var onResolved = ref[0];
                    var onRejected = ref[1];
                    var resolve = ref[2];
                    var reject = ref[3];

                    try {
                        if (this$1.state === RESOLVED) {
                            if (isFunction(onResolved)) {
                                resolve(onResolved.call(undefined, this$1.value));
                            } else {
                                resolve(this$1.value);
                            }
                        } else if (this$1.state === REJECTED) {
                            if (isFunction(onRejected)) {
                                resolve(onRejected.call(undefined, this$1.value));
                            } else {
                                reject(this$1.value);
                            }
                        }
                    } catch (e) {
                        reject(e);
                    }
                }
            }
        });
    };

    p.then = function then(onResolved, onRejected) {
        var this$1 = this;

        return new PromiseFn(function (resolve, reject) {
            this$1.deferred.push([onResolved, onRejected, resolve, reject]);
            this$1.notify();
        });
    };

    p.catch = function (onRejected) {
        return this.then(undefined, onRejected);
    };

    function ajax(url, options) {
        return new Promise(function (resolve, reject) {

            var env = assign({
                data: null,
                method: 'GET',
                headers: {},
                xhr: new XMLHttpRequest(),
                beforeSend: noop,
                responseType: ''
            }, options);

            env.beforeSend(env);

            var xhr = env.xhr;

            for (var prop in env) {
                if (prop in xhr) {
                    try {

                        xhr[prop] = env[prop];

                    } catch (e) {}
                }
            }

            xhr.open(env.method.toUpperCase(), url);

            for (var header in env.headers) {
                xhr.setRequestHeader(header, env.headers[header]);
            }

            on(xhr, 'load', function () {

                if (xhr.status === 0 || xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) {
                    resolve(xhr);
                } else {
                    reject(assign(Error(xhr.statusText), {
                        xhr: xhr,
                        status: xhr.status
                    }));
                }

            });

            on(xhr, 'error', function () { return reject(assign(Error('Network Error'), {xhr: xhr})); });
            on(xhr, 'timeout', function () { return reject(assign(Error('Network Timeout'), {xhr: xhr})); });

            xhr.send(env.data);
        });
    }

    function getImage(src, srcset, sizes) {

        return new Promise(function (resolve, reject) {
            var img = new Image();

            img.onerror = reject;
            img.onload = function () { return resolve(img); };

            sizes && (img.sizes = sizes);
            srcset && (img.srcset = srcset);
            img.src = src;
        });

    }

    /* global DocumentTouch */

    var isIE = /msie|trident/i.test(window.navigator.userAgent);
    var isRtl = attr(document.documentElement, 'dir') === 'rtl';

    var hasTouchEvents = 'ontouchstart' in window;
    var hasPointerEvents = window.PointerEvent;
    var hasTouch = hasTouchEvents
        || window.DocumentTouch && document instanceof DocumentTouch
        || navigator.maxTouchPoints; // IE >=11

    var pointerDown = hasPointerEvents ? 'pointerdown' : hasTouchEvents ? 'touchstart' : 'mousedown';
    var pointerMove = hasPointerEvents ? 'pointermove' : hasTouchEvents ? 'touchmove' : 'mousemove';
    var pointerUp = hasPointerEvents ? 'pointerup' : hasTouchEvents ? 'touchend' : 'mouseup';
    var pointerEnter = hasPointerEvents ? 'pointerenter' : hasTouchEvents ? '' : 'mouseenter';
    var pointerLeave = hasPointerEvents ? 'pointerleave' : hasTouchEvents ? '' : 'mouseleave';
    var pointerCancel = hasPointerEvents ? 'pointercancel' : 'touchcancel';

    function ready(fn) {

        if (document.readyState !== 'loading') {
            fn();
            return;
        }

        var unbind = on(document, 'DOMContentLoaded', function () {
            unbind();
            fn();
        });
    }

    function index(element, ref) {
        return ref
            ? toNodes(element).indexOf(toNode(ref))
            : toNodes((element = toNode(element)) && element.parentNode.children).indexOf(element);
    }

    function getIndex(i, elements, current, finite) {
        if ( current === void 0 ) current = 0;
        if ( finite === void 0 ) finite = false;


        elements = toNodes(elements);

        var length = elements.length;

        i = isNumeric(i)
            ? toNumber(i)
            : i === 'next'
                ? current + 1
                : i === 'previous'
                    ? current - 1
                    : index(elements, i);

        if (finite) {
            return clamp(i, 0, length - 1);
        }

        i %= length;

        return i < 0 ? i + length : i;
    }

    function empty(element) {
        element = $(element);
        element.innerHTML = '';
        return element;
    }

    function html(parent, html) {
        parent = $(parent);
        return isUndefined(html)
            ? parent.innerHTML
            : append(parent.hasChildNodes() ? empty(parent) : parent, html);
    }

    function prepend(parent, element) {

        parent = $(parent);

        if (!parent.hasChildNodes()) {
            return append(parent, element);
        } else {
            return insertNodes(element, function (element) { return parent.insertBefore(element, parent.firstChild); });
        }
    }

    function append(parent, element) {
        parent = $(parent);
        return insertNodes(element, function (element) { return parent.appendChild(element); });
    }

    function before(ref, element) {
        ref = $(ref);
        return insertNodes(element, function (element) { return ref.parentNode.insertBefore(element, ref); });
    }

    function after(ref, element) {
        ref = $(ref);
        return insertNodes(element, function (element) { return ref.nextSibling
            ? before(ref.nextSibling, element)
            : append(ref.parentNode, element); }
        );
    }

    function insertNodes(element, fn) {
        element = isString(element) ? fragment(element) : element;
        return element
            ? 'length' in element
                ? toNodes(element).map(fn)
                : fn(element)
            : null;
    }

    function remove(element) {
        toNodes(element).map(function (element) { return element.parentNode && element.parentNode.removeChild(element); });
    }

    function wrapAll(element, structure) {

        structure = toNode(before(element, structure));

        while (structure.firstChild) {
            structure = structure.firstChild;
        }

        append(structure, element);

        return structure;
    }

    function wrapInner(element, structure) {
        return toNodes(toNodes(element).map(function (element) { return element.hasChildNodes ? wrapAll(toNodes(element.childNodes), structure) : append(element, structure); }
        ));
    }

    function unwrap(element) {
        toNodes(element)
            .map(function (element) { return element.parentNode; })
            .filter(function (value, index, self) { return self.indexOf(value) === index; })
            .forEach(function (parent) {
                before(parent, parent.childNodes);
                remove(parent);
            });
    }

    var fragmentRe = /^\s*<(\w+|!)[^>]*>/;
    var singleTagRe = /^<(\w+)\s*\/?>(?:<\/\1>)?$/;

    function fragment(html) {

        var matches = singleTagRe.exec(html);
        if (matches) {
            return document.createElement(matches[1]);
        }

        var container = document.createElement('div');
        if (fragmentRe.test(html)) {
            container.insertAdjacentHTML('beforeend', html.trim());
        } else {
            container.textContent = html;
        }

        return container.childNodes.length > 1 ? toNodes(container.childNodes) : container.firstChild;

    }

    function apply(node, fn) {

        if (!node || node.nodeType !== 1) {
            return;
        }

        fn(node);
        node = node.firstElementChild;
        while (node) {
            apply(node, fn);
            node = node.nextElementSibling;
        }
    }

    function $(selector, context) {
        return !isString(selector)
            ? toNode(selector)
            : isHtml(selector)
                ? toNode(fragment(selector))
                : find(selector, context);
    }

    function $$(selector, context) {
        return !isString(selector)
            ? toNodes(selector)
            : isHtml(selector)
                ? toNodes(fragment(selector))
                : findAll(selector, context);
    }

    function isHtml(str) {
        return str[0] === '<' || str.match(/^\s*</);
    }

    function addClass(element) {
        var args = [], len = arguments.length - 1;
        while ( len-- > 0 ) args[ len ] = arguments[ len + 1 ];

        apply$1(element, args, 'add');
    }

    function removeClass(element) {
        var args = [], len = arguments.length - 1;
        while ( len-- > 0 ) args[ len ] = arguments[ len + 1 ];

        apply$1(element, args, 'remove');
    }

    function removeClasses(element, cls) {
        attr(element, 'class', function (value) { return (value || '').replace(new RegExp(("\\b" + cls + "\\b"), 'g'), ''); });
    }

    function replaceClass(element) {
        var args = [], len = arguments.length - 1;
        while ( len-- > 0 ) args[ len ] = arguments[ len + 1 ];

        args[0] && removeClass(element, args[0]);
        args[1] && addClass(element, args[1]);
    }

    function hasClass(element, cls) {
        return cls && toNodes(element).some(function (element) { return element.classList.contains(cls.split(' ')[0]); });
    }

    function toggleClass(element) {
        var args = [], len = arguments.length - 1;
        while ( len-- > 0 ) args[ len ] = arguments[ len + 1 ];


        if (!args.length) {
            return;
        }

        args = getArgs$1(args);

        var force = !isString(args[args.length - 1]) ? args.pop() : []; // in iOS 9.3 force === undefined evaluates to false

        args = args.filter(Boolean);

        toNodes(element).forEach(function (ref) {
            var classList = ref.classList;

            for (var i = 0; i < args.length; i++) {
                supports.Force
                    ? classList.toggle.apply(classList, [args[i]].concat(force))
                    : (classList[(!isUndefined(force) ? force : !classList.contains(args[i])) ? 'add' : 'remove'](args[i]));
            }
        });

    }

    function apply$1(element, args, fn) {
        args = getArgs$1(args).filter(Boolean);

        args.length && toNodes(element).forEach(function (ref) {
            var classList = ref.classList;

            supports.Multiple
                ? classList[fn].apply(classList, args)
                : args.forEach(function (cls) { return classList[fn](cls); });
        });
    }

    function getArgs$1(args) {
        return args.reduce(function (args, arg) { return args.concat.call(args, isString(arg) && includes(arg, ' ') ? arg.trim().split(' ') : arg); }
            , []);
    }

    // IE 11
    var supports = {

        get Multiple() {
            return this.get('_multiple');
        },

        get Force() {
            return this.get('_force');
        },

        get: function(key) {

            if (!hasOwn(this, key)) {
                var ref = document.createElement('_');
                var classList = ref.classList;
                classList.add('a', 'b');
                classList.toggle('c', false);
                this._multiple = classList.contains('b');
                this._force = !classList.contains('c');
            }

            return this[key];
        }

    };

    var cssNumber = {
        'animation-iteration-count': true,
        'column-count': true,
        'fill-opacity': true,
        'flex-grow': true,
        'flex-shrink': true,
        'font-weight': true,
        'line-height': true,
        'opacity': true,
        'order': true,
        'orphans': true,
        'stroke-dasharray': true,
        'stroke-dashoffset': true,
        'widows': true,
        'z-index': true,
        'zoom': true
    };

    function css(element, property, value) {

        return toNodes(element).map(function (element) {

            if (isString(property)) {

                property = propName(property);

                if (isUndefined(value)) {
                    return getStyle(element, property);
                } else if (!value && !isNumber(value)) {
                    element.style.removeProperty(property);
                } else {
                    element.style[property] = isNumeric(value) && !cssNumber[property] ? (value + "px") : value;
                }

            } else if (isArray(property)) {

                var styles = getStyles(element);

                return property.reduce(function (props, property) {
                    props[property] = styles[propName(property)];
                    return props;
                }, {});

            } else if (isObject(property)) {
                each(property, function (value, property) { return css(element, property, value); });
            }

            return element;

        })[0];

    }

    function getStyles(element, pseudoElt) {
        element = toNode(element);
        return element.ownerDocument.defaultView.getComputedStyle(element, pseudoElt);
    }

    function getStyle(element, property, pseudoElt) {
        return getStyles(element, pseudoElt)[property];
    }

    var vars = {};

    function getCssVar(name) {

        var docEl = document.documentElement;

        if (!isIE) {
            return getStyles(docEl).getPropertyValue(("--gsl-" + name));
        }

        if (!(name in vars)) {

            /* usage in css: .gsl-name:before { content:"xyz" } */

            var element = append(docEl, document.createElement('div'));

            addClass(element, ("gsl-" + name));

            vars[name] = getStyle(element, 'content', ':before').replace(/^["'](.*)["']$/, '$1');

            remove(element);

        }

        return vars[name];

    }

    var cssProps = {};

    function propName(name) {

        var ret = cssProps[name];
        if (!ret) {
            ret = cssProps[name] = vendorPropName(name) || name;
        }
        return ret;
    }

    var cssPrefixes = ['webkit', 'moz', 'ms'];

    function vendorPropName(name) {

        name = hyphenate(name);

        var ref = document.documentElement;
        var style = ref.style;

        if (name in style) {
            return name;
        }

        var i = cssPrefixes.length, prefixedName;

        while (i--) {
            prefixedName = "-" + (cssPrefixes[i]) + "-" + name;
            if (prefixedName in style) {
                return prefixedName;
            }
        }
    }

    function transition(element, props, duration, timing) {
        if ( duration === void 0 ) duration = 400;
        if ( timing === void 0 ) timing = 'linear';


        return Promise.all(toNodes(element).map(function (element) { return new Promise(function (resolve, reject) {

                for (var name in props) {
                    var value = css(element, name);
                    if (value === '') {
                        css(element, name, value);
                    }
                }

                var timer = setTimeout(function () { return trigger(element, 'transitionend'); }, duration);

                once(element, 'transitionend transitioncanceled', function (ref) {
                    var type = ref.type;

                    clearTimeout(timer);
                    removeClass(element, 'gsl-transition');
                    css(element, {
                        'transition-property': '',
                        'transition-duration': '',
                        'transition-timing-function': ''
                    });
                    type === 'transitioncanceled' ? reject() : resolve();
                }, false, function (ref) {
                    var target = ref.target;

                    return element === target;
                });

                addClass(element, 'gsl-transition');
                css(element, assign({
                    'transition-property': Object.keys(props).map(propName).join(','),
                    'transition-duration': (duration + "ms"),
                    'transition-timing-function': timing
                }, props));

            }); }
        ));

    }

    var Transition = {

        start: transition,

        stop: function(element) {
            trigger(element, 'transitionend');
            return Promise.resolve();
        },

        cancel: function(element) {
            trigger(element, 'transitioncanceled');
        },

        inProgress: function(element) {
            return hasClass(element, 'gsl-transition');
        }

    };

    var animationPrefix = 'gsl-animation-';
    var clsCancelAnimation = 'gsl-cancel-animation';

    function animate(element, animation, duration, origin, out) {
        var arguments$1 = arguments;
        if ( duration === void 0 ) duration = 200;


        return Promise.all(toNodes(element).map(function (element) { return new Promise(function (resolve, reject) {

                if (hasClass(element, clsCancelAnimation)) {
                    requestAnimationFrame(function () { return Promise.resolve().then(function () { return animate.apply(void 0, arguments$1).then(resolve, reject); }
                        ); }
                    );
                    return;
                }

                var cls = animation + " " + animationPrefix + (out ? 'leave' : 'enter');

                if (startsWith(animation, animationPrefix)) {

                    if (origin) {
                        cls += " gsl-transform-origin-" + origin;
                    }

                    if (out) {
                        cls += " " + animationPrefix + "reverse";
                    }

                }

                reset();

                once(element, 'animationend animationcancel', function (ref) {
                    var type = ref.type;


                    var hasReset = false;

                    if (type === 'animationcancel') {
                        reject();
                        reset();
                    } else {
                        resolve();
                        Promise.resolve().then(function () {
                            hasReset = true;
                            reset();
                        });
                    }

                    requestAnimationFrame(function () {
                        if (!hasReset) {
                            addClass(element, clsCancelAnimation);

                            requestAnimationFrame(function () { return removeClass(element, clsCancelAnimation); });
                        }
                    });

                }, false, function (ref) {
                    var target = ref.target;

                    return element === target;
                });

                css(element, 'animationDuration', (duration + "ms"));
                addClass(element, cls);

                function reset() {
                    css(element, 'animationDuration', '');
                    removeClasses(element, (animationPrefix + "\\S*"));
                }

            }); }
        ));

    }

    var inProgress = new RegExp((animationPrefix + "(enter|leave)"));
    var Animation = {

        in: function(element, animation, duration, origin) {
            return animate(element, animation, duration, origin, false);
        },

        out: function(element, animation, duration, origin) {
            return animate(element, animation, duration, origin, true);
        },

        inProgress: function(element) {
            return inProgress.test(attr(element, 'class'));
        },

        cancel: function(element) {
            trigger(element, 'animationcancel');
        }

    };

    var dirs = {
        width: ['x', 'left', 'right'],
        height: ['y', 'top', 'bottom']
    };

    function positionAt(element, target, elAttach, targetAttach, elOffset, targetOffset, flip, boundary) {

        elAttach = getPos(elAttach);
        targetAttach = getPos(targetAttach);

        var flipped = {element: elAttach, target: targetAttach};

        if (!element || !target) {
            return flipped;
        }

        var dim = getDimensions(element);
        var targetDim = getDimensions(target);
        var position = targetDim;

        moveTo(position, elAttach, dim, -1);
        moveTo(position, targetAttach, targetDim, 1);

        elOffset = getOffsets(elOffset, dim.width, dim.height);
        targetOffset = getOffsets(targetOffset, targetDim.width, targetDim.height);

        elOffset['x'] += targetOffset['x'];
        elOffset['y'] += targetOffset['y'];

        position.left += elOffset['x'];
        position.top += elOffset['y'];

        if (flip) {

            var boundaries = [getDimensions(getWindow(element))];

            if (boundary) {
                boundaries.unshift(getDimensions(boundary));
            }

            each(dirs, function (ref, prop) {
                var dir = ref[0];
                var align = ref[1];
                var alignFlip = ref[2];


                if (!(flip === true || includes(flip, dir))) {
                    return;
                }

                boundaries.some(function (boundary) {

                    var elemOffset = elAttach[dir] === align
                        ? -dim[prop]
                        : elAttach[dir] === alignFlip
                            ? dim[prop]
                            : 0;

                    var targetOffset = targetAttach[dir] === align
                        ? targetDim[prop]
                        : targetAttach[dir] === alignFlip
                            ? -targetDim[prop]
                            : 0;

                    if (position[align] < boundary[align] || position[align] + dim[prop] > boundary[alignFlip]) {

                        var centerOffset = dim[prop] / 2;
                        var centerTargetOffset = targetAttach[dir] === 'center' ? -targetDim[prop] / 2 : 0;

                        return elAttach[dir] === 'center' && (
                            apply(centerOffset, centerTargetOffset)
                            || apply(-centerOffset, -centerTargetOffset)
                        ) || apply(elemOffset, targetOffset);

                    }

                    function apply(elemOffset, targetOffset) {

                        var newVal = position[align] + elemOffset + targetOffset - elOffset[dir] * 2;

                        if (newVal >= boundary[align] && newVal + dim[prop] <= boundary[alignFlip]) {
                            position[align] = newVal;

                            ['element', 'target'].forEach(function (el) {
                                flipped[el][dir] = !elemOffset
                                    ? flipped[el][dir]
                                    : flipped[el][dir] === dirs[prop][1]
                                        ? dirs[prop][2]
                                        : dirs[prop][1];
                            });

                            return true;
                        }

                    }

                });

            });
        }

        offset(element, position);

        return flipped;
    }

    function offset(element, coordinates) {

        element = toNode(element);

        if (coordinates) {

            var currentOffset = offset(element);
            var pos = css(element, 'position');

            ['left', 'top'].forEach(function (prop) {
                if (prop in coordinates) {
                    var value = css(element, prop);
                    css(element, prop, coordinates[prop] - currentOffset[prop]
                        + toFloat(pos === 'absolute' && value === 'auto'
                            ? position(element)[prop]
                            : value)
                    );
                }
            });

            return;
        }

        return getDimensions(element);
    }

    function getDimensions(element) {

        element = toNode(element);

        var ref = getWindow(element);
        var top = ref.pageYOffset;
        var left = ref.pageXOffset;

        if (isWindow(element)) {

            var height = element.innerHeight;
            var width = element.innerWidth;

            return {
                top: top,
                left: left,
                height: height,
                width: width,
                bottom: top + height,
                right: left + width
            };
        }

        var style, hidden;

        if (!isVisible(element) && css(element, 'display') === 'none') {

            style = attr(element, 'style');
            hidden = attr(element, 'hidden');

            attr(element, {
                style: ((style || '') + ";display:block !important;"),
                hidden: null
            });
        }

        var rect = element.getBoundingClientRect();

        if (!isUndefined(style)) {
            attr(element, {style: style, hidden: hidden});
        }

        return {
            height: rect.height,
            width: rect.width,
            top: rect.top + top,
            left: rect.left + left,
            bottom: rect.bottom + top,
            right: rect.right + left
        };
    }

    function position(element) {
        element = toNode(element);

        var parent = element.offsetParent || getDocEl(element);
        var parentOffset = offset(parent);
        var ref = ['top', 'left'].reduce(function (props, prop) {
            var propName = ucfirst(prop);
            props[prop] -= parentOffset[prop]
                + toFloat(css(element, ("margin" + propName)))
                + toFloat(css(parent, ("border" + propName + "Width")));
            return props;
        }, offset(element));
        var top = ref.top;
        var left = ref.left;

        return {top: top, left: left};
    }

    var height = dimension('height');
    var width = dimension('width');

    function dimension(prop) {
        var propName = ucfirst(prop);
        return function (element, value) {

            element = toNode(element);

            if (isUndefined(value)) {

                if (isWindow(element)) {
                    return element[("inner" + propName)];
                }

                if (isDocument(element)) {
                    var doc = element.documentElement;
                    return Math.max(doc[("offset" + propName)], doc[("scroll" + propName)]);
                }

                value = css(element, prop);
                value = value === 'auto' ? element[("offset" + propName)] : toFloat(value) || 0;

                return value - boxModelAdjust(prop, element);

            } else {

                css(element, prop, !value && value !== 0
                    ? ''
                    : +value + boxModelAdjust(prop, element) + 'px'
                );

            }

        };
    }

    function boxModelAdjust(prop, element, sizing) {
        if ( sizing === void 0 ) sizing = 'border-box';

        return css(element, 'boxSizing') === sizing
            ? dirs[prop].slice(1).map(ucfirst).reduce(function (value, prop) { return value
                + toFloat(css(element, ("padding" + prop)))
                + toFloat(css(element, ("border" + prop + "Width"))); }
                , 0)
            : 0;
    }

    function moveTo(position, attach, dim, factor) {
        each(dirs, function (ref, prop) {
            var dir = ref[0];
            var align = ref[1];
            var alignFlip = ref[2];

            if (attach[dir] === alignFlip) {
                position[align] += dim[prop] * factor;
            } else if (attach[dir] === 'center') {
                position[align] += dim[prop] * factor / 2;
            }
        });
    }

    function getPos(pos) {

        var x = /left|center|right/;
        var y = /top|center|bottom/;

        pos = (pos || '').split(' ');

        if (pos.length === 1) {
            pos = x.test(pos[0])
                ? pos.concat(['center'])
                : y.test(pos[0])
                    ? ['center'].concat(pos)
                    : ['center', 'center'];
        }

        return {
            x: x.test(pos[0]) ? pos[0] : 'center',
            y: y.test(pos[1]) ? pos[1] : 'center'
        };
    }

    function getOffsets(offsets, width, height) {

        var ref = (offsets || '').split(' ');
        var x = ref[0];
        var y = ref[1];

        return {
            x: x ? toFloat(x) * (endsWith(x, '%') ? width / 100 : 1) : 0,
            y: y ? toFloat(y) * (endsWith(y, '%') ? height / 100 : 1) : 0
        };
    }

    function flipPosition(pos) {
        switch (pos) {
            case 'left':
                return 'right';
            case 'right':
                return 'left';
            case 'top':
                return 'bottom';
            case 'bottom':
                return 'top';
            default:
                return pos;
        }
    }

    function isInView(element, topOffset, leftOffset) {
        if ( topOffset === void 0 ) topOffset = 0;
        if ( leftOffset === void 0 ) leftOffset = 0;


        if (!isVisible(element)) {
            return false;
        }

        element = toNode(element);

        var win = getWindow(element);
        var client = element.getBoundingClientRect();
        var bounding = {
            top: -topOffset,
            left: -leftOffset,
            bottom: topOffset + height(win),
            right: leftOffset + width(win)
        };

        return intersectRect(client, bounding) || pointInRect({x: client.left, y: client.top}, bounding);

    }

    function scrolledOver(element, heightOffset) {
        if ( heightOffset === void 0 ) heightOffset = 0;


        if (!isVisible(element)) {
            return 0;
        }

        element = toNode(element);

        var win = getWindow(element);
        var doc = getDocument(element);
        var elHeight = element.offsetHeight + heightOffset;
        var ref = offsetPosition(element);
        var top = ref[0];
        var vp = height(win);
        var vh = vp + Math.min(0, top - vp);
        var diff = Math.max(0, vp - (height(doc) + heightOffset - (top + elHeight)));

        return clamp(((vh + win.pageYOffset - top) / ((vh + (elHeight - (diff < vp ? diff : 0))) / 100)) / 100);
    }

    function scrollTop(element, top) {
        element = toNode(element);

        if (isWindow(element) || isDocument(element)) {
            var ref = getWindow(element);
            var scrollTo = ref.scrollTo;
            var pageXOffset = ref.pageXOffset;
            scrollTo(pageXOffset, top);
        } else {
            element.scrollTop = top;
        }
    }

    function offsetPosition(element) {
        var offset = [0, 0];

        do {

            offset[0] += element.offsetTop;
            offset[1] += element.offsetLeft;

            if (css(element, 'position') === 'fixed') {
                var win = getWindow(element);
                offset[0] += win.pageYOffset;
                offset[1] += win.pageXOffset;
                return offset;
            }

        } while ((element = element.offsetParent));

        return offset;
    }

    function toPx(value, property, element) {
        if ( property === void 0 ) property = 'width';
        if ( element === void 0 ) element = window;

        return isNumeric(value)
            ? +value
            : endsWith(value, 'vh')
                ? percent(height(getWindow(element)), value)
                : endsWith(value, 'vw')
                    ? percent(width(getWindow(element)), value)
                    : endsWith(value, '%')
                        ? percent(getDimensions(element)[property], value)
                        : toFloat(value);
    }

    function percent(base, value) {
        return base * toFloat(value) / 100;
    }

    function getWindow(element) {
        return isWindow(element) ? element : getDocument(element).defaultView;
    }

    function getDocument(element) {
        return toNode(element).ownerDocument;
    }

    function getDocEl(element) {
        return getDocument(element).documentElement;
    }

    /*
        Based on:
        Copyright (c) 2016 Wilson Page wilsonpage@me.com
        https://github.com/wilsonpage/fastdom
    */

    var fastdom = {

        reads: [],
        writes: [],

        read: function(task) {
            this.reads.push(task);
            scheduleFlush();
            return task;
        },

        write: function(task) {
            this.writes.push(task);
            scheduleFlush();
            return task;
        },

        clear: function(task) {
            return remove$1(this.reads, task) || remove$1(this.writes, task);
        },

        flush: function() {

            runTasks(this.reads);
            runTasks(this.writes.splice(0, this.writes.length));

            this.scheduled = false;

            if (this.reads.length || this.writes.length) {
                scheduleFlush();
            }

        }

    };

    function scheduleFlush() {
        if (!fastdom.scheduled) {
            fastdom.scheduled = true;
            requestAnimationFrame(fastdom.flush.bind(fastdom));
        }
    }

    function runTasks(tasks) {
        var task;
        while ((task = tasks.shift())) {
            task();
        }
    }

    function remove$1(array, item) {
        var index = array.indexOf(item);
        return !!~index && !!array.splice(index, 1);
    }

    function MouseTracker() {}

    MouseTracker.prototype = {

        positions: [],
        position: null,

        init: function() {
            var this$1 = this;


            this.positions = [];
            this.position = null;

            var ticking = false;
            this.unbind = on(document, 'mousemove', function (e) {

                if (ticking) {
                    return;
                }

                setTimeout(function () {

                    var time = Date.now();
                    var ref = this$1.positions;
                    var length = ref.length;

                    if (length && (time - this$1.positions[length - 1].time > 100)) {
                        this$1.positions.splice(0, length);
                    }

                    this$1.positions.push({time: time, x: e.pageX, y: e.pageY});

                    if (this$1.positions.length > 5) {
                        this$1.positions.shift();
                    }

                    ticking = false;
                }, 5);

                ticking = true;
            });

        },

        cancel: function() {
            if (this.unbind) {
                this.unbind();
            }
        },

        movesTo: function(target) {

            if (this.positions.length < 2) {
                return false;
            }

            var p = offset(target);
            var position = this.positions[this.positions.length - 1];
            var ref = this.positions;
            var prevPos = ref[0];

            if (p.left <= position.x && position.x <= p.right && p.top <= position.y && position.y <= p.bottom) {
                return false;
            }

            var points = [
                [{x: p.left, y: p.top}, {x: p.right, y: p.bottom}],
                [{x: p.right, y: p.top}, {x: p.left, y: p.bottom}]
            ];

            if (p.right <= position.x) ; else if (p.left >= position.x) {
                points[0].reverse();
                points[1].reverse();
            } else if (p.bottom <= position.y) {
                points[0].reverse();
            } else if (p.top >= position.y) {
                points[1].reverse();
            }

            return !!points.reduce(function (result, point) {
                return result + (slope(prevPos, point[0]) < slope(position, point[0]) && slope(prevPos, point[1]) > slope(position, point[1]));
            }, 0);
        }

    };

    function slope(a, b) {
        return (b.y - a.y) / (b.x - a.x);
    }

    var strats = {};

    strats.events =
    strats.created =
    strats.beforeConnect =
    strats.connected =
    strats.beforeDisconnect =
    strats.disconnected =
    strats.destroy = concatStrat;

    // args strategy
    strats.args = function (parentVal, childVal) {
        return childVal !== false && concatStrat(childVal || parentVal);
    };

    // update strategy
    strats.update = function (parentVal, childVal) {
        return sortBy(concatStrat(parentVal, isFunction(childVal) ? {read: childVal} : childVal), 'order');
    };

    // property strategy
    strats.props = function (parentVal, childVal) {

        if (isArray(childVal)) {
            childVal = childVal.reduce(function (value, key) {
                value[key] = String;
                return value;
            }, {});
        }

        return strats.methods(parentVal, childVal);
    };

    // extend strategy
    strats.computed =
    strats.methods = function (parentVal, childVal) {
        return childVal
            ? parentVal
                ? assign({}, parentVal, childVal)
                : childVal
            : parentVal;
    };

    // data strategy
    strats.data = function (parentVal, childVal, vm) {

        if (!vm) {

            if (!childVal) {
                return parentVal;
            }

            if (!parentVal) {
                return childVal;
            }

            return function (vm) {
                return mergeFnData(parentVal, childVal, vm);
            };

        }

        return mergeFnData(parentVal, childVal, vm);
    };

    function mergeFnData(parentVal, childVal, vm) {
        return strats.computed(
            isFunction(parentVal)
                ? parentVal.call(vm, vm)
                : parentVal,
            isFunction(childVal)
                ? childVal.call(vm, vm)
                : childVal
        );
    }

    // concat strategy
    function concatStrat(parentVal, childVal) {

        parentVal = parentVal && !isArray(parentVal) ? [parentVal] : parentVal;

        return childVal
            ? parentVal
                ? parentVal.concat(childVal)
                : isArray(childVal)
                    ? childVal
                    : [childVal]
            : parentVal;
    }

    // default strategy
    function defaultStrat(parentVal, childVal) {
        return isUndefined(childVal) ? parentVal : childVal;
    }

    function mergeOptions(parent, child, vm) {

        var options = {};

        if (isFunction(child)) {
            child = child.options;
        }

        if (child.extends) {
            parent = mergeOptions(parent, child.extends, vm);
        }

        if (child.mixins) {
            for (var i = 0, l = child.mixins.length; i < l; i++) {
                parent = mergeOptions(parent, child.mixins[i], vm);
            }
        }

        for (var key in parent) {
            mergeKey(key);
        }

        for (var key$1 in child) {
            if (!hasOwn(parent, key$1)) {
                mergeKey(key$1);
            }
        }

        function mergeKey(key) {
            options[key] = (strats[key] || defaultStrat)(parent[key], child[key], vm);
        }

        return options;
    }

    function parseOptions(options, args) {
        var obj;

        if ( args === void 0 ) args = [];

        try {

            return !options
                ? {}
                : startsWith(options, '{')
                    ? JSON.parse(options)
                    : args.length && !includes(options, ':')
                        ? (( obj = {}, obj[args[0]] = options, obj ))
                        : options.split(';').reduce(function (options, option) {
                            var ref = option.split(/:(.*)/);
                            var key = ref[0];
                            var value = ref[1];
                            if (key && !isUndefined(value)) {
                                options[key.trim()] = value.trim();
                            }
                            return options;
                        }, {});

        } catch (e) {
            return {};
        }

    }

    var id = 0;

    var Player = function(el) {
        this.id = ++id;
        this.el = toNode(el);
    };

    Player.prototype.isVideo = function () {
        return this.isYoutube() || this.isVimeo() || this.isHTML5();
    };

    Player.prototype.isHTML5 = function () {
        return this.el.tagName === 'VIDEO';
    };

    Player.prototype.isIFrame = function () {
        return this.el.tagName === 'IFRAME';
    };

    Player.prototype.isYoutube = function () {
        return this.isIFrame() && !!this.el.src.match(/\/\/.*?youtube(-nocookie)?\.[a-z]+\/(watch\?v=[^&\s]+|embed)|youtu\.be\/.*/);
    };

    Player.prototype.isVimeo = function () {
        return this.isIFrame() && !!this.el.src.match(/vimeo\.com\/video\/.*/);
    };

    Player.prototype.enableApi = function () {
            var this$1 = this;


        if (this.ready) {
            return this.ready;
        }

        var youtube = this.isYoutube();
        var vimeo = this.isVimeo();

        var poller;

        if (youtube || vimeo) {

            return this.ready = new Promise(function (resolve) {

                once(this$1.el, 'load', function () {
                    if (youtube) {
                        var listener = function () { return post(this$1.el, {event: 'listening', id: this$1.id}); };
                        poller = setInterval(listener, 100);
                        listener();
                    }
                });

                listen(function (data) { return youtube && data.id === this$1.id && data.event === 'onReady' || vimeo && Number(data.player_id) === this$1.id; })
                    .then(function () {
                        resolve();
                        poller && clearInterval(poller);
                    });

                attr(this$1.el, 'src', ("" + (this$1.el.src) + (includes(this$1.el.src, '?') ? '&' : '?') + (youtube ? 'enablejsapi=1' : ("api=1&player_id=" + (this$1.id)))));

            });

        }

        return Promise.resolve();

    };

    Player.prototype.play = function () {
            var this$1 = this;


        if (!this.isVideo()) {
            return;
        }

        if (this.isIFrame()) {
            this.enableApi().then(function () { return post(this$1.el, {func: 'playVideo', method: 'play'}); });
        } else if (this.isHTML5()) {
            try {
                var promise = this.el.play();

                if (promise) {
                    promise.catch(noop);
                }
            } catch (e) {}
        }
    };

    Player.prototype.pause = function () {
            var this$1 = this;


        if (!this.isVideo()) {
            return;
        }

        if (this.isIFrame()) {
            this.enableApi().then(function () { return post(this$1.el, {func: 'pauseVideo', method: 'pause'}); });
        } else if (this.isHTML5()) {
            this.el.pause();
        }
    };

    Player.prototype.mute = function () {
            var this$1 = this;


        if (!this.isVideo()) {
            return;
        }

        if (this.isIFrame()) {
            this.enableApi().then(function () { return post(this$1.el, {func: 'mute', method: 'setVolume', value: 0}); });
        } else if (this.isHTML5()) {
            this.el.muted = true;
            attr(this.el, 'muted', '');
        }

    };

    function post(el, cmd) {
        try {
            el.contentWindow.postMessage(JSON.stringify(assign({event: 'command'}, cmd)), '*');
        } catch (e) {}
    }

    function listen(cb) {

        return new Promise(function (resolve) {

            once(window, 'message', function (_, data) { return resolve(data); }, false, function (ref) {
                var data = ref.data;


                if (!data || !isString(data)) {
                    return;
                }

                try {
                    data = JSON.parse(data);
                } catch (e) {
                    return;
                }

                return data && cb(data);

            });

        });

    }

    var IntersectionObserver = 'IntersectionObserver' in window
        ? window.IntersectionObserver
        : /*@__PURE__*/(function () {
        function IntersectionObserverClass(callback, ref) {
            var this$1 = this;
            if ( ref === void 0 ) ref = {};
            var rootMargin = ref.rootMargin; if ( rootMargin === void 0 ) rootMargin = '0 0';


                this.targets = [];

                var ref$1 = (rootMargin || '0 0').split(' ').map(toFloat);
            var offsetTop = ref$1[0];
            var offsetLeft = ref$1[1];

                this.offsetTop = offsetTop;
                this.offsetLeft = offsetLeft;

                var pending;
                this.apply = function () {

                    if (pending) {
                        return;
                    }

                    pending = requestAnimationFrame(function () { return setTimeout(function () {
                        var records = this$1.takeRecords();

                        if (records.length) {
                            callback(records, this$1);
                        }

                        pending = false;
                    }); });

                };

                this.off = on(window, 'scroll resize load', this.apply, {passive: true, capture: true});

            }

            IntersectionObserverClass.prototype.takeRecords = function () {
                var this$1 = this;

                return this.targets.filter(function (entry) {

                    var inView = isInView(entry.target, this$1.offsetTop, this$1.offsetLeft);

                    if (entry.isIntersecting === null || inView ^ entry.isIntersecting) {
                        entry.isIntersecting = inView;
                        return true;
                    }

                });
            };

            IntersectionObserverClass.prototype.observe = function (target) {
                this.targets.push({
                    target: target,
                    isIntersecting: null
                });
                this.apply();
            };

            IntersectionObserverClass.prototype.disconnect = function () {
                this.targets = [];
                this.off();
            };

        return IntersectionObserverClass;
    }());



    var util = /*#__PURE__*/Object.freeze({
        ajax: ajax,
        getImage: getImage,
        transition: transition,
        Transition: Transition,
        animate: animate,
        Animation: Animation,
        attr: attr,
        hasAttr: hasAttr,
        removeAttr: removeAttr,
        data: data,
        addClass: addClass,
        removeClass: removeClass,
        removeClasses: removeClasses,
        replaceClass: replaceClass,
        hasClass: hasClass,
        toggleClass: toggleClass,
        positionAt: positionAt,
        offset: offset,
        position: position,
        height: height,
        width: width,
        boxModelAdjust: boxModelAdjust,
        flipPosition: flipPosition,
        isInView: isInView,
        scrolledOver: scrolledOver,
        scrollTop: scrollTop,
        offsetPosition: offsetPosition,
        toPx: toPx,
        ready: ready,
        index: index,
        getIndex: getIndex,
        empty: empty,
        html: html,
        prepend: prepend,
        append: append,
        before: before,
        after: after,
        remove: remove,
        wrapAll: wrapAll,
        wrapInner: wrapInner,
        unwrap: unwrap,
        fragment: fragment,
        apply: apply,
        $: $,
        $$: $$,
        isIE: isIE,
        isRtl: isRtl,
        hasTouch: hasTouch,
        pointerDown: pointerDown,
        pointerMove: pointerMove,
        pointerUp: pointerUp,
        pointerEnter: pointerEnter,
        pointerLeave: pointerLeave,
        pointerCancel: pointerCancel,
        on: on,
        off: off,
        once: once,
        trigger: trigger,
        createEvent: createEvent,
        toEventTargets: toEventTargets,
        isTouch: isTouch,
        getEventPos: getEventPos,
        fastdom: fastdom,
        isVoidElement: isVoidElement,
        isVisible: isVisible,
        selInput: selInput,
        isInput: isInput,
        filter: filter,
        within: within,
        bind: bind,
        hasOwn: hasOwn,
        hyphenate: hyphenate,
        camelize: camelize,
        ucfirst: ucfirst,
        startsWith: startsWith,
        endsWith: endsWith,
        includes: includes,
        findIndex: findIndex,
        isArray: isArray,
        isFunction: isFunction,
        isObject: isObject,
        isPlainObject: isPlainObject,
        isWindow: isWindow,
        isDocument: isDocument,
        isJQuery: isJQuery,
        isNode: isNode,
        isNodeCollection: isNodeCollection,
        isBoolean: isBoolean,
        isString: isString,
        isNumber: isNumber,
        isNumeric: isNumeric,
        isEmpty: isEmpty,
        isUndefined: isUndefined,
        toBoolean: toBoolean,
        toNumber: toNumber,
        toFloat: toFloat,
        toNode: toNode,
        toNodes: toNodes,
        toList: toList,
        toMs: toMs,
        isEqual: isEqual,
        swap: swap,
        assign: assign,
        each: each,
        sortBy: sortBy,
        uniqueBy: uniqueBy,
        clamp: clamp,
        noop: noop,
        intersectRect: intersectRect,
        pointInRect: pointInRect,
        Dimensions: Dimensions,
        MouseTracker: MouseTracker,
        mergeOptions: mergeOptions,
        parseOptions: parseOptions,
        Player: Player,
        Promise: Promise,
        Deferred: Deferred,
        IntersectionObserver: IntersectionObserver,
        query: query,
        queryAll: queryAll,
        find: find,
        findAll: findAll,
        matches: matches,
        closest: closest,
        parents: parents,
        escape: escape,
        css: css,
        getStyles: getStyles,
        getStyle: getStyle,
        getCssVar: getCssVar,
        propName: propName
    });

    function globalAPI (gslUIkit) {

        var DATA = gslUIkit.data;

        gslUIkit.use = function (plugin) {

            if (plugin.installed) {
                return;
            }

            plugin.call(null, this);
            plugin.installed = true;

            return this;
        };

        gslUIkit.mixin = function (mixin, component) {
            component = (isString(component) ? gslUIkit.component(component) : component) || this;
            component.options = mergeOptions(component.options, mixin);
        };

        gslUIkit.extend = function (options) {

            options = options || {};

            var Super = this;
            var Sub = function gslUIkitComponent(options) {
                this._init(options);
            };

            Sub.prototype = Object.create(Super.prototype);
            Sub.prototype.constructor = Sub;
            Sub.options = mergeOptions(Super.options, options);

            Sub.super = Super;
            Sub.extend = Super.extend;

            return Sub;
        };

        gslUIkit.update = function (element, e) {

            element = element ? toNode(element) : document.body;

            path(element, function (element) { return update(element[DATA], e); });
            apply(element, function (element) { return update(element[DATA], e); });

        };

        var container;
        Object.defineProperty(gslUIkit, 'container', {

            get: function() {
                return container || document.body;
            },

            set: function(element) {
                container = $(element);
            }

        });

        function update(data, e) {

            if (!data) {
                return;
            }

            for (var name in data) {
                if (data[name]._connected) {
                    data[name]._callUpdate(e);
                }
            }

        }

        function path(node, fn) {
            if (node && node !== document.body && node.parentNode) {
                path(node.parentNode, fn);
                fn(node.parentNode);
            }
        }

    }

    function hooksAPI (gslUIkit) {

        gslUIkit.prototype._callHook = function (hook) {
            var this$1 = this;


            var handlers = this.$options[hook];

            if (handlers) {
                handlers.forEach(function (handler) { return handler.call(this$1); });
            }
        };

        gslUIkit.prototype._callConnected = function () {

            if (this._connected) {
                return;
            }

            this._data = {};
            this._computeds = {};
            this._initProps();

            this._callHook('beforeConnect');
            this._connected = true;

            this._initEvents();
            this._initObserver();

            this._callHook('connected');
            this._callUpdate();
        };

        gslUIkit.prototype._callDisconnected = function () {

            if (!this._connected) {
                return;
            }

            this._callHook('beforeDisconnect');

            if (this._observer) {
                this._observer.disconnect();
                this._observer = null;
            }

            this._unbindEvents();
            this._callHook('disconnected');

            this._connected = false;

        };

        gslUIkit.prototype._callUpdate = function (e) {
            var this$1 = this;
            if ( e === void 0 ) e = 'update';


            var type = e.type || e;

            if (includes(['update', 'resize'], type)) {
                this._callWatches();
            }

            var updates = this.$options.update;
            var ref = this._frames;
            var reads = ref.reads;
            var writes = ref.writes;

            if (!updates) {
                return;
            }

            updates.forEach(function (ref, i) {
                var read = ref.read;
                var write = ref.write;
                var events = ref.events;


                if (type !== 'update' && !includes(events, type)) {
                    return;
                }

                if (read && !includes(fastdom.reads, reads[i])) {
                    reads[i] = fastdom.read(function () {

                        var result = this$1._connected && read.call(this$1, this$1._data, type);

                        if (result === false && write) {
                            fastdom.clear(writes[i]);
                        } else if (isPlainObject(result)) {
                            assign(this$1._data, result);
                        }
                    });
                }

                if (write && !includes(fastdom.writes, writes[i])) {
                    writes[i] = fastdom.write(function () { return this$1._connected && write.call(this$1, this$1._data, type); });
                }

            });

        };

    }

    function stateAPI (gslUIkit) {

        var uid = 0;

        gslUIkit.prototype._init = function (options) {

            options = options || {};
            options.data = normalizeData(options, this.constructor.options);

            this.$options = mergeOptions(this.constructor.options, options, this);
            this.$el = null;
            this.$props = {};

            this._frames = {reads: {}, writes: {}};
            this._events = [];

            this._uid = uid++;
            this._initData();
            this._initMethods();
            this._initComputeds();
            this._callHook('created');

            if (options.el) {
                this.$mount(options.el);
            }
        };

        gslUIkit.prototype._initData = function () {

            var ref = this.$options;
            var data = ref.data; if ( data === void 0 ) data = {};

            for (var key in data) {
                this.$props[key] = this[key] = data[key];
            }
        };

        gslUIkit.prototype._initMethods = function () {

            var ref = this.$options;
            var methods = ref.methods;

            if (methods) {
                for (var key in methods) {
                    this[key] = bind(methods[key], this);
                }
            }
        };

        gslUIkit.prototype._initComputeds = function () {

            var ref = this.$options;
            var computed = ref.computed;

            this._computeds = {};

            if (computed) {
                for (var key in computed) {
                    registerComputed(this, key, computed[key]);
                }
            }
        };

        gslUIkit.prototype._callWatches = function () {

            var ref = this;
            var computed = ref.$options.computed;
            var _computeds = ref._computeds;

            for (var key in _computeds) {

                var value = _computeds[key];
                delete _computeds[key];

                if (computed[key].watch && !isEqual(value, this[key])) {
                    computed[key].watch.call(this, this[key], value);
                }

            }

        };

        gslUIkit.prototype._initProps = function (props) {

            var key;

            props = props || getProps(this.$options, this.$name);

            for (key in props) {
                if (!isUndefined(props[key])) {
                    this.$props[key] = props[key];
                }
            }

            var exclude = [this.$options.computed, this.$options.methods];
            for (key in this.$props) {
                if (key in props && notIn(exclude, key)) {
                    this[key] = this.$props[key];
                }
            }
        };

        gslUIkit.prototype._initEvents = function () {
            var this$1 = this;


            var ref = this.$options;
            var events = ref.events;

            if (events) {

                events.forEach(function (event) {

                    if (!hasOwn(event, 'handler')) {
                        for (var key in event) {
                            registerEvent(this$1, event[key], key);
                        }
                    } else {
                        registerEvent(this$1, event);
                    }

                });
            }
        };

        gslUIkit.prototype._unbindEvents = function () {
            this._events.forEach(function (unbind) { return unbind(); });
            this._events = [];
        };

        gslUIkit.prototype._initObserver = function () {
            var this$1 = this;


            var ref = this.$options;
            var attrs = ref.attrs;
            var props = ref.props;
            var el = ref.el;
            if (this._observer || !props || attrs === false) {
                return;
            }

            attrs = isArray(attrs) ? attrs : Object.keys(props);

            this._observer = new MutationObserver(function () {

                var data = getProps(this$1.$options, this$1.$name);
                if (attrs.some(function (key) { return !isUndefined(data[key]) && data[key] !== this$1.$props[key]; })) {
                    this$1.$reset();
                }

            });

            var filter = attrs.map(function (key) { return hyphenate(key); }).concat(this.$name);

            this._observer.observe(el, {
                attributes: true,
                attributeFilter: filter.concat(filter.map(function (key) { return ("data-" + key); }))
            });
        };

        function getProps(opts, name) {

            var data$1 = {};
            var args = opts.args; if ( args === void 0 ) args = [];
            var props = opts.props; if ( props === void 0 ) props = {};
            var el = opts.el;

            if (!props) {
                return data$1;
            }

            for (var key in props) {
                var prop = hyphenate(key);
                var value = data(el, prop);

                if (!isUndefined(value)) {

                    value = props[key] === Boolean && value === ''
                        ? true
                        : coerce(props[key], value);

                    if (prop === 'target' && (!value || startsWith(value, '_'))) {
                        continue;
                    }

                    data$1[key] = value;
                }
            }

            var options = parseOptions(data(el, name), args);

            for (var key$1 in options) {
                var prop$1 = camelize(key$1);
                if (props[prop$1] !== undefined) {
                    data$1[prop$1] = coerce(props[prop$1], options[key$1]);
                }
            }

            return data$1;
        }

        function registerComputed(component, key, cb) {
            Object.defineProperty(component, key, {

                enumerable: true,

                get: function() {

                    var _computeds = component._computeds;
                    var $props = component.$props;
                    var $el = component.$el;

                    if (!hasOwn(_computeds, key)) {
                        _computeds[key] = (cb.get || cb).call(component, $props, $el);
                    }

                    return _computeds[key];
                },

                set: function(value) {

                    var _computeds = component._computeds;

                    _computeds[key] = cb.set ? cb.set.call(component, value) : value;

                    if (isUndefined(_computeds[key])) {
                        delete _computeds[key];
                    }
                }

            });
        }

        function registerEvent(component, event, key) {

            if (!isPlainObject(event)) {
                event = ({name: key, handler: event});
            }

            var name = event.name;
            var el = event.el;
            var handler = event.handler;
            var capture = event.capture;
            var passive = event.passive;
            var delegate = event.delegate;
            var filter = event.filter;
            var self = event.self;
            el = isFunction(el)
                ? el.call(component)
                : el || component.$el;

            if (isArray(el)) {
                el.forEach(function (el) { return registerEvent(component, assign({}, event, {el: el}), key); });
                return;
            }

            if (!el || filter && !filter.call(component)) {
                return;
            }

            handler = detail(isString(handler) ? component[handler] : bind(handler, component));

            if (self) {
                handler = selfFilter(handler);
            }

            component._events.push(
                on(
                    el,
                    name,
                    !delegate
                        ? null
                        : isString(delegate)
                            ? delegate
                            : delegate.call(component),
                    handler,
                    isBoolean(passive)
                        ? {passive: passive, capture: capture}
                        : capture
                )
            );

        }

        function selfFilter(handler) {
            return function selfHandler(e) {
                if (e.target === e.currentTarget || e.target === e.current) {
                    return handler.call(null, e);
                }
            };
        }

        function notIn(options, key) {
            return options.every(function (arr) { return !arr || !hasOwn(arr, key); });
        }

        function detail(listener) {
            return function (e) { return isArray(e.detail) ? listener.apply(void 0, [e].concat(e.detail)) : listener(e); };
        }

        function coerce(type, value) {

            if (type === Boolean) {
                return toBoolean(value);
            } else if (type === Number) {
                return toNumber(value);
            } else if (type === 'list') {
                return toList(value);
            }

            return type ? type(value) : value;
        }

        function normalizeData(ref, ref$1) {
            var data = ref.data;
            var el = ref.el;
            var args = ref$1.args;
            var props = ref$1.props; if ( props === void 0 ) props = {};

            data = isArray(data)
                ? !isEmpty(args)
                    ? data.slice(0, args.length).reduce(function (data, value, index) {
                        if (isPlainObject(value)) {
                            assign(data, value);
                        } else {
                            data[args[index]] = value;
                        }
                        return data;
                    }, {})
                    : undefined
                : data;

            if (data) {
                for (var key in data) {
                    if (isUndefined(data[key])) {
                        delete data[key];
                    } else {
                        data[key] = props[key] ? coerce(props[key], data[key], el) : data[key];
                    }
                }
            }

            return data;
        }
    }

    function instanceAPI (gslUIkit) {

        var DATA = gslUIkit.data;

        gslUIkit.prototype.$mount = function (el) {

            var ref = this.$options;
            var name = ref.name;

            if (!el[DATA]) {
                el[DATA] = {};
            }

            if (el[DATA][name]) {
                return;
            }

            el[DATA][name] = this;

            this.$el = this.$options.el = this.$options.el || el;

            if (within(el, document)) {
                this._callConnected();
            }
        };

        gslUIkit.prototype.$emit = function (e) {
            this._callUpdate(e);
        };

        gslUIkit.prototype.$reset = function () {
            this._callDisconnected();
            this._callConnected();
        };

        gslUIkit.prototype.$destroy = function (removeEl) {
            if ( removeEl === void 0 ) removeEl = false;


            var ref = this.$options;
            var el = ref.el;
            var name = ref.name;

            if (el) {
                this._callDisconnected();
            }

            this._callHook('destroy');

            if (!el || !el[DATA]) {
                return;
            }

            delete el[DATA][name];

            if (!isEmpty(el[DATA])) {
                delete el[DATA];
            }

            if (removeEl) {
                remove(this.$el);
            }
        };

        gslUIkit.prototype.$create = function (component, element, data) {
            return gslUIkit[component](element, data);
        };

        gslUIkit.prototype.$update = gslUIkit.update;
        gslUIkit.prototype.$getComponent = gslUIkit.getComponent;

        var names = {};
        Object.defineProperties(gslUIkit.prototype, {

            $container: Object.getOwnPropertyDescriptor(gslUIkit, 'container'),

            $name: {

                get: function() {
                    var ref = this.$options;
                    var name = ref.name;

                    if (!names[name]) {
                        names[name] = gslUIkit.prefix + hyphenate(name);
                    }

                    return names[name];
                }

            }

        });

    }

    function componentAPI (gslUIkit) {

        var DATA = gslUIkit.data;

        var components = {};

        gslUIkit.component = function (name, options) {

            if (!options) {

                if (isPlainObject(components[name])) {
                    components[name] = gslUIkit.extend(components[name]);
                }

                return components[name];

            }

            gslUIkit[name] = function (element, data) {
                var i = arguments.length, argsArray = Array(i);
                while ( i-- ) argsArray[i] = arguments[i];


                var component = gslUIkit.component(name);

                if (isPlainObject(element)) {
                    return new component({data: element});
                }

                if (component.options.functional) {
                    return new component({data: [].concat( argsArray )});
                }

                return element && element.nodeType ? init(element) : $$(element).map(init)[0];

                function init(element) {

                    var instance = gslUIkit.getComponent(element, name);

                    if (instance) {
                        if (!data) {
                            return instance;
                        } else {
                            instance.$destroy();
                        }
                    }

                    return new component({el: element, data: data});

                }

            };

            var opt = isPlainObject(options) ? assign({}, options) : options.options;

            opt.name = name;

            if (opt.install) {
                opt.install(gslUIkit, opt, name);
            }

            if (gslUIkit._initialized && !opt.functional) {
                var id = hyphenate(name);
                fastdom.read(function () { return gslUIkit[name](("[gsl-" + id + "],[data-gsl-" + id + "]")); });
            }

            return components[name] = isPlainObject(options) ? opt : options;
        };

        gslUIkit.getComponents = function (element) { return element && element[DATA] || {}; };
        gslUIkit.getComponent = function (element, name) { return gslUIkit.getComponents(element)[name]; };

        gslUIkit.connect = function (node) {

            if (node[DATA]) {
                for (var name in node[DATA]) {
                    node[DATA][name]._callConnected();
                }
            }

            for (var i = 0; i < node.attributes.length; i++) {

                var name$1 = getComponentName(node.attributes[i].name);

                if (name$1 && name$1 in components) {
                    gslUIkit[name$1](node);
                }

            }

        };

        gslUIkit.disconnect = function (node) {
            for (var name in node[DATA]) {
                node[DATA][name]._callDisconnected();
            }
        };

    }

    function getComponentName(attribute) {
        return startsWith(attribute, 'gsl-') || startsWith(attribute, 'data-gsl-')
            ? camelize(attribute.replace('data-gsl-', '').replace('gsl-', ''))
            : false;
    }

    var gslUIkit = function (options) {
        this._init(options);
    };

    gslUIkit.util = util;
    gslUIkit.data = '__uikit__';
    gslUIkit.prefix = 'gsl-';
    gslUIkit.options = {};

    globalAPI(gslUIkit);
    hooksAPI(gslUIkit);
    stateAPI(gslUIkit);
    componentAPI(gslUIkit);
    instanceAPI(gslUIkit);

    var Class = {

        connected: function() {
            !hasClass(this.$el, this.$name) && addClass(this.$el, this.$name);
        }

    };

    var Togglable = {

        props: {
            cls: Boolean,
            animation: 'list',
            duration: Number,
            origin: String,
            transition: String,
            queued: Boolean
        },

        data: {
            cls: false,
            animation: [false],
            duration: 200,
            origin: false,
            transition: 'linear',
            queued: false,

            initProps: {
                overflow: '',
                height: '',
                paddingTop: '',
                paddingBottom: '',
                marginTop: '',
                marginBottom: ''
            },

            hideProps: {
                overflow: 'hidden',
                height: 0,
                paddingTop: 0,
                paddingBottom: 0,
                marginTop: 0,
                marginBottom: 0
            }

        },

        computed: {

            hasAnimation: function(ref) {
                var animation = ref.animation;

                return !!animation[0];
            },

            hasTransition: function(ref) {
                var animation = ref.animation;

                return this.hasAnimation && animation[0] === true;
            }

        },

        methods: {

            toggleElement: function(targets, show, animate) {
                var this$1 = this;

                return new Promise(function (resolve) {

                    targets = toNodes(targets);

                    var all = function (targets) { return Promise.all(targets.map(function (el) { return this$1._toggleElement(el, show, animate); })); };
                    var toggled = targets.filter(function (el) { return this$1.isToggled(el); });
                    var untoggled = targets.filter(function (el) { return !includes(toggled, el); });

                    var p;

                    if (!this$1.queued || !isUndefined(animate) || !isUndefined(show) || !this$1.hasAnimation || targets.length < 2) {

                        p = all(untoggled.concat(toggled));

                    } else {

                        var body = document.body;
                        var scroll = body.scrollTop;
                        var el = toggled[0];
                        var inProgress = Animation.inProgress(el) && hasClass(el, 'gsl-animation-leave')
                                || Transition.inProgress(el) && el.style.height === '0px';

                        p = all(toggled);

                        if (!inProgress) {
                            p = p.then(function () {
                                var p = all(untoggled);
                                body.scrollTop = scroll;
                                return p;
                            });
                        }

                    }

                    p.then(resolve, noop);

                });
            },

            toggleNow: function(targets, show) {
                var this$1 = this;

                return new Promise(function (resolve) { return Promise.all(toNodes(targets).map(function (el) { return this$1._toggleElement(el, show, false); })).then(resolve, noop); });
            },

            isToggled: function(el) {
                var nodes = toNodes(el || this.$el);
                return this.cls
                    ? hasClass(nodes, this.cls.split(' ')[0])
                    : !hasAttr(nodes, 'hidden');
            },

            updateAria: function(el) {
                if (this.cls === false) {
                    attr(el, 'aria-hidden', !this.isToggled(el));
                }
            },

            _toggleElement: function(el, show, animate) {
                var this$1 = this;


                show = isBoolean(show)
                    ? show
                    : Animation.inProgress(el)
                        ? hasClass(el, 'gsl-animation-leave')
                        : Transition.inProgress(el)
                            ? el.style.height === '0px'
                            : !this.isToggled(el);

                if (!trigger(el, ("before" + (show ? 'show' : 'hide')), [this])) {
                    return Promise.reject();
                }

                var promise = (
                    isFunction(animate)
                        ? animate
                        : animate === false || !this.hasAnimation
                            ? this._toggle
                            : this.hasTransition
                                ? toggleHeight(this)
                                : toggleAnimation(this)
                )(el, show);

                trigger(el, show ? 'show' : 'hide', [this]);

                var final = function () {
                    trigger(el, show ? 'shown' : 'hidden', [this$1]);
                    this$1.$update(el);
                };

                return promise ? promise.then(final) : Promise.resolve(final());
            },

            _toggle: function(el, toggled) {

                if (!el) {
                    return;
                }

                toggled = Boolean(toggled);

                var changed;
                if (this.cls) {
                    changed = includes(this.cls, ' ') || toggled !== hasClass(el, this.cls);
                    changed && toggleClass(el, this.cls, includes(this.cls, ' ') ? undefined : toggled);
                } else {
                    changed = toggled === hasAttr(el, 'hidden');
                    changed && attr(el, 'hidden', !toggled ? '' : null);
                }

                $$('[autofocus]', el).some(function (el) { return isVisible(el) ? el.focus() || true : el.blur(); });

                this.updateAria(el);
                changed && this.$update(el);
            }

        }

    };

    function toggleHeight(ref) {
        var isToggled = ref.isToggled;
        var duration = ref.duration;
        var initProps = ref.initProps;
        var hideProps = ref.hideProps;
        var transition = ref.transition;
        var _toggle = ref._toggle;

        return function (el, show) {

            var inProgress = Transition.inProgress(el);
            var inner = el.hasChildNodes ? toFloat(css(el.firstElementChild, 'marginTop')) + toFloat(css(el.lastElementChild, 'marginBottom')) : 0;
            var currentHeight = isVisible(el) ? height(el) + (inProgress ? 0 : inner) : 0;

            Transition.cancel(el);

            if (!isToggled(el)) {
                _toggle(el, true);
            }

            height(el, '');

            // Update child components first
            fastdom.flush();

            var endHeight = height(el) + (inProgress ? 0 : inner);
            height(el, currentHeight);

            return (show
                    ? Transition.start(el, assign({}, initProps, {overflow: 'hidden', height: endHeight}), Math.round(duration * (1 - currentHeight / endHeight)), transition)
                    : Transition.start(el, hideProps, Math.round(duration * (currentHeight / endHeight)), transition).then(function () { return _toggle(el, false); })
            ).then(function () { return css(el, initProps); });

        };
    }

    function toggleAnimation(ref) {
        var animation = ref.animation;
        var duration = ref.duration;
        var origin = ref.origin;
        var _toggle = ref._toggle;

        return function (el, show) {

            Animation.cancel(el);

            if (show) {
                _toggle(el, true);
                return Animation.in(el, animation[0], duration, origin);
            }

            return Animation.out(el, animation[1] || animation[0], duration, origin).then(function () { return _toggle(el, false); });
        };
    }

    var Accordion = {

        mixins: [Class, Togglable],

        props: {
            targets: String,
            active: null,
            collapsible: Boolean,
            multiple: Boolean,
            toggle: String,
            content: String,
            transition: String
        },

        data: {
            targets: '> *',
            active: false,
            animation: [true],
            collapsible: true,
            multiple: false,
            clsOpen: 'gsl-open',
            toggle: '> .gsl-accordion-title',
            content: '> .gsl-accordion-content',
            transition: 'ease'
        },

        computed: {

            items: function(ref, $el) {
                var targets = ref.targets;

                return $$(targets, $el);
            }

        },

        events: [

            {

                name: 'click',

                delegate: function() {
                    return ((this.targets) + " " + (this.$props.toggle));
                },

                handler: function(e) {
                    e.preventDefault();
                    this.toggle(index($$(((this.targets) + " " + (this.$props.toggle)), this.$el), e.current));
                }

            }

        ],

        connected: function() {

            if (this.active === false) {
                return;
            }

            var active = this.items[Number(this.active)];
            if (active && !hasClass(active, this.clsOpen)) {
                this.toggle(active, false);
            }
        },

        update: function() {
            var this$1 = this;


            this.items.forEach(function (el) { return this$1._toggle($(this$1.content, el), hasClass(el, this$1.clsOpen)); });

            var active = !this.collapsible && !hasClass(this.items, this.clsOpen) && this.items[0];
            if (active) {
                this.toggle(active, false);
            }
        },

        methods: {

            toggle: function(item, animate) {
                var this$1 = this;


                var index = getIndex(item, this.items);
                var active = filter(this.items, ("." + (this.clsOpen)));

                item = this.items[index];

                item && [item]
                    .concat(!this.multiple && !includes(active, item) && active || [])
                    .forEach(function (el) {

                        var isItem = el === item;
                        var state = isItem && !hasClass(el, this$1.clsOpen);

                        if (!state && isItem && !this$1.collapsible && active.length < 2) {
                            return;
                        }

                        toggleClass(el, this$1.clsOpen, state);

                        var content = el._wrapper ? el._wrapper.firstElementChild : $(this$1.content, el);

                        if (!el._wrapper) {
                            el._wrapper = wrapAll(content, '<div>');
                            attr(el._wrapper, 'hidden', state ? '' : null);
                        }

                        this$1._toggle(content, true);
                        this$1.toggleElement(el._wrapper, state, animate).then(function () {

                            if (hasClass(el, this$1.clsOpen) !== state) {
                                return;
                            }

                            if (!state) {
                                this$1._toggle(content, false);
                            }

                            el._wrapper = null;
                            unwrap(content);

                        });

                    });
            }

        }

    };

    var Alert = {

        mixins: [Class, Togglable],

        args: 'animation',

        props: {
            close: String
        },

        data: {
            animation: [true],
            selClose: '.gsl-alert-close',
            duration: 150,
            hideProps: assign({opacity: 0}, Togglable.data.hideProps)
        },

        events: [

            {

                name: 'click',

                delegate: function() {
                    return this.selClose;
                },

                handler: function(e) {
                    e.preventDefault();
                    this.close();
                }

            }

        ],

        methods: {

            close: function() {
                var this$1 = this;

                this.toggleElement(this.$el).then(function () { return this$1.$destroy(true); });
            }

        }

    };

    function Core (gslUIkit) {

        ready(function () {

            gslUIkit.update();
            on(window, 'load resize', function () { return gslUIkit.update(null, 'resize'); });
            on(document, 'loadedmetadata load', function (ref) {
                var target = ref.target;

                return gslUIkit.update(target, 'resize');
            }, true);

            // throttle `scroll` event (Safari triggers multiple `scroll` events per frame)
            var pending;
            on(window, 'scroll', function (e) {

                if (pending) {
                    return;
                }
                pending = true;
                fastdom.write(function () { return pending = false; });

                var target = e.target;
                gslUIkit.update(target.nodeType !== 1 ? document.body : target, e.type);

            }, {passive: true, capture: true});

            var started = 0;
            on(document, 'animationstart', function (ref) {
                var target = ref.target;

                if ((css(target, 'animationName') || '').match(/^gsl-.*(left|right)/)) {

                    started++;
                    css(document.body, 'overflowX', 'hidden');
                    setTimeout(function () {
                        if (!--started) {
                            css(document.body, 'overflowX', '');
                        }
                    }, toMs(css(target, 'animationDuration')) + 100);
                }
            }, true);

            var off;
            on(document, pointerDown, function (e) {

                off && off();

                if (!isTouch(e)) {
                    return;
                }

                var pos = getEventPos(e);
                var target = 'tagName' in e.target ? e.target : e.target.parentNode;
                off = once(document, pointerUp, function (e) {

                    var ref = getEventPos(e);
                    var x = ref.x;
                    var y = ref.y;

                    // swipe
                    if (target && x && Math.abs(pos.x - x) > 100 || y && Math.abs(pos.y - y) > 100) {

                        setTimeout(function () {
                            trigger(target, 'swipe');
                            trigger(target, ("swipe" + (swipeDirection(pos.x, pos.y, x, y))));
                        });

                    }

                });
            }, {passive: true});

        });

    }

    function swipeDirection(x1, y1, x2, y2) {
        return Math.abs(x1 - x2) >= Math.abs(y1 - y2)
            ? x1 - x2 > 0
                ? 'Left'
                : 'Right'
            : y1 - y2 > 0
                ? 'Up'
                : 'Down';
    }

    var Video = {

        args: 'autoplay',

        props: {
            automute: Boolean,
            autoplay: Boolean
        },

        data: {
            automute: false,
            autoplay: true
        },

        computed: {

            inView: function(ref) {
                var autoplay = ref.autoplay;

                return autoplay === 'inview';
            }

        },

        connected: function() {

            if (this.inView && !hasAttr(this.$el, 'preload')) {
                this.$el.preload = 'none';
            }

            this.player = new Player(this.$el);

            if (this.automute) {
                this.player.mute();
            }

        },

        update: {

            read: function() {

                return !this.player
                    ? false
                    : {
                        visible: isVisible(this.$el) && css(this.$el, 'visibility') !== 'hidden',
                        inView: this.inView && isInView(this.$el)
                    };
            },

            write: function(ref) {
                var visible = ref.visible;
                var inView = ref.inView;


                if (!visible || this.inView && !inView) {
                    this.player.pause();
                } else if (this.autoplay === true || this.inView && inView) {
                    this.player.play();
                }

            },

            events: ['resize', 'scroll']

        }

    };

    var Cover = {

        mixins: [Class, Video],

        props: {
            width: Number,
            height: Number
        },

        data: {
            automute: true
        },

        update: {

            read: function() {

                var el = this.$el;

                if (!isVisible(el)) {
                    return false;
                }

                var ref = el.parentNode;
                var height = ref.offsetHeight;
                var width = ref.offsetWidth;

                return {height: height, width: width};
            },

            write: function(ref) {
                var height = ref.height;
                var width = ref.width;


                var el = this.$el;
                var elWidth = this.width || el.naturalWidth || el.videoWidth || el.clientWidth;
                var elHeight = this.height || el.naturalHeight || el.videoHeight || el.clientHeight;

                if (!elWidth || !elHeight) {
                    return;
                }

                css(el, Dimensions.cover(
                    {
                        width: elWidth,
                        height: elHeight
                    },
                    {
                        width: width + (width % 2 ? 1 : 0),
                        height: height + (height % 2 ? 1 : 0)
                    }
                ));

            },

            events: ['resize']

        }

    };

    var Position = {

        props: {
            pos: String,
            offset: null,
            flip: Boolean,
            clsPos: String
        },

        data: {
            pos: ("bottom-" + (!isRtl ? 'left' : 'right')),
            flip: true,
            offset: false,
            clsPos: ''
        },

        computed: {

            pos: function(ref) {
                var pos = ref.pos;

                return (pos + (!includes(pos, '-') ? '-center' : '')).split('-');
            },

            dir: function() {
                return this.pos[0];
            },

            align: function() {
                return this.pos[1];
            }

        },

        methods: {

            positionAt: function(element, target, boundary) {

                removeClasses(element, ((this.clsPos) + "-(top|bottom|left|right)(-[a-z]+)?"));
                css(element, {top: '', left: ''});

                var node;
                var ref = this;
                var offset$1 = ref.offset;
                var axis = this.getAxis();

                if (!isNumeric(offset$1)) {
                    node = $(offset$1);
                    offset$1 = node
                        ? offset(node)[axis === 'x' ? 'left' : 'top'] - offset(target)[axis === 'x' ? 'right' : 'bottom']
                        : 0;
                }

                var ref$1 = positionAt(
                    element,
                    target,
                    axis === 'x' ? ((flipPosition(this.dir)) + " " + (this.align)) : ((this.align) + " " + (flipPosition(this.dir))),
                    axis === 'x' ? ((this.dir) + " " + (this.align)) : ((this.align) + " " + (this.dir)),
                    axis === 'x' ? ("" + (this.dir === 'left' ? -offset$1 : offset$1)) : (" " + (this.dir === 'top' ? -offset$1 : offset$1)),
                    null,
                    this.flip,
                    boundary
                ).target;
                var x = ref$1.x;
                var y = ref$1.y;

                this.dir = axis === 'x' ? x : y;
                this.align = axis === 'x' ? y : x;

                toggleClass(element, ((this.clsPos) + "-" + (this.dir) + "-" + (this.align)), this.offset === false);

            },

            getAxis: function() {
                return this.dir === 'top' || this.dir === 'bottom' ? 'y' : 'x';
            }

        }

    };

    var active;

    var Drop = {

        mixins: [Position, Togglable],

        args: 'pos',

        props: {
            mode: 'list',
            toggle: Boolean,
            boundary: Boolean,
            boundaryAlign: Boolean,
            delayShow: Number,
            delayHide: Number,
            clsDrop: String
        },

        data: {
            mode: ['click', 'hover'],
            toggle: '- *',
            boundary: window,
            boundaryAlign: false,
            delayShow: 0,
            delayHide: 800,
            clsDrop: false,
            hoverIdle: 200,
            animation: ['gsl-animation-fade'],
            cls: 'gsl-open'
        },

        computed: {

            boundary: function(ref, $el) {
                var boundary = ref.boundary;

                return query(boundary, $el);
            },

            clsDrop: function(ref) {
                var clsDrop = ref.clsDrop;

                return clsDrop || ("gsl-" + (this.$options.name));
            },

            clsPos: function() {
                return this.clsDrop;
            }

        },

        created: function() {
            this.tracker = new MouseTracker();
        },

        connected: function() {

            addClass(this.$el, this.clsDrop);

            var ref = this.$props;
            var toggle = ref.toggle;
            this.toggle = toggle && this.$create('toggle', query(toggle, this.$el), {
                target: this.$el,
                mode: this.mode
            });

            !this.toggle && trigger(this.$el, 'updatearia');

        },

        events: [


            {

                name: 'click',

                delegate: function() {
                    return ("." + (this.clsDrop) + "-close");
                },

                handler: function(e) {
                    e.preventDefault();
                    this.hide(false);
                }

            },

            {

                name: 'click',

                delegate: function() {
                    return 'a[href^="#"]';
                },

                handler: function(e) {

                    var id = e.target.hash;

                    if (!id) {
                        e.preventDefault();
                    }

                    if (!id || !within(id, this.$el)) {
                        this.hide(false);
                    }
                }

            },

            {

                name: 'beforescroll',

                handler: function() {
                    this.hide(false);
                }

            },

            {

                name: 'toggle',

                self: true,

                handler: function(e, toggle) {

                    e.preventDefault();

                    if (this.isToggled()) {
                        this.hide(false);
                    } else {
                        this.show(toggle, false);
                    }
                }

            },

            {

                name: pointerEnter,

                filter: function() {
                    return includes(this.mode, 'hover');
                },

                handler: function(e) {

                    if (isTouch(e)) {
                        return;
                    }

                    if (active
                        && active !== this
                        && active.toggle
                        && includes(active.toggle.mode, 'hover')
                        && !within(e.target, active.toggle.$el)
                        && !pointInRect({x: e.pageX, y: e.pageY}, offset(active.$el))
                    ) {
                        active.hide(false);
                    }

                    e.preventDefault();
                    this.show(this.toggle);
                }

            },

            {

                name: 'toggleshow',

                handler: function(e, toggle) {

                    if (toggle && !includes(toggle.target, this.$el)) {
                        return;
                    }

                    e.preventDefault();
                    this.show(toggle || this.toggle);
                }

            },

            {

                name: ("togglehide " + pointerLeave),

                handler: function(e, toggle) {

                    if (isTouch(e) || toggle && !includes(toggle.target, this.$el)) {
                        return;
                    }

                    e.preventDefault();

                    if (this.toggle && includes(this.toggle.mode, 'hover')) {
                        this.hide();
                    }
                }

            },

            {

                name: 'beforeshow',

                self: true,

                handler: function() {
                    this.clearTimers();
                    Animation.cancel(this.$el);
                    this.position();
                }

            },

            {

                name: 'show',

                self: true,

                handler: function() {
                    this.tracker.init();
                    trigger(this.$el, 'updatearia');
                    registerEvent();
                }

            },

            {

                name: 'beforehide',

                self: true,

                handler: function() {
                    this.clearTimers();
                }

            },

            {

                name: 'hide',

                handler: function(ref) {
                    var target = ref.target;


                    if (this.$el !== target) {
                        active = active === null && within(target, this.$el) && this.isToggled() ? this : active;
                        return;
                    }

                    active = this.isActive() ? null : active;
                    trigger(this.$el, 'updatearia');
                    this.tracker.cancel();
                }

            },

            {

                name: 'updatearia',

                self: true,

                handler: function(e, toggle) {

                    e.preventDefault();

                    this.updateAria(this.$el);

                    if (toggle || this.toggle) {
                        attr((toggle || this.toggle).$el, 'aria-expanded', this.isToggled() ? 'true' : 'false');
                        toggleClass(this.toggle.$el, this.cls, this.isToggled());
                    }
                }
            }

        ],

        update: {

            write: function() {

                if (this.isToggled() && !Animation.inProgress(this.$el)) {
                    this.position();
                }

            },

            events: ['resize']

        },

        methods: {

            show: function(toggle, delay) {
                var this$1 = this;
                if ( delay === void 0 ) delay = true;


                var show = function () { return !this$1.isToggled() && this$1.toggleElement(this$1.$el, true); };
                var tryShow = function () {

                    this$1.toggle = toggle || this$1.toggle;

                    this$1.clearTimers();

                    if (this$1.isActive()) {
                        return;
                    } else if (delay && active && active !== this$1 && active.isDelaying) {
                        this$1.showTimer = setTimeout(this$1.show, 10);
                        return;
                    } else if (this$1.isParentOf(active)) {

                        if (active.hideTimer) {
                            active.hide(false);
                        } else {
                            return;
                        }

                    } else if (active && this$1.isChildOf(active)) {

                        active.clearTimers();

                    } else if (active && !this$1.isChildOf(active) && !this$1.isParentOf(active)) {

                        var prev;
                        while (active && active !== prev && !this$1.isChildOf(active)) {
                            prev = active;
                            active.hide(false);
                        }

                    }

                    if (delay && this$1.delayShow) {
                        this$1.showTimer = setTimeout(show, this$1.delayShow);
                    } else {
                        show();
                    }

                    active = this$1;
                };

                if (toggle && this.toggle && toggle.$el !== this.toggle.$el) {

                    once(this.$el, 'hide', tryShow);
                    this.hide(false);

                } else {
                    tryShow();
                }
            },

            hide: function(delay) {
                var this$1 = this;
                if ( delay === void 0 ) delay = true;


                var hide = function () { return this$1.toggleNow(this$1.$el, false); };

                this.clearTimers();

                this.isDelaying = this.tracker.movesTo(this.$el);

                if (delay && this.isDelaying) {
                    this.hideTimer = setTimeout(this.hide, this.hoverIdle);
                } else if (delay && this.delayHide) {
                    this.hideTimer = setTimeout(hide, this.delayHide);
                } else {
                    hide();
                }
            },

            clearTimers: function() {
                clearTimeout(this.showTimer);
                clearTimeout(this.hideTimer);
                this.showTimer = null;
                this.hideTimer = null;
                this.isDelaying = false;
            },

            isActive: function() {
                return active === this;
            },

            isChildOf: function(drop) {
                return drop && drop !== this && within(this.$el, drop.$el);
            },

            isParentOf: function(drop) {
                return drop && drop !== this && within(drop.$el, this.$el);
            },

            position: function() {

                removeClasses(this.$el, ((this.clsDrop) + "-(stack|boundary)"));
                css(this.$el, {top: '', left: '', display: 'block'});
                toggleClass(this.$el, ((this.clsDrop) + "-boundary"), this.boundaryAlign);

                var boundary = offset(this.boundary);
                var alignTo = this.boundaryAlign ? boundary : offset(this.toggle.$el);

                if (this.align === 'justify') {
                    var prop = this.getAxis() === 'y' ? 'width' : 'height';
                    css(this.$el, prop, alignTo[prop]);
                } else if (this.$el.offsetWidth > Math.max(boundary.right - alignTo.left, alignTo.right - boundary.left)) {
                    addClass(this.$el, ((this.clsDrop) + "-stack"));
                }

                this.positionAt(this.$el, this.boundaryAlign ? this.boundary : this.toggle.$el, this.boundary);

                css(this.$el, 'display', '');

            }

        }

    };

    var registered;

    function registerEvent() {

        if (registered) {
            return;
        }

        registered = true;
        on(document, pointerUp, function (ref) {
            var target = ref.target;
            var defaultPrevented = ref.defaultPrevented;

            var prev;

            if (defaultPrevented) {
                return;
            }

            while (active && active !== prev && !within(target, active.$el) && !(active.toggle && within(target, active.toggle.$el))) {
                prev = active;
                active.hide(false);
            }
        });
    }

    var Dropdown = {

        extends: Drop

    };

    var FormCustom = {

        mixins: [Class],

        args: 'target',

        props: {
            target: Boolean
        },

        data: {
            target: false
        },

        computed: {

            input: function(_, $el) {
                return $(selInput, $el);
            },

            state: function() {
                return this.input.nextElementSibling;
            },

            target: function(ref, $el) {
                var target = ref.target;

                return target && (target === true
                    && this.input.parentNode === $el
                    && this.input.nextElementSibling
                    || query(target, $el));
            }

        },

        update: function() {

            var ref = this;
            var target = ref.target;
            var input = ref.input;

            if (!target) {
                return;
            }

            var option;
            var prop = isInput(target) ? 'value' : 'textContent';
            var prev = target[prop];
            var value = input.files && input.files[0]
                ? input.files[0].name
                : matches(input, 'select') && (option = $$('option', input).filter(function (el) { return el.selected; })[0])
                    ? option.textContent
                    : input.value;

            if (prev !== value) {
                target[prop] = value;
            }

        },

        events: {

            change: function() {
                this.$emit();
            }

        }

    };

    // Deprecated
    var Gif = {

        update: {

            read: function(data) {

                var inview = isInView(this.$el);

                if (!inview || data.isInView === inview) {
                    return false;
                }

                data.isInView = inview;
            },

            write: function() {
                this.$el.src = this.$el.src;
            },

            events: ['scroll', 'resize']
        }

    };

    var Margin = {

        props: {
            margin: String,
            firstColumn: Boolean
        },

        data: {
            margin: 'gsl-margin-small-top',
            firstColumn: 'gsl-first-column'
        },

        update: {

            read: function(data) {

                var items = this.$el.children;
                var rows = [[]];

                if (!items.length || !isVisible(this.$el)) {
                    return data.rows = rows;
                }

                data.rows = getRows(items);
                data.stacks = !data.rows.some(function (row) { return row.length > 1; });

            },

            write: function(ref) {
                var this$1 = this;
                var rows = ref.rows;


                rows.forEach(function (row, i) { return row.forEach(function (el, j) {
                        toggleClass(el, this$1.margin, i !== 0);
                        toggleClass(el, this$1.firstColumn, j === 0);
                    }); }
                );

            },

            events: ['resize']

        }

    };

    function getRows(items) {
        var rows = [[]];

        for (var i = 0; i < items.length; i++) {

            var el = items[i];
            var dim = getOffset(el);

            if (!dim.height) {
                continue;
            }

            for (var j = rows.length - 1; j >= 0; j--) {

                var row = rows[j];

                if (!row[0]) {
                    row.push(el);
                    break;
                }

                var leftDim = (void 0);
                if (row[0].offsetParent === el.offsetParent) {
                    leftDim = getOffset(row[0]);
                } else {
                    dim = getOffset(el, true);
                    leftDim = getOffset(row[0], true);
                }

                if (dim.top >= leftDim.bottom - 1) {
                    rows.push([el]);
                    break;
                }

                if (dim.bottom > leftDim.top) {

                    if (dim.left < leftDim.left && !isRtl) {
                        row.unshift(el);
                        break;
                    }

                    row.push(el);
                    break;
                }

                if (j === 0) {
                    rows.unshift([el]);
                    break;
                }

            }

        }

        return rows;

    }

    function getOffset(element, offset) {
        var assign;

        if ( offset === void 0 ) offset = false;

        var offsetTop = element.offsetTop;
        var offsetLeft = element.offsetLeft;
        var offsetHeight = element.offsetHeight;

        if (offset) {
            (assign = offsetPosition(element), offsetTop = assign[0], offsetLeft = assign[1]);
        }

        return {
            top: offsetTop,
            left: offsetLeft,
            height: offsetHeight,
            bottom: offsetTop + offsetHeight
        };
    }

    var Grid = {

        extends: Margin,

        mixins: [Class],

        name: 'grid',

        props: {
            masonry: Boolean,
            parallax: Number
        },

        data: {
            margin: 'gsl-grid-margin',
            clsStack: 'gsl-grid-stack',
            masonry: false,
            parallax: 0
        },

        computed: {

            length: function(_, $el) {
                return $el.children.length;
            },

            parallax: function(ref) {
                var parallax = ref.parallax;

                return parallax && this.length ? Math.abs(parallax) : '';
            }

        },

        connected: function() {
            this.masonry && addClass(this.$el, 'gsl-flex-top gsl-flex-wrap-top');
        },

        update: [

            {

                read: function(ref) {
                    var rows = ref.rows;


                    if (this.masonry || this.parallax) {
                        rows = rows.map(function (elements) { return sortBy(elements, 'offsetLeft'); });

                        if (isRtl) {
                            rows.map(function (row) { return row.reverse(); });
                        }

                    }

                    var transitionInProgress = rows.some(function (elements) { return elements.some(Transition.inProgress); });
                    var translates = false;
                    var elHeight = '';

                    if (this.masonry && this.length) {

                        var height = 0;

                        translates = rows.reduce(function (translates, row, i) {

                            translates[i] = row.map(function (_, j) { return i === 0 ? 0 : toFloat(translates[i - 1][j]) + (height - toFloat(rows[i - 1][j] && rows[i - 1][j].offsetHeight)); });
                            height = row.reduce(function (height, el) { return Math.max(height, el.offsetHeight); }, 0);

                            return translates;

                        }, []);

                        elHeight = maxColumnHeight(rows) + getMarginTop(this.$el, this.margin) * (rows.length - 1);

                    }

                    return {rows: rows, translates: translates, height: !transitionInProgress ? elHeight : false};

                },

                write: function(ref) {
                    var stacks = ref.stacks;
                    var height = ref.height;


                    toggleClass(this.$el, this.clsStack, stacks);

                    css(this.$el, 'paddingBottom', this.parallax);
                    height !== false && css(this.$el, 'height', height);

                },

                events: ['resize']

            },

            {

                read: function(ref) {
                    var height$1 = ref.height;

                    return {
                        scrolled: this.parallax
                            ? scrolledOver(this.$el, height$1 ? height$1 - height(this.$el) : 0) * this.parallax
                            : false
                    };
                },

                write: function(ref) {
                    var rows = ref.rows;
                    var scrolled = ref.scrolled;
                    var translates = ref.translates;


                    if (scrolled === false && !translates) {
                        return;
                    }

                    rows.forEach(function (row, i) { return row.forEach(function (el, j) { return css(el, 'transform', !scrolled && !translates ? '' : ("translateY(" + ((translates && -translates[i][j]) + (scrolled ? j % 2 ? scrolled : scrolled / 8 : 0)) + "px)")); }
                        ); }
                    );

                },

                events: ['scroll', 'resize']

            }

        ]

    };

    function getMarginTop(root, cls) {

        var nodes = toNodes(root.children);
        var ref = nodes.filter(function (el) { return hasClass(el, cls); });
        var node = ref[0];

        return toFloat(node
            ? css(node, 'marginTop')
            : css(nodes[0], 'paddingLeft'));
    }

    function maxColumnHeight(rows) {
        return Math.max.apply(Math, rows.reduce(function (sum, row) {
            row.forEach(function (el, i) { return sum[i] = (sum[i] || 0) + el.offsetHeight; });
            return sum;
        }, []));
    }

    // IE 11 fix (min-height on a flex container won't apply to its flex items)
    var FlexBug = isIE ? {

        data: {
            selMinHeight: false,
            forceHeight: false
        },

        computed: {

            elements: function(ref, $el) {
                var selMinHeight = ref.selMinHeight;

                return selMinHeight ? $$(selMinHeight, $el) : [$el];
            }

        },

        update: [

            {

                read: function() {
                    css(this.elements, 'height', '');
                },

                order: -5,

                events: ['resize']

            },

            {

                write: function() {
                    var this$1 = this;

                    this.elements.forEach(function (el) {
                        var height = toFloat(css(el, 'minHeight'));
                        if (height && (this$1.forceHeight || Math.round(height + boxModelAdjust('height', el, 'content-box')) >= el.offsetHeight)) {
                            css(el, 'height', height);
                        }
                    });
                },

                order: 5,

                events: ['resize']

            }

        ]

    } : {};

    var HeightMatch = {

        mixins: [FlexBug],

        args: 'target',

        props: {
            target: String,
            row: Boolean
        },

        data: {
            target: '> *',
            row: true,
            forceHeight: true
        },

        computed: {

            elements: function(ref, $el) {
                var target = ref.target;

                return $$(target, $el);
            }

        },

        update: {

            read: function() {
                return {
                    rows: (this.row ? getRows(this.elements) : [this.elements]).map(match)
                };
            },

            write: function(ref) {
                var rows = ref.rows;

                rows.forEach(function (ref) {
                        var heights = ref.heights;
                        var elements = ref.elements;

                        return elements.forEach(function (el, i) { return css(el, 'minHeight', heights[i]); }
                    );
                }
                );
            },

            events: ['resize']

        }

    };

    function match(elements) {
        var assign;


        if (elements.length < 2) {
            return {heights: [''], elements: elements};
        }

        var ref = getHeights(elements);
        var heights = ref.heights;
        var max = ref.max;
        var hasMinHeight = elements.some(function (el) { return el.style.minHeight; });
        var hasShrunk = elements.some(function (el, i) { return !el.style.minHeight && heights[i] < max; });

        if (hasMinHeight && hasShrunk) {
            css(elements, 'minHeight', '');
            ((assign = getHeights(elements), heights = assign.heights, max = assign.max));
        }

        heights = elements.map(function (el, i) { return heights[i] === max && toFloat(el.style.minHeight).toFixed(2) !== max.toFixed(2) ? '' : max; }
        );

        return {heights: heights, elements: elements};
    }

    function getHeights(elements) {
        var heights = elements.map(function (el) { return offset(el).height - boxModelAdjust('height', el, 'content-box'); });
        var max = Math.max.apply(null, heights);

        return {heights: heights, max: max};
    }

    var HeightViewport = {

        mixins: [FlexBug],

        props: {
            expand: Boolean,
            offsetTop: Boolean,
            offsetBottom: Boolean,
            minHeight: Number
        },

        data: {
            expand: false,
            offsetTop: false,
            offsetBottom: false,
            minHeight: 0
        },

        update: {

            read: function() {

                var minHeight = '';
                var box = boxModelAdjust('height', this.$el, 'content-box');

                if (this.expand) {

                    minHeight = height(window) - (offsetHeight(document.documentElement) - offsetHeight(this.$el)) - box || '';

                } else {

                    // on mobile devices (iOS and Android) window.innerHeight !== 100vh
                    minHeight = 'calc(100vh';

                    if (this.offsetTop) {

                        var ref = offset(this.$el);
                        var top = ref.top;
                        minHeight += top < height(window) / 2 ? (" - " + top + "px") : '';

                    }

                    if (this.offsetBottom === true) {

                        minHeight += " - " + (offsetHeight(this.$el.nextElementSibling)) + "px";

                    } else if (isNumeric(this.offsetBottom)) {

                        minHeight += " - " + (this.offsetBottom) + "vh";

                    } else if (this.offsetBottom && endsWith(this.offsetBottom, 'px')) {

                        minHeight += " - " + (toFloat(this.offsetBottom)) + "px";

                    } else if (isString(this.offsetBottom)) {

                        minHeight += " - " + (offsetHeight(query(this.offsetBottom, this.$el))) + "px";

                    }

                    minHeight += (box ? (" - " + box + "px") : '') + ")";

                }

                return {minHeight: minHeight};
            },

            write: function(ref) {
                var minHeight = ref.minHeight;


                css(this.$el, {minHeight: minHeight});

                if (this.minHeight && toFloat(css(this.$el, 'minHeight')) < this.minHeight) {
                    css(this.$el, 'minHeight', this.minHeight);
                }

            },

            events: ['resize']

        }

    };

    function offsetHeight(el) {
        return el && el.offsetHeight || 0;
    }

    var Svg = {

        args: 'src',

        props: {
            id: Boolean,
            icon: String,
            src: String,
            style: String,
            width: Number,
            height: Number,
            ratio: Number,
            'class': String,
            strokeAnimation: Boolean,
            attributes: 'list'
        },

        data: {
            ratio: 1,
            include: ['style', 'class'],
            'class': '',
            strokeAnimation: false
        },

        beforeConnect: function() {
            var this$1 = this;
            var assign;


            this.class += ' gsl-svg';

            if (!this.icon && includes(this.src, '#')) {

                var parts = this.src.split('#');

                if (parts.length > 1) {
                    (assign = parts, this.src = assign[0], this.icon = assign[1]);
                }
            }

            this.svg = this.getSvg().then(function (el) {
                this$1.applyAttributes(el);
                return this$1.svgEl = insertSVG(el, this$1.$el);
            }, noop);

        },

        disconnected: function() {
            var this$1 = this;


            if (isVoidElement(this.$el)) {
                attr(this.$el, 'hidden', null);
            }

            if (this.svg) {
                this.svg.then(function (svg) { return (!this$1._connected || svg !== this$1.svgEl) && remove(svg); }, noop);
            }

            this.svg = this.svgEl = null;

        },

        update: {

            read: function() {
                return !!(this.strokeAnimation && this.svgEl && isVisible(this.svgEl));
            },

            write: function() {
                applyAnimation(this.svgEl);
            },

            type: ['resize']

        },

        methods: {

            getSvg: function() {
                var this$1 = this;

                return loadSVG(this.src).then(function (svg) { return parseSVG(svg, this$1.icon) || Promise.reject('SVG not found.'); }
                );
            },

            applyAttributes: function(el) {
                var this$1 = this;


                for (var prop in this.$options.props) {
                    if (this[prop] && includes(this.include, prop)) {
                        attr(el, prop, this[prop]);
                    }
                }

                for (var attribute in this.attributes) {
                    var ref = this.attributes[attribute].split(':', 2);
                    var prop$1 = ref[0];
                    var value = ref[1];
                    attr(el, prop$1, value);
                }

                if (!this.id) {
                    removeAttr(el, 'id');
                }

                var props = ['width', 'height'];
                var dimensions = [this.width, this.height];

                if (!dimensions.some(function (val) { return val; })) {
                    dimensions = props.map(function (prop) { return attr(el, prop); });
                }

                var viewBox = attr(el, 'viewBox');
                if (viewBox && !dimensions.some(function (val) { return val; })) {
                    dimensions = viewBox.split(' ').slice(2);
                }

                dimensions.forEach(function (val, i) {
                    val = (val | 0) * this$1.ratio;
                    val && attr(el, props[i], val);

                    if (val && !dimensions[i ^ 1]) {
                        removeAttr(el, props[i ^ 1]);
                    }
                });

                attr(el, 'data-svg', this.icon || this.src);

            }

        }

    };

    var svgs = {};

    function loadSVG(src) {

        if (svgs[src]) {
            return svgs[src];
        }

        return svgs[src] = new Promise(function (resolve, reject) {

            if (!src) {
                reject();
                return;
            }

            if (startsWith(src, 'data:')) {
                resolve(decodeURIComponent(src.split(',')[1]));
            } else {

                ajax(src).then(
                    function (xhr) { return resolve(xhr.response); },
                    function () { return reject('SVG not found.'); }
                );

            }

        });
    }

    function parseSVG(svg, icon) {

        if (icon && includes(svg, '<symbol')) {
            svg = parseSymbols(svg, icon) || svg;
        }

        svg = $(svg.substr(svg.indexOf('<svg')));
        return svg && svg.hasChildNodes() && svg;
    }

    var symbolRe = /<symbol(.*?id=(['"])(.*?)\2[^]*?<\/)symbol>/g;
    var symbols = {};

    function parseSymbols(svg, icon) {

        if (!symbols[svg]) {

            symbols[svg] = {};

            var match;
            while ((match = symbolRe.exec(svg))) {
                symbols[svg][match[3]] = "<svg xmlns=\"http://www.w3.org/2000/svg\"" + (match[1]) + "svg>";
            }

            symbolRe.lastIndex = 0;

        }

        return symbols[svg][icon];
    }

    function applyAnimation(el) {

        var length = getMaxPathLength(el);

        if (length) {
            el.style.setProperty('--gsl-animation-stroke', length);
        }

    }

    function getMaxPathLength(el) {
        return Math.ceil(Math.max.apply(Math, $$('[stroke]', el).map(function (stroke) { return stroke.getTotalLength && stroke.getTotalLength() || 0; }
        ).concat([0])));
    }

    function insertSVG(el, root) {
        if (isVoidElement(root) || root.tagName === 'CANVAS') {

            attr(root, 'hidden', true);

            var next = root.nextElementSibling;
            return equals(el, next)
                ? next
                : after(root, el);

        } else {

            var last = root.lastElementChild;
            return equals(el, last)
                ? last
                : append(root, el);

        }
    }

    function equals(el, other) {
        return attr(el, 'data-svg') === attr(other, 'data-svg');
    }

    var closeIcon = "<svg width=\"14\" height=\"14\" viewBox=\"0 0 14 14\" xmlns=\"http://www.w3.org/2000/svg\"><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"1\" y1=\"1\" x2=\"13\" y2=\"13\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"13\" y1=\"1\" x2=\"1\" y2=\"13\"/></svg>";

    var closeLarge = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" x1=\"1\" y1=\"1\" x2=\"19\" y2=\"19\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" x1=\"19\" y1=\"1\" x2=\"1\" y2=\"19\"/></svg>";

    var marker = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><rect x=\"9\" y=\"4\" width=\"1\" height=\"11\"/><rect x=\"4\" y=\"9\" width=\"11\" height=\"1\"/></svg>";

    var navbarToggleIcon = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><rect y=\"9\" width=\"20\" height=\"2\"/><rect y=\"3\" width=\"20\" height=\"2\"/><rect y=\"15\" width=\"20\" height=\"2\"/></svg>";

    var overlayIcon = "<svg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"><rect x=\"19\" y=\"0\" width=\"1\" height=\"40\"/><rect x=\"0\" y=\"19\" width=\"40\" height=\"1\"/></svg>";

    var paginationNext = "<svg width=\"7\" height=\"12\" viewBox=\"0 0 7 12\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"1 1 6 6 1 11\"/></svg>";

    var paginationPrevious = "<svg width=\"7\" height=\"12\" viewBox=\"0 0 7 12\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"6 1 1 6 6 11\"/></svg>";

    var searchIcon = "<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" cx=\"9\" cy=\"9\" r=\"7\"/><path fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" d=\"M14,14 L18,18 L14,14 Z\"/></svg>";

    var searchLarge = "<svg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.8\" cx=\"17.5\" cy=\"17.5\" r=\"16.5\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.8\" x1=\"38\" y1=\"39\" x2=\"29\" y2=\"30\"/></svg>";

    var searchNavbar = "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><circle fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" cx=\"10.5\" cy=\"10.5\" r=\"9.5\"/><line fill=\"none\" stroke=\"#000\" stroke-width=\"1.1\" x1=\"23\" y1=\"23\" x2=\"17\" y2=\"17\"/></svg>";

    var slidenavNext = "<svg width=\"14px\" height=\"24px\" viewBox=\"0 0 14 24\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" points=\"1.225,23 12.775,12 1.225,1 \"/></svg>";

    var slidenavNextLarge = "<svg width=\"25px\" height=\"40px\" viewBox=\"0 0 25 40\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"2\" points=\"4.002,38.547 22.527,20.024 4,1.5 \"/></svg>";

    var slidenavPrevious = "<svg width=\"14px\" height=\"24px\" viewBox=\"0 0 14 24\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.4\" points=\"12.775,1 1.225,12 12.775,23 \"/></svg>";

    var slidenavPreviousLarge = "<svg width=\"25px\" height=\"40px\" viewBox=\"0 0 25 40\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"2\" points=\"20.527,1.5 2,20.024 20.525,38.547 \"/></svg>";

    var spinner = "<svg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"><circle fill=\"none\" stroke=\"#000\" cx=\"15\" cy=\"15\" r=\"14\"/></svg>";

    var totop = "<svg width=\"18\" height=\"10\" viewBox=\"0 0 18 10\" xmlns=\"http://www.w3.org/2000/svg\"><polyline fill=\"none\" stroke=\"#000\" stroke-width=\"1.2\" points=\"1 9 9 1 17 9 \"/></svg>";

    var parsed = {};
    var icons = {
        spinner: spinner,
        totop: totop,
        marker: marker,
        'close-icon': closeIcon,
        'close-large': closeLarge,
        'navbar-toggle-icon': navbarToggleIcon,
        'overlay-icon': overlayIcon,
        'pagination-next': paginationNext,
        'pagination-previous': paginationPrevious,
        'search-icon': searchIcon,
        'search-large': searchLarge,
        'search-navbar': searchNavbar,
        'slidenav-next': slidenavNext,
        'slidenav-next-large': slidenavNextLarge,
        'slidenav-previous': slidenavPrevious,
        'slidenav-previous-large': slidenavPreviousLarge
    };

    var Icon = {

        install: install,

        extends: Svg,

        args: 'icon',

        props: ['icon'],

        data: {include: []},

        isIcon: true,

        beforeConnect: function() {
            addClass(this.$el, 'gsl-icon');
        },

        methods: {

            getSvg: function() {

                var icon = getIcon(applyRtl(this.icon));

                if (!icon) {
                    return Promise.reject('Icon not found.');
                }

                return Promise.resolve(icon);
            }

        }

    };

    var IconComponent = {

        args: false,

        extends: Icon,

        data: function (vm) { return ({
            icon: hyphenate(vm.constructor.options.name)
        }); },

        beforeConnect: function() {
            addClass(this.$el, this.$name);
        }

    };

    var Slidenav = {

        extends: IconComponent,

        beforeConnect: function() {
            addClass(this.$el, 'gsl-slidenav');
        },

        computed: {

            icon: function(ref, $el) {
                var icon = ref.icon;

                return hasClass($el, 'gsl-slidenav-large')
                    ? (icon + "-large")
                    : icon;
            }

        }

    };

    var Search = {

        extends: IconComponent,

        computed: {

            icon: function(ref, $el) {
                var icon = ref.icon;

                return hasClass($el, 'gsl-search-icon') && parents($el, '.gsl-search-large').length
                    ? 'search-large'
                    : parents($el, '.gsl-search-navbar').length
                        ? 'search-navbar'
                        : icon;
            }

        }

    };

    var Close = {

        extends: IconComponent,

        computed: {

            icon: function() {
                return ("close-" + (hasClass(this.$el, 'gsl-close-large') ? 'large' : 'icon'));
            }

        }

    };

    var Spinner = {

        extends: IconComponent,

        connected: function() {
            var this$1 = this;

            this.svg.then(function (svg) { return this$1.ratio !== 1 && css($('circle', svg), 'strokeWidth', 1 / this$1.ratio); }, noop);
        }

    };

    function install(gslUIkit) {
        gslUIkit.icon.add = function (name, svg) {
            var obj;


            var added = isString(name) ? (( obj = {}, obj[name] = svg, obj )) : name;
            each(added, function (svg, name) {
                icons[name] = svg;
                delete parsed[name];
            });

            if (gslUIkit._initialized) {
                apply(document.body, function (el) { return each(gslUIkit.getComponents(el), function (cmp) {
                        cmp.$options.isIcon && cmp.icon in added && cmp.$reset();
                    }); }
                );
            }
        };
    }

    function getIcon(icon) {

        if (!icons[icon]) {
            return null;
        }

        if (!parsed[icon]) {
            parsed[icon] = $(icons[icon].trim());
        }

        return parsed[icon].cloneNode(true);
    }

    function applyRtl(icon) {
        return isRtl ? swap(swap(icon, 'left', 'right'), 'previous', 'next') : icon;
    }

    var Img = {

        args: 'dataSrc',

        props: {
            dataSrc: String,
            dataSrcset: Boolean,
            sizes: String,
            width: Number,
            height: Number,
            offsetTop: String,
            offsetLeft: String,
            target: String
        },

        data: {
            dataSrc: '',
            dataSrcset: false,
            sizes: false,
            width: false,
            height: false,
            offsetTop: '50vh',
            offsetLeft: 0,
            target: false
        },

        computed: {

            cacheKey: function(ref) {
                var dataSrc = ref.dataSrc;

                return ((this.$name) + "." + dataSrc);
            },

            width: function(ref) {
                var width = ref.width;
                var dataWidth = ref.dataWidth;

                return width || dataWidth;
            },

            height: function(ref) {
                var height = ref.height;
                var dataHeight = ref.dataHeight;

                return height || dataHeight;
            },

            sizes: function(ref) {
                var sizes = ref.sizes;
                var dataSizes = ref.dataSizes;

                return sizes || dataSizes;
            },

            isImg: function(_, $el) {
                return isImg($el);
            },

            target: {

                get: function(ref) {
                    var target = ref.target;

                    return [this.$el].concat(queryAll(target, this.$el));
                },

                watch: function() {
                    this.observe();
                }

            },

            offsetTop: function(ref) {
                var offsetTop = ref.offsetTop;

                return toPx(offsetTop, 'height');
            },

            offsetLeft: function(ref) {
                var offsetLeft = ref.offsetLeft;

                return toPx(offsetLeft, 'width');
            }

        },

        connected: function() {

            if (storage[this.cacheKey]) {
                setSrcAttrs(this.$el, storage[this.cacheKey] || this.dataSrc, this.dataSrcset, this.sizes);
            } else if (this.isImg && this.width && this.height) {
                setSrcAttrs(this.$el, getPlaceholderImage(this.width, this.height, this.sizes));
            }

            this.observer = new IntersectionObserver(this.load, {
                rootMargin: ((this.offsetTop) + "px " + (this.offsetLeft) + "px")
            });

            requestAnimationFrame(this.observe);

        },

        disconnected: function() {
            this.observer.disconnect();
        },

        update: {

            read: function(ref) {
                var this$1 = this;
                var image = ref.image;


                if (!image && document.readyState === 'complete') {
                    this.load(this.observer.takeRecords());
                }

                if (this.isImg) {
                    return false;
                }

                image && image.then(function (img) { return img && img.currentSrc !== '' && setSrcAttrs(this$1.$el, currentSrc(img)); });

            },

            write: function(data) {

                if (this.dataSrcset && window.devicePixelRatio !== 1) {

                    var bgSize = css(this.$el, 'backgroundSize');
                    if (bgSize.match(/^(auto\s?)+$/) || toFloat(bgSize) === data.bgSize) {
                        data.bgSize = getSourceSize(this.dataSrcset, this.sizes);
                        css(this.$el, 'backgroundSize', ((data.bgSize) + "px"));
                    }

                }

            },

            events: ['resize']

        },

        methods: {

            load: function(entries) {
                var this$1 = this;


                if (!entries.some(function (entry) { return entry.isIntersecting; })) {
                    return;
                }

                this._data.image = getImage(this.dataSrc, this.dataSrcset, this.sizes).then(function (img) {

                    setSrcAttrs(this$1.$el, currentSrc(img), img.srcset, img.sizes);
                    storage[this$1.cacheKey] = currentSrc(img);
                    return img;

                }, noop);

                this.observer.disconnect();
            },

            observe: function() {
                var this$1 = this;

                if (!this._data.image && this._connected) {
                    this.target.forEach(function (el) { return this$1.observer.observe(el); });
                }
            }

        }

    };

    function setSrcAttrs(el, src, srcset, sizes) {

        if (isImg(el)) {
            sizes && (el.sizes = sizes);
            srcset && (el.srcset = srcset);
            src && (el.src = src);
        } else if (src) {

            var change = !includes(el.style.backgroundImage, src);
            if (change) {
                css(el, 'backgroundImage', ("url(" + (escape(src)) + ")"));
                trigger(el, createEvent('load', false));
            }

        }

    }

    function getPlaceholderImage(width, height, sizes) {
        var assign;


        if (sizes) {
            ((assign = Dimensions.ratio({width: width, height: height}, 'width', toPx(sizesToPixel(sizes))), width = assign.width, height = assign.height));
        }

        return ("data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"" + width + "\" height=\"" + height + "\"></svg>");
    }

    var sizesRe = /\s*(.*?)\s*(\w+|calc\(.*?\))\s*(?:,|$)/g;
    function sizesToPixel(sizes) {
        var matches;

        sizesRe.lastIndex = 0;

        while ((matches = sizesRe.exec(sizes))) {
            if (!matches[1] || window.matchMedia(matches[1]).matches) {
                matches = evaluateSize(matches[2]);
                break;
            }
        }

        return matches || '100vw';
    }

    var sizeRe = /\d+(?:\w+|%)/g;
    var additionRe = /[+-]?(\d+)/g;
    function evaluateSize(size) {
        return startsWith(size, 'calc')
            ? size
                .substring(5, size.length - 1)
                .replace(sizeRe, function (size) { return toPx(size); })
                .replace(/ /g, '')
                .match(additionRe)
                .reduce(function (a, b) { return a + +b; }, 0)
            : size;
    }

    var srcSetRe = /\s+\d+w\s*(?:,|$)/g;
    function getSourceSize(srcset, sizes) {
        var srcSize = toPx(sizesToPixel(sizes));
        var descriptors = (srcset.match(srcSetRe) || []).map(toFloat).sort(function (a, b) { return a - b; });

        return descriptors.filter(function (size) { return size >= srcSize; })[0] || descriptors.pop() || '';
    }

    function isImg(el) {
        return el.tagName === 'IMG';
    }

    function currentSrc(el) {
        return el.currentSrc || el.src;
    }

    var key = '__test__';
    var storage;

    // workaround for Safari's private browsing mode and accessing sessionStorage in Blink
    try {
        storage = window.sessionStorage || {};
        storage[key] = 1;
        delete storage[key];
    } catch (e) {
        storage = {};
    }

    var Media = {

        props: {
            media: Boolean
        },

        data: {
            media: false
        },

        computed: {

            matchMedia: function() {
                var media = toMedia(this.media);
                return !media || window.matchMedia(media).matches;
            }

        }

    };

    function toMedia(value) {

        if (isString(value)) {
            if (value[0] === '@') {
                var name = "breakpoint-" + (value.substr(1));
                value = toFloat(getCssVar(name));
            } else if (isNaN(value)) {
                return value;
            }
        }

        return value && !isNaN(value) ? ("(min-width: " + value + "px)") : false;
    }

    var Leader = {

        mixins: [Class, Media],

        props: {
            fill: String
        },

        data: {
            fill: '',
            clsWrapper: 'gsl-leader-fill',
            clsHide: 'gsl-leader-hide',
            attrFill: 'data-fill'
        },

        computed: {

            fill: function(ref) {
                var fill = ref.fill;

                return fill || getCssVar('leader-fill-content');
            }

        },

        connected: function() {
            var assign;

            (assign = wrapInner(this.$el, ("<span class=\"" + (this.clsWrapper) + "\">")), this.wrapper = assign[0]);
        },

        disconnected: function() {
            unwrap(this.wrapper.childNodes);
        },

        update: {

            read: function(ref) {
                var changed = ref.changed;
                var width = ref.width;


                var prev = width;

                width = Math.floor(this.$el.offsetWidth / 2);

                return {
                    width: width,
                    fill: this.fill,
                    changed: changed || prev !== width,
                    hide: !this.matchMedia
                };
            },

            write: function(data) {

                toggleClass(this.wrapper, this.clsHide, data.hide);

                if (data.changed) {
                    data.changed = false;
                    attr(this.wrapper, this.attrFill, new Array(data.width).join(data.fill));
                }

            },

            events: ['resize']

        }

    };

    var Container = {

        props: {
            container: Boolean
        },

        data: {
            container: true
        },

        computed: {

            container: function(ref) {
                var container = ref.container;

                return container === true && this.$container || container && $(container);
            }

        }

    };

    var active$1;

    var Modal = {

        mixins: [Class, Container, Togglable],

        props: {
            selPanel: String,
            selClose: String,
            escClose: Boolean,
            bgClose: Boolean,
            stack: Boolean
        },

        data: {
            cls: 'gsl-open',
            escClose: true,
            bgClose: true,
            overlay: true,
            stack: false
        },

        computed: {

            panel: function(ref, $el) {
                var selPanel = ref.selPanel;

                return $(selPanel, $el);
            },

            transitionElement: function() {
                return this.panel;
            },

            bgClose: function(ref) {
                var bgClose = ref.bgClose;

                return bgClose && this.panel;
            }

        },

        beforeDisconnect: function() {
            if (this.isToggled()) {
                this.toggleNow(this.$el, false);
            }
        },

        events: [

            {

                name: 'click',

                delegate: function() {
                    return this.selClose;
                },

                handler: function(e) {
                    e.preventDefault();
                    this.hide();
                }

            },

            {

                name: 'toggle',

                self: true,

                handler: function(e) {

                    if (e.defaultPrevented) {
                        return;
                    }

                    e.preventDefault();
                    this.toggle();
                }

            },

            {
                name: 'beforeshow',

                self: true,

                handler: function(e) {

                    var prev = active$1 && active$1 !== this && active$1;

                    active$1 = this;

                    if (prev) {
                        if (this.stack) {
                            this.prev = prev;
                        } else {

                            active$1 = prev;

                            if (prev.isToggled()) {
                                prev.hide().then(this.show);
                            } else {
                                once(prev.$el, 'beforeshow hidden', this.show, false, function (ref) {
                                    var target = ref.target;
                                    var type = ref.type;

                                    return type === 'hidden' && target === prev.$el;
                                });
                            }
                            e.preventDefault();

                        }

                        return;
                    }

                    registerEvents();

                }

            },

            {

                name: 'show',

                self: true,

                handler: function() {

                    if (!hasClass(document.documentElement, this.clsPage)) {
                        this.scrollbarWidth = width(window) - width(document);
                        css(document.body, 'overflowY', this.scrollbarWidth && this.overlay ? 'scroll' : '');
                    }

                    addClass(document.documentElement, this.clsPage);

                }

            },

            {

                name: 'hide',

                self: true,

                handler: function() {
                    if (!active$1 || active$1 === this && !this.prev) {
                        deregisterEvents();
                    }
                }

            },

            {

                name: 'hidden',

                self: true,

                handler: function() {

                    var found;
                    var ref = this;
                    var prev = ref.prev;

                    active$1 = active$1 && active$1 !== this && active$1 || prev;

                    if (!active$1) {

                        css(document.body, 'overflowY', '');

                    } else {
                        while (prev) {

                            if (prev.clsPage === this.clsPage) {
                                found = true;
                                break;
                            }

                            prev = prev.prev;

                        }

                    }

                    if (!found) {
                        removeClass(document.documentElement, this.clsPage);
                    }

                }

            }

        ],

        methods: {

            toggle: function() {
                return this.isToggled() ? this.hide() : this.show();
            },

            show: function() {
                var this$1 = this;


                if (this.isToggled()) {
                    return Promise.resolve();
                }

                if (this.container && this.$el.parentNode !== this.container) {
                    append(this.container, this.$el);
                    return new Promise(function (resolve) { return requestAnimationFrame(function () { return this$1.show().then(resolve); }
                        ); }
                    );
                }

                return this.toggleElement(this.$el, true, animate$1(this));
            },

            hide: function() {
                return this.isToggled()
                    ? this.toggleElement(this.$el, false, animate$1(this))
                    : Promise.resolve();
            },

            getActive: function() {
                return active$1;
            }

        }

    };

    var events;

    function registerEvents() {

        if (events) {
            return;
        }

        events = [
            on(document, pointerUp, function (ref) {
                var target = ref.target;
                var defaultPrevented = ref.defaultPrevented;

                if (active$1 && active$1.bgClose && !defaultPrevented && (!active$1.overlay || within(target, active$1.$el)) && !within(target, active$1.panel)) {
                    active$1.hide();
                }
            }),
            on(document, 'keydown', function (e) {
                if (e.keyCode === 27 && active$1 && active$1.escClose) {
                    e.preventDefault();
                    active$1.hide();
                }
            })
        ];
    }

    function deregisterEvents() {
        events && events.forEach(function (unbind) { return unbind(); });
        events = null;
    }

    function animate$1(ref) {
        var transitionElement = ref.transitionElement;
        var _toggle = ref._toggle;

        return function (el, show) { return new Promise(function (resolve, reject) { return once(el, 'show hide', function () {
                    el._reject && el._reject();
                    el._reject = reject;

                    _toggle(el, show);

                    if (toMs(css(transitionElement, 'transitionDuration'))) {
                        once(transitionElement, 'transitionend', resolve, false, function (e) { return e.target === transitionElement; });
                    } else {
                        resolve();
                    }
                }); }
            ); };
    }

    var Modal$1 = {

        install: install$1,

        mixins: [Modal],

        data: {
            clsPage: 'gsl-modal-page',
            selPanel: '.gsl-modal-dialog',
            selClose: '.gsl-modal-close, .gsl-modal-close-default, .gsl-modal-close-outside, .gsl-modal-close-full'
        },

        events: [

            {
                name: 'show',

                self: true,

                handler: function() {

                    if (hasClass(this.panel, 'gsl-margin-auto-vertical')) {
                        addClass(this.$el, 'gsl-flex');
                    } else {
                        css(this.$el, 'display', 'block');
                    }

                    height(this.$el); // force reflow
                }
            },

            {
                name: 'hidden',

                self: true,

                handler: function() {

                    css(this.$el, 'display', '');
                    removeClass(this.$el, 'gsl-flex');

                }
            }

        ]

    };

    function install$1(gslUIkit) {

        gslUIkit.modal.dialog = function (content, options) {

            var dialog = gslUIkit.modal((" <div class=\"gsl-modal\"> <div class=\"gsl-modal-dialog\">" + content + "</div> </div> "), options);

            dialog.show();

            on(dialog.$el, 'hidden', function (ref) {
                var target = ref.target;
                var currentTarget = ref.currentTarget;

                if (target === currentTarget) {
                    Promise.resolve(function () { return dialog.$destroy(true); });
                }
            });

            return dialog;
        };

        gslUIkit.modal.alert = function (message, options) {

            options = assign({bgClose: false, escClose: false, labels: gslUIkit.modal.labels}, options);

            return new Promise(
                function (resolve) { return on(gslUIkit.modal.dialog((" <div class=\"gsl-modal-body\">" + (isString(message) ? message : html(message)) + "</div> <div class=\"gsl-modal-footer gsl-text-right\"> <button class=\"gsl-button gsl-button-primary gsl-modal-close\" autofocus>" + (options.labels.ok) + "</button> </div> "), options).$el, 'hide', resolve); }
            );
        };

        gslUIkit.modal.confirm = function (message, options) {

            options = assign({bgClose: false, escClose: true, labels: gslUIkit.modal.labels}, options);

            return new Promise(function (resolve, reject) {

                var confirm = gslUIkit.modal.dialog((" <form> <div class=\"gsl-modal-body\">" + (isString(message) ? message : html(message)) + "</div> <div class=\"gsl-modal-footer gsl-text-right\"> <button class=\"gsl-button gsl-button-default gsl-modal-close\" type=\"button\">" + (options.labels.cancel) + "</button> <button class=\"gsl-button gsl-button-primary\" autofocus>" + (options.labels.ok) + "</button> </div> </form> "), options);

                var resolved = false;

                on(confirm.$el, 'submit', 'form', function (e) {
                    e.preventDefault();
                    resolve();
                    resolved = true;
                    confirm.hide();
                });
                on(confirm.$el, 'hide', function () {
                    if (!resolved) {
                        reject();
                    }
                });

            });
        };

        gslUIkit.modal.prompt = function (message, value, options) {

            options = assign({bgClose: false, escClose: true, labels: gslUIkit.modal.labels}, options);

            return new Promise(function (resolve) {

                var prompt = gslUIkit.modal.dialog((" <form class=\"gsl-form-stacked\"> <div class=\"gsl-modal-body\"> <label>" + (isString(message) ? message : html(message)) + "</label> <input class=\"gsl-input\" autofocus> </div> <div class=\"gsl-modal-footer gsl-text-right\"> <button class=\"gsl-button gsl-button-default gsl-modal-close\" type=\"button\">" + (options.labels.cancel) + "</button> <button class=\"gsl-button gsl-button-primary\">" + (options.labels.ok) + "</button> </div> </form> "), options),
                    input = $('input', prompt.$el);

                input.value = value;

                var resolved = false;

                on(prompt.$el, 'submit', 'form', function (e) {
                    e.preventDefault();
                    resolve(input.value);
                    resolved = true;
                    prompt.hide();
                });
                on(prompt.$el, 'hide', function () {
                    if (!resolved) {
                        resolve(null);
                    }
                });

            });
        };

        gslUIkit.modal.labels = {
            ok: 'Ok',
            cancel: 'Cancel'
        };

    }

    var Nav = {

        extends: Accordion,

        data: {
            targets: '> .gsl-parent',
            toggle: '> a',
            content: '> ul'
        }

    };

    var Navbar = {

        mixins: [Class, FlexBug],

        props: {
            dropdown: String,
            mode: 'list',
            align: String,
            offset: Number,
            boundary: Boolean,
            boundaryAlign: Boolean,
            clsDrop: String,
            delayShow: Number,
            delayHide: Number,
            dropbar: Boolean,
            dropbarMode: String,
            dropbarAnchor: Boolean,
            duration: Number
        },

        data: {
            dropdown: '.gsl-navbar-nav > li',
            align: !isRtl ? 'left' : 'right',
            clsDrop: 'gsl-navbar-dropdown',
            mode: undefined,
            offset: undefined,
            delayShow: undefined,
            delayHide: undefined,
            boundaryAlign: undefined,
            flip: 'x',
            boundary: true,
            dropbar: false,
            dropbarMode: 'slide',
            dropbarAnchor: false,
            duration: 200,
            forceHeight: true,
            selMinHeight: '.gsl-navbar-nav > li > a, .gsl-navbar-item, .gsl-navbar-toggle'
        },

        computed: {

            boundary: function(ref, $el) {
                var boundary = ref.boundary;
                var boundaryAlign = ref.boundaryAlign;

                return (boundary === true || boundaryAlign) ? $el : boundary;
            },

            dropbarAnchor: function(ref, $el) {
                var dropbarAnchor = ref.dropbarAnchor;

                return query(dropbarAnchor, $el);
            },

            pos: function(ref) {
                var align = ref.align;

                return ("bottom-" + align);
            },

            dropdowns: function(ref, $el) {
                var dropdown = ref.dropdown;
                var clsDrop = ref.clsDrop;

                return $$((dropdown + " ." + clsDrop), $el);
            }

        },

        beforeConnect: function() {

            var ref = this.$props;
            var dropbar = ref.dropbar;

            this.dropbar = dropbar && (query(dropbar, this.$el) || $('+ .gsl-navbar-dropbar', this.$el) || $('<div></div>'));

            if (this.dropbar) {

                addClass(this.dropbar, 'gsl-navbar-dropbar');

                if (this.dropbarMode === 'slide') {
                    addClass(this.dropbar, 'gsl-navbar-dropbar-slide');
                }
            }

        },

        disconnected: function() {
            this.dropbar && remove(this.dropbar);
        },

        update: function() {
            var this$1 = this;


            this.$create(
                'drop',
                this.dropdowns.filter(function (el) { return !this$1.getDropdown(el); }),
                assign({}, this.$props, {boundary: this.boundary, pos: this.pos, offset: this.dropbar || this.offset})
            );

        },

        events: [

            {
                name: 'mouseover',

                delegate: function() {
                    return this.dropdown;
                },

                handler: function(ref) {
                    var current = ref.current;

                    var active = this.getActive();
                    if (active && active.toggle && !within(active.toggle.$el, current) && !active.tracker.movesTo(active.$el)) {
                        active.hide(false);
                    }
                }

            },

            {
                name: 'mouseleave',

                el: function() {
                    return this.dropbar;
                },

                handler: function() {
                    var active = this.getActive();

                    if (active && !this.dropdowns.some(function (el) { return matches(el, ':hover'); })) {
                        active.hide();
                    }
                }
            },

            {
                name: 'beforeshow',

                capture: true,

                filter: function() {
                    return this.dropbar;
                },

                handler: function() {

                    if (!this.dropbar.parentNode) {
                        after(this.dropbarAnchor || this.$el, this.dropbar);
                    }

                }
            },

            {
                name: 'show',

                capture: true,

                filter: function() {
                    return this.dropbar;
                },

                handler: function(_, drop) {

                    var $el = drop.$el;
                    var dir = drop.dir;

                    this.clsDrop && addClass($el, ((this.clsDrop) + "-dropbar"));

                    if (dir === 'bottom') {
                        this.transitionTo($el.offsetHeight + toFloat(css($el, 'marginTop')) + toFloat(css($el, 'marginBottom')), $el);
                    }
                }
            },

            {
                name: 'beforehide',

                filter: function() {
                    return this.dropbar;
                },

                handler: function(e, ref) {
                    var $el = ref.$el;


                    var active = this.getActive();

                    if (matches(this.dropbar, ':hover') && active && active.$el === $el) {
                        e.preventDefault();
                    }
                }
            },

            {
                name: 'hide',

                filter: function() {
                    return this.dropbar;
                },

                handler: function(_, ref) {
                    var $el = ref.$el;


                    var active = this.getActive();

                    if (!active || active && active.$el === $el) {
                        this.transitionTo(0);
                    }
                }
            }

        ],

        methods: {

            getActive: function() {
                var ref = this.dropdowns.map(this.getDropdown).filter(function (drop) { return drop && drop.isActive(); });
                var active = ref[0];
                return active && includes(active.mode, 'hover') && within(active.toggle.$el, this.$el) && active;
            },

            transitionTo: function(newHeight, el) {
                var this$1 = this;


                var ref = this;
                var dropbar = ref.dropbar;
                var oldHeight = isVisible(dropbar) ? height(dropbar) : 0;

                el = oldHeight < newHeight && el;

                css(el, 'clip', ("rect(0," + (el.offsetWidth) + "px," + oldHeight + "px,0)"));

                height(dropbar, oldHeight);

                Transition.cancel([el, dropbar]);
                return Promise.all([
                    Transition.start(dropbar, {height: newHeight}, this.duration),
                    Transition.start(el, {clip: ("rect(0," + (el.offsetWidth) + "px," + newHeight + "px,0)")}, this.duration)
                ])
                    .catch(noop)
                    .then(function () {
                        css(el, {clip: ''});
                        this$1.$update(dropbar);
                    });
            },

            getDropdown: function(el) {
                return this.$getComponent(el, 'drop') || this.$getComponent(el, 'dropdown');
            }

        }

    };

    var Offcanvas = {

        mixins: [Modal],

        args: 'mode',

        props: {
            mode: String,
            flip: Boolean,
            overlay: Boolean
        },

        data: {
            mode: 'slide',
            flip: false,
            overlay: false,
            clsPage: 'gsl-offcanvas-page',
            clsContainer: 'gsl-offcanvas-container',
            selPanel: '.gsl-offcanvas-bar',
            clsFlip: 'gsl-offcanvas-flip',
            clsContainerAnimation: 'gsl-offcanvas-container-animation',
            clsSidebarAnimation: 'gsl-offcanvas-bar-animation',
            clsMode: 'gsl-offcanvas',
            clsOverlay: 'gsl-offcanvas-overlay',
            selClose: '.gsl-offcanvas-close'
        },

        computed: {

            clsFlip: function(ref) {
                var flip = ref.flip;
                var clsFlip = ref.clsFlip;

                return flip ? clsFlip : '';
            },

            clsOverlay: function(ref) {
                var overlay = ref.overlay;
                var clsOverlay = ref.clsOverlay;

                return overlay ? clsOverlay : '';
            },

            clsMode: function(ref) {
                var mode = ref.mode;
                var clsMode = ref.clsMode;

                return (clsMode + "-" + mode);
            },

            clsSidebarAnimation: function(ref) {
                var mode = ref.mode;
                var clsSidebarAnimation = ref.clsSidebarAnimation;

                return mode === 'none' || mode === 'reveal' ? '' : clsSidebarAnimation;
            },

            clsContainerAnimation: function(ref) {
                var mode = ref.mode;
                var clsContainerAnimation = ref.clsContainerAnimation;

                return mode !== 'push' && mode !== 'reveal' ? '' : clsContainerAnimation;
            },

            transitionElement: function(ref) {
                var mode = ref.mode;

                return mode === 'reveal' ? this.panel.parentNode : this.panel;
            }

        },

        events: [

            {

                name: 'click',

                delegate: function() {
                    return 'a[href^="#"]';
                },

                handler: function(ref) {
                    var current = ref.current;

                    if (current.hash && $(current.hash, document.body)) {
                        this.hide();
                    }
                }

            },

            {
                name: 'touchstart',

                passive: true,

                el: function() {
                    return this.panel;
                },

                handler: function(ref) {
                    var targetTouches = ref.targetTouches;


                    if (targetTouches.length === 1) {
                        this.clientY = targetTouches[0].clientY;
                    }

                }

            },

            {
                name: 'touchmove',

                self: true,
                passive: false,

                filter: function() {
                    return this.overlay;
                },

                handler: function(e) {
                    e.cancelable && e.preventDefault();
                }

            },

            {
                name: 'touchmove',

                passive: false,

                el: function() {
                    return this.panel;
                },

                handler: function(e) {

                    if (e.targetTouches.length !== 1) {
                        return;
                    }

                    var clientY = event.targetTouches[0].clientY - this.clientY;
                    var ref = this.panel;
                    var scrollTop = ref.scrollTop;
                    var scrollHeight = ref.scrollHeight;
                    var clientHeight = ref.clientHeight;

                    if (clientHeight >= scrollHeight
                        || scrollTop === 0 && clientY > 0
                        || scrollHeight - scrollTop <= clientHeight && clientY < 0
                    ) {
                        e.cancelable && e.preventDefault();
                    }

                }

            },

            {
                name: 'show',

                self: true,

                handler: function() {

                    if (this.mode === 'reveal' && !hasClass(this.panel.parentNode, this.clsMode)) {
                        wrapAll(this.panel, '<div>');
                        addClass(this.panel.parentNode, this.clsMode);
                    }

                    css(document.documentElement, 'overflowY', this.overlay ? 'hidden' : '');
                    addClass(document.body, this.clsContainer, this.clsFlip);
                    css(this.$el, 'display', 'block');
                    addClass(this.$el, this.clsOverlay);
                    addClass(this.panel, this.clsSidebarAnimation, this.mode !== 'reveal' ? this.clsMode : '');

                    height(document.body); // force reflow
                    addClass(document.body, this.clsContainerAnimation);

                    this.clsContainerAnimation && suppressUserScale();

                }
            },

            {
                name: 'hide',

                self: true,

                handler: function() {
                    removeClass(document.body, this.clsContainerAnimation);

                    var active = this.getActive();
                    if (this.mode === 'none' || active && active !== this && active !== this.prev) {
                        trigger(this.panel, 'transitionend');
                    }
                }
            },

            {
                name: 'hidden',

                self: true,

                handler: function() {

                    this.clsContainerAnimation && resumeUserScale();

                    if (this.mode === 'reveal') {
                        unwrap(this.panel);
                    }

                    removeClass(this.panel, this.clsSidebarAnimation, this.clsMode);
                    removeClass(this.$el, this.clsOverlay);
                    css(this.$el, 'display', '');
                    removeClass(document.body, this.clsContainer, this.clsFlip);

                    css(document.documentElement, 'overflowY', '');

                }
            },

            {
                name: 'swipeLeft swipeRight',

                handler: function(e) {

                    if (this.isToggled() && endsWith(e.type, 'Left') ^ this.flip) {
                        this.hide();
                    }

                }
            }

        ]

    };

    // Chrome in responsive mode zooms page upon opening offcanvas
    function suppressUserScale() {
        getViewport().content += ',user-scalable=0';
    }

    function resumeUserScale() {
        var viewport = getViewport();
        viewport.content = viewport.content.replace(/,user-scalable=0$/, '');
    }

    function getViewport() {
        return $('meta[name="viewport"]', document.head) || append(document.head, '<meta name="viewport">');
    }

    var OverflowAuto = {

        mixins: [Class],

        props: {
            selContainer: String,
            selContent: String
        },

        data: {
            selContainer: '.gsl-modal',
            selContent: '.gsl-modal-dialog'
        },

        computed: {

            container: function(ref, $el) {
                var selContainer = ref.selContainer;

                return closest($el, selContainer);
            },

            content: function(ref, $el) {
                var selContent = ref.selContent;

                return closest($el, selContent);
            }

        },

        connected: function() {
            css(this.$el, 'minHeight', 150);
        },

        update: {

            read: function() {

                if (!this.content || !this.container) {
                    return false;
                }

                return {
                    current: toFloat(css(this.$el, 'maxHeight')),
                    max: Math.max(150, height(this.container) - (offset(this.content).height - height(this.$el)))
                };
            },

            write: function(ref) {
                var current = ref.current;
                var max = ref.max;

                css(this.$el, 'maxHeight', max);
                if (Math.round(current) !== Math.round(max)) {
                    trigger(this.$el, 'resize');
                }
            },

            events: ['resize']

        }

    };

    var Responsive = {

        props: ['width', 'height'],

        connected: function() {
            addClass(this.$el, 'gsl-responsive-width');
        },

        update: {

            read: function() {
                return isVisible(this.$el) && this.width && this.height
                    ? {width: width(this.$el.parentNode), height: this.height}
                    : false;
            },

            write: function(dim) {
                height(this.$el, Dimensions.contain({
                    height: this.height,
                    width: this.width
                }, dim).height);
            },

            events: ['resize']

        }

    };

    var Scroll = {

        props: {
            duration: Number,
            offset: Number
        },

        data: {
            duration: 1000,
            offset: 0
        },

        methods: {

            scrollTo: function(el) {
                var this$1 = this;


                el = el && $(el) || document.body;

                var docHeight = height(document);
                var winHeight = height(window);

                var target = offset(el).top - this.offset;
                if (target + winHeight > docHeight) {
                    target = docHeight - winHeight;
                }

                if (!trigger(this.$el, 'beforescroll', [this, el])) {
                    return;
                }

                var start = Date.now();
                var startY = window.pageYOffset;
                var step = function () {

                    var currentY = startY + (target - startY) * ease(clamp((Date.now() - start) / this$1.duration));

                    scrollTop(window, currentY);

                    // scroll more if we have not reached our destination
                    if (currentY !== target) {
                        requestAnimationFrame(step);
                    } else {
                        trigger(this$1.$el, 'scrolled', [this$1, el]);
                    }

                };

                step();

            }

        },

        events: {

            click: function(e) {

                if (e.defaultPrevented) {
                    return;
                }

                e.preventDefault();
                this.scrollTo(escape(decodeURIComponent(this.$el.hash)).substr(1));
            }

        }

    };

    function ease(k) {
        return 0.5 * (1 - Math.cos(Math.PI * k));
    }

    var Scrollspy = {

        args: 'cls',

        props: {
            cls: String,
            target: String,
            hidden: Boolean,
            offsetTop: Number,
            offsetLeft: Number,
            repeat: Boolean,
            delay: Number
        },

        data: function () { return ({
            cls: false,
            target: false,
            hidden: true,
            offsetTop: 0,
            offsetLeft: 0,
            repeat: false,
            delay: 0,
            inViewClass: 'gsl-scrollspy-inview'
        }); },

        computed: {

            elements: function(ref, $el) {
                var target = ref.target;

                return target ? $$(target, $el) : [$el];
            }

        },

        update: [

            {

                write: function() {
                    if (this.hidden) {
                        css(filter(this.elements, (":not(." + (this.inViewClass) + ")")), 'visibility', 'hidden');
                    }
                }

            },

            {

                read: function(ref) {
                    var this$1 = this;
                    var update = ref.update;


                    if (!update) {
                        return;
                    }

                    this.elements.forEach(function (el) {

                        var state = el._ukScrollspyState;

                        if (!state) {
                            state = {cls: data(el, 'gsl-scrollspy-class') || this$1.cls};
                        }

                        state.show = isInView(el, this$1.offsetTop, this$1.offsetLeft);
                        el._ukScrollspyState = state;

                    });

                },

                write: function(data) {
                    var this$1 = this;


                    // Let child components be applied at least once first
                    if (!data.update) {
                        this.$emit();
                        return data.update = true;
                    }

                    this.elements.forEach(function (el) {

                        var state = el._ukScrollspyState;
                        var cls = state.cls;

                        if (state.show && !state.inview && !state.queued) {

                            var show = function () {

                                css(el, 'visibility', '');
                                addClass(el, this$1.inViewClass);
                                toggleClass(el, cls);

                                trigger(el, 'inview');

                                this$1.$update(el);

                                state.inview = true;
                                state.abort && state.abort();
                            };

                            if (this$1.delay) {

                                state.queued = true;
                                data.promise = (data.promise || Promise.resolve()).then(function () {
                                    return !state.inview && new Promise(function (resolve) {

                                        var timer = setTimeout(function () {

                                            show();
                                            resolve();

                                        }, data.promise || this$1.elements.length === 1 ? this$1.delay : 0);

                                        state.abort = function () {
                                            clearTimeout(timer);
                                            resolve();
                                            state.queued = false;
                                        };

                                    });

                                });

                            } else {
                                show();
                            }

                        } else if (!state.show && (state.inview || state.queued) && this$1.repeat) {

                            state.abort && state.abort();

                            if (!state.inview) {
                                return;
                            }

                            css(el, 'visibility', this$1.hidden ? 'hidden' : '');
                            removeClass(el, this$1.inViewClass);
                            toggleClass(el, cls);

                            trigger(el, 'outview');

                            this$1.$update(el);

                            state.inview = false;

                        }


                    });

                },

                events: ['scroll', 'resize']

            }

        ]

    };

    var ScrollspyNav = {

        props: {
            cls: String,
            closest: String,
            scroll: Boolean,
            overflow: Boolean,
            offset: Number
        },

        data: {
            cls: 'gsl-active',
            closest: false,
            scroll: false,
            overflow: true,
            offset: 0
        },

        computed: {

            links: function(_, $el) {
                return $$('a[href^="#"]', $el).filter(function (el) { return el.hash; });
            },

            elements: function(ref) {
                var selector = ref.closest;

                return closest(this.links, selector || '*');
            },

            targets: function() {
                return $$(this.links.map(function (el) { return escape(el.hash).substr(1); }).join(','));
            }

        },

        update: [

            {

                read: function() {
                    if (this.scroll) {
                        this.$create('scroll', this.links, {offset: this.offset || 0});
                    }
                }

            },

            {

                read: function(data) {
                    var this$1 = this;


                    var scroll = window.pageYOffset + this.offset + 1;
                    var max = height(document) - height(window) + this.offset;

                    data.active = false;

                    this.targets.every(function (el, i) {

                        var ref = offset(el);
                        var top = ref.top;
                        var last = i + 1 === this$1.targets.length;

                        if (!this$1.overflow && (i === 0 && top > scroll || last && top + el.offsetTop < scroll)) {
                            return false;
                        }

                        if (!last && offset(this$1.targets[i + 1]).top <= scroll) {
                            return true;
                        }

                        if (scroll >= max) {
                            for (var j = this$1.targets.length - 1; j > i; j--) {
                                if (isInView(this$1.targets[j])) {
                                    el = this$1.targets[j];
                                    break;
                                }
                            }
                        }

                        return !(data.active = $(filter(this$1.links, ("[href=\"#" + (el.id) + "\"]"))));

                    });

                },

                write: function(ref) {
                    var active = ref.active;


                    this.links.forEach(function (el) { return el.blur(); });
                    removeClass(this.elements, this.cls);

                    if (active) {
                        trigger(this.$el, 'active', [active, addClass(this.closest ? closest(active, this.closest) : active, this.cls)]);
                    }

                },

                events: ['scroll', 'resize']

            }

        ]

    };

    var Sticky = {

        mixins: [Class, Media],

        props: {
            top: null,
            bottom: Boolean,
            offset: Number,
            animation: String,
            clsActive: String,
            clsInactive: String,
            clsFixed: String,
            clsBelow: String,
            selTarget: String,
            widthElement: Boolean,
            showOnUp: Boolean,
            targetOffset: Number
        },

        data: {
            top: 0,
            bottom: false,
            offset: 0,
            animation: '',
            clsActive: 'gsl-active',
            clsInactive: '',
            clsFixed: 'gsl-sticky-fixed',
            clsBelow: 'gsl-sticky-below',
            selTarget: '',
            widthElement: false,
            showOnUp: false,
            targetOffset: false
        },

        computed: {

            selTarget: function(ref, $el) {
                var selTarget = ref.selTarget;

                return selTarget && $(selTarget, $el) || $el;
            },

            widthElement: function(ref, $el) {
                var widthElement = ref.widthElement;

                return query(widthElement, $el) || this.placeholder;
            },

            isActive: {

                get: function() {
                    return hasClass(this.selTarget, this.clsActive);
                },

                set: function(value) {
                    if (value && !this.isActive) {
                        replaceClass(this.selTarget, this.clsInactive, this.clsActive);
                        trigger(this.$el, 'active');
                    } else if (!value && !hasClass(this.selTarget, this.clsInactive)) {
                        replaceClass(this.selTarget, this.clsActive, this.clsInactive);
                        trigger(this.$el, 'inactive');
                    }
                }

            }

        },

        connected: function() {
            this.placeholder = $('+ .gsl-sticky-placeholder', this.$el) || $('<div class="gsl-sticky-placeholder"></div>');
            this.isFixed = false;
            this.isActive = false;
        },

        disconnected: function() {

            if (this.isFixed) {
                this.hide();
                removeClass(this.selTarget, this.clsInactive);
            }

            remove(this.placeholder);
            this.placeholder = null;
            this.widthElement = null;
        },

        events: [

            {

                name: 'load hashchange popstate',

                el: window,

                handler: function() {
                    var this$1 = this;


                    if (!(this.targetOffset !== false && location.hash && window.pageYOffset > 0)) {
                        return;
                    }

                    var target = $(location.hash);

                    if (target) {
                        fastdom.read(function () {

                            var ref = offset(target);
                            var top = ref.top;
                            var elTop = offset(this$1.$el).top;
                            var elHeight = this$1.$el.offsetHeight;

                            if (this$1.isFixed && elTop + elHeight >= top && elTop <= top + target.offsetHeight) {
                                scrollTop(window, top - elHeight - (isNumeric(this$1.targetOffset) ? this$1.targetOffset : 0) - this$1.offset);
                            }

                        });
                    }

                }

            }

        ],

        update: [

            {

                read: function(ref, type) {
                    var height = ref.height;


                    if (this.isActive && type !== 'update') {

                        this.hide();
                        height = this.$el.offsetHeight;
                        this.show();

                    }

                    height = !this.isActive ? this.$el.offsetHeight : height;

                    this.topOffset = offset(this.isFixed ? this.placeholder : this.$el).top;
                    this.bottomOffset = this.topOffset + height;

                    var bottom = parseProp('bottom', this);

                    this.top = Math.max(toFloat(parseProp('top', this)), this.topOffset) - this.offset;
                    this.bottom = bottom && bottom - height;
                    this.inactive = !this.matchMedia;

                    return {
                        lastScroll: false,
                        height: height,
                        margins: css(this.$el, ['marginTop', 'marginBottom', 'marginLeft', 'marginRight'])
                    };
                },

                write: function(ref) {
                    var height = ref.height;
                    var margins = ref.margins;


                    var ref$1 = this;
                    var placeholder = ref$1.placeholder;

                    css(placeholder, assign({height: height}, margins));

                    if (!within(placeholder, document)) {
                        after(this.$el, placeholder);
                        attr(placeholder, 'hidden', '');
                    }

                    // ensure active/inactive classes are applied
                    this.isActive = this.isActive;

                },

                events: ['resize']

            },

            {

                read: function(ref) {
                    var scroll = ref.scroll; if ( scroll === void 0 ) scroll = 0;


                    this.width = (isVisible(this.widthElement) ? this.widthElement : this.$el).offsetWidth;

                    this.scroll = window.pageYOffset;

                    return {
                        dir: scroll <= this.scroll ? 'down' : 'up',
                        scroll: this.scroll,
                        visible: isVisible(this.$el),
                        top: offsetPosition(this.placeholder)[0]
                    };
                },

                write: function(data, type) {
                    var this$1 = this;


                    var initTimestamp = data.initTimestamp; if ( initTimestamp === void 0 ) initTimestamp = 0;
                    var dir = data.dir;
                    var lastDir = data.lastDir;
                    var lastScroll = data.lastScroll;
                    var scroll = data.scroll;
                    var top = data.top;
                    var visible = data.visible;
                    var now = performance.now();

                    data.lastScroll = scroll;

                    if (scroll < 0 || scroll === lastScroll || !visible || this.disabled || this.showOnUp && type !== 'scroll') {
                        return;
                    }

                    if (now - initTimestamp > 300 || dir !== lastDir) {
                        data.initScroll = scroll;
                        data.initTimestamp = now;
                    }

                    data.lastDir = dir;

                    if (this.showOnUp && Math.abs(data.initScroll - scroll) <= 30 && Math.abs(lastScroll - scroll) <= 10) {
                        return;
                    }

                    if (this.inactive
                        || scroll < this.top
                        || this.showOnUp && (scroll <= this.top || dir === 'down' || dir === 'up' && !this.isFixed && scroll <= this.bottomOffset)
                    ) {

                        if (!this.isFixed) {

                            if (Animation.inProgress(this.$el) && top > scroll) {
                                Animation.cancel(this.$el);
                                this.hide();
                            }

                            return;
                        }

                        this.isFixed = false;

                        if (this.animation && scroll > this.topOffset) {
                            Animation.cancel(this.$el);
                            Animation.out(this.$el, this.animation).then(function () { return this$1.hide(); }, noop);
                        } else {
                            this.hide();
                        }

                    } else if (this.isFixed) {

                        this.update();

                    } else if (this.animation) {

                        Animation.cancel(this.$el);
                        this.show();
                        Animation.in(this.$el, this.animation).catch(noop);

                    } else {
                        this.show();
                    }

                },

                events: ['resize', 'scroll']

            }

        ],

        methods: {

            show: function() {

                this.isFixed = true;
                this.update();
                attr(this.placeholder, 'hidden', null);

            },

            hide: function() {

                this.isActive = false;
                removeClass(this.$el, this.clsFixed, this.clsBelow);
                css(this.$el, {position: '', top: '', width: ''});
                attr(this.placeholder, 'hidden', '');

            },

            update: function() {

                var active = this.top !== 0 || this.scroll > this.top;
                var top = Math.max(0, this.offset);

                if (this.bottom && this.scroll > this.bottom - this.offset) {
                    top = this.bottom - this.scroll;
                }

                css(this.$el, {
                    position: 'fixed',
                    top: (top + "px"),
                    width: this.width
                });

                this.isActive = active;
                toggleClass(this.$el, this.clsBelow, this.scroll > this.bottomOffset);
                addClass(this.$el, this.clsFixed);

            }

        }

    };

    function parseProp(prop, ref) {
        var $props = ref.$props;
        var $el = ref.$el;
        var propOffset = ref[(prop + "Offset")];


        var value = $props[prop];

        if (!value) {
            return;
        }

        if (isNumeric(value)) {

            return propOffset + toFloat(value);

        } else if (isString(value) && value.match(/^-?\d+vh$/)) {

            return height(window) * toFloat(value) / 100;

        } else {

            var el = value === true ? $el.parentNode : query(value, $el);

            if (el) {
                return offset(el).top + el.offsetHeight;
            }

        }
    }

    var Switcher = {

        mixins: [Togglable],

        args: 'connect',

        props: {
            connect: String,
            toggle: String,
            active: Number,
            swiping: Boolean
        },

        data: {
            connect: '~.gsl-switcher',
            toggle: '> * > :first-child',
            active: 0,
            swiping: true,
            cls: 'gsl-active',
            clsContainer: 'gsl-switcher',
            attrItem: 'gsl-switcher-item',
            queued: true
        },

        computed: {

            connects: function(ref, $el) {
                var connect = ref.connect;

                return queryAll(connect, $el);
            },

            toggles: function(ref, $el) {
                var toggle = ref.toggle;

                return $$(toggle, $el);
            }

        },

        events: [

            {

                name: 'click',

                delegate: function() {
                    return ((this.toggle) + ":not(.gsl-disabled)");
                },

                handler: function(e) {
                    e.preventDefault();
                    this.show(toNodes(this.$el.children).filter(function (el) { return within(e.current, el); })[0]);
                }

            },

            {
                name: 'click',

                el: function() {
                    return this.connects;
                },

                delegate: function() {
                    return ("[" + (this.attrItem) + "],[data-" + (this.attrItem) + "]");
                },

                handler: function(e) {
                    e.preventDefault();
                    this.show(data(e.current, this.attrItem));
                }
            },

            {
                name: 'swipeRight swipeLeft',

                filter: function() {
                    return this.swiping;
                },

                el: function() {
                    return this.connects;
                },

                handler: function(ref) {
                    var type = ref.type;

                    this.show(endsWith(type, 'Left') ? 'next' : 'previous');
                }
            }

        ],

        update: function() {
            var this$1 = this;


            this.connects.forEach(function (list) { return this$1.updateAria(list.children); });
            var ref = this.$el;
            var children = ref.children;
            this.show(filter(children, ("." + (this.cls)))[0] || children[this.active] || children[0]);

        },

        methods: {

            index: function() {
                return !isEmpty(this.connects) && index(filter(this.connects[0].children, ("." + (this.cls)))[0]);
            },

            show: function(item) {
                var this$1 = this;


                var ref = this.$el;
                var children = ref.children;
                var length = children.length;
                var prev = this.index();
                var hasPrev = prev >= 0;
                var dir = item === 'previous' ? -1 : 1;

                var toggle, active, next = getIndex(item, children, prev);

                for (var i = 0; i < length; i++, next = (next + dir + length) % length) {
                    if (!matches(this.toggles[next], '.gsl-disabled *, .gsl-disabled, [disabled]')) {
                        toggle = this.toggles[next];
                        active = children[next];
                        break;
                    }
                }

                if (!active || prev >= 0 && hasClass(active, this.cls) || prev === next) {
                    return;
                }

                removeClass(children, this.cls);
                addClass(active, this.cls);
                attr(this.toggles, 'aria-expanded', false);
                attr(toggle, 'aria-expanded', true);

                this.connects.forEach(function (list) {
                    if (!hasPrev) {
                        this$1.toggleNow(list.children[next]);
                    } else {
                        this$1.toggleElement([list.children[prev], list.children[next]]);
                    }
                });

            }

        }

    };

    var Tab = {

        mixins: [Class],

        extends: Switcher,

        props: {
            media: Boolean
        },

        data: {
            media: 960,
            attrItem: 'gsl-tab-item'
        },

        connected: function() {

            var cls = hasClass(this.$el, 'gsl-tab-left')
                ? 'gsl-tab-left'
                : hasClass(this.$el, 'gsl-tab-right')
                    ? 'gsl-tab-right'
                    : false;

            if (cls) {
                this.$create('toggle', this.$el, {cls: cls, mode: 'media', media: this.media});
            }
        }

    };

    var Toggle = {

        mixins: [Media, Togglable],

        args: 'target',

        props: {
            href: String,
            target: null,
            mode: 'list'
        },

        data: {
            href: false,
            target: false,
            mode: 'click',
            queued: true
        },

        computed: {

            target: function(ref, $el) {
                var href = ref.href;
                var target = ref.target;

                target = queryAll(target || href, $el);
                return target.length && target || [$el];
            }

        },

        connected: function() {
            trigger(this.target, 'updatearia', [this]);
        },

        events: [

            {

                name: (pointerEnter + " " + pointerLeave),

                filter: function() {
                    return includes(this.mode, 'hover');
                },

                handler: function(e) {
                    if (!isTouch(e)) {
                        this.toggle(("toggle" + (e.type === pointerEnter ? 'show' : 'hide')));
                    }
                }

            },

            {

                name: 'click',

                filter: function() {
                    return includes(this.mode, 'click') || hasTouch && includes(this.mode, 'hover');
                },

                handler: function(e) {

                    // TODO better isToggled handling
                    var link;
                    if (closest(e.target, 'a[href="#"], a[href=""]')
                        || (link = closest(e.target, 'a[href]')) && (
                            this.cls
                            || !isVisible(this.target)
                            || link.hash && matches(this.target, link.hash)
                        )
                    ) {
                        e.preventDefault();
                    }

                    this.toggle();
                }

            }

        ],

        update: {

            read: function() {
                return includes(this.mode, 'media') && this.media
                    ? {match: this.matchMedia}
                    : false;
            },

            write: function(ref) {
                var match = ref.match;


                var toggled = this.isToggled(this.target);
                if (match ? !toggled : toggled) {
                    this.toggle();
                }

            },

            events: ['resize']

        },

        methods: {

            toggle: function(type) {
                if (trigger(this.target, type || 'toggle', [this])) {
                    this.toggleElement(this.target);
                }
            }

        }

    };

    function core (gslUIkit) {

        // core components
        gslUIkit.component('accordion', Accordion);
        gslUIkit.component('alert', Alert);
        gslUIkit.component('cover', Cover);
        gslUIkit.component('drop', Drop);
        gslUIkit.component('dropdown', Dropdown);
        gslUIkit.component('formCustom', FormCustom);
        gslUIkit.component('gif', Gif);
        gslUIkit.component('grid', Grid);
        gslUIkit.component('heightMatch', HeightMatch);
        gslUIkit.component('heightViewport', HeightViewport);
        gslUIkit.component('icon', Icon);
        gslUIkit.component('img', Img);
        gslUIkit.component('leader', Leader);
        gslUIkit.component('margin', Margin);
        gslUIkit.component('modal', Modal$1);
        gslUIkit.component('nav', Nav);
        gslUIkit.component('navbar', Navbar);
        gslUIkit.component('offcanvas', Offcanvas);
        gslUIkit.component('overflowAuto', OverflowAuto);
        gslUIkit.component('responsive', Responsive);
        gslUIkit.component('scroll', Scroll);
        gslUIkit.component('scrollspy', Scrollspy);
        gslUIkit.component('scrollspyNav', ScrollspyNav);
        gslUIkit.component('sticky', Sticky);
        gslUIkit.component('svg', Svg);
        gslUIkit.component('switcher', Switcher);
        gslUIkit.component('tab', Tab);
        gslUIkit.component('toggle', Toggle);
        gslUIkit.component('video', Video);

        // Icon components
        gslUIkit.component('close', Close);
        gslUIkit.component('marker', IconComponent);
        gslUIkit.component('navbarToggleIcon', IconComponent);
        gslUIkit.component('overlayIcon', IconComponent);
        gslUIkit.component('paginationNext', IconComponent);
        gslUIkit.component('paginationPrevious', IconComponent);
        gslUIkit.component('searchIcon', Search);
        gslUIkit.component('slidenavNext', Slidenav);
        gslUIkit.component('slidenavPrevious', Slidenav);
        gslUIkit.component('spinner', Spinner);
        gslUIkit.component('totop', IconComponent);

        // core functionality
        gslUIkit.use(Core);

    }

    function boot (gslUIkit) {

        var connect = gslUIkit.connect;
        var disconnect = gslUIkit.disconnect;

        if (!('MutationObserver' in window)) {
            return;
        }

        if (document.body) {

            init();

        } else {

            (new MutationObserver(function () {

                if (document.body) {
                    this.disconnect();
                    init();
                }

            })).observe(document, {childList: true, subtree: true});

        }

        function init() {

            apply(document.body, connect);

            fastdom.flush();

            (new MutationObserver(function (mutations) { return mutations.forEach(applyMutation); })).observe(document, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true
            });

            gslUIkit._initialized = true;
        }

        function applyMutation(mutation) {

            var target = mutation.target;
            var type = mutation.type;

            var update = type !== 'attributes'
                ? applyChildList(mutation)
                : applyAttribute(mutation);

            update && gslUIkit.update(target);

        }

        function applyAttribute(ref) {
            var target = ref.target;
            var attributeName = ref.attributeName;


            if (attributeName === 'href') {
                return true;
            }

            var name = getComponentName(attributeName);

            if (!name || !(name in gslUIkit)) {
                return;
            }

            if (hasAttr(target, attributeName)) {
                gslUIkit[name](target);
                return true;
            }

            var component = gslUIkit.getComponent(target, name);

            if (component) {
                component.$destroy();
                return true;
            }

        }

        function applyChildList(ref) {
            var addedNodes = ref.addedNodes;
            var removedNodes = ref.removedNodes;


            for (var i = 0; i < addedNodes.length; i++) {
                apply(addedNodes[i], connect);
            }

            for (var i$1 = 0; i$1 < removedNodes.length; i$1++) {
                apply(removedNodes[i$1], disconnect);
            }

            return true;
        }

        function apply(node, fn) {

            if (node.nodeType !== 1 || hasAttr(node, 'gsl-no-boot')) {
                return;
            }

            fn(node);
            node = node.firstElementChild;
            while (node) {
                var next = node.nextElementSibling;
                apply(node, fn);
                node = next;
            }
        }

    }

    gslUIkit.version = '3.1.5';

    core(gslUIkit);

    {
        boot(gslUIkit);
    }

    return gslUIkit;

}));

//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidWlraXQtY29yZS5qcyIsInNvdXJjZXMiOlsic3JjL2pzL3V0aWwvbGFuZy5qcyIsInNyYy9qcy91dGlsL2F0dHIuanMiLCJzcmMvanMvdXRpbC9zZWxlY3Rvci5qcyIsInNyYy9qcy91dGlsL2ZpbHRlci5qcyIsInNyYy9qcy91dGlsL2V2ZW50LmpzIiwic3JjL2pzL3V0aWwvcHJvbWlzZS5qcyIsInNyYy9qcy91dGlsL2FqYXguanMiLCJzcmMvanMvdXRpbC9lbnYuanMiLCJzcmMvanMvdXRpbC9kb20uanMiLCJzcmMvanMvdXRpbC9jbGFzcy5qcyIsInNyYy9qcy91dGlsL3N0eWxlLmpzIiwic3JjL2pzL3V0aWwvYW5pbWF0aW9uLmpzIiwic3JjL2pzL3V0aWwvZGltZW5zaW9ucy5qcyIsInNyYy9qcy91dGlsL2Zhc3Rkb20uanMiLCJzcmMvanMvdXRpbC9tb3VzZS5qcyIsInNyYy9qcy91dGlsL29wdGlvbnMuanMiLCJzcmMvanMvdXRpbC9wbGF5ZXIuanMiLCJzcmMvanMvdXRpbC9pbnRlcnNlY3Rpb24uanMiLCJzcmMvanMvYXBpL2dsb2JhbC5qcyIsInNyYy9qcy9hcGkvaG9va3MuanMiLCJzcmMvanMvYXBpL3N0YXRlLmpzIiwic3JjL2pzL2FwaS9pbnN0YW5jZS5qcyIsInNyYy9qcy9hcGkvY29tcG9uZW50LmpzIiwic3JjL2pzL2FwaS9pbmRleC5qcyIsInNyYy9qcy9taXhpbi9jbGFzcy5qcyIsInNyYy9qcy9taXhpbi90b2dnbGFibGUuanMiLCJzcmMvanMvY29yZS9hY2NvcmRpb24uanMiLCJzcmMvanMvY29yZS9hbGVydC5qcyIsInNyYy9qcy9jb3JlL2NvcmUuanMiLCJzcmMvanMvY29yZS92aWRlby5qcyIsInNyYy9qcy9jb3JlL2NvdmVyLmpzIiwic3JjL2pzL21peGluL3Bvc2l0aW9uLmpzIiwic3JjL2pzL2NvcmUvZHJvcC5qcyIsInNyYy9qcy9jb3JlL2Ryb3Bkb3duLmpzIiwic3JjL2pzL2NvcmUvZm9ybS1jdXN0b20uanMiLCJzcmMvanMvY29yZS9naWYuanMiLCJzcmMvanMvY29yZS9tYXJnaW4uanMiLCJzcmMvanMvY29yZS9ncmlkLmpzIiwic3JjL2pzL21peGluL2ZsZXgtYnVnLmpzIiwic3JjL2pzL2NvcmUvaGVpZ2h0LW1hdGNoLmpzIiwic3JjL2pzL2NvcmUvaGVpZ2h0LXZpZXdwb3J0LmpzIiwic3JjL2pzL2NvcmUvc3ZnLmpzIiwic3JjL2pzL2NvcmUvaWNvbi5qcyIsInNyYy9qcy9jb3JlL2ltZy5qcyIsInNyYy9qcy9taXhpbi9tZWRpYS5qcyIsInNyYy9qcy9jb3JlL2xlYWRlci5qcyIsInNyYy9qcy9taXhpbi9jb250YWluZXIuanMiLCJzcmMvanMvbWl4aW4vbW9kYWwuanMiLCJzcmMvanMvY29yZS9tb2RhbC5qcyIsInNyYy9qcy9jb3JlL25hdi5qcyIsInNyYy9qcy9jb3JlL25hdmJhci5qcyIsInNyYy9qcy9jb3JlL29mZmNhbnZhcy5qcyIsInNyYy9qcy9jb3JlL292ZXJmbG93LWF1dG8uanMiLCJzcmMvanMvY29yZS9yZXNwb25zaXZlLmpzIiwic3JjL2pzL2NvcmUvc2Nyb2xsLmpzIiwic3JjL2pzL2NvcmUvc2Nyb2xsc3B5LmpzIiwic3JjL2pzL2NvcmUvc2Nyb2xsc3B5LW5hdi5qcyIsInNyYy9qcy9jb3JlL3N0aWNreS5qcyIsInNyYy9qcy9jb3JlL3N3aXRjaGVyLmpzIiwic3JjL2pzL2NvcmUvdGFiLmpzIiwic3JjL2pzL2NvcmUvdG9nZ2xlLmpzIiwic3JjL2pzL2NvcmUvaW5kZXguanMiLCJzcmMvanMvYXBpL2Jvb3QuanMiLCJzcmMvanMvdWlraXQtY29yZS5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQgZnVuY3Rpb24gYmluZChmbiwgY29udGV4dCkge1xuICAgIHJldHVybiBmdW5jdGlvbiAoYSkge1xuICAgICAgICBjb25zdCBsID0gYXJndW1lbnRzLmxlbmd0aDtcbiAgICAgICAgcmV0dXJuIGwgPyBsID4gMSA/IGZuLmFwcGx5KGNvbnRleHQsIGFyZ3VtZW50cykgOiBmbi5jYWxsKGNvbnRleHQsIGEpIDogZm4uY2FsbChjb250ZXh0KTtcbiAgICB9O1xufVxuXG5jb25zdCBvYmpQcm90b3R5cGUgPSBPYmplY3QucHJvdG90eXBlO1xuY29uc3Qge2hhc093blByb3BlcnR5fSA9IG9ialByb3RvdHlwZTtcblxuZXhwb3J0IGZ1bmN0aW9uIGhhc093bihvYmosIGtleSkge1xuICAgIHJldHVybiBoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwga2V5KTtcbn1cblxuY29uc3QgaHlwaGVuYXRlQ2FjaGUgPSB7fTtcbmNvbnN0IGh5cGhlbmF0ZVJlID0gLyhbYS16XFxkXSkoW0EtWl0pL2c7XG5cbmV4cG9ydCBmdW5jdGlvbiBoeXBoZW5hdGUoc3RyKSB7XG5cbiAgICBpZiAoIShzdHIgaW4gaHlwaGVuYXRlQ2FjaGUpKSB7XG4gICAgICAgIGh5cGhlbmF0ZUNhY2hlW3N0cl0gPSBzdHJcbiAgICAgICAgICAgIC5yZXBsYWNlKGh5cGhlbmF0ZVJlLCAnJDEtJDInKVxuICAgICAgICAgICAgLnRvTG93ZXJDYXNlKCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGh5cGhlbmF0ZUNhY2hlW3N0cl07XG59XG5cbmNvbnN0IGNhbWVsaXplUmUgPSAvLShcXHcpL2c7XG5cbmV4cG9ydCBmdW5jdGlvbiBjYW1lbGl6ZShzdHIpIHtcbiAgICByZXR1cm4gc3RyLnJlcGxhY2UoY2FtZWxpemVSZSwgdG9VcHBlcik7XG59XG5cbmZ1bmN0aW9uIHRvVXBwZXIoXywgYykge1xuICAgIHJldHVybiBjID8gYy50b1VwcGVyQ2FzZSgpIDogJyc7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB1Y2ZpcnN0KHN0cikge1xuICAgIHJldHVybiBzdHIubGVuZ3RoID8gdG9VcHBlcihudWxsLCBzdHIuY2hhckF0KDApKSArIHN0ci5zbGljZSgxKSA6ICcnO1xufVxuXG5jb25zdCBzdHJQcm90b3R5cGUgPSBTdHJpbmcucHJvdG90eXBlO1xuY29uc3Qgc3RhcnRzV2l0aEZuID0gc3RyUHJvdG90eXBlLnN0YXJ0c1dpdGggfHwgZnVuY3Rpb24gKHNlYXJjaCkgeyByZXR1cm4gdGhpcy5sYXN0SW5kZXhPZihzZWFyY2gsIDApID09PSAwOyB9O1xuXG5leHBvcnQgZnVuY3Rpb24gc3RhcnRzV2l0aChzdHIsIHNlYXJjaCkge1xuICAgIHJldHVybiBzdGFydHNXaXRoRm4uY2FsbChzdHIsIHNlYXJjaCk7XG59XG5cbmNvbnN0IGVuZHNXaXRoRm4gPSBzdHJQcm90b3R5cGUuZW5kc1dpdGggfHwgZnVuY3Rpb24gKHNlYXJjaCkgeyByZXR1cm4gdGhpcy5zdWJzdHIoLXNlYXJjaC5sZW5ndGgpID09PSBzZWFyY2g7IH07XG5cbmV4cG9ydCBmdW5jdGlvbiBlbmRzV2l0aChzdHIsIHNlYXJjaCkge1xuICAgIHJldHVybiBlbmRzV2l0aEZuLmNhbGwoc3RyLCBzZWFyY2gpO1xufVxuXG5jb25zdCBhcnJQcm90b3R5cGUgPSBBcnJheS5wcm90b3R5cGU7XG5cbmNvbnN0IGluY2x1ZGVzRm4gPSBmdW5jdGlvbiAoc2VhcmNoLCBpKSB7IHJldHVybiB+dGhpcy5pbmRleE9mKHNlYXJjaCwgaSk7IH07XG5jb25zdCBpbmNsdWRlc1N0ciA9IHN0clByb3RvdHlwZS5pbmNsdWRlcyB8fCBpbmNsdWRlc0ZuO1xuY29uc3QgaW5jbHVkZXNBcnJheSA9IGFyclByb3RvdHlwZS5pbmNsdWRlcyB8fCBpbmNsdWRlc0ZuO1xuXG5leHBvcnQgZnVuY3Rpb24gaW5jbHVkZXMob2JqLCBzZWFyY2gpIHtcbiAgICByZXR1cm4gb2JqICYmIChpc1N0cmluZyhvYmopID8gaW5jbHVkZXNTdHIgOiBpbmNsdWRlc0FycmF5KS5jYWxsKG9iaiwgc2VhcmNoKTtcbn1cblxuY29uc3QgZmluZEluZGV4Rm4gPSBhcnJQcm90b3R5cGUuZmluZEluZGV4IHx8IGZ1bmN0aW9uIChwcmVkaWNhdGUpIHtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IHRoaXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgaWYgKHByZWRpY2F0ZS5jYWxsKGFyZ3VtZW50c1sxXSwgdGhpc1tpXSwgaSwgdGhpcykpIHtcbiAgICAgICAgICAgIHJldHVybiBpO1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiAtMTtcbn07XG5cbmV4cG9ydCBmdW5jdGlvbiBmaW5kSW5kZXgoYXJyYXksIHByZWRpY2F0ZSkge1xuICAgIHJldHVybiBmaW5kSW5kZXhGbi5jYWxsKGFycmF5LCBwcmVkaWNhdGUpO1xufVxuXG5leHBvcnQgY29uc3Qge2lzQXJyYXl9ID0gQXJyYXk7XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0Z1bmN0aW9uKG9iaikge1xuICAgIHJldHVybiB0eXBlb2Ygb2JqID09PSAnZnVuY3Rpb24nO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNPYmplY3Qob2JqKSB7XG4gICAgcmV0dXJuIG9iaiAhPT0gbnVsbCAmJiB0eXBlb2Ygb2JqID09PSAnb2JqZWN0Jztcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzUGxhaW5PYmplY3Qob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgT2JqZWN0LmdldFByb3RvdHlwZU9mKG9iaikgPT09IG9ialByb3RvdHlwZTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzV2luZG93KG9iaikge1xuICAgIHJldHVybiBpc09iamVjdChvYmopICYmIG9iaiA9PT0gb2JqLndpbmRvdztcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzRG9jdW1lbnQob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgb2JqLm5vZGVUeXBlID09PSA5O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNKUXVlcnkob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgISFvYmouanF1ZXJ5O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNOb2RlKG9iaikge1xuICAgIHJldHVybiBvYmogaW5zdGFuY2VvZiBOb2RlIHx8IGlzT2JqZWN0KG9iaikgJiYgb2JqLm5vZGVUeXBlID49IDE7XG59XG5cbmNvbnN0IHt0b1N0cmluZ30gPSBvYmpQcm90b3R5cGU7XG5leHBvcnQgZnVuY3Rpb24gaXNOb2RlQ29sbGVjdGlvbihvYmopIHtcbiAgICByZXR1cm4gdG9TdHJpbmcuY2FsbChvYmopLm1hdGNoKC9eXFxbb2JqZWN0IChOb2RlTGlzdHxIVE1MQ29sbGVjdGlvbilcXF0kLyk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0Jvb2xlYW4odmFsdWUpIHtcbiAgICByZXR1cm4gdHlwZW9mIHZhbHVlID09PSAnYm9vbGVhbic7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc1N0cmluZyh2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdzdHJpbmcnO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNOdW1iZXIodmFsdWUpIHtcbiAgICByZXR1cm4gdHlwZW9mIHZhbHVlID09PSAnbnVtYmVyJztcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzTnVtZXJpYyh2YWx1ZSkge1xuICAgIHJldHVybiBpc051bWJlcih2YWx1ZSkgfHwgaXNTdHJpbmcodmFsdWUpICYmICFpc05hTih2YWx1ZSAtIHBhcnNlRmxvYXQodmFsdWUpKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzRW1wdHkob2JqKSB7XG4gICAgcmV0dXJuICEoaXNBcnJheShvYmopXG4gICAgICAgID8gb2JqLmxlbmd0aFxuICAgICAgICA6IGlzT2JqZWN0KG9iailcbiAgICAgICAgICAgID8gT2JqZWN0LmtleXMob2JqKS5sZW5ndGhcbiAgICAgICAgICAgIDogZmFsc2VcbiAgICApO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNVbmRlZmluZWQodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUgPT09IHZvaWQgMDtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvQm9vbGVhbih2YWx1ZSkge1xuICAgIHJldHVybiBpc0Jvb2xlYW4odmFsdWUpXG4gICAgICAgID8gdmFsdWVcbiAgICAgICAgOiB2YWx1ZSA9PT0gJ3RydWUnIHx8IHZhbHVlID09PSAnMScgfHwgdmFsdWUgPT09ICcnXG4gICAgICAgICAgICA/IHRydWVcbiAgICAgICAgICAgIDogdmFsdWUgPT09ICdmYWxzZScgfHwgdmFsdWUgPT09ICcwJ1xuICAgICAgICAgICAgICAgID8gZmFsc2VcbiAgICAgICAgICAgICAgICA6IHZhbHVlO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9OdW1iZXIodmFsdWUpIHtcbiAgICBjb25zdCBudW1iZXIgPSBOdW1iZXIodmFsdWUpO1xuICAgIHJldHVybiAhaXNOYU4obnVtYmVyKSA/IG51bWJlciA6IGZhbHNlO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9GbG9hdCh2YWx1ZSkge1xuICAgIHJldHVybiBwYXJzZUZsb2F0KHZhbHVlKSB8fCAwO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9Ob2RlKGVsZW1lbnQpIHtcbiAgICByZXR1cm4gaXNOb2RlKGVsZW1lbnQpIHx8IGlzV2luZG93KGVsZW1lbnQpIHx8IGlzRG9jdW1lbnQoZWxlbWVudClcbiAgICAgICAgPyBlbGVtZW50XG4gICAgICAgIDogaXNOb2RlQ29sbGVjdGlvbihlbGVtZW50KSB8fCBpc0pRdWVyeShlbGVtZW50KVxuICAgICAgICAgICAgPyBlbGVtZW50WzBdXG4gICAgICAgICAgICA6IGlzQXJyYXkoZWxlbWVudClcbiAgICAgICAgICAgICAgICA/IHRvTm9kZShlbGVtZW50WzBdKVxuICAgICAgICAgICAgICAgIDogbnVsbDtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvTm9kZXMoZWxlbWVudCkge1xuICAgIHJldHVybiBpc05vZGUoZWxlbWVudClcbiAgICAgICAgPyBbZWxlbWVudF1cbiAgICAgICAgOiBpc05vZGVDb2xsZWN0aW9uKGVsZW1lbnQpXG4gICAgICAgICAgICA/IGFyclByb3RvdHlwZS5zbGljZS5jYWxsKGVsZW1lbnQpXG4gICAgICAgICAgICA6IGlzQXJyYXkoZWxlbWVudClcbiAgICAgICAgICAgICAgICA/IGVsZW1lbnQubWFwKHRvTm9kZSkuZmlsdGVyKEJvb2xlYW4pXG4gICAgICAgICAgICAgICAgOiBpc0pRdWVyeShlbGVtZW50KVxuICAgICAgICAgICAgICAgICAgICA/IGVsZW1lbnQudG9BcnJheSgpXG4gICAgICAgICAgICAgICAgICAgIDogW107XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB0b0xpc3QodmFsdWUpIHtcbiAgICByZXR1cm4gaXNBcnJheSh2YWx1ZSlcbiAgICAgICAgPyB2YWx1ZVxuICAgICAgICA6IGlzU3RyaW5nKHZhbHVlKVxuICAgICAgICAgICAgPyB2YWx1ZS5zcGxpdCgvLCg/IVteKF0qXFwpKS8pLm1hcCh2YWx1ZSA9PiBpc051bWVyaWModmFsdWUpXG4gICAgICAgICAgICAgICAgPyB0b051bWJlcih2YWx1ZSlcbiAgICAgICAgICAgICAgICA6IHRvQm9vbGVhbih2YWx1ZS50cmltKCkpKVxuICAgICAgICAgICAgOiBbdmFsdWVdO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9Ncyh0aW1lKSB7XG4gICAgcmV0dXJuICF0aW1lXG4gICAgICAgID8gMFxuICAgICAgICA6IGVuZHNXaXRoKHRpbWUsICdtcycpXG4gICAgICAgICAgICA/IHRvRmxvYXQodGltZSlcbiAgICAgICAgICAgIDogdG9GbG9hdCh0aW1lKSAqIDEwMDA7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0VxdWFsKHZhbHVlLCBvdGhlcikge1xuICAgIHJldHVybiB2YWx1ZSA9PT0gb3RoZXJcbiAgICAgICAgfHwgaXNPYmplY3QodmFsdWUpXG4gICAgICAgICYmIGlzT2JqZWN0KG90aGVyKVxuICAgICAgICAmJiBPYmplY3Qua2V5cyh2YWx1ZSkubGVuZ3RoID09PSBPYmplY3Qua2V5cyhvdGhlcikubGVuZ3RoXG4gICAgICAgICYmIGVhY2godmFsdWUsICh2YWwsIGtleSkgPT4gdmFsID09PSBvdGhlcltrZXldKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHN3YXAodmFsdWUsIGEsIGIpIHtcbiAgICByZXR1cm4gdmFsdWUucmVwbGFjZShuZXcgUmVnRXhwKGAke2F9fCR7Yn1gLCAnbWcnKSwgbWF0Y2ggPT4ge1xuICAgICAgICByZXR1cm4gbWF0Y2ggPT09IGEgPyBiIDogYTtcbiAgICB9KTtcbn1cblxuZXhwb3J0IGNvbnN0IGFzc2lnbiA9IE9iamVjdC5hc3NpZ24gfHwgZnVuY3Rpb24gKHRhcmdldCwgLi4uYXJncykge1xuICAgIHRhcmdldCA9IE9iamVjdCh0YXJnZXQpO1xuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgYXJncy5sZW5ndGg7IGkrKykge1xuICAgICAgICBjb25zdCBzb3VyY2UgPSBhcmdzW2ldO1xuICAgICAgICBpZiAoc291cmNlICE9PSBudWxsKSB7XG4gICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBzb3VyY2UpIHtcbiAgICAgICAgICAgICAgICBpZiAoaGFzT3duKHNvdXJjZSwga2V5KSkge1xuICAgICAgICAgICAgICAgICAgICB0YXJnZXRba2V5XSA9IHNvdXJjZVtrZXldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gdGFyZ2V0O1xufTtcblxuZXhwb3J0IGZ1bmN0aW9uIGVhY2gob2JqLCBjYikge1xuICAgIGZvciAoY29uc3Qga2V5IGluIG9iaikge1xuICAgICAgICBpZiAoZmFsc2UgPT09IGNiKG9ialtrZXldLCBrZXkpKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHRydWU7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBzb3J0QnkoYXJyYXksIHByb3ApIHtcbiAgICByZXR1cm4gYXJyYXkuc29ydCgoe1twcm9wXTogcHJvcEEgPSAwfSwge1twcm9wXTogcHJvcEIgPSAwfSkgPT5cbiAgICAgICAgcHJvcEEgPiBwcm9wQlxuICAgICAgICAgICAgPyAxXG4gICAgICAgICAgICA6IHByb3BCID4gcHJvcEFcbiAgICAgICAgICAgICAgICA/IC0xXG4gICAgICAgICAgICAgICAgOiAwXG4gICAgKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHVuaXF1ZUJ5KGFycmF5LCBwcm9wKSB7XG4gICAgY29uc3Qgc2VlbiA9IG5ldyBTZXQoKTtcbiAgICByZXR1cm4gYXJyYXkuZmlsdGVyKCh7W3Byb3BdOiBjaGVja30pID0+IHNlZW4uaGFzKGNoZWNrKVxuICAgICAgICA/IGZhbHNlXG4gICAgICAgIDogc2Vlbi5hZGQoY2hlY2spIHx8IHRydWUgLy8gSUUgMTEgZG9lcyBub3QgcmV0dXJuIHRoZSBTZXQgb2JqZWN0XG4gICAgKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGNsYW1wKG51bWJlciwgbWluID0gMCwgbWF4ID0gMSkge1xuICAgIHJldHVybiBNYXRoLm1pbihNYXRoLm1heCh0b051bWJlcihudW1iZXIpIHx8IDAsIG1pbiksIG1heCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBub29wKCkge31cblxuZXhwb3J0IGZ1bmN0aW9uIGludGVyc2VjdFJlY3QocjEsIHIyKSB7XG4gICAgcmV0dXJuIHIxLmxlZnQgPCByMi5yaWdodCAmJlxuICAgICAgICByMS5yaWdodCA+IHIyLmxlZnQgJiZcbiAgICAgICAgcjEudG9wIDwgcjIuYm90dG9tICYmXG4gICAgICAgIHIxLmJvdHRvbSA+IHIyLnRvcDtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHBvaW50SW5SZWN0KHBvaW50LCByZWN0KSB7XG4gICAgcmV0dXJuIHBvaW50LnggPD0gcmVjdC5yaWdodCAmJlxuICAgICAgICBwb2ludC54ID49IHJlY3QubGVmdCAmJlxuICAgICAgICBwb2ludC55IDw9IHJlY3QuYm90dG9tICYmXG4gICAgICAgIHBvaW50LnkgPj0gcmVjdC50b3A7XG59XG5cbmV4cG9ydCBjb25zdCBEaW1lbnNpb25zID0ge1xuXG4gICAgcmF0aW8oZGltZW5zaW9ucywgcHJvcCwgdmFsdWUpIHtcblxuICAgICAgICBjb25zdCBhUHJvcCA9IHByb3AgPT09ICd3aWR0aCcgPyAnaGVpZ2h0JyA6ICd3aWR0aCc7XG5cbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIFthUHJvcF06IGRpbWVuc2lvbnNbcHJvcF0gPyBNYXRoLnJvdW5kKHZhbHVlICogZGltZW5zaW9uc1thUHJvcF0gLyBkaW1lbnNpb25zW3Byb3BdKSA6IGRpbWVuc2lvbnNbYVByb3BdLFxuICAgICAgICAgICAgW3Byb3BdOiB2YWx1ZVxuICAgICAgICB9O1xuICAgIH0sXG5cbiAgICBjb250YWluKGRpbWVuc2lvbnMsIG1heERpbWVuc2lvbnMpIHtcbiAgICAgICAgZGltZW5zaW9ucyA9IGFzc2lnbih7fSwgZGltZW5zaW9ucyk7XG5cbiAgICAgICAgZWFjaChkaW1lbnNpb25zLCAoXywgcHJvcCkgPT4gZGltZW5zaW9ucyA9IGRpbWVuc2lvbnNbcHJvcF0gPiBtYXhEaW1lbnNpb25zW3Byb3BdXG4gICAgICAgICAgICA/IHRoaXMucmF0aW8oZGltZW5zaW9ucywgcHJvcCwgbWF4RGltZW5zaW9uc1twcm9wXSlcbiAgICAgICAgICAgIDogZGltZW5zaW9uc1xuICAgICAgICApO1xuXG4gICAgICAgIHJldHVybiBkaW1lbnNpb25zO1xuICAgIH0sXG5cbiAgICBjb3ZlcihkaW1lbnNpb25zLCBtYXhEaW1lbnNpb25zKSB7XG4gICAgICAgIGRpbWVuc2lvbnMgPSB0aGlzLmNvbnRhaW4oZGltZW5zaW9ucywgbWF4RGltZW5zaW9ucyk7XG5cbiAgICAgICAgZWFjaChkaW1lbnNpb25zLCAoXywgcHJvcCkgPT4gZGltZW5zaW9ucyA9IGRpbWVuc2lvbnNbcHJvcF0gPCBtYXhEaW1lbnNpb25zW3Byb3BdXG4gICAgICAgICAgICA/IHRoaXMucmF0aW8oZGltZW5zaW9ucywgcHJvcCwgbWF4RGltZW5zaW9uc1twcm9wXSlcbiAgICAgICAgICAgIDogZGltZW5zaW9uc1xuICAgICAgICApO1xuXG4gICAgICAgIHJldHVybiBkaW1lbnNpb25zO1xuICAgIH1cblxufTtcbiIsImltcG9ydCB7aXNGdW5jdGlvbiwgaXNPYmplY3QsIGlzVW5kZWZpbmVkLCB0b05vZGUsIHRvTm9kZXN9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBmdW5jdGlvbiBhdHRyKGVsZW1lbnQsIG5hbWUsIHZhbHVlKSB7XG5cbiAgICBpZiAoaXNPYmplY3QobmFtZSkpIHtcbiAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gbmFtZSkge1xuICAgICAgICAgICAgYXR0cihlbGVtZW50LCBrZXksIG5hbWVba2V5XSk7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmIChpc1VuZGVmaW5lZCh2YWx1ZSkpIHtcbiAgICAgICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcbiAgICAgICAgcmV0dXJuIGVsZW1lbnQgJiYgZWxlbWVudC5nZXRBdHRyaWJ1dGUobmFtZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgdG9Ob2RlcyhlbGVtZW50KS5mb3JFYWNoKGVsZW1lbnQgPT4ge1xuXG4gICAgICAgICAgICBpZiAoaXNGdW5jdGlvbih2YWx1ZSkpIHtcbiAgICAgICAgICAgICAgICB2YWx1ZSA9IHZhbHVlLmNhbGwoZWxlbWVudCwgYXR0cihlbGVtZW50LCBuYW1lKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICh2YWx1ZSA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIHJlbW92ZUF0dHIoZWxlbWVudCwgbmFtZSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuc2V0QXR0cmlidXRlKG5hbWUsIHZhbHVlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBoYXNBdHRyKGVsZW1lbnQsIG5hbWUpIHtcbiAgICByZXR1cm4gdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gZWxlbWVudC5oYXNBdHRyaWJ1dGUobmFtZSkpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcmVtb3ZlQXR0cihlbGVtZW50LCBuYW1lKSB7XG4gICAgZWxlbWVudCA9IHRvTm9kZXMoZWxlbWVudCk7XG4gICAgbmFtZS5zcGxpdCgnICcpLmZvckVhY2gobmFtZSA9PlxuICAgICAgICBlbGVtZW50LmZvckVhY2goZWxlbWVudCA9PlxuICAgICAgICAgICAgZWxlbWVudC5oYXNBdHRyaWJ1dGUobmFtZSkgJiYgZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUobmFtZSlcbiAgICAgICAgKVxuICAgICk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBkYXRhKGVsZW1lbnQsIGF0dHJpYnV0ZSkge1xuICAgIGZvciAobGV0IGkgPSAwLCBhdHRycyA9IFthdHRyaWJ1dGUsIGBkYXRhLSR7YXR0cmlidXRlfWBdOyBpIDwgYXR0cnMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgaWYgKGhhc0F0dHIoZWxlbWVudCwgYXR0cnNbaV0pKSB7XG4gICAgICAgICAgICByZXR1cm4gYXR0cihlbGVtZW50LCBhdHRyc1tpXSk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCJpbXBvcnQge3JlbW92ZUF0dHJ9IGZyb20gJy4vYXR0cic7XG5pbXBvcnQge2lzRG9jdW1lbnQsIGlzTm9kZSwgaXNTdHJpbmcsIHN0YXJ0c1dpdGgsIHRvTm9kZSwgdG9Ob2Rlc30gZnJvbSAnLi9sYW5nJztcblxuZXhwb3J0IGZ1bmN0aW9uIHF1ZXJ5KHNlbGVjdG9yLCBjb250ZXh0KSB7XG4gICAgcmV0dXJuIHRvTm9kZShzZWxlY3RvcikgfHwgZmluZChzZWxlY3RvciwgZ2V0Q29udGV4dChzZWxlY3RvciwgY29udGV4dCkpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcXVlcnlBbGwoc2VsZWN0b3IsIGNvbnRleHQpIHtcbiAgICBjb25zdCBub2RlcyA9IHRvTm9kZXMoc2VsZWN0b3IpO1xuICAgIHJldHVybiBub2Rlcy5sZW5ndGggJiYgbm9kZXMgfHwgZmluZEFsbChzZWxlY3RvciwgZ2V0Q29udGV4dChzZWxlY3RvciwgY29udGV4dCkpO1xufVxuXG5mdW5jdGlvbiBnZXRDb250ZXh0KHNlbGVjdG9yLCBjb250ZXh0ID0gZG9jdW1lbnQpIHtcbiAgICByZXR1cm4gaXNDb250ZXh0U2VsZWN0b3Ioc2VsZWN0b3IpIHx8IGlzRG9jdW1lbnQoY29udGV4dClcbiAgICAgICAgPyBjb250ZXh0XG4gICAgICAgIDogY29udGV4dC5vd25lckRvY3VtZW50O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZmluZChzZWxlY3RvciwgY29udGV4dCkge1xuICAgIHJldHVybiB0b05vZGUoX3F1ZXJ5KHNlbGVjdG9yLCBjb250ZXh0LCAncXVlcnlTZWxlY3RvcicpKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGZpbmRBbGwoc2VsZWN0b3IsIGNvbnRleHQpIHtcbiAgICByZXR1cm4gdG9Ob2RlcyhfcXVlcnkoc2VsZWN0b3IsIGNvbnRleHQsICdxdWVyeVNlbGVjdG9yQWxsJykpO1xufVxuXG5mdW5jdGlvbiBfcXVlcnkoc2VsZWN0b3IsIGNvbnRleHQgPSBkb2N1bWVudCwgcXVlcnlGbikge1xuXG4gICAgaWYgKCFzZWxlY3RvciB8fCAhaXNTdHJpbmcoc2VsZWN0b3IpKSB7XG4gICAgICAgIHJldHVybiBudWxsO1xuICAgIH1cblxuICAgIHNlbGVjdG9yID0gc2VsZWN0b3IucmVwbGFjZShjb250ZXh0U2FuaXRpemVSZSwgJyQxIConKTtcblxuICAgIGxldCByZW1vdmVzO1xuXG4gICAgaWYgKGlzQ29udGV4dFNlbGVjdG9yKHNlbGVjdG9yKSkge1xuXG4gICAgICAgIHJlbW92ZXMgPSBbXTtcblxuICAgICAgICBzZWxlY3RvciA9IHNwbGl0U2VsZWN0b3Ioc2VsZWN0b3IpLm1hcCgoc2VsZWN0b3IsIGkpID0+IHtcblxuICAgICAgICAgICAgbGV0IGN0eCA9IGNvbnRleHQ7XG5cbiAgICAgICAgICAgIGlmIChzZWxlY3RvclswXSA9PT0gJyEnKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBzZWxlY3RvcnMgPSBzZWxlY3Rvci5zdWJzdHIoMSkudHJpbSgpLnNwbGl0KCcgJyk7XG4gICAgICAgICAgICAgICAgY3R4ID0gY2xvc2VzdChjb250ZXh0LnBhcmVudE5vZGUsIHNlbGVjdG9yc1swXSk7XG4gICAgICAgICAgICAgICAgc2VsZWN0b3IgPSBzZWxlY3RvcnMuc2xpY2UoMSkuam9pbignICcpLnRyaW0oKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoc2VsZWN0b3JbMF0gPT09ICctJykge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qgc2VsZWN0b3JzID0gc2VsZWN0b3Iuc3Vic3RyKDEpLnRyaW0oKS5zcGxpdCgnICcpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHByZXYgPSAoY3R4IHx8IGNvbnRleHQpLnByZXZpb3VzRWxlbWVudFNpYmxpbmc7XG4gICAgICAgICAgICAgICAgY3R4ID0gbWF0Y2hlcyhwcmV2LCBzZWxlY3Rvci5zdWJzdHIoMSkpID8gcHJldiA6IG51bGw7XG4gICAgICAgICAgICAgICAgc2VsZWN0b3IgPSBzZWxlY3RvcnMuc2xpY2UoMSkuam9pbignICcpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICghY3R4KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICghY3R4LmlkKSB7XG4gICAgICAgICAgICAgICAgY3R4LmlkID0gYHVrLSR7RGF0ZS5ub3coKX0ke2l9YDtcbiAgICAgICAgICAgICAgICByZW1vdmVzLnB1c2goKCkgPT4gcmVtb3ZlQXR0cihjdHgsICdpZCcpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGAjJHtlc2NhcGUoY3R4LmlkKX0gJHtzZWxlY3Rvcn1gO1xuXG4gICAgICAgIH0pLmZpbHRlcihCb29sZWFuKS5qb2luKCcsJyk7XG5cbiAgICAgICAgY29udGV4dCA9IGRvY3VtZW50O1xuXG4gICAgfVxuXG4gICAgdHJ5IHtcblxuICAgICAgICByZXR1cm4gY29udGV4dFtxdWVyeUZuXShzZWxlY3Rvcik7XG5cbiAgICB9IGNhdGNoIChlKSB7XG5cbiAgICAgICAgcmV0dXJuIG51bGw7XG5cbiAgICB9IGZpbmFsbHkge1xuXG4gICAgICAgIHJlbW92ZXMgJiYgcmVtb3Zlcy5mb3JFYWNoKHJlbW92ZSA9PiByZW1vdmUoKSk7XG5cbiAgICB9XG5cbn1cblxuY29uc3QgY29udGV4dFNlbGVjdG9yUmUgPSAvKF58W15cXFxcXSwpXFxzKlshPit+LV0vO1xuY29uc3QgY29udGV4dFNhbml0aXplUmUgPSAvKFshPit+LV0pKD89XFxzK1shPit+LV18XFxzKiQpL2c7XG5cbmZ1bmN0aW9uIGlzQ29udGV4dFNlbGVjdG9yKHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuIGlzU3RyaW5nKHNlbGVjdG9yKSAmJiBzZWxlY3Rvci5tYXRjaChjb250ZXh0U2VsZWN0b3JSZSk7XG59XG5cbmNvbnN0IHNlbGVjdG9yUmUgPSAvLio/W15cXFxcXSg/Oix8JCkvZztcblxuZnVuY3Rpb24gc3BsaXRTZWxlY3RvcihzZWxlY3Rvcikge1xuICAgIHJldHVybiBzZWxlY3Rvci5tYXRjaChzZWxlY3RvclJlKS5tYXAoc2VsZWN0b3IgPT4gc2VsZWN0b3IucmVwbGFjZSgvLCQvLCAnJykudHJpbSgpKTtcbn1cblxuY29uc3QgZWxQcm90byA9IEVsZW1lbnQucHJvdG90eXBlO1xuY29uc3QgbWF0Y2hlc0ZuID0gZWxQcm90by5tYXRjaGVzIHx8IGVsUHJvdG8ud2Via2l0TWF0Y2hlc1NlbGVjdG9yIHx8IGVsUHJvdG8ubXNNYXRjaGVzU2VsZWN0b3I7XG5cbmV4cG9ydCBmdW5jdGlvbiBtYXRjaGVzKGVsZW1lbnQsIHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuIHRvTm9kZXMoZWxlbWVudCkuc29tZShlbGVtZW50ID0+IG1hdGNoZXNGbi5jYWxsKGVsZW1lbnQsIHNlbGVjdG9yKSk7XG59XG5cbmNvbnN0IGNsb3Nlc3RGbiA9IGVsUHJvdG8uY2xvc2VzdCB8fCBmdW5jdGlvbiAoc2VsZWN0b3IpIHtcbiAgICBsZXQgYW5jZXN0b3IgPSB0aGlzO1xuXG4gICAgZG8ge1xuXG4gICAgICAgIGlmIChtYXRjaGVzKGFuY2VzdG9yLCBzZWxlY3RvcikpIHtcbiAgICAgICAgICAgIHJldHVybiBhbmNlc3RvcjtcbiAgICAgICAgfVxuXG4gICAgICAgIGFuY2VzdG9yID0gYW5jZXN0b3IucGFyZW50Tm9kZTtcblxuICAgIH0gd2hpbGUgKGFuY2VzdG9yICYmIGFuY2VzdG9yLm5vZGVUeXBlID09PSAxKTtcbn07XG5cbmV4cG9ydCBmdW5jdGlvbiBjbG9zZXN0KGVsZW1lbnQsIHNlbGVjdG9yKSB7XG5cbiAgICBpZiAoc3RhcnRzV2l0aChzZWxlY3RvciwgJz4nKSkge1xuICAgICAgICBzZWxlY3RvciA9IHNlbGVjdG9yLnNsaWNlKDEpO1xuICAgIH1cblxuICAgIHJldHVybiBpc05vZGUoZWxlbWVudClcbiAgICAgICAgPyBlbGVtZW50LnBhcmVudE5vZGUgJiYgY2xvc2VzdEZuLmNhbGwoZWxlbWVudCwgc2VsZWN0b3IpXG4gICAgICAgIDogdG9Ob2RlcyhlbGVtZW50KS5tYXAoZWxlbWVudCA9PiBjbG9zZXN0KGVsZW1lbnQsIHNlbGVjdG9yKSkuZmlsdGVyKEJvb2xlYW4pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcGFyZW50cyhlbGVtZW50LCBzZWxlY3Rvcikge1xuICAgIGNvbnN0IGVsZW1lbnRzID0gW107XG4gICAgbGV0IHBhcmVudCA9IHRvTm9kZShlbGVtZW50KS5wYXJlbnROb2RlO1xuXG4gICAgd2hpbGUgKHBhcmVudCAmJiBwYXJlbnQubm9kZVR5cGUgPT09IDEpIHtcblxuICAgICAgICBpZiAobWF0Y2hlcyhwYXJlbnQsIHNlbGVjdG9yKSkge1xuICAgICAgICAgICAgZWxlbWVudHMucHVzaChwYXJlbnQpO1xuICAgICAgICB9XG5cbiAgICAgICAgcGFyZW50ID0gcGFyZW50LnBhcmVudE5vZGU7XG4gICAgfVxuXG4gICAgcmV0dXJuIGVsZW1lbnRzO1xufVxuXG5jb25zdCBlc2NhcGVGbiA9IHdpbmRvdy5DU1MgJiYgQ1NTLmVzY2FwZSB8fCBmdW5jdGlvbiAoY3NzKSB7IHJldHVybiBjc3MucmVwbGFjZSgvKFteXFx4N2YtXFx1RkZGRlxcdy1dKS9nLCBtYXRjaCA9PiBgXFxcXCR7bWF0Y2h9YCk7IH07XG5leHBvcnQgZnVuY3Rpb24gZXNjYXBlKGNzcykge1xuICAgIHJldHVybiBpc1N0cmluZyhjc3MpID8gZXNjYXBlRm4uY2FsbChudWxsLCBjc3MpIDogJyc7XG59XG4iLCJpbXBvcnQge2Nsb3Nlc3QsIG1hdGNoZXN9IGZyb20gJy4vc2VsZWN0b3InO1xuaW1wb3J0IHtpc0RvY3VtZW50LCBpc1N0cmluZywgdG9Ob2RlLCB0b05vZGVzfSBmcm9tICcuL2xhbmcnO1xuXG5jb25zdCB2b2lkRWxlbWVudHMgPSB7XG4gICAgYXJlYTogdHJ1ZSxcbiAgICBiYXNlOiB0cnVlLFxuICAgIGJyOiB0cnVlLFxuICAgIGNvbDogdHJ1ZSxcbiAgICBlbWJlZDogdHJ1ZSxcbiAgICBocjogdHJ1ZSxcbiAgICBpbWc6IHRydWUsXG4gICAgaW5wdXQ6IHRydWUsXG4gICAga2V5Z2VuOiB0cnVlLFxuICAgIGxpbms6IHRydWUsXG4gICAgbWVudWl0ZW06IHRydWUsXG4gICAgbWV0YTogdHJ1ZSxcbiAgICBwYXJhbTogdHJ1ZSxcbiAgICBzb3VyY2U6IHRydWUsXG4gICAgdHJhY2s6IHRydWUsXG4gICAgd2JyOiB0cnVlXG59O1xuZXhwb3J0IGZ1bmN0aW9uIGlzVm9pZEVsZW1lbnQoZWxlbWVudCkge1xuICAgIHJldHVybiB0b05vZGVzKGVsZW1lbnQpLnNvbWUoZWxlbWVudCA9PiB2b2lkRWxlbWVudHNbZWxlbWVudC50YWdOYW1lLnRvTG93ZXJDYXNlKCldKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzVmlzaWJsZShlbGVtZW50KSB7XG4gICAgcmV0dXJuIHRvTm9kZXMoZWxlbWVudCkuc29tZShlbGVtZW50ID0+IGVsZW1lbnQub2Zmc2V0V2lkdGggfHwgZWxlbWVudC5vZmZzZXRIZWlnaHQgfHwgZWxlbWVudC5nZXRDbGllbnRSZWN0cygpLmxlbmd0aCk7XG59XG5cbmV4cG9ydCBjb25zdCBzZWxJbnB1dCA9ICdpbnB1dCxzZWxlY3QsdGV4dGFyZWEsYnV0dG9uJztcbmV4cG9ydCBmdW5jdGlvbiBpc0lucHV0KGVsZW1lbnQpIHtcbiAgICByZXR1cm4gdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gbWF0Y2hlcyhlbGVtZW50LCBzZWxJbnB1dCkpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZmlsdGVyKGVsZW1lbnQsIHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuIHRvTm9kZXMoZWxlbWVudCkuZmlsdGVyKGVsZW1lbnQgPT4gbWF0Y2hlcyhlbGVtZW50LCBzZWxlY3RvcikpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gd2l0aGluKGVsZW1lbnQsIHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuICFpc1N0cmluZyhzZWxlY3RvcilcbiAgICAgICAgPyBlbGVtZW50ID09PSBzZWxlY3RvciB8fCAoaXNEb2N1bWVudChzZWxlY3RvcilcbiAgICAgICAgICAgID8gc2VsZWN0b3IuZG9jdW1lbnRFbGVtZW50XG4gICAgICAgICAgICA6IHRvTm9kZShzZWxlY3RvcikpLmNvbnRhaW5zKHRvTm9kZShlbGVtZW50KSkgLy8gSUUgMTEgZG9jdW1lbnQgZG9lcyBub3QgaW1wbGVtZW50IGNvbnRhaW5zXG4gICAgICAgIDogbWF0Y2hlcyhlbGVtZW50LCBzZWxlY3RvcikgfHwgY2xvc2VzdChlbGVtZW50LCBzZWxlY3Rvcik7XG59XG4iLCJpbXBvcnQge3dpdGhpbn0gZnJvbSAnLi9maWx0ZXInO1xuaW1wb3J0IHtjbG9zZXN0LCBmaW5kQWxsfSBmcm9tICcuL3NlbGVjdG9yJztcbmltcG9ydCB7aXNBcnJheSwgaXNGdW5jdGlvbiwgaXNTdHJpbmcsIHRvTm9kZSwgdG9Ob2Rlc30gZnJvbSAnLi9sYW5nJztcblxuZXhwb3J0IGZ1bmN0aW9uIG9uKC4uLmFyZ3MpIHtcblxuICAgIGxldCBbdGFyZ2V0cywgdHlwZSwgc2VsZWN0b3IsIGxpc3RlbmVyLCB1c2VDYXB0dXJlXSA9IGdldEFyZ3MoYXJncyk7XG5cbiAgICB0YXJnZXRzID0gdG9FdmVudFRhcmdldHModGFyZ2V0cyk7XG5cbiAgICBpZiAoc2VsZWN0b3IpIHtcbiAgICAgICAgbGlzdGVuZXIgPSBkZWxlZ2F0ZSh0YXJnZXRzLCBzZWxlY3RvciwgbGlzdGVuZXIpO1xuICAgIH1cblxuICAgIGlmIChsaXN0ZW5lci5sZW5ndGggPiAxKSB7XG4gICAgICAgIGxpc3RlbmVyID0gZGV0YWlsKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICB0eXBlLnNwbGl0KCcgJykuZm9yRWFjaCh0eXBlID0+XG4gICAgICAgIHRhcmdldHMuZm9yRWFjaCh0YXJnZXQgPT5cbiAgICAgICAgICAgIHRhcmdldC5hZGRFdmVudExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyLCB1c2VDYXB0dXJlKVxuICAgICAgICApXG4gICAgKTtcbiAgICByZXR1cm4gKCkgPT4gb2ZmKHRhcmdldHMsIHR5cGUsIGxpc3RlbmVyLCB1c2VDYXB0dXJlKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG9mZih0YXJnZXRzLCB0eXBlLCBsaXN0ZW5lciwgdXNlQ2FwdHVyZSA9IGZhbHNlKSB7XG4gICAgdGFyZ2V0cyA9IHRvRXZlbnRUYXJnZXRzKHRhcmdldHMpO1xuICAgIHR5cGUuc3BsaXQoJyAnKS5mb3JFYWNoKHR5cGUgPT5cbiAgICAgICAgdGFyZ2V0cy5mb3JFYWNoKHRhcmdldCA9PlxuICAgICAgICAgICAgdGFyZ2V0LnJlbW92ZUV2ZW50TGlzdGVuZXIodHlwZSwgbGlzdGVuZXIsIHVzZUNhcHR1cmUpXG4gICAgICAgIClcbiAgICApO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gb25jZSguLi5hcmdzKSB7XG5cbiAgICBjb25zdCBbZWxlbWVudCwgdHlwZSwgc2VsZWN0b3IsIGxpc3RlbmVyLCB1c2VDYXB0dXJlLCBjb25kaXRpb25dID0gZ2V0QXJncyhhcmdzKTtcbiAgICBjb25zdCBvZmYgPSBvbihlbGVtZW50LCB0eXBlLCBzZWxlY3RvciwgZSA9PiB7XG4gICAgICAgIGNvbnN0IHJlc3VsdCA9ICFjb25kaXRpb24gfHwgY29uZGl0aW9uKGUpO1xuICAgICAgICBpZiAocmVzdWx0KSB7XG4gICAgICAgICAgICBvZmYoKTtcbiAgICAgICAgICAgIGxpc3RlbmVyKGUsIHJlc3VsdCk7XG4gICAgICAgIH1cbiAgICB9LCB1c2VDYXB0dXJlKTtcblxuICAgIHJldHVybiBvZmY7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB0cmlnZ2VyKHRhcmdldHMsIGV2ZW50LCBkZXRhaWwpIHtcbiAgICByZXR1cm4gdG9FdmVudFRhcmdldHModGFyZ2V0cykucmVkdWNlKChub3RDYW5jZWxlZCwgdGFyZ2V0KSA9PlxuICAgICAgICBub3RDYW5jZWxlZCAmJiB0YXJnZXQuZGlzcGF0Y2hFdmVudChjcmVhdGVFdmVudChldmVudCwgdHJ1ZSwgdHJ1ZSwgZGV0YWlsKSlcbiAgICAgICAgLCB0cnVlKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGNyZWF0ZUV2ZW50KGUsIGJ1YmJsZXMgPSB0cnVlLCBjYW5jZWxhYmxlID0gZmFsc2UsIGRldGFpbCkge1xuICAgIGlmIChpc1N0cmluZyhlKSkge1xuICAgICAgICBjb25zdCBldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdDdXN0b21FdmVudCcpOyAvLyBJRSAxMVxuICAgICAgICBldmVudC5pbml0Q3VzdG9tRXZlbnQoZSwgYnViYmxlcywgY2FuY2VsYWJsZSwgZGV0YWlsKTtcbiAgICAgICAgZSA9IGV2ZW50O1xuICAgIH1cblxuICAgIHJldHVybiBlO1xufVxuXG5mdW5jdGlvbiBnZXRBcmdzKGFyZ3MpIHtcbiAgICBpZiAoaXNGdW5jdGlvbihhcmdzWzJdKSkge1xuICAgICAgICBhcmdzLnNwbGljZSgyLCAwLCBmYWxzZSk7XG4gICAgfVxuICAgIHJldHVybiBhcmdzO1xufVxuXG5mdW5jdGlvbiBkZWxlZ2F0ZShkZWxlZ2F0ZXMsIHNlbGVjdG9yLCBsaXN0ZW5lcikge1xuICAgIHJldHVybiBlID0+IHtcblxuICAgICAgICBkZWxlZ2F0ZXMuZm9yRWFjaChkZWxlZ2F0ZSA9PiB7XG5cbiAgICAgICAgICAgIGNvbnN0IGN1cnJlbnQgPSBzZWxlY3RvclswXSA9PT0gJz4nXG4gICAgICAgICAgICAgICAgPyBmaW5kQWxsKHNlbGVjdG9yLCBkZWxlZ2F0ZSkucmV2ZXJzZSgpLmZpbHRlcihlbGVtZW50ID0+IHdpdGhpbihlLnRhcmdldCwgZWxlbWVudCkpWzBdXG4gICAgICAgICAgICAgICAgOiBjbG9zZXN0KGUudGFyZ2V0LCBzZWxlY3Rvcik7XG5cbiAgICAgICAgICAgIGlmIChjdXJyZW50KSB7XG4gICAgICAgICAgICAgICAgZS5kZWxlZ2F0ZSA9IGRlbGVnYXRlO1xuICAgICAgICAgICAgICAgIGUuY3VycmVudCA9IGN1cnJlbnQ7XG5cbiAgICAgICAgICAgICAgICBsaXN0ZW5lci5jYWxsKHRoaXMsIGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pO1xuXG4gICAgfTtcbn1cblxuZnVuY3Rpb24gZGV0YWlsKGxpc3RlbmVyKSB7XG4gICAgcmV0dXJuIGUgPT4gaXNBcnJheShlLmRldGFpbCkgPyBsaXN0ZW5lciguLi5bZV0uY29uY2F0KGUuZGV0YWlsKSkgOiBsaXN0ZW5lcihlKTtcbn1cblxuZnVuY3Rpb24gaXNFdmVudFRhcmdldCh0YXJnZXQpIHtcbiAgICByZXR1cm4gdGFyZ2V0ICYmICdhZGRFdmVudExpc3RlbmVyJyBpbiB0YXJnZXQ7XG59XG5cbmZ1bmN0aW9uIHRvRXZlbnRUYXJnZXQodGFyZ2V0KSB7XG4gICAgcmV0dXJuIGlzRXZlbnRUYXJnZXQodGFyZ2V0KSA/IHRhcmdldCA6IHRvTm9kZSh0YXJnZXQpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9FdmVudFRhcmdldHModGFyZ2V0KSB7XG4gICAgcmV0dXJuIGlzQXJyYXkodGFyZ2V0KVxuICAgICAgICAgICAgPyB0YXJnZXQubWFwKHRvRXZlbnRUYXJnZXQpLmZpbHRlcihCb29sZWFuKVxuICAgICAgICAgICAgOiBpc1N0cmluZyh0YXJnZXQpXG4gICAgICAgICAgICAgICAgPyBmaW5kQWxsKHRhcmdldClcbiAgICAgICAgICAgICAgICA6IGlzRXZlbnRUYXJnZXQodGFyZ2V0KVxuICAgICAgICAgICAgICAgICAgICA/IFt0YXJnZXRdXG4gICAgICAgICAgICAgICAgICAgIDogdG9Ob2Rlcyh0YXJnZXQpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNUb3VjaChlKSB7XG4gICAgcmV0dXJuIGUucG9pbnRlclR5cGUgPT09ICd0b3VjaCcgfHwgZS50b3VjaGVzO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZ2V0RXZlbnRQb3MoZSwgcHJvcCA9ICdjbGllbnQnKSB7XG4gICAgY29uc3Qge3RvdWNoZXMsIGNoYW5nZWRUb3VjaGVzfSA9IGU7XG4gICAgY29uc3Qge1tgJHtwcm9wfVhgXTogeCwgW2Ake3Byb3B9WWBdOiB5fSA9IHRvdWNoZXMgJiYgdG91Y2hlc1swXSB8fCBjaGFuZ2VkVG91Y2hlcyAmJiBjaGFuZ2VkVG91Y2hlc1swXSB8fCBlO1xuXG4gICAgcmV0dXJuIHt4LCB5fTtcbn1cbiIsIi8qIGdsb2JhbCBzZXRJbW1lZGlhdGUgKi9cbmltcG9ydCB7aXNGdW5jdGlvbiwgaXNPYmplY3R9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBjb25zdCBQcm9taXNlID0gJ1Byb21pc2UnIGluIHdpbmRvdyA/IHdpbmRvdy5Qcm9taXNlIDogUHJvbWlzZUZuO1xuXG5leHBvcnQgY2xhc3MgRGVmZXJyZWQge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLnByb21pc2UgPSBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgICAgICB0aGlzLnJlamVjdCA9IHJlamVjdDtcbiAgICAgICAgICAgIHRoaXMucmVzb2x2ZSA9IHJlc29sdmU7XG4gICAgICAgIH0pO1xuICAgIH1cbn1cblxuLyoqXG4gKiBQcm9taXNlcy9BKyBwb2x5ZmlsbCB2MS4xLjQgKGh0dHBzOi8vZ2l0aHViLmNvbS9icmFtc3RlaW4vcHJvbWlzKVxuICovXG5cbmNvbnN0IFJFU09MVkVEID0gMDtcbmNvbnN0IFJFSkVDVEVEID0gMTtcbmNvbnN0IFBFTkRJTkcgPSAyO1xuXG5jb25zdCBhc3luYyA9ICdzZXRJbW1lZGlhdGUnIGluIHdpbmRvdyA/IHNldEltbWVkaWF0ZSA6IHNldFRpbWVvdXQ7XG5cbmZ1bmN0aW9uIFByb21pc2VGbihleGVjdXRvcikge1xuXG4gICAgdGhpcy5zdGF0ZSA9IFBFTkRJTkc7XG4gICAgdGhpcy52YWx1ZSA9IHVuZGVmaW5lZDtcbiAgICB0aGlzLmRlZmVycmVkID0gW107XG5cbiAgICBjb25zdCBwcm9taXNlID0gdGhpcztcblxuICAgIHRyeSB7XG4gICAgICAgIGV4ZWN1dG9yKFxuICAgICAgICAgICAgeCA9PiB7XG4gICAgICAgICAgICAgICAgcHJvbWlzZS5yZXNvbHZlKHgpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHIgPT4ge1xuICAgICAgICAgICAgICAgIHByb21pc2UucmVqZWN0KHIpO1xuICAgICAgICAgICAgfVxuICAgICAgICApO1xuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgcHJvbWlzZS5yZWplY3QoZSk7XG4gICAgfVxufVxuXG5Qcm9taXNlRm4ucmVqZWN0ID0gZnVuY3Rpb24gKHIpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIHJlamVjdChyKTtcbiAgICB9KTtcbn07XG5cblByb21pc2VGbi5yZXNvbHZlID0gZnVuY3Rpb24gKHgpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIHJlc29sdmUoeCk7XG4gICAgfSk7XG59O1xuXG5Qcm9taXNlRm4uYWxsID0gZnVuY3Rpb24gYWxsKGl0ZXJhYmxlKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlRm4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCByZXN1bHQgPSBbXTtcbiAgICAgICAgbGV0IGNvdW50ID0gMDtcblxuICAgICAgICBpZiAoaXRlcmFibGUubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICByZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiByZXNvbHZlcihpKSB7XG4gICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24gKHgpIHtcbiAgICAgICAgICAgICAgICByZXN1bHRbaV0gPSB4O1xuICAgICAgICAgICAgICAgIGNvdW50ICs9IDE7XG5cbiAgICAgICAgICAgICAgICBpZiAoY291bnQgPT09IGl0ZXJhYmxlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlcmFibGUubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIFByb21pc2VGbi5yZXNvbHZlKGl0ZXJhYmxlW2ldKS50aGVuKHJlc29sdmVyKGkpLCByZWplY3QpO1xuICAgICAgICB9XG4gICAgfSk7XG59O1xuXG5Qcm9taXNlRm4ucmFjZSA9IGZ1bmN0aW9uIHJhY2UoaXRlcmFibGUpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlcmFibGUubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIFByb21pc2VGbi5yZXNvbHZlKGl0ZXJhYmxlW2ldKS50aGVuKHJlc29sdmUsIHJlamVjdCk7XG4gICAgICAgIH1cbiAgICB9KTtcbn07XG5cbmNvbnN0IHAgPSBQcm9taXNlRm4ucHJvdG90eXBlO1xuXG5wLnJlc29sdmUgPSBmdW5jdGlvbiByZXNvbHZlKHgpIHtcbiAgICBjb25zdCBwcm9taXNlID0gdGhpcztcblxuICAgIGlmIChwcm9taXNlLnN0YXRlID09PSBQRU5ESU5HKSB7XG4gICAgICAgIGlmICh4ID09PSBwcm9taXNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdQcm9taXNlIHNldHRsZWQgd2l0aCBpdHNlbGYuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgY2FsbGVkID0gZmFsc2U7XG5cbiAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgIGNvbnN0IHRoZW4gPSB4ICYmIHgudGhlbjtcblxuICAgICAgICAgICAgaWYgKHggIT09IG51bGwgJiYgaXNPYmplY3QoeCkgJiYgaXNGdW5jdGlvbih0aGVuKSkge1xuICAgICAgICAgICAgICAgIHRoZW4uY2FsbChcbiAgICAgICAgICAgICAgICAgICAgeCxcbiAgICAgICAgICAgICAgICAgICAgeCA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIWNhbGxlZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHByb21pc2UucmVzb2x2ZSh4KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHIgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFjYWxsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwcm9taXNlLnJlamVjdChyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgaWYgKCFjYWxsZWQpIHtcbiAgICAgICAgICAgICAgICBwcm9taXNlLnJlamVjdChlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHByb21pc2Uuc3RhdGUgPSBSRVNPTFZFRDtcbiAgICAgICAgcHJvbWlzZS52YWx1ZSA9IHg7XG4gICAgICAgIHByb21pc2Uubm90aWZ5KCk7XG4gICAgfVxufTtcblxucC5yZWplY3QgPSBmdW5jdGlvbiByZWplY3QocmVhc29uKSB7XG4gICAgY29uc3QgcHJvbWlzZSA9IHRoaXM7XG5cbiAgICBpZiAocHJvbWlzZS5zdGF0ZSA9PT0gUEVORElORykge1xuICAgICAgICBpZiAocmVhc29uID09PSBwcm9taXNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdQcm9taXNlIHNldHRsZWQgd2l0aCBpdHNlbGYuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBwcm9taXNlLnN0YXRlID0gUkVKRUNURUQ7XG4gICAgICAgIHByb21pc2UudmFsdWUgPSByZWFzb247XG4gICAgICAgIHByb21pc2Uubm90aWZ5KCk7XG4gICAgfVxufTtcblxucC5ub3RpZnkgPSBmdW5jdGlvbiBub3RpZnkoKSB7XG4gICAgYXN5bmMoKCkgPT4ge1xuICAgICAgICBpZiAodGhpcy5zdGF0ZSAhPT0gUEVORElORykge1xuICAgICAgICAgICAgd2hpbGUgKHRoaXMuZGVmZXJyZWQubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgW29uUmVzb2x2ZWQsIG9uUmVqZWN0ZWQsIHJlc29sdmUsIHJlamVjdF0gPSB0aGlzLmRlZmVycmVkLnNoaWZ0KCk7XG5cbiAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5zdGF0ZSA9PT0gUkVTT0xWRUQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKG9uUmVzb2x2ZWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShvblJlc29sdmVkLmNhbGwodW5kZWZpbmVkLCB0aGlzLnZhbHVlKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc29sdmUodGhpcy52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5zdGF0ZSA9PT0gUkVKRUNURUQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKG9uUmVqZWN0ZWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShvblJlamVjdGVkLmNhbGwodW5kZWZpbmVkLCB0aGlzLnZhbHVlKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlamVjdCh0aGlzLnZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVqZWN0KGUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0pO1xufTtcblxucC50aGVuID0gZnVuY3Rpb24gdGhlbihvblJlc29sdmVkLCBvblJlamVjdGVkKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlRm4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICB0aGlzLmRlZmVycmVkLnB1c2goW29uUmVzb2x2ZWQsIG9uUmVqZWN0ZWQsIHJlc29sdmUsIHJlamVjdF0pO1xuICAgICAgICB0aGlzLm5vdGlmeSgpO1xuICAgIH0pO1xufTtcblxucC5jYXRjaCA9IGZ1bmN0aW9uIChvblJlamVjdGVkKSB7XG4gICAgcmV0dXJuIHRoaXMudGhlbih1bmRlZmluZWQsIG9uUmVqZWN0ZWQpO1xufTtcbiIsImltcG9ydCB7b259IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtQcm9taXNlfSBmcm9tICcuL3Byb21pc2UnO1xuaW1wb3J0IHthc3NpZ24sIG5vb3B9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBmdW5jdGlvbiBhamF4KHVybCwgb3B0aW9ucykge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG5cbiAgICAgICAgY29uc3QgZW52ID0gYXNzaWduKHtcbiAgICAgICAgICAgIGRhdGE6IG51bGwsXG4gICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgICAgICAgaGVhZGVyczoge30sXG4gICAgICAgICAgICB4aHI6IG5ldyBYTUxIdHRwUmVxdWVzdCgpLFxuICAgICAgICAgICAgYmVmb3JlU2VuZDogbm9vcCxcbiAgICAgICAgICAgIHJlc3BvbnNlVHlwZTogJydcbiAgICAgICAgfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgZW52LmJlZm9yZVNlbmQoZW52KTtcblxuICAgICAgICBjb25zdCB7eGhyfSA9IGVudjtcblxuICAgICAgICBmb3IgKGNvbnN0IHByb3AgaW4gZW52KSB7XG4gICAgICAgICAgICBpZiAocHJvcCBpbiB4aHIpIHtcbiAgICAgICAgICAgICAgICB0cnkge1xuXG4gICAgICAgICAgICAgICAgICAgIHhocltwcm9wXSA9IGVudltwcm9wXTtcblxuICAgICAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHt9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB4aHIub3BlbihlbnYubWV0aG9kLnRvVXBwZXJDYXNlKCksIHVybCk7XG5cbiAgICAgICAgZm9yIChjb25zdCBoZWFkZXIgaW4gZW52LmhlYWRlcnMpIHtcbiAgICAgICAgICAgIHhoci5zZXRSZXF1ZXN0SGVhZGVyKGhlYWRlciwgZW52LmhlYWRlcnNbaGVhZGVyXSk7XG4gICAgICAgIH1cblxuICAgICAgICBvbih4aHIsICdsb2FkJywgKCkgPT4ge1xuXG4gICAgICAgICAgICBpZiAoeGhyLnN0YXR1cyA9PT0gMCB8fCB4aHIuc3RhdHVzID49IDIwMCAmJiB4aHIuc3RhdHVzIDwgMzAwIHx8IHhoci5zdGF0dXMgPT09IDMwNCkge1xuICAgICAgICAgICAgICAgIHJlc29sdmUoeGhyKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmVqZWN0KGFzc2lnbihFcnJvcih4aHIuc3RhdHVzVGV4dCksIHtcbiAgICAgICAgICAgICAgICAgICAgeGhyLFxuICAgICAgICAgICAgICAgICAgICBzdGF0dXM6IHhoci5zdGF0dXNcbiAgICAgICAgICAgICAgICB9KSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgb24oeGhyLCAnZXJyb3InLCAoKSA9PiByZWplY3QoYXNzaWduKEVycm9yKCdOZXR3b3JrIEVycm9yJyksIHt4aHJ9KSkpO1xuICAgICAgICBvbih4aHIsICd0aW1lb3V0JywgKCkgPT4gcmVqZWN0KGFzc2lnbihFcnJvcignTmV0d29yayBUaW1lb3V0JyksIHt4aHJ9KSkpO1xuXG4gICAgICAgIHhoci5zZW5kKGVudi5kYXRhKTtcbiAgICB9KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGdldEltYWdlKHNyYywgc3Jjc2V0LCBzaXplcykge1xuXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgaW1nID0gbmV3IEltYWdlKCk7XG5cbiAgICAgICAgaW1nLm9uZXJyb3IgPSByZWplY3Q7XG4gICAgICAgIGltZy5vbmxvYWQgPSAoKSA9PiByZXNvbHZlKGltZyk7XG5cbiAgICAgICAgc2l6ZXMgJiYgKGltZy5zaXplcyA9IHNpemVzKTtcbiAgICAgICAgc3Jjc2V0ICYmIChpbWcuc3Jjc2V0ID0gc3Jjc2V0KTtcbiAgICAgICAgaW1nLnNyYyA9IHNyYztcbiAgICB9KTtcblxufVxuIiwiLyogZ2xvYmFsIERvY3VtZW50VG91Y2ggKi9cbmltcG9ydCB7YXR0cn0gZnJvbSAnLi9hdHRyJztcblxuZXhwb3J0IGNvbnN0IGlzSUUgPSAvbXNpZXx0cmlkZW50L2kudGVzdCh3aW5kb3cubmF2aWdhdG9yLnVzZXJBZ2VudCk7XG5leHBvcnQgY29uc3QgaXNSdGwgPSBhdHRyKGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCwgJ2RpcicpID09PSAncnRsJztcblxuY29uc3QgaGFzVG91Y2hFdmVudHMgPSAnb250b3VjaHN0YXJ0JyBpbiB3aW5kb3c7XG5jb25zdCBoYXNQb2ludGVyRXZlbnRzID0gd2luZG93LlBvaW50ZXJFdmVudDtcbmV4cG9ydCBjb25zdCBoYXNUb3VjaCA9IGhhc1RvdWNoRXZlbnRzXG4gICAgfHwgd2luZG93LkRvY3VtZW50VG91Y2ggJiYgZG9jdW1lbnQgaW5zdGFuY2VvZiBEb2N1bWVudFRvdWNoXG4gICAgfHwgbmF2aWdhdG9yLm1heFRvdWNoUG9pbnRzOyAvLyBJRSA+PTExXG5cbmV4cG9ydCBjb25zdCBwb2ludGVyRG93biA9IGhhc1BvaW50ZXJFdmVudHMgPyAncG9pbnRlcmRvd24nIDogaGFzVG91Y2hFdmVudHMgPyAndG91Y2hzdGFydCcgOiAnbW91c2Vkb3duJztcbmV4cG9ydCBjb25zdCBwb2ludGVyTW92ZSA9IGhhc1BvaW50ZXJFdmVudHMgPyAncG9pbnRlcm1vdmUnIDogaGFzVG91Y2hFdmVudHMgPyAndG91Y2htb3ZlJyA6ICdtb3VzZW1vdmUnO1xuZXhwb3J0IGNvbnN0IHBvaW50ZXJVcCA9IGhhc1BvaW50ZXJFdmVudHMgPyAncG9pbnRlcnVwJyA6IGhhc1RvdWNoRXZlbnRzID8gJ3RvdWNoZW5kJyA6ICdtb3VzZXVwJztcbmV4cG9ydCBjb25zdCBwb2ludGVyRW50ZXIgPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJlbnRlcicgOiBoYXNUb3VjaEV2ZW50cyA/ICcnIDogJ21vdXNlZW50ZXInO1xuZXhwb3J0IGNvbnN0IHBvaW50ZXJMZWF2ZSA9IGhhc1BvaW50ZXJFdmVudHMgPyAncG9pbnRlcmxlYXZlJyA6IGhhc1RvdWNoRXZlbnRzID8gJycgOiAnbW91c2VsZWF2ZSc7XG5leHBvcnQgY29uc3QgcG9pbnRlckNhbmNlbCA9IGhhc1BvaW50ZXJFdmVudHMgPyAncG9pbnRlcmNhbmNlbCcgOiAndG91Y2hjYW5jZWwnO1xuXG4iLCJpbXBvcnQge29ufSBmcm9tICcuL2V2ZW50JztcbmltcG9ydCB7ZmluZCwgZmluZEFsbH0gZnJvbSAnLi9zZWxlY3Rvcic7XG5pbXBvcnQge2NsYW1wLCBpc051bWVyaWMsIGlzU3RyaW5nLCBpc1VuZGVmaW5lZCwgdG9Ob2RlLCB0b05vZGVzLCB0b051bWJlcn0gZnJvbSAnLi9sYW5nJztcblxuZXhwb3J0IGZ1bmN0aW9uIHJlYWR5KGZuKSB7XG5cbiAgICBpZiAoZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnKSB7XG4gICAgICAgIGZuKCk7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCB1bmJpbmQgPSBvbihkb2N1bWVudCwgJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHVuYmluZCgpO1xuICAgICAgICBmbigpO1xuICAgIH0pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaW5kZXgoZWxlbWVudCwgcmVmKSB7XG4gICAgcmV0dXJuIHJlZlxuICAgICAgICA/IHRvTm9kZXMoZWxlbWVudCkuaW5kZXhPZih0b05vZGUocmVmKSlcbiAgICAgICAgOiB0b05vZGVzKChlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpKSAmJiBlbGVtZW50LnBhcmVudE5vZGUuY2hpbGRyZW4pLmluZGV4T2YoZWxlbWVudCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRJbmRleChpLCBlbGVtZW50cywgY3VycmVudCA9IDAsIGZpbml0ZSA9IGZhbHNlKSB7XG5cbiAgICBlbGVtZW50cyA9IHRvTm9kZXMoZWxlbWVudHMpO1xuXG4gICAgY29uc3Qge2xlbmd0aH0gPSBlbGVtZW50cztcblxuICAgIGkgPSBpc051bWVyaWMoaSlcbiAgICAgICAgPyB0b051bWJlcihpKVxuICAgICAgICA6IGkgPT09ICduZXh0J1xuICAgICAgICAgICAgPyBjdXJyZW50ICsgMVxuICAgICAgICAgICAgOiBpID09PSAncHJldmlvdXMnXG4gICAgICAgICAgICAgICAgPyBjdXJyZW50IC0gMVxuICAgICAgICAgICAgICAgIDogaW5kZXgoZWxlbWVudHMsIGkpO1xuXG4gICAgaWYgKGZpbml0ZSkge1xuICAgICAgICByZXR1cm4gY2xhbXAoaSwgMCwgbGVuZ3RoIC0gMSk7XG4gICAgfVxuXG4gICAgaSAlPSBsZW5ndGg7XG5cbiAgICByZXR1cm4gaSA8IDAgPyBpICsgbGVuZ3RoIDogaTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGVtcHR5KGVsZW1lbnQpIHtcbiAgICBlbGVtZW50ID0gJChlbGVtZW50KTtcbiAgICBlbGVtZW50LmlubmVySFRNTCA9ICcnO1xuICAgIHJldHVybiBlbGVtZW50O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaHRtbChwYXJlbnQsIGh0bWwpIHtcbiAgICBwYXJlbnQgPSAkKHBhcmVudCk7XG4gICAgcmV0dXJuIGlzVW5kZWZpbmVkKGh0bWwpXG4gICAgICAgID8gcGFyZW50LmlubmVySFRNTFxuICAgICAgICA6IGFwcGVuZChwYXJlbnQuaGFzQ2hpbGROb2RlcygpID8gZW1wdHkocGFyZW50KSA6IHBhcmVudCwgaHRtbCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBwcmVwZW5kKHBhcmVudCwgZWxlbWVudCkge1xuXG4gICAgcGFyZW50ID0gJChwYXJlbnQpO1xuXG4gICAgaWYgKCFwYXJlbnQuaGFzQ2hpbGROb2RlcygpKSB7XG4gICAgICAgIHJldHVybiBhcHBlbmQocGFyZW50LCBlbGVtZW50KTtcbiAgICB9IGVsc2Uge1xuICAgICAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiBwYXJlbnQuaW5zZXJ0QmVmb3JlKGVsZW1lbnQsIHBhcmVudC5maXJzdENoaWxkKSk7XG4gICAgfVxufVxuXG5leHBvcnQgZnVuY3Rpb24gYXBwZW5kKHBhcmVudCwgZWxlbWVudCkge1xuICAgIHBhcmVudCA9ICQocGFyZW50KTtcbiAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiBwYXJlbnQuYXBwZW5kQ2hpbGQoZWxlbWVudCkpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYmVmb3JlKHJlZiwgZWxlbWVudCkge1xuICAgIHJlZiA9ICQocmVmKTtcbiAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiByZWYucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoZWxlbWVudCwgcmVmKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBhZnRlcihyZWYsIGVsZW1lbnQpIHtcbiAgICByZWYgPSAkKHJlZik7XG4gICAgcmV0dXJuIGluc2VydE5vZGVzKGVsZW1lbnQsIGVsZW1lbnQgPT4gcmVmLm5leHRTaWJsaW5nXG4gICAgICAgID8gYmVmb3JlKHJlZi5uZXh0U2libGluZywgZWxlbWVudClcbiAgICAgICAgOiBhcHBlbmQocmVmLnBhcmVudE5vZGUsIGVsZW1lbnQpXG4gICAgKTtcbn1cblxuZnVuY3Rpb24gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZm4pIHtcbiAgICBlbGVtZW50ID0gaXNTdHJpbmcoZWxlbWVudCkgPyBmcmFnbWVudChlbGVtZW50KSA6IGVsZW1lbnQ7XG4gICAgcmV0dXJuIGVsZW1lbnRcbiAgICAgICAgPyAnbGVuZ3RoJyBpbiBlbGVtZW50XG4gICAgICAgICAgICA/IHRvTm9kZXMoZWxlbWVudCkubWFwKGZuKVxuICAgICAgICAgICAgOiBmbihlbGVtZW50KVxuICAgICAgICA6IG51bGw7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiByZW1vdmUoZWxlbWVudCkge1xuICAgIHRvTm9kZXMoZWxlbWVudCkubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5wYXJlbnROb2RlICYmIGVsZW1lbnQucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChlbGVtZW50KSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB3cmFwQWxsKGVsZW1lbnQsIHN0cnVjdHVyZSkge1xuXG4gICAgc3RydWN0dXJlID0gdG9Ob2RlKGJlZm9yZShlbGVtZW50LCBzdHJ1Y3R1cmUpKTtcblxuICAgIHdoaWxlIChzdHJ1Y3R1cmUuZmlyc3RDaGlsZCkge1xuICAgICAgICBzdHJ1Y3R1cmUgPSBzdHJ1Y3R1cmUuZmlyc3RDaGlsZDtcbiAgICB9XG5cbiAgICBhcHBlbmQoc3RydWN0dXJlLCBlbGVtZW50KTtcblxuICAgIHJldHVybiBzdHJ1Y3R1cmU7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB3cmFwSW5uZXIoZWxlbWVudCwgc3RydWN0dXJlKSB7XG4gICAgcmV0dXJuIHRvTm9kZXModG9Ob2RlcyhlbGVtZW50KS5tYXAoZWxlbWVudCA9PlxuICAgICAgICBlbGVtZW50Lmhhc0NoaWxkTm9kZXMgPyB3cmFwQWxsKHRvTm9kZXMoZWxlbWVudC5jaGlsZE5vZGVzKSwgc3RydWN0dXJlKSA6IGFwcGVuZChlbGVtZW50LCBzdHJ1Y3R1cmUpXG4gICAgKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB1bndyYXAoZWxlbWVudCkge1xuICAgIHRvTm9kZXMoZWxlbWVudClcbiAgICAgICAgLm1hcChlbGVtZW50ID0+IGVsZW1lbnQucGFyZW50Tm9kZSlcbiAgICAgICAgLmZpbHRlcigodmFsdWUsIGluZGV4LCBzZWxmKSA9PiBzZWxmLmluZGV4T2YodmFsdWUpID09PSBpbmRleClcbiAgICAgICAgLmZvckVhY2gocGFyZW50ID0+IHtcbiAgICAgICAgICAgIGJlZm9yZShwYXJlbnQsIHBhcmVudC5jaGlsZE5vZGVzKTtcbiAgICAgICAgICAgIHJlbW92ZShwYXJlbnQpO1xuICAgICAgICB9KTtcbn1cblxuY29uc3QgZnJhZ21lbnRSZSA9IC9eXFxzKjwoXFx3K3whKVtePl0qPi87XG5jb25zdCBzaW5nbGVUYWdSZSA9IC9ePChcXHcrKVxccypcXC8/Pig/OjxcXC9cXDE+KT8kLztcblxuZXhwb3J0IGZ1bmN0aW9uIGZyYWdtZW50KGh0bWwpIHtcblxuICAgIGNvbnN0IG1hdGNoZXMgPSBzaW5nbGVUYWdSZS5leGVjKGh0bWwpO1xuICAgIGlmIChtYXRjaGVzKSB7XG4gICAgICAgIHJldHVybiBkb2N1bWVudC5jcmVhdGVFbGVtZW50KG1hdGNoZXNbMV0pO1xuICAgIH1cblxuICAgIGNvbnN0IGNvbnRhaW5lciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICAgIGlmIChmcmFnbWVudFJlLnRlc3QoaHRtbCkpIHtcbiAgICAgICAgY29udGFpbmVyLmluc2VydEFkamFjZW50SFRNTCgnYmVmb3JlZW5kJywgaHRtbC50cmltKCkpO1xuICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnRhaW5lci50ZXh0Q29udGVudCA9IGh0bWw7XG4gICAgfVxuXG4gICAgcmV0dXJuIGNvbnRhaW5lci5jaGlsZE5vZGVzLmxlbmd0aCA+IDEgPyB0b05vZGVzKGNvbnRhaW5lci5jaGlsZE5vZGVzKSA6IGNvbnRhaW5lci5maXJzdENoaWxkO1xuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBhcHBseShub2RlLCBmbikge1xuXG4gICAgaWYgKCFub2RlIHx8IG5vZGUubm9kZVR5cGUgIT09IDEpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGZuKG5vZGUpO1xuICAgIG5vZGUgPSBub2RlLmZpcnN0RWxlbWVudENoaWxkO1xuICAgIHdoaWxlIChub2RlKSB7XG4gICAgICAgIGFwcGx5KG5vZGUsIGZuKTtcbiAgICAgICAgbm9kZSA9IG5vZGUubmV4dEVsZW1lbnRTaWJsaW5nO1xuICAgIH1cbn1cblxuZXhwb3J0IGZ1bmN0aW9uICQoc2VsZWN0b3IsIGNvbnRleHQpIHtcbiAgICByZXR1cm4gIWlzU3RyaW5nKHNlbGVjdG9yKVxuICAgICAgICA/IHRvTm9kZShzZWxlY3RvcilcbiAgICAgICAgOiBpc0h0bWwoc2VsZWN0b3IpXG4gICAgICAgICAgICA/IHRvTm9kZShmcmFnbWVudChzZWxlY3RvcikpXG4gICAgICAgICAgICA6IGZpbmQoc2VsZWN0b3IsIGNvbnRleHQpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gJCQoc2VsZWN0b3IsIGNvbnRleHQpIHtcbiAgICByZXR1cm4gIWlzU3RyaW5nKHNlbGVjdG9yKVxuICAgICAgICA/IHRvTm9kZXMoc2VsZWN0b3IpXG4gICAgICAgIDogaXNIdG1sKHNlbGVjdG9yKVxuICAgICAgICAgICAgPyB0b05vZGVzKGZyYWdtZW50KHNlbGVjdG9yKSlcbiAgICAgICAgICAgIDogZmluZEFsbChzZWxlY3RvciwgY29udGV4dCk7XG59XG5cbmZ1bmN0aW9uIGlzSHRtbChzdHIpIHtcbiAgICByZXR1cm4gc3RyWzBdID09PSAnPCcgfHwgc3RyLm1hdGNoKC9eXFxzKjwvKTtcbn1cblxuIiwiaW1wb3J0IHthdHRyfSBmcm9tICcuL2F0dHInO1xuaW1wb3J0IHtoYXNPd24sIGluY2x1ZGVzLCBpc1N0cmluZywgaXNVbmRlZmluZWQsIHRvTm9kZXN9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBmdW5jdGlvbiBhZGRDbGFzcyhlbGVtZW50LCAuLi5hcmdzKSB7XG4gICAgYXBwbHkoZWxlbWVudCwgYXJncywgJ2FkZCcpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcmVtb3ZlQ2xhc3MoZWxlbWVudCwgLi4uYXJncykge1xuICAgIGFwcGx5KGVsZW1lbnQsIGFyZ3MsICdyZW1vdmUnKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHJlbW92ZUNsYXNzZXMoZWxlbWVudCwgY2xzKSB7XG4gICAgYXR0cihlbGVtZW50LCAnY2xhc3MnLCB2YWx1ZSA9PiAodmFsdWUgfHwgJycpLnJlcGxhY2UobmV3IFJlZ0V4cChgXFxcXGIke2Nsc31cXFxcYmAsICdnJyksICcnKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiByZXBsYWNlQ2xhc3MoZWxlbWVudCwgLi4uYXJncykge1xuICAgIGFyZ3NbMF0gJiYgcmVtb3ZlQ2xhc3MoZWxlbWVudCwgYXJnc1swXSk7XG4gICAgYXJnc1sxXSAmJiBhZGRDbGFzcyhlbGVtZW50LCBhcmdzWzFdKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGhhc0NsYXNzKGVsZW1lbnQsIGNscykge1xuICAgIHJldHVybiBjbHMgJiYgdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gZWxlbWVudC5jbGFzc0xpc3QuY29udGFpbnMoY2xzLnNwbGl0KCcgJylbMF0pKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvZ2dsZUNsYXNzKGVsZW1lbnQsIC4uLmFyZ3MpIHtcblxuICAgIGlmICghYXJncy5sZW5ndGgpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGFyZ3MgPSBnZXRBcmdzKGFyZ3MpO1xuXG4gICAgY29uc3QgZm9yY2UgPSAhaXNTdHJpbmcoYXJnc1thcmdzLmxlbmd0aCAtIDFdKSA/IGFyZ3MucG9wKCkgOiBbXTsgLy8gaW4gaU9TIDkuMyBmb3JjZSA9PT0gdW5kZWZpbmVkIGV2YWx1YXRlcyB0byBmYWxzZVxuXG4gICAgYXJncyA9IGFyZ3MuZmlsdGVyKEJvb2xlYW4pO1xuXG4gICAgdG9Ob2RlcyhlbGVtZW50KS5mb3JFYWNoKCh7Y2xhc3NMaXN0fSkgPT4ge1xuICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IGFyZ3MubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIHN1cHBvcnRzLkZvcmNlXG4gICAgICAgICAgICAgICAgPyBjbGFzc0xpc3QudG9nZ2xlKC4uLlthcmdzW2ldXS5jb25jYXQoZm9yY2UpKVxuICAgICAgICAgICAgICAgIDogKGNsYXNzTGlzdFsoIWlzVW5kZWZpbmVkKGZvcmNlKSA/IGZvcmNlIDogIWNsYXNzTGlzdC5jb250YWlucyhhcmdzW2ldKSkgPyAnYWRkJyA6ICdyZW1vdmUnXShhcmdzW2ldKSk7XG4gICAgICAgIH1cbiAgICB9KTtcblxufVxuXG5mdW5jdGlvbiBhcHBseShlbGVtZW50LCBhcmdzLCBmbikge1xuICAgIGFyZ3MgPSBnZXRBcmdzKGFyZ3MpLmZpbHRlcihCb29sZWFuKTtcblxuICAgIGFyZ3MubGVuZ3RoICYmIHRvTm9kZXMoZWxlbWVudCkuZm9yRWFjaCgoe2NsYXNzTGlzdH0pID0+IHtcbiAgICAgICAgc3VwcG9ydHMuTXVsdGlwbGVcbiAgICAgICAgICAgID8gY2xhc3NMaXN0W2ZuXSguLi5hcmdzKVxuICAgICAgICAgICAgOiBhcmdzLmZvckVhY2goY2xzID0+IGNsYXNzTGlzdFtmbl0oY2xzKSk7XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIGdldEFyZ3MoYXJncykge1xuICAgIHJldHVybiBhcmdzLnJlZHVjZSgoYXJncywgYXJnKSA9PlxuICAgICAgICBhcmdzLmNvbmNhdC5jYWxsKGFyZ3MsIGlzU3RyaW5nKGFyZykgJiYgaW5jbHVkZXMoYXJnLCAnICcpID8gYXJnLnRyaW0oKS5zcGxpdCgnICcpIDogYXJnKVxuICAgICAgICAsIFtdKTtcbn1cblxuLy8gSUUgMTFcbmNvbnN0IHN1cHBvcnRzID0ge1xuXG4gICAgZ2V0IE11bHRpcGxlKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5nZXQoJ19tdWx0aXBsZScpO1xuICAgIH0sXG5cbiAgICBnZXQgRm9yY2UoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmdldCgnX2ZvcmNlJyk7XG4gICAgfSxcblxuICAgIGdldChrZXkpIHtcblxuICAgICAgICBpZiAoIWhhc093bih0aGlzLCBrZXkpKSB7XG4gICAgICAgICAgICBjb25zdCB7Y2xhc3NMaXN0fSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ18nKTtcbiAgICAgICAgICAgIGNsYXNzTGlzdC5hZGQoJ2EnLCAnYicpO1xuICAgICAgICAgICAgY2xhc3NMaXN0LnRvZ2dsZSgnYycsIGZhbHNlKTtcbiAgICAgICAgICAgIHRoaXMuX211bHRpcGxlID0gY2xhc3NMaXN0LmNvbnRhaW5zKCdiJyk7XG4gICAgICAgICAgICB0aGlzLl9mb3JjZSA9ICFjbGFzc0xpc3QuY29udGFpbnMoJ2MnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0aGlzW2tleV07XG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHtpc0lFfSBmcm9tICcuL2Vudic7XG5pbXBvcnQge2FwcGVuZCwgcmVtb3ZlfSBmcm9tICcuL2RvbSc7XG5pbXBvcnQge2FkZENsYXNzfSBmcm9tICcuL2NsYXNzJztcbmltcG9ydCB7ZWFjaCwgaHlwaGVuYXRlLCBpc0FycmF5LCBpc051bWJlciwgaXNOdW1lcmljLCBpc09iamVjdCwgaXNTdHJpbmcsIGlzVW5kZWZpbmVkLCB0b05vZGUsIHRvTm9kZXN9IGZyb20gJy4vbGFuZyc7XG5cbmNvbnN0IGNzc051bWJlciA9IHtcbiAgICAnYW5pbWF0aW9uLWl0ZXJhdGlvbi1jb3VudCc6IHRydWUsXG4gICAgJ2NvbHVtbi1jb3VudCc6IHRydWUsXG4gICAgJ2ZpbGwtb3BhY2l0eSc6IHRydWUsXG4gICAgJ2ZsZXgtZ3Jvdyc6IHRydWUsXG4gICAgJ2ZsZXgtc2hyaW5rJzogdHJ1ZSxcbiAgICAnZm9udC13ZWlnaHQnOiB0cnVlLFxuICAgICdsaW5lLWhlaWdodCc6IHRydWUsXG4gICAgJ29wYWNpdHknOiB0cnVlLFxuICAgICdvcmRlcic6IHRydWUsXG4gICAgJ29ycGhhbnMnOiB0cnVlLFxuICAgICdzdHJva2UtZGFzaGFycmF5JzogdHJ1ZSxcbiAgICAnc3Ryb2tlLWRhc2hvZmZzZXQnOiB0cnVlLFxuICAgICd3aWRvd3MnOiB0cnVlLFxuICAgICd6LWluZGV4JzogdHJ1ZSxcbiAgICAnem9vbSc6IHRydWVcbn07XG5cbmV4cG9ydCBmdW5jdGlvbiBjc3MoZWxlbWVudCwgcHJvcGVydHksIHZhbHVlKSB7XG5cbiAgICByZXR1cm4gdG9Ob2RlcyhlbGVtZW50KS5tYXAoZWxlbWVudCA9PiB7XG5cbiAgICAgICAgaWYgKGlzU3RyaW5nKHByb3BlcnR5KSkge1xuXG4gICAgICAgICAgICBwcm9wZXJ0eSA9IHByb3BOYW1lKHByb3BlcnR5KTtcblxuICAgICAgICAgICAgaWYgKGlzVW5kZWZpbmVkKHZhbHVlKSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBnZXRTdHlsZShlbGVtZW50LCBwcm9wZXJ0eSk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKCF2YWx1ZSAmJiAhaXNOdW1iZXIodmFsdWUpKSB7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5zdHlsZS5yZW1vdmVQcm9wZXJ0eShwcm9wZXJ0eSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuc3R5bGVbcHJvcGVydHldID0gaXNOdW1lcmljKHZhbHVlKSAmJiAhY3NzTnVtYmVyW3Byb3BlcnR5XSA/IGAke3ZhbHVlfXB4YCA6IHZhbHVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0gZWxzZSBpZiAoaXNBcnJheShwcm9wZXJ0eSkpIHtcblxuICAgICAgICAgICAgY29uc3Qgc3R5bGVzID0gZ2V0U3R5bGVzKGVsZW1lbnQpO1xuXG4gICAgICAgICAgICByZXR1cm4gcHJvcGVydHkucmVkdWNlKChwcm9wcywgcHJvcGVydHkpID0+IHtcbiAgICAgICAgICAgICAgICBwcm9wc1twcm9wZXJ0eV0gPSBzdHlsZXNbcHJvcE5hbWUocHJvcGVydHkpXTtcbiAgICAgICAgICAgICAgICByZXR1cm4gcHJvcHM7XG4gICAgICAgICAgICB9LCB7fSk7XG5cbiAgICAgICAgfSBlbHNlIGlmIChpc09iamVjdChwcm9wZXJ0eSkpIHtcbiAgICAgICAgICAgIGVhY2gocHJvcGVydHksICh2YWx1ZSwgcHJvcGVydHkpID0+IGNzcyhlbGVtZW50LCBwcm9wZXJ0eSwgdmFsdWUpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBlbGVtZW50O1xuXG4gICAgfSlbMF07XG5cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGdldFN0eWxlcyhlbGVtZW50LCBwc2V1ZG9FbHQpIHtcbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuICAgIHJldHVybiBlbGVtZW50Lm93bmVyRG9jdW1lbnQuZGVmYXVsdFZpZXcuZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50LCBwc2V1ZG9FbHQpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZ2V0U3R5bGUoZWxlbWVudCwgcHJvcGVydHksIHBzZXVkb0VsdCkge1xuICAgIHJldHVybiBnZXRTdHlsZXMoZWxlbWVudCwgcHNldWRvRWx0KVtwcm9wZXJ0eV07XG59XG5cbmNvbnN0IHZhcnMgPSB7fTtcblxuZXhwb3J0IGZ1bmN0aW9uIGdldENzc1ZhcihuYW1lKSB7XG5cbiAgICBjb25zdCBkb2NFbCA9IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcblxuICAgIGlmICghaXNJRSkge1xuICAgICAgICByZXR1cm4gZ2V0U3R5bGVzKGRvY0VsKS5nZXRQcm9wZXJ0eVZhbHVlKGAtLXVrLSR7bmFtZX1gKTtcbiAgICB9XG5cbiAgICBpZiAoIShuYW1lIGluIHZhcnMpKSB7XG5cbiAgICAgICAgLyogdXNhZ2UgaW4gY3NzOiAudWstbmFtZTpiZWZvcmUgeyBjb250ZW50OlwieHl6XCIgfSAqL1xuXG4gICAgICAgIGNvbnN0IGVsZW1lbnQgPSBhcHBlbmQoZG9jRWwsIGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpKTtcblxuICAgICAgICBhZGRDbGFzcyhlbGVtZW50LCBgdWstJHtuYW1lfWApO1xuXG4gICAgICAgIHZhcnNbbmFtZV0gPSBnZXRTdHlsZShlbGVtZW50LCAnY29udGVudCcsICc6YmVmb3JlJykucmVwbGFjZSgvXltcIiddKC4qKVtcIiddJC8sICckMScpO1xuXG4gICAgICAgIHJlbW92ZShlbGVtZW50KTtcblxuICAgIH1cblxuICAgIHJldHVybiB2YXJzW25hbWVdO1xuXG59XG5cbmNvbnN0IGNzc1Byb3BzID0ge307XG5cbmV4cG9ydCBmdW5jdGlvbiBwcm9wTmFtZShuYW1lKSB7XG5cbiAgICBsZXQgcmV0ID0gY3NzUHJvcHNbbmFtZV07XG4gICAgaWYgKCFyZXQpIHtcbiAgICAgICAgcmV0ID0gY3NzUHJvcHNbbmFtZV0gPSB2ZW5kb3JQcm9wTmFtZShuYW1lKSB8fCBuYW1lO1xuICAgIH1cbiAgICByZXR1cm4gcmV0O1xufVxuXG5jb25zdCBjc3NQcmVmaXhlcyA9IFsnd2Via2l0JywgJ21veicsICdtcyddO1xuXG5mdW5jdGlvbiB2ZW5kb3JQcm9wTmFtZShuYW1lKSB7XG5cbiAgICBuYW1lID0gaHlwaGVuYXRlKG5hbWUpO1xuXG4gICAgY29uc3Qge3N0eWxlfSA9IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcblxuICAgIGlmIChuYW1lIGluIHN0eWxlKSB7XG4gICAgICAgIHJldHVybiBuYW1lO1xuICAgIH1cblxuICAgIGxldCBpID0gY3NzUHJlZml4ZXMubGVuZ3RoLCBwcmVmaXhlZE5hbWU7XG5cbiAgICB3aGlsZSAoaS0tKSB7XG4gICAgICAgIHByZWZpeGVkTmFtZSA9IGAtJHtjc3NQcmVmaXhlc1tpXX0tJHtuYW1lfWA7XG4gICAgICAgIGlmIChwcmVmaXhlZE5hbWUgaW4gc3R5bGUpIHtcbiAgICAgICAgICAgIHJldHVybiBwcmVmaXhlZE5hbWU7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCJpbXBvcnQge2F0dHJ9IGZyb20gJy4vYXR0cic7XG5pbXBvcnQge1Byb21pc2V9IGZyb20gJy4vcHJvbWlzZSc7XG5pbXBvcnQge29uY2UsIHRyaWdnZXJ9IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtjc3MsIHByb3BOYW1lfSBmcm9tICcuL3N0eWxlJztcbmltcG9ydCB7YXNzaWduLCBzdGFydHNXaXRoLCB0b05vZGVzfSBmcm9tICcuL2xhbmcnO1xuaW1wb3J0IHthZGRDbGFzcywgaGFzQ2xhc3MsIHJlbW92ZUNsYXNzLCByZW1vdmVDbGFzc2VzfSBmcm9tICcuL2NsYXNzJztcblxuZXhwb3J0IGZ1bmN0aW9uIHRyYW5zaXRpb24oZWxlbWVudCwgcHJvcHMsIGR1cmF0aW9uID0gNDAwLCB0aW1pbmcgPSAnbGluZWFyJykge1xuXG4gICAgcmV0dXJuIFByb21pc2UuYWxsKHRvTm9kZXMoZWxlbWVudCkubWFwKGVsZW1lbnQgPT5cbiAgICAgICAgbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXG4gICAgICAgICAgICBmb3IgKGNvbnN0IG5hbWUgaW4gcHJvcHMpIHtcbiAgICAgICAgICAgICAgICBjb25zdCB2YWx1ZSA9IGNzcyhlbGVtZW50LCBuYW1lKTtcbiAgICAgICAgICAgICAgICBpZiAodmFsdWUgPT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgIGNzcyhlbGVtZW50LCBuYW1lLCB2YWx1ZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCB0aW1lciA9IHNldFRpbWVvdXQoKCkgPT4gdHJpZ2dlcihlbGVtZW50LCAndHJhbnNpdGlvbmVuZCcpLCBkdXJhdGlvbik7XG5cbiAgICAgICAgICAgIG9uY2UoZWxlbWVudCwgJ3RyYW5zaXRpb25lbmQgdHJhbnNpdGlvbmNhbmNlbGVkJywgKHt0eXBlfSkgPT4ge1xuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lcik7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3MoZWxlbWVudCwgJ3VrLXRyYW5zaXRpb24nKTtcbiAgICAgICAgICAgICAgICBjc3MoZWxlbWVudCwge1xuICAgICAgICAgICAgICAgICAgICAndHJhbnNpdGlvbi1wcm9wZXJ0eSc6ICcnLFxuICAgICAgICAgICAgICAgICAgICAndHJhbnNpdGlvbi1kdXJhdGlvbic6ICcnLFxuICAgICAgICAgICAgICAgICAgICAndHJhbnNpdGlvbi10aW1pbmctZnVuY3Rpb24nOiAnJ1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHR5cGUgPT09ICd0cmFuc2l0aW9uY2FuY2VsZWQnID8gcmVqZWN0KCkgOiByZXNvbHZlKCk7XG4gICAgICAgICAgICB9LCBmYWxzZSwgKHt0YXJnZXR9KSA9PiBlbGVtZW50ID09PSB0YXJnZXQpO1xuXG4gICAgICAgICAgICBhZGRDbGFzcyhlbGVtZW50LCAndWstdHJhbnNpdGlvbicpO1xuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIGFzc2lnbih7XG4gICAgICAgICAgICAgICAgJ3RyYW5zaXRpb24tcHJvcGVydHknOiBPYmplY3Qua2V5cyhwcm9wcykubWFwKHByb3BOYW1lKS5qb2luKCcsJyksXG4gICAgICAgICAgICAgICAgJ3RyYW5zaXRpb24tZHVyYXRpb24nOiBgJHtkdXJhdGlvbn1tc2AsXG4gICAgICAgICAgICAgICAgJ3RyYW5zaXRpb24tdGltaW5nLWZ1bmN0aW9uJzogdGltaW5nXG4gICAgICAgICAgICB9LCBwcm9wcykpO1xuXG4gICAgICAgIH0pXG4gICAgKSk7XG5cbn1cblxuZXhwb3J0IGNvbnN0IFRyYW5zaXRpb24gPSB7XG5cbiAgICBzdGFydDogdHJhbnNpdGlvbixcblxuICAgIHN0b3AoZWxlbWVudCkge1xuICAgICAgICB0cmlnZ2VyKGVsZW1lbnQsICd0cmFuc2l0aW9uZW5kJyk7XG4gICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUoKTtcbiAgICB9LFxuXG4gICAgY2FuY2VsKGVsZW1lbnQpIHtcbiAgICAgICAgdHJpZ2dlcihlbGVtZW50LCAndHJhbnNpdGlvbmNhbmNlbGVkJyk7XG4gICAgfSxcblxuICAgIGluUHJvZ3Jlc3MoZWxlbWVudCkge1xuICAgICAgICByZXR1cm4gaGFzQ2xhc3MoZWxlbWVudCwgJ3VrLXRyYW5zaXRpb24nKTtcbiAgICB9XG5cbn07XG5cbmNvbnN0IGFuaW1hdGlvblByZWZpeCA9ICd1ay1hbmltYXRpb24tJztcbmNvbnN0IGNsc0NhbmNlbEFuaW1hdGlvbiA9ICd1ay1jYW5jZWwtYW5pbWF0aW9uJztcblxuZXhwb3J0IGZ1bmN0aW9uIGFuaW1hdGUoZWxlbWVudCwgYW5pbWF0aW9uLCBkdXJhdGlvbiA9IDIwMCwgb3JpZ2luLCBvdXQpIHtcblxuICAgIHJldHVybiBQcm9taXNlLmFsbCh0b05vZGVzKGVsZW1lbnQpLm1hcChlbGVtZW50ID0+XG4gICAgICAgIG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcblxuICAgICAgICAgICAgaWYgKGhhc0NsYXNzKGVsZW1lbnQsIGNsc0NhbmNlbEFuaW1hdGlvbikpIHtcbiAgICAgICAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT5cbiAgICAgICAgICAgICAgICAgICAgUHJvbWlzZS5yZXNvbHZlKCkudGhlbigoKSA9PlxuICAgICAgICAgICAgICAgICAgICAgICAgYW5pbWF0ZSguLi5hcmd1bWVudHMpLnRoZW4ocmVzb2x2ZSwgcmVqZWN0KVxuICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGxldCBjbHMgPSBgJHthbmltYXRpb259ICR7YW5pbWF0aW9uUHJlZml4fSR7b3V0ID8gJ2xlYXZlJyA6ICdlbnRlcid9YDtcblxuICAgICAgICAgICAgaWYgKHN0YXJ0c1dpdGgoYW5pbWF0aW9uLCBhbmltYXRpb25QcmVmaXgpKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAob3JpZ2luKSB7XG4gICAgICAgICAgICAgICAgICAgIGNscyArPSBgIHVrLXRyYW5zZm9ybS1vcmlnaW4tJHtvcmlnaW59YDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAob3V0KSB7XG4gICAgICAgICAgICAgICAgICAgIGNscyArPSBgICR7YW5pbWF0aW9uUHJlZml4fXJldmVyc2VgO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXNldCgpO1xuXG4gICAgICAgICAgICBvbmNlKGVsZW1lbnQsICdhbmltYXRpb25lbmQgYW5pbWF0aW9uY2FuY2VsJywgKHt0eXBlfSkgPT4ge1xuXG4gICAgICAgICAgICAgICAgbGV0IGhhc1Jlc2V0ID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZSA9PT0gJ2FuaW1hdGlvbmNhbmNlbCcpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVqZWN0KCk7XG4gICAgICAgICAgICAgICAgICAgIHJlc2V0KCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgICAgICBQcm9taXNlLnJlc29sdmUoKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGhhc1Jlc2V0ID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc2V0KCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmICghaGFzUmVzZXQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFkZENsYXNzKGVsZW1lbnQsIGNsc0NhbmNlbEFuaW1hdGlvbik7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiByZW1vdmVDbGFzcyhlbGVtZW50LCBjbHNDYW5jZWxBbmltYXRpb24pKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9LCBmYWxzZSwgKHt0YXJnZXR9KSA9PiBlbGVtZW50ID09PSB0YXJnZXQpO1xuXG4gICAgICAgICAgICBjc3MoZWxlbWVudCwgJ2FuaW1hdGlvbkR1cmF0aW9uJywgYCR7ZHVyYXRpb259bXNgKTtcbiAgICAgICAgICAgIGFkZENsYXNzKGVsZW1lbnQsIGNscyk7XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIHJlc2V0KCkge1xuICAgICAgICAgICAgICAgIGNzcyhlbGVtZW50LCAnYW5pbWF0aW9uRHVyYXRpb24nLCAnJyk7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3NlcyhlbGVtZW50LCBgJHthbmltYXRpb25QcmVmaXh9XFxcXFMqYCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSlcbiAgICApKTtcblxufVxuXG5jb25zdCBpblByb2dyZXNzID0gbmV3IFJlZ0V4cChgJHthbmltYXRpb25QcmVmaXh9KGVudGVyfGxlYXZlKWApO1xuZXhwb3J0IGNvbnN0IEFuaW1hdGlvbiA9IHtcblxuICAgIGluKGVsZW1lbnQsIGFuaW1hdGlvbiwgZHVyYXRpb24sIG9yaWdpbikge1xuICAgICAgICByZXR1cm4gYW5pbWF0ZShlbGVtZW50LCBhbmltYXRpb24sIGR1cmF0aW9uLCBvcmlnaW4sIGZhbHNlKTtcbiAgICB9LFxuXG4gICAgb3V0KGVsZW1lbnQsIGFuaW1hdGlvbiwgZHVyYXRpb24sIG9yaWdpbikge1xuICAgICAgICByZXR1cm4gYW5pbWF0ZShlbGVtZW50LCBhbmltYXRpb24sIGR1cmF0aW9uLCBvcmlnaW4sIHRydWUpO1xuICAgIH0sXG5cbiAgICBpblByb2dyZXNzKGVsZW1lbnQpIHtcbiAgICAgICAgcmV0dXJuIGluUHJvZ3Jlc3MudGVzdChhdHRyKGVsZW1lbnQsICdjbGFzcycpKTtcbiAgICB9LFxuXG4gICAgY2FuY2VsKGVsZW1lbnQpIHtcbiAgICAgICAgdHJpZ2dlcihlbGVtZW50LCAnYW5pbWF0aW9uY2FuY2VsJyk7XG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHtjc3N9IGZyb20gJy4vc3R5bGUnO1xuaW1wb3J0IHthdHRyfSBmcm9tICcuL2F0dHInO1xuaW1wb3J0IHtpc1Zpc2libGV9IGZyb20gJy4vZmlsdGVyJztcbmltcG9ydCB7Y2xhbXAsIGVhY2gsIGVuZHNXaXRoLCBpbmNsdWRlcywgaW50ZXJzZWN0UmVjdCwgaXNEb2N1bWVudCwgaXNOdW1lcmljLCBpc1VuZGVmaW5lZCwgaXNXaW5kb3csIHBvaW50SW5SZWN0LCB0b0Zsb2F0LCB0b05vZGUsIHVjZmlyc3R9IGZyb20gJy4vbGFuZyc7XG5cbmNvbnN0IGRpcnMgPSB7XG4gICAgd2lkdGg6IFsneCcsICdsZWZ0JywgJ3JpZ2h0J10sXG4gICAgaGVpZ2h0OiBbJ3knLCAndG9wJywgJ2JvdHRvbSddXG59O1xuXG5leHBvcnQgZnVuY3Rpb24gcG9zaXRpb25BdChlbGVtZW50LCB0YXJnZXQsIGVsQXR0YWNoLCB0YXJnZXRBdHRhY2gsIGVsT2Zmc2V0LCB0YXJnZXRPZmZzZXQsIGZsaXAsIGJvdW5kYXJ5KSB7XG5cbiAgICBlbEF0dGFjaCA9IGdldFBvcyhlbEF0dGFjaCk7XG4gICAgdGFyZ2V0QXR0YWNoID0gZ2V0UG9zKHRhcmdldEF0dGFjaCk7XG5cbiAgICBjb25zdCBmbGlwcGVkID0ge2VsZW1lbnQ6IGVsQXR0YWNoLCB0YXJnZXQ6IHRhcmdldEF0dGFjaH07XG5cbiAgICBpZiAoIWVsZW1lbnQgfHwgIXRhcmdldCkge1xuICAgICAgICByZXR1cm4gZmxpcHBlZDtcbiAgICB9XG5cbiAgICBjb25zdCBkaW0gPSBnZXREaW1lbnNpb25zKGVsZW1lbnQpO1xuICAgIGNvbnN0IHRhcmdldERpbSA9IGdldERpbWVuc2lvbnModGFyZ2V0KTtcbiAgICBjb25zdCBwb3NpdGlvbiA9IHRhcmdldERpbTtcblxuICAgIG1vdmVUbyhwb3NpdGlvbiwgZWxBdHRhY2gsIGRpbSwgLTEpO1xuICAgIG1vdmVUbyhwb3NpdGlvbiwgdGFyZ2V0QXR0YWNoLCB0YXJnZXREaW0sIDEpO1xuXG4gICAgZWxPZmZzZXQgPSBnZXRPZmZzZXRzKGVsT2Zmc2V0LCBkaW0ud2lkdGgsIGRpbS5oZWlnaHQpO1xuICAgIHRhcmdldE9mZnNldCA9IGdldE9mZnNldHModGFyZ2V0T2Zmc2V0LCB0YXJnZXREaW0ud2lkdGgsIHRhcmdldERpbS5oZWlnaHQpO1xuXG4gICAgZWxPZmZzZXRbJ3gnXSArPSB0YXJnZXRPZmZzZXRbJ3gnXTtcbiAgICBlbE9mZnNldFsneSddICs9IHRhcmdldE9mZnNldFsneSddO1xuXG4gICAgcG9zaXRpb24ubGVmdCArPSBlbE9mZnNldFsneCddO1xuICAgIHBvc2l0aW9uLnRvcCArPSBlbE9mZnNldFsneSddO1xuXG4gICAgaWYgKGZsaXApIHtcblxuICAgICAgICBjb25zdCBib3VuZGFyaWVzID0gW2dldERpbWVuc2lvbnMoZ2V0V2luZG93KGVsZW1lbnQpKV07XG5cbiAgICAgICAgaWYgKGJvdW5kYXJ5KSB7XG4gICAgICAgICAgICBib3VuZGFyaWVzLnVuc2hpZnQoZ2V0RGltZW5zaW9ucyhib3VuZGFyeSkpO1xuICAgICAgICB9XG5cbiAgICAgICAgZWFjaChkaXJzLCAoW2RpciwgYWxpZ24sIGFsaWduRmxpcF0sIHByb3ApID0+IHtcblxuICAgICAgICAgICAgaWYgKCEoZmxpcCA9PT0gdHJ1ZSB8fCBpbmNsdWRlcyhmbGlwLCBkaXIpKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgYm91bmRhcmllcy5zb21lKGJvdW5kYXJ5ID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGVsZW1PZmZzZXQgPSBlbEF0dGFjaFtkaXJdID09PSBhbGlnblxuICAgICAgICAgICAgICAgICAgICA/IC1kaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgOiBlbEF0dGFjaFtkaXJdID09PSBhbGlnbkZsaXBcbiAgICAgICAgICAgICAgICAgICAgICAgID8gZGltW3Byb3BdXG4gICAgICAgICAgICAgICAgICAgICAgICA6IDA7XG5cbiAgICAgICAgICAgICAgICBjb25zdCB0YXJnZXRPZmZzZXQgPSB0YXJnZXRBdHRhY2hbZGlyXSA9PT0gYWxpZ25cbiAgICAgICAgICAgICAgICAgICAgPyB0YXJnZXREaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgOiB0YXJnZXRBdHRhY2hbZGlyXSA9PT0gYWxpZ25GbGlwXG4gICAgICAgICAgICAgICAgICAgICAgICA/IC10YXJnZXREaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgICAgIDogMDtcblxuICAgICAgICAgICAgICAgIGlmIChwb3NpdGlvblthbGlnbl0gPCBib3VuZGFyeVthbGlnbl0gfHwgcG9zaXRpb25bYWxpZ25dICsgZGltW3Byb3BdID4gYm91bmRhcnlbYWxpZ25GbGlwXSkge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlck9mZnNldCA9IGRpbVtwcm9wXSAvIDI7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlclRhcmdldE9mZnNldCA9IHRhcmdldEF0dGFjaFtkaXJdID09PSAnY2VudGVyJyA/IC10YXJnZXREaW1bcHJvcF0gLyAyIDogMDtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZWxBdHRhY2hbZGlyXSA9PT0gJ2NlbnRlcicgJiYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgYXBwbHkoY2VudGVyT2Zmc2V0LCBjZW50ZXJUYXJnZXRPZmZzZXQpXG4gICAgICAgICAgICAgICAgICAgICAgICB8fCBhcHBseSgtY2VudGVyT2Zmc2V0LCAtY2VudGVyVGFyZ2V0T2Zmc2V0KVxuICAgICAgICAgICAgICAgICAgICApIHx8IGFwcGx5KGVsZW1PZmZzZXQsIHRhcmdldE9mZnNldCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBhcHBseShlbGVtT2Zmc2V0LCB0YXJnZXRPZmZzZXQpIHtcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBuZXdWYWwgPSBwb3NpdGlvblthbGlnbl0gKyBlbGVtT2Zmc2V0ICsgdGFyZ2V0T2Zmc2V0IC0gZWxPZmZzZXRbZGlyXSAqIDI7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKG5ld1ZhbCA+PSBib3VuZGFyeVthbGlnbl0gJiYgbmV3VmFsICsgZGltW3Byb3BdIDw9IGJvdW5kYXJ5W2FsaWduRmxpcF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBvc2l0aW9uW2FsaWduXSA9IG5ld1ZhbDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgWydlbGVtZW50JywgJ3RhcmdldCddLmZvckVhY2goZWwgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZsaXBwZWRbZWxdW2Rpcl0gPSAhZWxlbU9mZnNldFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA/IGZsaXBwZWRbZWxdW2Rpcl1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiBmbGlwcGVkW2VsXVtkaXJdID09PSBkaXJzW3Byb3BdWzFdXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA/IGRpcnNbcHJvcF1bMl1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDogZGlyc1twcm9wXVsxXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvZmZzZXQoZWxlbWVudCwgcG9zaXRpb24pO1xuXG4gICAgcmV0dXJuIGZsaXBwZWQ7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvZmZzZXQoZWxlbWVudCwgY29vcmRpbmF0ZXMpIHtcblxuICAgIGVsZW1lbnQgPSB0b05vZGUoZWxlbWVudCk7XG5cbiAgICBpZiAoY29vcmRpbmF0ZXMpIHtcblxuICAgICAgICBjb25zdCBjdXJyZW50T2Zmc2V0ID0gb2Zmc2V0KGVsZW1lbnQpO1xuICAgICAgICBjb25zdCBwb3MgPSBjc3MoZWxlbWVudCwgJ3Bvc2l0aW9uJyk7XG5cbiAgICAgICAgWydsZWZ0JywgJ3RvcCddLmZvckVhY2gocHJvcCA9PiB7XG4gICAgICAgICAgICBpZiAocHJvcCBpbiBjb29yZGluYXRlcykge1xuICAgICAgICAgICAgICAgIGNvbnN0IHZhbHVlID0gY3NzKGVsZW1lbnQsIHByb3ApO1xuICAgICAgICAgICAgICAgIGNzcyhlbGVtZW50LCBwcm9wLCBjb29yZGluYXRlc1twcm9wXSAtIGN1cnJlbnRPZmZzZXRbcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgKyB0b0Zsb2F0KHBvcyA9PT0gJ2Fic29sdXRlJyAmJiB2YWx1ZSA9PT0gJ2F1dG8nXG4gICAgICAgICAgICAgICAgICAgICAgICA/IHBvc2l0aW9uKGVsZW1lbnQpW3Byb3BdXG4gICAgICAgICAgICAgICAgICAgICAgICA6IHZhbHVlKVxuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICByZXR1cm4gZ2V0RGltZW5zaW9ucyhlbGVtZW50KTtcbn1cblxuZnVuY3Rpb24gZ2V0RGltZW5zaW9ucyhlbGVtZW50KSB7XG5cbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuXG4gICAgY29uc3Qge3BhZ2VZT2Zmc2V0OiB0b3AsIHBhZ2VYT2Zmc2V0OiBsZWZ0fSA9IGdldFdpbmRvdyhlbGVtZW50KTtcblxuICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSkge1xuXG4gICAgICAgIGNvbnN0IGhlaWdodCA9IGVsZW1lbnQuaW5uZXJIZWlnaHQ7XG4gICAgICAgIGNvbnN0IHdpZHRoID0gZWxlbWVudC5pbm5lcldpZHRoO1xuXG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICB0b3AsXG4gICAgICAgICAgICBsZWZ0LFxuICAgICAgICAgICAgaGVpZ2h0LFxuICAgICAgICAgICAgd2lkdGgsXG4gICAgICAgICAgICBib3R0b206IHRvcCArIGhlaWdodCxcbiAgICAgICAgICAgIHJpZ2h0OiBsZWZ0ICsgd2lkdGhcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICBsZXQgc3R5bGUsIGhpZGRlbjtcblxuICAgIGlmICghaXNWaXNpYmxlKGVsZW1lbnQpICYmIGNzcyhlbGVtZW50LCAnZGlzcGxheScpID09PSAnbm9uZScpIHtcblxuICAgICAgICBzdHlsZSA9IGF0dHIoZWxlbWVudCwgJ3N0eWxlJyk7XG4gICAgICAgIGhpZGRlbiA9IGF0dHIoZWxlbWVudCwgJ2hpZGRlbicpO1xuXG4gICAgICAgIGF0dHIoZWxlbWVudCwge1xuICAgICAgICAgICAgc3R5bGU6IGAke3N0eWxlIHx8ICcnfTtkaXNwbGF5OmJsb2NrICFpbXBvcnRhbnQ7YCxcbiAgICAgICAgICAgIGhpZGRlbjogbnVsbFxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBjb25zdCByZWN0ID0gZWxlbWVudC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcblxuICAgIGlmICghaXNVbmRlZmluZWQoc3R5bGUpKSB7XG4gICAgICAgIGF0dHIoZWxlbWVudCwge3N0eWxlLCBoaWRkZW59KTtcbiAgICB9XG5cbiAgICByZXR1cm4ge1xuICAgICAgICBoZWlnaHQ6IHJlY3QuaGVpZ2h0LFxuICAgICAgICB3aWR0aDogcmVjdC53aWR0aCxcbiAgICAgICAgdG9wOiByZWN0LnRvcCArIHRvcCxcbiAgICAgICAgbGVmdDogcmVjdC5sZWZ0ICsgbGVmdCxcbiAgICAgICAgYm90dG9tOiByZWN0LmJvdHRvbSArIHRvcCxcbiAgICAgICAgcmlnaHQ6IHJlY3QucmlnaHQgKyBsZWZ0XG4gICAgfTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHBvc2l0aW9uKGVsZW1lbnQpIHtcbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuXG4gICAgY29uc3QgcGFyZW50ID0gZWxlbWVudC5vZmZzZXRQYXJlbnQgfHwgZ2V0RG9jRWwoZWxlbWVudCk7XG4gICAgY29uc3QgcGFyZW50T2Zmc2V0ID0gb2Zmc2V0KHBhcmVudCk7XG4gICAgY29uc3Qge3RvcCwgbGVmdH0gPSBbJ3RvcCcsICdsZWZ0J10ucmVkdWNlKChwcm9wcywgcHJvcCkgPT4ge1xuICAgICAgICBjb25zdCBwcm9wTmFtZSA9IHVjZmlyc3QocHJvcCk7XG4gICAgICAgIHByb3BzW3Byb3BdIC09IHBhcmVudE9mZnNldFtwcm9wXVxuICAgICAgICAgICAgKyB0b0Zsb2F0KGNzcyhlbGVtZW50LCBgbWFyZ2luJHtwcm9wTmFtZX1gKSlcbiAgICAgICAgICAgICsgdG9GbG9hdChjc3MocGFyZW50LCBgYm9yZGVyJHtwcm9wTmFtZX1XaWR0aGApKTtcbiAgICAgICAgcmV0dXJuIHByb3BzO1xuICAgIH0sIG9mZnNldChlbGVtZW50KSk7XG5cbiAgICByZXR1cm4ge3RvcCwgbGVmdH07XG59XG5cbmV4cG9ydCBjb25zdCBoZWlnaHQgPSBkaW1lbnNpb24oJ2hlaWdodCcpO1xuZXhwb3J0IGNvbnN0IHdpZHRoID0gZGltZW5zaW9uKCd3aWR0aCcpO1xuXG5mdW5jdGlvbiBkaW1lbnNpb24ocHJvcCkge1xuICAgIGNvbnN0IHByb3BOYW1lID0gdWNmaXJzdChwcm9wKTtcbiAgICByZXR1cm4gKGVsZW1lbnQsIHZhbHVlKSA9PiB7XG5cbiAgICAgICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgICAgICBpZiAoaXNVbmRlZmluZWQodmFsdWUpKSB7XG5cbiAgICAgICAgICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBlbGVtZW50W2Bpbm5lciR7cHJvcE5hbWV9YF07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChpc0RvY3VtZW50KGVsZW1lbnQpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZG9jID0gZWxlbWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gICAgICAgICAgICAgICAgcmV0dXJuIE1hdGgubWF4KGRvY1tgb2Zmc2V0JHtwcm9wTmFtZX1gXSwgZG9jW2BzY3JvbGwke3Byb3BOYW1lfWBdKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFsdWUgPSBjc3MoZWxlbWVudCwgcHJvcCk7XG4gICAgICAgICAgICB2YWx1ZSA9IHZhbHVlID09PSAnYXV0bycgPyBlbGVtZW50W2BvZmZzZXQke3Byb3BOYW1lfWBdIDogdG9GbG9hdCh2YWx1ZSkgfHwgMDtcblxuICAgICAgICAgICAgcmV0dXJuIHZhbHVlIC0gYm94TW9kZWxBZGp1c3QocHJvcCwgZWxlbWVudCk7XG5cbiAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIHByb3AsICF2YWx1ZSAmJiB2YWx1ZSAhPT0gMFxuICAgICAgICAgICAgICAgID8gJydcbiAgICAgICAgICAgICAgICA6ICt2YWx1ZSArIGJveE1vZGVsQWRqdXN0KHByb3AsIGVsZW1lbnQpICsgJ3B4J1xuICAgICAgICAgICAgKTtcblxuICAgICAgICB9XG5cbiAgICB9O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYm94TW9kZWxBZGp1c3QocHJvcCwgZWxlbWVudCwgc2l6aW5nID0gJ2JvcmRlci1ib3gnKSB7XG4gICAgcmV0dXJuIGNzcyhlbGVtZW50LCAnYm94U2l6aW5nJykgPT09IHNpemluZ1xuICAgICAgICA/IGRpcnNbcHJvcF0uc2xpY2UoMSkubWFwKHVjZmlyc3QpLnJlZHVjZSgodmFsdWUsIHByb3ApID0+XG4gICAgICAgICAgICB2YWx1ZVxuICAgICAgICAgICAgKyB0b0Zsb2F0KGNzcyhlbGVtZW50LCBgcGFkZGluZyR7cHJvcH1gKSlcbiAgICAgICAgICAgICsgdG9GbG9hdChjc3MoZWxlbWVudCwgYGJvcmRlciR7cHJvcH1XaWR0aGApKVxuICAgICAgICAgICAgLCAwKVxuICAgICAgICA6IDA7XG59XG5cbmZ1bmN0aW9uIG1vdmVUbyhwb3NpdGlvbiwgYXR0YWNoLCBkaW0sIGZhY3Rvcikge1xuICAgIGVhY2goZGlycywgKFtkaXIsIGFsaWduLCBhbGlnbkZsaXBdLCBwcm9wKSA9PiB7XG4gICAgICAgIGlmIChhdHRhY2hbZGlyXSA9PT0gYWxpZ25GbGlwKSB7XG4gICAgICAgICAgICBwb3NpdGlvblthbGlnbl0gKz0gZGltW3Byb3BdICogZmFjdG9yO1xuICAgICAgICB9IGVsc2UgaWYgKGF0dGFjaFtkaXJdID09PSAnY2VudGVyJykge1xuICAgICAgICAgICAgcG9zaXRpb25bYWxpZ25dICs9IGRpbVtwcm9wXSAqIGZhY3RvciAvIDI7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gZ2V0UG9zKHBvcykge1xuXG4gICAgY29uc3QgeCA9IC9sZWZ0fGNlbnRlcnxyaWdodC87XG4gICAgY29uc3QgeSA9IC90b3B8Y2VudGVyfGJvdHRvbS87XG5cbiAgICBwb3MgPSAocG9zIHx8ICcnKS5zcGxpdCgnICcpO1xuXG4gICAgaWYgKHBvcy5sZW5ndGggPT09IDEpIHtcbiAgICAgICAgcG9zID0geC50ZXN0KHBvc1swXSlcbiAgICAgICAgICAgID8gcG9zLmNvbmNhdChbJ2NlbnRlciddKVxuICAgICAgICAgICAgOiB5LnRlc3QocG9zWzBdKVxuICAgICAgICAgICAgICAgID8gWydjZW50ZXInXS5jb25jYXQocG9zKVxuICAgICAgICAgICAgICAgIDogWydjZW50ZXInLCAnY2VudGVyJ107XG4gICAgfVxuXG4gICAgcmV0dXJuIHtcbiAgICAgICAgeDogeC50ZXN0KHBvc1swXSkgPyBwb3NbMF0gOiAnY2VudGVyJyxcbiAgICAgICAgeTogeS50ZXN0KHBvc1sxXSkgPyBwb3NbMV0gOiAnY2VudGVyJ1xuICAgIH07XG59XG5cbmZ1bmN0aW9uIGdldE9mZnNldHMob2Zmc2V0cywgd2lkdGgsIGhlaWdodCkge1xuXG4gICAgY29uc3QgW3gsIHldID0gKG9mZnNldHMgfHwgJycpLnNwbGl0KCcgJyk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgICB4OiB4ID8gdG9GbG9hdCh4KSAqIChlbmRzV2l0aCh4LCAnJScpID8gd2lkdGggLyAxMDAgOiAxKSA6IDAsXG4gICAgICAgIHk6IHkgPyB0b0Zsb2F0KHkpICogKGVuZHNXaXRoKHksICclJykgPyBoZWlnaHQgLyAxMDAgOiAxKSA6IDBcbiAgICB9O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZmxpcFBvc2l0aW9uKHBvcykge1xuICAgIHN3aXRjaCAocG9zKSB7XG4gICAgICAgIGNhc2UgJ2xlZnQnOlxuICAgICAgICAgICAgcmV0dXJuICdyaWdodCc7XG4gICAgICAgIGNhc2UgJ3JpZ2h0JzpcbiAgICAgICAgICAgIHJldHVybiAnbGVmdCc7XG4gICAgICAgIGNhc2UgJ3RvcCc6XG4gICAgICAgICAgICByZXR1cm4gJ2JvdHRvbSc7XG4gICAgICAgIGNhc2UgJ2JvdHRvbSc6XG4gICAgICAgICAgICByZXR1cm4gJ3RvcCc7XG4gICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICByZXR1cm4gcG9zO1xuICAgIH1cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzSW5WaWV3KGVsZW1lbnQsIHRvcE9mZnNldCA9IDAsIGxlZnRPZmZzZXQgPSAwKSB7XG5cbiAgICBpZiAoIWlzVmlzaWJsZShlbGVtZW50KSkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgIGNvbnN0IHdpbiA9IGdldFdpbmRvdyhlbGVtZW50KTtcbiAgICBjb25zdCBjbGllbnQgPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgIGNvbnN0IGJvdW5kaW5nID0ge1xuICAgICAgICB0b3A6IC10b3BPZmZzZXQsXG4gICAgICAgIGxlZnQ6IC1sZWZ0T2Zmc2V0LFxuICAgICAgICBib3R0b206IHRvcE9mZnNldCArIGhlaWdodCh3aW4pLFxuICAgICAgICByaWdodDogbGVmdE9mZnNldCArIHdpZHRoKHdpbilcbiAgICB9O1xuXG4gICAgcmV0dXJuIGludGVyc2VjdFJlY3QoY2xpZW50LCBib3VuZGluZykgfHwgcG9pbnRJblJlY3Qoe3g6IGNsaWVudC5sZWZ0LCB5OiBjbGllbnQudG9wfSwgYm91bmRpbmcpO1xuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBzY3JvbGxlZE92ZXIoZWxlbWVudCwgaGVpZ2h0T2Zmc2V0ID0gMCkge1xuXG4gICAgaWYgKCFpc1Zpc2libGUoZWxlbWVudCkpIHtcbiAgICAgICAgcmV0dXJuIDA7XG4gICAgfVxuXG4gICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgIGNvbnN0IHdpbiA9IGdldFdpbmRvdyhlbGVtZW50KTtcbiAgICBjb25zdCBkb2MgPSBnZXREb2N1bWVudChlbGVtZW50KTtcbiAgICBjb25zdCBlbEhlaWdodCA9IGVsZW1lbnQub2Zmc2V0SGVpZ2h0ICsgaGVpZ2h0T2Zmc2V0O1xuICAgIGNvbnN0IFt0b3BdID0gb2Zmc2V0UG9zaXRpb24oZWxlbWVudCk7XG4gICAgY29uc3QgdnAgPSBoZWlnaHQod2luKTtcbiAgICBjb25zdCB2aCA9IHZwICsgTWF0aC5taW4oMCwgdG9wIC0gdnApO1xuICAgIGNvbnN0IGRpZmYgPSBNYXRoLm1heCgwLCB2cCAtIChoZWlnaHQoZG9jKSArIGhlaWdodE9mZnNldCAtICh0b3AgKyBlbEhlaWdodCkpKTtcblxuICAgIHJldHVybiBjbGFtcCgoKHZoICsgd2luLnBhZ2VZT2Zmc2V0IC0gdG9wKSAvICgodmggKyAoZWxIZWlnaHQgLSAoZGlmZiA8IHZwID8gZGlmZiA6IDApKSkgLyAxMDApKSAvIDEwMCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBzY3JvbGxUb3AoZWxlbWVudCwgdG9wKSB7XG4gICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSB8fCBpc0RvY3VtZW50KGVsZW1lbnQpKSB7XG4gICAgICAgIGNvbnN0IHtzY3JvbGxUbywgcGFnZVhPZmZzZXR9ID0gZ2V0V2luZG93KGVsZW1lbnQpO1xuICAgICAgICBzY3JvbGxUbyhwYWdlWE9mZnNldCwgdG9wKTtcbiAgICB9IGVsc2Uge1xuICAgICAgICBlbGVtZW50LnNjcm9sbFRvcCA9IHRvcDtcbiAgICB9XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvZmZzZXRQb3NpdGlvbihlbGVtZW50KSB7XG4gICAgY29uc3Qgb2Zmc2V0ID0gWzAsIDBdO1xuXG4gICAgZG8ge1xuXG4gICAgICAgIG9mZnNldFswXSArPSBlbGVtZW50Lm9mZnNldFRvcDtcbiAgICAgICAgb2Zmc2V0WzFdICs9IGVsZW1lbnQub2Zmc2V0TGVmdDtcblxuICAgICAgICBpZiAoY3NzKGVsZW1lbnQsICdwb3NpdGlvbicpID09PSAnZml4ZWQnKSB7XG4gICAgICAgICAgICBjb25zdCB3aW4gPSBnZXRXaW5kb3coZWxlbWVudCk7XG4gICAgICAgICAgICBvZmZzZXRbMF0gKz0gd2luLnBhZ2VZT2Zmc2V0O1xuICAgICAgICAgICAgb2Zmc2V0WzFdICs9IHdpbi5wYWdlWE9mZnNldDtcbiAgICAgICAgICAgIHJldHVybiBvZmZzZXQ7XG4gICAgICAgIH1cblxuICAgIH0gd2hpbGUgKChlbGVtZW50ID0gZWxlbWVudC5vZmZzZXRQYXJlbnQpKTtcblxuICAgIHJldHVybiBvZmZzZXQ7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB0b1B4KHZhbHVlLCBwcm9wZXJ0eSA9ICd3aWR0aCcsIGVsZW1lbnQgPSB3aW5kb3cpIHtcbiAgICByZXR1cm4gaXNOdW1lcmljKHZhbHVlKVxuICAgICAgICA/ICt2YWx1ZVxuICAgICAgICA6IGVuZHNXaXRoKHZhbHVlLCAndmgnKVxuICAgICAgICAgICAgPyBwZXJjZW50KGhlaWdodChnZXRXaW5kb3coZWxlbWVudCkpLCB2YWx1ZSlcbiAgICAgICAgICAgIDogZW5kc1dpdGgodmFsdWUsICd2dycpXG4gICAgICAgICAgICAgICAgPyBwZXJjZW50KHdpZHRoKGdldFdpbmRvdyhlbGVtZW50KSksIHZhbHVlKVxuICAgICAgICAgICAgICAgIDogZW5kc1dpdGgodmFsdWUsICclJylcbiAgICAgICAgICAgICAgICAgICAgPyBwZXJjZW50KGdldERpbWVuc2lvbnMoZWxlbWVudClbcHJvcGVydHldLCB2YWx1ZSlcbiAgICAgICAgICAgICAgICAgICAgOiB0b0Zsb2F0KHZhbHVlKTtcbn1cblxuZnVuY3Rpb24gcGVyY2VudChiYXNlLCB2YWx1ZSkge1xuICAgIHJldHVybiBiYXNlICogdG9GbG9hdCh2YWx1ZSkgLyAxMDA7XG59XG5cbmZ1bmN0aW9uIGdldFdpbmRvdyhlbGVtZW50KSB7XG4gICAgcmV0dXJuIGlzV2luZG93KGVsZW1lbnQpID8gZWxlbWVudCA6IGdldERvY3VtZW50KGVsZW1lbnQpLmRlZmF1bHRWaWV3O1xufVxuXG5mdW5jdGlvbiBnZXREb2N1bWVudChlbGVtZW50KSB7XG4gICAgcmV0dXJuIHRvTm9kZShlbGVtZW50KS5vd25lckRvY3VtZW50O1xufVxuXG5mdW5jdGlvbiBnZXREb2NFbChlbGVtZW50KSB7XG4gICAgcmV0dXJuIGdldERvY3VtZW50KGVsZW1lbnQpLmRvY3VtZW50RWxlbWVudDtcbn1cbiIsIi8qXG4gICAgQmFzZWQgb246XG4gICAgQ29weXJpZ2h0IChjKSAyMDE2IFdpbHNvbiBQYWdlIHdpbHNvbnBhZ2VAbWUuY29tXG4gICAgaHR0cHM6Ly9naXRodWIuY29tL3dpbHNvbnBhZ2UvZmFzdGRvbVxuKi9cblxuZXhwb3J0IGNvbnN0IGZhc3Rkb20gPSB7XG5cbiAgICByZWFkczogW10sXG4gICAgd3JpdGVzOiBbXSxcblxuICAgIHJlYWQodGFzaykge1xuICAgICAgICB0aGlzLnJlYWRzLnB1c2godGFzayk7XG4gICAgICAgIHNjaGVkdWxlRmx1c2goKTtcbiAgICAgICAgcmV0dXJuIHRhc2s7XG4gICAgfSxcblxuICAgIHdyaXRlKHRhc2spIHtcbiAgICAgICAgdGhpcy53cml0ZXMucHVzaCh0YXNrKTtcbiAgICAgICAgc2NoZWR1bGVGbHVzaCgpO1xuICAgICAgICByZXR1cm4gdGFzaztcbiAgICB9LFxuXG4gICAgY2xlYXIodGFzaykge1xuICAgICAgICByZXR1cm4gcmVtb3ZlKHRoaXMucmVhZHMsIHRhc2spIHx8IHJlbW92ZSh0aGlzLndyaXRlcywgdGFzayk7XG4gICAgfSxcblxuICAgIGZsdXNoKCkge1xuXG4gICAgICAgIHJ1blRhc2tzKHRoaXMucmVhZHMpO1xuICAgICAgICBydW5UYXNrcyh0aGlzLndyaXRlcy5zcGxpY2UoMCwgdGhpcy53cml0ZXMubGVuZ3RoKSk7XG5cbiAgICAgICAgdGhpcy5zY2hlZHVsZWQgPSBmYWxzZTtcblxuICAgICAgICBpZiAodGhpcy5yZWFkcy5sZW5ndGggfHwgdGhpcy53cml0ZXMubGVuZ3RoKSB7XG4gICAgICAgICAgICBzY2hlZHVsZUZsdXNoKCk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZnVuY3Rpb24gc2NoZWR1bGVGbHVzaCgpIHtcbiAgICBpZiAoIWZhc3Rkb20uc2NoZWR1bGVkKSB7XG4gICAgICAgIGZhc3Rkb20uc2NoZWR1bGVkID0gdHJ1ZTtcbiAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZhc3Rkb20uZmx1c2guYmluZChmYXN0ZG9tKSk7XG4gICAgfVxufVxuXG5mdW5jdGlvbiBydW5UYXNrcyh0YXNrcykge1xuICAgIGxldCB0YXNrO1xuICAgIHdoaWxlICgodGFzayA9IHRhc2tzLnNoaWZ0KCkpKSB7XG4gICAgICAgIHRhc2soKTtcbiAgICB9XG59XG5cbmZ1bmN0aW9uIHJlbW92ZShhcnJheSwgaXRlbSkge1xuICAgIGNvbnN0IGluZGV4ID0gYXJyYXkuaW5kZXhPZihpdGVtKTtcbiAgICByZXR1cm4gISF+aW5kZXggJiYgISFhcnJheS5zcGxpY2UoaW5kZXgsIDEpO1xufVxuIiwiaW1wb3J0IHtvbn0gZnJvbSAnLi9ldmVudCc7XG5pbXBvcnQge29mZnNldH0gZnJvbSAnLi9kaW1lbnNpb25zJztcblxuZXhwb3J0IGZ1bmN0aW9uIE1vdXNlVHJhY2tlcigpIHt9XG5cbk1vdXNlVHJhY2tlci5wcm90b3R5cGUgPSB7XG5cbiAgICBwb3NpdGlvbnM6IFtdLFxuICAgIHBvc2l0aW9uOiBudWxsLFxuXG4gICAgaW5pdCgpIHtcblxuICAgICAgICB0aGlzLnBvc2l0aW9ucyA9IFtdO1xuICAgICAgICB0aGlzLnBvc2l0aW9uID0gbnVsbDtcblxuICAgICAgICBsZXQgdGlja2luZyA9IGZhbHNlO1xuICAgICAgICB0aGlzLnVuYmluZCA9IG9uKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgZSA9PiB7XG5cbiAgICAgICAgICAgIGlmICh0aWNraW5nKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHRpbWUgPSBEYXRlLm5vdygpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHtsZW5ndGh9ID0gdGhpcy5wb3NpdGlvbnM7XG5cbiAgICAgICAgICAgICAgICBpZiAobGVuZ3RoICYmICh0aW1lIC0gdGhpcy5wb3NpdGlvbnNbbGVuZ3RoIC0gMV0udGltZSA+IDEwMCkpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wb3NpdGlvbnMuc3BsaWNlKDAsIGxlbmd0aCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy5wb3NpdGlvbnMucHVzaCh7dGltZSwgeDogZS5wYWdlWCwgeTogZS5wYWdlWX0pO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMucG9zaXRpb25zLmxlbmd0aCA+IDUpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wb3NpdGlvbnMuc2hpZnQoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aWNraW5nID0gZmFsc2U7XG4gICAgICAgICAgICB9LCA1KTtcblxuICAgICAgICAgICAgdGlja2luZyA9IHRydWU7XG4gICAgICAgIH0pO1xuXG4gICAgfSxcblxuICAgIGNhbmNlbCgpIHtcbiAgICAgICAgaWYgKHRoaXMudW5iaW5kKSB7XG4gICAgICAgICAgICB0aGlzLnVuYmluZCgpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIG1vdmVzVG8odGFyZ2V0KSB7XG5cbiAgICAgICAgaWYgKHRoaXMucG9zaXRpb25zLmxlbmd0aCA8IDIpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHAgPSBvZmZzZXQodGFyZ2V0KTtcbiAgICAgICAgY29uc3QgcG9zaXRpb24gPSB0aGlzLnBvc2l0aW9uc1t0aGlzLnBvc2l0aW9ucy5sZW5ndGggLSAxXTtcbiAgICAgICAgY29uc3QgW3ByZXZQb3NdID0gdGhpcy5wb3NpdGlvbnM7XG5cbiAgICAgICAgaWYgKHAubGVmdCA8PSBwb3NpdGlvbi54ICYmIHBvc2l0aW9uLnggPD0gcC5yaWdodCAmJiBwLnRvcCA8PSBwb3NpdGlvbi55ICYmIHBvc2l0aW9uLnkgPD0gcC5ib3R0b20pIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHBvaW50cyA9IFtcbiAgICAgICAgICAgIFt7eDogcC5sZWZ0LCB5OiBwLnRvcH0sIHt4OiBwLnJpZ2h0LCB5OiBwLmJvdHRvbX1dLFxuICAgICAgICAgICAgW3t4OiBwLnJpZ2h0LCB5OiBwLnRvcH0sIHt4OiBwLmxlZnQsIHk6IHAuYm90dG9tfV1cbiAgICAgICAgXTtcblxuICAgICAgICBpZiAocC5yaWdodCA8PSBwb3NpdGlvbi54KSB7XG4gICAgICAgICAgICAvLyBlbXB0eVxuICAgICAgICB9IGVsc2UgaWYgKHAubGVmdCA+PSBwb3NpdGlvbi54KSB7XG4gICAgICAgICAgICBwb2ludHNbMF0ucmV2ZXJzZSgpO1xuICAgICAgICAgICAgcG9pbnRzWzFdLnJldmVyc2UoKTtcbiAgICAgICAgfSBlbHNlIGlmIChwLmJvdHRvbSA8PSBwb3NpdGlvbi55KSB7XG4gICAgICAgICAgICBwb2ludHNbMF0ucmV2ZXJzZSgpO1xuICAgICAgICB9IGVsc2UgaWYgKHAudG9wID49IHBvc2l0aW9uLnkpIHtcbiAgICAgICAgICAgIHBvaW50c1sxXS5yZXZlcnNlKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gISFwb2ludHMucmVkdWNlKChyZXN1bHQsIHBvaW50KSA9PiB7XG4gICAgICAgICAgICByZXR1cm4gcmVzdWx0ICsgKHNsb3BlKHByZXZQb3MsIHBvaW50WzBdKSA8IHNsb3BlKHBvc2l0aW9uLCBwb2ludFswXSkgJiYgc2xvcGUocHJldlBvcywgcG9pbnRbMV0pID4gc2xvcGUocG9zaXRpb24sIHBvaW50WzFdKSk7XG4gICAgICAgIH0sIDApO1xuICAgIH1cblxufTtcblxuZnVuY3Rpb24gc2xvcGUoYSwgYikge1xuICAgIHJldHVybiAoYi55IC0gYS55KSAvIChiLnggLSBhLngpO1xufVxuIiwiaW1wb3J0IHthc3NpZ24sIGhhc093biwgaW5jbHVkZXMsIGlzQXJyYXksIGlzRnVuY3Rpb24sIGlzVW5kZWZpbmVkLCBzb3J0QnksIHN0YXJ0c1dpdGh9IGZyb20gJy4vbGFuZyc7XG5cbmNvbnN0IHN0cmF0cyA9IHt9O1xuXG5zdHJhdHMuZXZlbnRzID1cbnN0cmF0cy5jcmVhdGVkID1cbnN0cmF0cy5iZWZvcmVDb25uZWN0ID1cbnN0cmF0cy5jb25uZWN0ZWQgPVxuc3RyYXRzLmJlZm9yZURpc2Nvbm5lY3QgPVxuc3RyYXRzLmRpc2Nvbm5lY3RlZCA9XG5zdHJhdHMuZGVzdHJveSA9IGNvbmNhdFN0cmF0O1xuXG4vLyBhcmdzIHN0cmF0ZWd5XG5zdHJhdHMuYXJncyA9IGZ1bmN0aW9uIChwYXJlbnRWYWwsIGNoaWxkVmFsKSB7XG4gICAgcmV0dXJuIGNoaWxkVmFsICE9PSBmYWxzZSAmJiBjb25jYXRTdHJhdChjaGlsZFZhbCB8fCBwYXJlbnRWYWwpO1xufTtcblxuLy8gdXBkYXRlIHN0cmF0ZWd5XG5zdHJhdHMudXBkYXRlID0gZnVuY3Rpb24gKHBhcmVudFZhbCwgY2hpbGRWYWwpIHtcbiAgICByZXR1cm4gc29ydEJ5KGNvbmNhdFN0cmF0KHBhcmVudFZhbCwgaXNGdW5jdGlvbihjaGlsZFZhbCkgPyB7cmVhZDogY2hpbGRWYWx9IDogY2hpbGRWYWwpLCAnb3JkZXInKTtcbn07XG5cbi8vIHByb3BlcnR5IHN0cmF0ZWd5XG5zdHJhdHMucHJvcHMgPSBmdW5jdGlvbiAocGFyZW50VmFsLCBjaGlsZFZhbCkge1xuXG4gICAgaWYgKGlzQXJyYXkoY2hpbGRWYWwpKSB7XG4gICAgICAgIGNoaWxkVmFsID0gY2hpbGRWYWwucmVkdWNlKCh2YWx1ZSwga2V5KSA9PiB7XG4gICAgICAgICAgICB2YWx1ZVtrZXldID0gU3RyaW5nO1xuICAgICAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgICB9LCB7fSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHN0cmF0cy5tZXRob2RzKHBhcmVudFZhbCwgY2hpbGRWYWwpO1xufTtcblxuLy8gZXh0ZW5kIHN0cmF0ZWd5XG5zdHJhdHMuY29tcHV0ZWQgPVxuc3RyYXRzLm1ldGhvZHMgPSBmdW5jdGlvbiAocGFyZW50VmFsLCBjaGlsZFZhbCkge1xuICAgIHJldHVybiBjaGlsZFZhbFxuICAgICAgICA/IHBhcmVudFZhbFxuICAgICAgICAgICAgPyBhc3NpZ24oe30sIHBhcmVudFZhbCwgY2hpbGRWYWwpXG4gICAgICAgICAgICA6IGNoaWxkVmFsXG4gICAgICAgIDogcGFyZW50VmFsO1xufTtcblxuLy8gZGF0YSBzdHJhdGVneVxuc3RyYXRzLmRhdGEgPSBmdW5jdGlvbiAocGFyZW50VmFsLCBjaGlsZFZhbCwgdm0pIHtcblxuICAgIGlmICghdm0pIHtcblxuICAgICAgICBpZiAoIWNoaWxkVmFsKSB7XG4gICAgICAgICAgICByZXR1cm4gcGFyZW50VmFsO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKCFwYXJlbnRWYWwpIHtcbiAgICAgICAgICAgIHJldHVybiBjaGlsZFZhbDtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBmdW5jdGlvbiAodm0pIHtcbiAgICAgICAgICAgIHJldHVybiBtZXJnZUZuRGF0YShwYXJlbnRWYWwsIGNoaWxkVmFsLCB2bSk7XG4gICAgICAgIH07XG5cbiAgICB9XG5cbiAgICByZXR1cm4gbWVyZ2VGbkRhdGEocGFyZW50VmFsLCBjaGlsZFZhbCwgdm0pO1xufTtcblxuZnVuY3Rpb24gbWVyZ2VGbkRhdGEocGFyZW50VmFsLCBjaGlsZFZhbCwgdm0pIHtcbiAgICByZXR1cm4gc3RyYXRzLmNvbXB1dGVkKFxuICAgICAgICBpc0Z1bmN0aW9uKHBhcmVudFZhbClcbiAgICAgICAgICAgID8gcGFyZW50VmFsLmNhbGwodm0sIHZtKVxuICAgICAgICAgICAgOiBwYXJlbnRWYWwsXG4gICAgICAgIGlzRnVuY3Rpb24oY2hpbGRWYWwpXG4gICAgICAgICAgICA/IGNoaWxkVmFsLmNhbGwodm0sIHZtKVxuICAgICAgICAgICAgOiBjaGlsZFZhbFxuICAgICk7XG59XG5cbi8vIGNvbmNhdCBzdHJhdGVneVxuZnVuY3Rpb24gY29uY2F0U3RyYXQocGFyZW50VmFsLCBjaGlsZFZhbCkge1xuXG4gICAgcGFyZW50VmFsID0gcGFyZW50VmFsICYmICFpc0FycmF5KHBhcmVudFZhbCkgPyBbcGFyZW50VmFsXSA6IHBhcmVudFZhbDtcblxuICAgIHJldHVybiBjaGlsZFZhbFxuICAgICAgICA/IHBhcmVudFZhbFxuICAgICAgICAgICAgPyBwYXJlbnRWYWwuY29uY2F0KGNoaWxkVmFsKVxuICAgICAgICAgICAgOiBpc0FycmF5KGNoaWxkVmFsKVxuICAgICAgICAgICAgICAgID8gY2hpbGRWYWxcbiAgICAgICAgICAgICAgICA6IFtjaGlsZFZhbF1cbiAgICAgICAgOiBwYXJlbnRWYWw7XG59XG5cbi8vIGRlZmF1bHQgc3RyYXRlZ3lcbmZ1bmN0aW9uIGRlZmF1bHRTdHJhdChwYXJlbnRWYWwsIGNoaWxkVmFsKSB7XG4gICAgcmV0dXJuIGlzVW5kZWZpbmVkKGNoaWxkVmFsKSA/IHBhcmVudFZhbCA6IGNoaWxkVmFsO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gbWVyZ2VPcHRpb25zKHBhcmVudCwgY2hpbGQsIHZtKSB7XG5cbiAgICBjb25zdCBvcHRpb25zID0ge307XG5cbiAgICBpZiAoaXNGdW5jdGlvbihjaGlsZCkpIHtcbiAgICAgICAgY2hpbGQgPSBjaGlsZC5vcHRpb25zO1xuICAgIH1cblxuICAgIGlmIChjaGlsZC5leHRlbmRzKSB7XG4gICAgICAgIHBhcmVudCA9IG1lcmdlT3B0aW9ucyhwYXJlbnQsIGNoaWxkLmV4dGVuZHMsIHZtKTtcbiAgICB9XG5cbiAgICBpZiAoY2hpbGQubWl4aW5zKSB7XG4gICAgICAgIGZvciAobGV0IGkgPSAwLCBsID0gY2hpbGQubWl4aW5zLmxlbmd0aDsgaSA8IGw7IGkrKykge1xuICAgICAgICAgICAgcGFyZW50ID0gbWVyZ2VPcHRpb25zKHBhcmVudCwgY2hpbGQubWl4aW5zW2ldLCB2bSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBwYXJlbnQpIHtcbiAgICAgICAgbWVyZ2VLZXkoa2V5KTtcbiAgICB9XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjaGlsZCkge1xuICAgICAgICBpZiAoIWhhc093bihwYXJlbnQsIGtleSkpIHtcbiAgICAgICAgICAgIG1lcmdlS2V5KGtleSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBtZXJnZUtleShrZXkpIHtcbiAgICAgICAgb3B0aW9uc1trZXldID0gKHN0cmF0c1trZXldIHx8IGRlZmF1bHRTdHJhdCkocGFyZW50W2tleV0sIGNoaWxkW2tleV0sIHZtKTtcbiAgICB9XG5cbiAgICByZXR1cm4gb3B0aW9ucztcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHBhcnNlT3B0aW9ucyhvcHRpb25zLCBhcmdzID0gW10pIHtcblxuICAgIHRyeSB7XG5cbiAgICAgICAgcmV0dXJuICFvcHRpb25zXG4gICAgICAgICAgICA/IHt9XG4gICAgICAgICAgICA6IHN0YXJ0c1dpdGgob3B0aW9ucywgJ3snKVxuICAgICAgICAgICAgICAgID8gSlNPTi5wYXJzZShvcHRpb25zKVxuICAgICAgICAgICAgICAgIDogYXJncy5sZW5ndGggJiYgIWluY2x1ZGVzKG9wdGlvbnMsICc6JylcbiAgICAgICAgICAgICAgICAgICAgPyAoe1thcmdzWzBdXTogb3B0aW9uc30pXG4gICAgICAgICAgICAgICAgICAgIDogb3B0aW9ucy5zcGxpdCgnOycpLnJlZHVjZSgob3B0aW9ucywgb3B0aW9uKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBba2V5LCB2YWx1ZV0gPSBvcHRpb24uc3BsaXQoLzooLiopLyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoa2V5ICYmICFpc1VuZGVmaW5lZCh2YWx1ZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvcHRpb25zW2tleS50cmltKCldID0gdmFsdWUudHJpbSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG9wdGlvbnM7XG4gICAgICAgICAgICAgICAgICAgIH0sIHt9KTtcblxuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgcmV0dXJuIHt9O1xuICAgIH1cblxufVxuIiwiaW1wb3J0IHthdHRyfSBmcm9tICcuL2F0dHInO1xuaW1wb3J0IHtvbmNlfSBmcm9tICcuL2V2ZW50JztcbmltcG9ydCB7UHJvbWlzZX0gZnJvbSAnLi9wcm9taXNlJztcbmltcG9ydCB7YXNzaWduLCBpbmNsdWRlcywgaXNTdHJpbmcsIG5vb3AsIHRvTm9kZX0gZnJvbSAnLi9sYW5nJztcblxubGV0IGlkID0gMDtcblxuZXhwb3J0IGNsYXNzIFBsYXllciB7XG5cbiAgICBjb25zdHJ1Y3RvcihlbCkge1xuICAgICAgICB0aGlzLmlkID0gKytpZDtcbiAgICAgICAgdGhpcy5lbCA9IHRvTm9kZShlbCk7XG4gICAgfVxuXG4gICAgaXNWaWRlbygpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuaXNZb3V0dWJlKCkgfHwgdGhpcy5pc1ZpbWVvKCkgfHwgdGhpcy5pc0hUTUw1KCk7XG4gICAgfVxuXG4gICAgaXNIVE1MNSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuZWwudGFnTmFtZSA9PT0gJ1ZJREVPJztcbiAgICB9XG5cbiAgICBpc0lGcmFtZSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuZWwudGFnTmFtZSA9PT0gJ0lGUkFNRSc7XG4gICAgfVxuXG4gICAgaXNZb3V0dWJlKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5pc0lGcmFtZSgpICYmICEhdGhpcy5lbC5zcmMubWF0Y2goL1xcL1xcLy4qP3lvdXR1YmUoLW5vY29va2llKT9cXC5bYS16XStcXC8od2F0Y2hcXD92PVteJlxcc10rfGVtYmVkKXx5b3V0dVxcLmJlXFwvLiovKTtcbiAgICB9XG5cbiAgICBpc1ZpbWVvKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5pc0lGcmFtZSgpICYmICEhdGhpcy5lbC5zcmMubWF0Y2goL3ZpbWVvXFwuY29tXFwvdmlkZW9cXC8uKi8pO1xuICAgIH1cblxuICAgIGVuYWJsZUFwaSgpIHtcblxuICAgICAgICBpZiAodGhpcy5yZWFkeSkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMucmVhZHk7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCB5b3V0dWJlID0gdGhpcy5pc1lvdXR1YmUoKTtcbiAgICAgICAgY29uc3QgdmltZW8gPSB0aGlzLmlzVmltZW8oKTtcblxuICAgICAgICBsZXQgcG9sbGVyO1xuXG4gICAgICAgIGlmICh5b3V0dWJlIHx8IHZpbWVvKSB7XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLnJlYWR5ID0gbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XG5cbiAgICAgICAgICAgICAgICBvbmNlKHRoaXMuZWwsICdsb2FkJywgKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoeW91dHViZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgbGlzdGVuZXIgPSAoKSA9PiBwb3N0KHRoaXMuZWwsIHtldmVudDogJ2xpc3RlbmluZycsIGlkOiB0aGlzLmlkfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBwb2xsZXIgPSBzZXRJbnRlcnZhbChsaXN0ZW5lciwgMTAwKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxpc3RlbmVyKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGxpc3RlbihkYXRhID0+IHlvdXR1YmUgJiYgZGF0YS5pZCA9PT0gdGhpcy5pZCAmJiBkYXRhLmV2ZW50ID09PSAnb25SZWFkeScgfHwgdmltZW8gJiYgTnVtYmVyKGRhdGEucGxheWVyX2lkKSA9PT0gdGhpcy5pZClcbiAgICAgICAgICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcG9sbGVyICYmIGNsZWFySW50ZXJ2YWwocG9sbGVyKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBhdHRyKHRoaXMuZWwsICdzcmMnLCBgJHt0aGlzLmVsLnNyY30ke2luY2x1ZGVzKHRoaXMuZWwuc3JjLCAnPycpID8gJyYnIDogJz8nfSR7eW91dHViZSA/ICdlbmFibGVqc2FwaT0xJyA6IGBhcGk9MSZwbGF5ZXJfaWQ9JHt0aGlzLmlkfWB9YCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKCk7XG5cbiAgICB9XG5cbiAgICBwbGF5KCkge1xuXG4gICAgICAgIGlmICghdGhpcy5pc1ZpZGVvKCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLmlzSUZyYW1lKCkpIHtcbiAgICAgICAgICAgIHRoaXMuZW5hYmxlQXBpKCkudGhlbigoKSA9PiBwb3N0KHRoaXMuZWwsIHtmdW5jOiAncGxheVZpZGVvJywgbWV0aG9kOiAncGxheSd9KSk7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc0hUTUw1KCkpIHtcbiAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgY29uc3QgcHJvbWlzZSA9IHRoaXMuZWwucGxheSgpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHByb21pc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgcHJvbWlzZS5jYXRjaChub29wKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGNhdGNoIChlKSB7fVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgcGF1c2UoKSB7XG5cbiAgICAgICAgaWYgKCF0aGlzLmlzVmlkZW8oKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuaXNJRnJhbWUoKSkge1xuICAgICAgICAgICAgdGhpcy5lbmFibGVBcGkoKS50aGVuKCgpID0+IHBvc3QodGhpcy5lbCwge2Z1bmM6ICdwYXVzZVZpZGVvJywgbWV0aG9kOiAncGF1c2UnfSkpO1xuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuaXNIVE1MNSgpKSB7XG4gICAgICAgICAgICB0aGlzLmVsLnBhdXNlKCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBtdXRlKCkge1xuXG4gICAgICAgIGlmICghdGhpcy5pc1ZpZGVvKCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLmlzSUZyYW1lKCkpIHtcbiAgICAgICAgICAgIHRoaXMuZW5hYmxlQXBpKCkudGhlbigoKSA9PiBwb3N0KHRoaXMuZWwsIHtmdW5jOiAnbXV0ZScsIG1ldGhvZDogJ3NldFZvbHVtZScsIHZhbHVlOiAwfSkpO1xuICAgICAgICB9IGVsc2UgaWYgKHRoaXMuaXNIVE1MNSgpKSB7XG4gICAgICAgICAgICB0aGlzLmVsLm11dGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIGF0dHIodGhpcy5lbCwgJ211dGVkJywgJycpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn1cblxuZnVuY3Rpb24gcG9zdChlbCwgY21kKSB7XG4gICAgdHJ5IHtcbiAgICAgICAgZWwuY29udGVudFdpbmRvdy5wb3N0TWVzc2FnZShKU09OLnN0cmluZ2lmeShhc3NpZ24oe2V2ZW50OiAnY29tbWFuZCd9LCBjbWQpKSwgJyonKTtcbiAgICB9IGNhdGNoIChlKSB7fVxufVxuXG5mdW5jdGlvbiBsaXN0ZW4oY2IpIHtcblxuICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcblxuICAgICAgICBvbmNlKHdpbmRvdywgJ21lc3NhZ2UnLCAoXywgZGF0YSkgPT4gcmVzb2x2ZShkYXRhKSwgZmFsc2UsICh7ZGF0YX0pID0+IHtcblxuICAgICAgICAgICAgaWYgKCFkYXRhIHx8ICFpc1N0cmluZyhkYXRhKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICBkYXRhID0gSlNPTi5wYXJzZShkYXRhKTtcbiAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBkYXRhICYmIGNiKGRhdGEpO1xuXG4gICAgICAgIH0pO1xuXG4gICAgfSk7XG5cbn1cbiIsImltcG9ydCB7dG9GbG9hdH0gZnJvbSAnLi9sYW5nJztcbmltcG9ydCB7b259IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtpc0luVmlld30gZnJvbSAnLi9kaW1lbnNpb25zJztcblxuZXhwb3J0IGNvbnN0IEludGVyc2VjdGlvbk9ic2VydmVyID0gJ0ludGVyc2VjdGlvbk9ic2VydmVyJyBpbiB3aW5kb3dcbiAgICA/IHdpbmRvdy5JbnRlcnNlY3Rpb25PYnNlcnZlclxuICAgIDogY2xhc3MgSW50ZXJzZWN0aW9uT2JzZXJ2ZXJDbGFzcyB7XG5cbiAgICAgICAgY29uc3RydWN0b3IoY2FsbGJhY2ssIHtyb290TWFyZ2luID0gJzAgMCd9ID0ge30pIHtcblxuICAgICAgICAgICAgdGhpcy50YXJnZXRzID0gW107XG5cbiAgICAgICAgICAgIGNvbnN0IFtvZmZzZXRUb3AsIG9mZnNldExlZnRdID0gKHJvb3RNYXJnaW4gfHwgJzAgMCcpLnNwbGl0KCcgJykubWFwKHRvRmxvYXQpO1xuXG4gICAgICAgICAgICB0aGlzLm9mZnNldFRvcCA9IG9mZnNldFRvcDtcbiAgICAgICAgICAgIHRoaXMub2Zmc2V0TGVmdCA9IG9mZnNldExlZnQ7XG5cbiAgICAgICAgICAgIGxldCBwZW5kaW5nO1xuICAgICAgICAgICAgdGhpcy5hcHBseSA9ICgpID0+IHtcblxuICAgICAgICAgICAgICAgIGlmIChwZW5kaW5nKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBwZW5kaW5nID0gcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCByZWNvcmRzID0gdGhpcy50YWtlUmVjb3JkcygpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChyZWNvcmRzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2socmVjb3JkcywgdGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBwZW5kaW5nID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgfSkpO1xuXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICB0aGlzLm9mZiA9IG9uKHdpbmRvdywgJ3Njcm9sbCByZXNpemUgbG9hZCcsIHRoaXMuYXBwbHksIHtwYXNzaXZlOiB0cnVlLCBjYXB0dXJlOiB0cnVlfSk7XG5cbiAgICAgICAgfVxuXG4gICAgICAgIHRha2VSZWNvcmRzKCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMudGFyZ2V0cy5maWx0ZXIoZW50cnkgPT4ge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgaW5WaWV3ID0gaXNJblZpZXcoZW50cnkudGFyZ2V0LCB0aGlzLm9mZnNldFRvcCwgdGhpcy5vZmZzZXRMZWZ0KTtcblxuICAgICAgICAgICAgICAgIGlmIChlbnRyeS5pc0ludGVyc2VjdGluZyA9PT0gbnVsbCB8fCBpblZpZXcgXiBlbnRyeS5pc0ludGVyc2VjdGluZykge1xuICAgICAgICAgICAgICAgICAgICBlbnRyeS5pc0ludGVyc2VjdGluZyA9IGluVmlldztcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIG9ic2VydmUodGFyZ2V0KSB7XG4gICAgICAgICAgICB0aGlzLnRhcmdldHMucHVzaCh7XG4gICAgICAgICAgICAgICAgdGFyZ2V0LFxuICAgICAgICAgICAgICAgIGlzSW50ZXJzZWN0aW5nOiBudWxsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHRoaXMuYXBwbHkoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGRpc2Nvbm5lY3QoKSB7XG4gICAgICAgICAgICB0aGlzLnRhcmdldHMgPSBbXTtcbiAgICAgICAgICAgIHRoaXMub2ZmKCk7XG4gICAgICAgIH1cblxuICAgIH07XG4iLCJpbXBvcnQgeyQsIGFwcGx5LCBpc1N0cmluZywgbWVyZ2VPcHRpb25zLCB0b05vZGV9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiAoVUlraXQpIHtcblxuICAgIGNvbnN0IERBVEEgPSBVSWtpdC5kYXRhO1xuXG4gICAgVUlraXQudXNlID0gZnVuY3Rpb24gKHBsdWdpbikge1xuXG4gICAgICAgIGlmIChwbHVnaW4uaW5zdGFsbGVkKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBwbHVnaW4uY2FsbChudWxsLCB0aGlzKTtcbiAgICAgICAgcGx1Z2luLmluc3RhbGxlZCA9IHRydWU7XG5cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuICAgIFVJa2l0Lm1peGluID0gZnVuY3Rpb24gKG1peGluLCBjb21wb25lbnQpIHtcbiAgICAgICAgY29tcG9uZW50ID0gKGlzU3RyaW5nKGNvbXBvbmVudCkgPyBVSWtpdC5jb21wb25lbnQoY29tcG9uZW50KSA6IGNvbXBvbmVudCkgfHwgdGhpcztcbiAgICAgICAgY29tcG9uZW50Lm9wdGlvbnMgPSBtZXJnZU9wdGlvbnMoY29tcG9uZW50Lm9wdGlvbnMsIG1peGluKTtcbiAgICB9O1xuXG4gICAgVUlraXQuZXh0ZW5kID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcblxuICAgICAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgICAgICBjb25zdCBTdXBlciA9IHRoaXM7XG4gICAgICAgIGNvbnN0IFN1YiA9IGZ1bmN0aW9uIFVJa2l0Q29tcG9uZW50KG9wdGlvbnMpIHtcbiAgICAgICAgICAgIHRoaXMuX2luaXQob3B0aW9ucyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgU3ViLnByb3RvdHlwZSA9IE9iamVjdC5jcmVhdGUoU3VwZXIucHJvdG90eXBlKTtcbiAgICAgICAgU3ViLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IFN1YjtcbiAgICAgICAgU3ViLm9wdGlvbnMgPSBtZXJnZU9wdGlvbnMoU3VwZXIub3B0aW9ucywgb3B0aW9ucyk7XG5cbiAgICAgICAgU3ViLnN1cGVyID0gU3VwZXI7XG4gICAgICAgIFN1Yi5leHRlbmQgPSBTdXBlci5leHRlbmQ7XG5cbiAgICAgICAgcmV0dXJuIFN1YjtcbiAgICB9O1xuXG4gICAgVUlraXQudXBkYXRlID0gZnVuY3Rpb24gKGVsZW1lbnQsIGUpIHtcblxuICAgICAgICBlbGVtZW50ID0gZWxlbWVudCA/IHRvTm9kZShlbGVtZW50KSA6IGRvY3VtZW50LmJvZHk7XG5cbiAgICAgICAgcGF0aChlbGVtZW50LCBlbGVtZW50ID0+IHVwZGF0ZShlbGVtZW50W0RBVEFdLCBlKSk7XG4gICAgICAgIGFwcGx5KGVsZW1lbnQsIGVsZW1lbnQgPT4gdXBkYXRlKGVsZW1lbnRbREFUQV0sIGUpKTtcblxuICAgIH07XG5cbiAgICBsZXQgY29udGFpbmVyO1xuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShVSWtpdCwgJ2NvbnRhaW5lcicsIHtcblxuICAgICAgICBnZXQoKSB7XG4gICAgICAgICAgICByZXR1cm4gY29udGFpbmVyIHx8IGRvY3VtZW50LmJvZHk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgc2V0KGVsZW1lbnQpIHtcbiAgICAgICAgICAgIGNvbnRhaW5lciA9ICQoZWxlbWVudCk7XG4gICAgICAgIH1cblxuICAgIH0pO1xuXG4gICAgZnVuY3Rpb24gdXBkYXRlKGRhdGEsIGUpIHtcblxuICAgICAgICBpZiAoIWRhdGEpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAoY29uc3QgbmFtZSBpbiBkYXRhKSB7XG4gICAgICAgICAgICBpZiAoZGF0YVtuYW1lXS5fY29ubmVjdGVkKSB7XG4gICAgICAgICAgICAgICAgZGF0YVtuYW1lXS5fY2FsbFVwZGF0ZShlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcGF0aChub2RlLCBmbikge1xuICAgICAgICBpZiAobm9kZSAmJiBub2RlICE9PSBkb2N1bWVudC5ib2R5ICYmIG5vZGUucGFyZW50Tm9kZSkge1xuICAgICAgICAgICAgcGF0aChub2RlLnBhcmVudE5vZGUsIGZuKTtcbiAgICAgICAgICAgIGZuKG5vZGUucGFyZW50Tm9kZSk7XG4gICAgICAgIH1cbiAgICB9XG5cbn1cbiIsImltcG9ydCB7YXNzaWduLCBmYXN0ZG9tLCBpbmNsdWRlcywgaXNQbGFpbk9iamVjdH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9jYWxsSG9vayA9IGZ1bmN0aW9uIChob29rKSB7XG5cbiAgICAgICAgY29uc3QgaGFuZGxlcnMgPSB0aGlzLiRvcHRpb25zW2hvb2tdO1xuXG4gICAgICAgIGlmIChoYW5kbGVycykge1xuICAgICAgICAgICAgaGFuZGxlcnMuZm9yRWFjaChoYW5kbGVyID0+IGhhbmRsZXIuY2FsbCh0aGlzKSk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9jYWxsQ29ubmVjdGVkID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGlmICh0aGlzLl9jb25uZWN0ZWQpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuX2RhdGEgPSB7fTtcbiAgICAgICAgdGhpcy5fY29tcHV0ZWRzID0ge307XG4gICAgICAgIHRoaXMuX2luaXRQcm9wcygpO1xuXG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdiZWZvcmVDb25uZWN0Jyk7XG4gICAgICAgIHRoaXMuX2Nvbm5lY3RlZCA9IHRydWU7XG5cbiAgICAgICAgdGhpcy5faW5pdEV2ZW50cygpO1xuICAgICAgICB0aGlzLl9pbml0T2JzZXJ2ZXIoKTtcblxuICAgICAgICB0aGlzLl9jYWxsSG9vaygnY29ubmVjdGVkJyk7XG4gICAgICAgIHRoaXMuX2NhbGxVcGRhdGUoKTtcbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9jYWxsRGlzY29ubmVjdGVkID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGlmICghdGhpcy5fY29ubmVjdGVkKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLl9jYWxsSG9vaygnYmVmb3JlRGlzY29ubmVjdCcpO1xuXG4gICAgICAgIGlmICh0aGlzLl9vYnNlcnZlcikge1xuICAgICAgICAgICAgdGhpcy5fb2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xuICAgICAgICAgICAgdGhpcy5fb2JzZXJ2ZXIgPSBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5fdW5iaW5kRXZlbnRzKCk7XG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdkaXNjb25uZWN0ZWQnKTtcblxuICAgICAgICB0aGlzLl9jb25uZWN0ZWQgPSBmYWxzZTtcblxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2NhbGxVcGRhdGUgPSBmdW5jdGlvbiAoZSA9ICd1cGRhdGUnKSB7XG5cbiAgICAgICAgY29uc3QgdHlwZSA9IGUudHlwZSB8fCBlO1xuXG4gICAgICAgIGlmIChpbmNsdWRlcyhbJ3VwZGF0ZScsICdyZXNpemUnXSwgdHlwZSkpIHtcbiAgICAgICAgICAgIHRoaXMuX2NhbGxXYXRjaGVzKCk7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCB1cGRhdGVzID0gdGhpcy4kb3B0aW9ucy51cGRhdGU7XG4gICAgICAgIGNvbnN0IHtyZWFkcywgd3JpdGVzfSA9IHRoaXMuX2ZyYW1lcztcblxuICAgICAgICBpZiAoIXVwZGF0ZXMpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHVwZGF0ZXMuZm9yRWFjaCgoe3JlYWQsIHdyaXRlLCBldmVudHN9LCBpKSA9PiB7XG5cbiAgICAgICAgICAgIGlmICh0eXBlICE9PSAndXBkYXRlJyAmJiAhaW5jbHVkZXMoZXZlbnRzLCB0eXBlKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKHJlYWQgJiYgIWluY2x1ZGVzKGZhc3Rkb20ucmVhZHMsIHJlYWRzW2ldKSkge1xuICAgICAgICAgICAgICAgIHJlYWRzW2ldID0gZmFzdGRvbS5yZWFkKCgpID0+IHtcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCByZXN1bHQgPSB0aGlzLl9jb25uZWN0ZWQgJiYgcmVhZC5jYWxsKHRoaXMsIHRoaXMuX2RhdGEsIHR5cGUpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChyZXN1bHQgPT09IGZhbHNlICYmIHdyaXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBmYXN0ZG9tLmNsZWFyKHdyaXRlc1tpXSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoaXNQbGFpbk9iamVjdChyZXN1bHQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhc3NpZ24odGhpcy5fZGF0YSwgcmVzdWx0KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAod3JpdGUgJiYgIWluY2x1ZGVzKGZhc3Rkb20ud3JpdGVzLCB3cml0ZXNbaV0pKSB7XG4gICAgICAgICAgICAgICAgd3JpdGVzW2ldID0gZmFzdGRvbS53cml0ZSgoKSA9PiB0aGlzLl9jb25uZWN0ZWQgJiYgd3JpdGUuY2FsbCh0aGlzLCB0aGlzLl9kYXRhLCB0eXBlKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICB9O1xuXG59XG4iLCJpbXBvcnQge2Fzc2lnbiwgYmluZCwgY2FtZWxpemUsIGRhdGEgYXMgZ2V0RGF0YSwgaGFzT3duLCBoeXBoZW5hdGUsIGlzQXJyYXksIGlzQm9vbGVhbiwgaXNFbXB0eSwgaXNFcXVhbCwgaXNGdW5jdGlvbiwgaXNQbGFpbk9iamVjdCwgaXNTdHJpbmcsIGlzVW5kZWZpbmVkLCBtZXJnZU9wdGlvbnMsIG9uLCBwYXJzZU9wdGlvbnMsIHN0YXJ0c1dpdGgsIHRvQm9vbGVhbiwgdG9MaXN0LCB0b051bWJlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgbGV0IHVpZCA9IDA7XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2luaXQgPSBmdW5jdGlvbiAob3B0aW9ucykge1xuXG4gICAgICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgICAgICBvcHRpb25zLmRhdGEgPSBub3JtYWxpemVEYXRhKG9wdGlvbnMsIHRoaXMuY29uc3RydWN0b3Iub3B0aW9ucyk7XG5cbiAgICAgICAgdGhpcy4kb3B0aW9ucyA9IG1lcmdlT3B0aW9ucyh0aGlzLmNvbnN0cnVjdG9yLm9wdGlvbnMsIG9wdGlvbnMsIHRoaXMpO1xuICAgICAgICB0aGlzLiRlbCA9IG51bGw7XG4gICAgICAgIHRoaXMuJHByb3BzID0ge307XG5cbiAgICAgICAgdGhpcy5fZnJhbWVzID0ge3JlYWRzOiB7fSwgd3JpdGVzOiB7fX07XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IFtdO1xuXG4gICAgICAgIHRoaXMuX3VpZCA9IHVpZCsrO1xuICAgICAgICB0aGlzLl9pbml0RGF0YSgpO1xuICAgICAgICB0aGlzLl9pbml0TWV0aG9kcygpO1xuICAgICAgICB0aGlzLl9pbml0Q29tcHV0ZWRzKCk7XG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdjcmVhdGVkJyk7XG5cbiAgICAgICAgaWYgKG9wdGlvbnMuZWwpIHtcbiAgICAgICAgICAgIHRoaXMuJG1vdW50KG9wdGlvbnMuZWwpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdERhdGEgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgY29uc3Qge2RhdGEgPSB7fX0gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIGZvciAoY29uc3Qga2V5IGluIGRhdGEpIHtcbiAgICAgICAgICAgIHRoaXMuJHByb3BzW2tleV0gPSB0aGlzW2tleV0gPSBkYXRhW2tleV07XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9pbml0TWV0aG9kcyA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBjb25zdCB7bWV0aG9kc30gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIGlmIChtZXRob2RzKSB7XG4gICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBtZXRob2RzKSB7XG4gICAgICAgICAgICAgICAgdGhpc1trZXldID0gYmluZChtZXRob2RzW2tleV0sIHRoaXMpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdENvbXB1dGVkcyA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBjb25zdCB7Y29tcHV0ZWR9ID0gdGhpcy4kb3B0aW9ucztcblxuICAgICAgICB0aGlzLl9jb21wdXRlZHMgPSB7fTtcblxuICAgICAgICBpZiAoY29tcHV0ZWQpIHtcbiAgICAgICAgICAgIGZvciAoY29uc3Qga2V5IGluIGNvbXB1dGVkKSB7XG4gICAgICAgICAgICAgICAgcmVnaXN0ZXJDb21wdXRlZCh0aGlzLCBrZXksIGNvbXB1dGVkW2tleV0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5fY2FsbFdhdGNoZXMgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgY29uc3QgeyRvcHRpb25zOiB7Y29tcHV0ZWR9LCBfY29tcHV0ZWRzfSA9IHRoaXM7XG5cbiAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gX2NvbXB1dGVkcykge1xuXG4gICAgICAgICAgICBjb25zdCB2YWx1ZSA9IF9jb21wdXRlZHNba2V5XTtcbiAgICAgICAgICAgIGRlbGV0ZSBfY29tcHV0ZWRzW2tleV07XG5cbiAgICAgICAgICAgIGlmIChjb21wdXRlZFtrZXldLndhdGNoICYmICFpc0VxdWFsKHZhbHVlLCB0aGlzW2tleV0pKSB7XG4gICAgICAgICAgICAgICAgY29tcHV0ZWRba2V5XS53YXRjaC5jYWxsKHRoaXMsIHRoaXNba2V5XSwgdmFsdWUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2luaXRQcm9wcyA9IGZ1bmN0aW9uIChwcm9wcykge1xuXG4gICAgICAgIGxldCBrZXk7XG5cbiAgICAgICAgcHJvcHMgPSBwcm9wcyB8fCBnZXRQcm9wcyh0aGlzLiRvcHRpb25zLCB0aGlzLiRuYW1lKTtcblxuICAgICAgICBmb3IgKGtleSBpbiBwcm9wcykge1xuICAgICAgICAgICAgaWYgKCFpc1VuZGVmaW5lZChwcm9wc1trZXldKSkge1xuICAgICAgICAgICAgICAgIHRoaXMuJHByb3BzW2tleV0gPSBwcm9wc1trZXldO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgZXhjbHVkZSA9IFt0aGlzLiRvcHRpb25zLmNvbXB1dGVkLCB0aGlzLiRvcHRpb25zLm1ldGhvZHNdO1xuICAgICAgICBmb3IgKGtleSBpbiB0aGlzLiRwcm9wcykge1xuICAgICAgICAgICAgaWYgKGtleSBpbiBwcm9wcyAmJiBub3RJbihleGNsdWRlLCBrZXkpKSB7XG4gICAgICAgICAgICAgICAgdGhpc1trZXldID0gdGhpcy4kcHJvcHNba2V5XTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2luaXRFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgY29uc3Qge2V2ZW50c30gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIGlmIChldmVudHMpIHtcblxuICAgICAgICAgICAgZXZlbnRzLmZvckVhY2goZXZlbnQgPT4ge1xuXG4gICAgICAgICAgICAgICAgaWYgKCFoYXNPd24oZXZlbnQsICdoYW5kbGVyJykpIHtcbiAgICAgICAgICAgICAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlZ2lzdGVyRXZlbnQodGhpcywgZXZlbnRba2V5XSwga2V5KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHJlZ2lzdGVyRXZlbnQodGhpcywgZXZlbnQpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl91bmJpbmRFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHRoaXMuX2V2ZW50cy5mb3JFYWNoKHVuYmluZCA9PiB1bmJpbmQoKSk7XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IFtdO1xuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2luaXRPYnNlcnZlciA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBsZXQge2F0dHJzLCBwcm9wcywgZWx9ID0gdGhpcy4kb3B0aW9ucztcbiAgICAgICAgaWYgKHRoaXMuX29ic2VydmVyIHx8ICFwcm9wcyB8fCBhdHRycyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGF0dHJzID0gaXNBcnJheShhdHRycykgPyBhdHRycyA6IE9iamVjdC5rZXlzKHByb3BzKTtcblxuICAgICAgICB0aGlzLl9vYnNlcnZlciA9IG5ldyBNdXRhdGlvbk9ic2VydmVyKCgpID0+IHtcblxuICAgICAgICAgICAgY29uc3QgZGF0YSA9IGdldFByb3BzKHRoaXMuJG9wdGlvbnMsIHRoaXMuJG5hbWUpO1xuICAgICAgICAgICAgaWYgKGF0dHJzLnNvbWUoa2V5ID0+ICFpc1VuZGVmaW5lZChkYXRhW2tleV0pICYmIGRhdGFba2V5XSAhPT0gdGhpcy4kcHJvcHNba2V5XSkpIHtcbiAgICAgICAgICAgICAgICB0aGlzLiRyZXNldCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGNvbnN0IGZpbHRlciA9IGF0dHJzLm1hcChrZXkgPT4gaHlwaGVuYXRlKGtleSkpLmNvbmNhdCh0aGlzLiRuYW1lKTtcblxuICAgICAgICB0aGlzLl9vYnNlcnZlci5vYnNlcnZlKGVsLCB7XG4gICAgICAgICAgICBhdHRyaWJ1dGVzOiB0cnVlLFxuICAgICAgICAgICAgYXR0cmlidXRlRmlsdGVyOiBmaWx0ZXIuY29uY2F0KGZpbHRlci5tYXAoa2V5ID0+IGBkYXRhLSR7a2V5fWApKVxuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgZnVuY3Rpb24gZ2V0UHJvcHMob3B0cywgbmFtZSkge1xuXG4gICAgICAgIGNvbnN0IGRhdGEgPSB7fTtcbiAgICAgICAgY29uc3Qge2FyZ3MgPSBbXSwgcHJvcHMgPSB7fSwgZWx9ID0gb3B0cztcblxuICAgICAgICBpZiAoIXByb3BzKSB7XG4gICAgICAgICAgICByZXR1cm4gZGF0YTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAoY29uc3Qga2V5IGluIHByb3BzKSB7XG4gICAgICAgICAgICBjb25zdCBwcm9wID0gaHlwaGVuYXRlKGtleSk7XG4gICAgICAgICAgICBsZXQgdmFsdWUgPSBnZXREYXRhKGVsLCBwcm9wKTtcblxuICAgICAgICAgICAgaWYgKCFpc1VuZGVmaW5lZCh2YWx1ZSkpIHtcblxuICAgICAgICAgICAgICAgIHZhbHVlID0gcHJvcHNba2V5XSA9PT0gQm9vbGVhbiAmJiB2YWx1ZSA9PT0gJydcbiAgICAgICAgICAgICAgICAgICAgPyB0cnVlXG4gICAgICAgICAgICAgICAgICAgIDogY29lcmNlKHByb3BzW2tleV0sIHZhbHVlKTtcblxuICAgICAgICAgICAgICAgIGlmIChwcm9wID09PSAndGFyZ2V0JyAmJiAoIXZhbHVlIHx8IHN0YXJ0c1dpdGgodmFsdWUsICdfJykpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGRhdGFba2V5XSA9IHZhbHVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgY29uc3Qgb3B0aW9ucyA9IHBhcnNlT3B0aW9ucyhnZXREYXRhKGVsLCBuYW1lKSwgYXJncyk7XG5cbiAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gb3B0aW9ucykge1xuICAgICAgICAgICAgY29uc3QgcHJvcCA9IGNhbWVsaXplKGtleSk7XG4gICAgICAgICAgICBpZiAocHJvcHNbcHJvcF0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGRhdGFbcHJvcF0gPSBjb2VyY2UocHJvcHNbcHJvcF0sIG9wdGlvbnNba2V5XSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gZGF0YTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiByZWdpc3RlckNvbXB1dGVkKGNvbXBvbmVudCwga2V5LCBjYikge1xuICAgICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkoY29tcG9uZW50LCBrZXksIHtcblxuICAgICAgICAgICAgZW51bWVyYWJsZTogdHJ1ZSxcblxuICAgICAgICAgICAgZ2V0KCkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge19jb21wdXRlZHMsICRwcm9wcywgJGVsfSA9IGNvbXBvbmVudDtcblxuICAgICAgICAgICAgICAgIGlmICghaGFzT3duKF9jb21wdXRlZHMsIGtleSkpIHtcbiAgICAgICAgICAgICAgICAgICAgX2NvbXB1dGVkc1trZXldID0gKGNiLmdldCB8fCBjYikuY2FsbChjb21wb25lbnQsICRwcm9wcywgJGVsKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gX2NvbXB1dGVkc1trZXldO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgc2V0KHZhbHVlKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCB7X2NvbXB1dGVkc30gPSBjb21wb25lbnQ7XG5cbiAgICAgICAgICAgICAgICBfY29tcHV0ZWRzW2tleV0gPSBjYi5zZXQgPyBjYi5zZXQuY2FsbChjb21wb25lbnQsIHZhbHVlKSA6IHZhbHVlO1xuXG4gICAgICAgICAgICAgICAgaWYgKGlzVW5kZWZpbmVkKF9jb21wdXRlZHNba2V5XSkpIHtcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlIF9jb21wdXRlZHNba2V5XTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVnaXN0ZXJFdmVudChjb21wb25lbnQsIGV2ZW50LCBrZXkpIHtcblxuICAgICAgICBpZiAoIWlzUGxhaW5PYmplY3QoZXZlbnQpKSB7XG4gICAgICAgICAgICBldmVudCA9ICh7bmFtZToga2V5LCBoYW5kbGVyOiBldmVudH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IHtuYW1lLCBlbCwgaGFuZGxlciwgY2FwdHVyZSwgcGFzc2l2ZSwgZGVsZWdhdGUsIGZpbHRlciwgc2VsZn0gPSBldmVudDtcbiAgICAgICAgZWwgPSBpc0Z1bmN0aW9uKGVsKVxuICAgICAgICAgICAgPyBlbC5jYWxsKGNvbXBvbmVudClcbiAgICAgICAgICAgIDogZWwgfHwgY29tcG9uZW50LiRlbDtcblxuICAgICAgICBpZiAoaXNBcnJheShlbCkpIHtcbiAgICAgICAgICAgIGVsLmZvckVhY2goZWwgPT4gcmVnaXN0ZXJFdmVudChjb21wb25lbnQsIGFzc2lnbih7fSwgZXZlbnQsIHtlbH0pLCBrZXkpKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICghZWwgfHwgZmlsdGVyICYmICFmaWx0ZXIuY2FsbChjb21wb25lbnQpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBoYW5kbGVyID0gZGV0YWlsKGlzU3RyaW5nKGhhbmRsZXIpID8gY29tcG9uZW50W2hhbmRsZXJdIDogYmluZChoYW5kbGVyLCBjb21wb25lbnQpKTtcblxuICAgICAgICBpZiAoc2VsZikge1xuICAgICAgICAgICAgaGFuZGxlciA9IHNlbGZGaWx0ZXIoaGFuZGxlcik7XG4gICAgICAgIH1cblxuICAgICAgICBjb21wb25lbnQuX2V2ZW50cy5wdXNoKFxuICAgICAgICAgICAgb24oXG4gICAgICAgICAgICAgICAgZWwsXG4gICAgICAgICAgICAgICAgbmFtZSxcbiAgICAgICAgICAgICAgICAhZGVsZWdhdGVcbiAgICAgICAgICAgICAgICAgICAgPyBudWxsXG4gICAgICAgICAgICAgICAgICAgIDogaXNTdHJpbmcoZGVsZWdhdGUpXG4gICAgICAgICAgICAgICAgICAgICAgICA/IGRlbGVnYXRlXG4gICAgICAgICAgICAgICAgICAgICAgICA6IGRlbGVnYXRlLmNhbGwoY29tcG9uZW50KSxcbiAgICAgICAgICAgICAgICBoYW5kbGVyLFxuICAgICAgICAgICAgICAgIGlzQm9vbGVhbihwYXNzaXZlKVxuICAgICAgICAgICAgICAgICAgICA/IHtwYXNzaXZlLCBjYXB0dXJlfVxuICAgICAgICAgICAgICAgICAgICA6IGNhcHR1cmVcbiAgICAgICAgICAgIClcbiAgICAgICAgKTtcblxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNlbGZGaWx0ZXIoaGFuZGxlcikge1xuICAgICAgICByZXR1cm4gZnVuY3Rpb24gc2VsZkhhbmRsZXIoZSkge1xuICAgICAgICAgICAgaWYgKGUudGFyZ2V0ID09PSBlLmN1cnJlbnRUYXJnZXQgfHwgZS50YXJnZXQgPT09IGUuY3VycmVudCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBoYW5kbGVyLmNhbGwobnVsbCwgZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gbm90SW4ob3B0aW9ucywga2V5KSB7XG4gICAgICAgIHJldHVybiBvcHRpb25zLmV2ZXJ5KGFyciA9PiAhYXJyIHx8ICFoYXNPd24oYXJyLCBrZXkpKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBkZXRhaWwobGlzdGVuZXIpIHtcbiAgICAgICAgcmV0dXJuIGUgPT4gaXNBcnJheShlLmRldGFpbCkgPyBsaXN0ZW5lciguLi5bZV0uY29uY2F0KGUuZGV0YWlsKSkgOiBsaXN0ZW5lcihlKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBjb2VyY2UodHlwZSwgdmFsdWUpIHtcblxuICAgICAgICBpZiAodHlwZSA9PT0gQm9vbGVhbikge1xuICAgICAgICAgICAgcmV0dXJuIHRvQm9vbGVhbih2YWx1ZSk7XG4gICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gTnVtYmVyKSB7XG4gICAgICAgICAgICByZXR1cm4gdG9OdW1iZXIodmFsdWUpO1xuICAgICAgICB9IGVsc2UgaWYgKHR5cGUgPT09ICdsaXN0Jykge1xuICAgICAgICAgICAgcmV0dXJuIHRvTGlzdCh2YWx1ZSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdHlwZSA/IHR5cGUodmFsdWUpIDogdmFsdWU7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gbm9ybWFsaXplRGF0YSh7ZGF0YSwgZWx9LCB7YXJncywgcHJvcHMgPSB7fX0pIHtcbiAgICAgICAgZGF0YSA9IGlzQXJyYXkoZGF0YSlcbiAgICAgICAgICAgID8gIWlzRW1wdHkoYXJncylcbiAgICAgICAgICAgICAgICA/IGRhdGEuc2xpY2UoMCwgYXJncy5sZW5ndGgpLnJlZHVjZSgoZGF0YSwgdmFsdWUsIGluZGV4KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChpc1BsYWluT2JqZWN0KHZhbHVlKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYXNzaWduKGRhdGEsIHZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFbYXJnc1tpbmRleF1dID0gdmFsdWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGRhdGE7XG4gICAgICAgICAgICAgICAgfSwge30pXG4gICAgICAgICAgICAgICAgOiB1bmRlZmluZWRcbiAgICAgICAgICAgIDogZGF0YTtcblxuICAgICAgICBpZiAoZGF0YSkge1xuICAgICAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gZGF0YSkge1xuICAgICAgICAgICAgICAgIGlmIChpc1VuZGVmaW5lZChkYXRhW2tleV0pKSB7XG4gICAgICAgICAgICAgICAgICAgIGRlbGV0ZSBkYXRhW2tleV07XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZGF0YVtrZXldID0gcHJvcHNba2V5XSA/IGNvZXJjZShwcm9wc1trZXldLCBkYXRhW2tleV0sIGVsKSA6IGRhdGFba2V5XTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gZGF0YTtcbiAgICB9XG59XG4iLCJpbXBvcnQge2h5cGhlbmF0ZSwgaXNFbXB0eSwgcmVtb3ZlLCB3aXRoaW59IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiAoVUlraXQpIHtcblxuICAgIGNvbnN0IERBVEEgPSBVSWtpdC5kYXRhO1xuXG4gICAgVUlraXQucHJvdG90eXBlLiRtb3VudCA9IGZ1bmN0aW9uIChlbCkge1xuXG4gICAgICAgIGNvbnN0IHtuYW1lfSA9IHRoaXMuJG9wdGlvbnM7XG5cbiAgICAgICAgaWYgKCFlbFtEQVRBXSkge1xuICAgICAgICAgICAgZWxbREFUQV0gPSB7fTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChlbFtEQVRBXVtuYW1lXSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgZWxbREFUQV1bbmFtZV0gPSB0aGlzO1xuXG4gICAgICAgIHRoaXMuJGVsID0gdGhpcy4kb3B0aW9ucy5lbCA9IHRoaXMuJG9wdGlvbnMuZWwgfHwgZWw7XG5cbiAgICAgICAgaWYgKHdpdGhpbihlbCwgZG9jdW1lbnQpKSB7XG4gICAgICAgICAgICB0aGlzLl9jYWxsQ29ubmVjdGVkKCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLiRlbWl0ID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgdGhpcy5fY2FsbFVwZGF0ZShlKTtcbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLiRyZXNldCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdGhpcy5fY2FsbERpc2Nvbm5lY3RlZCgpO1xuICAgICAgICB0aGlzLl9jYWxsQ29ubmVjdGVkKCk7XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS4kZGVzdHJveSA9IGZ1bmN0aW9uIChyZW1vdmVFbCA9IGZhbHNlKSB7XG5cbiAgICAgICAgY29uc3Qge2VsLCBuYW1lfSA9IHRoaXMuJG9wdGlvbnM7XG5cbiAgICAgICAgaWYgKGVsKSB7XG4gICAgICAgICAgICB0aGlzLl9jYWxsRGlzY29ubmVjdGVkKCk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLl9jYWxsSG9vaygnZGVzdHJveScpO1xuXG4gICAgICAgIGlmICghZWwgfHwgIWVsW0RBVEFdKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBkZWxldGUgZWxbREFUQV1bbmFtZV07XG5cbiAgICAgICAgaWYgKCFpc0VtcHR5KGVsW0RBVEFdKSkge1xuICAgICAgICAgICAgZGVsZXRlIGVsW0RBVEFdO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHJlbW92ZUVsKSB7XG4gICAgICAgICAgICByZW1vdmUodGhpcy4kZWwpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS4kY3JlYXRlID0gZnVuY3Rpb24gKGNvbXBvbmVudCwgZWxlbWVudCwgZGF0YSkge1xuICAgICAgICByZXR1cm4gVUlraXRbY29tcG9uZW50XShlbGVtZW50LCBkYXRhKTtcbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLiR1cGRhdGUgPSBVSWtpdC51cGRhdGU7XG4gICAgVUlraXQucHJvdG90eXBlLiRnZXRDb21wb25lbnQgPSBVSWtpdC5nZXRDb21wb25lbnQ7XG5cbiAgICBjb25zdCBuYW1lcyA9IHt9O1xuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0aWVzKFVJa2l0LnByb3RvdHlwZSwge1xuXG4gICAgICAgICRjb250YWluZXI6IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoVUlraXQsICdjb250YWluZXInKSxcblxuICAgICAgICAkbmFtZToge1xuXG4gICAgICAgICAgICBnZXQoKSB7XG4gICAgICAgICAgICAgICAgY29uc3Qge25hbWV9ID0gdGhpcy4kb3B0aW9ucztcblxuICAgICAgICAgICAgICAgIGlmICghbmFtZXNbbmFtZV0pIHtcbiAgICAgICAgICAgICAgICAgICAgbmFtZXNbbmFtZV0gPSBVSWtpdC5wcmVmaXggKyBoeXBoZW5hdGUobmFtZSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIG5hbWVzW25hbWVdO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH0pO1xuXG59XG4iLCJpbXBvcnQgeyQkLCBhc3NpZ24sIGNhbWVsaXplLCBmYXN0ZG9tLCBoeXBoZW5hdGUsIGlzUGxhaW5PYmplY3QsIHN0YXJ0c1dpdGh9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiAoVUlraXQpIHtcblxuICAgIGNvbnN0IERBVEEgPSBVSWtpdC5kYXRhO1xuXG4gICAgY29uc3QgY29tcG9uZW50cyA9IHt9O1xuXG4gICAgVUlraXQuY29tcG9uZW50ID0gZnVuY3Rpb24gKG5hbWUsIG9wdGlvbnMpIHtcblxuICAgICAgICBpZiAoIW9wdGlvbnMpIHtcblxuICAgICAgICAgICAgaWYgKGlzUGxhaW5PYmplY3QoY29tcG9uZW50c1tuYW1lXSkpIHtcbiAgICAgICAgICAgICAgICBjb21wb25lbnRzW25hbWVdID0gVUlraXQuZXh0ZW5kKGNvbXBvbmVudHNbbmFtZV0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gY29tcG9uZW50c1tuYW1lXTtcblxuICAgICAgICB9XG5cbiAgICAgICAgVUlraXRbbmFtZV0gPSBmdW5jdGlvbiAoZWxlbWVudCwgZGF0YSkge1xuXG4gICAgICAgICAgICBjb25zdCBjb21wb25lbnQgPSBVSWtpdC5jb21wb25lbnQobmFtZSk7XG5cbiAgICAgICAgICAgIGlmIChpc1BsYWluT2JqZWN0KGVsZW1lbnQpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIG5ldyBjb21wb25lbnQoe2RhdGE6IGVsZW1lbnR9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGNvbXBvbmVudC5vcHRpb25zLmZ1bmN0aW9uYWwpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gbmV3IGNvbXBvbmVudCh7ZGF0YTogWy4uLmFyZ3VtZW50c119KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGVsZW1lbnQgJiYgZWxlbWVudC5ub2RlVHlwZSA/IGluaXQoZWxlbWVudCkgOiAkJChlbGVtZW50KS5tYXAoaW5pdClbMF07XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGluaXQoZWxlbWVudCkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgaW5zdGFuY2UgPSBVSWtpdC5nZXRDb21wb25lbnQoZWxlbWVudCwgbmFtZSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2UpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFkYXRhKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gaW5zdGFuY2U7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbnN0YW5jZS4kZGVzdHJveSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIG5ldyBjb21wb25lbnQoe2VsOiBlbGVtZW50LCBkYXRhfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IG9wdCA9IGlzUGxhaW5PYmplY3Qob3B0aW9ucykgPyBhc3NpZ24oe30sIG9wdGlvbnMpIDogb3B0aW9ucy5vcHRpb25zO1xuXG4gICAgICAgIG9wdC5uYW1lID0gbmFtZTtcblxuICAgICAgICBpZiAob3B0Lmluc3RhbGwpIHtcbiAgICAgICAgICAgIG9wdC5pbnN0YWxsKFVJa2l0LCBvcHQsIG5hbWUpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKFVJa2l0Ll9pbml0aWFsaXplZCAmJiAhb3B0LmZ1bmN0aW9uYWwpIHtcbiAgICAgICAgICAgIGNvbnN0IGlkID0gaHlwaGVuYXRlKG5hbWUpO1xuICAgICAgICAgICAgZmFzdGRvbS5yZWFkKCgpID0+IFVJa2l0W25hbWVdKGBbdWstJHtpZH1dLFtkYXRhLXVrLSR7aWR9XWApKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBjb21wb25lbnRzW25hbWVdID0gaXNQbGFpbk9iamVjdChvcHRpb25zKSA/IG9wdCA6IG9wdGlvbnM7XG4gICAgfTtcblxuICAgIFVJa2l0LmdldENvbXBvbmVudHMgPSBlbGVtZW50ID0+IGVsZW1lbnQgJiYgZWxlbWVudFtEQVRBXSB8fCB7fTtcbiAgICBVSWtpdC5nZXRDb21wb25lbnQgPSAoZWxlbWVudCwgbmFtZSkgPT4gVUlraXQuZ2V0Q29tcG9uZW50cyhlbGVtZW50KVtuYW1lXTtcblxuICAgIFVJa2l0LmNvbm5lY3QgPSBub2RlID0+IHtcblxuICAgICAgICBpZiAobm9kZVtEQVRBXSkge1xuICAgICAgICAgICAgZm9yIChjb25zdCBuYW1lIGluIG5vZGVbREFUQV0pIHtcbiAgICAgICAgICAgICAgICBub2RlW0RBVEFdW25hbWVdLl9jYWxsQ29ubmVjdGVkKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IG5vZGUuYXR0cmlidXRlcy5sZW5ndGg7IGkrKykge1xuXG4gICAgICAgICAgICBjb25zdCBuYW1lID0gZ2V0Q29tcG9uZW50TmFtZShub2RlLmF0dHJpYnV0ZXNbaV0ubmFtZSk7XG5cbiAgICAgICAgICAgIGlmIChuYW1lICYmIG5hbWUgaW4gY29tcG9uZW50cykge1xuICAgICAgICAgICAgICAgIFVJa2l0W25hbWVdKG5vZGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBVSWtpdC5kaXNjb25uZWN0ID0gbm9kZSA9PiB7XG4gICAgICAgIGZvciAoY29uc3QgbmFtZSBpbiBub2RlW0RBVEFdKSB7XG4gICAgICAgICAgICBub2RlW0RBVEFdW25hbWVdLl9jYWxsRGlzY29ubmVjdGVkKCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRDb21wb25lbnROYW1lKGF0dHJpYnV0ZSkge1xuICAgIHJldHVybiBzdGFydHNXaXRoKGF0dHJpYnV0ZSwgJ3VrLScpIHx8IHN0YXJ0c1dpdGgoYXR0cmlidXRlLCAnZGF0YS11ay0nKVxuICAgICAgICA/IGNhbWVsaXplKGF0dHJpYnV0ZS5yZXBsYWNlKCdkYXRhLXVrLScsICcnKS5yZXBsYWNlKCd1ay0nLCAnJykpXG4gICAgICAgIDogZmFsc2U7XG59XG4iLCJpbXBvcnQgZ2xvYmFsQVBJIGZyb20gJy4vZ2xvYmFsJztcbmltcG9ydCBob29rc0FQSSBmcm9tICcuL2hvb2tzJztcbmltcG9ydCBzdGF0ZUFQSSBmcm9tICcuL3N0YXRlJztcbmltcG9ydCBpbnN0YW5jZUFQSSBmcm9tICcuL2luc3RhbmNlJztcbmltcG9ydCBjb21wb25lbnRBUEkgZnJvbSAnLi9jb21wb25lbnQnO1xuaW1wb3J0ICogYXMgdXRpbCBmcm9tICd1aWtpdC11dGlsJztcblxuY29uc3QgVUlraXQgPSBmdW5jdGlvbiAob3B0aW9ucykge1xuICAgIHRoaXMuX2luaXQob3B0aW9ucyk7XG59O1xuXG5VSWtpdC51dGlsID0gdXRpbDtcblVJa2l0LmRhdGEgPSAnX191aWtpdF9fJztcblVJa2l0LnByZWZpeCA9ICd1ay0nO1xuVUlraXQub3B0aW9ucyA9IHt9O1xuXG5nbG9iYWxBUEkoVUlraXQpO1xuaG9va3NBUEkoVUlraXQpO1xuc3RhdGVBUEkoVUlraXQpO1xuY29tcG9uZW50QVBJKFVJa2l0KTtcbmluc3RhbmNlQVBJKFVJa2l0KTtcblxuZXhwb3J0IGRlZmF1bHQgVUlraXQ7XG4iLCJpbXBvcnQge2FkZENsYXNzLCBoYXNDbGFzc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgIWhhc0NsYXNzKHRoaXMuJGVsLCB0aGlzLiRuYW1lKSAmJiBhZGRDbGFzcyh0aGlzLiRlbCwgdGhpcy4kbmFtZSk7XG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHskJCwgQW5pbWF0aW9uLCBhc3NpZ24sIGF0dHIsIGNzcywgZmFzdGRvbSwgaGFzQXR0ciwgaGFzQ2xhc3MsIGhlaWdodCwgaW5jbHVkZXMsIGlzQm9vbGVhbiwgaXNGdW5jdGlvbiwgaXNVbmRlZmluZWQsIGlzVmlzaWJsZSwgbm9vcCwgUHJvbWlzZSwgdG9GbG9hdCwgdG9nZ2xlQ2xhc3MsIHRvTm9kZXMsIFRyYW5zaXRpb24sIHRyaWdnZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBwcm9wczoge1xuICAgICAgICBjbHM6IEJvb2xlYW4sXG4gICAgICAgIGFuaW1hdGlvbjogJ2xpc3QnLFxuICAgICAgICBkdXJhdGlvbjogTnVtYmVyLFxuICAgICAgICBvcmlnaW46IFN0cmluZyxcbiAgICAgICAgdHJhbnNpdGlvbjogU3RyaW5nLFxuICAgICAgICBxdWV1ZWQ6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBjbHM6IGZhbHNlLFxuICAgICAgICBhbmltYXRpb246IFtmYWxzZV0sXG4gICAgICAgIGR1cmF0aW9uOiAyMDAsXG4gICAgICAgIG9yaWdpbjogZmFsc2UsXG4gICAgICAgIHRyYW5zaXRpb246ICdsaW5lYXInLFxuICAgICAgICBxdWV1ZWQ6IGZhbHNlLFxuXG4gICAgICAgIGluaXRQcm9wczoge1xuICAgICAgICAgICAgb3ZlcmZsb3c6ICcnLFxuICAgICAgICAgICAgaGVpZ2h0OiAnJyxcbiAgICAgICAgICAgIHBhZGRpbmdUb3A6ICcnLFxuICAgICAgICAgICAgcGFkZGluZ0JvdHRvbTogJycsXG4gICAgICAgICAgICBtYXJnaW5Ub3A6ICcnLFxuICAgICAgICAgICAgbWFyZ2luQm90dG9tOiAnJ1xuICAgICAgICB9LFxuXG4gICAgICAgIGhpZGVQcm9wczoge1xuICAgICAgICAgICAgb3ZlcmZsb3c6ICdoaWRkZW4nLFxuICAgICAgICAgICAgaGVpZ2h0OiAwLFxuICAgICAgICAgICAgcGFkZGluZ1RvcDogMCxcbiAgICAgICAgICAgIHBhZGRpbmdCb3R0b206IDAsXG4gICAgICAgICAgICBtYXJnaW5Ub3A6IDAsXG4gICAgICAgICAgICBtYXJnaW5Cb3R0b206IDBcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaGFzQW5pbWF0aW9uKHthbmltYXRpb259KSB7XG4gICAgICAgICAgICByZXR1cm4gISFhbmltYXRpb25bMF07XG4gICAgICAgIH0sXG5cbiAgICAgICAgaGFzVHJhbnNpdGlvbih7YW5pbWF0aW9ufSkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaGFzQW5pbWF0aW9uICYmIGFuaW1hdGlvblswXSA9PT0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICB0b2dnbGVFbGVtZW50KHRhcmdldHMsIHNob3csIGFuaW1hdGUpIHtcbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcblxuICAgICAgICAgICAgICAgIHRhcmdldHMgPSB0b05vZGVzKHRhcmdldHMpO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWxsID0gdGFyZ2V0cyA9PiBQcm9taXNlLmFsbCh0YXJnZXRzLm1hcChlbCA9PiB0aGlzLl90b2dnbGVFbGVtZW50KGVsLCBzaG93LCBhbmltYXRlKSkpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHRvZ2dsZWQgPSB0YXJnZXRzLmZpbHRlcihlbCA9PiB0aGlzLmlzVG9nZ2xlZChlbCkpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHVudG9nZ2xlZCA9IHRhcmdldHMuZmlsdGVyKGVsID0+ICFpbmNsdWRlcyh0b2dnbGVkLCBlbCkpO1xuXG4gICAgICAgICAgICAgICAgbGV0IHA7XG5cbiAgICAgICAgICAgICAgICBpZiAoIXRoaXMucXVldWVkIHx8ICFpc1VuZGVmaW5lZChhbmltYXRlKSB8fCAhaXNVbmRlZmluZWQoc2hvdykgfHwgIXRoaXMuaGFzQW5pbWF0aW9uIHx8IHRhcmdldHMubGVuZ3RoIDwgMikge1xuXG4gICAgICAgICAgICAgICAgICAgIHAgPSBhbGwodW50b2dnbGVkLmNvbmNhdCh0b2dnbGVkKSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHtib2R5fSA9IGRvY3VtZW50O1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBzY3JvbGwgPSBib2R5LnNjcm9sbFRvcDtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgW2VsXSA9IHRvZ2dsZWQ7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGluUHJvZ3Jlc3MgPSBBbmltYXRpb24uaW5Qcm9ncmVzcyhlbCkgJiYgaGFzQ2xhc3MoZWwsICd1ay1hbmltYXRpb24tbGVhdmUnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHx8IFRyYW5zaXRpb24uaW5Qcm9ncmVzcyhlbCkgJiYgZWwuc3R5bGUuaGVpZ2h0ID09PSAnMHB4JztcblxuICAgICAgICAgICAgICAgICAgICBwID0gYWxsKHRvZ2dsZWQpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICghaW5Qcm9ncmVzcykge1xuICAgICAgICAgICAgICAgICAgICAgICAgcCA9IHAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgcCA9IGFsbCh1bnRvZ2dsZWQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkuc2Nyb2xsVG9wID0gc2Nyb2xsO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBwO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHAudGhlbihyZXNvbHZlLCBub29wKTtcblxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdG9nZ2xlTm93KHRhcmdldHMsIHNob3cpIHtcbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IFByb21pc2UuYWxsKHRvTm9kZXModGFyZ2V0cykubWFwKGVsID0+IHRoaXMuX3RvZ2dsZUVsZW1lbnQoZWwsIHNob3csIGZhbHNlKSkpLnRoZW4ocmVzb2x2ZSwgbm9vcCkpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGlzVG9nZ2xlZChlbCkge1xuICAgICAgICAgICAgY29uc3Qgbm9kZXMgPSB0b05vZGVzKGVsIHx8IHRoaXMuJGVsKTtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmNsc1xuICAgICAgICAgICAgICAgID8gaGFzQ2xhc3Mobm9kZXMsIHRoaXMuY2xzLnNwbGl0KCcgJylbMF0pXG4gICAgICAgICAgICAgICAgOiAhaGFzQXR0cihub2RlcywgJ2hpZGRlbicpO1xuICAgICAgICB9LFxuXG4gICAgICAgIHVwZGF0ZUFyaWEoZWwpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLmNscyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICBhdHRyKGVsLCAnYXJpYS1oaWRkZW4nLCAhdGhpcy5pc1RvZ2dsZWQoZWwpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICBfdG9nZ2xlRWxlbWVudChlbCwgc2hvdywgYW5pbWF0ZSkge1xuXG4gICAgICAgICAgICBzaG93ID0gaXNCb29sZWFuKHNob3cpXG4gICAgICAgICAgICAgICAgPyBzaG93XG4gICAgICAgICAgICAgICAgOiBBbmltYXRpb24uaW5Qcm9ncmVzcyhlbClcbiAgICAgICAgICAgICAgICAgICAgPyBoYXNDbGFzcyhlbCwgJ3VrLWFuaW1hdGlvbi1sZWF2ZScpXG4gICAgICAgICAgICAgICAgICAgIDogVHJhbnNpdGlvbi5pblByb2dyZXNzKGVsKVxuICAgICAgICAgICAgICAgICAgICAgICAgPyBlbC5zdHlsZS5oZWlnaHQgPT09ICcwcHgnXG4gICAgICAgICAgICAgICAgICAgICAgICA6ICF0aGlzLmlzVG9nZ2xlZChlbCk7XG5cbiAgICAgICAgICAgIGlmICghdHJpZ2dlcihlbCwgYGJlZm9yZSR7c2hvdyA/ICdzaG93JyA6ICdoaWRlJ31gLCBbdGhpc10pKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVqZWN0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IHByb21pc2UgPSAoXG4gICAgICAgICAgICAgICAgaXNGdW5jdGlvbihhbmltYXRlKVxuICAgICAgICAgICAgICAgICAgICA/IGFuaW1hdGVcbiAgICAgICAgICAgICAgICAgICAgOiBhbmltYXRlID09PSBmYWxzZSB8fCAhdGhpcy5oYXNBbmltYXRpb25cbiAgICAgICAgICAgICAgICAgICAgICAgID8gdGhpcy5fdG9nZ2xlXG4gICAgICAgICAgICAgICAgICAgICAgICA6IHRoaXMuaGFzVHJhbnNpdGlvblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgID8gdG9nZ2xlSGVpZ2h0KHRoaXMpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgOiB0b2dnbGVBbmltYXRpb24odGhpcylcbiAgICAgICAgICAgICkoZWwsIHNob3cpO1xuXG4gICAgICAgICAgICB0cmlnZ2VyKGVsLCBzaG93ID8gJ3Nob3cnIDogJ2hpZGUnLCBbdGhpc10pO1xuXG4gICAgICAgICAgICBjb25zdCBmaW5hbCA9ICgpID0+IHtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKGVsLCBzaG93ID8gJ3Nob3duJyA6ICdoaWRkZW4nLCBbdGhpc10pO1xuICAgICAgICAgICAgICAgIHRoaXMuJHVwZGF0ZShlbCk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICByZXR1cm4gcHJvbWlzZSA/IHByb21pc2UudGhlbihmaW5hbCkgOiBQcm9taXNlLnJlc29sdmUoZmluYWwoKSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgX3RvZ2dsZShlbCwgdG9nZ2xlZCkge1xuXG4gICAgICAgICAgICBpZiAoIWVsKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0b2dnbGVkID0gQm9vbGVhbih0b2dnbGVkKTtcblxuICAgICAgICAgICAgbGV0IGNoYW5nZWQ7XG4gICAgICAgICAgICBpZiAodGhpcy5jbHMpIHtcbiAgICAgICAgICAgICAgICBjaGFuZ2VkID0gaW5jbHVkZXModGhpcy5jbHMsICcgJykgfHwgdG9nZ2xlZCAhPT0gaGFzQ2xhc3MoZWwsIHRoaXMuY2xzKTtcbiAgICAgICAgICAgICAgICBjaGFuZ2VkICYmIHRvZ2dsZUNsYXNzKGVsLCB0aGlzLmNscywgaW5jbHVkZXModGhpcy5jbHMsICcgJykgPyB1bmRlZmluZWQgOiB0b2dnbGVkKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgY2hhbmdlZCA9IHRvZ2dsZWQgPT09IGhhc0F0dHIoZWwsICdoaWRkZW4nKTtcbiAgICAgICAgICAgICAgICBjaGFuZ2VkICYmIGF0dHIoZWwsICdoaWRkZW4nLCAhdG9nZ2xlZCA/ICcnIDogbnVsbCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQkKCdbYXV0b2ZvY3VzXScsIGVsKS5zb21lKGVsID0+IGlzVmlzaWJsZShlbCkgPyBlbC5mb2N1cygpIHx8IHRydWUgOiBlbC5ibHVyKCkpO1xuXG4gICAgICAgICAgICB0aGlzLnVwZGF0ZUFyaWEoZWwpO1xuICAgICAgICAgICAgY2hhbmdlZCAmJiB0aGlzLiR1cGRhdGUoZWwpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmZ1bmN0aW9uIHRvZ2dsZUhlaWdodCh7aXNUb2dnbGVkLCBkdXJhdGlvbiwgaW5pdFByb3BzLCBoaWRlUHJvcHMsIHRyYW5zaXRpb24sIF90b2dnbGV9KSB7XG4gICAgcmV0dXJuIChlbCwgc2hvdykgPT4ge1xuXG4gICAgICAgIGNvbnN0IGluUHJvZ3Jlc3MgPSBUcmFuc2l0aW9uLmluUHJvZ3Jlc3MoZWwpO1xuICAgICAgICBjb25zdCBpbm5lciA9IGVsLmhhc0NoaWxkTm9kZXMgPyB0b0Zsb2F0KGNzcyhlbC5maXJzdEVsZW1lbnRDaGlsZCwgJ21hcmdpblRvcCcpKSArIHRvRmxvYXQoY3NzKGVsLmxhc3RFbGVtZW50Q2hpbGQsICdtYXJnaW5Cb3R0b20nKSkgOiAwO1xuICAgICAgICBjb25zdCBjdXJyZW50SGVpZ2h0ID0gaXNWaXNpYmxlKGVsKSA/IGhlaWdodChlbCkgKyAoaW5Qcm9ncmVzcyA/IDAgOiBpbm5lcikgOiAwO1xuXG4gICAgICAgIFRyYW5zaXRpb24uY2FuY2VsKGVsKTtcblxuICAgICAgICBpZiAoIWlzVG9nZ2xlZChlbCkpIHtcbiAgICAgICAgICAgIF90b2dnbGUoZWwsIHRydWUpO1xuICAgICAgICB9XG5cbiAgICAgICAgaGVpZ2h0KGVsLCAnJyk7XG5cbiAgICAgICAgLy8gVXBkYXRlIGNoaWxkIGNvbXBvbmVudHMgZmlyc3RcbiAgICAgICAgZmFzdGRvbS5mbHVzaCgpO1xuXG4gICAgICAgIGNvbnN0IGVuZEhlaWdodCA9IGhlaWdodChlbCkgKyAoaW5Qcm9ncmVzcyA/IDAgOiBpbm5lcik7XG4gICAgICAgIGhlaWdodChlbCwgY3VycmVudEhlaWdodCk7XG5cbiAgICAgICAgcmV0dXJuIChzaG93XG4gICAgICAgICAgICAgICAgPyBUcmFuc2l0aW9uLnN0YXJ0KGVsLCBhc3NpZ24oe30sIGluaXRQcm9wcywge292ZXJmbG93OiAnaGlkZGVuJywgaGVpZ2h0OiBlbmRIZWlnaHR9KSwgTWF0aC5yb3VuZChkdXJhdGlvbiAqICgxIC0gY3VycmVudEhlaWdodCAvIGVuZEhlaWdodCkpLCB0cmFuc2l0aW9uKVxuICAgICAgICAgICAgICAgIDogVHJhbnNpdGlvbi5zdGFydChlbCwgaGlkZVByb3BzLCBNYXRoLnJvdW5kKGR1cmF0aW9uICogKGN1cnJlbnRIZWlnaHQgLyBlbmRIZWlnaHQpKSwgdHJhbnNpdGlvbikudGhlbigoKSA9PiBfdG9nZ2xlKGVsLCBmYWxzZSkpXG4gICAgICAgICkudGhlbigoKSA9PiBjc3MoZWwsIGluaXRQcm9wcykpO1xuXG4gICAgfTtcbn1cblxuZnVuY3Rpb24gdG9nZ2xlQW5pbWF0aW9uKHthbmltYXRpb24sIGR1cmF0aW9uLCBvcmlnaW4sIF90b2dnbGV9KSB7XG4gICAgcmV0dXJuIChlbCwgc2hvdykgPT4ge1xuXG4gICAgICAgIEFuaW1hdGlvbi5jYW5jZWwoZWwpO1xuXG4gICAgICAgIGlmIChzaG93KSB7XG4gICAgICAgICAgICBfdG9nZ2xlKGVsLCB0cnVlKTtcbiAgICAgICAgICAgIHJldHVybiBBbmltYXRpb24uaW4oZWwsIGFuaW1hdGlvblswXSwgZHVyYXRpb24sIG9yaWdpbik7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gQW5pbWF0aW9uLm91dChlbCwgYW5pbWF0aW9uWzFdIHx8IGFuaW1hdGlvblswXSwgZHVyYXRpb24sIG9yaWdpbikudGhlbigoKSA9PiBfdG9nZ2xlKGVsLCBmYWxzZSkpO1xuICAgIH07XG59XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IFRvZ2dsYWJsZSBmcm9tICcuLi9taXhpbi90b2dnbGFibGUnO1xuaW1wb3J0IHskLCAkJCwgYXR0ciwgZmlsdGVyLCBnZXRJbmRleCwgaGFzQ2xhc3MsIGluY2x1ZGVzLCBpbmRleCwgdG9nZ2xlQ2xhc3MsIHVud3JhcCwgd3JhcEFsbH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBUb2dnbGFibGVdLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgdGFyZ2V0czogU3RyaW5nLFxuICAgICAgICBhY3RpdmU6IG51bGwsXG4gICAgICAgIGNvbGxhcHNpYmxlOiBCb29sZWFuLFxuICAgICAgICBtdWx0aXBsZTogQm9vbGVhbixcbiAgICAgICAgdG9nZ2xlOiBTdHJpbmcsXG4gICAgICAgIGNvbnRlbnQ6IFN0cmluZyxcbiAgICAgICAgdHJhbnNpdGlvbjogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdGFyZ2V0czogJz4gKicsXG4gICAgICAgIGFjdGl2ZTogZmFsc2UsXG4gICAgICAgIGFuaW1hdGlvbjogW3RydWVdLFxuICAgICAgICBjb2xsYXBzaWJsZTogdHJ1ZSxcbiAgICAgICAgbXVsdGlwbGU6IGZhbHNlLFxuICAgICAgICBjbHNPcGVuOiAndWstb3BlbicsXG4gICAgICAgIHRvZ2dsZTogJz4gLnVrLWFjY29yZGlvbi10aXRsZScsXG4gICAgICAgIGNvbnRlbnQ6ICc+IC51ay1hY2NvcmRpb24tY29udGVudCcsXG4gICAgICAgIHRyYW5zaXRpb246ICdlYXNlJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGl0ZW1zKHt0YXJnZXRzfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJCQodGFyZ2V0cywgJGVsKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGAke3RoaXMudGFyZ2V0c30gJHt0aGlzLiRwcm9wcy50b2dnbGV9YDtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZShpbmRleCgkJChgJHt0aGlzLnRhcmdldHN9ICR7dGhpcy4kcHJvcHMudG9nZ2xlfWAsIHRoaXMuJGVsKSwgZS5jdXJyZW50KSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgXSxcblxuICAgIGNvbm5lY3RlZCgpIHtcblxuICAgICAgICBpZiAodGhpcy5hY3RpdmUgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBhY3RpdmUgPSB0aGlzLml0ZW1zW051bWJlcih0aGlzLmFjdGl2ZSldO1xuICAgICAgICBpZiAoYWN0aXZlICYmICFoYXNDbGFzcyhhY3RpdmUsIHRoaXMuY2xzT3BlbikpIHtcbiAgICAgICAgICAgIHRoaXMudG9nZ2xlKGFjdGl2ZSwgZmFsc2UpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIHVwZGF0ZSgpIHtcblxuICAgICAgICB0aGlzLml0ZW1zLmZvckVhY2goZWwgPT4gdGhpcy5fdG9nZ2xlKCQodGhpcy5jb250ZW50LCBlbCksIGhhc0NsYXNzKGVsLCB0aGlzLmNsc09wZW4pKSk7XG5cbiAgICAgICAgY29uc3QgYWN0aXZlID0gIXRoaXMuY29sbGFwc2libGUgJiYgIWhhc0NsYXNzKHRoaXMuaXRlbXMsIHRoaXMuY2xzT3BlbikgJiYgdGhpcy5pdGVtc1swXTtcbiAgICAgICAgaWYgKGFjdGl2ZSkge1xuICAgICAgICAgICAgdGhpcy50b2dnbGUoYWN0aXZlLCBmYWxzZSk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIHRvZ2dsZShpdGVtLCBhbmltYXRlKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGluZGV4ID0gZ2V0SW5kZXgoaXRlbSwgdGhpcy5pdGVtcyk7XG4gICAgICAgICAgICBjb25zdCBhY3RpdmUgPSBmaWx0ZXIodGhpcy5pdGVtcywgYC4ke3RoaXMuY2xzT3Blbn1gKTtcblxuICAgICAgICAgICAgaXRlbSA9IHRoaXMuaXRlbXNbaW5kZXhdO1xuXG4gICAgICAgICAgICBpdGVtICYmIFtpdGVtXVxuICAgICAgICAgICAgICAgIC5jb25jYXQoIXRoaXMubXVsdGlwbGUgJiYgIWluY2x1ZGVzKGFjdGl2ZSwgaXRlbSkgJiYgYWN0aXZlIHx8IFtdKVxuICAgICAgICAgICAgICAgIC5mb3JFYWNoKGVsID0+IHtcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBpc0l0ZW0gPSBlbCA9PT0gaXRlbTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3Qgc3RhdGUgPSBpc0l0ZW0gJiYgIWhhc0NsYXNzKGVsLCB0aGlzLmNsc09wZW4pO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICghc3RhdGUgJiYgaXNJdGVtICYmICF0aGlzLmNvbGxhcHNpYmxlICYmIGFjdGl2ZS5sZW5ndGggPCAyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyhlbCwgdGhpcy5jbHNPcGVuLCBzdGF0ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgY29udGVudCA9IGVsLl93cmFwcGVyID8gZWwuX3dyYXBwZXIuZmlyc3RFbGVtZW50Q2hpbGQgOiAkKHRoaXMuY29udGVudCwgZWwpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICghZWwuX3dyYXBwZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVsLl93cmFwcGVyID0gd3JhcEFsbChjb250ZW50LCAnPGRpdj4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGF0dHIoZWwuX3dyYXBwZXIsICdoaWRkZW4nLCBzdGF0ZSA/ICcnIDogbnVsbCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB0aGlzLl90b2dnbGUoY29udGVudCwgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlRWxlbWVudChlbC5fd3JhcHBlciwgc3RhdGUsIGFuaW1hdGUpLnRoZW4oKCkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaGFzQ2xhc3MoZWwsIHRoaXMuY2xzT3BlbikgIT09IHN0YXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXN0YXRlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5fdG9nZ2xlKGNvbnRlbnQsIGZhbHNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgZWwuX3dyYXBwZXIgPSBudWxsO1xuICAgICAgICAgICAgICAgICAgICAgICAgdW53cmFwKGNvbnRlbnQpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQgVG9nZ2xhYmxlIGZyb20gJy4uL21peGluL3RvZ2dsYWJsZSc7XG5pbXBvcnQge2Fzc2lnbn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBUb2dnbGFibGVdLFxuXG4gICAgYXJnczogJ2FuaW1hdGlvbicsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBjbG9zZTogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgYW5pbWF0aW9uOiBbdHJ1ZV0sXG4gICAgICAgIHNlbENsb3NlOiAnLnVrLWFsZXJ0LWNsb3NlJyxcbiAgICAgICAgZHVyYXRpb246IDE1MCxcbiAgICAgICAgaGlkZVByb3BzOiBhc3NpZ24oe29wYWNpdHk6IDB9LCBUb2dnbGFibGUuZGF0YS5oaWRlUHJvcHMpXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuc2VsQ2xvc2U7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5jbG9zZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgY2xvc2UoKSB7XG4gICAgICAgICAgICB0aGlzLnRvZ2dsZUVsZW1lbnQodGhpcy4kZWwpLnRoZW4oKCkgPT4gdGhpcy4kZGVzdHJveSh0cnVlKSk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCB7Y3NzLCBmYXN0ZG9tLCBnZXRFdmVudFBvcywgaXNUb3VjaCwgb24sIG9uY2UsIHBvaW50ZXJEb3duLCBwb2ludGVyVXAsIHJlYWR5LCB0b01zLCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gKFVJa2l0KSB7XG5cbiAgICByZWFkeSgoKSA9PiB7XG5cbiAgICAgICAgVUlraXQudXBkYXRlKCk7XG4gICAgICAgIG9uKHdpbmRvdywgJ2xvYWQgcmVzaXplJywgKCkgPT4gVUlraXQudXBkYXRlKG51bGwsICdyZXNpemUnKSk7XG4gICAgICAgIG9uKGRvY3VtZW50LCAnbG9hZGVkbWV0YWRhdGEgbG9hZCcsICh7dGFyZ2V0fSkgPT4gVUlraXQudXBkYXRlKHRhcmdldCwgJ3Jlc2l6ZScpLCB0cnVlKTtcblxuICAgICAgICAvLyB0aHJvdHRsZSBgc2Nyb2xsYCBldmVudCAoU2FmYXJpIHRyaWdnZXJzIG11bHRpcGxlIGBzY3JvbGxgIGV2ZW50cyBwZXIgZnJhbWUpXG4gICAgICAgIGxldCBwZW5kaW5nO1xuICAgICAgICBvbih3aW5kb3csICdzY3JvbGwnLCBlID0+IHtcblxuICAgICAgICAgICAgaWYgKHBlbmRpbmcpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBwZW5kaW5nID0gdHJ1ZTtcbiAgICAgICAgICAgIGZhc3Rkb20ud3JpdGUoKCkgPT4gcGVuZGluZyA9IGZhbHNlKTtcblxuICAgICAgICAgICAgY29uc3Qge3RhcmdldH0gPSBlO1xuICAgICAgICAgICAgVUlraXQudXBkYXRlKHRhcmdldC5ub2RlVHlwZSAhPT0gMSA/IGRvY3VtZW50LmJvZHkgOiB0YXJnZXQsIGUudHlwZSk7XG5cbiAgICAgICAgfSwge3Bhc3NpdmU6IHRydWUsIGNhcHR1cmU6IHRydWV9KTtcblxuICAgICAgICBsZXQgc3RhcnRlZCA9IDA7XG4gICAgICAgIG9uKGRvY3VtZW50LCAnYW5pbWF0aW9uc3RhcnQnLCAoe3RhcmdldH0pID0+IHtcbiAgICAgICAgICAgIGlmICgoY3NzKHRhcmdldCwgJ2FuaW1hdGlvbk5hbWUnKSB8fCAnJykubWF0Y2goL151ay0uKihsZWZ0fHJpZ2h0KS8pKSB7XG5cbiAgICAgICAgICAgICAgICBzdGFydGVkKys7XG4gICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmJvZHksICdvdmVyZmxvd1gnLCAnaGlkZGVuJyk7XG4gICAgICAgICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmICghLS1zdGFydGVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ292ZXJmbG93WCcsICcnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0sIHRvTXMoY3NzKHRhcmdldCwgJ2FuaW1hdGlvbkR1cmF0aW9uJykpICsgMTAwKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSwgdHJ1ZSk7XG5cbiAgICAgICAgbGV0IG9mZjtcbiAgICAgICAgb24oZG9jdW1lbnQsIHBvaW50ZXJEb3duLCBlID0+IHtcblxuICAgICAgICAgICAgb2ZmICYmIG9mZigpO1xuXG4gICAgICAgICAgICBpZiAoIWlzVG91Y2goZSkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IHBvcyA9IGdldEV2ZW50UG9zKGUpO1xuICAgICAgICAgICAgY29uc3QgdGFyZ2V0ID0gJ3RhZ05hbWUnIGluIGUudGFyZ2V0ID8gZS50YXJnZXQgOiBlLnRhcmdldC5wYXJlbnROb2RlO1xuICAgICAgICAgICAgb2ZmID0gb25jZShkb2N1bWVudCwgcG9pbnRlclVwLCBlID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHt4LCB5fSA9IGdldEV2ZW50UG9zKGUpO1xuXG4gICAgICAgICAgICAgICAgLy8gc3dpcGVcbiAgICAgICAgICAgICAgICBpZiAodGFyZ2V0ICYmIHggJiYgTWF0aC5hYnMocG9zLnggLSB4KSA+IDEwMCB8fCB5ICYmIE1hdGguYWJzKHBvcy55IC0geSkgPiAxMDApIHtcblxuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGFyZ2V0LCAnc3dpcGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGFyZ2V0LCBgc3dpcGUke3N3aXBlRGlyZWN0aW9uKHBvcy54LCBwb3MueSwgeCwgeSl9YCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSwge3Bhc3NpdmU6IHRydWV9KTtcblxuICAgIH0pO1xuXG59XG5cbmZ1bmN0aW9uIHN3aXBlRGlyZWN0aW9uKHgxLCB5MSwgeDIsIHkyKSB7XG4gICAgcmV0dXJuIE1hdGguYWJzKHgxIC0geDIpID49IE1hdGguYWJzKHkxIC0geTIpXG4gICAgICAgID8geDEgLSB4MiA+IDBcbiAgICAgICAgICAgID8gJ0xlZnQnXG4gICAgICAgICAgICA6ICdSaWdodCdcbiAgICAgICAgOiB5MSAtIHkyID4gMFxuICAgICAgICAgICAgPyAnVXAnXG4gICAgICAgICAgICA6ICdEb3duJztcbn1cbiIsImltcG9ydCB7Y3NzLCBoYXNBdHRyLCBpc0luVmlldywgaXNWaXNpYmxlLCBQbGF5ZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBhcmdzOiAnYXV0b3BsYXknLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgYXV0b211dGU6IEJvb2xlYW4sXG4gICAgICAgIGF1dG9wbGF5OiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgYXV0b211dGU6IGZhbHNlLFxuICAgICAgICBhdXRvcGxheTogdHJ1ZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGluVmlldyh7YXV0b3BsYXl9KSB7XG4gICAgICAgICAgICByZXR1cm4gYXV0b3BsYXkgPT09ICdpbnZpZXcnO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgY29ubmVjdGVkKCkge1xuXG4gICAgICAgIGlmICh0aGlzLmluVmlldyAmJiAhaGFzQXR0cih0aGlzLiRlbCwgJ3ByZWxvYWQnKSkge1xuICAgICAgICAgICAgdGhpcy4kZWwucHJlbG9hZCA9ICdub25lJztcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMucGxheWVyID0gbmV3IFBsYXllcih0aGlzLiRlbCk7XG5cbiAgICAgICAgaWYgKHRoaXMuYXV0b211dGUpIHtcbiAgICAgICAgICAgIHRoaXMucGxheWVyLm11dGUoKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG5cbiAgICAgICAgICAgIHJldHVybiAhdGhpcy5wbGF5ZXJcbiAgICAgICAgICAgICAgICA/IGZhbHNlXG4gICAgICAgICAgICAgICAgOiB7XG4gICAgICAgICAgICAgICAgICAgIHZpc2libGU6IGlzVmlzaWJsZSh0aGlzLiRlbCkgJiYgY3NzKHRoaXMuJGVsLCAndmlzaWJpbGl0eScpICE9PSAnaGlkZGVuJyxcbiAgICAgICAgICAgICAgICAgICAgaW5WaWV3OiB0aGlzLmluVmlldyAmJiBpc0luVmlldyh0aGlzLiRlbClcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKHt2aXNpYmxlLCBpblZpZXd9KSB7XG5cbiAgICAgICAgICAgIGlmICghdmlzaWJsZSB8fCB0aGlzLmluVmlldyAmJiAhaW5WaWV3KSB7XG4gICAgICAgICAgICAgICAgdGhpcy5wbGF5ZXIucGF1c2UoKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5hdXRvcGxheSA9PT0gdHJ1ZSB8fCB0aGlzLmluVmlldyAmJiBpblZpZXcpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnBsYXllci5wbGF5KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJywgJ3Njcm9sbCddXG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgVmlkZW8gZnJvbSAnLi92aWRlbyc7XG5pbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IHtjc3MsIERpbWVuc2lvbnMsIGlzVmlzaWJsZX0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBWaWRlb10sXG5cbiAgICBwcm9wczoge1xuICAgICAgICB3aWR0aDogTnVtYmVyLFxuICAgICAgICBoZWlnaHQ6IE51bWJlclxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGF1dG9tdXRlOiB0cnVlXG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGVsID0gdGhpcy4kZWw7XG5cbiAgICAgICAgICAgIGlmICghaXNWaXNpYmxlKGVsKSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3Qge29mZnNldEhlaWdodDogaGVpZ2h0LCBvZmZzZXRXaWR0aDogd2lkdGh9ID0gZWwucGFyZW50Tm9kZTtcblxuICAgICAgICAgICAgcmV0dXJuIHtoZWlnaHQsIHdpZHRofTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7aGVpZ2h0LCB3aWR0aH0pIHtcblxuICAgICAgICAgICAgY29uc3QgZWwgPSB0aGlzLiRlbDtcbiAgICAgICAgICAgIGNvbnN0IGVsV2lkdGggPSB0aGlzLndpZHRoIHx8IGVsLm5hdHVyYWxXaWR0aCB8fCBlbC52aWRlb1dpZHRoIHx8IGVsLmNsaWVudFdpZHRoO1xuICAgICAgICAgICAgY29uc3QgZWxIZWlnaHQgPSB0aGlzLmhlaWdodCB8fCBlbC5uYXR1cmFsSGVpZ2h0IHx8IGVsLnZpZGVvSGVpZ2h0IHx8IGVsLmNsaWVudEhlaWdodDtcblxuICAgICAgICAgICAgaWYgKCFlbFdpZHRoIHx8ICFlbEhlaWdodCkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY3NzKGVsLCBEaW1lbnNpb25zLmNvdmVyKFxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgd2lkdGg6IGVsV2lkdGgsXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodDogZWxIZWlnaHRcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgd2lkdGg6IHdpZHRoICsgKHdpZHRoICUgMiA/IDEgOiAwKSxcbiAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiBoZWlnaHQgKyAoaGVpZ2h0ICUgMiA/IDEgOiAwKVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICkpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgeyQsIGNzcywgZmxpcFBvc2l0aW9uLCBpbmNsdWRlcywgaXNOdW1lcmljLCBpc1J0bCwgb2Zmc2V0IGFzIGdldE9mZnNldCwgcG9zaXRpb25BdCwgcmVtb3ZlQ2xhc3NlcywgdG9nZ2xlQ2xhc3N9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBwcm9wczoge1xuICAgICAgICBwb3M6IFN0cmluZyxcbiAgICAgICAgb2Zmc2V0OiBudWxsLFxuICAgICAgICBmbGlwOiBCb29sZWFuLFxuICAgICAgICBjbHNQb3M6IFN0cmluZ1xuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIHBvczogYGJvdHRvbS0keyFpc1J0bCA/ICdsZWZ0JyA6ICdyaWdodCd9YCxcbiAgICAgICAgZmxpcDogdHJ1ZSxcbiAgICAgICAgb2Zmc2V0OiBmYWxzZSxcbiAgICAgICAgY2xzUG9zOiAnJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIHBvcyh7cG9zfSkge1xuICAgICAgICAgICAgcmV0dXJuIChwb3MgKyAoIWluY2x1ZGVzKHBvcywgJy0nKSA/ICctY2VudGVyJyA6ICcnKSkuc3BsaXQoJy0nKTtcbiAgICAgICAgfSxcblxuICAgICAgICBkaXIoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5wb3NbMF07XG4gICAgICAgIH0sXG5cbiAgICAgICAgYWxpZ24oKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5wb3NbMV07XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgcG9zaXRpb25BdChlbGVtZW50LCB0YXJnZXQsIGJvdW5kYXJ5KSB7XG5cbiAgICAgICAgICAgIHJlbW92ZUNsYXNzZXMoZWxlbWVudCwgYCR7dGhpcy5jbHNQb3N9LSh0b3B8Ym90dG9tfGxlZnR8cmlnaHQpKC1bYS16XSspP2ApO1xuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIHt0b3A6ICcnLCBsZWZ0OiAnJ30pO1xuXG4gICAgICAgICAgICBsZXQgbm9kZTtcbiAgICAgICAgICAgIGxldCB7b2Zmc2V0fSA9IHRoaXM7XG4gICAgICAgICAgICBjb25zdCBheGlzID0gdGhpcy5nZXRBeGlzKCk7XG5cbiAgICAgICAgICAgIGlmICghaXNOdW1lcmljKG9mZnNldCkpIHtcbiAgICAgICAgICAgICAgICBub2RlID0gJChvZmZzZXQpO1xuICAgICAgICAgICAgICAgIG9mZnNldCA9IG5vZGVcbiAgICAgICAgICAgICAgICAgICAgPyBnZXRPZmZzZXQobm9kZSlbYXhpcyA9PT0gJ3gnID8gJ2xlZnQnIDogJ3RvcCddIC0gZ2V0T2Zmc2V0KHRhcmdldClbYXhpcyA9PT0gJ3gnID8gJ3JpZ2h0JyA6ICdib3R0b20nXVxuICAgICAgICAgICAgICAgICAgICA6IDA7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IHt4LCB5fSA9IHBvc2l0aW9uQXQoXG4gICAgICAgICAgICAgICAgZWxlbWVudCxcbiAgICAgICAgICAgICAgICB0YXJnZXQsXG4gICAgICAgICAgICAgICAgYXhpcyA9PT0gJ3gnID8gYCR7ZmxpcFBvc2l0aW9uKHRoaXMuZGlyKX0gJHt0aGlzLmFsaWdufWAgOiBgJHt0aGlzLmFsaWdufSAke2ZsaXBQb3NpdGlvbih0aGlzLmRpcil9YCxcbiAgICAgICAgICAgICAgICBheGlzID09PSAneCcgPyBgJHt0aGlzLmRpcn0gJHt0aGlzLmFsaWdufWAgOiBgJHt0aGlzLmFsaWdufSAke3RoaXMuZGlyfWAsXG4gICAgICAgICAgICAgICAgYXhpcyA9PT0gJ3gnID8gYCR7dGhpcy5kaXIgPT09ICdsZWZ0JyA/IC1vZmZzZXQgOiBvZmZzZXR9YCA6IGAgJHt0aGlzLmRpciA9PT0gJ3RvcCcgPyAtb2Zmc2V0IDogb2Zmc2V0fWAsXG4gICAgICAgICAgICAgICAgbnVsbCxcbiAgICAgICAgICAgICAgICB0aGlzLmZsaXAsXG4gICAgICAgICAgICAgICAgYm91bmRhcnlcbiAgICAgICAgICAgICkudGFyZ2V0O1xuXG4gICAgICAgICAgICB0aGlzLmRpciA9IGF4aXMgPT09ICd4JyA/IHggOiB5O1xuICAgICAgICAgICAgdGhpcy5hbGlnbiA9IGF4aXMgPT09ICd4JyA/IHkgOiB4O1xuXG4gICAgICAgICAgICB0b2dnbGVDbGFzcyhlbGVtZW50LCBgJHt0aGlzLmNsc1Bvc30tJHt0aGlzLmRpcn0tJHt0aGlzLmFsaWdufWAsIHRoaXMub2Zmc2V0ID09PSBmYWxzZSk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICBnZXRBeGlzKCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuZGlyID09PSAndG9wJyB8fCB0aGlzLmRpciA9PT0gJ2JvdHRvbScgPyAneScgOiAneCc7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCBQb3NpdGlvbiBmcm9tICcuLi9taXhpbi9wb3NpdGlvbic7XG5pbXBvcnQgVG9nZ2xhYmxlIGZyb20gJy4uL21peGluL3RvZ2dsYWJsZSc7XG5pbXBvcnQge2FkZENsYXNzLCBBbmltYXRpb24sIGF0dHIsIGNzcywgaW5jbHVkZXMsIGlzVG91Y2gsIE1vdXNlVHJhY2tlciwgb2Zmc2V0LCBvbiwgb25jZSwgcG9pbnRlckVudGVyLCBwb2ludGVyTGVhdmUsIHBvaW50ZXJVcCwgcG9pbnRJblJlY3QsIHF1ZXJ5LCByZW1vdmVDbGFzc2VzLCB0b2dnbGVDbGFzcywgdHJpZ2dlciwgd2l0aGlufSBmcm9tICd1aWtpdC11dGlsJztcblxubGV0IGFjdGl2ZTtcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbUG9zaXRpb24sIFRvZ2dsYWJsZV0sXG5cbiAgICBhcmdzOiAncG9zJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1vZGU6ICdsaXN0JyxcbiAgICAgICAgdG9nZ2xlOiBCb29sZWFuLFxuICAgICAgICBib3VuZGFyeTogQm9vbGVhbixcbiAgICAgICAgYm91bmRhcnlBbGlnbjogQm9vbGVhbixcbiAgICAgICAgZGVsYXlTaG93OiBOdW1iZXIsXG4gICAgICAgIGRlbGF5SGlkZTogTnVtYmVyLFxuICAgICAgICBjbHNEcm9wOiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtb2RlOiBbJ2NsaWNrJywgJ2hvdmVyJ10sXG4gICAgICAgIHRvZ2dsZTogJy0gKicsXG4gICAgICAgIGJvdW5kYXJ5OiB3aW5kb3csXG4gICAgICAgIGJvdW5kYXJ5QWxpZ246IGZhbHNlLFxuICAgICAgICBkZWxheVNob3c6IDAsXG4gICAgICAgIGRlbGF5SGlkZTogODAwLFxuICAgICAgICBjbHNEcm9wOiBmYWxzZSxcbiAgICAgICAgaG92ZXJJZGxlOiAyMDAsXG4gICAgICAgIGFuaW1hdGlvbjogWyd1ay1hbmltYXRpb24tZmFkZSddLFxuICAgICAgICBjbHM6ICd1ay1vcGVuJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGJvdW5kYXJ5KHtib3VuZGFyeX0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHF1ZXJ5KGJvdW5kYXJ5LCAkZWwpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc0Ryb3Aoe2Nsc0Ryb3B9KSB7XG4gICAgICAgICAgICByZXR1cm4gY2xzRHJvcCB8fCBgdWstJHt0aGlzLiRvcHRpb25zLm5hbWV9YDtcbiAgICAgICAgfSxcblxuICAgICAgICBjbHNQb3MoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5jbHNEcm9wO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgY3JlYXRlZCgpIHtcbiAgICAgICAgdGhpcy50cmFja2VyID0gbmV3IE1vdXNlVHJhY2tlcigpO1xuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzRHJvcCk7XG5cbiAgICAgICAgY29uc3Qge3RvZ2dsZX0gPSB0aGlzLiRwcm9wcztcbiAgICAgICAgdGhpcy50b2dnbGUgPSB0b2dnbGUgJiYgdGhpcy4kY3JlYXRlKCd0b2dnbGUnLCBxdWVyeSh0b2dnbGUsIHRoaXMuJGVsKSwge1xuICAgICAgICAgICAgdGFyZ2V0OiB0aGlzLiRlbCxcbiAgICAgICAgICAgIG1vZGU6IHRoaXMubW9kZVxuICAgICAgICB9KTtcblxuICAgICAgICAhdGhpcy50b2dnbGUgJiYgdHJpZ2dlcih0aGlzLiRlbCwgJ3VwZGF0ZWFyaWEnKTtcblxuICAgIH0sXG5cbiAgICBldmVudHM6IFtcblxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGAuJHt0aGlzLmNsc0Ryb3B9LWNsb3NlYDtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gJ2FbaHJlZl49XCIjXCJdJztcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgaWQgPSBlLnRhcmdldC5oYXNoO1xuXG4gICAgICAgICAgICAgICAgaWYgKCFpZCkge1xuICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCFpZCB8fCAhd2l0aGluKGlkLCB0aGlzLiRlbCkpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKGZhbHNlKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdiZWZvcmVzY3JvbGwnLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuICAgICAgICAgICAgICAgIHRoaXMuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICd0b2dnbGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUsIHRvZ2dsZSkge1xuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaXNUb2dnbGVkKCkpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKGZhbHNlKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3codG9nZ2xlLCBmYWxzZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiBwb2ludGVyRW50ZXIsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gaW5jbHVkZXModGhpcy5tb2RlLCAnaG92ZXInKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGlzVG91Y2goZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChhY3RpdmVcbiAgICAgICAgICAgICAgICAgICAgJiYgYWN0aXZlICE9PSB0aGlzXG4gICAgICAgICAgICAgICAgICAgICYmIGFjdGl2ZS50b2dnbGVcbiAgICAgICAgICAgICAgICAgICAgJiYgaW5jbHVkZXMoYWN0aXZlLnRvZ2dsZS5tb2RlLCAnaG92ZXInKVxuICAgICAgICAgICAgICAgICAgICAmJiAhd2l0aGluKGUudGFyZ2V0LCBhY3RpdmUudG9nZ2xlLiRlbClcbiAgICAgICAgICAgICAgICAgICAgJiYgIXBvaW50SW5SZWN0KHt4OiBlLnBhZ2VYLCB5OiBlLnBhZ2VZfSwgb2Zmc2V0KGFjdGl2ZS4kZWwpKVxuICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvdyh0aGlzLnRvZ2dsZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICd0b2dnbGVzaG93JyxcblxuICAgICAgICAgICAgaGFuZGxlcihlLCB0b2dnbGUpIHtcblxuICAgICAgICAgICAgICAgIGlmICh0b2dnbGUgJiYgIWluY2x1ZGVzKHRvZ2dsZS50YXJnZXQsIHRoaXMuJGVsKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvdyh0b2dnbGUgfHwgdGhpcy50b2dnbGUpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiBgdG9nZ2xlaGlkZSAke3BvaW50ZXJMZWF2ZX1gLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUsIHRvZ2dsZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGlzVG91Y2goZSkgfHwgdG9nZ2xlICYmICFpbmNsdWRlcyh0b2dnbGUudGFyZ2V0LCB0aGlzLiRlbCkpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLnRvZ2dsZSAmJiBpbmNsdWRlcyh0aGlzLnRvZ2dsZS5tb2RlLCAnaG92ZXInKSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmhpZGUoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdiZWZvcmVzaG93JyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmNsZWFyVGltZXJzKCk7XG4gICAgICAgICAgICAgICAgQW5pbWF0aW9uLmNhbmNlbCh0aGlzLiRlbCk7XG4gICAgICAgICAgICAgICAgdGhpcy5wb3NpdGlvbigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnc2hvdycsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgdGhpcy50cmFja2VyLmluaXQoKTtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAndXBkYXRlYXJpYScpO1xuICAgICAgICAgICAgICAgIHJlZ2lzdGVyRXZlbnQoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2JlZm9yZWhpZGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuICAgICAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2hpZGUnLFxuXG4gICAgICAgICAgICBoYW5kbGVyKHt0YXJnZXR9KSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy4kZWwgIT09IHRhcmdldCkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSBhY3RpdmUgPT09IG51bGwgJiYgd2l0aGluKHRhcmdldCwgdGhpcy4kZWwpICYmIHRoaXMuaXNUb2dnbGVkKCkgPyB0aGlzIDogYWN0aXZlO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYWN0aXZlID0gdGhpcy5pc0FjdGl2ZSgpID8gbnVsbCA6IGFjdGl2ZTtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAndXBkYXRlYXJpYScpO1xuICAgICAgICAgICAgICAgIHRoaXMudHJhY2tlci5jYW5jZWwoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3VwZGF0ZWFyaWEnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUsIHRvZ2dsZSkge1xuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgdGhpcy51cGRhdGVBcmlhKHRoaXMuJGVsKTtcblxuICAgICAgICAgICAgICAgIGlmICh0b2dnbGUgfHwgdGhpcy50b2dnbGUpIHtcbiAgICAgICAgICAgICAgICAgICAgYXR0cigodG9nZ2xlIHx8IHRoaXMudG9nZ2xlKS4kZWwsICdhcmlhLWV4cGFuZGVkJywgdGhpcy5pc1RvZ2dsZWQoKSA/ICd0cnVlJyA6ICdmYWxzZScpO1xuICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyh0aGlzLnRvZ2dsZS4kZWwsIHRoaXMuY2xzLCB0aGlzLmlzVG9nZ2xlZCgpKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICB3cml0ZSgpIHtcblxuICAgICAgICAgICAgaWYgKHRoaXMuaXNUb2dnbGVkKCkgJiYgIUFuaW1hdGlvbi5pblByb2dyZXNzKHRoaXMuJGVsKSkge1xuICAgICAgICAgICAgICAgIHRoaXMucG9zaXRpb24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBzaG93KHRvZ2dsZSwgZGVsYXkgPSB0cnVlKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHNob3cgPSAoKSA9PiAhdGhpcy5pc1RvZ2dsZWQoKSAmJiB0aGlzLnRvZ2dsZUVsZW1lbnQodGhpcy4kZWwsIHRydWUpO1xuICAgICAgICAgICAgY29uc3QgdHJ5U2hvdyA9ICgpID0+IHtcblxuICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlID0gdG9nZ2xlIHx8IHRoaXMudG9nZ2xlO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5jbGVhclRpbWVycygpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaXNBY3RpdmUoKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChkZWxheSAmJiBhY3RpdmUgJiYgYWN0aXZlICE9PSB0aGlzICYmIGFjdGl2ZS5pc0RlbGF5aW5nKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvd1RpbWVyID0gc2V0VGltZW91dCh0aGlzLnNob3csIDEwKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc1BhcmVudE9mKGFjdGl2ZSkpIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoYWN0aXZlLmhpZGVUaW1lcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aXZlLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGFjdGl2ZSAmJiB0aGlzLmlzQ2hpbGRPZihhY3RpdmUpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlLmNsZWFyVGltZXJzKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGFjdGl2ZSAmJiAhdGhpcy5pc0NoaWxkT2YoYWN0aXZlKSAmJiAhdGhpcy5pc1BhcmVudE9mKGFjdGl2ZSkpIHtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgcHJldjtcbiAgICAgICAgICAgICAgICAgICAgd2hpbGUgKGFjdGl2ZSAmJiBhY3RpdmUgIT09IHByZXYgJiYgIXRoaXMuaXNDaGlsZE9mKGFjdGl2ZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHByZXYgPSBhY3RpdmU7XG4gICAgICAgICAgICAgICAgICAgICAgICBhY3RpdmUuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChkZWxheSAmJiB0aGlzLmRlbGF5U2hvdykge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNob3dUaW1lciA9IHNldFRpbWVvdXQoc2hvdywgdGhpcy5kZWxheVNob3cpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHNob3coKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBhY3RpdmUgPSB0aGlzO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgaWYgKHRvZ2dsZSAmJiB0aGlzLnRvZ2dsZSAmJiB0b2dnbGUuJGVsICE9PSB0aGlzLnRvZ2dsZS4kZWwpIHtcblxuICAgICAgICAgICAgICAgIG9uY2UodGhpcy4kZWwsICdoaWRlJywgdHJ5U2hvdyk7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlKGZhbHNlKTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0cnlTaG93KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAgaGlkZShkZWxheSA9IHRydWUpIHtcblxuICAgICAgICAgICAgY29uc3QgaGlkZSA9ICgpID0+IHRoaXMudG9nZ2xlTm93KHRoaXMuJGVsLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcblxuICAgICAgICAgICAgdGhpcy5pc0RlbGF5aW5nID0gdGhpcy50cmFja2VyLm1vdmVzVG8odGhpcy4kZWwpO1xuXG4gICAgICAgICAgICBpZiAoZGVsYXkgJiYgdGhpcy5pc0RlbGF5aW5nKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlVGltZXIgPSBzZXRUaW1lb3V0KHRoaXMuaGlkZSwgdGhpcy5ob3ZlcklkbGUpO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChkZWxheSAmJiB0aGlzLmRlbGF5SGlkZSkge1xuICAgICAgICAgICAgICAgIHRoaXMuaGlkZVRpbWVyID0gc2V0VGltZW91dChoaWRlLCB0aGlzLmRlbGF5SGlkZSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGhpZGUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICBjbGVhclRpbWVycygpIHtcbiAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aGlzLnNob3dUaW1lcik7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGhpcy5oaWRlVGltZXIpO1xuICAgICAgICAgICAgdGhpcy5zaG93VGltZXIgPSBudWxsO1xuICAgICAgICAgICAgdGhpcy5oaWRlVGltZXIgPSBudWxsO1xuICAgICAgICAgICAgdGhpcy5pc0RlbGF5aW5nID0gZmFsc2U7XG4gICAgICAgIH0sXG5cbiAgICAgICAgaXNBY3RpdmUoKSB7XG4gICAgICAgICAgICByZXR1cm4gYWN0aXZlID09PSB0aGlzO1xuICAgICAgICB9LFxuXG4gICAgICAgIGlzQ2hpbGRPZihkcm9wKSB7XG4gICAgICAgICAgICByZXR1cm4gZHJvcCAmJiBkcm9wICE9PSB0aGlzICYmIHdpdGhpbih0aGlzLiRlbCwgZHJvcC4kZWwpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGlzUGFyZW50T2YoZHJvcCkge1xuICAgICAgICAgICAgcmV0dXJuIGRyb3AgJiYgZHJvcCAhPT0gdGhpcyAmJiB3aXRoaW4oZHJvcC4kZWwsIHRoaXMuJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICBwb3NpdGlvbigpIHtcblxuICAgICAgICAgICAgcmVtb3ZlQ2xhc3Nlcyh0aGlzLiRlbCwgYCR7dGhpcy5jbHNEcm9wfS0oc3RhY2t8Ym91bmRhcnkpYCk7XG4gICAgICAgICAgICBjc3ModGhpcy4kZWwsIHt0b3A6ICcnLCBsZWZ0OiAnJywgZGlzcGxheTogJ2Jsb2NrJ30pO1xuICAgICAgICAgICAgdG9nZ2xlQ2xhc3ModGhpcy4kZWwsIGAke3RoaXMuY2xzRHJvcH0tYm91bmRhcnlgLCB0aGlzLmJvdW5kYXJ5QWxpZ24pO1xuXG4gICAgICAgICAgICBjb25zdCBib3VuZGFyeSA9IG9mZnNldCh0aGlzLmJvdW5kYXJ5KTtcbiAgICAgICAgICAgIGNvbnN0IGFsaWduVG8gPSB0aGlzLmJvdW5kYXJ5QWxpZ24gPyBib3VuZGFyeSA6IG9mZnNldCh0aGlzLnRvZ2dsZS4kZWwpO1xuXG4gICAgICAgICAgICBpZiAodGhpcy5hbGlnbiA9PT0gJ2p1c3RpZnknKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgcHJvcCA9IHRoaXMuZ2V0QXhpcygpID09PSAneScgPyAnd2lkdGgnIDogJ2hlaWdodCc7XG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCBwcm9wLCBhbGlnblRvW3Byb3BdKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy4kZWwub2Zmc2V0V2lkdGggPiBNYXRoLm1heChib3VuZGFyeS5yaWdodCAtIGFsaWduVG8ubGVmdCwgYWxpZ25Uby5yaWdodCAtIGJvdW5kYXJ5LmxlZnQpKSB7XG4gICAgICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsIGAke3RoaXMuY2xzRHJvcH0tc3RhY2tgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5wb3NpdGlvbkF0KHRoaXMuJGVsLCB0aGlzLmJvdW5kYXJ5QWxpZ24gPyB0aGlzLmJvdW5kYXJ5IDogdGhpcy50b2dnbGUuJGVsLCB0aGlzLmJvdW5kYXJ5KTtcblxuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnZGlzcGxheScsICcnKTtcblxuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmxldCByZWdpc3RlcmVkO1xuXG5mdW5jdGlvbiByZWdpc3RlckV2ZW50KCkge1xuXG4gICAgaWYgKHJlZ2lzdGVyZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHJlZ2lzdGVyZWQgPSB0cnVlO1xuICAgIG9uKGRvY3VtZW50LCBwb2ludGVyVXAsICh7dGFyZ2V0LCBkZWZhdWx0UHJldmVudGVkfSkgPT4ge1xuICAgICAgICBsZXQgcHJldjtcblxuICAgICAgICBpZiAoZGVmYXVsdFByZXZlbnRlZCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgd2hpbGUgKGFjdGl2ZSAmJiBhY3RpdmUgIT09IHByZXYgJiYgIXdpdGhpbih0YXJnZXQsIGFjdGl2ZS4kZWwpICYmICEoYWN0aXZlLnRvZ2dsZSAmJiB3aXRoaW4odGFyZ2V0LCBhY3RpdmUudG9nZ2xlLiRlbCkpKSB7XG4gICAgICAgICAgICBwcmV2ID0gYWN0aXZlO1xuICAgICAgICAgICAgYWN0aXZlLmhpZGUoZmFsc2UpO1xuICAgICAgICB9XG4gICAgfSk7XG59XG4iLCJpbXBvcnQgRHJvcCBmcm9tICcuL2Ryb3AnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBleHRlbmRzOiBEcm9wXG5cbn07XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IHskLCAkJCwgaXNJbnB1dCwgbWF0Y2hlcywgcXVlcnksIHNlbElucHV0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3NdLFxuXG4gICAgYXJnczogJ3RhcmdldCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICB0YXJnZXQ6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICB0YXJnZXQ6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaW5wdXQoXywgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJChzZWxJbnB1dCwgJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICBzdGF0ZSgpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmlucHV0Lm5leHRFbGVtZW50U2libGluZztcbiAgICAgICAgfSxcblxuICAgICAgICB0YXJnZXQoe3RhcmdldH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHRhcmdldCAmJiAodGFyZ2V0ID09PSB0cnVlXG4gICAgICAgICAgICAgICAgJiYgdGhpcy5pbnB1dC5wYXJlbnROb2RlID09PSAkZWxcbiAgICAgICAgICAgICAgICAmJiB0aGlzLmlucHV0Lm5leHRFbGVtZW50U2libGluZ1xuICAgICAgICAgICAgICAgIHx8IHF1ZXJ5KHRhcmdldCwgJGVsKSk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICB1cGRhdGUoKSB7XG5cbiAgICAgICAgY29uc3Qge3RhcmdldCwgaW5wdXR9ID0gdGhpcztcblxuICAgICAgICBpZiAoIXRhcmdldCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IG9wdGlvbjtcbiAgICAgICAgY29uc3QgcHJvcCA9IGlzSW5wdXQodGFyZ2V0KSA/ICd2YWx1ZScgOiAndGV4dENvbnRlbnQnO1xuICAgICAgICBjb25zdCBwcmV2ID0gdGFyZ2V0W3Byb3BdO1xuICAgICAgICBjb25zdCB2YWx1ZSA9IGlucHV0LmZpbGVzICYmIGlucHV0LmZpbGVzWzBdXG4gICAgICAgICAgICA/IGlucHV0LmZpbGVzWzBdLm5hbWVcbiAgICAgICAgICAgIDogbWF0Y2hlcyhpbnB1dCwgJ3NlbGVjdCcpICYmIChvcHRpb24gPSAkJCgnb3B0aW9uJywgaW5wdXQpLmZpbHRlcihlbCA9PiBlbC5zZWxlY3RlZClbMF0pXG4gICAgICAgICAgICAgICAgPyBvcHRpb24udGV4dENvbnRlbnRcbiAgICAgICAgICAgICAgICA6IGlucHV0LnZhbHVlO1xuXG4gICAgICAgIGlmIChwcmV2ICE9PSB2YWx1ZSkge1xuICAgICAgICAgICAgdGFyZ2V0W3Byb3BdID0gdmFsdWU7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBldmVudHM6IHtcblxuICAgICAgICBjaGFuZ2UoKSB7XG4gICAgICAgICAgICB0aGlzLiRlbWl0KCk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCB7aXNJblZpZXd9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG4vLyBEZXByZWNhdGVkXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKGRhdGEpIHtcblxuICAgICAgICAgICAgY29uc3QgaW52aWV3ID0gaXNJblZpZXcodGhpcy4kZWwpO1xuXG4gICAgICAgICAgICBpZiAoIWludmlldyB8fCBkYXRhLmlzSW5WaWV3ID09PSBpbnZpZXcpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGRhdGEuaXNJblZpZXcgPSBpbnZpZXc7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoKSB7XG4gICAgICAgICAgICB0aGlzLiRlbC5zcmMgPSB0aGlzLiRlbC5zcmM7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Njcm9sbCcsICdyZXNpemUnXVxuICAgIH1cblxufTtcbiIsImltcG9ydCB7aXNSdGwsIGlzVmlzaWJsZSwgb2Zmc2V0UG9zaXRpb24sIHRvZ2dsZUNsYXNzfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgbWFyZ2luOiBTdHJpbmcsXG4gICAgICAgIGZpcnN0Q29sdW1uOiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgbWFyZ2luOiAndWstbWFyZ2luLXNtYWxsLXRvcCcsXG4gICAgICAgIGZpcnN0Q29sdW1uOiAndWstZmlyc3QtY29sdW1uJ1xuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKGRhdGEpIHtcblxuICAgICAgICAgICAgY29uc3QgaXRlbXMgPSB0aGlzLiRlbC5jaGlsZHJlbjtcbiAgICAgICAgICAgIGNvbnN0IHJvd3MgPSBbW11dO1xuXG4gICAgICAgICAgICBpZiAoIWl0ZW1zLmxlbmd0aCB8fCAhaXNWaXNpYmxlKHRoaXMuJGVsKSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBkYXRhLnJvd3MgPSByb3dzO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkYXRhLnJvd3MgPSBnZXRSb3dzKGl0ZW1zKTtcbiAgICAgICAgICAgIGRhdGEuc3RhY2tzID0gIWRhdGEucm93cy5zb21lKHJvdyA9PiByb3cubGVuZ3RoID4gMSk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7cm93c30pIHtcblxuICAgICAgICAgICAgcm93cy5mb3JFYWNoKChyb3csIGkpID0+XG4gICAgICAgICAgICAgICAgcm93LmZvckVhY2goKGVsLCBqKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKGVsLCB0aGlzLm1hcmdpbiwgaSAhPT0gMCk7XG4gICAgICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKGVsLCB0aGlzLmZpcnN0Q29sdW1uLCBqID09PSAwKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuXG5leHBvcnQgZnVuY3Rpb24gZ2V0Um93cyhpdGVtcykge1xuICAgIGNvbnN0IHJvd3MgPSBbW11dO1xuXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBpdGVtcy5sZW5ndGg7IGkrKykge1xuXG4gICAgICAgIGNvbnN0IGVsID0gaXRlbXNbaV07XG4gICAgICAgIGxldCBkaW0gPSBnZXRPZmZzZXQoZWwpO1xuXG4gICAgICAgIGlmICghZGltLmhlaWdodCkge1xuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgIH1cblxuICAgICAgICBmb3IgKGxldCBqID0gcm93cy5sZW5ndGggLSAxOyBqID49IDA7IGotLSkge1xuXG4gICAgICAgICAgICBjb25zdCByb3cgPSByb3dzW2pdO1xuXG4gICAgICAgICAgICBpZiAoIXJvd1swXSkge1xuICAgICAgICAgICAgICAgIHJvdy5wdXNoKGVsKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgbGV0IGxlZnREaW07XG4gICAgICAgICAgICBpZiAocm93WzBdLm9mZnNldFBhcmVudCA9PT0gZWwub2Zmc2V0UGFyZW50KSB7XG4gICAgICAgICAgICAgICAgbGVmdERpbSA9IGdldE9mZnNldChyb3dbMF0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBkaW0gPSBnZXRPZmZzZXQoZWwsIHRydWUpO1xuICAgICAgICAgICAgICAgIGxlZnREaW0gPSBnZXRPZmZzZXQocm93WzBdLCB0cnVlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGRpbS50b3AgPj0gbGVmdERpbS5ib3R0b20gLSAxKSB7XG4gICAgICAgICAgICAgICAgcm93cy5wdXNoKFtlbF0pO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoZGltLmJvdHRvbSA+IGxlZnREaW0udG9wKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAoZGltLmxlZnQgPCBsZWZ0RGltLmxlZnQgJiYgIWlzUnRsKSB7XG4gICAgICAgICAgICAgICAgICAgIHJvdy51bnNoaWZ0KGVsKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcm93LnB1c2goZWwpO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoaiA9PT0gMCkge1xuICAgICAgICAgICAgICAgIHJvd3MudW5zaGlmdChbZWxdKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9XG5cbiAgICByZXR1cm4gcm93cztcblxufVxuXG5mdW5jdGlvbiBnZXRPZmZzZXQoZWxlbWVudCwgb2Zmc2V0ID0gZmFsc2UpIHtcblxuICAgIGxldCB7b2Zmc2V0VG9wLCBvZmZzZXRMZWZ0LCBvZmZzZXRIZWlnaHR9ID0gZWxlbWVudDtcblxuICAgIGlmIChvZmZzZXQpIHtcbiAgICAgICAgW29mZnNldFRvcCwgb2Zmc2V0TGVmdF0gPSBvZmZzZXRQb3NpdGlvbihlbGVtZW50KTtcbiAgICB9XG5cbiAgICByZXR1cm4ge1xuICAgICAgICB0b3A6IG9mZnNldFRvcCxcbiAgICAgICAgbGVmdDogb2Zmc2V0TGVmdCxcbiAgICAgICAgaGVpZ2h0OiBvZmZzZXRIZWlnaHQsXG4gICAgICAgIGJvdHRvbTogb2Zmc2V0VG9wICsgb2Zmc2V0SGVpZ2h0XG4gICAgfTtcbn1cbiIsImltcG9ydCBNYXJnaW4gZnJvbSAnLi9tYXJnaW4nO1xuaW1wb3J0IENsYXNzIGZyb20gJy4uL21peGluL2NsYXNzJztcbmltcG9ydCB7YWRkQ2xhc3MsIGNzcywgaGFzQ2xhc3MsIGhlaWdodCBhcyBnZXRIZWlnaHQsIGlzUnRsLCBzY3JvbGxlZE92ZXIsIHRvRmxvYXQsIHRvZ2dsZUNsYXNzLCB0b05vZGVzLCBUcmFuc2l0aW9uLCBzb3J0Qnl9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBleHRlbmRzOiBNYXJnaW4sXG5cbiAgICBtaXhpbnM6IFtDbGFzc10sXG5cbiAgICBuYW1lOiAnZ3JpZCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBtYXNvbnJ5OiBCb29sZWFuLFxuICAgICAgICBwYXJhbGxheDogTnVtYmVyXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgbWFyZ2luOiAndWstZ3JpZC1tYXJnaW4nLFxuICAgICAgICBjbHNTdGFjazogJ3VrLWdyaWQtc3RhY2snLFxuICAgICAgICBtYXNvbnJ5OiBmYWxzZSxcbiAgICAgICAgcGFyYWxsYXg6IDBcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBsZW5ndGgoXywgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJGVsLmNoaWxkcmVuLmxlbmd0aDtcbiAgICAgICAgfSxcblxuICAgICAgICBwYXJhbGxheCh7cGFyYWxsYXh9KSB7XG4gICAgICAgICAgICByZXR1cm4gcGFyYWxsYXggJiYgdGhpcy5sZW5ndGggPyBNYXRoLmFicyhwYXJhbGxheCkgOiAnJztcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgdGhpcy5tYXNvbnJ5ICYmIGFkZENsYXNzKHRoaXMuJGVsLCAndWstZmxleC10b3AgdWstZmxleC13cmFwLXRvcCcpO1xuICAgIH0sXG5cbiAgICB1cGRhdGU6IFtcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIHJlYWQoe3Jvd3N9KSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5tYXNvbnJ5IHx8IHRoaXMucGFyYWxsYXgpIHtcbiAgICAgICAgICAgICAgICAgICAgcm93cyA9IHJvd3MubWFwKGVsZW1lbnRzID0+IHNvcnRCeShlbGVtZW50cywgJ29mZnNldExlZnQnKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlzUnRsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByb3dzLm1hcChyb3cgPT4gcm93LnJldmVyc2UoKSk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNvbnN0IHRyYW5zaXRpb25JblByb2dyZXNzID0gcm93cy5zb21lKGVsZW1lbnRzID0+IGVsZW1lbnRzLnNvbWUoVHJhbnNpdGlvbi5pblByb2dyZXNzKSk7XG4gICAgICAgICAgICAgICAgbGV0IHRyYW5zbGF0ZXMgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICBsZXQgZWxIZWlnaHQgPSAnJztcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLm1hc29ucnkgJiYgdGhpcy5sZW5ndGgpIHtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgaGVpZ2h0ID0gMDtcblxuICAgICAgICAgICAgICAgICAgICB0cmFuc2xhdGVzID0gcm93cy5yZWR1Y2UoKHRyYW5zbGF0ZXMsIHJvdywgaSkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0cmFuc2xhdGVzW2ldID0gcm93Lm1hcCgoXywgaikgPT4gaSA9PT0gMCA/IDAgOiB0b0Zsb2F0KHRyYW5zbGF0ZXNbaSAtIDFdW2pdKSArIChoZWlnaHQgLSB0b0Zsb2F0KHJvd3NbaSAtIDFdW2pdICYmIHJvd3NbaSAtIDFdW2pdLm9mZnNldEhlaWdodCkpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGhlaWdodCA9IHJvdy5yZWR1Y2UoKGhlaWdodCwgZWwpID0+IE1hdGgubWF4KGhlaWdodCwgZWwub2Zmc2V0SGVpZ2h0KSwgMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB0cmFuc2xhdGVzO1xuXG4gICAgICAgICAgICAgICAgICAgIH0sIFtdKTtcblxuICAgICAgICAgICAgICAgICAgICBlbEhlaWdodCA9IG1heENvbHVtbkhlaWdodChyb3dzKSArIGdldE1hcmdpblRvcCh0aGlzLiRlbCwgdGhpcy5tYXJnaW4pICogKHJvd3MubGVuZ3RoIC0gMSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4ge3Jvd3MsIHRyYW5zbGF0ZXMsIGhlaWdodDogIXRyYW5zaXRpb25JblByb2dyZXNzID8gZWxIZWlnaHQgOiBmYWxzZX07XG5cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIHdyaXRlKHtzdGFja3MsIGhlaWdodH0pIHtcblxuICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKHRoaXMuJGVsLCB0aGlzLmNsc1N0YWNrLCBzdGFja3MpO1xuXG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAncGFkZGluZ0JvdHRvbScsIHRoaXMucGFyYWxsYXgpO1xuICAgICAgICAgICAgICAgIGhlaWdodCAhPT0gZmFsc2UgJiYgY3NzKHRoaXMuJGVsLCAnaGVpZ2h0JywgaGVpZ2h0KTtcblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIHJlYWQoe2hlaWdodH0pIHtcbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBzY3JvbGxlZDogdGhpcy5wYXJhbGxheFxuICAgICAgICAgICAgICAgICAgICAgICAgPyBzY3JvbGxlZE92ZXIodGhpcy4kZWwsIGhlaWdodCA/IGhlaWdodCAtIGdldEhlaWdodCh0aGlzLiRlbCkgOiAwKSAqIHRoaXMucGFyYWxsYXhcbiAgICAgICAgICAgICAgICAgICAgICAgIDogZmFsc2VcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgd3JpdGUoe3Jvd3MsIHNjcm9sbGVkLCB0cmFuc2xhdGVzfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHNjcm9sbGVkID09PSBmYWxzZSAmJiAhdHJhbnNsYXRlcykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcm93cy5mb3JFYWNoKChyb3csIGkpID0+XG4gICAgICAgICAgICAgICAgICAgIHJvdy5mb3JFYWNoKChlbCwgaikgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNzcyhlbCwgJ3RyYW5zZm9ybScsICFzY3JvbGxlZCAmJiAhdHJhbnNsYXRlcyA/ICcnIDogYHRyYW5zbGF0ZVkoJHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAodHJhbnNsYXRlcyAmJiAtdHJhbnNsYXRlc1tpXVtqXSkgKyAoc2Nyb2xsZWQgPyBqICUgMiA/IHNjcm9sbGVkIDogc2Nyb2xsZWQgLyA4IDogMClcbiAgICAgICAgICAgICAgICAgICAgICAgIH1weClgKVxuICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgKTtcblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Njcm9sbCcsICdyZXNpemUnXVxuXG4gICAgICAgIH1cblxuICAgIF1cblxufTtcblxuZnVuY3Rpb24gZ2V0TWFyZ2luVG9wKHJvb3QsIGNscykge1xuXG4gICAgY29uc3Qgbm9kZXMgPSB0b05vZGVzKHJvb3QuY2hpbGRyZW4pO1xuICAgIGNvbnN0IFtub2RlXSA9IG5vZGVzLmZpbHRlcihlbCA9PiBoYXNDbGFzcyhlbCwgY2xzKSk7XG5cbiAgICByZXR1cm4gdG9GbG9hdChub2RlXG4gICAgICAgID8gY3NzKG5vZGUsICdtYXJnaW5Ub3AnKVxuICAgICAgICA6IGNzcyhub2Rlc1swXSwgJ3BhZGRpbmdMZWZ0JykpO1xufVxuXG5mdW5jdGlvbiBtYXhDb2x1bW5IZWlnaHQocm93cykge1xuICAgIHJldHVybiBNYXRoLm1heCguLi5yb3dzLnJlZHVjZSgoc3VtLCByb3cpID0+IHtcbiAgICAgICAgcm93LmZvckVhY2goKGVsLCBpKSA9PiBzdW1baV0gPSAoc3VtW2ldIHx8IDApICsgZWwub2Zmc2V0SGVpZ2h0KTtcbiAgICAgICAgcmV0dXJuIHN1bTtcbiAgICB9LCBbXSkpO1xufVxuIiwiaW1wb3J0IHskJCwgYm94TW9kZWxBZGp1c3QsIGNzcywgaXNJRSwgdG9GbG9hdH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbi8vIElFIDExIGZpeCAobWluLWhlaWdodCBvbiBhIGZsZXggY29udGFpbmVyIHdvbid0IGFwcGx5IHRvIGl0cyBmbGV4IGl0ZW1zKVxuZXhwb3J0IGRlZmF1bHQgaXNJRSA/IHtcblxuICAgIGRhdGE6IHtcbiAgICAgICAgc2VsTWluSGVpZ2h0OiBmYWxzZSxcbiAgICAgICAgZm9yY2VIZWlnaHQ6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgZWxlbWVudHMoe3NlbE1pbkhlaWdodH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHNlbE1pbkhlaWdodCA/ICQkKHNlbE1pbkhlaWdodCwgJGVsKSA6IFskZWxdO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLmVsZW1lbnRzLCAnaGVpZ2h0JywgJycpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgb3JkZXI6IC01LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgd3JpdGUoKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5lbGVtZW50cy5mb3JFYWNoKGVsID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaGVpZ2h0ID0gdG9GbG9hdChjc3MoZWwsICdtaW5IZWlnaHQnKSk7XG4gICAgICAgICAgICAgICAgICAgIGlmIChoZWlnaHQgJiYgKHRoaXMuZm9yY2VIZWlnaHQgfHwgTWF0aC5yb3VuZChoZWlnaHQgKyBib3hNb2RlbEFkanVzdCgnaGVpZ2h0JywgZWwsICdjb250ZW50LWJveCcpKSA+PSBlbC5vZmZzZXRIZWlnaHQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjc3MoZWwsICdoZWlnaHQnLCBoZWlnaHQpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBvcmRlcjogNSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICAgICAgfVxuXG4gICAgXVxuXG59IDoge307XG4iLCJpbXBvcnQgRmxleEJ1ZyBmcm9tICcuLi9taXhpbi9mbGV4LWJ1Zyc7XG5pbXBvcnQge2dldFJvd3N9IGZyb20gJy4vbWFyZ2luJztcbmltcG9ydCB7JCQsIGJveE1vZGVsQWRqdXN0LCBjc3MsIG9mZnNldCwgdG9GbG9hdH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0ZsZXhCdWddLFxuXG4gICAgYXJnczogJ3RhcmdldCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICB0YXJnZXQ6IFN0cmluZyxcbiAgICAgICAgcm93OiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdGFyZ2V0OiAnPiAqJyxcbiAgICAgICAgcm93OiB0cnVlLFxuICAgICAgICBmb3JjZUhlaWdodDogdHJ1ZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGVsZW1lbnRzKHt0YXJnZXR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiAkJCh0YXJnZXQsICRlbCk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICByb3dzOiAodGhpcy5yb3cgPyBnZXRSb3dzKHRoaXMuZWxlbWVudHMpIDogW3RoaXMuZWxlbWVudHNdKS5tYXAobWF0Y2gpXG4gICAgICAgICAgICB9O1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKHtyb3dzfSkge1xuICAgICAgICAgICAgcm93cy5mb3JFYWNoKCh7aGVpZ2h0cywgZWxlbWVudHN9KSA9PlxuICAgICAgICAgICAgICAgIGVsZW1lbnRzLmZvckVhY2goKGVsLCBpKSA9PlxuICAgICAgICAgICAgICAgICAgICBjc3MoZWwsICdtaW5IZWlnaHQnLCBoZWlnaHRzW2ldKVxuICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9XG5cbn07XG5cbmZ1bmN0aW9uIG1hdGNoKGVsZW1lbnRzKSB7XG5cbiAgICBpZiAoZWxlbWVudHMubGVuZ3RoIDwgMikge1xuICAgICAgICByZXR1cm4ge2hlaWdodHM6IFsnJ10sIGVsZW1lbnRzfTtcbiAgICB9XG5cbiAgICBsZXQge2hlaWdodHMsIG1heH0gPSBnZXRIZWlnaHRzKGVsZW1lbnRzKTtcbiAgICBjb25zdCBoYXNNaW5IZWlnaHQgPSBlbGVtZW50cy5zb21lKGVsID0+IGVsLnN0eWxlLm1pbkhlaWdodCk7XG4gICAgY29uc3QgaGFzU2hydW5rID0gZWxlbWVudHMuc29tZSgoZWwsIGkpID0+ICFlbC5zdHlsZS5taW5IZWlnaHQgJiYgaGVpZ2h0c1tpXSA8IG1heCk7XG5cbiAgICBpZiAoaGFzTWluSGVpZ2h0ICYmIGhhc1NocnVuaykge1xuICAgICAgICBjc3MoZWxlbWVudHMsICdtaW5IZWlnaHQnLCAnJyk7XG4gICAgICAgICh7aGVpZ2h0cywgbWF4fSA9IGdldEhlaWdodHMoZWxlbWVudHMpKTtcbiAgICB9XG5cbiAgICBoZWlnaHRzID0gZWxlbWVudHMubWFwKChlbCwgaSkgPT5cbiAgICAgICAgaGVpZ2h0c1tpXSA9PT0gbWF4ICYmIHRvRmxvYXQoZWwuc3R5bGUubWluSGVpZ2h0KS50b0ZpeGVkKDIpICE9PSBtYXgudG9GaXhlZCgyKSA/ICcnIDogbWF4XG4gICAgKTtcblxuICAgIHJldHVybiB7aGVpZ2h0cywgZWxlbWVudHN9O1xufVxuXG5mdW5jdGlvbiBnZXRIZWlnaHRzKGVsZW1lbnRzKSB7XG4gICAgY29uc3QgaGVpZ2h0cyA9IGVsZW1lbnRzLm1hcChlbCA9PiBvZmZzZXQoZWwpLmhlaWdodCAtIGJveE1vZGVsQWRqdXN0KCdoZWlnaHQnLCBlbCwgJ2NvbnRlbnQtYm94JykpO1xuICAgIGNvbnN0IG1heCA9IE1hdGgubWF4LmFwcGx5KG51bGwsIGhlaWdodHMpO1xuXG4gICAgcmV0dXJuIHtoZWlnaHRzLCBtYXh9O1xufVxuIiwiaW1wb3J0IEZsZXhCdWcgZnJvbSAnLi4vbWl4aW4vZmxleC1idWcnO1xuaW1wb3J0IHtib3hNb2RlbEFkanVzdCwgY3NzLCBlbmRzV2l0aCwgaGVpZ2h0LCBpc051bWVyaWMsIGlzU3RyaW5nLCBvZmZzZXQsIHF1ZXJ5LCB0b0Zsb2F0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbRmxleEJ1Z10sXG5cbiAgICBwcm9wczoge1xuICAgICAgICBleHBhbmQ6IEJvb2xlYW4sXG4gICAgICAgIG9mZnNldFRvcDogQm9vbGVhbixcbiAgICAgICAgb2Zmc2V0Qm90dG9tOiBCb29sZWFuLFxuICAgICAgICBtaW5IZWlnaHQ6IE51bWJlclxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGV4cGFuZDogZmFsc2UsXG4gICAgICAgIG9mZnNldFRvcDogZmFsc2UsXG4gICAgICAgIG9mZnNldEJvdHRvbTogZmFsc2UsXG4gICAgICAgIG1pbkhlaWdodDogMFxuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuXG4gICAgICAgICAgICBsZXQgbWluSGVpZ2h0ID0gJyc7XG4gICAgICAgICAgICBjb25zdCBib3ggPSBib3hNb2RlbEFkanVzdCgnaGVpZ2h0JywgdGhpcy4kZWwsICdjb250ZW50LWJveCcpO1xuXG4gICAgICAgICAgICBpZiAodGhpcy5leHBhbmQpIHtcblxuICAgICAgICAgICAgICAgIG1pbkhlaWdodCA9IGhlaWdodCh3aW5kb3cpIC0gKG9mZnNldEhlaWdodChkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQpIC0gb2Zmc2V0SGVpZ2h0KHRoaXMuJGVsKSkgLSBib3ggfHwgJyc7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAvLyBvbiBtb2JpbGUgZGV2aWNlcyAoaU9TIGFuZCBBbmRyb2lkKSB3aW5kb3cuaW5uZXJIZWlnaHQgIT09IDEwMHZoXG4gICAgICAgICAgICAgICAgbWluSGVpZ2h0ID0gJ2NhbGMoMTAwdmgnO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMub2Zmc2V0VG9wKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3Qge3RvcH0gPSBvZmZzZXQodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICBtaW5IZWlnaHQgKz0gdG9wIDwgaGVpZ2h0KHdpbmRvdykgLyAyID8gYCAtICR7dG9wfXB4YCA6ICcnO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMub2Zmc2V0Qm90dG9tID09PSB0cnVlKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbWluSGVpZ2h0ICs9IGAgLSAke29mZnNldEhlaWdodCh0aGlzLiRlbC5uZXh0RWxlbWVudFNpYmxpbmcpfXB4YDtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoaXNOdW1lcmljKHRoaXMub2Zmc2V0Qm90dG9tKSkge1xuXG4gICAgICAgICAgICAgICAgICAgIG1pbkhlaWdodCArPSBgIC0gJHt0aGlzLm9mZnNldEJvdHRvbX12aGA7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHRoaXMub2Zmc2V0Qm90dG9tICYmIGVuZHNXaXRoKHRoaXMub2Zmc2V0Qm90dG9tLCAncHgnKSkge1xuXG4gICAgICAgICAgICAgICAgICAgIG1pbkhlaWdodCArPSBgIC0gJHt0b0Zsb2F0KHRoaXMub2Zmc2V0Qm90dG9tKX1weGA7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGlzU3RyaW5nKHRoaXMub2Zmc2V0Qm90dG9tKSkge1xuXG4gICAgICAgICAgICAgICAgICAgIG1pbkhlaWdodCArPSBgIC0gJHtvZmZzZXRIZWlnaHQocXVlcnkodGhpcy5vZmZzZXRCb3R0b20sIHRoaXMuJGVsKSl9cHhgO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbWluSGVpZ2h0ICs9IGAke2JveCA/IGAgLSAke2JveH1weGAgOiAnJ30pYDtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4ge21pbkhlaWdodH07XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoe21pbkhlaWdodH0pIHtcblxuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCB7bWluSGVpZ2h0fSk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLm1pbkhlaWdodCAmJiB0b0Zsb2F0KGNzcyh0aGlzLiRlbCwgJ21pbkhlaWdodCcpKSA8IHRoaXMubWluSGVpZ2h0KSB7XG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnbWluSGVpZ2h0JywgdGhpcy5taW5IZWlnaHQpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9XG5cbn07XG5cbmZ1bmN0aW9uIG9mZnNldEhlaWdodChlbCkge1xuICAgIHJldHVybiBlbCAmJiBlbC5vZmZzZXRIZWlnaHQgfHwgMDtcbn1cbiIsImltcG9ydCB7JCwgJCQsIGFmdGVyLCBhamF4LCBhcHBlbmQsIGF0dHIsIGluY2x1ZGVzLCBpc1Zpc2libGUsIGlzVm9pZEVsZW1lbnQsIG5vb3AsIFByb21pc2UsIHJlbW92ZSwgcmVtb3ZlQXR0ciwgc3RhcnRzV2l0aH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGFyZ3M6ICdzcmMnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgaWQ6IEJvb2xlYW4sXG4gICAgICAgIGljb246IFN0cmluZyxcbiAgICAgICAgc3JjOiBTdHJpbmcsXG4gICAgICAgIHN0eWxlOiBTdHJpbmcsXG4gICAgICAgIHdpZHRoOiBOdW1iZXIsXG4gICAgICAgIGhlaWdodDogTnVtYmVyLFxuICAgICAgICByYXRpbzogTnVtYmVyLFxuICAgICAgICAnY2xhc3MnOiBTdHJpbmcsXG4gICAgICAgIHN0cm9rZUFuaW1hdGlvbjogQm9vbGVhbixcbiAgICAgICAgYXR0cmlidXRlczogJ2xpc3QnXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgcmF0aW86IDEsXG4gICAgICAgIGluY2x1ZGU6IFsnc3R5bGUnLCAnY2xhc3MnXSxcbiAgICAgICAgJ2NsYXNzJzogJycsXG4gICAgICAgIHN0cm9rZUFuaW1hdGlvbjogZmFsc2VcbiAgICB9LFxuXG4gICAgYmVmb3JlQ29ubmVjdCgpIHtcblxuICAgICAgICB0aGlzLmNsYXNzICs9ICcgdWstc3ZnJztcblxuICAgICAgICBpZiAoIXRoaXMuaWNvbiAmJiBpbmNsdWRlcyh0aGlzLnNyYywgJyMnKSkge1xuXG4gICAgICAgICAgICBjb25zdCBwYXJ0cyA9IHRoaXMuc3JjLnNwbGl0KCcjJyk7XG5cbiAgICAgICAgICAgIGlmIChwYXJ0cy5sZW5ndGggPiAxKSB7XG4gICAgICAgICAgICAgICAgW3RoaXMuc3JjLCB0aGlzLmljb25dID0gcGFydHM7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnN2ZyA9IHRoaXMuZ2V0U3ZnKCkudGhlbihlbCA9PiB7XG4gICAgICAgICAgICB0aGlzLmFwcGx5QXR0cmlidXRlcyhlbCk7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5zdmdFbCA9IGluc2VydFNWRyhlbCwgdGhpcy4kZWwpO1xuICAgICAgICB9LCBub29wKTtcblxuICAgIH0sXG5cbiAgICBkaXNjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgaWYgKGlzVm9pZEVsZW1lbnQodGhpcy4kZWwpKSB7XG4gICAgICAgICAgICBhdHRyKHRoaXMuJGVsLCAnaGlkZGVuJywgbnVsbCk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpcy5zdmcpIHtcbiAgICAgICAgICAgIHRoaXMuc3ZnLnRoZW4oc3ZnID0+ICghdGhpcy5fY29ubmVjdGVkIHx8IHN2ZyAhPT0gdGhpcy5zdmdFbCkgJiYgcmVtb3ZlKHN2ZyksIG5vb3ApO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5zdmcgPSB0aGlzLnN2Z0VsID0gbnVsbDtcblxuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgcmV0dXJuICEhKHRoaXMuc3Ryb2tlQW5pbWF0aW9uICYmIHRoaXMuc3ZnRWwgJiYgaXNWaXNpYmxlKHRoaXMuc3ZnRWwpKTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSgpIHtcbiAgICAgICAgICAgIGFwcGx5QW5pbWF0aW9uKHRoaXMuc3ZnRWwpO1xuICAgICAgICB9LFxuXG4gICAgICAgIHR5cGU6IFsncmVzaXplJ11cblxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgZ2V0U3ZnKCkge1xuICAgICAgICAgICAgcmV0dXJuIGxvYWRTVkcodGhpcy5zcmMpLnRoZW4oc3ZnID0+XG4gICAgICAgICAgICAgICAgcGFyc2VTVkcoc3ZnLCB0aGlzLmljb24pIHx8IFByb21pc2UucmVqZWN0KCdTVkcgbm90IGZvdW5kLicpXG4gICAgICAgICAgICApO1xuICAgICAgICB9LFxuXG4gICAgICAgIGFwcGx5QXR0cmlidXRlcyhlbCkge1xuXG4gICAgICAgICAgICBmb3IgKGNvbnN0IHByb3AgaW4gdGhpcy4kb3B0aW9ucy5wcm9wcykge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzW3Byb3BdICYmIGluY2x1ZGVzKHRoaXMuaW5jbHVkZSwgcHJvcCkpIHtcbiAgICAgICAgICAgICAgICAgICAgYXR0cihlbCwgcHJvcCwgdGhpc1twcm9wXSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBmb3IgKGNvbnN0IGF0dHJpYnV0ZSBpbiB0aGlzLmF0dHJpYnV0ZXMpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBbcHJvcCwgdmFsdWVdID0gdGhpcy5hdHRyaWJ1dGVzW2F0dHJpYnV0ZV0uc3BsaXQoJzonLCAyKTtcbiAgICAgICAgICAgICAgICBhdHRyKGVsLCBwcm9wLCB2YWx1ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICghdGhpcy5pZCkge1xuICAgICAgICAgICAgICAgIHJlbW92ZUF0dHIoZWwsICdpZCcpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCBwcm9wcyA9IFsnd2lkdGgnLCAnaGVpZ2h0J107XG4gICAgICAgICAgICBsZXQgZGltZW5zaW9ucyA9IFt0aGlzLndpZHRoLCB0aGlzLmhlaWdodF07XG5cbiAgICAgICAgICAgIGlmICghZGltZW5zaW9ucy5zb21lKHZhbCA9PiB2YWwpKSB7XG4gICAgICAgICAgICAgICAgZGltZW5zaW9ucyA9IHByb3BzLm1hcChwcm9wID0+IGF0dHIoZWwsIHByb3ApKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3Qgdmlld0JveCA9IGF0dHIoZWwsICd2aWV3Qm94Jyk7XG4gICAgICAgICAgICBpZiAodmlld0JveCAmJiAhZGltZW5zaW9ucy5zb21lKHZhbCA9PiB2YWwpKSB7XG4gICAgICAgICAgICAgICAgZGltZW5zaW9ucyA9IHZpZXdCb3guc3BsaXQoJyAnKS5zbGljZSgyKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZGltZW5zaW9ucy5mb3JFYWNoKCh2YWwsIGkpID0+IHtcbiAgICAgICAgICAgICAgICB2YWwgPSAodmFsIHwgMCkgKiB0aGlzLnJhdGlvO1xuICAgICAgICAgICAgICAgIHZhbCAmJiBhdHRyKGVsLCBwcm9wc1tpXSwgdmFsKTtcblxuICAgICAgICAgICAgICAgIGlmICh2YWwgJiYgIWRpbWVuc2lvbnNbaSBeIDFdKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlbW92ZUF0dHIoZWwsIHByb3BzW2kgXiAxXSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGF0dHIoZWwsICdkYXRhLXN2ZycsIHRoaXMuaWNvbiB8fCB0aGlzLnNyYyk7XG5cbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5jb25zdCBzdmdzID0ge307XG5cbmZ1bmN0aW9uIGxvYWRTVkcoc3JjKSB7XG5cbiAgICBpZiAoc3Znc1tzcmNdKSB7XG4gICAgICAgIHJldHVybiBzdmdzW3NyY107XG4gICAgfVxuXG4gICAgcmV0dXJuIHN2Z3Nbc3JjXSA9IG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcblxuICAgICAgICBpZiAoIXNyYykge1xuICAgICAgICAgICAgcmVqZWN0KCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoc3RhcnRzV2l0aChzcmMsICdkYXRhOicpKSB7XG4gICAgICAgICAgICByZXNvbHZlKGRlY29kZVVSSUNvbXBvbmVudChzcmMuc3BsaXQoJywnKVsxXSkpO1xuICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICBhamF4KHNyYykudGhlbihcbiAgICAgICAgICAgICAgICB4aHIgPT4gcmVzb2x2ZSh4aHIucmVzcG9uc2UpLFxuICAgICAgICAgICAgICAgICgpID0+IHJlamVjdCgnU1ZHIG5vdCBmb3VuZC4nKVxuICAgICAgICAgICAgKTtcblxuICAgICAgICB9XG5cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gcGFyc2VTVkcoc3ZnLCBpY29uKSB7XG5cbiAgICBpZiAoaWNvbiAmJiBpbmNsdWRlcyhzdmcsICc8c3ltYm9sJykpIHtcbiAgICAgICAgc3ZnID0gcGFyc2VTeW1ib2xzKHN2ZywgaWNvbikgfHwgc3ZnO1xuICAgIH1cblxuICAgIHN2ZyA9ICQoc3ZnLnN1YnN0cihzdmcuaW5kZXhPZignPHN2ZycpKSk7XG4gICAgcmV0dXJuIHN2ZyAmJiBzdmcuaGFzQ2hpbGROb2RlcygpICYmIHN2Zztcbn1cblxuY29uc3Qgc3ltYm9sUmUgPSAvPHN5bWJvbCguKj9pZD0oWydcIl0pKC4qPylcXDJbXl0qPzxcXC8pc3ltYm9sPi9nO1xuY29uc3Qgc3ltYm9scyA9IHt9O1xuXG5mdW5jdGlvbiBwYXJzZVN5bWJvbHMoc3ZnLCBpY29uKSB7XG5cbiAgICBpZiAoIXN5bWJvbHNbc3ZnXSkge1xuXG4gICAgICAgIHN5bWJvbHNbc3ZnXSA9IHt9O1xuXG4gICAgICAgIGxldCBtYXRjaDtcbiAgICAgICAgd2hpbGUgKChtYXRjaCA9IHN5bWJvbFJlLmV4ZWMoc3ZnKSkpIHtcbiAgICAgICAgICAgIHN5bWJvbHNbc3ZnXVttYXRjaFszXV0gPSBgPHN2ZyB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCIke21hdGNoWzFdfXN2Zz5gO1xuICAgICAgICB9XG5cbiAgICAgICAgc3ltYm9sUmUubGFzdEluZGV4ID0gMDtcblxuICAgIH1cblxuICAgIHJldHVybiBzeW1ib2xzW3N2Z11baWNvbl07XG59XG5cbmZ1bmN0aW9uIGFwcGx5QW5pbWF0aW9uKGVsKSB7XG5cbiAgICBjb25zdCBsZW5ndGggPSBnZXRNYXhQYXRoTGVuZ3RoKGVsKTtcblxuICAgIGlmIChsZW5ndGgpIHtcbiAgICAgICAgZWwuc3R5bGUuc2V0UHJvcGVydHkoJy0tdWstYW5pbWF0aW9uLXN0cm9rZScsIGxlbmd0aCk7XG4gICAgfVxuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRNYXhQYXRoTGVuZ3RoKGVsKSB7XG4gICAgcmV0dXJuIE1hdGguY2VpbChNYXRoLm1heCguLi4kJCgnW3N0cm9rZV0nLCBlbCkubWFwKHN0cm9rZSA9PlxuICAgICAgICBzdHJva2UuZ2V0VG90YWxMZW5ndGggJiYgc3Ryb2tlLmdldFRvdGFsTGVuZ3RoKCkgfHwgMFxuICAgICkuY29uY2F0KFswXSkpKTtcbn1cblxuZnVuY3Rpb24gaW5zZXJ0U1ZHKGVsLCByb290KSB7XG4gICAgaWYgKGlzVm9pZEVsZW1lbnQocm9vdCkgfHwgcm9vdC50YWdOYW1lID09PSAnQ0FOVkFTJykge1xuXG4gICAgICAgIGF0dHIocm9vdCwgJ2hpZGRlbicsIHRydWUpO1xuXG4gICAgICAgIGNvbnN0IG5leHQgPSByb290Lm5leHRFbGVtZW50U2libGluZztcbiAgICAgICAgcmV0dXJuIGVxdWFscyhlbCwgbmV4dClcbiAgICAgICAgICAgID8gbmV4dFxuICAgICAgICAgICAgOiBhZnRlcihyb290LCBlbCk7XG5cbiAgICB9IGVsc2Uge1xuXG4gICAgICAgIGNvbnN0IGxhc3QgPSByb290Lmxhc3RFbGVtZW50Q2hpbGQ7XG4gICAgICAgIHJldHVybiBlcXVhbHMoZWwsIGxhc3QpXG4gICAgICAgICAgICA/IGxhc3RcbiAgICAgICAgICAgIDogYXBwZW5kKHJvb3QsIGVsKTtcblxuICAgIH1cbn1cblxuZnVuY3Rpb24gZXF1YWxzKGVsLCBvdGhlcikge1xuICAgIHJldHVybiBhdHRyKGVsLCAnZGF0YS1zdmcnKSA9PT0gYXR0cihvdGhlciwgJ2RhdGEtc3ZnJyk7XG59XG4iLCJpbXBvcnQgU1ZHIGZyb20gJy4vc3ZnJztcbmltcG9ydCBjbG9zZUljb24gZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvY2xvc2UtaWNvbi5zdmcnO1xuaW1wb3J0IGNsb3NlTGFyZ2UgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvY2xvc2UtbGFyZ2Uuc3ZnJztcbmltcG9ydCBtYXJrZXIgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvbWFya2VyLnN2Zyc7XG5pbXBvcnQgbmF2YmFyVG9nZ2xlSWNvbiBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9uYXZiYXItdG9nZ2xlLWljb24uc3ZnJztcbmltcG9ydCBvdmVybGF5SWNvbiBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9vdmVybGF5LWljb24uc3ZnJztcbmltcG9ydCBwYWdpbmF0aW9uTmV4dCBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9wYWdpbmF0aW9uLW5leHQuc3ZnJztcbmltcG9ydCBwYWdpbmF0aW9uUHJldmlvdXMgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvcGFnaW5hdGlvbi1wcmV2aW91cy5zdmcnO1xuaW1wb3J0IHNlYXJjaEljb24gZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc2VhcmNoLWljb24uc3ZnJztcbmltcG9ydCBzZWFyY2hMYXJnZSBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zZWFyY2gtbGFyZ2Uuc3ZnJztcbmltcG9ydCBzZWFyY2hOYXZiYXIgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc2VhcmNoLW5hdmJhci5zdmcnO1xuaW1wb3J0IHNsaWRlbmF2TmV4dCBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zbGlkZW5hdi1uZXh0LnN2Zyc7XG5pbXBvcnQgc2xpZGVuYXZOZXh0TGFyZ2UgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc2xpZGVuYXYtbmV4dC1sYXJnZS5zdmcnO1xuaW1wb3J0IHNsaWRlbmF2UHJldmlvdXMgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc2xpZGVuYXYtcHJldmlvdXMuc3ZnJztcbmltcG9ydCBzbGlkZW5hdlByZXZpb3VzTGFyZ2UgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc2xpZGVuYXYtcHJldmlvdXMtbGFyZ2Uuc3ZnJztcbmltcG9ydCBzcGlubmVyIGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL3NwaW5uZXIuc3ZnJztcbmltcG9ydCB0b3RvcCBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy90b3RvcC5zdmcnO1xuaW1wb3J0IHskLCBhZGRDbGFzcywgYXBwbHksIGNzcywgZWFjaCwgaGFzQ2xhc3MsIGh5cGhlbmF0ZSwgaXNSdGwsIGlzU3RyaW5nLCBub29wLCBwYXJlbnRzLCBQcm9taXNlLCBzd2FwfSBmcm9tICd1aWtpdC11dGlsJztcblxuY29uc3QgcGFyc2VkID0ge307XG5jb25zdCBpY29ucyA9IHtcbiAgICBzcGlubmVyLFxuICAgIHRvdG9wLFxuICAgIG1hcmtlcixcbiAgICAnY2xvc2UtaWNvbic6IGNsb3NlSWNvbixcbiAgICAnY2xvc2UtbGFyZ2UnOiBjbG9zZUxhcmdlLFxuICAgICduYXZiYXItdG9nZ2xlLWljb24nOiBuYXZiYXJUb2dnbGVJY29uLFxuICAgICdvdmVybGF5LWljb24nOiBvdmVybGF5SWNvbixcbiAgICAncGFnaW5hdGlvbi1uZXh0JzogcGFnaW5hdGlvbk5leHQsXG4gICAgJ3BhZ2luYXRpb24tcHJldmlvdXMnOiBwYWdpbmF0aW9uUHJldmlvdXMsXG4gICAgJ3NlYXJjaC1pY29uJzogc2VhcmNoSWNvbixcbiAgICAnc2VhcmNoLWxhcmdlJzogc2VhcmNoTGFyZ2UsXG4gICAgJ3NlYXJjaC1uYXZiYXInOiBzZWFyY2hOYXZiYXIsXG4gICAgJ3NsaWRlbmF2LW5leHQnOiBzbGlkZW5hdk5leHQsXG4gICAgJ3NsaWRlbmF2LW5leHQtbGFyZ2UnOiBzbGlkZW5hdk5leHRMYXJnZSxcbiAgICAnc2xpZGVuYXYtcHJldmlvdXMnOiBzbGlkZW5hdlByZXZpb3VzLFxuICAgICdzbGlkZW5hdi1wcmV2aW91cy1sYXJnZSc6IHNsaWRlbmF2UHJldmlvdXNMYXJnZVxufTtcblxuY29uc3QgSWNvbiA9IHtcblxuICAgIGluc3RhbGwsXG5cbiAgICBleHRlbmRzOiBTVkcsXG5cbiAgICBhcmdzOiAnaWNvbicsXG5cbiAgICBwcm9wczogWydpY29uJ10sXG5cbiAgICBkYXRhOiB7aW5jbHVkZTogW119LFxuXG4gICAgaXNJY29uOiB0cnVlLFxuXG4gICAgYmVmb3JlQ29ubmVjdCgpIHtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsICd1ay1pY29uJyk7XG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBnZXRTdmcoKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGljb24gPSBnZXRJY29uKGFwcGx5UnRsKHRoaXMuaWNvbikpO1xuXG4gICAgICAgICAgICBpZiAoIWljb24pIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZWplY3QoJ0ljb24gbm90IGZvdW5kLicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKGljb24pO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmV4cG9ydCBkZWZhdWx0IEljb247XG5cbmV4cG9ydCBjb25zdCBJY29uQ29tcG9uZW50ID0ge1xuXG4gICAgYXJnczogZmFsc2UsXG5cbiAgICBleHRlbmRzOiBJY29uLFxuXG4gICAgZGF0YTogdm0gPT4gKHtcbiAgICAgICAgaWNvbjogaHlwaGVuYXRlKHZtLmNvbnN0cnVjdG9yLm9wdGlvbnMubmFtZSlcbiAgICB9KSxcblxuICAgIGJlZm9yZUNvbm5lY3QoKSB7XG4gICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCB0aGlzLiRuYW1lKTtcbiAgICB9XG5cbn07XG5cbmV4cG9ydCBjb25zdCBTbGlkZW5hdiA9IHtcblxuICAgIGV4dGVuZHM6IEljb25Db21wb25lbnQsXG5cbiAgICBiZWZvcmVDb25uZWN0KCkge1xuICAgICAgICBhZGRDbGFzcyh0aGlzLiRlbCwgJ3VrLXNsaWRlbmF2Jyk7XG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaWNvbih7aWNvbn0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIGhhc0NsYXNzKCRlbCwgJ3VrLXNsaWRlbmF2LWxhcmdlJylcbiAgICAgICAgICAgICAgICA/IGAke2ljb259LWxhcmdlYFxuICAgICAgICAgICAgICAgIDogaWNvbjtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5leHBvcnQgY29uc3QgU2VhcmNoID0ge1xuXG4gICAgZXh0ZW5kczogSWNvbkNvbXBvbmVudCxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaWNvbih7aWNvbn0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIGhhc0NsYXNzKCRlbCwgJ3VrLXNlYXJjaC1pY29uJykgJiYgcGFyZW50cygkZWwsICcudWstc2VhcmNoLWxhcmdlJykubGVuZ3RoXG4gICAgICAgICAgICAgICAgPyAnc2VhcmNoLWxhcmdlJ1xuICAgICAgICAgICAgICAgIDogcGFyZW50cygkZWwsICcudWstc2VhcmNoLW5hdmJhcicpLmxlbmd0aFxuICAgICAgICAgICAgICAgICAgICA/ICdzZWFyY2gtbmF2YmFyJ1xuICAgICAgICAgICAgICAgICAgICA6IGljb247XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZXhwb3J0IGNvbnN0IENsb3NlID0ge1xuXG4gICAgZXh0ZW5kczogSWNvbkNvbXBvbmVudCxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaWNvbigpIHtcbiAgICAgICAgICAgIHJldHVybiBgY2xvc2UtJHtoYXNDbGFzcyh0aGlzLiRlbCwgJ3VrLWNsb3NlLWxhcmdlJykgPyAnbGFyZ2UnIDogJ2ljb24nfWA7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZXhwb3J0IGNvbnN0IFNwaW5uZXIgPSB7XG5cbiAgICBleHRlbmRzOiBJY29uQ29tcG9uZW50LFxuXG4gICAgY29ubmVjdGVkKCkge1xuICAgICAgICB0aGlzLnN2Zy50aGVuKHN2ZyA9PiB0aGlzLnJhdGlvICE9PSAxICYmIGNzcygkKCdjaXJjbGUnLCBzdmcpLCAnc3Ryb2tlV2lkdGgnLCAxIC8gdGhpcy5yYXRpbyksIG5vb3ApO1xuICAgIH1cblxufTtcblxuZnVuY3Rpb24gaW5zdGFsbChVSWtpdCkge1xuICAgIFVJa2l0Lmljb24uYWRkID0gKG5hbWUsIHN2ZykgPT4ge1xuXG4gICAgICAgIGNvbnN0IGFkZGVkID0gaXNTdHJpbmcobmFtZSkgPyAoe1tuYW1lXTogc3ZnfSkgOiBuYW1lO1xuICAgICAgICBlYWNoKGFkZGVkLCAoc3ZnLCBuYW1lKSA9PiB7XG4gICAgICAgICAgICBpY29uc1tuYW1lXSA9IHN2ZztcbiAgICAgICAgICAgIGRlbGV0ZSBwYXJzZWRbbmFtZV07XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGlmIChVSWtpdC5faW5pdGlhbGl6ZWQpIHtcbiAgICAgICAgICAgIGFwcGx5KGRvY3VtZW50LmJvZHksIGVsID0+XG4gICAgICAgICAgICAgICAgZWFjaChVSWtpdC5nZXRDb21wb25lbnRzKGVsKSwgY21wID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY21wLiRvcHRpb25zLmlzSWNvbiAmJiBjbXAuaWNvbiBpbiBhZGRlZCAmJiBjbXAuJHJlc2V0KCk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICk7XG4gICAgICAgIH1cbiAgICB9O1xufVxuXG5mdW5jdGlvbiBnZXRJY29uKGljb24pIHtcblxuICAgIGlmICghaWNvbnNbaWNvbl0pIHtcbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuXG4gICAgaWYgKCFwYXJzZWRbaWNvbl0pIHtcbiAgICAgICAgcGFyc2VkW2ljb25dID0gJChpY29uc1tpY29uXS50cmltKCkpO1xuICAgIH1cblxuICAgIHJldHVybiBwYXJzZWRbaWNvbl0uY2xvbmVOb2RlKHRydWUpO1xufVxuXG5mdW5jdGlvbiBhcHBseVJ0bChpY29uKSB7XG4gICAgcmV0dXJuIGlzUnRsID8gc3dhcChzd2FwKGljb24sICdsZWZ0JywgJ3JpZ2h0JyksICdwcmV2aW91cycsICduZXh0JykgOiBpY29uO1xufVxuIiwiaW1wb3J0IHtjcmVhdGVFdmVudCwgY3NzLCBEaW1lbnNpb25zLCBlc2NhcGUsIGdldEltYWdlLCBpbmNsdWRlcywgSW50ZXJzZWN0aW9uT2JzZXJ2ZXIsIG5vb3AsIHF1ZXJ5QWxsLCBzdGFydHNXaXRoLCB0b0Zsb2F0LCB0b1B4LCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgYXJnczogJ2RhdGFTcmMnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgZGF0YVNyYzogU3RyaW5nLFxuICAgICAgICBkYXRhU3Jjc2V0OiBCb29sZWFuLFxuICAgICAgICBzaXplczogU3RyaW5nLFxuICAgICAgICB3aWR0aDogTnVtYmVyLFxuICAgICAgICBoZWlnaHQ6IE51bWJlcixcbiAgICAgICAgb2Zmc2V0VG9wOiBTdHJpbmcsXG4gICAgICAgIG9mZnNldExlZnQ6IFN0cmluZyxcbiAgICAgICAgdGFyZ2V0OiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBkYXRhU3JjOiAnJyxcbiAgICAgICAgZGF0YVNyY3NldDogZmFsc2UsXG4gICAgICAgIHNpemVzOiBmYWxzZSxcbiAgICAgICAgd2lkdGg6IGZhbHNlLFxuICAgICAgICBoZWlnaHQ6IGZhbHNlLFxuICAgICAgICBvZmZzZXRUb3A6ICc1MHZoJyxcbiAgICAgICAgb2Zmc2V0TGVmdDogMCxcbiAgICAgICAgdGFyZ2V0OiBmYWxzZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGNhY2hlS2V5KHtkYXRhU3JjfSkge1xuICAgICAgICAgICAgcmV0dXJuIGAke3RoaXMuJG5hbWV9LiR7ZGF0YVNyY31gO1xuICAgICAgICB9LFxuXG4gICAgICAgIHdpZHRoKHt3aWR0aCwgZGF0YVdpZHRofSkge1xuICAgICAgICAgICAgcmV0dXJuIHdpZHRoIHx8IGRhdGFXaWR0aDtcbiAgICAgICAgfSxcblxuICAgICAgICBoZWlnaHQoe2hlaWdodCwgZGF0YUhlaWdodH0pIHtcbiAgICAgICAgICAgIHJldHVybiBoZWlnaHQgfHwgZGF0YUhlaWdodDtcbiAgICAgICAgfSxcblxuICAgICAgICBzaXplcyh7c2l6ZXMsIGRhdGFTaXplc30pIHtcbiAgICAgICAgICAgIHJldHVybiBzaXplcyB8fCBkYXRhU2l6ZXM7XG4gICAgICAgIH0sXG5cbiAgICAgICAgaXNJbWcoXywgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gaXNJbWcoJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICB0YXJnZXQ6IHtcblxuICAgICAgICAgICAgZ2V0KHt0YXJnZXR9KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFt0aGlzLiRlbF0uY29uY2F0KHF1ZXJ5QWxsKHRhcmdldCwgdGhpcy4kZWwpKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIHdhdGNoKCkge1xuICAgICAgICAgICAgICAgIHRoaXMub2JzZXJ2ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgb2Zmc2V0VG9wKHtvZmZzZXRUb3B9KSB7XG4gICAgICAgICAgICByZXR1cm4gdG9QeChvZmZzZXRUb3AsICdoZWlnaHQnKTtcbiAgICAgICAgfSxcblxuICAgICAgICBvZmZzZXRMZWZ0KHtvZmZzZXRMZWZ0fSkge1xuICAgICAgICAgICAgcmV0dXJuIHRvUHgob2Zmc2V0TGVmdCwgJ3dpZHRoJyk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgaWYgKHN0b3JhZ2VbdGhpcy5jYWNoZUtleV0pIHtcbiAgICAgICAgICAgIHNldFNyY0F0dHJzKHRoaXMuJGVsLCBzdG9yYWdlW3RoaXMuY2FjaGVLZXldIHx8IHRoaXMuZGF0YVNyYywgdGhpcy5kYXRhU3Jjc2V0LCB0aGlzLnNpemVzKTtcbiAgICAgICAgfSBlbHNlIGlmICh0aGlzLmlzSW1nICYmIHRoaXMud2lkdGggJiYgdGhpcy5oZWlnaHQpIHtcbiAgICAgICAgICAgIHNldFNyY0F0dHJzKHRoaXMuJGVsLCBnZXRQbGFjZWhvbGRlckltYWdlKHRoaXMud2lkdGgsIHRoaXMuaGVpZ2h0LCB0aGlzLnNpemVzKSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm9ic2VydmVyID0gbmV3IEludGVyc2VjdGlvbk9ic2VydmVyKHRoaXMubG9hZCwge1xuICAgICAgICAgICAgcm9vdE1hcmdpbjogYCR7dGhpcy5vZmZzZXRUb3B9cHggJHt0aGlzLm9mZnNldExlZnR9cHhgXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZSh0aGlzLm9ic2VydmUpO1xuXG4gICAgfSxcblxuICAgIGRpc2Nvbm5lY3RlZCgpIHtcbiAgICAgICAgdGhpcy5vYnNlcnZlci5kaXNjb25uZWN0KCk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoe2ltYWdlfSkge1xuXG4gICAgICAgICAgICBpZiAoIWltYWdlICYmIGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmxvYWQodGhpcy5vYnNlcnZlci50YWtlUmVjb3JkcygpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKHRoaXMuaXNJbWcpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGltYWdlICYmIGltYWdlLnRoZW4oaW1nID0+IGltZyAmJiBpbWcuY3VycmVudFNyYyAhPT0gJycgJiYgc2V0U3JjQXR0cnModGhpcy4kZWwsIGN1cnJlbnRTcmMoaW1nKSkpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoZGF0YSkge1xuXG4gICAgICAgICAgICBpZiAodGhpcy5kYXRhU3Jjc2V0ICYmIHdpbmRvdy5kZXZpY2VQaXhlbFJhdGlvICE9PSAxKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBiZ1NpemUgPSBjc3ModGhpcy4kZWwsICdiYWNrZ3JvdW5kU2l6ZScpO1xuICAgICAgICAgICAgICAgIGlmIChiZ1NpemUubWF0Y2goL14oYXV0b1xccz8pKyQvKSB8fCB0b0Zsb2F0KGJnU2l6ZSkgPT09IGRhdGEuYmdTaXplKSB7XG4gICAgICAgICAgICAgICAgICAgIGRhdGEuYmdTaXplID0gZ2V0U291cmNlU2l6ZSh0aGlzLmRhdGFTcmNzZXQsIHRoaXMuc2l6ZXMpO1xuICAgICAgICAgICAgICAgICAgICBjc3ModGhpcy4kZWwsICdiYWNrZ3JvdW5kU2l6ZScsIGAke2RhdGEuYmdTaXplfXB4YCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgbG9hZChlbnRyaWVzKSB7XG5cbiAgICAgICAgICAgIGlmICghZW50cmllcy5zb21lKGVudHJ5ID0+IGVudHJ5LmlzSW50ZXJzZWN0aW5nKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5fZGF0YS5pbWFnZSA9IGdldEltYWdlKHRoaXMuZGF0YVNyYywgdGhpcy5kYXRhU3Jjc2V0LCB0aGlzLnNpemVzKS50aGVuKGltZyA9PiB7XG5cbiAgICAgICAgICAgICAgICBzZXRTcmNBdHRycyh0aGlzLiRlbCwgY3VycmVudFNyYyhpbWcpLCBpbWcuc3Jjc2V0LCBpbWcuc2l6ZXMpO1xuICAgICAgICAgICAgICAgIHN0b3JhZ2VbdGhpcy5jYWNoZUtleV0gPSBjdXJyZW50U3JjKGltZyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGltZztcblxuICAgICAgICAgICAgfSwgbm9vcCk7XG5cbiAgICAgICAgICAgIHRoaXMub2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xuICAgICAgICB9LFxuXG4gICAgICAgIG9ic2VydmUoKSB7XG4gICAgICAgICAgICBpZiAoIXRoaXMuX2RhdGEuaW1hZ2UgJiYgdGhpcy5fY29ubmVjdGVkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy50YXJnZXQuZm9yRWFjaChlbCA9PiB0aGlzLm9ic2VydmVyLm9ic2VydmUoZWwpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiBzZXRTcmNBdHRycyhlbCwgc3JjLCBzcmNzZXQsIHNpemVzKSB7XG5cbiAgICBpZiAoaXNJbWcoZWwpKSB7XG4gICAgICAgIHNpemVzICYmIChlbC5zaXplcyA9IHNpemVzKTtcbiAgICAgICAgc3Jjc2V0ICYmIChlbC5zcmNzZXQgPSBzcmNzZXQpO1xuICAgICAgICBzcmMgJiYgKGVsLnNyYyA9IHNyYyk7XG4gICAgfSBlbHNlIGlmIChzcmMpIHtcblxuICAgICAgICBjb25zdCBjaGFuZ2UgPSAhaW5jbHVkZXMoZWwuc3R5bGUuYmFja2dyb3VuZEltYWdlLCBzcmMpO1xuICAgICAgICBpZiAoY2hhbmdlKSB7XG4gICAgICAgICAgICBjc3MoZWwsICdiYWNrZ3JvdW5kSW1hZ2UnLCBgdXJsKCR7ZXNjYXBlKHNyYyl9KWApO1xuICAgICAgICAgICAgdHJpZ2dlcihlbCwgY3JlYXRlRXZlbnQoJ2xvYWQnLCBmYWxzZSkpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn1cblxuZnVuY3Rpb24gZ2V0UGxhY2Vob2xkZXJJbWFnZSh3aWR0aCwgaGVpZ2h0LCBzaXplcykge1xuXG4gICAgaWYgKHNpemVzKSB7XG4gICAgICAgICh7d2lkdGgsIGhlaWdodH0gPSBEaW1lbnNpb25zLnJhdGlvKHt3aWR0aCwgaGVpZ2h0fSwgJ3dpZHRoJywgdG9QeChzaXplc1RvUGl4ZWwoc2l6ZXMpKSkpO1xuICAgIH1cblxuICAgIHJldHVybiBgZGF0YTppbWFnZS9zdmcreG1sO3V0ZjgsPHN2ZyB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCIgd2lkdGg9XCIke3dpZHRofVwiIGhlaWdodD1cIiR7aGVpZ2h0fVwiPjwvc3ZnPmA7XG59XG5cbmNvbnN0IHNpemVzUmUgPSAvXFxzKiguKj8pXFxzKihcXHcrfGNhbGNcXCguKj9cXCkpXFxzKig/Oix8JCkvZztcbmZ1bmN0aW9uIHNpemVzVG9QaXhlbChzaXplcykge1xuICAgIGxldCBtYXRjaGVzO1xuXG4gICAgc2l6ZXNSZS5sYXN0SW5kZXggPSAwO1xuXG4gICAgd2hpbGUgKChtYXRjaGVzID0gc2l6ZXNSZS5leGVjKHNpemVzKSkpIHtcbiAgICAgICAgaWYgKCFtYXRjaGVzWzFdIHx8IHdpbmRvdy5tYXRjaE1lZGlhKG1hdGNoZXNbMV0pLm1hdGNoZXMpIHtcbiAgICAgICAgICAgIG1hdGNoZXMgPSBldmFsdWF0ZVNpemUobWF0Y2hlc1syXSk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBtYXRjaGVzIHx8ICcxMDB2dyc7XG59XG5cbmNvbnN0IHNpemVSZSA9IC9cXGQrKD86XFx3K3wlKS9nO1xuY29uc3QgYWRkaXRpb25SZSA9IC9bKy1dPyhcXGQrKS9nO1xuZnVuY3Rpb24gZXZhbHVhdGVTaXplKHNpemUpIHtcbiAgICByZXR1cm4gc3RhcnRzV2l0aChzaXplLCAnY2FsYycpXG4gICAgICAgID8gc2l6ZVxuICAgICAgICAgICAgLnN1YnN0cmluZyg1LCBzaXplLmxlbmd0aCAtIDEpXG4gICAgICAgICAgICAucmVwbGFjZShzaXplUmUsIHNpemUgPT4gdG9QeChzaXplKSlcbiAgICAgICAgICAgIC5yZXBsYWNlKC8gL2csICcnKVxuICAgICAgICAgICAgLm1hdGNoKGFkZGl0aW9uUmUpXG4gICAgICAgICAgICAucmVkdWNlKChhLCBiKSA9PiBhICsgK2IsIDApXG4gICAgICAgIDogc2l6ZTtcbn1cblxuY29uc3Qgc3JjU2V0UmUgPSAvXFxzK1xcZCt3XFxzKig/Oix8JCkvZztcbmZ1bmN0aW9uIGdldFNvdXJjZVNpemUoc3Jjc2V0LCBzaXplcykge1xuICAgIGNvbnN0IHNyY1NpemUgPSB0b1B4KHNpemVzVG9QaXhlbChzaXplcykpO1xuICAgIGNvbnN0IGRlc2NyaXB0b3JzID0gKHNyY3NldC5tYXRjaChzcmNTZXRSZSkgfHwgW10pLm1hcCh0b0Zsb2F0KS5zb3J0KChhLCBiKSA9PiBhIC0gYik7XG5cbiAgICByZXR1cm4gZGVzY3JpcHRvcnMuZmlsdGVyKHNpemUgPT4gc2l6ZSA+PSBzcmNTaXplKVswXSB8fCBkZXNjcmlwdG9ycy5wb3AoKSB8fCAnJztcbn1cblxuZnVuY3Rpb24gaXNJbWcoZWwpIHtcbiAgICByZXR1cm4gZWwudGFnTmFtZSA9PT0gJ0lNRyc7XG59XG5cbmZ1bmN0aW9uIGN1cnJlbnRTcmMoZWwpIHtcbiAgICByZXR1cm4gZWwuY3VycmVudFNyYyB8fCBlbC5zcmM7XG59XG5cbmNvbnN0IGtleSA9ICdfX3Rlc3RfXyc7XG5sZXQgc3RvcmFnZTtcblxuLy8gd29ya2Fyb3VuZCBmb3IgU2FmYXJpJ3MgcHJpdmF0ZSBicm93c2luZyBtb2RlIGFuZCBhY2Nlc3Npbmcgc2Vzc2lvblN0b3JhZ2UgaW4gQmxpbmtcbnRyeSB7XG4gICAgc3RvcmFnZSA9IHdpbmRvdy5zZXNzaW9uU3RvcmFnZSB8fCB7fTtcbiAgICBzdG9yYWdlW2tleV0gPSAxO1xuICAgIGRlbGV0ZSBzdG9yYWdlW2tleV07XG59IGNhdGNoIChlKSB7XG4gICAgc3RvcmFnZSA9IHt9O1xufVxuIiwiaW1wb3J0IHtnZXRDc3NWYXIsIGlzU3RyaW5nLCB0b0Zsb2F0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgbWVkaWE6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtZWRpYTogZmFsc2VcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBtYXRjaE1lZGlhKCkge1xuICAgICAgICAgICAgY29uc3QgbWVkaWEgPSB0b01lZGlhKHRoaXMubWVkaWEpO1xuICAgICAgICAgICAgcmV0dXJuICFtZWRpYSB8fCB3aW5kb3cubWF0Y2hNZWRpYShtZWRpYSkubWF0Y2hlcztcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiB0b01lZGlhKHZhbHVlKSB7XG5cbiAgICBpZiAoaXNTdHJpbmcodmFsdWUpKSB7XG4gICAgICAgIGlmICh2YWx1ZVswXSA9PT0gJ0AnKSB7XG4gICAgICAgICAgICBjb25zdCBuYW1lID0gYGJyZWFrcG9pbnQtJHt2YWx1ZS5zdWJzdHIoMSl9YDtcbiAgICAgICAgICAgIHZhbHVlID0gdG9GbG9hdChnZXRDc3NWYXIobmFtZSkpO1xuICAgICAgICB9IGVsc2UgaWYgKGlzTmFOKHZhbHVlKSkge1xuICAgICAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHZhbHVlICYmICFpc05hTih2YWx1ZSkgPyBgKG1pbi13aWR0aDogJHt2YWx1ZX1weClgIDogZmFsc2U7XG59XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IE1lZGlhIGZyb20gJy4uL21peGluL21lZGlhJztcbmltcG9ydCB7YXR0ciwgZ2V0Q3NzVmFyLCB0b2dnbGVDbGFzcywgdW53cmFwLCB3cmFwSW5uZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgTWVkaWFdLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgZmlsbDogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgZmlsbDogJycsXG4gICAgICAgIGNsc1dyYXBwZXI6ICd1ay1sZWFkZXItZmlsbCcsXG4gICAgICAgIGNsc0hpZGU6ICd1ay1sZWFkZXItaGlkZScsXG4gICAgICAgIGF0dHJGaWxsOiAnZGF0YS1maWxsJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGZpbGwoe2ZpbGx9KSB7XG4gICAgICAgICAgICByZXR1cm4gZmlsbCB8fCBnZXRDc3NWYXIoJ2xlYWRlci1maWxsLWNvbnRlbnQnKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgW3RoaXMud3JhcHBlcl0gPSB3cmFwSW5uZXIodGhpcy4kZWwsIGA8c3BhbiBjbGFzcz1cIiR7dGhpcy5jbHNXcmFwcGVyfVwiPmApO1xuICAgIH0sXG5cbiAgICBkaXNjb25uZWN0ZWQoKSB7XG4gICAgICAgIHVud3JhcCh0aGlzLndyYXBwZXIuY2hpbGROb2Rlcyk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoe2NoYW5nZWQsIHdpZHRofSkge1xuXG4gICAgICAgICAgICBjb25zdCBwcmV2ID0gd2lkdGg7XG5cbiAgICAgICAgICAgIHdpZHRoID0gTWF0aC5mbG9vcih0aGlzLiRlbC5vZmZzZXRXaWR0aCAvIDIpO1xuXG4gICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgIHdpZHRoLFxuICAgICAgICAgICAgICAgIGZpbGw6IHRoaXMuZmlsbCxcbiAgICAgICAgICAgICAgICBjaGFuZ2VkOiBjaGFuZ2VkIHx8IHByZXYgIT09IHdpZHRoLFxuICAgICAgICAgICAgICAgIGhpZGU6ICF0aGlzLm1hdGNoTWVkaWFcbiAgICAgICAgICAgIH07XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoZGF0YSkge1xuXG4gICAgICAgICAgICB0b2dnbGVDbGFzcyh0aGlzLndyYXBwZXIsIHRoaXMuY2xzSGlkZSwgZGF0YS5oaWRlKTtcblxuICAgICAgICAgICAgaWYgKGRhdGEuY2hhbmdlZCkge1xuICAgICAgICAgICAgICAgIGRhdGEuY2hhbmdlZCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIGF0dHIodGhpcy53cmFwcGVyLCB0aGlzLmF0dHJGaWxsLCBuZXcgQXJyYXkoZGF0YS53aWR0aCkuam9pbihkYXRhLmZpbGwpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHskfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgY29udGFpbmVyOiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgY29udGFpbmVyOiB0cnVlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgY29udGFpbmVyKHtjb250YWluZXJ9KSB7XG4gICAgICAgICAgICByZXR1cm4gY29udGFpbmVyID09PSB0cnVlICYmIHRoaXMuJGNvbnRhaW5lciB8fCBjb250YWluZXIgJiYgJChjb250YWluZXIpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgeyQsIGFkZENsYXNzLCBhcHBlbmQsIGNzcywgaGFzQ2xhc3MsIG9uLCBvbmNlLCBwb2ludGVyVXAsIFByb21pc2UsIHJlbW92ZUNsYXNzLCB0b01zLCB3aWR0aCwgd2l0aGlufSBmcm9tICd1aWtpdC11dGlsJztcbmltcG9ydCBDbGFzcyBmcm9tICcuL2NsYXNzJztcbmltcG9ydCBDb250YWluZXIgZnJvbSAnLi9jb250YWluZXInO1xuaW1wb3J0IFRvZ2dsYWJsZSBmcm9tICcuL3RvZ2dsYWJsZSc7XG5cbmxldCBhY3RpdmU7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBDb250YWluZXIsIFRvZ2dsYWJsZV0sXG5cbiAgICBwcm9wczoge1xuICAgICAgICBzZWxQYW5lbDogU3RyaW5nLFxuICAgICAgICBzZWxDbG9zZTogU3RyaW5nLFxuICAgICAgICBlc2NDbG9zZTogQm9vbGVhbixcbiAgICAgICAgYmdDbG9zZTogQm9vbGVhbixcbiAgICAgICAgc3RhY2s6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBjbHM6ICd1ay1vcGVuJyxcbiAgICAgICAgZXNjQ2xvc2U6IHRydWUsXG4gICAgICAgIGJnQ2xvc2U6IHRydWUsXG4gICAgICAgIG92ZXJsYXk6IHRydWUsXG4gICAgICAgIHN0YWNrOiBmYWxzZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIHBhbmVsKHtzZWxQYW5lbH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuICQoc2VsUGFuZWwsICRlbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdHJhbnNpdGlvbkVsZW1lbnQoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5wYW5lbDtcbiAgICAgICAgfSxcblxuICAgICAgICBiZ0Nsb3NlKHtiZ0Nsb3NlfSkge1xuICAgICAgICAgICAgcmV0dXJuIGJnQ2xvc2UgJiYgdGhpcy5wYW5lbDtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGJlZm9yZURpc2Nvbm5lY3QoKSB7XG4gICAgICAgIGlmICh0aGlzLmlzVG9nZ2xlZCgpKSB7XG4gICAgICAgICAgICB0aGlzLnRvZ2dsZU5vdyh0aGlzLiRlbCwgZmFsc2UpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuc2VsQ2xvc2U7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICd0b2dnbGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcblxuICAgICAgICAgICAgICAgIGlmIChlLmRlZmF1bHRQcmV2ZW50ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuICAgICAgICAgICAgbmFtZTogJ2JlZm9yZXNob3cnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHByZXYgPSBhY3RpdmUgJiYgYWN0aXZlICE9PSB0aGlzICYmIGFjdGl2ZTtcblxuICAgICAgICAgICAgICAgIGFjdGl2ZSA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICBpZiAocHJldikge1xuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5zdGFjaykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5wcmV2ID0gcHJldjtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgYWN0aXZlID0gcHJldjtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHByZXYuaXNUb2dnbGVkKCkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwcmV2LmhpZGUoKS50aGVuKHRoaXMuc2hvdyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9uY2UocHJldi4kZWwsICdiZWZvcmVzaG93IGhpZGRlbicsIHRoaXMuc2hvdywgZmFsc2UsICh7dGFyZ2V0LCB0eXBlfSkgPT4gdHlwZSA9PT0gJ2hpZGRlbicgJiYgdGFyZ2V0ID09PSBwcmV2LiRlbCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZWdpc3RlckV2ZW50cygpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdzaG93JyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICghaGFzQ2xhc3MoZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LCB0aGlzLmNsc1BhZ2UpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2Nyb2xsYmFyV2lkdGggPSB3aWR0aCh3aW5kb3cpIC0gd2lkdGgoZG9jdW1lbnQpO1xuICAgICAgICAgICAgICAgICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ292ZXJmbG93WScsIHRoaXMuc2Nyb2xsYmFyV2lkdGggJiYgdGhpcy5vdmVybGF5ID8gJ3Njcm9sbCcgOiAnJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYWRkQ2xhc3MoZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LCB0aGlzLmNsc1BhZ2UpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdoaWRlJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcbiAgICAgICAgICAgICAgICBpZiAoIWFjdGl2ZSB8fCBhY3RpdmUgPT09IHRoaXMgJiYgIXRoaXMucHJldikge1xuICAgICAgICAgICAgICAgICAgICBkZXJlZ2lzdGVyRXZlbnRzKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnaGlkZGVuJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGxldCBmb3VuZCwge3ByZXZ9ID0gdGhpcztcblxuICAgICAgICAgICAgICAgIGFjdGl2ZSA9IGFjdGl2ZSAmJiBhY3RpdmUgIT09IHRoaXMgJiYgYWN0aXZlIHx8IHByZXY7XG5cbiAgICAgICAgICAgICAgICBpZiAoIWFjdGl2ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgIGNzcyhkb2N1bWVudC5ib2R5LCAnb3ZlcmZsb3dZJywgJycpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgd2hpbGUgKHByZXYpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHByZXYuY2xzUGFnZSA9PT0gdGhpcy5jbHNQYWdlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZm91bmQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBwcmV2ID0gcHJldi5wcmV2O1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmICghZm91bmQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3MoZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LCB0aGlzLmNsc1BhZ2UpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgdG9nZ2xlKCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaXNUb2dnbGVkKCkgPyB0aGlzLmhpZGUoKSA6IHRoaXMuc2hvdygpO1xuICAgICAgICB9LFxuXG4gICAgICAgIHNob3coKSB7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLmlzVG9nZ2xlZCgpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVzb2x2ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAodGhpcy5jb250YWluZXIgJiYgdGhpcy4kZWwucGFyZW50Tm9kZSAhPT0gdGhpcy5jb250YWluZXIpIHtcbiAgICAgICAgICAgICAgICBhcHBlbmQodGhpcy5jb250YWluZXIsIHRoaXMuJGVsKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UocmVzb2x2ZSA9PlxuICAgICAgICAgICAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvdygpLnRoZW4ocmVzb2x2ZSlcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLnRvZ2dsZUVsZW1lbnQodGhpcy4kZWwsIHRydWUsIGFuaW1hdGUodGhpcykpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGhpZGUoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5pc1RvZ2dsZWQoKVxuICAgICAgICAgICAgICAgID8gdGhpcy50b2dnbGVFbGVtZW50KHRoaXMuJGVsLCBmYWxzZSwgYW5pbWF0ZSh0aGlzKSlcbiAgICAgICAgICAgICAgICA6IFByb21pc2UucmVzb2x2ZSgpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGdldEFjdGl2ZSgpIHtcbiAgICAgICAgICAgIHJldHVybiBhY3RpdmU7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxubGV0IGV2ZW50cztcblxuZnVuY3Rpb24gcmVnaXN0ZXJFdmVudHMoKSB7XG5cbiAgICBpZiAoZXZlbnRzKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBldmVudHMgPSBbXG4gICAgICAgIG9uKGRvY3VtZW50LCBwb2ludGVyVXAsICh7dGFyZ2V0LCBkZWZhdWx0UHJldmVudGVkfSkgPT4ge1xuICAgICAgICAgICAgaWYgKGFjdGl2ZSAmJiBhY3RpdmUuYmdDbG9zZSAmJiAhZGVmYXVsdFByZXZlbnRlZCAmJiAoIWFjdGl2ZS5vdmVybGF5IHx8IHdpdGhpbih0YXJnZXQsIGFjdGl2ZS4kZWwpKSAmJiAhd2l0aGluKHRhcmdldCwgYWN0aXZlLnBhbmVsKSkge1xuICAgICAgICAgICAgICAgIGFjdGl2ZS5oaWRlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pLFxuICAgICAgICBvbihkb2N1bWVudCwgJ2tleWRvd24nLCBlID0+IHtcbiAgICAgICAgICAgIGlmIChlLmtleUNvZGUgPT09IDI3ICYmIGFjdGl2ZSAmJiBhY3RpdmUuZXNjQ2xvc2UpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgYWN0aXZlLmhpZGUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSlcbiAgICBdO1xufVxuXG5mdW5jdGlvbiBkZXJlZ2lzdGVyRXZlbnRzKCkge1xuICAgIGV2ZW50cyAmJiBldmVudHMuZm9yRWFjaCh1bmJpbmQgPT4gdW5iaW5kKCkpO1xuICAgIGV2ZW50cyA9IG51bGw7XG59XG5cbmZ1bmN0aW9uIGFuaW1hdGUoe3RyYW5zaXRpb25FbGVtZW50LCBfdG9nZ2xlfSkge1xuICAgIHJldHVybiAoZWwsIHNob3cpID0+XG4gICAgICAgIG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+XG4gICAgICAgICAgICBvbmNlKGVsLCAnc2hvdyBoaWRlJywgKCkgPT4ge1xuICAgICAgICAgICAgICAgIGVsLl9yZWplY3QgJiYgZWwuX3JlamVjdCgpO1xuICAgICAgICAgICAgICAgIGVsLl9yZWplY3QgPSByZWplY3Q7XG5cbiAgICAgICAgICAgICAgICBfdG9nZ2xlKGVsLCBzaG93KTtcblxuICAgICAgICAgICAgICAgIGlmICh0b01zKGNzcyh0cmFuc2l0aW9uRWxlbWVudCwgJ3RyYW5zaXRpb25EdXJhdGlvbicpKSkge1xuICAgICAgICAgICAgICAgICAgICBvbmNlKHRyYW5zaXRpb25FbGVtZW50LCAndHJhbnNpdGlvbmVuZCcsIHJlc29sdmUsIGZhbHNlLCBlID0+IGUudGFyZ2V0ID09PSB0cmFuc2l0aW9uRWxlbWVudCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pXG4gICAgICAgICk7XG59XG4iLCJpbXBvcnQgTW9kYWwgZnJvbSAnLi4vbWl4aW4vbW9kYWwnO1xuaW1wb3J0IHskLCBhZGRDbGFzcywgYXNzaWduLCBjc3MsIGhhc0NsYXNzLCBoZWlnaHQsIGh0bWwsIGlzU3RyaW5nLCBvbiwgUHJvbWlzZSwgcmVtb3ZlQ2xhc3N9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBpbnN0YWxsLFxuXG4gICAgbWl4aW5zOiBbTW9kYWxdLFxuXG4gICAgZGF0YToge1xuICAgICAgICBjbHNQYWdlOiAndWstbW9kYWwtcGFnZScsXG4gICAgICAgIHNlbFBhbmVsOiAnLnVrLW1vZGFsLWRpYWxvZycsXG4gICAgICAgIHNlbENsb3NlOiAnLnVrLW1vZGFsLWNsb3NlLCAudWstbW9kYWwtY2xvc2UtZGVmYXVsdCwgLnVrLW1vZGFsLWNsb3NlLW91dHNpZGUsIC51ay1tb2RhbC1jbG9zZS1mdWxsJ1xuICAgIH0sXG5cbiAgICBldmVudHM6IFtcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnc2hvdycsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAoaGFzQ2xhc3ModGhpcy5wYW5lbCwgJ3VrLW1hcmdpbi1hdXRvLXZlcnRpY2FsJykpIHtcbiAgICAgICAgICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsICd1ay1mbGV4Jyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnZGlzcGxheScsICdibG9jaycpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGhlaWdodCh0aGlzLiRlbCk7IC8vIGZvcmNlIHJlZmxvd1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdoaWRkZW4nLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnZGlzcGxheScsICcnKTtcbiAgICAgICAgICAgICAgICByZW1vdmVDbGFzcyh0aGlzLiRlbCwgJ3VrLWZsZXgnKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdXG5cbn07XG5cbmZ1bmN0aW9uIGluc3RhbGwoVUlraXQpIHtcblxuICAgIFVJa2l0Lm1vZGFsLmRpYWxvZyA9IGZ1bmN0aW9uIChjb250ZW50LCBvcHRpb25zKSB7XG5cbiAgICAgICAgY29uc3QgZGlhbG9nID0gVUlraXQubW9kYWwoYFxuICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsXCI+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsLWRpYWxvZ1wiPiR7Y29udGVudH08L2Rpdj5cbiAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgYCwgb3B0aW9ucyk7XG5cbiAgICAgICAgZGlhbG9nLnNob3coKTtcblxuICAgICAgICBvbihkaWFsb2cuJGVsLCAnaGlkZGVuJywgKHt0YXJnZXQsIGN1cnJlbnRUYXJnZXR9KSA9PiB7XG4gICAgICAgICAgICBpZiAodGFyZ2V0ID09PSBjdXJyZW50VGFyZ2V0KSB7XG4gICAgICAgICAgICAgICAgUHJvbWlzZS5yZXNvbHZlKCgpID0+IGRpYWxvZy4kZGVzdHJveSh0cnVlKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiBkaWFsb2c7XG4gICAgfTtcblxuICAgIFVJa2l0Lm1vZGFsLmFsZXJ0ID0gZnVuY3Rpb24gKG1lc3NhZ2UsIG9wdGlvbnMpIHtcblxuICAgICAgICBvcHRpb25zID0gYXNzaWduKHtiZ0Nsb3NlOiBmYWxzZSwgZXNjQ2xvc2U6IGZhbHNlLCBsYWJlbHM6IFVJa2l0Lm1vZGFsLmxhYmVsc30sIG9wdGlvbnMpO1xuXG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShcbiAgICAgICAgICAgIHJlc29sdmUgPT4gb24oVUlraXQubW9kYWwuZGlhbG9nKGBcbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwidWstbW9kYWwtYm9keVwiPiR7aXNTdHJpbmcobWVzc2FnZSkgPyBtZXNzYWdlIDogaHRtbChtZXNzYWdlKX08L2Rpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwidWstbW9kYWwtZm9vdGVyIHVrLXRleHQtcmlnaHRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tcHJpbWFyeSB1ay1tb2RhbC1jbG9zZVwiIGF1dG9mb2N1cz4ke29wdGlvbnMubGFiZWxzLm9rfTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgYCwgb3B0aW9ucykuJGVsLCAnaGlkZScsIHJlc29sdmUpXG4gICAgICAgICk7XG4gICAgfTtcblxuICAgIFVJa2l0Lm1vZGFsLmNvbmZpcm0gPSBmdW5jdGlvbiAobWVzc2FnZSwgb3B0aW9ucykge1xuXG4gICAgICAgIG9wdGlvbnMgPSBhc3NpZ24oe2JnQ2xvc2U6IGZhbHNlLCBlc2NDbG9zZTogdHJ1ZSwgbGFiZWxzOiBVSWtpdC5tb2RhbC5sYWJlbHN9LCBvcHRpb25zKTtcblxuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBjb25maXJtID0gVUlraXQubW9kYWwuZGlhbG9nKGBcbiAgICAgICAgICAgICAgICA8Zm9ybT5cbiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsLWJvZHlcIj4ke2lzU3RyaW5nKG1lc3NhZ2UpID8gbWVzc2FnZSA6IGh0bWwobWVzc2FnZSl9PC9kaXY+XG4gICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbC1mb290ZXIgdWstdGV4dC1yaWdodFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tZGVmYXVsdCB1ay1tb2RhbC1jbG9zZVwiIHR5cGU9XCJidXR0b25cIj4ke29wdGlvbnMubGFiZWxzLmNhbmNlbH08L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJ1ay1idXR0b24gdWstYnV0dG9uLXByaW1hcnlcIiBhdXRvZm9jdXM+JHtvcHRpb25zLmxhYmVscy5va308L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPC9mb3JtPlxuICAgICAgICAgICAgYCwgb3B0aW9ucyk7XG5cbiAgICAgICAgICAgIGxldCByZXNvbHZlZCA9IGZhbHNlO1xuXG4gICAgICAgICAgICBvbihjb25maXJtLiRlbCwgJ3N1Ym1pdCcsICdmb3JtJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgY29uZmlybS5oaWRlKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIG9uKGNvbmZpcm0uJGVsLCAnaGlkZScsICgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoIXJlc29sdmVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlamVjdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBVSWtpdC5tb2RhbC5wcm9tcHQgPSBmdW5jdGlvbiAobWVzc2FnZSwgdmFsdWUsIG9wdGlvbnMpIHtcblxuICAgICAgICBvcHRpb25zID0gYXNzaWduKHtiZ0Nsb3NlOiBmYWxzZSwgZXNjQ2xvc2U6IHRydWUsIGxhYmVsczogVUlraXQubW9kYWwubGFiZWxzfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBwcm9tcHQgPSBVSWtpdC5tb2RhbC5kaWFsb2coYFxuICAgICAgICAgICAgICAgICAgICA8Zm9ybSBjbGFzcz1cInVrLWZvcm0tc3RhY2tlZFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsLWJvZHlcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWw+JHtpc1N0cmluZyhtZXNzYWdlKSA/IG1lc3NhZ2UgOiBodG1sKG1lc3NhZ2UpfTwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGlucHV0IGNsYXNzPVwidWstaW5wdXRcIiBhdXRvZm9jdXM+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbC1mb290ZXIgdWstdGV4dC1yaWdodFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJ1ay1idXR0b24gdWstYnV0dG9uLWRlZmF1bHQgdWstbW9kYWwtY2xvc2VcIiB0eXBlPVwiYnV0dG9uXCI+JHtvcHRpb25zLmxhYmVscy5jYW5jZWx9PC9idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tcHJpbWFyeVwiPiR7b3B0aW9ucy5sYWJlbHMub2t9PC9idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgPC9mb3JtPlxuICAgICAgICAgICAgICAgIGAsIG9wdGlvbnMpLFxuICAgICAgICAgICAgICAgIGlucHV0ID0gJCgnaW5wdXQnLCBwcm9tcHQuJGVsKTtcblxuICAgICAgICAgICAgaW5wdXQudmFsdWUgPSB2YWx1ZTtcblxuICAgICAgICAgICAgbGV0IHJlc29sdmVkID0gZmFsc2U7XG5cbiAgICAgICAgICAgIG9uKHByb21wdC4kZWwsICdzdWJtaXQnLCAnZm9ybScsIGUgPT4ge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlKGlucHV0LnZhbHVlKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgcHJvbXB0LmhpZGUoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgb24ocHJvbXB0LiRlbCwgJ2hpZGUnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCFyZXNvbHZlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKG51bGwpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBVSWtpdC5tb2RhbC5sYWJlbHMgPSB7XG4gICAgICAgIG9rOiAnT2snLFxuICAgICAgICBjYW5jZWw6ICdDYW5jZWwnXG4gICAgfTtcblxufVxuIiwiaW1wb3J0IEFjY29yZGlvbiBmcm9tICcuL2FjY29yZGlvbic7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGV4dGVuZHM6IEFjY29yZGlvbixcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdGFyZ2V0czogJz4gLnVrLXBhcmVudCcsXG4gICAgICAgIHRvZ2dsZTogJz4gYScsXG4gICAgICAgIGNvbnRlbnQ6ICc+IHVsJ1xuICAgIH1cblxufTtcbiIsImltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQgRmxleEJ1ZyBmcm9tICcuLi9taXhpbi9mbGV4LWJ1Zyc7XG5pbXBvcnQgeyQsICQkLCBhZGRDbGFzcywgYWZ0ZXIsIGFzc2lnbiwgY3NzLCBoZWlnaHQsIGluY2x1ZGVzLCBpc1J0bCwgaXNWaXNpYmxlLCBtYXRjaGVzLCBub29wLCBQcm9taXNlLCBxdWVyeSwgcmVtb3ZlLCB0b0Zsb2F0LCBUcmFuc2l0aW9uLCB3aXRoaW59IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgRmxleEJ1Z10sXG5cbiAgICBwcm9wczoge1xuICAgICAgICBkcm9wZG93bjogU3RyaW5nLFxuICAgICAgICBtb2RlOiAnbGlzdCcsXG4gICAgICAgIGFsaWduOiBTdHJpbmcsXG4gICAgICAgIG9mZnNldDogTnVtYmVyLFxuICAgICAgICBib3VuZGFyeTogQm9vbGVhbixcbiAgICAgICAgYm91bmRhcnlBbGlnbjogQm9vbGVhbixcbiAgICAgICAgY2xzRHJvcDogU3RyaW5nLFxuICAgICAgICBkZWxheVNob3c6IE51bWJlcixcbiAgICAgICAgZGVsYXlIaWRlOiBOdW1iZXIsXG4gICAgICAgIGRyb3BiYXI6IEJvb2xlYW4sXG4gICAgICAgIGRyb3BiYXJNb2RlOiBTdHJpbmcsXG4gICAgICAgIGRyb3BiYXJBbmNob3I6IEJvb2xlYW4sXG4gICAgICAgIGR1cmF0aW9uOiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBkcm9wZG93bjogJy51ay1uYXZiYXItbmF2ID4gbGknLFxuICAgICAgICBhbGlnbjogIWlzUnRsID8gJ2xlZnQnIDogJ3JpZ2h0JyxcbiAgICAgICAgY2xzRHJvcDogJ3VrLW5hdmJhci1kcm9wZG93bicsXG4gICAgICAgIG1vZGU6IHVuZGVmaW5lZCxcbiAgICAgICAgb2Zmc2V0OiB1bmRlZmluZWQsXG4gICAgICAgIGRlbGF5U2hvdzogdW5kZWZpbmVkLFxuICAgICAgICBkZWxheUhpZGU6IHVuZGVmaW5lZCxcbiAgICAgICAgYm91bmRhcnlBbGlnbjogdW5kZWZpbmVkLFxuICAgICAgICBmbGlwOiAneCcsXG4gICAgICAgIGJvdW5kYXJ5OiB0cnVlLFxuICAgICAgICBkcm9wYmFyOiBmYWxzZSxcbiAgICAgICAgZHJvcGJhck1vZGU6ICdzbGlkZScsXG4gICAgICAgIGRyb3BiYXJBbmNob3I6IGZhbHNlLFxuICAgICAgICBkdXJhdGlvbjogMjAwLFxuICAgICAgICBmb3JjZUhlaWdodDogdHJ1ZSxcbiAgICAgICAgc2VsTWluSGVpZ2h0OiAnLnVrLW5hdmJhci1uYXYgPiBsaSA+IGEsIC51ay1uYXZiYXItaXRlbSwgLnVrLW5hdmJhci10b2dnbGUnXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgYm91bmRhcnkoe2JvdW5kYXJ5LCBib3VuZGFyeUFsaWdufSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gKGJvdW5kYXJ5ID09PSB0cnVlIHx8IGJvdW5kYXJ5QWxpZ24pID8gJGVsIDogYm91bmRhcnk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZHJvcGJhckFuY2hvcih7ZHJvcGJhckFuY2hvcn0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHF1ZXJ5KGRyb3BiYXJBbmNob3IsICRlbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgcG9zKHthbGlnbn0pIHtcbiAgICAgICAgICAgIHJldHVybiBgYm90dG9tLSR7YWxpZ259YDtcbiAgICAgICAgfSxcblxuICAgICAgICBkcm9wZG93bnMoe2Ryb3Bkb3duLCBjbHNEcm9wfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJCQoYCR7ZHJvcGRvd259IC4ke2Nsc0Ryb3B9YCwgJGVsKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGJlZm9yZUNvbm5lY3QoKSB7XG5cbiAgICAgICAgY29uc3Qge2Ryb3BiYXJ9ID0gdGhpcy4kcHJvcHM7XG5cbiAgICAgICAgdGhpcy5kcm9wYmFyID0gZHJvcGJhciAmJiAocXVlcnkoZHJvcGJhciwgdGhpcy4kZWwpIHx8ICQoJysgLnVrLW5hdmJhci1kcm9wYmFyJywgdGhpcy4kZWwpIHx8ICQoJzxkaXY+PC9kaXY+JykpO1xuXG4gICAgICAgIGlmICh0aGlzLmRyb3BiYXIpIHtcblxuICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy5kcm9wYmFyLCAndWstbmF2YmFyLWRyb3BiYXInKTtcblxuICAgICAgICAgICAgaWYgKHRoaXMuZHJvcGJhck1vZGUgPT09ICdzbGlkZScpIHtcbiAgICAgICAgICAgICAgICBhZGRDbGFzcyh0aGlzLmRyb3BiYXIsICd1ay1uYXZiYXItZHJvcGJhci1zbGlkZScpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgZGlzY29ubmVjdGVkKCkge1xuICAgICAgICB0aGlzLmRyb3BiYXIgJiYgcmVtb3ZlKHRoaXMuZHJvcGJhcik7XG4gICAgfSxcblxuICAgIHVwZGF0ZSgpIHtcblxuICAgICAgICB0aGlzLiRjcmVhdGUoXG4gICAgICAgICAgICAnZHJvcCcsXG4gICAgICAgICAgICB0aGlzLmRyb3Bkb3ducy5maWx0ZXIoZWwgPT4gIXRoaXMuZ2V0RHJvcGRvd24oZWwpKSxcbiAgICAgICAgICAgIGFzc2lnbih7fSwgdGhpcy4kcHJvcHMsIHtib3VuZGFyeTogdGhpcy5ib3VuZGFyeSwgcG9zOiB0aGlzLnBvcywgb2Zmc2V0OiB0aGlzLmRyb3BiYXIgfHwgdGhpcy5vZmZzZXR9KVxuICAgICAgICApO1xuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdtb3VzZW92ZXInLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wZG93bjtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoe2N1cnJlbnR9KSB7XG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcbiAgICAgICAgICAgICAgICBpZiAoYWN0aXZlICYmIGFjdGl2ZS50b2dnbGUgJiYgIXdpdGhpbihhY3RpdmUudG9nZ2xlLiRlbCwgY3VycmVudCkgJiYgIWFjdGl2ZS50cmFja2VyLm1vdmVzVG8oYWN0aXZlLiRlbCkpIHtcbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdtb3VzZWxlYXZlJyxcblxuICAgICAgICAgICAgZWwoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuZHJvcGJhcjtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmIChhY3RpdmUgJiYgIXRoaXMuZHJvcGRvd25zLnNvbWUoZWwgPT4gbWF0Y2hlcyhlbCwgJzpob3ZlcicpKSkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnYmVmb3Jlc2hvdycsXG5cbiAgICAgICAgICAgIGNhcHR1cmU6IHRydWUsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICghdGhpcy5kcm9wYmFyLnBhcmVudE5vZGUpIHtcbiAgICAgICAgICAgICAgICAgICAgYWZ0ZXIodGhpcy5kcm9wYmFyQW5jaG9yIHx8IHRoaXMuJGVsLCB0aGlzLmRyb3BiYXIpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdzaG93JyxcblxuICAgICAgICAgICAgY2FwdHVyZTogdHJ1ZSxcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmRyb3BiYXI7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKF8sIGRyb3ApIHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHskZWwsIGRpcn0gPSBkcm9wO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5jbHNEcm9wICYmIGFkZENsYXNzKCRlbCwgYCR7dGhpcy5jbHNEcm9wfS1kcm9wYmFyYCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoZGlyID09PSAnYm90dG9tJykge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRyYW5zaXRpb25UbygkZWwub2Zmc2V0SGVpZ2h0ICsgdG9GbG9hdChjc3MoJGVsLCAnbWFyZ2luVG9wJykpICsgdG9GbG9hdChjc3MoJGVsLCAnbWFyZ2luQm90dG9tJykpLCAkZWwpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnYmVmb3JlaGlkZScsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlLCB7JGVsfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmIChtYXRjaGVzKHRoaXMuZHJvcGJhciwgJzpob3ZlcicpICYmIGFjdGl2ZSAmJiBhY3RpdmUuJGVsID09PSAkZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnaGlkZScsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihfLCB7JGVsfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmICghYWN0aXZlIHx8IGFjdGl2ZSAmJiBhY3RpdmUuJGVsID09PSAkZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy50cmFuc2l0aW9uVG8oMCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIGdldEFjdGl2ZSgpIHtcbiAgICAgICAgICAgIGNvbnN0IFthY3RpdmVdID0gdGhpcy5kcm9wZG93bnMubWFwKHRoaXMuZ2V0RHJvcGRvd24pLmZpbHRlcihkcm9wID0+IGRyb3AgJiYgZHJvcC5pc0FjdGl2ZSgpKTtcbiAgICAgICAgICAgIHJldHVybiBhY3RpdmUgJiYgaW5jbHVkZXMoYWN0aXZlLm1vZGUsICdob3ZlcicpICYmIHdpdGhpbihhY3RpdmUudG9nZ2xlLiRlbCwgdGhpcy4kZWwpICYmIGFjdGl2ZTtcbiAgICAgICAgfSxcblxuICAgICAgICB0cmFuc2l0aW9uVG8obmV3SGVpZ2h0LCBlbCkge1xuXG4gICAgICAgICAgICBjb25zdCB7ZHJvcGJhcn0gPSB0aGlzO1xuICAgICAgICAgICAgY29uc3Qgb2xkSGVpZ2h0ID0gaXNWaXNpYmxlKGRyb3BiYXIpID8gaGVpZ2h0KGRyb3BiYXIpIDogMDtcblxuICAgICAgICAgICAgZWwgPSBvbGRIZWlnaHQgPCBuZXdIZWlnaHQgJiYgZWw7XG5cbiAgICAgICAgICAgIGNzcyhlbCwgJ2NsaXAnLCBgcmVjdCgwLCR7ZWwub2Zmc2V0V2lkdGh9cHgsJHtvbGRIZWlnaHR9cHgsMClgKTtcblxuICAgICAgICAgICAgaGVpZ2h0KGRyb3BiYXIsIG9sZEhlaWdodCk7XG5cbiAgICAgICAgICAgIFRyYW5zaXRpb24uY2FuY2VsKFtlbCwgZHJvcGJhcl0pO1xuICAgICAgICAgICAgcmV0dXJuIFByb21pc2UuYWxsKFtcbiAgICAgICAgICAgICAgICBUcmFuc2l0aW9uLnN0YXJ0KGRyb3BiYXIsIHtoZWlnaHQ6IG5ld0hlaWdodH0sIHRoaXMuZHVyYXRpb24pLFxuICAgICAgICAgICAgICAgIFRyYW5zaXRpb24uc3RhcnQoZWwsIHtjbGlwOiBgcmVjdCgwLCR7ZWwub2Zmc2V0V2lkdGh9cHgsJHtuZXdIZWlnaHR9cHgsMClgfSwgdGhpcy5kdXJhdGlvbilcbiAgICAgICAgICAgIF0pXG4gICAgICAgICAgICAgICAgLmNhdGNoKG5vb3ApXG4gICAgICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjc3MoZWwsIHtjbGlwOiAnJ30pO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiR1cGRhdGUoZHJvcGJhcik7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZ2V0RHJvcGRvd24oZWwpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLiRnZXRDb21wb25lbnQoZWwsICdkcm9wJykgfHwgdGhpcy4kZ2V0Q29tcG9uZW50KGVsLCAnZHJvcGRvd24nKTtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IE1vZGFsIGZyb20gJy4uL21peGluL21vZGFsJztcbmltcG9ydCB7JCwgYWRkQ2xhc3MsIGFwcGVuZCwgY3NzLCBlbmRzV2l0aCwgaGFzQ2xhc3MsIGhlaWdodCwgcmVtb3ZlQ2xhc3MsIHRyaWdnZXIsIHVud3JhcCwgd3JhcEFsbH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW01vZGFsXSxcblxuICAgIGFyZ3M6ICdtb2RlJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1vZGU6IFN0cmluZyxcbiAgICAgICAgZmxpcDogQm9vbGVhbixcbiAgICAgICAgb3ZlcmxheTogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIG1vZGU6ICdzbGlkZScsXG4gICAgICAgIGZsaXA6IGZhbHNlLFxuICAgICAgICBvdmVybGF5OiBmYWxzZSxcbiAgICAgICAgY2xzUGFnZTogJ3VrLW9mZmNhbnZhcy1wYWdlJyxcbiAgICAgICAgY2xzQ29udGFpbmVyOiAndWstb2ZmY2FudmFzLWNvbnRhaW5lcicsXG4gICAgICAgIHNlbFBhbmVsOiAnLnVrLW9mZmNhbnZhcy1iYXInLFxuICAgICAgICBjbHNGbGlwOiAndWstb2ZmY2FudmFzLWZsaXAnLFxuICAgICAgICBjbHNDb250YWluZXJBbmltYXRpb246ICd1ay1vZmZjYW52YXMtY29udGFpbmVyLWFuaW1hdGlvbicsXG4gICAgICAgIGNsc1NpZGViYXJBbmltYXRpb246ICd1ay1vZmZjYW52YXMtYmFyLWFuaW1hdGlvbicsXG4gICAgICAgIGNsc01vZGU6ICd1ay1vZmZjYW52YXMnLFxuICAgICAgICBjbHNPdmVybGF5OiAndWstb2ZmY2FudmFzLW92ZXJsYXknLFxuICAgICAgICBzZWxDbG9zZTogJy51ay1vZmZjYW52YXMtY2xvc2UnXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgY2xzRmxpcCh7ZmxpcCwgY2xzRmxpcH0pIHtcbiAgICAgICAgICAgIHJldHVybiBmbGlwID8gY2xzRmxpcCA6ICcnO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc092ZXJsYXkoe292ZXJsYXksIGNsc092ZXJsYXl9KSB7XG4gICAgICAgICAgICByZXR1cm4gb3ZlcmxheSA/IGNsc092ZXJsYXkgOiAnJztcbiAgICAgICAgfSxcblxuICAgICAgICBjbHNNb2RlKHttb2RlLCBjbHNNb2RlfSkge1xuICAgICAgICAgICAgcmV0dXJuIGAke2Nsc01vZGV9LSR7bW9kZX1gO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc1NpZGViYXJBbmltYXRpb24oe21vZGUsIGNsc1NpZGViYXJBbmltYXRpb259KSB7XG4gICAgICAgICAgICByZXR1cm4gbW9kZSA9PT0gJ25vbmUnIHx8IG1vZGUgPT09ICdyZXZlYWwnID8gJycgOiBjbHNTaWRlYmFyQW5pbWF0aW9uO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc0NvbnRhaW5lckFuaW1hdGlvbih7bW9kZSwgY2xzQ29udGFpbmVyQW5pbWF0aW9ufSkge1xuICAgICAgICAgICAgcmV0dXJuIG1vZGUgIT09ICdwdXNoJyAmJiBtb2RlICE9PSAncmV2ZWFsJyA/ICcnIDogY2xzQ29udGFpbmVyQW5pbWF0aW9uO1xuICAgICAgICB9LFxuXG4gICAgICAgIHRyYW5zaXRpb25FbGVtZW50KHttb2RlfSkge1xuICAgICAgICAgICAgcmV0dXJuIG1vZGUgPT09ICdyZXZlYWwnID8gdGhpcy5wYW5lbC5wYXJlbnROb2RlIDogdGhpcy5wYW5lbDtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuICdhW2hyZWZePVwiI1wiXSc7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKHtjdXJyZW50fSkge1xuICAgICAgICAgICAgICAgIGlmIChjdXJyZW50Lmhhc2ggJiYgJChjdXJyZW50Lmhhc2gsIGRvY3VtZW50LmJvZHkpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICd0b3VjaHN0YXJ0JyxcblxuICAgICAgICAgICAgcGFzc2l2ZTogdHJ1ZSxcblxuICAgICAgICAgICAgZWwoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMucGFuZWw7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKHt0YXJnZXRUb3VjaGVzfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHRhcmdldFRvdWNoZXMubGVuZ3RoID09PSAxKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY2xpZW50WSA9IHRhcmdldFRvdWNoZXNbMF0uY2xpZW50WTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICd0b3VjaG1vdmUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuICAgICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5vdmVybGF5O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgZS5jYW5jZWxhYmxlICYmIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICd0b3VjaG1vdmUnLFxuXG4gICAgICAgICAgICBwYXNzaXZlOiBmYWxzZSxcblxuICAgICAgICAgICAgZWwoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMucGFuZWw7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcblxuICAgICAgICAgICAgICAgIGlmIChlLnRhcmdldFRvdWNoZXMubGVuZ3RoICE9PSAxKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb25zdCBjbGllbnRZID0gZXZlbnQudGFyZ2V0VG91Y2hlc1swXS5jbGllbnRZIC0gdGhpcy5jbGllbnRZO1xuICAgICAgICAgICAgICAgIGNvbnN0IHtzY3JvbGxUb3AsIHNjcm9sbEhlaWdodCwgY2xpZW50SGVpZ2h0fSA9IHRoaXMucGFuZWw7XG5cbiAgICAgICAgICAgICAgICBpZiAoY2xpZW50SGVpZ2h0ID49IHNjcm9sbEhlaWdodFxuICAgICAgICAgICAgICAgICAgICB8fCBzY3JvbGxUb3AgPT09IDAgJiYgY2xpZW50WSA+IDBcbiAgICAgICAgICAgICAgICAgICAgfHwgc2Nyb2xsSGVpZ2h0IC0gc2Nyb2xsVG9wIDw9IGNsaWVudEhlaWdodCAmJiBjbGllbnRZIDwgMFxuICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICBlLmNhbmNlbGFibGUgJiYgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuICAgICAgICAgICAgbmFtZTogJ3Nob3cnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMubW9kZSA9PT0gJ3JldmVhbCcgJiYgIWhhc0NsYXNzKHRoaXMucGFuZWwucGFyZW50Tm9kZSwgdGhpcy5jbHNNb2RlKSkge1xuICAgICAgICAgICAgICAgICAgICB3cmFwQWxsKHRoaXMucGFuZWwsICc8ZGl2PicpO1xuICAgICAgICAgICAgICAgICAgICBhZGRDbGFzcyh0aGlzLnBhbmVsLnBhcmVudE5vZGUsIHRoaXMuY2xzTW9kZSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCwgJ292ZXJmbG93WScsIHRoaXMub3ZlcmxheSA/ICdoaWRkZW4nIDogJycpO1xuICAgICAgICAgICAgICAgIGFkZENsYXNzKGRvY3VtZW50LmJvZHksIHRoaXMuY2xzQ29udGFpbmVyLCB0aGlzLmNsc0ZsaXApO1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ2Rpc3BsYXknLCAnYmxvY2snKTtcbiAgICAgICAgICAgICAgICBhZGRDbGFzcyh0aGlzLiRlbCwgdGhpcy5jbHNPdmVybGF5KTtcbiAgICAgICAgICAgICAgICBhZGRDbGFzcyh0aGlzLnBhbmVsLCB0aGlzLmNsc1NpZGViYXJBbmltYXRpb24sIHRoaXMubW9kZSAhPT0gJ3JldmVhbCcgPyB0aGlzLmNsc01vZGUgOiAnJyk7XG5cbiAgICAgICAgICAgICAgICBoZWlnaHQoZG9jdW1lbnQuYm9keSk7IC8vIGZvcmNlIHJlZmxvd1xuICAgICAgICAgICAgICAgIGFkZENsYXNzKGRvY3VtZW50LmJvZHksIHRoaXMuY2xzQ29udGFpbmVyQW5pbWF0aW9uKTtcblxuICAgICAgICAgICAgICAgIHRoaXMuY2xzQ29udGFpbmVyQW5pbWF0aW9uICYmIHN1cHByZXNzVXNlclNjYWxlKCk7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnaGlkZScsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3MoZG9jdW1lbnQuYm9keSwgdGhpcy5jbHNDb250YWluZXJBbmltYXRpb24pO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5tb2RlID09PSAnbm9uZScgfHwgYWN0aXZlICYmIGFjdGl2ZSAhPT0gdGhpcyAmJiBhY3RpdmUgIT09IHRoaXMucHJldikge1xuICAgICAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMucGFuZWwsICd0cmFuc2l0aW9uZW5kJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdoaWRkZW4nLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5jbHNDb250YWluZXJBbmltYXRpb24gJiYgcmVzdW1lVXNlclNjYWxlKCk7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5tb2RlID09PSAncmV2ZWFsJykge1xuICAgICAgICAgICAgICAgICAgICB1bndyYXAodGhpcy5wYW5lbCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy5wYW5lbCwgdGhpcy5jbHNTaWRlYmFyQW5pbWF0aW9uLCB0aGlzLmNsc01vZGUpO1xuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuJGVsLCB0aGlzLmNsc092ZXJsYXkpO1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ2Rpc3BsYXknLCAnJyk7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3MoZG9jdW1lbnQuYm9keSwgdGhpcy5jbHNDb250YWluZXIsIHRoaXMuY2xzRmxpcCk7XG5cbiAgICAgICAgICAgICAgICBjc3MoZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LCAnb3ZlcmZsb3dZJywgJycpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuICAgICAgICAgICAgbmFtZTogJ3N3aXBlTGVmdCBzd2lwZVJpZ2h0JyxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc1RvZ2dsZWQoKSAmJiBlbmRzV2l0aChlLnR5cGUsICdMZWZ0JykgXiB0aGlzLmZsaXApIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgIF1cblxufTtcblxuLy8gQ2hyb21lIGluIHJlc3BvbnNpdmUgbW9kZSB6b29tcyBwYWdlIHVwb24gb3BlbmluZyBvZmZjYW52YXNcbmZ1bmN0aW9uIHN1cHByZXNzVXNlclNjYWxlKCkge1xuICAgIGdldFZpZXdwb3J0KCkuY29udGVudCArPSAnLHVzZXItc2NhbGFibGU9MCc7XG59XG5cbmZ1bmN0aW9uIHJlc3VtZVVzZXJTY2FsZSgpIHtcbiAgICBjb25zdCB2aWV3cG9ydCA9IGdldFZpZXdwb3J0KCk7XG4gICAgdmlld3BvcnQuY29udGVudCA9IHZpZXdwb3J0LmNvbnRlbnQucmVwbGFjZSgvLHVzZXItc2NhbGFibGU9MCQvLCAnJyk7XG59XG5cbmZ1bmN0aW9uIGdldFZpZXdwb3J0KCkge1xuICAgIHJldHVybiAkKCdtZXRhW25hbWU9XCJ2aWV3cG9ydFwiXScsIGRvY3VtZW50LmhlYWQpIHx8IGFwcGVuZChkb2N1bWVudC5oZWFkLCAnPG1ldGEgbmFtZT1cInZpZXdwb3J0XCI+Jyk7XG59XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IHtjbG9zZXN0LCBjc3MsIGhlaWdodCwgb2Zmc2V0LCB0b0Zsb2F0LCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3NdLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgc2VsQ29udGFpbmVyOiBTdHJpbmcsXG4gICAgICAgIHNlbENvbnRlbnQ6IFN0cmluZ1xuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIHNlbENvbnRhaW5lcjogJy51ay1tb2RhbCcsXG4gICAgICAgIHNlbENvbnRlbnQ6ICcudWstbW9kYWwtZGlhbG9nJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGNvbnRhaW5lcih7c2VsQ29udGFpbmVyfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gY2xvc2VzdCgkZWwsIHNlbENvbnRhaW5lcik7XG4gICAgICAgIH0sXG5cbiAgICAgICAgY29udGVudCh7c2VsQ29udGVudH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIGNsb3Nlc3QoJGVsLCBzZWxDb250ZW50KTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgY3NzKHRoaXMuJGVsLCAnbWluSGVpZ2h0JywgMTUwKTtcbiAgICB9LFxuXG4gICAgdXBkYXRlOiB7XG5cbiAgICAgICAgcmVhZCgpIHtcblxuICAgICAgICAgICAgaWYgKCF0aGlzLmNvbnRlbnQgfHwgIXRoaXMuY29udGFpbmVyKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgIGN1cnJlbnQ6IHRvRmxvYXQoY3NzKHRoaXMuJGVsLCAnbWF4SGVpZ2h0JykpLFxuICAgICAgICAgICAgICAgIG1heDogTWF0aC5tYXgoMTUwLCBoZWlnaHQodGhpcy5jb250YWluZXIpIC0gKG9mZnNldCh0aGlzLmNvbnRlbnQpLmhlaWdodCAtIGhlaWdodCh0aGlzLiRlbCkpKVxuICAgICAgICAgICAgfTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7Y3VycmVudCwgbWF4fSkge1xuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnbWF4SGVpZ2h0JywgbWF4KTtcbiAgICAgICAgICAgIGlmIChNYXRoLnJvdW5kKGN1cnJlbnQpICE9PSBNYXRoLnJvdW5kKG1heCkpIHtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAncmVzaXplJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQge2FkZENsYXNzLCBEaW1lbnNpb25zLCBoZWlnaHQsIGlzVmlzaWJsZSwgd2lkdGh9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBwcm9wczogWyd3aWR0aCcsICdoZWlnaHQnXSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsICd1ay1yZXNwb25zaXZlLXdpZHRoJyk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG4gICAgICAgICAgICByZXR1cm4gaXNWaXNpYmxlKHRoaXMuJGVsKSAmJiB0aGlzLndpZHRoICYmIHRoaXMuaGVpZ2h0XG4gICAgICAgICAgICAgICAgPyB7d2lkdGg6IHdpZHRoKHRoaXMuJGVsLnBhcmVudE5vZGUpLCBoZWlnaHQ6IHRoaXMuaGVpZ2h0fVxuICAgICAgICAgICAgICAgIDogZmFsc2U7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoZGltKSB7XG4gICAgICAgICAgICBoZWlnaHQodGhpcy4kZWwsIERpbWVuc2lvbnMuY29udGFpbih7XG4gICAgICAgICAgICAgICAgaGVpZ2h0OiB0aGlzLmhlaWdodCxcbiAgICAgICAgICAgICAgICB3aWR0aDogdGhpcy53aWR0aFxuICAgICAgICAgICAgfSwgZGltKS5oZWlnaHQpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHskLCBjbGFtcCwgZXNjYXBlLCBoZWlnaHQsIG9mZnNldCwgc2Nyb2xsVG9wLCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgZHVyYXRpb246IE51bWJlcixcbiAgICAgICAgb2Zmc2V0OiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBkdXJhdGlvbjogMTAwMCxcbiAgICAgICAgb2Zmc2V0OiAwXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBzY3JvbGxUbyhlbCkge1xuXG4gICAgICAgICAgICBlbCA9IGVsICYmICQoZWwpIHx8IGRvY3VtZW50LmJvZHk7XG5cbiAgICAgICAgICAgIGNvbnN0IGRvY0hlaWdodCA9IGhlaWdodChkb2N1bWVudCk7XG4gICAgICAgICAgICBjb25zdCB3aW5IZWlnaHQgPSBoZWlnaHQod2luZG93KTtcblxuICAgICAgICAgICAgbGV0IHRhcmdldCA9IG9mZnNldChlbCkudG9wIC0gdGhpcy5vZmZzZXQ7XG4gICAgICAgICAgICBpZiAodGFyZ2V0ICsgd2luSGVpZ2h0ID4gZG9jSGVpZ2h0KSB7XG4gICAgICAgICAgICAgICAgdGFyZ2V0ID0gZG9jSGVpZ2h0IC0gd2luSGVpZ2h0O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoIXRyaWdnZXIodGhpcy4kZWwsICdiZWZvcmVzY3JvbGwnLCBbdGhpcywgZWxdKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3Qgc3RhcnQgPSBEYXRlLm5vdygpO1xuICAgICAgICAgICAgY29uc3Qgc3RhcnRZID0gd2luZG93LnBhZ2VZT2Zmc2V0O1xuICAgICAgICAgICAgY29uc3Qgc3RlcCA9ICgpID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGN1cnJlbnRZID0gc3RhcnRZICsgKHRhcmdldCAtIHN0YXJ0WSkgKiBlYXNlKGNsYW1wKChEYXRlLm5vdygpIC0gc3RhcnQpIC8gdGhpcy5kdXJhdGlvbikpO1xuXG4gICAgICAgICAgICAgICAgc2Nyb2xsVG9wKHdpbmRvdywgY3VycmVudFkpO1xuXG4gICAgICAgICAgICAgICAgLy8gc2Nyb2xsIG1vcmUgaWYgd2UgaGF2ZSBub3QgcmVhY2hlZCBvdXIgZGVzdGluYXRpb25cbiAgICAgICAgICAgICAgICBpZiAoY3VycmVudFkgIT09IHRhcmdldCkge1xuICAgICAgICAgICAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoc3RlcCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdHJpZ2dlcih0aGlzLiRlbCwgJ3Njcm9sbGVkJywgW3RoaXMsIGVsXSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBzdGVwKCk7XG5cbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGV2ZW50czoge1xuXG4gICAgICAgIGNsaWNrKGUpIHtcblxuICAgICAgICAgICAgaWYgKGUuZGVmYXVsdFByZXZlbnRlZCkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdGhpcy5zY3JvbGxUbyhlc2NhcGUoZGVjb2RlVVJJQ29tcG9uZW50KHRoaXMuJGVsLmhhc2gpKS5zdWJzdHIoMSkpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmZ1bmN0aW9uIGVhc2Uoaykge1xuICAgIHJldHVybiAwLjUgKiAoMSAtIE1hdGguY29zKE1hdGguUEkgKiBrKSk7XG59XG4iLCJpbXBvcnQgeyQkLCBhZGRDbGFzcywgY3NzLCBkYXRhLCBmaWx0ZXIsIGlzSW5WaWV3LCBQcm9taXNlLCByZW1vdmVDbGFzcywgdG9nZ2xlQ2xhc3MsIHRyaWdnZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBhcmdzOiAnY2xzJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGNsczogU3RyaW5nLFxuICAgICAgICB0YXJnZXQ6IFN0cmluZyxcbiAgICAgICAgaGlkZGVuOiBCb29sZWFuLFxuICAgICAgICBvZmZzZXRUb3A6IE51bWJlcixcbiAgICAgICAgb2Zmc2V0TGVmdDogTnVtYmVyLFxuICAgICAgICByZXBlYXQ6IEJvb2xlYW4sXG4gICAgICAgIGRlbGF5OiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YTogKCkgPT4gKHtcbiAgICAgICAgY2xzOiBmYWxzZSxcbiAgICAgICAgdGFyZ2V0OiBmYWxzZSxcbiAgICAgICAgaGlkZGVuOiB0cnVlLFxuICAgICAgICBvZmZzZXRUb3A6IDAsXG4gICAgICAgIG9mZnNldExlZnQ6IDAsXG4gICAgICAgIHJlcGVhdDogZmFsc2UsXG4gICAgICAgIGRlbGF5OiAwLFxuICAgICAgICBpblZpZXdDbGFzczogJ3VrLXNjcm9sbHNweS1pbnZpZXcnXG4gICAgfSksXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGVsZW1lbnRzKHt0YXJnZXR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiB0YXJnZXQgPyAkJCh0YXJnZXQsICRlbCkgOiBbJGVsXTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIHVwZGF0ZTogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgd3JpdGUoKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaGlkZGVuKSB7XG4gICAgICAgICAgICAgICAgICAgIGNzcyhmaWx0ZXIodGhpcy5lbGVtZW50cywgYDpub3QoLiR7dGhpcy5pblZpZXdDbGFzc30pYCksICd2aXNpYmlsaXR5JywgJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgcmVhZCh7dXBkYXRlfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKCF1cGRhdGUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudHMuZm9yRWFjaChlbCA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IHN0YXRlID0gZWwuX3VrU2Nyb2xsc3B5U3RhdGU7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFzdGF0ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhdGUgPSB7Y2xzOiBkYXRhKGVsLCAndWstc2Nyb2xsc3B5LWNsYXNzJykgfHwgdGhpcy5jbHN9O1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgc3RhdGUuc2hvdyA9IGlzSW5WaWV3KGVsLCB0aGlzLm9mZnNldFRvcCwgdGhpcy5vZmZzZXRMZWZ0KTtcbiAgICAgICAgICAgICAgICAgICAgZWwuX3VrU2Nyb2xsc3B5U3RhdGUgPSBzdGF0ZTtcblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICB3cml0ZShkYXRhKSB7XG5cbiAgICAgICAgICAgICAgICAvLyBMZXQgY2hpbGQgY29tcG9uZW50cyBiZSBhcHBsaWVkIGF0IGxlYXN0IG9uY2UgZmlyc3RcbiAgICAgICAgICAgICAgICBpZiAoIWRhdGEudXBkYXRlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuJGVtaXQoKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGRhdGEudXBkYXRlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVsZW1lbnRzLmZvckVhY2goZWwgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHN0YXRlID0gZWwuX3VrU2Nyb2xsc3B5U3RhdGU7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHtjbHN9ID0gc3RhdGU7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0YXRlLnNob3cgJiYgIXN0YXRlLmludmlldyAmJiAhc3RhdGUucXVldWVkKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHNob3cgPSAoKSA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjc3MoZWwsICd2aXNpYmlsaXR5JywgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFkZENsYXNzKGVsLCB0aGlzLmluVmlld0NsYXNzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyhlbCwgY2xzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIoZWwsICdpbnZpZXcnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJHVwZGF0ZShlbCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdGF0ZS5pbnZpZXcgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YXRlLmFib3J0ICYmIHN0YXRlLmFib3J0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5kZWxheSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3RhdGUucXVldWVkID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhLnByb21pc2UgPSAoZGF0YS5wcm9taXNlIHx8IFByb21pc2UucmVzb2x2ZSgpKS50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuICFzdGF0ZS5pbnZpZXcgJiYgbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHRpbWVyID0gc2V0VGltZW91dCgoKSA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LCBkYXRhLnByb21pc2UgfHwgdGhpcy5lbGVtZW50cy5sZW5ndGggPT09IDEgPyB0aGlzLmRlbGF5IDogMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YXRlLmFib3J0ID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lcik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0YXRlLnF1ZXVlZCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCFzdGF0ZS5zaG93ICYmIChzdGF0ZS5pbnZpZXcgfHwgc3RhdGUucXVldWVkKSAmJiB0aGlzLnJlcGVhdCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBzdGF0ZS5hYm9ydCAmJiBzdGF0ZS5hYm9ydCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXN0YXRlLmludmlldykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgY3NzKGVsLCAndmlzaWJpbGl0eScsIHRoaXMuaGlkZGVuID8gJ2hpZGRlbicgOiAnJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZW1vdmVDbGFzcyhlbCwgdGhpcy5pblZpZXdDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyhlbCwgY2xzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdHJpZ2dlcihlbCwgJ291dHZpZXcnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy4kdXBkYXRlKGVsKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc3RhdGUuaW52aWV3ID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Njcm9sbCcsICdyZXNpemUnXVxuXG4gICAgICAgIH1cblxuICAgIF1cblxufTtcblxuIiwiaW1wb3J0IHskLCAkJCwgYWRkQ2xhc3MsIGNsb3Nlc3QsIGVzY2FwZSwgZmlsdGVyLCBoZWlnaHQsIGlzSW5WaWV3LCBvZmZzZXQsIHJlbW92ZUNsYXNzLCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgY2xzOiBTdHJpbmcsXG4gICAgICAgIGNsb3Nlc3Q6IFN0cmluZyxcbiAgICAgICAgc2Nyb2xsOiBCb29sZWFuLFxuICAgICAgICBvdmVyZmxvdzogQm9vbGVhbixcbiAgICAgICAgb2Zmc2V0OiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBjbHM6ICd1ay1hY3RpdmUnLFxuICAgICAgICBjbG9zZXN0OiBmYWxzZSxcbiAgICAgICAgc2Nyb2xsOiBmYWxzZSxcbiAgICAgICAgb3ZlcmZsb3c6IHRydWUsXG4gICAgICAgIG9mZnNldDogMFxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGxpbmtzKF8sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuICQkKCdhW2hyZWZePVwiI1wiXScsICRlbCkuZmlsdGVyKGVsID0+IGVsLmhhc2gpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGVsZW1lbnRzKHtjbG9zZXN0OiBzZWxlY3Rvcn0pIHtcbiAgICAgICAgICAgIHJldHVybiBjbG9zZXN0KHRoaXMubGlua3MsIHNlbGVjdG9yIHx8ICcqJyk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdGFyZ2V0cygpIHtcbiAgICAgICAgICAgIHJldHVybiAkJCh0aGlzLmxpbmtzLm1hcChlbCA9PiBlc2NhcGUoZWwuaGFzaCkuc3Vic3RyKDEpKS5qb2luKCcsJykpO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLnNjcm9sbCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRjcmVhdGUoJ3Njcm9sbCcsIHRoaXMubGlua3MsIHtvZmZzZXQ6IHRoaXMub2Zmc2V0IHx8IDB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIHJlYWQoZGF0YSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qgc2Nyb2xsID0gd2luZG93LnBhZ2VZT2Zmc2V0ICsgdGhpcy5vZmZzZXQgKyAxO1xuICAgICAgICAgICAgICAgIGNvbnN0IG1heCA9IGhlaWdodChkb2N1bWVudCkgLSBoZWlnaHQod2luZG93KSArIHRoaXMub2Zmc2V0O1xuXG4gICAgICAgICAgICAgICAgZGF0YS5hY3RpdmUgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgIHRoaXMudGFyZ2V0cy5ldmVyeSgoZWwsIGkpID0+IHtcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCB7dG9wfSA9IG9mZnNldChlbCk7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGxhc3QgPSBpICsgMSA9PT0gdGhpcy50YXJnZXRzLmxlbmd0aDtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIXRoaXMub3ZlcmZsb3cgJiYgKGkgPT09IDAgJiYgdG9wID4gc2Nyb2xsIHx8IGxhc3QgJiYgdG9wICsgZWwub2Zmc2V0VG9wIDwgc2Nyb2xsKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFsYXN0ICYmIG9mZnNldCh0aGlzLnRhcmdldHNbaSArIDFdKS50b3AgPD0gc2Nyb2xsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmIChzY3JvbGwgPj0gbWF4KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKGxldCBqID0gdGhpcy50YXJnZXRzLmxlbmd0aCAtIDE7IGogPiBpOyBqLS0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoaXNJblZpZXcodGhpcy50YXJnZXRzW2pdKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbCA9IHRoaXMudGFyZ2V0c1tqXTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICEoZGF0YS5hY3RpdmUgPSAkKGZpbHRlcih0aGlzLmxpbmtzLCBgW2hyZWY9XCIjJHtlbC5pZH1cIl1gKSkpO1xuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIHdyaXRlKHthY3RpdmV9KSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmxpbmtzLmZvckVhY2goZWwgPT4gZWwuYmx1cigpKTtcbiAgICAgICAgICAgICAgICByZW1vdmVDbGFzcyh0aGlzLmVsZW1lbnRzLCB0aGlzLmNscyk7XG5cbiAgICAgICAgICAgICAgICBpZiAoYWN0aXZlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGhpcy4kZWwsICdhY3RpdmUnLCBbYWN0aXZlLCBhZGRDbGFzcyh0aGlzLmNsb3Nlc3QgPyBjbG9zZXN0KGFjdGl2ZSwgdGhpcy5jbG9zZXN0KSA6IGFjdGl2ZSwgdGhpcy5jbHMpXSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsnc2Nyb2xsJywgJ3Jlc2l6ZSddXG5cbiAgICAgICAgfVxuXG4gICAgXVxuXG59O1xuIiwiaW1wb3J0IENsYXNzIGZyb20gJy4uL21peGluL2NsYXNzJztcbmltcG9ydCBNZWRpYSBmcm9tICcuLi9taXhpbi9tZWRpYSc7XG5pbXBvcnQgeyQsIGFkZENsYXNzLCBhZnRlciwgQW5pbWF0aW9uLCBhc3NpZ24sIGF0dHIsIGNzcywgZmFzdGRvbSwgaGFzQ2xhc3MsIGhlaWdodCwgaXNOdW1lcmljLCBpc1N0cmluZywgaXNWaXNpYmxlLCBub29wLCBvZmZzZXQsIG9mZnNldFBvc2l0aW9uLCBxdWVyeSwgcmVtb3ZlLCByZW1vdmVDbGFzcywgcmVwbGFjZUNsYXNzLCBzY3JvbGxUb3AsIHRvRmxvYXQsIHRvZ2dsZUNsYXNzLCB0cmlnZ2VyLCB3aXRoaW59IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgTWVkaWFdLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgdG9wOiBudWxsLFxuICAgICAgICBib3R0b206IEJvb2xlYW4sXG4gICAgICAgIG9mZnNldDogTnVtYmVyLFxuICAgICAgICBhbmltYXRpb246IFN0cmluZyxcbiAgICAgICAgY2xzQWN0aXZlOiBTdHJpbmcsXG4gICAgICAgIGNsc0luYWN0aXZlOiBTdHJpbmcsXG4gICAgICAgIGNsc0ZpeGVkOiBTdHJpbmcsXG4gICAgICAgIGNsc0JlbG93OiBTdHJpbmcsXG4gICAgICAgIHNlbFRhcmdldDogU3RyaW5nLFxuICAgICAgICB3aWR0aEVsZW1lbnQ6IEJvb2xlYW4sXG4gICAgICAgIHNob3dPblVwOiBCb29sZWFuLFxuICAgICAgICB0YXJnZXRPZmZzZXQ6IE51bWJlclxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIHRvcDogMCxcbiAgICAgICAgYm90dG9tOiBmYWxzZSxcbiAgICAgICAgb2Zmc2V0OiAwLFxuICAgICAgICBhbmltYXRpb246ICcnLFxuICAgICAgICBjbHNBY3RpdmU6ICd1ay1hY3RpdmUnLFxuICAgICAgICBjbHNJbmFjdGl2ZTogJycsXG4gICAgICAgIGNsc0ZpeGVkOiAndWstc3RpY2t5LWZpeGVkJyxcbiAgICAgICAgY2xzQmVsb3c6ICd1ay1zdGlja3ktYmVsb3cnLFxuICAgICAgICBzZWxUYXJnZXQ6ICcnLFxuICAgICAgICB3aWR0aEVsZW1lbnQ6IGZhbHNlLFxuICAgICAgICBzaG93T25VcDogZmFsc2UsXG4gICAgICAgIHRhcmdldE9mZnNldDogZmFsc2VcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBzZWxUYXJnZXQoe3NlbFRhcmdldH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHNlbFRhcmdldCAmJiAkKHNlbFRhcmdldCwgJGVsKSB8fCAkZWw7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd2lkdGhFbGVtZW50KHt3aWR0aEVsZW1lbnR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiBxdWVyeSh3aWR0aEVsZW1lbnQsICRlbCkgfHwgdGhpcy5wbGFjZWhvbGRlcjtcbiAgICAgICAgfSxcblxuICAgICAgICBpc0FjdGl2ZToge1xuXG4gICAgICAgICAgICBnZXQoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGhhc0NsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0FjdGl2ZSk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBzZXQodmFsdWUpIHtcbiAgICAgICAgICAgICAgICBpZiAodmFsdWUgJiYgIXRoaXMuaXNBY3RpdmUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVwbGFjZUNsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0luYWN0aXZlLCB0aGlzLmNsc0FjdGl2ZSk7XG4gICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGhpcy4kZWwsICdhY3RpdmUnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCF2YWx1ZSAmJiAhaGFzQ2xhc3ModGhpcy5zZWxUYXJnZXQsIHRoaXMuY2xzSW5hY3RpdmUpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlcGxhY2VDbGFzcyh0aGlzLnNlbFRhcmdldCwgdGhpcy5jbHNBY3RpdmUsIHRoaXMuY2xzSW5hY3RpdmUpO1xuICAgICAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAnaW5hY3RpdmUnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgdGhpcy5wbGFjZWhvbGRlciA9ICQoJysgLnVrLXN0aWNreS1wbGFjZWhvbGRlcicsIHRoaXMuJGVsKSB8fCAkKCc8ZGl2IGNsYXNzPVwidWstc3RpY2t5LXBsYWNlaG9sZGVyXCI+PC9kaXY+Jyk7XG4gICAgICAgIHRoaXMuaXNGaXhlZCA9IGZhbHNlO1xuICAgICAgICB0aGlzLmlzQWN0aXZlID0gZmFsc2U7XG4gICAgfSxcblxuICAgIGRpc2Nvbm5lY3RlZCgpIHtcblxuICAgICAgICBpZiAodGhpcy5pc0ZpeGVkKSB7XG4gICAgICAgICAgICB0aGlzLmhpZGUoKTtcbiAgICAgICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0luYWN0aXZlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJlbW92ZSh0aGlzLnBsYWNlaG9sZGVyKTtcbiAgICAgICAgdGhpcy5wbGFjZWhvbGRlciA9IG51bGw7XG4gICAgICAgIHRoaXMud2lkdGhFbGVtZW50ID0gbnVsbDtcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnbG9hZCBoYXNoY2hhbmdlIHBvcHN0YXRlJyxcblxuICAgICAgICAgICAgZWw6IHdpbmRvdyxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICghKHRoaXMudGFyZ2V0T2Zmc2V0ICE9PSBmYWxzZSAmJiBsb2NhdGlvbi5oYXNoICYmIHdpbmRvdy5wYWdlWU9mZnNldCA+IDApKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb25zdCB0YXJnZXQgPSAkKGxvY2F0aW9uLmhhc2gpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRhcmdldCkge1xuICAgICAgICAgICAgICAgICAgICBmYXN0ZG9tLnJlYWQoKCkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCB7dG9wfSA9IG9mZnNldCh0YXJnZXQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgZWxUb3AgPSBvZmZzZXQodGhpcy4kZWwpLnRvcDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsSGVpZ2h0ID0gdGhpcy4kZWwub2Zmc2V0SGVpZ2h0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5pc0ZpeGVkICYmIGVsVG9wICsgZWxIZWlnaHQgPj0gdG9wICYmIGVsVG9wIDw9IHRvcCArIHRhcmdldC5vZmZzZXRIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JvbGxUb3Aod2luZG93LCB0b3AgLSBlbEhlaWdodCAtIChpc051bWVyaWModGhpcy50YXJnZXRPZmZzZXQpID8gdGhpcy50YXJnZXRPZmZzZXQgOiAwKSAtIHRoaXMub2Zmc2V0KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKHtoZWlnaHR9LCB0eXBlKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc0FjdGl2ZSAmJiB0eXBlICE9PSAndXBkYXRlJykge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICBoZWlnaHQgPSB0aGlzLiRlbC5vZmZzZXRIZWlnaHQ7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvdygpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaGVpZ2h0ID0gIXRoaXMuaXNBY3RpdmUgPyB0aGlzLiRlbC5vZmZzZXRIZWlnaHQgOiBoZWlnaHQ7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnRvcE9mZnNldCA9IG9mZnNldCh0aGlzLmlzRml4ZWQgPyB0aGlzLnBsYWNlaG9sZGVyIDogdGhpcy4kZWwpLnRvcDtcbiAgICAgICAgICAgICAgICB0aGlzLmJvdHRvbU9mZnNldCA9IHRoaXMudG9wT2Zmc2V0ICsgaGVpZ2h0O1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYm90dG9tID0gcGFyc2VQcm9wKCdib3R0b20nLCB0aGlzKTtcblxuICAgICAgICAgICAgICAgIHRoaXMudG9wID0gTWF0aC5tYXgodG9GbG9hdChwYXJzZVByb3AoJ3RvcCcsIHRoaXMpKSwgdGhpcy50b3BPZmZzZXQpIC0gdGhpcy5vZmZzZXQ7XG4gICAgICAgICAgICAgICAgdGhpcy5ib3R0b20gPSBib3R0b20gJiYgYm90dG9tIC0gaGVpZ2h0O1xuICAgICAgICAgICAgICAgIHRoaXMuaW5hY3RpdmUgPSAhdGhpcy5tYXRjaE1lZGlhO1xuXG4gICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgbGFzdFNjcm9sbDogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodCxcbiAgICAgICAgICAgICAgICAgICAgbWFyZ2luczogY3NzKHRoaXMuJGVsLCBbJ21hcmdpblRvcCcsICdtYXJnaW5Cb3R0b20nLCAnbWFyZ2luTGVmdCcsICdtYXJnaW5SaWdodCddKVxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICB3cml0ZSh7aGVpZ2h0LCBtYXJnaW5zfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge3BsYWNlaG9sZGVyfSA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICBjc3MocGxhY2Vob2xkZXIsIGFzc2lnbih7aGVpZ2h0fSwgbWFyZ2lucykpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCF3aXRoaW4ocGxhY2Vob2xkZXIsIGRvY3VtZW50KSkge1xuICAgICAgICAgICAgICAgICAgICBhZnRlcih0aGlzLiRlbCwgcGxhY2Vob2xkZXIpO1xuICAgICAgICAgICAgICAgICAgICBhdHRyKHBsYWNlaG9sZGVyLCAnaGlkZGVuJywgJycpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIGVuc3VyZSBhY3RpdmUvaW5hY3RpdmUgY2xhc3NlcyBhcmUgYXBwbGllZFxuICAgICAgICAgICAgICAgIHRoaXMuaXNBY3RpdmUgPSB0aGlzLmlzQWN0aXZlO1xuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgcmVhZCh7c2Nyb2xsID0gMH0pIHtcblxuICAgICAgICAgICAgICAgIHRoaXMud2lkdGggPSAoaXNWaXNpYmxlKHRoaXMud2lkdGhFbGVtZW50KSA/IHRoaXMud2lkdGhFbGVtZW50IDogdGhpcy4kZWwpLm9mZnNldFdpZHRoO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5zY3JvbGwgPSB3aW5kb3cucGFnZVlPZmZzZXQ7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBkaXI6IHNjcm9sbCA8PSB0aGlzLnNjcm9sbCA/ICdkb3duJyA6ICd1cCcsXG4gICAgICAgICAgICAgICAgICAgIHNjcm9sbDogdGhpcy5zY3JvbGwsXG4gICAgICAgICAgICAgICAgICAgIHZpc2libGU6IGlzVmlzaWJsZSh0aGlzLiRlbCksXG4gICAgICAgICAgICAgICAgICAgIHRvcDogb2Zmc2V0UG9zaXRpb24odGhpcy5wbGFjZWhvbGRlcilbMF1cbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgd3JpdGUoZGF0YSwgdHlwZSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge2luaXRUaW1lc3RhbXAgPSAwLCBkaXIsIGxhc3REaXIsIGxhc3RTY3JvbGwsIHNjcm9sbCwgdG9wLCB2aXNpYmxlfSA9IGRhdGE7XG4gICAgICAgICAgICAgICAgY29uc3Qgbm93ID0gcGVyZm9ybWFuY2Uubm93KCk7XG5cbiAgICAgICAgICAgICAgICBkYXRhLmxhc3RTY3JvbGwgPSBzY3JvbGw7XG5cbiAgICAgICAgICAgICAgICBpZiAoc2Nyb2xsIDwgMCB8fCBzY3JvbGwgPT09IGxhc3RTY3JvbGwgfHwgIXZpc2libGUgfHwgdGhpcy5kaXNhYmxlZCB8fCB0aGlzLnNob3dPblVwICYmIHR5cGUgIT09ICdzY3JvbGwnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAobm93IC0gaW5pdFRpbWVzdGFtcCA+IDMwMCB8fCBkaXIgIT09IGxhc3REaXIpIHtcbiAgICAgICAgICAgICAgICAgICAgZGF0YS5pbml0U2Nyb2xsID0gc2Nyb2xsO1xuICAgICAgICAgICAgICAgICAgICBkYXRhLmluaXRUaW1lc3RhbXAgPSBub3c7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZGF0YS5sYXN0RGlyID0gZGlyO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuc2hvd09uVXAgJiYgTWF0aC5hYnMoZGF0YS5pbml0U2Nyb2xsIC0gc2Nyb2xsKSA8PSAzMCAmJiBNYXRoLmFicyhsYXN0U2Nyb2xsIC0gc2Nyb2xsKSA8PSAxMCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaW5hY3RpdmVcbiAgICAgICAgICAgICAgICAgICAgfHwgc2Nyb2xsIDwgdGhpcy50b3BcbiAgICAgICAgICAgICAgICAgICAgfHwgdGhpcy5zaG93T25VcCAmJiAoc2Nyb2xsIDw9IHRoaXMudG9wIHx8IGRpciA9PT0gJ2Rvd24nIHx8IGRpciA9PT0gJ3VwJyAmJiAhdGhpcy5pc0ZpeGVkICYmIHNjcm9sbCA8PSB0aGlzLmJvdHRvbU9mZnNldClcbiAgICAgICAgICAgICAgICApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIXRoaXMuaXNGaXhlZCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoQW5pbWF0aW9uLmluUHJvZ3Jlc3ModGhpcy4kZWwpICYmIHRvcCA+IHNjcm9sbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5jYW5jZWwodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB0aGlzLmlzRml4ZWQgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5hbmltYXRpb24gJiYgc2Nyb2xsID4gdGhpcy50b3BPZmZzZXQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5jYW5jZWwodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgQW5pbWF0aW9uLm91dCh0aGlzLiRlbCwgdGhpcy5hbmltYXRpb24pLnRoZW4oKCkgPT4gdGhpcy5oaWRlKCksIG5vb3ApO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc0ZpeGVkKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cGRhdGUoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5hbmltYXRpb24pIHtcblxuICAgICAgICAgICAgICAgICAgICBBbmltYXRpb24uY2FuY2VsKHRoaXMuJGVsKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5pbih0aGlzLiRlbCwgdGhpcy5hbmltYXRpb24pLmNhdGNoKG5vb3ApO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJywgJ3Njcm9sbCddXG5cbiAgICAgICAgfVxuXG4gICAgXSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBzaG93KCkge1xuXG4gICAgICAgICAgICB0aGlzLmlzRml4ZWQgPSB0cnVlO1xuICAgICAgICAgICAgdGhpcy51cGRhdGUoKTtcbiAgICAgICAgICAgIGF0dHIodGhpcy5wbGFjZWhvbGRlciwgJ2hpZGRlbicsIG51bGwpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgaGlkZSgpIHtcblxuICAgICAgICAgICAgdGhpcy5pc0FjdGl2ZSA9IGZhbHNlO1xuICAgICAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzRml4ZWQsIHRoaXMuY2xzQmVsb3cpO1xuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCB7cG9zaXRpb246ICcnLCB0b3A6ICcnLCB3aWR0aDogJyd9KTtcbiAgICAgICAgICAgIGF0dHIodGhpcy5wbGFjZWhvbGRlciwgJ2hpZGRlbicsICcnKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIHVwZGF0ZSgpIHtcblxuICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy50b3AgIT09IDAgfHwgdGhpcy5zY3JvbGwgPiB0aGlzLnRvcDtcbiAgICAgICAgICAgIGxldCB0b3AgPSBNYXRoLm1heCgwLCB0aGlzLm9mZnNldCk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLmJvdHRvbSAmJiB0aGlzLnNjcm9sbCA+IHRoaXMuYm90dG9tIC0gdGhpcy5vZmZzZXQpIHtcbiAgICAgICAgICAgICAgICB0b3AgPSB0aGlzLmJvdHRvbSAtIHRoaXMuc2Nyb2xsO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjc3ModGhpcy4kZWwsIHtcbiAgICAgICAgICAgICAgICBwb3NpdGlvbjogJ2ZpeGVkJyxcbiAgICAgICAgICAgICAgICB0b3A6IGAke3RvcH1weGAsXG4gICAgICAgICAgICAgICAgd2lkdGg6IHRoaXMud2lkdGhcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLmlzQWN0aXZlID0gYWN0aXZlO1xuICAgICAgICAgICAgdG9nZ2xlQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzQmVsb3csIHRoaXMuc2Nyb2xsID4gdGhpcy5ib3R0b21PZmZzZXQpO1xuICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzRml4ZWQpO1xuXG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZnVuY3Rpb24gcGFyc2VQcm9wKHByb3AsIHskcHJvcHMsICRlbCwgW2Ake3Byb3B9T2Zmc2V0YF06IHByb3BPZmZzZXR9KSB7XG5cbiAgICBjb25zdCB2YWx1ZSA9ICRwcm9wc1twcm9wXTtcblxuICAgIGlmICghdmFsdWUpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmIChpc051bWVyaWModmFsdWUpKSB7XG5cbiAgICAgICAgcmV0dXJuIHByb3BPZmZzZXQgKyB0b0Zsb2F0KHZhbHVlKTtcblxuICAgIH0gZWxzZSBpZiAoaXNTdHJpbmcodmFsdWUpICYmIHZhbHVlLm1hdGNoKC9eLT9cXGQrdmgkLykpIHtcblxuICAgICAgICByZXR1cm4gaGVpZ2h0KHdpbmRvdykgKiB0b0Zsb2F0KHZhbHVlKSAvIDEwMDtcblxuICAgIH0gZWxzZSB7XG5cbiAgICAgICAgY29uc3QgZWwgPSB2YWx1ZSA9PT0gdHJ1ZSA/ICRlbC5wYXJlbnROb2RlIDogcXVlcnkodmFsdWUsICRlbCk7XG5cbiAgICAgICAgaWYgKGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gb2Zmc2V0KGVsKS50b3AgKyBlbC5vZmZzZXRIZWlnaHQ7XG4gICAgICAgIH1cblxuICAgIH1cbn1cbiIsImltcG9ydCBUb2dnbGFibGUgZnJvbSAnLi4vbWl4aW4vdG9nZ2xhYmxlJztcbmltcG9ydCB7JCQsIGFkZENsYXNzLCBhdHRyLCBkYXRhLCBlbmRzV2l0aCwgZmlsdGVyLCBnZXRJbmRleCwgaGFzQ2xhc3MsIGluZGV4LCBpc0VtcHR5LCBtYXRjaGVzLCBxdWVyeUFsbCwgcmVtb3ZlQ2xhc3MsIHRvTm9kZXMsIHdpdGhpbn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW1RvZ2dsYWJsZV0sXG5cbiAgICBhcmdzOiAnY29ubmVjdCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBjb25uZWN0OiBTdHJpbmcsXG4gICAgICAgIHRvZ2dsZTogU3RyaW5nLFxuICAgICAgICBhY3RpdmU6IE51bWJlcixcbiAgICAgICAgc3dpcGluZzogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGNvbm5lY3Q6ICd+LnVrLXN3aXRjaGVyJyxcbiAgICAgICAgdG9nZ2xlOiAnPiAqID4gOmZpcnN0LWNoaWxkJyxcbiAgICAgICAgYWN0aXZlOiAwLFxuICAgICAgICBzd2lwaW5nOiB0cnVlLFxuICAgICAgICBjbHM6ICd1ay1hY3RpdmUnLFxuICAgICAgICBjbHNDb250YWluZXI6ICd1ay1zd2l0Y2hlcicsXG4gICAgICAgIGF0dHJJdGVtOiAndWstc3dpdGNoZXItaXRlbScsXG4gICAgICAgIHF1ZXVlZDogdHJ1ZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGNvbm5lY3RzKHtjb25uZWN0fSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gcXVlcnlBbGwoY29ubmVjdCwgJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICB0b2dnbGVzKHt0b2dnbGV9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiAkJCh0b2dnbGUsICRlbCk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBldmVudHM6IFtcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdjbGljaycsXG5cbiAgICAgICAgICAgIGRlbGVnYXRlKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBgJHt0aGlzLnRvZ2dsZX06bm90KC51ay1kaXNhYmxlZClgO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvdyh0b05vZGVzKHRoaXMuJGVsLmNoaWxkcmVuKS5maWx0ZXIoZWwgPT4gd2l0aGluKGUuY3VycmVudCwgZWwpKVswXSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBlbCgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5jb25uZWN0cztcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGRlbGVnYXRlKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBgWyR7dGhpcy5hdHRySXRlbX1dLFtkYXRhLSR7dGhpcy5hdHRySXRlbX1dYDtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3coZGF0YShlLmN1cnJlbnQsIHRoaXMuYXR0ckl0ZW0pKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnc3dpcGVSaWdodCBzd2lwZUxlZnQnLFxuXG4gICAgICAgICAgICBmaWx0ZXIoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuc3dpcGluZztcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGVsKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmNvbm5lY3RzO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcih7dHlwZX0pIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3coZW5kc1dpdGgodHlwZSwgJ0xlZnQnKSA/ICduZXh0JyA6ICdwcmV2aW91cycpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgdXBkYXRlKCkge1xuXG4gICAgICAgIHRoaXMuY29ubmVjdHMuZm9yRWFjaChsaXN0ID0+IHRoaXMudXBkYXRlQXJpYShsaXN0LmNoaWxkcmVuKSk7XG4gICAgICAgIGNvbnN0IHtjaGlsZHJlbn0gPSB0aGlzLiRlbDtcbiAgICAgICAgdGhpcy5zaG93KGZpbHRlcihjaGlsZHJlbiwgYC4ke3RoaXMuY2xzfWApWzBdIHx8IGNoaWxkcmVuW3RoaXMuYWN0aXZlXSB8fCBjaGlsZHJlblswXSk7XG5cbiAgICB9LFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIGluZGV4KCkge1xuICAgICAgICAgICAgcmV0dXJuICFpc0VtcHR5KHRoaXMuY29ubmVjdHMpICYmIGluZGV4KGZpbHRlcih0aGlzLmNvbm5lY3RzWzBdLmNoaWxkcmVuLCBgLiR7dGhpcy5jbHN9YClbMF0pO1xuICAgICAgICB9LFxuXG4gICAgICAgIHNob3coaXRlbSkge1xuXG4gICAgICAgICAgICBjb25zdCB7Y2hpbGRyZW59ID0gdGhpcy4kZWw7XG4gICAgICAgICAgICBjb25zdCB7bGVuZ3RofSA9IGNoaWxkcmVuO1xuICAgICAgICAgICAgY29uc3QgcHJldiA9IHRoaXMuaW5kZXgoKTtcbiAgICAgICAgICAgIGNvbnN0IGhhc1ByZXYgPSBwcmV2ID49IDA7XG4gICAgICAgICAgICBjb25zdCBkaXIgPSBpdGVtID09PSAncHJldmlvdXMnID8gLTEgOiAxO1xuXG4gICAgICAgICAgICBsZXQgdG9nZ2xlLCBhY3RpdmUsIG5leHQgPSBnZXRJbmRleChpdGVtLCBjaGlsZHJlbiwgcHJldik7XG5cbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgbGVuZ3RoOyBpKyssIG5leHQgPSAobmV4dCArIGRpciArIGxlbmd0aCkgJSBsZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBpZiAoIW1hdGNoZXModGhpcy50b2dnbGVzW25leHRdLCAnLnVrLWRpc2FibGVkICosIC51ay1kaXNhYmxlZCwgW2Rpc2FibGVkXScpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRvZ2dsZSA9IHRoaXMudG9nZ2xlc1tuZXh0XTtcbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlID0gY2hpbGRyZW5bbmV4dF07XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFhY3RpdmUgfHwgcHJldiA+PSAwICYmIGhhc0NsYXNzKGFjdGl2ZSwgdGhpcy5jbHMpIHx8IHByZXYgPT09IG5leHQpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJlbW92ZUNsYXNzKGNoaWxkcmVuLCB0aGlzLmNscyk7XG4gICAgICAgICAgICBhZGRDbGFzcyhhY3RpdmUsIHRoaXMuY2xzKTtcbiAgICAgICAgICAgIGF0dHIodGhpcy50b2dnbGVzLCAnYXJpYS1leHBhbmRlZCcsIGZhbHNlKTtcbiAgICAgICAgICAgIGF0dHIodG9nZ2xlLCAnYXJpYS1leHBhbmRlZCcsIHRydWUpO1xuXG4gICAgICAgICAgICB0aGlzLmNvbm5lY3RzLmZvckVhY2gobGlzdCA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCFoYXNQcmV2KSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlTm93KGxpc3QuY2hpbGRyZW5bbmV4dF0pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlRWxlbWVudChbbGlzdC5jaGlsZHJlbltwcmV2XSwgbGlzdC5jaGlsZHJlbltuZXh0XV0pO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCBTd2l0Y2hlciBmcm9tICcuL3N3aXRjaGVyJztcbmltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQge2hhc0NsYXNzfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3NdLFxuXG4gICAgZXh0ZW5kczogU3dpdGNoZXIsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBtZWRpYTogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIG1lZGlhOiA5NjAsXG4gICAgICAgIGF0dHJJdGVtOiAndWstdGFiLWl0ZW0nXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcblxuICAgICAgICBjb25zdCBjbHMgPSBoYXNDbGFzcyh0aGlzLiRlbCwgJ3VrLXRhYi1sZWZ0JylcbiAgICAgICAgICAgID8gJ3VrLXRhYi1sZWZ0J1xuICAgICAgICAgICAgOiBoYXNDbGFzcyh0aGlzLiRlbCwgJ3VrLXRhYi1yaWdodCcpXG4gICAgICAgICAgICAgICAgPyAndWstdGFiLXJpZ2h0J1xuICAgICAgICAgICAgICAgIDogZmFsc2U7XG5cbiAgICAgICAgaWYgKGNscykge1xuICAgICAgICAgICAgdGhpcy4kY3JlYXRlKCd0b2dnbGUnLCB0aGlzLiRlbCwge2NscywgbW9kZTogJ21lZGlhJywgbWVkaWE6IHRoaXMubWVkaWF9KTtcbiAgICAgICAgfVxuICAgIH1cblxufTtcbiIsImltcG9ydCBNZWRpYSBmcm9tICcuLi9taXhpbi9tZWRpYSc7XG5pbXBvcnQgVG9nZ2xhYmxlIGZyb20gJy4uL21peGluL3RvZ2dsYWJsZSc7XG5pbXBvcnQge2Nsb3Nlc3QsIGhhc1RvdWNoLCBpbmNsdWRlcywgaXNUb3VjaCwgaXNWaXNpYmxlLCBtYXRjaGVzLCBwb2ludGVyRW50ZXIsIHBvaW50ZXJMZWF2ZSwgcXVlcnlBbGwsIHRyaWdnZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtNZWRpYSwgVG9nZ2xhYmxlXSxcblxuICAgIGFyZ3M6ICd0YXJnZXQnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgaHJlZjogU3RyaW5nLFxuICAgICAgICB0YXJnZXQ6IG51bGwsXG4gICAgICAgIG1vZGU6ICdsaXN0J1xuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGhyZWY6IGZhbHNlLFxuICAgICAgICB0YXJnZXQ6IGZhbHNlLFxuICAgICAgICBtb2RlOiAnY2xpY2snLFxuICAgICAgICBxdWV1ZWQ6IHRydWVcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICB0YXJnZXQoe2hyZWYsIHRhcmdldH0sICRlbCkge1xuICAgICAgICAgICAgdGFyZ2V0ID0gcXVlcnlBbGwodGFyZ2V0IHx8IGhyZWYsICRlbCk7XG4gICAgICAgICAgICByZXR1cm4gdGFyZ2V0Lmxlbmd0aCAmJiB0YXJnZXQgfHwgWyRlbF07XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG4gICAgICAgIHRyaWdnZXIodGhpcy50YXJnZXQsICd1cGRhdGVhcmlhJywgW3RoaXNdKTtcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiBgJHtwb2ludGVyRW50ZXJ9ICR7cG9pbnRlckxlYXZlfWAsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gaW5jbHVkZXModGhpcy5tb2RlLCAnaG92ZXInKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGlmICghaXNUb3VjaChlKSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZShgdG9nZ2xlJHtlLnR5cGUgPT09IHBvaW50ZXJFbnRlciA/ICdzaG93JyA6ICdoaWRlJ31gKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdjbGljaycsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gaW5jbHVkZXModGhpcy5tb2RlLCAnY2xpY2snKSB8fCBoYXNUb3VjaCAmJiBpbmNsdWRlcyh0aGlzLm1vZGUsICdob3ZlcicpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG5cbiAgICAgICAgICAgICAgICAvLyBUT0RPIGJldHRlciBpc1RvZ2dsZWQgaGFuZGxpbmdcbiAgICAgICAgICAgICAgICBsZXQgbGluaztcbiAgICAgICAgICAgICAgICBpZiAoY2xvc2VzdChlLnRhcmdldCwgJ2FbaHJlZj1cIiNcIl0sIGFbaHJlZj1cIlwiXScpXG4gICAgICAgICAgICAgICAgICAgIHx8IChsaW5rID0gY2xvc2VzdChlLnRhcmdldCwgJ2FbaHJlZl0nKSkgJiYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5jbHNcbiAgICAgICAgICAgICAgICAgICAgICAgIHx8ICFpc1Zpc2libGUodGhpcy50YXJnZXQpXG4gICAgICAgICAgICAgICAgICAgICAgICB8fCBsaW5rLmhhc2ggJiYgbWF0Y2hlcyh0aGlzLnRhcmdldCwgbGluay5oYXNoKVxuICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgKSB7XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgcmV0dXJuIGluY2x1ZGVzKHRoaXMubW9kZSwgJ21lZGlhJykgJiYgdGhpcy5tZWRpYVxuICAgICAgICAgICAgICAgID8ge21hdGNoOiB0aGlzLm1hdGNoTWVkaWF9XG4gICAgICAgICAgICAgICAgOiBmYWxzZTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7bWF0Y2h9KSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHRvZ2dsZWQgPSB0aGlzLmlzVG9nZ2xlZCh0aGlzLnRhcmdldCk7XG4gICAgICAgICAgICBpZiAobWF0Y2ggPyAhdG9nZ2xlZCA6IHRvZ2dsZWQpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9LFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIHRvZ2dsZSh0eXBlKSB7XG4gICAgICAgICAgICBpZiAodHJpZ2dlcih0aGlzLnRhcmdldCwgdHlwZSB8fCAndG9nZ2xlJywgW3RoaXNdKSkge1xuICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlRWxlbWVudCh0aGlzLnRhcmdldCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCBBY2NvcmRpb24gZnJvbSAnLi9hY2NvcmRpb24nO1xuaW1wb3J0IEFsZXJ0IGZyb20gJy4vYWxlcnQnO1xuaW1wb3J0IENvcmUgZnJvbSAnLi9jb3JlJztcbmltcG9ydCBDb3ZlciBmcm9tICcuL2NvdmVyJztcbmltcG9ydCBEcm9wIGZyb20gJy4vZHJvcCc7XG5pbXBvcnQgRHJvcGRvd24gZnJvbSAnLi9kcm9wZG93bic7XG5pbXBvcnQgRm9ybUN1c3RvbSBmcm9tICcuL2Zvcm0tY3VzdG9tJztcbmltcG9ydCBHaWYgZnJvbSAnLi9naWYnO1xuaW1wb3J0IEdyaWQgZnJvbSAnLi9ncmlkJztcbmltcG9ydCBIZWlnaHRNYXRjaCBmcm9tICcuL2hlaWdodC1tYXRjaCc7XG5pbXBvcnQgSGVpZ2h0Vmlld3BvcnQgZnJvbSAnLi9oZWlnaHQtdmlld3BvcnQnO1xuaW1wb3J0IEljb24sIHtJY29uQ29tcG9uZW50LCBTbGlkZW5hdiwgU2VhcmNoLCBDbG9zZSwgU3Bpbm5lcn0gZnJvbSAnLi9pY29uJztcbmltcG9ydCBJbWcgZnJvbSAnLi9pbWcnO1xuaW1wb3J0IExlYWRlciBmcm9tICcuL2xlYWRlcic7XG5pbXBvcnQgTWFyZ2luIGZyb20gJy4vbWFyZ2luJztcbmltcG9ydCBNb2RhbCBmcm9tICcuL21vZGFsJztcbmltcG9ydCBOYXYgZnJvbSAnLi9uYXYnO1xuaW1wb3J0IE5hdmJhciBmcm9tICcuL25hdmJhcic7XG5pbXBvcnQgT2ZmY2FudmFzIGZyb20gJy4vb2ZmY2FudmFzJztcbmltcG9ydCBPdmVyZmxvd0F1dG8gZnJvbSAnLi9vdmVyZmxvdy1hdXRvJztcbmltcG9ydCBSZXNwb25zaXZlIGZyb20gJy4vcmVzcG9uc2l2ZSc7XG5pbXBvcnQgU2Nyb2xsIGZyb20gJy4vc2Nyb2xsJztcbmltcG9ydCBTY3JvbGxzcHkgZnJvbSAnLi9zY3JvbGxzcHknO1xuaW1wb3J0IFNjcm9sbHNweU5hdiBmcm9tICcuL3Njcm9sbHNweS1uYXYnO1xuaW1wb3J0IFN0aWNreSBmcm9tICcuL3N0aWNreSc7XG5pbXBvcnQgU3ZnIGZyb20gJy4vc3ZnJztcbmltcG9ydCBTd2l0Y2hlciBmcm9tICcuL3N3aXRjaGVyJztcbmltcG9ydCBUYWIgZnJvbSAnLi90YWInO1xuaW1wb3J0IFRvZ2dsZSBmcm9tICcuL3RvZ2dsZSc7XG5pbXBvcnQgVmlkZW8gZnJvbSAnLi92aWRlbyc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgLy8gY29yZSBjb21wb25lbnRzXG4gICAgVUlraXQuY29tcG9uZW50KCdhY2NvcmRpb24nLCBBY2NvcmRpb24pO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnYWxlcnQnLCBBbGVydCk7XG4gICAgVUlraXQuY29tcG9uZW50KCdjb3ZlcicsIENvdmVyKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ2Ryb3AnLCBEcm9wKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ2Ryb3Bkb3duJywgRHJvcGRvd24pO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnZm9ybUN1c3RvbScsIEZvcm1DdXN0b20pO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnZ2lmJywgR2lmKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ2dyaWQnLCBHcmlkKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ2hlaWdodE1hdGNoJywgSGVpZ2h0TWF0Y2gpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnaGVpZ2h0Vmlld3BvcnQnLCBIZWlnaHRWaWV3cG9ydCk7XG4gICAgVUlraXQuY29tcG9uZW50KCdpY29uJywgSWNvbik7XG4gICAgVUlraXQuY29tcG9uZW50KCdpbWcnLCBJbWcpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnbGVhZGVyJywgTGVhZGVyKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ21hcmdpbicsIE1hcmdpbik7XG4gICAgVUlraXQuY29tcG9uZW50KCdtb2RhbCcsIE1vZGFsKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ25hdicsIE5hdik7XG4gICAgVUlraXQuY29tcG9uZW50KCduYXZiYXInLCBOYXZiYXIpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnb2ZmY2FudmFzJywgT2ZmY2FudmFzKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ292ZXJmbG93QXV0bycsIE92ZXJmbG93QXV0byk7XG4gICAgVUlraXQuY29tcG9uZW50KCdyZXNwb25zaXZlJywgUmVzcG9uc2l2ZSk7XG4gICAgVUlraXQuY29tcG9uZW50KCdzY3JvbGwnLCBTY3JvbGwpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnc2Nyb2xsc3B5JywgU2Nyb2xsc3B5KTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ3Njcm9sbHNweU5hdicsIFNjcm9sbHNweU5hdik7XG4gICAgVUlraXQuY29tcG9uZW50KCdzdGlja3knLCBTdGlja3kpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgnc3ZnJywgU3ZnKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ3N3aXRjaGVyJywgU3dpdGNoZXIpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgndGFiJywgVGFiKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ3RvZ2dsZScsIFRvZ2dsZSk7XG4gICAgVUlraXQuY29tcG9uZW50KCd2aWRlbycsIFZpZGVvKTtcblxuICAgIC8vIEljb24gY29tcG9uZW50c1xuICAgIFVJa2l0LmNvbXBvbmVudCgnY2xvc2UnLCBDbG9zZSk7XG4gICAgVUlraXQuY29tcG9uZW50KCdtYXJrZXInLCBJY29uQ29tcG9uZW50KTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ25hdmJhclRvZ2dsZUljb24nLCBJY29uQ29tcG9uZW50KTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ292ZXJsYXlJY29uJywgSWNvbkNvbXBvbmVudCk7XG4gICAgVUlraXQuY29tcG9uZW50KCdwYWdpbmF0aW9uTmV4dCcsIEljb25Db21wb25lbnQpO1xuICAgIFVJa2l0LmNvbXBvbmVudCgncGFnaW5hdGlvblByZXZpb3VzJywgSWNvbkNvbXBvbmVudCk7XG4gICAgVUlraXQuY29tcG9uZW50KCdzZWFyY2hJY29uJywgU2VhcmNoKTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ3NsaWRlbmF2TmV4dCcsIFNsaWRlbmF2KTtcbiAgICBVSWtpdC5jb21wb25lbnQoJ3NsaWRlbmF2UHJldmlvdXMnLCBTbGlkZW5hdik7XG4gICAgVUlraXQuY29tcG9uZW50KCdzcGlubmVyJywgU3Bpbm5lcik7XG4gICAgVUlraXQuY29tcG9uZW50KCd0b3RvcCcsIEljb25Db21wb25lbnQpO1xuXG4gICAgLy8gY29yZSBmdW5jdGlvbmFsaXR5XG4gICAgVUlraXQudXNlKENvcmUpO1xuXG59XG4iLCJpbXBvcnQge2dldENvbXBvbmVudE5hbWV9IGZyb20gJy4vY29tcG9uZW50JztcbmltcG9ydCB7ZmFzdGRvbSwgaGFzQXR0cn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgY29uc3Qge2Nvbm5lY3QsIGRpc2Nvbm5lY3R9ID0gVUlraXQ7XG5cbiAgICBpZiAoISgnTXV0YXRpb25PYnNlcnZlcicgaW4gd2luZG93KSkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKGRvY3VtZW50LmJvZHkpIHtcblxuICAgICAgICBpbml0KCk7XG5cbiAgICB9IGVsc2Uge1xuXG4gICAgICAgIChuZXcgTXV0YXRpb25PYnNlcnZlcihmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgIGlmIChkb2N1bWVudC5ib2R5KSB7XG4gICAgICAgICAgICAgICAgdGhpcy5kaXNjb25uZWN0KCk7XG4gICAgICAgICAgICAgICAgaW5pdCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pKS5vYnNlcnZlKGRvY3VtZW50LCB7Y2hpbGRMaXN0OiB0cnVlLCBzdWJ0cmVlOiB0cnVlfSk7XG5cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBpbml0KCkge1xuXG4gICAgICAgIGFwcGx5KGRvY3VtZW50LmJvZHksIGNvbm5lY3QpO1xuXG4gICAgICAgIGZhc3Rkb20uZmx1c2goKTtcblxuICAgICAgICAobmV3IE11dGF0aW9uT2JzZXJ2ZXIobXV0YXRpb25zID0+IG11dGF0aW9ucy5mb3JFYWNoKGFwcGx5TXV0YXRpb24pKSkub2JzZXJ2ZShkb2N1bWVudCwge1xuICAgICAgICAgICAgY2hpbGRMaXN0OiB0cnVlLFxuICAgICAgICAgICAgc3VidHJlZTogdHJ1ZSxcbiAgICAgICAgICAgIGNoYXJhY3RlckRhdGE6IHRydWUsXG4gICAgICAgICAgICBhdHRyaWJ1dGVzOiB0cnVlXG4gICAgICAgIH0pO1xuXG4gICAgICAgIFVJa2l0Ll9pbml0aWFsaXplZCA9IHRydWU7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gYXBwbHlNdXRhdGlvbihtdXRhdGlvbikge1xuXG4gICAgICAgIGNvbnN0IHt0YXJnZXQsIHR5cGV9ID0gbXV0YXRpb247XG5cbiAgICAgICAgY29uc3QgdXBkYXRlID0gdHlwZSAhPT0gJ2F0dHJpYnV0ZXMnXG4gICAgICAgICAgICA/IGFwcGx5Q2hpbGRMaXN0KG11dGF0aW9uKVxuICAgICAgICAgICAgOiBhcHBseUF0dHJpYnV0ZShtdXRhdGlvbik7XG5cbiAgICAgICAgdXBkYXRlICYmIFVJa2l0LnVwZGF0ZSh0YXJnZXQpO1xuXG4gICAgfVxuXG4gICAgZnVuY3Rpb24gYXBwbHlBdHRyaWJ1dGUoe3RhcmdldCwgYXR0cmlidXRlTmFtZX0pIHtcblxuICAgICAgICBpZiAoYXR0cmlidXRlTmFtZSA9PT0gJ2hyZWYnKSB7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IG5hbWUgPSBnZXRDb21wb25lbnROYW1lKGF0dHJpYnV0ZU5hbWUpO1xuXG4gICAgICAgIGlmICghbmFtZSB8fCAhKG5hbWUgaW4gVUlraXQpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoaGFzQXR0cih0YXJnZXQsIGF0dHJpYnV0ZU5hbWUpKSB7XG4gICAgICAgICAgICBVSWtpdFtuYW1lXSh0YXJnZXQpO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBjb21wb25lbnQgPSBVSWtpdC5nZXRDb21wb25lbnQodGFyZ2V0LCBuYW1lKTtcblxuICAgICAgICBpZiAoY29tcG9uZW50KSB7XG4gICAgICAgICAgICBjb21wb25lbnQuJGRlc3Ryb3koKTtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG5cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBhcHBseUNoaWxkTGlzdCh7YWRkZWROb2RlcywgcmVtb3ZlZE5vZGVzfSkge1xuXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgYWRkZWROb2Rlcy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgYXBwbHkoYWRkZWROb2Rlc1tpXSwgY29ubmVjdCk7XG4gICAgICAgIH1cblxuICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHJlbW92ZWROb2Rlcy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgYXBwbHkocmVtb3ZlZE5vZGVzW2ldLCBkaXNjb25uZWN0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGFwcGx5KG5vZGUsIGZuKSB7XG5cbiAgICAgICAgaWYgKG5vZGUubm9kZVR5cGUgIT09IDEgfHwgaGFzQXR0cihub2RlLCAndWstbm8tYm9vdCcpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBmbihub2RlKTtcbiAgICAgICAgbm9kZSA9IG5vZGUuZmlyc3RFbGVtZW50Q2hpbGQ7XG4gICAgICAgIHdoaWxlIChub2RlKSB7XG4gICAgICAgICAgICBjb25zdCBuZXh0ID0gbm9kZS5uZXh0RWxlbWVudFNpYmxpbmc7XG4gICAgICAgICAgICBhcHBseShub2RlLCBmbik7XG4gICAgICAgICAgICBub2RlID0gbmV4dDtcbiAgICAgICAgfVxuICAgIH1cblxufVxuIiwiaW1wb3J0IFVJa2l0IGZyb20gJy4vYXBpL2luZGV4JztcbmltcG9ydCBjb3JlIGZyb20gJy4vY29yZS9pbmRleCc7XG5pbXBvcnQgYm9vdCBmcm9tICcuL2FwaS9ib290JztcblxuVUlraXQudmVyc2lvbiA9IFZFUlNJT047XG5cbmNvcmUoVUlraXQpO1xuXG5pZiAoIUJVTkRMRUQpIHtcbiAgICBib290KFVJa2l0KTtcbn1cblxuZXhwb3J0IGRlZmF1bHQgVUlraXQ7XG4iXSwibmFtZXMiOlsiY29uc3QiLCJsZXQiLCJhcmd1bWVudHMiLCJ0aGlzIiwic2VsZWN0b3JzIiwiYXBwbHkiLCJnZXRBcmdzIiwicmVtb3ZlIiwia2V5IiwiZGF0YSIsImdldERhdGEiLCJwcm9wIiwibmFtZSIsIm9mZnNldCIsImdldE9mZnNldCIsImhlaWdodCIsImdldEhlaWdodCIsIlNWRyIsImFjdGl2ZSIsImFuaW1hdGUiLCJpbnN0YWxsIiwiTW9kYWwiLCJpIl0sIm1hcHBpbmdzIjoiOzs7Ozs7OztJQUFPLFNBQVMsSUFBSSxDQUFDLEVBQUUsRUFBRSxPQUFPLEVBQUU7UUFDOUIsT0FBTyxVQUFVLENBQUMsRUFBRTtZQUNoQkEsSUFBTSxDQUFDLEdBQUcsU0FBUyxDQUFDLE1BQU0sQ0FBQztZQUMzQixPQUFPLENBQUMsR0FBRyxDQUFDLEdBQUcsQ0FBQyxHQUFHLEVBQUUsQ0FBQyxLQUFLLENBQUMsT0FBTyxFQUFFLFNBQVMsQ0FBQyxHQUFHLEVBQUUsQ0FBQyxJQUFJLENBQUMsT0FBTyxFQUFFLENBQUMsQ0FBQyxHQUFHLEVBQUUsQ0FBQyxJQUFJLENBQUMsT0FBTyxDQUFDLENBQUM7U0FDNUYsQ0FBQztLQUNMOztJQUVEQSxJQUFNLFlBQVksR0FBRyxNQUFNLENBQUMsU0FBUyxDQUFDO0lBQy9CLGlEQUErQjs7QUFFdEMsSUFBTyxTQUFTLE1BQU0sQ0FBQyxHQUFHLEVBQUUsR0FBRyxFQUFFO1FBQzdCLE9BQU8sY0FBYyxDQUFDLElBQUksQ0FBQyxHQUFHLEVBQUUsR0FBRyxDQUFDLENBQUM7S0FDeEM7O0lBRURBLElB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