const observerByElement = new Map();
const observedChildrenByElement = new Map();
const elementByObservedChild = new Map();
const removeHandlersByElement = new Map();

function removeObserverIfUnused(elem) {
    if (removeHandlersByElement.has(elem)) {
        if (removeHandlersByElement.get(elem).size > 0) {
            return;
        }

        removeHandlersByElement.delete(elem);
    }

    if (observerByElement.has(elem)) {
        return;
    }

    const parentElem = elementByObservedChild.get(elem);
    if (parentElem === undefined || !observerByElement.has(parentElem)) {
        return;
    }

    observedChildrenByElement.get(parentElem).delete(elem);
    elementByObservedChild.delete(elem);

    if (observedChildrenByElement.get(parentElem).size > 0) {
        return;
    }

    const observer = observerByElement.get(parentElem);
    observer.disconnect();
    observerByElement.delete(parentElem);

    observedChildrenByElement.delete(parentElem);

    removeObserverIfUnused(parentElem);
}

function handleElementRemove(elem) {
    const observedChildren = observedChildrenByElement.get(elem) ?? [];
    const removeHandlers = removeHandlersByElement.get(elem) ?? [];

    removeHandlersByElement.delete(elem);

    removeObserverIfUnused(elem);

    for (const child of observedChildren) {
        handleElementRemove(child);
    }

    for (const handler of removeHandlers) {
        handler();
    }
}

function handleObserverRecords(elem, mutationRecords) {
    const observedChildren = observedChildrenByElement.get(elem);

    const removedElems = new Set();
    for (const mutationRecord of mutationRecords) {
        for (const removedNode of mutationRecord.removedNodes) {
            if (observedChildren.has(removedNode)) {
                removedElems.add(removedNode);
            }
        }
    }

    for (const removedElem of removedElems) {
        const parentElem = removedElem.parentElement;
        if (parentElem !== null) {
            const parentElemOrig = elementByObservedChild.get(removedElem);
            if (parentElem === parentElemOrig) {
                continue;
            } else {
                console.warn('Element remove observer - node was moved'); // TODO consider supporting "move"
            }
        }

        handleElementRemove(removedElem);
    }
}

function addObserverToParentElement(elem) {
    const parentElem = elem.parentElement;
    if (parentElem === null) {
        return;
    }

    if (!observerByElement.has(parentElem)) {
        addObserverToParentElement(parentElem);

        const observer = new MutationObserver((mutationRecords) => handleObserverRecords(parentElem, mutationRecords));
        observer.observe(parentElem, { childList: true, characterData: false });

        observerByElement.set(parentElem, observer);
        observedChildrenByElement.set(parentElem, new Set());
    }

    if (!observedChildrenByElement.get(parentElem).has(elem)) {
        observedChildrenByElement.get(parentElem).add(elem);
        elementByObservedChild.set(elem, parentElem);
    }
}

export default {
    /**
     * @param {HTMLElement}      element
     * @param {function(): void} handler
     */
    addHandler: function (element, handler) {
        addObserverToParentElement(element);

        if (!removeHandlersByElement.has(element)) {
            removeHandlersByElement.set(element, new Set());
        }

        removeHandlersByElement.get(element).add(handler);
    },

    /**
     * @param {HTMLElement}      element
     * @param {function(): void} handler
     */
    removeHandler: function (element, handler) {
        if (!removeHandlersByElement.has(element)) {
            return;
        }

        removeHandlersByElement.get(element).delete(handler);

        removeObserverIfUnused(element);
    },

    /**
     * @param {HTMLElement} element
     */
    handleMutationQueueImmediately: function (element) { // TODO remove this method once evalJsCode() in apiService is called at least thru JS microtask
        const parentElem = elementByObservedChild.get(element);
        if (parentElem === undefined) {
            return;
        }

        handleObserverRecords(parentElem, observerByElement.get(parentElem).takeRecords());
    },
};
