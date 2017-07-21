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
        _this.formats = json.formats || null;

        _this.events = _typeof(json.events) === 'object' ? json.events : null;
        _this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        _this.render = function () {
            _this.setData(json.datatable);

            var ChartClass = (0, _Utils.stringToFunction)(_this.class, window);

            _this.gchart = new ChartClass(_this.element);

            // <formats>

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
         * Formats columns of the DataTable.
         *
         * @public
         * @param {Array.<Object>} formatArr Array of format definitions
         */

    }, {
        key: 'applyFormats',
        value: function applyFormats(formatArr) {
            for (var a = 0; a < formatArr.length; a++) {
                var formatJson = formatArr[a];
                var formatter = new google.visualization[formatJson.type](formatJson.config);

                formatter.format(this.data, formatJson.index);
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

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

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

                    var _binding = _slicedToArray(binding, 2),
                        controlWrapper = _binding[0],
                        chartWrapper = _binding[1];

                    this.gchart.bind(new google.visualization.ControlWrapper(controlWrapper[0]), new google.visualization.ChartWrapper(chartWrapper[0]));
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

/**
 * ChartNotFound Error
 *
 * Thrown when when the getChart() method cannot find a chart with the given label.
 *
 * @type {function}
 */


var RenderableNotFound = exports.RenderableNotFound = function (_LavaError4) {
    _inherits(RenderableNotFound, _LavaError4);

    function RenderableNotFound(label) {
        _classCallCheck(this, RenderableNotFound);

        var _this5 = _possibleConstructorReturn(this, (RenderableNotFound.__proto__ || Object.getPrototypeOf(RenderableNotFound)).call(this, '[lava.js] Chart with label "' + label + '" was not found.'));

        _this5.name = 'ChartNotFound';
        return _this5;
    }

    return RenderableNotFound;
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
     * @return {Chart}
     */


    _createClass(LavaJs, [{
        key: 'createChart',
        value: function createChart(json) {
            console.log('JSON_PAYLOAD', json);

            this._addPackages(json.packages);

            this.store(new this.Chart(json));
        }

        /**
         * Create and store a new Chart from a JSON payload.
         *
         * @public
         * @see createChart
         * @param  {object} json
         * @return {Chart}
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

                console.log('[lava.js] Executing lava.ready(callback)');
                $lava._readyCallback();

                console.log('[lava.js] Firing "ready" event.');
                $lava.emit('ready');
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
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.Renderable = undefined;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

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


var _Errors = require('./Errors.es6');

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
        key: 'uuid',
        value: function uuid() {
            return this.type + '::' + this.label;
        }

        /**
         * Draws the chart with the preset data and options.
         *
         * @public
         */

    }, {
        key: 'draw',
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
        key: 'setData',
        value: function setData(payload) {
            // If a DataTable#toJson() payload is received, with formatted columns,
            // then payload.data will be defined, and used as the DataTable
            if (_typeof(payload.data) === 'object') {
                payload = payload.data;
            }

            // Since Google compiles their classes, we can't use instanceof to check since
            // it is no longer called a "DataTable" (it's "gvjs_P" but that could change...)
            if (typeof payload.getTableProperties === 'function') {
                this.data = payload;

                // Otherwise assume it is a JSON representation of a DataTable and create one.
            } else {
                this.data = new google.visualization.DataTable(payload);
            }
        }

        /**
         * Sets the options for the chart.
         *
         * @public
         * @param {object} options
         */

    }, {
        key: 'setOptions',
        value: function setOptions(options) {
            this.options = options;
        }
    }]);

    return Renderable;
}();

},{"./Errors.es6":37}],40:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.noop = noop;
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJub2RlX21vZHVsZXMvZXZlbnRzL2V2ZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX1N5bWJvbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2FycmF5TGlrZUtleXMuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlRm9yLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZUdldFRhZy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2Jhc2VJc1R5cGVkQXJyYXkuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19iYXNlS2V5c0luLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVRpbWVzLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fYmFzZVVuYXJ5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY2FzdEZ1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fY3JlYXRlQmFzZUZvci5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX2ZyZWVHbG9iYWwuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19nZXRSYXdUYWcuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19pc0luZGV4LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9faXNQcm90b3R5cGUuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19uYXRpdmVLZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL19ub2RlVXRpbC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvX29iamVjdFRvU3RyaW5nLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9fcm9vdC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvZm9ySW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lkZW50aXR5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0FyZ3VtZW50cy5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNBcnJheUxpa2UuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzQnVmZmVyLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0Z1bmN0aW9uLmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9pc0xlbmd0aC5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNPYmplY3QuanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL2lzT2JqZWN0TGlrZS5qcyIsIm5vZGVfbW9kdWxlcy9sb2Rhc2gvaXNUeXBlZEFycmF5LmpzIiwibm9kZV9tb2R1bGVzL2xvZGFzaC9rZXlzSW4uanMiLCJub2RlX21vZHVsZXMvbG9kYXNoL3N0dWJGYWxzZS5qcyIsInNyY1xcbGF2YS5lbnRyeS5lczYiLCJzcmNcXGxhdmFcXENoYXJ0LmVzNiIsInNyY1xcbGF2YVxcRGFzaGJvYXJkLmVzNiIsInNyY1xcbGF2YVxcRXJyb3JzLmVzNiIsInNyY1xcbGF2YVxcTGF2YS5lczYiLCJzcmNcXGxhdmFcXFJlbmRlcmFibGUuZXM2Iiwic3JjXFxsYXZhXFxVdGlscy5lczYiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDOVNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ05BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDaEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDNUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2xCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM1REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNwQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNkQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUN6QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7OztBQ0pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDOUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2xCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDcEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ1RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3ZDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNyQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDcENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMxQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDakNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNyQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ25DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQy9CQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDN0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzNCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7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QUF4QyxFQUFpRDtBQUNwRCxRQUFJLGFBQWEsYUFBYSxLQUFiLENBQW1CLEdBQW5CLENBQWpCO0FBQ0EsUUFBSSxPQUFPLFdBQVcsR0FBWCxFQUFYOztBQUVBLFNBQUssSUFBSSxJQUFJLENBQWIsRUFBZ0IsSUFBSSxXQUFXLE1BQS9CLEVBQXVDLEdBQXZDLEVBQTRDO0FBQ3hDLGtCQUFVLFFBQVEsV0FBVyxDQUFYLENBQVIsQ0FBVjtBQUNIOztBQUVELFdBQU8sUUFBUSxJQUFSLENBQVA7QUFDSCIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICB0aGlzLl9ldmVudHMgPSB0aGlzLl9ldmVudHMgfHwge307XG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59XG5tb2R1bGUuZXhwb3J0cyA9IEV2ZW50RW1pdHRlcjtcblxuLy8gQmFja3dhcmRzLWNvbXBhdCB3aXRoIG5vZGUgMC4xMC54XG5FdmVudEVtaXR0ZXIuRXZlbnRFbWl0dGVyID0gRXZlbnRFbWl0dGVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9ldmVudHMgPSB1bmRlZmluZWQ7XG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLl9tYXhMaXN0ZW5lcnMgPSB1bmRlZmluZWQ7XG5cbi8vIEJ5IGRlZmF1bHQgRXZlbnRFbWl0dGVycyB3aWxsIHByaW50IGEgd2FybmluZyBpZiBtb3JlIHRoYW4gMTAgbGlzdGVuZXJzIGFyZVxuLy8gYWRkZWQgdG8gaXQuIFRoaXMgaXMgYSB1c2VmdWwgZGVmYXVsdCB3aGljaCBoZWxwcyBmaW5kaW5nIG1lbW9yeSBsZWFrcy5cbkV2ZW50RW1pdHRlci5kZWZhdWx0TWF4TGlzdGVuZXJzID0gMTA7XG5cbi8vIE9idmlvdXNseSBub3QgYWxsIEVtaXR0ZXJzIHNob3VsZCBiZSBsaW1pdGVkIHRvIDEwLiBUaGlzIGZ1bmN0aW9uIGFsbG93c1xuLy8gdGhhdCB0byBiZSBpbmNyZWFzZWQuIFNldCB0byB6ZXJvIGZvciB1bmxpbWl0ZWQuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnNldE1heExpc3RlbmVycyA9IGZ1bmN0aW9uKG4pIHtcbiAgaWYgKCFpc051bWJlcihuKSB8fCBuIDwgMCB8fCBpc05hTihuKSlcbiAgICB0aHJvdyBUeXBlRXJyb3IoJ24gbXVzdCBiZSBhIHBvc2l0aXZlIG51bWJlcicpO1xuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSBuO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgdmFyIGVyLCBoYW5kbGVyLCBsZW4sIGFyZ3MsIGksIGxpc3RlbmVycztcblxuICBpZiAoIXRoaXMuX2V2ZW50cylcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmICh0eXBlID09PSAnZXJyb3InKSB7XG4gICAgaWYgKCF0aGlzLl9ldmVudHMuZXJyb3IgfHxcbiAgICAgICAgKGlzT2JqZWN0KHRoaXMuX2V2ZW50cy5lcnJvcikgJiYgIXRoaXMuX2V2ZW50cy5lcnJvci5sZW5ndGgpKSB7XG4gICAgICBlciA9IGFyZ3VtZW50c1sxXTtcbiAgICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgICAgICB2YXIgZXJyID0gbmV3IEVycm9yKCdVbmNhdWdodCwgdW5zcGVjaWZpZWQgXCJlcnJvclwiIGV2ZW50LiAoJyArIGVyICsgJyknKTtcbiAgICAgICAgZXJyLmNvbnRleHQgPSBlcjtcbiAgICAgICAgdGhyb3cgZXJyO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIGhhbmRsZXIgPSB0aGlzLl9ldmVudHNbdHlwZV07XG5cbiAgaWYgKGlzVW5kZWZpbmVkKGhhbmRsZXIpKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAoaXNGdW5jdGlvbihoYW5kbGVyKSkge1xuICAgIHN3aXRjaCAoYXJndW1lbnRzLmxlbmd0aCkge1xuICAgICAgLy8gZmFzdCBjYXNlc1xuICAgICAgY2FzZSAxOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcyk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAyOlxuICAgICAgICBoYW5kbGVyLmNhbGwodGhpcywgYXJndW1lbnRzWzFdKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIDM6XG4gICAgICAgIGhhbmRsZXIuY2FsbCh0aGlzLCBhcmd1bWVudHNbMV0sIGFyZ3VtZW50c1syXSk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgLy8gc2xvd2VyXG4gICAgICBkZWZhdWx0OlxuICAgICAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICAgICAgaGFuZGxlci5hcHBseSh0aGlzLCBhcmdzKTtcbiAgICB9XG4gIH0gZWxzZSBpZiAoaXNPYmplY3QoaGFuZGxlcikpIHtcbiAgICBhcmdzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoYXJndW1lbnRzLCAxKTtcbiAgICBsaXN0ZW5lcnMgPSBoYW5kbGVyLnNsaWNlKCk7XG4gICAgbGVuID0gbGlzdGVuZXJzLmxlbmd0aDtcbiAgICBmb3IgKGkgPSAwOyBpIDwgbGVuOyBpKyspXG4gICAgICBsaXN0ZW5lcnNbaV0uYXBwbHkodGhpcywgYXJncyk7XG4gIH1cblxuICByZXR1cm4gdHJ1ZTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXIgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICB2YXIgbTtcblxuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgdGhpcy5fZXZlbnRzID0ge307XG5cbiAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICBpZiAodGhpcy5fZXZlbnRzLm5ld0xpc3RlbmVyKVxuICAgIHRoaXMuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICBpc0Z1bmN0aW9uKGxpc3RlbmVyLmxpc3RlbmVyKSA/XG4gICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyIDogbGlzdGVuZXIpO1xuXG4gIGlmICghdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIC8vIE9wdGltaXplIHRoZSBjYXNlIG9mIG9uZSBsaXN0ZW5lci4gRG9uJ3QgbmVlZCB0aGUgZXh0cmEgYXJyYXkgb2JqZWN0LlxuICAgIHRoaXMuX2V2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICBlbHNlIGlmIChpc09iamVjdCh0aGlzLl9ldmVudHNbdHlwZV0pKVxuICAgIC8vIElmIHdlJ3ZlIGFscmVhZHkgZ290IGFuIGFycmF5LCBqdXN0IGFwcGVuZC5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0ucHVzaChsaXN0ZW5lcik7XG4gIGVsc2VcbiAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICB0aGlzLl9ldmVudHNbdHlwZV0gPSBbdGhpcy5fZXZlbnRzW3R5cGVdLCBsaXN0ZW5lcl07XG5cbiAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgaWYgKGlzT2JqZWN0KHRoaXMuX2V2ZW50c1t0eXBlXSkgJiYgIXRoaXMuX2V2ZW50c1t0eXBlXS53YXJuZWQpIHtcbiAgICBpZiAoIWlzVW5kZWZpbmVkKHRoaXMuX21heExpc3RlbmVycykpIHtcbiAgICAgIG0gPSB0aGlzLl9tYXhMaXN0ZW5lcnM7XG4gICAgfSBlbHNlIHtcbiAgICAgIG0gPSBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgICB9XG5cbiAgICBpZiAobSAmJiBtID4gMCAmJiB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoID4gbSkge1xuICAgICAgdGhpcy5fZXZlbnRzW3R5cGVdLndhcm5lZCA9IHRydWU7XG4gICAgICBjb25zb2xlLmVycm9yKCcobm9kZSkgd2FybmluZzogcG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSAnICtcbiAgICAgICAgICAgICAgICAgICAgJ2xlYWsgZGV0ZWN0ZWQuICVkIGxpc3RlbmVycyBhZGRlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICdVc2UgZW1pdHRlci5zZXRNYXhMaXN0ZW5lcnMoKSB0byBpbmNyZWFzZSBsaW1pdC4nLFxuICAgICAgICAgICAgICAgICAgICB0aGlzLl9ldmVudHNbdHlwZV0ubGVuZ3RoKTtcbiAgICAgIGlmICh0eXBlb2YgY29uc29sZS50cmFjZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAvLyBub3Qgc3VwcG9ydGVkIGluIElFIDEwXG4gICAgICAgIGNvbnNvbGUudHJhY2UoKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbih0eXBlLCBsaXN0ZW5lcikge1xuICBpZiAoIWlzRnVuY3Rpb24obGlzdGVuZXIpKVxuICAgIHRocm93IFR5cGVFcnJvcignbGlzdGVuZXIgbXVzdCBiZSBhIGZ1bmN0aW9uJyk7XG5cbiAgdmFyIGZpcmVkID0gZmFsc2U7XG5cbiAgZnVuY3Rpb24gZygpIHtcbiAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGcpO1xuXG4gICAgaWYgKCFmaXJlZCkge1xuICAgICAgZmlyZWQgPSB0cnVlO1xuICAgICAgbGlzdGVuZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH1cblxuICBnLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHRoaXMub24odHlwZSwgZyk7XG5cbiAgcmV0dXJuIHRoaXM7XG59O1xuXG4vLyBlbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWZmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZFxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9IGZ1bmN0aW9uKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBsaXN0LCBwb3NpdGlvbiwgbGVuZ3RoLCBpO1xuXG4gIGlmICghaXNGdW5jdGlvbihsaXN0ZW5lcikpXG4gICAgdGhyb3cgVHlwZUVycm9yKCdsaXN0ZW5lciBtdXN0IGJlIGEgZnVuY3Rpb24nKTtcblxuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldHVybiB0aGlzO1xuXG4gIGxpc3QgPSB0aGlzLl9ldmVudHNbdHlwZV07XG4gIGxlbmd0aCA9IGxpc3QubGVuZ3RoO1xuICBwb3NpdGlvbiA9IC0xO1xuXG4gIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fFxuICAgICAgKGlzRnVuY3Rpb24obGlzdC5saXN0ZW5lcikgJiYgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICBpZiAodGhpcy5fZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3RlbmVyKTtcblxuICB9IGVsc2UgaWYgKGlzT2JqZWN0KGxpc3QpKSB7XG4gICAgZm9yIChpID0gbGVuZ3RoOyBpLS0gPiAwOykge1xuICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8XG4gICAgICAgICAgKGxpc3RbaV0ubGlzdGVuZXIgJiYgbGlzdFtpXS5saXN0ZW5lciA9PT0gbGlzdGVuZXIpKSB7XG4gICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKHBvc2l0aW9uIDwgMClcbiAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgaWYgKGxpc3QubGVuZ3RoID09PSAxKSB7XG4gICAgICBsaXN0Lmxlbmd0aCA9IDA7XG4gICAgICBkZWxldGUgdGhpcy5fZXZlbnRzW3R5cGVdO1xuICAgIH0gZWxzZSB7XG4gICAgICBsaXN0LnNwbGljZShwb3NpdGlvbiwgMSk7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcilcbiAgICAgIHRoaXMuZW1pdCgncmVtb3ZlTGlzdGVuZXInLCB0eXBlLCBsaXN0ZW5lcik7XG4gIH1cblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIga2V5LCBsaXN0ZW5lcnM7XG5cbiAgaWYgKCF0aGlzLl9ldmVudHMpXG4gICAgcmV0dXJuIHRoaXM7XG5cbiAgLy8gbm90IGxpc3RlbmluZyBmb3IgcmVtb3ZlTGlzdGVuZXIsIG5vIG5lZWQgdG8gZW1pdFxuICBpZiAoIXRoaXMuX2V2ZW50cy5yZW1vdmVMaXN0ZW5lcikge1xuICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKVxuICAgICAgdGhpcy5fZXZlbnRzID0ge307XG4gICAgZWxzZSBpZiAodGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgICAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgIGZvciAoa2V5IGluIHRoaXMuX2V2ZW50cykge1xuICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycyhrZXkpO1xuICAgIH1cbiAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICB0aGlzLl9ldmVudHMgPSB7fTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGxpc3RlbmVycyA9IHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICBpZiAoaXNGdW5jdGlvbihsaXN0ZW5lcnMpKSB7XG4gICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICB9IGVsc2UgaWYgKGxpc3RlbmVycykge1xuICAgIC8vIExJRk8gb3JkZXJcbiAgICB3aGlsZSAobGlzdGVuZXJzLmxlbmd0aClcbiAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2xpc3RlbmVycy5sZW5ndGggLSAxXSk7XG4gIH1cbiAgZGVsZXRlIHRoaXMuX2V2ZW50c1t0eXBlXTtcblxuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24odHlwZSkge1xuICB2YXIgcmV0O1xuICBpZiAoIXRoaXMuX2V2ZW50cyB8fCAhdGhpcy5fZXZlbnRzW3R5cGVdKVxuICAgIHJldCA9IFtdO1xuICBlbHNlIGlmIChpc0Z1bmN0aW9uKHRoaXMuX2V2ZW50c1t0eXBlXSkpXG4gICAgcmV0ID0gW3RoaXMuX2V2ZW50c1t0eXBlXV07XG4gIGVsc2VcbiAgICByZXQgPSB0aGlzLl9ldmVudHNbdHlwZV0uc2xpY2UoKTtcbiAgcmV0dXJuIHJldDtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKHR5cGUpIHtcbiAgaWYgKHRoaXMuX2V2ZW50cykge1xuICAgIHZhciBldmxpc3RlbmVyID0gdGhpcy5fZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKGlzRnVuY3Rpb24oZXZsaXN0ZW5lcikpXG4gICAgICByZXR1cm4gMTtcbiAgICBlbHNlIGlmIChldmxpc3RlbmVyKVxuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICB9XG4gIHJldHVybiAwO1xufTtcblxuRXZlbnRFbWl0dGVyLmxpc3RlbmVyQ291bnQgPSBmdW5jdGlvbihlbWl0dGVyLCB0eXBlKSB7XG4gIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG59O1xuXG5mdW5jdGlvbiBpc0Z1bmN0aW9uKGFyZykge1xuICByZXR1cm4gdHlwZW9mIGFyZyA9PT0gJ2Z1bmN0aW9uJztcbn1cblxuZnVuY3Rpb24gaXNOdW1iZXIoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnbnVtYmVyJztcbn1cblxuZnVuY3Rpb24gaXNPYmplY3QoYXJnKSB7XG4gIHJldHVybiB0eXBlb2YgYXJnID09PSAnb2JqZWN0JyAmJiBhcmcgIT09IG51bGw7XG59XG5cbmZ1bmN0aW9uIGlzVW5kZWZpbmVkKGFyZykge1xuICByZXR1cm4gYXJnID09PSB2b2lkIDA7XG59XG4iLCJ2YXIgcm9vdCA9IHJlcXVpcmUoJy4vX3Jvb3QnKTtcblxuLyoqIEJ1aWx0LWluIHZhbHVlIHJlZmVyZW5jZXMuICovXG52YXIgU3ltYm9sID0gcm9vdC5TeW1ib2w7XG5cbm1vZHVsZS5leHBvcnRzID0gU3ltYm9sO1xuIiwidmFyIGJhc2VUaW1lcyA9IHJlcXVpcmUoJy4vX2Jhc2VUaW1lcycpLFxuICAgIGlzQXJndW1lbnRzID0gcmVxdWlyZSgnLi9pc0FyZ3VtZW50cycpLFxuICAgIGlzQXJyYXkgPSByZXF1aXJlKCcuL2lzQXJyYXknKSxcbiAgICBpc0J1ZmZlciA9IHJlcXVpcmUoJy4vaXNCdWZmZXInKSxcbiAgICBpc0luZGV4ID0gcmVxdWlyZSgnLi9faXNJbmRleCcpLFxuICAgIGlzVHlwZWRBcnJheSA9IHJlcXVpcmUoJy4vaXNUeXBlZEFycmF5Jyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKlxuICogQ3JlYXRlcyBhbiBhcnJheSBvZiB0aGUgZW51bWVyYWJsZSBwcm9wZXJ0eSBuYW1lcyBvZiB0aGUgYXJyYXktbGlrZSBgdmFsdWVgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBxdWVyeS5cbiAqIEBwYXJhbSB7Ym9vbGVhbn0gaW5oZXJpdGVkIFNwZWNpZnkgcmV0dXJuaW5nIGluaGVyaXRlZCBwcm9wZXJ0eSBuYW1lcy5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcHJvcGVydHkgbmFtZXMuXG4gKi9cbmZ1bmN0aW9uIGFycmF5TGlrZUtleXModmFsdWUsIGluaGVyaXRlZCkge1xuICB2YXIgaXNBcnIgPSBpc0FycmF5KHZhbHVlKSxcbiAgICAgIGlzQXJnID0gIWlzQXJyICYmIGlzQXJndW1lbnRzKHZhbHVlKSxcbiAgICAgIGlzQnVmZiA9ICFpc0FyciAmJiAhaXNBcmcgJiYgaXNCdWZmZXIodmFsdWUpLFxuICAgICAgaXNUeXBlID0gIWlzQXJyICYmICFpc0FyZyAmJiAhaXNCdWZmICYmIGlzVHlwZWRBcnJheSh2YWx1ZSksXG4gICAgICBza2lwSW5kZXhlcyA9IGlzQXJyIHx8IGlzQXJnIHx8IGlzQnVmZiB8fCBpc1R5cGUsXG4gICAgICByZXN1bHQgPSBza2lwSW5kZXhlcyA/IGJhc2VUaW1lcyh2YWx1ZS5sZW5ndGgsIFN0cmluZykgOiBbXSxcbiAgICAgIGxlbmd0aCA9IHJlc3VsdC5sZW5ndGg7XG5cbiAgZm9yICh2YXIga2V5IGluIHZhbHVlKSB7XG4gICAgaWYgKChpbmhlcml0ZWQgfHwgaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwga2V5KSkgJiZcbiAgICAgICAgIShza2lwSW5kZXhlcyAmJiAoXG4gICAgICAgICAgIC8vIFNhZmFyaSA5IGhhcyBlbnVtZXJhYmxlIGBhcmd1bWVudHMubGVuZ3RoYCBpbiBzdHJpY3QgbW9kZS5cbiAgICAgICAgICAga2V5ID09ICdsZW5ndGgnIHx8XG4gICAgICAgICAgIC8vIE5vZGUuanMgMC4xMCBoYXMgZW51bWVyYWJsZSBub24taW5kZXggcHJvcGVydGllcyBvbiBidWZmZXJzLlxuICAgICAgICAgICAoaXNCdWZmICYmIChrZXkgPT0gJ29mZnNldCcgfHwga2V5ID09ICdwYXJlbnQnKSkgfHxcbiAgICAgICAgICAgLy8gUGhhbnRvbUpTIDIgaGFzIGVudW1lcmFibGUgbm9uLWluZGV4IHByb3BlcnRpZXMgb24gdHlwZWQgYXJyYXlzLlxuICAgICAgICAgICAoaXNUeXBlICYmIChrZXkgPT0gJ2J1ZmZlcicgfHwga2V5ID09ICdieXRlTGVuZ3RoJyB8fCBrZXkgPT0gJ2J5dGVPZmZzZXQnKSkgfHxcbiAgICAgICAgICAgLy8gU2tpcCBpbmRleCBwcm9wZXJ0aWVzLlxuICAgICAgICAgICBpc0luZGV4KGtleSwgbGVuZ3RoKVxuICAgICAgICApKSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBhcnJheUxpa2VLZXlzO1xuIiwidmFyIGNyZWF0ZUJhc2VGb3IgPSByZXF1aXJlKCcuL19jcmVhdGVCYXNlRm9yJyk7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYGJhc2VGb3JPd25gIHdoaWNoIGl0ZXJhdGVzIG92ZXIgYG9iamVjdGBcbiAqIHByb3BlcnRpZXMgcmV0dXJuZWQgYnkgYGtleXNGdW5jYCBhbmQgaW52b2tlcyBgaXRlcmF0ZWVgIGZvciBlYWNoIHByb3BlcnR5LlxuICogSXRlcmF0ZWUgZnVuY3Rpb25zIG1heSBleGl0IGl0ZXJhdGlvbiBlYXJseSBieSBleHBsaWNpdGx5IHJldHVybmluZyBgZmFsc2VgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gaXRlcmF0ZSBvdmVyLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IGtleXNGdW5jIFRoZSBmdW5jdGlvbiB0byBnZXQgdGhlIGtleXMgb2YgYG9iamVjdGAuXG4gKiBAcmV0dXJucyB7T2JqZWN0fSBSZXR1cm5zIGBvYmplY3RgLlxuICovXG52YXIgYmFzZUZvciA9IGNyZWF0ZUJhc2VGb3IoKTtcblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlRm9yO1xuIiwidmFyIFN5bWJvbCA9IHJlcXVpcmUoJy4vX1N5bWJvbCcpLFxuICAgIGdldFJhd1RhZyA9IHJlcXVpcmUoJy4vX2dldFJhd1RhZycpLFxuICAgIG9iamVjdFRvU3RyaW5nID0gcmVxdWlyZSgnLi9fb2JqZWN0VG9TdHJpbmcnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIG51bGxUYWcgPSAnW29iamVjdCBOdWxsXScsXG4gICAgdW5kZWZpbmVkVGFnID0gJ1tvYmplY3QgVW5kZWZpbmVkXSc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBnZXRUYWdgIHdpdGhvdXQgZmFsbGJhY2tzIGZvciBidWdneSBlbnZpcm9ubWVudHMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgYHRvU3RyaW5nVGFnYC5cbiAqL1xuZnVuY3Rpb24gYmFzZUdldFRhZyh2YWx1ZSkge1xuICBpZiAodmFsdWUgPT0gbnVsbCkge1xuICAgIHJldHVybiB2YWx1ZSA9PT0gdW5kZWZpbmVkID8gdW5kZWZpbmVkVGFnIDogbnVsbFRhZztcbiAgfVxuICByZXR1cm4gKHN5bVRvU3RyaW5nVGFnICYmIHN5bVRvU3RyaW5nVGFnIGluIE9iamVjdCh2YWx1ZSkpXG4gICAgPyBnZXRSYXdUYWcodmFsdWUpXG4gICAgOiBvYmplY3RUb1N0cmluZyh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUdldFRhZztcbiIsInZhciBiYXNlR2V0VGFnID0gcmVxdWlyZSgnLi9fYmFzZUdldFRhZycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhcmdzVGFnID0gJ1tvYmplY3QgQXJndW1lbnRzXSc7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNBcmd1bWVudHNgLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGBhcmd1bWVudHNgIG9iamVjdCxcbiAqL1xuZnVuY3Rpb24gYmFzZUlzQXJndW1lbnRzKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGJhc2VHZXRUYWcodmFsdWUpID09IGFyZ3NUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYmFzZUlzQXJndW1lbnRzO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyksXG4gICAgaXNPYmplY3RMaWtlID0gcmVxdWlyZSgnLi9pc09iamVjdExpa2UnKTtcblxuLyoqIGBPYmplY3QjdG9TdHJpbmdgIHJlc3VsdCByZWZlcmVuY2VzLiAqL1xudmFyIGFyZ3NUYWcgPSAnW29iamVjdCBBcmd1bWVudHNdJyxcbiAgICBhcnJheVRhZyA9ICdbb2JqZWN0IEFycmF5XScsXG4gICAgYm9vbFRhZyA9ICdbb2JqZWN0IEJvb2xlYW5dJyxcbiAgICBkYXRlVGFnID0gJ1tvYmplY3QgRGF0ZV0nLFxuICAgIGVycm9yVGFnID0gJ1tvYmplY3QgRXJyb3JdJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBtYXBUYWcgPSAnW29iamVjdCBNYXBdJyxcbiAgICBudW1iZXJUYWcgPSAnW29iamVjdCBOdW1iZXJdJyxcbiAgICBvYmplY3RUYWcgPSAnW29iamVjdCBPYmplY3RdJyxcbiAgICByZWdleHBUYWcgPSAnW29iamVjdCBSZWdFeHBdJyxcbiAgICBzZXRUYWcgPSAnW29iamVjdCBTZXRdJyxcbiAgICBzdHJpbmdUYWcgPSAnW29iamVjdCBTdHJpbmddJyxcbiAgICB3ZWFrTWFwVGFnID0gJ1tvYmplY3QgV2Vha01hcF0nO1xuXG52YXIgYXJyYXlCdWZmZXJUYWcgPSAnW29iamVjdCBBcnJheUJ1ZmZlcl0nLFxuICAgIGRhdGFWaWV3VGFnID0gJ1tvYmplY3QgRGF0YVZpZXddJyxcbiAgICBmbG9hdDMyVGFnID0gJ1tvYmplY3QgRmxvYXQzMkFycmF5XScsXG4gICAgZmxvYXQ2NFRhZyA9ICdbb2JqZWN0IEZsb2F0NjRBcnJheV0nLFxuICAgIGludDhUYWcgPSAnW29iamVjdCBJbnQ4QXJyYXldJyxcbiAgICBpbnQxNlRhZyA9ICdbb2JqZWN0IEludDE2QXJyYXldJyxcbiAgICBpbnQzMlRhZyA9ICdbb2JqZWN0IEludDMyQXJyYXldJyxcbiAgICB1aW50OFRhZyA9ICdbb2JqZWN0IFVpbnQ4QXJyYXldJyxcbiAgICB1aW50OENsYW1wZWRUYWcgPSAnW29iamVjdCBVaW50OENsYW1wZWRBcnJheV0nLFxuICAgIHVpbnQxNlRhZyA9ICdbb2JqZWN0IFVpbnQxNkFycmF5XScsXG4gICAgdWludDMyVGFnID0gJ1tvYmplY3QgVWludDMyQXJyYXldJztcblxuLyoqIFVzZWQgdG8gaWRlbnRpZnkgYHRvU3RyaW5nVGFnYCB2YWx1ZXMgb2YgdHlwZWQgYXJyYXlzLiAqL1xudmFyIHR5cGVkQXJyYXlUYWdzID0ge307XG50eXBlZEFycmF5VGFnc1tmbG9hdDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Zsb2F0NjRUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDhUYWddID0gdHlwZWRBcnJheVRhZ3NbaW50MTZUYWddID1cbnR5cGVkQXJyYXlUYWdzW2ludDMyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW3VpbnQ4VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50OENsYW1wZWRUYWddID0gdHlwZWRBcnJheVRhZ3NbdWludDE2VGFnXSA9XG50eXBlZEFycmF5VGFnc1t1aW50MzJUYWddID0gdHJ1ZTtcbnR5cGVkQXJyYXlUYWdzW2FyZ3NUYWddID0gdHlwZWRBcnJheVRhZ3NbYXJyYXlUYWddID1cbnR5cGVkQXJyYXlUYWdzW2FycmF5QnVmZmVyVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Jvb2xUYWddID1cbnR5cGVkQXJyYXlUYWdzW2RhdGFWaWV3VGFnXSA9IHR5cGVkQXJyYXlUYWdzW2RhdGVUYWddID1cbnR5cGVkQXJyYXlUYWdzW2Vycm9yVGFnXSA9IHR5cGVkQXJyYXlUYWdzW2Z1bmNUYWddID1cbnR5cGVkQXJyYXlUYWdzW21hcFRhZ10gPSB0eXBlZEFycmF5VGFnc1tudW1iZXJUYWddID1cbnR5cGVkQXJyYXlUYWdzW29iamVjdFRhZ10gPSB0eXBlZEFycmF5VGFnc1tyZWdleHBUYWddID1cbnR5cGVkQXJyYXlUYWdzW3NldFRhZ10gPSB0eXBlZEFycmF5VGFnc1tzdHJpbmdUYWddID1cbnR5cGVkQXJyYXlUYWdzW3dlYWtNYXBUYWddID0gZmFsc2U7XG5cbi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8uaXNUeXBlZEFycmF5YCB3aXRob3V0IE5vZGUuanMgb3B0aW1pemF0aW9ucy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHR5cGVkIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGJhc2VJc1R5cGVkQXJyYXkodmFsdWUpIHtcbiAgcmV0dXJuIGlzT2JqZWN0TGlrZSh2YWx1ZSkgJiZcbiAgICBpc0xlbmd0aCh2YWx1ZS5sZW5ndGgpICYmICEhdHlwZWRBcnJheVRhZ3NbYmFzZUdldFRhZyh2YWx1ZSldO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VJc1R5cGVkQXJyYXk7XG4iLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0JyksXG4gICAgaXNQcm90b3R5cGUgPSByZXF1aXJlKCcuL19pc1Byb3RvdHlwZScpLFxuICAgIG5hdGl2ZUtleXNJbiA9IHJlcXVpcmUoJy4vX25hdGl2ZUtleXNJbicpO1xuXG4vKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKiogVXNlZCB0byBjaGVjayBvYmplY3RzIGZvciBvd24gcHJvcGVydGllcy4gKi9cbnZhciBoYXNPd25Qcm9wZXJ0eSA9IG9iamVjdFByb3RvLmhhc093blByb3BlcnR5O1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLmtleXNJbmAgd2hpY2ggZG9lc24ndCB0cmVhdCBzcGFyc2UgYXJyYXlzIGFzIGRlbnNlLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0IFRoZSBvYmplY3QgdG8gcXVlcnkuXG4gKiBAcmV0dXJucyB7QXJyYXl9IFJldHVybnMgdGhlIGFycmF5IG9mIHByb3BlcnR5IG5hbWVzLlxuICovXG5mdW5jdGlvbiBiYXNlS2V5c0luKG9iamVjdCkge1xuICBpZiAoIWlzT2JqZWN0KG9iamVjdCkpIHtcbiAgICByZXR1cm4gbmF0aXZlS2V5c0luKG9iamVjdCk7XG4gIH1cbiAgdmFyIGlzUHJvdG8gPSBpc1Byb3RvdHlwZShvYmplY3QpLFxuICAgICAgcmVzdWx0ID0gW107XG5cbiAgZm9yICh2YXIga2V5IGluIG9iamVjdCkge1xuICAgIGlmICghKGtleSA9PSAnY29uc3RydWN0b3InICYmIChpc1Byb3RvIHx8ICFoYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwga2V5KSkpKSB7XG4gICAgICByZXN1bHQucHVzaChrZXkpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VLZXlzSW47XG4iLCIvKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLnRpbWVzYCB3aXRob3V0IHN1cHBvcnQgZm9yIGl0ZXJhdGVlIHNob3J0aGFuZHNcbiAqIG9yIG1heCBhcnJheSBsZW5ndGggY2hlY2tzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0ge251bWJlcn0gbiBUaGUgbnVtYmVyIG9mIHRpbWVzIHRvIGludm9rZSBgaXRlcmF0ZWVgLlxuICogQHBhcmFtIHtGdW5jdGlvbn0gaXRlcmF0ZWUgVGhlIGZ1bmN0aW9uIGludm9rZWQgcGVyIGl0ZXJhdGlvbi5cbiAqIEByZXR1cm5zIHtBcnJheX0gUmV0dXJucyB0aGUgYXJyYXkgb2YgcmVzdWx0cy5cbiAqL1xuZnVuY3Rpb24gYmFzZVRpbWVzKG4sIGl0ZXJhdGVlKSB7XG4gIHZhciBpbmRleCA9IC0xLFxuICAgICAgcmVzdWx0ID0gQXJyYXkobik7XG5cbiAgd2hpbGUgKCsraW5kZXggPCBuKSB7XG4gICAgcmVzdWx0W2luZGV4XSA9IGl0ZXJhdGVlKGluZGV4KTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGJhc2VUaW1lcztcbiIsIi8qKlxuICogVGhlIGJhc2UgaW1wbGVtZW50YXRpb24gb2YgYF8udW5hcnlgIHdpdGhvdXQgc3VwcG9ydCBmb3Igc3RvcmluZyBtZXRhZGF0YS5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gZnVuYyBUaGUgZnVuY3Rpb24gdG8gY2FwIGFyZ3VtZW50cyBmb3IuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBjYXBwZWQgZnVuY3Rpb24uXG4gKi9cbmZ1bmN0aW9uIGJhc2VVbmFyeShmdW5jKSB7XG4gIHJldHVybiBmdW5jdGlvbih2YWx1ZSkge1xuICAgIHJldHVybiBmdW5jKHZhbHVlKTtcbiAgfTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBiYXNlVW5hcnk7XG4iLCJ2YXIgaWRlbnRpdHkgPSByZXF1aXJlKCcuL2lkZW50aXR5Jyk7XG5cbi8qKlxuICogQ2FzdHMgYHZhbHVlYCB0byBgaWRlbnRpdHlgIGlmIGl0J3Mgbm90IGEgZnVuY3Rpb24uXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGluc3BlY3QuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgY2FzdCBmdW5jdGlvbi5cbiAqL1xuZnVuY3Rpb24gY2FzdEZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ2Z1bmN0aW9uJyA/IHZhbHVlIDogaWRlbnRpdHk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY2FzdEZ1bmN0aW9uO1xuIiwiLyoqXG4gKiBDcmVhdGVzIGEgYmFzZSBmdW5jdGlvbiBmb3IgbWV0aG9kcyBsaWtlIGBfLmZvckluYCBhbmQgYF8uZm9yT3duYC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtib29sZWFufSBbZnJvbVJpZ2h0XSBTcGVjaWZ5IGl0ZXJhdGluZyBmcm9tIHJpZ2h0IHRvIGxlZnQuXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IFJldHVybnMgdGhlIG5ldyBiYXNlIGZ1bmN0aW9uLlxuICovXG5mdW5jdGlvbiBjcmVhdGVCYXNlRm9yKGZyb21SaWdodCkge1xuICByZXR1cm4gZnVuY3Rpb24ob2JqZWN0LCBpdGVyYXRlZSwga2V5c0Z1bmMpIHtcbiAgICB2YXIgaW5kZXggPSAtMSxcbiAgICAgICAgaXRlcmFibGUgPSBPYmplY3Qob2JqZWN0KSxcbiAgICAgICAgcHJvcHMgPSBrZXlzRnVuYyhvYmplY3QpLFxuICAgICAgICBsZW5ndGggPSBwcm9wcy5sZW5ndGg7XG5cbiAgICB3aGlsZSAobGVuZ3RoLS0pIHtcbiAgICAgIHZhciBrZXkgPSBwcm9wc1tmcm9tUmlnaHQgPyBsZW5ndGggOiArK2luZGV4XTtcbiAgICAgIGlmIChpdGVyYXRlZShpdGVyYWJsZVtrZXldLCBrZXksIGl0ZXJhYmxlKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBvYmplY3Q7XG4gIH07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gY3JlYXRlQmFzZUZvcjtcbiIsIi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZ2xvYmFsYCBmcm9tIE5vZGUuanMuICovXG52YXIgZnJlZUdsb2JhbCA9IHR5cGVvZiBnbG9iYWwgPT0gJ29iamVjdCcgJiYgZ2xvYmFsICYmIGdsb2JhbC5PYmplY3QgPT09IE9iamVjdCAmJiBnbG9iYWw7XG5cbm1vZHVsZS5leHBvcnRzID0gZnJlZUdsb2JhbDtcbiIsInZhciBTeW1ib2wgPSByZXF1aXJlKCcuL19TeW1ib2wnKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqIFVzZWQgdG8gY2hlY2sgb2JqZWN0cyBmb3Igb3duIHByb3BlcnRpZXMuICovXG52YXIgaGFzT3duUHJvcGVydHkgPSBvYmplY3RQcm90by5oYXNPd25Qcm9wZXJ0eTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHN5bVRvU3RyaW5nVGFnID0gU3ltYm9sID8gU3ltYm9sLnRvU3RyaW5nVGFnIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIEEgc3BlY2lhbGl6ZWQgdmVyc2lvbiBvZiBgYmFzZUdldFRhZ2Agd2hpY2ggaWdub3JlcyBgU3ltYm9sLnRvU3RyaW5nVGFnYCB2YWx1ZXMuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHF1ZXJ5LlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgcmF3IGB0b1N0cmluZ1RhZ2AuXG4gKi9cbmZ1bmN0aW9uIGdldFJhd1RhZyh2YWx1ZSkge1xuICB2YXIgaXNPd24gPSBoYXNPd25Qcm9wZXJ0eS5jYWxsKHZhbHVlLCBzeW1Ub1N0cmluZ1RhZyksXG4gICAgICB0YWcgPSB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ107XG5cbiAgdHJ5IHtcbiAgICB2YWx1ZVtzeW1Ub1N0cmluZ1RhZ10gPSB1bmRlZmluZWQ7XG4gICAgdmFyIHVubWFza2VkID0gdHJ1ZTtcbiAgfSBjYXRjaCAoZSkge31cblxuICB2YXIgcmVzdWx0ID0gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG4gIGlmICh1bm1hc2tlZCkge1xuICAgIGlmIChpc093bikge1xuICAgICAgdmFsdWVbc3ltVG9TdHJpbmdUYWddID0gdGFnO1xuICAgIH0gZWxzZSB7XG4gICAgICBkZWxldGUgdmFsdWVbc3ltVG9TdHJpbmdUYWddO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGdldFJhd1RhZztcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKiogVXNlZCB0byBkZXRlY3QgdW5zaWduZWQgaW50ZWdlciB2YWx1ZXMuICovXG52YXIgcmVJc1VpbnQgPSAvXig/OjB8WzEtOV1cXGQqKSQvO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBpbmRleC5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcGFyYW0ge251bWJlcn0gW2xlbmd0aD1NQVhfU0FGRV9JTlRFR0VSXSBUaGUgdXBwZXIgYm91bmRzIG9mIGEgdmFsaWQgaW5kZXguXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIHZhbGlkIGluZGV4LCBlbHNlIGBmYWxzZWAuXG4gKi9cbmZ1bmN0aW9uIGlzSW5kZXgodmFsdWUsIGxlbmd0aCkge1xuICBsZW5ndGggPSBsZW5ndGggPT0gbnVsbCA/IE1BWF9TQUZFX0lOVEVHRVIgOiBsZW5ndGg7XG4gIHJldHVybiAhIWxlbmd0aCAmJlxuICAgICh0eXBlb2YgdmFsdWUgPT0gJ251bWJlcicgfHwgcmVJc1VpbnQudGVzdCh2YWx1ZSkpICYmXG4gICAgKHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPCBsZW5ndGgpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzSW5kZXg7XG4iLCIvKiogVXNlZCBmb3IgYnVpbHQtaW4gbWV0aG9kIHJlZmVyZW5jZXMuICovXG52YXIgb2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGxpa2VseSBhIHByb3RvdHlwZSBvYmplY3QuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBwcm90b3R5cGUsIGVsc2UgYGZhbHNlYC5cbiAqL1xuZnVuY3Rpb24gaXNQcm90b3R5cGUodmFsdWUpIHtcbiAgdmFyIEN0b3IgPSB2YWx1ZSAmJiB2YWx1ZS5jb25zdHJ1Y3RvcixcbiAgICAgIHByb3RvID0gKHR5cGVvZiBDdG9yID09ICdmdW5jdGlvbicgJiYgQ3Rvci5wcm90b3R5cGUpIHx8IG9iamVjdFByb3RvO1xuXG4gIHJldHVybiB2YWx1ZSA9PT0gcHJvdG87XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNQcm90b3R5cGU7XG4iLCIvKipcbiAqIFRoaXMgZnVuY3Rpb24gaXMgbGlrZVxuICogW2BPYmplY3Qua2V5c2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLW9iamVjdC5rZXlzKVxuICogZXhjZXB0IHRoYXQgaXQgaW5jbHVkZXMgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydGllcy5cbiAqXG4gKiBAcHJpdmF0ZVxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqL1xuZnVuY3Rpb24gbmF0aXZlS2V5c0luKG9iamVjdCkge1xuICB2YXIgcmVzdWx0ID0gW107XG4gIGlmIChvYmplY3QgIT0gbnVsbCkge1xuICAgIGZvciAodmFyIGtleSBpbiBPYmplY3Qob2JqZWN0KSkge1xuICAgICAgcmVzdWx0LnB1c2goa2V5KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBuYXRpdmVLZXlzSW47XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHByb2Nlc3NgIGZyb20gTm9kZS5qcy4gKi9cbnZhciBmcmVlUHJvY2VzcyA9IG1vZHVsZUV4cG9ydHMgJiYgZnJlZUdsb2JhbC5wcm9jZXNzO1xuXG4vKiogVXNlZCB0byBhY2Nlc3MgZmFzdGVyIE5vZGUuanMgaGVscGVycy4gKi9cbnZhciBub2RlVXRpbCA9IChmdW5jdGlvbigpIHtcbiAgdHJ5IHtcbiAgICByZXR1cm4gZnJlZVByb2Nlc3MgJiYgZnJlZVByb2Nlc3MuYmluZGluZyAmJiBmcmVlUHJvY2Vzcy5iaW5kaW5nKCd1dGlsJyk7XG4gIH0gY2F0Y2ggKGUpIHt9XG59KCkpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IG5vZGVVdGlsO1xuIiwiLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNy4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG5hdGl2ZU9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKlxuICogQ29udmVydHMgYHZhbHVlYCB0byBhIHN0cmluZyB1c2luZyBgT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZ2AuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNvbnZlcnQuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSBjb252ZXJ0ZWQgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBvYmplY3RUb1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gbmF0aXZlT2JqZWN0VG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gb2JqZWN0VG9TdHJpbmc7XG4iLCJ2YXIgZnJlZUdsb2JhbCA9IHJlcXVpcmUoJy4vX2ZyZWVHbG9iYWwnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBzZWxmYC4gKi9cbnZhciBmcmVlU2VsZiA9IHR5cGVvZiBzZWxmID09ICdvYmplY3QnICYmIHNlbGYgJiYgc2VsZi5PYmplY3QgPT09IE9iamVjdCAmJiBzZWxmO1xuXG4vKiogVXNlZCBhcyBhIHJlZmVyZW5jZSB0byB0aGUgZ2xvYmFsIG9iamVjdC4gKi9cbnZhciByb290ID0gZnJlZUdsb2JhbCB8fCBmcmVlU2VsZiB8fCBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHJvb3Q7XG4iLCJ2YXIgYmFzZUZvciA9IHJlcXVpcmUoJy4vX2Jhc2VGb3InKSxcbiAgICBjYXN0RnVuY3Rpb24gPSByZXF1aXJlKCcuL19jYXN0RnVuY3Rpb24nKSxcbiAgICBrZXlzSW4gPSByZXF1aXJlKCcuL2tleXNJbicpO1xuXG4vKipcbiAqIEl0ZXJhdGVzIG92ZXIgb3duIGFuZCBpbmhlcml0ZWQgZW51bWVyYWJsZSBzdHJpbmcga2V5ZWQgcHJvcGVydGllcyBvZiBhblxuICogb2JqZWN0IGFuZCBpbnZva2VzIGBpdGVyYXRlZWAgZm9yIGVhY2ggcHJvcGVydHkuIFRoZSBpdGVyYXRlZSBpcyBpbnZva2VkXG4gKiB3aXRoIHRocmVlIGFyZ3VtZW50czogKHZhbHVlLCBrZXksIG9iamVjdCkuIEl0ZXJhdGVlIGZ1bmN0aW9ucyBtYXkgZXhpdFxuICogaXRlcmF0aW9uIGVhcmx5IGJ5IGV4cGxpY2l0bHkgcmV0dXJuaW5nIGBmYWxzZWAuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjMuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIGl0ZXJhdGUgb3Zlci5cbiAqIEBwYXJhbSB7RnVuY3Rpb259IFtpdGVyYXRlZT1fLmlkZW50aXR5XSBUaGUgZnVuY3Rpb24gaW52b2tlZCBwZXIgaXRlcmF0aW9uLlxuICogQHJldHVybnMge09iamVjdH0gUmV0dXJucyBgb2JqZWN0YC5cbiAqIEBzZWUgXy5mb3JJblJpZ2h0XG4gKiBAZXhhbXBsZVxuICpcbiAqIGZ1bmN0aW9uIEZvbygpIHtcbiAqICAgdGhpcy5hID0gMTtcbiAqICAgdGhpcy5iID0gMjtcbiAqIH1cbiAqXG4gKiBGb28ucHJvdG90eXBlLmMgPSAzO1xuICpcbiAqIF8uZm9ySW4obmV3IEZvbywgZnVuY3Rpb24odmFsdWUsIGtleSkge1xuICogICBjb25zb2xlLmxvZyhrZXkpO1xuICogfSk7XG4gKiAvLyA9PiBMb2dzICdhJywgJ2InLCB0aGVuICdjJyAoaXRlcmF0aW9uIG9yZGVyIGlzIG5vdCBndWFyYW50ZWVkKS5cbiAqL1xuZnVuY3Rpb24gZm9ySW4ob2JqZWN0LCBpdGVyYXRlZSkge1xuICByZXR1cm4gb2JqZWN0ID09IG51bGxcbiAgICA/IG9iamVjdFxuICAgIDogYmFzZUZvcihvYmplY3QsIGNhc3RGdW5jdGlvbihpdGVyYXRlZSksIGtleXNJbik7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZm9ySW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgdGhlIGZpcnN0IGFyZ3VtZW50IGl0IHJlY2VpdmVzLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBzaW5jZSAwLjEuMFxuICogQG1lbWJlck9mIF9cbiAqIEBjYXRlZ29yeSBVdGlsXG4gKiBAcGFyYW0geyp9IHZhbHVlIEFueSB2YWx1ZS5cbiAqIEByZXR1cm5zIHsqfSBSZXR1cm5zIGB2YWx1ZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIHZhciBvYmplY3QgPSB7ICdhJzogMSB9O1xuICpcbiAqIGNvbnNvbGUubG9nKF8uaWRlbnRpdHkob2JqZWN0KSA9PT0gb2JqZWN0KTtcbiAqIC8vID0+IHRydWVcbiAqL1xuZnVuY3Rpb24gaWRlbnRpdHkodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlkZW50aXR5O1xuIiwidmFyIGJhc2VJc0FyZ3VtZW50cyA9IHJlcXVpcmUoJy4vX2Jhc2VJc0FyZ3VtZW50cycpLFxuICAgIGlzT2JqZWN0TGlrZSA9IHJlcXVpcmUoJy4vaXNPYmplY3RMaWtlJyk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKiBVc2VkIHRvIGNoZWNrIG9iamVjdHMgZm9yIG93biBwcm9wZXJ0aWVzLiAqL1xudmFyIGhhc093blByb3BlcnR5ID0gb2JqZWN0UHJvdG8uaGFzT3duUHJvcGVydHk7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIHByb3BlcnR5SXNFbnVtZXJhYmxlID0gb2JqZWN0UHJvdG8ucHJvcGVydHlJc0VudW1lcmFibGU7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgbGlrZWx5IGFuIGBhcmd1bWVudHNgIG9iamVjdC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDAuMS4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhbiBgYXJndW1lbnRzYCBvYmplY3QsXG4gKiAgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQXJndW1lbnRzKGZ1bmN0aW9uKCkgeyByZXR1cm4gYXJndW1lbnRzOyB9KCkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcmd1bWVudHMoWzEsIDIsIDNdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc0FyZ3VtZW50cyA9IGJhc2VJc0FyZ3VtZW50cyhmdW5jdGlvbigpIHsgcmV0dXJuIGFyZ3VtZW50czsgfSgpKSA/IGJhc2VJc0FyZ3VtZW50cyA6IGZ1bmN0aW9uKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdExpa2UodmFsdWUpICYmIGhhc093blByb3BlcnR5LmNhbGwodmFsdWUsICdjYWxsZWUnKSAmJlxuICAgICFwcm9wZXJ0eUlzRW51bWVyYWJsZS5jYWxsKHZhbHVlLCAnY2FsbGVlJyk7XG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJndW1lbnRzO1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGFuIGBBcnJheWAgb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMC4xLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGFuIGFycmF5LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheShbMSwgMiwgM10pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheShkb2N1bWVudC5ib2R5LmNoaWxkcmVuKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0FycmF5KF8ubm9vcCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG52YXIgaXNBcnJheSA9IEFycmF5LmlzQXJyYXk7XG5cbm1vZHVsZS5leHBvcnRzID0gaXNBcnJheTtcbiIsInZhciBpc0Z1bmN0aW9uID0gcmVxdWlyZSgnLi9pc0Z1bmN0aW9uJyksXG4gICAgaXNMZW5ndGggPSByZXF1aXJlKCcuL2lzTGVuZ3RoJyk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgYXJyYXktbGlrZS4gQSB2YWx1ZSBpcyBjb25zaWRlcmVkIGFycmF5LWxpa2UgaWYgaXQnc1xuICogbm90IGEgZnVuY3Rpb24gYW5kIGhhcyBhIGB2YWx1ZS5sZW5ndGhgIHRoYXQncyBhbiBpbnRlZ2VyIGdyZWF0ZXIgdGhhbiBvclxuICogZXF1YWwgdG8gYDBgIGFuZCBsZXNzIHRoYW4gb3IgZXF1YWwgdG8gYE51bWJlci5NQVhfU0FGRV9JTlRFR0VSYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhcnJheS1saWtlLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzQXJyYXlMaWtlKGRvY3VtZW50LmJvZHkuY2hpbGRyZW4pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoJ2FiYycpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNBcnJheUxpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzQXJyYXlMaWtlKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPSBudWxsICYmIGlzTGVuZ3RoKHZhbHVlLmxlbmd0aCkgJiYgIWlzRnVuY3Rpb24odmFsdWUpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQXJyYXlMaWtlO1xuIiwidmFyIHJvb3QgPSByZXF1aXJlKCcuL19yb290JyksXG4gICAgc3R1YkZhbHNlID0gcmVxdWlyZSgnLi9zdHViRmFsc2UnKTtcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBleHBvcnRzYC4gKi9cbnZhciBmcmVlRXhwb3J0cyA9IHR5cGVvZiBleHBvcnRzID09ICdvYmplY3QnICYmIGV4cG9ydHMgJiYgIWV4cG9ydHMubm9kZVR5cGUgJiYgZXhwb3J0cztcblxuLyoqIERldGVjdCBmcmVlIHZhcmlhYmxlIGBtb2R1bGVgLiAqL1xudmFyIGZyZWVNb2R1bGUgPSBmcmVlRXhwb3J0cyAmJiB0eXBlb2YgbW9kdWxlID09ICdvYmplY3QnICYmIG1vZHVsZSAmJiAhbW9kdWxlLm5vZGVUeXBlICYmIG1vZHVsZTtcblxuLyoqIERldGVjdCB0aGUgcG9wdWxhciBDb21tb25KUyBleHRlbnNpb24gYG1vZHVsZS5leHBvcnRzYC4gKi9cbnZhciBtb2R1bGVFeHBvcnRzID0gZnJlZU1vZHVsZSAmJiBmcmVlTW9kdWxlLmV4cG9ydHMgPT09IGZyZWVFeHBvcnRzO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBCdWZmZXIgPSBtb2R1bGVFeHBvcnRzID8gcm9vdC5CdWZmZXIgOiB1bmRlZmluZWQ7XG5cbi8qIEJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzIGZvciB0aG9zZSB3aXRoIHRoZSBzYW1lIG5hbWUgYXMgb3RoZXIgYGxvZGFzaGAgbWV0aG9kcy4gKi9cbnZhciBuYXRpdmVJc0J1ZmZlciA9IEJ1ZmZlciA/IEJ1ZmZlci5pc0J1ZmZlciA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlci5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMy4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBhIGJ1ZmZlciwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzQnVmZmVyKG5ldyBCdWZmZXIoMikpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNCdWZmZXIobmV3IFVpbnQ4QXJyYXkoMikpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xudmFyIGlzQnVmZmVyID0gbmF0aXZlSXNCdWZmZXIgfHwgc3R1YkZhbHNlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGlzQnVmZmVyO1xuIiwidmFyIGJhc2VHZXRUYWcgPSByZXF1aXJlKCcuL19iYXNlR2V0VGFnJyksXG4gICAgaXNPYmplY3QgPSByZXF1aXJlKCcuL2lzT2JqZWN0Jyk7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBhc3luY1RhZyA9ICdbb2JqZWN0IEFzeW5jRnVuY3Rpb25dJyxcbiAgICBmdW5jVGFnID0gJ1tvYmplY3QgRnVuY3Rpb25dJyxcbiAgICBnZW5UYWcgPSAnW29iamVjdCBHZW5lcmF0b3JGdW5jdGlvbl0nLFxuICAgIHByb3h5VGFnID0gJ1tvYmplY3QgUHJveHldJztcblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBjbGFzc2lmaWVkIGFzIGEgYEZ1bmN0aW9uYCBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBmdW5jdGlvbiwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzRnVuY3Rpb24oXyk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc0Z1bmN0aW9uKC9hYmMvKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzRnVuY3Rpb24odmFsdWUpIHtcbiAgaWYgKCFpc09iamVjdCh2YWx1ZSkpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgLy8gVGhlIHVzZSBvZiBgT2JqZWN0I3RvU3RyaW5nYCBhdm9pZHMgaXNzdWVzIHdpdGggdGhlIGB0eXBlb2ZgIG9wZXJhdG9yXG4gIC8vIGluIFNhZmFyaSA5IHdoaWNoIHJldHVybnMgJ29iamVjdCcgZm9yIHR5cGVkIGFycmF5cyBhbmQgb3RoZXIgY29uc3RydWN0b3JzLlxuICB2YXIgdGFnID0gYmFzZUdldFRhZyh2YWx1ZSk7XG4gIHJldHVybiB0YWcgPT0gZnVuY1RhZyB8fCB0YWcgPT0gZ2VuVGFnIHx8IHRhZyA9PSBhc3luY1RhZyB8fCB0YWcgPT0gcHJveHlUYWc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNGdW5jdGlvbjtcbiIsIi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIE1BWF9TQUZFX0lOVEVHRVIgPSA5MDA3MTk5MjU0NzQwOTkxO1xuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGEgdmFsaWQgYXJyYXktbGlrZSBsZW5ndGguXG4gKlxuICogKipOb3RlOioqIFRoaXMgbWV0aG9kIGlzIGxvb3NlbHkgYmFzZWQgb25cbiAqIFtgVG9MZW5ndGhgXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi83LjAvI3NlYy10b2xlbmd0aCkuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSB2YWxpZCBsZW5ndGgsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc0xlbmd0aCgzKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzTGVuZ3RoKE51bWJlci5NSU5fVkFMVUUpO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzTGVuZ3RoKEluZmluaXR5KTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc0xlbmd0aCgnMycpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNMZW5ndGgodmFsdWUpIHtcbiAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PSAnbnVtYmVyJyAmJlxuICAgIHZhbHVlID4gLTEgJiYgdmFsdWUgJSAxID09IDAgJiYgdmFsdWUgPD0gTUFYX1NBRkVfSU5URUdFUjtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBpc0xlbmd0aDtcbiIsIi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgdGhlXG4gKiBbbGFuZ3VhZ2UgdHlwZV0oaHR0cDovL3d3dy5lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzcuMC8jc2VjLWVjbWFzY3JpcHQtbGFuZ3VhZ2UtdHlwZXMpXG4gKiBvZiBgT2JqZWN0YC4gKGUuZy4gYXJyYXlzLCBmdW5jdGlvbnMsIG9iamVjdHMsIHJlZ2V4ZXMsIGBuZXcgTnVtYmVyKDApYCwgYW5kIGBuZXcgU3RyaW5nKCcnKWApXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAwLjEuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYW4gb2JqZWN0LCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNPYmplY3Qoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3QoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0KF8ubm9vcCk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdChudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0KHZhbHVlKSB7XG4gIHZhciB0eXBlID0gdHlwZW9mIHZhbHVlO1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiAodHlwZSA9PSAnb2JqZWN0JyB8fCB0eXBlID09ICdmdW5jdGlvbicpO1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGlzT2JqZWN0O1xuIiwiLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZS4gQSB2YWx1ZSBpcyBvYmplY3QtbGlrZSBpZiBpdCdzIG5vdCBgbnVsbGBcbiAqIGFuZCBoYXMgYSBgdHlwZW9mYCByZXN1bHQgb2YgXCJvYmplY3RcIi5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZSh7fSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0TGlrZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgIT0gbnVsbCAmJiB0eXBlb2YgdmFsdWUgPT0gJ29iamVjdCc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gaXNPYmplY3RMaWtlO1xuIiwidmFyIGJhc2VJc1R5cGVkQXJyYXkgPSByZXF1aXJlKCcuL19iYXNlSXNUeXBlZEFycmF5JyksXG4gICAgYmFzZVVuYXJ5ID0gcmVxdWlyZSgnLi9fYmFzZVVuYXJ5JyksXG4gICAgbm9kZVV0aWwgPSByZXF1aXJlKCcuL19ub2RlVXRpbCcpO1xuXG4vKiBOb2RlLmpzIGhlbHBlciByZWZlcmVuY2VzLiAqL1xudmFyIG5vZGVJc1R5cGVkQXJyYXkgPSBub2RlVXRpbCAmJiBub2RlVXRpbC5pc1R5cGVkQXJyYXk7XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhIHR5cGVkIGFycmF5LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgdHlwZWQgYXJyYXksIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc1R5cGVkQXJyYXkobmV3IFVpbnQ4QXJyYXkpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNUeXBlZEFycmF5KFtdKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbnZhciBpc1R5cGVkQXJyYXkgPSBub2RlSXNUeXBlZEFycmF5ID8gYmFzZVVuYXJ5KG5vZGVJc1R5cGVkQXJyYXkpIDogYmFzZUlzVHlwZWRBcnJheTtcblxubW9kdWxlLmV4cG9ydHMgPSBpc1R5cGVkQXJyYXk7XG4iLCJ2YXIgYXJyYXlMaWtlS2V5cyA9IHJlcXVpcmUoJy4vX2FycmF5TGlrZUtleXMnKSxcbiAgICBiYXNlS2V5c0luID0gcmVxdWlyZSgnLi9fYmFzZUtleXNJbicpLFxuICAgIGlzQXJyYXlMaWtlID0gcmVxdWlyZSgnLi9pc0FycmF5TGlrZScpO1xuXG4vKipcbiAqIENyZWF0ZXMgYW4gYXJyYXkgb2YgdGhlIG93biBhbmQgaW5oZXJpdGVkIGVudW1lcmFibGUgcHJvcGVydHkgbmFtZXMgb2YgYG9iamVjdGAuXG4gKlxuICogKipOb3RlOioqIE5vbi1vYmplY3QgdmFsdWVzIGFyZSBjb2VyY2VkIHRvIG9iamVjdHMuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAzLjAuMFxuICogQGNhdGVnb3J5IE9iamVjdFxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdCBUaGUgb2JqZWN0IHRvIHF1ZXJ5LlxuICogQHJldHVybnMge0FycmF5fSBSZXR1cm5zIHRoZSBhcnJheSBvZiBwcm9wZXJ0eSBuYW1lcy5cbiAqIEBleGFtcGxlXG4gKlxuICogZnVuY3Rpb24gRm9vKCkge1xuICogICB0aGlzLmEgPSAxO1xuICogICB0aGlzLmIgPSAyO1xuICogfVxuICpcbiAqIEZvby5wcm90b3R5cGUuYyA9IDM7XG4gKlxuICogXy5rZXlzSW4obmV3IEZvbyk7XG4gKiAvLyA9PiBbJ2EnLCAnYicsICdjJ10gKGl0ZXJhdGlvbiBvcmRlciBpcyBub3QgZ3VhcmFudGVlZClcbiAqL1xuZnVuY3Rpb24ga2V5c0luKG9iamVjdCkge1xuICByZXR1cm4gaXNBcnJheUxpa2Uob2JqZWN0KSA/IGFycmF5TGlrZUtleXMob2JqZWN0LCB0cnVlKSA6IGJhc2VLZXlzSW4ob2JqZWN0KTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBrZXlzSW47XG4iLCIvKipcbiAqIFRoaXMgbWV0aG9kIHJldHVybnMgYGZhbHNlYC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMTMuMFxuICogQGNhdGVnb3J5IFV0aWxcbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8udGltZXMoMiwgXy5zdHViRmFsc2UpO1xuICogLy8gPT4gW2ZhbHNlLCBmYWxzZV1cbiAqL1xuZnVuY3Rpb24gc3R1YkZhbHNlKCkge1xuICByZXR1cm4gZmFsc2U7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gc3R1YkZhbHNlO1xuIiwiaW1wb3J0IHsgTGF2YUpzIH0gZnJvbSAnLi9sYXZhL0xhdmEuZXM2JztcclxuaW1wb3J0IHsgYWRkRXZlbnQsIGRvbUxvYWRlZCB9IGZyb20gJy4vbGF2YS9VdGlscy5lczYnO1xyXG5cclxuLyoqXHJcbiAqIEFzc2lnbiB0aGUgTGF2YS5qcyBtb2R1bGUgdG8gdGhlIHdpbmRvdyBhbmRcclxuICogbGV0ICRsYXZhIGJlIGFuIGFsaWFzIHRvIHRoZSBtb2R1bGUuXHJcbiAqL1xyXG5sZXQgJGxhdmEgPSB3aW5kb3cubGF2YSA9IG5ldyBMYXZhSnMoKTtcclxuXHJcbi8qKlxyXG4gKiBPbmNlIHRoZSBET00gaGFzIGxvYWRlZC4uLlxyXG4gKi9cclxuZG9tTG9hZGVkKCkudGhlbigoKSA9PiB7XHJcbiAgICAvKipcclxuICAgICAqIEFkZGluZyB0aGUgcmVzaXplIGV2ZW50IGxpc3RlbmVyIGZvciByZWRyYXdpbmcgY2hhcnRzIGlmXHJcbiAgICAgKiB0aGUgb3B0aW9uIHJlc3BvbnNpdmUgaXMgc2V0IHRvIHRydWUuXHJcbiAgICAgKi9cclxuICAgIGlmICgkbGF2YS5vcHRpb25zLnJlc3BvbnNpdmUgPT09IHRydWUpIHtcclxuICAgICAgICBsZXQgZGVib3VuY2VkID0gbnVsbDtcclxuXHJcbiAgICAgICAgYWRkRXZlbnQod2luZG93LCAncmVzaXplJywgKCkgPT4ge1xyXG4gICAgICAgICAgICBsZXQgcmVkcmF3ID0gJGxhdmEucmVkcmF3QWxsLmJpbmQoJGxhdmEpO1xyXG5cclxuICAgICAgICAgICAgY2xlYXJUaW1lb3V0KGRlYm91bmNlZCk7XHJcblxyXG4gICAgICAgICAgICBkZWJvdW5jZWQgPSBzZXRUaW1lb3V0KCgpID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gV2luZG93IHJlLXNpemVkLCByZWRyYXdpbmcuLi4nKTtcclxuXHJcbiAgICAgICAgICAgICAgICByZWRyYXcoKTtcclxuICAgICAgICAgICAgfSwgJGxhdmEub3B0aW9ucy5kZWJvdW5jZV90aW1lb3V0KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICBpZiAoJGxhdmEub3B0aW9ucy5hdXRvX3J1biA9PT0gdHJ1ZSkge1xyXG4gICAgICAgICRsYXZhLnJ1bigpO1xyXG4gICAgfVxyXG59KTtcclxuIiwiLyoqXHJcbiAqIENoYXJ0IG1vZHVsZVxyXG4gKlxyXG4gKiBAY2xhc3MgICAgIENoYXJ0XHJcbiAqIEBtb2R1bGUgICAgbGF2YS9DaGFydFxyXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxyXG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXHJcbiAqIEBsaWNlbnNlICAgTUlUXHJcbiAqL1xyXG5pbXBvcnQgX2ZvckluIGZyb20gJ2xvZGFzaC9mb3JJbic7XHJcbmltcG9ydCB7IFJlbmRlcmFibGUgfSBmcm9tICcuL1JlbmRlcmFibGUuZXM2JztcclxuaW1wb3J0IHsgc3RyaW5nVG9GdW5jdGlvbiB9IGZyb20gJy4vVXRpbHMuZXM2JztcclxuXHJcbi8qKlxyXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgdHlwZSAgICAgIC0gVHlwZSBvZiBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgR29vZ2xlIGNoYXJ0IHBhY2thZ2UgdG8gbG9hZC5cclxuICogQHByb3BlcnR5IHtib29sZWFufSAgcG5nT3V0cHV0IC0gU2hvdWxkIHRoZSBjaGFydCBiZSBkaXNwbGF5ZWQgYXMgYSBQTkcuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZXZlbnRzICAgIC0gRXZlbnRzIGFuZCBjYWxsYmFja3MgdG8gYXBwbHkgdG8gdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0FycmF5fSAgICBmb3JtYXRzICAgLSBGb3JtYXR0ZXJzIHRvIGFwcGx5IHRvIHRoZSBjaGFydCBkYXRhLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gQ3JlYXRlcyBpZGVudGlmaWNhdGlvbiBzdHJpbmcgZm9yIHRoZSBjaGFydC5cclxuICovXHJcbmV4cG9ydCBjbGFzcyBDaGFydCBleHRlbmRzIFJlbmRlcmFibGVcclxue1xyXG4gICAgLyoqXHJcbiAgICAgKiBDaGFydCBDbGFzc1xyXG4gICAgICpcclxuICAgICAqIFRoaXMgaXMgdGhlIGphdmFzY3JpcHQgdmVyc2lvbiBvZiBhIGxhdmFjaGFydCB3aXRoIG1ldGhvZHMgZm9yIGludGVyYWN0aW5nIHdpdGhcclxuICAgICAqIHRoZSBnb29nbGUgY2hhcnQgYW5kIHRoZSBQSFAgbGF2YWNoYXJ0IG91dHB1dC5cclxuICAgICAqXHJcbiAgICAgKiBAcGFyYW0ge29iamVjdH0ganNvblxyXG4gICAgICogQGNvbnN0cnVjdG9yXHJcbiAgICAgKi9cclxuICAgIGNvbnN0cnVjdG9yIChqc29uKSB7XHJcbiAgICAgICAgc3VwZXIoanNvbik7XHJcblxyXG4gICAgICAgIHRoaXMudHlwZSAgICA9IGpzb24udHlwZTtcclxuICAgICAgICB0aGlzLmNsYXNzICAgPSBqc29uLmNsYXNzO1xyXG4gICAgICAgIHRoaXMuZm9ybWF0cyA9IGpzb24uZm9ybWF0cyB8fCBudWxsO1xyXG5cclxuICAgICAgICB0aGlzLmV2ZW50cyAgICA9IHR5cGVvZiBqc29uLmV2ZW50cyA9PT0gJ29iamVjdCcgPyBqc29uLmV2ZW50cyA6IG51bGw7XHJcbiAgICAgICAgdGhpcy5wbmdPdXRwdXQgPSB0eXBlb2YganNvbi5wbmdPdXRwdXQgPT09ICd1bmRlZmluZWQnID8gZmFsc2UgOiBCb29sZWFuKGpzb24ucG5nT3V0cHV0KTtcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogQW55IGRlcGVuZGVuY3kgb24gd2luZG93Lmdvb2dsZSBtdXN0IGJlIGluIHRoZSByZW5kZXIgc2NvcGUuXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5yZW5kZXIgPSAoKSA9PiB7XHJcbiAgICAgICAgICAgIHRoaXMuc2V0RGF0YShqc29uLmRhdGF0YWJsZSk7XHJcblxyXG4gICAgICAgICAgICBsZXQgQ2hhcnRDbGFzcyA9IHN0cmluZ1RvRnVuY3Rpb24odGhpcy5jbGFzcywgd2luZG93KTtcclxuXHJcbiAgICAgICAgICAgIHRoaXMuZ2NoYXJ0ID0gbmV3IENoYXJ0Q2xhc3ModGhpcy5lbGVtZW50KTtcclxuXHJcbiAgICAgICAgICAgIC8vIDxmb3JtYXRzPlxyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuZXZlbnRzKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcclxuICAgICAgICAgICAgICAgIC8vIFRPRE86IElkZWEuLi4gZm9yd2FyZCBldmVudHMgdG8gYmUgbGlzdGVuYWJsZSBieSB0aGUgdXNlciwgaW5zdGVhZCBvZiBoYXZpbmcgdGhlIHVzZXIgZGVmaW5lIHRoZW0gYXMgYSBzdHJpbmcgY2FsbGJhY2suXHJcbiAgICAgICAgICAgICAgICAvLyBsYXZhLmdldCgnTXlDb29sQ2hhcnQnKS5vbigncmVhZHknLCBmdW5jdGlvbihkYXRhKSB7XHJcbiAgICAgICAgICAgICAgICAvLyAgICAgY29uc29sZS5sb2codGhpcyk7ICAvLyBnQ2hhcnRcclxuICAgICAgICAgICAgICAgIC8vIH0pO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGlmICh0aGlzLnBuZ091dHB1dCkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5kcmF3UG5nKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IGFzIGEgUE5HIGluc3RlYWQgb2YgdGhlIHN0YW5kYXJkIFNWR1xyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBleHRlcm5hbCBcImNoYXJ0LmdldEltYWdlVVJJXCJcclxuICAgICAqIEBzZWUge0BsaW5rIGh0dHBzOi8vZGV2ZWxvcGVycy5nb29nbGUuY29tL2NoYXJ0L2ludGVyYWN0aXZlL2RvY3MvcHJpbnRpbmd8UHJpbnRpbmcgUE5HIENoYXJ0c31cclxuICAgICAqL1xyXG4gICAgZHJhd1BuZygpIHtcclxuICAgICAgICBsZXQgaW1nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW1nJyk7XHJcbiAgICAgICAgICAgIGltZy5zcmMgPSB0aGlzLmdjaGFydC5nZXRJbWFnZVVSSSgpO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQuaW5uZXJIVE1MID0gJyc7XHJcbiAgICAgICAgdGhpcy5lbGVtZW50LmFwcGVuZENoaWxkKGltZyk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBGb3JtYXRzIGNvbHVtbnMgb2YgdGhlIERhdGFUYWJsZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0ge0FycmF5LjxPYmplY3Q+fSBmb3JtYXRBcnIgQXJyYXkgb2YgZm9ybWF0IGRlZmluaXRpb25zXHJcbiAgICAgKi9cclxuICAgIGFwcGx5Rm9ybWF0cyhmb3JtYXRBcnIpIHtcclxuICAgICAgICBmb3IobGV0IGE9MDsgYSA8IGZvcm1hdEFyci5sZW5ndGg7IGErKykge1xyXG4gICAgICAgICAgICBsZXQgZm9ybWF0SnNvbiA9IGZvcm1hdEFyclthXTtcclxuICAgICAgICAgICAgbGV0IGZvcm1hdHRlciA9IG5ldyBnb29nbGUudmlzdWFsaXphdGlvbltmb3JtYXRKc29uLnR5cGVdKGZvcm1hdEpzb24uY29uZmlnKTtcclxuXHJcbiAgICAgICAgICAgIGZvcm1hdHRlci5mb3JtYXQodGhpcy5kYXRhLCBmb3JtYXRKc29uLmluZGV4KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBdHRhY2ggdGhlIGRlZmluZWQgY2hhcnQgZXZlbnQgaGFuZGxlcnMuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2F0dGFjaEV2ZW50cygpIHtcclxuICAgICAgICBsZXQgJGNoYXJ0ID0gdGhpcztcclxuXHJcbiAgICAgICAgX2ZvckluKHRoaXMuZXZlbnRzLCBmdW5jdGlvbiAoY2FsbGJhY2ssIGV2ZW50KSB7XHJcbiAgICAgICAgICAgIGxldCBjb250ZXh0ID0gd2luZG93O1xyXG4gICAgICAgICAgICBsZXQgZnVuYyA9IGNhbGxiYWNrO1xyXG5cclxuICAgICAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ29iamVjdCcpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRleHQgPSBjb250ZXh0W2NhbGxiYWNrWzBdXTtcclxuICAgICAgICAgICAgICAgIGZ1bmMgPSBjYWxsYmFja1sxXTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBUaGUgXCIkeyRjaGFydC51dWlkKCl9Ojoke2V2ZW50fVwiIGV2ZW50IHdpbGwgYmUgaGFuZGxlZCBieSBcIiR7ZnVuY31cIiBpbiB0aGUgY29udGV4dGAsIGNvbnRleHQpO1xyXG5cclxuICAgICAgICAgICAgLyoqXHJcbiAgICAgICAgICAgICAqIFNldCB0aGUgY29udGV4dCBvZiBcInRoaXNcIiB3aXRoaW4gdGhlIHVzZXIgcHJvdmlkZWQgY2FsbGJhY2sgdG8gdGhlXHJcbiAgICAgICAgICAgICAqIGNoYXJ0IHRoYXQgZmlyZWQgdGhlIGV2ZW50IHdoaWxlIHByb3ZpZGluZyB0aGUgZGF0YXRhYmxlIG9mIHRoZSBjaGFydFxyXG4gICAgICAgICAgICAgKiB0byB0aGUgY2FsbGJhY2sgYXMgYW4gYXJndW1lbnQuXHJcbiAgICAgICAgICAgICAqL1xyXG4gICAgICAgICAgICBnb29nbGUudmlzdWFsaXphdGlvbi5ldmVudHMuYWRkTGlzdGVuZXIoJGNoYXJ0LmdjaGFydCwgZXZlbnQsIGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICAgICAgY29uc3QgY2FsbGJhY2sgPSBjb250ZXh0W2Z1bmNdLmJpbmQoJGNoYXJ0LmdjaGFydCk7XHJcblxyXG4gICAgICAgICAgICAgICAgY2FsbGJhY2soJGNoYXJ0LmRhdGEpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufVxyXG4iLCIvKipcclxuICogRGFzaGJvYXJkIG1vZHVsZVxyXG4gKlxyXG4gKiBAY2xhc3MgICAgIERhc2hib2FyZFxyXG4gKiBAbW9kdWxlICAgIGxhdmEvRGFzaGJvYXJkXHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmltcG9ydCB7IFJlbmRlcmFibGUgfSBmcm9tICcuL1JlbmRlcmFibGUuZXM2JztcclxuaW1wb3J0IHsgc3RyaW5nVG9GdW5jdGlvbiB9IGZyb20gJy4vVXRpbHMuZXM2JztcclxuXHJcbi8qKlxyXG4gKiBEYXNoYm9hcmQgY2xhc3NcclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgdHlwZSAgICAgIC0gVHlwZSBvZiBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgR29vZ2xlIGNoYXJ0IHBhY2thZ2UgdG8gbG9hZC5cclxuICogQHByb3BlcnR5IHtib29sZWFufSAgcG5nT3V0cHV0IC0gU2hvdWxkIHRoZSBjaGFydCBiZSBkaXNwbGF5ZWQgYXMgYSBQTkcuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtBcnJheX0gICAgZm9ybWF0cyAgIC0gRm9ybWF0dGVycyB0byBhcHBseSB0byB0aGUgY2hhcnQgZGF0YS5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgcHJvbWlzZXMgIC0gUHJvbWlzZXMgdXNlZCBpbiB0aGUgcmVuZGVyaW5nIGNoYWluLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSBpbml0ICAgICAgLSBJbml0aWFsaXplcyB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IGNvbmZpZ3VyZSAtIENvbmZpZ3VyZXMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gQ3JlYXRlcyBpZGVudGlmaWNhdGlvbiBzdHJpbmcgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgX2Vycm9ycyAgIC0gQ29sbGVjdGlvbiBvZiBlcnJvcnMgdG8gYmUgdGhyb3duLlxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIERhc2hib2FyZCBleHRlbmRzIFJlbmRlcmFibGVcclxue1xyXG4gICAgY29uc3RydWN0b3IoanNvbikge1xyXG4gICAgICAgIHN1cGVyKGpzb24pO1xyXG5cclxuICAgICAgICB0aGlzLnR5cGUgICAgID0gJ0Rhc2hib2FyZCc7XHJcbiAgICAgICAgdGhpcy5iaW5kaW5ncyA9IGpzb24uYmluZGluZ3M7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIEFueSBkZXBlbmRlbmN5IG9uIHdpbmRvdy5nb29nbGUgbXVzdCBiZSBpbiB0aGUgcmVuZGVyIHNjb3BlLlxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMucmVuZGVyID0gKCkgPT4ge1xyXG4gICAgICAgICAgICB0aGlzLnNldERhdGEoanNvbi5kYXRhdGFibGUpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5nY2hhcnQgPSBuZXcgZ29vZ2xlLnZpc3VhbGl6YXRpb24uRGFzaGJvYXJkKHRoaXMuZWxlbWVudCk7XHJcblxyXG4gICAgICAgICAgICB0aGlzLl9hdHRhY2hCaW5kaW5ncygpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuZXZlbnRzKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLl9hdHRhY2hFdmVudHMoKTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgdGhpcy5kcmF3KCk7XHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFByb2Nlc3MgYW5kIGF0dGFjaCB0aGUgYmluZGluZ3MgdG8gdGhlIGRhc2hib2FyZC5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICovXHJcbiAgICBfYXR0YWNoQmluZGluZ3MoKSB7XHJcbiAgICAgICAgZm9yIChsZXQgYmluZGluZyBvZiB0aGlzLmJpbmRpbmdzKSB7XHJcbiAgICAgICAgICAgIGxldCBbY29udHJvbFdyYXBwZXIsIGNoYXJ0V3JhcHBlcl0gPSBiaW5kaW5nO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5nY2hhcnQuYmluZChcclxuICAgICAgICAgICAgICAgIG5ldyBnb29nbGUudmlzdWFsaXphdGlvbi5Db250cm9sV3JhcHBlcihcclxuICAgICAgICAgICAgICAgICAgICBjb250cm9sV3JhcHBlclswXVxyXG4gICAgICAgICAgICAgICAgKSxcclxuICAgICAgICAgICAgICAgIG5ldyBnb29nbGUudmlzdWFsaXphdGlvbi5DaGFydFdyYXBwZXIoXHJcbiAgICAgICAgICAgICAgICAgICAgY2hhcnRXcmFwcGVyWzBdXHJcbiAgICAgICAgICAgICAgICApXHJcbiAgICAgICAgICAgICk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBFcnJvcnMgbW9kdWxlXHJcbiAqXHJcbiAqIEBtb2R1bGUgICAgbGF2YS9FcnJvcnNcclxuICogQGF1dGhvciAgICBLZXZpbiBIaWxsIDxrZXZpbmtoaWxsQGdtYWlsLmNvbT5cclxuICogQGNvcHlyaWdodCAoYykgMjAxNywgS0hpbGwgRGVzaWduc1xyXG4gKiBAbGljZW5zZSAgIE1JVFxyXG4gKi9cclxuY2xhc3MgTGF2YUVycm9yIGV4dGVuZHMgRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKG1lc3NhZ2UpIHtcclxuICAgICAgICBzdXBlcigpO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgICAgPSAnTGF2YUVycm9yJztcclxuICAgICAgICB0aGlzLm1lc3NhZ2UgPSAobWVzc2FnZSB8fCAnJyk7XHJcbiAgICB9O1xyXG59XHJcblxyXG4vKipcclxuICogSW52YWxpZENhbGxiYWNrIEVycm9yXHJcbiAqXHJcbiAqIHRocm93biB3aGVuIHdoZW4gYW55dGhpbmcgYnV0IGEgZnVuY3Rpb24gaXMgZ2l2ZW4gYXMgYSBjYWxsYmFja1xyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgSW52YWxpZENhbGxiYWNrIGV4dGVuZHMgTGF2YUVycm9yXHJcbntcclxuICAgIGNvbnN0cnVjdG9yIChjYWxsYmFjaykge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gXCIke3R5cGVvZiBjYWxsYmFja31cIiBpcyBub3QgYSB2YWxpZCBjYWxsYmFjay5gKTtcclxuXHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRDYWxsYmFjayc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBJbnZhbGlkTGFiZWwgRXJyb3JcclxuICpcclxuICogVGhyb3duIHdoZW4gd2hlbiBhbnl0aGluZyBidXQgYSBzdHJpbmcgaXMgZ2l2ZW4gYXMgYSBsYWJlbC5cclxuICpcclxuICogQHR5cGUge2Z1bmN0aW9ufVxyXG4gKi9cclxuZXhwb3J0IGNsYXNzIEludmFsaWRMYWJlbCBleHRlbmRzIExhdmFFcnJvclxyXG57XHJcbiAgICBjb25zdHJ1Y3RvciAobGFiZWwpIHtcclxuICAgICAgICBzdXBlcihgW2xhdmEuanNdIFwiJHt0eXBlb2YgbGFiZWx9XCIgaXMgbm90IGEgdmFsaWQgbGFiZWwuYCk7XHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0ludmFsaWRMYWJlbCc7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBFbGVtZW50SWROb3RGb3VuZCBFcnJvclxyXG4gKlxyXG4gKiBUaHJvd24gd2hlbiB3aGVuIGFueXRoaW5nIGJ1dCBhIHN0cmluZyBpcyBnaXZlbiBhcyBhIGxhYmVsLlxyXG4gKlxyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgRWxlbWVudElkTm90Rm91bmQgZXh0ZW5kcyBMYXZhRXJyb3Jcclxue1xyXG4gICAgY29uc3RydWN0b3IgKGVsZW1JZCkge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gRE9NIG5vZGUgd2hlcmUgaWQ9XCIke2VsZW1JZH1cIiB3YXMgbm90IGZvdW5kLmApO1xyXG5cclxuICAgICAgICB0aGlzLm5hbWUgPSAnRWxlbWVudElkTm90Rm91bmQnO1xyXG4gICAgfVxyXG59XHJcblxyXG4vKipcclxuICogQ2hhcnROb3RGb3VuZCBFcnJvclxyXG4gKlxyXG4gKiBUaHJvd24gd2hlbiB3aGVuIHRoZSBnZXRDaGFydCgpIG1ldGhvZCBjYW5ub3QgZmluZCBhIGNoYXJ0IHdpdGggdGhlIGdpdmVuIGxhYmVsLlxyXG4gKlxyXG4gKiBAdHlwZSB7ZnVuY3Rpb259XHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgUmVuZGVyYWJsZU5vdEZvdW5kIGV4dGVuZHMgTGF2YUVycm9yXHJcbntcclxuICAgIGNvbnN0cnVjdG9yIChsYWJlbCkge1xyXG4gICAgICAgIHN1cGVyKGBbbGF2YS5qc10gQ2hhcnQgd2l0aCBsYWJlbCBcIiR7bGFiZWx9XCIgd2FzIG5vdCBmb3VuZC5gKTtcclxuXHJcbiAgICAgICAgdGhpcy5uYW1lID0gJ0NoYXJ0Tm90Rm91bmQnO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBsYXZhLmpzIG1vZHVsZVxyXG4gKlxyXG4gKiBAbW9kdWxlICAgIGxhdmEvTGF2YVxyXG4gKiBAYXV0aG9yICAgIEtldmluIEhpbGwgPGtldmlua2hpbGxAZ21haWwuY29tPlxyXG4gKiBAY29weXJpZ2h0IChjKSAyMDE3LCBLSGlsbCBEZXNpZ25zXHJcbiAqIEBsaWNlbnNlICAgaHR0cDovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL01JVCBNSVRcclxuICovXHJcbmltcG9ydCBfZm9ySW4gZnJvbSAnbG9kYXNoL2ZvckluJztcclxuaW1wb3J0IEV2ZW50RW1pdHRlciBmcm9tICdldmVudHMnO1xyXG5pbXBvcnQgeyBDaGFydCB9IGZyb20gJy4vQ2hhcnQuZXM2JztcclxuaW1wb3J0IHsgRGFzaGJvYXJkIH0gZnJvbSAnLi9EYXNoYm9hcmQuZXM2JztcclxuaW1wb3J0IHsgbm9vcCB9IGZyb20gJy4vVXRpbHMuZXM2JztcclxuaW1wb3J0IHsgSW52YWxpZENhbGxiYWNrLCBSZW5kZXJhYmxlTm90Rm91bmQgfSBmcm9tICcuL0Vycm9ycy5lczYnXHJcblxyXG5cclxuLyoqXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgICAgICAgICAgICBWRVJTSU9OICAgICAgICBWZXJzaW9uIG9mIHRoZSBtb2R1bGUuXHJcbiAqIEBwcm9wZXJ0eSB7Q2hhcnR9ICAgICAgICAgICAgICBDaGFydCAgICAgICAgICBDaGFydCBjbGFzcy5cclxuICogQHByb3BlcnR5IHtEYXNoYm9hcmR9ICAgICAgICAgIERhc2hib2FyZCAgICAgIERhc2hib2FyZCBjbGFzcy5cclxuICogQHByb3BlcnR5IHtvYmplY3R9ICAgICAgICAgICAgIF9lcnJvcnNcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgICAgICAgICAgIEdPT0dMRV9MT0FERVJfVVJMICAgICBVcmwgdG8gR29vZ2xlJ3Mgc3RhdGljIGxvYWRlclxyXG4gKiBAcHJvcGVydHkge29iamVjdH0gICAgICAgICAgICAgb3B0aW9ucyAgICAgICAgT3B0aW9ucyBmb3IgdGhlIG1vZHVsZVxyXG4gKiBAcHJvcGVydHkge2Z1bmN0aW9ufSAgICAgICAgICAgX3JlYWR5Q2FsbGJhY2tcclxuICogQHByb3BlcnR5IHtBcnJheS48c3RyaW5nPn0gICAgIF9wYWNrYWdlc1xyXG4gKiBAcHJvcGVydHkge0FycmF5LjxSZW5kZXJhYmxlPn0gX3JlbmRlcmFibGVzXHJcbiAqL1xyXG5leHBvcnQgY2xhc3MgTGF2YUpzIGV4dGVuZHMgRXZlbnRFbWl0dGVyXHJcbntcclxuICAgIGNvbnN0cnVjdG9yKCkge1xyXG4gICAgICAgIHN1cGVyKCk7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFZlcnNpb24gb2YgdGhlIExhdmEuanMgbW9kdWxlLlxyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge3N0cmluZ31cclxuICAgICAgICAgKiBAcHVibGljXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgdGhpcy5WRVJTSU9OID0gJzQuMC4wJztcclxuXHJcbiAgICAgICAgLyoqXHJcbiAgICAgICAgICogVmVyc2lvbiBvZiB0aGUgR29vZ2xlIGNoYXJ0cyBBUEkgdG8gbG9hZC5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OID0gJ2N1cnJlbnQnO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBVcmxzIHRvIEdvb2dsZSdzIHN0YXRpYyBsb2FkZXJcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtzdHJpbmd9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuR09PR0xFX0xPQURFUl9VUkwgPSAnaHR0cHM6Ly93d3cuZ3N0YXRpYy5jb20vY2hhcnRzL2xvYWRlci5qcyc7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIENoYXJ0IG1vZHVsZSB3aXRoaW4gTGF2YS5qc1xyXG4gICAgICAgICAqXHJcbiAgICAgICAgICogQHR5cGUge0NoYXJ0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLkNoYXJ0ID0gQ2hhcnQ7XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIFN0b3JpbmcgdGhlIERhc2hib2FyZCBtb2R1bGUgd2l0aGluIExhdmEuanNcclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtEYXNoYm9hcmR9XHJcbiAgICAgICAgICogQHB1YmxpY1xyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuRGFzaGJvYXJkID0gRGFzaGJvYXJkO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBKU09OIG9iamVjdCBvZiBjb25maWcgaXRlbXMuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAdHlwZSB7T2JqZWN0fVxyXG4gICAgICAgICAqIEBwdWJsaWNcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLm9wdGlvbnMgPSBPUFRJT05TX0pTT047XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIEFycmF5IG9mIHZpc3VhbGl6YXRpb24gcGFja2FnZXMgZm9yIGNoYXJ0cyBhbmQgZGFzaGJvYXJkcy5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtBcnJheS48c3RyaW5nPn1cclxuICAgICAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuX3BhY2thZ2VzID0gW107XHJcblxyXG4gICAgICAgIC8qKlxyXG4gICAgICAgICAqIEFycmF5IG9mIGNoYXJ0cyBhbmQgZGFzaGJvYXJkcyBzdG9yZWQgaW4gdGhlIG1vZHVsZS5cclxuICAgICAgICAgKlxyXG4gICAgICAgICAqIEB0eXBlIHtBcnJheS48UmVuZGVyYWJsZT59XHJcbiAgICAgICAgICogQHByaXZhdGVcclxuICAgICAgICAgKi9cclxuICAgICAgICB0aGlzLl9yZW5kZXJhYmxlcyA9IFtdO1xyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBSZWFkeSBjYWxsYmFjayB0byBiZSBjYWxsZWQgd2hlbiB0aGUgbW9kdWxlIGlzIGZpbmlzaGVkIHJ1bm5pbmcuXHJcbiAgICAgICAgICpcclxuICAgICAgICAgKiBAY2FsbGJhY2sgX3JlYWR5Q2FsbGJhY2tcclxuICAgICAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICAgICAqL1xyXG4gICAgICAgIHRoaXMuX3JlYWR5Q2FsbGJhY2sgPSBub29wO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGEgbmV3IENoYXJ0IGZyb20gYSBKU09OIHBheWxvYWQuXHJcbiAgICAgKlxyXG4gICAgICogVGhlIEpTT04gcGF5bG9hZCBjb21lcyBmcm9tIHRoZSBQSFAgQ2hhcnQgY2xhc3MuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtICB7b2JqZWN0fSBqc29uXHJcbiAgICAgKiBAcmV0dXJuIHtDaGFydH1cclxuICAgICAqL1xyXG4gICAgY3JlYXRlQ2hhcnQoanNvbikge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdKU09OX1BBWUxPQUQnLCBqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy5fYWRkUGFja2FnZXMoanNvbi5wYWNrYWdlcyk7XHJcblxyXG4gICAgICAgIHRoaXMuc3RvcmUobmV3IHRoaXMuQ2hhcnQoanNvbikpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGFuZCBzdG9yZSBhIG5ldyBDaGFydCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBzZWUgY3JlYXRlQ2hhcnRcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7Q2hhcnR9XHJcbiAgICAgKi9cclxuICAgIGFkZE5ld0NoYXJ0KGpzb24pIHtcclxuICAgICAgICB0aGlzLnN0b3JlKHRoaXMuY3JlYXRlQ2hhcnQoanNvbikpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGEgbmV3IERhc2hib2FyZCB3aXRoIGEgZ2l2ZW4gbGFiZWwuXHJcbiAgICAgKlxyXG4gICAgICogVGhlIEpTT04gcGF5bG9hZCBjb21lcyBmcm9tIHRoZSBQSFAgRGFzaGJvYXJkIGNsYXNzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSAge29iamVjdH0ganNvblxyXG4gICAgICogQHJldHVybiB7RGFzaGJvYXJkfVxyXG4gICAgICovXHJcbiAgICBjcmVhdGVEYXNoYm9hcmQoanNvbikge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKCdKU09OX1BBWUxPQUQnLCBqc29uKTtcclxuXHJcbiAgICAgICAgdGhpcy5fYWRkUGFja2FnZXMoanNvbi5wYWNrYWdlcyk7XHJcblxyXG4gICAgICAgIHJldHVybiBuZXcgdGhpcy5EYXNoYm9hcmQoanNvbik7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBDcmVhdGUgYW5kIHN0b3JlIGEgbmV3IERhc2hib2FyZCBmcm9tIGEgSlNPTiBwYXlsb2FkLlxyXG4gICAgICpcclxuICAgICAqIFRoZSBKU09OIHBheWxvYWQgY29tZXMgZnJvbSB0aGUgUEhQIERhc2hib2FyZCBjbGFzcy5cclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAc2VlIGNyZWF0ZURhc2hib2FyZFxyXG4gICAgICogQHBhcmFtICB7b2JqZWN0fSBqc29uXHJcbiAgICAgKiBAcmV0dXJuIHtEYXNoYm9hcmR9XHJcbiAgICAgKi9cclxuICAgIGFkZE5ld0Rhc2hib2FyZChqc29uKSB7XHJcbiAgICAgICAgdGhpcy5zdG9yZSh0aGlzLmNyZWF0ZURhc2hib2FyZChqc29uKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSdW5zIHRoZSBMYXZhLmpzIG1vZHVsZVxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgcnVuKCkge1xyXG4gICAgICAgIGNvbnN0ICRsYXZhID0gdGhpcztcclxuXHJcbiAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBSdW5uaW5nLi4uJyk7XHJcbiAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBMb2FkaW5nIG9wdGlvbnM6JywgdGhpcy5vcHRpb25zKTtcclxuXHJcbiAgICAgICAgJGxhdmEuX2xvYWRHb29nbGUoKS50aGVuKCgpID0+IHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBHb29nbGUgaXMgcmVhZHkuJyk7XHJcblxyXG4gICAgICAgICAgICBfZm9ySW4oJGxhdmEuX3JlbmRlcmFibGVzLCByZW5kZXJhYmxlID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gUmVuZGVyaW5nICR7cmVuZGVyYWJsZS51dWlkKCl9YCk7XHJcblxyXG4gICAgICAgICAgICAgICAgcmVuZGVyYWJsZS5yZW5kZXIoKTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIEV4ZWN1dGluZyBsYXZhLnJlYWR5KGNhbGxiYWNrKScpO1xyXG4gICAgICAgICAgICAkbGF2YS5fcmVhZHlDYWxsYmFjaygpO1xyXG5cclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ1tsYXZhLmpzXSBGaXJpbmcgXCJyZWFkeVwiIGV2ZW50LicpO1xyXG4gICAgICAgICAgICAkbGF2YS5lbWl0KCdyZWFkeScpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU3RvcmVzIGEgcmVuZGVyYWJsZSBsYXZhIG9iamVjdCB3aXRoaW4gdGhlIG1vZHVsZS5cclxuICAgICAqXHJcbiAgICAgKiBAcGFyYW0ge1JlbmRlcmFibGV9IHJlbmRlcmFibGVcclxuICAgICAqL1xyXG4gICAgc3RvcmUocmVuZGVyYWJsZSkge1xyXG4gICAgICAgIGNvbnNvbGUubG9nKGBbbGF2YS5qc10gU3RvcmluZyAke3JlbmRlcmFibGUudXVpZCgpfWApO1xyXG5cclxuICAgICAgICB0aGlzLl9yZW5kZXJhYmxlc1tyZW5kZXJhYmxlLmxhYmVsXSA9IHJlbmRlcmFibGU7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBBc3NpZ25zIGEgY2FsbGJhY2sgZm9yIHdoZW4gdGhlIGNoYXJ0cyBhcmUgcmVhZHkgdG8gYmUgaW50ZXJhY3RlZCB3aXRoLlxyXG4gICAgICpcclxuICAgICAqIFRoaXMgaXMgdXNlZCB0byB3cmFwIGNhbGxzIHRvIGxhdmEubG9hZERhdGEoKSBvciBsYXZhLmxvYWRPcHRpb25zKClcclxuICAgICAqIHRvIHByb3RlY3QgYWdhaW5zdCBhY2Nlc3NpbmcgY2hhcnRzIHRoYXQgYXJlbid0IGxvYWRlZCB5ZXRcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBjYWxsYmFja1xyXG4gICAgICovXHJcbiAgICByZWFkeShjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLl9yZWFkeUNhbGxiYWNrID0gY2FsbGJhY2s7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgZGF0YSBpbnRvIHRoZSBjaGFydCBhbmQgcmVkcmF3cy5cclxuICAgICAqXHJcbiAgICAgKlxyXG4gICAgICogVXNlZCB3aXRoIGFuIEFKQVggY2FsbCB0byBhIFBIUCBtZXRob2QgcmV0dXJuaW5nIERhdGFUYWJsZS0+dG9Kc29uKCksXHJcbiAgICAgKiBhIGNoYXJ0IGNhbiBiZSBkeW5hbWljYWxseSB1cGRhdGUgaW4gcGFnZSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWREYXRhKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldERhdGEoanNvbik7XHJcblxyXG4gICAgICAgICAgICBpZiAodHlwZW9mIGpzb24uZm9ybWF0cyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICAgICAgICAgIGNoYXJ0LmFwcGx5Rm9ybWF0cyhqc29uLmZvcm1hdHMpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBjaGFydC5kcmF3KCk7XHJcblxyXG4gICAgICAgICAgICBjYWxsYmFjayhjaGFydCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBMb2FkcyBuZXcgb3B0aW9ucyBpbnRvIGEgY2hhcnQgYW5kIHJlZHJhd3MuXHJcbiAgICAgKlxyXG4gICAgICpcclxuICAgICAqIFVzZWQgd2l0aCBhbiBBSkFYIGNhbGwsIG9yIGphdmFzY3JpcHQgZXZlbnRzLCB0byBsb2FkIGEgbmV3IGFycmF5IG9mIG9wdGlvbnMgaW50byBhIGNoYXJ0LlxyXG4gICAgICogVGhpcyBjYW4gYmUgdXNlZCB0byB1cGRhdGUgYSBjaGFydCBkeW5hbWljYWxseSwgd2l0aG91dCByZWxvYWRzLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBsYWJlbFxyXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGpzb25cclxuICAgICAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrXHJcbiAgICAgKi9cclxuICAgIGxvYWRPcHRpb25zKGxhYmVsLCBqc29uLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgICAgIGNhbGxiYWNrID0gY2FsbGJhY2sgfHwgbm9vcDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0aGlzLmdldChsYWJlbCwgZnVuY3Rpb24gKGNoYXJ0KSB7XHJcbiAgICAgICAgICAgIGNoYXJ0LnNldE9wdGlvbnMoanNvbik7XHJcbiAgICAgICAgICAgIGNoYXJ0LmRyYXcoKTtcclxuXHJcbiAgICAgICAgICAgIGNhbGxiYWNrKGNoYXJ0KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFJlZHJhd3MgYWxsIG9mIHRoZSByZWdpc3RlcmVkIGNoYXJ0cyBvbiBzY3JlZW4uXHJcbiAgICAgKlxyXG4gICAgICogVGhpcyBtZXRob2QgaXMgYXR0YWNoZWQgdG8gdGhlIHdpbmRvdyByZXNpemUgZXZlbnQgd2l0aCBkZWJvdW5jaW5nXHJcbiAgICAgKiB0byBtYWtlIHRoZSBjaGFydHMgcmVzcG9uc2l2ZSB0byB0aGUgYnJvd3NlciByZXNpemluZy5cclxuICAgICAqL1xyXG4gICAgcmVkcmF3QWxsKCkge1xyXG4gICAgICAgIGZvciAobGV0IHJlbmRlcmFibGUgb2YgdGhpcy5fcmVuZGVyYWJsZXMpIHtcclxuICAgICAgICAgICAgY29uc29sZS5sb2coYFtsYXZhLmpzXSBSZWRyYXdpbmcgJHtyZW5kZXJhYmxlLnV1aWQoKX1gKTtcclxuXHJcbiAgICAgICAgICAgIGNvbnN0IHJlZHJhdyA9IHJlbmRlcmFibGUuZHJhdy5iaW5kKHJlbmRlcmFibGUpO1xyXG5cclxuICAgICAgICAgICAgcmVkcmF3KCk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogUmV0dXJucyB0aGUgTGF2YUNoYXJ0IGphdmFzY3JpcHQgb2JqZWN0c1xyXG4gICAgICpcclxuICAgICAqXHJcbiAgICAgKiBUaGUgTGF2YUNoYXJ0IG9iamVjdCBob2xkcyBhbGwgdGhlIHVzZXIgZGVmaW5lZCBwcm9wZXJ0aWVzIHN1Y2ggYXMgZGF0YSwgb3B0aW9ucywgZm9ybWF0cyxcclxuICAgICAqIHRoZSBHb29nbGVDaGFydCBvYmplY3QsIGFuZCByZWxhdGl2ZSBtZXRob2RzIGZvciBpbnRlcm5hbCB1c2UuXHJcbiAgICAgKlxyXG4gICAgICogVGhlIEdvb2dsZUNoYXJ0IG9iamVjdCBpcyBhdmFpbGFibGUgYXMgXCIuY2hhcnRcIiBmcm9tIHRoZSByZXR1cm5lZCBMYXZhQ2hhcnQuXHJcbiAgICAgKiBJdCBjYW4gYmUgdXNlZCB0byBhY2Nlc3MgYW55IG9mIHRoZSBhdmFpbGFibGUgbWV0aG9kcyBzdWNoIGFzXHJcbiAgICAgKiBnZXRJbWFnZVVSSSgpIG9yIGdldENoYXJ0TGF5b3V0SW50ZXJmYWNlKCkuXHJcbiAgICAgKiBTZWUgaHR0cHM6Ly9nb29nbGUtZGV2ZWxvcGVycy5hcHBzcG90LmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL2dhbGxlcnkvbGluZWNoYXJ0I21ldGhvZHNcclxuICAgICAqIGZvciBzb21lIGV4YW1wbGVzIHJlbGF0aXZlIHRvIExpbmVDaGFydHMuXHJcbiAgICAgKlxyXG4gICAgICogQHB1YmxpY1xyXG4gICAgICogQHBhcmFtICB7c3RyaW5nfSAgIGxhYmVsXHJcbiAgICAgKiBAcGFyYW0gIHtGdW5jdGlvbn0gY2FsbGJhY2tcclxuICAgICAqIEB0aHJvd3MgSW52YWxpZExhYmVsXHJcbiAgICAgKiBAdGhyb3dzIEludmFsaWRDYWxsYmFja1xyXG4gICAgICogQHRocm93cyBSZW5kZXJhYmxlTm90Rm91bmRcclxuICAgICAqL1xyXG4gICAgZ2V0KGxhYmVsLCBjYWxsYmFjaykge1xyXG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgdGhyb3cgbmV3IEludmFsaWRDYWxsYmFjayhjYWxsYmFjayk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBsZXQgcmVuZGVyYWJsZSA9IHRoaXMuX3JlbmRlcmFibGVzW2xhYmVsXTtcclxuXHJcbiAgICAgICAgaWYgKCEgcmVuZGVyYWJsZSkge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgUmVuZGVyYWJsZU5vdEZvdW5kKGxhYmVsKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGNhbGxiYWNrKHJlbmRlcmFibGUpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQWRkcyB0byB0aGUgbGlzdCBvZiBwYWNrYWdlcyB0aGF0IEdvb2dsZSBuZWVkcyB0byBsb2FkLlxyXG4gICAgICpcclxuICAgICAqIEBwcml2YXRlXHJcbiAgICAgKiBAcGFyYW0ge0FycmF5fSBwYWNrYWdlc1xyXG4gICAgICogQHJldHVybiB7QXJyYXl9XHJcbiAgICAgKi9cclxuICAgIF9hZGRQYWNrYWdlcyhwYWNrYWdlcykge1xyXG4gICAgICAgIHRoaXMuX3BhY2thZ2VzID0gdGhpcy5fcGFja2FnZXMuY29uY2F0KHBhY2thZ2VzKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIExvYWQgdGhlIEdvb2dsZSBTdGF0aWMgTG9hZGVyIGFuZCByZXNvbHZlIHRoZSBwcm9taXNlIHdoZW4gcmVhZHkuXHJcbiAgICAgKlxyXG4gICAgICogQHByaXZhdGVcclxuICAgICAqL1xyXG4gICAgX2xvYWRHb29nbGUoKSB7XHJcbiAgICAgICAgY29uc3QgJGxhdmEgPSB0aGlzO1xyXG5cclxuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gUmVzb2x2aW5nIEdvb2dsZS4uLicpO1xyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuX2dvb2dsZUlzTG9hZGVkKCkpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gU3RhdGljIGxvYWRlciBmb3VuZCwgaW5pdGlhbGl6aW5nIHdpbmRvdy5nb29nbGUnKTtcclxuXHJcbiAgICAgICAgICAgICAgICAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnW2xhdmEuanNdIFN0YXRpYyBsb2FkZXIgbm90IGZvdW5kLCBhcHBlbmRpbmcgdG8gaGVhZCcpO1xyXG5cclxuICAgICAgICAgICAgICAgICRsYXZhLl9hZGRHb29nbGVTY3JpcHRUb0hlYWQocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgICAgICAvLyBUaGlzIHdpbGwgY2FsbCAkbGF2YS5fZ29vZ2xlQ2hhcnRMb2FkZXIocmVzb2x2ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIENoZWNrIGlmIEdvb2dsZSdzIFN0YXRpYyBMb2FkZXIgaXMgaW4gcGFnZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHJldHVybnMge2Jvb2xlYW59XHJcbiAgICAgKi9cclxuICAgIF9nb29nbGVJc0xvYWRlZCgpIHtcclxuICAgICAgICBjb25zdCBzY3JpcHRzID0gZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ3NjcmlwdCcpO1xyXG5cclxuICAgICAgICBmb3IgKGxldCBzY3JpcHQgb2Ygc2NyaXB0cykge1xyXG4gICAgICAgICAgICBpZiAoc2NyaXB0LnNyYyA9PT0gdGhpcy5HT09HTEVfTE9BREVSX1VSTCkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSdW5zIHRoZSBHb29nbGUgY2hhcnQgbG9hZGVyIGFuZCByZXNvbHZlcyB0aGUgcHJvbWlzZS5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHBhcmFtIHtQcm9taXNlLnJlc29sdmV9IHJlc29sdmVcclxuICAgICAqL1xyXG4gICAgX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpIHtcclxuICAgICAgICBsZXQgY29uZmlnID0ge1xyXG4gICAgICAgICAgICBwYWNrYWdlczogdGhpcy5fcGFja2FnZXMsXHJcbiAgICAgICAgICAgIGxhbmd1YWdlOiB0aGlzLm9wdGlvbnMubG9jYWxlXHJcbiAgICAgICAgfTtcclxuXHJcbiAgICAgICAgaWYgKHRoaXMub3B0aW9ucy5tYXBzX2FwaV9rZXkgIT09ICcnKSB7XHJcbiAgICAgICAgICAgIGNvbmZpZy5tYXBzQXBpS2V5ID0gdGhpcy5vcHRpb25zLm1hcHNfYXBpX2tleTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGNvbnNvbGUubG9nKCdbbGF2YS5qc10gTG9hZGluZyBHb29nbGUgd2l0aCBjb25maWc6JywgY29uZmlnKTtcclxuXHJcbiAgICAgICAgZ29vZ2xlLmNoYXJ0cy5sb2FkKHRoaXMuR09PR0xFX0FQSV9WRVJTSU9OLCBjb25maWcpO1xyXG5cclxuICAgICAgICBnb29nbGUuY2hhcnRzLnNldE9uTG9hZENhbGxiYWNrKHJlc29sdmUpO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogQ3JlYXRlIGEgbmV3IHNjcmlwdCB0YWcgZm9yIHRoZSBHb29nbGUgU3RhdGljIExvYWRlci5cclxuICAgICAqXHJcbiAgICAgKiBAcHJpdmF0ZVxyXG4gICAgICogQHBhcmFtIHtQcm9taXNlLnJlc29sdmV9IHJlc29sdmVcclxuICAgICAqIEByZXR1cm5zIHtFbGVtZW50fVxyXG4gICAgICovXHJcbiAgICBfYWRkR29vZ2xlU2NyaXB0VG9IZWFkKHJlc29sdmUpIHtcclxuICAgICAgICBsZXQgJGxhdmEgPSB0aGlzO1xyXG4gICAgICAgIGxldCBzY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtcclxuXHJcbiAgICAgICAgc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcclxuICAgICAgICBzY3JpcHQuYXN5bmMgPSB0cnVlO1xyXG4gICAgICAgIHNjcmlwdC5zcmMgPSB0aGlzLkdPT0dMRV9MT0FERVJfVVJMO1xyXG4gICAgICAgIHNjcmlwdC5vbmxvYWQgPSBzY3JpcHQub25yZWFkeXN0YXRlY2hhbmdlID0gZnVuY3Rpb24gKGV2ZW50KSB7XHJcbiAgICAgICAgICAgIGV2ZW50ID0gZXZlbnQgfHwgd2luZG93LmV2ZW50O1xyXG5cclxuICAgICAgICAgICAgaWYgKGV2ZW50LnR5cGUgPT09ICdsb2FkJyB8fCAoL2xvYWRlZHxjb21wbGV0ZS8udGVzdCh0aGlzLnJlYWR5U3RhdGUpKSkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5vbmxvYWQgPSB0aGlzLm9ucmVhZHlzdGF0ZWNoYW5nZSA9IG51bGw7XHJcblxyXG4gICAgICAgICAgICAgICAgJGxhdmEuX2dvb2dsZUNoYXJ0TG9hZGVyKHJlc29sdmUpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuXHJcbiAgICAgICAgZG9jdW1lbnQuaGVhZC5hcHBlbmRDaGlsZChzY3JpcHQpO1xyXG4gICAgfVxyXG59XHJcbiIsIi8qKlxyXG4gKiBDaGFydCBjbGFzcyB1c2VkIGZvciBzdG9yaW5nIGFsbCB0aGUgbmVlZGVkIGNvbmZpZ3VyYXRpb24gZm9yIHJlbmRlcmluZy5cclxuICpcclxuICogQHR5cGVkZWYge0Z1bmN0aW9ufSAgQ2hhcnRcclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgbGFiZWwgICAgIC0gTGFiZWwgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtzdHJpbmd9ICAgdHlwZSAgICAgIC0gVHlwZSBvZiBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgZWxlbWVudCAgIC0gSHRtbCBlbGVtZW50IGluIHdoaWNoIHRvIHJlbmRlciB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGNoYXJ0ICAgICAtIEdvb2dsZSBjaGFydCBvYmplY3QuXHJcbiAqIEBwcm9wZXJ0eSB7c3RyaW5nfSAgIHBhY2thZ2UgICAtIFR5cGUgb2YgR29vZ2xlIGNoYXJ0IHBhY2thZ2UgdG8gbG9hZC5cclxuICogQHByb3BlcnR5IHtib29sZWFufSAgcG5nT3V0cHV0IC0gU2hvdWxkIHRoZSBjaGFydCBiZSBkaXNwbGF5ZWQgYXMgYSBQTkcuXHJcbiAqIEBwcm9wZXJ0eSB7T2JqZWN0fSAgIGRhdGEgICAgICAtIERhdGF0YWJsZSBmb3IgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge09iamVjdH0gICBvcHRpb25zICAgLSBDb25maWd1cmF0aW9uIG9wdGlvbnMgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtBcnJheX0gICAgZm9ybWF0cyAgIC0gRm9ybWF0dGVycyB0byBhcHBseSB0byB0aGUgY2hhcnQgZGF0YS5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgcHJvbWlzZXMgIC0gUHJvbWlzZXMgdXNlZCBpbiB0aGUgcmVuZGVyaW5nIGNoYWluLlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSBpbml0ICAgICAgLSBJbml0aWFsaXplcyB0aGUgY2hhcnQuXHJcbiAqIEBwcm9wZXJ0eSB7RnVuY3Rpb259IGNvbmZpZ3VyZSAtIENvbmZpZ3VyZXMgdGhlIGNoYXJ0LlxyXG4gKiBAcHJvcGVydHkge0Z1bmN0aW9ufSByZW5kZXIgICAgLSBSZW5kZXJzIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtGdW5jdGlvbn0gdXVpZCAgICAgIC0gQ3JlYXRlcyBpZGVudGlmaWNhdGlvbiBzdHJpbmcgZm9yIHRoZSBjaGFydC5cclxuICogQHByb3BlcnR5IHtPYmplY3R9ICAgX2Vycm9ycyAgIC0gQ29sbGVjdGlvbiBvZiBlcnJvcnMgdG8gYmUgdGhyb3duLlxyXG4gKi9cclxuaW1wb3J0IHsgRWxlbWVudElkTm90Rm91bmQgfSBmcm9tIFwiLi9FcnJvcnMuZXM2XCI7XHJcblxyXG4vKipcclxuICogQ2hhcnQgbW9kdWxlXHJcbiAqXHJcbiAqIEBjbGFzcyAgICAgQ2hhcnRcclxuICogQG1vZHVsZSAgICBsYXZhL0NoYXJ0XHJcbiAqIEBhdXRob3IgICAgS2V2aW4gSGlsbCA8a2V2aW5raGlsbEBnbWFpbC5jb20+XHJcbiAqIEBjb3B5cmlnaHQgKGMpIDIwMTcsIEtIaWxsIERlc2lnbnNcclxuICogQGxpY2Vuc2UgICBNSVRcclxuICovXHJcbmV4cG9ydCBjbGFzcyBSZW5kZXJhYmxlXHJcbntcclxuICAgIC8qKlxyXG4gICAgICogQ2hhcnQgQ2xhc3NcclxuICAgICAqXHJcbiAgICAgKiBUaGlzIGlzIHRoZSBqYXZhc2NyaXB0IHZlcnNpb24gb2YgYSBsYXZhY2hhcnQgd2l0aCBtZXRob2RzIGZvciBpbnRlcmFjdGluZyB3aXRoXHJcbiAgICAgKiB0aGUgZ29vZ2xlIGNoYXJ0IGFuZCB0aGUgUEhQIGxhdmFjaGFydCBvdXRwdXQuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGpzb25cclxuICAgICAqIEBjb25zdHJ1Y3RvclxyXG4gICAgICovXHJcbiAgICBjb25zdHJ1Y3Rvcihqc29uKSB7XHJcbiAgICAgICAgdGhpcy5nY2hhcnQgICAgPSBudWxsO1xyXG4gICAgICAgIHRoaXMubGFiZWwgICAgID0ganNvbi5sYWJlbDtcclxuICAgICAgICB0aGlzLm9wdGlvbnMgICA9IGpzb24ub3B0aW9ucztcclxuICAgICAgICB0aGlzLmVsZW1lbnRJZCA9IGpzb24uZWxlbWVudElkO1xyXG5cclxuICAgICAgICB0aGlzLmVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0aGlzLmVsZW1lbnRJZCk7XHJcblxyXG4gICAgICAgIGlmICghIHRoaXMuZWxlbWVudCkge1xyXG4gICAgICAgICAgICB0aHJvdyBuZXcgRWxlbWVudElkTm90Rm91bmQodGhpcy5lbGVtZW50SWQpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFVuaXF1ZSBpZGVudGlmaWVyIGZvciB0aGUgQ2hhcnQuXHJcbiAgICAgKlxyXG4gICAgICogQHJldHVybiB7c3RyaW5nfVxyXG4gICAgICovXHJcbiAgICB1dWlkKCkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLnR5cGUrJzo6Jyt0aGlzLmxhYmVsO1xyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRHJhd3MgdGhlIGNoYXJ0IHdpdGggdGhlIHByZXNldCBkYXRhIGFuZCBvcHRpb25zLlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqL1xyXG4gICAgZHJhdygpIHtcclxuICAgICAgICB0aGlzLmdjaGFydC5kcmF3KHRoaXMuZGF0YSwgdGhpcy5vcHRpb25zKTtcclxuICAgIH1cclxuXHJcbiAgICAvKipcclxuICAgICAqIFNldHMgdGhlIGRhdGEgZm9yIHRoZSBjaGFydCBieSBjcmVhdGluZyBhIG5ldyBEYXRhVGFibGVcclxuICAgICAqXHJcbiAgICAgKiBAcHVibGljXHJcbiAgICAgKiBAZXh0ZXJuYWwgXCJnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGVcIlxyXG4gICAgICogQHNlZSAgIHtAbGluayBodHRwczovL2RldmVsb3BlcnMuZ29vZ2xlLmNvbS9jaGFydC9pbnRlcmFjdGl2ZS9kb2NzL3JlZmVyZW5jZSNEYXRhVGFibGV8RGF0YVRhYmxlIENsYXNzfVxyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBheWxvYWQgSnNvbiByZXByZXNlbnRhdGlvbiBvZiBhIERhdGFUYWJsZVxyXG4gICAgICovXHJcbiAgICBzZXREYXRhKHBheWxvYWQpIHtcclxuICAgICAgICAgLy8gSWYgYSBEYXRhVGFibGUjdG9Kc29uKCkgcGF5bG9hZCBpcyByZWNlaXZlZCwgd2l0aCBmb3JtYXR0ZWQgY29sdW1ucyxcclxuICAgICAgICAgLy8gdGhlbiBwYXlsb2FkLmRhdGEgd2lsbCBiZSBkZWZpbmVkLCBhbmQgdXNlZCBhcyB0aGUgRGF0YVRhYmxlXHJcbiAgICAgICAgaWYgKHR5cGVvZiBwYXlsb2FkLmRhdGEgPT09ICdvYmplY3QnKSB7XHJcbiAgICAgICAgICAgIHBheWxvYWQgPSBwYXlsb2FkLmRhdGE7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICAvLyBTaW5jZSBHb29nbGUgY29tcGlsZXMgdGhlaXIgY2xhc3Nlcywgd2UgY2FuJ3QgdXNlIGluc3RhbmNlb2YgdG8gY2hlY2sgc2luY2VcclxuICAgICAgICAvLyBpdCBpcyBubyBsb25nZXIgY2FsbGVkIGEgXCJEYXRhVGFibGVcIiAoaXQncyBcImd2anNfUFwiIGJ1dCB0aGF0IGNvdWxkIGNoYW5nZS4uLilcclxuICAgICAgICBpZiAodHlwZW9mIHBheWxvYWQuZ2V0VGFibGVQcm9wZXJ0aWVzID09PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIHRoaXMuZGF0YSA9IHBheWxvYWQ7XHJcblxyXG4gICAgICAgIC8vIE90aGVyd2lzZSBhc3N1bWUgaXQgaXMgYSBKU09OIHJlcHJlc2VudGF0aW9uIG9mIGEgRGF0YVRhYmxlIGFuZCBjcmVhdGUgb25lLlxyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHRoaXMuZGF0YSA9IG5ldyBnb29nbGUudmlzdWFsaXphdGlvbi5EYXRhVGFibGUocGF5bG9hZCk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8qKlxyXG4gICAgICogU2V0cyB0aGUgb3B0aW9ucyBmb3IgdGhlIGNoYXJ0LlxyXG4gICAgICpcclxuICAgICAqIEBwdWJsaWNcclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zXHJcbiAgICAgKi9cclxuICAgIHNldE9wdGlvbnMob3B0aW9ucykge1xyXG4gICAgICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XHJcbiAgICB9XHJcbn1cclxuIiwiLyoganNoaW50IHVuZGVmOiB0cnVlLCB1bnVzZWQ6IHRydWUgKi9cclxuLyogZ2xvYmFscyBkb2N1bWVudCAqL1xyXG5cclxuLyoqXHJcbiAqIEZ1bmN0aW9uIHRoYXQgZG9lcyBub3RoaW5nLlxyXG4gKlxyXG4gKiBAcmV0dXJuIHt1bmRlZmluZWR9XHJcbiAqL1xyXG5leHBvcnQgZnVuY3Rpb24gbm9vcCgpIHtcclxuICAgIHJldHVybiB1bmRlZmluZWQ7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBTaW1wbGUgUHJvbWlzZSBmb3IgdGhlIERPTSB0byBiZSByZWFkeS5cclxuICpcclxuICogQHJldHVybiB7UHJvbWlzZX1cclxuICovXHJcbmV4cG9ydCBmdW5jdGlvbiBkb21Mb2FkZWQoKSB7XHJcbiAgICByZXR1cm4gbmV3IFByb21pc2UocmVzb2x2ZSA9PiB7XHJcbiAgICAgICAgaWYgKGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdpbnRlcmFjdGl2ZScgfHwgZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gJ2NvbXBsZXRlJykge1xyXG4gICAgICAgICAgICByZXNvbHZlKCk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIHJlc29sdmUpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG59XHJcblxyXG4vKipcclxuICogTWV0aG9kIGZvciBhdHRhY2hpbmcgZXZlbnRzIHRvIG9iamVjdHMuXHJcbiAqXHJcbiAqIENyZWRpdCB0byBBbGV4IFYuXHJcbiAqXHJcbiAqIEBsaW5rIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vdXNlcnMvMzI3OTM0L2FsZXgtdlxyXG4gKiBAbGluayBodHRwOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8zMTUwMTM5XHJcbiAqIEBwYXJhbSB7b2JqZWN0fSB0YXJnZXRcclxuICogQHBhcmFtIHtzdHJpbmd9IHR5cGVcclxuICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcclxuICogQHBhcmFtIHtib29sfSBldmVudFJldHVyblxyXG4gKi9cclxuZXhwb3J0IGZ1bmN0aW9uIGFkZEV2ZW50KHRhcmdldCwgdHlwZSwgY2FsbGJhY2ssIGV2ZW50UmV0dXJuKVxyXG57XHJcbiAgICBpZiAodGFyZ2V0ID09PSBudWxsIHx8IHR5cGVvZiB0YXJnZXQgPT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgICAgcmV0dXJuO1xyXG4gICAgfVxyXG5cclxuICAgIGlmICh0YXJnZXQuYWRkRXZlbnRMaXN0ZW5lcikge1xyXG4gICAgICAgIHRhcmdldC5hZGRFdmVudExpc3RlbmVyKHR5cGUsIGNhbGxiYWNrLCAhIWV2ZW50UmV0dXJuKTtcclxuICAgIH1cclxuICAgIGVsc2UgaWYodGFyZ2V0LmF0dGFjaEV2ZW50KSB7XHJcbiAgICAgICAgdGFyZ2V0LmF0dGFjaEV2ZW50KFwib25cIiArIHR5cGUsIGNhbGxiYWNrKTtcclxuICAgIH1cclxuICAgIGVsc2Uge1xyXG4gICAgICAgIHRhcmdldFtcIm9uXCIgKyB0eXBlXSA9IGNhbGxiYWNrO1xyXG4gICAgfVxyXG59XHJcblxyXG4vKipcclxuICogR2V0IGEgZnVuY3Rpb24gYSBieSBpdHMnIG5hbWVzcGFjZWQgc3RyaW5nIG5hbWUgd2l0aCBjb250ZXh0LlxyXG4gKlxyXG4gKiBDcmVkaXQgdG8gSmFzb24gQnVudGluZ1xyXG4gKlxyXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL3VzZXJzLzE3OTAvamFzb24tYnVudGluZ1xyXG4gKiBAbGluayBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzU5OTEwXHJcbiAqIEBwYXJhbSB7c3RyaW5nfSBmdW5jdGlvbk5hbWVcclxuICogQHBhcmFtIHtvYmplY3R9IGNvbnRleHRcclxuICogQHByaXZhdGVcclxuICovXHJcbmV4cG9ydCBmdW5jdGlvbiBzdHJpbmdUb0Z1bmN0aW9uKGZ1bmN0aW9uTmFtZSwgY29udGV4dCkge1xyXG4gICAgbGV0IG5hbWVzcGFjZXMgPSBmdW5jdGlvbk5hbWUuc3BsaXQoJy4nKTtcclxuICAgIGxldCBmdW5jID0gbmFtZXNwYWNlcy5wb3AoKTtcclxuXHJcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IG5hbWVzcGFjZXMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICBjb250ZXh0ID0gY29udGV4dFtuYW1lc3BhY2VzW2ldXTtcclxuICAgIH1cclxuXHJcbiAgICByZXR1cm4gY29udGV4dFtmdW5jXTtcclxufVxyXG4iXX0=
