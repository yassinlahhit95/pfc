// ========================================
// FUNCIONALIDAD DEL MENÚ LATERAL
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  const botonMenu = document.getElementById("botonMenu");
  const botonMenuExterno = document.getElementById("botonMenuExterno");
  const barraLateral = document.getElementById("barraLateral");
  const contenidoPrincipal = document.querySelector(".contenido-principal");

  // Toggle del menú
  const toggleMenu = () => {
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
      // Mobile: overlay menu
      barraLateral.classList.toggle("activo");
      const isOpen = barraLateral.classList.contains("activo");
      botonMenuExterno.style.opacity = isOpen ? "0" : "1";
      botonMenuExterno.style.pointerEvents = isOpen ? "none" : "auto";
    } else {
      // Desktop: slide menu
      barraLateral.classList.toggle("oculto");
      contenidoPrincipal?.classList.toggle("expandido");
      botonMenuExterno.classList.toggle("visible");
    }
  };

  // Event listeners para ambos botones
  if (botonMenu && botonMenuExterno && barraLateral) {
    botonMenu.addEventListener("click", toggleMenu);
    botonMenuExterno.addEventListener("click", toggleMenu);

    // Cerrar menú al hacer clic fuera (solo móvil)
    document.addEventListener("click", (e) => {
      if (window.innerWidth <= 768 && barraLateral.classList.contains("activo")) {
        const clickInside = barraLateral.contains(e.target) || 
                          botonMenu.contains(e.target) || 
                          botonMenuExterno.contains(e.target);
        
        if (!clickInside) {
          barraLateral.classList.remove("activo");
          botonMenuExterno.style.opacity = "1";
          botonMenuExterno.style.pointerEvents = "auto";
        }
      }
    });
  }

  // Submenús
  document.querySelectorAll(".tiene-submenu > .enlace-menu").forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      e.preventDefault();
      toggle.parentElement.classList.toggle("abierto");
    });
  });
});
