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

// Mueve la función fuera del DOMContentLoaded y hazla global
function toggleSlider() {
  const slider = document.getElementById("menuSlider");
  const checkbox = document.getElementById("menuCheckbox");
  if (checkbox) {
    if (checkbox.checked) {
      slider.style.width = "250px";
    } else {
      slider.style.width = "0";
    }
  } else {
    slider.style.width = slider.style.width === '250px' ? '0' : '250px';
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const slider = document.getElementById("menuSlider");
  const checkbox = document.getElementById("menuCheckbox");
  const userInfo = document.getElementById('userInfo');
  const dropdown = document.getElementById('logoutDropdown');
  const arrow = document.getElementById('arrow');

  if (checkbox) {
    checkbox.addEventListener('change', toggleSlider);
  }

  if (slider && userInfo && dropdown && arrow) {
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

let currentStep = 1;
const totalSteps = 4;

function showStep(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const div = document.getElementById('step' + i);
        if (div) div.style.display = (i === step) ? 'block' : 'none';
    }
}

function nextStep(step) {
    if (validateStep(step)) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep(step) {
    currentStep--;
    showStep(currentStep);
}

function validateStep(step) {
    switch (step) {
        case 1:
            const nombre = document.getElementById('nombre').value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            const edad = parseInt(document.getElementById('edad').value, 10);
            if (!nombre || !apellidos || isNaN(edad) || edad < 18) {
                alert('Completa todos los campos y asegúrate de ser mayor de edad.');
                return false;
            }
            break;
        case 2:
            const correo = document.getElementById('correo').value.trim();
            if (!correo || !correo.includes('@')) {
                alert('Introduce un correo válido.');
                return false;
            }
            break;
        case 3:
            const passwd = document.getElementById('passwd').value;
            const confpasswd = document.getElementById('confpasswd').value;
            if (!passwd || passwd !== confpasswd) {
                alert('Las contraseñas no coinciden o están vacías.');
                return false;
            }
            break;
        case 4:
            const tipo = document.querySelector('input[name="tipo"]:checked');
            if (!tipo) {
                alert('Selecciona un tipo de usuario.');
                return false;
            }
            break;
    }
    return true;
}

// Mostrar el primer paso al cargar
document.addEventListener('DOMContentLoaded', function () {
    showStep(1);
});

document.getElementById('registForm').addEventListener('submit', function(e) {
    const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
    if (fechaNacimiento) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        document.getElementById('edad').value = edad;
    }
});

// Calcula la edad automáticamente al cambiar la fecha de nacimiento
document.getElementById('fecha_nacimiento').addEventListener('change', function() {
    const fechaNacimiento = this.value;
    if (fechaNacimiento) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        document.getElementById('edad').value = edad;
    } else {
        document.getElementById('edad').value = '';
    }
});