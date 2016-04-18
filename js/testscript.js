window.onscroll = function() {
  var scrolled = window.pageYOffset || document.documentElement.scrollTop;
  document.getElementById('showScroll').innerHTML = scrolled + 'px';
}
