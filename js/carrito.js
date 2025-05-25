document.addEventListener('DOMContentLoaded', function() {
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    const listaCarrito = document.getElementById('lista-carrito');
    const totalCarrito = document.getElementById('total');
    const btnComprar = document.getElementById('btn-comprar');
    const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
    const metodoPagoSelect = document.getElementById('metodo-pago');
    const tarjetaInfo = document.getElementById('tarjeta-info');

    // Mostrar/ocultar campos de tarjeta según selección
    metodoPagoSelect.addEventListener('change', function() {
        if (this.value === 'tarjeta') {
            tarjetaInfo.style.display = 'block';
        } else {
            tarjetaInfo.style.display = 'none';
        }
    });

    function mostrarCarrito() {
        listaCarrito.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            listaCarrito.innerHTML = '<div class="col-12 text-center py-5"><h5 class="text-muted">Tu carrito está vacío</h5></div>';
            if (btnComprar) btnComprar.style.display = 'none';
        } else {
            if (btnComprar) btnComprar.style.display = 'block';

            carrito.forEach((curso, index) => {
                total += curso.precio;

                const card = document.createElement('div');
                card.className = 'col-md-4';

                card.innerHTML = `
                    <div class="card shadow-sm border-0">
                        <img src="${curso.img}" class="card-img-top p-3" alt="${curso.nombre}" style="height: 200px; object-fit: contain;" />
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary fw-bold">${curso.nombre}</h5>
                            <p class="text-success fw-bold">$${curso.precio.toFixed(2)}</p>
                            <button class="btn btn-danger rounded-pill px-4 eliminar-curso" data-index="${index}">Eliminar</button>
                        </div>
                    </div>
                `;

                listaCarrito.appendChild(card);
            });
        }

        totalCarrito.textContent = total.toFixed(2);
        localStorage.setItem('carrito', JSON.stringify(carrito));

        document.querySelectorAll('.eliminar-curso').forEach(button => {
            button.addEventListener('click', (e) => {
                const idx = e.target.getAttribute('data-index');
                carrito.splice(idx, 1);
                mostrarCarrito();
                
                // Mostrar notificación de eliminado
                const toast = new bootstrap.Toast(document.getElementById('toastAgregado'));
                toast._element.querySelector('.toast-body').textContent = 'Curso eliminado del carrito';
                toast._element.querySelector('.toast-header').className = 'toast-header bg-danger text-white';
                toast.show();
            });
        });
    }

    // Añadir curso al carrito
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const nombre = button.getAttribute('data-nombre');
            const precio = parseFloat(button.getAttribute('data-precio'));
            const img = button.getAttribute('data-img');

            const existe = carrito.some(curso => curso.nombre === nombre);
            
            if (!existe) {
                carrito.push({ nombre, precio, img });
                mostrarCarrito();
                
                const toast = new bootstrap.Toast(document.getElementById('toastAgregado'));
                toast._element.querySelector('.toast-body').textContent = 'Curso agregado al carrito correctamente';
                toast._element.querySelector('.toast-header').className = 'toast-header bg-primary text-white';
                toast.show();
            } else {
                const toast = new bootstrap.Toast(document.getElementById('toastAgregado'));
                toast._element.querySelector('.toast-body').textContent = 'Este curso ya está en tu carrito';
                toast._element.querySelector('.toast-header').className = 'toast-header bg-warning text-dark';
                toast.show();
            }
        });
    });

    // Botón Comprar
    if (btnComprar) {
        btnComprar.addEventListener('click', () => {
            modalPago.show();
        });
    }

// Procesar pago - Versión corregida
document.getElementById('form-pago').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const metodoPago = document.getElementById('metodo-pago').value;
    
    // Validar datos de tarjeta si es el método seleccionado
    if (metodoPago === 'tarjeta') {
        const numeroTarjeta = document.getElementById('numero-tarjeta').value;
        const fechaExpiracion = document.getElementById('fecha-expiracion').value;
        const cvv = document.getElementById('cvv').value;
        
        if (!numeroTarjeta || !fechaExpiracion || !cvv) {
            alert('Por favor complete todos los datos de la tarjeta');
            return;
        }
        
        // Validación simple de tarjeta (solo para ejemplo)
        if (!/^\d{16}$/.test(numeroTarjeta.replace(/\s/g, ''))) {
            alert('Número de tarjeta inválido');
            return;
        }
    }
    
    // Verificar sesión y procesar pago
    fetch('../php/procesar_pago.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            metodo_pago: metodoPago,
            carrito: carrito
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Limpiar carrito
            carrito = [];
            localStorage.removeItem('carrito');
            mostrarCarrito();
            modalPago.hide();
            
            // Mostrar notificación de éxito
            const toast = new bootstrap.Toast(document.getElementById('toastCompra'));
            toast.show();
            
            // Redirigir a mis cursos
            setTimeout(() => {
                
                window.location.href = '../php/mis_cursos.php';
            }, 2000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar el pago: ' + error.message);
    });
});

    // Inicializar carrito
    mostrarCarrito();
});