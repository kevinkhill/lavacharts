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

var _Lava2 = _interopRequireDefault(_Lava);

var _Utils = require('./lava/Utils.es6');

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

},{"./lava/Lava.es6":38,"./lava/Utils.es6":41}],35:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

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

},{"./Renderable.es6":40,"./Utils.es6":41,"lodash/forIn":21}],36:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

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

},{"./Renderable.es6":40,"./Utils.es6":41}],37:[function(require,module,exports){
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

var _Chart = require('./Chart.es6');

var _Chart2 = _interopRequireDefault(_Chart);

var _Dashboard = require('./Dashboard.es6');

var _Dashboard2 = _interopRequireDefault(_Dashboard);

var _Options = require('./Options.js');

var _Options2 = _interopRequireDefault(_Options);

var _Utils = require('./Utils.es6');

var _Errors = require('./Errors.es6');

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

},{"./Chart.es6":35,"./Dashboard.es6":36,"./Errors.es6":37,"./Options.js":39,"./Utils.es6":41,"events":1,"lodash/forIn":21}],39:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});
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

},{"./Errors.es6":37,"./Utils.es6":41}],41:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJub2RlX21vZHVsZXMvZXZlbnRzL2V2ZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX1N5bWJvbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2FycmF5TGlrZUtleXMuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlRm9yLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZUdldFRhZy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc1R5cGVkQXJyYXkuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlS2V5c0luLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVRpbWVzLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVVuYXJ5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY2FzdEZ1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY3JlYXRlQmFzZUZvci5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2ZyZWVHbG9iYWwuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19nZXRSYXdUYWcuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19pc0luZGV4LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9faXNQcm90b3R5cGUuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19uYXRpdmVLZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19ub2RlVXRpbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX29iamVjdFRvU3RyaW5nLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fcm9vdC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvZm9ySW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lkZW50aXR5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheUxpa2UuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzQnVmZmVyLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0Z1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0xlbmd0aC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNPYmplY3QuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzT2JqZWN0TGlrZS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNUeXBlZEFycmF5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9rZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL3N0dWJGYWxzZS5qcyIsInNyY1xcbGF2YS5lbnRyeS5lczYiLCJzcmNcXGxhdmFcXENoYXJ0LmVzNiIsInNyY1xcbGF2YVxcRGFzaGJvYXJkLmVzNiIsInNyY1xcbGF2YVxcRXJyb3JzLmVzNiIsInNyY1xcbGF2YVxcTGF2YS5lczYiLCJzcmNcXGxhdmFcXE9wdGlvbnMuanMiLCJzcmNcXGxhdmFcXFJlbmRlcmFibGUuZXM2Iiwic3JjXFxsYXZhXFxVdGlscy5lczYiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDOVNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ05BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDaEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDNUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2xCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM1REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNkQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUN6QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7OztBQ0pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDOUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2xCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDcEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ1RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3ZDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNyQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDcENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMxQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNyQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ25DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQy9CQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDN0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzNCQTtB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QUFYOztBQUVBLFNBQUssSUFBSSxJQUFJLENBQWIsRUFBZ0IsSUFBSSxXQUFXLE1BQS9CLEVBQXVDLEdBQXZDLEVBQTRDO0FBQ3hDLGtCQUFVLFFBQVEsV0FBVyxDQUFYLENBQVIsQ0FBVjtBQUNIOztBQUVELFdBQU8sUUFBUSxJQUFSLENBQVA7QUFDSCIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICB0aGlzLl9ldmVudHMgPSB0aGlzLl9ldmVudHMgfHwge307XG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbkV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uKG4pIHtcbiAgaWYgKCFpc051bWJlcihuKSB8fCBuIDwgMCB8fCBpc05hTihuKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ24gbXVzdCBiZSBhIHBvc2l0aXZlIG51bWJlcicpO1xuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIGVyLCBoYW5kbGVyLCBsZW4sIGFyZ3MsIGksIGxpc3RlbmVycztcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmICh0eXBlID09PSAnZXJyb3InKSB7XG4gICAgaWYgKCF0aGlzLl9ldmVudHMuZXJyb3IgfHxcbiAgICAgICAgKGlzT2JqZWN0KHRoaXMuX2V2ZW50cy5lcnJvcikgJiYgIXRoaXMuX2V2ZW50cy5lcnJvci5sZW5ndGgpKSB7XG4gICAgICBlciA9IGFyZ3VtZW50c1sxXTtcbiAgICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgICAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmNhdWdodCwgdW5zcGVjaWZpZWQgXCJlcnJvclwiIGV2ZW50LiAoJyArIGVyICsgJyknKTtcbiAgICAgICAgZXJyLmNvbnRleHQgPSBlcjtcbiAgICAgICAgdGhyb3cgZXJyO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIGhhbmRsZXIgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgaWYgKGlzVW5kZWZpbmVkKGhhbmRsZXIpKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAoaXNGdW5jdGlvbihoYW5kbGVyKSkge1xuICAgIHN3aXRjaCAoYXJndW1lbnRzLmxlbmd0aCkge1xuICAgICAgLy8gZmFzdCBjYXNlc1xuICAgICAgY2FzZSAxOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcyk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAyOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcywgYXJndW1lbnRzWzFdKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIDM6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzLCBhcmd1bWVudHNbMV0sIGFyZ3VtZW50c1syXSk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgLy8gc2xvd2VyXG4gICAgICBkZWZhdWx0OlxuICAgICAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICAgICAgaGFuZGxlci5hcHBseSh0aGlzLCBhcmdzKTtcbiAgICB9XG4gIH0gZWxzZSBpZiAoaXNPYmplY3QoaGFuZGxlcikpIHtcbiAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICBsaXN0ZW5lcnMgPSBoYW5kbGVyLnNsaWNlKCk7XG4gICAgbGVuID0gbGlzdGVuZXJzLmxlbmd0aDtcbiAgICBmb3IgKGkgPSAwOyBpIDwgbGVuOyBpKyspXG4gICAgICBsaXN0ZW5lcnNbaV0uYXBwbHkodGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgbTtcblxuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgdGhpcy5fZXZlbnRzID0ge307XG5cbiAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICBpZiAodGhpcy5fZXZlbnRzLm5ld0xpc3RlbmVyKVxuICAgIHRoaXMuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICBpc0Z1bmN0aW9uKGxpc3RlbmVyLmxpc3RlbmVyKSA/XG4gICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICBlbHNlIGlmIChpc09iamVjdCh0aGlzLl9ldmVudHNbdHlwZV0pKVxuICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0ucHVzaChsaXN0ZW5lcik7XG4gIGVsc2VcbiAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0gPSBbdGhpcy5fZXZlbnRzW3R5cGVdLCBsaXN0ZW5lcl07XG5cbiAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgaWYgKGlzT2JqZWN0KHRoaXMuX2V2ZW50c1t0eXBlXSkgJiYgIXRoaXMuX2V2ZW50c1t0eXBlXS53YXJuZWQpIHtcbiAgICBpZiAoIWlzVW5kZWZpbmVkKHRoaXMuX21heExpc3RlbmVycykpIHtcbiAgICAgIG0gPSB0aGlzLl9tYXhMaXN0ZW5lcnM7XG4gICAgfSBlbHNlIHtcbiAgICAgIG0gPSBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgICB9XG5cbiAgICBpZiAobSAmJiBtID4gMCAmJiB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoID4gbSkge1xuICAgICAgdGhpcy5fZXZlbnRzW3R5cGVdLndhcm5lZCA9IHRydWU7XG4gICAgICBjb25zb2xlLmVycm9yKCcobm9kZSkgd2FybmluZzogcG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSAnICtcbiAgICAgICAgICAgICAgICAgICAgJ2xlYWsgZGV0ZWN0ZWQuICVkIGxpc3RlbmVycyBhZGRlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICdVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byBpbmNyZWFzZSBsaW1pdC4nLFxuICAgICAgICAgICAgICAgICAgICB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoKTtcbiAgICAgIGlmICh0eXBlb2YgY29uc29sZS50cmFjZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAvLyBub3Qgc3VwcG9ydGVkIGluIElFIDEwXG4gICAgICAgIGNvbnNvbGUudHJhY2UoKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgdmFyIGZpcmVkID0gZmFsc2U7XG5cbiAgZnVuY3Rpb24gZygpIHtcbiAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGcpO1xuXG4gICAgaWYgKCFmaXJlZCkge1xuICAgICAgZmlyZWQgPSB0cnVlO1xuICAgICAgbGlzdGVuZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH1cblxuICBnLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHRoaXMub24odHlwZSwgZyk7XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG4vLyBlbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWZmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZFxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBsaXN0LCBwb3NpdGlvbiwgbGVuZ3RoLCBpO1xuXG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldHVybiB0aGlzO1xuXG4gIGxpc3QgPSB0aGlzLl9ldmVudHNbdHlwZV07XG4gIGxlbmd0aCA9IGxpc3QubGVuZ3RoO1xuICBwb3NpdGlvbiA9IC0xO1xuXG4gIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fFxuICAgICAgKGlzRnVuY3Rpb24obGlzdC5saXN0ZW5lcikgJiYgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICBpZiAodGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3RlbmVyKTtcblxuICB9IGVsc2UgaWYgKGlzT2JqZWN0KGxpc3QpKSB7XG4gICAgZm9yIChpID0gbGVuZ3RoOyBpLS0gPiAwOykge1xuICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8XG4gICAgICAgICAgKGxpc3RbaV0ubGlzdGVuZXIgJiYgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKSB7XG4gICAgICBsaXN0Lmxlbmd0aCA9IDA7XG4gICAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIH0gZWxzZSB7XG4gICAgICBsaXN0LnNwbGljZShwb3NpdGlvbiwgMSk7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0ZW5lcik7XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIga2V5LCBsaXN0ZW5lcnM7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgcmV0dXJuIHRoaXM7XG5cbiAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICBpZiAoIXRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcikge1xuICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKVxuICAgICAgdGhpcy5fZXZlbnRzID0ge307XG4gICAgZWxzZSBpZiAodGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgIGZvciAoa2V5IGluIHRoaXMuX2V2ZW50cykge1xuICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgIH1cbiAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGxpc3RlbmVycyA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICBpZiAoaXNGdW5jdGlvbihsaXN0ZW5lcnMpKSB7XG4gICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICB9IGVsc2UgaWYgKGxpc3RlbmVycykge1xuICAgIC8vIExJRk8gb3JkZXJcbiAgICB3aGlsZSAobGlzdGVuZXJzLmxlbmd0aClcbiAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2xpc3RlbmVycy5sZW5ndGggLSAxXSk7XG4gIH1cbiAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIgcmV0O1xuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldCA9IFtdO1xuICBlbHNlIGlmIChpc0Z1bmN0aW9uKHRoaXMuX2V2ZW50c1t0eXBlXSkpXG4gICAgcmV0ID0gW3RoaXMuX2V2ZW50c1t0eXBlXV07XG4gIGVsc2VcbiAgICByZXQgPSB0aGlzLl9ldmVudHNbdHlwZV0uc2xpY2UoKTtcbiAgcmV0dXJuIHJldDtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgaWYgKHRoaXMuX2V2ZW50cykge1xuICAgIHZhciBldmxpc3RlbmVyID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKGlzRnVuY3Rpb24oZXZsaXN0ZW5lcikpXG4gICAgICByZXR1cm4gMTtcbiAgICBlbHNlIGlmIChldmxpc3RlbmVyKVxuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICB9XG4gIHJldHVybiAwO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG59O1xuXG5mdW5jdGlvbiBpc0Z1bmN0aW9uKGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ2Z1bmN0aW9uJztcbn1cblxuZnVuY3Rpb24gaXNOdW1iZXIoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnbnVtYmVyJztcbn1cblxuZnVuY3Rpb24gaXNPYmplY3QoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnb2JqZWN0JyAmJiBhcmcgIT09IG51bGw7XG59XG5cbmZ1bmN0aW9uIGlzVW5kZWZpbmVkKGFyZykge1xuICByZXR1cm4gYXJnID09PSB2b2lkIDA7XG59XG4iLCJ2YXIgcm9vdCA9IHJlcXVpcmUoJy4vX3Jvb3QnKTtcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgU3ltYm9sID0gcm9vdC5TeW1ib2w7XG5cbm1vZHVsZS5leHBvcnRzID0gU3ltYm9sO1xuIiwidmFyIGJhc2VUaW1lcyA9IHJlcXVpcmUoJy4vX2Jhc2VUaW1lcycpLFxuICAgIGlzQXJndW1lbnRzID0gcmVxdWlyZSgnLi9pc0FyZ3VtZW50cycpLFxuICAgIGlzQXJyYXkgPSByZXF1aXJlKCcuL2lzQXJyYXknKSxcbiAgICBpc0J1ZmZlciA9IHJlcXVpcmUoJy4vaXNCdWZmZXInKSxcbiAgICBpc0luZGV4ID0gcmVxdWlyZSgnLi9faXNJbmRleCcpLFxuICAgIGlzVHlwZWRBcnJheSA9IHJlcXVpcmUoJy4vaXNUeXBlZEFycmF5Jyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKlxuICogQ3JlYXRlcyBhbiBhcnJheSBvZiB0aGUgZW51bWVyYWJsZSBwcm9wZXJ0eSBuYW1lcyBvZiB0aGUgYXJyYXktbGlrZSBgdmFsdWVgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBxdWVyeS5cbiAqIEBwYXJhbSB7Ym9vbGVhbn0gaW5oZXJpdGVkIFNwZWNpZnkgcmV0dXJuaW5nIGluaGVyaXRlZCBwcm9wZXJ0eSBuYW1lcy5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKi9cbmZ1bmN0aW9uIGFycmF5TGlrZUtleXModmFsdWUsIGluaGVyaXRlZCkge1xuICB2YXIgaXNBcnIgPSBpc0FycmF5KHZhbHVlKSxcbiAgICAgIGlzQXJnID0gIWlzQXJyICYmIGlzQXJndW1lbnRzKHZhbHVlKSxcbiAgICAgIGlzQnVmZiA9ICFpc0FyciAmJiAhaXNBcmcgJiYgaXNCdWZmZXIodmFsdWUpLFxuICAgICAgaXNUeXBlID0gIWlzQXJyICYmICFpc0FyZyAmJiAhaXNCdWZmICYmIGlzVHlwZWRBcnJheSh2YWx1ZSksXG4gICAgICBza2lwSW5kZXhlcyA9IGlzQXJyIHx8IGlzQXJnIHx8IGlzQnVmZiB8fCBpc1R5cGUsXG4gICAgICByZXN1bHQgPSBza2lwSW5kZXhlcyA/IGJhc2VUaW1lcyh2YWx1ZS5sZW5ndGgsIFN0cmluZykgOiBbXSxcbiAgICAgIGxlbmd0aCA9IHJlc3VsdC5sZW5ndGg7XG5cbiAgZm9yICh2YXIga2V5IGluIHZhbHVlKSB7XG4gICAgaWYgKChpbmhlcml0ZWQgfHwgaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwga2V5KSkgJiZcbiAgICAgICAgIShza2lwSW5kZXhlcyAmJiAoXG4gICAgICAgICAgIC8vIFNhZmFyaSA5IGhhcyBlbnVtZXJhYmxlIGBhcmd1bWVudHMubGVuZ3RoYCBpbiBzdHJpY3QgbW9kZS5cbiAgICAgICAgICAga2V5ID09ICdsZW5ndGgnIHx8XG4gICAgICAgICAgIC8vIE5vZGUuanMgMC4xMCBoYXMgZW51bWVyYWJsZSBub24taW5kZXggcHJvcGVydGllcyBvbiBidWZmZXJzLlxuICAgICAgICAgICAoaXNCdWZmICYmIChrZXkgPT0gJ29mZnNldCcgfHwga2V5ID09ICdwYXJlbnQnKSkgfHxcbiAgICAgICAgICAgLy8gUGhhbnRvbUpTIDIgaGFzIGVudW1lcmFibGUgbm9uLWluZGV4IHByb3BlcnRpZXMgb24gdHlwZWQgYXJyYXlzLlxuICAgICAgICAgICAoaXNUeXBlICYmIChrZXkgPT0gJ2J1ZmZlcicgfHwga2V5ID09ICdieXRlTGVuZ3RoJyB8fCBrZXkgPT0gJ2J5dGVPZmZzZXQnKSkgfHxcbiAgICAgICAgICAgLy8gU2tpcCBpbmRleCBwcm9wZXJ0aWVzLlxuICAgICAgICAgICBpc0luZGV4KGtleSwgbGVuZ3RoKVxuICAgICAgICApKSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBhcnJheUxpa2VLZXlzO1xuIiwidmFyIGNyZWF0ZUJhc2VGb3IgPSByZXF1aXJlKCcuL19jcmVhdGVCYXNlRm9yJyk7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYGJhc2VGb3JPd25gIHdoaWNoIGl0ZXJhdGVzIG92ZXIgYG9iamVjdGBcbiAqIHByb3BlcnRpZXMgcmV0dXJuZWQgYnkgYGtleXNGdW5jYCBhbmQgaW52b2tlcyBgaXRlcmF0ZWVgIGZvciBlYWNoIHByb3BlcnR5LlxuICogSXRlcmF0ZWUgZnVuY3Rpb25zIG1heSBleGl0IGl0ZXJhdGlvbiBlYXJseSBieSBleHBsaWNpdGx5IHJldHVybmluZyBgZmFsc2VgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gaXRlcmF0ZSBvdmVyLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IGtleXNGdW5jIFRoZSBmdW5jdGlvbiB0byBnZXQgdGhlIGtleXMgb2YgYG9iamVjdGAuXG4gKiBAcmV0dXJucyB7T2JqZWN0fSBSZXR1cm5zIGBvYmplY3RgLlxuICovXG52YXIgYmFzZUZvciA9IGNyZWF0ZUJhc2VGb3IoKTtcblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlRm9yO1xuIiwidmFyIFN5bWJvbCA9IHJlcXVpcmUoJy4vX1N5bWJvbCcpLFxuICAgIGdldFJhd1RhZyA9IHJlcXVpcmUoJy4vX2dldFJhd1RhZycpLFxuICAgIG9iamVjdFRvU3RyaW5nID0gcmVxdWlyZSgnLi9fb2JqZWN0VG9TdHJpbmcnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIG51bGxUYWcgPSAnW29iamVjdCBOdWxsXScsXG4gICAgdW5kZWZpbmVkVGFnID0gJ1tvYmplY3QgVW5kZWZpbmVkXSc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBnZXRUYWdgIHdpdGhvdXQgZmFsbGJhY2tzIGZvciBidWdneSBlbnZpcm9ubWVudHMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgYHRvU3RyaW5nVGFnYC5cbiAqL1xuZnVuY3Rpb24gYmFzZUdldFRhZyh2YWx1ZSkge1xuICBpZiAodmFsdWUgPT0gbnVsbCkge1xuICAgIHJldHVybiB2YWx1ZSA9PT0gdW5kZWZpbmVkID8gdW5kZWZpbmVkVGFnIDogbnVsbFRhZztcbiAgfVxuICByZXR1cm4gKHN5bVRvU3RyaW5nVGFnICYmIHN5bVRvU3RyaW5nVGFnIGluIE9iamVjdCh2YWx1ZSkpXG4gICAgPyBnZXRSYXdUYWcodmFsdWUpXG4gICAgOiBvYmplY3RUb1N0cmluZyh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUdldFRhZztcbiIsInZhciBiYXNlR2V0VGFnID0gcmVxdWlyZSgnLi9fYmFzZUdldFRhZycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhcmdzVGFnID0gJ1tvYmplY3QgQXJndW1lbnRzXSc7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNBcmd1bWVudHNgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGBhcmd1bWVudHNgIG9iamVjdCxcbiAqL1xuZnVuY3Rpb24gYmFzZUlzQXJndW1lbnRzKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGJhc2VHZXRUYWcodmFsdWUpID09IGFyZ3NUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUlzQXJndW1lbnRzO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyksXG4gICAgaXNPYmplY3RMaWtlID0gcmVxdWlyZSgnLi9pc09iamVjdExpa2UnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIGFyZ3NUYWcgPSAnW29iamVjdCBBcmd1bWVudHNdJyxcbiAgICBhcnJheVRhZyA9ICdbb2JqZWN0IEFycmF5XScsXG4gICAgYm9vbFRhZyA9ICdbb2JqZWN0IEJvb2xlYW5dJyxcbiAgICBkYXRlVGFnID0gJ1tvYmplY3QgRGF0ZV0nLFxuICAgIGVycm9yVGFnID0gJ1tvYmplY3QgRXJyb3JdJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBtYXBUYWcgPSAnW29iamVjdCBNYXBdJyxcbiAgICBudW1iZXJUYWcgPSAnW29iamVjdCBOdW1iZXJdJyxcbiAgICBvYmplY3RUYWcgPSAnW29iamVjdCBPYmplY3RdJyxcbiAgICByZWdleHBUYWcgPSAnW29iamVjdCBSZWdFeHBdJyxcbiAgICBzZXRUYWcgPSAnW29iamVjdCBTZXRdJyxcbiAgICBzdHJpbmdUYWcgPSAnW29iamVjdCBTdHJpbmddJyxcbiAgICB3ZWFrTWFwVGFnID0gJ1tvYmplY3QgV2Vha01hcF0nO1xuXG52YXIgYXJyYXlCdWZmZXJUYWcgPSAnW29iamVjdCBBcnJheUJ1ZmZlcl0nLFxuICAgIGRhdGFWaWV3VGFnID0gJ1tvYmplY3QgRGF0YVZpZXddJyxcbiAgICBmbG9hdDMyVGFnID0gJ1tvYmplY3QgRmxvYXQzMkFycmF5XScsXG4gICAgZmxvYXQ2NFRhZyA9ICdbb2JqZWN0IEZsb2F0NjRBcnJheV0nLFxuICAgIGludDhUYWcgPSAnW29iamVjdCBJbnQ4QXJyYXldJyxcbiAgICBpbnQxNlRhZyA9ICdbb2JqZWN0IEludDE2QXJyYXldJyxcbiAgICBpbnQzMlRhZyA9ICdbb2JqZWN0IEludDMyQXJyYXldJyxcbiAgICB1aW50OFRhZyA9ICdbb2JqZWN0IFVpbnQ4QXJyYXldJyxcbiAgICB1aW50OENsYW1wZWRUYWcgPSAnW29iamVjdCBVaW50OENsYW1wZWRBcnJheV0nLFxuICAgIHVpbnQxNlRhZyA9ICdbb2JqZWN0IFVpbnQxNkFycmF5XScsXG4gICAgdWludDMyVGFnID0gJ1tvYmplY3QgVWludDMyQXJyYXldJztcblxuLyoqIFVzZWQgdG8gaWRlbnRpZnkgYHRvU3RyaW5nVGFnYCB2YWx1ZXMgb2YgdHlwZWQgYXJyYXlzLiAqL1xudmFyIHR5cGVkQXJyYXlUYWdzID0ge307XG50eXBlZEFycmF5VGFnc1tmbG9hdDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Zsb2F0NjRUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDhUYWddID0gdHlwZWRBcnJheVRhZ3NbaW50MTZUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW3VpbnQ4VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50OENsYW1wZWRUYWddID0gdHlwZWRBcnJheVRhZ3NbdWludDE2VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50MzJUYWddID0gdHJ1ZTtcbnR5cGVkQXJyYXlUYWdzW2FyZ3NUYWddID0gdHlwZWRBcnJheVRhZ3NbYXJyYXlUYWddID1cbnR5cGVkQXJyYXlUYWdzW2FycmF5QnVmZmVyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Jvb2xUYWddID1cbnR5cGVkQXJyYXlUYWdzW2RhdGFWaWV3VGFnXSA9IHR5cGVkQXJyYXlUYWdzW2RhdGVUYWddID1cbnR5cGVkQXJyYXlUYWdzW2Vycm9yVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Z1bmNUYWddID1cbnR5cGVkQXJyYXlUYWdzW21hcFRhZ10gPSB0eXBlZEFycmF5VGFnc1tudW1iZXJUYWddID1cbnR5cGVkQXJyYXlUYWdzW29iamVjdFRhZ10gPSB0eXBlZEFycmF5VGFnc1tyZWdleHBUYWddID1cbnR5cGVkQXJyYXlUYWdzW3NldFRhZ10gPSB0eXBlZEFycmF5VGFnc1tzdHJpbmdUYWddID1cbnR5cGVkQXJyYXlUYWdzW3dlYWtNYXBUYWddID0gZmFsc2U7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNUeXBlZEFycmF5YCB3aXRob3V0IE5vZGUuanMgb3B0aW1pemF0aW9ucy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHR5cGVkIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGJhc2VJc1R5cGVkQXJyYXkodmFsdWUpIHtcbiAgcmV0dXJuIGlzT2JqZWN0TGlrZSh2YWx1ZSkgJiZcbiAgICBpc0xlbmd0aCh2YWx1ZS5sZW5ndGgpICYmICEhdHlwZWRBcnJheVRhZ3NbYmFzZUdldFRhZyh2YWx1ZSldO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VJc1R5cGVkQXJyYXk7XG4iLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0JyksXG4gICAgaXNQcm90b3R5cGUgPSByZXF1aXJlKCcuL19pc1Byb3RvdHlwZScpLFxuICAgIG5hdGl2ZUtleXNJbiA9IHJlcXVpcmUoJy4vX25hdGl2ZUtleXNJbicpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLmtleXNJbmAgd2hpY2ggZG9lc24ndCB0cmVhdCBzcGFyc2UgYXJyYXlzIGFzIGRlbnNlLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICovXG5mdW5jdGlvbiBiYXNlS2V5c0luKG9iamVjdCkge1xuICBpZiAoIWlzT2JqZWN0KG9iamVjdCkpIHtcbiAgICByZXR1cm4gbmF0aXZlS2V5c0luKG9iamVjdCk7XG4gIH1cbiAgdmFyIGlzUHJvdG8gPSBpc1Byb3RvdHlwZShvYmplY3QpLFxuICAgICAgcmVzdWx0ID0gW107XG5cbiAgZm9yICh2YXIga2V5IGluIG9iamVjdCkge1xuICAgIGlmICghKGtleSA9PSAnY29uc3RydWN0b3InICYmIChpc1Byb3RvIHx8ICFoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwga2V5KSkpKSB7XG4gICAgICByZXN1bHQucHVzaChrZXkpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VLZXlzSW47XG4iLCIvKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLnRpbWVzYCB3aXRob3V0IHN1cHBvcnQgZm9yIGl0ZXJhdGVlIHNob3J0aGFuZHNcbiAqIG9yIG1heCBhcnJheSBsZW5ndGggY2hlY2tzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge251bWJlcn0gbiBUaGUgbnVtYmVyIG9mIHRpbWVzIHRvIGludm9rZSBgaXRlcmF0ZWVgLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcmVzdWx0cy5cbiAqL1xuZnVuY3Rpb24gYmFzZVRpbWVzKG4sIGl0ZXJhdGVlKSB7XG4gIHZhciBpbmRleCA9IC0xLFxuICAgICAgcmVzdWx0ID0gQXJyYXkobik7XG5cbiAgd2hpbGUgKCsraW5kZXggPCBuKSB7XG4gICAgcmVzdWx0W2luZGV4XSA9IGl0ZXJhdGVlKGluZGV4KTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VUaW1lcztcbiIsIi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8udW5hcnlgIHdpdGhvdXQgc3VwcG9ydCBmb3Igc3RvcmluZyBtZXRhZGF0YS5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gZnVuYyBUaGUgZnVuY3Rpb24gdG8gY2FwIGFyZ3VtZW50cyBmb3IuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBjYXBwZWQgZnVuY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIGJhc2VVbmFyeShmdW5jKSB7XG4gIHJldHVybiBmdW5jdGlvbih2YWx1ZSkge1xuICAgIHJldHVybiBmdW5jKHZhbHVlKTtcbiAgfTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlVW5hcnk7XG4iLCJ2YXIgaWRlbnRpdHkgPSByZXF1aXJlKCcuL2lkZW50aXR5Jyk7XG5cbi8qKlxuICogQ2FzdHMgYHZhbHVlYCB0byBgaWRlbnRpdHlgIGlmIGl0J3Mgbm90IGEgZnVuY3Rpb24uXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGluc3BlY3QuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgY2FzdCBmdW5jdGlvbi5cbiAqL1xuZnVuY3Rpb24gY2FzdEZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ2Z1bmN0aW9uJyA/IHZhbHVlIDogaWRlbnRpdHk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY2FzdEZ1bmN0aW9uO1xuIiwiLyoqXG4gKiBDcmVhdGVzIGEgYmFzZSBmdW5jdGlvbiBmb3IgbWV0aG9kcyBsaWtlIGBfLmZvckluYCBhbmQgYF8uZm9yT3duYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtib29sZWFufSBbZnJvbVJpZ2h0XSBTcGVjaWZ5IGl0ZXJhdGluZyBmcm9tIHJpZ2h0IHRvIGxlZnQuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBiYXNlIGZ1bmN0aW9uLlxuICovXG5mdW5jdGlvbiBjcmVhdGVCYXNlRm9yKGZyb21SaWdodCkge1xuICByZXR1cm4gZnVuY3Rpb24ob2JqZWN0LCBpdGVyYXRlZSwga2V5c0Z1bmMpIHtcbiAgICB2YXIgaW5kZXggPSAtMSxcbiAgICAgICAgaXRlcmFibGUgPSBPYmplY3Qob2JqZWN0KSxcbiAgICAgICAgcHJvcHMgPSBrZXlzRnVuYyhvYmplY3QpLFxuICAgICAgICBsZW5ndGggPSBwcm9wcy5sZW5ndGg7XG5cbiAgICB3aGlsZSAobGVuZ3RoLS0pIHtcbiAgICAgIHZhciBrZXkgPSBwcm9wc1tmcm9tUmlnaHQgPyBsZW5ndGggOiArK2luZGV4XTtcbiAgICAgIGlmIChpdGVyYXRlZShpdGVyYWJsZVtrZXldLCBrZXksIGl0ZXJhYmxlKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBvYmplY3Q7XG4gIH07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY3JlYXRlQmFzZUZvcjtcbiIsIi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZ2xvYmFsYCBmcm9tIE5vZGUuanMuICovXG52YXIgZnJlZUdsb2JhbCA9IHR5cGVvZiBnbG9iYWwgPT0gJ29iamVjdCcgJiYgZ2xvYmFsICYmIGdsb2JhbC5PYmplY3QgPT09IE9iamVjdCAmJiBnbG9iYWw7XG5cbm1vZHVsZS5leHBvcnRzID0gZnJlZUdsb2JhbDtcbiIsInZhciBTeW1ib2wgPSByZXF1aXJlKCcuL19TeW1ib2wnKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIEEgc3BlY2lhbGl6ZWQgdmVyc2lvbiBvZiBgYmFzZUdldFRhZ2Agd2hpY2ggaWdub3JlcyBgU3ltYm9sLnRvU3RyaW5nVGFnYCB2YWx1ZXMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgcmF3IGB0b1N0cmluZ1RhZ2AuXG4gKi9cbmZ1bmN0aW9uIGdldFJhd1RhZyh2YWx1ZSkge1xuICB2YXIgaXNPd24gPSBoYXNPd25Qcm9wZXJ0eS5jYWxsKHZhbHVlLCBzeW1Ub1N0cmluZ1RhZyksXG4gICAgICB0YWcgPSB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ107XG5cbiAgdHJ5IHtcbiAgICB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ10gPSB1bmRlZmluZWQ7XG4gICAgdmFyIHVubWFza2VkID0gdHJ1ZTtcbiAgfSBjYXRjaCAoZSkge31cblxuICB2YXIgcmVzdWx0ID0gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG4gIGlmICh1bm1hc2tlZCkge1xuICAgIGlmIChpc093bikge1xuICAgICAgdmFsdWVbc3ltVG9TdHJpbmdUYWddID0gdGFnO1xuICAgIH0gZWxzZSB7XG4gICAgICBkZWxldGUgdmFsdWVbc3ltVG9TdHJpbmdUYWddO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGdldFJhd1RhZztcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKiogVXNlZCB0byBkZXRlY3QgdW5zaWduZWQgaW50ZWdlciB2YWx1ZXMuICovXG52YXIgcmVJc1VpbnQgPSAvXig/OjB8WzEtOV1cXGQqKSQvO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBpbmRleC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcGFyYW0ge251bWJlcn0gW2xlbmd0aD1NQVhfU0FGRV9JTlRFR0VSXSBUaGUgdXBwZXIgYm91bmRzIG9mIGEgdmFsaWQgaW5kZXguXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGluZGV4LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGlzSW5kZXgodmFsdWUsIGxlbmd0aCkge1xuICBsZW5ndGggPSBsZW5ndGggPT0gbnVsbCA/IE1BWF9TQUZFX0lOVEVHRVIgOiBsZW5ndGg7XG4gIHJldHVybiAhIWxlbmd0aCAmJlxuICAgICh0eXBlb2YgdmFsdWUgPT0gJ251bWJlcicgfHwgcmVJc1VpbnQudGVzdCh2YWx1ZSkpICYmXG4gICAgKHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPCBsZW5ndGgpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzSW5kZXg7XG4iLCIvKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGxpa2VseSBhIHByb3RvdHlwZSBvYmplY3QuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBwcm90b3R5cGUsIGVsc2UgYGZhbHNlYC5cbiAqL1xuZnVuY3Rpb24gaXNQcm90b3R5cGUodmFsdWUpIHtcbiAgdmFyIEN0b3IgPSB2YWx1ZSAmJiB2YWx1ZS5jb25zdHJ1Y3RvcixcbiAgICAgIHByb3RvID0gKHR5cGVvZiBDdG9yID09ICdmdW5jdGlvbicgJiYgQ3Rvci5wcm90b3R5cGUpIHx8IG9iamVjdFByb3RvO1xuXG4gIHJldHVybiB2YWx1ZSA9PT0gcHJvdG87XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNQcm90b3R5cGU7XG4iLCIvKipcbiAqIFRoaXMgZnVuY3Rpb24gaXMgbGlrZVxuICogW2BPYmplY3Qua2V5c2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLW9iamVjdC5rZXlzKVxuICogZXhjZXB0IHRoYXQgaXQgaW5jbHVkZXMgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydGllcy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqL1xuZnVuY3Rpb24gbmF0aXZlS2V5c0luKG9iamVjdCkge1xuICB2YXIgcmVzdWx0ID0gW107XG4gIGlmIChvYmplY3QgIT0gbnVsbCkge1xuICAgIGZvciAodmFyIGtleSBpbiBPYmplY3Qob2JqZWN0KSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBuYXRpdmVLZXlzSW47XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHByb2Nlc3NgIGZyb20gTm9kZS5qcy4gKi9cbnZhciBmcmVlUHJvY2VzcyA9IG1vZHVsZUV4cG9ydHMgJiYgZnJlZUdsb2JhbC5wcm9jZXNzO1xuXG4vKiogVXNlZCB0byBhY2Nlc3MgZmFzdGVyIE5vZGUuanMgaGVscGVycy4gKi9cbnZhciBub2RlVXRpbCA9IChmdW5jdGlvbigpIHtcbiAgdHJ5IHtcbiAgICByZXR1cm4gZnJlZVByb2Nlc3MgJiYgZnJlZVByb2Nlc3MuYmluZGluZyAmJiBmcmVlUHJvY2Vzcy5iaW5kaW5nKCd1dGlsJyk7XG4gIH0gY2F0Y2ggKGUpIHt9XG59KCkpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IG5vZGVVdGlsO1xuIiwiLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKlxuICogQ29udmVydHMgYHZhbHVlYCB0byBhIHN0cmluZyB1c2luZyBgT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZ2AuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNvbnZlcnQuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSBjb252ZXJ0ZWQgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBvYmplY3RUb1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gb2JqZWN0VG9TdHJpbmc7XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBzZWxmYC4gKi9cbnZhciBmcmVlU2VsZiA9IHR5cGVvZiBzZWxmID09ICdvYmplY3QnICYmIHNlbGYgJiYgc2VsZi5PYmplY3QgPT09IE9iamVjdCAmJiBzZWxmO1xuXG4vKiogVXNlZCBhcyBhIHJlZmVyZW5jZSB0byB0aGUgZ2xvYmFsIG9iamVjdC4gKi9cbnZhciByb290ID0gZnJlZUdsb2JhbCB8fCBmcmVlU2VsZiB8fCBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHJvb3Q7XG4iLCJ2YXIgYmFzZUZvciA9IHJlcXVpcmUoJy4vX2Jhc2VGb3InKSxcbiAgICBjYXN0RnVuY3Rpb24gPSByZXF1aXJlKCcuL19jYXN0RnVuY3Rpb24nKSxcbiAgICBrZXlzSW4gPSByZXF1aXJlKCcuL2tleXNJbicpO1xuXG4vKipcbiAqIEl0ZXJhdGVzIG92ZXIgb3duIGFuZCBpbmhlcml0ZWQgZW51bWVyYWJsZSBzdHJpbmcga2V5ZWQgcHJvcGVydGllcyBvZiBhblxuICogb2JqZWN0IGFuZCBpbnZva2VzIGBpdGVyYXRlZWAgZm9yIGVhY2ggcHJvcGVydHkuIFRoZSBpdGVyYXRlZSBpcyBpbnZva2VkXG4gKiB3aXRoIHRocmVlIGFyZ3VtZW50czogKHZhbHVlLCBrZXksIG9iamVjdCkuIEl0ZXJhdGVlIGZ1bmN0aW9ucyBtYXkgZXhpdFxuICogaXRlcmF0aW9uIGVhcmx5IGJ5IGV4cGxpY2l0bHkgcmV0dXJuaW5nIGBmYWxzZWAuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjMuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIGl0ZXJhdGUgb3Zlci5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IFtpdGVyYXRlZT1fLmlkZW50aXR5XSBUaGUgZnVuY3Rpb24gaW52b2tlZCBwZXIgaXRlcmF0aW9uLlxuICogQHJldHVybnMge09iamVjdH0gUmV0dXJucyBgb2JqZWN0YC5cbiAqIEBzZWUgXy5mb3JJblJpZ2h0XG4gKiBAZXhhbXBsZVxuICpcbiAqIGZ1bmN0aW9uIEZvbygpIHtcbiAqICAgdGhpcy5hID0gMTtcbiAqICAgdGhpcy5iID0gMjtcbiAqIH1cbiAqXG4gKiBGb28ucHJvdG90eXBlLmMgPSAzO1xuICpcbiAqIF8uZm9ySW4obmV3IEZvbywgZnVuY3Rpb24odmFsdWUsIGtleSkge1xuICogICBjb25zb2xlLmxvZyhrZXkpO1xuICogfSk7XG4gKiAvLyA9PiBMb2dzICdhJywgJ2InLCB0aGVuICdjJyAoaXRlcmF0aW9uIG9yZGVyIGlzIG5vdCBndWFyYW50ZWVkKS5cbiAqL1xuZnVuY3Rpb24gZm9ySW4ob2JqZWN0LCBpdGVyYXRlZSkge1xuICByZXR1cm4gb2JqZWN0ID09IG51bGxcbiAgICA/IG9iamVjdFxuICAgIDogYmFzZUZvcihvYmplY3QsIGNhc3RGdW5jdGlvbihpdGVyYXRlZSksIGtleXNJbik7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZm9ySW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgdGhlIGZpcnN0IGFyZ3VtZW50IGl0IHJlY2VpdmVzLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBzaW5jZSAwLjEuMFxuICogQG1lbWJlck9mIF9cbiAqIEBjYXRlZ29yeSBVdGlsXG4gKiBAcGFyYW0geyp9IHZhbHVlIEFueSB2YWx1ZS5cbiAqIEByZXR1cm5zIHsqfSBSZXR1cm5zIGB2YWx1ZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIHZhciBvYmplY3QgPSB7ICdhJzogMSB9O1xuICpcbiAqIGNvbnNvbGUubG9nKF8uaWRlbnRpdHkob2JqZWN0KSA9PT0gb2JqZWN0KTtcbiAqIC8vID0+IHRydWVcbiAqL1xuZnVuY3Rpb24gaWRlbnRpdHkodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlkZW50aXR5O1xuIiwidmFyIGJhc2VJc0FyZ3VtZW50cyA9IHJlcXVpcmUoJy4vX2Jhc2VJc0FyZ3VtZW50cycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHByb3BlcnR5SXNFbnVtZXJhYmxlID0gb2JqZWN0UHJvdG8ucHJvcGVydHlJc0VudW1lcmFibGU7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgbGlrZWx5IGFuIGBhcmd1bWVudHNgIG9iamVjdC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBgYXJndW1lbnRzYCBvYmplY3QsXG4gKiAgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQXJndW1lbnRzKGZ1bmN0aW9uKCkgeyByZXR1cm4gYXJndW1lbnRzOyB9KCkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcmd1bWVudHMoWzEsIDIsIDNdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc0FyZ3VtZW50cyA9IGJhc2VJc0FyZ3VtZW50cyhmdW5jdGlvbigpIHsgcmV0dXJuIGFyZ3VtZW50czsgfSgpKSA/IGJhc2VJc0FyZ3VtZW50cyA6IGZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGhhc093blByb3BlcnR5LmNhbGwodmFsdWUsICdjYWxsZWUnKSAmJlxuICAgICFwcm9wZXJ0eUlzRW51bWVyYWJsZS5jYWxsKHZhbHVlLCAnY2FsbGVlJyk7XG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJndW1lbnRzO1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGFuIGBBcnJheWAgb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheShbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheShkb2N1bWVudC5ib2R5LmNoaWxkcmVuKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KF8ubm9vcCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNBcnJheSA9IEFycmF5LmlzQXJyYXk7XG5cbm1vZHVsZS5leHBvcnRzID0gaXNBcnJheTtcbiIsInZhciBpc0Z1bmN0aW9uID0gcmVxdWlyZSgnLi9pc0Z1bmN0aW9uJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYXJyYXktbGlrZS4gQSB2YWx1ZSBpcyBjb25zaWRlcmVkIGFycmF5LWxpa2UgaWYgaXQnc1xuICogbm90IGEgZnVuY3Rpb24gYW5kIGhhcyBhIGB2YWx1ZS5sZW5ndGhgIHRoYXQncyBhbiBpbnRlZ2VyIGdyZWF0ZXIgdGhhbiBvclxuICogZXF1YWwgdG8gYDBgIGFuZCBsZXNzIHRoYW4gb3IgZXF1YWwgdG8gYE51bWJlci5NQVhfU0FGRV9JTlRFR0VSYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhcnJheS1saWtlLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKGRvY3VtZW50LmJvZHkuY2hpbGRyZW4pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoJ2FiYycpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzQXJyYXlMaWtlKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPSBudWxsICYmIGlzTGVuZ3RoKHZhbHVlLmxlbmd0aCkgJiYgIWlzRnVuY3Rpb24odmFsdWUpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJyYXlMaWtlO1xuIiwidmFyIHJvb3QgPSByZXF1aXJlKCcuL19yb290JyksXG4gICAgc3R1YkZhbHNlID0gcmVxdWlyZSgnLi9zdHViRmFsc2UnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBCdWZmZXIgPSBtb2R1bGVFeHBvcnRzID8gcm9vdC5CdWZmZXIgOiB1bmRlZmluZWQ7XG5cbi8qIEJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzIGZvciB0aG9zZSB3aXRoIHRoZSBzYW1lIG5hbWUgYXMgb3RoZXIgYGxvZGFzaGAgbWV0aG9kcy4gKi9cbnZhciBuYXRpdmVJc0J1ZmZlciA9IEJ1ZmZlciA/IEJ1ZmZlci5pc0J1ZmZlciA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlci5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMy4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlciwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQnVmZmVyKG5ldyBCdWZmZXIoMikpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNCdWZmZXIobmV3IFVpbnQ4QXJyYXkoMikpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzQnVmZmVyID0gbmF0aXZlSXNCdWZmZXIgfHwgc3R1YkZhbHNlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQnVmZmVyO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0Jyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhc3luY1RhZyA9ICdbb2JqZWN0IEFzeW5jRnVuY3Rpb25dJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBnZW5UYWcgPSAnW29iamVjdCBHZW5lcmF0b3JGdW5jdGlvbl0nLFxuICAgIHByb3h5VGFnID0gJ1tvYmplY3QgUHJveHldJztcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGEgYEZ1bmN0aW9uYCBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBmdW5jdGlvbiwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzRnVuY3Rpb24oXyk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0Z1bmN0aW9uKC9hYmMvKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzRnVuY3Rpb24odmFsdWUpIHtcbiAgaWYgKCFpc09iamVjdCh2YWx1ZSkpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgLy8gVGhlIHVzZSBvZiBgT2JqZWN0I3RvU3RyaW5nYCBhdm9pZHMgaXNzdWVzIHdpdGggdGhlIGB0eXBlb2ZgIG9wZXJhdG9yXG4gIC8vIGluIFNhZmFyaSA5IHdoaWNoIHJldHVybnMgJ29iamVjdCcgZm9yIHR5cGVkIGFycmF5cyBhbmQgb3RoZXIgY29uc3RydWN0b3JzLlxuICB2YXIgdGFnID0gYmFzZUdldFRhZyh2YWx1ZSk7XG4gIHJldHVybiB0YWcgPT0gZnVuY1RhZyB8fCB0YWcgPT0gZ2VuVGFnIHx8IHRhZyA9PSBhc3luY1RhZyB8fCB0YWcgPT0gcHJveHlUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNGdW5jdGlvbjtcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBsZW5ndGguXG4gKlxuICogKipOb3RlOioqIFRoaXMgbWV0aG9kIGlzIGxvb3NlbHkgYmFzZWQgb25cbiAqIFtgVG9MZW5ndGhgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy10b2xlbmd0aCkuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBsZW5ndGgsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0xlbmd0aCgzKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzTGVuZ3RoKE51bWJlci5NSU5fVkFMVUUpO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzTGVuZ3RoKEluZmluaXR5KTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0xlbmd0aCgnMycpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNMZW5ndGgodmFsdWUpIHtcbiAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PSAnbnVtYmVyJyAmJlxuICAgIHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPD0gTUFYX1NBRkVfSU5URUdFUjtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0xlbmd0aDtcbiIsIi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgdGhlXG4gKiBbbGFuZ3VhZ2UgdHlwZV0oaHR0cDovL3d3dy5lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLWVjbWFzY3JpcHQtbGFuZ3VhZ2UtdHlwZXMpXG4gKiBvZiBgT2JqZWN0YC4gKGUuZy4gYXJyYXlzLCBmdW5jdGlvbnMsIG9iamVjdHMsIHJlZ2V4ZXMsIGBuZXcgTnVtYmVyKDApYCwgYW5kIGBuZXcgU3RyaW5nKCcnKWApXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNPYmplY3Qoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3QoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0KF8ubm9vcCk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdChudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0KHZhbHVlKSB7XG4gIHZhciB0eXBlID0gdHlwZW9mIHZhbHVlO1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiAodHlwZSA9PSAnb2JqZWN0JyB8fCB0eXBlID09ICdmdW5jdGlvbicpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzT2JqZWN0O1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZS4gQSB2YWx1ZSBpcyBvYmplY3QtbGlrZSBpZiBpdCdzIG5vdCBgbnVsbGBcbiAqIGFuZCBoYXMgYSBgdHlwZW9mYCByZXN1bHQgb2YgXCJvYmplY3RcIi5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZSh7fSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0TGlrZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiB0eXBlb2YgdmFsdWUgPT0gJ29iamVjdCc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNPYmplY3RMaWtlO1xuIiwidmFyIGJhc2VJc1R5cGVkQXJyYXkgPSByZXF1aXJlKCcuL19iYXNlSXNUeXBlZEFycmF5JyksXG4gICAgYmFzZVVuYXJ5ID0gcmVxdWlyZSgnLi9fYmFzZVVuYXJ5JyksXG4gICAgbm9kZVV0aWwgPSByZXF1aXJlKCcuL19ub2RlVXRpbCcpO1xuXG4vKiBOb2RlLmpzIGhlbHBlciByZWZlcmVuY2VzLiAqL1xudmFyIG5vZGVJc1R5cGVkQXJyYXkgPSBub2RlVXRpbCAmJiBub2RlVXRpbC5pc1R5cGVkQXJyYXk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhIHR5cGVkIGFycmF5LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdHlwZWQgYXJyYXksIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc1R5cGVkQXJyYXkobmV3IFVpbnQ4QXJyYXkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNUeXBlZEFycmF5KFtdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc1R5cGVkQXJyYXkgPSBub2RlSXNUeXBlZEFycmF5ID8gYmFzZVVuYXJ5KG5vZGVJc1R5cGVkQXJyYXkpIDogYmFzZUlzVHlwZWRBcnJheTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc1R5cGVkQXJyYXk7XG4iLCJ2YXIgYXJyYXlMaWtlS2V5cyA9IHJlcXVpcmUoJy4vX2FycmF5TGlrZUtleXMnKSxcbiAgICBiYXNlS2V5c0luID0gcmVxdWlyZSgnLi9fYmFzZUtleXNJbicpLFxuICAgIGlzQXJyYXlMaWtlID0gcmVxdWlyZSgnLi9pc0FycmF5TGlrZScpO1xuXG4vKipcbiAqIENyZWF0ZXMgYW4gYXJyYXkgb2YgdGhlIG93biBhbmQgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydHkgbmFtZXMgb2YgYG9iamVjdGAuXG4gKlxuICogKipOb3RlOioqIE5vbi1vYmplY3QgdmFsdWVzIGFyZSBjb2VyY2VkIHRvIG9iamVjdHMuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAzLjAuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqIEBleGFtcGxlXG4gKlxuICogZnVuY3Rpb24gRm9vKCkge1xuICogICB0aGlzLmEgPSAxO1xuICogICB0aGlzLmIgPSAyO1xuICogfVxuICpcbiAqIEZvby5wcm90b3R5cGUuYyA9IDM7XG4gKlxuICogXy5rZXlzSW4obmV3IEZvbyk7XG4gKiAvLyA9PiBbJ2EnLCAnYicsICdjJ10gKGl0ZXJhdGlvbiBvcmRlciBpcyBub3QgZ3VhcmFudGVlZClcbiAqL1xuZnVuY3Rpb24ga2V5c0luKG9iamVjdCkge1xuICByZXR1cm4gaXNBcnJheUxpa2Uob2JqZWN0KSA/IGFycmF5TGlrZUtleXMob2JqZWN0LCB0cnVlKSA6IGJhc2VLZXlzSW4ob2JqZWN0KTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBrZXlzSW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgYGZhbHNlYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMTMuMFxuICogQGNhdGVnb3J5IFV0aWxcbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8udGltZXMoMiwgXy5zdHViRmFsc2UpO1xuICogLy8gPT4gW2ZhbHNlLCBmYWxzZV1cbiAqL1xuZnVuY3Rpb24gc3R1YkZhbHNlKCkge1xuICByZXR1cm4gZmFsc2U7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gc3R1YkZhbHNlO1xuIiwiLyoganNoaW50IGJyb3dzZXI6dHJ1ZSAqL1xyXG4vKiBnbG9iYWxzIF9fT1BUSU9OU19fOnRydWUgKi9cclxuXHJcbmltcG9ydCBMYXZhSnMgZnJvbSAnLi9sYXZhL0xhdmEuZXM2JztcclxuaW1wb3J0IHsgZG9tTG9hZGVkIH0gZnJvbSAnLi9sYXZhL1V0aWxzLmVzNic7XHJcblxyXG4vKipcclxuICogQXNzaWduIHRoZSBMYXZhLmpzIG1vZHVsZSB0byB0aGUgd2luZG93IGFuZFxyXG4gKiBsZXQgJGxhdmEgYmUgYW4gYWxpYXMgdG8gdGhlIG1vZHVsZS5cclxuICovXHJcbndpbmRvdy5sYXZhID0gbmV3IExhdmFKcygpO1xyXG5cclxuLyoqXHJcbiAqIElmIExhdmEuanMgd2FzIGxvYWRlZCBmcm9tIExhdmFjaGFydHMsIHRoZSBfX09QVElPTlNfX1xyXG4gKiBwbGFjZWhvbGRlciB3aWxsIGJlIGEgSlNPTiBvYmplY3Qgb2Ygb3B0aW9ucyB0aGF0XHJcbiAqIHdlcmUgc2V0IHNlcnZlci1zaWRlLlxyXG4gKi9cclxuaWYgKHR5cGVvZiBfX09QVElPTlNfXyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgIHdpbmRvdy5sYXZhLm9wdGlvbnMgPSBfX09QVElPTlNfXztcclxufVxyXG5cclxuLyoqXHJcbiAqIElmIExhdmEuanMgd2FzIHNldCB0byBhdXRvX3J1biB0aGVuIG9uY2UgdGhlIERPTVxyXG4gKiBpcyByZWFkeSwgcmVuZGVyaW5nIHdpbGwgYmVnaW4uXHJcbiAqL1xyXG5pZiAod2luZG93LmxhdmEub3B0aW9ucy5hdXRvX3J1biA9PT0gdHJ1ZSkge1xyXG4gICAgZG9tTG9hZGVkKCkudGhlbigoKSA9PiB7XHJcbiAgICAgICAgd2luZG93LmxhdmEucnVuKCk7XHJcbiAgICB9KTtcclxufVxyXG4iLCIvKipcclxuICogQ2hhcnQgbW9kdWxlXHJcbiAqXHJcbiAqIEBjbGFzcyAgICAgQ2hhcnRcclxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmltcG9ydCBfZm9ySW4gZnJvbSAnbG9kYXNoL2ZvckluJztcclxuaW1wb3J0IHsgUmVuZGVyYWJsZSB9IGZyb20gJy4vUmVuZGVyYWJsZS5lczYnO1xyXG5pbXBvcnQgeyBzdHJpbmdUb0Z1bmN0aW9uIH0gZnJvbSAnLi9VdGlscy5lczYnO1xyXG5cclxuLyoqXHJcbiAqIENoYXJ0IGNsYXNzIHVzZWQgZm9yIHN0b3JpbmcgYWxsIHRoZSBuZWVkZWQgY29uZmlndXJhdGlvbiBmb3IgcmVuZGVyaW5nLlxyXG4gKlxyXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBDaGFydFxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBsYWJlbCAgICAgLSBMYWJlbCBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgY2hhcnQgICAgIC0gR29vZ2xlIGNoYXJ0IG9iamVjdC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgcGFja2FnZSAgIC0gVHlwZSBvZiBHb29nbGUgY2hhcnQgcGFja2FnZSB0byBsb2FkLlxyXG4gKiBAcHJvcGVydHkge2Jvb2xlYW59ICBwbmdPdXRwdXQgLSBTaG91bGQgdGhlIGNoYXJ0IGJlIGRpc3BsYXllZCBhcyBhIFBORy5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZGF0YSAgICAgIC0gRGF0YXRhYmxlIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIG9wdGlvbnMgICAtIENvbmZpZ3VyYXRpb24gb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBldmVudHMgICAgLSBFdmVudHMgYW5kIGNhbGxiYWNrcyB0byBhcHBseSB0byB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7QXJyYXl9ICAgIGZvcm1hdHMgICAtIEZvcm1hdHRlcnMgdG8gYXBwbHkgdG8gdGhlIGNoYXJ0IGRhdGEuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSB1dWlkICAgICAgLSBDcmVhdGVzIGlkZW50aWZpY2F0aW9uIHN0cmluZyBmb3IgdGhlIGNoYXJ0LlxyXG4gKi9cclxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hhcnQgZXh0ZW5kcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIC8qKlxyXG4gICAgICogQ2hhcnQgQ2xhc3NcclxuICAgICAqXHJcbiAgICAgKiBUaGlzIGlzIHRoZSBqYXZhc2NyaXB0IHZlcnNpb24gb2YgYSBsYXZhY2hhcnQgd2l0aCBtZXRob2RzIGZvciBpbnRlcmFjdGluZyB3aXRoXHJcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGpzb25cclxuICAgICAqIEBjb25zdHJ1Y3RvclxyXG4gICAgICovXHJcbiAgICBjb25zdHJ1Y3RvciAoanNvbikge1xyXG4gICAgICAgIHN1cGVyKGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLnR5cGUgICAgPSBqc29uLnR5cGU7XHJcbiAgICAgICAgdGhpcy5jbGFzcyAgID0ganNvbi5jbGFzcztcclxuICAgICAgICB0aGlzLmZvcm1hdHMgPSBqc29uLmZvcm1hdHM7XHJcblxyXG4gICAgICAgIHRoaXMuZXZlbnRzICAgID0gdHlwZW9mIGpzb24uZXZlbnRzID09PSAnb2JqZWN0JyA/IGpzb24uZXZlbnRzIDogbnVsbDtcclxuICAgICAgICB0aGlzLnBuZ091dHB1dCA9IHR5cGVvZiBqc29uLnBuZ091dHB1dCA9PT0gJ3VuZGVmaW5lZCcgPyBmYWxzZSA6IEJvb2xlYW4oanNvbi5wbmdPdXRwdXQpO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBbnkgZGVwZW5kZW5jeSBvbiB3aW5kb3cuZ29vZ2xlIG11c3QgYmUgaW4gdGhlIHJlbmRlciBzY29wZS5cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcclxuICAgICAgICAgICAgdGhpcy5zZXREYXRhKGpzb24uZGF0YXRhYmxlKTtcclxuXHJcbiAgICAgICAgICAgIGxldCBDaGFydENsYXNzID0gc3RyaW5nVG9GdW5jdGlvbih0aGlzLmNsYXNzLCB3aW5kb3cpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5nY2hhcnQgPSBuZXcgQ2hhcnRDbGFzcyh0aGlzLmVsZW1lbnQpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuZm9ybWF0cykge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5hcHBseUZvcm1hdHMoKTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuZXZlbnRzKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcclxuICAgICAgICAgICAgICAgIC8vIFRPRE86IElkZWEuLi4gZm9yd2FyZCBldmVudHMgdG8gYmUgbGlzdGVuYWJsZSBieSB0aGUgdXNlciwgaW5zdGVhZCBvZiBoYXZpbmcgdGhlIHVzZXIgZGVmaW5lIHRoZW0gYXMgYSBzdHJpbmcgY2FsbGJhY2suXHJcbiAgICAgICAgICAgICAgICAvLyBsYXZhLmdldCgnTXlDb29sQ2hhcnQnKS5vbigncmVhZHknLCBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAvLyAgICAgY29uc29sZS5sb2codGhpcyk7ICAvLyBnQ2hhcnRcclxuICAgICAgICAgICAgICAgIC8vIH0pO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLnBuZ091dHB1dCkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5kcmF3UG5nKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IGFzIGEgUE5HIGluc3RlYWQgb2YgdGhlIHN0YW5kYXJkIFNWR1xyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBleHRlcm5hbCBcImNoYXJ0LmdldEltYWdlVVJJXCJcclxuICAgICAqIEBzZWUge0BsaW5rIGh0dHBzOi8vZGV2ZWxvcGVycy5nb29nbGUuY29tL2NoYXJ0L2ludGVyYWN0aXZlL2RvY3MvcHJpbnRpbmd8UHJpbnRpbmcgUE5HIENoYXJ0c31cclxuICAgICAqL1xyXG4gICAgZHJhd1BuZygpIHtcclxuICAgICAgICBsZXQgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XHJcbiAgICAgICAgICAgIGltZy5zcmMgPSB0aGlzLmdjaGFydC5nZXRJbWFnZVVSSSgpO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQuaW5uZXJIVE1MID0gJyc7XHJcbiAgICAgICAgdGhpcy5lbGVtZW50LmFwcGVuZENoaWxkKGltZyk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBcHBseSB0aGUgZm9ybWF0cyB0byB0aGUgRGF0YVRhYmxlXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtBcnJheX0gZm9ybWF0c1xyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICovXHJcbiAgICBhcHBseUZvcm1hdHMoZm9ybWF0cykge1xyXG4gICAgICAgIGlmICghIGZvcm1hdHMpIHtcclxuICAgICAgICAgICAgZm9ybWF0cyA9IHRoaXMuZm9ybWF0cztcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGZvciAobGV0IGZvcm1hdCBvZiBmb3JtYXRzKSB7XHJcbiAgICAgICAgICAgIGxldCBmb3JtYXR0ZXIgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb25bZm9ybWF0LnR5cGVdKGZvcm1hdC5vcHRpb25zKTtcclxuXHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gQ29sdW1uIGluZGV4IFske2Zvcm1hdC5pbmRleH1dIGZvcm1hdHRlZCB3aXRoOmAsIGZvcm1hdHRlcik7XHJcblxyXG4gICAgICAgICAgICBmb3JtYXR0ZXIuZm9ybWF0KHRoaXMuZGF0YSwgZm9ybWF0LmluZGV4KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBdHRhY2ggdGhlIGRlZmluZWQgY2hhcnQgZXZlbnQgaGFuZGxlcnMuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2F0dGFjaEV2ZW50cygpIHtcclxuICAgICAgICBsZXQgJGNoYXJ0ID0gdGhpcztcclxuXHJcbiAgICAgICAgX2ZvckluKHRoaXMuZXZlbnRzLCBmdW5jdGlvbiAoY2FsbGJhY2ssIGV2ZW50KSB7XHJcbiAgICAgICAgICAgIGxldCBjb250ZXh0ID0gd2luZG93O1xyXG4gICAgICAgICAgICBsZXQgZnVuYyA9IGNhbGxiYWNrO1xyXG5cclxuICAgICAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ29iamVjdCcpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRleHQgPSBjb250ZXh0W2NhbGxiYWNrWzBdXTtcclxuICAgICAgICAgICAgICAgIGZ1bmMgPSBjYWxsYmFja1sxXTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBUaGUgXCIkeyRjaGFydC51dWlkKCl9Ojoke2V2ZW50fVwiIGV2ZW50IHdpbGwgYmUgaGFuZGxlZCBieSBcIiR7ZnVuY31cIiBpbiB0aGUgY29udGV4dGAsIGNvbnRleHQpO1xyXG5cclxuICAgICAgICAgICAgLyoqXHJcbiAgICAgICAgICAgICAqIFNldCB0aGUgY29udGV4dCBvZiBcInRoaXNcIiB3aXRoaW4gdGhlIHVzZXIgcHJvdmlkZWQgY2FsbGJhY2sgdG8gdGhlXHJcbiAgICAgICAgICAgICAqIGNoYXJ0IHRoYXQgZmlyZWQgdGhlIGV2ZW50IHdoaWxlIHByb3ZpZGluZyB0aGUgZGF0YXRhYmxlIG9mIHRoZSBjaGFydFxyXG4gICAgICAgICAgICAgKiB0byB0aGUgY2FsbGJhY2sgYXMgYW4gYXJndW1lbnQuXHJcbiAgICAgICAgICAgICAqL1xyXG4gICAgICAgICAgICBnb29nbGUudmlzdWFsaXphdGlvbi5ldmVudHMuYWRkTGlzdGVuZXIoJGNoYXJ0LmdjaGFydCwgZXZlbnQsIGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICAgICAgY29uc3QgY2FsbGJhY2sgPSBjb250ZXh0W2Z1bmNdLmJpbmQoJGNoYXJ0LmdjaGFydCk7XHJcblxyXG4gICAgICAgICAgICAgICAgY2FsbGJhY2soJGNoYXJ0LmRhdGEpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufVxyXG4iLCIvKipcclxuICogRGFzaGJvYXJkIG1vZHVsZVxyXG4gKlxyXG4gKiBAY2xhc3MgICAgIERhc2hib2FyZFxyXG4gKiBAbW9kdWxlICAgIGxhdmEvRGFzaGJvYXJkXHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmltcG9ydCB7IFJlbmRlcmFibGUgfSBmcm9tICcuL1JlbmRlcmFibGUuZXM2JztcclxuaW1wb3J0IHsgc3RyaW5nVG9GdW5jdGlvbiB9IGZyb20gJy4vVXRpbHMuZXM2JztcclxuXHJcbi8qKlxyXG4gKiBEYXNoYm9hcmQgY2xhc3NcclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgRGFzaGJvYXJkXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIGxhYmVsICAgICAtIExhYmVsIGZvciB0aGUgRGFzaGJvYXJkLlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIHZpc3VhbGl6YXRpb24gKERhc2hib2FyZCkuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGVsZW1lbnQgICAtIEh0bWwgZWxlbWVudCBpbiB3aGljaCB0byByZW5kZXIgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBwYWNrYWdlICAgLSBUeXBlIG9mIHZpc3VhbGl6YXRpb24gcGFja2FnZSB0byBsb2FkLlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBkYXRhICAgICAgLSBEYXRhdGFibGUgZm9yIHRoZSBEYXNoYm9hcmQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIG9wdGlvbnMgICAtIENvbmZpZ3VyYXRpb24gb3B0aW9ucy5cclxuICogQHByb3BlcnR5IHtBcnJheX0gICAgYmluZGluZ3MgIC0gQ2hhcnQgYW5kIENvbnRyb2wgYmluZGluZ3MuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIERhc2hib2FyZC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gVW5pcXVlIGlkZW50aWZpZXIgZm9yIHRoZSBEYXNoYm9hcmQuXHJcbiAqL1xyXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBEYXNoYm9hcmQgZXh0ZW5kcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIGNvbnN0cnVjdG9yKGpzb24pIHtcclxuICAgICAgICBzdXBlcihqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy50eXBlICAgICA9ICdEYXNoYm9hcmQnO1xyXG4gICAgICAgIHRoaXMuYmluZGluZ3MgPSBqc29uLmJpbmRpbmdzO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBBbnkgZGVwZW5kZW5jeSBvbiB3aW5kb3cuZ29vZ2xlIG11c3QgYmUgaW4gdGhlIHJlbmRlciBzY29wZS5cclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLnJlbmRlciA9ICgpID0+IHtcclxuICAgICAgICAgICAgdGhpcy5zZXREYXRhKGpzb24uZGF0YXRhYmxlKTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhc2hib2FyZCh0aGlzLmVsZW1lbnQpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5fYXR0YWNoQmluZGluZ3MoKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLmV2ZW50cykge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5fYXR0YWNoRXZlbnRzKCk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHRoaXMuZHJhdygpO1xyXG4gICAgICAgIH07XHJcbiAgICB9XHJcblxyXG4gICAgLy8gQFRPRE86IHRoaXMgbmVlZHMgdG8gYmUgbW9kaWZpZWQgZm9yIHRoZSBvdGhlciB0eXBlcyBvZiBiaW5kaW5ncy5cclxuXHJcbiAgICAvKipcclxuICAgICAqIFByb2Nlc3MgYW5kIGF0dGFjaCB0aGUgYmluZGluZ3MgdG8gdGhlIGRhc2hib2FyZC5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICovXHJcbiAgICBfYXR0YWNoQmluZGluZ3MoKSB7XHJcbiAgICAgICAgZm9yIChsZXQgYmluZGluZyBvZiB0aGlzLmJpbmRpbmdzKSB7XHJcbiAgICAgICAgICAgIGxldCBjb250cm9sV3JhcHMgPSBbXTtcclxuICAgICAgICAgICAgbGV0IGNoYXJ0V3JhcHMgPSBbXTtcclxuXHJcbiAgICAgICAgICAgIGZvciAobGV0IGNvbnRyb2xXcmFwIG9mIGJpbmRpbmcuY29udHJvbFdyYXBwZXJzKSB7XHJcbiAgICAgICAgICAgICAgICBjb250cm9sV3JhcHMucHVzaChcclxuICAgICAgICAgICAgICAgICAgICBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uQ29udHJvbFdyYXBwZXIoY29udHJvbFdyYXApXHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBmb3IgKGxldCBjaGFydFdyYXAgb2YgYmluZGluZy5jaGFydFdyYXBwZXJzKSB7XHJcbiAgICAgICAgICAgICAgICBjaGFydFdyYXBzLnB1c2goXHJcbiAgICAgICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkNoYXJ0V3JhcHBlcihjaGFydFdyYXApXHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLmdjaGFydC5iaW5kKGNvbnRyb2xXcmFwcywgY2hhcnRXcmFwcyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBFcnJvcnMgbW9kdWxlXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9FcnJvcnNcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuY2xhc3MgTGF2YUVycm9yIGV4dGVuZHMgRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKG1lc3NhZ2UpIHtcclxuICAgICAgICBzdXBlcigpO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgICAgPSAnTGF2YUVycm9yJztcclxuICAgICAgICB0aGlzLm1lc3NhZ2UgPSAobWVzc2FnZSB8fCAnJyk7XHJcbiAgICB9O1xyXG59XHJcblxyXG4vKipcclxuICogSW52YWxpZENhbGxiYWNrIEVycm9yXHJcbiAqXHJcbiAqIHRocm93biB3aGVuIHdoZW4gYW55dGhpbmcgYnV0IGEgZnVuY3Rpb24gaXMgZ2l2ZW4gYXMgYSBjYWxsYmFja1xyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgSW52YWxpZENhbGxiYWNrIGV4dGVuZHMgTGF2YUVycm9yXHJcbntcclxuICAgIGNvbnN0cnVjdG9yIChjYWxsYmFjaykge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gXCIke3R5cGVvZiBjYWxsYmFja31cIiBpcyBub3QgYSB2YWxpZCBjYWxsYmFjay5gKTtcclxuXHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRDYWxsYmFjayc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBJbnZhbGlkTGFiZWwgRXJyb3JcclxuICpcclxuICogVGhyb3duIHdoZW4gd2hlbiBhbnl0aGluZyBidXQgYSBzdHJpbmcgaXMgZ2l2ZW4gYXMgYSBsYWJlbC5cclxuICpcclxuICogQHR5cGUge2Z1bmN0aW9ufVxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIEludmFsaWRMYWJlbCBleHRlbmRzIExhdmFFcnJvclxyXG57XHJcbiAgICBjb25zdHJ1Y3RvciAobGFiZWwpIHtcclxuICAgICAgICBzdXBlcihgW2xhdmEuanNdIFwiJHt0eXBlb2YgbGFiZWx9XCIgaXMgbm90IGEgdmFsaWQgbGFiZWwuYCk7XHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRMYWJlbCc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBFbGVtZW50SWROb3RGb3VuZCBFcnJvclxyXG4gKlxyXG4gKiBUaHJvd24gd2hlbiB3aGVuIGFueXRoaW5nIGJ1dCBhIHN0cmluZyBpcyBnaXZlbiBhcyBhIGxhYmVsLlxyXG4gKlxyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgRWxlbWVudElkTm90Rm91bmQgZXh0ZW5kcyBMYXZhRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGVsZW1JZCkge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gRE9NIG5vZGUgd2hlcmUgaWQ9XCIke2VsZW1JZH1cIiB3YXMgbm90IGZvdW5kLmApO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgPSAnRWxlbWVudElkTm90Rm91bmQnO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qIGpzaGludCBicm93c2VyOnRydWUgKi9cclxuLyogZ2xvYmFscyBnb29nbGU6dHJ1ZSAqL1xyXG5cclxuLyoqXHJcbiAqIGxhdmEuanMgbW9kdWxlXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9MYXZhXHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBodHRwOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvTUlUIE1JVFxyXG4gKi9cclxuaW1wb3J0IF9mb3JJbiBmcm9tICdsb2Rhc2gvZm9ySW4nO1xyXG5pbXBvcnQgRXZlbnRFbWl0dGVyIGZyb20gJ2V2ZW50cyc7XHJcbmltcG9ydCBDaGFydCBmcm9tICcuL0NoYXJ0LmVzNic7XHJcbmltcG9ydCBEYXNoYm9hcmQgZnJvbSAnLi9EYXNoYm9hcmQuZXM2JztcclxuaW1wb3J0IGRlZmF1bHRPcHRpb25zIGZyb20gJy4vT3B0aW9ucy5qcyc7XHJcbmltcG9ydCB7IG5vb3AsIGFkZEV2ZW50IH0gZnJvbSAnLi9VdGlscy5lczYnO1xyXG5pbXBvcnQgeyBJbnZhbGlkQ2FsbGJhY2ssIFJlbmRlcmFibGVOb3RGb3VuZCB9IGZyb20gJy4vRXJyb3JzLmVzNidcclxuXHJcblxyXG4vKipcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgICAgICAgICAgIFZFUlNJT05cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgICAgICAgICAgIEdPT0dMRV9BUElfVkVSU0lPTlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICAgICAgICAgICAgR09PR0xFX0xPQURFUl9VUkxcclxuICogQHByb3BlcnR5IHtDaGFydH0gICAgICAgICAgICAgIENoYXJ0XHJcbiAqIEBwcm9wZXJ0eSB7RGFzaGJvYXJkfSAgICAgICAgICBEYXNoYm9hcmRcclxuICogQHByb3BlcnR5IHtvYmplY3R9ICAgICAgICAgICAgIG9wdGlvbnNcclxuICogQHByb3BlcnR5IHtmdW5jdGlvbn0gICAgICAgICAgIF9yZWFkeUNhbGxiYWNrXHJcbiAqIEBwcm9wZXJ0eSB7QXJyYXkuPHN0cmluZz59ICAgICBfcGFja2FnZXNcclxuICogQHByb3BlcnR5IHtBcnJheS48UmVuZGVyYWJsZT59IF9yZW5kZXJhYmxlc1xyXG4gKi9cclxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGF2YUpzIGV4dGVuZHMgRXZlbnRFbWl0dGVyXHJcbntcclxuICAgIGNvbnN0cnVjdG9yKG5ld09wdGlvbnMpIHtcclxuICAgICAgICBzdXBlcigpO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBWZXJzaW9uIG9mIHRoZSBMYXZhLmpzIG1vZHVsZS5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuVkVSU0lPTiA9ICc0LjAuMCc7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFZlcnNpb24gb2YgdGhlIEdvb2dsZSBjaGFydHMgQVBJIHRvIGxvYWQuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7c3RyaW5nfVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkdPT0dMRV9BUElfVkVSU0lPTiA9ICdjdXJyZW50JztcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogVXJscyB0byBHb29nbGUncyBzdGF0aWMgbG9hZGVyXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7c3RyaW5nfVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkdPT0dMRV9MT0FERVJfVVJMID0gJ2h0dHBzOi8vd3d3LmdzdGF0aWMuY29tL2NoYXJ0cy9sb2FkZXIuanMnO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBTdG9yaW5nIHRoZSBDaGFydCBtb2R1bGUgd2l0aGluIExhdmEuanNcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtDaGFydH1cclxuICAgICAgICAgKiBAcHVibGljXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5DaGFydCA9IENoYXJ0O1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBTdG9yaW5nIHRoZSBEYXNoYm9hcmQgbW9kdWxlIHdpdGhpbiBMYXZhLmpzXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7RGFzaGJvYXJkfVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkRhc2hib2FyZCA9IERhc2hib2FyZDtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogSlNPTiBvYmplY3Qgb2YgY29uZmlnIGl0ZW1zLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge09iamVjdH1cclxuICAgICAgICAgKiBAcHVibGljXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5vcHRpb25zID0gbmV3T3B0aW9ucyB8fCBkZWZhdWx0T3B0aW9ucztcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogUmVmZXJlbmNlIHRvIHRoZSBnb29nbGUudmlzdWFsaXphdGlvbiBvYmplY3QuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7Z29vZ2xlLnZpc3VhbGl6YXRpb259XHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy52aXN1YWxpemF0aW9uID0gbnVsbDtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogQXJyYXkgb2YgdmlzdWFsaXphdGlvbiBwYWNrYWdlcyBmb3IgY2hhcnRzIGFuZCBkYXNoYm9hcmRzLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge0FycmF5LjxzdHJpbmc+fVxyXG4gICAgICAgICAqIEBwcml2YXRlXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5fcGFja2FnZXMgPSBbXTtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogQXJyYXkgb2YgY2hhcnRzIGFuZCBkYXNoYm9hcmRzIHN0b3JlZCBpbiB0aGUgbW9kdWxlLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge0FycmF5LjxSZW5kZXJhYmxlPn1cclxuICAgICAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuX3JlbmRlcmFibGVzID0gW107XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFJlYWR5IGNhbGxiYWNrIHRvIGJlIGNhbGxlZCB3aGVuIHRoZSBtb2R1bGUgaXMgZmluaXNoZWQgcnVubmluZy5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEBjYWxsYmFjayBfcmVhZHlDYWxsYmFja1xyXG4gICAgICAgICAqIEBwcml2YXRlXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5fcmVhZHlDYWxsYmFjayA9IG5vb3A7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBDcmVhdGUgYSBuZXcgQ2hhcnQgZnJvbSBhIEpTT04gcGF5bG9hZC5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgSlNPTiBwYXlsb2FkIGNvbWVzIGZyb20gdGhlIFBIUCBDaGFydCBjbGFzcy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGpzb25cclxuICAgICAqIEByZXR1cm4ge1JlbmRlcmFibGV9XHJcbiAgICAgKi9cclxuICAgIGNyZWF0ZUNoYXJ0KGpzb24pIHtcclxuICAgICAgICBjb25zb2xlLmxvZygnQ3JlYXRpbmcgQ2hhcnQnLCBqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy5fYWRkUGFja2FnZXMoanNvbi5wYWNrYWdlcyk7IC8vIFRPRE86IG1vdmUgdGhpcyBpbnRvIHRoZSBzdG9yZSBtZXRob2Q/XHJcblxyXG4gICAgICAgIHJldHVybiBuZXcgdGhpcy5DaGFydChqc29uKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhbmQgc3RvcmUgYSBuZXcgQ2hhcnQgZnJvbSBhIEpTT04gcGF5bG9hZC5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAc2VlIGNyZWF0ZUNoYXJ0XHJcbiAgICAgKiBAcGFyYW0ge29iamVjdH0ganNvblxyXG4gICAgICovXHJcbiAgICBhZGROZXdDaGFydChqc29uKSB7IC8vVE9ETzogcmVuYW1lIHRvIHN0b3JlTmV3Q2hhcnQoanNvbikgP1xyXG4gICAgICAgIHRoaXMuc3RvcmUodGhpcy5jcmVhdGVDaGFydChqc29uKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBDcmVhdGUgYSBuZXcgRGFzaGJvYXJkIHdpdGggYSBnaXZlbiBsYWJlbC5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgSlNPTiBwYXlsb2FkIGNvbWVzIGZyb20gdGhlIFBIUCBEYXNoYm9hcmQgY2xhc3MuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtICB7b2JqZWN0fSBqc29uXHJcbiAgICAgKiBAcmV0dXJuIHtEYXNoYm9hcmR9XHJcbiAgICAgKi9cclxuICAgIGNyZWF0ZURhc2hib2FyZChqc29uKSB7XHJcbiAgICAgICAgY29uc29sZS5sb2coJ0NyZWF0aW5nIERhc2hib2FyZCcsIGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLl9hZGRQYWNrYWdlcyhqc29uLnBhY2thZ2VzKTtcclxuXHJcbiAgICAgICAgcmV0dXJuIG5ldyB0aGlzLkRhc2hib2FyZChqc29uKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENyZWF0ZSBhbmQgc3RvcmUgYSBuZXcgRGFzaGJvYXJkIGZyb20gYSBKU09OIHBheWxvYWQuXHJcbiAgICAgKlxyXG4gICAgICogVGhlIEpTT04gcGF5bG9hZCBjb21lcyBmcm9tIHRoZSBQSFAgRGFzaGJvYXJkIGNsYXNzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBzZWUgY3JlYXRlRGFzaGJvYXJkXHJcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGpzb25cclxuICAgICAqIEByZXR1cm4ge0Rhc2hib2FyZH1cclxuICAgICAqL1xyXG4gICAgYWRkTmV3RGFzaGJvYXJkKGpzb24pIHsgLy9UT0RPOiByZW5hbWUgdG8gc3RvcmVOZXdEYXNoYm9hcmQoanNvbikgP1xyXG4gICAgICAgIHRoaXMuc3RvcmUodGhpcy5jcmVhdGVEYXNoYm9hcmQoanNvbikpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogUHVibGljIG1ldGhvZCBmb3IgaW5pdGlhbGl6aW5nIGdvb2dsZSBvbiB0aGUgcGFnZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKi9cclxuICAgIGluaXQoKSB7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMuX2xvYWRHb29nbGUoKS50aGVuKCgpID0+IHtcclxuICAgICAgICAgICAgdGhpcy52aXN1YWxpemF0aW9uID0gZ29vZ2xlLnZpc3VhbGl6YXRpb247XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSdW5zIHRoZSBMYXZhLmpzIG1vZHVsZVxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgcnVuKCkge1xyXG4gICAgICAgIC8vIGNvbnN0ICRsYXZhID0gdGhpcztcclxuXHJcbiAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBSdW5uaW5nLi4uJyk7XHJcbiAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBMb2FkaW5nIG9wdGlvbnM6JywgdGhpcy5vcHRpb25zKTtcclxuXHJcbiAgICAgICAgdGhpcy5fYXR0YWNoUmVkcmF3SGFuZGxlcigpO1xyXG5cclxuICAgICAgICB0aGlzLmluaXQoKS50aGVuKCgpID0+IHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBHb29nbGUgaXMgcmVhZHkuJyk7XHJcblxyXG4gICAgICAgICAgICBfZm9ySW4odGhpcy5fcmVuZGVyYWJsZXMsIHJlbmRlcmFibGUgPT4ge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBSZW5kZXJpbmcgJHtyZW5kZXJhYmxlLnV1aWQoKX1gKTtcclxuXHJcbiAgICAgICAgICAgICAgICByZW5kZXJhYmxlLnJlbmRlcigpO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gRmlyaW5nIFwicmVhZHlcIiBldmVudC4nKTtcclxuICAgICAgICAgICAgdGhpcy5lbWl0KCdyZWFkeScpO1xyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBFeGVjdXRpbmcgbGF2YS5yZWFkeShjYWxsYmFjayknKTtcclxuICAgICAgICAgICAgdGhpcy5fcmVhZHlDYWxsYmFjaygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU3RvcmVzIGEgcmVuZGVyYWJsZSBsYXZhIG9iamVjdCB3aXRoaW4gdGhlIG1vZHVsZS5cclxuICAgICAqXHJcbiAgICAgKiBAcGFyYW0ge1JlbmRlcmFibGV9IHJlbmRlcmFibGVcclxuICAgICAqL1xyXG4gICAgc3RvcmUocmVuZGVyYWJsZSkge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gU3RvcmluZyAke3JlbmRlcmFibGUudXVpZCgpfWApO1xyXG5cclxuICAgICAgICB0aGlzLl9yZW5kZXJhYmxlc1tyZW5kZXJhYmxlLmxhYmVsXSA9IHJlbmRlcmFibGU7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSZXR1cm5zIHRoZSBMYXZhQ2hhcnQgamF2YXNjcmlwdCBvYmplY3RzXHJcbiAgICAgKlxyXG4gICAgICpcclxuICAgICAqIFRoZSBMYXZhQ2hhcnQgb2JqZWN0IGhvbGRzIGFsbCB0aGUgdXNlciBkZWZpbmVkIHByb3BlcnRpZXMgc3VjaCBhcyBkYXRhLCBvcHRpb25zLCBmb3JtYXRzLFxyXG4gICAgICogdGhlIEdvb2dsZUNoYXJ0IG9iamVjdCwgYW5kIHJlbGF0aXZlIG1ldGhvZHMgZm9yIGludGVybmFsIHVzZS5cclxuICAgICAqXHJcbiAgICAgKiBUaGUgR29vZ2xlQ2hhcnQgb2JqZWN0IGlzIGF2YWlsYWJsZSBhcyBcIi5jaGFydFwiIGZyb20gdGhlIHJldHVybmVkIExhdmFDaGFydC5cclxuICAgICAqIEl0IGNhbiBiZSB1c2VkIHRvIGFjY2VzcyBhbnkgb2YgdGhlIGF2YWlsYWJsZSBtZXRob2RzIHN1Y2ggYXNcclxuICAgICAqIGdldEltYWdlVVJJKCkgb3IgZ2V0Q2hhcnRMYXlvdXRJbnRlcmZhY2UoKS5cclxuICAgICAqIFNlZSBodHRwczovL2dvb2dsZS1kZXZlbG9wZXJzLmFwcHNwb3QuY29tL2NoYXJ0L2ludGVyYWN0aXZlL2RvY3MvZ2FsbGVyeS9saW5lY2hhcnQjbWV0aG9kc1xyXG4gICAgICogZm9yIHNvbWUgZXhhbXBsZXMgcmVsYXRpdmUgdG8gTGluZUNoYXJ0cy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0gIHtzdHJpbmd9ICAgbGFiZWxcclxuICAgICAqIEBwYXJhbSAge0Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICogQHRocm93cyBJbnZhbGlkTGFiZWxcclxuICAgICAqIEB0aHJvd3MgSW52YWxpZENhbGxiYWNrXHJcbiAgICAgKiBAdGhyb3dzIFJlbmRlcmFibGVOb3RGb3VuZFxyXG4gICAgICovXHJcbiAgICBnZXQobGFiZWwsIGNhbGxiYWNrKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgSW52YWxpZENhbGxiYWNrKGNhbGxiYWNrKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGxldCByZW5kZXJhYmxlID0gdGhpcy5fcmVuZGVyYWJsZXNbbGFiZWxdO1xyXG5cclxuICAgICAgICBpZiAoISByZW5kZXJhYmxlKSB7XHJcbiAgICAgICAgICAgIHRocm93IG5ldyBSZW5kZXJhYmxlTm90Rm91bmQobGFiZWwpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgY2FsbGJhY2socmVuZGVyYWJsZSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBc3NpZ25zIGEgY2FsbGJhY2sgZm9yIHdoZW4gdGhlIGNoYXJ0cyBhcmUgcmVhZHkgdG8gYmUgaW50ZXJhY3RlZCB3aXRoLlxyXG4gICAgICpcclxuICAgICAqIFRoaXMgaXMgdXNlZCB0byB3cmFwIGNhbGxzIHRvIGxhdmEubG9hZERhdGEoKSBvciBsYXZhLmxvYWRPcHRpb25zKClcclxuICAgICAqIHRvIHByb3RlY3QgYWdhaW5zdCBhY2Nlc3NpbmcgY2hhcnRzIHRoYXQgYXJlbid0IGxvYWRlZCB5ZXRcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICovXHJcbiAgICByZWFkeShjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrID0gY2FsbGJhY2s7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgZGF0YSBpbnRvIHRoZSBjaGFydCBhbmQgcmVkcmF3cy5cclxuICAgICAqXHJcbiAgICAgKlxyXG4gICAgICogVXNlZCB3aXRoIGFuIEFKQVggY2FsbCB0byBhIFBIUCBtZXRob2QgcmV0dXJuaW5nIERhdGFUYWJsZS0+dG9Kc29uKCksXHJcbiAgICAgKiBhIGNoYXJ0IGNhbiBiZSBkeW5hbWljYWxseSB1cGRhdGUgaW4gcGFnZSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWREYXRhKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldERhdGEoanNvbik7XHJcblxyXG4gICAgICAgICAgICBpZiAodHlwZW9mIGpzb24uZm9ybWF0cyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICAgICAgICAgIGNoYXJ0LmFwcGx5Rm9ybWF0cyhqc29uLmZvcm1hdHMpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBjaGFydC5kcmF3KCk7XHJcblxyXG4gICAgICAgICAgICBjYWxsYmFjayhjaGFydCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgb3B0aW9ucyBpbnRvIGEgY2hhcnQgYW5kIHJlZHJhd3MuXHJcbiAgICAgKlxyXG4gICAgICpcclxuICAgICAqIFVzZWQgd2l0aCBhbiBBSkFYIGNhbGwsIG9yIGphdmFzY3JpcHQgZXZlbnRzLCB0byBsb2FkIGEgbmV3IGFycmF5IG9mIG9wdGlvbnMgaW50byBhIGNoYXJ0LlxyXG4gICAgICogVGhpcyBjYW4gYmUgdXNlZCB0byB1cGRhdGUgYSBjaGFydCBkeW5hbWljYWxseSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWRPcHRpb25zKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gY2FsbGJhY2sgfHwgbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldE9wdGlvbnMoanNvbik7XHJcbiAgICAgICAgICAgIGNoYXJ0LmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGNhbGxiYWNrKGNoYXJ0KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJlZHJhd3MgYWxsIG9mIHRoZSByZWdpc3RlcmVkIGNoYXJ0cyBvbiBzY3JlZW4uXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBtZXRob2QgaXMgYXR0YWNoZWQgdG8gdGhlIHdpbmRvdyByZXNpemUgZXZlbnQgd2l0aCBkZWJvdW5jaW5nXHJcbiAgICAgKiB0byBtYWtlIHRoZSBjaGFydHMgcmVzcG9uc2l2ZSB0byB0aGUgYnJvd3NlciByZXNpemluZy5cclxuICAgICAqL1xyXG4gICAgcmVkcmF3QWxsKCkge1xyXG4gICAgICAgIGlmICh0aGlzLl9yZW5kZXJhYmxlcy5sZW5ndGggPT09IDApIHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBOb3RoaW5nIHRvIHJlZHJhdy5gKTtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhgW2xhdmEuanNdIFJlZHJhd2luZyAke3RoaXMuX3JlbmRlcmFibGVzLmxlbmd0aH0gcmVuZGVyYWJsZXMuYCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBmb3IgKGxldCByZW5kZXJhYmxlIG9mIHRoaXMuX3JlbmRlcmFibGVzKSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gUmVkcmF3aW5nICR7cmVuZGVyYWJsZS51dWlkKCl9YCk7XHJcblxyXG4gICAgICAgICAgICBsZXQgcmVkcmF3ID0gcmVuZGVyYWJsZS5kcmF3LmJpbmQocmVuZGVyYWJsZSk7XHJcblxyXG4gICAgICAgICAgICByZWRyYXcoKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQWxpYXNpbmcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uYXJyYXlUb0RhdGFUYWJsZSB0byBsYXZhLmFycmF5VG9EYXRhVGFibGVcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0ge0FycmF5fSBhcnJcclxuICAgICAqIEByZXR1cm4ge2dvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZX1cclxuICAgICAqL1xyXG4gICAgYXJyYXlUb0RhdGFUYWJsZShhcnIpIHtcclxuICAgICAgICByZXR1cm4gdGhpcy52aXN1YWxpemF0aW9uLmFycmF5VG9EYXRhVGFibGUoYXJyKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIEFkZHMgdG8gdGhlIGxpc3Qgb2YgcGFja2FnZXMgdGhhdCBHb29nbGUgbmVlZHMgdG8gbG9hZC5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHBhcmFtIHtBcnJheX0gcGFja2FnZXNcclxuICAgICAqIEByZXR1cm4ge0FycmF5fVxyXG4gICAgICovXHJcbiAgICBfYWRkUGFja2FnZXMocGFja2FnZXMpIHtcclxuICAgICAgICB0aGlzLl9wYWNrYWdlcyA9IHRoaXMuX3BhY2thZ2VzLmNvbmNhdChwYWNrYWdlcyk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBdHRhY2ggYSBsaXN0ZW5lciB0byB0aGUgd2luZG93IHJlc2l6ZSBldmVudCBmb3IgcmVkcmF3aW5nIHRoZSBjaGFydHMuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2F0dGFjaFJlZHJhd0hhbmRsZXIoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5yZXNwb25zaXZlID09PSB0cnVlKSB7XHJcbiAgICAgICAgICAgIGxldCBkZWJvdW5jZWQgPSBudWxsO1xyXG5cclxuICAgICAgICAgICAgYWRkRXZlbnQod2luZG93LCAncmVzaXplJywgKCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgLy8gbGV0IHJlZHJhdyA9IHRoaXMucmVkcmF3QWxsKCkuYmluZCh0aGlzKTtcclxuXHJcbiAgICAgICAgICAgICAgICBjbGVhclRpbWVvdXQoZGVib3VuY2VkKTtcclxuXHJcbiAgICAgICAgICAgICAgICBkZWJvdW5jZWQgPSBzZXRUaW1lb3V0KCgpID0+IHtcclxuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFdpbmRvdyByZS1zaXplZCwgcmVkcmF3aW5nLi4uJyk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIHJlZHJhdygpO1xyXG4gICAgICAgICAgICAgICAgICAgIHRoaXMucmVkcmF3QWxsKClcclxuICAgICAgICAgICAgICAgIH0sIHRoaXMub3B0aW9ucy5kZWJvdW5jZV90aW1lb3V0KTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogTG9hZCB0aGUgR29vZ2xlIFN0YXRpYyBMb2FkZXIgYW5kIHJlc29sdmUgdGhlIHByb21pc2Ugd2hlbiByZWFkeS5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICovXHJcbiAgICBfbG9hZEdvb2dsZSgpIHtcclxuICAgICAgICBjb25zdCAkbGF2YSA9IHRoaXM7XHJcblxyXG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZShyZXNvbHZlID0+IHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBSZXNvbHZpbmcgR29vZ2xlLi4uJyk7XHJcblxyXG4gICAgICAgICAgICBpZiAodGhpcy5fZ29vZ2xlSXNMb2FkZWQoKSkge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBTdGF0aWMgbG9hZGVyIGZvdW5kLCBpbml0aWFsaXppbmcgd2luZG93Lmdvb2dsZScpO1xyXG5cclxuICAgICAgICAgICAgICAgICRsYXZhLl9nb29nbGVDaGFydExvYWRlcihyZXNvbHZlKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gU3RhdGljIGxvYWRlciBub3QgZm91bmQsIGFwcGVuZGluZyB0byBoZWFkJyk7XHJcblxyXG4gICAgICAgICAgICAgICAgJGxhdmEuX2FkZEdvb2dsZVNjcmlwdFRvSGVhZChyZXNvbHZlKTtcclxuICAgICAgICAgICAgICAgIC8vIFRoaXMgd2lsbCBjYWxsICRsYXZhLl9nb29nbGVDaGFydExvYWRlcihyZXNvbHZlKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ2hlY2sgaWYgR29vZ2xlJ3MgU3RhdGljIExvYWRlciBpcyBpbiBwYWdlLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cclxuICAgICAqL1xyXG4gICAgX2dvb2dsZUlzTG9hZGVkKCkge1xyXG4gICAgICAgIGNvbnN0IHNjcmlwdHMgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnc2NyaXB0Jyk7XHJcblxyXG4gICAgICAgIGZvciAobGV0IHNjcmlwdCBvZiBzY3JpcHRzKSB7XHJcbiAgICAgICAgICAgIGlmIChzY3JpcHQuc3JjID09PSB0aGlzLkdPT0dMRV9MT0FERVJfVVJMKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJ1bnMgdGhlIEdvb2dsZSBjaGFydCBsb2FkZXIgYW5kIHJlc29sdmVzIHRoZSBwcm9taXNlLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKiBAcGFyYW0ge1Byb21pc2UucmVzb2x2ZX0gcmVzb2x2ZVxyXG4gICAgICovXHJcbiAgICBfZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSkge1xyXG4gICAgICAgIGxldCBjb25maWcgPSB7XHJcbiAgICAgICAgICAgIHBhY2thZ2VzOiB0aGlzLl9wYWNrYWdlcyxcclxuICAgICAgICAgICAgbGFuZ3VhZ2U6IHRoaXMub3B0aW9ucy5sb2NhbGVcclxuICAgICAgICB9O1xyXG5cclxuICAgICAgICBpZiAodGhpcy5vcHRpb25zLm1hcHNfYXBpX2tleSAhPT0gJycpIHtcclxuICAgICAgICAgICAgY29uZmlnLm1hcHNBcGlLZXkgPSB0aGlzLm9wdGlvbnMubWFwc19hcGlfa2V5O1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBMb2FkaW5nIEdvb2dsZSB3aXRoIGNvbmZpZzonLCBjb25maWcpO1xyXG5cclxuICAgICAgICBnb29nbGUuY2hhcnRzLmxvYWQodGhpcy5HT09HTEVfQVBJX1ZFUlNJT04sIGNvbmZpZyk7XHJcblxyXG4gICAgICAgIGdvb2dsZS5jaGFydHMuc2V0T25Mb2FkQ2FsbGJhY2socmVzb2x2ZSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBDcmVhdGUgYSBuZXcgc2NyaXB0IHRhZyBmb3IgdGhlIEdvb2dsZSBTdGF0aWMgTG9hZGVyLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKiBAcGFyYW0ge1Byb21pc2UucmVzb2x2ZX0gcmVzb2x2ZVxyXG4gICAgICogQHJldHVybnMge0VsZW1lbnR9XHJcbiAgICAgKi9cclxuICAgIF9hZGRHb29nbGVTY3JpcHRUb0hlYWQocmVzb2x2ZSkge1xyXG4gICAgICAgIGxldCAkbGF2YSA9IHRoaXM7XHJcbiAgICAgICAgbGV0IHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NjcmlwdCcpO1xyXG5cclxuICAgICAgICBzY3JpcHQudHlwZSA9ICd0ZXh0L2phdmFzY3JpcHQnO1xyXG4gICAgICAgIHNjcmlwdC5hc3luYyA9IHRydWU7XHJcbiAgICAgICAgc2NyaXB0LnNyYyA9IHRoaXMuR09PR0xFX0xPQURFUl9VUkw7XHJcbiAgICAgICAgc2NyaXB0Lm9ubG9hZCA9IHNjcmlwdC5vbnJlYWR5c3RhdGVjaGFuZ2UgPSBmdW5jdGlvbiAoZXZlbnQpIHtcclxuICAgICAgICAgICAgZXZlbnQgPSBldmVudCB8fCB3aW5kb3cuZXZlbnQ7XHJcblxyXG4gICAgICAgICAgICBpZiAoZXZlbnQudHlwZSA9PT0gJ2xvYWQnIHx8ICgvbG9hZGVkfGNvbXBsZXRlLy50ZXN0KHRoaXMucmVhZHlTdGF0ZSkpKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLm9ubG9hZCA9IHRoaXMub25yZWFkeXN0YXRlY2hhbmdlID0gbnVsbDtcclxuXHJcbiAgICAgICAgICAgICAgICAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG5cclxuICAgICAgICBkb2N1bWVudC5oZWFkLmFwcGVuZENoaWxkKHNjcmlwdCk7XHJcbiAgICB9XHJcbn1cclxuIiwiY29uc3QgZGVmYXVsdE9wdGlvbnMgPSB7XG4gICAgXCJhdXRvX3J1blwiICAgICAgICA6IGZhbHNlLFxuICAgIFwibG9jYWxlXCIgICAgICAgICAgOiBcImVuXCIsXG4gICAgXCJ0aW1lem9uZVwiICAgICAgICA6IFwiQW1lcmljYS9Mb3NfQW5nZWxlc1wiLFxuICAgIFwiZGF0ZXRpbWVfZm9ybWF0XCIgOiBcIlwiLFxuICAgIFwibWFwc19hcGlfa2V5XCIgICAgOiBcIlwiLFxuICAgIFwicmVzcG9uc2l2ZVwiICAgICAgOiB0cnVlLFxuICAgIFwiZGVib3VuY2VfdGltZW91dFwiOiAyNTBcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmF1bHRPcHRpb25zO1xuIiwiLyoqXHJcbiAqIENoYXJ0IGNsYXNzIHVzZWQgZm9yIHN0b3JpbmcgYWxsIHRoZSBuZWVkZWQgY29uZmlndXJhdGlvbiBmb3IgcmVuZGVyaW5nLlxyXG4gKlxyXG4gKiBAdHlwZWRlZiB7RnVuY3Rpb259ICBDaGFydFxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICBsYWJlbCAgICAgLSBMYWJlbCBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge3N0cmluZ30gICB0eXBlICAgICAgLSBUeXBlIG9mIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBlbGVtZW50ICAgLSBIdG1sIGVsZW1lbnQgaW4gd2hpY2ggdG8gcmVuZGVyIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgY2hhcnQgICAgIC0gR29vZ2xlIGNoYXJ0IG9iamVjdC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgcGFja2FnZSAgIC0gVHlwZSBvZiBHb29nbGUgY2hhcnQgcGFja2FnZSB0byBsb2FkLlxyXG4gKiBAcHJvcGVydHkge2Jvb2xlYW59ICBwbmdPdXRwdXQgLSBTaG91bGQgdGhlIGNoYXJ0IGJlIGRpc3BsYXllZCBhcyBhIFBORy5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZGF0YSAgICAgIC0gRGF0YXRhYmxlIGZvciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIG9wdGlvbnMgICAtIENvbmZpZ3VyYXRpb24gb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBmb3JtYXRzICAgLSBGb3JtYXR0ZXJzIHRvIGFwcGx5IHRvIHRoZSBjaGFydCBkYXRhLlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBwcm9taXNlcyAgLSBQcm9taXNlcyB1c2VkIGluIHRoZSByZW5kZXJpbmcgY2hhaW4uXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IGluaXQgICAgICAtIEluaXRpYWxpemVzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gY29uZmlndXJlIC0gQ29uZmlndXJlcyB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IHJlbmRlciAgICAtIFJlbmRlcnMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSB1dWlkICAgICAgLSBDcmVhdGVzIGlkZW50aWZpY2F0aW9uIHN0cmluZyBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBfZXJyb3JzICAgLSBDb2xsZWN0aW9uIG9mIGVycm9ycyB0byBiZSB0aHJvd24uXHJcbiAqL1xyXG5pbXBvcnQgeyBFbGVtZW50SWROb3RGb3VuZCB9IGZyb20gXCIuL0Vycm9ycy5lczZcIjtcclxuaW1wb3J0IHsgZ2V0VHlwZSB9IGZyb20gXCIuL1V0aWxzLmVzNlwiXHJcblxyXG4vKipcclxuICogQ2hhcnQgbW9kdWxlXHJcbiAqXHJcbiAqIEBjbGFzcyAgICAgQ2hhcnRcclxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmV4cG9ydCBjbGFzcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIC8qKlxyXG4gICAgICogQ2hhcnQgQ2xhc3NcclxuICAgICAqXHJcbiAgICAgKiBUaGlzIGlzIHRoZSBqYXZhc2NyaXB0IHZlcnNpb24gb2YgYSBsYXZhY2hhcnQgd2l0aCBtZXRob2RzIGZvciBpbnRlcmFjdGluZyB3aXRoXHJcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGpzb25cclxuICAgICAqIEBjb25zdHJ1Y3RvclxyXG4gICAgICovXHJcbiAgICBjb25zdHJ1Y3Rvcihqc29uKSB7XHJcbiAgICAgICAgdGhpcy5nY2hhcnQgICAgPSBudWxsO1xyXG4gICAgICAgIHRoaXMubGFiZWwgICAgID0ganNvbi5sYWJlbDtcclxuICAgICAgICB0aGlzLm9wdGlvbnMgICA9IGpzb24ub3B0aW9ucztcclxuICAgICAgICB0aGlzLmVsZW1lbnRJZCA9IGpzb24uZWxlbWVudElkO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0aGlzLmVsZW1lbnRJZCk7XHJcblxyXG4gICAgICAgIGlmICghIHRoaXMuZWxlbWVudCkge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgRWxlbWVudElkTm90Rm91bmQodGhpcy5lbGVtZW50SWQpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgQ2hhcnQuXHJcbiAgICAgKlxyXG4gICAgICogQHJldHVybiB7c3RyaW5nfVxyXG4gICAgICovXHJcbiAgICB1dWlkKCkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLnR5cGUrJzo6Jyt0aGlzLmxhYmVsO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IHdpdGggdGhlIHByZXNldCBkYXRhIGFuZCBvcHRpb25zLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgZHJhdygpIHtcclxuICAgICAgICB0aGlzLmdjaGFydC5kcmF3KHRoaXMuZGF0YSwgdGhpcy5vcHRpb25zKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFNldHMgdGhlIGRhdGEgZm9yIHRoZSBjaGFydCBieSBjcmVhdGluZyBhIG5ldyBEYXRhVGFibGVcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAZXh0ZXJuYWwgXCJnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGVcIlxyXG4gICAgICogQHNlZSAgIHtAbGluayBodHRwczovL2RldmVsb3BlcnMuZ29vZ2xlLmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL3JlZmVyZW5jZSNEYXRhVGFibGV8RGF0YVRhYmxlIENsYXNzfVxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBheWxvYWQgSnNvbiByZXByZXNlbnRhdGlvbiBvZiBhIERhdGFUYWJsZVxyXG4gICAgICovXHJcbiAgICBzZXREYXRhKHBheWxvYWQpIHtcclxuICAgICAgICAvLyBJZiB0aGUgcGF5bG9hZCBpcyBmcm9tIEpvaW5lZERhdGFUYWJsZTo6dG9Kc29uKCksIHRoZW4gY3JlYXRlXHJcbiAgICAgICAgLy8gdHdvIG5ldyBEYXRhVGFibGVzIGFuZCBqb2luIHRoZW0gd2l0aCB0aGUgZGVmaW5lZCBvcHRpb25zLlxyXG4gICAgICAgIGlmIChnZXRUeXBlKHBheWxvYWQuZGF0YSkgPT09ICdBcnJheScpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gZ29vZ2xlLnZpc3VhbGl6YXRpb24uZGF0YS5qb2luKFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMF0pLFxyXG4gICAgICAgICAgICAgICAgbmV3IGdvb2dsZS52aXN1YWxpemF0aW9uLkRhdGFUYWJsZShwYXlsb2FkLmRhdGFbMV0pLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5rZXlzLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5qb2luTWV0aG9kLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zLFxyXG4gICAgICAgICAgICAgICAgcGF5bG9hZC5kdDJDb2x1bW5zXHJcbiAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvLyBTaW5jZSBHb29nbGUgY29tcGlsZXMgdGhlaXIgY2xhc3Nlcywgd2UgY2FuJ3QgdXNlIGluc3RhbmNlb2YgdG8gY2hlY2sgc2luY2VcclxuICAgICAgICAvLyBpdCBpcyBubyBsb25nZXIgY2FsbGVkIGEgXCJEYXRhVGFibGVcIiAoaXQncyBcImd2anNfUFwiIGJ1dCB0aGF0IGNvdWxkIGNoYW5nZS4uLilcclxuICAgICAgICBpZiAoZ2V0VHlwZShwYXlsb2FkLmdldFRhYmxlUHJvcGVydGllcykgPT09ICdGdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhpcy5kYXRhID0gcGF5bG9hZDtcclxuXHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIElmIGEgRGF0YVRhYmxlI3RvSnNvbigpIHBheWxvYWQgaXMgcmVjZWl2ZWQsIHdpdGggZm9ybWF0dGVkIGNvbHVtbnMsXHJcbiAgICAgICAgLy8gdGhlbiBwYXlsb2FkLmRhdGEgd2lsbCBiZSBkZWZpbmVkLCBhbmQgdXNlZCBhcyB0aGUgRGF0YVRhYmxlXHJcbiAgICAgICAgaWYgKGdldFR5cGUocGF5bG9hZC5kYXRhKSA9PT0gJ09iamVjdCcpIHtcclxuICAgICAgICAgICAgcGF5bG9hZCA9IHBheWxvYWQuZGF0YTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy8gVE9ETzogaGFuZGxlIGZvcm1hdHMgYmV0dGVyLi4uXHJcblxyXG4gICAgICAgIC8vIElmIHdlIHJlYWNoIGhlcmUsIHRoZW4gaXQgbXVzdCBiZSBzdGFuZGFyZCBKU09OIGZvciBjcmVhdGluZyBhIERhdGFUYWJsZS5cclxuICAgICAgICB0aGlzLmRhdGEgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGF0YVRhYmxlKHBheWxvYWQpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU2V0cyB0aGUgb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zXHJcbiAgICAgKi9cclxuICAgIHNldE9wdGlvbnMob3B0aW9ucykge1xyXG4gICAgICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoganNoaW50IHVuZGVmOiB0cnVlLCB1bnVzZWQ6IHRydWUgKi9cclxuLyogZ2xvYmFscyBkb2N1bWVudCAqL1xyXG5cclxuLyoqXHJcbiAqIEZ1bmN0aW9uIHRoYXQgZG9lcyBub3RoaW5nLlxyXG4gKlxyXG4gKiBAcmV0dXJuIHt1bmRlZmluZWR9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gbm9vcCgpIHtcclxuICAgIHJldHVybiB1bmRlZmluZWQ7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZXR1cm4gdGhlIHR5cGUgb2Ygb2JqZWN0LlxyXG4gKlxyXG4gKiBAcGFyYW0ge29iamVjdH0gb2JqZWN0XHJcbiAqIEByZXR1cm4ge21peGVkfVxyXG4gKi9cclxuZXhwb3J0IGZ1bmN0aW9uIGdldFR5cGUob2JqZWN0KSB7XHJcbiAgICBsZXQgdHlwZSA9IE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChvYmplY3QpO1xyXG5cclxuICAgIHJldHVybiB0eXBlLnJlcGxhY2UoJ1tvYmplY3QgJywnJykucmVwbGFjZSgnXScsJycpO1xyXG59XHJcblxyXG4vKipcclxuICogU2ltcGxlIFByb21pc2UgZm9yIHRoZSBET00gdG8gYmUgcmVhZHkuXHJcbiAqXHJcbiAqIEByZXR1cm4ge1Byb21pc2V9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gZG9tTG9hZGVkKCkge1xyXG4gICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xyXG4gICAgICAgIGlmIChkb2N1bWVudC5yZWFkeVN0YXRlID09PSAnaW50ZXJhY3RpdmUnIHx8IGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdjb21wbGV0ZScpIHtcclxuICAgICAgICAgICAgcmVzb2x2ZSgpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCByZXNvbHZlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxufVxyXG5cclxuLyoqXHJcbiAqIE1ldGhvZCBmb3IgYXR0YWNoaW5nIGV2ZW50cyB0byBvYmplY3RzLlxyXG4gKlxyXG4gKiBDcmVkaXQgdG8gQWxleCBWLlxyXG4gKlxyXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzMyNzkzNC9hbGV4LXZcclxuICogQGxpbmsgaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzE1MDEzOVxyXG4gKiBAcGFyYW0ge29iamVjdH0gdGFyZ2V0XHJcbiAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlXHJcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAqIEBwYXJhbSB7Ym9vbH0gZXZlbnRSZXR1cm5cclxuICovXHJcbmV4cG9ydCBmdW5jdGlvbiBhZGRFdmVudCh0YXJnZXQsIHR5cGUsIGNhbGxiYWNrLCBldmVudFJldHVybilcclxue1xyXG4gICAgaWYgKHRhcmdldCA9PT0gbnVsbCB8fCB0eXBlb2YgdGFyZ2V0ID09PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIHJldHVybjtcclxuICAgIH1cclxuXHJcbiAgICBpZiAodGFyZ2V0LmFkZEV2ZW50TGlzdGVuZXIpIHtcclxuICAgICAgICB0YXJnZXQuYWRkRXZlbnRMaXN0ZW5lcih0eXBlLCBjYWxsYmFjaywgISFldmVudFJldHVybik7XHJcbiAgICB9XHJcbiAgICBlbHNlIGlmKHRhcmdldC5hdHRhY2hFdmVudCkge1xyXG4gICAgICAgIHRhcmdldC5hdHRhY2hFdmVudChcIm9uXCIgKyB0eXBlLCBjYWxsYmFjayk7XHJcbiAgICB9XHJcbiAgICBlbHNlIHtcclxuICAgICAgICB0YXJnZXRbXCJvblwiICsgdHlwZV0gPSBjYWxsYmFjaztcclxuICAgIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIEdldCBhIGZ1bmN0aW9uIGEgYnkgaXRzJyBuYW1lc3BhY2VkIHN0cmluZyBuYW1lIHdpdGggY29udGV4dC5cclxuICpcclxuICogQ3JlZGl0IHRvIEphc29uIEJ1bnRpbmdcclxuICpcclxuICogQGxpbmsgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS91c2Vycy8xNzkwL2phc29uLWJ1bnRpbmdcclxuICogQGxpbmsgaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS9hLzM1OTkxMFxyXG4gKiBAcGFyYW0ge3N0cmluZ30gZnVuY3Rpb25OYW1lXHJcbiAqIEBwYXJhbSB7b2JqZWN0fSBjb250ZXh0XHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gc3RyaW5nVG9GdW5jdGlvbihmdW5jdGlvbk5hbWUsIGNvbnRleHQpIHtcclxuICAgIGxldCBuYW1lc3BhY2VzID0gZnVuY3Rpb25OYW1lLnNwbGl0KCcuJyk7XHJcbiAgICBsZXQgZnVuYyA9IG5hbWVzcGFjZXMucG9wKCk7XHJcblxyXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBuYW1lc3BhY2VzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgY29udGV4dCA9IGNvbnRleHRbbmFtZXNwYWNlc1tpXV07XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIGNvbnRleHRbZnVuY107XHJcbn1cclxuIl19
