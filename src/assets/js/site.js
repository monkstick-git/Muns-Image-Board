document.addEventListener("DOMContentLoaded", function () {
  const lazyImages = document.querySelectorAll("img.lazyload");

  lazyImages.forEach((img) => {
    const src = img.getAttribute("data-src");
    if (src) {
      img.setAttribute("src", src);
    }
  });
});
