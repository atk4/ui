const attributeToName = 'data-atk4-teleport-to';
const attributeFromIdName = 'data-atk4-teleport-from-id';

function handleElementsTeleport(elems) {
    const teleportTargets = new Map();
    for (const elem of elems) {
        const teleportTo = elem.getAttribute(attributeToName);
        if (!teleportTo || !elem.isConnected) {
            continue;
        }

        if (!teleportTargets.has(teleportTo)) {
            const targets = document.querySelectorAll(teleportTo);
            if (targets.length !== 1) {
                throw new Error('Target DOM element not found');
            }

            teleportTargets.set(teleportTo, targets[0]);
        }

        const target = teleportTargets.get(teleportTo);
        if (elem.parentElement === target) {
            continue;
        }

        const elemId = elem.id;
        if (!elemId) {
            throw new Error('DOM element ID is required');
        }

        elem.setAttribute(attributeFromIdName, elem.parentElement.id);

        for (const elemOrig of target.querySelectorAll(':scope > *[id="' + CSS.escape(elemId) + '"]')) {
            elemOrig.remove();
        }

        target.append(elem);

        elem.removeAttribute(attributeToName);
    }
}

// needed for example for /demos/data-action/jsactions2.php and "Argument/Preview" action
function handlePossibleModalReloadKeepOriginalDimmer(elem, getOrigElementFx) {
    if ((elem.classList.contains('ui') && elem.classList.contains('modal')) || elem.classList.contains('atk-right-panel')) {
        const elemOrig = getOrigElementFx();
        if (elemOrig === null) {
            return;
        }

        // TODO remove this hack
        // https://github.com/fomantic/Fomantic-UI/issues/3176
        elemOrig.replaceChildren(...elem.childNodes);
        elem.replaceWith(elemOrig);
    }
}

function handleObserverRecords(mutationRecords) {
    const elems = new Set();
    for (const mutationRecord of mutationRecords) {
        for (const addedNode of mutationRecord.addedNodes) {
            if (addedNode instanceof Element) {
                if (addedNode.matches('*[' + attributeToName + ']')) {
                    elems.add(addedNode);
                }
                for (const elem of addedNode.querySelectorAll('*[' + attributeToName + ']')) {
                    elems.add(elem);
                }
            }
        }
        if (mutationRecord.type === 'attributes') {
            elems.add(mutationRecord.target);
        }
    }

    const getOrigElementFx = (elem) => {
        let elemOrig = null;
        if (elem.id && elem.isConnected) {
            for (const mutationRecord of mutationRecords) {
                for (const removedNode of mutationRecord.removedNodes) {
                    if (removedNode instanceof Element) {
                        if (removedNode.matches('#' + CSS.escape(elem.id)) && !removedNode.isConnected) {
                            elemOrig = removedNode;

                            continue;
                        }
                        for (const elem2 of removedNode.querySelectorAll('#' + CSS.escape(elem.id))) {
                            if (!elem2.isConnected) {
                                elemOrig = elem2;

                                break;
                            }
                        }
                    }
                }
            }
        }

        return elemOrig;
    };

    for (const elem of elems) {
        handlePossibleModalReloadKeepOriginalDimmer(elem, () => getOrigElementFx(elem));
    }

    handleElementsTeleport(elems);
}

const observer = new MutationObserver((mutationRecords) => handleObserverRecords(mutationRecords));
observer.observe(document, { subtree: true, childList: true, attributeFilter: [attributeToName] });

handleElementsTeleport(document.querySelectorAll('*[' + attributeToName + ']'));

export default {
    handleMutationQueueImmediately: function () { // TODO remove this method once evalJsCode() in apiService is called at least thru JS microtask
        handleObserverRecords(observer.takeRecords());
    },
};
