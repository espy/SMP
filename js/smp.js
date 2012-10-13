/*

Störmer Murphy and Partners

Alex Feyerke for We Are Fellows, 2012

 */

$(window).ready(function() {
  initListeners();
});

function initListeners() {
  $('a.localeSwitcher').click(function(event){
    event.preventDefault();
    switchLocale(this);
  });
  $('ul.projectList li:not(.listTitle)').hover(function(event){
    event.preventDefault();
    var img = $(this).data('image');
    $('#backgroundImage').empty().append('<img src="'+img+'">');
  });
}

function switchLocale(element) {
  var locale = $(element).data('target-locale');
  var baseURL = $(element).data('base-url');
  var url = window.location.href;
  url = url.replace(baseURL, "");
  // Check whether the URL has a locale and remove it
  var parts = _.compact(url.split("/"));
  if(parts[0].length === 2){
    parts.shift();
  }
  var newURL = baseURL +"/"+ locale + "/" + parts.join("/")+"/";
  window.location = newURL;
}