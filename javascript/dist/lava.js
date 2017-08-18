(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

function EventEmitter() {
  this._events = this._events || {};
  this._maxListeners = this._maxListeners || undefined;
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
EventEmitter.defaultMaxListeners = 10;

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function(n) {
  if (!isNumber(n) || n < 0 || isNaN(n))
    throw TypeError('n must be a positive number');
  this._maxListeners = n;
  return this;
};

EventEmitter.prototype.emit = function(type) {
  var er, handler, len, args, i, listeners;

  if (!this._events)
    this._events = {};

  // If there is no 'error' event listener then throw.
  if (type === 'error') {
    if (!this._events.error ||
        (isObject(this._events.error) && !this._events.error.length)) {
      er = arguments[1];
      if (er instanceof Error) {
        throw er; // Unhandled 'error' event
      } else {
        // At least give some kind of context to the user
        var err = new Error('Uncaught, unspecified "error" event. (' + er + ')');
        err.context = er;
        throw err;
      }
    }
  }

  handler = this._events[type];

  if (isUndefined(handler))
    return false;

  if (isFunction(handler)) {
    switch (arguments.length) {
      // fast cases
      case 1:
        handler.call(this);
        break;
      case 2:
        handler.call(this, arguments[1]);
        break;
      case 3:
        handler.call(this, arguments[1], arguments[2]);
        break;
      // slower
      default:
        args = Array.prototype.slice.call(arguments, 1);
        handler.apply(this, args);
    }
  } else if (isObject(handler)) {
    args = Array.prototype.slice.call(arguments, 1);
    listeners = handler.slice();
    len = listeners.length;
    for (i = 0; i < len; i++)
      listeners[i].apply(this, args);
  }

  return true;
};

EventEmitter.prototype.addListener = function(type, listener) {
  var m;

  if (!isFunction(listener))
    throw TypeError('listener must be a function');

  if (!this._events)
    this._events = {};

  // To avoid recursion in the case that type === "newListener"! Before
  // adding it to the listeners, first emit "newListener".
  if (this._events.newListener)
    this.emit('newListener', type,
              isFunction(listener.listener) ?
              listener.listener : listener);

  if (!this._events[type])
    // Optimize the case of one listener. Don't need the extra array object.
    this._events[type] = listener;
  else if (isObject(this._events[type]))
    // If we've already got an array, just append.
    this._events[type].push(listener);
  else
    // Adding the second element, need to change to array.
    this._events[type] = [this._events[type], listener];

  // Check for listener leak
  if (isObject(this._events[type]) && !this._events[type].warned) {
    if (!isUndefined(this._maxListeners)) {
      m = this._maxListeners;
    } else {
      m = EventEmitter.defaultMaxListeners;
    }

    if (m && m > 0 && this._events[type].length > m) {
      this._events[type].warned = true;
      console.error('(node) warning: possible EventEmitter memory ' +
                    'leak detected. %d listeners added. ' +
                    'Use emitter.setMaxListeners() to increase limit.',
                    this._events[type].length);
      if (typeof console.trace === 'function') {
        // not supported in IE 10
        console.trace();
      }
    }
  }

  return this;
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.once = function(type, listener) {
  if (!isFunction(listener))
    throw TypeError('listener must be a function');

  var fired = false;

  function g() {
    this.removeListener(type, g);

    if (!fired) {
      fired = true;
      listener.apply(this, arguments);
    }
  }

  g.listener = listener;
  this.on(type, g);

  return this;
};

// emits a 'removeListener' event iff the listener was removed
EventEmitter.prototype.removeListener = function(type, listener) {
  var list, position, length, i;

  if (!isFunction(listener))
    throw TypeError('listener must be a function');

  if (!this._events || !this._events[type])
    return this;

  list = this._events[type];
  length = list.length;
  position = -1;

  if (list === listener ||
      (isFunction(list.listener) && list.listener === listener)) {
    delete this._events[type];
    if (this._events.removeListener)
      this.emit('removeListener', type, listener);

  } else if (isObject(list)) {
    for (i = length; i-- > 0;) {
      if (list[i] === listener ||
          (list[i].listener && list[i].listener === listener)) {
        position = i;
        break;
      }
    }

    if (position < 0)
      return this;

    if (list.length === 1) {
      list.length = 0;
      delete this._events[type];
    } else {
      list.splice(position, 1);
    }

    if (this._events.removeListener)
      this.emit('removeListener', type, listener);
  }

  return this;
};

EventEmitter.prototype.removeAllListeners = function(type) {
  var key, listeners;

  if (!this._events)
    return this;

  // not listening for removeListener, no need to emit
  if (!this._events.removeListener) {
    if (arguments.length === 0)
      this._events = {};
    else if (this._events[type])
      delete this._events[type];
    return this;
  }

  // emit removeListener for all listeners on all events
  if (arguments.length === 0) {
    for (key in this._events) {
      if (key === 'removeListener') continue;
      this.removeAllListeners(key);
    }
    this.removeAllListeners('removeListener');
    this._events = {};
    return this;
  }

  listeners = this._events[type];

  if (isFunction(listeners)) {
    this.removeListener(type, listeners);
  } else if (listeners) {
    // LIFO order
    while (listeners.length)
      this.removeListener(type, listeners[listeners.length - 1]);
  }
  delete this._events[type];

  return this;
};

EventEmitter.prototype.listeners = function(type) {
  var ret;
  if (!this._events || !this._events[type])
    ret = [];
  else if (isFunction(this._events[type]))
    ret = [this._events[type]];
  else
    ret = this._events[type].slice();
  return ret;
};

EventEmitter.prototype.listenerCount = function(type) {
  if (this._events) {
    var evlistener = this._events[type];

    if (isFunction(evlistener))
      return 1;
    else if (evlistener)
      return evlistener.length;
  }
  return 0;
};

EventEmitter.listenerCount = function(emitter, type) {
  return emitter.listenerCount(type);
};

function isFunction(arg) {
  return typeof arg === 'function';
}

function isNumber(arg) {
  return typeof arg === 'number';
}

function isObject(arg) {
  return typeof arg === 'object' && arg !== null;
}

function isUndefined(arg) {
  return arg === void 0;
}

},{}],2:[function(require,module,exports){
var root = require('./_root');

/** Built-in value references. */
var Symbol = root.Symbol;

module.exports = Symbol;

},{"./_root":20}],3:[function(require,module,exports){
var baseTimes = require('./_baseTimes'),
    isArguments = require('./isArguments'),
    isArray = require('./isArray'),
    isBuffer = require('./isBuffer'),
    isIndex = require('./_isIndex'),
    isTypedArray = require('./isTypedArray');

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Creates an array of the enumerable property names of the array-like `value`.
 *
 * @private
 * @param {*} value The value to query.
 * @param {boolean} inherited Specify returning inherited property names.
 * @returns {Array} Returns the array of property names.
 */
function arrayLikeKeys(value, inherited) {
  var isArr = isArray(value),
      isArg = !isArr && isArguments(value),
      isBuff = !isArr && !isArg && isBuffer(value),
      isType = !isArr && !isArg && !isBuff && isTypedArray(value),
      skipIndexes = isArr || isArg || isBuff || isType,
      result = skipIndexes ? baseTimes(value.length, String) : [],
      length = result.length;

  for (var key in value) {
    if ((inherited || hasOwnProperty.call(value, key)) &&
        !(skipIndexes && (
           // Safari 9 has enumerable `arguments.length` in strict mode.
           key == 'length' ||
           // Node.js 0.10 has enumerable non-index properties on buffers.
           (isBuff && (key == 'offset' || key == 'parent')) ||
           // PhantomJS 2 has enumerable non-index properties on typed arrays.
           (isType && (key == 'buffer' || key == 'byteLength' || key == 'byteOffset')) ||
           // Skip index properties.
           isIndex(key, length)
        ))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = arrayLikeKeys;

},{"./_baseTimes":9,"./_isIndex":15,"./isArguments":23,"./isArray":24,"./isBuffer":26,"./isTypedArray":31}],4:[function(require,module,exports){
var createBaseFor = require('./_createBaseFor');

/**
 * The base implementation of `baseForOwn` which iterates over `object`
 * properties returned by `keysFunc` and invokes `iteratee` for each property.
 * Iteratee functions may exit iteration early by explicitly returning `false`.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @returns {Object} Returns `object`.
 */
var baseFor = createBaseFor();

module.exports = baseFor;

},{"./_createBaseFor":12}],5:[function(require,module,exports){
var Symbol = require('./_Symbol'),
    getRawTag = require('./_getRawTag'),
    objectToString = require('./_objectToString');

/** `Object#toString` result references. */
var nullTag = '[object Null]',
    undefinedTag = '[object Undefined]';

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * The base implementation of `getTag` without fallbacks for buggy environments.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */
function baseGetTag(value) {
  if (value == null) {
    return value === undefined ? undefinedTag : nullTag;
  }
  return (symToStringTag && symToStringTag in Object(value))
    ? getRawTag(value)
    : objectToString(value);
}

module.exports = baseGetTag;

},{"./_Symbol":2,"./_getRawTag":14,"./_objectToString":19}],6:[function(require,module,exports){
var baseGetTag = require('./_baseGetTag'),
    isObjectLike = require('./isObjectLike');

/** `Object#toString` result references. */
var argsTag = '[object Arguments]';

/**
 * The base implementation of `_.isArguments`.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 */
function baseIsArguments(value) {
  return isObjectLike(value) && baseGetTag(value) == argsTag;
}

module.exports = baseIsArguments;

},{"./_baseGetTag":5,"./isObjectLike":30}],7:[function(require,module,exports){
var baseGetTag = require('./_baseGetTag'),
    isLength = require('./isLength'),
    isObjectLike = require('./isObjectLike');

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    arrayTag = '[object Array]',
    boolTag = '[object Boolean]',
    dateTag = '[object Date]',
    errorTag = '[object Error]',
    funcTag = '[object Function]',
    mapTag = '[object Map]',
    numberTag = '[object Number]',
    objectTag = '[object Object]',
    regexpTag = '[object RegExp]',
    setTag = '[object Set]',
    stringTag = '[object String]',
    weakMapTag = '[object WeakMap]';

var arrayBufferTag = '[object ArrayBuffer]',
    dataViewTag = '[object DataView]',
    float32Tag = '[object Float32Array]',
    float64Tag = '[object Float64Array]',
    int8Tag = '[object Int8Array]',
    int16Tag = '[object Int16Array]',
    int32Tag = '[object Int32Array]',
    uint8Tag = '[object Uint8Array]',
    uint8ClampedTag = '[object Uint8ClampedArray]',
    uint16Tag = '[object Uint16Array]',
    uint32Tag = '[object Uint32Array]';

/** Used to identify `toStringTag` values of typed arrays. */
var typedArrayTags = {};
typedArrayTags[float32Tag] = typedArrayTags[float64Tag] =
typedArrayTags[int8Tag] = typedArrayTags[int16Tag] =
typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] =
typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] =
typedArrayTags[uint32Tag] = true;
typedArrayTags[argsTag] = typedArrayTags[arrayTag] =
typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] =
typedArrayTags[dataViewTag] = typedArrayTags[dateTag] =
typedArrayTags[errorTag] = typedArrayTags[funcTag] =
typedArrayTags[mapTag] = typedArrayTags[numberTag] =
typedArrayTags[objectTag] = typedArrayTags[regexpTag] =
typedArrayTags[setTag] = typedArrayTags[stringTag] =
typedArrayTags[weakMapTag] = false;

/**
 * The base implementation of `_.isTypedArray` without Node.js optimizations.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 */
function baseIsTypedArray(value) {
  return isObjectLike(value) &&
    isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
}

module.exports = baseIsTypedArray;

},{"./_baseGetTag":5,"./isLength":28,"./isObjectLike":30}],8:[function(require,module,exports){
var isObject = require('./isObject'),
    isPrototype = require('./_isPrototype'),
    nativeKeysIn = require('./_nativeKeysIn');

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * The base implementation of `_.keysIn` which doesn't treat sparse arrays as dense.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */
function baseKeysIn(object) {
  if (!isObject(object)) {
    return nativeKeysIn(object);
  }
  var isProto = isPrototype(object),
      result = [];

  for (var key in object) {
    if (!(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = baseKeysIn;

},{"./_isPrototype":16,"./_nativeKeysIn":17,"./isObject":29}],9:[function(require,module,exports){
/**
 * The base implementation of `_.times` without support for iteratee shorthands
 * or max array length checks.
 *
 * @private
 * @param {number} n The number of times to invoke `iteratee`.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array} Returns the array of results.
 */
function baseTimes(n, iteratee) {
  var index = -1,
      result = Array(n);

  while (++index < n) {
    result[index] = iteratee(index);
  }
  return result;
}

module.exports = baseTimes;

},{}],10:[function(require,module,exports){
/**
 * The base implementation of `_.unary` without support for storing metadata.
 *
 * @private
 * @param {Function} func The function to cap arguments for.
 * @returns {Function} Returns the new capped function.
 */
function baseUnary(func) {
  return function(value) {
    return func(value);
  };
}

module.exports = baseUnary;

},{}],11:[function(require,module,exports){
var identity = require('./identity');

/**
 * Casts `value` to `identity` if it's not a function.
 *
 * @private
 * @param {*} value The value to inspect.
 * @returns {Function} Returns cast function.
 */
function castFunction(value) {
  return typeof value == 'function' ? value : identity;
}

module.exports = castFunction;

},{"./identity":22}],12:[function(require,module,exports){
/**
 * Creates a base function for methods like `_.forIn` and `_.forOwn`.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseFor(fromRight) {
  return function(object, iteratee, keysFunc) {
    var index = -1,
        iterable = Object(object),
        props = keysFunc(object),
        length = props.length;

    while (length--) {
      var key = props[fromRight ? length : ++index];
      if (iteratee(iterable[key], key, iterable) === false) {
        break;
      }
    }
    return object;
  };
}

module.exports = createBaseFor;

},{}],13:[function(require,module,exports){
(function (global){
/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof global == 'object' && global && global.Object === Object && global;

module.exports = freeGlobal;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],14:[function(require,module,exports){
var Symbol = require('./_Symbol');

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the raw `toStringTag`.
 */
function getRawTag(value) {
  var isOwn = hasOwnProperty.call(value, symToStringTag),
      tag = value[symToStringTag];

  try {
    value[symToStringTag] = undefined;
    var unmasked = true;
  } catch (e) {}

  var result = nativeObjectToString.call(value);
  if (unmasked) {
    if (isOwn) {
      value[symToStringTag] = tag;
    } else {
      delete value[symToStringTag];
    }
  }
  return result;
}

module.exports = getRawTag;

},{"./_Symbol":2}],15:[function(require,module,exports){
/** Used as references for various `Number` constants. */
var MAX_SAFE_INTEGER = 9007199254740991;

/** Used to detect unsigned integer values. */
var reIsUint = /^(?:0|[1-9]\d*)$/;

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  length = length == null ? MAX_SAFE_INTEGER : length;
  return !!length &&
    (typeof value == 'number' || reIsUint.test(value)) &&
    (value > -1 && value % 1 == 0 && value < length);
}

module.exports = isIndex;

},{}],16:[function(require,module,exports){
/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Checks if `value` is likely a prototype object.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a prototype, else `false`.
 */
function isPrototype(value) {
  var Ctor = value && value.constructor,
      proto = (typeof Ctor == 'function' && Ctor.prototype) || objectProto;

  return value === proto;
}

module.exports = isPrototype;

},{}],17:[function(require,module,exports){
/**
 * This function is like
 * [`Object.keys`](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
 * except that it includes inherited enumerable properties.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */
function nativeKeysIn(object) {
  var result = [];
  if (object != null) {
    for (var key in Object(object)) {
      result.push(key);
    }
  }
  return result;
}

module.exports = nativeKeysIn;

},{}],18:[function(require,module,exports){
var freeGlobal = require('./_freeGlobal');

/** Detect free variable `exports`. */
var freeExports = typeof exports == 'object' && exports && !exports.nodeType && exports;

/** Detect free variable `module`. */
var freeModule = freeExports && typeof module == 'object' && module && !module.nodeType && module;

/** Detect the popular CommonJS extension `module.exports`. */
var moduleExports = freeModule && freeModule.exports === freeExports;

/** Detect free variable `process` from Node.js. */
var freeProcess = moduleExports && freeGlobal.process;

/** Used to access faster Node.js helpers. */
var nodeUtil = (function() {
  try {
    return freeProcess && freeProcess.binding && freeProcess.binding('util');
  } catch (e) {}
}());

module.exports = nodeUtil;

},{"./_freeGlobal":13}],19:[function(require,module,exports){
/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/**
 * Converts `value` to a string using `Object.prototype.toString`.
 *
 * @private
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 */
function objectToString(value) {
  return nativeObjectToString.call(value);
}

module.exports = objectToString;

},{}],20:[function(require,module,exports){
var freeGlobal = require('./_freeGlobal');

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

module.exports = root;

},{"./_freeGlobal":13}],21:[function(require,module,exports){
var baseFor = require('./_baseFor'),
    castFunction = require('./_castFunction'),
    keysIn = require('./keysIn');

/**
 * Iterates over own and inherited enumerable string keyed properties of an
 * object and invokes `iteratee` for each property. The iteratee is invoked
 * with three arguments: (value, key, object). Iteratee functions may exit
 * iteration early by explicitly returning `false`.
 *
 * @static
 * @memberOf _
 * @since 0.3.0
 * @category Object
 * @param {Object} object The object to iterate over.
 * @param {Function} [iteratee=_.identity] The function invoked per iteration.
 * @returns {Object} Returns `object`.
 * @see _.forInRight
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.forIn(new Foo, function(value, key) {
 *   console.log(key);
 * });
 * // => Logs 'a', 'b', then 'c' (iteration order is not guaranteed).
 */
function forIn(object, iteratee) {
  return object == null
    ? object
    : baseFor(object, castFunction(iteratee), keysIn);
}

module.exports = forIn;

},{"./_baseFor":4,"./_castFunction":11,"./keysIn":32}],22:[function(require,module,exports){
/**
 * This method returns the first argument it receives.
 *
 * @static
 * @since 0.1.0
 * @memberOf _
 * @category Util
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'a': 1 };
 *
 * console.log(_.identity(object) === object);
 * // => true
 */
function identity(value) {
  return value;
}

module.exports = identity;

},{}],23:[function(require,module,exports){
var baseIsArguments = require('./_baseIsArguments'),
    isObjectLike = require('./isObjectLike');

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/** Built-in value references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/**
 * Checks if `value` is likely an `arguments` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 *  else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */
var isArguments = baseIsArguments(function() { return arguments; }()) ? baseIsArguments : function(value) {
  return isObjectLike(value) && hasOwnProperty.call(value, 'callee') &&
    !propertyIsEnumerable.call(value, 'callee');
};

module.exports = isArguments;

},{"./_baseIsArguments":6,"./isObjectLike":30}],24:[function(require,module,exports){
/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(document.body.children);
 * // => false
 *
 * _.isArray('abc');
 * // => false
 *
 * _.isArray(_.noop);
 * // => false
 */
var isArray = Array.isArray;

module.exports = isArray;

},{}],25:[function(require,module,exports){
var isFunction = require('./isFunction'),
    isLength = require('./isLength');

/**
 * Checks if `value` is array-like. A value is considered array-like if it's
 * not a function and has a `value.length` that's an integer greater than or
 * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 * @example
 *
 * _.isArrayLike([1, 2, 3]);
 * // => true
 *
 * _.isArrayLike(document.body.children);
 * // => true
 *
 * _.isArrayLike('abc');
 * // => true
 *
 * _.isArrayLike(_.noop);
 * // => false
 */
function isArrayLike(value) {
  return value != null && isLength(value.length) && !isFunction(value);
}

module.exports = isArrayLike;

},{"./isFunction":27,"./isLength":28}],26:[function(require,module,exports){
var root = require('./_root'),
    stubFalse = require('./stubFalse');

/** Detect free variable `exports`. */
var freeExports = typeof exports == 'object' && exports && !exports.nodeType && exports;

/** Detect free variable `module`. */
var freeModule = freeExports && typeof module == 'object' && module && !module.nodeType && module;

/** Detect the popular CommonJS extension `module.exports`. */
var moduleExports = freeModule && freeModule.exports === freeExports;

/** Built-in value references. */
var Buffer = moduleExports ? root.Buffer : undefined;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeIsBuffer = Buffer ? Buffer.isBuffer : undefined;

/**
 * Checks if `value` is a buffer.
 *
 * @static
 * @memberOf _
 * @since 4.3.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a buffer, else `false`.
 * @example
 *
 * _.isBuffer(new Buffer(2));
 * // => true
 *
 * _.isBuffer(new Uint8Array(2));
 * // => false
 */
var isBuffer = nativeIsBuffer || stubFalse;

module.exports = isBuffer;

},{"./_root":20,"./stubFalse":33}],27:[function(require,module,exports){
var baseGetTag = require('./_baseGetTag'),
    isObject = require('./isObject');

/** `Object#toString` result references. */
var asyncTag = '[object AsyncFunction]',
    funcTag = '[object Function]',
    genTag = '[object GeneratorFunction]',
    proxyTag = '[object Proxy]';

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a function, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  if (!isObject(value)) {
    return false;
  }
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in Safari 9 which returns 'object' for typed arrays and other constructors.
  var tag = baseGetTag(value);
  return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
}

module.exports = isFunction;

},{"./_baseGetTag":5,"./isObject":29}],28:[function(require,module,exports){
/** Used as references for various `Number` constants. */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This method is loosely based on
 * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 * @example
 *
 * _.isLength(3);
 * // => true
 *
 * _.isLength(Number.MIN_VALUE);
 * // => false
 *
 * _.isLength(Infinity);
 * // => false
 *
 * _.isLength('3');
 * // => false
 */
function isLength(value) {
  return typeof value == 'number' &&
    value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

module.exports = isLength;

},{}],29:[function(require,module,exports){
/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return value != null && (type == 'object' || type == 'function');
}

module.exports = isObject;

},{}],30:[function(require,module,exports){
/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return value != null && typeof value == 'object';
}

module.exports = isObjectLike;

},{}],31:[function(require,module,exports){
var baseIsTypedArray = require('./_baseIsTypedArray'),
    baseUnary = require('./_baseUnary'),
    nodeUtil = require('./_nodeUtil');

/* Node.js helper references. */
var nodeIsTypedArray = nodeUtil && nodeUtil.isTypedArray;

/**
 * Checks if `value` is classified as a typed array.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 * @example
 *
 * _.isTypedArray(new Uint8Array);
 * // => true
 *
 * _.isTypedArray([]);
 * // => false
 */
var isTypedArray = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;

module.exports = isTypedArray;

},{"./_baseIsTypedArray":7,"./_baseUnary":10,"./_nodeUtil":18}],32:[function(require,module,exports){
var arrayLikeKeys = require('./_arrayLikeKeys'),
    baseKeysIn = require('./_baseKeysIn'),
    isArrayLike = require('./isArrayLike');

/**
 * Creates an array of the own and inherited enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keysIn(new Foo);
 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
 */
function keysIn(object) {
  return isArrayLike(object) ? arrayLikeKeys(object, true) : baseKeysIn(object);
}

module.exports = keysIn;

},{"./_arrayLikeKeys":3,"./_baseKeysIn":8,"./isArrayLike":25}],33:[function(require,module,exports){
/**
 * This method returns `false`.
 *
 * @static
 * @memberOf _
 * @since 4.13.0
 * @category Util
 * @returns {boolean} Returns `false`.
 * @example
 *
 * _.times(2, _.stubFalse);
 * // => [false, false]
 */
function stubFalse() {
  return false;
}

module.exports = stubFalse;

},{}],34:[function(require,module,exports){
'use strict';

var _Lava = require('./lava/Lava');

var _Lava2 = _interopRequireDefault(_Lava);

var _Utils = require('./lava/Utils');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
/* jshint browser:true */
/* globals __OPTIONS__:true */

window.lava = new _Lava2.default();

/**
 * If Lava.js was loaded from Lavacharts, the __OPTIONS__
 * placeholder will be a JSON object of options that
 * were set server-side.
 */
if (typeof __OPTIONS__ !== 'undefined') {
  window.lava.options = __OPTIONS__;
}

/**
 * If Lava.js was set to auto_run then once the DOM
 * is ready, rendering will begin.
 */
if (window.lava.options.auto_run === true) {
  (0, _Utils.domLoaded)().then(function () {
    window.lava.run();
  });
}

},{"./lava/Lava":38,"./lava/Utils":41}],35:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _forIn2 = require('lodash/forIn');

var _forIn3 = _interopRequireDefault(_forIn2);

var _Renderable2 = require('./Renderable');

var _Utils = require('./Utils');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * Chart module
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @class     Chart
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @module    lava/Chart
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @author    Kevin Hill <kevinkhill@gmail.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @copyright (c) 2017, KHill Designs
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @license   MIT
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                */


/**
 * Chart class used for storing all the needed configuration for rendering.
 *
 * @typedef {Function}  Chart
 * @property {string}   label     - Label for the chart.
 * @property {string}   type      - Type of chart.
 * @property {Object}   element   - Html element in which to render the chart.
 * @property {Object}   chart     - Google chart object.
 * @property {string}   package   - Type of Google chart package to load.
 * @property {boolean}  pngOutput - Should the chart be displayed as a PNG.
 * @property {Object}   data      - Datatable for the chart.
 * @property {Object}   options   - Configuration options for the chart.
 * @property {Object}   events    - Events and callbacks to apply to the chart.
 * @property {Array}    formats   - Formatters to apply to the chart data.
 * @property {Function} render    - Renders the chart.
 * @property {Function} uuid      - Creates identification string for the chart.
 */
var Chart = function (_Renderable) {
    _inherits(Chart, _Renderable);

    /**
     * Chart Class
     *
     * This is the javascript version of a lavachart with methods for interacting with
     * the google chart and the PHP lavachart output.
     *
     * @param {object} json
     * @constructor
     */
    function Chart(json) {
        _classCallCheck(this, Chart);

        var _this = _possibleConstructorReturn(this, (Chart.__proto__ || Object.getPrototypeOf(Chart)).call(this, json));

        _this.type = json.type;
        _this.class = json.class;
        _this.formats = json.formats;

        _this.events = _typeof(json.events) === 'object' ? json.events : null;
        _this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        _this.render = function () {
            _this.setData(json.datatable);

            var ChartClass = (0, _Utils.stringToFunction)(_this.class, window);

            _this.gchart = new ChartClass(_this.element);

            if (_this.formats) {
                _this.applyFormats();
            }

            if (_this.events) {
                _this._attachEvents();
                // TODO: Idea... forward events to be listenable by the user, instead of having the user define them as a string callback.
                // lava.get('MyCoolChart').on('ready', function(data) {
                //     console.log(this);  // gChart
                // });
            }

            _this.draw();

            if (_this.pngOutput) {
                _this.drawPng();
            }
        };
        return _this;
    }

    /**
     * Draws the chart as a PNG instead of the standard SVG
     *
     * @public
     * @external "chart.getImageURI"
     * @see {@link https://developers.google.com/chart/interactive/docs/printing|Printing PNG Charts}
     */


    _createClass(Chart, [{
        key: 'drawPng',
        value: function drawPng() {
            var img = document.createElement('img');
            img.src = this.gchart.getImageURI();

            this.element.innerHTML = '';
            this.element.appendChild(img);
        }

        /**
         * Apply the formats to the DataTable
         *
         * @param {Array} formats
         * @public
         */

    }, {
        key: 'applyFormats',
        value: function applyFormats(formats) {
            if (!formats) {
                formats = this.formats;
            }

            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = formats[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var format = _step.value;

                    var formatter = new google.visualization[format.type](format.options);

                    console.log('[lava.js] Column index [' + format.index + '] formatted with:', formatter);

                    formatter.format(this.data, format.index);
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }
        }

        /**
         * Attach the defined chart event handlers.
         *
         * @private
         */

    }, {
        key: '_attachEvents',
        value: function _attachEvents() {
            var $chart = this;

            (0, _forIn3.default)(this.events, function (callback, event) {
                var context = window;
                var func = callback;

                if ((typeof callback === 'undefined' ? 'undefined' : _typeof(callback)) === 'object') {
                    context = context[callback[0]];
                    func = callback[1];
                }

                console.log('[lava.js] The "' + $chart.uuid() + '::' + event + '" event will be handled by "' + func + '" in the context', context);

                /**
                 * Set the context of "this" within the user provided callback to the
                 * chart that fired the event while providing the datatable of the chart
                 * to the callback as an argument.
                 */
                google.visualization.events.addListener($chart.gchart, event, function () {
                    var callback = context[func].bind($chart.gchart);

                    callback($chart.data);
                });
            });
        }
    }]);

    return Chart;
}(_Renderable2.Renderable);

exports.default = Chart;

},{"./Renderable":40,"./Utils":41,"lodash/forIn":21}],36:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Renderable2 = require('./Renderable');

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * Dashboard module
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @class     Dashboard
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @module    lava/Dashboard
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @author    Kevin Hill <kevinkhill@gmail.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @copyright (c) 2017, KHill Designs
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @license   MIT
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                */


/**
 * Dashboard class
 *
 * @typedef {Function}  Dashboard
 * @property {string}   label     - Label for the Dashboard.
 * @property {string}   type      - Type of visualization (Dashboard).
 * @property {Object}   element   - Html element in which to render the chart.
 * @property {string}   package   - Type of visualization package to load.
 * @property {Object}   data      - Datatable for the Dashboard.
 * @property {Object}   options   - Configuration options.
 * @property {Array}    bindings  - Chart and Control bindings.
 * @property {Function} render    - Renders the Dashboard.
 * @property {Function} uuid      - Unique identifier for the Dashboard.
 */
var Dashboard = function (_Renderable) {
    _inherits(Dashboard, _Renderable);

    function Dashboard(json) {
        _classCallCheck(this, Dashboard);

        var _this = _possibleConstructorReturn(this, (Dashboard.__proto__ || Object.getPrototypeOf(Dashboard)).call(this, json));

        _this.type = 'Dashboard';
        _this.bindings = json.bindings;

        /**
         * Any dependency on window.google must be in the render scope.
         */
        _this.render = function () {
            _this.setData(json.datatable);

            _this.gchart = new google.visualization.Dashboard(_this.element);

            _this._attachBindings();

            if (_this.events) {
                _this._attachEvents();
            }

            _this.draw();
        };
        return _this;
    }

    // @TODO: this needs to be modified for the other types of bindings.

    /**
     * Process and attach the bindings to the dashboard.
     *
     * @private
     */


    _createClass(Dashboard, [{
        key: '_attachBindings',
        value: function _attachBindings() {
            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = this.bindings[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var binding = _step.value;

                    var controlWraps = [];
                    var chartWraps = [];

                    var _iteratorNormalCompletion2 = true;
                    var _didIteratorError2 = false;
                    var _iteratorError2 = undefined;

                    try {
                        for (var _iterator2 = binding.controlWrappers[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                            var controlWrap = _step2.value;

                            controlWraps.push(new google.visualization.ControlWrapper(controlWrap));
                        }
                    } catch (err) {
                        _didIteratorError2 = true;
                        _iteratorError2 = err;
                    } finally {
                        try {
                            if (!_iteratorNormalCompletion2 && _iterator2.return) {
                                _iterator2.return();
                            }
                        } finally {
                            if (_didIteratorError2) {
                                throw _iteratorError2;
                            }
                        }
                    }

                    var _iteratorNormalCompletion3 = true;
                    var _didIteratorError3 = false;
                    var _iteratorError3 = undefined;

                    try {
                        for (var _iterator3 = binding.chartWrappers[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                            var chartWrap = _step3.value;

                            chartWraps.push(new google.visualization.ChartWrapper(chartWrap));
                        }
                    } catch (err) {
                        _didIteratorError3 = true;
                        _iteratorError3 = err;
                    } finally {
                        try {
                            if (!_iteratorNormalCompletion3 && _iterator3.return) {
                                _iterator3.return();
                            }
                        } finally {
                            if (_didIteratorError3) {
                                throw _iteratorError3;
                            }
                        }
                    }

                    this.gchart.bind(controlWraps, chartWraps);
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }
        }
    }]);

    return Dashboard;
}(_Renderable2.Renderable);

exports.default = Dashboard;

},{"./Renderable":40}],37:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/**
 * Errors module
 *
 * @module    lava/Errors
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
var LavaError = function (_Error) {
    _inherits(LavaError, _Error);

    function LavaError(message) {
        _classCallCheck(this, LavaError);

        var _this = _possibleConstructorReturn(this, (LavaError.__proto__ || Object.getPrototypeOf(LavaError)).call(this));

        _this.name = 'LavaError';
        _this.message = message || '';
        return _this;
    }

    return LavaError;
}(Error);

/**
 * InvalidCallback Error
 *
 * thrown when when anything but a function is given as a callback
 * @type {function}
 */


var InvalidCallback = exports.InvalidCallback = function (_LavaError) {
    _inherits(InvalidCallback, _LavaError);

    function InvalidCallback(callback) {
        _classCallCheck(this, InvalidCallback);

        var _this2 = _possibleConstructorReturn(this, (InvalidCallback.__proto__ || Object.getPrototypeOf(InvalidCallback)).call(this, '[lava.js] "' + (typeof callback === 'undefined' ? 'undefined' : _typeof(callback)) + '" is not a valid callback.'));

        _this2.name = 'InvalidCallback';
        return _this2;
    }

    return InvalidCallback;
}(LavaError);

/**
 * InvalidLabel Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */


var InvalidLabel = exports.InvalidLabel = function (_LavaError2) {
    _inherits(InvalidLabel, _LavaError2);

    function InvalidLabel(label) {
        _classCallCheck(this, InvalidLabel);

        var _this3 = _possibleConstructorReturn(this, (InvalidLabel.__proto__ || Object.getPrototypeOf(InvalidLabel)).call(this, '[lava.js] "' + (typeof label === 'undefined' ? 'undefined' : _typeof(label)) + '" is not a valid label.'));

        _this3.name = 'InvalidLabel';
        return _this3;
    }

    return InvalidLabel;
}(LavaError);

/**
 * ElementIdNotFound Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */


var ElementIdNotFound = exports.ElementIdNotFound = function (_LavaError3) {
    _inherits(ElementIdNotFound, _LavaError3);

    function ElementIdNotFound(elemId) {
        _classCallCheck(this, ElementIdNotFound);

        var _this4 = _possibleConstructorReturn(this, (ElementIdNotFound.__proto__ || Object.getPrototypeOf(ElementIdNotFound)).call(this, '[lava.js] DOM node where id="' + elemId + '" was not found.'));

        _this4.name = 'ElementIdNotFound';
        return _this4;
    }

    return ElementIdNotFound;
}(LavaError);

},{}],38:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _forIn2 = require('lodash/forIn');

var _forIn3 = _interopRequireDefault(_forIn2);

var _events = require('events');

var _events2 = _interopRequireDefault(_events);

var _Chart = require('./Chart');

var _Chart2 = _interopRequireDefault(_Chart);

var _Dashboard = require('./Dashboard');

var _Dashboard2 = _interopRequireDefault(_Dashboard);

var _Options = require('./Options');

var _Options2 = _interopRequireDefault(_Options);

var _Utils = require('./Utils');

var _Errors = require('./Errors');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /* jshint browser:true */
/* globals google:true */

/**
 * lava.js module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   http://opensource.org/licenses/MIT MIT
 */


/**
 * @property {string}             VERSION
 * @property {string}             GOOGLE_API_VERSION
 * @property {string}             GOOGLE_LOADER_URL
 * @property {Chart}              Chart
 * @property {Dashboard}          Dashboard
 * @property {object}             options
 * @property {function}           _readyCallback
 * @property {Array.<string>}     _packages
 * @property {Array.<Renderable>} _renderables
 */
var LavaJs = function (_EventEmitter) {
    _inherits(LavaJs, _EventEmitter);

    function LavaJs(newOptions) {
        _classCallCheck(this, LavaJs);

        /**
         * Version of the Lava.js module.
         *
         * @type {string}
         * @public
         */
        var _this = _possibleConstructorReturn(this, (LavaJs.__proto__ || Object.getPrototypeOf(LavaJs)).call(this));

        _this.VERSION = '4.0.0';

        /**
         * Version of the Google charts API to load.
         *
         * @type {string}
         * @public
         */
        _this.GOOGLE_API_VERSION = 'current';

        /**
         * Urls to Google's static loader
         *
         * @type {string}
         * @public
         */
        _this.GOOGLE_LOADER_URL = 'https://www.gstatic.com/charts/loader.js';

        /**
         * Storing the Chart module within Lava.js
         *
         * @type {Chart}
         * @public
         */
        _this.Chart = _Chart2.default;

        /**
         * Storing the Dashboard module within Lava.js
         *
         * @type {Dashboard}
         * @public
         */
        _this.Dashboard = _Dashboard2.default;

        /**
         * JSON object of config items.
         *
         * @type {Object}
         * @public
         */
        _this.options = newOptions || _Options2.default;

        /**
         * Reference to the google.visualization object.
         *
         * @type {google.visualization}
         */
        _this.visualization = null;

        /**
         * Array of visualization packages for charts and dashboards.
         *
         * @type {Array.<string>}
         * @private
         */
        _this._packages = [];

        /**
         * Array of charts and dashboards stored in the module.
         *
         * @type {Array.<Renderable>}
         * @private
         */
        _this._renderables = [];

        /**
         * Ready callback to be called when the module is finished running.
         *
         * @callback _readyCallback
         * @private
         */
        _this._readyCallback = _Utils.noop;
        return _this;
    }

    /**
     * Create a new Chart from a JSON payload.
     *
     * The JSON payload comes from the PHP Chart class.
     *
     * @public
     * @param  {object} json
     * @return {Renderable}
     */


    _createClass(LavaJs, [{
        key: 'createChart',
        value: function createChart(json) {
            console.log('Creating Chart', json);

            this._addPackages(json.packages); // TODO: move this into the store method?

            return new this.Chart(json);
        }

        /**
         * Create and store a new Chart from a JSON payload.
         *
         * @public
         * @see createChart
         * @param {object} json
         */

    }, {
        key: 'addNewChart',
        value: function addNewChart(json) {
            //TODO: rename to storeNewChart(json) ?
            this.store(this.createChart(json));
        }

        /**
         * Create a new Dashboard with a given label.
         *
         * The JSON payload comes from the PHP Dashboard class.
         *
         * @public
         * @param  {object} json
         * @return {Dashboard}
         */

    }, {
        key: 'createDashboard',
        value: function createDashboard(json) {
            console.log('Creating Dashboard', json);

            this._addPackages(json.packages);

            return new this.Dashboard(json);
        }

        /**
         * Create and store a new Dashboard from a JSON payload.
         *
         * The JSON payload comes from the PHP Dashboard class.
         *
         * @public
         * @see createDashboard
         * @param  {object} json
         * @return {Dashboard}
         */

    }, {
        key: 'addNewDashboard',
        value: function addNewDashboard(json) {
            //TODO: rename to storeNewDashboard(json) ?
            this.store(this.createDashboard(json));
        }

        /**
         * Public method for initializing google on the page.
         *
         * @public
         */

    }, {
        key: 'init',
        value: function init() {
            var _this2 = this;

            return this._loadGoogle().then(function () {
                _this2.visualization = google.visualization;
            });
        }

        /**
         * Runs the Lava.js module
         *
         * @public
         */

    }, {
        key: 'run',
        value: function run() {
            var _this3 = this;

            // const $lava = this;

            console.log('[lava.js] Running...');
            console.log('[lava.js] Loading options:', this.options);

            this._attachRedrawHandler();

            this.init().then(function () {
                console.log('[lava.js] Google is ready.');

                (0, _forIn3.default)(_this3._renderables, function (renderable) {
                    console.log('[lava.js] Rendering ' + renderable.uuid());

                    renderable.render();
                });

                console.log('[lava.js] Firing "ready" event.');
                _this3.emit('ready');

                console.log('[lava.js] Executing lava.ready(callback)');
                _this3._readyCallback();
            });
        }

        /**
         * Stores a renderable lava object within the module.
         *
         * @param {Renderable} renderable
         */

    }, {
        key: 'store',
        value: function store(renderable) {
            console.log('[lava.js] Storing ' + renderable.uuid());

            this._renderables[renderable.label] = renderable;
        }

        /**
         * Returns the LavaChart javascript objects
         *
         *
         * The LavaChart object holds all the user defined properties such as data, options, formats,
         * the GoogleChart object, and relative methods for internal use.
         *
         * The GoogleChart object is available as ".chart" from the returned LavaChart.
         * It can be used to access any of the available methods such as
         * getImageURI() or getChartLayoutInterface().
         * See https://google-developers.appspot.com/chart/interactive/docs/gallery/linechart#methods
         * for some examples relative to LineCharts.
         *
         * @public
         * @param  {string}   label
         * @param  {Function} callback
         * @throws InvalidLabel
         * @throws InvalidCallback
         * @throws RenderableNotFound
         */

    }, {
        key: 'get',
        value: function get(label, callback) {
            if (typeof callback !== 'function') {
                throw new _Errors.InvalidCallback(callback);
            }

            var renderable = this._renderables[label];

            if (!renderable) {
                throw new _Errors.RenderableNotFound(label);
            }

            callback(renderable);
        }

        /**
         * Assigns a callback for when the charts are ready to be interacted with.
         *
         * This is used to wrap calls to lava.loadData() or lava.loadOptions()
         * to protect against accessing charts that aren't loaded yet
         *
         * @public
         * @param {function} callback
         */

    }, {
        key: 'ready',
        value: function ready(callback) {
            if (typeof callback !== 'function') {
                throw new _Errors.InvalidCallback(callback);
            }

            this._readyCallback = callback;
        }

        /**
         * Loads new data into the chart and redraws.
         *
         *
         * Used with an AJAX call to a PHP method returning DataTable->toJson(),
         * a chart can be dynamically update in page, without reloads.
         *
         * @public
         * @param {string} label
         * @param {string} json
         * @param {Function} callback
         */

    }, {
        key: 'loadData',
        value: function loadData(label, json, callback) {
            if (typeof callback === 'undefined') {
                callback = _Utils.noop;
            }

            if (typeof callback !== 'function') {
                throw new _Errors.InvalidCallback(callback);
            }

            this.get(label, function (chart) {
                chart.setData(json);

                if (typeof json.formats !== 'undefined') {
                    chart.applyFormats(json.formats);
                }

                chart.draw();

                callback(chart);
            });
        }

        /**
         * Loads new options into a chart and redraws.
         *
         *
         * Used with an AJAX call, or javascript events, to load a new array of options into a chart.
         * This can be used to update a chart dynamically, without reloads.
         *
         * @public
         * @param {string} label
         * @param {string} json
         * @param {Function} callback
         */

    }, {
        key: 'loadOptions',
        value: function loadOptions(label, json, callback) {
            if (typeof callback === 'undefined') {
                callback = callback || _Utils.noop;
            }

            if (typeof callback !== 'function') {
                throw new _Errors.InvalidCallback(callback);
            }

            this.get(label, function (chart) {
                chart.setOptions(json);
                chart.draw();

                callback(chart);
            });
        }

        /**
         * Redraws all of the registered charts on screen.
         *
         * This method is attached to the window resize event with debouncing
         * to make the charts responsive to the browser resizing.
         */

    }, {
        key: 'redrawAll',
        value: function redrawAll() {
            if (this._renderables.length === 0) {
                console.log('[lava.js] Nothing to redraw.');

                return false;
            } else {
                console.log('[lava.js] Redrawing ' + this._renderables.length + ' renderables.');
            }

            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = this._renderables[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var renderable = _step.value;

                    console.log('[lava.js] Redrawing ' + renderable.uuid());

                    var redraw = renderable.draw.bind(renderable);

                    redraw();
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }

            return true;
        }

        /**
         * Aliasing google.visualization.arrayToDataTable to lava.arrayToDataTable
         *
         * @public
         * @param {Array} arr
         * @return {google.visualization.DataTable}
         */

    }, {
        key: 'arrayToDataTable',
        value: function arrayToDataTable(arr) {
            return this.visualization.arrayToDataTable(arr);
        }

        /**
         * Adds to the list of packages that Google needs to load.
         *
         * @private
         * @param {Array} packages
         * @return {Array}
         */

    }, {
        key: '_addPackages',
        value: function _addPackages(packages) {
            this._packages = this._packages.concat(packages);
        }

        /**
         * Attach a listener to the window resize event for redrawing the charts.
         *
         * @private
         */

    }, {
        key: '_attachRedrawHandler',
        value: function _attachRedrawHandler() {
            var _this4 = this;

            if (this.options.responsive === true) {
                var debounced = null;

                (0, _Utils.addEvent)(window, 'resize', function () {
                    // let redraw = this.redrawAll().bind(this);

                    clearTimeout(debounced);

                    debounced = setTimeout(function () {
                        console.log('[lava.js] Window re-sized, redrawing...');

                        // redraw();
                        _this4.redrawAll();
                    }, _this4.options.debounce_timeout);
                });
            }
        }

        /**
         * Load the Google Static Loader and resolve the promise when ready.
         *
         * @private
         */

    }, {
        key: '_loadGoogle',
        value: function _loadGoogle() {
            var _this5 = this;

            var $lava = this;

            return new Promise(function (resolve) {
                console.log('[lava.js] Resolving Google...');

                if (_this5._googleIsLoaded()) {
                    console.log('[lava.js] Static loader found, initializing window.google');

                    $lava._googleChartLoader(resolve);
                } else {
                    console.log('[lava.js] Static loader not found, appending to head');

                    $lava._addGoogleScriptToHead(resolve);
                    // This will call $lava._googleChartLoader(resolve);
                }
            });
        }

        /**
         * Check if Google's Static Loader is in page.
         *
         * @private
         * @returns {boolean}
         */

    }, {
        key: '_googleIsLoaded',
        value: function _googleIsLoaded() {
            var scripts = document.getElementsByTagName('script');

            var _iteratorNormalCompletion2 = true;
            var _didIteratorError2 = false;
            var _iteratorError2 = undefined;

            try {
                for (var _iterator2 = scripts[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                    var script = _step2.value;

                    if (script.src === this.GOOGLE_LOADER_URL) {
                        return true;
                    }
                }
            } catch (err) {
                _didIteratorError2 = true;
                _iteratorError2 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion2 && _iterator2.return) {
                        _iterator2.return();
                    }
                } finally {
                    if (_didIteratorError2) {
                        throw _iteratorError2;
                    }
                }
            }
        }

        /**
         * Runs the Google chart loader and resolves the promise.
         *
         * @private
         * @param {Promise.resolve} resolve
         */

    }, {
        key: '_googleChartLoader',
        value: function _googleChartLoader(resolve) {
            var config = {
                packages: this._packages,
                language: this.options.locale
            };

            if (this.options.maps_api_key !== '') {
                config.mapsApiKey = this.options.maps_api_key;
            }

            console.log('[lava.js] Loading Google with config:', config);

            google.charts.load(this.GOOGLE_API_VERSION, config);

            google.charts.setOnLoadCallback(resolve);
        }

        /**
         * Create a new script tag for the Google Static Loader.
         *
         * @private
         * @param {Promise.resolve} resolve
         * @returns {Element}
         */

    }, {
        key: '_addGoogleScriptToHead',
        value: function _addGoogleScriptToHead(resolve) {
            var $lava = this;
            var script = document.createElement('script');

            script.type = 'text/javascript';
            script.async = true;
            script.src = this.GOOGLE_LOADER_URL;
            script.onload = script.onreadystatechange = function (event) {
                event = event || window.event;

                if (event.type === 'load' || /loaded|complete/.test(this.readyState)) {
                    this.onload = this.onreadystatechange = null;

                    $lava._googleChartLoader(resolve);
                }
            };

            document.head.appendChild(script);
        }
    }]);

    return LavaJs;
}(_events2.default);

exports.default = LavaJs;

},{"./Chart":35,"./Dashboard":36,"./Errors":37,"./Options":39,"./Utils":41,"events":1,"lodash/forIn":21}],39:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Options module
 *
 * Default configuration options for using Lava.js as a standalone library.
 *
 * @module    lava/Options
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */

/**
 * @type {{auto_run: boolean, locale: string, timezone: string, datetime_format: string, maps_api_key: string, responsive: boolean, debounce_timeout: number}}
 */
var defaultOptions = {
  "auto_run": false,
  "locale": "en",
  "timezone": "America/Los_Angeles",
  "datetime_format": "",
  "maps_api_key": "",
  "responsive": true,
  "debounce_timeout": 250
};

exports.default = defaultOptions;

},{}],40:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.Renderable = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * Chart class used for storing all the needed configuration for rendering.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @typedef {Function}  Chart
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {string}   label     - Label for the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {string}   type      - Type of chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   element   - Html element in which to render the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   chart     - Google chart object.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {string}   package   - Type of Google chart package to load.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {boolean}  pngOutput - Should the chart be displayed as a PNG.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   data      - Datatable for the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   options   - Configuration options for the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Array}    formats   - Formatters to apply to the chart data.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   promises  - Promises used in the rendering chain.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Function} init      - Initializes the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Function} configure - Configures the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Function} render    - Renders the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Function} uuid      - Creates identification string for the chart.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @property {Object}   _errors   - Collection of errors to be thrown.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */


var _Utils = require("./Utils");

var _Errors = require("./Errors");

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
var Renderable = exports.Renderable = function () {
    /**
     * Chart Class
     *
     * This is the javascript version of a lavachart with methods for interacting with
     * the google chart and the PHP lavachart output.
     *
     * @param {object} json
     * @constructor
     */
    function Renderable(json) {
        _classCallCheck(this, Renderable);

        this.gchart = null;
        this.label = json.label;
        this.options = json.options;
        this.elementId = json.elementId;

        this.element = document.getElementById(this.elementId);

        if (!this.element) {
            throw new _Errors.ElementIdNotFound(this.elementId);
        }
    }

    /**
     * Unique identifier for the Chart.
     *
     * @return {string}
     */


    _createClass(Renderable, [{
        key: "uuid",
        value: function uuid() {
            return this.type + '::' + this.label;
        }

        /**
         * Draws the chart with the preset data and options.
         *
         * @public
         */

    }, {
        key: "draw",
        value: function draw() {
            this.gchart.draw(this.data, this.options);
        }

        /**
         * Sets the data for the chart by creating a new DataTable
         *
         * @public
         * @external "google.visualization.DataTable"
         * @see   {@link https://developers.google.com/chart/interactive/docs/reference#DataTable|DataTable Class}
         * @param {object} payload Json representation of a DataTable
         */

    }, {
        key: "setData",
        value: function setData(payload) {
            // If the payload is from JoinedDataTable::toJson(), then create
            // two new DataTables and join them with the defined options.
            if ((0, _Utils.getType)(payload.data) === 'Array') {
                this.data = google.visualization.data.join(new google.visualization.DataTable(payload.data[0]), new google.visualization.DataTable(payload.data[1]), payload.keys, payload.joinMethod, payload.dt2Columns, payload.dt2Columns);

                return;
            }

            // Since Google compiles their classes, we can't use instanceof to check since
            // it is no longer called a "DataTable" (it's "gvjs_P" but that could change...)
            if ((0, _Utils.getType)(payload.getTableProperties) === 'Function') {
                this.data = payload;

                return;
            }

            // If a DataTable#toJson() payload is received, with formatted columns,
            // then payload.data will be defined, and used as the DataTable
            if ((0, _Utils.getType)(payload.data) === 'Object') {
                payload = payload.data;
            }
            // TODO: handle formats better...

            // If we reach here, then it must be standard JSON for creating a DataTable.
            this.data = new google.visualization.DataTable(payload);
        }

        /**
         * Sets the options for the chart.
         *
         * @public
         * @param {object} options
         */

    }, {
        key: "setOptions",
        value: function setOptions(options) {
            this.options = options;
        }
    }]);

    return Renderable;
}();

},{"./Errors":37,"./Utils":41}],41:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.noop = noop;
exports.getType = getType;
exports.domLoaded = domLoaded;
exports.addEvent = addEvent;
exports.stringToFunction = stringToFunction;
/* jshint undef: true, unused: true */
/* globals document */

/**
 * Function that does nothing.
 *
 * @return {undefined}
 */
function noop() {
    return undefined;
}

/**
 * Return the type of object.
 *
 * @param {object} object
 * @return {mixed}
 */
function getType(object) {
    var type = Object.prototype.toString.call(object);

    return type.replace('[object ', '').replace(']', '');
}

/**
 * Simple Promise for the DOM to be ready.
 *
 * @return {Promise}
 */
function domLoaded() {
    return new Promise(function (resolve) {
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            resolve();
        } else {
            document.addEventListener('DOMContentLoaded', resolve);
        }
    });
}

/**
 * Method for attaching events to objects.
 *
 * Credit to Alex V.
 *
 * @link https://stackoverflow.com/users/327934/alex-v
 * @link http://stackoverflow.com/a/3150139
 * @param {object} target
 * @param {string} type
 * @param {Function} callback
 * @param {bool} eventReturn
 */
function addEvent(target, type, callback, eventReturn) {
    if (target === null || typeof target === 'undefined') {
        return;
    }

    if (target.addEventListener) {
        target.addEventListener(type, callback, !!eventReturn);
    } else if (target.attachEvent) {
        target.attachEvent("on" + type, callback);
    } else {
        target["on" + type] = callback;
    }
}

/**
 * Get a function a by its' namespaced string name with context.
 *
 * Credit to Jason Bunting
 *
 * @link https://stackoverflow.com/users/1790/jason-bunting
 * @link https://stackoverflow.com/a/359910
 * @param {string} functionName
 * @param {object} context
 * @private
 */
function stringToFunction(functionName, context) {
    var namespaces = functionName.split('.');
    var func = namespaces.pop();

    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }

    return context[func];
}

},{}]},{},[34])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJub2RlX21vZHVsZXMvZXZlbnRzL2V2ZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX1N5bWJvbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2FycmF5TGlrZUtleXMuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlRm9yLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZUdldFRhZy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc1R5cGVkQXJyYXkuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlS2V5c0luLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVRpbWVzLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVVuYXJ5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY2FzdEZ1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY3JlYXRlQmFzZUZvci5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2ZyZWVHbG9iYWwuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19nZXRSYXdUYWcuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19pc0luZGV4LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9faXNQcm90b3R5cGUuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19uYXRpdmVLZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19ub2RlVXRpbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX29iamVjdFRvU3RyaW5nLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fcm9vdC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvZm9ySW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lkZW50aXR5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheUxpa2UuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzQnVmZmVyLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0Z1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0xlbmd0aC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNPYmplY3QuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzT2JqZWN0TGlrZS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNUeXBlZEFycmF5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9rZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL3N0dWJGYWxzZS5qcyIsInNyY1xcbGF2YS5lbnRyeS5qcyIsInNyY1xcbGF2YVxcQ2hhcnQuanMiLCJzcmNcXGxhdmFcXERhc2hib2FyZC5qcyIsInNyY1xcbGF2YVxcRXJyb3JzLmpzIiwic3JjXFxsYXZhXFxMYXZhLmpzIiwic3JjXFxsYXZhXFxPcHRpb25zLmpzIiwic3JjXFxsYXZhXFxSZW5kZXJhYmxlLmpzIiwic3JjXFxsYXZhXFxVdGlscy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM5U0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDTkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNoQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM1QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzVEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3BCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDZEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQ3pCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7O0FDSkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM5Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDVEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzFCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDL0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM3QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDM0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNoQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7QUNmQTs7OztBQUNBOzs7O0FBRUE7Ozs7QUFOQTtBQUNBOztBQVNBLE9BQU8sSUFBUCxHQUFjLG9CQUFkOztBQUVBOzs7OztBQUtBLElBQUksT0FBTyxXQUFQLEtBQXVCLFdBQTNCLEVBQXdDO0FBQ3BDLFNBQU8sSUFBUCxDQUFZLE9BQVosR0FBc0IsV0FBdEI7QUFDSDs7QUFFRDs7OztBQUlBLElBQUksT0FBTyxJQUFQLENBQVksT0FBWixDQUFvQixRQUFwQixLQUFpQyxJQUFyQyxFQUEyQztBQUN2QywwQkFBWSxJQUFaLENBQWlCLFlBQU07QUFDbkIsV0FBTyxJQUFQLENBQVksR0FBWjtBQUNILEdBRkQ7QUFHSDs7Ozs7Ozs7Ozs7OztBQ3BCRDs7OztBQUNBOztBQUNBOzs7Ozs7OzsrZUFYQTs7Ozs7Ozs7Ozs7QUFhQTs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFpQnFCLEs7OztBQUVqQjs7Ozs7Ozs7O0FBU0EsbUJBQWEsSUFBYixFQUFtQjtBQUFBOztBQUFBLGtIQUNULElBRFM7O0FBR2YsY0FBSyxJQUFMLEdBQWUsS0FBSyxJQUFwQjtBQUNBLGNBQUssS0FBTCxHQUFlLEtBQUssS0FBcEI7QUFDQSxjQUFLLE9BQUwsR0FBZSxLQUFLLE9BQXBCOztBQUVBLGNBQUssTUFBTCxHQUFpQixRQUFPLEtBQUssTUFBWixNQUF1QixRQUF2QixHQUFrQyxLQUFLLE1BQXZDLEdBQWdELElBQWpFO0FBQ0EsY0FBSyxTQUFMLEdBQWlCLE9BQU8sS0FBSyxTQUFaLEtBQTBCLFdBQTFCLEdBQXdDLEtBQXhDLEdBQWdELFFBQVEsS0FBSyxTQUFiLENBQWpFOztBQUVBOzs7QUFHQSxjQUFLLE1BQUwsR0FBYyxZQUFNO0FBQ2hCLGtCQUFLLE9BQUwsQ0FBYSxLQUFLLFNBQWxCOztBQUVBLGdCQUFJLGFBQWEsNkJBQWlCLE1BQUssS0FBdEIsRUFBNkIsTUFBN0IsQ0FBakI7O0FBRUEsa0JBQUssTUFBTCxHQUFjLElBQUksVUFBSixDQUFlLE1BQUssT0FBcEIsQ0FBZDs7QUFFQSxnQkFBSSxNQUFLLE9BQVQsRUFBa0I7QUFDZCxzQkFBSyxZQUFMO0FBQ0g7O0FBRUQsZ0JBQUksTUFBSyxNQUFULEVBQWlCO0FBQ2Isc0JBQUssYUFBTDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0g7O0FBRUQsa0JBQUssSUFBTDs7QUFFQSxnQkFBSSxNQUFLLFNBQVQsRUFBb0I7QUFDaEIsc0JBQUssT0FBTDtBQUNIO0FBQ0osU0F4QkQ7QUFiZTtBQXNDbEI7O0FBRUQ7Ozs7Ozs7Ozs7O2tDQU9VO0FBQ04sZ0JBQUksTUFBTSxTQUFTLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBVjtBQUNJLGdCQUFJLEdBQUosR0FBVSxLQUFLLE1BQUwsQ0FBWSxXQUFaLEVBQVY7O0FBRUosaUJBQUssT0FBTCxDQUFhLFNBQWIsR0FBeUIsRUFBekI7QUFDQSxpQkFBSyxPQUFMLENBQWEsV0FBYixDQUF5QixHQUF6QjtBQUNIOztBQUVEOzs7Ozs7Ozs7cUNBTWEsTyxFQUFTO0FBQ2xCLGdCQUFJLENBQUUsT0FBTixFQUFlO0FBQ1gsMEJBQVUsS0FBSyxPQUFmO0FBQ0g7O0FBSGlCO0FBQUE7QUFBQTs7QUFBQTtBQUtsQixxQ0FBbUIsT0FBbkIsOEhBQTRCO0FBQUEsd0JBQW5CLE1BQW1COztBQUN4Qix3QkFBSSxZQUFZLElBQUksT0FBTyxhQUFQLENBQXFCLE9BQU8sSUFBNUIsQ0FBSixDQUFzQyxPQUFPLE9BQTdDLENBQWhCOztBQUVBLDRCQUFRLEdBQVIsOEJBQXVDLE9BQU8sS0FBOUMsd0JBQXdFLFNBQXhFOztBQUVBLDhCQUFVLE1BQVYsQ0FBaUIsS0FBSyxJQUF0QixFQUE0QixPQUFPLEtBQW5DO0FBQ0g7QUFYaUI7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQVlyQjs7QUFFRDs7Ozs7Ozs7d0NBS2dCO0FBQ1osZ0JBQUksU0FBUyxJQUFiOztBQUVBLGlDQUFPLEtBQUssTUFBWixFQUFvQixVQUFVLFFBQVYsRUFBb0IsS0FBcEIsRUFBMkI7QUFDM0Msb0JBQUksVUFBVSxNQUFkO0FBQ0Esb0JBQUksT0FBTyxRQUFYOztBQUVBLG9CQUFJLFFBQU8sUUFBUCx5Q0FBTyxRQUFQLE9BQW9CLFFBQXhCLEVBQWtDO0FBQzlCLDhCQUFVLFFBQVEsU0FBUyxDQUFULENBQVIsQ0FBVjtBQUNBLDJCQUFPLFNBQVMsQ0FBVCxDQUFQO0FBQ0g7O0FBRUQsd0JBQVEsR0FBUixxQkFBOEIsT0FBTyxJQUFQLEVBQTlCLFVBQWdELEtBQWhELG9DQUFvRixJQUFwRix1QkFBNEcsT0FBNUc7O0FBRUE7Ozs7O0FBS0EsdUJBQU8sYUFBUCxDQUFxQixNQUFyQixDQUE0QixXQUE1QixDQUF3QyxPQUFPLE1BQS9DLEVBQXVELEtBQXZELEVBQThELFlBQVc7QUFDckUsd0JBQU0sV0FBVyxRQUFRLElBQVIsRUFBYyxJQUFkLENBQW1CLE9BQU8sTUFBMUIsQ0FBakI7O0FBRUEsNkJBQVMsT0FBTyxJQUFoQjtBQUNILGlCQUpEO0FBS0gsYUFyQkQ7QUFzQkg7Ozs7OztrQkFwSGdCLEs7Ozs7Ozs7Ozs7O0FDckJyQjs7Ozs7OytlQVRBOzs7Ozs7Ozs7OztBQVdBOzs7Ozs7Ozs7Ozs7OztJQWNxQixTOzs7QUFFakIsdUJBQVksSUFBWixFQUFrQjtBQUFBOztBQUFBLDBIQUNSLElBRFE7O0FBR2QsY0FBSyxJQUFMLEdBQWdCLFdBQWhCO0FBQ0EsY0FBSyxRQUFMLEdBQWdCLEtBQUssUUFBckI7O0FBRUE7OztBQUdBLGNBQUssTUFBTCxHQUFjLFlBQU07QUFDaEIsa0JBQUssT0FBTCxDQUFhLEtBQUssU0FBbEI7O0FBRUEsa0JBQUssTUFBTCxHQUFjLElBQUksT0FBTyxhQUFQLENBQXFCLFNBQXpCLENBQW1DLE1BQUssT0FBeEMsQ0FBZDs7QUFFQSxrQkFBSyxlQUFMOztBQUVBLGdCQUFJLE1BQUssTUFBVCxFQUFpQjtBQUNiLHNCQUFLLGFBQUw7QUFDSDs7QUFFRCxrQkFBSyxJQUFMO0FBQ0gsU0FaRDtBQVRjO0FBc0JqQjs7QUFFRDs7QUFFQTs7Ozs7Ozs7OzBDQUtrQjtBQUFBO0FBQUE7QUFBQTs7QUFBQTtBQUNkLHFDQUFvQixLQUFLLFFBQXpCLDhIQUFtQztBQUFBLHdCQUExQixPQUEwQjs7QUFDL0Isd0JBQUksZUFBZSxFQUFuQjtBQUNBLHdCQUFJLGFBQWEsRUFBakI7O0FBRitCO0FBQUE7QUFBQTs7QUFBQTtBQUkvQiw4Q0FBd0IsUUFBUSxlQUFoQyxtSUFBaUQ7QUFBQSxnQ0FBeEMsV0FBd0M7O0FBQzdDLHlDQUFhLElBQWIsQ0FDSSxJQUFJLE9BQU8sYUFBUCxDQUFxQixjQUF6QixDQUF3QyxXQUF4QyxDQURKO0FBR0g7QUFSOEI7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTs7QUFBQTtBQUFBO0FBQUE7O0FBQUE7QUFVL0IsOENBQXNCLFFBQVEsYUFBOUIsbUlBQTZDO0FBQUEsZ0NBQXBDLFNBQW9DOztBQUN6Qyx1Q0FBVyxJQUFYLENBQ0ksSUFBSSxPQUFPLGFBQVAsQ0FBcUIsWUFBekIsQ0FBc0MsU0FBdEMsQ0FESjtBQUdIO0FBZDhCO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7O0FBZ0IvQix5QkFBSyxNQUFMLENBQVksSUFBWixDQUFpQixZQUFqQixFQUErQixVQUEvQjtBQUNIO0FBbEJhO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFtQmpCOzs7Ozs7a0JBcERnQixTOzs7Ozs7Ozs7Ozs7Ozs7OztBQ3pCckI7Ozs7Ozs7O0lBUU0sUzs7O0FBRUYsdUJBQWEsT0FBYixFQUFzQjtBQUFBOztBQUFBOztBQUdsQixjQUFLLElBQUwsR0FBZSxXQUFmO0FBQ0EsY0FBSyxPQUFMLEdBQWdCLFdBQVcsRUFBM0I7QUFKa0I7QUFLckI7OztFQVBtQixLOztBQVV4Qjs7Ozs7Ozs7SUFNYSxlLFdBQUEsZTs7O0FBRVQsNkJBQWEsUUFBYixFQUF1QjtBQUFBOztBQUFBLCtKQUNRLFFBRFIseUNBQ1EsUUFEUjs7QUFHbkIsZUFBSyxJQUFMLEdBQVksaUJBQVo7QUFIbUI7QUFJdEI7OztFQU5nQyxTOztBQVNyQzs7Ozs7Ozs7O0lBT2EsWSxXQUFBLFk7OztBQUVULDBCQUFhLEtBQWIsRUFBb0I7QUFBQTs7QUFBQSx5SkFDVyxLQURYLHlDQUNXLEtBRFg7O0FBRWhCLGVBQUssSUFBTCxHQUFZLGNBQVo7QUFGZ0I7QUFHbkI7OztFQUw2QixTOztBQVFsQzs7Ozs7Ozs7O0lBT2EsaUIsV0FBQSxpQjs7O0FBRVQsK0JBQWEsTUFBYixFQUFxQjtBQUFBOztBQUFBLDZLQUNxQixNQURyQjs7QUFHakIsZUFBSyxJQUFMLEdBQVksbUJBQVo7QUFIaUI7QUFJcEI7OztFQU5rQyxTOzs7Ozs7Ozs7OztBQzVDdkM7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOztBQUNBOzs7Ozs7OzsrZUFqQkE7QUFDQTs7QUFFQTs7Ozs7Ozs7OztBQWlCQTs7Ozs7Ozs7Ozs7SUFXcUIsTTs7O0FBRWpCLG9CQUFZLFVBQVosRUFBd0I7QUFBQTs7QUFHcEI7Ozs7OztBQUhvQjs7QUFTcEIsY0FBSyxPQUFMLEdBQWUsT0FBZjs7QUFFQTs7Ozs7O0FBTUEsY0FBSyxrQkFBTCxHQUEwQixTQUExQjs7QUFFQTs7Ozs7O0FBTUEsY0FBSyxpQkFBTCxHQUF5QiwwQ0FBekI7O0FBRUE7Ozs7OztBQU1BLGNBQUssS0FBTDs7QUFFQTs7Ozs7O0FBTUEsY0FBSyxTQUFMOztBQUVBOzs7Ozs7QUFNQSxjQUFLLE9BQUwsR0FBZSwrQkFBZjs7QUFFQTs7Ozs7QUFLQSxjQUFLLGFBQUwsR0FBcUIsSUFBckI7O0FBRUE7Ozs7OztBQU1BLGNBQUssU0FBTCxHQUFpQixFQUFqQjs7QUFFQTs7Ozs7O0FBTUEsY0FBSyxZQUFMLEdBQW9CLEVBQXBCOztBQUVBOzs7Ozs7QUFNQSxjQUFLLGNBQUw7QUFoRm9CO0FBaUZ2Qjs7QUFFRDs7Ozs7Ozs7Ozs7OztvQ0FTWSxJLEVBQU07QUFDZCxvQkFBUSxHQUFSLENBQVksZ0JBQVosRUFBOEIsSUFBOUI7O0FBRUEsaUJBQUssWUFBTCxDQUFrQixLQUFLLFFBQXZCLEVBSGMsQ0FHb0I7O0FBRWxDLG1CQUFPLElBQUksS0FBSyxLQUFULENBQWUsSUFBZixDQUFQO0FBQ0g7O0FBRUQ7Ozs7Ozs7Ozs7b0NBT1ksSSxFQUFNO0FBQUU7QUFDaEIsaUJBQUssS0FBTCxDQUFXLEtBQUssV0FBTCxDQUFpQixJQUFqQixDQUFYO0FBQ0g7O0FBRUQ7Ozs7Ozs7Ozs7Ozt3Q0FTZ0IsSSxFQUFNO0FBQ2xCLG9CQUFRLEdBQVIsQ0FBWSxvQkFBWixFQUFrQyxJQUFsQzs7QUFFQSxpQkFBSyxZQUFMLENBQWtCLEtBQUssUUFBdkI7O0FBRUEsbUJBQU8sSUFBSSxLQUFLLFNBQVQsQ0FBbUIsSUFBbkIsQ0FBUDtBQUNIOztBQUVEOzs7Ozs7Ozs7Ozs7O3dDQVVnQixJLEVBQU07QUFBRTtBQUNwQixpQkFBSyxLQUFMLENBQVcsS0FBSyxlQUFMLENBQXFCLElBQXJCLENBQVg7QUFDSDs7QUFFRDs7Ozs7Ozs7K0JBS087QUFBQTs7QUFDSCxtQkFBTyxLQUFLLFdBQUwsR0FBbUIsSUFBbkIsQ0FBd0IsWUFBTTtBQUNqQyx1QkFBSyxhQUFMLEdBQXFCLE9BQU8sYUFBNUI7QUFDSCxhQUZNLENBQVA7QUFHSDs7QUFFRDs7Ozs7Ozs7OEJBS007QUFBQTs7QUFDRjs7QUFFQSxvQkFBUSxHQUFSLENBQVksc0JBQVo7QUFDQSxvQkFBUSxHQUFSLENBQVksNEJBQVosRUFBMEMsS0FBSyxPQUEvQzs7QUFFQSxpQkFBSyxvQkFBTDs7QUFFQSxpQkFBSyxJQUFMLEdBQVksSUFBWixDQUFpQixZQUFNO0FBQ25CLHdCQUFRLEdBQVIsQ0FBWSw0QkFBWjs7QUFFQSxxQ0FBTyxPQUFLLFlBQVosRUFBMEIsc0JBQWM7QUFDcEMsNEJBQVEsR0FBUiwwQkFBbUMsV0FBVyxJQUFYLEVBQW5DOztBQUVBLCtCQUFXLE1BQVg7QUFDSCxpQkFKRDs7QUFNQSx3QkFBUSxHQUFSLENBQVksaUNBQVo7QUFDQSx1QkFBSyxJQUFMLENBQVUsT0FBVjs7QUFFQSx3QkFBUSxHQUFSLENBQVksMENBQVo7QUFDQSx1QkFBSyxjQUFMO0FBQ0gsYUFkRDtBQWVIOztBQUVEOzs7Ozs7Ozs4QkFLTSxVLEVBQVk7QUFDZCxvQkFBUSxHQUFSLHdCQUFpQyxXQUFXLElBQVgsRUFBakM7O0FBRUEsaUJBQUssWUFBTCxDQUFrQixXQUFXLEtBQTdCLElBQXNDLFVBQXRDO0FBQ0g7O0FBRUQ7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OzRCQW9CSSxLLEVBQU8sUSxFQUFVO0FBQ2pCLGdCQUFJLE9BQU8sUUFBUCxLQUFvQixVQUF4QixFQUFvQztBQUNoQyxzQkFBTSw0QkFBb0IsUUFBcEIsQ0FBTjtBQUNIOztBQUVELGdCQUFJLGFBQWEsS0FBSyxZQUFMLENBQWtCLEtBQWxCLENBQWpCOztBQUVBLGdCQUFJLENBQUUsVUFBTixFQUFrQjtBQUNkLHNCQUFNLCtCQUF1QixLQUF2QixDQUFOO0FBQ0g7O0FBRUQscUJBQVMsVUFBVDtBQUNIOztBQUVEOzs7Ozs7Ozs7Ozs7OEJBU00sUSxFQUFVO0FBQ1osZ0JBQUksT0FBTyxRQUFQLEtBQW9CLFVBQXhCLEVBQW9DO0FBQ2hDLHNCQUFNLDRCQUFvQixRQUFwQixDQUFOO0FBQ0g7O0FBRUQsaUJBQUssY0FBTCxHQUFzQixRQUF0QjtBQUNIOztBQUVEOzs7Ozs7Ozs7Ozs7Ozs7aUNBWVMsSyxFQUFPLEksRUFBTSxRLEVBQVU7QUFDNUIsZ0JBQUksT0FBTyxRQUFQLEtBQW9CLFdBQXhCLEVBQXFDO0FBQ2pDO0FBQ0g7O0FBRUQsZ0JBQUksT0FBTyxRQUFQLEtBQW9CLFVBQXhCLEVBQW9DO0FBQ2hDLHNCQUFNLDRCQUFvQixRQUFwQixDQUFOO0FBQ0g7O0FBRUQsaUJBQUssR0FBTCxDQUFTLEtBQVQsRUFBZ0IsVUFBVSxLQUFWLEVBQWlCO0FBQzdCLHNCQUFNLE9BQU4sQ0FBYyxJQUFkOztBQUVBLG9CQUFJLE9BQU8sS0FBSyxPQUFaLEtBQXdCLFdBQTVCLEVBQXlDO0FBQ3JDLDBCQUFNLFlBQU4sQ0FBbUIsS0FBSyxPQUF4QjtBQUNIOztBQUVELHNCQUFNLElBQU47O0FBRUEseUJBQVMsS0FBVDtBQUNILGFBVkQ7QUFXSDs7QUFFRDs7Ozs7Ozs7Ozs7Ozs7O29DQVlZLEssRUFBTyxJLEVBQU0sUSxFQUFVO0FBQy9CLGdCQUFJLE9BQU8sUUFBUCxLQUFvQixXQUF4QixFQUFxQztBQUNqQywyQkFBVyx1QkFBWDtBQUNIOztBQUVELGdCQUFJLE9BQU8sUUFBUCxLQUFvQixVQUF4QixFQUFvQztBQUNoQyxzQkFBTSw0QkFBb0IsUUFBcEIsQ0FBTjtBQUNIOztBQUVELGlCQUFLLEdBQUwsQ0FBUyxLQUFULEVBQWdCLFVBQVUsS0FBVixFQUFpQjtBQUM3QixzQkFBTSxVQUFOLENBQWlCLElBQWpCO0FBQ0Esc0JBQU0sSUFBTjs7QUFFQSx5QkFBUyxLQUFUO0FBQ0gsYUFMRDtBQU1IOztBQUVEOzs7Ozs7Ozs7b0NBTVk7QUFDUixnQkFBSSxLQUFLLFlBQUwsQ0FBa0IsTUFBbEIsS0FBNkIsQ0FBakMsRUFBb0M7QUFDaEMsd0JBQVEsR0FBUjs7QUFFQSx1QkFBTyxLQUFQO0FBQ0gsYUFKRCxNQUlPO0FBQ0gsd0JBQVEsR0FBUiwwQkFBbUMsS0FBSyxZQUFMLENBQWtCLE1BQXJEO0FBQ0g7O0FBUE87QUFBQTtBQUFBOztBQUFBO0FBU1IscUNBQXVCLEtBQUssWUFBNUIsOEhBQTBDO0FBQUEsd0JBQWpDLFVBQWlDOztBQUN0Qyw0QkFBUSxHQUFSLDBCQUFtQyxXQUFXLElBQVgsRUFBbkM7O0FBRUEsd0JBQUksU0FBUyxXQUFXLElBQVgsQ0FBZ0IsSUFBaEIsQ0FBcUIsVUFBckIsQ0FBYjs7QUFFQTtBQUNIO0FBZk87QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTs7QUFpQlIsbUJBQU8sSUFBUDtBQUNIOztBQUVEOzs7Ozs7Ozs7O3lDQU9pQixHLEVBQUs7QUFDbEIsbUJBQU8sS0FBSyxhQUFMLENBQW1CLGdCQUFuQixDQUFvQyxHQUFwQyxDQUFQO0FBQ0g7O0FBRUQ7Ozs7Ozs7Ozs7cUNBT2EsUSxFQUFVO0FBQ25CLGlCQUFLLFNBQUwsR0FBaUIsS0FBSyxTQUFMLENBQWUsTUFBZixDQUFzQixRQUF0QixDQUFqQjtBQUNIOztBQUVEOzs7Ozs7OzsrQ0FLdUI7QUFBQTs7QUFDbkIsZ0JBQUksS0FBSyxPQUFMLENBQWEsVUFBYixLQUE0QixJQUFoQyxFQUFzQztBQUNsQyxvQkFBSSxZQUFZLElBQWhCOztBQUVBLHFDQUFTLE1BQVQsRUFBaUIsUUFBakIsRUFBMkIsWUFBTTtBQUM3Qjs7QUFFQSxpQ0FBYSxTQUFiOztBQUVBLGdDQUFZLFdBQVcsWUFBTTtBQUN6QixnQ0FBUSxHQUFSLENBQVkseUNBQVo7O0FBRUE7QUFDQSwrQkFBSyxTQUFMO0FBQ0gscUJBTFcsRUFLVCxPQUFLLE9BQUwsQ0FBYSxnQkFMSixDQUFaO0FBTUgsaUJBWEQ7QUFZSDtBQUNKOztBQUVEOzs7Ozs7OztzQ0FLYztBQUFBOztBQUNWLGdCQUFNLFFBQVEsSUFBZDs7QUFFQSxtQkFBTyxJQUFJLE9BQUosQ0FBWSxtQkFBVztBQUMxQix3QkFBUSxHQUFSLENBQVksK0JBQVo7O0FBRUEsb0JBQUksT0FBSyxlQUFMLEVBQUosRUFBNEI7QUFDeEIsNEJBQVEsR0FBUixDQUFZLDJEQUFaOztBQUVBLDBCQUFNLGtCQUFOLENBQXlCLE9BQXpCO0FBQ0gsaUJBSkQsTUFJTztBQUNILDRCQUFRLEdBQVIsQ0FBWSxzREFBWjs7QUFFQSwwQkFBTSxzQkFBTixDQUE2QixPQUE3QjtBQUNBO0FBQ0g7QUFDSixhQWJNLENBQVA7QUFjSDs7QUFFRDs7Ozs7Ozs7OzBDQU1rQjtBQUNkLGdCQUFNLFVBQVUsU0FBUyxvQkFBVCxDQUE4QixRQUE5QixDQUFoQjs7QUFEYztBQUFBO0FBQUE7O0FBQUE7QUFHZCxzQ0FBbUIsT0FBbkIsbUlBQTRCO0FBQUEsd0JBQW5CLE1BQW1COztBQUN4Qix3QkFBSSxPQUFPLEdBQVAsS0FBZSxLQUFLLGlCQUF4QixFQUEyQztBQUN2QywrQkFBTyxJQUFQO0FBQ0g7QUFDSjtBQVBhO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFRakI7O0FBRUQ7Ozs7Ozs7OzsyQ0FNbUIsTyxFQUFTO0FBQ3hCLGdCQUFJLFNBQVM7QUFDVCwwQkFBVSxLQUFLLFNBRE47QUFFVCwwQkFBVSxLQUFLLE9BQUwsQ0FBYTtBQUZkLGFBQWI7O0FBS0EsZ0JBQUksS0FBSyxPQUFMLENBQWEsWUFBYixLQUE4QixFQUFsQyxFQUFzQztBQUNsQyx1QkFBTyxVQUFQLEdBQW9CLEtBQUssT0FBTCxDQUFhLFlBQWpDO0FBQ0g7O0FBRUQsb0JBQVEsR0FBUixDQUFZLHVDQUFaLEVBQXFELE1BQXJEOztBQUVBLG1CQUFPLE1BQVAsQ0FBYyxJQUFkLENBQW1CLEtBQUssa0JBQXhCLEVBQTRDLE1BQTVDOztBQUVBLG1CQUFPLE1BQVAsQ0FBYyxpQkFBZCxDQUFnQyxPQUFoQztBQUNIOztBQUVEOzs7Ozs7Ozs7OytDQU91QixPLEVBQVM7QUFDNUIsZ0JBQUksUUFBUSxJQUFaO0FBQ0EsZ0JBQUksU0FBUyxTQUFTLGFBQVQsQ0FBdUIsUUFBdkIsQ0FBYjs7QUFFQSxtQkFBTyxJQUFQLEdBQWMsaUJBQWQ7QUFDQSxtQkFBTyxLQUFQLEdBQWUsSUFBZjtBQUNBLG1CQUFPLEdBQVAsR0FBYSxLQUFLLGlCQUFsQjtBQUNBLG1CQUFPLE1BQVAsR0FBZ0IsT0FBTyxrQkFBUCxHQUE0QixVQUFVLEtBQVYsRUFBaUI7QUFDekQsd0JBQVEsU0FBUyxPQUFPLEtBQXhCOztBQUVBLG9CQUFJLE1BQU0sSUFBTixLQUFlLE1BQWYsSUFBMEIsa0JBQWtCLElBQWxCLENBQXVCLEtBQUssVUFBNUIsQ0FBOUIsRUFBd0U7QUFDcEUseUJBQUssTUFBTCxHQUFjLEtBQUssa0JBQUwsR0FBMEIsSUFBeEM7O0FBRUEsMEJBQU0sa0JBQU4sQ0FBeUIsT0FBekI7QUFDSDtBQUNKLGFBUkQ7O0FBVUEscUJBQVMsSUFBVCxDQUFjLFdBQWQsQ0FBMEIsTUFBMUI7QUFDSDs7Ozs7O2tCQXRkZ0IsTTs7Ozs7Ozs7QUMvQnJCOzs7Ozs7Ozs7OztBQVdBOzs7QUFHQSxJQUFNLGlCQUFpQjtBQUNuQixjQUFvQixLQUREO0FBRW5CLFlBQW9CLElBRkQ7QUFHbkIsY0FBb0IscUJBSEQ7QUFJbkIscUJBQW9CLEVBSkQ7QUFLbkIsa0JBQW9CLEVBTEQ7QUFNbkIsZ0JBQW9CLElBTkQ7QUFPbkIsc0JBQW9CO0FBUEQsQ0FBdkI7O2tCQVVlLGM7Ozs7Ozs7Ozs7cWpCQ3hCZjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQW9CQTs7QUFDQTs7OztBQUVBOzs7Ozs7Ozs7SUFTYSxVLFdBQUEsVTtBQUVUOzs7Ozs7Ozs7QUFTQSx3QkFBWSxJQUFaLEVBQWtCO0FBQUE7O0FBQ2QsYUFBSyxNQUFMLEdBQWlCLElBQWpCO0FBQ0EsYUFBSyxLQUFMLEdBQWlCLEtBQUssS0FBdEI7QUFDQSxhQUFLLE9BQUwsR0FBaUIsS0FBSyxPQUF0QjtBQUNBLGFBQUssU0FBTCxHQUFpQixLQUFLLFNBQXRCOztBQUVBLGFBQUssT0FBTCxHQUFlLFNBQVMsY0FBVCxDQUF3QixLQUFLLFNBQTdCLENBQWY7O0FBRUEsWUFBSSxDQUFFLEtBQUssT0FBWCxFQUFvQjtBQUNoQixrQkFBTSw4QkFBc0IsS0FBSyxTQUEzQixDQUFOO0FBQ0g7QUFDSjs7QUFFRDs7Ozs7Ozs7OytCQUtPO0FBQ0gsbUJBQU8sS0FBSyxJQUFMLEdBQVUsSUFBVixHQUFlLEtBQUssS0FBM0I7QUFDSDs7QUFFRDs7Ozs7Ozs7K0JBS087QUFDSCxpQkFBSyxNQUFMLENBQVksSUFBWixDQUFpQixLQUFLLElBQXRCLEVBQTRCLEtBQUssT0FBakM7QUFDSDs7QUFFRDs7Ozs7Ozs7Ozs7Z0NBUVEsTyxFQUFTO0FBQ2I7QUFDQTtBQUNBLGdCQUFJLG9CQUFRLFFBQVEsSUFBaEIsTUFBMEIsT0FBOUIsRUFBdUM7QUFDbkMscUJBQUssSUFBTCxHQUFZLE9BQU8sYUFBUCxDQUFxQixJQUFyQixDQUEwQixJQUExQixDQUNSLElBQUksT0FBTyxhQUFQLENBQXFCLFNBQXpCLENBQW1DLFFBQVEsSUFBUixDQUFhLENBQWIsQ0FBbkMsQ0FEUSxFQUVSLElBQUksT0FBTyxhQUFQLENBQXFCLFNBQXpCLENBQW1DLFFBQVEsSUFBUixDQUFhLENBQWIsQ0FBbkMsQ0FGUSxFQUdSLFFBQVEsSUFIQSxFQUlSLFFBQVEsVUFKQSxFQUtSLFFBQVEsVUFMQSxFQU1SLFFBQVEsVUFOQSxDQUFaOztBQVNBO0FBQ0g7O0FBRUQ7QUFDQTtBQUNBLGdCQUFJLG9CQUFRLFFBQVEsa0JBQWhCLE1BQXdDLFVBQTVDLEVBQXdEO0FBQ3BELHFCQUFLLElBQUwsR0FBWSxPQUFaOztBQUVBO0FBQ0g7O0FBRUQ7QUFDQTtBQUNBLGdCQUFJLG9CQUFRLFFBQVEsSUFBaEIsTUFBMEIsUUFBOUIsRUFBd0M7QUFDcEMsMEJBQVUsUUFBUSxJQUFsQjtBQUNIO0FBQ0Q7O0FBRUE7QUFDQSxpQkFBSyxJQUFMLEdBQVksSUFBSSxPQUFPLGFBQVAsQ0FBcUIsU0FBekIsQ0FBbUMsT0FBbkMsQ0FBWjtBQUNIOztBQUVEOzs7Ozs7Ozs7bUNBTVcsTyxFQUFTO0FBQ2hCLGlCQUFLLE9BQUwsR0FBZSxPQUFmO0FBQ0g7Ozs7Ozs7Ozs7OztRQ3JIVyxJLEdBQUEsSTtRQVVBLE8sR0FBQSxPO1FBV0EsUyxHQUFBLFM7UUFzQkEsUSxHQUFBLFE7UUE0QkEsZ0IsR0FBQSxnQjtBQS9FaEI7QUFDQTs7QUFFQTs7Ozs7QUFLTyxTQUFTLElBQVQsR0FBZ0I7QUFDbkIsV0FBTyxTQUFQO0FBQ0g7O0FBRUQ7Ozs7OztBQU1PLFNBQVMsT0FBVCxDQUFpQixNQUFqQixFQUF5QjtBQUM1QixRQUFJLE9BQU8sT0FBTyxTQUFQLENBQWlCLFFBQWpCLENBQTBCLElBQTFCLENBQStCLE1BQS9CLENBQVg7O0FBRUEsV0FBTyxLQUFLLE9BQUwsQ0FBYSxVQUFiLEVBQXdCLEVBQXhCLEVBQTRCLE9BQTVCLENBQW9DLEdBQXBDLEVBQXdDLEVBQXhDLENBQVA7QUFDSDs7QUFFRDs7Ozs7QUFLTyxTQUFTLFNBQVQsR0FBcUI7QUFDeEIsV0FBTyxJQUFJLE9BQUosQ0FBWSxtQkFBVztBQUMxQixZQUFJLFNBQVMsVUFBVCxLQUF3QixhQUF4QixJQUF5QyxTQUFTLFVBQVQsS0FBd0IsVUFBckUsRUFBaUY7QUFDN0U7QUFDSCxTQUZELE1BRU87QUFDSCxxQkFBUyxnQkFBVCxDQUEwQixrQkFBMUIsRUFBOEMsT0FBOUM7QUFDSDtBQUNKLEtBTk0sQ0FBUDtBQU9IOztBQUVEOzs7Ozs7Ozs7Ozs7QUFZTyxTQUFTLFFBQVQsQ0FBa0IsTUFBbEIsRUFBMEIsSUFBMUIsRUFBZ0MsUUFBaEMsRUFBMEMsV0FBMUMsRUFDUDtBQUNJLFFBQUksV0FBVyxJQUFYLElBQW1CLE9BQU8sTUFBUCxLQUFrQixXQUF6QyxFQUFzRDtBQUNsRDtBQUNIOztBQUVELFFBQUksT0FBTyxnQkFBWCxFQUE2QjtBQUN6QixlQUFPLGdCQUFQLENBQXdCLElBQXhCLEVBQThCLFFBQTlCLEVBQXdDLENBQUMsQ0FBQyxXQUExQztBQUNILEtBRkQsTUFHSyxJQUFHLE9BQU8sV0FBVixFQUF1QjtBQUN4QixlQUFPLFdBQVAsQ0FBbUIsT0FBTyxJQUExQixFQUFnQyxRQUFoQztBQUNILEtBRkksTUFHQTtBQUNELGVBQU8sT0FBTyxJQUFkLElBQXNCLFFBQXRCO0FBQ0g7QUFDSjs7QUFFRDs7Ozs7Ozs7Ozs7QUFXTyxTQUFTLGdCQUFULENBQTBCLFlBQTFCLEVBQXdDLE9BQXhDLEVBQWlEO0FBQ3BELFFBQUksYUFBYSxhQUFhLEtBQWIsQ0FBbUIsR0FBbkIsQ0FBakI7QUFDQSxRQUFJLE9BQU8sV0FBVyxHQUFYLEVBQVg7O0FBRUEsU0FBSyxJQUFJLElBQUksQ0FBYixFQUFnQixJQUFJLFdBQVcsTUFBL0IsRUFBdUMsR0FBdkMsRUFBNEM7QUFDeEMsa0JBQVUsUUFBUSxXQUFXLENBQVgsQ0FBUixDQUFWO0FBQ0g7O0FBRUQsV0FBTyxRQUFRLElBQVIsQ0FBUDtBQUNIIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsIi8vIENvcHlyaWdodCBKb3llbnQsIEluYy4gYW5kIG90aGVyIE5vZGUgY29udHJpYnV0b3JzLlxuLy9cbi8vIFBlcm1pc3Npb24gaXMgaGVyZWJ5IGdyYW50ZWQsIGZyZWUgb2YgY2hhcmdlLCB0byBhbnkgcGVyc29uIG9idGFpbmluZyBhXG4vLyBjb3B5IG9mIHRoaXMgc29mdHdhcmUgYW5kIGFzc29jaWF0ZWQgZG9jdW1lbnRhdGlvbiBmaWxlcyAodGhlXG4vLyBcIlNvZnR3YXJlXCIpLCB0byBkZWFsIGluIHRoZSBTb2Z0d2FyZSB3aXRob3V0IHJlc3RyaWN0aW9uLCBpbmNsdWRpbmdcbi8vIHdpdGhvdXQgbGltaXRhdGlvbiB0aGUgcmlnaHRzIHRvIHVzZSwgY29weSwgbW9kaWZ5LCBtZXJnZSwgcHVibGlzaCxcbi8vIGRpc3RyaWJ1dGUsIHN1YmxpY2Vuc2UsIGFuZC9vciBzZWxsIGNvcGllcyBvZiB0aGUgU29mdHdhcmUsIGFuZCB0byBwZXJtaXRcbi8vIHBlcnNvbnMgdG8gd2hvbSB0aGUgU29mdHdhcmUgaXMgZnVybmlzaGVkIHRvIGRvIHNvLCBzdWJqZWN0IHRvIHRoZVxuLy8gZm9sbG93aW5nIGNvbmRpdGlvbnM6XG4vL1xuLy8gVGhlIGFib3ZlIGNvcHlyaWdodCBub3RpY2UgYW5kIHRoaXMgcGVybWlzc2lvbiBub3RpY2Ugc2hhbGwgYmUgaW5jbHVkZWRcbi8vIGluIGFsbCBjb3BpZXMgb3Igc3Vic3RhbnRpYWwgcG9ydGlvbnMgb2YgdGhlIFNvZnR3YXJlLlxuLy9cbi8vIFRIRSBTT0ZUV0FSRSBJUyBQUk9WSURFRCBcIkFTIElTXCIsIFdJVEhPVVQgV0FSUkFOVFkgT0YgQU5ZIEtJTkQsIEVYUFJFU1Ncbi8vIE9SIElNUExJRUQsIElOQ0xVRElORyBCVVQgTk9UIExJTUlURUQgVE8gVEhFIFdBUlJBTlRJRVMgT0Zcbi8vIE1FUkNIQU5UQUJJTElUWSwgRklUTkVTUyBGT1IgQSBQQVJUSUNVTEFSIFBVUlBPU0UgQU5EIE5PTklORlJJTkdFTUVOVC4gSU5cbi8vIE5PIEVWRU5UIFNIQUxMIFRIRSBBVVRIT1JTIE9SIENPUFlSSUdIVCBIT0xERVJTIEJFIExJQUJMRSBGT1IgQU5ZIENMQUlNLFxuLy8gREFNQUdFUyBPUiBPVEhFUiBMSUFCSUxJVFksIFdIRVRIRVIgSU4gQU4gQUNUSU9OIE9GIENPTlRSQUNULCBUT1JUIE9SXG4vLyBPVEhFUldJU0UsIEFSSVNJTkcgRlJPTSwgT1VUIE9GIE9SIElOIENPTk5FQ1RJT04gV0lUSCBUSEUgU09GVFdBUkUgT1IgVEhFXG4vLyBVU0UgT1IgT1RIRVIgREVBTElOR1MgSU4gVEhFIFNPRlRXQVJFLlxuXG5mdW5jdGlvbiBFdmVudEVtaXR0ZXIoKSB7XG4gIHRoaXMuX2V2ZW50cyA9IHRoaXMuX2V2ZW50cyB8fCB7fTtcbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gdGhpcy5fbWF4TGlzdGVuZXJzIHx8IHVuZGVmaW5lZDtcbn1cbm1vZHVsZS5leHBvcnRzID0gRXZlbnRFbWl0dGVyO1xuXG4vLyBCYWNrd2FyZHMtY29tcGF0IHdpdGggbm9kZSAwLjEwLnhcbkV2ZW50RW1pdHRlci5FdmVudEVtaXR0ZXIgPSBFdmVudEVtaXR0ZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50cyA9IHVuZGVmaW5lZDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX21heExpc3RlbmVycyA9IHVuZGVmaW5lZDtcblxuLy8gQnkgZGVmYXVsdCBFdmVudEVtaXR0ZXJzIHdpbGwgcHJpbnQgYSB3YXJuaW5nIGlmIG1vcmUgdGhhbiAxMCBsaXN0ZW5lcnMgYXJlXG4vLyBhZGRlZCB0byBpdC4gVGhpcyBpcyBhIHVzZWZ1bCBkZWZhdWx0IHdoaWNoIGhlbHBzIGZpbmRpbmcgbWVtb3J5IGxlYWtzLlxuRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnMgPSAxMDtcblxuLy8gT2J2aW91c2x5IG5vdCBhbGwgRW1pdHRlcnMgc2hvdWxkIGJlIGxpbWl0ZWQgdG8gMTAuIFRoaXMgZnVuY3Rpb24gYWxsb3dzXG4vLyB0aGF0IHRvIGJlIGluY3JlYXNlZC4gU2V0IHRvIHplcm8gZm9yIHVubGltaXRlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuc2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24obikge1xuICBpZiAoIWlzTnVtYmVyKG4pIHx8IG4gPCAwIHx8IGlzTmFOKG4pKVxuICAgIHRocm93IFR5cGVFcnJvcignbiBtdXN0IGJlIGEgcG9zaXRpdmUgbnVtYmVyJyk7XG4gIHRoaXMuX21heExpc3RlbmVycyA9IG47XG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5lbWl0ID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIgZXIsIGhhbmRsZXIsIGxlbiwgYXJncywgaSwgbGlzdGVuZXJzO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzKVxuICAgIHRoaXMuX2V2ZW50cyA9IHt9O1xuXG4gIC8vIElmIHRoZXJlIGlzIG5vICdlcnJvcicgZXZlbnQgbGlzdGVuZXIgdGhlbiB0aHJvdy5cbiAgaWYgKHR5cGUgPT09ICdlcnJvcicpIHtcbiAgICBpZiAoIXRoaXMuX2V2ZW50cy5lcnJvciB8fFxuICAgICAgICAoaXNPYmplY3QodGhpcy5fZXZlbnRzLmVycm9yKSAmJiAhdGhpcy5fZXZlbnRzLmVycm9yLmxlbmd0aCkpIHtcbiAgICAgIGVyID0gYXJndW1lbnRzWzFdO1xuICAgICAgaWYgKGVyIGluc3RhbmNlb2YgRXJyb3IpIHtcbiAgICAgICAgdGhyb3cgZXI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBBdCBsZWFzdCBnaXZlIHNvbWUga2luZCBvZiBjb250ZXh0IHRvIHRoZSB1c2VyXG4gICAgICAgIHZhciBlcnIgPSBuZXcgRXJyb3IoJ1VuY2F1Z2h0LCB1bnNwZWNpZmllZCBcImVycm9yXCIgZXZlbnQuICgnICsgZXIgKyAnKScpO1xuICAgICAgICBlcnIuY29udGV4dCA9IGVyO1xuICAgICAgICB0aHJvdyBlcnI7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgaGFuZGxlciA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICBpZiAoaXNVbmRlZmluZWQoaGFuZGxlcikpXG4gICAgcmV0dXJuIGZhbHNlO1xuXG4gIGlmIChpc0Z1bmN0aW9uKGhhbmRsZXIpKSB7XG4gICAgc3dpdGNoIChhcmd1bWVudHMubGVuZ3RoKSB7XG4gICAgICAvLyBmYXN0IGNhc2VzXG4gICAgICBjYXNlIDE6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIDI6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzLCBhcmd1bWVudHNbMV0pO1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgMzpcbiAgICAgICAgaGFuZGxlci5jYWxsKHRoaXMsIGFyZ3VtZW50c1sxXSwgYXJndW1lbnRzWzJdKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICAvLyBzbG93ZXJcbiAgICAgIGRlZmF1bHQ6XG4gICAgICAgIGFyZ3MgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChhcmd1bWVudHMsIDEpO1xuICAgICAgICBoYW5kbGVyLmFwcGx5KHRoaXMsIGFyZ3MpO1xuICAgIH1cbiAgfSBlbHNlIGlmIChpc09iamVjdChoYW5kbGVyKSkge1xuICAgIGFyZ3MgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChhcmd1bWVudHMsIDEpO1xuICAgIGxpc3RlbmVycyA9IGhhbmRsZXIuc2xpY2UoKTtcbiAgICBsZW4gPSBsaXN0ZW5lcnMubGVuZ3RoO1xuICAgIGZvciAoaSA9IDA7IGkgPCBsZW47IGkrKylcbiAgICAgIGxpc3RlbmVyc1tpXS5hcHBseSh0aGlzLCBhcmdzKTtcbiAgfVxuXG4gIHJldHVybiB0cnVlO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lciA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBtO1xuXG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcblxuICAvLyBUbyBhdm9pZCByZWN1cnNpb24gaW4gdGhlIGNhc2UgdGhhdCB0eXBlID09PSBcIm5ld0xpc3RlbmVyXCIhIEJlZm9yZVxuICAvLyBhZGRpbmcgaXQgdG8gdGhlIGxpc3RlbmVycywgZmlyc3QgZW1pdCBcIm5ld0xpc3RlbmVyXCIuXG4gIGlmICh0aGlzLl9ldmVudHMubmV3TGlzdGVuZXIpXG4gICAgdGhpcy5lbWl0KCduZXdMaXN0ZW5lcicsIHR5cGUsXG4gICAgICAgICAgICAgIGlzRnVuY3Rpb24obGlzdGVuZXIubGlzdGVuZXIpID9cbiAgICAgICAgICAgICAgbGlzdGVuZXIubGlzdGVuZXIgOiBsaXN0ZW5lcik7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHNbdHlwZV0pXG4gICAgLy8gT3B0aW1pemUgdGhlIGNhc2Ugb2Ygb25lIGxpc3RlbmVyLiBEb24ndCBuZWVkIHRoZSBleHRyYSBhcnJheSBvYmplY3QuXG4gICAgdGhpcy5fZXZlbnRzW3R5cGVdID0gbGlzdGVuZXI7XG4gIGVsc2UgaWYgKGlzT2JqZWN0KHRoaXMuX2V2ZW50c1t0eXBlXSkpXG4gICAgLy8gSWYgd2UndmUgYWxyZWFkeSBnb3QgYW4gYXJyYXksIGp1c3QgYXBwZW5kLlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXS5wdXNoKGxpc3RlbmVyKTtcbiAgZWxzZVxuICAgIC8vIEFkZGluZyB0aGUgc2Vjb25kIGVsZW1lbnQsIG5lZWQgdG8gY2hhbmdlIHRvIGFycmF5LlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXSA9IFt0aGlzLl9ldmVudHNbdHlwZV0sIGxpc3RlbmVyXTtcblxuICAvLyBDaGVjayBmb3IgbGlzdGVuZXIgbGVha1xuICBpZiAoaXNPYmplY3QodGhpcy5fZXZlbnRzW3R5cGVdKSAmJiAhdGhpcy5fZXZlbnRzW3R5cGVdLndhcm5lZCkge1xuICAgIGlmICghaXNVbmRlZmluZWQodGhpcy5fbWF4TGlzdGVuZXJzKSkge1xuICAgICAgbSA9IHRoaXMuX21heExpc3RlbmVycztcbiAgICB9IGVsc2Uge1xuICAgICAgbSA9IEV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzO1xuICAgIH1cblxuICAgIGlmIChtICYmIG0gPiAwICYmIHRoaXMuX2V2ZW50c1t0eXBlXS5sZW5ndGggPiBtKSB7XG4gICAgICB0aGlzLl9ldmVudHNbdHlwZV0ud2FybmVkID0gdHJ1ZTtcbiAgICAgIGNvbnNvbGUuZXJyb3IoJyhub2RlKSB3YXJuaW5nOiBwb3NzaWJsZSBFdmVudEVtaXR0ZXIgbWVtb3J5ICcgK1xuICAgICAgICAgICAgICAgICAgICAnbGVhayBkZXRlY3RlZC4gJWQgbGlzdGVuZXJzIGFkZGVkLiAnICtcbiAgICAgICAgICAgICAgICAgICAgJ1VzZSBlbWl0dGVyLnNldE1heExpc3RlbmVycygpIHRvIGluY3JlYXNlIGxpbWl0LicsXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuX2V2ZW50c1t0eXBlXS5sZW5ndGgpO1xuICAgICAgaWYgKHR5cGVvZiBjb25zb2xlLnRyYWNlID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIC8vIG5vdCBzdXBwb3J0ZWQgaW4gSUUgMTBcbiAgICAgICAgY29uc29sZS50cmFjZSgpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub25jZSA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICB2YXIgZmlyZWQgPSBmYWxzZTtcblxuICBmdW5jdGlvbiBnKCkge1xuICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgZyk7XG5cbiAgICBpZiAoIWZpcmVkKSB7XG4gICAgICBmaXJlZCA9IHRydWU7XG4gICAgICBsaXN0ZW5lci5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgIH1cbiAgfVxuXG4gIGcubGlzdGVuZXIgPSBsaXN0ZW5lcjtcbiAgdGhpcy5vbih0eXBlLCBnKTtcblxuICByZXR1cm4gdGhpcztcbn07XG5cbi8vIGVtaXRzIGEgJ3JlbW92ZUxpc3RlbmVyJyBldmVudCBpZmYgdGhlIGxpc3RlbmVyIHdhcyByZW1vdmVkXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUxpc3RlbmVyID0gZnVuY3Rpb24odHlwZSwgbGlzdGVuZXIpIHtcbiAgdmFyIGxpc3QsIHBvc2l0aW9uLCBsZW5ndGgsIGk7XG5cbiAgaWYgKCFpc0Z1bmN0aW9uKGxpc3RlbmVyKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ2xpc3RlbmVyIG11c3QgYmUgYSBmdW5jdGlvbicpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzIHx8ICF0aGlzLl9ldmVudHNbdHlwZV0pXG4gICAgcmV0dXJuIHRoaXM7XG5cbiAgbGlzdCA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgbGVuZ3RoID0gbGlzdC5sZW5ndGg7XG4gIHBvc2l0aW9uID0gLTE7XG5cbiAgaWYgKGxpc3QgPT09IGxpc3RlbmVyIHx8XG4gICAgICAoaXNGdW5jdGlvbihsaXN0Lmxpc3RlbmVyKSAmJiBsaXN0Lmxpc3RlbmVyID09PSBsaXN0ZW5lcikpIHtcbiAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIGlmICh0aGlzLl9ldmVudHMucmVtb3ZlTGlzdGVuZXIpXG4gICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgbGlzdGVuZXIpO1xuXG4gIH0gZWxzZSBpZiAoaXNPYmplY3QobGlzdCkpIHtcbiAgICBmb3IgKGkgPSBsZW5ndGg7IGktLSA+IDA7KSB7XG4gICAgICBpZiAobGlzdFtpXSA9PT0gbGlzdGVuZXIgfHxcbiAgICAgICAgICAobGlzdFtpXS5saXN0ZW5lciAmJiBsaXN0W2ldLmxpc3RlbmVyID09PSBsaXN0ZW5lcikpIHtcbiAgICAgICAgcG9zaXRpb24gPSBpO1xuICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAocG9zaXRpb24gPCAwKVxuICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICBpZiAobGlzdC5sZW5ndGggPT09IDEpIHtcbiAgICAgIGxpc3QubGVuZ3RoID0gMDtcbiAgICAgIGRlbGV0ZSB0aGlzLl9ldmVudHNbdHlwZV07XG4gICAgfSBlbHNlIHtcbiAgICAgIGxpc3Quc3BsaWNlKHBvc2l0aW9uLCAxKTtcbiAgICB9XG5cbiAgICBpZiAodGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3RlbmVyKTtcbiAgfVxuXG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVBbGxMaXN0ZW5lcnMgPSBmdW5jdGlvbih0eXBlKSB7XG4gIHZhciBrZXksIGxpc3RlbmVycztcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICByZXR1cm4gdGhpcztcblxuICAvLyBub3QgbGlzdGVuaW5nIGZvciByZW1vdmVMaXN0ZW5lciwgbm8gbmVlZCB0byBlbWl0XG4gIGlmICghdGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKSB7XG4gICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApXG4gICAgICB0aGlzLl9ldmVudHMgPSB7fTtcbiAgICBlbHNlIGlmICh0aGlzLl9ldmVudHNbdHlwZV0pXG4gICAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgLy8gZW1pdCByZW1vdmVMaXN0ZW5lciBmb3IgYWxsIGxpc3RlbmVycyBvbiBhbGwgZXZlbnRzXG4gIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgZm9yIChrZXkgaW4gdGhpcy5fZXZlbnRzKSB7XG4gICAgICBpZiAoa2V5ID09PSAncmVtb3ZlTGlzdGVuZXInKSBjb250aW51ZTtcbiAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKGtleSk7XG4gICAgfVxuICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKCdyZW1vdmVMaXN0ZW5lcicpO1xuICAgIHRoaXMuX2V2ZW50cyA9IHt9O1xuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgbGlzdGVuZXJzID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gIGlmIChpc0Z1bmN0aW9uKGxpc3RlbmVycykpIHtcbiAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVycyk7XG4gIH0gZWxzZSBpZiAobGlzdGVuZXJzKSB7XG4gICAgLy8gTElGTyBvcmRlclxuICAgIHdoaWxlIChsaXN0ZW5lcnMubGVuZ3RoKVxuICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnNbbGlzdGVuZXJzLmxlbmd0aCAtIDFdKTtcbiAgfVxuICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lcnMgPSBmdW5jdGlvbih0eXBlKSB7XG4gIHZhciByZXQ7XG4gIGlmICghdGhpcy5fZXZlbnRzIHx8ICF0aGlzLl9ldmVudHNbdHlwZV0pXG4gICAgcmV0ID0gW107XG4gIGVsc2UgaWYgKGlzRnVuY3Rpb24odGhpcy5fZXZlbnRzW3R5cGVdKSlcbiAgICByZXQgPSBbdGhpcy5fZXZlbnRzW3R5cGVdXTtcbiAgZWxzZVxuICAgIHJldCA9IHRoaXMuX2V2ZW50c1t0eXBlXS5zbGljZSgpO1xuICByZXR1cm4gcmV0O1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lckNvdW50ID0gZnVuY3Rpb24odHlwZSkge1xuICBpZiAodGhpcy5fZXZlbnRzKSB7XG4gICAgdmFyIGV2bGlzdGVuZXIgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgICBpZiAoaXNGdW5jdGlvbihldmxpc3RlbmVyKSlcbiAgICAgIHJldHVybiAxO1xuICAgIGVsc2UgaWYgKGV2bGlzdGVuZXIpXG4gICAgICByZXR1cm4gZXZsaXN0ZW5lci5sZW5ndGg7XG4gIH1cbiAgcmV0dXJuIDA7XG59O1xuXG5FdmVudEVtaXR0ZXIubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKGVtaXR0ZXIsIHR5cGUpIHtcbiAgcmV0dXJuIGVtaXR0ZXIubGlzdGVuZXJDb3VudCh0eXBlKTtcbn07XG5cbmZ1bmN0aW9uIGlzRnVuY3Rpb24oYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnZnVuY3Rpb24nO1xufVxuXG5mdW5jdGlvbiBpc051bWJlcihhcmcpIHtcbiAgcmV0dXJuIHR5cGVvZiBhcmcgPT09ICdudW1iZXInO1xufVxuXG5mdW5jdGlvbiBpc09iamVjdChhcmcpIHtcbiAgcmV0dXJuIHR5cGVvZiBhcmcgPT09ICdvYmplY3QnICYmIGFyZyAhPT0gbnVsbDtcbn1cblxuZnVuY3Rpb24gaXNVbmRlZmluZWQoYXJnKSB7XG4gIHJldHVybiBhcmcgPT09IHZvaWQgMDtcbn1cbiIsInZhciByb290ID0gcmVxdWlyZSgnLi9fcm9vdCcpO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBTeW1ib2wgPSByb290LlN5bWJvbDtcblxubW9kdWxlLmV4cG9ydHMgPSBTeW1ib2w7XG4iLCJ2YXIgYmFzZVRpbWVzID0gcmVxdWlyZSgnLi9fYmFzZVRpbWVzJyksXG4gICAgaXNBcmd1bWVudHMgPSByZXF1aXJlKCcuL2lzQXJndW1lbnRzJyksXG4gICAgaXNBcnJheSA9IHJlcXVpcmUoJy4vaXNBcnJheScpLFxuICAgIGlzQnVmZmVyID0gcmVxdWlyZSgnLi9pc0J1ZmZlcicpLFxuICAgIGlzSW5kZXggPSByZXF1aXJlKCcuL19pc0luZGV4JyksXG4gICAgaXNUeXBlZEFycmF5ID0gcmVxdWlyZSgnLi9pc1R5cGVkQXJyYXknKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqXG4gKiBDcmVhdGVzIGFuIGFycmF5IG9mIHRoZSBlbnVtZXJhYmxlIHByb3BlcnR5IG5hbWVzIG9mIHRoZSBhcnJheS1saWtlIGB2YWx1ZWAuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHBhcmFtIHtib29sZWFufSBpbmhlcml0ZWQgU3BlY2lmeSByZXR1cm5pbmcgaW5oZXJpdGVkIHByb3BlcnR5IG5hbWVzLlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqL1xuZnVuY3Rpb24gYXJyYXlMaWtlS2V5cyh2YWx1ZSwgaW5oZXJpdGVkKSB7XG4gIHZhciBpc0FyciA9IGlzQXJyYXkodmFsdWUpLFxuICAgICAgaXNBcmcgPSAhaXNBcnIgJiYgaXNBcmd1bWVudHModmFsdWUpLFxuICAgICAgaXNCdWZmID0gIWlzQXJyICYmICFpc0FyZyAmJiBpc0J1ZmZlcih2YWx1ZSksXG4gICAgICBpc1R5cGUgPSAhaXNBcnIgJiYgIWlzQXJnICYmICFpc0J1ZmYgJiYgaXNUeXBlZEFycmF5KHZhbHVlKSxcbiAgICAgIHNraXBJbmRleGVzID0gaXNBcnIgfHwgaXNBcmcgfHwgaXNCdWZmIHx8IGlzVHlwZSxcbiAgICAgIHJlc3VsdCA9IHNraXBJbmRleGVzID8gYmFzZVRpbWVzKHZhbHVlLmxlbmd0aCwgU3RyaW5nKSA6IFtdLFxuICAgICAgbGVuZ3RoID0gcmVzdWx0Lmxlbmd0aDtcblxuICBmb3IgKHZhciBrZXkgaW4gdmFsdWUpIHtcbiAgICBpZiAoKGluaGVyaXRlZCB8fCBoYXNPd25Qcm9wZXJ0eS5jYWxsKHZhbHVlLCBrZXkpKSAmJlxuICAgICAgICAhKHNraXBJbmRleGVzICYmIChcbiAgICAgICAgICAgLy8gU2FmYXJpIDkgaGFzIGVudW1lcmFibGUgYGFyZ3VtZW50cy5sZW5ndGhgIGluIHN0cmljdCBtb2RlLlxuICAgICAgICAgICBrZXkgPT0gJ2xlbmd0aCcgfHxcbiAgICAgICAgICAgLy8gTm9kZS5qcyAwLjEwIGhhcyBlbnVtZXJhYmxlIG5vbi1pbmRleCBwcm9wZXJ0aWVzIG9uIGJ1ZmZlcnMuXG4gICAgICAgICAgIChpc0J1ZmYgJiYgKGtleSA9PSAnb2Zmc2V0JyB8fCBrZXkgPT0gJ3BhcmVudCcpKSB8fFxuICAgICAgICAgICAvLyBQaGFudG9tSlMgMiBoYXMgZW51bWVyYWJsZSBub24taW5kZXggcHJvcGVydGllcyBvbiB0eXBlZCBhcnJheXMuXG4gICAgICAgICAgIChpc1R5cGUgJiYgKGtleSA9PSAnYnVmZmVyJyB8fCBrZXkgPT0gJ2J5dGVMZW5ndGgnIHx8IGtleSA9PSAnYnl0ZU9mZnNldCcpKSB8fFxuICAgICAgICAgICAvLyBTa2lwIGluZGV4IHByb3BlcnRpZXMuXG4gICAgICAgICAgIGlzSW5kZXgoa2V5LCBsZW5ndGgpXG4gICAgICAgICkpKSB7XG4gICAgICByZXN1bHQucHVzaChrZXkpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGFycmF5TGlrZUtleXM7XG4iLCJ2YXIgY3JlYXRlQmFzZUZvciA9IHJlcXVpcmUoJy4vX2NyZWF0ZUJhc2VGb3InKTtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgYmFzZUZvck93bmAgd2hpY2ggaXRlcmF0ZXMgb3ZlciBgb2JqZWN0YFxuICogcHJvcGVydGllcyByZXR1cm5lZCBieSBga2V5c0Z1bmNgIGFuZCBpbnZva2VzIGBpdGVyYXRlZWAgZm9yIGVhY2ggcHJvcGVydHkuXG4gKiBJdGVyYXRlZSBmdW5jdGlvbnMgbWF5IGV4aXQgaXRlcmF0aW9uIGVhcmx5IGJ5IGV4cGxpY2l0bHkgcmV0dXJuaW5nIGBmYWxzZWAuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3QgVGhlIG9iamVjdCB0byBpdGVyYXRlIG92ZXIuXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBpdGVyYXRlZSBUaGUgZnVuY3Rpb24gaW52b2tlZCBwZXIgaXRlcmF0aW9uLlxuICogQHBhcmFtIHtGdW5jdGlvbn0ga2V5c0Z1bmMgVGhlIGZ1bmN0aW9uIHRvIGdldCB0aGUga2V5cyBvZiBgb2JqZWN0YC5cbiAqIEByZXR1cm5zIHtPYmplY3R9IFJldHVybnMgYG9iamVjdGAuXG4gKi9cbnZhciBiYXNlRm9yID0gY3JlYXRlQmFzZUZvcigpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VGb3I7XG4iLCJ2YXIgU3ltYm9sID0gcmVxdWlyZSgnLi9fU3ltYm9sJyksXG4gICAgZ2V0UmF3VGFnID0gcmVxdWlyZSgnLi9fZ2V0UmF3VGFnJyksXG4gICAgb2JqZWN0VG9TdHJpbmcgPSByZXF1aXJlKCcuL19vYmplY3RUb1N0cmluZycpO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgbnVsbFRhZyA9ICdbb2JqZWN0IE51bGxdJyxcbiAgICB1bmRlZmluZWRUYWcgPSAnW29iamVjdCBVbmRlZmluZWRdJztcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgc3ltVG9TdHJpbmdUYWcgPSBTeW1ib2wgPyBTeW1ib2wudG9TdHJpbmdUYWcgOiB1bmRlZmluZWQ7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYGdldFRhZ2Agd2l0aG91dCBmYWxsYmFja3MgZm9yIGJ1Z2d5IGVudmlyb25tZW50cy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSBgdG9TdHJpbmdUYWdgLlxuICovXG5mdW5jdGlvbiBiYXNlR2V0VGFnKHZhbHVlKSB7XG4gIGlmICh2YWx1ZSA9PSBudWxsKSB7XG4gICAgcmV0dXJuIHZhbHVlID09PSB1bmRlZmluZWQgPyB1bmRlZmluZWRUYWcgOiBudWxsVGFnO1xuICB9XG4gIHJldHVybiAoc3ltVG9TdHJpbmdUYWcgJiYgc3ltVG9TdHJpbmdUYWcgaW4gT2JqZWN0KHZhbHVlKSlcbiAgICA/IGdldFJhd1RhZyh2YWx1ZSlcbiAgICA6IG9iamVjdFRvU3RyaW5nKHZhbHVlKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlR2V0VGFnO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNPYmplY3RMaWtlID0gcmVxdWlyZSgnLi9pc09iamVjdExpa2UnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIGFyZ3NUYWcgPSAnW29iamVjdCBBcmd1bWVudHNdJztcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy5pc0FyZ3VtZW50c2AuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gYGFyZ3VtZW50c2Agb2JqZWN0LFxuICovXG5mdW5jdGlvbiBiYXNlSXNBcmd1bWVudHModmFsdWUpIHtcbiAgcmV0dXJuIGlzT2JqZWN0TGlrZSh2YWx1ZSkgJiYgYmFzZUdldFRhZyh2YWx1ZSkgPT0gYXJnc1RhZztcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlSXNBcmd1bWVudHM7XG4iLCJ2YXIgYmFzZUdldFRhZyA9IHJlcXVpcmUoJy4vX2Jhc2VHZXRUYWcnKSxcbiAgICBpc0xlbmd0aCA9IHJlcXVpcmUoJy4vaXNMZW5ndGgnKSxcbiAgICBpc09iamVjdExpa2UgPSByZXF1aXJlKCcuL2lzT2JqZWN0TGlrZScpO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgYXJnc1RhZyA9ICdbb2JqZWN0IEFyZ3VtZW50c10nLFxuICAgIGFycmF5VGFnID0gJ1tvYmplY3QgQXJyYXldJyxcbiAgICBib29sVGFnID0gJ1tvYmplY3QgQm9vbGVhbl0nLFxuICAgIGRhdGVUYWcgPSAnW29iamVjdCBEYXRlXScsXG4gICAgZXJyb3JUYWcgPSAnW29iamVjdCBFcnJvcl0nLFxuICAgIGZ1bmNUYWcgPSAnW29iamVjdCBGdW5jdGlvbl0nLFxuICAgIG1hcFRhZyA9ICdbb2JqZWN0IE1hcF0nLFxuICAgIG51bWJlclRhZyA9ICdbb2JqZWN0IE51bWJlcl0nLFxuICAgIG9iamVjdFRhZyA9ICdbb2JqZWN0IE9iamVjdF0nLFxuICAgIHJlZ2V4cFRhZyA9ICdbb2JqZWN0IFJlZ0V4cF0nLFxuICAgIHNldFRhZyA9ICdbb2JqZWN0IFNldF0nLFxuICAgIHN0cmluZ1RhZyA9ICdbb2JqZWN0IFN0cmluZ10nLFxuICAgIHdlYWtNYXBUYWcgPSAnW29iamVjdCBXZWFrTWFwXSc7XG5cbnZhciBhcnJheUJ1ZmZlclRhZyA9ICdbb2JqZWN0IEFycmF5QnVmZmVyXScsXG4gICAgZGF0YVZpZXdUYWcgPSAnW29iamVjdCBEYXRhVmlld10nLFxuICAgIGZsb2F0MzJUYWcgPSAnW29iamVjdCBGbG9hdDMyQXJyYXldJyxcbiAgICBmbG9hdDY0VGFnID0gJ1tvYmplY3QgRmxvYXQ2NEFycmF5XScsXG4gICAgaW50OFRhZyA9ICdbb2JqZWN0IEludDhBcnJheV0nLFxuICAgIGludDE2VGFnID0gJ1tvYmplY3QgSW50MTZBcnJheV0nLFxuICAgIGludDMyVGFnID0gJ1tvYmplY3QgSW50MzJBcnJheV0nLFxuICAgIHVpbnQ4VGFnID0gJ1tvYmplY3QgVWludDhBcnJheV0nLFxuICAgIHVpbnQ4Q2xhbXBlZFRhZyA9ICdbb2JqZWN0IFVpbnQ4Q2xhbXBlZEFycmF5XScsXG4gICAgdWludDE2VGFnID0gJ1tvYmplY3QgVWludDE2QXJyYXldJyxcbiAgICB1aW50MzJUYWcgPSAnW29iamVjdCBVaW50MzJBcnJheV0nO1xuXG4vKiogVXNlZCB0byBpZGVudGlmeSBgdG9TdHJpbmdUYWdgIHZhbHVlcyBvZiB0eXBlZCBhcnJheXMuICovXG52YXIgdHlwZWRBcnJheVRhZ3MgPSB7fTtcbnR5cGVkQXJyYXlUYWdzW2Zsb2F0MzJUYWddID0gdHlwZWRBcnJheVRhZ3NbZmxvYXQ2NFRhZ10gPVxudHlwZWRBcnJheVRhZ3NbaW50OFRhZ10gPSB0eXBlZEFycmF5VGFnc1tpbnQxNlRhZ10gPVxudHlwZWRBcnJheVRhZ3NbaW50MzJUYWddID0gdHlwZWRBcnJheVRhZ3NbdWludDhUYWddID1cbnR5cGVkQXJyYXlUYWdzW3VpbnQ4Q2xhbXBlZFRhZ10gPSB0eXBlZEFycmF5VGFnc1t1aW50MTZUYWddID1cbnR5cGVkQXJyYXlUYWdzW3VpbnQzMlRhZ10gPSB0cnVlO1xudHlwZWRBcnJheVRhZ3NbYXJnc1RhZ10gPSB0eXBlZEFycmF5VGFnc1thcnJheVRhZ10gPVxudHlwZWRBcnJheVRhZ3NbYXJyYXlCdWZmZXJUYWddID0gdHlwZWRBcnJheVRhZ3NbYm9vbFRhZ10gPVxudHlwZWRBcnJheVRhZ3NbZGF0YVZpZXdUYWddID0gdHlwZWRBcnJheVRhZ3NbZGF0ZVRhZ10gPVxudHlwZWRBcnJheVRhZ3NbZXJyb3JUYWddID0gdHlwZWRBcnJheVRhZ3NbZnVuY1RhZ10gPVxudHlwZWRBcnJheVRhZ3NbbWFwVGFnXSA9IHR5cGVkQXJyYXlUYWdzW251bWJlclRhZ10gPVxudHlwZWRBcnJheVRhZ3Nbb2JqZWN0VGFnXSA9IHR5cGVkQXJyYXlUYWdzW3JlZ2V4cFRhZ10gPVxudHlwZWRBcnJheVRhZ3Nbc2V0VGFnXSA9IHR5cGVkQXJyYXlUYWdzW3N0cmluZ1RhZ10gPVxudHlwZWRBcnJheVRhZ3Nbd2Vha01hcFRhZ10gPSBmYWxzZTtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy5pc1R5cGVkQXJyYXlgIHdpdGhvdXQgTm9kZS5qcyBvcHRpbWl6YXRpb25zLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdHlwZWQgYXJyYXksIGVsc2UgYGZhbHNlYC5cbiAqL1xuZnVuY3Rpb24gYmFzZUlzVHlwZWRBcnJheSh2YWx1ZSkge1xuICByZXR1cm4gaXNPYmplY3RMaWtlKHZhbHVlKSAmJlxuICAgIGlzTGVuZ3RoKHZhbHVlLmxlbmd0aCkgJiYgISF0eXBlZEFycmF5VGFnc1tiYXNlR2V0VGFnKHZhbHVlKV07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUlzVHlwZWRBcnJheTtcbiIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vaXNPYmplY3QnKSxcbiAgICBpc1Byb3RvdHlwZSA9IHJlcXVpcmUoJy4vX2lzUHJvdG90eXBlJyksXG4gICAgbmF0aXZlS2V5c0luID0gcmVxdWlyZSgnLi9fbmF0aXZlS2V5c0luJyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8ua2V5c0luYCB3aGljaCBkb2Vzbid0IHRyZWF0IHNwYXJzZSBhcnJheXMgYXMgZGVuc2UuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3QgVGhlIG9iamVjdCB0byBxdWVyeS5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKi9cbmZ1bmN0aW9uIGJhc2VLZXlzSW4ob2JqZWN0KSB7XG4gIGlmICghaXNPYmplY3Qob2JqZWN0KSkge1xuICAgIHJldHVybiBuYXRpdmVLZXlzSW4ob2JqZWN0KTtcbiAgfVxuICB2YXIgaXNQcm90byA9IGlzUHJvdG90eXBlKG9iamVjdCksXG4gICAgICByZXN1bHQgPSBbXTtcblxuICBmb3IgKHZhciBrZXkgaW4gb2JqZWN0KSB7XG4gICAgaWYgKCEoa2V5ID09ICdjb25zdHJ1Y3RvcicgJiYgKGlzUHJvdG8gfHwgIWhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBrZXkpKSkpIHtcbiAgICAgIHJlc3VsdC5wdXNoKGtleSk7XG4gICAgfVxuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUtleXNJbjtcbiIsIi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8udGltZXNgIHdpdGhvdXQgc3VwcG9ydCBmb3IgaXRlcmF0ZWUgc2hvcnRoYW5kc1xuICogb3IgbWF4IGFycmF5IGxlbmd0aCBjaGVja3MuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7bnVtYmVyfSBuIFRoZSBudW1iZXIgb2YgdGltZXMgdG8gaW52b2tlIGBpdGVyYXRlZWAuXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBpdGVyYXRlZSBUaGUgZnVuY3Rpb24gaW52b2tlZCBwZXIgaXRlcmF0aW9uLlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiByZXN1bHRzLlxuICovXG5mdW5jdGlvbiBiYXNlVGltZXMobiwgaXRlcmF0ZWUpIHtcbiAgdmFyIGluZGV4ID0gLTEsXG4gICAgICByZXN1bHQgPSBBcnJheShuKTtcblxuICB3aGlsZSAoKytpbmRleCA8IG4pIHtcbiAgICByZXN1bHRbaW5kZXhdID0gaXRlcmF0ZWUoaW5kZXgpO1xuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZVRpbWVzO1xuIiwiLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy51bmFyeWAgd2l0aG91dCBzdXBwb3J0IGZvciBzdG9yaW5nIG1ldGFkYXRhLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBmdW5jIFRoZSBmdW5jdGlvbiB0byBjYXAgYXJndW1lbnRzIGZvci5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn0gUmV0dXJucyB0aGUgbmV3IGNhcHBlZCBmdW5jdGlvbi5cbiAqL1xuZnVuY3Rpb24gYmFzZVVuYXJ5KGZ1bmMpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uKHZhbHVlKSB7XG4gICAgcmV0dXJuIGZ1bmModmFsdWUpO1xuICB9O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VVbmFyeTtcbiIsInZhciBpZGVudGl0eSA9IHJlcXVpcmUoJy4vaWRlbnRpdHknKTtcblxuLyoqXG4gKiBDYXN0cyBgdmFsdWVgIHRvIGBpZGVudGl0eWAgaWYgaXQncyBub3QgYSBmdW5jdGlvbi5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gaW5zcGVjdC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn0gUmV0dXJucyBjYXN0IGZ1bmN0aW9uLlxuICovXG5mdW5jdGlvbiBjYXN0RnVuY3Rpb24odmFsdWUpIHtcbiAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PSAnZnVuY3Rpb24nID8gdmFsdWUgOiBpZGVudGl0eTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBjYXN0RnVuY3Rpb247XG4iLCIvKipcbiAqIENyZWF0ZXMgYSBiYXNlIGZ1bmN0aW9uIGZvciBtZXRob2RzIGxpa2UgYF8uZm9ySW5gIGFuZCBgXy5mb3JPd25gLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge2Jvb2xlYW59IFtmcm9tUmlnaHRdIFNwZWNpZnkgaXRlcmF0aW5nIGZyb20gcmlnaHQgdG8gbGVmdC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn0gUmV0dXJucyB0aGUgbmV3IGJhc2UgZnVuY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIGNyZWF0ZUJhc2VGb3IoZnJvbVJpZ2h0KSB7XG4gIHJldHVybiBmdW5jdGlvbihvYmplY3QsIGl0ZXJhdGVlLCBrZXlzRnVuYykge1xuICAgIHZhciBpbmRleCA9IC0xLFxuICAgICAgICBpdGVyYWJsZSA9IE9iamVjdChvYmplY3QpLFxuICAgICAgICBwcm9wcyA9IGtleXNGdW5jKG9iamVjdCksXG4gICAgICAgIGxlbmd0aCA9IHByb3BzLmxlbmd0aDtcblxuICAgIHdoaWxlIChsZW5ndGgtLSkge1xuICAgICAgdmFyIGtleSA9IHByb3BzW2Zyb21SaWdodCA/IGxlbmd0aCA6ICsraW5kZXhdO1xuICAgICAgaWYgKGl0ZXJhdGVlKGl0ZXJhYmxlW2tleV0sIGtleSwgaXRlcmFibGUpID09PSBmYWxzZSkge1xuICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIG9iamVjdDtcbiAgfTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBjcmVhdGVCYXNlRm9yO1xuIiwiLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBnbG9iYWxgIGZyb20gTm9kZS5qcy4gKi9cbnZhciBmcmVlR2xvYmFsID0gdHlwZW9mIGdsb2JhbCA9PSAnb2JqZWN0JyAmJiBnbG9iYWwgJiYgZ2xvYmFsLk9iamVjdCA9PT0gT2JqZWN0ICYmIGdsb2JhbDtcblxubW9kdWxlLmV4cG9ydHMgPSBmcmVlR2xvYmFsO1xuIiwidmFyIFN5bWJvbCA9IHJlcXVpcmUoJy4vX1N5bWJvbCcpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKipcbiAqIFVzZWQgdG8gcmVzb2x2ZSB0aGVcbiAqIFtgdG9TdHJpbmdUYWdgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy1vYmplY3QucHJvdG90eXBlLnRvc3RyaW5nKVxuICogb2YgdmFsdWVzLlxuICovXG52YXIgbmF0aXZlT2JqZWN0VG9TdHJpbmcgPSBvYmplY3RQcm90by50b1N0cmluZztcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgc3ltVG9TdHJpbmdUYWcgPSBTeW1ib2wgPyBTeW1ib2wudG9TdHJpbmdUYWcgOiB1bmRlZmluZWQ7XG5cbi8qKlxuICogQSBzcGVjaWFsaXplZCB2ZXJzaW9uIG9mIGBiYXNlR2V0VGFnYCB3aGljaCBpZ25vcmVzIGBTeW1ib2wudG9TdHJpbmdUYWdgIHZhbHVlcy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSByYXcgYHRvU3RyaW5nVGFnYC5cbiAqL1xuZnVuY3Rpb24gZ2V0UmF3VGFnKHZhbHVlKSB7XG4gIHZhciBpc093biA9IGhhc093blByb3BlcnR5LmNhbGwodmFsdWUsIHN5bVRvU3RyaW5nVGFnKSxcbiAgICAgIHRhZyA9IHZhbHVlW3N5bVRvU3RyaW5nVGFnXTtcblxuICB0cnkge1xuICAgIHZhbHVlW3N5bVRvU3RyaW5nVGFnXSA9IHVuZGVmaW5lZDtcbiAgICB2YXIgdW5tYXNrZWQgPSB0cnVlO1xuICB9IGNhdGNoIChlKSB7fVxuXG4gIHZhciByZXN1bHQgPSBuYXRpdmVPYmplY3RUb1N0cmluZy5jYWxsKHZhbHVlKTtcbiAgaWYgKHVubWFza2VkKSB7XG4gICAgaWYgKGlzT3duKSB7XG4gICAgICB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ10gPSB0YWc7XG4gICAgfSBlbHNlIHtcbiAgICAgIGRlbGV0ZSB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ107XG4gICAgfVxuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZ2V0UmF3VGFnO1xuIiwiLyoqIFVzZWQgYXMgcmVmZXJlbmNlcyBmb3IgdmFyaW91cyBgTnVtYmVyYCBjb25zdGFudHMuICovXG52YXIgTUFYX1NBRkVfSU5URUdFUiA9IDkwMDcxOTkyNTQ3NDA5OTE7XG5cbi8qKiBVc2VkIHRvIGRldGVjdCB1bnNpZ25lZCBpbnRlZ2VyIHZhbHVlcy4gKi9cbnZhciByZUlzVWludCA9IC9eKD86MHxbMS05XVxcZCopJC87XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBhcnJheS1saWtlIGluZGV4LlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEBwYXJhbSB7bnVtYmVyfSBbbGVuZ3RoPU1BWF9TQUZFX0lOVEVHRVJdIFRoZSB1cHBlciBib3VuZHMgb2YgYSB2YWxpZCBpbmRleC5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgaW5kZXgsIGVsc2UgYGZhbHNlYC5cbiAqL1xuZnVuY3Rpb24gaXNJbmRleCh2YWx1ZSwgbGVuZ3RoKSB7XG4gIGxlbmd0aCA9IGxlbmd0aCA9PSBudWxsID8gTUFYX1NBRkVfSU5URUdFUiA6IGxlbmd0aDtcbiAgcmV0dXJuICEhbGVuZ3RoICYmXG4gICAgKHR5cGVvZiB2YWx1ZSA9PSAnbnVtYmVyJyB8fCByZUlzVWludC50ZXN0KHZhbHVlKSkgJiZcbiAgICAodmFsdWUgPiAtMSAmJiB2YWx1ZSAlIDEgPT0gMCAmJiB2YWx1ZSA8IGxlbmd0aCk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNJbmRleDtcbiIsIi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgbGlrZWx5IGEgcHJvdG90eXBlIG9iamVjdC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHByb3RvdHlwZSwgZWxzZSBgZmFsc2VgLlxuICovXG5mdW5jdGlvbiBpc1Byb3RvdHlwZSh2YWx1ZSkge1xuICB2YXIgQ3RvciA9IHZhbHVlICYmIHZhbHVlLmNvbnN0cnVjdG9yLFxuICAgICAgcHJvdG8gPSAodHlwZW9mIEN0b3IgPT0gJ2Z1bmN0aW9uJyAmJiBDdG9yLnByb3RvdHlwZSkgfHwgb2JqZWN0UHJvdG87XG5cbiAgcmV0dXJuIHZhbHVlID09PSBwcm90bztcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc1Byb3RvdHlwZTtcbiIsIi8qKlxuICogVGhpcyBmdW5jdGlvbiBpcyBsaWtlXG4gKiBbYE9iamVjdC5rZXlzYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LmtleXMpXG4gKiBleGNlcHQgdGhhdCBpdCBpbmNsdWRlcyBpbmhlcml0ZWQgZW51bWVyYWJsZSBwcm9wZXJ0aWVzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICovXG5mdW5jdGlvbiBuYXRpdmVLZXlzSW4ob2JqZWN0KSB7XG4gIHZhciByZXN1bHQgPSBbXTtcbiAgaWYgKG9iamVjdCAhPSBudWxsKSB7XG4gICAgZm9yICh2YXIga2V5IGluIE9iamVjdChvYmplY3QpKSB7XG4gICAgICByZXN1bHQucHVzaChrZXkpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IG5hdGl2ZUtleXNJbjtcbiIsInZhciBmcmVlR2xvYmFsID0gcmVxdWlyZSgnLi9fZnJlZUdsb2JhbCcpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGV4cG9ydHNgLiAqL1xudmFyIGZyZWVFeHBvcnRzID0gdHlwZW9mIGV4cG9ydHMgPT0gJ29iamVjdCcgJiYgZXhwb3J0cyAmJiAhZXhwb3J0cy5ub2RlVHlwZSAmJiBleHBvcnRzO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYG1vZHVsZWAuICovXG52YXIgZnJlZU1vZHVsZSA9IGZyZWVFeHBvcnRzICYmIHR5cGVvZiBtb2R1bGUgPT0gJ29iamVjdCcgJiYgbW9kdWxlICYmICFtb2R1bGUubm9kZVR5cGUgJiYgbW9kdWxlO1xuXG4vKiogRGV0ZWN0IHRoZSBwb3B1bGFyIENvbW1vbkpTIGV4dGVuc2lvbiBgbW9kdWxlLmV4cG9ydHNgLiAqL1xudmFyIG1vZHVsZUV4cG9ydHMgPSBmcmVlTW9kdWxlICYmIGZyZWVNb2R1bGUuZXhwb3J0cyA9PT0gZnJlZUV4cG9ydHM7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgcHJvY2Vzc2AgZnJvbSBOb2RlLmpzLiAqL1xudmFyIGZyZWVQcm9jZXNzID0gbW9kdWxlRXhwb3J0cyAmJiBmcmVlR2xvYmFsLnByb2Nlc3M7XG5cbi8qKiBVc2VkIHRvIGFjY2VzcyBmYXN0ZXIgTm9kZS5qcyBoZWxwZXJzLiAqL1xudmFyIG5vZGVVdGlsID0gKGZ1bmN0aW9uKCkge1xuICB0cnkge1xuICAgIHJldHVybiBmcmVlUHJvY2VzcyAmJiBmcmVlUHJvY2Vzcy5iaW5kaW5nICYmIGZyZWVQcm9jZXNzLmJpbmRpbmcoJ3V0aWwnKTtcbiAgfSBjYXRjaCAoZSkge31cbn0oKSk7XG5cbm1vZHVsZS5leHBvcnRzID0gbm9kZVV0aWw7XG4iLCIvKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKipcbiAqIFVzZWQgdG8gcmVzb2x2ZSB0aGVcbiAqIFtgdG9TdHJpbmdUYWdgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy1vYmplY3QucHJvdG90eXBlLnRvc3RyaW5nKVxuICogb2YgdmFsdWVzLlxuICovXG52YXIgbmF0aXZlT2JqZWN0VG9TdHJpbmcgPSBvYmplY3RQcm90by50b1N0cmluZztcblxuLyoqXG4gKiBDb252ZXJ0cyBgdmFsdWVgIHRvIGEgc3RyaW5nIHVzaW5nIGBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY29udmVydC5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIGNvbnZlcnRlZCBzdHJpbmcuXG4gKi9cbmZ1bmN0aW9uIG9iamVjdFRvU3RyaW5nKHZhbHVlKSB7XG4gIHJldHVybiBuYXRpdmVPYmplY3RUb1N0cmluZy5jYWxsKHZhbHVlKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBvYmplY3RUb1N0cmluZztcbiIsInZhciBmcmVlR2xvYmFsID0gcmVxdWlyZSgnLi9fZnJlZUdsb2JhbCcpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHNlbGZgLiAqL1xudmFyIGZyZWVTZWxmID0gdHlwZW9mIHNlbGYgPT0gJ29iamVjdCcgJiYgc2VsZiAmJiBzZWxmLk9iamVjdCA9PT0gT2JqZWN0ICYmIHNlbGY7XG5cbi8qKiBVc2VkIGFzIGEgcmVmZXJlbmNlIHRvIHRoZSBnbG9iYWwgb2JqZWN0LiAqL1xudmFyIHJvb3QgPSBmcmVlR2xvYmFsIHx8IGZyZWVTZWxmIHx8IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5cbm1vZHVsZS5leHBvcnRzID0gcm9vdDtcbiIsInZhciBiYXNlRm9yID0gcmVxdWlyZSgnLi9fYmFzZUZvcicpLFxuICAgIGNhc3RGdW5jdGlvbiA9IHJlcXVpcmUoJy4vX2Nhc3RGdW5jdGlvbicpLFxuICAgIGtleXNJbiA9IHJlcXVpcmUoJy4va2V5c0luJyk7XG5cbi8qKlxuICogSXRlcmF0ZXMgb3ZlciBvd24gYW5kIGluaGVyaXRlZCBlbnVtZXJhYmxlIHN0cmluZyBrZXllZCBwcm9wZXJ0aWVzIG9mIGFuXG4gKiBvYmplY3QgYW5kIGludm9rZXMgYGl0ZXJhdGVlYCBmb3IgZWFjaCBwcm9wZXJ0eS4gVGhlIGl0ZXJhdGVlIGlzIGludm9rZWRcbiAqIHdpdGggdGhyZWUgYXJndW1lbnRzOiAodmFsdWUsIGtleSwgb2JqZWN0KS4gSXRlcmF0ZWUgZnVuY3Rpb25zIG1heSBleGl0XG4gKiBpdGVyYXRpb24gZWFybHkgYnkgZXhwbGljaXRseSByZXR1cm5pbmcgYGZhbHNlYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMy4wXG4gKiBAY2F0ZWdvcnkgT2JqZWN0XG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gaXRlcmF0ZSBvdmVyLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gW2l0ZXJhdGVlPV8uaWRlbnRpdHldIFRoZSBmdW5jdGlvbiBpbnZva2VkIHBlciBpdGVyYXRpb24uXG4gKiBAcmV0dXJucyB7T2JqZWN0fSBSZXR1cm5zIGBvYmplY3RgLlxuICogQHNlZSBfLmZvckluUmlnaHRcbiAqIEBleGFtcGxlXG4gKlxuICogZnVuY3Rpb24gRm9vKCkge1xuICogICB0aGlzLmEgPSAxO1xuICogICB0aGlzLmIgPSAyO1xuICogfVxuICpcbiAqIEZvby5wcm90b3R5cGUuYyA9IDM7XG4gKlxuICogXy5mb3JJbihuZXcgRm9vLCBmdW5jdGlvbih2YWx1ZSwga2V5KSB7XG4gKiAgIGNvbnNvbGUubG9nKGtleSk7XG4gKiB9KTtcbiAqIC8vID0+IExvZ3MgJ2EnLCAnYicsIHRoZW4gJ2MnIChpdGVyYXRpb24gb3JkZXIgaXMgbm90IGd1YXJhbnRlZWQpLlxuICovXG5mdW5jdGlvbiBmb3JJbihvYmplY3QsIGl0ZXJhdGVlKSB7XG4gIHJldHVybiBvYmplY3QgPT0gbnVsbFxuICAgID8gb2JqZWN0XG4gICAgOiBiYXNlRm9yKG9iamVjdCwgY2FzdEZ1bmN0aW9uKGl0ZXJhdGVlKSwga2V5c0luKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBmb3JJbjtcbiIsIi8qKlxuICogVGhpcyBtZXRob2QgcmV0dXJucyB0aGUgZmlyc3QgYXJndW1lbnQgaXQgcmVjZWl2ZXMuXG4gKlxuICogQHN0YXRpY1xuICogQHNpbmNlIDAuMS4wXG4gKiBAbWVtYmVyT2YgX1xuICogQGNhdGVnb3J5IFV0aWxcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgQW55IHZhbHVlLlxuICogQHJldHVybnMgeyp9IFJldHVybnMgYHZhbHVlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogdmFyIG9iamVjdCA9IHsgJ2EnOiAxIH07XG4gKlxuICogY29uc29sZS5sb2coXy5pZGVudGl0eShvYmplY3QpID09PSBvYmplY3QpO1xuICogLy8gPT4gdHJ1ZVxuICovXG5mdW5jdGlvbiBpZGVudGl0eSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWU7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaWRlbnRpdHk7XG4iLCJ2YXIgYmFzZUlzQXJndW1lbnRzID0gcmVxdWlyZSgnLi9fYmFzZUlzQXJndW1lbnRzJyksXG4gICAgaXNPYmplY3RMaWtlID0gcmVxdWlyZSgnLi9pc09iamVjdExpa2UnKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgcHJvcGVydHlJc0VudW1lcmFibGUgPSBvYmplY3RQcm90by5wcm9wZXJ0eUlzRW51bWVyYWJsZTtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBsaWtlbHkgYW4gYGFyZ3VtZW50c2Agb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGBhcmd1bWVudHNgIG9iamVjdCxcbiAqICBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcmd1bWVudHMoZnVuY3Rpb24oKSB7IHJldHVybiBhcmd1bWVudHM7IH0oKSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0FyZ3VtZW50cyhbMSwgMiwgM10pO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzQXJndW1lbnRzID0gYmFzZUlzQXJndW1lbnRzKGZ1bmN0aW9uKCkgeyByZXR1cm4gYXJndW1lbnRzOyB9KCkpID8gYmFzZUlzQXJndW1lbnRzIDogZnVuY3Rpb24odmFsdWUpIHtcbiAgcmV0dXJuIGlzT2JqZWN0TGlrZSh2YWx1ZSkgJiYgaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwgJ2NhbGxlZScpICYmXG4gICAgIXByb3BlcnR5SXNFbnVtZXJhYmxlLmNhbGwodmFsdWUsICdjYWxsZWUnKTtcbn07XG5cbm1vZHVsZS5leHBvcnRzID0gaXNBcmd1bWVudHM7XG4iLCIvKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYW4gYEFycmF5YCBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gYXJyYXksIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0FycmF5KFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0FycmF5KGRvY3VtZW50LmJvZHkuY2hpbGRyZW4pO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzQXJyYXkoJ2FiYycpO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzQXJyYXkoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc0FycmF5ID0gQXJyYXkuaXNBcnJheTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc0FycmF5O1xuIiwidmFyIGlzRnVuY3Rpb24gPSByZXF1aXJlKCcuL2lzRnVuY3Rpb24nKSxcbiAgICBpc0xlbmd0aCA9IHJlcXVpcmUoJy4vaXNMZW5ndGgnKTtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhcnJheS1saWtlLiBBIHZhbHVlIGlzIGNvbnNpZGVyZWQgYXJyYXktbGlrZSBpZiBpdCdzXG4gKiBub3QgYSBmdW5jdGlvbiBhbmQgaGFzIGEgYHZhbHVlLmxlbmd0aGAgdGhhdCdzIGFuIGludGVnZXIgZ3JlYXRlciB0aGFuIG9yXG4gKiBlcXVhbCB0byBgMGAgYW5kIGxlc3MgdGhhbiBvciBlcXVhbCB0byBgTnVtYmVyLk1BWF9TQUZFX0lOVEVHRVJgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFycmF5LWxpa2UsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0FycmF5TGlrZShbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoZG9jdW1lbnQuYm9keS5jaGlsZHJlbik7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0FycmF5TGlrZSgnYWJjJyk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0FycmF5TGlrZShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNBcnJheUxpa2UodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlICE9IG51bGwgJiYgaXNMZW5ndGgodmFsdWUubGVuZ3RoKSAmJiAhaXNGdW5jdGlvbih2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNBcnJheUxpa2U7XG4iLCJ2YXIgcm9vdCA9IHJlcXVpcmUoJy4vX3Jvb3QnKSxcbiAgICBzdHViRmFsc2UgPSByZXF1aXJlKCcuL3N0dWJGYWxzZScpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGV4cG9ydHNgLiAqL1xudmFyIGZyZWVFeHBvcnRzID0gdHlwZW9mIGV4cG9ydHMgPT0gJ29iamVjdCcgJiYgZXhwb3J0cyAmJiAhZXhwb3J0cy5ub2RlVHlwZSAmJiBleHBvcnRzO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYG1vZHVsZWAuICovXG52YXIgZnJlZU1vZHVsZSA9IGZyZWVFeHBvcnRzICYmIHR5cGVvZiBtb2R1bGUgPT0gJ29iamVjdCcgJiYgbW9kdWxlICYmICFtb2R1bGUubm9kZVR5cGUgJiYgbW9kdWxlO1xuXG4vKiogRGV0ZWN0IHRoZSBwb3B1bGFyIENvbW1vbkpTIGV4dGVuc2lvbiBgbW9kdWxlLmV4cG9ydHNgLiAqL1xudmFyIG1vZHVsZUV4cG9ydHMgPSBmcmVlTW9kdWxlICYmIGZyZWVNb2R1bGUuZXhwb3J0cyA9PT0gZnJlZUV4cG9ydHM7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIEJ1ZmZlciA9IG1vZHVsZUV4cG9ydHMgPyByb290LkJ1ZmZlciA6IHVuZGVmaW5lZDtcblxuLyogQnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMgZm9yIHRob3NlIHdpdGggdGhlIHNhbWUgbmFtZSBhcyBvdGhlciBgbG9kYXNoYCBtZXRob2RzLiAqL1xudmFyIG5hdGl2ZUlzQnVmZmVyID0gQnVmZmVyID8gQnVmZmVyLmlzQnVmZmVyIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgYnVmZmVyLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4zLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgYnVmZmVyLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNCdWZmZXIobmV3IEJ1ZmZlcigyKSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0J1ZmZlcihuZXcgVWludDhBcnJheSgyKSk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNCdWZmZXIgPSBuYXRpdmVJc0J1ZmZlciB8fCBzdHViRmFsc2U7XG5cbm1vZHVsZS5leHBvcnRzID0gaXNCdWZmZXI7XG4iLCJ2YXIgYmFzZUdldFRhZyA9IHJlcXVpcmUoJy4vX2Jhc2VHZXRUYWcnKSxcbiAgICBpc09iamVjdCA9IHJlcXVpcmUoJy4vaXNPYmplY3QnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIGFzeW5jVGFnID0gJ1tvYmplY3QgQXN5bmNGdW5jdGlvbl0nLFxuICAgIGZ1bmNUYWcgPSAnW29iamVjdCBGdW5jdGlvbl0nLFxuICAgIGdlblRhZyA9ICdbb2JqZWN0IEdlbmVyYXRvckZ1bmN0aW9uXScsXG4gICAgcHJveHlUYWcgPSAnW29iamVjdCBQcm94eV0nO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYSBgRnVuY3Rpb25gIG9iamVjdC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIGZ1bmN0aW9uLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNGdW5jdGlvbihfKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzRnVuY3Rpb24oL2FiYy8pO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNGdW5jdGlvbih2YWx1ZSkge1xuICBpZiAoIWlzT2JqZWN0KHZhbHVlKSkge1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuICAvLyBUaGUgdXNlIG9mIGBPYmplY3QjdG9TdHJpbmdgIGF2b2lkcyBpc3N1ZXMgd2l0aCB0aGUgYHR5cGVvZmAgb3BlcmF0b3JcbiAgLy8gaW4gU2FmYXJpIDkgd2hpY2ggcmV0dXJucyAnb2JqZWN0JyBmb3IgdHlwZWQgYXJyYXlzIGFuZCBvdGhlciBjb25zdHJ1Y3RvcnMuXG4gIHZhciB0YWcgPSBiYXNlR2V0VGFnKHZhbHVlKTtcbiAgcmV0dXJuIHRhZyA9PSBmdW5jVGFnIHx8IHRhZyA9PSBnZW5UYWcgfHwgdGFnID09IGFzeW5jVGFnIHx8IHRhZyA9PSBwcm94eVRhZztcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0Z1bmN0aW9uO1xuIiwiLyoqIFVzZWQgYXMgcmVmZXJlbmNlcyBmb3IgdmFyaW91cyBgTnVtYmVyYCBjb25zdGFudHMuICovXG52YXIgTUFYX1NBRkVfSU5URUdFUiA9IDkwMDcxOTkyNTQ3NDA5OTE7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBhcnJheS1saWtlIGxlbmd0aC5cbiAqXG4gKiAqKk5vdGU6KiogVGhpcyBtZXRob2QgaXMgbG9vc2VseSBiYXNlZCBvblxuICogW2BUb0xlbmd0aGBdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLXRvbGVuZ3RoKS5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGxlbmd0aCwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzTGVuZ3RoKDMpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNMZW5ndGgoTnVtYmVyLk1JTl9WQUxVRSk7XG4gKiAvLyA9PiBmYWxzZVxuICpcbiAqIF8uaXNMZW5ndGgoSW5maW5pdHkpO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzTGVuZ3RoKCczJyk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc0xlbmd0aCh2YWx1ZSkge1xuICByZXR1cm4gdHlwZW9mIHZhbHVlID09ICdudW1iZXInICYmXG4gICAgdmFsdWUgPiAtMSAmJiB2YWx1ZSAlIDEgPT0gMCAmJiB2YWx1ZSA8PSBNQVhfU0FGRV9JTlRFR0VSO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzTGVuZ3RoO1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyB0aGVcbiAqIFtsYW5ndWFnZSB0eXBlXShodHRwOi8vd3d3LmVjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtZWNtYXNjcmlwdC1sYW5ndWFnZS10eXBlcylcbiAqIG9mIGBPYmplY3RgLiAoZS5nLiBhcnJheXMsIGZ1bmN0aW9ucywgb2JqZWN0cywgcmVnZXhlcywgYG5ldyBOdW1iZXIoMClgLCBhbmQgYG5ldyBTdHJpbmcoJycpYClcbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBvYmplY3QsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc09iamVjdCh7fSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdChbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3QoXy5ub29wKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0KG51bGwpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNPYmplY3QodmFsdWUpIHtcbiAgdmFyIHR5cGUgPSB0eXBlb2YgdmFsdWU7XG4gIHJldHVybiB2YWx1ZSAhPSBudWxsICYmICh0eXBlID09ICdvYmplY3QnIHx8IHR5cGUgPT0gJ2Z1bmN0aW9uJyk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNPYmplY3Q7XG4iLCIvKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIG9iamVjdC1saWtlLiBBIHZhbHVlIGlzIG9iamVjdC1saWtlIGlmIGl0J3Mgbm90IGBudWxsYFxuICogYW5kIGhhcyBhIGB0eXBlb2ZgIHJlc3VsdCBvZiBcIm9iamVjdFwiLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIG9iamVjdC1saWtlLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKHt9KTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKF8ubm9vcCk7XG4gKiAvLyA9PiBmYWxzZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKG51bGwpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNPYmplY3RMaWtlKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPSBudWxsICYmIHR5cGVvZiB2YWx1ZSA9PSAnb2JqZWN0Jztcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc09iamVjdExpa2U7XG4iLCJ2YXIgYmFzZUlzVHlwZWRBcnJheSA9IHJlcXVpcmUoJy4vX2Jhc2VJc1R5cGVkQXJyYXknKSxcbiAgICBiYXNlVW5hcnkgPSByZXF1aXJlKCcuL19iYXNlVW5hcnknKSxcbiAgICBub2RlVXRpbCA9IHJlcXVpcmUoJy4vX25vZGVVdGlsJyk7XG5cbi8qIE5vZGUuanMgaGVscGVyIHJlZmVyZW5jZXMuICovXG52YXIgbm9kZUlzVHlwZWRBcnJheSA9IG5vZGVVdGlsICYmIG5vZGVVdGlsLmlzVHlwZWRBcnJheTtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGEgdHlwZWQgYXJyYXkuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAzLjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB0eXBlZCBhcnJheSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzVHlwZWRBcnJheShuZXcgVWludDhBcnJheSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc1R5cGVkQXJyYXkoW10pO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzVHlwZWRBcnJheSA9IG5vZGVJc1R5cGVkQXJyYXkgPyBiYXNlVW5hcnkobm9kZUlzVHlwZWRBcnJheSkgOiBiYXNlSXNUeXBlZEFycmF5O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzVHlwZWRBcnJheTtcbiIsInZhciBhcnJheUxpa2VLZXlzID0gcmVxdWlyZSgnLi9fYXJyYXlMaWtlS2V5cycpLFxuICAgIGJhc2VLZXlzSW4gPSByZXF1aXJlKCcuL19iYXNlS2V5c0luJyksXG4gICAgaXNBcnJheUxpa2UgPSByZXF1aXJlKCcuL2lzQXJyYXlMaWtlJyk7XG5cbi8qKlxuICogQ3JlYXRlcyBhbiBhcnJheSBvZiB0aGUgb3duIGFuZCBpbmhlcml0ZWQgZW51bWVyYWJsZSBwcm9wZXJ0eSBuYW1lcyBvZiBgb2JqZWN0YC5cbiAqXG4gKiAqKk5vdGU6KiogTm9uLW9iamVjdCB2YWx1ZXMgYXJlIGNvZXJjZWQgdG8gb2JqZWN0cy5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDMuMC4wXG4gKiBAY2F0ZWdvcnkgT2JqZWN0XG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICogQGV4YW1wbGVcbiAqXG4gKiBmdW5jdGlvbiBGb28oKSB7XG4gKiAgIHRoaXMuYSA9IDE7XG4gKiAgIHRoaXMuYiA9IDI7XG4gKiB9XG4gKlxuICogRm9vLnByb3RvdHlwZS5jID0gMztcbiAqXG4gKiBfLmtleXNJbihuZXcgRm9vKTtcbiAqIC8vID0+IFsnYScsICdiJywgJ2MnXSAoaXRlcmF0aW9uIG9yZGVyIGlzIG5vdCBndWFyYW50ZWVkKVxuICovXG5mdW5jdGlvbiBrZXlzSW4ob2JqZWN0KSB7XG4gIHJldHVybiBpc0FycmF5TGlrZShvYmplY3QpID8gYXJyYXlMaWtlS2V5cyhvYmplY3QsIHRydWUpIDogYmFzZUtleXNJbihvYmplY3QpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGtleXNJbjtcbiIsIi8qKlxuICogVGhpcyBtZXRob2QgcmV0dXJucyBgZmFsc2VgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4xMy4wXG4gKiBAY2F0ZWdvcnkgVXRpbFxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy50aW1lcygyLCBfLnN0dWJGYWxzZSk7XG4gKiAvLyA9PiBbZmFsc2UsIGZhbHNlXVxuICovXG5mdW5jdGlvbiBzdHViRmFsc2UoKSB7XG4gIHJldHVybiBmYWxzZTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBzdHViRmFsc2U7XG4iLCIvKiBqc2hpbnQgYnJvd3Nlcjp0cnVlICovXHJcbi8qIGdsb2JhbHMgX19PUFRJT05TX186dHJ1ZSAqL1xyXG5cclxuaW1wb3J0IExhdmFKcyBmcm9tICcuL2xhdmEvTGF2YSc7XHJcbmltcG9ydCB7IGRvbUxvYWRlZCB9IGZyb20gJy4vbGF2YS9VdGlscyc7XHJcblxyXG4vKipcclxuICogQXNzaWduIHRoZSBMYXZhLmpzIG1vZHVsZSB0byB0aGUgd2luZG93IGFuZFxyXG4gKiBsZXQgJGxhdmEgYmUgYW4gYWxpYXMgdG8gdGhlIG1vZHVsZS5cclxuICovXHJcbndpbmRvdy5sYXZhID0gbmV3IExhdmFKcygpO1xyXG5cclxuLyoqXHJcbiAqIElmIExhdmEuanMgd2FzIGxvYWRlZCBmcm9tIExhdmFjaGFydHMsIHRoZSBfX09QVElPTlNfX1xyXG4gKiBwbGFjZWhvbGRlciB3aWxsIGJlIGEgSlNPTiBvYmplY3Qgb2Ygb3B0aW9ucyB0aGF0XHJcbiAqIHdlcmUgc2V0IHNlcnZlci1zaWRlLlxyXG4gKi9cclxuaWYgKHR5cGVvZiBfX09QVElPTlNfXyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgIHdpbmRvdy5sYXZhLm9wdGlvbnMgPSBfX09QVElPTlNfXztcclxufVxyXG5cclxuLyoqXHJcbiAqIElmIExhdmEuanMgd2FzIHNldCB0byBhdXRvX3J1biB0aGVuIG9uY2UgdGhlIERPTVxyXG4gKiBpcyByZWFkeSwgcmVuZGVyaW5nIHdpbGwgYmVnaW4uXHJcbiAqL1xyXG5pZiAod2luZG93LmxhdmEub3B0aW9ucy5hdXRvX3J1biA9PT0gdHJ1ZSkge1xyXG4gICAgZG9tTG9hZGVkKCkudGhlbigoKSA9PiB7XHJcbiAgICAgICAgd2luZG93LmxhdmEucnVuKCk7XHJcbiAgICB9KTtcclxufVxyXG4iLCIvKipcclxuICogQ2hhcnQgbW9kdWxlXHJcbiAqXHJcbiAqIEBjbGFzcyAgICAgQ2hhcnRcclxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmltcG9ydCBfZm9ySW4gZnJvbSAnbG9kYXNoL2ZvckluJztcclxuaW1wb3J0IHsgUmVuZGVyYWJsZSB9IGZyb20gJy4vUmVuZGVyYWJsZSc7XHJcbmltcG9ydCB7IHN0cmluZ1RvRnVuY3Rpb24gfSBmcm9tICcuL1V0aWxzJztcclxuXHJcbi8qKlxyXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgdHlwZSAgICAgIC0gVHlwZSBvZiBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgR29vZ2xlIGNoYXJ0IHBhY2thZ2UgdG8gbG9hZC5cclxuICogQHByb3BlcnR5IHtib29sZWFufSAgcG5nT3V0cHV0IC0gU2hvdWxkIHRoZSBjaGFydCBiZSBkaXNwbGF5ZWQgYXMgYSBQTkcuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZXZlbnRzICAgIC0gRXZlbnRzIGFuZCBjYWxsYmFja3MgdG8gYXBwbHkgdG8gdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBmb3JtYXRzICAgLSBGb3JtYXR0ZXJzIHRvIGFwcGx5IHRvIHRoZSBjaGFydCBkYXRhLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gQ3JlYXRlcyBpZGVudGlmaWNhdGlvbiBzdHJpbmcgZm9yIHRoZSBjaGFydC5cclxuICovXHJcbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENoYXJ0IGV4dGVuZHMgUmVuZGVyYWJsZVxyXG57XHJcbiAgICAvKipcclxuICAgICAqIENoYXJ0IENsYXNzXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBpcyB0aGUgamF2YXNjcmlwdCB2ZXJzaW9uIG9mIGEgbGF2YWNoYXJ0IHdpdGggbWV0aG9kcyBmb3IgaW50ZXJhY3Rpbmcgd2l0aFxyXG4gICAgICogdGhlIGdvb2dsZSBjaGFydCBhbmQgdGhlIFBIUCBsYXZhY2hhcnQgb3V0cHV0LlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBqc29uXHJcbiAgICAgKiBAY29uc3RydWN0b3JcclxuICAgICAqL1xyXG4gICAgY29uc3RydWN0b3IgKGpzb24pIHtcclxuICAgICAgICBzdXBlcihqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy50eXBlICAgID0ganNvbi50eXBlO1xyXG4gICAgICAgIHRoaXMuY2xhc3MgICA9IGpzb24uY2xhc3M7XHJcbiAgICAgICAgdGhpcy5mb3JtYXRzID0ganNvbi5mb3JtYXRzO1xyXG5cclxuICAgICAgICB0aGlzLmV2ZW50cyAgICA9IHR5cGVvZiBqc29uLmV2ZW50cyA9PT0gJ29iamVjdCcgPyBqc29uLmV2ZW50cyA6IG51bGw7XHJcbiAgICAgICAgdGhpcy5wbmdPdXRwdXQgPSB0eXBlb2YganNvbi5wbmdPdXRwdXQgPT09ICd1bmRlZmluZWQnID8gZmFsc2UgOiBCb29sZWFuKGpzb24ucG5nT3V0cHV0KTtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogQW55IGRlcGVuZGVuY3kgb24gd2luZG93Lmdvb2dsZSBtdXN0IGJlIGluIHRoZSByZW5kZXIgc2NvcGUuXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5yZW5kZXIgPSAoKSA9PiB7XHJcbiAgICAgICAgICAgIHRoaXMuc2V0RGF0YShqc29uLmRhdGF0YWJsZSk7XHJcblxyXG4gICAgICAgICAgICBsZXQgQ2hhcnRDbGFzcyA9IHN0cmluZ1RvRnVuY3Rpb24odGhpcy5jbGFzcywgd2luZG93KTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IENoYXJ0Q2xhc3ModGhpcy5lbGVtZW50KTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLmZvcm1hdHMpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMuYXBwbHlGb3JtYXRzKCk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLmV2ZW50cykge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5fYXR0YWNoRXZlbnRzKCk7XHJcbiAgICAgICAgICAgICAgICAvLyBUT0RPOiBJZGVhLi4uIGZvcndhcmQgZXZlbnRzIHRvIGJlIGxpc3RlbmFibGUgYnkgdGhlIHVzZXIsIGluc3RlYWQgb2YgaGF2aW5nIHRoZSB1c2VyIGRlZmluZSB0aGVtIGFzIGEgc3RyaW5nIGNhbGxiYWNrLlxyXG4gICAgICAgICAgICAgICAgLy8gbGF2YS5nZXQoJ015Q29vbENoYXJ0Jykub24oJ3JlYWR5JywgZnVuY3Rpb24oZGF0YSkge1xyXG4gICAgICAgICAgICAgICAgLy8gICAgIGNvbnNvbGUubG9nKHRoaXMpOyAgLy8gZ0NoYXJ0XHJcbiAgICAgICAgICAgICAgICAvLyB9KTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgdGhpcy5kcmF3KCk7XHJcblxyXG4gICAgICAgICAgICBpZiAodGhpcy5wbmdPdXRwdXQpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMuZHJhd1BuZygpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIERyYXdzIHRoZSBjaGFydCBhcyBhIFBORyBpbnN0ZWFkIG9mIHRoZSBzdGFuZGFyZCBTVkdcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAZXh0ZXJuYWwgXCJjaGFydC5nZXRJbWFnZVVSSVwiXHJcbiAgICAgKiBAc2VlIHtAbGluayBodHRwczovL2RldmVsb3BlcnMuZ29vZ2xlLmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL3ByaW50aW5nfFByaW50aW5nIFBORyBDaGFydHN9XHJcbiAgICAgKi9cclxuICAgIGRyYXdQbmcoKSB7XHJcbiAgICAgICAgbGV0IGltZyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2ltZycpO1xyXG4gICAgICAgICAgICBpbWcuc3JjID0gdGhpcy5nY2hhcnQuZ2V0SW1hZ2VVUkkoKTtcclxuXHJcbiAgICAgICAgdGhpcy5lbGVtZW50LmlubmVySFRNTCA9ICcnO1xyXG4gICAgICAgIHRoaXMuZWxlbWVudC5hcHBlbmRDaGlsZChpbWcpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQXBwbHkgdGhlIGZvcm1hdHMgdG8gdGhlIERhdGFUYWJsZVxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7QXJyYXl9IGZvcm1hdHNcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgYXBwbHlGb3JtYXRzKGZvcm1hdHMpIHtcclxuICAgICAgICBpZiAoISBmb3JtYXRzKSB7XHJcbiAgICAgICAgICAgIGZvcm1hdHMgPSB0aGlzLmZvcm1hdHM7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBmb3IgKGxldCBmb3JtYXQgb2YgZm9ybWF0cykge1xyXG4gICAgICAgICAgICBsZXQgZm9ybWF0dGVyID0gbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uW2Zvcm1hdC50eXBlXShmb3JtYXQub3B0aW9ucyk7XHJcblxyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhgW2xhdmEuanNdIENvbHVtbiBpbmRleCBbJHtmb3JtYXQuaW5kZXh9XSBmb3JtYXR0ZWQgd2l0aDpgLCBmb3JtYXR0ZXIpO1xyXG5cclxuICAgICAgICAgICAgZm9ybWF0dGVyLmZvcm1hdCh0aGlzLmRhdGEsIGZvcm1hdC5pbmRleCk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQXR0YWNoIHRoZSBkZWZpbmVkIGNoYXJ0IGV2ZW50IGhhbmRsZXJzLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKi9cclxuICAgIF9hdHRhY2hFdmVudHMoKSB7XHJcbiAgICAgICAgbGV0ICRjaGFydCA9IHRoaXM7XHJcblxyXG4gICAgICAgIF9mb3JJbih0aGlzLmV2ZW50cywgZnVuY3Rpb24gKGNhbGxiYWNrLCBldmVudCkge1xyXG4gICAgICAgICAgICBsZXQgY29udGV4dCA9IHdpbmRvdztcclxuICAgICAgICAgICAgbGV0IGZ1bmMgPSBjYWxsYmFjaztcclxuXHJcbiAgICAgICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICdvYmplY3QnKSB7XHJcbiAgICAgICAgICAgICAgICBjb250ZXh0ID0gY29udGV4dFtjYWxsYmFja1swXV07XHJcbiAgICAgICAgICAgICAgICBmdW5jID0gY2FsbGJhY2tbMV07XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gVGhlIFwiJHskY2hhcnQudXVpZCgpfTo6JHtldmVudH1cIiBldmVudCB3aWxsIGJlIGhhbmRsZWQgYnkgXCIke2Z1bmN9XCIgaW4gdGhlIGNvbnRleHRgLCBjb250ZXh0KTtcclxuXHJcbiAgICAgICAgICAgIC8qKlxyXG4gICAgICAgICAgICAgKiBTZXQgdGhlIGNvbnRleHQgb2YgXCJ0aGlzXCIgd2l0aGluIHRoZSB1c2VyIHByb3ZpZGVkIGNhbGxiYWNrIHRvIHRoZVxyXG4gICAgICAgICAgICAgKiBjaGFydCB0aGF0IGZpcmVkIHRoZSBldmVudCB3aGlsZSBwcm92aWRpbmcgdGhlIGRhdGF0YWJsZSBvZiB0aGUgY2hhcnRcclxuICAgICAgICAgICAgICogdG8gdGhlIGNhbGxiYWNrIGFzIGFuIGFyZ3VtZW50LlxyXG4gICAgICAgICAgICAgKi9cclxuICAgICAgICAgICAgZ29vZ2xlLnZpc3VhbGl6YXRpb24uZXZlbnRzLmFkZExpc3RlbmVyKCRjaGFydC5nY2hhcnQsIGV2ZW50LCBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgIGNvbnN0IGNhbGxiYWNrID0gY29udGV4dFtmdW5jXS5iaW5kKCRjaGFydC5nY2hhcnQpO1xyXG5cclxuICAgICAgICAgICAgICAgIGNhbGxiYWNrKCRjaGFydC5kYXRhKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoqXHJcbiAqIERhc2hib2FyZCBtb2R1bGVcclxuICpcclxuICogQGNsYXNzICAgICBEYXNoYm9hcmRcclxuICogQG1vZHVsZSAgICBsYXZhL0Rhc2hib2FyZFxyXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxyXG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXHJcbiAqIEBsaWNlbnNlICAgTUlUXHJcbiAqL1xyXG5pbXBvcnQgeyBSZW5kZXJhYmxlIH0gZnJvbSAnLi9SZW5kZXJhYmxlJztcclxuXHJcbi8qKlxyXG4gKiBEYXNoYm9hcmQgY2xhc3NcclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgRGFzaGJvYXJkXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIGxhYmVsICAgICAtIExhYmVsIGZvciB0aGUgRGFzaGJvYXJkLlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIHZpc3VhbGl6YXRpb24gKERhc2hib2FyZCkuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGVsZW1lbnQgICAtIEh0bWwgZWxlbWVudCBpbiB3aGljaCB0byByZW5kZXIgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBwYWNrYWdlICAgLSBUeXBlIG9mIHZpc3VhbGl6YXRpb24gcGFja2FnZSB0byBsb2FkLlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBkYXRhICAgICAgLSBEYXRhdGFibGUgZm9yIHRoZSBEYXNoYm9hcmQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIG9wdGlvbnMgICAtIENvbmZpZ3VyYXRpb24gb3B0aW9ucy5cclxuICogQHByb3BlcnR5IHtBcnJheX0gICAgYmluZGluZ3MgIC0gQ2hhcnQgYW5kIENvbnRyb2wgYmluZGluZ3MuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIERhc2hib2FyZC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gVW5pcXVlIGlkZW50aWZpZXIgZm9yIHRoZSBEYXNoYm9hcmQuXHJcbiAqL1xyXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBEYXNoYm9hcmQgZXh0ZW5kcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIGNvbnN0cnVjdG9yKGpzb24pIHtcclxuICAgICAgICBzdXBlcihqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy50eXBlICAgICA9ICdEYXNoYm9hcmQnO1xyXG4gICAgICAgIHRoaXMuYmluZGluZ3MgPSBqc29uLmJpbmRpbmdzO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBbnkgZGVwZW5kZW5jeSBvbiB3aW5kb3cuZ29vZ2xlIG11c3QgYmUgaW4gdGhlIHJlbmRlciBzY29wZS5cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcclxuICAgICAgICAgICAgdGhpcy5zZXREYXRhKGpzb24uZGF0YXRhYmxlKTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhc2hib2FyZCh0aGlzLmVsZW1lbnQpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5fYXR0YWNoQmluZGluZ3MoKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLmV2ZW50cykge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5fYXR0YWNoRXZlbnRzKCk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHRoaXMuZHJhdygpO1xyXG4gICAgICAgIH07XHJcbiAgICB9XHJcblxyXG4gICAgLy8gQFRPRE86IHRoaXMgbmVlZHMgdG8gYmUgbW9kaWZpZWQgZm9yIHRoZSBvdGhlciB0eXBlcyBvZiBiaW5kaW5ncy5cclxuXHJcbiAgICAvKipcclxuICAgICAqIFByb2Nlc3MgYW5kIGF0dGFjaCB0aGUgYmluZGluZ3MgdG8gdGhlIGRhc2hib2FyZC5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICovXHJcbiAgICBfYXR0YWNoQmluZGluZ3MoKSB7XHJcbiAgICAgICAgZm9yIChsZXQgYmluZGluZyBvZiB0aGlzLmJpbmRpbmdzKSB7XHJcbiAgICAgICAgICAgIGxldCBjb250cm9sV3JhcHMgPSBbXTtcclxuICAgICAgICAgICAgbGV0IGNoYXJ0V3JhcHMgPSBbXTtcclxuXHJcbiAgICAgICAgICAgIGZvciAobGV0IGNvbnRyb2xXcmFwIG9mIGJpbmRpbmcuY29udHJvbFdyYXBwZXJzKSB7XHJcbiAgICAgICAgICAgICAgICBjb250cm9sV3JhcHMucHVzaChcclxuICAgICAgICAgICAgICAgICAgICBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uQ29udHJvbFdyYXBwZXIoY29udHJvbFdyYXApXHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBmb3IgKGxldCBjaGFydFdyYXAgb2YgYmluZGluZy5jaGFydFdyYXBwZXJzKSB7XHJcbiAgICAgICAgICAgICAgICBjaGFydFdyYXBzLnB1c2goXHJcbiAgICAgICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkNoYXJ0V3JhcHBlcihjaGFydFdyYXApXHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLmdjaGFydC5iaW5kKGNvbnRyb2xXcmFwcywgY2hhcnRXcmFwcyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBFcnJvcnMgbW9kdWxlXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9FcnJvcnNcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuY2xhc3MgTGF2YUVycm9yIGV4dGVuZHMgRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKG1lc3NhZ2UpIHtcclxuICAgICAgICBzdXBlcigpO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgICAgPSAnTGF2YUVycm9yJztcclxuICAgICAgICB0aGlzLm1lc3NhZ2UgPSAobWVzc2FnZSB8fCAnJyk7XHJcbiAgICB9O1xyXG59XHJcblxyXG4vKipcclxuICogSW52YWxpZENhbGxiYWNrIEVycm9yXHJcbiAqXHJcbiAqIHRocm93biB3aGVuIHdoZW4gYW55dGhpbmcgYnV0IGEgZnVuY3Rpb24gaXMgZ2l2ZW4gYXMgYSBjYWxsYmFja1xyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgSW52YWxpZENhbGxiYWNrIGV4dGVuZHMgTGF2YUVycm9yXHJcbntcclxuICAgIGNvbnN0cnVjdG9yIChjYWxsYmFjaykge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gXCIke3R5cGVvZiBjYWxsYmFja31cIiBpcyBub3QgYSB2YWxpZCBjYWxsYmFjay5gKTtcclxuXHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRDYWxsYmFjayc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBJbnZhbGlkTGFiZWwgRXJyb3JcclxuICpcclxuICogVGhyb3duIHdoZW4gd2hlbiBhbnl0aGluZyBidXQgYSBzdHJpbmcgaXMgZ2l2ZW4gYXMgYSBsYWJlbC5cclxuICpcclxuICogQHR5cGUge2Z1bmN0aW9ufVxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIEludmFsaWRMYWJlbCBleHRlbmRzIExhdmFFcnJvclxyXG57XHJcbiAgICBjb25zdHJ1Y3RvciAobGFiZWwpIHtcclxuICAgICAgICBzdXBlcihgW2xhdmEuanNdIFwiJHt0eXBlb2YgbGFiZWx9XCIgaXMgbm90IGEgdmFsaWQgbGFiZWwuYCk7XHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRMYWJlbCc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBFbGVtZW50SWROb3RGb3VuZCBFcnJvclxyXG4gKlxyXG4gKiBUaHJvd24gd2hlbiB3aGVuIGFueXRoaW5nIGJ1dCBhIHN0cmluZyBpcyBnaXZlbiBhcyBhIGxhYmVsLlxyXG4gKlxyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgRWxlbWVudElkTm90Rm91bmQgZXh0ZW5kcyBMYXZhRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGVsZW1JZCkge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gRE9NIG5vZGUgd2hlcmUgaWQ9XCIke2VsZW1JZH1cIiB3YXMgbm90IGZvdW5kLmApO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgPSAnRWxlbWVudElkTm90Rm91bmQnO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qIGpzaGludCBicm93c2VyOnRydWUgKi9cclxuLyogZ2xvYmFscyBnb29nbGU6dHJ1ZSAqL1xyXG5cclxuLyoqXHJcbiAqIGxhdmEuanMgbW9kdWxlXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9MYXZhXHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBodHRwOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvTUlUIE1JVFxyXG4gKi9cclxuaW1wb3J0IF9mb3JJbiBmcm9tICdsb2Rhc2gvZm9ySW4nO1xyXG5pbXBvcnQgRXZlbnRFbWl0dGVyIGZyb20gJ2V2ZW50cyc7XHJcbmltcG9ydCBDaGFydCBmcm9tICcuL0NoYXJ0JztcclxuaW1wb3J0IERhc2hib2FyZCBmcm9tICcuL0Rhc2hib2FyZCc7XHJcbmltcG9ydCBkZWZhdWx0T3B0aW9ucyBmcm9tICcuL09wdGlvbnMnO1xyXG5pbXBvcnQgeyBub29wLCBhZGRFdmVudCB9IGZyb20gJy4vVXRpbHMnO1xyXG5pbXBvcnQgeyBJbnZhbGlkQ2FsbGJhY2ssIFJlbmRlcmFibGVOb3RGb3VuZCB9IGZyb20gJy4vRXJyb3JzJ1xyXG5cclxuXHJcbi8qKlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICAgICAgICAgICAgVkVSU0lPTlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICAgICAgICAgICAgR09PR0xFX0FQSV9WRVJTSU9OXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgICAgICAgICAgICBHT09HTEVfTE9BREVSX1VSTFxyXG4gKiBAcHJvcGVydHkge0NoYXJ0fSAgICAgICAgICAgICAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtEYXNoYm9hcmR9ICAgICAgICAgIERhc2hib2FyZFxyXG4gKiBAcHJvcGVydHkge29iamVjdH0gICAgICAgICAgICAgb3B0aW9uc1xyXG4gKiBAcHJvcGVydHkge2Z1bmN0aW9ufSAgICAgICAgICAgX3JlYWR5Q2FsbGJhY2tcclxuICogQHByb3BlcnR5IHtBcnJheS48c3RyaW5nPn0gICAgIF9wYWNrYWdlc1xyXG4gKiBAcHJvcGVydHkge0FycmF5LjxSZW5kZXJhYmxlPn0gX3JlbmRlcmFibGVzXHJcbiAqL1xyXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMYXZhSnMgZXh0ZW5kcyBFdmVudEVtaXR0ZXJcclxue1xyXG4gICAgY29uc3RydWN0b3IobmV3T3B0aW9ucykge1xyXG4gICAgICAgIHN1cGVyKCk7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFZlcnNpb24gb2YgdGhlIExhdmEuanMgbW9kdWxlLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge3N0cmluZ31cclxuICAgICAgICAgKiBAcHVibGljXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5WRVJTSU9OID0gJzQuMC4wJztcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogVmVyc2lvbiBvZiB0aGUgR29vZ2xlIGNoYXJ0cyBBUEkgdG8gbG9hZC5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OID0gJ2N1cnJlbnQnO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBVcmxzIHRvIEdvb2dsZSdzIHN0YXRpYyBsb2FkZXJcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0xPQURFUl9VUkwgPSAnaHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vY2hhcnRzL2xvYWRlci5qcyc7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIENoYXJ0IG1vZHVsZSB3aXRoaW4gTGF2YS5qc1xyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge0NoYXJ0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkNoYXJ0ID0gQ2hhcnQ7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIERhc2hib2FyZCBtb2R1bGUgd2l0aGluIExhdmEuanNcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtEYXNoYm9hcmR9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuRGFzaGJvYXJkID0gRGFzaGJvYXJkO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBKU09OIG9iamVjdCBvZiBjb25maWcgaXRlbXMuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7T2JqZWN0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLm9wdGlvbnMgPSBuZXdPcHRpb25zIHx8IGRlZmF1bHRPcHRpb25zO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBSZWZlcmVuY2UgdG8gdGhlIGdvb2dsZS52aXN1YWxpemF0aW9uIG9iamVjdC5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtnb29nbGUudmlzdWFsaXphdGlvbn1cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnZpc3VhbGl6YXRpb24gPSBudWxsO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBcnJheSBvZiB2aXN1YWxpemF0aW9uIHBhY2thZ2VzIGZvciBjaGFydHMgYW5kIGRhc2hib2FyZHMuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7QXJyYXkuPHN0cmluZz59XHJcbiAgICAgICAgICogQHByaXZhdGVcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLl9wYWNrYWdlcyA9IFtdO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBcnJheSBvZiBjaGFydHMgYW5kIGRhc2hib2FyZHMgc3RvcmVkIGluIHRoZSBtb2R1bGUuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7QXJyYXkuPFJlbmRlcmFibGU+fVxyXG4gICAgICAgICAqIEBwcml2YXRlXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5fcmVuZGVyYWJsZXMgPSBbXTtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogUmVhZHkgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIHdoZW4gdGhlIG1vZHVsZSBpcyBmaW5pc2hlZCBydW5uaW5nLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQGNhbGxiYWNrIF9yZWFkeUNhbGxiYWNrXHJcbiAgICAgICAgICogQHByaXZhdGVcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrID0gbm9vcDtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIENoYXJ0IGNsYXNzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7UmVuZGVyYWJsZX1cclxuICAgICAqL1xyXG4gICAgY3JlYXRlQ2hhcnQoanNvbikge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdDcmVhdGluZyBDaGFydCcsIGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLl9hZGRQYWNrYWdlcyhqc29uLnBhY2thZ2VzKTsgLy8gVE9ETzogbW92ZSB0aGlzIGludG8gdGhlIHN0b3JlIG1ldGhvZD9cclxuXHJcbiAgICAgICAgcmV0dXJuIG5ldyB0aGlzLkNoYXJ0KGpzb24pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBzZWUgY3JlYXRlQ2hhcnRcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBqc29uXHJcbiAgICAgKi9cclxuICAgIGFkZE5ld0NoYXJ0KGpzb24pIHsgLy9UT0RPOiByZW5hbWUgdG8gc3RvcmVOZXdDaGFydChqc29uKSA/XHJcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZUNoYXJ0KGpzb24pKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhIG5ldyBEYXNoYm9hcmQgd2l0aCBhIGdpdmVuIGxhYmVsLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIERhc2hib2FyZCBjbGFzcy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGpzb25cclxuICAgICAqIEByZXR1cm4ge0Rhc2hib2FyZH1cclxuICAgICAqL1xyXG4gICAgY3JlYXRlRGFzaGJvYXJkKGpzb24pIHtcclxuICAgICAgICBjb25zb2xlLmxvZygnQ3JlYXRpbmcgRGFzaGJvYXJkJywganNvbik7XHJcblxyXG4gICAgICAgIHRoaXMuX2FkZFBhY2thZ2VzKGpzb24ucGFja2FnZXMpO1xyXG5cclxuICAgICAgICByZXR1cm4gbmV3IHRoaXMuRGFzaGJvYXJkKGpzb24pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBEYXNoYm9hcmQgZnJvbSBhIEpTT04gcGF5bG9hZC5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgSlNPTiBwYXlsb2FkIGNvbWVzIGZyb20gdGhlIFBIUCBEYXNoYm9hcmQgY2xhc3MuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHNlZSBjcmVhdGVEYXNoYm9hcmRcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7RGFzaGJvYXJkfVxyXG4gICAgICovXHJcbiAgICBhZGROZXdEYXNoYm9hcmQoanNvbikgeyAvL1RPRE86IHJlbmFtZSB0byBzdG9yZU5ld0Rhc2hib2FyZChqc29uKSA/XHJcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZURhc2hib2FyZChqc29uKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBQdWJsaWMgbWV0aG9kIGZvciBpbml0aWFsaXppbmcgZ29vZ2xlIG9uIHRoZSBwYWdlLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgaW5pdCgpIHtcclxuICAgICAgICByZXR1cm4gdGhpcy5fbG9hZEdvb2dsZSgpLnRoZW4oKCkgPT4ge1xyXG4gICAgICAgICAgICB0aGlzLnZpc3VhbGl6YXRpb24gPSBnb29nbGUudmlzdWFsaXphdGlvbjtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJ1bnMgdGhlIExhdmEuanMgbW9kdWxlXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICovXHJcbiAgICBydW4oKSB7XHJcbiAgICAgICAgLy8gY29uc3QgJGxhdmEgPSB0aGlzO1xyXG5cclxuICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFJ1bm5pbmcuLi4nKTtcclxuICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIExvYWRpbmcgb3B0aW9uczonLCB0aGlzLm9wdGlvbnMpO1xyXG5cclxuICAgICAgICB0aGlzLl9hdHRhY2hSZWRyYXdIYW5kbGVyKCk7XHJcblxyXG4gICAgICAgIHRoaXMuaW5pdCgpLnRoZW4oKCkgPT4ge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEdvb2dsZSBpcyByZWFkeS4nKTtcclxuXHJcbiAgICAgICAgICAgIF9mb3JJbih0aGlzLl9yZW5kZXJhYmxlcywgcmVuZGVyYWJsZSA9PiB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhgW2xhdmEuanNdIFJlbmRlcmluZyAke3JlbmRlcmFibGUudXVpZCgpfWApO1xyXG5cclxuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUucmVuZGVyKCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBGaXJpbmcgXCJyZWFkeVwiIGV2ZW50LicpO1xyXG4gICAgICAgICAgICB0aGlzLmVtaXQoJ3JlYWR5Jyk7XHJcblxyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEV4ZWN1dGluZyBsYXZhLnJlYWR5KGNhbGxiYWNrKScpO1xyXG4gICAgICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBTdG9yZXMgYSByZW5kZXJhYmxlIGxhdmEgb2JqZWN0IHdpdGhpbiB0aGUgbW9kdWxlLlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7UmVuZGVyYWJsZX0gcmVuZGVyYWJsZVxyXG4gICAgICovXHJcbiAgICBzdG9yZShyZW5kZXJhYmxlKSB7XHJcbiAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBTdG9yaW5nICR7cmVuZGVyYWJsZS51dWlkKCl9YCk7XHJcblxyXG4gICAgICAgIHRoaXMuX3JlbmRlcmFibGVzW3JlbmRlcmFibGUubGFiZWxdID0gcmVuZGVyYWJsZTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJldHVybnMgdGhlIExhdmFDaGFydCBqYXZhc2NyaXB0IG9iamVjdHNcclxuICAgICAqXHJcbiAgICAgKlxyXG4gICAgICogVGhlIExhdmFDaGFydCBvYmplY3QgaG9sZHMgYWxsIHRoZSB1c2VyIGRlZmluZWQgcHJvcGVydGllcyBzdWNoIGFzIGRhdGEsIG9wdGlvbnMsIGZvcm1hdHMsXHJcbiAgICAgKiB0aGUgR29vZ2xlQ2hhcnQgb2JqZWN0LCBhbmQgcmVsYXRpdmUgbWV0aG9kcyBmb3IgaW50ZXJuYWwgdXNlLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBHb29nbGVDaGFydCBvYmplY3QgaXMgYXZhaWxhYmxlIGFzIFwiLmNoYXJ0XCIgZnJvbSB0aGUgcmV0dXJuZWQgTGF2YUNoYXJ0LlxyXG4gICAgICogSXQgY2FuIGJlIHVzZWQgdG8gYWNjZXNzIGFueSBvZiB0aGUgYXZhaWxhYmxlIG1ldGhvZHMgc3VjaCBhc1xyXG4gICAgICogZ2V0SW1hZ2VVUkkoKSBvciBnZXRDaGFydExheW91dEludGVyZmFjZSgpLlxyXG4gICAgICogU2VlIGh0dHBzOi8vZ29vZ2xlLWRldmVsb3BlcnMuYXBwc3BvdC5jb20vY2hhcnQvaW50ZXJhY3RpdmUvZG9jcy9nYWxsZXJ5L2xpbmVjaGFydCNtZXRob2RzXHJcbiAgICAgKiBmb3Igc29tZSBleGFtcGxlcyByZWxhdGl2ZSB0byBMaW5lQ2hhcnRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSAge3N0cmluZ30gICBsYWJlbFxyXG4gICAgICogQHBhcmFtICB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKiBAdGhyb3dzIEludmFsaWRMYWJlbFxyXG4gICAgICogQHRocm93cyBJbnZhbGlkQ2FsbGJhY2tcclxuICAgICAqIEB0aHJvd3MgUmVuZGVyYWJsZU5vdEZvdW5kXHJcbiAgICAgKi9cclxuICAgIGdldChsYWJlbCwgY2FsbGJhY2spIHtcclxuICAgICAgICBpZiAodHlwZW9mIGNhbGxiYWNrICE9PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIHRocm93IG5ldyBJbnZhbGlkQ2FsbGJhY2soY2FsbGJhY2spO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgbGV0IHJlbmRlcmFibGUgPSB0aGlzLl9yZW5kZXJhYmxlc1tsYWJlbF07XHJcblxyXG4gICAgICAgIGlmICghIHJlbmRlcmFibGUpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IFJlbmRlcmFibGVOb3RGb3VuZChsYWJlbCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBjYWxsYmFjayhyZW5kZXJhYmxlKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIEFzc2lnbnMgYSBjYWxsYmFjayBmb3Igd2hlbiB0aGUgY2hhcnRzIGFyZSByZWFkeSB0byBiZSBpbnRlcmFjdGVkIHdpdGguXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBpcyB1c2VkIHRvIHdyYXAgY2FsbHMgdG8gbGF2YS5sb2FkRGF0YSgpIG9yIGxhdmEubG9hZE9wdGlvbnMoKVxyXG4gICAgICogdG8gcHJvdGVjdCBhZ2FpbnN0IGFjY2Vzc2luZyBjaGFydHMgdGhhdCBhcmVuJ3QgbG9hZGVkIHlldFxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7ZnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIHJlYWR5KGNhbGxiYWNrKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRoaXMuX3JlYWR5Q2FsbGJhY2sgPSBjYWxsYmFjaztcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIExvYWRzIG5ldyBkYXRhIGludG8gdGhlIGNoYXJ0IGFuZCByZWRyYXdzLlxyXG4gICAgICpcclxuICAgICAqXHJcbiAgICAgKiBVc2VkIHdpdGggYW4gQUpBWCBjYWxsIHRvIGEgUEhQIG1ldGhvZCByZXR1cm5pbmcgRGF0YVRhYmxlLT50b0pzb24oKSxcclxuICAgICAqIGEgY2hhcnQgY2FuIGJlIGR5bmFtaWNhbGx5IHVwZGF0ZSBpbiBwYWdlLCB3aXRob3V0IHJlbG9hZHMuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGxhYmVsXHJcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30ganNvblxyXG4gICAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcclxuICAgICAqL1xyXG4gICAgbG9hZERhdGEobGFiZWwsIGpzb24sIGNhbGxiYWNrKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICAgICAgY2FsbGJhY2sgPSBub29wO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRoaXMuZ2V0KGxhYmVsLCBmdW5jdGlvbiAoY2hhcnQpIHtcclxuICAgICAgICAgICAgY2hhcnQuc2V0RGF0YShqc29uKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0eXBlb2YganNvbi5mb3JtYXRzICE9PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgICAgICAgICAgY2hhcnQuYXBwbHlGb3JtYXRzKGpzb24uZm9ybWF0cyk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGNoYXJ0LmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGNhbGxiYWNrKGNoYXJ0KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIExvYWRzIG5ldyBvcHRpb25zIGludG8gYSBjaGFydCBhbmQgcmVkcmF3cy5cclxuICAgICAqXHJcbiAgICAgKlxyXG4gICAgICogVXNlZCB3aXRoIGFuIEFKQVggY2FsbCwgb3IgamF2YXNjcmlwdCBldmVudHMsIHRvIGxvYWQgYSBuZXcgYXJyYXkgb2Ygb3B0aW9ucyBpbnRvIGEgY2hhcnQuXHJcbiAgICAgKiBUaGlzIGNhbiBiZSB1c2VkIHRvIHVwZGF0ZSBhIGNoYXJ0IGR5bmFtaWNhbGx5LCB3aXRob3V0IHJlbG9hZHMuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGxhYmVsXHJcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30ganNvblxyXG4gICAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcclxuICAgICAqL1xyXG4gICAgbG9hZE9wdGlvbnMobGFiZWwsIGpzb24sIGNhbGxiYWNrKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICAgICAgY2FsbGJhY2sgPSBjYWxsYmFjayB8fCBub29wO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRoaXMuZ2V0KGxhYmVsLCBmdW5jdGlvbiAoY2hhcnQpIHtcclxuICAgICAgICAgICAgY2hhcnQuc2V0T3B0aW9ucyhqc29uKTtcclxuICAgICAgICAgICAgY2hhcnQuZHJhdygpO1xyXG5cclxuICAgICAgICAgICAgY2FsbGJhY2soY2hhcnQpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogUmVkcmF3cyBhbGwgb2YgdGhlIHJlZ2lzdGVyZWQgY2hhcnRzIG9uIHNjcmVlbi5cclxuICAgICAqXHJcbiAgICAgKiBUaGlzIG1ldGhvZCBpcyBhdHRhY2hlZCB0byB0aGUgd2luZG93IHJlc2l6ZSBldmVudCB3aXRoIGRlYm91bmNpbmdcclxuICAgICAqIHRvIG1ha2UgdGhlIGNoYXJ0cyByZXNwb25zaXZlIHRvIHRoZSBicm93c2VyIHJlc2l6aW5nLlxyXG4gICAgICovXHJcbiAgICByZWRyYXdBbGwoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuX3JlbmRlcmFibGVzLmxlbmd0aCA9PT0gMCkge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhgW2xhdmEuanNdIE5vdGhpbmcgdG8gcmVkcmF3LmApO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gUmVkcmF3aW5nICR7dGhpcy5fcmVuZGVyYWJsZXMubGVuZ3RofSByZW5kZXJhYmxlcy5gKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGZvciAobGV0IHJlbmRlcmFibGUgb2YgdGhpcy5fcmVuZGVyYWJsZXMpIHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBSZWRyYXdpbmcgJHtyZW5kZXJhYmxlLnV1aWQoKX1gKTtcclxuXHJcbiAgICAgICAgICAgIGxldCByZWRyYXcgPSByZW5kZXJhYmxlLmRyYXcuYmluZChyZW5kZXJhYmxlKTtcclxuXHJcbiAgICAgICAgICAgIHJlZHJhdygpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBbGlhc2luZyBnb29nbGUudmlzdWFsaXphdGlvbi5hcnJheVRvRGF0YVRhYmxlIHRvIGxhdmEuYXJyYXlUb0RhdGFUYWJsZVxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7QXJyYXl9IGFyclxyXG4gICAgICogQHJldHVybiB7Z29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlfVxyXG4gICAgICovXHJcbiAgICBhcnJheVRvRGF0YVRhYmxlKGFycikge1xyXG4gICAgICAgIHJldHVybiB0aGlzLnZpc3VhbGl6YXRpb24uYXJyYXlUb0RhdGFUYWJsZShhcnIpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQWRkcyB0byB0aGUgbGlzdCBvZiBwYWNrYWdlcyB0aGF0IEdvb2dsZSBuZWVkcyB0byBsb2FkLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKiBAcGFyYW0ge0FycmF5fSBwYWNrYWdlc1xyXG4gICAgICogQHJldHVybiB7QXJyYXl9XHJcbiAgICAgKi9cclxuICAgIF9hZGRQYWNrYWdlcyhwYWNrYWdlcykge1xyXG4gICAgICAgIHRoaXMuX3BhY2thZ2VzID0gdGhpcy5fcGFja2FnZXMuY29uY2F0KHBhY2thZ2VzKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIEF0dGFjaCBhIGxpc3RlbmVyIHRvIHRoZSB3aW5kb3cgcmVzaXplIGV2ZW50IGZvciByZWRyYXdpbmcgdGhlIGNoYXJ0cy5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICovXHJcbiAgICBfYXR0YWNoUmVkcmF3SGFuZGxlcigpIHtcclxuICAgICAgICBpZiAodGhpcy5vcHRpb25zLnJlc3BvbnNpdmUgPT09IHRydWUpIHtcclxuICAgICAgICAgICAgbGV0IGRlYm91bmNlZCA9IG51bGw7XHJcblxyXG4gICAgICAgICAgICBhZGRFdmVudCh3aW5kb3csICdyZXNpemUnLCAoKSA9PiB7XHJcbiAgICAgICAgICAgICAgICAvLyBsZXQgcmVkcmF3ID0gdGhpcy5yZWRyYXdBbGwoKS5iaW5kKHRoaXMpO1xyXG5cclxuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dChkZWJvdW5jZWQpO1xyXG5cclxuICAgICAgICAgICAgICAgIGRlYm91bmNlZCA9IHNldFRpbWVvdXQoKCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gV2luZG93IHJlLXNpemVkLCByZWRyYXdpbmcuLi4nKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgLy8gcmVkcmF3KCk7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5yZWRyYXdBbGwoKVxyXG4gICAgICAgICAgICAgICAgfSwgdGhpcy5vcHRpb25zLmRlYm91bmNlX3RpbWVvdXQpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkIHRoZSBHb29nbGUgU3RhdGljIExvYWRlciBhbmQgcmVzb2x2ZSB0aGUgcHJvbWlzZSB3aGVuIHJlYWR5LlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKi9cclxuICAgIF9sb2FkR29vZ2xlKCkge1xyXG4gICAgICAgIGNvbnN0ICRsYXZhID0gdGhpcztcclxuXHJcbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFJlc29sdmluZyBHb29nbGUuLi4nKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLl9nb29nbGVJc0xvYWRlZCgpKSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFN0YXRpYyBsb2FkZXIgZm91bmQsIGluaXRpYWxpemluZyB3aW5kb3cuZ29vZ2xlJyk7XHJcblxyXG4gICAgICAgICAgICAgICAgJGxhdmEuX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBTdGF0aWMgbG9hZGVyIG5vdCBmb3VuZCwgYXBwZW5kaW5nIHRvIGhlYWQnKTtcclxuXHJcbiAgICAgICAgICAgICAgICAkbGF2YS5fYWRkR29vZ2xlU2NyaXB0VG9IZWFkKHJlc29sdmUpO1xyXG4gICAgICAgICAgICAgICAgLy8gVGhpcyB3aWxsIGNhbGwgJGxhdmEuX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBDaGVjayBpZiBHb29nbGUncyBTdGF0aWMgTG9hZGVyIGlzIGluIHBhZ2UuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqIEByZXR1cm5zIHtib29sZWFufVxyXG4gICAgICovXHJcbiAgICBfZ29vZ2xlSXNMb2FkZWQoKSB7XHJcbiAgICAgICAgY29uc3Qgc2NyaXB0cyA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdzY3JpcHQnKTtcclxuXHJcbiAgICAgICAgZm9yIChsZXQgc2NyaXB0IG9mIHNjcmlwdHMpIHtcclxuICAgICAgICAgICAgaWYgKHNjcmlwdC5zcmMgPT09IHRoaXMuR09PR0xFX0xPQURFUl9VUkwpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogUnVucyB0aGUgR29vZ2xlIGNoYXJ0IGxvYWRlciBhbmQgcmVzb2x2ZXMgdGhlIHByb21pc2UuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqIEBwYXJhbSB7UHJvbWlzZS5yZXNvbHZlfSByZXNvbHZlXHJcbiAgICAgKi9cclxuICAgIF9nb29nbGVDaGFydExvYWRlcihyZXNvbHZlKSB7XHJcbiAgICAgICAgbGV0IGNvbmZpZyA9IHtcclxuICAgICAgICAgICAgcGFja2FnZXM6IHRoaXMuX3BhY2thZ2VzLFxyXG4gICAgICAgICAgICBsYW5ndWFnZTogdGhpcy5vcHRpb25zLmxvY2FsZVxyXG4gICAgICAgIH07XHJcblxyXG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMubWFwc19hcGlfa2V5ICE9PSAnJykge1xyXG4gICAgICAgICAgICBjb25maWcubWFwc0FwaUtleSA9IHRoaXMub3B0aW9ucy5tYXBzX2FwaV9rZXk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIExvYWRpbmcgR29vZ2xlIHdpdGggY29uZmlnOicsIGNvbmZpZyk7XHJcblxyXG4gICAgICAgIGdvb2dsZS5jaGFydHMubG9hZCh0aGlzLkdPT0dMRV9BUElfVkVSU0lPTiwgY29uZmlnKTtcclxuXHJcbiAgICAgICAgZ29vZ2xlLmNoYXJ0cy5zZXRPbkxvYWRDYWxsYmFjayhyZXNvbHZlKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhIG5ldyBzY3JpcHQgdGFnIGZvciB0aGUgR29vZ2xlIFN0YXRpYyBMb2FkZXIuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqIEBwYXJhbSB7UHJvbWlzZS5yZXNvbHZlfSByZXNvbHZlXHJcbiAgICAgKiBAcmV0dXJucyB7RWxlbWVudH1cclxuICAgICAqL1xyXG4gICAgX2FkZEdvb2dsZVNjcmlwdFRvSGVhZChyZXNvbHZlKSB7XHJcbiAgICAgICAgbGV0ICRsYXZhID0gdGhpcztcclxuICAgICAgICBsZXQgc2NyaXB0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7XHJcblxyXG4gICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XHJcbiAgICAgICAgc2NyaXB0LmFzeW5jID0gdHJ1ZTtcclxuICAgICAgICBzY3JpcHQuc3JjID0gdGhpcy5HT09HTEVfTE9BREVSX1VSTDtcclxuICAgICAgICBzY3JpcHQub25sb2FkID0gc2NyaXB0Lm9ucmVhZHlzdGF0ZWNoYW5nZSA9IGZ1bmN0aW9uIChldmVudCkge1xyXG4gICAgICAgICAgICBldmVudCA9IGV2ZW50IHx8IHdpbmRvdy5ldmVudDtcclxuXHJcbiAgICAgICAgICAgIGlmIChldmVudC50eXBlID09PSAnbG9hZCcgfHwgKC9sb2FkZWR8Y29tcGxldGUvLnRlc3QodGhpcy5yZWFkeVN0YXRlKSkpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMub25sb2FkID0gdGhpcy5vbnJlYWR5c3RhdGVjaGFuZ2UgPSBudWxsO1xyXG5cclxuICAgICAgICAgICAgICAgICRsYXZhLl9nb29nbGVDaGFydExvYWRlcihyZXNvbHZlKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH07XHJcblxyXG4gICAgICAgIGRvY3VtZW50LmhlYWQuYXBwZW5kQ2hpbGQoc2NyaXB0KTtcclxuICAgIH1cclxufVxyXG4iLCIvKipcclxuICogT3B0aW9ucyBtb2R1bGVcclxuICpcclxuICogRGVmYXVsdCBjb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHVzaW5nIExhdmEuanMgYXMgYSBzdGFuZGFsb25lIGxpYnJhcnkuXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9PcHRpb25zXHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcblxyXG4vKipcclxuICogQHR5cGUge3thdXRvX3J1bjogYm9vbGVhbiwgbG9jYWxlOiBzdHJpbmcsIHRpbWV6b25lOiBzdHJpbmcsIGRhdGV0aW1lX2Zvcm1hdDogc3RyaW5nLCBtYXBzX2FwaV9rZXk6IHN0cmluZywgcmVzcG9uc2l2ZTogYm9vbGVhbiwgZGVib3VuY2VfdGltZW91dDogbnVtYmVyfX1cclxuICovXHJcbmNvbnN0IGRlZmF1bHRPcHRpb25zID0ge1xyXG4gICAgXCJhdXRvX3J1blwiICAgICAgICA6IGZhbHNlLFxyXG4gICAgXCJsb2NhbGVcIiAgICAgICAgICA6IFwiZW5cIixcclxuICAgIFwidGltZXpvbmVcIiAgICAgICAgOiBcIkFtZXJpY2EvTG9zX0FuZ2VsZXNcIixcclxuICAgIFwiZGF0ZXRpbWVfZm9ybWF0XCIgOiBcIlwiLFxyXG4gICAgXCJtYXBzX2FwaV9rZXlcIiAgICA6IFwiXCIsXHJcbiAgICBcInJlc3BvbnNpdmVcIiAgICAgIDogdHJ1ZSxcclxuICAgIFwiZGVib3VuY2VfdGltZW91dFwiOiAyNTBcclxufTtcclxuXHJcbmV4cG9ydCBkZWZhdWx0IGRlZmF1bHRPcHRpb25zO1xyXG4iLCIvKipcclxuICogQ2hhcnQgY2xhc3MgdXNlZCBmb3Igc3RvcmluZyBhbGwgdGhlIG5lZWRlZCBjb25maWd1cmF0aW9uIGZvciByZW5kZXJpbmcuXHJcbiAqXHJcbiAqIEB0eXBlZGVmIHtGdW5jdGlvbn0gIENoYXJ0XHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIGxhYmVsICAgICAtIExhYmVsIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHR5cGUgICAgICAtIFR5cGUgb2YgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGVsZW1lbnQgICAtIEh0bWwgZWxlbWVudCBpbiB3aGljaCB0byByZW5kZXIgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBjaGFydCAgICAgLSBHb29nbGUgY2hhcnQgb2JqZWN0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBwYWNrYWdlICAgLSBUeXBlIG9mIEdvb2dsZSBjaGFydCBwYWNrYWdlIHRvIGxvYWQuXHJcbiAqIEBwcm9wZXJ0eSB7Ym9vbGVhbn0gIHBuZ091dHB1dCAtIFNob3VsZCB0aGUgY2hhcnQgYmUgZGlzcGxheWVkIGFzIGEgUE5HLlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBkYXRhICAgICAgLSBEYXRhdGFibGUgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgb3B0aW9ucyAgIC0gQ29uZmlndXJhdGlvbiBvcHRpb25zIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7QXJyYXl9ICAgIGZvcm1hdHMgICAtIEZvcm1hdHRlcnMgdG8gYXBwbHkgdG8gdGhlIGNoYXJ0IGRhdGEuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIHByb21pc2VzICAtIFByb21pc2VzIHVzZWQgaW4gdGhlIHJlbmRlcmluZyBjaGFpbi5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gaW5pdCAgICAgIC0gSW5pdGlhbGl6ZXMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSBjb25maWd1cmUgLSBDb25maWd1cmVzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gcmVuZGVyICAgIC0gUmVuZGVycyB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHV1aWQgICAgICAtIENyZWF0ZXMgaWRlbnRpZmljYXRpb24gc3RyaW5nIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIF9lcnJvcnMgICAtIENvbGxlY3Rpb24gb2YgZXJyb3JzIHRvIGJlIHRocm93bi5cclxuICovXHJcbmltcG9ydCB7IGdldFR5cGUgfSBmcm9tIFwiLi9VdGlsc1wiXHJcbmltcG9ydCB7IEVsZW1lbnRJZE5vdEZvdW5kIH0gZnJvbSBcIi4vRXJyb3JzXCI7XHJcblxyXG4vKipcclxuICogQ2hhcnQgbW9kdWxlXHJcbiAqXHJcbiAqIEBjbGFzcyAgICAgQ2hhcnRcclxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmV4cG9ydCBjbGFzcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIC8qKlxyXG4gICAgICogQ2hhcnQgQ2xhc3NcclxuICAgICAqXHJcbiAgICAgKiBUaGlzIGlzIHRoZSBqYXZhc2NyaXB0IHZlcnNpb24gb2YgYSBsYXZhY2hhcnQgd2l0aCBtZXRob2RzIGZvciBpbnRlcmFjdGluZyB3aXRoXHJcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGpzb25cclxuICAgICAqIEBjb25zdHJ1Y3RvclxyXG4gICAgICovXHJcbiAgICBjb25zdHJ1Y3Rvcihqc29uKSB7XHJcbiAgICAgICAgdGhpcy5nY2hhcnQgICAgPSBudWxsO1xyXG4gICAgICAgIHRoaXMubGFiZWwgICAgID0ganNvbi5sYWJlbDtcclxuICAgICAgICB0aGlzLm9wdGlvbnMgICA9IGpzb24ub3B0aW9ucztcclxuICAgICAgICB0aGlzLmVsZW1lbnRJZCA9IGpzb24uZWxlbWVudElkO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0aGlzLmVsZW1lbnRJZCk7XHJcblxyXG4gICAgICAgIGlmICghIHRoaXMuZWxlbWVudCkge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgRWxlbWVudElkTm90Rm91bmQodGhpcy5lbGVtZW50SWQpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgQ2hhcnQuXHJcbiAgICAgKlxyXG4gICAgICogQHJldHVybiB7c3RyaW5nfVxyXG4gICAgICovXHJcbiAgICB1dWlkKCkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLnR5cGUrJzo6Jyt0aGlzLmxhYmVsO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IHdpdGggdGhlIHByZXNldCBkYXRhIGFuZCBvcHRpb25zLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgZHJhdygpIHtcclxuICAgICAgICB0aGlzLmdjaGFydC5kcmF3KHRoaXMuZGF0YSwgdGhpcy5vcHRpb25zKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFNldHMgdGhlIGRhdGEgZm9yIHRoZSBjaGFydCBieSBjcmVhdGluZyBhIG5ldyBEYXRhVGFibGVcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAZXh0ZXJuYWwgXCJnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGVcIlxyXG4gICAgICogQHNlZSAgIHtAbGluayBodHRwczovL2RldmVsb3BlcnMuZ29vZ2xlLmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL3JlZmVyZW5jZSNEYXRhVGFibGV8RGF0YVRhYmxlIENsYXNzfVxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBheWxvYWQgSnNvbiByZXByZXNlbnRhdGlvbiBvZiBhIERhdGFUYWJsZVxyXG4gICAgICovXHJcbiAgICBzZXREYXRhKHBheWxvYWQpIHtcclxuICAgICAgICAvLyBJZiB0aGUgcGF5bG9hZCBpcyBmcm9tIEpvaW5lZERhdGFUYWJsZTo6dG9Kc29uKCksIHRoZW4gY3JlYXRlXHJcbiAgICAgICAgLy8gdHdvIG5ldyBEYXRhVGFibGVzIGFuZCBqb2luIHRoZW0gd2l0aCB0aGUgZGVmaW5lZCBvcHRpb25zLlxyXG4gICAgICAgIGlmIChnZXRUeXBlKHBheWxvYWQuZGF0YSkgPT09ICdBcnJheScpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gZ29vZ2xlLnZpc3VhbGl6YXRpb24uZGF0YS5qb2luKFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMF0pLFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMV0pLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5rZXlzLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5qb2luTWV0aG9kLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zXHJcbiAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvLyBTaW5jZSBHb29nbGUgY29tcGlsZXMgdGhlaXIgY2xhc3Nlcywgd2UgY2FuJ3QgdXNlIGluc3RhbmNlb2YgdG8gY2hlY2sgc2luY2VcclxuICAgICAgICAvLyBpdCBpcyBubyBsb25nZXIgY2FsbGVkIGEgXCJEYXRhVGFibGVcIiAoaXQncyBcImd2anNfUFwiIGJ1dCB0aGF0IGNvdWxkIGNoYW5nZS4uLilcclxuICAgICAgICBpZiAoZ2V0VHlwZShwYXlsb2FkLmdldFRhYmxlUHJvcGVydGllcykgPT09ICdGdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gcGF5bG9hZDtcclxuXHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIElmIGEgRGF0YVRhYmxlI3RvSnNvbigpIHBheWxvYWQgaXMgcmVjZWl2ZWQsIHdpdGggZm9ybWF0dGVkIGNvbHVtbnMsXHJcbiAgICAgICAgLy8gdGhlbiBwYXlsb2FkLmRhdGEgd2lsbCBiZSBkZWZpbmVkLCBhbmQgdXNlZCBhcyB0aGUgRGF0YVRhYmxlXHJcbiAgICAgICAgaWYgKGdldFR5cGUocGF5bG9hZC5kYXRhKSA9PT0gJ09iamVjdCcpIHtcclxuICAgICAgICAgICAgcGF5bG9hZCA9IHBheWxvYWQuZGF0YTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy8gVE9ETzogaGFuZGxlIGZvcm1hdHMgYmV0dGVyLi4uXHJcblxyXG4gICAgICAgIC8vIElmIHdlIHJlYWNoIGhlcmUsIHRoZW4gaXQgbXVzdCBiZSBzdGFuZGFyZCBKU09OIGZvciBjcmVhdGluZyBhIERhdGFUYWJsZS5cclxuICAgICAgICB0aGlzLmRhdGEgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlKHBheWxvYWQpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU2V0cyB0aGUgb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zXHJcbiAgICAgKi9cclxuICAgIHNldE9wdGlvbnMob3B0aW9ucykge1xyXG4gICAgICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoganNoaW50IHVuZGVmOiB0cnVlLCB1bnVzZWQ6IHRydWUgKi9cclxuLyogZ2xvYmFscyBkb2N1bWVudCAqL1xyXG5cclxuLyoqXHJcbiAqIEZ1bmN0aW9uIHRoYXQgZG9lcyBub3RoaW5nLlxyXG4gKlxyXG4gKiBAcmV0dXJuIHt1bmRlZmluZWR9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gbm9vcCgpIHtcclxuICAgIHJldHVybiB1bmRlZmluZWQ7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZXR1cm4gdGhlIHR5cGUgb2Ygb2JqZWN0LlxyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gb2JqZWN0XHJcbiAqIEByZXR1cm4ge21peGVkfVxyXG4gKi9cclxuZXhwb3J0IGZ1bmN0aW9uIGdldFR5cGUob2JqZWN0KSB7XHJcbiAgICBsZXQgdHlwZSA9IE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChvYmplY3QpO1xyXG5cclxuICAgIHJldHVybiB0eXBlLnJlcGxhY2UoJ1tvYmplY3QgJywnJykucmVwbGFjZSgnXScsJycpO1xyXG59XHJcblxyXG4vKipcclxuICogU2ltcGxlIFByb21pc2UgZm9yIHRoZSBET00gdG8gYmUgcmVhZHkuXHJcbiAqXHJcbiAqIEByZXR1cm4ge1Byb21pc2V9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gZG9tTG9hZGVkKCkge1xyXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xyXG4gICAgICAgIGlmIChkb2N1bWVudC5yZWFkeVN0YXRlID09PSAnaW50ZXJhY3RpdmUnIHx8IGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScpIHtcclxuICAgICAgICAgICAgcmVzb2x2ZSgpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCByZXNvbHZlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIE1ldGhvZCBmb3IgYXR0YWNoaW5nIGV2ZW50cyB0byBvYmplY3RzLlxyXG4gKlxyXG4gKiBDcmVkaXQgdG8gQWxleCBWLlxyXG4gKlxyXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzMyNzkzNC9hbGV4LXZcclxuICogQGxpbmsgaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzE1MDEzOVxyXG4gKiBAcGFyYW0ge29iamVjdH0gdGFyZ2V0XHJcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlXHJcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAqIEBwYXJhbSB7Ym9vbH0gZXZlbnRSZXR1cm5cclxuICovXHJcbmV4cG9ydCBmdW5jdGlvbiBhZGRFdmVudCh0YXJnZXQsIHR5cGUsIGNhbGxiYWNrLCBldmVudFJldHVybilcclxue1xyXG4gICAgaWYgKHRhcmdldCA9PT0gbnVsbCB8fCB0eXBlb2YgdGFyZ2V0ID09PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIHJldHVybjtcclxuICAgIH1cclxuXHJcbiAgICBpZiAodGFyZ2V0LmFkZEV2ZW50TGlzdGVuZXIpIHtcclxuICAgICAgICB0YXJnZXQuYWRkRXZlbnRMaXN0ZW5lcih0eXBlLCBjYWxsYmFjaywgISFldmVudFJldHVybik7XHJcbiAgICB9XHJcbiAgICBlbHNlIGlmKHRhcmdldC5hdHRhY2hFdmVudCkge1xyXG4gICAgICAgIHRhcmdldC5hdHRhY2hFdmVudChcIm9uXCIgKyB0eXBlLCBjYWxsYmFjayk7XHJcbiAgICB9XHJcbiAgICBlbHNlIHtcclxuICAgICAgICB0YXJnZXRbXCJvblwiICsgdHlwZV0gPSBjYWxsYmFjaztcclxuICAgIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIEdldCBhIGZ1bmN0aW9uIGEgYnkgaXRzJyBuYW1lc3BhY2VkIHN0cmluZyBuYW1lIHdpdGggY29udGV4dC5cclxuICpcclxuICogQ3JlZGl0IHRvIEphc29uIEJ1bnRpbmdcclxuICpcclxuICogQGxpbmsgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS91c2Vycy8xNzkwL2phc29uLWJ1bnRpbmdcclxuICogQGxpbmsgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS9hLzM1OTkxMFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gZnVuY3Rpb25OYW1lXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBjb250ZXh0XHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gc3RyaW5nVG9GdW5jdGlvbihmdW5jdGlvbk5hbWUsIGNvbnRleHQpIHtcclxuICAgIGxldCBuYW1lc3BhY2VzID0gZnVuY3Rpb25OYW1lLnNwbGl0KCcuJyk7XHJcbiAgICBsZXQgZnVuYyA9IG5hbWVzcGFjZXMucG9wKCk7XHJcblxyXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBuYW1lc3BhY2VzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgY29udGV4dCA9IGNvbnRleHRbbmFtZXNwYWNlc1tpXV07XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIGNvbnRleHRbZnVuY107XHJcbn1cclxuIl19
