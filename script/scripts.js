
let isRegistering = true;

function toggleAuthPanel() {
  const panel = document.getElementById("authPanel");
  panel.style.top = panel.style.top === "0%" ? "-80%" : "0%";
}
//ahora si
function toggleAuthMode() {
  const form = document.getElementById("authForm");
  const title = document.getElementById("formTitle");
  const registerFields = document.querySelectorAll(".register-field");
  const loginFields = document.querySelectorAll(".login-field");

  isRegistering = !isRegistering;
//comentario gg
  if (isRegistering) {
    title.textContent = "Registro";
    form.setAttribute("action", "../php/create.php");
    registerFields.forEach(field => field.style.display = "block");
    loginFields.forEach(field => field.style.display = "none");
    document.querySelector('[name="emailL"]').removeAttribute('required');
    document.querySelector('[name="passwordL"]').removeAttribute('required');
  } else {
    title.textContent = "Iniciar Sesión";
    form.setAttribute("action", "../php/create.php");
    registerFields.forEach(field => field.style.display = "none");
    loginFields.forEach(field => field.style.display = "block");
    document.querySelector('[name="email"]').removeAttribute('required');
    document.querySelector('[name="password"]').removeAttribute('required');
    document.querySelector('[name="name"]').removeAttribute('required');
    document.querySelector('[name="lastName"]').removeAttribute('required');
    document.querySelector('[name="secondLastName"]').removeAttribute('required');
    document.querySelector('[name="phone"]').removeAttribute('required');
    document.querySelector('[name="membershipType"]').removeAttribute('required');
    document.querySelector('[name="paymentMethod"]').removeAttribute('required');
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const registerFields = document.querySelectorAll(".register-field");
  const loginFields = document.querySelectorAll(".login-field");

  registerFields.forEach(field => field.style.display = "block");
  loginFields.forEach(field => field.style.display = "none");
});


let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-item');
const totalSlides = slides.length;

function updateCarousel() {
    const carouselInner = document.querySelector('.carousel-inner');
    carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
}



