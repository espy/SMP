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
  // scroll to anchor on studio page
  if(typeof scrollToSection != 'undefined' && scrollToSection !== ""){
    var target = $('.'+scrollToSection).offset().top - 85;
    $(document.body).animate({scrollTop: target}, 200);
  }
});

function initListeners() {
  // init history and listener
  getHashes();
  $(window).bind('statechange',function(data){
    getHashes();
    updateProjectGalleryNavigation();
    updateGallery();
  });
  // lazy load some images, like the static google map, to reduce perceived page load time
  $('img.lazy').each(function(){
    $(this).attr('src', $(this).data('src'));
  });
  // Projects - Hover behaviour for project list columns
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
  // Project - fix project description header
  $(window).scroll(function(){
    if($(window).scrollTop() >= $(window).height() - 106){
      $('.projectSlider').addClass('fixedProjectSliderHeader');
    } else {
      $('.projectSlider').removeClass('fixedProjectSliderHeader');
    }
  });
  // Project - Slider behaviour
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
  // Project - Gallery button behaviour
  $('a.next, a.previous').click(function(event){
    event.preventDefault();
    History.pushState(null, null, $(this).attr('href'));
  });
  // Project - keyboard navigation for the gallery
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
  // Studio - subnavigation
  $('a.anchor').click(function(event){
    event.preventDefault();
    var target = $('.'+$(this).data('anchor')).offset().top - 85;
    $(document.body).animate({scrollTop: target}, 200);
    $(this).closest('ul').children('li').removeClass('current_page_item');
    $(this).closest('li').addClass('current_page_item');
    var url = _.first(hashes, 2);
    var newURL = root+"/"+url.concat($(this).data('anchor').toLowerCase()).join('/')+"/";
    History.replaceState(null, null, newURL);
  });
  // Studio - Hover behaviour for staff
  // highlight image while hovering over name
  $('.staffInfo a').hover(function(){
    $(this).closest('.staffRow').find('.staffImages li.'+$(this).data('item')+' .overlay').addClass('hover');
  }, function(){
    $(this).closest('.staffRow').find('.staffImages li.'+$(this).data('item')+' .overlay').removeClass('hover');
  });
  // highlight name while hovering over image
  $('.staffImages img').hover(function(){
    $(this).closest('.staffRow').find('.staffInfo li a.'+$(this).parent().data('item')).addClass('hover');
  }, function(){
    $(this).closest('.staffRow').find('.staffInfo li a.'+$(this).parent().data('item')).removeClass('hover');
  });
  // initialize studio hover area
  if($('.viewport.studio').length > 0){
    $('.wrapper').mousemove(function(event){
      var xPos = event.pageX  - $(this).offset().left;
      if(xPos > 170 && xPos < 340){
        $('.viewport.studio .showOnHover').addClass('isHovering');
        $('.viewport.studio .hideOnHover').addClass('isHovering');
      } else {
        $('.viewport.studio .showOnHover').removeClass('isHovering');
        $('.viewport.studio .hideOnHover').removeClass('isHovering');
      }
    });
  }
  $(window).resize(function(){
    redrawLayout();
  });
  redrawLayout();
}

// Slides project description up to top of window or as far as it will go
// Scrolls to last scroll position, if it exists
function openSlider() {
  if($(window).scrollTop() === 0){
    var target = $(window).height() - 106;
    if(lastSliderPosition){
      target = lastSliderPosition;
    }
    $(document.body).animate({scrollTop: target}, 200);
  }
}

// Closes project description slider and remembers the last scroll position
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

// attempts to determine if the background image is there and has width, then fades it in
function showBGImage() {
  if($('#backgroundImage img').width() && $('#backgroundImage img').width() !== 0){
    scaleBGImage();
    $('#backgroundImage').fadeOut(0);
    $('#backgroundImage').css('visibility', 'visible');
    $('#backgroundImage').fadeIn(250);
  } else {
    // try again until image has width and can be layouted
    _.delay(function(){
      lazyBGImage();
    }, 100);
  }
}

// layout ALL THE THINGS!
function redrawLayout() {
  lastSliderPosition = null;
  // make project lists full height
  var viewportHeight = $(window).height() - 50;
  var targetHeight = viewportHeight;
  $('ul.projectList.projects').each(function(){
    if(!$(this).data('original-height')){
      $(this).data('original-height', $(this).height());
    }
    if($(this).data('original-height') > targetHeight){
      targetHeight = $(this).height();
    }
  });
  $('ul.projectList.projects').height(targetHeight + 70);
  // make first index column full height if it isn't already
  var indexColumnHeight = $('.projectList.index:first-child').height();
  if(indexColumnHeight !== 0 && indexColumnHeight < viewportHeight){
    $('.projectList.index:first-child').height(targetHeight-10);
    console.log("resizing!",indexColumnHeight,viewportHeight);
  }
  scaleBGImage();
  // layout the individual project page with its gallery
  if($('.viewport.project, .viewport.projects' ).length > 0){
    $('.viewport.project').height($('.projectDescription').height());
    $('.prevNavi, .nextNavi').height(viewportHeight);
    $('.previous span, .next span').css('margin-top', viewportHeight/2);
    $('.repeat span, .nextProject span').css('margin-top', viewportHeight/4.4);
    $('.projectSlider').css('margin-top', $(window).height() - 103 - $('.projectSlider h1').height());
    var targetWidth = ($(window).width() - ($(window).width() - 1115)/2)-10;
    $('#backgroundImage, .nextNavi').css({'right': 10});
    $('#backgroundImage').css({'width': targetWidth});
  }
  if($('.viewport.studio').length > 0){
    var imageHeight = $(window).height() - 225;
    $('.previous span, .next span, .nextProject span').css('margin-top', imageHeight/2);
    $('.nextNavi').css({'right': 0});
  }
  // general layout
  $('.viewport, .columnsOverlay').css('background-position', $('.viewport').offset().left);
}

function scaleBGImage() {
  var bgImage = $('#backgroundImage img')[0];
  var targetWidth = ($(window).width() - ($(window).width() - 1115)/2)-10;
  var dimensions;
  if($('#backgroundImage').hasClass('studio')){
    var targetHeight = $(window).height() - 225;
    dimensions = getFitAroundSizes($('#backgroundImage img'), targetWidth, targetHeight);
    $('#backgroundImage, #backgroundImage .columnsOverlay').height(targetHeight);
    $('#backgroundImage').width(targetWidth - 30);
    $('#backgroundImage img').width(dimensions[0] - 30).height(dimensions[1]);
  } else {
    dimensions = getFitAroundSizes($('#backgroundImage img'), targetWidth, $(window).height());
    $('#backgroundImage img').width(dimensions[0]).height(dimensions[1]);
  }
}

function updateProjectGalleryNavigation(){
  // FIXME this loses currentImageNumber if the anchor subnav is uses.
  // Need to save currentImage somewhere other than URL, I suppose
  var imageNumber = _.last(hashes);
  if(imageNumber.length > 2){
    imageNumber = 0;
  }
  var url;
  if($('.viewport.studio').length > 0){
    url = _.first(hashes, 2);
  } else {
    url = _.first(hashes, 3);
  }
  var galleryData = $('#backgroundImage').data('gallery');
  // determine next image
  var newNext = parseInt(imageNumber, 10) + 1;
  if(newNext > galleryData.length){
    newNext = 1;
  }
  // determine previous image
  var newPrevious = parseInt(imageNumber, 10) - 1;
  if(newPrevious <= 0){
    newPrevious = galleryData.length;
  }
  // are we at the end of a project's gallery?
  if(imageNumber == galleryData.length){
    $('.nextNavi a').addClass('lastImageInGallery');
  } else {
    $('.nextNavi a').removeClass('lastImageInGallery');
  }
  // update the button urls
  $('a.next').attr('href', root+"/"+url.concat(newNext).join('/')+"/");
  $('a.previous').attr('href', root+"/"+url.concat(newPrevious).join('/')+"/");
}

function updateGallery() {
  if(_.last(hashes).length > 2) return;
  var imageNumber = parseInt(_.last(hashes), 10)-1;
  var galleryData = $('#backgroundImage').data('gallery');
  var img = galleryData[imageNumber].image.url;
  setBackgroundImage(img);
}

function setBackgroundImage(img){
  if($('#backgroundImage img').attr('src') == img) return;
  $('#backgroundImage img').remove();
  $('#backgroundImage').append('<img src="">');
  $('#backgroundImage').css('visibility', 'hidden');
  $('#backgroundImage').waitForImages(function() {
    // this calls a debounced version of showBGImage
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

// this is a crap way of doing this, admittedly
function getHashes() {
  var state = History.getState();
  var url = state.url;
  // FIXME this removes an IE fuckup but breaks normal browsers if there's a www. in the url
  // url = url.replace(".", "");
  url = url.replace(root+"/", "").split("/");
  hashes = _.compact(url);
}