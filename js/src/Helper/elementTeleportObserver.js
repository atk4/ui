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
