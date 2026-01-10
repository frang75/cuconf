// Simulación de Base de Datos de artículos
const articulos = [

    {
        titulo: "¿Qué es el acoso escolar?",
        resumen: "¿Cómo saber si estoy sufiendo acoso?",
        imagen: "./img/imagen_1_acoso_escolar.png",
        link: "articulo01.html"
   
    },
    {
        titulo: "Ansiedad",
        resumen: "¿Qué és y cuántos tipos hay?",
        imagen: "./img/imagen_2_ansiedad.png",
        link: "articulo03.html"
    },
    {
        titulo: "Estrés postraumático",
        resumen: "¿Qué és y cuántos tipos hay?",
        imagen: "./img/imagen_4_eestrées_postraumático.png",
        link: "articulo04.html"
    },
    {
        titulo: "Depresión",
        resumen: "¿Qué és y cuántos tipos hay?",
        imagen: "./img/imagen_5_depresión.png",
        link: "articulo05.html"
    },
    {
        titulo: "Trastornos alimenticios (TCA)",
        resumen: "¿Qué és y cuántos tipos hay?",
        imagen: "./img/imagen_6_TCA.png",
        link: "articulo06.html"
    },
    {
        titulo: "TDAH",
        resumen: "¿Qué és y cuántos tipos hay?",
        imagen: "./img/imagen_7_TDAH.png",
        link: "articulo07.html"
    }
];

// Función para renderizar (mostrar) los artículos en el HTML
const contenedorBlog = document.getElementById('blog-container');

// Solo ejecutamos esto si estamos en la página de inicio (donde existe el contenedor)
if (contenedorBlog) {
    articulos.forEach(articulo => {
        // Creamos el HTML de cada tarjeta
        const tarjetaHTML = `
            <article class="card">
                <img src="${articulo.imagen}" alt="${articulo.titulo}" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">${articulo.titulo}</h3>
                    <p class="card-excerpt">${articulo.resumen}</p>
                    <a href="${articulo.link}" class="read-more">Leer artículo completo &rarr;</a>
                </div>
            </article>
        `;

        // Lo añadimos al contenedor
        contenedorBlog.innerHTML += tarjetaHTML;
    });
}
