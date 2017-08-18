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

var _Renderable3 = _interopRequireDefault(_Renderable2);

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

        _this.formats = json.formats;

        _this.events = _typeof(json.events) === 'object' ? json.events : null;
        _this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        _this.render = function () {
            _this.setData(json.datatable);

            _this.gchart = new google.visualization[_this.class](_this.element);

            if (_this.formats) {
                _this.applyFormats();
            }

            if (_this.events) {
                _this._attachEvents();
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

                console.log('[lava.js] The "' + $chart.uuid + '::' + event + '" event will be handled by "' + func + '" in the context', context);

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
}(_Renderable3.default);

exports.default = Chart;

},{"./Renderable":40,"lodash/forIn":21}],36:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Renderable2 = require('./Renderable');

var _Renderable3 = _interopRequireDefault(_Renderable2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

        json.type = 'Dashboard';

        var _this = _possibleConstructorReturn(this, (Dashboard.__proto__ || Object.getPrototypeOf(Dashboard)).call(this, json));

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
}(_Renderable3.default);

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
var LavaJsError = function (_Error) {
    _inherits(LavaJsError, _Error);

    function LavaJsError(message) {
        _classCallCheck(this, LavaJsError);

        var _this = _possibleConstructorReturn(this, (LavaJsError.__proto__ || Object.getPrototypeOf(LavaJsError)).call(this));

        _this.name = 'LavaJsError';
        _this.message = message || '';
        return _this;
    }

    return LavaJsError;
}(Error);

/**
 * InvalidCallback Error
 *
 * thrown when when anything but a function is given as a callback
 * @type {function}
 */


var InvalidCallback = exports.InvalidCallback = function (_LavaJsError) {
    _inherits(InvalidCallback, _LavaJsError);

    function InvalidCallback(callback) {
        _classCallCheck(this, InvalidCallback);

        var _this2 = _possibleConstructorReturn(this, (InvalidCallback.__proto__ || Object.getPrototypeOf(InvalidCallback)).call(this, '[lava.js] "' + (typeof callback === 'undefined' ? 'undefined' : _typeof(callback)) + '" is not a valid callback.'));

        _this2.name = 'InvalidCallback';
        return _this2;
    }

    return InvalidCallback;
}(LavaJsError);

/**
 * InvalidLabel Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */


var InvalidLabel = exports.InvalidLabel = function (_LavaJsError2) {
    _inherits(InvalidLabel, _LavaJsError2);

    function InvalidLabel(label) {
        _classCallCheck(this, InvalidLabel);

        var _this3 = _possibleConstructorReturn(this, (InvalidLabel.__proto__ || Object.getPrototypeOf(InvalidLabel)).call(this, '[lava.js] "' + (typeof label === 'undefined' ? 'undefined' : _typeof(label)) + '" is not a valid label.'));

        _this3.name = 'InvalidLabel';
        return _this3;
    }

    return InvalidLabel;
}(LavaJsError);

/**
 * ElementIdNotFound Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */


var ElementIdNotFound = exports.ElementIdNotFound = function (_LavaJsError3) {
    _inherits(ElementIdNotFound, _LavaJsError3);

    function ElementIdNotFound(elemId) {
        _classCallCheck(this, ElementIdNotFound);

        var _this4 = _possibleConstructorReturn(this, (ElementIdNotFound.__proto__ || Object.getPrototypeOf(ElementIdNotFound)).call(this, '[lava.js] DOM node where id="' + elemId + '" was not found.'));

        _this4.name = 'ElementIdNotFound';
        return _this4;
    }

    return ElementIdNotFound;
}(LavaJsError);

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
         * Runs the Lava.js module
         *
         * @public
         */

    }, {
        key: 'run',
        value: function run() {
            var _this2 = this;

            console.log('[lava.js] Running...');
            console.log('[lava.js] Loading options:', this.options);

            this._attachRedrawHandler();

            this._loadGoogle().then(function () {
                console.log('[lava.js] Google is ready.');

                _this2.visualization = google.visualization;

                (0, _forIn3.default)(_this2._renderables, function (renderable) {
                    console.log('[lava.js] Rendering ' + renderable.uuid);

                    renderable.render();
                });

                console.log('[lava.js] Firing "ready" event.');
                _this2.emit('ready');

                console.log('[lava.js] Executing lava.ready(callback)');
                _this2._readyCallback();
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
            console.log('[lava.js] Storing ' + renderable.uuid);

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

                    console.log('[lava.js] Redrawing ' + renderable.uuid);

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
            var _this3 = this;

            if (this.options.responsive === true) {
                var debounced = null;

                (0, _Utils.addEvent)(window, 'resize', function () {
                    // let redraw = this.redrawAll().bind(this);

                    clearTimeout(debounced);

                    debounced = setTimeout(function () {
                        console.log('[lava.js] Window re-sized, redrawing...');

                        // redraw();
                        _this3.redrawAll();
                    }, _this3.options.debounce_timeout);
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
            var _this4 = this;

            var $lava = this;

            return new Promise(function (resolve) {
                console.log('[lava.js] Resolving Google...');

                if (_this4._googleIsLoaded()) {
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

var _VisualizationProps = require("./VisualizationProps");

var _VisualizationProps2 = _interopRequireDefault(_VisualizationProps);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
var Renderable = function () {
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
        this.type = json.type;
        this.label = json.label;
        this.options = json.options;
        this.elementId = json.elementId;

        this.element = document.getElementById(this.elementId);

        if (!this.element) {
            throw new _Errors.ElementIdNotFound(this.elementId);
        }

        this._vizProps = new _VisualizationProps2.default(this.type);
    }

    /**
     * The google.visualization class needed for rendering.
     *
     * @return {string}
     */


    _createClass(Renderable, [{
        key: "draw",


        /**
         * Draws the chart with the preset data and options.
         *
         * @public
         */
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
            // If the payload is from the php class JoinedDataTable->toJson(), then create
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

            // If an Array is received, then attempt to use parse with arrayToDataTable.
            if ((0, _Utils.getType)(payload) === 'Array') {
                this.data = google.visualization.arrayToDataTable(payload);

                return;
            }

            // If a php DataTable->toJson() payload is received, with formatted columns,
            // then payload.data will be defined, and used as the DataTable
            if ((0, _Utils.getType)(payload.data) === 'Object') {
                payload = payload.data;

                // TODO: handle formats better...
            }

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
    }, {
        key: "class",
        get: function get() {
            return this._vizProps.class;
        }

        /**
         * Unique identifier for the Chart.
         *
         * @return {string}
         */

    }, {
        key: "uuid",
        get: function get() {
            return this.type + '::' + this.label;
        }
    }]);

    return Renderable;
}();

exports.default = Renderable;

},{"./Errors":37,"./Utils":41,"./VisualizationProps":42}],41:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.noop = noop;
exports.getType = getType;
exports.domLoaded = domLoaded;
exports.addEvent = addEvent;
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

},{}],42:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * VisualizationProps class
 *
 * This module provides the needed properties for rendering charts retrieved
 * by the chart type.
 *
 * @class     VisualizationProps
 * @module    lava/VisualizationProps
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
var VisualizationProps = function () {
    /**
     * Build a new VisualizationProps class for the given chart type.
     *
     * @param {string} chartType
     */
    function VisualizationProps(chartType) {
        _classCallCheck(this, VisualizationProps);

        this.chartType = chartType;

        /**
         * Map of chart types to their visualization package.
         */
        this.CHART_TYPE_PACKAGE_MAP = {
            AnnotationChart: 'annotationchart',
            AreaChart: 'corechart',
            BarChart: 'corechart',
            BubbleChart: 'corechart',
            CalendarChart: 'calendar',
            CandlestickChart: 'corechart',
            ColumnChart: 'corechart',
            ComboChart: 'corechart',
            DonutChart: 'corechart',
            GanttChart: 'gantt',
            GaugeChart: 'gauge',
            GeoChart: 'geochart',
            HistogramChart: 'corechart',
            LineChart: 'corechart',
            PieChart: 'corechart',
            SankeyChart: 'sankey',
            ScatterChart: 'corechart',
            SteppedAreaChart: 'corechart',
            TableChart: 'table',
            TimelineChart: 'timeline',
            TreeMapChart: 'treemap',
            WordTreeChart: 'wordtree'
        };

        /**
         * Map of chart types to their visualization class name.
         */
        this.CHART_TYPE_CLASS_MAP = {
            AnnotationChart: 'AnnotationChart',
            AreaChart: 'AreaChart',
            BarChart: 'BarChart',
            BubbleChart: 'BubbleChart',
            CalendarChart: 'Calendar',
            CandlestickChart: 'CandlestickChart',
            ColumnChart: 'ColumnChart',
            ComboChart: 'ComboChart',
            DonutChart: 'PieChart',
            GanttChart: 'Gantt',
            GaugeChart: 'Gauge',
            GeoChart: 'GeoChart',
            HistogramChart: 'Histogram',
            LineChart: 'LineChart',
            PieChart: 'PieChart',
            SankeyChart: 'Sankey',
            ScatterChart: 'ScatterChart',
            SteppedAreaChart: 'SteppedAreaChart',
            TableChart: 'Table',
            TimelineChart: 'Timeline',
            TreeMapChart: 'TreeMap',
            WordTreeChart: 'WordTree'
        };

        /**
         * Map of chart types to their versions.
         */
        this.CHART_TYPE_VERSION_MAP = {
            AnnotationChart: 1,
            AreaChart: 1,
            BarChart: 1,
            BubbleChart: 1,
            CalendarChart: 1.1,
            CandlestickChart: 1,
            ColumnChart: 1,
            ComboChart: 1,
            DonutChart: 1,
            GanttChart: 1,
            GaugeChart: 1,
            GeoChart: 1,
            HistogramChart: 1,
            LineChart: 1,
            PieChart: 1,
            SankeyChart: 1,
            ScatterChart: 1,
            SteppedAreaChart: 1,
            TableChart: 1,
            TimelineChart: 1,
            TreeMapChart: 1,
            WordTreeChart: 1
        };
    }

    /**
     * Return the visualization package for the chart type
     *
     * @return {string}
     */


    _createClass(VisualizationProps, [{
        key: 'package',
        get: function get() {
            return this.CHART_TYPE_PACKAGE_MAP[this.chartType];
        }

        /**
         * Return the visualization class for the chart type
         *
         * @return {string}
         */

    }, {
        key: 'class',
        get: function get() {
            return this.CHART_TYPE_CLASS_MAP[this.chartType];
        }

        /**
         * Return the visualization version for the chart type
         *
         * @return {number}
         */

    }, {
        key: 'version',
        get: function get() {
            return this.CHART_TYPE_VERSION_MAP[this.chartType];
        }
    }]);

    return VisualizationProps;
}();

exports.default = VisualizationProps;

},{}]},{},[34])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJub2RlX21vZHVsZXMvZXZlbnRzL2V2ZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX1N5bWJvbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2FycmF5TGlrZUtleXMuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlRm9yLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZUdldFRhZy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc1R5cGVkQXJyYXkuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlS2V5c0luLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVRpbWVzLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVVuYXJ5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY2FzdEZ1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY3JlYXRlQmFzZUZvci5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2ZyZWVHbG9iYWwuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19nZXRSYXdUYWcuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19pc0luZGV4LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9faXNQcm90b3R5cGUuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19uYXRpdmVLZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19ub2RlVXRpbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX29iamVjdFRvU3RyaW5nLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fcm9vdC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvZm9ySW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lkZW50aXR5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheUxpa2UuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzQnVmZmVyLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0Z1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0xlbmd0aC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNPYmplY3QuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzT2JqZWN0TGlrZS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNUeXBlZEFycmF5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9rZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL3N0dWJGYWxzZS5qcyIsInNyY1xcbGF2YS5lbnRyeS5qcyIsInNyY1xcbGF2YVxcQ2hhcnQuanMiLCJzcmNcXGxhdmFcXERhc2hib2FyZC5qcyIsInNyY1xcbGF2YVxcRXJyb3JzLmpzIiwic3JjXFxsYXZhXFxMYXZhLmpzIiwic3JjXFxsYXZhXFxPcHRpb25zLmpzIiwic3JjXFxsYXZhXFxSZW5kZXJhYmxlLmpzIiwic3JjXFxsYXZhXFxVdGlscy5qcyIsInNyY1xcbGF2YVxcVmlzdWFsaXphdGlvblByb3BzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzlTQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNOQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2pEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2hCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzVCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNsQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDNURBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2pDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDcEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNkQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDZEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7O0FDekJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7QUNKQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzlDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNsQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3BCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNUQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN2Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDckJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3BDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDMUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2pDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDckNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNuQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMvQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzdCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7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QUFxQnhCLDBCQUFrQixTQXJCTTtBQXNCeEIsMkJBQWtCO0FBdEJNLFNBQTVCOztBQXlCQTs7O0FBR0EsYUFBSyxzQkFBTCxHQUE4QjtBQUMxQiw2QkFBa0IsQ0FEUTtBQUUxQix1QkFBa0IsQ0FGUTtBQUcxQixzQkFBa0IsQ0FIUTtBQUkxQix5QkFBa0IsQ0FKUTtBQUsxQiwyQkFBa0IsR0FMUTtBQU0xQiw4QkFBa0IsQ0FOUTtBQU8xQix5QkFBa0IsQ0FQUTtBQVExQix3QkFBa0IsQ0FSUTtBQVMxQix3QkFBa0IsQ0FUUTtBQVUxQix3QkFBa0IsQ0FWUTtBQVcxQix3QkFBa0IsQ0FYUTtBQVkxQixzQkFBa0IsQ0FaUTtBQWExQiw0QkFBa0IsQ0FiUTtBQWMxQix1QkFBa0IsQ0FkUTtBQWUxQixzQkFBa0IsQ0FmUTtBQWdCMUIseUJBQWtCLENBaEJRO0FBaUIxQiwwQkFBa0IsQ0FqQlE7QUFrQjFCLDhCQUFrQixDQWxCUTtBQW1CMUIsd0JBQWtCLENBbkJRO0FBb0IxQiwyQkFBa0IsQ0FwQlE7QUFxQjFCLDBCQUFrQixDQXJCUTtBQXNCMUIsMkJBQWtCO0FBdEJRLFNBQTlCO0FBd0JIOztBQUVEOzs7Ozs7Ozs7NEJBS2M7QUFDVixtQkFBTyxLQUFLLHNCQUFMLENBQTRCLEtBQUssU0FBakMsQ0FBUDtBQUNIOztBQUVEOzs7Ozs7Ozs0QkFLWTtBQUNSLG1CQUFPLEtBQUssb0JBQUwsQ0FBMEIsS0FBSyxTQUEvQixDQUFQO0FBQ0g7O0FBRUQ7Ozs7Ozs7OzRCQUtjO0FBQ1YsbUJBQU8sS0FBSyxzQkFBTCxDQUE0QixLQUFLLFNBQWpDLENBQVA7QUFDSDs7Ozs7O2tCQXhIZ0Isa0IiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiLy8gQ29weXJpZ2h0IEpveWVudCwgSW5jLiBhbmQgb3RoZXIgTm9kZSBjb250cmlidXRvcnMuXG4vL1xuLy8gUGVybWlzc2lvbiBpcyBoZXJlYnkgZ3JhbnRlZCwgZnJlZSBvZiBjaGFyZ2UsIHRvIGFueSBwZXJzb24gb2J0YWluaW5nIGFcbi8vIGNvcHkgb2YgdGhpcyBzb2Z0d2FyZSBhbmQgYXNzb2NpYXRlZCBkb2N1bWVudGF0aW9uIGZpbGVzICh0aGVcbi8vIFwiU29mdHdhcmVcIiksIHRvIGRlYWwgaW4gdGhlIFNvZnR3YXJlIHdpdGhvdXQgcmVzdHJpY3Rpb24sIGluY2x1ZGluZ1xuLy8gd2l0aG91dCBsaW1pdGF0aW9uIHRoZSByaWdodHMgdG8gdXNlLCBjb3B5LCBtb2RpZnksIG1lcmdlLCBwdWJsaXNoLFxuLy8gZGlzdHJpYnV0ZSwgc3VibGljZW5zZSwgYW5kL29yIHNlbGwgY29waWVzIG9mIHRoZSBTb2Z0d2FyZSwgYW5kIHRvIHBlcm1pdFxuLy8gcGVyc29ucyB0byB3aG9tIHRoZSBTb2Z0d2FyZSBpcyBmdXJuaXNoZWQgdG8gZG8gc28sIHN1YmplY3QgdG8gdGhlXG4vLyBmb2xsb3dpbmcgY29uZGl0aW9uczpcbi8vXG4vLyBUaGUgYWJvdmUgY29weXJpZ2h0IG5vdGljZSBhbmQgdGhpcyBwZXJtaXNzaW9uIG5vdGljZSBzaGFsbCBiZSBpbmNsdWRlZFxuLy8gaW4gYWxsIGNvcGllcyBvciBzdWJzdGFudGlhbCBwb3J0aW9ucyBvZiB0aGUgU29mdHdhcmUuXG4vL1xuLy8gVEhFIFNPRlRXQVJFIElTIFBST1ZJREVEIFwiQVMgSVNcIiwgV0lUSE9VVCBXQVJSQU5UWSBPRiBBTlkgS0lORCwgRVhQUkVTU1xuLy8gT1IgSU1QTElFRCwgSU5DTFVESU5HIEJVVCBOT1QgTElNSVRFRCBUTyBUSEUgV0FSUkFOVElFUyBPRlxuLy8gTUVSQ0hBTlRBQklMSVRZLCBGSVRORVNTIEZPUiBBIFBBUlRJQ1VMQVIgUFVSUE9TRSBBTkQgTk9OSU5GUklOR0VNRU5ULiBJTlxuLy8gTk8gRVZFTlQgU0hBTEwgVEhFIEFVVEhPUlMgT1IgQ09QWVJJR0hUIEhPTERFUlMgQkUgTElBQkxFIEZPUiBBTlkgQ0xBSU0sXG4vLyBEQU1BR0VTIE9SIE9USEVSIExJQUJJTElUWSwgV0hFVEhFUiBJTiBBTiBBQ1RJT04gT0YgQ09OVFJBQ1QsIFRPUlQgT1Jcbi8vIE9USEVSV0lTRSwgQVJJU0lORyBGUk9NLCBPVVQgT0YgT1IgSU4gQ09OTkVDVElPTiBXSVRIIFRIRSBTT0ZUV0FSRSBPUiBUSEVcbi8vIFVTRSBPUiBPVEhFUiBERUFMSU5HUyBJTiBUSEUgU09GVFdBUkUuXG5cbmZ1bmN0aW9uIEV2ZW50RW1pdHRlcigpIHtcbiAgdGhpcy5fZXZlbnRzID0gdGhpcy5fZXZlbnRzIHx8IHt9O1xuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSB0aGlzLl9tYXhMaXN0ZW5lcnMgfHwgdW5kZWZpbmVkO1xufVxubW9kdWxlLmV4cG9ydHMgPSBFdmVudEVtaXR0ZXI7XG5cbi8vIEJhY2t3YXJkcy1jb21wYXQgd2l0aCBub2RlIDAuMTAueFxuRXZlbnRFbWl0dGVyLkV2ZW50RW1pdHRlciA9IEV2ZW50RW1pdHRlcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fZXZlbnRzID0gdW5kZWZpbmVkO1xuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fbWF4TGlzdGVuZXJzID0gdW5kZWZpbmVkO1xuXG4vLyBCeSBkZWZhdWx0IEV2ZW50RW1pdHRlcnMgd2lsbCBwcmludCBhIHdhcm5pbmcgaWYgbW9yZSB0aGFuIDEwIGxpc3RlbmVycyBhcmVcbi8vIGFkZGVkIHRvIGl0LiBUaGlzIGlzIGEgdXNlZnVsIGRlZmF1bHQgd2hpY2ggaGVscHMgZmluZGluZyBtZW1vcnkgbGVha3MuXG5FdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycyA9IDEwO1xuXG4vLyBPYnZpb3VzbHkgbm90IGFsbCBFbWl0dGVycyBzaG91bGQgYmUgbGltaXRlZCB0byAxMC4gVGhpcyBmdW5jdGlvbiBhbGxvd3Ncbi8vIHRoYXQgdG8gYmUgaW5jcmVhc2VkLiBTZXQgdG8gemVybyBmb3IgdW5saW1pdGVkLlxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5zZXRNYXhMaXN0ZW5lcnMgPSBmdW5jdGlvbihuKSB7XG4gIGlmICghaXNOdW1iZXIobikgfHwgbiA8IDAgfHwgaXNOYU4obikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCduIG11c3QgYmUgYSBwb3NpdGl2ZSBudW1iZXInKTtcbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gbjtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmVtaXQgPSBmdW5jdGlvbih0eXBlKSB7XG4gIHZhciBlciwgaGFuZGxlciwgbGVuLCBhcmdzLCBpLCBsaXN0ZW5lcnM7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgdGhpcy5fZXZlbnRzID0ge307XG5cbiAgLy8gSWYgdGhlcmUgaXMgbm8gJ2Vycm9yJyBldmVudCBsaXN0ZW5lciB0aGVuIHRocm93LlxuICBpZiAodHlwZSA9PT0gJ2Vycm9yJykge1xuICAgIGlmICghdGhpcy5fZXZlbnRzLmVycm9yIHx8XG4gICAgICAgIChpc09iamVjdCh0aGlzLl9ldmVudHMuZXJyb3IpICYmICF0aGlzLl9ldmVudHMuZXJyb3IubGVuZ3RoKSkge1xuICAgICAgZXIgPSBhcmd1bWVudHNbMV07XG4gICAgICBpZiAoZXIgaW5zdGFuY2VvZiBFcnJvcikge1xuICAgICAgICB0aHJvdyBlcjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIC8vIEF0IGxlYXN0IGdpdmUgc29tZSBraW5kIG9mIGNvbnRleHQgdG8gdGhlIHVzZXJcbiAgICAgICAgdmFyIGVyciA9IG5ldyBFcnJvcignVW5jYXVnaHQsIHVuc3BlY2lmaWVkIFwiZXJyb3JcIiBldmVudC4gKCcgKyBlciArICcpJyk7XG4gICAgICAgIGVyci5jb250ZXh0ID0gZXI7XG4gICAgICAgIHRocm93IGVycjtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICBoYW5kbGVyID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gIGlmIChpc1VuZGVmaW5lZChoYW5kbGVyKSlcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgaWYgKGlzRnVuY3Rpb24oaGFuZGxlcikpIHtcbiAgICBzd2l0Y2ggKGFyZ3VtZW50cy5sZW5ndGgpIHtcbiAgICAgIC8vIGZhc3QgY2FzZXNcbiAgICAgIGNhc2UgMTpcbiAgICAgICAgaGFuZGxlci5jYWxsKHRoaXMpO1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgMjpcbiAgICAgICAgaGFuZGxlci5jYWxsKHRoaXMsIGFyZ3VtZW50c1sxXSk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAzOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcywgYXJndW1lbnRzWzFdLCBhcmd1bWVudHNbMl0pO1xuICAgICAgICBicmVhaztcbiAgICAgIC8vIHNsb3dlclxuICAgICAgZGVmYXVsdDpcbiAgICAgICAgYXJncyA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGFyZ3VtZW50cywgMSk7XG4gICAgICAgIGhhbmRsZXIuYXBwbHkodGhpcywgYXJncyk7XG4gICAgfVxuICB9IGVsc2UgaWYgKGlzT2JqZWN0KGhhbmRsZXIpKSB7XG4gICAgYXJncyA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGFyZ3VtZW50cywgMSk7XG4gICAgbGlzdGVuZXJzID0gaGFuZGxlci5zbGljZSgpO1xuICAgIGxlbiA9IGxpc3RlbmVycy5sZW5ndGg7XG4gICAgZm9yIChpID0gMDsgaSA8IGxlbjsgaSsrKVxuICAgICAgbGlzdGVuZXJzW2ldLmFwcGx5KHRoaXMsIGFyZ3MpO1xuICB9XG5cbiAgcmV0dXJuIHRydWU7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyID0gZnVuY3Rpb24odHlwZSwgbGlzdGVuZXIpIHtcbiAgdmFyIG07XG5cbiAgaWYgKCFpc0Z1bmN0aW9uKGxpc3RlbmVyKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ2xpc3RlbmVyIG11c3QgYmUgYSBmdW5jdGlvbicpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzKVxuICAgIHRoaXMuX2V2ZW50cyA9IHt9O1xuXG4gIC8vIFRvIGF2b2lkIHJlY3Vyc2lvbiBpbiB0aGUgY2FzZSB0aGF0IHR5cGUgPT09IFwibmV3TGlzdGVuZXJcIiEgQmVmb3JlXG4gIC8vIGFkZGluZyBpdCB0byB0aGUgbGlzdGVuZXJzLCBmaXJzdCBlbWl0IFwibmV3TGlzdGVuZXJcIi5cbiAgaWYgKHRoaXMuX2V2ZW50cy5uZXdMaXN0ZW5lcilcbiAgICB0aGlzLmVtaXQoJ25ld0xpc3RlbmVyJywgdHlwZSxcbiAgICAgICAgICAgICAgaXNGdW5jdGlvbihsaXN0ZW5lci5saXN0ZW5lcikgP1xuICAgICAgICAgICAgICBsaXN0ZW5lci5saXN0ZW5lciA6IGxpc3RlbmVyKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50c1t0eXBlXSlcbiAgICAvLyBPcHRpbWl6ZSB0aGUgY2FzZSBvZiBvbmUgbGlzdGVuZXIuIERvbid0IG5lZWQgdGhlIGV4dHJhIGFycmF5IG9iamVjdC5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0gPSBsaXN0ZW5lcjtcbiAgZWxzZSBpZiAoaXNPYmplY3QodGhpcy5fZXZlbnRzW3R5cGVdKSlcbiAgICAvLyBJZiB3ZSd2ZSBhbHJlYWR5IGdvdCBhbiBhcnJheSwganVzdCBhcHBlbmQuXG4gICAgdGhpcy5fZXZlbnRzW3R5cGVdLnB1c2gobGlzdGVuZXIpO1xuICBlbHNlXG4gICAgLy8gQWRkaW5nIHRoZSBzZWNvbmQgZWxlbWVudCwgbmVlZCB0byBjaGFuZ2UgdG8gYXJyYXkuXG4gICAgdGhpcy5fZXZlbnRzW3R5cGVdID0gW3RoaXMuX2V2ZW50c1t0eXBlXSwgbGlzdGVuZXJdO1xuXG4gIC8vIENoZWNrIGZvciBsaXN0ZW5lciBsZWFrXG4gIGlmIChpc09iamVjdCh0aGlzLl9ldmVudHNbdHlwZV0pICYmICF0aGlzLl9ldmVudHNbdHlwZV0ud2FybmVkKSB7XG4gICAgaWYgKCFpc1VuZGVmaW5lZCh0aGlzLl9tYXhMaXN0ZW5lcnMpKSB7XG4gICAgICBtID0gdGhpcy5fbWF4TGlzdGVuZXJzO1xuICAgIH0gZWxzZSB7XG4gICAgICBtID0gRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gICAgfVxuXG4gICAgaWYgKG0gJiYgbSA+IDAgJiYgdGhpcy5fZXZlbnRzW3R5cGVdLmxlbmd0aCA+IG0pIHtcbiAgICAgIHRoaXMuX2V2ZW50c1t0eXBlXS53YXJuZWQgPSB0cnVlO1xuICAgICAgY29uc29sZS5lcnJvcignKG5vZGUpIHdhcm5pbmc6IHBvc3NpYmxlIEV2ZW50RW1pdHRlciBtZW1vcnkgJyArXG4gICAgICAgICAgICAgICAgICAgICdsZWFrIGRldGVjdGVkLiAlZCBsaXN0ZW5lcnMgYWRkZWQuICcgK1xuICAgICAgICAgICAgICAgICAgICAnVXNlIGVtaXR0ZXIuc2V0TWF4TGlzdGVuZXJzKCkgdG8gaW5jcmVhc2UgbGltaXQuJyxcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5fZXZlbnRzW3R5cGVdLmxlbmd0aCk7XG4gICAgICBpZiAodHlwZW9mIGNvbnNvbGUudHJhY2UgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgLy8gbm90IHN1cHBvcnRlZCBpbiBJRSAxMFxuICAgICAgICBjb25zb2xlLnRyYWNlKCk7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbmNlID0gZnVuY3Rpb24odHlwZSwgbGlzdGVuZXIpIHtcbiAgaWYgKCFpc0Z1bmN0aW9uKGxpc3RlbmVyKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ2xpc3RlbmVyIG11c3QgYmUgYSBmdW5jdGlvbicpO1xuXG4gIHZhciBmaXJlZCA9IGZhbHNlO1xuXG4gIGZ1bmN0aW9uIGcoKSB7XG4gICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBnKTtcblxuICAgIGlmICghZmlyZWQpIHtcbiAgICAgIGZpcmVkID0gdHJ1ZTtcbiAgICAgIGxpc3RlbmVyLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gICAgfVxuICB9XG5cbiAgZy5saXN0ZW5lciA9IGxpc3RlbmVyO1xuICB0aGlzLm9uKHR5cGUsIGcpO1xuXG4gIHJldHVybiB0aGlzO1xufTtcblxuLy8gZW1pdHMgYSAncmVtb3ZlTGlzdGVuZXInIGV2ZW50IGlmZiB0aGUgbGlzdGVuZXIgd2FzIHJlbW92ZWRcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXIgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgbGlzdCwgcG9zaXRpb24sIGxlbmd0aCwgaTtcblxuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMgfHwgIXRoaXMuX2V2ZW50c1t0eXBlXSlcbiAgICByZXR1cm4gdGhpcztcblxuICBsaXN0ID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuICBsZW5ndGggPSBsaXN0Lmxlbmd0aDtcbiAgcG9zaXRpb24gPSAtMTtcblxuICBpZiAobGlzdCA9PT0gbGlzdGVuZXIgfHxcbiAgICAgIChpc0Z1bmN0aW9uKGxpc3QubGlzdGVuZXIpICYmIGxpc3QubGlzdGVuZXIgPT09IGxpc3RlbmVyKSkge1xuICAgIGRlbGV0ZSB0aGlzLl9ldmVudHNbdHlwZV07XG4gICAgaWYgKHRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0ZW5lcik7XG5cbiAgfSBlbHNlIGlmIChpc09iamVjdChsaXN0KSkge1xuICAgIGZvciAoaSA9IGxlbmd0aDsgaS0tID4gMDspIHtcbiAgICAgIGlmIChsaXN0W2ldID09PSBsaXN0ZW5lciB8fFxuICAgICAgICAgIChsaXN0W2ldLmxpc3RlbmVyICYmIGxpc3RbaV0ubGlzdGVuZXIgPT09IGxpc3RlbmVyKSkge1xuICAgICAgICBwb3NpdGlvbiA9IGk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgfVxuICAgIH1cblxuICAgIGlmIChwb3NpdGlvbiA8IDApXG4gICAgICByZXR1cm4gdGhpcztcblxuICAgIGlmIChsaXN0Lmxlbmd0aCA9PT0gMSkge1xuICAgICAgbGlzdC5sZW5ndGggPSAwO1xuICAgICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICB9IGVsc2Uge1xuICAgICAgbGlzdC5zcGxpY2UocG9zaXRpb24sIDEpO1xuICAgIH1cblxuICAgIGlmICh0aGlzLl9ldmVudHMucmVtb3ZlTGlzdGVuZXIpXG4gICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgbGlzdGVuZXIpO1xuICB9XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJlbW92ZUFsbExpc3RlbmVycyA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIGtleSwgbGlzdGVuZXJzO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzKVxuICAgIHJldHVybiB0aGlzO1xuXG4gIC8vIG5vdCBsaXN0ZW5pbmcgZm9yIHJlbW92ZUxpc3RlbmVyLCBubyBuZWVkIHRvIGVtaXRcbiAgaWYgKCF0aGlzLl9ldmVudHMucmVtb3ZlTGlzdGVuZXIpIHtcbiAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMClcbiAgICAgIHRoaXMuX2V2ZW50cyA9IHt9O1xuICAgIGVsc2UgaWYgKHRoaXMuX2V2ZW50c1t0eXBlXSlcbiAgICAgIGRlbGV0ZSB0aGlzLl9ldmVudHNbdHlwZV07XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICAvLyBlbWl0IHJlbW92ZUxpc3RlbmVyIGZvciBhbGwgbGlzdGVuZXJzIG9uIGFsbCBldmVudHNcbiAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICBmb3IgKGtleSBpbiB0aGlzLl9ldmVudHMpIHtcbiAgICAgIGlmIChrZXkgPT09ICdyZW1vdmVMaXN0ZW5lcicpIGNvbnRpbnVlO1xuICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoa2V5KTtcbiAgICB9XG4gICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoJ3JlbW92ZUxpc3RlbmVyJyk7XG4gICAgdGhpcy5fZXZlbnRzID0ge307XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICBsaXN0ZW5lcnMgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgaWYgKGlzRnVuY3Rpb24obGlzdGVuZXJzKSkge1xuICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzKTtcbiAgfSBlbHNlIGlmIChsaXN0ZW5lcnMpIHtcbiAgICAvLyBMSUZPIG9yZGVyXG4gICAgd2hpbGUgKGxpc3RlbmVycy5sZW5ndGgpXG4gICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyc1tsaXN0ZW5lcnMubGVuZ3RoIC0gMV0pO1xuICB9XG4gIGRlbGV0ZSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVycyA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIHJldDtcbiAgaWYgKCF0aGlzLl9ldmVudHMgfHwgIXRoaXMuX2V2ZW50c1t0eXBlXSlcbiAgICByZXQgPSBbXTtcbiAgZWxzZSBpZiAoaXNGdW5jdGlvbih0aGlzLl9ldmVudHNbdHlwZV0pKVxuICAgIHJldCA9IFt0aGlzLl9ldmVudHNbdHlwZV1dO1xuICBlbHNlXG4gICAgcmV0ID0gdGhpcy5fZXZlbnRzW3R5cGVdLnNsaWNlKCk7XG4gIHJldHVybiByZXQ7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbih0eXBlKSB7XG4gIGlmICh0aGlzLl9ldmVudHMpIHtcbiAgICB2YXIgZXZsaXN0ZW5lciA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICAgIGlmIChpc0Z1bmN0aW9uKGV2bGlzdGVuZXIpKVxuICAgICAgcmV0dXJuIDE7XG4gICAgZWxzZSBpZiAoZXZsaXN0ZW5lcilcbiAgICAgIHJldHVybiBldmxpc3RlbmVyLmxlbmd0aDtcbiAgfVxuICByZXR1cm4gMDtcbn07XG5cbkV2ZW50RW1pdHRlci5saXN0ZW5lckNvdW50ID0gZnVuY3Rpb24oZW1pdHRlciwgdHlwZSkge1xuICByZXR1cm4gZW1pdHRlci5saXN0ZW5lckNvdW50KHR5cGUpO1xufTtcblxuZnVuY3Rpb24gaXNGdW5jdGlvbihhcmcpIHtcbiAgcmV0dXJuIHR5cGVvZiBhcmcgPT09ICdmdW5jdGlvbic7XG59XG5cbmZ1bmN0aW9uIGlzTnVtYmVyKGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ251bWJlcic7XG59XG5cbmZ1bmN0aW9uIGlzT2JqZWN0KGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ29iamVjdCcgJiYgYXJnICE9PSBudWxsO1xufVxuXG5mdW5jdGlvbiBpc1VuZGVmaW5lZChhcmcpIHtcbiAgcmV0dXJuIGFyZyA9PT0gdm9pZCAwO1xufVxuIiwidmFyIHJvb3QgPSByZXF1aXJlKCcuL19yb290Jyk7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIFN5bWJvbCA9IHJvb3QuU3ltYm9sO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFN5bWJvbDtcbiIsInZhciBiYXNlVGltZXMgPSByZXF1aXJlKCcuL19iYXNlVGltZXMnKSxcbiAgICBpc0FyZ3VtZW50cyA9IHJlcXVpcmUoJy4vaXNBcmd1bWVudHMnKSxcbiAgICBpc0FycmF5ID0gcmVxdWlyZSgnLi9pc0FycmF5JyksXG4gICAgaXNCdWZmZXIgPSByZXF1aXJlKCcuL2lzQnVmZmVyJyksXG4gICAgaXNJbmRleCA9IHJlcXVpcmUoJy4vX2lzSW5kZXgnKSxcbiAgICBpc1R5cGVkQXJyYXkgPSByZXF1aXJlKCcuL2lzVHlwZWRBcnJheScpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKipcbiAqIENyZWF0ZXMgYW4gYXJyYXkgb2YgdGhlIGVudW1lcmFibGUgcHJvcGVydHkgbmFtZXMgb2YgdGhlIGFycmF5LWxpa2UgYHZhbHVlYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcXVlcnkuXG4gKiBAcGFyYW0ge2Jvb2xlYW59IGluaGVyaXRlZCBTcGVjaWZ5IHJldHVybmluZyBpbmhlcml0ZWQgcHJvcGVydHkgbmFtZXMuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICovXG5mdW5jdGlvbiBhcnJheUxpa2VLZXlzKHZhbHVlLCBpbmhlcml0ZWQpIHtcbiAgdmFyIGlzQXJyID0gaXNBcnJheSh2YWx1ZSksXG4gICAgICBpc0FyZyA9ICFpc0FyciAmJiBpc0FyZ3VtZW50cyh2YWx1ZSksXG4gICAgICBpc0J1ZmYgPSAhaXNBcnIgJiYgIWlzQXJnICYmIGlzQnVmZmVyKHZhbHVlKSxcbiAgICAgIGlzVHlwZSA9ICFpc0FyciAmJiAhaXNBcmcgJiYgIWlzQnVmZiAmJiBpc1R5cGVkQXJyYXkodmFsdWUpLFxuICAgICAgc2tpcEluZGV4ZXMgPSBpc0FyciB8fCBpc0FyZyB8fCBpc0J1ZmYgfHwgaXNUeXBlLFxuICAgICAgcmVzdWx0ID0gc2tpcEluZGV4ZXMgPyBiYXNlVGltZXModmFsdWUubGVuZ3RoLCBTdHJpbmcpIDogW10sXG4gICAgICBsZW5ndGggPSByZXN1bHQubGVuZ3RoO1xuXG4gIGZvciAodmFyIGtleSBpbiB2YWx1ZSkge1xuICAgIGlmICgoaW5oZXJpdGVkIHx8IGhhc093blByb3BlcnR5LmNhbGwodmFsdWUsIGtleSkpICYmXG4gICAgICAgICEoc2tpcEluZGV4ZXMgJiYgKFxuICAgICAgICAgICAvLyBTYWZhcmkgOSBoYXMgZW51bWVyYWJsZSBgYXJndW1lbnRzLmxlbmd0aGAgaW4gc3RyaWN0IG1vZGUuXG4gICAgICAgICAgIGtleSA9PSAnbGVuZ3RoJyB8fFxuICAgICAgICAgICAvLyBOb2RlLmpzIDAuMTAgaGFzIGVudW1lcmFibGUgbm9uLWluZGV4IHByb3BlcnRpZXMgb24gYnVmZmVycy5cbiAgICAgICAgICAgKGlzQnVmZiAmJiAoa2V5ID09ICdvZmZzZXQnIHx8IGtleSA9PSAncGFyZW50JykpIHx8XG4gICAgICAgICAgIC8vIFBoYW50b21KUyAyIGhhcyBlbnVtZXJhYmxlIG5vbi1pbmRleCBwcm9wZXJ0aWVzIG9uIHR5cGVkIGFycmF5cy5cbiAgICAgICAgICAgKGlzVHlwZSAmJiAoa2V5ID09ICdidWZmZXInIHx8IGtleSA9PSAnYnl0ZUxlbmd0aCcgfHwga2V5ID09ICdieXRlT2Zmc2V0JykpIHx8XG4gICAgICAgICAgIC8vIFNraXAgaW5kZXggcHJvcGVydGllcy5cbiAgICAgICAgICAgaXNJbmRleChrZXksIGxlbmd0aClcbiAgICAgICAgKSkpIHtcbiAgICAgIHJlc3VsdC5wdXNoKGtleSk7XG4gICAgfVxuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYXJyYXlMaWtlS2V5cztcbiIsInZhciBjcmVhdGVCYXNlRm9yID0gcmVxdWlyZSgnLi9fY3JlYXRlQmFzZUZvcicpO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBiYXNlRm9yT3duYCB3aGljaCBpdGVyYXRlcyBvdmVyIGBvYmplY3RgXG4gKiBwcm9wZXJ0aWVzIHJldHVybmVkIGJ5IGBrZXlzRnVuY2AgYW5kIGludm9rZXMgYGl0ZXJhdGVlYCBmb3IgZWFjaCBwcm9wZXJ0eS5cbiAqIEl0ZXJhdGVlIGZ1bmN0aW9ucyBtYXkgZXhpdCBpdGVyYXRpb24gZWFybHkgYnkgZXhwbGljaXRseSByZXR1cm5pbmcgYGZhbHNlYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIGl0ZXJhdGUgb3Zlci5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IGl0ZXJhdGVlIFRoZSBmdW5jdGlvbiBpbnZva2VkIHBlciBpdGVyYXRpb24uXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBrZXlzRnVuYyBUaGUgZnVuY3Rpb24gdG8gZ2V0IHRoZSBrZXlzIG9mIGBvYmplY3RgLlxuICogQHJldHVybnMge09iamVjdH0gUmV0dXJucyBgb2JqZWN0YC5cbiAqL1xudmFyIGJhc2VGb3IgPSBjcmVhdGVCYXNlRm9yKCk7XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUZvcjtcbiIsInZhciBTeW1ib2wgPSByZXF1aXJlKCcuL19TeW1ib2wnKSxcbiAgICBnZXRSYXdUYWcgPSByZXF1aXJlKCcuL19nZXRSYXdUYWcnKSxcbiAgICBvYmplY3RUb1N0cmluZyA9IHJlcXVpcmUoJy4vX29iamVjdFRvU3RyaW5nJyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBudWxsVGFnID0gJ1tvYmplY3QgTnVsbF0nLFxuICAgIHVuZGVmaW5lZFRhZyA9ICdbb2JqZWN0IFVuZGVmaW5lZF0nO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBzeW1Ub1N0cmluZ1RhZyA9IFN5bWJvbCA/IFN5bWJvbC50b1N0cmluZ1RhZyA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgZ2V0VGFnYCB3aXRob3V0IGZhbGxiYWNrcyBmb3IgYnVnZ3kgZW52aXJvbm1lbnRzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBxdWVyeS5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIGB0b1N0cmluZ1RhZ2AuXG4gKi9cbmZ1bmN0aW9uIGJhc2VHZXRUYWcodmFsdWUpIHtcbiAgaWYgKHZhbHVlID09IG51bGwpIHtcbiAgICByZXR1cm4gdmFsdWUgPT09IHVuZGVmaW5lZCA/IHVuZGVmaW5lZFRhZyA6IG51bGxUYWc7XG4gIH1cbiAgcmV0dXJuIChzeW1Ub1N0cmluZ1RhZyAmJiBzeW1Ub1N0cmluZ1RhZyBpbiBPYmplY3QodmFsdWUpKVxuICAgID8gZ2V0UmF3VGFnKHZhbHVlKVxuICAgIDogb2JqZWN0VG9TdHJpbmcodmFsdWUpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VHZXRUYWc7XG4iLCJ2YXIgYmFzZUdldFRhZyA9IHJlcXVpcmUoJy4vX2Jhc2VHZXRUYWcnKSxcbiAgICBpc09iamVjdExpa2UgPSByZXF1aXJlKCcuL2lzT2JqZWN0TGlrZScpO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgYXJnc1RhZyA9ICdbb2JqZWN0IEFyZ3VtZW50c10nO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLmlzQXJndW1lbnRzYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBgYXJndW1lbnRzYCBvYmplY3QsXG4gKi9cbmZ1bmN0aW9uIGJhc2VJc0FyZ3VtZW50cyh2YWx1ZSkge1xuICByZXR1cm4gaXNPYmplY3RMaWtlKHZhbHVlKSAmJiBiYXNlR2V0VGFnKHZhbHVlKSA9PSBhcmdzVGFnO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VJc0FyZ3VtZW50cztcbiIsInZhciBiYXNlR2V0VGFnID0gcmVxdWlyZSgnLi9fYmFzZUdldFRhZycpLFxuICAgIGlzTGVuZ3RoID0gcmVxdWlyZSgnLi9pc0xlbmd0aCcpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhcmdzVGFnID0gJ1tvYmplY3QgQXJndW1lbnRzXScsXG4gICAgYXJyYXlUYWcgPSAnW29iamVjdCBBcnJheV0nLFxuICAgIGJvb2xUYWcgPSAnW29iamVjdCBCb29sZWFuXScsXG4gICAgZGF0ZVRhZyA9ICdbb2JqZWN0IERhdGVdJyxcbiAgICBlcnJvclRhZyA9ICdbb2JqZWN0IEVycm9yXScsXG4gICAgZnVuY1RhZyA9ICdbb2JqZWN0IEZ1bmN0aW9uXScsXG4gICAgbWFwVGFnID0gJ1tvYmplY3QgTWFwXScsXG4gICAgbnVtYmVyVGFnID0gJ1tvYmplY3QgTnVtYmVyXScsXG4gICAgb2JqZWN0VGFnID0gJ1tvYmplY3QgT2JqZWN0XScsXG4gICAgcmVnZXhwVGFnID0gJ1tvYmplY3QgUmVnRXhwXScsXG4gICAgc2V0VGFnID0gJ1tvYmplY3QgU2V0XScsXG4gICAgc3RyaW5nVGFnID0gJ1tvYmplY3QgU3RyaW5nXScsXG4gICAgd2Vha01hcFRhZyA9ICdbb2JqZWN0IFdlYWtNYXBdJztcblxudmFyIGFycmF5QnVmZmVyVGFnID0gJ1tvYmplY3QgQXJyYXlCdWZmZXJdJyxcbiAgICBkYXRhVmlld1RhZyA9ICdbb2JqZWN0IERhdGFWaWV3XScsXG4gICAgZmxvYXQzMlRhZyA9ICdbb2JqZWN0IEZsb2F0MzJBcnJheV0nLFxuICAgIGZsb2F0NjRUYWcgPSAnW29iamVjdCBGbG9hdDY0QXJyYXldJyxcbiAgICBpbnQ4VGFnID0gJ1tvYmplY3QgSW50OEFycmF5XScsXG4gICAgaW50MTZUYWcgPSAnW29iamVjdCBJbnQxNkFycmF5XScsXG4gICAgaW50MzJUYWcgPSAnW29iamVjdCBJbnQzMkFycmF5XScsXG4gICAgdWludDhUYWcgPSAnW29iamVjdCBVaW50OEFycmF5XScsXG4gICAgdWludDhDbGFtcGVkVGFnID0gJ1tvYmplY3QgVWludDhDbGFtcGVkQXJyYXldJyxcbiAgICB1aW50MTZUYWcgPSAnW29iamVjdCBVaW50MTZBcnJheV0nLFxuICAgIHVpbnQzMlRhZyA9ICdbb2JqZWN0IFVpbnQzMkFycmF5XSc7XG5cbi8qKiBVc2VkIHRvIGlkZW50aWZ5IGB0b1N0cmluZ1RhZ2AgdmFsdWVzIG9mIHR5cGVkIGFycmF5cy4gKi9cbnZhciB0eXBlZEFycmF5VGFncyA9IHt9O1xudHlwZWRBcnJheVRhZ3NbZmxvYXQzMlRhZ10gPSB0eXBlZEFycmF5VGFnc1tmbG9hdDY0VGFnXSA9XG50eXBlZEFycmF5VGFnc1tpbnQ4VGFnXSA9IHR5cGVkQXJyYXlUYWdzW2ludDE2VGFnXSA9XG50eXBlZEFycmF5VGFnc1tpbnQzMlRhZ10gPSB0eXBlZEFycmF5VGFnc1t1aW50OFRhZ10gPVxudHlwZWRBcnJheVRhZ3NbdWludDhDbGFtcGVkVGFnXSA9IHR5cGVkQXJyYXlUYWdzW3VpbnQxNlRhZ10gPVxudHlwZWRBcnJheVRhZ3NbdWludDMyVGFnXSA9IHRydWU7XG50eXBlZEFycmF5VGFnc1thcmdzVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2FycmF5VGFnXSA9XG50eXBlZEFycmF5VGFnc1thcnJheUJ1ZmZlclRhZ10gPSB0eXBlZEFycmF5VGFnc1tib29sVGFnXSA9XG50eXBlZEFycmF5VGFnc1tkYXRhVmlld1RhZ10gPSB0eXBlZEFycmF5VGFnc1tkYXRlVGFnXSA9XG50eXBlZEFycmF5VGFnc1tlcnJvclRhZ10gPSB0eXBlZEFycmF5VGFnc1tmdW5jVGFnXSA9XG50eXBlZEFycmF5VGFnc1ttYXBUYWddID0gdHlwZWRBcnJheVRhZ3NbbnVtYmVyVGFnXSA9XG50eXBlZEFycmF5VGFnc1tvYmplY3RUYWddID0gdHlwZWRBcnJheVRhZ3NbcmVnZXhwVGFnXSA9XG50eXBlZEFycmF5VGFnc1tzZXRUYWddID0gdHlwZWRBcnJheVRhZ3Nbc3RyaW5nVGFnXSA9XG50eXBlZEFycmF5VGFnc1t3ZWFrTWFwVGFnXSA9IGZhbHNlO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLmlzVHlwZWRBcnJheWAgd2l0aG91dCBOb2RlLmpzIG9wdGltaXphdGlvbnMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB0eXBlZCBhcnJheSwgZWxzZSBgZmFsc2VgLlxuICovXG5mdW5jdGlvbiBiYXNlSXNUeXBlZEFycmF5KHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmXG4gICAgaXNMZW5ndGgodmFsdWUubGVuZ3RoKSAmJiAhIXR5cGVkQXJyYXlUYWdzW2Jhc2VHZXRUYWcodmFsdWUpXTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlSXNUeXBlZEFycmF5O1xuIiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9pc09iamVjdCcpLFxuICAgIGlzUHJvdG90eXBlID0gcmVxdWlyZSgnLi9faXNQcm90b3R5cGUnKSxcbiAgICBuYXRpdmVLZXlzSW4gPSByZXF1aXJlKCcuL19uYXRpdmVLZXlzSW4nKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy5rZXlzSW5gIHdoaWNoIGRvZXNuJ3QgdHJlYXQgc3BhcnNlIGFycmF5cyBhcyBkZW5zZS5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqL1xuZnVuY3Rpb24gYmFzZUtleXNJbihvYmplY3QpIHtcbiAgaWYgKCFpc09iamVjdChvYmplY3QpKSB7XG4gICAgcmV0dXJuIG5hdGl2ZUtleXNJbihvYmplY3QpO1xuICB9XG4gIHZhciBpc1Byb3RvID0gaXNQcm90b3R5cGUob2JqZWN0KSxcbiAgICAgIHJlc3VsdCA9IFtdO1xuXG4gIGZvciAodmFyIGtleSBpbiBvYmplY3QpIHtcbiAgICBpZiAoIShrZXkgPT0gJ2NvbnN0cnVjdG9yJyAmJiAoaXNQcm90byB8fCAhaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIGtleSkpKSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlS2V5c0luO1xuIiwiLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy50aW1lc2Agd2l0aG91dCBzdXBwb3J0IGZvciBpdGVyYXRlZSBzaG9ydGhhbmRzXG4gKiBvciBtYXggYXJyYXkgbGVuZ3RoIGNoZWNrcy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtudW1iZXJ9IG4gVGhlIG51bWJlciBvZiB0aW1lcyB0byBpbnZva2UgYGl0ZXJhdGVlYC5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IGl0ZXJhdGVlIFRoZSBmdW5jdGlvbiBpbnZva2VkIHBlciBpdGVyYXRpb24uXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHJlc3VsdHMuXG4gKi9cbmZ1bmN0aW9uIGJhc2VUaW1lcyhuLCBpdGVyYXRlZSkge1xuICB2YXIgaW5kZXggPSAtMSxcbiAgICAgIHJlc3VsdCA9IEFycmF5KG4pO1xuXG4gIHdoaWxlICgrK2luZGV4IDwgbikge1xuICAgIHJlc3VsdFtpbmRleF0gPSBpdGVyYXRlZShpbmRleCk7XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlVGltZXM7XG4iLCIvKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLnVuYXJ5YCB3aXRob3V0IHN1cHBvcnQgZm9yIHN0b3JpbmcgbWV0YWRhdGEuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGZ1bmMgVGhlIGZ1bmN0aW9uIHRvIGNhcCBhcmd1bWVudHMgZm9yLlxuICogQHJldHVybnMge0Z1bmN0aW9ufSBSZXR1cm5zIHRoZSBuZXcgY2FwcGVkIGZ1bmN0aW9uLlxuICovXG5mdW5jdGlvbiBiYXNlVW5hcnkoZnVuYykge1xuICByZXR1cm4gZnVuY3Rpb24odmFsdWUpIHtcbiAgICByZXR1cm4gZnVuYyh2YWx1ZSk7XG4gIH07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZVVuYXJ5O1xuIiwidmFyIGlkZW50aXR5ID0gcmVxdWlyZSgnLi9pZGVudGl0eScpO1xuXG4vKipcbiAqIENhc3RzIGB2YWx1ZWAgdG8gYGlkZW50aXR5YCBpZiBpdCdzIG5vdCBhIGZ1bmN0aW9uLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBpbnNwZWN0LlxuICogQHJldHVybnMge0Z1bmN0aW9ufSBSZXR1cm5zIGNhc3QgZnVuY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIGNhc3RGdW5jdGlvbih2YWx1ZSkge1xuICByZXR1cm4gdHlwZW9mIHZhbHVlID09ICdmdW5jdGlvbicgPyB2YWx1ZSA6IGlkZW50aXR5O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGNhc3RGdW5jdGlvbjtcbiIsIi8qKlxuICogQ3JlYXRlcyBhIGJhc2UgZnVuY3Rpb24gZm9yIG1ldGhvZHMgbGlrZSBgXy5mb3JJbmAgYW5kIGBfLmZvck93bmAuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Ym9vbGVhbn0gW2Zyb21SaWdodF0gU3BlY2lmeSBpdGVyYXRpbmcgZnJvbSByaWdodCB0byBsZWZ0LlxuICogQHJldHVybnMge0Z1bmN0aW9ufSBSZXR1cm5zIHRoZSBuZXcgYmFzZSBmdW5jdGlvbi5cbiAqL1xuZnVuY3Rpb24gY3JlYXRlQmFzZUZvcihmcm9tUmlnaHQpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uKG9iamVjdCwgaXRlcmF0ZWUsIGtleXNGdW5jKSB7XG4gICAgdmFyIGluZGV4ID0gLTEsXG4gICAgICAgIGl0ZXJhYmxlID0gT2JqZWN0KG9iamVjdCksXG4gICAgICAgIHByb3BzID0ga2V5c0Z1bmMob2JqZWN0KSxcbiAgICAgICAgbGVuZ3RoID0gcHJvcHMubGVuZ3RoO1xuXG4gICAgd2hpbGUgKGxlbmd0aC0tKSB7XG4gICAgICB2YXIga2V5ID0gcHJvcHNbZnJvbVJpZ2h0ID8gbGVuZ3RoIDogKytpbmRleF07XG4gICAgICBpZiAoaXRlcmF0ZWUoaXRlcmFibGVba2V5XSwga2V5LCBpdGVyYWJsZSkgPT09IGZhbHNlKSB7XG4gICAgICAgIGJyZWFrO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gb2JqZWN0O1xuICB9O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGNyZWF0ZUJhc2VGb3I7XG4iLCIvKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGdsb2JhbGAgZnJvbSBOb2RlLmpzLiAqL1xudmFyIGZyZWVHbG9iYWwgPSB0eXBlb2YgZ2xvYmFsID09ICdvYmplY3QnICYmIGdsb2JhbCAmJiBnbG9iYWwuT2JqZWN0ID09PSBPYmplY3QgJiYgZ2xvYmFsO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZyZWVHbG9iYWw7XG4iLCJ2YXIgU3ltYm9sID0gcmVxdWlyZSgnLi9fU3ltYm9sJyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKlxuICogVXNlZCB0byByZXNvbHZlIHRoZVxuICogW2B0b1N0cmluZ1RhZ2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLW9iamVjdC5wcm90b3R5cGUudG9zdHJpbmcpXG4gKiBvZiB2YWx1ZXMuXG4gKi9cbnZhciBuYXRpdmVPYmplY3RUb1N0cmluZyA9IG9iamVjdFByb3RvLnRvU3RyaW5nO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBzeW1Ub1N0cmluZ1RhZyA9IFN5bWJvbCA/IFN5bWJvbC50b1N0cmluZ1RhZyA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBBIHNwZWNpYWxpemVkIHZlcnNpb24gb2YgYGJhc2VHZXRUYWdgIHdoaWNoIGlnbm9yZXMgYFN5bWJvbC50b1N0cmluZ1RhZ2AgdmFsdWVzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBxdWVyeS5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIHJhdyBgdG9TdHJpbmdUYWdgLlxuICovXG5mdW5jdGlvbiBnZXRSYXdUYWcodmFsdWUpIHtcbiAgdmFyIGlzT3duID0gaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwgc3ltVG9TdHJpbmdUYWcpLFxuICAgICAgdGFnID0gdmFsdWVbc3ltVG9TdHJpbmdUYWddO1xuXG4gIHRyeSB7XG4gICAgdmFsdWVbc3ltVG9TdHJpbmdUYWddID0gdW5kZWZpbmVkO1xuICAgIHZhciB1bm1hc2tlZCA9IHRydWU7XG4gIH0gY2F0Y2ggKGUpIHt9XG5cbiAgdmFyIHJlc3VsdCA9IG5hdGl2ZU9iamVjdFRvU3RyaW5nLmNhbGwodmFsdWUpO1xuICBpZiAodW5tYXNrZWQpIHtcbiAgICBpZiAoaXNPd24pIHtcbiAgICAgIHZhbHVlW3N5bVRvU3RyaW5nVGFnXSA9IHRhZztcbiAgICB9IGVsc2Uge1xuICAgICAgZGVsZXRlIHZhbHVlW3N5bVRvU3RyaW5nVGFnXTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBnZXRSYXdUYWc7XG4iLCIvKiogVXNlZCBhcyByZWZlcmVuY2VzIGZvciB2YXJpb3VzIGBOdW1iZXJgIGNvbnN0YW50cy4gKi9cbnZhciBNQVhfU0FGRV9JTlRFR0VSID0gOTAwNzE5OTI1NDc0MDk5MTtcblxuLyoqIFVzZWQgdG8gZGV0ZWN0IHVuc2lnbmVkIGludGVnZXIgdmFsdWVzLiAqL1xudmFyIHJlSXNVaW50ID0gL14oPzowfFsxLTldXFxkKikkLztcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGFycmF5LWxpa2UgaW5kZXguXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHBhcmFtIHtudW1iZXJ9IFtsZW5ndGg9TUFYX1NBRkVfSU5URUdFUl0gVGhlIHVwcGVyIGJvdW5kcyBvZiBhIHZhbGlkIGluZGV4LlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBpbmRleCwgZWxzZSBgZmFsc2VgLlxuICovXG5mdW5jdGlvbiBpc0luZGV4KHZhbHVlLCBsZW5ndGgpIHtcbiAgbGVuZ3RoID0gbGVuZ3RoID09IG51bGwgPyBNQVhfU0FGRV9JTlRFR0VSIDogbGVuZ3RoO1xuICByZXR1cm4gISFsZW5ndGggJiZcbiAgICAodHlwZW9mIHZhbHVlID09ICdudW1iZXInIHx8IHJlSXNVaW50LnRlc3QodmFsdWUpKSAmJlxuICAgICh2YWx1ZSA+IC0xICYmIHZhbHVlICUgMSA9PSAwICYmIHZhbHVlIDwgbGVuZ3RoKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0luZGV4O1xuIiwiLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBsaWtlbHkgYSBwcm90b3R5cGUgb2JqZWN0LlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgcHJvdG90eXBlLCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGlzUHJvdG90eXBlKHZhbHVlKSB7XG4gIHZhciBDdG9yID0gdmFsdWUgJiYgdmFsdWUuY29uc3RydWN0b3IsXG4gICAgICBwcm90byA9ICh0eXBlb2YgQ3RvciA9PSAnZnVuY3Rpb24nICYmIEN0b3IucHJvdG90eXBlKSB8fCBvYmplY3RQcm90bztcblxuICByZXR1cm4gdmFsdWUgPT09IHByb3RvO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzUHJvdG90eXBlO1xuIiwiLyoqXG4gKiBUaGlzIGZ1bmN0aW9uIGlzIGxpa2VcbiAqIFtgT2JqZWN0LmtleXNgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy1vYmplY3Qua2V5cylcbiAqIGV4Y2VwdCB0aGF0IGl0IGluY2x1ZGVzIGluaGVyaXRlZCBlbnVtZXJhYmxlIHByb3BlcnRpZXMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3QgVGhlIG9iamVjdCB0byBxdWVyeS5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKi9cbmZ1bmN0aW9uIG5hdGl2ZUtleXNJbihvYmplY3QpIHtcbiAgdmFyIHJlc3VsdCA9IFtdO1xuICBpZiAob2JqZWN0ICE9IG51bGwpIHtcbiAgICBmb3IgKHZhciBrZXkgaW4gT2JqZWN0KG9iamVjdCkpIHtcbiAgICAgIHJlc3VsdC5wdXNoKGtleSk7XG4gICAgfVxuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gbmF0aXZlS2V5c0luO1xuIiwidmFyIGZyZWVHbG9iYWwgPSByZXF1aXJlKCcuL19mcmVlR2xvYmFsJyk7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZXhwb3J0c2AuICovXG52YXIgZnJlZUV4cG9ydHMgPSB0eXBlb2YgZXhwb3J0cyA9PSAnb2JqZWN0JyAmJiBleHBvcnRzICYmICFleHBvcnRzLm5vZGVUeXBlICYmIGV4cG9ydHM7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgbW9kdWxlYC4gKi9cbnZhciBmcmVlTW9kdWxlID0gZnJlZUV4cG9ydHMgJiYgdHlwZW9mIG1vZHVsZSA9PSAnb2JqZWN0JyAmJiBtb2R1bGUgJiYgIW1vZHVsZS5ub2RlVHlwZSAmJiBtb2R1bGU7XG5cbi8qKiBEZXRlY3QgdGhlIHBvcHVsYXIgQ29tbW9uSlMgZXh0ZW5zaW9uIGBtb2R1bGUuZXhwb3J0c2AuICovXG52YXIgbW9kdWxlRXhwb3J0cyA9IGZyZWVNb2R1bGUgJiYgZnJlZU1vZHVsZS5leHBvcnRzID09PSBmcmVlRXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBwcm9jZXNzYCBmcm9tIE5vZGUuanMuICovXG52YXIgZnJlZVByb2Nlc3MgPSBtb2R1bGVFeHBvcnRzICYmIGZyZWVHbG9iYWwucHJvY2VzcztcblxuLyoqIFVzZWQgdG8gYWNjZXNzIGZhc3RlciBOb2RlLmpzIGhlbHBlcnMuICovXG52YXIgbm9kZVV0aWwgPSAoZnVuY3Rpb24oKSB7XG4gIHRyeSB7XG4gICAgcmV0dXJuIGZyZWVQcm9jZXNzICYmIGZyZWVQcm9jZXNzLmJpbmRpbmcgJiYgZnJlZVByb2Nlc3MuYmluZGluZygndXRpbCcpO1xuICB9IGNhdGNoIChlKSB7fVxufSgpKTtcblxubW9kdWxlLmV4cG9ydHMgPSBub2RlVXRpbDtcbiIsIi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKlxuICogVXNlZCB0byByZXNvbHZlIHRoZVxuICogW2B0b1N0cmluZ1RhZ2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLW9iamVjdC5wcm90b3R5cGUudG9zdHJpbmcpXG4gKiBvZiB2YWx1ZXMuXG4gKi9cbnZhciBuYXRpdmVPYmplY3RUb1N0cmluZyA9IG9iamVjdFByb3RvLnRvU3RyaW5nO1xuXG4vKipcbiAqIENvbnZlcnRzIGB2YWx1ZWAgdG8gYSBzdHJpbmcgdXNpbmcgYE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmdgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjb252ZXJ0LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgY29udmVydGVkIHN0cmluZy5cbiAqL1xuZnVuY3Rpb24gb2JqZWN0VG9TdHJpbmcodmFsdWUpIHtcbiAgcmV0dXJuIG5hdGl2ZU9iamVjdFRvU3RyaW5nLmNhbGwodmFsdWUpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IG9iamVjdFRvU3RyaW5nO1xuIiwidmFyIGZyZWVHbG9iYWwgPSByZXF1aXJlKCcuL19mcmVlR2xvYmFsJyk7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgc2VsZmAuICovXG52YXIgZnJlZVNlbGYgPSB0eXBlb2Ygc2VsZiA9PSAnb2JqZWN0JyAmJiBzZWxmICYmIHNlbGYuT2JqZWN0ID09PSBPYmplY3QgJiYgc2VsZjtcblxuLyoqIFVzZWQgYXMgYSByZWZlcmVuY2UgdG8gdGhlIGdsb2JhbCBvYmplY3QuICovXG52YXIgcm9vdCA9IGZyZWVHbG9iYWwgfHwgZnJlZVNlbGYgfHwgRnVuY3Rpb24oJ3JldHVybiB0aGlzJykoKTtcblxubW9kdWxlLmV4cG9ydHMgPSByb290O1xuIiwidmFyIGJhc2VGb3IgPSByZXF1aXJlKCcuL19iYXNlRm9yJyksXG4gICAgY2FzdEZ1bmN0aW9uID0gcmVxdWlyZSgnLi9fY2FzdEZ1bmN0aW9uJyksXG4gICAga2V5c0luID0gcmVxdWlyZSgnLi9rZXlzSW4nKTtcblxuLyoqXG4gKiBJdGVyYXRlcyBvdmVyIG93biBhbmQgaW5oZXJpdGVkIGVudW1lcmFibGUgc3RyaW5nIGtleWVkIHByb3BlcnRpZXMgb2YgYW5cbiAqIG9iamVjdCBhbmQgaW52b2tlcyBgaXRlcmF0ZWVgIGZvciBlYWNoIHByb3BlcnR5LiBUaGUgaXRlcmF0ZWUgaXMgaW52b2tlZFxuICogd2l0aCB0aHJlZSBhcmd1bWVudHM6ICh2YWx1ZSwga2V5LCBvYmplY3QpLiBJdGVyYXRlZSBmdW5jdGlvbnMgbWF5IGV4aXRcbiAqIGl0ZXJhdGlvbiBlYXJseSBieSBleHBsaWNpdGx5IHJldHVybmluZyBgZmFsc2VgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4zLjBcbiAqIEBjYXRlZ29yeSBPYmplY3RcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3QgVGhlIG9iamVjdCB0byBpdGVyYXRlIG92ZXIuXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBbaXRlcmF0ZWU9Xy5pZGVudGl0eV0gVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEByZXR1cm5zIHtPYmplY3R9IFJldHVybnMgYG9iamVjdGAuXG4gKiBAc2VlIF8uZm9ySW5SaWdodFxuICogQGV4YW1wbGVcbiAqXG4gKiBmdW5jdGlvbiBGb28oKSB7XG4gKiAgIHRoaXMuYSA9IDE7XG4gKiAgIHRoaXMuYiA9IDI7XG4gKiB9XG4gKlxuICogRm9vLnByb3RvdHlwZS5jID0gMztcbiAqXG4gKiBfLmZvckluKG5ldyBGb28sIGZ1bmN0aW9uKHZhbHVlLCBrZXkpIHtcbiAqICAgY29uc29sZS5sb2coa2V5KTtcbiAqIH0pO1xuICogLy8gPT4gTG9ncyAnYScsICdiJywgdGhlbiAnYycgKGl0ZXJhdGlvbiBvcmRlciBpcyBub3QgZ3VhcmFudGVlZCkuXG4gKi9cbmZ1bmN0aW9uIGZvckluKG9iamVjdCwgaXRlcmF0ZWUpIHtcbiAgcmV0dXJuIG9iamVjdCA9PSBudWxsXG4gICAgPyBvYmplY3RcbiAgICA6IGJhc2VGb3Iob2JqZWN0LCBjYXN0RnVuY3Rpb24oaXRlcmF0ZWUpLCBrZXlzSW4pO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGZvckluO1xuIiwiLyoqXG4gKiBUaGlzIG1ldGhvZCByZXR1cm5zIHRoZSBmaXJzdCBhcmd1bWVudCBpdCByZWNlaXZlcy5cbiAqXG4gKiBAc3RhdGljXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBtZW1iZXJPZiBfXG4gKiBAY2F0ZWdvcnkgVXRpbFxuICogQHBhcmFtIHsqfSB2YWx1ZSBBbnkgdmFsdWUuXG4gKiBAcmV0dXJucyB7Kn0gUmV0dXJucyBgdmFsdWVgLlxuICogQGV4YW1wbGVcbiAqXG4gKiB2YXIgb2JqZWN0ID0geyAnYSc6IDEgfTtcbiAqXG4gKiBjb25zb2xlLmxvZyhfLmlkZW50aXR5KG9iamVjdCkgPT09IG9iamVjdCk7XG4gKiAvLyA9PiB0cnVlXG4gKi9cbmZ1bmN0aW9uIGlkZW50aXR5KHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpZGVudGl0eTtcbiIsInZhciBiYXNlSXNBcmd1bWVudHMgPSByZXF1aXJlKCcuL19iYXNlSXNBcmd1bWVudHMnKSxcbiAgICBpc09iamVjdExpa2UgPSByZXF1aXJlKCcuL2lzT2JqZWN0TGlrZScpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBwcm9wZXJ0eUlzRW51bWVyYWJsZSA9IG9iamVjdFByb3RvLnByb3BlcnR5SXNFbnVtZXJhYmxlO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGxpa2VseSBhbiBgYXJndW1lbnRzYCBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gYGFyZ3VtZW50c2Agb2JqZWN0LFxuICogIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0FyZ3VtZW50cyhmdW5jdGlvbigpIHsgcmV0dXJuIGFyZ3VtZW50czsgfSgpKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJndW1lbnRzKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNBcmd1bWVudHMgPSBiYXNlSXNBcmd1bWVudHMoZnVuY3Rpb24oKSB7IHJldHVybiBhcmd1bWVudHM7IH0oKSkgPyBiYXNlSXNBcmd1bWVudHMgOiBmdW5jdGlvbih2YWx1ZSkge1xuICByZXR1cm4gaXNPYmplY3RMaWtlKHZhbHVlKSAmJiBoYXNPd25Qcm9wZXJ0eS5jYWxsKHZhbHVlLCAnY2FsbGVlJykgJiZcbiAgICAhcHJvcGVydHlJc0VudW1lcmFibGUuY2FsbCh2YWx1ZSwgJ2NhbGxlZScpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc0FyZ3VtZW50cztcbiIsIi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhbiBgQXJyYXlgIG9iamVjdC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBhcnJheSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQXJyYXkoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXkoZG9jdW1lbnQuYm9keS5jaGlsZHJlbik7XG4gKiAvLyA9PiBmYWxzZVxuICpcbiAqIF8uaXNBcnJheSgnYWJjJyk7XG4gKiAvLyA9PiBmYWxzZVxuICpcbiAqIF8uaXNBcnJheShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzQXJyYXkgPSBBcnJheS5pc0FycmF5O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJyYXk7XG4iLCJ2YXIgaXNGdW5jdGlvbiA9IHJlcXVpcmUoJy4vaXNGdW5jdGlvbicpLFxuICAgIGlzTGVuZ3RoID0gcmVxdWlyZSgnLi9pc0xlbmd0aCcpO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGFycmF5LWxpa2UuIEEgdmFsdWUgaXMgY29uc2lkZXJlZCBhcnJheS1saWtlIGlmIGl0J3NcbiAqIG5vdCBhIGZ1bmN0aW9uIGFuZCBoYXMgYSBgdmFsdWUubGVuZ3RoYCB0aGF0J3MgYW4gaW50ZWdlciBncmVhdGVyIHRoYW4gb3JcbiAqIGVxdWFsIHRvIGAwYCBhbmQgbGVzcyB0aGFuIG9yIGVxdWFsIHRvIGBOdW1iZXIuTUFYX1NBRkVfSU5URUdFUmAuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYXJyYXktbGlrZSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0FycmF5TGlrZShkb2N1bWVudC5ib2R5LmNoaWxkcmVuKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKCdhYmMnKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKF8ubm9vcCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc0FycmF5TGlrZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiBpc0xlbmd0aCh2YWx1ZS5sZW5ndGgpICYmICFpc0Z1bmN0aW9uKHZhbHVlKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0FycmF5TGlrZTtcbiIsInZhciByb290ID0gcmVxdWlyZSgnLi9fcm9vdCcpLFxuICAgIHN0dWJGYWxzZSA9IHJlcXVpcmUoJy4vc3R1YkZhbHNlJyk7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZXhwb3J0c2AuICovXG52YXIgZnJlZUV4cG9ydHMgPSB0eXBlb2YgZXhwb3J0cyA9PSAnb2JqZWN0JyAmJiBleHBvcnRzICYmICFleHBvcnRzLm5vZGVUeXBlICYmIGV4cG9ydHM7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgbW9kdWxlYC4gKi9cbnZhciBmcmVlTW9kdWxlID0gZnJlZUV4cG9ydHMgJiYgdHlwZW9mIG1vZHVsZSA9PSAnb2JqZWN0JyAmJiBtb2R1bGUgJiYgIW1vZHVsZS5ub2RlVHlwZSAmJiBtb2R1bGU7XG5cbi8qKiBEZXRlY3QgdGhlIHBvcHVsYXIgQ29tbW9uSlMgZXh0ZW5zaW9uIGBtb2R1bGUuZXhwb3J0c2AuICovXG52YXIgbW9kdWxlRXhwb3J0cyA9IGZyZWVNb2R1bGUgJiYgZnJlZU1vZHVsZS5leHBvcnRzID09PSBmcmVlRXhwb3J0cztcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgQnVmZmVyID0gbW9kdWxlRXhwb3J0cyA/IHJvb3QuQnVmZmVyIDogdW5kZWZpbmVkO1xuXG4vKiBCdWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcyBmb3IgdGhvc2Ugd2l0aCB0aGUgc2FtZSBuYW1lIGFzIG90aGVyIGBsb2Rhc2hgIG1ldGhvZHMuICovXG52YXIgbmF0aXZlSXNCdWZmZXIgPSBCdWZmZXIgPyBCdWZmZXIuaXNCdWZmZXIgOiB1bmRlZmluZWQ7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYSBidWZmZXIuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjMuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBidWZmZXIsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0J1ZmZlcihuZXcgQnVmZmVyKDIpKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQnVmZmVyKG5ldyBVaW50OEFycmF5KDIpKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc0J1ZmZlciA9IG5hdGl2ZUlzQnVmZmVyIHx8IHN0dWJGYWxzZTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc0J1ZmZlcjtcbiIsInZhciBiYXNlR2V0VGFnID0gcmVxdWlyZSgnLi9fYmFzZUdldFRhZycpLFxuICAgIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9pc09iamVjdCcpO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgYXN5bmNUYWcgPSAnW29iamVjdCBBc3luY0Z1bmN0aW9uXScsXG4gICAgZnVuY1RhZyA9ICdbb2JqZWN0IEZ1bmN0aW9uXScsXG4gICAgZ2VuVGFnID0gJ1tvYmplY3QgR2VuZXJhdG9yRnVuY3Rpb25dJyxcbiAgICBwcm94eVRhZyA9ICdbb2JqZWN0IFByb3h5XSc7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhIGBGdW5jdGlvbmAgb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgZnVuY3Rpb24sIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0Z1bmN0aW9uKF8pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNGdW5jdGlvbigvYWJjLyk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc0Z1bmN0aW9uKHZhbHVlKSB7XG4gIGlmICghaXNPYmplY3QodmFsdWUpKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIC8vIFRoZSB1c2Ugb2YgYE9iamVjdCN0b1N0cmluZ2AgYXZvaWRzIGlzc3VlcyB3aXRoIHRoZSBgdHlwZW9mYCBvcGVyYXRvclxuICAvLyBpbiBTYWZhcmkgOSB3aGljaCByZXR1cm5zICdvYmplY3QnIGZvciB0eXBlZCBhcnJheXMgYW5kIG90aGVyIGNvbnN0cnVjdG9ycy5cbiAgdmFyIHRhZyA9IGJhc2VHZXRUYWcodmFsdWUpO1xuICByZXR1cm4gdGFnID09IGZ1bmNUYWcgfHwgdGFnID09IGdlblRhZyB8fCB0YWcgPT0gYXN5bmNUYWcgfHwgdGFnID09IHByb3h5VGFnO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzRnVuY3Rpb247XG4iLCIvKiogVXNlZCBhcyByZWZlcmVuY2VzIGZvciB2YXJpb3VzIGBOdW1iZXJgIGNvbnN0YW50cy4gKi9cbnZhciBNQVhfU0FGRV9JTlRFR0VSID0gOTAwNzE5OTI1NDc0MDk5MTtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGFycmF5LWxpa2UgbGVuZ3RoLlxuICpcbiAqICoqTm90ZToqKiBUaGlzIG1ldGhvZCBpcyBsb29zZWx5IGJhc2VkIG9uXG4gKiBbYFRvTGVuZ3RoYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtdG9sZW5ndGgpLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgbGVuZ3RoLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNMZW5ndGgoMyk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0xlbmd0aChOdW1iZXIuTUlOX1ZBTFVFKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0xlbmd0aChJbmZpbml0eSk7XG4gKiAvLyA9PiBmYWxzZVxuICpcbiAqIF8uaXNMZW5ndGgoJzMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzTGVuZ3RoKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ251bWJlcicgJiZcbiAgICB2YWx1ZSA+IC0xICYmIHZhbHVlICUgMSA9PSAwICYmIHZhbHVlIDw9IE1BWF9TQUZFX0lOVEVHRVI7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNMZW5ndGg7XG4iLCIvKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIHRoZVxuICogW2xhbmd1YWdlIHR5cGVdKGh0dHA6Ly93d3cuZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy1lY21hc2NyaXB0LWxhbmd1YWdlLXR5cGVzKVxuICogb2YgYE9iamVjdGAuIChlLmcuIGFycmF5cywgZnVuY3Rpb25zLCBvYmplY3RzLCByZWdleGVzLCBgbmV3IE51bWJlcigwKWAsIGFuZCBgbmV3IFN0cmluZygnJylgKVxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIG9iamVjdCwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzT2JqZWN0KHt9KTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0KFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdChfLm5vb3ApO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3QobnVsbCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc09iamVjdCh2YWx1ZSkge1xuICB2YXIgdHlwZSA9IHR5cGVvZiB2YWx1ZTtcbiAgcmV0dXJuIHZhbHVlICE9IG51bGwgJiYgKHR5cGUgPT0gJ29iamVjdCcgfHwgdHlwZSA9PSAnZnVuY3Rpb24nKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc09iamVjdDtcbiIsIi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UuIEEgdmFsdWUgaXMgb2JqZWN0LWxpa2UgaWYgaXQncyBub3QgYG51bGxgXG4gKiBhbmQgaGFzIGEgYHR5cGVvZmAgcmVzdWx0IG9mIFwib2JqZWN0XCIuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc09iamVjdExpa2Uoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc09iamVjdExpa2UobnVsbCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc09iamVjdExpa2UodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlICE9IG51bGwgJiYgdHlwZW9mIHZhbHVlID09ICdvYmplY3QnO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzT2JqZWN0TGlrZTtcbiIsInZhciBiYXNlSXNUeXBlZEFycmF5ID0gcmVxdWlyZSgnLi9fYmFzZUlzVHlwZWRBcnJheScpLFxuICAgIGJhc2VVbmFyeSA9IHJlcXVpcmUoJy4vX2Jhc2VVbmFyeScpLFxuICAgIG5vZGVVdGlsID0gcmVxdWlyZSgnLi9fbm9kZVV0aWwnKTtcblxuLyogTm9kZS5qcyBoZWxwZXIgcmVmZXJlbmNlcy4gKi9cbnZhciBub2RlSXNUeXBlZEFycmF5ID0gbm9kZVV0aWwgJiYgbm9kZVV0aWwuaXNUeXBlZEFycmF5O1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYSB0eXBlZCBhcnJheS5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDMuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHR5cGVkIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNUeXBlZEFycmF5KG5ldyBVaW50OEFycmF5KTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzVHlwZWRBcnJheShbXSk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNUeXBlZEFycmF5ID0gbm9kZUlzVHlwZWRBcnJheSA/IGJhc2VVbmFyeShub2RlSXNUeXBlZEFycmF5KSA6IGJhc2VJc1R5cGVkQXJyYXk7XG5cbm1vZHVsZS5leHBvcnRzID0gaXNUeXBlZEFycmF5O1xuIiwidmFyIGFycmF5TGlrZUtleXMgPSByZXF1aXJlKCcuL19hcnJheUxpa2VLZXlzJyksXG4gICAgYmFzZUtleXNJbiA9IHJlcXVpcmUoJy4vX2Jhc2VLZXlzSW4nKSxcbiAgICBpc0FycmF5TGlrZSA9IHJlcXVpcmUoJy4vaXNBcnJheUxpa2UnKTtcblxuLyoqXG4gKiBDcmVhdGVzIGFuIGFycmF5IG9mIHRoZSBvd24gYW5kIGluaGVyaXRlZCBlbnVtZXJhYmxlIHByb3BlcnR5IG5hbWVzIG9mIGBvYmplY3RgLlxuICpcbiAqICoqTm90ZToqKiBOb24tb2JqZWN0IHZhbHVlcyBhcmUgY29lcmNlZCB0byBvYmplY3RzLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBPYmplY3RcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3QgVGhlIG9iamVjdCB0byBxdWVyeS5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKiBAZXhhbXBsZVxuICpcbiAqIGZ1bmN0aW9uIEZvbygpIHtcbiAqICAgdGhpcy5hID0gMTtcbiAqICAgdGhpcy5iID0gMjtcbiAqIH1cbiAqXG4gKiBGb28ucHJvdG90eXBlLmMgPSAzO1xuICpcbiAqIF8ua2V5c0luKG5ldyBGb28pO1xuICogLy8gPT4gWydhJywgJ2InLCAnYyddIChpdGVyYXRpb24gb3JkZXIgaXMgbm90IGd1YXJhbnRlZWQpXG4gKi9cbmZ1bmN0aW9uIGtleXNJbihvYmplY3QpIHtcbiAgcmV0dXJuIGlzQXJyYXlMaWtlKG9iamVjdCkgPyBhcnJheUxpa2VLZXlzKG9iamVjdCwgdHJ1ZSkgOiBiYXNlS2V5c0luKG9iamVjdCk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0ga2V5c0luO1xuIiwiLyoqXG4gKiBUaGlzIG1ldGhvZCByZXR1cm5zIGBmYWxzZWAuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjEzLjBcbiAqIEBjYXRlZ29yeSBVdGlsXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLnRpbWVzKDIsIF8uc3R1YkZhbHNlKTtcbiAqIC8vID0+IFtmYWxzZSwgZmFsc2VdXG4gKi9cbmZ1bmN0aW9uIHN0dWJGYWxzZSgpIHtcbiAgcmV0dXJuIGZhbHNlO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IHN0dWJGYWxzZTtcbiIsIi8qIGpzaGludCBicm93c2VyOnRydWUgKi9cclxuLyogZ2xvYmFscyBfX09QVElPTlNfXzp0cnVlICovXHJcblxyXG5pbXBvcnQgTGF2YUpzIGZyb20gJy4vbGF2YS9MYXZhJztcclxuaW1wb3J0IHsgZG9tTG9hZGVkIH0gZnJvbSAnLi9sYXZhL1V0aWxzJztcclxuXHJcbi8qKlxyXG4gKiBBc3NpZ24gdGhlIExhdmEuanMgbW9kdWxlIHRvIHRoZSB3aW5kb3cgYW5kXHJcbiAqIGxldCAkbGF2YSBiZSBhbiBhbGlhcyB0byB0aGUgbW9kdWxlLlxyXG4gKi9cclxud2luZG93LmxhdmEgPSBuZXcgTGF2YUpzKCk7XHJcblxyXG4vKipcclxuICogSWYgTGF2YS5qcyB3YXMgbG9hZGVkIGZyb20gTGF2YWNoYXJ0cywgdGhlIF9fT1BUSU9OU19fXHJcbiAqIHBsYWNlaG9sZGVyIHdpbGwgYmUgYSBKU09OIG9iamVjdCBvZiBvcHRpb25zIHRoYXRcclxuICogd2VyZSBzZXQgc2VydmVyLXNpZGUuXHJcbiAqL1xyXG5pZiAodHlwZW9mIF9fT1BUSU9OU19fICE9PSAndW5kZWZpbmVkJykge1xyXG4gICAgd2luZG93LmxhdmEub3B0aW9ucyA9IF9fT1BUSU9OU19fO1xyXG59XHJcblxyXG4vKipcclxuICogSWYgTGF2YS5qcyB3YXMgc2V0IHRvIGF1dG9fcnVuIHRoZW4gb25jZSB0aGUgRE9NXHJcbiAqIGlzIHJlYWR5LCByZW5kZXJpbmcgd2lsbCBiZWdpbi5cclxuICovXHJcbmlmICh3aW5kb3cubGF2YS5vcHRpb25zLmF1dG9fcnVuID09PSB0cnVlKSB7XHJcbiAgICBkb21Mb2FkZWQoKS50aGVuKCgpID0+IHtcclxuICAgICAgICB3aW5kb3cubGF2YS5ydW4oKTtcclxuICAgIH0pO1xyXG59XHJcbiIsIi8qKlxyXG4gKiBDaGFydCBtb2R1bGVcclxuICpcclxuICogQGNsYXNzICAgICBDaGFydFxyXG4gKiBAbW9kdWxlICAgIGxhdmEvQ2hhcnRcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuaW1wb3J0IF9mb3JJbiBmcm9tICdsb2Rhc2gvZm9ySW4nO1xyXG5pbXBvcnQgUmVuZGVyYWJsZSBmcm9tICcuL1JlbmRlcmFibGUnO1xyXG5cclxuLyoqXHJcbiAqIENoYXJ0IGNsYXNzIHVzZWQgZm9yIHN0b3JpbmcgYWxsIHRoZSBuZWVkZWQgY29uZmlndXJhdGlvbiBmb3IgcmVuZGVyaW5nLlxyXG4gKlxyXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBDaGFydFxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBsYWJlbCAgICAgLSBMYWJlbCBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgY2hhcnQgICAgIC0gR29vZ2xlIGNoYXJ0IG9iamVjdC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgcGFja2FnZSAgIC0gVHlwZSBvZiBHb29nbGUgY2hhcnQgcGFja2FnZSB0byBsb2FkLlxyXG4gKiBAcHJvcGVydHkge2Jvb2xlYW59ICBwbmdPdXRwdXQgLSBTaG91bGQgdGhlIGNoYXJ0IGJlIGRpc3BsYXllZCBhcyBhIFBORy5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZGF0YSAgICAgIC0gRGF0YXRhYmxlIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIG9wdGlvbnMgICAtIENvbmZpZ3VyYXRpb24gb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBldmVudHMgICAgLSBFdmVudHMgYW5kIGNhbGxiYWNrcyB0byBhcHBseSB0byB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7QXJyYXl9ICAgIGZvcm1hdHMgICAtIEZvcm1hdHRlcnMgdG8gYXBwbHkgdG8gdGhlIGNoYXJ0IGRhdGEuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSB1dWlkICAgICAgLSBDcmVhdGVzIGlkZW50aWZpY2F0aW9uIHN0cmluZyBmb3IgdGhlIGNoYXJ0LlxyXG4gKi9cclxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hhcnQgZXh0ZW5kcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIC8qKlxyXG4gICAgICogQ2hhcnQgQ2xhc3NcclxuICAgICAqXHJcbiAgICAgKiBUaGlzIGlzIHRoZSBqYXZhc2NyaXB0IHZlcnNpb24gb2YgYSBsYXZhY2hhcnQgd2l0aCBtZXRob2RzIGZvciBpbnRlcmFjdGluZyB3aXRoXHJcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGpzb25cclxuICAgICAqIEBjb25zdHJ1Y3RvclxyXG4gICAgICovXHJcbiAgICBjb25zdHJ1Y3RvciAoanNvbikge1xyXG4gICAgICAgIHN1cGVyKGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLmZvcm1hdHMgPSBqc29uLmZvcm1hdHM7XHJcblxyXG4gICAgICAgIHRoaXMuZXZlbnRzICAgID0gdHlwZW9mIGpzb24uZXZlbnRzID09PSAnb2JqZWN0JyA/IGpzb24uZXZlbnRzIDogbnVsbDtcclxuICAgICAgICB0aGlzLnBuZ091dHB1dCA9IHR5cGVvZiBqc29uLnBuZ091dHB1dCA9PT0gJ3VuZGVmaW5lZCcgPyBmYWxzZSA6IEJvb2xlYW4oanNvbi5wbmdPdXRwdXQpO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBbnkgZGVwZW5kZW5jeSBvbiB3aW5kb3cuZ29vZ2xlIG11c3QgYmUgaW4gdGhlIHJlbmRlciBzY29wZS5cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcclxuICAgICAgICAgICAgdGhpcy5zZXREYXRhKGpzb24uZGF0YXRhYmxlKTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uW3RoaXMuY2xhc3NdKHRoaXMuZWxlbWVudCk7XHJcblxyXG4gICAgICAgICAgICBpZiAodGhpcy5mb3JtYXRzKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLmFwcGx5Rm9ybWF0cygpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBpZiAodGhpcy5ldmVudHMpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMuX2F0dGFjaEV2ZW50cygpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLnBuZ091dHB1dCkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5kcmF3UG5nKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IGFzIGEgUE5HIGluc3RlYWQgb2YgdGhlIHN0YW5kYXJkIFNWR1xyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBleHRlcm5hbCBcImNoYXJ0LmdldEltYWdlVVJJXCJcclxuICAgICAqIEBzZWUge0BsaW5rIGh0dHBzOi8vZGV2ZWxvcGVycy5nb29nbGUuY29tL2NoYXJ0L2ludGVyYWN0aXZlL2RvY3MvcHJpbnRpbmd8UHJpbnRpbmcgUE5HIENoYXJ0c31cclxuICAgICAqL1xyXG4gICAgZHJhd1BuZygpIHtcclxuICAgICAgICBsZXQgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XHJcbiAgICAgICAgICAgIGltZy5zcmMgPSB0aGlzLmdjaGFydC5nZXRJbWFnZVVSSSgpO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQuaW5uZXJIVE1MID0gJyc7XHJcbiAgICAgICAgdGhpcy5lbGVtZW50LmFwcGVuZENoaWxkKGltZyk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBcHBseSB0aGUgZm9ybWF0cyB0byB0aGUgRGF0YVRhYmxlXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtBcnJheX0gZm9ybWF0c1xyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICovXHJcbiAgICBhcHBseUZvcm1hdHMoZm9ybWF0cykge1xyXG4gICAgICAgIGlmICghIGZvcm1hdHMpIHtcclxuICAgICAgICAgICAgZm9ybWF0cyA9IHRoaXMuZm9ybWF0cztcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGZvciAobGV0IGZvcm1hdCBvZiBmb3JtYXRzKSB7XHJcbiAgICAgICAgICAgIGxldCBmb3JtYXR0ZXIgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb25bZm9ybWF0LnR5cGVdKGZvcm1hdC5vcHRpb25zKTtcclxuXHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gQ29sdW1uIGluZGV4IFske2Zvcm1hdC5pbmRleH1dIGZvcm1hdHRlZCB3aXRoOmAsIGZvcm1hdHRlcik7XHJcblxyXG4gICAgICAgICAgICBmb3JtYXR0ZXIuZm9ybWF0KHRoaXMuZGF0YSwgZm9ybWF0LmluZGV4KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBdHRhY2ggdGhlIGRlZmluZWQgY2hhcnQgZXZlbnQgaGFuZGxlcnMuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2F0dGFjaEV2ZW50cygpIHtcclxuICAgICAgICBsZXQgJGNoYXJ0ID0gdGhpcztcclxuXHJcbiAgICAgICAgX2ZvckluKHRoaXMuZXZlbnRzLCBmdW5jdGlvbiAoY2FsbGJhY2ssIGV2ZW50KSB7XHJcbiAgICAgICAgICAgIGxldCBjb250ZXh0ID0gd2luZG93O1xyXG4gICAgICAgICAgICBsZXQgZnVuYyA9IGNhbGxiYWNrO1xyXG5cclxuICAgICAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ29iamVjdCcpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRleHQgPSBjb250ZXh0W2NhbGxiYWNrWzBdXTtcclxuICAgICAgICAgICAgICAgIGZ1bmMgPSBjYWxsYmFja1sxXTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBUaGUgXCIkeyRjaGFydC51dWlkfTo6JHtldmVudH1cIiBldmVudCB3aWxsIGJlIGhhbmRsZWQgYnkgXCIke2Z1bmN9XCIgaW4gdGhlIGNvbnRleHRgLCBjb250ZXh0KTtcclxuXHJcbiAgICAgICAgICAgIC8qKlxyXG4gICAgICAgICAgICAgKiBTZXQgdGhlIGNvbnRleHQgb2YgXCJ0aGlzXCIgd2l0aGluIHRoZSB1c2VyIHByb3ZpZGVkIGNhbGxiYWNrIHRvIHRoZVxyXG4gICAgICAgICAgICAgKiBjaGFydCB0aGF0IGZpcmVkIHRoZSBldmVudCB3aGlsZSBwcm92aWRpbmcgdGhlIGRhdGF0YWJsZSBvZiB0aGUgY2hhcnRcclxuICAgICAgICAgICAgICogdG8gdGhlIGNhbGxiYWNrIGFzIGFuIGFyZ3VtZW50LlxyXG4gICAgICAgICAgICAgKi9cclxuICAgICAgICAgICAgZ29vZ2xlLnZpc3VhbGl6YXRpb24uZXZlbnRzLmFkZExpc3RlbmVyKCRjaGFydC5nY2hhcnQsIGV2ZW50LCBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgIGNvbnN0IGNhbGxiYWNrID0gY29udGV4dFtmdW5jXS5iaW5kKCRjaGFydC5nY2hhcnQpO1xyXG5cclxuICAgICAgICAgICAgICAgIGNhbGxiYWNrKCRjaGFydC5kYXRhKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoqXHJcbiAqIERhc2hib2FyZCBtb2R1bGVcclxuICpcclxuICogQGNsYXNzICAgICBEYXNoYm9hcmRcclxuICogQG1vZHVsZSAgICBsYXZhL0Rhc2hib2FyZFxyXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxyXG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXHJcbiAqIEBsaWNlbnNlICAgTUlUXHJcbiAqL1xyXG5pbXBvcnQgUmVuZGVyYWJsZSBmcm9tICcuL1JlbmRlcmFibGUnO1xyXG5cclxuLyoqXHJcbiAqIERhc2hib2FyZCBjbGFzc1xyXG4gKlxyXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBEYXNoYm9hcmRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBEYXNoYm9hcmQuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHR5cGUgICAgICAtIFR5cGUgb2YgdmlzdWFsaXphdGlvbiAoRGFzaGJvYXJkKS5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgdmlzdWFsaXphdGlvbiBwYWNrYWdlIHRvIGxvYWQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIERhc2hib2FyZC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgb3B0aW9ucyAgIC0gQ29uZmlndXJhdGlvbiBvcHRpb25zLlxyXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBiaW5kaW5ncyAgLSBDaGFydCBhbmQgQ29udHJvbCBiaW5kaW5ncy5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gcmVuZGVyICAgIC0gUmVuZGVycyB0aGUgRGFzaGJvYXJkLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSB1dWlkICAgICAgLSBVbmlxdWUgaWRlbnRpZmllciBmb3IgdGhlIERhc2hib2FyZC5cclxuICovXHJcbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERhc2hib2FyZCBleHRlbmRzIFJlbmRlcmFibGVcclxue1xyXG4gICAgY29uc3RydWN0b3IoanNvbikge1xyXG4gICAgICAgIGpzb24udHlwZSA9ICdEYXNoYm9hcmQnO1xyXG5cclxuICAgICAgICBzdXBlcihqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kaW5ncyA9IGpzb24uYmluZGluZ3M7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIEFueSBkZXBlbmRlbmN5IG9uIHdpbmRvdy5nb29nbGUgbXVzdCBiZSBpbiB0aGUgcmVuZGVyIHNjb3BlLlxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMucmVuZGVyID0gKCkgPT4ge1xyXG4gICAgICAgICAgICB0aGlzLnNldERhdGEoanNvbi5kYXRhdGFibGUpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5nY2hhcnQgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGFzaGJvYXJkKHRoaXMuZWxlbWVudCk7XHJcblxyXG4gICAgICAgICAgICB0aGlzLl9hdHRhY2hCaW5kaW5ncygpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuZXZlbnRzKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgdGhpcy5kcmF3KCk7XHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICAvLyBAVE9ETzogdGhpcyBuZWVkcyB0byBiZSBtb2RpZmllZCBmb3IgdGhlIG90aGVyIHR5cGVzIG9mIGJpbmRpbmdzLlxyXG5cclxuICAgIC8qKlxyXG4gICAgICogUHJvY2VzcyBhbmQgYXR0YWNoIHRoZSBiaW5kaW5ncyB0byB0aGUgZGFzaGJvYXJkLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKi9cclxuICAgIF9hdHRhY2hCaW5kaW5ncygpIHtcclxuICAgICAgICBmb3IgKGxldCBiaW5kaW5nIG9mIHRoaXMuYmluZGluZ3MpIHtcclxuICAgICAgICAgICAgbGV0IGNvbnRyb2xXcmFwcyA9IFtdO1xyXG4gICAgICAgICAgICBsZXQgY2hhcnRXcmFwcyA9IFtdO1xyXG5cclxuICAgICAgICAgICAgZm9yIChsZXQgY29udHJvbFdyYXAgb2YgYmluZGluZy5jb250cm9sV3JhcHBlcnMpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRyb2xXcmFwcy5wdXNoKFxyXG4gICAgICAgICAgICAgICAgICAgIG5ldyBnb29nbGUudmlzdWFsaXphdGlvbi5Db250cm9sV3JhcHBlcihjb250cm9sV3JhcClcclxuICAgICAgICAgICAgICAgICk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGZvciAobGV0IGNoYXJ0V3JhcCBvZiBiaW5kaW5nLmNoYXJ0V3JhcHBlcnMpIHtcclxuICAgICAgICAgICAgICAgIGNoYXJ0V3JhcHMucHVzaChcclxuICAgICAgICAgICAgICAgICAgICBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uQ2hhcnRXcmFwcGVyKGNoYXJ0V3JhcClcclxuICAgICAgICAgICAgICAgICk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0LmJpbmQoY29udHJvbFdyYXBzLCBjaGFydFdyYXBzKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbn1cclxuIiwiLyoqXHJcbiAqIEVycm9ycyBtb2R1bGVcclxuICpcclxuICogQG1vZHVsZSAgICBsYXZhL0Vycm9yc1xyXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxyXG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXHJcbiAqIEBsaWNlbnNlICAgTUlUXHJcbiAqL1xyXG5jbGFzcyBMYXZhSnNFcnJvciBleHRlbmRzIEVycm9yXHJcbntcclxuICAgIGNvbnN0cnVjdG9yIChtZXNzYWdlKSB7XHJcbiAgICAgICAgc3VwZXIoKTtcclxuXHJcbiAgICAgICAgdGhpcy5uYW1lICAgID0gJ0xhdmFKc0Vycm9yJztcclxuICAgICAgICB0aGlzLm1lc3NhZ2UgPSAobWVzc2FnZSB8fCAnJyk7XHJcbiAgICB9O1xyXG59XHJcblxyXG4vKipcclxuICogSW52YWxpZENhbGxiYWNrIEVycm9yXHJcbiAqXHJcbiAqIHRocm93biB3aGVuIHdoZW4gYW55dGhpbmcgYnV0IGEgZnVuY3Rpb24gaXMgZ2l2ZW4gYXMgYSBjYWxsYmFja1xyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgSW52YWxpZENhbGxiYWNrIGV4dGVuZHMgTGF2YUpzRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGNhbGxiYWNrKSB7XHJcbiAgICAgICAgc3VwZXIoYFtsYXZhLmpzXSBcIiR7dHlwZW9mIGNhbGxiYWNrfVwiIGlzIG5vdCBhIHZhbGlkIGNhbGxiYWNrLmApO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgPSAnSW52YWxpZENhbGxiYWNrJztcclxuICAgIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIEludmFsaWRMYWJlbCBFcnJvclxyXG4gKlxyXG4gKiBUaHJvd24gd2hlbiB3aGVuIGFueXRoaW5nIGJ1dCBhIHN0cmluZyBpcyBnaXZlbiBhcyBhIGxhYmVsLlxyXG4gKlxyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgSW52YWxpZExhYmVsIGV4dGVuZHMgTGF2YUpzRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGxhYmVsKSB7XHJcbiAgICAgICAgc3VwZXIoYFtsYXZhLmpzXSBcIiR7dHlwZW9mIGxhYmVsfVwiIGlzIG5vdCBhIHZhbGlkIGxhYmVsLmApO1xyXG4gICAgICAgIHRoaXMubmFtZSA9ICdJbnZhbGlkTGFiZWwnO1xyXG4gICAgfVxyXG59XHJcblxyXG4vKipcclxuICogRWxlbWVudElkTm90Rm91bmQgRXJyb3JcclxuICpcclxuICogVGhyb3duIHdoZW4gd2hlbiBhbnl0aGluZyBidXQgYSBzdHJpbmcgaXMgZ2l2ZW4gYXMgYSBsYWJlbC5cclxuICpcclxuICogQHR5cGUge2Z1bmN0aW9ufVxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIEVsZW1lbnRJZE5vdEZvdW5kIGV4dGVuZHMgTGF2YUpzRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGVsZW1JZCkge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gRE9NIG5vZGUgd2hlcmUgaWQ9XCIke2VsZW1JZH1cIiB3YXMgbm90IGZvdW5kLmApO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgPSAnRWxlbWVudElkTm90Rm91bmQnO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qIGpzaGludCBicm93c2VyOnRydWUgKi9cclxuLyogZ2xvYmFscyBnb29nbGU6dHJ1ZSAqL1xyXG4vKipcclxuICogbGF2YS5qcyBtb2R1bGVcclxuICpcclxuICogQG1vZHVsZSAgICBsYXZhL0xhdmFcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIGh0dHA6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9NSVQgTUlUXHJcbiAqL1xyXG5pbXBvcnQgX2ZvckluIGZyb20gJ2xvZGFzaC9mb3JJbic7XHJcbmltcG9ydCBFdmVudEVtaXR0ZXIgZnJvbSAnZXZlbnRzJztcclxuaW1wb3J0IENoYXJ0IGZyb20gJy4vQ2hhcnQnO1xyXG5pbXBvcnQgRGFzaGJvYXJkIGZyb20gJy4vRGFzaGJvYXJkJztcclxuaW1wb3J0IGRlZmF1bHRPcHRpb25zIGZyb20gJy4vT3B0aW9ucyc7XHJcbmltcG9ydCB7YWRkRXZlbnQsIG5vb3B9IGZyb20gJy4vVXRpbHMnO1xyXG5pbXBvcnQge0ludmFsaWRDYWxsYmFjaywgUmVuZGVyYWJsZU5vdEZvdW5kfSBmcm9tICcuL0Vycm9ycydcclxuXHJcbi8qKlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICAgICAgICAgICAgVkVSU0lPTlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICAgICAgICAgICAgR09PR0xFX0FQSV9WRVJTSU9OXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgICAgICAgICAgICBHT09HTEVfTE9BREVSX1VSTFxyXG4gKiBAcHJvcGVydHkge0NoYXJ0fSAgICAgICAgICAgICAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtEYXNoYm9hcmR9ICAgICAgICAgIERhc2hib2FyZFxyXG4gKiBAcHJvcGVydHkge29iamVjdH0gICAgICAgICAgICAgb3B0aW9uc1xyXG4gKiBAcHJvcGVydHkge2Z1bmN0aW9ufSAgICAgICAgICAgX3JlYWR5Q2FsbGJhY2tcclxuICogQHByb3BlcnR5IHtBcnJheS48c3RyaW5nPn0gICAgIF9wYWNrYWdlc1xyXG4gKiBAcHJvcGVydHkge0FycmF5LjxSZW5kZXJhYmxlPn0gX3JlbmRlcmFibGVzXHJcbiAqL1xyXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMYXZhSnMgZXh0ZW5kcyBFdmVudEVtaXR0ZXJcclxue1xyXG4gICAgY29uc3RydWN0b3IobmV3T3B0aW9ucykge1xyXG4gICAgICAgIHN1cGVyKCk7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFZlcnNpb24gb2YgdGhlIExhdmEuanMgbW9kdWxlLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge3N0cmluZ31cclxuICAgICAgICAgKiBAcHVibGljXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5WRVJTSU9OID0gJzQuMC4wJztcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogVmVyc2lvbiBvZiB0aGUgR29vZ2xlIGNoYXJ0cyBBUEkgdG8gbG9hZC5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OID0gJ2N1cnJlbnQnO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBVcmxzIHRvIEdvb2dsZSdzIHN0YXRpYyBsb2FkZXJcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0xPQURFUl9VUkwgPSAnaHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vY2hhcnRzL2xvYWRlci5qcyc7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIENoYXJ0IG1vZHVsZSB3aXRoaW4gTGF2YS5qc1xyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge0NoYXJ0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkNoYXJ0ID0gQ2hhcnQ7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIERhc2hib2FyZCBtb2R1bGUgd2l0aGluIExhdmEuanNcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtEYXNoYm9hcmR9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuRGFzaGJvYXJkID0gRGFzaGJvYXJkO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBKU09OIG9iamVjdCBvZiBjb25maWcgaXRlbXMuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7T2JqZWN0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLm9wdGlvbnMgPSBuZXdPcHRpb25zIHx8IGRlZmF1bHRPcHRpb25zO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBSZWZlcmVuY2UgdG8gdGhlIGdvb2dsZS52aXN1YWxpemF0aW9uIG9iamVjdC5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtnb29nbGUudmlzdWFsaXphdGlvbn1cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnZpc3VhbGl6YXRpb24gPSBudWxsO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBcnJheSBvZiB2aXN1YWxpemF0aW9uIHBhY2thZ2VzIGZvciBjaGFydHMgYW5kIGRhc2hib2FyZHMuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7QXJyYXkuPHN0cmluZz59XHJcbiAgICAgICAgICogQHByaXZhdGVcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLl9wYWNrYWdlcyA9IFtdO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBcnJheSBvZiBjaGFydHMgYW5kIGRhc2hib2FyZHMgc3RvcmVkIGluIHRoZSBtb2R1bGUuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7QXJyYXkuPFJlbmRlcmFibGU+fVxyXG4gICAgICAgICAqIEBwcml2YXRlXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5fcmVuZGVyYWJsZXMgPSBbXTtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogUmVhZHkgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIHdoZW4gdGhlIG1vZHVsZSBpcyBmaW5pc2hlZCBydW5uaW5nLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQGNhbGxiYWNrIF9yZWFkeUNhbGxiYWNrXHJcbiAgICAgICAgICogQHByaXZhdGVcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrID0gbm9vcDtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIENoYXJ0IGNsYXNzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7UmVuZGVyYWJsZX1cclxuICAgICAqL1xyXG4gICAgY3JlYXRlQ2hhcnQoanNvbikge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdDcmVhdGluZyBDaGFydCcsIGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLl9hZGRQYWNrYWdlcyhqc29uLnBhY2thZ2VzKTsgLy8gVE9ETzogbW92ZSB0aGlzIGludG8gdGhlIHN0b3JlIG1ldGhvZD9cclxuXHJcbiAgICAgICAgcmV0dXJuIG5ldyB0aGlzLkNoYXJ0KGpzb24pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBzZWUgY3JlYXRlQ2hhcnRcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBqc29uXHJcbiAgICAgKi9cclxuICAgIGFkZE5ld0NoYXJ0KGpzb24pIHsgLy9UT0RPOiByZW5hbWUgdG8gc3RvcmVOZXdDaGFydChqc29uKSA/XHJcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZUNoYXJ0KGpzb24pKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhIG5ldyBEYXNoYm9hcmQgd2l0aCBhIGdpdmVuIGxhYmVsLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIERhc2hib2FyZCBjbGFzcy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGpzb25cclxuICAgICAqIEByZXR1cm4ge0Rhc2hib2FyZH1cclxuICAgICAqL1xyXG4gICAgY3JlYXRlRGFzaGJvYXJkKGpzb24pIHtcclxuICAgICAgICBjb25zb2xlLmxvZygnQ3JlYXRpbmcgRGFzaGJvYXJkJywganNvbik7XHJcblxyXG4gICAgICAgIHRoaXMuX2FkZFBhY2thZ2VzKGpzb24ucGFja2FnZXMpO1xyXG5cclxuICAgICAgICByZXR1cm4gbmV3IHRoaXMuRGFzaGJvYXJkKGpzb24pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBEYXNoYm9hcmQgZnJvbSBhIEpTT04gcGF5bG9hZC5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgSlNPTiBwYXlsb2FkIGNvbWVzIGZyb20gdGhlIFBIUCBEYXNoYm9hcmQgY2xhc3MuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHNlZSBjcmVhdGVEYXNoYm9hcmRcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7RGFzaGJvYXJkfVxyXG4gICAgICovXHJcbiAgICBhZGROZXdEYXNoYm9hcmQoanNvbikgeyAvL1RPRE86IHJlbmFtZSB0byBzdG9yZU5ld0Rhc2hib2FyZChqc29uKSA/XHJcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZURhc2hib2FyZChqc29uKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSdW5zIHRoZSBMYXZhLmpzIG1vZHVsZVxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgcnVuKCkge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gUnVubmluZy4uLicpO1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gTG9hZGluZyBvcHRpb25zOicsIHRoaXMub3B0aW9ucyk7XHJcblxyXG4gICAgICAgIHRoaXMuX2F0dGFjaFJlZHJhd0hhbmRsZXIoKTtcclxuXHJcbiAgICAgICAgdGhpcy5fbG9hZEdvb2dsZSgpLnRoZW4oKCkgPT4ge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEdvb2dsZSBpcyByZWFkeS4nKTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMudmlzdWFsaXphdGlvbiA9IGdvb2dsZS52aXN1YWxpemF0aW9uO1xyXG5cclxuICAgICAgICAgICAgX2ZvckluKHRoaXMuX3JlbmRlcmFibGVzLCByZW5kZXJhYmxlID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gUmVuZGVyaW5nICR7cmVuZGVyYWJsZS51dWlkfWApO1xyXG5cclxuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUucmVuZGVyKCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBGaXJpbmcgXCJyZWFkeVwiIGV2ZW50LicpO1xyXG4gICAgICAgICAgICB0aGlzLmVtaXQoJ3JlYWR5Jyk7XHJcblxyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEV4ZWN1dGluZyBsYXZhLnJlYWR5KGNhbGxiYWNrKScpO1xyXG4gICAgICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBTdG9yZXMgYSByZW5kZXJhYmxlIGxhdmEgb2JqZWN0IHdpdGhpbiB0aGUgbW9kdWxlLlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7UmVuZGVyYWJsZX0gcmVuZGVyYWJsZVxyXG4gICAgICovXHJcbiAgICBzdG9yZShyZW5kZXJhYmxlKSB7XHJcbiAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBTdG9yaW5nICR7cmVuZGVyYWJsZS51dWlkfWApO1xyXG5cclxuICAgICAgICB0aGlzLl9yZW5kZXJhYmxlc1tyZW5kZXJhYmxlLmxhYmVsXSA9IHJlbmRlcmFibGU7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSZXR1cm5zIHRoZSBMYXZhQ2hhcnQgamF2YXNjcmlwdCBvYmplY3RzXHJcbiAgICAgKlxyXG4gICAgICpcclxuICAgICAqIFRoZSBMYXZhQ2hhcnQgb2JqZWN0IGhvbGRzIGFsbCB0aGUgdXNlciBkZWZpbmVkIHByb3BlcnRpZXMgc3VjaCBhcyBkYXRhLCBvcHRpb25zLCBmb3JtYXRzLFxyXG4gICAgICogdGhlIEdvb2dsZUNoYXJ0IG9iamVjdCwgYW5kIHJlbGF0aXZlIG1ldGhvZHMgZm9yIGludGVybmFsIHVzZS5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgR29vZ2xlQ2hhcnQgb2JqZWN0IGlzIGF2YWlsYWJsZSBhcyBcIi5jaGFydFwiIGZyb20gdGhlIHJldHVybmVkIExhdmFDaGFydC5cclxuICAgICAqIEl0IGNhbiBiZSB1c2VkIHRvIGFjY2VzcyBhbnkgb2YgdGhlIGF2YWlsYWJsZSBtZXRob2RzIHN1Y2ggYXNcclxuICAgICAqIGdldEltYWdlVVJJKCkgb3IgZ2V0Q2hhcnRMYXlvdXRJbnRlcmZhY2UoKS5cclxuICAgICAqIFNlZSBodHRwczovL2dvb2dsZS1kZXZlbG9wZXJzLmFwcHNwb3QuY29tL2NoYXJ0L2ludGVyYWN0aXZlL2RvY3MvZ2FsbGVyeS9saW5lY2hhcnQjbWV0aG9kc1xyXG4gICAgICogZm9yIHNvbWUgZXhhbXBsZXMgcmVsYXRpdmUgdG8gTGluZUNoYXJ0cy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0gIHtzdHJpbmd9ICAgbGFiZWxcclxuICAgICAqIEBwYXJhbSAge0Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICogQHRocm93cyBJbnZhbGlkTGFiZWxcclxuICAgICAqIEB0aHJvd3MgSW52YWxpZENhbGxiYWNrXHJcbiAgICAgKiBAdGhyb3dzIFJlbmRlcmFibGVOb3RGb3VuZFxyXG4gICAgICovXHJcbiAgICBnZXQobGFiZWwsIGNhbGxiYWNrKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGxldCByZW5kZXJhYmxlID0gdGhpcy5fcmVuZGVyYWJsZXNbbGFiZWxdO1xyXG5cclxuICAgICAgICBpZiAoISByZW5kZXJhYmxlKSB7XHJcbiAgICAgICAgICAgIHRocm93IG5ldyBSZW5kZXJhYmxlTm90Rm91bmQobGFiZWwpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgY2FsbGJhY2socmVuZGVyYWJsZSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBc3NpZ25zIGEgY2FsbGJhY2sgZm9yIHdoZW4gdGhlIGNoYXJ0cyBhcmUgcmVhZHkgdG8gYmUgaW50ZXJhY3RlZCB3aXRoLlxyXG4gICAgICpcclxuICAgICAqIFRoaXMgaXMgdXNlZCB0byB3cmFwIGNhbGxzIHRvIGxhdmEubG9hZERhdGEoKSBvciBsYXZhLmxvYWRPcHRpb25zKClcclxuICAgICAqIHRvIHByb3RlY3QgYWdhaW5zdCBhY2Nlc3NpbmcgY2hhcnRzIHRoYXQgYXJlbid0IGxvYWRlZCB5ZXRcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICovXHJcbiAgICByZWFkeShjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrID0gY2FsbGJhY2s7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgZGF0YSBpbnRvIHRoZSBjaGFydCBhbmQgcmVkcmF3cy5cclxuICAgICAqXHJcbiAgICAgKlxyXG4gICAgICogVXNlZCB3aXRoIGFuIEFKQVggY2FsbCB0byBhIFBIUCBtZXRob2QgcmV0dXJuaW5nIERhdGFUYWJsZS0+dG9Kc29uKCksXHJcbiAgICAgKiBhIGNoYXJ0IGNhbiBiZSBkeW5hbWljYWxseSB1cGRhdGUgaW4gcGFnZSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWREYXRhKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldERhdGEoanNvbik7XHJcblxyXG4gICAgICAgICAgICBpZiAodHlwZW9mIGpzb24uZm9ybWF0cyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICAgICAgICAgIGNoYXJ0LmFwcGx5Rm9ybWF0cyhqc29uLmZvcm1hdHMpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBjaGFydC5kcmF3KCk7XHJcblxyXG4gICAgICAgICAgICBjYWxsYmFjayhjaGFydCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgb3B0aW9ucyBpbnRvIGEgY2hhcnQgYW5kIHJlZHJhd3MuXHJcbiAgICAgKlxyXG4gICAgICpcclxuICAgICAqIFVzZWQgd2l0aCBhbiBBSkFYIGNhbGwsIG9yIGphdmFzY3JpcHQgZXZlbnRzLCB0byBsb2FkIGEgbmV3IGFycmF5IG9mIG9wdGlvbnMgaW50byBhIGNoYXJ0LlxyXG4gICAgICogVGhpcyBjYW4gYmUgdXNlZCB0byB1cGRhdGUgYSBjaGFydCBkeW5hbWljYWxseSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWRPcHRpb25zKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gY2FsbGJhY2sgfHwgbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldE9wdGlvbnMoanNvbik7XHJcbiAgICAgICAgICAgIGNoYXJ0LmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGNhbGxiYWNrKGNoYXJ0KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJlZHJhd3MgYWxsIG9mIHRoZSByZWdpc3RlcmVkIGNoYXJ0cyBvbiBzY3JlZW4uXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBtZXRob2QgaXMgYXR0YWNoZWQgdG8gdGhlIHdpbmRvdyByZXNpemUgZXZlbnQgd2l0aCBkZWJvdW5jaW5nXHJcbiAgICAgKiB0byBtYWtlIHRoZSBjaGFydHMgcmVzcG9uc2l2ZSB0byB0aGUgYnJvd3NlciByZXNpemluZy5cclxuICAgICAqL1xyXG4gICAgcmVkcmF3QWxsKCkge1xyXG4gICAgICAgIGlmICh0aGlzLl9yZW5kZXJhYmxlcy5sZW5ndGggPT09IDApIHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBOb3RoaW5nIHRvIHJlZHJhdy5gKTtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhgW2xhdmEuanNdIFJlZHJhd2luZyAke3RoaXMuX3JlbmRlcmFibGVzLmxlbmd0aH0gcmVuZGVyYWJsZXMuYCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBmb3IgKGxldCByZW5kZXJhYmxlIG9mIHRoaXMuX3JlbmRlcmFibGVzKSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gUmVkcmF3aW5nICR7cmVuZGVyYWJsZS51dWlkfWApO1xyXG5cclxuICAgICAgICAgICAgbGV0IHJlZHJhdyA9IHJlbmRlcmFibGUuZHJhdy5iaW5kKHJlbmRlcmFibGUpO1xyXG5cclxuICAgICAgICAgICAgcmVkcmF3KCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIEFsaWFzaW5nIGdvb2dsZS52aXN1YWxpemF0aW9uLmFycmF5VG9EYXRhVGFibGUgdG8gbGF2YS5hcnJheVRvRGF0YVRhYmxlXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtIHtBcnJheX0gYXJyXHJcbiAgICAgKiBAcmV0dXJuIHtnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGV9XHJcbiAgICAgKi9cclxuICAgIGFycmF5VG9EYXRhVGFibGUoYXJyKSB7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMudmlzdWFsaXphdGlvbi5hcnJheVRvRGF0YVRhYmxlKGFycik7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBZGRzIHRvIHRoZSBsaXN0IG9mIHBhY2thZ2VzIHRoYXQgR29vZ2xlIG5lZWRzIHRvIGxvYWQuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqIEBwYXJhbSB7QXJyYXl9IHBhY2thZ2VzXHJcbiAgICAgKiBAcmV0dXJuIHtBcnJheX1cclxuICAgICAqL1xyXG4gICAgX2FkZFBhY2thZ2VzKHBhY2thZ2VzKSB7XHJcbiAgICAgICAgdGhpcy5fcGFja2FnZXMgPSB0aGlzLl9wYWNrYWdlcy5jb25jYXQocGFja2FnZXMpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQXR0YWNoIGEgbGlzdGVuZXIgdG8gdGhlIHdpbmRvdyByZXNpemUgZXZlbnQgZm9yIHJlZHJhd2luZyB0aGUgY2hhcnRzLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKi9cclxuICAgIF9hdHRhY2hSZWRyYXdIYW5kbGVyKCkge1xyXG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMucmVzcG9uc2l2ZSA9PT0gdHJ1ZSkge1xyXG4gICAgICAgICAgICBsZXQgZGVib3VuY2VkID0gbnVsbDtcclxuXHJcbiAgICAgICAgICAgIGFkZEV2ZW50KHdpbmRvdywgJ3Jlc2l6ZScsICgpID0+IHtcclxuICAgICAgICAgICAgICAgIC8vIGxldCByZWRyYXcgPSB0aGlzLnJlZHJhd0FsbCgpLmJpbmQodGhpcyk7XHJcblxyXG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KGRlYm91bmNlZCk7XHJcblxyXG4gICAgICAgICAgICAgICAgZGVib3VuY2VkID0gc2V0VGltZW91dCgoKSA9PiB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBXaW5kb3cgcmUtc2l6ZWQsIHJlZHJhd2luZy4uLicpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAvLyByZWRyYXcoKTtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLnJlZHJhd0FsbCgpXHJcbiAgICAgICAgICAgICAgICB9LCB0aGlzLm9wdGlvbnMuZGVib3VuY2VfdGltZW91dCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIExvYWQgdGhlIEdvb2dsZSBTdGF0aWMgTG9hZGVyIGFuZCByZXNvbHZlIHRoZSBwcm9taXNlIHdoZW4gcmVhZHkuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2xvYWRHb29nbGUoKSB7XHJcbiAgICAgICAgY29uc3QgJGxhdmEgPSB0aGlzO1xyXG5cclxuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gUmVzb2x2aW5nIEdvb2dsZS4uLicpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuX2dvb2dsZUlzTG9hZGVkKCkpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gU3RhdGljIGxvYWRlciBmb3VuZCwgaW5pdGlhbGl6aW5nIHdpbmRvdy5nb29nbGUnKTtcclxuXHJcbiAgICAgICAgICAgICAgICAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFN0YXRpYyBsb2FkZXIgbm90IGZvdW5kLCBhcHBlbmRpbmcgdG8gaGVhZCcpO1xyXG5cclxuICAgICAgICAgICAgICAgICRsYXZhLl9hZGRHb29nbGVTY3JpcHRUb0hlYWQocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgICAgICAvLyBUaGlzIHdpbGwgY2FsbCAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENoZWNrIGlmIEdvb2dsZSdzIFN0YXRpYyBMb2FkZXIgaXMgaW4gcGFnZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59XHJcbiAgICAgKi9cclxuICAgIF9nb29nbGVJc0xvYWRlZCgpIHtcclxuICAgICAgICBjb25zdCBzY3JpcHRzID0gZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ3NjcmlwdCcpO1xyXG5cclxuICAgICAgICBmb3IgKGxldCBzY3JpcHQgb2Ygc2NyaXB0cykge1xyXG4gICAgICAgICAgICBpZiAoc2NyaXB0LnNyYyA9PT0gdGhpcy5HT09HTEVfTE9BREVSX1VSTCkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSdW5zIHRoZSBHb29nbGUgY2hhcnQgbG9hZGVyIGFuZCByZXNvbHZlcyB0aGUgcHJvbWlzZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHBhcmFtIHtQcm9taXNlLnJlc29sdmV9IHJlc29sdmVcclxuICAgICAqL1xyXG4gICAgX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpIHtcclxuICAgICAgICBsZXQgY29uZmlnID0ge1xyXG4gICAgICAgICAgICBwYWNrYWdlczogdGhpcy5fcGFja2FnZXMsXHJcbiAgICAgICAgICAgIGxhbmd1YWdlOiB0aGlzLm9wdGlvbnMubG9jYWxlXHJcbiAgICAgICAgfTtcclxuXHJcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5tYXBzX2FwaV9rZXkgIT09ICcnKSB7XHJcbiAgICAgICAgICAgIGNvbmZpZy5tYXBzQXBpS2V5ID0gdGhpcy5vcHRpb25zLm1hcHNfYXBpX2tleTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gTG9hZGluZyBHb29nbGUgd2l0aCBjb25maWc6JywgY29uZmlnKTtcclxuXHJcbiAgICAgICAgZ29vZ2xlLmNoYXJ0cy5sb2FkKHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OLCBjb25maWcpO1xyXG5cclxuICAgICAgICBnb29nbGUuY2hhcnRzLnNldE9uTG9hZENhbGxiYWNrKHJlc29sdmUpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGEgbmV3IHNjcmlwdCB0YWcgZm9yIHRoZSBHb29nbGUgU3RhdGljIExvYWRlci5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHBhcmFtIHtQcm9taXNlLnJlc29sdmV9IHJlc29sdmVcclxuICAgICAqIEByZXR1cm5zIHtFbGVtZW50fVxyXG4gICAgICovXHJcbiAgICBfYWRkR29vZ2xlU2NyaXB0VG9IZWFkKHJlc29sdmUpIHtcclxuICAgICAgICBsZXQgJGxhdmEgPSB0aGlzO1xyXG4gICAgICAgIGxldCBzY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtcclxuXHJcbiAgICAgICAgc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcclxuICAgICAgICBzY3JpcHQuYXN5bmMgPSB0cnVlO1xyXG4gICAgICAgIHNjcmlwdC5zcmMgPSB0aGlzLkdPT0dMRV9MT0FERVJfVVJMO1xyXG4gICAgICAgIHNjcmlwdC5vbmxvYWQgPSBzY3JpcHQub25yZWFkeXN0YXRlY2hhbmdlID0gZnVuY3Rpb24gKGV2ZW50KSB7XHJcbiAgICAgICAgICAgIGV2ZW50ID0gZXZlbnQgfHwgd2luZG93LmV2ZW50O1xyXG5cclxuICAgICAgICAgICAgaWYgKGV2ZW50LnR5cGUgPT09ICdsb2FkJyB8fCAoL2xvYWRlZHxjb21wbGV0ZS8udGVzdCh0aGlzLnJlYWR5U3RhdGUpKSkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5vbmxvYWQgPSB0aGlzLm9ucmVhZHlzdGF0ZWNoYW5nZSA9IG51bGw7XHJcblxyXG4gICAgICAgICAgICAgICAgJGxhdmEuX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuXHJcbiAgICAgICAgZG9jdW1lbnQuaGVhZC5hcHBlbmRDaGlsZChzY3JpcHQpO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBPcHRpb25zIG1vZHVsZVxyXG4gKlxyXG4gKiBEZWZhdWx0IGNvbmZpZ3VyYXRpb24gb3B0aW9ucyBmb3IgdXNpbmcgTGF2YS5qcyBhcyBhIHN0YW5kYWxvbmUgbGlicmFyeS5cclxuICpcclxuICogQG1vZHVsZSAgICBsYXZhL09wdGlvbnNcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBAdHlwZSB7e2F1dG9fcnVuOiBib29sZWFuLCBsb2NhbGU6IHN0cmluZywgdGltZXpvbmU6IHN0cmluZywgZGF0ZXRpbWVfZm9ybWF0OiBzdHJpbmcsIG1hcHNfYXBpX2tleTogc3RyaW5nLCByZXNwb25zaXZlOiBib29sZWFuLCBkZWJvdW5jZV90aW1lb3V0OiBudW1iZXJ9fVxyXG4gKi9cclxuY29uc3QgZGVmYXVsdE9wdGlvbnMgPSB7XHJcbiAgICBcImF1dG9fcnVuXCIgICAgICAgIDogZmFsc2UsXHJcbiAgICBcImxvY2FsZVwiICAgICAgICAgIDogXCJlblwiLFxyXG4gICAgXCJ0aW1lem9uZVwiICAgICAgICA6IFwiQW1lcmljYS9Mb3NfQW5nZWxlc1wiLFxyXG4gICAgXCJkYXRldGltZV9mb3JtYXRcIiA6IFwiXCIsXHJcbiAgICBcIm1hcHNfYXBpX2tleVwiICAgIDogXCJcIixcclxuICAgIFwicmVzcG9uc2l2ZVwiICAgICAgOiB0cnVlLFxyXG4gICAgXCJkZWJvdW5jZV90aW1lb3V0XCI6IDI1MFxyXG59O1xyXG5cclxuZXhwb3J0IGRlZmF1bHQgZGVmYXVsdE9wdGlvbnM7XHJcbiIsIi8qKlxyXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgdHlwZSAgICAgIC0gVHlwZSBvZiBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgR29vZ2xlIGNoYXJ0IHBhY2thZ2UgdG8gbG9hZC5cclxuICogQHByb3BlcnR5IHtib29sZWFufSAgcG5nT3V0cHV0IC0gU2hvdWxkIHRoZSBjaGFydCBiZSBkaXNwbGF5ZWQgYXMgYSBQTkcuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtBcnJheX0gICAgZm9ybWF0cyAgIC0gRm9ybWF0dGVycyB0byBhcHBseSB0byB0aGUgY2hhcnQgZGF0YS5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgcHJvbWlzZXMgIC0gUHJvbWlzZXMgdXNlZCBpbiB0aGUgcmVuZGVyaW5nIGNoYWluLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSBpbml0ICAgICAgLSBJbml0aWFsaXplcyB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IGNvbmZpZ3VyZSAtIENvbmZpZ3VyZXMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gQ3JlYXRlcyBpZGVudGlmaWNhdGlvbiBzdHJpbmcgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgX2Vycm9ycyAgIC0gQ29sbGVjdGlvbiBvZiBlcnJvcnMgdG8gYmUgdGhyb3duLlxyXG4gKi9cclxuaW1wb3J0IHtnZXRUeXBlfSBmcm9tIFwiLi9VdGlsc1wiXHJcbmltcG9ydCB7RWxlbWVudElkTm90Rm91bmR9IGZyb20gXCIuL0Vycm9yc1wiO1xyXG5pbXBvcnQgVmlzdWFsaXphdGlvblByb3BzIGZyb20gJy4vVmlzdWFsaXphdGlvblByb3BzJztcclxuXHJcbi8qKlxyXG4gKiBDaGFydCBtb2R1bGVcclxuICpcclxuICogQGNsYXNzICAgICBDaGFydFxyXG4gKiBAbW9kdWxlICAgIGxhdmEvQ2hhcnRcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVuZGVyYWJsZVxyXG57XHJcbiAgICAvKipcclxuICAgICAqIENoYXJ0IENsYXNzXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBpcyB0aGUgamF2YXNjcmlwdCB2ZXJzaW9uIG9mIGEgbGF2YWNoYXJ0IHdpdGggbWV0aG9kcyBmb3IgaW50ZXJhY3Rpbmcgd2l0aFxyXG4gICAgICogdGhlIGdvb2dsZSBjaGFydCBhbmQgdGhlIFBIUCBsYXZhY2hhcnQgb3V0cHV0LlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBqc29uXHJcbiAgICAgKiBAY29uc3RydWN0b3JcclxuICAgICAqL1xyXG4gICAgY29uc3RydWN0b3IoanNvbikge1xyXG4gICAgICAgIHRoaXMuZ2NoYXJ0ICAgID0gbnVsbDtcclxuICAgICAgICB0aGlzLnR5cGUgICAgICA9IGpzb24udHlwZTtcclxuICAgICAgICB0aGlzLmxhYmVsICAgICA9IGpzb24ubGFiZWw7XHJcbiAgICAgICAgdGhpcy5vcHRpb25zICAgPSBqc29uLm9wdGlvbnM7XHJcbiAgICAgICAgdGhpcy5lbGVtZW50SWQgPSBqc29uLmVsZW1lbnRJZDtcclxuXHJcbiAgICAgICAgdGhpcy5lbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQodGhpcy5lbGVtZW50SWQpO1xyXG5cclxuICAgICAgICBpZiAoISB0aGlzLmVsZW1lbnQpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEVsZW1lbnRJZE5vdEZvdW5kKHRoaXMuZWxlbWVudElkKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRoaXMuX3ZpelByb3BzID0gbmV3IFZpc3VhbGl6YXRpb25Qcm9wcyh0aGlzLnR5cGUpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogVGhlIGdvb2dsZS52aXN1YWxpemF0aW9uIGNsYXNzIG5lZWRlZCBmb3IgcmVuZGVyaW5nLlxyXG4gICAgICpcclxuICAgICAqIEByZXR1cm4ge3N0cmluZ31cclxuICAgICAqL1xyXG4gICAgZ2V0IGNsYXNzKClcclxuICAgIHtcclxuICAgICAgICByZXR1cm4gdGhpcy5fdml6UHJvcHMuY2xhc3NcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgQ2hhcnQuXHJcbiAgICAgKlxyXG4gICAgICogQHJldHVybiB7c3RyaW5nfVxyXG4gICAgICovXHJcbiAgICBnZXQgdXVpZCgpIHtcclxuICAgICAgICByZXR1cm4gdGhpcy50eXBlKyc6OicrdGhpcy5sYWJlbDtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIERyYXdzIHRoZSBjaGFydCB3aXRoIHRoZSBwcmVzZXQgZGF0YSBhbmQgb3B0aW9ucy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKi9cclxuICAgIGRyYXcoKSB7XHJcbiAgICAgICAgdGhpcy5nY2hhcnQuZHJhdyh0aGlzLmRhdGEsIHRoaXMub3B0aW9ucyk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBTZXRzIHRoZSBkYXRhIGZvciB0aGUgY2hhcnQgYnkgY3JlYXRpbmcgYSBuZXcgRGF0YVRhYmxlXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQGV4dGVybmFsIFwiZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlXCJcclxuICAgICAqIEBzZWUgICB7QGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXJzLmdvb2dsZS5jb20vY2hhcnQvaW50ZXJhY3RpdmUvZG9jcy9yZWZlcmVuY2UjRGF0YVRhYmxlfERhdGFUYWJsZSBDbGFzc31cclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBwYXlsb2FkIEpzb24gcmVwcmVzZW50YXRpb24gb2YgYSBEYXRhVGFibGVcclxuICAgICAqL1xyXG4gICAgc2V0RGF0YShwYXlsb2FkKSB7XHJcbiAgICAgICAgLy8gSWYgdGhlIHBheWxvYWQgaXMgZnJvbSB0aGUgcGhwIGNsYXNzIEpvaW5lZERhdGFUYWJsZS0+dG9Kc29uKCksIHRoZW4gY3JlYXRlXHJcbiAgICAgICAgLy8gdHdvIG5ldyBEYXRhVGFibGVzIGFuZCBqb2luIHRoZW0gd2l0aCB0aGUgZGVmaW5lZCBvcHRpb25zLlxyXG4gICAgICAgIGlmIChnZXRUeXBlKHBheWxvYWQuZGF0YSkgPT09ICdBcnJheScpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gZ29vZ2xlLnZpc3VhbGl6YXRpb24uZGF0YS5qb2luKFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMF0pLFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMV0pLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5rZXlzLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5qb2luTWV0aG9kLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zXHJcbiAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvLyBTaW5jZSBHb29nbGUgY29tcGlsZXMgdGhlaXIgY2xhc3Nlcywgd2UgY2FuJ3QgdXNlIGluc3RhbmNlb2YgdG8gY2hlY2sgc2luY2VcclxuICAgICAgICAvLyBpdCBpcyBubyBsb25nZXIgY2FsbGVkIGEgXCJEYXRhVGFibGVcIiAoaXQncyBcImd2anNfUFwiIGJ1dCB0aGF0IGNvdWxkIGNoYW5nZS4uLilcclxuICAgICAgICBpZiAoZ2V0VHlwZShwYXlsb2FkLmdldFRhYmxlUHJvcGVydGllcykgPT09ICdGdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gcGF5bG9hZDtcclxuXHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIElmIGFuIEFycmF5IGlzIHJlY2VpdmVkLCB0aGVuIGF0dGVtcHQgdG8gdXNlIHBhcnNlIHdpdGggYXJyYXlUb0RhdGFUYWJsZS5cclxuICAgICAgICBpZiAoZ2V0VHlwZShwYXlsb2FkKSA9PT0gJ0FycmF5Jykge1xyXG4gICAgICAgICAgICB0aGlzLmRhdGEgPSBnb29nbGUudmlzdWFsaXphdGlvbi5hcnJheVRvRGF0YVRhYmxlKHBheWxvYWQpO1xyXG5cclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLy8gSWYgYSBwaHAgRGF0YVRhYmxlLT50b0pzb24oKSBwYXlsb2FkIGlzIHJlY2VpdmVkLCB3aXRoIGZvcm1hdHRlZCBjb2x1bW5zLFxyXG4gICAgICAgIC8vIHRoZW4gcGF5bG9hZC5kYXRhIHdpbGwgYmUgZGVmaW5lZCwgYW5kIHVzZWQgYXMgdGhlIERhdGFUYWJsZVxyXG4gICAgICAgIGlmIChnZXRUeXBlKHBheWxvYWQuZGF0YSkgPT09ICdPYmplY3QnKSB7XHJcbiAgICAgICAgICAgIHBheWxvYWQgPSBwYXlsb2FkLmRhdGE7XHJcblxyXG4gICAgICAgICAgICAvLyBUT0RPOiBoYW5kbGUgZm9ybWF0cyBiZXR0ZXIuLi5cclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIElmIHdlIHJlYWNoIGhlcmUsIHRoZW4gaXQgbXVzdCBiZSBzdGFuZGFyZCBKU09OIGZvciBjcmVhdGluZyBhIERhdGFUYWJsZS5cclxuICAgICAgICB0aGlzLmRhdGEgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlKHBheWxvYWQpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU2V0cyB0aGUgb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zXHJcbiAgICAgKi9cclxuICAgIHNldE9wdGlvbnMob3B0aW9ucykge1xyXG4gICAgICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoganNoaW50IHVuZGVmOiB0cnVlLCB1bnVzZWQ6IHRydWUgKi9cclxuLyogZ2xvYmFscyBkb2N1bWVudCAqL1xyXG5cclxuLyoqXHJcbiAqIEZ1bmN0aW9uIHRoYXQgZG9lcyBub3RoaW5nLlxyXG4gKlxyXG4gKiBAcmV0dXJuIHt1bmRlZmluZWR9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gbm9vcCgpIHtcclxuICAgIHJldHVybiB1bmRlZmluZWQ7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZXR1cm4gdGhlIHR5cGUgb2Ygb2JqZWN0LlxyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gb2JqZWN0XHJcbiAqIEByZXR1cm4ge21peGVkfVxyXG4gKi9cclxuZXhwb3J0IGZ1bmN0aW9uIGdldFR5cGUob2JqZWN0KSB7XHJcbiAgICBsZXQgdHlwZSA9IE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChvYmplY3QpO1xyXG5cclxuICAgIHJldHVybiB0eXBlLnJlcGxhY2UoJ1tvYmplY3QgJywnJykucmVwbGFjZSgnXScsJycpO1xyXG59XHJcblxyXG4vKipcclxuICogU2ltcGxlIFByb21pc2UgZm9yIHRoZSBET00gdG8gYmUgcmVhZHkuXHJcbiAqXHJcbiAqIEByZXR1cm4ge1Byb21pc2V9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gZG9tTG9hZGVkKCkge1xyXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xyXG4gICAgICAgIGlmIChkb2N1bWVudC5yZWFkeVN0YXRlID09PSAnaW50ZXJhY3RpdmUnIHx8IGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScpIHtcclxuICAgICAgICAgICAgcmVzb2x2ZSgpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCByZXNvbHZlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIE1ldGhvZCBmb3IgYXR0YWNoaW5nIGV2ZW50cyB0byBvYmplY3RzLlxyXG4gKlxyXG4gKiBDcmVkaXQgdG8gQWxleCBWLlxyXG4gKlxyXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzMyNzkzNC9hbGV4LXZcclxuICogQGxpbmsgaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzE1MDEzOVxyXG4gKiBAcGFyYW0ge29iamVjdH0gdGFyZ2V0XHJcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlXHJcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAqIEBwYXJhbSB7Ym9vbH0gZXZlbnRSZXR1cm5cclxuICovXHJcbmV4cG9ydCBmdW5jdGlvbiBhZGRFdmVudCh0YXJnZXQsIHR5cGUsIGNhbGxiYWNrLCBldmVudFJldHVybilcclxue1xyXG4gICAgaWYgKHRhcmdldCA9PT0gbnVsbCB8fCB0eXBlb2YgdGFyZ2V0ID09PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIHJldHVybjtcclxuICAgIH1cclxuXHJcbiAgICBpZiAodGFyZ2V0LmFkZEV2ZW50TGlzdGVuZXIpIHtcclxuICAgICAgICB0YXJnZXQuYWRkRXZlbnRMaXN0ZW5lcih0eXBlLCBjYWxsYmFjaywgISFldmVudFJldHVybik7XHJcbiAgICB9XHJcbiAgICBlbHNlIGlmKHRhcmdldC5hdHRhY2hFdmVudCkge1xyXG4gICAgICAgIHRhcmdldC5hdHRhY2hFdmVudChcIm9uXCIgKyB0eXBlLCBjYWxsYmFjayk7XHJcbiAgICB9XHJcbiAgICBlbHNlIHtcclxuICAgICAgICB0YXJnZXRbXCJvblwiICsgdHlwZV0gPSBjYWxsYmFjaztcclxuICAgIH1cclxufVxyXG4iLCIvKipcbiAqIFZpc3VhbGl6YXRpb25Qcm9wcyBjbGFzc1xuICpcbiAqIFRoaXMgbW9kdWxlIHByb3ZpZGVzIHRoZSBuZWVkZWQgcHJvcGVydGllcyBmb3IgcmVuZGVyaW5nIGNoYXJ0cyByZXRyaWV2ZWRcbiAqIGJ5IHRoZSBjaGFydCB0eXBlLlxuICpcbiAqIEBjbGFzcyAgICAgVmlzdWFsaXphdGlvblByb3BzXG4gKiBAbW9kdWxlICAgIGxhdmEvVmlzdWFsaXphdGlvblByb3BzXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xuICogQGxpY2Vuc2UgICBNSVRcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVmlzdWFsaXphdGlvblByb3BzXG57XG4gICAgLyoqXG4gICAgICogQnVpbGQgYSBuZXcgVmlzdWFsaXphdGlvblByb3BzIGNsYXNzIGZvciB0aGUgZ2l2ZW4gY2hhcnQgdHlwZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBjaGFydFR5cGVcbiAgICAgKi9cbiAgICBjb25zdHJ1Y3RvcihjaGFydFR5cGUpIHtcbiAgICAgICAgdGhpcy5jaGFydFR5cGUgPSBjaGFydFR5cGU7XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIE1hcCBvZiBjaGFydCB0eXBlcyB0byB0aGVpciB2aXN1YWxpemF0aW9uIHBhY2thZ2UuXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLkNIQVJUX1RZUEVfUEFDS0FHRV9NQVAgPSB7XG4gICAgICAgICAgICBBbm5vdGF0aW9uQ2hhcnQgOiAnYW5ub3RhdGlvbmNoYXJ0JyxcbiAgICAgICAgICAgIEFyZWFDaGFydCAgICAgICA6ICdjb3JlY2hhcnQnLFxuICAgICAgICAgICAgQmFyQ2hhcnQgICAgICAgIDogJ2NvcmVjaGFydCcsXG4gICAgICAgICAgICBCdWJibGVDaGFydCAgICAgOiAnY29yZWNoYXJ0JyxcbiAgICAgICAgICAgIENhbGVuZGFyQ2hhcnQgICA6ICdjYWxlbmRhcicsXG4gICAgICAgICAgICBDYW5kbGVzdGlja0NoYXJ0OiAnY29yZWNoYXJ0JyxcbiAgICAgICAgICAgIENvbHVtbkNoYXJ0ICAgICA6ICdjb3JlY2hhcnQnLFxuICAgICAgICAgICAgQ29tYm9DaGFydCAgICAgIDogJ2NvcmVjaGFydCcsXG4gICAgICAgICAgICBEb251dENoYXJ0ICAgICAgOiAnY29yZWNoYXJ0JyxcbiAgICAgICAgICAgIEdhbnR0Q2hhcnQgICAgICA6ICdnYW50dCcsXG4gICAgICAgICAgICBHYXVnZUNoYXJ0ICAgICAgOiAnZ2F1Z2UnLFxuICAgICAgICAgICAgR2VvQ2hhcnQgICAgICAgIDogJ2dlb2NoYXJ0JyxcbiAgICAgICAgICAgIEhpc3RvZ3JhbUNoYXJ0ICA6ICdjb3JlY2hhcnQnLFxuICAgICAgICAgICAgTGluZUNoYXJ0ICAgICAgIDogJ2NvcmVjaGFydCcsXG4gICAgICAgICAgICBQaWVDaGFydCAgICAgICAgOiAnY29yZWNoYXJ0JyxcbiAgICAgICAgICAgIFNhbmtleUNoYXJ0ICAgICA6ICdzYW5rZXknLFxuICAgICAgICAgICAgU2NhdHRlckNoYXJ0ICAgIDogJ2NvcmVjaGFydCcsXG4gICAgICAgICAgICBTdGVwcGVkQXJlYUNoYXJ0OiAnY29yZWNoYXJ0JyxcbiAgICAgICAgICAgIFRhYmxlQ2hhcnQgICAgICA6ICd0YWJsZScsXG4gICAgICAgICAgICBUaW1lbGluZUNoYXJ0ICAgOiAndGltZWxpbmUnLFxuICAgICAgICAgICAgVHJlZU1hcENoYXJ0ICAgIDogJ3RyZWVtYXAnLFxuICAgICAgICAgICAgV29yZFRyZWVDaGFydCAgIDogJ3dvcmR0cmVlJ1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBNYXAgb2YgY2hhcnQgdHlwZXMgdG8gdGhlaXIgdmlzdWFsaXphdGlvbiBjbGFzcyBuYW1lLlxuICAgICAgICAgKi9cbiAgICAgICAgdGhpcy5DSEFSVF9UWVBFX0NMQVNTX01BUCA9IHtcbiAgICAgICAgICAgIEFubm90YXRpb25DaGFydCA6ICdBbm5vdGF0aW9uQ2hhcnQnLFxuICAgICAgICAgICAgQXJlYUNoYXJ0ICAgICAgIDogJ0FyZWFDaGFydCcsXG4gICAgICAgICAgICBCYXJDaGFydCAgICAgICAgOiAnQmFyQ2hhcnQnLFxuICAgICAgICAgICAgQnViYmxlQ2hhcnQgICAgIDogJ0J1YmJsZUNoYXJ0JyxcbiAgICAgICAgICAgIENhbGVuZGFyQ2hhcnQgICA6ICdDYWxlbmRhcicsXG4gICAgICAgICAgICBDYW5kbGVzdGlja0NoYXJ0OiAnQ2FuZGxlc3RpY2tDaGFydCcsXG4gICAgICAgICAgICBDb2x1bW5DaGFydCAgICAgOiAnQ29sdW1uQ2hhcnQnLFxuICAgICAgICAgICAgQ29tYm9DaGFydCAgICAgIDogJ0NvbWJvQ2hhcnQnLFxuICAgICAgICAgICAgRG9udXRDaGFydCAgICAgIDogJ1BpZUNoYXJ0JyxcbiAgICAgICAgICAgIEdhbnR0Q2hhcnQgICAgICA6ICdHYW50dCcsXG4gICAgICAgICAgICBHYXVnZUNoYXJ0ICAgICAgOiAnR2F1Z2UnLFxuICAgICAgICAgICAgR2VvQ2hhcnQgICAgICAgIDogJ0dlb0NoYXJ0JyxcbiAgICAgICAgICAgIEhpc3RvZ3JhbUNoYXJ0ICA6ICdIaXN0b2dyYW0nLFxuICAgICAgICAgICAgTGluZUNoYXJ0ICAgICAgIDogJ0xpbmVDaGFydCcsXG4gICAgICAgICAgICBQaWVDaGFydCAgICAgICAgOiAnUGllQ2hhcnQnLFxuICAgICAgICAgICAgU2Fua2V5Q2hhcnQgICAgIDogJ1NhbmtleScsXG4gICAgICAgICAgICBTY2F0dGVyQ2hhcnQgICAgOiAnU2NhdHRlckNoYXJ0JyxcbiAgICAgICAgICAgIFN0ZXBwZWRBcmVhQ2hhcnQ6ICdTdGVwcGVkQXJlYUNoYXJ0JyxcbiAgICAgICAgICAgIFRhYmxlQ2hhcnQgICAgICA6ICdUYWJsZScsXG4gICAgICAgICAgICBUaW1lbGluZUNoYXJ0ICAgOiAnVGltZWxpbmUnLFxuICAgICAgICAgICAgVHJlZU1hcENoYXJ0ICAgIDogJ1RyZWVNYXAnLFxuICAgICAgICAgICAgV29yZFRyZWVDaGFydCAgIDogJ1dvcmRUcmVlJ1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBNYXAgb2YgY2hhcnQgdHlwZXMgdG8gdGhlaXIgdmVyc2lvbnMuXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLkNIQVJUX1RZUEVfVkVSU0lPTl9NQVAgPSB7XG4gICAgICAgICAgICBBbm5vdGF0aW9uQ2hhcnQgOiAxLFxuICAgICAgICAgICAgQXJlYUNoYXJ0ICAgICAgIDogMSxcbiAgICAgICAgICAgIEJhckNoYXJ0ICAgICAgICA6IDEsXG4gICAgICAgICAgICBCdWJibGVDaGFydCAgICAgOiAxLFxuICAgICAgICAgICAgQ2FsZW5kYXJDaGFydCAgIDogMS4xLFxuICAgICAgICAgICAgQ2FuZGxlc3RpY2tDaGFydDogMSxcbiAgICAgICAgICAgIENvbHVtbkNoYXJ0ICAgICA6IDEsXG4gICAgICAgICAgICBDb21ib0NoYXJ0ICAgICAgOiAxLFxuICAgICAgICAgICAgRG9udXRDaGFydCAgICAgIDogMSxcbiAgICAgICAgICAgIEdhbnR0Q2hhcnQgICAgICA6IDEsXG4gICAgICAgICAgICBHYXVnZUNoYXJ0ICAgICAgOiAxLFxuICAgICAgICAgICAgR2VvQ2hhcnQgICAgICAgIDogMSxcbiAgICAgICAgICAgIEhpc3RvZ3JhbUNoYXJ0ICA6IDEsXG4gICAgICAgICAgICBMaW5lQ2hhcnQgICAgICAgOiAxLFxuICAgICAgICAgICAgUGllQ2hhcnQgICAgICAgIDogMSxcbiAgICAgICAgICAgIFNhbmtleUNoYXJ0ICAgICA6IDEsXG4gICAgICAgICAgICBTY2F0dGVyQ2hhcnQgICAgOiAxLFxuICAgICAgICAgICAgU3RlcHBlZEFyZWFDaGFydDogMSxcbiAgICAgICAgICAgIFRhYmxlQ2hhcnQgICAgICA6IDEsXG4gICAgICAgICAgICBUaW1lbGluZUNoYXJ0ICAgOiAxLFxuICAgICAgICAgICAgVHJlZU1hcENoYXJ0ICAgIDogMSxcbiAgICAgICAgICAgIFdvcmRUcmVlQ2hhcnQgICA6IDFcbiAgICAgICAgfTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm4gdGhlIHZpc3VhbGl6YXRpb24gcGFja2FnZSBmb3IgdGhlIGNoYXJ0IHR5cGVcbiAgICAgKlxuICAgICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICAgKi9cbiAgICBnZXQgcGFja2FnZSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuQ0hBUlRfVFlQRV9QQUNLQUdFX01BUFt0aGlzLmNoYXJ0VHlwZV07XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmV0dXJuIHRoZSB2aXN1YWxpemF0aW9uIGNsYXNzIGZvciB0aGUgY2hhcnQgdHlwZVxuICAgICAqXG4gICAgICogQHJldHVybiB7c3RyaW5nfVxuICAgICAqL1xuICAgIGdldCBjbGFzcygpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuQ0hBUlRfVFlQRV9DTEFTU19NQVBbdGhpcy5jaGFydFR5cGVdO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFJldHVybiB0aGUgdmlzdWFsaXphdGlvbiB2ZXJzaW9uIGZvciB0aGUgY2hhcnQgdHlwZVxuICAgICAqXG4gICAgICogQHJldHVybiB7bnVtYmVyfVxuICAgICAqL1xuICAgIGdldCB2ZXJzaW9uKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5DSEFSVF9UWVBFX1ZFUlNJT05fTUFQW3RoaXMuY2hhcnRUeXBlXTtcbiAgICB9XG59XG4iXX0=
