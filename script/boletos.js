document.addEventListener("DOMContentLoaded", () => {
  const seatButtons = document.querySelectorAll(".cs-seat");
  const selectedSeatsDisplay = document.getElementById("selected-seats");
  const confirmButton = document.getElementById("confirm-button");

  const seatPrice = 50;
  let selectedSeats = [];

  function updateSelectedDisplay() {
    selectedSeatsDisplay.textContent = selectedSeats.length;
    selectedSeatList.textContent = selectedSeats.length > 0 ? selectedSeats.join(", ") : "Ninguno";
  }


  seatButtons.forEach(button => {
    button.classList.add("cs-available");


    button.addEventListener("click", () => {
      const seat = button.dataset.seat;

      if (button.classList.contains("cs-occupied")) return;

      if (button.classList.contains("cs-selected")) {
        button.classList.remove("cs-selected");
        button.classList.add("cs-available");
        selectedSeats = selectedSeats.filter(s => s !== seat);
      } else {

        if (selectedSeats.length >= 3) {
          const limitModal = new bootstrap.Modal(document.getElementById("limitModal"));
          limitModal.show();
          return;
        }


        button.classList.remove("cs-available");
        button.classList.add("cs-selected");
        selectedSeats.push(seat);
      }

      updateSelectedDisplay();
    });

  });



  confirmButton.addEventListener("click", () => {
    if (selectedSeats.length === 0) {
      alert("Por favor selecciona al menos un asiento.");
      return;
    }

    const total = selectedSeats.length * seatPrice;

    // Actualizar contenido del modal
    document.getElementById("modal-seats").textContent = selectedSeats.join(", ");
    document.getElementById("modal-price").textContent = seatPrice;
    document.getElementById("modal-total").textContent = total;

    // Mostrar el modal
    const summaryModal = new bootstrap.Modal(document.getElementById("summaryModal"));
    summaryModal.show();
  });


});


const selectedSeatList = document.getElementById("selected-seat-list");
