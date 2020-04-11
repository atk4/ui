import $ from "jquery";
import debounce from 'debounce';

/**
 * Singleton class.
 * Panel needs to be reload to display different
 * content. This service will take care of this.
 *
 */
class PanelService {

  static getInstance() {
    return this.instance;
  }

  constructor() {
    if (!this.instance) {
      this.instance = this;
      this.service = {
        panels: [], // a collection of panels.
        currentVisibleId: null, // the current panel id that is in a visible state.
        currentParams: null, // url argument of the current panel.
      }
    }
    return this.instance;
  }

  /**
   * Add a panel to this service and
   * initial panel setup.
   *
   * Atk4/ui callback may call this on each callback so
   * we need to make sure it is not add multiple time.
   *
   * @param params
   */
  addPanel(params)
  {
    const that = this;

    // don't add if already there.
    if (this.getPropertyValue(params.id, 'id')) {
      return;
    }

    const newPanel = {
      [params.id] : {
        id: params.id,
        $panel: $('#' + params.id),
        visible: params.visible,
        closeSelector: params.closeSelector,
        url: params.url,
        modal: params.modal,
        triggerElement: null,
        triggeredActive: {element: null, css: null},
        warning: {selector: params.warning.selector, trigger: params.warning.trigger},
        clearable : params.clearable,
        loader: {selector: params.loader.selector, trigger: params.loader.trigger},
        hasClickAway: params.hasClickAway,
        modalAction: null,
      }
    };

    // add click handler.
    newPanel[params.id].$panel.on('click', params.closeSelector, function(){
      that.closePanel(params.id);
    });

    this.service.panels.push(newPanel);
  }

  /**
   * Open the panel.
   * Params expected the following arguments:
   *   triggered : A string or jQuery object that will triggered panel to open.
   *   activeCss: Either an object containing a jQuery selector with a css class or css class.
   *    As an Object:  element: the jQuery selector within the triggered element;
   *                   css:     the css class to applying to the triggered element when panel is open.
   *
   *    As a css class: the css class to applied to the triggered element when panel open.
   *
   * @param params              The params objects.
   * @returns {PanelService|*}
   */
  openPanel(params) {
    // if no id is provide, then get the first one.
    // no id mean the first panel in list.
    const panelId = (params.openId) ? params.openId : Object.keys(this.service.panels[0])[0];
    // save our open param.
    this.service.currentParams = params;
    if (this.isSameElement(panelId, params.triggered)) {
      return;
    }
    // first check if current panel can be click away.
    if (this.service.currentVisibleId && !this.getPropertyValue(this.service.currentVisibleId, 'hasClickAway')) {
      return;
    }
    this.initOpen(panelId);

  }

  /**
   * Will check if panel can open or reload.
   *
   * @param id
   */
  initOpen(id) {
    const that = this;
    if (this.service.currentVisibleId && id !== this.service.currentVisibleId) {
      // trying to open a different panel so close current one if allowed.
      if (this.needConfirmation(this.service.currentVisibleId)) {
        // need to ask user
        const $modal = $(this.getPropertyValue(this.service.currentVisibleId, 'modal'));
        $modal.modal('setting', 'onApprove', function(e) {
          that.doClosePanel(id);
        });
        $modal.modal('show');
      } else {
        this.doClosePanel(this.service.currentVisibleId);
        this.doOpenPanel(id);
        this.initPanelReload(id);
      }
    } else if (this.service.currentVisibleId === id) {
      // current panel already open try to reload new content
      if (this.needConfirmation(id)) {
        const $modal = $(this.getPropertyValue(id, 'modal'));
        $modal.modal('setting', 'onApprove', function(e) {
          that.doOpenPanel(id);
          that.initPanelReload(id);
        });
        $modal.modal('show');
      } else {
        this.doOpenPanel(id);
        this.initPanelReload(id);
      }
    } else {
      this.doOpenPanel(id);
      this.initPanelReload(id);
    }
  }

  /**
   * Will check if panel is reloadable and
   * setup proper url argument from triggered item
   * via it's data property.
   *
   * @param id
   */
  initPanelReload(id)
  {
    const params = this.service.currentParams;
    // do we need to load anything in this panel.
    if (this.getPropertyValue(id, 'url')) {
      //Convert our array of args to object.
      //Args must be defined as data-attributeName in the triggered element.
      const args = params.reloadArgs.reduce( (obj, item) => {
        obj[item] = params.triggered.data(item);
        return obj;
      }, {});
      //add url argument if pass to panel
      if (params.urlArgs !== 'undefined') {
        $.extend(args, params.urlArgs);
      }
      this.doReloadPanel(id, args);
    }
  }

  /**
   * Do the actual opening.
   *
   * @param panelId
   * @param params
   */
  doOpenPanel(panelId) {
    const params = this.service.currentParams;

    let triggerElement = params.triggered;

    if (typeof triggerElement === 'string') {
      triggerElement = $(triggerElement);
    }

    // will apply css class to triggering element if provide.
    if (triggerElement.length > 0) {
      // no need to do anything if we are using the same panel.
      // if (this.getPropertyValue(panelId, 'on') && this.isSameElement(panelId, triggerElement)) {
      //   return;
      // }

      this.setTriggerElement(panelId, triggerElement, params);
    }

    this.getPropertyValue(panelId, '$panel').addClass(this.getPropertyValue(panelId, 'visible'));
    this.service.currentVisibleId = panelId;
    if (this.getPropertyValue(panelId, 'hasClickAway')) {
      this.addClickAwayEvent(panelId);
    }
  }

  /**
   * Close panel.
   * if confirmation is needed, will ask user.
   */
  closePanel(id) {
    const that = this;
    if (this.needConfirmation(id)) {
      const $modal = $(this.getPropertyValue(id, 'modal'));
      $modal.modal('setting', 'onApprove', function(e) {
        that.doClosePanel(id);
      }).modal('show');
    } else {
      this.doClosePanel(id);
    }
  }

  /**
   * Close panel and cleanup.
   *
   * @returns {PanelService|*}
   */
  doClosePanel(id) {
    //remove document event.
    this.removeClickAwayEvent();
    this.removeWarning(id);

    //do the actual closing.
    this.getPropertyValue(id, '$panel').removeClass(this.getPropertyValue(id, 'visible'));
    this.service.currentVisibleId = null;

    // clean up
    const triggeredActive = this.getPropertyValue(id, 'triggeredActive');
    if (triggeredActive.element && triggeredActive.element.length > 0) {
      this.deActivated(triggeredActive.element, triggeredActive.css);
    }
    triggeredActive.element = null;
    triggeredActive.css = null;
    this.setPropertyValue(id, 'triggeredActive', triggeredActive);
    this.setPropertyValue(id, 'triggerElement', null);
  }

  /**
   * Load panel content.
   *
   * @param args
   */
  doReloadPanel(id, args) {
    const loader = this.getPropertyValue(id, 'loader');
    const $panel = this.getPropertyValue(id, '$panel');
    const url = this.getPropertyValue(id, 'url');

    // do some cleanup.
    this.removeWarning(id);
    this.clearPanelContent(id);

    $panel.find(loader.selector).addClass(loader.trigger);
    $panel.api({
      on: 'now',
      url: url,
      data: args,
      method: 'GET',
      stateContext: null,
      onComplete: function(r,s) {
        $panel.find(loader.selector).removeClass(loader.trigger);
      }
    });
  }

  /**
   * Set triggering element that fire the panel to open.
   * If panel is open by html element, you can specified class on these
   * elements that will be add or remove, depending on the panel state.
   * Thus, creating a visual onto which html element has fire the event.
   *
   * @param id
   * @param trigger
   * @param params
   */
  setTriggerElement(id, trigger, params) {
    this.setPropertyValue(id, 'triggerElement', trigger);

    //Do we need to setup css class on triggering element.
    if (params.activeCSS) {
      let element, css;

      if (params.activeCSS instanceof Object) {
        element = this.getPropertyValue(id, 'triggerElement').find(params.activeCSS.element);
        css = params.activeCSS.css;
      } else {
        element = trigger;
        css = params.activeCSS;
      }

      this.deActivated(this.getPropertyValue(id, 'triggeredActive').element, this.getPropertyValue(id, 'triggeredActive').css);

      this.activated(element, css);
      const newTriggeredActive = {element:element, css:css};
      this.setPropertyValue(id, 'triggeredActive', newTriggeredActive);
    }
  }

  /**
   * Add click away and esc closing event handler.
   */
  addClickAwayEvent(id){
    const that = this;
    // clicking anywhere in main tag will close panel.
    $('main').on('click.atkPanel', debounce(function(evt){
      that.closePanel(id);
    }, 300));

    // pressing esc key will close panel.
    $(document).on('keyup.atkPanel', debounce(function(evt) {
      if (evt.keyCode === 27) {
        that.closePanel(id);
      }
    }, 300));
  }

  /**
   * Remove click away and esc events.
   */
  removeClickAwayEvent() {
    $('main').off('click.atkPanel');
    $(document).off('keyup.atkPanel');
  }
  /**
   * Compare a  jQuery element to the actual triggered element for this panel.
   *
   * @param el          the element to compare against.
   * @returns {boolean} True when both jQuery element are equal.
   */
  isSameElement(id, el) {
    const triggerElement = this.getPropertyValue(id, 'triggerElement');
    let isSame = false;
    if (el && triggerElement) {
      isSame = (el.length == triggerElement.length && el.length == el.filter(triggerElement).length);
    }
    return isSame;
  }

  /**
   * Removed a css class to a jQuery element.
   * This should normally be your triggering panel element.
   *
   * @param element
   * @param css
   */
  deActivated(element, css) {
    if (element) {
      element.removeClass(css);
    }
  }

  /**
   * Add a css class name to a jQuery element.
   * This should normally be your triggering panel element.
   *
   * @param element
   * @param css
   */
  activated(element, css) {
    if (element) {
      element.addClass(css);
    }
  }

  /**
   * Check if Warning sign is on.
   *
   * @param id
   * @returns {boolean}
   */
  isWarningOn(id) {
    const $panel = this.getPropertyValue(id, '$panel');
    const warning = this.getPropertyValue(id, 'warning');

    return $panel.find(warning.selector).hasClass(warning.trigger);
  }


  removeWarning(id) {
    const $panel = this.getPropertyValue(id, '$panel');
    const warning = this.getPropertyValue(id, 'warning');

    return $panel.find(warning.selector).removeClass(warning.trigger);
  }

  /**
   * Check if panel can be closed, i.e.
   * it has a confirmation modal attach and warning sign is not on.
   *
   * @param id
   * @returns {boolean}
   */
  needConfirmation(id) {
    return (this.getPropertyValue(id, 'modal') && this.isWarningOn(id));
  }

  /**
   * Clear content.
   */
  clearPanelContent(id) {
    const $panel = this.getPropertyValue(id, '$panel');
    const clearables = this.getPropertyValue(id, 'clearable');
    clearables.forEach(clearable => {
      $panel.find(clearable).html('');
    });
  }

  /*********** UTILITIES ****************/

  /**
   * Set a property value for a panel designated by id.
   *
   * @param id    the id of the panel to set property too.
   * @param prop  the property inside panel
   * @param value the value.
   */
  setPropertyValue(id, prop, value)
  {
    this.service.panels.forEach(panel => {
      if (panel[id]) {
        panel[id][prop] = value;
      }
    });
  }

  /**
   * Return the panel property represent by id in collections.
   * if prop is not specify, then it will return the entire panel object.
   *
   * @param id
   * @param prop
   * @returns {*|jQuery|HTMLElement}
   */
  getPropertyValue(id, prop = null) {
    let value = null;
    this.service.panels.forEach(panel => {
      if (panel[id]) {
        value = prop ? panel[id][prop] : panel[id];
      }
    });

    return value;
  }
}

const panelService = new PanelService();
Object.freeze(panelService);

export default panelService;
