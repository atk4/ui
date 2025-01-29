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
    if (parentElem === null || !observerByElement.has(parentElem)) {
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

function addObserverToParentElement(elem) {
    const parentElem = elem.parentElement;
    if (parentElem === null) {
        return;
    }

    if (!observerByElement.has(parentElem)) {
        addObserverToParentElement(parentElem);

        const observer = new MutationObserver((mutationRecords) => {
            for (const mutationRecord of mutationRecords) {
                if (mutationRecord.removedNodes.length > 0) {
                    const removedNodes = observedChildrenByElement.get(parentElem).intersection(mutationRecord.removedNodes);
                    for (const removedNode of removedNodes) {
                        handleElementRemove(removedNode);
                    }
                }
            }
        });
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
     * @param {HTMLElement} element
     */
    addHandler: function (element, handler) {
        addObserverToParentElement(element);

        if (!removeHandlersByElement.has(element)) {
            removeHandlersByElement.set(element, new Set());
        }

        removeHandlersByElement.get(element).add(handler);
    },

    /**
     * @param {HTMLElement} element
     */
    removeHandler: function (element, handler) {
        removeHandlersByElement.get(element).delete(handler);

        removeObserverIfUnused(element);
    },
};
