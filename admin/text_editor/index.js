var wysihtml5 = {
  version : "0.4.0pre",
  commands : {},
  dom : {},
  quirks : {},
  toolbar : {},
  lang : {},
  selection : {},
  views : {},
  INVISIBLE_SPACE : "\ufeff",
  EMPTY_FUNCTION : function() {
  },
  ELEMENT_NODE : 1,
  TEXT_NODE : 3,
  BACKSPACE_KEY : 8,
  ENTER_KEY : 13,
  ESCAPE_KEY : 27,
  SPACE_KEY : 32,
  DELETE_KEY : 46
};
window.rangy = function() {
  function isHostMethod(o, p) {
    var t = typeof o[p];
    return t == FUNCTION || (!!(t == OBJECT && o[p]) || "unknown" == t);
  }
  function isHostObject(o, p) {
    return!!(typeof o[p] == OBJECT && o[p]);
  }
  function isHostProperty(o, p) {
    return typeof o[p] != UNDEFINED;
  }
  function createMultiplePropertyTest(testFunc) {
    return function(o, matchIndexes) {
      var i = matchIndexes.length;
      for (;i--;) {
        if (!testFunc(o, matchIndexes[i])) {
          return false;
        }
      }
      return true;
    };
  }
  function isTextRange(range) {
    return range && (areHostMethods(range, textRangeMethods) && areHostProperties(range, textRangeProperties));
  }
  function fail(reason) {
    window.alert("Rangy not supported in your browser. Reason: " + reason);
    api.initialized = true;
    api.supported = false;
  }
  function init() {
    if (!api.initialized) {
      var range;
      var codeSegments = false;
      var i = false;
      if (isHostMethod(document, "createRange")) {
        range = document.createRange();
        if (areHostMethods(range, r20)) {
          if (areHostProperties(range, domRangeProperties)) {
            codeSegments = true;
          }
        }
        range.detach();
      }
      if ((range = isHostObject(document, "body") ? document.body : document.getElementsByTagName("body")[0]) && isHostMethod(range, "createTextRange")) {
        range = range.createTextRange();
        if (isTextRange(range)) {
          i = true;
        }
      }
      if (!codeSegments) {
        if (!i) {
          fail("Neither Range nor TextRange are implemented");
        }
      }
      api.initialized = true;
      api.features = {
        implementsDomRange : codeSegments,
        implementsTextRange : i
      };
      codeSegments = beginswith.concat(caseSensitive);
      i = 0;
      range = codeSegments.length;
      for (;i < range;++i) {
        try {
          codeSegments[i](api);
        } catch (ex) {
          if (isHostObject(window, "console")) {
            if (isHostMethod(window.console, "log")) {
              window.console.log("Init listener threw an exception. Continuing.", ex);
            }
          }
        }
      }
    }
  }
  function Module(name) {
    this.name = name;
    this.supported = this.initialized = false;
  }
  var OBJECT = "object";
  var FUNCTION = "function";
  var UNDEFINED = "undefined";
  var domRangeProperties = "startContainer startOffset endContainer endOffset collapsed commonAncestorContainer START_TO_START START_TO_END END_TO_START END_TO_END".split(" ");
  var r20 = "setStart setStartBefore setStartAfter setEnd setEndBefore setEndAfter collapse selectNode selectNodeContents compareBoundaryPoints deleteContents extractContents cloneContents insertNode surroundContents cloneRange toString detach".split(" ");
  var textRangeProperties = "boundingHeight boundingLeft boundingTop boundingWidth htmlText text".split(" ");
  var textRangeMethods = "collapse compareEndPoints duplicate getBookmark moveToBookmark moveToElementText parentElement pasteHTML select setEndPoint getBoundingClientRect".split(" ");
  var areHostMethods = createMultiplePropertyTest(isHostMethod);
  var completed = createMultiplePropertyTest(isHostObject);
  var areHostProperties = createMultiplePropertyTest(isHostProperty);
  var api = {
    version : "1.2.2",
    initialized : false,
    supported : true,
    util : {
      isHostMethod : isHostMethod,
      isHostObject : isHostObject,
      isHostProperty : isHostProperty,
      areHostMethods : areHostMethods,
      areHostObjects : completed,
      areHostProperties : areHostProperties,
      isTextRange : isTextRange
    },
    features : {},
    modules : {},
    config : {
      alertOnWarn : false,
      preferTextRange : false
    }
  };
  api.fail = fail;
  api.warn = function(msg) {
    msg = "Rangy warning: " + msg;
    if (api.config.alertOnWarn) {
      window.alert(msg);
    } else {
      if (typeof window.console != UNDEFINED) {
        if (typeof window.console.log != UNDEFINED) {
          window.console.log(msg);
        }
      }
    }
  };
  if ({}.hasOwnProperty) {
    api.util.extend = function(opt_attributes, protos) {
      var proto;
      for (proto in protos) {
        if (protos.hasOwnProperty(proto)) {
          opt_attributes[proto] = protos[proto];
        }
      }
    };
  } else {
    fail("hasOwnProperty not supported");
  }
  var caseSensitive = [];
  var beginswith = [];
  api.init = init;
  api.addInitListener = function(listener) {
    if (api.initialized) {
      listener(api);
    } else {
      caseSensitive.push(listener);
    }
  };
  var configList = [];
  api.addCreateMissingNativeApiListener = function(name) {
    configList.push(name);
  };
  api.createMissingNativeApi = function(win) {
    win = win || window;
    init();
    var i = 0;
    var valuesLen = configList.length;
    for (;i < valuesLen;++i) {
      configList[i](win);
    }
  };
  Module.prototype.fail = function(positionError) {
    this.initialized = true;
    this.supported = false;
    throw Error("Module '" + this.name + "' failed to load: " + positionError);
  };
  Module.prototype.warn = function(msg) {
    api.warn("Module " + this.name + ": " + msg);
  };
  Module.prototype.createError = function(name) {
    return Error("Error in Rangy " + this.name + " module: " + name);
  };
  api.createModule = function(name, initFunc) {
    var module = new Module(name);
    api.modules[name] = module;
    beginswith.push(function(deepDataAndEvents) {
      initFunc(deepDataAndEvents, module);
      module.initialized = true;
      module.supported = true;
    });
  };
  api.requireModules = function(fileExtensions) {
    var i = 0;
    var valuesLen = fileExtensions.length;
    var module;
    var moduleName;
    for (;i < valuesLen;++i) {
      moduleName = fileExtensions[i];
      module = api.modules[moduleName];
      if (!module || !(module instanceof Module)) {
        throw Error("Module '" + moduleName + "' not found");
      }
      if (!module.supported) {
        throw Error("Module '" + moduleName + "' not supported");
      }
    }
  };
  var D = false;
  completed = function() {
    if (!D) {
      D = true;
      if (!api.initialized) {
        init();
      }
    }
  };
  if (typeof window == UNDEFINED) {
    fail("No window found");
  } else {
    if (typeof document == UNDEFINED) {
      fail("No document found");
    } else {
      return isHostMethod(document, "addEventListener") && document.addEventListener("DOMContentLoaded", completed, false), isHostMethod(window, "addEventListener") ? window.addEventListener("load", completed, false) : isHostMethod(window, "attachEvent") ? window.attachEvent("onload", completed) : fail("Window does not have required addEventListener or attachEvent method"), api;
    }
  }
}();
rangy.createModule("DomUtil", function(api, inSender) {
  function getNodeIndex(node) {
    var i = 0;
    for (;node = node.previousSibling;) {
      i++;
    }
    return i;
  }
  function getCommonAncestor(node, node2) {
    var ancestors = [];
    var n;
    n = node;
    for (;n;n = n.parentNode) {
      ancestors.push(n);
    }
    n = node2;
    for (;n;n = n.parentNode) {
      if (arrayContains(ancestors, n)) {
        return n;
      }
    }
    return null;
  }
  function getClosestAncestorIn(node, ancestor, selfIsAncestor) {
    selfIsAncestor = selfIsAncestor ? node : node.parentNode;
    for (;selfIsAncestor;) {
      node = selfIsAncestor.parentNode;
      if (node === ancestor) {
        return selfIsAncestor;
      }
      selfIsAncestor = node;
    }
    return null;
  }
  function isCharacterDataNode(node) {
    node = node.nodeType;
    return 3 == node || (4 == node || 8 == node);
  }
  function insertAfter(node, precedingNode) {
    var nextNode = precedingNode.nextSibling;
    var parent = precedingNode.parentNode;
    if (nextNode) {
      parent.insertBefore(node, nextNode);
    } else {
      parent.appendChild(node);
    }
    return node;
  }
  function getDocument(node) {
    if (9 == node.nodeType) {
      return node;
    }
    if (typeof node.ownerDocument != UNDEFINED) {
      return node.ownerDocument;
    }
    if (typeof node.document != UNDEFINED) {
      return node.document;
    }
    if (node.parentNode) {
      return getDocument(node.parentNode);
    }
    throw Error("getDocument: no document found for node");
  }
  function inspectNode(node) {
    return!node ? "[No node]" : isCharacterDataNode(node) ? '"' + node.data + '"' : 1 == node.nodeType ? "<" + node.nodeName + (node.id ? ' id="' + node.id + '"' : "") + ">[" + node.childNodes.length + "]" : node.nodeName;
  }
  function NodeIterator(root) {
    this._next = this.root = root;
  }
  function DomPosition(node, offset) {
    this.node = node;
    this.offset = offset;
  }
  function DOMException(codeName) {
    this.code = this[codeName];
    this.codeName = codeName;
    this.message = "DOMException: " + this.codeName;
  }
  var UNDEFINED = "undefined";
  var util = api.util;
  if (!util.areHostMethods(document, ["createDocumentFragment", "createElement", "createTextNode"])) {
    inSender.fail("document missing a Node creation method");
  }
  if (!util.isHostMethod(document, "getElementsByTagName")) {
    inSender.fail("document missing getElementsByTagName method");
  }
  var el = document.createElement("div");
  if (!util.areHostMethods(el, ["insertBefore", "appendChild", "cloneNode"])) {
    inSender.fail("Incomplete Element implementation");
  }
  if (!util.isHostProperty(el, "innerHTML")) {
    inSender.fail("Element is missing innerHTML property");
  }
  el = document.createTextNode("test");
  if (!util.areHostMethods(el, ["splitText", "deleteData", "insertData", "appendData", "cloneNode"])) {
    inSender.fail("Incomplete Text Node implementation");
  }
  var arrayContains = function(arr, item) {
    var i = arr.length;
    for (;i--;) {
      if (arr[i] === item) {
        return true;
      }
    }
    return false;
  };
  NodeIterator.prototype = {
    _current : null,
    hasNext : function() {
      return!!this._next;
    },
    next : function() {
      var n = this._current = this._next;
      var child;
      if (this._current) {
        child = n.firstChild;
        if (!child) {
          child = null;
          for (;n !== this.root && !(child = n.nextSibling);) {
            n = n.parentNode;
          }
        }
        this._next = child;
      }
      return this._current;
    },
    detach : function() {
      this._current = this._next = this.root = null;
    }
  };
  DomPosition.prototype = {
    equals : function(pos) {
      return this.node === pos.node & this.offset == pos.offset;
    },
    inspect : function() {
      return "[DomPosition(" + inspectNode(this.node) + ":" + this.offset + ")]";
    }
  };
  DOMException.prototype = {
    INDEX_SIZE_ERR : 1,
    HIERARCHY_REQUEST_ERR : 3,
    WRONG_DOCUMENT_ERR : 4,
    NO_MODIFICATION_ALLOWED_ERR : 7,
    NOT_FOUND_ERR : 8,
    NOT_SUPPORTED_ERR : 9,
    INVALID_STATE_ERR : 11
  };
  DOMException.prototype.toString = function() {
    return this.message;
  };
  api.dom = {
    arrayContains : arrayContains,
    isHtmlNamespace : function(node) {
      var ns;
      return typeof node.namespaceURI == UNDEFINED || (null === (ns = node.namespaceURI) || "http://www.w3.org/1999/xhtml" == ns);
    },
    parentElement : function(node) {
      node = node.parentNode;
      return 1 == node.nodeType ? node : null;
    },
    getNodeIndex : getNodeIndex,
    getNodeLength : function(node) {
      var childNodes;
      return isCharacterDataNode(node) ? node.length : (childNodes = node.childNodes) ? childNodes.length : 0;
    },
    getCommonAncestor : getCommonAncestor,
    isAncestorOf : function(node, descendant, selfIsAncestor) {
      descendant = selfIsAncestor ? descendant : descendant.parentNode;
      for (;descendant;) {
        if (descendant === node) {
          return true;
        }
        descendant = descendant.parentNode;
      }
      return false;
    },
    getClosestAncestorIn : getClosestAncestorIn,
    isCharacterDataNode : isCharacterDataNode,
    insertAfter : insertAfter,
    splitDataNode : function(node, index) {
      var newNode = node.cloneNode(false);
      newNode.deleteData(0, index);
      node.deleteData(index, node.length - index);
      insertAfter(newNode, node);
      return newNode;
    },
    getDocument : getDocument,
    getWindow : function(node) {
      node = getDocument(node);
      if (typeof node.defaultView != UNDEFINED) {
        return node.defaultView;
      }
      if (typeof node.parentWindow != UNDEFINED) {
        return node.parentWindow;
      }
      throw Error("Cannot get a window object for node");
    },
    getIframeWindow : function(iframeEl) {
      if (typeof iframeEl.contentWindow != UNDEFINED) {
        return iframeEl.contentWindow;
      }
      if (typeof iframeEl.contentDocument != UNDEFINED) {
        return iframeEl.contentDocument.defaultView;
      }
      throw Error("getIframeWindow: No Window object found for iframe element");
    },
    getIframeDocument : function(iframeEl) {
      if (typeof iframeEl.contentDocument != UNDEFINED) {
        return iframeEl.contentDocument;
      }
      if (typeof iframeEl.contentWindow != UNDEFINED) {
        return iframeEl.contentWindow.document;
      }
      throw Error("getIframeWindow: No Document object found for iframe element");
    },
    getBody : function(doc) {
      return util.isHostObject(doc, "body") ? doc.body : doc.getElementsByTagName("body")[0];
    },
    getRootContainer : function(node) {
      var parent;
      for (;parent = node.parentNode;) {
        node = parent;
      }
      return node;
    },
    comparePoints : function(nodeA, root, nodeB, offsetB) {
      var nodeC;
      if (nodeA == nodeB) {
        return root === offsetB ? 0 : root < offsetB ? -1 : 1;
      }
      if (nodeC = getClosestAncestorIn(nodeB, nodeA, true)) {
        return root <= getNodeIndex(nodeC) ? -1 : 1;
      }
      if (nodeC = getClosestAncestorIn(nodeA, nodeB, true)) {
        return getNodeIndex(nodeC) < offsetB ? -1 : 1;
      }
      root = getCommonAncestor(nodeA, nodeB);
      nodeA = nodeA === root ? root : getClosestAncestorIn(nodeA, root, true);
      nodeB = nodeB === root ? root : getClosestAncestorIn(nodeB, root, true);
      if (nodeA === nodeB) {
        throw Error("comparePoints got to case 4 and childA and childB are the same!");
      }
      root = root.firstChild;
      for (;root;) {
        if (root === nodeA) {
          return-1;
        }
        if (root === nodeB) {
          return 1;
        }
        root = root.nextSibling;
      }
      throw Error("Should not be here!");
    },
    inspectNode : inspectNode,
    fragmentFromNodeChildren : function(node) {
      var fragment = getDocument(node).createDocumentFragment();
      var child;
      for (;child = node.firstChild;) {
        fragment.appendChild(child);
      }
      return fragment;
    },
    createIterator : function(root) {
      return new NodeIterator(root);
    },
    DomPosition : DomPosition
  };
  api.DOMException = DOMException;
});
rangy.createModule("DomRange", function(api) {
  function isNonTextPartiallySelected(node, range) {
    return 3 != node.nodeType && (dom.isAncestorOf(node, range.startContainer, true) || dom.isAncestorOf(node, range.endContainer, true));
  }
  function getRangeDocument(range) {
    return dom.getDocument(range.startContainer);
  }
  function dispatchEvent(range, type, args) {
    if (type = range._listeners[type]) {
      var i = 0;
      var l = type.length;
      for (;i < l;++i) {
        type[i].call(range, {
          target : range,
          args : args
        });
      }
    }
  }
  function getBoundaryBeforeNode(node) {
    return new DomPosition(node.parentNode, dom.getNodeIndex(node));
  }
  function getBoundaryAfterNode(node) {
    return new DomPosition(node.parentNode, dom.getNodeIndex(node) + 1);
  }
  function insertNodeAtPosition(node, n, o) {
    var firstNodeInserted = 11 == node.nodeType ? node.firstChild : node;
    if (dom.isCharacterDataNode(n)) {
      if (o == n.length) {
        dom.insertAfter(node, n);
      } else {
        n.parentNode.insertBefore(node, 0 == o ? n : dom.splitDataNode(n, o));
      }
    } else {
      if (o >= n.childNodes.length) {
        n.appendChild(node);
      } else {
        n.insertBefore(node, n.childNodes[o]);
      }
    }
    return firstNodeInserted;
  }
  function cloneSubtree(iterator) {
    var subIterator;
    var node;
    var frag = getRangeDocument(iterator.range).createDocumentFragment();
    for (;node = iterator.next();) {
      subIterator = iterator.isPartiallySelectedSubtree();
      node = node.cloneNode(!subIterator);
      if (subIterator) {
        subIterator = iterator.getSubtreeIterator();
        node.appendChild(cloneSubtree(subIterator));
        subIterator.detach(true);
      }
      if (10 == node.nodeType) {
        throw new DOMException("HIERARCHY_REQUEST_ERR");
      }
      frag.appendChild(node);
    }
    return frag;
  }
  function iterateSubtree(rangeIterator, func, iteratorState) {
    var node;
    var name;
    iteratorState = iteratorState || {
      stop : false
    };
    for (;node = rangeIterator.next();) {
      if (rangeIterator.isPartiallySelectedSubtree()) {
        if (false === func(node)) {
          iteratorState.stop = true;
          break;
        } else {
          if (node = rangeIterator.getSubtreeIterator(), iterateSubtree(node, func, iteratorState), node.detach(true), iteratorState.stop) {
            break;
          }
        }
      } else {
        node = dom.createIterator(node);
        for (;name = node.next();) {
          if (false === func(name)) {
            iteratorState.stop = true;
            return;
          }
        }
      }
    }
  }
  function deleteSubtree(iterator) {
    var subIterator;
    for (;iterator.next();) {
      if (iterator.isPartiallySelectedSubtree()) {
        subIterator = iterator.getSubtreeIterator();
        deleteSubtree(subIterator);
        subIterator.detach(true);
      } else {
        iterator.remove();
      }
    }
  }
  function extractSubtree(iterator) {
    var node;
    var frag = getRangeDocument(iterator.range).createDocumentFragment();
    var subIterator;
    for (;node = iterator.next();) {
      if (iterator.isPartiallySelectedSubtree()) {
        node = node.cloneNode(false);
        subIterator = iterator.getSubtreeIterator();
        node.appendChild(extractSubtree(subIterator));
        subIterator.detach(true);
      } else {
        iterator.remove();
      }
      if (10 == node.nodeType) {
        throw new DOMException("HIERARCHY_REQUEST_ERR");
      }
      frag.appendChild(node);
    }
    return frag;
  }
  function getNodesInRange(range, nodeTypes, filter) {
    var d = !(!nodeTypes || !nodeTypes.length);
    var regex;
    var filterExists = !!filter;
    if (d) {
      regex = RegExp("^(" + nodeTypes.join("|") + ")$");
    }
    var nodes = [];
    iterateSubtree(new RangeIterator(range, false), function(node) {
      if (!d || regex.test(node.nodeType)) {
        if (!filterExists || filter(node)) {
          nodes.push(node);
        }
      }
    });
    return nodes;
  }
  function inspect(range) {
    return "[" + ("undefined" == typeof range.getName ? "Range" : range.getName()) + "(" + dom.inspectNode(range.startContainer) + ":" + range.startOffset + ", " + dom.inspectNode(range.endContainer) + ":" + range.endOffset + ")]";
  }
  function RangeIterator(range, clonePartiallySelectedTextNodes) {
    this.range = range;
    this.clonePartiallySelectedTextNodes = clonePartiallySelectedTextNodes;
    if (!range.collapsed) {
      this.sc = range.startContainer;
      this.so = range.startOffset;
      this.ec = range.endContainer;
      this.eo = range.endOffset;
      var root = range.commonAncestorContainer;
      if (this.sc === this.ec && dom.isCharacterDataNode(this.sc)) {
        this.isSingleCharacterDataNode = true;
        this._first = this._last = this._next = this.sc;
      } else {
        this._first = this._next = this.sc === root && !dom.isCharacterDataNode(this.sc) ? this.sc.childNodes[this.so] : dom.getClosestAncestorIn(this.sc, root, true);
        this._last = this.ec === root && !dom.isCharacterDataNode(this.ec) ? this.ec.childNodes[this.eo - 1] : dom.getClosestAncestorIn(this.ec, root, true);
      }
    }
  }
  function RangeException(codeName) {
    this.code = this[codeName];
    this.codeName = codeName;
    this.message = "RangeException: " + this.codeName;
  }
  function RangeNodeIterator(range, nodeTypes, filter) {
    this.nodes = getNodesInRange(range, nodeTypes, filter);
    this._next = this.nodes[0];
    this._position = 0;
  }
  function createAncestorFinder(nodeTypes) {
    return function(node, selfIsAncestor) {
      var t;
      var n = selfIsAncestor ? node : node.parentNode;
      for (;n;) {
        t = n.nodeType;
        if (dom.arrayContains(nodeTypes, t)) {
          return n;
        }
        n = n.parentNode;
      }
      return null;
    };
  }
  function assertNoDocTypeNotationEntityAncestor(dataAndEvents, recurring) {
    if (getDocTypeNotationEntityAncestor(dataAndEvents, recurring)) {
      throw new RangeException("INVALID_NODE_TYPE_ERR");
    }
  }
  function assertNotDetached(range) {
    if (!range.startContainer) {
      throw new DOMException("INVALID_STATE_ERR");
    }
  }
  function assertValidNodeType(node, invalidTypes) {
    if (!dom.arrayContains(invalidTypes, node.nodeType)) {
      throw new RangeException("INVALID_NODE_TYPE_ERR");
    }
  }
  function assertValidOffset(node, recurring) {
    if (0 > recurring || recurring > (dom.isCharacterDataNode(node) ? node.length : node.childNodes.length)) {
      throw new DOMException("INDEX_SIZE_ERR");
    }
  }
  function assertSameDocumentOrFragment(node1, node2) {
    if (getDocumentOrFragmentContainer(node1, true) !== getDocumentOrFragmentContainer(node2, true)) {
      throw new DOMException("WRONG_DOCUMENT_ERR");
    }
  }
  function assertNodeNotReadOnly(node) {
    if (getReadonlyAncestor(node, true)) {
      throw new DOMException("NO_MODIFICATION_ALLOWED_ERR");
    }
  }
  function assertNode(dataAndEvents, codeName) {
    if (!dataAndEvents) {
      throw new DOMException(codeName);
    }
  }
  function assertRangeValid(range) {
    assertNotDetached(range);
    if (!dom.arrayContains(rootContainerNodeTypes, range.startContainer.nodeType) && !getDocumentOrFragmentContainer(range.startContainer, true) || (!dom.arrayContains(rootContainerNodeTypes, range.endContainer.nodeType) && !getDocumentOrFragmentContainer(range.endContainer, true) || (!(range.startOffset <= (dom.isCharacterDataNode(range.startContainer) ? range.startContainer.length : range.startContainer.childNodes.length)) || !(range.endOffset <= (dom.isCharacterDataNode(range.endContainer) ? range.endContainer.length :
    range.endContainer.childNodes.length))))) {
      throw Error("Range error: Range is no longer valid after DOM mutation (" + range.inspect() + ")");
    }
  }
  function RangePrototype() {
  }
  function copyComparisonConstantsToObject(obj) {
    obj.START_TO_START = s2s;
    obj.START_TO_END = s2e;
    obj.END_TO_END = e2e;
    obj.END_TO_START = e2s;
    obj.NODE_BEFORE = n_b;
    obj.NODE_AFTER = n_a;
    obj.NODE_BEFORE_AND_AFTER = n_b_a;
    obj.NODE_INSIDE = n_i;
  }
  function copyComparisonConstants(constructor) {
    copyComparisonConstantsToObject(constructor);
    copyComparisonConstantsToObject(constructor.prototype);
  }
  function createRangeContentRemover(remover, boundaryUpdater) {
    return function() {
      assertRangeValid(this);
      var node = this.startContainer;
      var offset = this.startOffset;
      var root = this.commonAncestorContainer;
      var iterator = new RangeIterator(this, true);
      if (node !== root) {
        node = dom.getClosestAncestorIn(node, root, true);
        offset = getBoundaryAfterNode(node);
        node = offset.node;
        offset = offset.offset;
      }
      iterateSubtree(iterator, assertNodeNotReadOnly);
      iterator.reset();
      root = remover(iterator);
      iterator.detach();
      boundaryUpdater(this, node, offset, node, offset);
      return root;
    };
  }
  function createPrototypeRange(constructor, boundaryUpdater, detacher) {
    function createBeforeAfterNodeSetter(isBefore, isStart) {
      return function(node) {
        assertNotDetached(this);
        assertValidNodeType(node, insertableNodeTypes);
        assertValidNodeType(getRootContainer(node), rootContainerNodeTypes);
        node = (isBefore ? getBoundaryBeforeNode : getBoundaryAfterNode)(node);
        (isStart ? setRangeStart : setRangeEnd)(this, node.node, node.offset);
      };
    }
    function setRangeStart(range, node, offset) {
      var ec = range.endContainer;
      var eo = range.endOffset;
      if (node !== range.startContainer || offset !== range.startOffset) {
        if (getRootContainer(node) != getRootContainer(ec) || 1 == dom.comparePoints(node, offset, ec, eo)) {
          ec = node;
          eo = offset;
        }
        boundaryUpdater(range, node, offset, ec, eo);
      }
    }
    function setRangeEnd(range, node, offset) {
      var sc = range.startContainer;
      var so = range.startOffset;
      if (node !== range.endContainer || offset !== range.endOffset) {
        if (getRootContainer(node) != getRootContainer(sc) || -1 == dom.comparePoints(node, offset, sc, so)) {
          sc = node;
          so = offset;
        }
        boundaryUpdater(range, sc, so, node, offset);
      }
    }
    constructor.prototype = new RangePrototype;
    api.util.extend(constructor.prototype, {
      setStart : function(node, recurring) {
        assertNotDetached(this);
        assertNoDocTypeNotationEntityAncestor(node, true);
        assertValidOffset(node, recurring);
        setRangeStart(this, node, recurring);
      },
      setEnd : function(node, recurring) {
        assertNotDetached(this);
        assertNoDocTypeNotationEntityAncestor(node, true);
        assertValidOffset(node, recurring);
        setRangeEnd(this, node, recurring);
      },
      setStartBefore : createBeforeAfterNodeSetter(true, true),
      setStartAfter : createBeforeAfterNodeSetter(false, true),
      setEndBefore : createBeforeAfterNodeSetter(true, false),
      setEndAfter : createBeforeAfterNodeSetter(false, false),
      collapse : function(recurring) {
        assertRangeValid(this);
        if (recurring) {
          boundaryUpdater(this, this.startContainer, this.startOffset, this.startContainer, this.startOffset);
        } else {
          boundaryUpdater(this, this.endContainer, this.endOffset, this.endContainer, this.endOffset);
        }
      },
      selectNodeContents : function(node) {
        assertNotDetached(this);
        assertNoDocTypeNotationEntityAncestor(node, true);
        boundaryUpdater(this, node, 0, node, dom.getNodeLength(node));
      },
      selectNode : function(node) {
        assertNotDetached(this);
        assertNoDocTypeNotationEntityAncestor(node, false);
        assertValidNodeType(node, insertableNodeTypes);
        var start = getBoundaryBeforeNode(node);
        node = getBoundaryAfterNode(node);
        boundaryUpdater(this, start.node, start.offset, node.node, node.offset);
      },
      extractContents : createRangeContentRemover(extractSubtree, boundaryUpdater),
      deleteContents : createRangeContentRemover(deleteSubtree, boundaryUpdater),
      canSurroundContents : function() {
        assertRangeValid(this);
        assertNodeNotReadOnly(this.startContainer);
        assertNodeNotReadOnly(this.endContainer);
        var iterator = new RangeIterator(this, true);
        var b = iterator._first && isNonTextPartiallySelected(iterator._first, this) || iterator._last && isNonTextPartiallySelected(iterator._last, this);
        iterator.detach();
        return!b;
      },
      detach : function() {
        detacher(this);
      },
      splitBoundaries : function() {
        assertRangeValid(this);
        var sc = this.startContainer;
        var so = this.startOffset;
        var ec = this.endContainer;
        var eo = this.endOffset;
        var startEndSame = sc === ec;
        if (dom.isCharacterDataNode(ec)) {
          if (0 < eo && eo < ec.length) {
            dom.splitDataNode(ec, eo);
          }
        }
        if (dom.isCharacterDataNode(sc)) {
          if (0 < so && so < sc.length) {
            sc = dom.splitDataNode(sc, so);
            if (startEndSame) {
              eo -= so;
              ec = sc;
            } else {
              if (ec == sc.parentNode) {
                if (eo >= dom.getNodeIndex(sc)) {
                  eo++;
                }
              }
            }
            so = 0;
          }
        }
        boundaryUpdater(this, sc, so, ec, eo);
      },
      normalizeBoundaries : function() {
        assertRangeValid(this);
        var sc = this.startContainer;
        var so = this.startOffset;
        var ec = this.endContainer;
        var eo = this.endOffset;
        var mergeForward = function(node) {
          var sibling = node.nextSibling;
          if (sibling) {
            if (sibling.nodeType == node.nodeType) {
              ec = node;
              eo = node.length;
              node.appendData(sibling.data);
              sibling.parentNode.removeChild(sibling);
            }
          }
        };
        var mergeBackward = function(node) {
          var sibling = node.previousSibling;
          if (sibling && sibling.nodeType == node.nodeType) {
            sc = node;
            var nodeLength = node.length;
            so = sibling.length;
            node.insertData(0, sibling.data);
            sibling.parentNode.removeChild(sibling);
            if (sc == ec) {
              eo += so;
              ec = sc;
            } else {
              if (ec == node.parentNode) {
                sibling = dom.getNodeIndex(node);
                if (eo == sibling) {
                  ec = node;
                  eo = nodeLength;
                } else {
                  if (eo > sibling) {
                    eo--;
                  }
                }
              }
            }
          }
        };
        var endNode = true;
        if (dom.isCharacterDataNode(ec)) {
          if (ec.length == eo) {
            mergeForward(ec);
          }
        } else {
          if (0 < eo) {
            if (endNode = ec.childNodes[eo - 1]) {
              if (dom.isCharacterDataNode(endNode)) {
                mergeForward(endNode);
              }
            }
          }
          endNode = !this.collapsed;
        }
        if (endNode) {
          if (dom.isCharacterDataNode(sc)) {
            if (0 == so) {
              mergeBackward(sc);
            }
          } else {
            if (so < sc.childNodes.length) {
              if (mergeForward = sc.childNodes[so]) {
                if (dom.isCharacterDataNode(mergeForward)) {
                  mergeBackward(mergeForward);
                }
              }
            }
          }
        } else {
          sc = ec;
          so = eo;
        }
        boundaryUpdater(this, sc, so, ec, eo);
      },
      collapseToPoint : function(node, offset) {
        assertNotDetached(this);
        assertNoDocTypeNotationEntityAncestor(node, true);
        assertValidOffset(node, offset);
        if (node !== this.startContainer || (offset !== this.startOffset || (node !== this.endContainer || offset !== this.endOffset))) {
          boundaryUpdater(this, node, offset, node, offset);
        }
      }
    });
    copyComparisonConstants(constructor);
  }
  function updateCollapsedAndCommonAncestor(range) {
    range.collapsed = range.startContainer === range.endContainer && range.startOffset === range.endOffset;
    range.commonAncestorContainer = range.collapsed ? range.startContainer : dom.getCommonAncestor(range.startContainer, range.endContainer);
  }
  function updateBoundaries(range, startContainer, startOffset, endContainer, endOffset) {
    var startMoved = range.startContainer !== startContainer || range.startOffset !== startOffset;
    var endMoved = range.endContainer !== endContainer || range.endOffset !== endOffset;
    range.startContainer = startContainer;
    range.startOffset = startOffset;
    range.endContainer = endContainer;
    range.endOffset = endOffset;
    updateCollapsedAndCommonAncestor(range);
    dispatchEvent(range, "boundarychange", {
      startMoved : startMoved,
      endMoved : endMoved
    });
  }
  function Range(doc) {
    this.startContainer = doc;
    this.startOffset = 0;
    this.endContainer = doc;
    this.endOffset = 0;
    this._listeners = {
      boundarychange : [],
      detach : []
    };
    updateCollapsedAndCommonAncestor(this);
  }
  api.requireModules(["DomUtil"]);
  var dom = api.dom;
  var DomPosition = dom.DomPosition;
  var DOMException = api.DOMException;
  RangeIterator.prototype = {
    _current : null,
    _next : null,
    _first : null,
    _last : null,
    isSingleCharacterDataNode : false,
    reset : function() {
      this._current = null;
      this._next = this._first;
    },
    hasNext : function() {
      return!!this._next;
    },
    next : function() {
      var current = this._current = this._next;
      if (current) {
        this._next = current !== this._last ? current.nextSibling : null;
        if (dom.isCharacterDataNode(current)) {
          if (this.clonePartiallySelectedTextNodes) {
            if (current === this.ec) {
              (current = current.cloneNode(true)).deleteData(this.eo, current.length - this.eo);
            }
            if (this._current === this.sc) {
              (current = current.cloneNode(true)).deleteData(0, this.so);
            }
          }
        }
      }
      return current;
    },
    remove : function() {
      var current = this._current;
      var start;
      var end;
      if (dom.isCharacterDataNode(current) && (current === this.sc || current === this.ec)) {
        start = current === this.sc ? this.so : 0;
        end = current === this.ec ? this.eo : current.length;
        if (start != end) {
          current.deleteData(start, end - start);
        }
      } else {
        if (current.parentNode) {
          current.parentNode.removeChild(current);
        }
      }
    },
    isPartiallySelectedSubtree : function() {
      return isNonTextPartiallySelected(this._current, this.range);
    },
    getSubtreeIterator : function() {
      var subRange;
      if (this.isSingleCharacterDataNode) {
        subRange = this.range.cloneRange();
        subRange.collapse();
      } else {
        subRange = new Range(getRangeDocument(this.range));
        var current = this._current;
        var startContainer = current;
        var startOffset = 0;
        var endContainer = current;
        var endOffset = dom.getNodeLength(current);
        if (dom.isAncestorOf(current, this.sc, true)) {
          startContainer = this.sc;
          startOffset = this.so;
        }
        if (dom.isAncestorOf(current, this.ec, true)) {
          endContainer = this.ec;
          endOffset = this.eo;
        }
        updateBoundaries(subRange, startContainer, startOffset, endContainer, endOffset);
      }
      return new RangeIterator(subRange, this.clonePartiallySelectedTextNodes);
    },
    detach : function(dataAndEvents) {
      if (dataAndEvents) {
        this.range.detach();
      }
      this.range = this._current = this._next = this._first = this._last = this.sc = this.so = this.ec = this.eo = null;
    }
  };
  RangeException.prototype = {
    BAD_BOUNDARYPOINTS_ERR : 1,
    INVALID_NODE_TYPE_ERR : 2
  };
  RangeException.prototype.toString = function() {
    return this.message;
  };
  RangeNodeIterator.prototype = {
    _current : null,
    hasNext : function() {
      return!!this._next;
    },
    next : function() {
      this._current = this._next;
      this._next = this.nodes[++this._position];
      return this._current;
    },
    detach : function() {
      this._current = this._next = this.nodes = null;
    }
  };
  var insertableNodeTypes = [1, 3, 4, 5, 7, 8, 10];
  var rootContainerNodeTypes = [2, 9, 11];
  var surroundNodeTypes = [1, 3, 4, 5, 7, 8, 10, 11];
  var beforeAfterNodeTypes = [1, 3, 4, 5, 7, 8];
  var getRootContainer = dom.getRootContainer;
  var getDocumentOrFragmentContainer = createAncestorFinder([9, 11]);
  var getReadonlyAncestor = createAncestorFinder([5, 6, 10, 12]);
  var getDocTypeNotationEntityAncestor = createAncestorFinder([6, 10, 12]);
  var target = document.createElement("style");
  var htmlParsingConforms = false;
  try {
    target.innerHTML = "<b>x</b>";
    htmlParsingConforms = 3 == target.firstChild.nodeType;
  } catch (da) {
  }
  api.features.htmlParsingConforms = htmlParsingConforms;
  var rangeProperties = "startContainer startOffset endContainer endOffset collapsed commonAncestorContainer".split(" ");
  var s2s = 0;
  var s2e = 1;
  var e2e = 2;
  var e2s = 3;
  var n_b = 0;
  var n_a = 1;
  var n_b_a = 2;
  var n_i = 3;
  RangePrototype.prototype = {
    attachListener : function(type, listener) {
      this._listeners[type].push(listener);
    },
    compareBoundaryPoints : function(how, range) {
      assertRangeValid(this);
      assertSameDocumentOrFragment(this.startContainer, range.startContainer);
      var prefixA = how == e2s || how == s2s ? "start" : "end";
      var prefixB = how == s2e || how == s2s ? "start" : "end";
      return dom.comparePoints(this[prefixA + "Container"], this[prefixA + "Offset"], range[prefixB + "Container"], range[prefixB + "Offset"]);
    },
    insertNode : function(node) {
      assertRangeValid(this);
      assertValidNodeType(node, surroundNodeTypes);
      assertNodeNotReadOnly(this.startContainer);
      if (dom.isAncestorOf(node, this.startContainer, true)) {
        throw new DOMException("HIERARCHY_REQUEST_ERR");
      }
      node = insertNodeAtPosition(node, this.startContainer, this.startOffset);
      this.setStartBefore(node);
    },
    cloneContents : function() {
      assertRangeValid(this);
      var clone;
      var element;
      if (this.collapsed) {
        return getRangeDocument(this).createDocumentFragment();
      }
      if (this.startContainer === this.endContainer && dom.isCharacterDataNode(this.startContainer)) {
        return clone = this.startContainer.cloneNode(true), clone.data = clone.data.slice(this.startOffset, this.endOffset), element = getRangeDocument(this).createDocumentFragment(), element.appendChild(clone), element;
      }
      element = new RangeIterator(this, true);
      clone = cloneSubtree(element);
      element.detach();
      return clone;
    },
    canSurroundContents : function() {
      assertRangeValid(this);
      assertNodeNotReadOnly(this.startContainer);
      assertNodeNotReadOnly(this.endContainer);
      var iterator = new RangeIterator(this, true);
      var b = iterator._first && isNonTextPartiallySelected(iterator._first, this) || iterator._last && isNonTextPartiallySelected(iterator._last, this);
      iterator.detach();
      return!b;
    },
    surroundContents : function(node) {
      assertValidNodeType(node, beforeAfterNodeTypes);
      if (!this.canSurroundContents()) {
        throw new RangeException("BAD_BOUNDARYPOINTS_ERR");
      }
      var ol = this.extractContents();
      if (node.hasChildNodes()) {
        for (;node.lastChild;) {
          node.removeChild(node.lastChild);
        }
      }
      insertNodeAtPosition(node, this.startContainer, this.startOffset);
      node.appendChild(ol);
      this.selectNode(node);
    },
    cloneRange : function() {
      assertRangeValid(this);
      var range = new Range(getRangeDocument(this));
      var i = rangeProperties.length;
      var prop;
      for (;i--;) {
        prop = rangeProperties[i];
        range[prop] = this[prop];
      }
      return range;
    },
    toString : function() {
      assertRangeValid(this);
      var sc = this.startContainer;
      if (sc === this.endContainer && dom.isCharacterDataNode(sc)) {
        return 3 == sc.nodeType || 4 == sc.nodeType ? sc.data.slice(this.startOffset, this.endOffset) : "";
      }
      var textBits = [];
      sc = new RangeIterator(this, true);
      iterateSubtree(sc, function(node) {
        if (3 == node.nodeType || 4 == node.nodeType) {
          textBits.push(node.data);
        }
      });
      sc.detach();
      return textBits.join("");
    },
    compareNode : function(node) {
      assertRangeValid(this);
      var parentNode = node.parentNode;
      var nodeIndex = dom.getNodeIndex(node);
      if (!parentNode) {
        throw new DOMException("NOT_FOUND_ERR");
      }
      node = this.comparePoint(parentNode, nodeIndex);
      parentNode = this.comparePoint(parentNode, nodeIndex + 1);
      return 0 > node ? 0 < parentNode ? n_b_a : n_b : 0 < parentNode ? n_a : n_i;
    },
    comparePoint : function(node, offset) {
      assertRangeValid(this);
      assertNode(node, "HIERARCHY_REQUEST_ERR");
      assertSameDocumentOrFragment(node, this.startContainer);
      return 0 > dom.comparePoints(node, offset, this.startContainer, this.startOffset) ? -1 : 0 < dom.comparePoints(node, offset, this.endContainer, this.endOffset) ? 1 : 0;
    },
    createContextualFragment : htmlParsingConforms ? function(xhtml) {
      var node = this.startContainer;
      var doc = dom.getDocument(node);
      if (!node) {
        throw new DOMException("INVALID_STATE_ERR");
      }
      var el = null;
      if (1 == node.nodeType) {
        el = node;
      } else {
        if (dom.isCharacterDataNode(node)) {
          el = dom.parentElement(node);
        }
      }
      el = null === el || "HTML" == el.nodeName && (dom.isHtmlNamespace(dom.getDocument(el).documentElement) && dom.isHtmlNamespace(el)) ? doc.createElement("body") : el.cloneNode(false);
      el.innerHTML = xhtml;
      return dom.fragmentFromNodeChildren(el);
    } : function(xhtml) {
      assertNotDetached(this);
      var element = getRangeDocument(this).createElement("body");
      element.innerHTML = xhtml;
      return dom.fragmentFromNodeChildren(element);
    },
    toHtml : function() {
      assertRangeValid(this);
      var container = getRangeDocument(this).createElement("div");
      container.appendChild(this.cloneContents());
      return container.innerHTML;
    },
    intersectsNode : function(node, recurring) {
      assertRangeValid(this);
      assertNode(node, "NOT_FOUND_ERR");
      if (dom.getDocument(node) !== getRangeDocument(this)) {
        return false;
      }
      var parent = node.parentNode;
      var offset = dom.getNodeIndex(node);
      assertNode(parent, "NOT_FOUND_ERR");
      var startComparison = dom.comparePoints(parent, offset, this.endContainer, this.endOffset);
      parent = dom.comparePoints(parent, offset + 1, this.startContainer, this.startOffset);
      return recurring ? 0 >= startComparison && 0 <= parent : 0 > startComparison && 0 < parent;
    },
    isPointInRange : function(node, offset) {
      assertRangeValid(this);
      assertNode(node, "HIERARCHY_REQUEST_ERR");
      assertSameDocumentOrFragment(node, this.startContainer);
      return 0 <= dom.comparePoints(node, offset, this.startContainer, this.startOffset) && 0 >= dom.comparePoints(node, offset, this.endContainer, this.endOffset);
    },
    intersectsRange : function(range, dataAndEvents) {
      assertRangeValid(this);
      if (getRangeDocument(range) != getRangeDocument(this)) {
        throw new DOMException("WRONG_DOCUMENT_ERR");
      }
      var start = dom.comparePoints(this.startContainer, this.startOffset, range.endContainer, range.endOffset);
      var end = dom.comparePoints(this.endContainer, this.endOffset, range.startContainer, range.startOffset);
      return dataAndEvents ? 0 >= start && 0 <= end : 0 > start && 0 < end;
    },
    intersection : function(range) {
      if (this.intersectsRange(range)) {
        var startComparison = dom.comparePoints(this.startContainer, this.startOffset, range.startContainer, range.startOffset);
        var endComparison = dom.comparePoints(this.endContainer, this.endOffset, range.endContainer, range.endOffset);
        var intersectionRange = this.cloneRange();
        if (-1 == startComparison) {
          intersectionRange.setStart(range.startContainer, range.startOffset);
        }
        if (1 == endComparison) {
          intersectionRange.setEnd(range.endContainer, range.endOffset);
        }
        return intersectionRange;
      }
      return null;
    },
    union : function(range) {
      if (this.intersectsRange(range, true)) {
        var unionRange = this.cloneRange();
        if (-1 == dom.comparePoints(range.startContainer, range.startOffset, this.startContainer, this.startOffset)) {
          unionRange.setStart(range.startContainer, range.startOffset);
        }
        if (1 == dom.comparePoints(range.endContainer, range.endOffset, this.endContainer, this.endOffset)) {
          unionRange.setEnd(range.endContainer, range.endOffset);
        }
        return unionRange;
      }
      throw new RangeException("Ranges do not intersect");
    },
    containsNode : function(node, deepDataAndEvents) {
      return deepDataAndEvents ? this.intersectsNode(node, false) : this.compareNode(node) == n_i;
    },
    containsNodeContents : function(node) {
      return 0 <= this.comparePoint(node, 0) && 0 >= this.comparePoint(node, dom.getNodeLength(node));
    },
    containsRange : function(range) {
      return this.intersection(range).equals(range);
    },
    containsNodeText : function(node) {
      var nodeRange = this.cloneRange();
      nodeRange.selectNode(node);
      var textNodes = nodeRange.getNodes([3]);
      return 0 < textNodes.length ? (nodeRange.setStart(textNodes[0], 0), node = textNodes.pop(), nodeRange.setEnd(node, node.length), node = this.containsRange(nodeRange), nodeRange.detach(), node) : this.containsNodeContents(node);
    },
    createNodeIterator : function(nodeTypes, filter) {
      assertRangeValid(this);
      return new RangeNodeIterator(this, nodeTypes, filter);
    },
    getNodes : function(nodeTypes, filter) {
      assertRangeValid(this);
      return getNodesInRange(this, nodeTypes, filter);
    },
    getDocument : function() {
      return getRangeDocument(this);
    },
    collapseBefore : function(node) {
      assertNotDetached(this);
      this.setEndBefore(node);
      this.collapse(false);
    },
    collapseAfter : function(node) {
      assertNotDetached(this);
      this.setStartAfter(node);
      this.collapse(true);
    },
    getName : function() {
      return "DomRange";
    },
    equals : function(range) {
      return Range.rangesEqual(this, range);
    },
    inspect : function() {
      return inspect(this);
    }
  };
  createPrototypeRange(Range, updateBoundaries, function(range) {
    assertNotDetached(range);
    range.startContainer = range.startOffset = range.endContainer = range.endOffset = null;
    range.collapsed = range.commonAncestorContainer = null;
    dispatchEvent(range, "detach", null);
    range._listeners = null;
  });
  api.rangePrototype = RangePrototype.prototype;
  Range.rangeProperties = rangeProperties;
  Range.RangeIterator = RangeIterator;
  Range.copyComparisonConstants = copyComparisonConstants;
  Range.createPrototypeRange = createPrototypeRange;
  Range.inspect = inspect;
  Range.getRangeDocument = getRangeDocument;
  Range.rangesEqual = function(r1, r2) {
    return r1.startContainer === r2.startContainer && (r1.startOffset === r2.startOffset && (r1.endContainer === r2.endContainer && r1.endOffset === r2.endOffset));
  };
  api.DomRange = Range;
  api.RangeException = RangeException;
});
rangy.createModule("WrappedRange", function(api) {
  function getTextRangeBoundaryPosition(textRange, workingNode, isStart, isCollapsed) {
    var workingRange = textRange.duplicate();
    workingRange.collapse(isStart);
    var containerElement = workingRange.parentElement();
    if (!dom.isAncestorOf(workingNode, containerElement, true)) {
      containerElement = workingNode;
    }
    if (!containerElement.canHaveHTML) {
      return new DomPosition(containerElement.parentNode, dom.getNodeIndex(containerElement));
    }
    workingNode = dom.getDocument(containerElement).createElement("span");
    var comparison;
    var boundaryNode = isStart ? "StartToStart" : "StartToEnd";
    do {
      containerElement.insertBefore(workingNode, workingNode.previousSibling);
      workingRange.moveToElementText(workingNode);
    } while (0 < (comparison = workingRange.compareEndPoints(boundaryNode, textRange)) && workingNode.previousSibling);
    boundaryNode = workingNode.nextSibling;
    if (-1 == comparison && (boundaryNode && dom.isCharacterDataNode(boundaryNode))) {
      workingRange.setEndPoint(isStart ? "EndToStart" : "EndToEnd", textRange);
      if (/[\r\n]/.test(boundaryNode.data)) {
        containerElement = workingRange.duplicate();
        isStart = containerElement.text.replace(/\r\n/g, "\r").length;
        isStart = containerElement.moveStart("character", isStart);
        for (;-1 == containerElement.compareEndPoints("StartToEnd", containerElement);) {
          isStart++;
          containerElement.moveStart("character", 1);
        }
      } else {
        isStart = workingRange.text.length;
      }
      containerElement = new DomPosition(boundaryNode, isStart);
    } else {
      boundaryNode = (isCollapsed || !isStart) && workingNode.previousSibling;
      containerElement = (isStart = (isCollapsed || isStart) && workingNode.nextSibling) && dom.isCharacterDataNode(isStart) ? new DomPosition(isStart, 0) : boundaryNode && dom.isCharacterDataNode(boundaryNode) ? new DomPosition(boundaryNode, boundaryNode.length) : new DomPosition(containerElement, dom.getNodeIndex(workingNode));
    }
    workingNode.parentNode.removeChild(workingNode);
    return containerElement;
  }
  function createBoundaryTextRange(boundaryPosition, isStart) {
    var boundaryNode;
    var boundaryParent;
    var boundaryOffset = boundaryPosition.offset;
    var workingNode = dom.getDocument(boundaryPosition.node);
    var workingRange = workingNode.body.createTextRange();
    var h = dom.isCharacterDataNode(boundaryPosition.node);
    if (h) {
      boundaryNode = boundaryPosition.node;
      boundaryParent = boundaryNode.parentNode;
    } else {
      boundaryNode = boundaryPosition.node.childNodes;
      boundaryNode = boundaryOffset < boundaryNode.length ? boundaryNode[boundaryOffset] : null;
      boundaryParent = boundaryPosition.node;
    }
    workingNode = workingNode.createElement("span");
    workingNode.innerHTML = "&#feff;";
    if (boundaryNode) {
      boundaryParent.insertBefore(workingNode, boundaryNode);
    } else {
      boundaryParent.appendChild(workingNode);
    }
    workingRange.moveToElementText(workingNode);
    workingRange.collapse(!isStart);
    boundaryParent.removeChild(workingNode);
    if (h) {
      workingRange[isStart ? "moveStart" : "moveEnd"]("character", boundaryOffset);
    }
    return workingRange;
  }
  api.requireModules(["DomUtil", "DomRange"]);
  var WrappedRange;
  var dom = api.dom;
  var DomPosition = dom.DomPosition;
  var DomRange = api.DomRange;
  if (api.features.implementsDomRange && (!api.features.implementsTextRange || !api.config.preferTextRange)) {
    var updateRangeProperties = function(range) {
      var i = rangeProperties.length;
      var prop;
      for (;i--;) {
        prop = rangeProperties[i];
        range[prop] = range.nativeRange[prop];
      }
    };
    var rangeProto;
    var rangeProperties = DomRange.rangeProperties;
    var createBeforeAfterNodeSetter;
    WrappedRange = function(range) {
      if (!range) {
        throw Error("Range must be specified");
      }
      this.nativeRange = range;
      updateRangeProperties(this);
    };
    DomRange.createPrototypeRange(WrappedRange, function(range, startContainer, recurring, endContainer, endOffset) {
      var g = range.endContainer !== endContainer || range.endOffset != endOffset;
      if (range.startContainer !== startContainer || (range.startOffset != recurring || g)) {
        range.setEnd(endContainer, endOffset);
        range.setStart(startContainer, recurring);
      }
    }, function(range) {
      range.nativeRange.detach();
      range.detached = true;
      var i = rangeProperties.length;
      var prop;
      for (;i--;) {
        prop = rangeProperties[i];
        range[prop] = null;
      }
    });
    rangeProto = WrappedRange.prototype;
    rangeProto.selectNode = function(node) {
      this.nativeRange.selectNode(node);
      updateRangeProperties(this);
    };
    rangeProto.deleteContents = function() {
      this.nativeRange.deleteContents();
      updateRangeProperties(this);
    };
    rangeProto.extractContents = function() {
      var frag = this.nativeRange.extractContents();
      updateRangeProperties(this);
      return frag;
    };
    rangeProto.cloneContents = function() {
      return this.nativeRange.cloneContents();
    };
    rangeProto.surroundContents = function(node) {
      this.nativeRange.surroundContents(node);
      updateRangeProperties(this);
    };
    rangeProto.collapse = function(recurring) {
      this.nativeRange.collapse(recurring);
      updateRangeProperties(this);
    };
    rangeProto.cloneRange = function() {
      return new WrappedRange(this.nativeRange.cloneRange());
    };
    rangeProto.refresh = function() {
      updateRangeProperties(this);
    };
    rangeProto.toString = function() {
      return this.nativeRange.toString();
    };
    var testTextNode = document.createTextNode("test");
    dom.getBody(document).appendChild(testTextNode);
    var range = document.createRange();
    range.setStart(testTextNode, 0);
    range.setEnd(testTextNode, 0);
    try {
      range.setStart(testTextNode, 1);
      rangeProto.setStart = function(node, recurring) {
        this.nativeRange.setStart(node, recurring);
        updateRangeProperties(this);
      };
      rangeProto.setEnd = function(node, recurring) {
        this.nativeRange.setEnd(node, recurring);
        updateRangeProperties(this);
      };
      createBeforeAfterNodeSetter = function(oppositeName) {
        return function(node) {
          this.nativeRange[oppositeName](node);
          updateRangeProperties(this);
        };
      };
    } catch (v) {
      rangeProto.setStart = function(node, recurring) {
        try {
          this.nativeRange.setStart(node, recurring);
        } catch (c) {
          this.nativeRange.setEnd(node, recurring);
          this.nativeRange.setStart(node, recurring);
        }
        updateRangeProperties(this);
      };
      rangeProto.setEnd = function(node, recurring) {
        try {
          this.nativeRange.setEnd(node, recurring);
        } catch (c) {
          this.nativeRange.setStart(node, recurring);
          this.nativeRange.setEnd(node, recurring);
        }
        updateRangeProperties(this);
      };
      createBeforeAfterNodeSetter = function(name, oppositeName) {
        return function(node) {
          try {
            this.nativeRange[name](node);
          } catch (d) {
            this.nativeRange[oppositeName](node);
            this.nativeRange[name](node);
          }
          updateRangeProperties(this);
        };
      };
    }
    rangeProto.setStartBefore = createBeforeAfterNodeSetter("setStartBefore", "setEndBefore");
    rangeProto.setStartAfter = createBeforeAfterNodeSetter("setStartAfter", "setEndAfter");
    rangeProto.setEndBefore = createBeforeAfterNodeSetter("setEndBefore", "setStartBefore");
    rangeProto.setEndAfter = createBeforeAfterNodeSetter("setEndAfter", "setStartAfter");
    range.selectNodeContents(testTextNode);
    rangeProto.selectNodeContents = range.startContainer == testTextNode && (range.endContainer == testTextNode && (0 == range.startOffset && range.endOffset == testTextNode.length)) ? function(node) {
      this.nativeRange.selectNodeContents(node);
      updateRangeProperties(this);
    } : function(node) {
      this.setStart(node, 0);
      this.setEnd(node, DomRange.getEndOffset(node));
    };
    range.selectNodeContents(testTextNode);
    range.setEnd(testTextNode, 3);
    createBeforeAfterNodeSetter = document.createRange();
    createBeforeAfterNodeSetter.selectNodeContents(testTextNode);
    createBeforeAfterNodeSetter.setEnd(testTextNode, 4);
    createBeforeAfterNodeSetter.setStart(testTextNode, 2);
    rangeProto.compareBoundaryPoints = -1 == range.compareBoundaryPoints(range.START_TO_END, createBeforeAfterNodeSetter) & 1 == range.compareBoundaryPoints(range.END_TO_START, createBeforeAfterNodeSetter) ? function(type, range) {
      range = range.nativeRange || range;
      if (type == range.START_TO_END) {
        type = range.END_TO_START;
      } else {
        if (type == range.END_TO_START) {
          type = range.START_TO_END;
        }
      }
      return this.nativeRange.compareBoundaryPoints(type, range);
    } : function(type, range) {
      return this.nativeRange.compareBoundaryPoints(type, range.nativeRange || range);
    };
    if (api.util.isHostMethod(range, "createContextualFragment")) {
      rangeProto.createContextualFragment = function(html) {
        return this.nativeRange.createContextualFragment(html);
      };
    }
    dom.getBody(document).removeChild(testTextNode);
    range.detach();
    createBeforeAfterNodeSetter.detach();
    api.createNativeRange = function(doc) {
      doc = doc || document;
      return doc.createRange();
    };
  } else {
    if (api.features.implementsTextRange) {
      WrappedRange = function(textRange) {
        this.textRange = textRange;
        this.refresh();
      };
      WrappedRange.prototype = new DomRange(document);
      WrappedRange.prototype.refresh = function() {
        var start;
        var end;
        var endEl = this.textRange;
        start = endEl.parentElement();
        var range3 = endEl.duplicate();
        range3.collapse(true);
        end = range3.parentElement();
        range3 = endEl.duplicate();
        range3.collapse(false);
        endEl = range3.parentElement();
        end = end == endEl ? end : dom.getCommonAncestor(end, endEl);
        end = end == start ? end : dom.getCommonAncestor(start, end);
        if (0 == this.textRange.compareEndPoints("StartToEnd", this.textRange)) {
          end = start = getTextRangeBoundaryPosition(this.textRange, end, true, true);
        } else {
          start = getTextRangeBoundaryPosition(this.textRange, end, true, false);
          end = getTextRangeBoundaryPosition(this.textRange, end, false, false);
        }
        this.setStart(start.node, start.offset);
        this.setEnd(end.node, end.offset);
      };
      DomRange.copyComparisonConstants(WrappedRange);
      rangeProto = function() {
        return this;
      }();
      if ("undefined" == typeof rangeProto.Range) {
        rangeProto.Range = WrappedRange;
      }
      api.createNativeRange = function(doc) {
        doc = doc || document;
        return doc.body.createTextRange();
      };
    }
  }
  if (api.features.implementsTextRange) {
    WrappedRange.rangeToTextRange = function(range) {
      if (range.collapsed) {
        return createBoundaryTextRange(new DomPosition(range.startContainer, range.startOffset), true);
      }
      var nodeRange = createBoundaryTextRange(new DomPosition(range.startContainer, range.startOffset), true);
      var endRange = createBoundaryTextRange(new DomPosition(range.endContainer, range.endOffset), false);
      range = dom.getDocument(range.startContainer).body.createTextRange();
      range.setEndPoint("StartToStart", nodeRange);
      range.setEndPoint("EndToEnd", endRange);
      return range;
    };
  }
  WrappedRange.prototype.getName = function() {
    return "WrappedRange";
  };
  api.WrappedRange = WrappedRange;
  api.createRange = function(doc) {
    doc = doc || document;
    return new WrappedRange(api.createNativeRange(doc));
  };
  api.createRangyRange = function(doc) {
    doc = doc || document;
    return new DomRange(doc);
  };
  api.createIframeRange = function(iframeEl) {
    return api.createRange(dom.getIframeDocument(iframeEl));
  };
  api.createIframeRangyRange = function(iframeEl) {
    return api.createRangyRange(dom.getIframeDocument(iframeEl));
  };
  api.addCreateMissingNativeApiListener(function(iframeDoc) {
    iframeDoc = iframeDoc.document;
    if ("undefined" == typeof iframeDoc.createRange) {
      iframeDoc.createRange = function() {
        return api.createRange(this);
      };
    }
    iframeDoc = iframeDoc = null;
  });
});
rangy.createModule("WrappedSelection", function(api, inSender) {
  function getWinSelection(winParam) {
    return(winParam || window).getSelection();
  }
  function getSelection(win) {
    return(win || window).document.selection;
  }
  function updateAnchorAndFocusFromRange(sel, range, backwards) {
    var anchorPrefix = backwards ? "end" : "start";
    backwards = backwards ? "start" : "end";
    sel.anchorNode = range[anchorPrefix + "Container"];
    sel.anchorOffset = range[anchorPrefix + "Offset"];
    sel.focusNode = range[backwards + "Container"];
    sel.focusOffset = range[backwards + "Offset"];
  }
  function updateEmptySelection(sel) {
    sel.anchorNode = sel.focusNode = null;
    sel.anchorOffset = sel.focusOffset = 0;
    sel.rangeCount = 0;
    sel.isCollapsed = true;
    sel._ranges.length = 0;
  }
  function getNativeRange(range) {
    var nativeRange;
    if (range instanceof DomRange) {
      nativeRange = range._selectionNativeRange;
      if (!nativeRange) {
        nativeRange = api.createNativeRange(dom.getDocument(range.startContainer));
        nativeRange.setEnd(range.endContainer, range.endOffset);
        nativeRange.setStart(range.startContainer, range.startOffset);
        range._selectionNativeRange = nativeRange;
        range.attachListener("detach", function() {
          this._selectionNativeRange = null;
        });
      }
    } else {
      if (range instanceof WrappedRange) {
        nativeRange = range.nativeRange;
      } else {
        if (api.features.implementsDomRange) {
          if (range instanceof dom.getWindow(range.startContainer).Range) {
            nativeRange = range;
          }
        }
      }
    }
    return nativeRange;
  }
  function getSingleElementFromRange(range) {
    var rangeNodes = range.getNodes();
    var i;
    a: {
      if (!rangeNodes.length || 1 != rangeNodes[0].nodeType) {
        i = false;
      } else {
        i = 1;
        var len = rangeNodes.length;
        for (;i < len;++i) {
          if (!dom.isAncestorOf(rangeNodes[0], rangeNodes[i])) {
            i = false;
            break a;
          }
        }
        i = true;
      }
    }
    if (!i) {
      throw Error("getSingleElementFromRange: range " + range.inspect() + " did not consist of a single element");
    }
    return rangeNodes[0];
  }
  function updateFromTextRange(sel, range) {
    var wrappedRange = new WrappedRange(range);
    sel._ranges = [wrappedRange];
    updateAnchorAndFocusFromRange(sel, wrappedRange, false);
    sel.rangeCount = 1;
    sel.isCollapsed = wrappedRange.collapsed;
  }
  function updateControlSelection(sel) {
    sel._ranges.length = 0;
    if ("None" == sel.docSelection.type) {
      updateEmptySelection(sel);
    } else {
      var controlRange = sel.docSelection.createRange();
      if (controlRange && "undefined" != typeof controlRange.text) {
        updateFromTextRange(sel, controlRange);
      } else {
        sel.rangeCount = controlRange.length;
        var range;
        var doc = dom.getDocument(controlRange.item(0));
        var i = 0;
        for (;i < sel.rangeCount;++i) {
          range = api.createRange(doc);
          range.selectNode(controlRange.item(i));
          sel._ranges.push(range);
        }
        sel.isCollapsed = 1 == sel.rangeCount && sel._ranges[0].collapsed;
        updateAnchorAndFocusFromRange(sel, sel._ranges[sel.rangeCount - 1], false);
      }
    }
  }
  function addRangeToControlSelection(sel, range) {
    var values = sel.docSelection.createRange();
    var el = getSingleElementFromRange(range);
    var doc = dom.getDocument(values.item(0));
    doc = dom.getBody(doc).createControlRange();
    var i = 0;
    var valuesLen = values.length;
    for (;i < valuesLen;++i) {
      doc.add(values.item(i));
    }
    try {
      doc.add(el);
    } catch (h) {
      throw Error("addRange(): Element within the specified Range could not be added to control selection (does it have layout?)");
    }
    doc.select();
    updateControlSelection(sel);
  }
  function WrappedSelection(selection, docSelection, win) {
    this.nativeSelection = selection;
    this.docSelection = docSelection;
    this._ranges = [];
    this.win = win;
    this.refresh();
  }
  function createControlSelection(sel, ranges) {
    var doc = dom.getDocument(ranges[0].startContainer);
    doc = dom.getBody(doc).createControlRange();
    var i = 0;
    var el;
    for (;i < rangeCount;++i) {
      el = getSingleElementFromRange(ranges[i]);
      try {
        doc.add(el);
      } catch (g) {
        throw Error("setRanges(): Element within the one of the specified Ranges could not be added to control selection (does it have layout?)");
      }
    }
    doc.select();
    updateControlSelection(sel);
  }
  function assertNodeInSameDocument(sel, node) {
    if (sel.anchorNode && dom.getDocument(sel.anchorNode) !== dom.getDocument(node)) {
      throw new DOMException("WRONG_DOCUMENT_ERR");
    }
  }
  function inspect(sel) {
    var qs = [];
    var focus = new DomPosition(sel.anchorNode, sel.anchorOffset);
    var anchor = new DomPosition(sel.focusNode, sel.focusOffset);
    var name = "function" == typeof sel.getName ? sel.getName() : "Selection";
    if ("undefined" != typeof sel.rangeCount) {
      var i = 0;
      var len = sel.rangeCount;
      for (;i < len;++i) {
        qs[i] = DomRange.inspect(sel.getRangeAt(i));
      }
    }
    return "[" + name + "(Ranges: " + qs.join(", ") + ")(anchor: " + focus.inspect() + ", focus: " + anchor.inspect() + "]";
  }
  api.requireModules(["DomUtil", "DomRange", "WrappedRange"]);
  api.config.checkSelectionRanges = true;
  var dom = api.dom;
  var util = api.util;
  var DomRange = api.DomRange;
  var WrappedRange = api.WrappedRange;
  var DOMException = api.DOMException;
  var DomPosition = dom.DomPosition;
  var getDocSelection;
  var selectionIsCollapsed;
  var testSelection = api.util.isHostMethod(window, "getSelection");
  var implementsDocSelection = api.util.isHostObject(document, "selection");
  var useDocumentSelection = implementsDocSelection && (!testSelection || api.config.preferTextRange);
  if (useDocumentSelection) {
    getDocSelection = getSelection;
    api.isSelectionValid = function(winParam) {
      winParam = (winParam || window).document;
      var $sel = winParam.selection;
      return "None" != $sel.type || dom.getDocument($sel.createRange().parentElement()) == winParam;
    };
  } else {
    if (testSelection) {
      getDocSelection = getWinSelection;
      api.isSelectionValid = function() {
        return true;
      };
    } else {
      inSender.fail("Neither document.selection or window.getSelection() detected.");
    }
  }
  api.getNativeSelection = getDocSelection;
  testSelection = getDocSelection();
  var testRange = api.createNativeRange(document);
  var selProto = dom.getBody(document);
  var selectionHasAnchorAndFocus = util.areHostObjects(testSelection, util.areHostProperties(testSelection, ["anchorOffset", "focusOffset"]));
  api.features.selectionHasAnchorAndFocus = selectionHasAnchorAndFocus;
  var selectionHasExtend = util.isHostMethod(testSelection, "extend");
  api.features.selectionHasExtend = selectionHasExtend;
  var selectionHasRangeCount = "number" == typeof testSelection.rangeCount;
  api.features.selectionHasRangeCount = selectionHasRangeCount;
  var selectionSupportsMultipleRanges = false;
  var d = true;
  if (util.areHostMethods(testSelection, ["addRange", "getRangeAt", "removeAllRanges"]) && ("number" == typeof testSelection.rangeCount && api.features.implementsDomRange)) {
    var iframe = document.createElement("iframe");
    selProto.appendChild(iframe);
    d = dom.getIframeDocument(iframe);
    d.open();
    d.write("<html><head></head><body>12</body></html>");
    d.close();
    var sel = dom.getIframeWindow(iframe).getSelection();
    var textNode = d.documentElement.lastChild.firstChild;
    var r1 = d.createRange();
    r1.setStart(textNode, 1);
    r1.collapse(true);
    sel.addRange(r1);
    d = 1 == sel.rangeCount;
    sel.removeAllRanges();
    var r2 = r1.cloneRange();
    r1.setStart(textNode, 0);
    r2.setEnd(textNode, 2);
    sel.addRange(r1);
    sel.addRange(r2);
    selectionSupportsMultipleRanges = 2 == sel.rangeCount;
    r1.detach();
    r2.detach();
    selProto.removeChild(iframe);
  }
  api.features.selectionSupportsMultipleRanges = selectionSupportsMultipleRanges;
  api.features.collapsedNonEditableSelectionsSupported = d;
  var implementsControlRange = false;
  if (selProto) {
    if (util.isHostMethod(selProto, "createControlRange")) {
      selProto = selProto.createControlRange();
      if (util.areHostProperties(selProto, ["item", "add"])) {
        implementsControlRange = true;
      }
    }
  }
  api.features.implementsControlRange = implementsControlRange;
  selectionIsCollapsed = selectionHasAnchorAndFocus ? function(sel) {
    return sel.anchorNode === sel.focusNode && sel.anchorOffset === sel.focusOffset;
  } : function(sel) {
    return sel.rangeCount ? sel.getRangeAt(sel.rangeCount - 1).collapsed : false;
  };
  var getSelectionRangeAt;
  if (util.isHostMethod(testSelection, "getRangeAt")) {
    getSelectionRangeAt = function(sel, index) {
      try {
        return sel.getRangeAt(index);
      } catch (c) {
        return null;
      }
    };
  } else {
    if (selectionHasAnchorAndFocus) {
      getSelectionRangeAt = function(sel) {
        var range = dom.getDocument(sel.anchorNode);
        range = api.createRange(range);
        range.setStart(sel.anchorNode, sel.anchorOffset);
        range.setEnd(sel.focusNode, sel.focusOffset);
        if (range.collapsed !== this.isCollapsed) {
          range.setStart(sel.focusNode, sel.focusOffset);
          range.setEnd(sel.anchorNode, sel.anchorOffset);
        }
        return range;
      };
    }
  }
  api.getSelection = function(win) {
    win = win || window;
    var sel = win._rangySelection;
    var nativeSel = getDocSelection(win);
    var docSel = implementsDocSelection ? getSelection(win) : null;
    if (sel) {
      sel.nativeSelection = nativeSel;
      sel.docSelection = docSel;
      sel.refresh(win);
    } else {
      sel = new WrappedSelection(nativeSel, docSel, win);
      win._rangySelection = sel;
    }
    return sel;
  };
  api.getIframeSelection = function(iframeEl) {
    return api.getSelection(dom.getIframeWindow(iframeEl));
  };
  selProto = WrappedSelection.prototype;
  if (!useDocumentSelection && (selectionHasAnchorAndFocus && util.areHostMethods(testSelection, ["removeAllRanges", "addRange"]))) {
    selProto.removeAllRanges = function() {
      this.nativeSelection.removeAllRanges();
      updateEmptySelection(this);
    };
    var addRangeBackwards = function(sel, range) {
      var endRange = DomRange.getRangeDocument(range);
      endRange = api.createRange(endRange);
      endRange.collapseToPoint(range.endContainer, range.endOffset);
      sel.nativeSelection.addRange(getNativeRange(endRange));
      sel.nativeSelection.extend(range.startContainer, range.startOffset);
      sel.refresh();
    };
    selProto.addRange = selectionHasRangeCount ? function(range, backwards) {
      if (implementsControlRange && (implementsDocSelection && "Control" == this.docSelection.type)) {
        addRangeToControlSelection(this, range);
      } else {
        if (backwards && selectionHasExtend) {
          addRangeBackwards(this, range);
        } else {
          var nativeRange;
          if (selectionSupportsMultipleRanges) {
            nativeRange = this.rangeCount;
          } else {
            this.removeAllRanges();
            nativeRange = 0;
          }
          this.nativeSelection.addRange(getNativeRange(range));
          this.rangeCount = this.nativeSelection.rangeCount;
          if (this.rangeCount == nativeRange + 1) {
            if (api.config.checkSelectionRanges) {
              if (nativeRange = getSelectionRangeAt(this.nativeSelection, this.rangeCount - 1)) {
                if (!DomRange.rangesEqual(nativeRange, range)) {
                  range = new WrappedRange(nativeRange);
                }
              }
            }
            this._ranges[this.rangeCount - 1] = range;
            updateAnchorAndFocusFromRange(this, range, selectionIsBackwards(this.nativeSelection));
            this.isCollapsed = selectionIsCollapsed(this);
          } else {
            this.refresh();
          }
        }
      }
    } : function(range, backwards) {
      if (backwards && selectionHasExtend) {
        addRangeBackwards(this, range);
      } else {
        this.nativeSelection.addRange(getNativeRange(range));
        this.refresh();
      }
    };
    selProto.setRanges = function(ranges) {
      if (implementsControlRange && 1 < ranges.length) {
        createControlSelection(this, ranges);
      } else {
        this.removeAllRanges();
        var i = 0;
        var len = ranges.length;
        for (;i < len;++i) {
          this.addRange(ranges[i]);
        }
      }
    };
  } else {
    if (util.isHostMethod(testSelection, "empty") && (util.isHostMethod(testRange, "select") && (implementsControlRange && useDocumentSelection))) {
      selProto.removeAllRanges = function() {
        try {
          if (this.docSelection.empty(), "None" != this.docSelection.type) {
            var doc;
            if (this.anchorNode) {
              doc = dom.getDocument(this.anchorNode);
            } else {
              if ("Control" == this.docSelection.type) {
                var controlRange = this.docSelection.createRange();
                if (controlRange.length) {
                  doc = dom.getDocument(controlRange.item(0)).body.createTextRange();
                }
              }
            }
            if (doc) {
              doc.body.createTextRange().select();
              this.docSelection.empty();
            }
          }
        } catch (c) {
        }
        updateEmptySelection(this);
      };
      selProto.addRange = function(range) {
        if ("Control" == this.docSelection.type) {
          addRangeToControlSelection(this, range);
        } else {
          WrappedRange.rangeToTextRange(range).select();
          this._ranges[0] = range;
          this.rangeCount = 1;
          this.isCollapsed = this._ranges[0].collapsed;
          updateAnchorAndFocusFromRange(this, range, false);
        }
      };
      selProto.setRanges = function(ranges) {
        this.removeAllRanges();
        var len = ranges.length;
        if (1 < len) {
          createControlSelection(this, ranges);
        } else {
          if (len) {
            this.addRange(ranges[0]);
          }
        }
      };
    } else {
      return inSender.fail("No means of selecting a Range or TextRange was found"), false;
    }
  }
  selProto.getRangeAt = function(index) {
    if (0 > index || index >= this.rangeCount) {
      throw new DOMException("INDEX_SIZE_ERR");
    }
    return this._ranges[index];
  };
  var refreshSelection;
  if (useDocumentSelection) {
    refreshSelection = function(sel) {
      var range;
      if (api.isSelectionValid(sel.win)) {
        range = sel.docSelection.createRange();
      } else {
        range = dom.getBody(sel.win.document).createTextRange();
        range.collapse(true);
      }
      if ("Control" == sel.docSelection.type) {
        updateControlSelection(sel);
      } else {
        if (range && "undefined" != typeof range.text) {
          updateFromTextRange(sel, range);
        } else {
          updateEmptySelection(sel);
        }
      }
    };
  } else {
    if (util.isHostMethod(testSelection, "getRangeAt") && "number" == typeof testSelection.rangeCount) {
      refreshSelection = function(sel) {
        if (implementsControlRange && (implementsDocSelection && "Control" == sel.docSelection.type)) {
          updateControlSelection(sel);
        } else {
          if (sel._ranges.length = sel.rangeCount = sel.nativeSelection.rangeCount, sel.rangeCount) {
            var i = 0;
            var len = sel.rangeCount;
            for (;i < len;++i) {
              sel._ranges[i] = new api.WrappedRange(sel.nativeSelection.getRangeAt(i));
            }
            updateAnchorAndFocusFromRange(sel, sel._ranges[sel.rangeCount - 1], selectionIsBackwards(sel.nativeSelection));
            sel.isCollapsed = selectionIsCollapsed(sel);
          } else {
            updateEmptySelection(sel);
          }
        }
      };
    } else {
      if (selectionHasAnchorAndFocus && ("boolean" == typeof testSelection.isCollapsed && ("boolean" == typeof testRange.collapsed && api.features.implementsDomRange))) {
        refreshSelection = function(sel) {
          var nativeSel;
          nativeSel = sel.nativeSelection;
          if (nativeSel.anchorNode) {
            nativeSel = getSelectionRangeAt(nativeSel, 0);
            sel._ranges = [nativeSel];
            sel.rangeCount = 1;
            nativeSel = sel.nativeSelection;
            sel.anchorNode = nativeSel.anchorNode;
            sel.anchorOffset = nativeSel.anchorOffset;
            sel.focusNode = nativeSel.focusNode;
            sel.focusOffset = nativeSel.focusOffset;
            sel.isCollapsed = selectionIsCollapsed(sel);
          } else {
            updateEmptySelection(sel);
          }
        };
      } else {
        return inSender.fail("No means of obtaining a Range or TextRange from the user's selection was found"), false;
      }
    }
  }
  selProto.refresh = function(i) {
    var oldRanges = i ? this._ranges.slice(0) : null;
    refreshSelection(this);
    if (i) {
      i = oldRanges.length;
      if (i != this._ranges.length) {
        return false;
      }
      for (;i--;) {
        if (!DomRange.rangesEqual(oldRanges[i], this._ranges[i])) {
          return false;
        }
      }
      return true;
    }
  };
  var removeRangeManually = function(sel, range) {
    var ranges = sel.getAllRanges();
    var removed = false;
    sel.removeAllRanges();
    var i = 0;
    var len = ranges.length;
    for (;i < len;++i) {
      if (removed || range !== ranges[i]) {
        sel.addRange(ranges[i]);
      } else {
        removed = true;
      }
    }
    if (!sel.rangeCount) {
      updateEmptySelection(sel);
    }
  };
  selProto.removeRange = implementsControlRange ? function(range) {
    if ("Control" == this.docSelection.type) {
      var values = this.docSelection.createRange();
      range = getSingleElementFromRange(range);
      var row = dom.getDocument(values.item(0));
      row = dom.getBody(row).createControlRange();
      var cur;
      var optionsDidChange = false;
      var i = 0;
      var valuesLen = values.length;
      for (;i < valuesLen;++i) {
        cur = values.item(i);
        if (cur !== range || optionsDidChange) {
          row.add(values.item(i));
        } else {
          optionsDidChange = true;
        }
      }
      row.select();
      updateControlSelection(this);
    } else {
      removeRangeManually(this, range);
    }
  } : function(range) {
    removeRangeManually(this, range);
  };
  var selectionIsBackwards;
  if (!useDocumentSelection && (selectionHasAnchorAndFocus && api.features.implementsDomRange)) {
    selectionIsBackwards = function(sel) {
      var backwards = false;
      if (sel.anchorNode) {
        backwards = 1 == dom.comparePoints(sel.anchorNode, sel.anchorOffset, sel.focusNode, sel.focusOffset);
      }
      return backwards;
    };
    selProto.isBackwards = function() {
      return selectionIsBackwards(this);
    };
  } else {
    selectionIsBackwards = selProto.isBackwards = function() {
      return false;
    };
  }
  selProto.toString = function() {
    var rangeTexts = [];
    var i = 0;
    var len = this.rangeCount;
    for (;i < len;++i) {
      rangeTexts[i] = "" + this._ranges[i];
    }
    return rangeTexts.join("");
  };
  selProto.collapse = function(recurring, offset) {
    assertNodeInSameDocument(this, recurring);
    var range = api.createRange(dom.getDocument(recurring));
    range.collapseToPoint(recurring, offset);
    this.removeAllRanges();
    this.addRange(range);
    this.isCollapsed = true;
  };
  selProto.collapseToStart = function() {
    if (this.rangeCount) {
      var range = this._ranges[0];
      this.collapse(range.startContainer, range.startOffset);
    } else {
      throw new DOMException("INVALID_STATE_ERR");
    }
  };
  selProto.collapseToEnd = function() {
    if (this.rangeCount) {
      var range = this._ranges[this.rangeCount - 1];
      this.collapse(range.endContainer, range.endOffset);
    } else {
      throw new DOMException("INVALID_STATE_ERR");
    }
  };
  selProto.selectAllChildren = function(node) {
    assertNodeInSameDocument(this, node);
    var range = api.createRange(dom.getDocument(node));
    range.selectNodeContents(node);
    this.removeAllRanges();
    this.addRange(range);
  };
  selProto.deleteFromDocument = function() {
    if (implementsControlRange && (implementsDocSelection && "Control" == this.docSelection.type)) {
      var a = this.docSelection.createRange();
      var t;
      for (;a.length;) {
        t = a.item(0);
        a.remove(t);
        t.parentNode.removeChild(t);
      }
      this.refresh();
    } else {
      if (this.rangeCount) {
        a = this.getAllRanges();
        this.removeAllRanges();
        t = 0;
        var al = a.length;
        for (;t < al;++t) {
          a[t].deleteContents();
        }
        this.addRange(a[al - 1]);
      }
    }
  };
  selProto.getAllRanges = function() {
    return this._ranges.slice(0);
  };
  selProto.setSingleRange = function(keepData) {
    this.setRanges([keepData]);
  };
  selProto.containsNode = function(node, deepDataAndEvents) {
    var i = 0;
    var ii = this._ranges.length;
    for (;i < ii;++i) {
      if (this._ranges[i].containsNode(node, deepDataAndEvents)) {
        return true;
      }
    }
    return false;
  };
  selProto.toHtml = function() {
    var html = "";
    if (this.rangeCount) {
      html = DomRange.getRangeDocument(this._ranges[0]).createElement("div");
      var i = 0;
      var ii = this._ranges.length;
      for (;i < ii;++i) {
        html.appendChild(this._ranges[i].cloneContents());
      }
      html = html.innerHTML;
    }
    return html;
  };
  selProto.getName = function() {
    return "WrappedSelection";
  };
  selProto.inspect = function() {
    return inspect(this);
  };
  selProto.detach = function() {
    this.win = this.anchorNode = this.focusNode = this.win._rangySelection = null;
  };
  WrappedSelection.inspect = inspect;
  api.Selection = WrappedSelection;
  api.selectionPrototype = selProto;
  api.addCreateMissingNativeApiListener(function(win) {
    if ("undefined" == typeof win.getSelection) {
      win.getSelection = function() {
        return api.getSelection(this);
      };
    }
    win = null;
  });
});
var Base = function() {
};
Base.extend = function(opt_attributes, protoProps) {
  var extend = Base.prototype.extend;
  Base._prototyping = true;
  var proto = new this;
  extend.call(proto, opt_attributes);
  proto.base = function() {
  };
  delete Base._prototyping;
  var constructor = proto.constructor;
  var klass = proto.constructor = function() {
    if (!Base._prototyping) {
      if (this._constructing || this.constructor == klass) {
        this._constructing = true;
        constructor.apply(this, arguments);
        delete this._constructing;
      } else {
        if (null != arguments[0]) {
          return(arguments[0].extend || extend).call(arguments[0], proto);
        }
      }
    }
  };
  klass.ancestor = this;
  klass.extend = this.extend;
  klass.forEach = this.forEach;
  klass.implement = this.implement;
  klass.prototype = proto;
  klass.toString = this.toString;
  klass.valueOf = function(object) {
    return "object" == object ? klass : constructor.valueOf();
  };
  extend.call(klass, protoProps);
  if ("function" == typeof klass.init) {
    klass.init();
  }
  return klass;
};
Base.prototype = {
  extend : function(opt_attributes, value) {
    if (1 < arguments.length) {
      var tmp = this[opt_attributes];
      if (tmp && ("function" == typeof value && ((!tmp.valueOf || tmp.valueOf() != value.valueOf()) && /\bbase\b/.test(value)))) {
        var matcherFunction = value.valueOf();
        value = function() {
          var previous = this.base || Base.prototype.base;
          this.base = tmp;
          var props = matcherFunction.apply(this, arguments);
          this.base = previous;
          return props;
        };
        value.valueOf = function(object) {
          return "object" == object ? value : matcherFunction;
        };
        value.toString = Base.toString;
      }
      this[opt_attributes] = value;
    } else {
      if (opt_attributes) {
        var extend = Base.prototype.extend;
        if (!Base._prototyping) {
          if ("function" != typeof this) {
            extend = this.extend || extend;
          }
        }
        var proto = {
          toSource : null
        };
        var hidden = ["constructor", "toString", "valueOf"];
        var i = Base._prototyping ? 0 : 1;
        for (;key = hidden[i++];) {
          if (opt_attributes[key] != proto[key]) {
            extend.call(this, key, opt_attributes[key]);
          }
        }
        var key;
        for (key in opt_attributes) {
          if (!proto[key]) {
            extend.call(this, key, opt_attributes[key]);
          }
        }
      }
    }
    return this;
  }
};
Base = Base.extend({
  constructor : function(attributes) {
    this.extend(attributes);
  }
}, {
  ancestor : Object,
  version : "1.1",
  forEach : function(obj, f, opt_obj) {
    var key;
    for (key in obj) {
      if (void 0 === this.prototype[key]) {
        f.call(opt_obj, obj[key], key, obj);
      }
    }
  },
  implement : function() {
    var i = 0;
    for (;i < arguments.length;i++) {
      if ("function" == typeof arguments[i]) {
        arguments[i](this.prototype);
      } else {
        this.prototype.extend(arguments[i]);
      }
    }
    return this;
  },
  toString : function() {
    return String(this.valueOf());
  }
});
wysihtml5.browser = function() {
  var userAgent = navigator.userAgent;
  var testElement = document.createElement("div");
  var isIE = -1 !== userAgent.indexOf("MSIE") && -1 === userAgent.indexOf("Opera");
  var isGecko = -1 !== userAgent.indexOf("Gecko") && -1 === userAgent.indexOf("KHTML");
  var isWebKit = -1 !== userAgent.indexOf("AppleWebKit/");
  var isChrome = -1 !== userAgent.indexOf("Chrome/");
  var isOpera = -1 !== userAgent.indexOf("Opera/");
  var buggyCommands = {
    formatBlock : isIE,
    insertUnorderedList : isIE || isWebKit,
    insertOrderedList : isIE || isWebKit
  };
  var supported = {
    insertHTML : isGecko
  };
  return{
    USER_AGENT : userAgent,
    supported : function() {
      var userAgent = this.USER_AGENT.toLowerCase();
      var hasContentEditableSupport = "contentEditable" in testElement;
      var hasEditingApiSupport = document.execCommand && (document.queryCommandSupported && document.queryCommandState);
      var hasQuerySelectorSupport = document.querySelector && document.querySelectorAll;
      userAgent = this.isIos() && 5 > +(/ipad|iphone|ipod/.test(userAgent) && userAgent.match(/ os (\d+).+? like mac os x/) || [, 0])[1] || (this.isAndroid() && 4 > +(userAgent.match(/android (\d+)/) || [, 0])[1] || (-1 !== userAgent.indexOf("opera mobi") || -1 !== userAgent.indexOf("hpwos/")));
      return hasContentEditableSupport && (hasEditingApiSupport && (hasQuerySelectorSupport && !userAgent));
    },
    isTouchDevice : function() {
      return this.supportsEvent("touchmove");
    },
    isIos : function() {
      return/ipad|iphone|ipod/i.test(this.USER_AGENT);
    },
    isAndroid : function() {
      return-1 !== this.USER_AGENT.indexOf("Android");
    },
    supportsSandboxedIframes : function() {
      return isIE;
    },
    throwsMixedContentWarningWhenIframeSrcIsEmpty : function() {
      return!("querySelector" in document);
    },
    displaysCaretInEmptyContentEditableCorrectly : function() {
      return isIE;
    },
    hasCurrentStyleProperty : function() {
      return "currentStyle" in testElement;
    },
    hasHistoryIssue : function() {
      return isGecko;
    },
    insertsLineBreaksOnReturn : function() {
      return isGecko;
    },
    supportsPlaceholderAttributeOn : function(element) {
      return "placeholder" in element;
    },
    supportsEvent : function(eventName) {
      var r;
      if (!(r = "on" + eventName in testElement)) {
        testElement.setAttribute("on" + eventName, "return;");
        r = "function" === typeof testElement["on" + eventName];
      }
      return r;
    },
    supportsEventsInIframeCorrectly : function() {
      return!isOpera;
    },
    supportsHTML5Tags : function(d) {
      d = d.createElement("div");
      d.innerHTML = "<article>foo</article>";
      return "<article>foo</article>" === d.innerHTML.toLowerCase();
    },
    supportsCommand : function(doc, command) {
      if (!buggyCommands[command]) {
        try {
          return doc.queryCommandSupported(command);
        } catch (c) {
        }
        try {
          return doc.queryCommandEnabled(command);
        } catch (d) {
          return!!supported[command];
        }
      }
      return false;
    },
    doesAutoLinkingInContentEditable : function() {
      return isIE;
    },
    canDisableAutoLinking : function() {
      return this.supportsCommand(document, "AutoUrlDetect");
    },
    clearsContentEditableCorrectly : function() {
      return isGecko || (isOpera || isWebKit);
    },
    supportsGetAttributeCorrectly : function() {
      return "1" != document.createElement("td").getAttribute("rowspan");
    },
    canSelectImagesInContentEditable : function() {
      return isGecko || (isIE || isOpera);
    },
    autoScrollsToCaret : function() {
      return!isWebKit;
    },
    autoClosesUnclosedTags : function() {
      var element = testElement.cloneNode(false);
      var returnValue;
      element.innerHTML = "<p><div></div>";
      element = element.innerHTML.toLowerCase();
      returnValue = "<p></p><div></div>" === element || "<p><div></div></p>" === element;
      this.autoClosesUnclosedTags = function() {
        return returnValue;
      };
      return returnValue;
    },
    supportsNativeGetElementsByClassName : function() {
      return-1 !== String(document.getElementsByClassName).indexOf("[native code]");
    },
    supportsSelectionModify : function() {
      return "getSelection" in window && "modify" in window.getSelection();
    },
    needsSpaceAfterLineBreak : function() {
      return isOpera;
    },
    supportsSpeechApiOn : function(input) {
      return 11 <= (userAgent.match(/Chrome\/(\d+)/) || [, 0])[1] && ("onwebkitspeechchange" in input || "speech" in input);
    },
    crashesWhenDefineProperty : function(value) {
      return isIE && ("XMLHttpRequest" === value || "XDomainRequest" === value);
    },
    doesAsyncFocus : function() {
      return isIE;
    },
    hasProblemsSettingCaretAfterImg : function() {
      return isIE;
    },
    hasUndoInContextMenu : function() {
      return isGecko || (isChrome || isOpera);
    },
    hasInsertNodeIssue : function() {
      return isOpera;
    },
    hasIframeFocusIssue : function() {
      return isIE;
    }
  };
}();
wysihtml5.lang.array = function(arr) {
  return{
    contains : function(obj) {
      if (arr.indexOf) {
        return-1 !== arr.indexOf(obj);
      }
      var i = 0;
      var e = arr.length;
      for (;i < e;i++) {
        if (arr[i] === obj) {
          return true;
        }
      }
      return false;
    },
    without : function(value) {
      value = wysihtml5.lang.array(value);
      var newArr = [];
      var i = 0;
      var e = arr.length;
      for (;i < e;i++) {
        if (!value.contains(arr[i])) {
          newArr.push(arr[i]);
        }
      }
      return newArr;
    },
    get : function() {
      var i = 0;
      var e = arr.length;
      var value = [];
      for (;i < e;i++) {
        value.push(arr[i]);
      }
      return value;
    }
  };
};
wysihtml5.lang.Dispatcher = Base.extend({
  on : function(name, fn) {
    this.events = this.events || {};
    this.events[name] = this.events[name] || [];
    this.events[name].push(fn);
    return this;
  },
  off : function(name, o) {
    this.events = this.events || {};
    var i = 0;
    var arr;
    var value;
    if (name) {
      arr = this.events[name] || [];
      value = [];
      for (;i < arr.length;i++) {
        if (arr[i] !== o) {
          if (o) {
            value.push(arr[i]);
          }
        }
      }
      this.events[name] = value;
    } else {
      this.events = {};
    }
    return this;
  },
  fire : function(name, opt_attributes) {
    this.events = this.events || {};
    var codeSegments = this.events[name] || [];
    var i = 0;
    for (;i < codeSegments.length;i++) {
      codeSegments[i].call(this, opt_attributes);
    }
    return this;
  },
  observe : function() {
    return this.on.apply(this, arguments);
  },
  stopObserving : function() {
    return this.off.apply(this, arguments);
  }
});
wysihtml5.lang.object = function(a) {
  return{
    merge : function(b) {
      var prop;
      for (prop in b) {
        a[prop] = b[prop];
      }
      return this;
    },
    get : function() {
      return a;
    },
    clone : function() {
      var out = {};
      var p;
      for (p in a) {
        out[p] = a[p];
      }
      return out;
    },
    isArray : function() {
      return "[object Array]" === Object.prototype.toString.call(a);
    }
  };
};
(function() {
  var trimLeft = /^\s+/;
  var r20 = /\s+$/;
  wysihtml5.lang.string = function(str) {
    str = String(str);
    return{
      trim : function() {
        return str.replace(trimLeft, "").replace(r20, "");
      },
      interpolate : function(vars) {
        var i;
        for (i in vars) {
          str = this.replace("#{" + i + "}").by(vars[i]);
        }
        return str;
      },
      replace : function(regex) {
        return{
          by : function(value) {
            return str.split(regex).join(value);
          }
        };
      }
    };
  };
})();
(function(wysihtml5) {
  function update(element) {
    if (!that.contains(element.nodeName)) {
      if (element.nodeType === wysihtml5.TEXT_NODE && element.data.match(rclass)) {
        var parent = element.parentNode;
        var p;
        p = parent.ownerDocument;
        var o = p._wysihtml5_tempElement;
        if (!o) {
          o = p._wysihtml5_tempElement = p.createElement("div");
        }
        p = o;
        p.innerHTML = "<span></span>" + element.data.replace(rclass, function(dataAndEvents, text) {
          var name = (text.match(cx) || [])[1] || "";
          var value = old[name];
          text = text.replace(cx, "");
          if (text.split(value).length > text.split(name).length) {
            text += name;
            name = "";
          }
          var key = value = text;
          if (text.length > length) {
            key = key.substr(0, length) + "...";
          }
          if ("www." === value.substr(0, 4)) {
            value = "http://" + value;
          }
          return'<a href="' + value + '">' + key + "</a>" + name;
        });
        p.removeChild(p.firstChild);
        for (;p.firstChild;) {
          parent.insertBefore(p.firstChild, element);
        }
        parent.removeChild(element);
      } else {
        parent = wysihtml5.lang.array(element.childNodes).get();
        p = parent.length;
        o = 0;
        for (;o < p;o++) {
          update(parent[o]);
        }
        return element;
      }
    }
  }
  var that = wysihtml5.lang.array("CODE PRE A SCRIPT HEAD TITLE STYLE".split(" "));
  var rclass = /((https?:\/\/|www\.)[^\s<]{3,})/gi;
  var cx = /([^\w\/\-](,?))$/i;
  var length = 100;
  var old = {
    ")" : "(",
    "]" : "[",
    "}" : "{"
  };
  wysihtml5.dom.autoLink = function(element) {
    var elm;
    a: {
      elm = element;
      var attr;
      for (;elm.parentNode;) {
        elm = elm.parentNode;
        attr = elm.nodeName;
        if (that.contains(attr)) {
          elm = true;
          break a;
        }
        if ("body" === attr) {
          break;
        }
      }
      elm = false;
    }
    if (elm) {
      return element;
    }
    if (element === element.ownerDocument.documentElement) {
      element = element.ownerDocument.body;
    }
    return update(element);
  };
  wysihtml5.dom.autoLink.URL_REG_EXP = rclass;
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  dom.addClass = function(element, name) {
    var classList = element.classList;
    if (classList) {
      return classList.add(name);
    }
    if (!dom.hasClass(element, name)) {
      element.className += " " + name;
    }
  };
  dom.removeClass = function(element, elem) {
    var css = element.classList;
    if (css) {
      return css.remove(elem);
    }
    element.className = element.className.replace(RegExp("(^|\\s+)" + elem + "(\\s+|$)"), " ");
  };
  dom.hasClass = function(element, item) {
    var value = element.classList;
    if (value) {
      return value.contains(item);
    }
    value = element.className;
    return 0 < value.length && (value == item || RegExp("(^|\\s)" + item + "(\\s|$)").test(value));
  };
})(wysihtml5);
wysihtml5.dom.contains = function() {
  var container = document.documentElement;
  if (container.contains) {
    return function(container, element) {
      if (element.nodeType !== wysihtml5.ELEMENT_NODE) {
        element = element.parentNode;
      }
      return container !== element && container.contains(element);
    };
  }
  if (container.compareDocumentPosition) {
    return function(container, element) {
      return!!(container.compareDocumentPosition(element) & 16);
    };
  }
}();
wysihtml5.dom.convertToList = function() {
  function _createListItem(doc, list) {
    var listItem = doc.createElement("li");
    list.appendChild(listItem);
    return listItem;
  }
  return function(element, el) {
    if ("UL" === element.nodeName || ("OL" === element.nodeName || "MENU" === element.nodeName)) {
      return element;
    }
    var doc = element.ownerDocument;
    var list = doc.createElement(el);
    var nodes = element.querySelectorAll("br");
    var len = nodes.length;
    var node;
    var p;
    var BR;
    var currentListItem;
    var i;
    i = 0;
    for (;i < len;i++) {
      node = nodes[i];
      for (;(p = node.parentNode) && (p !== element && p.lastChild === node);) {
        if ("block" === wysihtml5.dom.getStyle("display").from(p)) {
          p.removeChild(node);
          break;
        }
        wysihtml5.dom.insert(node).after(node.parentNode);
      }
    }
    nodes = wysihtml5.lang.array(element.childNodes).get();
    len = nodes.length;
    i = 0;
    for (;i < len;i++) {
      currentListItem = currentListItem || _createListItem(doc, list);
      node = nodes[i];
      p = "block" === wysihtml5.dom.getStyle("display").from(node);
      BR = "BR" === node.nodeName;
      if (p) {
        currentListItem = currentListItem.firstChild ? _createListItem(doc, list) : currentListItem;
        currentListItem.appendChild(node);
        currentListItem = null;
      } else {
        if (BR) {
          currentListItem = currentListItem.firstChild ? null : currentListItem;
        } else {
          currentListItem.appendChild(node);
        }
      }
    }
    if (0 === nodes.length) {
      _createListItem(doc, list);
    }
    element.parentNode.replaceChild(list, element);
    return list;
  };
}();
wysihtml5.dom.copyAttributes = function(q) {
  return{
    from : function(source) {
      return{
        to : function(target) {
          var i;
          var k = 0;
          var l = q.length;
          for (;k < l;k++) {
            i = q[k];
            if ("undefined" !== typeof source[i]) {
              if ("" !== source[i]) {
                target[i] = source[i];
              }
            }
          }
          return{
            andTo : arguments.callee
          };
        }
      };
    }
  };
};
(function(dom) {
  var BOX_SIZING_PROPERTIES = ["-webkit-box-sizing", "-moz-box-sizing", "-ms-box-sizing", "box-sizing"];
  dom.copyStyles = function(arr) {
    return{
      from : function(element) {
        var i;
        b: {
          i = 0;
          var j = BOX_SIZING_PROPERTIES.length;
          for (;i < j;i++) {
            if ("border-box" === dom.getStyle(BOX_SIZING_PROPERTIES[i]).from(element)) {
              i = BOX_SIZING_PROPERTIES[i];
              break b;
            }
          }
          i = void 0;
        }
        i = i ? parseInt(dom.getStyle("width").from(element), 10) < element.offsetWidth : false;
        if (i) {
          arr = wysihtml5.lang.array(arr).without(BOX_SIZING_PROPERTIES);
        }
        var cssText = "";
        i = arr.length;
        j = 0;
        var property;
        for (;j < i;j++) {
          property = arr[j];
          cssText += property + ":" + dom.getStyle(property).from(element) + ";";
        }
        return{
          to : function(target) {
            dom.setStyles(cssText).on(target);
            return{
              andTo : arguments.callee
            };
          }
        };
      }
    };
  };
})(wysihtml5.dom);
(function(wysihtml5) {
  wysihtml5.dom.delegate = function(element, selector, event, callback) {
    return wysihtml5.dom.observe(element, event, function(e) {
      var target = e.target;
      var previous = wysihtml5.lang.array(element.querySelectorAll(selector));
      for (;target && target !== element;) {
        if (previous.contains(target)) {
          callback.call(target, e);
          break;
        }
        target = target.parentNode;
      }
    });
  };
})(wysihtml5);
wysihtml5.dom.getAsDom = function() {
  var codeSegments = "abbr article aside audio bdi canvas command datalist details figcaption figure footer header hgroup keygen mark meter nav output progress rp rt ruby svg section source summary time track video wbr".split(" ");
  return function(c, doc) {
    doc = doc || document;
    var d;
    if ("object" === typeof c && c.nodeType) {
      d = doc.createElement("div");
      d.appendChild(c);
    } else {
      if (wysihtml5.browser.supportsHTML5Tags(doc)) {
        d = doc.createElement("div");
        d.innerHTML = c;
      } else {
        d = doc;
        if (!d._wysihtml5_supportsHTML5Tags) {
          var i = 0;
          var valuesLen = codeSegments.length;
          for (;i < valuesLen;i++) {
            d.createElement(codeSegments[i]);
          }
          d._wysihtml5_supportsHTML5Tags = true;
        }
        d = doc;
        i = d.createElement("div");
        i.style.display = "none";
        d.body.appendChild(i);
        try {
          i.innerHTML = c;
        } catch (h) {
        }
        d.body.removeChild(i);
        d = i;
      }
    }
    return d;
  };
}();
wysihtml5.dom.getParentElement = function() {
  function _isSameNodeName(keys, obj) {
    return!obj || !obj.length ? true : "string" === typeof obj ? keys === obj : wysihtml5.lang.array(obj).contains(keys);
  }
  return function(node, child, expr) {
    expr = expr || 50;
    if (child.className || child.classRegExp) {
      a: {
        var nodeName = child.nodeName;
        var old = child.className;
        child = child.classRegExp;
        for (;expr-- && (node && "BODY" !== node.nodeName);) {
          var value;
          if (value = node.nodeType === wysihtml5.ELEMENT_NODE) {
            if (value = _isSameNodeName(node.nodeName, nodeName)) {
              value = old;
              var worlds = (node.className || "").match(child) || [];
              value = !value ? !!worlds.length : worlds[worlds.length - 1] === value;
            }
          }
          if (value) {
            break a;
          }
          node = node.parentNode;
        }
        node = null;
      }
      return node;
    }
    a: {
      nodeName = child.nodeName;
      old = expr;
      for (;old-- && (node && "BODY" !== node.nodeName);) {
        if (_isSameNodeName(node.nodeName, nodeName)) {
          break a;
        }
        node = node.parentNode;
      }
      node = null;
    }
    return node;
  };
}();
wysihtml5.dom.getStyle = function() {
  var old = {
    "float" : "styleFloat" in document.createElement("div").style ? "styleFloat" : "cssFloat"
  };
  var r20 = /\-[a-z]/g;
  return function(name) {
    return{
      from : function(element) {
        if (element.nodeType === wysihtml5.ELEMENT_NODE) {
          var doc = element.ownerDocument;
          var camelizedProperty = old[name] || name.replace(r20, function(charsetPart) {
            return charsetPart.charAt(1).toUpperCase();
          });
          var style = element.style;
          var currentStyle = element.currentStyle;
          var styleValue = style[camelizedProperty];
          if (styleValue) {
            return styleValue;
          }
          if (currentStyle) {
            try {
              return currentStyle[camelizedProperty];
            } catch (j) {
            }
          }
          camelizedProperty = doc.defaultView || doc.parentWindow;
          doc = ("height" === name || "width" === name) && "TEXTAREA" === element.nodeName;
          var originalOverflow;
          if (camelizedProperty.getComputedStyle) {
            return doc && (originalOverflow = style.overflow, style.overflow = "hidden"), element = camelizedProperty.getComputedStyle(element, null).getPropertyValue(name), doc && (style.overflow = originalOverflow || ""), element;
          }
        }
      }
    };
  };
}();
wysihtml5.dom.hasElementWithTagName = function() {
  var $cookies = {};
  var DOCUMENT_IDENTIFIER = 1;
  return function(doc, tagName) {
    var key = (doc._wysihtml5_identifier || (doc._wysihtml5_identifier = DOCUMENT_IDENTIFIER++)) + ":" + tagName;
    var value = $cookies[key];
    if (!value) {
      value = $cookies[key] = doc.getElementsByTagName(tagName);
    }
    return 0 < value.length;
  };
}();
(function(wysihtml5) {
  var $cookies = {};
  var DOCUMENT_IDENTIFIER = 1;
  wysihtml5.dom.hasElementWithClassName = function(doc, className) {
    if (!wysihtml5.browser.supportsNativeGetElementsByClassName()) {
      return!!doc.querySelector("." + className);
    }
    var key = (doc._wysihtml5_identifier || (doc._wysihtml5_identifier = DOCUMENT_IDENTIFIER++)) + ":" + className;
    var value = $cookies[key];
    if (!value) {
      value = $cookies[key] = doc.getElementsByClassName(className);
    }
    return 0 < value.length;
  };
})(wysihtml5);
wysihtml5.dom.insert = function(node) {
  return{
    after : function(element) {
      element.parentNode.insertBefore(node, element.nextSibling);
    },
    before : function(element) {
      element.parentNode.insertBefore(node, element);
    },
    into : function(element) {
      element.appendChild(node);
    }
  };
};
wysihtml5.dom.insertCSS = function(cssText) {
  cssText = cssText.join("\n");
  return{
    into : function(doc) {
      var el = doc.createElement("style");
      el.type = "text/css";
      if (el.styleSheet) {
        el.styleSheet.cssText = cssText;
      } else {
        el.appendChild(doc.createTextNode(cssText));
      }
      var insertAt = doc.querySelector("head link");
      if (insertAt) {
        insertAt.parentNode.insertBefore(el, insertAt);
      } else {
        if (doc = doc.querySelector("head")) {
          doc.appendChild(el);
        }
      }
    }
  };
};
wysihtml5.dom.observe = function(object, type, callback) {
  type = "string" === typeof type ? [type] : type;
  var handlerWrapper;
  var name;
  var i = 0;
  var l = type.length;
  for (;i < l;i++) {
    name = type[i];
    if (object.addEventListener) {
      object.addEventListener(name, callback, false);
    } else {
      handlerWrapper = function(event) {
        if (!("target" in event)) {
          event.target = event.srcElement;
        }
        event.preventDefault = event.preventDefault || function() {
          this.returnValue = false;
        };
        event.stopPropagation = event.stopPropagation || function() {
          this.cancelBubble = true;
        };
        callback.call(object, event);
      };
      object.attachEvent("on" + name, handlerWrapper);
    }
  }
  return{
    stop : function() {
      var name;
      var i = 0;
      var l = type.length;
      for (;i < l;i++) {
        name = type[i];
        if (object.removeEventListener) {
          object.removeEventListener(name, callback, false);
        } else {
          object.detachEvent("on" + name, handlerWrapper);
        }
      }
    }
  };
};
wysihtml5.dom.parse = function() {
  function parseNode(node, obj) {
    var children = node.childNodes;
    var l = children.length;
    var clone = cleanableNodeTypes[node.nodeType];
    var i = 0;
    var name;
    clone = clone && clone(node);
    if (!clone) {
      return null;
    }
    i = 0;
    for (;i < l;i++) {
      if (name = parseNode(children[i], obj)) {
        clone.appendChild(name);
      }
    }
    return obj && (1 >= clone.childNodes.length && (clone.nodeName.toLowerCase() === DEFAULT_NODE_NAME && !clone.attributes.length)) ? clone.firstChild : clone;
  }
  function _getAttribute(el, name) {
    name = name.toLowerCase();
    var found;
    if (found = "IMG" == el.nodeName) {
      if (found = "src" == name) {
        var target;
        a: {
          try {
            target = el.complete && !el.mozMatchesSelector(":-moz-broken");
            break a;
          } catch (e) {
            if (el.complete && "complete" === el.readyState) {
              target = true;
              break a;
            }
          }
          target = void 0;
        }
        found = true === target;
      }
    }
    return found ? el.src : msie && "outerHTML" in el ? -1 != el.outerHTML.toLowerCase().indexOf(" " + name + "=") ? el.getAttribute(name) : null : el.getAttribute(name);
  }
  var cleanableNodeTypes = {
    1 : function(element) {
      var rule;
      var input;
      var attributes = obj.tags;
      input = element.nodeName.toLowerCase();
      rule = element.scopeName;
      if (element._wysihtml5) {
        return null;
      }
      element._wysihtml5 = 1;
      if ("wysihtml5-temp" === element.className) {
        return null;
      }
      if (rule) {
        if ("HTML" != rule) {
          input = rule + ":" + input;
        }
      }
      if ("outerHTML" in element) {
        if (!wysihtml5.browser.autoClosesUnclosedTags()) {
          if ("P" === element.nodeName && "</p>" !== element.outerHTML.slice(-4).toLowerCase()) {
            input = "div";
          }
        }
      }
      if (input in attributes) {
        rule = attributes[input];
        if (!rule || rule.remove) {
          return null;
        }
        rule = "string" === typeof rule ? {
          rename_tag : rule
        } : rule;
      } else {
        if (element.firstChild) {
          rule = {
            rename_tag : DEFAULT_NODE_NAME
          };
        } else {
          return null;
        }
      }
      input = element.ownerDocument.createElement(rule.rename_tag || input);
      attributes = {};
      var url = rule.set_class;
      var n = rule.add_class;
      var value = rule.set_attributes;
      var elements = rule.check_attributes;
      var index = obj.classes;
      var i = 0;
      var queue = [];
      rule = [];
      var list = [];
      var s = [];
      var key;
      if (value) {
        attributes = wysihtml5.lang.object(value).clone();
      }
      if (elements) {
        for (key in elements) {
          if (value = attributeCheckMethods[elements[key]]) {
            value = value(_getAttribute(element, key));
            if ("string" === typeof value) {
              attributes[key] = value;
            }
          }
        }
      }
      if (url) {
        queue.push(url);
      }
      if (n) {
        for (key in n) {
          if (value = addClassMethods[n[key]]) {
            url = value(_getAttribute(element, key));
            if ("string" === typeof url) {
              queue.push(url);
            }
          }
        }
      }
      index["_wysihtml5-temp-placeholder"] = 1;
      if (s = element.getAttribute("class")) {
        queue = queue.concat(s.split(slashSplit));
      }
      n = queue.length;
      for (;i < n;i++) {
        element = queue[i];
        if (index[element]) {
          rule.push(element);
        }
      }
      index = rule.length;
      for (;index--;) {
        element = rule[index];
        if (!wysihtml5.lang.array(list).contains(element)) {
          list.unshift(element);
        }
      }
      if (list.length) {
        attributes["class"] = list.join(" ");
      }
      for (key in attributes) {
        try {
          input.setAttribute(key, attributes[key]);
        } catch (l) {
        }
      }
      if (attributes.src) {
        if ("undefined" !== typeof attributes.width) {
          input.setAttribute("width", attributes.width);
        }
        if ("undefined" !== typeof attributes.height) {
          input.setAttribute("height", attributes.height);
        }
      }
      return input;
    },
    3 : function(elem) {
      return elem.ownerDocument.createTextNode(elem.data);
    }
  };
  var DEFAULT_NODE_NAME = "span";
  var slashSplit = /\s+/;
  var defaultRules = {
    tags : {},
    classes : {}
  };
  var obj = {};
  var msie = !wysihtml5.browser.supportsGetAttributeCorrectly();
  var rclass = /^https?:\/\//i;
  var pattern = /^(\/|https?:\/\/)/i;
  var expression = /^(\/|https?:\/\/|mailto:)/i;
  var r20 = /[^ a-z0-9_\-]/gi;
  var rreturn = /\D/g;
  var attributeCheckMethods = {
    url : function(value) {
      return!value || !value.match(rclass) ? null : value.replace(rclass, function(m3) {
        return m3.toLowerCase();
      });
    },
    src : function(source) {
      return!source || !source.match(pattern) ? null : source.replace(pattern, function(m3) {
        return m3.toLowerCase();
      });
    },
    href : function(params) {
      return!params || !params.match(expression) ? null : params.replace(expression, function(m3) {
        return m3.toLowerCase();
      });
    },
    alt : function(c) {
      return!c ? "" : c.replace(r20, "");
    },
    numbers : function(b) {
      return(b = (b || "").replace(rreturn, "")) || null;
    }
  };
  var check = {
    left : "wysiwyg-float-left",
    right : "wysiwyg-float-right"
  };
  var calc_x = {
    left : "wysiwyg-text-align-left",
    right : "wysiwyg-text-align-right",
    center : "wysiwyg-text-align-center",
    justify : "wysiwyg-text-align-justify"
  };
  var button = {
    left : "wysiwyg-clear-left",
    right : "wysiwyg-clear-right",
    both : "wysiwyg-clear-both",
    all : "wysiwyg-clear-both"
  };
  var mapping = {
    1 : "wysiwyg-font-size-xx-small",
    2 : "wysiwyg-font-size-small",
    3 : "wysiwyg-font-size-medium",
    4 : "wysiwyg-font-size-large",
    5 : "wysiwyg-font-size-x-large",
    6 : "wysiwyg-font-size-xx-large",
    7 : "wysiwyg-font-size-xx-large",
    "-" : "wysiwyg-font-size-smaller",
    "+" : "wysiwyg-font-size-larger"
  };
  var addClassMethods = {
    align_img : function(row) {
      return check[String(row).toLowerCase()];
    },
    align_text : function(row) {
      return calc_x[String(row).toLowerCase()];
    },
    clear_br : function(row) {
      return button[String(row).toLowerCase()];
    },
    size_font : function(attributeValue) {
      return mapping[String(attributeValue).charAt(0)];
    }
  };
  return function(el, node, child, attributes) {
    wysihtml5.lang.object(obj).merge(defaultRules).merge(node).get();
    child = child || (el.ownerDocument || document);
    node = child.createDocumentFragment();
    var i = "string" === typeof el;
    el = i ? wysihtml5.dom.getAsDom(el, child) : el;
    for (;el.firstChild;) {
      child = el.firstChild;
      el.removeChild(child);
      if (child = parseNode(child, attributes)) {
        node.appendChild(child);
      }
    }
    el.innerHTML = "";
    el.appendChild(node);
    return i ? wysihtml5.quirks.getCorrectInnerHTML(el) : el;
  };
}();
wysihtml5.dom.removeEmptyTextNodes = function(node) {
  var opt_nodes = wysihtml5.lang.array(node.childNodes).get();
  var len = opt_nodes.length;
  var i = 0;
  for (;i < len;i++) {
    node = opt_nodes[i];
    if (node.nodeType === wysihtml5.TEXT_NODE) {
      if ("" === node.data) {
        node.parentNode.removeChild(node);
      }
    }
  }
};
wysihtml5.dom.renameElement = function(element, name) {
  var value = element.ownerDocument.createElement(name);
  var child;
  for (;child = element.firstChild;) {
    value.appendChild(child);
  }
  wysihtml5.dom.copyAttributes(["align", "className"]).from(element).to(value);
  element.parentNode.replaceChild(value, element);
  return value;
};
wysihtml5.dom.replaceWithChildNodes = function(node) {
  if (node.parentNode) {
    if (node.firstChild) {
      var frag = node.ownerDocument.createDocumentFragment();
      for (;node.firstChild;) {
        frag.appendChild(node.firstChild);
      }
      node.parentNode.replaceChild(frag, node);
    } else {
      node.parentNode.removeChild(node);
    }
  }
};
(function(dom) {
  function _appendLineBreak(element) {
    var lineBreak = element.ownerDocument.createElement("br");
    element.appendChild(lineBreak);
  }
  dom.resolveList = function(list, useLineBreaks) {
    if (list.nodeName.match(/^(MENU|UL|OL)$/)) {
      var doc = list.ownerDocument;
      var fragment = doc.createDocumentFragment();
      var a = list.previousElementSibling || list.previousSibling;
      var c;
      var e;
      if (useLineBreaks) {
        if (a) {
          if ("block" !== dom.getStyle("display").from(a)) {
            _appendLineBreak(fragment);
          }
        }
        for (;e = list.firstElementChild || list.firstChild;) {
          doc = e.lastChild;
          for (;a = e.firstChild;) {
            c = (c = a === doc) && ("block" !== dom.getStyle("display").from(a) && "BR" !== a.nodeName);
            fragment.appendChild(a);
            if (c) {
              _appendLineBreak(fragment);
            }
          }
          e.parentNode.removeChild(e);
        }
      } else {
        for (;e = list.firstElementChild || list.firstChild;) {
          if (e.querySelector && e.querySelector("div, p, ul, ol, menu, blockquote, h1, h2, h3, h4, h5, h6")) {
            for (;a = e.firstChild;) {
              fragment.appendChild(a);
            }
          } else {
            c = doc.createElement("p");
            for (;a = e.firstChild;) {
              c.appendChild(a);
            }
            fragment.appendChild(c);
          }
          e.parentNode.removeChild(e);
        }
      }
      list.parentNode.replaceChild(fragment, list);
    }
  };
})(wysihtml5.dom);
(function(wysihtml5) {
  var doc = document;
  var configList = "parent top opener frameElement frames localStorage globalStorage sessionStorage indexedDB".split(" ");
  var styleSheets = "open close openDialog showModalDialog alert confirm prompt openDatabase postMessage XMLHttpRequest XDomainRequest".split(" ");
  var documentProperties = ["referrer", "write", "open", "close"];
  wysihtml5.dom.Sandbox = Base.extend({
    constructor : function(readyCallback, config) {
      this.callback = readyCallback || wysihtml5.EMPTY_FUNCTION;
      this.config = wysihtml5.lang.object({}).merge(config).get();
      this.iframe = this._createIframe();
    },
    insertInto : function(element) {
      if ("string" === typeof element) {
        element = doc.getElementById(element);
      }
      element.appendChild(this.iframe);
    },
    getIframe : function() {
      return this.iframe;
    },
    getWindow : function() {
      this._readyError();
    },
    getDocument : function() {
      this._readyError();
    },
    destroy : function() {
      var tabPage = this.getIframe();
      tabPage.parentNode.removeChild(tabPage);
    },
    _readyError : function() {
      throw Error("wysihtml5.Sandbox: Sandbox iframe isn't loaded yet");
    },
    _createIframe : function() {
      var that = this;
      var iframe = doc.createElement("iframe");
      iframe.className = "wysihtml5-sandbox";
      wysihtml5.dom.setAttributes({
        security : "restricted",
        allowtransparency : "true",
        frameborder : 0,
        width : 0,
        height : 0,
        marginwidth : 0,
        marginheight : 0
      }).on(iframe);
      if (wysihtml5.browser.throwsMixedContentWarningWhenIframeSrcIsEmpty()) {
        iframe.src = "javascript:'<html></html>'";
      }
      iframe.onload = function() {
        iframe.onreadystatechange = iframe.onload = null;
        that._onLoadIframe(iframe);
      };
      iframe.onreadystatechange = function() {
        if (/loaded|complete/.test(iframe.readyState)) {
          iframe.onreadystatechange = iframe.onload = null;
          that._onLoadIframe(iframe);
        }
      };
      return iframe;
    },
    _onLoadIframe : function(iframe) {
      if (wysihtml5.dom.contains(doc.documentElement, iframe)) {
        var dfd = this;
        var iframeWindow = iframe.contentWindow;
        var iframeDocument = iframe.contentWindow.document;
        var i = this._getHtml({
          charset : doc.characterSet || (doc.charset || "utf-8"),
          stylesheets : this.config.stylesheets
        });
        iframeDocument.open("text/html", "replace");
        iframeDocument.write(i);
        iframeDocument.close();
        this.getWindow = function() {
          return iframe.contentWindow;
        };
        this.getDocument = function() {
          return iframe.contentWindow.document;
        };
        iframeWindow.onerror = function(url, err, linerNr) {
          throw Error("wysihtml5.Sandbox: " + url, err, linerNr);
        };
        if (!wysihtml5.browser.supportsSandboxedIframes()) {
          var ln;
          i = 0;
          ln = configList.length;
          for (;i < ln;i++) {
            this._unset(iframeWindow, configList[i]);
          }
          i = 0;
          ln = styleSheets.length;
          for (;i < ln;i++) {
            this._unset(iframeWindow, styleSheets[i], wysihtml5.EMPTY_FUNCTION);
          }
          i = 0;
          ln = documentProperties.length;
          for (;i < ln;i++) {
            this._unset(iframeDocument, documentProperties[i]);
          }
          this._unset(iframeDocument, "cookie", "", true);
        }
        this.loaded = true;
        setTimeout(function() {
          dfd.callback(dfd);
        }, 0);
      }
    },
    _getHtml : function(templateVars) {
      var string = templateVars.stylesheets;
      var html = "";
      var _i = 0;
      var _len;
      if (string = "string" === typeof string ? [string] : string) {
        _len = string.length;
        for (;_i < _len;_i++) {
          html += '<link rel="stylesheet" href="' + string[_i] + '">';
        }
      }
      templateVars.stylesheets = html;
      return wysihtml5.lang.string('<!DOCTYPE html><html><head><meta charset="#{charset}">#{stylesheets}</head><body></body></html>').interpolate(templateVars);
    },
    _unset : function(object, property, value, dataAndEvents) {
      try {
        object[property] = value;
      } catch (j) {
      }
      try {
        object.__defineGetter__(property, function() {
          return value;
        });
      } catch (k) {
      }
      if (dataAndEvents) {
        try {
          object.__defineSetter__(property, function() {
          });
        } catch (m) {
        }
      }
      if (!wysihtml5.browser.crashesWhenDefineProperty(property)) {
        try {
          var desc = {
            get : function() {
              return value;
            }
          };
          if (dataAndEvents) {
            desc.set = function() {
            };
          }
          Object.defineProperty(object, property, desc);
        } catch (v) {
        }
      }
    }
  });
})(wysihtml5);
(function() {
  var mapping = {
    className : "class"
  };
  wysihtml5.dom.setAttributes = function(attributes) {
    return{
      on : function(element) {
        var i;
        for (i in attributes) {
          element.setAttribute(mapping[i] || i, attributes[i]);
        }
      }
    };
  };
})();
wysihtml5.dom.setStyles = function(styles) {
  return{
    on : function(element) {
      element = element.style;
      if ("string" === typeof styles) {
        element.cssText += ";" + styles;
      } else {
        var name;
        for (name in styles) {
          if ("float" === name) {
            element.cssFloat = styles[name];
            element.styleFloat = styles[name];
          } else {
            element[name] = styles[name];
          }
        }
      }
    }
  };
};
(function(dom) {
  dom.simulatePlaceholder = function(editor, view, value) {
    var unset = function() {
      if (view.hasPlaceholderSet()) {
        view.clear();
      }
      view.placeholderSet = false;
      dom.removeClass(view.element, "placeholder");
    };
    var set = function() {
      if (view.isEmpty()) {
        view.placeholderSet = true;
        view.setValue(value);
        dom.addClass(view.element, "placeholder");
      }
    };
    editor.on("set_placeholder", set).on("unset_placeholder", unset).on("focus:composer", unset).on("paste:composer", unset).on("blur:composer", set);
    set();
  };
})(wysihtml5.dom);
(function(dom) {
  var documentElement = document.documentElement;
  if ("textContent" in documentElement) {
    dom.setTextContent = function(element, text) {
      element.textContent = text;
    };
    dom.getTextContent = function(element) {
      return element.textContent;
    };
  } else {
    if ("innerText" in documentElement) {
      dom.setTextContent = function(element, value) {
        element.innerText = value;
      };
      dom.getTextContent = function(element) {
        return element.innerText;
      };
    } else {
      dom.setTextContent = function(element, value) {
        element.nodeValue = value;
      };
      dom.getTextContent = function(element) {
        return element.nodeValue;
      };
    }
  }
})(wysihtml5.dom);
wysihtml5.quirks.cleanPastedHTML = function() {
  var fx = {
    "a u" : wysihtml5.dom.replaceWithChildNodes
  };
  return function(content, type, func) {
    type = type || fx;
    func = func || (content.ownerDocument || document);
    var strip = "string" === typeof content;
    var parts;
    var subLn;
    var id;
    var j = 0;
    content = strip ? wysihtml5.dom.getAsDom(content, func) : content;
    for (id in type) {
      parts = content.querySelectorAll(id);
      func = type[id];
      subLn = parts.length;
      for (;j < subLn;j++) {
        func(parts[j]);
      }
    }
    return strip ? content.innerHTML : content;
  };
}();
wysihtml5.quirks.ensureProperClearing = function() {
  var process = function() {
    var elem = this;
    setTimeout(function() {
      var zeroQuoted = elem.innerHTML.toLowerCase();
      if ("<p>&nbsp;</p>" == zeroQuoted || "<p>&nbsp;</p><p>&nbsp;</p>" == zeroQuoted) {
        elem.innerHTML = "";
      }
    }, 0);
  };
  return function(composer) {
    wysihtml5.dom.observe(composer.element, ["cut", "keydown"], process);
  };
}();
(function(wysihtml5) {
  wysihtml5.quirks.getCorrectInnerHTML = function(element) {
    var text = element.innerHTML;
    if (-1 === text.indexOf("%7E")) {
      return text;
    }
    element = element.querySelectorAll("[href*='~'], [src*='~']");
    var url;
    var r20;
    var kl;
    var k;
    k = 0;
    kl = element.length;
    for (;k < kl;k++) {
      url = element[k].href || element[k].src;
      r20 = wysihtml5.lang.string(url).replace("~").by("%7E");
      text = wysihtml5.lang.string(text).replace(r20).by(url);
    }
    return text;
  };
})(wysihtml5);
(function(wysihtml5) {
  wysihtml5.quirks.redraw = function(element) {
    wysihtml5.dom.addClass(element, "wysihtml5-quirks-redraw");
    wysihtml5.dom.removeClass(element, "wysihtml5-quirks-redraw");
    try {
      var doc = element.ownerDocument;
      doc.execCommand("italic", false, null);
      doc.execCommand("italic", false, null);
    } catch (d) {
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  wysihtml5.Selection = Base.extend({
    constructor : function(editor) {
      window.rangy.init();
      this.editor = editor;
      this.composer = editor.composer;
      this.doc = this.composer.doc;
    },
    getBookmark : function() {
      var rng = this.getRange();
      return rng && rng.cloneRange();
    },
    setBookmark : function(pos) {
      if (pos) {
        this.setSelection(pos);
      }
    },
    setBefore : function(node) {
      var range = rangy.createRange(this.doc);
      range.setStartBefore(node);
      range.setEndBefore(node);
      return this.setSelection(range);
    },
    setAfter : function(node) {
      var range = rangy.createRange(this.doc);
      range.setStartAfter(node);
      range.setEndAfter(node);
      return this.setSelection(range);
    },
    selectNode : function(node, dataAndEvents) {
      var range = rangy.createRange(this.doc);
      var isElement = node.nodeType === wysihtml5.ELEMENT_NODE;
      var canHaveHTML = "canHaveHTML" in node ? node.canHaveHTML : "IMG" !== node.nodeName;
      var extname = isElement ? node.innerHTML : node.data;
      extname = "" === extname || extname === wysihtml5.INVISIBLE_SPACE;
      var recurring = dom.getStyle("display").from(node);
      recurring = "block" === recurring || "list-item" === recurring;
      if (extname && (isElement && (canHaveHTML && !dataAndEvents))) {
        try {
          node.innerHTML = wysihtml5.INVISIBLE_SPACE;
        } catch (j) {
        }
      }
      if (canHaveHTML) {
        range.selectNodeContents(node);
      } else {
        range.selectNode(node);
      }
      if (canHaveHTML && (extname && isElement)) {
        range.collapse(recurring);
      } else {
        if (canHaveHTML) {
          if (extname) {
            range.setStartAfter(node);
            range.setEndAfter(node);
          }
        }
      }
      this.setSelection(range);
    },
    getSelectedNode : function(selection) {
      if (selection && (this.doc.selection && ("Control" === this.doc.selection.type && ((selection = this.doc.selection.createRange()) && selection.length)))) {
        return selection.item(0);
      }
      selection = this.getSelection(this.doc);
      return selection.focusNode === selection.anchorNode ? selection.focusNode : (selection = this.getRange(this.doc)) ? selection.commonAncestorContainer : this.doc.body;
    },
    executeAndRestore : function(method, restoreScrollPosition) {
      var body = this.doc.body;
      var oldScrollTop = restoreScrollPosition && body.scrollTop;
      var oldScrollLeft = restoreScrollPosition && body.scrollLeft;
      var html = '<span class="_wysihtml5-temp-placeholder">' + wysihtml5.INVISIBLE_SPACE + "</span>";
      var self = this.getRange(this.doc);
      var node;
      if (self) {
        if (wysihtml5.browser.hasInsertNodeIssue()) {
          this.doc.execCommand("insertHTML", false, html);
        } else {
          html = self.createContextualFragment(html);
          self.insertNode(html);
        }
        try {
          method(self.startContainer, self.endContainer);
        } catch (k) {
          setTimeout(function() {
            throw k;
          }, 0);
        }
        if (self = this.doc.querySelector("._wysihtml5-temp-placeholder")) {
          html = rangy.createRange(this.doc);
          node = self.nextSibling;
          if (wysihtml5.browser.hasInsertNodeIssue() && (node && "BR" === node.nodeName)) {
            node = this.doc.createTextNode(wysihtml5.INVISIBLE_SPACE);
            dom.insert(node).after(self);
            html.setStartBefore(node);
            html.setEndBefore(node);
          } else {
            html.selectNode(self);
            html.deleteContents();
          }
          this.setSelection(html);
        } else {
          body.focus();
        }
        if (restoreScrollPosition) {
          body.scrollTop = oldScrollTop;
          body.scrollLeft = oldScrollLeft;
        }
        try {
          self.parentNode.removeChild(self);
        } catch (m) {
        }
      } else {
        method(body, body);
      }
    },
    executeAndRestoreSimple : function(method) {
      var recurring;
      var prefix_i;
      var range = this.getRange();
      var node = this.doc.body;
      var lastNode;
      if (range) {
        recurring = range.getNodes([3]);
        node = recurring[0] || range.startContainer;
        lastNode = recurring[recurring.length - 1] || range.endContainer;
        recurring = node === range.startContainer ? range.startOffset : 0;
        prefix_i = lastNode === range.endContainer ? range.endOffset : lastNode.length;
        try {
          method(range.startContainer, range.endContainer);
        } catch (g) {
          setTimeout(function() {
            throw g;
          }, 0);
        }
        method = rangy.createRange(this.doc);
        try {
          method.setStart(node, recurring);
        } catch (j) {
        }
        try {
          method.setEnd(lastNode, prefix_i);
        } catch (k) {
        }
        try {
          this.setSelection(method);
        } catch (m) {
        }
      } else {
        method(node, node);
      }
    },
    set : function(endNode, mL) {
      var range = rangy.createRange(this.doc);
      range.setStart(endNode, mL || 0);
      this.setSelection(range);
    },
    insertHTML : function(html) {
      html = rangy.createRange(this.doc).createContextualFragment(html);
      var lastChild = html.lastChild;
      this.insertNode(html);
      if (lastChild) {
        this.setAfter(lastChild);
      }
    },
    insertNode : function(node) {
      var range = this.getRange();
      if (range) {
        range.insertNode(node);
      }
    },
    surround : function(node) {
      var range = this.getRange();
      if (range) {
        try {
          range.surroundContents(node);
          this.selectNode(node);
        } catch (c) {
          node.appendChild(range.extractContents());
          range.insertNode(node);
        }
      }
    },
    scrollIntoView : function() {
      var doc = this.doc;
      var tmp = doc.documentElement.scrollHeight > doc.documentElement.offsetHeight;
      var node;
      if (!(node = doc._wysihtml5ScrollIntoViewElement)) {
        node = doc.createElement("span");
        node.innerHTML = wysihtml5.INVISIBLE_SPACE;
      }
      node = doc._wysihtml5ScrollIntoViewElement = node;
      if (tmp) {
        this.insertNode(node);
        tmp = node;
        var cur = 0;
        if (tmp.parentNode) {
          do {
            cur += tmp.offsetTop || 0;
            tmp = tmp.offsetParent;
          } while (tmp);
        }
        tmp = cur;
        node.parentNode.removeChild(node);
        if (tmp >= doc.body.scrollTop + doc.documentElement.offsetHeight - 5) {
          doc.body.scrollTop = tmp;
        }
      }
    },
    selectLine : function() {
      if (wysihtml5.browser.supportsSelectionModify()) {
        this._selectLine_W3C();
      } else {
        if (this.doc.selection) {
          this._selectLine_MSIE();
        }
      }
    },
    _selectLine_W3C : function() {
      var selection = this.doc.defaultView.getSelection();
      selection.modify("extend", "left", "lineboundary");
      selection.modify("extend", "right", "lineboundary");
    },
    _selectLine_MSIE : function() {
      var range = this.doc.selection.createRange();
      var rangeTop = range.boundingTop;
      var scrollWidth = this.doc.body.scrollWidth;
      var node;
      if (range.moveToPoint) {
        if (0 === rangeTop) {
          node = this.doc.createElement("span");
          this.insertNode(node);
          rangeTop = node.offsetTop;
          node.parentNode.removeChild(node);
        }
        rangeTop += 1;
        node = -10;
        for (;node < scrollWidth;node += 2) {
          try {
            range.moveToPoint(node, rangeTop);
            break;
          } catch (h) {
          }
        }
        node = this.doc.selection.createRange();
        for (;0 <= scrollWidth;scrollWidth--) {
          try {
            node.moveToPoint(scrollWidth, rangeTop);
            break;
          } catch (i) {
          }
        }
        range.setEndPoint("EndToEnd", node);
        range.select();
      }
    },
    getText : function() {
      var selection = this.getSelection();
      return selection ? selection.toString() : "";
    },
    getNodes : function(nodeType, filter) {
      var range = this.getRange();
      return range ? range.getNodes([nodeType], filter) : [];
    },
    getRange : function() {
      var sel = this.getSelection();
      return sel && (sel.rangeCount && sel.getRangeAt(0));
    },
    getSelection : function() {
      return rangy.getSelection(this.doc.defaultView || this.doc.parentWindow);
    },
    setSelection : function(key) {
      return rangy.getSelection(this.doc.defaultView || this.doc.parentWindow).setSingleRange(key);
    }
  });
})(wysihtml5);
(function(wysihtml5, editor) {
  function isSplitPoint(node, offset) {
    return editor.dom.isCharacterDataNode(node) ? 0 == offset ? !!node.previousSibling : offset == node.length ? !!node.nextSibling : true : 0 < offset && offset < node.childNodes.length;
  }
  function splitNodeAt(node, descendantNode, descendantOffset) {
    var newNode;
    if (editor.dom.isCharacterDataNode(descendantNode)) {
      if (0 == descendantOffset) {
        descendantOffset = editor.dom.getNodeIndex(descendantNode);
        descendantNode = descendantNode.parentNode;
      } else {
        if (descendantOffset == descendantNode.length) {
          descendantOffset = editor.dom.getNodeIndex(descendantNode) + 1;
          descendantNode = descendantNode.parentNode;
        } else {
          newNode = editor.dom.splitDataNode(descendantNode, descendantOffset);
        }
      }
    }
    if (!newNode) {
      newNode = descendantNode.cloneNode(false);
      if (newNode.id) {
        newNode.removeAttribute("id");
      }
      var child;
      for (;child = descendantNode.childNodes[descendantOffset];) {
        newNode.appendChild(child);
      }
      editor.dom.insertAfter(newNode, descendantNode);
    }
    return descendantNode == node ? newNode : splitNodeAt(node, newNode.parentNode, editor.dom.getNodeIndex(newNode));
  }
  function Merge(firstNode) {
    this.firstTextNode = (this.isElementMerge = firstNode.nodeType == wysihtml5.ELEMENT_NODE) ? firstNode.lastChild : firstNode;
    this.textNodes = [this.firstTextNode];
  }
  function HTMLApplier(tagNames, cssClass, similarClassRegExp, normalize) {
    this.tagNames = tagNames || [defaultTagName];
    this.cssClass = cssClass || "";
    this.similarClassRegExp = similarClassRegExp;
    this.normalize = normalize;
    this.applyToAnyTagName = false;
  }
  var defaultTagName = "span";
  var r20 = /\s+/g;
  Merge.prototype = {
    doMerge : function() {
      var data = [];
      var n;
      var node;
      var i = 0;
      var len = this.textNodes.length;
      for (;i < len;++i) {
        n = this.textNodes[i];
        node = n.parentNode;
        data[i] = n.data;
        if (i) {
          node.removeChild(n);
          if (!node.hasChildNodes()) {
            node.parentNode.removeChild(node);
          }
        }
      }
      return this.firstTextNode.data = data = data.join("");
    },
    getLength : function() {
      var i = this.textNodes.length;
      var len = 0;
      for (;i--;) {
        len += this.textNodes[i].length;
      }
      return len;
    },
    toString : function() {
      var qs = [];
      var i = 0;
      var len = this.textNodes.length;
      for (;i < len;++i) {
        qs[i] = "'" + this.textNodes[i].data + "'";
      }
      return "[Merge(" + qs.join(",") + ")]";
    }
  };
  HTMLApplier.prototype = {
    getAncestorWithClass : function(node) {
      var cssClassMatch;
      for (;node;) {
        if (this.cssClass) {
          if (cssClassMatch = this.cssClass, node.className) {
            var codeSegments = node.className.match(this.similarClassRegExp) || [];
            cssClassMatch = codeSegments[codeSegments.length - 1] === cssClassMatch;
          } else {
            cssClassMatch = false;
          }
        } else {
          cssClassMatch = true;
        }
        if (node.nodeType == wysihtml5.ELEMENT_NODE && (editor.dom.arrayContains(this.tagNames, node.tagName.toLowerCase()) && cssClassMatch)) {
          return node;
        }
        node = node.parentNode;
      }
      return false;
    },
    postApply : function(textNodes, range) {
      var firstNode = textNodes[0];
      var lastNode = textNodes[textNodes.length - 1];
      var merges = [];
      var currentMerge;
      var testTextNode = firstNode;
      var endNode = lastNode;
      var recurring = 0;
      var rangeEndOffset = lastNode.length;
      var textNode;
      var precedingTextNode;
      var i = 0;
      var len = textNodes.length;
      for (;i < len;++i) {
        textNode = textNodes[i];
        if (precedingTextNode = this.getAdjacentMergeableTextNode(textNode.parentNode, false)) {
          if (!currentMerge) {
            currentMerge = new Merge(precedingTextNode);
            merges.push(currentMerge);
          }
          currentMerge.textNodes.push(textNode);
          if (textNode === firstNode) {
            testTextNode = currentMerge.firstTextNode;
            recurring = testTextNode.length;
          }
          if (textNode === lastNode) {
            endNode = currentMerge.firstTextNode;
            rangeEndOffset = currentMerge.getLength();
          }
        } else {
          currentMerge = null;
        }
      }
      if (firstNode = this.getAdjacentMergeableTextNode(lastNode.parentNode, true)) {
        if (!currentMerge) {
          currentMerge = new Merge(lastNode);
          merges.push(currentMerge);
        }
        currentMerge.textNodes.push(firstNode);
      }
      if (merges.length) {
        i = 0;
        len = merges.length;
        for (;i < len;++i) {
          merges[i].doMerge();
        }
        range.setStart(testTextNode, recurring);
        range.setEnd(endNode, rangeEndOffset);
      }
    },
    getAdjacentMergeableTextNode : function(node, forward) {
      var adjacentNode = node.nodeType == wysihtml5.TEXT_NODE;
      var el = adjacentNode ? node.parentNode : node;
      var propName = forward ? "nextSibling" : "previousSibling";
      if (adjacentNode) {
        if ((adjacentNode = node[propName]) && adjacentNode.nodeType == wysihtml5.TEXT_NODE) {
          return adjacentNode;
        }
      } else {
        if ((adjacentNode = el[propName]) && this.areElementsMergeable(node, adjacentNode)) {
          return adjacentNode[forward ? "firstChild" : "lastChild"];
        }
      }
      return null;
    },
    areElementsMergeable : function(node, el2) {
      var i;
      if (i = editor.dom.arrayContains(this.tagNames, (node.tagName || "").toLowerCase())) {
        if (i = editor.dom.arrayContains(this.tagNames, (el2.tagName || "").toLowerCase())) {
          if (i = node.className.replace(r20, " ") == el2.className.replace(r20, " ")) {
            a: {
              if (node.attributes.length != el2.attributes.length) {
                i = false;
              } else {
                i = 0;
                var valuesLen = node.attributes.length;
                var a;
                var b;
                for (;i < valuesLen;++i) {
                  if (a = node.attributes[i], b = a.name, "class" != b && (b = el2.attributes.getNamedItem(b), a.specified != b.specified || a.specified && a.nodeValue !== b.nodeValue)) {
                    i = false;
                    break a;
                  }
                }
                i = true;
              }
            }
          }
        }
      }
      return i;
    },
    createContainer : function(doc) {
      doc = doc.createElement(this.tagNames[0]);
      if (this.cssClass) {
        doc.className = this.cssClass;
      }
      return doc;
    },
    applyToTextNode : function(textNode) {
      var parent = textNode.parentNode;
      if (1 == parent.childNodes.length && editor.dom.arrayContains(this.tagNames, parent.tagName.toLowerCase())) {
        if (this.cssClass) {
          textNode = this.cssClass;
          if (parent.className) {
            if (parent.className) {
              parent.className = parent.className.replace(this.similarClassRegExp, "");
            }
            parent.className += " " + textNode;
          } else {
            parent.className = textNode;
          }
        }
      } else {
        parent = this.createContainer(editor.dom.getDocument(textNode));
        textNode.parentNode.insertBefore(parent, textNode);
        parent.appendChild(textNode);
      }
    },
    isRemovable : function(el) {
      return editor.dom.arrayContains(this.tagNames, el.tagName.toLowerCase()) && wysihtml5.lang.string(el.className).trim() == this.cssClass;
    },
    undoToTextNode : function(newRange, range, node) {
      if (!range.containsNode(node)) {
        newRange = range.cloneRange();
        newRange.selectNode(node);
        if (newRange.isPointInRange(range.endContainer, range.endOffset)) {
          if (isSplitPoint(range.endContainer, range.endOffset)) {
            splitNodeAt(node, range.endContainer, range.endOffset);
            range.setEndAfter(node);
          }
        }
        if (newRange.isPointInRange(range.startContainer, range.startOffset)) {
          if (isSplitPoint(range.startContainer, range.startOffset)) {
            node = splitNodeAt(node, range.startContainer, range.startOffset);
          }
        }
      }
      if (this.similarClassRegExp) {
        if (node.className) {
          node.className = node.className.replace(this.similarClassRegExp, "");
        }
      }
      if (this.isRemovable(node)) {
        range = node;
        node = range.parentNode;
        for (;range.firstChild;) {
          node.insertBefore(range.firstChild, range);
        }
        node.removeChild(range);
      }
    },
    applyToRange : function(range) {
      var textNodes = range.getNodes([wysihtml5.TEXT_NODE]);
      if (!textNodes.length) {
        try {
          var textNode = this.createContainer(range.endContainer.ownerDocument);
          range.surroundContents(textNode);
          this.selectNode(range, textNode);
          return;
        } catch (e) {
        }
      }
      range.splitBoundaries();
      textNodes = range.getNodes([wysihtml5.TEXT_NODE]);
      if (textNodes.length) {
        var i = 0;
        var len = textNodes.length;
        for (;i < len;++i) {
          textNode = textNodes[i];
          if (!this.getAncestorWithClass(textNode)) {
            this.applyToTextNode(textNode);
          }
        }
        range.setStart(textNodes[0], 0);
        textNode = textNodes[textNodes.length - 1];
        range.setEnd(textNode, textNode.length);
        if (this.normalize) {
          this.postApply(textNodes, range);
        }
      }
    },
    undoToRange : function(range) {
      var node = range.getNodes([wysihtml5.TEXT_NODE]);
      var textNode;
      var ancestorWithClass;
      if (node.length) {
        range.splitBoundaries();
        node = range.getNodes([wysihtml5.TEXT_NODE]);
      } else {
        node = range.endContainer.ownerDocument.createTextNode(wysihtml5.INVISIBLE_SPACE);
        range.insertNode(node);
        range.selectNode(node);
        node = [node];
      }
      var i = 0;
      var len = node.length;
      for (;i < len;++i) {
        textNode = node[i];
        if (ancestorWithClass = this.getAncestorWithClass(textNode)) {
          this.undoToTextNode(textNode, range, ancestorWithClass);
        }
      }
      if (1 == len) {
        this.selectNode(range, node[0]);
      } else {
        range.setStart(node[0], 0);
        textNode = node[node.length - 1];
        range.setEnd(textNode, textNode.length);
        if (this.normalize) {
          this.postApply(node, range);
        }
      }
    },
    selectNode : function(dataAndEvents, node) {
      var isElement = node.nodeType === wysihtml5.ELEMENT_NODE;
      var bindingContextMayDifferFromDomParentElement = "canHaveHTML" in node ? node.canHaveHTML : true;
      var extname = isElement ? node.innerHTML : node.data;
      if ((extname = "" === extname || extname === wysihtml5.INVISIBLE_SPACE) && (isElement && bindingContextMayDifferFromDomParentElement)) {
        try {
          node.innerHTML = wysihtml5.INVISIBLE_SPACE;
        } catch (h) {
        }
      }
      dataAndEvents.selectNodeContents(node);
      if (extname && isElement) {
        dataAndEvents.collapse(false);
      } else {
        if (extname) {
          dataAndEvents.setStartAfter(node);
          dataAndEvents.setEndAfter(node);
        }
      }
    },
    getTextSelectedByRange : function(textNode, range) {
      var textRange = range.cloneRange();
      textRange.selectNodeContents(textNode);
      var text = textRange.intersection(range);
      text = text ? text.toString() : "";
      textRange.detach();
      return text;
    },
    isAppliedToRange : function(range) {
      var assigns = [];
      var vvar;
      var textNodes = range.getNodes([wysihtml5.TEXT_NODE]);
      if (!textNodes.length) {
        return(vvar = this.getAncestorWithClass(range.startContainer)) ? [vvar] : false;
      }
      var i = 0;
      var len = textNodes.length;
      var selectedText;
      for (;i < len;++i) {
        selectedText = this.getTextSelectedByRange(textNodes[i], range);
        vvar = this.getAncestorWithClass(textNodes[i]);
        if ("" != selectedText && !vvar) {
          return false;
        }
        assigns.push(vvar);
      }
      return assigns;
    },
    toggleRange : function(range) {
      if (this.isAppliedToRange(range)) {
        this.undoToRange(range);
      } else {
        this.applyToRange(range);
      }
    }
  };
  wysihtml5.selection.HTMLApplier = HTMLApplier;
})(wysihtml5, rangy);
wysihtml5.Commands = Base.extend({
  constructor : function(editor) {
    this.editor = editor;
    this.composer = editor.composer;
    this.doc = this.composer.doc;
  },
  support : function(command) {
    return wysihtml5.browser.supportsCommand(this.doc, command);
  },
  exec : function(composer, command) {
    var obj = wysihtml5.commands[composer];
    var args = wysihtml5.lang.array(arguments).get();
    var fun = obj && obj.exec;
    var result = null;
    this.editor.fire("beforecommand:composer");
    if (fun) {
      args.unshift(this.composer);
      result = fun.apply(obj, args);
    } else {
      try {
        result = this.doc.execCommand(composer, false, command);
      } catch (h) {
      }
    }
    this.editor.fire("aftercommand:composer");
    return result;
  },
  state : function(composer, command) {
    var obj = wysihtml5.commands[composer];
    var args = wysihtml5.lang.array(arguments).get();
    var fun = obj && obj.state;
    if (fun) {
      return args.unshift(this.composer), fun.apply(obj, args);
    }
    try {
      return this.doc.queryCommandState(composer);
    } catch (f) {
      return false;
    }
  }
});
wysihtml5.commands.bold = {
  exec : function(composer, command) {
    return wysihtml5.commands.formatInline.exec(composer, command, "b");
  },
  state : function(composer, command) {
    return wysihtml5.commands.formatInline.state(composer, command, "b");
  }
};
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  wysihtml5.commands.createLink = {
    exec : function(composer, command, target) {
      var anchors = this.state(composer, command);
      if (anchors) {
        composer.selection.executeAndRestore(function() {
          var len = anchors.length;
          var i = 0;
          var anchor;
          var part;
          var textContent;
          for (;i < len;i++) {
            anchor = anchors[i];
            part = dom.getParentElement(anchor, {
              nodeName : "code"
            });
            textContent = dom.getTextContent(anchor);
            if (textContent.match(dom.autoLink.URL_REG_EXP) && !part) {
              dom.renameElement(anchor, "code");
            } else {
              dom.replaceWithChildNodes(anchor);
            }
          }
        });
      } else {
        command = target = "object" === typeof target ? target : {
          href : target
        };
        target = composer.doc;
        var e = "_wysihtml5-temp-" + +new Date;
        var i = 0;
        var codeSegments;
        var element;
        var key;
        wysihtml5.commands.formatInline.exec(composer, void 0, "A", e, /non-matching-class/g);
        codeSegments = target.querySelectorAll("A." + e);
        e = codeSegments.length;
        for (;i < e;i++) {
          for (key in element = codeSegments[i], element.removeAttribute("class"), command) {
            element.setAttribute(key, command[key]);
          }
        }
        key = element;
        if (1 === e) {
          e = dom.getTextContent(element);
          i = !!element.querySelector("*");
          e = "" === e || e === wysihtml5.INVISIBLE_SPACE;
          if (!i) {
            if (e) {
              dom.setTextContent(element, command.text || element.href);
              command = target.createTextNode(" ");
              composer.selection.setAfter(element);
              dom.insert(command).after(element);
              key = command;
            }
          }
        }
        composer.selection.setAfter(key);
      }
    },
    state : function(composer, command) {
      return wysihtml5.commands.formatInline.state(composer, command, "A");
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-font-size-[0-9a-z\-]+/g;
  wysihtml5.commands.fontSize = {
    exec : function(composer, command, size) {
      return wysihtml5.commands.formatInline.exec(composer, command, "span", "wysiwyg-font-size-" + size, REG_EXP);
    },
    state : function(composer, command, size) {
      return wysihtml5.commands.formatInline.state(composer, command, "span", "wysiwyg-font-size-" + size, REG_EXP);
    },
    value : function() {
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-color-[0-9a-z]+/g;
  wysihtml5.commands.foreColor = {
    exec : function(composer, command, color) {
      return wysihtml5.commands.formatInline.exec(composer, command, "span", "wysiwyg-color-" + color, REG_EXP);
    },
    state : function(composer, command, color) {
      return wysihtml5.commands.formatInline.state(composer, command, "span", "wysiwyg-color-" + color, REG_EXP);
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  function $(node) {
    node = node.previousSibling;
    for (;node && (node.nodeType === wysihtml5.TEXT_NODE && !wysihtml5.lang.string(node.data).trim());) {
      node = node.previousSibling;
    }
    return node;
  }
  function parse(node) {
    node = node.nextSibling;
    for (;node && (node.nodeType === wysihtml5.TEXT_NODE && !wysihtml5.lang.string(node.data).trim());) {
      node = node.nextSibling;
    }
    return node;
  }
  function convertToList(element) {
    return "BR" === element.nodeName || "block" === dom.getStyle("display").from(element) ? true : false;
  }
  var dom = wysihtml5.dom;
  var BLOCK_ELEMENTS_GROUP = "H1 H2 H3 H4 H5 H6 P BLOCKQUOTE DIV".split(" ");
  wysihtml5.commands.formatBlock = {
    exec : function(composer, command, nodeName, className, classRegExp) {
      var doc = composer.doc;
      var blockElement = this.state(composer, command, nodeName, className, classRegExp);
      var useLineBreaks = composer.config.useLineBreaks;
      var defaultNodeName = useLineBreaks ? "DIV" : "P";
      var selectedNode;
      nodeName = "string" === typeof nodeName ? nodeName.toUpperCase() : nodeName;
      if (blockElement) {
        composer.selection.executeAndRestoreSimple(function() {
          if (classRegExp) {
            blockElement.className = blockElement.className.replace(classRegExp, "");
          }
          if (!wysihtml5.lang.string(blockElement.className).trim() && (useLineBreaks || "P" === nodeName)) {
            var s = blockElement;
            var d = s.ownerDocument;
            var c = parse(s);
            var start = $(s);
            if (c) {
              if (!convertToList(c)) {
                s.parentNode.insertBefore(d.createElement("br"), c);
              }
            }
            if (start) {
              if (!convertToList(start)) {
                s.parentNode.insertBefore(d.createElement("br"), s);
              }
            }
            dom.replaceWithChildNodes(blockElement);
          } else {
            dom.renameElement(blockElement, "P" === nodeName ? "DIV" : defaultNodeName);
          }
        });
      } else {
        if (null === nodeName || wysihtml5.lang.array(BLOCK_ELEMENTS_GROUP).contains(nodeName)) {
          if (selectedNode = composer.selection.getSelectedNode(), blockElement = dom.getParentElement(selectedNode, {
            nodeName : BLOCK_ELEMENTS_GROUP
          })) {
            composer.selection.executeAndRestore(function() {
              if (nodeName) {
                blockElement = dom.renameElement(blockElement, nodeName);
              }
              if (className) {
                var element = blockElement;
                if (element.className) {
                  element.className = element.className.replace(classRegExp, "");
                  element.className += " " + className;
                } else {
                  element.className = className;
                }
              }
            });
            return;
          }
        }
        if (composer.commands.support(command)) {
          composer = nodeName || defaultNodeName;
          if (className) {
            var node = dom.observe(doc, "DOMNodeInserted", function(element) {
              element = element.target;
              var left;
              if (element.nodeType === wysihtml5.ELEMENT_NODE) {
                left = dom.getStyle("display").from(element);
                if ("inline" !== left.substr(0, 6)) {
                  element.className += " " + className;
                }
              }
            })
          }
          doc.execCommand(command, false, composer);
          if (node) {
            node.stop();
          }
        } else {
          blockElement = doc.createElement(nodeName || defaultNodeName);
          if (className) {
            blockElement.className = className;
          }
          command = blockElement;
          composer.selection.selectLine();
          composer.selection.surround(command);
          doc = parse(command);
          node = $(command);
          if (doc) {
            if ("BR" === doc.nodeName) {
              doc.parentNode.removeChild(doc);
            }
          }
          if (node) {
            if ("BR" === node.nodeName) {
              node.parentNode.removeChild(node);
            }
          }
          if (doc = command.lastChild) {
            if ("BR" === doc.nodeName) {
              doc.parentNode.removeChild(doc);
            }
          }
          composer.selection.selectNode(command, wysihtml5.browser.displaysCaretInEmptyContentEditableCorrectly());
        }
      }
    },
    state : function(composer, command, nodeName, className, classRegExp) {
      nodeName = "string" === typeof nodeName ? nodeName.toUpperCase() : nodeName;
      composer = composer.selection.getSelectedNode();
      return dom.getParentElement(composer, {
        nodeName : nodeName,
        className : className,
        classRegExp : classRegExp
      });
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  function _getApplier(tagName, className, classRegExp) {
    var identifier = tagName + ":" + className;
    if (!currentIdentifiers[identifier]) {
      var htmlApplier = currentIdentifiers;
      var HTMLApplier = wysihtml5.selection.HTMLApplier;
      var alias = ALIAS_MAPPING[tagName];
      tagName = alias ? [tagName.toLowerCase(), alias.toLowerCase()] : [tagName.toLowerCase()];
      htmlApplier[identifier] = new HTMLApplier(tagName, className, classRegExp, true);
    }
    return currentIdentifiers[identifier];
  }
  var ALIAS_MAPPING = {
    strong : "b",
    em : "i",
    b : "strong",
    i : "em"
  };
  var currentIdentifiers = {};
  wysihtml5.commands.formatInline = {
    exec : function(composer, command, tagName, className, classRegExp) {
      command = composer.selection.getRange();
      if (!command) {
        return false;
      }
      _getApplier(tagName, className, classRegExp).toggleRange(command);
      composer.selection.setSelection(command);
    },
    state : function(composer, command, tagName, className, classRegExp) {
      command = composer.doc;
      var aliasTagName = ALIAS_MAPPING[tagName] || tagName;
      if (!wysihtml5.dom.hasElementWithTagName(command, tagName) && !wysihtml5.dom.hasElementWithTagName(command, aliasTagName) || className && !wysihtml5.dom.hasElementWithClassName(command, className)) {
        return false;
      }
      composer = composer.selection.getRange();
      return!composer ? false : _getApplier(tagName, className, classRegExp).isAppliedToRange(composer);
    }
  };
})(wysihtml5);
wysihtml5.commands.insertHTML = {
  exec : function(composer, command, tag) {
    if (composer.commands.support(command)) {
      composer.doc.execCommand(command, false, tag);
    } else {
      composer.selection.insertHTML(tag);
    }
  },
  state : function() {
    return false;
  }
};
(function(wysihtml5) {
  wysihtml5.commands.insertImage = {
    exec : function(composer, item, target) {
      target = "object" === typeof target ? target : {
        src : target
      };
      var doc = composer.doc;
      item = this.state(composer);
      var k;
      if (item) {
        composer.selection.setBefore(item);
        target = item.parentNode;
        target.removeChild(item);
        wysihtml5.dom.removeEmptyTextNodes(target);
        if ("A" === target.nodeName) {
          if (!target.firstChild) {
            composer.selection.setAfter(target);
            target.parentNode.removeChild(target);
          }
        }
        wysihtml5.quirks.redraw(composer.element);
      } else {
        item = doc.createElement("IMG");
        for (k in target) {
          if ("className" === k) {
            k = "class";
          }
          item.setAttribute(k, target[k]);
        }
        composer.selection.insertNode(item);
        if (wysihtml5.browser.hasProblemsSettingCaretAfterImg()) {
          target = doc.createTextNode(wysihtml5.INVISIBLE_SPACE);
          composer.selection.insertNode(target);
          composer.selection.setAfter(target);
        } else {
          composer.selection.setAfter(item);
        }
      }
    },
    state : function(composer) {
      var target;
      if (!wysihtml5.dom.hasElementWithTagName(composer.doc, "IMG")) {
        return false;
      }
      target = composer.selection.getSelectedNode();
      if (!target) {
        return false;
      }
      if ("IMG" === target.nodeName) {
        return target;
      }
      if (target.nodeType !== wysihtml5.ELEMENT_NODE) {
        return false;
      }
      target = composer.selection.getText();
      if (target = wysihtml5.lang.string(target).trim()) {
        return false;
      }
      composer = composer.selection.getNodes(wysihtml5.ELEMENT_NODE, function(node) {
        return "IMG" === node.nodeName;
      });
      return 1 !== composer.length ? false : composer[0];
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var oppositeCommand = "<br>" + (wysihtml5.browser.needsSpaceAfterLineBreak() ? " " : "");
  wysihtml5.commands.insertLineBreak = {
    exec : function(composer, command) {
      if (composer.commands.support(command)) {
        composer.doc.execCommand(command, false, null);
        if (!wysihtml5.browser.autoScrollsToCaret()) {
          composer.selection.scrollIntoView();
        }
      } else {
        composer.commands.exec("insertHTML", oppositeCommand);
      }
    },
    state : function() {
      return false;
    }
  };
})(wysihtml5);
wysihtml5.commands.insertOrderedList = {
  exec : function(composer, command) {
    var doc = composer.doc;
    var i = composer.selection.getSelectedNode();
    var list = wysihtml5.dom.getParentElement(i, {
      nodeName : "OL"
    });
    var otherList = wysihtml5.dom.getParentElement(i, {
      nodeName : "UL"
    });
    i = "_wysihtml5-temp-" + (new Date).getTime();
    var tempElement;
    if (!list && (!otherList && composer.commands.support(command))) {
      doc.execCommand(command, false, null);
    } else {
      if (list) {
        composer.selection.executeAndRestore(function() {
          wysihtml5.dom.resolveList(list, composer.config.useLineBreaks);
        });
      } else {
        if (otherList) {
          composer.selection.executeAndRestore(function() {
            wysihtml5.dom.renameElement(otherList, "ol");
          });
        } else {
          composer.commands.exec("formatBlock", "div", i);
          tempElement = doc.querySelector("." + i);
          doc = "" === tempElement.innerHTML || (tempElement.innerHTML === wysihtml5.INVISIBLE_SPACE || "<br>" === tempElement.innerHTML);
          composer.selection.executeAndRestore(function() {
            list = wysihtml5.dom.convertToList(tempElement, "ol");
          });
          if (doc) {
            composer.selection.selectNode(list.querySelector("li"), true);
          }
        }
      }
    }
  },
  state : function(composer) {
    composer = composer.selection.getSelectedNode();
    return wysihtml5.dom.getParentElement(composer, {
      nodeName : "OL"
    });
  }
};
wysihtml5.commands.insertUnorderedList = {
  exec : function(composer, command) {
    var doc = composer.doc;
    var i = composer.selection.getSelectedNode();
    var list = wysihtml5.dom.getParentElement(i, {
      nodeName : "UL"
    });
    var otherList = wysihtml5.dom.getParentElement(i, {
      nodeName : "OL"
    });
    i = "_wysihtml5-temp-" + (new Date).getTime();
    var tempElement;
    if (!list && (!otherList && composer.commands.support(command))) {
      doc.execCommand(command, false, null);
    } else {
      if (list) {
        composer.selection.executeAndRestore(function() {
          wysihtml5.dom.resolveList(list, composer.config.useLineBreaks);
        });
      } else {
        if (otherList) {
          composer.selection.executeAndRestore(function() {
            wysihtml5.dom.renameElement(otherList, "ul");
          });
        } else {
          composer.commands.exec("formatBlock", "div", i);
          tempElement = doc.querySelector("." + i);
          doc = "" === tempElement.innerHTML || (tempElement.innerHTML === wysihtml5.INVISIBLE_SPACE || "<br>" === tempElement.innerHTML);
          composer.selection.executeAndRestore(function() {
            list = wysihtml5.dom.convertToList(tempElement, "ul");
          });
          if (doc) {
            composer.selection.selectNode(list.querySelector("li"), true);
          }
        }
      }
    }
  },
  state : function(composer) {
    composer = composer.selection.getSelectedNode();
    return wysihtml5.dom.getParentElement(composer, {
      nodeName : "UL"
    });
  }
};
wysihtml5.commands.italic = {
  exec : function(composer, command) {
    return wysihtml5.commands.formatInline.exec(composer, command, "i");
  },
  state : function(composer, command) {
    return wysihtml5.commands.formatInline.state(composer, command, "i");
  }
};
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-text-align-[0-9a-z]+/g;
  wysihtml5.commands.justifyCenter = {
    exec : function(composer) {
      return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", null, "wysiwyg-text-align-center", REG_EXP);
    },
    state : function(composer) {
      return wysihtml5.commands.formatBlock.state(composer, "formatBlock", null, "wysiwyg-text-align-center", REG_EXP);
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-text-align-[0-9a-z]+/g;
  wysihtml5.commands.justifyLeft = {
    exec : function(composer) {
      return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", null, "wysiwyg-text-align-left", REG_EXP);
    },
    state : function(composer) {
      return wysihtml5.commands.formatBlock.state(composer, "formatBlock", null, "wysiwyg-text-align-left", REG_EXP);
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-text-align-[0-9a-z]+/g;
  wysihtml5.commands.justifyRight = {
    exec : function(composer) {
      return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", null, "wysiwyg-text-align-right", REG_EXP);
    },
    state : function(composer) {
      return wysihtml5.commands.formatBlock.state(composer, "formatBlock", null, "wysiwyg-text-align-right", REG_EXP);
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var REG_EXP = /wysiwyg-text-align-[0-9a-z]+/g;
  wysihtml5.commands.justifyFull = {
    exec : function(composer) {
      return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", null, "wysiwyg-text-align-justify", REG_EXP);
    },
    state : function(composer) {
      return wysihtml5.commands.formatBlock.state(composer, "formatBlock", null, "wysiwyg-text-align-justify", REG_EXP);
    }
  };
})(wysihtml5);
wysihtml5.commands.redo = {
  exec : function(composer) {
    return composer.undoManager.redo();
  },
  state : function() {
    return false;
  }
};
wysihtml5.commands.underline = {
  exec : function(composer, command) {
    return wysihtml5.commands.formatInline.exec(composer, command, "u");
  },
  state : function(composer, command) {
    return wysihtml5.commands.formatInline.state(composer, command, "u");
  }
};
wysihtml5.commands.undo = {
  exec : function(composer) {
    return composer.undoManager.undo();
  },
  state : function() {
    return false;
  }
};
(function(wysihtml5) {
  var UNDO_HTML = '<span id="_wysihtml5-undo" class="_wysihtml5-temp">' + wysihtml5.INVISIBLE_SPACE + "</span>";
  var REDO_HTML = '<span id="_wysihtml5-redo" class="_wysihtml5-temp">' + wysihtml5.INVISIBLE_SPACE + "</span>";
  var dom = wysihtml5.dom;
  wysihtml5.UndoManager = wysihtml5.lang.Dispatcher.extend({
    constructor : function(editor) {
      this.editor = editor;
      this.composer = editor.composer;
      this.element = this.composer.element;
      this.position = 0;
      this.historyStr = [];
      this.historyDom = [];
      this.transact();
      this._observe();
    },
    _observe : function() {
      var that = this;
      var doc = this.composer.sandbox.getDocument();
      var ev;
      dom.observe(this.element, "keydown", function(e) {
        if (!(e.altKey || !e.ctrlKey && !e.metaKey)) {
          var code = e.keyCode;
          var c = 90 === code && e.shiftKey || 89 === code;
          if (90 === code && !e.shiftKey) {
            that.undo();
            e.preventDefault();
          } else {
            if (c) {
              that.redo();
              e.preventDefault();
            }
          }
        }
      });
      dom.observe(this.element, "keydown", function(e) {
        e = e.keyCode;
        if (e !== ev) {
          ev = e;
          if (8 === e || 46 === e) {
            that.transact();
          }
        }
      });
      if (wysihtml5.browser.hasUndoInContextMenu()) {
        var scrollIntervalId;
        var g;
        var cleanUp = function() {
          var tabPage;
          for (;tabPage = doc.querySelector("._wysihtml5-temp");) {
            tabPage.parentNode.removeChild(tabPage);
          }
          clearInterval(scrollIntervalId);
        };
        dom.observe(this.element, "contextmenu", function() {
          cleanUp();
          that.composer.selection.executeAndRestoreSimple(function() {
            if (that.element.lastChild) {
              that.composer.selection.setAfter(that.element.lastChild);
            }
            doc.execCommand("insertHTML", false, UNDO_HTML);
            doc.execCommand("insertHTML", false, REDO_HTML);
            doc.execCommand("undo", false, null);
          });
          scrollIntervalId = setInterval(function() {
            if (doc.getElementById("_wysihtml5-redo")) {
              cleanUp();
              that.redo();
            } else {
              if (!doc.getElementById("_wysihtml5-undo")) {
                cleanUp();
                that.undo();
              }
            }
          }, 400);
          if (!g) {
            g = true;
            dom.observe(document, "mousedown", cleanUp);
            dom.observe(doc, ["mousedown", "paste", "cut", "copy"], cleanUp);
          }
        });
      }
      this.editor.on("newword:composer", function() {
        that.transact();
      }).on("beforecommand:composer", function() {
        that.transact();
      });
    },
    transact : function() {
      var node = this.historyStr[this.position - 1];
      var copies = this.composer.getValue();
      if (copies !== node) {
        if (25 < (this.historyStr.length = this.historyDom.length = this.position)) {
          this.historyStr.shift();
          this.historyDom.shift();
          this.position--;
        }
        this.position++;
        var el = this.composer.selection.getRange();
        node = el.startContainer || this.element;
        var idx = el.startOffset || 0;
        var position;
        if (node.nodeType === wysihtml5.ELEMENT_NODE) {
          el = node;
        } else {
          el = node.parentNode;
          position = this.getChildNodeIndex(el, node);
        }
        el.setAttribute("data-wysihtml5-selection-offset", idx);
        if ("undefined" !== typeof position) {
          el.setAttribute("data-wysihtml5-selection-node", position);
        }
        position = this.element.cloneNode(!!copies);
        this.historyDom.push(position);
        this.historyStr.push(copies);
        el.removeAttribute("data-wysihtml5-selection-offset");
        el.removeAttribute("data-wysihtml5-selection-node");
      }
    },
    undo : function() {
      this.transact();
      if (this.undoPossible()) {
        this.set(this.historyDom[--this.position - 1]);
        this.editor.fire("undo:composer");
      }
    },
    redo : function() {
      if (this.redoPossible()) {
        this.set(this.historyDom[++this.position - 1]);
        this.editor.fire("redo:composer");
      }
    },
    undoPossible : function() {
      return 1 < this.position;
    },
    redoPossible : function() {
      return this.position < this.historyStr.length;
    },
    set : function(node) {
      this.element.innerHTML = "";
      var i = 0;
      var childNodes = node.childNodes;
      var valuesLen = node.childNodes.length;
      for (;i < valuesLen;i++) {
        this.element.appendChild(childNodes[i].cloneNode(true));
      }
      if (node.hasAttribute("data-wysihtml5-selection-offset")) {
        i = node.getAttribute("data-wysihtml5-selection-offset");
        childNodes = node.getAttribute("data-wysihtml5-selection-node");
        node = this.element;
      } else {
        node = this.element.querySelector("[data-wysihtml5-selection-offset]") || this.element;
        i = node.getAttribute("data-wysihtml5-selection-offset");
        childNodes = node.getAttribute("data-wysihtml5-selection-node");
        node.removeAttribute("data-wysihtml5-selection-offset");
        node.removeAttribute("data-wysihtml5-selection-node");
      }
      if (null !== childNodes) {
        node = this.getChildNodeByIndex(node, +childNodes);
      }
      this.composer.selection.set(node, i);
    },
    getChildNodeIndex : function(parent, obj) {
      var i = 0;
      var arr = parent.childNodes;
      var e = arr.length;
      for (;i < e;i++) {
        if (arr[i] === obj) {
          return i;
        }
      }
    },
    getChildNodeByIndex : function(parent, index) {
      return parent.childNodes[index];
    }
  });
})(wysihtml5);
wysihtml5.views.View = Base.extend({
  constructor : function(container, element, config) {
    this.parent = container;
    this.element = element;
    this.config = config;
    this._observeViewChange();
  },
  _observeViewChange : function() {
    var that = this;
    this.parent.on("beforeload", function() {
      that.parent.on("change_view", function(view) {
        if (view === that.name) {
          that.parent.currentView = that;
          that.show();
          setTimeout(function() {
            that.focus();
          }, 0);
        } else {
          that.hide();
        }
      });
    });
  },
  focus : function() {
    if (this.element.ownerDocument.querySelector(":focus") !== this.element) {
      try {
        this.element.focus();
      } catch (b) {
      }
    }
  },
  hide : function() {
    this.element.style.display = "none";
  },
  show : function() {
    this.element.style.display = "";
  },
  disable : function() {
    this.element.setAttribute("disabled", "disabled");
  },
  enable : function() {
    this.element.removeAttribute("disabled");
  }
});
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  var browser = wysihtml5.browser;
  wysihtml5.views.Composer = wysihtml5.views.View.extend({
    name : "composer",
    CARET_HACK : "<br>",
    constructor : function(name, body1, config) {
      this.base(name, body1, config);
      this.textarea = this.parent.textarea;
      this._initSandbox();
    },
    clear : function() {
      this.element.innerHTML = browser.displaysCaretInEmptyContentEditableCorrectly() ? "" : this.CARET_HACK;
    },
    getValue : function(parse) {
      var val = this.isEmpty() ? "" : wysihtml5.quirks.getCorrectInnerHTML(this.element);
      if (parse) {
        val = this.parent.parse(val);
      }
      return val = wysihtml5.lang.string(val).replace(wysihtml5.INVISIBLE_SPACE).by("");
    },
    setValue : function(value, parse) {
      if (parse) {
        value = this.parent.parse(value);
      }
      try {
        this.element.innerHTML = value;
      } catch (c) {
        this.element.innerText = value;
      }
    },
    show : function() {
      this.iframe.style.display = this._displayStyle || "";
      if (!this.textarea.element.disabled) {
        this.disable();
        this.enable();
      }
    },
    hide : function() {
      this._displayStyle = dom.getStyle("display").from(this.iframe);
      if ("none" === this._displayStyle) {
        this._displayStyle = null;
      }
      this.iframe.style.display = "none";
    },
    disable : function() {
      this.parent.fire("disable:composer");
      this.element.removeAttribute("contentEditable");
    },
    enable : function() {
      this.parent.fire("enable:composer");
      this.element.setAttribute("contentEditable", "true");
    },
    focus : function(recurring) {
      if (wysihtml5.browser.doesAsyncFocus()) {
        if (this.hasPlaceholderSet()) {
          this.clear();
        }
      }
      this.base();
      var lastChild = this.element.lastChild;
      if (recurring) {
        if (lastChild) {
          if ("BR" === lastChild.nodeName) {
            this.selection.setBefore(this.element.lastChild);
          } else {
            this.selection.setAfter(this.element.lastChild);
          }
        }
      }
    },
    getTextContent : function() {
      return dom.getTextContent(this.element);
    },
    hasPlaceholderSet : function() {
      return this.getTextContent() == this.textarea.element.getAttribute("placeholder") && this.placeholderSet;
    },
    isEmpty : function() {
      var inputStr = this.element.innerHTML.toLowerCase();
      return "" === inputStr || ("<br>" === inputStr || ("<p></p>" === inputStr || ("<p><br></p>" === inputStr || this.hasPlaceholderSet())));
    },
    _initSandbox : function() {
      var functionStub = this;
      this.sandbox = new dom.Sandbox(function() {
        functionStub._create();
      }, {
        stylesheets : this.config.stylesheets
      });
      this.iframe = this.sandbox.getIframe();
      var textareaElement = this.textarea.element;
      dom.insert(this.iframe).after(textareaElement);
      if (textareaElement.form) {
        var hiddenField = document.createElement("input");
        hiddenField.type = "hidden";
        hiddenField.name = "_wysihtml5_mode";
        hiddenField.value = 1;
        dom.insert(hiddenField).after(textareaElement);
      }
    },
    _create : function() {
      var submenu = this;
      this.doc = this.sandbox.getDocument();
      this.element = this.doc.body;
      this.textarea = this.parent.textarea;
      this.element.innerHTML = this.textarea.getValue(true);
      this.selection = new wysihtml5.Selection(this.parent);
      this.commands = new wysihtml5.Commands(this.parent);
      dom.copyAttributes("className spellcheck title lang dir accessKey".split(" ")).from(this.textarea.element).to(this.element);
      dom.addClass(this.element, this.config.composerClassName);
      if (this.config.style) {
        this.style();
      }
      this.observe();
      var name = this.config.name;
      if (name) {
        dom.addClass(this.element, name);
        dom.addClass(this.iframe, name);
      }
      this.enable();
      if (this.textarea.element.disabled) {
        this.disable();
      }
      if (name = "string" === typeof this.config.placeholder ? this.config.placeholder : this.textarea.element.getAttribute("placeholder")) {
        dom.simulatePlaceholder(this.parent, this, name);
      }
      this.commands.exec("styleWithCSS", false);
      this._initAutoLinking();
      this._initObjectResizing();
      this._initUndoManager();
      this._initLineBreaking();
      if (this.textarea.element.hasAttribute("autofocus") || document.querySelector(":focus") == this.textarea.element) {
        if (!browser.isIos()) {
          setTimeout(function() {
            submenu.focus(true);
          }, 100);
        }
      }
      if (!browser.clearsContentEditableCorrectly()) {
        wysihtml5.quirks.ensureProperClearing(this);
      }
      if (this.initSync) {
        if (this.config.sync) {
          this.initSync();
        }
      }
      this.textarea.hide();
      this.parent.fire("beforeload").fire("load");
    },
    _initAutoLinking : function() {
      var that = this;
      var supportsDisablingOfAutoLinking = browser.canDisableAutoLinking();
      var supportsAutoLinking = browser.doesAutoLinkingInContentEditable();
      if (supportsDisablingOfAutoLinking) {
        this.commands.exec("autoUrlDetect", false);
      }
      if (this.config.autoLink) {
        if (!supportsAutoLinking || supportsAutoLinking && supportsDisablingOfAutoLinking) {
          this.parent.on("newword:composer", function() {
            if (dom.getTextContent(that.element).match(dom.autoLink.URL_REG_EXP)) {
              that.selection.executeAndRestore(function(dataAndEvents, endContainer) {
                dom.autoLink(endContainer.parentNode);
              });
            }
          });
          dom.observe(this.element, "blur", function() {
            dom.autoLink(that.element);
          });
        }
        var ka = this.sandbox.getDocument().getElementsByTagName("a");
        var LINKY_URL_REGEXP = dom.autoLink.URL_REG_EXP;
        var getTextContent = function(element) {
          element = wysihtml5.lang.string(dom.getTextContent(element)).trim();
          if ("www." === element.substr(0, 4)) {
            element = "http://" + element;
          }
          return element;
        };
        dom.observe(this.element, "keydown", function(event) {
          if (ka.length) {
            event = that.selection.getSelectedNode(event.target.ownerDocument);
            var element = dom.getParentElement(event, {
              nodeName : "A"
            }, 4);
            var maxLength;
            if (element) {
              maxLength = getTextContent(element);
              setTimeout(function() {
                var text = getTextContent(element);
                if (text !== maxLength) {
                  if (text.match(LINKY_URL_REGEXP)) {
                    element.setAttribute("href", text);
                  }
                }
              }, 0);
            }
          }
        });
      }
    },
    _initObjectResizing : function() {
      this.commands.exec("enableObjectResizing", true);
      if (browser.supportsEvent("resizeend")) {
        var properties = ["width", "height"];
        var ii = properties.length;
        var element = this.element;
        dom.observe(element, "resizeend", function(node) {
          node = node.target || node.srcElement;
          var style = node.style;
          var i = 0;
          var property;
          if ("IMG" === node.nodeName) {
            for (;i < ii;i++) {
              property = properties[i];
              if (style[property]) {
                node.setAttribute(property, parseInt(style[property], 10));
                style[property] = "";
              }
            }
            wysihtml5.quirks.redraw(element);
          }
        });
      }
    },
    _initUndoManager : function() {
      this.undoManager = new wysihtml5.UndoManager(this.parent);
    },
    _initLineBreaking : function() {
      function adjust(selectedNode) {
        var parentElement = dom.getParentElement(selectedNode, {
          nodeName : ["P", "DIV"]
        }, 2);
        if (parentElement) {
          that.selection.executeAndRestore(function() {
            if (that.config.useLineBreaks) {
              dom.replaceWithChildNodes(parentElement);
            } else {
              if ("P" !== parentElement.nodeName) {
                dom.renameElement(parentElement, "p");
              }
            }
          });
        }
      }
      var that = this;
      var nodeName = "LI P H1 H2 H3 H4 H5 H6".split(" ");
      var LIST_TAGS = ["UL", "OL", "MENU"];
      if (!this.config.useLineBreaks) {
        dom.observe(this.element, ["focus", "keydown"], function() {
          if (that.isEmpty()) {
            var node = that.doc.createElement("P");
            that.element.innerHTML = "";
            that.element.appendChild(node);
            if (browser.displaysCaretInEmptyContentEditableCorrectly()) {
              that.selection.selectNode(node, true);
            } else {
              node.innerHTML = "<br>";
              that.selection.setBefore(node.firstChild);
            }
          }
        });
      }
      dom.observe(this.doc, "keydown", function(event) {
        var keyCode = event.keyCode;
        if (!event.shiftKey && !(keyCode !== wysihtml5.ENTER_KEY && keyCode !== wysihtml5.BACKSPACE_KEY)) {
          var child = dom.getParentElement(that.selection.getSelectedNode(), {
            nodeName : nodeName
          }, 4);
          if (child) {
            setTimeout(function() {
              var selectedNode = that.selection.getSelectedNode();
              var list;
              if ("LI" === child.nodeName) {
                if (!selectedNode) {
                  return;
                }
                if (!(list = dom.getParentElement(selectedNode, {
                  nodeName : LIST_TAGS
                }, 2))) {
                  adjust(selectedNode);
                }
              }
              if (keyCode === wysihtml5.ENTER_KEY) {
                if (child.nodeName.match(/^H[1-6]$/)) {
                  adjust(selectedNode);
                }
              }
            }, 0);
          } else {
            if (that.config.useLineBreaks) {
              if (keyCode === wysihtml5.ENTER_KEY && !wysihtml5.browser.insertsLineBreaksOnReturn()) {
                that.commands.exec("insertLineBreak");
                event.preventDefault();
              }
            }
          }
        }
      });
    }
  });
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  var d = document;
  var node = window;
  var div = d.createElement("div");
  var requires = "background-color color cursor font-family font-size font-style font-variant font-weight line-height letter-spacing text-align text-decoration text-indent text-rendering word-break word-wrap word-spacing".split(" ");
  var arr = "background-color border-collapse border-bottom-color border-bottom-style border-bottom-width border-left-color border-left-style border-left-width border-right-color border-right-style border-right-width border-top-color border-top-style border-top-width clear display float margin-bottom margin-left margin-right margin-top outline-color outline-offset outline-width outline-style padding-left padding-right padding-top padding-bottom position top left right bottom z-index vertical-align text-align -webkit-box-sizing -moz-box-sizing -ms-box-sizing box-sizing -webkit-box-shadow -moz-box-shadow -ms-box-shadow box-shadow -webkit-border-top-right-radius -moz-border-radius-topright border-top-right-radius -webkit-border-bottom-right-radius -moz-border-radius-bottomright border-bottom-right-radius -webkit-border-bottom-left-radius -moz-border-radius-bottomleft border-bottom-left-radius -webkit-border-top-left-radius -moz-border-radius-topleft border-top-left-radius width height".split(" ");
  var ADDITIONAL_CSS_RULES = ["html                 { height: 100%; }", "body                 { height: 100%; padding: 1px 0 0 0; margin: -1px 0 0 0; }", "body > p:first-child { margin-top: 0; }", "._wysihtml5-temp     { display: none; }", wysihtml5.browser.isGecko ? "body.placeholder { color: graytext !important; }" : "body.placeholder { color: #a9a9a9 !important; }", "img:-moz-broken      { -moz-force-broken-image-icon: 1; height: 24px; width: 24px; }"];
  wysihtml5.views.Composer.prototype.style = function() {
    var that = this;
    var container = d.querySelector(":focus");
    var element = this.textarea.element;
    var hasPlaceholder = element.hasAttribute("placeholder");
    var roleName = hasPlaceholder && element.getAttribute("placeholder");
    var originalDisplayValue = element.style.display;
    var value = element.disabled;
    var displayValueForCopying;
    this.focusStylesHost = div.cloneNode(false);
    this.blurStylesHost = div.cloneNode(false);
    this.disabledStylesHost = div.cloneNode(false);
    if (hasPlaceholder) {
      element.removeAttribute("placeholder");
    }
    if (element === container) {
      element.blur();
    }
    element.disabled = false;
    element.style.display = displayValueForCopying = "none";
    if (element.getAttribute("rows") && "auto" === dom.getStyle("height").from(element) || element.getAttribute("cols") && "auto" === dom.getStyle("width").from(element)) {
      element.style.display = displayValueForCopying = originalDisplayValue;
    }
    dom.copyStyles(arr).from(element).to(this.iframe).andTo(this.blurStylesHost);
    dom.copyStyles(requires).from(element).to(this.element).andTo(this.blurStylesHost);
    dom.insertCSS(ADDITIONAL_CSS_RULES).into(this.element.ownerDocument);
    element.disabled = true;
    dom.copyStyles(arr).from(element).to(this.disabledStylesHost);
    dom.copyStyles(requires).from(element).to(this.disabledStylesHost);
    element.disabled = value;
    element.style.display = originalDisplayValue;
    if (element.setActive) {
      try {
        element.setActive();
      } catch (r) {
      }
    } else {
      var styles = element.style;
      value = d.documentElement.scrollTop || d.body.scrollTop;
      var newVal = d.documentElement.scrollLeft || d.body.scrollLeft;
      styles = {
        position : styles.position,
        top : styles.top,
        left : styles.left,
        WebkitUserSelect : styles.WebkitUserSelect
      };
      dom.setStyles({
        position : "absolute",
        top : "-99999px",
        left : "-99999px",
        WebkitUserSelect : "none"
      }).on(element);
      element.focus();
      dom.setStyles(styles).on(element);
      if (node.scrollTo) {
        node.scrollTo(newVal, value);
      }
    }
    element.style.display = displayValueForCopying;
    dom.copyStyles(arr).from(element).to(this.focusStylesHost);
    dom.copyStyles(requires).from(element).to(this.focusStylesHost);
    element.style.display = originalDisplayValue;
    dom.copyStyles(["display"]).from(element).to(this.iframe);
    var boxFormattingStyles = wysihtml5.lang.array(arr).without(["display"]);
    if (container) {
      container.focus();
    } else {
      element.blur();
    }
    if (hasPlaceholder) {
      element.setAttribute("placeholder", roleName);
    }
    this.parent.on("focus:composer", function() {
      dom.copyStyles(boxFormattingStyles).from(that.focusStylesHost).to(that.iframe);
      dom.copyStyles(requires).from(that.focusStylesHost).to(that.element);
    });
    this.parent.on("blur:composer", function() {
      dom.copyStyles(boxFormattingStyles).from(that.blurStylesHost).to(that.iframe);
      dom.copyStyles(requires).from(that.blurStylesHost).to(that.element);
    });
    this.parent.observe("disable:composer", function() {
      dom.copyStyles(boxFormattingStyles).from(that.disabledStylesHost).to(that.iframe);
      dom.copyStyles(requires).from(that.disabledStylesHost).to(that.element);
    });
    this.parent.observe("enable:composer", function() {
      dom.copyStyles(boxFormattingStyles).from(that.blurStylesHost).to(that.iframe);
      dom.copyStyles(requires).from(that.blurStylesHost).to(that.element);
    });
    return this;
  };
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  var browser = wysihtml5.browser;
  var keysHit = {
    66 : "bold",
    73 : "italic",
    85 : "underline"
  };
  wysihtml5.views.Composer.prototype.observe = function() {
    var that = this;
    var myType = this.getValue();
    var value = this.sandbox.getIframe();
    var element = this.element;
    var container = browser.supportsEventsInIframeCorrectly() ? element : this.sandbox.getWindow();
    dom.observe(value, "DOMNodeRemoved", function() {
      clearInterval(poll);
      that.parent.fire("destroy:composer");
    });
    var poll = setInterval(function() {
      if (!dom.contains(document.documentElement, value)) {
        clearInterval(poll);
        that.parent.fire("destroy:composer");
      }
    }, 250);
    dom.observe(container, "focus", function() {
      that.parent.fire("focus").fire("focus:composer");
      setTimeout(function() {
        myType = that.getValue();
      }, 0);
    });
    dom.observe(container, "blur", function() {
      if (myType !== that.getValue()) {
        that.parent.fire("change").fire("change:composer");
      }
      that.parent.fire("blur").fire("blur:composer");
    });
    dom.observe(element, "dragenter", function() {
      that.parent.fire("unset_placeholder");
    });
    dom.observe(element, ["drop", "paste"], function() {
      setTimeout(function() {
        that.parent.fire("paste").fire("paste:composer");
      }, 0);
    });
    dom.observe(element, "keyup", function(keyCode) {
      keyCode = keyCode.keyCode;
      if (keyCode === wysihtml5.SPACE_KEY || keyCode === wysihtml5.ENTER_KEY) {
        that.parent.fire("newword:composer");
      }
    });
    this.parent.on("paste:composer", function() {
      setTimeout(function() {
        that.parent.fire("newword:composer");
      }, 0);
    });
    if (!browser.canSelectImagesInContentEditable()) {
      dom.observe(element, "mousedown", function(evt) {
        var node = evt.target;
        if ("IMG" === node.nodeName) {
          that.selection.selectNode(node);
          evt.preventDefault();
        }
      });
    }
    if (browser.hasHistoryIssue()) {
      if (browser.supportsSelectionModify()) {
        dom.observe(element, "keydown", function(event) {
          if (event.metaKey || event.ctrlKey) {
            var code = event.keyCode;
            var selection = element.ownerDocument.defaultView.getSelection();
            if (37 === code || 39 === code) {
              if (37 === code) {
                selection.modify("extend", "left", "lineboundary");
                if (!event.shiftKey) {
                  selection.collapseToStart();
                }
              }
              if (39 === code) {
                selection.modify("extend", "right", "lineboundary");
                if (!event.shiftKey) {
                  selection.collapseToEnd();
                }
              }
              event.preventDefault();
            }
          }
        });
      }
    }
    dom.observe(element, "keydown", function(event) {
      var composer = keysHit[event.keyCode];
      if ((event.ctrlKey || event.metaKey) && (!event.altKey && composer)) {
        that.commands.exec(composer);
        event.preventDefault();
      }
    });
    dom.observe(element, "keydown", function(event) {
      var t = that.selection.getSelectedNode(true);
      var e = event.keyCode;
      if (t && ("IMG" === t.nodeName && (e === wysihtml5.BACKSPACE_KEY || e === wysihtml5.DELETE_KEY))) {
        e = t.parentNode;
        e.removeChild(t);
        if ("A" === e.nodeName) {
          if (!e.firstChild) {
            e.parentNode.removeChild(e);
          }
        }
        setTimeout(function() {
          wysihtml5.quirks.redraw(element);
        }, 0);
        event.preventDefault();
      }
    });
    if (browser.hasIframeFocusIssue()) {
      dom.observe(this.iframe, "focus", function() {
        setTimeout(function() {
          if (that.doc.querySelector(":focus") !== that.element) {
            that.focus();
          }
        }, 0);
      });
      dom.observe(this.element, "blur", function() {
        setTimeout(function() {
          that.selection.getSelection().removeAllRanges();
        }, 0);
      });
    }
    var special = {
      IMG : "Image: ",
      A : "Link: "
    };
    dom.observe(element, "mouseover", function(element) {
      element = element.target;
      var type = element.nodeName;
      if (!("A" !== type && "IMG" !== type)) {
        if (!element.hasAttribute("title")) {
          type = special[type] + (element.getAttribute("href") || element.getAttribute("src"));
          element.setAttribute("title", type);
        }
      }
    });
  };
})(wysihtml5);
(function(wysihtml5) {
  wysihtml5.views.Synchronizer = Base.extend({
    constructor : function(inNode, el, composer) {
      this.editor = inNode;
      this.textarea = el;
      this.composer = composer;
      this._observe();
    },
    fromComposerToTextarea : function(shouldParseHtml) {
      this.textarea.setValue(wysihtml5.lang.string(this.composer.getValue()).trim(), shouldParseHtml);
    },
    fromTextareaToComposer : function(shouldParseHtml) {
      var pdataOld = this.textarea.getValue();
      if (pdataOld) {
        this.composer.setValue(pdataOld, shouldParseHtml);
      } else {
        this.composer.clear();
        this.editor.fire("set_placeholder");
      }
    },
    sync : function(shouldParseHtml) {
      if ("textarea" === this.editor.currentView.name) {
        this.fromTextareaToComposer(shouldParseHtml);
      } else {
        this.fromComposerToTextarea(shouldParseHtml);
      }
    },
    _observe : function() {
      var scrollIntervalId;
      var that = this;
      var which = this.textarea.element.form;
      var startInterval = function() {
        scrollIntervalId = setInterval(function() {
          that.fromComposerToTextarea();
        }, 400);
      };
      var cleanUp = function() {
        clearInterval(scrollIntervalId);
        scrollIntervalId = null;
      };
      startInterval();
      if (which) {
        wysihtml5.dom.observe(which, "submit", function() {
          that.sync(true);
        });
        wysihtml5.dom.observe(which, "reset", function() {
          setTimeout(function() {
            that.fromTextareaToComposer();
          }, 0);
        });
      }
      this.editor.on("change_view", function(dataAndEvents) {
        if ("composer" === dataAndEvents && !scrollIntervalId) {
          that.fromTextareaToComposer(true);
          startInterval();
        } else {
          if ("textarea" === dataAndEvents) {
            that.fromComposerToTextarea(true);
            cleanUp();
          }
        }
      });
      this.editor.on("destroy:composer", cleanUp);
    }
  });
})(wysihtml5);
wysihtml5.views.Textarea = wysihtml5.views.View.extend({
  name : "textarea",
  constructor : function(name, body1, config) {
    this.base(name, body1, config);
    this._observe();
  },
  clear : function() {
    this.element.value = "";
  },
  getValue : function(parse) {
    var val = this.isEmpty() ? "" : this.element.value;
    if (parse) {
      val = this.parent.parse(val);
    }
    return val;
  },
  setValue : function(value, parse) {
    if (parse) {
      value = this.parent.parse(value);
    }
    this.element.value = value;
  },
  hasPlaceholderSet : function() {
    var supportsPlaceholder = wysihtml5.browser.supportsPlaceholderAttributeOn(this.element);
    var placeholderText = this.element.getAttribute("placeholder") || null;
    var value = this.element.value;
    return supportsPlaceholder && !value || value === placeholderText;
  },
  isEmpty : function() {
    return!wysihtml5.lang.string(this.element.value).trim() || this.hasPlaceholderSet();
  },
  _observe : function() {
    var wrapper = this.element;
    var parent = this.parent;
    var eventMapping = {
      focusin : "focus",
      focusout : "blur"
    };
    var eventType = wysihtml5.browser.supportsEvent("focusin") ? ["focusin", "focusout", "change"] : ["focus", "blur", "change"];
    parent.on("beforeload", function() {
      wysihtml5.dom.observe(wrapper, eventType, function(event) {
        event = eventMapping[event.type] || event.type;
        parent.fire(event).fire(event + ":textarea");
      });
      wysihtml5.dom.observe(wrapper, ["paste", "drop"], function() {
        setTimeout(function() {
          parent.fire("paste").fire("paste:textarea");
        }, 0);
      });
    });
  }
});
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  wysihtml5.toolbar.Dialog = wysihtml5.lang.Dispatcher.extend({
    constructor : function(link, container) {
      this.link = link;
      this.container = container;
    },
    _observe : function() {
      if (!this._observed) {
        var that = this;
        var callbackWrapper = function(event) {
          var attributes = that._serialize();
          if (attributes == that.elementToChange) {
            that.fire("edit", attributes);
          } else {
            that.fire("save", attributes);
          }
          that.hide();
          event.preventDefault();
          event.stopPropagation();
        };
        dom.observe(that.link, "click", function() {
          if (dom.hasClass(that.link, "wysihtml5-command-dialog-opened")) {
            setTimeout(function() {
              that.hide();
            }, 0);
          }
        });
        dom.observe(this.container, "keydown", function(event) {
          var keyCode = event.keyCode;
          if (keyCode === wysihtml5.ENTER_KEY) {
            callbackWrapper(event);
          }
          if (keyCode === wysihtml5.ESCAPE_KEY) {
            that.hide();
          }
        });
        dom.delegate(this.container, "[data-wysihtml5-dialog-action=save]", "click", callbackWrapper);
        dom.delegate(this.container, "[data-wysihtml5-dialog-action=cancel]", "click", function(event) {
          that.fire("cancel");
          that.hide();
          event.preventDefault();
          event.stopPropagation();
        });
        var formElements = this.container.querySelectorAll("input, select, textarea");
        var i = 0;
        var n = formElements.length;
        var cleanUp = function() {
          clearInterval(that.interval);
        };
        for (;i < n;i++) {
          dom.observe(formElements[i], "change", cleanUp);
        }
        this._observed = true;
      }
    },
    _serialize : function() {
      var data = this.elementToChange || {};
      var results = this.container.querySelectorAll("[data-wysihtml5-dialog-field]");
      var l = results.length;
      var i = 0;
      for (;i < l;i++) {
        data[results[i].getAttribute("data-wysihtml5-dialog-field")] = results[i].value;
      }
      return data;
    },
    _interpolate : function(dataAndEvents) {
      var elem;
      var value;
      var docElem = document.querySelector(":focus");
      var checkSet = this.container.querySelectorAll("[data-wysihtml5-dialog-field]");
      var l = checkSet.length;
      var i = 0;
      for (;i < l;i++) {
        elem = checkSet[i];
        if (elem !== docElem) {
          if (!(dataAndEvents && "hidden" === elem.type)) {
            value = elem.getAttribute("data-wysihtml5-dialog-field");
            value = this.elementToChange ? this.elementToChange[value] || "" : elem.defaultValue;
            elem.value = value;
          }
        }
      }
    },
    show : function(elementToChange) {
      if (!dom.hasClass(this.link, "wysihtml5-command-dialog-opened")) {
        var that = this;
        var firstField = this.container.querySelector("input, select, textarea");
        this.elementToChange = elementToChange;
        this._observe();
        this._interpolate();
        if (elementToChange) {
          this.interval = setInterval(function() {
            that._interpolate(true);
          }, 500);
        }
        dom.addClass(this.link, "wysihtml5-command-dialog-opened");
        this.container.style.display = "";
        this.fire("show");
        if (firstField && !elementToChange) {
          try {
            firstField.focus();
          } catch (f) {
          }
        }
      }
    },
    hide : function() {
      clearInterval(this.interval);
      this.elementToChange = null;
      dom.removeClass(this.link, "wysihtml5-command-dialog-opened");
      this.container.style.display = "none";
      this.fire("hide");
    }
  });
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  var o = {
    position : "relative"
  };
  var wrapperStyles = {
    left : 0,
    margin : 0,
    opacity : 0,
    overflow : "hidden",
    padding : 0,
    position : "absolute",
    top : 0,
    zIndex : 1
  };
  var inputStyles = {
    cursor : "inherit",
    fontSize : "50px",
    height : "50px",
    marginTop : "-25px",
    outline : 0,
    padding : 0,
    position : "absolute",
    right : "-4px",
    top : "50%"
  };
  var inputAttributes = {
    "x-webkit-speech" : "",
    speech : ""
  };
  wysihtml5.toolbar.Speech = function(parent, link) {
    var input = document.createElement("input");
    if (wysihtml5.browser.supportsSpeechApiOn(input)) {
      var wrapper = parent.editor.textarea.element.getAttribute("lang");
      if (wrapper) {
        inputAttributes.lang = wrapper;
      }
      wrapper = document.createElement("div");
      wysihtml5.lang.object(wrapperStyles).merge({
        width : link.offsetWidth + "px",
        height : link.offsetHeight + "px"
      });
      dom.insert(input).into(wrapper);
      dom.insert(wrapper).into(link);
      dom.setStyles(inputStyles).on(input);
      dom.setAttributes(inputAttributes).on(input);
      dom.setStyles(wrapperStyles).on(wrapper);
      dom.setStyles(o).on(link);
      dom.observe(input, "onwebkitspeechchange" in input ? "webkitspeechchange" : "speechchange", function() {
        parent.execCommand("insertText", input.value);
        input.value = "";
      });
      dom.observe(input, "click", function(event) {
        if (dom.hasClass(link, "wysihtml5-command-disabled")) {
          event.preventDefault();
        }
        event.stopPropagation();
      });
    } else {
      link.style.display = "none";
    }
  };
})(wysihtml5);
(function(wysihtml5) {
  var dom = wysihtml5.dom;
  wysihtml5.toolbar.Toolbar = Base.extend({
    constructor : function(editor, a) {
      this.editor = editor;
      this.container = "string" === typeof a ? document.getElementById(a) : a;
      this.composer = editor.composer;
      this._getLinks("command");
      this._getLinks("action");
      this._observe();
      this.show();
      var codeSegments = this.container.querySelectorAll("[data-wysihtml5-command=insertSpeech]");
      var valuesLen = codeSegments.length;
      var i = 0;
      for (;i < valuesLen;i++) {
        new wysihtml5.toolbar.Speech(this, codeSegments[i]);
      }
    },
    _getLinks : function(type) {
      var codeSegments = this[type + "Links"] = wysihtml5.lang.array(this.container.querySelectorAll("[data-wysihtml5-" + type + "]")).get();
      var valuesLen = codeSegments.length;
      var i = 0;
      var mapping = this[type + "Mapping"] = {};
      var link;
      var grp;
      var name;
      var value;
      var dialog;
      for (;i < valuesLen;i++) {
        link = codeSegments[i];
        name = link.getAttribute("data-wysihtml5-" + type);
        value = link.getAttribute("data-wysihtml5-" + type + "-value");
        grp = this.container.querySelector("[data-wysihtml5-" + type + "-group='" + name + "']");
        dialog = this._getDialog(link, name);
        mapping[name + ":" + value] = {
          link : link,
          group : grp,
          name : name,
          value : value,
          dialog : dialog,
          state : false
        };
      }
    },
    _getDialog : function(link, command) {
      var that = this;
      var dialogElement = this.container.querySelector("[data-wysihtml5-dialog='" + command + "']");
      var dialog;
      var pos;
      if (dialogElement) {
        dialog = new wysihtml5.toolbar.Dialog(link, dialogElement);
        dialog.on("show", function() {
          pos = that.composer.selection.getBookmark();
          that.editor.fire("show:dialog", {
            command : command,
            dialogContainer : dialogElement,
            commandLink : link
          });
        });
        dialog.on("save", function(attributes) {
          if (pos) {
            that.composer.selection.setBookmark(pos);
          }
          that._execCommand(command, attributes);
          that.editor.fire("save:dialog", {
            command : command,
            dialogContainer : dialogElement,
            commandLink : link
          });
        });
        dialog.on("cancel", function() {
          that.editor.focus(false);
          that.editor.fire("cancel:dialog", {
            command : command,
            dialogContainer : dialogElement,
            commandLink : link
          });
        });
      }
      return dialog;
    },
    execCommand : function(command, recurring) {
      if (!this.commandsDisabled) {
        var commandObj = this.commandMapping[command + ":" + recurring];
        if (commandObj && (commandObj.dialog && !commandObj.state)) {
          commandObj.dialog.show();
        } else {
          this._execCommand(command, recurring);
        }
      }
    },
    _execCommand : function(composer, oppositeCommand) {
      this.editor.focus(false);
      this.composer.commands.exec(composer, oppositeCommand);
      this._updateLinkStates();
    },
    execAction : function(action) {
      var editor = this.editor;
      if ("change_view" === action) {
        if (editor.currentView === editor.textarea) {
          editor.fire("change_view", "composer");
        } else {
          editor.fire("change_view", "textarea");
        }
      }
    },
    _observe : function() {
      var that = this;
      var editor = this.editor;
      var activeClassName = this.container;
      var links = this.commandLinks.concat(this.actionLinks);
      var l = links.length;
      var i = 0;
      for (;i < l;i++) {
        dom.setAttributes({
          href : "javascript:;",
          unselectable : "on"
        }).on(links[i]);
      }
      dom.delegate(activeClassName, "[data-wysihtml5-command], [data-wysihtml5-action]", "mousedown", function(types) {
        types.preventDefault();
      });
      dom.delegate(activeClassName, "[data-wysihtml5-command]", "click", function(types) {
        var command = this.getAttribute("data-wysihtml5-command");
        var commandValue = this.getAttribute("data-wysihtml5-command-value");
        that.execCommand(command, commandValue);
        types.preventDefault();
      });
      dom.delegate(activeClassName, "[data-wysihtml5-action]", "click", function(types) {
        var action = this.getAttribute("data-wysihtml5-action");
        that.execAction(action);
        types.preventDefault();
      });
      editor.on("focus:composer", function() {
        that.bookmark = null;
        clearInterval(that.interval);
        that.interval = setInterval(function() {
          that._updateLinkStates();
        }, 500);
      });
      editor.on("blur:composer", function() {
        clearInterval(that.interval);
      });
      editor.on("destroy:composer", function() {
        clearInterval(that.interval);
      });
      editor.on("change_view", function(composer) {
        setTimeout(function() {
          that.commandsDisabled = "composer" !== composer;
          that._updateLinkStates();
          if (that.commandsDisabled) {
            dom.addClass(activeClassName, "wysihtml5-commands-disabled");
          } else {
            dom.removeClass(activeClassName, "wysihtml5-commands-disabled");
          }
        }, 0);
      });
    },
    _updateLinkStates : function() {
      var args = this.commandMapping;
      var iteratee = this.actionMapping;
      var index;
      var state;
      var command;
      for (index in args) {
        command = args[index];
        if (this.commandsDisabled) {
          state = false;
          dom.removeClass(command.link, "wysihtml5-command-active");
          if (command.group) {
            dom.removeClass(command.group, "wysihtml5-command-active");
          }
          if (command.dialog) {
            command.dialog.hide();
          }
        } else {
          state = this.composer.commands.state(command.name, command.value);
          if (wysihtml5.lang.object(state).isArray()) {
            state = 1 === state.length ? state[0] : true;
          }
          dom.removeClass(command.link, "wysihtml5-command-disabled");
          if (command.group) {
            dom.removeClass(command.group, "wysihtml5-command-disabled");
          }
        }
        if (command.state !== state) {
          if (command.state = state) {
            dom.addClass(command.link, "wysihtml5-command-active");
            if (command.group) {
              dom.addClass(command.group, "wysihtml5-command-active");
            }
            if (command.dialog) {
              if ("object" === typeof state) {
                command.dialog.show(state);
              } else {
                command.dialog.hide();
              }
            }
          } else {
            dom.removeClass(command.link, "wysihtml5-command-active");
            if (command.group) {
              dom.removeClass(command.group, "wysihtml5-command-active");
            }
            if (command.dialog) {
              command.dialog.hide();
            }
          }
        }
      }
      for (index in iteratee) {
        args = iteratee[index];
        if ("change_view" === args.name) {
          args.state = this.editor.currentView === this.editor.textarea;
          if (args.state) {
            dom.addClass(args.link, "wysihtml5-action-active");
          } else {
            dom.removeClass(args.link, "wysihtml5-action-active");
          }
        }
      }
    },
    show : function() {
      this.container.style.display = "";
    },
    hide : function() {
      this.container.style.display = "none";
    }
  });
})(wysihtml5);
(function(wysihtml5) {
  var defaultConfig = {
    name : void 0,
    style : true,
    toolbar : void 0,
    autoLink : true,
    parserRules : {
      tags : {
        br : {},
        span : {},
        div : {},
        p : {}
      },
      classes : {}
    },
    parser : wysihtml5.dom.parse,
    composerClassName : "wysihtml5-editor",
    bodyClassName : "wysihtml5-supported",
    useLineBreaks : true,
    stylesheets : [],
    placeholderText : void 0,
    supportTouchDevices : true
  };
  wysihtml5.Editor = wysihtml5.lang.Dispatcher.extend({
    constructor : function(a, config) {
      this.textareaElement = "string" === typeof a ? document.getElementById(a) : a;
      this.config = wysihtml5.lang.object({}).merge(defaultConfig).merge(config).get();
      this.currentView = this.textarea = new wysihtml5.views.Textarea(this, this.textareaElement, this.config);
      this._isCompatible = wysihtml5.browser.supported();
      if (!this._isCompatible || !this.config.supportTouchDevices && wysihtml5.browser.isTouchDevice()) {
        var that = this;
        setTimeout(function() {
          that.fire("beforeload").fire("load");
        }, 0);
      } else {
        wysihtml5.dom.addClass(document.body, this.config.bodyClassName);
        this.currentView = this.composer = new wysihtml5.views.Composer(this, this.textareaElement, this.config);
        if ("function" === typeof this.config.parser) {
          this._initParser();
        }
        this.on("beforeload", function() {
          this.synchronizer = new wysihtml5.views.Synchronizer(this, this.textarea, this.composer);
          if (this.config.toolbar) {
            this.toolbar = new wysihtml5.toolbar.Toolbar(this, this.config.toolbar);
          }
        });
        try {
          console.log("Heya! This page is using wysihtml5 for rich text editing. Check out https://github.com/xing/wysihtml5");
        } catch (f) {
        }
      }
    },
    isCompatible : function() {
      return this._isCompatible;
    },
    clear : function() {
      this.currentView.clear();
      return this;
    },
    getValue : function(parse) {
      return this.currentView.getValue(parse);
    },
    setValue : function(value, parse) {
      this.fire("unset_placeholder");
      if (!value) {
        return this.clear();
      }
      this.currentView.setValue(value, parse);
      return this;
    },
    focus : function(recurring) {
      this.currentView.focus(recurring);
      return this;
    },
    disable : function() {
      this.currentView.disable();
      return this;
    },
    enable : function() {
      this.currentView.enable();
      return this;
    },
    isEmpty : function() {
      return this.currentView.isEmpty();
    },
    hasPlaceholderSet : function() {
      return this.currentView.hasPlaceholderSet();
    },
    parse : function(value) {
      var values = this.config.parser(value, this.config.parserRules, this.composer.sandbox.getDocument(), true);
      if ("object" === typeof value) {
        wysihtml5.quirks.redraw(value);
      }
      return values;
    },
    _initParser : function() {
      this.on("paste:composer", function() {
        var that = this;
        that.composer.selection.executeAndRestore(function() {
          wysihtml5.quirks.cleanPastedHTML(that.composer.element);
          that.parse(that.composer.element);
        }, true);
      });
    }
  });
})(wysihtml5);
