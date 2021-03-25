const baseUrl = window.location.origin;

$(function() {
    /********* LOYALTY PROGRAMS *********/
    // Create & Update loyalty program
    document.getElementById('loyalty').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(document.getElementById("loyalty"));

        Loyalty.save(formData)
        .then(res => res.json())
        .then(res => {
            if (res.id) {
                const loyalty = new Loyalty(
                    res.id,
                    res.name,
                    res.description,
                    null,
                    res.active,
                    null
                    );
                
                if (parseInt(document.getElementById('id_loyalty').value) > 0) {
                    loyalty.updateRow();
                } else {
                    loyalty.addRow();
                    select = document.getElementById('uploadProgram');
                    opt = document.createElement("option");
                    opt.value = res.id;
                    opt.text = res.name;
                    select.add(opt);
                }

                Loyalty.clearForm();
            }
        })
        .catch(error => console.error('Error:', error));
    });
    // Btn Cancelar
    const btnCancelEditProgram = document.getElementById('btnLoyaltyReset');
    btnCancelEditProgram.addEventListener('click', () => {
        Loyalty.clearForm();
    });
    // Properties Loyalty Programs
    document.getElementById('formAddPropertyProgram').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(document.getElementById("formAddPropertyProgram"));
        formData.append('properties', 'save');

        Loyalty.save(formData)
        .then(res => res.json())
        .then(res => {
            if (res.id) {
                const loyalty = new Loyalty(
                    res.id,
                    res.name,
                    res.description,
                    res.html_tags,
                    res.active,
                    res.date_end
                    );
                Loyalty.deleteRowsproperties();
                loyalty.addRowsProperties();
                Loyalty.resetFormProperties();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('newProperty').addEventListener('click', () => {
        document.getElementById('formProgramProperty').style.display = 'block';
        Loyalty.resetFormProperties();
    });

    $("#modalConfigProgram").on("hidden.bs.modal", function () {
        document.getElementById('formProgramProperty').style.display = 'none';
        document.getElementById('id_loyalty_property').value = '';
        Loyalty.deleteRowsproperties();
        Loyalty.resetFormProperties();
    });

    document.getElementById('propElement').addEventListener('change', e => {
        Loyalty.renderPreviewProperty();
        if (e.target.value != 'a')
            document.querySelector('.propDivUrl').style.display = 'none';
        else 
            document.querySelector('.propDivUrl').style.display = 'block';
    });

    document.getElementById('propText').addEventListener('keyup', () => Loyalty.renderPreviewProperty());
    document.getElementById('propUrl').addEventListener('keyup', () => Loyalty.renderPreviewProperty());

    /********* LOYALTY PROMOTIONS *********/
    // document.getElementById('promotions').addEventListener('change', e => {
        
    // });
    document.getElementById('uploadAjaxPromotions').addEventListener('submit', e => {
        e.preventDefault();
        const id_loyalty = document.getElementById('uploadProgram').value;
        const excelPromotions = document.getElementById('promotions').files[0];

        LoyaltyPromotions.convertExcelToJson(excelPromotions)
        .then(res => {
            const totalPromotions = res.length;
            let progress = 0;
            for (let key in res) {
                if (res.hasOwnProperty(key)) {
                    const promotion = res[key];
                    let formData = new FormData();
                    formData.append('id_loyalty', id_loyalty);
                    if (res[key].hasOwnProperty('id_product'))
                        formData.append('id_product', promotion.id_product);
                    if (res[key].hasOwnProperty('ean13'))
                        formData.append('ean13', promotion.ean13);
                    if (res[key].hasOwnProperty('promotion')) {
                        if (typeof(promotion.promotion) == 'number' && promotion.promotion < 1) {
                            formData.append('promotion', `${promotion.promotion*100}%`);
                        } else {
                            formData.append('promotion', promotion.promotion);
                        }
                    }
                    if (res[key].hasOwnProperty('description'))
                        formData.append('description', promotion.description);
                    
                    LoyaltyPromotions.save(formData)
                    .then(res => res.json())
                    .then(res => {
                        progress = ((parseInt(key) + 1) * 100) / totalPromotions;
                        document.querySelector('.progress-bar').innerText = `${progress.toFixed()}%`
                        document.querySelector('.progress-bar').style.width = `${progress.toFixed()}%`
                        console.log(res)
                    })
                    .catch(error => console.log('Error:', error));

                    if (key == 2)
                        return;
                }
            }
        })
        .catch(error => console.log('Error:', error));
    });
});

/********* FUNCTIONS LOYALTY PROGRAMS *********/
// Get program by id
getDataProgram = id => {
    Loyalty.getById(id)
    .then(res => res.json())
    .then(res => {
        if(res.id){
            document.getElementById('id_loyalty').value = res.id;
            document.getElementById('name').value = res.name;
            document.getElementById('description').value = res.description;
            document.getElementById('btnLoyaltyReset').style.display = 'block';
            document.getElementById('btnLoyaltySubmit').innerText = 'Actualizar';
        }
    })
    .catch(error => console.log('Error:', error));
}
// Delete program
const deleteProgram = id => {
    if (confirm(`Se va a eliminar el Programa de lealtad con id: ${id}`)) {
        const formData = new FormData();
        formData.append("id_loyalty", id);

        Loyalty.remove(formData)
        .then(res => res.json())
        .then(res => {
            if (res.success)
                document.querySelector(`.program-${id}`).remove();
                Array.from(document.getElementById('uploadProgram').options).forEach(option => {
                    if (option.value == id) {
                        option.remove(option.index);
                    }
                });
        })
        .catch(error => console.log('Error:', error));
    }
}
// show Modal properties program
const configLoyalty = id => {
    document.getElementById('id_loyalty_property').value = id;
    Loyalty.getById(id)
    .then(res => res.json())
    .then(res => {
        if(res.id){
            const loyalty = new Loyalty(
                res.id,
                res.name,
                res.description,
                res.html_tags,
                res.active,
                res.date_end
                );
            
            loyalty.addRowsProperties();
        }
    })
    .catch(error => console.log('Error:', error));
    $('#modalConfigProgram').modal();
}
// Delete properties of program
const deletePropertyProgram = (id, idLoyalty) => {
    if (confirm('Se va a eliminar la propiedad con id: '+id)) {
        const formData = new FormData(document.getElementById("formAddPropertyProgram"));
        formData.append('properties', 'delete');
        formData.append('id_property', id);
        formData.append('id_loyalty', idLoyalty);

        Loyalty.save(formData)
        .then(res => res.json())
        .then(res => {
            document.querySelector('.property-'+id).remove();
        })
        .catch(error => console.error('Error:', error));
    }
}
// Delete all promotions of program
const deletePromotionsByLoyalty = idLoyalty => {
    if (confirm('Se van a eliminar todas las promociones del programa de lealtad con id: '+idLoyalty)) {
        const formData = new FormData();
        formData.append('id_loyalty', idLoyalty);
        LoyaltyPromotions.deleteByLoyalty(formData)
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert('Las promociones se eliminaron.');
            } else {
                alert('Error al eliminar las promociones.')
                console.log(res.error);
            }
        })
        .catch(error => console.log('Error:', error));
    }
}