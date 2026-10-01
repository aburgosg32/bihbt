<table>
    <thead>
        <tr>
            <th>Cuenta</th>
            <th>HC</th>
            <th>Paciente</th>
            <th>Edad</th>
            <th>Modalidad</th>
            <th>Código</th>
            <th>Estudio</th>
            <th>Tipo Servicio</th>
            <th>Servicio</th>
            <th>Financiamiento</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($citas as $item)
        <tr>
            <td>{{ $item->id_cuenta_atencion }}</td>
            <td>{{ $item->nro_historia_clinica }}</td>
            <td>{{ $item->paciente }}</td>
            <td>{{ $item->edad }} {{ $item->tipo_edad }}</td>
            <td>{{ $item->modalidad }}</td>
            <td>{{ $item->codigo_estudio }}</td>
            <td>{{ $item->estudio }}</td>
            <td>{{ $item->tipo_servicio }}</td>
            <td>{{ $item->nombre_servicio }}</td>
            <td>{{ $item->fuente_financiamiento }}</td>
            <td>{{ date('d-m-Y', strtotime($item->fecha_cita)) }}</td>
            <td>{{ substr($item->hora_cita, 0, 5) }}</td>
            <td>{{ $item->estado }}</td>
        </tr>
        @endforeach
    </tbody>
</table>