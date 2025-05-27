// Elimina la declaración duplicada y mantén solo una al inicio del archivo
let currentStep = 1;
const totalSteps = 6;

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

// Funciones de navegación entre pasos
function showStep(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const div = document.getElementById('step' + i);
        if (div) div.style.display = (i === step) ? 'block' : 'none';
    }
    currentStep = step;
}

function nextStep(step) {
    if (!validateStep(step)) return;

    // Oculta el paso actual
    document.getElementById('step' + step).style.display = 'none';

    // Lógica para mostrar el siguiente paso
    if (step === 4) {
        // Siempre muestra el paso 5 después de la contraseña
        document.getElementById('step5').style.display = 'block';
    } else if (step === 5) {
        // Si elige "Empresa", muestra el paso 6, si no, envía el formulario
        const tipo = document.querySelector('input[name="tipo"]:checked');
        if (tipo && tipo.value === 'empresa') {
            document.getElementById('step6').style.display = 'block';
        } else {
            document.getElementById('registForm').submit();
        }
    } else if (step === 6) {
        document.getElementById('registForm').submit();
    } else {
        // Para los demás pasos
        document.getElementById('step' + (step + 1)).style.display = 'block';
    }
}

function prevStep(step) {
    document.getElementById('step' + step).style.display = 'none';
    document.getElementById('step' + (step - 1)).style.display = 'block';
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
            const telefono = document.getElementById('telefono').value.trim();
            // Validación: solo dígitos, longitud mínima 6 (ajusta según país)
            const telefonoRegex = /^[0-9]{6,15}$/;
            if (!telefonoRegex.test(telefono)) {
                alert('Escribe un número de teléfono válido.');
                return false;
            }
            break;
        case 4:
            const passwd = document.getElementById('passwd').value;
            const confpasswd = document.getElementById('confpasswd').value;
            if (!passwd || passwd !== confpasswd) {
                alert('Las contraseñas no coinciden o están vacías.');
                return false;
            }
            break;
        case 5:
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

    // Evento para cambiar a paso 6 si se selecciona "Empresa"
    document.querySelectorAll('input[name="tipo"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'empresa') {
                const btnNext5 = document.getElementById('btnNext5');
                if (btnNext5) btnNext5.textContent = 'Siguiente';
            } else {
                const btnNext5 = document.getElementById('btnNext5');
                if (btnNext5) btnNext5.textContent = 'Finalizar';
            }
        });
    });
});

// Selección visual de cards en el paso 5
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.user-type-card');
    cards.forEach(card => {
        card.addEventListener('click', function () {
            cards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;

            // Cambia el texto del botón según selección
            const btnNext5 = document.getElementById('btnNext5');
            if (this.querySelector('input[type="radio"]').value === 'empresa') {
                btnNext5.textContent = 'Siguiente';
            } else {
                btnNext5.textContent = 'Finalizar';
            }
        });
    });
});

// Calcula la edad automáticamente al cambiar la fecha de nacimiento
document.addEventListener('DOMContentLoaded', function() {
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    const edadInput = document.getElementById('edad');
    if (fechaNacimiento && edadInput) {
        fechaNacimiento.addEventListener('change', function() {
            const hoy = new Date();
            const nacimiento = new Date(this.value);
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const m = hoy.getMonth() - nacimiento.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }
            edadInput.value = edad;
        });
    }
});