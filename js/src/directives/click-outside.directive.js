/**
 * Vue directive for handling click
 * outside a component. You can specify other components
 * outside of the component using the directive via an array where the directive will not be apply.
 * Example a button use for opening a popup. Exclude is indicate
 * via a reference name.
 *
 * <button ref="button" @click="showPopup = !showPopup" > OPEN Popup </button>
 *
 * Example usage:
 * <div
 * v-show="showPopup"
 * v-closable="{
 *   exclude: ['button'], //using button ref name.
 *   handler: 'onClose'   // an onClose method on the component using the directive.
 * }"
 * </div>
 */
let handleOutsideClick;

export default {
  bind (el, binding, vnode) {
    // the click/touchstart handler
    handleOutsideClick = (e) => {
      e.stopPropagation();
      // Get the handler method name and the exclude array
      // from the object used in v-closable
      const { handler, exclude } = binding.value;
      // This variable indicates if the clicked element is excluded
      let clickedOnExcludedEl = false;
      exclude.forEach(refName => {
        // We only run this code if we haven't detected
        // any excluded element yet
        if (!clickedOnExcludedEl) {
          // Get the element using the reference name
          const excludedEl = vnode.context.$refs[refName];
          // See if this excluded element
          // is the same element the user just clicked on
          clickedOnExcludedEl = excludedEl.contains(e.target)
        }
      });
      // We check to see if the clicked element is not
      // the component element and not excluded
      if (!el.contains(e.target) && !clickedOnExcludedEl) {
        // If the clicked element is outside the component && one of the exclude element.
        vnode.context[handler](e)
      }
    };
    // Register click/touchstart event listeners on the whole page
    document.addEventListener('click', handleOutsideClick);
    document.addEventListener('touchstart', handleOutsideClick);
  },
  unbind () {
    // If the element that has v-closable is removed, then
    // unbind click/touchstart listeners from the whole page
    document.removeEventListener('click', handleOutsideClick);
    document.removeEventListener('touchstart', handleOutsideClick);
  }
}
