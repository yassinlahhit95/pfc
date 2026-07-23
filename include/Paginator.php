<?php
// Paginación servidor (LIMIT/OFFSET) para tablas que pueden crecer sin límite
// (historial/auditoría). Reutiliza exactamente las mismas clases CSS que la
// paginación cliente de public/js/core/paginacion.js (.paginacion-wrap,
// .pag-info, .pag-pages, .pag-btn...) para que ambas se vean idénticas —
// aquí los controles son <a href> normales en vez de onclick, porque cada
// página es una petición GET nueva al servidor, no una re-pintada en el DOM.
class Paginator
{
    // Misma lógica de elipsis que _buildPageNumbers() en paginacion.js —
    // mantenlas en sync si cambia una.
    public static function buildPageNumbers(int $current, int $total): array
    {
        if ($total <= 7) {
            return range(1, $total);
        }
        $pages = [1];
        $start = max(2, $current - 1);
        $end   = min($total - 1, $current + 1);
        if ($start > 2) $pages[] = '...';
        for ($i = $start; $i <= $end; $i++) $pages[] = $i;
        if ($end < $total - 1) $pages[] = '...';
        $pages[] = $total;
        return $pages;
    }

    // $urlBuilder recibe un número de página y devuelve la URL (ya debe
    // conservar cualquier filtro activo — normalmente ?pagina=N&...).
    public static function render(int $pagina, int $totalRegistros, int $porPagina, callable $urlBuilder): string
    {
        if ($totalRegistros === 0) return '';

        $totalPaginas = max(1, (int)ceil($totalRegistros / $porPagina));
        $pagina       = max(1, min($pagina, $totalPaginas));
        $desde        = ($pagina - 1) * $porPagina + 1;
        $hasta        = min($pagina * $porPagina, $totalRegistros);

        $html = '<div class="paginacion-wrap">';
        $html .= '<span class="pag-info">Mostrando <b>' . $desde . '–' . $hasta . '</b> de <b>' . $totalRegistros . '</b> entradas</span>';

        if ($totalPaginas > 1) {
            $html .= '<div class="pag-pages">';

            if ($pagina > 1) {
                $html .= '<a href="' . Security::escapeHtml($urlBuilder($pagina - 1)) . '" class="pag-btn pag-nav"><i class="fas fa-chevron-left"></i></a>';
            } else {
                $html .= '<span class="pag-btn pag-nav" disabled><i class="fas fa-chevron-left"></i></span>';
            }

            foreach (self::buildPageNumbers($pagina, $totalPaginas) as $num) {
                if ($num === '...') {
                    $html .= '<span class="pag-ellipsis">…</span>';
                } elseif ($num === $pagina) {
                    $html .= '<span class="pag-btn activo">' . $num . '</span>';
                } else {
                    $html .= '<a href="' . Security::escapeHtml($urlBuilder($num)) . '" class="pag-btn">' . $num . '</a>';
                }
            }

            if ($pagina < $totalPaginas) {
                $html .= '<a href="' . Security::escapeHtml($urlBuilder($pagina + 1)) . '" class="pag-btn pag-nav"><i class="fas fa-chevron-right"></i></a>';
            } else {
                $html .= '<span class="pag-btn pag-nav" disabled><i class="fas fa-chevron-right"></i></span>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}
