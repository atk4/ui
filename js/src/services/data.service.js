/**
 * Save and Retrieve Session or Local Web storage data.
 */
class DataService {
    constructor() {
        this.hasStorage = this.storageAvailable('localStorage') && this.storageAvailable('sessionStorage');
        this.storage = { session: sessionStorage, local: localStorage };
    }

    /**
     * Check if storage is available.
     *
     * @returns {boolean|*}
     */
    storageAvailable(type) {
        let storage;
        try {
            storage = window[type];
            const x = '__storage_test__';
            storage.setItem(x, x);
            storage.removeItem(x);

            return true;
        } catch (e) {
            return e instanceof DOMException
                && (
                    e.code === 22 // everything except Firefox
                    || e.code === 1014 // Firefox
                    || e.name === 'QuotaExceededError' // test name field too, because code might not be present, everything except Firefox
                    || e.name === 'NS_ERROR_DOM_QUOTA_REACHED' // Firefox
                )
                // acknowledge QuotaExceededError only if there's something already stored
                && (storage && storage.length > 0);
        }
    }

    /**
     * Check for valid JSON string.
     *
     * @returns {boolean}
     */
    isJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            console.error('JSON string parse failed: ' + e.message);

            return false;
        }

        return true;
    }

    /**
     * Set Item data value to local or web storage.
     * The item is the key associated with the data value in web or local storage.
     * Will add item value or replace it if already exist.
     */
    setData(item, value, type = 'local') {
        if (this.hasStorage) {
            this.storage[type].setItem(item, value);
        } else {
            console.error('Session storage is not available in your browser');
        }
    }

    /**
     * Get data value using an item as key.
     */
    getData(item, type = 'local') {
        let value = null;
        if (this.hasStorage) {
            value = this.storage[type].getItem(item);
        }

        return value;
    }

    /**
     * Clear associated data using item as key.
     */
    clearData(item, type = 'local') {
        if (this.hasStorage) {
            this.storage[type].removeItem(item);
        }
    }

    /**
     * Return store data for an item or empty object.
     *
     * @returns {{ session: *, local: * }}
     */
    getStoreData(name) {
        const store = {};
        if (name) {
            const localData = this.getData(name, 'local');
            if (localData) {
                store[name + '_local_store'] = localData;
            }
            const sessionData = this.getData(name, 'session');
            if (sessionData) {
                store[name + '_session_store'] = sessionData;
            }
        }

        return store;
    }

    /**
     * Similar to set data but make sure that value is
     * a valid JSON string prior to set data.
     */
    setJsonData(item, value, type = 'local') {
        if (!this.isJsonString(value)) {
            return;
        }
        this.setData(item, value, type);
    }

    /**
     * Will either create or merge with existing data.
     * Merging is done with Object assign, prioritizing new value.
     * Previous data, if exist, and value must be a valid JSON string.
     */
    addJsonData(item, value, type = 'local') {
        const previous = this.getData(item, type);
        if (!this.isJsonString(value) || !this.isJsonString(previous)) {
            return;
        }

        if (previous) {
            this.setData(item, JSON.stringify(Object.assign(JSON.parse(previous), JSON.parse(value))), type);
        } else {
            this.setData(item, value, type);
        }
    }
}

export default Object.freeze(new DataService());
