document.addEventListener("DOMContentLoaded", () => {
  const seatButtons = document.querySelectorAll(".cs-seat");
  const selectedSeatsDisplay = document.getElementById("selected-seats");
  const selectedSeatList = document.getElementById("selected-seat-list");
  const confirmButton = document.getElementById("confirm-button");
  const tipoBoletoSelect = document.getElementById("tipo-boleto");

  let selectedSeats = [];

  function getPrecioSeleccionado() {
    const selectedOption = tipoBoletoSelect.options[tipoBoletoSelect.selectedIndex];
    return parseFloat(selectedOption.getAttribute("data-precio")) || 0;
  }

  function updateSelectedDisplay() {
    selectedSeatsDisplay.textContent = selectedSeats.length;
    selectedSeatList.textContent = selectedSeats.length > 0 ? selectedSeats.join(", ") : "Ninguno";
  }


  function actualizarResumenModal() {
    const precio = getPrecioSeleccionado();
    const total = selectedSeats.length * precio;

    const selectedOption = tipoBoletoSelect.options[tipoBoletoSelect.selectedIndex];
    const tipoNombre = selectedOption.textContent.split(" - ")[0]; // Extrae "Básico", "Cliente", etc.

    document.getElementById("modal-ticket-type").textContent = tipoNombre;
    document.getElementById("modal-seats").textContent = selectedSeats.join(", ");
    document.getElementById("modal-price").textContent = precio.toFixed(2);
    document.getElementById("modal-total").textContent = total.toFixed(2);
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

    actualizarResumenModal();

    const summaryModal = new bootstrap.Modal(document.getElementById("summaryModal"));
    summaryModal.show();
  });

  // Si el usuario cambia el tipo de boleto, actualiza el resumen si ya hay asientos seleccionados
  tipoBoletoSelect.addEventListener("change", () => {
    if (selectedSeats.length > 0) {
      actualizarResumenModal();
    }
  });
});
