/**
 * Save and Retrieve Session or Local Web storage data.
 *
 */
class DataService {

  static getInstance() {
    return this.instance;
  }

  constructor() {
    if (!this.instance) {
      this.instance = this;
      this.hasStorage = this.storageAvailable('localStorage') && this.storageAvailable('sessionStorage');
      this.hasLocal = this.storageAvailable('localStorage');
      this.hasSession = this.storageAvailable('sessionStorage');
      this.storage  = {session: sessionStorage, local: localStorage};
    }
    return this.instance;
  }

  /**
   * Check if storage is available.
   *
   * @param type
   * @returns {boolean|boolean|*}
   */
  storageAvailable(type) {
    let storage;
    try {
      storage = window[type];
      const x = '__storage_test__';
      storage.setItem(x, x);
      storage.removeItem(x);
      return true;
    }
    catch(e) {
      return e instanceof DOMException && (
          // everything except Firefox
        e.code === 22 ||
        // Firefox
        e.code === 1014 ||
        // test name field too, because code might not be present
        // everything except Firefox
        e.name === 'QuotaExceededError' ||
        // Firefox
        e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
        // acknowledge QuotaExceededError only if there's something already stored
        (storage && storage.length !== 0);
    }
  }

  /**
   * Check for valide json string.
   * @param str
   * @returns {boolean}
   */
  isJsonString(str) {
    try {
      JSON.parse(str);
    } catch (e) {
      console.error('Invalid json strign supply.');
      return false;
    }
    return true;
  }

  setData(item, value, type = 'local') {
      if (this.hasStorage) {
        this.storage[type].setItem(item, value);
      }  else {
        console.error('Session storage is not available in your Browser.')
      }
  }

  setJsonData(item, value, type = 'local') {
    if (!this.isJsonString(value)) {
      return;
    }

    if (this.hasStorage) {
      this.storage[type].setItem(item, value);
    }  else {
      console.error('Session storage is not available in your Browser.')
    }
  }

  addJsonData(item, value, type = 'local') {
    if (!this.isJsonString(value)) {
      return;
    }

    if (this.hasStorage) {
      let previousData = JSON.parse(this.storage[type].getItem(item));
      if (previousData) {
        this.storage[type].setItem(item, JSON.stringify(Object.assign(previousData, JSON.parse(value))));
      } else {
        this.storage[type].setItem(item, value);
      }
    } else {
      console.error('Session storage is not available in your Browser.')
    }
  }

  getData(item, type = 'local') {
    let value = null;
    if (this.hasStorage) {
      value = this.storage[type].getItem(item);
    }
    return value;
  }

  clearData(item, type = 'local') {
    this.storage[type].removeItem(item);
  }

}

const dataService = new DataService();
Object.freeze(dataService);

export default dataService;
