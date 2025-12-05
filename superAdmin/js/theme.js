// ========================================
// PERSONALIZACIÓN DE TEMA Y COLORES
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  const inputColor = document.getElementById("colorBarra");
  const botonReset = document.getElementById("resetColor");
  
  if (inputColor || botonReset) {
    const variableCss = "--fondo-barra";
    const colorPorDefecto = "#1e293b";

    const aplicarColor = (color) => {
      document.documentElement.style.setProperty(variableCss, color);
    };

    // Cargar color guardado
    const colorGuardado = localStorage.getItem("colorDashboard");
    if (colorGuardado) {
      aplicarColor(colorGuardado);
      if (inputColor) inputColor.value = colorGuardado;
    }

    // Cambio de color
    inputColor?.addEventListener("input", (e) => {
      aplicarColor(e.target.value);
      localStorage.setItem("colorDashboard", e.target.value);
    });

    // Reset
    botonReset?.addEventListener("click", () => {
      aplicarColor(colorPorDefecto);
      localStorage.removeItem("colorDashboard");
      if (inputColor) inputColor.value = colorPorDefecto;
    });
  }
});
