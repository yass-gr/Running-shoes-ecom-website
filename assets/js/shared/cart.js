const closeBtn = $(".closeCart");
const container = $(".cartContainer");
const cart = $(".cart");
const cartOpen = $(".cartOpen");

closeBtn.on("click", () => {
  gsap.to(cart, {
    translateX: "100%",
    duration: 0.7,
    ease: "expo.in",
  });
  gsap.to(container, {
    opacity: 0,
    duration: 0.7,
    ease: "expo.in",
  });
});

cartOpen.on("click", () => {
  gsap.to(cart, {
    translateX: "0%",
    duration: 1,
    ease: "expo.out",
  });
  gsap.to(container, {
    opacity: 1,
    duration: 1,
    ease: "expo.out",
  });
});
