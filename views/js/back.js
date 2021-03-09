const baseUrl = window.location.origin;
let urlFormAction;

$(function() {
    urlFormAction = $('#loyalty').attr('action');
    var rowObject;
    let excelFile;
    $('#promotions').change((e)=> {
        excelFile = e.target.files[0];
    });
    // Create loyalty program
    $(document).on("submit", "#loyalty", function(e) {
        e.preventDefault();

        const form = $(this);
        let formData = new FormData(document.getElementById("loyalty"));
        formData.append("submit", "addLoyaltyProgram");
        urlFormAction = form.attr('action');

        let action = $('#id_loyalty').val() > 0 ? 'update' : 'create';

        crudPrograms(action, formData);
    });
    // Update loyalty program
    $(document).on('click', '#tablePrograms .icon-edit', async function() {
        const id_program = $(this).data('id-program');
        const res = await fetch($('#loyalty').attr('action')+`?id_loyalty=${id_program}&submit=getLoyaltyProgramById`);

        if (res.ok) {
            $('#loyalty .btn-primary').text('Actualizar');
            $('.btn-clear').show();
            const program = await res.json();
            $('#id_loyalty').val(program[0].id_loyalty);
            $('#name').val(program[0].name);
            $('#description').val(program[0].description);
        } else {
            console.log(res.status)
        }
    });
    // Delete loyalty program
    $(document).on('click', '#tablePrograms .icon-trash', function() {
        const id_program = $(this).data('id-program');
        //const data = JSON.stringify({submit: 'removeLoyaltyProgram', id_loyalty: id_program});
        const formData = new FormData();
        formData.append("submit", "removeLoyaltyProgram");
        formData.append("id_loyalty", id_program);

        $(this).parent().parent().addClass('item-remove');

        crudPrograms('delete', formData);
    });

    $("#uploadAjaxPromotions").on("submit", function(e){
        e.preventDefault();

        if (excelFile) {
            let fileReader = new FileReader();
            fileReader.readAsBinaryString(excelFile);
            fileReader.onload = (event) => {
                let data = event.target.result;
                let workbook = XLSX.read(data, {type: 'binary'});
                workbook.SheetNames.forEach(sheet => {
                    rowObject = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheet]);
                    console.log(rowObject[0].LEYENDAS)
                })
            }
        }


        // const form = $(this);
        // let formData = new FormData(document.getElementById("uploadAjaxPromotions"));
        // formData.append("submit", "savePromotions");

        // $.ajax({
        //     url: form.attr('action'),
        //     type: "post",
        //     dataType: "html",
        //     data: formData,
        //     cache: false,
        //     contentType: false,
        //     processData: false,
        //     beforeSend: function() {
        //         form.find('button').attr('disabled', true).text('Registrando promociones ...');
        //     },
        // })
        // .done(function(res){
        //     console.log(res)

        //     form.find('button').attr('disabled', false).text('Subir promociones');
        // });
    });

    $(document).on('click', '.loyalty-programs-actions .icon-cog', function() {
        $('#modalLoyatyProgram').modal();
    });

    // Properties loyalty programs
    $('#newProperty').on('click', () => $('#formProgramProperty').show() );

    $("#modalLoyatyProgram").on("hidden.bs.modal", function () {
        document.getElementById("formAddPropertyProgram").reset();
    });

    $("#propElement").on('change',function() {
        showPreviewProperty();
        if ($(this).val() != 'a')
            $('.propDivUrl').hide();
        else 
            $('.propDivUrl').show();
    });

    $('#propText, #propUrl').keyup(() => showPreviewProperty() );

    showPreviewProperty();
});

const showPreviewProperty = () => {
    let element = $('#propElement').val();
    let text = $('#propText').val() == '' ? $('#propText').attr('placeholder') : $('#propText').val();
    let url = $('#propUrl').val() == '' ? $('#propUrl').attr('placeholder') : $('#propUrl').val();
    let urlTarget = $('#propUrlTarget').val();
    let startLorem = '<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. ';
    let endLorem = ' perspiciatis earum ullam magni obcaecati quis explicabo esse.</p>';
    let html = startLorem;

    //if (url.slice(0, 1) == '/')
    url = baseUrl + url;

    switch (element) {
        case 'a':
            html += `<a href="${url}" title="${text}" target="${urlTarget}">${text}</a>`;
            break;
        case 'strong':
            html += `<strong>${text}</strong>`;
            break;
        default:
            break;
    }

    $('.prop-preview').html(html+endLorem);
}

const crudPrograms = (action, data) => {
    $.ajax({
        url: urlFormAction,
        type: "post",
        dataType: "json",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
            //form.find('button').attr('disabled', true).text('Registrando programa ...');
        },
    })
    .done(function(res) {
        console.log(res.success)
        if (res.success) {
            document.getElementById("loyalty").reset();

            if (action === 'delete')
                $('#tablePrograms').find('.item-remove').parent().parent().remove();

            if (action === 'create' || action === 'update' && res.data)
                addRowTable('tablePrograms', res.data, action);
        }

        //form.find('button').attr('disabled', false).text('Crear');
    });

}

const addRowTable = (idTable, data, action) => {
    console.log(data);
    let tableBody = $(`#${idTable}`).find('tbody');
    let columnStatus = '';

    let iconActions = `<div class="col-md-3"><i  data-id-program="${data[0].id_loyalty}" title="Eliminar" class="icon icon-trash"></i></div>`;

    if (idTable === 'tablePrograms') {
        let iconStatusClass = data[0].active ? 'enabled' : 'disabled';
        let iconStatusText = data[0].active ? 'check' : 'clear';

        columnStatus = `<td>
            <i class="material-icons action-${iconStatusClass}">${iconStatusText}</i>
        </td>`;

        iconActions = `<div class="col-md-3"><i data-id-program="${data[0].id_loyalty}" title="Ajustes" class="icon icon-cog"></i></div>
        <div class="col-md-3"><i data-id-program="${data[0].id_loyalty}" title="Borras todas la promociones de este programa" class="icon icon-remove-circle"></i></div>
        <div class="col-md-3"><i data-id-program="${data[0].id_loyalty}" title="Editar" class="icon icon-edit"></i></div>${iconActions}`;
    }

    const row = `<tr>
        <td>${data[0].id_loyalty}</td>
        <td>${data[0].name}</td>
        <td>${data[0].description}</td>
        ${columnStatus}
        <td>
            <div class="row loyalty-programs-actions">
                ${iconActions}
            </div>
        </td>
    </tr>`;

    if (action === 'update') {
        $(row).insertAfter(`.program-${data[0].id_loyalty}`);
        $(`.program-${data[0].id_loyalty}:first`).remove();
        clearFormLoyanty();
    } else {
        $(tableBody).append(row);
    }
}

const deleteProgram = (idProgram) => {
    console.log(idProgram);
}

const clearFormLoyanty = () => {
    $('#id_loyalty').val('');
    $('#loyalty .btn-primary').text('Crear');
    $('button[type="reset"]').hide();
}