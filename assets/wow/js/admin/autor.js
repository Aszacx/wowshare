//Gestión de Autores 
    //Abrir modal de Gestionar Autores
    $("#gestion-autor").on("click",function(){
        $("#title-autores").text("Gestión de Autores");
        $("#gestionar-autores").modal({
            show:true,
            backdrop:"static"
        });
    });

    //Agregar Autor
    $("body").on("click", "#nuevo-autor", function(){
        $("#title-autor").text("Nuevo Autor");
        $("#btn-autor").removeClass('btn-warning');
        $("#btn-autor").addClass('btn-success').val("Guardar");
        $("#form-autor")[0].reset();
        $("#form-autor").attr("action", "admin/agregarAutor");
        $("#modal-autor").modal({
            show:true,
            backdrop:"static"
        });
    });

    //Editar Autor
    $("body").on("click","#tabla-autores a",function(event) {
        event.preventDefault(); 
        idAutor = $(this).attr("href");       
        editarAutor(idAutor); 
    });

    $("#form-autor").formValidation({
        framework: "bootstrap",
        container: 'tooltip',
        icon: {
            valid: "glyphicon",
            invalid: "glyphicon",
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
            autor: {
                validators: {
                    notEmpty: {
                        message: "Nombre de autor vacío."
                    },
                    stringLength: {
                        min: 5,
                        max: 30,
                    },
                    blank: {}
                }
            }
        }
    })
    .on("success.form.fv", function(e) {
        e.preventDefault();

        var $form = $(e.target),
            fv    = $form.data("formValidation");

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: $form.serialize(),
            dataType: "JSON"
        }).success( function(response) {
            $("#msj-autor").removeClass();
            if(response === true && $("#btn-autor").val() == "Guardar") {
                $("#form-autor")[0].reset();
                $("#modal-autor").modal("hide");
                listarAutor();
                gestionarAutores("", 1);
                $("#msj-autor").addClass("alert text-center alert-success alert-accion").html("Autor registrado.").show(100).delay(3500).hide(100);
            }
            else if(response === true && $("#btn-autor").val() == "Editar") {
                $("#form-autor")[0].reset();
                $("#modal-autor").modal("hide");
                listarAutor();
                gestionarAutores("", 1);
                $("#msj-autor").addClass("alert text-center alert-warning alert-accion").html("Autor editado.").show(100).delay(3500).hide(100);
            } else {
                $("#msj-autor").addClass("alert text-center alert-danger alert-accion").html("Error al registrar.").show(100).delay(3500).hide(100);
            }
        });
    });

    //Buscar Autor
    $("#buscar-autor").keyup(function(){
        buscar = $("#buscar-autor").val();
        gestionarAutores(buscar, 1);
    });
    
    //Eliminar Autor
    $("body").on("click", "#tabla-autores #eliminar-autor", function(event){
        $("#msj-autor").removeClass();
        idAutor = $(this).attr("value");
        eliminarAutor(idAutor);
    });

    //Paginación
    $("body").on("click", "#paginacion-autores li a", function(e){
        e.preventDefault();
        valor = $(this).attr("href");
        buscar = $("#buscar-autor").val();
        gestionarAutores(buscar, valor);
    });

//Gestión de Autores
function gestionarAutores(buscar, pagina){
    $.ajax({
        type: "POST",
        url: "admin/gestionarAutores",
        cache: false,
        data: {buscar_autor: buscar, pagina_autor: pagina},
        dataType: "JSON",
    }).success( function(datos){
        html = "<table class='table table-bordered'><thead>";
        html += "<tr><th>ID</th><th>Autor</th><th>Acciones</th></tr>";
        html += "</thead><tbody>";
        $.each(datos.autor, function (key, item){
            html += "<tr><td>"+item.id+"</td><td>"+item.autor+"</td><td>"; 
            html += " <a href="+item.id+" title='Editar' class='btn btn-primary btn-xs'><i class='glyphicon glyphicon-pencil'></i></a>"; 
            html += " <button type='button' id='eliminar-autor' title='Eliminar' class='btn btn-danger btn-xs' value="+item.id+"><i class='glyphicon glyphicon-trash'></i></button></td></tr>";
        });
        html +="</tbody></table>";
        $("#tabla-autores").html(html);
        total_registros = datos.total_registros;
        cantidad = datos.cantidad;
        paginarRegistros(pagina, total_registros, cantidad);
        $("#paginacion-autores").html(paginador);
    });
}

function listarAutor(){
    $.ajax({
        type:"POST",
        url:"admin/listarAutor",
        cache: false,
        dataType: "JSON",
        success: function(datos){
            html = "<label class='input-group-addon'>Autor:</label>";
            html += "<select id='autor' name='autor' class='form-control'>";                  
            html += "<option value=''>Selecciona Autor</option>";
            $.each(datos, function (key, item) {
                html += "<option value="+item.id+">"+item.autor+"</option>";
            });
            html += "</select>";
            html += "<span class='input-group-btn'><button class='btn btn-primary' type='button' id='nuevo-autor'><i class='fa fa-plus'></i></button></span>";
            $("#lista-autor").html(html);
        }
    });
}

function editarAutor(id){
    $.ajax({
        type: "POST",
        url: "admin/editarAutor",
        cache: false,
        data: {idAutor:id},
        dataType: "JSON"
    }).success( function(datos) {
            var datos = eval(datos);
            $("#form-autor")[0].reset();
            $("#form-autor").attr("action", "admin/actualizarAutor");
            $("#title-autor").text("Editar Autor");
            $("#btn-autor").addClass("btn-warning").val("Editar");
            $("#idAutor").val(idAutor);
            $("#edit-autor").val(datos[0]);
            $("#modal-autor").modal({
                show:true,
                backdrop:"static"
            });
    });
}

function eliminarAutor(id){
    var pregunta = confirm("¿Esta seguro de eliminar este autor?");
    if(pregunta == true){
        $.ajax({
            type: "POST",
            url: "admin/eliminarAutor",
            cache: false,
            data: "idAutor= "+id,
            dataType: "JSON"
        }).success( function(response) {
            if(response === true) {
                listarAutor();
                gestionarAutores("", 1);
                $("#msj-autor").addClass("alert text-center alert-danger alert-accion").html("Registro eliminado.").show(100).delay(3500).hide(100);
            } else {
                $("#msj-autor").addClass("alert text-center alert-danger alert-accion").html("Error al eliminar.").show(100).delay(3500).hide(100);
            }
        });
    }
}