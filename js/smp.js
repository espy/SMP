/*

Störmer Murphy and Partners

Alex Feyerke for We Are Fellows, 2012

 */

var lazyBGImage;

$(window).ready(function() {
  lazyBGImage = _.debounce(showBGImage, 100);
  initListeners();
});

$(window).load(function() {
  redrawLayout();
});

function initListeners() {
  // lazy load some images, like the static google map
  $('img.lazy').each(function(){
    $(this).attr('src', $(this).data('src'));
  });
  $('ul.projectList:not(.index) li:not(.listTitle)').hover(
    // mouse in
    function(event){
      // white column background
      $(this).closest('ul').addClass('hover');

      event.preventDefault();
      var img = $(this).data('image');
      if($('#backgroundImage img').attr('src') == img) return;

      $('#backgroundImage').empty().append('<img src="">');
      $('#backgroundImage').css('visibility', 'hidden');
      $('#backgroundImage').waitForImages(function() {
        lazyBGImage();
      });
      $('#backgroundImage img').attr('src', img);
    },
    // mouse out
    function(event){
      $(this).closest('ul').removeClass('hover');
      $('#backgroundImage').empty();
    }
  );
  $('.viewport').scroll(function(){
    if($('.projectSlider').offset().top <= 10){
      $('.projectSlider').addClass('fixedProjectSliderHeader');
    } else {
      $('.projectSlider').removeClass('fixedProjectSliderHeader');
    }
  });
  $('.projectSlider .header').click(function(){
    if($(this).parent().hasClass('fixedProjectSliderHeader')){
      // Header is at top and will scroll down
      //$('.projectSlider').offset().top =
      $('.viewport').animate({scrollTop:0}, 200);
    } else {
      // Header is at bottom and will scroll up
      var target = $('.viewport').height()-55;
      $('.viewport').animate({scrollTop:target}, 200);
    }
  });
  $(window).resize(function(){
    redrawLayout();
  });
  redrawLayout();
}

function showBGImage() {
  if($('#backgroundImage img').width() && $('#backgroundImage img').width() !== 0){
    scaleBGImage();
    $('#backgroundImage').fadeOut(0);
    $('#backgroundImage').css('visibility', 'visible');
    $('#backgroundImage').fadeIn(250);
  } else {
    _.delay(function(){
      lazyBGImage();
    }, 100);
  }
}

function checkIfBackgroundImageExists() {
  console.log("checkIfBackgroundImageExists");
  console.log("has img", $('#backgroundImage img').width());
  if($('#backgroundImage img').width() && $('#backgroundImage img').width() !== 0){
    scaleBGImage();
    //$('#backgroundImage').fadeOut(0);
    //$('#backgroundImage').css('visibility', 'visible');
    //$('#backgroundImage').fadeIn(250);
  }
}

function redrawLayout() {
  console.log("redrawLayout: ");
  // make project lists full height
  var viewportHeight = $(window).height() - 50;
  var targetHeight = viewportHeight;
  console.log($(this).height());
  $('ul.projectList').each(function(){
    if(!$(this).data('original-height')){
      $(this).data('original-height', $(this).height());
    }
    if($(this).data('original-height') > targetHeight){
      targetHeight = $(this).height();
    }
  });
  $('.viewport').height(viewportHeight);
  $('ul.projectList:not(index)').height(targetHeight + 70);
  $('ul.projectList.index').height(targetHeight);
  scaleBGImage();
  $('.projectSlider').css('margin-top', $(window).height() - 83 - $('.projectSlider h1').height());
}

function scaleBGImage() {
  var bgImage = $('#backgroundImage img')[0];
  console.log("bgImage: ",bgImage);
  var dimensions = getFitAroundSizes($('#backgroundImage img'), 1115, $(window).height());
  console.log("dimensions: ",dimensions);
  $('#backgroundImage img').width(dimensions[0]);
  $('#backgroundImage img').height(dimensions[1]);
}

/*

-------------------------- HELPERS --------------------------

*/


// returns optimal sizes for an element that needs to fit around
// something while maintaining aspect ratio

function getFitAroundSizes(element, targetWidth, targetHeight){
  var ratio = 1;
  var widthRatio = 1;
  var heightRatio = 1;
  var objectWidth;
  var objectHeight;
  if($(element).data('original-width')){
    objectWidth = $(element).data('original-width');
    objectHeight = $(element).data('original-height');
  } else {
    objectWidth = $(element).width();
    objectHeight = $(element).height();
    $(element).data('original-width', objectWidth);
    $(element).data('original-height', objectHeight);
  }
  widthRatio = targetWidth / objectWidth;
  heightRatio = targetHeight / objectHeight;
  if (widthRatio > heightRatio) {
    ratio = widthRatio;
  } else {
    ratio = heightRatio;
  }
  return [objectWidth * ratio, objectHeight * ratio];
}