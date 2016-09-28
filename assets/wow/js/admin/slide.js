//Gestión de Slides
    //Eliminar Slide
    $("body").on("click",".slides #eliminar-slide",function(event){
        id = $(this).attr("value");
        tabla = "slide";
        eliminarRegistro(id, tabla);
    });
    
    //Estatus de Slide
    $("body").on("click",".slides #estatus-slide",function(event){
        id = $(this).attr("value"); 
        tabla = "slide";      
        estatusRegistro(id, tabla); 
    });

    //Paginación
    $("body").on("click","#paginacion-slides li a", function(e){
        e.preventDefault();
        valor = $(this).attr("href");
        buscar = 1;
        gestionarSlides(buscar, valor);
    });
    
//Gestión de Slides
function gestionarSlides(buscar, pagina){
    $.ajax({
        type:"POST",
        url:"admin/leerDatos",
        cache: false,
        data: {buscar_slide:buscar, pagina_slide:pagina, tabla: "slide"},
        dataType: "JSON"
    }).success( function(datos){
            html = "<table class='table table-bordered'><thead>";
            html += "<tr><th>Nombre</th><th>Slide</th><th>Acciones</th></tr>";
            html += "</thead><tbody>";
            $.each(datos.registros, function (key, item){
                html += "<td>"+item.nombre+"</td><td><img src=../"+item.miniatura+"></td>";
                html += "<td><button type='button' id='estatus-slide' title='Estatus' class='btn btn-xs' value="+item.id+">"+item.estatus+"</button>"; 
                html += " <button type='button' id='eliminar-slide' title='Eliminar' class='btn btn-danger btn-xs' value="+item.id+"><i class='glyphicon glyphicon-trash'></i></button></td></tr>";
            });
            html +="</tbody></table>";
            switch(buscar) {
                case 1:
                    $("#tabla-slides").html(html);
                    total_registros = datos.total_registros;
                    cantidad = datos.cantidad;
                    paginarRegistros(pagina, total_registros, cantidad);
                    $("#paginacion-slides").html(paginador);
                break;
                case 2:
                    $("#publicidad-300x300").html(html);
                    total_registros = datos.total_registros;
                    cantidad = datos.cantidad;
                    paginarRegistros(pagina, total_registros, cantidad);
                    $("#paginacion-publi-uno").html(paginador);
                break;
                case 3:
                    $("#publicidad-1000x150").html(html);
                    total_registros = datos.total_registros;
                    cantidad = datos.cantidad;
                    paginarRegistros(pagina, total_registros, cantidad);
                    $("#paginacion-publi-dos").html(paginador);
                break;
            }
    });
}

function agregarSlide(){
    var formData = new FormData($("#form-slide")[0]);
    //var seleccion = $("#slides").val();
    //formData.append(seleccion);
    $.ajax({
        type: "POST",
        url: "admin/createSlide",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "JSON"
    }).success( function(response){
        $(".msj-slide").removeClass();
        if(response === true) {
            $("#form-slide")[0].reset();
            gestionarSlides(1, 1);
            gestionarSlides(2, 1);
            gestionarSlides(3, 1);
            $("#msj-slide").addClass("alert text-center alert-success alert-accion").html("Imágen subida correctamente.").show(100).delay(3500).hide(100);
        }
        else{
            $("#msj-slide").addClass("alert text-center alert-danger alert-accion").html("Error al subir imágen.").show(100).delay(3500).hide(100);
        }
    });
}