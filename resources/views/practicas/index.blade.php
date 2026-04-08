<h1>Prácticas Empresariales 🚀</h1>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
        </tr>
    </thead>
    <tbody id="tabla">
    </tbody>
</table>

<script>
fetch("{{ route('practicas.data') }}")
.then(res => res.json())
.then(data => {
    let tabla = document.getElementById("tabla");

    data.forEach(item => {
        tabla.innerHTML += `
            <tr>
                <td>${item.id}</td>
                <td>${item.nombre}</td>
            </tr>
        `;
    });
});
</script>