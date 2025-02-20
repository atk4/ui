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
function handlePossibleModalReloadKeepState(elem, elemOrig) {
    if ((elemOrig.classList.contains('ui') && elemOrig.classList.contains('modal')) || elemOrig.classList.contains('atk-right-panel')) {
        for (const node of [...elemOrig.childNodes]) { // eslint-disable-line unicorn/no-useless-spread
            if (node instanceof Element && node.classList.contains('ui') && node.classList.contains('dimmer')) {
                continue;
            }

            node.remove();
        }
        for (const node of [...elem.childNodes]) { // eslint-disable-line unicorn/no-useless-spread
            if (node instanceof Element && node.classList.contains('ui') && node.classList.contains('dimmer')) {
                continue;
            }

            elemOrig.append(node);
        }

        elem.replaceWith(elemOrig); // TODO remove this hack
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

    for (const elem of elems) {
        if (elem.id && elem.isConnected) {
            let elemOrig = null;
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

            if (elemOrig) {
                handlePossibleModalReloadKeepState(elem, elemOrig);
            }
        }
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
