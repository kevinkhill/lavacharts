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

var _Lava = require('./lava/Lava.es6');

var _Utils = require('./lava/Utils.es6');

/**
 * Assign the Lava.js module to the window and
 * let $lava be an alias to the module.
 */
var $lava = window.lava = new _Lava.LavaJs();

/**
 * Once the DOM has loaded...
 */
(0, _Utils.domLoaded)().then(function () {
    /**
     * Adding the resize event listener for redrawing charts if
     * the option responsive is set to true.
     */
    if ($lava.options.responsive === true) {
        var debounced = null;

        (0, _Utils.addEvent)(window, 'resize', function () {
            var redraw = $lava.redrawAll.bind($lava);

            clearTimeout(debounced);

            debounced = setTimeout(function () {
                console.log('[lava.js] Window re-sized, redrawing...');

                redraw();
            }, $lava.options.debounce_timeout);
        });
    }

    if ($lava.options.auto_run === true) {
        $lava.run();
    }
});

},{"./lava/Lava.es6":38,"./lava/Utils.es6":40}],35:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.Chart = undefined;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _forIn2 = require('lodash/forIn');

var _forIn3 = _interopRequireDefault(_forIn2);

var _Renderable2 = require('./Renderable.es6');

var _Utils = require('./Utils.es6');

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
var Chart = exports.Chart = function (_Renderable) {
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

},{"./Renderable.es6":39,"./Utils.es6":40,"lodash/forIn":21}],36:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.Dashboard = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _Renderable2 = require('./Renderable.es6');

var _Utils = require('./Utils.es6');

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
var Dashboard = exports.Dashboard = function (_Renderable) {
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

                    // let [controlWrapper, chartWrapper] = binding;

                    console.log('binding', binding);
                    console.log('controlWrapper', binding.controlWrappers);
                    console.log('chartWrapper', binding.chartWrappers);

                    this.gchart.bind(new google.visualization.ControlWrapper(binding.controlWrappers[0]), new google.visualization.ChartWrapper(binding.chartWrappers[0]));
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

},{"./Renderable.es6":39,"./Utils.es6":40}],37:[function(require,module,exports){
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
exports.LavaJs = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _forIn2 = require('lodash/forIn');

var _forIn3 = _interopRequireDefault(_forIn2);

var _events = require('events');

var _events2 = _interopRequireDefault(_events);

var _Chart = require('./Chart.es6');

var _Dashboard = require('./Dashboard.es6');

var _Utils = require('./Utils.es6');

var _Errors = require('./Errors.es6');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * lava.js module
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @module    lava/Lava
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @author    Kevin Hill <kevinkhill@gmail.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @copyright (c) 2017, KHill Designs
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @license   http://opensource.org/licenses/MIT MIT
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                */


/**
 * @property {string}             VERSION        Version of the module.
 * @property {Chart}              Chart          Chart class.
 * @property {Dashboard}          Dashboard      Dashboard class.
 * @property {object}             _errors
 * @property {string}             GOOGLE_LOADER_URL     Url to Google's static loader
 * @property {object}             options        Options for the module
 * @property {function}           _readyCallback
 * @property {Array.<string>}     _packages
 * @property {Array.<Renderable>} _renderables
 */
var LavaJs = exports.LavaJs = function (_EventEmitter) {
    _inherits(LavaJs, _EventEmitter);

    function LavaJs() {
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
        _this.Chart = _Chart.Chart;

        /**
         * Storing the Dashboard module within Lava.js
         *
         * @type {Dashboard}
         * @public
         */
        _this.Dashboard = _Dashboard.Dashboard;

        /**
         * JSON object of config items.
         *
         * @type {Object}
         * @public
         */
        _this.options = OPTIONS_JSON;

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
            console.log('JSON_PAYLOAD', json);

            this._addPackages(json.packages);

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
            console.log('JSON_PAYLOAD', json);

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
            var $lava = this;

            console.log('[lava.js] Running...');
            console.log('[lava.js] Loading options:', this.options);

            $lava._loadGoogle().then(function () {
                console.log('[lava.js] Google is ready.');

                (0, _forIn3.default)($lava._renderables, function (renderable) {
                    console.log('[lava.js] Rendering ' + renderable.uuid());

                    renderable.render();
                });

                console.log('[lava.js] Firing "ready" event.');
                $lava.emit('ready');

                console.log('[lava.js] Executing lava.ready(callback)');
                $lava._readyCallback();
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
         * Load the Google Static Loader and resolve the promise when ready.
         *
         * @private
         */

    }, {
        key: '_loadGoogle',
        value: function _loadGoogle() {
            var _this2 = this;

            var $lava = this;

            return new Promise(function (resolve) {
                console.log('[lava.js] Resolving Google...');

                if (_this2._googleIsLoaded()) {
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

},{"./Chart.es6":35,"./Dashboard.es6":36,"./Errors.es6":37,"./Utils.es6":40,"events":1,"lodash/forIn":21}],39:[function(require,module,exports){
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


var _Errors = require("./Errors.es6");

var _Utils = require("./Utils.es6");

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

},{"./Errors.es6":37,"./Utils.es6":40}],40:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJub2RlX21vZHVsZXMvZXZlbnRzL2V2ZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX1N5bWJvbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2FycmF5TGlrZUtleXMuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlRm9yLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZUdldFRhZy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc1R5cGVkQXJyYXkuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlS2V5c0luLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVRpbWVzLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVVuYXJ5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY2FzdEZ1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY3JlYXRlQmFzZUZvci5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2ZyZWVHbG9iYWwuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19nZXRSYXdUYWcuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19pc0luZGV4LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9faXNQcm90b3R5cGUuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19uYXRpdmVLZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19ub2RlVXRpbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX29iamVjdFRvU3RyaW5nLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fcm9vdC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvZm9ySW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lkZW50aXR5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheUxpa2UuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzQnVmZmVyLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0Z1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0xlbmd0aC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNPYmplY3QuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzT2JqZWN0TGlrZS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNUeXBlZEFycmF5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9rZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL3N0dWJGYWxzZS5qcyIsInNyYy9sYXZhLmVudHJ5LmVzNiIsInNyYy9sYXZhL0NoYXJ0LmVzNiIsInNyYy9sYXZhL0Rhc2hib2FyZC5lczYiLCJzcmMvbGF2YS9FcnJvcnMuZXM2Iiwic3JjL2xhdmEvTGF2YS5lczYiLCJzcmMvbGF2YS9SZW5kZXJhYmxlLmVzNiIsInNyYy9sYXZhL1V0aWxzLmVzNiJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM5U0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDTkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNoQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM1QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzVEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3BCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDZEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQ3pCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7O0FDSkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM5Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDVEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzFCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDL0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM3QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDM0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFD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bGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICB0aGlzLl9ldmVudHMgPSB0aGlzLl9ldmVudHMgfHwge307XG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbkV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uKG4pIHtcbiAgaWYgKCFpc051bWJlcihuKSB8fCBuIDwgMCB8fCBpc05hTihuKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ24gbXVzdCBiZSBhIHBvc2l0aXZlIG51bWJlcicpO1xuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIGVyLCBoYW5kbGVyLCBsZW4sIGFyZ3MsIGksIGxpc3RlbmVycztcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmICh0eXBlID09PSAnZXJyb3InKSB7XG4gICAgaWYgKCF0aGlzLl9ldmVudHMuZXJyb3IgfHxcbiAgICAgICAgKGlzT2JqZWN0KHRoaXMuX2V2ZW50cy5lcnJvcikgJiYgIXRoaXMuX2V2ZW50cy5lcnJvci5sZW5ndGgpKSB7XG4gICAgICBlciA9IGFyZ3VtZW50c1sxXTtcbiAgICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgICAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmNhdWdodCwgdW5zcGVjaWZpZWQgXCJlcnJvclwiIGV2ZW50LiAoJyArIGVyICsgJyknKTtcbiAgICAgICAgZXJyLmNvbnRleHQgPSBlcjtcbiAgICAgICAgdGhyb3cgZXJyO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIGhhbmRsZXIgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgaWYgKGlzVW5kZWZpbmVkKGhhbmRsZXIpKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAoaXNGdW5jdGlvbihoYW5kbGVyKSkge1xuICAgIHN3aXRjaCAoYXJndW1lbnRzLmxlbmd0aCkge1xuICAgICAgLy8gZmFzdCBjYXNlc1xuICAgICAgY2FzZSAxOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcyk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAyOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcywgYXJndW1lbnRzWzFdKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIDM6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzLCBhcmd1bWVudHNbMV0sIGFyZ3VtZW50c1syXSk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgLy8gc2xvd2VyXG4gICAgICBkZWZhdWx0OlxuICAgICAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICAgICAgaGFuZGxlci5hcHBseSh0aGlzLCBhcmdzKTtcbiAgICB9XG4gIH0gZWxzZSBpZiAoaXNPYmplY3QoaGFuZGxlcikpIHtcbiAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICBsaXN0ZW5lcnMgPSBoYW5kbGVyLnNsaWNlKCk7XG4gICAgbGVuID0gbGlzdGVuZXJzLmxlbmd0aDtcbiAgICBmb3IgKGkgPSAwOyBpIDwgbGVuOyBpKyspXG4gICAgICBsaXN0ZW5lcnNbaV0uYXBwbHkodGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgbTtcblxuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgdGhpcy5fZXZlbnRzID0ge307XG5cbiAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICBpZiAodGhpcy5fZXZlbnRzLm5ld0xpc3RlbmVyKVxuICAgIHRoaXMuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICBpc0Z1bmN0aW9uKGxpc3RlbmVyLmxpc3RlbmVyKSA/XG4gICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICBlbHNlIGlmIChpc09iamVjdCh0aGlzLl9ldmVudHNbdHlwZV0pKVxuICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0ucHVzaChsaXN0ZW5lcik7XG4gIGVsc2VcbiAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0gPSBbdGhpcy5fZXZlbnRzW3R5cGVdLCBsaXN0ZW5lcl07XG5cbiAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgaWYgKGlzT2JqZWN0KHRoaXMuX2V2ZW50c1t0eXBlXSkgJiYgIXRoaXMuX2V2ZW50c1t0eXBlXS53YXJuZWQpIHtcbiAgICBpZiAoIWlzVW5kZWZpbmVkKHRoaXMuX21heExpc3RlbmVycykpIHtcbiAgICAgIG0gPSB0aGlzLl9tYXhMaXN0ZW5lcnM7XG4gICAgfSBlbHNlIHtcbiAgICAgIG0gPSBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgICB9XG5cbiAgICBpZiAobSAmJiBtID4gMCAmJiB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoID4gbSkge1xuICAgICAgdGhpcy5fZXZlbnRzW3R5cGVdLndhcm5lZCA9IHRydWU7XG4gICAgICBjb25zb2xlLmVycm9yKCcobm9kZSkgd2FybmluZzogcG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSAnICtcbiAgICAgICAgICAgICAgICAgICAgJ2xlYWsgZGV0ZWN0ZWQuICVkIGxpc3RlbmVycyBhZGRlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICdVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byBpbmNyZWFzZSBsaW1pdC4nLFxuICAgICAgICAgICAgICAgICAgICB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoKTtcbiAgICAgIGlmICh0eXBlb2YgY29uc29sZS50cmFjZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAvLyBub3Qgc3VwcG9ydGVkIGluIElFIDEwXG4gICAgICAgIGNvbnNvbGUudHJhY2UoKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgdmFyIGZpcmVkID0gZmFsc2U7XG5cbiAgZnVuY3Rpb24gZygpIHtcbiAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGcpO1xuXG4gICAgaWYgKCFmaXJlZCkge1xuICAgICAgZmlyZWQgPSB0cnVlO1xuICAgICAgbGlzdGVuZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH1cblxuICBnLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHRoaXMub24odHlwZSwgZyk7XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG4vLyBlbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWZmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZFxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBsaXN0LCBwb3NpdGlvbiwgbGVuZ3RoLCBpO1xuXG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldHVybiB0aGlzO1xuXG4gIGxpc3QgPSB0aGlzLl9ldmVudHNbdHlwZV07XG4gIGxlbmd0aCA9IGxpc3QubGVuZ3RoO1xuICBwb3NpdGlvbiA9IC0xO1xuXG4gIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fFxuICAgICAgKGlzRnVuY3Rpb24obGlzdC5saXN0ZW5lcikgJiYgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICBpZiAodGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3RlbmVyKTtcblxuICB9IGVsc2UgaWYgKGlzT2JqZWN0KGxpc3QpKSB7XG4gICAgZm9yIChpID0gbGVuZ3RoOyBpLS0gPiAwOykge1xuICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8XG4gICAgICAgICAgKGxpc3RbaV0ubGlzdGVuZXIgJiYgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKSB7XG4gICAgICBsaXN0Lmxlbmd0aCA9IDA7XG4gICAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIH0gZWxzZSB7XG4gICAgICBsaXN0LnNwbGljZShwb3NpdGlvbiwgMSk7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0ZW5lcik7XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIga2V5LCBsaXN0ZW5lcnM7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgcmV0dXJuIHRoaXM7XG5cbiAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICBpZiAoIXRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcikge1xuICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKVxuICAgICAgdGhpcy5fZXZlbnRzID0ge307XG4gICAgZWxzZSBpZiAodGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgIGZvciAoa2V5IGluIHRoaXMuX2V2ZW50cykge1xuICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgIH1cbiAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGxpc3RlbmVycyA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICBpZiAoaXNGdW5jdGlvbihsaXN0ZW5lcnMpKSB7XG4gICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICB9IGVsc2UgaWYgKGxpc3RlbmVycykge1xuICAgIC8vIExJRk8gb3JkZXJcbiAgICB3aGlsZSAobGlzdGVuZXJzLmxlbmd0aClcbiAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2xpc3RlbmVycy5sZW5ndGggLSAxXSk7XG4gIH1cbiAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIgcmV0O1xuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldCA9IFtdO1xuICBlbHNlIGlmIChpc0Z1bmN0aW9uKHRoaXMuX2V2ZW50c1t0eXBlXSkpXG4gICAgcmV0ID0gW3RoaXMuX2V2ZW50c1t0eXBlXV07XG4gIGVsc2VcbiAgICByZXQgPSB0aGlzLl9ldmVudHNbdHlwZV0uc2xpY2UoKTtcbiAgcmV0dXJuIHJldDtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgaWYgKHRoaXMuX2V2ZW50cykge1xuICAgIHZhciBldmxpc3RlbmVyID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKGlzRnVuY3Rpb24oZXZsaXN0ZW5lcikpXG4gICAgICByZXR1cm4gMTtcbiAgICBlbHNlIGlmIChldmxpc3RlbmVyKVxuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICB9XG4gIHJldHVybiAwO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG59O1xuXG5mdW5jdGlvbiBpc0Z1bmN0aW9uKGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ2Z1bmN0aW9uJztcbn1cblxuZnVuY3Rpb24gaXNOdW1iZXIoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnbnVtYmVyJztcbn1cblxuZnVuY3Rpb24gaXNPYmplY3QoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnb2JqZWN0JyAmJiBhcmcgIT09IG51bGw7XG59XG5cbmZ1bmN0aW9uIGlzVW5kZWZpbmVkKGFyZykge1xuICByZXR1cm4gYXJnID09PSB2b2lkIDA7XG59XG4iLCJ2YXIgcm9vdCA9IHJlcXVpcmUoJy4vX3Jvb3QnKTtcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgU3ltYm9sID0gcm9vdC5TeW1ib2w7XG5cbm1vZHVsZS5leHBvcnRzID0gU3ltYm9sO1xuIiwidmFyIGJhc2VUaW1lcyA9IHJlcXVpcmUoJy4vX2Jhc2VUaW1lcycpLFxuICAgIGlzQXJndW1lbnRzID0gcmVxdWlyZSgnLi9pc0FyZ3VtZW50cycpLFxuICAgIGlzQXJyYXkgPSByZXF1aXJlKCcuL2lzQXJyYXknKSxcbiAgICBpc0J1ZmZlciA9IHJlcXVpcmUoJy4vaXNCdWZmZXInKSxcbiAgICBpc0luZGV4ID0gcmVxdWlyZSgnLi9faXNJbmRleCcpLFxuICAgIGlzVHlwZWRBcnJheSA9IHJlcXVpcmUoJy4vaXNUeXBlZEFycmF5Jyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKlxuICogQ3JlYXRlcyBhbiBhcnJheSBvZiB0aGUgZW51bWVyYWJsZSBwcm9wZXJ0eSBuYW1lcyBvZiB0aGUgYXJyYXktbGlrZSBgdmFsdWVgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBxdWVyeS5cbiAqIEBwYXJhbSB7Ym9vbGVhbn0gaW5oZXJpdGVkIFNwZWNpZnkgcmV0dXJuaW5nIGluaGVyaXRlZCBwcm9wZXJ0eSBuYW1lcy5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKi9cbmZ1bmN0aW9uIGFycmF5TGlrZUtleXModmFsdWUsIGluaGVyaXRlZCkge1xuICB2YXIgaXNBcnIgPSBpc0FycmF5KHZhbHVlKSxcbiAgICAgIGlzQXJnID0gIWlzQXJyICYmIGlzQXJndW1lbnRzKHZhbHVlKSxcbiAgICAgIGlzQnVmZiA9ICFpc0FyciAmJiAhaXNBcmcgJiYgaXNCdWZmZXIodmFsdWUpLFxuICAgICAgaXNUeXBlID0gIWlzQXJyICYmICFpc0FyZyAmJiAhaXNCdWZmICYmIGlzVHlwZWRBcnJheSh2YWx1ZSksXG4gICAgICBza2lwSW5kZXhlcyA9IGlzQXJyIHx8IGlzQXJnIHx8IGlzQnVmZiB8fCBpc1R5cGUsXG4gICAgICByZXN1bHQgPSBza2lwSW5kZXhlcyA/IGJhc2VUaW1lcyh2YWx1ZS5sZW5ndGgsIFN0cmluZykgOiBbXSxcbiAgICAgIGxlbmd0aCA9IHJlc3VsdC5sZW5ndGg7XG5cbiAgZm9yICh2YXIga2V5IGluIHZhbHVlKSB7XG4gICAgaWYgKChpbmhlcml0ZWQgfHwgaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwga2V5KSkgJiZcbiAgICAgICAgIShza2lwSW5kZXhlcyAmJiAoXG4gICAgICAgICAgIC8vIFNhZmFyaSA5IGhhcyBlbnVtZXJhYmxlIGBhcmd1bWVudHMubGVuZ3RoYCBpbiBzdHJpY3QgbW9kZS5cbiAgICAgICAgICAga2V5ID09ICdsZW5ndGgnIHx8XG4gICAgICAgICAgIC8vIE5vZGUuanMgMC4xMCBoYXMgZW51bWVyYWJsZSBub24taW5kZXggcHJvcGVydGllcyBvbiBidWZmZXJzLlxuICAgICAgICAgICAoaXNCdWZmICYmIChrZXkgPT0gJ29mZnNldCcgfHwga2V5ID09ICdwYXJlbnQnKSkgfHxcbiAgICAgICAgICAgLy8gUGhhbnRvbUpTIDIgaGFzIGVudW1lcmFibGUgbm9uLWluZGV4IHByb3BlcnRpZXMgb24gdHlwZWQgYXJyYXlzLlxuICAgICAgICAgICAoaXNUeXBlICYmIChrZXkgPT0gJ2J1ZmZlcicgfHwga2V5ID09ICdieXRlTGVuZ3RoJyB8fCBrZXkgPT0gJ2J5dGVPZmZzZXQnKSkgfHxcbiAgICAgICAgICAgLy8gU2tpcCBpbmRleCBwcm9wZXJ0aWVzLlxuICAgICAgICAgICBpc0luZGV4KGtleSwgbGVuZ3RoKVxuICAgICAgICApKSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBhcnJheUxpa2VLZXlzO1xuIiwidmFyIGNyZWF0ZUJhc2VGb3IgPSByZXF1aXJlKCcuL19jcmVhdGVCYXNlRm9yJyk7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYGJhc2VGb3JPd25gIHdoaWNoIGl0ZXJhdGVzIG92ZXIgYG9iamVjdGBcbiAqIHByb3BlcnRpZXMgcmV0dXJuZWQgYnkgYGtleXNGdW5jYCBhbmQgaW52b2tlcyBgaXRlcmF0ZWVgIGZvciBlYWNoIHByb3BlcnR5LlxuICogSXRlcmF0ZWUgZnVuY3Rpb25zIG1heSBleGl0IGl0ZXJhdGlvbiBlYXJseSBieSBleHBsaWNpdGx5IHJldHVybmluZyBgZmFsc2VgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gaXRlcmF0ZSBvdmVyLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IGtleXNGdW5jIFRoZSBmdW5jdGlvbiB0byBnZXQgdGhlIGtleXMgb2YgYG9iamVjdGAuXG4gKiBAcmV0dXJucyB7T2JqZWN0fSBSZXR1cm5zIGBvYmplY3RgLlxuICovXG52YXIgYmFzZUZvciA9IGNyZWF0ZUJhc2VGb3IoKTtcblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlRm9yO1xuIiwidmFyIFN5bWJvbCA9IHJlcXVpcmUoJy4vX1N5bWJvbCcpLFxuICAgIGdldFJhd1RhZyA9IHJlcXVpcmUoJy4vX2dldFJhd1RhZycpLFxuICAgIG9iamVjdFRvU3RyaW5nID0gcmVxdWlyZSgnLi9fb2JqZWN0VG9TdHJpbmcnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIG51bGxUYWcgPSAnW29iamVjdCBOdWxsXScsXG4gICAgdW5kZWZpbmVkVGFnID0gJ1tvYmplY3QgVW5kZWZpbmVkXSc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBnZXRUYWdgIHdpdGhvdXQgZmFsbGJhY2tzIGZvciBidWdneSBlbnZpcm9ubWVudHMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgYHRvU3RyaW5nVGFnYC5cbiAqL1xuZnVuY3Rpb24gYmFzZUdldFRhZyh2YWx1ZSkge1xuICBpZiAodmFsdWUgPT0gbnVsbCkge1xuICAgIHJldHVybiB2YWx1ZSA9PT0gdW5kZWZpbmVkID8gdW5kZWZpbmVkVGFnIDogbnVsbFRhZztcbiAgfVxuICByZXR1cm4gKHN5bVRvU3RyaW5nVGFnICYmIHN5bVRvU3RyaW5nVGFnIGluIE9iamVjdCh2YWx1ZSkpXG4gICAgPyBnZXRSYXdUYWcodmFsdWUpXG4gICAgOiBvYmplY3RUb1N0cmluZyh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUdldFRhZztcbiIsInZhciBiYXNlR2V0VGFnID0gcmVxdWlyZSgnLi9fYmFzZUdldFRhZycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhcmdzVGFnID0gJ1tvYmplY3QgQXJndW1lbnRzXSc7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNBcmd1bWVudHNgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGBhcmd1bWVudHNgIG9iamVjdCxcbiAqL1xuZnVuY3Rpb24gYmFzZUlzQXJndW1lbnRzKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGJhc2VHZXRUYWcodmFsdWUpID09IGFyZ3NUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUlzQXJndW1lbnRzO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyksXG4gICAgaXNPYmplY3RMaWtlID0gcmVxdWlyZSgnLi9pc09iamVjdExpa2UnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIGFyZ3NUYWcgPSAnW29iamVjdCBBcmd1bWVudHNdJyxcbiAgICBhcnJheVRhZyA9ICdbb2JqZWN0IEFycmF5XScsXG4gICAgYm9vbFRhZyA9ICdbb2JqZWN0IEJvb2xlYW5dJyxcbiAgICBkYXRlVGFnID0gJ1tvYmplY3QgRGF0ZV0nLFxuICAgIGVycm9yVGFnID0gJ1tvYmplY3QgRXJyb3JdJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBtYXBUYWcgPSAnW29iamVjdCBNYXBdJyxcbiAgICBudW1iZXJUYWcgPSAnW29iamVjdCBOdW1iZXJdJyxcbiAgICBvYmplY3RUYWcgPSAnW29iamVjdCBPYmplY3RdJyxcbiAgICByZWdleHBUYWcgPSAnW29iamVjdCBSZWdFeHBdJyxcbiAgICBzZXRUYWcgPSAnW29iamVjdCBTZXRdJyxcbiAgICBzdHJpbmdUYWcgPSAnW29iamVjdCBTdHJpbmddJyxcbiAgICB3ZWFrTWFwVGFnID0gJ1tvYmplY3QgV2Vha01hcF0nO1xuXG52YXIgYXJyYXlCdWZmZXJUYWcgPSAnW29iamVjdCBBcnJheUJ1ZmZlcl0nLFxuICAgIGRhdGFWaWV3VGFnID0gJ1tvYmplY3QgRGF0YVZpZXddJyxcbiAgICBmbG9hdDMyVGFnID0gJ1tvYmplY3QgRmxvYXQzMkFycmF5XScsXG4gICAgZmxvYXQ2NFRhZyA9ICdbb2JqZWN0IEZsb2F0NjRBcnJheV0nLFxuICAgIGludDhUYWcgPSAnW29iamVjdCBJbnQ4QXJyYXldJyxcbiAgICBpbnQxNlRhZyA9ICdbb2JqZWN0IEludDE2QXJyYXldJyxcbiAgICBpbnQzMlRhZyA9ICdbb2JqZWN0IEludDMyQXJyYXldJyxcbiAgICB1aW50OFRhZyA9ICdbb2JqZWN0IFVpbnQ4QXJyYXldJyxcbiAgICB1aW50OENsYW1wZWRUYWcgPSAnW29iamVjdCBVaW50OENsYW1wZWRBcnJheV0nLFxuICAgIHVpbnQxNlRhZyA9ICdbb2JqZWN0IFVpbnQxNkFycmF5XScsXG4gICAgdWludDMyVGFnID0gJ1tvYmplY3QgVWludDMyQXJyYXldJztcblxuLyoqIFVzZWQgdG8gaWRlbnRpZnkgYHRvU3RyaW5nVGFnYCB2YWx1ZXMgb2YgdHlwZWQgYXJyYXlzLiAqL1xudmFyIHR5cGVkQXJyYXlUYWdzID0ge307XG50eXBlZEFycmF5VGFnc1tmbG9hdDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Zsb2F0NjRUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDhUYWddID0gdHlwZWRBcnJheVRhZ3NbaW50MTZUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW3VpbnQ4VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50OENsYW1wZWRUYWddID0gdHlwZWRBcnJheVRhZ3NbdWludDE2VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50MzJUYWddID0gdHJ1ZTtcbnR5cGVkQXJyYXlUYWdzW2FyZ3NUYWddID0gdHlwZWRBcnJheVRhZ3NbYXJyYXlUYWddID1cbnR5cGVkQXJyYXlUYWdzW2FycmF5QnVmZmVyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Jvb2xUYWddID1cbnR5cGVkQXJyYXlUYWdzW2RhdGFWaWV3VGFnXSA9IHR5cGVkQXJyYXlUYWdzW2RhdGVUYWddID1cbnR5cGVkQXJyYXlUYWdzW2Vycm9yVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Z1bmNUYWddID1cbnR5cGVkQXJyYXlUYWdzW21hcFRhZ10gPSB0eXBlZEFycmF5VGFnc1tudW1iZXJUYWddID1cbnR5cGVkQXJyYXlUYWdzW29iamVjdFRhZ10gPSB0eXBlZEFycmF5VGFnc1tyZWdleHBUYWddID1cbnR5cGVkQXJyYXlUYWdzW3NldFRhZ10gPSB0eXBlZEFycmF5VGFnc1tzdHJpbmdUYWddID1cbnR5cGVkQXJyYXlUYWdzW3dlYWtNYXBUYWddID0gZmFsc2U7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNUeXBlZEFycmF5YCB3aXRob3V0IE5vZGUuanMgb3B0aW1pemF0aW9ucy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHR5cGVkIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGJhc2VJc1R5cGVkQXJyYXkodmFsdWUpIHtcbiAgcmV0dXJuIGlzT2JqZWN0TGlrZSh2YWx1ZSkgJiZcbiAgICBpc0xlbmd0aCh2YWx1ZS5sZW5ndGgpICYmICEhdHlwZWRBcnJheVRhZ3NbYmFzZUdldFRhZyh2YWx1ZSldO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VJc1R5cGVkQXJyYXk7XG4iLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0JyksXG4gICAgaXNQcm90b3R5cGUgPSByZXF1aXJlKCcuL19pc1Byb3RvdHlwZScpLFxuICAgIG5hdGl2ZUtleXNJbiA9IHJlcXVpcmUoJy4vX25hdGl2ZUtleXNJbicpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLmtleXNJbmAgd2hpY2ggZG9lc24ndCB0cmVhdCBzcGFyc2UgYXJyYXlzIGFzIGRlbnNlLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICovXG5mdW5jdGlvbiBiYXNlS2V5c0luKG9iamVjdCkge1xuICBpZiAoIWlzT2JqZWN0KG9iamVjdCkpIHtcbiAgICByZXR1cm4gbmF0aXZlS2V5c0luKG9iamVjdCk7XG4gIH1cbiAgdmFyIGlzUHJvdG8gPSBpc1Byb3RvdHlwZShvYmplY3QpLFxuICAgICAgcmVzdWx0ID0gW107XG5cbiAgZm9yICh2YXIga2V5IGluIG9iamVjdCkge1xuICAgIGlmICghKGtleSA9PSAnY29uc3RydWN0b3InICYmIChpc1Byb3RvIHx8ICFoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwga2V5KSkpKSB7XG4gICAgICByZXN1bHQucHVzaChrZXkpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VLZXlzSW47XG4iLCIvKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLnRpbWVzYCB3aXRob3V0IHN1cHBvcnQgZm9yIGl0ZXJhdGVlIHNob3J0aGFuZHNcbiAqIG9yIG1heCBhcnJheSBsZW5ndGggY2hlY2tzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge251bWJlcn0gbiBUaGUgbnVtYmVyIG9mIHRpbWVzIHRvIGludm9rZSBgaXRlcmF0ZWVgLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcmVzdWx0cy5cbiAqL1xuZnVuY3Rpb24gYmFzZVRpbWVzKG4sIGl0ZXJhdGVlKSB7XG4gIHZhciBpbmRleCA9IC0xLFxuICAgICAgcmVzdWx0ID0gQXJyYXkobik7XG5cbiAgd2hpbGUgKCsraW5kZXggPCBuKSB7XG4gICAgcmVzdWx0W2luZGV4XSA9IGl0ZXJhdGVlKGluZGV4KTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VUaW1lcztcbiIsIi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8udW5hcnlgIHdpdGhvdXQgc3VwcG9ydCBmb3Igc3RvcmluZyBtZXRhZGF0YS5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gZnVuYyBUaGUgZnVuY3Rpb24gdG8gY2FwIGFyZ3VtZW50cyBmb3IuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBjYXBwZWQgZnVuY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIGJhc2VVbmFyeShmdW5jKSB7XG4gIHJldHVybiBmdW5jdGlvbih2YWx1ZSkge1xuICAgIHJldHVybiBmdW5jKHZhbHVlKTtcbiAgfTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlVW5hcnk7XG4iLCJ2YXIgaWRlbnRpdHkgPSByZXF1aXJlKCcuL2lkZW50aXR5Jyk7XG5cbi8qKlxuICogQ2FzdHMgYHZhbHVlYCB0byBgaWRlbnRpdHlgIGlmIGl0J3Mgbm90IGEgZnVuY3Rpb24uXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGluc3BlY3QuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgY2FzdCBmdW5jdGlvbi5cbiAqL1xuZnVuY3Rpb24gY2FzdEZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ2Z1bmN0aW9uJyA/IHZhbHVlIDogaWRlbnRpdHk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY2FzdEZ1bmN0aW9uO1xuIiwiLyoqXG4gKiBDcmVhdGVzIGEgYmFzZSBmdW5jdGlvbiBmb3IgbWV0aG9kcyBsaWtlIGBfLmZvckluYCBhbmQgYF8uZm9yT3duYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtib29sZWFufSBbZnJvbVJpZ2h0XSBTcGVjaWZ5IGl0ZXJhdGluZyBmcm9tIHJpZ2h0IHRvIGxlZnQuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBiYXNlIGZ1bmN0aW9uLlxuICovXG5mdW5jdGlvbiBjcmVhdGVCYXNlRm9yKGZyb21SaWdodCkge1xuICByZXR1cm4gZnVuY3Rpb24ob2JqZWN0LCBpdGVyYXRlZSwga2V5c0Z1bmMpIHtcbiAgICB2YXIgaW5kZXggPSAtMSxcbiAgICAgICAgaXRlcmFibGUgPSBPYmplY3Qob2JqZWN0KSxcbiAgICAgICAgcHJvcHMgPSBrZXlzRnVuYyhvYmplY3QpLFxuICAgICAgICBsZW5ndGggPSBwcm9wcy5sZW5ndGg7XG5cbiAgICB3aGlsZSAobGVuZ3RoLS0pIHtcbiAgICAgIHZhciBrZXkgPSBwcm9wc1tmcm9tUmlnaHQgPyBsZW5ndGggOiArK2luZGV4XTtcbiAgICAgIGlmIChpdGVyYXRlZShpdGVyYWJsZVtrZXldLCBrZXksIGl0ZXJhYmxlKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBvYmplY3Q7XG4gIH07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY3JlYXRlQmFzZUZvcjtcbiIsIi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZ2xvYmFsYCBmcm9tIE5vZGUuanMuICovXG52YXIgZnJlZUdsb2JhbCA9IHR5cGVvZiBnbG9iYWwgPT0gJ29iamVjdCcgJiYgZ2xvYmFsICYmIGdsb2JhbC5PYmplY3QgPT09IE9iamVjdCAmJiBnbG9iYWw7XG5cbm1vZHVsZS5leHBvcnRzID0gZnJlZUdsb2JhbDtcbiIsInZhciBTeW1ib2wgPSByZXF1aXJlKCcuL19TeW1ib2wnKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIEEgc3BlY2lhbGl6ZWQgdmVyc2lvbiBvZiBgYmFzZUdldFRhZ2Agd2hpY2ggaWdub3JlcyBgU3ltYm9sLnRvU3RyaW5nVGFnYCB2YWx1ZXMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgcmF3IGB0b1N0cmluZ1RhZ2AuXG4gKi9cbmZ1bmN0aW9uIGdldFJhd1RhZyh2YWx1ZSkge1xuICB2YXIgaXNPd24gPSBoYXNPd25Qcm9wZXJ0eS5jYWxsKHZhbHVlLCBzeW1Ub1N0cmluZ1RhZyksXG4gICAgICB0YWcgPSB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ107XG5cbiAgdHJ5IHtcbiAgICB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ10gPSB1bmRlZmluZWQ7XG4gICAgdmFyIHVubWFza2VkID0gdHJ1ZTtcbiAgfSBjYXRjaCAoZSkge31cblxuICB2YXIgcmVzdWx0ID0gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG4gIGlmICh1bm1hc2tlZCkge1xuICAgIGlmIChpc093bikge1xuICAgICAgdmFsdWVbc3ltVG9TdHJpbmdUYWddID0gdGFnO1xuICAgIH0gZWxzZSB7XG4gICAgICBkZWxldGUgdmFsdWVbc3ltVG9TdHJpbmdUYWddO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGdldFJhd1RhZztcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKiogVXNlZCB0byBkZXRlY3QgdW5zaWduZWQgaW50ZWdlciB2YWx1ZXMuICovXG52YXIgcmVJc1VpbnQgPSAvXig/OjB8WzEtOV1cXGQqKSQvO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBpbmRleC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcGFyYW0ge251bWJlcn0gW2xlbmd0aD1NQVhfU0FGRV9JTlRFR0VSXSBUaGUgdXBwZXIgYm91bmRzIG9mIGEgdmFsaWQgaW5kZXguXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGluZGV4LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGlzSW5kZXgodmFsdWUsIGxlbmd0aCkge1xuICBsZW5ndGggPSBsZW5ndGggPT0gbnVsbCA/IE1BWF9TQUZFX0lOVEVHRVIgOiBsZW5ndGg7XG4gIHJldHVybiAhIWxlbmd0aCAmJlxuICAgICh0eXBlb2YgdmFsdWUgPT0gJ251bWJlcicgfHwgcmVJc1VpbnQudGVzdCh2YWx1ZSkpICYmXG4gICAgKHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPCBsZW5ndGgpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzSW5kZXg7XG4iLCIvKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGxpa2VseSBhIHByb3RvdHlwZSBvYmplY3QuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBwcm90b3R5cGUsIGVsc2UgYGZhbHNlYC5cbiAqL1xuZnVuY3Rpb24gaXNQcm90b3R5cGUodmFsdWUpIHtcbiAgdmFyIEN0b3IgPSB2YWx1ZSAmJiB2YWx1ZS5jb25zdHJ1Y3RvcixcbiAgICAgIHByb3RvID0gKHR5cGVvZiBDdG9yID09ICdmdW5jdGlvbicgJiYgQ3Rvci5wcm90b3R5cGUpIHx8IG9iamVjdFByb3RvO1xuXG4gIHJldHVybiB2YWx1ZSA9PT0gcHJvdG87XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNQcm90b3R5cGU7XG4iLCIvKipcbiAqIFRoaXMgZnVuY3Rpb24gaXMgbGlrZVxuICogW2BPYmplY3Qua2V5c2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLW9iamVjdC5rZXlzKVxuICogZXhjZXB0IHRoYXQgaXQgaW5jbHVkZXMgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydGllcy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqL1xuZnVuY3Rpb24gbmF0aXZlS2V5c0luKG9iamVjdCkge1xuICB2YXIgcmVzdWx0ID0gW107XG4gIGlmIChvYmplY3QgIT0gbnVsbCkge1xuICAgIGZvciAodmFyIGtleSBpbiBPYmplY3Qob2JqZWN0KSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBuYXRpdmVLZXlzSW47XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHByb2Nlc3NgIGZyb20gTm9kZS5qcy4gKi9cbnZhciBmcmVlUHJvY2VzcyA9IG1vZHVsZUV4cG9ydHMgJiYgZnJlZUdsb2JhbC5wcm9jZXNzO1xuXG4vKiogVXNlZCB0byBhY2Nlc3MgZmFzdGVyIE5vZGUuanMgaGVscGVycy4gKi9cbnZhciBub2RlVXRpbCA9IChmdW5jdGlvbigpIHtcbiAgdHJ5IHtcbiAgICByZXR1cm4gZnJlZVByb2Nlc3MgJiYgZnJlZVByb2Nlc3MuYmluZGluZyAmJiBmcmVlUHJvY2Vzcy5iaW5kaW5nKCd1dGlsJyk7XG4gIH0gY2F0Y2ggKGUpIHt9XG59KCkpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IG5vZGVVdGlsO1xuIiwiLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKlxuICogQ29udmVydHMgYHZhbHVlYCB0byBhIHN0cmluZyB1c2luZyBgT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZ2AuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNvbnZlcnQuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSBjb252ZXJ0ZWQgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBvYmplY3RUb1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gb2JqZWN0VG9TdHJpbmc7XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBzZWxmYC4gKi9cbnZhciBmcmVlU2VsZiA9IHR5cGVvZiBzZWxmID09ICdvYmplY3QnICYmIHNlbGYgJiYgc2VsZi5PYmplY3QgPT09IE9iamVjdCAmJiBzZWxmO1xuXG4vKiogVXNlZCBhcyBhIHJlZmVyZW5jZSB0byB0aGUgZ2xvYmFsIG9iamVjdC4gKi9cbnZhciByb290ID0gZnJlZUdsb2JhbCB8fCBmcmVlU2VsZiB8fCBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHJvb3Q7XG4iLCJ2YXIgYmFzZUZvciA9IHJlcXVpcmUoJy4vX2Jhc2VGb3InKSxcbiAgICBjYXN0RnVuY3Rpb24gPSByZXF1aXJlKCcuL19jYXN0RnVuY3Rpb24nKSxcbiAgICBrZXlzSW4gPSByZXF1aXJlKCcuL2tleXNJbicpO1xuXG4vKipcbiAqIEl0ZXJhdGVzIG92ZXIgb3duIGFuZCBpbmhlcml0ZWQgZW51bWVyYWJsZSBzdHJpbmcga2V5ZWQgcHJvcGVydGllcyBvZiBhblxuICogb2JqZWN0IGFuZCBpbnZva2VzIGBpdGVyYXRlZWAgZm9yIGVhY2ggcHJvcGVydHkuIFRoZSBpdGVyYXRlZSBpcyBpbnZva2VkXG4gKiB3aXRoIHRocmVlIGFyZ3VtZW50czogKHZhbHVlLCBrZXksIG9iamVjdCkuIEl0ZXJhdGVlIGZ1bmN0aW9ucyBtYXkgZXhpdFxuICogaXRlcmF0aW9uIGVhcmx5IGJ5IGV4cGxpY2l0bHkgcmV0dXJuaW5nIGBmYWxzZWAuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjMuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIGl0ZXJhdGUgb3Zlci5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IFtpdGVyYXRlZT1fLmlkZW50aXR5XSBUaGUgZnVuY3Rpb24gaW52b2tlZCBwZXIgaXRlcmF0aW9uLlxuICogQHJldHVybnMge09iamVjdH0gUmV0dXJucyBgb2JqZWN0YC5cbiAqIEBzZWUgXy5mb3JJblJpZ2h0XG4gKiBAZXhhbXBsZVxuICpcbiAqIGZ1bmN0aW9uIEZvbygpIHtcbiAqICAgdGhpcy5hID0gMTtcbiAqICAgdGhpcy5iID0gMjtcbiAqIH1cbiAqXG4gKiBGb28ucHJvdG90eXBlLmMgPSAzO1xuICpcbiAqIF8uZm9ySW4obmV3IEZvbywgZnVuY3Rpb24odmFsdWUsIGtleSkge1xuICogICBjb25zb2xlLmxvZyhrZXkpO1xuICogfSk7XG4gKiAvLyA9PiBMb2dzICdhJywgJ2InLCB0aGVuICdjJyAoaXRlcmF0aW9uIG9yZGVyIGlzIG5vdCBndWFyYW50ZWVkKS5cbiAqL1xuZnVuY3Rpb24gZm9ySW4ob2JqZWN0LCBpdGVyYXRlZSkge1xuICByZXR1cm4gb2JqZWN0ID09IG51bGxcbiAgICA/IG9iamVjdFxuICAgIDogYmFzZUZvcihvYmplY3QsIGNhc3RGdW5jdGlvbihpdGVyYXRlZSksIGtleXNJbik7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZm9ySW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgdGhlIGZpcnN0IGFyZ3VtZW50IGl0IHJlY2VpdmVzLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBzaW5jZSAwLjEuMFxuICogQG1lbWJlck9mIF9cbiAqIEBjYXRlZ29yeSBVdGlsXG4gKiBAcGFyYW0geyp9IHZhbHVlIEFueSB2YWx1ZS5cbiAqIEByZXR1cm5zIHsqfSBSZXR1cm5zIGB2YWx1ZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIHZhciBvYmplY3QgPSB7ICdhJzogMSB9O1xuICpcbiAqIGNvbnNvbGUubG9nKF8uaWRlbnRpdHkob2JqZWN0KSA9PT0gb2JqZWN0KTtcbiAqIC8vID0+IHRydWVcbiAqL1xuZnVuY3Rpb24gaWRlbnRpdHkodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlkZW50aXR5O1xuIiwidmFyIGJhc2VJc0FyZ3VtZW50cyA9IHJlcXVpcmUoJy4vX2Jhc2VJc0FyZ3VtZW50cycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHByb3BlcnR5SXNFbnVtZXJhYmxlID0gb2JqZWN0UHJvdG8ucHJvcGVydHlJc0VudW1lcmFibGU7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgbGlrZWx5IGFuIGBhcmd1bWVudHNgIG9iamVjdC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBgYXJndW1lbnRzYCBvYmplY3QsXG4gKiAgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQXJndW1lbnRzKGZ1bmN0aW9uKCkgeyByZXR1cm4gYXJndW1lbnRzOyB9KCkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcmd1bWVudHMoWzEsIDIsIDNdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc0FyZ3VtZW50cyA9IGJhc2VJc0FyZ3VtZW50cyhmdW5jdGlvbigpIHsgcmV0dXJuIGFyZ3VtZW50czsgfSgpKSA/IGJhc2VJc0FyZ3VtZW50cyA6IGZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGhhc093blByb3BlcnR5LmNhbGwodmFsdWUsICdjYWxsZWUnKSAmJlxuICAgICFwcm9wZXJ0eUlzRW51bWVyYWJsZS5jYWxsKHZhbHVlLCAnY2FsbGVlJyk7XG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJndW1lbnRzO1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGFuIGBBcnJheWAgb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheShbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheShkb2N1bWVudC5ib2R5LmNoaWxkcmVuKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KF8ubm9vcCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNBcnJheSA9IEFycmF5LmlzQXJyYXk7XG5cbm1vZHVsZS5leHBvcnRzID0gaXNBcnJheTtcbiIsInZhciBpc0Z1bmN0aW9uID0gcmVxdWlyZSgnLi9pc0Z1bmN0aW9uJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYXJyYXktbGlrZS4gQSB2YWx1ZSBpcyBjb25zaWRlcmVkIGFycmF5LWxpa2UgaWYgaXQnc1xuICogbm90IGEgZnVuY3Rpb24gYW5kIGhhcyBhIGB2YWx1ZS5sZW5ndGhgIHRoYXQncyBhbiBpbnRlZ2VyIGdyZWF0ZXIgdGhhbiBvclxuICogZXF1YWwgdG8gYDBgIGFuZCBsZXNzIHRoYW4gb3IgZXF1YWwgdG8gYE51bWJlci5NQVhfU0FGRV9JTlRFR0VSYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhcnJheS1saWtlLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKGRvY3VtZW50LmJvZHkuY2hpbGRyZW4pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoJ2FiYycpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzQXJyYXlMaWtlKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPSBudWxsICYmIGlzTGVuZ3RoKHZhbHVlLmxlbmd0aCkgJiYgIWlzRnVuY3Rpb24odmFsdWUpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJyYXlMaWtlO1xuIiwidmFyIHJvb3QgPSByZXF1aXJlKCcuL19yb290JyksXG4gICAgc3R1YkZhbHNlID0gcmVxdWlyZSgnLi9zdHViRmFsc2UnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBCdWZmZXIgPSBtb2R1bGVFeHBvcnRzID8gcm9vdC5CdWZmZXIgOiB1bmRlZmluZWQ7XG5cbi8qIEJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzIGZvciB0aG9zZSB3aXRoIHRoZSBzYW1lIG5hbWUgYXMgb3RoZXIgYGxvZGFzaGAgbWV0aG9kcy4gKi9cbnZhciBuYXRpdmVJc0J1ZmZlciA9IEJ1ZmZlciA/IEJ1ZmZlci5pc0J1ZmZlciA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlci5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMy4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlciwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQnVmZmVyKG5ldyBCdWZmZXIoMikpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNCdWZmZXIobmV3IFVpbnQ4QXJyYXkoMikpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzQnVmZmVyID0gbmF0aXZlSXNCdWZmZXIgfHwgc3R1YkZhbHNlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQnVmZmVyO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0Jyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhc3luY1RhZyA9ICdbb2JqZWN0IEFzeW5jRnVuY3Rpb25dJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBnZW5UYWcgPSAnW29iamVjdCBHZW5lcmF0b3JGdW5jdGlvbl0nLFxuICAgIHByb3h5VGFnID0gJ1tvYmplY3QgUHJveHldJztcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGEgYEZ1bmN0aW9uYCBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBmdW5jdGlvbiwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzRnVuY3Rpb24oXyk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0Z1bmN0aW9uKC9hYmMvKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzRnVuY3Rpb24odmFsdWUpIHtcbiAgaWYgKCFpc09iamVjdCh2YWx1ZSkpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgLy8gVGhlIHVzZSBvZiBgT2JqZWN0I3RvU3RyaW5nYCBhdm9pZHMgaXNzdWVzIHdpdGggdGhlIGB0eXBlb2ZgIG9wZXJhdG9yXG4gIC8vIGluIFNhZmFyaSA5IHdoaWNoIHJldHVybnMgJ29iamVjdCcgZm9yIHR5cGVkIGFycmF5cyBhbmQgb3RoZXIgY29uc3RydWN0b3JzLlxuICB2YXIgdGFnID0gYmFzZUdldFRhZyh2YWx1ZSk7XG4gIHJldHVybiB0YWcgPT0gZnVuY1RhZyB8fCB0YWcgPT0gZ2VuVGFnIHx8IHRhZyA9PSBhc3luY1RhZyB8fCB0YWcgPT0gcHJveHlUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNGdW5jdGlvbjtcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBsZW5ndGguXG4gKlxuICogKipOb3RlOioqIFRoaXMgbWV0aG9kIGlzIGxvb3NlbHkgYmFzZWQgb25cbiAqIFtgVG9MZW5ndGhgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy10b2xlbmd0aCkuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBsZW5ndGgsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0xlbmd0aCgzKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzTGVuZ3RoKE51bWJlci5NSU5fVkFMVUUpO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzTGVuZ3RoKEluZmluaXR5KTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0xlbmd0aCgnMycpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNMZW5ndGgodmFsdWUpIHtcbiAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PSAnbnVtYmVyJyAmJlxuICAgIHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPD0gTUFYX1NBRkVfSU5URUdFUjtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0xlbmd0aDtcbiIsIi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgdGhlXG4gKiBbbGFuZ3VhZ2UgdHlwZV0oaHR0cDovL3d3dy5lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLWVjbWFzY3JpcHQtbGFuZ3VhZ2UtdHlwZXMpXG4gKiBvZiBgT2JqZWN0YC4gKGUuZy4gYXJyYXlzLCBmdW5jdGlvbnMsIG9iamVjdHMsIHJlZ2V4ZXMsIGBuZXcgTnVtYmVyKDApYCwgYW5kIGBuZXcgU3RyaW5nKCcnKWApXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNPYmplY3Qoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3QoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0KF8ubm9vcCk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdChudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0KHZhbHVlKSB7XG4gIHZhciB0eXBlID0gdHlwZW9mIHZhbHVlO1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiAodHlwZSA9PSAnb2JqZWN0JyB8fCB0eXBlID09ICdmdW5jdGlvbicpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzT2JqZWN0O1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZS4gQSB2YWx1ZSBpcyBvYmplY3QtbGlrZSBpZiBpdCdzIG5vdCBgbnVsbGBcbiAqIGFuZCBoYXMgYSBgdHlwZW9mYCByZXN1bHQgb2YgXCJvYmplY3RcIi5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZSh7fSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0TGlrZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiB0eXBlb2YgdmFsdWUgPT0gJ29iamVjdCc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNPYmplY3RMaWtlO1xuIiwidmFyIGJhc2VJc1R5cGVkQXJyYXkgPSByZXF1aXJlKCcuL19iYXNlSXNUeXBlZEFycmF5JyksXG4gICAgYmFzZVVuYXJ5ID0gcmVxdWlyZSgnLi9fYmFzZVVuYXJ5JyksXG4gICAgbm9kZVV0aWwgPSByZXF1aXJlKCcuL19ub2RlVXRpbCcpO1xuXG4vKiBOb2RlLmpzIGhlbHBlciByZWZlcmVuY2VzLiAqL1xudmFyIG5vZGVJc1R5cGVkQXJyYXkgPSBub2RlVXRpbCAmJiBub2RlVXRpbC5pc1R5cGVkQXJyYXk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhIHR5cGVkIGFycmF5LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdHlwZWQgYXJyYXksIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc1R5cGVkQXJyYXkobmV3IFVpbnQ4QXJyYXkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNUeXBlZEFycmF5KFtdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc1R5cGVkQXJyYXkgPSBub2RlSXNUeXBlZEFycmF5ID8gYmFzZVVuYXJ5KG5vZGVJc1R5cGVkQXJyYXkpIDogYmFzZUlzVHlwZWRBcnJheTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc1R5cGVkQXJyYXk7XG4iLCJ2YXIgYXJyYXlMaWtlS2V5cyA9IHJlcXVpcmUoJy4vX2FycmF5TGlrZUtleXMnKSxcbiAgICBiYXNlS2V5c0luID0gcmVxdWlyZSgnLi9fYmFzZUtleXNJbicpLFxuICAgIGlzQXJyYXlMaWtlID0gcmVxdWlyZSgnLi9pc0FycmF5TGlrZScpO1xuXG4vKipcbiAqIENyZWF0ZXMgYW4gYXJyYXkgb2YgdGhlIG93biBhbmQgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydHkgbmFtZXMgb2YgYG9iamVjdGAuXG4gKlxuICogKipOb3RlOioqIE5vbi1vYmplY3QgdmFsdWVzIGFyZSBjb2VyY2VkIHRvIG9iamVjdHMuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAzLjAuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqIEBleGFtcGxlXG4gKlxuICogZnVuY3Rpb24gRm9vKCkge1xuICogICB0aGlzLmEgPSAxO1xuICogICB0aGlzLmIgPSAyO1xuICogfVxuICpcbiAqIEZvby5wcm90b3R5cGUuYyA9IDM7XG4gKlxuICogXy5rZXlzSW4obmV3IEZvbyk7XG4gKiAvLyA9PiBbJ2EnLCAnYicsICdjJ10gKGl0ZXJhdGlvbiBvcmRlciBpcyBub3QgZ3VhcmFudGVlZClcbiAqL1xuZnVuY3Rpb24ga2V5c0luKG9iamVjdCkge1xuICByZXR1cm4gaXNBcnJheUxpa2Uob2JqZWN0KSA/IGFycmF5TGlrZUtleXMob2JqZWN0LCB0cnVlKSA6IGJhc2VLZXlzSW4ob2JqZWN0KTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBrZXlzSW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgYGZhbHNlYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMTMuMFxuICogQGNhdGVnb3J5IFV0aWxcbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8udGltZXMoMiwgXy5zdHViRmFsc2UpO1xuICogLy8gPT4gW2ZhbHNlLCBmYWxzZV1cbiAqL1xuZnVuY3Rpb24gc3R1YkZhbHNlKCkge1xuICByZXR1cm4gZmFsc2U7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gc3R1YkZhbHNlO1xuIiwiaW1wb3J0IHsgTGF2YUpzIH0gZnJvbSAnLi9sYXZhL0xhdmEuZXM2JztcbmltcG9ydCB7IGFkZEV2ZW50LCBkb21Mb2FkZWQgfSBmcm9tICcuL2xhdmEvVXRpbHMuZXM2JztcblxuLyoqXG4gKiBBc3NpZ24gdGhlIExhdmEuanMgbW9kdWxlIHRvIHRoZSB3aW5kb3cgYW5kXG4gKiBsZXQgJGxhdmEgYmUgYW4gYWxpYXMgdG8gdGhlIG1vZHVsZS5cbiAqL1xubGV0ICRsYXZhID0gd2luZG93LmxhdmEgPSBuZXcgTGF2YUpzKCk7XG5cbi8qKlxuICogT25jZSB0aGUgRE9NIGhhcyBsb2FkZWQuLi5cbiAqL1xuZG9tTG9hZGVkKCkudGhlbigoKSA9PiB7XG4gICAgLyoqXG4gICAgICogQWRkaW5nIHRoZSByZXNpemUgZXZlbnQgbGlzdGVuZXIgZm9yIHJlZHJhd2luZyBjaGFydHMgaWZcbiAgICAgKiB0aGUgb3B0aW9uIHJlc3BvbnNpdmUgaXMgc2V0IHRvIHRydWUuXG4gICAgICovXG4gICAgaWYgKCRsYXZhLm9wdGlvbnMucmVzcG9uc2l2ZSA9PT0gdHJ1ZSkge1xuICAgICAgICBsZXQgZGVib3VuY2VkID0gbnVsbDtcblxuICAgICAgICBhZGRFdmVudCh3aW5kb3csICdyZXNpemUnLCAoKSA9PiB7XG4gICAgICAgICAgICBsZXQgcmVkcmF3ID0gJGxhdmEucmVkcmF3QWxsLmJpbmQoJGxhdmEpO1xuXG4gICAgICAgICAgICBjbGVhclRpbWVvdXQoZGVib3VuY2VkKTtcblxuICAgICAgICAgICAgZGVib3VuY2VkID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBXaW5kb3cgcmUtc2l6ZWQsIHJlZHJhd2luZy4uLicpO1xuXG4gICAgICAgICAgICAgICAgcmVkcmF3KCk7XG4gICAgICAgICAgICB9LCAkbGF2YS5vcHRpb25zLmRlYm91bmNlX3RpbWVvdXQpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICBpZiAoJGxhdmEub3B0aW9ucy5hdXRvX3J1biA9PT0gdHJ1ZSkge1xuICAgICAgICAkbGF2YS5ydW4oKTtcbiAgICB9XG59KTtcbiIsIi8qKlxuICogQ2hhcnQgbW9kdWxlXG4gKlxuICogQGNsYXNzICAgICBDaGFydFxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xuICogQGxpY2Vuc2UgICBNSVRcbiAqL1xuaW1wb3J0IF9mb3JJbiBmcm9tICdsb2Rhc2gvZm9ySW4nO1xuaW1wb3J0IHsgUmVuZGVyYWJsZSB9IGZyb20gJy4vUmVuZGVyYWJsZS5lczYnO1xuaW1wb3J0IHsgc3RyaW5nVG9GdW5jdGlvbiB9IGZyb20gJy4vVXRpbHMuZXM2JztcblxuLyoqXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cbiAqXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBDaGFydFxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHR5cGUgICAgICAtIFR5cGUgb2YgY2hhcnQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBwYWNrYWdlICAgLSBUeXBlIG9mIEdvb2dsZSBjaGFydCBwYWNrYWdlIHRvIGxvYWQuXG4gKiBAcHJvcGVydHkge2Jvb2xlYW59ICBwbmdPdXRwdXQgLSBTaG91bGQgdGhlIGNoYXJ0IGJlIGRpc3BsYXllZCBhcyBhIFBORy5cbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgb3B0aW9ucyAgIC0gQ29uZmlndXJhdGlvbiBvcHRpb25zIGZvciB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBldmVudHMgICAgLSBFdmVudHMgYW5kIGNhbGxiYWNrcyB0byBhcHBseSB0byB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBmb3JtYXRzICAgLSBGb3JtYXR0ZXJzIHRvIGFwcGx5IHRvIHRoZSBjaGFydCBkYXRhLlxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gcmVuZGVyICAgIC0gUmVuZGVycyB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSB1dWlkICAgICAgLSBDcmVhdGVzIGlkZW50aWZpY2F0aW9uIHN0cmluZyBmb3IgdGhlIGNoYXJ0LlxuICovXG5leHBvcnQgY2xhc3MgQ2hhcnQgZXh0ZW5kcyBSZW5kZXJhYmxlXG57XG4gICAgLyoqXG4gICAgICogQ2hhcnQgQ2xhc3NcbiAgICAgKlxuICAgICAqIFRoaXMgaXMgdGhlIGphdmFzY3JpcHQgdmVyc2lvbiBvZiBhIGxhdmFjaGFydCB3aXRoIG1ldGhvZHMgZm9yIGludGVyYWN0aW5nIHdpdGhcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0ganNvblxuICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAqL1xuICAgIGNvbnN0cnVjdG9yIChqc29uKSB7XG4gICAgICAgIHN1cGVyKGpzb24pO1xuXG4gICAgICAgIHRoaXMudHlwZSAgICA9IGpzb24udHlwZTtcbiAgICAgICAgdGhpcy5jbGFzcyAgID0ganNvbi5jbGFzcztcbiAgICAgICAgdGhpcy5mb3JtYXRzID0ganNvbi5mb3JtYXRzO1xuXG4gICAgICAgIHRoaXMuZXZlbnRzICAgID0gdHlwZW9mIGpzb24uZXZlbnRzID09PSAnb2JqZWN0JyA/IGpzb24uZXZlbnRzIDogbnVsbDtcbiAgICAgICAgdGhpcy5wbmdPdXRwdXQgPSB0eXBlb2YganNvbi5wbmdPdXRwdXQgPT09ICd1bmRlZmluZWQnID8gZmFsc2UgOiBCb29sZWFuKGpzb24ucG5nT3V0cHV0KTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogQW55IGRlcGVuZGVuY3kgb24gd2luZG93Lmdvb2dsZSBtdXN0IGJlIGluIHRoZSByZW5kZXIgc2NvcGUuXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0RGF0YShqc29uLmRhdGF0YWJsZSk7XG5cbiAgICAgICAgICAgIGxldCBDaGFydENsYXNzID0gc3RyaW5nVG9GdW5jdGlvbih0aGlzLmNsYXNzLCB3aW5kb3cpO1xuXG4gICAgICAgICAgICB0aGlzLmdjaGFydCA9IG5ldyBDaGFydENsYXNzKHRoaXMuZWxlbWVudCk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLmZvcm1hdHMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmFwcGx5Rm9ybWF0cygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAodGhpcy5ldmVudHMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcbiAgICAgICAgICAgICAgICAvLyBUT0RPOiBJZGVhLi4uIGZvcndhcmQgZXZlbnRzIHRvIGJlIGxpc3RlbmFibGUgYnkgdGhlIHVzZXIsIGluc3RlYWQgb2YgaGF2aW5nIHRoZSB1c2VyIGRlZmluZSB0aGVtIGFzIGEgc3RyaW5nIGNhbGxiYWNrLlxuICAgICAgICAgICAgICAgIC8vIGxhdmEuZ2V0KCdNeUNvb2xDaGFydCcpLm9uKCdyZWFkeScsIGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgICAgICAgICAvLyAgICAgY29uc29sZS5sb2codGhpcyk7ICAvLyBnQ2hhcnRcbiAgICAgICAgICAgICAgICAvLyB9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5kcmF3KCk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLnBuZ091dHB1dCkge1xuICAgICAgICAgICAgICAgIHRoaXMuZHJhd1BuZygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIERyYXdzIHRoZSBjaGFydCBhcyBhIFBORyBpbnN0ZWFkIG9mIHRoZSBzdGFuZGFyZCBTVkdcbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAZXh0ZXJuYWwgXCJjaGFydC5nZXRJbWFnZVVSSVwiXG4gICAgICogQHNlZSB7QGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXJzLmdvb2dsZS5jb20vY2hhcnQvaW50ZXJhY3RpdmUvZG9jcy9wcmludGluZ3xQcmludGluZyBQTkcgQ2hhcnRzfVxuICAgICAqL1xuICAgIGRyYXdQbmcoKSB7XG4gICAgICAgIGxldCBpbWcgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbWcnKTtcbiAgICAgICAgICAgIGltZy5zcmMgPSB0aGlzLmdjaGFydC5nZXRJbWFnZVVSSSgpO1xuXG4gICAgICAgIHRoaXMuZWxlbWVudC5pbm5lckhUTUwgPSAnJztcbiAgICAgICAgdGhpcy5lbGVtZW50LmFwcGVuZENoaWxkKGltZyk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQXBwbHkgdGhlIGZvcm1hdHMgdG8gdGhlIERhdGFUYWJsZVxuICAgICAqXG4gICAgICogQHBhcmFtIHtBcnJheX0gZm9ybWF0c1xuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBhcHBseUZvcm1hdHMoZm9ybWF0cykge1xuICAgICAgICBpZiAoISBmb3JtYXRzKSB7XG4gICAgICAgICAgICBmb3JtYXRzID0gdGhpcy5mb3JtYXRzO1xuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChsZXQgZm9ybWF0IG9mIGZvcm1hdHMpIHtcbiAgICAgICAgICAgIGxldCBmb3JtYXR0ZXIgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb25bZm9ybWF0LnR5cGVdKGZvcm1hdC5vcHRpb25zKTtcblxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBDb2x1bW4gaW5kZXggWyR7Zm9ybWF0LmluZGV4fV0gZm9ybWF0dGVkIHdpdGg6YCwgZm9ybWF0dGVyKTtcblxuICAgICAgICAgICAgZm9ybWF0dGVyLmZvcm1hdCh0aGlzLmRhdGEsIGZvcm1hdC5pbmRleCk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBdHRhY2ggdGhlIGRlZmluZWQgY2hhcnQgZXZlbnQgaGFuZGxlcnMuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9hdHRhY2hFdmVudHMoKSB7XG4gICAgICAgIGxldCAkY2hhcnQgPSB0aGlzO1xuXG4gICAgICAgIF9mb3JJbih0aGlzLmV2ZW50cywgZnVuY3Rpb24gKGNhbGxiYWNrLCBldmVudCkge1xuICAgICAgICAgICAgbGV0IGNvbnRleHQgPSB3aW5kb3c7XG4gICAgICAgICAgICBsZXQgZnVuYyA9IGNhbGxiYWNrO1xuXG4gICAgICAgICAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgICAgIGNvbnRleHQgPSBjb250ZXh0W2NhbGxiYWNrWzBdXTtcbiAgICAgICAgICAgICAgICBmdW5jID0gY2FsbGJhY2tbMV07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gVGhlIFwiJHskY2hhcnQudXVpZCgpfTo6JHtldmVudH1cIiBldmVudCB3aWxsIGJlIGhhbmRsZWQgYnkgXCIke2Z1bmN9XCIgaW4gdGhlIGNvbnRleHRgLCBjb250ZXh0KTtcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBTZXQgdGhlIGNvbnRleHQgb2YgXCJ0aGlzXCIgd2l0aGluIHRoZSB1c2VyIHByb3ZpZGVkIGNhbGxiYWNrIHRvIHRoZVxuICAgICAgICAgICAgICogY2hhcnQgdGhhdCBmaXJlZCB0aGUgZXZlbnQgd2hpbGUgcHJvdmlkaW5nIHRoZSBkYXRhdGFibGUgb2YgdGhlIGNoYXJ0XG4gICAgICAgICAgICAgKiB0byB0aGUgY2FsbGJhY2sgYXMgYW4gYXJndW1lbnQuXG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIGdvb2dsZS52aXN1YWxpemF0aW9uLmV2ZW50cy5hZGRMaXN0ZW5lcigkY2hhcnQuZ2NoYXJ0LCBldmVudCwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgY2FsbGJhY2sgPSBjb250ZXh0W2Z1bmNdLmJpbmQoJGNoYXJ0LmdjaGFydCk7XG5cbiAgICAgICAgICAgICAgICBjYWxsYmFjaygkY2hhcnQuZGF0YSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgfVxufVxuIiwiLyoqXG4gKiBEYXNoYm9hcmQgbW9kdWxlXG4gKlxuICogQGNsYXNzICAgICBEYXNoYm9hcmRcbiAqIEBtb2R1bGUgICAgbGF2YS9EYXNoYm9hcmRcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXG4gKiBAbGljZW5zZSAgIE1JVFxuICovXG5pbXBvcnQgeyBSZW5kZXJhYmxlIH0gZnJvbSAnLi9SZW5kZXJhYmxlLmVzNic7XG5pbXBvcnQgeyBzdHJpbmdUb0Z1bmN0aW9uIH0gZnJvbSAnLi9VdGlscy5lczYnO1xuXG4vKipcbiAqIERhc2hib2FyZCBjbGFzc1xuICpcbiAqIEB0eXBlZGVmIHtGdW5jdGlvbn0gIERhc2hib2FyZFxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBEYXNoYm9hcmQuXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIHZpc3VhbGl6YXRpb24gKERhc2hib2FyZCkuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgdmlzdWFsaXphdGlvbiBwYWNrYWdlIHRvIGxvYWQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBkYXRhICAgICAgLSBEYXRhdGFibGUgZm9yIHRoZSBEYXNoYm9hcmQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMuXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBiaW5kaW5ncyAgLSBDaGFydCBhbmQgQ29udHJvbCBiaW5kaW5ncy5cbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIERhc2hib2FyZC5cbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHV1aWQgICAgICAtIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgRGFzaGJvYXJkLlxuICovXG5leHBvcnQgY2xhc3MgRGFzaGJvYXJkIGV4dGVuZHMgUmVuZGVyYWJsZVxue1xuICAgIGNvbnN0cnVjdG9yKGpzb24pIHtcbiAgICAgICAgc3VwZXIoanNvbik7XG5cbiAgICAgICAgdGhpcy50eXBlICAgICA9ICdEYXNoYm9hcmQnO1xuICAgICAgICB0aGlzLmJpbmRpbmdzID0ganNvbi5iaW5kaW5ncztcblxuICAgICAgICAvKipcbiAgICAgICAgICogQW55IGRlcGVuZGVuY3kgb24gd2luZG93Lmdvb2dsZSBtdXN0IGJlIGluIHRoZSByZW5kZXIgc2NvcGUuXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcbiAgICAgICAgICAgIHRoaXMuc2V0RGF0YShqc29uLmRhdGF0YWJsZSk7XG5cbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhc2hib2FyZCh0aGlzLmVsZW1lbnQpO1xuXG4gICAgICAgICAgICB0aGlzLl9hdHRhY2hCaW5kaW5ncygpO1xuXG4gICAgICAgICAgICBpZiAodGhpcy5ldmVudHMpIHtcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5kcmF3KCk7XG4gICAgICAgIH07XG4gICAgfVxuXG4gICAgLy8gQFRPRE86IHRoaXMgbmVlZHMgdG8gYmUgbW9kaWZpZWQgZm9yIHRoZSBvdGhlciB0eXBlcyBvZiBiaW5kaW5ncy5cblxuICAgIC8qKlxuICAgICAqIFByb2Nlc3MgYW5kIGF0dGFjaCB0aGUgYmluZGluZ3MgdG8gdGhlIGRhc2hib2FyZC5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2F0dGFjaEJpbmRpbmdzKCkge1xuICAgICAgICBmb3IgKGxldCBiaW5kaW5nIG9mIHRoaXMuYmluZGluZ3MpIHtcbiAgICAgICAgICAgIC8vIGxldCBbY29udHJvbFdyYXBwZXIsIGNoYXJ0V3JhcHBlcl0gPSBiaW5kaW5nO1xuXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnYmluZGluZycsIGJpbmRpbmcpO1xuICAgICAgICAgICAgY29uc29sZS5sb2coJ2NvbnRyb2xXcmFwcGVyJywgYmluZGluZy5jb250cm9sV3JhcHBlcnMpO1xuICAgICAgICAgICAgY29uc29sZS5sb2coJ2NoYXJ0V3JhcHBlcicsIGJpbmRpbmcuY2hhcnRXcmFwcGVycyk7XG5cbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0LmJpbmQoXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkNvbnRyb2xXcmFwcGVyKFxuICAgICAgICAgICAgICAgICAgICBiaW5kaW5nLmNvbnRyb2xXcmFwcGVyc1swXVxuICAgICAgICAgICAgICAgICksXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkNoYXJ0V3JhcHBlcihcbiAgICAgICAgICAgICAgICAgICAgYmluZGluZy5jaGFydFdyYXBwZXJzWzBdXG4gICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgKTtcbiAgICAgICAgfVxuICAgIH1cbn1cbiIsIi8qKlxuICogRXJyb3JzIG1vZHVsZVxuICpcbiAqIEBtb2R1bGUgICAgbGF2YS9FcnJvcnNcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXG4gKiBAbGljZW5zZSAgIE1JVFxuICovXG5jbGFzcyBMYXZhRXJyb3IgZXh0ZW5kcyBFcnJvclxue1xuICAgIGNvbnN0cnVjdG9yIChtZXNzYWdlKSB7XG4gICAgICAgIHN1cGVyKCk7XG5cbiAgICAgICAgdGhpcy5uYW1lICAgID0gJ0xhdmFFcnJvcic7XG4gICAgICAgIHRoaXMubWVzc2FnZSA9IChtZXNzYWdlIHx8ICcnKTtcbiAgICB9O1xufVxuXG4vKipcbiAqIEludmFsaWRDYWxsYmFjayBFcnJvclxuICpcbiAqIHRocm93biB3aGVuIHdoZW4gYW55dGhpbmcgYnV0IGEgZnVuY3Rpb24gaXMgZ2l2ZW4gYXMgYSBjYWxsYmFja1xuICogQHR5cGUge2Z1bmN0aW9ufVxuICovXG5leHBvcnQgY2xhc3MgSW52YWxpZENhbGxiYWNrIGV4dGVuZHMgTGF2YUVycm9yXG57XG4gICAgY29uc3RydWN0b3IgKGNhbGxiYWNrKSB7XG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gXCIke3R5cGVvZiBjYWxsYmFja31cIiBpcyBub3QgYSB2YWxpZCBjYWxsYmFjay5gKTtcblxuICAgICAgICB0aGlzLm5hbWUgPSAnSW52YWxpZENhbGxiYWNrJztcbiAgICB9XG59XG5cbi8qKlxuICogSW52YWxpZExhYmVsIEVycm9yXG4gKlxuICogVGhyb3duIHdoZW4gd2hlbiBhbnl0aGluZyBidXQgYSBzdHJpbmcgaXMgZ2l2ZW4gYXMgYSBsYWJlbC5cbiAqXG4gKiBAdHlwZSB7ZnVuY3Rpb259XG4gKi9cbmV4cG9ydCBjbGFzcyBJbnZhbGlkTGFiZWwgZXh0ZW5kcyBMYXZhRXJyb3JcbntcbiAgICBjb25zdHJ1Y3RvciAobGFiZWwpIHtcbiAgICAgICAgc3VwZXIoYFtsYXZhLmpzXSBcIiR7dHlwZW9mIGxhYmVsfVwiIGlzIG5vdCBhIHZhbGlkIGxhYmVsLmApO1xuICAgICAgICB0aGlzLm5hbWUgPSAnSW52YWxpZExhYmVsJztcbiAgICB9XG59XG5cbi8qKlxuICogRWxlbWVudElkTm90Rm91bmQgRXJyb3JcbiAqXG4gKiBUaHJvd24gd2hlbiB3aGVuIGFueXRoaW5nIGJ1dCBhIHN0cmluZyBpcyBnaXZlbiBhcyBhIGxhYmVsLlxuICpcbiAqIEB0eXBlIHtmdW5jdGlvbn1cbiAqL1xuZXhwb3J0IGNsYXNzIEVsZW1lbnRJZE5vdEZvdW5kIGV4dGVuZHMgTGF2YUVycm9yXG57XG4gICAgY29uc3RydWN0b3IgKGVsZW1JZCkge1xuICAgICAgICBzdXBlcihgW2xhdmEuanNdIERPTSBub2RlIHdoZXJlIGlkPVwiJHtlbGVtSWR9XCIgd2FzIG5vdCBmb3VuZC5gKTtcblxuICAgICAgICB0aGlzLm5hbWUgPSAnRWxlbWVudElkTm90Rm91bmQnO1xuICAgIH1cbn1cbiIsIi8qKlxuICogbGF2YS5qcyBtb2R1bGVcbiAqXG4gKiBAbW9kdWxlICAgIGxhdmEvTGF2YVxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcbiAqIEBsaWNlbnNlICAgaHR0cDovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL01JVCBNSVRcbiAqL1xuaW1wb3J0IF9mb3JJbiBmcm9tICdsb2Rhc2gvZm9ySW4nO1xuaW1wb3J0IEV2ZW50RW1pdHRlciBmcm9tICdldmVudHMnO1xuaW1wb3J0IHsgQ2hhcnQgfSBmcm9tICcuL0NoYXJ0LmVzNic7XG5pbXBvcnQgeyBEYXNoYm9hcmQgfSBmcm9tICcuL0Rhc2hib2FyZC5lczYnO1xuaW1wb3J0IHsgbm9vcCB9IGZyb20gJy4vVXRpbHMuZXM2JztcbmltcG9ydCB7IEludmFsaWRDYWxsYmFjaywgUmVuZGVyYWJsZU5vdEZvdW5kIH0gZnJvbSAnLi9FcnJvcnMuZXM2J1xuXG5cbi8qKlxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgICAgICAgICAgIFZFUlNJT04gICAgICAgIFZlcnNpb24gb2YgdGhlIG1vZHVsZS5cbiAqIEBwcm9wZXJ0eSB7Q2hhcnR9ICAgICAgICAgICAgICBDaGFydCAgICAgICAgICBDaGFydCBjbGFzcy5cbiAqIEBwcm9wZXJ0eSB7RGFzaGJvYXJkfSAgICAgICAgICBEYXNoYm9hcmQgICAgICBEYXNoYm9hcmQgY2xhc3MuXG4gKiBAcHJvcGVydHkge29iamVjdH0gICAgICAgICAgICAgX2Vycm9yc1xuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgICAgICAgICAgIEdPT0dMRV9MT0FERVJfVVJMICAgICBVcmwgdG8gR29vZ2xlJ3Mgc3RhdGljIGxvYWRlclxuICogQHByb3BlcnR5IHtvYmplY3R9ICAgICAgICAgICAgIG9wdGlvbnMgICAgICAgIE9wdGlvbnMgZm9yIHRoZSBtb2R1bGVcbiAqIEBwcm9wZXJ0eSB7ZnVuY3Rpb259ICAgICAgICAgICBfcmVhZHlDYWxsYmFja1xuICogQHByb3BlcnR5IHtBcnJheS48c3RyaW5nPn0gICAgIF9wYWNrYWdlc1xuICogQHByb3BlcnR5IHtBcnJheS48UmVuZGVyYWJsZT59IF9yZW5kZXJhYmxlc1xuICovXG5leHBvcnQgY2xhc3MgTGF2YUpzIGV4dGVuZHMgRXZlbnRFbWl0dGVyXG57XG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIHN1cGVyKCk7XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFZlcnNpb24gb2YgdGhlIExhdmEuanMgbW9kdWxlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7c3RyaW5nfVxuICAgICAgICAgKiBAcHVibGljXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLlZFUlNJT04gPSAnNC4wLjAnO1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBWZXJzaW9uIG9mIHRoZSBHb29nbGUgY2hhcnRzIEFQSSB0byBsb2FkLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7c3RyaW5nfVxuICAgICAgICAgKiBAcHVibGljXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLkdPT0dMRV9BUElfVkVSU0lPTiA9ICdjdXJyZW50JztcblxuICAgICAgICAvKipcbiAgICAgICAgICogVXJscyB0byBHb29nbGUncyBzdGF0aWMgbG9hZGVyXG4gICAgICAgICAqXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XG4gICAgICAgICAqIEBwdWJsaWNcbiAgICAgICAgICovXG4gICAgICAgIHRoaXMuR09PR0xFX0xPQURFUl9VUkwgPSAnaHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vY2hhcnRzL2xvYWRlci5qcyc7XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIENoYXJ0IG1vZHVsZSB3aXRoaW4gTGF2YS5qc1xuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7Q2hhcnR9XG4gICAgICAgICAqIEBwdWJsaWNcbiAgICAgICAgICovXG4gICAgICAgIHRoaXMuQ2hhcnQgPSBDaGFydDtcblxuICAgICAgICAvKipcbiAgICAgICAgICogU3RvcmluZyB0aGUgRGFzaGJvYXJkIG1vZHVsZSB3aXRoaW4gTGF2YS5qc1xuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7RGFzaGJvYXJkfVxuICAgICAgICAgKiBAcHVibGljXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLkRhc2hib2FyZCA9IERhc2hib2FyZDtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSlNPTiBvYmplY3Qgb2YgY29uZmlnIGl0ZW1zLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7T2JqZWN0fVxuICAgICAgICAgKiBAcHVibGljXG4gICAgICAgICAqL1xuICAgICAgICB0aGlzLm9wdGlvbnMgPSBPUFRJT05TX0pTT047XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEFycmF5IG9mIHZpc3VhbGl6YXRpb24gcGFja2FnZXMgZm9yIGNoYXJ0cyBhbmQgZGFzaGJvYXJkcy5cbiAgICAgICAgICpcbiAgICAgICAgICogQHR5cGUge0FycmF5LjxzdHJpbmc+fVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdGhpcy5fcGFja2FnZXMgPSBbXTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogQXJyYXkgb2YgY2hhcnRzIGFuZCBkYXNoYm9hcmRzIHN0b3JlZCBpbiB0aGUgbW9kdWxlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAdHlwZSB7QXJyYXkuPFJlbmRlcmFibGU+fVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdGhpcy5fcmVuZGVyYWJsZXMgPSBbXTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogUmVhZHkgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIHdoZW4gdGhlIG1vZHVsZSBpcyBmaW5pc2hlZCBydW5uaW5nLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAY2FsbGJhY2sgX3JlYWR5Q2FsbGJhY2tcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHRoaXMuX3JlYWR5Q2FsbGJhY2sgPSBub29wO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENyZWF0ZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxuICAgICAqXG4gICAgICogVGhlIEpTT04gcGF5bG9hZCBjb21lcyBmcm9tIHRoZSBQSFAgQ2hhcnQgY2xhc3MuXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtICB7b2JqZWN0fSBqc29uXG4gICAgICogQHJldHVybiB7UmVuZGVyYWJsZX1cbiAgICAgKi9cbiAgICBjcmVhdGVDaGFydChqc29uKSB7XG4gICAgICAgIGNvbnNvbGUubG9nKCdKU09OX1BBWUxPQUQnLCBqc29uKTtcblxuICAgICAgICB0aGlzLl9hZGRQYWNrYWdlcyhqc29uLnBhY2thZ2VzKTtcblxuICAgICAgICByZXR1cm4gbmV3IHRoaXMuQ2hhcnQoanNvbik7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqIEBzZWUgY3JlYXRlQ2hhcnRcbiAgICAgKiBAcGFyYW0ge29iamVjdH0ganNvblxuICAgICAqL1xuICAgIGFkZE5ld0NoYXJ0KGpzb24pIHtcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZUNoYXJ0KGpzb24pKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDcmVhdGUgYSBuZXcgRGFzaGJvYXJkIHdpdGggYSBnaXZlbiBsYWJlbC5cbiAgICAgKlxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIERhc2hib2FyZCBjbGFzcy5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGpzb25cbiAgICAgKiBAcmV0dXJuIHtEYXNoYm9hcmR9XG4gICAgICovXG4gICAgY3JlYXRlRGFzaGJvYXJkKGpzb24pIHtcbiAgICAgICAgY29uc29sZS5sb2coJ0pTT05fUEFZTE9BRCcsIGpzb24pO1xuXG4gICAgICAgIHRoaXMuX2FkZFBhY2thZ2VzKGpzb24ucGFja2FnZXMpO1xuXG4gICAgICAgIHJldHVybiBuZXcgdGhpcy5EYXNoYm9hcmQoanNvbik7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBEYXNoYm9hcmQgZnJvbSBhIEpTT04gcGF5bG9hZC5cbiAgICAgKlxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIERhc2hib2FyZCBjbGFzcy5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAc2VlIGNyZWF0ZURhc2hib2FyZFxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxuICAgICAqIEByZXR1cm4ge0Rhc2hib2FyZH1cbiAgICAgKi9cbiAgICBhZGROZXdEYXNoYm9hcmQoanNvbikge1xuICAgICAgICB0aGlzLnN0b3JlKHRoaXMuY3JlYXRlRGFzaGJvYXJkKGpzb24pKTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBSdW5zIHRoZSBMYXZhLmpzIG1vZHVsZVxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIHJ1bigpIHtcbiAgICAgICAgY29uc3QgJGxhdmEgPSB0aGlzO1xuXG4gICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gUnVubmluZy4uLicpO1xuICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIExvYWRpbmcgb3B0aW9uczonLCB0aGlzLm9wdGlvbnMpO1xuXG4gICAgICAgICRsYXZhLl9sb2FkR29vZ2xlKCkudGhlbigoKSA9PiB7XG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEdvb2dsZSBpcyByZWFkeS4nKTtcblxuICAgICAgICAgICAgX2ZvckluKCRsYXZhLl9yZW5kZXJhYmxlcywgcmVuZGVyYWJsZSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBSZW5kZXJpbmcgJHtyZW5kZXJhYmxlLnV1aWQoKX1gKTtcblxuICAgICAgICAgICAgICAgIHJlbmRlcmFibGUucmVuZGVyKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBGaXJpbmcgXCJyZWFkeVwiIGV2ZW50LicpO1xuICAgICAgICAgICAgJGxhdmEuZW1pdCgncmVhZHknKTtcblxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBFeGVjdXRpbmcgbGF2YS5yZWFkeShjYWxsYmFjayknKTtcbiAgICAgICAgICAgICRsYXZhLl9yZWFkeUNhbGxiYWNrKCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFN0b3JlcyBhIHJlbmRlcmFibGUgbGF2YSBvYmplY3Qgd2l0aGluIHRoZSBtb2R1bGUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1JlbmRlcmFibGV9IHJlbmRlcmFibGVcbiAgICAgKi9cbiAgICBzdG9yZShyZW5kZXJhYmxlKSB7XG4gICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gU3RvcmluZyAke3JlbmRlcmFibGUudXVpZCgpfWApO1xuXG4gICAgICAgIHRoaXMuX3JlbmRlcmFibGVzW3JlbmRlcmFibGUubGFiZWxdID0gcmVuZGVyYWJsZTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBBc3NpZ25zIGEgY2FsbGJhY2sgZm9yIHdoZW4gdGhlIGNoYXJ0cyBhcmUgcmVhZHkgdG8gYmUgaW50ZXJhY3RlZCB3aXRoLlxuICAgICAqXG4gICAgICogVGhpcyBpcyB1c2VkIHRvIHdyYXAgY2FsbHMgdG8gbGF2YS5sb2FkRGF0YSgpIG9yIGxhdmEubG9hZE9wdGlvbnMoKVxuICAgICAqIHRvIHByb3RlY3QgYWdhaW5zdCBhY2Nlc3NpbmcgY2hhcnRzIHRoYXQgYXJlbid0IGxvYWRlZCB5ZXRcbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqL1xuICAgIHJlYWR5KGNhbGxiYWNrKSB7XG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBJbnZhbGlkQ2FsbGJhY2soY2FsbGJhY2spO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5fcmVhZHlDYWxsYmFjayA9IGNhbGxiYWNrO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIExvYWRzIG5ldyBkYXRhIGludG8gdGhlIGNoYXJ0IGFuZCByZWRyYXdzLlxuICAgICAqXG4gICAgICpcbiAgICAgKiBVc2VkIHdpdGggYW4gQUpBWCBjYWxsIHRvIGEgUEhQIG1ldGhvZCByZXR1cm5pbmcgRGF0YVRhYmxlLT50b0pzb24oKSxcbiAgICAgKiBhIGNoYXJ0IGNhbiBiZSBkeW5hbWljYWxseSB1cGRhdGUgaW4gcGFnZSwgd2l0aG91dCByZWxvYWRzLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBqc29uXG4gICAgICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAgICAgKi9cbiAgICBsb2FkRGF0YShsYWJlbCwganNvbiwgY2FsbGJhY2spIHtcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgIGNhbGxiYWNrID0gbm9vcDtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIHRocm93IG5ldyBJbnZhbGlkQ2FsbGJhY2soY2FsbGJhY2spO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5nZXQobGFiZWwsIGZ1bmN0aW9uIChjaGFydCkge1xuICAgICAgICAgICAgY2hhcnQuc2V0RGF0YShqc29uKTtcblxuICAgICAgICAgICAgaWYgKHR5cGVvZiBqc29uLmZvcm1hdHMgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgY2hhcnQuYXBwbHlGb3JtYXRzKGpzb24uZm9ybWF0cyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNoYXJ0LmRyYXcoKTtcblxuICAgICAgICAgICAgY2FsbGJhY2soY2hhcnQpO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBMb2FkcyBuZXcgb3B0aW9ucyBpbnRvIGEgY2hhcnQgYW5kIHJlZHJhd3MuXG4gICAgICpcbiAgICAgKlxuICAgICAqIFVzZWQgd2l0aCBhbiBBSkFYIGNhbGwsIG9yIGphdmFzY3JpcHQgZXZlbnRzLCB0byBsb2FkIGEgbmV3IGFycmF5IG9mIG9wdGlvbnMgaW50byBhIGNoYXJ0LlxuICAgICAqIFRoaXMgY2FuIGJlIHVzZWQgdG8gdXBkYXRlIGEgY2hhcnQgZHluYW1pY2FsbHksIHdpdGhvdXQgcmVsb2Fkcy5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gbGFiZWxcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30ganNvblxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAgICovXG4gICAgbG9hZE9wdGlvbnMobGFiZWwsIGpzb24sIGNhbGxiYWNrKSB7XG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBjYWxsYmFjayA9IGNhbGxiYWNrIHx8IG5vb3A7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodHlwZW9mIGNhbGxiYWNrICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRoaXMuZ2V0KGxhYmVsLCBmdW5jdGlvbiAoY2hhcnQpIHtcbiAgICAgICAgICAgIGNoYXJ0LnNldE9wdGlvbnMoanNvbik7XG4gICAgICAgICAgICBjaGFydC5kcmF3KCk7XG5cbiAgICAgICAgICAgIGNhbGxiYWNrKGNoYXJ0KTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmVkcmF3cyBhbGwgb2YgdGhlIHJlZ2lzdGVyZWQgY2hhcnRzIG9uIHNjcmVlbi5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIGlzIGF0dGFjaGVkIHRvIHRoZSB3aW5kb3cgcmVzaXplIGV2ZW50IHdpdGggZGVib3VuY2luZ1xuICAgICAqIHRvIG1ha2UgdGhlIGNoYXJ0cyByZXNwb25zaXZlIHRvIHRoZSBicm93c2VyIHJlc2l6aW5nLlxuICAgICAqL1xuICAgIHJlZHJhd0FsbCgpIHtcbiAgICAgICAgZm9yIChsZXQgcmVuZGVyYWJsZSBvZiB0aGlzLl9yZW5kZXJhYmxlcykge1xuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBSZWRyYXdpbmcgJHtyZW5kZXJhYmxlLnV1aWQoKX1gKTtcblxuICAgICAgICAgICAgY29uc3QgcmVkcmF3ID0gcmVuZGVyYWJsZS5kcmF3LmJpbmQocmVuZGVyYWJsZSk7XG5cbiAgICAgICAgICAgIHJlZHJhdygpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUmV0dXJucyB0aGUgTGF2YUNoYXJ0IGphdmFzY3JpcHQgb2JqZWN0c1xuICAgICAqXG4gICAgICpcbiAgICAgKiBUaGUgTGF2YUNoYXJ0IG9iamVjdCBob2xkcyBhbGwgdGhlIHVzZXIgZGVmaW5lZCBwcm9wZXJ0aWVzIHN1Y2ggYXMgZGF0YSwgb3B0aW9ucywgZm9ybWF0cyxcbiAgICAgKiB0aGUgR29vZ2xlQ2hhcnQgb2JqZWN0LCBhbmQgcmVsYXRpdmUgbWV0aG9kcyBmb3IgaW50ZXJuYWwgdXNlLlxuICAgICAqXG4gICAgICogVGhlIEdvb2dsZUNoYXJ0IG9iamVjdCBpcyBhdmFpbGFibGUgYXMgXCIuY2hhcnRcIiBmcm9tIHRoZSByZXR1cm5lZCBMYXZhQ2hhcnQuXG4gICAgICogSXQgY2FuIGJlIHVzZWQgdG8gYWNjZXNzIGFueSBvZiB0aGUgYXZhaWxhYmxlIG1ldGhvZHMgc3VjaCBhc1xuICAgICAqIGdldEltYWdlVVJJKCkgb3IgZ2V0Q2hhcnRMYXlvdXRJbnRlcmZhY2UoKS5cbiAgICAgKiBTZWUgaHR0cHM6Ly9nb29nbGUtZGV2ZWxvcGVycy5hcHBzcG90LmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL2dhbGxlcnkvbGluZWNoYXJ0I21ldGhvZHNcbiAgICAgKiBmb3Igc29tZSBleGFtcGxlcyByZWxhdGl2ZSB0byBMaW5lQ2hhcnRzLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqIEBwYXJhbSAge3N0cmluZ30gICBsYWJlbFxuICAgICAqIEBwYXJhbSAge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgICAqIEB0aHJvd3MgSW52YWxpZExhYmVsXG4gICAgICogQHRocm93cyBJbnZhbGlkQ2FsbGJhY2tcbiAgICAgKiBAdGhyb3dzIFJlbmRlcmFibGVOb3RGb3VuZFxuICAgICAqL1xuICAgIGdldChsYWJlbCwgY2FsbGJhY2spIHtcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgcmVuZGVyYWJsZSA9IHRoaXMuX3JlbmRlcmFibGVzW2xhYmVsXTtcblxuICAgICAgICBpZiAoISByZW5kZXJhYmxlKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgUmVuZGVyYWJsZU5vdEZvdW5kKGxhYmVsKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNhbGxiYWNrKHJlbmRlcmFibGUpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEFkZHMgdG8gdGhlIGxpc3Qgb2YgcGFja2FnZXMgdGhhdCBHb29nbGUgbmVlZHMgdG8gbG9hZC5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtBcnJheX0gcGFja2FnZXNcbiAgICAgKiBAcmV0dXJuIHtBcnJheX1cbiAgICAgKi9cbiAgICBfYWRkUGFja2FnZXMocGFja2FnZXMpIHtcbiAgICAgICAgdGhpcy5fcGFja2FnZXMgPSB0aGlzLl9wYWNrYWdlcy5jb25jYXQocGFja2FnZXMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIExvYWQgdGhlIEdvb2dsZSBTdGF0aWMgTG9hZGVyIGFuZCByZXNvbHZlIHRoZSBwcm9taXNlIHdoZW4gcmVhZHkuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9sb2FkR29vZ2xlKCkge1xuICAgICAgICBjb25zdCAkbGF2YSA9IHRoaXM7XG5cbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBSZXNvbHZpbmcgR29vZ2xlLi4uJyk7XG5cbiAgICAgICAgICAgIGlmICh0aGlzLl9nb29nbGVJc0xvYWRlZCgpKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBTdGF0aWMgbG9hZGVyIGZvdW5kLCBpbml0aWFsaXppbmcgd2luZG93Lmdvb2dsZScpO1xuXG4gICAgICAgICAgICAgICAgJGxhdmEuX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFN0YXRpYyBsb2FkZXIgbm90IGZvdW5kLCBhcHBlbmRpbmcgdG8gaGVhZCcpO1xuXG4gICAgICAgICAgICAgICAgJGxhdmEuX2FkZEdvb2dsZVNjcmlwdFRvSGVhZChyZXNvbHZlKTtcbiAgICAgICAgICAgICAgICAvLyBUaGlzIHdpbGwgY2FsbCAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIENoZWNrIGlmIEdvb2dsZSdzIFN0YXRpYyBMb2FkZXIgaXMgaW4gcGFnZS5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59XG4gICAgICovXG4gICAgX2dvb2dsZUlzTG9hZGVkKCkge1xuICAgICAgICBjb25zdCBzY3JpcHRzID0gZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ3NjcmlwdCcpO1xuXG4gICAgICAgIGZvciAobGV0IHNjcmlwdCBvZiBzY3JpcHRzKSB7XG4gICAgICAgICAgICBpZiAoc2NyaXB0LnNyYyA9PT0gdGhpcy5HT09HTEVfTE9BREVSX1VSTCkge1xuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogUnVucyB0aGUgR29vZ2xlIGNoYXJ0IGxvYWRlciBhbmQgcmVzb2x2ZXMgdGhlIHByb21pc2UuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqIEBwYXJhbSB7UHJvbWlzZS5yZXNvbHZlfSByZXNvbHZlXG4gICAgICovXG4gICAgX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpIHtcbiAgICAgICAgbGV0IGNvbmZpZyA9IHtcbiAgICAgICAgICAgIHBhY2thZ2VzOiB0aGlzLl9wYWNrYWdlcyxcbiAgICAgICAgICAgIGxhbmd1YWdlOiB0aGlzLm9wdGlvbnMubG9jYWxlXG4gICAgICAgIH07XG5cbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5tYXBzX2FwaV9rZXkgIT09ICcnKSB7XG4gICAgICAgICAgICBjb25maWcubWFwc0FwaUtleSA9IHRoaXMub3B0aW9ucy5tYXBzX2FwaV9rZXk7XG4gICAgICAgIH1cblxuICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIExvYWRpbmcgR29vZ2xlIHdpdGggY29uZmlnOicsIGNvbmZpZyk7XG5cbiAgICAgICAgZ29vZ2xlLmNoYXJ0cy5sb2FkKHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OLCBjb25maWcpO1xuXG4gICAgICAgIGdvb2dsZS5jaGFydHMuc2V0T25Mb2FkQ2FsbGJhY2socmVzb2x2ZSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ3JlYXRlIGEgbmV3IHNjcmlwdCB0YWcgZm9yIHRoZSBHb29nbGUgU3RhdGljIExvYWRlci5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICogQHBhcmFtIHtQcm9taXNlLnJlc29sdmV9IHJlc29sdmVcbiAgICAgKiBAcmV0dXJucyB7RWxlbWVudH1cbiAgICAgKi9cbiAgICBfYWRkR29vZ2xlU2NyaXB0VG9IZWFkKHJlc29sdmUpIHtcbiAgICAgICAgbGV0ICRsYXZhID0gdGhpcztcbiAgICAgICAgbGV0IHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NjcmlwdCcpO1xuXG4gICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgIHNjcmlwdC5hc3luYyA9IHRydWU7XG4gICAgICAgIHNjcmlwdC5zcmMgPSB0aGlzLkdPT0dMRV9MT0FERVJfVVJMO1xuICAgICAgICBzY3JpcHQub25sb2FkID0gc2NyaXB0Lm9ucmVhZHlzdGF0ZWNoYW5nZSA9IGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgZXZlbnQgPSBldmVudCB8fCB3aW5kb3cuZXZlbnQ7XG5cbiAgICAgICAgICAgIGlmIChldmVudC50eXBlID09PSAnbG9hZCcgfHwgKC9sb2FkZWR8Y29tcGxldGUvLnRlc3QodGhpcy5yZWFkeVN0YXRlKSkpIHtcbiAgICAgICAgICAgICAgICB0aGlzLm9ubG9hZCA9IHRoaXMub25yZWFkeXN0YXRlY2hhbmdlID0gbnVsbDtcblxuICAgICAgICAgICAgICAgICRsYXZhLl9nb29nbGVDaGFydExvYWRlcihyZXNvbHZlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICBkb2N1bWVudC5oZWFkLmFwcGVuZENoaWxkKHNjcmlwdCk7XG4gICAgfVxufVxuIiwiLyoqXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cbiAqXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBDaGFydFxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHR5cGUgICAgICAtIFR5cGUgb2YgY2hhcnQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBwYWNrYWdlICAgLSBUeXBlIG9mIEdvb2dsZSBjaGFydCBwYWNrYWdlIHRvIGxvYWQuXG4gKiBAcHJvcGVydHkge2Jvb2xlYW59ICBwbmdPdXRwdXQgLSBTaG91bGQgdGhlIGNoYXJ0IGJlIGRpc3BsYXllZCBhcyBhIFBORy5cbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgb3B0aW9ucyAgIC0gQ29uZmlndXJhdGlvbiBvcHRpb25zIGZvciB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBmb3JtYXRzICAgLSBGb3JtYXR0ZXJzIHRvIGFwcGx5IHRvIHRoZSBjaGFydCBkYXRhLlxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgcHJvbWlzZXMgIC0gUHJvbWlzZXMgdXNlZCBpbiB0aGUgcmVuZGVyaW5nIGNoYWluLlxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gaW5pdCAgICAgIC0gSW5pdGlhbGl6ZXMgdGhlIGNoYXJ0LlxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gY29uZmlndXJlIC0gQ29uZmlndXJlcyB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHV1aWQgICAgICAtIENyZWF0ZXMgaWRlbnRpZmljYXRpb24gc3RyaW5nIGZvciB0aGUgY2hhcnQuXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBfZXJyb3JzICAgLSBDb2xsZWN0aW9uIG9mIGVycm9ycyB0byBiZSB0aHJvd24uXG4gKi9cbmltcG9ydCB7IEVsZW1lbnRJZE5vdEZvdW5kIH0gZnJvbSBcIi4vRXJyb3JzLmVzNlwiO1xuaW1wb3J0IHsgZ2V0VHlwZSB9IGZyb20gXCIuL1V0aWxzLmVzNlwiXG5cbi8qKlxuICogQ2hhcnQgbW9kdWxlXG4gKlxuICogQGNsYXNzICAgICBDaGFydFxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xuICogQGxpY2Vuc2UgICBNSVRcbiAqL1xuZXhwb3J0IGNsYXNzIFJlbmRlcmFibGVcbntcbiAgICAvKipcbiAgICAgKiBDaGFydCBDbGFzc1xuICAgICAqXG4gICAgICogVGhpcyBpcyB0aGUgamF2YXNjcmlwdCB2ZXJzaW9uIG9mIGEgbGF2YWNoYXJ0IHdpdGggbWV0aG9kcyBmb3IgaW50ZXJhY3Rpbmcgd2l0aFxuICAgICAqIHRoZSBnb29nbGUgY2hhcnQgYW5kIHRoZSBQSFAgbGF2YWNoYXJ0IG91dHB1dC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBqc29uXG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgY29uc3RydWN0b3IoanNvbikge1xuICAgICAgICB0aGlzLmdjaGFydCAgICA9IG51bGw7XG4gICAgICAgIHRoaXMubGFiZWwgICAgID0ganNvbi5sYWJlbDtcbiAgICAgICAgdGhpcy5vcHRpb25zICAgPSBqc29uLm9wdGlvbnM7XG4gICAgICAgIHRoaXMuZWxlbWVudElkID0ganNvbi5lbGVtZW50SWQ7XG5cbiAgICAgICAgdGhpcy5lbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQodGhpcy5lbGVtZW50SWQpO1xuXG4gICAgICAgIGlmICghIHRoaXMuZWxlbWVudCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVsZW1lbnRJZE5vdEZvdW5kKHRoaXMuZWxlbWVudElkKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgQ2hhcnQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAgICovXG4gICAgdXVpZCgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMudHlwZSsnOjonK3RoaXMubGFiZWw7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IHdpdGggdGhlIHByZXNldCBkYXRhIGFuZCBvcHRpb25zLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGRyYXcoKSB7XG4gICAgICAgIHRoaXMuZ2NoYXJ0LmRyYXcodGhpcy5kYXRhLCB0aGlzLm9wdGlvbnMpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNldHMgdGhlIGRhdGEgZm9yIHRoZSBjaGFydCBieSBjcmVhdGluZyBhIG5ldyBEYXRhVGFibGVcbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAZXh0ZXJuYWwgXCJnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGVcIlxuICAgICAqIEBzZWUgICB7QGxpbmsgaHR0cHM6Ly9kZXZlbG9wZXJzLmdvb2dsZS5jb20vY2hhcnQvaW50ZXJhY3RpdmUvZG9jcy9yZWZlcmVuY2UjRGF0YVRhYmxlfERhdGFUYWJsZSBDbGFzc31cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gcGF5bG9hZCBKc29uIHJlcHJlc2VudGF0aW9uIG9mIGEgRGF0YVRhYmxlXG4gICAgICovXG4gICAgc2V0RGF0YShwYXlsb2FkKSB7XG4gICAgICAgIC8vIElmIHRoZSBwYXlsb2FkIGlzIGZyb20gSm9pbmVkRGF0YVRhYmxlOjp0b0pzb24oKSwgdGhlbiBjcmVhdGVcbiAgICAgICAgLy8gdHdvIG5ldyBEYXRhVGFibGVzIGFuZCBqb2luIHRoZW0gd2l0aCB0aGUgZGVmaW5lZCBvcHRpb25zLlxuICAgICAgICBpZiAoZ2V0VHlwZShwYXlsb2FkLmRhdGEpID09PSAnQXJyYXknKSB7XG4gICAgICAgICAgICB0aGlzLmRhdGEgPSBnb29nbGUudmlzdWFsaXphdGlvbi5kYXRhLmpvaW4oXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMF0pLFxuICAgICAgICAgICAgICAgIG5ldyBnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGUocGF5bG9hZC5kYXRhWzFdKSxcbiAgICAgICAgICAgICAgICBwYXlsb2FkLmtleXMsXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5qb2luTWV0aG9kLFxuICAgICAgICAgICAgICAgIHBheWxvYWQuZHQyQ29sdW1ucyxcbiAgICAgICAgICAgICAgICBwYXlsb2FkLmR0MkNvbHVtbnNcbiAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFNpbmNlIEdvb2dsZSBjb21waWxlcyB0aGVpciBjbGFzc2VzLCB3ZSBjYW4ndCB1c2UgaW5zdGFuY2VvZiB0byBjaGVjayBzaW5jZVxuICAgICAgICAvLyBpdCBpcyBubyBsb25nZXIgY2FsbGVkIGEgXCJEYXRhVGFibGVcIiAoaXQncyBcImd2anNfUFwiIGJ1dCB0aGF0IGNvdWxkIGNoYW5nZS4uLilcbiAgICAgICAgaWYgKGdldFR5cGUocGF5bG9hZC5nZXRUYWJsZVByb3BlcnRpZXMpID09PSAnRnVuY3Rpb24nKSB7XG4gICAgICAgICAgICB0aGlzLmRhdGEgPSBwYXlsb2FkO1xuXG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBJZiBhIERhdGFUYWJsZSN0b0pzb24oKSBwYXlsb2FkIGlzIHJlY2VpdmVkLCB3aXRoIGZvcm1hdHRlZCBjb2x1bW5zLFxuICAgICAgICAvLyB0aGVuIHBheWxvYWQuZGF0YSB3aWxsIGJlIGRlZmluZWQsIGFuZCB1c2VkIGFzIHRoZSBEYXRhVGFibGVcbiAgICAgICAgaWYgKGdldFR5cGUocGF5bG9hZC5kYXRhKSA9PT0gJ09iamVjdCcpIHtcbiAgICAgICAgICAgIHBheWxvYWQgPSBwYXlsb2FkLmRhdGE7XG4gICAgICAgIH1cbiAgICAgICAgLy8gVE9ETzogaGFuZGxlIGZvcm1hdHMgYmV0dGVyLi4uXG5cbiAgICAgICAgLy8gSWYgd2UgcmVhY2ggaGVyZSwgdGhlbiBpdCBtdXN0IGJlIHN0YW5kYXJkIEpTT04gZm9yIGNyZWF0aW5nIGEgRGF0YVRhYmxlLlxuICAgICAgICB0aGlzLmRhdGEgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlKHBheWxvYWQpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFNldHMgdGhlIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9uc1xuICAgICAqL1xuICAgIHNldE9wdGlvbnMob3B0aW9ucykge1xuICAgICAgICB0aGlzLm9wdGlvbnMgPSBvcHRpb25zO1xuICAgIH1cbn1cbiIsIi8qIGpzaGludCB1bmRlZjogdHJ1ZSwgdW51c2VkOiB0cnVlICovXG4vKiBnbG9iYWxzIGRvY3VtZW50ICovXG5cbi8qKlxuICogRnVuY3Rpb24gdGhhdCBkb2VzIG5vdGhpbmcuXG4gKlxuICogQHJldHVybiB7dW5kZWZpbmVkfVxuICovXG5leHBvcnQgZnVuY3Rpb24gbm9vcCgpIHtcbiAgICByZXR1cm4gdW5kZWZpbmVkO1xufVxuXG4vKipcbiAqIFJldHVybiB0aGUgdHlwZSBvZiBvYmplY3QuXG4gKlxuICogQHBhcmFtIHtvYmplY3R9IG9iamVjdFxuICogQHJldHVybiB7bWl4ZWR9XG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBnZXRUeXBlKG9iamVjdCkge1xuICAgIGxldCB0eXBlID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKG9iamVjdCk7XG5cbiAgICByZXR1cm4gdHlwZS5yZXBsYWNlKCdbb2JqZWN0ICcsJycpLnJlcGxhY2UoJ10nLCcnKTtcbn1cblxuLyoqXG4gKiBTaW1wbGUgUHJvbWlzZSBmb3IgdGhlIERPTSB0byBiZSByZWFkeS5cbiAqXG4gKiBAcmV0dXJuIHtQcm9taXNlfVxuICovXG5leHBvcnQgZnVuY3Rpb24gZG9tTG9hZGVkKCkge1xuICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcbiAgICAgICAgaWYgKGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdpbnRlcmFjdGl2ZScgfHwgZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gJ2NvbXBsZXRlJykge1xuICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIHJlc29sdmUpO1xuICAgICAgICB9XG4gICAgfSk7XG59XG5cbi8qKlxuICogTWV0aG9kIGZvciBhdHRhY2hpbmcgZXZlbnRzIHRvIG9iamVjdHMuXG4gKlxuICogQ3JlZGl0IHRvIEFsZXggVi5cbiAqXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzMyNzkzNC9hbGV4LXZcbiAqIEBsaW5rIGh0dHA6Ly9zdGFja292ZXJmbG93LmNvbS9hLzMxNTAxMzlcbiAqIEBwYXJhbSB7b2JqZWN0fSB0YXJnZXRcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICogQHBhcmFtIHtib29sfSBldmVudFJldHVyblxuICovXG5leHBvcnQgZnVuY3Rpb24gYWRkRXZlbnQodGFyZ2V0LCB0eXBlLCBjYWxsYmFjaywgZXZlbnRSZXR1cm4pXG57XG4gICAgaWYgKHRhcmdldCA9PT0gbnVsbCB8fCB0eXBlb2YgdGFyZ2V0ID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKHRhcmdldC5hZGRFdmVudExpc3RlbmVyKSB7XG4gICAgICAgIHRhcmdldC5hZGRFdmVudExpc3RlbmVyKHR5cGUsIGNhbGxiYWNrLCAhIWV2ZW50UmV0dXJuKTtcbiAgICB9XG4gICAgZWxzZSBpZih0YXJnZXQuYXR0YWNoRXZlbnQpIHtcbiAgICAgICAgdGFyZ2V0LmF0dGFjaEV2ZW50KFwib25cIiArIHR5cGUsIGNhbGxiYWNrKTtcbiAgICB9XG4gICAgZWxzZSB7XG4gICAgICAgIHRhcmdldFtcIm9uXCIgKyB0eXBlXSA9IGNhbGxiYWNrO1xuICAgIH1cbn1cblxuLyoqXG4gKiBHZXQgYSBmdW5jdGlvbiBhIGJ5IGl0cycgbmFtZXNwYWNlZCBzdHJpbmcgbmFtZSB3aXRoIGNvbnRleHQuXG4gKlxuICogQ3JlZGl0IHRvIEphc29uIEJ1bnRpbmdcbiAqXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzE3OTAvamFzb24tYnVudGluZ1xuICogQGxpbmsgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS9hLzM1OTkxMFxuICogQHBhcmFtIHtzdHJpbmd9IGZ1bmN0aW9uTmFtZVxuICogQHBhcmFtIHtvYmplY3R9IGNvbnRleHRcbiAqIEBwcml2YXRlXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBzdHJpbmdUb0Z1bmN0aW9uKGZ1bmN0aW9uTmFtZSwgY29udGV4dCkge1xuICAgIGxldCBuYW1lc3BhY2VzID0gZnVuY3Rpb25OYW1lLnNwbGl0KCcuJyk7XG4gICAgbGV0IGZ1bmMgPSBuYW1lc3BhY2VzLnBvcCgpO1xuXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBuYW1lc3BhY2VzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIGNvbnRleHQgPSBjb250ZXh0W25hbWVzcGFjZXNbaV1dO1xuICAgIH1cblxuICAgIHJldHVybiBjb250ZXh0W2Z1bmNdO1xufVxuIl19
