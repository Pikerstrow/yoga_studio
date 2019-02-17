;
(function($) {
  "use strict";

  function preloader() {
    if ($('.preloader').length) {
      $(window).on('load', function() {
        $('.preloader').delay(1000).fadeOut('1000'); // 100 slow
        $('body').delay(1000).css({
          'overflow': 'visible'
        }); // 5000
      });
    }
  };
  var wow = new WOW({
    mobile: false
  });
  wow.init();

  preloader();
})(jQuery);

$(window).scroll(function() {
  if ($(this).scrollTop() >= 100) {
    $('.btn-top').fadeIn(50);
  }
  else {
    $('.btn-top').fadeOut(50);
  }
  if ($(document).scrollTop() > 100) {
    $('.navbar').addClass('affix');
  }
  else {
    $('.navbar').removeClass('affix');
  }
  if ($(document).scrollTop() > 400) {
    $('.hamburger').addClass(50);
  }
  else {
    $('.hamburger').removeClass(50);
  }
});
$('.btn-top').click(function() {
  $('body,html').animate({
    scrollTop: 0
  }, 2000);
});
$('.dropdown-item ,.navLink, .navbar-brand, .logo').click(function() {
  var sectionTo = $(this).attr('href');
  $('html, body').animate({
    scrollTop: $(sectionTo).offset().top
  }, 1000);
});

var forEach = function(t, o, r) {
  if ("[object Object]" === Object.prototype.toString.call(t))
    for (var c in t) Object.prototype.hasOwnProperty.call(t, c) && o.call(r, t[c], c, t);
  else
    for (var e = 0, l = t.length; l > e; e++) o.call(r, t[e], e, t)
};

var hamburgers = document.querySelectorAll(".hamburger");
if (hamburgers.length > 0) {
  forEach(hamburgers, function(hamburger) {
    hamburger.addEventListener("click", function() {
      this.classList.toggle("is-active");
    }, false);
  });
}

function openNav() {
  document.getElementById("myNav").style.height = "100%";
  $('.fixHamb').css('display', 'none');
}
function closeNav() {
  document.getElementById("myNav").style.height = "0%";
  $('.hamburger').removeClass('is-active');
  $('.fixHamb').css('display', 'block');
  $('#hambFadeIn').addClass('animated fadeIn');
}

function readMore() {
  var dots = document.getElementById("dots");
  var moreText = document.getElementById("more");
  var btnText = document.getElementById("myBtn");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    btnText.innerHTML = "Більше";
    moreText.style.display = "none";
  } else {
    dots.style.display = "none";
    btnText.innerHTML = "Згорнути";
    moreText.style.display = "inline";
  }
}
