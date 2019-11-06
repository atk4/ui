import throttle from 'lodash.throttle';

/**
 * Simple helper to help displaying Fomantic-ui Dropdown within an atk table.
 * Because atk table use overflow: scroll, Dropdown is not
 * display on top of table.
 *
 * This utility will properly set css style for dropdown menu to be display correctly.
 *
 */
function showTableDropdown() {
  // getting element composing dropdown.
  const $that = $(this);
  const $menu = $(this).find('.menu');
  const position  =  $that.offset();
  const hasFloating = $that.hasClass('floating');

  /**
   * Check if menu fit below button.
   *
   * @returns {boolean}
   */
  function hasPlaceBelow() {
    return $menu.outerHeight() < $(window).height() - $that[0].getBoundingClientRect().bottom;
  }

  /**
   * Set menu style for displaying at right position.
   */
  function setCssPosition() {
    // console.log(position.top, $that.scrollTop());
    let top, left;
    // check if we need to place menu above or down button.
    if (hasPlaceBelow()) {
      top = position.top + $that.outerHeight();
      top = hasFloating ? top + 5 : top;
    } else {
      top = position.top - $menu.height();
      top = hasFloating ? top - 5 : top;
    }
    top = top - $(window).scrollTop();
    left = position.left;

    const style = `position: fixed; z-index: 12; top: 0px; margin-top: ${top}px !important; left: ${left}px !important; width: fit-content !important; min-width: 0`;
    $menu.css('cssText', style);
  }

  setCssPosition();
  $(window).on('scroll.atktable', throttle(setCssPosition, 10));
  $(window).on('resize.atktable', function(){
    $that.dropdown('hide');
  });
}

/**
 * Reset css and handler when hiding dropdown.
 */
function hideTableDropdown() {
  // reset positioning.
  const $menu = $(this).find('.menu');
  $menu.css('cssText', '');
  $(window).off('scroll.atktable');
  $(window).off('resize.atktable');
}

// Export function to atk.
export const tableDropdown = {
  onShow : showTableDropdown,
  onHide : hideTableDropdown,
};
