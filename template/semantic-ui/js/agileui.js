// This .js file exists only for demonstrational purposes.

function leftMenu() {
  var mobileWidth = 768,
      windowWidth = $(window).width();

  console.log(mobileWidth, windowWidth);

  if ( windowWidth < mobileWidth ) {

  }

}

$(function(){

  leftMenu();

  $('.leftMenuTrigger').click(function(){
    $('.ui.left.sidebar').toggleClass('visible');
  });

});

$(window).resize(function() {
  leftMenu();
});