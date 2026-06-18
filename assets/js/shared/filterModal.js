const overlay = document.querySelector(".filter-overlay");
const panel = document.querySelector(".filter-panel");
const openBtn = document.querySelector(".collection-toolbar__filter");
const closeBtn = document.querySelector(".filter-panel__close");

if (!overlay || !panel || !openBtn || !closeBtn) {
  console.warn("filter panel: required elements not found");
} else {
  const allToggles = panel.querySelectorAll(".filter-size, .filter-color, .filter-checkbox:not(.filter-checkbox--disabled)");

  function updateApplyBtn() {
    const hasSelection = panel.querySelector(".filter-size.is-active, .filter-color.is-active, .filter-checkbox.is-active");
    panel.classList.toggle("has-selection", !!hasSelection);
  }

  function openPanel() {
    overlay.classList.add("is-open");
    panel.classList.add("is-open");
    document.body.style.overflow = "hidden";
    updateApplyBtn();
  }

  function closePanel() {
    overlay.classList.remove("is-open");
    panel.classList.remove("is-open");
    document.body.style.overflow = "";
  }

  openBtn.addEventListener("click", openPanel);
  closeBtn.addEventListener("click", closePanel);
  overlay.addEventListener("click", closePanel);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closePanel();
  });

  allToggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("is-active");
      updateApplyBtn();
    });
  });
}
