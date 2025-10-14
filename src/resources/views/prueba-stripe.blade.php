<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Tienda de Calzados</title>
    <script src="https://js.stripe.com/clover/stripe.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #555;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            font-weight: bold;
            font-size: 20px;
            color: #333;
        }

        #payment-element {
            margin: 30px 0;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .powered-by {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }

        .stripe-badge {
            display: inline-block;
            margin-left: 5px;
            font-weight: 600;
            color: #635bff;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner-center {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <h1>💳 Finalizar Compra</h1>
        <p class="subtitle">Completa tu pago de forma segura</p>

        <div class="order-summary">
            <h3 style="margin-bottom: 15px; color: #333;">Resumen del Pedido</h3>
            <div class="summary-item">
                <span>Subtotal:</span>
                <span id="subtotal">€0.00</span>
            </div>
            <div class="summary-item">
                <span>Descuento:</span>
                <span id="descuento">€0.00</span>
            </div>
            <div class="summary-item">
                <span>Envío:</span>
                <span id="envio">€0.00</span>
            </div>
            <div class="summary-total">
                <span>Total:</span>
                <span id="total">€0.00</span>
            </div>
        </div>

        <form id="payment-form">
            <div id="payment-element"></div>

            <button type="submit" id="submit-btn" class="btn">
                <span id="button-text">Pagar Ahora</span>
            </button>

            <div id="payment-message" class="message"></div>
        </form>

        <div class="powered-by">
            Pago seguro mediante <span class="stripe-badge">Stripe</span>
        </div>
    </div>

    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner-center"></div>
    </div>
    <script>
        // ==========================================
        // CONFIGURACIÓN
        // ==========================================
        const STRIPE_PUBLIC_KEY =
            'pk_test_51SFelaCVle5n45gv8bMw5NioXumEJnuuAQvPASe2fjGVH4NsS6DBWeB9SshUho3ZAcunfaKCKkEPH5Z9YpReXoXe00TbeMomgn';
        const API_URL = 'http://localhost:8000/api';
        const AMOUNT = 50; // Cantidad a pagar en euros (prueba)

        // ==========================================
        // ESTADO GLOBAL
        // ==========================================
        let stripe;
        let elements;
        let clientSecret;
        let isProcessing = false;

        // ==========================================
        // INICIALIZACIÓN
        // ==========================================
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                console.log('🚀 Inicializando Stripe...');

                // Inicializar Stripe
                stripe = Stripe(STRIPE_PUBLIC_KEY);

                // Crear Payment Intent en el backend
                await createTestPaymentIntent();

                // Crear elementos de Stripe
                elements = stripe.elements({
                    clientSecret: clientSecret,
                    appearance: {
                        theme: 'stripe',
                        variables: {
                            colorPrimary: '#667eea',
                        }
                    }
                });

                // Montar Payment Element
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');
                console.log('✅ Payment Element montado');

                // Actualizar UI con datos de prueba
                updateTestOrderData();

                // Event listener para el formulario
                const form = document.getElementById('payment-form');
                form.addEventListener('submit', handleSubmit);

            } catch (error) {
                console.error('❌ Error de inicialización:', error);
                showMessage('Error al inicializar el pago: ' + error.message, 'error');
            }
        });

        // ==========================================
        // CREAR PAYMENT INTENT DE PRUEBA
        // ==========================================
        async function createTestPaymentIntent() {
            try {
                console.log('📡 Creando Payment Intent...');

                // Preparar datos de prueba
                const testOrderData = {
                    envio_nombre: 'Cliente Prueba',
                    envio_email: 'prueba@test.com',
                    envio_telefono: '600000000',
                    envio_direccion_calle: 'Calle de Prueba',
                    envio_direccion_numero_calle: '123',
                    envio_direccion_ciudad: 'Madrid',
                    envio_direccion_cp: '28001',
                    usar_misma_direccion_facturacion: true,
                    metodo_pago: 'stripe',
                    // Items de prueba (simulando un carrito)
                    items: [{
                        variant_size_id: 76,
                        cantidad: 1
                    }]
                };

                // Llamar al endpoint para crear un pedido de prueba
                const response = await fetch(`${API_URL}/orders/guest`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(testOrderData)
                });

                if (!response.ok) {
                    console.log(response.message);
                    throw new Error(`Error HTTP ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Error creando pedido de prueba');
                }

                console.log('✅ Pedido creado:', data.data.id);
                console.log('✅ Client Secret obtenido');

                // Guardar datos necesarios
                clientSecret = data.payment.clientSecret;
                window.testOrderId = data.data.id;
                window.testPaymentIntentId = data.payment.paymentIntentId;

            } catch (error) {
                console.error('❌ Error creando Payment Intent:', error);
                showMessage('Error al crear el pago de prueba: ' + error.message, 'error');
                throw error;
            }
        }

        // ==========================================
        // ACTUALIZAR UI CON DATOS DE PRUEBA
        // ==========================================
        function updateTestOrderData() {
            const subtotal = AMOUNT - 5; // Restar envío
            const descuento = 0;
            const envio = 5;
            const total = AMOUNT;

            document.getElementById('subtotal').textContent = `€${subtotal.toFixed(2)}`;
            document.getElementById('descuento').textContent = `-€${descuento.toFixed(2)}`;
            document.getElementById('envio').textContent = `€${envio.toFixed(2)}`;
            document.getElementById('total').textContent = `€${total.toFixed(2)}`;

            console.log('💰 Datos de prueba actualizados:', {
                subtotal,
                descuento,
                envio,
                total
            });
        }

        // ==========================================
        // MANEJAR ENVÍO DEL FORMULARIO
        // ==========================================
        async function handleSubmit(event) {
            event.preventDefault();

            if (isProcessing) return;
            isProcessing = true;

            setLoading(true);
            disableButton(true);

            try {
                console.log('🔄 Procesando pago...');

                // Confirmar el pago con Stripe
                const result = await stripe.confirmPayment({
                    elements,
                    redirect: "if_required", // evita redirecciones
                });

                console.log("estes el Result despues de confirmPayment",result);
                
                if (result.error) {
                    console.error('❌ Error de Stripe:', error);
                    showMessage(`Error: ${error.message}`, 'error');
                }
                else if (result.paymentIntent.status === "succeeded") {
                    console.log('✅ Pago procesado exitosamente');
                    showMessage('¡Pago procesado correctamente!', 'success');

                    // Confirmar pago en el backend
                    await confirmPaymentBackend(window.testOrderId, window.testPaymentIntentId);
                }

            } catch (err) {
                console.error('❌ Error en handleSubmit:', err);
                showMessage('Error al procesar el pago: ' + err.message, 'error');
            } finally {
                setLoading(false);
                disableButton(false);
                isProcessing = false;
            }
        }

        // ==========================================
        // CONFIRMAR PAGO EN EL BACKEND
        // ==========================================
        async function confirmPaymentBackend(orderId, paymentIntentId) {
            try {
                console.log('📡 Confirmando pago en backend...');

                const response = await fetch(`${API_URL}/orders/${orderId}/confirmar-pago`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntentId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    console.log('✅ Pago confirmado en backend');
                    showMessage('¡Pedido confirmado! Redirigiendo...', 'success');

                    // Redirigir a página de éxito
                    setTimeout(() => {
                        console.log("todo completdo y esto seria la REDIRECCIOn");

                    }, 2000);
                } else {
                    console.error('❌ Error confirmando:', data.message);
                    showMessage('Error al confirmar el pago: ' + data.message, 'error');
                }

            } catch (error) {
                console.error('❌ Error confirmando pago:', error);
                showMessage('Error al confirmar el pago: ' + error.message, 'error');
            }
        }

        // ==========================================
        // FUNCIONES AUXILIARES
        // ==========================================

        function showMessage(message, type) {
            const messageDiv = document.getElementById('payment-message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type} show`;

            console.log(`📢 ${type.toUpperCase()}: ${message}`);

            if (type === 'error') {
                setTimeout(() => {
                    messageDiv.classList.remove('show');
                }, 5000);
            }
        }

        function setLoading(isLoading) {
            const overlay = document.getElementById('loading-overlay');
            if (isLoading) {
                overlay.classList.add('show');
                console.log('⏳ Cargando...');
            } else {
                overlay.classList.remove('show');
            }
        }

        function disableButton(disabled) {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = disabled;

            if (disabled) {
                submitBtn.innerHTML = '<span class="spinner"></span><span id="button-text">Procesando...</span>';
            } else {
                submitBtn.innerHTML = '<span id="button-text">Pagar Ahora</span>';
            }
        }

        // Manejar cambios en el estado del pago
        document.addEventListener('load', () => {
            const observer = new MutationObserver(() => {
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = false; // Permitir envío
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
</body>

</html>
