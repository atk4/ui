function handleObserverRecords(mutationRecords) {
}

const observer = new MutationObserver((mutationRecords) => handleObserverRecords(mutationRecords));

export default {
    handleMutationQueueImmediately: function () { // TODO remove this method once evalJsCode() in apiService is called at least thru JS microtask
        handleObserverRecords(observer.takeRecords());
    },
};
