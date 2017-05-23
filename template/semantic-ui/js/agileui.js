// This .js file exists only for demonstrational purposes.

function leftMenu() {
  var mobileWidth = 991,
      leftMenuWidth = $('.ui.left.sidebar').outerWidth(),
      windowWidth = $(window).width();

  console.log(mobileWidth, windowWidth, leftMenuWidth);

  $('.ui.left.sidebar').prepend('<a href="javascript:void(0)" class="item atk-leftMenuClose"><i class="close icon"></i></a>');
  $('.atk-leftMenuClose').click(function(){
    $('body').removeClass('atk-leftMenu-visible');
  });

  if ( windowWidth < mobileWidth ) {
    $('.atk-leftMenuTrigger').click(function(){
      $('body').toggleClass('atk-leftMenu-visible');
    });
  } else if ( windowWidth > mobileWidth && $('body.atk-leftMenu-visible').length ) {
    $('body').removeClass('atk-leftMenu-visible');
  }

}


$(function(){

  leftMenu();
  $('.atk-leftMenuTrigger').click(function(){
    $('.ui.left.sidebar').toggleClass('visible');
  });

});


$(window).resize(function() {

  leftMenu();

});