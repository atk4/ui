// This .js file exists only for demonstrational purposes.


/*

function leftMenu() {
  var mobileWidth = 991,
      leftMenuWidth = $('.ui.left.sidebar').outerWidth(),
      windowWidth = $(window).width()
      menuVisible = $('body').hasClass('atk-leftMenu-visible');

  console.log(mobileWidth, windowWidth, leftMenuWidth, menuVisible);

  if ( windowWidth < mobileWidth && menuVisible) {
    //$('.atk-leftMenuTrigger').click(function(){
      $('body').removeClass('atk-leftMenu-visible');
    //});
  } else if ( windowWidth >= mobileWidth && !menuVisible ) {
    //$('.atk-leftMenuClose').click();
    $('body').addClass('atk-leftMenu-visible');
  }

}
*/


$(function(){
  $('.ui.left.sidebar').prepend('<a href="javascript:void(0)" class="item atk-leftMenuClose"><i class="close icon"></i></a>');
  $('.atk-leftMenuClose').click(function(){
    $('body').removeClass('atk-leftMenu-visible');
  });

  //leftMenu();
  $('.atk-leftMenuTrigger').click(function(){
    $('.ui.left.sidebar').toggleClass('visible');
     $('body').toggleClass('atk-leftMenu-visible');
  });

});


/*
$(window).resize(function() {

  //leftMenu();

});
*/
