// ========================================
// GRÁFICOS DEL DASHBOARD
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  const chartCanvas = document.getElementById("mainChart");
  
  if (chartCanvas) {
    const ctx = chartCanvas.getContext("2d");
    
    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul"],
        datasets: [
          {
            label: "Estudiantes",
            data: [1150, 1180, 1200, 1220, 1210, 1225, 1234],
            borderColor: "#667eea",
            backgroundColor: "rgba(102, 126, 234, 0.1)",
            tension: 0.4,
            fill: true,
          },
          {
            label: "Profesores",
            data: [82, 84, 85, 86, 87, 88, 89],
            borderColor: "#10b981",
            backgroundColor: "rgba(16, 185, 129, 0.1)",
            tension: 0.4,
            fill: true,
          },
          {
            label: "Cursos",
            data: [40, 41, 42, 43, 44, 45, 45],
            borderColor: "#f59e0b",
            backgroundColor: "rgba(245, 158, 11, 0.1)",
            tension: 0.4,
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: "top",
          },
        },
        scales: {
          y: {
            beginAtZero: false,
            grid: {
              color: "rgba(0, 0, 0, 0.05)",
            },
          },
          x: {
            grid: {
              display: false,
            },
          },
        },
      },
    });
  }
});
