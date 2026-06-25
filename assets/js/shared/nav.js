const navItem = $(".nav-item");
const menu = $(".menu");
const menuContent = $(".menuContent");

let selected = 0;

navItem.on("mouseenter", (e) => {
  menu.addClass("show");
  selected = $(e.currentTarget).data("tab");
  refreshMenu();
});

menuContent.on("mouseleave", () => {
  menu.removeClass("show");
});

const refreshMenu = () => {
  $(".tab-content").hide();
  $(`.tab-content[data-tab="${selected}"]`).show();
};

$(".tab-content").hide();
refreshMenu();

const annSlides = $(".ann-slide");
const annLeft = $(".ann-arrow--left");
const annRight = $(".ann-arrow--right");
let annCurrent = 0;
let annTimer;

function annGoTo(idx) {
  annSlides.removeClass("active");
  annCurrent = (idx + annSlides.length) % annSlides.length;
  annSlides.eq(annCurrent).addClass("active");
}

function annStartAutoplay() {
  annTimer = setInterval(() => annGoTo(annCurrent + 1), 4000);
}

function annResetAutoplay() {
  clearInterval(annTimer);
  annStartAutoplay();
}

annLeft.on("click", () => {
  annGoTo(annCurrent - 1);
  annResetAutoplay();
});

annRight.on("click", () => {
  annGoTo(annCurrent + 1);
  annResetAutoplay();
});

annStartAutoplay();
