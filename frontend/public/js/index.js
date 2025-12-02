// Inicializa el mapa en Piedecuesta
const map = L.map("map").setView([6.9931457, -73.0861263], 17);

// Capa de tiles de OSM
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// Ícono personalizado
const customIcon = L.icon({
  iconUrl: "./public/img/684908.png",
  iconSize: [16, 16],
  iconAnchor: [8, 16],
  popupAnchor: [0, -16],
});

// Marcador inicial
let marker = L.marker([6.9931457, -73.0861263], { icon: customIcon })
  .addTo(map)
  .bindPopup("Piedecuesta, Santander");

// Función para obtener ubicación actual
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    document.getElementById("ubicacion").value = "Geolocalización no soportada";
  }
}

function showError(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      //document.getElementById("ubicacion").value = "Permiso de ubicación denegado";
      break;
    case error.POSITION_UNAVAILABLE:
      //document.getElementById("ubicacion").value = "Ubicación no disponible";
      break;
    case error.TIMEOUT:
      //document.getElementById("ubicacion").value = "Tiempo de espera agotado";
      break;
    default:
    //document.getElementById("ubicacion").value = "Error desconocido";
  }
}

function showPosition(position) {
  const lat = position.coords.latitude.toFixed(6);
  const lon = position.coords.longitude.toFixed(6);

  // ✅ Cargar en los inputs separados
  document.getElementById("latitud").value = lat;
  document.getElementById("longitud").value = lon;

  // Mover mapa y marcador
  map.setView([lat, lon], 17);
  marker.setLatLng([lat, lon]).bindPopup("Tu ubicación actual").openPopup();
}

// Evento doble clic en el mapa
map.on("dblclick", function (e) {
  const lat = e.latlng.lat.toFixed(6);
  const lon = e.latlng.lng.toFixed(6);

  // ✅ Cargar en los inputs separados
  document.getElementById("latitud").value = lat;
  document.getElementById("longitud").value = lon;

  // Mover marcador
  marker
    .setLatLng([e.latlng.lat, e.latlng.lng])
    .bindPopup("Ubicación seleccionada")
    .openPopup();
});

document
  .getElementById("form_cliente")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    // Ajustar fecha a ISO si existe
    if (data.fechaInstalacion) {
      data.fechaInstalacion = new Date(data.fechaInstalacion).toISOString();
    }

    // Convertir latitud/longitud a número
    if (data.latitud) data.latitud = parseFloat(data.latitud);
    if (data.longitud) data.longitud = parseFloat(data.longitud);

    try {
      const response = await fetch("/api/cliente", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();
      console.log("Respuesta:", result);
    } catch (err) {
      console.error("Error:", err);
    }
  });

  // Inicializar tabla con datos
    document.addEventListener("DOMContentLoaded", () => {
     // $('#tablaClientes').bootstrapTable({ data: datosBackend });
    });

    // Formatear fecha
    function formatearFecha(value) {
      const fecha = new Date(value);
      return fecha.toLocaleString("es-CO", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
      });
    }