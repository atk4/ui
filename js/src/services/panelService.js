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
        currentVisibleId: null // the current panel id that is in a visible state.
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
        preventClosing: false,
        triggerElement: null,
        triggeredActive: {element: null, css: null},
        on: false,
        warning: {selector: params.warning.selector, trigger: params.warning.trigger},
        clearable : params.clearable,
        loader: {selector: params.loader.selector, trigger: params.loader.trigger}
      }
    };

    // add click handler.
    newPanel[params.id].$panel.on('click', params.closeSelector, function(){
      that.closePanel(params.id);
    });

    this.service.panels.push(newPanel);
  }

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

  /**
   * Open the panel.
   * Params expected the following arguments:
   *   triggered : A string or jQuery object that will triggered panel to open.
   *   activedCss: Either an object containing a jQuery selector with a css class or css class.
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
    // no id mean the panel attached to $app->layout->panel property.
    const panelId = (params.openId) ? params.openId : Object.keys(this.service.panels[0])[0];

    if (this.service.currentVisibleId && panelId !== this.service.currentVisibleId) {
      if (this.canClose(this.service.currentVisibleId)) {
        this.closePanel(this.service.currentVisibleId);
        this.doOpen(panelId, params);
      } else {
        this.closePanel(this.service.currentVisibleId);
      }
    } else {
      this.doOpen(panelId, params);
    }
  }

  /**
   * Do the actual opening.
   *
   * @param panelId
   * @param params
   */
  doOpen(panelId, params) {
    let triggerElement = params.triggered;

    if (typeof triggerElement === 'string') {
      triggerElement = $(triggerElement);
    }

    if (triggerElement.length > 0) {

      // no need to do anything if we are using the same panel.
      if (this.getPropertyValue(panelId, 'on') && this.isSameElement(panelId, triggerElement)) {
        return;
      }

      this.setTriggerElement(panelId, triggerElement, params);

      // do we need to load anything in this panel.
      if (this.getPropertyValue(panelId, 'url')) {
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

        this.reloadPanel(panelId, args);
      }
    }

    this.getPropertyValue(panelId, '$panel').addClass(this.getPropertyValue(panelId, 'visible'));
    this.setPropertyValue(panelId, 'on', true);
    this.service.currentVisibleId = panelId;
    this.addCloseEvent(panelId);
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
    if (params.activedCSS) {
      let element, css;

      if (params.activedCSS instanceof Object) {
        element = this.getPropertyValue(id, 'triggerElement').find(params.activedCSS.element);
        css = params.activedCSS.css;
      } else {
        element = trigger;
        css = params.activedCSS;
      }

      if (this.getPropertyValue(id, 'on')) {
        this.deActivated(this.getPropertyValue(id, 'triggeredActive').element, this.getPropertyValue(id, 'triggeredActive').css);
      }

      this.activated(element, css);
      const newTriggeredActive = {element:element, css:css};
      this.setPropertyValue(id, 'triggeredActive', newTriggeredActive);
    }
  }

  /**
   * Add closing event handler to mainContainerWrapper and document.
   */
  addCloseEvent(id){
    const that = this;
    $('main').on('click.atkPanel', debounce(function(evt){
      that.closePanel(id);
    }, 300));

    $(document).on('keyup.atkPanel', debounce(function(evt) {
      if (evt.keyCode === 27) {
        that.closePanel(id);
      }
    }, 300));
  }

  /**
   * Compare a  jQueryr element to the actual triggered element for this panel.
   *
   * @param el          the element to compare against.
   * @returns {boolean} True when both jQuery element are equal.
   */
  isSameElement(id, el) {
    const triggerElement = this.getPropertyValue(id, 'triggerElement');
    return (el.length == triggerElement.length && el.length == el.filter(triggerElement).length);
  }

  /**
   * Removed a css class to a jQuery element.
   * This should normally be your triggering panel element.
   *
   * @param element
   * @param css
   */
  deActivated(element, css) {
    element.removeClass(css);
  }

  /**
   * Add a css class name to a jQuery element.
   * This should normally be your triggering panel element.
   *
   * @param element
   * @param css
   */
  activated(element, css) {
    element.addClass(css);
  }

  /**
   * Close panel.
   * If modal is set and prevent closing is on then closing of panel
   * should be handle by Fomantic-UI modal onApprove function.
   */
  closePanel(id) {
    if (this.getPropertyValue(id, 'on')) {
      if (this.canClose(id)) {
        this.doClosePanel(id);
      } else {
        const $modal = $(this.getPropertyValue(id, 'modal'));
        $modal.modal('show');
      }
    }
  }

  /**
   * Check if panel can be closed, i.e.
   * it has a confirmation modal attach and warning sign is not on.
   *
   * @param id
   * @returns {boolean}
   */
  canClose(id) {
    return !(this.getPropertyValue(id, 'modal') && this.isWarningOn(id));
  }

  /**
   * Close panel and cleanup.
   *
   * @returns {PanelService|*}
   */
  doClosePanel(id) {
    //remove document event.
    $('main').off('click.atkPanel');
    $(document).off('keyup.atkPanel');

    //do the actual closing.
    this.getPropertyValue(id, '$panel').removeClass(this.getPropertyValue(id, 'visible'));
    // clean up
    if (this.getPropertyValue(id, 'on')) {
      const triggeredActive = this.getPropertyValue(id, 'triggeredActive');
      if (triggeredActive.element && triggeredActive.element.length > 0) {
        this.deActivated(triggeredActive.element, triggeredActive.css);
      }
      this.setPropertyValue(id, 'on', false);
      triggeredActive.element = null;
      triggeredActive.css = null;
      this.setPropertyValue(id, 'triggeredActive', triggeredActive);
      this.setPropertyValue(id, 'triggerElement', null);

    }

    return this.instance;
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

  /**
   * Load panel content.
   *
   * @param args
   */
  reloadPanel(id, args) {
    const loader = this.getPropertyValue(id, 'loader');
    this.clearPanelContent(id);
    const $panel = this.getPropertyValue(id, '$panel');
    const url = this.getPropertyValue(id, 'url');
    // const context = panel.find('.atk-panelLoader');
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
}

const panelService = new PanelService();
Object.freeze(panelService);

export default panelService;
