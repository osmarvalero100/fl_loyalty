class Loyalty {

    constructor(id = null, name, description, htmlTags = null, active, dateEnd) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.htmlTags = htmlTags;
        this.active = active;
        this.dateEnd = dateEnd;
    }

    #getStatusIcon() {
        let iconStatusClass = this.active ? 'enabled' : 'disabled';
        let iconStatusText = this.active  ? 'check' : 'clear';
        let title = this.active  ? 'Desactivar' : 'Activar';

        return `<i title="${title}" onclick="changeStatus(${this.id})" class="material-icons action-${iconStatusClass}">${iconStatusText}</i>`;
    }

    static getUrlAjaxController(action) {
        return loyalty_ajax_link + '&' + new URLSearchParams({
            ajax: true,
            action: action
        });
    }

    static async getById(id) {
        return await fetch(this.getUrlAjaxController('getById') + '&' + new URLSearchParams({
            id_loyalty: id
        }));
    }

    static async save(data) {
        return await fetch(this.getUrlAjaxController('save'), {
            method: 'POST',
            body: data,
        });
    }

    static async changeStatus(data) {
        return await fetch(this.getUrlAjaxController('changeStatus'), {
            method: 'POST',
            body: data,
        });
    }

    static async remove(data) {
        return await fetch(this.getUrlAjaxController('remove'), {
            method: 'POST',
            body: data
        });
    }

    static clearForm() {
        document.getElementById('id_loyalty').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('date_end').value = '';
        document.getElementById('btnLoyaltySubmit').innerText = 'Crear';
        document.getElementById('btnLoyaltyReset').style.display = 'none';
    }

    static renderPreviewProperty() {
        const element = document.getElementById('propElement');
        const propText = document.getElementById('propText');
        const text = propText.value == '' ? propText.placeholder : propText.value;
        const elUrl = document.getElementById('propUrl');
        let url = elUrl.value == '' ? elUrl.placeholder : elUrl.value;
        const urlTarget = document.getElementById('propUrlTarget').value;
        const startLorem = '<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. ';
        const endLorem = ' perspiciatis earum ullam magni obcaecati quis explicabo esse.</p>';
        let html = startLorem;
    
        url = baseUrl + url;
    
        switch (element.value) {
            case 'a':
                html += `<a href="${url}" title="${text}" target="${urlTarget}">${text}</a>`;
                break;
            case 'strong':
                html += `<strong>${text}</strong>`;
                break;
            default:
                break;
        }
    
        document.querySelector('.prop-preview').innerHTML = html + endLorem;
    }

    addRow() {
        const tbl = document.getElementById('tablePrograms');
        const row = tbl.insertRow(tbl.rows.length);
        row.classList.add(`program-${this.id}`);
        const id_cell = row.insertCell(0);
        id_cell.innerText = this.id;
        const name_cell = row.insertCell(1);
        name_cell.innerText = this.name;
        const description_cell = row.insertCell(2);
        description_cell.innerText = this.description;
        const date_end_cell = row.insertCell(3)
        date_end_cell.innerText = this.dateEnd;
        const status_cell = row.insertCell(4);
        status_cell.innerHTML = this.#getStatusIcon();
        const actions_cell = row.insertCell(5);
        actions_cell.innerHTML = `<div class="row loyalty-programs-actions">
            <div class="col-md-3"><i onclick="listPromotions(${this.id})" title="Ver las promociones de este programa" class="icon icon-eye-open"></i></div>
            <div class="col-md-3"><i onclick="configLoyalty(${this.id})" title="Ajustes" class="icon icon-cog"></i></div>
            <div class="col-md-2"><i data-id-program="${this.id}" title="Borrar todas la promociones de este programa" class="icon icon-remove-circle"></i></div>
            <div class="col-md-2"><i onclick="getDataProgram(${this.id})" title="Editar" class="icon icon-edit"></i></div>
            <div class="col-md-2"><i onclick="deleteProgram(${this.id})" title="Eliminar" class="icon icon-trash"></i></div>
            </div>`;
    }

    updateRow() {
        const row = document.querySelector(`.program-${this.id}`);
        row.cells[1].innerText = this.name;
        row.cells[2].innerText = this.description;
        row.cells[3].innerText = this.dateEnd;
        row.cells[4].innerHTML = this.#getStatusIcon();
    }

    addRowsProperties() {
        const htmlTags = JSON.parse(this.htmlTags)
       
        if (htmlTags) {
            const tbl = document.getElementById('tableProgramsProperties');

            htmlTags.forEach(element => {
                const row = tbl.insertRow(tbl.rows.length);
                row.classList.add('property-'+element.id);
                const id_cell = row.insertCell(0);
                id_cell.innerText = element.id;
                const element_cell = row.insertCell(1);
                const text_cell = row.insertCell(2)
                text_cell.innerText = element.text;
                const html_cell = row.insertCell(3)
                if (element.element == 'strong'){
                    element_cell.innerText = 'Negrita';
                    html_cell.innerHTML = `<strong>${element.text}</strong>`;
                }
                if (element.element == 'a') {
                    element_cell.innerText = 'Link';
                    const link = `<a title="${element.text}" href="${element.url}" target="">${element.text}</a>`;
                    html_cell.innerHTML = link;
                }
                const icon_cell = row.insertCell(4);
                icon_cell.classList.add('text-center');
                const iconDelete =`<i onclick="deletePropertyProgram('${element.id}', ${this.id})" class="icon icon-trash"></i>`;
                icon_cell.innerHTML = iconDelete;
            });
        }
    }

    static deleteRowsproperties() {
        const tbl = document.getElementById('tableProgramsProperties');
        const rows = tbl.rows.length;
        if (rows > 1) {
            for (let i=rows-1; i >= 1; i--){
                tbl.deleteRow(i)
            }
        }
    }

    static resetFormProperties() {
        document.getElementById("formAddPropertyProgram").reset();
        document.querySelector('.propDivUrl').style.display = 'none';
        Loyalty.renderPreviewProperty();
    }
    
}