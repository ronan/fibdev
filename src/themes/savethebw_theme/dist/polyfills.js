/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/mdn-polyfills/Element.prototype.closest.js":
/*!*****************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Element.prototype.closest.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

Element.prototype.matches||(Element.prototype.matches=Element.prototype.msMatchesSelector||Element.prototype.webkitMatchesSelector),window.Element&&!Element.prototype.closest&&(Element.prototype.closest=function(e){var t=this;do{if(t.matches(e))return t;t=t.parentElement||t.parentNode}while(null!==t&&1===t.nodeType);return null});


/***/ }),

/***/ "./node_modules/mdn-polyfills/Element.prototype.matches.js":
/*!*****************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Element.prototype.matches.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

Element.prototype.matches||(Element.prototype.matches=Element.prototype.msMatchesSelector||Element.prototype.webkitMatchesSelector);


/***/ }),

/***/ "./node_modules/mdn-polyfills/Node.prototype.append.js":
/*!*************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Node.prototype.append.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

!function(){function t(){var e=Array.prototype.slice.call(arguments),n=document.createDocumentFragment();e.forEach(function(e){var t=e instanceof Node;n.appendChild(t?e:document.createTextNode(String(e)))}),this.appendChild(n)}[Element.prototype,Document.prototype,DocumentFragment.prototype].forEach(function(e){e.hasOwnProperty("append")||Object.defineProperty(e,"append",{configurable:!0,enumerable:!0,writable:!0,value:t})})}();


/***/ }),

/***/ "./node_modules/mdn-polyfills/Node.prototype.before.js":
/*!*************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Node.prototype.before.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

!function(){function t(){var e=Array.prototype.slice.call(arguments),o=document.createDocumentFragment();e.forEach(function(e){var t=e instanceof Node;o.appendChild(t?e:document.createTextNode(String(e)))}),this.parentNode.insertBefore(o,this)}[Element.prototype,CharacterData.prototype,DocumentType.prototype].forEach(function(e){e.hasOwnProperty("before")||Object.defineProperty(e,"before",{configurable:!0,enumerable:!0,writable:!0,value:t})})}();


/***/ }),

/***/ "./node_modules/mdn-polyfills/Node.prototype.prepend.js":
/*!**************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Node.prototype.prepend.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

!function(){function t(){var e=Array.prototype.slice.call(arguments),n=document.createDocumentFragment();e.forEach(function(e){var t=e instanceof Node;n.appendChild(t?e:document.createTextNode(String(e)))}),this.insertBefore(n,this.firstChild)}[Element.prototype,Document.prototype,DocumentFragment.prototype].forEach(function(e){e.hasOwnProperty("prepend")||Object.defineProperty(e,"prepend",{configurable:!0,enumerable:!0,writable:!0,value:t})})}();


/***/ }),

/***/ "./node_modules/mdn-polyfills/Node.prototype.remove.js":
/*!*************************************************************!*\
  !*** ./node_modules/mdn-polyfills/Node.prototype.remove.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

!function(){function t(){null!==this.parentNode&&this.parentNode.removeChild(this)}[Element.prototype,CharacterData.prototype,DocumentType.prototype].forEach(function(e){e.hasOwnProperty("remove")||Object.defineProperty(e,"remove",{configurable:!0,enumerable:!0,writable:!0,value:t})})}();


/***/ }),

/***/ 8:
/*!**********************************************************************************************************************************************************************************************************************************************!*\
  !*** multi mdn-polyfills/Element.prototype.closest mdn-polyfills/Element.prototype.matches mdn-polyfills/Node.prototype.append mdn-polyfills/Node.prototype.before mdn-polyfills/Node.prototype.prepend mdn-polyfills/Node.prototype.remove ***!
  \**********************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! mdn-polyfills/Element.prototype.closest */"./node_modules/mdn-polyfills/Element.prototype.closest.js");
__webpack_require__(/*! mdn-polyfills/Element.prototype.matches */"./node_modules/mdn-polyfills/Element.prototype.matches.js");
__webpack_require__(/*! mdn-polyfills/Node.prototype.append */"./node_modules/mdn-polyfills/Node.prototype.append.js");
__webpack_require__(/*! mdn-polyfills/Node.prototype.before */"./node_modules/mdn-polyfills/Node.prototype.before.js");
__webpack_require__(/*! mdn-polyfills/Node.prototype.prepend */"./node_modules/mdn-polyfills/Node.prototype.prepend.js");
module.exports = __webpack_require__(/*! mdn-polyfills/Node.prototype.remove */"./node_modules/mdn-polyfills/Node.prototype.remove.js");


/***/ })

/******/ });
//# sourceMappingURL=polyfills.js.map