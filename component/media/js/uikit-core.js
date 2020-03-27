/*! gslUIkit 3.2.6 | http://www.getuikit.com | (c) 2014 - 2019 YOOtheme | MIT License */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define('uikit', factory) :
    (global = global || self, global.gslUIkit = factory());
}(this, (function () { 'use strict';

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

    var toString = objPrototype.toString;
    function isPlainObject(obj) {
        return toString.call(obj) === '[object Object]';
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
        return isObject(obj) && obj.nodeType >= 1;
    }

    function isElement(obj) {
        return isObject(obj) && obj.nodeType === 1;
    }

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
        return isNode(element)
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

    function toWindow(element) {
        if (isWindow(element)) {
            return element;
        }

        element = toNode(element);

        return element
            ? (isDocument(element)
                ? element
                : element.ownerDocument
            ).defaultView
            : window;
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

    function last(array) {
        return array[array.length - 1];
    }

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
                    ctx = closest(parent(context), selectors[0]);
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

        } while ((ancestor = parent(ancestor)));
    };

    function closest(element, selector) {

        if (startsWith(selector, '>')) {
            selector = selector.slice(1);
        }

        return isElement(element)
            ? closestFn.call(element, selector)
            : toNodes(element).map(function (element) { return closest(element, selector); }).filter(Boolean);
    }

    function parent(element) {
        element = toNode(element);
        return element && isElement(element.parentNode) && element.parentNode;
    }

    function parents(element, selector) {
        var elements = [];

        while ((element = parent(element))) {
            if (!selector || matches(element, selector)) {
                elements.push(element);
            }
        }

        return elements;
    }

    function children(element, selector) {
        element = toNode(element);
        var children = element ? toNodes(element.children) : [];
        return selector ? children.filter(function (element) { return matches(element, selector); }) : children;
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

        if (listener.length > 1) {
            listener = detail(listener);
        }

        if (useCapture && useCapture.self) {
            listener = selfFilter(listener);
        }

        if (selector) {
            listener = delegate(targets, selector, listener);
        }

        useCapture = useCaptureFilter(useCapture);

        type.split(' ').forEach(function (type) { return targets.forEach(function (target) { return target.addEventListener(type, listener, useCapture); }
            ); }
        );
        return function () { return off(targets, type, listener, useCapture); };
    }

    function off(targets, type, listener, useCapture) {
        if ( useCapture === void 0 ) useCapture = false;

        useCapture = useCaptureFilter(useCapture);
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

    function selfFilter(listener) {
        return function (e) {
            if (e.target === e.currentTarget || e.target === e.current) {
                return listener.call(null, e);
            }
        };
    }

    function useCaptureFilter(options) {
        return options && isIE && !isBoolean(options)
            ? !!options.capture
            : options;
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
        return e.pointerType === 'touch' || !!e.touches;
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
            : children(parent(element)).indexOf(element);
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
            .map(parent)
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

        if (!isElement(node)) {
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

        var force = !isString(last(args)) ? args.pop() : []; // in iOS 9.3 force === undefined evaluates to false

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
                        transitionProperty: '',
                        transitionDuration: '',
                        transitionTimingFunction: ''
                    });
                    type === 'transitioncanceled' ? reject() : resolve();
                }, {self: true});

                addClass(element, 'gsl-transition');
                css(element, assign({
                    transitionProperty: Object.keys(props).map(propName).join(','),
                    transitionDuration: (duration + "ms"),
                    transitionTimingFunction: timing
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

                }, {self: true});

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

            var boundaries = [getDimensions(toWindow(element))];

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

        if (!coordinates) {
            return getDimensions(element);
        }

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
    }

    function getDimensions(element) {

        if (!element) {
            return {};
        }

        var ref = toWindow(element);
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

        element = toNode(element);

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

    function position(element, parent) {
        var elementOffset = offset(element);
        var parentOffset = offset(parent || toNode(element).offsetParent || toWindow(element).document.documentElement);

        return {top: elementOffset.top - parentOffset.top, left: elementOffset.left - parentOffset.left};
    }

    function offsetPosition(element) {
        var offset = [0, 0];

        element = toNode(element);

        do {

            offset[0] += element.offsetTop;
            offset[1] += element.offsetLeft;

            if (css(element, 'position') === 'fixed') {
                var win = toWindow(element);
                offset[0] += win.pageYOffset;
                offset[1] += win.pageXOffset;
                return offset;
            }

        } while ((element = element.offsetParent));

        return offset;
    }

    var height = dimension('height');
    var width = dimension('width');

    function dimension(prop) {
        var propName = ucfirst(prop);
        return function (element, value) {

            if (isUndefined(value)) {

                if (isWindow(element)) {
                    return element[("inner" + propName)];
                }

                if (isDocument(element)) {
                    var doc = element.documentElement;
                    return Math.max(doc[("offset" + propName)], doc[("scroll" + propName)]);
                }

                element = toNode(element);

                value = css(element, prop);
                value = value === 'auto' ? element[("offset" + propName)] : toFloat(value) || 0;

                return value - boxModelAdjust(element, prop);

            } else {

                css(element, prop, !value && value !== 0
                    ? ''
                    : +value + boxModelAdjust(element, prop) + 'px'
                );

            }

        };
    }

    function boxModelAdjust(element, prop, sizing) {
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

    function toPx(value, property, element) {
        if ( property === void 0 ) property = 'width';
        if ( element === void 0 ) element = window;

        return isNumeric(value)
            ? +value
            : endsWith(value, 'vh')
                ? percent(height(toWindow(element)), value)
                : endsWith(value, 'vw')
                    ? percent(width(toWindow(element)), value)
                    : endsWith(value, '%')
                        ? percent(getDimensions(element)[property], value)
                        : toFloat(value);
    }

    function percent(base, value) {
        return base * toFloat(value) / 100;
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

        flush: flush

    };

    function flush(recursion) {
        if ( recursion === void 0 ) recursion = 1;

        runTasks(fastdom.reads);
        runTasks(fastdom.writes.splice(0, fastdom.writes.length));

        fastdom.scheduled = false;

        if (fastdom.reads.length || fastdom.writes.length) {
            scheduleFlush(recursion + 1);
        }
    }

    var RECURSION_LIMIT = 5;
    function scheduleFlush(recursion) {
        if (!fastdom.scheduled) {
            fastdom.scheduled = true;
            if (recursion > RECURSION_LIMIT) {
                throw new Error('Maximum recursion limit reached.');
            } else if (recursion) {
                Promise.resolve().then(function () { return flush(recursion); });
            } else {
                requestAnimationFrame(function () { return flush(); });
            }
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

        init: function() {
            var this$1 = this;


            this.positions = [];

            var position;
            this.unbind = on(document, 'mousemove', function (e) { return position = getEventPos(e, 'page'); });
            this.interval = setInterval(function () {

                if (!position) {
                    return;
                }

                this$1.positions.push(position);

                if (this$1.positions.length > 5) {
                    this$1.positions.shift();
                }
            }, 50);

        },

        cancel: function() {
            this.unbind && this.unbind();
            this.interval && clearInterval(this.interval);
        },

        movesTo: function(target) {

            if (this.positions.length < 2) {
                return false;
            }

            var p = offset(target);
            var left = p.left;
            var right = p.right;
            var top = p.top;
            var bottom = p.bottom;

            var ref = this.positions;
            var prevPosition = ref[0];
            var position = last(this.positions);
            var path = [prevPosition, position];

            if (pointInRect(position, p)) {
                return false;
            }

            var diagonals = [[{x: left, y: top}, {x: right, y: bottom}], [{x: left, y: bottom}, {x: right, y: top}]];

            return diagonals.some(function (diagonal) {
                var intersection = intersect(path, diagonal);
                return intersection && pointInRect(intersection, p);
            });
        }

    };

    // Inspired by http://paulbourke.net/geometry/pointlineplane/
    function intersect(ref, ref$1) {
        var ref_0 = ref[0];
        var x1 = ref_0.x;
        var y1 = ref_0.y;
        var ref_1 = ref[1];
        var x2 = ref_1.x;
        var y2 = ref_1.y;
        var ref$1_0 = ref$1[0];
        var x3 = ref$1_0.x;
        var y3 = ref$1_0.y;
        var ref$1_1 = ref$1[1];
        var x4 = ref$1_1.x;
        var y4 = ref$1_1.y;


        var denominator = (y4 - y3) * (x2 - x1) - (x4 - x3) * (y2 - y1);

        // Lines are parallel
        if (denominator === 0) {
            return false;
        }

        var ua = ((x4 - x3) * (y1 - y3) - (y4 - y3) * (x1 - x3)) / denominator;

        if (ua < 0) {
            return false;
        }

        // Return a object with the x and y coordinates of the intersection
        return {x: x1 + ua * (x2 - x1), y: y1 + ua * (y2 - y1)};
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

    function isInView(element, offsetTop, offsetLeft) {
        if ( offsetTop === void 0 ) offsetTop = 0;
        if ( offsetLeft === void 0 ) offsetLeft = 0;


        if (!isVisible(element)) {
            return false;
        }

        var parents = overflowParents(element).concat(element);

        for (var i = 0; i < parents.length - 1; i++) {
            var ref = offset(getViewport(parents[i]));
            var top = ref.top;
            var left = ref.left;
            var bottom = ref.bottom;
            var right = ref.right;
            var vp = {
                top: top - offsetTop,
                left: left - offsetLeft,
                bottom: bottom + offsetTop,
                right: right + offsetLeft
            };

            var client = offset(parents[i + 1]);

            if (!intersectRect(client, vp) && !pointInRect({x: client.left, y: client.top}, vp)) {
                return false;
            }
        }

        return true;
    }

    function scrollTop(element, top) {

        if (isWindow(element) || isDocument(element)) {
            element = getScrollingElement(element);
        } else {
            element = toNode(element);
        }

        element.scrollTop = top;
    }

    function scrollIntoView(element, ref) {
        if ( ref === void 0 ) ref = {};
        var duration = ref.duration; if ( duration === void 0 ) duration = 1000;
        var offset = ref.offset; if ( offset === void 0 ) offset = 0;


        if (!isVisible(element)) {
            return;
        }

        var parents = overflowParents(element).concat(element);
        duration /= parents.length - 1;

        var promise = Promise.resolve();
        var loop = function ( i ) {
            promise = promise.then(function () { return new Promise(function (resolve) {

                    var scrollElement = parents[i];
                    var element = parents[i + 1];

                    var scroll = scrollElement.scrollTop;
                    var top = position(element, getViewport(scrollElement)).top - offset;

                    var start = Date.now();
                    var step = function () {

                        var percent = ease(clamp((Date.now() - start) / duration));

                        scrollTop(scrollElement, scroll + top * percent);

                        // scroll more if we have not reached our destination
                        if (percent !== 1) {
                            requestAnimationFrame(step);
                        } else {
                            resolve();
                        }

                    };

                    step();
                }); }
            );
        };

        for (var i = 0; i < parents.length - 1; i++) loop( i );

        return promise;

        function ease(k) {
            return 0.5 * (1 - Math.cos(Math.PI * k));
        }

    }

    function scrolledOver(element, heightOffset) {
        if ( heightOffset === void 0 ) heightOffset = 0;


        if (!isVisible(element)) {
            return 0;
        }

        var scrollElement = last(scrollParents(element));
        var scrollHeight = scrollElement.scrollHeight;
        var scrollTop = scrollElement.scrollTop;
        var viewport = getViewport(scrollElement);
        var viewportHeight = offset(viewport).height;
        var viewportTop = offsetPosition(element)[0] - scrollTop - offsetPosition(scrollElement)[0];
        var viewportDist = Math.min(viewportHeight, viewportTop + scrollTop);

        var top = viewportTop - viewportDist;
        var dist = Math.min(
            offset(element).height + heightOffset + viewportDist,
            scrollHeight - (viewportTop + scrollTop),
            scrollHeight - viewportHeight
        );

        return clamp(-1 * top / dist);
    }

    function scrollParents(element, overflowRe) {
        if ( overflowRe === void 0 ) overflowRe = /auto|scroll/;

        var scrollEl = getScrollingElement(element);
        var scrollParents = parents(element).filter(function (parent) { return parent === scrollEl
            || overflowRe.test(css(parent, 'overflow'))
            && parent.scrollHeight > offset(parent).height; }
        ).reverse();
        return scrollParents.length ? scrollParents : [scrollEl];
    }

    function getViewport(scrollElement) {
        return scrollElement === getScrollingElement(scrollElement) ? window : scrollElement;
    }

    function overflowParents(element) {
        return scrollParents(element, /auto|scroll|hidden/);
    }

    function getScrollingElement(element) {
        var ref = toWindow(element);
        var document = ref.document;
        return document.scrollingElement || document.documentElement;
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
        __proto__: null,
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
        offsetPosition: offsetPosition,
        height: height,
        width: width,
        boxModelAdjust: boxModelAdjust,
        flipPosition: flipPosition,
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
        isElement: isElement,
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
        toWindow: toWindow,
        toList: toList,
        toMs: toMs,
        isEqual: isEqual,
        swap: swap,
        assign: assign,
        last: last,
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
        parent: parent,
        parents: parents,
        children: children,
        escape: escape,
        css: css,
        getStyles: getStyles,
        getStyle: getStyle,
        getCssVar: getCssVar,
        propName: propName,
        isInView: isInView,
        scrollTop: scrollTop,
        scrollIntoView: scrollIntoView,
        scrolledOver: scrolledOver,
        scrollParents: scrollParents,
        getViewport: getViewport
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

            parents(element).reverse().forEach(function (element) { return update(element[DATA], e); });
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
                    this[key] = methods[key].bind(this);
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

            component._events.push(
                on(
                    el,
                    name,
                    !delegate
                        ? null
                        : isString(delegate)
                            ? delegate
                            : delegate.call(component),
                    isString(handler) ? component[handler] : handler.bind(component),
                    {passive: passive, capture: capture, self: self}
                )
            );

        }

        function notIn(options, key) {
            return options.every(function (arr) { return !arr || !hasOwn(arr, key); });
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
                        data[key] = props[key] ? coerce(props[key], data[key]) : data[key];
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

            var id = hyphenate(name);

            name = camelize(id);

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

                return component.options.functional
                    ? new component({data: isPlainObject(element) ? element : [].concat( argsArray )})
                    : !element ? init(element) : $$(element).map(init)[0];

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
    gslUIkit.version = '3.2.6';

    globalAPI(gslUIkit);
    hooksAPI(gslUIkit);
    stateAPI(gslUIkit);
    componentAPI(gslUIkit);
    instanceAPI(gslUIkit);

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

                gslUIkit.update(null, e.type);

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

                // Handle Swipe Gesture
                var pos = getEventPos(e);
                var target = 'tagName' in e.target ? e.target : e.target.parentNode;
                off = once(document, (pointerUp + " " + pointerCancel), function (e) {

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

                // Force click event anywhere on iOS < 13
                if (pointerDown === 'touchstart') {
                    css(document.body, 'cursor', 'pointer');
                    once(document, (pointerUp + " " + pointerCancel), function () { return setTimeout(function () { return css(document.body, 'cursor', ''); }
                        , 50); }
                    );
                }

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

    function boot (gslUIkit) {

        var connect = gslUIkit.connect;
        var disconnect = gslUIkit.disconnect;

        if (!('MutationObserver' in window)) {
            return;
        }

        fastdom.read(init);

        function init() {

            if (document.body) {
                apply(document.body, connect);
            }

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

    }

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

                    var p;

                    if (!this$1.queued || !isUndefined(animate) || !isUndefined(show) || !this$1.hasAnimation || targets.length < 2) {

                        p = all(targets);

                    } else {

                        var toggled = targets.filter(function (el) { return this$1.isToggled(el); });
                        var untoggled = targets.filter(function (el) { return !includes(toggled, el); });
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
                return this.toggleElement(targets, show, false);
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
                            } else {
                                var toggle = $(this$1.$props.toggle, el);
                                if (animate !== false && !isInView(toggle)) {
                                    scrollIntoView(toggle);
                                }
                            }

                            el._wrapper = null;
                            unwrap(content);

                        });

                    });
            }

        }

    };

    var alert = {

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

    var cover = {

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
                var ref = el.parentNode;
                var height = ref.offsetHeight;
                var width = ref.offsetWidth;
                var dim = Dimensions.cover(
                    {
                        width: this.width || el.naturalWidth || el.videoWidth || el.clientWidth,
                        height: this.height || el.naturalHeight || el.videoHeight || el.clientHeight
                    },
                    {
                        width: width + (width % 2 ? 1 : 0),
                        height: height + (height % 2 ? 1 : 0)
                    }
                );

                if (!dim.width || !dim.height) {
                    return false;
                }

                return dim;
            },

            write: function(ref) {
                var height = ref.height;
                var width = ref.width;

                css(this.$el, {height: height, width: width});
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

                handler: function(ref) {
                    var defaultPrevented = ref.defaultPrevented;
                    var hash = ref.current.hash;

                    if (!defaultPrevented && hash && !within(hash, this.$el)) {
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

                name: 'toggleshow',

                self: true,

                handler: function(e, toggle) {
                    e.preventDefault();
                    this.show(toggle);
                }

            },

            {

                name: 'togglehide',

                self: true,

                handler: function(e) {
                    e.preventDefault();
                    this.hide();
                }

            },

            {

                name: pointerEnter,

                filter: function() {
                    return includes(this.mode, 'hover');
                },

                handler: function(e) {
                    if (!isTouch(e)) {
                        this.clearTimers();
                    }
                }

            },

            {

                name: pointerLeave,

                filter: function() {
                    return includes(this.mode, 'hover');
                },

                handler: function(e) {
                    if (!isTouch(e) && !matches(this.$el, ':hover')) {
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
                    var this$1 = this;


                    active = this;

                    this.tracker.init();
                    trigger(this.$el, 'updatearia');

                    // If triggered from an click event handler, delay adding the click handler
                    var off = delayOn(document, 'click', function (ref) {
                        var defaultPrevented = ref.defaultPrevented;
                        var target = ref.target;

                        if (!defaultPrevented && !within(target, this$1.$el) && !(this$1.toggle && within(target, this$1.toggle.$el))) {
                            this$1.hide(false);
                        }
                    });

                    once(this.$el, 'hide', off, {self: true});
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
                        attr((toggle || this.toggle).$el, 'aria-expanded', this.isToggled());
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
                if ( toggle === void 0 ) toggle = this.toggle;
                if ( delay === void 0 ) delay = true;


                if (this.isToggled() && toggle && this.toggle && toggle.$el !== this.toggle.$el) {
                    this.hide(false);
                }

                this.toggle = toggle;

                this.clearTimers();

                if (this.isActive()) {
                    return;
                }

                if (active) {

                    if (delay && active.isDelaying) {
                        this.showTimer = setTimeout(this.show, 10);
                        return;
                    }

                    while (active && !within(this.$el, active.$el)) {
                        active.hide(false);
                    }
                }

                this.showTimer = setTimeout(function () { return !this$1.isToggled() && this$1.toggleElement(this$1.$el, true); }, delay && this.delayShow || 0);

            },

            hide: function(delay) {
                var this$1 = this;
                if ( delay === void 0 ) delay = true;


                var hide = function () { return this$1.toggleNow(this$1.$el, false); };

                this.clearTimers();

                this.isDelaying = getPositionedElements(this.$el).some(function (el) { return this$1.tracker.movesTo(el); });

                if (delay && this.isDelaying) {
                    this.hideTimer = setTimeout(this.hide, 50);
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

    function getPositionedElements(el) {
        var result = css(el, 'position') !== 'static' ? [el] : [];
        return result.concat.apply(result, children(el).map(getPositionedElements));
    }

    function delayOn(el, type, fn) {
        var off = once(el, type, function () { return off = on(el, type, fn); }
        , true);
        return function () { return off(); };
    }

    var dropdown = {

        extends: Drop

    };

    var formCustom = {

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
                : matches(input, 'select') && (option = $$('option', input).filter(function (el) { return el.selected; })[0]) // eslint-disable-line prefer-destructuring
                    ? option.textContent
                    : input.value;

            if (prev !== value) {
                target[prop] = value;
            }

        },

        events: [

            {
                name: 'change',

                handler: function() {
                    this.$emit();
                }
            },

            {
                name: 'reset',

                el: function() {
                    return closest(this.$el, 'form');
                },

                handler: function() {
                    this.$emit();
                }
            }

        ]

    };

    // Deprecated
    var gif = {

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

                if (dim.top >= leftDim.bottom - 1 && dim.top !== leftDim.top) {
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

    var grid = {

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

                write: function(ref) {
                    var stacks = ref.stacks;

                    toggleClass(this.$el, this.clsStack, stacks);
                },

                events: ['resize']

            },

            {

                read: function(ref) {
                    var rows = ref.rows;


                    if (this.masonry || this.parallax) {
                        rows = rows.map(function (elements) { return sortBy(elements, 'offsetLeft'); });

                        if (isRtl) {
                            rows.map(function (row) { return row.reverse(); });
                        }

                    } else {
                        return false;
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

                    var padding = this.parallax && getPaddingBottom(this.parallax, rows, translates);

                    return {padding: padding, rows: rows, translates: translates, height: !transitionInProgress ? elHeight : false};

                },

                write: function(ref) {
                    var stacks = ref.stacks;
                    var height = ref.height;
                    var padding = ref.padding;


                    toggleClass(this.$el, this.clsStack, stacks);

                    css(this.$el, 'paddingBottom', padding);
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

    function getPaddingBottom(distance, rows, translates) {
        var column = 0;
        var max = 0;
        var maxScrolled = 0;
        for (var i = rows.length - 1; i >= 0; i--) {
            for (var j = column; j < rows[i].length; j++) {
                var el = rows[i][j];
                var bottom = el.offsetTop + height(el) + (translates && -translates[i][j]);
                max = Math.max(max, bottom);
                maxScrolled = Math.max(maxScrolled, bottom + (j % 2 ? distance : distance / 8));
                column++;
            }
        }
        return maxScrolled - max;
    }

    function getMarginTop(root, cls) {

        var nodes = children(root);
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

        props: {
            selMinHeight: String
        },

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
                        if (height && (this$1.forceHeight || Math.round(height + boxModelAdjust(el, 'height', 'content-box')) >= el.offsetHeight)) {
                            css(el, 'height', height);
                        }
                    });
                },

                order: 5,

                events: ['resize']

            }

        ]

    } : {};

    var heightMatch = {

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
        var heights = elements.map(function (el) { return offset(el).height - boxModelAdjust(el, 'height', 'content-box'); });
        var max = Math.max.apply(null, heights);

        return {heights: heights, max: max};
    }

    var heightViewport = {

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

            read: function(ref) {
                var prev = ref.minHeight;


                if (!isVisible(this.$el)) {
                    return false;
                }

                var minHeight = '';
                var box = boxModelAdjust(this.$el, 'height', 'content-box');

                if (this.expand) {

                    this.$el.dataset.heightExpand = '';

                    if ($('[data-height-expand]') !== this.$el) {
                        return false;
                    }

                    minHeight = height(window) - (offsetHeight(document.documentElement) - offsetHeight(this.$el)) - box || '';

                } else {

                    // on mobile devices (iOS and Android) window.innerHeight !== 100vh
                    minHeight = 'calc(100vh';

                    if (this.offsetTop) {

                        var ref$1 = offset(this.$el);
                        var top = ref$1.top;
                        minHeight += top > 0 && top < height(window) / 2 ? (" - " + top + "px") : '';

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

                return {minHeight: minHeight, prev: prev};
            },

            write: function(ref) {
                var minHeight = ref.minHeight;
                var prev = ref.prev;


                css(this.$el, {minHeight: minHeight});

                if (minHeight !== prev) {
                    this.$update(this.$el, 'resize');
                }

                if (this.minHeight && toFloat(css(this.$el, 'minHeight')) < this.minHeight) {
                    css(this.$el, 'minHeight', this.minHeight);
                }

            },

            events: ['resize']

        }

    };

    function offsetHeight(el) {
        return el && offset(el).height || 0;
    }

    var SVG = {

        args: 'src',

        props: {
            id: Boolean,
            icon: String,
            src: String,
            style: String,
            width: Number,
            height: Number,
            ratio: Number,
            class: String,
            strokeAnimation: Boolean,
            focusable: Boolean, // IE 11
            attributes: 'list'
        },

        data: {
            ratio: 1,
            include: ['style', 'class', 'focusable'],
            class: '',
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

        extends: SVG,

        args: 'icon',

        props: ['icon'],

        data: {
            include: ['focusable']
        },

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

    var img = {

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


                // Old chromium based browsers (UC Browser) did not implement `isIntersecting`
                if (!entries.some(function (entry) { return isUndefined(entry.isIntersecting) || entry.isIntersecting; })) {
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

    var leader = {

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

    var active$1 = [];

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

                    if (includes(active$1, this)) {
                        return false;
                    }

                    if (!this.stack && active$1.length) {
                        Promise.all(active$1.map(function (modal) { return modal.hide(); })).then(this.show);
                        e.preventDefault();
                    } else {
                        active$1.push(this);
                    }
                }

            },

            {

                name: 'show',

                self: true,

                handler: function() {
                    var this$1 = this;


                    if (width(window) - width(document) && this.overlay) {
                        css(document.body, 'overflowY', 'scroll');
                    }

                    addClass(document.documentElement, this.clsPage);

                    if (this.bgClose) {
                        once(this.$el, 'hide', delayOn(document, 'click', function (ref) {
                            var defaultPrevented = ref.defaultPrevented;
                            var target = ref.target;

                            var current = last(active$1);
                            if (!defaultPrevented
                                && current === this$1
                                && (!current.overlay || within(target, current.$el))
                                && !within(target, current.panel)
                            ) {
                                current.hide();
                            }
                        }), {self: true});
                    }

                    if (this.escClose) {
                        once(this.$el, 'hide', on(document, 'keydown', function (e) {
                            var current = last(active$1);
                            if (e.keyCode === 27 && current === this$1) {
                                e.preventDefault();
                                current.hide();
                            }
                        }), {self: true});
                    }
                }

            },

            {

                name: 'hidden',

                self: true,

                handler: function() {
                    var this$1 = this;


                    active$1.splice(active$1.indexOf(this), 1);

                    if (!active$1.length) {
                        css(document.body, 'overflowY', '');
                    }

                    if (!active$1.some(function (modal) { return modal.clsPage === this$1.clsPage; })) {
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


                if (this.container && this.$el.parentNode !== this.container) {
                    append(this.container, this.$el);
                    return new Promise(function (resolve) { return requestAnimationFrame(function () { return this$1.show().then(resolve); }
                        ); }
                    );
                }

                return this.toggleElement(this.$el, true, animate$1(this));
            },

            hide: function() {
                return this.toggleElement(this.$el, false, animate$1(this));
            }

        }

    };

    function animate$1(ref) {
        var transitionElement = ref.transitionElement;
        var _toggle = ref._toggle;

        return function (el, show) { return new Promise(function (resolve, reject) { return once(el, 'show hide', function () {
                    el._reject && el._reject();
                    el._reject = reject;

                    _toggle(el, show);

                    var off = once(transitionElement, 'transitionstart', function () {
                        once(transitionElement, 'transitionend transitioncancel', resolve, {self: true});
                        clearTimeout(timer);
                    }, {self: true});

                    var timer = setTimeout(function () {
                        off();
                        resolve();
                    }, toMs(css(transitionElement, 'transitionDuration')));

                }); }
            ); };
    }

    var modal = {

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

            on(dialog.$el, 'hidden', function () { return Promise.resolve(function () { return dialog.$destroy(true); }); }, {self: true});

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

    var nav = {

        extends: Accordion,

        data: {
            targets: '> .gsl-parent',
            toggle: '> a',
            content: '> ul'
        }

    };

    var navbar = {

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

    var offcanvas = {

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
            selClose: '.gsl-offcanvas-close',
            container: false
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
                    var hash = ref.current.hash;
                    var defaultPrevented = ref.defaultPrevented;

                    if (!defaultPrevented && hash && $(hash, document.body)) {
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
                    css(document.body, 'touch-action', 'pan-y pinch-zoom');
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
                    css(document.body, 'touch-action', '');
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
        getViewport$1().content += ',user-scalable=0';
    }

    function resumeUserScale() {
        var viewport = getViewport$1();
        viewport.content = viewport.content.replace(/,user-scalable=0$/, '');
    }

    function getViewport$1() {
        return $('meta[name="viewport"]', document.head) || append(document.head, '<meta name="viewport">');
    }

    var overflowAuto = {

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

    var responsive = {

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

    var scroll = {

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

                if (trigger(this.$el, 'beforescroll', [this, el])) {
                    scrollIntoView(el, this.$props).then(function () { return trigger(this$1.$el, 'scrolled', [this$1, el]); }
                    );
                }

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

    var scrollspy = {

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
                        var toggle = function (inview) {

                            css(el, 'visibility', !inview && this$1.hidden ? 'hidden' : '');

                            toggleClass(el, this$1.inViewClass, inview);
                            toggleClass(el, state.cls);

                            trigger(el, inview ? 'inview' : 'outview');

                            state.inview = inview;

                            this$1.$update(el);

                        };

                        if (state.show && !state.inview && !state.queued) {

                            state.queued = true;

                            data.promise = (data.promise || Promise.resolve()).then(function () { return new Promise(function (resolve) { return setTimeout(resolve, this$1.delay); }
                                ); }
                            ).then(function () {
                                toggle(true);
                                setTimeout(function () { return state.queued = false; }, 300);
                            });

                        } else if (!state.show && state.inview && !state.queued && this$1.repeat) {

                            toggle(false);

                        }

                    });

                },

                events: ['scroll', 'resize']

            }

        ]

    };

    var scrollspyNav = {

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

            targets: function() {
                return $$(this.links.map(function (el) { return escape(el.hash).substr(1); }).join(','));
            },

            elements: function(ref) {
                var selector = ref.closest;

                return closest($$(this.targets.map(function (el) { return ("[href=\"#" + (el.id) + "\"]"); }).join(',')), selector || '*');
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

                read: function() {
                    var this$1 = this;


                    var ref = this.targets;
                    var length = ref.length;

                    if (!length || !isVisible(this.$el)) {
                        return false;
                    }

                    var scrollElement = last(scrollParents(this.targets[0]));
                    var scrollTop = scrollElement.scrollTop;
                    var scrollHeight = scrollElement.scrollHeight;
                    var viewport = getViewport(scrollElement);
                    var scroll = scrollTop;
                    var max = scrollHeight - offset(viewport).height;
                    var active = false;

                    if (scroll === max) {
                        active = length - 1;
                    } else {

                        this.targets.every(function (el, i) {
                            var ref = position(el, viewport);
                            var top = ref.top;
                            if (top - this$1.offset <= 0) {
                                active = i;
                                return true;
                            }
                        });

                        if (active === false && this.overflow) {
                            active = 0;
                        }
                    }

                    return {active: active};
                },

                write: function(ref) {
                    var active = ref.active;


                    this.links.forEach(function (el) { return el.blur(); });
                    removeClass(this.elements, this.cls);

                    if (active !== false) {
                        trigger(this.$el, 'active', [active, addClass(this.elements[active], this.cls)]);
                    }

                },

                events: ['scroll', 'resize']

            }

        ]

    };

    var sticky = {

        mixins: [Class, Media],

        props: {
            top: null,
            bottom: Boolean,
            offset: String,
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

            offset: function(ref) {
                var offset = ref.offset;

                return toPx(offset);
            },

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

        if (isNumeric(value) && isString(value) && value.match(/^-?\d/)) {

            return propOffset + toPx(value);

        } else {

            return offset(value === true ? $el.parentNode : query(value, $el)).bottom;

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
                    this.show(children(this.$el).filter(function (el) { return within(e.current, el); })[0]);
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

            this.swiping && css(this.connects, 'touch-action', 'pan-y pinch-zoom');

        },

        methods: {

            index: function() {
                return !isEmpty(this.connects) ? index(filter(this.connects[0].children, ("." + (this.cls)))[0]) : -1;
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

                if (!active || prev === next) {
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

    var tab = {

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

    var toggle = {

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
                            this.cls && !hasClass(this.target, this.cls.split(' ')[0])
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



    var components = /*#__PURE__*/Object.freeze({
        __proto__: null,
        Accordion: Accordion,
        Alert: alert,
        Cover: cover,
        Drop: Drop,
        Dropdown: dropdown,
        FormCustom: formCustom,
        Gif: gif,
        Grid: grid,
        HeightMatch: heightMatch,
        HeightViewport: heightViewport,
        Icon: Icon,
        Img: img,
        Leader: leader,
        Margin: Margin,
        Modal: modal,
        Nav: nav,
        Navbar: navbar,
        Offcanvas: offcanvas,
        OverflowAuto: overflowAuto,
        Responsive: responsive,
        Scroll: scroll,
        Scrollspy: scrollspy,
        ScrollspyNav: scrollspyNav,
        Sticky: sticky,
        Svg: SVG,
        Switcher: Switcher,
        Tab: tab,
        Toggle: toggle,
        Video: Video,
        Close: Close,
        Spinner: Spinner,
        SlidenavNext: Slidenav,
        SlidenavPrevious: Slidenav,
        SearchIcon: Search,
        Marker: IconComponent,
        NavbarToggleIcon: IconComponent,
        OverlayIcon: IconComponent,
        PaginationNext: IconComponent,
        PaginationPrevious: IconComponent,
        Totop: IconComponent
    });

    // register components
    each(components, function (component, name) { return gslUIkit.component(name, component); }
    );

    // core functionality
    gslUIkit.use(Core);

    boot(gslUIkit);

    return gslUIkit;

})));

//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidWlraXQtY29yZS5qcyIsInNvdXJjZXMiOlsic3JjL2pzL3V0aWwvbGFuZy5qcyIsInNyYy9qcy91dGlsL2F0dHIuanMiLCJzcmMvanMvdXRpbC9lbnYuanMiLCJzcmMvanMvdXRpbC9zZWxlY3Rvci5qcyIsInNyYy9qcy91dGlsL2ZpbHRlci5qcyIsInNyYy9qcy91dGlsL2V2ZW50LmpzIiwic3JjL2pzL3V0aWwvcHJvbWlzZS5qcyIsInNyYy9qcy91dGlsL2FqYXguanMiLCJzcmMvanMvdXRpbC9kb20uanMiLCJzcmMvanMvdXRpbC9jbGFzcy5qcyIsInNyYy9qcy91dGlsL3N0eWxlLmpzIiwic3JjL2pzL3V0aWwvYW5pbWF0aW9uLmpzIiwic3JjL2pzL3V0aWwvZGltZW5zaW9ucy5qcyIsInNyYy9qcy91dGlsL2Zhc3Rkb20uanMiLCJzcmMvanMvdXRpbC9tb3VzZS5qcyIsInNyYy9qcy91dGlsL29wdGlvbnMuanMiLCJzcmMvanMvdXRpbC9wbGF5ZXIuanMiLCJzcmMvanMvdXRpbC92aWV3cG9ydC5qcyIsInNyYy9qcy91dGlsL2ludGVyc2VjdGlvbi5qcyIsInNyYy9qcy9hcGkvZ2xvYmFsLmpzIiwic3JjL2pzL2FwaS9ob29rcy5qcyIsInNyYy9qcy9hcGkvc3RhdGUuanMiLCJzcmMvanMvYXBpL2luc3RhbmNlLmpzIiwic3JjL2pzL2FwaS9jb21wb25lbnQuanMiLCJzcmMvanMvYXBpL2luZGV4LmpzIiwic3JjL2pzL2NvcmUvY29yZS5qcyIsInNyYy9qcy9hcGkvYm9vdC5qcyIsInNyYy9qcy9taXhpbi9jbGFzcy5qcyIsInNyYy9qcy9taXhpbi90b2dnbGFibGUuanMiLCJzcmMvanMvY29yZS9hY2NvcmRpb24uanMiLCJzcmMvanMvY29yZS9hbGVydC5qcyIsInNyYy9qcy9jb3JlL3ZpZGVvLmpzIiwic3JjL2pzL2NvcmUvY292ZXIuanMiLCJzcmMvanMvbWl4aW4vcG9zaXRpb24uanMiLCJzcmMvanMvY29yZS9kcm9wLmpzIiwic3JjL2pzL2NvcmUvZHJvcGRvd24uanMiLCJzcmMvanMvY29yZS9mb3JtLWN1c3RvbS5qcyIsInNyYy9qcy9jb3JlL2dpZi5qcyIsInNyYy9qcy9jb3JlL21hcmdpbi5qcyIsInNyYy9qcy9jb3JlL2dyaWQuanMiLCJzcmMvanMvbWl4aW4vZmxleC1idWcuanMiLCJzcmMvanMvY29yZS9oZWlnaHQtbWF0Y2guanMiLCJzcmMvanMvY29yZS9oZWlnaHQtdmlld3BvcnQuanMiLCJzcmMvanMvY29yZS9zdmcuanMiLCJzcmMvanMvY29yZS9pY29uLmpzIiwic3JjL2pzL2NvcmUvaW1nLmpzIiwic3JjL2pzL21peGluL21lZGlhLmpzIiwic3JjL2pzL2NvcmUvbGVhZGVyLmpzIiwic3JjL2pzL21peGluL2NvbnRhaW5lci5qcyIsInNyYy9qcy9taXhpbi9tb2RhbC5qcyIsInNyYy9qcy9jb3JlL21vZGFsLmpzIiwic3JjL2pzL2NvcmUvbmF2LmpzIiwic3JjL2pzL2NvcmUvbmF2YmFyLmpzIiwic3JjL2pzL2NvcmUvb2ZmY2FudmFzLmpzIiwic3JjL2pzL2NvcmUvb3ZlcmZsb3ctYXV0by5qcyIsInNyYy9qcy9jb3JlL3Jlc3BvbnNpdmUuanMiLCJzcmMvanMvY29yZS9zY3JvbGwuanMiLCJzcmMvanMvY29yZS9zY3JvbGxzcHkuanMiLCJzcmMvanMvY29yZS9zY3JvbGxzcHktbmF2LmpzIiwic3JjL2pzL2NvcmUvc3RpY2t5LmpzIiwic3JjL2pzL2NvcmUvc3dpdGNoZXIuanMiLCJzcmMvanMvY29yZS90YWIuanMiLCJzcmMvanMvY29yZS90b2dnbGUuanMiLCJzcmMvanMvdWlraXQtY29yZS5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCBvYmpQcm90b3R5cGUgPSBPYmplY3QucHJvdG90eXBlO1xuY29uc3Qge2hhc093blByb3BlcnR5fSA9IG9ialByb3RvdHlwZTtcblxuZXhwb3J0IGZ1bmN0aW9uIGhhc093bihvYmosIGtleSkge1xuICAgIHJldHVybiBoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwga2V5KTtcbn1cblxuY29uc3QgaHlwaGVuYXRlQ2FjaGUgPSB7fTtcbmNvbnN0IGh5cGhlbmF0ZVJlID0gLyhbYS16XFxkXSkoW0EtWl0pL2c7XG5cbmV4cG9ydCBmdW5jdGlvbiBoeXBoZW5hdGUoc3RyKSB7XG5cbiAgICBpZiAoIShzdHIgaW4gaHlwaGVuYXRlQ2FjaGUpKSB7XG4gICAgICAgIGh5cGhlbmF0ZUNhY2hlW3N0cl0gPSBzdHJcbiAgICAgICAgICAgIC5yZXBsYWNlKGh5cGhlbmF0ZVJlLCAnJDEtJDInKVxuICAgICAgICAgICAgLnRvTG93ZXJDYXNlKCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGh5cGhlbmF0ZUNhY2hlW3N0cl07XG59XG5cbmNvbnN0IGNhbWVsaXplUmUgPSAvLShcXHcpL2c7XG5cbmV4cG9ydCBmdW5jdGlvbiBjYW1lbGl6ZShzdHIpIHtcbiAgICByZXR1cm4gc3RyLnJlcGxhY2UoY2FtZWxpemVSZSwgdG9VcHBlcik7XG59XG5cbmZ1bmN0aW9uIHRvVXBwZXIoXywgYykge1xuICAgIHJldHVybiBjID8gYy50b1VwcGVyQ2FzZSgpIDogJyc7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB1Y2ZpcnN0KHN0cikge1xuICAgIHJldHVybiBzdHIubGVuZ3RoID8gdG9VcHBlcihudWxsLCBzdHIuY2hhckF0KDApKSArIHN0ci5zbGljZSgxKSA6ICcnO1xufVxuXG5jb25zdCBzdHJQcm90b3R5cGUgPSBTdHJpbmcucHJvdG90eXBlO1xuY29uc3Qgc3RhcnRzV2l0aEZuID0gc3RyUHJvdG90eXBlLnN0YXJ0c1dpdGggfHwgZnVuY3Rpb24gKHNlYXJjaCkgeyByZXR1cm4gdGhpcy5sYXN0SW5kZXhPZihzZWFyY2gsIDApID09PSAwOyB9O1xuXG5leHBvcnQgZnVuY3Rpb24gc3RhcnRzV2l0aChzdHIsIHNlYXJjaCkge1xuICAgIHJldHVybiBzdGFydHNXaXRoRm4uY2FsbChzdHIsIHNlYXJjaCk7XG59XG5cbmNvbnN0IGVuZHNXaXRoRm4gPSBzdHJQcm90b3R5cGUuZW5kc1dpdGggfHwgZnVuY3Rpb24gKHNlYXJjaCkgeyByZXR1cm4gdGhpcy5zdWJzdHIoLXNlYXJjaC5sZW5ndGgpID09PSBzZWFyY2g7IH07XG5cbmV4cG9ydCBmdW5jdGlvbiBlbmRzV2l0aChzdHIsIHNlYXJjaCkge1xuICAgIHJldHVybiBlbmRzV2l0aEZuLmNhbGwoc3RyLCBzZWFyY2gpO1xufVxuXG5jb25zdCBhcnJQcm90b3R5cGUgPSBBcnJheS5wcm90b3R5cGU7XG5cbmNvbnN0IGluY2x1ZGVzRm4gPSBmdW5jdGlvbiAoc2VhcmNoLCBpKSB7IHJldHVybiB+dGhpcy5pbmRleE9mKHNlYXJjaCwgaSk7IH07XG5jb25zdCBpbmNsdWRlc1N0ciA9IHN0clByb3RvdHlwZS5pbmNsdWRlcyB8fCBpbmNsdWRlc0ZuO1xuY29uc3QgaW5jbHVkZXNBcnJheSA9IGFyclByb3RvdHlwZS5pbmNsdWRlcyB8fCBpbmNsdWRlc0ZuO1xuXG5leHBvcnQgZnVuY3Rpb24gaW5jbHVkZXMob2JqLCBzZWFyY2gpIHtcbiAgICByZXR1cm4gb2JqICYmIChpc1N0cmluZyhvYmopID8gaW5jbHVkZXNTdHIgOiBpbmNsdWRlc0FycmF5KS5jYWxsKG9iaiwgc2VhcmNoKTtcbn1cblxuY29uc3QgZmluZEluZGV4Rm4gPSBhcnJQcm90b3R5cGUuZmluZEluZGV4IHx8IGZ1bmN0aW9uIChwcmVkaWNhdGUpIHtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IHRoaXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgaWYgKHByZWRpY2F0ZS5jYWxsKGFyZ3VtZW50c1sxXSwgdGhpc1tpXSwgaSwgdGhpcykpIHtcbiAgICAgICAgICAgIHJldHVybiBpO1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiAtMTtcbn07XG5cbmV4cG9ydCBmdW5jdGlvbiBmaW5kSW5kZXgoYXJyYXksIHByZWRpY2F0ZSkge1xuICAgIHJldHVybiBmaW5kSW5kZXhGbi5jYWxsKGFycmF5LCBwcmVkaWNhdGUpO1xufVxuXG5leHBvcnQgY29uc3Qge2lzQXJyYXl9ID0gQXJyYXk7XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0Z1bmN0aW9uKG9iaikge1xuICAgIHJldHVybiB0eXBlb2Ygb2JqID09PSAnZnVuY3Rpb24nO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNPYmplY3Qob2JqKSB7XG4gICAgcmV0dXJuIG9iaiAhPT0gbnVsbCAmJiB0eXBlb2Ygb2JqID09PSAnb2JqZWN0Jztcbn1cblxuY29uc3Qge3RvU3RyaW5nfSA9IG9ialByb3RvdHlwZTtcbmV4cG9ydCBmdW5jdGlvbiBpc1BsYWluT2JqZWN0KG9iaikge1xuICAgIHJldHVybiB0b1N0cmluZy5jYWxsKG9iaikgPT09ICdbb2JqZWN0IE9iamVjdF0nO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNXaW5kb3cob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgb2JqID09PSBvYmoud2luZG93O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNEb2N1bWVudChvYmopIHtcbiAgICByZXR1cm4gaXNPYmplY3Qob2JqKSAmJiBvYmoubm9kZVR5cGUgPT09IDk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0pRdWVyeShvYmopIHtcbiAgICByZXR1cm4gaXNPYmplY3Qob2JqKSAmJiAhIW9iai5qcXVlcnk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc05vZGUob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgb2JqLm5vZGVUeXBlID49IDE7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0VsZW1lbnQob2JqKSB7XG4gICAgcmV0dXJuIGlzT2JqZWN0KG9iaikgJiYgb2JqLm5vZGVUeXBlID09PSAxO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNOb2RlQ29sbGVjdGlvbihvYmopIHtcbiAgICByZXR1cm4gdG9TdHJpbmcuY2FsbChvYmopLm1hdGNoKC9eXFxbb2JqZWN0IChOb2RlTGlzdHxIVE1MQ29sbGVjdGlvbilcXF0kLyk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc0Jvb2xlYW4odmFsdWUpIHtcbiAgICByZXR1cm4gdHlwZW9mIHZhbHVlID09PSAnYm9vbGVhbic7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc1N0cmluZyh2YWx1ZSkge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdzdHJpbmcnO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNOdW1iZXIodmFsdWUpIHtcbiAgICByZXR1cm4gdHlwZW9mIHZhbHVlID09PSAnbnVtYmVyJztcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzTnVtZXJpYyh2YWx1ZSkge1xuICAgIHJldHVybiBpc051bWJlcih2YWx1ZSkgfHwgaXNTdHJpbmcodmFsdWUpICYmICFpc05hTih2YWx1ZSAtIHBhcnNlRmxvYXQodmFsdWUpKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzRW1wdHkob2JqKSB7XG4gICAgcmV0dXJuICEoaXNBcnJheShvYmopXG4gICAgICAgID8gb2JqLmxlbmd0aFxuICAgICAgICA6IGlzT2JqZWN0KG9iailcbiAgICAgICAgICAgID8gT2JqZWN0LmtleXMob2JqKS5sZW5ndGhcbiAgICAgICAgICAgIDogZmFsc2VcbiAgICApO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNVbmRlZmluZWQodmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUgPT09IHZvaWQgMDtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvQm9vbGVhbih2YWx1ZSkge1xuICAgIHJldHVybiBpc0Jvb2xlYW4odmFsdWUpXG4gICAgICAgID8gdmFsdWVcbiAgICAgICAgOiB2YWx1ZSA9PT0gJ3RydWUnIHx8IHZhbHVlID09PSAnMScgfHwgdmFsdWUgPT09ICcnXG4gICAgICAgICAgICA/IHRydWVcbiAgICAgICAgICAgIDogdmFsdWUgPT09ICdmYWxzZScgfHwgdmFsdWUgPT09ICcwJ1xuICAgICAgICAgICAgICAgID8gZmFsc2VcbiAgICAgICAgICAgICAgICA6IHZhbHVlO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9OdW1iZXIodmFsdWUpIHtcbiAgICBjb25zdCBudW1iZXIgPSBOdW1iZXIodmFsdWUpO1xuICAgIHJldHVybiAhaXNOYU4obnVtYmVyKSA/IG51bWJlciA6IGZhbHNlO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9GbG9hdCh2YWx1ZSkge1xuICAgIHJldHVybiBwYXJzZUZsb2F0KHZhbHVlKSB8fCAwO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9Ob2RlKGVsZW1lbnQpIHtcbiAgICByZXR1cm4gaXNOb2RlKGVsZW1lbnQpXG4gICAgICAgID8gZWxlbWVudFxuICAgICAgICA6IGlzTm9kZUNvbGxlY3Rpb24oZWxlbWVudCkgfHwgaXNKUXVlcnkoZWxlbWVudClcbiAgICAgICAgICAgID8gZWxlbWVudFswXVxuICAgICAgICAgICAgOiBpc0FycmF5KGVsZW1lbnQpXG4gICAgICAgICAgICAgICAgPyB0b05vZGUoZWxlbWVudFswXSlcbiAgICAgICAgICAgICAgICA6IG51bGw7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB0b05vZGVzKGVsZW1lbnQpIHtcbiAgICByZXR1cm4gaXNOb2RlKGVsZW1lbnQpXG4gICAgICAgID8gW2VsZW1lbnRdXG4gICAgICAgIDogaXNOb2RlQ29sbGVjdGlvbihlbGVtZW50KVxuICAgICAgICAgICAgPyBhcnJQcm90b3R5cGUuc2xpY2UuY2FsbChlbGVtZW50KVxuICAgICAgICAgICAgOiBpc0FycmF5KGVsZW1lbnQpXG4gICAgICAgICAgICAgICAgPyBlbGVtZW50Lm1hcCh0b05vZGUpLmZpbHRlcihCb29sZWFuKVxuICAgICAgICAgICAgICAgIDogaXNKUXVlcnkoZWxlbWVudClcbiAgICAgICAgICAgICAgICAgICAgPyBlbGVtZW50LnRvQXJyYXkoKVxuICAgICAgICAgICAgICAgICAgICA6IFtdO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9XaW5kb3coZWxlbWVudCkge1xuICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSkge1xuICAgICAgICByZXR1cm4gZWxlbWVudDtcbiAgICB9XG5cbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuXG4gICAgcmV0dXJuIGVsZW1lbnRcbiAgICAgICAgPyAoaXNEb2N1bWVudChlbGVtZW50KVxuICAgICAgICAgICAgPyBlbGVtZW50XG4gICAgICAgICAgICA6IGVsZW1lbnQub3duZXJEb2N1bWVudFxuICAgICAgICApLmRlZmF1bHRWaWV3XG4gICAgICAgIDogd2luZG93O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdG9MaXN0KHZhbHVlKSB7XG4gICAgcmV0dXJuIGlzQXJyYXkodmFsdWUpXG4gICAgICAgID8gdmFsdWVcbiAgICAgICAgOiBpc1N0cmluZyh2YWx1ZSlcbiAgICAgICAgICAgID8gdmFsdWUuc3BsaXQoLywoPyFbXihdKlxcKSkvKS5tYXAodmFsdWUgPT4gaXNOdW1lcmljKHZhbHVlKVxuICAgICAgICAgICAgICAgID8gdG9OdW1iZXIodmFsdWUpXG4gICAgICAgICAgICAgICAgOiB0b0Jvb2xlYW4odmFsdWUudHJpbSgpKSlcbiAgICAgICAgICAgIDogW3ZhbHVlXTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvTXModGltZSkge1xuICAgIHJldHVybiAhdGltZVxuICAgICAgICA/IDBcbiAgICAgICAgOiBlbmRzV2l0aCh0aW1lLCAnbXMnKVxuICAgICAgICAgICAgPyB0b0Zsb2F0KHRpbWUpXG4gICAgICAgICAgICA6IHRvRmxvYXQodGltZSkgKiAxMDAwO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaXNFcXVhbCh2YWx1ZSwgb3RoZXIpIHtcbiAgICByZXR1cm4gdmFsdWUgPT09IG90aGVyXG4gICAgICAgIHx8IGlzT2JqZWN0KHZhbHVlKVxuICAgICAgICAmJiBpc09iamVjdChvdGhlcilcbiAgICAgICAgJiYgT2JqZWN0LmtleXModmFsdWUpLmxlbmd0aCA9PT0gT2JqZWN0LmtleXMob3RoZXIpLmxlbmd0aFxuICAgICAgICAmJiBlYWNoKHZhbHVlLCAodmFsLCBrZXkpID0+IHZhbCA9PT0gb3RoZXJba2V5XSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBzd2FwKHZhbHVlLCBhLCBiKSB7XG4gICAgcmV0dXJuIHZhbHVlLnJlcGxhY2UobmV3IFJlZ0V4cChgJHthfXwke2J9YCwgJ21nJyksIG1hdGNoID0+IHtcbiAgICAgICAgcmV0dXJuIG1hdGNoID09PSBhID8gYiA6IGE7XG4gICAgfSk7XG59XG5cbmV4cG9ydCBjb25zdCBhc3NpZ24gPSBPYmplY3QuYXNzaWduIHx8IGZ1bmN0aW9uICh0YXJnZXQsIC4uLmFyZ3MpIHtcbiAgICB0YXJnZXQgPSBPYmplY3QodGFyZ2V0KTtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IGFyZ3MubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgY29uc3Qgc291cmNlID0gYXJnc1tpXTtcbiAgICAgICAgaWYgKHNvdXJjZSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gc291cmNlKSB7XG4gICAgICAgICAgICAgICAgaWYgKGhhc093bihzb3VyY2UsIGtleSkpIHtcbiAgICAgICAgICAgICAgICAgICAgdGFyZ2V0W2tleV0gPSBzb3VyY2Vba2V5XTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHRhcmdldDtcbn07XG5cbmV4cG9ydCBmdW5jdGlvbiBsYXN0KGFycmF5KSB7XG4gICAgcmV0dXJuIGFycmF5W2FycmF5Lmxlbmd0aCAtIDFdO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZWFjaChvYmosIGNiKSB7XG4gICAgZm9yIChjb25zdCBrZXkgaW4gb2JqKSB7XG4gICAgICAgIGlmIChmYWxzZSA9PT0gY2Iob2JqW2tleV0sIGtleSkpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHNvcnRCeShhcnJheSwgcHJvcCkge1xuICAgIHJldHVybiBhcnJheS5zb3J0KCh7W3Byb3BdOiBwcm9wQSA9IDB9LCB7W3Byb3BdOiBwcm9wQiA9IDB9KSA9PlxuICAgICAgICBwcm9wQSA+IHByb3BCXG4gICAgICAgICAgICA/IDFcbiAgICAgICAgICAgIDogcHJvcEIgPiBwcm9wQVxuICAgICAgICAgICAgICAgID8gLTFcbiAgICAgICAgICAgICAgICA6IDBcbiAgICApO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdW5pcXVlQnkoYXJyYXksIHByb3ApIHtcbiAgICBjb25zdCBzZWVuID0gbmV3IFNldCgpO1xuICAgIHJldHVybiBhcnJheS5maWx0ZXIoKHtbcHJvcF06IGNoZWNrfSkgPT4gc2Vlbi5oYXMoY2hlY2spXG4gICAgICAgID8gZmFsc2VcbiAgICAgICAgOiBzZWVuLmFkZChjaGVjaykgfHwgdHJ1ZSAvLyBJRSAxMSBkb2VzIG5vdCByZXR1cm4gdGhlIFNldCBvYmplY3RcbiAgICApO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gY2xhbXAobnVtYmVyLCBtaW4gPSAwLCBtYXggPSAxKSB7XG4gICAgcmV0dXJuIE1hdGgubWluKE1hdGgubWF4KHRvTnVtYmVyKG51bWJlcikgfHwgMCwgbWluKSwgbWF4KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG5vb3AoKSB7fVxuXG5leHBvcnQgZnVuY3Rpb24gaW50ZXJzZWN0UmVjdChyMSwgcjIpIHtcbiAgICByZXR1cm4gcjEubGVmdCA8IHIyLnJpZ2h0ICYmXG4gICAgICAgIHIxLnJpZ2h0ID4gcjIubGVmdCAmJlxuICAgICAgICByMS50b3AgPCByMi5ib3R0b20gJiZcbiAgICAgICAgcjEuYm90dG9tID4gcjIudG9wO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcG9pbnRJblJlY3QocG9pbnQsIHJlY3QpIHtcbiAgICByZXR1cm4gcG9pbnQueCA8PSByZWN0LnJpZ2h0ICYmXG4gICAgICAgIHBvaW50LnggPj0gcmVjdC5sZWZ0ICYmXG4gICAgICAgIHBvaW50LnkgPD0gcmVjdC5ib3R0b20gJiZcbiAgICAgICAgcG9pbnQueSA+PSByZWN0LnRvcDtcbn1cblxuZXhwb3J0IGNvbnN0IERpbWVuc2lvbnMgPSB7XG5cbiAgICByYXRpbyhkaW1lbnNpb25zLCBwcm9wLCB2YWx1ZSkge1xuXG4gICAgICAgIGNvbnN0IGFQcm9wID0gcHJvcCA9PT0gJ3dpZHRoJyA/ICdoZWlnaHQnIDogJ3dpZHRoJztcblxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgW2FQcm9wXTogZGltZW5zaW9uc1twcm9wXSA/IE1hdGgucm91bmQodmFsdWUgKiBkaW1lbnNpb25zW2FQcm9wXSAvIGRpbWVuc2lvbnNbcHJvcF0pIDogZGltZW5zaW9uc1thUHJvcF0sXG4gICAgICAgICAgICBbcHJvcF06IHZhbHVlXG4gICAgICAgIH07XG4gICAgfSxcblxuICAgIGNvbnRhaW4oZGltZW5zaW9ucywgbWF4RGltZW5zaW9ucykge1xuICAgICAgICBkaW1lbnNpb25zID0gYXNzaWduKHt9LCBkaW1lbnNpb25zKTtcblxuICAgICAgICBlYWNoKGRpbWVuc2lvbnMsIChfLCBwcm9wKSA9PiBkaW1lbnNpb25zID0gZGltZW5zaW9uc1twcm9wXSA+IG1heERpbWVuc2lvbnNbcHJvcF1cbiAgICAgICAgICAgID8gdGhpcy5yYXRpbyhkaW1lbnNpb25zLCBwcm9wLCBtYXhEaW1lbnNpb25zW3Byb3BdKVxuICAgICAgICAgICAgOiBkaW1lbnNpb25zXG4gICAgICAgICk7XG5cbiAgICAgICAgcmV0dXJuIGRpbWVuc2lvbnM7XG4gICAgfSxcblxuICAgIGNvdmVyKGRpbWVuc2lvbnMsIG1heERpbWVuc2lvbnMpIHtcbiAgICAgICAgZGltZW5zaW9ucyA9IHRoaXMuY29udGFpbihkaW1lbnNpb25zLCBtYXhEaW1lbnNpb25zKTtcblxuICAgICAgICBlYWNoKGRpbWVuc2lvbnMsIChfLCBwcm9wKSA9PiBkaW1lbnNpb25zID0gZGltZW5zaW9uc1twcm9wXSA8IG1heERpbWVuc2lvbnNbcHJvcF1cbiAgICAgICAgICAgID8gdGhpcy5yYXRpbyhkaW1lbnNpb25zLCBwcm9wLCBtYXhEaW1lbnNpb25zW3Byb3BdKVxuICAgICAgICAgICAgOiBkaW1lbnNpb25zXG4gICAgICAgICk7XG5cbiAgICAgICAgcmV0dXJuIGRpbWVuc2lvbnM7XG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHtpc0Z1bmN0aW9uLCBpc09iamVjdCwgaXNVbmRlZmluZWQsIHRvTm9kZSwgdG9Ob2Rlc30gZnJvbSAnLi9sYW5nJztcblxuZXhwb3J0IGZ1bmN0aW9uIGF0dHIoZWxlbWVudCwgbmFtZSwgdmFsdWUpIHtcblxuICAgIGlmIChpc09iamVjdChuYW1lKSkge1xuICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBuYW1lKSB7XG4gICAgICAgICAgICBhdHRyKGVsZW1lbnQsIGtleSwgbmFtZVtrZXldKTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKGlzVW5kZWZpbmVkKHZhbHVlKSkge1xuICAgICAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuICAgICAgICByZXR1cm4gZWxlbWVudCAmJiBlbGVtZW50LmdldEF0dHJpYnV0ZShuYW1lKTtcbiAgICB9IGVsc2Uge1xuICAgICAgICB0b05vZGVzKGVsZW1lbnQpLmZvckVhY2goZWxlbWVudCA9PiB7XG5cbiAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKHZhbHVlKSkge1xuICAgICAgICAgICAgICAgIHZhbHVlID0gdmFsdWUuY2FsbChlbGVtZW50LCBhdHRyKGVsZW1lbnQsIG5hbWUpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKHZhbHVlID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQXR0cihlbGVtZW50LCBuYW1lKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgZWxlbWVudC5zZXRBdHRyaWJ1dGUobmFtZSwgdmFsdWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9XG5cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGhhc0F0dHIoZWxlbWVudCwgbmFtZSkge1xuICAgIHJldHVybiB0b05vZGVzKGVsZW1lbnQpLnNvbWUoZWxlbWVudCA9PiBlbGVtZW50Lmhhc0F0dHJpYnV0ZShuYW1lKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiByZW1vdmVBdHRyKGVsZW1lbnQsIG5hbWUpIHtcbiAgICBlbGVtZW50ID0gdG9Ob2RlcyhlbGVtZW50KTtcbiAgICBuYW1lLnNwbGl0KCcgJykuZm9yRWFjaChuYW1lID0+XG4gICAgICAgIGVsZW1lbnQuZm9yRWFjaChlbGVtZW50ID0+XG4gICAgICAgICAgICBlbGVtZW50Lmhhc0F0dHJpYnV0ZShuYW1lKSAmJiBlbGVtZW50LnJlbW92ZUF0dHJpYnV0ZShuYW1lKVxuICAgICAgICApXG4gICAgKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGRhdGEoZWxlbWVudCwgYXR0cmlidXRlKSB7XG4gICAgZm9yIChsZXQgaSA9IDAsIGF0dHJzID0gW2F0dHJpYnV0ZSwgYGRhdGEtJHthdHRyaWJ1dGV9YF07IGkgPCBhdHRycy5sZW5ndGg7IGkrKykge1xuICAgICAgICBpZiAoaGFzQXR0cihlbGVtZW50LCBhdHRyc1tpXSkpIHtcbiAgICAgICAgICAgIHJldHVybiBhdHRyKGVsZW1lbnQsIGF0dHJzW2ldKTtcbiAgICAgICAgfVxuICAgIH1cbn1cbiIsIi8qIGdsb2JhbCBEb2N1bWVudFRvdWNoICovXG5pbXBvcnQge2F0dHJ9IGZyb20gJy4vYXR0cic7XG5cbmV4cG9ydCBjb25zdCBpc0lFID0gL21zaWV8dHJpZGVudC9pLnRlc3Qod2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQpO1xuZXhwb3J0IGNvbnN0IGlzUnRsID0gYXR0cihkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQsICdkaXInKSA9PT0gJ3J0bCc7XG5cbmNvbnN0IGhhc1RvdWNoRXZlbnRzID0gJ29udG91Y2hzdGFydCcgaW4gd2luZG93O1xuY29uc3QgaGFzUG9pbnRlckV2ZW50cyA9IHdpbmRvdy5Qb2ludGVyRXZlbnQ7XG5leHBvcnQgY29uc3QgaGFzVG91Y2ggPSBoYXNUb3VjaEV2ZW50c1xuICAgIHx8IHdpbmRvdy5Eb2N1bWVudFRvdWNoICYmIGRvY3VtZW50IGluc3RhbmNlb2YgRG9jdW1lbnRUb3VjaFxuICAgIHx8IG5hdmlnYXRvci5tYXhUb3VjaFBvaW50czsgLy8gSUUgPj0xMVxuXG5leHBvcnQgY29uc3QgcG9pbnRlckRvd24gPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJkb3duJyA6IGhhc1RvdWNoRXZlbnRzID8gJ3RvdWNoc3RhcnQnIDogJ21vdXNlZG93bic7XG5leHBvcnQgY29uc3QgcG9pbnRlck1vdmUgPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJtb3ZlJyA6IGhhc1RvdWNoRXZlbnRzID8gJ3RvdWNobW92ZScgOiAnbW91c2Vtb3ZlJztcbmV4cG9ydCBjb25zdCBwb2ludGVyVXAgPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJ1cCcgOiBoYXNUb3VjaEV2ZW50cyA/ICd0b3VjaGVuZCcgOiAnbW91c2V1cCc7XG5leHBvcnQgY29uc3QgcG9pbnRlckVudGVyID0gaGFzUG9pbnRlckV2ZW50cyA/ICdwb2ludGVyZW50ZXInIDogaGFzVG91Y2hFdmVudHMgPyAnJyA6ICdtb3VzZWVudGVyJztcbmV4cG9ydCBjb25zdCBwb2ludGVyTGVhdmUgPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJsZWF2ZScgOiBoYXNUb3VjaEV2ZW50cyA/ICcnIDogJ21vdXNlbGVhdmUnO1xuZXhwb3J0IGNvbnN0IHBvaW50ZXJDYW5jZWwgPSBoYXNQb2ludGVyRXZlbnRzID8gJ3BvaW50ZXJjYW5jZWwnIDogJ3RvdWNoY2FuY2VsJztcbiIsImltcG9ydCB7cmVtb3ZlQXR0cn0gZnJvbSAnLi9hdHRyJztcbmltcG9ydCB7aXNEb2N1bWVudCwgaXNFbGVtZW50LCBpc1N0cmluZywgc3RhcnRzV2l0aCwgdG9Ob2RlLCB0b05vZGVzfSBmcm9tICcuL2xhbmcnO1xuXG5leHBvcnQgZnVuY3Rpb24gcXVlcnkoc2VsZWN0b3IsIGNvbnRleHQpIHtcbiAgICByZXR1cm4gdG9Ob2RlKHNlbGVjdG9yKSB8fCBmaW5kKHNlbGVjdG9yLCBnZXRDb250ZXh0KHNlbGVjdG9yLCBjb250ZXh0KSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBxdWVyeUFsbChzZWxlY3RvciwgY29udGV4dCkge1xuICAgIGNvbnN0IG5vZGVzID0gdG9Ob2RlcyhzZWxlY3Rvcik7XG4gICAgcmV0dXJuIG5vZGVzLmxlbmd0aCAmJiBub2RlcyB8fCBmaW5kQWxsKHNlbGVjdG9yLCBnZXRDb250ZXh0KHNlbGVjdG9yLCBjb250ZXh0KSk7XG59XG5cbmZ1bmN0aW9uIGdldENvbnRleHQoc2VsZWN0b3IsIGNvbnRleHQgPSBkb2N1bWVudCkge1xuICAgIHJldHVybiBpc0NvbnRleHRTZWxlY3RvcihzZWxlY3RvcikgfHwgaXNEb2N1bWVudChjb250ZXh0KVxuICAgICAgICA/IGNvbnRleHRcbiAgICAgICAgOiBjb250ZXh0Lm93bmVyRG9jdW1lbnQ7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBmaW5kKHNlbGVjdG9yLCBjb250ZXh0KSB7XG4gICAgcmV0dXJuIHRvTm9kZShfcXVlcnkoc2VsZWN0b3IsIGNvbnRleHQsICdxdWVyeVNlbGVjdG9yJykpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZmluZEFsbChzZWxlY3RvciwgY29udGV4dCkge1xuICAgIHJldHVybiB0b05vZGVzKF9xdWVyeShzZWxlY3RvciwgY29udGV4dCwgJ3F1ZXJ5U2VsZWN0b3JBbGwnKSk7XG59XG5cbmZ1bmN0aW9uIF9xdWVyeShzZWxlY3RvciwgY29udGV4dCA9IGRvY3VtZW50LCBxdWVyeUZuKSB7XG5cbiAgICBpZiAoIXNlbGVjdG9yIHx8ICFpc1N0cmluZyhzZWxlY3RvcikpIHtcbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuXG4gICAgc2VsZWN0b3IgPSBzZWxlY3Rvci5yZXBsYWNlKGNvbnRleHRTYW5pdGl6ZVJlLCAnJDEgKicpO1xuXG4gICAgbGV0IHJlbW92ZXM7XG5cbiAgICBpZiAoaXNDb250ZXh0U2VsZWN0b3Ioc2VsZWN0b3IpKSB7XG5cbiAgICAgICAgcmVtb3ZlcyA9IFtdO1xuXG4gICAgICAgIHNlbGVjdG9yID0gc3BsaXRTZWxlY3RvcihzZWxlY3RvcikubWFwKChzZWxlY3RvciwgaSkgPT4ge1xuXG4gICAgICAgICAgICBsZXQgY3R4ID0gY29udGV4dDtcblxuICAgICAgICAgICAgaWYgKHNlbGVjdG9yWzBdID09PSAnIScpIHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHNlbGVjdG9ycyA9IHNlbGVjdG9yLnN1YnN0cigxKS50cmltKCkuc3BsaXQoJyAnKTtcbiAgICAgICAgICAgICAgICBjdHggPSBjbG9zZXN0KHBhcmVudChjb250ZXh0KSwgc2VsZWN0b3JzWzBdKTtcbiAgICAgICAgICAgICAgICBzZWxlY3RvciA9IHNlbGVjdG9ycy5zbGljZSgxKS5qb2luKCcgJykudHJpbSgpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChzZWxlY3RvclswXSA9PT0gJy0nKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBzZWxlY3RvcnMgPSBzZWxlY3Rvci5zdWJzdHIoMSkudHJpbSgpLnNwbGl0KCcgJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgcHJldiA9IChjdHggfHwgY29udGV4dCkucHJldmlvdXNFbGVtZW50U2libGluZztcbiAgICAgICAgICAgICAgICBjdHggPSBtYXRjaGVzKHByZXYsIHNlbGVjdG9yLnN1YnN0cigxKSkgPyBwcmV2IDogbnVsbDtcbiAgICAgICAgICAgICAgICBzZWxlY3RvciA9IHNlbGVjdG9ycy5zbGljZSgxKS5qb2luKCcgJyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFjdHgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFjdHguaWQpIHtcbiAgICAgICAgICAgICAgICBjdHguaWQgPSBgdWstJHtEYXRlLm5vdygpfSR7aX1gO1xuICAgICAgICAgICAgICAgIHJlbW92ZXMucHVzaCgoKSA9PiByZW1vdmVBdHRyKGN0eCwgJ2lkJykpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gYCMke2VzY2FwZShjdHguaWQpfSAke3NlbGVjdG9yfWA7XG5cbiAgICAgICAgfSkuZmlsdGVyKEJvb2xlYW4pLmpvaW4oJywnKTtcblxuICAgICAgICBjb250ZXh0ID0gZG9jdW1lbnQ7XG5cbiAgICB9XG5cbiAgICB0cnkge1xuXG4gICAgICAgIHJldHVybiBjb250ZXh0W3F1ZXJ5Rm5dKHNlbGVjdG9yKTtcblxuICAgIH0gY2F0Y2ggKGUpIHtcblxuICAgICAgICByZXR1cm4gbnVsbDtcblxuICAgIH0gZmluYWxseSB7XG5cbiAgICAgICAgcmVtb3ZlcyAmJiByZW1vdmVzLmZvckVhY2gocmVtb3ZlID0+IHJlbW92ZSgpKTtcblxuICAgIH1cblxufVxuXG5jb25zdCBjb250ZXh0U2VsZWN0b3JSZSA9IC8oXnxbXlxcXFxdLClcXHMqWyE+K34tXS87XG5jb25zdCBjb250ZXh0U2FuaXRpemVSZSA9IC8oWyE+K34tXSkoPz1cXHMrWyE+K34tXXxcXHMqJCkvZztcblxuZnVuY3Rpb24gaXNDb250ZXh0U2VsZWN0b3Ioc2VsZWN0b3IpIHtcbiAgICByZXR1cm4gaXNTdHJpbmcoc2VsZWN0b3IpICYmIHNlbGVjdG9yLm1hdGNoKGNvbnRleHRTZWxlY3RvclJlKTtcbn1cblxuY29uc3Qgc2VsZWN0b3JSZSA9IC8uKj9bXlxcXFxdKD86LHwkKS9nO1xuXG5mdW5jdGlvbiBzcGxpdFNlbGVjdG9yKHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuIHNlbGVjdG9yLm1hdGNoKHNlbGVjdG9yUmUpLm1hcChzZWxlY3RvciA9PiBzZWxlY3Rvci5yZXBsYWNlKC8sJC8sICcnKS50cmltKCkpO1xufVxuXG5jb25zdCBlbFByb3RvID0gRWxlbWVudC5wcm90b3R5cGU7XG5jb25zdCBtYXRjaGVzRm4gPSBlbFByb3RvLm1hdGNoZXMgfHwgZWxQcm90by53ZWJraXRNYXRjaGVzU2VsZWN0b3IgfHwgZWxQcm90by5tc01hdGNoZXNTZWxlY3RvcjtcblxuZXhwb3J0IGZ1bmN0aW9uIG1hdGNoZXMoZWxlbWVudCwgc2VsZWN0b3IpIHtcbiAgICByZXR1cm4gdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gbWF0Y2hlc0ZuLmNhbGwoZWxlbWVudCwgc2VsZWN0b3IpKTtcbn1cblxuY29uc3QgY2xvc2VzdEZuID0gZWxQcm90by5jbG9zZXN0IHx8IGZ1bmN0aW9uIChzZWxlY3Rvcikge1xuICAgIGxldCBhbmNlc3RvciA9IHRoaXM7XG5cbiAgICBkbyB7XG5cbiAgICAgICAgaWYgKG1hdGNoZXMoYW5jZXN0b3IsIHNlbGVjdG9yKSkge1xuICAgICAgICAgICAgcmV0dXJuIGFuY2VzdG9yO1xuICAgICAgICB9XG5cbiAgICB9IHdoaWxlICgoYW5jZXN0b3IgPSBwYXJlbnQoYW5jZXN0b3IpKSk7XG59O1xuXG5leHBvcnQgZnVuY3Rpb24gY2xvc2VzdChlbGVtZW50LCBzZWxlY3Rvcikge1xuXG4gICAgaWYgKHN0YXJ0c1dpdGgoc2VsZWN0b3IsICc+JykpIHtcbiAgICAgICAgc2VsZWN0b3IgPSBzZWxlY3Rvci5zbGljZSgxKTtcbiAgICB9XG5cbiAgICByZXR1cm4gaXNFbGVtZW50KGVsZW1lbnQpXG4gICAgICAgID8gY2xvc2VzdEZuLmNhbGwoZWxlbWVudCwgc2VsZWN0b3IpXG4gICAgICAgIDogdG9Ob2RlcyhlbGVtZW50KS5tYXAoZWxlbWVudCA9PiBjbG9zZXN0KGVsZW1lbnQsIHNlbGVjdG9yKSkuZmlsdGVyKEJvb2xlYW4pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcGFyZW50KGVsZW1lbnQpIHtcbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuICAgIHJldHVybiBlbGVtZW50ICYmIGlzRWxlbWVudChlbGVtZW50LnBhcmVudE5vZGUpICYmIGVsZW1lbnQucGFyZW50Tm9kZTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHBhcmVudHMoZWxlbWVudCwgc2VsZWN0b3IpIHtcbiAgICBjb25zdCBlbGVtZW50cyA9IFtdO1xuXG4gICAgd2hpbGUgKChlbGVtZW50ID0gcGFyZW50KGVsZW1lbnQpKSkge1xuICAgICAgICBpZiAoIXNlbGVjdG9yIHx8IG1hdGNoZXMoZWxlbWVudCwgc2VsZWN0b3IpKSB7XG4gICAgICAgICAgICBlbGVtZW50cy5wdXNoKGVsZW1lbnQpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIGVsZW1lbnRzO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gY2hpbGRyZW4oZWxlbWVudCwgc2VsZWN0b3IpIHtcbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuICAgIGNvbnN0IGNoaWxkcmVuID0gZWxlbWVudCA/IHRvTm9kZXMoZWxlbWVudC5jaGlsZHJlbikgOiBbXTtcbiAgICByZXR1cm4gc2VsZWN0b3IgPyBjaGlsZHJlbi5maWx0ZXIoZWxlbWVudCA9PiBtYXRjaGVzKGVsZW1lbnQsIHNlbGVjdG9yKSkgOiBjaGlsZHJlbjtcbn1cblxuY29uc3QgZXNjYXBlRm4gPSB3aW5kb3cuQ1NTICYmIENTUy5lc2NhcGUgfHwgZnVuY3Rpb24gKGNzcykgeyByZXR1cm4gY3NzLnJlcGxhY2UoLyhbXlxceDdmLVxcdUZGRkZcXHctXSkvZywgbWF0Y2ggPT4gYFxcXFwke21hdGNofWApOyB9O1xuZXhwb3J0IGZ1bmN0aW9uIGVzY2FwZShjc3MpIHtcbiAgICByZXR1cm4gaXNTdHJpbmcoY3NzKSA/IGVzY2FwZUZuLmNhbGwobnVsbCwgY3NzKSA6ICcnO1xufVxuIiwiaW1wb3J0IHtjbG9zZXN0LCBtYXRjaGVzfSBmcm9tICcuL3NlbGVjdG9yJztcbmltcG9ydCB7aXNEb2N1bWVudCwgaXNTdHJpbmcsIHRvTm9kZSwgdG9Ob2Rlc30gZnJvbSAnLi9sYW5nJztcblxuY29uc3Qgdm9pZEVsZW1lbnRzID0ge1xuICAgIGFyZWE6IHRydWUsXG4gICAgYmFzZTogdHJ1ZSxcbiAgICBicjogdHJ1ZSxcbiAgICBjb2w6IHRydWUsXG4gICAgZW1iZWQ6IHRydWUsXG4gICAgaHI6IHRydWUsXG4gICAgaW1nOiB0cnVlLFxuICAgIGlucHV0OiB0cnVlLFxuICAgIGtleWdlbjogdHJ1ZSxcbiAgICBsaW5rOiB0cnVlLFxuICAgIG1lbnVpdGVtOiB0cnVlLFxuICAgIG1ldGE6IHRydWUsXG4gICAgcGFyYW06IHRydWUsXG4gICAgc291cmNlOiB0cnVlLFxuICAgIHRyYWNrOiB0cnVlLFxuICAgIHdicjogdHJ1ZVxufTtcbmV4cG9ydCBmdW5jdGlvbiBpc1ZvaWRFbGVtZW50KGVsZW1lbnQpIHtcbiAgICByZXR1cm4gdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gdm9pZEVsZW1lbnRzW2VsZW1lbnQudGFnTmFtZS50b0xvd2VyQ2FzZSgpXSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBpc1Zpc2libGUoZWxlbWVudCkge1xuICAgIHJldHVybiB0b05vZGVzKGVsZW1lbnQpLnNvbWUoZWxlbWVudCA9PiBlbGVtZW50Lm9mZnNldFdpZHRoIHx8IGVsZW1lbnQub2Zmc2V0SGVpZ2h0IHx8IGVsZW1lbnQuZ2V0Q2xpZW50UmVjdHMoKS5sZW5ndGgpO1xufVxuXG5leHBvcnQgY29uc3Qgc2VsSW5wdXQgPSAnaW5wdXQsc2VsZWN0LHRleHRhcmVhLGJ1dHRvbic7XG5leHBvcnQgZnVuY3Rpb24gaXNJbnB1dChlbGVtZW50KSB7XG4gICAgcmV0dXJuIHRvTm9kZXMoZWxlbWVudCkuc29tZShlbGVtZW50ID0+IG1hdGNoZXMoZWxlbWVudCwgc2VsSW5wdXQpKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGZpbHRlcihlbGVtZW50LCBzZWxlY3Rvcikge1xuICAgIHJldHVybiB0b05vZGVzKGVsZW1lbnQpLmZpbHRlcihlbGVtZW50ID0+IG1hdGNoZXMoZWxlbWVudCwgc2VsZWN0b3IpKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHdpdGhpbihlbGVtZW50LCBzZWxlY3Rvcikge1xuICAgIHJldHVybiAhaXNTdHJpbmcoc2VsZWN0b3IpXG4gICAgICAgID8gZWxlbWVudCA9PT0gc2VsZWN0b3IgfHwgKGlzRG9jdW1lbnQoc2VsZWN0b3IpXG4gICAgICAgICAgICA/IHNlbGVjdG9yLmRvY3VtZW50RWxlbWVudFxuICAgICAgICAgICAgOiB0b05vZGUoc2VsZWN0b3IpKS5jb250YWlucyh0b05vZGUoZWxlbWVudCkpIC8vIElFIDExIGRvY3VtZW50IGRvZXMgbm90IGltcGxlbWVudCBjb250YWluc1xuICAgICAgICA6IG1hdGNoZXMoZWxlbWVudCwgc2VsZWN0b3IpIHx8IGNsb3Nlc3QoZWxlbWVudCwgc2VsZWN0b3IpO1xufVxuIiwiaW1wb3J0IHtpc0lFfSBmcm9tICcuL2Vudic7XG5pbXBvcnQge3dpdGhpbn0gZnJvbSAnLi9maWx0ZXInO1xuaW1wb3J0IHtjbG9zZXN0LCBmaW5kQWxsfSBmcm9tICcuL3NlbGVjdG9yJztcbmltcG9ydCB7aXNBcnJheSwgaXNCb29sZWFuLCBpc0Z1bmN0aW9uLCBpc1N0cmluZywgdG9Ob2RlLCB0b05vZGVzfSBmcm9tICcuL2xhbmcnO1xuXG5leHBvcnQgZnVuY3Rpb24gb24oLi4uYXJncykge1xuXG4gICAgbGV0IFt0YXJnZXRzLCB0eXBlLCBzZWxlY3RvciwgbGlzdGVuZXIsIHVzZUNhcHR1cmVdID0gZ2V0QXJncyhhcmdzKTtcblxuICAgIHRhcmdldHMgPSB0b0V2ZW50VGFyZ2V0cyh0YXJnZXRzKTtcblxuICAgIGlmIChsaXN0ZW5lci5sZW5ndGggPiAxKSB7XG4gICAgICAgIGxpc3RlbmVyID0gZGV0YWlsKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICBpZiAodXNlQ2FwdHVyZSAmJiB1c2VDYXB0dXJlLnNlbGYpIHtcbiAgICAgICAgbGlzdGVuZXIgPSBzZWxmRmlsdGVyKGxpc3RlbmVyKTtcbiAgICB9XG5cbiAgICBpZiAoc2VsZWN0b3IpIHtcbiAgICAgICAgbGlzdGVuZXIgPSBkZWxlZ2F0ZSh0YXJnZXRzLCBzZWxlY3RvciwgbGlzdGVuZXIpO1xuICAgIH1cblxuICAgIHVzZUNhcHR1cmUgPSB1c2VDYXB0dXJlRmlsdGVyKHVzZUNhcHR1cmUpO1xuXG4gICAgdHlwZS5zcGxpdCgnICcpLmZvckVhY2godHlwZSA9PlxuICAgICAgICB0YXJnZXRzLmZvckVhY2godGFyZ2V0ID0+XG4gICAgICAgICAgICB0YXJnZXQuYWRkRXZlbnRMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lciwgdXNlQ2FwdHVyZSlcbiAgICAgICAgKVxuICAgICk7XG4gICAgcmV0dXJuICgpID0+IG9mZih0YXJnZXRzLCB0eXBlLCBsaXN0ZW5lciwgdXNlQ2FwdHVyZSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvZmYodGFyZ2V0cywgdHlwZSwgbGlzdGVuZXIsIHVzZUNhcHR1cmUgPSBmYWxzZSkge1xuICAgIHVzZUNhcHR1cmUgPSB1c2VDYXB0dXJlRmlsdGVyKHVzZUNhcHR1cmUpO1xuICAgIHRhcmdldHMgPSB0b0V2ZW50VGFyZ2V0cyh0YXJnZXRzKTtcbiAgICB0eXBlLnNwbGl0KCcgJykuZm9yRWFjaCh0eXBlID0+XG4gICAgICAgIHRhcmdldHMuZm9yRWFjaCh0YXJnZXQgPT5cbiAgICAgICAgICAgIHRhcmdldC5yZW1vdmVFdmVudExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyLCB1c2VDYXB0dXJlKVxuICAgICAgICApXG4gICAgKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG9uY2UoLi4uYXJncykge1xuXG4gICAgY29uc3QgW2VsZW1lbnQsIHR5cGUsIHNlbGVjdG9yLCBsaXN0ZW5lciwgdXNlQ2FwdHVyZSwgY29uZGl0aW9uXSA9IGdldEFyZ3MoYXJncyk7XG4gICAgY29uc3Qgb2ZmID0gb24oZWxlbWVudCwgdHlwZSwgc2VsZWN0b3IsIGUgPT4ge1xuICAgICAgICBjb25zdCByZXN1bHQgPSAhY29uZGl0aW9uIHx8IGNvbmRpdGlvbihlKTtcbiAgICAgICAgaWYgKHJlc3VsdCkge1xuICAgICAgICAgICAgb2ZmKCk7XG4gICAgICAgICAgICBsaXN0ZW5lcihlLCByZXN1bHQpO1xuICAgICAgICB9XG4gICAgfSwgdXNlQ2FwdHVyZSk7XG5cbiAgICByZXR1cm4gb2ZmO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gdHJpZ2dlcih0YXJnZXRzLCBldmVudCwgZGV0YWlsKSB7XG4gICAgcmV0dXJuIHRvRXZlbnRUYXJnZXRzKHRhcmdldHMpLnJlZHVjZSgobm90Q2FuY2VsZWQsIHRhcmdldCkgPT5cbiAgICAgICAgbm90Q2FuY2VsZWQgJiYgdGFyZ2V0LmRpc3BhdGNoRXZlbnQoY3JlYXRlRXZlbnQoZXZlbnQsIHRydWUsIHRydWUsIGRldGFpbCkpXG4gICAgICAgICwgdHJ1ZSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBjcmVhdGVFdmVudChlLCBidWJibGVzID0gdHJ1ZSwgY2FuY2VsYWJsZSA9IGZhbHNlLCBkZXRhaWwpIHtcbiAgICBpZiAoaXNTdHJpbmcoZSkpIHtcbiAgICAgICAgY29uc3QgZXZlbnQgPSBkb2N1bWVudC5jcmVhdGVFdmVudCgnQ3VzdG9tRXZlbnQnKTsgLy8gSUUgMTFcbiAgICAgICAgZXZlbnQuaW5pdEN1c3RvbUV2ZW50KGUsIGJ1YmJsZXMsIGNhbmNlbGFibGUsIGRldGFpbCk7XG4gICAgICAgIGUgPSBldmVudDtcbiAgICB9XG5cbiAgICByZXR1cm4gZTtcbn1cblxuZnVuY3Rpb24gZ2V0QXJncyhhcmdzKSB7XG4gICAgaWYgKGlzRnVuY3Rpb24oYXJnc1syXSkpIHtcbiAgICAgICAgYXJncy5zcGxpY2UoMiwgMCwgZmFsc2UpO1xuICAgIH1cbiAgICByZXR1cm4gYXJncztcbn1cblxuZnVuY3Rpb24gZGVsZWdhdGUoZGVsZWdhdGVzLCBzZWxlY3RvciwgbGlzdGVuZXIpIHtcbiAgICByZXR1cm4gZSA9PiB7XG5cbiAgICAgICAgZGVsZWdhdGVzLmZvckVhY2goZGVsZWdhdGUgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBjdXJyZW50ID0gc2VsZWN0b3JbMF0gPT09ICc+J1xuICAgICAgICAgICAgICAgID8gZmluZEFsbChzZWxlY3RvciwgZGVsZWdhdGUpLnJldmVyc2UoKS5maWx0ZXIoZWxlbWVudCA9PiB3aXRoaW4oZS50YXJnZXQsIGVsZW1lbnQpKVswXVxuICAgICAgICAgICAgICAgIDogY2xvc2VzdChlLnRhcmdldCwgc2VsZWN0b3IpO1xuXG4gICAgICAgICAgICBpZiAoY3VycmVudCkge1xuICAgICAgICAgICAgICAgIGUuZGVsZWdhdGUgPSBkZWxlZ2F0ZTtcbiAgICAgICAgICAgICAgICBlLmN1cnJlbnQgPSBjdXJyZW50O1xuXG4gICAgICAgICAgICAgICAgbGlzdGVuZXIuY2FsbCh0aGlzLCBlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KTtcblxuICAgIH07XG59XG5cbmZ1bmN0aW9uIGRldGFpbChsaXN0ZW5lcikge1xuICAgIHJldHVybiBlID0+IGlzQXJyYXkoZS5kZXRhaWwpID8gbGlzdGVuZXIoLi4uW2VdLmNvbmNhdChlLmRldGFpbCkpIDogbGlzdGVuZXIoZSk7XG59XG5cbmZ1bmN0aW9uIHNlbGZGaWx0ZXIobGlzdGVuZXIpIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgaWYgKGUudGFyZ2V0ID09PSBlLmN1cnJlbnRUYXJnZXQgfHwgZS50YXJnZXQgPT09IGUuY3VycmVudCkge1xuICAgICAgICAgICAgcmV0dXJuIGxpc3RlbmVyLmNhbGwobnVsbCwgZSk7XG4gICAgICAgIH1cbiAgICB9O1xufVxuXG5mdW5jdGlvbiB1c2VDYXB0dXJlRmlsdGVyKG9wdGlvbnMpIHtcbiAgICByZXR1cm4gb3B0aW9ucyAmJiBpc0lFICYmICFpc0Jvb2xlYW4ob3B0aW9ucylcbiAgICAgICAgPyAhIW9wdGlvbnMuY2FwdHVyZVxuICAgICAgICA6IG9wdGlvbnM7XG59XG5cbmZ1bmN0aW9uIGlzRXZlbnRUYXJnZXQodGFyZ2V0KSB7XG4gICAgcmV0dXJuIHRhcmdldCAmJiAnYWRkRXZlbnRMaXN0ZW5lcicgaW4gdGFyZ2V0O1xufVxuXG5mdW5jdGlvbiB0b0V2ZW50VGFyZ2V0KHRhcmdldCkge1xuICAgIHJldHVybiBpc0V2ZW50VGFyZ2V0KHRhcmdldCkgPyB0YXJnZXQgOiB0b05vZGUodGFyZ2V0KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvRXZlbnRUYXJnZXRzKHRhcmdldCkge1xuICAgIHJldHVybiBpc0FycmF5KHRhcmdldClcbiAgICAgICAgICAgID8gdGFyZ2V0Lm1hcCh0b0V2ZW50VGFyZ2V0KS5maWx0ZXIoQm9vbGVhbilcbiAgICAgICAgICAgIDogaXNTdHJpbmcodGFyZ2V0KVxuICAgICAgICAgICAgICAgID8gZmluZEFsbCh0YXJnZXQpXG4gICAgICAgICAgICAgICAgOiBpc0V2ZW50VGFyZ2V0KHRhcmdldClcbiAgICAgICAgICAgICAgICAgICAgPyBbdGFyZ2V0XVxuICAgICAgICAgICAgICAgICAgICA6IHRvTm9kZXModGFyZ2V0KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGlzVG91Y2goZSkge1xuICAgIHJldHVybiBlLnBvaW50ZXJUeXBlID09PSAndG91Y2gnIHx8ICEhZS50b3VjaGVzO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZ2V0RXZlbnRQb3MoZSwgcHJvcCA9ICdjbGllbnQnKSB7XG4gICAgY29uc3Qge3RvdWNoZXMsIGNoYW5nZWRUb3VjaGVzfSA9IGU7XG4gICAgY29uc3Qge1tgJHtwcm9wfVhgXTogeCwgW2Ake3Byb3B9WWBdOiB5fSA9IHRvdWNoZXMgJiYgdG91Y2hlc1swXSB8fCBjaGFuZ2VkVG91Y2hlcyAmJiBjaGFuZ2VkVG91Y2hlc1swXSB8fCBlO1xuXG4gICAgcmV0dXJuIHt4LCB5fTtcbn1cbiIsIi8qIGdsb2JhbCBzZXRJbW1lZGlhdGUgKi9cbmltcG9ydCB7aXNGdW5jdGlvbiwgaXNPYmplY3R9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBjb25zdCBQcm9taXNlID0gJ1Byb21pc2UnIGluIHdpbmRvdyA/IHdpbmRvdy5Qcm9taXNlIDogUHJvbWlzZUZuO1xuXG5leHBvcnQgY2xhc3MgRGVmZXJyZWQge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLnByb21pc2UgPSBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgICAgICB0aGlzLnJlamVjdCA9IHJlamVjdDtcbiAgICAgICAgICAgIHRoaXMucmVzb2x2ZSA9IHJlc29sdmU7XG4gICAgICAgIH0pO1xuICAgIH1cbn1cblxuLyoqXG4gKiBQcm9taXNlcy9BKyBwb2x5ZmlsbCB2MS4xLjQgKGh0dHBzOi8vZ2l0aHViLmNvbS9icmFtc3RlaW4vcHJvbWlzKVxuICovXG5cbmNvbnN0IFJFU09MVkVEID0gMDtcbmNvbnN0IFJFSkVDVEVEID0gMTtcbmNvbnN0IFBFTkRJTkcgPSAyO1xuXG5jb25zdCBhc3luYyA9ICdzZXRJbW1lZGlhdGUnIGluIHdpbmRvdyA/IHNldEltbWVkaWF0ZSA6IHNldFRpbWVvdXQ7XG5cbmZ1bmN0aW9uIFByb21pc2VGbihleGVjdXRvcikge1xuXG4gICAgdGhpcy5zdGF0ZSA9IFBFTkRJTkc7XG4gICAgdGhpcy52YWx1ZSA9IHVuZGVmaW5lZDtcbiAgICB0aGlzLmRlZmVycmVkID0gW107XG5cbiAgICBjb25zdCBwcm9taXNlID0gdGhpcztcblxuICAgIHRyeSB7XG4gICAgICAgIGV4ZWN1dG9yKFxuICAgICAgICAgICAgeCA9PiB7XG4gICAgICAgICAgICAgICAgcHJvbWlzZS5yZXNvbHZlKHgpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHIgPT4ge1xuICAgICAgICAgICAgICAgIHByb21pc2UucmVqZWN0KHIpO1xuICAgICAgICAgICAgfVxuICAgICAgICApO1xuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgcHJvbWlzZS5yZWplY3QoZSk7XG4gICAgfVxufVxuXG5Qcm9taXNlRm4ucmVqZWN0ID0gZnVuY3Rpb24gKHIpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIHJlamVjdChyKTtcbiAgICB9KTtcbn07XG5cblByb21pc2VGbi5yZXNvbHZlID0gZnVuY3Rpb24gKHgpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIHJlc29sdmUoeCk7XG4gICAgfSk7XG59O1xuXG5Qcm9taXNlRm4uYWxsID0gZnVuY3Rpb24gYWxsKGl0ZXJhYmxlKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlRm4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICBjb25zdCByZXN1bHQgPSBbXTtcbiAgICAgICAgbGV0IGNvdW50ID0gMDtcblxuICAgICAgICBpZiAoaXRlcmFibGUubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICByZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiByZXNvbHZlcihpKSB7XG4gICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24gKHgpIHtcbiAgICAgICAgICAgICAgICByZXN1bHRbaV0gPSB4O1xuICAgICAgICAgICAgICAgIGNvdW50ICs9IDE7XG5cbiAgICAgICAgICAgICAgICBpZiAoY291bnQgPT09IGl0ZXJhYmxlLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKHJlc3VsdCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlcmFibGUubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIFByb21pc2VGbi5yZXNvbHZlKGl0ZXJhYmxlW2ldKS50aGVuKHJlc29sdmVyKGkpLCByZWplY3QpO1xuICAgICAgICB9XG4gICAgfSk7XG59O1xuXG5Qcm9taXNlRm4ucmFjZSA9IGZ1bmN0aW9uIHJhY2UoaXRlcmFibGUpIHtcbiAgICByZXR1cm4gbmV3IFByb21pc2VGbigocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlcmFibGUubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgIFByb21pc2VGbi5yZXNvbHZlKGl0ZXJhYmxlW2ldKS50aGVuKHJlc29sdmUsIHJlamVjdCk7XG4gICAgICAgIH1cbiAgICB9KTtcbn07XG5cbmNvbnN0IHAgPSBQcm9taXNlRm4ucHJvdG90eXBlO1xuXG5wLnJlc29sdmUgPSBmdW5jdGlvbiByZXNvbHZlKHgpIHtcbiAgICBjb25zdCBwcm9taXNlID0gdGhpcztcblxuICAgIGlmIChwcm9taXNlLnN0YXRlID09PSBQRU5ESU5HKSB7XG4gICAgICAgIGlmICh4ID09PSBwcm9taXNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdQcm9taXNlIHNldHRsZWQgd2l0aCBpdHNlbGYuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgY2FsbGVkID0gZmFsc2U7XG5cbiAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgIGNvbnN0IHRoZW4gPSB4ICYmIHgudGhlbjtcblxuICAgICAgICAgICAgaWYgKHggIT09IG51bGwgJiYgaXNPYmplY3QoeCkgJiYgaXNGdW5jdGlvbih0aGVuKSkge1xuICAgICAgICAgICAgICAgIHRoZW4uY2FsbChcbiAgICAgICAgICAgICAgICAgICAgeCxcbiAgICAgICAgICAgICAgICAgICAgeCA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoIWNhbGxlZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHByb21pc2UucmVzb2x2ZSh4KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHIgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFjYWxsZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwcm9taXNlLnJlamVjdChyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgaWYgKCFjYWxsZWQpIHtcbiAgICAgICAgICAgICAgICBwcm9taXNlLnJlamVjdChlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHByb21pc2Uuc3RhdGUgPSBSRVNPTFZFRDtcbiAgICAgICAgcHJvbWlzZS52YWx1ZSA9IHg7XG4gICAgICAgIHByb21pc2Uubm90aWZ5KCk7XG4gICAgfVxufTtcblxucC5yZWplY3QgPSBmdW5jdGlvbiByZWplY3QocmVhc29uKSB7XG4gICAgY29uc3QgcHJvbWlzZSA9IHRoaXM7XG5cbiAgICBpZiAocHJvbWlzZS5zdGF0ZSA9PT0gUEVORElORykge1xuICAgICAgICBpZiAocmVhc29uID09PSBwcm9taXNlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdQcm9taXNlIHNldHRsZWQgd2l0aCBpdHNlbGYuJyk7XG4gICAgICAgIH1cblxuICAgICAgICBwcm9taXNlLnN0YXRlID0gUkVKRUNURUQ7XG4gICAgICAgIHByb21pc2UudmFsdWUgPSByZWFzb247XG4gICAgICAgIHByb21pc2Uubm90aWZ5KCk7XG4gICAgfVxufTtcblxucC5ub3RpZnkgPSBmdW5jdGlvbiBub3RpZnkoKSB7XG4gICAgYXN5bmMoKCkgPT4ge1xuICAgICAgICBpZiAodGhpcy5zdGF0ZSAhPT0gUEVORElORykge1xuICAgICAgICAgICAgd2hpbGUgKHRoaXMuZGVmZXJyZWQubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgW29uUmVzb2x2ZWQsIG9uUmVqZWN0ZWQsIHJlc29sdmUsIHJlamVjdF0gPSB0aGlzLmRlZmVycmVkLnNoaWZ0KCk7XG5cbiAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5zdGF0ZSA9PT0gUkVTT0xWRUQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKG9uUmVzb2x2ZWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShvblJlc29sdmVkLmNhbGwodW5kZWZpbmVkLCB0aGlzLnZhbHVlKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc29sdmUodGhpcy52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5zdGF0ZSA9PT0gUkVKRUNURUQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpc0Z1bmN0aW9uKG9uUmVqZWN0ZWQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShvblJlamVjdGVkLmNhbGwodW5kZWZpbmVkLCB0aGlzLnZhbHVlKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlamVjdCh0aGlzLnZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVqZWN0KGUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0pO1xufTtcblxucC50aGVuID0gZnVuY3Rpb24gdGhlbihvblJlc29sdmVkLCBvblJlamVjdGVkKSB7XG4gICAgcmV0dXJuIG5ldyBQcm9taXNlRm4oKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICB0aGlzLmRlZmVycmVkLnB1c2goW29uUmVzb2x2ZWQsIG9uUmVqZWN0ZWQsIHJlc29sdmUsIHJlamVjdF0pO1xuICAgICAgICB0aGlzLm5vdGlmeSgpO1xuICAgIH0pO1xufTtcblxucC5jYXRjaCA9IGZ1bmN0aW9uIChvblJlamVjdGVkKSB7XG4gICAgcmV0dXJuIHRoaXMudGhlbih1bmRlZmluZWQsIG9uUmVqZWN0ZWQpO1xufTtcbiIsImltcG9ydCB7b259IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtQcm9taXNlfSBmcm9tICcuL3Byb21pc2UnO1xuaW1wb3J0IHthc3NpZ24sIG5vb3B9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBmdW5jdGlvbiBhamF4KHVybCwgb3B0aW9ucykge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG5cbiAgICAgICAgY29uc3QgZW52ID0gYXNzaWduKHtcbiAgICAgICAgICAgIGRhdGE6IG51bGwsXG4gICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgICAgICAgaGVhZGVyczoge30sXG4gICAgICAgICAgICB4aHI6IG5ldyBYTUxIdHRwUmVxdWVzdCgpLFxuICAgICAgICAgICAgYmVmb3JlU2VuZDogbm9vcCxcbiAgICAgICAgICAgIHJlc3BvbnNlVHlwZTogJydcbiAgICAgICAgfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgZW52LmJlZm9yZVNlbmQoZW52KTtcblxuICAgICAgICBjb25zdCB7eGhyfSA9IGVudjtcblxuICAgICAgICBmb3IgKGNvbnN0IHByb3AgaW4gZW52KSB7XG4gICAgICAgICAgICBpZiAocHJvcCBpbiB4aHIpIHtcbiAgICAgICAgICAgICAgICB0cnkge1xuXG4gICAgICAgICAgICAgICAgICAgIHhocltwcm9wXSA9IGVudltwcm9wXTtcblxuICAgICAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHt9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICB4aHIub3BlbihlbnYubWV0aG9kLnRvVXBwZXJDYXNlKCksIHVybCk7XG5cbiAgICAgICAgZm9yIChjb25zdCBoZWFkZXIgaW4gZW52LmhlYWRlcnMpIHtcbiAgICAgICAgICAgIHhoci5zZXRSZXF1ZXN0SGVhZGVyKGhlYWRlciwgZW52LmhlYWRlcnNbaGVhZGVyXSk7XG4gICAgICAgIH1cblxuICAgICAgICBvbih4aHIsICdsb2FkJywgKCkgPT4ge1xuXG4gICAgICAgICAgICBpZiAoeGhyLnN0YXR1cyA9PT0gMCB8fCB4aHIuc3RhdHVzID49IDIwMCAmJiB4aHIuc3RhdHVzIDwgMzAwIHx8IHhoci5zdGF0dXMgPT09IDMwNCkge1xuICAgICAgICAgICAgICAgIHJlc29sdmUoeGhyKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmVqZWN0KGFzc2lnbihFcnJvcih4aHIuc3RhdHVzVGV4dCksIHtcbiAgICAgICAgICAgICAgICAgICAgeGhyLFxuICAgICAgICAgICAgICAgICAgICBzdGF0dXM6IHhoci5zdGF0dXNcbiAgICAgICAgICAgICAgICB9KSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgb24oeGhyLCAnZXJyb3InLCAoKSA9PiByZWplY3QoYXNzaWduKEVycm9yKCdOZXR3b3JrIEVycm9yJyksIHt4aHJ9KSkpO1xuICAgICAgICBvbih4aHIsICd0aW1lb3V0JywgKCkgPT4gcmVqZWN0KGFzc2lnbihFcnJvcignTmV0d29yayBUaW1lb3V0JyksIHt4aHJ9KSkpO1xuXG4gICAgICAgIHhoci5zZW5kKGVudi5kYXRhKTtcbiAgICB9KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGdldEltYWdlKHNyYywgc3Jjc2V0LCBzaXplcykge1xuXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcbiAgICAgICAgY29uc3QgaW1nID0gbmV3IEltYWdlKCk7XG5cbiAgICAgICAgaW1nLm9uZXJyb3IgPSByZWplY3Q7XG4gICAgICAgIGltZy5vbmxvYWQgPSAoKSA9PiByZXNvbHZlKGltZyk7XG5cbiAgICAgICAgc2l6ZXMgJiYgKGltZy5zaXplcyA9IHNpemVzKTtcbiAgICAgICAgc3Jjc2V0ICYmIChpbWcuc3Jjc2V0ID0gc3Jjc2V0KTtcbiAgICAgICAgaW1nLnNyYyA9IHNyYztcbiAgICB9KTtcblxufVxuIiwiaW1wb3J0IHtvbn0gZnJvbSAnLi9ldmVudCc7XG5pbXBvcnQge2NoaWxkcmVuLCBmaW5kLCBmaW5kQWxsLCBwYXJlbnR9IGZyb20gJy4vc2VsZWN0b3InO1xuaW1wb3J0IHtjbGFtcCwgaXNFbGVtZW50LCBpc051bWVyaWMsIGlzU3RyaW5nLCBpc1VuZGVmaW5lZCwgdG9Ob2RlLCB0b05vZGVzLCB0b051bWJlcn0gZnJvbSAnLi9sYW5nJztcblxuZXhwb3J0IGZ1bmN0aW9uIHJlYWR5KGZuKSB7XG5cbiAgICBpZiAoZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnKSB7XG4gICAgICAgIGZuKCk7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCB1bmJpbmQgPSBvbihkb2N1bWVudCwgJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHVuYmluZCgpO1xuICAgICAgICBmbigpO1xuICAgIH0pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaW5kZXgoZWxlbWVudCwgcmVmKSB7XG4gICAgcmV0dXJuIHJlZlxuICAgICAgICA/IHRvTm9kZXMoZWxlbWVudCkuaW5kZXhPZih0b05vZGUocmVmKSlcbiAgICAgICAgOiBjaGlsZHJlbihwYXJlbnQoZWxlbWVudCkpLmluZGV4T2YoZWxlbWVudCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRJbmRleChpLCBlbGVtZW50cywgY3VycmVudCA9IDAsIGZpbml0ZSA9IGZhbHNlKSB7XG5cbiAgICBlbGVtZW50cyA9IHRvTm9kZXMoZWxlbWVudHMpO1xuXG4gICAgY29uc3Qge2xlbmd0aH0gPSBlbGVtZW50cztcblxuICAgIGkgPSBpc051bWVyaWMoaSlcbiAgICAgICAgPyB0b051bWJlcihpKVxuICAgICAgICA6IGkgPT09ICduZXh0J1xuICAgICAgICAgICAgPyBjdXJyZW50ICsgMVxuICAgICAgICAgICAgOiBpID09PSAncHJldmlvdXMnXG4gICAgICAgICAgICAgICAgPyBjdXJyZW50IC0gMVxuICAgICAgICAgICAgICAgIDogaW5kZXgoZWxlbWVudHMsIGkpO1xuXG4gICAgaWYgKGZpbml0ZSkge1xuICAgICAgICByZXR1cm4gY2xhbXAoaSwgMCwgbGVuZ3RoIC0gMSk7XG4gICAgfVxuXG4gICAgaSAlPSBsZW5ndGg7XG5cbiAgICByZXR1cm4gaSA8IDAgPyBpICsgbGVuZ3RoIDogaTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGVtcHR5KGVsZW1lbnQpIHtcbiAgICBlbGVtZW50ID0gJChlbGVtZW50KTtcbiAgICBlbGVtZW50LmlubmVySFRNTCA9ICcnO1xuICAgIHJldHVybiBlbGVtZW50O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gaHRtbChwYXJlbnQsIGh0bWwpIHtcbiAgICBwYXJlbnQgPSAkKHBhcmVudCk7XG4gICAgcmV0dXJuIGlzVW5kZWZpbmVkKGh0bWwpXG4gICAgICAgID8gcGFyZW50LmlubmVySFRNTFxuICAgICAgICA6IGFwcGVuZChwYXJlbnQuaGFzQ2hpbGROb2RlcygpID8gZW1wdHkocGFyZW50KSA6IHBhcmVudCwgaHRtbCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBwcmVwZW5kKHBhcmVudCwgZWxlbWVudCkge1xuXG4gICAgcGFyZW50ID0gJChwYXJlbnQpO1xuXG4gICAgaWYgKCFwYXJlbnQuaGFzQ2hpbGROb2RlcygpKSB7XG4gICAgICAgIHJldHVybiBhcHBlbmQocGFyZW50LCBlbGVtZW50KTtcbiAgICB9IGVsc2Uge1xuICAgICAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiBwYXJlbnQuaW5zZXJ0QmVmb3JlKGVsZW1lbnQsIHBhcmVudC5maXJzdENoaWxkKSk7XG4gICAgfVxufVxuXG5leHBvcnQgZnVuY3Rpb24gYXBwZW5kKHBhcmVudCwgZWxlbWVudCkge1xuICAgIHBhcmVudCA9ICQocGFyZW50KTtcbiAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiBwYXJlbnQuYXBwZW5kQ2hpbGQoZWxlbWVudCkpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYmVmb3JlKHJlZiwgZWxlbWVudCkge1xuICAgIHJlZiA9ICQocmVmKTtcbiAgICByZXR1cm4gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZWxlbWVudCA9PiByZWYucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoZWxlbWVudCwgcmVmKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBhZnRlcihyZWYsIGVsZW1lbnQpIHtcbiAgICByZWYgPSAkKHJlZik7XG4gICAgcmV0dXJuIGluc2VydE5vZGVzKGVsZW1lbnQsIGVsZW1lbnQgPT4gcmVmLm5leHRTaWJsaW5nXG4gICAgICAgID8gYmVmb3JlKHJlZi5uZXh0U2libGluZywgZWxlbWVudClcbiAgICAgICAgOiBhcHBlbmQocmVmLnBhcmVudE5vZGUsIGVsZW1lbnQpXG4gICAgKTtcbn1cblxuZnVuY3Rpb24gaW5zZXJ0Tm9kZXMoZWxlbWVudCwgZm4pIHtcbiAgICBlbGVtZW50ID0gaXNTdHJpbmcoZWxlbWVudCkgPyBmcmFnbWVudChlbGVtZW50KSA6IGVsZW1lbnQ7XG4gICAgcmV0dXJuIGVsZW1lbnRcbiAgICAgICAgPyAnbGVuZ3RoJyBpbiBlbGVtZW50XG4gICAgICAgICAgICA/IHRvTm9kZXMoZWxlbWVudCkubWFwKGZuKVxuICAgICAgICAgICAgOiBmbihlbGVtZW50KVxuICAgICAgICA6IG51bGw7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiByZW1vdmUoZWxlbWVudCkge1xuICAgIHRvTm9kZXMoZWxlbWVudCkubWFwKGVsZW1lbnQgPT4gZWxlbWVudC5wYXJlbnROb2RlICYmIGVsZW1lbnQucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChlbGVtZW50KSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB3cmFwQWxsKGVsZW1lbnQsIHN0cnVjdHVyZSkge1xuXG4gICAgc3RydWN0dXJlID0gdG9Ob2RlKGJlZm9yZShlbGVtZW50LCBzdHJ1Y3R1cmUpKTtcblxuICAgIHdoaWxlIChzdHJ1Y3R1cmUuZmlyc3RDaGlsZCkge1xuICAgICAgICBzdHJ1Y3R1cmUgPSBzdHJ1Y3R1cmUuZmlyc3RDaGlsZDtcbiAgICB9XG5cbiAgICBhcHBlbmQoc3RydWN0dXJlLCBlbGVtZW50KTtcblxuICAgIHJldHVybiBzdHJ1Y3R1cmU7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB3cmFwSW5uZXIoZWxlbWVudCwgc3RydWN0dXJlKSB7XG4gICAgcmV0dXJuIHRvTm9kZXModG9Ob2RlcyhlbGVtZW50KS5tYXAoZWxlbWVudCA9PlxuICAgICAgICBlbGVtZW50Lmhhc0NoaWxkTm9kZXMgPyB3cmFwQWxsKHRvTm9kZXMoZWxlbWVudC5jaGlsZE5vZGVzKSwgc3RydWN0dXJlKSA6IGFwcGVuZChlbGVtZW50LCBzdHJ1Y3R1cmUpXG4gICAgKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiB1bndyYXAoZWxlbWVudCkge1xuICAgIHRvTm9kZXMoZWxlbWVudClcbiAgICAgICAgLm1hcChwYXJlbnQpXG4gICAgICAgIC5maWx0ZXIoKHZhbHVlLCBpbmRleCwgc2VsZikgPT4gc2VsZi5pbmRleE9mKHZhbHVlKSA9PT0gaW5kZXgpXG4gICAgICAgIC5mb3JFYWNoKHBhcmVudCA9PiB7XG4gICAgICAgICAgICBiZWZvcmUocGFyZW50LCBwYXJlbnQuY2hpbGROb2Rlcyk7XG4gICAgICAgICAgICByZW1vdmUocGFyZW50KTtcbiAgICAgICAgfSk7XG59XG5cbmNvbnN0IGZyYWdtZW50UmUgPSAvXlxccyo8KFxcdyt8ISlbXj5dKj4vO1xuY29uc3Qgc2luZ2xlVGFnUmUgPSAvXjwoXFx3KylcXHMqXFwvPz4oPzo8XFwvXFwxPik/JC87XG5cbmV4cG9ydCBmdW5jdGlvbiBmcmFnbWVudChodG1sKSB7XG5cbiAgICBjb25zdCBtYXRjaGVzID0gc2luZ2xlVGFnUmUuZXhlYyhodG1sKTtcbiAgICBpZiAobWF0Y2hlcykge1xuICAgICAgICByZXR1cm4gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChtYXRjaGVzWzFdKTtcbiAgICB9XG5cbiAgICBjb25zdCBjb250YWluZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgICBpZiAoZnJhZ21lbnRSZS50ZXN0KGh0bWwpKSB7XG4gICAgICAgIGNvbnRhaW5lci5pbnNlcnRBZGphY2VudEhUTUwoJ2JlZm9yZWVuZCcsIGh0bWwudHJpbSgpKTtcbiAgICB9IGVsc2Uge1xuICAgICAgICBjb250YWluZXIudGV4dENvbnRlbnQgPSBodG1sO1xuICAgIH1cblxuICAgIHJldHVybiBjb250YWluZXIuY2hpbGROb2Rlcy5sZW5ndGggPiAxID8gdG9Ob2Rlcyhjb250YWluZXIuY2hpbGROb2RlcykgOiBjb250YWluZXIuZmlyc3RDaGlsZDtcblxufVxuXG5leHBvcnQgZnVuY3Rpb24gYXBwbHkobm9kZSwgZm4pIHtcblxuICAgIGlmICghaXNFbGVtZW50KG5vZGUpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBmbihub2RlKTtcbiAgICBub2RlID0gbm9kZS5maXJzdEVsZW1lbnRDaGlsZDtcbiAgICB3aGlsZSAobm9kZSkge1xuICAgICAgICBjb25zdCBuZXh0ID0gbm9kZS5uZXh0RWxlbWVudFNpYmxpbmc7XG4gICAgICAgIGFwcGx5KG5vZGUsIGZuKTtcbiAgICAgICAgbm9kZSA9IG5leHQ7XG4gICAgfVxufVxuXG5leHBvcnQgZnVuY3Rpb24gJChzZWxlY3RvciwgY29udGV4dCkge1xuICAgIHJldHVybiAhaXNTdHJpbmcoc2VsZWN0b3IpXG4gICAgICAgID8gdG9Ob2RlKHNlbGVjdG9yKVxuICAgICAgICA6IGlzSHRtbChzZWxlY3RvcilcbiAgICAgICAgICAgID8gdG9Ob2RlKGZyYWdtZW50KHNlbGVjdG9yKSlcbiAgICAgICAgICAgIDogZmluZChzZWxlY3RvciwgY29udGV4dCk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiAkJChzZWxlY3RvciwgY29udGV4dCkge1xuICAgIHJldHVybiAhaXNTdHJpbmcoc2VsZWN0b3IpXG4gICAgICAgID8gdG9Ob2RlcyhzZWxlY3RvcilcbiAgICAgICAgOiBpc0h0bWwoc2VsZWN0b3IpXG4gICAgICAgICAgICA/IHRvTm9kZXMoZnJhZ21lbnQoc2VsZWN0b3IpKVxuICAgICAgICAgICAgOiBmaW5kQWxsKHNlbGVjdG9yLCBjb250ZXh0KTtcbn1cblxuZnVuY3Rpb24gaXNIdG1sKHN0cikge1xuICAgIHJldHVybiBzdHJbMF0gPT09ICc8JyB8fCBzdHIubWF0Y2goL15cXHMqPC8pO1xufVxuIiwiaW1wb3J0IHthdHRyfSBmcm9tICcuL2F0dHInO1xuaW1wb3J0IHtoYXNPd24sIGluY2x1ZGVzLCBpc1N0cmluZywgaXNVbmRlZmluZWQsIGxhc3QsIHRvTm9kZXN9IGZyb20gJy4vbGFuZyc7XG5cbmV4cG9ydCBmdW5jdGlvbiBhZGRDbGFzcyhlbGVtZW50LCAuLi5hcmdzKSB7XG4gICAgYXBwbHkoZWxlbWVudCwgYXJncywgJ2FkZCcpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcmVtb3ZlQ2xhc3MoZWxlbWVudCwgLi4uYXJncykge1xuICAgIGFwcGx5KGVsZW1lbnQsIGFyZ3MsICdyZW1vdmUnKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHJlbW92ZUNsYXNzZXMoZWxlbWVudCwgY2xzKSB7XG4gICAgYXR0cihlbGVtZW50LCAnY2xhc3MnLCB2YWx1ZSA9PiAodmFsdWUgfHwgJycpLnJlcGxhY2UobmV3IFJlZ0V4cChgXFxcXGIke2Nsc31cXFxcYmAsICdnJyksICcnKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiByZXBsYWNlQ2xhc3MoZWxlbWVudCwgLi4uYXJncykge1xuICAgIGFyZ3NbMF0gJiYgcmVtb3ZlQ2xhc3MoZWxlbWVudCwgYXJnc1swXSk7XG4gICAgYXJnc1sxXSAmJiBhZGRDbGFzcyhlbGVtZW50LCBhcmdzWzFdKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGhhc0NsYXNzKGVsZW1lbnQsIGNscykge1xuICAgIHJldHVybiBjbHMgJiYgdG9Ob2RlcyhlbGVtZW50KS5zb21lKGVsZW1lbnQgPT4gZWxlbWVudC5jbGFzc0xpc3QuY29udGFpbnMoY2xzLnNwbGl0KCcgJylbMF0pKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvZ2dsZUNsYXNzKGVsZW1lbnQsIC4uLmFyZ3MpIHtcblxuICAgIGlmICghYXJncy5sZW5ndGgpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGFyZ3MgPSBnZXRBcmdzKGFyZ3MpO1xuXG4gICAgY29uc3QgZm9yY2UgPSAhaXNTdHJpbmcobGFzdChhcmdzKSkgPyBhcmdzLnBvcCgpIDogW107IC8vIGluIGlPUyA5LjMgZm9yY2UgPT09IHVuZGVmaW5lZCBldmFsdWF0ZXMgdG8gZmFsc2VcblxuICAgIGFyZ3MgPSBhcmdzLmZpbHRlcihCb29sZWFuKTtcblxuICAgIHRvTm9kZXMoZWxlbWVudCkuZm9yRWFjaCgoe2NsYXNzTGlzdH0pID0+IHtcbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBhcmdzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICBzdXBwb3J0cy5Gb3JjZVxuICAgICAgICAgICAgICAgID8gY2xhc3NMaXN0LnRvZ2dsZSguLi5bYXJnc1tpXV0uY29uY2F0KGZvcmNlKSlcbiAgICAgICAgICAgICAgICA6IChjbGFzc0xpc3RbKCFpc1VuZGVmaW5lZChmb3JjZSkgPyBmb3JjZSA6ICFjbGFzc0xpc3QuY29udGFpbnMoYXJnc1tpXSkpID8gJ2FkZCcgOiAncmVtb3ZlJ10oYXJnc1tpXSkpO1xuICAgICAgICB9XG4gICAgfSk7XG5cbn1cblxuZnVuY3Rpb24gYXBwbHkoZWxlbWVudCwgYXJncywgZm4pIHtcbiAgICBhcmdzID0gZ2V0QXJncyhhcmdzKS5maWx0ZXIoQm9vbGVhbik7XG5cbiAgICBhcmdzLmxlbmd0aCAmJiB0b05vZGVzKGVsZW1lbnQpLmZvckVhY2goKHtjbGFzc0xpc3R9KSA9PiB7XG4gICAgICAgIHN1cHBvcnRzLk11bHRpcGxlXG4gICAgICAgICAgICA/IGNsYXNzTGlzdFtmbl0oLi4uYXJncylcbiAgICAgICAgICAgIDogYXJncy5mb3JFYWNoKGNscyA9PiBjbGFzc0xpc3RbZm5dKGNscykpO1xuICAgIH0pO1xufVxuXG5mdW5jdGlvbiBnZXRBcmdzKGFyZ3MpIHtcbiAgICByZXR1cm4gYXJncy5yZWR1Y2UoKGFyZ3MsIGFyZykgPT5cbiAgICAgICAgYXJncy5jb25jYXQuY2FsbChhcmdzLCBpc1N0cmluZyhhcmcpICYmIGluY2x1ZGVzKGFyZywgJyAnKSA/IGFyZy50cmltKCkuc3BsaXQoJyAnKSA6IGFyZylcbiAgICAgICAgLCBbXSk7XG59XG5cbi8vIElFIDExXG5jb25zdCBzdXBwb3J0cyA9IHtcblxuICAgIGdldCBNdWx0aXBsZSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuZ2V0KCdfbXVsdGlwbGUnKTtcbiAgICB9LFxuXG4gICAgZ2V0IEZvcmNlKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5nZXQoJ19mb3JjZScpO1xuICAgIH0sXG5cbiAgICBnZXQoa2V5KSB7XG5cbiAgICAgICAgaWYgKCFoYXNPd24odGhpcywga2V5KSkge1xuICAgICAgICAgICAgY29uc3Qge2NsYXNzTGlzdH0gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdfJyk7XG4gICAgICAgICAgICBjbGFzc0xpc3QuYWRkKCdhJywgJ2InKTtcbiAgICAgICAgICAgIGNsYXNzTGlzdC50b2dnbGUoJ2MnLCBmYWxzZSk7XG4gICAgICAgICAgICB0aGlzLl9tdWx0aXBsZSA9IGNsYXNzTGlzdC5jb250YWlucygnYicpO1xuICAgICAgICAgICAgdGhpcy5fZm9yY2UgPSAhY2xhc3NMaXN0LmNvbnRhaW5zKCdjJyk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpc1trZXldO1xuICAgIH1cblxufTtcbiIsImltcG9ydCB7aXNJRX0gZnJvbSAnLi9lbnYnO1xuaW1wb3J0IHthcHBlbmQsIHJlbW92ZX0gZnJvbSAnLi9kb20nO1xuaW1wb3J0IHthZGRDbGFzc30gZnJvbSAnLi9jbGFzcyc7XG5pbXBvcnQge2VhY2gsIGh5cGhlbmF0ZSwgaXNBcnJheSwgaXNOdW1iZXIsIGlzTnVtZXJpYywgaXNPYmplY3QsIGlzU3RyaW5nLCBpc1VuZGVmaW5lZCwgdG9Ob2RlLCB0b05vZGVzfSBmcm9tICcuL2xhbmcnO1xuXG5jb25zdCBjc3NOdW1iZXIgPSB7XG4gICAgJ2FuaW1hdGlvbi1pdGVyYXRpb24tY291bnQnOiB0cnVlLFxuICAgICdjb2x1bW4tY291bnQnOiB0cnVlLFxuICAgICdmaWxsLW9wYWNpdHknOiB0cnVlLFxuICAgICdmbGV4LWdyb3cnOiB0cnVlLFxuICAgICdmbGV4LXNocmluayc6IHRydWUsXG4gICAgJ2ZvbnQtd2VpZ2h0JzogdHJ1ZSxcbiAgICAnbGluZS1oZWlnaHQnOiB0cnVlLFxuICAgICdvcGFjaXR5JzogdHJ1ZSxcbiAgICAnb3JkZXInOiB0cnVlLFxuICAgICdvcnBoYW5zJzogdHJ1ZSxcbiAgICAnc3Ryb2tlLWRhc2hhcnJheSc6IHRydWUsXG4gICAgJ3N0cm9rZS1kYXNob2Zmc2V0JzogdHJ1ZSxcbiAgICAnd2lkb3dzJzogdHJ1ZSxcbiAgICAnei1pbmRleCc6IHRydWUsXG4gICAgJ3pvb20nOiB0cnVlXG59O1xuXG5leHBvcnQgZnVuY3Rpb24gY3NzKGVsZW1lbnQsIHByb3BlcnR5LCB2YWx1ZSkge1xuXG4gICAgcmV0dXJuIHRvTm9kZXMoZWxlbWVudCkubWFwKGVsZW1lbnQgPT4ge1xuXG4gICAgICAgIGlmIChpc1N0cmluZyhwcm9wZXJ0eSkpIHtcblxuICAgICAgICAgICAgcHJvcGVydHkgPSBwcm9wTmFtZShwcm9wZXJ0eSk7XG5cbiAgICAgICAgICAgIGlmIChpc1VuZGVmaW5lZCh2YWx1ZSkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZ2V0U3R5bGUoZWxlbWVudCwgcHJvcGVydHkpO1xuICAgICAgICAgICAgfSBlbHNlIGlmICghdmFsdWUgJiYgIWlzTnVtYmVyKHZhbHVlKSkge1xuICAgICAgICAgICAgICAgIGVsZW1lbnQuc3R5bGUucmVtb3ZlUHJvcGVydHkocHJvcGVydHkpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBlbGVtZW50LnN0eWxlW3Byb3BlcnR5XSA9IGlzTnVtZXJpYyh2YWx1ZSkgJiYgIWNzc051bWJlcltwcm9wZXJ0eV0gPyBgJHt2YWx1ZX1weGAgOiB2YWx1ZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9IGVsc2UgaWYgKGlzQXJyYXkocHJvcGVydHkpKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHN0eWxlcyA9IGdldFN0eWxlcyhlbGVtZW50KTtcblxuICAgICAgICAgICAgcmV0dXJuIHByb3BlcnR5LnJlZHVjZSgocHJvcHMsIHByb3BlcnR5KSA9PiB7XG4gICAgICAgICAgICAgICAgcHJvcHNbcHJvcGVydHldID0gc3R5bGVzW3Byb3BOYW1lKHByb3BlcnR5KV07XG4gICAgICAgICAgICAgICAgcmV0dXJuIHByb3BzO1xuICAgICAgICAgICAgfSwge30pO1xuXG4gICAgICAgIH0gZWxzZSBpZiAoaXNPYmplY3QocHJvcGVydHkpKSB7XG4gICAgICAgICAgICBlYWNoKHByb3BlcnR5LCAodmFsdWUsIHByb3BlcnR5KSA9PiBjc3MoZWxlbWVudCwgcHJvcGVydHksIHZhbHVlKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gZWxlbWVudDtcblxuICAgIH0pWzBdO1xuXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRTdHlsZXMoZWxlbWVudCwgcHNldWRvRWx0KSB7XG4gICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcbiAgICByZXR1cm4gZWxlbWVudC5vd25lckRvY3VtZW50LmRlZmF1bHRWaWV3LmdldENvbXB1dGVkU3R5bGUoZWxlbWVudCwgcHNldWRvRWx0KTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGdldFN0eWxlKGVsZW1lbnQsIHByb3BlcnR5LCBwc2V1ZG9FbHQpIHtcbiAgICByZXR1cm4gZ2V0U3R5bGVzKGVsZW1lbnQsIHBzZXVkb0VsdClbcHJvcGVydHldO1xufVxuXG5jb25zdCB2YXJzID0ge307XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRDc3NWYXIobmFtZSkge1xuXG4gICAgY29uc3QgZG9jRWwgPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG5cbiAgICBpZiAoIWlzSUUpIHtcbiAgICAgICAgcmV0dXJuIGdldFN0eWxlcyhkb2NFbCkuZ2V0UHJvcGVydHlWYWx1ZShgLS11ay0ke25hbWV9YCk7XG4gICAgfVxuXG4gICAgaWYgKCEobmFtZSBpbiB2YXJzKSkge1xuXG4gICAgICAgIC8qIHVzYWdlIGluIGNzczogLnVrLW5hbWU6YmVmb3JlIHsgY29udGVudDpcInh5elwiIH0gKi9cblxuICAgICAgICBjb25zdCBlbGVtZW50ID0gYXBwZW5kKGRvY0VsLCBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKSk7XG5cbiAgICAgICAgYWRkQ2xhc3MoZWxlbWVudCwgYHVrLSR7bmFtZX1gKTtcblxuICAgICAgICB2YXJzW25hbWVdID0gZ2V0U3R5bGUoZWxlbWVudCwgJ2NvbnRlbnQnLCAnOmJlZm9yZScpLnJlcGxhY2UoL15bXCInXSguKilbXCInXSQvLCAnJDEnKTtcblxuICAgICAgICByZW1vdmUoZWxlbWVudCk7XG5cbiAgICB9XG5cbiAgICByZXR1cm4gdmFyc1tuYW1lXTtcblxufVxuXG5jb25zdCBjc3NQcm9wcyA9IHt9O1xuXG5leHBvcnQgZnVuY3Rpb24gcHJvcE5hbWUobmFtZSkge1xuXG4gICAgbGV0IHJldCA9IGNzc1Byb3BzW25hbWVdO1xuICAgIGlmICghcmV0KSB7XG4gICAgICAgIHJldCA9IGNzc1Byb3BzW25hbWVdID0gdmVuZG9yUHJvcE5hbWUobmFtZSkgfHwgbmFtZTtcbiAgICB9XG4gICAgcmV0dXJuIHJldDtcbn1cblxuY29uc3QgY3NzUHJlZml4ZXMgPSBbJ3dlYmtpdCcsICdtb3onLCAnbXMnXTtcblxuZnVuY3Rpb24gdmVuZG9yUHJvcE5hbWUobmFtZSkge1xuXG4gICAgbmFtZSA9IGh5cGhlbmF0ZShuYW1lKTtcblxuICAgIGNvbnN0IHtzdHlsZX0gPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG5cbiAgICBpZiAobmFtZSBpbiBzdHlsZSkge1xuICAgICAgICByZXR1cm4gbmFtZTtcbiAgICB9XG5cbiAgICBsZXQgaSA9IGNzc1ByZWZpeGVzLmxlbmd0aCwgcHJlZml4ZWROYW1lO1xuXG4gICAgd2hpbGUgKGktLSkge1xuICAgICAgICBwcmVmaXhlZE5hbWUgPSBgLSR7Y3NzUHJlZml4ZXNbaV19LSR7bmFtZX1gO1xuICAgICAgICBpZiAocHJlZml4ZWROYW1lIGluIHN0eWxlKSB7XG4gICAgICAgICAgICByZXR1cm4gcHJlZml4ZWROYW1lO1xuICAgICAgICB9XG4gICAgfVxufVxuIiwiaW1wb3J0IHthdHRyfSBmcm9tICcuL2F0dHInO1xuaW1wb3J0IHtQcm9taXNlfSBmcm9tICcuL3Byb21pc2UnO1xuaW1wb3J0IHtvbmNlLCB0cmlnZ2VyfSBmcm9tICcuL2V2ZW50JztcbmltcG9ydCB7Y3NzLCBwcm9wTmFtZX0gZnJvbSAnLi9zdHlsZSc7XG5pbXBvcnQge2Fzc2lnbiwgc3RhcnRzV2l0aCwgdG9Ob2Rlc30gZnJvbSAnLi9sYW5nJztcbmltcG9ydCB7YWRkQ2xhc3MsIGhhc0NsYXNzLCByZW1vdmVDbGFzcywgcmVtb3ZlQ2xhc3Nlc30gZnJvbSAnLi9jbGFzcyc7XG5cbmV4cG9ydCBmdW5jdGlvbiB0cmFuc2l0aW9uKGVsZW1lbnQsIHByb3BzLCBkdXJhdGlvbiA9IDQwMCwgdGltaW5nID0gJ2xpbmVhcicpIHtcblxuICAgIHJldHVybiBQcm9taXNlLmFsbCh0b05vZGVzKGVsZW1lbnQpLm1hcChlbGVtZW50ID0+XG4gICAgICAgIG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcblxuICAgICAgICAgICAgZm9yIChjb25zdCBuYW1lIGluIHByb3BzKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgdmFsdWUgPSBjc3MoZWxlbWVudCwgbmFtZSk7XG4gICAgICAgICAgICAgICAgaWYgKHZhbHVlID09PSAnJykge1xuICAgICAgICAgICAgICAgICAgICBjc3MoZWxlbWVudCwgbmFtZSwgdmFsdWUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgdGltZXIgPSBzZXRUaW1lb3V0KCgpID0+IHRyaWdnZXIoZWxlbWVudCwgJ3RyYW5zaXRpb25lbmQnKSwgZHVyYXRpb24pO1xuXG4gICAgICAgICAgICBvbmNlKGVsZW1lbnQsICd0cmFuc2l0aW9uZW5kIHRyYW5zaXRpb25jYW5jZWxlZCcsICh7dHlwZX0pID0+IHtcbiAgICAgICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKGVsZW1lbnQsICd1ay10cmFuc2l0aW9uJyk7XG4gICAgICAgICAgICAgICAgY3NzKGVsZW1lbnQsIHtcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNpdGlvblByb3BlcnR5OiAnJyxcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNpdGlvbkR1cmF0aW9uOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uOiAnJ1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHR5cGUgPT09ICd0cmFuc2l0aW9uY2FuY2VsZWQnID8gcmVqZWN0KCkgOiByZXNvbHZlKCk7XG4gICAgICAgICAgICB9LCB7c2VsZjogdHJ1ZX0pO1xuXG4gICAgICAgICAgICBhZGRDbGFzcyhlbGVtZW50LCAndWstdHJhbnNpdGlvbicpO1xuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIGFzc2lnbih7XG4gICAgICAgICAgICAgICAgdHJhbnNpdGlvblByb3BlcnR5OiBPYmplY3Qua2V5cyhwcm9wcykubWFwKHByb3BOYW1lKS5qb2luKCcsJyksXG4gICAgICAgICAgICAgICAgdHJhbnNpdGlvbkR1cmF0aW9uOiBgJHtkdXJhdGlvbn1tc2AsXG4gICAgICAgICAgICAgICAgdHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uOiB0aW1pbmdcbiAgICAgICAgICAgIH0sIHByb3BzKSk7XG5cbiAgICAgICAgfSlcbiAgICApKTtcblxufVxuXG5leHBvcnQgY29uc3QgVHJhbnNpdGlvbiA9IHtcblxuICAgIHN0YXJ0OiB0cmFuc2l0aW9uLFxuXG4gICAgc3RvcChlbGVtZW50KSB7XG4gICAgICAgIHRyaWdnZXIoZWxlbWVudCwgJ3RyYW5zaXRpb25lbmQnKTtcbiAgICAgICAgcmV0dXJuIFByb21pc2UucmVzb2x2ZSgpO1xuICAgIH0sXG5cbiAgICBjYW5jZWwoZWxlbWVudCkge1xuICAgICAgICB0cmlnZ2VyKGVsZW1lbnQsICd0cmFuc2l0aW9uY2FuY2VsZWQnKTtcbiAgICB9LFxuXG4gICAgaW5Qcm9ncmVzcyhlbGVtZW50KSB7XG4gICAgICAgIHJldHVybiBoYXNDbGFzcyhlbGVtZW50LCAndWstdHJhbnNpdGlvbicpO1xuICAgIH1cblxufTtcblxuY29uc3QgYW5pbWF0aW9uUHJlZml4ID0gJ3VrLWFuaW1hdGlvbi0nO1xuY29uc3QgY2xzQ2FuY2VsQW5pbWF0aW9uID0gJ3VrLWNhbmNlbC1hbmltYXRpb24nO1xuXG5leHBvcnQgZnVuY3Rpb24gYW5pbWF0ZShlbGVtZW50LCBhbmltYXRpb24sIGR1cmF0aW9uID0gMjAwLCBvcmlnaW4sIG91dCkge1xuXG4gICAgcmV0dXJuIFByb21pc2UuYWxsKHRvTm9kZXMoZWxlbWVudCkubWFwKGVsZW1lbnQgPT5cbiAgICAgICAgbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXG4gICAgICAgICAgICBpZiAoaGFzQ2xhc3MoZWxlbWVudCwgY2xzQ2FuY2VsQW5pbWF0aW9uKSkge1xuICAgICAgICAgICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PlxuICAgICAgICAgICAgICAgICAgICBQcm9taXNlLnJlc29sdmUoKS50aGVuKCgpID0+XG4gICAgICAgICAgICAgICAgICAgICAgICBhbmltYXRlKC4uLmFyZ3VtZW50cykudGhlbihyZXNvbHZlLCByZWplY3QpXG4gICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgbGV0IGNscyA9IGAke2FuaW1hdGlvbn0gJHthbmltYXRpb25QcmVmaXh9JHtvdXQgPyAnbGVhdmUnIDogJ2VudGVyJ31gO1xuXG4gICAgICAgICAgICBpZiAoc3RhcnRzV2l0aChhbmltYXRpb24sIGFuaW1hdGlvblByZWZpeCkpIHtcblxuICAgICAgICAgICAgICAgIGlmIChvcmlnaW4pIHtcbiAgICAgICAgICAgICAgICAgICAgY2xzICs9IGAgdWstdHJhbnNmb3JtLW9yaWdpbi0ke29yaWdpbn1gO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmIChvdXQpIHtcbiAgICAgICAgICAgICAgICAgICAgY2xzICs9IGAgJHthbmltYXRpb25QcmVmaXh9cmV2ZXJzZWA7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJlc2V0KCk7XG5cbiAgICAgICAgICAgIG9uY2UoZWxlbWVudCwgJ2FuaW1hdGlvbmVuZCBhbmltYXRpb25jYW5jZWwnLCAoe3R5cGV9KSA9PiB7XG5cbiAgICAgICAgICAgICAgICBsZXQgaGFzUmVzZXQgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgIGlmICh0eXBlID09PSAnYW5pbWF0aW9uY2FuY2VsJykge1xuICAgICAgICAgICAgICAgICAgICByZWplY3QoKTtcbiAgICAgICAgICAgICAgICAgICAgcmVzZXQoKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgICAgIFByb21pc2UucmVzb2x2ZSgpLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgaGFzUmVzZXQgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzZXQoKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFoYXNSZXNldCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgYWRkQ2xhc3MoZWxlbWVudCwgY2xzQ2FuY2VsQW5pbWF0aW9uKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHJlbW92ZUNsYXNzKGVsZW1lbnQsIGNsc0NhbmNlbEFuaW1hdGlvbikpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH0sIHtzZWxmOiB0cnVlfSk7XG5cbiAgICAgICAgICAgIGNzcyhlbGVtZW50LCAnYW5pbWF0aW9uRHVyYXRpb24nLCBgJHtkdXJhdGlvbn1tc2ApO1xuICAgICAgICAgICAgYWRkQ2xhc3MoZWxlbWVudCwgY2xzKTtcblxuICAgICAgICAgICAgZnVuY3Rpb24gcmVzZXQoKSB7XG4gICAgICAgICAgICAgICAgY3NzKGVsZW1lbnQsICdhbmltYXRpb25EdXJhdGlvbicsICcnKTtcbiAgICAgICAgICAgICAgICByZW1vdmVDbGFzc2VzKGVsZW1lbnQsIGAke2FuaW1hdGlvblByZWZpeH1cXFxcUypgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KVxuICAgICkpO1xuXG59XG5cbmNvbnN0IGluUHJvZ3Jlc3MgPSBuZXcgUmVnRXhwKGAke2FuaW1hdGlvblByZWZpeH0oZW50ZXJ8bGVhdmUpYCk7XG5leHBvcnQgY29uc3QgQW5pbWF0aW9uID0ge1xuXG4gICAgaW4oZWxlbWVudCwgYW5pbWF0aW9uLCBkdXJhdGlvbiwgb3JpZ2luKSB7XG4gICAgICAgIHJldHVybiBhbmltYXRlKGVsZW1lbnQsIGFuaW1hdGlvbiwgZHVyYXRpb24sIG9yaWdpbiwgZmFsc2UpO1xuICAgIH0sXG5cbiAgICBvdXQoZWxlbWVudCwgYW5pbWF0aW9uLCBkdXJhdGlvbiwgb3JpZ2luKSB7XG4gICAgICAgIHJldHVybiBhbmltYXRlKGVsZW1lbnQsIGFuaW1hdGlvbiwgZHVyYXRpb24sIG9yaWdpbiwgdHJ1ZSk7XG4gICAgfSxcblxuICAgIGluUHJvZ3Jlc3MoZWxlbWVudCkge1xuICAgICAgICByZXR1cm4gaW5Qcm9ncmVzcy50ZXN0KGF0dHIoZWxlbWVudCwgJ2NsYXNzJykpO1xuICAgIH0sXG5cbiAgICBjYW5jZWwoZWxlbWVudCkge1xuICAgICAgICB0cmlnZ2VyKGVsZW1lbnQsICdhbmltYXRpb25jYW5jZWwnKTtcbiAgICB9XG5cbn07XG4iLCJpbXBvcnQge2Nzc30gZnJvbSAnLi9zdHlsZSc7XG5pbXBvcnQge2F0dHJ9IGZyb20gJy4vYXR0cic7XG5pbXBvcnQge2lzVmlzaWJsZX0gZnJvbSAnLi9maWx0ZXInO1xuaW1wb3J0IHtlYWNoLCBlbmRzV2l0aCwgaW5jbHVkZXMsIGlzRG9jdW1lbnQsIGlzTnVtZXJpYywgaXNVbmRlZmluZWQsIGlzV2luZG93LCB0b0Zsb2F0LCB0b05vZGUsIHRvV2luZG93LCB1Y2ZpcnN0fSBmcm9tICcuL2xhbmcnO1xuXG5jb25zdCBkaXJzID0ge1xuICAgIHdpZHRoOiBbJ3gnLCAnbGVmdCcsICdyaWdodCddLFxuICAgIGhlaWdodDogWyd5JywgJ3RvcCcsICdib3R0b20nXVxufTtcblxuZXhwb3J0IGZ1bmN0aW9uIHBvc2l0aW9uQXQoZWxlbWVudCwgdGFyZ2V0LCBlbEF0dGFjaCwgdGFyZ2V0QXR0YWNoLCBlbE9mZnNldCwgdGFyZ2V0T2Zmc2V0LCBmbGlwLCBib3VuZGFyeSkge1xuXG4gICAgZWxBdHRhY2ggPSBnZXRQb3MoZWxBdHRhY2gpO1xuICAgIHRhcmdldEF0dGFjaCA9IGdldFBvcyh0YXJnZXRBdHRhY2gpO1xuXG4gICAgY29uc3QgZmxpcHBlZCA9IHtlbGVtZW50OiBlbEF0dGFjaCwgdGFyZ2V0OiB0YXJnZXRBdHRhY2h9O1xuXG4gICAgaWYgKCFlbGVtZW50IHx8ICF0YXJnZXQpIHtcbiAgICAgICAgcmV0dXJuIGZsaXBwZWQ7XG4gICAgfVxuXG4gICAgY29uc3QgZGltID0gZ2V0RGltZW5zaW9ucyhlbGVtZW50KTtcbiAgICBjb25zdCB0YXJnZXREaW0gPSBnZXREaW1lbnNpb25zKHRhcmdldCk7XG4gICAgY29uc3QgcG9zaXRpb24gPSB0YXJnZXREaW07XG5cbiAgICBtb3ZlVG8ocG9zaXRpb24sIGVsQXR0YWNoLCBkaW0sIC0xKTtcbiAgICBtb3ZlVG8ocG9zaXRpb24sIHRhcmdldEF0dGFjaCwgdGFyZ2V0RGltLCAxKTtcblxuICAgIGVsT2Zmc2V0ID0gZ2V0T2Zmc2V0cyhlbE9mZnNldCwgZGltLndpZHRoLCBkaW0uaGVpZ2h0KTtcbiAgICB0YXJnZXRPZmZzZXQgPSBnZXRPZmZzZXRzKHRhcmdldE9mZnNldCwgdGFyZ2V0RGltLndpZHRoLCB0YXJnZXREaW0uaGVpZ2h0KTtcblxuICAgIGVsT2Zmc2V0Wyd4J10gKz0gdGFyZ2V0T2Zmc2V0Wyd4J107XG4gICAgZWxPZmZzZXRbJ3knXSArPSB0YXJnZXRPZmZzZXRbJ3knXTtcblxuICAgIHBvc2l0aW9uLmxlZnQgKz0gZWxPZmZzZXRbJ3gnXTtcbiAgICBwb3NpdGlvbi50b3AgKz0gZWxPZmZzZXRbJ3knXTtcblxuICAgIGlmIChmbGlwKSB7XG5cbiAgICAgICAgY29uc3QgYm91bmRhcmllcyA9IFtnZXREaW1lbnNpb25zKHRvV2luZG93KGVsZW1lbnQpKV07XG5cbiAgICAgICAgaWYgKGJvdW5kYXJ5KSB7XG4gICAgICAgICAgICBib3VuZGFyaWVzLnVuc2hpZnQoZ2V0RGltZW5zaW9ucyhib3VuZGFyeSkpO1xuICAgICAgICB9XG5cbiAgICAgICAgZWFjaChkaXJzLCAoW2RpciwgYWxpZ24sIGFsaWduRmxpcF0sIHByb3ApID0+IHtcblxuICAgICAgICAgICAgaWYgKCEoZmxpcCA9PT0gdHJ1ZSB8fCBpbmNsdWRlcyhmbGlwLCBkaXIpKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgYm91bmRhcmllcy5zb21lKGJvdW5kYXJ5ID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGVsZW1PZmZzZXQgPSBlbEF0dGFjaFtkaXJdID09PSBhbGlnblxuICAgICAgICAgICAgICAgICAgICA/IC1kaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgOiBlbEF0dGFjaFtkaXJdID09PSBhbGlnbkZsaXBcbiAgICAgICAgICAgICAgICAgICAgICAgID8gZGltW3Byb3BdXG4gICAgICAgICAgICAgICAgICAgICAgICA6IDA7XG5cbiAgICAgICAgICAgICAgICBjb25zdCB0YXJnZXRPZmZzZXQgPSB0YXJnZXRBdHRhY2hbZGlyXSA9PT0gYWxpZ25cbiAgICAgICAgICAgICAgICAgICAgPyB0YXJnZXREaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgOiB0YXJnZXRBdHRhY2hbZGlyXSA9PT0gYWxpZ25GbGlwXG4gICAgICAgICAgICAgICAgICAgICAgICA/IC10YXJnZXREaW1bcHJvcF1cbiAgICAgICAgICAgICAgICAgICAgICAgIDogMDtcblxuICAgICAgICAgICAgICAgIGlmIChwb3NpdGlvblthbGlnbl0gPCBib3VuZGFyeVthbGlnbl0gfHwgcG9zaXRpb25bYWxpZ25dICsgZGltW3Byb3BdID4gYm91bmRhcnlbYWxpZ25GbGlwXSkge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlck9mZnNldCA9IGRpbVtwcm9wXSAvIDI7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNlbnRlclRhcmdldE9mZnNldCA9IHRhcmdldEF0dGFjaFtkaXJdID09PSAnY2VudGVyJyA/IC10YXJnZXREaW1bcHJvcF0gLyAyIDogMDtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZWxBdHRhY2hbZGlyXSA9PT0gJ2NlbnRlcicgJiYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgYXBwbHkoY2VudGVyT2Zmc2V0LCBjZW50ZXJUYXJnZXRPZmZzZXQpXG4gICAgICAgICAgICAgICAgICAgICAgICB8fCBhcHBseSgtY2VudGVyT2Zmc2V0LCAtY2VudGVyVGFyZ2V0T2Zmc2V0KVxuICAgICAgICAgICAgICAgICAgICApIHx8IGFwcGx5KGVsZW1PZmZzZXQsIHRhcmdldE9mZnNldCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmdW5jdGlvbiBhcHBseShlbGVtT2Zmc2V0LCB0YXJnZXRPZmZzZXQpIHtcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBuZXdWYWwgPSBwb3NpdGlvblthbGlnbl0gKyBlbGVtT2Zmc2V0ICsgdGFyZ2V0T2Zmc2V0IC0gZWxPZmZzZXRbZGlyXSAqIDI7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKG5ld1ZhbCA+PSBib3VuZGFyeVthbGlnbl0gJiYgbmV3VmFsICsgZGltW3Byb3BdIDw9IGJvdW5kYXJ5W2FsaWduRmxpcF0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBvc2l0aW9uW2FsaWduXSA9IG5ld1ZhbDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgWydlbGVtZW50JywgJ3RhcmdldCddLmZvckVhY2goZWwgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZsaXBwZWRbZWxdW2Rpcl0gPSAhZWxlbU9mZnNldFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA/IGZsaXBwZWRbZWxdW2Rpcl1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiBmbGlwcGVkW2VsXVtkaXJdID09PSBkaXJzW3Byb3BdWzFdXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA/IGRpcnNbcHJvcF1bMl1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDogZGlyc1twcm9wXVsxXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBvZmZzZXQoZWxlbWVudCwgcG9zaXRpb24pO1xuXG4gICAgcmV0dXJuIGZsaXBwZWQ7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvZmZzZXQoZWxlbWVudCwgY29vcmRpbmF0ZXMpIHtcblxuICAgIGlmICghY29vcmRpbmF0ZXMpIHtcbiAgICAgICAgcmV0dXJuIGdldERpbWVuc2lvbnMoZWxlbWVudCk7XG4gICAgfVxuXG4gICAgY29uc3QgY3VycmVudE9mZnNldCA9IG9mZnNldChlbGVtZW50KTtcbiAgICBjb25zdCBwb3MgPSBjc3MoZWxlbWVudCwgJ3Bvc2l0aW9uJyk7XG5cbiAgICBbJ2xlZnQnLCAndG9wJ10uZm9yRWFjaChwcm9wID0+IHtcbiAgICAgICAgaWYgKHByb3AgaW4gY29vcmRpbmF0ZXMpIHtcbiAgICAgICAgICAgIGNvbnN0IHZhbHVlID0gY3NzKGVsZW1lbnQsIHByb3ApO1xuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIHByb3AsIGNvb3JkaW5hdGVzW3Byb3BdIC0gY3VycmVudE9mZnNldFtwcm9wXVxuICAgICAgICAgICAgICAgICsgdG9GbG9hdChwb3MgPT09ICdhYnNvbHV0ZScgJiYgdmFsdWUgPT09ICdhdXRvJ1xuICAgICAgICAgICAgICAgICAgICA/IHBvc2l0aW9uKGVsZW1lbnQpW3Byb3BdXG4gICAgICAgICAgICAgICAgICAgIDogdmFsdWUpXG4gICAgICAgICAgICApO1xuICAgICAgICB9XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIGdldERpbWVuc2lvbnMoZWxlbWVudCkge1xuXG4gICAgaWYgKCFlbGVtZW50KSB7XG4gICAgICAgIHJldHVybiB7fTtcbiAgICB9XG5cbiAgICBjb25zdCB7cGFnZVlPZmZzZXQ6IHRvcCwgcGFnZVhPZmZzZXQ6IGxlZnR9ID0gdG9XaW5kb3coZWxlbWVudCk7XG5cbiAgICBpZiAoaXNXaW5kb3coZWxlbWVudCkpIHtcblxuICAgICAgICBjb25zdCBoZWlnaHQgPSBlbGVtZW50LmlubmVySGVpZ2h0O1xuICAgICAgICBjb25zdCB3aWR0aCA9IGVsZW1lbnQuaW5uZXJXaWR0aDtcblxuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgdG9wLFxuICAgICAgICAgICAgbGVmdCxcbiAgICAgICAgICAgIGhlaWdodCxcbiAgICAgICAgICAgIHdpZHRoLFxuICAgICAgICAgICAgYm90dG9tOiB0b3AgKyBoZWlnaHQsXG4gICAgICAgICAgICByaWdodDogbGVmdCArIHdpZHRoXG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgbGV0IHN0eWxlLCBoaWRkZW47XG5cbiAgICBpZiAoIWlzVmlzaWJsZShlbGVtZW50KSAmJiBjc3MoZWxlbWVudCwgJ2Rpc3BsYXknKSA9PT0gJ25vbmUnKSB7XG5cbiAgICAgICAgc3R5bGUgPSBhdHRyKGVsZW1lbnQsICdzdHlsZScpO1xuICAgICAgICBoaWRkZW4gPSBhdHRyKGVsZW1lbnQsICdoaWRkZW4nKTtcblxuICAgICAgICBhdHRyKGVsZW1lbnQsIHtcbiAgICAgICAgICAgIHN0eWxlOiBgJHtzdHlsZSB8fCAnJ307ZGlzcGxheTpibG9jayAhaW1wb3J0YW50O2AsXG4gICAgICAgICAgICBoaWRkZW46IG51bGxcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgIGNvbnN0IHJlY3QgPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuXG4gICAgaWYgKCFpc1VuZGVmaW5lZChzdHlsZSkpIHtcbiAgICAgICAgYXR0cihlbGVtZW50LCB7c3R5bGUsIGhpZGRlbn0pO1xuICAgIH1cblxuICAgIHJldHVybiB7XG4gICAgICAgIGhlaWdodDogcmVjdC5oZWlnaHQsXG4gICAgICAgIHdpZHRoOiByZWN0LndpZHRoLFxuICAgICAgICB0b3A6IHJlY3QudG9wICsgdG9wLFxuICAgICAgICBsZWZ0OiByZWN0LmxlZnQgKyBsZWZ0LFxuICAgICAgICBib3R0b206IHJlY3QuYm90dG9tICsgdG9wLFxuICAgICAgICByaWdodDogcmVjdC5yaWdodCArIGxlZnRcbiAgICB9O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcG9zaXRpb24oZWxlbWVudCwgcGFyZW50KSB7XG4gICAgY29uc3QgZWxlbWVudE9mZnNldCA9IG9mZnNldChlbGVtZW50KTtcbiAgICBjb25zdCBwYXJlbnRPZmZzZXQgPSBvZmZzZXQocGFyZW50IHx8IHRvTm9kZShlbGVtZW50KS5vZmZzZXRQYXJlbnQgfHwgdG9XaW5kb3coZWxlbWVudCkuZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50KTtcblxuICAgIHJldHVybiB7dG9wOiBlbGVtZW50T2Zmc2V0LnRvcCAtIHBhcmVudE9mZnNldC50b3AsIGxlZnQ6IGVsZW1lbnRPZmZzZXQubGVmdCAtIHBhcmVudE9mZnNldC5sZWZ0fTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG9mZnNldFBvc2l0aW9uKGVsZW1lbnQpIHtcbiAgICBjb25zdCBvZmZzZXQgPSBbMCwgMF07XG5cbiAgICBlbGVtZW50ID0gdG9Ob2RlKGVsZW1lbnQpO1xuXG4gICAgZG8ge1xuXG4gICAgICAgIG9mZnNldFswXSArPSBlbGVtZW50Lm9mZnNldFRvcDtcbiAgICAgICAgb2Zmc2V0WzFdICs9IGVsZW1lbnQub2Zmc2V0TGVmdDtcblxuICAgICAgICBpZiAoY3NzKGVsZW1lbnQsICdwb3NpdGlvbicpID09PSAnZml4ZWQnKSB7XG4gICAgICAgICAgICBjb25zdCB3aW4gPSB0b1dpbmRvdyhlbGVtZW50KTtcbiAgICAgICAgICAgIG9mZnNldFswXSArPSB3aW4ucGFnZVlPZmZzZXQ7XG4gICAgICAgICAgICBvZmZzZXRbMV0gKz0gd2luLnBhZ2VYT2Zmc2V0O1xuICAgICAgICAgICAgcmV0dXJuIG9mZnNldDtcbiAgICAgICAgfVxuXG4gICAgfSB3aGlsZSAoKGVsZW1lbnQgPSBlbGVtZW50Lm9mZnNldFBhcmVudCkpO1xuXG4gICAgcmV0dXJuIG9mZnNldDtcbn1cblxuZXhwb3J0IGNvbnN0IGhlaWdodCA9IGRpbWVuc2lvbignaGVpZ2h0Jyk7XG5leHBvcnQgY29uc3Qgd2lkdGggPSBkaW1lbnNpb24oJ3dpZHRoJyk7XG5cbmZ1bmN0aW9uIGRpbWVuc2lvbihwcm9wKSB7XG4gICAgY29uc3QgcHJvcE5hbWUgPSB1Y2ZpcnN0KHByb3ApO1xuICAgIHJldHVybiAoZWxlbWVudCwgdmFsdWUpID0+IHtcblxuICAgICAgICBpZiAoaXNVbmRlZmluZWQodmFsdWUpKSB7XG5cbiAgICAgICAgICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSkge1xuICAgICAgICAgICAgICAgIHJldHVybiBlbGVtZW50W2Bpbm5lciR7cHJvcE5hbWV9YF07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChpc0RvY3VtZW50KGVsZW1lbnQpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgZG9jID0gZWxlbWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gICAgICAgICAgICAgICAgcmV0dXJuIE1hdGgubWF4KGRvY1tgb2Zmc2V0JHtwcm9wTmFtZX1gXSwgZG9jW2BzY3JvbGwke3Byb3BOYW1lfWBdKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZWxlbWVudCA9IHRvTm9kZShlbGVtZW50KTtcblxuICAgICAgICAgICAgdmFsdWUgPSBjc3MoZWxlbWVudCwgcHJvcCk7XG4gICAgICAgICAgICB2YWx1ZSA9IHZhbHVlID09PSAnYXV0bycgPyBlbGVtZW50W2BvZmZzZXQke3Byb3BOYW1lfWBdIDogdG9GbG9hdCh2YWx1ZSkgfHwgMDtcblxuICAgICAgICAgICAgcmV0dXJuIHZhbHVlIC0gYm94TW9kZWxBZGp1c3QoZWxlbWVudCwgcHJvcCk7XG5cbiAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgY3NzKGVsZW1lbnQsIHByb3AsICF2YWx1ZSAmJiB2YWx1ZSAhPT0gMFxuICAgICAgICAgICAgICAgID8gJydcbiAgICAgICAgICAgICAgICA6ICt2YWx1ZSArIGJveE1vZGVsQWRqdXN0KGVsZW1lbnQsIHByb3ApICsgJ3B4J1xuICAgICAgICAgICAgKTtcblxuICAgICAgICB9XG5cbiAgICB9O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYm94TW9kZWxBZGp1c3QoZWxlbWVudCwgcHJvcCwgc2l6aW5nID0gJ2JvcmRlci1ib3gnKSB7XG4gICAgcmV0dXJuIGNzcyhlbGVtZW50LCAnYm94U2l6aW5nJykgPT09IHNpemluZ1xuICAgICAgICA/IGRpcnNbcHJvcF0uc2xpY2UoMSkubWFwKHVjZmlyc3QpLnJlZHVjZSgodmFsdWUsIHByb3ApID0+XG4gICAgICAgICAgICB2YWx1ZVxuICAgICAgICAgICAgKyB0b0Zsb2F0KGNzcyhlbGVtZW50LCBgcGFkZGluZyR7cHJvcH1gKSlcbiAgICAgICAgICAgICsgdG9GbG9hdChjc3MoZWxlbWVudCwgYGJvcmRlciR7cHJvcH1XaWR0aGApKVxuICAgICAgICAgICAgLCAwKVxuICAgICAgICA6IDA7XG59XG5cbmZ1bmN0aW9uIG1vdmVUbyhwb3NpdGlvbiwgYXR0YWNoLCBkaW0sIGZhY3Rvcikge1xuICAgIGVhY2goZGlycywgKFtkaXIsIGFsaWduLCBhbGlnbkZsaXBdLCBwcm9wKSA9PiB7XG4gICAgICAgIGlmIChhdHRhY2hbZGlyXSA9PT0gYWxpZ25GbGlwKSB7XG4gICAgICAgICAgICBwb3NpdGlvblthbGlnbl0gKz0gZGltW3Byb3BdICogZmFjdG9yO1xuICAgICAgICB9IGVsc2UgaWYgKGF0dGFjaFtkaXJdID09PSAnY2VudGVyJykge1xuICAgICAgICAgICAgcG9zaXRpb25bYWxpZ25dICs9IGRpbVtwcm9wXSAqIGZhY3RvciAvIDI7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gZ2V0UG9zKHBvcykge1xuXG4gICAgY29uc3QgeCA9IC9sZWZ0fGNlbnRlcnxyaWdodC87XG4gICAgY29uc3QgeSA9IC90b3B8Y2VudGVyfGJvdHRvbS87XG5cbiAgICBwb3MgPSAocG9zIHx8ICcnKS5zcGxpdCgnICcpO1xuXG4gICAgaWYgKHBvcy5sZW5ndGggPT09IDEpIHtcbiAgICAgICAgcG9zID0geC50ZXN0KHBvc1swXSlcbiAgICAgICAgICAgID8gcG9zLmNvbmNhdChbJ2NlbnRlciddKVxuICAgICAgICAgICAgOiB5LnRlc3QocG9zWzBdKVxuICAgICAgICAgICAgICAgID8gWydjZW50ZXInXS5jb25jYXQocG9zKVxuICAgICAgICAgICAgICAgIDogWydjZW50ZXInLCAnY2VudGVyJ107XG4gICAgfVxuXG4gICAgcmV0dXJuIHtcbiAgICAgICAgeDogeC50ZXN0KHBvc1swXSkgPyBwb3NbMF0gOiAnY2VudGVyJyxcbiAgICAgICAgeTogeS50ZXN0KHBvc1sxXSkgPyBwb3NbMV0gOiAnY2VudGVyJ1xuICAgIH07XG59XG5cbmZ1bmN0aW9uIGdldE9mZnNldHMob2Zmc2V0cywgd2lkdGgsIGhlaWdodCkge1xuXG4gICAgY29uc3QgW3gsIHldID0gKG9mZnNldHMgfHwgJycpLnNwbGl0KCcgJyk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgICB4OiB4ID8gdG9GbG9hdCh4KSAqIChlbmRzV2l0aCh4LCAnJScpID8gd2lkdGggLyAxMDAgOiAxKSA6IDAsXG4gICAgICAgIHk6IHkgPyB0b0Zsb2F0KHkpICogKGVuZHNXaXRoKHksICclJykgPyBoZWlnaHQgLyAxMDAgOiAxKSA6IDBcbiAgICB9O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZmxpcFBvc2l0aW9uKHBvcykge1xuICAgIHN3aXRjaCAocG9zKSB7XG4gICAgICAgIGNhc2UgJ2xlZnQnOlxuICAgICAgICAgICAgcmV0dXJuICdyaWdodCc7XG4gICAgICAgIGNhc2UgJ3JpZ2h0JzpcbiAgICAgICAgICAgIHJldHVybiAnbGVmdCc7XG4gICAgICAgIGNhc2UgJ3RvcCc6XG4gICAgICAgICAgICByZXR1cm4gJ2JvdHRvbSc7XG4gICAgICAgIGNhc2UgJ2JvdHRvbSc6XG4gICAgICAgICAgICByZXR1cm4gJ3RvcCc7XG4gICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICByZXR1cm4gcG9zO1xuICAgIH1cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHRvUHgodmFsdWUsIHByb3BlcnR5ID0gJ3dpZHRoJywgZWxlbWVudCA9IHdpbmRvdykge1xuICAgIHJldHVybiBpc051bWVyaWModmFsdWUpXG4gICAgICAgID8gK3ZhbHVlXG4gICAgICAgIDogZW5kc1dpdGgodmFsdWUsICd2aCcpXG4gICAgICAgICAgICA/IHBlcmNlbnQoaGVpZ2h0KHRvV2luZG93KGVsZW1lbnQpKSwgdmFsdWUpXG4gICAgICAgICAgICA6IGVuZHNXaXRoKHZhbHVlLCAndncnKVxuICAgICAgICAgICAgICAgID8gcGVyY2VudCh3aWR0aCh0b1dpbmRvdyhlbGVtZW50KSksIHZhbHVlKVxuICAgICAgICAgICAgICAgIDogZW5kc1dpdGgodmFsdWUsICclJylcbiAgICAgICAgICAgICAgICAgICAgPyBwZXJjZW50KGdldERpbWVuc2lvbnMoZWxlbWVudClbcHJvcGVydHldLCB2YWx1ZSlcbiAgICAgICAgICAgICAgICAgICAgOiB0b0Zsb2F0KHZhbHVlKTtcbn1cblxuZnVuY3Rpb24gcGVyY2VudChiYXNlLCB2YWx1ZSkge1xuICAgIHJldHVybiBiYXNlICogdG9GbG9hdCh2YWx1ZSkgLyAxMDA7XG59XG4iLCJpbXBvcnQge1Byb21pc2V9IGZyb20gJy4vcHJvbWlzZSc7XG4vKlxuICAgIEJhc2VkIG9uOlxuICAgIENvcHlyaWdodCAoYykgMjAxNiBXaWxzb24gUGFnZSB3aWxzb25wYWdlQG1lLmNvbVxuICAgIGh0dHBzOi8vZ2l0aHViLmNvbS93aWxzb25wYWdlL2Zhc3Rkb21cbiovXG5cbmV4cG9ydCBjb25zdCBmYXN0ZG9tID0ge1xuXG4gICAgcmVhZHM6IFtdLFxuICAgIHdyaXRlczogW10sXG5cbiAgICByZWFkKHRhc2spIHtcbiAgICAgICAgdGhpcy5yZWFkcy5wdXNoKHRhc2spO1xuICAgICAgICBzY2hlZHVsZUZsdXNoKCk7XG4gICAgICAgIHJldHVybiB0YXNrO1xuICAgIH0sXG5cbiAgICB3cml0ZSh0YXNrKSB7XG4gICAgICAgIHRoaXMud3JpdGVzLnB1c2godGFzayk7XG4gICAgICAgIHNjaGVkdWxlRmx1c2goKTtcbiAgICAgICAgcmV0dXJuIHRhc2s7XG4gICAgfSxcblxuICAgIGNsZWFyKHRhc2spIHtcbiAgICAgICAgcmV0dXJuIHJlbW92ZSh0aGlzLnJlYWRzLCB0YXNrKSB8fCByZW1vdmUodGhpcy53cml0ZXMsIHRhc2spO1xuICAgIH0sXG5cbiAgICBmbHVzaFxuXG59O1xuXG5mdW5jdGlvbiBmbHVzaChyZWN1cnNpb24gPSAxKSB7XG4gICAgcnVuVGFza3MoZmFzdGRvbS5yZWFkcyk7XG4gICAgcnVuVGFza3MoZmFzdGRvbS53cml0ZXMuc3BsaWNlKDAsIGZhc3Rkb20ud3JpdGVzLmxlbmd0aCkpO1xuXG4gICAgZmFzdGRvbS5zY2hlZHVsZWQgPSBmYWxzZTtcblxuICAgIGlmIChmYXN0ZG9tLnJlYWRzLmxlbmd0aCB8fCBmYXN0ZG9tLndyaXRlcy5sZW5ndGgpIHtcbiAgICAgICAgc2NoZWR1bGVGbHVzaChyZWN1cnNpb24gKyAxKTtcbiAgICB9XG59XG5cbmNvbnN0IFJFQ1VSU0lPTl9MSU1JVCA9IDU7XG5mdW5jdGlvbiBzY2hlZHVsZUZsdXNoKHJlY3Vyc2lvbikge1xuICAgIGlmICghZmFzdGRvbS5zY2hlZHVsZWQpIHtcbiAgICAgICAgZmFzdGRvbS5zY2hlZHVsZWQgPSB0cnVlO1xuICAgICAgICBpZiAocmVjdXJzaW9uID4gUkVDVVJTSU9OX0xJTUlUKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ01heGltdW0gcmVjdXJzaW9uIGxpbWl0IHJlYWNoZWQuJyk7XG4gICAgICAgIH0gZWxzZSBpZiAocmVjdXJzaW9uKSB7XG4gICAgICAgICAgICBQcm9taXNlLnJlc29sdmUoKS50aGVuKCgpID0+IGZsdXNoKHJlY3Vyc2lvbikpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IGZsdXNoKCkpO1xuICAgICAgICB9XG4gICAgfVxufVxuXG5mdW5jdGlvbiBydW5UYXNrcyh0YXNrcykge1xuICAgIGxldCB0YXNrO1xuICAgIHdoaWxlICgodGFzayA9IHRhc2tzLnNoaWZ0KCkpKSB7XG4gICAgICAgIHRhc2soKTtcbiAgICB9XG59XG5cbmZ1bmN0aW9uIHJlbW92ZShhcnJheSwgaXRlbSkge1xuICAgIGNvbnN0IGluZGV4ID0gYXJyYXkuaW5kZXhPZihpdGVtKTtcbiAgICByZXR1cm4gISF+aW5kZXggJiYgISFhcnJheS5zcGxpY2UoaW5kZXgsIDEpO1xufVxuIiwiaW1wb3J0IHtnZXRFdmVudFBvcywgb259IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtsYXN0LCBwb2ludEluUmVjdH0gZnJvbSAnLi9sYW5nJztcbmltcG9ydCB7b2Zmc2V0fSBmcm9tICcuL2RpbWVuc2lvbnMnO1xuXG5leHBvcnQgZnVuY3Rpb24gTW91c2VUcmFja2VyKCkge31cblxuTW91c2VUcmFja2VyLnByb3RvdHlwZSA9IHtcblxuICAgIHBvc2l0aW9uczogW10sXG5cbiAgICBpbml0KCkge1xuXG4gICAgICAgIHRoaXMucG9zaXRpb25zID0gW107XG5cbiAgICAgICAgbGV0IHBvc2l0aW9uO1xuICAgICAgICB0aGlzLnVuYmluZCA9IG9uKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgZSA9PiBwb3NpdGlvbiA9IGdldEV2ZW50UG9zKGUsICdwYWdlJykpO1xuICAgICAgICB0aGlzLmludGVydmFsID0gc2V0SW50ZXJ2YWwoKCkgPT4ge1xuXG4gICAgICAgICAgICBpZiAoIXBvc2l0aW9uKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLnBvc2l0aW9ucy5wdXNoKHBvc2l0aW9uKTtcblxuICAgICAgICAgICAgaWYgKHRoaXMucG9zaXRpb25zLmxlbmd0aCA+IDUpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnBvc2l0aW9ucy5zaGlmdCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LCA1MCk7XG5cbiAgICB9LFxuXG4gICAgY2FuY2VsKCkge1xuICAgICAgICB0aGlzLnVuYmluZCAmJiB0aGlzLnVuYmluZCgpO1xuICAgICAgICB0aGlzLmludGVydmFsICYmIGNsZWFySW50ZXJ2YWwodGhpcy5pbnRlcnZhbCk7XG4gICAgfSxcblxuICAgIG1vdmVzVG8odGFyZ2V0KSB7XG5cbiAgICAgICAgaWYgKHRoaXMucG9zaXRpb25zLmxlbmd0aCA8IDIpIHtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHAgPSBvZmZzZXQodGFyZ2V0KTtcbiAgICAgICAgY29uc3Qge2xlZnQsIHJpZ2h0LCB0b3AsIGJvdHRvbX0gPSBwO1xuXG4gICAgICAgIGNvbnN0IFtwcmV2UG9zaXRpb25dID0gdGhpcy5wb3NpdGlvbnM7XG4gICAgICAgIGNvbnN0IHBvc2l0aW9uID0gbGFzdCh0aGlzLnBvc2l0aW9ucyk7XG4gICAgICAgIGNvbnN0IHBhdGggPSBbcHJldlBvc2l0aW9uLCBwb3NpdGlvbl07XG5cbiAgICAgICAgaWYgKHBvaW50SW5SZWN0KHBvc2l0aW9uLCBwKSkge1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgZGlhZ29uYWxzID0gW1t7eDogbGVmdCwgeTogdG9wfSwge3g6IHJpZ2h0LCB5OiBib3R0b219XSwgW3t4OiBsZWZ0LCB5OiBib3R0b219LCB7eDogcmlnaHQsIHk6IHRvcH1dXTtcblxuICAgICAgICByZXR1cm4gZGlhZ29uYWxzLnNvbWUoZGlhZ29uYWwgPT4ge1xuICAgICAgICAgICAgY29uc3QgaW50ZXJzZWN0aW9uID0gaW50ZXJzZWN0KHBhdGgsIGRpYWdvbmFsKTtcbiAgICAgICAgICAgIHJldHVybiBpbnRlcnNlY3Rpb24gJiYgcG9pbnRJblJlY3QoaW50ZXJzZWN0aW9uLCBwKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG59O1xuXG4vLyBJbnNwaXJlZCBieSBodHRwOi8vcGF1bGJvdXJrZS5uZXQvZ2VvbWV0cnkvcG9pbnRsaW5lcGxhbmUvXG5mdW5jdGlvbiBpbnRlcnNlY3QoW3t4OiB4MSwgeTogeTF9LCB7eDogeDIsIHk6IHkyfV0sIFt7eDogeDMsIHk6IHkzfSwge3g6IHg0LCB5OiB5NH1dKSB7XG5cbiAgICBjb25zdCBkZW5vbWluYXRvciA9ICh5NCAtIHkzKSAqICh4MiAtIHgxKSAtICh4NCAtIHgzKSAqICh5MiAtIHkxKTtcblxuICAgIC8vIExpbmVzIGFyZSBwYXJhbGxlbFxuICAgIGlmIChkZW5vbWluYXRvciA9PT0gMCkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY29uc3QgdWEgPSAoKHg0IC0geDMpICogKHkxIC0geTMpIC0gKHk0IC0geTMpICogKHgxIC0geDMpKSAvIGRlbm9taW5hdG9yO1xuXG4gICAgaWYgKHVhIDwgMCkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgLy8gUmV0dXJuIGEgb2JqZWN0IHdpdGggdGhlIHggYW5kIHkgY29vcmRpbmF0ZXMgb2YgdGhlIGludGVyc2VjdGlvblxuICAgIHJldHVybiB7eDogeDEgKyB1YSAqICh4MiAtIHgxKSwgeTogeTEgKyB1YSAqICh5MiAtIHkxKX07XG59XG4iLCJpbXBvcnQge2Fzc2lnbiwgaGFzT3duLCBpbmNsdWRlcywgaXNBcnJheSwgaXNGdW5jdGlvbiwgaXNVbmRlZmluZWQsIHNvcnRCeSwgc3RhcnRzV2l0aH0gZnJvbSAnLi9sYW5nJztcblxuY29uc3Qgc3RyYXRzID0ge307XG5cbnN0cmF0cy5ldmVudHMgPVxuc3RyYXRzLmNyZWF0ZWQgPVxuc3RyYXRzLmJlZm9yZUNvbm5lY3QgPVxuc3RyYXRzLmNvbm5lY3RlZCA9XG5zdHJhdHMuYmVmb3JlRGlzY29ubmVjdCA9XG5zdHJhdHMuZGlzY29ubmVjdGVkID1cbnN0cmF0cy5kZXN0cm95ID0gY29uY2F0U3RyYXQ7XG5cbi8vIGFyZ3Mgc3RyYXRlZ3lcbnN0cmF0cy5hcmdzID0gZnVuY3Rpb24gKHBhcmVudFZhbCwgY2hpbGRWYWwpIHtcbiAgICByZXR1cm4gY2hpbGRWYWwgIT09IGZhbHNlICYmIGNvbmNhdFN0cmF0KGNoaWxkVmFsIHx8IHBhcmVudFZhbCk7XG59O1xuXG4vLyB1cGRhdGUgc3RyYXRlZ3lcbnN0cmF0cy51cGRhdGUgPSBmdW5jdGlvbiAocGFyZW50VmFsLCBjaGlsZFZhbCkge1xuICAgIHJldHVybiBzb3J0QnkoY29uY2F0U3RyYXQocGFyZW50VmFsLCBpc0Z1bmN0aW9uKGNoaWxkVmFsKSA/IHtyZWFkOiBjaGlsZFZhbH0gOiBjaGlsZFZhbCksICdvcmRlcicpO1xufTtcblxuLy8gcHJvcGVydHkgc3RyYXRlZ3lcbnN0cmF0cy5wcm9wcyA9IGZ1bmN0aW9uIChwYXJlbnRWYWwsIGNoaWxkVmFsKSB7XG5cbiAgICBpZiAoaXNBcnJheShjaGlsZFZhbCkpIHtcbiAgICAgICAgY2hpbGRWYWwgPSBjaGlsZFZhbC5yZWR1Y2UoKHZhbHVlLCBrZXkpID0+IHtcbiAgICAgICAgICAgIHZhbHVlW2tleV0gPSBTdHJpbmc7XG4gICAgICAgICAgICByZXR1cm4gdmFsdWU7XG4gICAgICAgIH0sIHt9KTtcbiAgICB9XG5cbiAgICByZXR1cm4gc3RyYXRzLm1ldGhvZHMocGFyZW50VmFsLCBjaGlsZFZhbCk7XG59O1xuXG4vLyBleHRlbmQgc3RyYXRlZ3lcbnN0cmF0cy5jb21wdXRlZCA9XG5zdHJhdHMubWV0aG9kcyA9IGZ1bmN0aW9uIChwYXJlbnRWYWwsIGNoaWxkVmFsKSB7XG4gICAgcmV0dXJuIGNoaWxkVmFsXG4gICAgICAgID8gcGFyZW50VmFsXG4gICAgICAgICAgICA/IGFzc2lnbih7fSwgcGFyZW50VmFsLCBjaGlsZFZhbClcbiAgICAgICAgICAgIDogY2hpbGRWYWxcbiAgICAgICAgOiBwYXJlbnRWYWw7XG59O1xuXG4vLyBkYXRhIHN0cmF0ZWd5XG5zdHJhdHMuZGF0YSA9IGZ1bmN0aW9uIChwYXJlbnRWYWwsIGNoaWxkVmFsLCB2bSkge1xuXG4gICAgaWYgKCF2bSkge1xuXG4gICAgICAgIGlmICghY2hpbGRWYWwpIHtcbiAgICAgICAgICAgIHJldHVybiBwYXJlbnRWYWw7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIXBhcmVudFZhbCkge1xuICAgICAgICAgICAgcmV0dXJuIGNoaWxkVmFsO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGZ1bmN0aW9uICh2bSkge1xuICAgICAgICAgICAgcmV0dXJuIG1lcmdlRm5EYXRhKHBhcmVudFZhbCwgY2hpbGRWYWwsIHZtKTtcbiAgICAgICAgfTtcblxuICAgIH1cblxuICAgIHJldHVybiBtZXJnZUZuRGF0YShwYXJlbnRWYWwsIGNoaWxkVmFsLCB2bSk7XG59O1xuXG5mdW5jdGlvbiBtZXJnZUZuRGF0YShwYXJlbnRWYWwsIGNoaWxkVmFsLCB2bSkge1xuICAgIHJldHVybiBzdHJhdHMuY29tcHV0ZWQoXG4gICAgICAgIGlzRnVuY3Rpb24ocGFyZW50VmFsKVxuICAgICAgICAgICAgPyBwYXJlbnRWYWwuY2FsbCh2bSwgdm0pXG4gICAgICAgICAgICA6IHBhcmVudFZhbCxcbiAgICAgICAgaXNGdW5jdGlvbihjaGlsZFZhbClcbiAgICAgICAgICAgID8gY2hpbGRWYWwuY2FsbCh2bSwgdm0pXG4gICAgICAgICAgICA6IGNoaWxkVmFsXG4gICAgKTtcbn1cblxuLy8gY29uY2F0IHN0cmF0ZWd5XG5mdW5jdGlvbiBjb25jYXRTdHJhdChwYXJlbnRWYWwsIGNoaWxkVmFsKSB7XG5cbiAgICBwYXJlbnRWYWwgPSBwYXJlbnRWYWwgJiYgIWlzQXJyYXkocGFyZW50VmFsKSA/IFtwYXJlbnRWYWxdIDogcGFyZW50VmFsO1xuXG4gICAgcmV0dXJuIGNoaWxkVmFsXG4gICAgICAgID8gcGFyZW50VmFsXG4gICAgICAgICAgICA/IHBhcmVudFZhbC5jb25jYXQoY2hpbGRWYWwpXG4gICAgICAgICAgICA6IGlzQXJyYXkoY2hpbGRWYWwpXG4gICAgICAgICAgICAgICAgPyBjaGlsZFZhbFxuICAgICAgICAgICAgICAgIDogW2NoaWxkVmFsXVxuICAgICAgICA6IHBhcmVudFZhbDtcbn1cblxuLy8gZGVmYXVsdCBzdHJhdGVneVxuZnVuY3Rpb24gZGVmYXVsdFN0cmF0KHBhcmVudFZhbCwgY2hpbGRWYWwpIHtcbiAgICByZXR1cm4gaXNVbmRlZmluZWQoY2hpbGRWYWwpID8gcGFyZW50VmFsIDogY2hpbGRWYWw7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBtZXJnZU9wdGlvbnMocGFyZW50LCBjaGlsZCwgdm0pIHtcblxuICAgIGNvbnN0IG9wdGlvbnMgPSB7fTtcblxuICAgIGlmIChpc0Z1bmN0aW9uKGNoaWxkKSkge1xuICAgICAgICBjaGlsZCA9IGNoaWxkLm9wdGlvbnM7XG4gICAgfVxuXG4gICAgaWYgKGNoaWxkLmV4dGVuZHMpIHtcbiAgICAgICAgcGFyZW50ID0gbWVyZ2VPcHRpb25zKHBhcmVudCwgY2hpbGQuZXh0ZW5kcywgdm0pO1xuICAgIH1cblxuICAgIGlmIChjaGlsZC5taXhpbnMpIHtcbiAgICAgICAgZm9yIChsZXQgaSA9IDAsIGwgPSBjaGlsZC5taXhpbnMubGVuZ3RoOyBpIDwgbDsgaSsrKSB7XG4gICAgICAgICAgICBwYXJlbnQgPSBtZXJnZU9wdGlvbnMocGFyZW50LCBjaGlsZC5taXhpbnNbaV0sIHZtKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZvciAoY29uc3Qga2V5IGluIHBhcmVudCkge1xuICAgICAgICBtZXJnZUtleShrZXkpO1xuICAgIH1cblxuICAgIGZvciAoY29uc3Qga2V5IGluIGNoaWxkKSB7XG4gICAgICAgIGlmICghaGFzT3duKHBhcmVudCwga2V5KSkge1xuICAgICAgICAgICAgbWVyZ2VLZXkoa2V5KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIG1lcmdlS2V5KGtleSkge1xuICAgICAgICBvcHRpb25zW2tleV0gPSAoc3RyYXRzW2tleV0gfHwgZGVmYXVsdFN0cmF0KShwYXJlbnRba2V5XSwgY2hpbGRba2V5XSwgdm0pO1xuICAgIH1cblxuICAgIHJldHVybiBvcHRpb25zO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcGFyc2VPcHRpb25zKG9wdGlvbnMsIGFyZ3MgPSBbXSkge1xuXG4gICAgdHJ5IHtcblxuICAgICAgICByZXR1cm4gIW9wdGlvbnNcbiAgICAgICAgICAgID8ge31cbiAgICAgICAgICAgIDogc3RhcnRzV2l0aChvcHRpb25zLCAneycpXG4gICAgICAgICAgICAgICAgPyBKU09OLnBhcnNlKG9wdGlvbnMpXG4gICAgICAgICAgICAgICAgOiBhcmdzLmxlbmd0aCAmJiAhaW5jbHVkZXMob3B0aW9ucywgJzonKVxuICAgICAgICAgICAgICAgICAgICA/ICh7W2FyZ3NbMF1dOiBvcHRpb25zfSlcbiAgICAgICAgICAgICAgICAgICAgOiBvcHRpb25zLnNwbGl0KCc7JykucmVkdWNlKChvcHRpb25zLCBvcHRpb24pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IFtrZXksIHZhbHVlXSA9IG9wdGlvbi5zcGxpdCgvOiguKikvKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChrZXkgJiYgIWlzVW5kZWZpbmVkKHZhbHVlKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnNba2V5LnRyaW0oKV0gPSB2YWx1ZS50cmltKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb3B0aW9ucztcbiAgICAgICAgICAgICAgICAgICAgfSwge30pO1xuXG4gICAgfSBjYXRjaCAoZSkge1xuICAgICAgICByZXR1cm4ge307XG4gICAgfVxuXG59XG4iLCJpbXBvcnQge2F0dHJ9IGZyb20gJy4vYXR0cic7XG5pbXBvcnQge29uY2V9IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHtQcm9taXNlfSBmcm9tICcuL3Byb21pc2UnO1xuaW1wb3J0IHthc3NpZ24sIGluY2x1ZGVzLCBpc1N0cmluZywgbm9vcCwgdG9Ob2RlfSBmcm9tICcuL2xhbmcnO1xuXG5sZXQgaWQgPSAwO1xuXG5leHBvcnQgY2xhc3MgUGxheWVyIHtcblxuICAgIGNvbnN0cnVjdG9yKGVsKSB7XG4gICAgICAgIHRoaXMuaWQgPSArK2lkO1xuICAgICAgICB0aGlzLmVsID0gdG9Ob2RlKGVsKTtcbiAgICB9XG5cbiAgICBpc1ZpZGVvKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5pc1lvdXR1YmUoKSB8fCB0aGlzLmlzVmltZW8oKSB8fCB0aGlzLmlzSFRNTDUoKTtcbiAgICB9XG5cbiAgICBpc0hUTUw1KCkge1xuICAgICAgICByZXR1cm4gdGhpcy5lbC50YWdOYW1lID09PSAnVklERU8nO1xuICAgIH1cblxuICAgIGlzSUZyYW1lKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5lbC50YWdOYW1lID09PSAnSUZSQU1FJztcbiAgICB9XG5cbiAgICBpc1lvdXR1YmUoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmlzSUZyYW1lKCkgJiYgISF0aGlzLmVsLnNyYy5tYXRjaCgvXFwvXFwvLio/eW91dHViZSgtbm9jb29raWUpP1xcLlthLXpdK1xcLyh3YXRjaFxcP3Y9W14mXFxzXSt8ZW1iZWQpfHlvdXR1XFwuYmVcXC8uKi8pO1xuICAgIH1cblxuICAgIGlzVmltZW8oKSB7XG4gICAgICAgIHJldHVybiB0aGlzLmlzSUZyYW1lKCkgJiYgISF0aGlzLmVsLnNyYy5tYXRjaCgvdmltZW9cXC5jb21cXC92aWRlb1xcLy4qLyk7XG4gICAgfVxuXG4gICAgZW5hYmxlQXBpKCkge1xuXG4gICAgICAgIGlmICh0aGlzLnJlYWR5KSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5yZWFkeTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHlvdXR1YmUgPSB0aGlzLmlzWW91dHViZSgpO1xuICAgICAgICBjb25zdCB2aW1lbyA9IHRoaXMuaXNWaW1lbygpO1xuXG4gICAgICAgIGxldCBwb2xsZXI7XG5cbiAgICAgICAgaWYgKHlvdXR1YmUgfHwgdmltZW8pIHtcblxuICAgICAgICAgICAgcmV0dXJuIHRoaXMucmVhZHkgPSBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcblxuICAgICAgICAgICAgICAgIG9uY2UodGhpcy5lbCwgJ2xvYWQnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGlmICh5b3V0dWJlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBsaXN0ZW5lciA9ICgpID0+IHBvc3QodGhpcy5lbCwge2V2ZW50OiAnbGlzdGVuaW5nJywgaWQ6IHRoaXMuaWR9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBvbGxlciA9IHNldEludGVydmFsKGxpc3RlbmVyLCAxMDApO1xuICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGVuZXIoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgbGlzdGVuKGRhdGEgPT4geW91dHViZSAmJiBkYXRhLmlkID09PSB0aGlzLmlkICYmIGRhdGEuZXZlbnQgPT09ICdvblJlYWR5JyB8fCB2aW1lbyAmJiBOdW1iZXIoZGF0YS5wbGF5ZXJfaWQpID09PSB0aGlzLmlkKVxuICAgICAgICAgICAgICAgICAgICAudGhlbigoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBwb2xsZXIgJiYgY2xlYXJJbnRlcnZhbChwb2xsZXIpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGF0dHIodGhpcy5lbCwgJ3NyYycsIGAke3RoaXMuZWwuc3JjfSR7aW5jbHVkZXModGhpcy5lbC5zcmMsICc/JykgPyAnJicgOiAnPyd9JHt5b3V0dWJlID8gJ2VuYWJsZWpzYXBpPTEnIDogYGFwaT0xJnBsYXllcl9pZD0ke3RoaXMuaWR9YH1gKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUoKTtcblxuICAgIH1cblxuICAgIHBsYXkoKSB7XG5cbiAgICAgICAgaWYgKCF0aGlzLmlzVmlkZW8oKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuaXNJRnJhbWUoKSkge1xuICAgICAgICAgICAgdGhpcy5lbmFibGVBcGkoKS50aGVuKCgpID0+IHBvc3QodGhpcy5lbCwge2Z1bmM6ICdwbGF5VmlkZW8nLCBtZXRob2Q6ICdwbGF5J30pKTtcbiAgICAgICAgfSBlbHNlIGlmICh0aGlzLmlzSFRNTDUoKSkge1xuICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICBjb25zdCBwcm9taXNlID0gdGhpcy5lbC5wbGF5KCk7XG5cbiAgICAgICAgICAgICAgICBpZiAocHJvbWlzZSkge1xuICAgICAgICAgICAgICAgICAgICBwcm9taXNlLmNhdGNoKG5vb3ApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHt9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBwYXVzZSgpIHtcblxuICAgICAgICBpZiAoIXRoaXMuaXNWaWRlbygpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGhpcy5pc0lGcmFtZSgpKSB7XG4gICAgICAgICAgICB0aGlzLmVuYWJsZUFwaSgpLnRoZW4oKCkgPT4gcG9zdCh0aGlzLmVsLCB7ZnVuYzogJ3BhdXNlVmlkZW8nLCBtZXRob2Q6ICdwYXVzZSd9KSk7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc0hUTUw1KCkpIHtcbiAgICAgICAgICAgIHRoaXMuZWwucGF1c2UoKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIG11dGUoKSB7XG5cbiAgICAgICAgaWYgKCF0aGlzLmlzVmlkZW8oKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHRoaXMuaXNJRnJhbWUoKSkge1xuICAgICAgICAgICAgdGhpcy5lbmFibGVBcGkoKS50aGVuKCgpID0+IHBvc3QodGhpcy5lbCwge2Z1bmM6ICdtdXRlJywgbWV0aG9kOiAnc2V0Vm9sdW1lJywgdmFsdWU6IDB9KSk7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc0hUTUw1KCkpIHtcbiAgICAgICAgICAgIHRoaXMuZWwubXV0ZWQgPSB0cnVlO1xuICAgICAgICAgICAgYXR0cih0aGlzLmVsLCAnbXV0ZWQnLCAnJyk7XG4gICAgICAgIH1cblxuICAgIH1cblxufVxuXG5mdW5jdGlvbiBwb3N0KGVsLCBjbWQpIHtcbiAgICB0cnkge1xuICAgICAgICBlbC5jb250ZW50V2luZG93LnBvc3RNZXNzYWdlKEpTT04uc3RyaW5naWZ5KGFzc2lnbih7ZXZlbnQ6ICdjb21tYW5kJ30sIGNtZCkpLCAnKicpO1xuICAgIH0gY2F0Y2ggKGUpIHt9XG59XG5cbmZ1bmN0aW9uIGxpc3RlbihjYikge1xuXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuXG4gICAgICAgIG9uY2Uod2luZG93LCAnbWVzc2FnZScsIChfLCBkYXRhKSA9PiByZXNvbHZlKGRhdGEpLCBmYWxzZSwgKHtkYXRhfSkgPT4ge1xuXG4gICAgICAgICAgICBpZiAoIWRhdGEgfHwgIWlzU3RyaW5nKGRhdGEpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGRhdGEgPSBKU09OLnBhcnNlKGRhdGEpO1xuICAgICAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGRhdGEgJiYgY2IoZGF0YSk7XG5cbiAgICAgICAgfSk7XG5cbiAgICB9KTtcblxufVxuIiwiaW1wb3J0IHtjc3N9IGZyb20gJy4vc3R5bGUnO1xuaW1wb3J0IHtQcm9taXNlfSBmcm9tICcuL3Byb21pc2UnO1xuaW1wb3J0IHtpc1Zpc2libGV9IGZyb20gJy4vZmlsdGVyJztcbmltcG9ydCB7cGFyZW50c30gZnJvbSAnLi9zZWxlY3Rvcic7XG5pbXBvcnQge29mZnNldCwgb2Zmc2V0UG9zaXRpb24sIHBvc2l0aW9ufSBmcm9tICcuL2RpbWVuc2lvbnMnO1xuaW1wb3J0IHtjbGFtcCwgaW50ZXJzZWN0UmVjdCwgaXNEb2N1bWVudCwgaXNXaW5kb3csIGxhc3QsIHBvaW50SW5SZWN0LCB0b05vZGUsIHRvV2luZG93fSBmcm9tICcuL2xhbmcnO1xuXG5leHBvcnQgZnVuY3Rpb24gaXNJblZpZXcoZWxlbWVudCwgb2Zmc2V0VG9wID0gMCwgb2Zmc2V0TGVmdCA9IDApIHtcblxuICAgIGlmICghaXNWaXNpYmxlKGVsZW1lbnQpKSB7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICBjb25zdCBwYXJlbnRzID0gb3ZlcmZsb3dQYXJlbnRzKGVsZW1lbnQpLmNvbmNhdChlbGVtZW50KTtcblxuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgcGFyZW50cy5sZW5ndGggLSAxOyBpKyspIHtcbiAgICAgICAgY29uc3Qge3RvcCwgbGVmdCwgYm90dG9tLCByaWdodH0gPSBvZmZzZXQoZ2V0Vmlld3BvcnQocGFyZW50c1tpXSkpO1xuICAgICAgICBjb25zdCB2cCA9IHtcbiAgICAgICAgICAgIHRvcDogdG9wIC0gb2Zmc2V0VG9wLFxuICAgICAgICAgICAgbGVmdDogbGVmdCAtIG9mZnNldExlZnQsXG4gICAgICAgICAgICBib3R0b206IGJvdHRvbSArIG9mZnNldFRvcCxcbiAgICAgICAgICAgIHJpZ2h0OiByaWdodCArIG9mZnNldExlZnRcbiAgICAgICAgfTtcblxuICAgICAgICBjb25zdCBjbGllbnQgPSBvZmZzZXQocGFyZW50c1tpICsgMV0pO1xuXG4gICAgICAgIGlmICghaW50ZXJzZWN0UmVjdChjbGllbnQsIHZwKSAmJiAhcG9pbnRJblJlY3Qoe3g6IGNsaWVudC5sZWZ0LCB5OiBjbGllbnQudG9wfSwgdnApKSB7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gdHJ1ZTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHNjcm9sbFRvcChlbGVtZW50LCB0b3ApIHtcblxuICAgIGlmIChpc1dpbmRvdyhlbGVtZW50KSB8fCBpc0RvY3VtZW50KGVsZW1lbnQpKSB7XG4gICAgICAgIGVsZW1lbnQgPSBnZXRTY3JvbGxpbmdFbGVtZW50KGVsZW1lbnQpO1xuICAgIH0gZWxzZSB7XG4gICAgICAgIGVsZW1lbnQgPSB0b05vZGUoZWxlbWVudCk7XG4gICAgfVxuXG4gICAgZWxlbWVudC5zY3JvbGxUb3AgPSB0b3A7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBzY3JvbGxJbnRvVmlldyhlbGVtZW50LCB7ZHVyYXRpb24gPSAxMDAwLCBvZmZzZXQgPSAwfSA9IHt9KSB7XG5cbiAgICBpZiAoIWlzVmlzaWJsZShlbGVtZW50KSkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3QgcGFyZW50cyA9IG92ZXJmbG93UGFyZW50cyhlbGVtZW50KS5jb25jYXQoZWxlbWVudCk7XG4gICAgZHVyYXRpb24gLz0gcGFyZW50cy5sZW5ndGggLSAxO1xuXG4gICAgbGV0IHByb21pc2UgPSBQcm9taXNlLnJlc29sdmUoKTtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IHBhcmVudHMubGVuZ3RoIC0gMTsgaSsrKSB7XG4gICAgICAgIHByb21pc2UgPSBwcm9taXNlLnRoZW4oKCkgPT5cbiAgICAgICAgICAgIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qgc2Nyb2xsRWxlbWVudCA9IHBhcmVudHNbaV07XG4gICAgICAgICAgICAgICAgY29uc3QgZWxlbWVudCA9IHBhcmVudHNbaSArIDFdO1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge3Njcm9sbFRvcDogc2Nyb2xsfSA9IHNjcm9sbEVsZW1lbnQ7XG4gICAgICAgICAgICAgICAgY29uc3QgdG9wID0gcG9zaXRpb24oZWxlbWVudCwgZ2V0Vmlld3BvcnQoc2Nyb2xsRWxlbWVudCkpLnRvcCAtIG9mZnNldDtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHN0YXJ0ID0gRGF0ZS5ub3coKTtcbiAgICAgICAgICAgICAgICBjb25zdCBzdGVwID0gKCkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHBlcmNlbnQgPSBlYXNlKGNsYW1wKChEYXRlLm5vdygpIC0gc3RhcnQpIC8gZHVyYXRpb24pKTtcblxuICAgICAgICAgICAgICAgICAgICBzY3JvbGxUb3Aoc2Nyb2xsRWxlbWVudCwgc2Nyb2xsICsgdG9wICogcGVyY2VudCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gc2Nyb2xsIG1vcmUgaWYgd2UgaGF2ZSBub3QgcmVhY2hlZCBvdXIgZGVzdGluYXRpb25cbiAgICAgICAgICAgICAgICAgICAgaWYgKHBlcmNlbnQgIT09IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZShzdGVwKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgIHN0ZXAoKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHByb21pc2U7XG5cbiAgICBmdW5jdGlvbiBlYXNlKGspIHtcbiAgICAgICAgcmV0dXJuIDAuNSAqICgxIC0gTWF0aC5jb3MoTWF0aC5QSSAqIGspKTtcbiAgICB9XG5cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIHNjcm9sbGVkT3ZlcihlbGVtZW50LCBoZWlnaHRPZmZzZXQgPSAwKSB7XG5cbiAgICBpZiAoIWlzVmlzaWJsZShlbGVtZW50KSkge1xuICAgICAgICByZXR1cm4gMDtcbiAgICB9XG5cbiAgICBjb25zdCBzY3JvbGxFbGVtZW50ID0gbGFzdChzY3JvbGxQYXJlbnRzKGVsZW1lbnQpKTtcbiAgICBjb25zdCB7c2Nyb2xsSGVpZ2h0LCBzY3JvbGxUb3B9ID0gc2Nyb2xsRWxlbWVudDtcbiAgICBjb25zdCB2aWV3cG9ydCA9IGdldFZpZXdwb3J0KHNjcm9sbEVsZW1lbnQpO1xuICAgIGNvbnN0IHZpZXdwb3J0SGVpZ2h0ID0gb2Zmc2V0KHZpZXdwb3J0KS5oZWlnaHQ7XG4gICAgY29uc3Qgdmlld3BvcnRUb3AgPSBvZmZzZXRQb3NpdGlvbihlbGVtZW50KVswXSAtIHNjcm9sbFRvcCAtIG9mZnNldFBvc2l0aW9uKHNjcm9sbEVsZW1lbnQpWzBdO1xuICAgIGNvbnN0IHZpZXdwb3J0RGlzdCA9IE1hdGgubWluKHZpZXdwb3J0SGVpZ2h0LCB2aWV3cG9ydFRvcCArIHNjcm9sbFRvcCk7XG5cbiAgICBjb25zdCB0b3AgPSB2aWV3cG9ydFRvcCAtIHZpZXdwb3J0RGlzdDtcbiAgICBjb25zdCBkaXN0ID0gTWF0aC5taW4oXG4gICAgICAgIG9mZnNldChlbGVtZW50KS5oZWlnaHQgKyBoZWlnaHRPZmZzZXQgKyB2aWV3cG9ydERpc3QsXG4gICAgICAgIHNjcm9sbEhlaWdodCAtICh2aWV3cG9ydFRvcCArIHNjcm9sbFRvcCksXG4gICAgICAgIHNjcm9sbEhlaWdodCAtIHZpZXdwb3J0SGVpZ2h0XG4gICAgKTtcblxuICAgIHJldHVybiBjbGFtcCgtMSAqIHRvcCAvIGRpc3QpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gc2Nyb2xsUGFyZW50cyhlbGVtZW50LCBvdmVyZmxvd1JlID0gL2F1dG98c2Nyb2xsLykge1xuICAgIGNvbnN0IHNjcm9sbEVsID0gZ2V0U2Nyb2xsaW5nRWxlbWVudChlbGVtZW50KTtcbiAgICBjb25zdCBzY3JvbGxQYXJlbnRzID0gcGFyZW50cyhlbGVtZW50KS5maWx0ZXIocGFyZW50ID0+XG4gICAgICAgIHBhcmVudCA9PT0gc2Nyb2xsRWxcbiAgICAgICAgfHwgb3ZlcmZsb3dSZS50ZXN0KGNzcyhwYXJlbnQsICdvdmVyZmxvdycpKVxuICAgICAgICAmJiBwYXJlbnQuc2Nyb2xsSGVpZ2h0ID4gb2Zmc2V0KHBhcmVudCkuaGVpZ2h0XG4gICAgKS5yZXZlcnNlKCk7XG4gICAgcmV0dXJuIHNjcm9sbFBhcmVudHMubGVuZ3RoID8gc2Nyb2xsUGFyZW50cyA6IFtzY3JvbGxFbF07XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBnZXRWaWV3cG9ydChzY3JvbGxFbGVtZW50KSB7XG4gICAgcmV0dXJuIHNjcm9sbEVsZW1lbnQgPT09IGdldFNjcm9sbGluZ0VsZW1lbnQoc2Nyb2xsRWxlbWVudCkgPyB3aW5kb3cgOiBzY3JvbGxFbGVtZW50O1xufVxuXG5mdW5jdGlvbiBvdmVyZmxvd1BhcmVudHMoZWxlbWVudCkge1xuICAgIHJldHVybiBzY3JvbGxQYXJlbnRzKGVsZW1lbnQsIC9hdXRvfHNjcm9sbHxoaWRkZW4vKTtcbn1cblxuZnVuY3Rpb24gZ2V0U2Nyb2xsaW5nRWxlbWVudChlbGVtZW50KSB7XG4gICAgY29uc3Qge2RvY3VtZW50fSA9IHRvV2luZG93KGVsZW1lbnQpO1xuICAgIHJldHVybiBkb2N1bWVudC5zY3JvbGxpbmdFbGVtZW50IHx8IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcbn1cbiIsImltcG9ydCB7b259IGZyb20gJy4vZXZlbnQnO1xuaW1wb3J0IHt0b0Zsb2F0fSBmcm9tICcuL2xhbmcnO1xuaW1wb3J0IHtpc0luVmlld30gZnJvbSAnLi92aWV3cG9ydCc7XG5cbmV4cG9ydCBjb25zdCBJbnRlcnNlY3Rpb25PYnNlcnZlciA9ICdJbnRlcnNlY3Rpb25PYnNlcnZlcicgaW4gd2luZG93XG4gICAgPyB3aW5kb3cuSW50ZXJzZWN0aW9uT2JzZXJ2ZXJcbiAgICA6IGNsYXNzIEludGVyc2VjdGlvbk9ic2VydmVyQ2xhc3Mge1xuXG4gICAgICAgIGNvbnN0cnVjdG9yKGNhbGxiYWNrLCB7cm9vdE1hcmdpbiA9ICcwIDAnfSA9IHt9KSB7XG5cbiAgICAgICAgICAgIHRoaXMudGFyZ2V0cyA9IFtdO1xuXG4gICAgICAgICAgICBjb25zdCBbb2Zmc2V0VG9wLCBvZmZzZXRMZWZ0XSA9IChyb290TWFyZ2luIHx8ICcwIDAnKS5zcGxpdCgnICcpLm1hcCh0b0Zsb2F0KTtcblxuICAgICAgICAgICAgdGhpcy5vZmZzZXRUb3AgPSBvZmZzZXRUb3A7XG4gICAgICAgICAgICB0aGlzLm9mZnNldExlZnQgPSBvZmZzZXRMZWZ0O1xuXG4gICAgICAgICAgICBsZXQgcGVuZGluZztcbiAgICAgICAgICAgIHRoaXMuYXBwbHkgPSAoKSA9PiB7XG5cbiAgICAgICAgICAgICAgICBpZiAocGVuZGluZykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcGVuZGluZyA9IHJlcXVlc3RBbmltYXRpb25GcmFtZSgoKSA9PiBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgcmVjb3JkcyA9IHRoaXMudGFrZVJlY29yZHMoKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAocmVjb3Jkcy5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKHJlY29yZHMsIHRoaXMpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcGVuZGluZyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIH0pKTtcblxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgdGhpcy5vZmYgPSBvbih3aW5kb3csICdzY3JvbGwgcmVzaXplIGxvYWQnLCB0aGlzLmFwcGx5LCB7cGFzc2l2ZTogdHJ1ZSwgY2FwdHVyZTogdHJ1ZX0pO1xuXG4gICAgICAgIH1cblxuICAgICAgICB0YWtlUmVjb3JkcygpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnRhcmdldHMuZmlsdGVyKGVudHJ5ID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IGluVmlldyA9IGlzSW5WaWV3KGVudHJ5LnRhcmdldCwgdGhpcy5vZmZzZXRUb3AsIHRoaXMub2Zmc2V0TGVmdCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoZW50cnkuaXNJbnRlcnNlY3RpbmcgPT09IG51bGwgfHwgaW5WaWV3IF4gZW50cnkuaXNJbnRlcnNlY3RpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgZW50cnkuaXNJbnRlcnNlY3RpbmcgPSBpblZpZXc7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cblxuICAgICAgICBvYnNlcnZlKHRhcmdldCkge1xuICAgICAgICAgICAgdGhpcy50YXJnZXRzLnB1c2goe1xuICAgICAgICAgICAgICAgIHRhcmdldCxcbiAgICAgICAgICAgICAgICBpc0ludGVyc2VjdGluZzogbnVsbFxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB0aGlzLmFwcGx5KCk7XG4gICAgICAgIH1cblxuICAgICAgICBkaXNjb25uZWN0KCkge1xuICAgICAgICAgICAgdGhpcy50YXJnZXRzID0gW107XG4gICAgICAgICAgICB0aGlzLm9mZigpO1xuICAgICAgICB9XG5cbiAgICB9O1xuIiwiaW1wb3J0IHskLCBhcHBseSwgaXNTdHJpbmcsIG1lcmdlT3B0aW9ucywgcGFyZW50cywgdG9Ob2RlfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gKFVJa2l0KSB7XG5cbiAgICBjb25zdCBEQVRBID0gVUlraXQuZGF0YTtcblxuICAgIFVJa2l0LnVzZSA9IGZ1bmN0aW9uIChwbHVnaW4pIHtcblxuICAgICAgICBpZiAocGx1Z2luLmluc3RhbGxlZCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgcGx1Z2luLmNhbGwobnVsbCwgdGhpcyk7XG4gICAgICAgIHBsdWdpbi5pbnN0YWxsZWQgPSB0cnVlO1xuXG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbiAgICBVSWtpdC5taXhpbiA9IGZ1bmN0aW9uIChtaXhpbiwgY29tcG9uZW50KSB7XG4gICAgICAgIGNvbXBvbmVudCA9IChpc1N0cmluZyhjb21wb25lbnQpID8gVUlraXQuY29tcG9uZW50KGNvbXBvbmVudCkgOiBjb21wb25lbnQpIHx8IHRoaXM7XG4gICAgICAgIGNvbXBvbmVudC5vcHRpb25zID0gbWVyZ2VPcHRpb25zKGNvbXBvbmVudC5vcHRpb25zLCBtaXhpbik7XG4gICAgfTtcblxuICAgIFVJa2l0LmV4dGVuZCA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG5cbiAgICAgICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgICAgICAgY29uc3QgU3VwZXIgPSB0aGlzO1xuICAgICAgICBjb25zdCBTdWIgPSBmdW5jdGlvbiBVSWtpdENvbXBvbmVudChvcHRpb25zKSB7XG4gICAgICAgICAgICB0aGlzLl9pbml0KG9wdGlvbnMpO1xuICAgICAgICB9O1xuXG4gICAgICAgIFN1Yi5wcm90b3R5cGUgPSBPYmplY3QuY3JlYXRlKFN1cGVyLnByb3RvdHlwZSk7XG4gICAgICAgIFN1Yi5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBTdWI7XG4gICAgICAgIFN1Yi5vcHRpb25zID0gbWVyZ2VPcHRpb25zKFN1cGVyLm9wdGlvbnMsIG9wdGlvbnMpO1xuXG4gICAgICAgIFN1Yi5zdXBlciA9IFN1cGVyO1xuICAgICAgICBTdWIuZXh0ZW5kID0gU3VwZXIuZXh0ZW5kO1xuXG4gICAgICAgIHJldHVybiBTdWI7XG4gICAgfTtcblxuICAgIFVJa2l0LnVwZGF0ZSA9IGZ1bmN0aW9uIChlbGVtZW50LCBlKSB7XG5cbiAgICAgICAgZWxlbWVudCA9IGVsZW1lbnQgPyB0b05vZGUoZWxlbWVudCkgOiBkb2N1bWVudC5ib2R5O1xuXG4gICAgICAgIHBhcmVudHMoZWxlbWVudCkucmV2ZXJzZSgpLmZvckVhY2goZWxlbWVudCA9PiB1cGRhdGUoZWxlbWVudFtEQVRBXSwgZSkpO1xuICAgICAgICBhcHBseShlbGVtZW50LCBlbGVtZW50ID0+IHVwZGF0ZShlbGVtZW50W0RBVEFdLCBlKSk7XG5cbiAgICB9O1xuXG4gICAgbGV0IGNvbnRhaW5lcjtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkoVUlraXQsICdjb250YWluZXInLCB7XG5cbiAgICAgICAgZ2V0KCkge1xuICAgICAgICAgICAgcmV0dXJuIGNvbnRhaW5lciB8fCBkb2N1bWVudC5ib2R5O1xuICAgICAgICB9LFxuXG4gICAgICAgIHNldChlbGVtZW50KSB7XG4gICAgICAgICAgICBjb250YWluZXIgPSAkKGVsZW1lbnQpO1xuICAgICAgICB9XG5cbiAgICB9KTtcblxuICAgIGZ1bmN0aW9uIHVwZGF0ZShkYXRhLCBlKSB7XG5cbiAgICAgICAgaWYgKCFkYXRhKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBmb3IgKGNvbnN0IG5hbWUgaW4gZGF0YSkge1xuICAgICAgICAgICAgaWYgKGRhdGFbbmFtZV0uX2Nvbm5lY3RlZCkge1xuICAgICAgICAgICAgICAgIGRhdGFbbmFtZV0uX2NhbGxVcGRhdGUoZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgIH1cblxufVxuIiwiaW1wb3J0IHthc3NpZ24sIGZhc3Rkb20sIGluY2x1ZGVzLCBpc1BsYWluT2JqZWN0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gKFVJa2l0KSB7XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2NhbGxIb29rID0gZnVuY3Rpb24gKGhvb2spIHtcblxuICAgICAgICBjb25zdCBoYW5kbGVycyA9IHRoaXMuJG9wdGlvbnNbaG9va107XG5cbiAgICAgICAgaWYgKGhhbmRsZXJzKSB7XG4gICAgICAgICAgICBoYW5kbGVycy5mb3JFYWNoKGhhbmRsZXIgPT4gaGFuZGxlci5jYWxsKHRoaXMpKTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2NhbGxDb25uZWN0ZWQgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgaWYgKHRoaXMuX2Nvbm5lY3RlZCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5fZGF0YSA9IHt9O1xuICAgICAgICB0aGlzLl9jb21wdXRlZHMgPSB7fTtcbiAgICAgICAgdGhpcy5faW5pdFByb3BzKCk7XG5cbiAgICAgICAgdGhpcy5fY2FsbEhvb2soJ2JlZm9yZUNvbm5lY3QnKTtcbiAgICAgICAgdGhpcy5fY29ubmVjdGVkID0gdHJ1ZTtcblxuICAgICAgICB0aGlzLl9pbml0RXZlbnRzKCk7XG4gICAgICAgIHRoaXMuX2luaXRPYnNlcnZlcigpO1xuXG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdjb25uZWN0ZWQnKTtcbiAgICAgICAgdGhpcy5fY2FsbFVwZGF0ZSgpO1xuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2NhbGxEaXNjb25uZWN0ZWQgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgaWYgKCF0aGlzLl9jb25uZWN0ZWQpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdiZWZvcmVEaXNjb25uZWN0Jyk7XG5cbiAgICAgICAgaWYgKHRoaXMuX29ic2VydmVyKSB7XG4gICAgICAgICAgICB0aGlzLl9vYnNlcnZlci5kaXNjb25uZWN0KCk7XG4gICAgICAgICAgICB0aGlzLl9vYnNlcnZlciA9IG51bGw7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLl91bmJpbmRFdmVudHMoKTtcbiAgICAgICAgdGhpcy5fY2FsbEhvb2soJ2Rpc2Nvbm5lY3RlZCcpO1xuXG4gICAgICAgIHRoaXMuX2Nvbm5lY3RlZCA9IGZhbHNlO1xuXG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5fY2FsbFVwZGF0ZSA9IGZ1bmN0aW9uIChlID0gJ3VwZGF0ZScpIHtcblxuICAgICAgICBjb25zdCB0eXBlID0gZS50eXBlIHx8IGU7XG5cbiAgICAgICAgaWYgKGluY2x1ZGVzKFsndXBkYXRlJywgJ3Jlc2l6ZSddLCB0eXBlKSkge1xuICAgICAgICAgICAgdGhpcy5fY2FsbFdhdGNoZXMoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHVwZGF0ZXMgPSB0aGlzLiRvcHRpb25zLnVwZGF0ZTtcbiAgICAgICAgY29uc3Qge3JlYWRzLCB3cml0ZXN9ID0gdGhpcy5fZnJhbWVzO1xuXG4gICAgICAgIGlmICghdXBkYXRlcykge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdXBkYXRlcy5mb3JFYWNoKCh7cmVhZCwgd3JpdGUsIGV2ZW50c30sIGkpID0+IHtcblxuICAgICAgICAgICAgaWYgKHR5cGUgIT09ICd1cGRhdGUnICYmICFpbmNsdWRlcyhldmVudHMsIHR5cGUpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAocmVhZCAmJiAhaW5jbHVkZXMoZmFzdGRvbS5yZWFkcywgcmVhZHNbaV0pKSB7XG4gICAgICAgICAgICAgICAgcmVhZHNbaV0gPSBmYXN0ZG9tLnJlYWQoKCkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHJlc3VsdCA9IHRoaXMuX2Nvbm5lY3RlZCAmJiByZWFkLmNhbGwodGhpcywgdGhpcy5fZGF0YSwgdHlwZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3VsdCA9PT0gZmFsc2UgJiYgd3JpdGUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZhc3Rkb20uY2xlYXIod3JpdGVzW2ldKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChpc1BsYWluT2JqZWN0KHJlc3VsdCkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFzc2lnbih0aGlzLl9kYXRhLCByZXN1bHQpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICh3cml0ZSAmJiAhaW5jbHVkZXMoZmFzdGRvbS53cml0ZXMsIHdyaXRlc1tpXSkpIHtcbiAgICAgICAgICAgICAgICB3cml0ZXNbaV0gPSBmYXN0ZG9tLndyaXRlKCgpID0+IHRoaXMuX2Nvbm5lY3RlZCAmJiB3cml0ZS5jYWxsKHRoaXMsIHRoaXMuX2RhdGEsIHR5cGUpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KTtcblxuICAgIH07XG5cbn1cbiIsImltcG9ydCB7YXNzaWduLCBjYW1lbGl6ZSwgZGF0YSBhcyBnZXREYXRhLCBoYXNPd24sIGh5cGhlbmF0ZSwgaXNBcnJheSwgaXNFbXB0eSwgaXNFcXVhbCwgaXNGdW5jdGlvbiwgaXNQbGFpbk9iamVjdCwgaXNTdHJpbmcsIGlzVW5kZWZpbmVkLCBtZXJnZU9wdGlvbnMsIG9uLCBwYXJzZU9wdGlvbnMsIHN0YXJ0c1dpdGgsIHRvQm9vbGVhbiwgdG9MaXN0LCB0b051bWJlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgbGV0IHVpZCA9IDA7XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX2luaXQgPSBmdW5jdGlvbiAob3B0aW9ucykge1xuXG4gICAgICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgICAgICBvcHRpb25zLmRhdGEgPSBub3JtYWxpemVEYXRhKG9wdGlvbnMsIHRoaXMuY29uc3RydWN0b3Iub3B0aW9ucyk7XG5cbiAgICAgICAgdGhpcy4kb3B0aW9ucyA9IG1lcmdlT3B0aW9ucyh0aGlzLmNvbnN0cnVjdG9yLm9wdGlvbnMsIG9wdGlvbnMsIHRoaXMpO1xuICAgICAgICB0aGlzLiRlbCA9IG51bGw7XG4gICAgICAgIHRoaXMuJHByb3BzID0ge307XG5cbiAgICAgICAgdGhpcy5fZnJhbWVzID0ge3JlYWRzOiB7fSwgd3JpdGVzOiB7fX07XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IFtdO1xuXG4gICAgICAgIHRoaXMuX3VpZCA9IHVpZCsrO1xuICAgICAgICB0aGlzLl9pbml0RGF0YSgpO1xuICAgICAgICB0aGlzLl9pbml0TWV0aG9kcygpO1xuICAgICAgICB0aGlzLl9pbml0Q29tcHV0ZWRzKCk7XG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdjcmVhdGVkJyk7XG5cbiAgICAgICAgaWYgKG9wdGlvbnMuZWwpIHtcbiAgICAgICAgICAgIHRoaXMuJG1vdW50KG9wdGlvbnMuZWwpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdERhdGEgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgY29uc3Qge2RhdGEgPSB7fX0gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIGZvciAoY29uc3Qga2V5IGluIGRhdGEpIHtcbiAgICAgICAgICAgIHRoaXMuJHByb3BzW2tleV0gPSB0aGlzW2tleV0gPSBkYXRhW2tleV07XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9pbml0TWV0aG9kcyA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBjb25zdCB7bWV0aG9kc30gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIGlmIChtZXRob2RzKSB7XG4gICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBtZXRob2RzKSB7XG4gICAgICAgICAgICAgICAgdGhpc1trZXldID0gbWV0aG9kc1trZXldLmJpbmQodGhpcyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9pbml0Q29tcHV0ZWRzID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGNvbnN0IHtjb21wdXRlZH0gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgIHRoaXMuX2NvbXB1dGVkcyA9IHt9O1xuXG4gICAgICAgIGlmIChjb21wdXRlZCkge1xuICAgICAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gY29tcHV0ZWQpIHtcbiAgICAgICAgICAgICAgICByZWdpc3RlckNvbXB1dGVkKHRoaXMsIGtleSwgY29tcHV0ZWRba2V5XSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLl9jYWxsV2F0Y2hlcyA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBjb25zdCB7JG9wdGlvbnM6IHtjb21wdXRlZH0sIF9jb21wdXRlZHN9ID0gdGhpcztcblxuICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBfY29tcHV0ZWRzKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHZhbHVlID0gX2NvbXB1dGVkc1trZXldO1xuICAgICAgICAgICAgZGVsZXRlIF9jb21wdXRlZHNba2V5XTtcblxuICAgICAgICAgICAgaWYgKGNvbXB1dGVkW2tleV0ud2F0Y2ggJiYgIWlzRXF1YWwodmFsdWUsIHRoaXNba2V5XSkpIHtcbiAgICAgICAgICAgICAgICBjb21wdXRlZFtrZXldLndhdGNoLmNhbGwodGhpcywgdGhpc1trZXldLCB2YWx1ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdFByb3BzID0gZnVuY3Rpb24gKHByb3BzKSB7XG5cbiAgICAgICAgbGV0IGtleTtcblxuICAgICAgICBwcm9wcyA9IHByb3BzIHx8IGdldFByb3BzKHRoaXMuJG9wdGlvbnMsIHRoaXMuJG5hbWUpO1xuXG4gICAgICAgIGZvciAoa2V5IGluIHByb3BzKSB7XG4gICAgICAgICAgICBpZiAoIWlzVW5kZWZpbmVkKHByb3BzW2tleV0pKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kcHJvcHNba2V5XSA9IHByb3BzW2tleV07XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBleGNsdWRlID0gW3RoaXMuJG9wdGlvbnMuY29tcHV0ZWQsIHRoaXMuJG9wdGlvbnMubWV0aG9kc107XG4gICAgICAgIGZvciAoa2V5IGluIHRoaXMuJHByb3BzKSB7XG4gICAgICAgICAgICBpZiAoa2V5IGluIHByb3BzICYmIG5vdEluKGV4Y2x1ZGUsIGtleSkpIHtcbiAgICAgICAgICAgICAgICB0aGlzW2tleV0gPSB0aGlzLiRwcm9wc1trZXldO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdEV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICBjb25zdCB7ZXZlbnRzfSA9IHRoaXMuJG9wdGlvbnM7XG5cbiAgICAgICAgaWYgKGV2ZW50cykge1xuXG4gICAgICAgICAgICBldmVudHMuZm9yRWFjaChldmVudCA9PiB7XG5cbiAgICAgICAgICAgICAgICBpZiAoIWhhc093bihldmVudCwgJ2hhbmRsZXInKSkge1xuICAgICAgICAgICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBldmVudCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVnaXN0ZXJFdmVudCh0aGlzLCBldmVudFtrZXldLCBrZXkpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgcmVnaXN0ZXJFdmVudCh0aGlzLCBldmVudCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuX3VuYmluZEV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdGhpcy5fZXZlbnRzLmZvckVhY2godW5iaW5kID0+IHVuYmluZCgpKTtcbiAgICAgICAgdGhpcy5fZXZlbnRzID0gW107XG4gICAgfTtcblxuICAgIFVJa2l0LnByb3RvdHlwZS5faW5pdE9ic2VydmVyID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGxldCB7YXR0cnMsIHByb3BzLCBlbH0gPSB0aGlzLiRvcHRpb25zO1xuICAgICAgICBpZiAodGhpcy5fb2JzZXJ2ZXIgfHwgIXByb3BzIHx8IGF0dHJzID09PSBmYWxzZSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgYXR0cnMgPSBpc0FycmF5KGF0dHJzKSA/IGF0dHJzIDogT2JqZWN0LmtleXMocHJvcHMpO1xuXG4gICAgICAgIHRoaXMuX29ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIoKCkgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBkYXRhID0gZ2V0UHJvcHModGhpcy4kb3B0aW9ucywgdGhpcy4kbmFtZSk7XG4gICAgICAgICAgICBpZiAoYXR0cnMuc29tZShrZXkgPT4gIWlzVW5kZWZpbmVkKGRhdGFba2V5XSkgJiYgZGF0YVtrZXldICE9PSB0aGlzLiRwcm9wc1trZXldKSkge1xuICAgICAgICAgICAgICAgIHRoaXMuJHJlc2V0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgY29uc3QgZmlsdGVyID0gYXR0cnMubWFwKGtleSA9PiBoeXBoZW5hdGUoa2V5KSkuY29uY2F0KHRoaXMuJG5hbWUpO1xuXG4gICAgICAgIHRoaXMuX29ic2VydmVyLm9ic2VydmUoZWwsIHtcbiAgICAgICAgICAgIGF0dHJpYnV0ZXM6IHRydWUsXG4gICAgICAgICAgICBhdHRyaWJ1dGVGaWx0ZXI6IGZpbHRlci5jb25jYXQoZmlsdGVyLm1hcChrZXkgPT4gYGRhdGEtJHtrZXl9YCkpXG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBmdW5jdGlvbiBnZXRQcm9wcyhvcHRzLCBuYW1lKSB7XG5cbiAgICAgICAgY29uc3QgZGF0YSA9IHt9O1xuICAgICAgICBjb25zdCB7YXJncyA9IFtdLCBwcm9wcyA9IHt9LCBlbH0gPSBvcHRzO1xuXG4gICAgICAgIGlmICghcHJvcHMpIHtcbiAgICAgICAgICAgIHJldHVybiBkYXRhO1xuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChjb25zdCBrZXkgaW4gcHJvcHMpIHtcbiAgICAgICAgICAgIGNvbnN0IHByb3AgPSBoeXBoZW5hdGUoa2V5KTtcbiAgICAgICAgICAgIGxldCB2YWx1ZSA9IGdldERhdGEoZWwsIHByb3ApO1xuXG4gICAgICAgICAgICBpZiAoIWlzVW5kZWZpbmVkKHZhbHVlKSkge1xuXG4gICAgICAgICAgICAgICAgdmFsdWUgPSBwcm9wc1trZXldID09PSBCb29sZWFuICYmIHZhbHVlID09PSAnJ1xuICAgICAgICAgICAgICAgICAgICA/IHRydWVcbiAgICAgICAgICAgICAgICAgICAgOiBjb2VyY2UocHJvcHNba2V5XSwgdmFsdWUpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHByb3AgPT09ICd0YXJnZXQnICYmICghdmFsdWUgfHwgc3RhcnRzV2l0aCh2YWx1ZSwgJ18nKSkpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZGF0YVtrZXldID0gdmFsdWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBvcHRpb25zID0gcGFyc2VPcHRpb25zKGdldERhdGEoZWwsIG5hbWUpLCBhcmdzKTtcblxuICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBvcHRpb25zKSB7XG4gICAgICAgICAgICBjb25zdCBwcm9wID0gY2FtZWxpemUoa2V5KTtcbiAgICAgICAgICAgIGlmIChwcm9wc1twcm9wXSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgZGF0YVtwcm9wXSA9IGNvZXJjZShwcm9wc1twcm9wXSwgb3B0aW9uc1trZXldKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBkYXRhO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHJlZ2lzdGVyQ29tcHV0ZWQoY29tcG9uZW50LCBrZXksIGNiKSB7XG4gICAgICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShjb21wb25lbnQsIGtleSwge1xuXG4gICAgICAgICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuXG4gICAgICAgICAgICBnZXQoKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCB7X2NvbXB1dGVkcywgJHByb3BzLCAkZWx9ID0gY29tcG9uZW50O1xuXG4gICAgICAgICAgICAgICAgaWYgKCFoYXNPd24oX2NvbXB1dGVkcywga2V5KSkge1xuICAgICAgICAgICAgICAgICAgICBfY29tcHV0ZWRzW2tleV0gPSAoY2IuZ2V0IHx8IGNiKS5jYWxsKGNvbXBvbmVudCwgJHByb3BzLCAkZWwpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJldHVybiBfY29tcHV0ZWRzW2tleV07XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBzZXQodmFsdWUpIHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHtfY29tcHV0ZWRzfSA9IGNvbXBvbmVudDtcblxuICAgICAgICAgICAgICAgIF9jb21wdXRlZHNba2V5XSA9IGNiLnNldCA/IGNiLnNldC5jYWxsKGNvbXBvbmVudCwgdmFsdWUpIDogdmFsdWU7XG5cbiAgICAgICAgICAgICAgICBpZiAoaXNVbmRlZmluZWQoX2NvbXB1dGVkc1trZXldKSkge1xuICAgICAgICAgICAgICAgICAgICBkZWxldGUgX2NvbXB1dGVkc1trZXldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiByZWdpc3RlckV2ZW50KGNvbXBvbmVudCwgZXZlbnQsIGtleSkge1xuXG4gICAgICAgIGlmICghaXNQbGFpbk9iamVjdChldmVudCkpIHtcbiAgICAgICAgICAgIGV2ZW50ID0gKHtuYW1lOiBrZXksIGhhbmRsZXI6IGV2ZW50fSk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQge25hbWUsIGVsLCBoYW5kbGVyLCBjYXB0dXJlLCBwYXNzaXZlLCBkZWxlZ2F0ZSwgZmlsdGVyLCBzZWxmfSA9IGV2ZW50O1xuICAgICAgICBlbCA9IGlzRnVuY3Rpb24oZWwpXG4gICAgICAgICAgICA/IGVsLmNhbGwoY29tcG9uZW50KVxuICAgICAgICAgICAgOiBlbCB8fCBjb21wb25lbnQuJGVsO1xuXG4gICAgICAgIGlmIChpc0FycmF5KGVsKSkge1xuICAgICAgICAgICAgZWwuZm9yRWFjaChlbCA9PiByZWdpc3RlckV2ZW50KGNvbXBvbmVudCwgYXNzaWduKHt9LCBldmVudCwge2VsfSksIGtleSkpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKCFlbCB8fCBmaWx0ZXIgJiYgIWZpbHRlci5jYWxsKGNvbXBvbmVudCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbXBvbmVudC5fZXZlbnRzLnB1c2goXG4gICAgICAgICAgICBvbihcbiAgICAgICAgICAgICAgICBlbCxcbiAgICAgICAgICAgICAgICBuYW1lLFxuICAgICAgICAgICAgICAgICFkZWxlZ2F0ZVxuICAgICAgICAgICAgICAgICAgICA/IG51bGxcbiAgICAgICAgICAgICAgICAgICAgOiBpc1N0cmluZyhkZWxlZ2F0ZSlcbiAgICAgICAgICAgICAgICAgICAgICAgID8gZGVsZWdhdGVcbiAgICAgICAgICAgICAgICAgICAgICAgIDogZGVsZWdhdGUuY2FsbChjb21wb25lbnQpLFxuICAgICAgICAgICAgICAgIGlzU3RyaW5nKGhhbmRsZXIpID8gY29tcG9uZW50W2hhbmRsZXJdIDogaGFuZGxlci5iaW5kKGNvbXBvbmVudCksXG4gICAgICAgICAgICAgICAge3Bhc3NpdmUsIGNhcHR1cmUsIHNlbGZ9XG4gICAgICAgICAgICApXG4gICAgICAgICk7XG5cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBub3RJbihvcHRpb25zLCBrZXkpIHtcbiAgICAgICAgcmV0dXJuIG9wdGlvbnMuZXZlcnkoYXJyID0+ICFhcnIgfHwgIWhhc093bihhcnIsIGtleSkpO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGNvZXJjZSh0eXBlLCB2YWx1ZSkge1xuXG4gICAgICAgIGlmICh0eXBlID09PSBCb29sZWFuKSB7XG4gICAgICAgICAgICByZXR1cm4gdG9Cb29sZWFuKHZhbHVlKTtcbiAgICAgICAgfSBlbHNlIGlmICh0eXBlID09PSBOdW1iZXIpIHtcbiAgICAgICAgICAgIHJldHVybiB0b051bWJlcih2YWx1ZSk7XG4gICAgICAgIH0gZWxzZSBpZiAodHlwZSA9PT0gJ2xpc3QnKSB7XG4gICAgICAgICAgICByZXR1cm4gdG9MaXN0KHZhbHVlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiB0eXBlID8gdHlwZSh2YWx1ZSkgOiB2YWx1ZTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBub3JtYWxpemVEYXRhKHtkYXRhLCBlbH0sIHthcmdzLCBwcm9wcyA9IHt9fSkge1xuICAgICAgICBkYXRhID0gaXNBcnJheShkYXRhKVxuICAgICAgICAgICAgPyAhaXNFbXB0eShhcmdzKVxuICAgICAgICAgICAgICAgID8gZGF0YS5zbGljZSgwLCBhcmdzLmxlbmd0aCkucmVkdWNlKChkYXRhLCB2YWx1ZSwgaW5kZXgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGlzUGxhaW5PYmplY3QodmFsdWUpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhc3NpZ24oZGF0YSwgdmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVthcmdzW2luZGV4XV0gPSB2YWx1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZGF0YTtcbiAgICAgICAgICAgICAgICB9LCB7fSlcbiAgICAgICAgICAgICAgICA6IHVuZGVmaW5lZFxuICAgICAgICAgICAgOiBkYXRhO1xuXG4gICAgICAgIGlmIChkYXRhKSB7XG4gICAgICAgICAgICBmb3IgKGNvbnN0IGtleSBpbiBkYXRhKSB7XG4gICAgICAgICAgICAgICAgaWYgKGlzVW5kZWZpbmVkKGRhdGFba2V5XSkpIHtcbiAgICAgICAgICAgICAgICAgICAgZGVsZXRlIGRhdGFba2V5XTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBkYXRhW2tleV0gPSBwcm9wc1trZXldID8gY29lcmNlKHByb3BzW2tleV0sIGRhdGFba2V5XSwgZWwpIDogZGF0YVtrZXldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBkYXRhO1xuICAgIH1cbn1cbiIsImltcG9ydCB7aHlwaGVuYXRlLCBpc0VtcHR5LCByZW1vdmUsIHdpdGhpbn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgY29uc3QgREFUQSA9IFVJa2l0LmRhdGE7XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuJG1vdW50ID0gZnVuY3Rpb24gKGVsKSB7XG5cbiAgICAgICAgY29uc3Qge25hbWV9ID0gdGhpcy4kb3B0aW9ucztcblxuICAgICAgICBpZiAoIWVsW0RBVEFdKSB7XG4gICAgICAgICAgICBlbFtEQVRBXSA9IHt9O1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGVsW0RBVEFdW25hbWVdKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBlbFtEQVRBXVtuYW1lXSA9IHRoaXM7XG5cbiAgICAgICAgdGhpcy4kZWwgPSB0aGlzLiRvcHRpb25zLmVsID0gdGhpcy4kb3B0aW9ucy5lbCB8fCBlbDtcblxuICAgICAgICBpZiAod2l0aGluKGVsLCBkb2N1bWVudCkpIHtcbiAgICAgICAgICAgIHRoaXMuX2NhbGxDb25uZWN0ZWQoKTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuJGVtaXQgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICB0aGlzLl9jYWxsVXBkYXRlKGUpO1xuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuJHJlc2V0ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICB0aGlzLl9jYWxsRGlzY29ubmVjdGVkKCk7XG4gICAgICAgIHRoaXMuX2NhbGxDb25uZWN0ZWQoKTtcbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLiRkZXN0cm95ID0gZnVuY3Rpb24gKHJlbW92ZUVsID0gZmFsc2UpIHtcblxuICAgICAgICBjb25zdCB7ZWwsIG5hbWV9ID0gdGhpcy4kb3B0aW9ucztcblxuICAgICAgICBpZiAoZWwpIHtcbiAgICAgICAgICAgIHRoaXMuX2NhbGxEaXNjb25uZWN0ZWQoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuX2NhbGxIb29rKCdkZXN0cm95Jyk7XG5cbiAgICAgICAgaWYgKCFlbCB8fCAhZWxbREFUQV0pIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGRlbGV0ZSBlbFtEQVRBXVtuYW1lXTtcblxuICAgICAgICBpZiAoIWlzRW1wdHkoZWxbREFUQV0pKSB7XG4gICAgICAgICAgICBkZWxldGUgZWxbREFUQV07XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocmVtb3ZlRWwpIHtcbiAgICAgICAgICAgIHJlbW92ZSh0aGlzLiRlbCk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgVUlraXQucHJvdG90eXBlLiRjcmVhdGUgPSBmdW5jdGlvbiAoY29tcG9uZW50LCBlbGVtZW50LCBkYXRhKSB7XG4gICAgICAgIHJldHVybiBVSWtpdFtjb21wb25lbnRdKGVsZW1lbnQsIGRhdGEpO1xuICAgIH07XG5cbiAgICBVSWtpdC5wcm90b3R5cGUuJHVwZGF0ZSA9IFVJa2l0LnVwZGF0ZTtcbiAgICBVSWtpdC5wcm90b3R5cGUuJGdldENvbXBvbmVudCA9IFVJa2l0LmdldENvbXBvbmVudDtcblxuICAgIGNvbnN0IG5hbWVzID0ge307XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnRpZXMoVUlraXQucHJvdG90eXBlLCB7XG5cbiAgICAgICAgJGNvbnRhaW5lcjogT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihVSWtpdCwgJ2NvbnRhaW5lcicpLFxuXG4gICAgICAgICRuYW1lOiB7XG5cbiAgICAgICAgICAgIGdldCgpIHtcbiAgICAgICAgICAgICAgICBjb25zdCB7bmFtZX0gPSB0aGlzLiRvcHRpb25zO1xuXG4gICAgICAgICAgICAgICAgaWYgKCFuYW1lc1tuYW1lXSkge1xuICAgICAgICAgICAgICAgICAgICBuYW1lc1tuYW1lXSA9IFVJa2l0LnByZWZpeCArIGh5cGhlbmF0ZShuYW1lKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gbmFtZXNbbmFtZV07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfSk7XG5cbn1cbiIsImltcG9ydCB7JCQsIGFzc2lnbiwgY2FtZWxpemUsIGZhc3Rkb20sIGh5cGhlbmF0ZSwgaXNQbGFpbk9iamVjdCwgc3RhcnRzV2l0aH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgY29uc3QgREFUQSA9IFVJa2l0LmRhdGE7XG5cbiAgICBjb25zdCBjb21wb25lbnRzID0ge307XG5cbiAgICBVSWtpdC5jb21wb25lbnQgPSBmdW5jdGlvbiAobmFtZSwgb3B0aW9ucykge1xuXG4gICAgICAgIGNvbnN0IGlkID0gaHlwaGVuYXRlKG5hbWUpO1xuXG4gICAgICAgIG5hbWUgPSBjYW1lbGl6ZShpZCk7XG5cbiAgICAgICAgaWYgKCFvcHRpb25zKSB7XG5cbiAgICAgICAgICAgIGlmIChpc1BsYWluT2JqZWN0KGNvbXBvbmVudHNbbmFtZV0pKSB7XG4gICAgICAgICAgICAgICAgY29tcG9uZW50c1tuYW1lXSA9IFVJa2l0LmV4dGVuZChjb21wb25lbnRzW25hbWVdKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGNvbXBvbmVudHNbbmFtZV07XG5cbiAgICAgICAgfVxuXG4gICAgICAgIFVJa2l0W25hbWVdID0gZnVuY3Rpb24gKGVsZW1lbnQsIGRhdGEpIHtcblxuICAgICAgICAgICAgY29uc3QgY29tcG9uZW50ID0gVUlraXQuY29tcG9uZW50KG5hbWUpO1xuXG4gICAgICAgICAgICByZXR1cm4gY29tcG9uZW50Lm9wdGlvbnMuZnVuY3Rpb25hbFxuICAgICAgICAgICAgICAgID8gbmV3IGNvbXBvbmVudCh7ZGF0YTogaXNQbGFpbk9iamVjdChlbGVtZW50KSA/IGVsZW1lbnQgOiBbLi4uYXJndW1lbnRzXX0pXG4gICAgICAgICAgICAgICAgOiAhZWxlbWVudCA/IGluaXQoZWxlbWVudCkgOiAkJChlbGVtZW50KS5tYXAoaW5pdClbMF07XG5cbiAgICAgICAgICAgIGZ1bmN0aW9uIGluaXQoZWxlbWVudCkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgaW5zdGFuY2UgPSBVSWtpdC5nZXRDb21wb25lbnQoZWxlbWVudCwgbmFtZSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoaW5zdGFuY2UpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFkYXRhKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gaW5zdGFuY2U7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbnN0YW5jZS4kZGVzdHJveSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIG5ldyBjb21wb25lbnQoe2VsOiBlbGVtZW50LCBkYXRhfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIGNvbnN0IG9wdCA9IGlzUGxhaW5PYmplY3Qob3B0aW9ucykgPyBhc3NpZ24oe30sIG9wdGlvbnMpIDogb3B0aW9ucy5vcHRpb25zO1xuXG4gICAgICAgIG9wdC5uYW1lID0gbmFtZTtcblxuICAgICAgICBpZiAob3B0Lmluc3RhbGwpIHtcbiAgICAgICAgICAgIG9wdC5pbnN0YWxsKFVJa2l0LCBvcHQsIG5hbWUpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKFVJa2l0Ll9pbml0aWFsaXplZCAmJiAhb3B0LmZ1bmN0aW9uYWwpIHtcbiAgICAgICAgICAgIGZhc3Rkb20ucmVhZCgoKSA9PiBVSWtpdFtuYW1lXShgW3VrLSR7aWR9XSxbZGF0YS11ay0ke2lkfV1gKSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gY29tcG9uZW50c1tuYW1lXSA9IGlzUGxhaW5PYmplY3Qob3B0aW9ucykgPyBvcHQgOiBvcHRpb25zO1xuICAgIH07XG5cbiAgICBVSWtpdC5nZXRDb21wb25lbnRzID0gZWxlbWVudCA9PiBlbGVtZW50ICYmIGVsZW1lbnRbREFUQV0gfHwge307XG4gICAgVUlraXQuZ2V0Q29tcG9uZW50ID0gKGVsZW1lbnQsIG5hbWUpID0+IFVJa2l0LmdldENvbXBvbmVudHMoZWxlbWVudClbbmFtZV07XG5cbiAgICBVSWtpdC5jb25uZWN0ID0gbm9kZSA9PiB7XG5cbiAgICAgICAgaWYgKG5vZGVbREFUQV0pIHtcbiAgICAgICAgICAgIGZvciAoY29uc3QgbmFtZSBpbiBub2RlW0RBVEFdKSB7XG4gICAgICAgICAgICAgICAgbm9kZVtEQVRBXVtuYW1lXS5fY2FsbENvbm5lY3RlZCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBub2RlLmF0dHJpYnV0ZXMubGVuZ3RoOyBpKyspIHtcblxuICAgICAgICAgICAgY29uc3QgbmFtZSA9IGdldENvbXBvbmVudE5hbWUobm9kZS5hdHRyaWJ1dGVzW2ldLm5hbWUpO1xuXG4gICAgICAgICAgICBpZiAobmFtZSAmJiBuYW1lIGluIGNvbXBvbmVudHMpIHtcbiAgICAgICAgICAgICAgICBVSWtpdFtuYW1lXShub2RlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgVUlraXQuZGlzY29ubmVjdCA9IG5vZGUgPT4ge1xuICAgICAgICBmb3IgKGNvbnN0IG5hbWUgaW4gbm9kZVtEQVRBXSkge1xuICAgICAgICAgICAgbm9kZVtEQVRBXVtuYW1lXS5fY2FsbERpc2Nvbm5lY3RlZCgpO1xuICAgICAgICB9XG4gICAgfTtcblxufVxuXG5leHBvcnQgZnVuY3Rpb24gZ2V0Q29tcG9uZW50TmFtZShhdHRyaWJ1dGUpIHtcbiAgICByZXR1cm4gc3RhcnRzV2l0aChhdHRyaWJ1dGUsICd1ay0nKSB8fCBzdGFydHNXaXRoKGF0dHJpYnV0ZSwgJ2RhdGEtdWstJylcbiAgICAgICAgPyBjYW1lbGl6ZShhdHRyaWJ1dGUucmVwbGFjZSgnZGF0YS11ay0nLCAnJykucmVwbGFjZSgndWstJywgJycpKVxuICAgICAgICA6IGZhbHNlO1xufVxuIiwiaW1wb3J0IGdsb2JhbEFQSSBmcm9tICcuL2dsb2JhbCc7XG5pbXBvcnQgaG9va3NBUEkgZnJvbSAnLi9ob29rcyc7XG5pbXBvcnQgc3RhdGVBUEkgZnJvbSAnLi9zdGF0ZSc7XG5pbXBvcnQgaW5zdGFuY2VBUEkgZnJvbSAnLi9pbnN0YW5jZSc7XG5pbXBvcnQgY29tcG9uZW50QVBJIGZyb20gJy4vY29tcG9uZW50JztcbmltcG9ydCAqIGFzIHV0aWwgZnJvbSAndWlraXQtdXRpbCc7XG5cbmNvbnN0IFVJa2l0ID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICB0aGlzLl9pbml0KG9wdGlvbnMpO1xufTtcblxuVUlraXQudXRpbCA9IHV0aWw7XG5VSWtpdC5kYXRhID0gJ19fdWlraXRfXyc7XG5VSWtpdC5wcmVmaXggPSAndWstJztcblVJa2l0Lm9wdGlvbnMgPSB7fTtcblVJa2l0LnZlcnNpb24gPSBWRVJTSU9OO1xuXG5nbG9iYWxBUEkoVUlraXQpO1xuaG9va3NBUEkoVUlraXQpO1xuc3RhdGVBUEkoVUlraXQpO1xuY29tcG9uZW50QVBJKFVJa2l0KTtcbmluc3RhbmNlQVBJKFVJa2l0KTtcblxuZXhwb3J0IGRlZmF1bHQgVUlraXQ7XG4iLCJpbXBvcnQge2NzcywgZmFzdGRvbSwgZ2V0RXZlbnRQb3MsIGlzVG91Y2gsIG9uLCBvbmNlLCBwb2ludGVyQ2FuY2VsLCBwb2ludGVyRG93biwgcG9pbnRlclVwLCByZWFkeSwgdG9NcywgdHJpZ2dlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIChVSWtpdCkge1xuXG4gICAgcmVhZHkoKCkgPT4ge1xuXG4gICAgICAgIFVJa2l0LnVwZGF0ZSgpO1xuICAgICAgICBvbih3aW5kb3csICdsb2FkIHJlc2l6ZScsICgpID0+IFVJa2l0LnVwZGF0ZShudWxsLCAncmVzaXplJykpO1xuICAgICAgICBvbihkb2N1bWVudCwgJ2xvYWRlZG1ldGFkYXRhIGxvYWQnLCAoe3RhcmdldH0pID0+IFVJa2l0LnVwZGF0ZSh0YXJnZXQsICdyZXNpemUnKSwgdHJ1ZSk7XG5cbiAgICAgICAgLy8gdGhyb3R0bGUgYHNjcm9sbGAgZXZlbnQgKFNhZmFyaSB0cmlnZ2VycyBtdWx0aXBsZSBgc2Nyb2xsYCBldmVudHMgcGVyIGZyYW1lKVxuICAgICAgICBsZXQgcGVuZGluZztcbiAgICAgICAgb24od2luZG93LCAnc2Nyb2xsJywgZSA9PiB7XG5cbiAgICAgICAgICAgIGlmIChwZW5kaW5nKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcGVuZGluZyA9IHRydWU7XG4gICAgICAgICAgICBmYXN0ZG9tLndyaXRlKCgpID0+IHBlbmRpbmcgPSBmYWxzZSk7XG5cbiAgICAgICAgICAgIFVJa2l0LnVwZGF0ZShudWxsLCBlLnR5cGUpO1xuXG4gICAgICAgIH0sIHtwYXNzaXZlOiB0cnVlLCBjYXB0dXJlOiB0cnVlfSk7XG5cbiAgICAgICAgbGV0IHN0YXJ0ZWQgPSAwO1xuICAgICAgICBvbihkb2N1bWVudCwgJ2FuaW1hdGlvbnN0YXJ0JywgKHt0YXJnZXR9KSA9PiB7XG4gICAgICAgICAgICBpZiAoKGNzcyh0YXJnZXQsICdhbmltYXRpb25OYW1lJykgfHwgJycpLm1hdGNoKC9edWstLioobGVmdHxyaWdodCkvKSkge1xuXG4gICAgICAgICAgICAgICAgc3RhcnRlZCsrO1xuICAgICAgICAgICAgICAgIGNzcyhkb2N1bWVudC5ib2R5LCAnb3ZlcmZsb3dYJywgJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoIS0tc3RhcnRlZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmJvZHksICdvdmVyZmxvd1gnLCAnJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9LCB0b01zKGNzcyh0YXJnZXQsICdhbmltYXRpb25EdXJhdGlvbicpKSArIDEwMCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sIHRydWUpO1xuXG4gICAgICAgIGxldCBvZmY7XG4gICAgICAgIG9uKGRvY3VtZW50LCBwb2ludGVyRG93biwgZSA9PiB7XG5cbiAgICAgICAgICAgIG9mZiAmJiBvZmYoKTtcblxuICAgICAgICAgICAgaWYgKCFpc1RvdWNoKGUpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBIYW5kbGUgU3dpcGUgR2VzdHVyZVxuICAgICAgICAgICAgY29uc3QgcG9zID0gZ2V0RXZlbnRQb3MoZSk7XG4gICAgICAgICAgICBjb25zdCB0YXJnZXQgPSAndGFnTmFtZScgaW4gZS50YXJnZXQgPyBlLnRhcmdldCA6IGUudGFyZ2V0LnBhcmVudE5vZGU7XG4gICAgICAgICAgICBvZmYgPSBvbmNlKGRvY3VtZW50LCBgJHtwb2ludGVyVXB9ICR7cG9pbnRlckNhbmNlbH1gLCBlID0+IHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHt4LCB5fSA9IGdldEV2ZW50UG9zKGUpO1xuXG4gICAgICAgICAgICAgICAgLy8gc3dpcGVcbiAgICAgICAgICAgICAgICBpZiAodGFyZ2V0ICYmIHggJiYgTWF0aC5hYnMocG9zLnggLSB4KSA+IDEwMCB8fCB5ICYmIE1hdGguYWJzKHBvcy55IC0geSkgPiAxMDApIHtcblxuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGFyZ2V0LCAnc3dpcGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGFyZ2V0LCBgc3dpcGUke3N3aXBlRGlyZWN0aW9uKHBvcy54LCBwb3MueSwgeCwgeSl9YCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gRm9yY2UgY2xpY2sgZXZlbnQgYW55d2hlcmUgb24gaU9TIDwgMTNcbiAgICAgICAgICAgIGlmIChwb2ludGVyRG93biA9PT0gJ3RvdWNoc3RhcnQnKSB7XG4gICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmJvZHksICdjdXJzb3InLCAncG9pbnRlcicpO1xuICAgICAgICAgICAgICAgIG9uY2UoZG9jdW1lbnQsIGAke3BvaW50ZXJVcH0gJHtwb2ludGVyQ2FuY2VsfWAsICgpID0+XG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNzcyhkb2N1bWVudC5ib2R5LCAnY3Vyc29yJywgJycpXG4gICAgICAgICAgICAgICAgICAgICwgNTApXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LCB7cGFzc2l2ZTogdHJ1ZX0pO1xuXG4gICAgfSk7XG5cbn1cblxuZnVuY3Rpb24gc3dpcGVEaXJlY3Rpb24oeDEsIHkxLCB4MiwgeTIpIHtcbiAgICByZXR1cm4gTWF0aC5hYnMoeDEgLSB4MikgPj0gTWF0aC5hYnMoeTEgLSB5MilcbiAgICAgICAgPyB4MSAtIHgyID4gMFxuICAgICAgICAgICAgPyAnTGVmdCdcbiAgICAgICAgICAgIDogJ1JpZ2h0J1xuICAgICAgICA6IHkxIC0geTIgPiAwXG4gICAgICAgICAgICA/ICdVcCdcbiAgICAgICAgICAgIDogJ0Rvd24nO1xufVxuIiwiaW1wb3J0IHtnZXRDb21wb25lbnROYW1lfSBmcm9tICcuL2NvbXBvbmVudCc7XG5pbXBvcnQge2FwcGx5LCBmYXN0ZG9tLCBoYXNBdHRyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gKFVJa2l0KSB7XG5cbiAgICBjb25zdCB7Y29ubmVjdCwgZGlzY29ubmVjdH0gPSBVSWtpdDtcblxuICAgIGlmICghKCdNdXRhdGlvbk9ic2VydmVyJyBpbiB3aW5kb3cpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBmYXN0ZG9tLnJlYWQoaW5pdCk7XG5cbiAgICBmdW5jdGlvbiBpbml0KCkge1xuXG4gICAgICAgIGlmIChkb2N1bWVudC5ib2R5KSB7XG4gICAgICAgICAgICBhcHBseShkb2N1bWVudC5ib2R5LCBjb25uZWN0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIChuZXcgTXV0YXRpb25PYnNlcnZlcihtdXRhdGlvbnMgPT4gbXV0YXRpb25zLmZvckVhY2goYXBwbHlNdXRhdGlvbikpKS5vYnNlcnZlKGRvY3VtZW50LCB7XG4gICAgICAgICAgICBjaGlsZExpc3Q6IHRydWUsXG4gICAgICAgICAgICBzdWJ0cmVlOiB0cnVlLFxuICAgICAgICAgICAgY2hhcmFjdGVyRGF0YTogdHJ1ZSxcbiAgICAgICAgICAgIGF0dHJpYnV0ZXM6IHRydWVcbiAgICAgICAgfSk7XG5cbiAgICAgICAgVUlraXQuX2luaXRpYWxpemVkID0gdHJ1ZTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBhcHBseU11dGF0aW9uKG11dGF0aW9uKSB7XG5cbiAgICAgICAgY29uc3Qge3RhcmdldCwgdHlwZX0gPSBtdXRhdGlvbjtcblxuICAgICAgICBjb25zdCB1cGRhdGUgPSB0eXBlICE9PSAnYXR0cmlidXRlcydcbiAgICAgICAgICAgID8gYXBwbHlDaGlsZExpc3QobXV0YXRpb24pXG4gICAgICAgICAgICA6IGFwcGx5QXR0cmlidXRlKG11dGF0aW9uKTtcblxuICAgICAgICB1cGRhdGUgJiYgVUlraXQudXBkYXRlKHRhcmdldCk7XG5cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBhcHBseUF0dHJpYnV0ZSh7dGFyZ2V0LCBhdHRyaWJ1dGVOYW1lfSkge1xuXG4gICAgICAgIGlmIChhdHRyaWJ1dGVOYW1lID09PSAnaHJlZicpIHtcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgbmFtZSA9IGdldENvbXBvbmVudE5hbWUoYXR0cmlidXRlTmFtZSk7XG5cbiAgICAgICAgaWYgKCFuYW1lIHx8ICEobmFtZSBpbiBVSWtpdCkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChoYXNBdHRyKHRhcmdldCwgYXR0cmlidXRlTmFtZSkpIHtcbiAgICAgICAgICAgIFVJa2l0W25hbWVdKHRhcmdldCk7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGNvbXBvbmVudCA9IFVJa2l0LmdldENvbXBvbmVudCh0YXJnZXQsIG5hbWUpO1xuXG4gICAgICAgIGlmIChjb21wb25lbnQpIHtcbiAgICAgICAgICAgIGNvbXBvbmVudC4kZGVzdHJveSgpO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cblxuICAgIH1cblxuICAgIGZ1bmN0aW9uIGFwcGx5Q2hpbGRMaXN0KHthZGRlZE5vZGVzLCByZW1vdmVkTm9kZXN9KSB7XG5cbiAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBhZGRlZE5vZGVzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICBhcHBseShhZGRlZE5vZGVzW2ldLCBjb25uZWN0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgcmVtb3ZlZE5vZGVzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICBhcHBseShyZW1vdmVkTm9kZXNbaV0sIGRpc2Nvbm5lY3QpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuXG59XG4iLCJpbXBvcnQge2FkZENsYXNzLCBoYXNDbGFzc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgIWhhc0NsYXNzKHRoaXMuJGVsLCB0aGlzLiRuYW1lKSAmJiBhZGRDbGFzcyh0aGlzLiRlbCwgdGhpcy4kbmFtZSk7XG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHskJCwgQW5pbWF0aW9uLCBhc3NpZ24sIGF0dHIsIGNzcywgZmFzdGRvbSwgaGFzQXR0ciwgaGFzQ2xhc3MsIGhlaWdodCwgaW5jbHVkZXMsIGlzQm9vbGVhbiwgaXNGdW5jdGlvbiwgaXNVbmRlZmluZWQsIGlzVmlzaWJsZSwgbm9vcCwgUHJvbWlzZSwgdG9GbG9hdCwgdG9nZ2xlQ2xhc3MsIHRvTm9kZXMsIFRyYW5zaXRpb24sIHRyaWdnZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBwcm9wczoge1xuICAgICAgICBjbHM6IEJvb2xlYW4sXG4gICAgICAgIGFuaW1hdGlvbjogJ2xpc3QnLFxuICAgICAgICBkdXJhdGlvbjogTnVtYmVyLFxuICAgICAgICBvcmlnaW46IFN0cmluZyxcbiAgICAgICAgdHJhbnNpdGlvbjogU3RyaW5nLFxuICAgICAgICBxdWV1ZWQ6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBjbHM6IGZhbHNlLFxuICAgICAgICBhbmltYXRpb246IFtmYWxzZV0sXG4gICAgICAgIGR1cmF0aW9uOiAyMDAsXG4gICAgICAgIG9yaWdpbjogZmFsc2UsXG4gICAgICAgIHRyYW5zaXRpb246ICdsaW5lYXInLFxuICAgICAgICBxdWV1ZWQ6IGZhbHNlLFxuXG4gICAgICAgIGluaXRQcm9wczoge1xuICAgICAgICAgICAgb3ZlcmZsb3c6ICcnLFxuICAgICAgICAgICAgaGVpZ2h0OiAnJyxcbiAgICAgICAgICAgIHBhZGRpbmdUb3A6ICcnLFxuICAgICAgICAgICAgcGFkZGluZ0JvdHRvbTogJycsXG4gICAgICAgICAgICBtYXJnaW5Ub3A6ICcnLFxuICAgICAgICAgICAgbWFyZ2luQm90dG9tOiAnJ1xuICAgICAgICB9LFxuXG4gICAgICAgIGhpZGVQcm9wczoge1xuICAgICAgICAgICAgb3ZlcmZsb3c6ICdoaWRkZW4nLFxuICAgICAgICAgICAgaGVpZ2h0OiAwLFxuICAgICAgICAgICAgcGFkZGluZ1RvcDogMCxcbiAgICAgICAgICAgIHBhZGRpbmdCb3R0b206IDAsXG4gICAgICAgICAgICBtYXJnaW5Ub3A6IDAsXG4gICAgICAgICAgICBtYXJnaW5Cb3R0b206IDBcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaGFzQW5pbWF0aW9uKHthbmltYXRpb259KSB7XG4gICAgICAgICAgICByZXR1cm4gISFhbmltYXRpb25bMF07XG4gICAgICAgIH0sXG5cbiAgICAgICAgaGFzVHJhbnNpdGlvbih7YW5pbWF0aW9ufSkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuaGFzQW5pbWF0aW9uICYmIGFuaW1hdGlvblswXSA9PT0gdHJ1ZTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICB0b2dnbGVFbGVtZW50KHRhcmdldHMsIHNob3csIGFuaW1hdGUpIHtcbiAgICAgICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcblxuICAgICAgICAgICAgICAgIHRhcmdldHMgPSB0b05vZGVzKHRhcmdldHMpO1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWxsID0gdGFyZ2V0cyA9PiBQcm9taXNlLmFsbCh0YXJnZXRzLm1hcChlbCA9PiB0aGlzLl90b2dnbGVFbGVtZW50KGVsLCBzaG93LCBhbmltYXRlKSkpO1xuXG4gICAgICAgICAgICAgICAgbGV0IHA7XG5cbiAgICAgICAgICAgICAgICBpZiAoIXRoaXMucXVldWVkIHx8ICFpc1VuZGVmaW5lZChhbmltYXRlKSB8fCAhaXNVbmRlZmluZWQoc2hvdykgfHwgIXRoaXMuaGFzQW5pbWF0aW9uIHx8IHRhcmdldHMubGVuZ3RoIDwgMikge1xuXG4gICAgICAgICAgICAgICAgICAgIHAgPSBhbGwodGFyZ2V0cyk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHRvZ2dsZWQgPSB0YXJnZXRzLmZpbHRlcihlbCA9PiB0aGlzLmlzVG9nZ2xlZChlbCkpO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCB1bnRvZ2dsZWQgPSB0YXJnZXRzLmZpbHRlcihlbCA9PiAhaW5jbHVkZXModG9nZ2xlZCwgZWwpKTtcbiAgICAgICAgICAgICAgICAgICAgY29uc3Qge2JvZHl9ID0gZG9jdW1lbnQ7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHNjcm9sbCA9IGJvZHkuc2Nyb2xsVG9wO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBbZWxdID0gdG9nZ2xlZDtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaW5Qcm9ncmVzcyA9IEFuaW1hdGlvbi5pblByb2dyZXNzKGVsKSAmJiBoYXNDbGFzcyhlbCwgJ3VrLWFuaW1hdGlvbi1sZWF2ZScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfHwgVHJhbnNpdGlvbi5pblByb2dyZXNzKGVsKSAmJiBlbC5zdHlsZS5oZWlnaHQgPT09ICcwcHgnO1xuXG4gICAgICAgICAgICAgICAgICAgIHAgPSBhbGwodG9nZ2xlZCk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFpblByb2dyZXNzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBwID0gcC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBwID0gYWxsKHVudG9nZ2xlZCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYm9keS5zY3JvbGxUb3AgPSBzY3JvbGw7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHA7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcC50aGVuKHJlc29sdmUsIG5vb3ApO1xuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSxcblxuICAgICAgICB0b2dnbGVOb3codGFyZ2V0cywgc2hvdykge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMudG9nZ2xlRWxlbWVudCh0YXJnZXRzLCBzaG93LCBmYWxzZSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgaXNUb2dnbGVkKGVsKSB7XG4gICAgICAgICAgICBjb25zdCBub2RlcyA9IHRvTm9kZXMoZWwgfHwgdGhpcy4kZWwpO1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuY2xzXG4gICAgICAgICAgICAgICAgPyBoYXNDbGFzcyhub2RlcywgdGhpcy5jbHMuc3BsaXQoJyAnKVswXSlcbiAgICAgICAgICAgICAgICA6ICFoYXNBdHRyKG5vZGVzLCAnaGlkZGVuJyk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdXBkYXRlQXJpYShlbCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuY2xzID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgIGF0dHIoZWwsICdhcmlhLWhpZGRlbicsICF0aGlzLmlzVG9nZ2xlZChlbCkpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIF90b2dnbGVFbGVtZW50KGVsLCBzaG93LCBhbmltYXRlKSB7XG5cbiAgICAgICAgICAgIHNob3cgPSBpc0Jvb2xlYW4oc2hvdylcbiAgICAgICAgICAgICAgICA/IHNob3dcbiAgICAgICAgICAgICAgICA6IEFuaW1hdGlvbi5pblByb2dyZXNzKGVsKVxuICAgICAgICAgICAgICAgICAgICA/IGhhc0NsYXNzKGVsLCAndWstYW5pbWF0aW9uLWxlYXZlJylcbiAgICAgICAgICAgICAgICAgICAgOiBUcmFuc2l0aW9uLmluUHJvZ3Jlc3MoZWwpXG4gICAgICAgICAgICAgICAgICAgICAgICA/IGVsLnN0eWxlLmhlaWdodCA9PT0gJzBweCdcbiAgICAgICAgICAgICAgICAgICAgICAgIDogIXRoaXMuaXNUb2dnbGVkKGVsKTtcblxuICAgICAgICAgICAgaWYgKCF0cmlnZ2VyKGVsLCBgYmVmb3JlJHtzaG93ID8gJ3Nob3cnIDogJ2hpZGUnfWAsIFt0aGlzXSkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZWplY3QoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgcHJvbWlzZSA9IChcbiAgICAgICAgICAgICAgICBpc0Z1bmN0aW9uKGFuaW1hdGUpXG4gICAgICAgICAgICAgICAgICAgID8gYW5pbWF0ZVxuICAgICAgICAgICAgICAgICAgICA6IGFuaW1hdGUgPT09IGZhbHNlIHx8ICF0aGlzLmhhc0FuaW1hdGlvblxuICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLl90b2dnbGVcbiAgICAgICAgICAgICAgICAgICAgICAgIDogdGhpcy5oYXNUcmFuc2l0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPyB0b2dnbGVIZWlnaHQodGhpcylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA6IHRvZ2dsZUFuaW1hdGlvbih0aGlzKVxuICAgICAgICAgICAgKShlbCwgc2hvdyk7XG5cbiAgICAgICAgICAgIHRyaWdnZXIoZWwsIHNob3cgPyAnc2hvdycgOiAnaGlkZScsIFt0aGlzXSk7XG5cbiAgICAgICAgICAgIGNvbnN0IGZpbmFsID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIHRyaWdnZXIoZWwsIHNob3cgPyAnc2hvd24nIDogJ2hpZGRlbicsIFt0aGlzXSk7XG4gICAgICAgICAgICAgICAgdGhpcy4kdXBkYXRlKGVsKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIHJldHVybiBwcm9taXNlID8gcHJvbWlzZS50aGVuKGZpbmFsKSA6IFByb21pc2UucmVzb2x2ZShmaW5hbCgpKTtcbiAgICAgICAgfSxcblxuICAgICAgICBfdG9nZ2xlKGVsLCB0b2dnbGVkKSB7XG5cbiAgICAgICAgICAgIGlmICghZWwpIHtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRvZ2dsZWQgPSBCb29sZWFuKHRvZ2dsZWQpO1xuXG4gICAgICAgICAgICBsZXQgY2hhbmdlZDtcbiAgICAgICAgICAgIGlmICh0aGlzLmNscykge1xuICAgICAgICAgICAgICAgIGNoYW5nZWQgPSBpbmNsdWRlcyh0aGlzLmNscywgJyAnKSB8fCB0b2dnbGVkICE9PSBoYXNDbGFzcyhlbCwgdGhpcy5jbHMpO1xuICAgICAgICAgICAgICAgIGNoYW5nZWQgJiYgdG9nZ2xlQ2xhc3MoZWwsIHRoaXMuY2xzLCBpbmNsdWRlcyh0aGlzLmNscywgJyAnKSA/IHVuZGVmaW5lZCA6IHRvZ2dsZWQpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBjaGFuZ2VkID0gdG9nZ2xlZCA9PT0gaGFzQXR0cihlbCwgJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIGNoYW5nZWQgJiYgYXR0cihlbCwgJ2hpZGRlbicsICF0b2dnbGVkID8gJycgOiBudWxsKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCQoJ1thdXRvZm9jdXNdJywgZWwpLnNvbWUoZWwgPT4gaXNWaXNpYmxlKGVsKSA/IGVsLmZvY3VzKCkgfHwgdHJ1ZSA6IGVsLmJsdXIoKSk7XG5cbiAgICAgICAgICAgIHRoaXMudXBkYXRlQXJpYShlbCk7XG4gICAgICAgICAgICBjaGFuZ2VkICYmIHRoaXMuJHVwZGF0ZShlbCk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZnVuY3Rpb24gdG9nZ2xlSGVpZ2h0KHtpc1RvZ2dsZWQsIGR1cmF0aW9uLCBpbml0UHJvcHMsIGhpZGVQcm9wcywgdHJhbnNpdGlvbiwgX3RvZ2dsZX0pIHtcbiAgICByZXR1cm4gKGVsLCBzaG93KSA9PiB7XG5cbiAgICAgICAgY29uc3QgaW5Qcm9ncmVzcyA9IFRyYW5zaXRpb24uaW5Qcm9ncmVzcyhlbCk7XG4gICAgICAgIGNvbnN0IGlubmVyID0gZWwuaGFzQ2hpbGROb2RlcyA/IHRvRmxvYXQoY3NzKGVsLmZpcnN0RWxlbWVudENoaWxkLCAnbWFyZ2luVG9wJykpICsgdG9GbG9hdChjc3MoZWwubGFzdEVsZW1lbnRDaGlsZCwgJ21hcmdpbkJvdHRvbScpKSA6IDA7XG4gICAgICAgIGNvbnN0IGN1cnJlbnRIZWlnaHQgPSBpc1Zpc2libGUoZWwpID8gaGVpZ2h0KGVsKSArIChpblByb2dyZXNzID8gMCA6IGlubmVyKSA6IDA7XG5cbiAgICAgICAgVHJhbnNpdGlvbi5jYW5jZWwoZWwpO1xuXG4gICAgICAgIGlmICghaXNUb2dnbGVkKGVsKSkge1xuICAgICAgICAgICAgX3RvZ2dsZShlbCwgdHJ1ZSk7XG4gICAgICAgIH1cblxuICAgICAgICBoZWlnaHQoZWwsICcnKTtcblxuICAgICAgICAvLyBVcGRhdGUgY2hpbGQgY29tcG9uZW50cyBmaXJzdFxuICAgICAgICBmYXN0ZG9tLmZsdXNoKCk7XG5cbiAgICAgICAgY29uc3QgZW5kSGVpZ2h0ID0gaGVpZ2h0KGVsKSArIChpblByb2dyZXNzID8gMCA6IGlubmVyKTtcbiAgICAgICAgaGVpZ2h0KGVsLCBjdXJyZW50SGVpZ2h0KTtcblxuICAgICAgICByZXR1cm4gKHNob3dcbiAgICAgICAgICAgICAgICA/IFRyYW5zaXRpb24uc3RhcnQoZWwsIGFzc2lnbih7fSwgaW5pdFByb3BzLCB7b3ZlcmZsb3c6ICdoaWRkZW4nLCBoZWlnaHQ6IGVuZEhlaWdodH0pLCBNYXRoLnJvdW5kKGR1cmF0aW9uICogKDEgLSBjdXJyZW50SGVpZ2h0IC8gZW5kSGVpZ2h0KSksIHRyYW5zaXRpb24pXG4gICAgICAgICAgICAgICAgOiBUcmFuc2l0aW9uLnN0YXJ0KGVsLCBoaWRlUHJvcHMsIE1hdGgucm91bmQoZHVyYXRpb24gKiAoY3VycmVudEhlaWdodCAvIGVuZEhlaWdodCkpLCB0cmFuc2l0aW9uKS50aGVuKCgpID0+IF90b2dnbGUoZWwsIGZhbHNlKSlcbiAgICAgICAgKS50aGVuKCgpID0+IGNzcyhlbCwgaW5pdFByb3BzKSk7XG5cbiAgICB9O1xufVxuXG5mdW5jdGlvbiB0b2dnbGVBbmltYXRpb24oe2FuaW1hdGlvbiwgZHVyYXRpb24sIG9yaWdpbiwgX3RvZ2dsZX0pIHtcbiAgICByZXR1cm4gKGVsLCBzaG93KSA9PiB7XG5cbiAgICAgICAgQW5pbWF0aW9uLmNhbmNlbChlbCk7XG5cbiAgICAgICAgaWYgKHNob3cpIHtcbiAgICAgICAgICAgIF90b2dnbGUoZWwsIHRydWUpO1xuICAgICAgICAgICAgcmV0dXJuIEFuaW1hdGlvbi5pbihlbCwgYW5pbWF0aW9uWzBdLCBkdXJhdGlvbiwgb3JpZ2luKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBBbmltYXRpb24ub3V0KGVsLCBhbmltYXRpb25bMV0gfHwgYW5pbWF0aW9uWzBdLCBkdXJhdGlvbiwgb3JpZ2luKS50aGVuKCgpID0+IF90b2dnbGUoZWwsIGZhbHNlKSk7XG4gICAgfTtcbn1cbiIsImltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQgVG9nZ2xhYmxlIGZyb20gJy4uL21peGluL3RvZ2dsYWJsZSc7XG5pbXBvcnQgeyQsICQkLCBhdHRyLCBmaWx0ZXIsIGdldEluZGV4LCBoYXNDbGFzcywgaW5jbHVkZXMsIGluZGV4LCBpc0luVmlldywgc2Nyb2xsSW50b1ZpZXcsIHRvZ2dsZUNsYXNzLCB1bndyYXAsIHdyYXBBbGx9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgVG9nZ2xhYmxlXSxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIHRhcmdldHM6IFN0cmluZyxcbiAgICAgICAgYWN0aXZlOiBudWxsLFxuICAgICAgICBjb2xsYXBzaWJsZTogQm9vbGVhbixcbiAgICAgICAgbXVsdGlwbGU6IEJvb2xlYW4sXG4gICAgICAgIHRvZ2dsZTogU3RyaW5nLFxuICAgICAgICBjb250ZW50OiBTdHJpbmcsXG4gICAgICAgIHRyYW5zaXRpb246IFN0cmluZ1xuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIHRhcmdldHM6ICc+IConLFxuICAgICAgICBhY3RpdmU6IGZhbHNlLFxuICAgICAgICBhbmltYXRpb246IFt0cnVlXSxcbiAgICAgICAgY29sbGFwc2libGU6IHRydWUsXG4gICAgICAgIG11bHRpcGxlOiBmYWxzZSxcbiAgICAgICAgY2xzT3BlbjogJ3VrLW9wZW4nLFxuICAgICAgICB0b2dnbGU6ICc+IC51ay1hY2NvcmRpb24tdGl0bGUnLFxuICAgICAgICBjb250ZW50OiAnPiAudWstYWNjb3JkaW9uLWNvbnRlbnQnLFxuICAgICAgICB0cmFuc2l0aW9uOiAnZWFzZSdcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBpdGVtcyh7dGFyZ2V0c30sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuICQkKHRhcmdldHMsICRlbCk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBldmVudHM6IFtcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdjbGljaycsXG5cbiAgICAgICAgICAgIGRlbGVnYXRlKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBgJHt0aGlzLnRhcmdldHN9ICR7dGhpcy4kcHJvcHMudG9nZ2xlfWA7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgdGhpcy50b2dnbGUoaW5kZXgoJCQoYCR7dGhpcy50YXJnZXRzfSAke3RoaXMuJHByb3BzLnRvZ2dsZX1gLCB0aGlzLiRlbCksIGUuY3VycmVudCkpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgaWYgKHRoaXMuYWN0aXZlID09PSBmYWxzZSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5pdGVtc1tOdW1iZXIodGhpcy5hY3RpdmUpXTtcbiAgICAgICAgaWYgKGFjdGl2ZSAmJiAhaGFzQ2xhc3MoYWN0aXZlLCB0aGlzLmNsc09wZW4pKSB7XG4gICAgICAgICAgICB0aGlzLnRvZ2dsZShhY3RpdmUsIGZhbHNlKTtcbiAgICAgICAgfVxuICAgIH0sXG5cbiAgICB1cGRhdGUoKSB7XG5cbiAgICAgICAgdGhpcy5pdGVtcy5mb3JFYWNoKGVsID0+IHRoaXMuX3RvZ2dsZSgkKHRoaXMuY29udGVudCwgZWwpLCBoYXNDbGFzcyhlbCwgdGhpcy5jbHNPcGVuKSkpO1xuXG4gICAgICAgIGNvbnN0IGFjdGl2ZSA9ICF0aGlzLmNvbGxhcHNpYmxlICYmICFoYXNDbGFzcyh0aGlzLml0ZW1zLCB0aGlzLmNsc09wZW4pICYmIHRoaXMuaXRlbXNbMF07XG4gICAgICAgIGlmIChhY3RpdmUpIHtcbiAgICAgICAgICAgIHRoaXMudG9nZ2xlKGFjdGl2ZSwgZmFsc2UpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICB0b2dnbGUoaXRlbSwgYW5pbWF0ZSkge1xuXG4gICAgICAgICAgICBjb25zdCBpbmRleCA9IGdldEluZGV4KGl0ZW0sIHRoaXMuaXRlbXMpO1xuICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gZmlsdGVyKHRoaXMuaXRlbXMsIGAuJHt0aGlzLmNsc09wZW59YCk7XG5cbiAgICAgICAgICAgIGl0ZW0gPSB0aGlzLml0ZW1zW2luZGV4XTtcblxuICAgICAgICAgICAgaXRlbSAmJiBbaXRlbV1cbiAgICAgICAgICAgICAgICAuY29uY2F0KCF0aGlzLm11bHRpcGxlICYmICFpbmNsdWRlcyhhY3RpdmUsIGl0ZW0pICYmIGFjdGl2ZSB8fCBbXSlcbiAgICAgICAgICAgICAgICAuZm9yRWFjaChlbCA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaXNJdGVtID0gZWwgPT09IGl0ZW07XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHN0YXRlID0gaXNJdGVtICYmICFoYXNDbGFzcyhlbCwgdGhpcy5jbHNPcGVuKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIXN0YXRlICYmIGlzSXRlbSAmJiAhdGhpcy5jb2xsYXBzaWJsZSAmJiBhY3RpdmUubGVuZ3RoIDwgMikge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdG9nZ2xlQ2xhc3MoZWwsIHRoaXMuY2xzT3Blbiwgc3RhdGUpO1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNvbnRlbnQgPSBlbC5fd3JhcHBlciA/IGVsLl93cmFwcGVyLmZpcnN0RWxlbWVudENoaWxkIDogJCh0aGlzLmNvbnRlbnQsIGVsKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIWVsLl93cmFwcGVyKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5fd3JhcHBlciA9IHdyYXBBbGwoY29udGVudCwgJzxkaXY+Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBhdHRyKGVsLl93cmFwcGVyLCAnaGlkZGVuJywgc3RhdGUgPyAnJyA6IG51bGwpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5fdG9nZ2xlKGNvbnRlbnQsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZUVsZW1lbnQoZWwuX3dyYXBwZXIsIHN0YXRlLCBhbmltYXRlKS50aGVuKCgpID0+IHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGhhc0NsYXNzKGVsLCB0aGlzLmNsc09wZW4pICE9PSBzdGF0ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCFzdGF0ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuX3RvZ2dsZShjb250ZW50LCBmYWxzZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHRvZ2dsZSA9ICQodGhpcy4kcHJvcHMudG9nZ2xlLCBlbCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGFuaW1hdGUgIT09IGZhbHNlICYmICFpc0luVmlldyh0b2dnbGUpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcm9sbEludG9WaWV3KHRvZ2dsZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBlbC5fd3JhcHBlciA9IG51bGw7XG4gICAgICAgICAgICAgICAgICAgICAgICB1bndyYXAoY29udGVudCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IENsYXNzIGZyb20gJy4uL21peGluL2NsYXNzJztcbmltcG9ydCBUb2dnbGFibGUgZnJvbSAnLi4vbWl4aW4vdG9nZ2xhYmxlJztcbmltcG9ydCB7YXNzaWdufSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3MsIFRvZ2dsYWJsZV0sXG5cbiAgICBhcmdzOiAnYW5pbWF0aW9uJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGNsb3NlOiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBhbmltYXRpb246IFt0cnVlXSxcbiAgICAgICAgc2VsQ2xvc2U6ICcudWstYWxlcnQtY2xvc2UnLFxuICAgICAgICBkdXJhdGlvbjogMTUwLFxuICAgICAgICBoaWRlUHJvcHM6IGFzc2lnbih7b3BhY2l0eTogMH0sIFRvZ2dsYWJsZS5kYXRhLmhpZGVQcm9wcylcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5zZWxDbG9zZTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLmNsb3NlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgXSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBjbG9zZSgpIHtcbiAgICAgICAgICAgIHRoaXMudG9nZ2xlRWxlbWVudCh0aGlzLiRlbCkudGhlbigoKSA9PiB0aGlzLiRkZXN0cm95KHRydWUpKTtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHtjc3MsIGhhc0F0dHIsIGlzSW5WaWV3LCBpc1Zpc2libGUsIFBsYXllcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGFyZ3M6ICdhdXRvcGxheScsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBhdXRvbXV0ZTogQm9vbGVhbixcbiAgICAgICAgYXV0b3BsYXk6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBhdXRvbXV0ZTogZmFsc2UsXG4gICAgICAgIGF1dG9wbGF5OiB0cnVlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaW5WaWV3KHthdXRvcGxheX0pIHtcbiAgICAgICAgICAgIHJldHVybiBhdXRvcGxheSA9PT0gJ2ludmlldyc7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgaWYgKHRoaXMuaW5WaWV3ICYmICFoYXNBdHRyKHRoaXMuJGVsLCAncHJlbG9hZCcpKSB7XG4gICAgICAgICAgICB0aGlzLiRlbC5wcmVsb2FkID0gJ25vbmUnO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5wbGF5ZXIgPSBuZXcgUGxheWVyKHRoaXMuJGVsKTtcblxuICAgICAgICBpZiAodGhpcy5hdXRvbXV0ZSkge1xuICAgICAgICAgICAgdGhpcy5wbGF5ZXIubXV0ZSgpO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgdXBkYXRlOiB7XG5cbiAgICAgICAgcmVhZCgpIHtcblxuICAgICAgICAgICAgcmV0dXJuICF0aGlzLnBsYXllclxuICAgICAgICAgICAgICAgID8gZmFsc2VcbiAgICAgICAgICAgICAgICA6IHtcbiAgICAgICAgICAgICAgICAgICAgdmlzaWJsZTogaXNWaXNpYmxlKHRoaXMuJGVsKSAmJiBjc3ModGhpcy4kZWwsICd2aXNpYmlsaXR5JykgIT09ICdoaWRkZW4nLFxuICAgICAgICAgICAgICAgICAgICBpblZpZXc6IHRoaXMuaW5WaWV3ICYmIGlzSW5WaWV3KHRoaXMuJGVsKVxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoe3Zpc2libGUsIGluVmlld30pIHtcblxuICAgICAgICAgICAgaWYgKCF2aXNpYmxlIHx8IHRoaXMuaW5WaWV3ICYmICFpblZpZXcpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnBsYXllci5wYXVzZSgpO1xuICAgICAgICAgICAgfSBlbHNlIGlmICh0aGlzLmF1dG9wbGF5ID09PSB0cnVlIHx8IHRoaXMuaW5WaWV3ICYmIGluVmlldykge1xuICAgICAgICAgICAgICAgIHRoaXMucGxheWVyLnBsYXkoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnLCAnc2Nyb2xsJ11cblxuICAgIH1cblxufTtcbiIsImltcG9ydCBWaWRlbyBmcm9tICcuL3ZpZGVvJztcbmltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQge2NzcywgRGltZW5zaW9uc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBWaWRlb10sXG5cbiAgICBwcm9wczoge1xuICAgICAgICB3aWR0aDogTnVtYmVyLFxuICAgICAgICBoZWlnaHQ6IE51bWJlclxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGF1dG9tdXRlOiB0cnVlXG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGVsID0gdGhpcy4kZWw7XG4gICAgICAgICAgICBjb25zdCB7b2Zmc2V0SGVpZ2h0OiBoZWlnaHQsIG9mZnNldFdpZHRoOiB3aWR0aH0gPSBlbC5wYXJlbnROb2RlO1xuICAgICAgICAgICAgY29uc3QgZGltID0gRGltZW5zaW9ucy5jb3ZlcihcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHdpZHRoOiB0aGlzLndpZHRoIHx8IGVsLm5hdHVyYWxXaWR0aCB8fCBlbC52aWRlb1dpZHRoIHx8IGVsLmNsaWVudFdpZHRoLFxuICAgICAgICAgICAgICAgICAgICBoZWlnaHQ6IHRoaXMuaGVpZ2h0IHx8IGVsLm5hdHVyYWxIZWlnaHQgfHwgZWwudmlkZW9IZWlnaHQgfHwgZWwuY2xpZW50SGVpZ2h0XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHdpZHRoOiB3aWR0aCArICh3aWR0aCAlIDIgPyAxIDogMCksXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodDogaGVpZ2h0ICsgKGhlaWdodCAlIDIgPyAxIDogMClcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICApO1xuXG4gICAgICAgICAgICBpZiAoIWRpbS53aWR0aCB8fCAhZGltLmhlaWdodCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIGRpbTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7aGVpZ2h0LCB3aWR0aH0pIHtcbiAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwge2hlaWdodCwgd2lkdGh9KTtcbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgIH1cblxufTtcbiIsImltcG9ydCB7JCwgY3NzLCBmbGlwUG9zaXRpb24sIGluY2x1ZGVzLCBpc051bWVyaWMsIGlzUnRsLCBvZmZzZXQgYXMgZ2V0T2Zmc2V0LCBwb3NpdGlvbkF0LCByZW1vdmVDbGFzc2VzLCB0b2dnbGVDbGFzc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIHByb3BzOiB7XG4gICAgICAgIHBvczogU3RyaW5nLFxuICAgICAgICBvZmZzZXQ6IG51bGwsXG4gICAgICAgIGZsaXA6IEJvb2xlYW4sXG4gICAgICAgIGNsc1BvczogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgcG9zOiBgYm90dG9tLSR7IWlzUnRsID8gJ2xlZnQnIDogJ3JpZ2h0J31gLFxuICAgICAgICBmbGlwOiB0cnVlLFxuICAgICAgICBvZmZzZXQ6IGZhbHNlLFxuICAgICAgICBjbHNQb3M6ICcnXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgcG9zKHtwb3N9KSB7XG4gICAgICAgICAgICByZXR1cm4gKHBvcyArICghaW5jbHVkZXMocG9zLCAnLScpID8gJy1jZW50ZXInIDogJycpKS5zcGxpdCgnLScpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGRpcigpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnBvc1swXTtcbiAgICAgICAgfSxcblxuICAgICAgICBhbGlnbigpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnBvc1sxXTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBwb3NpdGlvbkF0KGVsZW1lbnQsIHRhcmdldCwgYm91bmRhcnkpIHtcblxuICAgICAgICAgICAgcmVtb3ZlQ2xhc3NlcyhlbGVtZW50LCBgJHt0aGlzLmNsc1Bvc30tKHRvcHxib3R0b218bGVmdHxyaWdodCkoLVthLXpdKyk/YCk7XG4gICAgICAgICAgICBjc3MoZWxlbWVudCwge3RvcDogJycsIGxlZnQ6ICcnfSk7XG5cbiAgICAgICAgICAgIGxldCBub2RlO1xuICAgICAgICAgICAgbGV0IHtvZmZzZXR9ID0gdGhpcztcbiAgICAgICAgICAgIGNvbnN0IGF4aXMgPSB0aGlzLmdldEF4aXMoKTtcblxuICAgICAgICAgICAgaWYgKCFpc051bWVyaWMob2Zmc2V0KSkge1xuICAgICAgICAgICAgICAgIG5vZGUgPSAkKG9mZnNldCk7XG4gICAgICAgICAgICAgICAgb2Zmc2V0ID0gbm9kZVxuICAgICAgICAgICAgICAgICAgICA/IGdldE9mZnNldChub2RlKVtheGlzID09PSAneCcgPyAnbGVmdCcgOiAndG9wJ10gLSBnZXRPZmZzZXQodGFyZ2V0KVtheGlzID09PSAneCcgPyAncmlnaHQnIDogJ2JvdHRvbSddXG4gICAgICAgICAgICAgICAgICAgIDogMDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3Qge3gsIHl9ID0gcG9zaXRpb25BdChcbiAgICAgICAgICAgICAgICBlbGVtZW50LFxuICAgICAgICAgICAgICAgIHRhcmdldCxcbiAgICAgICAgICAgICAgICBheGlzID09PSAneCcgPyBgJHtmbGlwUG9zaXRpb24odGhpcy5kaXIpfSAke3RoaXMuYWxpZ259YCA6IGAke3RoaXMuYWxpZ259ICR7ZmxpcFBvc2l0aW9uKHRoaXMuZGlyKX1gLFxuICAgICAgICAgICAgICAgIGF4aXMgPT09ICd4JyA/IGAke3RoaXMuZGlyfSAke3RoaXMuYWxpZ259YCA6IGAke3RoaXMuYWxpZ259ICR7dGhpcy5kaXJ9YCxcbiAgICAgICAgICAgICAgICBheGlzID09PSAneCcgPyBgJHt0aGlzLmRpciA9PT0gJ2xlZnQnID8gLW9mZnNldCA6IG9mZnNldH1gIDogYCAke3RoaXMuZGlyID09PSAndG9wJyA/IC1vZmZzZXQgOiBvZmZzZXR9YCxcbiAgICAgICAgICAgICAgICBudWxsLFxuICAgICAgICAgICAgICAgIHRoaXMuZmxpcCxcbiAgICAgICAgICAgICAgICBib3VuZGFyeVxuICAgICAgICAgICAgKS50YXJnZXQ7XG5cbiAgICAgICAgICAgIHRoaXMuZGlyID0gYXhpcyA9PT0gJ3gnID8geCA6IHk7XG4gICAgICAgICAgICB0aGlzLmFsaWduID0gYXhpcyA9PT0gJ3gnID8geSA6IHg7XG5cbiAgICAgICAgICAgIHRvZ2dsZUNsYXNzKGVsZW1lbnQsIGAke3RoaXMuY2xzUG9zfS0ke3RoaXMuZGlyfS0ke3RoaXMuYWxpZ259YCwgdGhpcy5vZmZzZXQgPT09IGZhbHNlKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIGdldEF4aXMoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5kaXIgPT09ICd0b3AnIHx8IHRoaXMuZGlyID09PSAnYm90dG9tJyA/ICd5JyA6ICd4JztcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IFBvc2l0aW9uIGZyb20gJy4uL21peGluL3Bvc2l0aW9uJztcbmltcG9ydCBUb2dnbGFibGUgZnJvbSAnLi4vbWl4aW4vdG9nZ2xhYmxlJztcbmltcG9ydCB7YWRkQ2xhc3MsIEFuaW1hdGlvbiwgYXR0ciwgY2hpbGRyZW4sIGNzcywgaW5jbHVkZXMsIGlzVG91Y2gsIG1hdGNoZXMsIE1vdXNlVHJhY2tlciwgb2Zmc2V0LCBvbiwgb25jZSwgcG9pbnRlckVudGVyLCBwb2ludGVyTGVhdmUsIHF1ZXJ5LCByZW1vdmVDbGFzc2VzLCB0b2dnbGVDbGFzcywgdHJpZ2dlciwgd2l0aGlufSBmcm9tICd1aWtpdC11dGlsJztcblxubGV0IGFjdGl2ZTtcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbUG9zaXRpb24sIFRvZ2dsYWJsZV0sXG5cbiAgICBhcmdzOiAncG9zJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1vZGU6ICdsaXN0JyxcbiAgICAgICAgdG9nZ2xlOiBCb29sZWFuLFxuICAgICAgICBib3VuZGFyeTogQm9vbGVhbixcbiAgICAgICAgYm91bmRhcnlBbGlnbjogQm9vbGVhbixcbiAgICAgICAgZGVsYXlTaG93OiBOdW1iZXIsXG4gICAgICAgIGRlbGF5SGlkZTogTnVtYmVyLFxuICAgICAgICBjbHNEcm9wOiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtb2RlOiBbJ2NsaWNrJywgJ2hvdmVyJ10sXG4gICAgICAgIHRvZ2dsZTogJy0gKicsXG4gICAgICAgIGJvdW5kYXJ5OiB3aW5kb3csXG4gICAgICAgIGJvdW5kYXJ5QWxpZ246IGZhbHNlLFxuICAgICAgICBkZWxheVNob3c6IDAsXG4gICAgICAgIGRlbGF5SGlkZTogODAwLFxuICAgICAgICBjbHNEcm9wOiBmYWxzZSxcbiAgICAgICAgYW5pbWF0aW9uOiBbJ3VrLWFuaW1hdGlvbi1mYWRlJ10sXG4gICAgICAgIGNsczogJ3VrLW9wZW4nXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgYm91bmRhcnkoe2JvdW5kYXJ5fSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gcXVlcnkoYm91bmRhcnksICRlbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgY2xzRHJvcCh7Y2xzRHJvcH0pIHtcbiAgICAgICAgICAgIHJldHVybiBjbHNEcm9wIHx8IGB1ay0ke3RoaXMuJG9wdGlvbnMubmFtZX1gO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc1BvcygpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmNsc0Ryb3A7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjcmVhdGVkKCkge1xuICAgICAgICB0aGlzLnRyYWNrZXIgPSBuZXcgTW91c2VUcmFja2VyKCk7XG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcblxuICAgICAgICBhZGRDbGFzcyh0aGlzLiRlbCwgdGhpcy5jbHNEcm9wKTtcblxuICAgICAgICBjb25zdCB7dG9nZ2xlfSA9IHRoaXMuJHByb3BzO1xuICAgICAgICB0aGlzLnRvZ2dsZSA9IHRvZ2dsZSAmJiB0aGlzLiRjcmVhdGUoJ3RvZ2dsZScsIHF1ZXJ5KHRvZ2dsZSwgdGhpcy4kZWwpLCB7XG4gICAgICAgICAgICB0YXJnZXQ6IHRoaXMuJGVsLFxuICAgICAgICAgICAgbW9kZTogdGhpcy5tb2RlXG4gICAgICAgIH0pO1xuXG4gICAgICAgICF0aGlzLnRvZ2dsZSAmJiB0cmlnZ2VyKHRoaXMuJGVsLCAndXBkYXRlYXJpYScpO1xuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gYC4ke3RoaXMuY2xzRHJvcH0tY2xvc2VgO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdjbGljaycsXG5cbiAgICAgICAgICAgIGRlbGVnYXRlKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiAnYVtocmVmXj1cIiNcIl0nO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcih7ZGVmYXVsdFByZXZlbnRlZCwgY3VycmVudDoge2hhc2h9fSkge1xuICAgICAgICAgICAgICAgIGlmICghZGVmYXVsdFByZXZlbnRlZCAmJiBoYXNoICYmICF3aXRoaW4oaGFzaCwgdGhpcy4kZWwpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnYmVmb3Jlc2Nyb2xsJyxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAndG9nZ2xlJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcihlLCB0b2dnbGUpIHtcblxuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmlzVG9nZ2xlZCgpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZShmYWxzZSk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93KHRvZ2dsZSwgZmFsc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3RvZ2dsZXNob3cnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUsIHRvZ2dsZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3codG9nZ2xlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3RvZ2dsZWhpZGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6IHBvaW50ZXJFbnRlcixcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBpbmNsdWRlcyh0aGlzLm1vZGUsICdob3ZlcicpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFpc1RvdWNoKGUpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6IHBvaW50ZXJMZWF2ZSxcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBpbmNsdWRlcyh0aGlzLm1vZGUsICdob3ZlcicpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFpc1RvdWNoKGUpICYmICFtYXRjaGVzKHRoaXMuJGVsLCAnOmhvdmVyJykpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnYmVmb3Jlc2hvdycsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5jbGVhclRpbWVycygpO1xuICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5jYW5jZWwodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgIHRoaXMucG9zaXRpb24oKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3Nob3cnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgYWN0aXZlID0gdGhpcztcblxuICAgICAgICAgICAgICAgIHRoaXMudHJhY2tlci5pbml0KCk7XG4gICAgICAgICAgICAgICAgdHJpZ2dlcih0aGlzLiRlbCwgJ3VwZGF0ZWFyaWEnKTtcblxuICAgICAgICAgICAgICAgIC8vIElmIHRyaWdnZXJlZCBmcm9tIGFuIGNsaWNrIGV2ZW50IGhhbmRsZXIsIGRlbGF5IGFkZGluZyB0aGUgY2xpY2sgaGFuZGxlclxuICAgICAgICAgICAgICAgIGNvbnN0IG9mZiA9IGRlbGF5T24oZG9jdW1lbnQsICdjbGljaycsICh7ZGVmYXVsdFByZXZlbnRlZCwgdGFyZ2V0fSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoIWRlZmF1bHRQcmV2ZW50ZWQgJiYgIXdpdGhpbih0YXJnZXQsIHRoaXMuJGVsKSAmJiAhKHRoaXMudG9nZ2xlICYmIHdpdGhpbih0YXJnZXQsIHRoaXMudG9nZ2xlLiRlbCkpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBvbmNlKHRoaXMuJGVsLCAnaGlkZScsIG9mZiwge3NlbGY6IHRydWV9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2JlZm9yZWhpZGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuICAgICAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2hpZGUnLFxuXG4gICAgICAgICAgICBoYW5kbGVyKHt0YXJnZXR9KSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy4kZWwgIT09IHRhcmdldCkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSBhY3RpdmUgPT09IG51bGwgJiYgd2l0aGluKHRhcmdldCwgdGhpcy4kZWwpICYmIHRoaXMuaXNUb2dnbGVkKCkgPyB0aGlzIDogYWN0aXZlO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYWN0aXZlID0gdGhpcy5pc0FjdGl2ZSgpID8gbnVsbCA6IGFjdGl2ZTtcbiAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAndXBkYXRlYXJpYScpO1xuICAgICAgICAgICAgICAgIHRoaXMudHJhY2tlci5jYW5jZWwoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3VwZGF0ZWFyaWEnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUsIHRvZ2dsZSkge1xuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgdGhpcy51cGRhdGVBcmlhKHRoaXMuJGVsKTtcblxuICAgICAgICAgICAgICAgIGlmICh0b2dnbGUgfHwgdGhpcy50b2dnbGUpIHtcbiAgICAgICAgICAgICAgICAgICAgYXR0cigodG9nZ2xlIHx8IHRoaXMudG9nZ2xlKS4kZWwsICdhcmlhLWV4cGFuZGVkJywgdGhpcy5pc1RvZ2dsZWQoKSk7XG4gICAgICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKHRoaXMudG9nZ2xlLiRlbCwgdGhpcy5jbHMsIHRoaXMuaXNUb2dnbGVkKCkpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgXSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHdyaXRlKCkge1xuXG4gICAgICAgICAgICBpZiAodGhpcy5pc1RvZ2dsZWQoKSAmJiAhQW5pbWF0aW9uLmluUHJvZ3Jlc3ModGhpcy4kZWwpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5wb3NpdGlvbigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9LFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIHNob3codG9nZ2xlID0gdGhpcy50b2dnbGUsIGRlbGF5ID0gdHJ1ZSkge1xuXG4gICAgICAgICAgICBpZiAodGhpcy5pc1RvZ2dsZWQoKSAmJiB0b2dnbGUgJiYgdGhpcy50b2dnbGUgJiYgdG9nZ2xlLiRlbCAhPT0gdGhpcy50b2dnbGUuJGVsKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlKGZhbHNlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy50b2dnbGUgPSB0b2dnbGU7XG5cbiAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcblxuICAgICAgICAgICAgaWYgKHRoaXMuaXNBY3RpdmUoKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGFjdGl2ZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGRlbGF5ICYmIGFjdGl2ZS5pc0RlbGF5aW5nKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvd1RpbWVyID0gc2V0VGltZW91dCh0aGlzLnNob3csIDEwKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHdoaWxlIChhY3RpdmUgJiYgIXdpdGhpbih0aGlzLiRlbCwgYWN0aXZlLiRlbCkpIHtcbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5zaG93VGltZXIgPSBzZXRUaW1lb3V0KCgpID0+ICF0aGlzLmlzVG9nZ2xlZCgpICYmIHRoaXMudG9nZ2xlRWxlbWVudCh0aGlzLiRlbCwgdHJ1ZSksIGRlbGF5ICYmIHRoaXMuZGVsYXlTaG93IHx8IDApO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgaGlkZShkZWxheSA9IHRydWUpIHtcblxuICAgICAgICAgICAgY29uc3QgaGlkZSA9ICgpID0+IHRoaXMudG9nZ2xlTm93KHRoaXMuJGVsLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHRoaXMuY2xlYXJUaW1lcnMoKTtcblxuICAgICAgICAgICAgdGhpcy5pc0RlbGF5aW5nID0gZ2V0UG9zaXRpb25lZEVsZW1lbnRzKHRoaXMuJGVsKS5zb21lKGVsID0+IHRoaXMudHJhY2tlci5tb3Zlc1RvKGVsKSk7XG5cbiAgICAgICAgICAgIGlmIChkZWxheSAmJiB0aGlzLmlzRGVsYXlpbmcpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmhpZGVUaW1lciA9IHNldFRpbWVvdXQodGhpcy5oaWRlLCA1MCk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGRlbGF5ICYmIHRoaXMuZGVsYXlIaWRlKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5oaWRlVGltZXIgPSBzZXRUaW1lb3V0KGhpZGUsIHRoaXMuZGVsYXlIaWRlKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaGlkZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIGNsZWFyVGltZXJzKCkge1xuICAgICAgICAgICAgY2xlYXJUaW1lb3V0KHRoaXMuc2hvd1RpbWVyKTtcbiAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aGlzLmhpZGVUaW1lcik7XG4gICAgICAgICAgICB0aGlzLnNob3dUaW1lciA9IG51bGw7XG4gICAgICAgICAgICB0aGlzLmhpZGVUaW1lciA9IG51bGw7XG4gICAgICAgICAgICB0aGlzLmlzRGVsYXlpbmcgPSBmYWxzZTtcbiAgICAgICAgfSxcblxuICAgICAgICBpc0FjdGl2ZSgpIHtcbiAgICAgICAgICAgIHJldHVybiBhY3RpdmUgPT09IHRoaXM7XG4gICAgICAgIH0sXG5cbiAgICAgICAgcG9zaXRpb24oKSB7XG5cbiAgICAgICAgICAgIHJlbW92ZUNsYXNzZXModGhpcy4kZWwsIGAke3RoaXMuY2xzRHJvcH0tKHN0YWNrfGJvdW5kYXJ5KWApO1xuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCB7dG9wOiAnJywgbGVmdDogJycsIGRpc3BsYXk6ICdibG9jayd9KTtcbiAgICAgICAgICAgIHRvZ2dsZUNsYXNzKHRoaXMuJGVsLCBgJHt0aGlzLmNsc0Ryb3B9LWJvdW5kYXJ5YCwgdGhpcy5ib3VuZGFyeUFsaWduKTtcblxuICAgICAgICAgICAgY29uc3QgYm91bmRhcnkgPSBvZmZzZXQodGhpcy5ib3VuZGFyeSk7XG4gICAgICAgICAgICBjb25zdCBhbGlnblRvID0gdGhpcy5ib3VuZGFyeUFsaWduID8gYm91bmRhcnkgOiBvZmZzZXQodGhpcy50b2dnbGUuJGVsKTtcblxuICAgICAgICAgICAgaWYgKHRoaXMuYWxpZ24gPT09ICdqdXN0aWZ5Jykge1xuICAgICAgICAgICAgICAgIGNvbnN0IHByb3AgPSB0aGlzLmdldEF4aXMoKSA9PT0gJ3knID8gJ3dpZHRoJyA6ICdoZWlnaHQnO1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgcHJvcCwgYWxpZ25Ub1twcm9wXSk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKHRoaXMuJGVsLm9mZnNldFdpZHRoID4gTWF0aC5tYXgoYm91bmRhcnkucmlnaHQgLSBhbGlnblRvLmxlZnQsIGFsaWduVG8ucmlnaHQgLSBib3VuZGFyeS5sZWZ0KSkge1xuICAgICAgICAgICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCBgJHt0aGlzLmNsc0Ryb3B9LXN0YWNrYCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMucG9zaXRpb25BdCh0aGlzLiRlbCwgdGhpcy5ib3VuZGFyeUFsaWduID8gdGhpcy5ib3VuZGFyeSA6IHRoaXMudG9nZ2xlLiRlbCwgdGhpcy5ib3VuZGFyeSk7XG5cbiAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ2Rpc3BsYXknLCAnJyk7XG5cbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiBnZXRQb3NpdGlvbmVkRWxlbWVudHMoZWwpIHtcbiAgICBjb25zdCByZXN1bHQgPSBjc3MoZWwsICdwb3NpdGlvbicpICE9PSAnc3RhdGljJyA/IFtlbF0gOiBbXTtcbiAgICByZXR1cm4gcmVzdWx0LmNvbmNhdC5hcHBseShyZXN1bHQsIGNoaWxkcmVuKGVsKS5tYXAoZ2V0UG9zaXRpb25lZEVsZW1lbnRzKSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBkZWxheU9uKGVsLCB0eXBlLCBmbikge1xuICAgIGxldCBvZmYgPSBvbmNlKGVsLCB0eXBlLCAoKSA9PlxuICAgICAgICBvZmYgPSBvbihlbCwgdHlwZSwgZm4pXG4gICAgLCB0cnVlKTtcbiAgICByZXR1cm4gKCkgPT4gb2ZmKCk7XG59XG4iLCJpbXBvcnQgRHJvcCBmcm9tICcuL2Ryb3AnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBleHRlbmRzOiBEcm9wXG5cbn07XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IHskLCAkJCwgY2xvc2VzdCwgaXNJbnB1dCwgbWF0Y2hlcywgcXVlcnksIHNlbElucHV0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3NdLFxuXG4gICAgYXJnczogJ3RhcmdldCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICB0YXJnZXQ6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICB0YXJnZXQ6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgaW5wdXQoXywgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJChzZWxJbnB1dCwgJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICBzdGF0ZSgpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmlucHV0Lm5leHRFbGVtZW50U2libGluZztcbiAgICAgICAgfSxcblxuICAgICAgICB0YXJnZXQoe3RhcmdldH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHRhcmdldCAmJiAodGFyZ2V0ID09PSB0cnVlXG4gICAgICAgICAgICAgICAgJiYgdGhpcy5pbnB1dC5wYXJlbnROb2RlID09PSAkZWxcbiAgICAgICAgICAgICAgICAmJiB0aGlzLmlucHV0Lm5leHRFbGVtZW50U2libGluZ1xuICAgICAgICAgICAgICAgIHx8IHF1ZXJ5KHRhcmdldCwgJGVsKSk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICB1cGRhdGUoKSB7XG5cbiAgICAgICAgY29uc3Qge3RhcmdldCwgaW5wdXR9ID0gdGhpcztcblxuICAgICAgICBpZiAoIXRhcmdldCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IG9wdGlvbjtcbiAgICAgICAgY29uc3QgcHJvcCA9IGlzSW5wdXQodGFyZ2V0KSA/ICd2YWx1ZScgOiAndGV4dENvbnRlbnQnO1xuICAgICAgICBjb25zdCBwcmV2ID0gdGFyZ2V0W3Byb3BdO1xuICAgICAgICBjb25zdCB2YWx1ZSA9IGlucHV0LmZpbGVzICYmIGlucHV0LmZpbGVzWzBdXG4gICAgICAgICAgICA/IGlucHV0LmZpbGVzWzBdLm5hbWVcbiAgICAgICAgICAgIDogbWF0Y2hlcyhpbnB1dCwgJ3NlbGVjdCcpICYmIChvcHRpb24gPSAkJCgnb3B0aW9uJywgaW5wdXQpLmZpbHRlcihlbCA9PiBlbC5zZWxlY3RlZClbMF0pIC8vIGVzbGludC1kaXNhYmxlLWxpbmUgcHJlZmVyLWRlc3RydWN0dXJpbmdcbiAgICAgICAgICAgICAgICA/IG9wdGlvbi50ZXh0Q29udGVudFxuICAgICAgICAgICAgICAgIDogaW5wdXQudmFsdWU7XG5cbiAgICAgICAgaWYgKHByZXYgIT09IHZhbHVlKSB7XG4gICAgICAgICAgICB0YXJnZXRbcHJvcF0gPSB2YWx1ZTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdjaGFuZ2UnLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuICAgICAgICAgICAgICAgIHRoaXMuJGVtaXQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAncmVzZXQnLFxuXG4gICAgICAgICAgICBlbCgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gY2xvc2VzdCh0aGlzLiRlbCwgJ2Zvcm0nKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kZW1pdCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdXG5cbn07XG4iLCJpbXBvcnQge2lzSW5WaWV3fSBmcm9tICd1aWtpdC11dGlsJztcblxuLy8gRGVwcmVjYXRlZFxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgdXBkYXRlOiB7XG5cbiAgICAgICAgcmVhZChkYXRhKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGludmlldyA9IGlzSW5WaWV3KHRoaXMuJGVsKTtcblxuICAgICAgICAgICAgaWYgKCFpbnZpZXcgfHwgZGF0YS5pc0luVmlldyA9PT0gaW52aWV3KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkYXRhLmlzSW5WaWV3ID0gaW52aWV3O1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKCkge1xuICAgICAgICAgICAgdGhpcy4kZWwuc3JjID0gdGhpcy4kZWwuc3JjO1xuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydzY3JvbGwnLCAncmVzaXplJ11cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQge2lzUnRsLCBpc1Zpc2libGUsIG9mZnNldFBvc2l0aW9uLCB0b2dnbGVDbGFzc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1hcmdpbjogU3RyaW5nLFxuICAgICAgICBmaXJzdENvbHVtbjogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIG1hcmdpbjogJ3VrLW1hcmdpbi1zbWFsbC10b3AnLFxuICAgICAgICBmaXJzdENvbHVtbjogJ3VrLWZpcnN0LWNvbHVtbidcbiAgICB9LFxuXG4gICAgdXBkYXRlOiB7XG5cbiAgICAgICAgcmVhZChkYXRhKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IGl0ZW1zID0gdGhpcy4kZWwuY2hpbGRyZW47XG4gICAgICAgICAgICBjb25zdCByb3dzID0gW1tdXTtcblxuICAgICAgICAgICAgaWYgKCFpdGVtcy5sZW5ndGggfHwgIWlzVmlzaWJsZSh0aGlzLiRlbCkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZGF0YS5yb3dzID0gcm93cztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZGF0YS5yb3dzID0gZ2V0Um93cyhpdGVtcyk7XG4gICAgICAgICAgICBkYXRhLnN0YWNrcyA9ICFkYXRhLnJvd3Muc29tZShyb3cgPT4gcm93Lmxlbmd0aCA+IDEpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoe3Jvd3N9KSB7XG5cbiAgICAgICAgICAgIHJvd3MuZm9yRWFjaCgocm93LCBpKSA9PlxuICAgICAgICAgICAgICAgIHJvdy5mb3JFYWNoKChlbCwgaikgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyhlbCwgdGhpcy5tYXJnaW4sIGkgIT09IDApO1xuICAgICAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyhlbCwgdGhpcy5maXJzdENvbHVtbiwgaiA9PT0gMCk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgIH1cblxufTtcblxuZXhwb3J0IGZ1bmN0aW9uIGdldFJvd3MoaXRlbXMpIHtcbiAgICBjb25zdCByb3dzID0gW1tdXTtcblxuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgaXRlbXMubGVuZ3RoOyBpKyspIHtcblxuICAgICAgICBjb25zdCBlbCA9IGl0ZW1zW2ldO1xuICAgICAgICBsZXQgZGltID0gZ2V0T2Zmc2V0KGVsKTtcblxuICAgICAgICBpZiAoIWRpbS5oZWlnaHQpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChsZXQgaiA9IHJvd3MubGVuZ3RoIC0gMTsgaiA+PSAwOyBqLS0pIHtcblxuICAgICAgICAgICAgY29uc3Qgcm93ID0gcm93c1tqXTtcblxuICAgICAgICAgICAgaWYgKCFyb3dbMF0pIHtcbiAgICAgICAgICAgICAgICByb3cucHVzaChlbCk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGxldCBsZWZ0RGltO1xuICAgICAgICAgICAgaWYgKHJvd1swXS5vZmZzZXRQYXJlbnQgPT09IGVsLm9mZnNldFBhcmVudCkge1xuICAgICAgICAgICAgICAgIGxlZnREaW0gPSBnZXRPZmZzZXQocm93WzBdKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgZGltID0gZ2V0T2Zmc2V0KGVsLCB0cnVlKTtcbiAgICAgICAgICAgICAgICBsZWZ0RGltID0gZ2V0T2Zmc2V0KHJvd1swXSwgdHJ1ZSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChkaW0udG9wID49IGxlZnREaW0uYm90dG9tIC0gMSAmJiBkaW0udG9wICE9PSBsZWZ0RGltLnRvcCkge1xuICAgICAgICAgICAgICAgIHJvd3MucHVzaChbZWxdKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGRpbS5ib3R0b20gPiBsZWZ0RGltLnRvcCkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGRpbS5sZWZ0IDwgbGVmdERpbS5sZWZ0ICYmICFpc1J0bCkge1xuICAgICAgICAgICAgICAgICAgICByb3cudW5zaGlmdChlbCk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJvdy5wdXNoKGVsKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGogPT09IDApIHtcbiAgICAgICAgICAgICAgICByb3dzLnVuc2hpZnQoW2VsXSk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfVxuXG4gICAgcmV0dXJuIHJvd3M7XG5cbn1cblxuZnVuY3Rpb24gZ2V0T2Zmc2V0KGVsZW1lbnQsIG9mZnNldCA9IGZhbHNlKSB7XG5cbiAgICBsZXQge29mZnNldFRvcCwgb2Zmc2V0TGVmdCwgb2Zmc2V0SGVpZ2h0fSA9IGVsZW1lbnQ7XG5cbiAgICBpZiAob2Zmc2V0KSB7XG4gICAgICAgIFtvZmZzZXRUb3AsIG9mZnNldExlZnRdID0gb2Zmc2V0UG9zaXRpb24oZWxlbWVudCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHtcbiAgICAgICAgdG9wOiBvZmZzZXRUb3AsXG4gICAgICAgIGxlZnQ6IG9mZnNldExlZnQsXG4gICAgICAgIGhlaWdodDogb2Zmc2V0SGVpZ2h0LFxuICAgICAgICBib3R0b206IG9mZnNldFRvcCArIG9mZnNldEhlaWdodFxuICAgIH07XG59XG4iLCJpbXBvcnQgTWFyZ2luIGZyb20gJy4vbWFyZ2luJztcbmltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQge2FkZENsYXNzLCBjaGlsZHJlbiwgY3NzLCBoYXNDbGFzcywgaGVpZ2h0IGFzIGdldEhlaWdodCwgaXNSdGwsIHNjcm9sbGVkT3ZlciwgdG9GbG9hdCwgdG9nZ2xlQ2xhc3MsIFRyYW5zaXRpb24sIHNvcnRCeX0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGV4dGVuZHM6IE1hcmdpbixcblxuICAgIG1peGluczogW0NsYXNzXSxcblxuICAgIG5hbWU6ICdncmlkJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1hc29ucnk6IEJvb2xlYW4sXG4gICAgICAgIHBhcmFsbGF4OiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtYXJnaW46ICd1ay1ncmlkLW1hcmdpbicsXG4gICAgICAgIGNsc1N0YWNrOiAndWstZ3JpZC1zdGFjaycsXG4gICAgICAgIG1hc29ucnk6IGZhbHNlLFxuICAgICAgICBwYXJhbGxheDogMFxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGxlbmd0aChfLCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiAkZWwuY2hpbGRyZW4ubGVuZ3RoO1xuICAgICAgICB9LFxuXG4gICAgICAgIHBhcmFsbGF4KHtwYXJhbGxheH0pIHtcbiAgICAgICAgICAgIHJldHVybiBwYXJhbGxheCAmJiB0aGlzLmxlbmd0aCA/IE1hdGguYWJzKHBhcmFsbGF4KSA6ICcnO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgY29ubmVjdGVkKCkge1xuICAgICAgICB0aGlzLm1hc29ucnkgJiYgYWRkQ2xhc3ModGhpcy4kZWwsICd1ay1mbGV4LXRvcCB1ay1mbGV4LXdyYXAtdG9wJyk7XG4gICAgfSxcblxuICAgIHVwZGF0ZTogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgd3JpdGUoe3N0YWNrc30pIHtcbiAgICAgICAgICAgICAgICB0b2dnbGVDbGFzcyh0aGlzLiRlbCwgdGhpcy5jbHNTdGFjaywgc3RhY2tzKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKHtyb3dzfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMubWFzb25yeSB8fCB0aGlzLnBhcmFsbGF4KSB7XG4gICAgICAgICAgICAgICAgICAgIHJvd3MgPSByb3dzLm1hcChlbGVtZW50cyA9PiBzb3J0QnkoZWxlbWVudHMsICdvZmZzZXRMZWZ0JykpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChpc1J0bCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcm93cy5tYXAocm93ID0+IHJvdy5yZXZlcnNlKCkpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY29uc3QgdHJhbnNpdGlvbkluUHJvZ3Jlc3MgPSByb3dzLnNvbWUoZWxlbWVudHMgPT4gZWxlbWVudHMuc29tZShUcmFuc2l0aW9uLmluUHJvZ3Jlc3MpKTtcbiAgICAgICAgICAgICAgICBsZXQgdHJhbnNsYXRlcyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIGxldCBlbEhlaWdodCA9ICcnO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMubWFzb25yeSAmJiB0aGlzLmxlbmd0aCkge1xuXG4gICAgICAgICAgICAgICAgICAgIGxldCBoZWlnaHQgPSAwO1xuXG4gICAgICAgICAgICAgICAgICAgIHRyYW5zbGF0ZXMgPSByb3dzLnJlZHVjZSgodHJhbnNsYXRlcywgcm93LCBpKSA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRyYW5zbGF0ZXNbaV0gPSByb3cubWFwKChfLCBqKSA9PiBpID09PSAwID8gMCA6IHRvRmxvYXQodHJhbnNsYXRlc1tpIC0gMV1bal0pICsgKGhlaWdodCAtIHRvRmxvYXQocm93c1tpIC0gMV1bal0gJiYgcm93c1tpIC0gMV1bal0ub2Zmc2V0SGVpZ2h0KSkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0ID0gcm93LnJlZHVjZSgoaGVpZ2h0LCBlbCkgPT4gTWF0aC5tYXgoaGVpZ2h0LCBlbC5vZmZzZXRIZWlnaHQpLCAwKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRyYW5zbGF0ZXM7XG5cbiAgICAgICAgICAgICAgICAgICAgfSwgW10pO1xuXG4gICAgICAgICAgICAgICAgICAgIGVsSGVpZ2h0ID0gbWF4Q29sdW1uSGVpZ2h0KHJvd3MpICsgZ2V0TWFyZ2luVG9wKHRoaXMuJGVsLCB0aGlzLm1hcmdpbikgKiAocm93cy5sZW5ndGggLSAxKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNvbnN0IHBhZGRpbmcgPSB0aGlzLnBhcmFsbGF4ICYmIGdldFBhZGRpbmdCb3R0b20odGhpcy5wYXJhbGxheCwgcm93cywgdHJhbnNsYXRlcyk7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4ge3BhZGRpbmcsIHJvd3MsIHRyYW5zbGF0ZXMsIGhlaWdodDogIXRyYW5zaXRpb25JblByb2dyZXNzID8gZWxIZWlnaHQgOiBmYWxzZX07XG5cbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIHdyaXRlKHtzdGFja3MsIGhlaWdodCwgcGFkZGluZ30pIHtcblxuICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKHRoaXMuJGVsLCB0aGlzLmNsc1N0YWNrLCBzdGFja3MpO1xuXG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAncGFkZGluZ0JvdHRvbScsIHBhZGRpbmcpO1xuICAgICAgICAgICAgICAgIGhlaWdodCAhPT0gZmFsc2UgJiYgY3NzKHRoaXMuJGVsLCAnaGVpZ2h0JywgaGVpZ2h0KTtcblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIHJlYWQoe2hlaWdodH0pIHtcbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBzY3JvbGxlZDogdGhpcy5wYXJhbGxheFxuICAgICAgICAgICAgICAgICAgICAgICAgPyBzY3JvbGxlZE92ZXIodGhpcy4kZWwsIGhlaWdodCA/IGhlaWdodCAtIGdldEhlaWdodCh0aGlzLiRlbCkgOiAwKSAqIHRoaXMucGFyYWxsYXhcbiAgICAgICAgICAgICAgICAgICAgICAgIDogZmFsc2VcbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgd3JpdGUoe3Jvd3MsIHNjcm9sbGVkLCB0cmFuc2xhdGVzfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHNjcm9sbGVkID09PSBmYWxzZSAmJiAhdHJhbnNsYXRlcykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcm93cy5mb3JFYWNoKChyb3csIGkpID0+XG4gICAgICAgICAgICAgICAgICAgIHJvdy5mb3JFYWNoKChlbCwgaikgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNzcyhlbCwgJ3RyYW5zZm9ybScsICFzY3JvbGxlZCAmJiAhdHJhbnNsYXRlcyA/ICcnIDogYHRyYW5zbGF0ZVkoJHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAodHJhbnNsYXRlcyAmJiAtdHJhbnNsYXRlc1tpXVtqXSkgKyAoc2Nyb2xsZWQgPyBqICUgMiA/IHNjcm9sbGVkIDogc2Nyb2xsZWQgLyA4IDogMClcbiAgICAgICAgICAgICAgICAgICAgICAgIH1weClgKVxuICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgKTtcblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Njcm9sbCcsICdyZXNpemUnXVxuXG4gICAgICAgIH1cblxuICAgIF1cblxufTtcblxuZnVuY3Rpb24gZ2V0UGFkZGluZ0JvdHRvbShkaXN0YW5jZSwgcm93cywgdHJhbnNsYXRlcykge1xuICAgIGxldCBjb2x1bW4gPSAwO1xuICAgIGxldCBtYXggPSAwO1xuICAgIGxldCBtYXhTY3JvbGxlZCA9IDA7XG4gICAgZm9yIChsZXQgaSA9IHJvd3MubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgZm9yIChsZXQgaiA9IGNvbHVtbjsgaiA8IHJvd3NbaV0ubGVuZ3RoOyBqKyspIHtcbiAgICAgICAgICAgIGNvbnN0IGVsID0gcm93c1tpXVtqXTtcbiAgICAgICAgICAgIGNvbnN0IGJvdHRvbSA9IGVsLm9mZnNldFRvcCArIGdldEhlaWdodChlbCkgKyAodHJhbnNsYXRlcyAmJiAtdHJhbnNsYXRlc1tpXVtqXSk7XG4gICAgICAgICAgICBtYXggPSBNYXRoLm1heChtYXgsIGJvdHRvbSk7XG4gICAgICAgICAgICBtYXhTY3JvbGxlZCA9IE1hdGgubWF4KG1heFNjcm9sbGVkLCBib3R0b20gKyAoaiAlIDIgPyBkaXN0YW5jZSA6IGRpc3RhbmNlIC8gOCkpO1xuICAgICAgICAgICAgY29sdW1uKys7XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIG1heFNjcm9sbGVkIC0gbWF4O1xufVxuXG5mdW5jdGlvbiBnZXRNYXJnaW5Ub3Aocm9vdCwgY2xzKSB7XG5cbiAgICBjb25zdCBub2RlcyA9IGNoaWxkcmVuKHJvb3QpO1xuICAgIGNvbnN0IFtub2RlXSA9IG5vZGVzLmZpbHRlcihlbCA9PiBoYXNDbGFzcyhlbCwgY2xzKSk7XG5cbiAgICByZXR1cm4gdG9GbG9hdChub2RlXG4gICAgICAgID8gY3NzKG5vZGUsICdtYXJnaW5Ub3AnKVxuICAgICAgICA6IGNzcyhub2Rlc1swXSwgJ3BhZGRpbmdMZWZ0JykpO1xufVxuXG5mdW5jdGlvbiBtYXhDb2x1bW5IZWlnaHQocm93cykge1xuICAgIHJldHVybiBNYXRoLm1heCguLi5yb3dzLnJlZHVjZSgoc3VtLCByb3cpID0+IHtcbiAgICAgICAgcm93LmZvckVhY2goKGVsLCBpKSA9PiBzdW1baV0gPSAoc3VtW2ldIHx8IDApICsgZWwub2Zmc2V0SGVpZ2h0KTtcbiAgICAgICAgcmV0dXJuIHN1bTtcbiAgICB9LCBbXSkpO1xufVxuIiwiaW1wb3J0IHskJCwgYm94TW9kZWxBZGp1c3QsIGNzcywgaXNJRSwgdG9GbG9hdH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbi8vIElFIDExIGZpeCAobWluLWhlaWdodCBvbiBhIGZsZXggY29udGFpbmVyIHdvbid0IGFwcGx5IHRvIGl0cyBmbGV4IGl0ZW1zKVxuZXhwb3J0IGRlZmF1bHQgaXNJRSA/IHtcblxuICAgIHByb3BzOiB7XG4gICAgICAgIHNlbE1pbkhlaWdodDogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgc2VsTWluSGVpZ2h0OiBmYWxzZSxcbiAgICAgICAgZm9yY2VIZWlnaHQ6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgZWxlbWVudHMoe3NlbE1pbkhlaWdodH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHNlbE1pbkhlaWdodCA/ICQkKHNlbE1pbkhlaWdodCwgJGVsKSA6IFskZWxdO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLmVsZW1lbnRzLCAnaGVpZ2h0JywgJycpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgb3JkZXI6IC01LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgd3JpdGUoKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5lbGVtZW50cy5mb3JFYWNoKGVsID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgaGVpZ2h0ID0gdG9GbG9hdChjc3MoZWwsICdtaW5IZWlnaHQnKSk7XG4gICAgICAgICAgICAgICAgICAgIGlmIChoZWlnaHQgJiYgKHRoaXMuZm9yY2VIZWlnaHQgfHwgTWF0aC5yb3VuZChoZWlnaHQgKyBib3hNb2RlbEFkanVzdChlbCwgJ2hlaWdodCcsICdjb250ZW50LWJveCcpKSA+PSBlbC5vZmZzZXRIZWlnaHQpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjc3MoZWwsICdoZWlnaHQnLCBoZWlnaHQpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBvcmRlcjogNSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICAgICAgfVxuXG4gICAgXVxuXG59IDoge307XG4iLCJpbXBvcnQgRmxleEJ1ZyBmcm9tICcuLi9taXhpbi9mbGV4LWJ1Zyc7XG5pbXBvcnQge2dldFJvd3N9IGZyb20gJy4vbWFyZ2luJztcbmltcG9ydCB7JCQsIGJveE1vZGVsQWRqdXN0LCBjc3MsIG9mZnNldCwgdG9GbG9hdH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0ZsZXhCdWddLFxuXG4gICAgYXJnczogJ3RhcmdldCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICB0YXJnZXQ6IFN0cmluZyxcbiAgICAgICAgcm93OiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdGFyZ2V0OiAnPiAqJyxcbiAgICAgICAgcm93OiB0cnVlLFxuICAgICAgICBmb3JjZUhlaWdodDogdHJ1ZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGVsZW1lbnRzKHt0YXJnZXR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiAkJCh0YXJnZXQsICRlbCk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICByb3dzOiAodGhpcy5yb3cgPyBnZXRSb3dzKHRoaXMuZWxlbWVudHMpIDogW3RoaXMuZWxlbWVudHNdKS5tYXAobWF0Y2gpXG4gICAgICAgICAgICB9O1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKHtyb3dzfSkge1xuICAgICAgICAgICAgcm93cy5mb3JFYWNoKCh7aGVpZ2h0cywgZWxlbWVudHN9KSA9PlxuICAgICAgICAgICAgICAgIGVsZW1lbnRzLmZvckVhY2goKGVsLCBpKSA9PlxuICAgICAgICAgICAgICAgICAgICBjc3MoZWwsICdtaW5IZWlnaHQnLCBoZWlnaHRzW2ldKVxuICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZXZlbnRzOiBbJ3Jlc2l6ZSddXG5cbiAgICB9XG5cbn07XG5cbmZ1bmN0aW9uIG1hdGNoKGVsZW1lbnRzKSB7XG5cbiAgICBpZiAoZWxlbWVudHMubGVuZ3RoIDwgMikge1xuICAgICAgICByZXR1cm4ge2hlaWdodHM6IFsnJ10sIGVsZW1lbnRzfTtcbiAgICB9XG5cbiAgICBsZXQge2hlaWdodHMsIG1heH0gPSBnZXRIZWlnaHRzKGVsZW1lbnRzKTtcbiAgICBjb25zdCBoYXNNaW5IZWlnaHQgPSBlbGVtZW50cy5zb21lKGVsID0+IGVsLnN0eWxlLm1pbkhlaWdodCk7XG4gICAgY29uc3QgaGFzU2hydW5rID0gZWxlbWVudHMuc29tZSgoZWwsIGkpID0+ICFlbC5zdHlsZS5taW5IZWlnaHQgJiYgaGVpZ2h0c1tpXSA8IG1heCk7XG5cbiAgICBpZiAoaGFzTWluSGVpZ2h0ICYmIGhhc1NocnVuaykge1xuICAgICAgICBjc3MoZWxlbWVudHMsICdtaW5IZWlnaHQnLCAnJyk7XG4gICAgICAgICh7aGVpZ2h0cywgbWF4fSA9IGdldEhlaWdodHMoZWxlbWVudHMpKTtcbiAgICB9XG5cbiAgICBoZWlnaHRzID0gZWxlbWVudHMubWFwKChlbCwgaSkgPT5cbiAgICAgICAgaGVpZ2h0c1tpXSA9PT0gbWF4ICYmIHRvRmxvYXQoZWwuc3R5bGUubWluSGVpZ2h0KS50b0ZpeGVkKDIpICE9PSBtYXgudG9GaXhlZCgyKSA/ICcnIDogbWF4XG4gICAgKTtcblxuICAgIHJldHVybiB7aGVpZ2h0cywgZWxlbWVudHN9O1xufVxuXG5mdW5jdGlvbiBnZXRIZWlnaHRzKGVsZW1lbnRzKSB7XG4gICAgY29uc3QgaGVpZ2h0cyA9IGVsZW1lbnRzLm1hcChlbCA9PiBvZmZzZXQoZWwpLmhlaWdodCAtIGJveE1vZGVsQWRqdXN0KGVsLCAnaGVpZ2h0JywgJ2NvbnRlbnQtYm94JykpO1xuICAgIGNvbnN0IG1heCA9IE1hdGgubWF4LmFwcGx5KG51bGwsIGhlaWdodHMpO1xuXG4gICAgcmV0dXJuIHtoZWlnaHRzLCBtYXh9O1xufVxuIiwiaW1wb3J0IEZsZXhCdWcgZnJvbSAnLi4vbWl4aW4vZmxleC1idWcnO1xuaW1wb3J0IHskLCBib3hNb2RlbEFkanVzdCwgY3NzLCBlbmRzV2l0aCwgaGVpZ2h0LCBpc051bWVyaWMsIGlzU3RyaW5nLCBpc1Zpc2libGUsIG9mZnNldCwgcXVlcnksIHRvRmxvYXR9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtGbGV4QnVnXSxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGV4cGFuZDogQm9vbGVhbixcbiAgICAgICAgb2Zmc2V0VG9wOiBCb29sZWFuLFxuICAgICAgICBvZmZzZXRCb3R0b206IEJvb2xlYW4sXG4gICAgICAgIG1pbkhlaWdodDogTnVtYmVyXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgZXhwYW5kOiBmYWxzZSxcbiAgICAgICAgb2Zmc2V0VG9wOiBmYWxzZSxcbiAgICAgICAgb2Zmc2V0Qm90dG9tOiBmYWxzZSxcbiAgICAgICAgbWluSGVpZ2h0OiAwXG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoe21pbkhlaWdodDogcHJldn0pIHtcblxuICAgICAgICAgICAgaWYgKCFpc1Zpc2libGUodGhpcy4kZWwpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBsZXQgbWluSGVpZ2h0ID0gJyc7XG4gICAgICAgICAgICBjb25zdCBib3ggPSBib3hNb2RlbEFkanVzdCh0aGlzLiRlbCwgJ2hlaWdodCcsICdjb250ZW50LWJveCcpO1xuXG4gICAgICAgICAgICBpZiAodGhpcy5leHBhbmQpIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuJGVsLmRhdGFzZXQuaGVpZ2h0RXhwYW5kID0gJyc7XG5cbiAgICAgICAgICAgICAgICBpZiAoJCgnW2RhdGEtaGVpZ2h0LWV4cGFuZF0nKSAhPT0gdGhpcy4kZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIG1pbkhlaWdodCA9IGhlaWdodCh3aW5kb3cpIC0gKG9mZnNldEhlaWdodChkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQpIC0gb2Zmc2V0SGVpZ2h0KHRoaXMuJGVsKSkgLSBib3ggfHwgJyc7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAvLyBvbiBtb2JpbGUgZGV2aWNlcyAoaU9TIGFuZCBBbmRyb2lkKSB3aW5kb3cuaW5uZXJIZWlnaHQgIT09IDEwMHZoXG4gICAgICAgICAgICAgICAgbWluSGVpZ2h0ID0gJ2NhbGMoMTAwdmgnO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMub2Zmc2V0VG9wKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3Qge3RvcH0gPSBvZmZzZXQodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICBtaW5IZWlnaHQgKz0gdG9wID4gMCAmJiB0b3AgPCBoZWlnaHQod2luZG93KSAvIDIgPyBgIC0gJHt0b3B9cHhgIDogJyc7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5vZmZzZXRCb3R0b20gPT09IHRydWUpIHtcblxuICAgICAgICAgICAgICAgICAgICBtaW5IZWlnaHQgKz0gYCAtICR7b2Zmc2V0SGVpZ2h0KHRoaXMuJGVsLm5leHRFbGVtZW50U2libGluZyl9cHhgO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChpc051bWVyaWModGhpcy5vZmZzZXRCb3R0b20pKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbWluSGVpZ2h0ICs9IGAgLSAke3RoaXMub2Zmc2V0Qm90dG9tfXZoYDtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5vZmZzZXRCb3R0b20gJiYgZW5kc1dpdGgodGhpcy5vZmZzZXRCb3R0b20sICdweCcpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbWluSGVpZ2h0ICs9IGAgLSAke3RvRmxvYXQodGhpcy5vZmZzZXRCb3R0b20pfXB4YDtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoaXNTdHJpbmcodGhpcy5vZmZzZXRCb3R0b20pKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbWluSGVpZ2h0ICs9IGAgLSAke29mZnNldEhlaWdodChxdWVyeSh0aGlzLm9mZnNldEJvdHRvbSwgdGhpcy4kZWwpKX1weGA7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBtaW5IZWlnaHQgKz0gYCR7Ym94ID8gYCAtICR7Ym94fXB4YCA6ICcnfSlgO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB7bWluSGVpZ2h0LCBwcmV2fTtcbiAgICAgICAgfSxcblxuICAgICAgICB3cml0ZSh7bWluSGVpZ2h0LCBwcmV2fSkge1xuXG4gICAgICAgICAgICBjc3ModGhpcy4kZWwsIHttaW5IZWlnaHR9KTtcblxuICAgICAgICAgICAgaWYgKG1pbkhlaWdodCAhPT0gcHJldikge1xuICAgICAgICAgICAgICAgIHRoaXMuJHVwZGF0ZSh0aGlzLiRlbCwgJ3Jlc2l6ZScpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAodGhpcy5taW5IZWlnaHQgJiYgdG9GbG9hdChjc3ModGhpcy4kZWwsICdtaW5IZWlnaHQnKSkgPCB0aGlzLm1pbkhlaWdodCkge1xuICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ21pbkhlaWdodCcsIHRoaXMubWluSGVpZ2h0KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiBvZmZzZXRIZWlnaHQoZWwpIHtcbiAgICByZXR1cm4gZWwgJiYgb2Zmc2V0KGVsKS5oZWlnaHQgfHwgMDtcbn1cbiIsImltcG9ydCB7JCwgJCQsIGFmdGVyLCBhamF4LCBhcHBlbmQsIGF0dHIsIGluY2x1ZGVzLCBpc1Zpc2libGUsIGlzVm9pZEVsZW1lbnQsIG5vb3AsIFByb21pc2UsIHJlbW92ZSwgcmVtb3ZlQXR0ciwgc3RhcnRzV2l0aH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGFyZ3M6ICdzcmMnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgaWQ6IEJvb2xlYW4sXG4gICAgICAgIGljb246IFN0cmluZyxcbiAgICAgICAgc3JjOiBTdHJpbmcsXG4gICAgICAgIHN0eWxlOiBTdHJpbmcsXG4gICAgICAgIHdpZHRoOiBOdW1iZXIsXG4gICAgICAgIGhlaWdodDogTnVtYmVyLFxuICAgICAgICByYXRpbzogTnVtYmVyLFxuICAgICAgICBjbGFzczogU3RyaW5nLFxuICAgICAgICBzdHJva2VBbmltYXRpb246IEJvb2xlYW4sXG4gICAgICAgIGZvY3VzYWJsZTogQm9vbGVhbiwgLy8gSUUgMTFcbiAgICAgICAgYXR0cmlidXRlczogJ2xpc3QnXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgcmF0aW86IDEsXG4gICAgICAgIGluY2x1ZGU6IFsnc3R5bGUnLCAnY2xhc3MnLCAnZm9jdXNhYmxlJ10sXG4gICAgICAgIGNsYXNzOiAnJyxcbiAgICAgICAgc3Ryb2tlQW5pbWF0aW9uOiBmYWxzZVxuICAgIH0sXG5cbiAgICBiZWZvcmVDb25uZWN0KCkge1xuXG4gICAgICAgIHRoaXMuY2xhc3MgKz0gJyB1ay1zdmcnO1xuXG4gICAgICAgIGlmICghdGhpcy5pY29uICYmIGluY2x1ZGVzKHRoaXMuc3JjLCAnIycpKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHBhcnRzID0gdGhpcy5zcmMuc3BsaXQoJyMnKTtcblxuICAgICAgICAgICAgaWYgKHBhcnRzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICBbdGhpcy5zcmMsIHRoaXMuaWNvbl0gPSBwYXJ0cztcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuc3ZnID0gdGhpcy5nZXRTdmcoKS50aGVuKGVsID0+IHtcbiAgICAgICAgICAgIHRoaXMuYXBwbHlBdHRyaWJ1dGVzKGVsKTtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnN2Z0VsID0gaW5zZXJ0U1ZHKGVsLCB0aGlzLiRlbCk7XG4gICAgICAgIH0sIG5vb3ApO1xuXG4gICAgfSxcblxuICAgIGRpc2Nvbm5lY3RlZCgpIHtcblxuICAgICAgICBpZiAoaXNWb2lkRWxlbWVudCh0aGlzLiRlbCkpIHtcbiAgICAgICAgICAgIGF0dHIodGhpcy4kZWwsICdoaWRkZW4nLCBudWxsKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aGlzLnN2Zykge1xuICAgICAgICAgICAgdGhpcy5zdmcudGhlbihzdmcgPT4gKCF0aGlzLl9jb25uZWN0ZWQgfHwgc3ZnICE9PSB0aGlzLnN2Z0VsKSAmJiByZW1vdmUoc3ZnKSwgbm9vcCk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnN2ZyA9IHRoaXMuc3ZnRWwgPSBudWxsO1xuXG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG4gICAgICAgICAgICByZXR1cm4gISEodGhpcy5zdHJva2VBbmltYXRpb24gJiYgdGhpcy5zdmdFbCAmJiBpc1Zpc2libGUodGhpcy5zdmdFbCkpO1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKCkge1xuICAgICAgICAgICAgYXBwbHlBbmltYXRpb24odGhpcy5zdmdFbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdHlwZTogWydyZXNpemUnXVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBnZXRTdmcoKSB7XG4gICAgICAgICAgICByZXR1cm4gbG9hZFNWRyh0aGlzLnNyYykudGhlbihzdmcgPT5cbiAgICAgICAgICAgICAgICBwYXJzZVNWRyhzdmcsIHRoaXMuaWNvbikgfHwgUHJvbWlzZS5yZWplY3QoJ1NWRyBub3QgZm91bmQuJylcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgYXBwbHlBdHRyaWJ1dGVzKGVsKSB7XG5cbiAgICAgICAgICAgIGZvciAoY29uc3QgcHJvcCBpbiB0aGlzLiRvcHRpb25zLnByb3BzKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXNbcHJvcF0gJiYgaW5jbHVkZXModGhpcy5pbmNsdWRlLCBwcm9wKSkge1xuICAgICAgICAgICAgICAgICAgICBhdHRyKGVsLCBwcm9wLCB0aGlzW3Byb3BdKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGZvciAoY29uc3QgYXR0cmlidXRlIGluIHRoaXMuYXR0cmlidXRlcykge1xuICAgICAgICAgICAgICAgIGNvbnN0IFtwcm9wLCB2YWx1ZV0gPSB0aGlzLmF0dHJpYnV0ZXNbYXR0cmlidXRlXS5zcGxpdCgnOicsIDIpO1xuICAgICAgICAgICAgICAgIGF0dHIoZWwsIHByb3AsIHZhbHVlKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCF0aGlzLmlkKSB7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQXR0cihlbCwgJ2lkJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IHByb3BzID0gWyd3aWR0aCcsICdoZWlnaHQnXTtcbiAgICAgICAgICAgIGxldCBkaW1lbnNpb25zID0gW3RoaXMud2lkdGgsIHRoaXMuaGVpZ2h0XTtcblxuICAgICAgICAgICAgaWYgKCFkaW1lbnNpb25zLnNvbWUodmFsID0+IHZhbCkpIHtcbiAgICAgICAgICAgICAgICBkaW1lbnNpb25zID0gcHJvcHMubWFwKHByb3AgPT4gYXR0cihlbCwgcHJvcCkpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCB2aWV3Qm94ID0gYXR0cihlbCwgJ3ZpZXdCb3gnKTtcbiAgICAgICAgICAgIGlmICh2aWV3Qm94ICYmICFkaW1lbnNpb25zLnNvbWUodmFsID0+IHZhbCkpIHtcbiAgICAgICAgICAgICAgICBkaW1lbnNpb25zID0gdmlld0JveC5zcGxpdCgnICcpLnNsaWNlKDIpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkaW1lbnNpb25zLmZvckVhY2goKHZhbCwgaSkgPT4ge1xuICAgICAgICAgICAgICAgIHZhbCA9ICh2YWwgfCAwKSAqIHRoaXMucmF0aW87XG4gICAgICAgICAgICAgICAgdmFsICYmIGF0dHIoZWwsIHByb3BzW2ldLCB2YWwpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHZhbCAmJiAhZGltZW5zaW9uc1tpIF4gMV0pIHtcbiAgICAgICAgICAgICAgICAgICAgcmVtb3ZlQXR0cihlbCwgcHJvcHNbaSBeIDFdKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgYXR0cihlbCwgJ2RhdGEtc3ZnJywgdGhpcy5pY29uIHx8IHRoaXMuc3JjKTtcblxuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmNvbnN0IHN2Z3MgPSB7fTtcblxuZnVuY3Rpb24gbG9hZFNWRyhzcmMpIHtcblxuICAgIGlmIChzdmdzW3NyY10pIHtcbiAgICAgICAgcmV0dXJuIHN2Z3Nbc3JjXTtcbiAgICB9XG5cbiAgICByZXR1cm4gc3Znc1tzcmNdID0gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXG4gICAgICAgIGlmICghc3JjKSB7XG4gICAgICAgICAgICByZWplY3QoKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChzdGFydHNXaXRoKHNyYywgJ2RhdGE6JykpIHtcbiAgICAgICAgICAgIHJlc29sdmUoZGVjb2RlVVJJQ29tcG9uZW50KHNyYy5zcGxpdCgnLCcpWzFdKSk7XG4gICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgIGFqYXgoc3JjKS50aGVuKFxuICAgICAgICAgICAgICAgIHhociA9PiByZXNvbHZlKHhoci5yZXNwb25zZSksXG4gICAgICAgICAgICAgICAgKCkgPT4gcmVqZWN0KCdTVkcgbm90IGZvdW5kLicpXG4gICAgICAgICAgICApO1xuXG4gICAgICAgIH1cblxuICAgIH0pO1xufVxuXG5mdW5jdGlvbiBwYXJzZVNWRyhzdmcsIGljb24pIHtcblxuICAgIGlmIChpY29uICYmIGluY2x1ZGVzKHN2ZywgJzxzeW1ib2wnKSkge1xuICAgICAgICBzdmcgPSBwYXJzZVN5bWJvbHMoc3ZnLCBpY29uKSB8fCBzdmc7XG4gICAgfVxuXG4gICAgc3ZnID0gJChzdmcuc3Vic3RyKHN2Zy5pbmRleE9mKCc8c3ZnJykpKTtcbiAgICByZXR1cm4gc3ZnICYmIHN2Zy5oYXNDaGlsZE5vZGVzKCkgJiYgc3ZnO1xufVxuXG5jb25zdCBzeW1ib2xSZSA9IC88c3ltYm9sKC4qP2lkPShbJ1wiXSkoLio/KVxcMlteXSo/PFxcLylzeW1ib2w+L2c7XG5jb25zdCBzeW1ib2xzID0ge307XG5cbmZ1bmN0aW9uIHBhcnNlU3ltYm9scyhzdmcsIGljb24pIHtcblxuICAgIGlmICghc3ltYm9sc1tzdmddKSB7XG5cbiAgICAgICAgc3ltYm9sc1tzdmddID0ge307XG5cbiAgICAgICAgbGV0IG1hdGNoO1xuICAgICAgICB3aGlsZSAoKG1hdGNoID0gc3ltYm9sUmUuZXhlYyhzdmcpKSkge1xuICAgICAgICAgICAgc3ltYm9sc1tzdmddW21hdGNoWzNdXSA9IGA8c3ZnIHhtbG5zPVwiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmdcIiR7bWF0Y2hbMV19c3ZnPmA7XG4gICAgICAgIH1cblxuICAgICAgICBzeW1ib2xSZS5sYXN0SW5kZXggPSAwO1xuXG4gICAgfVxuXG4gICAgcmV0dXJuIHN5bWJvbHNbc3ZnXVtpY29uXTtcbn1cblxuZnVuY3Rpb24gYXBwbHlBbmltYXRpb24oZWwpIHtcblxuICAgIGNvbnN0IGxlbmd0aCA9IGdldE1heFBhdGhMZW5ndGgoZWwpO1xuXG4gICAgaWYgKGxlbmd0aCkge1xuICAgICAgICBlbC5zdHlsZS5zZXRQcm9wZXJ0eSgnLS11ay1hbmltYXRpb24tc3Ryb2tlJywgbGVuZ3RoKTtcbiAgICB9XG5cbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGdldE1heFBhdGhMZW5ndGgoZWwpIHtcbiAgICByZXR1cm4gTWF0aC5jZWlsKE1hdGgubWF4KC4uLiQkKCdbc3Ryb2tlXScsIGVsKS5tYXAoc3Ryb2tlID0+XG4gICAgICAgIHN0cm9rZS5nZXRUb3RhbExlbmd0aCAmJiBzdHJva2UuZ2V0VG90YWxMZW5ndGgoKSB8fCAwXG4gICAgKS5jb25jYXQoWzBdKSkpO1xufVxuXG5mdW5jdGlvbiBpbnNlcnRTVkcoZWwsIHJvb3QpIHtcbiAgICBpZiAoaXNWb2lkRWxlbWVudChyb290KSB8fCByb290LnRhZ05hbWUgPT09ICdDQU5WQVMnKSB7XG5cbiAgICAgICAgYXR0cihyb290LCAnaGlkZGVuJywgdHJ1ZSk7XG5cbiAgICAgICAgY29uc3QgbmV4dCA9IHJvb3QubmV4dEVsZW1lbnRTaWJsaW5nO1xuICAgICAgICByZXR1cm4gZXF1YWxzKGVsLCBuZXh0KVxuICAgICAgICAgICAgPyBuZXh0XG4gICAgICAgICAgICA6IGFmdGVyKHJvb3QsIGVsKTtcblxuICAgIH0gZWxzZSB7XG5cbiAgICAgICAgY29uc3QgbGFzdCA9IHJvb3QubGFzdEVsZW1lbnRDaGlsZDtcbiAgICAgICAgcmV0dXJuIGVxdWFscyhlbCwgbGFzdClcbiAgICAgICAgICAgID8gbGFzdFxuICAgICAgICAgICAgOiBhcHBlbmQocm9vdCwgZWwpO1xuXG4gICAgfVxufVxuXG5mdW5jdGlvbiBlcXVhbHMoZWwsIG90aGVyKSB7XG4gICAgcmV0dXJuIGF0dHIoZWwsICdkYXRhLXN2ZycpID09PSBhdHRyKG90aGVyLCAnZGF0YS1zdmcnKTtcbn1cbiIsImltcG9ydCBTVkcgZnJvbSAnLi9zdmcnO1xuaW1wb3J0IGNsb3NlSWNvbiBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9jbG9zZS1pY29uLnN2Zyc7XG5pbXBvcnQgY2xvc2VMYXJnZSBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9jbG9zZS1sYXJnZS5zdmcnO1xuaW1wb3J0IG1hcmtlciBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9tYXJrZXIuc3ZnJztcbmltcG9ydCBuYXZiYXJUb2dnbGVJY29uIGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL25hdmJhci10b2dnbGUtaWNvbi5zdmcnO1xuaW1wb3J0IG92ZXJsYXlJY29uIGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL292ZXJsYXktaWNvbi5zdmcnO1xuaW1wb3J0IHBhZ2luYXRpb25OZXh0IGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL3BhZ2luYXRpb24tbmV4dC5zdmcnO1xuaW1wb3J0IHBhZ2luYXRpb25QcmV2aW91cyBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9wYWdpbmF0aW9uLXByZXZpb3VzLnN2Zyc7XG5pbXBvcnQgc2VhcmNoSWNvbiBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zZWFyY2gtaWNvbi5zdmcnO1xuaW1wb3J0IHNlYXJjaExhcmdlIGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL3NlYXJjaC1sYXJnZS5zdmcnO1xuaW1wb3J0IHNlYXJjaE5hdmJhciBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zZWFyY2gtbmF2YmFyLnN2Zyc7XG5pbXBvcnQgc2xpZGVuYXZOZXh0IGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL3NsaWRlbmF2LW5leHQuc3ZnJztcbmltcG9ydCBzbGlkZW5hdk5leHRMYXJnZSBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zbGlkZW5hdi1uZXh0LWxhcmdlLnN2Zyc7XG5pbXBvcnQgc2xpZGVuYXZQcmV2aW91cyBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zbGlkZW5hdi1wcmV2aW91cy5zdmcnO1xuaW1wb3J0IHNsaWRlbmF2UHJldmlvdXNMYXJnZSBmcm9tICcuLi8uLi9pbWFnZXMvY29tcG9uZW50cy9zbGlkZW5hdi1wcmV2aW91cy1sYXJnZS5zdmcnO1xuaW1wb3J0IHNwaW5uZXIgZnJvbSAnLi4vLi4vaW1hZ2VzL2NvbXBvbmVudHMvc3Bpbm5lci5zdmcnO1xuaW1wb3J0IHRvdG9wIGZyb20gJy4uLy4uL2ltYWdlcy9jb21wb25lbnRzL3RvdG9wLnN2Zyc7XG5pbXBvcnQgeyQsIGFkZENsYXNzLCBhcHBseSwgY3NzLCBlYWNoLCBoYXNDbGFzcywgaHlwaGVuYXRlLCBpc1J0bCwgaXNTdHJpbmcsIG5vb3AsIHBhcmVudHMsIFByb21pc2UsIHN3YXB9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5jb25zdCBwYXJzZWQgPSB7fTtcbmNvbnN0IGljb25zID0ge1xuICAgIHNwaW5uZXIsXG4gICAgdG90b3AsXG4gICAgbWFya2VyLFxuICAgICdjbG9zZS1pY29uJzogY2xvc2VJY29uLFxuICAgICdjbG9zZS1sYXJnZSc6IGNsb3NlTGFyZ2UsXG4gICAgJ25hdmJhci10b2dnbGUtaWNvbic6IG5hdmJhclRvZ2dsZUljb24sXG4gICAgJ292ZXJsYXktaWNvbic6IG92ZXJsYXlJY29uLFxuICAgICdwYWdpbmF0aW9uLW5leHQnOiBwYWdpbmF0aW9uTmV4dCxcbiAgICAncGFnaW5hdGlvbi1wcmV2aW91cyc6IHBhZ2luYXRpb25QcmV2aW91cyxcbiAgICAnc2VhcmNoLWljb24nOiBzZWFyY2hJY29uLFxuICAgICdzZWFyY2gtbGFyZ2UnOiBzZWFyY2hMYXJnZSxcbiAgICAnc2VhcmNoLW5hdmJhcic6IHNlYXJjaE5hdmJhcixcbiAgICAnc2xpZGVuYXYtbmV4dCc6IHNsaWRlbmF2TmV4dCxcbiAgICAnc2xpZGVuYXYtbmV4dC1sYXJnZSc6IHNsaWRlbmF2TmV4dExhcmdlLFxuICAgICdzbGlkZW5hdi1wcmV2aW91cyc6IHNsaWRlbmF2UHJldmlvdXMsXG4gICAgJ3NsaWRlbmF2LXByZXZpb3VzLWxhcmdlJzogc2xpZGVuYXZQcmV2aW91c0xhcmdlXG59O1xuXG5jb25zdCBJY29uID0ge1xuXG4gICAgaW5zdGFsbCxcblxuICAgIGV4dGVuZHM6IFNWRyxcblxuICAgIGFyZ3M6ICdpY29uJyxcblxuICAgIHByb3BzOiBbJ2ljb24nXSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgaW5jbHVkZTogWydmb2N1c2FibGUnXVxuICAgIH0sXG5cbiAgICBpc0ljb246IHRydWUsXG5cbiAgICBiZWZvcmVDb25uZWN0KCkge1xuICAgICAgICBhZGRDbGFzcyh0aGlzLiRlbCwgJ3VrLWljb24nKTtcbiAgICB9LFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIGdldFN2ZygpIHtcblxuICAgICAgICAgICAgY29uc3QgaWNvbiA9IGdldEljb24oYXBwbHlSdGwodGhpcy5pY29uKSk7XG5cbiAgICAgICAgICAgIGlmICghaWNvbikge1xuICAgICAgICAgICAgICAgIHJldHVybiBQcm9taXNlLnJlamVjdCgnSWNvbiBub3QgZm91bmQuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBQcm9taXNlLnJlc29sdmUoaWNvbik7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZXhwb3J0IGRlZmF1bHQgSWNvbjtcblxuZXhwb3J0IGNvbnN0IEljb25Db21wb25lbnQgPSB7XG5cbiAgICBhcmdzOiBmYWxzZSxcblxuICAgIGV4dGVuZHM6IEljb24sXG5cbiAgICBkYXRhOiB2bSA9PiAoe1xuICAgICAgICBpY29uOiBoeXBoZW5hdGUodm0uY29uc3RydWN0b3Iub3B0aW9ucy5uYW1lKVxuICAgIH0pLFxuXG4gICAgYmVmb3JlQ29ubmVjdCgpIHtcbiAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsIHRoaXMuJG5hbWUpO1xuICAgIH1cblxufTtcblxuZXhwb3J0IGNvbnN0IFNsaWRlbmF2ID0ge1xuXG4gICAgZXh0ZW5kczogSWNvbkNvbXBvbmVudCxcblxuICAgIGJlZm9yZUNvbm5lY3QoKSB7XG4gICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCAndWstc2xpZGVuYXYnKTtcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBpY29uKHtpY29ufSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gaGFzQ2xhc3MoJGVsLCAndWstc2xpZGVuYXYtbGFyZ2UnKVxuICAgICAgICAgICAgICAgID8gYCR7aWNvbn0tbGFyZ2VgXG4gICAgICAgICAgICAgICAgOiBpY29uO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG5cbmV4cG9ydCBjb25zdCBTZWFyY2ggPSB7XG5cbiAgICBleHRlbmRzOiBJY29uQ29tcG9uZW50LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBpY29uKHtpY29ufSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gaGFzQ2xhc3MoJGVsLCAndWstc2VhcmNoLWljb24nKSAmJiBwYXJlbnRzKCRlbCwgJy51ay1zZWFyY2gtbGFyZ2UnKS5sZW5ndGhcbiAgICAgICAgICAgICAgICA/ICdzZWFyY2gtbGFyZ2UnXG4gICAgICAgICAgICAgICAgOiBwYXJlbnRzKCRlbCwgJy51ay1zZWFyY2gtbmF2YmFyJykubGVuZ3RoXG4gICAgICAgICAgICAgICAgICAgID8gJ3NlYXJjaC1uYXZiYXInXG4gICAgICAgICAgICAgICAgICAgIDogaWNvbjtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5leHBvcnQgY29uc3QgQ2xvc2UgPSB7XG5cbiAgICBleHRlbmRzOiBJY29uQ29tcG9uZW50LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBpY29uKCkge1xuICAgICAgICAgICAgcmV0dXJuIGBjbG9zZS0ke2hhc0NsYXNzKHRoaXMuJGVsLCAndWstY2xvc2UtbGFyZ2UnKSA/ICdsYXJnZScgOiAnaWNvbid9YDtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5leHBvcnQgY29uc3QgU3Bpbm5lciA9IHtcblxuICAgIGV4dGVuZHM6IEljb25Db21wb25lbnQsXG5cbiAgICBjb25uZWN0ZWQoKSB7XG4gICAgICAgIHRoaXMuc3ZnLnRoZW4oc3ZnID0+IHRoaXMucmF0aW8gIT09IDEgJiYgY3NzKCQoJ2NpcmNsZScsIHN2ZyksICdzdHJva2VXaWR0aCcsIDEgLyB0aGlzLnJhdGlvKSwgbm9vcCk7XG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiBpbnN0YWxsKFVJa2l0KSB7XG4gICAgVUlraXQuaWNvbi5hZGQgPSAobmFtZSwgc3ZnKSA9PiB7XG5cbiAgICAgICAgY29uc3QgYWRkZWQgPSBpc1N0cmluZyhuYW1lKSA/ICh7W25hbWVdOiBzdmd9KSA6IG5hbWU7XG4gICAgICAgIGVhY2goYWRkZWQsIChzdmcsIG5hbWUpID0+IHtcbiAgICAgICAgICAgIGljb25zW25hbWVdID0gc3ZnO1xuICAgICAgICAgICAgZGVsZXRlIHBhcnNlZFtuYW1lXTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKFVJa2l0Ll9pbml0aWFsaXplZCkge1xuICAgICAgICAgICAgYXBwbHkoZG9jdW1lbnQuYm9keSwgZWwgPT5cbiAgICAgICAgICAgICAgICBlYWNoKFVJa2l0LmdldENvbXBvbmVudHMoZWwpLCBjbXAgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjbXAuJG9wdGlvbnMuaXNJY29uICYmIGNtcC5pY29uIGluIGFkZGVkICYmIGNtcC4kcmVzZXQoKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgKTtcbiAgICAgICAgfVxuICAgIH07XG59XG5cbmZ1bmN0aW9uIGdldEljb24oaWNvbikge1xuXG4gICAgaWYgKCFpY29uc1tpY29uXSkge1xuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG5cbiAgICBpZiAoIXBhcnNlZFtpY29uXSkge1xuICAgICAgICBwYXJzZWRbaWNvbl0gPSAkKGljb25zW2ljb25dLnRyaW0oKSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHBhcnNlZFtpY29uXS5jbG9uZU5vZGUodHJ1ZSk7XG59XG5cbmZ1bmN0aW9uIGFwcGx5UnRsKGljb24pIHtcbiAgICByZXR1cm4gaXNSdGwgPyBzd2FwKHN3YXAoaWNvbiwgJ2xlZnQnLCAncmlnaHQnKSwgJ3ByZXZpb3VzJywgJ25leHQnKSA6IGljb247XG59XG4iLCJpbXBvcnQge2NyZWF0ZUV2ZW50LCBjc3MsIERpbWVuc2lvbnMsIGVzY2FwZSwgZ2V0SW1hZ2UsIGluY2x1ZGVzLCBJbnRlcnNlY3Rpb25PYnNlcnZlciwgaXNVbmRlZmluZWQsIG5vb3AsIHF1ZXJ5QWxsLCBzdGFydHNXaXRoLCB0b0Zsb2F0LCB0b1B4LCB0cmlnZ2VyfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgYXJnczogJ2RhdGFTcmMnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgZGF0YVNyYzogU3RyaW5nLFxuICAgICAgICBkYXRhU3Jjc2V0OiBCb29sZWFuLFxuICAgICAgICBzaXplczogU3RyaW5nLFxuICAgICAgICB3aWR0aDogTnVtYmVyLFxuICAgICAgICBoZWlnaHQ6IE51bWJlcixcbiAgICAgICAgb2Zmc2V0VG9wOiBTdHJpbmcsXG4gICAgICAgIG9mZnNldExlZnQ6IFN0cmluZyxcbiAgICAgICAgdGFyZ2V0OiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBkYXRhU3JjOiAnJyxcbiAgICAgICAgZGF0YVNyY3NldDogZmFsc2UsXG4gICAgICAgIHNpemVzOiBmYWxzZSxcbiAgICAgICAgd2lkdGg6IGZhbHNlLFxuICAgICAgICBoZWlnaHQ6IGZhbHNlLFxuICAgICAgICBvZmZzZXRUb3A6ICc1MHZoJyxcbiAgICAgICAgb2Zmc2V0TGVmdDogMCxcbiAgICAgICAgdGFyZ2V0OiBmYWxzZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGNhY2hlS2V5KHtkYXRhU3JjfSkge1xuICAgICAgICAgICAgcmV0dXJuIGAke3RoaXMuJG5hbWV9LiR7ZGF0YVNyY31gO1xuICAgICAgICB9LFxuXG4gICAgICAgIHdpZHRoKHt3aWR0aCwgZGF0YVdpZHRofSkge1xuICAgICAgICAgICAgcmV0dXJuIHdpZHRoIHx8IGRhdGFXaWR0aDtcbiAgICAgICAgfSxcblxuICAgICAgICBoZWlnaHQoe2hlaWdodCwgZGF0YUhlaWdodH0pIHtcbiAgICAgICAgICAgIHJldHVybiBoZWlnaHQgfHwgZGF0YUhlaWdodDtcbiAgICAgICAgfSxcblxuICAgICAgICBzaXplcyh7c2l6ZXMsIGRhdGFTaXplc30pIHtcbiAgICAgICAgICAgIHJldHVybiBzaXplcyB8fCBkYXRhU2l6ZXM7XG4gICAgICAgIH0sXG5cbiAgICAgICAgaXNJbWcoXywgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gaXNJbWcoJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICB0YXJnZXQ6IHtcblxuICAgICAgICAgICAgZ2V0KHt0YXJnZXR9KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFt0aGlzLiRlbF0uY29uY2F0KHF1ZXJ5QWxsKHRhcmdldCwgdGhpcy4kZWwpKTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIHdhdGNoKCkge1xuICAgICAgICAgICAgICAgIHRoaXMub2JzZXJ2ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgb2Zmc2V0VG9wKHtvZmZzZXRUb3B9KSB7XG4gICAgICAgICAgICByZXR1cm4gdG9QeChvZmZzZXRUb3AsICdoZWlnaHQnKTtcbiAgICAgICAgfSxcblxuICAgICAgICBvZmZzZXRMZWZ0KHtvZmZzZXRMZWZ0fSkge1xuICAgICAgICAgICAgcmV0dXJuIHRvUHgob2Zmc2V0TGVmdCwgJ3dpZHRoJyk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgaWYgKHN0b3JhZ2VbdGhpcy5jYWNoZUtleV0pIHtcbiAgICAgICAgICAgIHNldFNyY0F0dHJzKHRoaXMuJGVsLCBzdG9yYWdlW3RoaXMuY2FjaGVLZXldIHx8IHRoaXMuZGF0YVNyYywgdGhpcy5kYXRhU3Jjc2V0LCB0aGlzLnNpemVzKTtcbiAgICAgICAgfSBlbHNlIGlmICh0aGlzLmlzSW1nICYmIHRoaXMud2lkdGggJiYgdGhpcy5oZWlnaHQpIHtcbiAgICAgICAgICAgIHNldFNyY0F0dHJzKHRoaXMuJGVsLCBnZXRQbGFjZWhvbGRlckltYWdlKHRoaXMud2lkdGgsIHRoaXMuaGVpZ2h0LCB0aGlzLnNpemVzKSk7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLm9ic2VydmVyID0gbmV3IEludGVyc2VjdGlvbk9ic2VydmVyKHRoaXMubG9hZCwge1xuICAgICAgICAgICAgcm9vdE1hcmdpbjogYCR7dGhpcy5vZmZzZXRUb3B9cHggJHt0aGlzLm9mZnNldExlZnR9cHhgXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZSh0aGlzLm9ic2VydmUpO1xuXG4gICAgfSxcblxuICAgIGRpc2Nvbm5lY3RlZCgpIHtcbiAgICAgICAgdGhpcy5vYnNlcnZlci5kaXNjb25uZWN0KCk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoe2ltYWdlfSkge1xuXG4gICAgICAgICAgICBpZiAoIWltYWdlICYmIGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmxvYWQodGhpcy5vYnNlcnZlci50YWtlUmVjb3JkcygpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKHRoaXMuaXNJbWcpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGltYWdlICYmIGltYWdlLnRoZW4oaW1nID0+IGltZyAmJiBpbWcuY3VycmVudFNyYyAhPT0gJycgJiYgc2V0U3JjQXR0cnModGhpcy4kZWwsIGN1cnJlbnRTcmMoaW1nKSkpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoZGF0YSkge1xuXG4gICAgICAgICAgICBpZiAodGhpcy5kYXRhU3Jjc2V0ICYmIHdpbmRvdy5kZXZpY2VQaXhlbFJhdGlvICE9PSAxKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCBiZ1NpemUgPSBjc3ModGhpcy4kZWwsICdiYWNrZ3JvdW5kU2l6ZScpO1xuICAgICAgICAgICAgICAgIGlmIChiZ1NpemUubWF0Y2goL14oYXV0b1xccz8pKyQvKSB8fCB0b0Zsb2F0KGJnU2l6ZSkgPT09IGRhdGEuYmdTaXplKSB7XG4gICAgICAgICAgICAgICAgICAgIGRhdGEuYmdTaXplID0gZ2V0U291cmNlU2l6ZSh0aGlzLmRhdGFTcmNzZXQsIHRoaXMuc2l6ZXMpO1xuICAgICAgICAgICAgICAgICAgICBjc3ModGhpcy4kZWwsICdiYWNrZ3JvdW5kU2l6ZScsIGAke2RhdGEuYmdTaXplfXB4YCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgbG9hZChlbnRyaWVzKSB7XG5cbiAgICAgICAgICAgIC8vIE9sZCBjaHJvbWl1bSBiYXNlZCBicm93c2VycyAoVUMgQnJvd3NlcikgZGlkIG5vdCBpbXBsZW1lbnQgYGlzSW50ZXJzZWN0aW5nYFxuICAgICAgICAgICAgaWYgKCFlbnRyaWVzLnNvbWUoZW50cnkgPT4gaXNVbmRlZmluZWQoZW50cnkuaXNJbnRlcnNlY3RpbmcpIHx8IGVudHJ5LmlzSW50ZXJzZWN0aW5nKSkge1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5fZGF0YS5pbWFnZSA9IGdldEltYWdlKHRoaXMuZGF0YVNyYywgdGhpcy5kYXRhU3Jjc2V0LCB0aGlzLnNpemVzKS50aGVuKGltZyA9PiB7XG5cbiAgICAgICAgICAgICAgICBzZXRTcmNBdHRycyh0aGlzLiRlbCwgY3VycmVudFNyYyhpbWcpLCBpbWcuc3Jjc2V0LCBpbWcuc2l6ZXMpO1xuICAgICAgICAgICAgICAgIHN0b3JhZ2VbdGhpcy5jYWNoZUtleV0gPSBjdXJyZW50U3JjKGltZyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGltZztcblxuICAgICAgICAgICAgfSwgbm9vcCk7XG5cbiAgICAgICAgICAgIHRoaXMub2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xuICAgICAgICB9LFxuXG4gICAgICAgIG9ic2VydmUoKSB7XG4gICAgICAgICAgICBpZiAoIXRoaXMuX2RhdGEuaW1hZ2UgJiYgdGhpcy5fY29ubmVjdGVkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy50YXJnZXQuZm9yRWFjaChlbCA9PiB0aGlzLm9ic2VydmVyLm9ic2VydmUoZWwpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiBzZXRTcmNBdHRycyhlbCwgc3JjLCBzcmNzZXQsIHNpemVzKSB7XG5cbiAgICBpZiAoaXNJbWcoZWwpKSB7XG4gICAgICAgIHNpemVzICYmIChlbC5zaXplcyA9IHNpemVzKTtcbiAgICAgICAgc3Jjc2V0ICYmIChlbC5zcmNzZXQgPSBzcmNzZXQpO1xuICAgICAgICBzcmMgJiYgKGVsLnNyYyA9IHNyYyk7XG4gICAgfSBlbHNlIGlmIChzcmMpIHtcblxuICAgICAgICBjb25zdCBjaGFuZ2UgPSAhaW5jbHVkZXMoZWwuc3R5bGUuYmFja2dyb3VuZEltYWdlLCBzcmMpO1xuICAgICAgICBpZiAoY2hhbmdlKSB7XG4gICAgICAgICAgICBjc3MoZWwsICdiYWNrZ3JvdW5kSW1hZ2UnLCBgdXJsKCR7ZXNjYXBlKHNyYyl9KWApO1xuICAgICAgICAgICAgdHJpZ2dlcihlbCwgY3JlYXRlRXZlbnQoJ2xvYWQnLCBmYWxzZSkpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn1cblxuZnVuY3Rpb24gZ2V0UGxhY2Vob2xkZXJJbWFnZSh3aWR0aCwgaGVpZ2h0LCBzaXplcykge1xuXG4gICAgaWYgKHNpemVzKSB7XG4gICAgICAgICh7d2lkdGgsIGhlaWdodH0gPSBEaW1lbnNpb25zLnJhdGlvKHt3aWR0aCwgaGVpZ2h0fSwgJ3dpZHRoJywgdG9QeChzaXplc1RvUGl4ZWwoc2l6ZXMpKSkpO1xuICAgIH1cblxuICAgIHJldHVybiBgZGF0YTppbWFnZS9zdmcreG1sO3V0ZjgsPHN2ZyB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCIgd2lkdGg9XCIke3dpZHRofVwiIGhlaWdodD1cIiR7aGVpZ2h0fVwiPjwvc3ZnPmA7XG59XG5cbmNvbnN0IHNpemVzUmUgPSAvXFxzKiguKj8pXFxzKihcXHcrfGNhbGNcXCguKj9cXCkpXFxzKig/Oix8JCkvZztcbmZ1bmN0aW9uIHNpemVzVG9QaXhlbChzaXplcykge1xuICAgIGxldCBtYXRjaGVzO1xuXG4gICAgc2l6ZXNSZS5sYXN0SW5kZXggPSAwO1xuXG4gICAgd2hpbGUgKChtYXRjaGVzID0gc2l6ZXNSZS5leGVjKHNpemVzKSkpIHtcbiAgICAgICAgaWYgKCFtYXRjaGVzWzFdIHx8IHdpbmRvdy5tYXRjaE1lZGlhKG1hdGNoZXNbMV0pLm1hdGNoZXMpIHtcbiAgICAgICAgICAgIG1hdGNoZXMgPSBldmFsdWF0ZVNpemUobWF0Y2hlc1syXSk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBtYXRjaGVzIHx8ICcxMDB2dyc7XG59XG5cbmNvbnN0IHNpemVSZSA9IC9cXGQrKD86XFx3K3wlKS9nO1xuY29uc3QgYWRkaXRpb25SZSA9IC9bKy1dPyhcXGQrKS9nO1xuZnVuY3Rpb24gZXZhbHVhdGVTaXplKHNpemUpIHtcbiAgICByZXR1cm4gc3RhcnRzV2l0aChzaXplLCAnY2FsYycpXG4gICAgICAgID8gc2l6ZVxuICAgICAgICAgICAgLnN1YnN0cmluZyg1LCBzaXplLmxlbmd0aCAtIDEpXG4gICAgICAgICAgICAucmVwbGFjZShzaXplUmUsIHNpemUgPT4gdG9QeChzaXplKSlcbiAgICAgICAgICAgIC5yZXBsYWNlKC8gL2csICcnKVxuICAgICAgICAgICAgLm1hdGNoKGFkZGl0aW9uUmUpXG4gICAgICAgICAgICAucmVkdWNlKChhLCBiKSA9PiBhICsgK2IsIDApXG4gICAgICAgIDogc2l6ZTtcbn1cblxuY29uc3Qgc3JjU2V0UmUgPSAvXFxzK1xcZCt3XFxzKig/Oix8JCkvZztcbmZ1bmN0aW9uIGdldFNvdXJjZVNpemUoc3Jjc2V0LCBzaXplcykge1xuICAgIGNvbnN0IHNyY1NpemUgPSB0b1B4KHNpemVzVG9QaXhlbChzaXplcykpO1xuICAgIGNvbnN0IGRlc2NyaXB0b3JzID0gKHNyY3NldC5tYXRjaChzcmNTZXRSZSkgfHwgW10pLm1hcCh0b0Zsb2F0KS5zb3J0KChhLCBiKSA9PiBhIC0gYik7XG5cbiAgICByZXR1cm4gZGVzY3JpcHRvcnMuZmlsdGVyKHNpemUgPT4gc2l6ZSA+PSBzcmNTaXplKVswXSB8fCBkZXNjcmlwdG9ycy5wb3AoKSB8fCAnJztcbn1cblxuZnVuY3Rpb24gaXNJbWcoZWwpIHtcbiAgICByZXR1cm4gZWwudGFnTmFtZSA9PT0gJ0lNRyc7XG59XG5cbmZ1bmN0aW9uIGN1cnJlbnRTcmMoZWwpIHtcbiAgICByZXR1cm4gZWwuY3VycmVudFNyYyB8fCBlbC5zcmM7XG59XG5cbmNvbnN0IGtleSA9ICdfX3Rlc3RfXyc7XG5sZXQgc3RvcmFnZTtcblxuLy8gd29ya2Fyb3VuZCBmb3IgU2FmYXJpJ3MgcHJpdmF0ZSBicm93c2luZyBtb2RlIGFuZCBhY2Nlc3Npbmcgc2Vzc2lvblN0b3JhZ2UgaW4gQmxpbmtcbnRyeSB7XG4gICAgc3RvcmFnZSA9IHdpbmRvdy5zZXNzaW9uU3RvcmFnZSB8fCB7fTtcbiAgICBzdG9yYWdlW2tleV0gPSAxO1xuICAgIGRlbGV0ZSBzdG9yYWdlW2tleV07XG59IGNhdGNoIChlKSB7XG4gICAgc3RvcmFnZSA9IHt9O1xufVxuIiwiaW1wb3J0IHtnZXRDc3NWYXIsIGlzU3RyaW5nLCB0b0Zsb2F0fSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgbWVkaWE6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtZWRpYTogZmFsc2VcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBtYXRjaE1lZGlhKCkge1xuICAgICAgICAgICAgY29uc3QgbWVkaWEgPSB0b01lZGlhKHRoaXMubWVkaWEpO1xuICAgICAgICAgICAgcmV0dXJuICFtZWRpYSB8fCB3aW5kb3cubWF0Y2hNZWRpYShtZWRpYSkubWF0Y2hlcztcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuXG5mdW5jdGlvbiB0b01lZGlhKHZhbHVlKSB7XG5cbiAgICBpZiAoaXNTdHJpbmcodmFsdWUpKSB7XG4gICAgICAgIGlmICh2YWx1ZVswXSA9PT0gJ0AnKSB7XG4gICAgICAgICAgICBjb25zdCBuYW1lID0gYGJyZWFrcG9pbnQtJHt2YWx1ZS5zdWJzdHIoMSl9YDtcbiAgICAgICAgICAgIHZhbHVlID0gdG9GbG9hdChnZXRDc3NWYXIobmFtZSkpO1xuICAgICAgICB9IGVsc2UgaWYgKGlzTmFOKHZhbHVlKSkge1xuICAgICAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHZhbHVlICYmICFpc05hTih2YWx1ZSkgPyBgKG1pbi13aWR0aDogJHt2YWx1ZX1weClgIDogZmFsc2U7XG59XG4iLCJpbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IE1lZGlhIGZyb20gJy4uL21peGluL21lZGlhJztcbmltcG9ydCB7YXR0ciwgZ2V0Q3NzVmFyLCB0b2dnbGVDbGFzcywgdW53cmFwLCB3cmFwSW5uZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgTWVkaWFdLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgZmlsbDogU3RyaW5nXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgZmlsbDogJycsXG4gICAgICAgIGNsc1dyYXBwZXI6ICd1ay1sZWFkZXItZmlsbCcsXG4gICAgICAgIGNsc0hpZGU6ICd1ay1sZWFkZXItaGlkZScsXG4gICAgICAgIGF0dHJGaWxsOiAnZGF0YS1maWxsJ1xuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGZpbGwoe2ZpbGx9KSB7XG4gICAgICAgICAgICByZXR1cm4gZmlsbCB8fCBnZXRDc3NWYXIoJ2xlYWRlci1maWxsLWNvbnRlbnQnKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgW3RoaXMud3JhcHBlcl0gPSB3cmFwSW5uZXIodGhpcy4kZWwsIGA8c3BhbiBjbGFzcz1cIiR7dGhpcy5jbHNXcmFwcGVyfVwiPmApO1xuICAgIH0sXG5cbiAgICBkaXNjb25uZWN0ZWQoKSB7XG4gICAgICAgIHVud3JhcCh0aGlzLndyYXBwZXIuY2hpbGROb2Rlcyk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoe2NoYW5nZWQsIHdpZHRofSkge1xuXG4gICAgICAgICAgICBjb25zdCBwcmV2ID0gd2lkdGg7XG5cbiAgICAgICAgICAgIHdpZHRoID0gTWF0aC5mbG9vcih0aGlzLiRlbC5vZmZzZXRXaWR0aCAvIDIpO1xuXG4gICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgIHdpZHRoLFxuICAgICAgICAgICAgICAgIGZpbGw6IHRoaXMuZmlsbCxcbiAgICAgICAgICAgICAgICBjaGFuZ2VkOiBjaGFuZ2VkIHx8IHByZXYgIT09IHdpZHRoLFxuICAgICAgICAgICAgICAgIGhpZGU6ICF0aGlzLm1hdGNoTWVkaWFcbiAgICAgICAgICAgIH07XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoZGF0YSkge1xuXG4gICAgICAgICAgICB0b2dnbGVDbGFzcyh0aGlzLndyYXBwZXIsIHRoaXMuY2xzSGlkZSwgZGF0YS5oaWRlKTtcblxuICAgICAgICAgICAgaWYgKGRhdGEuY2hhbmdlZCkge1xuICAgICAgICAgICAgICAgIGRhdGEuY2hhbmdlZCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIGF0dHIodGhpcy53cmFwcGVyLCB0aGlzLmF0dHJGaWxsLCBuZXcgQXJyYXkoZGF0YS53aWR0aCkuam9pbihkYXRhLmZpbGwpKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHskfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgY29udGFpbmVyOiBCb29sZWFuXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgY29udGFpbmVyOiB0cnVlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgY29udGFpbmVyKHtjb250YWluZXJ9KSB7XG4gICAgICAgICAgICByZXR1cm4gY29udGFpbmVyID09PSB0cnVlICYmIHRoaXMuJGNvbnRhaW5lciB8fCBjb250YWluZXIgJiYgJChjb250YWluZXIpO1xuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgeyQsIGFkZENsYXNzLCBhcHBlbmQsIGNzcywgaW5jbHVkZXMsIGxhc3QsIG9uLCBvbmNlLCBQcm9taXNlLCByZW1vdmVDbGFzcywgdG9Ncywgd2lkdGgsIHdpdGhpbn0gZnJvbSAndWlraXQtdXRpbCc7XG5pbXBvcnQgQ2xhc3MgZnJvbSAnLi9jbGFzcyc7XG5pbXBvcnQgQ29udGFpbmVyIGZyb20gJy4vY29udGFpbmVyJztcbmltcG9ydCBUb2dnbGFibGUgZnJvbSAnLi90b2dnbGFibGUnO1xuaW1wb3J0IHtkZWxheU9ufSBmcm9tICcuLi9jb3JlL2Ryb3AnO1xuXG5jb25zdCBhY3RpdmUgPSBbXTtcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbWl4aW5zOiBbQ2xhc3MsIENvbnRhaW5lciwgVG9nZ2xhYmxlXSxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIHNlbFBhbmVsOiBTdHJpbmcsXG4gICAgICAgIHNlbENsb3NlOiBTdHJpbmcsXG4gICAgICAgIGVzY0Nsb3NlOiBCb29sZWFuLFxuICAgICAgICBiZ0Nsb3NlOiBCb29sZWFuLFxuICAgICAgICBzdGFjazogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIGNsczogJ3VrLW9wZW4nLFxuICAgICAgICBlc2NDbG9zZTogdHJ1ZSxcbiAgICAgICAgYmdDbG9zZTogdHJ1ZSxcbiAgICAgICAgb3ZlcmxheTogdHJ1ZSxcbiAgICAgICAgc3RhY2s6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgcGFuZWwoe3NlbFBhbmVsfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJChzZWxQYW5lbCwgJGVsKTtcbiAgICAgICAgfSxcblxuICAgICAgICB0cmFuc2l0aW9uRWxlbWVudCgpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnBhbmVsO1xuICAgICAgICB9LFxuXG4gICAgICAgIGJnQ2xvc2Uoe2JnQ2xvc2V9KSB7XG4gICAgICAgICAgICByZXR1cm4gYmdDbG9zZSAmJiB0aGlzLnBhbmVsO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgYmVmb3JlRGlzY29ubmVjdCgpIHtcbiAgICAgICAgaWYgKHRoaXMuaXNUb2dnbGVkKCkpIHtcbiAgICAgICAgICAgIHRoaXMudG9nZ2xlTm93KHRoaXMuJGVsLCBmYWxzZSk7XG4gICAgICAgIH1cbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5zZWxDbG9zZTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLmhpZGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ3RvZ2dsZScsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGUuZGVmYXVsdFByZXZlbnRlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnYmVmb3Jlc2hvdycsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGluY2x1ZGVzKGFjdGl2ZSwgdGhpcykpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmICghdGhpcy5zdGFjayAmJiBhY3RpdmUubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIFByb21pc2UuYWxsKGFjdGl2ZS5tYXAobW9kYWwgPT4gbW9kYWwuaGlkZSgpKSkudGhlbih0aGlzLnNob3cpO1xuICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlLnB1c2godGhpcyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnc2hvdycsXG5cbiAgICAgICAgICAgIHNlbGY6IHRydWUsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAod2lkdGgod2luZG93KSAtIHdpZHRoKGRvY3VtZW50KSAmJiB0aGlzLm92ZXJsYXkpIHtcbiAgICAgICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmJvZHksICdvdmVyZmxvd1knLCAnc2Nyb2xsJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYWRkQ2xhc3MoZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LCB0aGlzLmNsc1BhZ2UpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuYmdDbG9zZSkge1xuICAgICAgICAgICAgICAgICAgICBvbmNlKHRoaXMuJGVsLCAnaGlkZScsIGRlbGF5T24oZG9jdW1lbnQsICdjbGljaycsICh7ZGVmYXVsdFByZXZlbnRlZCwgdGFyZ2V0fSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgY3VycmVudCA9IGxhc3QoYWN0aXZlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICghZGVmYXVsdFByZXZlbnRlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICYmIGN1cnJlbnQgPT09IHRoaXNcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAmJiAoIWN1cnJlbnQub3ZlcmxheSB8fCB3aXRoaW4odGFyZ2V0LCBjdXJyZW50LiRlbCkpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJiYgIXdpdGhpbih0YXJnZXQsIGN1cnJlbnQucGFuZWwpXG4gICAgICAgICAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjdXJyZW50LmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSksIHtzZWxmOiB0cnVlfSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuZXNjQ2xvc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgb25jZSh0aGlzLiRlbCwgJ2hpZGUnLCBvbihkb2N1bWVudCwgJ2tleWRvd24nLCBlID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGN1cnJlbnQgPSBsYXN0KGFjdGl2ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoZS5rZXlDb2RlID09PSAyNyAmJiBjdXJyZW50ID09PSB0aGlzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnQuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KSwge3NlbGY6IHRydWV9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6ICdoaWRkZW4nLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgYWN0aXZlLnNwbGljZShhY3RpdmUuaW5kZXhPZih0aGlzKSwgMSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoIWFjdGl2ZS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmJvZHksICdvdmVyZmxvd1knLCAnJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCFhY3RpdmUuc29tZShtb2RhbCA9PiBtb2RhbC5jbHNQYWdlID09PSB0aGlzLmNsc1BhZ2UpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCwgdGhpcy5jbHNQYWdlKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIHRvZ2dsZSgpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmlzVG9nZ2xlZCgpID8gdGhpcy5oaWRlKCkgOiB0aGlzLnNob3coKTtcbiAgICAgICAgfSxcblxuICAgICAgICBzaG93KCkge1xuXG4gICAgICAgICAgICBpZiAodGhpcy5jb250YWluZXIgJiYgdGhpcy4kZWwucGFyZW50Tm9kZSAhPT0gdGhpcy5jb250YWluZXIpIHtcbiAgICAgICAgICAgICAgICBhcHBlbmQodGhpcy5jb250YWluZXIsIHRoaXMuJGVsKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UocmVzb2x2ZSA9PlxuICAgICAgICAgICAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvdygpLnRoZW4ocmVzb2x2ZSlcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0aGlzLnRvZ2dsZUVsZW1lbnQodGhpcy4kZWwsIHRydWUsIGFuaW1hdGUodGhpcykpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGhpZGUoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy50b2dnbGVFbGVtZW50KHRoaXMuJGVsLCBmYWxzZSwgYW5pbWF0ZSh0aGlzKSk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZnVuY3Rpb24gYW5pbWF0ZSh7dHJhbnNpdGlvbkVsZW1lbnQsIF90b2dnbGV9KSB7XG4gICAgcmV0dXJuIChlbCwgc2hvdykgPT5cbiAgICAgICAgbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT5cbiAgICAgICAgICAgIG9uY2UoZWwsICdzaG93IGhpZGUnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgZWwuX3JlamVjdCAmJiBlbC5fcmVqZWN0KCk7XG4gICAgICAgICAgICAgICAgZWwuX3JlamVjdCA9IHJlamVjdDtcblxuICAgICAgICAgICAgICAgIF90b2dnbGUoZWwsIHNob3cpO1xuXG4gICAgICAgICAgICAgICAgY29uc3Qgb2ZmID0gb25jZSh0cmFuc2l0aW9uRWxlbWVudCwgJ3RyYW5zaXRpb25zdGFydCcsICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgb25jZSh0cmFuc2l0aW9uRWxlbWVudCwgJ3RyYW5zaXRpb25lbmQgdHJhbnNpdGlvbmNhbmNlbCcsIHJlc29sdmUsIHtzZWxmOiB0cnVlfSk7XG4gICAgICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lcik7XG4gICAgICAgICAgICAgICAgfSwge3NlbGY6IHRydWV9KTtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHRpbWVyID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIG9mZigpO1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKCk7XG4gICAgICAgICAgICAgICAgfSwgdG9Ncyhjc3ModHJhbnNpdGlvbkVsZW1lbnQsICd0cmFuc2l0aW9uRHVyYXRpb24nKSkpO1xuXG4gICAgICAgICAgICB9KVxuICAgICAgICApO1xufVxuIiwiaW1wb3J0IE1vZGFsIGZyb20gJy4uL21peGluL21vZGFsJztcbmltcG9ydCB7JCwgYWRkQ2xhc3MsIGFzc2lnbiwgY3NzLCBoYXNDbGFzcywgaGVpZ2h0LCBodG1sLCBpc1N0cmluZywgb24sIFByb21pc2UsIHJlbW92ZUNsYXNzfSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgaW5zdGFsbCxcblxuICAgIG1peGluczogW01vZGFsXSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgY2xzUGFnZTogJ3VrLW1vZGFsLXBhZ2UnLFxuICAgICAgICBzZWxQYW5lbDogJy51ay1tb2RhbC1kaWFsb2cnLFxuICAgICAgICBzZWxDbG9zZTogJy51ay1tb2RhbC1jbG9zZSwgLnVrLW1vZGFsLWNsb3NlLWRlZmF1bHQsIC51ay1tb2RhbC1jbG9zZS1vdXRzaWRlLCAudWstbW9kYWwtY2xvc2UtZnVsbCdcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuICAgICAgICAgICAgbmFtZTogJ3Nob3cnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuXG4gICAgICAgICAgICAgICAgaWYgKGhhc0NsYXNzKHRoaXMucGFuZWwsICd1ay1tYXJnaW4tYXV0by12ZXJ0aWNhbCcpKSB7XG4gICAgICAgICAgICAgICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCAndWstZmxleCcpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ2Rpc3BsYXknLCAnYmxvY2snKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBoZWlnaHQodGhpcy4kZWwpOyAvLyBmb3JjZSByZWZsb3dcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnaGlkZGVuJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ2Rpc3BsYXknLCAnJyk7XG4gICAgICAgICAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy4kZWwsICd1ay1mbGV4Jyk7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgXVxuXG59O1xuXG5mdW5jdGlvbiBpbnN0YWxsKFVJa2l0KSB7XG5cbiAgICBVSWtpdC5tb2RhbC5kaWFsb2cgPSBmdW5jdGlvbiAoY29udGVudCwgb3B0aW9ucykge1xuXG4gICAgICAgIGNvbnN0IGRpYWxvZyA9IFVJa2l0Lm1vZGFsKGBcbiAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbFwiPlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbC1kaWFsb2dcIj4ke2NvbnRlbnR9PC9kaXY+XG4gICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgIGAsIG9wdGlvbnMpO1xuXG4gICAgICAgIGRpYWxvZy5zaG93KCk7XG5cbiAgICAgICAgb24oZGlhbG9nLiRlbCwgJ2hpZGRlbicsICgpID0+IFByb21pc2UucmVzb2x2ZSgoKSA9PiBkaWFsb2cuJGRlc3Ryb3kodHJ1ZSkpLCB7c2VsZjogdHJ1ZX0pO1xuXG4gICAgICAgIHJldHVybiBkaWFsb2c7XG4gICAgfTtcblxuICAgIFVJa2l0Lm1vZGFsLmFsZXJ0ID0gZnVuY3Rpb24gKG1lc3NhZ2UsIG9wdGlvbnMpIHtcblxuICAgICAgICBvcHRpb25zID0gYXNzaWduKHtiZ0Nsb3NlOiBmYWxzZSwgZXNjQ2xvc2U6IGZhbHNlLCBsYWJlbHM6IFVJa2l0Lm1vZGFsLmxhYmVsc30sIG9wdGlvbnMpO1xuXG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShcbiAgICAgICAgICAgIHJlc29sdmUgPT4gb24oVUlraXQubW9kYWwuZGlhbG9nKGBcbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwidWstbW9kYWwtYm9keVwiPiR7aXNTdHJpbmcobWVzc2FnZSkgPyBtZXNzYWdlIDogaHRtbChtZXNzYWdlKX08L2Rpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwidWstbW9kYWwtZm9vdGVyIHVrLXRleHQtcmlnaHRcIj5cbiAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tcHJpbWFyeSB1ay1tb2RhbC1jbG9zZVwiIGF1dG9mb2N1cz4ke29wdGlvbnMubGFiZWxzLm9rfTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgYCwgb3B0aW9ucykuJGVsLCAnaGlkZScsIHJlc29sdmUpXG4gICAgICAgICk7XG4gICAgfTtcblxuICAgIFVJa2l0Lm1vZGFsLmNvbmZpcm0gPSBmdW5jdGlvbiAobWVzc2FnZSwgb3B0aW9ucykge1xuXG4gICAgICAgIG9wdGlvbnMgPSBhc3NpZ24oe2JnQ2xvc2U6IGZhbHNlLCBlc2NDbG9zZTogdHJ1ZSwgbGFiZWxzOiBVSWtpdC5tb2RhbC5sYWJlbHN9LCBvcHRpb25zKTtcblxuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBjb25maXJtID0gVUlraXQubW9kYWwuZGlhbG9nKGBcbiAgICAgICAgICAgICAgICA8Zm9ybT5cbiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsLWJvZHlcIj4ke2lzU3RyaW5nKG1lc3NhZ2UpID8gbWVzc2FnZSA6IGh0bWwobWVzc2FnZSl9PC9kaXY+XG4gICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbC1mb290ZXIgdWstdGV4dC1yaWdodFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tZGVmYXVsdCB1ay1tb2RhbC1jbG9zZVwiIHR5cGU9XCJidXR0b25cIj4ke29wdGlvbnMubGFiZWxzLmNhbmNlbH08L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJ1ay1idXR0b24gdWstYnV0dG9uLXByaW1hcnlcIiBhdXRvZm9jdXM+JHtvcHRpb25zLmxhYmVscy5va308L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPC9mb3JtPlxuICAgICAgICAgICAgYCwgb3B0aW9ucyk7XG5cbiAgICAgICAgICAgIGxldCByZXNvbHZlZCA9IGZhbHNlO1xuXG4gICAgICAgICAgICBvbihjb25maXJtLiRlbCwgJ3N1Ym1pdCcsICdmb3JtJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHJlc29sdmUoKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgY29uZmlybS5oaWRlKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIG9uKGNvbmZpcm0uJGVsLCAnaGlkZScsICgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoIXJlc29sdmVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlamVjdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBVSWtpdC5tb2RhbC5wcm9tcHQgPSBmdW5jdGlvbiAobWVzc2FnZSwgdmFsdWUsIG9wdGlvbnMpIHtcblxuICAgICAgICBvcHRpb25zID0gYXNzaWduKHtiZ0Nsb3NlOiBmYWxzZSwgZXNjQ2xvc2U6IHRydWUsIGxhYmVsczogVUlraXQubW9kYWwubGFiZWxzfSwgb3B0aW9ucyk7XG5cbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuXG4gICAgICAgICAgICBjb25zdCBwcm9tcHQgPSBVSWtpdC5tb2RhbC5kaWFsb2coYFxuICAgICAgICAgICAgICAgICAgICA8Zm9ybSBjbGFzcz1cInVrLWZvcm0tc3RhY2tlZFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInVrLW1vZGFsLWJvZHlcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWw+JHtpc1N0cmluZyhtZXNzYWdlKSA/IG1lc3NhZ2UgOiBodG1sKG1lc3NhZ2UpfTwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGlucHV0IGNsYXNzPVwidWstaW5wdXRcIiBhdXRvZm9jdXM+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJ1ay1tb2RhbC1mb290ZXIgdWstdGV4dC1yaWdodFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJ1ay1idXR0b24gdWstYnV0dG9uLWRlZmF1bHQgdWstbW9kYWwtY2xvc2VcIiB0eXBlPVwiYnV0dG9uXCI+JHtvcHRpb25zLmxhYmVscy5jYW5jZWx9PC9idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz1cInVrLWJ1dHRvbiB1ay1idXR0b24tcHJpbWFyeVwiPiR7b3B0aW9ucy5sYWJlbHMub2t9PC9idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgPC9mb3JtPlxuICAgICAgICAgICAgICAgIGAsIG9wdGlvbnMpLFxuICAgICAgICAgICAgICAgIGlucHV0ID0gJCgnaW5wdXQnLCBwcm9tcHQuJGVsKTtcblxuICAgICAgICAgICAgaW5wdXQudmFsdWUgPSB2YWx1ZTtcblxuICAgICAgICAgICAgbGV0IHJlc29sdmVkID0gZmFsc2U7XG5cbiAgICAgICAgICAgIG9uKHByb21wdC4kZWwsICdzdWJtaXQnLCAnZm9ybScsIGUgPT4ge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlKGlucHV0LnZhbHVlKTtcbiAgICAgICAgICAgICAgICByZXNvbHZlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgcHJvbXB0LmhpZGUoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgb24ocHJvbXB0LiRlbCwgJ2hpZGUnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCFyZXNvbHZlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXNvbHZlKG51bGwpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICBVSWtpdC5tb2RhbC5sYWJlbHMgPSB7XG4gICAgICAgIG9rOiAnT2snLFxuICAgICAgICBjYW5jZWw6ICdDYW5jZWwnXG4gICAgfTtcblxufVxuIiwiaW1wb3J0IEFjY29yZGlvbiBmcm9tICcuL2FjY29yZGlvbic7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIGV4dGVuZHM6IEFjY29yZGlvbixcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdGFyZ2V0czogJz4gLnVrLXBhcmVudCcsXG4gICAgICAgIHRvZ2dsZTogJz4gYScsXG4gICAgICAgIGNvbnRlbnQ6ICc+IHVsJ1xuICAgIH1cblxufTtcbiIsImltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQgRmxleEJ1ZyBmcm9tICcuLi9taXhpbi9mbGV4LWJ1Zyc7XG5pbXBvcnQgeyQsICQkLCBhZGRDbGFzcywgYWZ0ZXIsIGFzc2lnbiwgY3NzLCBoZWlnaHQsIGluY2x1ZGVzLCBpc1J0bCwgaXNWaXNpYmxlLCBtYXRjaGVzLCBub29wLCBQcm9taXNlLCBxdWVyeSwgcmVtb3ZlLCB0b0Zsb2F0LCBUcmFuc2l0aW9uLCB3aXRoaW59IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtDbGFzcywgRmxleEJ1Z10sXG5cbiAgICBwcm9wczoge1xuICAgICAgICBkcm9wZG93bjogU3RyaW5nLFxuICAgICAgICBtb2RlOiAnbGlzdCcsXG4gICAgICAgIGFsaWduOiBTdHJpbmcsXG4gICAgICAgIG9mZnNldDogTnVtYmVyLFxuICAgICAgICBib3VuZGFyeTogQm9vbGVhbixcbiAgICAgICAgYm91bmRhcnlBbGlnbjogQm9vbGVhbixcbiAgICAgICAgY2xzRHJvcDogU3RyaW5nLFxuICAgICAgICBkZWxheVNob3c6IE51bWJlcixcbiAgICAgICAgZGVsYXlIaWRlOiBOdW1iZXIsXG4gICAgICAgIGRyb3BiYXI6IEJvb2xlYW4sXG4gICAgICAgIGRyb3BiYXJNb2RlOiBTdHJpbmcsXG4gICAgICAgIGRyb3BiYXJBbmNob3I6IEJvb2xlYW4sXG4gICAgICAgIGR1cmF0aW9uOiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBkcm9wZG93bjogJy51ay1uYXZiYXItbmF2ID4gbGknLFxuICAgICAgICBhbGlnbjogIWlzUnRsID8gJ2xlZnQnIDogJ3JpZ2h0JyxcbiAgICAgICAgY2xzRHJvcDogJ3VrLW5hdmJhci1kcm9wZG93bicsXG4gICAgICAgIG1vZGU6IHVuZGVmaW5lZCxcbiAgICAgICAgb2Zmc2V0OiB1bmRlZmluZWQsXG4gICAgICAgIGRlbGF5U2hvdzogdW5kZWZpbmVkLFxuICAgICAgICBkZWxheUhpZGU6IHVuZGVmaW5lZCxcbiAgICAgICAgYm91bmRhcnlBbGlnbjogdW5kZWZpbmVkLFxuICAgICAgICBmbGlwOiAneCcsXG4gICAgICAgIGJvdW5kYXJ5OiB0cnVlLFxuICAgICAgICBkcm9wYmFyOiBmYWxzZSxcbiAgICAgICAgZHJvcGJhck1vZGU6ICdzbGlkZScsXG4gICAgICAgIGRyb3BiYXJBbmNob3I6IGZhbHNlLFxuICAgICAgICBkdXJhdGlvbjogMjAwLFxuICAgICAgICBmb3JjZUhlaWdodDogdHJ1ZSxcbiAgICAgICAgc2VsTWluSGVpZ2h0OiAnLnVrLW5hdmJhci1uYXYgPiBsaSA+IGEsIC51ay1uYXZiYXItaXRlbSwgLnVrLW5hdmJhci10b2dnbGUnXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgYm91bmRhcnkoe2JvdW5kYXJ5LCBib3VuZGFyeUFsaWdufSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gKGJvdW5kYXJ5ID09PSB0cnVlIHx8IGJvdW5kYXJ5QWxpZ24pID8gJGVsIDogYm91bmRhcnk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZHJvcGJhckFuY2hvcih7ZHJvcGJhckFuY2hvcn0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHF1ZXJ5KGRyb3BiYXJBbmNob3IsICRlbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgcG9zKHthbGlnbn0pIHtcbiAgICAgICAgICAgIHJldHVybiBgYm90dG9tLSR7YWxpZ259YDtcbiAgICAgICAgfSxcblxuICAgICAgICBkcm9wZG93bnMoe2Ryb3Bkb3duLCBjbHNEcm9wfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJCQoYCR7ZHJvcGRvd259IC4ke2Nsc0Ryb3B9YCwgJGVsKTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGJlZm9yZUNvbm5lY3QoKSB7XG5cbiAgICAgICAgY29uc3Qge2Ryb3BiYXJ9ID0gdGhpcy4kcHJvcHM7XG5cbiAgICAgICAgdGhpcy5kcm9wYmFyID0gZHJvcGJhciAmJiAocXVlcnkoZHJvcGJhciwgdGhpcy4kZWwpIHx8ICQoJysgLnVrLW5hdmJhci1kcm9wYmFyJywgdGhpcy4kZWwpIHx8ICQoJzxkaXY+PC9kaXY+JykpO1xuXG4gICAgICAgIGlmICh0aGlzLmRyb3BiYXIpIHtcblxuICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy5kcm9wYmFyLCAndWstbmF2YmFyLWRyb3BiYXInKTtcblxuICAgICAgICAgICAgaWYgKHRoaXMuZHJvcGJhck1vZGUgPT09ICdzbGlkZScpIHtcbiAgICAgICAgICAgICAgICBhZGRDbGFzcyh0aGlzLmRyb3BiYXIsICd1ay1uYXZiYXItZHJvcGJhci1zbGlkZScpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgZGlzY29ubmVjdGVkKCkge1xuICAgICAgICB0aGlzLmRyb3BiYXIgJiYgcmVtb3ZlKHRoaXMuZHJvcGJhcik7XG4gICAgfSxcblxuICAgIHVwZGF0ZSgpIHtcblxuICAgICAgICB0aGlzLiRjcmVhdGUoXG4gICAgICAgICAgICAnZHJvcCcsXG4gICAgICAgICAgICB0aGlzLmRyb3Bkb3ducy5maWx0ZXIoZWwgPT4gIXRoaXMuZ2V0RHJvcGRvd24oZWwpKSxcbiAgICAgICAgICAgIGFzc2lnbih7fSwgdGhpcy4kcHJvcHMsIHtib3VuZGFyeTogdGhpcy5ib3VuZGFyeSwgcG9zOiB0aGlzLnBvcywgb2Zmc2V0OiB0aGlzLmRyb3BiYXIgfHwgdGhpcy5vZmZzZXR9KVxuICAgICAgICApO1xuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdtb3VzZW92ZXInLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wZG93bjtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoe2N1cnJlbnR9KSB7XG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcbiAgICAgICAgICAgICAgICBpZiAoYWN0aXZlICYmIGFjdGl2ZS50b2dnbGUgJiYgIXdpdGhpbihhY3RpdmUudG9nZ2xlLiRlbCwgY3VycmVudCkgJiYgIWFjdGl2ZS50cmFja2VyLm1vdmVzVG8oYWN0aXZlLiRlbCkpIHtcbiAgICAgICAgICAgICAgICAgICAgYWN0aXZlLmhpZGUoZmFsc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdtb3VzZWxlYXZlJyxcblxuICAgICAgICAgICAgZWwoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuZHJvcGJhcjtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmIChhY3RpdmUgJiYgIXRoaXMuZHJvcGRvd25zLnNvbWUoZWwgPT4gbWF0Y2hlcyhlbCwgJzpob3ZlcicpKSkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnYmVmb3Jlc2hvdycsXG5cbiAgICAgICAgICAgIGNhcHR1cmU6IHRydWUsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICghdGhpcy5kcm9wYmFyLnBhcmVudE5vZGUpIHtcbiAgICAgICAgICAgICAgICAgICAgYWZ0ZXIodGhpcy5kcm9wYmFyQW5jaG9yIHx8IHRoaXMuJGVsLCB0aGlzLmRyb3BiYXIpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdzaG93JyxcblxuICAgICAgICAgICAgY2FwdHVyZTogdHJ1ZSxcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmRyb3BiYXI7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKF8sIGRyb3ApIHtcblxuICAgICAgICAgICAgICAgIGNvbnN0IHskZWwsIGRpcn0gPSBkcm9wO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5jbHNEcm9wICYmIGFkZENsYXNzKCRlbCwgYCR7dGhpcy5jbHNEcm9wfS1kcm9wYmFyYCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoZGlyID09PSAnYm90dG9tJykge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRyYW5zaXRpb25UbygkZWwub2Zmc2V0SGVpZ2h0ICsgdG9GbG9hdChjc3MoJGVsLCAnbWFyZ2luVG9wJykpICsgdG9GbG9hdChjc3MoJGVsLCAnbWFyZ2luQm90dG9tJykpLCAkZWwpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnYmVmb3JlaGlkZScsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlLCB7JGVsfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmIChtYXRjaGVzKHRoaXMuZHJvcGJhciwgJzpob3ZlcicpICYmIGFjdGl2ZSAmJiBhY3RpdmUuJGVsID09PSAkZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnaGlkZScsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5kcm9wYmFyO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihfLCB7JGVsfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy5nZXRBY3RpdmUoKTtcblxuICAgICAgICAgICAgICAgIGlmICghYWN0aXZlIHx8IGFjdGl2ZSAmJiBhY3RpdmUuJGVsID09PSAkZWwpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy50cmFuc2l0aW9uVG8oMCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgbWV0aG9kczoge1xuXG4gICAgICAgIGdldEFjdGl2ZSgpIHtcbiAgICAgICAgICAgIGNvbnN0IFthY3RpdmVdID0gdGhpcy5kcm9wZG93bnMubWFwKHRoaXMuZ2V0RHJvcGRvd24pLmZpbHRlcihkcm9wID0+IGRyb3AgJiYgZHJvcC5pc0FjdGl2ZSgpKTtcbiAgICAgICAgICAgIHJldHVybiBhY3RpdmUgJiYgaW5jbHVkZXMoYWN0aXZlLm1vZGUsICdob3ZlcicpICYmIHdpdGhpbihhY3RpdmUudG9nZ2xlLiRlbCwgdGhpcy4kZWwpICYmIGFjdGl2ZTtcbiAgICAgICAgfSxcblxuICAgICAgICB0cmFuc2l0aW9uVG8obmV3SGVpZ2h0LCBlbCkge1xuXG4gICAgICAgICAgICBjb25zdCB7ZHJvcGJhcn0gPSB0aGlzO1xuICAgICAgICAgICAgY29uc3Qgb2xkSGVpZ2h0ID0gaXNWaXNpYmxlKGRyb3BiYXIpID8gaGVpZ2h0KGRyb3BiYXIpIDogMDtcblxuICAgICAgICAgICAgZWwgPSBvbGRIZWlnaHQgPCBuZXdIZWlnaHQgJiYgZWw7XG5cbiAgICAgICAgICAgIGNzcyhlbCwgJ2NsaXAnLCBgcmVjdCgwLCR7ZWwub2Zmc2V0V2lkdGh9cHgsJHtvbGRIZWlnaHR9cHgsMClgKTtcblxuICAgICAgICAgICAgaGVpZ2h0KGRyb3BiYXIsIG9sZEhlaWdodCk7XG5cbiAgICAgICAgICAgIFRyYW5zaXRpb24uY2FuY2VsKFtlbCwgZHJvcGJhcl0pO1xuICAgICAgICAgICAgcmV0dXJuIFByb21pc2UuYWxsKFtcbiAgICAgICAgICAgICAgICBUcmFuc2l0aW9uLnN0YXJ0KGRyb3BiYXIsIHtoZWlnaHQ6IG5ld0hlaWdodH0sIHRoaXMuZHVyYXRpb24pLFxuICAgICAgICAgICAgICAgIFRyYW5zaXRpb24uc3RhcnQoZWwsIHtjbGlwOiBgcmVjdCgwLCR7ZWwub2Zmc2V0V2lkdGh9cHgsJHtuZXdIZWlnaHR9cHgsMClgfSwgdGhpcy5kdXJhdGlvbilcbiAgICAgICAgICAgIF0pXG4gICAgICAgICAgICAgICAgLmNhdGNoKG5vb3ApXG4gICAgICAgICAgICAgICAgLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjc3MoZWwsIHtjbGlwOiAnJ30pO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiR1cGRhdGUoZHJvcGJhcik7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZ2V0RHJvcGRvd24oZWwpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLiRnZXRDb21wb25lbnQoZWwsICdkcm9wJykgfHwgdGhpcy4kZ2V0Q29tcG9uZW50KGVsLCAnZHJvcGRvd24nKTtcbiAgICAgICAgfVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IE1vZGFsIGZyb20gJy4uL21peGluL21vZGFsJztcbmltcG9ydCB7JCwgYWRkQ2xhc3MsIGFwcGVuZCwgY3NzLCBlbmRzV2l0aCwgaGFzQ2xhc3MsIGhlaWdodCwgcmVtb3ZlQ2xhc3MsIHVud3JhcCwgd3JhcEFsbH0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW01vZGFsXSxcblxuICAgIGFyZ3M6ICdtb2RlJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIG1vZGU6IFN0cmluZyxcbiAgICAgICAgZmxpcDogQm9vbGVhbixcbiAgICAgICAgb3ZlcmxheTogQm9vbGVhblxuICAgIH0sXG5cbiAgICBkYXRhOiB7XG4gICAgICAgIG1vZGU6ICdzbGlkZScsXG4gICAgICAgIGZsaXA6IGZhbHNlLFxuICAgICAgICBvdmVybGF5OiBmYWxzZSxcbiAgICAgICAgY2xzUGFnZTogJ3VrLW9mZmNhbnZhcy1wYWdlJyxcbiAgICAgICAgY2xzQ29udGFpbmVyOiAndWstb2ZmY2FudmFzLWNvbnRhaW5lcicsXG4gICAgICAgIHNlbFBhbmVsOiAnLnVrLW9mZmNhbnZhcy1iYXInLFxuICAgICAgICBjbHNGbGlwOiAndWstb2ZmY2FudmFzLWZsaXAnLFxuICAgICAgICBjbHNDb250YWluZXJBbmltYXRpb246ICd1ay1vZmZjYW52YXMtY29udGFpbmVyLWFuaW1hdGlvbicsXG4gICAgICAgIGNsc1NpZGViYXJBbmltYXRpb246ICd1ay1vZmZjYW52YXMtYmFyLWFuaW1hdGlvbicsXG4gICAgICAgIGNsc01vZGU6ICd1ay1vZmZjYW52YXMnLFxuICAgICAgICBjbHNPdmVybGF5OiAndWstb2ZmY2FudmFzLW92ZXJsYXknLFxuICAgICAgICBzZWxDbG9zZTogJy51ay1vZmZjYW52YXMtY2xvc2UnLFxuICAgICAgICBjb250YWluZXI6IGZhbHNlXG4gICAgfSxcblxuICAgIGNvbXB1dGVkOiB7XG5cbiAgICAgICAgY2xzRmxpcCh7ZmxpcCwgY2xzRmxpcH0pIHtcbiAgICAgICAgICAgIHJldHVybiBmbGlwID8gY2xzRmxpcCA6ICcnO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc092ZXJsYXkoe292ZXJsYXksIGNsc092ZXJsYXl9KSB7XG4gICAgICAgICAgICByZXR1cm4gb3ZlcmxheSA/IGNsc092ZXJsYXkgOiAnJztcbiAgICAgICAgfSxcblxuICAgICAgICBjbHNNb2RlKHttb2RlLCBjbHNNb2RlfSkge1xuICAgICAgICAgICAgcmV0dXJuIGAke2Nsc01vZGV9LSR7bW9kZX1gO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc1NpZGViYXJBbmltYXRpb24oe21vZGUsIGNsc1NpZGViYXJBbmltYXRpb259KSB7XG4gICAgICAgICAgICByZXR1cm4gbW9kZSA9PT0gJ25vbmUnIHx8IG1vZGUgPT09ICdyZXZlYWwnID8gJycgOiBjbHNTaWRlYmFyQW5pbWF0aW9uO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNsc0NvbnRhaW5lckFuaW1hdGlvbih7bW9kZSwgY2xzQ29udGFpbmVyQW5pbWF0aW9ufSkge1xuICAgICAgICAgICAgcmV0dXJuIG1vZGUgIT09ICdwdXNoJyAmJiBtb2RlICE9PSAncmV2ZWFsJyA/ICcnIDogY2xzQ29udGFpbmVyQW5pbWF0aW9uO1xuICAgICAgICB9LFxuXG4gICAgICAgIHRyYW5zaXRpb25FbGVtZW50KHttb2RlfSkge1xuICAgICAgICAgICAgcmV0dXJuIG1vZGUgPT09ICdyZXZlYWwnID8gdGhpcy5wYW5lbC5wYXJlbnROb2RlIDogdGhpcy5wYW5lbDtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGV2ZW50czogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuICdhW2hyZWZePVwiI1wiXSc7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKHtjdXJyZW50OiB7aGFzaH0sIGRlZmF1bHRQcmV2ZW50ZWR9KSB7XG4gICAgICAgICAgICAgICAgaWYgKCFkZWZhdWx0UHJldmVudGVkICYmIGhhc2ggJiYgJChoYXNoLCBkb2N1bWVudC5ib2R5KSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmhpZGUoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAndG91Y2hzdGFydCcsXG5cbiAgICAgICAgICAgIHBhc3NpdmU6IHRydWUsXG5cbiAgICAgICAgICAgIGVsKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLnBhbmVsO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcih7dGFyZ2V0VG91Y2hlc30pIHtcblxuICAgICAgICAgICAgICAgIGlmICh0YXJnZXRUb3VjaGVzLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmNsaWVudFkgPSB0YXJnZXRUb3VjaGVzWzBdLmNsaWVudFk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAndG91Y2htb3ZlJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcbiAgICAgICAgICAgIHBhc3NpdmU6IGZhbHNlLFxuXG4gICAgICAgICAgICBmaWx0ZXIoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMub3ZlcmxheTtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUuY2FuY2VsYWJsZSAmJiBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAndG91Y2htb3ZlJyxcblxuICAgICAgICAgICAgcGFzc2l2ZTogZmFsc2UsXG5cbiAgICAgICAgICAgIGVsKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLnBhbmVsO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAoZS50YXJnZXRUb3VjaGVzLmxlbmd0aCAhPT0gMSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY29uc3QgY2xpZW50WSA9IGV2ZW50LnRhcmdldFRvdWNoZXNbMF0uY2xpZW50WSAtIHRoaXMuY2xpZW50WTtcbiAgICAgICAgICAgICAgICBjb25zdCB7c2Nyb2xsVG9wLCBzY3JvbGxIZWlnaHQsIGNsaWVudEhlaWdodH0gPSB0aGlzLnBhbmVsO1xuXG4gICAgICAgICAgICAgICAgaWYgKGNsaWVudEhlaWdodCA+PSBzY3JvbGxIZWlnaHRcbiAgICAgICAgICAgICAgICAgICAgfHwgc2Nyb2xsVG9wID09PSAwICYmIGNsaWVudFkgPiAwXG4gICAgICAgICAgICAgICAgICAgIHx8IHNjcm9sbEhlaWdodCAtIHNjcm9sbFRvcCA8PSBjbGllbnRIZWlnaHQgJiYgY2xpZW50WSA8IDBcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgZS5jYW5jZWxhYmxlICYmIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdzaG93JyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICh0aGlzLm1vZGUgPT09ICdyZXZlYWwnICYmICFoYXNDbGFzcyh0aGlzLnBhbmVsLnBhcmVudE5vZGUsIHRoaXMuY2xzTW9kZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgd3JhcEFsbCh0aGlzLnBhbmVsLCAnPGRpdj4nKTtcbiAgICAgICAgICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy5wYW5lbC5wYXJlbnROb2RlLCB0aGlzLmNsc01vZGUpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGNzcyhkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQsICdvdmVyZmxvd1knLCB0aGlzLm92ZXJsYXkgPyAnaGlkZGVuJyA6ICcnKTtcbiAgICAgICAgICAgICAgICBhZGRDbGFzcyhkb2N1bWVudC5ib2R5LCB0aGlzLmNsc0NvbnRhaW5lciwgdGhpcy5jbHNGbGlwKTtcbiAgICAgICAgICAgICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ3RvdWNoLWFjdGlvbicsICdwYW4teSBwaW5jaC16b29tJyk7XG4gICAgICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCAnZGlzcGxheScsICdibG9jaycpO1xuICAgICAgICAgICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCB0aGlzLmNsc092ZXJsYXkpO1xuICAgICAgICAgICAgICAgIGFkZENsYXNzKHRoaXMucGFuZWwsIHRoaXMuY2xzU2lkZWJhckFuaW1hdGlvbiwgdGhpcy5tb2RlICE9PSAncmV2ZWFsJyA/IHRoaXMuY2xzTW9kZSA6ICcnKTtcblxuICAgICAgICAgICAgICAgIGhlaWdodChkb2N1bWVudC5ib2R5KTsgLy8gZm9yY2UgcmVmbG93XG4gICAgICAgICAgICAgICAgYWRkQ2xhc3MoZG9jdW1lbnQuYm9keSwgdGhpcy5jbHNDb250YWluZXJBbmltYXRpb24pO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5jbHNDb250YWluZXJBbmltYXRpb24gJiYgc3VwcHJlc3NVc2VyU2NhbGUoKTtcblxuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAge1xuICAgICAgICAgICAgbmFtZTogJ2hpZGUnLFxuXG4gICAgICAgICAgICBzZWxmOiB0cnVlLFxuXG4gICAgICAgICAgICBoYW5kbGVyKCkge1xuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKGRvY3VtZW50LmJvZHksIHRoaXMuY2xzQ29udGFpbmVyQW5pbWF0aW9uKTtcbiAgICAgICAgICAgICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ3RvdWNoLWFjdGlvbicsICcnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcblxuICAgICAgICB7XG4gICAgICAgICAgICBuYW1lOiAnaGlkZGVuJyxcblxuICAgICAgICAgICAgc2VsZjogdHJ1ZSxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuY2xzQ29udGFpbmVyQW5pbWF0aW9uICYmIHJlc3VtZVVzZXJTY2FsZSgpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMubW9kZSA9PT0gJ3JldmVhbCcpIHtcbiAgICAgICAgICAgICAgICAgICAgdW53cmFwKHRoaXMucGFuZWwpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKHRoaXMucGFuZWwsIHRoaXMuY2xzU2lkZWJhckFuaW1hdGlvbiwgdGhpcy5jbHNNb2RlKTtcbiAgICAgICAgICAgICAgICByZW1vdmVDbGFzcyh0aGlzLiRlbCwgdGhpcy5jbHNPdmVybGF5KTtcbiAgICAgICAgICAgICAgICBjc3ModGhpcy4kZWwsICdkaXNwbGF5JywgJycpO1xuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKGRvY3VtZW50LmJvZHksIHRoaXMuY2xzQ29udGFpbmVyLCB0aGlzLmNsc0ZsaXApO1xuXG4gICAgICAgICAgICAgICAgY3NzKGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCwgJ292ZXJmbG93WScsICcnKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdzd2lwZUxlZnQgc3dpcGVSaWdodCcsXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaXNUb2dnbGVkKCkgJiYgZW5kc1dpdGgoZS50eXBlLCAnTGVmdCcpIF4gdGhpcy5mbGlwKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICBdXG5cbn07XG5cbi8vIENocm9tZSBpbiByZXNwb25zaXZlIG1vZGUgem9vbXMgcGFnZSB1cG9uIG9wZW5pbmcgb2ZmY2FudmFzXG5mdW5jdGlvbiBzdXBwcmVzc1VzZXJTY2FsZSgpIHtcbiAgICBnZXRWaWV3cG9ydCgpLmNvbnRlbnQgKz0gJyx1c2VyLXNjYWxhYmxlPTAnO1xufVxuXG5mdW5jdGlvbiByZXN1bWVVc2VyU2NhbGUoKSB7XG4gICAgY29uc3Qgdmlld3BvcnQgPSBnZXRWaWV3cG9ydCgpO1xuICAgIHZpZXdwb3J0LmNvbnRlbnQgPSB2aWV3cG9ydC5jb250ZW50LnJlcGxhY2UoLyx1c2VyLXNjYWxhYmxlPTAkLywgJycpO1xufVxuXG5mdW5jdGlvbiBnZXRWaWV3cG9ydCgpIHtcbiAgICByZXR1cm4gJCgnbWV0YVtuYW1lPVwidmlld3BvcnRcIl0nLCBkb2N1bWVudC5oZWFkKSB8fCBhcHBlbmQoZG9jdW1lbnQuaGVhZCwgJzxtZXRhIG5hbWU9XCJ2aWV3cG9ydFwiPicpO1xufVxuIiwiaW1wb3J0IENsYXNzIGZyb20gJy4uL21peGluL2NsYXNzJztcbmltcG9ydCB7Y2xvc2VzdCwgY3NzLCBoZWlnaHQsIG9mZnNldCwgdG9GbG9hdCwgdHJpZ2dlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzXSxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIHNlbENvbnRhaW5lcjogU3RyaW5nLFxuICAgICAgICBzZWxDb250ZW50OiBTdHJpbmdcbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBzZWxDb250YWluZXI6ICcudWstbW9kYWwnLFxuICAgICAgICBzZWxDb250ZW50OiAnLnVrLW1vZGFsLWRpYWxvZydcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBjb250YWluZXIoe3NlbENvbnRhaW5lcn0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIGNsb3Nlc3QoJGVsLCBzZWxDb250YWluZXIpO1xuICAgICAgICB9LFxuXG4gICAgICAgIGNvbnRlbnQoe3NlbENvbnRlbnR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiBjbG9zZXN0KCRlbCwgc2VsQ29udGVudCk7XG4gICAgICAgIH1cblxuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG4gICAgICAgIGNzcyh0aGlzLiRlbCwgJ21pbkhlaWdodCcsIDE1MCk7XG4gICAgfSxcblxuICAgIHVwZGF0ZToge1xuXG4gICAgICAgIHJlYWQoKSB7XG5cbiAgICAgICAgICAgIGlmICghdGhpcy5jb250ZW50IHx8ICF0aGlzLmNvbnRhaW5lcikge1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICBjdXJyZW50OiB0b0Zsb2F0KGNzcyh0aGlzLiRlbCwgJ21heEhlaWdodCcpKSxcbiAgICAgICAgICAgICAgICBtYXg6IE1hdGgubWF4KDE1MCwgaGVpZ2h0KHRoaXMuY29udGFpbmVyKSAtIChvZmZzZXQodGhpcy5jb250ZW50KS5oZWlnaHQgLSBoZWlnaHQodGhpcy4kZWwpKSlcbiAgICAgICAgICAgIH07XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoe2N1cnJlbnQsIG1heH0pIHtcbiAgICAgICAgICAgIGNzcyh0aGlzLiRlbCwgJ21heEhlaWdodCcsIG1heCk7XG4gICAgICAgICAgICBpZiAoTWF0aC5yb3VuZChjdXJyZW50KSAhPT0gTWF0aC5yb3VuZChtYXgpKSB7XG4gICAgICAgICAgICAgICAgdHJpZ2dlcih0aGlzLiRlbCwgJ3Jlc2l6ZScpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfVxuXG59O1xuIiwiaW1wb3J0IHthZGRDbGFzcywgRGltZW5zaW9ucywgaGVpZ2h0LCBpc1Zpc2libGUsIHdpZHRofSBmcm9tICd1aWtpdC11dGlsJztcblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgcHJvcHM6IFsnd2lkdGgnLCAnaGVpZ2h0J10sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG4gICAgICAgIGFkZENsYXNzKHRoaXMuJGVsLCAndWstcmVzcG9uc2l2ZS13aWR0aCcpO1xuICAgIH0sXG5cbiAgICB1cGRhdGU6IHtcblxuICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgcmV0dXJuIGlzVmlzaWJsZSh0aGlzLiRlbCkgJiYgdGhpcy53aWR0aCAmJiB0aGlzLmhlaWdodFxuICAgICAgICAgICAgICAgID8ge3dpZHRoOiB3aWR0aCh0aGlzLiRlbC5wYXJlbnROb2RlKSwgaGVpZ2h0OiB0aGlzLmhlaWdodH1cbiAgICAgICAgICAgICAgICA6IGZhbHNlO1xuICAgICAgICB9LFxuXG4gICAgICAgIHdyaXRlKGRpbSkge1xuICAgICAgICAgICAgaGVpZ2h0KHRoaXMuJGVsLCBEaW1lbnNpb25zLmNvbnRhaW4oe1xuICAgICAgICAgICAgICAgIGhlaWdodDogdGhpcy5oZWlnaHQsXG4gICAgICAgICAgICAgICAgd2lkdGg6IHRoaXMud2lkdGhcbiAgICAgICAgICAgIH0sIGRpbSkuaGVpZ2h0KTtcbiAgICAgICAgfSxcblxuICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgIH1cblxufTtcbiIsImltcG9ydCB7JCwgZXNjYXBlLCBzY3JvbGxJbnRvVmlldywgdHJpZ2dlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGR1cmF0aW9uOiBOdW1iZXIsXG4gICAgICAgIG9mZnNldDogTnVtYmVyXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgZHVyYXRpb246IDEwMDAsXG4gICAgICAgIG9mZnNldDogMFxuICAgIH0sXG5cbiAgICBtZXRob2RzOiB7XG5cbiAgICAgICAgc2Nyb2xsVG8oZWwpIHtcblxuICAgICAgICAgICAgZWwgPSBlbCAmJiAkKGVsKSB8fCBkb2N1bWVudC5ib2R5O1xuXG4gICAgICAgICAgICBpZiAodHJpZ2dlcih0aGlzLiRlbCwgJ2JlZm9yZXNjcm9sbCcsIFt0aGlzLCBlbF0pKSB7XG4gICAgICAgICAgICAgICAgc2Nyb2xsSW50b1ZpZXcoZWwsIHRoaXMuJHByb3BzKS50aGVuKCgpID0+XG4gICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGhpcy4kZWwsICdzY3JvbGxlZCcsIFt0aGlzLCBlbF0pXG4gICAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgZXZlbnRzOiB7XG5cbiAgICAgICAgY2xpY2soZSkge1xuXG4gICAgICAgICAgICBpZiAoZS5kZWZhdWx0UHJldmVudGVkKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB0aGlzLnNjcm9sbFRvKGVzY2FwZShkZWNvZGVVUklDb21wb25lbnQodGhpcy4kZWwuaGFzaCkpLnN1YnN0cigxKSk7XG4gICAgICAgIH1cblxuICAgIH1cblxufTtcbiIsImltcG9ydCB7JCQsIGNzcywgZGF0YSwgZmlsdGVyLCBpc0luVmlldywgUHJvbWlzZSwgdG9nZ2xlQ2xhc3MsIHRyaWdnZXJ9IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBhcmdzOiAnY2xzJyxcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGNsczogU3RyaW5nLFxuICAgICAgICB0YXJnZXQ6IFN0cmluZyxcbiAgICAgICAgaGlkZGVuOiBCb29sZWFuLFxuICAgICAgICBvZmZzZXRUb3A6IE51bWJlcixcbiAgICAgICAgb2Zmc2V0TGVmdDogTnVtYmVyLFxuICAgICAgICByZXBlYXQ6IEJvb2xlYW4sXG4gICAgICAgIGRlbGF5OiBOdW1iZXJcbiAgICB9LFxuXG4gICAgZGF0YTogKCkgPT4gKHtcbiAgICAgICAgY2xzOiBmYWxzZSxcbiAgICAgICAgdGFyZ2V0OiBmYWxzZSxcbiAgICAgICAgaGlkZGVuOiB0cnVlLFxuICAgICAgICBvZmZzZXRUb3A6IDAsXG4gICAgICAgIG9mZnNldExlZnQ6IDAsXG4gICAgICAgIHJlcGVhdDogZmFsc2UsXG4gICAgICAgIGRlbGF5OiAwLFxuICAgICAgICBpblZpZXdDbGFzczogJ3VrLXNjcm9sbHNweS1pbnZpZXcnXG4gICAgfSksXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIGVsZW1lbnRzKHt0YXJnZXR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiB0YXJnZXQgPyAkJCh0YXJnZXQsICRlbCkgOiBbJGVsXTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIHVwZGF0ZTogW1xuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgd3JpdGUoKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaGlkZGVuKSB7XG4gICAgICAgICAgICAgICAgICAgIGNzcyhmaWx0ZXIodGhpcy5lbGVtZW50cywgYDpub3QoLiR7dGhpcy5pblZpZXdDbGFzc30pYCksICd2aXNpYmlsaXR5JywgJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgcmVhZCh7dXBkYXRlfSkge1xuXG4gICAgICAgICAgICAgICAgaWYgKCF1cGRhdGUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuZWxlbWVudHMuZm9yRWFjaChlbCA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IHN0YXRlID0gZWwuX3VrU2Nyb2xsc3B5U3RhdGU7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFzdGF0ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgc3RhdGUgPSB7Y2xzOiBkYXRhKGVsLCAndWstc2Nyb2xsc3B5LWNsYXNzJykgfHwgdGhpcy5jbHN9O1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgc3RhdGUuc2hvdyA9IGlzSW5WaWV3KGVsLCB0aGlzLm9mZnNldFRvcCwgdGhpcy5vZmZzZXRMZWZ0KTtcbiAgICAgICAgICAgICAgICAgICAgZWwuX3VrU2Nyb2xsc3B5U3RhdGUgPSBzdGF0ZTtcblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICB3cml0ZShkYXRhKSB7XG5cbiAgICAgICAgICAgICAgICAvLyBMZXQgY2hpbGQgY29tcG9uZW50cyBiZSBhcHBsaWVkIGF0IGxlYXN0IG9uY2UgZmlyc3RcbiAgICAgICAgICAgICAgICBpZiAoIWRhdGEudXBkYXRlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuJGVtaXQoKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGRhdGEudXBkYXRlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVsZW1lbnRzLmZvckVhY2goZWwgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHN0YXRlID0gZWwuX3VrU2Nyb2xsc3B5U3RhdGU7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHRvZ2dsZSA9IGludmlldyA9PiB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNzcyhlbCwgJ3Zpc2liaWxpdHknLCAhaW52aWV3ICYmIHRoaXMuaGlkZGVuID8gJ2hpZGRlbicgOiAnJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKGVsLCB0aGlzLmluVmlld0NsYXNzLCBpbnZpZXcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdG9nZ2xlQ2xhc3MoZWwsIHN0YXRlLmNscyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRyaWdnZXIoZWwsIGludmlldyA/ICdpbnZpZXcnIDogJ291dHZpZXcnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc3RhdGUuaW52aWV3ID0gaW52aWV3O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLiR1cGRhdGUoZWwpO1xuXG4gICAgICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHN0YXRlLnNob3cgJiYgIXN0YXRlLmludmlldyAmJiAhc3RhdGUucXVldWVkKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHN0YXRlLnF1ZXVlZCA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEucHJvbWlzZSA9IChkYXRhLnByb21pc2UgfHwgUHJvbWlzZS5yZXNvbHZlKCkpLnRoZW4oKCkgPT5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXcgUHJvbWlzZShyZXNvbHZlID0+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQocmVzb2x2ZSwgdGhpcy5kZWxheSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgICAgICApLnRoZW4oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRvZ2dsZSh0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHN0YXRlLnF1ZXVlZCA9IGZhbHNlLCAzMDApO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICghc3RhdGUuc2hvdyAmJiBzdGF0ZS5pbnZpZXcgJiYgIXN0YXRlLnF1ZXVlZCAmJiB0aGlzLnJlcGVhdCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0b2dnbGUoZmFsc2UpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsnc2Nyb2xsJywgJ3Jlc2l6ZSddXG5cbiAgICAgICAgfVxuXG4gICAgXVxuXG59O1xuXG4iLCJpbXBvcnQgeyQkLCBhZGRDbGFzcywgY2xvc2VzdCwgZXNjYXBlLCBnZXRWaWV3cG9ydCwgaXNWaXNpYmxlLCBsYXN0LCBvZmZzZXQsIHBvc2l0aW9uLCByZW1vdmVDbGFzcywgc2Nyb2xsUGFyZW50cywgdHJpZ2dlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIHByb3BzOiB7XG4gICAgICAgIGNsczogU3RyaW5nLFxuICAgICAgICBjbG9zZXN0OiBTdHJpbmcsXG4gICAgICAgIHNjcm9sbDogQm9vbGVhbixcbiAgICAgICAgb3ZlcmZsb3c6IEJvb2xlYW4sXG4gICAgICAgIG9mZnNldDogTnVtYmVyXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgY2xzOiAndWstYWN0aXZlJyxcbiAgICAgICAgY2xvc2VzdDogZmFsc2UsXG4gICAgICAgIHNjcm9sbDogZmFsc2UsXG4gICAgICAgIG92ZXJmbG93OiB0cnVlLFxuICAgICAgICBvZmZzZXQ6IDBcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBsaW5rcyhfLCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiAkJCgnYVtocmVmXj1cIiNcIl0nLCAkZWwpLmZpbHRlcihlbCA9PiBlbC5oYXNoKTtcbiAgICAgICAgfSxcblxuICAgICAgICB0YXJnZXRzKCkge1xuICAgICAgICAgICAgcmV0dXJuICQkKHRoaXMubGlua3MubWFwKGVsID0+IGVzY2FwZShlbC5oYXNoKS5zdWJzdHIoMSkpLmpvaW4oJywnKSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgZWxlbWVudHMoe2Nsb3Nlc3Q6IHNlbGVjdG9yfSkge1xuICAgICAgICAgICAgcmV0dXJuIGNsb3Nlc3QoJCQodGhpcy50YXJnZXRzLm1hcChlbCA9PiBgW2hyZWY9XCIjJHtlbC5pZH1cIl1gKS5qb2luKCcsJykpLCBzZWxlY3RvciB8fCAnKicpO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKCkge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLnNjcm9sbCkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRjcmVhdGUoJ3Njcm9sbCcsIHRoaXMubGlua3MsIHtvZmZzZXQ6IHRoaXMub2Zmc2V0IHx8IDB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIHJlYWQoKSB7XG5cbiAgICAgICAgICAgICAgICBjb25zdCB7bGVuZ3RofSA9IHRoaXMudGFyZ2V0cztcblxuICAgICAgICAgICAgICAgIGlmICghbGVuZ3RoIHx8ICFpc1Zpc2libGUodGhpcy4kZWwpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb25zdCBzY3JvbGxFbGVtZW50ID0gbGFzdChzY3JvbGxQYXJlbnRzKHRoaXMudGFyZ2V0c1swXSkpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHtzY3JvbGxUb3AsIHNjcm9sbEhlaWdodH0gPSBzY3JvbGxFbGVtZW50O1xuICAgICAgICAgICAgICAgIGNvbnN0IHZpZXdwb3J0ID0gZ2V0Vmlld3BvcnQoc2Nyb2xsRWxlbWVudCk7XG4gICAgICAgICAgICAgICAgY29uc3Qgc2Nyb2xsID0gc2Nyb2xsVG9wO1xuICAgICAgICAgICAgICAgIGNvbnN0IG1heCA9IHNjcm9sbEhlaWdodCAtIG9mZnNldCh2aWV3cG9ydCkuaGVpZ2h0O1xuICAgICAgICAgICAgICAgIGxldCBhY3RpdmUgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgIGlmIChzY3JvbGwgPT09IG1heCkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSBsZW5ndGggLSAxO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy50YXJnZXRzLmV2ZXJ5KChlbCwgaSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3Qge3RvcH0gPSBwb3NpdGlvbihlbCwgdmlld3BvcnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHRvcCAtIHRoaXMub2Zmc2V0IDw9IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSBpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoYWN0aXZlID09PSBmYWxzZSAmJiB0aGlzLm92ZXJmbG93KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSAwO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIHthY3RpdmV9O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgd3JpdGUoe2FjdGl2ZX0pIHtcblxuICAgICAgICAgICAgICAgIHRoaXMubGlua3MuZm9yRWFjaChlbCA9PiBlbC5ibHVyKCkpO1xuICAgICAgICAgICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuZWxlbWVudHMsIHRoaXMuY2xzKTtcblxuICAgICAgICAgICAgICAgIGlmIChhY3RpdmUgIT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGhpcy4kZWwsICdhY3RpdmUnLCBbYWN0aXZlLCBhZGRDbGFzcyh0aGlzLmVsZW1lbnRzW2FjdGl2ZV0sIHRoaXMuY2xzKV0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZXZlbnRzOiBbJ3Njcm9sbCcsICdyZXNpemUnXVxuXG4gICAgICAgIH1cblxuICAgIF1cblxufTtcbiIsImltcG9ydCBDbGFzcyBmcm9tICcuLi9taXhpbi9jbGFzcyc7XG5pbXBvcnQgTWVkaWEgZnJvbSAnLi4vbWl4aW4vbWVkaWEnO1xuaW1wb3J0IHskLCBhZGRDbGFzcywgYWZ0ZXIsIEFuaW1hdGlvbiwgYXNzaWduLCBhdHRyLCBjc3MsIGZhc3Rkb20sIGhhc0NsYXNzLCBpc051bWVyaWMsIGlzU3RyaW5nLCBpc1Zpc2libGUsIG5vb3AsIG9mZnNldCwgb2Zmc2V0UG9zaXRpb24sIHF1ZXJ5LCByZW1vdmUsIHJlbW92ZUNsYXNzLCByZXBsYWNlQ2xhc3MsIHNjcm9sbFRvcCwgdG9GbG9hdCwgdG9nZ2xlQ2xhc3MsIHRvUHgsIHRyaWdnZXIsIHdpdGhpbn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzLCBNZWRpYV0sXG5cbiAgICBwcm9wczoge1xuICAgICAgICB0b3A6IG51bGwsXG4gICAgICAgIGJvdHRvbTogQm9vbGVhbixcbiAgICAgICAgb2Zmc2V0OiBTdHJpbmcsXG4gICAgICAgIGFuaW1hdGlvbjogU3RyaW5nLFxuICAgICAgICBjbHNBY3RpdmU6IFN0cmluZyxcbiAgICAgICAgY2xzSW5hY3RpdmU6IFN0cmluZyxcbiAgICAgICAgY2xzRml4ZWQ6IFN0cmluZyxcbiAgICAgICAgY2xzQmVsb3c6IFN0cmluZyxcbiAgICAgICAgc2VsVGFyZ2V0OiBTdHJpbmcsXG4gICAgICAgIHdpZHRoRWxlbWVudDogQm9vbGVhbixcbiAgICAgICAgc2hvd09uVXA6IEJvb2xlYW4sXG4gICAgICAgIHRhcmdldE9mZnNldDogTnVtYmVyXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgdG9wOiAwLFxuICAgICAgICBib3R0b206IGZhbHNlLFxuICAgICAgICBvZmZzZXQ6IDAsXG4gICAgICAgIGFuaW1hdGlvbjogJycsXG4gICAgICAgIGNsc0FjdGl2ZTogJ3VrLWFjdGl2ZScsXG4gICAgICAgIGNsc0luYWN0aXZlOiAnJyxcbiAgICAgICAgY2xzRml4ZWQ6ICd1ay1zdGlja3ktZml4ZWQnLFxuICAgICAgICBjbHNCZWxvdzogJ3VrLXN0aWNreS1iZWxvdycsXG4gICAgICAgIHNlbFRhcmdldDogJycsXG4gICAgICAgIHdpZHRoRWxlbWVudDogZmFsc2UsXG4gICAgICAgIHNob3dPblVwOiBmYWxzZSxcbiAgICAgICAgdGFyZ2V0T2Zmc2V0OiBmYWxzZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIG9mZnNldCh7b2Zmc2V0fSkge1xuICAgICAgICAgICAgcmV0dXJuIHRvUHgob2Zmc2V0KTtcbiAgICAgICAgfSxcblxuICAgICAgICBzZWxUYXJnZXQoe3NlbFRhcmdldH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHNlbFRhcmdldCAmJiAkKHNlbFRhcmdldCwgJGVsKSB8fCAkZWw7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd2lkdGhFbGVtZW50KHt3aWR0aEVsZW1lbnR9LCAkZWwpIHtcbiAgICAgICAgICAgIHJldHVybiBxdWVyeSh3aWR0aEVsZW1lbnQsICRlbCkgfHwgdGhpcy5wbGFjZWhvbGRlcjtcbiAgICAgICAgfSxcblxuICAgICAgICBpc0FjdGl2ZToge1xuXG4gICAgICAgICAgICBnZXQoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGhhc0NsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0FjdGl2ZSk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBzZXQodmFsdWUpIHtcbiAgICAgICAgICAgICAgICBpZiAodmFsdWUgJiYgIXRoaXMuaXNBY3RpdmUpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVwbGFjZUNsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0luYWN0aXZlLCB0aGlzLmNsc0FjdGl2ZSk7XG4gICAgICAgICAgICAgICAgICAgIHRyaWdnZXIodGhpcy4kZWwsICdhY3RpdmUnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCF2YWx1ZSAmJiAhaGFzQ2xhc3ModGhpcy5zZWxUYXJnZXQsIHRoaXMuY2xzSW5hY3RpdmUpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJlcGxhY2VDbGFzcyh0aGlzLnNlbFRhcmdldCwgdGhpcy5jbHNBY3RpdmUsIHRoaXMuY2xzSW5hY3RpdmUpO1xuICAgICAgICAgICAgICAgICAgICB0cmlnZ2VyKHRoaXMuJGVsLCAnaW5hY3RpdmUnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgdGhpcy5wbGFjZWhvbGRlciA9ICQoJysgLnVrLXN0aWNreS1wbGFjZWhvbGRlcicsIHRoaXMuJGVsKSB8fCAkKCc8ZGl2IGNsYXNzPVwidWstc3RpY2t5LXBsYWNlaG9sZGVyXCI+PC9kaXY+Jyk7XG4gICAgICAgIHRoaXMuaXNGaXhlZCA9IGZhbHNlO1xuICAgICAgICB0aGlzLmlzQWN0aXZlID0gZmFsc2U7XG4gICAgfSxcblxuICAgIGRpc2Nvbm5lY3RlZCgpIHtcblxuICAgICAgICBpZiAodGhpcy5pc0ZpeGVkKSB7XG4gICAgICAgICAgICB0aGlzLmhpZGUoKTtcbiAgICAgICAgICAgIHJlbW92ZUNsYXNzKHRoaXMuc2VsVGFyZ2V0LCB0aGlzLmNsc0luYWN0aXZlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJlbW92ZSh0aGlzLnBsYWNlaG9sZGVyKTtcbiAgICAgICAgdGhpcy5wbGFjZWhvbGRlciA9IG51bGw7XG4gICAgICAgIHRoaXMud2lkdGhFbGVtZW50ID0gbnVsbDtcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnbG9hZCBoYXNoY2hhbmdlIHBvcHN0YXRlJyxcblxuICAgICAgICAgICAgZWw6IHdpbmRvdyxcblxuICAgICAgICAgICAgaGFuZGxlcigpIHtcblxuICAgICAgICAgICAgICAgIGlmICghKHRoaXMudGFyZ2V0T2Zmc2V0ICE9PSBmYWxzZSAmJiBsb2NhdGlvbi5oYXNoICYmIHdpbmRvdy5wYWdlWU9mZnNldCA+IDApKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBjb25zdCB0YXJnZXQgPSAkKGxvY2F0aW9uLmhhc2gpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRhcmdldCkge1xuICAgICAgICAgICAgICAgICAgICBmYXN0ZG9tLnJlYWQoKCkgPT4ge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCB7dG9wfSA9IG9mZnNldCh0YXJnZXQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgZWxUb3AgPSBvZmZzZXQodGhpcy4kZWwpLnRvcDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGVsSGVpZ2h0ID0gdGhpcy4kZWwub2Zmc2V0SGVpZ2h0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5pc0ZpeGVkICYmIGVsVG9wICsgZWxIZWlnaHQgPj0gdG9wICYmIGVsVG9wIDw9IHRvcCArIHRhcmdldC5vZmZzZXRIZWlnaHQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JvbGxUb3Aod2luZG93LCB0b3AgLSBlbEhlaWdodCAtIChpc051bWVyaWModGhpcy50YXJnZXRPZmZzZXQpID8gdGhpcy50YXJnZXRPZmZzZXQgOiAwKSAtIHRoaXMub2Zmc2V0KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgdXBkYXRlOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICByZWFkKHtoZWlnaHR9LCB0eXBlKSB7XG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc0FjdGl2ZSAmJiB0eXBlICE9PSAndXBkYXRlJykge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICBoZWlnaHQgPSB0aGlzLiRlbC5vZmZzZXRIZWlnaHQ7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvdygpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaGVpZ2h0ID0gIXRoaXMuaXNBY3RpdmUgPyB0aGlzLiRlbC5vZmZzZXRIZWlnaHQgOiBoZWlnaHQ7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnRvcE9mZnNldCA9IG9mZnNldCh0aGlzLmlzRml4ZWQgPyB0aGlzLnBsYWNlaG9sZGVyIDogdGhpcy4kZWwpLnRvcDtcbiAgICAgICAgICAgICAgICB0aGlzLmJvdHRvbU9mZnNldCA9IHRoaXMudG9wT2Zmc2V0ICsgaGVpZ2h0O1xuXG4gICAgICAgICAgICAgICAgY29uc3QgYm90dG9tID0gcGFyc2VQcm9wKCdib3R0b20nLCB0aGlzKTtcblxuICAgICAgICAgICAgICAgIHRoaXMudG9wID0gTWF0aC5tYXgodG9GbG9hdChwYXJzZVByb3AoJ3RvcCcsIHRoaXMpKSwgdGhpcy50b3BPZmZzZXQpIC0gdGhpcy5vZmZzZXQ7XG4gICAgICAgICAgICAgICAgdGhpcy5ib3R0b20gPSBib3R0b20gJiYgYm90dG9tIC0gaGVpZ2h0O1xuICAgICAgICAgICAgICAgIHRoaXMuaW5hY3RpdmUgPSAhdGhpcy5tYXRjaE1lZGlhO1xuXG4gICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgbGFzdFNjcm9sbDogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodCxcbiAgICAgICAgICAgICAgICAgICAgbWFyZ2luczogY3NzKHRoaXMuJGVsLCBbJ21hcmdpblRvcCcsICdtYXJnaW5Cb3R0b20nLCAnbWFyZ2luTGVmdCcsICdtYXJnaW5SaWdodCddKVxuICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICB3cml0ZSh7aGVpZ2h0LCBtYXJnaW5zfSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge3BsYWNlaG9sZGVyfSA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICBjc3MocGxhY2Vob2xkZXIsIGFzc2lnbih7aGVpZ2h0fSwgbWFyZ2lucykpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCF3aXRoaW4ocGxhY2Vob2xkZXIsIGRvY3VtZW50KSkge1xuICAgICAgICAgICAgICAgICAgICBhZnRlcih0aGlzLiRlbCwgcGxhY2Vob2xkZXIpO1xuICAgICAgICAgICAgICAgICAgICBhdHRyKHBsYWNlaG9sZGVyLCAnaGlkZGVuJywgJycpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIGVuc3VyZSBhY3RpdmUvaW5hY3RpdmUgY2xhc3NlcyBhcmUgYXBwbGllZFxuICAgICAgICAgICAgICAgIHRoaXMuaXNBY3RpdmUgPSB0aGlzLmlzQWN0aXZlO1xuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJ11cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgcmVhZCh7c2Nyb2xsID0gMH0pIHtcblxuICAgICAgICAgICAgICAgIHRoaXMud2lkdGggPSAoaXNWaXNpYmxlKHRoaXMud2lkdGhFbGVtZW50KSA/IHRoaXMud2lkdGhFbGVtZW50IDogdGhpcy4kZWwpLm9mZnNldFdpZHRoO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5zY3JvbGwgPSB3aW5kb3cucGFnZVlPZmZzZXQ7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICBkaXI6IHNjcm9sbCA8PSB0aGlzLnNjcm9sbCA/ICdkb3duJyA6ICd1cCcsXG4gICAgICAgICAgICAgICAgICAgIHNjcm9sbDogdGhpcy5zY3JvbGwsXG4gICAgICAgICAgICAgICAgICAgIHZpc2libGU6IGlzVmlzaWJsZSh0aGlzLiRlbCksXG4gICAgICAgICAgICAgICAgICAgIHRvcDogb2Zmc2V0UG9zaXRpb24odGhpcy5wbGFjZWhvbGRlcilbMF1cbiAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgd3JpdGUoZGF0YSwgdHlwZSkge1xuXG4gICAgICAgICAgICAgICAgY29uc3Qge2luaXRUaW1lc3RhbXAgPSAwLCBkaXIsIGxhc3REaXIsIGxhc3RTY3JvbGwsIHNjcm9sbCwgdG9wLCB2aXNpYmxlfSA9IGRhdGE7XG4gICAgICAgICAgICAgICAgY29uc3Qgbm93ID0gcGVyZm9ybWFuY2Uubm93KCk7XG5cbiAgICAgICAgICAgICAgICBkYXRhLmxhc3RTY3JvbGwgPSBzY3JvbGw7XG5cbiAgICAgICAgICAgICAgICBpZiAoc2Nyb2xsIDwgMCB8fCBzY3JvbGwgPT09IGxhc3RTY3JvbGwgfHwgIXZpc2libGUgfHwgdGhpcy5kaXNhYmxlZCB8fCB0aGlzLnNob3dPblVwICYmIHR5cGUgIT09ICdzY3JvbGwnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAobm93IC0gaW5pdFRpbWVzdGFtcCA+IDMwMCB8fCBkaXIgIT09IGxhc3REaXIpIHtcbiAgICAgICAgICAgICAgICAgICAgZGF0YS5pbml0U2Nyb2xsID0gc2Nyb2xsO1xuICAgICAgICAgICAgICAgICAgICBkYXRhLmluaXRUaW1lc3RhbXAgPSBub3c7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgZGF0YS5sYXN0RGlyID0gZGlyO1xuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuc2hvd09uVXAgJiYgTWF0aC5hYnMoZGF0YS5pbml0U2Nyb2xsIC0gc2Nyb2xsKSA8PSAzMCAmJiBNYXRoLmFicyhsYXN0U2Nyb2xsIC0gc2Nyb2xsKSA8PSAxMCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaW5hY3RpdmVcbiAgICAgICAgICAgICAgICAgICAgfHwgc2Nyb2xsIDwgdGhpcy50b3BcbiAgICAgICAgICAgICAgICAgICAgfHwgdGhpcy5zaG93T25VcCAmJiAoc2Nyb2xsIDw9IHRoaXMudG9wIHx8IGRpciA9PT0gJ2Rvd24nIHx8IGRpciA9PT0gJ3VwJyAmJiAhdGhpcy5pc0ZpeGVkICYmIHNjcm9sbCA8PSB0aGlzLmJvdHRvbU9mZnNldClcbiAgICAgICAgICAgICAgICApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIXRoaXMuaXNGaXhlZCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoQW5pbWF0aW9uLmluUHJvZ3Jlc3ModGhpcy4kZWwpICYmIHRvcCA+IHNjcm9sbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5jYW5jZWwodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB0aGlzLmlzRml4ZWQgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAodGhpcy5hbmltYXRpb24gJiYgc2Nyb2xsID4gdGhpcy50b3BPZmZzZXQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5jYW5jZWwodGhpcy4kZWwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgQW5pbWF0aW9uLm91dCh0aGlzLiRlbCwgdGhpcy5hbmltYXRpb24pLnRoZW4oKCkgPT4gdGhpcy5oaWRlKCksIG5vb3ApO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5pc0ZpeGVkKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cGRhdGUoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodGhpcy5hbmltYXRpb24pIHtcblxuICAgICAgICAgICAgICAgICAgICBBbmltYXRpb24uY2FuY2VsKHRoaXMuJGVsKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgIEFuaW1hdGlvbi5pbih0aGlzLiRlbCwgdGhpcy5hbmltYXRpb24pLmNhdGNoKG5vb3ApO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zaG93KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBldmVudHM6IFsncmVzaXplJywgJ3Njcm9sbCddXG5cbiAgICAgICAgfVxuXG4gICAgXSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBzaG93KCkge1xuXG4gICAgICAgICAgICB0aGlzLmlzRml4ZWQgPSB0cnVlO1xuICAgICAgICAgICAgdGhpcy51cGRhdGUoKTtcbiAgICAgICAgICAgIGF0dHIodGhpcy5wbGFjZWhvbGRlciwgJ2hpZGRlbicsIG51bGwpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgaGlkZSgpIHtcblxuICAgICAgICAgICAgdGhpcy5pc0FjdGl2ZSA9IGZhbHNlO1xuICAgICAgICAgICAgcmVtb3ZlQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzRml4ZWQsIHRoaXMuY2xzQmVsb3cpO1xuICAgICAgICAgICAgY3NzKHRoaXMuJGVsLCB7cG9zaXRpb246ICcnLCB0b3A6ICcnLCB3aWR0aDogJyd9KTtcbiAgICAgICAgICAgIGF0dHIodGhpcy5wbGFjZWhvbGRlciwgJ2hpZGRlbicsICcnKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIHVwZGF0ZSgpIHtcblxuICAgICAgICAgICAgY29uc3QgYWN0aXZlID0gdGhpcy50b3AgIT09IDAgfHwgdGhpcy5zY3JvbGwgPiB0aGlzLnRvcDtcbiAgICAgICAgICAgIGxldCB0b3AgPSBNYXRoLm1heCgwLCB0aGlzLm9mZnNldCk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLmJvdHRvbSAmJiB0aGlzLnNjcm9sbCA+IHRoaXMuYm90dG9tIC0gdGhpcy5vZmZzZXQpIHtcbiAgICAgICAgICAgICAgICB0b3AgPSB0aGlzLmJvdHRvbSAtIHRoaXMuc2Nyb2xsO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjc3ModGhpcy4kZWwsIHtcbiAgICAgICAgICAgICAgICBwb3NpdGlvbjogJ2ZpeGVkJyxcbiAgICAgICAgICAgICAgICB0b3A6IGAke3RvcH1weGAsXG4gICAgICAgICAgICAgICAgd2lkdGg6IHRoaXMud2lkdGhcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB0aGlzLmlzQWN0aXZlID0gYWN0aXZlO1xuICAgICAgICAgICAgdG9nZ2xlQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzQmVsb3csIHRoaXMuc2Nyb2xsID4gdGhpcy5ib3R0b21PZmZzZXQpO1xuICAgICAgICAgICAgYWRkQ2xhc3ModGhpcy4kZWwsIHRoaXMuY2xzRml4ZWQpO1xuXG4gICAgICAgIH1cblxuICAgIH1cblxufTtcblxuZnVuY3Rpb24gcGFyc2VQcm9wKHByb3AsIHskcHJvcHMsICRlbCwgW2Ake3Byb3B9T2Zmc2V0YF06IHByb3BPZmZzZXR9KSB7XG5cbiAgICBjb25zdCB2YWx1ZSA9ICRwcm9wc1twcm9wXTtcblxuICAgIGlmICghdmFsdWUpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmIChpc051bWVyaWModmFsdWUpICYmIGlzU3RyaW5nKHZhbHVlKSAmJiB2YWx1ZS5tYXRjaCgvXi0/XFxkLykpIHtcblxuICAgICAgICByZXR1cm4gcHJvcE9mZnNldCArIHRvUHgodmFsdWUpO1xuXG4gICAgfSBlbHNlIHtcblxuICAgICAgICByZXR1cm4gb2Zmc2V0KHZhbHVlID09PSB0cnVlID8gJGVsLnBhcmVudE5vZGUgOiBxdWVyeSh2YWx1ZSwgJGVsKSkuYm90dG9tO1xuXG4gICAgfVxufVxuIiwiaW1wb3J0IFRvZ2dsYWJsZSBmcm9tICcuLi9taXhpbi90b2dnbGFibGUnO1xuaW1wb3J0IHskJCwgYWRkQ2xhc3MsIGF0dHIsIGNoaWxkcmVuLCBjc3MsIGRhdGEsIGVuZHNXaXRoLCBmaWx0ZXIsIGdldEluZGV4LCBpbmRleCwgaXNFbXB0eSwgbWF0Y2hlcywgcXVlcnlBbGwsIHJlbW92ZUNsYXNzLCB3aXRoaW59IGZyb20gJ3Vpa2l0LXV0aWwnO1xuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBtaXhpbnM6IFtUb2dnbGFibGVdLFxuXG4gICAgYXJnczogJ2Nvbm5lY3QnLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgY29ubmVjdDogU3RyaW5nLFxuICAgICAgICB0b2dnbGU6IFN0cmluZyxcbiAgICAgICAgYWN0aXZlOiBOdW1iZXIsXG4gICAgICAgIHN3aXBpbmc6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBjb25uZWN0OiAnfi51ay1zd2l0Y2hlcicsXG4gICAgICAgIHRvZ2dsZTogJz4gKiA+IDpmaXJzdC1jaGlsZCcsXG4gICAgICAgIGFjdGl2ZTogMCxcbiAgICAgICAgc3dpcGluZzogdHJ1ZSxcbiAgICAgICAgY2xzOiAndWstYWN0aXZlJyxcbiAgICAgICAgY2xzQ29udGFpbmVyOiAndWstc3dpdGNoZXInLFxuICAgICAgICBhdHRySXRlbTogJ3VrLXN3aXRjaGVyLWl0ZW0nLFxuICAgICAgICBxdWV1ZWQ6IHRydWVcbiAgICB9LFxuXG4gICAgY29tcHV0ZWQ6IHtcblxuICAgICAgICBjb25uZWN0cyh7Y29ubmVjdH0sICRlbCkge1xuICAgICAgICAgICAgcmV0dXJuIHF1ZXJ5QWxsKGNvbm5lY3QsICRlbCk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgdG9nZ2xlcyh7dG9nZ2xlfSwgJGVsKSB7XG4gICAgICAgICAgICByZXR1cm4gJCQodG9nZ2xlLCAkZWwpO1xuICAgICAgICB9XG5cbiAgICB9LFxuXG4gICAgZXZlbnRzOiBbXG5cbiAgICAgICAge1xuXG4gICAgICAgICAgICBuYW1lOiAnY2xpY2snLFxuXG4gICAgICAgICAgICBkZWxlZ2F0ZSgpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gYCR7dGhpcy50b2dnbGV9Om5vdCgudWstZGlzYWJsZWQpYDtcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIGhhbmRsZXIoZSkge1xuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB0aGlzLnNob3coY2hpbGRyZW4odGhpcy4kZWwpLmZpbHRlcihlbCA9PiB3aXRoaW4oZS5jdXJyZW50LCBlbCkpWzBdKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdjbGljaycsXG5cbiAgICAgICAgICAgIGVsKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmNvbm5lY3RzO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZGVsZWdhdGUoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGBbJHt0aGlzLmF0dHJJdGVtfV0sW2RhdGEtJHt0aGlzLmF0dHJJdGVtfV1gO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvdyhkYXRhKGUuY3VycmVudCwgdGhpcy5hdHRySXRlbSkpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuXG4gICAgICAgIHtcbiAgICAgICAgICAgIG5hbWU6ICdzd2lwZVJpZ2h0IHN3aXBlTGVmdCcsXG5cbiAgICAgICAgICAgIGZpbHRlcigpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5zd2lwaW5nO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgZWwoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuY29ubmVjdHM7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKHt0eXBlfSkge1xuICAgICAgICAgICAgICAgIHRoaXMuc2hvdyhlbmRzV2l0aCh0eXBlLCAnTGVmdCcpID8gJ25leHQnIDogJ3ByZXZpb3VzJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgIF0sXG5cbiAgICB1cGRhdGUoKSB7XG5cbiAgICAgICAgdGhpcy5jb25uZWN0cy5mb3JFYWNoKGxpc3QgPT4gdGhpcy51cGRhdGVBcmlhKGxpc3QuY2hpbGRyZW4pKTtcbiAgICAgICAgY29uc3Qge2NoaWxkcmVufSA9IHRoaXMuJGVsO1xuICAgICAgICB0aGlzLnNob3coZmlsdGVyKGNoaWxkcmVuLCBgLiR7dGhpcy5jbHN9YClbMF0gfHwgY2hpbGRyZW5bdGhpcy5hY3RpdmVdIHx8IGNoaWxkcmVuWzBdKTtcblxuICAgICAgICB0aGlzLnN3aXBpbmcgJiYgY3NzKHRoaXMuY29ubmVjdHMsICd0b3VjaC1hY3Rpb24nLCAncGFuLXkgcGluY2gtem9vbScpO1xuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICBpbmRleCgpIHtcbiAgICAgICAgICAgIHJldHVybiAhaXNFbXB0eSh0aGlzLmNvbm5lY3RzKSA/IGluZGV4KGZpbHRlcih0aGlzLmNvbm5lY3RzWzBdLmNoaWxkcmVuLCBgLiR7dGhpcy5jbHN9YClbMF0pIDogLTE7XG4gICAgICAgIH0sXG5cbiAgICAgICAgc2hvdyhpdGVtKSB7XG5cbiAgICAgICAgICAgIGNvbnN0IHtjaGlsZHJlbn0gPSB0aGlzLiRlbDtcbiAgICAgICAgICAgIGNvbnN0IHtsZW5ndGh9ID0gY2hpbGRyZW47XG4gICAgICAgICAgICBjb25zdCBwcmV2ID0gdGhpcy5pbmRleCgpO1xuICAgICAgICAgICAgY29uc3QgaGFzUHJldiA9IHByZXYgPj0gMDtcbiAgICAgICAgICAgIGNvbnN0IGRpciA9IGl0ZW0gPT09ICdwcmV2aW91cycgPyAtMSA6IDE7XG5cbiAgICAgICAgICAgIGxldCB0b2dnbGUsIGFjdGl2ZSwgbmV4dCA9IGdldEluZGV4KGl0ZW0sIGNoaWxkcmVuLCBwcmV2KTtcblxuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBsZW5ndGg7IGkrKywgbmV4dCA9IChuZXh0ICsgZGlyICsgbGVuZ3RoKSAlIGxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGlmICghbWF0Y2hlcyh0aGlzLnRvZ2dsZXNbbmV4dF0sICcudWstZGlzYWJsZWQgKiwgLnVrLWRpc2FibGVkLCBbZGlzYWJsZWRdJykpIHtcbiAgICAgICAgICAgICAgICAgICAgdG9nZ2xlID0gdGhpcy50b2dnbGVzW25leHRdO1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmUgPSBjaGlsZHJlbltuZXh0XTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoIWFjdGl2ZSB8fCBwcmV2ID09PSBuZXh0KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZW1vdmVDbGFzcyhjaGlsZHJlbiwgdGhpcy5jbHMpO1xuICAgICAgICAgICAgYWRkQ2xhc3MoYWN0aXZlLCB0aGlzLmNscyk7XG4gICAgICAgICAgICBhdHRyKHRoaXMudG9nZ2xlcywgJ2FyaWEtZXhwYW5kZWQnLCBmYWxzZSk7XG4gICAgICAgICAgICBhdHRyKHRvZ2dsZSwgJ2FyaWEtZXhwYW5kZWQnLCB0cnVlKTtcblxuICAgICAgICAgICAgdGhpcy5jb25uZWN0cy5mb3JFYWNoKGxpc3QgPT4ge1xuICAgICAgICAgICAgICAgIGlmICghaGFzUHJldikge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZU5vdyhsaXN0LmNoaWxkcmVuW25leHRdKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZUVsZW1lbnQoW2xpc3QuY2hpbGRyZW5bcHJldl0sIGxpc3QuY2hpbGRyZW5bbmV4dF1dKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgU3dpdGNoZXIgZnJvbSAnLi9zd2l0Y2hlcic7XG5pbXBvcnQgQ2xhc3MgZnJvbSAnLi4vbWl4aW4vY2xhc3MnO1xuaW1wb3J0IHtoYXNDbGFzc30gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW0NsYXNzXSxcblxuICAgIGV4dGVuZHM6IFN3aXRjaGVyLFxuXG4gICAgcHJvcHM6IHtcbiAgICAgICAgbWVkaWE6IEJvb2xlYW5cbiAgICB9LFxuXG4gICAgZGF0YToge1xuICAgICAgICBtZWRpYTogOTYwLFxuICAgICAgICBhdHRySXRlbTogJ3VrLXRhYi1pdGVtJ1xuICAgIH0sXG5cbiAgICBjb25uZWN0ZWQoKSB7XG5cbiAgICAgICAgY29uc3QgY2xzID0gaGFzQ2xhc3ModGhpcy4kZWwsICd1ay10YWItbGVmdCcpXG4gICAgICAgICAgICA/ICd1ay10YWItbGVmdCdcbiAgICAgICAgICAgIDogaGFzQ2xhc3ModGhpcy4kZWwsICd1ay10YWItcmlnaHQnKVxuICAgICAgICAgICAgICAgID8gJ3VrLXRhYi1yaWdodCdcbiAgICAgICAgICAgICAgICA6IGZhbHNlO1xuXG4gICAgICAgIGlmIChjbHMpIHtcbiAgICAgICAgICAgIHRoaXMuJGNyZWF0ZSgndG9nZ2xlJywgdGhpcy4kZWwsIHtjbHMsIG1vZGU6ICdtZWRpYScsIG1lZGlhOiB0aGlzLm1lZGlhfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgTWVkaWEgZnJvbSAnLi4vbWl4aW4vbWVkaWEnO1xuaW1wb3J0IFRvZ2dsYWJsZSBmcm9tICcuLi9taXhpbi90b2dnbGFibGUnO1xuaW1wb3J0IHtjbG9zZXN0LCBoYXNDbGFzcywgaGFzVG91Y2gsIGluY2x1ZGVzLCBpc1RvdWNoLCBpc1Zpc2libGUsIG1hdGNoZXMsIHBvaW50ZXJFbnRlciwgcG9pbnRlckxlYXZlLCBxdWVyeUFsbCwgdHJpZ2dlcn0gZnJvbSAndWlraXQtdXRpbCc7XG5cbmV4cG9ydCBkZWZhdWx0IHtcblxuICAgIG1peGluczogW01lZGlhLCBUb2dnbGFibGVdLFxuXG4gICAgYXJnczogJ3RhcmdldCcsXG5cbiAgICBwcm9wczoge1xuICAgICAgICBocmVmOiBTdHJpbmcsXG4gICAgICAgIHRhcmdldDogbnVsbCxcbiAgICAgICAgbW9kZTogJ2xpc3QnXG4gICAgfSxcblxuICAgIGRhdGE6IHtcbiAgICAgICAgaHJlZjogZmFsc2UsXG4gICAgICAgIHRhcmdldDogZmFsc2UsXG4gICAgICAgIG1vZGU6ICdjbGljaycsXG4gICAgICAgIHF1ZXVlZDogdHJ1ZVxuICAgIH0sXG5cbiAgICBjb21wdXRlZDoge1xuXG4gICAgICAgIHRhcmdldCh7aHJlZiwgdGFyZ2V0fSwgJGVsKSB7XG4gICAgICAgICAgICB0YXJnZXQgPSBxdWVyeUFsbCh0YXJnZXQgfHwgaHJlZiwgJGVsKTtcbiAgICAgICAgICAgIHJldHVybiB0YXJnZXQubGVuZ3RoICYmIHRhcmdldCB8fCBbJGVsXTtcbiAgICAgICAgfVxuXG4gICAgfSxcblxuICAgIGNvbm5lY3RlZCgpIHtcbiAgICAgICAgdHJpZ2dlcih0aGlzLnRhcmdldCwgJ3VwZGF0ZWFyaWEnLCBbdGhpc10pO1xuICAgIH0sXG5cbiAgICBldmVudHM6IFtcblxuICAgICAgICB7XG5cbiAgICAgICAgICAgIG5hbWU6IGAke3BvaW50ZXJFbnRlcn0gJHtwb2ludGVyTGVhdmV9YCxcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBpbmNsdWRlcyh0aGlzLm1vZGUsICdob3ZlcicpO1xuICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgaGFuZGxlcihlKSB7XG4gICAgICAgICAgICAgICAgaWYgKCFpc1RvdWNoKGUpKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlKGB0b2dnbGUke2UudHlwZSA9PT0gcG9pbnRlckVudGVyID8gJ3Nob3cnIDogJ2hpZGUnfWApO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHtcblxuICAgICAgICAgICAgbmFtZTogJ2NsaWNrJyxcblxuICAgICAgICAgICAgZmlsdGVyKCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBpbmNsdWRlcyh0aGlzLm1vZGUsICdjbGljaycpIHx8IGhhc1RvdWNoICYmIGluY2x1ZGVzKHRoaXMubW9kZSwgJ2hvdmVyJyk7XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICBoYW5kbGVyKGUpIHtcblxuICAgICAgICAgICAgICAgIC8vIFRPRE8gYmV0dGVyIGlzVG9nZ2xlZCBoYW5kbGluZ1xuICAgICAgICAgICAgICAgIGxldCBsaW5rO1xuICAgICAgICAgICAgICAgIGlmIChjbG9zZXN0KGUudGFyZ2V0LCAnYVtocmVmPVwiI1wiXSwgYVtocmVmPVwiXCJdJylcbiAgICAgICAgICAgICAgICAgICAgfHwgKGxpbmsgPSBjbG9zZXN0KGUudGFyZ2V0LCAnYVtocmVmXScpKSAmJiAoXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmNscyAmJiAhaGFzQ2xhc3ModGhpcy50YXJnZXQsIHRoaXMuY2xzLnNwbGl0KCcgJylbMF0pXG4gICAgICAgICAgICAgICAgICAgICAgICB8fCAhaXNWaXNpYmxlKHRoaXMudGFyZ2V0KVxuICAgICAgICAgICAgICAgICAgICAgICAgfHwgbGluay5oYXNoICYmIG1hdGNoZXModGhpcy50YXJnZXQsIGxpbmsuaGFzaClcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy50b2dnbGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICBdLFxuXG4gICAgdXBkYXRlOiB7XG5cbiAgICAgICAgcmVhZCgpIHtcbiAgICAgICAgICAgIHJldHVybiBpbmNsdWRlcyh0aGlzLm1vZGUsICdtZWRpYScpICYmIHRoaXMubWVkaWFcbiAgICAgICAgICAgICAgICA/IHttYXRjaDogdGhpcy5tYXRjaE1lZGlhfVxuICAgICAgICAgICAgICAgIDogZmFsc2U7XG4gICAgICAgIH0sXG5cbiAgICAgICAgd3JpdGUoe21hdGNofSkge1xuXG4gICAgICAgICAgICBjb25zdCB0b2dnbGVkID0gdGhpcy5pc1RvZ2dsZWQodGhpcy50YXJnZXQpO1xuICAgICAgICAgICAgaWYgKG1hdGNoID8gIXRvZ2dsZWQgOiB0b2dnbGVkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy50b2dnbGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIGV2ZW50czogWydyZXNpemUnXVxuXG4gICAgfSxcblxuICAgIG1ldGhvZHM6IHtcblxuICAgICAgICB0b2dnbGUodHlwZSkge1xuICAgICAgICAgICAgaWYgKHRyaWdnZXIodGhpcy50YXJnZXQsIHR5cGUgfHwgJ3RvZ2dsZScsIFt0aGlzXSkpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnRvZ2dsZUVsZW1lbnQodGhpcy50YXJnZXQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICB9XG5cbn07XG4iLCJpbXBvcnQgVUlraXQgZnJvbSAnLi9hcGkvaW5kZXgnO1xuaW1wb3J0IENvcmUgZnJvbSAnLi9jb3JlL2NvcmUnO1xuaW1wb3J0IGJvb3QgZnJvbSAnLi9hcGkvYm9vdCc7XG5pbXBvcnQgKiBhcyBjb21wb25lbnRzIGZyb20gJy4vY29yZS9pbmRleCc7XG5pbXBvcnQge2VhY2h9IGZyb20gJy4vdXRpbC9sYW5nJztcblxuLy8gcmVnaXN0ZXIgY29tcG9uZW50c1xuZWFjaChjb21wb25lbnRzLCAoY29tcG9uZW50LCBuYW1lKSA9PlxuICAgIFVJa2l0LmNvbXBvbmVudChuYW1lLCBjb21wb25lbnQpXG4pO1xuXG4vLyBjb3JlIGZ1bmN0aW9uYWxpdHlcblVJa2l0LnVzZShDb3JlKTtcblxuYm9vdChVSWtpdCk7XG5cbmV4cG9ydCBkZWZhdWx0IFVJa2l0O1xuIl0sIm5hbWVzIjpbImNvbnN0IiwibGV0IiwiYXJndW1lbnRzIiwidGhpcyIsInNlbGVjdG9ycyIsImFwcGx5IiwiZ2V0QXJncyIsInJlbW92ZSIsImtleSIsImkiLCJkYXRhIiwiZ2V0RGF0YSIsInByb3AiLCJuYW1lIiwib2Zmc2V0IiwiZ2V0T2Zmc2V0IiwiaGVpZ2h0IiwiZ2V0SGVpZ2h0IiwiYWN0aXZlIiwiYW5pbWF0ZSIsImluc3RhbGwiLCJnZXRWaWV3cG9ydCJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7SUFBQUEsSUFBTSxZQUFZLEdBQUcsTUFBTSxDQUFDLFNBQVMsQ0FBQztJQUMvQixpREFBK0I7O0FBRXRDLElBQU8sU0FBUyxNQUFNLENBQUMsR0FBRyxFQUFFLEdBQUcsRUFBRTtRQUM3QixPQUFPLGNBQWMsQ0FBQyxJQUFJLENBQUMsR0FBRyxFQUFFLEdBQUcsQ0FBQyxDQUFDO0tBQ3hDOztJQUVEQSxJQUFNLGNBQWMsR0FBRyxFQUFFLENBQUM7SUFDMUJBLElBQU0sV0FBVyxHQUFHLG1CQUFtQixDQUFDOztBQUV4QyxJQUFPLFNBQVMsU0FBUyxDQUFDLEdBQUcsRUFBRTs7UUFFM0IsSUFBSSxFQUFFLEdBQUcsSUFBSSxjQUFjLENBQUMsRUFBRTtZQUMxQixjQUFjLENBQUMsR0FBRyxDQUFDLEdBQUcsR0FBRztpQkFDcEIsT0FBTyxDQUFDLFdBQVcsRUFBRSxPQUFPLENBQUM7aUJBQzdCLFdBQVcsRUFBRSxDQUFDO1NBQ3RCOztR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