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
    $('#backgroundImage').empty().append('<img src="'+img+'">');
  });
  $(window).resize(function(){
    redrawLayout();
  });
  redrawLayout();
}

function redrawLayout() {
  // make project lists full height
  $('ul.projectList').height($(window).height() - 10);
}