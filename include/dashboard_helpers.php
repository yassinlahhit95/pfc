<?php
if (!function_exists('saludoHorario')) {
    function saludoHorario(): string {
        $hora = (int)date('H');
        return $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    }
    function fechaLegibleHoy(): string {
        $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        return $dias[date('w')] . ', ' . date('j') . ' de ' . $meses[date('n') - 1];
    }
}
