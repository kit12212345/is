var opened = false;
var audio = new Audio();
var time_audio = false;

function show_full_screen() {
  document.documentElement.requestFullscreen();
}

$(document).ready(function() {
  $('body').trigger('click');
  audio.preload = 'auto';
  audio.src = 'hb_m.mp3';
  audio.loop = true;

  var $clickMe = $('.click-icon'),
      $card = $('.card');

  $card.on('click', function() {
    clearTimeout(time_audio);
    if(opened === true){
      time_audio = setTimeout(function() {
        audio.pause();
        audio.src = 'hb_m.mp3';
      },800);
      opened = false;
    } else{
      time_audio = setTimeout(function() {
        audio.play();
      },800);
      opened = true;
    }
    $(this).toggleClass('is-opened');
    $clickMe.toggleClass('is-hidden');
  });

});
