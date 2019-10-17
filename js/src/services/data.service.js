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
      this.hasLocal = this.storageAvailable('localStorage');
      this.hasSession = this.storageAvailable('sessionStorage');
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

  setSessionData(item, value) {
    if (this.hasSession) {
      sessionStorage.setItem(item, value);
    } else {
      console.error('Session storage is not available in your Browser.')
    }
  }

  getSessionData(item) {
    let value = null;
    if (this.hasSession) {
      value = sessionStorage.getItem(item);
    }
    return value;
  }

  removeSessionItem(item) {
    if (this.hasSession) {
      sessionStorage.removeItem(item);
    }
  }

  setLocalData(item, value) {
    if (this.hasLocal) {
      localStorage.setItem(item, value);
    } else {
      console.error('Local storage is not available in your Browser.')
    }
  }

  getLocalData(item) {
    let value = null;
    if (this.hasLocal) {
      value = localStorage.getItem(item);
    }
    return value;
  }

  removeLocalItem(item) {
    if (this.hasLocal) {
      localStorage.removeItem(item, value);
    }
  }


}

const dataService = new DataService();
Object.freeze(dataService);

export default dataService;
