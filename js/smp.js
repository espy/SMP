/*

Störmer Murphy and Partners

Alex Feyerke for We Are Fellows, 2012

 */

var lazyBGImage;
var lastSliderPosition;
var hashes;

$(window).ready(function() {
  lazyBGImage = _.debounce(showBGImage, 100);
  initListeners();
});

$(window).load(function() {
  // make everything invisible, layout it, then fade in
  $('body').fadeOut().css('display', 'block');
  redrawLayout();
  $('body').fadeIn(250);
  if(scrollToSection){
    var target = $('.'+scrollToSection).offset().top - 85;
    $(document.body).animate({scrollTop: target}, 200);
  }
});

function initListeners() {
  // add history listener
  getHashes();
  $(window).bind('statechange',function(data){
    getHashes();
    updateProjectGalleryNavigation();
    updateGallery();
  });
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
      setBackgroundImage(img);
    },
    // mouse out
    function(event){
      $(this).closest('ul').removeClass('hover');
      $('#backgroundImage').empty();
    }
  );
  $(window).scroll(function(){
    if($(window).scrollTop() >= $(window).height() - 106){
      $('.projectSlider').addClass('fixedProjectSliderHeader');
    } else {
      $('.projectSlider').removeClass('fixedProjectSliderHeader');
    }
  });
  // Mouse over slider header: slide in to last position
  $('.projectDescription').mouseenter(function(){
    // only show if mouse is still in column after delay
    var descriptionDelay = setTimeout(function(){
        openSlider();
    }, 250);
    $(this).data('descriptionDelay', descriptionDelay);
  });
  // mouse out of slider: slide to bottom
  $('.projectDescription').mouseleave(function(){
    // clear the delay if the mouse leaves the center column before the timeout
    clearTimeout($(this).data('descriptionDelay'));
    closeSlider();
  });
  $('a.next, a.previous').click(function(event){
    event.preventDefault();
    History.pushState(null, null, $(this).attr('href'));
  });
  $(document).keydown(function(e){
    switch(e.which) {
      case 37: // left
        $('a.previous')[0].click();
      break;
      case 38: // up
        if($('.projectDescription')){
          openSlider();
        }
      break;
      case 39: // right
        if($('.nextProject.lastImageInGallery').length !== 0){
          $('.nextProject.lastImageInGallery')[0].click();
        } else {
          $('a.next')[0].click();
        }
      break;
      case 40: // down
        if($('.projectDescription')){
          closeSlider();
        }
      break;
      default: return; // exit this handler for other keys
    }
    e.preventDefault();
  });
  $('a.anchor').click(function(event){
    event.preventDefault();
    var target = $('.'+$(this).data('anchor')).offset().top - 85;
    $(document.body).animate({scrollTop: target}, 200);
    $(this).closest('ul').children('li').removeClass('current_page_item');
    $(this).closest('li').addClass('current_page_item');

  });
  $(window).resize(function(){
    redrawLayout();
  });
  redrawLayout();
}

function openSlider() {
  if($(window).scrollTop() === 0){
    var target = $(window).height() - 106;
    if(lastSliderPosition){
      target = lastSliderPosition;
    }
    $(document.body).animate({scrollTop: target}, 200);
  }
}

function closeSlider(){
  if($(window).scrollTop() > 0){
    if($(window).scrollTop() > $(window).height()-126){
      lastSliderPosition = $(window).scrollTop();
    } else {
      lastSliderPosition = 0;
    }
    $(document.body).animate({scrollTop: 0}, 200);
  }
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
  if($('#backgroundImage img').width() && $('#backgroundImage img').width() !== 0){
    scaleBGImage();
    //$('#backgroundImage').fadeOut(0);
    //$('#backgroundImage').css('visibility', 'visible');
    //$('#backgroundImage').fadeIn(250);
  }
}

function redrawLayout() {
  lastSliderPosition = null;
  // make project lists full height
  var viewportHeight = $(window).height() - 50;
  var targetHeight = viewportHeight;
  $('ul.projectList').each(function(){
    if(!$(this).data('original-height')){
      $(this).data('original-height', $(this).height());
    }
    if($(this).data('original-height') > targetHeight){
      targetHeight = $(this).height();
    }
  });
  //$('.viewport').height(viewportHeight);
  $('ul.projectList.projects, ul.projectList.index').height(targetHeight + 70);
  scaleBGImage();
  $('.viewport:not(.project)').height(targetHeight);
  $('.viewport.project').height($('.projectDescription').height());
  $('.prevNavi, .nextNavi').height(viewportHeight);
  $('.previous span, .next span').css('margin-top', viewportHeight/2);
  $('.repeat span, .nextProject span').css('margin-top', viewportHeight/4.4);
  $('.projectSlider').css('margin-top', $(window).height() - 103 - $('.projectSlider h1').height());
  $('#backgroundImage, .nextNavi').css('right', ($(window).width()/2) - (1170/2));
}

function scaleBGImage() {
  var bgImage = $('#backgroundImage img')[0];
  var dimensions = getFitAroundSizes($('#backgroundImage img'), 1115, $(window).height());
  $('#backgroundImage img').width(dimensions[0]);
  $('#backgroundImage img').height(dimensions[1]);
}

function updateProjectGalleryNavigation(){
  var url = _.first(hashes, 3);
  var imageNumber = _.last(hashes);
  var galleryData = $('#backgroundImage').data('gallery');
  var newNext = parseInt(imageNumber, 10) + 1;
  if(newNext > galleryData.length){
    newNext = 1;
  }
  var newPrevious = parseInt(imageNumber, 10) - 1;
  if(newPrevious <= 0){
    newPrevious = galleryData.length;
  }
  if(imageNumber == galleryData.length){
    $('.nextNavi a').addClass('lastImageInGallery');
  } else {
    $('.nextNavi a').removeClass('lastImageInGallery');
  }
  $('a.next').attr('href', root+"/"+url.concat(newNext).join('/')+"/");
  $('a.previous').attr('href', root+"/"+url.concat(newPrevious).join('/')+"/");
}

function updateGallery() {
  var imageNumber = parseInt(_.last(hashes), 10)-1;
  var galleryData = $('#backgroundImage').data('gallery');
  var img = galleryData[imageNumber].image.url;
  setBackgroundImage(img);
}

function setBackgroundImage(img){
  if($('#backgroundImage img').attr('src') == img) return;
  $('#backgroundImage').empty().append('<img src="">');
  $('#backgroundImage').css('visibility', 'hidden');
  $('#backgroundImage').waitForImages(function() {
    lazyBGImage();
  });
  $('#backgroundImage img').attr('src', img);
}

/*

-------------------------- HELPERS --------------------------

*/

// returns optimal sizes for an element that needs to fit around
// something while maintaining aspect ratio.
// Also saves original height in .data in case we need to resize it multiple times.

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

function getHashes() {
  var state = History.getState();
  var url = state.url;
  // FIXME this removes an IE fuckup but breaks normal browsers if there's a www. in the url
  // url = url.replace(".", "");
  url = url.replace(root+"/", "").split("/");
  hashes = _.compact(url);
}