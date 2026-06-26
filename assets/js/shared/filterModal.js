const overlay = document.querySelector(".filter-overlay");
const panel = document.querySelector(".filter-panel");
const openBtn = document.querySelector(".collection-toolbar__filter");
const closeBtn = document.querySelector(".filter-panel__close");
const applyBtn = document.querySelector(".filter-panel__apply");
const clearBtn = document.querySelector(".filter-panel__clear");
const panelCount = document.querySelector(".filter-panel__count");
const toolbarCount = document.querySelector(".collection-toolbar__count");
const grid = document.querySelector(".collection-grid");
const chips = document.querySelector(".collection-toolbar__chips");

if (!overlay || !panel || !openBtn || !closeBtn) {
  console.warn("filter panel: required elements not found");
} else {
  function updateApplyBtn() {
    const hasSelection = panel.querySelector(".filter-size.is-active, .filter-color.is-active, .filter-checkbox.is-active");
    panel.classList.toggle("has-selection", !!hasSelection);
  }

  function getVisibleCards() {
    const all = grid ? Array.from(grid.querySelectorAll(".card")) : [];
    return all.filter(c => c.style.display !== "none");
  }

  function updateAvailableOptions() {
    const visible = getVisibleCards();

    const availSizes = new Set();
    const availColors = new Set();
    const availMaterials = new Set();

    visible.forEach(c => {
      (c.dataset.sizes || "").split(",").map(s => s.trim()).filter(Boolean).forEach(s => availSizes.add(s));
      const col = (c.dataset.color || "").toLowerCase();
      if (col) availColors.add(col);
      const mat = (c.dataset.material || "").toLowerCase();
      if (mat) availMaterials.add(mat);
    });

    const allSizeBtns = panel.querySelectorAll(".filter-size");
    allSizeBtns.forEach(btn => {
      const val = btn.dataset.value;
      if (availSizes.has(val)) {
        btn.classList.remove("filter-size--disabled");
        btn.disabled = false;
      } else {
        btn.classList.remove("is-active");
        btn.classList.add("filter-size--disabled");
        btn.disabled = true;
      }
    });

    const allColorBtns = panel.querySelectorAll(".filter-color");
    allColorBtns.forEach(btn => {
      const val = btn.dataset.value.toLowerCase();
      if (availColors.has(val)) {
        btn.classList.remove("filter-color--disabled");
        btn.disabled = false;
      } else {
        btn.classList.remove("is-active");
        btn.classList.add("filter-color--disabled");
        btn.disabled = true;
      }
    });

    const allMatBtns = panel.querySelectorAll(".filter-checkbox[data-filter='material']");
    allMatBtns.forEach(btn => {
      const val = btn.dataset.value.toLowerCase();
      if (availMaterials.has(val)) {
        btn.classList.remove("filter-checkbox--disabled");
        btn.disabled = false;
      } else {
        btn.classList.remove("is-active");
        btn.classList.add("filter-checkbox--disabled");
        btn.disabled = true;
      }
    });

    const allPriceBtns = panel.querySelectorAll(".filter-checkbox[data-filter='price']");
    allPriceBtns.forEach(btn => {
      const range = getPriceRange(btn.dataset.value);
      if (!range) return;
      const [lo, hi] = range;
      const has = visible.some(c => {
        const p = parseFloat(c.dataset.price);
        return !isNaN(p) && p >= lo && p <= hi;
      });
      if (has) {
        btn.classList.remove("filter-checkbox--disabled");
        btn.disabled = false;
      } else {
        btn.classList.remove("is-active");
        btn.classList.add("filter-checkbox--disabled");
        btn.disabled = true;
      }
    });
  }

  function openPanel() {
    overlay.classList.add("is-open");
    panel.classList.add("is-open");
    document.body.style.overflow = "hidden";
    updateAvailableOptions();
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

  panel.addEventListener("click", (e) => {
    const btn = e.target.closest(".filter-size, .filter-color, .filter-checkbox");
    if (!btn || btn.disabled) return;
    if (btn.classList.contains("filter-checkbox--disabled") ||
        btn.classList.contains("filter-size--disabled") ||
        btn.classList.contains("filter-color--disabled")) return;
    btn.classList.toggle("is-active");
    updateApplyBtn();
  });

  function getPriceRange(value) {
    switch (value) {
      case "under75": return [0, 75];
      case "76-100": return [76, 100];
      case "101-125": return [101, 125];
      case "126-150": return [126, 150];
      case "over150": return [151, Infinity];
      default: return null;
    }
  }

  function matchCard(card, criteria) {
    if (criteria.sizes.length > 0) {
      const cardSizes = (card.dataset.sizes || "").split(",").map(s => s.trim()).filter(Boolean);
      const hasSize = criteria.sizes.some(s => cardSizes.includes(s));
      if (!hasSize) return false;
    }

    if (criteria.colors.length > 0) {
      const cardColor = (card.dataset.color || "").toLowerCase();
      const match = criteria.colors.some(c => c === cardColor);
      if (!match) return false;
    }

    if (criteria.materials.length > 0) {
      const cardMaterial = (card.dataset.material || "").toLowerCase();
      const match = criteria.materials.some(m => m === cardMaterial);
      if (!match) return false;
    }

    if (criteria.priceRanges.length > 0) {
      const cardPrice = parseFloat(card.dataset.price);
      if (isNaN(cardPrice)) return false;
      const inRange = criteria.priceRanges.some(([lo, hi]) => cardPrice >= lo && cardPrice <= hi);
      if (!inRange) return false;
    }

    return true;
  }

  function applyFilters() {
    const activeSizes = panel.querySelectorAll(".filter-size.is-active");
    const activeColors = panel.querySelectorAll(".filter-color.is-active");
    const activePrices = panel.querySelectorAll(".filter-checkbox.is-active[data-filter='price']");
    const activeMaterials = panel.querySelectorAll(".filter-checkbox.is-active[data-filter='material']");

    const criteria = {
      sizes: Array.from(activeSizes).map(b => b.dataset.value),
      colors: Array.from(activeColors).map(b => b.dataset.value.toLowerCase()),
      materials: Array.from(activeMaterials).map(b => b.dataset.value.toLowerCase()),
      priceRanges: Array.from(activePrices).map(b => getPriceRange(b.dataset.value)).filter(Boolean),
    };

    const hasActiveFilters = criteria.sizes.length > 0 || criteria.colors.length > 0 || criteria.materials.length > 0 || criteria.priceRanges.length > 0;

    const cards = grid ? grid.querySelectorAll(".card") : [];
    let visibleCount = 0;

    cards.forEach((card) => {
      if (!hasActiveFilters || matchCard(card, criteria)) {
        card.style.display = "";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    const total = cards.length;
    const shown = hasActiveFilters ? visibleCount : total;

    if (panelCount) panelCount.textContent = `(${shown} products)`;
    if (toolbarCount) toolbarCount.textContent = `(${shown} products)`;

    updateChips(criteria);

    panel.classList.remove("has-selection");
    closePanel();
    updateAvailableOptions();
  }

  if (applyBtn) {
    applyBtn.addEventListener("click", applyFilters);
  }

  function clearFilters() {
    panel.querySelectorAll(".filter-size.is-active, .filter-color.is-active, .filter-checkbox.is-active").forEach((btn) => btn.classList.remove("is-active"));

    const cards = grid ? grid.querySelectorAll(".card") : [];
    cards.forEach((card) => { card.style.display = ""; });
    const total = cards.length;

    if (panelCount) panelCount.textContent = `(${total} products)`;
    if (toolbarCount) toolbarCount.textContent = `(${total} products)`;
    if (chips) chips.innerHTML = "";

    panel.classList.remove("has-selection");
    updateAvailableOptions();
  }

  if (clearBtn) {
    clearBtn.addEventListener("click", clearFilters);
  }

  function updateChips(criteria) {
    if (!chips) return;

    const labels = [];

    criteria.sizes.forEach((s) => {
      labels.push({ text: `Size: ${s}`, type: "size", value: s });
    });
    criteria.colors.forEach((c) => {
      labels.push({ text: `Color: ${c.charAt(0).toUpperCase() + c.slice(1)}`, type: "color", value: c });
    });
    criteria.materials.forEach((m) => {
      labels.push({ text: `Material: ${m.charAt(0).toUpperCase() + m.slice(1)}`, type: "material", value: m });
    });
    criteria.priceRanges.forEach(([lo, hi]) => {
      let label;
      if (hi === Infinity) label = "Over $150";
      else if (lo === 0) label = `Under $${hi}`;
      else label = `$${lo}–$${hi}`;
      labels.push({ text: label, type: "price", value: `${lo}-${hi}` });
    });

    if (labels.length === 0) {
      chips.innerHTML = "";
      return;
    }

    chips.innerHTML = labels.map((l) =>
      `<span data-chip-type="${l.type}" data-chip-value="${l.value}">${l.text} <button class="chip-remove" type="button" aria-label="Remove ${l.text} filter">&times;</button></span>`
    ).join("");

    chips.querySelectorAll(".chip-remove").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const chip = e.target.closest("span");
        const type = chip.dataset.chipType;
        const value = chip.dataset.chipValue;
        chip.remove();

        const panelBtns = panel.querySelectorAll(`[data-filter="${type}"]`);
        panelBtns.forEach((b) => {
          if (type === "price") {
            const [lo, hi] = value.split("-").map(Number);
            const range = getPriceRange(b.dataset.value);
            if (range && range[0] === lo && range[1] === hi) b.classList.remove("is-active");
          } else {
            if (b.dataset.value.toLowerCase() === value) b.classList.remove("is-active");
          }
        });

        applyFilters();
      });
    });
  }
}

document.querySelectorAll(".info-faq__question").forEach((btn) => {
  btn.addEventListener("click", () => {
    const item = btn.closest(".info-faq__item");
    const isOpen = item.classList.contains("is-open");
    item.classList.toggle("is-open");
    btn.setAttribute("aria-expanded", !isOpen);
  });
});
