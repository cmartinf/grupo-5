<template>
  <footer class="footer bg-blue-dark text-light py-4 mt-4 pt-4">
    <div class="container-fluid mx-5">
      <div class="row">
        
        <div class="col-md-4 mb-3">
          <h5 class="text-uppercase text-primary-light">Sobre Manager News:</h5>
          <p class="text-white-50 small">
            Manager News te conecta con el mundo.
            Informate con noticias claras, confiables y seleccionadas para vos que querés estar un paso adelante.
          </p>
        </div>

        <div class="col-md-4 mb-3">
          <h5 class="text-uppercase text-primary-light">Seguinos en:</h5>
          <div class="d-flex">
            <a href="https://www.facebook.com" target="_blank" class="text-light me-3" aria-label="Facebook">
              <i class="bi bi-facebook fs-4"></i>
            </a>
            <a href="https://twitter.com" target="_blank" class="text-light me-3" aria-label="Twitter">
              <i class="bi bi-twitter fs-4"></i>
            </a>
            <a href="https://www.instagram.com" target="_blank" class="text-light me-3" aria-label="Instagram">
              <i class="bi bi-instagram fs-4"></i>
            </a>
            <a href="https://www.linkedin.com" target="_blank" class="text-light" aria-label="LinkedIn">
              <i class="bi bi-linkedin fs-4"></i>
            </a>
          </div>
        </div>
      </div>
  
      <div class="text-center border-top border-blue pt-3">
        <p class="small text-white-50 mb-0">
          &copy; 2025 Manager News. Todos los derechos reservados.
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
};
</script>

<style scoped>
/* Azul oscuro moderno para el fondo */
.bg-blue-dark {
  background-color: #0b1f3a;
}

/* Azul claro para los títulos */
.text-primary-light {
  color: #6ea8fe;
}

/* Bordes azul suave */
.border-blue {
  border-color: #6ea8fe !important;
}

/* Hover de íconos y enlaces */
.footer a:hover {
  color: #6ea8fe !important;
}

.footer a {
  transition: color 0.3s ease;
}

.footer .bi {
  transition: transform 0.3s ease;
}

.footer .bi:hover {
  transform: scale(1.1);
}

/* Color de texto secundario */
.text-white-50 {
  color: rgba(255, 255, 255, 0.65) !important;
}
</style>

  