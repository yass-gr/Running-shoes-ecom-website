const navItem = $(".nav-item");
const menu = $(".menu");
const menuContent = $(".menuContent");

let selected = 0;

const menuData = [
  {
    special: [
      "COLLECTION 1",
      "COLLECTION 2",
      "COLLECTION 3",
      "COLLECTION 4",
      "COLLECTION 5",
    ],
    cols: [
      {
        title: "MEN'S SHOES",
        items: ["Shop All", "item 1", "item 2", "item 3"],
      },
      {
        title: "COSTUMER FAVORITES",
        items: ["item 1", "item 2", "item 3"],
      },
      {
        title: "ACCESSORIES",
        items: ["item 1", "item 2", "item 3"],
      },
    ],

    grid: [
      {
        title: "NAME",
        src: "./assets/images/c2.jpg",
      },
      {
        title: "NAME",
        src: "./assets/images/c1.jpg",
      },
      {
        title: "NAME",
        src: "./assets/images/grid3.jpg",
      },
    ],
  },
  {
    special: [
      "COLLECTION 1",
      "COLLECTION 2",
      "COLLECTION 3",
      "COLLECTION 4",
      "COLLECTION 5",
    ],
    cols: [
      {
        title: "WOMENS'S SHOES",
        items: ["Shop All", "item 1", "item 2", "item 3"],
      },
      {
        title: "COSTUMER FAVORITES",
        items: ["item 1", "item 2", "item 3"],
      },
      {
        title: "ACCESSORIES",
        items: ["item 1", "item 2", "item 3"],
      },
    ],

    grid: [
      {
        title: "NAME",
        src: "./assets/images/c3.jpg",
      },
      {
        title: "NAME",
        src: "./assets/images/c2.jpg",
      },
      {
        title: "NAME",
        src: "./assets/images/grid4.jpg",
      },
    ],
  },
  {
    cols: [
      {
        title: "MEN",
        items: ["item 1", "item 2", "item 3"],
      },
      {
        title: "WOMEN",
        items: ["item 1", "item 2", "item 3"],
      },
    ],

    grid: [
      {
        title: "NAME",
        src: "./assets/images/cat1.webp",
      },
      {
        title: "NAME",
        src: "./assets/images/cat1.webp",
      },
    ],
  },
];

navItem.on("mouseenter", (e) => {
  menu.addClass("show");
  const index = parseInt($(e.target).data("index"));
  selected = index;
  refreshMenu();
});

menuContent.on("mouseleave", () => {
  menu.removeClass("show");
});

const refreshMenu = () => {
  menuContent.html("");
  if (selected !== 2) {
    const colsContainer = $("<div>").addClass("colsContainer");
    const col1 = $("<ul>");

    menuData[selected].special.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      col1.append(item);
    });
    const col2 = $("<div>")
      .html(`<h3>${menuData[selected].cols[0].title}</h3> `)
      .addClass("col");
    const ul1 = $("<ul>");

    menuData[selected].cols[0].items.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      ul1.append(item);
    });
    col2.append(ul1);

    const col3 = $("<div>")
      .html(`<h3>${menuData[selected].cols[1].title}</h3> `)
      .addClass("col");
    const ul2 = $("<ul>");

    menuData[selected].cols[1].items.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      ul2.append(item);
    });
    col3.append(ul2);

    const col4 = $("<div>")
      .html(`<h3>${menuData[selected].cols[2].title}</h3> `)
      .addClass("col");
    const ul3 = $("<ul>");

    menuData[selected].cols[2].items.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      ul3.append(item);
    });
    col4.append(ul3);

    colsContainer.append(col1, col2, col3, col4);

    const imgGrid = $("<div>").addClass("imgGrid");
    menuData[selected].grid.forEach((i) => {
      const container = $("<div>").addClass("gridItem");
      const img = $("<img>").attr(`src`, `${i.src}`);
      const title = $("<p>").html(`${i.title}`);
      container.append(img, title);
      imgGrid.append(container);
    });

    menuContent.append(colsContainer, imgGrid);
  } else {
    const colsContainer = $("<div>").addClass("colsContainer");

    const col2 = $("<div>")
      .html(`<h3>${menuData[selected].cols[0].title}</h3> `)
      .addClass("col");
    const ul1 = $("<ul>");

    menuData[selected].cols[0].items.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      ul1.append(item);
    });
    col2.append(ul1);

    const col3 = $("<div>")
      .html(`<h3>${menuData[selected].cols[1].title}</h3> `)
      .addClass("col");
    const ul2 = $("<ul>");

    menuData[selected].cols[1].items.forEach((i) => {
      const item = $("<li>").html(`<a>${i}</a>`);
      ul2.append(item);
    });
    col3.append(ul2);

    colsContainer.append(col2, col3);

    const imgGrid = $("<div>").addClass("imgGridSale");
    menuData[selected].grid.forEach((i) => {
      const container = $("<div>").addClass("gridItemSale");
      const img = $("<img>").attr(`src`, `${i.src}`);
      const title = $("<p>").html(`${i.title}`);
      container.append(img, title);
      imgGrid.append(container);
    });

    menuContent.append(colsContainer, imgGrid);
  }
};

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
