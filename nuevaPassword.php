<?php
require_once "includes/header.php"; 

if(!isset($_GET['token']) || empty($_GET['token'])){
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$token = htmlspecialchars($_GET['token']);
?>

<main class="container my-5 py-5" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card rounded-0 border-0 shadow-lg" style="background-color: var(--color-secundario);">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <i class="bi bi-key fs-1 mb-2 d-block" style="color: var(--color-principal);"></i>
                        <h2 class="fw-bold text-uppercase tracking-wide" style="color: var(--color-principal); letter-spacing: 2px;">Nueva Contraseña</h2>
                        <p class="text-muted small">Crea una nueva contraseña para tu cuenta de HERROR.</p>
                    </div>

                    <form action="controllers/nuevaPasswordController.php" method="POST" id="formNuevaPassword">
                        <input type="hidden" name="accion" value="actualizarPassword">
                        <input type="hidden" name="token" value="<?php echo $token; ?>">

                        <div class="mb-4 position-relative input-box">
                            <input type="password" class="form-control rounded-0 border-0 border-bottom" id="password" name="password" required>
                            <label for="password">Nueva Contraseña</label>
                            <i class="bi bi-lock"></i>
                            <div class="form-text mt-2 small text-muted">
                                Mínimo 8 caracteres, una mayúscula, una minúscula y un número.
                            </div>
                        </div>

                        <div class="mb-5 position-relative input-box">
                            <input type="password" class="form-control rounded-0 border-0 border-bottom" id="confirm_password" name="confirm_password" required>
                            <label for="confirm_password">Confirmar Contraseña</label>
                            <i class="bi bi-lock-fill"></i>
                        </div>

                        <button type="submit" class="btn btn-principal w-100 fw-bold text-uppercase rounded-0 py-3" style="letter-spacing: 1px;">Guardar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const swalRapido = Swal.mixin({
            confirmButtonColor: 'var(--color-principal, #000)',
            showClass: { popup: 'animate__animated animate__fadeIn animate__faster' },
            hideClass: { popup: 'animate__animated animate__fadeOut animate__faster' }
        });

        document.getElementById('formNuevaPassword').addEventListener('submit', function(e) {
            const pass1 = document.getElementById('password').value;
            const pass2 = document.getElementById('confirm_password').value;
            
            const patronPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;

            if (!patronPassword.test(pass1)) {
                e.preventDefault();
                swalRapido.fire({
                    icon: 'warning',
                    title: 'Contraseña poco segura',
                    text: 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula y un número.'
                });
                return;
            }

            if (pass1 !== pass2) {
                e.preventDefault();
                swalRapido.fire({
                    icon: 'error',
                    title: 'No coinciden',
                    text: 'Las contraseñas que has escrito no son iguales.'
                });
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('error')) {
            let error = urlParams.get('error');
            if(error === 'tokenCaducado') {
                swalRapido.fire({
                    icon: 'warning',
                    title: 'Tiempo Agotado',
                    text: 'El enlace ha caducado. Disponías de 1 hora. Por favor, solicita un nuevo correo.'
                });
            } else if(error === 'tokenInvalido') {
                swalRapido.fire({
                    icon: 'error',
                    title: 'Enlace no válido',
                    text: 'Este enlace ya ha sido utilizado o no existe.'
                });
            } else if(error === 'datosInvalidos') {
                swalRapido.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Los datos enviados no son válidos.'
                });
            }
        }
    });
</script>

<?php require_once "includes/footer.php"; ?>