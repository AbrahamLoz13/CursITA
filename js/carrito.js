document.addEventListener('DOMContentLoaded', function() {
    // Arreglo para almacenar cursos en el carrito
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

    const listaCarrito = document.getElementById('lista-carrito');
    const totalCarrito = document.getElementById('total');
    const btnComprar = document.getElementById('btn-comprar');
    const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));

    // Función para mostrar el carrito
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

                // Crear tarjeta para cada curso
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

        // Asignar evento eliminar a cada botón
        document.querySelectorAll('.eliminar-curso').forEach(button => {
            button.addEventListener('click', (e) => {
                const idx = e.target.getAttribute('data-index');
                carrito.splice(idx, 1);
                mostrarCarrito();
            });
        });
    }

    // Añadir curso al carrito desde botones
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const nombre = button.getAttribute('data-nombre');
            const precio = parseFloat(button.getAttribute('data-precio'));
            const img = button.getAttribute('data-img');

            // Verificar si el curso ya está en el carrito
            const existe = carrito.some(curso => curso.nombre === nombre);
            
            if (!existe) {
                carrito.push({ nombre, precio, img });
                mostrarCarrito();
                
                // Mostrar notificación
                const toast = new bootstrap.Toast(document.getElementById('toastAgregado'));
                toast.show();
            } else {
                alert('Este curso ya está en tu carrito');
            }
        });
    });

    // Botón Comprar
    if (btnComprar) {
        btnComprar.addEventListener('click', () => {
            modalPago.show();
        });
    }

    // Procesar pago
    document.getElementById('form-pago').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si el usuario está logueado
        fetch('php/verificar_sesion.php')
            .then(response => response.json())
            .then(data => {
                if (data.loggedin) {
                    // Enviar datos al servidor
                    const formData = new FormData();
                    formData.append('metodo_pago', document.getElementById('metodo-pago').value);
                    formData.append('carrito', JSON.stringify(carrito));
                    
                    fetch('php/procesar_pago.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Limpiar carrito y redirigir
                            carrito = [];
                            localStorage.removeItem('carrito');
                            mostrarCarrito();
                            modalPago.hide();
                            
                            // Mostrar notificación de éxito
                            const toast = new bootstrap.Toast(document.getElementById('toastCompra'));
                            toast.show();
                            
                            // Redirigir a mis cursos después de 2 segundos
                            setTimeout(() => {
                                window.location.href = 'php/mis_cursos.php';
                            }, 2000);
                        } else {
                            alert('Error al procesar el pago: ' + data.message);
                        }
                    });
                } else {
                    alert('Debes iniciar sesión para completar la compra');
                    window.location.href = 'login.php';
                }
            });
    });

    // Inicializar carrito
    mostrarCarrito();
});