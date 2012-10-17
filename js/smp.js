/*

Störmer Murphy and Partners

Alex Feyerke for We Are Fellows, 2012

 */

$(window).ready(function() {
  initListeners();
});

function initListeners() {
  $('ul.projectList li:not(.listTitle)').hover(function(event){
    event.preventDefault();
    var img = $(this).data('image');
    if($('#backgroundImage img').attr('src') == img) return;

    $('#backgroundImage').empty().append('<img src="">');
    $('#backgroundImage').css('visibility', 'hidden');
    $('#backgroundImage').waitForImages(function() {
      _.delay(function(){
        scaleBGImage();
        $('#backgroundImage').fadeOut(0);
        $('#backgroundImage').css('visibility', 'visible');
        $('#backgroundImage').fadeIn(250);
        }, 100);
    });

    $('#backgroundImage img').attr('src', img);
  });
  $(window).resize(function(){
    redrawLayout();
  });
  redrawLayout();
}

function redrawLayout() {
  // make project lists full height
  var targetHeight = $(window).height() - 10;
  $('ul.projectList').each(function(){
    if($(this).height() > targetHeight){
      targetHeight = $(this).height();
    }
  });
  $('.viewport').height($(window).height());
  $('ul.projectList').height(targetHeight);
  scaleBGImage();
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