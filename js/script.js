/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

function filterFunction() {
  const input = document.getElementById("myInput");
  const filter = input.value.toUpperCase();
  const div = document.getElementById("myDropdown");
  const a = div.getElementsByTagName("a");
  for (let i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}

function handleCredentialResponse(response) {
  // Decodifica el token JWT recibido
  const data = jwt_decode(response.credential);

  // Envía los datos al servidor para iniciar sesión o registrarse
  fetch('index.php?controlador=usuarios&action=googleSignIn', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
          nombre: data.name,
          correo: data.email,
          google_id: data.sub
      })
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          // Redirige al usuario a la página principal
          window.location.href = 'index.php';
      } else {
          alert('Error al iniciar sesión con Google.');
      }
  })
  .catch(error => console.error('Error:', error));
}

function toggleSlider() {
  const slider = document.getElementById("menuSlider");
  const checkbox = document.getElementById("menuCheckbox");

  if (checkbox.checked) {
    slider.style.width = "250px"; // Abre el slider
  } else {
    slider.style.width = "0"; // Cierra el slider
  }
}
document.addEventListener('DOMContentLoaded', function () {
  const userInfo = document.getElementById('userInfo');
  const dropdown = document.getElementById('logoutDropdown');
  const arrow = document.getElementById('arrow');

  if (userInfo && dropdown && arrow) {
    userInfo.addEventListener('click', function (e) {
      dropdown.classList.toggle('show');
      arrow.classList.toggle('up');
      arrow.textContent = dropdown.classList.contains('show') ? '▲' : '▼';
      e.stopPropagation();
    });

    document.addEventListener('click', function () {
      dropdown.classList.remove('show');
      arrow.classList.remove('up');
      arrow.textContent = '▼';
    });

    dropdown.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }
});