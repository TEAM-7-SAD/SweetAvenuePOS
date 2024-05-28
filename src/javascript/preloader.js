const spinnerWrapper = document.querySelector(".spinner-wrapper");

window.addEventListener("load", () => {
  spinnerWrapper.style.opacity = "0";

  setTimeout(() => {
    spinnerWrapper.style.display = "none";
  }, 200);
});
